<?
	include "settings.inc.php";
	if($task=="apagar"){$task_button = write_lang('key_apagar_este_registo','Apagar este Registo');$task_form="apaga";}
	elseif($task=="duplicar"){$task_button = write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form="duplicar";}
	elseif($value_id==0){$task_button = write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form="insere";}
	else {$task_button = write_lang('key_gravar_alteracoes','Gravar alterações');$task_form="altera";}
	$D=getdate();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$fields=db_list_fields($DB_name,'filters_values_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('values_name2',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE filters_values_lang ADD values_name2 varchar(255) NOT NULL');


	$rs = new DataObj;
	if(isset($_GET['value_id']) && $_GET['value_id'] > 0)
	{
		$sql = 'SELECT f.*, fl.values_name, fl.values_name2, fl.values_colorhex FROM filters_values AS f INNER JOIN filters_values_lang AS fl ON f.value_id=fl.values_id
		WHERE f.value_id='.$_GET['value_id'].' AND fl.values_lang="'.$admin_lang.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$value_id 					= $rs->db_data["value_id"];
			$value_id_import_ref = $rs->db_data["value_id_import_ref"];
			$category_id 				= $rs->db_data["category_id"];
			$value_order 				= $rs->db_data['value_order'];
			$values_name 				= $rs->db_data['values_name'];
			$values_name2 				= $rs->db_data['values_name2'];
			$values_colorhex 			= $rs->db_data['values_colorhex'];
			$value_active 				= $rs->db_data['value_active'];
			$I1 						= $rs->db_data["value_ext"];
		}
		$rs->db_close();
	}
	$sql = 'SELECT f.category_type FROM filters_categories AS f WHERE f.category_id='.$_GET['category_id'].'';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())$category_type = $rs->db_data["category_type"];
	$rs->db_close();
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	?>
	<div class="center">
	<form name="form" method="post"  enctype="multipart/form-data" action="pagina_values_trata.php?value_id=<? echo $value_id?>&category_id=<? echo $category_id?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>" >
	<input type="hidden" name="m_lang" value="<? echo $admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
	<tr>
	<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
        <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
        <tr>
			<td valign="top">
                <table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
                <tr>
                    <td valign="middle"><a href="list.php" class="branco"> &#60; Voltar para a lista de Filtros</a></td>
                </tr>
                </table>
			</td>
		</tr>
	<tr>
		<td align="left" valign="top" class="new_preto">
			<table width="100%" cellpadding=5 cellspacing=0 border=0 bgcolor="#C9EAFD">

			<tr>
				<td width="200" class="new_preto">&nbsp;&nbsp;Categoria:&nbsp;</td>
				<td class="new_preto"><? gera_lista_categorias($category_id);?></td>
			</tr>

			<tr>
				<td width="200" class="new_preto">&nbsp;&nbsp;Código:&nbsp;</td>
				<td class="new_preto"><input type="text" name="value_id_import_ref" id="value_id_import_ref"  value="<? echo $value_id_import_ref;?>" class="form-control" /></td>
			</tr>

			<tr>
				<td width="200" class="new_preto">&nbsp;&nbsp;Ordem:&nbsp;</td>
				<td class="new_preto"><input type="text" name="value_order" id="value_order"  value="<? echo $value_order;?>" class="form-control" /></td>
			</tr>

			<tr>
                <td width="200" class="new_preto">&nbsp;&nbsp;Nome:&nbsp;</td>
                <td class="new_preto"><input type="text" name="values_name" id="values_name" value="<? echo $values_name;?>" class="form-control" /></td>
			</tr>

			<tr>
                <td width="200" class="new_preto">&nbsp;&nbsp;Nome Alternativo:&nbsp;</td>
                <td class="new_preto"><input type="text" name="values_name2" id="values_name2" value="<? echo $values_name2;?>" class="form-control" /></td>
			</tr>

            <? if($category_type==2){ ?>
			<tr>
                <td width="200" class="new_preto">&nbsp;&nbsp;Color HEX:&nbsp;</td>
                <td class="new_preto"><input type="text" name="values_colorhex" id="values_colorhex" value="<? echo $values_colorhex;?>" class="form-control" /></td>
			</tr>

			<? } ?>
			<tr>
				<td width="200" class="new_preto">&nbsp;&nbsp;Ativo:&nbsp;</td>
				<td class="new_preto"><input type="checkbox" name="value_active" id="value_active" value="1" <? if($value_active==1){ echo 'checked="checked"';}?> /></td>
			</tr>

			<tr>
			<td class="new_preto" align="left" valign="top" width="150">
				Imagem:<br>
				<table width="100%" bgcolor="#cccccc" border=0 cellspacing=0 cellpadding=0>
				<tr><td><font style="font-family: verdana; font-size: 9px"><? echo write_lang('key_resolucao','Resolução');?> - <i>72 DPIs</i></font></td></tr>
				</table>
			</td>
			<td class="new_preto" align="left" valign="top"><?
			if($I1!="")
			{
				?><img src="<?php echo "../../files/filters/".$value_id."_1.".$I1;?>"><br><?
				if($task!="apagar"){ ?><? echo write_lang('key_apagar','Apagar');?> <input type="checkbox" name="Apagar_img1" value="1"><br><? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?>: <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" /><? }
			}
			else
			{?><? echo write_lang('key_nova','Nova');?>: <input type="file" name="img_peq1"><input type="hidden" name="tem_foto1" value="0"><? } ?>
			</td>
		</tr>

			</table>
		</td>
	</tr>
	</table>
	<? show_page_buttons();?>

    <?php

		define('N_PAGINAS', '20');

		$sql="
		SELECT
			p1.ID, p1.Ref as Referencia, p1l.Cabecalho, p1.Aproved
		FROM
			products p1
			INNER JOIN products_lang p1l ON p1.ID=p1l.ID_PAI
			INNER JOIN filters_products pf on p1.ID=pf.prod_id
		WHERE
			pf.cat_id=".$category_id." AND pf.valor_id=".$value_id."
		GROUP BY
			p1.ID
		ORDER BY
			p1.Aproved
		";

		$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);


		if(!$rs->EOF())
		{
			?><br>
				<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p2">
				<tr>
					<td width="30" height="35" align="center">&nbsp;</td>
					<td width="50" valign="middle" align="center"><b>Ref</b></td>
					<td width="40" valign="middle">&nbsp;</td>
					<td valign="middle" align="left"><b>Nome Produto</b></td>
					<td valign="middle" align="left"><b>Ativo</b></td>
				</tr>
			<?php
			$count=0;
			while(!$rs->EOF())
			{
				// echo '<a href="../products/products.php?ID='.$rs->db_data["ID"].'" class="preto">'.$rs->db_data["Referencia"].' - '.$rs->db_data["Cabecalho"].'</a><br>';
				$count++;
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#fff";$class="r2";$n="2";}

				?>
				<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">

				<td align="center">&nbsp;</td>
				<td valign="middle" align="center"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="../products/products.php?ID=<?php echo $rs->db_data["ID"]?>"><?php echo $rs->db_data["Referencia"]; ?></a></td>
				<td valign="middle"><div style="background:#<?php echo str_replace('#', '', $Cor)?>;width:20px;height:20px">&nbsp;</div></td>
				<td valign="middle" align="left"><a title="<?php echo write_lang('key_editar_pagina','Editar Página')?>" class="lista_links" href="../products/products.php?ID=<?php echo $rs->db_data["ID"]?>"><?php echo $rs->db_data["Cabecalho"]; ?></a></td>
				<td valign="middle" align="left"><?php echo ($rs->db_data["Aproved"]==1 ? 'Ativo' : ''); ?></td>
				</tr>
				<?php
				$rs->db_next();
			}
			?>
			</table>

            <?php
		}
		$rs->db_close();
		if($n_pags > 1)
		{

			$url_pages = 'pagina_values.php?value_id='.$_GET['value_id'].'&category_id='.$_GET['category_id'].'';

			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}

		?>



	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>
	</form>
	</div>
<? include "../layout/bottom.inc.php";?>