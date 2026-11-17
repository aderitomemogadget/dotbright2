<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME']==__FILE__ ) exit();
function regista_clique($id)
{
	if($GLOBALS['media_regista_clique']==1)
	{
		$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
		$rs=new DataObj;
		$rs->db_open($db_link, DB_NAME, 'select * from media where ID='.$id);
		if(!$rs->EOF()) $t_cliques=$rs->db_data['T_cliques'];
		$rs->db_close();
		$t_cliques++;
		$t_cliques=str2SQLstr($t_cliques);
		if($id)$res=Runquery($db_link,DB_NAME,'update media Set T_cliques="'.$t_cliques.'" where ID='.$id);
		db_closedb_conection($db_link);
	}
}
function regista_impressao($id,$n_impressoes,$t_impressoes)
{
	if(isset($GLOBALS['media_regista_impressao']) && $GLOBALS['media_regista_impressao']==1)
	{
		$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	    Runquery($db_link, DB_NAME, 'UPDATE media SET N_impressoes="'.$n_impressoes.'", T_impressoes="'.$t_impressoes.'" WHERE ID='.$id);
	    db_closedb_conection($db_link);
	}
}
function show_media($tipo,$bk=0)
{
    $db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
    $obj=new DataObj;
	$obj2=new DataObj;

	$ntable='media_type';

	$obj->db_open($db_link, DB_NAME, 'select * from '.$ntable.' where ID='.$tipo);
    if(!$obj->EOF())
	{
		$comportamento=$obj->db_data['Comportamento'];
		$largura=$obj->db_data['largura'];
		$altura=$obj->db_data['altura'];
		$select_image=$obj->db_data['select_image'];
		$bg_color=$obj->db_data['bg_color'];
		if(isset($obj->db_data['Aleatoria'])) $aleatoria=$obj->db_data['Aleatoria'];
		else $aleatoria=0;

		$obj->db_close();

		$bg_color=str_replace('#','',$bg_color);
		if($comportamento==-1)
		{
			$sql="Select ID,URL,img_ext,N_impressoes,T_impressoes,Script from media where (LINGUA='' OR LINGUA='".$GLOBALS['site_lang']."') AND Visible=1 and Tipo=$tipo $and and (Permanente=1 OR (curdate() between Data1 and Data2 and N_impressoes > 0 and N_cliques > 0)) order by Peso DESC";
			$obj2->db_open($db_link, DB_NAME, $sql);
			while(!$obj2->EOF())
			{
				$ID=$obj2->db_data['ID'];
				$url=$obj2->db_data['URL'];
				$I1=$obj2->db_data['img_ext'];
				$n_impressoes=$obj2->db_data['N_impressoes'];
				$t_impressoes=$obj2->db_data['T_impressoes'];
				regista_impressao($ID,$n_impressoes-1,$t_impressoes+1);
				$url=str_replace('&','%26',$url);
				$url=str_replace('?','%3f',$url);
				$script=stripcslashes($obj2->db_data['Script']);
				if($script)echo trata_tags(limpa_editor_lixo($script));
				$obj2->db_next();
			}
			$obj2->db_close();
		}

		// echo '$comportamento'.$comportamento;
		if($comportamento==0) return sem_comportamento($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria); // Năo tem comportamento mostra só um com maior peso
		if($comportamento==1) return media_slides($tipo, $largura, $altura, $select_image, $bg_color, $aleatoria); //SLIDES
		if($comportamento==2) {} // google analytics não apagar
		// if($comportamento==3) return show_media3($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria); // Slide
		// if($comportamento==4) return show_media4($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria); // Fade In
		// if($comportamento==5) return show_media5($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria);	// Fade In com slide descritivo topo
		// if($comportamento==6) return show_media6($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria);	// Fade In com slide descritivo direita
		// if($comportamento==7) return show_media7($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// Fade In com fundo
		// if($comportamento==8) return show_media8($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// Fade In com seleccao por baixo
		// if($comportamento==9) return show_media9($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// Video
		// if($comportamento==10) return show_media10($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// Video
		// if($comportamento==11) return show_media11($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// 3d 1
		// if($comportamento==12) return show_media12($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// galeria
		// if($comportamento==13) return show_media13($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// slideshow avançado
		// if($comportamento==14) return show_media14($tipo,$largura,$altura,1,$bg_color,$bk,$aleatoria);	// portfolio
		// if($comportamento==15) return show_media15($tipo,$largura,$altura,1,$bg_color,$bk,$aleatoria);	// portfolio
		// if($comportamento==16) return show_media16($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// Fade In com seleccao por cima
		// if($comportamento==17) return show_media17($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// News
		// if($comportamento==18) return show_media18($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// News
		// if($comportamento==19) return show_media19($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// News
		// if($comportamento==20) return show_media20($tipo,$largura,$altura,1,$bg_color,$aleatoria);	// News Login
		// if($comportamento==21) return show_media21($tipo,$largura,$altura,1,$bg_color,$aleatoria);
		// if($comportamento==25) return show_media25($tipo,$largura,$altura,1,$bg_color,$aleatoria);
		if($comportamento==3) return show_media_history($tipo,$largura,$altura,1,$bg_color,$aleatoria);
		if($comportamento==30) return full_page_bloco($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria); // Năo tem comportamento mostra só um com maior peso
		if($comportamento==4) return show_media_enderecos($tipo,$largura,$altura,1,$bg_color,$aleatoria);
	    if($comportamento==5) return show_media_boletins($tipo,$largura,$altura,1,$bg_color,$aleatoria);

	    if($comportamento==26) return show_media_ebook($tipo,$largura,$altura,1,$bg_color,$aleatoria);
	}

}
/*function show_media0($tipo,$largura,$altura,$select_image,$bg_color,$aleatoria=0){ global $site_http_url,$tema;?><iframe  src="<?=$site_http_url?>core/media/sem_comportamento/index.php?tipo=<?=$tipo?>&largura=<?=$largura?>&altura=<?=$altura?>&select_image=<?=$select_image?>&tema=<?=$tema['ID']?>&bg_color=<?=$bg_color?>&aleatoria=<?=$aleatoria?>"  width="<?=$largura?>" height="<?=$altura?>" style="overflow:hidden;" frameborder=0 SCROLLING="no"   <?php if($bg_color) echo 'style="background-color:'.$bg_color.'"';?>><p>Your browser does not support iframes.</p></iframe><?php }*/

function show_media_ebook($tipo,$largura,$altura,$select_image,$bg_color,$bk=0,$aleatoria=0)
{
	$return='';

	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS); 
	$obj=new DataObj;
	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY ID DESC';
	$obj->db_open($db_link, DB_NAME, $sql);


	if(!$obj->EOF())
	{
		$return .= '<div class="row">';
		while(!$obj->EOF())
		{
			$return .= '<div class="col-md-3 text-center">';
			$ID=$obj->db_data['ID'];
			$url=$obj->db_data['URL'];
			$I1=$obj->db_data['img_ext'];
			$I2=$obj->db_data['img_ext2'];
			$n_impressoes=$obj->db_data['N_impressoes'];
			$t_impressoes=$obj->db_data['T_impressoes'];
			regista_impressao($ID,$n_impressoes-1,$t_impressoes+1);
			$url=str_replace('&','%26',$url);
			$url=str_replace('?','%3f',$url);
			$script=stripcslashes($obj->db_data['Script']);
			$Descritivo=stripcslashes($obj->db_data['Descritivo']);
			if($I1!='')
			{
				$img_pdf='files/media/'.$ID.'_2.'.$I2;

				if($I2) $return .= "<a href=\"".SITE_HTTP_URL."/ebook/index.php?fname=".$ID."_2.pdf2\" target=\"_blank\">";
				
				$img_file='files/media/'.$ID.'_1.'.$I1;
				$return .= '<div class="clear"></div><div class="destaque_img"><div class="row"><div class="col-sm-12"><img style="max-width:200px" width="100%" border=0 src="'.SITE_HTTP_URL.$img_file.'?d='.dumpcache.'"></div><div style="padding-left:15px;padding-top:5px">'.$Descritivo.'</div></div></div><div class="clear"></div>';
				
				if($I2) $return .=  '</a>';
			}
			elseif($script) $return .=  $script;

			$return .= '</div>';
			$obj->db_next();
		}
		$return .= '</div>';
	}
	$obj->db_close();

	return $return;
}

function sem_comportamento($tipo,$largura,$altura,$select_image,$bg_color,$bk=0,$aleatoria=0)
{
	$return='';

	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;
	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso LIMIT 1';
	// echo $sql;
	$obj->db_open($db_link, DB_NAME, $sql);
	if(!$obj->EOF())
	{
		$ID=$obj->db_data['ID'];
		$url=$obj->db_data['URL'];
		$I1=$obj->db_data['img_ext'];
		$n_impressoes=$obj->db_data['N_impressoes'];
		$t_impressoes=$obj->db_data['T_impressoes'];
		regista_impressao($ID,$n_impressoes-1,$t_impressoes+1);
		$url=str_replace('&','%26',$url);
		$url=str_replace('?','%3f',$url);
		$script=stripcslashes($obj->db_data['Script']);
		$Descritivo=stripcslashes($obj->db_data['Descritivo']);
		if($I1!='')
		{
			if($url) $return .= "<a href=\"".SITE_HTTP_URL."banner_click.php?id=".$ID."&url=".$url."\" target=\"_top\">";
			if($I1=="swf"){}
			else
			{
				$img_file='files/media/'.$ID.'_1.'.$I1;
				$return .= '<div class="clear"></div><div class="destaque_img"><div class="row"><div class="col-sm-12"><img width="100%" border=0 src="'.SITE_HTTP_URL.$img_file.'?d='.dumpcache.'"></div>'.$Descritivo.'</div></div><div class="clear"></div>';
			}
			if($url) $return .=  '</a>';
		}
		elseif($script) $return .=  $script;
	}
	$obj->db_close();

	return $return;
}
function media_slides($tipo=0, $largura=0, $altura=0, $select_image=0, $bg_color='', $aleatoria=0)
{
	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;

	$return='';

	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso';
	// echo $sql;
	$obj->db_open($db_link, DB_NAME, $sql);
	if(!$obj->EOF())
	{
		if($obj->db_rows==1)
		{
			$ID=$obj->db_data['ID'];
			$url=$obj->db_data['URL'];
			$I1=$obj->db_data['img_ext'];
			$n_impressoes=$obj->db_data['N_impressoes'];
			$t_impressoes=$obj->db_data['T_impressoes'];
			regista_impressao($ID, $n_impressoes-1, $t_impressoes+1);
			$url=str_replace('&','%26',$url);
			$url=str_replace('?','%3f',$url);
			$script=stripcslashes($obj->db_data['Script']);
			
			$img_file='files/media/'.$ID.'_1.'.$I1;
			if($url) $return .= "<a href=\"".SITE_HTTP_URL."banner_click.php?id=".$ID."&url=".$url."\" target=\"_top\">";
			$return .= '<img src="'.SITE_HTTP_URL.$img_file.'?d='.filemtime(SITE_SHELL_URL.$img_file).'" width="100%" style="width:100%" alt="" border="0" />';
			if($url) $return .=  '</a>';
		}
		else
		{
			$return .= '<div class="banner" id="warp_bxslider'.$tipo.'"><ul class="bxslider" id="bxslider'.$tipo.'">';
			while(!$obj->EOF())
			{
				$ID=$obj->db_data['ID'];
				$url=$obj->db_data['URL'];
				$I1=$obj->db_data['img_ext'];
				$n_impressoes=$obj->db_data['N_impressoes'];
				$t_impressoes=$obj->db_data['T_impressoes'];
				regista_impressao($ID, $n_impressoes-1, $t_impressoes+1);
				$url=str_replace('&','%26',$url);
				$url=str_replace('?','%3f',$url);
				$script=stripcslashes($obj->db_data['Script']);
				if($I1!='')
				{
					if($I1 == 'swf'){}
					else
					{

						$return .= '<li>';

						$img_file='files/media/'.$ID.'_1.'.$I1;
						if($url) $return .= "<a href=\"".SITE_HTTP_URL."banner_click.php?id=".$ID."&url=".$url."\" target=\"_top\">";
						$return .= '<img src="'.SITE_HTTP_URL.$img_file.'?d='.filemtime(SITE_SHELL_URL.$img_file).'" width="100%" style="width:100%" alt="" border="0" />';
						if($url) $return .=  '</a>';

						$return .= '</li>';
					}
				}
				elseif($script){}

				$obj->db_next();
			}
			$return .= '</ul></div>';
		}

	}
	$obj->db_close(1);

	return $return;
}

function google_analytics()
{
	$return='';

	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;
	$sql='
			SELECT media.* FROM media INNER JOIN media_type mt ON mt.ID=media.Tipo  WHERE Visible=1 and Comportamento=2 AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso LIMIT 1';
	// echo $sql;
	$obj->db_open($db_link, DB_NAME, $sql);
	if(!$obj->EOF())
	{
		$ID=$obj->db_data['ID'];
		$n_impressoes=$obj->db_data['N_impressoes'];
		$t_impressoes=$obj->db_data['T_impressoes'];
		regista_impressao($ID,$n_impressoes-1,$t_impressoes+1);

		$script=stripcslashes($obj->db_data['Script']);

		if($script) $return .=  $script;
	}
	$obj->db_close();

	return $return;
}


function show_media_history($tipo=0, $largura=0, $altura=0, $select_image=0, $bg_color='', $aleatoria=0)
{
	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;

	$return='';

	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso';
	// echo $sql;
	$return .= '<div class="histor">';

	$obj->db_open($db_link, DB_NAME, $sql);
	//$return .= '-+ '.$obj->db_rows.' +-';
	$or = $obj->db_rows;
	$j = 1;
	if(!$obj->EOF())
	{

		//$return .= '';
		while(!$obj->EOF())
		{
			$ID=$obj->db_data['ID'];
			/**/
			$aclass = '';
			if($j == $or){
				$aclass = 'style="border-left:none;"';
			}
			$return .= '<div class="innerhistor" '.$aclass.'>';
				$return .= '<div class="squareli"></div>';
				$return .= '<div class="titlehist">'.$obj->db_data['Name'].'</div>';
				$return .= '<div class="deschist">'.$obj->db_data['Descritivo'].'</div>';
			$return .= '</div>';/**/

			$j++;
			$obj->db_next();
		}

	}
	$obj->db_close(1);
	$return .= '</ul>';
	return $return;
}


function full_page_bloco($tipo,$largura,$altura,$select_image,$bg_color,$bk=0,$aleatoria=0)
{
	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;
	$return='';
	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso';
	$obj->db_open($db_link, DB_NAME, $sql);
	if(!$obj->EOF())
	{

		while(!$obj->EOF())
		{
			$ID=$obj->db_data['ID'];
			$url=$obj->db_data['URL'];
			$I1=$obj->db_data['img_ext'];
			$n_impressoes=$obj->db_data['N_impressoes'];
			$t_impressoes=$obj->db_data['T_impressoes'];
			regista_impressao($ID, $n_impressoes-1, $t_impressoes+1);
			$url=str_replace('&','%26',$url);
			$url=str_replace('?','%3f',$url);
			$script=stripcslashes($obj->db_data['Script']);
			$texto=stripcslashes($obj->db_data['Descritivo']);

			$cursor='';
        	if($url){$cursor=';cursor:pointer';$link=' onclick="window.open(\''.$url.'\',\'_new\')"';}

			if($I1!='')
			{
				if($obj->db_data['img_ext']) $imagem='files/media/'.$obj->db_data['ID'].'_1.'.$obj->db_data['img_ext'];

				$active='';
		        if($i==0)$active='active';
		        $i++;

				if($I1 == 'swf'){}
				else
				{

					$min_height = '';
					if($altura != '') $min_height = 'min-height: '.$altura.'px !important;';

					$bloco_id = $_SESSION['bloco_id'];
					$return .= '
					<div id="home_bloco'.$bloco_id.'" class="d-flex align-items-center justify-content-center min-vh-100" style="'.$min_height.' background:url('.$imagem.') no-repeat top /cover'.$cursor.'" '.$link.' >


		                  <h1 style="color:#fff;font-family:SilkSerif-Light" class="mobile_h1">'.$texto.'</h1>


		            </div>';

		            $_SESSION['bloco_id'] +=1;

				}
			}
			elseif($script){}

			$obj->db_next();
		}

	}
	$obj->db_close(1);

	return $return;
}


function show_media_enderecos($tipo=0, $largura=0, $altura=0, $select_image=0, $bg_color='', $aleatoria=0)
{
	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;

	$return='';

	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso';
	// echo $sql;



	$obj->db_open($db_link, DB_NAME, $sql);
	//$return .= '-+ '.$obj->db_rows.' +-';
	$or = $obj->db_rows;
	$j = 1;
	$return .= '<div class="select-content">';
	if(!$obj->EOF())
	{
		$return .= '<div class="enderecos-select" id="enderecos-select"  onclick="showclose(this)" data-sc="0" data-id="'.$obj->db_data['ID'].'" ><span id="nameonselect">'.$obj->db_data['Name'].'</span> <span class="triangle-select mostrado">&#x25BC;</span><span id="close-endereco-div" class="oculto">&#x00D7;</span></div>';
		$return .= '<div class="endereco-div oculto">';
		$did = $obj->db_data['ID'];
		//$return .= '';
		$j=1;
		while(!$obj->EOF())
		{
			$address = base64_encode('<div class=""><strong>'.$obj->db_data['Name'].'</strong></div>'.'<div class="">'.nl2br($obj->db_data['Descritivo']).'</div>');
			$nameonselect = base64_encode($obj->db_data['Name']);

			$ID=$obj->db_data['ID'];
			if($did == $ID ){
				$faddress = $address;
			}
			$bg = '';
		if($j % 2){ $bg = 'style="background:#ffffff;"';}

			$return .= '<div class="endereco-box" '.$bg.' onclick="putthisaddres(\''.$address.'\',\''.$nameonselect.'\')">';
				$return .= '<div class="squareli"></div>';
				$return .= '<div class=""><strong>'.$obj->db_data['Name'].'</strong></div>';
				//$return .= '<div class="">'.nl2br($obj->db_data['Descritivo']).'</div>';
			$return .= '</div>';/**/

			$j++;


			$obj->db_next();
		}

	}
	$obj->db_close(1);
	$return .= '</div>';
	$return .= '</div>';


	$return .= '<div id="actual-address">';
	$return .= base64_decode($faddress);
	$return .= '</div>';


	$return .= '<script>
				function putthisaddres(address,nameonselect){
						$("#actual-address").html(atob(address));
						$("#nameonselect").html(atob(nameonselect));
						$("#enderecos-select").attr("data-sc",0);
	 					$(".endereco-div").removeClass("mostrado").addClass("oculto");
					$(".triangle-select").removeClass("oculto").addClass("mostrado");
					$("#close-endereco-div").removeClass("mostrado").addClass("oculto");

				}

	 			function showclose(obj){
	 				var state = $("#"+obj.id).attr("data-sc");
	 				if(state == 0){
	 					$(".endereco-div").removeClass("oculto").addClass("mostrado");
						$("#close-endereco-div").removeClass("oculto").addClass("mostrado");
						$(".triangle-select").removeClass("mostrado").addClass("oculto");
						setTimeout(function(){ $("#"+obj.id).attr("data-sc",1); }, 100);
	 				}
	 				if(state == 1){
	 					$("#"+obj.id).attr("data-sc",0);
	 					$(".endereco-div").removeClass("mostrado").addClass("oculto");
					$(".triangle-select").removeClass("oculto").addClass("mostrado");
					$("#close-endereco-div").removeClass("mostrado").addClass("oculto");
	 				}
	 			}

	 			$(document).on("click", function (e) {
				    if ($(e.target).closest(".endereco-div").length === 0) {
				    	var state = $("#enderecos-select").attr("data-sc");
				    	if(state == 1){
				            $("#enderecos-select").attr("data-sc",0);
		 					$(".endereco-div").removeClass("mostrado").addClass("oculto");
							$(".triangle-select").removeClass("oculto").addClass("mostrado");
							$("#close-endereco-div").removeClass("mostrado").addClass("oculto");
				     	}
				    }
				});
 			</script>';
	return $return;
}


function show_media_boletins($tipo=0, $largura=0, $altura=0, $select_image=0, $bg_color='', $aleatoria=0)
{
	$db_link=\db_opendb_conection(DB_HOST, DB_USER, DB_PASS);
	$obj=new DataObj;

	$return='';

	$sql='SELECT * FROM media WHERE Visible=1 and Tipo='.$tipo.' AND (Permanente=1 OR (CURDATE() BETWEEN Data1 AND Data2) AND N_impressoes > 0 and N_cliques > 0) AND (Lingua="" OR Lingua="'.$GLOBALS['site_lang'].'") ORDER BY Peso';

	$return .= '<div class="histor">';

	$obj->db_open($db_link, DB_NAME, $sql);
	//$return .= '-+ '.$obj->db_rows.' +-';
	$or = $obj->db_rows;
	$j = 1;
	if(!$obj->EOF())
	{

		$j=2;
		while(!$obj->EOF())
		{
			$ID=$obj->db_data['ID'];
			$bg = '';
		if($j % 2){ $bg = 'style="background:#dddddd;"';}



			$return .= '<div class="boletins-list" '.$bg.'>';


				$return .= '<a href="files/media/'.$ID.'_1.'.$obj->db_data['img_ext'].'" target="_blank">';



				$return .= '<div class="">';
				$return .= '<span class="pdf-icon">';
				$return .= '<i class="fas fa-file-pdf fa-2x"></i>';
				$return .= '</span>';

				$return .= $obj->db_data['Name'].'<br>';

				//$return .= '<span class="boletins-desc">'.$obj->db_data['Descritivo'].'</span>';
				$return .= '</div>';
				$return .= '</a>';
			$return .= '</div>';/**/

			$j++;
			$obj->db_next();
		}

	}
	$obj->db_close(1);
	$return .= '</div>';
	return $return;
}
