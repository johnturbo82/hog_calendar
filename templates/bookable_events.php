<h2>Aktuell offene Terminbuchungen</h2>
<p>Eine Übersicht über alle Aktivitäten im Jahr findet ihr auf unserer <a href="https://ingolstadt-chapter.de/events" target="_blank">Chapter Webseite</a>.</p>
<div class="bookings">
    <?php
    foreach ($this->_['event_list'] as $event) {
        if ($event->is_closed) {
            continue;
        }
        $link = SITE_ADDRESS . "?view=book&event_id=" . $event->id;
    ?>
        <div class="booking">
            <div class="cell bold"><?php echo $event->name; ?></div>
            <div class="cell"><?php echo $event->get_date_str(); ?></div>
            <a href="<?php echo SITE_ADDRESS . "?view=bookings&event_id=" . $event->id ?>" title='Buchungen anzeigen'>
                <div class="cell"><?php echo ($event->registrations == 1) ? $event->registrations . " Anmeldung" : $event->registrations . " Anmeldungen" ?></div>
            </a>
            <?php
            if (!$event->is_closed) {
            ?>
                <div class="cell right">
                    <a class="button" href="<?php echo $link ?>" title="Veranstaltung buchen">Buchen</a>
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