<?php
if(!session_id()){
    session_start();
}
require_once('global.php');

if(!isset($_SESSION['userId'])){
    echo -1;
    return 1;
}
$thisUserId = $_SESSION['userId'];

$conn = get_connection();

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

function unclapQuestion($postId)
{
    global $conn, $thisUserId;
    $postId = $conn->real_escape_string(trim($postId));

    $conn->query("DELETE FROM
        QuestionClaps
        WHERE (Question=$postId) AND (User=$thisUserId)
    ;") or die($conn->error);
    if ($conn->affected_rows == 0) {
        return 1;
    }
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

function unclapAnswer($postId)
{
    global $conn, $thisUserId;
    $postId = $conn->real_escape_string(trim($postId));

    $conn->query("DELETE FROM
        AnswerClaps
        WHERE (Answer=$postId) AND (User=$thisUserId)
    ;") or die($conn->error);

    if ($conn->affected_rows == 0) {
        return 1;
    }
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
        ;") or die($conn->error . " in line " . __LINE__);
    return 0;
}

function unfollow($userId)
{
    global $conn, $thisUserId;
    $userId = $conn->real_escape_string(trim($userId));
    $conn->query("DELETE
        FROM UserFollow
        WHERE (FollowedBy=$thisUserId) AND (FollowedTo=$userId)
    ;") or die($conn->error . " in line " . __LINE__);
    return 0;
}

function updateTags($tags)
{
    global $conn, $thisUserId;
    $tags = explode(',', trim($tags));

    /*Delete all previous tags for clean insertion (no error for duplicate insertion)*/
    $conn->query("DELETE FROM UserTag WHERE User=$thisUserId;") or die($conn->error . " in line " . __LINE__);
    
    $conn->autocommit(false);

    $insertStmt = $conn->prepare("INSERT INTO
                UserTag (User, Tag) VALUES ($thisUserId, ?)
            ;") or die($conn->error . " in line " . __LINE__);

    $id = '';
    $insertStmt->bind_param('i', $id);

    /*
    $tagMap = ''; // hash table to prevent insertion try for duplicate entry
    * the same process will be done by js in front end
    * if they are modifying it manually then domn't care...
    */
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

function updateName($name)
{
    global $conn, $thisUserId;
    /*
     * Todo Validate all characters.
    */
    $name = str_replace("  ", ' ', $name);
    $name = explode(' ', $name);
    if (count($name) != 2) {
        return 1;
    }
    $firstName = $conn->real_escape_string(htmlentities(htmlspecialchars(trim($name[0]))));
    $lastName = $conn->real_escape_string(htmlentities(htmlspecialchars(trim($name[1]))));

    $conn->query("UPDATE User 
            SET FirstName='$firstName',
            LastName='$lastName'
            WHERE Id=$thisUserId
        ;") or die($conn->error . " in line " . __LINE__);
    return 0;
}

function checkUserName(&$userName){
    
    return true;
}

function updateUserName($userName)
{
    global $conn, $thisUserId;

    /***********************************/
    $userName  = preg_replace("/[^A-Za-z0-9\-]/", '', $userName);
    if(strlen($userName) < 4){
        return 1;
    }
    $userName = $conn->real_escape_string((htmlentities(htmlspecialchars(trim($userName)))));
    $res = $conn->query("SELECT Id FROM User WHERE UserName='$userName';") or die($conn->error  . " in line ".__LINE__);
    if($res->num_rows != 0){
        return 1;
    }
    /***********************************/


    $userName = $conn->real_escape_string($userName);

    $conn->query("UPDATE User
            SET UserName='$userName' 
            WHERE Id=$thisUserId
    ;") or die($conn->error . " in line " . __LINE__);
    return 0;
}

function updateIntro($intro)
{
    global $conn, $thisUserId;

    $intro = $conn->real_escape_string(trim(htmlentities(htmlspecialchars(($intro)))));
    if (strlen($intro) < 5) {
        return 1;
    }

    $conn->query("UPDATE User
            SET Intro='$intro'
            WHERE Id=$thisUserId
    ;") or die($conn->error . " in line " . __LINE__);
    return 0;
}
function removeBookmark($id)
{
    global $conn, $thisUserId;
    $id = $conn->real_escape_string($id);

    /*
     * No need to check for permission as
     * in where clause User should be current user.
     */
    $conn->query("DELETE FROM 
            UserBookmarks
            WHERE (Question=$id) AND (User=$thisUserId)
        ;") or die($conn->error . " in line " . __LINE__);

    /* if nothing was deleted that means either id do not exist or thisUser do not have permission*/
    if ($conn->affected_rows == 0) {
        return 1;
    }
    return 0;
}
function removeQuestion($id)
{
    global $conn, $thisUserId;
    $id = $conn->real_escape_string($id);

    $res = $conn->query("SELECT
            Author
            FROM Question 
            WHERE Id=$id
        ;") or die($conn->error . " in line " . __LINE__);

    /* Not the question of current user.. */
    if ($res->num_rows == 0 || $res->fetch_all(MYSQLI_NUM)[0][0] != $thisUserId) {
        return 1;
    }


    $ans = $conn->query("SELECT
                GROUP_CONCAT(Id) FROM Answer WHERE
                WrittenFor=$id
            ;") or die($conn->error . " in line " . __LINE__);
    $ans = $ans->fetch_all(MYSQLI_NUM)[0][0];

    if ($ans == null) {
        $ans = -1;
    }

    $conn->autocommit(false);
    /*TODO
     * Make a single multiquery for following queries..
    */
    $conn->query("DELETE FROM UserBookmarks WHERE Question=$id;") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM AnswerClaps WHERE Answer IN ($ans);") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM Answer WHERE Id IN ($ans);") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM QuestionClaps WHERE Question=$id;") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM QuestionTag WHERE Question=$id;") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM Question WHERE Id=$id;") or die($conn->error . " in line " . __LINE__);

    $conn->commit();
    $conn->autocommit(true);

    return 0;
}

function removeAnswer($id)
{
    global $conn, $thisUserId;

    $id = $conn->real_escape_string($id);

    $res = $conn->query("SELECT Author FROM Answer WHERE Id=$id;") or die($conn->error . " in line " . __LINE__);
    if ($res->num_rows == 0 || $res->fetch_all(MYSQLI_NUM)[0][0] != $thisUserId) {
        return 1;
    }

    $conn->autocommit(false);
    /*TODO
     * Make a single multiquery for following queries..
    */
    $conn->query("DELETE FROM AnswerClaps WHERE Answer=$id;") or die($conn->error . " in line " . __LINE__);
    $conn->query("DELETE FROM Answer WHERE Id=$id;") or die($conn->error . " in line " . __LINE__);

    $conn->commit() or fail($conn->error . " in line " . __LINE__);
    $conn->autocommit(true);

    return 0;
}

if (isset($_POST['target'])) { /*FOr action like clap and bookmark target is must*/
    if (isset($_POST['clapQuestion'])) {
        clapQuestion($_POST['target']);
    } else if (isset($_POST['unclapQuestion'])) {
        unclapQuestion($_POST['target']);
    } else if (isset($_POST['clapAnswer'])) {
        clapAnswer($_POST['target']);
    } else if (isset($_POST['unclapAnswer'])) {
        unclapAnswer($_POST['target']);
    } else if (isset($_POST['bookmark'])) {
        echo bookMark($_POST['target']);
    } else if (isset($_POST['follow'])) {
        echo follow($_POST['target']);
    } else if (isset($_POST['unfollow'])) {
        echo unfollow($_POST['target']);
    } else if (isset($_POST['removeBookMark'])) {
        echo removeBookmark($_POST['target']);
    } else if (isset($_POST['removeQuestion'])) {
        echo removeQuestion($_POST['target']);
    } else if (isset($_POST['removeAnswer'])) {
        echo removeAnswer($_POST['target']);
    } else {
        echo 1;
    }
} else if (isset($_POST['param']) && isset($_POST['data'])) {
    if ($_POST['param'] == 'UpdateTags') {
        echo updateTags($_POST['data']);
    } else if ($_POST['param'] == 'UpdateName') {
        echo updateName($_POST['data']);
    } else if ($_POST['param'] == 'UpdateUserName') {
        echo updateUserName($_POST['data']);
    } else if ($_POST['param'] == 'UpdateIntro') {
        echo updateIntro($_POST['data']);
    } else {
        echo 1;
    }
} else {
    echo "What you want to do?";
}

$conn->close();
