<?php
$poll = $this->_['poll'];
if (isset($this->_['voted'])) {
?>
    <div class="notice"><?php echo $this->_['voted']; ?></div>
<?php
}
?>
<h2>Ergebnisse Umfrage "<?php echo $poll->name ?>"</h2>
<p><?php echo $poll->description ?></p>
<h3>Ergebnisse</h3>
<?php
foreach ($poll->options as $key => $option) {
?>
    <label class="poll results">
        <div class="share_visual" style="width: <?php echo $poll->poll_results[$key]['percentage'] ?>%;"></div>
        <div class="text">
            <?php echo $option ?>
        </div>
        <div class="votes">Stimmen: <?php echo (isset($poll->poll_results[$key]['absolute'])) ? $poll->poll_results[$key]['absolute'] : "0" ?></div>
    </label>
<?php
}
?>
<h3>Einzelabstimmungen</h3>
<table>
    <tr>
        <th>Lfd. Nr.</th>
        <th>Name</th>
        <th>Auswahl</th>
        <th class="no-mobile">Datum</th>
    </tr>
    <?php
    $k = 0;
    foreach ($this->_['results'] as $result) {
    ?>
        <tr>
            <td><?php echo ++$k ?></td>
            <td><?php echo trim($result['name']) ?>, <?php echo trim($result['givenname']) ?></td>
            <td><?php echo $poll->options[$result['vote']] ?></td>
            <td class="no-mobile"><?php echo date("d.m.Y H:i", strtotime($result['create_date'])); ?></td>
        </tr>
    <?php
    }
    ?>
</table>