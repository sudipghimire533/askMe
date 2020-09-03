var notification;
function notify(text, type = 0, keep_for = 3) {
    let not = notification.cloneNode(true);
    not.textContent = text;
    not.style.display = 'block';
    if (type == 1) {
        not.classList.add('warning');
    } else if (type == 2) {
        not.classList.add('error');
    }
    not.style.animationDuration = keep_for + 's';
    document.getElementsByClassName('notifyCenter')[0].appendChild(not);
    setTimeout(function () {
        not.remove();
    }, (keep_for + 1) * 1000);
}
function quickAction(action, id, sucessCallBack) {
    if (action != 'clapAnswer' && action != 'clapQuestion' && action != 'bookmark') {
        notify('No such Action...');
        return;
    }
    let req = new XMLHttpRequest;
    req.timeout = 5 * 1000;
    req.onerror = function () {
        console.log(this);
        notify('We were unable to send that Request to the server', 2);
        return -1;
    }
    req.ontimeout = req.onerror;
    req.onreadystatechange = function () {
        if (this.readyState == 4 && this.status == 200) {
            if (this.responseText == 0 || this.responseText == '0') {
                sucessCallBack();
                return 0;
            } else {
                this.onerror();
                return 1;
            }
        }
    };

    req.open('GET', '/server/quick_action.php?' + action + '=true&target=' + id);
    req.send();
}
function bookmarkLastStep(source) {
    source.onclick = function () {
        notify('You had already bookmarked that question. Visit your profile for mpre aftion...', 1);
    };
    source.classList.add('active');
    source.classList.remove('inactive');
}

function bookmark(source, alsoSend = false, id) {
    if (alsoSend === true) {
        quickAction("bookmark", id, function () {
            notify("Hurrayh!! That question has been bookmarked....");
            bookmarkLastStep(source);
        });
    } else {
        bookmarkLastStep(source);
    }
}