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
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=close_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen schließen"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Eventanmeldungen schließen" /></a>
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
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=open_event&event_id=" . $event->id . "&admin=" . $this->_['admin'] ?>" title="Eventanmeldungen wieder öffnen"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Eventanmeldungen wieder öffnen" /></a></div>
            <?php
            }
            ?>
        </div>
    <?php
    }
    ?>
</div>
<dialog class="push-broadcast-dialog" id="push-broadcast-dialog" aria-labelledby="push-broadcast-title" aria-describedby="push-broadcast-description">
    <div class="push-broadcast-dialog-header">
        <h2 id="push-broadcast-title">Push senden</h2>
        <button class="push-broadcast-dialog-close" id="push-broadcast-close" type="button" title="Schließen" aria-label="Schließen">x</button>
    </div>
    <p id="push-broadcast-description"></p>
    <p id="push-broadcast-status" role="status" aria-live="polite" hidden></p>
    <div class="push-broadcast-actions">
        <button class="button" id="push-broadcast-cancel" type="button">Abbrechen</button>
        <button class="button" id="push-broadcast-confirm" type="button">Senden</button>
        <button class="button" id="push-broadcast-finish" type="button" hidden>Schließen</button>
    </div>
</dialog>
<script>
var pushDialog = document.getElementById("push-broadcast-dialog");
var pushDescription = document.getElementById("push-broadcast-description");
var pushStatus = document.getElementById("push-broadcast-status");
var pushCancel = document.getElementById("push-broadcast-cancel");
var pushConfirm = document.getElementById("push-broadcast-confirm");
var pushFinish = document.getElementById("push-broadcast-finish");
var pushClose = document.getElementById("push-broadcast-close");
var activePushButton = null;
var pushIsSending = false;

function closePushDialog() {
    pushDialog.close();
    if (!pushIsSending && activePushButton) activePushButton.focus();
}

document.querySelectorAll(".push-broadcast-button").forEach(function (button) {
    button.addEventListener("click", function () {
        activePushButton = button;
        pushDescription.textContent = 'Push an alle Abonnenten für "' + button.dataset.eventTitle + '" senden?';
        pushStatus.textContent = "";
        pushStatus.hidden = true;
        pushCancel.hidden = false;
        pushCancel.disabled = false;
        pushConfirm.hidden = false;
        pushConfirm.disabled = false;
        pushFinish.hidden = true;
        pushIsSending = false;
        pushDialog.showModal();
        pushConfirm.focus();
    });
});

pushCancel.addEventListener("click", closePushDialog);
pushClose.addEventListener("click", closePushDialog);
pushFinish.addEventListener("click", closePushDialog);
pushDialog.addEventListener("cancel", function (event) {
    if (pushIsSending) event.preventDefault();
});
pushDialog.addEventListener("close", function () {
    if (!pushIsSending && activePushButton) activePushButton.focus();
});

pushConfirm.addEventListener("click", async function () {
    var button = activePushButton;
    var eventTitle = button.dataset.eventTitle;
    pushIsSending = true;
    pushCancel.hidden = true;
    pushConfirm.hidden = true;
    document.querySelectorAll(".push-broadcast-button").forEach(function (pushButton) {
        pushButton.disabled = true;
    });
    pushConfirm.disabled = true;
    pushCancel.disabled = true;
    pushStatus.hidden = false;
    pushStatus.textContent = "Push wird gesendet …";

    try {
        var response = await fetch("<?php echo SITE_ADDRESS ?>api/broadcast.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                admin: button.dataset.admin,
                event_id: button.dataset.eventId,
                title: eventTitle
            })
        });
        var result = await response.json();
        if (!response.ok) {
            throw new Error(result.error || "HTTP " + response.status);
        }
        pushStatus.textContent = "Gesendet: " + result.sent + ", fehlgeschlagen: " + result.failed;
        pushFinish.hidden = false;
    } catch (error) {
        pushStatus.textContent = "Fehler: " + error.message;
    } finally {
        pushIsSending = false;
        document.querySelectorAll(".push-broadcast-button").forEach(function (pushButton) {
            pushButton.disabled = false;
        });
        if (pushDialog.open) {
            (pushFinish.hidden ? pushClose : pushFinish).focus();
        } else if (activePushButton) {
            activePushButton.focus();
        }
    }
});
</script>
