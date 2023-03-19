<?php
$event = $this->_['event'];
if ($event->is_closed) {
?>
    <a class="button no-margin" href="<?php echo SITE_ADDRESS ?>?view=events">&laquo; Zur Eventübersicht</a>
    <p>Terminbuchung für das Event</p>
    <h2>"<?php echo $event->name ?>"</h2>
    <p><b><?php echo $event->get_from_str() ?></b> ist leider geschlossen.</p>
<?php
} else {
?>
    <a class="button no-margin" href="<?php echo SITE_ADDRESS ?>?view=events">&laquo; Zur Eventübersicht</a>
    <p>Terminbuchung für das Event</>
    <h2>"<?php echo $event->name ?>"</h2>
    <p><b><?php echo $event->get_from_str() ?></b>.</p>
    <?php
    $mailtext = "Die Anmeldung für das Event " . $event->name . " " . $event->get_from_str() . " war erfolgreich.\n";

    if (isset($event->location)) {
        echo "<p><strong>Ort:</strong> " . $event->location . "</p>";
        $mailtext .= "Ort: " . $event->location . "\n\n";
    }
    if (isset($event->description)) {
        echo "<p><strong>Weitere Infos:</strong> " . $event->description . "</p>";
    }
    $mailtext .= "Solltest Du nicht teilnehmen können, gib bitte dem Organisator rechtzeitig Bescheid. Achtung: Die Veranstaltungszeiten können sich noch ändern, bitte halte Dich über die Whatsapp-Gruppe und über die Website auf dem Laufenden.\n\nVielen Dank,\n" . MAIL_SIGNATURE;
    if ($event->registrations == 1) {
        echo "<p><a href='?view=bookings&event_id=" . $event->id . "'>Aktuell eine Anmeldung</a></p>";
    } else if ($event->registrations > 1) {
        echo "<p><a href='?view=bookings&event_id=" . $event->id . "'>Aktuell " . $event->registrations . " Anmeldungen</a></p>";
    }
    ?>
    <p><strong>Hinweis:</strong> Buchungen sind verbindlich sofern Kosten entstehen. Tischreservierungen werden nach Anzahl der angemeldeten Teilnehmer vorgenommen.</p>
    <p>Die Email-Adresse wird nur zum Versand einer Buchungsbestätigung verwendet und ist optional.</p>
    <form method="POST" action="<?php echo SITE_ADDRESS . "?view=bookme" ?>">
        <p><input type="text" name="givenname" placeholder="Vorname" value="<?php echo $_COOKIE['booking_givenname'] ?>" required /> *</p>
        <p><input type="text" name="name" placeholder="Name" value="<?php echo $_COOKIE['booking_name'] ?>" required /> *</p>
        <p><input type="email" name="email" placeholder="Email-Adresse" value="<?php echo $_COOKIE['booking_email'] ?>" /></p>
        <p><input type="number" name="persons" value="1" min="1" max="5" pattern="\d*" /> Person(en) insgesamt</p>
        <input type="hidden" name="event_id" value="<?php echo $event->id ?>" />
        <input type="hidden" name="from" value="<?php echo $event->from ?>" />
        <input type="hidden" name="eventname" value="<?php echo $event->name ?>" />
        <input type="hidden" name="mailtext" value="<?php echo $mailtext ?>" />
        <input type="submit" class="no-margin" value="Buchen">
    </form>
<?php
}
?>