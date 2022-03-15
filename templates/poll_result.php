<?php
$poll = $this->_['poll'];
?>
<h2>Ergebnisse Umfrage "<?php echo $poll->name ?>"</h2>
<p><?php echo $poll->description ?></p>
<?php
$i = 0;
?>
<h3>Ergebnisse</h3>
<?php
foreach ($poll->options as $key => $option) {
?>
    <label class="poll">
        <div class="share_visual" style="width: <?php echo $poll->poll_results[$key] ?>%;"></div>
        <div class="text">
            <?php echo $option ?>
        </div>
    </label>
<?php
}
?>