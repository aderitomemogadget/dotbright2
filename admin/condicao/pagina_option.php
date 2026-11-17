<?
	include "settings.inc.php";
	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID == 0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';}
	$D = getdate();

	if($ID!=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

		$fields=db_list_fields($DB_name,'products_components_options',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}


		$rs=new DataObj;
		$sql = "Select * from products_components_options WHERE ID=$ID";
		$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$visivel=$rs->db_data["Visivel"];
			$ordem=$rs->db_data['Ordem'];
			$img_ext=$rs->db_data["Img_ext"];

			$rs2=new DataObj;
			$sql="Select Nome, Descricao from products_components_options_lang where ID_PAI=$ID and Lang='$admin_lang' order by ID Desc";
			$rs2->db_open($db_link,$DB_name,$sql);
			$nome = stripslashes($rs2->db_data["Nome"]);
			$descricao = stripslashes($rs2->db_data["Descricao"]);
			$rs2->db_close();

		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script type="text/javascript">
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display: none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function select_page() {
	var editdoc=document.form.url_link;
	var newurl=showModalDialog("listcontent.php", "", "resizable: no; help: no; status: no; scroll: yes;");
	if(newurl) editdoc.value=newurl;
	}
</script>
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
<div class="center">
<form name="form" method="post" action="pagina_option_trata.php?task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="ID" value="<? echo $ID;?>" />
<input type="hidden" name="ID_ORG" value="<? echo $ID_ORG;?>" />
<input type="hidden" name="m_lang" value="<? echo $GLOBALS['admin_lang'];?>" />
<input type="hidden" name="slashes" value="'" />

<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
		<td valign="top">

			<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
				<tr>
					<td valign="middle"><a href="pagina.php?ID=<? echo $ID_ORG;?>" class="branco">« Voltar à listagem de opções</a></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td align="left" valign="top" class=preto>


			<table width="100%" cellpadding=0 cellspacing=0 border=0 style="background:#FFF">
				<tr >
					<td width="5" height="30">&nbsp;</td>
					<td width="200" class="new_preto">Visivel:&nbsp;</td>
					<td><input type="checkbox" name="visivel" value="1" <? if($visivel==1){ echo 'checked="checked"';}?> /></td>
					<td width="5" height="30">&nbsp;</td>
				</tr>
				<tr><td style="background:#87ceee " height="10" colspan="10"><br></td></tr>
				<tr >
					<td width="5" height="30">&nbsp;</td>
					<td width="200" class="new_preto">Ordem:&nbsp;</td>
					<td><input type="text" name="ordem" style="WIDTH: 50px" maxlength="50" value="<? echo $ordem;?>" class="box" /></td>
					<td width="5" height="30">&nbsp;</td>
				</tr>
				<tr><td style="background:#87ceee " height="10" colspan="10"><br></td></tr>
				<tr >
					<td width="5" height="30">&nbsp;</td>
					<td width="200" class="new_preto">Nome:&nbsp;</td>
					<td><input type="text" name="nome" style="WIDTH: 300px" maxlength="50" value="<? echo $nome;?>" class="box" /></td>
					<td width="5" height="30">&nbsp;</td>
				</tr>
				<tr><td style="background:#87ceee " height="10" colspan="10"><br></td></tr>
				<tr >
					<td width="5" height="30">&nbsp;</td>
					<td width="200" class="new_preto">Descricao:&nbsp;</td>
					<td><input type="text" name="descricao" style="WIDTH: 300px" maxlength="50" value="<? echo $descricao;?>" class="box" /></td>
					<td width="5" height="30">&nbsp;</td>
				</tr>
				<tr><td style="background:#87ceee " height="10" colspan="10"><br></td></tr>
				<tr>
					<td width="5" height="30"></td>
					<td width="200" class="new_preto"><?php echo write_lang('key_imagem','Imagem');?>:&nbsp;</td>
					<td><div style="overflow-y:hidden;max-width:600px"><?
					if($img_ext!="" && file_exists("../../files/products_components/".$ID."_1.".$img_ext))
					{
						if(file_exists("../../files/products_components/$ID"."_1.".$img_ext)){?><a target="_new" href="<? echo "../../files/products_components/".$ID."_1.".$img_ext;?>" style="color:#000"><img src="<? echo "../../files/products_components/".$ID."_1.".$img_ext;?>" height="48"></a><br><? }?><?
						if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
					}
					else{ echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><?php }?></td>
					<td width="5" height="30">&nbsp;</td>
				</tr>
			</table>


		</td>
	</tr>

</table>
<?php show_page_buttons();?>

<br>

</td>
<td width="10px">&nbsp;</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>






</table>
</form>



</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>

<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script><br /><br />
<? include "../layout/bottom.inc.php";?>
