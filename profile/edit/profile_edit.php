<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit your profile....</title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fs_css/all.css' />
    <script type='text/javascript' src='/global/global.js'></script>

    <link rel='stylesheet' type='text/css' href='/profile/edit/profile_edit.css' />
</head>

<body onload='Ready()'>
    <?php
    echo file_get_contents('../../global/navbar.php');
    ?>
    <div id='Main'>
        <div id='ProfileEditor'>
            <div class='editBlock' id='editProfileImage'>
                <div class='label'>You Profile Picture</div>
                <img class='value profile_img' alt='Sudip Ghimire' title='Your Profile pcture...' src='/user.png' />
                <i class='fas fa-pen edit_icon' title='Shange your Proifle picture...' onclick='editProfileImage(this)'></i>
                <div class='editor'>
                    <input type='file' name='Name' id='ProfleImage' />
                    <i class='fas fa-save save_icon' title='Save Your profile Picture...' onclick='editProfileImage(this)'></i>
                </div>
            </div>

            <div class='editBlock' id='editTags'>
                <div class='label'>You Tags</div>
                <div class='value'>
                    <span class='tag'>Programming</span>
                    <span class='tag'>Parasite</span>
                    <span class='tag'>Computers</span>
                </div>
                <i class='fas fa-pen edit_icon' title='Edit your faviourate Tags' onclick='editTags(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='Tags' id='Tags' value='Programming, Parasite, Computers' />
                    <i class='fas fa-save save_icon' title='Save my Tags..' onclick='editTags(this, true)'></i>
                </div>
            </div>

            <div class='editBlock' id='editName'>
                <div class='label'>You Name</div>
                <div class='value'>Sudip Ghimire</div>
                <i class='fas fa-pen edit_icon' title='Edit Your Name' onclick='editName(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='Name' id='Name' placeholder='Your Name' value='Sudip Ghimire' />
                    <i class='fas fa-save save_icon' title='Save My Name' onclick='editName(this,true)'></i>
                </div>
            </div>

            <div class='editBlock' id='editUserName'>
                <div class='label'>You UserName</div>
                <div class='value'>sudipghimire533</div>
                <i class='fas fa-pen edit_icon' title='Edit Your Username' onclick='editName(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='UserName' id='UserName' placeholder='New Username' value='sudipghimire533' />
                    <i class='fas fa-save save_icon' title='Save this Username' onclick='editUserName(this, true)'></i>
                </div>
            </div>

            <div class='editBlock' id='editIntro'>
                <div class='label'>You Intro</div>
                <div class='value'>Electrical Engineer at Facebook inc. Since 2007.</div>
                <i class='fas fa-pen edit_icon' title='Edit your Intro Text' onclick='editIntro(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='Intro' id='Intro' placeholder='You short Intro..' value='Electrical Engineer at Facebook Inc. since 2007.' />
                    <i class='fas fa-save save_icon' title='Save Your Intro Text..' onclick='editIntro(this, true);'></i>
                </div>
            </div>

        </div>
        <div class='notifyCenter'>
            <div class='notify' style='display: none;'></div>
        </div>
    </div>
</body>
<script>
    function Ready() {
        notification = document.getElementsByClassName('notify')[0];
    }

    function toggleEdit(source) {
        let block = source.parentElement;
        while (!block.classList.contains('editBlock')) {
            block = block.parentElement;
        }
        block.classList.toggle('active');
        lastEdit = block;

        source = block.getElementsByClassName('edit_icon')[0]; // even this is save icon switch it to edit icon;
        source.classList.toggle('fa-pen');
        source.classList.toggle('fa-times');
        source.setAttribute('title', 'Cancel');
    }

    function editName(source, save = false) {
        if (save === true) {
            let showName = document.getElementById('Name');
            let newName = showName.value.trim();
            sendData('ChangeName', newName, function() {
                document.getElementById('editName').getElementsByClassName('value')[0].textContent = newName;
                notify('Nice Name!!' + newName);
            });
        }
        toggleEdit(source);
    }

    function editIntro(source, save = false) {
        if (save === true) {
            let showIntro = document.getElementById('Intro');
            let newIntro = showIntro.value.trim();
            sendData('ChangeIntro', newIntro, function() {
                document.getElementById('editIntro').getElementsByClassName('value')[0].textContent = newIntro;
                notify('You intro has been updated!!!');
            });
        }
        toggleEdit(source);
    }

    function editUserName(source, save = false) {
        if (save === true) {
            let showUserName = document.getElementById('UserName');
            let newuserName = showUserName.value.trim();
            sendData('ChangeUserName', newuserName, function() {
                document.getElementById('editUserName').getElementsByClassName('value')[0].textContent = newuserName;
                notify('UserName changed...');
            });
        }
        toggleEdit(source);
    }

    function editTags(source, save = false) {
        if (save === true) {
            let showTags = document.getElementById('Tags');
            let newTags = new Array;
            showTags.value.trim().split(',').forEach(function(tag) {
                newTags.push(tag.trim());
            });
            sendData('ChangeUserTags', showTags.value.trim(), function() {
                let showTags = document.getElementById('editTags').getElementsByClassName('value')[0];
                showTags.innerHTML = '';
                newTags.forEach(function(tag) {
                    showTags.innerHTML += "<span class='tag'>" + tag + "</span>";
                });
                notify('Your tags had been updated..');
            })
        }
        toggleEdit(source);
    }

    function editProfileImage(source, save = false) {
        if (save == true) {}
        toggleEdit(source);
    }

    function sendData(param, data, sucess = function() {}) {
        let handler = new XMLHttpRequest;
        handler.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                sucess();
            }
        }
        handler.open('POST', '/server/quick_action.php');
        handler.send('Param=' + param + '&data=' + data);
    }
</script>

</html>