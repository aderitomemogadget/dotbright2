<?php
if (isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__)
{
	exit();
}
function show_search_pages()
{
	if (isset($_GET['pesquisa']))
	{
		$pesq = $_GET['pesquisa'];
	}
	else
	{
		$pesq = getLinguas('key_pesquisar',$GLOBALS['site_lang']);
	}

	$search_token = gerarCSFRToken(100, 'search_token');
	?>
	<script>function validarPesquisa() {
			if ($.trim($("#search_query_top").val()) != "") return true;
			else
			{
				Apprise("O campo de pesquisa encontra-se vazio.");
				return false;
			}
		}
	</script>
	<div id="search_block_top" class="clearfix">
	<form name="pesquisa" id="searchbox" action="<?php echo seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']) ?>" method="GET" onsubmit="return validarPesquisa()">
		<input type="hidden" name="token" value="<?php echo $search_token; ?>"/>
		<input class="search_query form-control"  id="search_query_top" placeholder="<?php echo getLinguas('key_pesquisar',$GLOBALS['site_lang']) ?>" type="text" value="<?php echo htmlspecialchars($pesq, ENT_QUOTES, "UTF-8"); ?>"  onfocus="if(this.value=='<?php echo getLinguas('key_pesquisar',$GLOBALS['site_lang']) ?>') this.value='';"  name="pesquisa" autocomplete="off"/>
		<button type="submit" name="submit_search" id="search_block_button" class="btn btn-default button-search"><span><?php echo getLinguas('key_pesquisar2')?></span></button>
	</form>

	</div>
	<div id="search_hinters" class="search_hinters"></div>
	<?php
}
function show_search()
{
	if (isset($_GET['pesquisa']))
	{
		$pesq = $_GET['pesquisa'];
	}
	else
	{
		$pesq = getLinguas('key_pesquisar',$GLOBALS['site_lang']);
	}

	$search_token = gerarCSFRToken(100, 'search_token');
	?>
	<script>function validarPesquisa() {
			if ($.trim($("#search_query_top").val()) != "") return true;
			else
			{
				Apprise("O campo de pesquisa encontra-se vazio.");
				return false;
			}
		}
	</script>
	<div id="search_block_top" class="clearfix">
	<form name="pesquisa" id="searchbox" action="<?php echo seo('pages.php?page_id='.$GLOBALS['pesquisa_page_id']) ?>" method="GET" onsubmit="return validarPesquisa()">
		<input type="hidden" name="token" value="<?php echo $search_token; ?>"/>
		<input class="search_query form-control"  id="search_query_top" placeholder="<?php echo getLinguas('key_pesquisar',$GLOBALS['site_lang']) ?>" type="text" value="<?php echo htmlspecialchars($pesq, ENT_QUOTES, "UTF-8"); ?>"  onfocus="if(this.value=='<?php echo getLinguas('key_pesquisar',$GLOBALS['site_lang']) ?>') this.value='';"  name="pesquisa" onkeyup="showHintsSearch();" autocomplete="off"/>
		<button type="submit" name="submit_search" id="search_block_button" class="btn btn-default button-search"><span><?php echo getLinguas('key_pesquisar2')?></span></button>
	</form>

	</div>
	<div id="search_hinters" class="search_hinters"></div>
	<?php
}
function show_search2()
{
	if (isset($_GET['pesquisa']))
	{
		$pesq = $_GET['pesquisa'];
	}
	else
	{
		// $pesq = $GLOBALS['key_pesquisar'][$GLOBALS['site_lang']];
	}

	// $search_token = gerarCSFRToken(100, 'search_token');
	?>
	<script>
	function validarPesquisaAdvance() 
	{
		var fams = '';
		$('#fams_pesq input:checked').each(function() 
		{
			if(fams) fams += ','+$(this).attr('value')
			else fams = $(this).attr('value');					
		});				
		
		var brds = '';
		$('#brands_pesq input:checked').each(function() 
		{
			if(brds) brds += ','+$(this).attr('value')
			else brds = $(this).attr('value');
		});
		if(document.getElementById('fams-pesq')) document.getElementById('fams-pesq').value = 'fams:'+fams+'|marca:'+brds;

		$('#searchbox').attr('action', '<?php echo seo('pages.php?page_id=' . $GLOBALS['pesquisa_page_id']) ?>'); 
		
		// $('#searchbox').submit();		
	}
	</script>
	<div id="search_block_top" class="clearfix">
	<form name="pesquisa" id="searchbox" action="" method="GET" onsubmit="validarPesquisaAdvance()">
		<input type="hidden" name="pesquisa2" id="fams-pesq" value="">
	
		<?
		if($_GET['ad']!=1){?>
			<input type="text" class="search_query form-control" id="search_query_top2" name="pesquisa" placeholder="<?=getLinguas('key_pesquisar',$GLOBALS['site_lang']) ?>" autocomplete="off" required />
		<? }
		else{?>		
		<input class="search_query form-control"  id="search_query_top" placeholder="<?php echo $GLOBALS['key_pesquisar'][$GLOBALS['site_lang']] ?>" type="text" value="<?= htmlentities($pesq) ?>" name="pesquisa" onkeyup="showHintsSearch();" autocomplete="off"/>
		<? }?>
		<button type="submit" name="submit_search" id="search_block_button" class="btn btn-default button-search"><img src="layout/dotbright/images/lupa.svg" alt="lupa"></button>
		<? /*<div onclick="if($('#pesquisa_avancada').is(':visible')){$('#pesquisa_avancada').hide();}else{$('#pesquisa_avancada').show();}" style="text-align:right;cursor:pointer">
		Pesquisa avançada
		</div> */?>
		
		
	</form><? /*
        <span class="search_nota">
            <span class="search_nota2"><?php echo getLinguas('key_ex')?></a></span>
            <a href="p4-pesquisa-pt?pesquisa=<?php echo getLinguas('key_iphone')?>"><?php echo getLinguas('key_iphone')?></a> ,
            <a href="p4-pesquisa-pt?pesquisa=<?php echo getLinguas('key_capas',$GLOBALS['site_lang'])?>"><?php echo getLinguas('key_capas',$GLOBALS['site_lang'])?></a>,
            <a href="p4-pesquisa-pt?pesquisa=<?php echo getLinguas('key_pelicula',$GLOBALS['site_lang'])?>"><?php echo getLinguas('key_pelicula',$GLOBALS['site_lang'])?></a>,
            <a href="p4-pesquisa-pt?pesquisa=<?php echo getLinguas('key_som',$GLOBALS['site_lang'])?>"><?php echo getLinguas('key_som',$GLOBALS['site_lang'])?></a>
        </span> */?>
	</div>
	<?
    if($_GET['ad']!=1)
    {?>
    <div id="search_resultados" class="search_resultados" style="display:none"></div>
	<div id="search_populares" class="search_populares" style="display:none"></div>
	<? }
    else{?>
    <div id="search_hinters" class="search_hinters"></div>
    <? }?>
	<?php
}
function getPesqAvan()
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new Dataobj;
	
	$fams = array();
	$pesquisa2 = (isset($_GET['pesquisa2']) && $_GET['pesquisa2'] ? $_GET['pesquisa2'] : '');
	if($pesquisa2 != '' )
	{
		$Str_pesquisar2 = strip_html_tags(strtolower_min($pesquisa2));
		//$Str_pesquisar2 = escape_mimic(str_replace(array('#','$','%','&','/','(',')','?','"',"'"), '', $Str_pesquisar2));
		
		$tt = explode('|', $Str_pesquisar2);
		$fams = explode(',', str_replace('fams:', '', $tt[0]));
		// $marcas = str_replace('marca:', '', $tt[1]);
				
	}
	$sql = '
			SELECT 
				menus_links.ID, URL_Nome2, catalogo_id
			FROM 
				menus_links 
				INNER JOIN menus_links_lang ON menus_links_lang.ID_PAI = menus_links.ID
			WHERE 
				Visivel=1 
				AND ID_ORG=1
				AND Logado=0
				AND tipo_catalogo<>""
				AND catalogo_id > 0
			ORDER BY 
				Ordem ASC
			';
			
			// $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Logado=0 AND Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$return = '';
	while (!$rs->EOF())
	{
		$catalogo_id = $rs->db_data['catalogo_id'];
		
		$return .= '<label for="cat'.$catalogo_id.'"><input type="checkbox" value="'.$catalogo_id.'" id="cat'.$catalogo_id.'" '.(count($fams) > 0 && in_array($catalogo_id, $fams) ? 'checked="checked"' : '').' name="familias[]">&nbsp;'.$rs->db_data['URL_Nome2'].'</label><br>';
		
		$rs->db_next();
	}
	$rs->db_close();
	return $return;
}
function getPesqAvan2()
{
	$marcas = array();
	$pesquisa2 = (isset($_GET['pesquisa2']) && $_GET['pesquisa2'] ? $_GET['pesquisa2'] : '');
	if($pesquisa2 != '' )
	{
		$Str_pesquisar2 = strip_html_tags(strtolower_min($pesquisa2));
		//$Str_pesquisar2 = escape_mimic(str_replace(array('#','$','%','&','/','(',')','?','"',"'"), '', $Str_pesquisar2));
		
		$tt = explode('|', $Str_pesquisar2);
		// $fams = explode(',', str_replace('fams:', '', $tt[0]));
		$marcas =  explode(',', str_replace('marca:', '', $tt[1]));
	}
	
	
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new Dataobj;
	$a=3;
	$sql = '
			SELECT 
				pf.ID, pf.img_ext, pfl.Nome
			FROM 
				brands pf 
				INNER JOIN brands_lang pfl ON pfl.ID_PAI=pf.ID 
			WHERE 
				pf.Visivel=1 
				AND preco_revenoff = 1
			GROUP BY 
				pf.ID asc
			ORDER BY 
				pf.Ordem=0, pf.Ordem asc
			';
			
			// $sql='SELECT * FROM menus_links where ID_ORG="'.$id_pai.'" AND Logado=0 AND Visivel=1 ORDER BY Ordem ASC';
	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$return = '<div style="float:left;min-width:160px">';
	while (!$rs->EOF())
	{
		$marca_id = $rs->db_data['ID'];
		$marca_name = $rs->db_data['Nome'];
		
		if($a==$b)
		{
			$return .= '</div><div style="float:left;min-width:160px">';	
			$b=0;
		}
		
		$return .= '<label for="marca'.$marca_id.'"><input type="checkbox" value="'.$marca_id.'" id="marca'.$marca_id.'"'.(count($marcas) > 0 && in_array($marca_id, $marcas) ? 'checked="checked"' : '').'  name="marcas[]">&nbsp;'.$marca_name.'</label><br>';
		
		$b++;
		$rs->db_next();
	}
	$rs->db_close();
	
	$return .= '</div>';
	return $return;
}
function show_ofertas()
{
	?>
	<a href="<?php echo seo('pages.php?page_id=100');?>" class="ofertas"><div class="title">OFERTAS DO DIA</div><div class="clock"></div></a>
	<?php
}
function remove_HTML($s, $keep = '', $expand = 'script|style|noframes|select|option')
{
	//prep the string
	$s = ' ' . $s;
	if (substr_count($s, 'class=destaque2>'))
	{
		$inicio = substr($s, 0, strpos($s, 'class=destaque2>') + 16);
		$s = substr($s, strpos($s, 'class=destaque2>') + 16, strlen($s));//s ficou sem o inicio
		$titulo = substr($s, 0, strpos($s, '<'));
		$s = $inicio . "[SPAN]" . $titulo . "[BR]" . substr($s, strpos($s, '<'), strlen($s));
	}
	else
	{
		$inicio = substr($s, 0, strpos($s, 'class="destaque2">') + 18);
		$s = substr($s, strpos($s, 'class="destaque2">') + 18, strlen($s));//s ficou sem o inicio
		$titulo = substr($s, 0, strpos($s, '<'));
		$s = $inicio . "[SPAN]" . $titulo . "[BR]" . substr($s, strpos($s, '<'), strlen($s));
	}
	//initialize keep tag logic
	if (strlen($keep) > 0)
	{
		$k = explode('|', $keep);
		for ($i = 0; $i < count($k); $i++)
		{
			$s = str_replace('<' . $k[$i], '[{(' . $k[$i], $s);
			$s = str_replace('</' . $k[$i], '[{(/' . $k[$i], $s);
		}
	}
	//begin removal
	//remove comment blocks
	while (stripos($s, '<!--') > 0)
	{
		$pos[1] = stripos($s, '<!--');
		$pos[2] = stripos($s, '-->', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 3;
		$x = substr($s, $pos[1], $len[1]);
		$s = str_replace($x, '', $s);
	}
	//remove tags with content between them
	if (strlen($expand) > 0)
	{
		$e = explode('|', $expand);
		for ($i = 0; $i < count($e); $i++)
		{
			while (stripos($s, '<' . $e[$i]) > 0)
			{
				$len[1] = strlen('<' . $e[$i]);
				$pos[1] = stripos($s, '<' . $e[$i]);
				$pos[2] = stripos($s, $e[$i] . '>', $pos[1] + $len[1]);
				$len[2] = $pos[2] - $pos[1] + $len[1];
				$x = substr($s, $pos[1], $len[2]);
				$s = str_replace($x, '', $s);
			}
		}
	}
	//remove remaining tags
	while (stripos($s, '<') > 0)
	{
		$pos[1] = stripos($s, '<');
		$pos[2] = stripos($s, '>', $pos[1]);
		$len[1] = $pos[2] - $pos[1] + 1;
		$x = substr($s, $pos[1], $len[1]);
		$s = str_replace($x, '', $s);
	}
	//finalize keep tag
	for ($i = 0; $i < count($k); $i++)
	{
		$s = str_replace('[{(' . $k[$i], '<' . $k[$i], $s);
		$s = str_replace('[{(/' . $k[$i], '</' . $k[$i], $s);
	}
	$s = str_replace('[BR]', '</span><BR><BR>', $s);
	$s = str_replace('[SPAN]', '<span class="destaque1b" style="font-size:12px;font-weight:bold;">', $s);

	return trim($s);
}

function show_pesquisa_resultados()
{
	if(isset($GLOBALS['pesq_type']))
	{
		$pesq = $GLOBALS['pesq_type'];
		if($pesq['produtos'])	echo catalogo_produtos();
		elseif($pesq['paginas'])echo show_pesquisa_pages();
	}
	else
	{
		echo catalogo_produtos();
	}
}

//Nuvem de palavras
function tag_info()
{
	$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
	$rs = new Dataobj;
	$rs->db_open($db_link, $GLOBALS['DB_name'], 'SELECT COUNT(Titulo) AS count, Titulo FROM pesquisa GROUP BY Titulo ORDER BY count DESC');
	while (!$rs->EOF())
	{
		if (strlen($rs->db_data['Titulo']) > 3)
		{
			$arr[$rs->db_data['Titulo']] = $rs->db_data['count'];
		}
		$rs->db_next();
	}
	$rs->db_close();
	ksort($arr);

	return $arr;
}

function getCloud($minFontSize = 12, $maxFontSize = 20)
{
	$min_size = 10;
	$max_size = 20;
	$tags = tag_info();
	$minimum_count = min(array_values($tags));
	$maximum_count = max(array_values($tags));
	$spread = $maximum_count - $minimum_count;
	if ($spread == 0)
	{
		$spread = 1;
	}
	$cloud_tags = array();
	foreach ($tags as $tag => $count)
	{
		$size = $min_size + ($count - $minimum_count) * ($max_size - $min_size) / $spread;
		$cloud_tags[] = '<a style="font-size: ' . floor($size) . 'px' . '" class="tag_cloud" href="' . seo('pages.php?page_id=' . $GLOBALS['pesquisa_page_id']) . '?pesquisa=' . $tag . '" title="' . $tag . '">' . htmlspecialchars(stripslashes($tag)) . '</a>';
		$count++;
		if ($count > 50)
		{
			break;
		}
	}
	$cloud_html = join("\n", $cloud_tags) . "\n";

	return $cloud_html;
}


function show_pesquisa_pages()
{
    $d = clean_var($_GET['pesquisa']);
    if(isset($_GET['pesquisa']) && $d!='')
    {
        global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url,$key_ler,$key_pesquisa_resultados,$txt_paginas,$key_pesquisa_sem_resultados;
        $db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
        $rs = new DataObj;$rs2=new DataObj;
        $d = lower_str(ltrim(rtrim($d)));
        $sql="SELECT distinct ID_PAI,Cabecalho,Cabecalho2,
        lower(Texto) as Texto
        FROM pages_lang
        WHERE (lower(Texto) LIKE '%".$d."%' OR Cabecalho like '%".$d."%'  ) AND Lang='$site_lang'  ORDER BY ID DESC  ";
       	$pesq = array();
       	$str = '';
        $j = 0;
        $rs->db_open($db_link,$DB_name,$sql);
        if(!$rs->EOF())
        {
        	$str .=  '<br><h3>'.$txt_paginas[$site_lang].'</h3>';
            $str .=  '<br />'.$key_pesquisa_resultados[$site_lang].'<br /><br />';

            while(!$rs->EOF())
            {
                $ID=$rs->db_data['ID_PAI'];
                $C=stripslashes($rs->db_data['Cabecalho']);
                $C2=stripslashes($rs->db_data['Cabecalho2']);

                if($C2) $C=$C.' : '.$C2;
                $sql="SELECT * from pages WHERE ID=$ID and Visible=1 and Pesquisavel=1 ";
                $rs2->db_open($db_link,$DB_name,$sql);
                if(!$rs2->EOF())
                {
                	$pesq[$j]['line'] = '<a href="'.seo('pages.php?page_id='.$ID).'">» '.$C.'</a><br>';
                	$str .= $pesq[$j]['line'];
                    $j++;
                }
                $rs2->db_close();
                $rs->db_next();
            }
        }
        $rs->db_close();
        if($j>0)
        {
        	return $str;
        }
        else
        {
        	return $key_pesquisa_sem_resultados[$site_lang];
        }
    }
}


?>