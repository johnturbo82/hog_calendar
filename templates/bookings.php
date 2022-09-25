<?php
if ($this->_['event']->registrations == 1) {
    echo "<h2>Eine Anmeldung für das Event</h2>";
} else {
    echo "<h2>" . $this->_['event']->registrations . " Anmeldungen für das Event \"" . $this->_['event']->name . "\"</h2>";
}
?>
<p><b><?php echo $this->_['event']->get_from_str() ?></b></p><?php
if ($this->_['event']->location != "") {
    echo "<p><b>" . $this->_['event']->location . "</b></p>";
}
?>
<table>
    <tr>
        <th>Lfd. Nr.</th>
        <th>Name</th>
        <th>Pers.</th>
        <th class="no-mobile">Seit</th>
    </tr>
    <?php
    $i = 0;
    foreach ($this->_['bookings'] as $booking) {
    ?>
        <tr>
            <td><?php echo ++$i ?></td>
            <td><?php echo trim($booking['name']) ?>, <?php echo trim($booking['givenname']) ?></td>
            <td><?php echo $booking['persons'] ?></td>
            <td class="no-mobile"><?php echo date("d.m.Y H:i", strtotime($booking['create_date'])) ?></td>
        </tr>
    <?php
    }
    ?>
</table>