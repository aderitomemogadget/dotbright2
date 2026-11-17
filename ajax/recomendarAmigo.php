<?php
	if(!isset($_GET['idprod']) || isset($_GET['idprod']) && ($_GET['idprod']) <= 0) die();
	$path=dirname( __FILE__ ).'/../core/';
	include $path.'core_files.php';

?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<META NAME="robots" CONTENT="noindex">
	<link rel="stylesheet" href="../external_api/bootstrap/css/bootstrap.min.css?dp=<?php echo dumpcache ?>" type="text/css" media="all"/>
	<link rel="stylesheet" href="../layout/<?php echo PROJETO; ?>/css/template.css?dp=<?=dumpcache?>" type="text/css" media="all"/>
	<link rel="stylesheet" href="../external_api/apprise/apprise-v2.css?dp=<?=dumpcache?>" type="text/css" media="all"/>
	<script src="../external_api/jquery-1.7.1.min.js?dp=<?=dumpcache?>"></script>
	<script src="../external_api/apprise/apprise-v2.js?dp=<?=dumpcache?>"></script>
	<script src="../external_api/reveal/jquery.fancybox.js?dp=<?=dumpcache?>"></script>
	<script src="../external_api/tooltipster/jquery.tooltipster.js?dp=<?=dumpcache?>"></script>
	<script src="../external_api/jquery.selectbox/jquery.selectbox-0.2.js?dp=<?=dumpcache?>"></script>
	<link href="https://fonts.googleapis.com/css2?family=Roboto:wght@100;300;400;500;700;900&display=swap" rel="stylesheet">
	<style>body{ overflow:hidden; }</style>
</head>
<body>
<?php
if (isset($_POST['Submeter']) && isset($_POST['idprod']) && intval($_POST['idprod']) > 0)
{
	$idprod=intval(clean_var($_POST['idprod']));
	$m_nome=clean_var($_POST['notificacao_nome']);
	$m_email=clean_var($_POST['notificacao_email']);
	// $m_tel=clean_var($_POST['notificacao_tel']);
	$msg=clean_var($_POST['notificacao_obs']);
	// $m_nif=clean_var($_POST['notificacao_nif']);

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT Ref FROM products WHERE ID='.$idprod;

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$msg=str_replace(' '.$idprod.' ',' '.$rs->db_data['Ref'].' ',$msg);
		$link = $site_http_url.seo('pages.php?page_id='.$GLOBALS['products_page_id'], $idprod, 2);
		$link = "<a href='".$link."' alt='".$msg."' target='_blank'>".$msg."</a>";
	}

	// $msg="Nome: ".$m_nome."<br>Email: ".$m_email."<br>Link: <a href='".$link."' alt='".$msg."' target='_blank'>".$msg."</a>";
	// $mailsubject = SITE_TITLE." - Recomendar a Amigo";





	$body = array
    (
        '[nome]' => $m_nome,
        '[email]' => $m_email,
        '[link]' => $link
    );

    $message = template_email($GLOBALS['template_recomendar_amigo'], $body);
    send_email($m_email, $message['subject'], $message['body']);
	// send_email($m_email,$message['subject'],$msg);


	echo '<script>parent.jQuery.fancybox.close();</script>';
}
else
{
	?>
	<script>
		function checkEmail(field) {
			email=field.value;
			AtPos=email.indexOf("@");
			StopPos=email.lastIndexOf(".");
			Message="";
			if (email == "" || AtPos == -1 || StopPos == -1 || (StopPos < AtPos) || (StopPos - AtPos == 1)) {
				alert("<?= getLinguas('key_email_correto', $GLOBALS['site_lang'])?>");
				field.value="";
				return false;
			}
			return true;
		}
	</script>
	<?php

	$info2=get_produtc_info(intval($_GET['idprod']));



	// $info=getDadosCliente($dados['cli_id']);
	// $email=$info['Email'];


	global $site_lang;

	?>
	<div class="">
		<form method="POST" name="comentario" id="comentario" onSubmit="return checkEmail(notificacao_email);">
		<div style="width:100%;font-weight:700;font-size:20px;border-bottom:solid 1px #ccc;padding-bottom:20px">
			Recomendar a um Amigo
		</div>
		<div style="width: 50%; float: left;">
			<img src="<?php echo $info2['prd_imgm']; ?>" style="width: 80%;">
			<div class="form-group">
					<textarea class="form-control" name="notificacao_obs" id="notificacao_obs" ><?php echo $info2['prod_nome'];?></textarea>
				</div>
		</div>
		<div style="width: 50%; float: left;">
			<input type="hidden" name="idprod" id="idprod" value="<?=intval($_GET['idprod']); ?>">
			<div><?= getLinguas('key_destinatario', $GLOBALS['site_lang'])?></div>
			<br>
			<div class="form-group">
				<input placeholder="<?= getLinguas('key_nome_destinatario', $GLOBALS['site_lang'])?>" class="form-control" type="text" name="notificacao_nome" id="notificacao_nome" value="">
			</div>
			<? /*<div class="form-group">
				<input placeholder="<?= getLinguas('key_nif', $GLOBALS['site_lang'])?>" class="form-control" type="text" name="notificacao_nif" id="notificacao_nif" value="">
			</div> */?>
			<div class="form-group">
				<input placeholder="<?= getLinguas('key_email_destinatario', $GLOBALS['site_lang'])?>" class="form-control" type="text" name="notificacao_email" id="notificacao_email" value="">
			</div>
			<? /*<div class="form-group">
				<input placeholder="<?= getLinguas('key_telefone', $GLOBALS['site_lang'])?>" class="form-control" type="text" name="notificacao_tel" id="notificacao_tel" value="">
			</div> */?>

			<div class="form-group">
				<input class="button3" type="submit" id="Submeter" name="Submeter" value="Enviar">
			</div>
		</div>
		</form>
	</div>
	<?

}
?>
</body>
</html>