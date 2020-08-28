function fillQuestion() {
    let Question = document.getElementsByClassName('Question')[0];

    let title = Question.getElementsByClassName('titleText')[0];
    title.textContent = thisQuestion.title;

    Question.getElementsByClassName('description')[0].firstElementChild.textContent = thisQuestion.info;

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

    Question.getElementsByClassName('added_on')[0].textContent = thisQuestion.addedOn;
    Question.getElementsByClassName('updated_on')[0].textContent = thisQuestion.updatedOn;
    Question.getElementsByClassName('visited_for')[0].textContent = thisQuestion.visit;

    // thisQuestion = null;
}