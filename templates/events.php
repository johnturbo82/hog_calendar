<h2>Aktuelle Terminbuchungen</h2>
<div class="bookings">
    <?php
    foreach ($this->_['event_list'] as $event) {
        $link = SITE_ADDRESS . "?view=book&event_id=" . $event->id;
    ?>
        <div class="booking">
            <div class="cell bold">
                <?php echo $event->name; ?><br />
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
                    if (($event->description != "") || (count($event->attachments) > 0)) {
                        echo "<a class='button more-button' title='Mehr Informationen'>Mehr Infos</a>";
                    }
                    ?>
                    <a class="button" href="<?php echo $link ?>" title="Veranstaltung buchen">Buchen</a>
                </div>

            <?php
            } else {
            ?>
                <div class="cell right">Anmeldung geschlossen.</div>
            <?php
            }
            ?>
            <?php
            if (($event->description != "") || (count($event->attachments) > 0)) {
            ?>
                <div class="description-container">
                    <div class="description-modal">
                        <div class="description">
                            <h2>Event</h2>
                            <p><strong><?php echo $event->name; ?></strong>, <?php echo $event->get_date_str(); ?></p>
                            <p><?php echo $event->location; ?></p>
                            <h3>Weitere Infos</h3>
                            <?php
                            if ($event->description != "") {
                                echo nl2br($event->description);
                            }
                            ?>
                            <span class="close">[x]</span>
                            <?php
                            if (isset($event->attachments)) {
                                foreach ($event->attachments as $file_id) {
                                    echo "<a class='event_image' target='_blank'href='https://drive.google.com/uc?export=view&id=" . $file_id . "'><img src='https://drive.google.com/uc?export=view&id=" . $file_id . "' /></a>";
                                }
                            }
                            ?>
                        </div>
                        <div class="interaction">
                            <a class="button" href="<?php echo $link ?>" title="Veranstaltung buchen">Buchen</a>
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