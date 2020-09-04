<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('global.php');

if (
	(isset($_POST['description']) == false) ||
	(isset($_POST['QuestionId']) == false)
) {
	echo "This is the form Receiving page";

	if (!(isset($_GET['test']))) {
		exit;
	}
}

$conn = get_connection();
$errorMessage;

$UserId = 2;
$QuestionId = $conn->real_escape_string(trim($_POST['QuestionId']));
$Description = $conn->real_escape_string(trim(htmlspecialchars($_POST['description'])));


/*
 * TODO:
 * Command is out of sync. may be due to multiquery.
*/
function fail($err, $lineno = __LINE__)
{
	global $conn, $QuestionId;
	$conn->close();
	echo "<i style='color:red;'>" . $err . ". At line no " . $lineno . "</i>";

	exit;
}
function sucess()
{
	global $conn, $QuestionId;
	echo "<br>Everything is done...";
	$res = $conn->query("SELECT URLTitle FROM Question WHERE Id=$QuestionId;") or fail($conn->error, __LINE__);
	print_r($res);
	exit;
	header("Location: /thread/$QuestionId");
	$conn->close();
	exit;
}

if (strlen($Description) < 20) {
	fail("Less than 20 character in Description. current count:" . strlen($Description));
}

$conn->autocommit(false);
$res = $conn->multi_query("INSERT INTO
			Answer(Author, WrittenFor, Description)
			VALUES
			($UserId, $QuestionId, '$Description');

			UPDATE Question SET LastActive=NOW() WHERE Id=$QuestionId;
			") or fail($conn->error, __LINE__);
if ($res == false) {
	fail("error in query...", __LINE__);
}
$conn->commit();
$conn->autocommit(true);


echo "<hr>";

print_r($conn);

sucess();



$conn->close();
exit;
