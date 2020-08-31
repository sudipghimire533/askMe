<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once("../server/global.php");

$conn = get_connection();

function fail($err, $line = 0)
{
    global $conn;
    echo "<i style='color:red'>" . __FILE__ . " in line " . $line . ". Error: " . $err . "</i>";
    $conn->close();
    exit;
}

$UserId = 1;

/* Fetch Personal data.. */
$res = $conn->query("SELECT
            CONCAT(user.Firstname, ' ', user.LastName) AS fullname,
            user.Email AS email,
            user.Phone AS phone,
            user.Location As location,
            user.Intro AS intro,
            user.UserName AS userName,
            COUNT(qn.Id) AS questionCount,
            COUNT(ans.Id) AS answerCount,
            SUM(ans.ClapsCount) AS clapCount
            FROM User AS user
            LEFT JOIN
            Answer ans ON ans.Author=user.Id
            LEFT JOIN
            Question  qn ON qn.Author=user.Id
            WHere user.Id=$UserId
        ;") or fail($conn->error);



$row = $res->fetch_assoc();
$UserName = $row['fullname'];

if ($UserName == null) {
    fail("We cannot get that User...");
}

$UserEmail = $row['email'];
$UserPhone = $row['phone'];
$UserIntro = $row['intro'];
$UserLocation = $row['location'];
$AnswerCount = $row['answerCount'];
$ClapsCount = $row['clapCount'];
// if user hasn't answered anything then it will be null so switch to 0
$ClapsCount = ($ClapsCount == null) ? 0 : $ClapsCount;
$QuestionCount = $row['questionCount'];


$res = $conn->query("SELECT
    COUNT(uf1.FollowedBy)
    FROM UserFollow uf1
    WHERE FollowedBy = $UserId
;") or fail($conn->error, __LINE__);
$FollowingCount = $res->fetch_array()[0];

$res = $conn->query("SELECT 
    COUNT(FollowedTo)
    FROM UserFollow WHERE
    FollowedTo = $UserId;
") or fail($conn->error, __LINE__);
$FollowersCount = $res->fetch_array()[0];



$res = $conn->query("SELECT
            GROUP_CONCAT(tg.Name)
            FROM Tags tg
            LEFT JOIN
            UserTag ut ON ut.Tag=tg.Id
            WHERE ut.User=$UserId
;") or fail($conn->error, __LINE__);
$UserTags = explode(',', $res->fetch_row()[0]);

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
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />
</head>

<body>
    <div id='Main'>
        <?php
        echo file_get_contents('../global/navbar.php');
        ?>
        <div class='profileContainer'>
            <div class='profileImage'>
                <img src='../user.png'></img>
            </div>
            <div class='profileInfo'>
                <div class='profileIdentity'>
                    <div class='profileName'><?php echo $UserName; ?></div>
                    <div class='followBtn'>
                        <i class='fa fa-heart'></i>
                        <span>Follow</span>
                    </div>
                    <div class='profileIntro'><?php echo $UserIntro; ?></div>
                </div>
                <div class='impressionContainer'>
                    <a href="/thread/thread.php?questionby=<?php echo $UserId; ?>" class='questionCount impr hv_border'>
                        <b class='count'>
                            <?php echo $QuestionCount; ?>
                        </b>
                        <span>Questions</span>
                        <div class='hoverlay'>See Question by <?php echo $UserName; ?></div>
                    </a>
                    <a href='/user/user.php?followedby=<?php echo $UserId; ?>' class='followingCount impr hv_border'>
                        <b class='count'>
                            <?php echo $FollowingCount; ?>
                        </b>
                        <span>Following</span>
                        <div class='hoverlay'>See users <?php echo $UserName; ?> is following</div>
                    </a>
                    <a href="/thread/thread.php?questionby=<?php echo $UserId; ?>" class='clapCount impr hv_border'>
                        <b class='count'>
                            <?php echo $ClapsCount; ?>
                        </b>
                        <span>Claps</span>
                        <div class='hoverlay'>See All Posts <?php echo $UserName; ?> giot clapped</div>
                    </a>
                    <a href="/user/user.php?followersof=<?php echo $UserId; ?>" class='followersCount impr hv_border'>
                        <b class='count'>
                            <?php echo $FollowersCount;  ?>
                        </b>
                        <span>Followers</span>
                        <div class='hoverlay'>See users following <?php echo $UserName; ?></div>
                    </a>
                    <a href="/thread/thread.php?answerby=<?php echo $UserId; ?>" class='answerCount impr hv_border'>
                        <b class='count'>
                            <?php echo $AnswerCount; ?>
                        </b>
                        <span>Answers</span>
                        <div class='hoverlay'>See Answers by <?php echo $UserName; ?></div>
                    </a>
                </div>
            </div>
        </div>
        <div class='moreInfo'>
            <div class='infoBlock' id='intrestedIn'>
                <div class='label'>Passionate About: </div>
                <div class='innerBlock'>
                    <!--Sample tag
                    <a href='#' class='tag'>Programming</a>
                    -->
                    <?php
                    foreach ($UserTags as &$tag) {
                        $tag = trim($tag);
                        if (strlen($tag) == 0) continue;
                        echo "<a href='../questions/taggedfor/$tag' class='tag'>$tag</a>";
                    }
                    ?>
                </div>
            </div>
            <div class='infoBlock' id='contactMe'>
                <div class='label'>Contact: </div>
                <div class='innerBlock'>
                    <div>
                        <i class='fas fa-envelope'></i>
                        <a href="mailto:<?php echo $UserEmail; ?>" id='Email' class='hv_border'>
                            <?php echo $UserEmail; ?>
                        </a>
                    </div>
                    <div>
                        <i class='fas fa-phone'></i>
                        <a href='#' id='Phone' class='hv_border'>
                            <?php echo $UserPhone; ?>
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