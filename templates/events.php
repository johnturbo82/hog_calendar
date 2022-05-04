<div class="legend">
    <h3>Legende</h3>
    <table>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/add.svg" alt="Buchen" /></div>
            </td>
            <td>Event buchen</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Anmeldung schließen" /></div>
            </td>
            <td>Anmeldung schließen</td>
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
            <td></td>
            <td></td>
        </tr>
    </table>
</div>
<h2>Anstehende Events</h2>
<p>Terminbuchungen für alle anstehenden Termine des Chapters.</p>
<div class="bookings">
    <?php
    foreach ($this->_['event_list'] as $event) {
        $link = SITE_ADDRESS . "?view=book&event_id=" . $event->id;
    ?>
        <div class="booking">
            <div class="cell bold"><?php echo $event->name; ?></div>
            <div class="cell"><?php echo $event->get_date_str(); ?></div>
            <a href="<?php echo SITE_ADDRESS . "?view=manage&event_id=" . $event->id ?>" title='Buchungen anzeigen'>
                <div class="cell"><?php echo ($event->registrations == 1) ? $event->registrations . " Anmeldung" : $event->registrations . " Anmeldungen" ?></div>
            </a>
            <?php
            if (!$event->is_closed) {
            ?>
                <div class="cell right">
                    <a class="button" href="<?php echo $link ?>" title="Veranstaltung buchen"><img src="<?php echo SITE_ADDRESS ?>images/icons/add.svg" alt="Buchen" /></a>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=close_event&event_id=" . $event->id ?>" title="Eventanmeldungen schließen"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Eventanmeldungen schließen" /></a>
                    <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></a>
                    <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr euch für das Event %22<?php echo $event->name ?>%22, <?php echo $event->get_date_str() ?> anmelden:%0A<?php echo urlencode($link) ?>" title="Link zur Buchung per WhatsApp verschicken">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" />
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