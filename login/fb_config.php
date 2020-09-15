<?php
require_once 'vendor/autoload.php';
$config = [
		'app_id' => '3099077556887981',
		'app_secret' => '2b88e75f7b59d0446c45ad2951ee8505',
		'default_graph_version' => 'v8.0',
	];

$fb = new Facebook\Facebook($config);

?>