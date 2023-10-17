<h2>Anmeldungen für das Event "<?php echo $this->_['event']['event_name'] ?>"</h2>
<p>am <?php echo date("d.m.Y", strtotime($this->_['event']['event_date'])) ?></p>
<table class="datatable">
    <thead>
        <tr>
            <th>Lfd. Nr.</th>
            <th>Name</th>
            <th>Pers.</th>
            <th class="no-mobile">Seit</th>
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
            </tr>
        <?php
        }
        ?>
    </tbody>
</table>