<?php
    if(file_exists('../files/settings2.php'))  $data = file_get_contents('../files/settings2.php');
    else $data = file_get_contents('../../core/settings.php');
    file_put_contents('../files/settings_temp.php', $data);
	include_once '../files/settings_temp.php';
	$vars = get_defined_vars();

	// echo '<pre>';print_r($vars);echo '</pre>';

	$flag=false;
	foreach ($vars as $key => $value) {
		if($key=='dev') $flag=true;
		if($flag) $variaveis[$key] = $value;
		if (strpos($key,'_page_id') !== false) $paginas[$key] = $value;
		if (strpos($key,'template_') !== false) $templates[$key] = $value;
	}
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include "funcoes.inc.php";
		
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";

	$task_button="Alterar";$task_form='altera';

?>
<script language="JavaScript">
if(document.getElementById)
{ 
    document.write('<style type="text/css">\n')
    document.write('.submenu{display: none;}\n')
    document.write('</style>\n')
}
function showmenu_prod(obj,obj2){
document.form.tab_a.value=obj;
document.form.tab_b.value=obj2;
if(document.getElementById){

    var el=document.getElementById(obj);
    var el2=document.getElementById(obj2);
    
    var ar=document.getElementById("masterdiv").getElementsByTagName("div"); 
    var ar2=document.getElementById("masterdiv").getElementsByTagName("td"); 
    
        if(el.style.display !="block"){
            for (var i=0; i<ar.length; i++){
                v="sub"+(i+1);
                 if(ar[i].className=="submenu") ar[i].style.display="none";
            }
            
            for (var i=0; i<ar2.length; i++){
                v="sub"+(i+1);
                 if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
            }
            el.style.display="block";
            el2.style.background="#000000";
        }
    }

}
window.onload=function(){<? if($t1 && $t2){?>showmenu_prod('<? echo $t1?>','<? echo $t2?>');<? }elseif(isset($_GET['pagina'])){ ?>showmenu_prod('sub2','pinta2');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}
</script>
<div class="center">

		<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border="0" cellspacing="0" cellpadding="4" align="center">
                <tr>
                    <td valign="top">
                        <div id="masterdiv"><br />
                            <table height="20" border=0 cellspacing=0 cellpadding=0>
                            <tr>
                                <td width="120" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer;background:#000" id="pinta1">Definições Gerais</td>
                                <td><img src="cm_fill.gif" width="8" height="1"></td>                       
                                <td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2">Logging</td>
                            </tr>
                            </table> 

                            <div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1">
                                <form name="form" method="post" action="pagina_trata.php?dump=<?=uniqID("")?>" enctype="multipart/form-data">
                                <input type="hidden" name="slashes" value="'">
                                <input type="hidden" name="tab_a" value="0">
                                <input type="hidden" name="tab_b" value="0">
                                <table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#F6F6F6" class="quadros">
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Ligação Base de Dados</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Host:</td>
                                    <td align="left" class="preto"><input name="DB_host" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['DB_host'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">User:</td>
                                    <td align="left" class="preto"><input name="DB_user" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['DB_user'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Password:</td>
                                    <td align="left" class="preto"><input name="DB_pass" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['DB_pass'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Nome:</td>
                                    <td align="left" class="preto"><input name="DB_name" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['DB_name'];?>"></td>
                                </tr>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Caminhos Plataforma</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Protocolo HTTP:</td>
                                    <td align="left" class="preto"><input name="http" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['http'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Pasta de desenvolvimento:</td>
                                    <td align="left" class="preto"><input name="dev_folder" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['dev_folder'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Shell URL:</td>
                                    <td align="left" class="preto"><input name="site_shell_url" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_shell_url'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">HTTP URL:</td>
                                    <td align="left" class="preto"><input name="site_http_url" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_http_url'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">HTTPS URL:</td>
                                    <td align="left" class="preto"><input name="site_https_url" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_https_url'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Website URL Email:</td>
                                    <td align="left" class="preto"><input name="website_url_email" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['website_url_email'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">HTTP URL Imagens:</td>
                                    <td align="left" class="preto"><input name="m_http_url_images" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['m_http_url_images'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">HTTP URL Ajax:</td>
                                    <td align="left" class="preto"><input name="site_http_ajax_url" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_http_ajax_url'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">HTTP URL Interno:</td>
                                    <td align="left" class="preto"><input name="site_http_url_internal" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_http_url_internal'];?>"></td>
                                </tr>
                                <tr>
                                    <td colspan="3">
                                        <span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Definições SMTP</span>
                                        <div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Nome que aparece no E-mail:</td>
                                    <td align="left" class="preto"><input name="FromName" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['FromName'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">E-mail enviado pelo site:</td>
                                    <td align="left" class="preto"><input name="form_recipient" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['form_recipient'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SMTP Host:</td>
                                    <td align="left" class="preto"><input name="Default_SMTP" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['Default_SMTP'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SMTP Login:</td>
                                    <td align="left" class="preto"><input name="Default_SMTP_login" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['Default_SMTP_login'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SMTP Password:</td>
                                    <td align="left" class="preto"><input name="Default_SMTP_pass" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['Default_SMTP_pass'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SMTP E-mail:</td>
                                    <td align="left" class="preto"><input name="Default_SMTP_Email" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['Default_SMTP_Email'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SMTP E-mail Notificações:</td>
                                    <td align="left" class="preto"><input name="Default_notifications_email" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['Default_notifications_email'];?>"></td>
                                </tr>   
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Definições Website</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>                    
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Titulo:</td>
                                    <td align="left" class="preto"><input name="site_title" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['site_title'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Copyright:</td>
                                    <td align="left" class="preto"><input name="site_copyright" style="WIDTH: 750px; HEIGHT: 20px" value="<?=htmlentities($variaveis['site_copyright']);?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">RSS URL:</td>
                                    <td align="left" class="preto"><input name="rss_site_url" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['rss_site_url'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">RSS Descrição:</td>
                                    <td align="left" class="preto"><textarea name="rss_site_description" style="WIDTH: 750px; HEIGHT: 50px"><?=$variaveis['rss_site_description'];?></textarea></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">RSS E-mail Editor:</td>
                                    <td align="left" class="preto"><input name="rss_email_editor" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['rss_email_editor'];?>"</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">RSS E-mail Autor:</td>
                                    <td align="left" class="preto"><input name="rss_email_autor" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['rss_email_autor'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">RSS E-mail Webmaster:</td>
                                    <td align="left" class="preto"><input name="rss_email_webmaster" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['rss_email_webmaster'];?>"></td>
                                </tr>      
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Margem de lucro Default:</td>
                                    <td align="left" class="preto"><input name="margem_lucro_default" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['margem_lucro_default'];?>"></td>
                                </tr>      
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Taxa de IVA Default:</td>
                                    <td align="left" class="preto"><input name="iva_default" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['iva_default'];?>"></td>
                                </tr>     
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Valor máximo por encomenda:</td>
                                    <td align="left" class="preto"><input name="valor_max_encomenda" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['valor_max_encomenda'];?>"></td>
                                </tr>                     
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Páginas</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">menu_topo_id</td>
                                    <td align="left" class="preto"><input name="menu_topo_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['menu_topo_id'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">cart_purchage_id</td>
                                    <td align="left" class="preto"><input name="cart_purchage_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['cart_purchage_id'];?>"></td>
                                </tr>
                                <?php 
                                foreach ($paginas as $key => $value) {
                                	?>
                                	<tr> 
        	                            <td>&nbsp;</td>
        	                            <td class="preto" align="left"><?=$key?></td>
        	                            <td align="left" class="preto"><input name="<?=$key?>" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$value;?>"></td>
        	                        </tr>
                                	<?php
                                }
                                ?>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Templates</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <?php 
                                foreach ($templates as $key => $value) {
                                	?>
                                	<tr> 
        	                            <td>&nbsp;</td>
        	                            <td class="preto" align="left"><?=$key?></td>
        	                            <td align="left" class="preto"><input name="<?=$key?>" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$value;?>"></td>
        	                        </tr>
                                	<?php
                                }
                                ?>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Publicidade</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <?php 
                                $publicidade = $variaveis['publicidade'];
                             
                                for($i=0;$i<count($publicidade['codigo']);$i++){
                                	?>
                                	<tr> 
        	                            <td>&nbsp;</td>
        	                            <td class="preto" align="left"><?=$publicidade['nome'][$i]?><input style="display:none" name="publicidade_nome[]" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$publicidade['nome'][$i]?>"></td>
        	                            <td align="left" class="preto"><input name="publicidade_codigo[]" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$publicidade['codigo'][$i]?>"></td>
        	                        </tr>
                                	<?php
                                }
                                ?>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Outras configurações</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Em Desenvolvimento?</td>
                                    <td align="left" class="preto">
                                        <select name="dev">
                                            <option value="1" <?php if($variaveis['dev']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['dev']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Lingua Admin:</td>
                                    <td align="left" class="preto"><input name="lingua_admin" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['lingua_admin'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Lingua Website:</td>
                                    <td align="left" class="preto"><input name="lingua_website" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['lingua_website'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">SEO:</td>
                                    <td align="left" class="preto">
                                        <select name="seo">
                                            <option value="1" <?php if($variaveis['seo']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['seo']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>   
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Modulo Voucher:</td>
                                    <td align="left" class="preto">
                                        <select name="module_voucher">
                                            <option value="1" <?php if($variaveis['module_voucher']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['module_voucher']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Tem Produtos:</td>
                                    <td align="left" class="preto">
                                        <select name="tem_produtos">
                                            <option value="1" <?php if($variaveis['tem_produtos']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['tem_produtos']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Tem Carrinho:</td>
                                    <td align="left" class="preto">
                                        <select name="tem_carrinho">
                                            <option value="1" <?php if($variaveis['tem_carrinho']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['tem_carrinho']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Tem Marcas:</td>
                                    <td align="left" class="preto">
                                        <select name="tem_marcas">
                                            <option value="1" <?php if($variaveis['tem_marcas']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['tem_marcas']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Tem Carteira:</td>
                                    <td align="left" class="preto">
                                        <select name="tem_carteira">
                                            <option value="1" <?php if($variaveis['tem_carteira']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['tem_carteira']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Tem Nickname:</td>
                                    <td align="left" class="preto">
                                        <select name="tem_nickname">
                                            <option value="1" <?php if($variaveis['tem_nickname']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['tem_nickname']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Save Stats:</td>
                                    <td align="left" class="preto">
                                        <select name="save_stats">
                                            <option value="1" <?php if($variaveis['save_stats']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['save_stats']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Registo Ativo Auto:</td>
                                    <td align="left" class="preto">
                                        <select name="registo_ativo_auto">
                                            <option value="1" <?php if($variaveis['registo_ativo_auto']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['registo_ativo_auto']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Subscribe Newsletter:</td>
                                    <td align="left" class="preto">
                                        <select name="subscribe_newsletter">
                                            <option value="1" <?php if($variaveis['subscribe_newsletter']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['subscribe_newsletter']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Usar tabelas de memoria:</td>
                                    <td align="left" class="preto">
                                        <select name="usememorytables">
                                            <option value="1" <?php if($variaveis['usememorytables']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['usememorytables']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">NIF Obrigatório?</td>
                                    <td align="left" class="preto">
                                        <select name="nif_mandatory">
                                            <option value="1" <?php if($variaveis['nif_mandatory']==1) echo 'selected'; ?>>Sim</option>
                                            <option value="0" <?php if($variaveis['nif_mandatory']==0) echo 'selected'; ?>>Não</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Familias Destaques:</td>
                                    <td align="left" class="preto"><input name="familias_destaques" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['familias_destaques'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">N Imagens Marca:</td>
                                    <td align="left" class="preto"><input name="n_imagens_marca" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['n_imagens_marca'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">N Imagens Familia:</td>
                                    <td align="left" class="preto"><input name="n_imagens_familia" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['n_imagens_familia'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Google Analytics Media ID:</td>
                                    <td align="left" class="preto"><input name="googleanalyticsmedia" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['googleanalyticsmedia'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Substituição de noticias Media ID:</td>
                                    <td align="left" class="preto"><input name="media_substituicao_noticias" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['media_substituicao_noticias'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Facebook ID:</td>
                                    <td align="left" class="preto"><input name="facebook_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['facebook_id'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Facebook Secret ID:</td>
                                    <td align="left" class="preto"><input name="facebook_secret_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['facebook_secret_id'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Twitter Consumer ID:</td>
                                    <td align="left" class="preto"><input name="twitter_consumer_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['twitter_consumer_id'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Twitter Consumer Secret:</td>
                                    <td align="left" class="preto"><input name="twitter_consumer_secret" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['twitter_consumer_secret'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Google Client ID:</td>
                                    <td align="left" class="preto"><input name="google_client_id" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['google_client_id'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Google client Secret:</td>
                                    <td align="left" class="preto"><input name="google_client_secret" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['google_client_secret'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Timezone:</td>
                                    <td align="left" class="preto">
                                        <select name="timezone">
                                        <?php 
                                            $tzlist = DateTimeZone::listIdentifiers(DateTimeZone::ALL); 
                                            foreach ($tzlist as $value) {
                                                ?><option <?php if(date_default_timezone_get()==$value) echo 'selected'; ?>><?=$value?></option><?php
                                            }
                                        ?>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Configurações de videos youtube</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Video Autoplay:</td>
                                    <td align="left" class="preto"><input name="video_autoplay" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['video_autoplay'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Videos Relacionados:</td>
                                    <td align="left" class="preto"><input name="video_rel" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['video_rel'];?>"></td>
                                </tr>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Editor</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Base Virtual:</td>
                                    <td align="left" class="preto"><input name="sBaseVirtual0" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['sBaseVirtual0'];?>"></td>
                                </tr>
                                <tr> 
                                    <td>&nbsp;</td>
                                    <td class="preto" align="left">Base:</td>
                                    <td align="left" class="preto"><input name="sBase0" style="WIDTH: 750px; HEIGHT: 20px" value="<?=$variaveis['sBase0'];?>"></td>
                                </tr>
                                <tr>
                                	<td colspan="3">
                                		<span style="font-weight:bold; font-size:18px; font-family:Arial; margin-left:15px;margin-top:15px;float:left">Código novo a adicionar ao ficheiro</span>
                                		<div style="width:100%; height:1px; background:grey; float:left; margin-top:5px; margin-bottom:10px;"></div>
                                	</td>
                                </tr>
                                <tr> 
                                    <td colspan="3" align="center" class="preto"><textarea name="novo_codigo" style="WIDTH: 95%; HEIGHT: 150px"></textarea></td>
                                </tr>
                                <tr> 
                                    <td colspan="3" align="center" class="preto">&nbsp;</td>
                                </tr>
                                </table>
                                <? show_page_buttons(); ?>
                                </form>
                            </div>

                            <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
                                
                                    <?php


                                        global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                                        $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
                                        $rs = new DataObj;$rs2 = new DataObj;

                                        $n_paginas = 20;
                                        $sql='SELECT count(*) as Total FROM logs_crons order by Data DESC';
                                        $rs->db_open($db_link, $DB_name, $sql);     
                                        $total = $rs->db_data['Total'];
                                        $rs->db_close();

                                        $n_pags = ceil($total / $n_paginas);
                                        if(!isset($_GET['pagina'])) $pagina = 1;
                                        else $pagina = $_GET['pagina'];
                                        
                                        $inicio = $pagina - 1;
                                        $inicio = $inicio * $n_paginas;                          
                                        $sql='SELECT * FROM logs_crons order by Data DESC';
                                        $sql2 = $sql.' Limit '.$inicio.', '.$n_paginas;
                                        $db_res = $rs->db_open($db_link,$DB_name,$sql2);
                                        if(!$rs->EOF())
                                        {    ?>
                                            <table width="100%" border="0" cellpadding="2" cellspacing="0" bgcolor="#F6F6F6" class="quadros" style="table-layout:fixed">
                                            <?php
                                            while(!$rs->EOF())
                                            {
                                                $mensagem   = stripslashes($rs->db_data["Mensagem"]);
                                                $query      = stripslashes($rs->db_data["Query"]);
                                                $data       = stripslashes($rs->db_data["Data"]);

                                                ?>
                                                <tr> 
                                                    <td colspan="3"><div style="width:100%; height:1px; background:grey; "></div></td>
                                                </tr>
                                                <tr> 
                                                    <td class="preto" align="left" style="word-wrap:break-word;"><?php echo $mensagem;?></td>
                                                    <td align="left"  class="preto" style="word-wrap:break-word;"><?php echo $query;?></td>
                                                    <td align="left" class="preto" style="word-wrap:break-word;"><?php echo $data;?></td>
                                                </tr>
                                                <?php 


                                                $rs->db_next();
                                            }  
                                             ?>

                                            </table>
                                        <?php 
                                            if($n_pags > 1)
                                            {       
                                                $return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
                                                if($pagina > 6)
                                                {
                                                    $pgn = 'onclick=window.open("pagina.php?pagina=1","_self")';             
                                                    $return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
                                                }
                                                for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
                                                {
                                                    $pgn = 'onclick=window.open("pagina.php?pagina='.$pi.'","_self")'; 
                                                    $return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
                                                }
                                                if($n_pags!=$pagina)
                                                {
                                                    $pgn='onclick=window.open("pagina.php?pagina='.$n_pags.'","_self")'; 
                                                    $return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
                                                }
                                                $return .= '<td>&nbsp;</td></tr></table>';
                                                echo $return;
                                            }   
                                        }
                                        else
                                        {?>
                                            <table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
                                            <tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
                                            <td colspan="8" align="center">Não Existem Logs</td>
                                            </tr>
                                            </table>
                                        <?php 
                                        }
                                        $rs->db_close();
                                    ?>
                            </div>
                        </div>
                    </td>
                </tr>
                </table>
            </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200px">&nbsp;</td>
        </tr>
        </table>
</div>
<? 	include "../layout/bottom.inc.php";?>