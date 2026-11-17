<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
	exit();
}
function get_noticias_title($page_id)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = "SELECT Cabecalho FROM pages inner join pages_lang on pages_lang.ID_PAI = pages.ID WHERE Lang='$site_lang' and ID_PAI='" . $page_id . "'";
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		return stripslashes($rs->db_data['Cabecalho']);
	}
	$rs->db_close();
}


function show_noticias_destaque()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 AND Familia_noticias=0 AND Visible=1 AND ((curdate() BETWEEN Data1 AND Data2) OR Permanente=1) AND Lang="' . $site_lang . '" ORDER BY Ordem ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{

		$return = '<div class="bottom_blocos"><h2>' . getLinguas('key_noticias_titulo',$GLOBALS['site_lang']) . '</h2><div class="vticker">';

		$return .= '<ul class="art">';

		while (!$rs->EOF())
		{
			$C = stripslashes($rs->db_data['Cabecalho']);
			$Texto2 = str_replace("\n", '<br>', stripslashes($rs->db_data['Texto2']));
			$Data_hora = explode(' ', $rs->db_data['Data1']);
			$Data = explode('-', $Data_hora[0]);
			$datatostr = $GLOBALS['monthName'][intval($Data[1])] . ' ' . $Data[2] . ', ' . $Data[0];
			$page_id = $rs->db_data['ID'];

			// echo '<h1 class="op_menu" onclick="window.open(\''.seo('pages.php?page_id='.$page_id).'\',\'_self\')"><span>'.$C.'</span></h1>';

			$return .= '<li  class="art">' . $Texto2 . '<br><span class="date">' . $datatostr . '</a>&nbsp;<a href="' . seo('pages.php?page_id=' . $page_id) . '" class="orange">' . getLinguas('key_vermais',$GLOBALS['site_lang']) . '</a></li>';

			$rs->db_next();
		}

		$return .= '</ul>';
		$return .= '</div>';
		$return .= '</div>';


	}
	$rs->db_close();


	return $return;
}

function get_noticias_media()
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$obj = new DataObj;
	$return = '';
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	if (isset($_GET['page_id']))
	{
		$sql = "SELECT *, pages.ID FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI = pages.ID WHERE Lang='" . $site_lang . "' AND ID_PAI='" . $_GET['page_id'] . "' AND Protected=0";
		$obj->db_open($db_link, $DB_name, $sql);
		if (!$obj->EOF())
		{
			$page_id = $obj->db_data['ID'];
			$Destaque_text = $obj->db_data['Destaque_text']; // text news
			$video_id = $obj->db_data['video_id'];
			if ($video_id > 0)
			{
				$rs = new DataObj;
				$sql = "Select video_code from video_menager_link WHERE video_id=$video_id";
				$rs->db_open($db_link, $DB_name, $sql);
				$video_code = $rs->db_data['video_code'];
				$rs->db_close();
				$script = '<iframe width="900" height="332" src="http://www.youtube.com/embed/' . $video_code . '?wmode=transparent&autoplay=' . $GLOBALS['video_autoplay'] . '&rel=' . $GLOBALS['video_rel'] . '" frameborder="0" allowfullscreen></iframe>';
			}
			else
			{
				$script = limpa_editor_lixo(stripslashes($obj->db_data["Texto3"]));
				$script = str_replace('http://youtu.be/', 'http://www.youtube.com/embed/', $script);
				preg_match('/<iframe.*width=\"(.*)\".*><\/iframe>/isU', $script, $matches);
				$width_ifr = ($matches[1]);
				preg_match('/<iframe.*height=\"(.*)\".*><\/iframe>/isU', $script, $matches2);
				$height_ifr = ($matches2[1]);
				$script = str_replace('width="' . $width_ifr . '"', 'width="900"', $script);
				$script = str_replace('height="' . $height_ifr . '"', 'height="332"', $script);
				preg_match('/<iframe.*src=\"(.*)\".*><\/iframe>/isU', $script, $matches3);
				if (substr_count($matches3[1], "?") && substr_count($matches3[1], "&"))
				{
					$pointer = "&";
				}
				if (substr_count($matches3[1], "?"))
				{
					$pointer = "&";
				}
				else
				{
					$pointer = "?";
				}
				$script = str_replace('src="' . $matches3[1] . '"', 'src="' . $matches3[1] . $pointer . 'wmode=transparent&autoplay=' . $GLOBALS['video_autoplay'] . '&rel=' . $GLOBALS['video_rel'] . '" ', $script);
			}
			$I1 = $obj->db_data['img_ext'];
			$img_file = "files/pages/" . $page_id . "_1." . $I1;
			$img_file_peq = "files/pages/" . $page_id . "_topo." . $I1;
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
			} //data/hora em que foi modificada/criada
			if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
			{
				if (redimencionaImagem($site_shell_url, $img_file, 900, 332, $img_file_peq))
				{
					cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 900, 332, 1, '#000000');
				}
			}
			if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$img = '';
			}
			else
			{
				$img = $m_http_url_images . 'pages/' . $page_id . '_topo.' . $I1 . '?d=' . uniqid();
			}
			if ($Destaque_text == 0 && $script)
			{
				$return = $script;
			}
			elseif ($img != '' && $I1)
			{
				$return = '<img src="' . $img . '" />';
			}
		}
		$obj->db_close();
	}

	return $return;
}

function show_menu_noticias()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 AND Familia_noticias=1 AND Visible=1 AND Lang="' . $site_lang . '" ORDER BY Ordem ASC';
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		$page_id = $rs->db_data['ID'];
		echo '<h1 class="op_menu" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')"><span>' . $C . '</span></h1>';
		$rs->db_next();
	}
	$rs->db_close();
}

function pnews_stats($page_tipo)
{
	if (isset($_GET['page_id']))
	{
		global $DB_host, $DB_user, $DB_pass, $DB_name;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
		$rs = new DataObj;
		$today = date('Y-m-d');
		$sql = 'SELECT pnews_id FROM pages_news WHERE page_id="' . $_GET['page_id'] . '" AND pnew_data="' . $today . '" AND pnew_tipo="' . $page_tipo . '"';
		$rs->db_open($db_link, $DB_name, $sql);
		if (!$rs->EOF())
		{
			$sql = 'UPDATE
						pages_news
					SET
						pnews_views=(pnews_views+1)
					WHERE
						pnews_id="' . $rs->db_data['pnews_id'] . '"
						AND
						page_id="' . $_GET['page_id'] . '"
						AND
						pnew_data="' . $today . '"
						AND
						pnews_tipo="' . $page_tipo . '"
				';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		else
		{
			$sql = 'INSERT INTO
						pages_news
						(
							page_id,
							pnew_data,
							pnews_views,
							pnews_tipo
						)
						VALUES
						(
							"' . $_GET['page_id'] . '",
							"' . $today . '",
							"1",
							"' . $page_tipo . '"
						)';
			$res = Runquery($db_link, $DB_name, $sql);
		}
		$rs->db_close();
	}
}

function pnews_popular()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$today = date("Y-m-d");
	$and = ' and pnews_tipo=2';
	$and2 = ' WHERE pnews_tipo=2';
	if (isset($GLOBALS['page_tipo']) && $GLOBALS['page_tipo'] && ($GLOBALS['page_tipo'] == 1 || $GLOBALS['page_tipo'] == 2))
	{
		$and = ' and pnews_tipo="' . $GLOBALS['page_tipo'] . '"';
		$and2 = ' WHERE pnews_tipo="' . $GLOBALS['page_tipo'] . '"';
	}
	$str = '';
	$str_today = '';
	$str_week = '';
	$str_month = '';
	$sql = 'SELECT * FROM pages_news WHERE pnew_data > DATE_SUB(NOW(), INTERVAL 1 DAY) ' . $and . ' GROUP BY page_id ORDER BY pnews_views DESC'; // TODAY
	$rs->db_open($db_link, $DB_name, $sql);
	if ($rs->EOF())
	{
		//Se não houver noticias de hoje vamos buscar as ultimas.
		$sql = 'SELECT * FROM pages_news ' . $and2 . ' GROUP BY page_id ORDER BY pnews_views DESC'; // TODAY
		$rs->db_close();
		$rs->db_open($db_link, $DB_name, $sql);
	}
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$news_title = get_noticias_title($rs->db_data['page_id']);
			if ($news_title)
			{
				$str_today .= '<div class="popular"><div class="bullet_popular"><img src="layout/' . PROJETO . '/images/bullet.png" alt="" /></div>';
				$str_today .= '<div class="news_popular">' . $news_title . '</div></div>';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();
	$sql = 'SELECT * FROM pages_news WHERE pnew_data > DATE_SUB(NOW(), INTERVAL 1 WEEK) ' . $and . ' GROUP BY page_id ORDER BY pnews_views DESC'; // WEEK
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$news_title = get_noticias_title($rs->db_data['page_id']);
			if ($news_title)
			{
				$str_week .= '<div class="popular"><div class="bullet_popular"><img src="layout/' . PROJETO . '/images/bullet.png" alt="" /></div>';
				$str_week .= '<div class="news_popular">' . $news_title . '</div></div>';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();
	$sql = 'SELECT * FROM pages_news WHERE pnew_data > DATE_SUB(NOW(), INTERVAL 1 MONTH) ' . $and . ' GROUP BY page_id ORDER BY pnews_views DESC'; // WEEK
	$rs->db_open($db_link, $DB_name, $sql);
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$news_title = get_noticias_title($rs->db_data['page_id']);
			if ($news_title)
			{
				$str_month .= '<div class="popular"><div class="bullet_popular"><img src="layout/' . PROJETO . '/images/bullet.png" alt="" /></div>';
				$str_month .= '<div class="news_popular">' . $news_title . '</div></div>';
			}
			$rs->db_next();
		}
	}
	$rs->db_close();
	$str .= '<div class="floated">';
	$str .= '<h1 class="t_mostpopular_' . $GLOBALS['site_lang'] . '"><span>MOST POPULAR</span></h1>';
	$str .= '
			<div class="mostpopular" id="tabs" style="height:198px">
			<div class="tab" id="tab1" onclick="most_popular(\'1\')"><span>Today</span></div>
			<div class="tab" id="tab2" onclick="most_popular(\'2\')"><span>Week</span></div>
			<div class="tab" id="tab3" onclick="most_popular(\'3\')"><span>Month</span></div>
			<div class="tabslash" id="tabs-1"><div class="scroll-pane"><p>' . $str_today . '</p></div></div>
			<div class="tabslash" id="tabs-2"><div class="scroll-pane2"><p>' . $str_week . '</p></div></div>
			<div class="tabslash" id="tabs-3"><div class="scroll-pane3"><p>' . $str_month . '</p></div></div>
			</div>';
	$str .= '</div>';
	$rs->db_close();

	return $str;
}

function show_fam_lista_noticias($id_fam = 0)
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = "SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' and ID_ORG=$id_fam order by Data1 Desc, Ordem Asc, ID Desc";
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		if ($C)
		{
			$C = strlen($C) > 30 ? substr($C, 0, 27) . " ..." : $C;
		}
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		if ($T2)
		{
			$T2 = strlen($T2) > 115 ? substr($T2, 0, 112) . " ..." : $T2;
		}
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = $rs->db_data['Data1'];
		$video_id = $rs->db_data['video_id'];
		$existe = 0;
		$obj2 = new DataObj;
		$sql = "SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
		$obj2->db_open($db_link, $DB_name, $sql);
		if (!$obj2->EOF())
		{
			$existe = 1;
		}
		$obj2->db_close();
		if ($video_id > 0 && $existe == 1)
		{
			$img_file = "files/videos/" . $video_id . "_1.png";
			$img_file_peq = "files/videos/" . $video_id . "_2.png";
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
			} //data/hora em que foi modificada/criada
			if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
			{
				if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq))
				{
					cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
				}
			}
			if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$img = '';
			}
			else
			{
				$img = $m_http_url_images . 'videos/' . $video_id . '_2.png?d=' . uniqid();
			}
		}
		else
		{
			$img_file = "files/pages/" . $page_id . "_1." . $I1;
			$img_file_peq = "files/pages/" . $page_id . "_2." . $I1;
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
			} //data/hora em que foi modificada/criada
			if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
			{
				if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq))
				{
					cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
				}
			}
			if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$img = $GLOBALS['site_http_url'] . 'layout/default/images/no_photo.png';
			}
			else
			{
				$img = $m_http_url_images . 'pages/' . $page_id . '_2.' . $I1 . '?d=' . uniqid();
			}
		}
		$str .= '<div class="noticia" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">
				<div class="noticiatext">
					<div class="hide-pic"><img src="' . $img . '" alt="" /></div>
					<div class="noticiatitulo">' . $C . '</div>
					<div class="noticiadate">' . $data1 . '</div>
					<div class="noticiaresumo">' . $T2 . '</div>
				</div>
			</div>';
		$rs->db_next();
		$c++;
	}
	$rs->db_close();
	if ($str)
	{
		if ($id_fam == 0)
		{
			$str .= '<div class="c"></div><div class="noticias_more">' . getLinguas('key_more',$GLOBALS['site_lang']) . '...</div>';
		}
	}
	echo $str;
}

function show_all_noticias($param = '',$tipo=1)
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	$pagina = filter_input(INPUT_GET, 'pagina', FILTER_SANITIZE_NUMBER_INT);
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images, $videos_page_id;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$tip = "";
	$str = '';
	$return='';
	if ($param == 'videos')
	{
		$tip = " and Texto3<>''";
	}
	$porpagina = 4;

	$and = '';
	if($videos_page_id > 0) $and =" AND (pages.ID_ORG!=$videos_page_id AND pages.ID!=$videos_page_id) ";
	$sql = "SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) $and and Familia_noticias=0 " . $tip . " and Lang='$site_lang' order by Data1 Desc, Ordem Asc, ID Desc";
	$rs2->db_open($db_link, $DB_name, $sql);
	$total = $rs2->db_rows;
	$rs2->db_close();
	$n_pags = ceil($total / $porpagina); // nº de paginas
	if (!$pagina)
	{
		$pagina = '1';
	}
	$inicio = $pagina - 1;
	$inicio = $inicio * $porpagina;
	$sql2 = $sql . " Limit $inicio, $porpagina";
	if ($n_pags > 1)
	{

		//echo '<button title="2" class="product_button" name="2" type="button">2</button></div>';
		$return = '<div class="paginacao">';
		if ($pagina > 6)
		{
			$p_seo = seo('pages.php?page_id=' . $GLOBALS['all_news_page_id']);
			if (substr_count($p_seo, '?'))
			{
				$ash = '&pagina=1';
			}
			else
			{
				$ash = '?pagina=1';
			}
			$pgn = 'href="' . $p_seo . $ash . '"';
			$return .= '<a class="product_button" ' . $pgn . '>1</a>';
		}
		for ($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
		{
			$p_seo = seo('pages.php?page_id=' . $GLOBALS['all_news_page_id']);
			if (substr_count($p_seo, '?'))
			{
				$ash = '&pagina=' . $pi;
			}
			else
			{
				$ash = '?pagina=' . $pi;
			}
			$pgn = 'href="' . $p_seo . $ash . '"';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<a class="product_button_selected" ' . $pgn . '>' . sprintf(($pi)) . '</a>' : '<a class="product_button" ' . $pgn . '>' . sprintf(($pi)) . '</a>' : '';
		}
		if ($n_pags != $pagina && $pagina + 6 < $n_pags)
		{
			$p_seo = seo('pages.php?page_id=' . $GLOBALS['all_news_page_id']);
			if (substr_count($p_seo, '?'))
			{
				$ash = '&pagina=' . $n_pags;
			}
			else
			{
				$ash = '?pagina=' . $n_pags;
			}
			$pgn = 'href="' . $p_seo . $ash . '"';
			$return .= '<a title="Próximo" class="product_button" ' . $pgn . '><img border="0" src="' . $GLOBALS['site_http_url'] . 'layout/default/filtros/foward.png" alt=""></a>';
		}
		$return .= '</div><div class="c"></div>';
	}
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql2);
	$str .= '<div class="row">';
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = $rs->db_data['Data1'];

		if(mb_strlen($T2) > 250){
			$T2 = substr($T2, 0 , 250).'... <span class="lermais">(ler mais)</span>';
		}

		/*if ($GLOBALS['site_lang'] == 'pt')
		{
			setlocale(LC_TIME, 'pt_PT.utf8');
		}
		else
		{
			setlocale(LC_TIME, 'en_EN.utf8');
		}*/
		$data1 = strftime("%d/%B/%Y", strtotime($data1));


		$video_id = $rs->db_data['video_id'];
		$existe = 0;
		/*$obj2 = new DataObj;
		$sql = "SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
		$obj2->db_open($db_link, $DB_name, $sql);
		if (!$obj2->EOF())
		{
			$existe = 1;
		}
		$obj2->db_close();*/
		if ($video_id > 0 && $existe == 1)
		{
			$img_file = "files/videos/" . $video_id . "_1.png";
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (!stream_resolve_include_path($site_shell_url . $img_file))
			{
				$img = '';
			}
			$img = $m_http_url_images . 'videos/' . $video_id . '_1.png?d=' . uniqid();
		}
		else
		{
			$img_file = "files/pages/" . $page_id . "_1." . $I1;
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (!stream_resolve_include_path($site_shell_url . $img_file))
			{
				$img = $GLOBALS['site_http_url'] . 'layout/default/images/no_photo2.png';
			}
			else
			{
				$img = $m_http_url_images . 'pages/' . $page_id . '_1.' . $I1 . '?d=' . uniqid();
			}
		}
		if($tipo==1){
			$str .= '<a href="' . seo('pages.php?page_id=' . $page_id) . '" class="news">
						<div class="col-md-4"><img style="width:95%" src="' . $img . '" alt="" /></div>
						<div class="col-md-8">
							<div class="noticia_titulo">' . $C . '</div>
							<div class="noticia_resumo">' . $T2 . '</div>
							<div class="noticia_data">' . $data1 . '</div>
						</div>
						<div class="c"></div>
					</a>';
				}

		if($tipo==2){

				//$str .= '<div class="row">';
			$str .= '<div class="col-md-6">';
			$str .= '<a href="' . seo('pages.php?page_id=' . $page_id) . '" class="news">
						<div class="col-md-12">
							<div class="noticia_titulo">' . $C . '</div>
							<div class="noticia_resumo">' . $T2 . '</div>
						</div>
						<div class="col-md-12"><img style="width:100%" src="' . $img . '" alt="" /></div>
						<div class="col-md-12">
							<div class="divider-10"></div>


						</div>
						<div class="c"></div>
					</a>';
			$str .= '</div>';
			//$str .= '</div>';
		}


		$rs->db_next();
		$c++;
	}
	$str .= '</div>';
	$rs->db_close();
	echo '<div class="news_wrapper">' . $str . $return . '</div>';
}
function show_lista_blog()
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	$str = '';
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = "SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=4 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' order by Data1 Desc, Ordem Asc, ID Desc limit 3";
	$str = '<div class="blogbox_white">';
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		if ($T2)
		{
			$T2 = strlen($T2) > 175 ? substr($T2, 0, 172) . " ..." : $T2;
		}
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = $rs->db_data['Data1'];
		$img_file = "files/pages/" . $page_id . "_1." . $I1;
		$img_file_peq = "files/pages/" . $page_id . "_2." . $I1;
		if (stream_resolve_include_path($site_shell_url . $img_file))
		{
			$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
		} // data/hora em que foi modificada/criada
		if (stream_resolve_include_path($site_shell_url . $img_file_peq))
		{
			$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
		} //data/hora em que foi modificada/criada
		if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
		{
			if (redimencionaImagem($site_shell_url, $img_file, 190, 100, $img_file_peq))
			{
				cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 175, 82);
			}
		}
		if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
		{
			$img = $GLOBALS['site_http_url'] . 'layout/default/images/no_photo4.png';
		}
		else
		{
			$img = $GLOBALS['site_http_url'] . $img_file_peq . '?d=' . dumpcache;
		}
		if ($c > 0)
		{
			$str .= '<div class="blog-sep"></div>';
		}
		$str .= '<div class="blog" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">
				<div class="blogtext">
					<div class="blogtitulo">' . $C . '</div>
					<div class="hide-picblog"><img src="' . $img . '" alt="" /></div>
					<div class="blogresumo">' . $T2 . '</div>
				</div>
			</div>';
		$rs->db_next();
		$c++;
	}
	$rs->db_close();
	if ($str)
	{
		$str .= '<div class="c"></div><div class="blog_more" onclick="window.open(\'' . seo('pages.php?page_id=' . $GLOBALS['all_blogs_page_id']) . '\',\'_self\')">ALL BLOG</div>';
	}
	$str .= '</div>';
	echo $str;
}

function show_lista_blog_all()
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	$str = '';
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = "SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=4 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' order by Data1 Desc, Ordem Asc, ID Desc";
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		if ($C)
		{
			$C = strlen($C) > 45 ? substr($C, 0, 42) : $C;
		}
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		if ($T2)
		{
			$T2 = strlen($T2) > 175 ? substr($T2, 0, 172) . "..." : $T2;
		}
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = $rs->db_data['Data1'];
		$img_file = "files/pages/" . $page_id . "_1." . $I1;
		$img_file_peq = "files/pages/" . $page_id . "_2." . $I1;
		if (stream_resolve_include_path($site_shell_url . $img_file))
		{
			$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
		} // data/hora em que foi modificada/criada
		if (stream_resolve_include_path($site_shell_url . $img_file_peq))
		{
			$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
		} //data/hora em que foi modificada/criada
		if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
		{
			if (redimencionaImagem($site_shell_url, $img_file, 190, 100, $img_file_peq))
			{
				cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 175, 82);
			}
		}
		if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
		{
			$img = $GLOBALS['site_http_url'] . 'layout/default/images/no_photo4.png';
		}
		else
		{
			$img = $GLOBALS['site_http_url'] . $img_file_peq . '?d=' . dumpcache;
		}
		if ($c > 0)
		{
			$str .= '<div class="blog-sep"></div>';
		}
		$str .= '
			 <div class="blog" style="margin-bottom:20px" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">
				<div class="blogtext">
					<div class="blogtitulo">' . $C . '</div>
					<div class="hide-picblog"><img src="' . $img . '" alt="" /></div>
					<div class="blogresumo">' . $T2 . '</div>
				</div>
			</div>';
		$rs->db_next();
		$c++;
	}
	$rs->db_close();
	echo $str;
}

function show_fam_lista_noticias___($id_fam)
{
	global $site_shell_url, $imagem_fundo2;
	$time_expire = time(); // Expire Time (1 hour)
	$cache_filename = $site_shell_url . 'files/tmp/show_fam_lista_noticias_' . $id_fam . '.txt';
	$str = '';
	$ids = array();
	?>
	<script language="JavaScript" type="text/JavaScript">
		function showHide2(theid) {
			if (document.getElementById) {
				var div1 = document.getElementById("div1" + theid);
				var div2 = document.getElementById("div2" + theid);
				if (div1.style.display == 'block') {
					div1.style.display = "none";
					div2.style.display = "block";
				} else {
					div1.style.display = "block";
					div2.style.display = "none";
				}
			}
		}
	</script>
	<?php
	if (stream_resolve_include_path($cache_filename) && filectime($cache_filename) > $time_expire)
	{
		$str = file_get_contents($cache_filename);
	}
	else
	{
		global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
		$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
		$rs = new DataObj;
		$sql = "SELECT pages.*,Cabecalho,Texto,Texto2,Texto3,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Visible=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' and ID_ORG=$id_fam order by Data1 Desc, Ordem Asc, ID Desc";
		$rs->db_open($db_link, $DB_name, $sql);
		$c = 0;
		while (!$rs->EOF())
		{
			$C = stripslashes($rs->db_data['Cabecalho']);
			$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
			$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
			$T1 = limpa_editor_lixo(stripslashes($rs->db_data['Texto']));
			$page_id = $rs->db_data['ID'];
			$I1 = $rs->db_data['img_ext'];
			$data1 = $rs->db_data['Data1'];
			$img_file = "files/pages/" . $page_id . "_1." . $I1;
			$img_file_peq = "files/pages/" . $page_id . "_2." . $I1;
			$ids[$c] = $page_id;
			$TimeStamp = mktime(0, 0, 0, (int)substr($data1, 5, 2), (int)substr($data1, 8, 2), (int)substr($data1, 0, 4));
			$data1 = date('F j, Y', $TimeStamp);
			if ($I1 != "" || $T3)
			{
				$img_nova = "/files/pages/" . $page_id . "_1c." . $I1;
				gera_image("/files/pages/" . $page_id . "_1." . $I1, $img_nova, 200, 200);
				$str .= '<div class="banner">
					<div class="separador"></div>';
				if ($I1 != "")
				{
					$str .= '<a href="' . seo('pages.php?page_id=' . $page_id) . '"><img class="fade" src="' . $m_http_url_images . 'pages/' . $page_id . '_1c.' . $I1 . '?d=' . uniqid() . '" alt="" border=0></a>';
				}
				else
				{
					$str .= $T3;
				}
				$str .= '<div class="separador"></div></div>';
			}
			$str .= '<div style="height:15px"></div>
					<div style="margin-left:15px">
						<div class="texto2_paginas none" id="div1' . $page_id . '">
							<div class="texto2_paginas" style="font-weight:bold"><a onmouseover="this.style.cursor=\'pointer\'" onClick="showHide2(\'' . $page_id . '\');">' . $C . '&nbsp;&nbsp;<font style="color:#569733">></font></a></div>
						</div>
						<div class="texto2_paginas" style="display:block;" id="div2' . $page_id . '">
							<div style="font-weight:bold"><table cellpadding="0" cellspacing="0" border="0"><tr><td class="texto2_paginas"><a onmouseover="this.style.cursor=\'pointer\'" onClick="showHide2(\'' . $page_id . '\');">' . $C . '&nbsp;&nbsp;<font style="color:#569733">></font></a></td></tr></table></div>
							<table cellpadding="0" cellspacing="0" border="0">';
			if ($id_fam == 14)
			{
				$str .= '<tr>
								<td>' . $T2 . '</td>
							</tr>
							<tr>
								<td height=10px></td>
							</tr>
							<tr>
								<td><a class="saberm3" href="' . seo('pages.php?page_id=' . $page_id) . '">saber mais+</a></td>
							</tr>';
			}
			else
			{
				$str .= '<tr>
								<td>' . $T1 . '</td>
							</tr>';
			}
			$str .= '</table></div></div>';
			$rs->db_next();
			$c++;
		}
		$rs->db_close();
		file_put_contents($cache_filename, $str);
	}
	echo $str;
	foreach ($ids as $value)
	{
		echo '<script language="JavaScript" type="text/JavaScript">';
		echo "showHide2('" . $value . "')";
		echo '</script>';
	}
}

function show_lista_noticias_last($limit = 8, $stl = '')
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	$time_expire = time() + 1 * 60 * 1; // Expire Time (1 hour)
	$cache_filename = $site_shell_url . 'files/tmp/show_lista_noticias_last_' . $from . '.txt';
	$str = '';
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql_all = "SELECT pages.*,Cabecalho,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Destaque=1 and ID_ORG<>14 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' order by Data1 Desc";
	$sql = "SELECT pages.*,Cabecalho,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Destaque=1 and ID_ORG<>14 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' order by Data1 Desc limit 8";
	$rs->db_open($db_link, $DB_name, $sql_all);
	if (!$rs->EOF())
	{
		$total = $rs->db_rows;
	}
	$rs->db_close();
	$str .= '<div class="destaques destaques-noticias" style="margin-bottom:5px;float:left' . $stl . '"><div class="destaques-titulo-noticias">Notícias<span></span></div><div class="destaques-noticias-lista" id="news-container"><ul>';
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		if ($C)
		{
			$C = strlen($C) > 60 ? substr($C, 0, 57) . " ..." : $C;
		}
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		if ($T2)
		{
			$T2 = strlen($T2) > 60 ? substr($T2, 0, 57) . " ..." : $T2;
		}
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = explode(' ', $rs->db_data['Data1']);
		$data1 = $data1[0];
		$video_id = $rs->db_data['video_id'];
		$existe = 0;
		$obj2 = new DataObj;
		$sql = "SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
		$obj2->db_open($db_link, $DB_name, $sql);
		if (!$obj2->EOF())
		{
			$existe = 1;
		}
		$obj2->db_close();
		$str .= '<li>
			<div class="destaques-noticias-item">
				<div class="destaques-nome">' . $C . '</div>
				<div class="destaques-data">' . $data1 . '</div>
				<div class="destaques-texto">' . $T2 . ' <a href="' . seo('pages.php?page_id=' . $page_id) . '"><img src="layout/icon-ver-mais.jpg" alt="" /></a></div>
			</div>
		</li>';
		$rs->db_next();
		$c++;
	}
	$rs->db_close();
	$str .= '</ul></div><div class="c"></div></div>';

	return $str;
}

function show_lista_noticias_last_text($limit = 8)
{
	global $site_shell_url, $from;
	if (!$from)
	{
		$from = 0;
	}
	$str = '';
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
	$qtd = 3;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = "SELECT pages.*,Cabecalho,Texto2,Texto3,Lang,video_id FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Destaque_text=1 and ((curdate() between Data1 and Data2) OR Permanente=1) and Lang='$site_lang' order by Data1 Desc limit 3";
	$c = 0;
	$rs->db_open($db_link, $DB_name, $sql);
	$str .= '<h1 class="t_news_title_' . $GLOBALS['site_lang'] . '"><span>Text News</span></h1>';
	while (!$rs->EOF())
	{
		$C = stripslashes($rs->db_data['Cabecalho']);
		if ($C)
		{
			$C2 = strlen($C) > 45 ? substr($C, 0, 42) . " ..." : $C;
		}
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		if ($T2)
		{
			$T2 = strlen($T2) > 285 ? substr($T2, 0, 283) . " ..." : $T2;
		}
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
		$page_id = $rs->db_data['ID'];
		$I1 = $rs->db_data['img_ext'];
		$data1 = $rs->db_data['Data1'];
		$video_id = $rs->db_data['video_id'];
		$existe = 0;
		$obj2 = new DataObj;
		$sql = "SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
		$obj2->db_open($db_link, $DB_name, $sql);
		if (!$obj2->EOF())
		{
			$existe = 1;
		}
		$obj2->db_close();
		if ($video_id > 0 && $existe == 1)
		{
			$img_file = "files/videos/" . $video_id . "_1.png";
			$img_file_peq = "files/videos/" . $video_id . "_text.png";
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
			} //data/hora em que foi modificada/criada
			if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
			{
				if (redimencionaImagem($site_shell_url, $img_file, 60, 60, $img_file_peq))
				{
					cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 55, 56);
				}
			}
			if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$img = '';
			}
			else
			{
				$img = $m_http_url_images . 'videos/' . $video_id . '_text.png?d=' . uniqid();
			}
		}
		else
		{
			$img_file = "files/pages/" . $page_id . "_1." . $I1;
			$img_file_peq = "files/pages/" . $page_id . "_text." . $I1;
			if (stream_resolve_include_path($site_shell_url . $img_file))
			{
				$original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
			} // data/hora em que foi modificada/criada
			if (stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
			} //data/hora em que foi modificada/criada
			if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file))
			{
				if (redimencionaImagem($site_shell_url, $img_file, 60, 60, $img_file_peq))
				{
					cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 55, 56);
				}
			}
			if (!stream_resolve_include_path($site_shell_url . $img_file_peq))
			{
				$img = $GLOBALS['site_http_url'] . 'layout/text_news.png';
			}
			else
			{
				$img = $m_http_url_images . 'pages/' . $page_id . '_text.' . $I1 . '?d=' . uniqid();
			}
		}
		$str .= '<div class="noticia2">
			<div class="noticiatext2">
				<div class="hide-pic2" style="cursor:pointer" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')"><img src="' . $img . '" alt="" /></div>
				<div class="noticiatitulo2" style="cursor:pointer" title="' . $C . '" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">' . $C2 . '</div>
				<div class="noticiadate2"  style="cursor:pointer" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">' . $data1 . '</div>
				<div class="noticiaresumo2"  style="cursor:pointer" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">' . $T2 . '</div>
				<div style="height:10px;width:100%;float:left"></div>
				<div style="position:absolute; bottom:0px; right:0px; border:#000 0px solid">
					<div class="noticias_more2" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">' . $GLOBALS["key_ler"][$GLOBALS["site_lang"]] . '</div>
					<div class="addthis_toolbox addthis_default_style" style="float:right;width:60px;">
					<a class="addthis_button_facebook" addthis:url="http://www.memogadget.com/"></a>
					<a class="addthis_button_twitter" addthis:url="http://www.memogadget.com/"></a>
					<a class="addthis_button_email" addthis:url="http://www.memogadget.com/"></a>
					</div>
				</div>
			</div>
		</div>';
		$rs->db_next();
		$c++;
	}
	$rs->db_close();

	return $str;
}

function show_paginacao($total, $qtd)
{
	$str = "";
	global $from;
	if ($total > $qtd)
	{
		$pageURL = get_uri();
		if (substr_count($pageURL, "&from"))
		{
			$pageURL = substr($pageURL, 0, strpos($pageURL, "&from"));
		}
		if (substr_count($pageURL, "?from"))
		{
			$pageURL = substr($pageURL, 0, strpos($pageURL, "?from"));
		}
		if (substr_count($pageURL, "?") > 0)
		{
			$op = "&";
		}
		else
		{
			$op = "?";
		}
		$i = 0;
		$str .= '<table width=100% border=0 cellspacing=0 cellpadding=10><tr><td height=20 class=numbers>page ';
		while ($i < ($total / $qtd))
		{
			if ($i > 0)
			{
				$str .= ' <img src="layout/ponto.png"> ';
			}
			if ($from == ($i * $qtd))
			{
				$str .= ($i + 1);
			}
			else
			{
				$str .= '<a class="numbers" href="' . $pageURL . $op . 'from=' . ($i * $qtd) . '">' . ($i + 1) . '</a>';
			}
			$i++;
		}
		$str .= '</td></tr></table>';
	}

	return $str;
}

function show_lista_noticias($id_org=0, $tipo='press')
{
	global $page_id;
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_shell_url,$m_http_url_images;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	if(!$_GET['per_page']) $per_page = 12;
	else $per_page = filter_input(INPUT_GET, 'per_page' , FILTER_SANITIZE_NUMBER_INT);
	if(!$_GET['curr_page']) $curr_page = 1;
	else $curr_page = filter_input(INPUT_GET, 'curr_page' , FILTER_SANITIZE_NUMBER_INT);


	$sql='SELECT COUNT(pages.ID) as Total FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Lang="'.$site_lang.'" and ID_ORG="'.$id_org.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	$total = $rs->db_data['Total'];
	$rs->db_close();

	echo '<link rel="stylesheet" href="layout/menu/menu_drop.css" type="text/css" media="all" />
	<style>
		a { text-decoration:none; font-size: 14px; color:#000; font-family:\'Open sans\',arial; text-transform: uppercase; }
		a:hover, a.active{ outline: 0; color: #D7D7D7;}
		
	</style>';


	// $last_page = ceil($total / $per_page);
	// if($curr_page>$last_page) $curr_page=1;

	// $per_page_html = createPerPageSelect($per_page,$total,$per_page);
	// $pagination_html = createPagination($curr_page,$total,$per_page,1,seo('pages.php?page_id='.$page_id).'?per_page='.$per_page.'&amp;curr_page=');



	$videos = 0;

	echo '<div class="articles_wrap row">';
	// echo '<div class="menu_wrapper col-md-3 col-sm-3 col-xs-12" style="padding:0">';
	// echo show_lista_noticias_menu($id_org);
	// echo '</div>';
	echo '<div class="articles_content col-xs-12"><div class="row">';
	// $sql='SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Lang="'.$site_lang.'" and ID_ORG="'.$id_org.'" order by Data1 Desc LIMIT '.(($curr_page - 1) * $per_page).','.$per_page;
	$Resumo = '';
	$sql='SELECT Texto2 FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 AND Lang="'.$site_lang.'" and pages.ID="'.$id_org.'"';
	
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{	
		$Resumo = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
	}
	$rs->db_close();
	
	$sql='SELECT pages.*,Cabecalho,Texto,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 and Lang="'.$site_lang.'" and ID_ORG="'.$id_org.'" order by Data1 Desc';
	$rs->db_open($db_link,$DB_name,$sql);
	$total = $rs->db_rows;
	$a = 0;
	while(!$rs->EOF())
	{

		$id_org = $rs->db_data['ID_ORG'];
		$C=stripslashes($rs->db_data['Cabecalho']);
		$T=limpa_editor_lixo(stripslashes($rs->db_data['Texto']));
		$T2=limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		// $T2 = (strlen($T2) > 230) ? substr($T2,0,230).'...' : $T2;

		$page_id=$rs->db_data['ID'];
		$I1=$rs->db_data['img_ext'];
		$External=$rs->db_data['External'];
		$img_file='files/pages/'.$page_id.'_1.'.$I1;

		if($I1!='' && file_exists($site_shell_url.$img_file))
		{
			$link_img = $img_file;
			$img_file = '<img src="'.$img_file.'"/>';
		}
		else $img_file='';

	
		if($tipo=='press')
		{
		
			echo '
			<div class="col-md-12 col-sm-12 col-xs-12" style="padding-bottom:20px">
			<div class="row">
				<div class="col-md-5"><div class="img-wrapper"><a href="'.seo('pages.php?page_id='.$page_id).'">'.$img_file.'</a></div></div>
				<div class="col-md-7">
					<h3 class="titulo2">'.$C.'</h3>
					<div class="texto">'.$T2.'</div>
					<div class="ver"><a href="'.seo('pages.php?page_id='.$page_id).'">'.$GLOBALS['key_vermais'][$GLOBALS['site_lang']].' ></a></div>
				</div>
			</div>
			</div><div class="clear"></div>';

		}
		elseif($tipo=='faqs')
		{
			$a++;
			if($a==1) echo '<div id="faqs">'.($Resumo!='' ? '<div class="titulo1">'.$Resumo.'</div>' : '');
			echo '
			<div class="col-md-12 col-sm-12 col-xs-12" style="padding:0">
			<div class="row" style="margin:0;">				
				<div class="col-md-12" style="padding:0">
					<h3 class="titulo2">'.$C.' <i class=\'fas fa-angle-right\'></i></h3>
					<div class="texto">'.$T.'</div>
				</div>
			</div>
			</div><div class="clear"></div>';
			
			if($a==$total) echo '</div>';
			
		}
		elseif($tipo=='videos')
		{
			echo '
			<div class="col-md-4 col-sm-6 col-xs-12" style="height:200px;">
			<div class="row">
				<div class="col-md-12"><div class="img-wrapper"><a class="video" data-fancybox-type="iframe" href="'.$T2.'">'.$img_file.'</a></div></div>
				<div class="col-md-12">
					<h3 class="titulo2">'.$C.'</h3>
				</div>
			</div>
			</div>';

		}
		else
		{
			echo '
			<div class="col-md-3 col-sm-6 col-xs-12" style="max-height:135px;">
			<div class="row">
				<div class="col-md-12"><div class="img-wrapper"><a class="video" data-fancybox-type="iframe" href="'.$T2.'">'.$img_file.'</a></div></div>
			</div>
			</div>';
		
		}

		$rs->db_next();
	}


	echo '</div></div></div>';


	echo '
		<script>
			$(document).ready(function()
			{

			    $("a.player").click(function() {
			        $.fancybox({
		                \'type\' : \'iframe\',
				        // hide the related video suggestions and autoplay the video
				        \'href\' : this.href.replace(new RegExp(\'watch\\?v=\', \'i\'), \'embed/\') + \'?rel=0&autoplay=1\',
				        \'overlayShow\' : true,
				        \'centerOnScroll\' : true,
				        \'speedIn\' : 100,
				        \'speedOut\' : 50,
				        \'width\' : 640,
				        \'height\' : 480
					});
					return false;
	            });


			$(\'#faqs h3\').each(function() 
			{
				var tis = $(this), state = false, answer = tis.next(\'div\').slideUp();
				tis.click(function() 
				{
					state = !state;
					answer.slideToggle(state);
					tis.toggleClass(\'active\',state);
				});
			});

		    });
		</script>
		<script src="layout/menu/menu_drop.js"></script>
	</div>';

	$rs->db_close();
}

function show_novidades()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Destaque=1 AND Tipo=2 AND Visible=1 AND Lang="' . $site_lang . '" ORDER BY Data DESC, Ordem ASC, ID DESC  LIMIT 0,3';
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		echo '<table width="100%" height="10px" cellpadding="0" cellspacing=0 border=0><tr><td class="new_preto" valign="top"></td></tr></table>';
		$C = stripslashes($rs->db_data['Cabecalho']);
		$T = stripslashes($rs->db_data['texto1']);
		if (!$T)
		{
			$T = $C;
		}
		$page_id = $rs->db_data['ID'];
		echo '<table width="100%" cellpadding="0" cellspacing=0 border=0><tr><td class="novidades2" valign="top" style="font-weight:bold">&bull; ' . $T . '</td></tr>';
		$uri = $_SERVER['REQUEST_URI'];
		$uri = str_replace($site_http_url, '', $uri);
		if ($uri == 'pages.php?page_id=' . $page_id)
		{
			echo '<tr><td class="saberm2" valign="top">saber mais+</td></tr></table>';
		}
		else
		{
			echo '<tr><td class="new_preto" valign="top"><a class="saberm" href="pages.php?page_id=' . $page_id . '">saber mais+</a></td></tr></table>';
		}
		$rs->db_next();
	}
	$rs->db_close();
}

function show_last_news($num)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images, $site_http_url, $media_substituicao_noticias, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$sql = 'SELECT pages.*,Cabecalho,Texto2,Lang FROM pages INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID WHERE Tipo=2 AND Visible=1 AND Destaque=1 AND ((curdate() BETWEEN Data1 AND Data2) OR Permanente=1) AND Lang="' . $site_lang . '" ORDER BY Data DESC, Ordem ASC, ID DESC  LIMIT 0,' . $num;
	$rs->db_open($db_link, $DB_name, $sql);
	echo '<div class="newswrapper">';
	if (!$rs->EOF())
	{
		echo '<div class="headernews HvLt">' . $GLOBALS['key_noticias_titulo'][$site_lang] . '</div><ul class="newsul">';
		while (!$rs->EOF())
		{
			$C = stripslashes($rs->db_data['Cabecalho']);
			$T = $C;
			$page_id = $rs->db_data['ID'];
			$uri = seo('pages.php?page_id=' . $page_id);
			echo '<li class="newsli"><a class="cbyg" href="' . str_replace($site_http_url, '', $uri) . '">' . $T . '</a></li>';
			$rs->db_next();
		}
		echo '</ul>';
	}
	else
	{
		echo show_media($media_substituicao_noticias);
	}
	echo '</div><div class="c"></div>';
	$rs->db_close();
}

function getComentariosProduto($page_id)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$result = array();
	$pos = 0;
	$sql = 'SELECT * FROM comments WHERE ID_Pag=' . $page_id . ' AND Bloked=0 ORDER BY Date DESC, ID DESC';
	$rs->db_open($db_link, $DB_name, $sql);
	while (!$rs->EOF())
	{
		$result[$pos]['nome'] = $rs->db_data['nome'];
		$result[$pos]['pais'] = $rs->db_data['pais'];
		$result[$pos]['local'] = $rs->db_data['local'];
		$result[$pos]['Comment'] = $rs->db_data['Comment'];

		$pos++;
		$rs->db_next();
	}
	$rs->db_close();

	return $result;
}

function inserirNovoComentario()
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$comentario_nome = addslashes($_POST['comentario_nome']);
	$comentario_local = addslashes($_POST['comentario_local']);
	$comentario_pais = addslashes($_POST['comentario_pais']);
	$page_id = addslashes($_POST['idpage']);
	$texto = addslashes($_POST['comentario_mensagem']);
	$data = date("Y-m-d H:i:s");

	$sql = 'INSERT INTO comments(nome,local,pais,Date,ID_Pag,Comment,Bloked) VALUES ("' . $comentario_nome . '","' . $comentario_local . '","' . $comentario_pais . '","' . $data . '","' . $page_id . '","' . $texto . '",1)';
	$res = Runquery($db_link, $DB_name, $sql);

	$sql = 'INSERT INTO mensagens(Data,Assunto,Mensagem,ID_Cliente) VALUES ("' . $data . '","Novo Comentário","Novo Comentário, <a href=\'' . $site_http_url . 'admin/pages/comments.php?ID=' . $page_id . '\' target=\'_top\'>Ver Comentário</a>",0)';
	$res = Runquery($db_link, $DB_name, $sql);

	return $res;
}

function show_last_3_news($id_fam = 0)
{
    global $site_shell_url, $from;
    if (!$from) {
        $from = 0;
    }
    $str = '';
    $videos = 0;


    global $DB_host, $DB_user, $DB_pass, $DB_name, $site_lang, $site_shell_url, $m_http_url_images;
    $qtd = 3;
    $db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
    $rs = new DataObj;
    $rs2 = new DataObj;


	define('N_PAGINAS', 12);
    $sql = "
		SELECT
			pages.*,Date(pages.Data1) AS Data1,Cabecalho,Texto,Texto2,Texto3,Lang,video_id
		FROM
			pages
			INNER JOIN pages_lang ON pages_lang.ID_PAI=pages.ID
		WHERE
			$and Tipo=2
			and Visible=1
			and ((curdate() between Data1 and Data2) OR Permanente=1)
			AND YEAR(Data1) >= 2006
			AND YEAR(Data1) <= ".date('Y')."
			and Lang='$site_lang'
			and ID_ORG=$id_fam
		ORDER BY
			Data1 Desc,
			Ordem Asc, ID Desc";
    // var_dump($sql);

    $c = 0;
    $rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit 3';
	$rs->db_open($db_link,$DB_name,$sql2);


    /*echo '<div class="container" style="margin-left: -45px;">';*/

	$aa = 3;
	$return = '';
	if($n_pags > 1)
	{

		$url_pages = seo('pages.php?page_id=' . $GLOBALS['all_news_page_id']).'?p=1'.$urls.$filtro_ano;

		$return = '<div style="clear:both"></div><div style="text-align:center;margin:5px 0">';
		if($pagina > 2)
		{
			$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';
			$return .= '<div style="font-size: 12px;font-weight:bold;width:35px;height:35px;color:#edf4f9;text-align:center;cursor:pointer;padding-top:2px;border:#000 0px solid;display:inline-block;margin:2px;padding:2px;background:#e0e0e0;color:#000;line-height:28px" '.$pgn.'>1... </div>';
		}
		for($pi = ($pagina - 2); $pi < ($pagina + 3); $pi++)
		{
			$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
			$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<div style="font-size: 12px;font-weight:bold;width:35px;height:35px;color:#edf4f9;text-align:center;cursor:pointer;padding-top:2px;border:#000 0px solid;display:inline-block;margin:2px;padding:2px;background:#fa6c15;line-height:28px" '.$pgn.'>'.sprintf($pi).'</div>' : '<div style="font-size: 12px;font-weight:bold;width:35px;height:35px;text-align:center;cursor:pointer;padding-top:2px;border:#000 0px solid;display:inline-block;margin:2px;padding:2px;background:#e0e0e0;color:#000;line-height:28px" '.$pgn.'>'.sprintf($pi).'</div>' :'';
		}
		if($n_pags!=$pagina)
		{
			$pgn='onclick=window.open("'.$url_pages.'&pagina='.$n_pags.'","_self")';
			$return .= '<div style="font-size: 12px;font-weight:bold;width:35px;height:35px;color:#edf4f9;text-align:center;cursor:pointer;padding-top:2px;border:#000 0px solid;display:inline-block;margin:2px;padding:2px;background:#e0e0e0;color:#000;line-height:28px" '.$pgn.'>...'.sprintf($n_pags).'</div>';
		}
		$return .= '</div>';
		echo $return;
	}

	echo '<div class="row">';
    $rou = 0;
    while (!$rs->EOF())
	{
        $C = stripslashes($rs->db_data['Cabecalho']);
		// $C = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $C);
        if ($C) {
            //$C=strlen($C) > 30 ? substr($C, 0, 27) . " ..." : $C;
        }
        $T2 = limpa_editor_lixo(stripslashes($rs->db_data['Texto2']));
		// $T2 = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $T2);
        if ($T2) {
            $T2=strlen($T2) > 100 ? substr($T2, 0, 100) . " ..." : $T2;
        }
        $T3 = limpa_editor_lixo(stripslashes($rs->db_data['Texto3']));
        $page_id = $rs->db_data['ID'];
        $I1 = $rs->db_data['img_ext'];
        $data1 = $rs->db_data['Data1'];
        $video_id = $rs->db_data['video_id'];
        $existe = 0;
        $obj2 = new DataObj;
        $sql = "SELECT * FROM video_menager_link WHERE video_id='" . $video_id . "'";
        $obj2->db_open($db_link, $DB_name, $sql);
        if (!$obj2->EOF()) {
            $existe = 1;
        }
        $obj2->db_close();
        if ($video_id > 0 && $existe == 1) {
            $img_file = "files/videos/" . $video_id . "_1.png";
            $img_file_peq = "files/videos/" . $video_id . "_2.png";
            if (stream_resolve_include_path($site_shell_url . $img_file)) {
                $original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
            } // data/hora em que foi modificada/criada
            if (stream_resolve_include_path($site_shell_url . $img_file_peq)) {
                $peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
            } //data/hora em que foi modificada/criada
            if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file)) {
                if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq)) {
                    cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
                }
            }
            if (!stream_resolve_include_path($site_shell_url . $img_file_peq)) {
                $img = '';
            } else {
                $img = $m_http_url_images . 'videos/' . $video_id . '_2.png?d=' . uniqid();
            }
        } else {
            $img_file = "files/pages/" . $page_id . "_1." . $I1;
            $img_file_peq = "files/pages/" . $page_id . "_2." . $I1;
            $img_file_med2 = "files/pages/" . $page_id . "_1_med2." . $I1;
            if (stream_resolve_include_path($site_shell_url . $img_file)) {
                $original = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file));
            } // data/hora em que foi modificada/criada
            if (stream_resolve_include_path($site_shell_url . $img_file_peq)) {
                $peq = date('Y-m-d H:i:s', filemtime($site_shell_url . $img_file_peq));
            } //data/hora em que foi modificada/criada
            if ((!stream_resolve_include_path($site_shell_url . $img_file_peq) || $peq < $original) && stream_resolve_include_path($site_shell_url . $img_file)) {
                if (redimencionaImagem($site_shell_url, $img_file, 150, 110, $img_file_peq)) {
                    cropImage($site_shell_url . $img_file_peq, $site_shell_url . $img_file_peq, 142, 103);
                }
            }
            if (!stream_resolve_include_path($site_shell_url . $img_file_peq)) {
                $img = $GLOBALS['site_http_url'] . 'layout/no_photo.png';
            } else {
                $img = $m_http_url_images . 'pages/' . $page_id . '_1.' . $I1 . '?d=' . uniqid();
            }
        }

		geraimagens($img_file, $img_file_med2, 370, 260, array('ativo' => 1, 'w' => 370, 'h' => 260));

		if(stream_resolve_include_path($GLOBSLS['site_shell_url'].$img_file_med2)) $img2 = $GLOBSLS['site_http_url'].$img_file_med2;
		else $img2 = $img;

        //o que vou precisar
        // onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')"
        // <img src="' . $img . '" alt="" />
        // $data1
        // $C
        // $T2
		if($aa == $bb)
		{
			echo '</div><div class="row">';
			$bb=0;
		}
        echo '<div class="col-xs-12 col-sm-6 col-md-4" style="cursor: pointer;" onclick="window.open(\'' . seo('pages.php?page_id=' . $page_id) . '\',\'_self\')">
                <div class="">
                    <div class="news-imagem" style="background-image: url('.$img2.')">
                        <img style="width: 100%;visibility: hidden; max-height: 190px;" src="'.$img2.'" alt="" />
                    </div>
                    <div class="news-data">
                        <img src="layout/cal-data.png" style="width: 16px;margin-right:7px;margin-bottom:0px;">'.strftime('%B %d, %Y', strtotime($data1)).'
                    </div>
                    <div class="news-text">'.$C.'</div>
                </div>
              </div>';

            /*ISTO É PARA O GOOGLE ARTICLES*/
            $data_article = $rs->db_data['Data'];
            $data_mod = $rs->db_data['Data_modify'];
            ?>
            <div itemscope itemtype="http://schema.org/NewsArticle" style="display: none;">
              <meta itemscope itemprop="mainEntityOfPage"  itemType="https://schema.org/WebPage" itemid="https://google.com/article"/>
              <meta itemprop="datePublished" content="<?= $data_article?>"/>
              <meta itemprop="dateModified" content="<?= $data_mod?>"/>
              <h1 itemprop="headline"><?= $C ?></h1>
              <h3 itemprop="author" itemscope itemtype="https://schema.org/Person">
                By <span itemprop="name"><?= PROJETO?></span>
              </h3>
              <div itemprop="image" itemscope itemtype="https://schema.org/ImageObject">
                <img src="<?= $img?>"/>
                <meta itemprop="url" content="<?= $img?>">
                <meta itemprop="width" content="800">
                <meta itemprop="height" content="600">
              </div>
              <div itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
                <div itemprop="logo" itemscope itemtype="https://schema.org/ImageObject">
                  <img src="<?= $GLOBALS['site_http_url']?>files/modulos_config_site/1_1.png"/>
                  <meta itemprop="url" content="<?= $GLOBALS['site_http_url']?>files/modulos_config_site/1_1.png">
                  <meta itemprop="width" content="250">
                  <meta itemprop="height" content="250">
                </div>
                <meta itemprop="name" content="<?= PROJETO?>">
              </div>
            </div>
            <?php
            /*ISTO É PARA O GOOGLE ARTICLES*/
        $rou++;

		$bb++;
        $rs->db_next();
        $c++;
    }
    $rs->db_close();
	echo '</div><br>';
	echo $return;
    /*$str .= '</div>';*/
    //$str .= '</div>';
    echo $str;
}
?>