<div class="menu">
    <a class="button" href="<?php echo SITE_ADDRESS ?>?view=events&admin=<?php echo $this->_['admin'] ?>">&laquo; Zurück zur Übersicht</a>
</div>
<h2>Neues Event anlegen</h2>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=create_event&admin=" . $this->_['admin'] ?>">
    <p>Name<br /><input type="text" name="name" placeholder="z.B. Feierabendrunde" required /> *</p>
    <p>
        Ort<br />
        <input type="text" 
               id="location-input" 
               name="location" 
               placeholder="Zielort des Events, nicht Treffpunkt!" 
               autocomplete="off"
               required /> *
    </p>
    <p>Organisator<br /><input type="text" name="organisator" placeholder="Organisator" value="<?php echo isset($_COOKIE['booking_givenname']) ? htmlspecialchars($_COOKIE['booking_givenname']) : ''; ?>" required /> *</p>
    <p>Von<br /><input type="datetime-local" name="event_date_start" value="<?php echo date('Y-m-d\TH:00', strtotime('+1 hour')); ?>" required /> *</p>
    <p>Bis<br /><input type="datetime-local" name="event_date_end" value="<?php echo date('Y-m-d\TH:00', strtotime('+2 hour')); ?>" required /> *</p>
    <p>Beschreibung<br /><textarea id="description" name="description" placeholder="z.B. wann und wo ist Treffpunkt? Wie wird gefahren?"></textarea></p>
    <p><label><input type="checkbox" id="keine_kutte" name="keine_kutte" /> Keine Kuttenpflicht</label></p>
    <p><input type="submit" value="Anlegen" /></p>
</form>

<script>
let autocomplete;

function initAutocomplete() {
    autocomplete = new google.maps.places.Autocomplete(
        document.getElementById('location-input'),
        {
            types: ['establishment', 'geocode'], // Geschäfte und Adressen
            fields: ['place_id', 'name', 'formatted_address', 'geometry'],
            componentRestrictions: {
                country: ['de', 'at', 'ch'] // Beschränke auf DACH-Region
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
    initDescriptionWatcher();
});
</script>

<script async defer 
        src="https://maps.googleapis.com/maps/api/js?key=<?php echo ACCESS_TOKEN; ?>&libraries=places&callback=initAutocomplete">
</script>
