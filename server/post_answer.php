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

$UserId = 1;
$QuestionId = trim($conn->real_escape_string($_POST['QuestionId']));
$Description = trim($conn->real_escape_string(htmlspecialchars($_POST['description'])));


function fail($err, $lineno = __LINE__)
{
	global $conn, $QuestionId;
	$conn->close();
	echo "<i style='color:red;'>" . $err . ". At line no " . $lineno . "</i>";

	exit;
}
function sucess()
{
	global $QuestionId;
	echo "<br>Everything is done...";

	header("Location: /thread/thread.php?id=$QuestionId&answerPosted=1");
	exit;
}

if (strlen($Description) < 20) {
	fail("Less than 20 character in Description. current count:" . strlen($Description));
}

$res = $conn->query("
			INSERT INTO
			Answer(Author, WrittenFor, Description)
			VALUES
			($UserId, $QuestionId, '$Description');
			;") or fail($conn->error, __LINE__);
//TO DO:
// UPDATE Question SET LastActive=NOW() WHERE Id=$QuestionId;

sucess();



$conn->close();
exit;
