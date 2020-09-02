let previewTitle, previewBody, previewtags;
let sample_prev_tag;
let converter;

let focuson;

function titlePreview(elem) {
    previewTitle.textContent = elem.value.trim();
    setTimeout(titlePreview, 2 * 1000, elem);
}
let prev_length = 0;
function bodyPreview(elem) {
    content = new String(elem.value.trim());

    // if (!(content.length - prev_length < 5)) { return; }

    prev_length = content.length;
    /* Do not need to sanitaze everuthing here because php will*/
    // TODO:  replace all is not available in chrome. create that prototype
    previewBody.textContent = content;
    setTimeout(bodyPreview, 5 * 1000, elem);
}
function tagPreview(elem) {
    previewtags.innerHTML = "";
    let tags = elem.value.split(',');
    tags.forEach(function (tag, i) {
        tag = tag.trim();
        if (tag.length < 1) {
            return;
        }
        let target = sample_prev_tag.cloneNode();
        target.textContent = tag;
        previewtags.appendChild(target);
    });
    setTimeout(tagPreview, 2 * 1000, elem);
}
function submitForm(event) {

}
function Ready() {
    let previewArea = document.getElementById('QuestionPreview');
    previewTitle = previewArea.getElementsByClassName('prev_title')[0];
    previewBody = previewArea.getElementsByClassName('prev_body')[0];
    previewtags = previewArea.getElementsByClassName('prev_tagContainer')[0];

    sample_prev_tag = document.createElement('span');
    sample_prev_tag.classList.add('prev_tag');

    titlePreview(document.getElementById('QuestionTitle'));
    bodyPreview(document.getElementById('QuestionBody'));
    tagPreview(document.getElementById('QuestionTags'));

    document.getElementsByClassName('AskQuestion')[0].onsubmit = function (ev) {
        document.getElementById('QuestionBodyReal').value =
            document.getElementById('QuestionBody').value;
    }
}