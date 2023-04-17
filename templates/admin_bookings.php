<?php
if ($this->_['event']->registrations == 1) {
    echo "<h2>Eine Anmeldung für das Event</h2>";
} else {
    echo "<h2>" . $this->_['event']->registrations . " Anmeldungen für das Event \"" . $this->_['event']->name . "\"</h2>";
}
?>
<p><b><?php echo $this->_['event']->get_from_str() ?></b></p>
<?php
if ($this->_['event']->location != "") {
    echo "<p><b>" . $this->_['event']->location . "</b></p>";
}
?>
<table class="datatable">
    <thead>
        <tr>
            <th>Lfd. Nr.</th>
            <th>Name</th>
            <th>Pers.</th>
            <th class="no-mobile">Seit</th>
            <th data-orderable="false"></th>
        </tr>
    </thead>
    <tbody>
        <?php
        $i = 0;
        foreach ($this->_['bookings'] as $booking) {
        ?>
            <tr>
                <td><?php echo ++$i ?></td>
                <td><?php echo trim($booking['name']) ?>, <?php echo trim($booking['givenname']) ?></td>
                <td><?php echo $booking['persons'] ?></td>
                <td class="no-mobile" data-order="<?php echo strtotime($booking['create_date']) ?>"><?php echo date("d.m.Y H:i", strtotime($booking['create_date'])) ?></td>
                <td class="icons">
                    <form onsubmit="return confirm('Soll die Buchung von <?php echo trim($booking['givenname']) ?> <?php echo trim($booking['name']) ?> wirklich storniert werden?');" method="POST" action="<?php echo SITE_ADDRESS ?>?view=storno">
                        <input type="hidden" name="event_id" value="<?php echo $booking['event_id'] ?>" />
                        <input type="hidden" name="booking_id" value="<?php echo $booking['id'] ?>" />
                        <input type="image" class="button" src="images/icons/trash.svg" alt="Buchung stornieren" title="Buchung stornieren" />
                    </form>
                </td>
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>

<?php
if (count($this->_['stornos']) > 0) {
?>
    <h2>Stornierte Anmeldungen</h2>
    <table class="datatable">
        <thead>
            <tr>
                <th>Lfd. Nr.</th>
                <th>Name</th>
                <th>Pers.</th>
                <th class="no-mobile">Storno seit</th>
            </tr>
        </thead>
        <tbody>
            <?php
            $k = 0;
            foreach ($this->_['stornos'] as $storno) {
            ?>
                <tr>
                    <td><?php echo ++$k ?></td>
                    <td><?php echo trim($storno['name']) ?>, <?php echo trim($storno['givenname']) ?></td>
                    <td><?php echo $storno['persons'] ?></td>
                    <td class="no-mobile" data-order="<?php echo strtotime($booking['update_date']) ?>"><?php echo date("d.m.Y H:i", strtotime($storno['update_date'])) ?></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
<?php
}
?>