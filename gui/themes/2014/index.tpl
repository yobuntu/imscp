<div id="login-wrapper">
	<div id="login-header" class="top-round">
		<div class="login-left">
			<!--<span class="icon-wrap-lb-less"><span class="icon-block-black key-tw-b"></span>Login</span>-->
			<a href="#" title="Admin Panel"><img src="/themes/2014/images/imscp_logo.png" width="285" height="97" alt="Admin Panel"></a>
		</div>
		<div class="login-right">
			<!--<a href="#" title="Admin Panel"><img src="/themes/2014/images/imscp_logo.png" width="142" height="45" alt="Admin Panel"></a>-->
			<!--<span class="icon-wrap-lb-less"><span class="icon-block-black key-tw-b"></span>Login</span>-->
		</div>
	</div>
	<div class="login-box bottom-round">
		<form action="index.php" method="post">
			<ul>
				<li><label>{TR_USERNAME}</label><input name="uname" type="text" value="" class="login-text-box"></li>
				<li><label>{TR_PASSWORD}</label><input name="upass" type="password" value="" class="login-text-box usr"></li>
				<li>
					<label>&nbsp;</label>
					<input name="login" type="Submit" class="submit-button-login" value="{TR_LOGIN}">
					<!-- BDP: lost_password_support -->
					<input name="lostpwd" type="Submit" class="submit-button-login pass" value="{TR_LOSTPW}">
					<!-- EDP: lost_password_support -->
				</li>
				<!--<li><label>&nbsp;</label><span class="rem-check"><input name="" type="checkbox" value=""></span><span class="rem-text">Remember Me</span></li>-->
			</ul>
		</form>
	</div>
</div>
<div id="footer-wrap">
	<div id="footer">
		<div class="login-footer-container">
			<span><a style="color: whitesmoke" href="{productLink}" target="blank">{productCopyright}</a></span>
		</div>
	</div>
</div>
