<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
    session_start();
}

require_once('global.php');

if (
    !(isset($_POST['title']) &&
        isset($_POST['description'])) &&
    isset($_POST['tags'])
) {
    echo "This is the form Receiving page";
}

$conn = get_connection();
$errorMessage;

$thisUserId = (int)$_SESSION['userId'];

$Author = $thisUserId;

function fail($err)
{
    global $conn;
    $conn->close();
    echo "<i style='color:red;'>" . $err . "</i>";

    exit;
}
function sucess()
{
    global $conn;
    global $URLTitle;
    echo "<h1>Question registerd at: </h1><a href='/thread/$URLTitle'>/threads/$URLTitle</a>";

    $conn->close();
    exit;
}

function validateTitle(&$title)
{
    global $errorMessage;
    $length = strlen($title);
    if ($length < 10) {
        $errorMessage =  "Title must be longer than ten character..";
        return false;
    } else if ($length > 200) {
        $errorMessage =  "Title Length mush be no more than 200 characters...";
        return false;
    }
    return true;
}

function validateDescription(&$description)
{
    global $errorMessage;
    $length = strlen($description);
    if ($length < 30) {
        $errorMessage  = "Description must be detailed, atleast of 30 character..";
        return false;
    }
    return true;
}

function validateTags(&$tags)
{
    global $conn, $errorMessage;
    $tags = $tags = explode(",", $tags);
    if (!count($tags) > 0) {
        $errorMessage = "Tag your question with atleast one tag..";
        return false;
    }

    // TODO: Remove duplicate tags...

    $stmt = $conn->prepare("SELECT Id FROM Tags WHERE Name = ? LIMIT 1;") or die("Failed to prepare sql statement");
    $tag = $tags[0];
    $stmt->bind_param("s", $tag);
    for ($i = 0; $i < count($tags); $i++) {
        $tag = trim($tags[$i]);
        $stmt->execute() or die($stmt->error);
        $res = $stmt->get_result();
        if ($res->num_rows == 0) {
            $errorMessage =  "Tag " . $tag . " is not available. Select another similar tag or request tag nammed " . $tag;
            return false;
        }
        /*Set the tags to respective index from table*/
        $tags[$i] = $res->fetch_array()[0];
    }
    $stmt->close();

    return true;
}

function validateEdit($editId)
{
    global $conn, $Author, $errorMessage;
    $editId  = $conn->real_escape_string($editId);
    $res = $conn->query("SELECT
        Author FROM Question WHERE Id=$editId
    ;") or fail($conn->error, __LINE__);
    if ($res->num_rows == 0) {
        $errorMessage = "You are editing non existing Question...";
        return false;
    }
    $res = $res->fetch_all(MYSQLI_NUM)[0][0]; // get author of that question...

    /*
     * This is also checked in main page but user can add a input element manually so check it again...
     * to prevent editingother question..
     */
    if ($res != $Author) {
        $errorMessage = 'You do not have permission to edit that Question....';
        return false;
    }
    return true;
}


$editId = '';
if (isset($_POST['isEdit'])) { /*If edit request...*/
    $editId = trim($_POST['isEdit']);
}

$Title =  $_POST['title'];
$Description = $_POST['description'];
$Tags  = $_POST['tags'];

$Title = $conn->real_escape_string(trim($Title));
$Description = $conn->real_escape_string(trim($Description));
$Tags = $conn->real_escape_string(trim($Tags));

function insertQuestion()
{
    global $conn, $Title, $Description, $Tags, $Author, $UserId, $URLTitle, $editId;

    $conn->autocommit(false);

    $editId = $conn->real_escape_string($editId);

    if ($editId == '') { // validate only if is not edit title-url as it is permanent
        /*get Url title*/
        $URLTitle = str_replace(" ", "-", strtolower($Title));
        $URLTitle  = preg_replace("/[^A-Za-z0-9\-]/", '', $URLTitle);

        $res = $conn->query("SELECT Id FROM Question WHERE URLTitle='$URLTitle';");
        if ($res->num_rows != 0) {
            $URLTitle .= $UserId;
            $res = $conn->query("SELECT Id FROM Question WHERE URLTitle='$URLTitle';");
            if ($res->num_rows != 0) {
                fail("You had already registered qustion with similar title. try updating that instead...");
            }
        }
    } else { // however we need utrltitle at last to redirect the user...
        $URLTitle = $conn->query("SELECT URLTitle FROM Question WHERE Id=$editId;") or fail($conn->error, __LINE__);
        $URLTitle = $URLTitle->fetch_array(MYSQLI_NUM)[0];
    }

    $QuestionId = -1; // this should be later set on either edit or insert request...

    if ($editId != '') {
        /*
         * If this is an edit request then clean all assciated tags to this question for clean insertion of all input tags
         * and update the values.. except question tag as it is same process for new question and edit
         */
        /*
         * Only title, description and last Mofdification date is to be updated(tags will updated later)
         */
        $conn->query("DELETE FROM
                    QuestionTag WHERE Question = $editId;") or fail($conn->error, __LINE__);
        $conn->query("UPDATE Question SET
                    Title='$Title',
                    Description='$Description',
                    ModifiedOn=Now()
                    WHERE Id=$editId
        ;") or fail($conn->error, __LINE__);

        $QuestionId = $editId;
    } else {
        // if not edit then insert...
        $res = $conn->query("INSERT INTO
            Question (Title, URLTitle, Author, Description)
            VALUES('$Title', '$URLTitle', '$Author', '$Description')
        ;") or fail("Cannot insert Data into Database Double check your Input and read the docs. Error: " . $conn->error);

        $QuestionId = $conn->insert_id;
    }

    $linkTag = $conn->prepare("INSERT INTO
            QuestionTag (Question, Tag)
            VALUES ($QuestionId, ?)
        ;") or fail($conn->error);

    $tag = "";
    $linkTag->bind_param("i", $tag);

    for ($i = 0; $i < count($Tags); ++$i) {
        $tag = $Tags[$i];
        $linkTag->execute() or fail("Error while linking tag. " . $linkTag->error);
    }

    $linkTag->close();
    $conn->commit();
    $conn->autocommit(true);

    sucess();
}

if ($editId != '') { // before anything else check for edit permission(if request is edit)....
    if (validateEdit($editId) == false) {
        fail($errorMessage);
    }
}
if (!(validateTitle($Title) &&
    validateDescription($Description) &&
    validateTags($Tags))) {
    echo "<br><i style='color:red'>" . $errorMessage . "</i><hr>";
    $conn->close();
    exit;
}

echo "All input considered valid<hr>";


insertQuestion();

$conn->close();
exit;
