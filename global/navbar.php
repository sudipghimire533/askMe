<?php
require_once '../server/global.php';
if(!session_id()){
	session_start();
}
$profileImage = null;
$proiflePath = null;
if(getLoginStatus()){
}
?>
<div id='NavBar'>
	<div id='navLeft'>
		<a href='/home?ref=navbar' id='navHome'>
			<i class='fas fa-home'></i>
		</a>
		<span id='navAsk'>
			<a href='/ask/ask.php?ref=navbar' id='navAskBtn'>Ask</a>
		</span>
	</div>
	<form id='navMiddle' method='GET' action='/home'>
		<input type='text' placeholder='What You Want To Find Today?' title='What You Want to Find Today' id='navSearch' name='query' />
		<i class='fas fa-search navSearchIcon' onclick='topSearch()'></i>
	</form>
	<i class='fas fa-search navTriggerIcon' onclick='triggerSearch(this)'></i>
	<div id='navRight'>
		<span id='navHelp'>
			<i class='fas fa-question-circle'></i>
		</span>
		<a href='/profile/profile.php' id='navUser'>
			<img src='/user.png' alt='Profile Image' title='Visit My Profile' id='navMe' />
		</a>
		<span id='navMenu'>
			<i class='fas fa-caret-down' onclick='toggleDropDown()'></i>
			<div id='navDropDown'>
				<a href='/profile/profile.php'>My Profile</a><br />
				<a href="/home/home.php/(username)/#askedQuestion">My Questions</a><br />
				<a href='/home/profile/(username)/#pinnedQuestion='>My Pins</a><br />
				<hr />
				<a href='/ask/ask.php?ref=dropdown'>Ask Community</a><br />
				<hr />
				<a href="/login/logout.php?taketo=/login">Log out</a>
			</div>
		</span>
	</div>
</div>
<style>
	#NavBar {
		box-sizing: border-box;
		position: relative;
		display: block;
		padding: 10px 20px;
		background: var(--Black);
		display: flex;
		flex-flow: row wrap;
		justify-content: space-between;
		align-items: center;
		justify-items: center;
		align-content: center;
		z-index: 3;
		margin: 0;
	}

	#navHome {
		cursor: pointer;
		color: var(--LightDark);
		text-decoration: none;
		transition: color .5s;
		font-size: 16px;
	}

	#navHome:hover,
	#navHome:focus {
		color: var(--White);
	}

	#NavBar>* {
		margin: 0 10px;
		display: inline-flex;
		flex-flow: row nowrap;
		align-items: center;
		position: relative;
	}

	#navMiddle {
		flex-grow: 1;
		max-width: 600px;
	}

	.navSearchIcon,
	.navTriggerIcon {
		cursor: pointer;
		color: var(--LightDark);
		transition-duration: .5s;
		transition-property: color, scale;
	}

	.navSearchIcon {
		position: absolute;
		right: 10px;
	}

	.navTriggerIcon {
		display: none !important;
		margin: 0 !important;
	}

	.navSearchIcon:hover {
		color: var(--White);
		scale: 1.1;
	}

	#navSearch {
		background: transparent;
		border: none;
		outline: none;
		border: 1px solid var(--LightDark);
		padding: 10px 20px;
		padding-right: 40px;
		color: var(--White);
		letter-spacing: 1.3px;
		position: relative;
		width: 100%;
		border-radius: 5px;
		font-size: 14px;
		font-weight: 700;
	}

	#navRight>* {
		margin: 0 5px;
		color: var(--LightDark);
		transition: color .5s;
		cursor: pointer;
	}

	#navRight>*:hover,
	#navRight>*:focus {
		color: var(--White);
	}

	#navMe {
		opacity: 0.8;
		transition: opacity .5s;
		border-radius: 50%;
		width: 42px;
	}

	#navMe:hover,
	#navMe:focus {
		opacity: 1;
	}

	#navAskBtn {
		background: var(--Niagara);
		color: var(--White);
		padding: 3px 20px;
		cursor: pointer;
		text-decoration: none;
		border: 2px solid var(--Niagara);
		transition: background .5s;
		box-sizing: border-box;
		margin-left: 10px;
		border-radius: 5px;
	}

	#navAskBtn:hover,
	#navAskBtn:focus {
		background: transparent;
	}

	#navMenu {
		opacity: 1;
		position: relative;
	}

	#navMenu>i {
		opacity: 0.7;
	}

	#navDropDown {
		position: absolute;
		right: -10px;
		border-radius: 10px;
		background: var(--Shaft);
		color: var(--White);
		max-height: 0;
		transition: max-height .5s;
		margin-top: 5px;
		overflow: hidden;
		cursor: default;
		width: 200px;
		z-index: 2;
	}

	#navMenu.active #navDropDown {
		max-height: 200vh;
	}

	#navDropDown>a {
		cursor: var(--White);
		line-height: calc(var(--fontPrimary) * 1.5);
		font-size: var(--fontPrimary);
		padding: 0 10px;
		color: var(--White);
		text-decoration: none;
		cursor: pointer;
		opacity: 0.8;
		transition: opacity .5s;

	}

	#navDropDown>a:hover,
	#navDropDown>a:focus {
		opacity: 1;
	}

	@media only screen and (max-width: 720px) {
		#navMiddle {
			max-width: 0px;
			overflow: hidden;
		}

		#NavBar.searching #navMiddle {
			max-width: 100%;
			transition: max-width .3s;
		}

		.navTriggerIcon {
			display: block !important;
		}

		#NavBar.searching #navLeft,
		#NavBar.searching #navRight {
			display: none !important;
		}

		#NavBar .navTriggerIcon:after {
			content: 'search';
			padding-left: 4px;
			font-family: Rubik, sans-serif;
		}

		#NavBar.searching .navTriggerIcon:after {
			content: 'cancel';
		}

	}

	@media only screen and (max-width: 360px) {

		#navUser {
			display: none;
		}
	}

	@media only screen and (max-width: 300px) {
		#navAskBtn {
			display: none;
		}
	}
</style>
<script type='text/javascript'>
	function topSearch() {
		document.getElementById('navMiddle').submit();
	}

	function toggleDropDown() {
		document.getElementById('navMenu').classList.toggle('active');
	}

	function triggerSearch(source) {
		document.getElementById('NavBar').classList.toggle('searching');
		source.classList.toggle('fa-search');
		source.classList.toggle('fa-times');
	}
</script>