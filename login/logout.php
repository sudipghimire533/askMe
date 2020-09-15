<?php
if(!session_id()){
	session_start();
}

session_unset();
session_destroy();
if(isset($_GET['taketo'])){
	header("Location: ".$_GET['taketo']);
	exit;
}
echo "<a href='/login/'>login</a>";
exit;
?>