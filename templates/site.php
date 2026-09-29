<!DOCTYPE HTML>
<html>

<head>
    <!-- Workaround für iOS/WebKit-Bug: Nach einem Push-Notification-Klick beim
         Kaltstart der PWA landet iOS auf der Startseite statt der Ziel-URL.
         Hier wird geprüft, ob der Service Worker eine "wartende" Ziel-URL in
         IndexedDB hinterlegt hat, und dorthin weitergeleitet. -->
    <script>
        (function () {
            if (!("indexedDB" in window)) return;
            try {
                var request = indexedDB.open("push-nav", 1);
                request.onupgradeneeded = function () {
                    request.result.createObjectStore("kv");
                };
                request.onsuccess = function () {
                    var db = request.result;
                    var tx = db.transaction("kv", "readwrite");
                    var store = tx.objectStore("kv");
                    var getReq = store.get("pendingUrl");
                    getReq.onsuccess = function () {
                        var url = getReq.result;
                        if (url) {
                            store.delete("pendingUrl");
                            var target = new URL(url, location.origin);
                            var current = location.pathname + location.search;
                            if (current !== target.pathname + target.search) {
                                location.replace(url);
                            }
                        }
                    };
                };
            } catch (e) {
                // IndexedDB nicht verfügbar oder blockiert -> einfach ignorieren
            }
        })();

        // Zweiter Teil des Workarounds: Wenn die App bereits läuft (auch aus dem
        // Hintergrund reaktiviert, ohne Neuladen der Seite), schickt der Service
        // Worker beim Notification-Klick eine Nachricht direkt an diese Seite.
        if ("serviceWorker" in navigator) {
            navigator.serviceWorker.addEventListener("message", function (event) {
                if (event.data && event.data.type === "push-navigate" && event.data.url) {
                    var target = new URL(event.data.url, location.origin);
                    var current = location.pathname + location.search;
                    if (current !== target.pathname + target.search) {
                        location.href = event.data.url;
                    }
                }
            });
        }
    </script>
    <title><?php echo (isset($this->_['title'])) ? $this->_['title'] : SHORT_NAME . " Events" ?><?php echo ($this->_['admin'] == PSEUDO_ADMIM_PASSWORD) ? " ADMIN" : "" ?></title>
    <link rel="stylesheet" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>css/vars.css?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>">
    <link rel="stylesheet" href="<?php echo SITE_ADDRESS ?>css/styles.css?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <link rel="manifest" href="<?php echo SITE_ADDRESS ?>manifest.json">
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo SITE_ADDRESS ?>images/icons/event_app_96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo SITE_ADDRESS ?>images/icons/event_app_192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo SITE_ADDRESS ?>images/icons/event_app_512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo SITE_ADDRESS ?>images/icons/event_app_192.png">
    <meta name="theme-color" content="<?php echo ($this->_['admin']) ? "#501014" : "#0a1014" ?>">
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/jquery-3.6.1.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/copy_to_clipboard.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/datatables.js"></script>
    <script type="text/javascript" src="<?php echo SITE_ADDRESS ?>js/site.js?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>"></script>
    <script defer src="<?php echo SITE_ADDRESS ?>js/push-subscribe.js?v=<?php echo CURRENT_VERSION ?>&r=<?php echo REVISION ?>"></script>
</head>

<body <?php echo ($this->_['admin']) ? "class='admin'" : "" ?>>
    <div class="container">
        <div class="content">
            <div class="nav <?php echo ($this->_['admin']) ? "admin" : "" ?>">
                <nav>
                    <a class="<?php echo ($_GET['view'] == "events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=events<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/events<?php echo ($_GET['view'] == "events") ? "_active" : "" ?>.svg" alt="Events">
                        <span>Events</span>
                    </a>
                    <?php
                    if ($this->_['admin'] == PSEUDO_ADMIM_PASSWORD) {
                    ?>
                        <a class="<?php echo ($_GET['view'] == "past_events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=past_events<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/past_events<?php echo ($_GET['view'] == "past_events") ? "_active" : "" ?>.svg" alt="Vergangene Events">
                            <span>Vergangene<br />Events</span>
                        </a>
                        <a class="<?php echo ($_GET['view'] == "polls") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=polls<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/survey<?php echo ($_GET['view'] == "polls") ? "_active" : "" ?>.svg" alt="Abstimmungen">
                            <span>Umfragen</span>
                        </a>
                        <a class="<?php echo ($_GET['view'] == "tutorials") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=tutorials<?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/help<?php echo ($_GET['view'] == "tutorials") ? "_active" : "" ?>.svg" alt="Hilfe">
                            <span>Hilfe</span>
                        </a>
                    <?php
                    } else {
                    ?>
                        <a class="<?php echo ($_GET['view'] == "my_events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=my_events">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/my_events<?php echo ($_GET['view'] == "my_events") ? "_active" : "" ?>.svg" alt="Meine Events">
                            <span>Meine<br />Events</span>
                        </a>
                        <a class="<?php echo ($_GET['view'] == "past_events") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=past_events">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/past_events<?php echo ($_GET['view'] == "past_events") ? "_active" : "" ?>.svg" alt="Vergangene Events">
                            <span>Vergangene<br />Events</span>
                        </a>
                        <a class="<?php echo ($_GET['view'] == "help") ? "active" : "" ?>" href="<?php echo SITE_ADDRESS ?>?view=help">
                            <img src="<?php echo SITE_ADDRESS ?>images/icons/help<?php echo ($_GET['view'] == "help") ? "_active" : "" ?>.svg" alt="Hilfe">
                            <span>Hilfe</span>
                        </a>
                    <?php
                    }
                    ?>
                </nav>
            </div>
            <img class="logo" src="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/logo.png" alt="<?php echo LEGAL_ENTITY_NAME ?>" />
            <h1 class="app-name"><?php echo APP_NAME ?></h1>
             <div class="push-controls" id="push-controls" hidden>
                <button class="button" type="button" id="enable-push-btn">Benachrichtigungen aktivieren</button>
                <button class="button" type="button" id="disable-push-btn" hidden>Benachrichtigungen deaktivieren</button>
                <span id="push-status" role="status" aria-live="polite"></span>
            </div>
            <?php echo $this->_['content'] ?>
        </div>
        <footer>
            <strong>&copy; Oliver Schöttner 2023 - <?php echo date("Y") ?></strong><br />
            <?php 
            if (defined('SUPPORT_EMAIL')) {
                echo "Bei Fragen und Anregungen: <a href='mailto:" . SUPPORT_EMAIL . "?subject=" . SITE_ADDRESS . " - Version " . CURRENT_VERSION ."'>" . SUPPORT_EMAIL ."</a>";
            }
            ?>
            <br />
            <span>Version <?php echo CURRENT_VERSION ?> | <a href="<?php echo SITE_ADDRESS ?>?view=support">Support und Versionshinweise</a></span>
        </footer>
    </div>
</body>

</html>