(function () {
    var subscriptionCount = document.getElementById("push-subscription-count");
    if (subscriptionCount) {
        fetch(subscriptionCount.dataset.endpoint, {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ admin: subscriptionCount.dataset.admin })
        })
            .then(function (response) {
                return response.json().then(function (result) {
                    if (!response.ok) throw new Error(result.error || "HTTP " + response.status);
                    return result;
                });
            })
            .then(function (result) {
                subscriptionCount.textContent = "Aktuell gespeicherte Push-Abonnenten: " + result.count;
            })
            .catch(function () {
                subscriptionCount.textContent = "Push-Abonnentenzahl konnte nicht geladen werden.";
            });
    }

    var pushDialog = document.getElementById("push-broadcast-dialog");
    if (!pushDialog) return;

    var pushDescription = document.getElementById("push-broadcast-description");
    var pushStatus = document.getElementById("push-broadcast-status");
    var pushCancel = document.getElementById("push-broadcast-cancel");
    var pushConfirm = document.getElementById("push-broadcast-confirm");
    var pushFinish = document.getElementById("push-broadcast-finish");
    var pushClose = document.getElementById("push-broadcast-close");
    var pushButtons = document.querySelectorAll(".push-broadcast-button");
    var activePushButton = null;
    var pushIsSending = false;

    function closePushDialog() {
        pushDialog.close();
        if (!pushIsSending && activePushButton) activePushButton.focus();
    }

    pushButtons.forEach(function (button) {
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
        pushButtons.forEach(function (pushButton) {
            pushButton.disabled = true;
        });
        pushConfirm.disabled = true;
        pushCancel.disabled = true;
        pushStatus.hidden = false;
        pushStatus.textContent = "Push wird gesendet …";

        try {
            var response = await fetch(pushDialog.dataset.endpoint, {
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
            pushButtons.forEach(function (pushButton) {
                pushButton.disabled = false;
            });
            if (pushDialog.open) {
                (pushFinish.hidden ? pushClose : pushFinish).focus();
            } else if (activePushButton) {
                activePushButton.focus();
            }
        }
    });
})();