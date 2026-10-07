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
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/edit.svg" alt="Event bearbeiten" /></div>
            </td>
            <td>Event bearbeiten</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/trash.svg" alt="Event löschen" /></div>
            </td>
            <td>Event löschen</td>
        </tr>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/keine_kutte.svg" alt="Keine Kuttenpflicht" /></div>
            </td>
            <td colspan="3">Um das Symbol an das Event zu bekommen muss im Beschreibungstext "Keine Kuttenpflicht" stehen.</td>
        </tr>
    </table>
</div>
<p id="push-subscription-count"
    data-endpoint="<?php echo htmlspecialchars(SITE_ADDRESS . "api/subscription-count.php", ENT_QUOTES, "UTF-8") ?>"
    data-admin="<?php echo htmlspecialchars($this->_['admin'], ENT_QUOTES, "UTF-8") ?>"
    role="status" aria-live="polite">Push-Abonnenten werden geladen …</p>
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
                $pushEventId = htmlspecialchars($event->id, ENT_QUOTES, "UTF-8");
                $pushEventTitle = htmlspecialchars($event->name, ENT_QUOTES, "UTF-8");
                $pushAdminKey = htmlspecialchars($this->_['admin'], ENT_QUOTES, "UTF-8");
            ?>
                <div class="cell right">
                    <?php
                    if (stripos($event->description, "Keine Kuttenpflicht") !== false) {
                    ?>
                        <img class="keine_kutte" src="<?php echo SITE_ADDRESS ?>images/icons/keine_kutte.svg" alt="Keine Kuttenspflicht" />
                    <?php
                    }
                    ?>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=edit_event&event_id=" . rawurlencode($event->id) . "&admin=" . $this->_['admin'] ?>" title="Event bearbeiten"><img src="<?php echo SITE_ADDRESS ?>images/icons/edit.svg" alt="Event bearbeiten" /></a>
                    <form class="inline-form" method="POST" action="<?php echo SITE_ADDRESS . "?view=delete_event&event_id=" . rawurlencode($event->id) . "&admin=" . $this->_['admin'] ?>" onsubmit="return confirm('Event &quot;<?php echo htmlspecialchars(addslashes($event->name), ENT_QUOTES, "UTF-8") ?>&quot; wirklich löschen?<?php echo ($event->registrations > 0) ? '\n\nAchtung: ' . (int)$event->registrations . ' Anmeldung(en) vorhanden!' : '' ?>');">
                        <button class="button" type="submit" title="Event löschen" aria-label="Event löschen"><img src="<?php echo SITE_ADDRESS ?>images/icons/trash.svg" alt="" /></button>
                    </form>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=close_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen schließen" onclick="return confirm('Anmeldungen für &quot;<?php echo htmlspecialchars(addslashes($event->name), ENT_QUOTES, "UTF-8") ?>&quot; wirklich schließen?');"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Eventanmeldungen schließen" /></a>
                    <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></a>
                    <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr euch für das Event %22<?php echo $event->name ?>%22, <?php echo $event->get_date_str() ?> anmelden:%0A<?php echo urlencode($link) ?>" title="Link zur Buchung per WhatsApp verschicken">
                        <img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" />
                    </a>
                    <button class="button push-broadcast-button" type="button" data-event-id="<?php echo $pushEventId ?>" data-event-title="<?php echo $pushEventTitle ?>" data-admin="<?php echo $pushAdminKey ?>" title="Push senden" aria-label="Push senden"><img src="<?php echo SITE_ADDRESS ?>images/icons/push.svg" alt="" /></button>
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
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=edit_event&event_id=" . rawurlencode($event->id) . "&admin=" . $this->_['admin'] ?>" title="Event bearbeiten"><img src="<?php echo SITE_ADDRESS ?>images/icons/edit.svg" alt="Event bearbeiten" /></a>
                    <form class="inline-form" method="POST" action="<?php echo SITE_ADDRESS . "?view=delete_event&event_id=" . rawurlencode($event->id) . "&admin=" . $this->_['admin'] ?>" onsubmit="return confirm('Event &quot;<?php echo htmlspecialchars(addslashes($event->name), ENT_QUOTES, "UTF-8") ?>&quot; wirklich löschen?<?php echo ($event->registrations > 0) ? '\n\nAchtung: ' . (int)$event->registrations . ' Anmeldung(en) vorhanden!' : '' ?>');">
                        <button class="button" type="submit" title="Event löschen" aria-label="Event löschen"><img src="<?php echo SITE_ADDRESS ?>images/icons/trash.svg" alt="" /></button>
                    </form>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=open_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen wieder öffnen"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Eventanmeldungen wieder öffnen" /></a></div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>
<dialog class="push-broadcast-dialog" id="push-broadcast-dialog" data-endpoint="<?php echo htmlspecialchars(SITE_ADDRESS . "api/broadcast.php", ENT_QUOTES, "UTF-8") ?>" aria-labelledby="push-broadcast-title" aria-describedby="push-broadcast-description">
    <div class="push-broadcast-dialog-header">
        <h2 id="push-broadcast-title">Push senden</h2>
        <button class="push-broadcast-dialog-close" id="push-broadcast-close" type="button" title="Schließen" aria-label="Schließen">x</button>
    </div>
    <label for="push-broadcast-type">Was möchtest du mitteilen?</label>
    <select id="push-broadcast-type">
        <option value="new_event">Neuer Termin – Jetzt anmelden</option>
        <option value="time_changed">Geänderte Zeit oder Datum</option>
        <option value="details_changed">Geänderte Details</option>
    </select>
    <p id="push-broadcast-description"></p>
    <p id="push-broadcast-status" role="status" aria-live="polite" hidden></p>
    <div class="push-broadcast-actions">
        <button class="button" id="push-broadcast-cancel" type="button">Abbrechen</button>
        <button class="button" id="push-broadcast-confirm" type="button">Senden</button>
        <button class="button" id="push-broadcast-finish" type="button" hidden>Schließen</button>
    </div>
</dialog>
<script defer src="<?php echo SITE_ADDRESS ?>js/admin-events.js?v=<?php echo CURRENT_VERSION ?>&amp;r=<?php echo REVISION ?>"></script>
