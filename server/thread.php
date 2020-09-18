<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
    session_start();
}

require_once('global.php');

class showQuestion
{
    private $conn, $thisUserId;

    public function  __construct()
    {
        $this->conn = get_connection();
        
        $this->thisUserId = -1;
        if(getLoginStatus()){
            $this->thisUserId = $this->conn->real_escape_string($_SESSION['userId']);
        }
    }
    public function __destruct()
    {
        $this->conn->close();
    }
    // TODO:
    // isBookmarked and isClapped is not working..
    public function getQuestionByUrl($url, &$response, &$id)
    {
        $url = $this->conn->real_escape_string(trim(urldecode($url)));
        /*
         * TODO:
         * OPTIMIZE This Query...
         * Also the query is not heavily tested
         * after creating seperate table for clapsCount for Answer
        */
        $res = $this->conn->query("SELECT
                    qn.Title AS title,
                    qn.Id AS id,
                    qn.Description AS info,
                    qn.AddedOn AS addedOn,
                    qn.VisitCount AS visit,
                    (
                        SELECT COUNT(User) FROM QuestionClaps WHERE Question = qn.Id
                    ) AS claps,
                    qn.ModifiedOn AS updatedOn,
                    CONCAT(user.FirstName, ' ', user.LastName) AS authorName,
                    user.Id AS authorId,
                    user.UserName AS authorPath,
                    GROUP_CONCAT(tg.Name) As tag,
                    (
                        SELECT (COUNT(Question) > 0) FROM UserBookmarks WHERE (User=$this->thisUserId) AND (Question=qn.Id)
                    ) as isBookmarked,
                    (
                        SELECT (COUNT(User) > 0) FROM QuestionClaps WHERE (User=$this->thisUserId) AND (Question=qn.Id)
                    ) as isClapped

                    FROM
                    Question qn
                    LEFT JOIN
                    QuestionTag qt ON qt.Question=qn.Id
                    LEFT JOIN
                    Tags tg ON qt.Tag=tg.Id
                    LEFT JOIN
                    User user ON qn.Author=user.Id
                    WHERE qn.URLTitle = '$url'
                    GROUP BY qn.Id
                ;") or die($this->conn->error);
        $response = $res->fetch_all(MYSQLI_ASSOC);
        if ($res->num_rows == 0) {
            $response = "That Question or related information not found";
            return false;
        }
        $id = $response[0]['id']; // set the id for further processing


        $this->conn->query("UPDATE Question
            SET VisitCount=VisitCount+1 
            WHERE Id=$id
            ;") or die($this->conn->error . " in lie " . __LINE__);

        $response = json_encode($response);
        return true;
    }
    public function getAnswerFor($id, &$response)
    {
        $id = $this->conn->real_escape_string($id);

        $res = $this->conn->query("SELECT
                    CONCAT(user.FirstName,' ', user.LastName) AS authorName,
                    user.Id AS authorId,
                    user.Intro AS authorIntro,
                    user.UserName AS authorPath,
                    user.Picture AS authorPicture,
                    ans.Description AS info,
                    ans.Id AS id,
                    ans.AddedOn AS addedOn,
                    ans.ModifiedOn As updatedOn,
                    (
                        SELECT COUNT(User) FROM AnswerClaps WHERE Answer=ans.Id
                    ) AS claps,
                    (
                        SELECT (COUNT(User) > 0) FROM AnswerClaps WHERE (Answer=ans.Id) AND (User=$this->thisUserId)
                    ) as isClapped
                    FROM
                    Answer ans
                    LEFT JOIN
                    User user On ans.Author=user.Id
                    WHERE ans.WrittenFor=$id
                    ORDER BY ans.ModifiedOn
                ;") or die($this->conn->error);
        if ($res->num_rows == 0) {
            $response = 0;
            return;
        }
        $response = $res->fetch_all(MYSQLI_ASSOC);
        $response = json_encode($response);
        return true;
    }
};
