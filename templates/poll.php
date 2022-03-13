<h2>Teilnahme an Umfrage "<?php echo $this->_['poll']['name'] ?>"</h2>
<p><?php echo $this->_['poll']['description'] ?></p>
<?php
$options = explode("\n", $this->_['poll']['options']);
$i = 0;
?>
<form method="POST" action="<?php echo SITE_ADDRESS ?>?view=vote">
    <?php
    foreach ($options as $option) {
    ?>
        <label>
            <input name="poll_<?php echo $this->_['poll']['id'] ?>" type="<?php echo ($this->_['poll']['multichoice'] == 0) ? "radio" : "checkbox" ?>" value="<?php echo $i++ ?>" /><?php echo $option ?> <br />
        </label>
    <?php
    }
    ?>
    <br />
    <input type="submit" class="no-margin" value="Abstimmen">
</form>