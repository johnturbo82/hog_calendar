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
$event_obj = new Event($event->id, $event->summary, $from, $to, $event->location);

$booking_table = "<table>";
$booking_table.= "<tr><th>Lfd. Nr.</th><th>Name</th><th>Pers.</th><th class='no-mobile'>Seit</th><th></th></tr>";
$i = 0;
$booking_no = 0;
foreach ($db->get_bookings($event->id) as $booking) {
    $booking_no = $booking_no + $booking['persons'];
    $booking_table.= "<tr>";
    $booking_table.= "<td>" . ++$i . "</td>";
    $booking_table.= "<td>" . $booking['name'] . ", " . $booking['givenname'] . "</td>";
    $booking_table.= "<td>" . $booking['persons'] . "</td>";
    $booking_table.= "<td class='no-mobile'>" . date("d.m.Y H:i", strtotime($booking['create_date'])) . "</td>";
    $booking_table.= "<td class='icons'>";
    $booking_table.= "<form onsubmit=\"return confirm('Soll die Buchung von " . $booking['givenname'] . " " . $booking['name'] . " wirklich storniert werden?');\" method='POST' action='storno.php'>";
    $booking_table.= "<input type='hidden' name='event_id' value='" . $booking['event_id'] . "' />";
    $booking_table.= "<input type='hidden' name='booking_id' value='" . $booking['id'] . "' />";
    $booking_table.= "<input type='image' class='button' src='images/icons/trash.svg' alt='Buchung stornieren' title='Buchung stornieren' />";
    $booking_table.= "</form>";
    $booking_table.= "</td>";
    $booking_table.= "</tr>";
}
$booking_table.= "</table>";

$stornos = $db->get_stornos($event->id);
$storno_table = "";
if (count($stornos) > 0) {
    $storno_table = "<h2>Stornierte Anmeldungen</h2>";
    $storno_table.= "<table>";
    $storno_table.= "<tr><th>Lfd. Nr.</th><th>Name</th><th>Pers.</th><th class='no-mobile'>Storno seit</th></tr>";
    $k = 0;
    foreach ($db->get_stornos($event->id) as $storno) {
        $storno_table.= "<tr>";
        $storno_table.= "<td>" . ++$k . "</td>";
        $storno_table.= "<td>" . $storno['name'] . ", " . $storno['givenname'] . "</td>";
        $storno_table.= "<td>" . $storno['persons'] . "</td>";
        $storno_table.= "<td class='no-mobile'>" . date("d.m.Y H:i", strtotime($storno['update_date'])) . "</td>";
        $storno_table.= "</tr>";
    }
    $storno_table.= "</table>";
}
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
            if ($booking_no == 1) {
                echo "<h2>Eine Anmeldung für das Event</h2>";
            } else {
                echo "<h2>" . $booking_no . " Anmeldungen für das Event</h2>";
            }
            ?>
            <p><b>"<?php echo $event_obj->name ?>"</b></p>
            <p><b><?php echo $event_obj->get_from_str() ?></b>.</p>
            <?php echo $booking_table ?>
            <?php echo $storno_table ?>
        </div>
        <footer>
            <a href="mailto:oliver@schoettner.rocks">Bei Fragen und Anregungen: oliver@schoettner.rocks</a>
        </footer>
    </div>
</body>
</html>