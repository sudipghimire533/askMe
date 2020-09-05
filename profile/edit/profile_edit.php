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
                    <i class='fas fa-plus addtag_icon' onclick='toggleAddTag(this)'></i>
                    <span class='addedTags'>
                        <!-- Initially this should be synchronous to .value element as in above -->
                        <span class='tag'>Programming</span>
                        <span class='tag'>Parasite</span>
                        <span class='tag'>Computers</span>
                    </span>
                    <input type='text' name='Tags' id='Tags' value='' style='display: none;' />
                    <i class='fas fa-save save_icon' title='Save my Tags..' onclick='editTags(this, true)'></i>
                    <div class='availableTags'>
                        <input type='text' placeholder='Filter Tags..' id='searchAvailableTags' onkeyup='filterTag(this.value)' />
                        <br />
                    </div>
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
    var allTags = new String;
    let addedTagsShow;

    function Ready() {
        notification = document.getElementsByClassName('notify')[0];

        addedTagsShow = document.getElementsByClassName('addedTags')[0];
        let avts = document.getElementsByClassName('availableTags')[0];
        let stg = document.createElement('span');
        stg.classList.add('tag');
        stg.setAttribute('onclick', 'addTag(this)')
        let new_tag;
        allTags.split(',').forEach(function(tag) {
            new_tag = stg.cloneNode(true);
            new_tag.textContent = tag.trim();
            avts.appendChild(new_tag);
        });
    }

    function addTag(source) {
        let new_tag = source.cloneNode(true);
        new_tag.setAttribute('onclick', '');
        addedTagsShow.appendChild(new_tag);
        source.classList.add('added');
    }

    function toggleAddTag(source) {
        source.classList.toggle('fa-times');
        source.classList.toggle('fa-plus');
        document.getElementsByClassName('availableTags')[0].classList.toggle('active');
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
            /*Add all added tags textContent into the tag input filed by seperating them with (,) */
            let inputTags = document.getElementsByClassName('addedTags')[0].getElementsByClassName('tag');
            if (inputTags.length == 0) {
                notify('You should atleast provide one tag', 2);
                return; // return if no tags given
            }
            let tagInput = document.getElementById('Tags');
            tagInput.value = '';
            for (let i = 0; i < inputTags.length - 1; i++) {
                tagInput.value += inputTags[i].textContent + ',';
            }
            tagInput.value += inputTags[inputTags.length - 1].textContent; // no comma at the end

            function sucess() {
                let tagShow = document.getElementById('editTags').getElementsByClassName('value')[0];
                tagShow.innerHTML = '';
                for (let i = 0; i < inputTags.length; ++i) {
                    tagShow.appendChild(inputTags[i].cloneNode(true));
                }
            }
            sendData('UpdateTags', tagInput.value, sucess);
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
<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once('../../server/global.php');

$conn = get_connection();

$allTags = $conn->query("SELECT GROUP_CONCAT(Name) FROM  Tags;") or die('There was an error...');
$allTags = $allTags->fetch_array(MYSQLI_NUM)[0];


// Give this value to javascript
echo "<script type='text/javascript'>allTags=" . json_encode($allTags) . "</script>";

$conn->close();

?>
</body>

</html>