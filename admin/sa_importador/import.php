<?php
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');


		ini_set('display_errors', 1);
		ini_set('display_startup_errors', 1);
		error_reporting(0);
	function limpa_special_import($s)
	{
		$s=strip_tags($s);
		$s=str_replace('<br>','',$s);
		$s=str_replace('<br/>','',$s);
		$s=str_replace('<br />','',$s);
		$s=trim($s,"\t\n\r\0\x0B");
		$v=array("(",")","³","°","²","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","#","=");
		$s=str_replace($v,'',$s);
		$v=array(" ","'","/","&",":",";","`","´","~","^","¨","_",",",".","|","\\","<",">","—","€","±","×","¿");
		$s=str_replace($v,'-',$s);
		$s=str_replace('-----','-',$s);
		$s=str_replace('----','-',$s);
		$s=str_replace('---','-',$s);
		$s=str_replace('--','-',$s);
		$v=array("á","à","ã","â","ä"); 	$s=str_replace($v,'a',$s);
		$v=array("é","è","ê","ë"); 		$s=str_replace($v,'e',$s);
		$v=array("í","ì","î","ï"); 		$s=str_replace($v,'i',$s);
		$v=array("ó","ò","õ","ô","ö"); 	$s=str_replace($v,'o',$s);
		$v=array("ú","ù","û","ü"); 		$s=str_replace($v,'u',$s);
		$v=array("ñ"); 					$s=str_replace($v,'n',$s);
		$v=array("ç"); 					$s=str_replace($v,'c',$s);
		$v=array("Á","À","Ã","Â","Ä"); 	$s=str_replace($v,'A',$s);
		$v=array("É","È","Ê","Ë"); 		$s=str_replace($v,'E',$s);
		$v=array("Í","Ì","Î","Ï"); 		$s=str_replace($v,'I',$s);
		$v=array("Ó","Ò","Õ","Ô","Ö"); 	$s=str_replace($v,'O',$s);
		$v=array("Ú","Ù","Û","Ü"); 		$s=str_replace($v,'U',$s);
		$v=array("Ñ"); 		$s=str_replace($v,'N',$s);
		$v=array("Ç"); 		$s=str_replace($v,'C',$s);
		return $s;
	}

	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);



	$field_array = array();
	$fields=db_list_fields($DB_name,'products_import',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Stock', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_import ADD Stock int(11) DEFAULT 0');}



	$obj = new DataObj;

	$sql2 = '
			SELECT
				pf.ID, pf.ID_ORG, pfl.Cabecalho
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI = pf.ID
			WHERE
				(Ref_integracao = "" OR Ref_integracao2 = "")
				AND Lang="'.$GLOBALS['lingua_website'].'"
			GROUP BY
				pf.ID
			';
			// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);
	$upd2 = '';
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$fam_id = $obj->db_data['ID'];
			$ID_ORG = $obj->db_data['ID_ORG'];

			$titulos = '';
			$p_way = geraCatalogoRecursivo($fam_id);
			$p_way_t = geraCatalogoRecursivo_c($p_way);
			if(count($p_way_t) > 0)
			{
				$s2 = '';
				for($a=0; $a<count($p_way_t['titulo']); $a++)
				{
					if($titulos != '') $titulos .= '_'.$p_way_t['titulo'][$a];
					else $titulos .= $p_way_t['titulo'][$a];
				}
			}

			$fam_nome = $obj->db_data['Cabecalho'];
			$upd = 'UPDATE products_families SET Ref_integracao = "'.limpa_special_import($titulos).'", Ref_integracao2 = "'.limpa_special_import($titulos).'" WHERE ID = "'.$fam_id.'"';
			// $upd2 .= $upd . '<br>';
			Runquery($db_link, $DB_name, $upd);


			$obj->db_next();
		}
	}
	$obj->db_close();



	$sql2 = '
			SELECT
				pf.category_id, pfl.category_name
			FROM
				filters_categories pf
				INNER JOIN filters_categories_lang pfl ON pfl.category_id = pf.category_id
			WHERE
				pf.value_id_import_ref = ""
				AND category_lang="'.$GLOBALS['lingua_website'].'"
			GROUP BY
				pf.category_id
			';
			// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);
	$upd2 = '';
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$category_id = $obj->db_data['category_id'];
			$category_name = $obj->db_data['category_name'];

			$upd = 'UPDATE filters_categories SET value_id_import_ref = "'.limpa_special_import($category_name).'" WHERE category_id = "'.$category_id.'"';
			Runquery($db_link, $DB_name, $upd);

			$upd = 'UPDATE filters_categories_lang SET value_id_import_ref = "'.limpa_special_import($category_name).'" WHERE category_id = "'.$category_id.'"';
			Runquery($db_link, $DB_name, $upd);

			$obj->db_next();
		}
	}
	$obj->db_close();



		$sql2 = '
			SELECT
				pf.value_id, pfl.values_name
			FROM
				filters_values pf
				INNER JOIN filters_values_lang pfl ON pfl.values_id = pf.value_id
			WHERE
				pf.value_id_import_ref = ""
				AND values_lang="'.$GLOBALS['lingua_website'].'"
			GROUP BY
				pf.value_id
			';
			// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);
	$upd2 = '';
	if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$category_id = $obj->db_data['value_id'];
			$category_name = $obj->db_data['values_name'];

			$upd = 'UPDATE filters_values SET value_id_import_ref = "'.limpa_special_import($category_name).'" WHERE value_id = "'.$category_id.'"';
			Runquery($db_link, $DB_name, $upd);

			$upd = 'UPDATE filters_values_lang SET value_id_import_ref = "'.limpa_special_import($category_name).'" WHERE values_id = "'.$category_id.'"';
			Runquery($db_link, $DB_name, $upd);

			$obj->db_next();
		}
	}
	$obj->db_close();


?>
<style>
.botao{ padding:0 5px}
</style>
<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center"><h3>Importador de produtos</h3></td></tr>
            </table>
            <form name="form1" method="post" action="import_trata.php" enctype="multipart/form-data">
                <table width="100%" border="0" cellpadding="0" cellspacing="0"  style="background-color:#DDD">
                    <tr><td style="background-color:#DDD" colspan="2" height="25" align="center"><b>1 - COMO IMPORTAR UM FICHEIRO XLSX</b></td></tr>
                    <tr>
                        <td colspan="2" style="padding:0 10px;background-color:#DDD" align="left">

						<?php echo $upd2;?>
							<table border="0" cellspacing="0" cellpadding="5">
                                <tr>
                                	<td align="left" style="font-weight:bold;line-height:15px">
							1. Campos com <span  style="font-weight:bold;color:#FF0000">obrigatório</span> tem que estar presentes no ficheiro<br>
							2. Produtos com Medidas, este deverá ter sempre as medidas todas disponiveis, porque se não forem todas enviadas as antigas serão removidas<br>
							3. Campo 'Tipo - (<span style="font-weight:bold;color:#FF0000">1</span>)', quando disponivel com texto a frente do ifen(-), exemp: 'Tipo - Colchões' será criado/atualizado como uma caracteristica <br>
							4. Se os produtos forem para Famílias/Sub Famílias de integração deverá colocar o nome exato que pretence à Família e Sub Família, sendo este case sensitive(A-Z|a-z) <br>
							5. O nome de cada coluna deverá ser exatamente igual ao exemplo que disponibilizado<br>
							</td></tr></table>

						<hr/>

						    <table border="1" cellspacing="0" cellpadding="5">
                                <tr>
									<td align="center" style="font-weight:bold;font-size:10px">a) </td>
                                	<?php
									foreach($GLOBALS['arr_campos_importar'] as $campos)
									{
										?><td align="center" style="font-weight:bold;font-size:10px"><?php echo $campos['CAMPO'].$campos['INFO'];?></td><?php
									}
									?>
                                </tr>
								<?php /* <tr>
									<td align="center" style="font-weight:bold;font-size:10px">b) </td>
                                	<?php
									foreach($GLOBALS['arr_campos_importar'] as $campos)
									{
										?><td align="center" style="font-weight:bold;font-size:10px"><?php echo $campos['CAMPO_ALT'].$campos['INFO'];?></td><?php
									}
									?>
                                </tr> */?>
                                <tr>
								<td align="center" style="font-weight:bold;font-size:10px">&nbsp;</td>
                                    <?php
									foreach($GLOBALS['arr_campos_importar'] as $campos)
									{
										if($campos['OBRIGATORIO'] == 1){?> <td align="center" style="font-weight:bold;color:#FF0000;font-size:10px">Obrigatório</td><?php }
										else{?> <td align="center">&nbsp;</td><?php }
									}
									?>
                                </tr>
                            </table>
							<hr/>
							<center><input type="button" class="botao" value=" Download Ficheiro Exemplo com apenas um Tipo" onclick="window.open('download/ficheiro_exemplo.xlsx','_blank')" /> <input type="button" class="botao" value=" Download Ficheiro Exemplo com vários Tipos" onclick="window.open('download/ficheiro_exemplo2.xlsx','_blank')" /></center>


							<hr/>
                        </td>
                    </tr>
                    <tr>
                        <td  align="left" width="150" style="padding:10px"><span id="tt1">2 - Enviar documento <b>XLSX</b>:</span></td>
                        <td align="left" valign="top" style="padding:10px"><span id="tt2"><input type="file" name="import_file">&nbsp;<input type="submit" class="botao" value=" Submeter para analise..." /></span></td>
                    </tr>
					<?php if(count(glob(dirname(__FILE__) . '/../../files/importar/*.xlsx')) > 0){ ?>
					<tr>
                        <td  align="left" width="120" style="padding:10px;vertical-align:top" >&nbsp;</td>
                        <td align="left" valign="top" style="padding:10px">
						<div id="ficheiro_imp2" style="font-size:13px">
						<?php
						$tem_ficheiros = 0; $num = 1;
						foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
						{
							$ext = pathinfo($filename, PATHINFO_EXTENSION);
							$file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename));
							echo $num.' - '.$file[0].'.'.$ext.' - Remover<br>';
							$tem_ficheiros = 1;
							$num++;
						}
						?></div><?php

						if($tem_ficheiros == 1)
						{
							?>
							<br><br><div id="carregar_analise"></div>
							<div id="carregar2_analise" style="display:none;background:#FFF"></div><?php
						}


						?>
						</td>
                    </tr>
					<?php }?>
					<tr>

                        <td align="left" valign="top" style="padding:10px" colspan="2">
						<div id="ficheiro_imp" style="font-size:13px;overflow:hidden;overflow-x:auto;height:auto;max-width:1080px"></div><?php

						// if($tem_ficheiros == 1)
						// {
							?>
							<br><br><div id="carregar" style="display:none"><input type="button" name="atualizar" id="atualizar" value="3 - Prosseguir com a atualização do site" onclick="callfile();return false;" class="botao" /></div>
							<div id="carregar2" style="display:none;background:#FFF"></div><?php
						// }


						?>
						</td>
                    </tr>

                </table>

            </form>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
                <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
        </td>

    </tr>
</table>

<script>

$( document).ready(function()
{
	$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";document.getElementById('ficheiro_imp').innerHTML = idx[1]; document.getElementById('tt1').style.display="block";document.getElementById('tt2').style.display="block";}else if(id==0){document.getElementById('tt1').style.display="none";document.getElementById('tt2').style.display="none";document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{ document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block"; document.getElementById('carregar2').innerHTML = id;}document.getElementById('ficheiro_imp').innerHTML = idx[1];}});
	var tt = setInterval(function()
	{
		$.ajax({type: 'GET',url: "verifica.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar').style.display="block";document.getElementById('carregar2').style.display="none";document.getElementById('ficheiro_imp').innerHTML = idx[1];document.getElementById('tt1').style.display="block";document.getElementById('tt2').style.display="block";}else if(id==0){document.getElementById('tt1').style.display="none";document.getElementById('tt2').style.display="none";document.getElementById('carregar').style.display="none";document.getElementById('carregar2').style.display="none";}else{document.getElementById('carregar').style.display="none"; document.getElementById('carregar2').style.display="block"; document.getElementById('carregar2').innerHTML = id;} document.getElementById('ficheiro_imp').innerHTML = idx[1];}});
	},
	3000);
	<?php if($tem_ficheiros == 1){?>
	$.ajax({type: 'GET',url: "verifica_analise.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar_analise').style.display="block";document.getElementById('carregar2_analise').style.display="none";document.getElementById('ficheiro_imp2').innerHTML = idx[1];}else if(id==0){document.getElementById('carregar_analise').style.display="none";document.getElementById('carregar2_analise').style.display="none";}else{ document.getElementById('carregar_analise').style.display="none"; document.getElementById('carregar2_analise').style.display="block"; document.getElementById('carregar2_analise').innerHTML = id;}document.getElementById('ficheiro_imp2').innerHTML = idx[1];}});
	var tt = setInterval(function()
	{
		$.ajax({type: 'GET',url: "verifica_analise.php",dataType: 'html',cache: false, success: function(id2){ idx = id2.split('[split]'); id = idx[0];  if(id=='-1'){document.getElementById('carregar_analise').style.display="block";document.getElementById('carregar2_analise').style.display="none";document.getElementById('ficheiro_imp2').innerHTML = idx[1];}else if(id==0){document.getElementById('carregar_analise').style.display="none";document.getElementById('carregar2_analise').style.display="none";}else{document.getElementById('carregar_analise').style.display="none"; document.getElementById('carregar2_analise').style.display="block"; document.getElementById('carregar2_analise').innerHTML = id;} document.getElementById('ficheiro_imp2').innerHTML = idx[1];}});
	},
	3000);
	<?php }?>
});

function callfile()
{
	$.ajax({type: 'GET',url: "import_xls2/run_webservice.php",dataType: 'html',cache: false, success: function(id){}});
	document.getElementById('carregar').style.display="none";
	document.getElementById('carregar2').style.display="block";
}
function remove(rem)
{
	if(confirm('Tem a certeza que quer remover este ficheiro?'))
	{
		$.ajax({type: 'GET',url: "remove.php?remove="+rem, dataType: 'html', cache: false, success: function(id){}});
	}
}
function remove2()
{
	if(confirm('Tem a certeza que quer cancelar a importação, os produtos que já foram atualizados não serão repostos?'))
	{
		$.ajax({type: 'GET',url: "remove.php?remove2=1", dataType: 'html', cache: false, success: function(id){}});
	}
}
</script>
<?php require_once('../layout/bottom.inc.php');