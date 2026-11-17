<?php
	if(!isset($_GET['idprod']) || isset($_GET['idprod']) && ($_GET['idprod']) <= 0) die();
	$path=dirname( __FILE__ ).'/../core/';
	include $path.'core_files.php';
?><html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
	<META NAME="robots" CONTENT="noindex">
	<link rel="stylesheet" href="../external_api/bootstrap/css/bootstrap.min.css?dp=<?php echo dumpcache ?>" type="text/css" media="all"/>
	<link rel="stylesheet" href="../layout/<?php echo PROJETO; ?>/css/template.css?dp=<?= dumpcache ?>" type="text/css" media="all"/>
	<link rel="stylesheet" href="../external_api/apprise/apprise-v2.css?dp=<?= dumpcache ?>" type="text/css" media="all"/>
	<script src="../external_api/jquery-1.7.1.min.js?dp=<?= dumpcache ?>"></script>
	<script src="../external_api/apprise/apprise-v2.js?dp=<?= dumpcache ?>"></script>
	<script src="../external_api/reveal/jquery.fancybox.js?dp=<?= dumpcache ?>"></script>
	<script src="../external_api/tooltipster/jquery.tooltipster.js?dp=<?= dumpcache ?>"></script>
	<script src="../external_api/jquery.selectbox/jquery.selectbox-0.2.js?dp=<?= dumpcache ?>"></script>
	<style>body{ overflow:hidden; }</style>
</head>
<body>
<?php
if (isset($_POST['Submeter']) && isset($_POST['idprod']) && intval($_POST['idprod']) > 0){
	$email=clean_var($_POST['notificacao_email']);
	$idprod=intval(clean_var($_POST['idprod']));

	global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
    $sql='SELECT ID_Prod FROM clientes_notificacoes_imgs  WHERE Email="'.$email.'" AND ID_Prod="'.$idprod.'"';
    $rs->db_open($db_link,$DB_name,$sql);
    if($rs->EOF()){
		inserirProdutosNotificacaoImgs($email, $idprod);
		echo '<script>parent.jQuery.fancybox.close();</script>';
	}
	else{
		$info2=get_produtc_info(intval($_GET['idprod']));
		?>
		<div class="row">
			<div class="col-xs-12">
				<div class="wp-side-image2">
					<div class="image" style="max-height:65px;overflow:hidden;max-width: 65px;margin: 0 auto 20px;"><img src="<?php echo $info2['prd_imgm'];?>" alt="<?php echo $info2['prod_nome'];?>"></div>
					<div class="clearfix"></div>
				</div>
			</div>
			<div class="col-xs-12">
				<div class="subtitulo_d" style="margin-bottom:20px">JA REALIZOU SOLICITAÇÃO DE FOTOS DO EQUIPAMENTO!</div>
			</div>
			<div class="col-xs-12">
				<a title="Close" class="fancybox-item fancybox-close button3" href="javascript:;">FECHAR</a>
			</div>
		</div>
		<?
	}	
	
}
else{
	?>
	<script>
		function checkEmail(field) {
			email=field.value;
			AtPos=email.indexOf("@");
			StopPos=email.lastIndexOf(".");
			Message="";
			if (email == "" || AtPos == -1 || StopPos == -1 || (StopPos < AtPos) || (StopPos - AtPos == 1)) {
				alert("Por favor insira um email correcto!");
				field.value="";
				return false;
			}
		}
	</script>
	<?php
	$info2=get_produtc_info(intval($_GET['idprod']));

	if ($dados=cliente_logado()){
		global $site_lang;
		$info=getDadosCliente($dados['cli_id']);
		$email=$info['Email'];	
		?>
		<form method="POST" name="comentario" id="comentario" onSubmit="return checkEmail(notificacao_email);">
			<input type="hidden" name="idprod" id="idprod" value="<?= intval($_GET['idprod']); ?>">

			<div class="row">
				<div class="col-xs-3">
					<div class="wp-side-image2">
						<div class="image" style="max-height:65px;overflow:hidden"><img src="<?php echo $info2['prd_imgm'];?>" alt="<?php echo $info2['prod_nome'];?>"></div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="col-xs-9">
					<div class="subtitulo_d" style="margin-bottom:5px">SOLICITAÇÃO DE FOTOS DO EQUIPAMENTO!</div><p style="color:#989899;font-size:12px;line-height:12px">Pretendo receber fotos produto real.</p>
				</div>
			</div>
			<br>
			<div class="form-group">
				<input placeholder="E-mail" class="form-control" type="text" name="notificacao_email" id="notificacao_email" value="<?= $email ?>">
			</div>
			<div class="form-group">
				<input class="button3" type="submit" id="Submeter" name="Submeter" value="Enviar">
			</div>
		</form>
		<?
	}
	else{
		global $site_lang; 
		?>
		<form method="POST" name="comentario" id="comentario" onSubmit="return checkEmail(notificacao_email);">
			<input type="hidden" name="idprod" id="idprod" value="<?= intval($_GET['idprod']); ?>">

			<div class="row">
				<div class="col-xs-3">
					<div class="wp-side-image2">
						<div class="image"style="max-height:65px;overflow:hidden"><img src="<?php echo $info2['prd_imgm'];?>" alt="<?php echo $info2['prod_nome'];?>"></div>
						<div class="clearfix"></div>
					</div>
				</div>
				<div class="col-xs-9">
					<div class="subtitulo_d" style="margin-bottom:5px">SOLICITAÇÃO DE FOTOS DO EQUIPAMENTO!</div><p style="color:#989899;font-size:12px;line-height:12px">Pretendo receber fotos produto real.</p>
				</div>
			</div>
			<br>
			<div class="form-group">
				<input placeholder="E-mail" class="form-control" type="text" name="notificacao_email" id="notificacao_email" value="">
			</div>
			<div class="form-group">
				<input class="button3" type="submit" id="Submeter" name="Submeter" value="Enviar">
			</div>
		</form>
		<?
	}
}
?>
</body>
</html>