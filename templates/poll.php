<?php
$poll = $this->_['poll'];
?>
<h2>Teilnahme an Umfrage "<?php echo $poll->name ?>"</h2>
<p><?php echo $poll->description ?></p>
<?php
$i = 0;
?>
<form method="POST" action="<?php echo SITE_ADDRESS ?>?view=vote">
    <p><input type="text" name="givenname" placeholder="Vorname" value="<?php echo $_COOKIE['booking_givenname'] ?>" required /> *</p>
    <p><input type="text" name="name" placeholder="Name" value="<?php echo $_COOKIE['booking_name'] ?>" required /> *</p>
    <p><input type="email" name="email" placeholder="Email-Adresse" value="<?php echo $_COOKIE['booking_email'] ?>" /></p>
    <h3>Abstimmung und Ergebnisse</h3>
    <?php
    foreach ($poll->options as $key => $option) {
    ?>
        <label class="poll">
            <div class="share_visual" style="width: <?php echo $poll->poll_results[$key] ?>%;"></div>
            <div class="text">
                <input name="vote" type="<?php echo (!$poll->multichoice) ? "radio" : "checkbox" ?>" value="<?php echo $i++ ?>" /><?php echo $option ?>
            </div>
        </label>
    <?php
    }
    ?>
    <br />
    <input type="hidden" name="poll_id" value="<?php echo $poll->id ?>">
    <input type="submit" class="no-margin" value="Abstimmen">
</form>