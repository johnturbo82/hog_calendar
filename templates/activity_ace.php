<p>
    <?php if ($this->_['year'] > 1000) { ?>
        <a href="<?php echo SITE_ADDRESS ?>?view=activity_ace&amp;year=<?php echo $this->_['year'] - 1 ?>&amp;admin=<?php echo rawurlencode($this->_['admin']) ?>">&laquo; <?php echo $this->_['year'] - 1 ?></a>
    <?php } ?>
    <?php if ($this->_['year'] < $this->_['current_year']) { ?>
        <a href="<?php echo SITE_ADDRESS ?>?view=activity_ace&amp;year=<?php echo $this->_['year'] + 1 ?>&amp;admin=<?php echo rawurlencode($this->_['admin']) ?>"><?php echo $this->_['year'] + 1 ?> &raquo;</a>
    <?php } ?>
</p>
<h2>Activity Ace <?php echo htmlspecialchars((string)$this->_['year'], ENT_QUOTES, "UTF-8") ?></h2>
<p>Die meistgebuchten Teilnehmenden des Jahres.</p>
<?php if (empty($this->_['results'])) { ?>
    <p>Für dieses Jahr liegen noch keine Anmeldungen vor.</p>
<?php } else { ?>
    <table class="datatable">
        <thead>
            <tr>
                <th>Platz</th>
                <th>Vorname</th>
                <th>Nachname</th>
                <th>Anmeldungen</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($this->_['results'] as $index => $result) { ?>
                <tr>
                    <td><?php echo $index + 1 ?></td>
                    <td><?php echo htmlspecialchars($result['givenname'], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?php echo htmlspecialchars($result['name'], ENT_QUOTES, "UTF-8") ?></td>
                    <td><?php echo (int)$result['booking_count'] ?></td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
<?php } ?>