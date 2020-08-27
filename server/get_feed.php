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
                    Tags tg ON qt.Tag=tg.Id;");

        /*
         * Initially Encode the Array into Json
         */
        $response = json_encode($res->fetch_all(MYSQLI_ASSOC));

        /*
         * Now decoding will result in the associative array
        */
        $response = json_decode($response, true);

        /*
         * The join in sql will result in duplicating question with unique tag
         * We have to bring all that tag into a single url and remove other.
         * So this container is to be treated as Map to know the duplication of question
        */
        $titleMap = array();

        /*
         * When duplicate question is encountered store it's index in
         * original container so that can be removed later 
        */
        $to_remove = array();

        for ($i = 0; $i < count($response); $i++) {
            /* We can work with ['url'] because it is also unique */
            if (array_key_exists($response[$i]['url'], $titleMap)) {
                /*
                 * This qestion has already appeared before and only new is tag.
                 * For now we will just add the tag to the first question with same url
                */
                $response[$titleMap[$response[$i]['url']]]['tag'] .= " " . $response[$i]['tag'];
                /*
                 * Mark it to remove later
                */
                $to_remove[] = $i;
            } else {
                /*
                 * This is new question so store it's index
                */
                $titleMap[$response[$i]['url']] = $i;
            }
        }

        for ($i = 0; $i < count($to_remove); $i++) {
            /*
             * Remove the array and Also adjust
             */
            array_splice($response, $to_remove[$i] - $i, 1);
        }


        $response = json_encode($response);

        echo $response;
    }
};


/*
    temp code below
    */
if (isset($_GET['test'])) {
    $g = new Getfeed;
    $g->Recent();
}
