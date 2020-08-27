var sample_question;
var feed_container;

//Temp Code
var res;
///////////

function createQuestion(question) {
    let target = sample_question.cloneNode(true);

    target.getElementsByClassName('titleText')[0].textContent = question.title;
    target.getElementsByClassName('titleText')[0].setAttribute('href', '/thread/' + question.url);

    target.getElementsByClassName('description')[0].firstElementChild.textContent = question.info + '...';

    feed_container.appendChild(target);
}