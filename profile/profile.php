<?php
require_once("../server/global.php");

$conn = get_conection();

$UserId = 1;

/* Fetch Personal data.. */
$res = $conn->query("SELECT
    CONCAT(user.Firstname, ' ', user.LastName) AS fullname,
    user.Email AS email,
    user.Phone AS phone,
    user.Location As location,
    user.Bio AS bio,
    user.Intro AS intro,
    user.UserName AS userName
    FROM User AS user WHere user.Id=$UserId;");
$row = $res->fetch_assoc();
$UserEmail = $row['email'];
$UserPhone = $row['phone'];
$UserName = $row['fullname'];
$userBio = $row['bio'];
$UserIntro = $row['intro'];
$UserLocation = $row['location'];


/* Get Progress Data */
$res = $conn->query("SELECT
    COUNT(Id) AS answerCount,
    SUM(ClapsCount) AS clapCount
    FROM Answers WHERE Author=$UserId;
");
$row = $res->fetch_assoc();
$AnswerCount = $row['answerCount'];
$ClapsCount = $row['clapCount'];
if (!($ClapsCount > 0)) {
    $ClapsCount = 0;
}

$res = $conn->query("SELECT
    COUNT(Id) FROM Question WHERE Author=$UserId;
");
$QuestionCount = $res->fetch_array()[0];

$res = $conn->query("SELECT
    COUNT(FollowedBy)
    FROM UserFollow
    WHERE FollowedBy = $UserId;
");
$FollowingCount = $res->fetch_array()[0];

$res = $conn->query("SELECT 
    COUNT(FollowedTo)
    FROM UserFollow WHERE
    FollowedTo = $UserId;
");
$FollowersCount = $res->fetch_array()[0];

$res = $conn->query("SELECT DISTINCT
    Name FROM Tags AS Tags
    CROSS JOIN UserTag AS UserTag WHERE UserTag.User=$UserId;");
$UserTags = array();

while ($row = $res->fetch_assoc()) {
    $UserTags[] = $row["Name"];
}


$conn->close();
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
                    <?php echo $QuestionCount; ?>
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
                    <?php echo $AnswerCount; ?>
                </b>
                <span>Answers</span>
            </span>
        </div>
        <div class='moreInfo'>
            <div class='infoBlock' id='intrestedIn'>
                <div class='label'>Passionate About: </div>
                <div class='innerBlock'>
                    <!--Sample tag
                    <a href='#' class='tag'>Programming</a>
                    -->
                    <?php
                    foreach ($UserTags as $tag) {
                        echo "<a href='../questions/taggedfor/$tag[2]' class='tag'>$tag</a>";
                    }
                    ?>
                </div>
            </div>
            <div class='infoBlock' id='contactMe'>
                <div class='label'>Contact: </div>
                <div class='innerBlock'>
                    <div>
                        <i class='fas fa-envelope'></i>
                        <a href='#' id='Email' class='hv_border'>
                            <?php echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fas fa-phone'></i>
                        <a href='#' id='Phone' class='hv_border'>
                            <?php echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fas fa-map-marker-alt'></i>
                        <a href='#' id='Location' class='hv_border'>
                            <?php echo $UserLocation; ?>
                        </a>
                    </div>
                </div>
            </div>
        </div>
</body>

</html>