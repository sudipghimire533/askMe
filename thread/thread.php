<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    echo "<i style='color:red;'>We need a thread to show..</i>";
    exit;
}

require_once("../server/thread.php");

$id = trim($_GET['id']);

$response = "No response from server";
$handler = new showQuestion;

$handler->getQuestionById($id, $response);

$QuestionInformation = $response;

$handler->getAnswerFor($id, $response);
$AnswerInformation = $response;
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help me for Homework</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='./thread.css' type="text/css" rel="stylesheet" />
    <link href='./question_entity.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />

    <script src='./thread.js' type='text/javascript'></script>
</head>

<body onload='Ready();'>
    <div id='Main'>
        <div class='threadSection'>
            <div class='Question'>
                <div class='questionTitle'>
                    <i class='qn_status fab fa-gripfire' title='Trending'></i>
                    <span class='titleText'></span>
                    <span class='quickAction'>
                        <i class='fas fa-bookmark'></i>
                        <i class='fas fa-star'></i>
                        <a href='#' class='fas fa-reply'></a>
                    </span>
                </div>
                <div class='description'>
                    <p>
                    </p>
                </div>
                <div class='questionInfo'>
                    <div class='tagContainer'> </div>
                    <span class='meta'>
                        <span class='label'>Posted by:</span>
                        <a href='#' class='asker_name meta'></a>
                    </span>
                    <span class='meta'>
                        <span class='label'>Added on:</span>
                        <span class='added_on'></span>
                    </span>
                    <span class='meta'>
                        <span class='label'>Updated on:</span>
                        <span class='updated_on'></span>
                    </span>
                    <span class='meta'>
                        <span class='label'>visit count:</span>
                        <span class='visited_for'></span>
                    </span>
                </div>
            </div>
            <div class='Answer'>
                <i class='fas fa-tick'></i>
                <div class='author'>
                    <span class='avatarContainer'>
                        <img src='' alt='' title='' class='avatar' />
                    </span>
                    <span class='authorAbout'>
                        <a href='#' class='authorName hv_border'></a><br />
                        <span class='authorIntro'></span>
                    </span>
                </div>
                <div class='ans_content'></div>
                <div class='impression'>
                    <span class='meta'>
                        <span class='label'>Added on:</span>
                        <span class='added_on'></span>
                    </span>
                    <span class='meta'>
                        <span class='label'>Updated on:</span>
                        <span class='updated_on'></span>
                    </span>
                </div>
            </div>
        </div>
    </div>
</body>
<script>
    var thisQuestion = <?php echo $QuestionInformation; ?>[0];
    var allAnswers = <?php echo $AnswerInformation; ?>;
    console.log(allAnswers);
    function Ready() {
        fillQuestion();
    }
</script>

</html>