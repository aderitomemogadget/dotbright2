<?php
	if($GLOBALS['rodape'] && !isset($_GET['popup'])) put_content($rodape);
	echo google_analytics();
	if($tem_acessibilidade){ ?>
	<script>
		$(document).ready(function()
		{
			var fontSize = parseInt($("body").css("font-size"));
			fontSize = fontSize + <?= (($_SESSION['font']-1)*2)?> + "px";
			$("*").css({'font-size':fontSize});
		});
	</script>
<?php } ?>
<!-- <a href="javascript:CookieConsent.show()">Cookies Settings</a> -->
<? include $path.'../cookies/memogadgetcookiebot.php'; ?>
</body>
</html>