<nav class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-header">
		<button type="button" class="navbar-toggle" data-toggle="collapse" data-target="#menu-navbar-collapse">
			<span class="sr-only">Toggle navigation</span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
			<span class="icon-bar"></span>
		</button>
		<a class="navbar-brand" href="index.php?sid={$sid}"><img src="images/rpi.png" /></a>
	</div>
	<div id="menu-navbar-collapse" class="collapse navbar-collapse">
		<ul class="nav navbar-nav">
			<li{if $page == "home"} class="active"{/if}><a href="../systempanel/index.php?sid={$sid}">Home</a></li>
			<li{if $page == "settings"} class="active"{/if}><a href="../systempanel/settings.php?sid={$sid}">Settings</a></li>
			{if $admin}
			<li{if $page == "admin"} class="active"{/if}><a href="index.php?sid={$sid}">Admin</a></li>
			{/if}
			<li><a href="logout.php?sid={$sid}">Logout</a></li>
		</ul>
	</div>
</nav>