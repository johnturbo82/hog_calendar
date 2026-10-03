// push-subscribe.js
// Include this in the Calendar web app, e.g. from an "Enable notifications" button.
//
// Uses same-origin PHP proxy endpoints (./api/*.php), which forward requests to
// push.schoettner.dev. This avoids known iOS Safari issues with cross-origin
// fetch() in installed standalone PWAs.

const PUSH_SCRIPT_URL = document.currentScript.src;
const PUSH_API_BASE = new URL("../api/", PUSH_SCRIPT_URL).href;
// sw.js is intentionally located in the /calendar/ web root (not under js/)
// so its default scope covers the entire app. A scope limited to js/ would keep
// clients.matchAll() in the notificationclick handler from finding the app page
// (which is outside that scope), preventing the iOS notification-click workaround
// from working reliably.
const PUSH_WORKER_URL = new URL("../sw.js", PUSH_SCRIPT_URL).href;
const PUSH_WORKER_SCOPE = new URL("../", PUSH_SCRIPT_URL).href;

async function fetchJson(url, options) {
  const response = await fetch(url, options);
  const result = await response.json();
  if (!response.ok) {
    throw new Error(result.error || `HTTP ${response.status}`);
  }
  return result;
}

async function getPushRegistration() {
  return navigator.serviceWorker.getRegistration(PUSH_WORKER_URL);
}

async function waitForActiveWorker(registration) {
  const worker = registration.active || registration.installing || registration.waiting;
  if (!worker) {
    throw new Error("Der Service Worker konnte nicht gestartet werden.");
  }
  if (worker.state === "activated") return;

  await new Promise((resolve, reject) => {
    const onStateChange = () => {
      if (worker.state === "activated") {
        worker.removeEventListener("statechange", onStateChange);
        resolve();
      } else if (worker.state === "redundant") {
        worker.removeEventListener("statechange", onStateChange);
        reject(new Error("Der Service Worker konnte nicht aktiviert werden."));
      }
    };
    worker.addEventListener("statechange", onStateChange);
    onStateChange();
  });
}

function urlBase64ToUint8Array(base64String) {
  const padding = "=".repeat((4 - (base64String.length % 4)) % 4);
  const base64 = (base64String + padding).replace(/-/g, "+").replace(/_/g, "/");
  const rawData = window.atob(base64);
  return Uint8Array.from([...rawData].map((char) => char.charCodeAt(0)));
}

async function enablePushNotifications(onProgress) {
  const log = onProgress || (() => {});

  if (!("serviceWorker" in navigator) || !("PushManager" in window) || !("Notification" in window)) {
    alert("Push-Benachrichtigungen werden von diesem Browser nicht unterstützt.");
    return;
  }

  log("Schritt 1/5: Berechtigung anfragen…");
  const permission = await Notification.requestPermission();
  if (permission !== "granted") {
    log("Berechtigung nicht erteilt (" + permission + ").");
    return false;
  }

  log("Schritt 2/5: Service Worker registrieren…");
  const registration = await navigator.serviceWorker.register(PUSH_WORKER_URL, {
    scope: PUSH_WORKER_SCOPE,
  });
  await waitForActiveWorker(registration);

  log("Schritt 3/5: VAPID Key laden…");
  const { publicKey } = await fetchJson(`${PUSH_API_BASE}vapid-public-key.php`);

  log("Schritt 4/5: Bei Push-Dienst abonnieren…");
  const subscription = await registration.pushManager.getSubscription() ||
    await registration.pushManager.subscribe({
      userVisibleOnly: true,
      applicationServerKey: urlBase64ToUint8Array(publicKey),
    });

  log("Schritt 5/5: Subscription an Server senden…");
  await fetchJson(`${PUSH_API_BASE}subscribe.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify(subscription.toJSON()),
  });

  log("Fertig.");
  return true;
}

async function disablePushNotifications(onProgress) {
  const log = onProgress || (() => {});
  const registration = await getPushRegistration();
  const subscription = await registration?.pushManager.getSubscription();
  if (!subscription) return;

  log("Deaktiviere Benachrichtigungen…");
  await fetchJson(`${PUSH_API_BASE}unsubscribe.php`, {
    method: "POST",
    headers: { "Content-Type": "application/json" },
    body: JSON.stringify({ endpoint: subscription.endpoint }),
  });
  await subscription.unsubscribe();
  log("Benachrichtigungen wurden deaktiviert.");
}

const pushControls = document.getElementById("push-controls");
if (pushControls) {
  const enableButton = document.getElementById("enable-push-btn");
  const disableButton = document.getElementById("disable-push-btn");
  const statusElement = document.getElementById("push-status");

  async function updatePushControls() {
    try {
      const registration = await getPushRegistration();
      const subscription = await registration?.pushManager.getSubscription();
      enableButton.hidden = Boolean(subscription);
      disableButton.hidden = !subscription;
    } catch (error) {
      statusElement.textContent = "Push-Status konnte nicht geladen werden.";
    }
  }

  if ("serviceWorker" in navigator && "PushManager" in window) {
    pushControls.hidden = false;
    updatePushControls();

    enableButton.addEventListener("click", async () => {
      enableButton.disabled = true;
      statusElement.textContent = "Aktiviere Benachrichtigungen…";
      try {
        const enabled = await enablePushNotifications((message) => {
          statusElement.textContent = message;
        });
        if (!enabled) statusElement.textContent = "Berechtigung wurde nicht erteilt.";
      } catch (error) {
        statusElement.textContent = `Fehler: ${error.message}`;
      } finally {
        enableButton.disabled = false;
        await updatePushControls();
      }
    });

    disableButton.addEventListener("click", async () => {
      disableButton.disabled = true;
      try {
        await disablePushNotifications((message) => {
          statusElement.textContent = message;
        });
      } catch (error) {
        statusElement.textContent = `Fehler: ${error.message}`;
      } finally {
        disableButton.disabled = false;
        await updatePushControls();
      }
    });
  }
}

const pushOnboarding = document.getElementById("push-onboarding");
if (pushOnboarding) {
  const onboardingButton = document.getElementById("push-onboarding-enable");
  const dismissButton = document.getElementById("push-onboarding-dismiss");
  const onboardingStatus = document.getElementById("push-onboarding-status");
  const dismissedKey = "push-onboarding-dismissed";
  const isStandalone = navigator.standalone === true ||
    window.matchMedia("(display-mode: standalone)").matches;

  function dismissPushOnboarding() {
    pushOnboarding.hidden = true;
    try {
      localStorage.setItem(dismissedKey, "true");
    } catch (error) {
      // The overlay can still be dismissed when storage is unavailable.
    }
  }

  async function showPushOnboarding() {
    if (!isStandalone || !("serviceWorker" in navigator) ||
        !("PushManager" in window) || !("Notification" in window) ||
        Notification.permission !== "default") {
      return;
    }

    try {
      if (localStorage.getItem(dismissedKey) === "true") return;
    } catch (error) {
      // Continue without persistence when storage is unavailable.
    }

    const registration = await getPushRegistration();
    if (await registration?.pushManager.getSubscription()) return;

    pushOnboarding.hidden = false;
  }

  dismissButton.addEventListener("click", dismissPushOnboarding);
  onboardingButton.addEventListener("click", async () => {
    onboardingButton.disabled = true;
    onboardingStatus.textContent = "Aktiviere Benachrichtigungen…";
    try {
      const enabled = await enablePushNotifications((message) => {
        onboardingStatus.textContent = message;
      });
      if (enabled) {
        dismissPushOnboarding();
      } else if (Notification.permission === "denied") {
        dismissPushOnboarding();
      } else {
        onboardingStatus.textContent = "Berechtigung wurde nicht erteilt.";
      }
    } catch (error) {
      onboardingStatus.textContent = `Aktivierung fehlgeschlagen: ${error.message}`;
    } finally {
      onboardingButton.disabled = false;
    }
  });

  showPushOnboarding().catch((error) => {
    console.error("Push-Onboarding konnte nicht geladen werden:", error);
  });
}

// Cleanup: An old service worker registered at js/sw.js (scope js/) comes from
// an earlier version and would otherwise remain active alongside the new
// registration (scope /calendar/), potentially causing duplicate notifications.
// Unregister it on each page load if it is still present.
if ("serviceWorker" in navigator) {
  navigator.serviceWorker.getRegistrations().then((registrations) => {
    registrations.forEach((registration) => {
      if (registration.active?.scriptURL.endsWith("/js/sw.js")) {
        registration.unregister();
      }
    });
  });
}
