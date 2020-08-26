<?php

$AskedQuestionCount  = 143;
$FollowingCount      = 728;
$ClapsCount          = 1098;
$FollowersCount      = 983;
$GivenAnswersCount   = 345;

$UserEmail      = 'sudipghimire533@gmail.com';
$UserPhone      = '9866008267';
$UserWebsite    = 'https://www.sudipghimire533.com';
$UserFacebook   = 'https://fb.com/sudip.ghimire';
$userTwitter    = 'https://twitter.com/sudip.ghimire';

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help me for Homework</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='./profile.css' type="text/css" rel="stylesheet" />
    <link href='../thread/question_entity.css' type='text/css' rel='stylesheet' />
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />
</head>

<body>
    <div id='Main'>
        <div class='impressionContainer'>
            <span class='questionCount impr'>
                <b class='count'>
                    <?php echo $AskedQuestionCount; ?>
                </b>
                <span>Questions</span>
            </span>
            <span class='followingCount impr'>
                <b class='count'>
                    <?php echo $FollowingCount; ?>
                </b>
                <span>Following</span>
            </span>
            <span class='clapCount impr'>
                <b class='count'>
                    <?php echo $ClapsCount; ?>
                </b>
                <span>Claps</span>
            </span>
            <span class='followersCount impr'>
                <b class='count'>
                    <?php echo $FollowersCount;  ?>
                </b>
                <span>Followers</span>
            </span>
            <span class='answerCount impr'>
                <b class='count'>
                    <?php echo $GivenAnswersCount; ?>
                </b>
                <span>Answers</span>
            </span>
        </div>
        <div class='moreInfo'>
            <div class='infoBlock' id='intrestedIn'>
                <div class='label'>Passionate About: </div>
                <div class='innerBlock'>
                    <a href='#' class='tag'>Programming</a>
                    <a href='#' class='tag'>Arts</a>
                    <a href='#' class='tag'>Phycology</a>
                    <a href='#' class='tag'>Writings</a>
                    <a href='#' class='tag'>Physics</a>
                    <a href='#' class='tag'>Phycology</a>
                    <a href='#' class='tag'>Writings</a>
                    <a href='#' class='tag'>Physics</a>
                </div>
            </div>
            <div class='infoBlock' id='contactMe'>
                <div class='label'>Contact: </div>
                <div class='innerBlock'>
                    <div>
                        <i class='fas fa-envelope'></i>
                        <a href='#' id='Email' class='hv_border'>
                            <?php  echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fas fa-phone'></i>
                        <a href='#' id='Phone' class='hv_border'>
                            <?php  echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fas fa-globe'></i>
                        <a href='#' id='Website' class='hv_border'>
                            <?php  echo $UserWebsite; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fab fa-facebook-square'></i>
                        <a href='#' id='Facebook' class='hv_border'>
                            <?php  echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fab fa-twitter-square'></i>
                        <a href='#' id='Twitter' class='hv_border'>
                            <?php  echo $userTwitter; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>