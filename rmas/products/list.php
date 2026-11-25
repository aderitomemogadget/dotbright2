<?
	include "settings.inc.php";
	global $DB_host,$db_link,$DB_name,$DB_user,$DB_pass;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(trim($Nivel)=='') $Nivel=0;
	if(trim($total_prods=='')) $total_prods=30;
	if(trim($from_prods=='')) $from_prods=0;
	if(trim($to_prods=='')) $to_prods=$total_prods;
	$limite_from=$from_prods;
	$limite_to=$to_prods;
	if($task && $ID_PROD)
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
	if(isset($_POST['post_fam']) && $_POST['post_fam']==1)
	{
		$res=Runquery($db_link,$DB_name,'TRUNCATE TABLE produtos_regras1');
		if($res)
		{
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.$_POST['fam1'].'","'.$_POST['ordem1'].'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.$_POST['fam2'].'","'.$_POST['ordem2'].'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.$_POST['fam3'].'","'.$_POST['ordem3'].'")');
			$res=Runquery($db_link,$DB_name,'INSERT INTO produtos_regras1(ID_ORG,Ordem) VALUES("'.$_POST['fam4'].'","'.$_POST['ordem4'].'")');
		}
	}
	
	// print_r($_POST);
	
	if(isset($_POST['m_familia2']) && $_POST['m_familia2'] > 0)
	{
		require_once 'filtros/funcoes.inc.php';
		
			$rs3 = new DataObj;
			foreach($_POST as $key => $valor)
			{
				if((strcmp(substr($key,0,4),"chk_")==0))
				{
					$id = $valor;
					if(isset($_POST['flag2']) && $_POST['flag2'] == 4)  $sql = "UPDATE products SET ID_ORG=".$_POST['m_familia2'].", Aproved=1 WHERE ID=".$id;
					else $sql = "UPDATE products SET ID_ORG=".$_POST['m_familia2']." WHERE ID=".$id;
					$res = Runquery($db_link,$DB_name,$sql);				
					$sql = "DELETE FROM products_families_obj WHERE ID_PROD=".$id;
					$res = Runquery($db_link,$DB_name,$sql);
					$sql = "INSERT INTO products_families_obj(ID_ORG,ID_PROD) VALUES ('".$_POST['m_familia2']."','".$id."')";
					$res = Runquery($db_link,$DB_name,$sql);					
					$sql = "UPDATE products_objs SET obj_type='products' WHERE obj_type_id='".$id."'";
					$res = Runquery($db_link,$DB_name,$sql);		
					
					
					$fams = getCategoriesRecursive($_POST['m_familia2']);
					if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $_POST['m_familia2'];
					if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
					if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
					if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;
					
					$sql = 'SELECT ID_Marca FROM products WHERE ID="'.$id.'"';
					$rs3->db_open($db_link,$DB_name,$sql);
					$ID_Marca = $rs3->db_data['ID_Marca'];
					$rs3->db_close();
					
					$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$id.'"');
					if($rs3->EOF())
					{			
				
						$sql = '
								INSERT INTO filters_products
								(
									cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, fam_id4, marca_id, filtros_rel2, FLAG, filtro_active
								)
								VALUES
								(
									"0", "0", "'.$id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$fam_id4.'", "'.$ID_Marca.'", "", 1, 1
								)
							';
				
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);				
					}
					else
					{
						$sql = 'UPDATE filters_products SET fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id3="'.$fam_id3.'", fam_id4="'.$fam_id4.'", marca_id="'.$ID_Marca.'" where prod_id = "'.$id.'"';
						$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
					}
					$rs3->db_close();
					if(isset($_POST['flag2']) && $_POST['flag2'] == 4) gera_produtos($id, $_POST['m_familia2'], $ID_Marca, 1, 'pt');
					// necessario por causa das imagens
					if(isset($_POST['flag2']) && $_POST['flag2'] == 4) gera_produtos($id, $_POST['m_familia2'], $ID_Marca, 1, 'pt');
					
					if(!$res)  echo db_error($db_link);
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
        include "../layout/menus.inc.php";
		show_page_tools('filter');?>
        <form action="reordena.php" name="lista_produtos" method="post"><?
		show_content_list();
        show_page_bottom_tools();?>
        </form>
        <? show_products_without_categorie();?>
    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top"><? show_fast_tools();?></td>
</tr>
</table>
<? 	include "../layout/bottom.inc.php";?>