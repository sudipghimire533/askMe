<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['id'])) {
    echo "<i style='color:red;'>We need a thread to show..</i>";
    exit;
}

// See post_answer.php
if (isset($_GET['answerPosted']) && $_GET['answerPosted'] == '1') {
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
    <script src='../global/global.js' type='text/javascript'></script>

    <script src='../global/js/showdown.min.js' type='text/javascript'></script>
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
                        <a href='#writeAnswer' class='fas fa-reply'></a>
                    </span>
                </div>
                <div class='description'>
                    <p></p>
                </div>
                <div class='questionInfo'>
                    <div class='tagContainer'></div>
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
                <div class='description'>
                    <p></p>
                </div>
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
            <a name='writeAnswer'></a>
            <form class='writerSection' method='POST' action='/server/post_answer.php'>
                <div class=' inputContainer'>
                    <div class='toolbar'>ToolBar</div>
                    <textarea placeholder='Write Question Description Here..' id='PostBody' required='' minlength='20' onfocus='startPreview(this,false);' onblur='endPreview()'></textarea>
                    <textarea name='description' id='PostBodyReal' style='display:none;' value=''></textarea>
                </div>
                <div id='PostPreview' class='Answer description' tabindex='0'>
                </div>
                <input type='text' name='QuestionId' value='<?php echo $id; ?>' style='display: none;' />
                <div class='inputContainer'>
                    <input type="submit" name="submit" value='Post' id='PostSubmit' />
                </div>
            </form>
        </div>
    </div>

    <div class='notifyCenter'>
        <div class='notify' style='display: none;'></div>
    </div>
</body>
<script>
    var thisQuestion = <?php echo $QuestionInformation; ?>[0];
    var allAnswers;
    var comverter;
    var notification;

    function Ready() {
        converter = new showdown.Converter();
        fillQuestion();
        allAnswers = <?php echo $AnswerInformation; ?>;
        sampleAnswer = document.getElementsByClassName('Answer')[0];
        if (allAnswers) {
            allAnswers.forEach(function(ans, index) {
                fillAnswer(index);
            });
        }
        allAnswers = null;
        thisQuestion = null;

        previewContainer = document.getElementById('PostPreview');
        document.getElementsByTagName('form')[0].onsubmit = function(ev) {
            document.getElementById('PostBodyReal').value =
                converter.makeHtml(document.getElementById('PostBody').value);
        };

        notification = document.getElementsByClassName('notify')[0];
        confirmAnswerBox =
            /*set this variable according to answer posted status*/
            <?php
            if (isset($_GET['answerPosted'])) {
                echo $_GET['answerPosted'];
            } else {
                echo '0';
            }
            ?>;

        if (confirmAnswerBox == '1') { // answer has been registered
            notify("Awesome!!You answer has been posted.", 0, 10);
        } else if (confirmAnswerBox == '2') { // unsucessful
            notify("Aww!!An error occured while posting answer. Try resubmitting or contact help center", 2, 20);
        }
    }
</script>

</html>