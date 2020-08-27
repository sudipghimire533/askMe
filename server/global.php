<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

define("HOST_NAME", "localhost");
define("HOST_ADMIN", "sudip");
define("HOST_KEY", "password");
define("HOST_DB", "askme");

function get_connection(){
    $conn = new mysqli(HOST_NAME, HOST_ADMIN, HOST_KEY, HOST_DB) or die("Connect failed: %s\n". $conn->error);    
    return $conn;
}
?>
