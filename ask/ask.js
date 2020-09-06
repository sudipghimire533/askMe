function submitForm() {
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

    /*Is this editing request....*/
    if (typeof (title) != undefined && typeof (tags) != undefined && typeof (description) != undefined && typeof (editQnId) != undefined) {
        document.getElementById('QuestionTitle').value = title; // populate title
        tags = tags.split(',');
        let smtg = document.createElement('span');
        smtg.classList.add('tag');
        for (let i = 0; i < tags.length; ++i) {
            smtg.textContent = tags[i];
            addTag(smtg); // populate tags
        }

        smtg = document.createElement('input');
        smtg.setAttribute('name', 'isEdit');
        smtg.setAttribute('type', 'hidden'); // this should be hidden element
        smtg.value = parseInt(editQnId);
        document.getElementsByClassName('AskQuestion')[0].appendChild(smtg); // since it is hidden we can appedn anywhere inside form
    }
}

function toggleAvailableTags(source) {
    source.textContent = '';
    source.classList.toggle('fa-times');
    source.classList.toggle('fa-add');
    document.getElementsByClassName('availableTags')[0].classList.toggle('active');
}
function addTag(source) {
    let tag = source.cloneNode(true);
    tag.setAttribute('onclick', '');
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