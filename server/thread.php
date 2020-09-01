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
    // TODO:
    // isBookmarked and isClapped is not working..
    public function getQuestionById($id, &$response)
    {
        $id = $this->conn->real_escape_string($id);
        /*
         * In below query 'thisUserId refers to the id of user browing this thread'
         * set that accordingly after implementing login
        */

        /*
         * TODO:
         * OPTIMIZE This Query...
         * Also the query is not heavily tested
         * after creating seperate table for clapsCount for Answer
        */
        $thisUserId = 1;
        $res = $this->conn->query("SELECT
                    qn.Title AS title,
                    qn.URLTitle AS url,
                    qn.Description AS info,
                    qn.AddedOn AS addedOn,
                    qn.VisitCount AS visit,
                    (
                        SELECT COUNT(User) FROM QuestionClaps WHERE Question = $id
                    ) AS claps,
                    qn.ModifiedOn AS updatedOn,
                    CONCAT(user.FirstName, ' ', user.LastName) AS authorName,
                    user.Id AS authorId,
                    GROUP_CONCAT(tg.Name) As tag,
                    ub.Question As isBookmarked,
                    ub.Question AS isClapped
                    FROM
                    Question qn
                    LEFT JOIN
                    QuestionTag qt ON qt.Question=qn.Id
                    LEFT JOIN
                    Tags tg ON qt.Tag=tg.Id
                    LEFT JOIN
                    User user ON qn.Author=user.Id
                    LEFT JOIN
                    UserBookmarks ub ON (ub.Question = qn.Id) AND (ub.User = $thisUserId)
                    WHERE qn.Id = $id
                    GROUP BY qn.Id
                ;") or die($this->conn->error);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        if ($res->num_rows == 0) {
            $response = "That Question or related information not found";
            return false;
        }
        $response[0]['info'] = htmlspecialchars_decode($response[0]['info']);
        $response = json_encode($response);
        return true;
    }
    public function getAnswerFor($id, &$response, $offset = 0, $count = 3)
    {
        $id = $this->conn->real_escape_string($id);
        $res = $this->conn->query("SELECT
                    CONCAT(user.FirstName,' ', user.LastName) AS authorName,
                    user.Id AS authorId,
                    user.Intro AS authorIntro,
                    ans.Description AS info,
                    ans.AddedOn AS addedOn,
                    ans.ModifiedOn As updatedOn,
                    (SELECT COUNT(User) FROM AnswerClaps WHERE Answer=$id)
                    FROM
                    Answer ans
                    LEFT JOIN
                    User user On ans.Author=user.Id
                    WHERE ans.WrittenFor=$id
                    ORDER BY ans.ModifiedOn ASC
                    LIMIT $offset,$count
                ;") or die($this->conn->error);
        if ($res->num_rows == 0) {
            $response = 0;
            return;
        }
        $response = $res->fetch_all(MYSQLI_ASSOC);
        foreach ($response as &$row) {
            $row['info'] = htmlspecialchars_decode($row['info']);
        }
        $response = json_encode($response);
        return true;
    }
};
