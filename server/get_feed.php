<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("global.php");

function fail($err, $lineno = __LINE__)
{
    echo "<i style='color:red;'>" . $err . ". At line no " . $lineno . "</i>";
    exit;
}

/*
 * TODO:
 * In all Getfeed's method implement a lower and upper bound
 * for limit.
 * In Feed the page number (may also be with ajax request)
 * Then the client will send the offset like 20,25
 * which indicated that we will put limit 20,25 in query and show end message if is out of bound...
 */
class Getfeed
{
    private $conn;

    public function  __construct()
    {
        $this->conn = get_connection();
    }

    public function __destruct()
    {
        $this->conn->close();
    }

    private function makePosts($question, &$response)
    {
        // Always give response from this function.
        $thisUser = 1;
        $res = $this->conn->query("SELECT DISTINCT
                qn.Title As title,
                qn.Id AS id,
                qn.URLTitle AS url,
                SUBSTRING(qn.Description,1, 270) AS info,
                GROUP_CONCAT(tg.Name) AS tag,
                CONCAT(user.FirstName, ' ', user.LastName) as authorName,
                user.Id AS authorId,
                ub.Question AS isBookmarked,
                qn.ModifiedOn As modifiedOn
                FROM Question qn
                LEFT JOIN
                QuestionTag qt ON qt.Question=qn.Id
                LEFT JOIN
                Tags tg ON qt.Tag=tg.Id
                LEFT JOIN
                User user ON user.Id = qn.Author
                LEFT JOIN
                UserBookmarks ub ON (ub.User=$thisUser) AND (ub.Question = qn.Id)
                WHERE qn.Id IN ($question)
                GROUP BY qn.Id
                ORDER BY qn.ModifiedOn DESC
            ;") or fail($this->conn->error, __LINE__);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        $response = json_encode($response);
        return 0;
    }

    public function Recent(&$response, &$notIn, &$count = 10)
    {
        $notIn = $this->conn->real_escape_string($notIn);
        $count = $this->conn->real_escape_string($count);

        $res = $this->conn->query("SELECT
                qn.Id  as Ids
                FROM
                Question qn
                WHERE qn.Id NOT IN ($notIn)
                ORDER BY qn.LastActive DESC
                LIMIT $count
            ;") or die($this->conn->error);
        if ($res->num_rows == 0) { // $notIn contains all of our posts.
            $response = 1;
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/

        return $this->makePosts($questions, $response);
    }

    public function searchQuery($query, &$response, $notIn, $count)
    {
        /*
         * SearchQuery() method also do not gurantee the result to $count
         * and i also don't know why
        */
        $query = $this->conn->real_escape_string(trim(urldecode($query)));
        $notIn = $this->conn->real_escape_string(trim(urldecode($notIn)));
        $count = $this->conn->real_escape_string(trim(urldecode($count)));
        if (strlen($query) == 0) { // empty query
            $this->Recent($response, -1);
            return 1;
        }
        $res = $this->conn->query("SELECT
                qn.Id as Ids
                FROM Question qn
                LEFT JOIN
                QuestionTag qt ON qt.Question=qn.Id
                LEFT JOIN
                Tags tg ON tg.Id=qt.Tag
                LEFT JOIN Answer ans ON ans.WrittenFor=qn.Id
                LEFT JOIN User user ON (user.Id = ans.Author) OR (user.Id = qn.Author)
                WHERE
                (
                    (qn.URLTitle LIKE '%$query%') OR
                    (qn.Title LIKE '%$query%') OR
                    (CONCAT(user.FirstName, user.LastName) LIKE '%$query%') OR
                    (tg.Name LIKE '%$query')
                ) AND
                qn.Id NOT IN ($notIn)
                LIMIT $count
        ;") or fail($this->conn->error, __LINE__);
        if ($res->num_rows == 0) {
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/

        return $this->makePosts($questions, $response);
    }


    public function postedBy($person, &$response, &$notIn, &$count)
    {
        $person = $this->conn->real_escape_string(trim(urldecode($person)));
        $notIn = $this->conn->real_escape_string(trim($notIn));
        $count = $this->conn->real_escape_string(trim($count));

        $res  = $this->conn->query("SELECT
                qn.Id FROM
                Question qn
                WHERE qn.Author=$person
                AND qn.Id NOT IN ($notIn)
                LIMIT $count
        ;") or fail($this->conn->error, __LINE__);
        if ($res->num_rows == 0) {
            $response = 1;
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/

        return $this->makePosts($questions, $response);
    }

    public function answerBy($person, &$response, &$notIn, &$count)
    {

        $person = $this->conn->real_escape_string(trim(urldecode($person)));
        $notIn = $this->conn->real_escape_string(trim($notIn));
        $count = $this->conn->real_escape_string(trim($count));

        $res  = $this->conn->query("SELECT
                qn.Id FROM
                Question qn
                LEFT JOIN
                Answer ans ON ans.WrittenFor = qn.Id
                WHERE ans.Author=$person
                AND qn.Id NOT IN($notIn)
                LIMIT $count
        ;") or fail($this->conn->error, __LINE__);
        if ($res->num_rows == 0) {
            $response = 1;
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/

        return $this->makePosts($questions, $response);
    }

    public function bookmarkBy($person, &$response, &$notIn, &$count)
    {
        $person = $this->conn->real_escape_string(trim(urldecode($person)));
        $notIn = $this->conn->real_escape_string(trim($notIn));
        $count = $this->conn->real_escape_string(trim($count));

        $res  = $this->conn->query("SELECT
                    qn.Id
                    FROM Question qn
                    LEFT JOIN
                    UserBookmarks ub IN ub.Question=qn.Id
                    WHERE ub.User=$person
                    AND qn.Id NOT IN ($notIn)
                    LIMIT $count
        ;") or fail($this->conn->error, __LINE__);
        if ($res->num_rows == 0) {
            $response = 1;
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/
    }

    public function activityBy($person, &$response, &$notIn, &$count)
    {
        /*
         * For activityBy() function we cannot gurantee that returened question can match 
         * $count paramater
         * At worst case it will be twise(when user has many answer and many question too)
         * but it will nor exceed $count*2
        */
        $got = $this->postedBy($person, $response, $notIn, $count);
        $res = ($got == 0) ? json_decode($response) : array();

        $got = $this->answerBy($person, $response, $notIn, $count);
        $response  = json_encode(
            array_merge(
                $res,
                ($got == 0) ? json_decode($response) : array()
            )
        );
    }

    public function taggedFor($tag, &$response, &$notIn, &$count)
    {
        $tag = $this->conn->real_escape_string(trim(urldecode($tag)));
        $notIn = $this->conn->real_escape_string(trim($notIn));
        $count = $this->conn->real_escape_string(trim($count));

        $res  = $this->conn->query("SELECT
                qt.Question
                FROM QuestionTag qt
                WHERE qt.Question NOT IN ($notIn)
                AND qt.Tag=(SELECT
                    Id FROM Tags Where Name='$tag'
                ) LIMIT $count;
        ;") or fail($this->conn->error, __LINE__);
        if ($res->num_rows == 0) {
            $response = 1;
            return 1;
        }
        $res = $res->fetch_all(MYSQLI_NUM);

        /* Join those ids with comma */
        $questions = "";
        for ($i = 0; $i < count($res) - 1; $i++) {
            $questions .= $res[$i][0] . ",";
        }
        $questions .= $res[count($res) - 1][0];
        /**************/

        return $this->makePosts($questions, $response);
    }
};

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (
        (isset($_POST['NotIn']) == false) ||
        (isset($_POST['Count']) == false) ||
        (isset($_POST['LoadQuestion']) == false) ||
        (isset($_POST['Param']) == false)
    ) {
        echo 2;
        //echo "Not all paramater found...";
        exit;
    }

    $notIn = trim($_POST['NotIn']);
    $count = trim($_POST['Count']);
    $param = trim($_POST['Param']);
    $loadQuestion = trim($_POST['LoadQuestion']);

    if (strlen($count) == 0 || strlen($notIn) == 0 || strlen($param) == 0) {
        echo 2;
        //echo "Empty paramater...";
        exit;
    }

    $handler = new Getfeed;

    $notIn = json_decode($notIn);
    $notIn = implode(',', $notIn);

    $response = "";
    $res = 1;

    if ($loadQuestion == 'Recent') {
        $res = $handler->Recent($response, $notIn, $count);
    } else if ($loadQuestion == 'TaggedFor') {
        $res = $handler->taggedFor($param, $response, $notIn, $count);
    } else if ($loadQuestion == 'QuestionBy') {
        $res = $handler->postedBy($param, $response, $notIn, $count);
    } else if ($loadQuestion == 'AnswerBy') {
        $res = $handler->answerBy($param, $response, $notIn, $count);
    } else if ($loadQuestion == 'BookmarkBy') {
        $res = $handler->bookmarkBy($param, $response, $notIn, $count);
    } else if ($loadQuestion == 'ActivityBy') {
        $res = $handler->activityBy($param, $response, $notIn, $count);
    } else if ($loadQuestion == 'SearchQuery') {
        $res = $handler->searchQuery($param, $response, $notIn, $count);
    } else { // unknown feed request..
        echo 2;
        //echo "Unknown Request...";
        exit;
    }
    if ($res == 0) {
        echo $response;
    } else {
        echo $res;
    }
}
