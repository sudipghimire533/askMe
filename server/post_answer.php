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

$thisUserId = 2;
$QuestionId = $conn->real_escape_string(trim($_POST['QuestionId']));
$Description = trim($_POST['description']);

$Editing = '';
if (isset($_POST['editAns'])) {
	$Editing = trim($_POST['editAns']);
}

/*However js will try to prevent XSS but still cant tak rist. this will be only necessary when XSS is intended
 * SO only prevent the attack noo need to care about question of this attacker.
*/
$Description = str_replace('<script>', '&lt;script>;', $Description);

function fail($err, $lineno = __LINE__)
{
	global $conn, $QuestionId;
	$conn->close();
	echo "<i style='color:red;'>" . $err . ". At line no " . $lineno . "</i>";

	exit;
}
function sucess()
{
	/* Redirect to orginal question */
	global $conn, $QuestionId;
	$res = $conn->query("SELECT URLTitle FROM Question WHERE Id=$QuestionId;") or fail($conn->error, __LINE__);
	header("Location: /thread/" . $res->fetch_array(MYSQLI_NUM)[0]);

	$conn->close();
	exit;
}

if (strlen($Description) < 20) {
	fail("Less than 20 character in Description. current count:" . strlen($Description));
}

$res = false;

/*
 * Do nott need transition for this small action...
 * $conn->autocommit(false);
 * Even if answer is updated/inserted but lastactive of question is not updated
 * (It is very rare) still do not have that much effect
*/

if ($Editing != '') { // this is editing of old answer...
	$Editing = $conn->real_escape_string($Editing);

	$res = $conn->query("SELECT
			Author FROM Answer
			WHERE Id=$Editing 
		;") or fail($conn->error, __LINE__);
	if ($res->num_rows == 0 || $res->fetch_array(MYSQLI_NUM)[0] != $thisUserId) {
		fail('You are editing answer that doesnot exist or you do not have permission to edit...', __LINE__);
	}

	$res = $conn->multi_query("UPDATE
			Answer
			SET Description='$Description'
		;") or fail($conn->error, __LINE__);
} else { // if this is not editing instead is a new answer....
	$res = $conn->query("INSERT INTO
			Answer(Author, WrittenFor, Description)
			VALUES
			($thisUserId, $QuestionId, '$Description')
		;") or fail($conn->error, __LINE__);
}

// for both case update last active....
$conn->query("UPDATE
			Question SET LastActive=NOW() WHERE Id=$QuestionId
		;") or fail($conn->error, __LINE__);

sucess();



$conn->close();
exit;
