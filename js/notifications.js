(function () {
    var databaseName = "push-nav";
    var databaseVersion = 2;
    var messageStoreName = "messages";
    var menuButton = document.getElementById("notifications-menu-button");
    var countElement = document.getElementById("notifications-menu-count");
    var dialog = document.getElementById("notifications-dialog");
    var list = document.getElementById("notifications-list");
    var emptyMessage = document.getElementById("notifications-empty");
    var deleteAllButton = document.getElementById("notifications-delete-all");
    var closeButton = document.getElementById("notifications-close");
    var doneButton = document.getElementById("notifications-done");

    if (!menuButton || !countElement || !dialog || !("indexedDB" in window)) return;

    function openDatabase() {
        return new Promise(function (resolve, reject) {
            var request = indexedDB.open(databaseName, databaseVersion);
            request.onupgradeneeded = function () {
                var database = request.result;
                if (!database.objectStoreNames.contains("kv")) {
                    database.createObjectStore("kv");
                }
                if (!database.objectStoreNames.contains(messageStoreName)) {
                    database.createObjectStore(messageStoreName, { keyPath: "id" });
                }
            };
            request.onsuccess = function () {
                request.result.onversionchange = function () {
                    request.result.close();
                };
                resolve(request.result);
            };
            request.onerror = function () {
                reject(request.error);
            };
            request.onblocked = function () {
                reject(new Error("Push-Meldungsdatenbank ist blockiert."));
            };
        });
    }

    async function getMessages() {
        var database = await openDatabase();
        try {
            return await new Promise(function (resolve, reject) {
                var transaction = database.transaction(messageStoreName, "readonly");
                var request = transaction.objectStore(messageStoreName).getAll();
                request.onsuccess = function () {
                    resolve(request.result.sort(function (a, b) {
                        return b.receivedAt - a.receivedAt;
                    }));
                };
                request.onerror = function () {
                    reject(request.error);
                };
            });
        } finally {
            database.close();
        }
    }

    async function markAllRead() {
        var database = await openDatabase();
        try {
            await new Promise(function (resolve, reject) {
                var transaction = database.transaction(messageStoreName, "readwrite");
                var store = transaction.objectStore(messageStoreName);
                var request = store.getAll();
                request.onsuccess = function () {
                    request.result.forEach(function (message) {
                        if (message.unread) {
                            message.unread = false;
                            store.put(message);
                        }
                    });
                };
                transaction.oncomplete = resolve;
                transaction.onerror = function () {
                    reject(transaction.error);
                };
            });
        } finally {
            database.close();
        }
    }

    async function deleteAllMessages() {
        var database = await openDatabase();
        try {
            await new Promise(function (resolve, reject) {
                var transaction = database.transaction(messageStoreName, "readwrite");
                transaction.objectStore(messageStoreName).clear();
                transaction.oncomplete = resolve;
                transaction.onerror = function () {
                    reject(transaction.error);
                };
            });
        } finally {
            database.close();
        }
    }

    function updateCount(messages) {
        var unreadCount = messages.reduce(function (count, message) {
            return count + (message.unread ? 1 : 0);
        }, 0);
        countElement.textContent = unreadCount > 99 ? "99+" : String(unreadCount);
        countElement.classList.toggle("is-empty", unreadCount === 0);
        menuButton.hidden = messages.length === 0;
        menuButton.setAttribute(
            "aria-label",
            unreadCount ? "Meldungen, " + unreadCount + " ungelesen" : "Meldungen"
        );
    }

    async function refreshCount() {
        try {
            updateCount(await getMessages());
        } catch (error) {
            console.error("Push-Meldungszähler konnte nicht geladen werden.", error);
        }
    }

    function formatTime(timestamp) {
        return new Intl.DateTimeFormat("de-DE", {
            dateStyle: "short",
            timeStyle: "short"
        }).format(new Date(timestamp));
    }

    function renderMessages(messages) {
        list.replaceChildren();
        emptyMessage.hidden = messages.length !== 0;
        deleteAllButton.disabled = messages.length === 0;

        messages.forEach(function (message) {
            var item = document.createElement("li");
            var button = document.createElement("button");
            var title = document.createElement("span");
            var body = document.createElement("span");
            var time = document.createElement("time");
            var target;

            button.type = "button";
            if (message.unread) button.classList.add("unread");
            title.className = "notifications-list-title";
            title.textContent = message.title || "Neue Nachricht";
            body.className = "notifications-list-body";
            body.textContent = message.body || "";
            time.className = "notifications-list-time";
            time.dateTime = new Date(message.receivedAt).toISOString();
            time.textContent = formatTime(message.receivedAt);
            button.append(title, body, time);

            try {
                target = new URL(message.url, window.location.origin);
            } catch (error) {
                target = null;
            }
            if (target && target.origin === window.location.origin &&
                target.pathname.indexOf(new URL("./", window.location.href).pathname) === 0) {
                button.addEventListener("click", function () {
                    window.location.href = target.href;
                });
            } else {
                button.disabled = true;
            }

            item.appendChild(button);
            list.appendChild(item);
        });
    }

    async function showMessages() {
        try {
            await markAllRead();
            var messages = await getMessages();
            renderMessages(messages);
            updateCount(messages);
        } catch (error) {
            console.error("Push-Meldungen konnten nicht geladen werden.", error);
            list.replaceChildren();
            emptyMessage.textContent = "Meldungen konnten nicht geladen werden.";
            emptyMessage.hidden = false;
        }
    }

    menuButton.addEventListener("click", async function () {
        emptyMessage.textContent = "Du hast noch keine Push-Meldungen erhalten.";
        dialog.showModal();
        doneButton.focus();
        await showMessages();
    });

    function closeDialog() {
        dialog.close();
        menuButton.focus();
    }

    closeButton.addEventListener("click", closeDialog);
    doneButton.addEventListener("click", closeDialog);
    dialog.addEventListener("close", function () {
        menuButton.focus();
    });

    deleteAllButton.addEventListener("click", async function () {
        if (!window.confirm("Möchtest du alle Push-Meldungen löschen?")) return;
        deleteAllButton.disabled = true;
        try {
            await deleteAllMessages();
            renderMessages([]);
            updateCount([]);
        } catch (error) {
            console.error("Push-Meldungen konnten nicht gelöscht werden.", error);
            emptyMessage.textContent = "Meldungen konnten nicht gelöscht werden.";
            emptyMessage.hidden = false;
        } finally {
            deleteAllButton.disabled = list.children.length === 0;
        }
    });

    if ("serviceWorker" in navigator) {
        navigator.serviceWorker.addEventListener("message", function (event) {
            if (event.data && event.data.type === "push-received") refreshCount();
        });
    }
    window.addEventListener("pageshow", refreshCount);
    refreshCount();
})();
