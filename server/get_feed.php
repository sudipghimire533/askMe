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
        $res = $this->conn->query("SELECT DISTINCT
                qn.Title As title,
                qn.Id AS id,
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
                UserBookmarks ub ON (ub.User = user.Id) AND (ub.Question = qn.Id)
                WHERE qn.Id IN ($question)
                GROUP BY qn.Id
            ;") or die($this->conn->error);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        foreach ($response as &$row) {
            $row['info'] = html_entity_decode($row['info']);
        }
        $response = json_encode($response);
        return 0;
    }
    public function Recent(&$response)
    {
        // Only select the desired post here
        $res = $this->conn->query("SELECT
                GROUP_CONCAT(qn.Id)  as Ids
                FROM
                Question qn
                ORDER BY qn.LastActive DESC
                LIMIT 10
            ;");
        $questions = $res->fetch_all(MYSQLI_ASSOC)[0]['Ids'];
        return $this->makePosts($questions, $response);
    }
    public function searchQuery($query, &$response)
    {
        $query = trim(urldecode($this->conn->real_escape_string($query)));
        if (strlen($query) == 0) { // empty query
            $this->Recent($response);
            return 1;
        }
    }
    private function XbyHelper($person, &$response, &$query)
    {
        $person = trim($this->conn->real_escape_string($person));
        if (strlen($person) == 0) {
            $this->Recent($response);
            return 1;
        }
        $res = $this->conn->query(
            $query . $person
        ) or die($this->conn->error);
        $posts = $res->fetch_all(MYSQLI_ASSOC)[0]['Ids'];
        if ($posts == null) {
            return 1;
        }
        return $this->makePosts($posts, $response);
    }
    public function postedBy($person, &$response)
    {
        $query = "SELECT
                    GROUP_CONCAT(qn.Id) as Ids
                    FROM
                    Question qn
                    WHERE qn.Author=";
        return $this->XbyHelper($person, $response, $query);
    }
    public function answerBy($person, &$response)
    {
        $query = "SELECT
                GROUP_CONCAT(qn.Id) as Ids
                FROM
                Question qn
                LEFT JOIN
                Answer ans ON ans.WrittenFor = qn.Id
                WHERE ans.Author=";
        return $this->XbyHelper($person, $response, $query);
    }
    public function bookmarkBy($person, &$response)
    {
        $query = "SELECT GROUP_CONCAT(qn.Id) as Ids
                FROM
                Question qn
                LEFT JOIN
                UserBookmarks ub ON ub.Question = qn.Id
                WHERE ub.User=";
        return $this->XbyHelper($person, $response, $query);
    }
    public function activityBy($person, &$response)
    {
        $got = $this->postedBy($person, $response);
        $qns = ($got == 0) ? json_decode($response) : array();

        $got = $this->answerBy($person, $response);
        $ans = ($got == 0) ? json_decode($response) : array();

        $response = json_encode(array_merge($ans, $qns));
    }
};
