Kompletter Jahresplan zum Drucken: <a href="https://www.ingolstadt-chapter.de/events/druckansicht/?event_year=<?php echo date("Y") ?>" target="_blank"><?php echo date("Y") ?></a> | <a href="https://www.ingolstadt-chapter.de/events/druckansicht/?event_year=<?php echo date("Y") + 1 ?>" target="_blank"><?php echo date("Y") + 1 ?></a>
<h2>Aktuelle Terminbuchungen</h2>
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
                    if (($event->description != "") || (count($event->attachments) > 0)) {
                        echo "<a class='button more-button' title='Mehr Informationen'>Infos</a>";
                    }
                    ?>
                    <a class="button" href="<?php echo $link ?>" title="Zur Veranstaltung an- oder abmelden">An-/Abmelden</a>
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
                            <span class="close">[x]</span>
                            <?php
                            if ($event->description != "") {
                                echo nl2br($event->description);
                            }
                            ?>
                            <br /><br />
                            <?php
                            if (isset($event->attachments)) {
                                foreach ($event->attachments as $file_id) {
                                    echo "<a class='event_image' target='_blank'href='https://drive.google.com/uc?export=view&id=" . $file_id . "'><img src='https://drive.google.com/thumbnail?id=" . $file_id . "' /></a>";
                                }
                            }
                            ?>
                        </div>
                        <div class="interaction">
                            <a class="button" href="<?php echo $link ?>" title="Zur Veranstaltung an- oder abmelden">An-/Abmelden</a>
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