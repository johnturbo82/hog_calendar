<a class="button" href="<?php echo SITE_ADDRESS . "?view=new_poll" ?>">+ Neue Umfrage</a>
<h2>Aktive Umfragen</h2>
<table>
    <tr>
        <th>Lfd. Nr.</th>
        <th>Name</th>
        <th class="no-mobile">Erstellt</th>
        <th></th>
    </tr>
    <?php
    $i = 0;
    foreach ($this->_['polls'] as $poll) {
        $link = SITE_ADDRESS . "?view=poll&poll_id=" . $poll->id;
    ?>
        <tr>
            <td><?php echo ++$i ?></td>
            <td><?php echo $poll->name ?></td>
            <td class="no-mobile"><?php echo date("d.m.Y H:i", strtotime($poll->create_date)) ?></td>
            <td class="icons">
                <a class="button" href="<?php echo $link ?>" title="An Umfrage teilnehmen">Teilnehmen</a>
                <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen">Ergebnisse</a>
                <a class="button" onClick="copyToClipboard('<?php echo $link ?>', this)" title="In die Zwischenablage kopieren">Kopieren</a>
                <a class="button whatsapp" href="whatsapp://send?text=Liebe Member,%0Ahier könnt ihr an der Umfrage %22<?php echo $poll->name ?>%22 ?> teilnehmen:%0A<?php echo $link ?>" title="Link zur Buchung per WhatsApp verschicken">
                    <img src="<?php echo SITE_ADDRESS ?>images/icons/whatsapp.svg" alt="Whatsapp" />
                </a>
            </td>
        </tr>
    <?php
    }
    ?>
</table>
<?php
if (count($this->_['inactive_polls']) > 0) {
?>
    <h2>Inaktive Umfragen</h2>
    <table>
        <tr>
            <th>Lfd. Nr.</th>
            <th>Name</th>
            <th class="no-mobile">Erstellt</th>
            <th></th>
        </tr>
        <?php
        $i = 0;
        foreach ($this->_['inactive_polls'] as $poll) {
        ?>
            <tr>
                <td><?php echo ++$i ?></td>
                <td><?php echo $poll->name ?></td>
                <td class="no-mobile"><?php echo date("d.m.Y H:i", strtotime($poll->create_date)) ?></td>
                <td class="icons">
                    <a class="button" href="<?php echo SITE_ADDRESS . "?view=poll_result&poll_id=" . $poll->id ?>" title="Ergebnisse anzeigen">Ergebnisse</a>
                </td>
            </tr>
        <?php
        }
        ?>
    </table>
<?php
}
?>