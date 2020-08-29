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
    nameContainer.setAttribute('href', '/profile/id?=' + thisQuestion.authorId);
    nameContainer.textContent = thisQuestion.authorName;

    Question.getElementsByClassName('added_on')[0].textContent =
        thisQuestion.addedOn.split(' ')[0];//do not need time after space
    Question.getElementsByClassName('updated_on')[0].textContent =
        thisQuestion.updatedOn.split(' ')[0];
    Question.getElementsByClassName('visited_for')[0].textContent =
        thisQuestion.visit;
}

let sampleAnswer;
function fillAnswer(index) {
    let Answer = sampleAnswer.cloneNode(true);

    Answer.getElementsByClassName('description')[0].firstElementChild.innerHTML =
        allAnswers[index].info;

    let nameContainer = Answer.getElementsByClassName('authorName')[0];
    nameContainer.setAttribute('href', '/profile/id?=' + allAnswers[index].authorId);
    nameContainer.textContent = allAnswers[index].authorName;

    Answer.getElementsByClassName('authorIntro')[0].textContent =
        allAnswers[index].authorIntro;

    let authorAvatar = 
        Answer.getElementsByClassName('avatarContainer')[0].getElementsByTagName('img')[0];
    authorAvatar.setAttribute('src', '../user.png');
    authorAvatar.setAttribute('alt', allAnswers[index].authorName);
    authorAvatar.setAttribute('title', allAnswers[index].authorName);

    Answer.getElementsByClassName('added_on')[0].textContent =
        allAnswers[index].addedOn.split(' ')[0];//do not need time after space
    Answer.getElementsByClassName('updated_on')[0].textContent =
        allAnswers[index].updatedOn.split(' ')[0];

    sampleAnswer.parentElement.appendChild(Answer);

    allAnswers[index] = null;
}

let previewContainer;
let prev_loop;
let stop = true;
function startPreview(elem, st = stop){
    if(st == true){return;}
    content = elem.value.trim();

    previewContainer.innerHTML = converter.makeHtml(content.replaceAll('<', '&lt;'));
    prev_loop = setTimeout(startPreview, 4 * 1000, elem, false);
}
function endPreview(elem){
    setTimeout(function(){
        stop = true;
        clearTimeout(prev_loop);
        prev_loop = null;  
    }, 5*1000);
    // When user stops typing and immeditly blur the input element then wait for next preview
}