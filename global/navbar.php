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
	<div id='navMiddle'>
		<input type='text' placeholder='What You Want To Find Today?' title='What You Wan to Find Today' id='navSearch' />
		<i class='fas fa-search'></i>	
	</div>
	<div id='navRight'>
		<span id='navUser'>
			<img src='../user.png' alt='Profile Image' title='Visit My Profile' id='navMe'/>
		</span>
		<span id='navHelp'>
			<i class='fas fa-question-circle'></i>
		</span>
		<span id='navMenu'>
			<i class='fas fa-caret-down'></i>
		</span>
	</div>
</div>
<style>
	#NavBar{
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
	#navHome{
		cursor: pointer;
		color: var(--LightDark);
		text-decoration: none;
		transition: color .5s;
		font-size: 16px;
	}
	#navHome:hover,
	#navHome:focus{
		color: var(--White);
	}
	#NavBar > *{
		margin: 0 10px;
		display: inline-flex;
		flex-flow: row nowrap;
		align-items: center;
		position: relative;
	}
	#navMiddle{
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
	#navMiddle i:hover{
		color: var(--White);
		scale: 1.1;
	}
	#navSearch{
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
	#navRight > *{
		margin: 0 5px;
		color: var(--LightDark);
		transition: color .5s;
		cursor: pointer;
	}
	#navRight > *:hover,
	#navRight > *:focus{
		color: var(--White);
	}
	#navMe {
		opacity: 0.8;
		transition: opacity .5s;
	}
	#navMe{
		border-radius: 50%;
		width: 32px;
	}
	#navMe:hover,
	#navMe:focus{
		opacity: 1;
	}
	#navAskBtn{
		background: var(--Niagara);
		color: var(--White);
		padding: 3px 20px;
		cursor: pointer;
		text-decoration: none;
		border: 2px solid var(--Niagara);
		transition: background .5s;
		box-sizing: border-box;
		margin-left: 10px;
	}
	#navAskBtn:hover,
	#navAskBtn:focus{
		background: transparent;
	}
	@media only screen and (max-width: 720px){
	  #navMiddle{
	  	display: none;
	  }
	}
</style>