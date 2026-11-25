<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
	exit();
}

function show_multiple_menus($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $return='';
	$cache_filename=SITE_SHELL_URL.'files/tmp/show_multiple_menus_'.$id.'_'.$GLOBALS['site_lang'].'.txt';
	$gerar='';
	//$gerar=1;

	if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
	if($gerar!=1 && file_exists($cache_filename) ) $return=file_get_contents($cache_filename);
	else
	{

		// $sql='SELECT ID,TipoMenu FROM menus_links WHERE ID_ORG="'.$id.'" AND Visivel=1 ORDER BY Ordem ASC';
		$sql='SELECT menus_links.ID,menus_links.TipoMenu, menus_links.catalogo_id, menus_links.class, menus_links.video,menus_links.img_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG="'.$id.'" AND Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
		// echo $sql;
		$rs->db_open($db_link,$DB_name,$sql);

		if(!$rs->EOF())
		{

			$total=$rs->db_rows;
			$a=0;

			$return .= '<div class="row">';
			while(!$rs->EOF())
			{
				$a++;
				$IDb=$rs->db_data['ID'];
				$TipoMenu=$rs->db_data['TipoMenu'];
				$URL_Nome=$rs->db_data['URL_Nome2'];

				if($TipoMenu == 'familias')
				{
					// if($total == $a) $return .= show_menu_footer_families($IDb,'col-sm-2');
					// else $return .= show_menu_footer_families($IDb);
				}
				elseif($TipoMenu == 'newsletter')
				{
					ob_start();
					$return2='';
					newsletterRegisterSmallForm($TipoMenu,'col-sm-2');
					$return2=ob_get_contents();// Grab output
					$return .= '<section class="footer-block newsletter_fot"><h4>'.$URL_Nome.'</h4><ul class="toggle-footer" style="text-align:left"><li style="text-align:left"><span>'.getLinguas('key_subscribe_newsletter2').'</span>'.$return2.'<div class="clear"></div></li><div class="clear"></div></ul></section>';
					ob_end_clean();// Discard output buffer
				}
				else
				{
					if($total == $a) $return .= show_menu_footer_w($IDb,'col-sm-2');
					else $return .= show_menu_footer_w($IDb);
				}

				$rs->db_next();
			}
			// $return .= '<div class="bottom-footer col-xs-12"><div><a class="_blank" target="_blank">'.$GLOBALS['site_copyright'].'</a> </div></div></div></div></div>';// '.show_media(18).''.show_media(19).'
			// $return .= '<div class="bottom-footer col-xs-12"><div>Copyright © 2023 Dotbright. Todos os direitos reservados. <a href="https://www.memogadget.com/pt/index.php" title="Desenvolvido por:memogadget" target="_blank" rel="nofollow" class="url"><span>Desenvolvido por:memogadget</span></a> </div></div></div></div></div>';// '.show_media(18).''.show_media(19).'
			// $return .= '<div class="bottom-footer col-xs-12" style=" width:100%;transform:translate(0,0%);">
			// 				<div class="col-md-9">
			// 					<div style="margin-top:9px">
			// 						Copyright © 2023 Dotbright. Todos os direitos reservados. 
			// 						<a href="https://www.memogadget.com/pt/index.php" title="Desenvolvido por:memogadget" target="_blank" rel="nofollow" class="url"><span>Desenvolvido por:memogadget</span></a> 
			// 					</div>
			// 				</div>';
			// 	$return .= '<div class="col-md-3">';

			// 			$return .= '<ul class="toggle-footer" style="display:flex">';
			// 				$return .= '<li  >';
			// 					$return .= '<a class="nobullet" href="https://www.livroreclamacoes.pt/inicio" target="_blank" rel="nofollow">';
			// 						$return .= '<img style="width:100% !important;max-width:100px !important;" src="layout/dotbright/images/L_reclamacoes.png" >';
			// 					$return .= '</a>';
			// 				$return .= '</li>';
			// 				$return .= '<li  >';
			// 					$return .= '<a class="nobullet" href="https://livrodeelogios.com/" target="_blank" rel="nofollow">';
			// 						$return .= '<img style="width:100% !important;max-width:100px !important;" src="layout/dotbright/images/L_elogios.png" >';
			// 					$return .= '</a>';
			// 				$return .= '</li>';
			// 			$return .= '</ul>';

			// 	$return .= '</div>';
			// $return .= '</div>';
			$return .= '</div>';
			$return .= '</div>';
			$return .= '</div>';// '.show_media(18).''.show_media(19).'';
		}
		$rs->db_close(1);

		$fh=fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);
	}

	return $return;
}

function show_menu_footer_urls($id=0,$nomargin='col-sm-2')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;$i=4;$i2=0;$Indexar=0;$Index='';$return='';

	$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_footer_urls_'.$id.'_'.$GLOBALS['site_lang'].'.txt';
	$gerar='';

	if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
	if($gerar!=1 && file_exists($cache_filename) ) $return=file_get_contents($cache_filename);
	else
	{

		$sql='SELECT menus_links.ID,menus_links.TipoMenu, menus_links.catalogo_id, menus_links.class, menus_links.video,menus_links.img_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG="'.$id.'" AND Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
		$rs->db_open($db_link,$DB_name,$sql);

		if(!$rs->EOF())
		{
			$URL_Nome='';
			$sql='SELECT menus_links.class,menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID="'.$id.'" AND menus_links.Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$URL_Nome=$rs2->db_data['URL_Nome2'];
				$class=$rs2->db_data['class'];
			}
			$rs2->db_close();



			while(!$rs->EOF())
			{
				// $i++;
				$IDb=$rs->db_data['ID'];
				$img_ext=$rs->db_data['img_ext'];

				$catalogo_id=$rs->db_data['catalogo_id'];
				$class=$rs->db_data['class'];

				if($catalogo_id > 0) $URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);else $URL_Link=seo($rs->db_data['URL_Link']);

				if($URL_Link == '') $URL_Link='#';
				$URL_Nome=$rs->db_data['URL_Nome2'];


				$video=str_replace("\n",'<br>',$rs->db_data['video']);
				$novajanela=$rs->db_data["novajanela"];
				if($novajanela == 1) $open=' target="_blank"';
				else $open='';
				// $Indexar=$rs->db_data["Indexar"];
				if($Indexar == 0) $Index=' rel="nofollow" ';
				else  $Index='';


				$stl='';
				$stl2='';
				if($img_ext && dotbright_image_file_exists($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
				{
					$stl='style="display:inline-block"';
					$stl2='class="nobullet"';
				}


				if($img_ext && dotbright_image_file_exists($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
				{
					if($size=@dotbright_files_image_size($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
					{
						$w = $size[0];
						$h = $size[1];
					}
					$return .= '<a '.$stl2.' href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.'><img style="width:100%;max-width:'.$w.'px" src="files/menus/'.$IDb.'_1.'.$img_ext.'" title="'.$URL_Nome.'" alt="" /></a>';

				}
				elseif($URL_Link)
				{
					$return .= '<a href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.' class="url"><span>'.$URL_Nome.'</span></a>';

				}
				elseif($video)
				{
				}
				else
				{
					$return .= ''.$URL_Nome.'';

				}


				$i2++;
				$rs->db_next();
			}

			$return .= '';
		}
		$rs->db_close(1);

		$fh=fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);
	}

	return $return;
}
function show_menu_footer_w($id=0,$nomargin='col-sm-2')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;$rs3=new Dataobj;
    $sql='SELECT menus_links.ID,menus_links.TipoMenu, menus_links.catalogo_id, menus_links.class, menus_links.video,menus_links.img_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG="'.$id.'" AND Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=4;$i2=0;$Indexar=0;$Index='';$return='';
	if(!$rs->EOF())
	{
		$URL_Nome='';
		$sql='SELECT menus_links.class,  menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID="'.$id.'" AND menus_links.Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$URL_Nome=$rs2->db_data['URL_Nome2'];
			$class=$rs2->db_data['class'];
		}
		$rs2->db_close();

		/* if($nomargin) $return='<div class="col-xs-4 col-md-3 bottom_blocos '.$nomargin.'" style="padding-right:0">';
		else  */
		if($class=='block_contact_infos') $return='<section id="'.$class.'" "><div>';
		else $return='<section id="'.$class.'" class="footer-block col-xs-12 col-sm-3">';

		if($URL_Nome) $return .= '<h4>'.$URL_Nome.'</h4>';

		$return .= '<ul class="toggle-footer">';

		while(!$rs->EOF())
		{
			// $i++;
			$IDb=$rs->db_data['ID'];
			$img_ext=$rs->db_data['img_ext'];



			$catalogo_id=$rs->db_data['catalogo_id'];
			$class=$rs->db_data['class'];

			if($catalogo_id > 0) $URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);else $URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Nome=$rs->db_data['URL_Nome2'];


			$video=str_replace("\n",'<br>',$rs->db_data['video']);
			$novajanela=$rs->db_data["novajanela"];
			if($novajanela == 1) $open=' target="_blank"';
			else $open='';
			// $Indexar=$rs->db_data["Indexar"];
			if($Indexar == 0) $Index=' rel="nofollow" ';
			else  $Index='';


			$stl='';
			$stl2='';

			if($img_ext && dotbright_image_file_exists($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
			{
				$stl='style="float:left;content:\'\'"';
				$stl2='class="nobullet"';
			}

			if($class != '') $return .= '<li class="'.$class.'" '.$stl.'>';else $return .= '<li class="item" '.$stl.'>';

			if($img_ext && dotbright_image_file_exists($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
			{
				if($size=@dotbright_files_image_size($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
				{
					$w = $size[0];
					$h = $size[1];
				}
				$return .= '<a '.$stl2.' href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.'><img style="width:100%;max-width:'.$w.'px" src="files/menus/'.$IDb.'_1.'.$img_ext.'" title="'.$URL_Nome.'" alt="" /></a>';

			}
			elseif($URL_Link)
			{
				$return .= '<a href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.'><span>'.$URL_Nome.'</span></a>';

			}
			elseif($video)
			{
				// $return .= '<ul>';
				// $return .= '<li>';

				// ob_start();

				// $return2='';
				// trata_tags(clean_code($video));
				// $return2=ob_get_contents();//Grab output

				// ob_end_clean();//Discard output buffer


				// if($return2=='') $return .= $video;else $return .= $return2;

				// $return .= '</li>';
				// $return .= '</ul>';
			}
			else
			{
				$return .= ''.$URL_Nome.'';

			}

			$return .= '</li>';

			$i2++;
			$rs->db_next();
		}

		if ((int)$id===245) {
            require_once __DIR__.'/../../../core/withdrawals.php';
            if (store_withdrawal_enabled()) $return .= '<li class="item"><a href="livre-resolucao.php"><span>Exercer direito de livre resolução</span></a></li>';
        }
		if($class=='block_contact_infos') $return .= '</ul></div></section>';
		else $return .= '</ul></section>';
	}
	$rs->db_close(1);

	return $return;
}

function show_menu_footer_families($id=0,$nomargin='col-sm-2')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;
	$return='';
    $sql='SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG=0
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				pf.Ordem';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=4;$i2=0;
	if(!$rs->EOF())
	{
		$URL_Nome='';
		$sql='SELECT menus_links.class,  menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID="'.$id.'" AND menus_links.Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" ORDER BY Ordem ASC';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			$URL_Nome=$rs2->db_data['URL_Nome2'];
			$class=$rs2->db_data['class'];
		}
		$rs2->db_close();

		/* if($nomargin) $return='<div class="col-xs-4 col-md-3 bottom_blocos '.$nomargin.'" style="padding-right:0">';
		else  */
		if($class=='block_contact_infos') $return='<section id="'.$class.'" class="footer-block col-xs-12 '.$nomargin.'"><div>';
		else $return='<section id="'.$class.'" class="footer-block col-xs-12 '.$nomargin.'">';

		if($URL_Nome) $return .= '<h4>'.$URL_Nome.'</h4>';

		$return .= '<ul class="toggle-footer">';

		while(!$rs->EOF())
		{
			// $i++;
			$URL_Nome=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];




			if($catalogo_id > 0) $URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);else $URL_Link=seo($rs->db_data['URL_Link']);





			if($class != '') $return .= '<li class="'.$class.'">';else $return .= '<li class="item">';

			if($img_ext && dotbright_image_file_exists($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
			{
				if($size=@dotbright_files_image_size($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
				{
					$w = $size[0];
					$h = $size[1];
				}
				$return .= '<a href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.'><img style="width:100%;max-width:'.$w.'px" src="files/menus/'.$IDb.'_1.'.$img_ext.'" title="'.$URL_Nome.'" alt="" /></a>';
			}
			elseif($URL_Link)
			{
				$return .= '<a href="'.$URL_Link.'" title="'.$URL_Nome.'" '.$open.' '.$Index.'><span>'.$URL_Nome.'</span></a>';

			}
			elseif($video)
			{
				// $return .= '<ul>';
				// $return .= '<li>';

				// ob_start();

				// $return2='';
				// trata_tags(clean_code($video));
				// $return2=ob_get_contents();//Grab output

				// ob_end_clean();//Discard output buffer


				// if($return2=='') $return .= $video;else $return .= $return2;

				// $return .= '</li>';
				// $return .= '</ul>';
			}
			else
			{
				$return .= ''.$URL_Nome.'';

			}

			$return .= '</li>';

			$i2++;
			$rs->db_next();
		}

		if($class=='block_contact_infos') $return .= '</ul></div></section>';
		else $return .= '</ul></section>';
	}
	$rs->db_close(1);

	return $return;
}


function show_menu_topo()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;
    $sql='
			SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG=0
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				pf.Ordem
			';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if(!$rs->EOF())
	{

		$return .= '<div id="freeplace-menu_t">';
		$return .= '<div id="freeplace-menu" class="top_menu"><ul>';

		while(!$rs->EOF())
		{
			$i++;
			$nomeb=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];

			if(temproductfiltersfams($catalogo_id))
			{
				$return .= '<li class="">';
				$return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';
				$res=show_submenu_topo($catalogo_id);
				if($res['return']) $return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res['return'].'</div></div>';
				$return .= '</li>';
			}

			$rs->db_next();
		}

		$return .= '</ul></div>';
		$return .= '</div>';
	}
	$rs->db_close();

	return $return;
}

function show_submenu_topo($ID_ORG=0,$z=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;
    $sql='
			SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				pf.Ordem=0,pf.Ordem
			';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$x=5;
		$y=0;
		while(!$rs->EOF())
		{
			if($x==$y)
			{
				// if($z==0) $return .= '<div class="clearright"></div>';
				$y=0;
				$stl='style="min-width:100px;margin-left:0"';
			}
			else $stl='style="min-width:100px"';

			$nomeb=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];

			if(temproductfiltersfams($catalogo_id))
			{
				$submenus=show_submenu_topo2($catalogo_id,1);
				$return .= '<div class="colm" '.$stl.'>';
				$return .= '<a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1).'">'.$nomeb.'</a><div class="clear"></div>';
				$return .= $submenus['return'];
				$return .= '</div>';
			}
			$y++;
			$rs->db_next();
		}
	}
	$rs->db_close();
	return array('return' => $return,'total' => $rs->db_rows);
}

function show_submenu_topo2($ID_ORG=0,$z=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;
    $sql='
			SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				pf.Ordem=0,pf.Ordem
			';
	$rs->db_open($db_link,$DB_name,$sql);

	if(!$rs->EOF())
	{
		$x=7;$y=0;

		$return .= '<div class="colm3">';
		while(!$rs->EOF())
		{


			if($x==$y)
			{
				$return .= '</div><div class="colm3">';
				$y=0;
			}

			$nomeb=$rs->db_data['Cabecalho'];
			$catalogo_id=$rs->db_data['ID'];

			if(temproductfiltersfams($catalogo_id))
			{
				$return .= '<div class="colm2">';

				$return .= '<a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1).'">'.$nomeb.'</a><div class="clear"></div>';

				$return .= '</div>';
			}


			$y++;
			$rs->db_next();
		}
		$return .= '</div>';
	}
	$total=$rs->db_rows;
	$rs->db_close();
	return array('return' => $return,'total' => $total);
}

function temproductfiltersfams($family_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$result=0;
	$pos=0;
	$sql='
			SELECT count(prod_id) as total
			FROM
				filters_products f
				INNER JOIN products p ON p.ID=f.prod_id
			WHERE
				(f.fam_id1='.$family_id.' OR f.fam_id2='.$family_id.' OR f.fam_id3='.$family_id.')
				AND f.filtro_active=1
				AND f.prod_id > 0
				AND p.Aproved=1
			';
	// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF() && $rs->db_data['total'] > 0) $result=1;
	$rs->db_close();
	return $result;
}

function show_menu_footer($id=0,$nomargin='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_shell_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql='SELECT menus_links.ID,menus_links.img_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2,menus_links.Indexar FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG="' . $id . '" AND Visivel=1 AND menus_links_lang.Lang="' . $site_lang . '" ORDER BY ORDEM ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	if (!$rs->EOF())
	{
		$sql='SELECT menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID="' . $id . '" AND menus_links.Visivel=1 AND menus_links_lang.Lang="' . $site_lang . '" ORDER BY ORDEM ASC';
		$rs2->db_open($db_link,$DB_name,$sql);
		if (!$rs2->EOF())
		{
			$URL_Nome=$rs2->db_data['URL_Nome2'];
		}
		else
		{
			$URL_Nome='';
		}
		$rs2->db_close();

		$return='<div class="bottom_blocos ' . $nomargin . '">';
		if ($URL_Nome)
		{
			$return .= '<h2>' . $URL_Nome . '</h2>';
		}

		$return .= '<ul>';

		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$img_ext=$rs->db_data['img_ext'];
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Nome=$rs->db_data['URL_Nome2'];
			$novajanela=$rs->db_data["novajanela"];
			if ($novajanela == 1)
			{
				$open=' target="_blank"';
			}
			else
			{
				$open='';
			}
			$Indexar=$rs->db_data["Indexar"];
			if ($Indexar == 0)
			{
				$Index=' rel="nofollow" ';
			}
			else
			{
				$Index='';
			}

			if ($img_ext && dotbright_image_file_exists($site_shell_url . 'files/menus/' . $IDb . '_1.' . $img_ext))
			{
				if($size=@dotbright_files_image_size($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
				{
					$w = $size[0];
					$h = $size[1];
				}

				if ($URL_Link == '')$return .= '<li class="social"><img style="width:100%;max-width:'.$w.'px"  src="files/menus/' . $IDb . '_1.' . $img_ext . '" title="' . $URL_Nome . '" alt="" /></li>';
				else $return .= '<li class="social"><a href="' . $URL_Link . '" title="' . $URL_Nome . '" ' . $open . ' ' . $Index . '><img src="files/menus/' . $IDb . '_1.' . $img_ext . '" title="' . $URL_Nome . '" alt="" /></a></li>';
			}
			elseif ($URL_Link)
			{

				$return .= '<li class="icon"><a href="' . $URL_Link . '" title="' . $URL_Nome . '" ' . $open . ' ' . $Index . '>' . $URL_Nome . '</a></li>';
			}
			else
			{
				$return .= '<li>' . $URL_Nome . '</li>';
			}

			$rs->db_next();
		}
		$return .= '</ul>';

		$return .= '</div>';
	}
	$rs->db_close();

	return $return;
}


function show_menu($id_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';

	$dados=cliente_logado();
	if($dados && $dados['Grupo'] > 0) $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Visivel=1 ORDER BY Ordem ASC';
	else $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Logado=0 AND Visivel=1 ORDER BY Ordem ASC';


	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$uri=$_SERVER['REQUEST_URI'];
		$uri=str_replace($site_http_url,'',$uri);

		$return .= '<nav id="main-nav"><ul id="main-menu" class="sm sm-clean">';

		/*$return .= '<div class="menu-container-mega"><div class="container">';
		$return .= '<div class="menu"><ul>';*/

		while(!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			$Largura=$rs->db_data['Largura'];
			$catalogo_id=0;
			$catalogo_id=$rs->db_data['catalogo_id'];
			if($catalogo_id > 0)$p_seo=str_replace($GLOBALS['site_http_url'],'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1));
			else $p_seo='#';
			$TipoBanner=$rs->db_data['TipoBanner'];
			$TipoMenu=$rs->db_data['TipoMenu'];
			$templates_id=explode(';',$rs->db_data['template_id']);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url,'',$URL_Link);

			$sql='SELECT URL_Nome2 FROM menus_links_lang WHERE ID_PAI='.$IDb.' and lang="'.$site_lang.'"';
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['URL_Nome2'];

				if($TipoMenu=='familias')
				{
					if(productCategoryHasProducts($catalogo_id))
					{
						if($URL_Link!='')
						{
							$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
							$return.='<li>';
							$return.='<a href="'.$URL_Link.'"><div class="menulink">'.$nomeb.'</div></a>';
							$return.='</li>';
						}
						else
						{
							$res=show_menu_shop_hoverintent($catalogo_id,$TipoBanner,$nomeb);

							$return.='<li>';
							if($res) $return.='<a href="'.$p_seo.'" onclick="if(is_touch_device()) return false;">'.$nomeb.'</a>';
							else $return.='<a href="'.$p_seo.'">'.$nomeb.'</a>';

							// if($res) $return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
							if($res) $return .= $res;
							$return.='</li>';
						}
					}
					else
					{
						$res=show_menu_shop_hoverintent(0,$TipoBanner,$nomeb);

						$return.='<li>';
						if($res) $return.='<a href="'.$p_seo.'" onclick="if(is_touch_device()) return false;">'.$nomeb.'</a>';
						else $return.='<a href="'.$p_seo.'">'.$nomeb.'</a>';

						// if($res) $return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
						if($res) $return .= $res;
						$return.='</li>';
					}
				}
				elseif($TipoMenu=='marcas')
				{
					// if(IS_FAM_ACTIVE($catalogo_id))
					// {
						if($URL_Link!='')
						{
							$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
							$return .= '<li>';
							$return .= '<a href="'.$URL_Link.'"><div class="menulink">'.$nomeb.'</div></a>';
							$return .= '</li>';
						}
						else
						{
							$res=getSubMenuItensbrands2($catalogo_id);

							$return .= '<li>';
							if($res) $return .= '<a href="'.$p_seo.'" onclick="if(is_touch_device()) return false;">'.$nomeb.'</a>';
							else $return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';

							if($res) $return .= $res;
							$return.='</li>';
						}
					// }
				}
				elseif($catalogo_id > 0)
				{
					$fam=get_family_info($catalogo_id);
					if(!empty($fam) && is_array($fam) && $fam['reparacoes'] == 1)
					{
						$res=getSubMenuItensFams2($catalogo_id);

							$return .= '<li>';
							if($res) $return .= '<a href="'.$p_seo.'" onclick="if(is_touch_device()) return false;">'.$nomeb.'</a>';
							else $return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';

							if($res) $return .= $res;
							$return.='</li>';
					}
					else
					{
						$res=show_menu_shop_hoverintent($catalogo_id,$TipoBanner,$nomeb);

						$return.='<li>';
						if($res) $return.='<a href="'.$p_seo.'"  onclick="if(is_touch_device()) return false;">'.$nomeb.'</a>';
						else $return.='<a href="'.$p_seo.'">'.$nomeb.'</a>';

						if($res) $return .= $res;
						$return.='</li>';
					}
				}
				else
				{
					$sql="
							SELECT m.* ,ml.URL_Nome2 FROM menus_links m INNER JOIN menus_links_lang ml ON ml.ID_PAI=m.ID
							WHERE ID_ORG='".$IDb."' AND Visivel=1 AND ml.lang='$site_lang' ORDER BY Ordem ASC
							";
					$rs3->db_open($db_link,$DB_name,$sql);
					if(!$rs3->EOF())
					{
						$return.='<li>';
						$return.='<a href="#">'.$nomeb.'</a>';
						$return.='<ul>';
						while(!$rs3->EOF())
						{
							$catalogo_id=0;
							$catalogo_id=$rs3->db_data['catalogo_id'];
							$TipoMenu=$rs3->db_data['TipoMenu'];
							$URL_Link='';
							if($TipoMenu=='catalogo')$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
							else $URL_Link=$rs3->db_data['URL_Link'];
							$URL_Link=seo(str_replace($site_http_url,'',$URL_Link));
							$return.='<li><a href="'.$URL_Link.'">'.$rs3->db_data['URL_Nome2'].'</a></li>';
							$rs3->db_next();
						 }
						$return.='</ul>';
						$return.='</li>';

					}
					else
					{
						$return.='<li>';
						if($URL_Link!="") $return.='<a href="'.$URL_Link.'">'.$nomeb.'</a>';
						else $return.='<a href="#">'.$nomeb.'</a>';
						$return.='</li>';
					}
					$rs3->db_close();
				}
			}
			$rs2->db_close();
			$rs->db_next();
		}
		//$return.='</ul></div></div></div>';
		$return.='</nav></ul>';
	}
	else
	{
		$sql='Select * from menus_links where ID="'.$id_pai.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			$Largura=$rs->db_data['Largura'];
			$catalogop_id=0;
			$catalogop_id=$rs->db_data['catalogo_id'];
			$TipoMenu=$rs->db_data['TipoMenu'];
			$templates_id=explode(';',$rs->db_data['template_id']);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url,'',$URL_Link);
			$uri=$_SERVER['REQUEST_URI'];
			$uri=str_replace($site_http_url,'',$uri);
			$return.='<div id="freeplace-menu" class="freeplace-menu"><ul>';
			if($TipoMenu=='familias')
			{
				$sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=".$catalogop_id." and pfl.lang='$site_lang'";
				$rs2->db_open($db_link,$DB_name,$sql);
				while(!$rs2->EOF())
				{
					$nomeb=$rs2->db_data['Cabecalho'];
					$catalogo_id=$rs2->db_data['ID'];
					$return.='<li>';
					$return.='<a href="'.$p_seo.'">'.$nomeb.'</a>';
					$res=show_menu_shop_hoverintent($catalogo_id,$TipoBanner,$nomeb);
					if($res) $return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
					$return.='</li>';
					$rs2->db_next();
				}
				$rs2->db_close();
			}
			elseif($TipoMenu=='marcas')
			{

				$sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=".$catalogop_id." and pfl.lang='$site_lang'";
				$rs2->db_open($db_link,$DB_name,$sql);
				while(!$rs2->EOF())
				{
					$nomeb=$rs2->db_data['Cabecalho'];
					$catalogo_id=$rs2->db_data['ID'];
					$return.='<li>';
					$return.='<a href="'.$p_seo.'">'.$nomeb.'</a>';
					// $res=show_menu_shop_hoverintent($catalogo_id,$TipoBanner,$nomeb);
					if($res) $return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
					$return.='</li>';
					$rs2->db_next();
				}
				$rs2->db_close();
			}
			$return.='</ul></nav><div class="c"></div></div></div></div>';
		}
	}
	$rs->db_close();
	echo $return;
}

function show_menu_lateral($id_pai,$tipo=0,$showfilter = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;

	$return='';

	if(isset($_GET['cat']) && intval($_GET['cat']) > 0 && $showfilter  == 1)
	{
		// $return .= '<h3 class="titulo_filtro">Filtrar por</h2>';
		//$return .= show_menu_brands(0,$TipoBanner,$nomeb);
			$return .= filtros_left(1);
			echo $return;
			return '';
		//exit;
	}

	$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_lateral_'.$id_pai.'_'.intval($_GET['cat']).'_'.$GLOBALS['site_lang'].'.txt';
	$gerar='';
	if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);

	if($gerar!=1 && file_exists($cache_filename) ) $return=file_get_contents($cache_filename);
	else
	{
		$return='';

		if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$return .= '<h3 style="border-bottom:1px solid #c0c0c0;padding:10px">Filtrar por</h2>';
			//$return .= show_menu_brands(0,$TipoBanner,$nomeb);
			$return .= filtros_left(1);
		}
		$return .= '<h3 style="border-bottom:1px solid #c0c0c0;padding:10px">Navegar por</h2>';

		$sql='Select * from menus_links where ID_ORG="'.$id_pai.'" and Visivel=1 order by Ordem asc';

		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$i=0;$x=10;$x2=0;
			$uri=$_SERVER["REQUEST_URI"];
			$uri=str_replace($site_http_url,'',$uri);
			$return .= '<section id="categorias-menu" data-accordion-group>';

			$style='';
			while(!$rs->EOF())
			{
				$i++;
				$IDb=$rs->db_data["ID"];
				$Largura=$rs->db_data["Largura"];
				$catalogo_id=0;
				$catalogo_id=$rs->db_data["catalogo_id"];
				$menu_ext=$rs->db_data["img_ext"];
				if($catalogo_id > 0) $p_seo=str_replace($GLOBALS['site_http_url'],'',seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1));
				else $p_seo='#';
				$TipoBanner=$rs->db_data["TipoBanner"];
				$TipoMenu=$rs->db_data["TipoMenu"];
				$templates_id=explode(';',$rs->db_data["template_id"]);
				$URL_Link=seo($rs->db_data['URL_Link']);
				$URL_Link=str_replace($site_http_url,'',$URL_Link);

				$img='';
				if(dotbright_image_file_exists('files/menus/'.$IDb.'_1.'.$menu_ext) && $menu_ext!='') $img='<img src="files/menus/'.$IDb.'_1.'.$menu_ext.'" style="max-width:20px" alt="" />';


				$sql="Select URL_Nome2 from menus_links_lang where ID_PAI=$IDb and lang='$site_lang'";
				$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					$nomeb=$rs2->db_data["URL_Nome2"];

					if($TipoMenu=='familias')
					{
						if(productCategoryHasProducts($catalogo_id))
						{
							if($URL_Link!="")
							{
								$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
								$return .= '<section data-accordion><button data-control>';
								$return .= '<a href="'.$URL_Link.'">'.$img.' '.$nomeb.'</a>';
								$return .= '</button></section>';
							}
							else
							{
								$res=getSubFam_lateral($catalogo_id);
								if($res) $p_seo='javascript:void(0);';
								$return .= '<section data-accordion><button data-control><a href="'.$p_seo.'" id="mn'.$IDb.'" onClick="return false;">'.$img.' '.$nomeb.'</a></button>';
								if($res) $return .= '<div data-content>'.$res.'</div></section>';
								$return .= '';
							}
						}
						elseif(productSubCategoryHasProducts($catalogo_id))
						{
							$res=getSubFam_lateral($catalogo_id);
							if($res) $p_seo='javascript:void(0);';
							$return .= '<section data-accordion><button class="no-subcategories" data-control><a href="'.$p_seo.'" id="mn'.$IDb.'">'.$img.' '.$nomeb.'</a></button>';
							if($res) $return .= '<div data-content>'.$res.'</div></section>';
							$return .= '';
						}
					}
					elseif($TipoMenu=='marcas')
					{
						/*$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
						$return .= '<section data-accordion><button data-control>';
						$return .= '<a href="'.$URL_Link.'">'.$nomeb.'</a>';
						$return .= '</button></section>';*/

						/*
						$return.='<div class="menu_tipo"><a href="'.$p_seo.'" onclick="SwitchMenuH('.$IDb.')">'.$nomeb.'</a></div>';
						$res=show_menu_brands(0,$TipoBanner,$nomeb);
						if($res) $return.='<div class="submenu" id="sub'.$IDb.'">'.$res.'<div class="c"></div></div>';*/
					}
					elseif($catalogo_id > 0)
					{
						$res=getSubFam_lateral($catalogo_id);
						if($res) $p_seo='javascript:void(0);';
						$return .= '<button data-control>';
						$return .= '<a href="'.$p_seo.'" id="mn'.$IDb.'">'.$img.' '.$nomeb.'</a>';
						// if($res) $return .= '<ul class="cd-secondary-nav is-hidden bgcolor2">'.$res.'<div class="c"></div></ul>';
						$return .= '</button>';
					}
					else
					{

						$sql="SELECT m.* ,ml.URL_Nome2
								FROM menus_links m
								INNER JOIN menus_links_lang ml ON ml.ID_PAI=m.ID
								WHERE ID_ORG='".$IDb."' AND Visivel=1 AND ml.lang='$site_lang'
								ORDER BY Ordem ASC";
						$rs3->db_open($db_link,$DB_name,$sql);
						if(!$rs3->EOF())
						{

							$p_seo='javascript:void(0);';
							$return .= '<section data-accordion><button data-control><a href="'.$p_seo.'" id="mn'.$IDb.'" onClick="return false;">'.$img.' '.$nomeb.'</a></button>';
							$return .= '<div data-content>';

							$j=0;
							while(!$rs3->EOF())
							{
								$catalogo_id=0;
								$catalogo_id=$rs3->db_data["catalogo_id"];
								$TipoMenu=$rs3->db_data["TipoMenu"];
								$URL_Link='';
								if($TipoMenu=='catalogo')$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);
								else $URL_Link=$rs3->db_data['URL_Link'];
								$URL_Link=seo(str_replace($site_http_url,'',$URL_Link));
								$return .= '<article><a href="'.$URL_Link.'">'.$rs3->db_data['URL_Nome2'].'</a></article>';
								$j++;
								$rs3->db_next();
							 }

							$return .= '</div></section>';


						}
						else
						{
							$return .= '<section data-accordion style="padding:6px">';
							if($URL_Link!="") $return .='<article><a href="'.$URL_Link.'">'.$img.' '.$nomeb.'</a></article>';
							else $return .= '<article><a href="javascript:void(0);">'.$img.' '.$nomeb.'</a></article>';
							$return .= '</section><div style="clear:both"></div>';
						}
						$rs3->db_close();
					}
				}
				$rs2->db_close();
				$rs->db_next();
			}
			$return .= '</section>';
		}
		else
		{
			$sql='Select * from menus_links where ID="'.$id_pai.'"';
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				$i=0;
				$uri=$_SERVER["REQUEST_URI"];
				$uri=str_replace($site_http_url,"",$uri);
				$return .= '<section id="categorias-menu" data-accordion-group>';
				$style='';

				$i++;
				$IDb=$rs->db_data["ID"];
				$Largura=$rs->db_data["Largura"];
				$catalogop_id=0;
				$catalogop_id=$rs->db_data["catalogo_id"];
				$TipoMenu=$rs->db_data["TipoMenu"];
				$templates_id=explode(';',$rs->db_data["template_id"]);
				$URL_Link=seo($rs->db_data['URL_Link']);
				$URL_Link=str_replace($site_http_url,'',$URL_Link);

				if($TipoMenu=='familias') $sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=".$catalogop_id." and pfl.lang='$site_lang'";
				else $sql='';

				// $return .= $sql;
				$rs2->db_open($db_link,$DB_name,$sql);
				while(!$rs2->EOF())
				{
					$nomeb=$rs2->db_data["Cabecalho"];
					$catalogo_id=$rs2->db_data["ID"];
					$return .= '<li class="">';
					$return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';
					$res=getSubFam_lateral($catalogo_id);
					if($res) $return .= '<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
					$return .= '</li>';
					$rs2->db_next();
				}
				$rs2->db_close();
				$return .= '</section>';
			}
		}
		$rs->db_close();

		$fh=fopen($cache_filename,'w');
		fwrite($fh,$return);
		fclose($fh);
		chmod($cache_filename,0755);
	}
	echo $return;
}

function getSubFam_lateral($ID_ORG=0)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql ='SELECT pf.ID,pfl.Cabecalho,pfl.Cabecalho2
	FROM products_families pf
	INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
	WHERE pf.visivel=1 AND pfl.lang="'.$site_lang.'" AND pf.ID_ORG="'.$ID_ORG.'"
	ORDER BY pf.Ordem=0,pf.Ordem ASC,pfl.Cabecalho ASC';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			$nomeb=$rs->db_data['Cabecalho'];
			if($rs->db_data['Cabecalho2'])$nomeb=$rs->db_data['Cabecalho2'];

			$res=getSubFam_lateral($IDb);
			$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
			if($res) $menus .= '<article data-accordion><button data-control><a href="javascript:void(0)" id="mn'.$IDb.'">'.$nomeb.'</a></button><div data-content>'.$res.'</div></article>';
			else
			{
				//$res=get_menus_filtros_lateral($IDb);
				if($res) $menus .= '<article data-accordion><button data-control><a href="javascript:void(0)" id="mn'.$IDb.'">'.$nomeb.'</a></button><div data-content>'.$res.'</div></article>';
				else $menus .= '<article><a href="'.$URL_Link.'">'.$nomeb.'</a></article>';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $menus;
}
function get_menus_filtros_lateral($IDb)
{
	$menus='';
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;

	//Le o tipo de filtro
	$sql='SELECT menu_cat_id,menu_cat_nome FROM menus_filtros WHERE menu_fam_id="'.$IDb.'" AND menu_visivel=1 AND menu_cat_id > 0 AND menu_valor_id=0 ORDER BY menu_ordem ASC';

	$rs->db_open($db_link,$DB_name,$sql);
	$a=0;
	if(!$rs->EOF())
	{
		$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);

		//Le os filtros todos
		while(!$rs->EOF())
		{
			$cat_id=$rs->db_data['menu_cat_id'];
			$menu_cat_nome=$rs->db_data['menu_cat_nome'];

			$sql ='SELECT menu_cat_id,menu_valor_id,menu_valor_nome
			FROM menus_filtros
			WHERE menu_fam_id="'.$IDb.'" AND menu_visivel=1 AND menu_cat_id='.$cat_id.' AND menu_valor_id > 0
			ORDER BY menu_ordem ASC';

			$rs2->db_open($db_link,$DB_name,$sql);
			$b=0;$tt=$rs2->db_rows;
			if(!$rs2->EOF())
			{
				$menus .= '<article data-accordion>';
				$menus .= '<button data-control><a href="javascript:void(0);">'.$menu_cat_nome.'</a></button> <div data-content>';
				while(!$rs2->EOF())
				{
					$b++;
					$cat_id=$rs2->db_data['menu_cat_id'];
					$valor_id=$rs2->db_data['menu_valor_id'];
					$menu_valor_nome=$rs2->db_data['menu_valor_nome'];
					$menus .= '<article onclick="clean_filters_silent();window.open(\''.$URL_Link.'?filtro='.$cat_id.'_'.$valor_id.'\',\'_self\')"><a href="'.$URL_Link.'?filtro='.$cat_id.'_'.$valor_id.'">'.$menu_valor_nome.'</a></article>';
					$rs2->db_next();
				}
				$menus .= '</div></article>';
			}
			$rs2->db_close();
			$a++;
			$rs->db_next();
		}
	}
	$rs->db_close();

	return $menus;
}


function familias_no_menu()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			ORDER BY pf.Ordem ASC limit 0,20';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		while(!$rs->EOF()){
			$IDb = $rs->db_data['ID'];
			$img_ext = $rs->db_data['img_ext'];
			$Cabecalho = $rs->db_data['Cabecalho'];
			$img_src = '';
			if(dotbright_image_file_exists('files/products_families/'.$IDb.'_2.'.$img_ext) && $img_ext!='') $img_src = 'files/products_families/'.$IDb.'_2.'.$img_ext.'';

			$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);

			
			$return .= '<li class="has-submenu">
							<a href="'.$p_seo.'">
								'.$Cabecalho.'
							</a>'.sub_familias_no_menu($IDb).'
						</li>';
						
			$rs->db_next();
		}
	}
	$rs->db_close();
	return $return;
}
function sub_familias_no_menu($ID)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			AND pf.ID_ORG='.$ID.'
			ORDER BY pf.Ordem ASC';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		$return .= '<ul class="megamenu">';
			$return .= '<li>';
				while(!$rs->EOF()){
					$IDb = $rs->db_data['ID'];
					$img_ext = $rs->db_data['img_ext'];
					$Cabecalho = $rs->db_data['Cabecalho'];
					$img_src = '';
					if(dotbright_image_file_exists('files/products_families/'.$IDb.'_2.'.$img_ext) && $img_ext!='') $img_src = 'files/products_families/'.$IDb.'_2.'.$img_ext.'';
					$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
					
					$return .= '<h4 class="menu-title"><a href="<?=$p_seo?>"><?=$Cabecalho?></a></h4>';
					$return .= sub_sub_familias_no_menu($IDb);
																			
					$rs->db_next();
				}
			$return .= '</li>';
		$return .= '</ul>';
	}
	$rs->db_close();
	return $return;
}
function sub_sub_familias_no_menu($ID){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			AND pf.ID_ORG='.$ID.'
			ORDER BY pf.Ordem ASC ';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		$return .= '<hr class="divider">';
		$return .= '<ul>';
		while(!$rs->EOF()){
			$IDb = $rs->db_data['ID'];
			$img_ext = $rs->db_data['img_ext'];
			$Cabecalho = $rs->db_data['Cabecalho'];
			$img_src = '';
			if(dotbright_image_file_exists('files/products_families/'.$IDb.'_2.'.$img_ext) && $img_ext!='') $img_src = 'files/products_families/'.$IDb.'_2.'.$img_ext.'';

			$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
			
			$return .= '<li><a href="'.$p_seo.'">'.$Cabecalho.'</a></li>';
			
			$rs->db_next();
		}
		$return .= '</ul>';
	}
	$rs->db_close();

	return $return;
}
function familias($ID=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			AND pf.ID_ORG='.$ID.'
			ORDER BY pf.Ordem ASC ';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		$return .= '<hr class="divider">';
		$return .= '<ul>';
		while(!$rs->EOF()){
			$IDb = $rs->db_data['ID'];
			$img_ext = $rs->db_data['img_ext'];
			$Cabecalho = $rs->db_data['Cabecalho'];
			$img_src = '';
			$p_seo = seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);											
			$return .= '<li><a href="'.$p_seo.'">'.$Cabecalho.'</a></li>';											
			$rs->db_next();
		}
		$return .= '</ul>';
	}
	$rs->db_close();

	return $return;  

}
function show_menu_options($ID = 0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$URL_Nome='';

	$sql='SELECT menus_links.ID,menus_links.TipoMenu,menus_links.catalogo_id,menus_links.class,menus_links.video,menus_links.img_ext,menus_links.img3_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2 
	FROM menus_links 
	INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID 
	WHERE menus_links.ID="'.$ID.'" AND Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" 
	ORDER BY Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$IDb = $rs->db_data['ID'];
		$I3=$rs->db_data["img3_ext"];
		$URL_Nome=$rs->db_data["URL_Nome"];
		if(dotbright_image_file_exists("files/menus/$IDb"."_3.".$I3))$icon="files/menus/$IDb"."_3.".$I3.'?d='.uniqid('');
	}
	$rs->db_close();


	$sql='SELECT menus_links.ID,menus_links.TipoMenu,menus_links.catalogo_id,menus_links.class,menus_links.video,menus_links.img_ext,menus_links.img3_ext,menus_links.novajanela,menus_links.URL_Link,menus_links_lang.URL_Nome2 
	FROM menus_links 
	INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID 
	WHERE menus_links.ID_ORG="'.$ID.'" AND Visivel=1 AND menus_links_lang.Lang="'.$site_lang.'" 
	ORDER BY Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$IDb = $rs->db_data['ID'];
		$I3=$rs->db_data["img3_ext"];
		$URL_Nome=$rs->db_data["URL_Nome"];
		
		$rs->db_next();
	}
	$rs->db_close();



}
function show_menu_lateral_novo($id_pai,$tipo=0,$showfilter = 0)
{
	if($_SESSION['ss_ismobile']==1)return;
	//-------------------------------------
											
	//<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1688','1688',1).'">Capas e Bolsas</a></li>
	//<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1542','1542',1).'">Películas</a></li>

    $return.= ' <div class="dropdown category-dropdown" id="clickDropdown">
					<a class="category-toggle text-capitalize pl-0 bg-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-display="static" title="Browse Categories" id="categoryDropdownToggle">
						<div id="img-menu-left"></div> 
						<span id="cat_menu_left"> Categorias</span>
					</a>';
		$return .= '<div class="dropdown-box text-default">';
			$return .= '<ul class="menu vertical-menu category-menu">';

				//$return .= show_menu_options(178);

				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="files/menus/222_1.svg" alt="SMARTPHONES">
									MOBILE</a>
								<ul class="megamenu" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li>
										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2998','2998',1).'">Smartphones e Telemóveis</a></h4>
										<hr class="divider">
										<ul>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3071','3071',1).'">Smartphones Android</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3433','3433',1).'">Smartphones iPhones</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2999','2999',1).'">Capas e Bolsas</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3007','3007',1).'">Películas</a></li>
										</ul>

										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3016','3016',1).'">Tablets</a></h4>
										<hr class="divider">
										<ul>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3495','3495',1).'">Android</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3494','3494',1).'">iPad</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3064','3064',1).'">Películas para Tablets</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3019','3019',1).'">Capas e Bolsas para Tablets</a></li>
										</ul>
									</li>
									<li>
										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3211','3211',1).'">WEARABLES</a></h4>
										<hr class="divider">
										<ul>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3474','3474',1).'">SmartWatch</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3478','3478',1).'">Smartband</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3422','3422',1).'">Pulseiras e Braceletes</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3212','3212',1).'">Películas</a></li>
										</ul>

										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2984','2984',1).'">AUDIO</a></h4>
										<hr class="divider">
										<ul>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3405','3405',1).'">Earbuds & Earphones</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3404','3404',1).'">Headphones</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2985','2985',1).'">Colunas Portáteis</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3305','3305',1).'">Capas para AirPods</a></li>
										</ul>
									</li>
									<li>
										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3519','3519',1).'">TELEMÓVEIS SENIORES E TECLAS</a></h4>
										<hr class="divider">
										
										<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2986','2986',1).'">ACESSÓRIOS</a></h4>
										<hr class="divider">
										<ul>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3079','3079',1).'">Carregadores</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3096','3096',1).'">Canetas Stylus</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3081','3081',1).'">Suportes</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3421','3421',1).'">Dispositivos de Rastreamento e Tags</a></li>
											<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2987','2987',1).'">Powerbanks</a></li>
										</ul>										
									</li>
									<li>
										<div class="menu-banner banner-fixed menu-banner4">
											<figure>
												<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-4.jpg" alt="Menu Banner"
													width="235" height="433" />
											</figure>
										</div>
									</li>
								</ul>
							</li>';
							
				
				$return .= '<li>
								<a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=163','163',1).'"><img class="icon_menu" src="files/menus/seminovos.svg" alt="SEMINOVOS">
								SEMINOVOS</a>
							</li>';

				$return .= '<li>
								<a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3100','3100',1).'"><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/mobile-phone.svg" alt="CABOS E ADAPTADORES">
								CABOS E ADAPTADORES</a>
							</li>';


				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="files/menus/223_1.svg" alt="Tablets">
								IMAGEM & SOM</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
									<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3408','3408',1).'">Home audio</a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3449','3449',1).'">Soundbars</a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=177','177',1).'">Video</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3409','3409',1).'">Câmaras de ação</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3410','3410',1).'">Acessórios</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3413','3413',1).'">Câmaras digitais</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3457','3457',1).'">Entretenimento e Streaming</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3458','3458',1).'">Reprodutores multimédia</a></li>
											</ul>	
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner"
														width="410" height="123"
														style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner"
														width="410" height="123"
														style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="files/menus/225_1.svg" alt="SMARTHOME E DOMÓTICA">
								SMARTHOME E DOMÓTICA</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3402','3402',1).'">Domótica</a></h4>
											<hr class="divider" />
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3414','3414',1).'">Câmaras de segurança</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3425','3425',1).'">Sensores e detetores</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3452','3452',1).'">Tomadas inteligentes</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3403','3403',1).'">Controlo remoto</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3472','3472',1).'">Comutadores inteligentes</a></li>
											</ul>
										</div>
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3390','3390',1).'">Iluminação</a></h4>
											<hr class="divider" />
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3391','3391',1).'">Lâmpadas</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3392','3392',1).'">Exterior</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3401','3401',1).'">Fitas LED</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3400','3400',1).'">Candeeiros de mesa</a></li>
											</ul>
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner"
														width="410" height="123"
														style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner"
														width="410" height="123"
														style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/pequenos_eletrodomesticos.svg" alt="som e video">
								PEQUENOS ELETRODOMÉSTICOS</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3415','3415',1).'">Preparação de alimentos</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3416','3416',1).'">AirFyer</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3451','3451',1).'">Grelhadores e Fritadeiras</a></li>
											</ul>

											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3395','3395',1).'">Limpeza de superfícies</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3396','3396',1).'">Aspiradores</a></li>												
											</ul>

											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3459','3459',1).'">Climatização e aquecimento</a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3417','3417',1).'">Desinfeção e esterilização</a></h4>
											<hr class="divider">
											
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3463','3463',1).'">Bricolage e jardim</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3464','3464',1).'">Bricolage</a></li>
											</ul>
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/saude_beleza.svg" alt="SAÚDE E BELEZA">
								SAÚDE E BELEZA</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3419','3419',1).'">Saúde</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3420','3420',1).'">Balanças</a></li>
											</ul>
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3461','3461',1).'">Cabelo</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3462','3462',1).'">Secador cabelo</a></li>
											</ul>
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3466','3466',1).'">Cuidado Feminino</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3467','3467',1).'">Rosto e corpo</a></li>
											</ul>
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';

				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/acessorios.svg">
								CONSOLAS, JOGOS & ACESSÓRIOS</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3095','3095',1).'">Consolas & Jogos </a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3095','3095',1).'">Controladores gaming</a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title mt-1"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3095','3095',1).'">Jogos & Puzzles</a></h4>
											<hr class="divider">
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';	

				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/desporto_mobilidade.svg" alt="DESPORTO E MOBILIDADE">
								DESPORTO E MOBILIDADE</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3453','3453',1).'">Mobilidade</a></h4>
											<hr class="divider">
										</div>
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3399','3399',1).'">Acessórios para Carro</a></h4>
											<hr class="divider">
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/redes_energia.svg" alt="REDES E ENERGIA">
								REDES E ENERGIA</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3437','3437',1).'">Energia</a></h4>
											<hr class="divider" />
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3448','3448',1).'">Pilhas e acessórios</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3438','3438',1).'">Extensões elétricas</a></li>
											</ul>
										</div>
										<div class="col-md-6 col-lg-6 col-6">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3469','3469',1).'">Redes</a></h4>
											<hr class="divider" />
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3470','3470',1).'">Expansão de rede</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3471','3471',1).'">Modems e Routers</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3482','3482',1).'">Adaptadores de rede</a></li>
											</ul>
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				$return .= '<li class="has-submenu">
								<a><img class="icon_menu" src="files/menus/226_1.svg" alt="INFORMÁTICA">
									INFORMÁTICA</a>
								<ul class="megamenu type2" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
									<li class="row">
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3083','3083',1).'">Periféricos</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3085','3085',1).'">Ratos e Teclados</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3084','3084',1).'">Webcams</a></li>																
											</ul>
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1532','1532',1).'">Armazenamento</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1533','1533',1).'">Cartões de memória</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3456','3456',1).'">Leitor de cartões</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1534','1534',1).'">Pen drive</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2500','2500',1).'">HDD,SSD & NVMe</a></li>
											</ul>															
										</div>
										<div class="col-md-4 col-lg-4 col-4">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3385','3385',1).'">Carregadores e transformadores</a></h4>
											<hr class="divider">
											<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=1536','1536',1).'">Acessórios</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3086','3086',1).'">Bolsas e Pastas</a></li>
												<li><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=3097','3097',1).'">Mochilas</a></li>
											</ul>
										</div>
									</li>
									<li class="row">
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner" width="410" height="123" style="background-color:#D2D2D2;" />
												</figure>													
											</div>
										</div>
										<div class="col-6">
											<div class="banner banner-fixed menu-banner5 br-xs">
												<figure>
													<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner" width="410" height="123" style="background-color:#9F9888;" />
												</figure>													
											</div>
										</div>
									</li>
								</ul>
							</li>';
				
				/*$return .= '<li>
								<a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=85','85',1).'"><img class="icon_menu" src="files/menus/228_1.svg" alt="Reparacoes">
								REPARAÇÕES</a>
							</li>';*/

				

				$return .= '<li class="has-submenu">
					<a><img class="icon_menu" src="files/menus/228_1.svg" alt="REPARAÇÕES">
						REPARAÇÕES</a>
					<ul class="megamenu" style="padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
						<li>'.gera_menu_f(86,$GLOBALS['reparacoes_page_id']).gera_menu_f(102,$GLOBALS['reparacoes_page_id']).gera_menu_f(124,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(929,$GLOBALS['reparacoes_page_id']).gera_menu_f(110,$GLOBALS['reparacoes_page_id']).gera_menu_f(2678,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(155,$GLOBALS['reparacoes_page_id']).gera_menu_f(1206,$GLOBALS['reparacoes_page_id']).gera_menu_f(2685,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(134,$GLOBALS['reparacoes_page_id']).gera_menu_f(119,$GLOBALS['reparacoes_page_id']).gera_menu_f(130,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(141,$GLOBALS['reparacoes_page_id']).gera_menu_f(100,$GLOBALS['reparacoes_page_id']).gera_menu_f(115,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(144,$GLOBALS['reparacoes_page_id']).gera_menu_f(98,$GLOBALS['reparacoes_page_id']).gera_menu_f(1342,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(101,$GLOBALS['reparacoes_page_id']).gera_menu_f(150,$GLOBALS['reparacoes_page_id']).gera_menu_f(995,$GLOBALS['reparacoes_page_id']).'</li>
						<li>'.gera_menu_f(927,$GLOBALS['reparacoes_page_id']).gera_menu_f(716,$GLOBALS['reparacoes_page_id']).gera_menu_f(2556,$GLOBALS['reparacoes_page_id']).'</li>
					</ul>
				</li>';
			$return .= '</ul>';
		$return .= '</div>';
	$return .= '</div>';

	?>
	<script>
		document.addEventListener("DOMContentLoaded",function () {
			var clickDropdown = document.getElementById("clickDropdown");
			var categoryDropdownToggle = document.getElementById("categoryDropdownToggle");

			categoryDropdownToggle.addEventListener("click",function (event) {
				// event.preventDefault();
				clickDropdown.classList.toggle("dropdown_new_show");
			});

			var hasSubmenusNew = document.querySelectorAll(".has-submenu");
			var activeSubmenu = null;

			hasSubmenusNew.forEach(function (hasSubmenu) {
				hasSubmenu.addEventListener("click",function (event) {
					// event.preventDefault();
					var megamenuNews = document.querySelectorAll(".megamenu");
					megamenuNews.forEach(function (megamenuNew) {
						if (megamenuNew !== hasSubmenu.querySelector(".megamenu")) {
							megamenuNew.classList.remove("megamenu_new_show");
						}
					});

					this.classList.toggle("submenu_active");


					var megamenuNew = this.querySelector(".megamenu");
					if (megamenuNew) {
						megamenuNew.classList.toggle("megamenu_new_show");
					}

					if (activeSubmenu && activeSubmenu !== this) {
						activeSubmenu.classList.remove("submenu_active");
						var prevMegamenu = activeSubmenu.querySelector(".megamenu_new_show");
						if (prevMegamenu) {
							prevMegamenu.classList.remove("megamenu_new_show");
						}
					}

					activeSubmenu = this;

					// megamenuNew.classList.toggle("megamenu_new_show");
					// hasSubmenu.classList.toggle("submenu_active");
				});
			});

			document.addEventListener("click",function (event) {
				var isInsideDropdown = clickDropdown.contains(event.target);
				var isInsideHasSubmenu = Array.from(hasSubmenusNew).some(function (hasSubmenu) {
					return hasSubmenu.contains(event.target);
				});

				if (!isInsideDropdown && !isInsideHasSubmenu) {
					var megamenuNews = document.querySelectorAll(".megamenu");
					megamenuNews.forEach(function (megamenuNew) {
						megamenuNew.classList.remove("megamenu_show");
					});

					hasSubmenusNew.forEach(function (hasSubmenu) {
						hasSubmenu.classList.remove("submenu_active");
					});

					if (clickDropdown.classList.contains('dropdown_new_show')) {
						clickDropdown.classList.remove('dropdown_new_show');
					}

					activeSubmenu = null;
				}
			});
		});

	</script>
	<?php
    return $return;
}
function gera_menu_f($ID=0,$Page_id='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	if(!$Page_id)$Page_id=$GLOBALS['catalog_page_id'];


	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			AND pf.ID="'.$ID.'"';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$Cabecalho = trim(str_replace('Reparações','',$rs->db_data['Cabecalho']));
		$Cabecalho = trim(str_replace('Reparacoes','',$Cabecalho));
		$p_seo = seo('pages.php?page_id='.$Page_id.'&cat='.$ID,$ID,1);						
		$return .= '<h4 class="menu-title"><a href="'.$p_seo.'">'.$Cabecalho.'</a></h4>';
		$return .= gera_sub_menu_f($ID,$Page_id);
	}
	$rs->db_close();
	return $return;
}

function gera_sub_menu_f($ID_ORG=0,$Page_id='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql = 'SELECT pf.*,Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID 
			WHERE pf.visivel=1 AND pfl.Lang="'.$GLOBALS['site_lang'].'"
			AND pf.ID_ORG="'.$ID_ORG.'"';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$return .= '<hr class="divider">';
		$return .= '<ul>';
		$t=$rs->db_rows;
		$n=0;
		while(!$rs->EOF())
		{
			if($n<5)
			{
				$IDb = $rs->db_data['ID'];
				$img_ext = $rs->db_data['img_ext'];
				$Cabecalho = $rs->db_data['Cabecalho'];
				$img_src = '';
				$p_seo = seo('pages.php?page_id='.$Page_id.'&cat='.$IDb,$IDb,1);											
				$return .= '<li><a href="'.$p_seo.'">'.$Cabecalho.'</a></li>';
			}
			$n++;
			$rs->db_next();
		}
		if($t>5)
		{
			$p_seo = seo('pages.php?page_id='.$Page_id.'&cat='.$ID_ORG,$ID_ORG,1);
			$return .= '<li><a style="color:#ff8a00" href="'.$p_seo.'"> » ver mais « </a></li>';
		}
		$return .= '</ul>';
	}
	$rs->db_close();

	return $return;
}
function show_menu_lateral_novo_old2($id_pai,$tipo=0,$showfilter = 0)
{
    $return .= ' <div class="dropdown category-dropdown">
						<a href="#" class="category-toggle text-capitalize pl-0 bg-white" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true" data-display="static" title="Browse Categories">							
							<div id="img-menu-left"></div> 
							<span id="cat_menu_left"> Categorias</span>
						</a>

						<div class="dropdown-box text-default">
							<ul class="menu vertical-menu category-menu">
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-android"></i>SMARTPHONES
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Laptops &amp;Computers</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Desktop
														Computers</a></li>
												<li><a href="shop-fullwidth-banner.html">Monitors</a></li>
												<li><a href="shop-fullwidth-banner.html">Laptops</a></li>
												<li><a href="shop-fullwidth-banner.html">Hard Drives &amp;
														Storage</a></li>
												<li><a href="shop-fullwidth-banner.html">Computer
														Accessories</a></li>
											</ul>

											<h4 class="menu-title mt-1">TV &amp;Video</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">TVs</a></li>
												<li><a href="shop-fullwidth-banner.html">Home Audio
														Speakers</a></li>
												<li><a href="shop-fullwidth-banner.html">Projectors</a></li>
												<li><a href="shop-fullwidth-banner.html">Media Streaming
														Devices</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Digital Cameras</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Digital SLR
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Sports & Action
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Camera Lenses</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Photo Printer</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Digital Memory
														Cards</a></li>
											</ul>

											<h4 class="menu-title mt-1">Cell Phones</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Carrier Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Unlocked Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Phone & Cellphone
														Cases</a></li>
												<li><a href="shop-fullwidth-banner.html">Cellphone
														Chargers</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner4">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-4.jpg" alt="Menu Banner"
														width="235" height="433" />
												</figure>
												<div class="banner-content">
													<h4 class="banner-subtitle font-weight-normal">Ofertas da Semana
														</h4>
													<h3 class="banner-title text-white">Economize no fone de ouvido inteligente
													</h3>
													<div
														class="banner-price-info text-primary font-weight-bolder text-uppercase text-primary">
														20% Desconto</div>
													<a href="shop.html"
														class="btn btn-white btn-outline btn-sm btn-rounded">Comprar
														Agora</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-gift"></i>SEMINOVOS
									</a>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-android"></i>TABLETS
									</a>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-furniture"></i>SMARTWATCHS
									</a>
									<ul class="megamenu type2" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li class="row">
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-158-smartwatchs-pt">SmartWatchs</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-33-braceletes-pt">Bracelets</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2576-capas-para-smartwatch-pt">Capas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Capas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2572-peliculas-para-smartwatch-pt">Películas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Películas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Huawei Watch</a></li>
												</ul>
											</div>
										</li>
										<li class="row">
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#D2D2D2;" />
													</figure>
													<div class="banner-content text-right y-50">
														<h4
															class="banner-subtitle font-weight-normal text-default text-capitalize">
															New Arrivals</h4>
														<h3 class="banner-title text-capitalize">
															Amazing Sofa</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal">
															Starting at <strong>$125.00</strong></div>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#9F9888;" />
													</figure>
													<div class="banner-content y-50">
														<h4
															class="banner-subtitle font-weight-normal text-white text-capitalize">
															Best Seller</h4>
														<h3 class="banner-title text-capitalize text-white">
															Chair &amp;Lamp</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal text-white">
															From <strong>$165.00</strong></div>
													</div>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="p2-c-163-seminovos-amp-oportunidades-pt">
										<i class="w-icon-electronics"></i>SOM & VIDEO
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title"><a href="p2-c-162-colunas-e-som-pt">Colunas e Som</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery & Watches</a></li>

											</ul>
										</li>
										<li>
											<h4 class="menu-title"><a href="p2-c-177-action-cams-pt">Action Cams</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery &Watches</a></li>
											</ul>
										</li>
										<li>
											<div class="banner-fixed menu-banner menu-banner2">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-2.jpg" alt="Menu Banner"
														width="235" height="347" />
												</figure>
												<div class="banner-content">
													<div class="banner-price-info mb-1 ls-normal">Get up to
														<strong
															class="text-primary text-uppercase">20%Off</strong>
													</div>
													<h3 class="banner-title">Hot Sales</h3>
													<a href="shop.html"
														class="btn btn-dark btn-sm btn-link btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-home"></i>ACESSÓRIOS
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Bedroom</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Beds,Frames &
														Bases</a></li>
												<li><a href="shop-fullwidth-banner.html">Dressers</a></li>
												<li><a href="shop-fullwidth-banner.html">Nightstands</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kid\'s Beds &
														Headboards</a></li>
												<li><a href="shop-fullwidth-banner.html">Armoires</a></li>
											</ul>

											<h4 class="menu-title mt-1">Living Room</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Coffee Tables</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Chairs</a></li>
												<li><a href="shop-fullwidth-banner.html">Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Futons & Sofa
														Beds</a></li>
												<li><a href="shop-fullwidth-banner.html">Cabinets &
														Chests</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Office</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Office Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Desks</a></li>
												<li><a href="shop-fullwidth-banner.html">Bookcases</a></li>
												<li><a href="shop-fullwidth-banner.html">File Cabinets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Breakroom
														Tables</a></li>
											</ul>

											<h4 class="menu-title mt-1">Kitchen & Dining</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Dining Sets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kitchen Storage
														Cabinets</a></li>
												<li><a href="shop-fullwidth-banner.html">Bashers Racks</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Room
														Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Bar Stools</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner3">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-3.jpg" alt="Menu Banner"
														width="235" height="461" />
												</figure>
												<div class="banner-content">
													<h4
														class="banner-subtitle font-weight-normal text-white mb-1">
														Restroom</h4>
													<h3 class="banner-title text-white">
														Furniture Sale</h3>
													<div
														class="banner-price-info text-white font-weight-normal ls-25">
														Up to <span
															class="text-primary text-uppercase font-weight-bold">25%
															Off</span></div>
													<a href="shop.html"
														class="btn btn-white btn-link btn-sm btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-tools"></i>REPARAÇÕES
									</a>
								</li>
								<li>
									<a href="shop.html"
										class="font-weight-bold text-uppercase text-primary ls-25">
										View All Categories<i class="w-icon-angle-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>';

    return $return;


}

function show_menu_leteral_novo_dot($id_pai=1,$tipo=0,$showfilter = 0)
{
    $return .= ' <div class="dropdown category-dropdown">
						<a href="#" class="category-toggle text-capitalize pl-0 bg-white" role="button"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
							data-display="static" title="Browse Categories">
							<span> <img id="img-menu-left" src="layout/imgs/menu.svg" style="margin-right:30px" width="30"> Categorias</span>
						</a>

						<div class="dropdown-box text-default">
							<ul class="menu vertical-menu category-menu">
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
											<div class="img">
										    <img src="files/menus/222_1.svg" alt="SMARTPHONES">
										    </div>
										SMARTPHONES
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Laptops &amp;Computers</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Desktop
														Computers</a></li>
												<li><a href="shop-fullwidth-banner.html">Monitors</a></li>
												<li><a href="shop-fullwidth-banner.html">Laptops</a></li>
												<li><a href="shop-fullwidth-banner.html">Hard Drives &amp;
														Storage</a></li>
												<li><a href="shop-fullwidth-banner.html">Computer
														Accessories</a></li>
											</ul>

											<h4 class="menu-title mt-1">TV &amp;Video</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">TVs</a></li>
												<li><a href="shop-fullwidth-banner.html">Home Audio
														Speakers</a></li>
												<li><a href="shop-fullwidth-banner.html">Projectors</a></li>
												<li><a href="shop-fullwidth-banner.html">Media Streaming
														Devices</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Digital Cameras</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Digital SLR
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Sports & Action
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Camera Lenses</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Photo Printer</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Digital Memory
														Cards</a></li>
											</ul>

											<h4 class="menu-title mt-1">Cell Phones</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Carrier Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Unlocked Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Phone & Cellphone
														Cases</a></li>
												<li><a href="shop-fullwidth-banner.html">Cellphone
														Chargers</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner4">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-4.jpg" alt="Menu Banner"
														width="235" height="433" />
												</figure>
												<div class="banner-content">
													<h4 class="banner-subtitle font-weight-normal">Ofertas da Semana
														</h4>
													<h3 class="banner-title text-white">Economize no fone de ouvido inteligente
													</h3>
													<div
														class="banner-price-info text-primary font-weight-bolder text-uppercase text-primary">
														20% Desconto</div>
													<a href="shop.html"
														class="btn btn-white btn-outline btn-sm btn-rounded">Comprar
														Agora</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-gift"></i>SEMINOVOS
									</a>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="p2-c-11-tablets-pt"></i>TABLETS
									</a>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-electronics"></i>SMARTWATCHS
									</a>
									<ul class="megamenu type2" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li class="row">
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-158-smartwatchs-pt">SmartWatchs</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-33-braceletes-pt">Bracelets</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2576-capas-para-smartwatch-pt">Capas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Capas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2572-peliculas-para-smartwatch-pt">Películas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Películas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Huawei Watch</a></li>
												</ul>
											</div>
										</li>
										<li class="row">
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#D2D2D2;" />
													</figure>
													<div class="banner-content text-right y-50">
														<h4
															class="banner-subtitle font-weight-normal text-default text-capitalize">
															New Arrivals</h4>
														<h3 class="banner-title text-capitalize">
															Amazing Sofa</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal">
															Starting at <strong>$125.00</strong></div>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#9F9888;" />
													</figure>
													<div class="banner-content y-50">
														<h4
															class="banner-subtitle font-weight-normal text-white text-capitalize">
															Best Seller</h4>
														<h3 class="banner-title text-capitalize text-white">
															Chair &amp;Lamp</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal text-white">
															From <strong>$165.00</strong></div>
													</div>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="p2-c-163-seminovos-amp-oportunidades-pt">
										<i class="w-icon-movie"></i>SOM & VIDEO
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title"><a href="p2-c-162-colunas-e-som-pt">Colunas e Som</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery & Watches</a></li>

											</ul>
										</li>
										<li>
											<h4 class="menu-title"><a href="p2-c-177-action-cams-pt">Action Cams</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery &Watches</a></li>
											</ul>
										</li>
										<li>
											<div class="banner-fixed menu-banner menu-banner2">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-2.jpg" alt="Menu Banner"
														width="235" height="347" />
												</figure>
												<div class="banner-content">
													<div class="banner-price-info mb-1 ls-normal">Get up to
														<strong
															class="text-primary text-uppercase">20%Off</strong>
													</div>
													<h3 class="banner-title">Hot Sales</h3>
													<a href="shop.html"
														class="btn btn-dark btn-sm btn-link btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-home"></i>ACESSÓRIOS
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Bedroom</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Beds,Frames &
														Bases</a></li>
												<li><a href="shop-fullwidth-banner.html">Dressers</a></li>
												<li><a href="shop-fullwidth-banner.html">Nightstands</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kid\'s Beds &
														Headboards</a></li>
												<li><a href="shop-fullwidth-banner.html">Armoires</a></li>
											</ul>

											<h4 class="menu-title mt-1">Living Room</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Coffee Tables</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Chairs</a></li>
												<li><a href="shop-fullwidth-banner.html">Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Futons & Sofa
														Beds</a></li>
												<li><a href="shop-fullwidth-banner.html">Cabinets &
														Chests</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Office</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Office Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Desks</a></li>
												<li><a href="shop-fullwidth-banner.html">Bookcases</a></li>
												<li><a href="shop-fullwidth-banner.html">File Cabinets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Breakroom
														Tables</a></li>
											</ul>

											<h4 class="menu-title mt-1">Kitchen & Dining</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Dining Sets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kitchen Storage
														Cabinets</a></li>
												<li><a href="shop-fullwidth-banner.html">Bashers Racks</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Room
														Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Bar Stools</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner3">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-3.jpg" alt="Menu Banner"
														width="235" height="461" />
												</figure>
												<div class="banner-content">
													<h4
														class="banner-subtitle font-weight-normal text-white mb-1">
														Restroom</h4>
													<h3 class="banner-title text-white">
														Furniture Sale</h3>
													<div
														class="banner-price-info text-white font-weight-normal ls-25">
														Up to <span
															class="text-primary text-uppercase font-weight-bold">25%
															Off</span></div>
													<a href="shop.html"
														class="btn btn-white btn-link btn-sm btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-gamepad"></i>REPARAÇÕES
									</a>
								</li>
								<li>
									<a href="shop.html"
										class="font-weight-bold text-uppercase text-primary ls-25">
										View All Categories<i class="w-icon-angle-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>';

    return $return;

}

function show_menu_leteral_novo_dotold(){
	$return .= ' <div class="dropdown category-dropdown">
						<a href="#" class="category-toggle text-capitalize pl-0 bg-white" role="button"
							data-toggle="dropdown" aria-haspopup="true" aria-expanded="true"
							data-display="static" title="Browse Categories">
							<!--<i class="w-icon-category"></i>-->
							<span> <img id="img-menu-left" src="layout/imgs/menu.svg" style="margin-right:30px" width="30"> Categorias</span>
						</a>

						<div class="dropdown-box text-default">
							<ul class="menu vertical-menu category-menu">
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<!--<i class="w-icon-tshirt2"></i>-->
										<div class="img">
										    <img src="files/menus/222_1.svg" alt="SMARTPHONES">
										    </div>
										SMARTPHONES
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Laptops &amp;Computers</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Desktop
														Computers</a></li>
												<li><a href="shop-fullwidth-banner.html">Monitors</a></li>
												<li><a href="shop-fullwidth-banner.html">Laptops</a></li>
												<li><a href="shop-fullwidth-banner.html">Hard Drives &amp;
														Storage</a></li>
												<li><a href="shop-fullwidth-banner.html">Computer
														Accessories</a></li>
											</ul>

											<h4 class="menu-title mt-1">TV &amp;Video</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">TVs</a></li>
												<li><a href="shop-fullwidth-banner.html">Home Audio
														Speakers</a></li>
												<li><a href="shop-fullwidth-banner.html">Projectors</a></li>
												<li><a href="shop-fullwidth-banner.html">Media Streaming
														Devices</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Digital Cameras</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Digital SLR
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Sports & Action
														Cameras</a></li>
												<li><a href="shop-fullwidth-banner.html">Camera Lenses</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Photo Printer</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Digital Memory
														Cards</a></li>
											</ul>

											<h4 class="menu-title mt-1">Cell Phones</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Carrier Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Unlocked Phones</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Phone & Cellphone
														Cases</a></li>
												<li><a href="shop-fullwidth-banner.html">Cellphone
														Chargers</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner4">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-4.jpg" alt="Menu Banner"
														width="235" height="433" />
												</figure>
												<div class="banner-content">
													<h4 class="banner-subtitle font-weight-normal">Ofertas da Semana
														</h4>
													<h3 class="banner-title text-white">Economize no fone de ouvido inteligente
													</h3>
													<div
														class="banner-price-info text-primary font-weight-bolder text-uppercase text-primary">
														20% Desconto</div>
													<a href="shop.html"
														class="btn btn-white btn-outline btn-sm btn-rounded">Comprar
														Agora</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-gift"></i>SEMINOVOS
									</a>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="p2-c-11-tablets-pt"></i>TABLETS
									</a>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-electronics"></i>SMARTWATCHS
									</a>
									<ul class="megamenu type2" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li class="row">
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-158-smartwatchs-pt">SmartWatchs</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-33-braceletes-pt">Bracelets</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2576-capas-para-smartwatch-pt">Capas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Capas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Capas para Huawei Watch</a></li>
												</ul>
											</div>
											<div class="col-md-3 col-lg-3 col-6">
												<h4 class="menu-title"><a href="p2-c-2572-peliculas-para-smartwatch-pt">Películas para Smartwhatch</a></h4>
												<hr class="divider" />
												<ul>
													<li><a href="shop-fullwidth-banner.html">Películas para Apple Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Samsung Watch</a></li>
													<li><a href="shop-fullwidth-banner.html">Películas para Huawei Watch</a></li>
												</ul>
											</div>
										</li>
										<li class="row">
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-5.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#D2D2D2;" />
													</figure>
													<div class="banner-content text-right y-50">
														<h4
															class="banner-subtitle font-weight-normal text-default text-capitalize">
															New Arrivals</h4>
														<h3 class="banner-title text-capitalize">
															Amazing Sofa</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal">
															Starting at <strong>$125.00</strong></div>
													</div>
												</div>
											</div>
											<div class="col-6">
												<div class="banner banner-fixed menu-banner5 br-xs">
													<figure>
														<img src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-6.jpg" alt="Banner"
															width="410" height="123"
															style="background-color:#9F9888;" />
													</figure>
													<div class="banner-content y-50">
														<h4
															class="banner-subtitle font-weight-normal text-white text-capitalize">
															Best Seller</h4>
														<h3 class="banner-title text-capitalize text-white">
															Chair &amp;Lamp</h3>
														<div
															class="banner-price-info font-weight-normal ls-normal text-white">
															From <strong>$165.00</strong></div>
													</div>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="p2-c-163-seminovos-amp-oportunidades-pt">
										<i class="w-icon-movie"></i>SOM & VIDEO
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title"><a href="p2-c-162-colunas-e-som-pt">Colunas e Som</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery & Watches</a></li>

											</ul>
										</li>
										<li>
											<h4 class="menu-title"><a href="p2-c-177-action-cams-pt">Action Cams</a></h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">New Arrivals</a></li>
												<li><a href="shop-fullwidth-banner.html">Best Sellers</a></li>
												<li><a href="shop-fullwidth-banner.html">Trending</a></li>
												<li><a href="shop-fullwidth-banner.html">Clothing</a></li>
												<li><a href="shop-fullwidth-banner.html">Shoes</a></li>
												<li><a href="shop-fullwidth-banner.html">Bags</a></li>
												<li><a href="shop-fullwidth-banner.html">Accessories</a></li>
												<li><a href="shop-fullwidth-banner.html">Jewlery &Watches</a></li>
											</ul>
										</li>
										<li>
											<div class="banner-fixed menu-banner menu-banner2">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-2.jpg" alt="Menu Banner"
														width="235" height="347" />
												</figure>
												<div class="banner-content">
													<div class="banner-price-info mb-1 ls-normal">Get up to
														<strong
															class="text-primary text-uppercase">20%Off</strong>
													</div>
													<h3 class="banner-title">Hot Sales</h3>
													<a href="shop.html"
														class="btn btn-dark btn-sm btn-link btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li class="has-submenu">
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-home"></i>ACESSÓRIOS
									</a>
									<ul class="megamenu" style="  padding:0 0 0 2rem;-webkit-transform:translate3d(-15px,0,0);transform:translate3d(-15px,0,0);">
										<li>
											<h4 class="menu-title">Bedroom</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Beds,Frames &
														Bases</a></li>
												<li><a href="shop-fullwidth-banner.html">Dressers</a></li>
												<li><a href="shop-fullwidth-banner.html">Nightstands</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kid\'s Beds &
														Headboards</a></li>
												<li><a href="shop-fullwidth-banner.html">Armoires</a></li>
											</ul>

											<h4 class="menu-title mt-1">Living Room</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Coffee Tables</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Chairs</a></li>
												<li><a href="shop-fullwidth-banner.html">Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Futons & Sofa
														Beds</a></li>
												<li><a href="shop-fullwidth-banner.html">Cabinets &
														Chests</a></li>
											</ul>
										</li>
										<li>
											<h4 class="menu-title">Office</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Office Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Desks</a></li>
												<li><a href="shop-fullwidth-banner.html">Bookcases</a></li>
												<li><a href="shop-fullwidth-banner.html">File Cabinets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Breakroom
														Tables</a></li>
											</ul>

											<h4 class="menu-title mt-1">Kitchen & Dining</h4>
											<hr class="divider">
											<ul>
												<li><a href="shop-fullwidth-banner.html">Dining Sets</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Kitchen Storage
														Cabinets</a></li>
												<li><a href="shop-fullwidth-banner.html">Bashers Racks</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Chairs</a>
												</li>
												<li><a href="shop-fullwidth-banner.html">Dining Room
														Tables</a></li>
												<li><a href="shop-fullwidth-banner.html">Bar Stools</a></li>
											</ul>
										</li>
										<li>
											<div class="menu-banner banner-fixed menu-banner3">
												<figure>
													<img style="width:100%" src="'.SITE_HTTP_URL.'layout/'.PROJETO.'/images/banner-3.jpg" alt="Menu Banner"
														width="235" height="461" />
												</figure>
												<div class="banner-content">
													<h4
														class="banner-subtitle font-weight-normal text-white mb-1">
														Restroom</h4>
													<h3 class="banner-title text-white">
														Furniture Sale</h3>
													<div
														class="banner-price-info text-white font-weight-normal ls-25">
														Up to <span
															class="text-primary text-uppercase font-weight-bold">25%
															Off</span></div>
													<a href="shop.html"
														class="btn btn-white btn-link btn-sm btn-slide-right btn-icon-right">
														Shop Now<i class="w-icon-long-arrow-right"></i>
													</a>
												</div>
											</div>
										</li>
									</ul>
								</li>
								<li>
									<a href="shop-fullwidth-banner.html">
										<i class="w-icon-gamepad"></i>REPARAÇÕES
									</a>
								</li>
								<li>
									<a href="shop.html"
										class="font-weight-bold text-uppercase text-primary ls-25">
										View All Categories<i class="w-icon-angle-right"></i>
									</a>
								</li>
							</ul>
						</div>
					</div>';

return $return;

}

function show_messages_media($id_modulo = 0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang,$page_id;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;

	
    // $sql ="SELECT Name FROM media where Tipo = $id_modulo ORDER BY ID ASC";
    // $rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
    // if(!$rs->EOF())
    // {
    //     echo '<div id="messages_topo">
    //         	<ul style="width:100%">';

    //     while (!$rs->EOF()) 
    //     {
            // echo '<li>'.$rs->db_data['Name'].'</li>';
    //         $rs->db_next();
    //     }
    //     echo '</ul>
    // </div>';
    // }
    // $rs->db_close();
    ?>

	<div class="home_line_title"> 
		<div class="row" id="rollover_text">
			<span>COMPRA · TROCA · VENDA · SEMINOVOS  |  Encontrar o melhor preço nunca foi tão fácil!  |  Descontos,ofertas especiais,promoções!</span>
		</div> 
	</div>
    
    <!-- <script type="text/javascript">
        $('#messages_topo').vTicker();
    </script> -->
    <?php
}
