// sw.js - Service Worker für Push-Benachrichtigungen
// Muss im Web-Root von ingolstadt-chapter.de liegen (oder mind. im Pfad-Scope von /calendar)

// Workaround für einen bekannten iOS/WebKit-Bug: Beim Kaltstart der PWA
// (App war komplett geschlossen) ignoriert iOS die an clients.openWindow()
// übergebene URL und öffnet stattdessen zwingend die start_url aus dem
// Manifest. Deshalb wird die Ziel-URL zusätzlich in IndexedDB abgelegt;
// die Startseite liest sie beim Laden aus und navigiert selbst dorthin.
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
      // Immer zuerst als Fallback ablegen, falls die Seite gerade lädt/neu lädt.
      await setPendingUrl(url);

      const allClients = await clients.matchAll({
        type: "window",
        includeUncontrolled: true,
      });

      // Falls die App bereits offen ist (auch im Hintergrund/suspendiert):
      // beide Mechanismen gleichzeitig versuchen, da je nach iOS-Zustand nur
      // einer zuverlässig greift: postMessage (falls JS der Seite noch läuft)
      // und navigate() (browser-seitig, funktioniert teils auch wenn die
      // Seite selbst gerade eingefroren/suspendiert ist).
      if (allClients.length > 0) {
        for (const client of allClients) {
          client.postMessage({ type: "push-navigate", url });
          await client.focus();
          if ("navigate" in client) {
            try {
              await client.navigate(url);
            } catch (e) {
              // navigate() kann fehlschlagen (z.B. cross-origin) -> ignorieren,
              // postMessage/pendingUrl-Fallback greifen dann trotzdem.
            }
          }
        }
        return;
      }

      // Kein offenes Fenster gefunden -> neues öffnen.
      // Die Startseite liest die abgelegte pendingUrl beim Laden aus und
      // navigiert selbst dorthin (Kaltstart-Fall).
      return clients.openWindow(url);
    })()
  );
});
