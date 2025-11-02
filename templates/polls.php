<a class="button" href="<?php echo SITE_ADDRESS . "?view=new_poll&admin=" . $this->_['admin'] ?>">+ Neue Umfrage</a>
<div class="legend">
    <h3>Legende</h3>
    <table>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/add.svg" alt="Teilnehmen" /></div>
            </td>
            <td>An Umfrage teilnehmen</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/list.svg" alt="Ergebnisse anzeigen" /></div>
            </td>
            <td>Ergebnisse anzeigen</td>
        </tr>
        <tr>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Umfrage schließen" /></div>
            </td>
            <td>Umfrage schließen</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></div>
            </td>
            <td>Link in Zwischenablage kopieren</td>
        </tr>
        <tr>
            <td>
                <div class="button whatsapp"><img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" /></div>
            </td>
            <td>Via WhatsApp versenden</td>
            <td>
                <div class="button"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Reaktivieren" /></div>
            </td>
            <td>Geschlossene Umfrage reaktivieren</td>
        </tr>
    </table>
</div>
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
                <a class="button" href="<?php echo $link ?>" title="An Umfrage teilnehmen"><img src="<?php echo SITE_ADDRESS ?>images/icons/add.svg" alt="Teilnehmen" /></a>
                <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen"><img src="<?php echo SITE_ADDRESS ?>images/icons/list.svg" alt="Ergebnisse anzeigen" /></a>
                <a class="button" href="<?php echo SITE_ADDRESS . "?view=inactivate_poll&poll_id=" . $poll->id ?>" title="Umfrage schließen"><img src="<?php echo SITE_ADDRESS ?>images/icons/stop.svg" alt="Umfrage schließen" /></a>
                <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren"><img src="<?php echo SITE_ADDRESS ?>images/icons/copy-to-clipboard.svg" alt="Kopieren" /></a>
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
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen"><img src="<?php echo SITE_ADDRESS ?>images/icons/list.svg" alt="Ergebnisse anzeigen" /></a>
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=activate_poll&poll_id=" . $poll->id ?>" title="Umfrage reaktivieren"><img src="<?php echo SITE_ADDRESS ?>images/icons/undo.svg" alt="Reaktivieren" /></a>
                </div>
            </div>
        <?php
        }
        ?>
    </div>
<?php
}
?>