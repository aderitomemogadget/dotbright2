<?php
	include "settings.inc.php";
	global $DB_host,$db_link,$DB_name,$DB_user,$DB_pass;
	if(trim($Nivel)=='') $Nivel=0;
	if(trim($total_prods=='')) $total_prods=30;
	if(trim($from_prods=='')) $from_prods=0;
	if(trim($to_prods=='')) $to_prods=$total_prods;
	$limite_from=$from_prods;
	$limite_to=$to_prods;

	/*******************************************************/
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($GLOBALS['update_db']==1)
	{
		$fields=db_list_fields($DB_name,'products_objs',$db_link);
		$columns=db_num_fields($fields);
		if($columns>0)
		{
			$field_array=array();
			for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
			if(!in_array('Promo_data_ini',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD Promo_data_ini Date NOT NULL ');
			if(!in_array('Promo_data_fim',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD Promo_data_fim Date NOT NULL ');
			if(!in_array('Promo_descricao',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD Promo_descricao longtext NOT NULL ');
		}
		$fields=db_list_fields($DB_name,'products_objs',$db_link);
		$columns=db_num_fields($fields);
		if($columns>0)
		{
			$field_array=array();
			for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
			if(!in_array('Promo_data_ini',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD Promo_data_ini Date NOT NULL ');
			if(!in_array('Promo_data_fim',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD Promo_data_fim Date NOT NULL ');
		}
	}
	/*******************************************************/

	/****************************************/
	/* Garantir que não á produto se destaque_homepage=0 */
	if($GLOBALS['destaque_homepage']==0)
	{
		$sql = 'update products set tem_prod_destaque_homepage=0';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	/****************************************/

	if($task && $ID_PROD>0)
	{
		switch ($task)
		{
			case "apaga":
				$res=Runquery($db_link,$DB_name,"UPDATE products SET Home=0 WHERE products.ID='$ID_PROD'");
				if(!$res)echo "<br>".db_error($db_link);
			break;
			case "inserir":
				$res=Runquery($db_link,$DB_name,"UPDATE products SET Home=1,Ordem=$Ordem WHERE products.ID='$ID_PROD'");
				if(!$res) echo "<br>".db_error($db_link);
			break;
		}
	}
	if(isset($_POST['post_fam']) && intval($_POST['post_fam'])==1)
	{
		$res=Runquery($db_link,$DB_name,'TRUNCATE TABLE produtos_regras1');
		if($res)
		{
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.intval($_POST['fam1']).'","'.intval($_POST['ordem1']).'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.intval($_POST['fam2']).'","'.intval($_POST['ordem2']).'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.intval($_POST['fam3']).'","'.intval($_POST['ordem3']).'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.intval($_POST['fam4']).'","'.intval($_POST['ordem4']).'")');
		}
	}
	if(isset($_POST['flag2']) && intval($_POST['flag2']) > 0)
	{
		require_once 'filtros/funcoes.inc.php';
		$rs3 = new DataObj;
		foreach($_POST as $key => $valor)
		{
		    if((strcmp(substr($key,0,4),"chk_")==0))
			{
				$id = intval($valor);
				$id_fam = intval($_POST['m_familia_'.$valor]);
				if($id_fam > 0)
				{
					$fams = getCategoriesRecursive($id_fam);
					if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $id_fam;
					if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
					if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
					if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

					$ID_Marca = 0;
					$sql = 'SELECT ID_Marca FROM products WHERE ID="'.$id.'"';
					$rs3->db_open($db_link,$DB_name,$sql);
					if(!$rs3->EOF())$ID_Marca=intval($rs3->db_data['ID_Marca']);
					$rs3->db_close();

					$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
					if($rs3->EOF())
					{
						$sql = 'INSERT INTO filters_products(cat_id,valor_id,prod_id,fam_id1,fam_id2,fam_id3,fam_id4,marca_id,filtros_rel2,FLAG,filtro_active)
								VALUES("0","0","'.$id.'","'.$fam_id1.'","'.$fam_id2.'","'.$fam_id3.'","'.$fam_id4.'","'.$ID_Marca.'","",1,1)';
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					}
					else
					{
						$sql = 'UPDATE filters_products SET fam_id1="'.$fam_id1.'",fam_id2="'.$fam_id2.'",fam_id3="'.$fam_id3.'",fam_id4="'.$fam_id4.'",marca_id="'.$ID_Marca.'" where prod_id="'.$id.'"';
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					}
					$rs3->db_close();

					if(isset($_POST['flag2']) && $_POST['flag2'] == 4)  $sql = "UPDATE products SET ID_ORG=".$id_fam.", Aproved=1 WHERE ID=".$id;
                    else $sql = "UPDATE products SET ID_ORG=".$id_fam." WHERE ID=".$id;

					$res = Runquery($db_link,$DB_name,$sql);
					$sql = "DELETE FROM products_families_obj WHERE ID_PROD=".$id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = "INSERT INTO products_families_obj(ID_ORG,ID_PROD) VALUES ('".$id_fam."','".$id."')";
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = "UPDATE products_objs SET products_objs='products' WHERE obj_type_id='".$id."'";
					$res = Runquery($db_link,$DB_name,$sql);

					if(isset($_POST['flag2']) && $_POST['flag2'] == 4) gera_produtos($id, $id_fam, $ID_Marca, 1, 'pt');
				}
			}
		}
	}
	db_closedb_conection($db_link);
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script>
function allFams(v){$('.fams').hide('');if(v > 0){$.get('allfams.php?val='+v+'&dp=<? echo date("YmdHis")?>', function(data) {  $('#fams'+v).show("slow"); $('#fams'+v).html(data);});}}
function totext2(v, txt, txt2){if(v > 0){$('#fam'+v).val(txt);$('#fam_'+v).html(txt2);}$('.fams').hide('');}
</script>
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
    <td valign="top" align="left"><?
		show_page_tools(filter);?>
        <form action="reordena.php" name="lista_produtos" method="post"><?
		show_content_list();
        show_page_bottom_tools();?>
        </form>
        <?php  show_products_without_categorie();?>
    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>