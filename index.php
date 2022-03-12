<?php

include('config.php');
include('classes/Event.php');
include('classes/Controller.php');
include('classes/Model.php');
include('classes/View.php');

$request = array_merge($_GET, $_POST);
$controller = new Controller($request);
echo $controller->display();