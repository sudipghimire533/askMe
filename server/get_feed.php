<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("global.php");


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
    public function Recent()
    {
        /*
        $res = $this->conn->query("SELECT DISTINCT
                    qn.Title As title,
                    qn.URLTitle AS url,
                    SUBSTRING(qn.Description, 1, 270) AS info
                    FROM Question AS qn
                    ORDER BY qn.LastActive DESC
                    LIMIT 10;
                ");
        */
        $res = $this->conn->query("SELECT DISTINCT
                    qn.Title AS title,
                    qn.URLTitle AS url,
                    SUBSTRING(qn.Description, 1, 270) AS info,
                    tg.Name As tag
                    FROM
                    Question qn LEFT JOIN
                    QuestionTag qt ON qt.Question=qn.Id LEFT JOIN
                    Tags tg ON qt.Tag=tg.Id
                    LIMIT 10
                    ;");

        $response = json_encode($res->fetch_all(MYSQLI_ASSOC));

        echo $response;
    }
};
