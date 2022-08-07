<div class="menu">
    <a class="button" href="<?php echo SITE_ADDRESS ?>?view=polls">&laquo; Zurück zur Übersicht</a>
</div>
<h2>Neue Umfrage anlegen</h2>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=create_poll" ?>">
    <p><input type="text" name="name" placeholder="Name der Umfrage" required /> *</p>
    <p><textarea name="description" placeholder="Beschreibung"></textarea></p>
    <p><textarea name="options" placeholder="Werte (Ein Wert pro Zeile)" required></textarea> *</p>
    <p><label><input type="checkbox" name="is_multichoice" /> Mehrfachauswahl möglich?</label></p>
    <p><label><input type="checkbox" name="is_order" /> Mehrfachbestellung möglich?</label></p>
    <p><input type="submit" value="Anlegen" /></p>
</form>