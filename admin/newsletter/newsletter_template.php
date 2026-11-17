<?
	include "settings.inc.php";
	global $site_http_url;
	$linguas_arr=explode(",",$linguas);
	$linguas_desc_arr=explode(",",$linguas_desc);
	if($task=="apagar"){$task_button="Apagar este Registo"; $task_form="apaga";}
	elseif ($task == "copiar")	{$task_button="Copiar"; $task_form="copia";}
	elseif($ID==0){$task_button="Gravar o Novo Registo"; $task_form="insere";}
	else {$task_button="Gravar alterações"; $task_form="altera";}
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	if($_GET['TEMPLATE'])$Template=$_GET['TEMPLATE'];
	if($_POST['ID'])$ID=$_POST['ID'];
	if($_POST['task_form'])$task=$_POST['task_form'];
	if($_POST['TEMPLATE'])$Template=$_POST['TEMPLATE'];
	if($_POST['show_tipo'])$show_tipo=$_POST['show_tipo'];
	if($_POST['template_options'])$template_options=$_POST['template_options'];
	if($_POST['grava_tipo'])$grava_tipo=$_POST['grava_tipo'];
	if($_GET['ID_Linha'])$id_linha=$_GET['ID_Linha'];
	if($Template && $ID==0) $task2="insere";
	if($grava_tipo)
	{
		$img_path="../../files/news_temp/";
		if(isset($_POST['campo1'])) $campo1=$_POST['campo1'];
		if(isset($_POST['campo2'])) $campo2=$_POST['campo2'];
		if(isset($_POST['campo3'])) $campo3=$_POST['campo3'];
		if(isset($_POST['texto_bg'])) $texto_bg=$_POST['texto_bg'];
		if(isset($_POST['texto_bg2'])) $texto_bg2=$_POST['texto_bg2'];
		if(isset($_POST['texto_bg3'])) $texto_bg3=$_POST['texto_bg3'];
		if(isset($_POST['cor_bg'])) $cor_bg=$_POST['cor_bg'];
		if(isset($_POST['cor_bg2'])) $cor_bg2=$_POST['cor_bg2'];
		if(isset($_POST['cor_bg3'])) $cor_bg3=$_POST['cor_bg3'];
		if(isset($_POST['ordem'])) $ordem=$_POST['ordem'];
		if(isset($_POST['link'])) $link=$_POST['link'];
		if(isset($_POST['link2'])) $link2=$_POST['link2'];
		if(isset($_POST['link3'])) $link3=$_POST['link3'];
		if(isset($_POST['align'])) $align=$_POST['align'];
		if(isset($_POST['align2'])) $align2=$_POST['align2'];
		if(isset($_POST['align3'])) $align3=$_POST['align3'];
		if(isset($_POST['valign'])) $valign=$_POST['valign'];
		if(isset($_POST['valign2'])) $valign2=$_POST['valign2'];
		if(isset($_POST['valign3'])) $valign3=$_POST['valign3'];
		if(isset($_POST['font'])) $font=$_POST['font'];
		if(isset($_POST['font2'])) $font2=$_POST['font2'];
		if(isset($_POST['font3'])) $font3=$_POST['font3'];
		$Img_peq1['name']=$_FILES['img_peq1']['name'];
		$Img_peq1['temp']=$_FILES['img_peq1']['tmp_name'];
		$Img_peq2['name']=$_FILES['img_peq2']['name'];
		$Img_peq2['temp']=$_FILES['img_peq2']['tmp_name'];
		$Img_peq3['name']=$_FILES['img_peq3']['name'];
		$Img_peq3['temp']=$_FILES['img_peq3']['tmp_name'];
		$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
		$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
		$altera_img3=0;if($Img_peq3['name']){$altera_img3=1;$pos=strrpos($Img_peq3['name'],".");$img_ext3=substr($Img_peq3['name'],$pos+1,strlen($Img_peq3['name'])+1-$pos);}
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($id_linha > 0)
		{
			$rs=new DataObj;
			$rs->db_open($db_link,$DB_name, "SELECT * FROM nl_newsletter_template WHERE ID_ORG=$ID and ID=$id_linha");
			if(!$rs->EOF())
			{
				$img_ext=$rs->db_data['img_ext'];
				$img_ext2=$rs->db_data['img2_ext'];
				$img_ext3=$rs->db_data['img3_ext'];
			}
			if($Apagar_img1){$ficheiro1=$id_linha."_1.".$img_ext; unlink($img_path.$ficheiro1); if(!$altera_img)$img_ext='';}
			if($Apagar_img2){$ficheiro2=$id_linha."_2.".$img2_ext; unlink($img_path.$ficheiro2); if(!$altera_img2)$img_ext2='';}
			if($Apagar_img3){$ficheiro3=$id_linha."_3.".$img3_ext; unlink($img_path.$ficheiro3); if(!$altera_img3)$img_ext3='';}

			$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
			$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
			$altera_img3=0;if($Img_peq3['name']){$altera_img3=1;$pos=strrpos($Img_peq3['name'],".");$img_ext3=substr($Img_peq3['name'],$pos+1,strlen($Img_peq3['name'])+1-$pos);}
			$sql="update nl_newsletter_template SET Font1='$font',Font2='$font2',Font3='$font3',Texto1='$campo1',Texto2='$campo2',Texto3='$campo3',Cor1='$texto_bg',Cor2='$texto_bg2',Cor3='$texto_bg3',Bg1='$cor_bg',Bg2='$cor_bg2',Bg3='$cor_bg3',Tipo='$grava_tipo',img_ext='$img_ext',img2_ext='$img_ext2',img3_ext='$img_ext3',Ordem='$ordem',Link1='$link',Link2='$link2',Link3='$link3',Align1='$align',Align2='$align2',Align3='$align3',VAlign1='$valign',VAlign2='$valign2',VAlign3='$valign3' where ID_ORG='$ID' and ID='$id_linha'";
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				if($altera_img) copia_segura($Img_peq1['temp'],$img_path.$id_linha."_1.".$img_ext);
				if($altera_img2) copia_segura($Img_peq2['temp'],$img_path.$id_linha."_2.".$img_ext2);
				if($altera_img3) copia_segura($Img_peq3['temp'],$img_path.$id_linha."_3.".$img_ext3);
			}
			else echo db_error($db_link);
			echo '<script>window.open("newsletter_template.php?ID='.$ID.'","_self")</script>';
		}
		else
		{
			$sql="insert into nl_newsletter_template (ID_ORG,Texto1,Texto2,Texto3,Cor1,Cor2,Cor3,Bg1,Bg2,Bg3,Tipo,img_ext,img2_ext,img3_ext,Ordem,Link1,Link2,Link3,Align1,Align2,Align3,VAlign1,VAlign2,VAlign3,Font1,Font2,Font3) VALUES ('$ID','$campo1','$campo2','$campo3','$texto_bg','$texto_bg2','$texto_bg3','$cor_bg','$cor_bg2','$cor_bg3','$grava_tipo','$img_ext','$img_ext2','$img_ext3','$ordem','$link','$link2','$link3','$align','$align2','$align3','$valign','$valign2','$valign3','$font','$font2','$font3')";
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$new_id=db_get_last_insert_id($db_link);
				if(isset($_POST['ID_Linhap']) && $_POST['ID_Linhap'] > 0)
				{
					$sql="UPDATE nl_newsletter_template_prods SET bloco_id='".$new_id."' WHERE bloco_id='".$_POST['ID_Linhap']."'";
					$res1=Runquery($db_link,$DB_name,$sql);
				}
				if($altera_img) copia_segura($Img_peq1['temp'],$img_path.$new_id."_1.".$img_ext);
				if($altera_img2) copia_segura($Img_peq2['temp'],$img_path.$new_id."_2.".$img_ext2);
				if($altera_img3) copia_segura($Img_peq3['temp'],$img_path.$new_id."_3.".$img_ext3);
			}
			else echo db_error($db_link);
		}
		if($id_linha > 0) $task2="altera";
	}
	if($task2=="eliminar" && $id_linha>0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql="delete from nl_newsletter_template where ID=$id_linha";
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res) echo db_error($db_link);
		$task2="altera";
	}
	include '../layout/header.inc.php';
	include '../layout/body.inc.php';
?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<script language="JavaScript" src="sa.js"></script>
<style>.new_quadros{background:#FFF !important}</style>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top" align="left"><? include '../layout/menus.inc.php';?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
            <div class="panel-heading">
                <h4 style="color:#fff">NEWSLETTER - CONFIG</h4>
            </div>
        </div><?
	$cabecalho='';$texto='';$m_visivel='';
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($ID != 0)
	{
		$dados=BuscarNewsletterindice2($ID);
		if($dados)
		{
			$ID=$dados->db_data['ID'];
			$cabecalho=$dados->db_data['Cabecalho'];
			$assunto=$dados->db_data['Assunto'];
            if($task == "copiar") $m_nome .= " (CÓPIA)";
			$texto=$dados->db_data['Texto'];
			$texto=limpa_editor_lixo($texto);
			$m_lingua=$dados->db_data['Lingua'];
			$m_visivel=$dados->db_data['Visivel'];
			$SMTP=$dados->db_data['SMTP'];
			$SMTP_Email=$dados->db_data['SMTP_Email'];
			$SMTP_login=$dados->db_data['SMTP_login'];
			$SMTP_pass=$dados->db_data['SMTP_pass'];
			$EmailToReply=$dados->db_data['EmailToReply'];
			$cabecalhoToReply=$dados->db_data['cabecalhoToReply'];
			$Template=$dados->db_data['Template'];
			$Titulo=$dados->db_data['Titulo'];
		}
		$dados->db_close();
	}
	if($cabecalho=='' && $texto=='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$config= new DataObj;
		$sql_config="select * from nl_config where ID=1";
		$hRes_config= $config->db_open($db_link,$DB_name,$sql_config);
		if(!$config->EOF())
		{
			$cabecalho=$config->db_data['cabecalho'];
			$SMTP=$config->db_data['SMTP'];
			$SMTP_Email=$config->db_data['SMTP_Email'];
			$SMTP_login=$config->db_data['SMTP_login'];
			$SMTP_pass=$config->db_data['SMTP_pass'];
			$emails_vez=$config->db_data['emails_vez'];
			$EmailToReply=$config->db_data['EmailToReply'];
			$cabecalhoToReply=$config->db_data['cabecalhoToReply'];
		}
		$config->db_close();
	}?>
	<form name="form" method="post" action="newsletter_trata.php?dump=<?=uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<?=$admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<input type="hidden" name="ID" value="<?=$ID?>" />
	<input type="hidden" name="task_form" value="<?=$task_form?>" />
	<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
	<script type="text/javascript" src="jscolor/jscolor.js"></script>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="5">
	<tr>
	    <td width="3" height="10"></td>
	    <td width="138" align="left" valign="top"></td>
	    <td align="left" valign="top" width="578"></td>
	    <td width="6"></td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Nome interno:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalho" maxlength="255" value="<?=$cabecalho;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP server:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP" maxlength="255" value="<?=$SMTP;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP Email:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_Email" maxlength="255" value="<?=$SMTP_Email;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP login:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_login" maxlength="255" value="<?=$SMTP_login;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">SMTP pass:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="SMTP_pass" maxlength="255" value="<?=$SMTP_pass;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Enviar e-mail para responder:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="EmailToReply" maxlength="255" value="<?=$EmailToReply;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Cabeçalho para responder:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="cabecalhoToReply" maxlength="255" value="<?=$cabecalhoToReply;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
	    <td height="25" width="3">&nbsp;</td>
	    <td class="preto" align="left" valign="top" width="138">Assunto:</td>
	    <td align="left" valign="top" width="578"> <input class="form-control" name="assunto" maxlength="255" value="<?=$assunto;?>"></td>
	    <td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="top" width="138">Título da newsletter:</td>
		<td align="left" valign="top" width="578"> <input class="form-control" name="n_titulo" maxlength="255" value="<?=$Titulo;?>"></td>
		<td width="6">&nbsp;</td>
	</tr>
	</table>

	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>

	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td align="center" valign="middle" class="t1">
		<input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button?> ">
		&nbsp;<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onClick="window.open('list.php','_self')">
		&nbsp;<input class="btn btn-primary" type="button" name="pagina" value=" Pré-visualizar newsletter " onClick="window.open('../../show_newsletter.php?ID=<?=$ID?>&amp;prev=1','_blank')">
		</td>
	</tr>
	</table>
	<input type="hidden" name="TEMPLATE" value="1" />
</form>
<? if($Template && $task_form == "altera"){ ?>
<form name="form" method="post" action="newsletter_template.php?ID=<?=$ID?>&amp;Template=1&amp;dump=<?=uniqID("")?>" enctype="multipart/form-data">
	<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
	<input type="hidden" name="TEMPLATE" value="1">
	<input type="hidden" name="task_form" value="<?=$task_form?>" />
	<input type="hidden" name="slashes" value="'">
	<input type="hidden" name="ID" value="<?=$ID?>">
	<input type="hidden" name="task2" value="<?=$task2?>">
	<table width="100%" class=p2 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td align="center" valign="middle" >CONSTRUÇÃO DA NEWSLETTER</td>
	</tr>
	</table>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 >
	<tr>
		<td bgcolor="#FFF"><?
	if($task2 == "editar" && $id_linha > 0)
	{
		?>
		<input type="hidden" name="id_linha" value="<?=$id_linha?>" />
		<?
		$rs=new DataObj;
		$sql='SELECT * FROM nl_newsletter_template WHERE ID_ORG="'.$ID.'" and ID="'.$id_linha.'" order by Ordem ASC';
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF())
		{
			$ID=$rs->db_data['ID'];
			$ID_ORG=$rs->db_data['ID_ORG'];
			$Tipo=$rs->db_data['Tipo'];
			$campo1=$rs->db_data['Texto1'];
			$campo2=$rs->db_data['Texto2'];
			$campo3=$rs->db_data['Texto3'];
			$Cor=$rs->db_data['Cor1'];
			$Cor2=$rs->db_data['Cor2'];
			$Cor3=$rs->db_data['Cor3'];
			$Bg=$rs->db_data['Bg1'];
			$Bg2=$rs->db_data['Bg2'];
			$Bg3=$rs->db_data['Bg3'];
			$I1=$rs->db_data['img_ext'];
			$I2=$rs->db_data['img2_ext'];
			$I3=$rs->db_data['img3_ext'];
			$I4=$rs->db_data['img4_ext'];
			$ordem=$rs->db_data['Ordem'];
			$link=$rs->db_data['Link1'];
			$link2=$rs->db_data['Link2'];
			$link3=$rs->db_data['Link3'];
			$align=$rs->db_data['Align1'];
			$align2=$rs->db_data['Align2'];
			$align3=$rs->db_data['Align3'];
			$valign=$rs->db_data['VAlign1'];
			$valign2=$rs->db_data['VAlign2'];
			$valign3=$rs->db_data['VAlign3'];
			$font=$rs->db_data['Font1'];
			$font2=$rs->db_data['Font2'];
			$font3=$rs->db_data['Font3'];
			$campos=array('ID' => $ID, 'ID_ORG' => $ID_ORG, 'Tipo' => $Tipo, 'Ordem' => $ordem, 'campo1' => $campo1, 'campo2' => $campo2, 'campo3' => $campo3 , 'Cor' => $Cor, 'Cor2' => $Cor2, 'Cor3' => $Cor3, 'Bg' => $Bg, 'Bg2' => $Bg2, 'Bg3' => $Bg3, 'I1' => $I1, 'I2' => $I2, 'I3' => $I3, 'link' => $link, 'link2' => $link2, 'link3' => $link3, 'align' => $align, 'align2' => $align2, 'align3' => $align3, 'valign' => $valign, 'valign2' => $valign2, 'valign3' => $valign3, 'font' => $font, 'font2' => $font2, 'font3' => $font3);
			?>
			<input type="hidden" name="grava_tipo" value="<?=$Tipo?>" />
			<?
            generate_form($campos, $task2, 'Alterar Bloco');
		}
		$rs->db_close();
	}
    if(!$show_tipo)echo getNewsleterPreview($ID, 'edit',701);
	if($show_tipo)
	{
		$task2='insere';
		$campos=array('ID' => 0, 'ID_ORG' => $ID_ORG, 'Tipo' => $template_options, 'Ordem' => 0, 'campo1' => '', 'campo2' => '', 'campo3' => '', 'Cor' => '', 'Cor2' => '', 'Cor3' => '', 'Bg' => '', 'Bg2' => '', 'Bg3' => '', 'I1' => '', 'I2' => '', 'I3' => '','link' => '', 'link2' => '', 'link3' => '', 'align' => '', 'align2' => '', 'align3' => '', 'valign' => '', 'valign2' => '', 'valign3' => '', 'font' => '', 'font2' => '', 'font3' => '');
		generate_form($campos, $task2, 'Inserir Bloco');?>
		<input type="hidden" name="grava_tipo" value="<?=$template_options?>" /><?
	}
	if(!$template_options && $task2 != "editar")
	{
		?>
		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td width="5px"></td>
				<td align="left" valign="middle" class="t1">
					<input type="hidden" name="show_tipo" value="1" />
					<select name="template_options">
					<? $z=1; while($z<=count($arr_opt)) {?>
					<option value="<?=$z?>"><?=$arr_opt[$z]?></option>
					<?  $z++; }?>
					</select>
					&nbsp;
					<input class="btn btn-primary" type="submit" name="Submit" value=" Criar Novo Bloco ">
					&nbsp;<input class="btn btn-secondary" type="button" name="cancelar" value=" Voltar à Listagem " onClick="window.open('list.php','_self')">
					<input class="btn btn-primary" style="float:right;" type="button" name="Gerar Template" value=" Gerar Template " onclick="if(confirm('Tem a certeza que pretende gerar nova template? Todas as alterações efetuadas no editor serão perdidas!')){window.open('<?=$site_http_url?>admin/newsletter/gerar_html.php?ID=<?=$ID?>','_self');}">
				</td>
			</tr>
		</table>
		<?
	}
	?>
		</tr>
		</table>
		<br/>
	</td>
</form>
<? } ?>
</td>
<td width="10px">&nbsp;</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>
<? 	include "../layout/bottom.inc.php";?>