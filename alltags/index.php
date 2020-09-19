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
    <title>Askme | All availabe tags</title>

    <link href='../global/global.css' type="text/css" rel="stylesheet" />
    <link href='../thread/question_entity.css' type='text/css' rel='stylesheet' />
    <link rel='stylesheet' type='text/css' href='../global/fonts/all.css' />
</head>

<body onload='Ready();'>
    <?php
    include('../global/navbar.php');
    ?>
    <div id='Main'>
        <div class='searchContainer'>
            <div class='label'>
            </div>
            <div class='boxContainer'>
                <input type='text' name='searchTag' placeholder='Filter Tags...' id='searchTag' onkeyup='filter(this.value)' class='box_input' />
                <i class='fa-search icon'></i>
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
    .tag:first-of-type,
    .tagCategory.inactive,
    .tagCategory:first-of-type {
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
        text-transform: uppercase;
    }

    .tagLetter:before {
        content: '# ';
    }

    .tagCategory .tag {
        font-size: var(--fontSmall);
    }

    .searchContainer {
        margin-bottom: 50px;
        margin-top: 30px;
    }

    .box_input {
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

    .boxContainer {
        position: relative;
        display: inline-block;
    }

    .boxContainer .icon {
        position: absolute;
        display: inline-block;
        background: var(--LightDark);
        line-height: 45px;
        height: 45px;
        padding-left: 5px;
        border-top-right-radius: 10px;
        border-bottom-right-radius: 10px;
        padding-right: 10px;
        cursor: pointer;
    }

    .tag.active {
        background: var(--Niagara);
    }
</style>
<script>
    let allTags = <?php echo $res; ?>; // we will assume everything is in lowercase
    let letterMap = new Map;

    let sampleSection;

    let lastMatch = new Array;
    let lastSection;

    function Ready() {
        sampleSection = document.getElementsByClassName('tagCategory')[0];
        let sampleTag = document.getElementsByClassName('tag')[0];
        let firstLetter = allTags[0].name[0];
        let currentSection = sampleSection.cloneNode(true);
        lastSection = currentSection;
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
            newTag.setAttribute('href', '/taggedfor/' + tag.name);
            currentSection.appendChild(newTag);
        });
        document.getElementsByClassName('tagShowCase')[0].appendChild(currentSection);
        letterMap.set(firstLetter, currentSection);
    }

    function filter(query) {
        query = String(query.trim()).toLowerCase();
        lastMatch.forEach(function(el, index) {
            el.classList.remove('active');
        });
        lastSection.classList.remove('active');
        if (query.length != 0) {
            letterMap.forEach(function(sec) {
                sec.classList.add('inactive');
            });
        } else {
            letterMap.forEach(function(sec) {
                sec.classList.remove('inactive');
            });
            return;
        }
        lastMatch = new Array;
        let section = letterMap.get(query[0]);
        if (section == null) return; // there no tag that start from given char

        section.classList.remove('inactive');
        section.classList.add('active');
        lastSection = section;

        let options = section.getElementsByClassName('tag');
        for (let i = 0; i < options.length; i++) {
            if (options[i].textContent.indexOf(query) != -1) {
                options[i].classList.add('active');
                options[i].classList.remove('inactive');
                lastMatch.push(options[i]);
            } else {}
        }
    }
</script>

</html>