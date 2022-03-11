<?php
require_once('classes/Model.php');
require_once('config.php');

$db = new Model();
if (!$db->delete_booking($_POST['booking_id'], $_POST['event_id'])) {
    die("ERROR");
}
$heading = "Location: manage_event.php?event_id=" . $_POST['event_id'];
header($heading);
exit();