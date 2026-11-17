<?php
//ainda so testei esta funcao na fotobook
function BuscarArvoreFams($id)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	while($id > 0)
	{
		$arvore2[]=$id;
		$sql="SELECT ID, ID_ORG FROM products_families WHERE ID=$id";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$id=$obj->db_data["ID_ORG"];
		$obj->db_close();
	}
	$output = array_reverse($arvore2);
	return $output;
}
function BuscarArvoreFams_($id_org)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj = new DataObj;

	$pai = $id_org;
	while($pai > 0)
	{
		$id_org=$pai;
		$sql="SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai=$obj->db_data["ID_ORG"];
		$arvore2[]=$obj->db_data["ID"];
	}
	$output="";
	$t = (count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t] = $arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();
	return $output;
}
function inserefiltro($prod_id=0,$cat_id=0,$valor_id=0,$fam_id=0,$subfam_id=0,$subfam_id2=0,$subfam_id3=0,$marca_id=0,$filtros_rel='',$filtros_rel2='', $opt=0,$foto='')
{
	if($prod_id > 0) cria_filtro_produto($prod_id, $cat_id, $valor_id, $fam_id, $subfam_id, $subfam_id2, $subfam_id3, $marca_id, $filtros_rel, $filtros_rel2, $opt,$foto);
}
function cria_filtro_produto($prod_id=0,$cat_id=0,$valor_id=0,$fam_id=0,$subfam_id=0,$subfam_id2=0,$subfam_id3=0,$marca_id=0,$filtros_rel='',$filtros_rel2='', $opt=0,$foto='')
{
	$hConn = db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;

	$sql = 'SELECT img_ext FROM filters_products WHERE cat_id="'.$cat_id.'" AND valor_id="'.$valor_id.'" AND prod_id="'.$prod_id.'"';
	$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
	$img_ext=$rs->db_data["img_ext"];
	$rs->db_close();

	//$Imagem=$_FILES['foto_'.$cat_id.'_'.$valor_id];
	
	$foto_name=$prod_id.'_f_'.$cat_id.'_'.$valor_id;
	$Apagar_img=$_POST['Apagar_foto_'.$cat_id.'_'.$valor_id];
	$tem_foto=$_POST['tem_foto_'.$cat_id.'_'.$valor_id];
	$img_path='../../files/products/';
	$Imagem["name"]=$_FILES['foto_'.$cat_id.'_'.$valor_id]['name'];
	$Imagem["temp"]=$_FILES['foto_'.$cat_id.'_'.$valor_id]['tmp_name'];

	$altera_img=0;if($Imagem['name']){$altera_img=1;$pos=strrpos($Imagem['name'],".");$img_ext=substr($Imagem['name'],$pos+1,strlen($Imagem['name'])+1-$pos);}
	if($tem_foto && $Apagar_img){$ficheiro1=$foto_name.".".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
	if($altera_img)copia_segura($Imagem["temp"],$img_path.$foto_name.".".$img_ext);

	if($altera_img)echo $Imagem["temp"];
	if($altera_img)echo $img_path.$foto_name.".".$img_ext;

	$rs->db_open($hConn,$GLOBALS['DB_name'],$sql);
	if($rs->EOF())
	{
		$sql ='INSERT INTO filters_products
				(
					cat_id,
					valor_id,
					prod_id,
					fam_id1,
					fam_id2,
					fam_id3,
					fam_id4,
					marca_id,
					img_ext,
					filtros_rel2,
					FLAG,
					filtro_active,
					filtro_area
				)
				VALUES
				(
					"'.$cat_id.'",
					"'.$valor_id.'",
					"'.$prod_id.'",
					"'.$fam_id.'",
					"'.$subfam_id.'",
					"'.$subfam_id2.'",
					"'.$subfam_id3.'",
					"'.$marca_id.'",
					"'.$img_ext.'",
					"'.$filtros_rel2.'",
					1,
					1,
					"'.$opt.'"
				)
			';
		$res=Runquery($hConn,$GLOBALS['DB_name'],$sql);
	}
	else
	{
		$sql = '
				UPDATE filters_products
				SET
					fam_id1="'.$fam_id.'",
					fam_id2="'.$subfam_id.'",
					fam_id3="'.$subfam_id2.'",
					fam_id4="'.$subfam_id3.'",
					marca_id="'.$marca_id.'",
					img_ext="'.$img_ext.'",
					filtros_rel2="'.$filtros_rel2.'",
					FLAG=1,
					filtro_active=1,
					filtro_area="'.$opt.'"
				WHERE
					cat_id="'.$cat_id.'"
					AND
					valor_id="'.$valor_id.'"
					AND
					prod_id="'.$prod_id.'"
			';

		$res = Runquery($hConn,$GLOBALS['DB_name'],$sql);
	}
	$rs->db_close();
}
?>