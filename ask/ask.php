<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a New Question</title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link href='/ask/ask.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fs_css/all.css' />
    <link rel='stylesheet' type='text/css' href='/global/js/trix.css' />

    <script src='/ask/ask.js' type='text/javascript'></script>
    <script src='/global/js/trix.js'></script>
</head>

<body onload='Ready()'>
    <?php
    echo file_get_contents('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='topInform'>
            <i class='fas fa-times' onclick='this.parentElement.remove()' style='cursor: pointer;position: absolute;right: 20px;'></i>
            <h2>Ask A Question</h2>
            <h3>
                Ask a Question with full information and easy to understand language by all. As you write your question
                then don't forget to tag your question with appropriate tags that area related to your question. You can
                also add some reasearch paper, images, internet document and many more that helps the community to give
                you the propor answer.
            </h3>
            <hr />
            <br />
            <h2>How To Ask A Quesion</h2>
            <ul>
                <li>Write A short But Descriptive title for your question.</li>
                <li>First check if the same question is already asked by someone else. This wil save your time.</li>
                <li>In body area write the complete detail about your question. Describe as much as you can with the
                    necessary info. If you had retained the information from any source, mention that also.</li>
                <li>If there is anything that can describe the Query more pricesly then you can attach thome images or
                    also link to video or any other online resources.</li>
                <li>Properly format your description in easy to read way by using provided formatting options. Like
                    hilighting important thing in quote. You can <a href='#'>read formatting guide.</a></li>
                <li>While writing also keep previewing to avoid any typo or unintentional mistake.</li>
            </ul>
        </div>
        <form class='AskQuestion' method='POST' action='/server/post_question.php'>
            <div class='WriterContainer'>
                <div class='inputContainer'>
                    <input class='inp' type='text' value='' name='title' placeholder='Write Question Title Here..' id='QuestionTitle' required='' minlength='10' maxlength='200' onkeyup="titlePreview(this)" />
                </div>
                <div class=' inputContainer'>
                    <input type='hidden' name='description' id='QuestionBody' value='' />
                    <div class='trixContainer'>
                        <trix-editor input='QuestionBody'></trix-editor>
                    </div>
                </div>
                <div class='tagComposer'>
                    <div class='inputContainer' style='display: none'>
                        <input class='inp' type='text' name='tags' placeholder='Tags' id='QuestionTags' required='' />
                    </div>
                    <div class='addTag inputContainer'>
                        <div class='addedTags'>
                        </div>
                        <i class='fas fa-plus addTag_icon' onclick='toggleAvailableTags(this)'>Add Tags</i>
                        <div class='availableTags'>
                            <input class='inp' type='text' id='searchAvailableTags' placeholder='filter tags' onkeyup='filterTag(this.value)' />
                        </div>
                    </div>
                </div>
            </div>
            <div class='inputContainer'>
                <input class='inp' type='submit' name='submit' value='Post Question' id='QuestionSubmit' onclick='submitForm()' />
            </div>
        </form>
    </div>
</body>
<script>
    var allTags = new String;
</script>
<?php

require_once('../server/global.php');

$conn = get_connection();

$allTags = $conn->query("SELECT GROUP_CONCAT(Name) FROM  Tags;") or die('There was an error...');
$allTags = $allTags->fetch_array(MYSQLI_NUM)[0];


// Give this value to javascript
echo "<script type='text/javascript'>allTags=" . json_encode($allTags) . "</script>";

$conn->close();

?>

</html>