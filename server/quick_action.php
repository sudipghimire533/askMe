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
    if ($userId == $thisUserId) { // Do not follow yourself
        return 1;
    }
    $conn->query("INSERT INTO
                UserFollow
                (FollowedBy, FollowedTO)
                VALUES ($thisUserId, $userId)
        ;") or die($conn->error);
    return 0;
}

/*
 * This function is not ready to use for inpur with special characters like + & etc...
 * Proper encoding of post paramater should be done beforehand
 * text `c++` sent from client is recived as `c` in post paramater due to improper url encoding
 * and that's where i am missing something....
*/
function updateTags($tags)
{
    global $conn, $thisUserId;
    $tags = explode(urlencode(','), trim($tags));

    /*Delete all previous tags for clean insertion (no error for duplicate insertion)*/
    $conn->query("DELETE FROM UserTag WHERE User=$thisUserId;") or die($conn->error . " in line " . __LINE__);

    $conn->autocommit(false);

    $insertStmt = $conn->prepare("INSERT INTO
                UserTag (User, Tag) VALUES ($thisUserId, ?)
            ;") or die($conn->error . " in line " . __LINE__);

    $id = '';
    $insertStmt->bind_param('i', $id);

    foreach ($tags as &$tag) {
        $tag = $conn->real_escape_string(trim($tag));
        if (strlen($tag) == 0) continue;

        $res = $conn->query("SELECT Id FROM Tags WHERE Name='$tag'") or die($conn->error . " in line " . __LINE__);
        if ($res->num_rows == 0) {
            return "unknown tag " . $tag;
        }
        $id = $res->fetch_array(MYSQLI_NUM)[0][0];
        $insertStmt->execute() or die($insertStmt->error . " in line " . __LINE__);
    }

    $conn->commit();
    $conn->autocommit(true);

    return 0;
}

if (isset($_GET['target'])) { /*FOr action like clap and bookmark target is must*/
    if (isset($_GET['clapQuestion'])) {
        clapQuestion($_GET['target']);
    } else if (isset($_GET['clapAnswer'])) {
        clapAnswer($_GET['target']);
    } else if (isset($_GET['bookmark'])) {
        echo bookMark($_GET['target']);
    } else if (isset($_GET['follow'])) {
        echo follow($_GET['target']);
    }
} else if (isset($_POST['param']) && isset($_POST['data'])) { /*For profile editing action param is must*/
    if ($_POST['param'] == 'UpdateTags') {
        $data = $_POST['data'];
        echo updateTags($data);
    }
} else {
    echo "What you want to do?";
}

$conn->close();
