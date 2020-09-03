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

function clapQuestion($postId)
{
    global $conn, $thisUserId;
    $postId = $conn->real_escape_string(trim($postId));

    $conn->query("INSERT INTO
        QuestionClaps  (Question, User)
        VALUES ($postId,$thisUserId)
    ;") or die($conn->error);
    return 0;
}

function clapAnswer($postId)
{
    global $conn, $thisUserId;
    $postId = $conn->real_escape_string(trim($postId));

    $conn->query("INSERT INTO
        AnswerClaps  (Answer, User)
        VALUES ($postId,$thisUserId)
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


if (isset($_GET['target'])) {
    if (isset($_GET['clapQuestion'])) {
        clapQuestion($_GET['target']);
    } else if (isset($_GET['clapAnswer'])) {
        clapAnswer($_GET['target']);
    } else if (isset($_GET['bookmark'])) {
        echo bookMark($_GET['target']);
    }
}

$conn->close();
