<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
	session_start();
}

$thisUserId = -1;

function get_connection(){
    $conn = new mysqli(HOST_NAME, HOST_ADMIN, HOST_KEY, HOST_DB) or die("Connect failed: %s\n". $conn->error);    
    return $conn;
}

include_once '../login/vendor/autoload.php';

function getLoginStatus(){
	global $thisUserId;
	$fb = new Facebook\Facebook([
	    'app_id' => FB_ID,
	    'app_secret' => FB_KEY,
	    'default_graph_version' => 'v8.0',
	]);
	if(!empty(session_id()) &&
		isset($_SESSION['token']) &&
		!empty($_SESSION['userId'])){
			$thisUserId = $_SESSION['userId'];
			$conn = get_connection();
			$res = $conn->query("SELECT
					user.Picture as pic,
					user.UserName as uname
					FROM
					UserLogin ul
					LEFT JOIN
					User user
					ON user.Id=ul.LocalId
					WHERE ul.LocalId=$thisUserId
				;") or die($conn->error);
			$conn->close();
			if($res->num_rows > 0){
				$res = $res->fetch_all(MYSQLI_ASSOC)[0];
				$_SESSION['picture'] = $res['pic'];
				$_SESSION['userName'] = $res['uname'];
				return true;
			}
	}
	session_unset();
	session_destroy();
	return false;
}

?>
