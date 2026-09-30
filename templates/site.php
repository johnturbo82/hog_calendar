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
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <link rel="manifest" href="<?php echo SITE_ADDRESS . (($this->_['admin']) ? 'manifest_admin.json' : 'manifest.json') ?>">
    <link rel="icon" type="image/x-icon" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.ico">
    <link rel="icon" type="image/png" href="<?php echo SITE_ADDRESS . CUSTOM_PATH ?>images/icons/favicon.png">
    <link rel="icon" type="image/png" sizes="96x96" href="<?php echo CUSTOM_PATH ?>images/icons/event_app_96.png">
    <link rel="icon" type="image/png" sizes="192x192" href="<?php echo CUSTOM_PATH ?>images/icons/event_app_192.png">
    <link rel="icon" type="image/png" sizes="512x512" href="<?php echo CUSTOM_PATH ?>images/icons/event_app_512.png">
    <link rel="apple-touch-icon" sizes="192x192" href="<?php echo CUSTOM_PATH ?>images/icons/<?php echo ($this->_['admin']) ? 'event_app_192_admin.png' : 'event_app_192.png' ?>">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2064-2752.jpg" media="(device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2752-2064.jpg" media="(device-width: 1032px) and (device-height: 1376px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2048-2732.jpg" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2732-2048.jpg" media="(device-width: 1024px) and (device-height: 1366px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1668-2420.jpg" media="(device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2420-1668.jpg" media="(device-width: 834px) and (device-height: 1210px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1668-2388.jpg" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2388-1668.jpg" media="(device-width: 834px) and (device-height: 1194px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1668-2224.jpg" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2224-1668.jpg" media="(device-width: 834px) and (device-height: 1112px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1536-2048.jpg" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2048-1536.jpg" media="(device-width: 768px) and (device-height: 1024px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1640-2360.jpg" media="(device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2360-1640.jpg" media="(device-width: 820px) and (device-height: 1180px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1620-2160.jpg" media="(device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2160-1620.jpg" media="(device-width: 810px) and (device-height: 1080px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1488-2266.jpg" media="(device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2266-1488.jpg" media="(device-width: 744px) and (device-height: 1133px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1320-2868.jpg" media="(device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2868-1320.jpg" media="(device-width: 440px) and (device-height: 956px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1206-2622.jpg" media="(device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2622-1206.jpg" media="(device-width: 402px) and (device-height: 874px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1260-2736.jpg" media="(device-width: 420px) and (device-height: 912px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2736-1260.jpg" media="(device-width: 420px) and (device-height: 912px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1290-2796.jpg" media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2796-1290.jpg" media="(device-width: 430px) and (device-height: 932px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1179-2556.jpg" media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2556-1179.jpg" media="(device-width: 393px) and (device-height: 852px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1170-2532.jpg" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2532-1170.jpg" media="(device-width: 390px) and (device-height: 844px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1284-2778.jpg" media="(device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2778-1284.jpg" media="(device-width: 428px) and (device-height: 926px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1080-2340.jpg" media="(device-width: 360px) and (device-height: 780px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2340-1080.jpg" media="(device-width: 360px) and (device-height: 780px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1242-2688.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2688-1242.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1125-2436.jpg" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2436-1125.jpg" media="(device-width: 375px) and (device-height: 812px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-828-1792.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1792-828.jpg" media="(device-width: 414px) and (device-height: 896px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1242-2208.jpg" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-2208-1242.jpg" media="(device-width: 414px) and (device-height: 736px) and (-webkit-device-pixel-ratio: 3) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-750-1334.jpg" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1334-750.jpg" media="(device-width: 375px) and (device-height: 667px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-640-1136.jpg" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: portrait)">
    <link rel="apple-touch-startup-image" href="<?php echo CUSTOM_PATH ?>images/splashscreen/apple-splash-1136-640.jpg" media="(device-width: 320px) and (device-height: 568px) and (-webkit-device-pixel-ratio: 2) and (orientation: landscape)">
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