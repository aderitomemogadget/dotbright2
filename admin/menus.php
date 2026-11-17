<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
	exit();
}
//menu 1
function count_words($str)
{
	while (substr_count($str, "  ") > 0)
	{
		$str=str_replace("  ", " ", $str);
	}

	return substr_count($str, " ") + 1;
}

function show_menu_resp($id_pai=0, $class='')
{


}


function show_menu_slider($id_pai=0, $class='')
{
	$dados_cliente=cliente_logado();

	$ID_ORG=0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG= intval($_GET['cat']);
	}

	$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_slider_'.$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';

	$time_expire=time() - 1*120*1; // Expire Time (2 minute)
	$gerar='';

	if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
	// echo $cache_filename;
	if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire) $return=file_get_contents($cache_filename);
	else
	{
		$rec_fams=array();
		if($ID_ORG > 0) $rec_fams=getCategoriesRec($ID_ORG);


		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$rs3=new Dataobj;
		$return='';$return_resp='';
		$sql='
				SELECT
					m.ID, m.catalogo_id, m.tipo_catalogo, m.img_ext, m.TipoMenu, m.URL_Link,
					ml.URL_Nome2
				FROM
					menus_links m
					INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
				WHERE
					m.ID_ORG="'.$id_pai.'"
					AND m.Visivel=1
					AND ml.Lang="'.$GLOBALS['site_lang'].'"
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' : ' AND (Logado=1 OR Logado=0)').'
				ORDER BY
					m.Ordem ASC
				';


		$rs->db_open($db_link,$DB_name,$sql);


		if(!$rs->EOF())
		{
			// $uri=$_SERVER['REQUEST_URI'];
			// $uri=str_replace($site_http_url,'',$uri);


			$return .= '<div class="menu_img '.$class.'"><div class="item"><div class="leftbutton-menu" id="goToPrevSlide_menu"></div><div class="slidercontent2"><ul id="menu-slider" class="menu-slider">';


			$a=0;
			while(!$rs->EOF())
			{
				$submenus=''; $submenus_mobile='';
				$menu_id=$rs->db_data['ID'];
				$menu_ext=$rs->db_data['img_ext'];
				$catalogo_id=$rs->db_data['catalogo_id'];
				$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
				$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
				$menu_nome=$rs->db_data['URL_Nome2'];
				$menu_url=seo($rs->db_data['URL_Link']);



				$menu_url2=''; $res_fams=''; $class_active='';

				if($catalogo_id > 0 && $menu_tipo == 'familias')
				{
					$menu_url2=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
				}
				elseif($catalogo_id > 0 && $menu_tipo == 'marcas')
				{
					$menu_url2='m-'.$catalogo_id.'-'. str_replace(' ', '', limpa_special($menu_nome)).'-'.$GLOBALS['site_lang'];
				}
				elseif($menu_url != '')
				{
					$menu_url2=$menu_url;
				}

				$img='';
				if(file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="'.$menu_nome.'" />';

				// MENU NIVEL 0
				$class='';
				if($catalogo_id > 0 && $menu_tipo == 'familias' && in_array($catalogo_id, $rec_fams)) $class='class="active2"';
				elseif($catalogo_id > 0 && $menu_tipo == 'marcas' && isset($_GET['s_marca']) && intval($_GET['s_marca']) == $catalogo_id) $class='class="active2"';

				$return .= '<li '.$class.'>';
				$return .= '<a href="'.$menu_url2.'" alt="'.$menu_nome.'" class="effect-shine"><h3><div class="img">'.$img.'</div><br><div class="txt">'.$menu_nome.'</div></h3></a>';
				$return .= '</li>';

				$rs->db_next();
			}

			$return .= ' </ul></div><div class="rightbutton-menu" id="goToNextSlide_menu"></div><div class="clear"></div></div></div>';

		}
		$rs->db_close(1);

		$fh=fopen($cache_filename, 'w');
		fwrite($fh, $return);
		fclose($fh);
		chmod($cache_filename, 0755);
	}

	return $return;
}

function devolve_class($menu_tipo='', $submenus='', $a=0, $active='')
{
	$class='';
	if($menu_tipo == 'favoritos') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'carrinho') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'login') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'linguas') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'minha_conta') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'user_data') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'search') $class=' class="'.$menu_tipo.'"';
	elseif($menu_tipo == 'logout') $class=' class="'.$menu_tipo.'"';
	// elseif($menu_tipo == 'favoritos') $class=' class="'.$menu_tipo.'"';
	// elseif($submenus != '' && $active!='') $class=' class="cbp-hr active"';
	// elseif($submenus != '' && $a==0) $class=' class="cbp-hr active"';
	else $class=' class="cbp-hr"';

	return $class;
}
function show_menu_topologado($id_pai=0, $responsive=0)
{

	$dados_cliente=cliente_logado();

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';$return_resp='';
	$sql='
			SELECT
				m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link,
				ml.URL_Nome2
			FROM
				menus_links m
				INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
			WHERE
				m.ID_ORG="'.$id_pai.'"
				AND m.Visivel=1
				AND ml.Lang="'.$GLOBALS['site_lang'].'"
				'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' : ' AND (Logado=1 OR Logado=0)').'
			ORDER BY
				m.Ordem ASC
			';


	$rs->db_open($db_link,$DB_name,$sql);

	if(!$rs->EOF())
	{
		// $uri=$_SERVER['REQUEST_URI'];
		// $uri=str_replace($site_http_url,'',$uri);


		$return .= '<div class="top_menu">';
		$return .= '<div class="clear"></div>';
		$return .= '<ul class="menu-desk" id="menutop'.$id_pai.'">';

		$return_resp .= '<div class="drop3">';
		$return_resp .= '<div class="m_menu" onclick="BlurryMenu.openMenu()"><i class="fa fa-bars" aria-hidden="true"></i></div>';
		$return_resp .= '<div class="m_respons"><a href="homepage"><img src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE : 'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" /></a></div>';
		$return_resp .= '<div class="m_menu2">';
		$return_resp .= '<div class="m_social"><a href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></div>';
		$return_resp .= '<div class="m_social" ><a href="'.seo('pages.php?page_id='.$GLOBALS['wishlist_page_id']).'?show_wishlist=1"><i class="fa fa-star-o" aria-hidden="true"></i></a></div>';
		$return_resp .= '<div class="m_social" onclick="BlurryMenu.openMenu(1)"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '</div>';
		$return_resp .= '<div id="menu">';


		$return_resp .= '<nav id="powermenu1" class="powerlistmenu"><ul>';
		if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li class="join-m"><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'">Minha Conta</a></li>';
		else $return_resp .= '<li class="join-m"><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1">'.getLinguas('key_registeseaqui').'</a></li>';

		// $return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').'><i style="font-size:24px;overflow: hidden;white-space: nowrap; text-indent: 100%;" class="fa fa-user ">'.(isset($dados_cliente) && is_array($dados_cliente) ? $dados_cliente['Nome'].' '.$dados_cliente['Apelido'] : getLinguas('key_login')).'</i></a><ul><li style="position:relative">'.showFormLogin_MOBILE().'</li></ul></li>';
		// $return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').'><i class="fa fa-user ">'.(isset($dados_cliente) && is_array($dados_cliente) ? $dados_cliente['Nome'].' '.$dados_cliente['Apelido'] : getLinguas('key_login')).'</i></a><ul><li style="position:relative">';
		$return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').' style="font-size:19px !important"><i class="fa fa-user "></i></a><ul><li style="position:relative">';

		ob_start();
		$return2='';
		show_menu_login3(1);
		$return2=ob_get_contents(); //Grab output
		$return_resp .= $return2;
		ob_end_clean(); //Discard output buffer

		$return_resp .= '</li></ul></li>';

		$return_resp .= '<div class="clear"></div>';

		$return_resp .= '<li class="pesqm"><div class="line">';
		$return_resp .= '<script>function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {	Apprise("O campo de pesquisa encontra-se vazio.");	return false;}}</script>';
		$return_resp .= '<form name="pesquisa2" id="searchbox2" action="'.seo('pages.php?page_id=' . $GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa_mobile()">';
		$return_resp .= '<a onclick="return false;" class="text-m"><input type="text" value="" id="pesquisa-mobile" name="pesquisa" /></a>';
		$return_resp .= '<a onclick="return false;" class="pesquisa-m" style="font-size:24px"> <i class="fa fa-search" aria-hidden="true" onclick="$(\'#searchbox2\').submit();"></i></form>';


		$return_resp .= '<div class="clear"></div></div></li>';
		$return_resp .= '<div class="clear"></div>';


		$a=0;
		while(!$rs->EOF())
		{
			$submenus=''; $submenus_mobile='';
			$menu_id=$rs->db_data['ID'];
			$menu_ext=$rs->db_data['img_ext'];
			$catalogo_id=$rs->db_data['catalogo_id'];
			$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
			$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
			$menu_nome=$rs->db_data['URL_Nome2'];
			$menu_url=seo($rs->db_data['URL_Link']);


			$img='';
			if(file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img class="svg" src="files/menus/'.$menu_id.'_1.'.$menu_ext.'">';

			$menu_url2=''; $res_fams=''; $class_active='';

			if($menu_url != '')
			{
				$menu_url2=$menu_url;
			}


			$res_menus=show_sub_menu($menu_id, $responsive); // SUBMENUS DOS MENUS
			if($res_menus) $submenus .= $res_menus;
			if($res_menus) $submenus_mobile .= $res_menus;

			if($catalogo_id > 0 && $menu_tipo == 'familias')
			{
				$sum_fams=show_menu_subfamilias($catalogo_id, 0, $rec_fams, $reparacoes);

				$class='';
				if(in_array($catalogo_id, $rec_fams)) $class='class="t active"'; else $class='class="t"';

				$p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);

				if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';

				 if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $submenus_mobile .= $sum_fams['return'];
			}



			if($res_menus) $menu_url2='#';

			// MENU NIVEL 0
			$class='';
			$class=devolve_class($menu_tipo, $submenus, $a, $class_active); // CARACTERISTICAS DO MENU

			 if($menu_tipo == 'user_data' && $dados_cliente['Nome']=='') $return .= '<li '.$class.' id="m_'.$menu_id.'" data_active="'.$class_active.'" data_id="'.$menu_id.'" data_menu="menutop'.$id_pai.'" style="margin-right:0">';
			 else $return .= '<li '.$class.' id="m_'.$menu_id.'" data_active="'.$class_active.'" data_id="'.$menu_id.'" data_menu="menutop'.$id_pai.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'data-onclick="showMenuTab('.$menu_id.', \'menutop'.$id_pai.'\');return false;"' : '').'>';


			// URL E NOME MENU
			if($menu_tipo == 'sales') $return .= '<a href="'.$menu_url2.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'onclick="showMenuTab('.$menu_id.', \'menutop'.$id_pai.'\');return false;"' : '').'><span>'.$menu_nome.'</span></a>';
			elseif($menu_tipo == 'login') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'" onclick="toggleLoginBox();return false;"><i class="fa fa-user" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			elseif($menu_tipo == 'minha_conta') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-user" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			elseif($menu_tipo == 'favoritos') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-star" aria-hidden="true" style="font-size:23px"></i> <span>'.$menu_nome.('<span id="wishlist-count">'.$GLOBALS['total_wishlist'].'</span>').'</span></a>';
			elseif($menu_tipo == 'linguas') $return .= '<div class="wrapper-linguas" onclick="$(\'.dropdown\').show();return false;">'.show_linguas_rep().'</div>';
			// elseif($menu_tipo == 'user_data' && $dados_cliente['Nome']!='') $return .= '<a href="'.$menu_url2.'" style="text-transform:lowercase">'.mb_strimwidth($dados_cliente['Nome'].' '.$dados_cliente['Apelido'], 0, 8, "...").'</a>';	// $return .= '<a href="#" style="text-transform:uppercase" onclick="window.open(\''.$GLOBALS['site_http_url'].$dados_cliente['Nickname'].'\',\'_self\');return false;">'.mb_strimwidth($dados_cliente['Nome'].' '.$dados_cliente['Apelido'], 0, 8, "...").'</a>';
			elseif($menu_tipo == 'user_data' && $dados_cliente['Nome']!='') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i style="font-size:19px" class="fa fa-user2">&nbsp;</i></a>';
			// elseif($menu_tipo == 'favoritos') $return .= '<a href="#">'.$menu_nome.(isset($total_wishlist) ? '('.$total_wishlist.')' : '').'</a>';
			elseif($menu_tipo == 'minas_encomendas') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-shopping-basket" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			elseif($menu_tipo == 'carrinho') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa  fa-shopping-cart" style="font-size:19px">&nbsp;</i></a>';
			elseif($menu_tipo == 'logout') $return .= '<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-sign-out" style="font-size:19px" aria-hidden="true"></i> <span>'.$menu_nome.'</span></a>';
			// elseif($menu_tipo == 'search') $return .= '<a href="#" onclick="$(\'#pesquisa_topo\').submit();return false;">'.$menu_nome.'</a>';
			else $return .= '<a href="'.$menu_url2.'">'.$menu_nome.'</a>';

			if($menu_tipo == 'favoritos') $return .= '<div class="total-fav">0</div>';
			elseif($menu_tipo == 'carrinho') $return .= '<div class="total-cart">0</div><div class="total-cart-res" style="display:none"></div>';
			elseif($menu_tipo == 'search')
			{
				if(isset($_GET['pesquisa'])) $pesq=($_GET['pesquisa']);
				$return .= '<div class="total-search"><script>function validarPesquisa(){if($.trim($("#pesquisa").val())!="") return true;else{Apprise("'.getLinguas('key_pesquisa_vazia').'");return false;}}</script><form name="pesquisa" id="pesquisa_topo" action="'.seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa()"><input value="'.htmlentities($pesq).'" name="pesquisa" id="pesquisa" value="" type="text" /></form></div>';
			}
			elseif($menu_tipo == 'login' || $menu_tipo == 'user_data')
			{
				// $return .= show_menu_login();
				// $return .= showWisthListProds();
				// $return .= showCartListProds();
			}

			if($submenus != '')
			{
				if($a==0) $return .= '<div class="cbp-hrsub active" id="sm_'.$menu_id.'">'.$submenus.'</div>';
				else $return .= '<div class="cbp-hrsub" id="sm_'.$menu_id.'">'.$submenus.'</div>';
				$a++;
			}

			$return .= '</li>';

			$return_resp .= '<li>';
			$return_resp .= '<a href="'.$menu_url2.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'onclick="return false;"' : '').'>'.$img.$menu_nome.'</a>';
			if($submenus_mobile != '')
			{
				$return_resp .= '<ul>';
				$return_resp .= $submenus_mobile;
				$return_resp .= '</ul>';
			}
			$return_resp .= '</li>';


			$rs->db_next();
		}

		$return .= '</ul><div class="c"></div></div>';
		$return_resp .= '</ul><div class="c"></div></nav></div>';

	}else
	{
		$return .= '<div class="top_menu">';
		$return .= '<div class="clear"></div>';
		$return .= '<ul class="menu-desk" id="menutop'.$id_pai.'">';

		$return_resp .= '<div class="drop3">';
		$return_resp .= '<div class="m_menu" onclick="BlurryMenu.openMenu()"><i class="fa fa-bars" aria-hidden="true"></i></div>';

		$return_resp .= '<div class="m_respons"><a href="homepage"><img src="layout/'.PROJETO.'/logos/logo_top_resp.png" alt="" border="0" /></a></div>';

		$return_resp .= '<div class="m_menu2">';
		$return_resp .= '<div class="m_social"><a href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></div>';

		$return_resp .= '<div class="m_social" ><a href="'.seo('pages.php?page_id='.$GLOBALS['wishlist_page_id']).'"><i class="fa fa-star-o" aria-hidden="true"></i></a></div>';
		$return_resp .= '<div class="m_social" onclick="BlurryMenu.openMenu(1)"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '</div>';

		$return_resp .= '<div id="menu">';


		$return_resp .= '<nav id="powermenu1" class="powerlistmenu"><ul>';
		if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li class="join-m"><a href="'.seo('pages.php?page_id=146').'">sell</a></li>';
		else $return_resp .= '<li class="join-m"><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1">join</a></li>';

		// $return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').'><i style="font-size:24px;overflow: hidden;white-space: nowrap; text-indent: 100%;" class="fa fa-user ">'.(isset($dados_cliente) && is_array($dados_cliente) ? $dados_cliente['Nome'].' '.$dados_cliente['Apelido'] : getLinguas('key_login')).'</i></a><ul><li style="position:relative">'.showFormLogin_MOBILE().'</li></ul></li>';
		// $return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').'><i class="fa fa-user ">'.(isset($dados_cliente) && is_array($dados_cliente) ? $dados_cliente['Nome'].' '.$dados_cliente['Apelido'] : getLinguas('key_login')).'</i></a><ul><li style="position:relative">';
		$return_resp .= '<li class="login-m"><a href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' : '').' style="font-size:19px !important"><i class="fa fa-user "></i></a><ul><li style="position:relative">';

		ob_start();
		$return2='';
		show_menu_login3(1);
		$return2=ob_get_contents(); //Grab output
		$return_resp .= $return2;
		ob_end_clean(); //Discard output buffer

		$return_resp .= '</li></ul></li>';

		$return_resp .= '<div class="clear"></div>';

		$return_resp .= '<li class="pesqm"><div class="line">';
		$return_resp .= '<script>function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {	Apprise("O campo de pesquisa encontra-se vazio.");	return false;}}</script>';
		$return_resp .= '<form name="pesquisa2" id="searchbox2" action="'.seo('pages.php?page_id=' . $GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa_mobile()">';
		$return_resp .= '<a onclick="return false;" class="text-m"><input type="text" value="ssss" id="pesquisa-mobile" name="pesquisa" /></a>';
		$return_resp .= '<a onclick="return false;" class="pesquisa-m" style="font-size:24px"> <i class="fa fa-search" aria-hidden="true" onclick="$(\'#searchbox2\').submit();"></i></form>';


		$return_resp .= '<div class="clear"></div></div></li>';
		$return_resp .= '<div class="clear"></div>';


		$sum_fams=show_menu_subfamilias(0, 0, $rec_fams, $reparacoes);



		$class='';
		// if(in_array($catalogo_id, $rec_fams)) $class='class="t active"'; else $class='class="t"';

		// $p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);

		// if(empty($sum_fams['return']) || $reparacoes == 1) $p_seo2=$p_seo; else $p_seo2='#';

		if(!empty($sum_fams['return'])) $submenus_mobile .= $sum_fams['return'];
		//$return_resp .= '<li>';
		if($submenus_mobile != '')
		{
			//$return_resp .= '<ul>';
			$return_resp .= $submenus_mobile;
			//$return_resp .= '</ul>';
		}
		//$return_resp .= '</li>';

		$return .= '<div class="c"></div></div>';
		$return_resp .= '</ul><div class="c"></div></nav></div>';



	}
	$rs->db_close(1);


	if($responsive == 1) echo $return_resp;
	else echo $return;
}



function show_sub_menu($id_pai=0, $responsive=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';$return_resp='';
	$sql='
			SELECT
				m.ID, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.img_ext,
				ml.URL_Nome2
			FROM
				menus_links m
				INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID
			WHERE
				m.ID_ORG="'.$id_pai.'"
				AND m.Visivel=1
				AND ml.Lang="'.$GLOBALS['site_lang'].'"
			ORDER BY
				m.Ordem ASC
			';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{

		// $return .= '<div class="cbp-hrsub"><div class="cbp-hrsub-inner"><ul>';
		// $return_resp .= '<ul>';
		$a=0;
		while(!$rs->EOF())
		{
			$menu_id=$rs->db_data['ID'];
			$menu_ext=$rs->db_data['img_ext'];
			$catalogo_id=$rs->db_data['catalogo_id'];
			$catalogo_tipo=$rs->db_data['tipo_catalogo'];
			$menu_tipo=$rs->db_data['TipoMenu'];
			$menu_nome=$rs->db_data['URL_Nome2'];
			$menu_url=seo($rs->db_data['URL_Link']);

			$img='';

			if(file_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='files/menus/'.$menu_id.'_1.'.$menu_ext;
			elseif($catalogo_id > 0 && $catalogo_tipo == 'familias')
			{
				$category_img=get_product_category($catalogo_id);
				if($category_img!='' && file_exists($category_img)) $img=$category_img;
			}
			elseif($catalogo_id > 0 && $catalogo_tipo == 'marcas')
			{
				$brand_info=get_produtc_brand($catalogo_id);
				if(isset($brand_info['marca_img']) && file_exists($brand_info['marca_img'])) $img=$brand_info['marca_img'];
			}

			$menu_url2='';

			if($menu_url != '')
			{
				$menu_url2=$menu_url;
			}
			elseif($menu_tipo=='familias' && $catalogo_id == 0)
			{
				$menu_url2=seo('pages.php?page_id='.$GLOBALS['categorias_page_id']);
			}
			elseif($menu_tipo=='marcas' && $catalogo_id == 0)
			{
				$menu_url2=seo('pages.php?page_id='.$GLOBALS['colecoes_page_id']);
			}
			elseif($catalogo_id > 0 && $catalogo_tipo == 'familias')
			{
				$menu_url2=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
			}
			elseif($catalogo_id > 0 && $catalogo_tipo == 'marcas')
			{
				$brand_name=buscacabecalho_marca($catalogo_id, $GLOBALS['site_lang']);
				$menu_url2='m-'.$catalogo_id.'-'. str_replace(' ', '', limpa_special($brand_name)).'-'.$GLOBALS['site_lang'];
			}

			if($menu_nome != '')
			{
				// $class='';
				// $class=devolve_class($menu_tipo, '', $a); // CARACTERISTICAS DO MENU

				if($menu_tipo == 'sales') $return .= '<a class="titulo" href="'.$menu_url2.'"><div class="menulink"><span>'.$menu_nome.'</span></div></a><div class="space">&nbsp;</div>';
				else $return .= '<a class="titulo" href="'.$menu_url2.'"><div class="menulink">'.$menu_nome.'</div></a><div class="space">&nbsp;</div>';

				$return_resp .= '<li>';
				$return_resp .= '<a class="titulo" href="'.$menu_url2.'">'.$menu_nome.'</a>';
				// $return_resp .= '<div class="space">&nbsp;</div>';
				$return_resp .= '</li>';

				$a++;
			}
			$rs->db_next();
		}

	}
	$rs->db_close(1);

	if($responsive == 1) return $return_resp;
	else return $return;
}

function returnSplit($str, $lines, $delimiter, $array=FALSE)
{
	$strlength=strlen($str);
	$wrappoint=floor($strlength / ($lines));
	$newstring=array_chunk(explode($delimiter, wordwrap($str, $wrappoint, $delimiter)), $lines);
	if (isset($newstring[1]))
	{
		for ($i=1; $i <= count($newstring); $i++)
		{
			$newstring[0][$lines - 1] .= ' ' . implode(' ', $newstring[$i]);
			unset($newstring[$i]);
		}
	}

	return ($array) ? $newstring[0] : implode($delimiter, $newstring[0]);
}
function IS_FAM_ACTIVE($family_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$result=0;
	$pos=0;
	$sql='SELECT ID FROM products_families WHERE visivel=1 AND ID='.$family_id;
	$rs->db_open($db_link,$DB_name, $sql);
	if(!$rs->EOF()) $result=1;
	else $result=0;
	$rs->db_close();

	return $result;
}

function show_menu_($id_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';

	$dados=cliente_logado();
	if($dados && $dados['Grupo'] > 0) $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Visivel=1 ORDER BY Ordem ASC';
	else $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Logado=0 AND Visivel=1 ORDER BY Ordem ASC';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$uri=$_SERVER['REQUEST_URI'];
		$uri=str_replace($site_http_url,'',$uri);
		$return .= '<div class="menu-container">';
		$return .= '<div class="menu_top"><ul>';
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
							$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
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
				}
				elseif($TipoMenu=='marcas')
				{
					if(IS_FAM_ACTIVE($catalogo_id))
					{
						if($URL_Link!='')
						{
							$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
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
					}
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
						$return.='<div class="cbp-hrsub"><div class="cbp-hrsub-inner">';
						$return.='<div style="float:left">';
						$return.='<h4>'.$nomeb.'</h4>';
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
						$return.='</ul><div style="clear:both"></div>';
						$return.='</div>';
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
		$return.='</ul><div class="c"></div></div></div>';
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
			$return.='</ul><div class="c"></div></div></div>';
		}
	}
	$rs->db_close();
	echo $return;
}


function show_menu_tipo($id_pai, $tipo=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$TipoBanner='0';

	$ntable='menus_links';
	$ntable2='menus_links_lang';
	$sql='SELECT * FROM ' . $ntable . ' WHERE ID_ORG="' . $id_pai . '" AND Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	// $return .= '<div class="menu_lateral" id="menu_lateral'.$tipo.'">';

	$condicao='WHERE ID_PAI="' . $id_pai . '" AND lang="' . $site_lang . '"';
	$sql="SELECT * FROM " . $ntable2 . " $condicao";
	$rs2->db_open($db_link, $DB_name, $sql);
	if (!$rs2->EOF())
	{
		$nomeb=$rs2->db_data["URL_Nome2"];
		$return .= '<div class="titulo_d_menu">' . $nomeb . '</div>';
	}

	$return .= '<div class="drop"><ul>';
	if (!$rs->EOF())
	{
		$i=0;
		$uri=$_SERVER["REQUEST_URI"];
		$uri=str_replace($site_http_url, "", $uri);


		$style='';
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data["ID"];
			$Largura=$rs->db_data["Largura"];
			$catalogo_id=0;
			$catalogo_id=$rs->db_data["catalogo_id"];
			if ($catalogo_id > 0)
			{
				$p_seo=str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $catalogo_id, $catalogo_id, 1));
			}
			else
			{
				$p_seo='#';
			}

			$TipoMenu=$rs->db_data["TipoMenu"];
			$templates_id=explode(';', $rs->db_data["template_id"]);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$condicao='WHERE ID_PAI="' . $IDb . '" AND lang="' . $site_lang . '"';
			$sql="SELECT * FROM " . $ntable2 . " $condicao";
			$rs2->db_open($db_link, $DB_name, $sql);
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data["URL_Nome2"];

				if ($TipoMenu == 'familias')
				{
					$class='';
					if (isset($_GET['cat']) && $_GET['cat'] > 0 || defined('is_home') || (isset($_GET['prod_id']) && $_GET['prod_id'] > 0))
					{
						$class='active';
					}
					/*
					 * $sub_menu=show_menu_fams($catalogo_id);
				if(!empty($sub_menu['menus']))
				{
					$return .= '<ul>'.$sub_menu['menus'].'<div class="c"></div></ul>';
				}
					 * */
					$res=show_menu_fams(0, $TipoBanner, $nomeb);
					if ($res)
					{
						$return .= '<li class="menu_tipo' . $tipo . ' ' . $class . '"><h3><a href="#" onclick="return false;">' . $nomeb . '</a></h3>';
						$return .= '<ul ' . $style . '>' . $res['menus'] . '</ul><div class="c"></div>';
					}
					else
					{
						$return .= '<li class="menu_tipo' . $tipo . ' ' . $class . '"><h3><a href="' . $p_seo . '">' . $nomeb . '</a></h3>';
					}

					$return .= '</li>';
					// $return .= '<div class="menu_tipo'.$tipo.'"><a href="'.$p_seo.'" onclick="SwitchMenuH('.$IDb.', \'familias\');return false;" class="familias" data="'.$IDb.'">'.$nomeb.'</a></div>';
					// $res=show_menu_fams(0, $TipoBanner, $nomeb);
					// if($res) $return .= '<div class="submenu" id="sub'.$IDb.'">'.$res.'<div class="c"></div></div>';
				}
				elseif ($TipoMenu == 'marcas')
				{
					$class='';
					if (isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
					{
						$class='active';
					}

					$res=show_menu_brands(0, $TipoBanner, $nomeb);
					if ($res)
					{
						$return .= '<li class="menu_tipo' . $tipo . ' ' . $class . '"><h3><a href="#" onclick="return false;">' . $nomeb . '</a></h3>';
						$return .= '<ul ' . $style . '>' . $res . '</ul><div class="c"></div>';
					}
					else
					{
						$return .= '<li class="menu_tipo' . $tipo . ' ' . $class . '"><h3><a href="' . $p_seo . '">' . $nomeb . '</a></h3>';
					}

					$return .= '</li>';

					// $return .= '<div class="menu_tipo'.$tipo.'"><a href="'.$p_seo.'" onclick="SwitchMenuH('.$IDb.', \'marcas\');return false;" class="marcas" data="'.$IDb.'">'.$nomeb.'</a></div>';
					// $res=show_menu_brands(0, $TipoBanner, $nomeb);
					// if($res) $return .= '<div class="submenu" id="sub'.$IDb.'">'.$res.'<div class="c"></div></div>';

				}
				elseif ($TipoMenu == 'precos')
				{
					$return .= '<li class="menu_tipo' . $tipo . '"><h3><a href="' . $p_seo . '">' . $nomeb . '</a></h3></li>';


					// $return .= '<div class="menu_tipo'.$tipo.'">';
					// $return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';
					// $res=show_menu_shop_hoverintent($catalogo_id, $TipoBanner, $nomeb);
					// if($res) $return .= '<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
					// $return .= '</div>';
				}
				elseif ($catalogo_id > 0)
				{

					$return .= '<li class="menu_tipo' . $tipo . '"><h3><a href="' . $p_seo . '">' . $nomeb . '</a></h3></li>';

					// $return .= '<div class="menu_tipo'.$tipo.'">';
					// $return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';
					// $res=show_menu_shop_hoverintent($catalogo_id, $TipoBanner, $nomeb);
					// if($res) $return .= '<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
					// $return .= '</div>';
				}
				else
				{
					$sql="
							SELECT
								m.* , ml.URL_Nome2
							FROM
								" . $ntable . " m
								INNER JOIN " . $ntable2 . " ml ON ml.ID_PAI=m.ID
							WHERE
								ID_ORG='" . $IDb . "' AND Visivel=1 AND ml.lang='$site_lang'
							ORDER BY
								Ordem ASC
							";
					$rs3->db_open($db_link, $DB_name, $sql);
					if (!$rs3->EOF())
					{


						/* $return .= '<div class="menu_tipo'.$tipo.'">';
						$return .= '<a href="#">'.$nomeb.'</a>';
						$return .= '</div>';

						$return .= '<div style="float:left">';

						$return .= '<ul>';
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

							$return .= '<li><a href="'.$URL_Link.'">'.$rs3->db_data['URL_Nome2'].'</a></li>';

							$j++;

							$rs3->db_next();
						 }
						$return .= '</ul><div style="clear:both"></div>';
						$return .= '</div>';						 */
					}
					else
					{

						if ($URL_Link != '')
						{
							$return .= '<li class="menu_tipo' . $tipo . '"><h3><a href="' . $URL_Link . '">' . $nomeb . '</a></h3></li>';
						}
						else
						{
							$return .= '<li class="menu_tipo' . $tipo . '"><h3><a href="#" onclick="return false;">' . $nomeb . '</a></h3></li>';
						}


						// $return .= '<div class="menu_tipo'.$tipo.'">';
						// if($URL_Link!="") $return .='<a href="'.$URL_Link.'">'.$nomeb.'</a>';
						// else $return .= '<a href="#">'.$nomeb.'</a>';
						// $return .= '</div>';
					}
					$rs3->db_close();
				}
			}
			$rs2->db_close();
			$rs->db_next();
		}

	}
	else
	{
		$ntable='menus_links';
		$ntable2='menus_links_lang';
		$sql='SELECT * FROM ' . $ntable . ' WHERE ID="' . $id_pai . '"';
		$rs->db_open($db_link, $DB_name, $sql);
		if (!$rs->EOF())
		{
			$i=0;
			$uri=$_SERVER["REQUEST_URI"];
			$uri=str_replace($site_http_url, "", $uri);

			$style='';

			$i++;
			$IDb=$rs->db_data["ID"];
			$Largura=$rs->db_data["Largura"];
			$catalogop_id=0;
			$catalogop_id=$rs->db_data["catalogo_id"];

			$TipoMenu=$rs->db_data["TipoMenu"];
			$templates_id=explode(';', $rs->db_data["template_id"]);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			if ($TipoMenu == 'familias')
			{
				$sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=" . $catalogop_id . " and pfl.lang='$site_lang'";
			}
			else
			{
				$sql='';
			}

			// $return .= $sql;
			$rs2->db_open($db_link, $DB_name, $sql);
			while (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data["Cabecalho"];
				$catalogo_id=$rs2->db_data["ID"];
				$return .= '<div class="menu_tipo' . $tipo . '">';
				$return .= '<a href="' . $p_seo . '">' . $nomeb . '</a>';
				// $res=show_menu_shop_hoverintent($catalogo_id, $TipoBanner, $nomeb);
				// if($res) $return .= '<div class="cbp-hrsub"><div class="cbp-hrsub-inner">'.$res.'</div></div>';
				$return .= '</div>';

				$rs2->db_next();
			}
			$rs2->db_close();

		}
	}
	$rs->db_close();
	$return .= '</ul></div>';
	echo $return;
}

function show_menu_shop_hoverintent($ID_ORG=0,$tipobanner=0,$menu_nome='')
{
	// $cache_filename=$GLOBALS['site_shell_url'].'files/tmp/menus_'.$ID_ORG.'_'.$site_lang.'.txt';
	// $time_expire=time() - 3600;
	// $gerar='';
	// if(isset($_GET['gerar'])) $gerar=$_GET['gerar'];
	// if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire && !isset($_GET['site_country'])) $return=file_get_contents($cache_filename);
	// else
	// {
		$return=getSubMenuItensFams2($ID_ORG);

		// $fh=fopen($cache_filename,'w');
		// fwrite($fh,$return);
		// fclose($fh);
		// chmod($cache_filename,0755);
	// }
	return $return;
}
function getSubMenuItensFams2($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='
			SELECT
				pf.ID, pf.img_ext, pfl.Cabecalho
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND visivel=1
			ORDER BY
				pf.Ordem=0, pf.Ordem asc, pfl.Cabecalho asc';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$menus .= '<ul>';

		while(!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			$marca_name='';
			$marca_name_c=$rs->db_data['Cabecalho'];
			$I1=$rs->db_data['img_ext'];

			// $URL_Link='mr-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;
			$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);

			// $img_file='files/products_families/'.$IDb.'_1.'.$I1;
			// $img_file_new='files/products_families/'.$IDb.'_1_menu.'.$I1;
			// $img_file_new2='files/products_families/'.$IDb.'_1_menu_grey.'.$I1;

			// geraimagens($img_file, $img_file_new, 100, 50, array('ativo'=>1, 'w'=>105,'h'=>55), 0, 0, 0);
			// geraimagens($img_file, $img_file_new2, 100, 50, array('ativo'=>1, 'w'=>105,'h'=>55), 0, 0, 0);

			// if(file_exists($img_file_new) && $I1 != '') $marca_name='<img src="'.$img_file_new.'" class="ch_img" style="vertical-align:middle" title="" alt="" />';

			/* if(file_exists($img_file_new) && $I1 != '') $menus .= '<li><a href="'.$URL_Link.'">'.$marca_name.'</a><ul></ul></li>';
			else  */
			$menus .= '<li><a href="'.$URL_Link.'">'.$marca_name_c.'</a>';

			$menus .= getSubMenuItensFams2($IDb);
			$menus .= '</li>';

			$rs->db_next();
		}
		$menus .= '</ul>';
	}
	$rs->db_close();
	return $menus;
}
function getSubMenuItensbrands2($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='
			SELECT
				pf.ID, pf.img_ext, pfl.Nome
			FROM
				brands pf
				INNER JOIN brands_lang pfl ON pfl.ID_PAI=pf.ID
				INNER JOIN filters_products fp ON fp.marca_id=pf.ID
				INNER JOIN products p ON p.ID=fp.prod_id
			WHERE
				pf.Visivel=1
				AND p.Aproved=1
				AND pfl.lang="'.$site_lang.'"
				AND pfl.Nome <> ""
				AND
				(
					fp.fam_id1='.$ID_ORG.'
					OR fp.fam_id2='.$ID_ORG.'
					OR fp.fam_id3='.$ID_ORG.'
				)
			GROUP BY
				pf.ID asc
			ORDER BY
				pf.Ordem=0, pf.Ordem asc, pfl.Nome asc';

	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$menus .= '<ul>';

		while(!$rs->EOF())
		{
			$marca_id=$rs->db_data['ID'];
			$marca_name=$rs->db_data['Nome'];
			$I1=$rs->db_data['img_ext'];

			// $URL_Link='mr-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;
			$URL_Link='m-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;

			$img_file='files/brands/'.$marca_id.'_1.'.$I1;
			$img_file_new='files/brands/'.$marca_id.'_1_menu.'.$I1;
			// $img_file_new2='files/brands/'.$marca_id.'_1_menu_grey.'.$I1;

			// geraimagens($img_file, $img_file_new, 100, 50, 1, 0, 0, 0);
			geraimagens($img_file, $img_file_new, 100, 50, array('ativo'=>1, 'w'=>105,'h'=>55), 0, 0, 0);

			// geraimagens($img_file, $img_file_new2, 100, 50, 1, 0, 0, 1);

			if(file_exists($img_file_new)) $marca_name='<img src="'.$img_file_new.'" class="ch_img" style="vertical-align:middle" title="" alt="" />';

			$menus .= '<li><span class="helper"></span><a href="'.$URL_Link.'">'.$marca_name.'</a><ul></ul></li>';

			$rs->db_next();
		}
		$menus .= '</ul>';
	}
	$rs->db_close();
	return $menus;
}
function getCategoriesRec($id_org)
{
	$hConn=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$obj=new DataObj;
	$arvore2=array();
	$pai=$id_org;
	while($pai > 0)
	{
		$id_org=$pai;
		$sql="SELECT ID, ID_ORG FROM products_families WHERE ID=$pai";
		$obj->db_open($hConn,$GLOBALS['DB_name'],$sql);
		$pai=$obj->db_data["ID_ORG"];
		$arvore2[]=$obj->db_data["ID"];
	}

	if(empty($arvore2)) $arvore2[]=$id_org;

	$output="";
	$t=(count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t]=$arvore2[$a];
		$t--;
	}
	ksort($output);
	$obj->db_close();
	return $output;
}
function show_menu_fams($ID_ORG=0,$tipobanner=0,$menu_nome='')
{
	$menus=getSubMenuItens_fams($ID_ORG);
	return $menus;
}
function getSubMenuItens_fams($ID_ORG=0)
{
	$menus='';
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql='
			SELECT
				pf.*,pfl.Cabecalho,pfl.Cabecalho2
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.visivel=1
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND pf.ID_ORG="'.$ID_ORG.'"
			ORDER BY
				pf.Ordem ASC,
				pfl.Cabecalho asc';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$IDb=$rs->db_data['ID'];
		$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
		// $URL_Link=str_replace($site_http_url,'',$URL_Link);
		if($rs->db_data['Cabecalho2']) $nomeb=$rs->db_data['Cabecalho2']; else $nomeb=$rs->db_data['Cabecalho'];

		$submenus='';
		$submenu=getSubMenuItens_fams($IDb);
		if($submenu != '') $submenus='<ul>'.$submenu.'</ul>';

		if($submenu != '')
		{
			if(isset($_GET['cat']) && $_GET['cat'] == $IDb) $menus.='<li><a href="#" class="active"  style="border-bottom:#c0c0c0 1px solid" onclick="return false;">'.$nomeb.'</a>'.$submenus.'<div class="c"></div></li><div class="c"></div>';
			else $menus.='<li><a href="#" onclick="return false;" style="border-bottom:#c0c0c0 1px solid">'.$nomeb.'</a>'.$submenus.'<div class="c"></div></li><div class="c"></div>';
		}
		else
		{
			if(isset($_GET['cat']) && $_GET['cat'] == $IDb) $menus.='<li><a href="'.$URL_Link.'" class="active">'.$nomeb.'</a><div class="c"></div></li><div class="c"></div>';
			else $menus.='<li onclick="javascript:window.open(\''.$URL_Link.'\',\'_self\')"><a href="'.$URL_Link.'">'.$nomeb.'</a><div class="c"></div></li><div class="c"></div>';
		}

		$rs->db_next();
	}
	$rs->db_close();

	return $menus;
}
/*
function show_menu_fams($ID_ORG=0, $tipobanner=0, $menu_nome='')
{
	// global $site_lang;
	// $cache_filename=$GLOBALS['site_shell_url'].'files/tmp/fam_menus_'.$ID_ORG.'_'.$site_lang.'.txt';
	// $time_expire=time() - 3600;
	// $gerar='';

	$menus=getSubMenuItens($ID_ORG);

	return $menus;
}*/

function show_menu_brands($ID_ORG=0, $tipobanner=0, $menu_nome='')
{
	global $site_lang;
	// $cache_filename=$GLOBALS['site_shell_url'].'files/tmp/brands_'.$ID_ORG.'_'.$site_lang.'.txt';
	// $time_expire=time() - 3600;
	// $gerar='';
	// if(isset($_GET['gerar'])) $gerar=$_GET['gerar'];
	// if($gerar!=1 && file_exists($cache_filename) && filectime($cache_filename) > $time_expire && !isset($_GET['site_country'])) $menus=file_get_contents($cache_filename);
	// else
	// {
	$menus=getSubMenuItensbrands($ID_ORG);

	// $fh=fopen($cache_filename, 'w');
	// fwrite($fh, $menus);
	// fclose($fh);
	// chmod($cache_filename, 0755);
	// }

	return $menus;
}

function getSubMenuItens($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass, $DB_name, $site_http_url, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='
			SELECT
				pf.*,pfl.Cabecalho,pfl.Cabecalho2
			FROM
				products_families pf
				INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.visivel=1
				AND pfl.lang="' . $site_lang . '"
				AND pf.ID_ORG="' . $ID_ORG . '"
			ORDER BY
				pfl.Cabecalho ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;

	if (!$rs->EOF())
	{
		$menus .= '<ul>';

		while (!$rs->EOF())
		{
			$IDb=$rs->db_data["ID"];
			$URL_Link=seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $IDb, $IDb, 1);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			if ($rs->db_data["Cabecalho2"])
			{
				$nomeb=$rs->db_data["Cabecalho2"];
			}
			else
			{
				$nomeb=$rs->db_data["Cabecalho"];
			}

			if (isset($_GET['cat']) && $_GET['cat'] == $IDb)
			{
				$menus .= '<li class="active"><a href="' . $URL_Link . '" class="active" style="color: #fe5e1e !important;">' . $nomeb . '</a><div class="c"></div></li><div class="c"></div>';
			}
			elseif (isset($_GET['cat']) && $_GET['cat'] > 0)
			{
				$menus .= '<li class="hidden2" onclick="javascript:window.open(\'' . $URL_Link . '\',\'_self\')"><a href="' . $URL_Link . '">' . $nomeb . '</a><div class="c"></div></li><div class="c"></div>';
			}
			else
			{
				$menus .= '<li onclick="javascript:window.open(\'' . $URL_Link . '\',\'_self\')"><a href="' . $URL_Link . '">' . $nomeb . '</a><div class="c"></div></li><div class="c"></div>';
			}

			$i++;

			$rs->db_next();
		}
		$rs->db_close();

		if (isset($_GET['cat']) && $_GET['cat'] > 0)
		{
			// $menus .= '<li class="expand"><img src="layout/' . PROJETO . '/images/exp.png" style="cursor:pointer" /><div class="c"></div></li><div class="c"></div>';
		}
		$menus .= '</ul>';
	}

	return $menus;
}

function getSubMenuItensbrands($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass, $DB_name, $site_http_url, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='
			SELECT
				pf.*,pfl.Nome
			FROM
				brands pf
				INNER JOIN brands_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.Visivel=1
				AND pfl.lang="' . $site_lang . '"
				AND pfl.Nome <> ""
			ORDER BY
				pfl.Nome ASC';
	// echo
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	while (!$rs->EOF())
	{
		$IDb=$rs->db_data["ID"];
		$nomeb=$rs->db_data["Nome"];
		$URL_Link='m-' . $IDb . '-' . limpa_special($nomeb) . '-' . $site_lang; //seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);

		// if(isset($_GET['s_marca']) && $_GET['s_marca']==$IDb)$menus .= '<div class="sub2 active"><a class="active" href="'.$URL_Link.'">'.$nomeb.'</a><div class="c"></div></div><div class="c"></div>';
		// else $menus .= '<div class="sub2"><a href="'.$URL_Link.'">'.$nomeb.'</a><div class="c"></div></div><div class="c"></div>';


		if (isset($_GET['s_marca']) && $_GET['s_marca'] == $IDb)
		{
			$menus .= '<li class="active"><a href="' . $URL_Link . '" class="active" style="color: #fe5e1e !important;">' . $nomeb . '</a><div class="c"></div></li>';
		}
		elseif (isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
		{
			$menus .= '<li class="hidden2"><a href="' . $URL_Link . '" class="active">' . $nomeb . '</a></li>';
		}
		else
		{
			$menus .= '<li onclick="javascript:window.open(\'' . $URL_Link . '\',\'_self\')"><a href="' . $URL_Link . '">' . $nomeb . '</a></li>';
		}


		$i++;

		$rs->db_next();
	}
	$rs->db_close();


	if (isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
	{
		$menus .= '<li class="expand"><img src="layout/' . PROJETO . '/images/exp.png" style="cursor:pointer" /><div class="c"></div></li><div class="c"></div>';
	}

	return $menus;
}

function montaSubMenu($menus)
{
	$blocos=5;
	$blocos2=1;
	$num_lines=10;
	$cc=1;
	$return='';
	$return .= '<div class="colm">';

	for ($i=0; $i < count($menus); $i++)
	{
		$cat_nome=$menus[$i][0];
		$cat_url=$menus[$i][1];
		$cat_type=$menus[$i][2];
		$cat_filters=$menus[$i]['FILTERS'];

		if ($cc == $num_lines || $cc == ($num_lines - 1) || $cc == ($num_lines - 2))
		{
			$cc=1;
			$return .= '</div>';
			if ($blocos == $blocos2)
			{
				$return .= '<div class="c" style="clear:both;width:100%;padding:0;margin:0;height:1px"></div>';
				$blocos2=0;
			}
			$return .= '<div class="colm">';
			$return .= '<span class="sub2"><a href="' . $cat_url . '"><b>' . $cat_nome . '</b></a></span>';
			$blocos2++;

		}
		else
		{
			$return .= '<span class="sub2"><a href="' . $cat_url . '"><b>' . $cat_nome . '</b></a></span>';
		}
		$cc++;

		for ($j=0; $j < count($cat_filters); $j++)
		{
			$filter_nome=$cat_filters[$j][0];
			$filter_url=$cat_filters[$j][1];
			$filter_type=$cat_filters[$j][2];
			$filter_values=$cat_filters[$j]['VALUES'];

			if ($cc == $num_lines)
			{
				$cc=2;
				$return .= '</div>';
				if ($blocos == $blocos2)
				{
					$return .= '<div class="c" style="clear:both;width:100%;padding:0;margin:0;height:1px"></div>';
					$blocos2=0;
				}
				$return .= '<div class="colm">';
				$return .= '<span class="sub2"><a href="' . $cat_url . '"><b>' . $cat_nome . ' (Cont.)</b></a></span>';
				$return .= '<span class="sub3">' . $filter_nome . '</span>';
				$blocos2++;
			}
			else
			{
				if ($cc == ($num_lines - 1))
				{
					$cc=2;
					$return .= '</div>';
					if ($blocos == $blocos2)
					{
						$return .= '<div class="c" style="clear:both;width:100%;padding:0;margin:0;height:1px"></div>';
						$blocos2=0;
					}
					$return .= '<div class="colm">';
					$return .= '<span class="sub2"><a href="' . $cat_url . '"><b>' . $cat_nome . ' (Cont.)</b></a></span>';
					$return .= '<span class="sub3">' . $filter_nome . '</span>';
					$blocos2++;
				}
				else
				{
					$return .= '<span class="sub3">' . $filter_nome . '</span>';
				}

			}
			$cc++;

			for ($k=0; $k < count($filter_values); $k++)
			{
				$value_nome=$filter_values[$k][0];
				$value_url=$filter_values[$k][1];
				$value_type=$filter_values[$k][2];
				if ($cc == $num_lines)
				{
					$cc=3;
					$return .= '</div>';
					if ($blocos == $blocos2)
					{
						$return .= '<div class="c" style="clear:both;width:100%;padding:0;margin:0;height:1px"></div>';
						$blocos2=0;
					}
					$return .= '<div class="colm">';
					$return .= '<span class="sub2"><a href="' . $cat_url . '"><b>' . $cat_nome . ' (Cont.)</b></a></span>';
					$return .= '<span class="sub3">' . $filter_nome . ' (Cont.)</span>';
					$return .= '<div class="sub4"><a href="' . $value_url . '"> - ' . $value_nome . '</a></div>';
					$blocos2++;
				}
				else
				{
					$return .= '<div class="sub4"><a href="' . $value_url . '"> - ' . $value_nome . '</a></div>';
				}
				$cc++;
			}
		}
	}
	$return .= '</div>';

	return $return;
}

/*
function show_menu_shop_hoverintent($ID_ORG=0, $tipobanner=0, $menu_nome='')
{
	$return='';
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$template;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;$rs2=new Dataobj;
    $sql="Select pf.*,pfl.Cabecalho,pfl.Cabecalho2 from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.visivel=1 and pfl.lang='$site_lang' and pf.ID_ORG='".$ID_ORG."' order by pfl.Cabecalho  asc";
    $rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	$j=0;
	$uri=$_SERVER["REQUEST_URI"];
	$uri=str_replace($site_http_url, "",$uri);
	$right=0;
	$zindex=4;
	if(!$rs->EOF())
	{
		$return .= '<div style="float:left">';
		$return .=  '<h4>'.$menu_nome.'</h4>';
		$return .=  '<ul>';
		while(!$rs->EOF())
		{
			if(productfiltersfams($rs->db_data["ID"]))
			{
				$i++;
				$IDb=$rs->db_data["ID"];
				$arr_f[]=$IDb;
				$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
				$URL_Link=str_replace($site_http_url,'',$URL_Link);
				if($rs->db_data["Cabecalho2"]) $nomeb=$rs->db_data["Cabecalho2"];
				else $nomeb=$rs->db_data["Cabecalho"];
				$return .= '<li><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
				$j++;
			}
			$rs->db_next();
		}
		$return .= '</ul><div style="clear:both"></div>';
		$return .= '</div>';
	}
    $rs->db_close();

	// echo '<pre style="color:#FFF">';
	// print_r($arr_f);
	// echo '</pre>';

	$rand_keys=array_rand($arr_f);

	// echo '<pre style="color:#FFF">';
	// print_r($rand_keys);
	// echo '</pre>';
		// echo '<pre style="color:#FFF">';
	// print_r($arr_f[$rand_keys]);
	// echo '</pre>';
	if(count($rand_keys) > 0)
	{

		echo products_objs_fam($arr_f[$rand_keys]);
		echo $return;
	}
	else
	{
		echo $return;
	}
} */
function show_menu_marcas_hoverintent($ID_ORG=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT * FROM brands WHERE Visivel=1 ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	$uri=$_SERVER["REQUEST_URI"];
	$uri=str_replace($site_http_url, "", $uri);
	$right=0;
	$zindex=4;
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data["ID"];
			$condicao="where ID_PAI=$IDb and Lang='$site_lang'";
			$rs2->db_open($db_link, $DB_name, "Select * from brands_lang $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data["Nome"];
				$URL_Link=$site_http_url . 'marca-' . $IDb . '-' . str_replace(' ', '', limpa_special($nomeb));
				$URL_Link=str_replace($site_http_url, '', $URL_Link);

				if ($i == 1)
				{
					$wd='width:400px';
					$b=2;
				}
				else
				{
					$wd='';
					$b='';
				}
				$style='style="background:#4b4b50; text-transform:uppercase !important"';
				echo '<div class="top"><div class="sub"><a href="' . $URL_Link . '" class="top p">' . $nomeb . '</a></div></div>';
			}
			$rs2->db_close();
			$rs->db_next();
		}
	}
	$rs->db_close();
}

function menu_nivel($id_org=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$pai=$id_org;
	$nivel=-1;
	while ($pai > 0)
	{
		$rs->db_open($db_link, $DB_name, 'SELECT ID_ORG FROM products_families WHERE ID="' . $pai . '"');
		$pai=$rs->db_data["ID_ORG"];
		$nivel++;
	}

	return array('nivel' => $nivel, 'id_org' => $id_org);
}

function get_pages_childs($id_org)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql="SELECT * FROM pages WHERE ID_ORG='" . $id_org . "' AND ID_ORG > 0";
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$id[]=$rs->db_data['ID'];
		$rs->db_next();
	}
	$rs->db_close();

	return $id;
}

function show_menu_others($id_pai, $org=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$ntable='menus_links';
	$ntable2='menus_links_lang';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	if ($org)
	{
		$sql="SELECT * FROM " . $ntable . " WHERE ID_ORG='" . $id_pai . "' AND Visivel=1 ORDER BY Ordem ASC";
	}
	else
	{
		$sql="SELECT * FROM " . $ntable . " WHERE ID='" . $id_pai . "' AND Visivel=1 ORDER BY Ordem ASC";
	}
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		$i=0;
		$uri=$_SERVER['REQUEST_URI'];
		$uri=str_replace($site_http_url, '', $uri);
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$Largura=$rs->db_data['Largura'];
			$templates_id=explode(';', $rs->db_data['template_id']);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$condicao="where ID_PAI=$IDb and lang='$site_lang'";
			$rs2->db_open($db_link, $DB_name, "Select * from " . $ntable2 . " $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['URL_Nome2'];
				$ul=str_replace('pages.php?page_id=', '', $rs->db_data['URL_Link']);
				$filhos=get_pages_childs($ul);
				if (substr_count($URL_Link, $_GET['page_id']) || in_array($_GET['page_id'], $filhos))
				{
					echo '<li class="master"><a href="#" style="background-image:url(' . $GLOBALS['site_http_url'] . 'layout/' . PROJETO . '/images/bg-menu2.jpg)"><b>' . $nomeb . '</b></a><ul style="display: block;">';
					echo show_menu_others2($IDb);
					echo '</ul></li>';
				}
				else
				{
					if ($URL_Link)
					{
						echo '<li class="master"><a href="#"><b>' . $nomeb . '</b></a><ul>';
						show_menu_others2($IDb);
						echo '</ul></li>';
					}
					else
					{
						echo '<li class="master"><a href="#"><b>' . $nomeb . '</b></a><ul>';
						show_menu_others2($IDb);
						echo '</ul></li>';
					}
				}
			}
			$rs2->db_close();
			$rs->db_next();
		}
	}
	$rs->db_close();
}

function show_menu_others2($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT * FROM " . $ntable . " WHERE ID_ORG='" . $id_pai . "' AND Visivel=1 ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		$i=0;
		$uri=$_SERVER['REQUEST_URI'];
		$uri=str_replace($site_http_url, '', $uri);
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$Largura=$rs->db_data['Largura'];
			$templates_id=explode(';', $rs->db_data['template_id']);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$condicao="where ID_PAI=$IDb and lang='$site_lang'";
			$rs2->db_open($db_link, $DB_name, "Select * from " . $ntable2 . " $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['URL_Nome2'];
				if (substr_count($URL_Link, $_GET['page_id']))
				{
					echo '<li class="active"><a href="' . $URL_Link . '"><span>' . $nomeb . '</span></a></li>';
				}
				else
				{
					if ($URL_Link)
					{
						echo '<li><a href="' . $URL_Link . '">' . $nomeb . '</a></li>';
					}
					else
					{
						echo '<li><a href="' . $URL_Link . '">' . $nomeb . '</a></li>';
					}
				}
			}
			$rs2->db_close();
			$rs->db_next();
		}
	}
	$rs->db_close();
}

function show_menu_shop($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT * FROM products_families WHERE visivel=1 AND ID_ORG=0 ORDER BY Ordem ASC";
	$hRes=$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	$uri=$_SERVER['REQUEST_URI'];
	$uri=str_replace($site_http_url, '', $uri);
	$right=0;
	$zindex=4;
	if (isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$prod_info=get_produtc_info($_GET['prod_id']);
		$fam_info=get_family_info($prod_info['fam_id']);
	}
	echo '<div id="menu-page"><ul>';
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$URL_Link=seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $IDb, $IDb, 1);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$Largura=$rs->db_data['Largura'];
			$templates_id=explode(';', $rs->db_data['template_id']);
			$condicao="where ID_PAI=$IDb and lang='$site_lang'";
			$rs2->db_open($db_link, $DB_name, "Select * from products_families_lang $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['Cabecalho'];
				if ($i == 4)
				{
					$wd=';width:254px';
					$b=2;
				}
				else
				{
					$wd='';
					$b='';
				}
				$id_pai=get_pai($_GET['cat']);
				if (($_GET['cat'] == $IDb && $_GET['cat'] > 0) || $id_pai == $IDb || $fam_info['fam_parent'] == $IDb)
				{
					echo '<li class="master" id="' . $IDb . '"><a href="#" style="background-image:url(' . $GLOBALS['site_http_url'] . 'layout/' . PROJETO . '/images/bg-menu2.jpg)"><b>' . $nomeb . '</b></a></li><ul id="catalogo_' . $IDb . '" style="display: block;">';
					echo show_menu2($IDb);
					echo '</ul>';
				}
				else
				{
					echo '<li class="master" id="' . $IDb . '"><a href="#"><b>' . $nomeb . '</b></a></li><ul id="catalogo_' . $IDb . '">';
					echo show_menu2($IDb);
					echo '</ul>';
				}
			}
			$rs2->db_close();
			$right += 118;
			$zindex--;
			$rs->db_next();
		}
	}
	$rs->db_close();
	show_menu_others(141);
	show_menu_others(143);
	echo '</ul></div>';
}

//menu 2
function show_menu2($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='" . $id_pai . "' ORDER BY Ordem ASC");
	if ($rs->EOF())
	{
		$rs->db_close();
		$id_pai=get_pai($id_pai);
		$rs=new Dataobj;
		$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='" . $id_pai . "' ORDER BY Ordem ASC");
	}
	$i=0;
	$uri=$_SERVER['REQUEST_URI'];
	$uri=str_replace($site_http_url, '', $uri);
	if (isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
	{
		$prod_info=get_produtc_info($_GET['prod_id']);
	}
	while (!$rs->EOF())
	{
		$i++;
		$IDb=$rs->db_data['ID'];
		$URL_Link=seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $IDb, $IDb, 1);
		$URL_Link=str_replace($site_http_url, '', $URL_Link);

		$Largura=$rs->db_data['Largura'];
		$templates_id=explode(';', $rs->db_data['template_id']);
		$rs2->db_open($db_link, $DB_name, "Select * from products_families_lang where ID_PAI=$IDb and lang='$site_lang'");
		if (!$rs2->EOF())
		{
			$nomeb=$rs2->db_data['Cabecalho'];
			$id_child=get_child($_GET['cat']);
			if (($_GET['cat'] == $IDb && $_GET['cat'] > 0) || $IDb == $id_child || $IDb == $prod_info['fam_id'])
			{
				$return .= '<li class="active"><a href="' . $URL_Link . '">' . $nomeb . '</a></li>';
			}
			else
			{
				$return .= '<li><a href="' . $URL_Link . '">' . $nomeb . '</a></li>';
			}
		}
		$rs2->db_close();
		$rs->db_next();
	}
	$rs->db_close();

	return $return;
}

function get_pai($id_org)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID='" . $id_org . "' ORDER BY Ordem ASC");
	if (!$rs->EOF())
	{
		$id_org=$rs->db_data['ID_ORG'];
	}
	$rs->db_close();

	return $id_org;
}

function get_child($id_org)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='" . $id_org . "' ORDER BY Ordem ASC LIMIT 1");
	if (!$rs->EOF())
	{
		$id_org=$rs->db_data['ID'];
	}
	$rs->db_close();

	return $id_org;
}

function show_menu3($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select " . $ntable . ".ID, " . $ntable . ".URL_Link, " . $ntable2 . ".URL_Nome2 from " . $ntable . " inner join " . $ntable2 . " on " . $ntable2 . ".ID_PAI=" . $ntable . ".ID where ID_ORG='" . $id_pai . "' and Visivel=1 order by Ordem asc";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=3;
	$uri=$_SERVER['REQUEST_URI'];
	$uri=str_replace($site_http_url, '', $uri);
	if (!$rs->EOF())
	{
		$total=$rs->db_rows;
		$a=0;
		echo '<div class="acessos-div"><div class="menus2">';
		while (!$rs->EOF())
		{
			$a++;
			$IDb=$rs->db_data['ID'];
			$URL_Link=$rs->db_data['URL_Link'];
			$URL_Nome=explode('|', $rs->db_data['URL_Nome2']);
			$id_page=str_replace("pages.php?page_id=", '', $URL_Link);
			if ($i == $i2)
			{
				echo '</div><div class="menus2">';
				$i2=0;
			}
			if ($URL_Nome[0] != '')
			{
				?>
				<div class="menus"><b><?= $URL_Nome[0]; ?></b>
				<div class="line">&nbsp;</div><? if ($URL_Link)
			{
				show_content($id_page);
			}
			else
			{
				show_menu3_sub($IDb);
			} ?></div><?
			}
			$i2++;
			$rs->db_next();
		}
		echo '</div></div>';
	}
	$rs->db_close();
}

function show_menu3_sub($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select " . $ntable . ".ID, " . $ntable . ".URL_Link, " . $ntable2 . ".URL_Nome2 from " . $ntable . " inner join " . $ntable2 . " on " . $ntable2 . ".ID_PAI=" . $ntable . ".ID where ID_ORG='" . $id_pai . "' and Visivel=1 order by Ordem Asc";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	$uri=$_SERVER['REQUEST_URI'];
	$uri=str_replace($site_http_url, '', $uri);
	if (!$rs->EOF())
	{
		$total=$rs->db_rows;
		$a=0;
		while (!$rs->EOF())
		{
			$a++;
			$IDb=$rs->db_data['ID'];
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$URL_Nome=explode('|', $rs->db_data['URL_Nome2']);
			?>
		<div style="width:250px;<?= $style; ?>"><a href="<?= $URL_Link; ?>"
		                                           class="footer_grey"><?= $URL_Nome[0]; ?></a></div><?
			$rs->db_next();
		}
	}
	$rs->db_close();
}

function show_follow_us($id=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo, $site_shell_url;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select " . $ntable . ".ID," . $ntable . ".img_ext, " . $ntable . ".URL_Link, " . $ntable2 . ".URL_Nome2 from " . $ntable . " inner join " . $ntable2 . " on " . $ntable2 . ".ID_PAI=" . $ntable . ".ID where ID_ORG='" . $id . "' and Visivel=1 and  " . $ntable2 . ".Lang='" . $site_lang . "' order by Ordem DESC";
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{

		while (!$rs->EOF())
		{
			$IDb=$rs->db_data['ID'];
			$img_ext=$rs->db_data['img_ext'];
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$URL_Nome=$rs->db_data['URL_Nome2'];
			if ($img_ext && file_exists($site_shell_url . 'files/menus/' . $IDb . '_1.' . $img_ext))
			{
				?>
				<div class="socials-networks"><a href="<?= $URL_Link; ?>"><img
						src="<?= 'files/menus/' . $IDb . '_1.' . $img_ext ?>" title="<?= $URL_Nome ?>" alt=""/></a>
				</div><? }
			else
			{
				?>
				<div class="socials-networks"><a href="<?= $URL_Link; ?>"><?= $URL_Nome ?></a></div><? }
			$rs->db_next();
		}
		echo '<div class="titlefooter">Segue-nos</div>';
	}
	$rs->db_close();
}

function show_footer()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo, $site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="SELECT menus_links.ID,menus_links.img_ext,menus_links.novajanela, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG='168' AND Visivel=1 AND  menus_links_lang.Lang='" . $site_lang . "' ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	if (!$rs->EOF())
	{
		$sql="SELECT menus_links.ID,menus_links.novajanela,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID='168' AND Visivel=1 AND  menus_links_lang.Lang='" . $site_lang . "' ORDER BY Ordem ASC";
		$rs2->db_open($db_link, $DB_name, $sql);
		$URL_Nome=$rs2->db_data['URL_Nome2'];
		$rs2->db_close();
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


			?><a href="<?= $URL_Link; ?>" title="<?= $URL_Nome; ?>" <?= $open ?> <?= $Index ?>><img
				alt="<?= $URL_Nome; ?>" src="<?= $site_http_url ?>files/menus/<?= $IDb ?>_1.<?= $img_ext ?>">
			</a>&nbsp;&nbsp;<?
			$rs->db_next();
		}
	}
	$rs->db_close();
}

function show_footer2()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo, $site_shell_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="SELECT menus_links.ID,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG='138' AND Visivel=1 AND  menus_links_lang.Lang='" . $site_lang . "' ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	if (!$rs->EOF())
	{
		$sql="SELECT menus_links.ID,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID='138' AND Visivel=1 AND  menus_links_lang.Lang='" . $site_lang . "' ORDER BY Ordem ASC";
		$rs2->db_open($db_link, $DB_name, $sql);
		$URL_Nome=$rs2->db_data['URL_Nome2'];
		$rs2->db_close();
		echo '<div id="info-pagamento"><div class="titulo-footer">' . $URL_Nome . ':</div>';
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$img_ext=$rs->db_data['img_ext'];
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Nome=$rs->db_data['URL_Nome2'];
			?>
			<div id="icon-trf-bancaria"
			     style="background:url(<?= $site_http_url ?>files/menus/<?= $IDb ?>_1.<?= $img_ext ?>) no-repeat"><?= $URL_Nome; ?></div><?
			$rs->db_next();
		}
		echo show_media(33);
		echo '</div>';
	}
	$rs->db_close();
}

function showLogo()
{
	global $key_home_description, $site_lang;
	if (substr_count($_SERVER["PHP_SELF"], "index.php"))
	{
		echo '<img src="layout/' . PROJETO . '/logos/logo.png" alt="">';
	}
	else
	{
		echo '<a href="index.php" title="' . $key_home_description[$site_lang] . '"><img src="layout/' . PROJETO . '/logos/logo.png" alt=""></a>';
	}
}

//LINGUAS MENU
function linguas_show()
{
	$pageURL=get_uri();
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT * FROM linguas WHERE Visivel='1' ORDER BY Ordem ASC";
	$hRes=$rs->db_open($db_link, $DB_name, $sql);
	$t=0;
	while (!$rs->EOF())
	{
		$siglas[]=$rs->db_data['Sigla'];
		$nomes[]=$rs->db_data['Cabecalho'];
		$rs->db_next();
		$t++;
	}
	$rs->db_close();
	$c=0;
	while ($c < $t)
	{
		$Sigla=$siglas[$c];
		$pageURL=str_replace("?site_lingua=" . $Sigla, "", $pageURL);
		$pageURL=str_replace("&site_lingua=" . $Sigla, "", $pageURL);
		$c++;
	}
	if (substr_count($pageURL, "?") > 0)
	{
		$op="&";
	}
	else
	{
		$op="?";
	}
	if ($t > 1)
	{
		$c=0;
		echo '<select  onchange="window.open($(this).val(),\'_self\')">';
		while ($c < $t)
		{
			$Sigla=$siglas[$c];
			$NomeLingua=$nomes[$c];
			if ($site_lang == $Sigla)
			{
				echo '<option value="' . $pageURL . $op . 'site_lingua=' . $Sigla . '" selected="selected">' . $NomeLingua . '</option>';
			}
			else
			{
				echo '<option value="' . $pageURL . $op . 'site_lingua=' . $Sigla . '">' . $NomeLingua . '</option>';
			}
			$c++;
		}
		echo '</select>';
	}
	// else
	// {
	// 	$Sigla 			= $siglas[0];
	// 	$NomeLingua 	= $nomes[0];
	// 	if($site_lang==$Sigla)echo $NomeLingua;
	// }
}

function show_menu_simples($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang, $site_http_url;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT ml.*, mll.URL_Nome2 FROM " . $ntable . " ml INNER JOIN " . $ntable2 . " mll ON (mll.ID_PAI=ml.ID) WHERE ml.ID='" . $id_pai . "' AND Visivel=1 AND mll.Lang='" . $site_lang . "' ORDER BY ml.Ordem, mll.URL_Nome2 ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		$nome_categoria=$rs->db_data['URL_Nome2'];
	}
	$rs->db_close();
	$str_menu='';
	$sql="SELECT ml.*, mll.URL_Nome2 FROM " . $ntable . " ml INNER JOIN " . $ntable2 . " mll ON (mll.ID_PAI=ml.ID) WHERE ID_ORG='" . $id_pai . "' AND Visivel=1 AND mll.Lang='" . $site_lang . "' ORDER BY ml.Ordem, mll.URL_Nome2 ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	echo '<div class="colMenu"><ul>';

	while (!$rs->EOF())
	{
		echo '<li><a href="' . str_replace($site_http_url, '', seo($rs->db_data['URL_Link'])) . '">' . ($rs->db_data['URL_Nome2']) . '</a></li>';
		$i++;
		$rs->db_next();
	}
	$rs->db_close();
	echo '</ul></div>';
}

/* Menu que mostra um lista de categorias */
$total_array=array();
$nivel=0;
function niveis_fams($id_org=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$pai=$id_org;
	while ($pai > 0)
	{
		$sql='SELECT ID_ORG FROM products_families WHERE ID="' . $pai . '"';
		$rs->db_open($db_link, $DB_name, $sql);
		$pai=$rs->db_data['ID_ORG'];
		$nivel[]=$pai;
	}

	return $nivel;
}

function menu_categorias() // aqui
{
	global $total_array;
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$str_menu='';
	$sql='SELECT pf.ID, pf.ID_ORG, pfl.Cabecalho, (SELECT count(id) FROM products p WHERE p.ID_ORG=pf.ID) AS Total
	FROM products_families pf
	INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI)
	WHERE pf.visivel=1 AND pf.Download=1 AND pf.ID_ORG=0
	ORDER BY pf.Ordem, pfl.Cabecalho ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		if (isset($_GET['prod_id']) && $_GET['prod_id'] > 0)
		{
			$prod_info=get_produtc_info($_GET['prod_id']);
			$niveis=niveis_fams($prod_info['fam_id']);
		}
		else
		{
			$niveis=niveis_fams($_GET['cat']);
		}
		echo '<div id="category" class="ui-widget_box">
		<div id="categoryHeading" class="ui-widget-header_sidebox">Categorias</div>
		<div id="categoryContent" class="ui-widget-content_sidebox">
		<ul>';
		while (!$rs->EOF())
		{
			$id=$rs->db_data['ID'];
			$id_org=$rs->db_data['ID_ORG'];
			$cabecalho=$rs->db_data['Cabecalho'];
			$total=$rs->db_data['Total'];
			$str_sub=array();
			$str_sub=menu_sub_categorias($id, $niveis);
			$total2=0;
			$total2 += $total + $str_sub['total'];
			echo '<li>';
			echo '<a ' . $class . ' href="' . seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $id, $id, 1) . '">' . htmlentities_memo(stripslashes($cabecalho)) . '</a>&nbsp;(' . $total2 . ')';
			if ((isset($_GET['cat']) && $_GET['cat'] == $id) || in_array($id, $niveis))
			{
				echo $str_sub['return'];
			}
			echo '</li>';
			$rs->db_next();
		}
		echo '</ul></div></div>';
	}
	$rs->db_close();
}

function menu_sub_categorias($id_org, $niveis) // aqui
{
	global $total_array;
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$str_menu='';
	$sql='SELECT pf.ID, pf.ID_ORG, pfl.Cabecalho, (SELECT count(id) FROM products p WHERE p.ID_ORG=pf.ID) AS Total
	FROM products_families pf
	INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI)
	WHERE pf.visivel=1 AND pf.Download=1 AND pf.ID_ORG="' . $id_org . '"
	ORDER BY pf.Ordem, pfl.Cabecalho ASC';
	$tree=array();
	$rs->db_open($db_link, $DB_name, $sql);
	$return='';
	if (!$rs->EOF())
	{
		$total3=0;
		$return .= '<ul>';
		while (!$rs->EOF())
		{
			$id=$rs->db_data['ID'];
			$id_org=$rs->db_data['ID_ORG'];
			$cabecalho=$rs->db_data['Cabecalho'];
			$total=$rs->db_data['Total'];
			$str_sub=array();
			$str_sub=menu_sub_categorias($id, $niveis);
			$total2=0;
			$total2 += $total + $str_sub['total'];
			$total3 += $total + $str_sub['total'];
			$return .= '<li>';
			$return .= '<a ' . $class . ' href="' . seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $id, $id, 1) . '">' . htmlentities_memo(stripslashes($cabecalho)) . '</a>&nbsp;(' . $total2 . ')';
			if ((isset($_GET['cat']) && $_GET['cat'] == $id) || in_array($id, $niveis))
			{
				$return .= $str_sub['return'];
			}
			$return .= '</li>';
			$rs->db_next();
		}
		$return .= '</ul>';
	}
	$rs->db_close();

	return array('return' => $return, 'total' => $total3);
}

function menu_categorias_recur($tree, $visible)
{
	global $total_array, $nivel;
	if (count($tree) > 0)
	{
		if ($visible)
		{
			$str='<ul style="display:block">';
		}
		else
		{
			$str='<ul class="none">';
		}
		$nivel++;
		foreach ($tree as $item)
		{
			if ($_GET['cat'] == $item['ID'])
			{
				$class='class="category_selected"';
				$visible=TRUE;
			}
			else
			{
				$class='';
				$visible=FALSE;
			}
			if (count($item['submenu']) > 0)
			{
				$str1=menu_categorias_recur($item['submenu'], $visible);
				$str .= '<li><a ' . $class . ' href="' . seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $item['ID'], $item['ID'], 1) . '">' . htmlentities_memo(stripslashes($item['Cabecalho'])) . '</a>';
				$total_array[]=$item['Total'];
				$str .= '&nbsp;(' . array_sum($total_array) . ')';
				$str .= $str1;
			}
			else
			{
				$str .= '<li><a ' . $class . ' href="' . seo('pages.php?page_id=' . $GLOBALS['catalog_page_id'] . '&cat=' . $item['ID'], $item['ID'], 1) . '">' . htmlentities_memo(stripslashes($item['Cabecalho'])) . '</a>';
				$str .= '&nbsp;(' . $item['Total'] . ')';
				$total_array[]=$item['Total'];
			}
			$str .= '</li>';
		}
		$str .= '</ul>';
	}

	return $str;
}

function formatTree($tree, $parent)
{
	$tree2=array();
	foreach ($tree as $i => $item)
	{
		if ($item['ID_ORG'] == $parent)
		{
			$tree2[$item['ID']]=$item;
			$tree2[$item['ID']]['submenu']=formatTree($tree, $item['ID']);
		}
	}

	return $tree2;
}

function show_marcas()
{
	global $total_array;
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql='SELECT bl.ID_PAI, bl.Nome
	FROM brands b
	INNER JOIN brands_lang bl ON (bl.ID_PAI=b.ID)
	WHERE b.Visivel=1 AND bl.Lang="' . $site_lang . '"';
	$rs->db_open($db_link, $DB_name, $sql);
	echo '<select name="s_marca" id="s_marca" style="width:100%">';
	echo '<option value="">' . $GLOBALS['key_selecione'][$site_lang] . '</option>';
	while (!$rs->EOF())
	{
		if (isset($_GET['s_marca']) && $_GET['s_marca'] == $rs->db_data['ID_PAI'])
		{
			$selected='selected';
		}
		else
		{
			$selected='';
		}
		echo '<option ' . $selected . ' value="' . $rs->db_data['ID_PAI'] . '">' . $rs->db_data['Nome'] . '</option>';
		$rs->db_next();
	}
	$rs->db_close();
	echo '</select>';
}