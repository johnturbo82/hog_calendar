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