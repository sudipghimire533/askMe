function submitForm() {
    /*Add all added tags textContent into the tag input filed by seperating them with (,) */
    let inputTags = document.getElementsByClassName('addedTags')[0].getElementsByClassName('tag');
    if (inputTags.length == 0) {
        alert('Add at least one tag...');
        return; // return if no tags given
    }
    let tagInput = document.getElementById('QuestionTags');
    tagInput.value = '';
    for (let i = 0; i < inputTags.length - 1; i++) {
        tagInput.value += inputTags[i].textContent + ',';
    }
    tagInput.value += inputTags[inputTags.length - 1].textContent; // no comma at the end
}

/*
 * This hash help not to add the tags that were already present( while editing question)
*/
let tagMap = new Map;

let availableTag;
let addedTagSample;
let availableTagSample;
function Ready() {
    addedTagSample = document.createElement('span');
    addedTagSample.classList.add('tag');
    addedTagSample.setAttribute('onclick', 'removeTag(this)');
    availableTagSample = addedTagSample.cloneNode(true);
    availableTagSample.setAttribute('onclick', 'addTag(this)');

    /*Is this editing request....*/
    if (typeof(isEditing) != 'undefined') {
        document.getElementById('QuestionTitle').value = title; // populate title
        tags = tags.split(',');
        let smtg = addedTagSample.cloneNode(true);
        for (let i = 0; i < tags.length; ++i) {
            smtg.textContent = tags[i];
            addTag(smtg); // populate tags
            tagMap.set(tags[i], true);
        }

        smtg = document.createElement('input');
        smtg.setAttribute('name', 'isEdit');
        smtg.setAttribute('type', 'hidden'); // this should be hidden element
        smtg.value = parseInt(editQnId);
        document.getElementsByClassName('AskQuestion')[0].appendChild(smtg); // since it is hidden we can appedn anywhere inside form
    }

    availableTag = document.getElementsByClassName('availableTags')[0];
    let new_tag;
    allTags.split(',').forEach(tag => {
        if (tagMap.has(tag)) return; // do not insert in available tags if it is already in added tags(when editing...)
        new_tag = availableTagSample.cloneNode();
        new_tag.textContent = tag;
        availableTag.appendChild(new_tag);
        tagMap.set(tag, true);
    });
}

function toggleAvailableTags(source) {
    source.textContent = '';
    source.classList.toggle('fa-times');
    source.classList.toggle('fa-add');
    document.getElementsByClassName('availableTags')[0].classList.toggle('active');
}
let ntg;
function removeTag(source) {
    ntg = source.cloneNode(true);
    ntg.setAttribute('onclick', 'addTag(this)');
    ntg.classList.remove('added');
    availableTag.appendChild(ntg);
    source.remove();
}
function addTag(source) {
    let tag = source.cloneNode(true);
    // After removal again put that on available tag list
    tag.setAttribute('onclick', 'removeTag(this)');
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