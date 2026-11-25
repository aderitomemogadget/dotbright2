<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__) {
	exit();
}

function modulo_pc($ID_Modulo)
{
	if($_SESSION['ss_ismobile']==0)return modulos($ID_Modulo);
}

function getModulo($ID_Modulo,$campos='*')
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dbo=new DataObj();
	$sql='SELECT '.$campos.' FROM modulos WHERE ID='.$ID_Modulo.' AND Visivel=1 ORDER BY ordem ASC';
	$dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	return $dbo;
}

function getModuloLang($ID_Modulo)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dbo=new DataObj();
	$sql='SELECT * FROM modulos_lang WHERE ID_PAI='.$ID_Modulo.' AND Lang="'.$GLOBALS['site_lang'].'"';
	$dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	return $dbo;
}

function getModuloGrupo($ID_Modulo,$campos='*')
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dbo=new DataObj();
	$sql='SELECT '.$campos.' FROM modulos WHERE ID_ORG='.$ID_Modulo.' AND Visivel=1 AND ((data <= CURDATE() AND data2 >= CURDATE()) OR (data <= CURDATE() AND permanente=1)) ORDER BY ordem ASC';
	$dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	return $dbo;
}

function getModuloImgs($ID_Modulo)
{
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dbo=new DataObj();
	$sql='SELECT bi.*,bil.bloco_img_titulo,bil.bloco_img_txt,bil.bloco_img2_txt FROM modulos_imgs bi INNER JOIN modulos_imgs_lang bil ON bil.bloco_img_ID_PAI=bi.bloco_img_id WHERE bil.lang="'.$GLOBALS['site_lang'].'" AND bi.bloco_img_ID_ORG="'.$ID_Modulo.'" ORDER BY bi.bloco_img_ordem ASC';
	$dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	return $dbo;
}

function getModuloTextStyles($class='',$color='',$size='',$weight='',$lineHeight='')
{
	$class=str_replace('class="','',$class);
	$class=str_replace('"','',$class);


	if($class=='clear')$class='';
	$result='';
	if($color == '0')$color='';
	if($size == '0')$size='';
	if($weight == '0')$weight='';
	if($lineHeight == '0')$lineHeight='';

	if($color != '' || $size != '' || $weight != '' || $lineHeight != '')
	{
		if(substr($color,0,1) == '.')$color=substr($color,1);//Vamos remover o.da classe

		//Vamos verificar se já é o novo modulo com classes:

		if($color != '' && substr_count('#',$color) <= 0)
		{
			if($class)$result=' class="'.$class.' '.$color.'"';
			else $result=' class="'.$color.'"';
		}
		else
		{
			//sistema antigo
			if($color)$result.='color:'.$color.';';
			if($size)$result.='font-size:'.$size.'px;';
			if($weight)$result.='font-weight:'.$weight.';';
			if($lineHeight)$result.='line-height:'.$lineHeight.'px;';
			if($class)$result=' class="'.$class.'" style="'.$result.'"';
			else $result=' style="'.$result.'"';
		}
	}
	else
	{
		if($class)$result=' class="'.$class.'"';
	}

	return $result;
}

function modulos($ID_Modulo=0)
{
	$result='';
	$dbo=getModulo($ID_Modulo,'categorias');
	if(!$dbo->EOF()) {
		$tipo=rtrim($dbo->db_data['categorias'],';');

		switch ($tipo) {
			case 1:
				//Grupo
				$result=showModuloGrupo($ID_Modulo);
				break;
			case 2:
				//Banner
				$result=showModuloBanner($ID_Modulo);
				break;
			case 3:
				//Texto
				$result=showModuloTextoCentrado($ID_Modulo);
				break;
			case 4:
				//Destaques imagems - circulos
				$result=showModuloCarrocell($ID_Modulo);
				break;
			case 5:
				//Destaques imagems - retangulos
				$result=showModuloCarrocell($ID_Modulo);
				break;
			case 6:
				//conteudos centrados
				$result=showModuloConteudos($ID_Modulo);
				break;
			case 7:
				//conteudos centrados
				$result=showModuloConteudos($ID_Modulo);
				break;
			case 8:
				//Texto lado a lado
				$result=showModuloTextoLadoaLado($ID_Modulo);
				break;
			case 9:
				//destaques tipo blogue
				$result=showModuloDestaques($ID_Modulo);
				break;
			case 10:
				//Breadcrumbs
				$result=showModuloBreadcrumbs($ID_Modulo);
				break;
			case 11:
				//Galeria de Imagens
				$result=showModuloGaleria($ID_Modulo);
				break;
			case 15:
				//Accordion Grupo
				$result=showModuloAccordion($ID_Modulo);
				break;
			case 16:
				//Google Maps
				$result=showModuloGoogleMaps($ID_Modulo);
				break;
			case 17:
				//Accordion Elemento
				$result=showModuloAccordion($ID_Modulo);
				break;
			case 18:
				//Imagem Esquerda,Texto Direita
				$result=showModuloImagemEsqTextoDir($ID_Modulo);
				break;
			case 19:
				//Apoios
				$result=showModuloApoios($ID_Modulo);
				break;
			case 20:
				//Tabela de Precos Grupo
				$result=showModuloGrupoTabelaPrecos($ID_Modulo);
				break;
			case 21:
				//Tabela de Precos Coluna
				$result=showModuloElementoTabelaPrecos($ID_Modulo);
				break;
			case 22:
				//Organização
				$result=showModuloElementoOrganizacao($ID_Modulo);
				break;
			case 23:
				//destaques tipo blogue varias
				$result=showModuloDestaques2($ID_Modulo);
				break;
			case 24:
				//destaques tipo blogue varias
				//$result=showMultiScroll($ID_Modulo);
                //destaques tipo blogue varias
                $result = showModuloListaComPopup($ID_Modulo);
				break;
		}
	}
	$dbo->db_close();

	return $result;
}

function showModuloGrupo($ID_Modulo)
{
	$result='';
	$dbo=getModuloGrupo($ID_Modulo,'ID');
	while (!$dbo->EOF()) {
		$result.=modulos($dbo->db_data['ID']);
		$dbo->db_next();
	}
	$dbo->db_close();

	return $result;
}

function showModuloBanner($ID_Modulo)
{
    $result='';
	$w='1024';
	$h='768';
    //if($ID_Modulo== 2) $h='322';

	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
	    $Anchor=$dbo->db_data['Anchor'];
        $img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Posicao=$dbo->db_data['Posicao'];
		$Qtd=$dbo->db_data['Qtd'];
		if(!$Qtd)$Qtd=3;
		$Tem_paginacao=$dbo->db_data['Tem_paginacao'];
		$Tem_setas=$dbo->db_data['Tem_setas'];
		$Tem_sombra=$dbo->db_data['Tem_sombra'];
		$Largura_fixa=$dbo->db_data['Largura'];
		$Altura=$dbo->db_data['Altura'];
		$altura_max=$dbo->db_data['altura_max'];

		if($altura_max)
		{
			echo '<style scoped>.modulocss'.$ID_Modulo.'{max-height:100vh !important}</style>';
			$h=$Altura;
			if(!$Largura_fixa)$result='<div style="height:100vh;overflow:hidden;width:100%;margin:0 auto;padding:0px;position:relative">';
			else $result='<div style="height:100vh">';
		}
		elseif($Altura)
		{
			echo '<style scoped>.modulocss'.$ID_Modulo.'{max-height:'.$Altura.'px !important}</style>';
			$h=$Altura;
			if(!$Largura_fixa)$result='<div  style="overflow:hidden;width:100%;margin:0 auto;padding:0px;position:relative">';
			else $result='<div>';
		}
		else
		{
            if($ID_Modulo== 2 )$txt  = 'id = "id_modulo2"';
			if(!$Largura_fixa)$result='<div '.$txt.' style="overflow:hidden;width:100%;margin:0 auto;padding:0px;position:relative">';
			else $result='<div>';
		}
		if(!$Anchor)$Anchor='main-slider';
		$idattr='id="'.$Anchor.'_bloco"';
		$count=0;
		$content='';
		$dbo2=getModuloImgs($ID_Modulo);

		if($dbo2->db_rows==1)
		{
			$imagem=$dbo2->db_data['bloco_img_ext'];
			$imagem_mobile=$dbo2->db_data['bloco_img2_ext'];

			$titulo=$dbo2->db_data['bloco_img_titulo'];
			$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$texto_m=limpa_editor_lixo($dbo2->db_data['bloco_img2_txt']);
			if($texto_m){ob_start();put_content($texto_m);$texto_m=ob_get_contents();ob_end_clean();}

			$url=$dbo2->db_data['bloco_img_url'];
			$url_video=$dbo2->db_data['bloco_img_video'];
			$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
			$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
			$a_data_x=$dbo2->db_data['a_data_x'];
			$a_data_y=$dbo2->db_data['a_data_y'];
			$a_data_voffset=$dbo2->db_data['a_data_voffset'];
			$a_data_hoffset=$dbo2->db_data['a_data_hoffset'];
			$a_data_speed=$dbo2->db_data['a_data_speed'];
			$a_data_start=$dbo2->db_data['a_data_start'];
			$a_data_easing=$dbo2->db_data['a_data_easing'];
			$a_data_endspeed=$dbo2->db_data['a_data_endspeed'];
			$a_data_text_align=$dbo2->db_data['a_data_text_align'];
			$b_data_x=$dbo2->db_data['b_data_x'];
			$b_data_y=$dbo2->db_data['b_data_y'];
			$b_data_voffset=$dbo2->db_data['b_data_voffset'];
			$b_data_hoffset=$dbo2->db_data['b_data_hoffset'];
			$b_data_speed=$dbo2->db_data['b_data_speed'];
			$b_data_start=$dbo2->db_data['b_data_start'];
			$b_data_easing=$dbo2->db_data['b_data_easing'];
			$b_data_endspeed=$dbo2->db_data['b_data_endspeed'];
			$b_data_text_align=$dbo2->db_data['b_data_text_align'];
			$c_data_x=$dbo2->db_data['c_data_x'];
			$c_data_y=$dbo2->db_data['c_data_y'];
			$c_data_voffset=$dbo2->db_data['c_data_voffset'];
			$c_data_hoffset=$dbo2->db_data['c_data_hoffset'];
			$c_data_speed=$dbo2->db_data['c_data_speed'];
			$c_data_start=$dbo2->db_data['c_data_start'];
			$c_data_easing=$dbo2->db_data['c_data_easing'];
			$c_data_endspeed=$dbo2->db_data['c_data_endspeed'];
			$c_data_text_align=$dbo2->db_data['c_data_text_align'];
			$d_data_x=$dbo2->db_data['d_data_x'];
			$d_data_y=$dbo2->db_data['d_data_y'];
			$d_data_voffset=$dbo2->db_data['d_data_voffset'];
			$d_data_hoffset=$dbo2->db_data['d_data_hoffset'];
			$d_data_speed=$dbo2->db_data['d_data_speed'];
			$d_data_start=$dbo2->db_data['d_data_start'];
			$d_data_easing=$dbo2->db_data['d_data_easing'];
			$d_data_endspeed=$dbo2->db_data['d_data_endspeed'];
			$d_data_text_align=$dbo2->db_data['d_data_text_align'];

			if(!$a_data_x)$a_data_x='center';
			if(!$b_data_x)$b_data_x='center';
			if(!$c_data_x)$c_data_x='center';
			if(!$d_data_x)$d_data_x='center';
			if(!$a_data_y)$a_data_y='center';
			if(!$b_data_y)$b_data_y='center';
			if(!$c_data_y)$c_data_y='center';
			if(!$d_data_y)$d_data_y='center';
			if(!$a_data_easing)$a_data_easing='Back.easeInOut';
			if(!$b_data_easing)$b_data_easing='Back.easeInOut';
			if(!$c_data_easing)$c_data_easing='Back.easeInOut';
			if(!$d_data_easing)$d_data_easing='Back.easeInOut';
			if(!$a_data_speed)$a_data_speed='1000';
			if(!$b_data_speed)$b_data_speed='1000';
			if(!$c_data_speed)$c_data_speed='1000';
			if(!$d_data_speed)$d_data_speed='1000';

			if($_SESSION['ss_ismobile']==1 && $imagem_mobile && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem_mobile))
			{
				$styleTitulo=getModuloTextStyles("tp-caption slider-title sft ".$c_data_text_align,$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles("tp-caption slider-sub-title sfl ".$d_data_text_align,$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
				$info=@dotbright_files_image_size(IMG_FOLDER.'/documentos/'.$imagem_mobile);
 				list($w,$h)=$info;

				$onclick='';
				if($url)$onclick.=' onclick="window.open(\''.$url.'\',\'_self\');" style="cursor:pointer" ';
				$content.='<li data-transition="'.$bloco_img_animacao.'" data-masterspeed="1300" '.$onclick.'>';
				$content.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem_mobile.'" alt="bg" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">';
				if($titulo && !$ocultar_titulo)$content.='<div data-x="'.$c_data_x.'" data-voffset="'.$c_data_voffset.'" data-hoffset="'.$c_data_hoffset.'" data-y="'.$c_data_y.'" data-speed="'.$c_data_speed.'" data-start="'.$c_data_start.'" data-easing="'.$c_data_easing.'" data-endspeed="'.$c_data_endspeed.'" '.$styleTitulo.'>'.$titulo.'</div>';
				if($texto_m)$content.='<div data-x="'.$d_data_x.'" data-voffset="'.$d_data_voffset.'" data-hoffset="'.$d_data_hoffset.'" data-y="'.$d_data_y.'" data-speed="'.$d_data_speed.'" data-start="'.$d_data_start.'" data-easing="'.$d_data_easing.'" data-endspeed="'.$d_data_endspeed.'" '.$styleTexto.'>'.$texto_m.'</div>';
				$content.='</li>';
				$count++;
			}
			else
			{
				$styleTitulo=getModuloTextStyles("tp-caption slider-title sft ".$a_data_text_align,$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles("tp-caption slider-sub-title sfl ".$b_data_text_align,$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
				if($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))
				{
					$info=@dotbright_files_image_size(IMG_FOLDER.'/documentos/'.$imagem);
	 				list($w,$h)=$info;

					$onclick='';
					if($url)$onclick.=' onclick="window.open(\''.$url.'\',\'_self\');" style="cursor:pointer" ';
					$content.='<li data-transition="'.$bloco_img_animacao.'" data-masterspeed="1300" '.$onclick.'>';
					$content.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="bg" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">';
					if($titulo && !$ocultar_titulo)$content.='<div data-x="'.$a_data_x.'" data-voffset="'.$a_data_voffset.'" data-hoffset="'.$a_data_hoffset.'" data-y="'.$a_data_y.'" data-speed="'.$a_data_speed.'" data-start="'.$a_data_start.'" data-easing="'.$a_data_easing.'" data-endspeed="'.$a_data_endspeed.'" '.$styleTitulo.'>'.$titulo.'</div>';
					if($texto)$content.='<div data-x="'.$b_data_x.'" data-voffset="'.$b_data_voffset.'" data-hoffset="'.$b_data_hoffset.'" data-y="'.$b_data_y.'" data-speed="'.$b_data_speed.'" data-start="'.$b_data_start.'" data-easing="'.$b_data_easing.'" data-endspeed="'.$b_data_endspeed.'" '.$styleTexto.'>'.$texto.'</div>';
					$content.='</li>';
					$count++;
				}
			}
			$dbo2->db_close();
			$data='';
			if($Altura)$data.='data-height="'.$Altura.'" ';

			$result.='
			<div class="page-slider-wrap page-slider-wrap'.$ID_Modulo.' modulocss'.$ID_Modulo.'" '.$idattr.' '.$data.'>
				<div class="page-slider page-slider'.$ID_Modulo.' modulo'.$ID_Modulo.'"><ul>'.$content.'</ul><div class="tp-bannertimer tp-bottom"></div></div>
			</div>';
			$result.='</div>
			<script>
			$(function (){
				var data= $(\'.page-slider-wrap'.$ID_Modulo.'\').data();
				if(data == undefined || data.height == undefined || data.height == 0 || data.height == \'\') height='.$h.';
				else height=data.height;
				var arrows=\'none\';
				var bullets=\'none\';
			
				$(\'.page-slider'.$ID_Modulo.'\').revolution({
					delay           :5000,
					startwidth      :'.$w.',
					startheight     :'.$h.',
					hideThumbs      :300,';
					if(!$Altura)$result.='autoHeight		:"on",';
					$result.='touchenabled    :\'on\',';
					$result.='onHoverStop     :\'off\',
					navOffsetHorizontal :0,
					navOffsetVertical   :0,
					navigationVAlign	:\'bottom\',
					navigationVOffset	:80,
					autoplay:0,';
					if($Tem_sombra) $result.='shadow:'.$Tem_sombra.',';
					$result.='fullWidth       :\'on\'
					}).bind(\'revolution.slide.onloaded\',function (e,data) {
						$(this).parent().css({
							background  :\'white\',
							height      :\'auto\',
							overflow    :\'hidden\'
						}).children().animate({opacity :1},500);
					});
				});
			</script>';
		}
		else
		{
			while (!$dbo2->EOF())
			{
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$imagem_mobile=$dbo2->db_data['bloco_img2_ext'];
				$a_data_x=$dbo2->db_data['a_data_x'];
				$a_data_y=$dbo2->db_data['a_data_y'];
				$a_data_voffset=$dbo2->db_data['a_data_voffset'];
				$a_data_hoffset=$dbo2->db_data['a_data_hoffset'];
				$a_data_speed=$dbo2->db_data['a_data_speed'];
				$a_data_start=$dbo2->db_data['a_data_start'];
				$a_data_easing=$dbo2->db_data['a_data_easing'];
				$a_data_endspeed=$dbo2->db_data['a_data_endspeed'];
				$a_data_text_align=$dbo2->db_data['a_data_text_align'];
				$b_data_x=$dbo2->db_data['b_data_x'];
				$b_data_y=$dbo2->db_data['b_data_y'];
				$b_data_voffset=$dbo2->db_data['b_data_voffset'];
				$b_data_hoffset=$dbo2->db_data['b_data_hoffset'];
				$b_data_speed=$dbo2->db_data['b_data_speed'];
				$b_data_start=$dbo2->db_data['b_data_start'];
				$b_data_easing=$dbo2->db_data['b_data_easing'];
				$b_data_endspeed=$dbo2->db_data['b_data_endspeed'];
				$b_data_text_align=$dbo2->db_data['b_data_text_align'];
				$c_data_x=$dbo2->db_data['c_data_x'];
				$c_data_y=$dbo2->db_data['c_data_y'];
				$c_data_voffset=$dbo2->db_data['c_data_voffset'];
				$c_data_hoffset=$dbo2->db_data['c_data_hoffset'];
				$c_data_speed=$dbo2->db_data['c_data_speed'];
				$c_data_start=$dbo2->db_data['c_data_start'];
				$c_data_easing=$dbo2->db_data['c_data_easing'];
				$c_data_endspeed=$dbo2->db_data['c_data_endspeed'];
				$c_data_text_align=$dbo2->db_data['c_data_text_align'];
				$d_data_x=$dbo2->db_data['d_data_x'];
				$d_data_y=$dbo2->db_data['d_data_y'];
				$d_data_voffset=$dbo2->db_data['d_data_voffset'];
				$d_data_hoffset=$dbo2->db_data['d_data_hoffset'];
				$d_data_speed=$dbo2->db_data['d_data_speed'];
				$d_data_start=$dbo2->db_data['d_data_start'];
				$d_data_easing=$dbo2->db_data['d_data_easing'];
				$d_data_endspeed=$dbo2->db_data['d_data_endspeed'];
				$d_data_text_align=$dbo2->db_data['d_data_text_align'];

				if(!$a_data_x)$a_data_x='center';
				if(!$b_data_x)$b_data_x='center';
				if(!$c_data_x)$c_data_x='center';
				if(!$d_data_x)$d_data_x='center';
				if(!$a_data_y)$a_data_y='center';
				if(!$b_data_y)$b_data_y='center';
				if(!$c_data_y)$c_data_y='center';
				if(!$d_data_y)$d_data_y='center';
				if(!$a_data_easing)$a_data_easing='Back.easeInOut';
				if(!$b_data_easing)$b_data_easing='Back.easeInOut';
				if(!$c_data_easing)$c_data_easing='Back.easeInOut';
				if(!$d_data_easing)$d_data_easing='Back.easeInOut';
				if(!$a_data_speed)$a_data_speed='1000';
				if(!$b_data_speed)$b_data_speed='1000';
				if(!$c_data_speed)$c_data_speed='1000';
				if(!$d_data_speed)$d_data_speed='1000';

				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
				if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

				$texto_m=limpa_editor_lixo($dbo2->db_data['bloco_img2_txt']);
				if($texto_m){ob_start();put_content($texto_m);$texto_m=ob_get_contents();ob_end_clean();}

				$url=$dbo2->db_data['bloco_img_url'];
				$url_video=$dbo2->db_data['bloco_img_video'];
				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
				$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];

				if($_SESSION['ss_ismobile']==1 && $imagem_mobile && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem_mobile))
				{
					$styleTitulo=getModuloTextStyles("tp-caption slider-title sft ".$c_data_text_align,$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
					$styleTexto=getModuloTextStyles("tp-caption slider-sub-title sfl ".$d_data_text_align,$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);

					$info=@dotbright_files_image_size(IMG_FOLDER.'/documentos/'.$imagem_mobile);
	 				list($w,$h)=$info;

					$onclick='';
					if($url)$onclick.=' onclick="window.open(\''.$url.'\',\'_self\');" style="cursor:pointer" ';
					$content.='<li data-transition="'.$bloco_img_animacao.'" data-masterspeed="1300" '.$onclick.'>';
					$content.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem_mobile.'" alt="bg" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">';
					if($titulo && !$ocultar_titulo)$content.='<div data-x="'.$c_data_x.'" data-voffset="'.$c_data_voffset.'" data-hoffset="'.$c_data_hoffset.'" data-y="'.$c_data_y.'" data-speed="'.$c_data_speed.'" data-start="'.$c_data_start.'" data-easing="'.$c_data_easing.'" data-endspeed="'.$c_data_endspeed.'" '.$styleTitulo.'>'.$titulo.'</div>';
					if($texto_m)$content.='<div data-x="'.$d_data_x.'" data-voffset="'.$d_data_voffset.'" data-hoffset="'.$d_data_hoffset.'" data-y="'.$d_data_y.'" data-speed="'.$d_data_speed.'" data-start="'.$d_data_start.'" data-easing="'.$d_data_easing.'" data-endspeed="'.$d_data_endspeed.'" '.$styleTexto.'>'.$texto_m.'</div>';
					$content.='</li>';
					$count++;
				}
				else
				{
					$styleTitulo=getModuloTextStyles("tp-caption slider-title sft ".$a_data_text_align,$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
					$styleTexto=getModuloTextStyles("tp-caption slider-sub-title sfl ".$b_data_text_align,$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
					if($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))
					{
						$info=@dotbright_files_image_size(IMG_FOLDER.'/documentos/'.$imagem);
		 				list($w,$h)=$info;

						$onclick='';
						if($url)$onclick.=' onclick="window.open(\''.$url.'\',\'_self\');" style="cursor:pointer" ';
						$content.='<li data-transition="'.$bloco_img_animacao.'" data-masterspeed="1300" '.$onclick.'>';
						$content.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="bg" data-bgfit="cover" data-bgposition="center center" data-bgrepeat="no-repeat">';
						if($titulo && !$ocultar_titulo)$content.='<div data-x="'.$a_data_x.'" data-voffset="'.$a_data_voffset.'" data-hoffset="'.$a_data_hoffset.'" data-y="'.$a_data_y.'" data-speed="'.$a_data_speed.'" data-start="'.$a_data_start.'" data-easing="'.$a_data_easing.'" data-endspeed="'.$a_data_endspeed.'" '.$styleTitulo.'>'.$titulo.'</div>';
						if($texto)$content.='<div data-x="'.$b_data_x.'" data-voffset="'.$b_data_voffset.'" data-hoffset="'.$b_data_hoffset.'" data-y="'.$b_data_y.'" data-speed="'.$b_data_speed.'" data-start="'.$b_data_start.'" data-easing="'.$b_data_easing.'" data-endspeed="'.$b_data_endspeed.'" '.$styleTexto.'>'.$texto.'</div>';
						$content.='</li>';
						$count++;
					}
				}
				$dbo2->db_next();
			}
			$dbo2->db_close();
			if($ID_Modulo==2 && !$Altura)
			{
				// Keep the homepage hero on a stable canvas.  The source banners
				// have different dimensions, so their intrinsic size must not
				// drive the slider height between transitions.
				$w=1560;
				$h=554;
			}

			$data='';

			if($Tem_paginacao)$data.='data-bullets="1" ';
			if($Tem_setas)$data.='data-arrows="1" ';
			if($Altura)$data.='data-height="'.$Altura.'" ';

			/*$result.='
			<div class="page-slider-wrap page-slider-wrap'.$ID_Modulo.' modulocss'.$ID_Modulo.'" '.$idattr.' '.$data.'> <!-- 540 '.$ID_Modulo.' -->
				<div id="memo'.$ID_Modulo.'"  class="page-slider page-slider'.$ID_Modulo.' modulo'.$ID_Modulo.'"><ul>'.$content.'</ul><div class="tp-bannertimer tp-bottom"></div></div>
			</div>';*/
			$result.='
			<div class="page-slider-wrap page-slider-wrap'.$ID_Modulo.' modulocss'.$ID_Modulo.'" '.$idattr.' '.$data.'>
				<div id="memo'.$ID_Modulo.'" class="page-slider page-slider'.$ID_Modulo.' modulo'.$ID_Modulo.'"><ul>'.$content.'</ul><div class="tp-bannertimer tp-bottom"></div></div>
			</div>';
			$result.='</div>
			<script>
			$(function(){
				var data= $(\'.page-slider-wrap'.$ID_Modulo.'\').data();				
				var arrows=\'solo\';
				var bullets=\'bullet\';
				if(data == undefined || data.height == undefined || data.height == 0 || data.height == \'\') height=550;
				else height=data.height;
				if(data == undefined || data.arrows == undefined || data.arrows == 0 || data.arrows == \'\') arrows=\'none\';
				if(data == undefined || data.bullets == undefined || data.bullets == 0 || data.bullets == \'\') bullets=\'none\';
				
				$(\'.page-slider'.$ID_Modulo.'\').revolution({
					delay           	:5000,
					startheight     	:'.$h.',
					hideThumbs      	:300,
					touchenabled    	:\'on\',
					onHoverStop     	:\'on\',
					startwidth      	:'.$w.',
					navOffsetHorizontal :0,
					navOffsetVertical   :0,
					navigationVAlign	:\'bottom\',
					navigationVOffset	:80,
					minFullScreenHeight :\'320\',
					navigationType		:bullets,
					navigationArrows	:arrows,
					fullScreen 			:"off",				
					autoplay 			:1,';
					if(!$Altura)$result.=($ID_Modulo==2 ? 'autoHeight:"off",  ' : 'autoHeight:"on",  ');
					//navigationType:"bullet",				// bullet,thumb,none
					//navigationArrows:"solo",				// nextto,solo,none
					//navigationInGrid:"off",				// on/off
					if($Tem_sombra) $result.='shadow:'.$Tem_sombra.',';

					$result.='fullWidth       :\'on\'
					}).bind(\'revolution.slide.onloaded\',function (e,data) {
							$(this).parent().css({
								background  :\'white\',
								height      :\'auto\',
								overflow    :\'hidden\'
							}).children().animate({opacity :1},500);
				});
			});
			$(window).on("load", function(){
				var img_lat = ($(".modulocss3").height());
				var img_cen = ($("#id_modulo2").height() );
				var tam_menu = ($("#menu-slider").height() );
			 	$(\'.menu_img \').css(\'paddingTop\', (img_lat - img_cen - tam_menu) );
			});
			$(window).resize(function(){
				var img_lat = ($(".modulocss3").height());
				var img_cen = ($("#id_modulo2").height() );
				var tam_menu = ($("#menu-slider").height() );
			 	$(\'.menu_img \').css(\'paddingTop\', (img_lat - img_cen - tam_menu) );
			});
			</script>';
		}
	}
	$dbo->db_close();
	$result.='<div class="clear"></div>';
	return $result;
}

function showModuloTextoCentrado($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF()) {
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Largura=$dbo->db_data['Largura'];

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles("text-center",$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';
		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			$styleBloco.='"';
		}
		if(!$Largura)$result='<div class="row" '.$styleBloco.'><div class="container fix_container" style="overflow:hidden">';
		else $result='<div class="row" '.$styleBloco.'><div>';

		$result.='<div '.$idattr.'>';
		if($titulo && !$ocultar_titulo)$result.='<div class="title text-center"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';

		$result.='<div class="title">';
		if($resumo)$result.='<div '.$styleResumo.'>'.$resumo.'</div>';
		$result.='</div>';
		$result.='</div>';
		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			if($styleBloco)$result.='<div class="container-out container-image container-no-bottom" >';
			else $result.='<div>';
			while (!$dbo2->EOF())
			{
				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];

				$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
				if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
				$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
				$anim='';
				$styleTitulo=getModuloTextStyles("title text-center",$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);

				if($bloco_img_animacao) $anim=' data-animate="'.$bloco_img_animacao.'"';
				if($titulo && !$ocultar_titulo)$result.='<div '.$styleTitulo.'><h2>'.$titulo.'</h2></div>';
				if($texto) $result.='<div '.$styleTexto.'>'.$texto.'</div>';
				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) $result.='<div class="thumbnail" '.$anim.'><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$titulo.'"></div>';
				$dbo2->db_next();
			}
			if($styleBloco)$result.='</div>';
			$result.='</div>
			</div>
			<div class="clear"></div>';
		}
		$dbo2->db_close();
	}
	$dbo->db_close();

	return $result;
}

function showModuloTextoLadoaLado($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Qtd=$dbo->db_data['Qtd'];
		$Largura=$dbo->db_data['Largura'];
		$Estilo_Back=$dbo->db_data['Estilo_Back'];

		if(!$Qtd)$Qtd=3;
		/*if($Qtd == 2)$class=' class="col-sm-6" ';
		if($Qtd == 3)$class=' class="col-lg-4" ';
		if($Qtd == 4)$class=' class="col-sm-3" ';*/
		if($Qtd == 2)$class=' class="col-lg-6 col-md-6 col-sm-6 col-xs-12" ';
		if($Qtd == 3)$class=' class="col-lg-4 col-md-4 col-sm-6 col-xs-12" ';
		if($Qtd == 4)$class=' class="col-lg-3 col-md-3 col-sm-6 col-xs-12" ';



	    /*Extra small (for smartphones .col-xs-*)
	    Small (for tablets .col-sm-*)
	    Medium (for laptops .col-md-*)
	    Large (for laptops/desktops .col-lg-*).*/



		// class="col-sm-6" --> 2 colunas
		// class="col-sm-5" --> 2 colunas
		// class="col-sm-4" --> 3 colunas
		// class="col-sm-3" --> 4 colunas
		// class="col-sm-2" --> 6 colunas
		// class="col-sm-1" --> 7 colunas

		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back==1)$styleBloco.='  background-attachment: fixed; ';
			if($Estilo_Back==2)$styleBloco.='  background-position: 50% 0; ';
			if($Estilo_Back==3)$styleBloco.='  background-size: cover; ';
			if($Estilo_Back==4)$styleBloco.='  background-attachment: fixed; background-size: cover; ';
			$styleBloco.='"';
		}
		if($Estilo_Back==2)
		{
			if($Largura)$result='<div class="row paralax" '.$styleBloco.'>';
			else $result='<div class="paralax" '.$styleBloco.'>';
		}
		else
		{
			if($Largura)$result='<div class="row" '.$styleBloco.'>';
			else $result='<div '.$styleBloco.'>';
		}

		//if(!$Largura)$result.='<div class="container fix_container" style="overflow:hidden">';
		if(!$Largura)$result.='<div class="container fix_container">';
		else $result.='<div style="width:100%">';

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=(limpa_editor_lixo(stripslashes($dbo2->db_data['texto'])));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$styleTexto=getModuloTextStyles('',$dbo->db_data['Texto2_cor'],$dbo->db_data['Texto2_tamanho'],$dbo->db_data['Texto2_font_weight'],$dbo->db_data['Texto2_line_height']);

		$idattr='';
		if($Anchor)	$idattr='id="'.$Anchor.'_bloco" ';


		if($styleBloco)$result.='<div class="container-out container-image container-no-bottom">';

		$result.='<div '.$idattr.'>';

		$result.='<div class="title title-section">';
		if($titulo && !$ocultar_titulo)$result.='<h2 '.$styleTitulo.'>'.$titulo.'</h2>';
		$result.='</div>';
		if($resumo)$result.='<div '.$styleResumo.'>'.$resumo.'</div>';
		if($texto)$result.='<div '.$styleTexto.'>'.$texto.'</div>';

		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			while (!$dbo2->EOF())
			{
				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
				if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

				$url=$dbo2->db_data['bloco_img_url'];
				$url_video=$dbo2->db_data['bloco_img_video'];
				$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
				$styleTitulo=getModuloTextStyles("entry-title",$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
				$anim='';
				if($bloco_img_animacao)$anim=' data-animate="'.$bloco_img_animacao.'"';
				if($url)$result.='<a href="'.$url.'">';
				$result.='<div '.$class.' '.$anim.'>';
				$titulo_x='';
				if(!$ocultar_titulo)$titulo_x=$titulo;

				if(isset($imagem) && $imagem !='' && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.=gerar_thumbnail(IMG_FOLDER.'/documentos/'.$imagem,'',$url,$url_video,'','','',1);
				if($titulo && !$ocultar_titulo)$result.='<h3 '.$styleTitulo.'>'.$titulo.'</h3>';
				if($texto)$result.='<div class="text"><p '.$styleTexto.'>'.$texto.'</p></div>';
				$result.='</div>';
				if($url)$result.='</a>';

				$dbo2->db_next();
			}
			$dbo2->db_close();
		}
		$dbo2->db_close();
		$result.='</div>';
		if($styleBloco)$result.='</div>';
		$result.='</div>
		</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();
	return $result;
}

function showModuloConteudos($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Largura=$dbo->db_data['Largura'];

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=(limpa_editor_lixo(stripslashes($dbo2->db_data['texto'])));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$texto3=(limpa_editor_lixo(stripslashes($dbo2->db_data['texto3'])));
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';

		//$result= '<div class="container fix_container" style="overflow:hidden">';
		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			$styleBloco.='"';
		}

		if(!$Largura)$result='<div '.$idattr.' class="row" '.$styleBloco.'><div class="container fix_container" style="overflow:hidden">';
		else $result='<div '.$idattr.' class="row" '.$styleBloco.'><div>';
//DIV DIV

		if($styleBloco)$result.='<div class="container-out container-image container-no-bottom">';
		$result.='<div class="col-sm-12">';
//DIV
		if($titulo && !$ocultar_titulo)$result.='<div class="title text-center"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';
		//*********old stuff:
		$result.='<div class="title">';
		if($resumo)$result.='<div><p '.$style_texto1.'>'.$resumo.'</p></div>';
		if($texto)$result.='<div><p '.$style_texto2.'>'.$texto.'</p></div>';
		if($texto3)$result.='<div><p '.$style_texto3.'>'.$texto3.'</p></div>';
		$result.='</div>';
		//******************************************************************************
		$dbo2=getModuloImgs($ID_Modulo);
		while (!$dbo2->EOF())
		{
			$titulo=$dbo2->db_data['bloco_img_titulo'];
			$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
			$imagem=$dbo2->db_data['bloco_img_ext'];
			$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$url=$dbo2->db_data['bloco_img_url'];
			$url_video=$dbo2->db_data['bloco_img_video'];
			$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
			$styleTitulo=getModuloTextStyles('',$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
			$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
			$url_final='';
			$class='';
			if($url_video)
			{
				$url_final=$url_video;
				$class='class="lightbox" data-fancybox-type="iframe" ';
			}
			elseif($url)
			{
				$url_final=$url;
			}
			$anim='';
			if($bloco_img_animacao)$anim=' data-animate="'.$bloco_img_animacao.'"';
			$result.='<article class="post" '.$anim.'>';
			//if($imagem && stream_resolve_include_path(IMG_FOLDER.'/documentos/'.$imagem)) $result.='<div class="entry-heading"><div class="thumbnail" '.$anim.'><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt=""></div></div>';

			if($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.=gerar_thumbnail(IMG_FOLDER.'/documentos/'.$imagem,'',$url,$url_video,'','','');

			$result.='<div class="entry-content">';
			if($titulo && !$ocultar_titulo)
			{
				if($url_final)$result.='<div class="title"><h2 class="h4"><a href="'.$url_final.'" '.$styleTitulo.'>'.$titulo.'</a></h2></div>';
				else $result.='<h3 class="entry-title">'.$titulo.'</h3>';
			}
			if($texto)$result.='<div class="text"><p '.$styleTexto.'>'.$texto.'</p></div>';

			$result.='</div>';
			$result.='</article>';

			$dbo2->db_next();
		}
		$dbo2->db_close();
		$result.='</div>';
		if($styleBloco)$result.='</div>';

		$result.='</div>
		</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloDestaques($ID_Modulo)
{
	$result='';
	$str='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Posicao=$dbo->db_data['Posicao'];
		$Qtd=$dbo->db_data['Qtd'];
		$Layout=$dbo->db_data['Layout'];
		$Largura=$dbo->db_data['Largura'];

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['texto'])));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$texto3=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['texto3'])));
		}
		$dbo2->db_close();

		$styleTitulo=getModuloTextStyles($dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles($dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);

		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';

if(!$Largura)$result='<div class="row" '.$styleBloco.'><div class="container fix_container" style="overflow:hidden">';
else $result='<div class="row" '.$styleBloco.'><div>';

		if($titulo && !$ocultar_titulo)$str.='<header class="text-center"><h1 '.$style_titulo.'>'.$titulo.'</h1></header>';

		$str.='<section class="posts" '.$idattr.'>';

		/***TEXTO SIMPLES CENTRADO*****/
		if($resumo || $texto || $texto3)
		{
			$str.='<div>';
			$str.='<div class="title">';
			if($resumo)$str.='<div><p '.$style_texto1.'>'.$resumo.'</p></div>';
			if($texto)$str.='<div><p '.$style_texto2.'>'.$texto.'</p></div>';
			if($texto3)$str.='<div><p '.$style_texto3.'>'.$texto3.'</p></div>';
			$str.='</div>';
			$str.='</div>';
		}

		$rs2=getModuloImgs($ID_Modulo);
		$c=0;
		while (!$rs2->EOF())
		{
			$c++;
			$anim='';
			$imagem=$rs2->db_data['bloco_img_ext'];
			$titulo=$rs2->db_data['bloco_img_titulo'];
			$ocultar_titulo=$rs2->db_data['bloco_img_titulo_visivel'];
			$txt=limpa_editor_lixo($rs2->db_data['bloco_img_txt']);
			if($txt){ob_start();put_content($txt);$txt=ob_get_contents();ob_end_clean();}
			$bloco_img_animacao=$rs2->db_data['bloco_img_animacao'];
			$bloco_titulo_cor=$rs2->db_data['bloco_img_titulo_cor'];
			$bloco_titulo_tamanho=$rs2->db_data['bloco_img_titulo_tamanho'];
			$bloco_titulo_font_weight=$rs2->db_data['bloco_img_titulo_font_weight'];
			$bloco_texto_cor=$rs2->db_data['bloco_img_txt_cor'];
			$bloco_texto_tamanho=$rs2->db_data['bloco_img_txt_tamanho'];
			$bloco_texto_line_height=$rs2->db_data['bloco_img_txt_line_height'];
			$bloco_texto_font_weight=$rs2->db_data['bloco_img_txt_font_weight'];
			$bloco_img_layout=$rs2->db_data['bloco_img_layout'];

			$bloco_style_titulo='';
			if($bloco_titulo_cor || $bloco_titulo_tamanho || $bloco_titulo_font_weight)
			{
				if($bloco_titulo_cor)$str_bloco_titulo_cor='color:'.$bloco_titulo_cor.';';
				if($bloco_titulo_tamanho)$str_bloco_titulo_tamanho='font-size:'.$bloco_titulo_tamanho.'px;';
				if($bloco_titulo_font_weight)$str_bloco_titulo_font_weight='font-weight:'.$bloco_titulo_font_weight.';';
				$bloco_style_titulo=' style="'.$str_bloco_titulo_cor.$str_bloco_titulo_tamanho.$str_bloco_titulo_font_weight.'"';
			}
			$bloco_style_texto='';
			if($bloco_texto_cor || $bloco_texto_tamanho || $bloco_texto_line_height || $bloco_texto_font_weight)
			{
				if($bloco_texto_cor)$str_bloco_texto_cor='color:'.$bloco_texto_cor.';';
				if($bloco_texto_tamanho)$str_bloco_texto_tamanho='font-size:'.$bloco_texto_tamanho.'px;';
				if($bloco_texto_line_height)$str_bloco_texto_line_height='line-height:'.$bloco_texto_line_height.'px;';
				if($bloco_texto_font_weight)$str_bloco_texto_font_weight='font-weight:'.$bloco_texto_font_weight.';';
				$bloco_style_texto=' style="'.$str_bloco_texto_cor.$str_bloco_texto_tamanho.$str_bloco_texto_font_weight.$str_bloco_texto_line_height.'"';
			}
			if($bloco_img_animacao)$anim=' data-animate="'.$bloco_img_animacao.'"';
			//$str.='<article class="post post-thumb post-type-gallery devider-top" '.$anim.'>';
			$str.='<article class=" post post-thumb post-type-gallery" '.$anim.'>';
			$bloco=0;
			if(!$Layout)$bloco=1;
			if($Layout == 2 && $c % 2 != 0)$bloco=1;

			$col1='col-md-6 col-sm-12 col-xs-12';
			$col2='col-md-6 col-sm-12 col-xs-12';
			if(!$bloco_img_layout)
			{
				$col1='col-md-6 col-sm-12 col-xs-12';
				$col2='col-md-6 col-sm-12 col-xs-12';
			}
			if($bloco_img_layout == 1)
			{
				$col1='col-md-4 col-sm-12 col-xs-12';
				$col2='col-md-8 col-sm-12 col-xs-12';
			}
			if($bloco_img_layout == 2)
			{
				$col1='col-md-8 col-sm-12 col-xs-12';
				$col2='col-md-4 col-sm-12 col-xs-12';
			}

			$info=@dotbright_files_image_size(IMG_FOLDER.'/documentos/'.$imagem);
			list($w,$h)=$info;

			if($bloco == 1) //Imagem à esq,texto à dir
			{
				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$str.='<div class="'.$col1.' vcenter"><div class="thumbnail"><img style="min-width:0px;max-width:'.$w.'px" src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
				$str.='<div class="'.$col2.' vcenter">';
				if($titulo && !$ocultar_titulo)$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
				if($txt)$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
				$str.='</div>';
			}
			else //Imagem à dir,texto à esq
			{
				$str.='<div class="'.$col1.' vcenter">';
				if($titulo && !$ocultar_titulo)$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
				if($txt)$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
				$str.='</div>';

				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$str.='<div class="'.$col2.' vcenter"><div class="thumbnail"><img style="max-width:'.$w.'px" src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
			}

			$str.='</article>';

			$rs2->db_next();
		}
		$rs2->db_close();

		$result.=$str;

		$result.='</section>'.'</div></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloBreadcrumbs($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$Largura=$dbo->db_data['Largura'];
		$Altura=$dbo->db_data['Altura'];

		if(!$Largura)$result='<div class="container fix_container"';
		else $result='<div';

		if($Altura)$result.=' style="height:'.$Altura.'px"';
		$result.='>';

		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';

		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=$dbo2->db_data['bloco_img_titulo'];
			$imagem=$dbo2->db_data['bloco_img_ext'];
			$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
			if($imagem)
			{
				$result.='<div '.$idattr.' class="page-header-image" style="background-image:url('.IMG_FOLDER.'/documentos/'.$imagem.')';
				if($Altura)
				{
					$result.=';height:'.$Altura.'px';
				}
				$result.='">';
			}
			else
			{
				if($idattr != '') $result.='<div '.$idattr.'>';
				else $result.='<div>';
			}

			$styleTitulo=getModuloTextStyles('',$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
			$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
			if($titulo && !$ocultar_titulo || $texto)
			{
				$result.='<div class="page-header-inner">';
				$result.='<div class="page-header-content">';
				if(($titulo!='' && $titulo!=' ') && !$ocultar_titulo)$result.='<h2 '.$styleTitulo.'>'.$titulo.'</h2>';
				if($texto)$result.='<p '.$styleTexto.'>'.$texto.'</p>';

				$result.='</div>';
				$result.='</div>';
			}
			$result.='<div class="breadcrumbs hide-to-bottom">';
			$result.='<div class="container"';
			if($Altura)$result.=' style="padding-top:'.($Altura - 43).'px"';

			$result.='>';
			$result.='<ul>'.showBreadcrumbsPages().'</ul>';
			$result.='</div>';
			$result.='</div>';
			$result.='</div>';
			$result.='</div>';
		}
		$result.='<div class="clear"></div>';
		$dbo2->db_close();
	}
	$dbo->db_close();

	return $result;
}

function showModuloGaleria($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Qtd=$dbo->db_data['Qtd'];
		$Largura=$dbo->db_data['Largura'];
		$Estilo=$dbo->db_data['Estilo'];
		$Estilo_Back=$dbo->db_data['Estilo_Back'];
		$Anchor=$dbo->db_data['Anchor'];
		if(!$Largura)$result='<div class="container fix_container">';
		else $result='<div>';


		// echo 'ancora: '.$Anchor;


		if(!$Qtd)$Qtd=3;

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$styleBloco='';$img_top = '';
		if($img_ext && dotbright_image_resolve('files/documentos/'.$img_ext))
		{
			// $styleBloco=' style="background-image:url(files/documentos/'.$img_ext.'); ';
			// if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			// $styleBloco.='"';
			$img_top = '<img src="files/documentos/'.$img_ext.'" border="0">';
		}
		if(!$Largura)
		{
			$result='<div class="row" '.$styleBloco.'>
			<div class="container fix_container">';
		}
		else
		{
			$result='<div class="row" '.$styleBloco.'>
			<div>';
		}

		if($Anchor != '')
			$result.='<div id="'.$Anchor.'_bloco">';
		else
			$result.='<div>';

		if($styleBloco)$result.='<div class="container-out container-image container-no-bottom">';


		if(($titulo && !$ocultar_titulo) || $resumo)
		{
			// $result.='<div>';
			if($img_top != '') $result .= '<div class="title frame"><h2 '.$styleTitulo.'>'.$img_top.'</h2></div>';
			else if($titulo && !$ocultar_titulo) $result .= '<div class="title frame"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';
			// $result.='<div class="title">';

				if($resumo)$result.='<div><p '.$styleResumo.'>'.$resumo.'</p></div>';

			// $result.='</div>';

			// $result.='</div>';
		}


		//Mas se tiver imagens tb vamos mostrar
		$result.='<div class="grid-wrap" style="padding:15px">';


		if($Estilo==1)
		{
			//vamos anular os espaços entro banners
			$result.='<section id="projects" class="grid" data-columns="'.$Qtd.'" data-masonry=\'{ "gutterWidth":3 }\' >';
		}
		else $result.='<section id="projects" class="grid" data-columns="'.$Qtd.'">';
		$dbo2 = getModuloImgs($ID_Modulo);
		while (!$dbo2->EOF())
		{
			$anim = '';
			$imagem = $dbo2->db_data['bloco_img_ext'];
			$txt = $dbo2->db_data['bloco_img_txt'];
			if($txt){ob_start();put_content($txt);$txt=ob_get_contents();ob_end_clean();}
			$titulo = $dbo2->db_data['bloco_img_titulo'];
			$bloco_img_animacao=  $dbo2->db_data['bloco_img_animacao'];
			$ocultar_titulo = $dbo2->db_data['bloco_img_titulo_visivel'];
			// $url=seo($dbo2->db_data['bloco_img_url']);
			$url = ($dbo2->db_data['bloco_img_url_default'] == 1 ? $GLOBALS['site_http_url'].'pg'.$GLOBALS['modulos_page_id'].'-m'.$bloco_img_id.'-'.limpa_special($titulo).'-'.$GLOBALS['site_lang'] : seo($dbo2->db_data['bloco_img_url']));
			$url_video=$dbo2->db_data['bloco_img_video'];
			$anim='';

			if($bloco_img_animacao)
			{
				// $anim =' data-animate="'.$bloco_img_animacao.'"';
			}
			$result.='<article class="project project-animated">';

			if($Estilo==1) $result.='<div class="project-heading grayscale">';
			else $result.='<div class="project-heading">';

			// if($titulo) $result .= '<div class="title" style="margin-top:10px !important;margin-bottom:10px !important;"><h4 style="font-size:14px;color:#000 !important">'.$titulo.'</h4></div>';
			// $result .= '<div '.$styleTexto.'>'.$txt.'</div>';


			$titulo_x='';
			if(!$ocultar_titulo)$titulo_x=$titulo;

			if($imagem)$url_img='files/documentos/'.$imagem;
			//$result .= gerar_thumbnail2($url_img,$anim,$url,$url_video,$titulo_x,$txt,$styleTitulo,$styleTexto,0,$Estilo);
			$result .= gerar_thumbnail_resize($url_img,$anim,$url,$url_video,$titulo_x,$txt,$styleTitulo,$styleTexto,0,$Estilo);

			$result.='</article>';
			$dbo2->db_next();
		}
		$dbo2->db_close();
		$result.='</section>';
		$result.='</div>';
		if($styleBloco)
		{
			$result.='</div>';
		}

		if(($titulo && !$ocultar_titulo) || $resumo)
		{
			// $result.='<div>';
			 if($titulo && !$ocultar_titulo) $result .= '<div class="title frame2">&nbsp;</div>';

		}
		$result.='</div>';
		$result.='</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}
function gerar_thumbnail_resize($img='', $anim='', $url='', $url_video='', $titulo='', $txt='', $styleTitulo='', $styleTexto='', $linkNormal=0, $Estilo=0)
{
	$r = '';
	$r .= '<div '.$anim.' style="text-align:center;display:inline-block;">';
	if($url != '') $r .= '<a href="'.$url.'">';

	$img_file = $img;
	$ext = pathinfo($img_file, PATHINFO_EXTENSION);
	$img_file_med2 = str_replace('.'.$ext, '_resize.'.$ext, $img_file);

	list($width, $height, $type, $attr) = dotbright_files_image_size($img_file);

	geraimagens($img_file, $img_file_med2, $width, 0, array('ativo' => 1, 'w' => 278, 'h' => 177),0,0,array(),'',0,0);

	if(dotbright_image_resolve($img_file_med2))
	{
		$r .= '<img src="'.$img_file_med2.'" alt="'.$titulo.'" border="0" />';
	}


	if($titulo)
	{
		$r .= '<div class="project-content">';

		$r .= '<h3 class="resize">'.$titulo.'</h3>';
		if($txt) $r.= '<div class="text"><p class="resize2">'.$txt.'</p></div>';

		$r .= '</div>';
	}
	if($url != '') $r .= '</a>';
	$r .= '</div>';
	return $r;
}
function gerar_thumbnail2($img='', $anim='', $url='', $url_video='', $titulo='', $txt='', $styleTitulo='', $styleTexto='', $linkNormal=0, $Estilo=0)
{
	$r = '';
	$r .= '<div '.$anim.' style="text-align:center;display:inline-block;">';
	if($url != '') $r .= '<a href="'.$url.'">';
	if(dotbright_image_resolve($img))
	{
		$r .= '<img src="'.$img.'" alt="'.$titulo.'" border="0" />';
	}


	if($titulo)
	{
		$r .= '<div class="project-content">';

		$r .= '<h3 class="resize">'.$titulo.'</h3>';
		if($txt) $r.= '<div class="text"><p class="resize2">'.$txt.'</p></div>';

		$r .= '</div>';
	}
	if($url != '') $r .= '</a>';
	$r .= '</div>';
	return $r;
}
function gerar_thumbnail($img='',$anim='',$url='',$url_video='',$titulo='',$txt='',$styleTitulo='',$styleTexto='',$linkNormal=0,$Estilo=0)
{
	$r='';
	if(dotbright_image_resolve($img))
	{
		$info=@dotbright_files_image_size($img);
		list($w,$h)=$info;

		$r.='<div '.$anim.' style="text-align:center" class="img-content2">';
		$r.='<img src="'.$img.'" alt="'.$titulo.'">';
		$r.='</div>';
	}

	if($url || $url_video)$r.='<ul class="project-action">';
	else $r.='<ul>';
	if($url)
	{
		if($linkNormal == 0)
		{
			$r.='<li><a class="lightbox btn btn-icon-view" href="'.$img.'" data-fancybox-title="<h4 '.$styleTitulo.'>'.$titulo.'</h4><p '.$styleTexto.'>'.$txt.'</p><p class=\'text-right\'><a href=\''.$url.'\' class=\'btn\'>Open URL</a></p>" data-fancybox-group="portfolio"></a></li>
			<li><a class="link btn btn-icon-link" href="'.$url.'"></a></li>';
		}
	}
	elseif($url_video)
	{
		$r.='<li><a class="lightbox btn btn-icon-link" href="'.$url_video.'" data-fancybox-type="iframe"></a></li>';

	}
	$r.='</ul>';
	if($titulo)
	{
		$r.='<div class="project-content">';

		if($url)$r.='<div class="title"><h2 class="h4"><a href="'.$url.'" '.$styleTitulo.'>'.$titulo.'</a></h2></div>';
		else $r.='<h3 '.$styleTitulo.'>'.$titulo.'</h3>';
		$r.='<div class="blueline2"></div>';
		if($txt)$r.='<div class="text"><p '.$styleTexto.'>'.$txt.'</p></div>';
		$r.='</div>';
	}

	return $r;
}
function gerar_thumbnail3($img='',$anim='',$url='',$url_video='',$titulo='',$txt='',$styleTitulo='',$styleTexto='',$linkNormal=0,$Estilo=0)
{
	$r='';
	$r.='<div class="project-content">';
	if(dotbright_image_resolve($img))
	{
		$info=@dotbright_files_image_size($img);
		list($w,$h)=$info;

		$r.='<div '.$anim.' style="text-align:center" class="img-content2">';
		$r.='<img src="'.$img.'" alt="'.$titulo.'">';
		$r.='</div>';
	}

	if($url || $url_video)$r.='<ul class="project-action">';
	else $r.='<ul>';
	if($url)
	{
		if($linkNormal == 0)
		{
			$r.='<li><a class="lightbox btn btn-icon-view" href="'.$img.'" data-fancybox-title="<h4 '.$styleTitulo.'>'.$titulo.'</h4><p '.$styleTexto.'>'.$txt.'</p><p class=\'text-right\'><a href=\''.$url.'\' class=\'btn\'>Open URL</a></p>" data-fancybox-group="portfolio"></a></li>
			<li><a class="link btn btn-icon-link" href="'.$url.'"></a></li>';
		}
	}
	elseif($url_video)
	{
		$r.='<li><a class="lightbox btn btn-icon-link" href="'.$url_video.'" data-fancybox-type="iframe"></a></li>';

	}
	$r.='</ul>';
	if($titulo)
	{

		if($url)$r.='<div class="title"><h2 class="h4"><a href="'.$url.'" '.$styleTitulo.'>'.$titulo.'</a></h2></div>';
		else $r.='<h3 '.$styleTitulo.'>'.$titulo.'</h3>';
		$r.='<div class="blueline2"></div>';
		if($txt)$r.='<div class="text"><p '.$styleTexto.'>'.$txt.'</p></div>';
	}

	$r.='</div>';
	return $r;
}
function showModuloGaleria_($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);

	if (!$dbo->EOF()) {
		$Anchor         = $dbo->db_data['Anchor'];
		$img_ext        = $dbo->db_data['img_ext'];
		$ocultar_titulo = $dbo->db_data['titulo_visivel'];
		$Qtd            = $dbo->db_data['Qtd'];
		$Largura        = $dbo->db_data['Largura'];
		$Estilo         = $dbo->db_data['Estilo'];
		$Estilo_Back    = $dbo->db_data['Estilo_Back'];

		$titulo='';
		$resumo='';

		if (!$Qtd) $Qtd = 3;

		$dbo2=getModuloLang($ID_Modulo);

		if(!$dbo2->EOF()) {
			$titulo = limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo = (limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));

			if ($resumo) {
				ob_start();
				put_content($resumo);
				$resumo=ob_get_contents();
				ob_end_clean();
			}
		}

		$dbo2->db_close();

		$styleTitulo = getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo = getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$styleBloco = '';

		// if ($img_ext && stream_resolve_include_path(IMG_FOLDER.'/documentos/'.$img_ext)) {
		// 	$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
		// 	if($Estilo_Back) {
		// 		$styleBloco.='  background-attachment: fixed; ';
		// 	}
		// 	$styleBloco .= '"';
		// }
		// if (!$Largura) {
		// 	$result .= '<div class="row" '.$styleBloco.'>
		// 					<div '.$idattr.' class="container">';
		// }
		// else {
		// 	$result.='<div class="row" '.$styleBloco.'>
		// 				<div '.$idattr.' class="container-fluid">';
		// }
		// if ($styleBloco) $result .= '<div class="container-out container-image container-no-bottom">';
		// if (($titulo && !$ocultar_titulo) || $resumo) {
		// 	$result .= '<div>';
		// 	if ($titulo && !$ocultar_titulo) $result .= '<div class="maintitle"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';
		// 	$result .= '<div class="title">';
		// 	if ($resumo) $result .= '<div><p ' . $styleResumo . '>' . $resumo . '</p></div>';
		// 	$result .= '</div>';
		// 	$result .= '</div>';
		// }
		// Mas se tiver imagens tb vamos mostrar
		// $result .= '<div class="grid-wrap">';

		?>
		<link rel="stylesheet" type="text/css" href="layout/memogadgetcms/modulos/galeria/css/galeria.css">
		<?php

		if ($Anchor != '') $result .= '<div id="' . $Anchor . '">';

		if(!$Largura) {
			$result = '<div class="container">';
		}
		else $result = '<div class="container-fluid">';


		$result .= '
				   		<div class="content">
				   			<div class="grid-wrap">
				   				<section id="projects" class="grid" data-columns="'.$Qtd.'" data-masonry=\'{ "gutterWidth":3 }\' >';

		$dbo2 = getModuloImgs($ID_Modulo);

		while (!$dbo2->EOF()) {
			$anim   = '';
			$imagem = $dbo2->db_data['bloco_img_ext'];
			$txt    = $dbo2->db_data['bloco_img_txt'];

			if ($txt) {
				ob_start();
				put_content($txt);
				$txt = ob_get_contents();
				ob_end_clean();
			}

			$titulo             = $dbo2->db_data['bloco_img_titulo'];
			$bloco_img_animacao = $dbo2->db_data['bloco_img_animacao'];
			$ocultar_titulo     = $dbo2->db_data['bloco_img_titulo_visivel'];
			$url                = $dbo2->db_data['bloco_img_url'];
			$url_video          = $dbo2->db_data['bloco_img_video'];
			$anim               = '';

			// if ($bloco_img_animacao) {
			// 	$anim =' data-animate="'.$bloco_img_animacao.'"';
			// }
			// if ($Estilo==1) $result.='<div class="project-heading grayscale">';
			// else $result.= '<div class="project-heading aaa">';
			// if ($titulo){
			// 	$result.='<div class="title" style="margin-top:10px !important;margin-bottom:10px !important;">
			// 			  	<h4  style="font-size:14px;color:#000 !important">'.$titulo.'</h4>
			// 			  </div>';
			// }
			// $result.= '<div '.$styleTexto.' class="atext">'.$txt.'</div>';//'.$styleTitulo.'
			// $titulo_x='';
			// if (!$ocultar_titulo) $titulo_x = $titulo;
			// $result.= gerar_thumbnail($url_img,$anim,$url,$url_video,$titulo_x,$txt,$styleTexto,0,$Estilo);
			$result .= '<article class="project project-animated illustration design">
							<div class="project-heading">';

			if ($imagem) {
				$url_img = ''.IMG_FOLDER.'/documentos/' . $imagem;

				$result .= '<div class="thumbnail">
								<img src="' . $url_img . '" alt="Image">
					  		</div>';
			}

			$result .= '<ul class="project-action">
							<li>
								<a class="lightbox btn btn-icon-view" href="' . $url_img . '" data-fancybox-title="
									<h4>' . $titulo . '</h4>
									<p>' . $txt . '</p>
									<p class=\'text-right\'>
										<a href=\'portfolio-single.html\' class=\'btn\'>Ver</a>
									</p>" data-fancybox-group="portfolio">
								</a>
							</li>
							<li>
								<a class="link btn btn-icon-link" href="portfolio-single.html"></a>
							</li>
				    	</ul>
				    </div>
					<div class="project-content">
						<div class="title">
							<h2 class="h5">' . $titulo . '</h2>
							<p class="desc">' . $txt . '</p>
						</div>
					</div>
			</article>';

			$dbo2->db_next();
		}
		$dbo2->db_close();

		$result .= '</section>
				</div>';

		// if ($styleBloco) {
		// 	$result.='</div>';
		// }

		$result .= '</div>
				</div>
			</div>
		</div>
			<div class="clear"></div>';

		if ($Anchor) $result .= '</div>';

		$return .= '<script type="text/javascript" src="layout/memogadgetcms/modulos/galeria/js/jquery.fancybox.pack.js"></script>';
	}
	$dbo->db_close();

	echo $result;
}

function gerar_thumbnail_($img='',$anim='',$url='',$url_video='',$titulo='',$txt='',$styleTexto='',$linkNormal=0,$Estilo=0)
{
	$r='';
	if(dotbright_image_resolve($img))
	{
		$info=@dotbright_files_image_size($img);
		list($w,$h)=$info;

		$r.='<div class="thumbnail" '.$anim.'>';
		$r.='<figure class="img-figure">';
		$r.='<img style="width:100%;max-width:'.$w.'px" src="'.$img.'" alt="'.$titulo.'">';
		$r.='</figure>';
		$r.='</div>';
	}


	if($titulo)
	{
		//$r.='</div>';
		$r.='<div class="project-content">';

		// if($url)$r.='<div class="title"><h2 class="h4"><a href="'.$url.'">'.$titulo.'</a></h2></div>';
		// else $r.='<h3 class="project-title">'.$titulo.'</h3>';
		$r.='<h3 class="project-title">'.$titulo.'</h3>';
		// var_dump($txt);
		if($txt)$r.='<div class="text"><p '.$styleTexto.'>'.$txt.'</p></div>';
		$r.='</div>';
	}

	return $r;

	/*
	if($url)
	{
		$result.='<ul class="project-action">
		<li><a class="lightbox btn btn-icon-view" href="'.IMG_FOLDER.'/documentos/'.$imagem.'" data-fancybox-title="<h4>'.$titulo.'</h4><p>'.$texto.'</p><p class=\'text-right\'><a href=\''.$url_final.'\' class=\'btn\'>View project</a></p>" data-fancybox-group="portfolio"></a></li>
		<li><a class="link btn btn-icon-link" href="'.$url.'"></a></li>
		</ul>';
	}
	elseif($url_video)
	{
		$result.='<ul class="project-action">
		<li><a class="lightbox btn btn-icon-link" href="'.$url_video.'" data-fancybox-type="iframe"></a></li>
		</ul>';
	}
	$result.='</div><div class="project-content">';
	if($titulo && !$ocultar_titulo)
	{
		if($url) $result.='<div class="title"><h2 class="h4"><a href="'.$url.'">'.$titulo.'</a></h2></div>';
		else $result.='<h3 class="project-title">'.$titulo.'</h3>';
	}
	if($texto) $result.='<div class="text"><p '.$styleTexto.'>'.$texto.'</p></div>';
	$result.='</div>';

*/

}

function showModuloGoogleMaps($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF()) {
		$Largura=$dbo->db_data['Largura'];
		if(!$Largura) {
			$result='<div class="container fix_container" style="overflow:hidden">';
		} else {
			$result='<div>';
		}
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=stripslashes($dbo2->db_data['resumo']);
			$texto=stripslashes($dbo2->db_data['texto']);
		}
		$dbo2->db_close();
		$result.='<div id="googlemaps"></div>'.$texto.$resumo;
		$result.='</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloImagemEsqTextoDir($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Largura=$dbo->db_data['Largura'];
		$Estilo_Back=$dbo->db_data['Estilo_Back'];

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';
		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			$styleBloco.='"';
		}

		if(!$Largura)
		{
			$result='<div class="row" '.$styleBloco.'>
			<div class="container fix_container" style="overflow:hidden">';
		}
		else
		{
			$result='<div class="row" '.$styleBloco.'>
			<div>';
		}

		$result.='<section class="main-row">';
		$result.='<div class="container" style="width:100% !important" '.$idattr.'>';
		if($titulo && !$ocultar_titulo)$result.='<div class="titleteam"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';
		$result.='<div class="title">';
		if($resumo)$result.='<div><p '.$styleResumo.'>'.$resumo.'</p></div>';
		$result.='</div>';

		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			while (!$dbo2->EOF())
			{
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$txt=$dbo2->db_data['bloco_img_txt'];
				if($txt){ob_start();put_content($txt);$txt=ob_get_contents();ob_end_clean();}

				$styleTitulo=getModuloTextStyles('',$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
				$result.='<div class="row">';
				$result.='<div class="col-md-4"><div class="grid-cell">';
				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Icon">';
				$result.='</div></div>';
				$result.='<div class="col-md-8"><div class="grid-cell">';
				if($titulo)$result.='<div class="title"><h4 '.$styleTitulo.'>'.$titulo.'</h4></div>';
				$result.='<div '.$styleTexto.'>'.$txt.'</div>';
				$result.='</div></div>';
				$result.='</div>';
				$dbo2->db_next();
			}
		}
		$dbo2->db_close();
		$result.='</div>';
		$result.='</section>';
		$result.='</div>
		</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloApoios($ID_Modulo)
{
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$ordem=$dbo->db_data['ordem'];
		$Largura=$dbo->db_data['Largura'];
		if(!$Largura)$result='<div class="container fix_container" style="overflow:hidden">';
		else $result='<div>';

		$Qtd=$dbo->db_data['Qtd'];
		$Largura=$dbo->db_data['Largura'];

		if(!$Qtd)$Qtd=3;
		if($Qtd == 2)$class=' class="col-sm-6" ';
		if($Qtd == 3)$class=' class="col-sm-4" ';
		if($Qtd == 4)$class=' class="col-sm-3" ';

		$titulo='';
		$resumo='';
		$texto='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=limpa_editor_lixo(stripslashes($dbo2->db_data['resumo']));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=limpa_editor_lixo(stripslashes($dbo2->db_data['texto']));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		if($titulo && !$ocultar_titulo)$result.='<div class="titleteam"><h4 '.$styleTitulo.'>'.$titulo.'</h4></div>';
		if($resumo)$result.=$resumo;
		$result.='<div class="row">';
		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$pos=0;
			while (!$dbo2->EOF())
			{
				if($pos == $Qtd)
				{
					$result.='</div>';
					$result.='<div class="row">';
					$pos=0;
				}
				$anim='';
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
				if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

				$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
				$url=$dbo2->db_data['bloco_img_url'];
				$styleTitulo=getModuloTextStyles('',$dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
				$result.='<div '.$class.' '.$styleTexto.'>';
				if($url != '')$result.='<a href="'.$url.'" target="_blank">';
				$result.='<div class="grid-cell" style="text-align:center;padding:10px">';
				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.='<div class="image"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$titulo.'"></div>';
				if($titulo && !$ocultar_titulo)$result.='<div class="title" style="margin:0 !important;"><h4 '.$styleTitulo.'>'.$titulo.'</h4></div>';
				$result.='<div '.$styleTexto.'>'.$texto.'</div>';
				$result.='</div>';
				if($url != '')$result.='</a>';
				$result.='</div>';
				$pos++;
				$dbo2->db_next();
			}
		}
		$result.='</div>';
		$dbo2->db_close();
		$result.='</div>';
	}
	$dbo->db_close();
	return $result;
}

function showBreadcrumbsPages()
{
	$result='';
	$str='';
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$dbo=new Dataobj();
	$dbo2=new Dataobj();

	/********************************************/
	$dbo3=new Dataobj();
	$dbo4=new Dataobj();
	$sql='SELECT ID_ORG FROM pages WHERE ID='.$GLOBALS['page_id'];

	$dbo3->db_open($db_link,$GLOBALS['DB_name'],$sql);
	$prevcab = '';
	if(!$dbo3->EOF())
	{
		$sql2 = 'SELECT Cabecalho
				FROM pages_lang
				WHERE ID_PAI = '.$dbo3->db_data['ID_ORG'].'
				AND Lang = "'.$GLOBALS['site_lang'].'"';

		$dbo4->db_open($db_link,$GLOBALS['DB_name'],$sql2);
		if(!$dbo4->EOF())
		{
			$prevcab_seo=seo('pages.php?page_id='.$dbo3->db_data['ID_ORG']);
			$prevcab = $dbo4->db_data['Cabecalho'];
		}
		$dbo4->db_close();
	}
	$dbo3->db_close();
	/********************************************/

	$sql='SELECT DefaultHome,ID_ORG FROM pages WHERE ID="'.$GLOBALS['page_id'].'"';
	$dbo->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$dbo->EOF())
	{
		$defaulthome=$dbo->db_data['DefaultHome'];
		$page_id_parent=$dbo->db_data['ID_ORG'];
		$Largura=$dbo->db_data['Largura'];

		$nome_temp='';
		$sql='SELECT Cabecalho, Cabecalho2 FROM pages_lang WHERE ID_PAI="'.$GLOBALS['page_id'].'" AND Lang="'.$GLOBALS['site_lang'].'"';
		$dbo2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$dbo2->EOF())
		{
			$nome_temp=$dbo2->db_data['Cabecalho'];
			if(!$GLOBALS['short_breadcum'])$nome_temp2=$dbo2->db_data['Cabecalho2'];
		}
		$dbo2->db_close();
	}
	$dbo->db_close();
	if($GLOBALS['page_id'] > 0)
	{
		$p_seo=seo('pages.php?page_id='.$GLOBALS['page_id']);
		if($nome_temp2 !='') $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.': '.$nome_temp2.'</a></li>';
		else $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.'</a></li>';

		if($page_id_parent > 0)
		{
			$str=showBreadcrumbsPagesRecursive($page_id_parent);
			//$str.='<li><a href="'.$p_seo.'">'.$nome_temp.'</a></li>';
			if($nome_temp2 !='') $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.': '.$nome_temp2.'</a></li>';
			else $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.'</a></li>';
		}
	}
	if(!$defaulthome) {
		$result.='<li><a href="'.$GLOBALS['site_http_url'].'homepage">HOME</a></li>';
		if($prevcab!=''){
			 $result.= '<li><a href="'.$prevcab_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$prevcab.'</a></li>';
		}
		$result.=$str;
	}

	return $result;
}

function showBreadcrumbsPagesRecursive($id_pai)
{
	$str='';
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new Dataobj();
	$rs2=new Dataobj();
	$sql='SELECT ID,ID_ORG FROM pages WHERE ID="'.$id_pai.'" AND Visible=1';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$page_idx=$rs->db_data['ID'];
		$page_id_parent=$rs->db_data['ID_ORG'];
		$nome_temp='';
		$sql='SELECT Cabecalho,Cabecalho2 FROM pages_lang WHERE ID_PAI="'.$page_idx.'" AND Lang="'.$GLOBALS['site_lang'].'"';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			$nome_temp=$rs2->db_data['Cabecalho'];
			$nome_temp2=$rs2->db_data['Cabecalho2'];
		}
		$rs2->db_close();
		$p_seo=seo('pages.php?page_id='.$page_idx);
		//$str='<li><a href="'.$p_seo.'">'.$nome_temp.'</a></li>';
		if($nome_temp2 != '') $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.': '.$nome_temp2.'</a></li>';
		else $str='<li><a href="'.$p_seo.'"> <i class="fa fa-angle-right" aria-hidden="true"></i> '.$nome_temp.'</a></li>';

		if($page_id_parent > 0)
		{
			if($nome_temp2 != '') $str='<li><a href="'.$p_seo.'">'.$nome_temp.': '.$nome_temp2.'</a></li>';
			else $str='<li><a href="'.$p_seo.'">'.$nome_temp.'</a></li>';
			$str=showBreadcrumbsPagesRecursive($page_id_parent).$str;
		}
	}
	$rs->db_close();
	return $str;
}

function showDestaques($ID_Modulo,$ID_Imagens)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Qtd=$dbo->db_data['Qtd'];
		if(!$Qtd)$Qtd=3;

		$Anchor=$dbo->db_data['Anchor'];
		$Posicao=$dbo->db_data['Posicao'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$img_ext=$dbo->db_data['img_ext'];
		$Largura=$dbo->db_data['Largura'];
		$Estilo_Back=$dbo->db_data['Estilo_Back'];

		if(!$Largura)$result='<div class="container fix_container" style="overflow:hidden">';
		else $result='<div>';

		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();

		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);

		$tipo_artigo='post';
		$animacao='post-latest';
		$tipo_galeria='post-type-gallery';
		if($Posicao == 1)
		{
			$tipo_artigo='project';
			$animacao='project-animated';
			$tipo_galeria='project-animated';
		}
		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			$styleBloco.='"';
		}

		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';

		$result.='<div  '.$idattr.'>';
		if($styleBloco)$result.='<div class="container-out container-image container-no-bottom" '.$styleBloco.' '.$idattr.'>';

		if($titulo && !$ocultar_titulo)$result.='<div class="title title-section"><h2 '.$styleTitulo.'>'.$titulo.'</h2><p '.$styleResumo.'>'.$resumo.'</p></div>';

		$result.='<div>';
		$result.='<div class="carousel-wrap">';
		$result.='<ul class="carousel-nav">';
		$result.='<li><a href="#" class="btn btn-icon-prev prev"></a></li>';
		$result.='<li><a href="#" class="btn btn-icon-next next"></a></li>';
		$result.='</ul>';
		$result.='<div class="carousel" data-visible="'.$Qtd.'">';

		$dbo2=getModuloImgs($ID_Imagens);
		while (!$dbo2->EOF())
		{
			$anim='';
			$imagem=$dbo2->db_data['bloco_img_ext'];
			$titulo=$dbo2->db_data['bloco_img_titulo'];
			$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

			$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
			$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
			$bloco_url=$dbo2->db_data['bloco_img_url'];

			//if($bloco_img_animacao) $anim=' data-animate="'.$bloco_img_animacao.'"';

			$styleTitulo=getModuloTextStyles('h4',$dbo2->db_data['bloco_img_titulo_cor'],
				$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
			$styleTexto=getModuloTextStyles('text',$dbo2->db_data['bloco_img_txt_cor'],
				$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],
				$dbo2->db_data['bloco_img_txt_line_height']);

			$result.='<article class="'.$tipo_artigo.' '.$animacao.' '.$tipo_galeria.'">';
			if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
				if($bloco_url == '') {
					$result.='<div class="'.$tipo_artigo.'-heading"><div class="thumbnail" '.$anim.'><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
				} else {
					$result.='<div class="'.$tipo_artigo.'-heading"><a href="'.$bloco_url.'"><div class="thumbnail" '.$anim.'><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></a></div>';
				}
			}
			$result.='<div class="'.$tipo_artigo.'-content">';

			if($titulo && !$ocultar_titulo) {
				if($bloco_url == '') {
					$result.='<div class="title"><h2 '.$styleTitulo.'>'.$titulo.'</h2></div>';
				} else {
					$result.='<div class="title"><a href="'.$bloco_url.'"><h2 '.$styleTitulo.'>'.$titulo.'</h2></a></div>';
				}
			}
			if($texto) {
				if(strlen($texto) > 250) {
					$texto=html_cut($texto,247).' ... <a href="'.$bloco_url.'">[+]</a>';
				}
				$result.='<div '.$styleTexto.'><p>'.$texto.'</p>';
				$result.='</div>';
			}
			$result.='</div>';
			$result.='</article>';
			$dbo2->db_next();
		}
		$dbo2->db_close();

		$result.='</div>';
		$result.='</div>';
		$result.='</div>';
		$result.='</div>';
		$result.='</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloGrupoTabelaPrecos($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF()) {
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Largura=$dbo->db_data['Largura'];
		if(!$Largura) {
			$result='<div class="container fix_container" style="overflow:hidden">';
		} else {
			$result='<div>';
		}
		$titulo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF()) {
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
		}
		$dbo2->db_close();
		$result.='<section class="main-row main-row-slim">';
		$result.='<div class="main-header main-header-gray">';
		$result.='<div class="page-header animated fadeIn">';
		$result.='<div class="container" style="width:100% !important">';
		$result.='<div class="row">';
		$result.='<div class="col-sm-12">';
		if($titulo && !$ocultar_titulo) {
			$result.='<h1 data-animate-in="fadeInRight" class="page-title animated fadeInRight">'.$titulo.'</h1>';
		}
		$dbo2=getModuloGrupo($ID_Modulo,'ID');
		if(!$dbo2->EOF()) {
			$result.='<ul data-animate-in="fadeInUpSmall" class="pricelist animated fadeInUpSmall">';
			while (!$dbo2->EOF()) {
				$result.=modulos($dbo2->db_data['ID']);
				$dbo2->db_next();
			}
			$result.='</ul>';
		}
		$dbo2->db_close();
		$result.='</div>';
		$result.='</div>';
		$result.='</div>';
		$result.='</div>';
		$result.='</div>';
		$result.='</section>';
		$result.='</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}

function showModuloElementoTabelaPrecos($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF()) {
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$ordem=$dbo->db_data['ordem'];
		$Largura=$dbo->db_data['Largura'];
		if(!$Largura) {
			$result='<div class="container fix_container" style="overflow:hidden">';
		} else {
			$result='<div>';
		}
		$titulo='';
		$resumo='';
		$texto='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=(limpa_editor_lixo(stripslashes($dbo2->db_data['texto'])));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$class='yellow';
		if($ordem == 2)$class='active';
		elseif($ordem == 1)$class='red';

		$result.='<li class="price-item '.$class.'">';
		$result.='<div class="price-head"><strong>'.$resumo.'</strong></div>';
		$result.='<div class="price-body">';
		$result.='<div class="price-content">';
		if($titulo && !$ocultar_titulo)$result.='<h2 class="price-title">'.$titulo.'</h2>';

		if($texto)
		{
			$result.='<ul>';
			$result.='<li>'.$texto.'</li>';
			$result.='</ul>';
		}
		$result.='</div>';
		$result.='<a class="btn btn-transparent btn-black btn-full" href="'.seo('pages.php?page_id='.$GLOBALS['contactos_page_id']).'" hidefocus="true" style="outline:medium none;"><span>FAÇA RESERVA</span></a>';
		$result.='</div>';
		$result.='</li>';
		$result.='</div>
		<div class="clear"></div>';
	}
	$dbo->db_close();

	return $result;
}


//***************************** novos modulos

function showModuloElementoOrganizacao($ID_Modulo)
{
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$ordem=$dbo->db_data['ordem'];
		$Largura=$dbo->db_data['Largura'];
		if(!$Largura)$result='<div class="container fix_container" style="overflow:hidden">';
		else $result='<div>';

		$Qtd=$dbo->db_data['Qtd'];
		$Largura=$dbo->db_data['Largura'];
		if(!$Qtd)$Qtd=4;

		$titulo='';
		$resumo='';
		$texto='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=limpa_editor_lixo(stripslashes($dbo2->db_data['resumo']));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=limpa_editor_lixo(stripslashes($dbo2->db_data['texto']));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		if($titulo && !$ocultar_titulo)$result.='<div class="titleteam"><h4 '.$styleTitulo.'>'.$titulo.'</h4></div>';

		$result.='<div class="row">';
		$dbo2=getModuloImgs($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$pos=0;
			$pos2=0;
			while (!$dbo2->EOF())
			{
				$lightbox=$dbo2->db_data['bloco_img_lightbox'];
				if($pos == $Qtd)
				{
					$result.='<div class="col-sm-2"></div>';
					$result.='</div>';
					$result.='<div class="row">';
					$pos=0;
				}
				$anim='';
				$imagem=$dbo2->db_data['bloco_img_ext'];
				$titulo=$dbo2->db_data['bloco_img_titulo'];
				$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
				if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

				$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
				$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
				$url=$dbo2->db_data['bloco_img_url'];
				$styleTitulo=getModuloTextStyles('',$dbo2->db_data['bloco_img_titulo_cor'],
					$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
				$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],
					$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],
					$dbo2->db_data['bloco_img_txt_line_height']);
				$result.='<div class="col-sm-2" '.$styleTexto.'>';
				if($url != '') {
					$result.='<a href="'.$url.'" target="_blank">';
				}
				$result.='<div class="grid-cell">';
				if($lightbox == 1) {
					if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
						$result.='<div class="project-heading"><div class="thumbnail"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$titulo.'"></div>
							<ul class="project-action">
								<li>
									<a class="lightbox btn btn-icon-view" href="#inline'.$pos2.'"></a>
								</li>
							</ul>
						</div>';
					}
				} else {
					if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
						$result.='<div class="image"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$titulo.'"></div>';
					}
				}
				if($titulo) {
					$result.='<div class="title" style="margin:0 !important;"><h4 '.$styleTitulo.'>'.$titulo.'</h4></div>';
				}
				if($lightbox == 1) {
					$result.="<div><a onclick=\"$('a[href=#inline".$pos2."]').click();return false;\" href=\"#\">&raquo;ver mais</a></div>";
					$result.='<div id="inline'.$pos2.'" style="display:none;width:675px;height:550px;padding:5px;overflow:hidden;background-color:#FFFFFF">
					<table width="100%" cellpadding=0 cellspacing=0 border=0>
						<tr>
							<td valign="top" width="140"><img width="140" src="'.IMG_FOLDER.'/documentos/'.$imagem.'"></td>
							<td width=10></td>
							<td valign="top">
								<table style="width:462px" cellpadding=0 cellspacing=0 border=0>
									<tr>
										<td style="font-size:16px;font-weight:bold;color:#000">'.$titulo.'</td>
									</tr>
									<tr>
										<td height=15></td>
									</tr>
									<tr>
										<td style="font-size:14px;">
										<div style="width:249px;overflow-y:auto;width:100%;max-height:450px">'.$texto.'</div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>';
				} else {
					$result.='<div '.$styleTexto.'>'.$texto.'</div>';
				}
				$result.='</div>';
				if($url != '') {
					$result.='</a>';
				}
				$result.='</div>';
				$pos++;
				$pos2++;
				$dbo2->db_next();
			}
		}
		$result.='</div>';
		$dbo2->db_close();
		$result.='</div>';
	}
	$dbo->db_close();
	return $result;
}

//***************************** novos modulos

function showMultiScroll($ID_Modulo)
{
	$result = '';
	$dbo = getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$result .= '<style>.wrapper{width:100%;height:100%;margin:0 auto;background:#CCC}.h_iframe{position:relative;}.h_iframe .ratio {display:block;width:100%;height:auto;}.h_iframe iframe {position:absolute;top:0;left:0;width:100%; height:100%;}</style>';
		$result .= '<div class="wrapper"><div class="h_iframe"><img class="ratio" src="layout/ratio.png"/><iframe id="frm'.$ID_Modulo.'" frameBorder="0" style="padding:0;margin:0" src="core/media/multiscroll/?ID_Modulo='.$ID_Modulo.'"></iframe></div></div>';
		// $result .= '<iframe id="frm'.$ID_Modulo.'" frameBorder="0" width="100%" height="'.$dbo->db_data['Altura'].'" style="padding:0;margin:0" src="core/media/multiscroll/?ID_Modulo='.$ID_Modulo.'"></iframe>';
	/* 	 $result .= '
		<script>
		$(window).on(\'resize load\', adjustIframes);
		function adjustIframes()
		{
			console.log(document.getElementById(\'frm'.$ID_Modulo.'\').contentWindow.document.body.scrollHeight + \'px\');
			if(screen.width < 360)	document.getElementById(\'frm'.$ID_Modulo.'\').style.height = document.getElementById(\'frm'.$ID_Modulo.'\').contentWindow.document.body.scrollHeight + \'px\';
		}
		</script>
		';  */
	}
	return $result;
}
function inicio_div_com_background($img_ext='',$Largura='',$Estilo_Back=0)
{
	$st='';
	$s='';
	if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
	{
		$st=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
		if($Estilo_Back)$st.='  background-attachment: fixed; ';
		$st.='"';
	}
	if($Largura>0)
	{
		$s='<div class="" '.$st.'><div>';
	}
	else
	{
		$s='<div class="" '.$st.'><div class="container fix_container" '.$Anim.'>';
	}
	return $s;
}
function fim_div_com_background()
{
	return '</div></div><div class="clear"></div>';
}
function inicio_div_container_out($styleBloco='',$Anchor='')
{
	$s='';
	$idattr='';
	if($Anchor)$idattr='id="'.$Anchor.'_bloco"';

	if($styleBloco != '')$s.='<div class="container-out container-image container-no-bottom" '.$idattr.'>';
	else $s.='<div '.$idattr.'>';

	return $s;
}
function fim_div_container_out()
{
	return '</div><div class="clear"></div>';
}
function seccao_titulo_resumo($ID_Modulo=0,$styleTitulo='',$styleResumo='',$ocultar_titulo=0)
{
	if(!$ID_Modulo)return '';

	$titulo='';
	$resumo='';
	$dbo2=getModuloLang($ID_Modulo);
	if(!$dbo2->EOF())
	{
		$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
		$resumo=limpa_editor_lixo(stripslashes($dbo2->db_data['resumo']));
		if($resumo != ""){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
	}
	$dbo2->db_close();

	$s='';
	if($titulo || $resumo)
	{
		$s.='<div class="title title-section">';
		if($titulo && !$ocultar_titulo)$s.='<h2 '.$styleTitulo.'>'.$titulo.'</h2>';
		if($resumo)$s.='<p '.$styleResumo.'>'.$resumo.'</p>';
		$s.='</div>';
	}
	return $s;
}
function inicio_div_wrap($Estilo=0)
{
	if($Estilo==3)return '<div class="testimonial-wrap">';
	elseif($Estilo==5)return '<div class="stacked-wrap">';
	else return '<div class="carousel-wrap">';
}
function fim_div_wrap()
{
	return '</div>';
}
function add_setas($Tem_setas=0)
{
	if($Tem_setas>0)return '<ul class="carousel-nav"><li><a href="#" class="btn btn-icon-prev prev"></a></li><li><a href="#" class="btn btn-icon-next next"></a></li></ul>';
}
function inicio_div_outter($Estilo=0)
{
	if($Estilo==3)return '<div class="testimonial-outer">';
	elseif($Estilo==5)return '<div class="stacked-outer">';
	else return '<div class="carousel-outer">';
}
function fim_div_outter()
{
	return '</div>';
}
function inicio_div_inner($Estilo=0)
{
	if($Estilo==3)return '<div class="testimonial-inner" style="display:table;text-align:left;float:none;top:auto;right:auto;bottom:auto;left:auto;z-index:auto;margin:0px;overflow:hidden;position:relative;width:100%;height:254px;">';
	elseif($Estilo==5) return '<div class="stacked-cards homepage-stacked">';
	else return '<div class="carousel-inner">';
}
function fim_div_inner()
{
	return '</div>';
}
function inicio_bloco_elementos($Estilo=0,$Qtd=3)
{
	$s='';
	if($Estilo==3)
	{
		//$s.='<ul style="text-align:left;float:none;position:absolute;top:0px;right:auto;bottom:auto;margin:0px;width:10260px;height:54px;left:0px;z-index:auto;opacity:1;" class="testimonial" data-auto="true">';
		$s.='<ul style="text-align:left;float:none;position:absolute;top:0px;right:auto;bottom:auto;margin:0px;width:100%;left:0px;z-index:auto;opacity:1;" class="testimonial" data-auto="true">';
	}
	elseif($Estilo==5)
	{
		$s.='<ul>';
	}
	else
	{
		$s.='<div style="display:block;text-align:start;float:none;top:auto;right:auto;bottom:auto;left:auto;z-index:auto;margin:0px;overflow:hidden;position:relative" class="caroufredsel_wrapper">';
		$s.='<div class="clients carousel" data-visible="'.$Qtd.'">';
	}
	return $s;
}
function fim_bloco_elementos($Estilo=0)
{
	if($Estilo==3)return '</ul>';
	else return '</div></div>';
}
function showModuloCarrocellImgs($ID_Modulo=0,$Estilo=0,$Posicao=0)
{
	$result='';
	if(!$ID_Modulo)return '';

	$animacao='project-latest';
	$tipo_galeria='post-type-gallery';
	$tipo_artigo='';

	if($Posicao==1){$animacao='project-animated';$tipo_galeria='project-animated';}
	if($Estilo==2)$tipo_artigo='project';

	$dbo2=getModuloImgs($ID_Modulo);
	while (!$dbo2->EOF())
	{
		$bloco_img_id = $dbo2->db_data['bloco_img_id'];
		$titulo=$dbo2->db_data['bloco_img_titulo'];
		$imagem=$dbo2->db_data['bloco_img_ext'];
		$ebook=$dbo2->db_data['bloco_pdf_ext'];
		$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
		if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
		$url=$dbo2->db_data['bloco_img_url'];
		$url_video=$dbo2->db_data['bloco_img_video'];
		$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
		$styleTexto=getModuloTextStyles('',$dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);
		$anim='';
		$titulo_x='';
		if(!$ocultar_titulo)$titulo_x=$titulo;

		// echo $texto;
		if(!$Estilo)
		{
			if($url != '')
			{
				$result.='<div class="client"><a href="'.seo($url).'" class="lightbox2" data-fancybox-type="iframe"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Client"></a></div>';
			}
			else
			{
				$result.='<div class="client"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Client"></div>';
			}
		}
		if($Estilo==1 || $Estilo==2)
		{
			if($ebook!='' && stream_resolve_include_path(IMG_FOLDER.'/documentos/'.$ebook))
			{

				$url = seo('pages.php?page_id='.$GLOBALS['ebook_page_id']).'?ebook_id='.$bloco_img_id;
				$result .='<a href="'.$url.'" target="_blank">';
			}
			/*elseif($url) $result .='<a href="'.$url.'" target="_blank">'; //implementar ebook*/
			$result.='<article class="'.$tipo_artigo.' '.$animacao.' '.$tipo_galeria.'"><div class="project-heading">';
			$result.=gerar_thumbnail(IMG_FOLDER.'/documentos/'.$imagem,$anim,$url,$url_video,$titulo_x,$texto,$styleTexto);
			$result.='</div></article>';
			/*if($url) $result .='</a>';*/
		}
		if($Estilo==3)
		{
			$result.='<li>
						<div class="testimonial-content">
							<div class="text">';
							if($imagem)$result.='<img src="'.IMG_FOLDER.'/documentos/'.$imagem.'">';
							$result.='</div>
						</div>
						<div class="testimonial-heading">
							<div class="title">
								<h4>'.$titulo_x.'</h4>
							</div>
							<div class="text">
								'.$texto.'
							</div>
						</div>
					</li>';
		}
		elseif($Estilo==5)
		{
			$result.='<li>
						<div class="stacked-content">';
			$result.=gerar_thumbnail(IMG_FOLDER.'/documentos/'.$imagem,$anim,$url,$url_video,$titulo_x,$texto,$styleTexto);
			$result.='</div>
					</li>';
		}

		$dbo2->db_next();
	}
	$dbo2->db_close();
	return $result;
}
function mostra_paginacao($Tem_paginacao=0,$Estilo=0)
{
	if($Tem_paginacao>0)
	{
		if($Estilo==3)return '<div style="display:block;" class="testimonial-pagi"><a class="" href="#1">1</a><a class="" href="#2">2</a></div>';
		else return '<div style="display:block;" class="carousel-pagi"><a class="selected" href="#"><span>1</span></a><a href="#"><span>2</span></a></div>';
	}
}
function showModuloCarrocell($ID_Modulo=0)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	//echo "&nbsp;";

	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];

		$Posicao=$dbo->db_data['Posicao'];
		$Qtd=$dbo->db_data['Qtd'];
		$Largura=$dbo->db_data['Largura'];
		$Tem_paginacao=$dbo->db_data['Tem_paginacao'];
		$Tem_setas=$dbo->db_data['Tem_setas'];
		$Estilo=$dbo->db_data['Estilo'];
		$Estilo_Back=$dbo->db_data['Estilo_Back'];
		$Modulo_animacao=$dbo->db_data['Modulo_animacao'];
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$result=inicio_div_com_background($img_ext,$Largura,$Estilo_Back);//***************Cria primeira div exterior com ou sem imagemd e fundo:
			$result.=inicio_div_container_out($styleBloco,$Anchor);//***************Cria div exterior com Anchor
				$result.=seccao_titulo_resumo($ID_Modulo,$styleTitulo,$styleResumo,$ocultar_titulo);//***************mostra titulo e resumo
				$result.=inicio_div_wrap($Estilo);//***************Cria div wrap que vai conter os elementos
					$result.=add_setas($Tem_setas);//***************adiciona setas de navegacao

					$result.=inicio_div_outter($Estilo);//***************Cria div outter
						$result.=inicio_div_inner($Estilo);//***************Cria div inner
							$result.=inicio_bloco_elementos($Estilo,$Qtd);//***************inicia bloco de elementos
								$result.=showModuloCarrocellImgs($ID_Modulo,$Estilo,$Posicao);//***************mostra todos os elementos
							$result.=fim_bloco_elementos($Estilo);//***************fim div
						$result.=fim_div_inner();

					$result.=fim_div_outter();
					$result.=mostra_paginacao($Tem_paginacao,$Estilo);
				$result.=fim_div_wrap();
			$result.=fim_div_container_out();
		$result.=fim_div_com_background();
	}
	$dbo->db_close();
	return $result;
}

function showModuloAccordion($ID_Modulo)
{
	$result='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF())
	{
		$Anchor=$dbo->db_data['Anchor'];
		$img_ext=$dbo->db_data['img_ext'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Largura=$dbo->db_data['Largura'];
		$Estilo=$dbo->db_data['Estilo'];
		$titulo='';
		$resumo='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=limpa_editor_lixo(stripslashes($dbo2->db_data['resumo']));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
		}
		$dbo2->db_close();
		$styleTitulo=getModuloTextStyles('',$dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles('',$dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);
		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';
		$tipo_artigo='project';

		$animacao='project-latest';
		$tipo_galeria='post-type-gallery';
		if($Posicao == 1)
		{
			$animacao='project-animated';
			$tipo_galeria='project-animated';
		}
		$styleBloco='';
		if($img_ext && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$img_ext))
		{
			$styleBloco=' style="background-image:url('.IMG_FOLDER.'/documentos/'.$img_ext.'); ';
			if($Estilo_Back)$styleBloco.='  background-attachment: fixed; ';
			$styleBloco.='"';
		}
		if(!$Largura)
		{
			$result='<div class="row" '.$styleBloco.' id="acc_'.$ID_Modulo.'"><div class="col-md-12">
			<div class="container fix_container" style="overflow:hidden">';
		}
		else
		{
			$result='<div class="row" '.$styleBloco.'><div class="col-md-12">
			<div>';
		}
		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';
		if($styleBloco)$result.='<div class="container-out container-image container-no-bottom"  '.$idattr.'>';
		else $result.='<div '.$idattr.'>';


		if($titulo && !$ocultar_titulo)$result.='<div class="title title-section"><h2 '.$styleTitulo.'>'.$titulo.'</h2><p '.$styleResumo.'>'.$resumo.'</p></div>';


		switch ($Estilo) {
		    case 1:
		        //TABS
					$result.='<div class="tab tab-full-width tab-colored" data-animation="slide">
					<ul class="tab-heading">';

					$dbo2=getModuloImgs($ID_Modulo);
					$i=0;
					while(!$dbo2->EOF())
					{
						$titulo=$dbo2->db_data['bloco_img_titulo'];
						$result.='<li class=""><a href="#">'.$titulo.'</a></li>';
						$dbo2->db_next();
					}
					$dbo2->db_close();

					$result.='</ul>
					<div class="tab-content">';

					$dbo2=getModuloImgs($ID_Modulo);
					$i=0;
					while(!$dbo2->EOF())
					{
						$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
						if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

						$result.='<div class="current">
											<div class="text">';


							ob_start();
							put_content($texto);
							$result.=ob_get_contents();
							ob_end_clean();


												$result.='
											</div>
										</div>';
						$dbo2->db_next();
					}
					$dbo2->db_close();

					$result.='</div></div>';
		        break;
		    case 2:
			        	$result.='<div class="accordion">';
						$dbo2=getModuloImgs($ID_Modulo);

						$i=0;
						while(!$dbo2->EOF())
						{
							/*$result .= '<style>
							#candidatura-anuncio .accordion-heading{
								    background: #f7f7f7;
								    padding:20px;
								    margin-top: 10px;
								}

								#candidatura-anuncio .accordion-content{
								    margin-top: 10px;
								    margin-bottom: 30px;
								    padding:20px;
								    color:#000000;
								}


								#candidatura-anuncio atag{
								    font-weight: normal;
								    font: 14px/16px Open Sans;
								}

								#candidatura-anuncio .accordion-heading{
								    position:relative;
								}


								.btn-candidatarse{
								    background-color: #f6600b;
								    color: #ffffff;
								    border-radius: 4px;
								    border: none;
								    display:inline-block;
								    cursor:pointer;
								    padding: 10px 20px;
								    text-transform: uppercase;
								    border:1px solid #f6600b;
								    margin-top:20px;
								}
									.btn-candidatarse:hover{
								    background-color: #fc7714;
								    color: #ffffff;
								    border-radius: 4px;

								}


								.btn-candidatarseh{
								    background-color: #f6600b;
								    color: #ffffff;
								    border-radius: 4px;
								    border: none;
								    display:inline-block;
								    cursor:pointer;
								    padding: 10px 20px;
								    text-transform: uppercase;
								    border:1px solid #f6600b;
								    width: 110px;
								    text-align: center;
								}




								.btn-candidatarseh:hover{
								    background-color: #fc7714;
								    color: #ffffff;
								    border-radius: 4px;
								    border:1px solid #f6600b;

								}


								.btn-candidatarseh-c{
								    background-color: #f7f7f7;
								    color: #ffffff;
								    border-radius: 4px;
								    border: none;
								    display:inline-block;
								    cursor:pointer;
								    padding: 10px 20px;
								    text-transform: uppercase;
								    border:1px solid #7f7f7f;
								    color:#7f7f7f;
								     width: 110px;
								    text-align: center;
								}




								.btn-candidatarseh-c:hover{
								    background-color: #7f7f7f;
								    border:1px solid #f7f7f7;
								    color: #f7f7f7;
								    border-radius: 4px;
								    border: none;

								}</style>';*/
							/**
							echo '<pre>';
							print_r($dbo2);
							echo '</pre>';
							/**/
							$i++;
							$titulo=$dbo2->db_data['bloco_img_titulo'];
							$imagem=$dbo2->db_data['bloco_img_ext'];
							$idpart = $dbo2->db_data['bloco_img_id'];
							$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
							if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

							$url=$dbo2->db_data['bloco_img_url'];
							$url_video=$dbo2->db_data['bloco_img_video'];
							$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
							$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
							//$styleTitulo=getModuloTextStyles($dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
							//$styleTexto=getModuloTextStyles($dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);

							/*if($i == 1)$result.='<div class="active">';*/
							if($i == 1)$result.='<div>';
							else $result.='<div>';

							if($titulo){
								$result.= '<div class="accordion-heading" id="accordion-heading'.$idpart.'" data-ref="'.$idpart.'" onclick="changebtn(this)" ><h5><a href="#">'.$titulo;
								$result.= '<div class="btn-candidatarseh btnsaux" id="btn-candidatarse'.$idpart.'-c"  data-ref="'.$idpart.'" onclick="changebtn(this)" style="float:right;font-size:10px;top: 30%;position: absolute;right: 22px;">Saber Mais</div></a></h5></div>';
							}
							$result.='<div class="accordion-content collapse" id="acontent'.$idpart.'">';
							$result.='<div class="row">';

							if($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.='<div class="col-sm-6"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$tituloimg.'"></div>';
							/*if($texto)$result.='<div class="col-sm-6"><p>'.$texto.'</p></div>';*/
							if($texto)
							{
								$result.='<div class="col-sm-12">';

								ob_start();
								put_content($texto);
								$result.=ob_get_contents();
								ob_end_clean();

								$result.='</div>';
							}
							$result.='</div>';
							$result.='</div>';
							$result.='</div>';
							$dbo2->db_next();
						}
						$dbo2->db_close();
						$result.='</div>';
						$result .= '<script>
										function changebtn(obj){
											var n = $("#"+obj.id).attr("data-ref");
											if($("#acontent"+n).css("display")=="none"){
												$("#btn-candidatarse"+n+"-c").removeClass("btn-candidatarseh").addClass("btn-candidatarseh-c");
												$("#btn-candidatarse"+n+"-c").html("Fechar");
											}else{
												$("#btn-candidatarse"+n+"-c").removeClass("btn-candidatarseh-c").addClass("btn-candidatarseh");
												$("#btn-candidatarse"+n+"-c").html("Saber Mais");
											}





										}
								    </script>';
		        break;
		    default:
			        $result.='<div class="accordion accordion-colored">';
					$dbo2=getModuloImgs($ID_Modulo);
					$i=0;
					while(!$dbo2->EOF())
					{
						$i++;
						$titulo=$dbo2->db_data['bloco_img_titulo'];
						$imagem=$dbo2->db_data['bloco_img_ext'];
						$texto=limpa_editor_lixo($dbo2->db_data['bloco_img_txt']);
						if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}

						$url=$dbo2->db_data['bloco_img_url'];
						$url_video=$dbo2->db_data['bloco_img_video'];
						$ocultar_titulo=$dbo2->db_data['bloco_img_titulo_visivel'];
						$bloco_img_animacao=$dbo2->db_data['bloco_img_animacao'];
						//$styleTitulo=getModuloTextStyles($dbo2->db_data['bloco_img_titulo_cor'],$dbo2->db_data['bloco_img_titulo_tamanho'],$dbo2->db_data['bloco_img_titulo_font_weight']);
						//$styleTexto=getModuloTextStyles($dbo2->db_data['bloco_img_txt_cor'],$dbo2->db_data['bloco_img_txt_tamanho'],$dbo2->db_data['bloco_img_txt_font_weight'],$dbo2->db_data['bloco_img_txt_line_height']);

						/*if($i == 1)$result.='<div class="active">';*/
						if($i == 1)$result.='<div>';
						else $result.='<div>';

						if($titulo)$result.='<div class="accordion-heading"><h5><a href="#">'.$titulo.'<i class="fa fa-plus" style="float:right;margin-right:5px;margin-top:5px;font-size:10px"></i></a></h5></div>';
						$result.='<div class="accordion-content collapse">';
						$result.='<div class="row">';

						if($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem))$result.='<div class="col-sm-6"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="'.$tituloimg.'"></div>';
						/*if($texto)$result.='<div class="col-sm-6"><p>'.$texto.'</p></div>';*/
						if($texto)
						{
							$result.='<div class="col-sm-12">';

							ob_start();
							put_content($texto);
							$result.=ob_get_contents();
							ob_end_clean();

							$result.='</div>';
						}
						$result.='</div>';
						$result.='</div>';
						$result.='</div>';
						$dbo2->db_next();
					}
					$dbo2->db_close();
					$result.='</div>';

		}
/**
		if($Estilo==1)
		{

		}
		else
		{

		}/**/
		$result.='</div>
		</div></div></div>
		<div class="clear"></div>';
	}
	$dbo->db_close();
	return $result;
}


function showModuloDestaques2($ID_Modulo)
{
	$result='';
	$str='';
	$dbo=getModulo($ID_Modulo);
	if(!$dbo->EOF()) {
		$Anchor=$dbo->db_data['Anchor'];
		$ocultar_titulo=$dbo->db_data['titulo_visivel'];
		$Layout=$dbo->db_data['Layout'];

		$titulo='';
		$resumo='';
		$texto='';
		$texto3='';
		$dbo2=getModuloLang($ID_Modulo);
		if(!$dbo2->EOF())
		{
			$titulo=limpa_editor_lixo(stripslashes($dbo2->db_data['titulo']));
			$resumo=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['resumo'])));
			if($resumo){ob_start();put_content($resumo);$resumo=ob_get_contents();ob_end_clean();}
			$texto=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['texto'])));
			if($texto){ob_start();put_content($texto);$texto=ob_get_contents();ob_end_clean();}
			$texto3=nl2br(limpa_editor_lixo(stripslashes($dbo2->db_data['texto3'])));
		}
		$dbo2->db_close();

		$styleTitulo=getModuloTextStyles($dbo->db_data['titulo_cor'],$dbo->db_data['titulo_tamanho'],$dbo->db_data['titulo_font_weight']);
		$styleResumo=getModuloTextStyles($dbo->db_data['Texto1_cor'],$dbo->db_data['Texto1_tamanho'],$dbo->db_data['Texto1_font_weight'],$dbo->db_data['Texto1_line_height']);

		$idattr='';
		if($Anchor)$idattr='id="'.$Anchor.'_bloco"';
		if($titulo && !$ocultar_titulo)$str.='<header class="text-center"><h1 '.$styleTitulo.'>'.$titulo.'</h1></header>';
		$str.='<section class="posts" '.$idattr.'>';

		/***TEXTO SIMPLES CENTRADO*****/
		if($resumo || $texto || $texto3)
		{
			$str.='<div>';
			$str.='<div class="title">';
			if($resumo)$str.='<div><p '.$styleResumo.'>'.$resumo.'</p></div>';
			if($texto)$str.='<div><p '.$styleResumo.'>'.$texto.'</p></div>';
			if($texto3)$str.='<div><p '.$styleResumo.'>'.$texto3.'</p></div>';
			$str.='</div>';
			$str.='</div>';
		}

		$rs2=getModuloImgs($ID_Modulo);
		$c=0;
		while (!$rs2->EOF())
		{
			$c++;
			$anim='';
			$imagem=$rs2->db_data['bloco_img_ext'];
			$titulo=$rs2->db_data['bloco_img_titulo'];
			$txt=limpa_editor_lixo($rs2->db_data['bloco_img_txt']);
			if($txt){ob_start();put_content($txt);$txt=ob_get_contents();ob_end_clean();}

			$bloco_img_animacao=$rs2->db_data['bloco_img_animacao'];
			$bloco_titulo_cor=$rs2->db_data['bloco_img_titulo_cor'];
			$bloco_titulo_tamanho=$rs2->db_data['bloco_img_titulo_tamanho'];
			$bloco_titulo_font_weight=$rs2->db_data['bloco_img_titulo_font_weight'];
			$bloco_texto_cor=$rs2->db_data['bloco_img_txt_cor'];
			$bloco_texto_tamanho=$rs2->db_data['bloco_img_txt_tamanho'];
			$bloco_texto_line_height=$rs2->db_data['bloco_img_txt_line_height'];
			$bloco_texto_font_weight=$rs2->db_data['bloco_img_txt_font_weight'];
			$bloco_img_layout=$rs2->db_data['bloco_img_layout'];
			$bloco_img_group=$rs2->db_data['bloco_img_group'];
			$bloco_img_main_image=$rs2->db_data['bloco_img_main_image'];
			$bloco_pdf_ext = $rs2->db_data['bloco_pdf_ext'];

			$bloco_style_titulo='';
			if($bloco_titulo_cor || $bloco_titulo_tamanho || $bloco_titulo_font_weight) {
				$str_bloco_titulo_cor='';
				$str_bloco_titulo_tamanho='';
				$str_bloco_titulo_font_weight='';
				if($bloco_titulo_cor) {
					$str_bloco_titulo_cor='color:'.$bloco_titulo_cor.';';
				}
				if($bloco_titulo_tamanho) {
					$str_bloco_titulo_tamanho='font-size:'.$bloco_titulo_tamanho.'px;';
				}
				if($bloco_titulo_font_weight) {
					$str_bloco_titulo_font_weight='font-weight:'.$bloco_titulo_font_weight.';';
				}

				$bloco_style_titulo=' style="'.$str_bloco_titulo_cor.$str_bloco_titulo_tamanho.$str_bloco_titulo_font_weight.'"';
			}
			$bloco_style_texto='';
			if($bloco_texto_cor || $bloco_texto_tamanho || $bloco_texto_line_height || $bloco_texto_font_weight) {
				$str_bloco_texto_cor='';
				$str_bloco_texto_tamanho='';
				$str_bloco_texto_font_weight='';
				$str_bloco_texto_line_height='';
				if($bloco_texto_cor) {
					$str_bloco_texto_cor='color:'.$bloco_texto_cor.';';
				}
				if($bloco_texto_tamanho) {
					$str_bloco_texto_tamanho='font-size:'.$bloco_texto_tamanho.'px;';
				}
				if($bloco_texto_line_height) {
					$str_bloco_texto_line_height='line-height:'.$bloco_texto_line_height.'px;';
				}
				if($bloco_texto_font_weight) {
					$str_bloco_texto_font_weight='font-weight:'.$bloco_texto_font_weight.';';
				}
				$bloco_style_texto=' style="'.$str_bloco_texto_cor.$str_bloco_texto_tamanho.$str_bloco_texto_font_weight.$str_bloco_texto_line_height.'"';
			}

			if($bloco_img_animacao) {
				$anim=' data-animate="'.$bloco_img_animacao.'"';
			}
			$str.='<div class="row">';
			$str.='<article class="post post-thumb post-type-gallery devider-top" '.$anim.'>';

			$bloco=0;
			if(!$Layout) {
				$bloco=1;
			}
			if($Layout == 2 && $c % 2 != 0) {
				$bloco=1;
			}

			$col1='col-sm-6';
			$col2='col-sm-6';
			if(!$bloco_img_layout) {
				$col1='col-sm-6';
				$col2='col-sm-6';
			}
			if($bloco_img_layout == 1) {
				$col1='col-sm-4';
				$col2='col-sm-8';
			}
			if($bloco_img_layout == 2) {
				$col1='col-sm-8';
				$col2='col-sm-4';
			}

			if($bloco_img_group != '' && $bloco_img_main_image == 0) {
				if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
					$str.='<div style="display:none;"><div class="thumbnail"><a class="lightbox" href="'.IMG_FOLDER.'/documentos/'.$imagem.'" data-fancybox-group="'.$bloco_img_group.'" data-fancybox-title="<h4>'.$titulo.'</h4><p>'.$txt.'</p>"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></a></div></div>';
				}
			} else {
				if($bloco_img_group != '' && $bloco_img_main_image == 1) {
					if($bloco == 1) {
						//Imagem à esq,texto à dir
						if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
							$str.='<div class="'.$col1.'"><div class="thumbnail"><a class="lightbox " href="'.IMG_FOLDER.'/documentos/'.$imagem.'" data-fancybox-group="'.$bloco_img_group.'" data-fancybox-title="<h4>'.$titulo.'</h4><p>'.$txt.'</p>"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"><i class="icon-lupa"></i></a></div></div>';
						}
						$str.='<div class="'.$col2.'">';
						if($titulo) {
							$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
						}
						if($txt) {
							$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
						}
						$str.='</div>';
					} else {
						//Imagem à dir,texto à esq
						$str.='<div class="'.$col1.'">';
						if($titulo) {
							$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
						}
						if($txt) {
							$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
						}
						$str.='</div>';
						if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
							$str.='<div class="'.$col2.'"><div class="thumbnail"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
						}
					}
				} else {
					if($bloco == 1) {
						//Imagem à esq,texto à dir
						if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
							$str.='<div class="'.$col1.'"><div class="thumbnail"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
						}
						$str.='<div class="'.$col2.'">';
						if($titulo) {
							$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
						}
						if($txt) {
							$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
						}
						$str.='</div>';
					} else {
						//Imagem à dir,texto à esq
						$str.='<div class="'.$col1.'">';
						if($titulo) {
							$str.='<div class="title"><h2 class="h4" '.$bloco_style_titulo.'>'.$titulo.'</h2></div>';
						}
						if($txt) {
							$str.='<div class="text" '.$bloco_style_texto.'>'.$txt.'</div>';
						}
						$str.='</div>';
						if(dotbright_image_resolve(IMG_FOLDER.'/documentos/'.$imagem)) {
							$str.='<div class="'.$col2.'"><div class="thumbnail"><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div></div>';
						}
					}
				}
			}

			/*if(stream_resolve_include_path(IMG_FOLDER.'/documentos/'.$imagem)) $result.='<div class="thumbnail" '.$anim.'><img src="'.IMG_FOLDER.'/documentos/'.$imagem.'" alt="Image"></div>';
			$result.='<ul class="project-action">';
			if($url)$result.= '<li style="margin-left:-12px !important"><a class="link btn btn-icon-link" href="'.$url.'"></a></li>';
			elseif($titulo && !$ocultar_titulo) $result.='<li style="margin-left:-12px !important"><a class="lightbox btn btn-icon-view" href="'.IMG_FOLDER.'/documentos/'.$imagem.'" data-fancybox-title="<h4>'.$titulo.'</h4><p>'.$txt.'</p>" data-fancybox-group="portfolio"></a></li>';
			else $result.='<li style="margin-left:-12px !important"><a class="lightbox btn btn-icon-view" href="'.IMG_FOLDER.'/documentos/'.$imagem.'" data-fancybox-group="portfolio"></a></li>';
			$result.='</ul>';
			$result.='</div>';*/

			$str.='</article>';
			$str.='</div>';

			$rs2->db_next();
		}
		$rs2->db_close();

		$result.=$str;

		$result.='</section>';
	}
	$dbo->db_close();

	return $result;
}
function showModuloListaComPopup($idModulo)
{
    $result = '';
    $dbo = getModulo($idModulo);
    if (!$dbo->EOF()) {

        $result .= '<div class="tipo24">';
        $dbo2 = getModuloImgs($idModulo);
        while (!$dbo2->EOF()) {
            $idImage = $dbo2->db_data['bloco_img_id'];
            $titulo = $dbo2->db_data['bloco_img_titulo'];
            $imagem = $dbo2->db_data['bloco_img_ext'];
            $ocultar_titulo = $dbo2->db_data['bloco_img_titulo_visivel'];
            $texto = $dbo2->db_data['bloco_img_txt'];
            $url = $dbo2->db_data['bloco_img_url'];

            $result .= '<div id="inline_' . $idImage . '" class="tipo24" style="display:none">';
            if ($imagem && dotbright_image_resolve(IMG_FOLDER.'/documentos/' . $imagem)) {
                $result .= '<div class="text-center"><img src="'.IMG_FOLDER.'/documentos/' . $imagem . '" alt="' . $titulo . '"></div>';
            }
            if (!$ocultar_titulo && $titulo) {
                $result .= '<h3 class="titulo">' . $titulo . '</h3>';
            }
            echo '<div class="block">';
            if ($texto) {
                $result .= '<div class="texto pull-left"><p>' . $texto . '</p></div>';
            }
            if($url)
            {
                $result .= '<div class="button pull-right"><a class="btn-read-more" href="'.$url.'">saber mais</a></div>';
            }
            $result .= '<div class="clearfix"></div>';
            $result .= '</div>';
            $result .= '</div>';


            $result .= '<div class="row">';
            $result .= '<div class="col-xs-12">';
            $result .= '<a class="lightbox2" href="#inline_' . $idImage . '" data-fancybox-type="iframe"> &gt;&gt; ' . $titulo . '</a>';
            $result .= '</div>';
            $result .= '</div>';


            $dbo2->db_next();
        }
        $dbo2->db_close();
        $result .= '</div>';
    }
    $dbo->db_close();

    return $result;
}
