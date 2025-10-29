<h2>Aktuelle Terminbuchungen für <?php echo $this->_['name']; ?></h2>
<div class="bookings">
    <?php
    foreach ($this->_['event_list'] as $event) {
        $link = SITE_ADDRESS . "?view=book&event_id=" . $event->id;
        $location = strpos($event->location, ",") ? explode(",", $event->location)[0] : $event->location;
    ?>
        <div class="booking">
            <div class="cell">
                <strong><?php echo $event->name; ?></strong><br /><?php echo $location; ?>
            </div>
            <div class="cell"><?php echo $event->get_date_str(); ?></div>
            <a href="<?php echo SITE_ADDRESS . "?view=bookings&event_id=" . $event->id ?>" title='Buchungen anzeigen'>
                <div class="cell"><?php echo ($event->registrations == 1) ? $event->registrations . " Anmeldung" : $event->registrations . " Anmeldungen" ?></div>
            </a>
            <?php
            if (!$event->is_closed) {
            ?>
                <div class="cell right">
                    <?php
                    if (stripos($event->description, "Keine Kuttenpflicht") !== false) {
                    ?>
                        <img class="keine_kutte" src="<?php echo SITE_ADDRESS ?>images/icons/keine_kutte.svg" alt="Keine Kuttenspflicht" />
                    <?php
                    }
                    ?>
                    <?php
                    if ($event->description != "") {
                    ?>
                        <a class="button more-button" title="Mehr Informationen">Infos</a>
                    <?php
                    }
                    ?>
                    <a class="button" href="<?php echo $link ?>" title="Buchung ändern">Ändern</a>
                </div>

            <?php
            } else {
            ?>
                <div class="cell right">Anmeldung geschlossen.</div>
            <?php
            }
            ?>
            <?php
            if ($event->description != "") {
            ?>
                <div class="description-container">
                    <div class="description-modal">
                        <div class="description">
                            <h2>Event</h2>
                            <p><strong><?php echo $event->name; ?></strong>, <?php echo $event->get_date_str(); ?></p>
                            <p><?php echo $event->location; ?></p>
                            <h3>Weitere Infos</h3><?php echo $event->description ?><span class="close">[x]</span>
                        </div>
                        <div class="interaction">
                            <a class="button" href="<?php echo $link ?>" title="Buchung ändern">Ändern</a>
                            <a class="button button-close" title="Schließen">Schließen</a>
                        </div>
                    </div>
                </div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>