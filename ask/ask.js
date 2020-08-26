let previewTitle, previewBody, previewtags;

function titlePreview(text) {
    previewTitle.textContent = text;
}
function bodyPreview(text) {
    previewBody.textContent = text;
}
function Ready() {
    // Initilize the containers for previewing...
    {
        let previewArea = document.getElementById('QuestionPreview');
        previewTitle = previewArea.getElementsByClassName('prev_title')[0];
        previewBody = previewArea.getElementsByClassName('prev_body')[0];
        previewtags = previewArea.getElementsByClassName('prev_tagContainer')[0];
    };
}