<?php
require_once('classes/Model.php');
require_once('config.php');

$safePost = filter_input_array(INPUT_POST);

setcookie("booking_name", $safePost['name'], time() + 3600 * 24 * 365 * 5);
setcookie("booking_givenname", $safePost['givenname'], time() + 3600 * 24 * 365 * 5);
setcookie("booking_email", $safePost['email'], time() + 3600 * 24 * 365 * 5);

$now = new DateTime('NOW');
$dt = new DateTime($safePost['from']);
$hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
if ($now >= $dt->modify($hour_str)) {
    die("Buchungen geschlossen.");
}

$db = new Model();
if ($db->booking_exists($safePost['event_id'], $safePost['name'], $safePost['givenname']) && $safePost['overwrite'] != "1") {
    $content = "<h2>Achtung!</h2>
                <p>Es besteht bereits eine Buchung auf den Namen:</p>
                <p><strong>\"" . $safePost['givenname'] . " " . $safePost['name'] . "\"</strong></p>
                <p>Soll die Buchung trotzdem durchgeführt werden? Eventuell können Doppelbuchungen entstehen.</p>
                <a href='cancel.php' class='button'>Nein, Abbruch!</a>
                <form method='POST' action='" . $_SERVER['PHP_SELF'] . "'>
                <input type='hidden' name='event_id' value='" . $safePost['event_id'] . "' />
                <input type='hidden' name='name' value='" . $safePost['name'] . "' />
                <input type='hidden' name='givenname' value='" . $safePost['givenname'] . "' />
                <input type='hidden' name='email' value='" . $safePost['email'] . "' />
                <input type='hidden' name='plusone' value='" . $safePost['plusone'] . "' />
                <input type='hidden' name='from' value='" . $safePost['from'] . "' />
                <input type='hidden' name='eventname' value='" . $safePost['eventname'] . "' />
                <input type='hidden' name='mailtext' value='" . $safePost['mailtext'] . "' />
                <input type='hidden' name='overwrite' value='1' />
                    <input type='submit' value='Trotzdem nochmal buchen.' />
                </form>";
} else {
    if ($db->new_booking($safePost['event_id'], $safePost['name'], $safePost['givenname'], $safePost['email'], $safePost['plusone'])) {
        if (isset($safePost['email'])) {
            $header = 'From: H.O.G. Ingolstadt Chapter <webmaster@ingolstadt-chapter.de>' . "\r\n" .
                'Reply-To: webmaster@ingolstadt-chapter.de' . "\r\n" .
                'X-Mailer: PHP/' . phpversion();
            mail($safePost['email'], "Event " . $safePost['eventname'] . " erfolgreich gebucht", $safePost['mailtext'], $header);
        }
    }
    $content = "<h2>Vielen Dank. Event erfolgreich gebucht.</h2>
                <p>Diese Seite kann geschlossen werden.</p>";
}
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
</head>

<body>
    <div class="container">
        <div class="content">
            <img src="images/Ingolstadt-Chapter.png" alt="H.O.G. Ingolstadt Chapter" />
            <h1>H.O.G. Ingolstadt Chapter Events</h1>
            <?php echo $content; ?>
        </div>
        <footer>
            <a href="mailto:oliver@schoettner.rocks">Bei Fragen und Anregungen: oliver@schoettner.rocks</a>
        </footer>
    </div>
</body>

</html>