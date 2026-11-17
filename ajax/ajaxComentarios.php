<?php
	$path='../core/';
	require_once $path.'settings.php';
	require_once $path.'sessions_engine.php';
	require_once $path.'db_engine.php';
	require_once $path.'seo_v2.php';
	require_once $path.'linguas.php';
	require_once $path.'global.php';
	require_once $path.'logins.php';
	require_once $path.'news.php';
	require_once $path.'shop.php';
?>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<META NAME="robots" CONTENT="noindex">
    <link rel="stylesheet" href="../layout/template.css?dp=<?=dumpcache?>" type="text/css" media="all" />
	<link rel="stylesheet" href="../external_api/apprise/apprise-v2.css?dp=<?=dumpcache?>" type="text/css" media="all" />
    <script src="../external_api/jquery-1.7.1.min.js?dp=<?=dumpcache?>"></script>
	<script src="../external_api/apprise/apprise-v2.js?dp=<?=dumpcache?>"></script>
    <script src="../external_api/reveal/jquery.fancybox.js?dp=<?=dumpcache?>"></script>
    <script src="../external_api/tooltipster/jquery.tooltipster.js?dp=<?=dumpcache?>"></script>
    <script src="../external_api/jquery.selectbox/jquery.selectbox-0.2.js?dp=<?=dumpcache?>"></script>
    <script src="../external_api/domesticos.js?dp=<?=dumpcache?>"></script>
    <style>body{overflow:hidden}</style>
</head>
<body>
<?
if(isset($_POST['Submeter']) && isset($_POST['idpage']) && $_POST['idpage']>0 )
{
	$res=inserirNovoComentario();
	echo '<table width="100%" border="0" cellpadding="0" cellspacing="0"><tr><td align="center" valign="middle" height="180px">Obrigado pelo seu comentário.</td></tr></table>';
	echo '<script>setTimeout(function(){parent.jQuery.fancybox.close();},1000)</script>';
}
else
{
	if($dados=cliente_logado())
	{
		$info=getDadosCliente($dados['cli_id']);
		$nome 	= $info['Nome'];
		$cidade=$info['Cidade'];
		$pais 	= $info['Pais'];
		global $site_lang,$key_comentarios_nome,$key_comentarios_local,$key_comentarios_pais,$key_comentarios_mensagem;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs_paises=new Dataobj;
		?>	
        <form method="POST" name="comentario" id="comentario" onSubmit="return validarNovoComentario();">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <input type="hidden" name="idpage" id="idpage" value="<?=$_GET['idpage'];?>">
                <input type="hidden" name="iduser" id="iduser" value="<?=$_GET['iduser'];?>">
                <tr> 
                    <td width="30%" class="HvBd" style="font-size:14px;"><?=$key_comentarios_nome[$site_lang];?>:</td>
                    <td width="70%"  class="HvRm" style="color:#6E6E6E;font-size:12px;"><div style="margin-bottom:6px;"><?=$nome;?><input type="hidden" name="comentario_nome" id="comentario_nome" value="<?=$nome?>"></div></td>
                </tr>				
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_comentarios_local[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;"><div style="margin-bottom:6px;"><?=$cidade?><input type="hidden" name="comentario_local" id="comentario_local" value="<?=$cidade?>"></div></td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_comentarios_pais[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;"><div style="margin-bottom:6px;">
                        <select class="inputregisto HvMd comentario_pais" style="width:331px" disabled="disabled">
                        <?
                            $sql='SELECT czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$site_lang.'" order by czl.zone_name ASC';
                            $rs_paises->db_open($db_link, $DB_name, $sql);
                            while(!$rs_paises->EOF())
                            {
                                $zone_name=$rs_paises->db_data['zone_name'];
                                echo '<option value="'.$zone_name.'"'; 
                                if(!$pais) $pais="Portugal";
                                if($pais==$zone_name){ 
                                    echo 'selected="selected"';
                                }
                                echo '>'.$zone_name.'</option>';

                                $rs_paises->db_next();
                            }
                            $rs_paises->db_close();
                        ?>
                        </select>
                        <input type="hidden" name="comentario_pais" id="comentario_pais" value="<?=$pais?>" ></div>
                    </td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_comentarios_mensagem[$site_lang];?>:</td>
                    <td>
                        <textarea name="comentario_mensagem" id="comentario_mensagem" style="width:333px;height:80px;resize:none;color:#6E6E6E;font-size:12px;" class="HvRm"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input style="margin-top:10px" class="button3" type="submit" id="Submeter" name="Submeter" value="Enviar Comentário">
                    </td>
                </tr>
            </table>
        </form>
		<?
		}
		else
		{
		global $site_lang,$key_comentarios_nome,$key_comentarios_local,$key_comentarios_pais,$key_comentarios_mensagem;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs_paises=new Dataobj;?>	
		<form method="POST" name="comentario" id="comentario" onSubmit="return validarNovoComentario();">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<input type="hidden" name="idpage" id="idpage" value="<?=$_GET['idpage'];?>">
				<input type="hidden" name="iduser" id="iduser" value="0">
				<tr> 
					<td width="30%" class="HvBd" style="font-size:14px;"><?=$key_comentarios_nome[$site_lang];?>:</td>
					<td width="70%"  class="HvRm" style="color:#6E6E6E;font-size:12px;"><input type="text" name="comentario_nome" id="comentario_nome" style="width:331px; margin-bottom:5px;" class="inputregisto HvRm"></td>
				</tr>
				<tr>
					<td class="HvBd" style="font-size:14px;"><?=$key_comentarios_local[$site_lang];?>:</td>
					<td class="HvRm" style="color:#6E6E6E;font-size:12px;"><input type="text" name="comentario_local" id="comentario_local"  style="width:331px; margin-bottom:5px;" class="inputregisto HvRm"></td>
				</tr>
				<tr>
					<td class="HvBd" style="font-size:14px;"><?=$key_comentarios_pais[$site_lang];?>:</td>
					<td class="HvRm" style="color:#6E6E6E;font-size:12px;">
						<select class="inputregisto HvMd comentario_pais" style="width:331px;margin-bottom:5px;" name="comentario_pais" id="comentario_pais" >
						<?
							$sql='SELECT c.zone_id,czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$site_lang.'" order by czl.zone_name ASC';
							$rs_paises->db_open($db_link, $DB_name, $sql);
							while(!$rs_paises->EOF())
							{
								$zone_name=$rs_paises->db_data['zone_name'];
								$zone_id=$rs_paises->db_data['zone_id'];
								echo '<option value="'.$zone_name.'"'; 
								if(!$comentario_pais) $comentario_pais=165;
								if($comentario_pais==$zone_id){ 
									echo 'selected="selected"';
								}
								echo '>'.$zone_name.'</option>';

								$rs_paises->db_next();
							}
							$rs_paises->db_close();
						?>
					</select>
					</td>
				</tr>
				<tr>
					<td class="HvBd" style="font-size:14px;"><?=$key_comentarios_mensagem[$site_lang];?>:</td>
					<td>
						<textarea  name="comentario_mensagem" id="comentario_mensagem" style="width:331px;height:80px;resize:none;color:#6E6E6E;font-size:12px;" class="inputregisto HvRm"></textarea>
					</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td>
						<input style="margin-top:10px" class="button3" type="submit" id="Submeter" name="Submeter" value="Enviar Comentário">
					</td>
				</tr>
			</table>
		</form>
		<?
		}
}
?>
</body>
</html>