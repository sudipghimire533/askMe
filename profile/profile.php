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

$thisUserId = 1;



if (isset($_GET['username'])) {
    $uname = $conn->real_escape_string(trim(urldecode($_GET['username'])));
} else {
    echo "No profile to show...";
    exit;
}

/*
 * Splitting of query is needed because
 * join will duplicate the data (like answerCount)
 * and not all subquery independent (like answer and answerClaps)
 */


/*
  * TODO:
  * Join all query into a multi query to
  * finnish all query in one contact with database
 */

$res = $conn->query("SELECT 
            user.Id as id,
            CONCAT(user.FirstName, ' ', user.LastName) as fullName,
            user.Email as email,
            user.Phone as phone,
            user.Location as location,
            user.Intro as intro,
            COUNT(qn.Id) as questionCount,
            (COUNT(uf.FollowedBy) != 0) as isFollowing,
            (
                SELECT COUNT(FollowedTo) FROM UserFollow WHERE FollowedTo=user.Id
            ) AS followers,
            (
                SELECT COUNT(FollowedBY) FROM UserFollow WHERE FollowedBy=user.Id
            ) AS following,
            (
                SELECT GROUP_CONCAT(ans.Id) FROM Answer ans WHERE ans.Author=user.Id
            ) AS answers,
            (
                 (SELECT COUNT(User) FROM QuestionClaps WHERE Question IN (qn.Id))
            ) AS questionClapCount
            FROM User user
            
            LEFT JOIN
            Question qn
            ON user.Id = qn.Author

            LEFT JOIN
            UserFollow uf
            ON (uf.FollowedBy = $thisUserId) AND (uf.FollowedTo=user.Id)

            Where user.UserName = '$uname';
        ") or fail($conn->error, __LINE__);

$res = $res->fetch_all(MYSQLI_ASSOC)[0];

$allAnswers = $res['answers'];

$UserId = $res['id'];
if ($UserId == null) { // There is no such User.....
    echo "<h1>We cannot get any user for this data.<br>Signin or share us with your friends to get registered on this id</h1>";
    $conn->close();
    exit;
}

$UserName = $res['fullName'];
$UserEmail = $res['email'];
$UserIntro = $res['intro'];
$UserLocation = $res['location'];
$UserPhone = $res['phone'];
$isFollowing = ($res['isFollowing'] == 1) ? true : false;
$FollowersCount = $res['followers'];
$FollowingCount = $res['following'];
$AnswerCount = count(explode(',', $res['answers']));
$QuestionCount = $res['questionCount'];

$questionClapsCount = $res['questionClapCount'];

$res = $conn->query("SELECT
            COUNT(ac.User) as answerClapCount
            FROM
            AnswerClaps ac
            WHERE ac.Answer IN ($allAnswers)
        ;") or fail($conn->error, __LINE__);

$answerClapCount = $res->fetch_all(MYSQLI_NUM)[0][0];

$ClapsCount = $questionClapsCount + $answerClapCount;


$res = $conn->query("SELECT
            GROUP_CONCAT(tg.Name) as tags
            FROM UserTag ut
            LEFT JOIN
            Tags tg
            ON tg.Id = ut.Tag
            WHERE ut.User = $UserId
        ") or fail($conn->error, __LINE__);
$UserTags = $res->fetch_all(MYSQLI_NUM)[0][0];
$UserTags = explode(",", $UserTags);

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Help me for Homework</title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fs_css/all.css' />
    <link href='/profile/profile.css' type='text/css' rel='stylesheet' />

    <script type='text/javascript' src='/global/global.js'></script>
    <?php
    if ($thisUserId == $UserId) {
        echo "<script type='text/javascript' src='/profile/profile_editor.js'></script>";
    }
    ?>
</head>

<body onload='Ready()'>
    <div id='Main'>
        <?php
        echo file_get_contents('../global/navbar.php');
        ?>
        <div class='profileContainer'>
            <?php
            if ($thisUserId == $UserId) { // visiting own profile..
                echo "<a class='editProfile' href='/profile/edit/profile_edit.php' style='color: white'>edit</a>";
            }
            ?>
            <div class='profileImage'>
                <img src='/user.png'></img>
            </div>
            <div class='profileInfo'>
                <div class='profileIdentity'>
                    <div class='profileName'>
                        <?php echo $UserName; ?>
                    </div>
                    <div class='followBtn <?php echo (($isFollowing == 0) ? 'inactive' : 'active'); ?>' onclick='follow(this, true, " . <?php $UserId ?>. ")' style='display: <?php echo ($thisUserId == $UserId) ? 'none' : 'inline-block'; ?>'>
                        <i class='fa fa-heart follow_icon'></i>
                        <span></span>
                    </div>
                    <br />
                    <span class='profileIntro'><?php echo $UserIntro; ?></span>
                </div>
                <div class='impressionContainer'>
                    <a href="/questionby/<?php echo $uname; ?>" class='questionCount impr hv_border'>
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
                    <a href="/home/home.php?questionby=<?php echo $UserId; ?>" class='clapCount impr hv_border'>
                        <b class='count'>
                            <?php echo $ClapsCount; ?>
                        </b>
                        <span>Claps</span>
                        <div class='hoverlay'>See All Posts <?php echo $UserName; ?> got clapped</div>
                    </a>
                    <a href="/user/user.php?followersof=<?php echo $UserId; ?>" class='followersCount impr hv_border'>
                        <b class='count'>
                            <?php echo $FollowersCount;  ?>
                        </b>
                        <span>Followers</span>
                        <div class='hoverlay'>See users following <?php echo $UserName; ?></div>
                    </a>
                    <a href="/answerby/<?php echo $uname; ?>" class='answerCount impr hv_border'>
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
                <span class='label'>Passionate About: </span>
                <div class='innerBlock'>
                    <!--Sample tag
                    <a href='#' class='tag'>Programming</a>
                    -->
                    <?php
                    foreach ($UserTags as &$tag) {
                        $tag = trim($tag);
                        if (strlen($tag) == 0) continue;
                        echo "<a href='/taggedfor/$tag' class='tag'>$tag</a>";
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
    </div>

    <div class='notifyCenter'>
        <div class='notify' style='display: none;'></div>
    </div>
</body>
<script>
    function followLastStep(source) {
        source.classList.add('active');
        source.classList.remove('inactive');
        source.onclick = function() {
            notify('You are already following ' + '<?php echo $UserName; ?>', 1);
        };
    }

    function follow(source, sendAlso = false, id) {
        if (sendAlso === true) {
            quickAction("follow", id, function() {
                notify("You are now following " + '<?php echo $UserName; ?>');
                followLastStep(source);
                let followersCounter = document.getElementsByClassName('followersCount')[0].getElementsByClassName('count')[0];
                followersCounter.textContent = parseInt(followersCounter.textContent) + 1;
            });

        } else {
            followLastStep(source);
        }
    }

    function Ready() {
        notification = document.getElementsByClassName('notify')[0];
    }
</script>

</html>