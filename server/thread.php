<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('global.php');


/*
 * TODO:
 * In below query $thisUserId is used to get the id of user browsing the data
 * This means some work is to be done for annonomous user(who is not signed in)
 */
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
    public function getQuestionByUrl($url, &$response, &$id)
    {
        $url = $this->conn->real_escape_string(trim(urldecode($url)));
        /*
         * In below query 'thisUserId refers to the id of user browing this thread'
         * set that accordingly after implementing login
        */
        $thisUserId = 1;
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
                    ub.Question As isBookmarked,
                    uc.Question AS isClapped
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
                    LEFT JOIN
                    QuestionClaps uc ON (uc.Question = qn.Id) AND (uc.User = $thisUserId)
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
        /*
         * In below query 'thisUserId refers to the id of user browing this thread'
         * set that accordingly after implementing login
        */
        $thisUserId = 1;
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
                    ac.Answer As isClapped,
                    (
                        SELECT COUNT(User) FROM AnswerClaps WHERE Answer=ans.Id
                    ) AS claps
                    FROM
                    Answer ans
                    LEFT JOIN
                    AnswerClaps ac ON (ac.User = $thisUserId) AND (ac.Answer=ans.Id)
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
