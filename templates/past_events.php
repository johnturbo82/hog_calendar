<h2>Vergangene Events</h2>
<div class="past_events">
    <table class="datatable" data-order='[[0, "desc"]]'>
        <thead>
            <tr>
                <th>Datum</th>
                <th>Event</th>
                <th>Anm.</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($this->_['event_list'] as $id => $event) {
            ?>
                <tr>
                    <td data-order="<?php echo strtotime($event['date']) ?>"><?php echo date("d.m.Y H:i", strtotime($event['date'])); ?></td>
                    <td><?php echo $event['name']; ?></td>
                    <td><a href="?view=past_bookings&event_id=<?php echo $id; ?>"><?php echo $event['attendee_count']; ?></a></td>
                </tr>
            <?php
            }
            ?>
        </tbody>
    </table>
</div>