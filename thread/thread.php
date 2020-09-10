<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Askme | </title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link href='/thread/question_entity.css' type="text/css" rel="stylesheet" />
    <link href='/thread/thread.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fonts/all.css' />
    <link rel='stylesheet' type='text/css' href='/global/trix/trix.css' />

    <script src='/global/global.js' type='text/javascript'></script>
    <script src='/thread/thread.js' type='text/javascript'></script>
    <script src='/global/trix/trix.js'></script>
</head>


<?php

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_GET['url'])) {
    echo "<i style='color:red;'>We need a thread to show..</i>";
    exit;
}


$thisUserId = 1;

// set current userd in a global js var
echo "<script>var thisUserId = $thisUserId;</script>";

require_once("../server/thread.php");

$url = trim($_GET['url']);
$response = "No response from server";
$handler = new showQuestion;

$onloadScript = ""; // any js that needs to be executed when page loads
$id; // process anything else in id. getQuestionByUrl should set this

if ($handler->getQuestionByUrl($url, $response, $id) == false) { // if request failed
    // however show the nav bar
    echo file_get_contents('../global/navbar.php');
    // and die
    die($response);
} else {
    $QuestionInformation = $response;

    $handler->getAnswerFor($id, $response);
    $AnswerInformation = $response;
}
?>

<body onload='Ready();'>
    <?php
    echo file_get_contents('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='threadSection'>
            <div class='Question'>
                <div class='questionTitle'>
                    <span class='titleText'></span>
                    <span class='clapIcon' title='Claps Count'>
                        <i class='fas fa-thumbs-up clap_icon inactive' title='Give a Clap to this Post' onclick="clap()"></i>
                        <br>
                        <span class='clapCount'></span>
                    </span>
                </div>
                <div class='description'>
                    <p></p>
                </div>
                <div class='questionInfo'>
                    <div class='tagContainer'></div>
                    <span class='meta' title='Posted by'>
                        <i class='fas fa-user-astronaut'></i>
                        <a href='#' class='asker_name' onclick="notify('Getting you to '+this.textContent+' Profile')"></a>
                    </span>
                    <span class='meta' title='First Registered on'>
                        <i class='fas fa-calendar-week'></i>
                        <span class='added_on'></span>
                    </span>
                    <span class='meta' title='Last updated on'>
                        <i class='fas fa-calendar-alt'></i>
                        <span class='updated_on'></span>
                    </span>
                    <span class='meta' title='Viewed for'>
                        <i class='fas fa-eye'></i>
                        <span class='visited_for'></span>
                    </span>
                    <span class='meta quickAction'>
                        <i class='fas fa-star bookmarkIcon' onclick="bookmark(this, true, 'QuestionId')"></i>
                        <a href='#writeAnswer' class='fas fa-reply' onclick="notify('Go hit it!!')"></a>
                    </span>
                    <span class='meta'>
                        <a href='#edit' class='fas fa-pen edit_icon' title='Edit this post..'> Edit</a>
                    </span>
                </div>
            </div>
            <hr />
            <div id='AnswersContaner'>
                <div class='Answer'>
                    <div class='author'>
                        <span class='avatarContainer'>
                            <img src='' alt='' title='' class='avatar' />
                        </span>
                        <span class='clapIcon' title='Clpas Count'>
                            <i class='fas fa-thumbs-up clap_icon inactive' title='Give a Clap to this Post' onclick="clap('params....')"></i>
                            <br>
                            <span class='clapCount'></span>
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
                        <span class='meta'>
                            <a href='#writeAnswer' class='fas fa-pen edit_icon' title='Edit this Answer..' onclick='editAnswer(this)'> Edit</a>
                        </span>
                        <span class='meta'>
                            <i class='fas fa-trash ans_trash' title='Delete this Answer...' onclick='removeAnswer(this)'> Delete</i>
                        </span>
                    </div>
                </div>
                <a name='writeAnswer'></a>
                <form class='writerSection' method='POST' action='/server/post_answer.php' id='answerForm'>
                    <div class=' inputContainer'>
                        <input type='hidden' name='description' id='QuestionBody' value='' />
                        <div class='trixContainer'>
                            <trix-editor input='QuestionBody'></trix-editor>
                        </div>
                    </div>
                    <input type='hidden' name='QuestionId' value='<?php echo $id; ?>' />
                    <div class='inputContainer'>
                        <input class='inp' type="submit" name="submit" value='Post' id='PostSubmit' />
                    </div>
                </form>
            </div>
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
        notification = document.getElementsByClassName('notify')[0];
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

        document.getElementById('PostSubmit').parentElement.style.display = 'none'; // hide until editor gains focus
        addEventListener('trix-focus', function() {
            document.getElementById('PostSubmit').parentElement.style.display = 'block';
        });
    }
</script>

</html>