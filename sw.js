// sw.js - Service worker for push notifications
// Must be located in the ingolstadt-chapter.de web root (or at least within the /calendar path scope).

// Workaround for a known iOS/WebKit bug: On a cold PWA start (the app was fully
// closed), iOS ignores the URL passed to clients.openWindow() and always opens
// the manifest's start_url instead. Store the target URL in IndexedDB as well;
// the start page reads it on load and navigates to it.
function setPendingUrl(url) {
  return new Promise((resolve) => {
    const request = indexedDB.open("push-nav", 1);
    request.onupgradeneeded = () => {
      request.result.createObjectStore("kv");
    };
    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction("kv", "readwrite");
      tx.objectStore("kv").put(url, "pendingUrl");
      tx.oncomplete = () => resolve();
      tx.onerror = () => resolve();
    };
    request.onerror = () => resolve();
  });
}

self.addEventListener("push", (event) => {
  let data = { title: "Neue Nachricht", body: "", url: "/calendar/" };
  try {
    data = event.data.json();
  } catch (e) {
    // ignore, defaults verwenden
  }

  const options = {
    body: data.body,
    data: { url: data.url || "/calendar/" },
    icon: "images/icons/event_app_96.png",
    badge: "images/icons/event_app_192.png",
    tag: "inchap-push", // ersetzt ältere Notifications statt sie zu stapeln
  };

  event.waitUntil(self.registration.showNotification(data.title, options));
});

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const url = event.notification.data?.url || "/calendar/";

  event.waitUntil(
    (async () => {
      // Always store this first as a fallback in case the page is loading or reloading.
      await setPendingUrl(url);

      const allClients = await clients.matchAll({
        type: "window",
        includeUncontrolled: true,
      });

      // If the app is already open (including in the background or suspended),
      // try both mechanisms at once because only one may work reliably depending
      // on the iOS state: postMessage (if the page's JS is still running) and
      // navigate() (browser-side; sometimes works when the page itself is frozen).
      if (allClients.length > 0) {
        for (const client of allClients) {
          client.postMessage({ type: "push-navigate", url });
          await client.focus();
          if ("navigate" in client) {
            try {
              await client.navigate(url);
            } catch (e) {
              // navigate() can fail (e.g. cross-origin); ignore it and let the
              // postMessage/pendingUrl fallback handle the navigation.
            }
          }
        }
        return;
      }

      // No open window was found -> open a new one.
      // On a cold start, the start page reads the stored pendingUrl and
      // navigates to it itself.
      return clients.openWindow(url);
    })()
  );
});
