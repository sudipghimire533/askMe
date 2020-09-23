<?php
require_once 'vendor/autoload.php';
require_once '../server/config.php';

$config = [
		'app_id' => FB_ID,
		'app_secret' => FB_KEY,
		'default_graph_version' => 'v8.0',
	];

$fb = new Facebook\Facebook($config);

?>