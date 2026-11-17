<?php
function show_produtosp($t)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$ID,$ID_Fam, $txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj; $obj2=new DataObj; $obj3=new DataObj;

	if(isset($_GET['f_produto']) && ltrim(rtrim($_GET['f_produto']))!='')
	{

		$sql = '
				SELECT
					p1l.Cabecalho,p1l.ID_PAI as ID
				FROM
					products_lang p1l
				WHERE
					p1l.Cabecalho Like "%'.ltrim(rtrim($_GET['f_produto'])).'%"
					AND
					p1l.Lang="'.$GLOBALS['admin_lang'].'"
				ORDER BY
					p1l.Cabecalho ASC
				LIMIT 50
			';
			// echo $sql.'<br />';
		$obj->db_open($db_link, $DB_name, $sql);
		$total2 = 0;$return = '';
		if(!$obj->EOF())
		{
			$return = '<table width="100%">';
			while(!$obj->EOF())
			{

				$return .=  "
				<tr>
				<td width='10' style='border-bottom:solid 1px #000' class='preto'><input type='checkbox' value='".$obj->db_data["ID"].";".urlencode($obj->db_data["Cabecalho"])."' class='prodclass_".$GLOBALS['DESCONTOf']."' id='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."'name='prodclass_".$GLOBALS['DESCONTOf']."_".$obj->db_data["ID"]."' /> </td>
				<td style='border-bottom:solid 1px #000'>" . $obj->db_data["Cabecalho"]."</div></td>
				</tr>
				";

				$obj->db_next();
			}

			$return .= '</table>';
		}
		$obj->db_close();

		return array('list_prods' => $return, 'total_prod' => $total, 'total_prd_prom' => $total2);

		db_closedb_conection($db_link);
	}
}
function gera_lista_produtos2($id_org=0,$space=1, $line='',$bloco_id=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$ID;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj = new DataObj;

	$fields = db_list_fields($DB_name,'nl_newsletter_template_prods',$db_link);
	$columns = db_num_fields($fields);
	for ($i=0; $i < $columns; $i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('bloco_id',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE nl_newsletter_template_prods ADD bloco_id INT(11) NOT NULL');

	$sql = "
			SELECT pf.PROD_ID as ID, pf.PRODUTO_TXT as Cabecalho
			FROM nl_newsletter_template_prods pf
			WHERE
				pf.NL_ID='".$id_org."'
				AND pf.bloco_id='".$bloco_id."'
			ORDER BY
				 pf.PRODUTO_TXT ASC
			";
	$obj->db_open($db_link, $DB_name, $sql);
    while(!$obj->EOF())
	{
		$return .= '
					<label class="label_check2" style="padding-left:20px;padding-top:1px;font-size:11px;margin-left:'.$space.'px" for="offer_fam_'.$obj->db_data["ID"].'_'.$line.'">
					<div style="float:left;width:13px"><input type="checkbox" id="offer_fam_'.$obj->db_data["ID"].'_'.$line.'" name="offer_fam_'.$obj->db_data["ID"].'_'.$line.'" value="'.$obj->db_data["ID"].'"';
		$return .= ' /></div>';

		$line2 = str_replace('.','_',$line);

		$return .= '
		<div style="float:left;width:450px">'.htmlentities_memo(stripslashes($obj->db_data["Cabecalho"])).'<div class="clearboth"></div></div>
		<div style="float:left"><a href="#" onclick="del_prod(\''.$line2.'\',\''.$line.'\',\''.$id_org.'\',\''.$obj->db_data["ID"].'\',\'3\',\''.$bloco_id.'\');return false;"><img src="img/delete_small.png" border="0" /></a></div><div class="clearboth"></div></label><div class="clearboth"></div>';

		// echo $obj->db_data["Cabecalho"].'<br />';
	    // $return .= gera_lista_familias($obj->db_data["ID"], ($space+10));
        $obj->db_next();
    }
    $obj->db_close();


	return $return;

}
function getNewsleterPreview($ID, $acao='',$width='700')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$website_url_email,$arr_fonts;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;	$rs2 = new DataObj;
	$rs->db_open($db_link,$DB_name, "SELECT * FROM nl_newsletter_template WHERE ID_ORG=$ID order by Ordem ASC");
	$html_gerado = '';
	$css = '<style>';
	if(!$rs->EOF())
	{
		if($acao==='edit') {
			$tt = 'border="1"';
			$csp = '3';
		}
		else{
			$tt='';
			$csp = '0';
		}



		while(!$rs->EOF())
		{
			$IDb=$rs->db_data["ID"];
			$ID_ORG=$rs->db_data["ID_ORG"];
			$Tipo=$rs->db_data["Tipo"];
			$campo1=nl2br($rs->db_data["Texto1"]);
			$campo2=nl2br($rs->db_data["Texto2"]);
			$campo3=nl2br($rs->db_data["Texto3"]);
			$Cor=$rs->db_data["Cor1"];
			$Cor2=$rs->db_data["Cor2"];
			$Cor3=$rs->db_data["Cor3"];
			$Bg=$rs->db_data["Bg1"];
			$Bg2=$rs->db_data["Bg2"];
			$Bg3=$rs->db_data["Bg2"];
			$I1=$rs->db_data["img_ext"];
			$I2=$rs->db_data["img2_ext"];
			$I3=$rs->db_data["img3_ext"];
			$I4=$rs->db_data["img4_ext"];
			$Ordem=$rs->db_data["Ordem"];
			$link=$rs->db_data["Link1"];
			$link2=$rs->db_data["Link2"];
			$link3=$rs->db_data["Link3"];

			$align=$rs->db_data["Align1"];
			$align2=$rs->db_data["Align2"];
			$align3=$rs->db_data["Align3"];

			$valign=$rs->db_data["VAlign1"];
			$valign2=$rs->db_data["VAlign2"];
			$valign3=$rs->db_data["VAlign3"];

			$font=$rs->db_data["Font1"];
			if(!$font) $font = $arr_fonts[1];
			$font2=$rs->db_data["Font2"];
			if(!$font2) $font2 = $arr_fonts[1];
			$font3=$rs->db_data["Font3"];
			if(!$font3) $font3 = $arr_fonts[1];

			$cssfont ='font-family:'.$font.';';
			$cssfont2 ='font-family:'.$font2.';';
			$cssfont3 ='font-family:'.$font3.';';
			$csscor='';
			$csscor2='';
			$csscor3='';
			$cssbgcor='';
			$cssbgcor2='';
			$cssbgcor3='';
			$urlimg1='';
			$urlimg2='';
			$urlimg3='';
			if($Cor) $csscor ='color:#'.$Cor.';';
			if($Cor2) $csscor2 ='color:#'.$Cor2.';';
			if($Cor3) $csscor3 ='color:#'.$Cor3.';';
			if($Bg) $cssbgcor =' bgcolor="#'.$Bg.'" ';
			if($Bg2) $cssbgcor2 =' bgcolor="#'.$Bg2.'" ';
			if($Bg3) $cssbgcor3 =' bgcolor="#'.$Bg3.'" ';



			if(!$align) $align='left';
			if(!$align2) $align2='left';
			if(!$align3) $align3='left';
			if(!$valign) $valign='top';
			if(!$valign2) $valign2='top';
			if(!$valign3) $valign3='top';

			$css.='.c_'.$IDb.'1{'.$cssfont.''.$csscor.'}';
			$css.='.c_'.$IDb.'1 a{'.$csscor.'}';
			$css.='.c_'.$IDb.'1 a:visited{'.$csscor.'}';
			$css.='.c_'.$IDb.'1 a:link{'.$csscor.'}';
			$css.='.c_'.$IDb.'1 a:active{'.$csscor.'}';
			$css.='.c_'.$IDb.'2{'.$cssfont2.''.$csscor2.'}';
			$css.='.c_'.$IDb.'2 a{'.$csscor2.'}';
			$css.='.c_'.$IDb.'2 a:visited{'.$csscor2.'}';
			$css.='.c_'.$IDb.'2 a:link{'.$csscor2.'}';
			$css.='.c_'.$IDb.'2 a:active{'.$csscor2.'}';
			$css.='.c_'.$IDb.'3{'.$cssfont3.''.$csscor3.'}';
			$css.='.c_'.$IDb.'3 a{'.$csscor3.'}';
			$css.='.c_'.$IDb.'3 a:visited{'.$csscor3.'}';
			$css.='.c_'.$IDb.'3 a:link{'.$csscor3.'}';
			$css.='.c_'.$IDb.'3 a:active{'.$csscor3.'}';

			if($I1) $urlimg1=$website_url_email."files/news_temp/".$IDb."_1.".$I1;
			if($I2) $urlimg2=$website_url_email."files/news_temp/".$IDb."_2.".$I2;
			if($I3) $urlimg3=$website_url_email."files/news_temp/".$IDb."_3.".$I3;

			if($acao==='edit'){
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td colspan="3" valign="middle"  align="right">
				<hr style="border:none;height:2px;background:#000"/><br/>
				</td>
				</tr>
				<tr>
				<td colspan="3" valign="middle" style="background:#AFAFAF" align="right">
				<table width="100%" border="0" cellspacing="0" cellpadding="1">
				<tr>
				<td valign="middle" align="left"><b>'.$Ordem.'-'.$GLOBALS['arr_opt'][$Tipo].'</b></td>
				<td valign="middle" width="1"><input type="button" value="Editar Bloco" name="Submit" class="btn btn-primary" onclick="window.open(\'newsletter_template.php?task2=editar&ID='.$ID_ORG.'&ID_Linha='.$IDb.'&Template=1&dump='.uniqid().'\',\'_self\')" /></td>
				<td valign="middle" width="1"><input type="button" value="Remover Bloco" name="Submit" class="btn btn-primary" onclick="window.open(\'newsletter_template.php?task2=eliminar&ID='.$ID_ORG.'&ID_Linha='.$IDb.'&Template=1&dump='.uniqid().'\',\'_self\')" /></td>
				</tr>
				</table>
				</td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==1)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='
				<tr>
				<td colspan="3" '.$cssbgcor.' class="c_'.$IDb.'1" valign="'.$valign.'" align="'.$align.'"><p style="text-align:'.$align.';margin:15px 0">';
				if($link!='') $html_gerado.='<a href="'.$link.'">'.$campo1.'</a>';
				else $html_gerado.=$campo1;
				$html_gerado.='</p></td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==2)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='
				<tr>
				<td colspan="3" '.$cssbgcor.' class="c_'.$IDb.'1"  valign="'.$valign.'" align="'.$align.'"><p style="text-align:'.$align.';margin:15px 0">';
				if($link!='') $html_gerado.='<a href="'.$link.'">'.$campo1.'</a>';
				else $html_gerado.=$campo1;
				$html_gerado.='</p></td>
				</td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==3)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='
				<tr>
				<td colspan="3" '.$cssbgcor.' valign="'.$valign.'" align="'.$align.'">';
				if($link!='') $html_gerado.='<a href="'.$link.'"><img style="display:block;" border="0" alt="" src="'.$urlimg1.'"></a>';
				else $html_gerado.='<img style="display:block;" border="0" alt="" src="'.$urlimg1.'">';
				$html_gerado.='</td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==4)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td width="345" '.$cssbgcor.' class="c_'.$IDb.'1" valign="'.$valign.'" align="'.$align.'"><p style="text-align:'.$align.';margin:15px 0">';
				if($link!='') $html_gerado.='<a href="'.$link.'">'.$campo1.'</a>';
				else $html_gerado.=$campo1;
				$html_gerado.='</p></td>
				<td width="10" valign="middle">&nbsp;</td>
				<td width="345" '.$cssbgcor2.' valign="'.$valign2.'" align="'.$align2.'" >';
				if($link2!='') $html_gerado.='<a href="'.$link2.'"><img border="0" style="display:block;" alt="" src="'.$urlimg1.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg1.'">';
				$html_gerado.='</td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==5)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td width="345" '.$cssbgcor2.' valign="'.$valign2.'" align="'.$align2.'" >';
				if($link2!='') $html_gerado.='<a href="'.$link2.'"><img border="0" style="display:block;" alt="" src="'.$urlimg1.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg1.'">';
				$html_gerado.='</td>
				<td width="10" valign="middle">&nbsp;</td>
				<td width="345" '.$cssbgcor.' class="c_'.$IDb.'1" valign="'.$valign.'" align="'.$align.'"><p style="text-align:'.$align.';margin:15px 0">';
				if($link!='') $html_gerado.='<a href="'.$link.'">'.$campo1.'</a>';
				else $html_gerado.=$campo1;
				$html_gerado.='</p></td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==6)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td width="345" '.$cssbgcor.' valign="'.$valign.'" align="'.$align.'">';
				if($link!='') $html_gerado.='<a href="'.$link.'"><img border="0" style="display:block;" alt="" src="'.$urlimg1.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg1.'">';
				$html_gerado.='</td>
				<td width="10" valign="middle" >&nbsp;</td>
				<td width="345" '.$cssbgcor2.' valign="'.$valign2.'" align="'.$align2.'">';
				if($link2!='') $html_gerado.='<a href="'.$link2.'"><img border="0" style="display:block;" alt="" src="'.$urlimg2.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg2.'">';
				$html_gerado.='</td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==7)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td width="345" '.$cssbgcor.' class="c_'.$IDb.'1" valign="'.$valign.'" align="'.$align.'"><p style="text-align:'.$align.';margin:15px 0">';
				if($link!='') $html_gerado.='<a href="'.$link.'">'.$campo1.'</a>';
				else $html_gerado.=$campo1;
				$html_gerado.='</p></td>
				<td width="10" valign="middle">&nbsp;</td>
				<td width="345" '.$cssbgcor2.'  class="c_'.$IDb.'2" valign="'.$valign2.'" align="'.$align2.'"><p style="text-align:'.$align2.';margin:15px 0">';
				if($link2!='') $html_gerado.='<a href="'.$link2.'">'.$campo2.'</a>';
				else $html_gerado.=$campo2;
				$html_gerado.='</p></td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==8)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td	'.$cssbgcor.' valign="middle" colspan="3" align="center"><a href="'.$GLOBALS['site_http_url'].'show_newsletter.php?ID='.$ID_ORG.'"><img src="'.$GLOBALS['site_http_url'].'layout/newsletter_templates/topo.png" border="0" /></a></td>
				</tr>';
				$html_gerado.='</table>';
			}
			if($Tipo==9 && $campo1 > 0)
			{
				$sql = 'SELECT PROD_ID FROM nl_newsletter_template_prods WHERE NL_ID="'.$_GET['ID'].'" AND bloco_id="'.$IDb.'"';
				$rs2->db_open($db_link,$DB_name, $sql);
				$total = $rs2->db_rows;
				if($campo1<=0) $campo1 = 5;
				$t = $campo1; $t2 = 0;
				$html_gerado2 = '';

				if(!$rs2->EOF())
				{


					$width2 = ((1 * $width) / $campo1);
					// echo $width2;
					if($width2 <= 0) $width2 = '20%';
					$html_gerado2 .= '<table width="100%" border="0" cellspacing="0" cellpadding="0"><tr>';



					while(!$rs2->EOF())
					{
						$prods = get_produtc_info($rs2->db_data['PROD_ID']);

						if($t==$t2)
						{
							$html_gerado2 .= '</tr><tr>';
							$t2=0;
						}

						$html_gerado2 .= '<td width="'.$width2.'">';
						$html_gerado2 .= '<table border="0" cellspacing="0" cellpadding="0">';
						$html_gerado2 .= '<tr><td align="center" height="140" valign="middle"><a href="'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$rs2->db_data['PROD_ID'], $rs2->db_data['PROD_ID'], 2).'"><img src="'.str_replace('files/', $GLOBALS['site_http_url'].'files/', $prods['prd_imgm_new']).'" border="0" alt="" /></a></td></tr>';
						$html_gerado2 .= '<tr><td align="center" height="100"><a href="'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$rs2->db_data['PROD_ID'], $rs2->db_data['PROD_ID'], 2).'" style="color: #000000; font-size: 13px; text-align: center; text-decoration: none;font-weight:bold;font-family:arial,sans-serif">'.$prods['prod_nome'].'</a></td></tr>';
						$html_gerado2 .= '<tr><td style="color: #EC1C24;font-size: 14px; height: 20px;text-align: center;font-family:arial,sans-serif">'.numberformat($prods['prod_precos']['PVP']).'</td></tr>';
						$html_gerado2 .= '<tr><td style="color: #EC1C24;font-size: 14px; height: 20px;text-align: center;font-family:arial,sans-serif" height="30">&nbsp;</td></tr>';
						$html_gerado2 .= '</table>';
						$html_gerado2 .= '</td>';

						$t2++;
						$rs2->db_next();
					}
						$html_gerado2 .= '</tr>';
						$html_gerado2 .= '</table>';
				}
				$rs2->db_close();


				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado .= '<tr><td colspan="3" '.$cssbgcor.' valign="middle" align="center">';

				$html_gerado .= '<table border="0" cellspacing="0" cellpadding="0">';
				$html_gerado .= '<tr>';
				$html_gerado .= '<td valign="top" align="center">';

				$html_gerado .= $html_gerado2;

				$html_gerado .= '</td>';
				$html_gerado .= '</tr>';
				$html_gerado .= '</table></td></tr>';
				$html_gerado.='</table>';

			}
			if($Tipo==10)
			{
				$html_gerado .= '<table cellspacing="0" cellpadding="'.$csp.'" width="'.$width.'" '.$tt.' align="center" bordercolor="#FFFFFF">';
				$html_gerado.='<tr>
				<td	valign="top" colspan="3" align="center">';
				$html_gerado.='<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr>
				<td width="226" '.$cssbgcor.' valign="'.$valign.'" align="'.$align.'">';
				if($link!='') $html_gerado.='<a href="'.$link.'"><img border="0" style="display:block;" alt="" src="'.$urlimg1.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg1.'">';
				$html_gerado.='</td>
				<td width="10" valign="middle" >&nbsp;</td>
				<td width="228" '.$cssbgcor2.' valign="'.$valign2.'" align="'.$align2.'">';
				if($link2!='') $html_gerado.='<a href="'.$link2.'"><img border="0" style="display:block;" alt="" src="'.$urlimg2.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg2.'">';
				$html_gerado.='</td>
				<td width="10" valign="middle" >&nbsp;</td>
				<td width="226" '.$cssbgcor3.' valign="'.$valign3.'" align="'.$align3.'">';
				if($link3!='') $html_gerado.='<a href="'.$link3.'"><img border="0" style="display:block;" alt="" src="'.$urlimg3.'"></a>';
				else $html_gerado.='<img border="0" style="display:block;" alt="" src="'.$urlimg3.'">';
				$html_gerado.='</td>
				</tr></table>';
				$html_gerado.='</td></tr>';
				$html_gerado.='</table>';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();
	$css.='</style>';

	$html_gerado=$css.$html_gerado;

	return $html_gerado;
}

function generate_form($campos=array(), $task2='Inserir Bloco', $task='inserir')
{
	global $arr_fonts;
	$ID = $campos['ID'];
	$ID_ORG = $campos['ID_ORG'];

	$Tipo = $campos['Tipo'];
	$ordem = $campos['Ordem'];

	$campo1 = $campos['campo1'];
	$campo2 = $campos['campo2'];
	$campo3 = $campos['campo3'];

	$Cor = $campos['Cor'];
	$Cor2 = $campos['Cor2'];
	$Cor3 = $campos['Cor3'];

	$Bg = $campos['Bg'];
	$Bg2 = $campos['Bg2'];
	$Bg3 = $campos['Bg3'];

	$I1 = $campos['I1'];
	$I2 = $campos['I2'];
	$I3 = $campos['I3'];

	$link = $campos['link'];
	$link2 = $campos['link2'];
	$link3 = $campos['link3'];
	$align = $campos['align'];
	$align2 = $campos['align2'];
	$align3 = $campos['align3'];
	$valign = $campos['valign'];
	$valign2 = $campos['valign2'];
	$valign3 = $campos['valign3'];
	$font = $campos['font'];
	$font2 = $campos['font2'];
	$font3 = $campos['font3'];

	// echo $Tipo.'<br />';
	if($Tipo==1)
	{
	?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
		<tr>
		<td width="5px"></td>
		<td valign="middle" align="left" class="t1">Texto Curto</td>
		</tr>
		</table>

		<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="3" height="10"></td>
		<td width="138" align="left" valign="middle"></td>
		<td align="left" valign="middle" width="578"></td>
		<td width="6"></td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
		<td align="left" valign="middle" width="578">
		<input  id="ordem" name="ordem" style="WIDTH: 40px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte:</td>
			<td align="left" valign="middle" width="578">
				<select name="font">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font) $font=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Texto:</td>
		<td align="left" valign="middle" width="578">
		<input name="campo1" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $campo1;?>">
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Cor Texto:</td>
		<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg" name="texto_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
		<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		</table>
	<?php
	}

	if($Tipo==2)
	{
	?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
		<tr>
		<td width="5px"></td>
		<td valign="middle" align="left" class="t1">Texto Longo</td>
		</tr>
		</table>

		<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="3" height="10"></td>
		<td width="138" align="left" valign="middle"></td>
		<td align="left" valign="middle" width="578"></td>
		<td width="6"></td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
		<td align="left" valign="middle" width="578">
		<input id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte:</td>
			<td align="left" valign="middle" width="578">
				<select name="font">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font) $font=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Texto:</td>
		<td align="left" valign="middle" width="578"> <textarea name="campo1"  style=" WIDTH: 100%; HEIGHT: 150px" ><?php echo $campo1;?></textarea></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Cor Texto:</td>
		<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg" name="texto_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
		<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		</table>
	<?php
	}

	if($Tipo==3)
	{
	?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
		<tr>
		<td width="5px"></td>
		<td valign="middle" align="left" class="t1">Imagem</td>
		</tr>
		</table>

		<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
		<tr>
		<td width="3" height="10"></td>
		<td width="138" align="left" valign="middle"></td>
		<td align="left" valign="middle" width="578"></td>
		<td width="6"></td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
		<td align="left" valign="middle" width="578">
		<input  id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Imagem:</td>
		<td align="left" valign="middle" width="578"><?
		if($I1!="" && file_exists('../../files/news_temp/'.$ID.'_1.'.$I1))
		{
		?>
			<div style="max-width:578px;max-height:200px;overflow:auto;"><img alt="" src="<?php echo '../../files/news_temp/'.$ID.'_1.'.$I1;?>" align="absmiddle" border="0" /></div><?
			if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img1' value='1'> ou Alterar <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
		}
		else{?>Nova: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?><br>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
		<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		</table>
	<?
	}
	if($Tipo==4)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Texto à Esquerda + Imagem à direita</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578">
	<input  id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte:</td>
			<td align="left" valign="middle" width="578">
				<select name="font">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font) $font=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Texto:</td>
	<td align="left" valign="middle" width="578"> <textarea name="campo1"  style="WIDTH: 100%; HEIGHT: 150px" ><?php echo $campo1;?></textarea></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Texto:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg" name="texto_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
	<td align="left" valign="middle" width="578">
		<select name="valign">
			<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
			<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
			<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
		</select>
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Link:</td>
	<td align="left" valign="middle" width="578">
	<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem:</td>
	<td align="left" valign="middle" width="578"><?
	if($I1!="")
	{
	?>
	<div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo '../../files/news_temp/'.$ID.'_1.'.$I1;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img1' value='1'> ou Alterar <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg2" name="cor_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="align2">
				<option value="left" <?php if(!$align2 || $align2=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align2=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align2=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align2=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign2">
				<option value="top" <?php if(!$valign2 || $valign2=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign2=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign2=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link2:</td>
		<td align="left" valign="middle" width="578">
		<input name="link2" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link2;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	</table>
	<?
	}
	if($Tipo==5)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Texto à direita + Imagem à esquerda</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578">
	<input  id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte:</td>
			<td align="left" valign="middle" width="578">
				<select name="font">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font) $font=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Texto:</td>
	<td align="left" valign="middle" width="578"> <textarea name="campo1"  style="WIDTH: 100%; HEIGHT: 150px" ><?php echo $campo1;?></textarea></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Texto:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg" name="texto_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem:</td>
	<td align="left" valign="middle" width="578"><?
	if($I1!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo '../../files/news_temp/'.$ID.'_1.'.$I1;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img1' value='1'> ou Alterar <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg2" name="cor_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="align2">
				<option value="left" <?php if(!$align2 || $align2=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align2=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align2=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align2=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign2">
				<option value="top" <?php if(!$valign2 || $valign2=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign2=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign2=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link2:</td>
		<td align="left" valign="middle" width="578">
		<input name="link2" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link2;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	</table>
	<?
	}
	if($Tipo==6)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Duas imagens</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578">
	<input id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem 1:</td>
	<td align="left" valign="middle" width="578" ><?
	if($I1!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo '../../files/news_temp/'.$ID.'_1.'.$I1;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img1' value='1'> ou Alterar <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 1:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem 2:</td>
	<td align="left" valign="middle" width="578" ><?
	if($I2!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo "../../files/news_temp/$ID"."_2.".$I2;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img2' value='1'> ou Alterar <input type='file' name='img_peq2'> <input type='hidden' name='tem_foto2' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq2'> <input type='hidden' name='tem_foto2' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg2" name="cor_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="align2">
				<option value="left" <?php if(!$align2 || $align2=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align2=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align2=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align2=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign2">
				<option value="top" <?php if(!$valign2 || $valign2=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign2=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign2=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link2:</td>
		<td align="left" valign="middle" width="578">
		<input name="link2" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link2;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	</table>
	<?
	}
	if($Tipo==7)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Dois blocos de texto</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578">
	<input id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte:</td>
			<td align="left" valign="middle" width="578">
				<select name="font">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font) $font=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Texto 1:</td>
	<td align="left" valign="middle" width="578"> <textarea name="campo1"  style="WIDTH: 100%; HEIGHT: 150px" ><?php echo $campo1;?></textarea></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Texto 1:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg" name="texto_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 1:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
			<td height="25" width="3">&nbsp;</td>
			<td class="preto" align="left" valign="middle" width="138">Fonte 2:</td>
			<td align="left" valign="middle" width="578">
				<select name="font2">
				<?php
				for ($f=1; $f <= count($arr_fonts); $f++)
				{
					if(!$font2) $font2=$arr_fonts[0];?>
					<option value="<?=$arr_fonts[$f]?>" <?php if($font2==$arr_fonts[$f]) echo 'selected';?>><?=$arr_fonts[$f]?></option>
				<?php }?>
				</select>
			</td>
			<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Texto 2:</td>
	<td align="left" valign="middle" width="578"> <textarea name="campo2"  style=" WIDTH: 100%; HEIGHT: 150px" ><?php echo $campo2;?></textarea></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Texto 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="texto_bg2" name="texto_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Cor2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg2" name="cor_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="align2">
				<option value="left" <?php if(!$align2 || $align2=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align2=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align2=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align2=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign2">
				<option value="top" <?php if(!$valign2 || $valign2=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign2=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign2=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link2:</td>
		<td align="left" valign="middle" width="578">
		<input name="link2" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link2;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	</table>
	<?
	}
	if($Tipo==8)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Problemas para visualizar esta newsletter corretamente? Veja aqui </td>
	</tr>
	</table>
	<?php
	}
	if($Tipo==9)
	{

	$bloco_id = (isset($_GET['ID_Linha']) && $_GET['ID_Linha'] > 0) ? $_GET['ID_Linha'] : rand();
	$prods_list_offers = gera_lista_produtos2($_GET['ID'],1,'',$bloco_id);
	?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Produtos</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578"><input type="hidden" id="ID_Linhap" name="ID_Linhap" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $bloco_id?>" />
	<input id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo:</td>
	<td align="left" valign="middle" width="578"> <input class="color" type="text" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" value="<?php echo $cor_bg;?>" /></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Nº de produtos por linha:</td>
	<td align="left" valign="middle" width="578"> <input type="text" name="campo1" style="WIDTH: 80px; HEIGHT: 20px" value="<?php echo $campo1;?>" /></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="top" width="138"><br /><br />Escolher Produtos:</td>
	<td align="left" valign="middle" width="578">
	<table width="400" cellpadding="1" cellspacing="0" border="0">
	<tr>
	<td align="left" colspan="10">
	<div class="boardbt" href="#" onclick="op_t('pesq_<?php echo $DESCONTOf?>')" style="position:relative; width:500px"></div>
	<div style="position:relative">
	<div style="position:absolute;top:5px;left:5px;width:392px;height:200px;background:#e9e9e9;display:none" id="pesq_<?php echo $DESCONTOf?>">
	<div style="float:left;width:320px;text-align:center;margin-top:2px"><input type="text" onfocus="if(this.value=='Pesquisar Produto') this.value='';" name="pesquisa<?php echo $DESCONTOf?>" id="pesquisa<?php echo $DESCONTOf?>" class="form-control" value="Pesquisar Produto" style="width:300px;margin:0 auto" /></div>
	<div style="float:left"><a href="#" onclick="pesquisa_prods('<?php echo $DESCONTOf?>','<?php echo $_GET['ID']?>','3','<?php echo $bloco_id?>');return false;"><img src="img/reload.png" border="0" height="27" /></a></div>
	<div class="clearboth"></div>
	<div style="margin:10px; height:150px;overflow:auto" id="res_<?php echo $DESCONTOf?>">&nbsp;</div>
	</div>
	</div>
	<div id="products_offer" class="board" style="width:500px"><?php echo $prods_list_offers;?></div>
	</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="4">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	</table>
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	</table>
	<?
	}
	if($Tipo==10)
	{?>
	<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
	<tr>
	<td width="5px"></td>
	<td valign="middle" align="left" class="t1">Três imagens</td>
	</tr>
	</table>
	<table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="0">
	<tr>
	<td width="3" height="10"></td>
	<td width="138" align="left" valign="middle"></td>
	<td align="left" valign="middle" width="578"></td>
	<td width="6"></td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Ordem:</td>
	<td align="left" valign="middle" width="578">
	<input id="ordem" name="ordem" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $ordem?>">
	</td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem 1:</td>
	<td align="left" valign="middle" width="578" ><?
	if($I1!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo '../../files/news_temp/'.$ID.'_1.'.$I1;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img1' value='1'> ou Alterar <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 1:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg" name="cor_bg" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align">
				<option value="left" <?php if(!$align || $align=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign">
				<option value="top" <?php if(!$valign || $valign=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem 2:</td>
	<td align="left" valign="middle" width="578" ><?
	if($I2!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo "../../files/news_temp/$ID"."_2.".$I2;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img2' value='1'> ou Alterar <input type='file' name='img_peq2'> <input type='hidden' name='tem_foto2' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq2'> <input type='hidden' name='tem_foto2' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 2:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg2" name="cor_bg2" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg2;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="align2">
				<option value="left" <?php if(!$align2 || $align2=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align2=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align2=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align2=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical 2:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign2">
				<option value="top" <?php if(!$valign2 || $valign2=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign2=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign2=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link2:</td>
		<td align="left" valign="middle" width="578">
		<input name="link2" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link2;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Imagem 3:</td>
	<td align="left" valign="middle" width="578" ><?
	if($I3!="")
	{?><div style="max-width:578px;max-height:200px;overflow:auto;"><img  alt="" src="<?php echo '../../files/news_temp/'.$ID.'_3.'.$I3;?>" align="absmiddle" border=0></div><?
	if($task!="apagar"){?>Apagar <input type='checkbox' name='Apagar_img3' value='1'> ou Alterar <input type='file' name='img_peq3'> <input type='hidden' name='tem_foto3' value='1'><? }
	}
	else{?>Nova: <input type=file name='img_peq3'> <input type='hidden' name='tem_foto3' value='0'><? }?><br></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
	<td height="25" width="3">&nbsp;</td>
	<td class="preto" align="left" valign="middle" width="138">Cor Fundo 3:</td>
	<td align="left" valign="middle" width="578"> <input class="color" id="cor_bg3" name="cor_bg3" style="WIDTH: 80px; HEIGHT: 20px" maxlength="255" value="<?php echo $Bg3;?>"></td>
	<td width="6">&nbsp;</td>
	</tr>
	<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Horizontal:</td>
		<td align="left" valign="middle" width="578">
			<select name="align3">
				<option value="left" <?php if(!$align3 || $align3=='left') echo 'selected';?>>Esquerda</option>
				<option value="center" <?php if($align3=='center') echo 'selected';?>>Centrado</option>
				<option value="right" <?php if($align3=='right') echo 'selected';?>>Direita</option>
				<option value="justify" <?php if($align3=='justify') echo 'selected';?>>Justificado</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Alinhamento Vertical:</td>
		<td align="left" valign="middle" width="578">
			<select name="valign3">
				<option value="top" <?php if(!$valign3 || $valign3=='top') echo 'selected';?>>Topo</option>
				<option value="middle" <?php if($valign3=='middle') echo 'selected';?>>Centrado</option>
				<option value="bottom" <?php if($valign3=='bottom') echo 'selected';?>>Fundo</option>
			</select>
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">Link:</td>
		<td align="left" valign="middle" width="578">
		<input name="link3" style=" WIDTH: 100%; HEIGHT: 20px" maxlength="255" value="<?php echo $link3;?>"></td>
		<td width="6">&nbsp;</td>
		</tr>
	<tr>
	</table>
	<?
	}

	if($Tipo > 0)
	{
	?>
		<table width="100%" cellspacing="0" cellpadding="0" border="0" class="p1">
		<tr>
		<td height="25" width="3">&nbsp;</td>
		<td class="preto" align="left" valign="middle" width="138">&nbsp;</td>
		<td align="left" valign="middle" width="578">
		<?php if($task2=='altera') {?>
		<input type="button" value=" <?php echo $task?> " name="Submit" class="btn btn-primary" onclick="window.open('newsletter_template.php?task2=editar&ID=<?=$ID_ORG?>&ID_Linha=<?=$ID?>&Template=1&dump=<?=uniqid()?>','_self')" />
		<?php }else{?>
		<input type="submit" value=" <?php echo $task?> " name="Submit" class="btn btn-primary" />
		<?php }?>
		<input class="btn btn-secondary" type="button" name="cancelar" value=" cancelar " onclick="window.open('newsletter_template.php?ID=<?php echo $ID_ORG?>','_self')" />
		</td>
		<td width="6">&nbsp;</td>
		</tr>
		</table>
	<?php
	}
}

function show_page_config()
{
	global $browser,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Configurações" onclick="javascript:window.open('config.php?dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }

function show_page_tools($filter=0)
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td width=29 class="text-center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1"  class="cb"></td>
		<td width="10px" class="text-center"></td>
		<td width="91px" class="t1">Pesquisar:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="Submeter pesquisa" class="btn btn-primary" ></td>
		<td width="10">&nbsp;</td>
        <td width="91px" class="t1">&nbsp;ordenar por:</td>
        <td><select onChange="document.lista_paginas.submit()" name="tipos" style="width:120px"><option value="0">data</option><option value="1" <? if($tipos==1) echo 'selected="selected"';?>>nome</option></select></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">NEWSLETTER</h4>
        </div>
    </div>
	<table width="100%" height="25px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
    <td align="center">
    	<a class="btn btn-primary" href="newsletter.php?ID=0">Criar Nova Newsletter</a>
    	<a class="btn btn-primary" href="newsletter_template.php?ID=0&TEMPLATE=1">Criar Nova Newsletter Template</a>
    </td>
    </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<?

	$path="../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from nl_newsletter order by ID";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$descricao=$rs->db_data["Cabecalho"];
		$titulo=$rs->db_data["Titulo"];
		$Template=$rs->db_data["Template"];
		if(!$descricao) $descricao="N/A";
		if(!$titulo) $titulo="N/A";

		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$i=0;
		?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
		<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
			<td width="6" class="text-center"></td>

            <td width="4" align="right"><a class="italico" href="newsletter_send/index.php?nova=1&ID_News=<?=$IDb?>&dump=<?=uniqid("");?>'"><img src="../images/arrow.png" width="16" height="16" border="0" align="absmiddle"></a></td>
            <td width="6" class="text-center"></td>
            <td width="4" align="right"><a class="italico" href="newsletter.php?ID=<?=$IDb?>"><img src="../images/edit.png" width="16" height="16" border="0" align="absmiddle"></a></td>
            <td style="text-align:left"><a class="italico" href="newsletter.php?ID=<?=$IDb?>">&nbsp;<? echo ($descricao);?> - <? echo ($titulo);?></a></td>

            <? if($Template) {?><td style="text-align:left;width:100px"><a class="italico" href="newsletter_template.php?task2=altera&ID=<?=$IDb?>&Template=<?=$Template?>">&nbsp;Editar template</a></td><? }?>

            <td width="25" style="text-align:left">&nbsp;<a class="italico" title="Configurar newsletter" href="newsletter.php?ID=<?=$IDb?>&amp;task=alterar"><img src="../images/config.png" border="0" align="absmiddle"></a></td>
            <td width="25" align="right" class=preto style="text-align:left"><a class="italico" title="Pré-visualizar newsletter" target="_blank" href="../../show_newsletter.php?ID=<?=$IDb?>&amp;prev=1"><img src="../images/computer_16.png" border="0" align="absmiddle"></a></td>

            <td width="72" align="right" class=preto style="text-align:left"><a class="italico" href="newsletter.php?ID=<?=$IDb?>&amp;task=copiar&amp;Dump=<? echo uniqid("");?>"><img title="duplicar pagina" src="duplicar.png" border=0></a></td>
            <td width="100" align="right" class=preto style="text-align:left"><a class="italico" href="newsletter.php?ID=<?=$IDb?>&amp;task=apagar&amp;Dump=<? echo uniqid("");?>"><img title="apagar pagina" src="del.png" border=0></a></td>

		</tr>
		</table><?
		$rs->db_next();
		$i++;
	}
	$rs->db_close();
}
function InsertNewRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_POST['cabecalho'])) $cabecalho=$_POST['cabecalho'];
	if(isset($_POST['assunto'])) $assunto=$_POST['assunto'];
	if(isset($_POST['texto'])) $texto=$_POST['texto'];
	if(isset($_POST['SMTP'])) $SMTP=$_POST['SMTP'];
	if(isset($_POST['SMTP_Email'])) $SMTP_Email=$_POST['SMTP_Email'];
	if(isset($_POST['SMTP_login'])) $SMTP_login=$_POST['SMTP_login'];
	if(isset($_POST['SMTP_pass'])) $SMTP_pass=$_POST['SMTP_pass'];

	if(isset($_POST['EmailToReply'])) $EmailToReply=$_POST['EmailToReply'];
	if(isset($_POST['cabecalhoToReply'])) $cabecalhoToReply=$_POST['cabecalhoToReply'];

	if(isset($_POST['n_titulo'])) $Titulo=$_POST['n_titulo'];
	if(isset($_POST['TEMPLATE'])) $template=$_POST['TEMPLATE'];

	if($_POST['slashes']=="'")
	{
		$cabecalho=addslashes($cabecalho);
		$assunto=addslashes($assunto);
		$texto=addslashes($texto);
		$Titulo=addslashes($Titulo);

	}

	$data=date("Y-m-d");
	$sql="insert into nl_newsletter (Data,Cabecalho,Assunto,SMTP,SMTP_Email,SMTP_login,SMTP_pass,EmailToReply,cabecalhoToReply,Titulo,Template,Texto) VALUES ('$data','$cabecalho','$assunto','$SMTP','$SMTP_Email','$SMTP_login','$SMTP_pass','$EmailToReply','$cabecalhoToReply','$Titulo','$template','$texto')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	else{
		$img_path=$site_shell_url."files/news_temp/";
		if(isset($_POST['ID']))
		{
			$new_id=db_get_last_insert_id($db_link);
			$IDx=$_POST['ID'];
			$rs=new DataObj;
			$sql="SELECT * from nl_newsletter_template WHERE ID_ORG=".$IDx;
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			$i=0;
			while(!$rs->EOF())
			{
				$line = $rs->db_data['ID'];
				$campo1 	= $rs->db_data['Texto1'];
				$campo2 	= $rs->db_data['Texto2'];
				$campo3 	= $rs->db_data['Texto3'];
				$texto_bg 	= $rs->db_data['Cor1'];
				$texto_bg2 	= $rs->db_data['Cor2'];
				$texto_bg3 	= $rs->db_data['Cor3'];
				$cor_bg 	= $rs->db_data['Bg1'];
				$cor_bg2	= $rs->db_data['Bg2'];
				$cor_bg3	= $rs->db_data['Bg3'];
				$grava_tipo = $rs->db_data['Tipo'];
				$img_ext 	= $rs->db_data['img_ext'];
				$img_ext2	= $rs->db_data['img2_ext'];
				$img_ext3	= $rs->db_data['img3_ext'];
				$link1=$rs->db_data["Link1"];
				$link2=$rs->db_data["Link2"];
				$link3=$rs->db_data["Link3"];
				$font1=$rs->db_data["Font1"];
				$font2=$rs->db_data["Font2"];
				$font3=$rs->db_data["Font3"];
				$align1=$rs->db_data["Align1"];
				$align2=$rs->db_data["Align2"];
				$align3=$rs->db_data["Align3"];
				$valign1=$rs->db_data["VAlign1"];
				$valign2=$rs->db_data["VAlign2"];
				$valign3=$rs->db_data["VAlign3"];
				$ordem 		= $rs->db_data['Ordem'];
				$sql="INSERT into nl_newsletter_template(ID_ORG,Texto1,Texto2,Texto3,Cor1,Cor2,Cor3,Bg1,Bg2,Bg3,Tipo,img_ext,img2_ext,img3_ext,Link1,Link2,Link3,Align1,Align2,Align3,VAlign1,VAlign2,VAlign3,Ordem,Font1,Font2,Font3) VALUES ('$new_id','$campo1','$campo2','$campo3','$texto_bg','$texto_bg2','$texto_bg3','$cor_bg','$cor_bg2','$cor_bg3','$grava_tipo','$img_ext','$img_ext2','$img_ext3','$link1','$link2','$link3','$align1','$align2','$align3','$valign1','$valign2','$valign3','$ordem','$font1','$font2','$font3')";
				$res=Runquery($db_link,$DB_name,$sql);
				if($res)
				{

					$new_id2=db_get_last_insert_id($db_link);


					copia_segura($img_path.$line."_1.".$img_ext,$img_path.$new_id2."_1.".$img_ext);
					copia_segura($img_path.$line."_2.".$img_ext2,$img_path.$new_id2."_2.".$img_ext2);
					copia_segura($img_path.$line."_3.".$img_ext3,$img_path.$new_id2."_3.".$img_ext3);
				}
				$rs->db_next();
			}
			$rs->db_close();
		}
	}
	db_closedb_conection($db_link);
	return($res);
}
function DeleteRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	if(isset($_POST['ID']))
	{
		$ID=$_POST['ID'];
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$res=Runquery($db_link,$DB_name,"DELETE FROM nl_newsletter WHERE ID=$ID");
		if(!$res)echo db_error($db_link);
		$res=Runquery($db_link,$DB_name,"DELETE FROM nl_newsletter_template WHERE ID_ORG=$ID");
		if(!$res)echo db_error($db_link);
		$res=Runquery($db_link,$DB_name,"DELETE FROM nl_newsletter_template_prods WHERE NL_ID=$ID");
		if(!$res)echo db_error($db_link);
		db_closedb_conection($db_link);
	}
	return($res);
}
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_POST['ID'])) $ID=$_POST['ID'];
	if(isset($_POST['cabecalho'])) $cabecalho=$_POST['cabecalho'];
	if(isset($_POST['assunto'])) $assunto=$_POST['assunto'];
	if(isset($_POST['texto'])) $texto=$_POST['texto'];
	if(isset($_POST['SMTP'])) $SMTP=$_POST['SMTP'];
	if(isset($_POST['SMTP_Email'])) $SMTP_Email=$_POST['SMTP_Email'];
	if(isset($_POST['SMTP_login'])) $SMTP_login=$_POST['SMTP_login'];
	if(isset($_POST['SMTP_pass'])) $SMTP_pass=$_POST['SMTP_pass'];

	if(isset($_POST['EmailToReply'])) $EmailToReply=$_POST['EmailToReply'];
	if(isset($_POST['cabecalhoToReply'])) $cabecalhoToReply=$_POST['cabecalhoToReply'];
	if(isset($_POST['n_titulo'])) $Titulo=$_POST['n_titulo'];

	if(substr_count($_SERVER['HTTP_REFERER'], 'newsletter_template.php'))
	{
		$upd = '';
	}else{
		$upd="Texto='$texto',";
	}

	if($_POST['slashes']=="'")
	{
		$cabecalho=addslashes($cabecalho);
		$assunto=addslashes($assunto);
		$texto=addslashes($texto);
		$Titulo=addslashes($Titulo);
	}
	$sql="UPDATE nl_newsletter Set ".$upd."Cabecalho='$cabecalho',Assunto='$assunto',SMTP='$SMTP',SMTP_Email='$SMTP_Email',SMTP_login='$SMTP_login',SMTP_pass='$SMTP_pass',EmailToReply='$EmailToReply',cabecalhoToReply='$cabecalhoToReply',Titulo='$Titulo' WHERE ID=$ID";
	$res=Runquery($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	return($res);
}
function CopiaRegistos()
{
	return InsertNewRecord();
}

function InsertNewConfig()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_POST['cabecalho'])) $cabecalho=$_POST['cabecalho'];
	if(isset($_POST['SMTP'])) $SMTP=$_POST['SMTP'];
	if(isset($_POST['SMTP_Email'])) $SMTP_Email=$_POST['SMTP_Email'];
	if(isset($_POST['SMTP_login'])) $SMTP_login=$_POST['SMTP_login'];
	if(isset($_POST['SMTP_pass'])) $SMTP_pass=$_POST['SMTP_pass'];

	if(isset($_POST['emails_vez'])) $emails_vez=$_POST['emails_vez'];

	if(isset($_POST['EmailToReply'])) $EmailToReply=$_POST['EmailToReply'];
	if(isset($_POST['cabecalhoToReply'])) $cabecalhoToReply=$_POST['cabecalhoToReply'];

	if($_POST['slashes']=="'")
	{
		$cabecalho=addslashes($cabecalho);
	}

	$sql="insert into nl_config (cabecalho,SMTP,SMTP_Email,SMTP_login,SMTP_pass,emails_vez,EmailToReply,cabecalhoToReply) VALUES ('$cabecalho','$SMTP','$SMTP_Email','$SMTP_login','$SMTP_pass','$emails_vez','$EmailToReply','$cabecalhoToReply')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeConfig()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_POST['ID'])) $ID=$_POST['ID'];
	if(isset($_POST['cabecalho'])) $cabecalho=$_POST['cabecalho'];
	if(isset($_POST['SMTP'])) $SMTP=$_POST['SMTP'];
	if(isset($_POST['SMTP_Email'])) $SMTP_Email=$_POST['SMTP_Email'];
	if(isset($_POST['SMTP_login'])) $SMTP_login=$_POST['SMTP_login'];
	if(isset($_POST['SMTP_pass'])) $SMTP_pass=$_POST['SMTP_pass'];

	if(isset($_POST['emails_vez'])) $emails_vez=$_POST['emails_vez'];

	if(isset($_POST['EmailToReply'])) $EmailToReply=$_POST['EmailToReply'];
	if(isset($_POST['cabecalhoToReply'])) $cabecalhoToReply=$_POST['cabecalhoToReply'];

	if($_POST['slashes']=="'")
	{
		$cabecalho=addslashes($cabecalho);
	}

	$sql="UPDATE nl_config Set cabecalho='$cabecalho',SMTP='$SMTP',SMTP_Email='$SMTP_Email',SMTP_login='$SMTP_login',SMTP_pass='$SMTP_pass',emails_vez='$emails_vez',EmailToReply='$EmailToReply',cabecalhoToReply='$cabecalhoToReply' WHERE ID=1";
	$res=Runquery($db_link,$DB_name,$sql);
	db_closedb_conection($db_link);
	return($res);
}
function show_fast_tools()
{
	global $site_http_url;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2 text-center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Gerir newsletter" onclick="javascript:window.open('list.php?dump=<?=uniqid("");?>','_self')"></td>
	</tr>
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Gerir emails" onclick="javascript:window.open('newsletter_emails/list.php?dump=<?=uniqid("");?>','_self')"></td>
	</tr>
    <?php /*<tr>

		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Importar emails" onclick="javascript:window.open('newsletter_import/index.php?dump=<?=uniqid("");?>','_self')"></td>

	</tr>*/?>
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Exportar emails" onclick="javascript:window.open('exportar.php','_self')"></td>
	</tr>
	<!-- <? if($_GET['ID']) {?>
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Enviar newsletter" onclick="javascript:window.open('newsletter_send/index.php?nova=1&ID_News=<?=$_GET['ID']?>&dump=<?=uniqid("");?>','_self')"></td>
	</tr>
	<? } ?>
    <tr>
		<td class="preto text-center"><input class="btn btn-primary" type="button" style="width:160px" value="Ver envios" onclick="javascript:window.open('newsletter_send/index.php?dump=<?=uniqid("");?>','_self')"></td>
	</tr> -->
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<? }
?>