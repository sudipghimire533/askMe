function fillQuestion() {
    let Question = document.getElementsByClassName('Question')[0];

    let title = Question.getElementsByClassName('titleText')[0];
    title.textContent = thisQuestion.title;

    Question.getElementsByClassName('description')[0].firstElementChild.innerHTML =
        thisQuestion.info;

    let tagContainer = Question.getElementsByClassName('tagContainer')[0];
    for (let i = 0, tags = thisQuestion.tag.split(','); i < tags.length; i++) {
        let tag = document.createElement('a');
        tag.classList.add('tag');
        tag.setAttribute('href', '/taggedfor/' + tags[i]);
        tag.textContent = tags[i];
        tagContainer.appendChild(tag);
    }

    let nameContainer = Question.getElementsByClassName('asker_name')[0];
    nameContainer.setAttribute('href', '/profile/' + thisQuestion.authorPath);
    nameContainer.textContent = thisQuestion.authorName;

    Question.getElementsByClassName('added_on')[0].textContent =
        thisQuestion.addedOn.split(' ')[0];//do not need time after space
    Question.getElementsByClassName('updated_on')[0].textContent =
        thisQuestion.updatedOn.split(' ')[0];
    Question.getElementsByClassName('visited_for')[0].textContent =
        thisQuestion.visit;
    Question.getElementsByClassName('clapCount')[0].textContent =
        thisQuestion.claps;

    let bookmarkIcon = Question.getElementsByClassName('bookmarkIcon')[0];
    if (thisQuestion.isBookmarked != null) { // is question bookmarked?
        bookmarkIcon.classList.add('active');
        bookmark(bookmarkIcon, false);
    } else {
        bookmarkIcon.setAttribute("onclick", "bookmark(this, true, " + thisQuestion.id + ")");
    }

    let clapIcon = Question.getElementsByClassName('clap_icon')[0];
    if (thisQuestion.isClapped != null) { // is question clapped?
        clapIcon.classList.add('active');
        clap(clapIcon, false);
    } else {
        clapIcon.setAttribute("onclick", "clap(this, true, 'qn', " + thisQuestion.id + ")");
    }
    if (thisQuestion.authorId != thisUserId) { // if this is not written by current user. remove edit button
        Question.getElementsByClassName('edit_icon')[0].remove();
    } else { // this is question by the user currently active..
        Question.getElementsByClassName('edit_icon')[0].setAttribute('href', '/ask/ask.php?edit=1&id=' + thisQuestion.id);
    }
}
function clapLastStep(source) {
    source.onclick = function () {
        notify('You already clapped this item. Visit your profile for more action.', 1);
    };
    source.classList.add('active');
    source.classList.remove('inactive');
}

function clap(source, alsoSend = false, type = 'ans', id = 0) {
    if (alsoSend === true) {
        if (type == 'qn' || type == 'ans') {
            quickAction((type == 'qn') ? "clapQuestion" : "clapAnswer", id, function () {
                notify('Yummy! Tasty clap');
                let counterText = source.parentElement.getElementsByClassName('clapCount')[0];
                counterText.textContent = parseInt(counterText.textContent) + 1; //  increase the counter
                clapLastStep(source);
            });
        } else {
            notify('Unknown Request....', 2);
            return;
        }
    } else {
        clapLastStep(source);
    }
}

let sampleAnswer;
function fillAnswer(index) {
    let Answer = sampleAnswer.cloneNode(true);

    Answer.getElementsByClassName('description')[0].firstElementChild.innerHTML =
        allAnswers[index].info;
    Answer.setAttribute('id', 'Answer' + allAnswers[index].id);

    let nameContainer = Answer.getElementsByClassName('authorName')[0];
    nameContainer.setAttribute('href', '/profile/' + allAnswers[index].authorPath);
    nameContainer.textContent = allAnswers[index].authorName;

    Answer.getElementsByClassName('authorIntro')[0].textContent =
        allAnswers[index].authorIntro;

    let authorAvatar =
        Answer.getElementsByClassName('avatarContainer')[0].getElementsByTagName('img')[0];
    authorAvatar.setAttribute('src', '/user.png');
    authorAvatar.setAttribute('alt', allAnswers[index].authorName);
    authorAvatar.setAttribute('title', allAnswers[index].authorName);

    Answer.getElementsByClassName('added_on')[0].textContent =
        allAnswers[index].addedOn.split(' ')[0];//do not need time after space
    Answer.getElementsByClassName('updated_on')[0].textContent =
        allAnswers[index].updatedOn.split(' ')[0];
    Answer.getElementsByClassName('clapCount')[0].textContent =
        allAnswers[index].claps;

    let clapIcon = Answer.getElementsByClassName('clap_icon')[0];
    if (allAnswers[index].isClapped != null) { // is question clapped?
        clapIcon.classList.add('active');
        clap(clapIcon, false);
    } else {
        clapIcon.setAttribute("onclick", "clap(this, true, 'ans', " + allAnswers[index].id + ")");
    }


    if (allAnswers[index].authorId != thisUserId) { // if this is not the answer written by current user. remove edit button
        Answer.getElementsByClassName('edit_icon')[0].remove();
    }


    sampleAnswer.parentElement.appendChild(Answer);
    allAnswers[index] = null;
}

function editAnswer(source) {
    let answer = source.parentElement;
    while (!answer.classList.contains('Answer')) { // get the .Answer element
        answer = answer.parentElement;
    }
    let value = answer.getElementsByClassName('description')[0].innerHTML.trim();
    let trix = document.getElementsByTagName('trix-editor')[0];

    /*Add content to input and also in editor...*/
    document.getElementById('QuestionBody').value = value;
    trix.editor.setSelectedRange([0, 0]); // at the beginning
    trix.editor.insertHTML(value);

    /*Add editing indecator*/
    let ind = document.createElement('input');
    ind.setAttribute('type', 'hidden');
    ind.setAttribute('name', 'editAns');
    ind.value = answer.id.replace('Answer', '');
    document.getElementsByClassName('writerSection')[0].appendChild(ind); // append a editing indicator..

    answer.remove(); // remove for answer list while editing...
}