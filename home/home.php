<?php require_once('../server/get_feed.php');$feedFetcher=new Getfeed;$posts=json_encode(array());$stat="Custom filtered Question...";$loadQuestion="unknown";$param="null";if(isset($_GET['query'])){$stat="Result for search '".htmlspecialchars($_GET['query'])."'";$loadQuestion="SearchQuery";$param=urldecode($_GET['query']);}else if(isset($_GET['by'])){$loadQuestion="ActivityBy";$param=$_GET['by'];}else if(isset($_GET['questionby'])){$loadQuestion="QuestionBy";$param=$_GET['questionby'];}else if(isset($_GET['answerby'])){$loadQuestion="AnswerBy";$param=$_GET['answerby'];}else if(isset($_GET['taggedfor'])){$loadQuestion="TaggedFor";$param=urlencode($_GET['taggedfor']);$stat="Question tagged for '".$param."'";}else{$stat="Today's Selections...";$loadQuestion="Recent";} ?><!doctypehtml><html lang="en"><head><meta charset="UTF-8"><meta content="width=device-width,initial-scale=1"name="viewport"><title>Askme | Help me for Homework</title><link href="/global/global.css"rel="stylesheet"type="text/css"><link href="/home/home.css"rel="stylesheet"type="text/css"><link href="/thread/question_entity.css"rel="stylesheet"type="text/css"><link href="/global/fonts/all.css"rel="stylesheet"type="text/css"><script src="/global/global.js"type="text/javascript"></script><script src="/home/home.js"type="text/javascript"></script></head><body onload="Ready()"><?php require('../global/navbar.php'); ?><div id="Main"><div class="feed_title"style="color:var(--Niagara);margin:20px 0"><?php echo $stat; ?></div><div class="QuestionFeed"><div class="Question"><div class="questionTitle"><a href="#"class="titleText"></a> <span class="quickAction"><i class="bookmarkIcon fa-star"onclick='bookmark(this,!0,"QuestionId")'title="Pin this Question.."></i> <a href="#"class="fa-reply reply_icon"title="Give answer to this Question..."onclick='notify("Go hit it!!")'></a></span></div><div class="description"><span></span></div><div class="questionInfo"><div class="tagContainer"><a href="#"class="tag"></a></div><div class="askingUser"><a href="#"class="asker_name hv_border"title=""></a> <span>updated on </span><span class="updated_on"></span></div></div></div></div><div class="ShowTags"><h2 class="label">Be Smart. The Smart way</h2><div class="label"><a href="/alltags/"style="color:var(--Niagara)">See all Tag</a></div></div><button id="loadMoreBtn">load more</button><div class="notifyCenter"><div class="notify"style="display:none"></div></div></div></body><script>let response;var showTags;function Ready(){sample_question=document.getElementsByClassName("Question")[0],feed_container=sample_question.parentElement,showTags=document.getElementsByClassName("ShowTags")[0],sample_tag_element=sample_question.getElementsByClassName("tagContainer")[0].firstElementChild;let e="<?php echo $param; ?>";document.getElementById("loadMoreBtn").onclick=function(){loadMore("<?php echo $loadQuestion; ?>",5,e)},loadMore("<?php echo $loadQuestion; ?>",10,e),notification=document.getElementsByClassName("notify")[0]}</script></html>