<h2>Kein Name gefunden</h2>
<p>Leider wurde kein Anmeldename gefunden, bitte trage hier deinen Namen ein, um Events anzuzeigen, zu den du angemeldet bist:</p>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=set_name" ?>">
<input type="text" name="givenname" placeholder="Vorname" /><br /><br />
<input type="text" name="name" placeholder="Nachname" /><br /><br />
<input type="submit" class="no-margin" value="Namen setzen" />
</form>