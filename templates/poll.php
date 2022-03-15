<?php
$poll = $this->_['poll'];
?>
<h2>Teilnahme an Umfrage "<?php echo $poll->name ?>"</h2>
<p><?php echo $poll->description ?></p>
<?php
$i = 0;
?>
<form method="POST" action="<?php echo SITE_ADDRESS ?>?view=vote">
    <?php
    foreach ($poll->options as $key => $option) {
    ?>
        <label class="poll">
            <div class="share_visual" style="width: <?php echo $poll->poll_results[$key] ?>%;"></div>
            <div class="text">
                <input name="poll_<?php echo $poll->id ?>" type="<?php echo (!$poll->multichoice) ? "radio" : "checkbox" ?>" value="<?php echo $i++ ?>" /><?php echo $option ?>
            </div>
        </label>
    <?php
    }
    ?>
    <br />
    <input type="submit" class="no-margin" value="Abstimmen">
</form>