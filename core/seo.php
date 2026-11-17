<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function seo2($t='',$page_id='',$prods='',$lang='',$IDb='',$Str='')
{
	//Versao sem querys a bd usada nos filtros
	global $seo;
	if(!$seo)
	{
		if($t==1)return 'pages.php?page_id='.$page_id.'&cat='.$IDb;
		if($t==2)return 'pages.php?page_id='.$page_id.'&prod_id='.$prods['prod_id'];
		if($t==5)return 'pages.php?page_id='.$page_id;
	}

	$sep='-';
	$pnome = $prods['prod_nome'];
	$strl = 100;
	if(strlen($pnome) > $strl)
	{
		$pnome = substr($pnome, 0,$strl);
	}

	if($t==1){$p2=limpa_special($Str); if($p2=='' || $p2=='-')$p2='page'; return lower_str('p'.$page_id.$sep.'c'.$sep.$IDb.$sep.$p2).$sep.$lang;}//familia
	if($t==2){$p2=limpa_special($pnome!='' ? $pnome : 'prod'); if($p2=='' || $p2=='-')$p2='page'; return lower_str('p'.$page_id.$sep.'p'.$sep.$prods['prod_id'].$sep.$p2).$sep.$lang;}//produtos
	if($t==5)return lower_str('p'.$page_id.$sep.'c'.$sep.$IDb).$sep.$lang;
}
function seo($u='', $id_mod='', $t='', $force_lang='')
{
	/*$key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
	$fp = fopen('/var/www/lojas/mhr/files/mysql_log.txt', 'a');
	fwrite( $fp, debug_backtrace()[$key]['file'].' » ');
	fclose( $fp );*/

	global $seo;
	if(!$seo) return $u;
	if($force_lang=='') $force_lang=$GLOBALS['site_lang'];

	$s='pages.php?page_id='; $sep='-';
	if(strpos($u,$s) !== false)
	{
		$n=intval(substr($u,strpos($u,$s)+strlen($s),strlen($u)));
		global $DB_host,$DB_user,$DB_pass,$DB_name,$page_id,$seo;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs->db_open($db_link, $DB_name, 'SELECT Cabecalho FROM pages_lang WHERE ID_PAI='.$n.' and Lang="'.$force_lang.'"');
		$p=str_replace(' ', '', limpa_special($rs->db_data['Cabecalho']));
		$p=str_replace('Catalogo', 'c', $p);
		$p=str_replace('Catalog', 'c', $p);
		$p=str_replace('Catalogue', 'c', $p);
		$p=str_replace('catalogue', 'c', $p);
		$p=str_replace('Products', 'c', $p);
		$p=str_replace('PRODUCTS', 'c', $p);
		$p=str_replace('products', 'c', $p);
		$p=str_replace('Produto', 'p', $p);
		$p=str_replace('Product', 'p', $p);
		$p=str_replace('marca', 'm', $p);
		$rs->db_close();

		$resto=str_replace($s.$n, '', $u);

		// echo $resto;

		if($resto && substr_count($resto,'cat=')) 			$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'club=')) 		$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'registar=')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'logout=')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'show_carteira=')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'show_wishlist=')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'show_compras=')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'#')) 	$resto=str_replace('&', '?', $resto);
		elseif($resto && substr_count($resto,'&')) 	$resto=str_replace('&', '?', $resto);
		else $resto='';


		if($p=='' || $p=='-')$p='page';

		if($t == 2) // FICHA DO PRODUTO
		{
			$p='p';
			$prods=get_produtc_info($id_mod,'', 0, 0, $force_lang);
			// $u=lower_str('p'.$n.$sep.$p.$sep.$id_mod.$sep.limpa_special($prods['prod_nome'])).$sep.$force_lang;
			$p2=limpa_special(($prods['prod_nome']!='' ? $prods['prod_nome'] : 'prod'));
			if($p2=='' || $p2=='-')$p2='page';
			$u=lower_str('p'.$n.$sep.$p.$sep.$id_mod.$sep.$p2).$sep.$force_lang;
		}
		elseif($t==1) // FAMILIAS DO CATALOGO
		{
			$p='c';
			$fams=get_family_info($id_mod);
			$p2=limpa_special($fams['fam_nome']);
			if($p2=='' || $p2=='-')$p2='page';
			$u=lower_str('p'.$n.$sep.$p.$sep.$id_mod.$sep.$p2).$sep.$force_lang;
		}
		elseif($t==3) // FAMILIAS DO CATALOGO & SUBFAMILIA
		{
			$p='c';
			$fams=get_family_info($id_mod);
			$fams_p=get_pai($id_mod);
			$fams2=get_family_info($fams_p);
			$p2=limpa_special($fams['fam_nome']);
			if($p2=='' || $p2=='-')$p2='page';
			$u=lower_str('p'.$n.$sep.$p.$sep.$fams2['fam_nome'].$sep.$id_mod.$sep.$p2).$sep.$force_lang;
		}
		elseif($t==4) // CLUB CATEGORIAS
		{
			$p='c';
			$fams=get_club_categories_info($id_mod);
			$p2=limpa_special($fams['fam_nome']);
			if($p2=='' || $p2=='-')$p2='page';
			$u=lower_str('p'.$n.$sep.$p.$sep.$id_mod.$sep.$p2).$sep.$force_lang;
		}
		elseif($t==5) // CATALOGO 2
		{
			$p='c';
			$u=lower_str('p'.$n.$sep.$p.$sep.$id_mod).$sep.$force_lang;
		}elseif($t==6) // CATALOGO 2
		{
			$p='p';
			$prods=get_produtc_info($id_mod,'', 0, 0, $force_lang);
			// $u=lower_str('p'.$n.$sep.$p.$sep.$id_mod.$sep.limpa_special($prods['prod_nome'])).$sep.$force_lang;
			$p2=limpa_special(($prods['prod_nome']!='' ? $prods['prod_nome'] : 'prod'));
			if($p2=='' || $p2=='-')$p2='page';
			$u=lower_str($id_mod.$sep.$p2).$sep.$prods['Ref_Ean'].'.html'; // para MHR
		}
		else $u=lower_str('p'.$n.$sep.$p.$sep.$force_lang.$resto);
	}
	return $u;
}
function seo_prod($prod='')
{
	//Versao sem querys a bd
	$n=$GLOBALS['products_page_id'];

	$u='index.php?page_id='.$n;
	global $seo,$site_lang;
	if(!$seo) return $u.'&prod_id='.$prod['prod_id'].'&site_lingua='.$site_lang;

	$p='p';
	$sep='-';
	$p2=limpa_special(($prod['prod_nome']!='' ? $prod['prod_nome'] : 'prod'));
	if($p2=='' || $p2=='-')$p2='page';

	$u=lower_str('p'.$n.$sep.$p.$sep.$prod['prod_id'].$sep.$p2).$sep.$site_lang;
	return $u;
}
function decodeString($s)
{
    if(function_exists('mb_strlen'))$l=mb_strlen($s,'8bit');
	else $l=strlen($s);
	$r='';
    for($i=0;$i<$l;$i++){$r.=dechex(ord($s[$i])).' ';}
    return trim($r);
}
function limpa_special($s)
{
    $s=strip_tags($s);
	$s=str_replace('<br>','',$s);
	$s=trim($s,"\t\n\r\0\x0B");
	$v=array("(",")","³","°","²","+","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","?","#","=");
    $s=str_replace($v,'',$s);
	$v=array(" ","'","/","&",":",";","`","´","~","^","¨","_","+",",",".","|","\\","<",">","–","—","–","­","–","—","€","±","×","¿");
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
	$s = preg_replace('/[^a-zA-Z0-9-_]/', '', $s);
	if(mb_strlen($s,"UTF-8") > 100) $s=mb_substr($s,0,100,"UTF-8");
	return $s;
}
function lower_str($s)
{
  $s=preg_replace("/Á/","/á/",$s);$s=preg_replace("/À/","/à/",$s);$s=preg_replace("/Â/","/â/",$s);$s=preg_replace("/Ä/","/ä/",$s);$s=preg_replace("/Ã/","/ã/",$s);
  $s=preg_replace("/É/","/é/",$s);$s=preg_replace("/È/","/è/",$s);$s=preg_replace("/Ê/","/ê/",$s);$s=preg_replace("/Ë/","/ë/",$s);
  $s=preg_replace("/Í/","/í/",$s);$s=preg_replace("/Ì/","/ì/",$s);$s=preg_replace("/Î/","/î/",$s);$s=preg_replace("/Ï/","/ï/",$s);
  $s=preg_replace("/Ó/","/ó/",$s);$s=preg_replace("/Ò/","/ò/",$s);$s=preg_replace("/Ô/","/ô/",$s);$s=preg_replace("/Ö/","/ö/",$s);$s=preg_replace("/Õ/","/õ/",$s);
  $s=preg_replace("/Ú/","/ú/",$s);$s=preg_replace("/Ù/","/ù/",$s);$s=preg_replace("/Û/","/û/",$s);$s=preg_replace("/Ü/","/ü/",$s);
  $s=preg_replace("/Ç/","/ç/",$s);
  return strtolower(ltrim(rtrim($s)));
}