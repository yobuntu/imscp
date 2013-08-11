<!DOCTYPE html>
<html>
<head>
	<title>{TR_PAGE_TITLE}</title>
	<meta http-equiv='Content-Script-Type' content='text/javascript'/>
	<meta http-equiv='Content-Style-Type' content='text/css'/>
	<meta http-equiv='Content-Type' content='text/html; charset={THEME_CHARSET}'/>
	<meta name='copyright' content='i-MSCP'/>
	<meta name='owner' content='i-MSCP'/>
	<meta name='publisher' content='i-MSCP'/>
	<meta name='robots' content='nofollow, noindex'/>
	<meta name='title' content='{TR_PAGE_TITLE}'/>
	<link href="{THEME_COLOR_PATH}/css/imscp2.css" rel="stylesheet" type="text/css"/>
	<link href="{THEME_COLOR_PATH}/css/{THEME_COLOR}.css" rel="stylesheet" type="text/css"/>
	<link href="{THEME_COLOR_PATH}/css/jquery-ui-{THEME_COLOR}.css" rel="stylesheet" type="text/css"/>
</head>
<body>
<div id="wrapper">
	<div id="header">
		<!-- INCLUDE "../partials/navigation/main_menu.tpl" -->
		<!--<div id="usermenu">
			<a href="#" class="admin-user"><span class="mnu-indicator"></span>Administrator<span class="user-icon"></span></a>
			<div class="sub-menu">
				<ul>
					<li><a href="#"><span class="icon-block-black cog-b"></span>Account Settings</a></li>
					<li><a href="#"><span class="icon-block-black info-about-b"></span>Help?</a></li>
					<li><a href="#"><span class="icon-block-black box-incoming-b"></span>Inbox</a></li>
					<li><a href="#"><span class="icon-block-black locked-tw-b"></span>Logout</a></li>
				</ul>
				<div class="admin-thumb">
					<img src="images/user-thumb1.png" alt="user" width="50" height="50"><span><a href="#" class="p-edit">Edit&nbsp;Profile</a></span>
				</div>
			</div>
		</div>
		-->
	</div>
	<!--
	<div id="shortcur-bar" class="column">
		<ul>
			<li><a href="#"><span class="sc-icon dashboard"></span> Dashboard </a></li>
			<li><a href="#"><span class="sc-icon settings"></span> Settings </a></li>
			<li><a href="#"><span class="sc-icon satistics"></span> Statistics</a></li>
			<li><a href="#"><span class="sc-icon userlist"></span> User's List </a></li>
			<li><a href="#"><span class="sc-icon tasklist"></span> Task List </a></li>
			<li><a href="#"><span class="sc-icon content-c"></span> Content </a></li>
			<li><a href="#"><span class="sc-icon reports-c"></span> Reports </a></li>
			<li><a href="#"><span class="sc-icon medialibrary"></span> Media Library </a></li>
		</ul>
	</div>
	-->
	<div id="container">
		<div id=sidebar>
			The left menu
		</div>
		<div id="content">
			{LAYOUT_CONTENT}
		</div>
	</div>
</div>
<!--
<div id="footer-wrap">
	<div id="footer">

	</div>
</div>
-->
</body>
</html>
