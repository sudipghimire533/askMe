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
                UserBookmarks ub ON (ub.User = user.Id) AND (ub.Question = qn.Id)
                WHERE qn.Id IN ($question)
                GROUP BY qn.Id
            ;") or die($this->conn->error);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        foreach ($response as &$row) {
            $row['info'] = html_entity_decode($row['info']);
        }
        $response = json_encode($response);
    }
    public function Recent(&$response)
    {
        // Only select the desired post here
        $res = $this->conn->query("SELECT
                qn.Id FROM
                Question qn
                ORDER BY qn.LastActive DESC
                LIMIT 10
            ;");
        $questions = "";
        /* Question id should be comma seperated before sending to makePost */
        for ($i = 0; $i < $res->num_rows - 1; $i++) {
            $questions .= $res->fetch_row()[0] . ",";
        }
        $questions .= $res->fetch_row()[0]; // no comma at end
        $this->makePosts($questions, $response);
    }
    public function searchQuery($query, &$response)
    {
        $query = trim(urldecode($this->conn->real_escape_string($query)));
        if (!strlen($query) > 0) { // empty query
            $this->Recent($response);
            return 1;
        }
    }
};
