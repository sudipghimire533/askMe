function notify(text, type = 0){
    let not = notification.cloneNode(true);
    not.textContent = text;
    not.style.display = 'block';
    if(type == 1){
    	not.classList.add('warning');
    } else if(type == 2){
    	not.classList.add('error');
    }
    document.getElementsByClassName('notifyCenter')[0].appendChild(not);
    setTimeout(function(){
        not.remove();
    },4*1000);
}