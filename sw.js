// sw.js - Service worker for push notifications
// Must be located in the ingolstadt-chapter.de web root (or at least within the /calendar path scope).

// Workaround for a known iOS/WebKit bug: On a cold PWA start (the app was fully
// closed), iOS ignores the URL passed to clients.openWindow() and always opens
// the manifest's start_url instead. Store the target URL in IndexedDB as well;
// the start page reads it on load and navigates to it.
function setPendingUrl(url) {
  return new Promise((resolve) => {
    const request = indexedDB.open("push-nav", 2);
    request.onupgradeneeded = () => {
      const db = request.result;
      if (!db.objectStoreNames.contains("kv")) {
        db.createObjectStore("kv");
      }
      if (!db.objectStoreNames.contains("messages")) {
        db.createObjectStore("messages", { keyPath: "id" });
      }
    };
    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction("kv", "readwrite");
      tx.objectStore("kv").put(url, "pendingUrl");
      tx.oncomplete = () => {
        db.close();
        resolve();
      };
      tx.onerror = () => {
        db.close();
        resolve();
      };
    };
    request.onerror = () => resolve();
    request.onblocked = () => resolve();
  });
}

function savePushMessage(message) {
  return new Promise((resolve, reject) => {
    const request = indexedDB.open("push-nav", 2);
    request.onupgradeneeded = () => {
      const db = request.result;
      if (!db.objectStoreNames.contains("kv")) {
        db.createObjectStore("kv");
      }
      if (!db.objectStoreNames.contains("messages")) {
        db.createObjectStore("messages", { keyPath: "id" });
      }
    };
    request.onerror = () => reject(request.error);
    request.onblocked = () => reject(new Error("Push-Meldungsdatenbank ist blockiert."));
    request.onsuccess = () => {
      const db = request.result;
      const tx = db.transaction("messages", "readwrite");
      const store = tx.objectStore("messages");
      const getAll = store.getAll();
      getAll.onsuccess = () => {
        const messages = getAll.result.sort((a, b) => b.receivedAt - a.receivedAt);
        messages.slice(9).forEach((oldMessage) => store.delete(oldMessage.id));
        store.put(message);
      };
      tx.oncomplete = () => {
        db.close();
        resolve();
      };
      tx.onerror = () => {
        db.close();
        reject(tx.error);
      };
    };
  });
}

self.addEventListener("push", (event) => {
  let data = { title: "Neue Nachricht", body: "", url: "/calendar/" };
  try {
    if (event.data) data = event.data.json();
  } catch (e) {
    // ignore, defaults verwenden
  }

  const message = {
    id: Date.now() + "-" + Math.random().toString(36).slice(2),
    title: typeof data.title === "string" ? data.title : "Neue Nachricht",
    body: typeof data.body === "string" ? data.body : "",
    url: typeof data.url === "string" ? data.url : "/calendar/",
    receivedAt: Date.now(),
    unread: true,
  };
  const options = {
    body: message.body,
    data: { url: message.url, messageId: message.id },
    icon: "images/icons/event_app_96.png",
    badge: "images/icons/event_app_192.png",
    tag: "inchap-push", // ersetzt ältere Notifications statt sie zu stapeln
  };

  event.waitUntil(
    Promise.all([
      self.registration.showNotification(message.title, options),
      savePushMessage(message)
        .then(() => self.clients.matchAll({ type: "window", includeUncontrolled: true }))
        .then((allClients) => {
          allClients.forEach((client) => client.postMessage({ type: "push-received" }));
        })
        .catch((error) => {
          console.error("Push-Meldung konnte nicht gespeichert oder gemeldet werden.", error);
        }),
    ])
  );
});

self.addEventListener("notificationclick", (event) => {
  event.notification.close();
  const url = event.notification.data?.url || "/calendar/";
  const messageId = event.notification.data?.messageId;

  event.waitUntil(
    (async () => {
      if (messageId) {
        try {
          const request = indexedDB.open("push-nav", 2);
          const db = await new Promise((resolve, reject) => {
            request.onsuccess = () => resolve(request.result);
            request.onerror = () => reject(request.error);
            request.onblocked = () => reject(new Error("Push-Meldungsdatenbank ist blockiert."));
            request.onupgradeneeded = () => {
              const database = request.result;
              if (!database.objectStoreNames.contains("kv")) database.createObjectStore("kv");
              if (!database.objectStoreNames.contains("messages")) {
                database.createObjectStore("messages", { keyPath: "id" });
              }
            };
          });
          await new Promise((resolve, reject) => {
            const tx = db.transaction("messages", "readwrite");
            const store = tx.objectStore("messages");
            const get = store.get(messageId);
            get.onsuccess = () => {
              if (get.result) {
                get.result.unread = false;
                store.put(get.result);
              }
            };
            tx.oncomplete = resolve;
            tx.onerror = () => reject(tx.error);
          });
          db.close();
        } catch (error) {
          console.error("Push-Meldung konnte nicht als gelesen markiert werden.", error);
        }
      }

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
