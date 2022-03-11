<?php
require_once('classes/Event.php');
require_once('classes/Model.php');
require_once('config.php');

$db = new Model();
$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events?key=" . ACCESS_TOKEN;
$json = file_get_contents($json_url);
$obj = json_decode($json);
$events = array();

foreach($obj->items as $event) {
    if ($event->visibility == "private") {
        continue;
    }
    if (isset($event->start->dateTime)) {
        $from = $event->start->dateTime;
        $to = $event->end->dateTime;
    } else {
        $from = $event->start->date;
        $to = $event->end->date;
    }
    $now = new DateTime("now");
    $event_date = new DateTime(date("Y-m-d", strtotime($from)));
    $event_date->add(new DateInterval('P1D'));
    if ($event_date <= $now) {
        continue;
    }
    $event_obj = new Event($event->id, $event->summary, $from, $to, $event->location);
    $events[$from . " " . $event->id] = $event_obj;
}
ksort($events);

$booking_table = "<div class='bookings'>";
foreach($events as $event) {
    $booking_table.= "<div class='booking'>";
    $booking_table.= "<div class='cell bold'>" . $event->name . "</div>";
    $booking_table.= "<div class='cell'>" . $event->get_date_str() . "</div>";
    $count = $db->get_booking_count($event->id);
    $booking_table.= "<a href='" . SITE_ADDRESS . "manage_event.php?event_id=" . $event->id . "' title='Buchungen anzeigen'>";
    if ($count == 1) {
        $booking_table.= "<div class='cell'>" . $count . " Anmeldung</div>";
    } else {
        $booking_table.= "<div class='cell'>" . $count . " Anmeldungen</div>";
    }
    $booking_table.= "</a>";
    $link = SITE_ADDRESS . "book.php?event_id=" . $event->id;
    $now = new DateTime('NOW');
    $dt = new DateTime($event->from);
    $hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
    if ($now < $dt->modify($hour_str)) {
        $booking_table.= "<div class='cell right'>";
        $booking_table.= "<a class='button' href='" . $link . "' title='Veranstaltung buchen'>Buchen</a>";
        $booking_table.= "<a class='button' onClick='copyToClipboard(\"" . $link . "\", this)' title='In die Zwischenablage kopieren'>Kopieren</a>";
        $booking_table.= "<a class='button whatsapp' href='whatsapp://send?text=Liebe Member,%0Ahier könnt ihr euch für das Event \"" . $event->name . "\", " . $event->get_date_str() . " anmelden:%0A" . $link . "' title='Link zur Buchung per WhatsApp verschicken'><img src='images/icons/whatsapp.svg' alt='Whatsapp' /></a>";
        $booking_table.= "</div>";
    } else {
        $booking_table.= "<div class='cell right'>Anmeldung geschlossen.</div>";
    }
    $booking_table.= "</div>";
}
$booking_table.= "</div>";
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>H.O.G. Events</title>
    <link rel="stylesheet" href="css/styles.css">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <link rel="shortcut icon" type="image/x-icon" href="images/icons/favicon.ico">
    <link rel="icon" type="image/x-icon" href="images/icons/favicon.ico">
    <link rel="icon" type="image/gif" href="images/icons/favicon.gif">
    <link rel="icon" type="image/png" href="images/icons/favicon.png">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon.png">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-57x57.png" sizes="57x57">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-60x60.png" sizes="60x60">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-72x72.png" sizes="72x72">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-76x76.png" sizes="76x76">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-114x114.png" sizes="114x114">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-120x120.png" sizes="120x120">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-128x128.png" sizes="128x128">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-144x144.png" sizes="144x144">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-152x152.png" sizes="152x152">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-180x180.png" sizes="180x180">
    <link rel="apple-touch-icon" href="images/icons/apple-touch-icon-precomposed.png">
    <link rel="icon" type="image/png" href="images/icons/favicon-16x16.png" sizes="16x16">
    <link rel="icon" type="image/png" href="images/icons/favicon-32x32.png" sizes="32x32">
    <link rel="icon" type="image/png" href="images/icons/favicon-96x96.png" sizes="96x96">
    <link rel="icon" type="image/png" href="images/icons/favicon-160x160.png" sizes="160x160">
    <link rel="icon" type="image/png" href="images/icons/favicon-192x192.png" sizes="192x192">
    <link rel="icon" type="image/png" href="images/icons/favicon-196x196.png" sizes="196x196">
    <meta name="msapplication-TileImage" content="images/icons/win8-tile-144x144.png">
    <meta name="msapplication-TileColor" content="#ffffff">
    <meta name="msapplication-navbutton-color" content="#ffffff">
    <meta name="msapplication-square70x70logo" content="images/icons/win8-tile-70x70.png">
    <meta name="msapplication-square144x144logo" content="images/icons/win8-tile-144x144.png">
    <meta name="msapplication-square150x150logo" content="images/icons/win8-tile-150x150.png">
    <meta name="msapplication-wide310x150logo" content="images/icons/win8-tile-310x150.png">
    <meta name="msapplication-square310x310logo" content="images/icons/win8-tile-310x310.png">
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
    <meta name="theme-color" content="#0a1014">
    <script>
        function copyToClipboard(string, el) {
            el.innerHTML = "Kopiert!";
            let textarea;
            let result;

            try {
                textarea = document.createElement('textarea');
                textarea.setAttribute('readonly', true);
                textarea.setAttribute('contenteditable', true);
                textarea.style.position = 'fixed'; // prevent scroll from jumping to the bottom when focus is set.
                textarea.value = string;

                document.body.appendChild(textarea);

                textarea.focus();
                textarea.select();

                const range = document.createRange();
                range.selectNodeContents(textarea);

                const sel = window.getSelection();
                sel.removeAllRanges();
                sel.addRange(range);

                textarea.setSelectionRange(0, textarea.value.length);
                result = document.execCommand('copy');
            } catch (err) {
                console.error(err);
                result = null;
            } finally {
                document.body.removeChild(textarea);
            }

            // manual copy fallback using prompt
            if (!result) {
                const isMac = navigator.platform.toUpperCase().indexOf('MAC') >= 0;
                const copyHotkey = isMac ? '⌘C' : 'CTRL+C';
                result = prompt(`Press ${copyHotkey}`, string); // eslint-disable-line no-alert
                if (!result) {
                return false;
                }
            }
            return true;
            }
    </script>
</head>
<body>
    <div class="container">
        <div class="content">
            <img src="images/Ingolstadt-Chapter.png" alt="H.O.G. Ingolstadt Chapter" />
            <h1>H.O.G. Ingolstadt Chapter Events</h1>
            <p>Terminbuchungen für alle anstehenden Termine des Chapters. Alle Buchungen sind verbindlich sofern Kosten entstehen.</p>
            <?php echo $booking_table ?>
        </div>
        <footer>
            <a href="mailto:oliver@schoettner.rocks">Bei Fragen und Anregungen: oliver@schoettner.rocks</a>
        </footer>
    </div>
</body>
</html>