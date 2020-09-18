<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
    session_start();
}

require_once("../server/global.php");


$conn = get_connection();

function fail($err, $line = 0)
{
    global $conn;
    echo "<i style='color:red'>" . __FILE__ . " in line " . $line . ". Error: " . $err . "</i>";
    $conn->close();
    exit;
}

$thisUserId = isset($_SESSION['userId'])? $_SESSION['userId'] : -1;


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
            user.PictureReal as picture,
            GROUP_CONCAT(qn.Id) as questions,
            (COUNT(uf.FollowedBy) != 0) as isFollowing,
            (
                SELECT COUNT(FollowedTo) FROM UserFollow WHERE FollowedTo=user.Id
            ) AS followers,
            (
                SELECT COUNT(FollowedBY) FROM UserFollow WHERE FollowedBy=user.Id
            ) AS following,
            (
                SELECT GROUP_CONCAT(ans.Id) FROM Answer ans WHERE ans.Author=user.Id
            ) AS answers

            FROM User user
            
            LEFT JOIN
            Question qn
            ON user.Id = qn.Author

            LEFT JOIN
            UserFollow uf
            ON (uf.FollowedBy = $thisUserId) AND (uf.FollowedTo=user.Id)

            Where user.UserName = '$uname'
        ;") or fail($conn->error, __LINE__);

$res = $res->fetch_all(MYSQLI_ASSOC)[0];
$allAnswers = $res['answers'];
$allQuestions = $res['questions'];

$allAnswers = ($allAnswers == null) ? -1 : $allAnswers;
$allQuestions = ($allQuestions == null) ? -1 : $allQuestions;

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
$UserPicture = $res['picture'];
$UserPhone = $res['phone'];
$isFollowing = ($res['isFollowing'] == 1) ? true : false;
$FollowersCount = $res['followers'];
$FollowingCount = $res['following'];
$AnswerCount = ($allAnswers == -1) ? 0 : count(explode(',', $allAnswers));
$QuestionCount = ($allQuestions == -1) ? 0 : count(explode(',', $allQuestions));

$ClapsCount = 0;
if (strlen($allAnswers) > 0) { // only when user had ever given answer...
    $res = $conn->query("SELECT
            (
                SELECT COUNT(User) FROM QuestionClaps WHERE Question IN ($allQuestions)
            ),
            (
                SELECT COUNT(user) FROM AnswerClaps WHERE Answer IN ($allAnswers)
            )
        ;") or fail($conn->error, __LINE__);
    $ClapsCount = $res->fetch_all(MYSQLI_NUM)[0];
    $ClapsCount = $ClapsCount[0] + $ClapsCount[1];
}

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

$res = $conn->query("SELECT
            qn.Id as id,
            qn.Title as title,
            qn.URLTitle as url,
            qn.Author as author
            FROM UserBookmarks ub
            LEFT JOIN
            Question qn
            ON (ub.Question=qn.Id) AND (ub.User = $UserId)
        ;") or fail($conn->error, __LINE__);
$userBookMarks = json_encode($res->fetch_all(MYSQLI_ASSOC));

$res = $conn->query("SELECT
        qn.Id as id,
        qn.Title as title,
        qn.URLTitle as url,
        qn.Author as author
        FROM Question qn
        WHERE qn.Author=$UserId
    ;");
$userQuestions = json_encode($res->fetch_all(MYSQLI_ASSOC));

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title> Askme | <?php echo $UserName; ?> </title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fonts/all.css' />
    <link href='/profile/profile.css' type='text/css' rel='stylesheet' />

    <script type='text/javascript' src='/global/global.js'></script>
</head>

<body onload='Ready()'>
    <?php
    require('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='profileContainer'>
            <?php
            if ($thisUserId == $UserId) { // visiting own profile..
                echo "<a class='editProfile' href='/profile_edit/profile_edit.php' style='color: white'>edit</a>";
            }
            ?>
            <div class='profileImage'>
                <img src='<?php echo $UserPicture; ?>'></img>
            </div>
            <div class='profileInfo'>
                <div class='profileIdentity'>
                    <div class='profileName'>
                        <?php echo $UserName; ?>
                    </div>
                    <div class='followBtn'>
                        <i class='fa fa-heart follow_icon'></i>
                        <span></span>
                    </div>
                    <br />
                    <span class='profileIntro'><?php echo $UserIntro; ?></span>
                </div>
                <div class='impressionContainer'>
                    <a href="#askedQuestion" class='questionCount impr hv_border'>
                        <b class='count'>
                            <?php echo $QuestionCount; ?>
                        </b>
                        <span>Questions</span>
                        <div class='hoverlay'>See Question by <?php echo $UserName; ?></div>
                    </a>
                    <a href='#' class='followingCount impr hv_border'>
                        <b class='count'>
                            <?php echo $FollowingCount; ?>
                        </b>
                        <span>Following</span>
                        <div class='hoverlay'>Currently <?php echo $UserName; ?> is following <?php echo $FollowingCount; ?> Users </div>
                    </a>
                    <a href="#askedQuestion" class='clapCount impr hv_border'>
                        <b class='count'>
                            <?php echo $ClapsCount; ?>
                        </b>
                        <span>Claps</span>
                        <div class='hoverlay'>See All Posts <?php echo $UserName; ?> got clapped</div>
                    </a>
                    <a href="#" class='followersCount impr hv_border'>
                        <b class='count'>
                            <?php echo $FollowersCount;  ?>
                        </b>
                        <span>Followers</span>
                        <div class='hoverlay'>Currently <?php echo $FollowersCount; ?> Users are following <?php echo $UserName; ?></div>
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
                <div class='label'>Contact:</div>
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
            <div class='infoBlock' id='pinnedQuestion'>
                <a name='pinnedQuestion'></a>
                <div class='label'><i class='fas fa-star'></i> Pinned Question</div>
                <div class='Question'>
                    <a href='#' title='visit this Question' class='title'></a>
                    <i class='fas fa-trash pin_trash' onclick="removeBookMark(this, 'id')" title='Remove ths Question from Your Bokmark List..'></i>
                </div>
            </div>
            <div class='infoBlock' id='askedQuestion'>
                <a name='askedQuestion'></a>
                <div class='label'><i class='fas fa-comment'></i> Asked Question</div>
            </div>
        </div>
    </div>
    <div class='notifyCenter'>
        <div class='notify' style='display: none;'></div>
    </div>
</body>
<script>
    var thisUser, profileUser;

    let userBookMarks = <?php echo $userBookMarks; ?>;
    let userQuestions = <?php echo $userQuestions; ?>;
    let bookmarkCount = 0;
    let questionSample;

    let npq, npqt, npqi;

    function fillQuestion(qn, parent, sample) {
        npq = questionSample.cloneNode(true);
        npqt = npq.getElementsByClassName('title')[0];
        npqt.setAttribute('href', '/thread/' + qn.url);
        npqt.textContent = qn.title;
        npqi = npq.getElementsByClassName('pin_trash')[0];
        if (qn.author == thisUser && parent.id == 'pinnedQuestion') {
            npqi.setAttribute('onclick', "removeBookMark(this, " + qn.id + ")");
        } else if (qn.author == thisUser && parent.id == 'askedQuestion') {
            npqi.setAttribute('onclick', "removeQuestion(this, " + qn.id + ")");
        } else {
            npqi.remove();
        }
        parent.appendChild(npq);
    }

    function Ready() {
        thisUser = <?php echo json_encode($thisUserId); ?>;
        profileUser = <?php echo json_encode($UserId); ?>

        let followBtn = document.getElementsByClassName('followBtn')[0];
        if (thisUser == profileUser) {
            followBtn.remove();
        } else if (<?php echo json_encode($isFollowing); ?> == true) {
            follow(followBtn, false, profileUser);
            followBtn.classList.add('active');
        } else {
            followBtn.classList.add('inactive');
            followBtn.onclick = function() {
                follow(followBtn, true, profileUser);
            };
        }



        questionSample = document.getElementsByClassName('Question')[0];

        let p = document.getElementById('pinnedQuestion');
        let count = 0;
        userBookMarks.forEach(qn => {
            if (qn.title != null) {
                fillQuestion(qn, p);
                count++;
            }
        });
        if (count == 0) {
            let txt = document.createElement('p');
            txt.textContent = 'No any pinned Question...';
            p.appendChild(txt);
        }
        count = 0;
        p = document.getElementById('askedQuestion');
        userQuestions.forEach(qn => {
            fillQuestion(qn, p);
            count++;
        });
        if (count == 0) {
            let txt = document.createElement('p');
            txt.textContent = 'No any Question yet...';
            p.appendChild(txt);
        }

        notification = document.getElementsByClassName('notify')[0];
    }

    function unfollow(source, id) {
        quickAction('unfollow', id, function() {
            notify('You took your follow back :( ');
            let followersCounter = document.getElementsByClassName('followersCount')[0].getElementsByClassName('count')[0];
            followersCounter.textContent = parseInt(followersCounter.textContent) - 1;
            source.classList.add('inactive');
            source.classList.remove('active');
            source.onclick = function() {
                follow(source, true, id);
            };
        });
    }

    function followLastStep(source, id = -1) {
        source.removeAttribute('onclick');
        source.classList.add('active');
        source.classList.remove('inactive');
        source.onclick = function() {
            unfollow(source, id);
        };
    }

    function follow(source, sendAlso = false, id = -1) {
        if (sendAlso === true) {
            quickAction("follow", id, function() {
                notify("You are now following " + '<?php echo $UserName; ?>');
                followLastStep(source, id);
                let followersCounter = document.getElementsByClassName('followersCount')[0].getElementsByClassName('count')[0];
                followersCounter.textContent = parseInt(followersCounter.textContent) + 1;
            });

        } else {
            followLastStep(source, id);
        }
    }

    function removeBookMark(source, id) {
        quickAction('removeBookMark', id, function() {
            while (!source.classList.contains('Question')) {
                source = source.parentElement;
            }
            source.remove();
            notify('Question had been removed from your list...');
        })
    }

    function removeQuestion(source, id) {
        if (!confirm('Do you really want to delete this Question...')) {
            return 1;
        }
        quickAction('removeQuestion', id, function() {
            while (!source.classList.contains('Question')) {
                source = source.parentElement;
            }
            source.remove();
            notify('That Question had been deleted');
        });
    }
</script>

</html>