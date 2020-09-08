<?php
require_once('../server/get_feed.php');

$feedFetcher = new Getfeed;
$posts = json_encode(array());

$onloadScript = ""; // any js that needs to be executed when page loads
$stat = "Custom filtered Question...";
$loadQuestion = "unknown";
$param = "null";

if (isset($_GET['query'])) {
    $stat =  "Result for search '" . htmlspecialchars($_GET['query']) . "'";
    $loadQuestion = "SearchQuery";
    $param = urldecode($_GET['query']);
} else if (isset($_GET['by'])) {
    $loadQuestion = "ActivityBy";
    $param = $_GET['by'];
} else if (isset($_GET['questionby'])) {
    $loadQuestion = "QuestionBy";
    $param = $_GET['questionby'];
} else if (isset($_GET['answerby'])) {
    $loadQuestion = "AnswerBy";
    $param = $_GET['answerby'];
} else if (isset($_GET['taggedfor'])) {
    $loadQuestion = "TaggedFor";
    $param = urlencode($_GET['taggedfor']);
    $stat = "Question tagged for '" . $param . "'";
} else {
    /*
     * Ths is now implemented in javascript loadMore() method;
    */
    // $feedFetcher->Recent($posts);
    $stat = "Today's Selections...";
    $loadQuestion = "Recent";
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Askme | Help me for Homework</title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link href='/home/home.css' type="text/css" rel="stylesheet" />
    <link href='/thread/question_entity.css' type='text/css' rel='stylesheet' />
    <link rel='stylesheet' type='text/css' href='/global/fonts/all.css' />
    <script type='text/javascript' src='/global/global.js'></script>

    <script type='text/javascript' src='/home/home.js'></script>
</head>

<body onload='Ready();'>
    <?php
    echo file_get_contents('../global/navbar.php');
    ?>
    <div id='Main'>

        <div class='feed_title' style='color: var(--Niagara);margin: 20px 0;'><?php echo $stat; ?></div>
        <div class='QuestionFeed'>
            <div class='Question'>
                <div class='questionTitle'>
                    <a href='#' class='titleText'></a>
                    <span class='quickAction'>
                        <i class='fas fa-star bookmarkIcon' title='Pin this Question..' onclick="bookmark(this, true, 'QuestionId')"></i>
                        <a href='#' class='fas fa-reply reply_icon' title='Give answer to this Question...' onclick='notify("Go hit it!!")'></a>
                    </span>
                </div>
                <div class='description'>
                    <span></span>
                </div>
                <div class='questionInfo'>
                    <div class='tagContainer'>
                        <a href='#' class='tag'></a herf='#'>
                    </div>
                    <div class='askingUser'>
                        <a href='#' class='asker_name hv_border' title=''></a>
                        <span> updated on </span>
                        <span class='updated_on'></span>
                    </div>
                </div>
            </div>
        </div>
        <div class='ShowTags'>
            <h2 class='label'>Be Smart. The Smart way</h2>
            <div class='label'>
                <a href='/alltags/' style='color: var(--Niagara);'>See all Tag</a>
            </div>
        </div>
        <button id='loadMoreBtn'>load more</button>
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


        //response = <!--?php /*echo $posts;*/ ?-->;

        //response.forEach(obj => {
        //  createQuestion(obj);
        //});
        let loadQuestion = "<?php echo $loadQuestion; ?>";
        let param = "<?php echo $param; ?>"

        document.getElementById('loadMoreBtn').onclick = function() {
            loadMore(loadQuestion, 2, param);
        };

        // Only load few posts at first
        loadMore(loadQuestion, 2, param);

        notification = document.getElementsByClassName('notify')[0];

        eval(<?php echo $onloadScript; ?>);
    }
</script>

</html>