<div id='NavBar'>
	<div id='navLeft'>
		<a href='/home/home.php?ref=navbar' id='navHome'>
			<i class='fas fa-home'></i>
			<span>Posts</span>
		</a>
		<span id='navAsk'>
			<a href='../ask/ask.php' id='navAskBtn'>Ask</a>
		</span>
	</div>
	<form id='navMiddle'>
		<input type='text' placeholder='What You Want To Find Today?' title='What You Wan to Find Today' id='navSearch' name='query' method='GET' action='../home/home.php/' />
		<i class='fas fa-search' type='submit' name='submit' onclick='topSearch()'></i>
	</form>
	<div id='navRight'>
		<a href='../profile/profile.php?id=' id='navUser'>
			<img src='../user.png' alt='Profile Image' title='Visit My Profile' id='navMe' />
		</a>
		<span id='navHelp'>
			<i class='fas fa-question-circle'></i>
		</span>
		<span id='navMenu'>
			<i class='fas fa-caret-down'onclick='toggleDropDown()'></i>
			<div id='navDropDown'>
				<a href='/profile/profile.php'>My Profile</a><br />
				<a href="/home/home.php?questionby=me">My Questions</a><br />
				<a href='/home/home.php?bookmarksof=me'>My Bokmarks</a><br />
				<hr />
				<a href="#">Log out</a>
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

	#navMiddle i{
		position: absolute;
		right: 10px;
		cursor: pointer;
		color: var(--LightDark);
		transition-duration: .5s;
		transition-property: color, scale;
	}

	#navMiddle i:hover {
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
	#navMenu{
		opacity: 1;
		position: relative;
	}
	#navMenu > i{
		opacity: 0.7;
	}
	#navDropDown{
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
	#navMenu.active #navDropDown{
		max-height: 200vh;
	}
	#navDropDown > a{
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
	#navDropDown > a:hover,
	#navDropDown > a:focus{
		opacity: 1;
	}
	@media only screen and (max-width: 720px) {
		#navMiddle {
			display: none;
		}
	}
</style>
<script type='text/javascript'>
	function topSearch() {
		document.getElementById('navMiddle').submit();
	}
	function toggleDropDown(){
		document.getElementById('navMenu').classList.toggle('active');
	}
</script>