var sample_question;
var feed_container;
var sample_tag_element;

var appended_questions = new Map;
appended_questions.set("-1", true); // a dummy set to send for fist time...
var feeded_tags = new Map;

function createQuestion(Question) {
    /*Only sow unique Question..*/
    if (appended_questions.has(Question.id)) {
        return;
    }

    let target = sample_question.cloneNode(true);

    target.getElementsByClassName('titleText')[0].textContent = Question.title;
    target.getElementsByClassName('titleText')[0].
        setAttribute('href', '/thread/' + Question.url);
    target.getElementsByClassName('reply_icon')[0].
        setAttribute('href', '/thread/thread.php?id=' + Question.id + '#writeAnswer');

    target.getElementsByClassName('description')[0].firstElementChild.textContent =
        Question.info + '...';

    for (let i = 0, tags = Question.tag.split(','); i < tags.length; i++) {
        let tag = sample_tag_element.cloneNode(true);
        tag.setAttribute('href', '/home/home.php?taggedfor=' + tags[i]);
        tag.textContent = tags[i];
        target.getElementsByClassName('tagContainer')[0].appendChild(tag);
        if (!feeded_tags.has(tags[i])) {
            // don't push directly becase single element can't be chld of 2! so clone it
            showTags.appendChild(tag.cloneNode(true));
            feeded_tags.set(tags[i], true);
        }
    }

    let user = target.getElementsByClassName('asker_name')[0];
    user.setAttribute('href', '../profile/profile.php?id=' + Question.authorId);
    user.setAttribute('title', 'Visit profile of ' + Question.authorName);
    user.textContent = Question.authorName;
    target.getElementsByClassName('updated_on')[0].textContent =
        Question.modifiedOn.split(' ')[0]; // only date not time
    let bookmarkIcon = target.getElementsByClassName('bookmarkIcon')[0];
    if (Question.isBookmarked !== null) { // is question bookmarked?
        bookmarkIcon.classList.add('active');
        bookmarkIcon.onclick = function () { bookmark(bookmarkIcon, false); };
    } else {
        bookmarkIcon.setAttribute("onclick", "bookmark(this, true," + Question.id + ")")
    }
    feed_container.appendChild(target);
    appended_questions.set(Question.id, true);
}


function loadMore(loadQuestion, count = 5, param = 'null') {
    let notIn = new Array;
    appended_questions.forEach(function (val, key) {
        notIn.push(key);
    });
    let handler = new XMLHttpRequest;
    handler.onerror = function () {
        alert('An error occured');
        console.log(this);
    }
    handler.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            console.log(this.responseText);
            if (this.responseText == '1' || this.responseText == 1) { // end of feed
                notify('You reached  the end', 1);
                loadMore = function () { notify('You ate all of our feed..', 1); }; // at the end destroy self
                return;
            } else if (this.responseText == '2' || this.responseText == 2) {
                // incomplete data sent.
                // caiuse me be user had modified this script manually after loading...
                notify('Unknown error occured...', 2);
                return;
            } else if (this.responseText == '3' || this.responseText == 3) {
                // uknown error
                // may be in database syntax or enything else
                notify('Unknown error occured...', 2);
                return;
            }
            let nextposts = JSON.parse(this.responseText);
            nextposts.forEach(function (obj) {
                createQuestion(obj);
            });
        }
    };
    handler.open("POST", "/server/get_feed.php", true);
    handler.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    handler.send("NotIn=" + JSON.stringify(notIn) + "&Count=" + count + "&LoadQuestion=" + loadQuestion + "&Param=" + param);
    console.log("Post Paramater are: NotIn=" + JSON.stringify(notIn) +
        "&Count=" + count + "&LoadQuestion=" + loadQuestion + "&Param=" + param);
}