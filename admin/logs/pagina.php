<?php
	require_once "settings.inc.php";
	$D = getdate();

	if(!$caminho_bt_editor){$caminho_bt_editor1='../../../editor/';$caminho_bt_editor2='../editor/';}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	require_once "../layout/header.inc.php";
	require_once "../layout/body.inc.php";
?>

<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
<script>var tabsel = '<?php echo (isset($_GET['tab']) && $_GET['tab'] > 0) ? $_GET['tab'] : '1';?>';</script>
<script>var SITE_HTTP_URL = '<?php echo $GLOBALS['site_http_url']?>';var SITE_SHELL_URL = '<?php echo $GLOBALS['site_shell_url']?>';</script>
<script language="JavaScript" src="sa.js"></script>
<div class="center" style="position:relative;" id="wp">


<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
<tr>
<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">LOGS</h4>
        </div>
    </div>
<table class="new_quadros" style="background:#FFF" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
<tr>
<td valign="top">
<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
<tr>
<td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de LOGS</a></td>
</tr>
</table>
</tr>
<tr>
<td align="left" valign="top" class="preto" style="background:#FFF">
<div class="wraper" id="wraper">

<div class="bigtitletab" onclick="infinittab(1);return false;" id="menutab1" style="float:none"></div>
<div class="clearboth"></div>
<div class="tabtop-open3" id="tab1"><?php require_once 'tab_dados.php';?>

</div>

</td>
</tr>
</table>
<img src="../images/bsh.png" width="100%" height="30px">
</td>
<td width="10px">&nbsp;</td>

</tr>
</table>

</div>
<?php require_once "../layout/bottom.inc.php";?>