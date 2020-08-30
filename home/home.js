var sample_question;
var feed_container;
var sample_tag_element;

var appended_questions = new Map;
var feeded_tags = new Map;

var notification;
/*
 * TODO:
 * While storing the direct html in the database
 * and also extracting only first 270 character to show up here 
 * I am pissed off.
 * Now while setting textContent it will display like &lp;h1 ...
 * If pushed directly as innerHTML there may be image or anything and also will be less than 270 visible character.
*/
function createQuestion(question) {
    /*Only sow unique Question..*/
    if (appended_questions.has(question.url)) {
        return;
    }

    let target = sample_question.cloneNode(true);

    target.getElementsByClassName('titleText')[0].textContent = question.title;
    target.getElementsByClassName('titleText')[0].setAttribute('href', '/thread/' + question.url);

    target.getElementsByClassName('description')[0].firstElementChild.innerHTML = question.info + '...';

    for (let i = 0, tags = question.tag.split(','); i < tags.length; i++) {
        let tag = sample_tag_element.cloneNode(true);
        tag.setAttribute('href', '/taggedfor/' + tags[i]);
        tag.textContent = tags[i];
        target.getElementsByClassName('tagContainer')[0].appendChild(tag);
        if(!feeded_tags.has(tags[i])) {
            // don't push directly becase single element can;t be chld of 2 so clone it
            showTags.appendChild(tag.cloneNode(true));
            feeded_tags.set(tags[i], true);
        }
    }
    feed_container.appendChild(target);
    appended_questions.set(question.url, true);
}

function bookmark(question){
    notify('This is notification to notify about a href apple', 0)
}