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
function bookmark(source, alsoSend = false) {
    if(alsoSend === true){
        // send request..
        notify("Question Has been Boorkarked. You can visit your profile to see all"+
        "your bookmarked questions", 0);
    }
    source.classList.add('active');
    source.setAttribute('title', 'You Bookmarked this question...');
    source.onclick = function(){notify('Hmm..Yo had already bookmarked this Question..', 1)};
}