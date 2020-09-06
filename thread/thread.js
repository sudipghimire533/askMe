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


    sampleAnswer.parentElement.appendChild(Answer);
    allAnswers[index] = null;
}

let previewContainer;
let prev_loop;
let stop = true;
function startPreview(elem, st = stop) {
    if (st == true) { return; }
    content = elem.value.trim();

    previewContainer.textContent = content;
    prev_loop = setTimeout(startPreview, 4 * 1000, elem, false);
}
function endPreview(elem) {
    setTimeout(function () {
        stop = true;
        clearTimeout(prev_loop);
        prev_loop = null;
    }, 5 * 1000);
    // When user stops typing and immeditly blur the input element then wait for next preview
}