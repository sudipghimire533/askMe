var sample_question;
var feed_container;
var sample_tag_element;

var appended_questions = new Map;


function createQuestion(question) {
    /*Only sow unique Question..*/
    if (appended_questions.has(question.url)) {
        return;
    }

    let target = sample_question.cloneNode(true);

    target.getElementsByClassName('titleText')[0].textContent = question.title;
    target.getElementsByClassName('titleText')[0].setAttribute('href', '/thread/' + question.url);

    target.getElementsByClassName('description')[0].firstElementChild.textContent = question.info + '...';

    for (let i = 0, tags = question.tag.split(' '); i < tags.length; i++) {
        let tag = sample_tag_element.cloneNode(true);
        tag.setAttribute('herf', '/taggedfor/' + tags[i]);
        tag.textContent = tags[i];
        target.getElementsByClassName('tagContainer')[0].appendChild(tag);
    }
    feed_container.appendChild(target);
    appended_questions.set(question.url, true);
}