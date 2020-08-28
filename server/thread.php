<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('global.php');

class showQuestion
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
    public function getQuestionById($id, &$response)
    {
        $id = $this->conn->real_escape_string($id);
        $res = $this->conn->query("SELECT
                    qn.Title AS title,
                    qn.URLTitle AS url,
                    qn.Description AS info,
                    qn.ClapsCount As claps,
                    qn.AddedOn AS addedOn,
                    qn.VisitCount AS visit,
                    qn.ModifiedOn AS updatedOn,
                    CONCAT(user.FirstName, ' ', user.LastName) AS authorName,
                    user.Id AS authorId,
                    GROUP_CONCAT(tg.Name) As tag
                    FROM
                    Question qn
                    LEFT JOIN
                    QuestionTag qt ON qt.Question=qn.Id
                    LEFT JOIN
                    Tags tg ON qt.Tag=tg.Id
                    LEFT JOIN
                    User user ON qn.Author=user.Id
                    WHERE qn.Id = $id
                ;") or die($this->conn->error);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        if ($response[0]['title'] == null) {
            $response = "That Question or related information not found";
            return false;
        }
        $response = json_encode($response);
    }
};


/*
    temp code below
    */
if (isset($_GET['test'])) {
    $g = new Getfeed;
    $g->Recent();
}
