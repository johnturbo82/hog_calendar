<a class="button" href="<?php echo SITE_ADDRESS . "?view=new_event&admin=" . $this->_['admin'] ?>">+ Neues Event</a>
<div class="legend">
    <h3>Legende</h3>
    <table>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Eventanmeldung schließen" /></div>
            </td>
            <td>Eventanmeldung schließen</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Eventanmeldung wieder öffnen" /></div>
            </td>
            <td>Eventanmeldung wieder öffnen</td>
        </tr>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></div>
            </td>
            <td>Link in Zwischenablage kopieren</td>
            <td>
                <div class="button whatsapp"><img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" /></div>
            </td>
            <td>Via WhatsApp versenden</td>
        </tr>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/keine_kutte.svg" alt="Keine Kuttenpflicht" /></div>
            </td>
            <td colspan="3">Um das Symbol an das Event zu bekommen muss im Beschreibungstext "Keine Kuttenpflicht" stehen.</td>
        </tr>
    </table>
</div>
<h2>Anstehende Events</h2>
<p>Terminbuchungen für alle anstehenden Termine des Chapters.</p>
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
            <a href="<?php echo SITE_ADDRESS . "?view=bookings&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title='Buchungen anzeigen'>
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
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=close_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen schließen"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Eventanmeldungen schließen" /></a>
                    <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></a>
                    <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr euch für das Event %22<?php echo $event->name ?>%22, <?php echo $event->get_date_str() ?> anmelden:%0A<?php echo urlencode($link) ?>" title="Link zur Buchung per WhatsApp verschicken">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" />
                    </a>
                </div>
            <?php
            } else {
            ?>
                <div class="cell right">
                    <?php
                    if (stripos($event->description, "Keine Kuttenpflicht") !== false) {
                    ?>
                        <img class="keine_kutte" src="<?php echo SITE_ADDRESS ?>images/icons/keine_kutte.svg" alt="Keine Kuttenspflicht" />
                    <?php
                    }
                    ?>
                    Geschlossen.
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=open_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen wieder öffnen"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Eventanmeldungen wieder öffnen" /></a></div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>