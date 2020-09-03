<?php

require_once('global.php');

$conn = get_connection();

$thisUserId = 1;

function bookMark($questionId)
{
    global $conn, $thisUserId;
    $questionId = $conn->real_escape_string(trim($questionId));
    $conn->query("INSERT INTO
            UserBookmarks (User, Question)
            VALUES($thisUserId, $questionId)
        ;") or die($conn->error);
    return 0;
}

function clap($postId, $type)
{
    global $conn, $thisUserId;
    $postId = $conn->real_escape_string(trim($postId));

    if ($type == 'qn') {
        $table = "QuestionClaps";
    } else if ($type == 'ans') {
        $table = "AnswerClaps";
    } else {
        return 1;
    }
    $colName = ($table == "QuestionClaps") ? "Question" : "Answer";
    $conn->query("INSERT INTO
            $table ($colName, User)
            VALUES ($postId, $thisUserId)
        ;") or die($conn->error);
    return 0;
}

function follow($userId)
{
    global $conn, $thisUserId;
    $userId = $conn->real_escape_string(trim($userId));

    $conn->query("INSERT INTO
                UserFollow
                (FollowedBy, FollowedTO)
                VALUES ($thisUserId, $userId)
        ;") or die($conn->error);
    return 0;
}


if (
    (isset($_GET['clapAnswer']) ||
        isset($_GET['clapQuestion'])) &&
    isset($_GET['clapTo'])
) {
    echo clap($_GET['clapTo'], (isset($_GET['clapAnswer']) ? "ans" : "qn"));
} else {
    echo "incomplete request...";
}

$conn->close();
