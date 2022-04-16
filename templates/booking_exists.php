<h2 class="error">Achtung, <?php echo $this->_['givenname'] ?>!</h2>
<p>Es besteht bereits eine Buchung auf den Namen:</p>
<p><strong><?php echo $this->_['givenname'] ?> <?php echo $this->_['name'] ?></strong></p>
<p>Soll die Buchung trotzdem durchgeführt werden? Eventuell können Doppelbuchungen entstehen.</p>
<a href="<?php echo SITE_ADDRESS ?>?view=cancel" class="button">Nein, Abbruch!</a>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=bookme" ?>">
    <input type="hidden" name="event_id" value="<?php echo $this->_['event_id'] ?>" />
    <input type="hidden" name="name" value="<?php echo $this->_['name'] ?>" />
    <input type="hidden" name="givenname" value="<?php echo $this->_['givenname'] ?>" />
    <input type="hidden" name="email" value="<?php echo $this->_['email'] ?>" />
    <input type="hidden" name="persons" value="<?php echo $this->_['persons'] ?>" />
    <input type="hidden" name="from" value="<?php echo $this->_['from'] ?>" />
    <input type="hidden" name="eventname" value="<?php echo $this->_['eventname'] ?>" />
    <input type="hidden" name="mailtext" value="<?php echo $this->_['mailtext'] ?>" />
    <input type="hidden" name="overwrite" value="1" />
    <input type="submit" value="Trotzdem nochmal buchen." />
</form>