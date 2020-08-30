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