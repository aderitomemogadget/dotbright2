<?php
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($category_id==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'filters_categories',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('category_active2',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories ADD category_active2 tinyint(2) NOT NULL');

	$fields=db_list_fields($DB_name,'filters_categories_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('category_name2',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE filters_categories_lang ADD category_name2 varchar(255) NOT NULL');

	if(isset($_GET['category_id']) && $_GET['category_id'] > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;
		$sql = '
				SELECT
					f.*, fl.category_name , fl.category_name2
				FROM
					filters_categories AS f
					INNER JOIN filters_categories_lang AS fl ON f.category_id=fl.category_id
				WHERE
					f.category_id='.$_GET['category_id'].' AND fl.category_lang="'.$admin_lang.'"
				';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$category_id 				= $rs->db_data["category_id"];
			$category_type	 			= $rs->db_data['category_type'];
			$category_order 			= $rs->db_data['category_order'];
			$category_name 				= $rs->db_data['category_name'];
			$category_name2 				= $rs->db_data['category_name2'];
			$category_active 			= $rs->db_data['category_active'];
			$category_active2 			= $rs->db_data['category_active2'];
		}
		$rs->db_close(1);
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
	<div class="center">
	<form name="form" method="post" action="pagina_trata.php?category_id=<?php echo $category_id?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" >
	<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left"><?php include "../layout/menus.inc.php"; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">FILTROS</h4>
        </div>
    </div>
	<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
	<td valign="top">
	<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
	<tr>
	<td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de Filtros</a></td>
	</tr>
	</table>
	</tr>
	<tr>
	<td align="left" valign="top" class="preto">
	<table width="100%" cellpadding=5 cellspacing=0 border=0 bgcolor="#C9EAFD">

	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Ordem:&nbsp;</td>
	<td class="new_preto"><input class="form-control" type="text" name="category_order" id="category_order" value="<?php echo $category_order;?>" class="new_input" /></td>
	</tr>
	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Nome:&nbsp;</td>
	<td class="new_preto"><input class="form-control" type="text" name="category_name" id="category_name" value="<?php echo $category_name;?>" class="new_input" /></td>
	</tr>

	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Nome Alternativo:&nbsp;</td>
	<td class="new_preto"><input class="form-control" type="text" name="category_name2" id="category_nam2e" value="<?php echo $category_name2;?>" class="new_input" /></td>
	</tr>

	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Tipo:&nbsp;</td>
	<td class="new_preto">
		<input type="radio" name="category_type" value="0" <?php if($category_type==0){ echo ' checked="checked" ';}?>class="new_input" /> Lista (normal)<br />
		<input type="radio" name="category_type" value="1" <?php if($category_type==1){ echo ' checked="checked" ';}?>class="new_input" /> Range<br />
		<input type="radio" name="category_type" value="2" <?php if($category_type==2){ echo ' checked="checked" ';}?>class="new_input" /> Color Pallete<br />
		<input type="radio" name="category_type" value="3" <?php if($category_type==3){ echo ' checked="checked" ';}?>class="new_input" /> Instruções Lavagem<br />
	</td>
	</tr>

	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Ativo:&nbsp;</td>
	<td class="new_preto"><input type="checkbox" name="category_active" id="category_active" value="1" class="new_input" <?php if($category_active==1){ echo 'checked="checked"';}?> /></td>
	</tr>

	<tr>
	<td width="150" class="new_preto">&nbsp;&nbsp;Visivel no menu:&nbsp;</td>
	<td class="new_preto"><input type="checkbox" name="category_active2" id="category_active2" value="1" class="new_input" <?php if($category_active2==1){ echo 'checked="checked"';}?> /></td>
	</tr>

	</table>
	</td>
	</tr>

	</table>
	<?php show_page_buttons();?>

	<?php if($category_id > 0){?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
    	<td width=29 align="center">&nbsp;</td>
		<td width="10px" align="center">&nbsp;</td>
	    <td align="right" class="branco"><a class="btn btn-primary" href="pagina_values.php?value_id=0&category_id=<?php echo $category_id;?>" class="branco">Inserir novo valor para <?php echo $category_name;?></a></td>
    	<td width="5"></td>
    </tr>
    </table>
	<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
	<tr>
	<td valign="top" align="left">
	<?php show_content_list2($category_id,-5,0,0,0,$category_type);?>
	</td>
	</tr>
	</table>
	<?php } ?>
	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200"><?php show_fast_tools();?></td>
	</tr>
	</table>
	</form>


	</div>
<?php include "../layout/bottom.inc.php";?>