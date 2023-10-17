<h2>Vergangene Events</h2>
<div class="past_events">
    <table>
        <tbody>
            <?php
            foreach ($this->_['event_list'] as $id => $event) {
            ?>
                <tr>
                    <td><?php echo date("d.m.Y H:i", strtotime($event['date'])); ?></td>
                    <td><?php echo $event['name']; ?></td>
                    <td><a href="?view=past_bookings&event_id=<?php echo $id; ?><?php echo ($this->_['admin']) ? "&admin=" . $this->_['admin'] : "" ?>"><?php echo ($event['attendee_count'] == 1) ? $event['attendee_count'] . " Anmeldungen" : $event['attendee_count'] . " Anmeldungen" ?></a></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>