<p>Terminbuchungen für alle anstehenden Termine des Chapters. Alle Buchungen sind verbindlich sofern Kosten entstehen.</p>
<div class="bookings">
    <?php
    foreach ($this->_['event_list'] as $event) {
        $now = new DateTime('NOW');
        $dt = new DateTime($event->from);
        $hour_str = '-' . HOURS_TO_EVENT_TO_CLOSE_BOOKING  . ' hours';
        $link = SITE_ADDRESS . "book/" . $event->id;
    ?>
        <div class="booking">
            <div class="cell bold"><?php echo $event->name; ?></div>
            <div class="cell"><?php echo $event->get_date_str(); ?></div>
            <a href="<?php echo SITE_ADDRESS . "manage/" . $event->id ?>" title='Buchungen anzeigen'>
                <div class="cell"><?php echo ($event->registrations == 1) ? $event->registrations . " Anmeldung" : $event->registrations . " Anmeldungen" ?></div>
            </a>
            <?php
            if ($now < $dt->modify($hour_str)) {
            ?>
                <div class="cell right">
                    <a class="button" href="<?php echo $link ?>" title="Veranstaltung buchen">Buchen</a>
                    <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren">Kopieren</a>
                    <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr euch für das Event %22<?php echo $event->name ?>%22, <?php echo $event->get_date_str() ?> anmelden:%0A<?php echo $link ?>" title="Link zur Buchung per WhatsApp verschicken">
                        <img src="images/icons/whatsapp.svg" alt="Whatsapp" />
                    </a>
                </div>
            <?php
            } else {
            ?>
                <div class="cell right">Anmeldung geschlossen.</div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>