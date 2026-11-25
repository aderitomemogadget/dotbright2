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
function putlogo()
{
	global $site_http_url;
	echo '<p style="text-align:center">';
	echo '<img src="'.$site_http_url.'/layout/logo_lb.png">';
	echo '</p>';
}
function show_menu_slider($id_pai=0, $class='')
{
	$dados_cliente=cliente_logado();
	$ID_ORG=0;
	if(isset($_GET['cat']) && intval($_GET['cat']) > 0)
	{
		$ID_ORG= intval($_GET['cat']);
	}
	$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_slider_'.(dotbright_shared_files_images() ? dotbright_product_images_cache_key() : '').$ID_ORG.'_'.$GLOBALS['site_lang'].'.txt';
	$time_expire=time() - 60*60; // Expire Time (1 hora)
	$gerar='';
	if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
	// echo $cache_filename;
	if($gerar!=1 && stream_resolve_include_path($cache_filename) && filectime($cache_filename) > $time_expire) $return=file_get_contents($cache_filename);
	else
	{
		$rec_fams=array();
		if($ID_ORG > 0) $rec_fams=getCategoriesRec($ID_ORG);
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
					'.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' :' AND (Logado=1 OR Logado=0)').'
				ORDER BY
					m.Ordem ASC
				';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
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
					if($catalogo_id==85)$menu_url2=seo('pages.php?page_id='.$GLOBALS['reparacoes_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
					else $menu_url2=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
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
				if(dotbright_image_resolve('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img src="files/menus/'.$menu_id.'_1.'.$menu_ext.'" alt="'.$menu_nome.'" />';

				// MENU NIVEL 0
				$class='';
				if($catalogo_id > 0 && $menu_tipo == 'familias' && in_array($catalogo_id, $rec_fams)) $class='class="active2"';
				elseif($catalogo_id > 0 && $menu_tipo == 'marcas' && isset($_GET['s_marca']) && intval($_GET['s_marca']) == $catalogo_id) $class='class="active2"';

				$return .= '<li '.$class.'>';
				//$return .= '<a href="'.$menu_url2.'" alt="'.$menu_nome.'" class="effect-shine"><h3><div class="img">'.$img.'</div><br><div class="txt">'.$menu_nome.'</div></h3></a>';
				$return .= '<a href="'.$menu_url2.'" alt="'.$menu_nome.'" class="effect-shine"><h3><div class="img">'.$img.'</div><div class="slider_line_blue"></div><div class="txt">'.$menu_nome.'</div></h3></a>';
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
	else $class=' class="cbp-hr"';
	return $class;
}
function get_subfamilias_menu($ID_ORG=0,$reparacoes=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$tema,$site_lang,$page_id,$seo,$site_shell_url;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $rs=new Dataobj;
    $rs2=new Dataobj;
    $familia = new Dataobj;
    $familia2 = new Dataobj;
	$return='';
    $sql='SELECT
				pf.ID,pfl.Cabecalho
			FROM
				products_families pf INNER JOIN products_families_lang pfl ON pfl.ID_PAI=pf.ID
			WHERE
				pf.ID_ORG='.$ID_ORG.'
				AND pfl.lang="'.$GLOBALS['site_lang'].'"
				AND visivel=1
			ORDER BY
				pf.Ordem ASC';


	if($ID_ORG==85)$reparacoes=1;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{
			$nomeb=$rs->db_data['Cabecalho'];
			$nomeb = trim(str_replace('Reparações','',$nomeb));
			$catalogo_id=$rs->db_data['ID'];
			$sum_fams=get_subfamilias_menu($catalogo_id,$reparacoes);
			
			if($reparacoes==1)$p_seo=seo('pages.php?page_id=148&cat='.$catalogo_id,$catalogo_id,1);
			else $p_seo=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id,$catalogo_id,1);


			if($sum_fams!='')
			{
				$return .= '<li class="has-submenu_new">';
				$return .= '<a href="#" class="toggle">'.$nomeb.'<span class="toggle-btn"></span></a>';
				$return .= '<ul class="megamenu_new 1" style="display:none">';
				$return .= '<li>';
				$return .= $sum_fams;
				$return .= '</li>';
				$return .= '</ul>';
				$return .= '</li>';
			}
			else $return .= '<li onclick="window.open(\''.$p_seo.'\',\'_self\')"><a href="'.$p_seo.'">'.$nomeb.'</a></li>';

			$rs->db_next();
		}
	}
	$rs->db_close();

	return $return;
}
function get_sub_menus($id_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';


	$sql='SELECT m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.menu_icon_fa, ml.texto_icon_fa, ml.URL_Nome2 FROM menus_links m INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID WHERE m.ID_ORG="'.$id_pai.'" AND m.Visivel=1 AND ml.Lang="'.$GLOBALS['site_lang'].'" '.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' :' AND (Logado=1 OR Logado=0)').' ORDER BY m.Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
	{
		$menu_id=$rs->db_data['ID'];
		$menu_ext=$rs->db_data['img_ext'];
		$catalogo_id=$rs->db_data['catalogo_id'];
		$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
		$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
		$menu_nome=$rs->db_data['URL_Nome2'];
		$menu_url=\seo($rs->db_data['URL_Link']);
		$img='';
		if(dotbright_image_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img class="svg" src="files/menus/'.$menu_id.'_1.'.$menu_ext.'">';
		$menu_url2='';
		$res_fams='';
		$class_active='';
		if($menu_url != '')$menu_url2=$menu_url;

		if($catalogo_id > 0 && $menu_tipo == 'familias')
		{
			$sum_fams=get_subfamilias_menu($catalogo_id);
			$p_seo=\seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
			if($sum_fams!='')
			{
				$return .= '<li class="has-submenu_new">';
				$return .= '<a href="#" class="toggle">'.$menu_nome.'<span class="toggle-btn"></span></a>';
				$return .= '<ul class="megamenu_new 2" style="display:none">';
				$return .= '<li>';
				$return .= $sum_fams;
				$return .= '</li>';
				$return .= '</ul>';
				$return .= '</li>';
			}
			else $return .= '<li onclick="window.open(\''.$p_seo.'\',\'_self\')"><a href="'.$p_seo.'">'.$menu_nome.'</a></li>';
		}
		else
		{
			$res_menus=get_sub_menus($menu_id); // SUBMENUS DOS MENUS
			if($res_menus!='')
			{
				$return .= '<li class="has-submenu_new">';
				$return .= '<a href="#" class="toggle">'.$menu_nome.'<span class="toggle-btn"></span></a>';
				$return .= '<ul class="megamenu_new 3" style="display:none">';
				$return .= '<li>';
				$return .= $res_menus;
				$return .= '</li>';
				$return .= '</ul>';
				$return .= '</li>';
			}
			else $return .= '<li onclick="window.open(\''.$menu_url.'\',\'_self\')"><a href="'.$menu_url.'">'.$menu_nome.'</a></li>';
		}

		$rs->db_next();
	}
	$rs->db_close();

	return $return;
}
function novo_menu_mobile()
{
	$dados_cliente=cliente_logado();
	if(!$dados_cliente) //Se nao estiver logado vamos fazer cache dos menus para tornar o site mais rapido
	{
		$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_topologado_'.(dotbright_shared_files_images() ? dotbright_product_images_cache_key() : '').$id_pai.'_'.$responsive.$GLOBALS['site_lang'].'.txt';
		$time_expire=time() - 60*60; // Expire Time (1 hora)
		$gerar='';
		if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
		if($gerar!=1 && is_file($cache_filename) && filectime($cache_filename) > $time_expire)
		{
			$return=file_get_contents($cache_filename);
			return $return;
		}
	}
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$return_resp='';

    //$whatsapp = media_social('Whatsapp',1);
    $whatsapp='https://wa.me/351914228888';

	
	$return_resp .= '<div id="search_mobile" class="search_mobile">
			<form name="pesquisa2" id="searchbox2" action="'.seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa_mobile()">
				<div style="width:100%;padding:5px;border-bottom:solid 1px #ccc">
					<input placeholder="Introduza o nome do artigo..." onkeyup="showMobileHints();" autocomplete="off" type="text" value="" id="pesquisa-mobile" name="pesquisa" style="font-size:16px;width:100%;left:40px;position:relative;height:30px;border:0" />
					<div style="position:fixed;top:0;left:0;z-index:99999;height:30px;line-height:30px;margin-left:10px;margin-top:8px"><a class="profile-toggle" onclick="hideMobile()"><i style="font-size:20px;color:#000" class="fa fa-arrow-left" aria-hidden="true"></i> </a></div>
				</div>
				<div id="search_mobile_hinters" class="search_mobile_hinters"></div>
			</form>
		</div>';

	$return_resp .= '<div class="drop3">';
		$return_resp .= '<div class="m_menu" onclick="Mobile_openMenu()"><i class="fa fa-bars" aria-hidden="true"></i></div>';

		$return_resp .= '<div class="m_respons"><a href="homepage"><img src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" /></a></div>';
		if($GLOBALS['linguas_topo_mobile']==1)
		{
			$return_resp .= '<div class="m_linguas">'.show_linguas('',1).'</div>';
		}
		$return_resp .= '<div class="m_menu2">';
		$return_resp .= '<div class="m_social"><a href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></div>';
        $return_resp .= '<div class="m_social"><a href="'.$whatsapp.'" target="_blank"><i class="fab fa-whatsapp" aria-hidden="true" style="width: 1.125em"></i></a></div>';
		//$return_resp .= '<div class="m_social" onclick="BlurryMenu.openMenu(1)"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '<div class="m_social" onclick="showMobile()"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '</div>';
		$return_resp .= '<div id="powermenu1_bg" style="display:none;width:100%;height:100vh;position:fixed;z-index:2;left:0;background-color:#000;opacity:1"></div>';
		$return_resp .= '<div id="menu" style="background-color:#2f2f2f">';

		$return_resp .= '
		<div id="profile">
			<div class="app-header">
				<div class="header">
					<div style="height: 40px; color: #000; width: 30%; text-align: left; display: table-cell; vertical-align:bottom">
						<img class="logo" style="margin:12px;max-width:60px" src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" />
					</div>
					<div style="height:50px;display:table-cell;vertical-align:middle;color:#000;text-align:right;padding-right:10px"><a class="profile-toggle" onclick="BlurryMenu.closeMenu()"><i style="font-size:35px" class="fas fa-times" aria-hidden="true"></i> </a></div>
				</div>
			</div>
			<div class="homepageContainer container">
				<div style="padding:10px;font-size:14px;float:left;width:100%">
					<div style="background:#2f2f2f;width:100%;padding:0px">

						<script>
							function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {Apprise("'.getLinguas('key_search_empty').'");return false;}}
							var timer = 0;

							function showMobile()
							{
								$(".app-header").hide();
								$(".homepageContainer").hide();
								$("#search_mobile").show();
								$("#search_mobile_hinters").show();
								$("#pesquisa-mobile").focus();
							}
							function hideMobile()
							{
								$("#search_mobile").hide();
								$("#search_mobile_hinters").hide();
								$(".app-header").show();
								$(".homepageContainer").show();
							}
							function showMobileHints()
							{
								if($("#pesquisa-mobile").val() != "")
								{									
									clearTimeout(timer);								    
								    timer = setTimeout(function () {
										var str = $("#pesquisa-mobile").val();
										$.ajax({type: "GET",url: SITE_HTTP_AJAX_URL+"livesearchm.php",data: { q: str }}).done(function(resultado){$("#search_mobile_hinters").html(resultado);});
								    }, 1000 || 0);
								}
							}
						</script>						
						<input placeholder="Introduza o nome do artigo..." onfocus="showMobile()" autocomplete="off" type="text" value="" style="height:30px;background-color:transparent;border:solid #424242 1px;color:#fff;width:200px" />
					</div>
				</div>
			</div>
			
			

			<div style="width:100%;height:20px;background:#2f2f2f"></div>
		</div>';
	

	$return_resp .= '<div class="mobile-menu-wrapper">
        <div class="mobile-menu-overlay"></div>
        <a href="#" onclick="Mobile_closeMenu()" class="mobile-menu-close"><i class="close-icon"></i></a>
        <div class="mobile-menu-container scrollable">
            <script>
                function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {   Apprise("O campo de pesquisa encontra-se vazio.");  return false;}}
            </script>
            <form id="searchbox2" style="z-index: 9999999;position: relative;" action="p4-pesquisa-pt" method="get" class="input-wrapper" onsubmit="return validarPesquisa_mobile()">
                <input type="text" class="form-control" name="pesquisa" autocomplete="off" placeholder="Introduza o nome do artigo..." required="" onfocus="showMobile()">

                <button onclick="$(\'#searchbox2\').submit();" class="btn btn-search" type="submit">
                    <i class="w-icon-search"></i>
                </button>
            </form>
            <div class="tab">
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a href="#main-menu" class="nav-link active">Menu</a>
                    </li>
                    <li class="nav-item">
                        <a href="#categories" class="nav-link">Categorias</a>
                    </li>
                </ul>
            </div>
            <div class="tab-content">
                <div class="tab-pane active" id="main-menu">
                    <ul class="mobile-menu">';

                    //Ler menu institucional mobile
                    $id_pai=316;
                    $sql='SELECT m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.menu_icon_fa, ml.texto_icon_fa, ml.URL_Nome2 FROM menus_links m INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID WHERE m.ID_ORG="'.$id_pai.'" AND m.Visivel=1 AND ml.Lang="'.$GLOBALS['site_lang'].'" '.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' :' AND (Logado=1 OR Logado=0)').' ORDER BY m.Ordem ASC';
					$rs->db_open($db_link,$DB_name,$sql);
                    while(!$rs->EOF())
					{
						$submenus='';
						$submenus_mobile='';
						$menu_id=$rs->db_data['ID'];
						$menu_ext=$rs->db_data['img_ext'];
						$catalogo_id=$rs->db_data['catalogo_id'];
						$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
						$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
						$menu_nome=$rs->db_data['URL_Nome2'];
						$menu_url=\seo($rs->db_data['URL_Link']);
						$img='';
						if(dotbright_image_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img class="svg" src="files/menus/'.$menu_id.'_1.'.$menu_ext.'">';
						$menu_url2='';
						$res_fams='';
						$class_active='';
						if($menu_url != '')$menu_url2=$menu_url;
						$return_resp .= '<li><a href="'.$menu_url.'">'.$menu_nome.'</a></li>';
						$rs->db_next();
					}
					$rs->db_close();

					//Agora os links para login

					if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><i class="fa fa-user" style="margin-right:10px"></i>MINHA CONTA</a></li>';
					else $return_resp .= '<li><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1"><i class="fa fa-user" style="margin-right:10px"></i>REGISTE-SE AQUI</a></li>';
					if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1"><i class="fa fa-user" style="margin-right:10px"></i>LOGOUT</a></li>';
					else $return_resp .= '<li><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><i class="fa fa-user" style="margin-right:10px"></i>LOGIN</a></li>';
		
					/*

	                    <li><a href="pages.php?page_id=24">SOBRE NÓS</a></li>
	                    <li><a href="pages.php?page_id=8">CONTACTE-NOS</a></li>
		                <li><a href="p5-registo-utilizador-pt" class="ml-0">Login</a></li>
		                <li><a href="p5-registo-utilizador-pt" class="ml-0">Registar</a></li>
		                */

		        $return_resp .= '
            		</ul>
            	</div>
                <div class="tab-pane" id="categories">
                    <ul class="mobile-menu">';


                    //Ler menu mobile de produtos
                    $id_pai=318;
                    $sql='SELECT m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.menu_icon_fa, ml.texto_icon_fa, ml.URL_Nome2 FROM menus_links m INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID WHERE m.ID_ORG="'.$id_pai.'" AND m.Visivel=1 AND ml.Lang="'.$GLOBALS['site_lang'].'" '.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' :' AND (Logado=1 OR Logado=0)').' ORDER BY m.Ordem ASC';
					$rs->db_open($db_link,$DB_name,$sql);
                    while(!$rs->EOF())
					{
						$menu_id=$rs->db_data['ID'];
						$menu_ext=$rs->db_data['img_ext'];
						$catalogo_id=$rs->db_data['catalogo_id'];
						$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
						$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
						$menu_nome=$rs->db_data['URL_Nome2'];
						$menu_url=\seo($rs->db_data['URL_Link']);
						$img='';
						if(dotbright_image_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img class="svg" src="files/menus/'.$menu_id.'_1.'.$menu_ext.'">';
						$menu_url2='';
						$res_fams='';
						$class_active='';
						if($menu_url != '')$menu_url2=$menu_url;

						if($catalogo_id > 0 && $menu_tipo == 'familias')
						{
							$sum_fams=get_subfamilias_menu($catalogo_id);
							$p_seo=\seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
							if($sum_fams!='')
							{
								$return_resp .= '<li class="has-submenu_new">';
								$return_resp .= '<a href="#" class="toggle">'.$menu_nome.'<span class="toggle-btn"></span></a>';
								$return_resp .= '<ul class="megamenu_new 4" style="display:none">';
								$return_resp .= '<li>';
								$return_resp .= $sum_fams;
								$return_resp .= '</li>';
								$return_resp .= '</ul>';
								$return_resp .= '</li>';
							}
							else $return_resp .= '<li onclick="window.open(\''.$p_seo.'\',\'_self\')"><a href="'.$p_seo.'">'.$menu_nome.'</a></li>';
						}
						else
						{
							$res_menus=get_sub_menus($menu_id); // SUBMENUS DOS MENUS
							if($res_menus!='')
							{
								$return_resp .= '<li class="has-submenu_new">';
								$return_resp .= '<a href="#" class="toggle">'.$menu_nome.'<span class="toggle-btn"></span></a>';
								$return_resp .= '<ul class="megamenu_new 5" style="display:none">';
								$return_resp .= '<li>';
								$return_resp .= $res_menus;
								$return_resp .= '</li>';
								$return_resp .= '</ul>';
								$return_resp .= '</li>';
							}
							else $return_resp .= '<li onclick="window.open(\''.$menu_url.'\',\'_self\')"><a href="'.$menu_url.'">'.$menu_nome.'</a></li>';
						}

						$rs->db_next();
					}
					$rs->db_close();
        			$return_resp .= '
                    </ul>
                </div>
            </div>
        </div>';
		return $return_resp;
}
function show_menu_topologado($id_pai=0,$responsive=0)
{
	if($_SESSION['ss_ismobile']==1 && $responsive==1)
	{
		//if($_GET['new']==1)return novo_menu_mobile();
		return novo_menu_mobile();
	}

	if($_SESSION['ss_ismobile']==1 && $responsive==0)return;
	if($_SESSION['ss_ismobile']==0 && $responsive==1)return;
	//------------------------------------------
	$dados_cliente=cliente_logado();
	if(!$dados_cliente) //Se nao estiver logado vamos fazer cache dos menus para tornar o site mais rapido
	{
		$cache_filename=SITE_SHELL_URL.'files/tmp/show_menu_topologado_'.(dotbright_shared_files_images() ? dotbright_product_images_cache_key() : '').$id_pai.'_'.$responsive.$GLOBALS['site_lang'].'.txt';
		$time_expire=time() - 60*60; // Expire Time (1 hora)
		$gerar='';
		if(isset($_GET['gerar'])) $gerar=intval($_GET['gerar']);
		if($gerar!=1 && is_file($cache_filename) && filectime($cache_filename) > $time_expire)
		{
			$return=file_get_contents($cache_filename);
			return $return;
		}
	}
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$return_resp='';

    //$whatsapp = media_social('Whatsapp',1);
    $whatsapp='https://wa.me/351914228888';

	$sql='SELECT m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.menu_icon_fa, ml.texto_icon_fa, ml.URL_Nome2 FROM menus_links m INNER JOIN	menus_links_lang ml ON ml.ID_PAI=m.ID WHERE m.ID_ORG="'.$id_pai.'" AND m.Visivel=1 AND ml.Lang="'.$GLOBALS['site_lang'].'" '.(isset($dados_cliente) && is_array($dados_cliente) ? ' AND (Logado=2 OR Logado=0)' :' AND (Logado=1 OR Logado=0)').' ORDER BY m.Ordem ASC';
	$rs->db_open($db_link,$DB_name,$sql);

	if(!$rs->EOF())
	{
		$return_resp .= '<div id="search_mobile" class="search_mobile">
				<form name="pesquisa2" id="searchbox2" action="'.seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa_mobile()">
					<div style="width:100%;padding:5px;border-bottom:solid 1px #ccc">
						<input placeholder="Introduza o nome do artigo..." onkeyup="showMobileHints();" autocomplete="off" type="text" value="" id="pesquisa-mobile" name="pesquisa" style="font-size:16px;width:100%;left:40px;position:relative;height:30px;border:0" />
						<div style="position:fixed;top:0;left:0;z-index:99999;height:30px;line-height:30px;margin-left:10px;margin-top:8px"><a class="profile-toggle" onclick="hideMobile()"><i style="font-size:20px;color:#000" class="fa fa-arrow-left" aria-hidden="true"></i> </a></div>
					</div>
					<div id="search_mobile_hinters" class="search_mobile_hinters"></div>
				</form>
			</div>';



		$return .= '<div class="top_menu top_menu2" id="menuuu">';
		$return .= '<div class="clear"></div>';
		$return .= '<ul class="menu-desk" id="menutop'.$id_pai.'">';

		$return_resp .= '<div class="drop3">';
		$return_resp .= '<div class="m_menu" onclick="BlurryMenu.openMenu()"><i class="fa fa-bars" aria-hidden="true"></i></div>';

		$return_resp .= '<div class="m_respons"><a href="homepage"><img src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" /></a></div>';
		if($GLOBALS['linguas_topo_mobile']==1)
		{
			$return_resp .= '<div class="m_linguas">'.show_linguas('',1).'</div>';
		}
		$return_resp .= '<div class="m_menu2">';
		$return_resp .= '<div class="m_social"><a href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></div>';
        $return_resp .= '<div class="m_social"><a href="'.$whatsapp.'" target="_blank"><i class="fab fa-whatsapp" aria-hidden="true" style="width: 1.125em"></i></a></div>';
		//$return_resp .= '<div class="m_social" onclick="BlurryMenu.openMenu(1)"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '<div class="m_social" onclick="showMobile()"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '</div>';
		$return_resp .= '<div id="powermenu1_bg" style="display:none;width:100%;height:100vh;position:fixed;z-index:2;left:0;background-color:#000;opacity:1"></div>';
		$return_resp .= '<div id="menu" style="background-color:#2f2f2f">';

		$return_resp .= '
		<div id="profile">
			<div class="app-header">
				<div class="header">
					<div style="height: 40px; color: #000; width: 30%; text-align: left; display: table-cell; vertical-align:bottom">
						<img class="logo" style="margin:12px;max-width:60px" src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" />
					</div>
					<div style="height:50px;display:table-cell;vertical-align:middle;color:#000;text-align:right;padding-right:10px"><a class="profile-toggle" onclick="BlurryMenu.closeMenu()"><i style="font-size:35px" class="fas fa-times" aria-hidden="true"></i> </a></div>
				</div>
			</div>
			<div class="homepageContainer container">
				<div style="padding:10px;font-size:14px;float:left;width:100%">
					<div style="background:#2f2f2f;width:100%;padding:0px">

						<script>
							function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {Apprise("'.getLinguas('key_search_empty').'");return false;}}
							var timer = 0;

							function showMobile()
							{
								$(".app-header").hide();
								$(".homepageContainer").hide();
								$("#search_mobile").show();
								$("#search_mobile_hinters").show();
								$("#pesquisa-mobile").focus();
							}
							function hideMobile()
							{
								$("#search_mobile").hide();
								$("#search_mobile_hinters").hide();
								$(".app-header").show();
								$(".homepageContainer").show();
							}
							function showMobileHints()
							{
								if($("#pesquisa-mobile").val() != "")
								{									
									clearTimeout(timer);								    
								    timer = setTimeout(function () {
										var str = $("#pesquisa-mobile").val();
										$.ajax({type: "GET",url: SITE_HTTP_AJAX_URL+"livesearchm.php",data: { q: str }}).done(function(resultado){$("#search_mobile_hinters").html(resultado);});
								    }, 1000 || 0);
								}
							}
						</script>						
						<input placeholder="Introduza o nome do artigo..." onfocus="showMobile()" autocomplete="off" type="text" value="" style="height:30px;background-color:transparent;border:solid #424242 1px;color:#fff;width:200px" />
					</div>
				</div>
			</div>
			
			

			<div style="width:100%;height:20px;background:#2f2f2f"></div>
		</div>
		<div id="lista-scroll" style="float: left;display:block;
					width: 100%;
					border-top-left-radius: 25px;
					border-top-right-radius: 25px;
					box-shadow: 1px -3px 3px 0px #888888;
					background-color: #fff;
					margin-top: -15px;
					padding: 10px;
					height: 100%">';

				/*

				<div id="lista-scroll" style="overflow: scroll;
			  overflow: scroll;
			  overflow-x: hidden;
			  float: left;
			  width: 100%;
			  border-top-left-radius: 25px;
			  border-top-right-radius: 25px;
			  box-shadow: 1px -3px 3px 0px #888888;
			  background-color: #fff;
			  margin-top: -15px;
			  padding: 10px;
			  height: 100%;">';
			*/



		$return_resp .= '<nav id="powermenu1" class="powerlistmenu 229" style="width:260px"><ul>';
		$return_resp .= '<li style="width:100%"><a href="/"  style="font-size:12px"><i class="fas fa-home" style="margin-right:10px"></i>Home</a></li>';
		
		if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><i class="fa fa-user" style="margin-right:10px"></i>Minha Conta</a></li>';
		else $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1"><i class="fa fa-user" style="margin-right:10px"></i>'.getLinguas('key_registeseaqui').'</a></li>';
		//$return_resp .= '<li style="width:100%"><a style="font-size:12px" href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' :'').'><i class="fas fa-sign-in-alt" style="margin-right:10px"></i>Login</a><ul><li style="position:relative">';
		if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1"><i class="fa fa-user" style="margin-right:10px"></i>Logout</a></li>';
		else $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><i class="fa fa-user" style="margin-right:10px"></i>Login</a></li>';
		
		/*
		ob_start();
		$return2='';
		//show_menu_login3(1);
		$return2=ob_get_contents(); //Grab output
		$return_resp .= $return2;
		ob_end_clean(); //Discard output buffer
		$return_resp .= '</li>';
		*/

		//$return_resp .= '</li></ul></li>';
		//$return_resp .= '<div class="clear"><br><b style="color:#000">Categorias de Produtos</b></div>';


		//Vou construir menus manualmente:
		//$return_resp .= '<li class="header header93"><a class="357" href="#" onclick="return false;"><img class="icon_menu" src="files/menus/222_1.svg" alt="SMARTPHONES">Mobile<span class="right fa fa-angle-right"></span></a><ul>'.getSubMenuItens(2983).'</ul></li>';
		//<h4 class="menu-title"><a href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat=2998','2998',1).'">Smartphones e Telemóveis</a></h4>

		//$return_resp .= '<li>123456789 123456789 123456789 1234</li>';

		$a=0;
		while(!$rs->EOF())
		{
			$submenus='';
			$submenus_mobile='';
			$menu_id=$rs->db_data['ID'];
			$menu_ext=$rs->db_data['img_ext'];
			$catalogo_id=$rs->db_data['catalogo_id'];
			$catalogo_tipo=$rs->db_data['tipo_catalogo']; // CAMPO 'Catálogo de Produtos' MARCAS ou CATEGORIAS
			$menu_tipo=$rs->db_data['TipoMenu']; // SELECT BOX BACKOFFICE 'Tipo Menu'
			$menu_nome=$rs->db_data['URL_Nome2'];
			$menu_url=\seo($rs->db_data['URL_Link']);
			$img='';
			if(dotbright_image_exists('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='<img class="svg" src="files/menus/'.$menu_id.'_1.'.$menu_ext.'">';
			$menu_url2='';
			$res_fams='';
			$class_active='';
			if($menu_url != '')$menu_url2=$menu_url;
			$res_menus=show_sub_menu($menu_id,$responsive); // SUBMENUS DOS MENUS
			if($res_menus) $submenus.=$res_menus;
			if($res_menus) $submenus_mobile.=$res_menus;
			if($catalogo_id > 0 && $menu_tipo == 'familias')
			{
				//if($catalogo_id==85)$reparacoes=1;
				$sum_fams=show_menu_subfamilias($catalogo_id,0,$rec_fams,$reparacoes);
				$class='';
				if(in_array($catalogo_id, $rec_fams))$class='class="t active"'; else $class='class="t"';
				$p_seo=\seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1);
				if(empty($sum_fams['return']) || $reparacoes == 1)
				{
					$submenus_mobile.=getSubMenuItens($catalogo_id);
					$p_seo2=$p_seo;
				}
				else $p_seo2='#';

				if(!empty($sum_fams['return']) && ($catalogo_id > 0 && $menu_tipo == 'familias')) $submenus_mobile.=$sum_fams['return'];

				//Vamos buscar filtros
			}
			if($res_menus) $menu_url2='#';
			// MENU NIVEL 0
			$class='';
			$class=devolve_class($menu_tipo, $submenus, $a, $class_active); // CARACTERISTICAS DO MENU
			if($menu_tipo == 'user_data' && $dados_cliente['Nome']=='') $return.='<li '.$class.' id="m_'.$menu_id.'" data_active="'.$class_active.'" data_id="'.$menu_id.'" data_menu="menutop'.$id_pai.'" style="margin-right:0">';
			else $return.='<li '.$class.' id="m_'.$menu_id.'" data_active="'.$class_active.'" data_id="'.$menu_id.'" data_menu="menutop'.$id_pai.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'data-onclick="showMenuTab('.$menu_id.', \'menutop'.$id_pai.'\');return false;"' :'').'>';

			// URL E NOME MENU
			if($menu_tipo == 'sales') $return.='<a href="'.$menu_url2.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'onclick="showMenuTab('.$menu_id.', \'menutop'.$id_pai.'\');return false;"' :'').'><span>'.$menu_nome.'</span></a>';
			elseif($menu_tipo == 'login')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'" onclick="toggleLoginBox();return false"><span>'.$img.'</span><span>'.$menu_nome.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'" onclick="toggleLoginBox();return false"><i class="fa fa-user" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			}
			elseif($menu_tipo == 'minha_conta')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span><span> '.$menu_nome.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-user" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			}
			elseif($menu_tipo == 'favoritos')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span><span>'.$menu_nome.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-star" aria-hidden="true" style="font-size:23px"></i> <span>'.$menu_nome.('<span id="wishlist-count">'.$GLOBALS['total_wishlist'].'</span>').'</span></a>';
			}
			elseif($menu_tipo == 'linguas') $return.='<div class="wrapper-linguas" onclick="$(\'.dropdown\').show();return false;">'.show_linguas_rep().'</div>';
			elseif($menu_tipo == 'user_data' && $dados_cliente['Nome']!='')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i style="font-size:19px" class="fa fa-user2">&nbsp;</i></a>';
			}
			elseif($menu_tipo == 'minas_encomendas')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa fa-shopping-basket" style="font-size:19px">&nbsp;</i><span>'.$menu_nome.'</span></a>';
			}
			elseif($menu_tipo == 'carrinho')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fa  fa-shopping-cart" style="font-size:19px">&nbsp;</i></a>';
			}
			elseif($menu_tipo == 'logout')
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span><span>'.$menu_nome.'</span></a>';
				else $return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><i class="fas fa-sign-out-alt" style="font-size:19px" aria-hidden="true"></i> <span>'.$menu_nome.'</span></a>';
			}
			else
			{
				if($img)$return.='<a href="'.$menu_url2.'" title="'.htmlentities($menu_nome).'"><span>'.$img.'</span></a>';
				else $return.='<a class="336" href="'.$menu_url2.'">'.$menu_nome.'</a>';
			}

			if($menu_tipo == 'favoritos') $return.='<div class="total-fav">0</div>';
			elseif($menu_tipo == 'carrinho') $return.='<div class="total-cart">0</div><div class="total-cart-res" style="display:none"></div>';
			elseif($menu_tipo == 'search')
			{
				if(isset($_GET['pesquisa'])) $pesq=($_GET['pesquisa']);
				$return.='<div class="total-search"><script>function validarPesquisa(){if($.trim($("#pesquisa").val())!="") return true;else{Apprise("'.getLinguas('key_pesquisa_vazia').'");return false;}}</script><form name="pesquisa" id="pesquisa_topo" action="'.\seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa()"><input value="'.htmlentities($pesq).'" name="pesquisa" id="pesquisa" value="" type="text" /><input type="submit"></form></div>';
			}

			if($submenus != '')
			{
				if($a==0) $return.='<div class="cbp-hrsub active" id="sm_'.$menu_id.'">'.$submenus.'</div>';
				else $return.='<div class="cbp-hrsub" id="sm_'.$menu_id.'">'.$submenus.'</div>';
				$a++;
			}

			$return.='</li>';

			$return_resp.='<li>';
			$return_resp.='<a class="357"  href="'.$menu_url2.'" '.(isset($menu_url2) && $menu_url2=='#' ? 'onclick="return false;"' :'').'>'.$img.$menu_nome.'</a>';
			if($submenus_mobile != '')
			{
				$return_resp.='<ul>';
				$return_resp.=$submenus_mobile;
				$return_resp.='</ul>';
			}
			$return_resp.='</li>';


			$rs->db_next();
		}
		$return .= '</ul><div class="c"></div></div>';
		$return_resp .= '</ul><div class="c"></div></nav></div>';
		$return_resp .= '</div>';

		$return_resp .= '<script>';
		$return_resp .= 'var isSafari = /^((?!chrome|android).)*safari/i.test(navigator.userAgent);';
		//$return_resp .= 'alert(isSafari)';

		$return_resp .= 'if(isSafari)$("#menu").height(window.innerHeight-30);';
		$return_resp .= 'else $("#menu").height(window.innerHeight);';

		$return_resp .= 'if(isSafari)$("#lista-scroll").height(window.innerHeight-130);';
		$return_resp .= 'else $("#lista-scroll").height(window.innerHeight-130);';
		
		//$return_resp .= '$(".drawer").height(window.innerHeight-130);console.log("a");';

		$return_resp .= '</script>';
	}
	else
	{
		$return_resp .= '<div id="search_mobile" class="search_mobile">
				<form name="pesquisa2" id="searchbox2" action="'.seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']).'" method="GET" onsubmit="return validarPesquisa_mobile()">
					<div style="width:100%;padding:5px;border-bottom:solid 1px #ccc">
						<input placeholder="Introduza o nome do artigo..."  onkeyup="showMobileHints();" autocomplete="off" type="text" value="" id="pesquisa-mobile" name="pesquisa" style="font-size:16px;width:100%;left:40px;position:relative;height:30px;border:0" />
						<div style="position:fixed;top:0;left:0;z-index:99999;height:30px;line-height:30px;margin-left:10px;margin-top:7px"><a class="profile-toggle" onclick="hideMobile()"><i style="font-size:20px;color:#000" class="fa fa-arrow-left" aria-hidden="true"></i> </a></div>
					</div>
					<div id="search_mobile_hinters" class="search_mobile_hinters"></div>
				</form>
			</div>';


		$return .= '<div class="top_menu top_menu2">';
		$return .= '<div class="clear"></div>';
		$return .= '<ul class="menu-desk" id="menutop'.$id_pai.'">';

		$return_resp .= '<div class="drop3">';
		$return_resp .= '<div class="m_menu" onclick="BlurryMenu.openMenu()"><i class="fa fa-bars" aria-hidden="true"></i></div>';
		$return_resp .= '<div class="m_respons"><a href="homepage"><img src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" /></a></div>';
		if($GLOBALS['linguas_topo_mobile']==1)
		{
			$return_resp .= '<div class="m_linguas">'.show_linguas('',1).'</div>';
		}
		$return_resp .= '<div class="m_menu2">';
		$return_resp .= '<div class="m_social"><a href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'"><i class="fa fa-shopping-cart" aria-hidden="true"></i></a></div>';
        $return_resp .= '<div class="m_social"><a href="'.$whatsapp.'" target="_blank"><i class="fab fa-whatsapp" aria-hidden="true" style="width: 1.125em;"></i></a></div>';
		//$return_resp .= '<div class="m_social" onclick="BlurryMenu.openMenu(1)"><i class="fa fa-search" aria-hidden="true"></i></div>';
		$return_resp .= '<div class="m_social" onclick="showMobile()"><i class="fa fa-search" aria-hidden="true"></i></div>';
		
		$return_resp .= '</div>';
		$return_resp .= '<div id="powermenu1_bg" style="display:none;width:100%;height:100vh;position:fixed;z-index:2;left:0;background-color:#000;opacity:0.9"></div>';
		$return_resp .= '<div id="menu" style="background-color:#eaeaea">';

		$return_resp .= '
		<div id="profile">
			<div class="app-header">
				<div class="header">
					<div style="height: 40px; color: #000; width: 30%; text-align: left; display: table-cell; vertical-align:bottom">
						<img class="logo" style="margin:12px;max-width:60px" src="'.(defined('LOGOTIPO_MOBILE') ? LOGOTIPO_MOBILE :'layout/'.PROJETO.'/logos/logo_top_resp.png').'" alt="" border="0" />
					</div>
					<div style="height:50px;display:table-cell;vertical-align:middle;color:#000;text-align:right;padding-right:10px"><a class="profile-toggle" onclick="BlurryMenu.closeMenu()"><i style="font-size:35px" class="fas fa-times" aria-hidden="true"></i> </a></div>
				</div>
			</div>
			<div class="homepageContainer container">
				<div style="padding:10px;font-size:14px;float:left;width:100%">
					<div style="background:#2f2f2f;width:100%;padding:0px">

						<script>
							function validarPesquisa_mobile() { if($.trim($("#pesquisa-mobile").val()) != "") return true; else {Apprise("'.getLinguas('key_search_empty').'");return false;}}
							var timer = 0;

							function showMobile()
							{
								$(".app-header").hide();
								$(".homepageContainer").hide();
								$("#search_mobile").show();
								$("#search_mobile_hinters").show();
								$("#pesquisa-mobile").focus();
							}
							function hideMobile()
							{
								$("#search_mobile").hide();
								$("#search_mobile_hinters").hide();
								$(".app-header").show();
								$(".homepageContainer").show();
							}
							function showMobileHints()
							{
								if($("#pesquisa-mobile").val() != "")
								{									
									clearTimeout(timer);								    
								    timer = setTimeout(function () {
										var str = $("#pesquisa-mobile").val();
										$.ajax({type: "GET",url: SITE_HTTP_AJAX_URL+"livesearchm.php",data: { q: str }}).done(function(resultado){$("#search_mobile_hinters").html(resultado);});
								    }, 1000 || 0);
								}
							}
						</script>
						<input name="pesquisa" placeholder="Introduza o nome do artigo..." onfocus="showMobile();" autocomplete="off" type="text" value="" style="height:30px;background-color:transparent;border:solid #424242 1px;color:#fff;width:200px" />

					</div>
				</div>
			</div>		

			<div style="width:100%;height:20px;background:#2f2f2f"></div>
		</div>
		<div style="overflow: scroll;
				overflow-x: scroll;
				overflow-x: hidden;
				float: left;
				width: 100%;
				border-top-left-radius: 25px;
				border-top-right-radius: 25px;
				box-shadow: 1px -3px 3px 0px #888888;
				background-color: #fff;
				margin-top: -15px;padding:10px">
		';


		$return_resp .= '<nav id="powermenu1" class="powerlistmenu" style="width:260px;margin-top:15px"><ul>';

		$return_resp .= '<li style="width:100%"><a href="/"  style="font-size:12px"><i class="fas fa-home" style="margin-right:10px"></i>Home</a></li>';

		if(isset($dados_cliente) && is_array($dados_cliente)) $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'"><i class="fa fa-user" style="margin-right:10px"></i>Minha Conta</a></li>';
		else $return_resp .= '<li style="width:100%"><a style="font-size:12px" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registar=1"><i class="fa fa-user" style="margin-right:10px"></i>'.getLinguas('key_registeseaqui').'</a></li>';

		$return_resp .= '<li style="width:100%"><a style="font-size:12px" href="" '.(isset($dados_cliente) && is_array($dados_cliente) ? 'class="online"' :'').'><i class="fas fa-sign-in-alt" style="margin-right:10px"></i>Login</a><ul><li style="position:relative">';

		ob_start();
		$return2='';
		show_menu_login3(1);
		$return2=ob_get_contents(); //Grab output
		$return_resp .= $return2;
		ob_end_clean(); //Discard output buffer

		$return_resp .= '</li></ul></li>';

		$return_resp .= '<div class="clear"></div>';

		$sum_fams=show_menu_subfamilias(0, 0, $rec_fams, $reparacoes);
		$class='';

		if(!empty($sum_fams['return'])) $submenus_mobile .= $sum_fams['return'];
		if($submenus_mobile != '')
		{
			$return_resp .= $submenus_mobile;
		}
		$return .= '<div class="c"></div></div>';
		$return_resp .= '</ul><div class="c"></div></nav></div>';
	}
	$rs->db_close(1);
	if(!$dados_cliente)
	{
		$fh=fopen($cache_filename, 'w');
		if($responsive == 1)fwrite($fh, $return_resp);
		else fwrite($fh, $return);
		fclose($fh);
		chmod($cache_filename, 0755);
	}
	if($responsive == 1) echo $return_resp;
	else echo $return;
}

function show_banner_mobile($responsive=0)
{
	if($_SESSION['ss_ismobile']==0)return;
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$return='';
	$return_resp='';
	$return_resp .= '<div id="mobile_banner">';
	$return_resp .= modulos(2);
	$return_resp .= '</div>';
	if($responsive == 1) echo $return_resp;
	else echo $return;
}

function show_sub_menu($id_pai=0, $responsive=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$return_resp='';
	$sql='SELECT
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
				m.Ordem ASC';
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		//$return_resp .= '<li>123456789 123456789 123456789 1234</li>';

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

			if(dotbright_image_resolve('files/menus/'.$menu_id.'_1.'.$menu_ext) && $menu_ext!='') $img='files/menus/'.$menu_id.'_1.'.$menu_ext;
			elseif($catalogo_id > 0 && $catalogo_tipo == 'familias')
			{
				if($category_img!='' && stream_resolve_include_path($category_img)) $img=$category_img;
			}
			elseif($catalogo_id > 0 && $catalogo_tipo == 'marcas')
			{
				$brand_info=get_produtc_brand($catalogo_id);
				if(isset($brand_info['marca_img']) && stream_resolve_include_path($brand_info['marca_img'])) $img=$brand_info['marca_img'];
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

				/*$res=show_menu_fams($catalogo_id, $TipoBanner, $nomeb);
				if ($res)
				{
					$return_resp .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="#" onclick="return false;">'.$nomeb.'</a></h3>';
					$return_resp .= '<ul '.$style.'>'.$res['menus'].'</ul><div class="c"></div>';
				}
				else
				{
					$return_resp .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="'.$p_seo.'">'.$nomeb.'</a></h3>';
				}
				$return_resp .= '</li>';*/

			}
			elseif($catalogo_id > 0 && $catalogo_tipo == 'marcas')
			{
				$brand_name=buscacabecalho_marca($catalogo_id, $GLOBALS['site_lang']);
				$menu_url2='m-'.$catalogo_id.'-'. str_replace(' ', '', limpa_special($brand_name)).'-'.$GLOBALS['site_lang'];
			}

			if($menu_nome != '')
			{
				if($menu_tipo == 'sales') $return .= '<a class="titulo" href="'.$menu_url2.'"><div class="menulink"><span>'.$menu_nome.'</span></div></a><div class="space">&nbsp;</div>';
				else $return .= '<a class="titulo" href="'.$menu_url2.'"><div class="menulink">'.$menu_nome.'</div></a><div class="space">&nbsp;</div>';
				$return_resp .= '<li>';
				$return_resp .= '<a class="titulo" href="'.$menu_url2.'">'.$menu_nome.'</a>';
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
			$newstring[0][$lines - 1] .= ' '.implode(' ', $newstring[$i]);
			unset($newstring[$i]);
		}
	}

	return ($array) ? $newstring[0] :implode($delimiter, $newstring[0]);
}
function IS_FAM_ACTIVE($family_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
function show_menu_tipo($id_pai, $tipo=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$return='';
	$TipoBanner='0';

	$ntable='menus_links';
	$ntable2='menus_links_lang';
	$sql='SELECT * FROM '.$ntable.' WHERE ID_ORG="'.$id_pai.'" AND Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link, $DB_name, $sql);

	$condicao='WHERE ID_PAI="'.$id_pai.'" AND lang="'.$site_lang.'"';
	$sql="SELECT * FROM ".$ntable2." $condicao";
	$rs2->db_open($db_link, $DB_name, $sql);
	if (!$rs2->EOF())
	{
		$nomeb=$rs2->db_data["URL_Nome2"];
		$return .= '<div class="titulo_d_menu">'.$nomeb.'</div>';
	}
	$rs2->db_close();

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
				$p_seo=str_replace($GLOBALS['site_http_url'], '', seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$catalogo_id, $catalogo_id, 1));
			}
			else
			{
				$p_seo='#';
			}

			$TipoMenu=$rs->db_data["TipoMenu"];
			$templates_id=explode(';', $rs->db_data["template_id"]);
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);

			$condicao='WHERE ID_PAI="'.$IDb.'" AND lang="'.$site_lang.'"';
			$sql="SELECT * FROM ".$ntable2." $condicao";
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
					$res=show_menu_fams(0, $TipoBanner, $nomeb);
					if ($res)
					{
						$return .= "\n";
						$return .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="#" onclick="return false;">'.$nomeb.'</a></h3>';
						$return .= "\n";
						$return .= '<ul '.$style.'>'.$res['menus'].'</ul><div class="c"></div>';
					}
					else
					{
						$return .= "\n";
						$return .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="'.$p_seo.'">'.$nomeb.'</a></h3>';
					}
					$return .= '</li>';
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
						$return .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="#" onclick="return false;">'.$nomeb.'</a></h3>';
						$return .= '<ul '.$style.'>'.$res.'</ul><div class="c"></div>';
					}
					else
					{
						$return .= '<li class="menu_tipo'.$tipo.' '.$class.'"><h3><a href="'.$p_seo.'">'.$nomeb.'</a></h3>';
					}
					$return .= '</li>';
				}
				elseif ($TipoMenu == 'precos')
				{
					$return .= '<li class="menu_tipo'.$tipo.'"><h3><a href="'.$p_seo.'">'.$nomeb.'</a></h3></li>';
				}
				elseif ($catalogo_id > 0)
				{

					$return .= '<li class="menu_tipo'.$tipo.'"><h3><a href="'.$p_seo.'">'.$nomeb.'</a></h3></li>';
				}
				else
				{
					$sql="
							SELECT
								m.* , ml.URL_Nome2
							FROM
								".$ntable." m
								INNER JOIN ".$ntable2." ml ON ml.ID_PAI=m.ID
							WHERE
								ID_ORG='".$IDb."' AND Visivel=1 AND ml.lang='$site_lang'
							ORDER BY
								Ordem ASC
							";
					$rs3->db_open($db_link, $DB_name, $sql);
					if (!$rs3->EOF())
					{
					}
					else
					{
						if ($URL_Link != '')
						{
							$return .= '<li class="menu_tipo'.$tipo.'"><h3><a href="'.$URL_Link.'">'.$nomeb.'</a></h3></li>';
						}
						else
						{
							$return .= '<li class="menu_tipo'.$tipo.'"><h3><a href="#" onclick="return false;">'.$nomeb.'</a></h3></li>';
						}
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
		$rs->db_close();
		$ntable='menus_links';
		$ntable2='menus_links_lang';
		$sql='SELECT * FROM '.$ntable.' WHERE ID="'.$id_pai.'"';
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
				$sql="Select pf.ID,pfl.Cabecalho from products_families pf inner join products_families_lang pfl on pfl.ID_PAI=pf.ID where pf.ID_ORG=".$catalogop_id." and pfl.lang='$site_lang'";
			}
			else
			{
				$sql='';
			}
			$rs2->db_open($db_link, $DB_name, $sql);
			while (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data["Cabecalho"];
				$catalogo_id=$rs2->db_data["ID"];
				$return .= '<div class="menu_tipo'.$tipo.'">';
				$return .= '<a href="'.$p_seo.'">'.$nomeb.'</a>';
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
	$return=getSubMenuItensFams2($ID_ORG);
	return $return;
}
function getSubMenuItensFams2($ID_ORG)
{
	$menus='';
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$familia=new Dataobj;
	$sql='SELECT
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
			$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
		// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$menus .= '<ul>';

		while(!$rs->EOF())
		{
			$marca_id=$rs->db_data['ID'];
			$marca_name=stripslashes($rs->db_data['Nome']);
			$I1=$rs->db_data['img_ext'];
			$URL_Link='m-'.$marca_id.'-'. str_replace(' ','',limpa_special($marca_name)).'-'.$site_lang.'?c='.$ID_ORG;
			$img_file='files/brands/'.$marca_id.'_1.'.$I1;
			$img_file_new='files/brands/'.$marca_id.'_1_menu.'.$I1;
			geraimagens($img_file, $img_file_new, 100, 50, array('ativo'=>1, 'w'=>105,'h'=>55), 0, 0, 0);
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
	$hConn=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
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
		$obj->db_close();
	}

	if(empty($arvore2)) $arvore2[]=$id_org;

	$output=array();
	$t=(count($arvore2)-1);
	for($a=0;$a<count($arvore2);$a++)
	{
		$output[$t]=$arvore2[$a];
		$t--;
	}
	ksort($output);
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{
		$IDb=$rs->db_data['ID'];
		$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb,$IDb,1);
		if($rs->db_data['Cabecalho2']) $nomeb=stripslashes($rs->db_data['Cabecalho2']); else $nomeb=stripslashes($rs->db_data['Cabecalho']);
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
function show_menu_brands($ID_ORG=0, $tipobanner=0, $menu_nome='')
{
	global $site_lang;
	$menus=getSubMenuItensbrands($ID_ORG);
	return $menus;
}
function getSubMenuItens($ID_ORG)
{
	$menus='';

	global $DB_host,$DB_user,$DB_pass, $DB_name, $site_http_url, $site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
				AND pfl.lang="'.$site_lang.'"
				AND pf.ID_ORG="'.$ID_ORG.'"
			ORDER BY
				pfl.Ordem ASC';
				//pfl.Cabecalho ASC';
	$i=0;
	$rs->db_open($db_link, $DB_name, $sql);
	if(!$rs->EOF())
	{
		$menus .= '<ul>';

		//$menus .= '<li>123456789 123456789 123456789 1234</li>';

		while (!$rs->EOF())
		{
			$IDb=$rs->db_data["ID"];
			$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb, $IDb, 1);
			$URL_Link=str_replace($site_http_url, '', $URL_Link);
			if ($rs->db_data["Cabecalho2"])$nomeb=$rs->db_data["Cabecalho2"];
			else $nomeb=$rs->db_data["Cabecalho"];
			if (isset($_GET['cat']) && $_GET['cat'] == $IDb)
			{
				$menus .= '<li class="active"><a href="'.$URL_Link.'" class="active" style="color:#fe5e1e !important">'.$nomeb.'</a><div class="c"></div></li><div class="c"></div>';
			}
			elseif (isset($_GET['cat']) && $_GET['cat'] > 0)
			{
				$menus .= '<li class="hidden2" onclick="javascript:window.open(\''.$URL_Link.'\',\'_self\')"><a href="'.$URL_Link.'">'.$nomeb.'</a><div class="c"></div></li><div class="c"></div>';
			}
			else
			{
				$menus .= '<li onclick="javascript:window.open(\''.$URL_Link.'\',\'_self\')"><a href="'.$URL_Link.'">'.$nomeb.'</a><div class="c"></div></li><div class="c"></div>';
			}

			$i++;

			$rs->db_next();
		}
		$menus .= '</ul>';
	}
	$rs->db_close();
	return $menus;
}
function getSubMenuItensbrands($ID_ORG)
{
	$menus='';
	global $DB_host,$DB_user,$DB_pass, $DB_name, $site_http_url, $site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
				AND pfl.lang="'.$site_lang.'"
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
		$URL_Link='m-'.$IDb.'-'.limpa_special($nomeb).'-'.$site_lang;
		if (isset($_GET['s_marca']) && $_GET['s_marca'] == $IDb)
		{
			$menus .= '<li class="active"><a href="'.$URL_Link.'" class="active" style="color:#fe5e1e !important;">'.$nomeb.'</a><div class="c"></div></li>';
		}
		elseif (isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
		{
			$menus .= '<li class="hidden2"><a href="'.$URL_Link.'" class="active">'.$nomeb.'</a></li>';
		}
		else
		{
			$menus .= '<li onclick="javascript:window.open(\''.$URL_Link.'\',\'_self\')"><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
		}
		$i++;
		$rs->db_next();
	}
	$rs->db_close();
	if (isset($_GET['s_marca']) && $_GET['s_marca'] > 0)
	{
		$menus .= '<li class="expand"><img src="layout/'.PROJETO.'/images/exp.png" style="cursor:pointer" /><div class="c"></div></li><div class="c"></div>';
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
			$return .= '<span class="sub2"><a href="'.$cat_url.'"><b>'.$cat_nome.'</b></a></span>';
			$blocos2++;

		}
		else
		{
			$return .= '<span class="sub2"><a href="'.$cat_url.'"><b>'.$cat_nome.'</b></a></span>';
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
				$return .= '<span class="sub2"><a href="'.$cat_url.'"><b>'.$cat_nome.' (Cont.)</b></a></span>';
				$return .= '<span class="sub3">'.$filter_nome.'</span>';
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
					$return .= '<span class="sub2"><a href="'.$cat_url.'"><b>'.$cat_nome.' (Cont.)</b></a></span>';
					$return .= '<span class="sub3">'.$filter_nome.'</span>';
					$blocos2++;
				}
				else
				{
					$return .= '<span class="sub3">'.$filter_nome.'</span>';
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
					$return .= '<span class="sub2"><a href="'.$cat_url.'"><b>'.$cat_nome.' (Cont.)</b></a></span>';
					$return .= '<span class="sub3">'.$filter_nome.' (Cont.)</span>';
					$return .= '<div class="sub4"><a href="'.$value_url.'"> - '.$value_nome.'</a></div>';
					$blocos2++;
				}
				else
				{
					$return .= '<div class="sub4"><a href="'.$value_url.'"> - '.$value_nome.'</a></div>';
				}
				$cc++;
			}
		}
	}
	$return .= '</div>';

	return $return;
}
function show_menu_marcas_hoverintent($ID_ORG=0)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $template;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$i=0;
	$uri=$_SERVER["REQUEST_URI"];
	$uri=str_replace($site_http_url, "", $uri);
	$right=0;
	$zindex=4;
	$sql="SELECT * FROM brands WHERE Visivel=1 ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
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
				$URL_Link=$site_http_url.'marca-'.$IDb.'-'.str_replace(' ', '', limpa_special($nomeb));
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
				echo '<div class="top"><div class="sub"><a href="'.$URL_Link.'" class="top p">'.$nomeb.'</a></div></div>';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$pai=$id_org;
	$nivel=-1;
	while ($pai > 0)
	{
		$rs->db_open($db_link, $DB_name, 'SELECT ID_ORG FROM products_families WHERE ID="'.$pai.'"');
		$pai=$rs->db_data["ID_ORG"];
		$rs->db_close();
		$nivel++;
	}

	return array('nivel' => $nivel, 'id_org' => $id_org);
}
function get_pages_childs($id_org)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql="SELECT * FROM pages WHERE ID_ORG='".$id_org."' AND ID_ORG > 0";
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	if ($org)
	{
		$sql="SELECT * FROM ".$ntable." WHERE ID_ORG='".$id_pai."' AND Visivel=1 ORDER BY Ordem ASC";
	}
	else
	{
		$sql="SELECT * FROM ".$ntable." WHERE ID='".$id_pai."' AND Visivel=1 ORDER BY Ordem ASC";
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
			$rs2->db_open($db_link, $DB_name, "Select * from ".$ntable2." $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['URL_Nome2'];
				$ul=str_replace('pages.php?page_id=', '', $rs->db_data['URL_Link']);
				$filhos=get_pages_childs($ul);
				if (substr_count($URL_Link, $_GET['page_id']) || in_array($_GET['page_id'], $filhos))
				{
					echo '<li class="master"><a href="#" style="background-image:url('.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/bg-menu2.jpg)"><b>'.$nomeb.'</b></a><ul style="display:block;">';
					echo show_menu_others2($IDb);
					echo '</ul></li>';
				}
				else
				{
					if ($URL_Link)
					{
						echo '<li class="master"><a href="#"><b>'.$nomeb.'</b></a><ul>';
						show_menu_others2($IDb);
						echo '</ul></li>';
					}
					else
					{
						echo '<li class="master"><a href="#"><b>'.$nomeb.'</b></a><ul>';
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

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT * FROM ".$ntable." WHERE ID_ORG='".$id_pai."' AND Visivel=1 ORDER BY Ordem ASC";
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
			$rs2->db_open($db_link, $DB_name, "Select * from ".$ntable2." $condicao");
			if (!$rs2->EOF())
			{
				$nomeb=$rs2->db_data['URL_Nome2'];
				if (substr_count($URL_Link, $_GET['page_id']))
				{
					echo '<li class="active"><a href="'.$URL_Link.'"><span>'.$nomeb.'</span></a></li>';
				}
				else
				{
					if ($URL_Link)
					{
						echo '<li><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
					}
					else
					{
						echo '<li><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
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
	$sql="SELECT * FROM products_families WHERE visivel=1 AND ID_ORG=0 ORDER BY Ordem ASC";
	$hRes=$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb, $IDb, 1);
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
					echo '<li class="master" id="'.$IDb.'"><a href="#" style="background-image:url('.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/bg-menu2.jpg)"><b>'.$nomeb.'</b></a></li><ul id="catalogo_'.$IDb.'" style="display:block;">';
					echo show_menu2($IDb);
					echo '</ul>';
				}
				else
				{
					echo '<li class="master" id="'.$IDb.'"><a href="#"><b>'.$nomeb.'</b></a></li><ul id="catalogo_'.$IDb.'">';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='".$id_pai."' ORDER BY Ordem ASC");
	if ($rs->EOF())
	{
		$rs->db_close();
		$id_pai=get_pai($id_pai);
		$rs=new Dataobj;
		$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='".$id_pai."' ORDER BY Ordem ASC");
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
		$URL_Link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$IDb, $IDb, 1);
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
				$return .= '<li class="active"><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
			}
			else
			{
				$return .= '<li><a href="'.$URL_Link.'">'.$nomeb.'</a></li>';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID='".$id_org."' ORDER BY Ordem ASC");
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs->db_open($db_link, $DB_name, "SELECT * FROM products_families WHERE visivel=1 AND ID_ORG='".$id_org."' ORDER BY Ordem ASC LIMIT 1");
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

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select ".$ntable.".ID, ".$ntable.".URL_Link, ".$ntable2.".URL_Nome2 from ".$ntable." inner join ".$ntable2." on ".$ntable2.".ID_PAI=".$ntable.".ID where ID_ORG='".$id_pai."' and Visivel=1 order by Ordem asc";
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
				<div class="line">&nbsp;</div><?php if ($URL_Link)
			{
				show_content($id_page);
			}
			else
			{
				show_menu3_sub($IDb);
			} ?></div><?php
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

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select ".$ntable.".ID, ".$ntable.".URL_Link, ".$ntable2.".URL_Nome2 from ".$ntable." inner join ".$ntable2." on ".$ntable2.".ID_PAI=".$ntable.".ID where ID_ORG='".$id_pai."' and Visivel=1 order by Ordem Asc";
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
			<div style="width:250px;<?= $style; ?>"><a href="<?= $URL_Link; ?>" class="footer_grey"><?= $URL_Nome[0]; ?></a></div><?php
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

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="Select ".$ntable.".ID,".$ntable.".img_ext, ".$ntable.".URL_Link, ".$ntable2.".URL_Nome2 from ".$ntable." inner join ".$ntable2." on ".$ntable2.".ID_PAI=".$ntable.".ID where ID_ORG='".$id."' and Visivel=1 and  ".$ntable2.".Lang='".$site_lang."' order by Ordem DESC";
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
			if ($img_ext && dotbright_image_resolve($site_shell_url.'files/menus/'.$IDb.'_1.'.$img_ext))
			{
				?>
				<div class="socials-networks"><a href="<?= $URL_Link; ?>"><img
						src="<?= 'files/menus/'.$IDb.'_1.'.$img_ext ?>" title="<?= $URL_Nome ?>" alt=""/></a>
				</div><?php }
			else
			{
				?>
				<div class="socials-networks"><a href="<?= $URL_Link; ?>"><?= $URL_Nome ?></a></div><?php }
			$rs->db_next();
		}
		echo '<div class="titlefooter">Segue-nos</div>';
	}
	$rs->db_close();
}
function show_footer()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo, $site_shell_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="SELECT menus_links.ID,menus_links.img_ext,menus_links.novajanela, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG='168' AND Visivel=1 AND  menus_links_lang.Lang='".$site_lang."' ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	if (!$rs->EOF())
	{
		$sql="SELECT menus_links.ID,menus_links.novajanela,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID='168' AND Visivel=1 AND  menus_links_lang.Lang='".$site_lang."' ORDER BY Ordem ASC";
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
			</a>&nbsp;&nbsp;<?php
			$rs->db_next();
		}
	}
	$rs->db_close();
}
function show_footer2()
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $site_http_url, $tema, $site_lang, $page_id, $seo, $site_shell_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$rs4=new Dataobj;
	$sql="SELECT menus_links.ID,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE ID_ORG='138' AND Visivel=1 AND  menus_links_lang.Lang='".$site_lang."' ORDER BY Ordem ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	if (!$rs->EOF())
	{
		$sql="SELECT menus_links.ID,menus_links.img_ext, menus_links.URL_Link, menus_links_lang.URL_Nome2 FROM menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI=menus_links.ID WHERE menus_links.ID='138' AND Visivel=1 AND  menus_links_lang.Lang='".$site_lang."' ORDER BY Ordem ASC";
		$rs2->db_open($db_link, $DB_name, $sql);
		$URL_Nome=$rs2->db_data['URL_Nome2'];
		$rs2->db_close();
		echo '<div id="info-pagamento"><div class="titulo-footer">'.$URL_Nome.':</div>';
		while (!$rs->EOF())
		{
			$i++;
			$IDb=$rs->db_data['ID'];
			$img_ext=$rs->db_data['img_ext'];
			$URL_Link=seo($rs->db_data['URL_Link']);
			$URL_Nome=$rs->db_data['URL_Nome2'];
			?>
			<div id="icon-trf-bancaria"
			     style="background:url(<?= $site_http_url ?>files/menus/<?= $IDb ?>_1.<?= $img_ext ?>) no-repeat"><?= $URL_Nome; ?></div><?php
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
		echo '<img src="layout/'.PROJETO.'/logos/logo.png" alt="">';
	}
	else
	{
		echo '<a href="index.php" title="'.$key_home_description[$site_lang].'"><img src="layout/'.PROJETO.'/logos/logo.png" alt=""></a>';
	}
}
//LINGUAS MENU
function linguas_show()
{
	$pageURL=get_uri();
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
		$pageURL=str_replace("?site_lingua=".$Sigla, "", $pageURL);
		$pageURL=str_replace("&site_lingua=".$Sigla, "", $pageURL);
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
				echo '<option value="'.$pageURL.$op.'site_lingua='.$Sigla.'" selected="selected">'.$NomeLingua.'</option>';
			}
			else
			{
				echo '<option value="'.$pageURL.$op.'site_lingua='.$Sigla.'">'.$NomeLingua.'</option>';
			}
			$c++;
		}
		echo '</select>';
	}
}
function show_menu_simples($id_pai)
{
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang, $site_http_url;
	$ntable='menus_links';
	$ntable2='menus_links_lang';

	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$sql="SELECT ml.*, mll.URL_Nome2 FROM ".$ntable." ml INNER JOIN ".$ntable2." mll ON (mll.ID_PAI=ml.ID) WHERE ml.ID='".$id_pai."' AND Visivel=1 AND mll.Lang='".$site_lang."' ORDER BY ml.Ordem, mll.URL_Nome2 ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		$nome_categoria=$rs->db_data['URL_Nome2'];
	}
	$rs->db_close();
	$str_menu='';
	$sql="SELECT ml.*, mll.URL_Nome2 FROM ".$ntable." ml INNER JOIN ".$ntable2." mll ON (mll.ID_PAI=ml.ID) WHERE ID_ORG='".$id_pai."' AND Visivel=1 AND mll.Lang='".$site_lang."' ORDER BY ml.Ordem, mll.URL_Nome2 ASC";
	$rs->db_open($db_link, $DB_name, $sql);
	$i=0;
	echo '<div class="colMenu"><ul>';

	while (!$rs->EOF())
	{
		echo '<li><a href="'.str_replace($site_http_url, '', seo($rs->db_data['URL_Link'])).'">'.($rs->db_data['URL_Nome2']).'</a></li>';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$pai=$id_org;
	while ($pai > 0)
	{
		$sql='SELECT ID_ORG FROM products_families WHERE ID="'.$pai.'"';
		$rs->db_open($db_link, $DB_name, $sql);
		$pai=$rs->db_data['ID_ORG'];
		$nivel[]=$pai;
		$rs->db_close();
	}

	return $nivel;
}
function menu_categorias() // aqui
{
	global $total_array;
	global $DB_host,$DB_user,$DB_pass, $DB_name, $m_http_url_images, $tema, $site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
			echo '<a '.$class.' href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$id, $id, 1).'">'.htmlentities_memo(stripslashes($cabecalho)).'</a>&nbsp;('.$total2.')';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$str_menu='';
	$sql='SELECT pf.ID, pf.ID_ORG, pfl.Cabecalho, (SELECT count(id) FROM products p WHERE p.ID_ORG=pf.ID) AS Total
	FROM products_families pf
	INNER JOIN products_families_lang pfl ON (pf.ID=pfl.ID_PAI)
	WHERE pf.visivel=1 AND pf.Download=1 AND pf.ID_ORG="'.$id_org.'"
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
			$return .= '<a '.$class.' href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$id, $id, 1).'">'.htmlentities_memo(stripslashes($cabecalho)).'</a>&nbsp;('.$total2.')';
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
				$str .= '<li><a '.$class.' href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$item['ID'], $item['ID'], 1).'">'.htmlentities_memo(stripslashes($item['Cabecalho'])).'</a>';
				$total_array[]=$item['Total'];
				$str .= '&nbsp;('.array_sum($total_array).')';
				$str .= $str1;
			}
			else
			{
				$str .= '<li><a '.$class.' href="'.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$item['ID'], $item['ID'], 1).'">'.htmlentities_memo(stripslashes($item['Cabecalho'])).'</a>';
				$str .= '&nbsp;('.$item['Total'].')';
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
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql='SELECT bl.ID_PAI, bl.Nome
	FROM brands b
	INNER JOIN brands_lang bl ON (bl.ID_PAI=b.ID)
	WHERE b.Visivel=1 AND bl.Lang="'.$site_lang.'"';
	$rs->db_open($db_link, $DB_name, $sql);
	echo '<select name="s_marca" id="s_marca" style="width:100%">';
	echo '<option value="">'.$GLOBALS['key_selecione'][$site_lang].'</option>';
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
		echo '<option '.$selected.' value="'.$rs->db_data['ID_PAI'].'">'.$rs->db_data['Nome'].'</option>';
		$rs->db_next();
	}
	$rs->db_close();
	echo '</select>';
}
function menu_topo_default($id_pai=1,$idMobile=2)
{
	$return='<style>
	@media only screen and (max-width:991px){

		.mobile-menu-wrapper {display:block;position:fixed !important;top:0;width:100%;z-index:30;height:64px}
		.main_content{padding-top:74px}
		#resp-menu {position:fixed;top:64px;left:0;right:0;bottom:0;background:#222;color:#FFF;overflow:scroll;overscroll-behavior:contain;z-index:50;transition:transform .3s ease-out 0s,opacity .3s ease-out 0s;padding:0;min-height:100%;transform:translate3d(0, 0, 0);pointer-events:auto;transition-delay:.1s;will-change:transform}
		#resp-menu li{padding:5px 0;line-height:30px}
		#resp-menu li:not(:last-child){border-bottom:1px solid rgba(255,255,255,0.1)}

		.collapsing {opacity:0;transition:opacity 0.3s linear;-webkit-transition:opacity 0.3s linear;-moz-transition:opacity 0.3s linear;-o-transition:opacity 0.3s linear}
		.navbar-collapse.collapse.in{opacity:1;transition:opacity 0.3s linear;-webkit-transition:opacity 0.3s linear;-moz-transition:opacity 0.3s linear;-o-transition:opacity 0.3s linear}

		/* Icon 1 */
		.animated-icon1, .animated-icon2, .animated-icon3 {width:30px;height:20px;position:relative;margin:0px;-webkit-transform:rotate(0deg);-moz-transform:rotate(0deg);-o-transform:rotate(0deg);transform:rotate(0deg);-webkit-transition:.5s ease-in-out;-moz-transition:.5s ease-in-out;-o-transition:.5s ease-in-out;transition:.5s ease-in-out;cursor:pointer}
		.animated-icon1 span, .animated-icon2 span, .animated-icon3 span {display:block;position:absolute;height:3px;width:100%;border-radius:9px;opacity:1;left:0;-webkit-transform:rotate(0deg);-moz-transform:rotate(0deg);-o-transform:rotate(0deg);transform:rotate(0deg);-webkit-transition:.25s ease-in-out;-moz-transition:.25s ease-in-out;-o-transition:.25s ease-in-out;transition:.25s ease-in-out}
		.animated-icon1 span {background:#e65100}
		.animated-icon2 span {background:#e3f2fd}
		.animated-icon3 span {background:#f3e5f5}
		.animated-icon1 span:nth-child(1) {top:0px}
		.animated-icon1 span:nth-child(2) {top:10px}
		.animated-icon1 span:nth-child(3) {top:20px}
		.animated-icon1.open span:nth-child(1) {top:11px;-webkit-transform:rotate(135deg);-moz-transform:rotate(135deg);-o-transform:rotate(135deg);transform:rotate(135deg)}
		.animated-icon1.open span:nth-child(2) {opacity:0;left:-60px}
		.animated-icon1.open span:nth-child(3) {top:11px;-webkit-transform:rotate(-135deg);-moz-transform:rotate(-135deg);-o-transform:rotate(-135deg);transform:rotate(-135deg)}
		/* Icon 3*/

		.animated-icon2 span:nth-child(1) {top:0px}
		.animated-icon2 span:nth-child(2), .animated-icon2 span:nth-child(3) {top:10px}
		.animated-icon2 span:nth-child(4) {top:20px}
		.animated-icon2.open span:nth-child(1) {top:11px;width:0%;left:50%}
		.animated-icon2.open span:nth-child(2) {-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg)}
		.animated-icon2.open span:nth-child(3) {-webkit-transform:rotate(-45deg);-moz-transform:rotate(-45deg);-o-transform:rotate(-45deg);transform:rotate(-45deg)}
		.animated-icon2.open span:nth-child(4) {top:11px;width:0%;left:50%}
		/* Icon 4 */

		.animated-icon3 span:nth-child(1) {top:0px;-webkit-transform-origin:left center;-moz-transform-origin:left center;-o-transform-origin:left center;transform-origin:left center}
		.animated-icon3 span:nth-child(2) {top:10px;-webkit-transform-origin:left center;-moz-transform-origin:left center;-o-transform-origin:left center;transform-origin:left center}
		.animated-icon3 span:nth-child(3) {top:20px;-webkit-transform-origin:left center;-moz-transform-origin:left center;-o-transform-origin:left center;transform-origin:left center}
		.animated-icon3.open span:nth-child(1) {-webkit-transform:rotate(45deg);-moz-transform:rotate(45deg);-o-transform:rotate(45deg);transform:rotate(45deg);top:0px;left:8px}
		.animated-icon3.open span:nth-child(2) {width:0%;opacity:0}
		.animated-icon3.open span:nth-child(3) {-webkit-transform:rotate(-45deg);-moz-transform:rotate(-45deg);-o-transform:rotate(-45deg);transform:rotate(-45deg);top:21px;left:8px}
	}
	</style>';

	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url, $site_lang, $site_title;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new Dataobj();
	$rs2 = new Dataobj();
	$logotipo =push_confs('LOGOTIPO_PRINCIPAL');
	$menu_orientacao = 1;
	if ($menu_orientacao == 1)
	{
		//Menus Site
		$return.='<div id="header" class="sticky-off site-menu-wrapper">';
		$return.='<div class="container">';
		$return.='<div class="navbar megamenu-width">';
		$return.='<div class="navbar-inner">';
		$return.='<a href="index.php" class="logo pull-left">';
		$return.='<img src="'.$logotipo.'" alt="'.$site_title.'">';
		$return.='</a>';
		$return.='<ul class="top-navigation">';
        if($GLOBALS['tem_area_reservada'])
        {
        	$return.='<li><ul>';
	        if($dados = cliente_logado())
	        {
	            $return.='<li><a href="'.seo('pages.php?page_id='.$GLOBALS['areaprivada_page_id']).'"><img alt="conta" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/icon-conta.png"> OLÁ, '.$dados['Nome'].'</a></li>';
	            $return.='<li><a href="'.seo('pages.php?page_id='.$GLOBALS['login_page_id']).'?logout=1">LOG OUT</a></li>';
	        }
	        else
	        {
	        	 $return.='<li><a href="'.seo('pages.php?page_id='.$GLOBALS['areaprivada_page_id']).'"><img alt="conta" src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/icon-conta.png"></a></li>';
	        }
        	$return.='</ul></li>';
	    }

	    $html_linguas=show_linguas_institucional();
        if($html_linguas != '')
        {
			$return.='<li>';
	        $return.=$html_linguas;
			$return.='</li>';
        }
		$return.='</ul>';

		$sql = "SELECT ID, URL_Link, URL_Nome,img_ext,TipoMenu, class FROM menus_links WHERE ID_ORG='$id_pai' AND Visivel=1 ORDER BY Ordem ASC";
		$rs->db_open($db_link, $DB_name, $sql);
		if (!$rs->EOF())
		{
			$i = 0;
			$return.='<ul id="main-menu" class="collapse navbar-collapse nav custom pull-right ">';
			while (!$rs->EOF())
			{
				$IDb = $rs->db_data['ID'];
				$TipoMenu = $rs->db_data['TipoMenu'];
				$URL_Link = seo($rs->db_data['URL_Link']);
				$classMenu = $rs->db_data['class'];
				$URL_Link = str_replace($site_http_url, '', $URL_Link);
				$URL_Nome = $rs->db_data['URL_Nome'];

				$sql = "Select * from menus_links_lang where ID_PAI='$IDb' and lang='$site_lang'";
				$rs2->db_open($db_link, $DB_name, $sql);
				if (!$rs2->EOF())
				{
					$seta = '';
					$class = '';

					$request = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/'));
					if (substr_count($request, $URL_Link))
					{
						$class = 'class="active"';
					}
					elseif ($request == "/" && substr_count($URL_Link, 'homepage'))
					{
						$class = 'class="active"';
					}
					else
					{
						if (verifica_filhos_vcore($IDb))$class = 'class="active"';
					}
					$auxvar = menusub_topo_defaul($IDb, $classMenu);
					$auxmargin = '';
					if($auxvar)
					{
						$auxmargin = 'style="margin-right:5px;"';
						$seta = '<i class="Chevron"></i>';
					}
					$nomeb = $rs2->db_data["URL_Nome2"].$seta;

					if ($rs->db_data["TipoMenu"] == 'familias')$return2 = menusub_topo_prod();
					else  $return2 = $auxvar;

					$link_image = '';
					$URL_Imagem = $rs->db_data['img_ext'];

					$URL_Imagem_Hover=$rs->db_data['img2_ext'];
					if($URL_Imagem)
					{
						if($URL_Imagem_Hover)
						{
							$home_id=home_page_id();
							if($page_id==$home_id && $IDb==54)
							{
								$link_image='<img src="files/menus/'.$IDb.'_2.'.$URL_Imagem_Hover.'"></img>';
							}else{
								$link_image='<img src="files/menus/'.$IDb.'_1.'.$URL_Imagem.'"

							onmouseover="this.src=\'files/menus/'.$IDb.'_2.'.$URL_Imagem_Hover.'\'"
							onmouseout="this.src=\'files/menus/'.$IDb.'_1.'.$URL_Imagem.'\'"
							border="0"
							alt=""
							></img>';
							}
						}
						else
						{
							$link_image='<img class="abrincar" src=files/menus/'.$IDb.'_1.'.$URL_Imagem.'></img>';
						}
					}
					else $link_image=$nomeb;

					if($rs->db_data['URL_Link'] == 'pages.php?page_id=1') {$href='href="'.$URL_Link.'"';}
					if($rs->db_data['URL_Link'] == '') {$href='';$class_menu='cursor';}
					if($rs->db_data['URL_Link'] != '') {$href='href="'.$URL_Link.'"';}
					if($TipoMenu == 'popup')$href = 'style="cursor:pointer" data-target="#popupforms" data-toggle="modal" onclick="openmodal(\''.$URL_Link.'\',\'100%\',\'520\')"';

					if($TipoMenu == 'pesquisa')
					{
						$pesquisa = '<form id="pesquisa_form2" name="pesquisa" action="pages.php" method="get" onsubmit="return validarPesquisa()" style="display:initial;">
             						<input type="hidden" name="page_id" value="'.$GLOBALS['pesquisa_page_id'].'"><div id="pesquisatexto" style="display:none;">
             						<input class="HvRm txtpesquisa" type="text" alt="Submit" value="" placeholder="Pesquisa" id="search" name="pesquisa" autocomplete="off">
             						<button style="background:transparent;border:none;"><svg alt="Submit" style="color: #89c545;" aria-hidden="true" focusable="false" data-prefix="fas" data-icon="search" class="svg-inline--fa fa-search fa-w-16 fa-2x" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg><!-- <i class="fas fa-search fa-2x" alt="Submit" style="color:#89c545;"></i> --></button>
             						</div>
             						</form>';

             						$pesquisa='<div class="modal " id="pesquisatexto" tabindex="-1" role="dialog" aria-labelledby="pesquisaModalLabel" style="display:none">
												  <div class="modal-dialog" role="document">
												    <div class="modal-content" style="background-color:#000">
												      <div class="modal-header">

												        <button type="button " class="close" data-dismiss="modal" aria-label="Close" onclick="CloseModal()">
												          <span aria-hidden="true">×</span>
												        </button>
												      </div>
												      <div class="modal-body">
												      	<h5 class="modal-title" id="pesquisaModalLabel" style="color:#f6600b;"></h5><br>
												            <div class="search">
												       <script></script>
												        <form name="pesquisa" action="pages.php" method="get" onsubmit="return validarPesquisa()">
												             <input type="hidden" name="page_id" value="4">

												            <input class="HvRm form-control" type="text" value="" placeholder="Pesquisa" id="search_t" name="pesquisa" onkeyup="showHintsSearch();" autocomplete="off" style="width: 88%;border: none;float:left;">

												            <button style="background:transparent;border:none;"><svg class="svg-inline--fa fa-search fa-w-16 fa-2x" alt="Submit" style="color: #89b61f;" aria-hidden="true" data-prefix="fas" data-icon="search" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg=""><path fill="currentColor" d="M505 442.7L405.3 343c-4.5-4.5-10.6-7-17-7H372c27.6-35.3 44-79.7 44-128C416 93.1 322.9 0 208 0S0 93.1 0 208s93.1 208 208 208c48.3 0 92.7-16.4 128-44v16.3c0 6.4 2.5 12.5 7 17l99.7 99.7c9.4 9.4 24.6 9.4 33.9 0l28.3-28.3c9.4-9.4 9.4-24.6.1-34zM208 336c-70.7 0-128-57.2-128-128 0-70.7 57.2-128 128-128 70.7 0 128 57.2 128 128 0 70.7-57.2 128-128 128z"></path></svg><!-- <i class="fas fa-search fa-2x" alt="Submit" style="color:#f6600b;"></i> --></button>
												        </form>
												        <div id="search_hinters" class="search_hinters"></div>
												    </div>
												    
												      </div>

												    </div>
												  </div>
												</div>';
						$return.='<li '.$class.'>'.$pesquisa.'<a id="botao-pesquisa" class="'.$class_menu.'">'.$link_image.'</a>'.$return2.'</li>';
					}
					else
					{
						if($link_image || $return2)$return.='<li '.$class.'><a class="'.$class_menu.'" '.$href.'>'.$link_image.'</a>'.$return2.'</li>';
					}
					$i++;
				}
				$rs2->db_close();
				$rs->db_next();
			}
			$return.='</ul>';
		}
		$rs->db_close();

		$return.='</div>';
		$return.='</div>';
		$return.='</div>';
		$return.='</div>';

		//Menus Mobile
		$return.='<div class="mobile-menu-wrapper">';
		$return.='<div class="container">';
		$return.='<div class="navbar megamenu-width">';
		$return.='<div class="navbar-inner">';
		$return.='<a href="index.php" class="logo">';
		$return.='<img src="'.$logotipo.'" alt="'.$site_title.'">';
		$return.='</a>';

		$sql = "SELECT ID,URL_Link,URL_Nome,TipoMenu FROM menus_links WHERE ID_ORG='$idMobile' AND Visivel=1 ORDER BY Ordem ASC";
		$rs->db_open($db_link,$DB_name,$sql);
		if (!$rs->EOF())
		{
			$i=0;
			$htmlLinguas = show_linguas(1);
			$return.='<ul id="mobile-menu" class="button-right pull-right">';
			$return.='<li>';
			$return.='<button type="button" class="navbar-toggle first-button" data-toggle="collapse" data-target=".navbar-collapse" data-parent=".mobile-menu-wrapper" style="white-space:nowrap;text-align:right;outline:none">';
			$return.='<div class="animated-icon1"><span></span><span></span><span></span></div>';
			$return.='</button>';
			$return.='</li>';
			$return.='</ul>';
			$return.='<div class="clear"></div>';
			$return.='<div id="resp-menu" class="collapse navbar-collapse slide custom" style="background-color:transparent">';
			$return.='<ul style="padding:0px !important;list-style-type:none;background-color:#222">';
			while (!$rs->EOF())
			{
				$IDb = $rs->db_data['ID'];
				$TipoMenu = $rs->db_data['TipoMenu'];
				$URL_Link = seo($rs->db_data['URL_Link']);
				$URL_Link = str_replace($site_http_url, '', $URL_Link);
				$URL_Nome = $rs->db_data['URL_Nome'];
				$sql = "Select * from menus_links_lang where ID_PAI='$IDb' and lang='$site_lang'";
				$rs2->db_open($db_link, $DB_name, $sql);
				if (!$rs2->EOF())
				{
					$class = '';
					$request = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/'));
					if (substr_count($request, $URL_Link))
					{
						$class = 'class="active"';
					}
					elseif ($request == "/" && substr_count($URL_Link, 'homepage'))
					{
						$class = 'class="active"';
					}
					else
					{
						if (verifica_filhos_vcore($IDb))
						{
							$class = 'class="active"';
						}
					}
					$nomeb = $rs2->db_data["URL_Nome2"];
					if ($URL_Nome != '[familias]')
					{
						$return2 = menusub_topo($IDb);
					}
					else
					{
						$return2 = menusub_topo_familias(0);
					}

					if($TipoMenu == 'popup')
					{
					$href = 'style="cursor:pointer" data-target="#popupforms" data-toggle="modal" onclick="openmodal(\''.$URL_Link.'\',\'100%\',\'520\')"';

					$pesquisa = '<form name="pesquisa" action="pages.php" method="get" onsubmit="return validarPesquisamobile()" style="display:initial;">
         						<input type="hidden" name="page_id" value="4"><div id="pesquisatexto" style="display:block;margin:10px auto"><input class="HvRm txtpesquisa" type="text" alt="'.getLinguas('key_pesquisar').'" value="" placeholder="'.getLinguas('key_pesquisar').'" id="pesq-mobile" name="pesquisa" autocomplete="off">
         						<button class="btn-pesq"><i class="fa fa-search" aria-hidden="true"></i></button></div></form>';
					$return.='<li '.$class.'>'.$pesquisa.'</li>';
				}
				else
				{
					$return .= '<li '.$class.'>';
					$return .= '<a class="mainaarrow" id="arrow-'.$IDb.'" href="'.$URL_Link.'" style="display:block"><i class="fas fa-dot-circle"></i>'.$nomeb;
					if(strlen($return2) > 0)
					{
						$return .= '<span class="mainarrow"><i class="fa fa-angle-right" aria-hidden="true"></i></span>';
					}
					$return.= '</a>';
					$return .= $return2;
					$return.= '</li>';
				}
				$i++;
			}
			$rs2->db_close();
			$rs->db_next();
		}

		global $subscribe_newsletter;

		if($subscribe_newsletter==1)
		{
			$return .= '<li '.$class.'><div class="newsletter-momenu">';
			ob_start();
			$return2='';
			newsletterRegisterSmallForm($TipoMenu, 'col-sm-2');
			$return2=ob_get_contents(); // Grab output
			$return .= '<span>'.getLinguas('key_subscribe_newsletter2').'</span>'.$return2.'';
			ob_end_clean(); // Discard output buffer
			$return.= '</div></li>';
		}

		//redes sociais
		$menus_redes_sociais=show_menu_footer_urls(260);
		if($menus_redes_sociais!='')
		{
			$return.= '<li '.$class.'><div class="newsletter-redes">';
			$return.= $menus_redes_sociais;
			$return.= '</div></li>';
		}

		//show_menu_footer_urls,260
		if ($htmlLinguas != "")
		{
			$htmlLinguas=str_replace('language-collapse','in',$htmlLinguas);
			$htmlLinguas=str_replace('slide custom','',$htmlLinguas);
			$return.=$htmlLinguas;
		}
		$return.='</ul>';
		$return.='</div>';
		}
		$rs->db_close();
		$return.='</div>';
		$return.='</div>';
		$return.='</div>';
		$return.='</div>';
		$return .= '<script>
		$(".mainaarrow").click(function(){
			if(!$("#"+this.id).hasClass("opened")){
				$("#"+this.id).addClass("opened");
			}else{
				$("#"+this.id).removeClass("opened");
			}

			});

			function validarPesquisamobile() {
			if($.trim($("#pesq-mobile").val()) != "") return true; else {	Apprise("'.getLinguas('key_search_empty').'");	return false;}}
		</script>';
	}
	else
	{
		//Menus Site
		$return.='<header id="header" class="sticky-off site-menu-wrapper">';
		$return.='<div class="container" >';
		$return.='<div class="navbar megamenu-width">';
		$return.='<div class="navbar-inner">';
		$return.='<a href="index.php" class="logo pull-right">';
		$return.='<img src="'.$logotipo.'" alt="'.$site_title.'">';
		$return.='</a>';


		$sql = "SELECT ID, URL_Link, URL_Nome FROM menus_links WHERE ID_ORG='$id_pai' AND Visivel=1 ORDER BY Ordem ASC";
		$rs->db_open($db_link, $DB_name, $sql);
		if (!$rs->EOF())
		{
			$i = 0;
			$return.='<ul id="main-menu" class="collapse navbar-collapse nav slide custom pull-left">';
			while (!$rs->EOF())
			{
				$IDb = $rs->db_data['ID'];
				$URL_Link = seo($rs->db_data['URL_Link']);
				$URL_Link = str_replace($site_http_url, '', $URL_Link);
				$URL_Nome = $rs->db_data['URL_Nome'];
				$sql = "Select * from menus_links_lang where ID_PAI='$IDb' and lang='$site_lang'";
				$rs2->db_open($db_link, $DB_name, $sql);
				if (!$rs2->EOF())
				{
					$class = '';
					$request = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/'));
					if (substr_count($request, $URL_Link))
					{
						$class = 'class="active"';
					}
					elseif ($request == "/" && substr_count($URL_Link, 'homepage'))
					{
						$class = 'class="active"';
					}
					else
					{
						if (verifica_filhos_vcore($IDb))
						{
							$class = 'class="active"';
						}
					}
					$nomeb = $rs2->db_data["URL_Nome2"];
					if ($URL_Nome != '[familias]')
					{
						$return2 = menusub_topo($IDb);
					}
					else
					{
						$return2 = menusub_topo_familias(0);
					}
					$return.='<li '.$class.'><a href="'.$URL_Link.'">'.$nomeb.'</a>'.$return2.'</li>';

					$i++;
				}
				$rs2->db_close();
				$rs->db_next();
			}
			$return.='</ul>';

		}

		$rs->db_close();

		$return.='</div>';
		$return.='</div>';
		$return.='</div>';
		$return.='</header>';

		//Menus Mobile
		$return.='<header class="mobile-menu-wrapper">';
		$return.='<div class="container" >';
		$return.='<div class="navbar megamenu-width">';
		$return.='<div class="navbar-inner">';
		$return.='<a href="index.php" class="logo pull-right">';
		$return.='<img src="'.$logotipo.'" alt="'.$site_title.'">';
		$return.='</a>';

		$sql = "SELECT ID, URL_Link, URL_Nome FROM menus_links WHERE ID_ORG='$idMobile' AND Visivel=1 ORDER BY Ordem ASC";
		$rs->db_open($db_link, $DB_name, $sql);
		if (!$rs->EOF())
		{
			$i = 0;

			$htmlLinguas = show_linguas(1);

			$return.='<ul id="mobile-menu" class="button-right pull-left">';
			if ($dados = cliente_logado())
			{
				$return.='<li><a href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?logout=1" class="navbar-toggle"><i class="fa fa-sign-out"></i></a></li>';
			}
			if ($htmlLinguas != "")
			{
				$return.='<li><button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".language-collapse" data-parent=".mobile-menu-wrapper"><i class="fa fa-globe"></i></button></li>';
			}
			$return.='<li><button class="navbar-toggle" type="button" data-toggle="collapse" data-target=".navbar-collapse" data-parent=".mobile-menu-wrapper"><i class="fa fa-reorder"></i></button></li>';
			$return.='</ul>';


			if ($htmlLinguas != "")
			{
				$return.=$htmlLinguas;
			}

			$return.='<div id="resp-menu" class="collapse navbar-collapse nav slide custom">';
			$return.='<ul>';
			while (!$rs->EOF())
			{
				$IDb = $rs->db_data['ID'];
				$URL_Link = seo($rs->db_data['URL_Link']);
				$URL_Link = str_replace($site_http_url, '', $URL_Link);
				$URL_Nome = $rs->db_data['URL_Nome'];
				$sql = "Select * from menus_links_lang where ID_PAI='$IDb' and lang='$site_lang'";
				$rs2->db_open($db_link, $DB_name, $sql);
				if (!$rs2->EOF())
				{
					$class = '';
					$request = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/'));
					if (substr_count($request, $URL_Link))
					{
						$class = 'class="active"';
					}
					elseif ($request == "/" && substr_count($URL_Link, 'homepage'))
					{
						$class = 'class="active"';
					}
					else
					{
						if (verifica_filhos_vcore($IDb))
						{
							$class = 'class="active"';
						}
					}
					$nomeb = $rs2->db_data["URL_Nome2"];
					if ($URL_Nome != '[familias]')
					{
						$return2 = menusub_topo($IDb);
					}
					else
					{
						$return2 = menusub_topo_familias(0);
					}
					$return.='<li '.$class.'><a href="'.$URL_Link.'">'.$nomeb.'</a>'.$return2.'</li>';

					$i++;
				}
				$rs2->db_close();
				$rs->db_next();
			}

			$return.='</ul>';
			$return.='</div>';

		}
		$rs->db_close();

		$return.='</div>';
		$return.='<div class="clear"></div>';
		$return.='</div>';
		$return.='</div>';
		$return.='</header>';
	}
	return $return;
}
function menusub_topo_defaul($id_pai = 0, $classMenu = '')
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url, $site_lang, $template;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new Dataobj;
	$rs2 = new Dataobj;
	$return = '';
	$page_id = intval($_GET['page_id']);
	$sql = 'SELECT ID, URL_Link, class FROM menus_links WHERE ID_ORG="'.$id_pai.'" AND Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		if($classMenu != '') $return.=' <ul '.$attr.' class="'.$classMenu.'">';
		else $return.=' <ul '.$attr.' class="dropdown">';
		while (!$rs->EOF())
		{
			$IDb = $rs->db_data['ID'];
			$raw_link = $rs->db_data['URL_Link'];
			$raw_link = explode('=',$raw_link);
			$link_id = $raw_link[1];
			$URL_Link = seo($rs->db_data['URL_Link']);
			$URL_Link = str_replace($site_http_url, '', $URL_Link);
			$classMenu = $rs->db_data['class'];
			if($classMenu == '') $classMenu = 'inner_dropdown';
			$active = '';
			if($link_id == $page_id) $active = 'class="active"';
			$condicao = "where ID_PAI=$IDb and lang='$site_lang'";
			$sql = "Select URL_Nome2 from menus_links_lang $condicao";
			$rs2->db_open($db_link, $DB_name, $sql);
			if (!$rs2->EOF())
			{
				$nomeb = $rs2->db_data["URL_Nome2"];
				$return2 = menusub_topo_defaul($IDb, $classMenu);
				if ($return2)
				{
                    if($URL_Link!=''){$return.='<li '.$active.'><a href="'.$URL_Link.'">'.$nomeb.'</a>'.$return2;}
					else{$return.='<li '.$active.'><a href="#">'.$nomeb.'</a>'.$return2;}
				}
				else
				{
					$return.='<li '.$active.'><a href="'.$URL_Link.'">'.$nomeb.'</a>';
				}
			}
			$rs2->db_close();
			$rs->db_next();
		}
		$return.='</ul>';
	}
	$rs->db_close();
	return $return;
}
function verifica_filhos_vcore($ID_ORG = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url, $site_lang;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs2 = new Dataobj;
	$sql = "Select * from menus_links INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI = menus_links.ID where ID_ORG=$ID_ORG and Lang='$site_lang'";
	$rs2->db_open($db_link, $DB_name, $sql);
	$x = 0;
	while (!$rs2->EOF())
	{
		$IDb = $rs2->db_data["ID"];
		$URL_Link = seo($rs2->db_data['URL_Link']);
		$URL_Link = str_replace($site_http_url, '', $URL_Link);

		$request = substr($_SERVER['REQUEST_URI'], strrpos($_SERVER['REQUEST_URI'], '/'));

		if ($URL_Link != '' && substr_count($request, $URL_Link))
		{
			return 1;
		}
		else
		{
			$x += verifica_filhos_vcore($IDb);
		}
		$rs2->db_next();
	}
	$rs2->db_close();

	return $x;
}

function media_social($tipo,$ret =1){

    $URL_Link = '';
    $nome = '';

    global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url, $site_lang;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs2 = new Dataobj;

    $sql = "SELECT m.ID, m.img_ext, m.catalogo_id, m.tipo_catalogo, m.TipoMenu, m.URL_Link, m.menu_icon_fa, ml.texto_icon_fa, ml.URL_Nome2
            FROM menus_links m 
            INNER JOIN menus_links_lang ml ON ml.ID_PAI=m.ID 
            WHERE m.TipoMenu =\"$tipo\" 
            AND m.Visivel=1 
            AND ml.Lang=\"$site_lang\"  
            AND (Logado=1 OR Logado=0)";
   // die($sql);
    $rs2->db_open($db_link, $DB_name, $sql);

    if(!$rs2->EOF())
    {
        $IDb = $rs2->db_data["ID"];
        $URL_Link = seo($rs2->db_data['URL_Link']);
        $nome = $rs2->db_data["URL_Nome2"];
    }
    $rs2->db_close();

    if($ret == 1 ){

        return  $URL_Link;

    }
    if($ret != 1 ) {

        return array('url' => $URL_Link, 'nome' => $nome);
    }

}