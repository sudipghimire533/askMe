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
    public function Recent(&$response)
    {
        $res = $this->conn->query("SELECT DISTINCT
                    qn.Title AS title,
                    qn.URLTitle AS url,
                    SUBSTRING(qn.Description, 1, 270) AS info,
                    GROUP_CONCAT(tg.Name) As tag
                    FROM
                    Question qn LEFT JOIN
                    QuestionTag qt ON qt.Question=qn.Id LEFT JOIN
                    Tags tg ON qt.Tag=tg.Id
                    GROUP BY qn.Id ORDER BY qn.LastActive DESC
                    LIMIT 10
                ;") or fail($this->conn->error, __LINE__);

        $response = $res->fetch_all(MYSQLI_ASSOC);

        foreach ($response as &$row) {
            $row['info'] = html_entity_decode($row['info']);
        }

        $response = json_encode($response);
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
