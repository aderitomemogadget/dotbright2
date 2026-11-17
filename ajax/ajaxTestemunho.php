<?php
$path='../core/';
require_once $path.'settings.php';
require_once $path.'sessions_engine.php';
require_once $path.'db_engine.php';
require_once $path.'seo_v2.php';
require_once $path.'linguas.php';
require_once $path.'global.php';
require_once $path.'logins.php';
require_once $path.'products.php';
require_once $path.'shop.php';
?><html xmlns="http://www.w3.org/1999/xhtml">
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
    <style>body {overflow:hidden;}</style>
</head>
<body>
<?
if(isset($_POST['Submeter']) && isset($_POST['idprod']) && $_POST['idprod']>0)
{
	$res=inserirNovoTestemunho();
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
		global $site_lang,$key_testemunhos_nome,$key_testemunhos_local,$key_testemunhos_pais,$key_testemunhos_mensagem;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs_paises=new Dataobj;?>
        <form method="POST" name="wishlist" id="wishlist" onSubmit="return validarNovoTestemunho();">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <input type="hidden" name="idprod" id="idprod" value="<?=$_GET['idprod'];?>">
                <tr> 
                    <td width="30%" class="HvBd" style="font-size:14px;"><?=$key_testemunhos_nome[$site_lang];?>:</td>
                    <td width="70%"  class="HvRm" style="color:#6E6E6E;font-size:12px;"><div style="margin-bottom:6px;"><?=$nome;?><input type="hidden" name="testemunho_nome" id="testemunho_nome" value="<?=$nome?>"></div></td>
                </tr>				
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_local[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;"><div style="margin-bottom:6px;"><?=$cidade?><input type="hidden" name="testemunho_local" id="testemunho_local" value="<?=$cidade?>" ></div></td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_pais[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;">
                        <select class="inputregisto HvMd testemunho_pais" style="width:331px"  disabled="disabled">
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
                        <input type="hidden" name="testemunho_pais" id="testemunho_pais" value="<?=$pais?>" >
                    </td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_mensagem[$site_lang];?>:</td>
                    <td>
                        <textarea name="testemunho_mensagem" id="testemunho_mensagem" style="width:333px;height:80px;resize:none;color:#6E6E6E;font-size:12px;" class="HvRm"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input style="margin-top:10px" class="button3" type="submit" id="Submeter" name="Submeter" value="Submeter">
                    </td>
                </tr>
            </table>
        </form>
	<?
	}
	else
	{
		global $site_lang,$key_testemunhos_nome,$key_testemunhos_local,$key_testemunhos_pais,$key_testemunhos_mensagem;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs_paises=new Dataobj;?>	
        <form method="POST" name="wishlist" id="wishlist" onSubmit="return validarNovoTestemunho();">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <input type="hidden" name="idprod" id="idprod" value="<?=$_GET['idprod'];?>">
                <tr> 
                    <td width="30%" class="HvBd" style="font-size:14px;"><?=$key_testemunhos_nome[$site_lang];?>:</td>
                    <td width="70%" class="HvRm" style="color:#6E6E6E;font-size:12px;"><input type="text" name="testemunho_nome" id="testemunho_nome" value="" style="width:331px;margin-bottom:5px;" class="inputregisto HvRm"></td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_local[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;"><input type="text" name="testemunho_local" id="testemunho_local" value="" style="width:331px;margin-bottom:5px;" class="inputregisto HvRm"></td>
                </tr>
                <tr>
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_pais[$site_lang];?>:</td>
                    <td class="HvRm" style="color:#6E6E6E;font-size:12px;">
                        <select class="inputregisto HvMd testemunho_pais" style="width:331px;margin-bottom:5px;" name="testemunho_pais" id="testemunho_pais" >
                        <?
                            $sql='SELECT c.zone_id,czl.zone_name FROM country_zones as c inner join country_zones_lang as czl on c.zone_id=czl.zone_id where czl.zone_lang="'.$site_lang.'" order by czl.zone_name ASC';
                            $rs_paises->db_open($db_link, $DB_name, $sql);
                            while(!$rs_paises->EOF())
                            {
                                $zone_name=$rs_paises->db_data['zone_name'];
                                $zone_id=$rs_paises->db_data['zone_id'];
                                echo '<option value="'.$zone_name.'"'; 
                                if(!$testemunho_pais) $testemunho_pais=165;
                                if($testemunho_pais==$zone_id){ 
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
                    <td class="HvBd" style="font-size:14px;"><?=$key_testemunhos_mensagem[$site_lang];?>:</td>
                    <td>
                        <textarea name="testemunho_mensagem" id="testemunho_mensagem" style="width:331px;height:80px;resize:none;color:#6E6E6E;font-size:12px;" class="inputregisto HvRm"></textarea>
                    </td>
                </tr>
                <tr>
                    <td>&nbsp;</td>
                    <td>
                        <input style="margin-top:10px" class="button3" type="submit" id="Submeter" name="Submeter" value="Submeter">
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