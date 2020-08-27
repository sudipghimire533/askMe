let previewTitle, previewBody, previewtags;
let sample_prev_tag;

function titlePreview(elem) {
    previewTitle.textContent = elem.value.trim();
    setTimeout(titlePreview, 1 * 1000, elem);
}
function bodyPreview(elem) {
    previewBody.textContent = elem.value.trim();
    setTimeout(bodyPreview, 1 * 1000, elem);
}
function tagPreview(elem) {
    if (!elem.value.endsWith(',')) { return; }

    let tags = elem.value.split(',');
    tags.forEach(function (tag) {
        tag = tag.trim();
        if (tag.length < 3) {
            return;
        }
        let target = sample_prev_tag.cloneNode();
        target.textContent = tag;
        sample_prev_tag.parentElement.appendChild(target);
    });
    setTimeout(tagPreview, 1 * 1000, elem);
}
function Ready() {
    // Initilize the containers for previewing...
    {
        let previewArea = document.getElementById('QuestionPreview');
        previewTitle = previewArea.getElementsByClassName('prev_title')[0];
        previewBody = previewArea.getElementsByClassName('prev_body')[0];
        previewtags = previewArea.getElementsByClassName('prev_tagContainer')[0];
        sample_prev_tag = previewArea.getElementsByClassName('prev_tag')[0];

        titlePreview(document.getElementById('QuestionTitle'));
        bodyPreview(document.getElementById('QuestionBody'));
        tagPreview(document.getElementById('QuestionTags'));
    }
}