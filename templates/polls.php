<a class="button" href="<?php echo SITE_ADDRESS . "?view=new_poll" ?>">+ Neue Umfrage</a>
<h2>Aktive Umfragen</h2>
<div class="polls">
    <?php
    foreach ($this->_['polls'] as $poll) {
        $link = SITE_ADDRESS . "?view=poll&poll_id=" . $poll->id;
    ?>
        <div class="poll">
            <div class="cell"><strong><?php echo $poll->name ?></strong></div>
            <div class="cell"><?php echo date("d.m.Y H:i", strtotime($poll->create_date)) ?></div>
            <div class="cell right">
                <a class="button" href="<?php echo $link ?>" title="An Umfrage teilnehmen">Teilnehmen</a>
                <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen">Ergebnisse</a>
                <a class="button" href="<?php echo SITE_ADDRESS . "?view=inactivate_poll&poll_id=" . $poll->id ?>" title="Umfrage beenden">Beenden</a>
                <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren">Kopieren</a>
                <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr an der Umfrage %22<?php echo $poll->name ?>%22 teilnehmen:%0A<?php echo urlencode($link) ?>" title="Link zur Buchung per WhatsApp verschicken">
                    <img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" />
                </a>
            </div>
        </div>
    <?php
    }
    ?>
</div>
<?php
if (count($this->_['inactive_polls']) > 0) {
?>
    <h2>Inaktive Umfragen</h2>
    <div class="polls">
        <?php
        $i = 0;
        foreach ($this->_['inactive_polls'] as $poll) {
        ?>
            <div class="poll">
                <div class="cell"><?php echo $poll->name ?></div>
                <div class="cell"><?php echo date("d.m.Y H:i", strtotime($poll->create_date)) ?></div>
                <div class="cell right">
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen">Ergebnisse</a>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=activate_poll&poll_id=" . $poll->id ?>" title="Umfrage reaktivieren">Reaktivieren</a>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>