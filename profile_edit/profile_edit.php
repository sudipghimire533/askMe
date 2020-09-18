<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if(!session_id()){
    session_start();
}

require_once('../server/global.php');

if(!getLoginStatus()){
    echo "<a href='/login'>sign in to get your profile page...</a>";
    exit;
}

$thisUserId = $_SESSION['userId'];

$conn = get_connection(); // close this connection later.(at end of this file)

$res = $conn->query("SELECT
            user.FirstName as firstName,
            user.LastName as lastName,
            user.UserName as userName,
            user.Intro as intro,
            user.PictureReal as picture,
            GROUP_CONCAT(tg.Name) as tags
            FROM User user
            LEFT JOIN
            UserTag ut ON ut.User=user.Id
            LEFT JOIN
            Tags tg ON tg.Id =ut.Tag
            WHERE user.Id=$thisUserId
        ;") or die($conn->error . " in line " . __LINE__);
$res = $res->fetch_all(MYSQLI_ASSOC)[0];

$FirstName = $res['firstName'];
$LastName = $res['lastName'];
$UserName = $res['userName'];
$Intro = $res['intro'];
$Tags = explode(',', $res['tags']);
$picture = $res['picture'];

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit your profile....</title>

    <link href='/global/global.css' type="text/css" rel="stylesheet" />
    <link rel='stylesheet' type='text/css' href='/global/fonts/all.css' />
    <script type='text/javascript' src='/global/global.js'></script>

    <link rel='stylesheet' type='text/css' href='/profile_edit/profile_edit.css' />
</head>

<body onload='Ready()'>
    <?php
    include('../global/navbar.php');
    ?>
    <div id='Main'>
        <div id='ProfileEditor'>
            <div class='editBlock' id='editProfileImage'>
                <div class='label'>You Profile Picture</div>
                <img class='value profile_img' alt='Sudip Ghimire' title='Your Profile pcture...' src='<?php echo $picture ?>' />
                <i class='fas fa-pen edit_icon' title='Change your Proifle picture...' onclick='toggleEdit(this)'></i>
                <div class='editor'>
                    <p>Your profile picture will be same from your facebook</p>
                </div>
            </div>

            <div class='editBlock' id='editTags'>
                <div class='label'>You Tags</div>
                <div class='value'>
                    <?php
                    foreach ($Tags as $tag) {
                        echo "<span class='tag'>$tag</span>";
                    }
                    ?>
                </div>
                <i class='fas fa-pen edit_icon' title='Edit your faviourate Tags' onclick='editTags(this, false)'></i>
                <div class='editor'>
                    <i class='fas fa-plus addtag_icon' onclick='toggleAddTag(this)'></i>
                    <span class='addedTags'>
                        <!-- Initially this should be synchronous to .value element as in above -->
                        <?php
                        foreach ($Tags as $tag) {
                            echo "<span class='tag' onclick='this.remove()'>$tag</span>";
                        }
                        ?>
                    </span>
                    <input type='text' name='Tags' id='Tags' value='' style='display: none;' />
                    <i class='fas fa-save save_icon' title='Save my Tags..' onclick='editTags(this, true)'> save</i>
                    <div class='availableTags'>
                        <input type='text' placeholder='Filter Tags..' id='searchAvailableTags' onkeyup='filterTag(this.value)' />
                        <br />
                    </div>
                </div>
            </div>

            <div class='editBlock' id='editName'>
                <div class='label'>You Name</div>
                <div class='value'><?php echo $FirstName . " " . $LastName; ?></div>
                <i class='fas fa-pen edit_icon' title='Edit Your Name' onclick='editName(this, false)'></i>
                <div class='editor'>
                    <input type='text' id='Name' placeholder='Your Name' value='<?php echo $FirstName . " " . $LastName; ?>' />
                    <i class='fas fa-save save_icon' title='Save My Name' onclick='editName(this,true)'> save</i>
                </div>
            </div>

            <div class='editBlock' id='editUserName'>
                <div class='label'>You UserName</div>
                <div class='value'><?php echo $UserName; ?></div>
                <i class='fas fa-pen edit_icon' title='Edit Your Username' onclick='editName(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='UserName' id='UserName' placeholder='New Username' value='<?php echo $UserName; ?>' />
                    <i class='fas fa-save save_icon' title='Save this Username' onclick='editUserName(this, true)'> save</i>
                </div>
            </div>

            <div class='editBlock' id='editIntro'>
                <div class='label'>You Intro</div>
                <div class='value'><?php echo $Intro ?></div>
                <i class='fas fa-pen edit_icon' title='Edit your Intro Text' onclick='editIntro(this, false)'></i>
                <div class='editor'>
                    <input type='text' name='Intro' id='Intro' placeholder='You short Intro..' value='<?php echo $Intro; ?>' />
                    <i class='fas fa-save save_icon' title='Save Your Intro Text..' onclick='editIntro(this, true);'> save</i>
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
    let addedTagsShow, availableTagShow;

    function Ready() {
        notification = document.getElementsByClassName('notify')[0];

        addedTagsShow = document.getElementsByClassName('addedTags')[0];
        availableTagShow = document.getElementsByClassName('availableTags')[0];
        let stg = document.createElement('span');
        stg.classList.add('tag');
        stg.setAttribute('onclick', 'addTag(this)')
        let new_tag;
        allTags.split(',').forEach(function(tag) {
            new_tag = stg.cloneNode(true);
            new_tag.textContent = tag.trim();
            availableTagShow.appendChild(new_tag);
        });
    }

    function addTag(source) {
        let new_tag = source.cloneNode(true);
        new_tag.setAttribute('onclick', 'removeTag(this)');
        addedTagsShow.appendChild(new_tag);
        source.classList.add('added');
    }
    let ntg;

    function removeTag(source) {
        ntg = source.cloneNode(true);
        ntg.setAttribute('onclick', 'addTag(this)');
        ntg.classList.remove('added');
        availableTagShow.appendChild(ntg);
        source.remove();
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
            newName = newName.replace("  ", " ");
            showName.value = newName;
            if(newName.indexOf(' ') != newName.lastIndexOf(' ')){
                notify('Only one space is allowed...', 1);
                return;
            }
            if(newName.length < 4){
                notify("Too short name...", 1);
                return;
            }
            sendData('UpdateName', newName, function() {
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
            sendData('UpdateIntro', decodeURI(newIntro), function() {
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

            if(newuserName !== newuserName.replace(/[^A-Za-z0-9_]/), ''){
                notify("only alphanumeric character is allowed in username", 2, 5);
                return;
            }

            sendData('UpdateUserName', newuserName, function() {
                document.getElementById('editUserName').getElementsByClassName('value')[0].textContent = newuserName;
                notify('UserName changed...');
            }, function(msg) {
                notify('An error occured. Try another username...', 2);
            });
        }
        toggleEdit(source);
    }

    function editTags(source, save = false) {
        if (save === true) {
            let tagMap = new Map;
            /*Add all added tags textContent into the tag input filed by seperating them with (,) */
            let inputTagsAll = document.getElementsByClassName('addedTags')[0].getElementsByClassName('tag');
            let inputTags = new Array;

            let tagInput = document.getElementById('Tags');
            tagInput.value = '';

            for (let i = 0; i < inputTagsAll.length; ++i) {
                if (!tagMap.has(inputTagsAll[i].textContent)) { // only unique tags
                    tagMap.set(inputTagsAll[i].textContent, true);
                    tagInput.value += inputTagsAll[i].textContent + ','; // keep filling the input text
                    inputTags.push(inputTagsAll[i]);
                }
            }
            tagInput.value = tagInput.value.substr(0, tagInput.value.length - 1); // no comma at the end..
            if (inputTags.length == 0) {
                notify('You should at least provide one tag', 2);
                return;
            }

            function sucess() {
                let tagShow = document.getElementById('editTags').getElementsByClassName('value')[0];
                tagShow.innerHTML = '';
                for (let i = 0; i < inputTags.length; ++i) {
                    tagShow.appendChild(inputTags[i].cloneNode(true));
                }
                notify('Your Tags has been updated sucessfully...');
            }
            sendData('UpdateTags', tagInput.value, sucess);
        }
        toggleEdit(source);
    }

    function sendData(param, data, sucess = function() {}, error = null) {
        let handler = new XMLHttpRequest;
        handler.onerror = function() {
            notify('Error while sending Request...', 2);
        };
        handler.onreadystatechange = function() {
            if (this.readyState == 4 && this.status == 200) {
                if (this.responseText == 0 || this.responseText == '0') {
                    sucess();
                } else {
                    if (error == null) {
                        notify('Server returned an error...', 2);
                        console.log(this.responseText);
                    } else {
                        error(this.textContent);
                    }
                }
            }
        }
        handler.open('POST', '/server/quick_action.php', true);
        handler.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        handler.send('param=' + param + '&data=' + encodeURIComponent(data));
        console.log('param=' + param + '&data=' + (data));
    }
</script>
<?php

$allTags = $conn->query("SELECT GROUP_CONCAT(Name) FROM  Tags;") or die('There was an error...');
$allTags = $allTags->fetch_array(MYSQLI_NUM)[0];


// Give this value to javascript
echo "<script type='text/javascript'>allTags=" . json_encode($allTags) . "</script>";

$conn->close();

?>
</body>

</html>