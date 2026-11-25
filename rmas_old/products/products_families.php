<?php
	include "settings.inc.php";
	
	if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}
	
	// CONFIGURACAO EM SETTINGS
	if (defined('FAM_CONF')) $fam_conf = unserialize(FAM_CONF); else $fam_conf = array();
	
	if(trim($Nivel) == '') $Nivel = 0;
	if(trim($total_prods=='')) $total_prods=200;
	if(trim($from_prods=='')) $from_prods=0;
	if(trim($to_prods=='')) $to_prods=$total_prods;
	$limite_from = $from_prods;
	$limite_to = $to_prods;
	
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$n_imagens_familia;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj; $rs2=new DataObj;
	
	$fields = db_list_fields($DB_name, 'products_families', $db_link);
	$columns = db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('preco_revenoff', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD preco_revenoff tinyint(1) DEFAULT 0');  }
	if(!in_array('Margem', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD Margem decimal(10,4) DEFAULT 0');  }
	if(!in_array('MargemTipo', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD MargemTipo tinyint(1) DEFAULT 0');}
	if(!in_array('Portes', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD Portes decimal(10,4) DEFAULT 0');}
	if(!in_array('points', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_families ADD points varchar(250)');}

	// MOVER
	if($_POST['flag2']==3)
	{
		foreach($_POST as $varName=> $value) 
		{ 
			$dv = $value;
			$str_id = substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql = "UPDATE products SET ID_ORG='$m_familia2' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$sql="DELETE FROM products_families_obj WHERE ID_PROD=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';

				$sql='INSERT INTO products_families_obj(ID_PROD,ID_ORG) values ("'.$str_id.'","'.$m_familia2.'")';
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		} 
	}

	if(isset($fam_conf) && $fam_conf['tem_caract'] == 1){?>
	<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
	<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
	<?php }?>
<link rel="stylesheet" type="text/css" href="avancada/chosen2.css" />
<link rel="stylesheet" type="text/css" href="avancada/sa.css" />
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
				//alert(v+" - "+obj);
				 if(ar[i].className=="submenu") ar[i].style.display="none";
			}
			
			for (var i=0; i<ar2.length; i++){
				v="sub"+(i+1);
				//alert(v+" - "+obj);
				 if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
			}
			el.style.display="block";
			el2.style.background="#000000";
		}
	}
}
window.onload =function(){<? if($t1 && $t2){?>showmenu_prod('<?php echo $t1?>','<?php echo $t2?>');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}
</script>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td align="left" valign="top"><? include "../layout/menus.inc.php"; ?>
        	
<?php
if(!$pesquisa && !$pesquisa2 && !$top_search)
{
	$C = '';$T = '';$I1 = '';$I2 = '';
	$ordem=0;
	if(isset($_GET['ID']) && $_GET['ID'] != 0)
	{
		$ID = $_GET['ID'];
		
		$rs->db_open($db_link, $DB_name, 'SELECT * FROM products_families WHERE ID='.$ID.' ORDER BY Ordem DESC');
		if(!$rs->EOF())
		{
			$D=str2date($rs->db_data["Data"]);
			$I1=$rs->db_data["img_ext"];
			$I2=$rs->db_data["img2_ext"];
			$I3=$rs->db_data["img3_ext"];
			$ordem=$rs->db_data["Ordem"];
			$id_org=$rs->db_data["ID_ORG"];
			$cor=$rs->db_data["cor"];
			$visivel=$rs->db_data["visivel"];
			$destaque=$rs->db_data["Destaque"];
			$margemcli=$rs->db_data["MargemCli"];
			$margemrev=$rs->db_data["MargemRev"];
			$cat1=$rs->db_data["cat1"];
			$cat2=$rs->db_data["cat2"];
			$cat3=$rs->db_data["cat3"];  
			$download=$rs->db_data["Download"];                             
			$ID_Media=$rs->db_data["ID_Media"]; 							
			$Desconto=number_format($rs->db_data["Desconto"],2);							
			$Margem=number_format($rs->db_data["Margem"],2);							
			$DescontoTipo=$rs->db_data["DescontoTipo"];					
			$MargemTipo=$rs->db_data["MargemTipo"];					
			$ID_Integracao=$rs->db_data["ID_Integracao"];					
			$preco_revenoff=$rs->db_data["preco_revenoff"];					
			$Portes=number_format($rs->db_data["Portes"],2);
			$pontos=$rs->db_data["points"];
		}
		$rs->db_close();	
		
		//********* linguas ***/
		$rs->db_open($db_link,$DB_name, "Select * from products_families_lang where ID_PAI=$ID and lang='$admin_lang'");
		$C=htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
		$C2=htmlentities_memo(stripslashes($rs->db_data["Cabecalho2"]));
		$T=stripslashes($rs->db_data["Texto"]);
		$URL1=$rs->db_data["URL"];
		$rs->db_close();
	}
	
	// SE EXISTIR id_org do query tem que ser passado para o select box
	if(isset($id_org) && $id_org > 0) $ID_ORG = $id_org;
	elseif($_GET['ID']==$_GET['ID_ORG']) $ID_ORG = $id_org;
	
?>
<form name="form" method="post" action="products_families_trata.php?prev=0&ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data" accept-charset="UTF-8">
<input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
<input type="hidden" name="slashes" value="'" />
<input type="hidden" name="tab_a" value="0" />
<input type="hidden" name="tab_b" value="0" />
<input name="Nivel" type="hidden" id="Nivel" value="<?php echo $Nivel;?>" />

<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
<tr> 
<td>
		
	<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
	<tr>
	<td><a href="list.php" class="new_caminho"><? echo write_lang('key_inicio','Ínicio')?></a> - <? show_products_menu_path($ID, "products_families.php", "products_families",$total_prods);?></td>
	</tr>
	</table>
	
	<div id="masterdiv">
		
		<br />
		
		<table height="20" border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer" id="pinta1"><? echo write_lang('key_categoria','CATEGORIA')?></td>	
		<?php 
		// IMAGEM DA FAMILIA
		if(isset($fam_conf) && $fam_conf['tem_imagens']){?>
		<td><img src="cm_fill.gif" width="8" height="1"></td>				
		<td width="220" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2"><? echo write_lang('key_imagens','IMAGENS')?></td>
		<?php }
		
		// CARACTERISTICAS DA FAMILIA
		if(isset($fam_conf) && $fam_conf['tem_caract']){?>
		<td><img src="cm_fill.gif" width="8" height="1"></td>
		<td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="cursor:pointer" id="pinta3"><? echo write_lang('key_caracteriticas','CARACTERÍSTICAS')?></td><td><img src="cm_fill.gif" width="8" height="1"></td>
		</tr>
		<?php }?>
		</table>
						
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub1">
		
		<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="#F6F6F6" class="border1">					
		<tr>
		<td>&nbsp;</td>
		<td></td>
		<td></td>
		<td>&nbsp;</td>
		</tr>
		
		<tr>
		<td>&nbsp;</td>
		<td>ATIVAR</td>
		<td align="left" class="preto"><input name="visivel" type="checkbox" value="1" <? if($visivel) echo ' checked="checked"'; ?> /> <? echo write_lang('key_tornar_categoria_visivel','Tornar categoria visível')?></td>
		<td>&nbsp;</td>
		</tr>
		
		<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
		<tr><td colspan="10">&nbsp;</td></tr>
		<tr>
		<td>&nbsp;</td>
		<td>REPARAÇÕES</td>
		<td align="left" class="preto"><input name="download" type="checkbox" value="1" <? if($download) echo "checked"; ?>> Família das reparações</td>
		<td>&nbsp;</td>
		</tr>
		
		<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
		<tr><td colspan="10">&nbsp;</td></tr>
		<tr>
		<td width="10">&nbsp;</td>
		<td class="preto" width="100"><? echo write_lang('key_ordem','Ordem')?>:</td>
		<td align="left" class="preto"><input class="box" name="ordem" style="width:80px;" maxlength="5" value="<?php echo $ordem;?>" /></td>
		<td width="4">&nbsp;</td>
		</tr>
						
		<tr>
		<td>&nbsp;</td>
		<td class="preto">Familia:</td>
		<td align="left"><?php gera_lista_familias($ID_ORG, 'products_families', 'products_families_lang');?></td>
		<td>&nbsp;</td>
		</tr>
			
		<?php 
		// NA LISTAGEM DA FAMILA APARECE O MEDIA
		if(isset($fam_conf) && $fam_conf['tem_banner'] == 1 && $ID_ORG==0){?>
		<tr>
		<td>&nbsp;</td>
		<td class="preto">Banner:</td>
		<td align="left"><?php gera_lista_medias($ID_Media,"media_type");?></td>
		<td>&nbsp;</td>
		</tr>
		<?php } ?>
						
		<tr>
		<td>&nbsp;</td>
		<td class="preto"><? echo write_lang('key_nome','Nome')?>:</td>
		<td align="left"><input class="box" type="text" name="cabecalho" style="width:100%;" maxlength="255" value="<?php echo $C;?>" /></td>
		<td>&nbsp;</td>
		</tr>					
		
		<?php 
		// NA INPORTAÇÃO É BLOQUEADO O TITULO E ESTE DEVE SER ATIVADO
		if(isset($fam_conf) && $fam_conf['tem_n_alterna'] == 1){?>
		<tr>
		<td>&nbsp;</td>
		<td class="preto"><? echo write_lang('key_nome','Nome')?> Alternativo:</td>
		<td align="left"><input class="box" type="text" name="cabecalho2" style="width:100%;" maxlength="255" value="<?php echo $C2;?>" /></td>
		<td>&nbsp;</td>
		</tr>
		<?php } 

		// NA INPORTAÇÃO EM VEZ DE TRAZER O PREÇO DE REVENDA TRAZ O PRECO FINAL
		if(isset($fam_conf) && $fam_conf['tem_p_c_final'] == 1){?>
		<tr> 
		<td>&nbsp;</td>
		<td></td>
		<td class="preto"><input type="checkbox" name="preco_revenoff" <?php if($preco_revenoff){?> checked="checked" <? }?> value="1" /> Buscar Preço Cliente Final</td>
		<td class="preto" align="left" valign="top"></td>
		</tr>
		<?php } 
		
		// DESTAQUES POR FAMILIAS NA HOMEPAGE
		//if(isset($fam_conf) && $fam_conf['tem_fams_destaque'] == 1){?>
		
		<?php// } ?>

		
		<tr>
		<td>&nbsp;</td>
		<td></td>
		<td></td>
		<td>&nbsp;</td>
		</tr>
		
		<?php 
		// BLOCO APRESENTADO APENAS SE FOR UPDATE
		if(isset($_GET['ID']) && $_GET['ID'] > 0)
		{			
			if(isset($fam_conf) && $fam_conf['tem_bonus'] == 1)
			{
				if(strpos($pontos, "%") != false)
				{
					$select1 = 'selected="selected"';
					$select2 = '';
					$pontos = str_replace("%", "", $pontos);
				}
				else
				{
					$select2 = 'selected="selected"';
					$select1 = '';
					$pontos = str_replace("€", "", $pontos);
				}			
			?>
			<tr><td style="border-bottom:#c0c0c0 1px solid;height:1px;font-size:1px" colspan="10"></td></tr>
			<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
			
			<tr bgcolor="#EAEAEA">
			<td width="10">&nbsp;</td>
			<td class="preto" width="50">Bonus:</td>
			<td align="left" class="preto"><input class="box" name="pontos" style="width:80px" maxlength="5" value="<?php echo $pontos;?>" /></td>
			<td width="4">&nbsp;</td>
			</tr>
			
			<tr bgcolor="#EAEAEA">
			<td width="10">&nbsp;</td>
			<td class="preto" width="50">Ação:</td>
			<td align="left" class="preto">
			<select name="tpoints" id="tpoints" class="chzn-select4" style="width:80px">
			<option value="€" <? echo $select2; ?>>€</option>
			<option value="%" <? echo $select1; ?>>%</option>
			</select>
			</td>
			<td width="4">&nbsp;</td>
			</tr>
			
			<?php 
			}
			
			// MARGEM POR FAMILIA
			if(isset($fam_conf) && $fam_conf['tem_desconto'] == 1)
			{
			?>
			<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
			<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
			
			<tr bgcolor="#EAEAEA">
			<td width="10">&nbsp;</td>
			<td class="preto" width="50">Desconto(%):</td>
			<td align="left" class="preto"><input class="box" name="Desconto" style="width:80px" maxlength="5" value="<?php echo $Desconto;?>" /></td>
			<td width="4">&nbsp;</td>
			</tr>
			
			<tr bgcolor="#EAEAEA">
			<td width="10">&nbsp;</td>
			<td class="preto" width="50">Ação:</td>
			<td align="left" class="preto">
			<select name="DescontoTipo" id="DescontoTipo" class="chzn-select4">
			<option value="0" <?php if($DescontoTipo==0 || $Desconto == 0){?>selected="selected"<?php }?>>Não aplicar</option>
			<option value="1" <?php if($DescontoTipo==1 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos</option>
			<option value="2" <?php if($DescontoTipo==2 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos sem promoção</option>									
			<option value="3" <?php if($DescontoTipo==3 && $Desconto > 0){?>selected="selected"<?php }?>>Aplicar a subfamilias/produtos com promoção</option>
			</select>
			</td>
			<td width="4">&nbsp;</td>
			</tr>
			
			<?php 
			}
		
			// MARGEM POR FAMILIA
			if(isset($fam_conf) && $fam_conf['tem_margem'] == 1)
			{
			?>
			<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
			<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
			
			<tr bgcolor="#EAEAEA"> 
			<td width="10">&nbsp;</td>
			<td class="preto" width="200">Margem(%):</td>
			<td class="preto" align="left" colspan="10"><input  class="box" name="margemcli" style="width:80px" value="<?php echo $margemcli;?>"></td>
			</tr>		
			<?php 
			}
		
			// PORTES POR FAMILIA
			if(isset($fam_conf) && $fam_conf['tem_portes'] == 1)
			{
			?>
			<tr bgcolor="#EAEAEA"><td style="border-bottom:#c0c0c0 1px solid" colspan="10">&nbsp;</td></tr>
			<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
			
			<tr bgcolor="#EAEAEA">
			<td width="10">&nbsp;</td>
			<td class="preto"  width="50">Portes(€):</td>
			<td align="left" class="preto"><input class="box" name="Portes" style="width:80px" maxlength="5" value="<?php echo $Portes;?>" ></td>
			<td width="4">&nbsp;</td>
			</tr>							
			<?php
			}
		}
		?>
		<tr bgcolor="#EAEAEA"><td colspan="10">&nbsp;</td></tr>
		
		</table>
		
		</div>
		
		<?php 
		// IMAGEM DA FAMILIA
		if(isset($fam_conf) && $fam_conf['tem_imagens'])
		{
		?>
			<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
			<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">			
			<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td>&nbsp;</td>
			</tr>
			
			<tr> 
			<td width="10">&nbsp;</td>
			<td class="preto" align="left" width="163"><? echo write_lang('key_imagem','Imagem')?> 1:
			<table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td class="preto">(jpg | png | gif) 72 dpis</td></tr></table>
			</td>
			<td class="preto3" align="left">
			<?php
			if($I1!='')
			{
			?>
				<div class="imagens_400"><img src="<? echo "../../files/products_families/".$ID."_1.".$I1;?>" alt="" border="0" /></div><br />
				<?php
				if($task != "apagar")
				{
					echo write_lang('key_apagar','Apagar');?> 
					<input type="checkbox" name="Apagar_img1" value="1"> 
					<?php echo write_lang('key_ou','ou'); echo write_lang('key_alterar','Alterar')?> <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" /><?php 
				}
			}
			else
			{
				echo write_lang('key_nova','Nova')?> : <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" />
			<?php }?>
			</td>
			<td width="4">&nbsp;</td>
			</tr>

			<tr>
			<td>&nbsp;</td>
			<td></td>
			<td></td>
			<td>&nbsp;</td>
			</tr>
			</table>
			</div>
		<?php }

		// CARACTERISTICAS DA FAMILIA
		if(isset($fam_conf) && $fam_conf['tem_caract'])
		{
		?>
			<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3">
			<table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
			<tr>
			<td width="10">&nbsp;</td>
			<td>

			<table width="100%" border=0 cellpadding="2" cellspacing="2">
			<tr>
			<td class="preto" width="200">Cor:</td>
			<td align="left">
			<table border=0 cellspacing=0 cellpadding=0>
			<tr>
			<td width="65%"><input type="text" disabled="disabled" style="height:17; width:15; background-color:<?php echo $cor?>">&nbsp;<input name="cor" type="text" style="height:17; width:80; font-family: Arial" value="<?php echo $cor?>">&nbsp;</td>
			<td width="35%"><input id="pick1" onClick="cp2.select(document.forms[0].cor,'pick1');return false;" name="pick1" type="button" style="height:17; width:52; border-color:#000000; background-color:#FFFFFF; font-size:10px; cursor:hand" value="escolher"></td>
			</tr>
			</table>
			</td>
			</tr> 
			<tr>
			<td class="preto" width="200">Texto:</td>
			<td></td>
			</tr>
			</table>
			
			<textarea name="texto" style="width:100%; HEIGHT:50px"><?php echo $T;?></textarea><?php show_editor("oEdit1", "texto", "99%", 150); ?>
			
			</td>
			<td width="4">&nbsp;</td>
			</tr>
			</table>
			</div>
		<?php }?>	
				
	</div>
	</td>
	</tr>
	</table>
		
	<table width="100%" height="5px" border="0" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
	
	<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
	<td>&nbsp;<input class=botao type="submit" name="Submit" value=" <?php echo $task_button;?> "> <input class=botao type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self');"></td>
	</tr>
	</table>
	
	<br />                                                  
</form>                        
<?php
	
	if($task_form == "altera")
	{
		$rs->db_open($db_link,$DB_name, "SELECT * FROM products_families WHERE ID_ORG=$ID ORDER BY Ordem ASC");
		if(!$rs->EOF())
		{?>
		<form action="reordena2.php" method="post"><input type="hidden" name="url_return" value="products_families.php?ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&total_prods=<?php echo $total_prods?>&checkshowimages=<?php echo $checkshowimages?>">
		<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
		<tr>
			<td>
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
				<tr> 
					<td width="5"></td>
					<td class="branco"> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?></td>                            
				</tr>
				</table>
				<?
				$count=0;
				while(!$rs->EOF())
				{
					$codigo=$rs->db_data["ID"];
					$ordem=$rs->db_data["Ordem"];									
					$desconto=number_format($rs->db_data["Desconto"],2);					
					$hRes=$rs2->db_open($db_link,$DB_name,"Select * from products_families_lang where ID_PAI=$codigo and lang='$admin_lang'");
					$cabecalho=stripslashes($rs2->db_data["Cabecalho"]);
					$rs2->db_close();
					
					if($count % 2 !=0)	{$cor="#def2fa";$class="r1";$n="1";}
					else				{$cor="#F6F6F6";$class="r2";$n="2";}?>
					<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
					<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
					<tr>
						<td width="29" align="center"><img src="../images/page.png"></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
						<td width="20" align="center" class="preto"><?php echo $codigo?></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
						<td class="preto"><a class="preto" href="products_families.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&Nivel=<?php echo $Nivel+1;?>">&nbsp;<? if($cabecalho) echo htmlentities_b($cabecalho); else echo "[Editar]"?></a><?		
						if($num_total) echo "&nbsp&nbsp<span style=\"{color:black;FONT-SIZE: 11px;}\">($num_total) TOTAL de produto(s)</span>&nbsp&nbsp";		
						if($num_prod_a) echo "&nbsp&nbsp<span style=\"{color:green;FONT-SIZE: 11px;}\">$num_prod_a Novo(s) ou Alterado(s)</span>&nbsp&nbsp";
						if($num_prod_p) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_d Em promoção</span>&nbsp&nbsp";		
						if($num_prod_n) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_n Novidades</span>&nbsp&nbsp";
						if($num_prod_d) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_d Destaques</span>&nbsp&nbsp";
						if($num_prod_s) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_s Saldos</span>&nbsp&nbsp";
						?></td>
						<? if($desconto > 0){?> <td width="80" class="desconto<?php echo $n?>"><?php echo $desconto?> %</td> <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td><? }else{?> <td width="20" class="desconto<?php echo $n?>">&nbsp;</td> <td width="4" align="right">&nbsp;</td><?php }?>
						<td width="20" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
						<td width="20" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products_families.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
						<td class="ordem<?php echo $n?>"><input class="ordem" type=text name="chk_<?php echo $codigo?>" value="<?php echo $ordem?>"></td>
					</tr>
					</table><?
					$rs->db_next();
					$count++;
				}
				$rs->db_close();?>
			</td>
		</tr>
		</table>
		
			<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
			<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
			<tr>
				<td>&nbsp;<input class="botao" type="submit" name="Reordenar" value="<? echo write_lang('key_reordenar','Reordenar')?> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?>"></td>
			</tr>
			</table>
			<img src="../images/bsh.png" width="890px">
		</form><?
		}
	}
}
                    if($task_form=="altera" || $pesquisa || $pesquisa2 || $top_search)
                    {
                        if($flag==1) // Apagar
                        {
                            foreach($_POST as $key=> $valor)
                            {
                                if((strcmp(substr($key,0,4),"chk_")==0))
                                {
                                    $id=$valor;
                                    $img_path="../../files/products/";      
                                    DeleteProd($id,$img_path);
                                }
                            }
                        }
                        if($flag==2) // Reordenar
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key=> $valor)
                            {
                                if((strcmp(substr($key,0,6),"field_")==0))
                                {
                                    $id=substr($key, strpos($key,"_")+1);
                                    $sql="UPDATE products SET Ordem=$valor WHERE ID=$id";
                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($flag==3) // mudar de familia
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key=> $valor)
                            {
                                if((strcmp(substr($key,0,4),"chk_")==0))
                                {
                                    $id = $valor;
                                    $sql = "UPDATE products SET ID_ORG=$m_familia WHERE ID=$id";
                                    $res = Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($flag==4) // actualiza preco
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key=> $valor)
                            {
                                if((strcmp(substr($key,0,6),"preco_")==0))
                                {
                                    $prod_id=substr($key, strpos($key,"_")+1);
                                    $preco=$valor;
                                    $sql="UPDATE products SET Preco1=$preco WHERE ID=$prod_id";
                    
                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }   
                        if($flag==5) // actualiza preco
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key=> $valor)
                            {
                                if((strcmp(substr($key,0,13),"configurador_")==0))
                                {
                                    $prod_id=substr($key, strpos($key,"_")+1);
                                    $conf=$valor;
                                    $sql="UPDATE products SET Configurador='$conf' WHERE ID=$prod_id";
                                    
                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($m_familia) $ID = $m_familia;
                        if($pesquisa)
                        {
                            /************************ SUPER NOVIDADE !!!! ****************************/
                            $rs = Pesquisa_backoffice($pesquisa);
                            /********************************************************/
                        }
                        elseif($pesquisa2)
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            $where=" WHERE lower(products_objs_lang.Cabecalho) like '%".strtolower($pesquisa2)."%' ";
                            $sql="Select products.*,products_lang.Cabecalho,products_objs_lang.Cabecalho as Ref from products INNER JOIN products_lang ON products_lang.ID_PAI=products.ID inner join products_objs on products_objs.obj_type_id=products.ID inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $where and products_lang.Lang='$admin_lang' and products_objs_lang.Lang='$admin_lang' order by products_objs_lang.Cabecalho Asc";
                            $rs=new DataObj;
                            $hRes=$rs->db_open($db_link,$DB_name,$sql);
                        }
						elseif($top_search)
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            $where=" WHERE lower(products_objs_lang.Cabecalho) like '%".strtolower($top_search)."%' ";
                            $sql="Select products.*,products_lang.Cabecalho,products_objs_lang.Cabecalho as Ref from products INNER JOIN products_lang ON products_lang.ID_PAI=products.ID inner join products_objs on products_objs.obj_type_id=products.ID inner join products_objs_lang on products_objs_lang.ID_PAI=products_objs.obj_id $where and products_lang.Lang='$admin_lang' and products_objs_lang.Lang='$admin_lang' order by products_objs_lang.Cabecalho Asc";
                            $rs=new DataObj;
                            $hRes=$rs->db_open($db_link,$DB_name,$sql);
							if($rs->EOF())
							{
								// nao encontrou vamos entao procurar no texto
								$rs=Pesquisa_backoffice($top_search);
							}
                        }
                        elseif($novos)
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            $where=" WHERE Aproved=0 ";
                            $sql="Select * from products $where order by Ref Asc";
                            $rs=new DataObj;
                            $rs->db_open($db_link,$DB_name,$sql);
                        }
                        else
						{ 
							$info=GetRecords_Recursos_1_1(0,$ID,$Nivel+1,"*",$limite_from,$limite_to,$total_prods);
							$rs = $info['obj'];
							$paginacao = $info['return'];
						}
                        
						if($rs && !$rs->EOF())
                        {   
                        ?>
                            <form name="form2" method="post">
                                <input type="hidden" name="ID_Fam" value="<?php echo $ID?>">
                                <input type="hidden" name="ID" value="<?php echo $ID?>">
                                <input type="hidden" name="ID_ORG" value="<?php echo $ID_ORG?>">
                                <input type="hidden" name="total_prods" value="<?php echo $total_prods?>">
                                <input type="hidden" name="checkshowimages" value="<?php echo $checkshowimages?>">
                                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                                <tr>
                                    <td>
                                    
                                        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                                        <tr> 
                                            <td width="5px">&nbsp;</td>
                                            <td class="branco"><?php echo write_lang('key_produtos','Produtos')?></td>
                                            <td>&nbsp;
                                                
                                             </td>                            
                                        </tr>
                                        </table>
                                        <table id="lista_produtos" class="p1" width="100%" border=0 cellspacing=0 cellpadding=0>
                                        <tr> 
                                            <td width="29px" height="10px" align="center"><input onClick='makeSelection("filter","form2")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
                                            <td width="4px" height="10px">&nbsp;</td>
                                            <td width="29px" height="10px">&nbsp;</td>
                                            <td width="4px" height="10px">&nbsp;</td>
                                            <td width="10px" height="10px">&nbsp;</td>
                                            <td width="1px">&nbsp;</td>
                                            <td width="1px">&nbsp;</td>
                                            <td class="branco"><?php echo write_lang('key_nome','Nome')?></td>
                                            <td width="26px">&nbsp;</td>
                                            <td width="4px">&nbsp;</td>
                                            <td width="30px">&nbsp;</td>
                                        </tr>
                                        </table>
                                        <?      
                                        $count=0;   
                                        
                                            while(!$rs->EOF())
                                            {
                                                $visivel=$rs->db_data["Aproved"];
                                                $codigo=$rs->db_data["ID"];
                                                $img_ext=$rs->db_data["img_ext"];
                                                $ordem=$rs->db_data["Ordem"];
                                                $ref=$rs->db_data["Ref"];
                                                $preco1=$rs->db_data["Preco1"];
                                                $preco2=$rs->db_data["Preco2"];
                                                $precoCusto=$rs->db_data["Preco_prec"];
                                                $ID=$rs->db_data["ID_ORG"];
                                                $importado=$rs->db_data["Importado"];
                                                $id_fornecedor=$rs->db_data["id_fornecedor"];
                                                $tipo=$rs->db_data["Tipo"];
                                                $configurador=$rs->db_data["Configurador"]; 
												$points = $rs->db_data["points"];
                                               
											   // LINGUAS
                                                $rs2->db_open($db_link, $DB_name, 'SELECT Cabecalho, Texto1 FROM products_lang WHERE ID_PAI='.$codigo.' AND lang="'.$admin_lang.'"');
                                                $C = $rs2->db_data["Cabecalho"];
                                                $T = $rs2->db_data["Texto1"];
                                                $rs2->db_close();
												
												$rs2->db_open($db_link,$DB_name, 'SELECT obj_pvp, obj_promo, obj_promo_ativo, obj_ativo, obj_novo FROM products_objs WHERE obj_type_id='.$codigo);
												$obj_pvp = $rs2->db_data["obj_pvp"];
												$obj_promo = $rs2->db_data["obj_promo"];
												$obj_promo_ativo = $rs2->db_data["obj_promo_ativo"];
												$obj_ativo = $rs2->db_data["obj_ativo"];											
												$obj_novo = $rs2->db_data["obj_novo"];											
                                                $rs2->db_close();												
				
												$icon_bonus = 'images/icon_bonus_nao.png';
												$icon_promo = 'images/icon_promo_nao.png';
												$icon_novidade = 'images/icon_novidade_nao.png';

												$prod_activo=0;
												if($visivel) $prod_activo = 1;

												// SE TIVER PONTOS > 0 E PROMOCAO CHECKADO ENTAO MOSTRAR ICON "BONUS"
												if($points > 0 && $points != '0€') $icon_bonus = 'images/icon_bonus.png';
												// SE TIVER APENAS PROMOCAO CHECKADO  ENTAO MOSTRAR ICON "PROMO"
												
												if($obj_promo_ativo > 0) $icon_promo = 'images/icon_promo.png';
												
												// SE TIVER APENAS VALOR NO CAMPO DE PROMOCAO SEM A CHECKBOX ENTAO APENAS APARECE COM O PREÇO RISCADO E NA LISTAGEM DAS PROMOS SEM ICON
												if($obj_novo > 0) $icon_novidade = 'images/icon_novidade.png';											
												
												if($obj_pvp!=$obj_promo && $obj_promo > 0 && $obj_promo_ativo==1)
												{
													$obj_p = $obj_promo;
												}
												else
												{
													$obj_p = $obj_pvp;
												}
												
                                                if($count % 2 !=0)  {$cor = "#def2fa"; $class = "r1"; $n = "1";}
                                                else                {$cor = "#F6F6F6"; $class = "r2"; $n = "2";}
												
                                                ?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
                                                <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
                                                <td width="29" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" /></td>
												<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>
                                                <td width="29" align="center"><img src="../images/page.png" /></td>
                                                <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg" /></td>                        
                                                <td width=10 class="preto">&nbsp;</td>                                                                                           
                                                <td><a class="preto" href="products.php?ID=<?php echo $rs->db_data["ID"];?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&Nivel=<?php echo $Nivel+1;?>">[<?php echo $codigo?>]&nbsp; - <? if($C) echo htmlentities_b($C); else echo "[Edit]"?> </a></td>
                              
												
												<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
												<td class="preto" width="24px" align="center"><? if($prod_activo){?><img src="images/icon_activo.png"><? }?></td> 
												<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
												
											
                                                <td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=duplicar"><img title="Duplicar" src="duplicate.png" border="0" /></a></td>
                                                <td width="20px" align="right" bgcolor="<?php echo $cor;?>"><a class="italico" href="products.php?ID=<?php echo $rs->db_data["ID"]?>&ID_ORG=<?php echo $ID;?>&ID_ORG_ANT=<?php echo $ID_ORG?>&task=apagar"><img title="<?php echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
                                                <td width="4px" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
                                                <td width="1px"></td>
                                                </tr>
                                                </table><?php
                                                $rs->db_next(); 
                                                $count++;
                                            }
                                        
                                        $rs->db_close();
										echo $paginacao;
                                        ?>
                                    </td>
                                </tr>
                                </table>
                            <?php
                             show_page_bottom_tools2();	
                            ?>
                            </form>
                            <?php
                        }
                    }
                    ?>
         </td>
         <td width="10px">&nbsp;</td>
         <td width="200px" valign="top"><?php show_fast_tools();?></td>
    </tr>
    </table>
</div>
<script src="avancada/chosen.jquery.min2.js?dp=<?php echo dumpcache?>" type="text/javascript"></script>
<script type="text/javascript"> 
$(".chzn-select4").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "300px", enable_new_filter:false});
$(".chzn-select6").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", enable_new_filter:false});
</script>
<?php include "../layout/bottom.inc.php";?>