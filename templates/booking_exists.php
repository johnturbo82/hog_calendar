<a class="button no-margin" href="<?php echo SITE_ADDRESS ?>?view=events">&laquo; Zur Eventübersicht</a>
<h2 class="error">Achtung!</h2>
<p>Es besteht bereits eine Buchung auf den Namen <strong><?php echo $this->_['booking']['givenname'] ?> <?php echo $this->_['booking']['name'] ?></strong> für die Veranstaltung <b>"<?php echo $this->_['event']->name ?>"</b> am <?php echo date("d.m.Y" , strtotime($this->_['event']->from)) ?>.</p>
<p>Du hast <strong><?php echo $this->_['booking']['persons'] ?> Person(en)</strong> angemeldet.</p>
<h2>Was möchtest du tun?</h2>
<h3>Lass mich sehen, wer sonst mit dabei ist.<h3>
<a class="button no-margin" href="<?php echo SITE_ADDRESS . "?view=bookings&event_id=" . $this->_['event']->id ?>"><?php echo $this->_['event']->registrations ?> angemeldete Teilnehmer</a>
<hr />
<h3>Buchung löschen?</h3>
<p>Ich habe mich anders entschieden und möchte die Buchung löschen. Sollten Kosten entstanden sein, bin ich bereit diese zu übernehmen.</p>
<form onsubmit="return confirm('Soll Deine Buchung auf den Namen <?php echo $this->_['booking']['givenname'] ?> <?php echo $this->_['booking']['name'] ?> wirklich storniert werden?');" method="POST" action="<?php echo SITE_ADDRESS . "?view=delete_booking" ?>">
    <input type="hidden" name="booking_id" value="<?php echo $this->_['booking']['id'] ?>" min="1" max="5" pattern="\d*">
    <input type="hidden" name="event_id" value="<?php echo $this->_['event']->id ?>" />
    <input type="submit" class="no-margin" value="Meine Buchung löschen" />
</form>
<hr />
<h3>Personenanzahl ändern?</h3>
<p>An der Anzahl der Personen hat sich was geändert. Inklusive mir will ich folgende Anzahl anmelden:</p>
<form method="POST" action="<?php echo SITE_ADDRESS . "?view=change_persons" ?>">
    <input type="number" name="persons" value="<?php echo $this->_['booking']['persons'] ?>" min="1" max="5" pattern="\d*">
    <input type="hidden" name="booking_id" value="<?php echo $this->_['booking']['id'] ?>" />
    <input type="hidden" name="event_id" value="<?php echo $this->_['event']->id ?>" />
    <input type="submit" value="Personenanzahl ändern" />
</form>
<hr />
<h3>Nichts, alles soll so bleiben wie es ist.</h3>
<a href="<?php echo SITE_ADDRESS ?>?view=cancel" class="button no-margin">Abbruch!</a>