<?php
require_once('classes/Event.php');
require_once('classes/Model.php');
require_once('config.php');

define("EVENT_ID", $_GET['event_id']);

$db = new Model();
$json_url = "https://www.googleapis.com/calendar/v3/calendars/" . CALENDAR_ID . "/events/" . EVENT_ID . "?key=" . ACCESS_TOKEN;
$json = file_get_contents($json_url);
$event = json_decode($json);

if ($event->visibility == "private") {
    die("Privates Event!");
}
if (!isset($event->summary)) {
    die("Kein Event gefunden. Bitte Event ID überprüfen.");
}
if (isset($event->start->dateTime)) {
    $from = $event->start->dateTime;
    $to = $event->end->dateTime;
} else {
    $from = $event->start->date;
    $to = $event->end->date;
}
$now = new DateTime('NOW');
$dt = new DateTime($from);
$hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
$booking_closed = false;
if ($now >= $dt->modify($hour_str)) {
    $booking_closed = true;
}
$event_obj = new Event($event->id, $event->summary, $from, $to, $event->location);
?>

<!DOCTYPE HTML>
<html>
<head>
    <title>Chapter Event "<?php echo $event_obj->name ?>" buchen...</title>
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
</head>
<body>
    <div class="container">
        <div class="content">
            <img src="images/Ingolstadt-Chapter.png" alt="H.O.G. Ingolstadt Chapter" />
            <h1>H.O.G. Ingolstadt Chapter Events</h1>
            <?php
            if ($booking_closed) {
            ?>
                <p>Terminbuchung für das Event</><p><b>"<?php echo $event_obj->name ?>"</b></p><p><b><?php echo $event_obj->get_from_str() ?></b> ist leider geschlossen.</p>
            <?php
            } else {
            ?>
                <p>Terminbuchung für das Event</><p><b>"<?php echo $event_obj->name ?>"</b></p><p><b><?php echo $event_obj->get_from_str() ?></b>.</p>
                <?php
                $mailtext = "Die Anmeldung für das Event " . $event_obj->name . " " . $event_obj->get_from_str() . " war erfolgreich.\n";
                            
                if (isset($event->location)) {
                    echo "<p>Ort: " . $event->location . "</p>";
                    $mailtext.= "Ort: " . $event->location . "\n\n";
                }
                $mailtext.= "Solltest Du nicht teilnehmen können, gib bitte dem Organisator rechtzeitig Bescheid.\n\nVielen Dank,\nDein Ingolstadt Chapter\n\nRIDE AND HAVE FUN!";
                ?>
                <?php
                $count = $db->get_booking_count(EVENT_ID);
                if ($count == 1) {
                    echo "<p><a href='bookings.php?event_id=" . EVENT_ID . "'>Aktuell eine Anmeldung</a></p>";
                } else if ($count > 1) {
                    echo "<p><a href='bookings.php?event_id=" . EVENT_ID . "'>Aktuell " . $count . " Anmeldungen</a></p>";
                }
                ?>
                <p>Alle Buchungen sind verbindlich sofern Kosten entstehen. Tischreservierungen werden nach Anzahl der angemeldeten Teilnehmer vorgenommen.</p>
                <p>Die Email-Adresse wird nur zum Versand einer Buchungsbestätigung verwendet und ist optional.</p>
                <form method="POST" action="bookme.php">
                    <p><input type="text" name="givenname" placeholder="Vorname" value="<?php echo $_COOKIE['booking_givenname'] ?>" required /> *</p>
                    <p><input type="text" name="name"  placeholder="Name" value="<?php echo $_COOKIE['booking_name'] ?>" required /> *</p>
                    <p><input type="email" name="email"  placeholder="Email-Adresse" value="<?php echo $_COOKIE['booking_email'] ?>" /></p>
                    <p><label><input type="checkbox" name="plusone" /> + 1 Person</label></p>
                    <p>Mehr als eine Person zusätzlich bitte gesondert anmelden.</p>
                    <input type="hidden" name="event_id" value="<?php echo $event->id ?>" />
                    <input type="hidden" name="from" value="<?php echo $from ?>" />
                    <input type="hidden" name="eventname" value="<?php echo $event_obj->name ?>" />
                    <input type="hidden" name="mailtext" value="<?php echo $mailtext ?>" />
                    <input type="submit" class="no-margin" value="Buchen">
                </form>
            <?php
            }
            ?>
        </div>
        <footer>
            <a href="mailto:oliver@schoettner.rocks">Bei Fragen und Anregungen: oliver@schoettner.rocks</a>
        </footer>
    </div>
</body>
</html>