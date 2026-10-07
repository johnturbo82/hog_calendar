<?php
$event = $this->_['event'] ?? null;
$is_edit = $event !== null;
$is_all_day = $is_edit && strlen((string)$event->from) === 10;
$e = function ($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8');
};
$all_day_end_date = $is_all_day ? date('Y-m-d', strtotime($event->to . ' -1 day')) : date('Y-m-d', strtotime('+1 day'));
?>
<div class="menu">
    <a class="button" href="<?php echo SITE_ADDRESS ?>?view=events&admin=<?php echo $this->_['admin'] ?>">&laquo; Zurück zur Übersicht</a>
</div>
<h2><?php echo $is_edit ? "Event bearbeiten" : "Neues Event anlegen" ?></h2>
<form method="POST" action="<?php echo SITE_ADDRESS . ($is_edit ? "?view=update_event&event_id=" . rawurlencode($event->id) . "&admin=" : "?view=create_event&admin=") . $this->_['admin'] ?>">
    <p>Name<br /><input type="text" name="name" placeholder="z.B. Feierabendrunde" value="<?php echo $is_edit ? $e($event->name) : '' ?>" required /> *</p>
    <p>
        Ort<br />
        <input type="text" 
               id="location-input" 
               name="location" 
               placeholder="Zielort des Events, nicht Treffpunkt!" 
               autocomplete="off"
               value="<?php echo $is_edit ? $e($event->location) : '' ?>"
               required /> *
    </p>
    <p>Organisator<br /><input type="text" name="organisator" placeholder="Organisator" value="<?php echo $is_edit ? $e($this->_['organisator']) : (isset($_COOKIE['booking_givenname']) ? $e($_COOKIE['booking_givenname']) : ''); ?>" required /> *</p>
    <p><label><input type="checkbox" id="all_day" name="all_day" value="1"<?php echo $is_all_day ? ' checked' : '' ?> /> Ganztägig</label></p>
    <p data-timed-field<?php echo $is_all_day ? ' hidden' : '' ?>>Von<br /><input type="datetime-local" name="event_date_start" value="<?php echo $is_edit ? date('Y-m-d\TH:i', strtotime($event->from)) : date('Y-m-d\TH:00', strtotime('+1 hour')); ?>"<?php echo $is_all_day ? ' disabled' : ' required' ?> /> *</p>
    <p data-timed-field<?php echo $is_all_day ? ' hidden' : '' ?>>Bis<br /><input type="datetime-local" name="event_date_end" value="<?php echo $is_edit ? date('Y-m-d\TH:i', strtotime($event->to)) : date('Y-m-d\TH:00', strtotime('+2 hour')); ?>"<?php echo $is_all_day ? ' disabled' : ' required' ?> /> *</p>
    <p data-all-day-field<?php echo $is_all_day ? '' : ' hidden' ?>>Von<br /><input type="date" name="event_start_date" value="<?php echo $is_all_day ? $e($event->from) : date('Y-m-d') ?>"<?php echo $is_all_day ? ' required' : ' disabled' ?> /> *</p>
    <p data-all-day-field<?php echo $is_all_day ? '' : ' hidden' ?>>Bis<br /><input type="date" name="event_end_date" value="<?php echo $e($all_day_end_date) ?>"<?php echo $is_all_day ? ' required' : ' disabled' ?> /> *</p>
    <p>Beschreibung<br /><textarea id="description" name="description" placeholder="z.B. wann und wo ist Treffpunkt? Wie wird gefahren?"><?php echo $is_edit ? $e($this->_['description']) : '' ?></textarea></p>
    <p><label><input type="checkbox" id="keine_kutte" name="keine_kutte"<?php echo ($is_edit && stripos($this->_['description'], 'keine kuttenpflicht') !== false) ? ' checked' : '' ?> /> Keine Kuttenpflicht</label></p>
    <p><input type="submit" value="<?php echo $is_edit ? 'Speichern' : 'Anlegen' ?>" /></p>
</form>

<script>
function initAllDayToggle() {
    const allDayCheckbox = document.getElementById('all_day');
    const timedFields = document.querySelectorAll('[data-timed-field]');
    const allDayFields = document.querySelectorAll('[data-all-day-field]');
    const timedStart = document.querySelector('[name="event_date_start"]');
    const timedEnd = document.querySelector('[name="event_date_end"]');
    const allDayStart = document.querySelector('[name="event_start_date"]');
    const allDayEnd = document.querySelector('[name="event_end_date"]');

    function formatDate(date) {
        return date.getFullYear() + '-' +
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0');
    }

    function updateEndDate() {
        if (allDayCheckbox.checked) {
            if (!allDayStart.value) {
                return;
            }
            const parts = allDayStart.value.split('-').map(Number);
            const endDate = new Date(parts[0], parts[1] - 1, parts[2]);
            endDate.setDate(endDate.getDate() + 1);
            allDayEnd.value = formatDate(endDate);
            return;
        }

        if (!timedStart.value) {
            return;
        }
        const endDateTime = new Date(timedStart.value);
        if (Number.isNaN(endDateTime.getTime())) {
            return;
        }
        endDateTime.setHours(endDateTime.getHours() + 1);
        timedEnd.value = formatDate(endDateTime) + 'T' +
            String(endDateTime.getHours()).padStart(2, '0') + ':' +
            String(endDateTime.getMinutes()).padStart(2, '0');
    }

    function updateDateFields() {
        timedFields.forEach(function(field) {
            field.hidden = allDayCheckbox.checked;
            field.querySelector('input').disabled = allDayCheckbox.checked;
        });
        allDayFields.forEach(function(field) {
            field.hidden = !allDayCheckbox.checked;
            field.querySelector('input').disabled = !allDayCheckbox.checked;
        });
    }

    timedStart.addEventListener('change', updateEndDate);
    allDayStart.addEventListener('change', updateEndDate);
    allDayCheckbox.addEventListener('change', function() {
        updateDateFields();
        updateEndDate();
    });
    updateDateFields();
}

let autocomplete;

function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('location-input'),
        {
            types: ['establishment', 'geocode'], // Businesses and addresses
            fields: ['place_id', 'name', 'formatted_address', 'geometry'],
            componentRestrictions: {
                country: ['de', 'at', 'ch'] // Restrict to the DACH region
            }
        }
    );

    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        
        if (!place.geometry) {
            console.log("Kein Ort für '" + place.name + "' gefunden.");
            return;
        }
    });
}

function initDescriptionWatcher() {
    const descriptionTextarea = document.getElementById('description');
    const keineKutteCheckbox = document.getElementById('keine_kutte');
    
    if (descriptionTextarea && keineKutteCheckbox) {
        descriptionTextarea.addEventListener('input', function() {
            const text = this.value.toLowerCase();
            if (text.includes('keine kuttenpflicht')) {
                keineKutteCheckbox.checked = true;
            } else {
                keineKutteCheckbox.checked = false;
            }
        });
        
        keineKutteCheckbox.addEventListener('change', function() {
            const currentText = descriptionTextarea.value;
            
            if (this.checked) {
                if (!currentText.toLowerCase().includes('keine kuttenpflicht')) {
                    const textToAdd = currentText.trim() === '' ? 'Keine Kuttenpflicht' : '\n\nKeine Kuttenpflicht';
                    descriptionTextarea.value = currentText + textToAdd;
                }
            } else {
                const lines = currentText.split('\n');
                const filteredLines = lines.filter(line => !line.toLowerCase().includes('keine kuttenpflicht'));
                
                while (filteredLines.length > 0 && filteredLines[filteredLines.length - 1].trim() === '') {
                    filteredLines.pop();
                }
                descriptionTextarea.value = filteredLines.join('\n');
            }
        });
    }
}

window.addEventListener('load', function() {
    if (typeof google === 'undefined') {
        console.warn('Google Maps API nicht geladen. Autocomplete nicht verfügbar.');
    }
    initAllDayToggle();
    initDescriptionWatcher();
});
</script>

<script async defer 
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo ACCESS_TOKEN; ?>&libraries=places&callback=initAutocomplete">
</script>
