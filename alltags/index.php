<?php require_once('../server/get_feed.php');$conn=get_connection();$res=$conn->query("SELECT tg.Name AS name FROM Tags tg ORDER BY tg.Name ASC;")or die($conn->error);$res=$res->fetch_all(MYSQLI_ASSOC);$res=json_encode($res);$conn->close(); ?><!doctypehtml><html lang="en"><head><meta charset="UTF-8"><meta content="width=device-width,initial-scale=1"name="viewport"><title>Askme | All availabe tags</title><link href="../global/global.css"rel="stylesheet"type="text/css"><link href="../thread/question_entity.css"rel="stylesheet"type="text/css"><link href="../global/fonts/all.css"rel="stylesheet"type="text/css"></head><body onload="Ready()"><?php include('../global/navbar.php'); ?><div id="Main"><div class="searchContainer"><div class="label"></div><div class="boxContainer"><input class="box_input"id="searchTag"name="searchTag"onkeyup="filter(this.value)"placeholder="Filter Tags..."> <i class="fa-search icon"></i></div></div><div class="tagShowCase"><section class="tagCategory"><h3 class="tagLetter"></h3><a class="tag"href="#"></a></section></div></div></body><style>.tag:first-of-type,.tagCategory.inactive,.tagCategory:first-of-type{display:none}#Main{max-width:950px;text-align:center}.tagShowCase{text-align:left}.tagCategory{margin:20px;padding:20px 2vw;border-radius:10px;background:var(--Shaft)}.tagLetter{border-bottom:2px solid var(--LightDark);text-transform:uppercase}.tagLetter:before{content:'# '}.tagCategory .tag{font-size:var(--fontSmall)}.searchContainer{margin-bottom:50px;margin-top:30px}.box_input{padding:10px 20px;width:500px;height:45px;box-sizing:border-box;max-width:100%;position:relative;text-transform:lowercase;background:var(--LightDark);border:none;outline:0;color:var(--White);letter-spacing:1.5px;font-size:var(--fontPrimary);font-family:Rubik,sans-serif;border-top-left-radius:10px;border-bottom-left-radius:10px}.boxContainer{position:relative;display:inline-block}.boxContainer .icon{position:absolute;display:inline-block;background:var(--LightDark);line-height:45px;height:45px;padding-left:5px;border-top-right-radius:10px;border-bottom-right-radius:10px;padding-right:10px;cursor:pointer}.tag.active{background:var(--Niagara)}</style><script>let allTags =<?php echo $res; ?>,letterMap = new Map,sampleSection,lastSection,lastMatch=new Array;function Ready(){sampleSection=document.getElementsByClassName("tagCategory")[0];let e=document.getElementsByClassName("tag")[0],t=allTags[0].name[0],a=sampleSection.cloneNode(!0);lastSection=a,a.getElementsByClassName("tagLetter")[0].textContent=t,allTags.forEach(function(s,n){t!=s.name[0]&&(document.getElementsByClassName("tagShowCase")[0].appendChild(a),letterMap.set(t,a),a=sampleSection.cloneNode(!0),t=s.name[0],a.getElementsByClassName("tagLetter")[0].textContent=t);let l=e.cloneNode(!0);l.textContent=s.name,l.setAttribute("href","/taggedfor/"+s.name),a.appendChild(l)}),document.getElementsByClassName("tagShowCase")[0].appendChild(a),letterMap.set(t,a)}function filter(e){if(e=String(e.trim()).toLowerCase(),lastMatch.forEach(function(e,t){e.classList.remove("active")}),lastSection.classList.remove("active"),0==e.length)return void letterMap.forEach(function(e){e.classList.remove("inactive")});letterMap.forEach(function(e){e.classList.add("inactive")}),lastMatch=new Array;let t=letterMap.get(e[0]);if(null==t)return;t.classList.remove("inactive"),t.classList.add("active"),lastSection=t;let a=t.getElementsByClassName("tag");for(let t=0;t<a.length;t++)-1!=a[t].textContent.indexOf(e)&&(a[t].classList.add("active"),a[t].classList.remove("inactive"),lastMatch.push(a[t]))}</script></html>