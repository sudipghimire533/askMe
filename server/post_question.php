<?php

require_once('global.php');

if (
    !(isset($_POST['title']) &&
        isset($_POST['description'])) &&
    isset($_POST['tags'])
) {
    echo "This is the form Receiving page";

    if (!(isset($_GET['test']))) {
        exit;
    }
}

$conn = get_connection();

$errorMessage;

function fail($err)
{
    global $conn;
    $conn->close();
    echo "<i style='color:red;'>" . $err . "</i>";

    exit;
}
function sucess($QuestionId)
{
    global $conn;
    global $URLTitle;
    echo "<h1>Question registerd at: </h1><a href='/thread/thread.php?id=$QuestionId'>/threads/$URLTitle</a>";

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

$Author = 1;

$Title =  $_POST['title'];
$Description = $_POST['description'];
$Tags  = $_POST['tags'];

$Title = $conn->real_escape_string(trim($Title));
$Description = $conn->real_escape_string(trim($Description));
$Tags = $conn->real_escape_string(trim($Tags));

function insertQuestion()
{
    global $conn, $Title, $Description, $Tags, $Author, $UserId, $URLTitle;

    $conn->autocommit(false);
    /*get Url title*/
    $URLTitle = str_replace(" ", "-", strtolower($Title));
    $URLTitle  = preg_replace("/[^A-Za-z0-9\-]/", '', $URLTitle);

    $res = $conn->query("SELECT Id FROM Question WHERE URLTitle='$URLTitle' LIMIT 1;");
    if ($res->num_rows != 0) {
        $URLTitle .= $UserId;
        $res = $conn->query("SELECT Id FROM Question WHERE URLTitle='$URLTitle' LIMIT 1;");
        if ($res->num_rows != 0) {
            fail("Cannot Register Question. Had you already posted similar question?");
        }
    }

    $res = $conn->query("INSERT INTO
            Question (Title, URLTitle, Author, Description)
            VALUES('$Title', '$URLTitle', '$Author', '$Description')
        ;") or fail("Cannot insert Data into Database Double check your Input and read the docs. Error: " . $conn->error);

    $QuestionId = $conn->insert_id;

    $linkTag = $conn->prepare("INSERT INTO
            QuestionTag (Question, Tag)
            VALUES ($QuestionId, ?)
        ;") or fail($linkTag->error);

    $tag = "";
    $linkTag->bind_param("i", $tag);

    for ($i = 0; $i < count($Tags); ++$i) {
        $tag = $Tags[$i];
        $linkTag->execute() or fail("Error while linking tag. " . $linkTag->error);
    }

    $linkTag->close();
    $conn->commit();

    sucess($QuestionId);
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
