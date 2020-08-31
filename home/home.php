<?php
require_once('../server/get_feed.php');

$feedFetcher = new Getfeed;

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help me for Homework</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='./home.css' type="text/css" rel="stylesheet" />
    <link href='../thread/question_entity.css' type='text/css' rel='stylesheet' />
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />
    <script type='text/javascript' src='../global/global.js'></script>

    <script type='text/javascript' src='home.js'></script>
</head>

<body onload='Ready();'>
    <?php
        echo file_get_contents('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='QuestionFeed'>
            <div class='Question'>
                <div class='questionTitle'>
                    <i class='qn_status fab fa-gripfire' title='Trending'></i>
                    <a href='#' class='titleText'></a>
                    <span class='quickAction'>
                        <i class='fas fa-bookmark' title='Bookmark this question to visit later..' onclick='bookmark(this)'></i>
                        <i class='fas fa-star'></i>
                        <a href='#' class='fas fa-reply' title='Give answer to this Question...'></a>
                    </span>
                </div>
                <div class='description'>
                    <span></span>
                </div>
                <div class='questionInfo'>
                    <div class='tagContainer'>
                        <a href='#' class='tag'></a herf='#'>
                    </div>
                    <!--div class='asking_user'>
                        <span>Asked By</span>
                        <a href='#' class='asker_name hv_border'>Sudip Ghimire</a>
                        <span> on </span>
                        <div class='asked_date'>2020-07-34</div>
                    </div-->
                </div>
            </div>
        </div>
        <div class='ShowTags'>
            <h2 class='label'>Be Smart. The Smart way</h2>
            <div class='label'>
                <a href='#' style='color: var(--Yellow);'>See all Tag</a>
            </div>
        </div>

        <div class='notifyCenter'>
            <div class='notify' style='display: none;'></div>
        </div>
    </div>
</body>

<script>
    let response;
    var showTags;

    function Ready() {
        sample_question = document.getElementsByClassName('Question')[0];
        feed_container = sample_question.parentElement;
        showTags = document.getElementsByClassName('ShowTags')[0];
        sample_tag_element = sample_question.getElementsByClassName('tagContainer')[0].firstElementChild;

        response = <?php $feedFetcher->Recent(); ?>;
        response.forEach(obj => {
            createQuestion(obj);
        });

    }
    notification = document.getElementsByClassName('notify')[0];
</script>

</html>