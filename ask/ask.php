<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ask a New Question</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='./ask.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />

    <script src='./ask.js' type='text/javascript'></script>
</head>

<body onload='Ready()'>
    <div id='Main'>
        <div class='topInform'>
            <i class='fas fa-cross' onclick='this.parentElement.remove()' style='cursor: pointer;position: absolute;right: 20px;'></i>
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
                    <input type='text' name='title' placeholder='Write Question Title Here..' id='QuestionTitle' required='' minlength='10' maxlength='200' />
                </div>
                <div class=' inputContainer'>
                    <div class='toolbar'>ToolBar</div>
                    <textarea name="description" placeholder='Write Question Description Here..' id='QuestionBody' required='' minlength='20'></textarea>
                </div>
                <div class='tagComposer'>
                    <div class='inputContainer'>
                        <input type='text' name='tags' placeholder='Tags' id='QuestionTags' required='' />
                    </div>
                    <i class='fas fa-corss addTagBtn'></i>
                </div>
            </div>
            <div id='QuestionPreview'>
                <div class='prev_title'></div>
                <p class='prev_body'></p>
                <div class='prev_tagContainer'>
                    <span class='prev_tag'></span>
                </div>
            </div>
            <div class='inputContainer'>
                <input type='submit' name='submit' value='Post Question' id='QuestionSubmit' />
            </div>
        </form>
    </div>
</body>

</html>