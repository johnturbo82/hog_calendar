<?php

date_default_timezone_set("Europe/Berlin");

include('config.php');
include('classes/Event.php');
include('classes/Poll.php');
include('classes/Controller.php');
include('classes/Model.php');
include('classes/View.php');
include('classes/ICS.php');

$safePost = filter_input_array(INPUT_POST);
$safeGet = filter_input_array(INPUT_GET);

$request = array_merge((is_array($safePost)) ? $safePost : array(), (is_array($safeGet)) ? $safeGet : array());

$controller = new Controller($request);
echo $controller->display();