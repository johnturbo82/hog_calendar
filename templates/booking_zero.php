<h2 class="error">Achtung, <?php echo $this->_['givenname'] ?>!</h2>
<p>Es wurden 0 Personen eingegeben, bitte beachte, dass die Eingabe die Anzahl aller Personen repräsentiert.</p>
<p>Wenn du nur dich anmelden willst, trage bitte "1" ein.</p>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=bookme" ?>">
    <input type="hidden" name="event_id" value="<?php echo $this->_['event_id'] ?>" />
    <p><input type="text" name="name" value="<?php echo $this->_['name'] ?>" /></p>
    <p><input type="text" name="givenname" value="<?php echo $this->_['givenname'] ?>" /></p>
    <p><input type="text" name="email" value="<?php echo $this->_['email'] ?>" /></p>
    <p><input type="number" name="persons" value="1" max="5" pattern="\d*" /> Person(en) insgesamt</p>
    <input type="hidden" name="from" value="<?php echo $this->_['from'] ?>" />
    <input type="hidden" name="eventname" value="<?php echo $this->_['eventname'] ?>" />
    <input type="hidden" name="mailtext" value="<?php echo $this->_['mailtext'] ?>" />
    <input type="submit" value="Buchen" />
</form>