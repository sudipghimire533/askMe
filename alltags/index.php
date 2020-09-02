<?php
require_once('../server/get_feed.php');

$conn = get_connection();

$res = $conn->query("SELECT tg.Name AS name FROM Tags tg ORDER BY tg.Name ASC;") or die($conn->error);
$res = $res->fetch_all(MYSQLI_ASSOC);
$res = json_encode($res);

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All availabe tags</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='../thread/question_entity.css' type='text/css' rel='stylesheet' />
    <link rel='stylesheet' type='text/css' href='../global/fs_css/all.css' />
</head>

<body onload='Ready();'>
    <?php
    echo file_get_contents('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='searchContainer'>
            <div class='label'>
            </div>
            <div class='searchBox'>
                <input type='text' name='searchTag' placeholder='Fileter Tags...' id='searchTag' onkeyup='filter(this.value)' />
                <i class='fas fa-search icon'></i>
            </div>
        </div>
        <div class='tagShowCase'>
            <section class='tagCategory'>
                <h3 class='tagLetter'></h3>
                <a href='#' class='tag'></a>
            </section>
        </div>
    </div>
</body>
<style>
    .tagCategory:first-of-type {
        display: none;
    }

    .tag:first-of-type {
        display: none;
    }

    #Main {
        max-width: 950px;
        text-align: center;
    }

    .tagShowCase {
        text-align: left;
    }

    .tagCategory {
        margin: 20px;
        padding: 20px 2vw;
        border-radius: 10px;
        background: var(--Shaft);
    }

    .tagLetter {
        border-bottom: 2px solid var(--LightDark);
    }

    .tagLetter:before {
        content: '#';
    }

    .tagCategory .tag {
        font-size: var(--fontSmall);
    }

    .searchContainer {
        margin-bottom: 50px;
        margin-top: 30px;
    }

    #searchTag {
        padding: 10px 20px;
        width: 500px;
        height: 45px;
        box-sizing: border-box;
        max-width: 100%;
        position: relative;
        text-transform: lowercase;
        background: var(--LightDark);
        border: none;
        outline: none;
        color: var(--White);
        letter-spacing: 1.5px;
        font-size: var(--fontPrimary);
        font-family: Rubik, sans-serif;
        border-top-left-radius: 10px;
        border-bottom-left-radius: 10px;
    }

    .searchBox {
        position: relative;
        display: inline-block;
    }

    .searchBox .icon {
        position: absolute;
        display: inline-block;
        background: var(--LightDark);
        line-height: 45px;
        padding-left: 5px;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        padding-right: 10px;
        cursor: pointer;
    }

    .tag.active {
        background: red;
    }

    .tagCategory.active {
        background: var(--Yellow);
    }
</style>
<script>
    let allTags = <?php echo $res; ?>; // we will assume everything is in lowercase
    let letterMap = new Map;

    let sampleSection;

    function Ready() {
        sampleSection = document.getElementsByClassName('tagCategory')[0];
        let sampleTag = document.getElementsByClassName('tag')[0];
        let firstLetter = allTags[0].name[0];
        let currentSection = sampleSection.cloneNode(true);
        currentSection.getElementsByClassName('tagLetter')[0].textContent = firstLetter;
        allTags.forEach(function(tag, i) {
            if (firstLetter != tag.name[0]) {
                document.getElementsByClassName('tagShowCase')[0].appendChild(currentSection);
                letterMap.set(firstLetter, currentSection);
                currentSection = sampleSection.cloneNode(true);
                firstLetter = tag.name[0];
                currentSection.getElementsByClassName('tagLetter')[0].textContent = firstLetter;
            }
            let newTag = sampleTag.cloneNode(true);
            newTag.textContent = tag.name;
            newTag.setAttribute('href', '/home/home.php?taggedfor=' + tag.name);
            currentSection.appendChild(newTag);
        });
        document.getElementsByClassName('tagShowCase')[0].appendChild(currentSection);
        letterMap.set(firstLetter, currentSection);
    }

    let lastActiveSection = sampleSection;

    function filter(query) {
        let searchIn = letterMap.get(query.trim()[0].toLowerCase());
        if (searchIn == null) {
            return;
        }
        lastActiveSection.classList.remove('active');
        lastActiveSection = searchIn;

        searchIn.classList.add('active');
        let options = searchIn.getElementsByClassName('tag');
        console.log(options);
        for (let i = 0; i < options.length; i++) {
            if (String(options[i].textContent).indexOf(query) != -1) {
                options[i].classList.add('active');
            } else {
                options[i].classList.remove('active');
            }
        }
    }
</script>

</html>