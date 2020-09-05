let previewTitle, previewBody, previewtags;
let sample_prev_tag;
let converter;

let focuson;
function titlePreview(elem) {
    previewTitle.textContent = elem.value.trim();
}
let prev_length = 0;
function bodyPreview(elem) {
    content = new String(elem.value.trim());

    prev_length = content.length;
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
        if (!tagMap.has(tag)) {
            return;
        }
        let target = sample_prev_tag.cloneNode();
        target.textContent = tag;
        previewtags.appendChild(target);
    });
    setTimeout(tagPreview, 2 * 1000, elem);
}
function submitForm() {
    /*Pu the use input into real field which form will submit*/
    document.getElementById('QuestionBodyReal').value =
        document.getElementById('QuestionBody').value;


    /*Add all added tags textContent into the tag input filed by seperating them with (,) */
    let inputTags = document.getElementsByClassName('addedTags')[0].getElementsByClassName('tag');
    if (inputTags.length == 0) {
        return; // return if no tags given
    }
    let tagInput = document.getElementById('QuestionTags');
    tagInput.value = '';
    for (let i = 0; i < inputTags.length - 1; i++) {
        tagInput.value += inputTags[i].textContent + ',';
    }
    tagInput.value += inputTags[inputTags.length - 1].textContent; // no comma at the end
}

let tagMap = new Map;

function Ready() {
    let previewArea = document.getElementById('QuestionPreview');
    previewTitle = previewArea.getElementsByClassName('prev_title')[0];
    previewBody = previewArea.getElementsByClassName('prev_body')[0];
    previewtags = previewArea.getElementsByClassName('prev_tagContainer')[0];

    sample_prev_tag = document.createElement('span');
    sample_prev_tag.classList.add('prev_tag');

    bodyPreview(document.getElementById('QuestionBody'));
    tagPreview(document.getElementById('QuestionTags'));

    let tg = document.createElement('span');
    tg.classList.add('tag');
    tg.setAttribute('onclick', 'addTag(this)');
    let avtg = document.getElementsByClassName('availableTags')[0];
    let new_tag;
    allTags.split(',').forEach(function (tag) {
        new_tag = tg.cloneNode();
        new_tag.textContent = tag.toLowerCase();
        avtg.appendChild(new_tag);
    });
}

function toggleAvailableTags(source) {
    source.textContent = '';
    source.classList.toggle('fa-times');
    source.classList.toggle('fa-add');
    document.getElementsByClassName('availableTags')[0].classList.toggle('active');
}
function addTag(source) {
    let tag = source.cloneNode(true);
    document.getElementsByClassName('addedTags')[0].appendChild(tag);
    source.classList.add('added');
}
function filterTag(query) {
    query = query.trim();
    let allTags = document.getElementsByClassName('availableTags')[0].getElementsByClassName('tag');
    if (query.length == 0) {
        for (let i = 0; i < allTags.length; i++) {
            allTags[i].classList.remove('inactive');
        }
        return;
    }
    for (let i = 0; i < allTags.length; i++) {
        if (allTags[i].textContent.indexOf(query) === -1) {
            allTags[i].classList.add('inactive');
        } else {
            allTags[i].classList.remove('inactive');
        }
    }
}