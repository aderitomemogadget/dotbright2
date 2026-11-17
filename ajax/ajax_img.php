<?php
	if(intval($_GET['prod_id']) > 0)
	{
		$path='../core/';
		include $path.'core_files.php';

		//se for uma reparacao devolve imagem:

		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$m_id=intval($_GET['prod_id']);
		$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT Ref from products WHERE ID='.$m_id);
		$ref = $rs->db_data['Ref'];
		$rs->db_close();

		if(substr($ref, 0,3)=='REP')
		{
			if(substr_count($ref, 'DIAGNOSTICO')) $img='diagno.jpg';
			if(substr_count($ref, 'REPIPHONE7AUSCULTADOR')) $img='AUSCULTADOR.jpg';
			if(substr_count($ref, 'REPIPHONE7AUSC')) $img='AUSCULTADOR.jpg';
			if(substr_count($ref, 'REPIPHONE7SPEAKER')) $img='AUSCULTADOR.jpg';
			if(substr_count($ref, 'REPIPHONE7MICROFONE')) $img='MICROFONE.jpg';
			if(substr_count($ref, 'REPIPHONE7MIC')) $img='MICROFONE.jpg';
			if(substr_count($ref, 'REPIPHONE7ALTIFALANTE')) $img='ALTIFALANTE.jpg';
			if(substr_count($ref, 'REPIPHONE7ALTI')) $img='ALTIFALANTE.jpg';
			if(substr_count($ref, 'REPIPHONE7CAMARA')) $img='CAMARA.jpg';
			if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
			if(substr_count($ref, 'REPIPHONE7CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
			if(substr_count($ref, 'REPIPHONE7VIBRADOR')) $img='VIBRADOR.jpg';
			if(substr_count($ref, 'REPIPHONE7VIBRA')) $img='VIBRADOR.jpg';
			if(substr_count($ref, 'REPIPHONE7ONOFF')) $img='ONOFF.jpg';
			if(substr_count($ref, 'REPIPHONE7SILENCIO')) $img='SILENCIO.jpg';
			if(substr_count($ref, 'REPIPHONE7MUTE')) $img='SILENCIO.jpg';
			if(substr_count($ref, 'REPIPHONE7VOLUME')) $img='VOLUME.jpg';
			if(substr_count($ref, 'REPIPHONE7HOME')) $img='HOME.jpg';
			if(substr_count($ref, 'REPIPHONE7SENSOR')) $img='SENSOR.jpg';
			if(substr_count($ref, 'REPIPHONE7AUDIOJACK')) $img='AUDIOJACK.jpg';
			if(substr_count($ref, 'REPIPHONE7CARGA')) $img='CCARGA.jpg';
			if(substr_count($ref, 'REPIPHONE7BATERIA')) $img='BATERIA.jpg';
			if(substr_count($ref, 'REPIPHONE7CARCACA')) $img='CARCACA.jpg';
			if(substr_count($ref, 'REPIPHONE7TRAS')) $img='VIDROTRASEIRO.jpg';
			if(substr_count($ref, 'REPSGA705TRASEIRA')) $img='TAMPA_TRASEIRA.jpg';
			if(substr_count($ref, 'REPIPHONE12PRODIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
			if(substr_count($ref, 'REPIPHONE12PRODIAG')) $img='DIAGNOSTICO.jpg';
			if(substr_count($ref, 'REPIPHONE12PROLENTECAMARA')) $img='LENTECAMARA.jpg';
			if(substr_count($ref, 'REPIPHONE12PROLENTECAM')) $img='LENTECAMARA.jpg';
			if(substr_count($ref, 'REPIPHONE11LEITORSIM')) $img='LEITORSIM.jpg';
			if(substr_count($ref, 'REPIPHONE11SIM')) $img='LEITORSIM.jpg';
			if(substr_count($ref, 'REPIPAD7LCD')) $img='LCD (1).jpg';
			if(substr_count($ref, 'REPIPAD7TOUCH')) $img='TOUCH.jpg';
			if(substr_count($ref, 'REPIPHONE6WIFI')) $img='WIFI.jpg';
			if(substr_count($ref, 'REPIPHONE6ICAUDIO')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'REPIPHONE6ICTOUCH')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'REPIPHONE6ICBACKLIGHT')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'REPIPHONE6ICCARGA')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'REPIP11MODULOOR')) $img='MODULO.jpg';
			if(substr_count($ref, 'REPIPHONE11MODULOLCD')) $img='LCD.jpg';
			if(substr_count($ref, 'LENTE1CC')) $img='LENTE1CC.jpg';
			if(substr_count($ref, 'LENTE1CE')) $img='LENTE1CE.jpg';
			if(substr_count($ref, 'LENTE1CEV')) $img='LENTE1CEV.jpg';
			if(substr_count($ref, 'LENTE2CC')) $img='LENTE2CC.jpg';
			if(substr_count($ref, 'LENTE2CEV')) $img='LENTE2CEV.jpg';
			if(substr_count($ref, 'LENTE2CEH')) $img='LENTE2CEH.jpg';
			if(substr_count($ref, 'LENTEi2C')) $img='LENTEi2C.jpg';
			if(substr_count($ref, 'LENTE3C')) $img='LENTE3C.jpg';
			if(substr_count($ref, 'LENTEi3C')) $img='LENTEi3C.jpg';
			if(substr_count($ref, 'LENTE4C')) $img='LENTE4C.jpg';
			if(substr_count($ref, 'CAMARA1CC')) $img='CAM1CC.jpg';
			if(substr_count($ref, 'CAMARA1CE')) $img='CAM1CE.jpg';
			if(substr_count($ref, 'CAMARA1CEV')) $img='CAM1CEV.jpg';
			if(substr_count($ref, 'CAMARA2CC')) $img='CAM2CC.jpg';
			if(substr_count($ref, 'CAMARA2CEV')) $img='CAM2CEV.jpg';
			if(substr_count($ref, 'CAMARA2CEH')) $img='CAM2CEH.jpg';
			if(substr_count($ref, 'CAMARAi2C')) $img='CAMi2C.jpg';
			if(substr_count($ref, 'CAMARA3C')) $img='CAM3C.jpg';
			if(substr_count($ref, 'CAMARAi3C')) $img='CAMi3C.jpg';
			if(substr_count($ref, 'CAMARA4C')) $img='CAM4C.jpg';
			if(substr_count($ref, 'TTRASEIRA2CC')) $img='TTRAS2CC.jpg';
			if(substr_count($ref, 'TTRASEIRA3CC')) $img='TTRAS3CC.jpg';
			if(substr_count($ref, 'VIDROTRAS2CC')) $img='VIDROTRAS2CC.jpg';
			if(substr_count($ref, 'VIDROTRAS2EH')) $img='VIDROTRAS2EH.jpg';
			if(substr_count($ref, 'VIDROTRAS2EV')) $img='VIDROTRAS2EV.jpg';
			if(substr_count($ref, 'VIDROTRASi2C')) $img='VIDROTRASi2C.jpg';
			if(substr_count($ref, 'VIDROTRAS3CC')) $img='VIDROTRAS3CC.jpg';
			if(substr_count($ref, 'VIDROTRASi3C')) $img='VIDROTRASi3C.jpg';
			if(substr_count($ref, 'REBALLING')) $img='REBALLING.jpg';
			if(substr_count($ref, 'MODULO')) $img='MODULO.jpg';
			if(substr_count($ref, 'MODULOFRAME')) $img='MODULO_aro.jpg';
			if(substr_count($ref, 'CCARGA')) $img='CCARGA.jpg';
			if(substr_count($ref, 'LEITORSIM')) $img='LEITORSIM.jpg';
			if(substr_count($ref, 'AUSCULTADOR')) $img='AUSCULTADOR.jpg';
			if(substr_count($ref, 'MICROFONE')) $img='MICROFONE.jpg';
			if(substr_count($ref, 'ALTIFALANTE')) $img='ALTIFALANTE.jpg';
			if(substr_count($ref, 'BATERIA')) $img='BATERIA.jpg';
			if(substr_count($ref, 'CARCACA')) $img='CARCACA.jpg';
			if(substr_count($ref, 'DIAGNOSTICO')) $img='DIAGNOSTICO.jpg';
			if(substr_count($ref, 'FRP')) $img='GOOGLE.jpg';
			if(substr_count($ref, 'UNLOCK')) $img='UNLOCK.jpg';
			if(substr_count($ref, 'CODIGO')) $img='CODIGO.jpg';
			if(substr_count($ref, 'LENTECAMARA')) $img='LENTE.jpg';
			if(substr_count($ref, 'TAMPATRASEIRA')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
			if(substr_count($ref, 'CAMARA')) $img='CAMARA.jpg';
			if(substr_count($ref, 'VIDROTRASEIRO')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'SIM')) $img='GAVETASIM.jpg';
			if(substr_count($ref, 'TOUCH')) $img='TOUCH.jpg';
			if(substr_count($ref, 'LCD')) $img='LCD.jpg';
			if(substr_count($ref, 'MODULOORIG')) $img='MODULO.jpg';
			if(substr_count($ref, 'MODULOCOMP')) $img='OLED.jpg';
			if(substr_count($ref, 'SWAP')) $img='SWAP.jpg';
			if(substr_count($ref, 'VIDROTRAS')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'ONOFF')) $img='ONOFF.jpg';
			if(substr_count($ref, 'SILENCIO')) $img='SILENCIO.jpg';
			if(substr_count($ref, 'VOLUME')) $img='VOLUME.jpg';
			if(substr_count($ref, 'VIBRADOR')) $img='VIBRADOR.jpg';
			if(substr_count($ref, 'SENSOR')) $img='SENSOR.jpg';
			if(substr_count($ref, 'ICCARGA')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'HOME')) $img='HOME.jpg';
			if(substr_count($ref, 'AUDIOJACK')) $img='AUDIOJACK.jpg';
			if(substr_count($ref, 'WIFI')) $img='WIFI.jpg';
			if(substr_count($ref, 'REDE')) $img='REDE.jpg';

			echo $GLOBALS['site_http_url'].'layout/imgs/'.$img;

		}
		else
		{

			$arr_imagens=get_product_images_first($prod_id);//na versao rapida trazemos apenas 1 imagem
			if(count($arr_imagens) > 0)
			{
				/*$webp=0;
				if( strpos( $_SERVER['HTTP_ACCEPT'], 'image/webp' ) !== false || strpos( $_SERVER['HTTP_USER_AGENT'], ' Chrome/' ) !== false ) {$webp=1;}
				$webp=0;*/

				$i_small=unserialize(IMG_SMALL);
				$i_thum=unserialize(IMG_THUM);
				$i_prod=unserialize(IMG_PROD);

				$i=0;

				$ext=$arr_imagens[$i]['img_ext'];
				$ordem=$arr_imagens[$i]['Ordem'];
				$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;
				$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.png';
				$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
				$img_file_prod='files/products/'.$prod_id.'_'.$ordem.'_prod.png';

				// VAMOS PRIMEIRO GERAR
				if($i_small['ativo'] == 1)	geraimagens($img_file, $img_file_small, $i_small['w'], $i_small['h'], $i_small['crop'], $i_small['m_trans'], $i_small['c_white']);
				if($i_thum['ativo'] == 1) 	geraimagens($img_file, $img_file_thum,  $i_thum['w'], $i_thum['h'], $i_thum['crop'], $i_thum['m_trans'], $i_thum['c_white']);
				if($i_prod['ativo'] == 1)	geraimagens($img_file, $img_file_prod,  $i_prod['w'], $i_prod['h'], $i_prod['crop'], $i_prod['m_trans'], $i_prod['c_white']);

				$img_file_small_cache='';
				$img_file_thum_cache='';
				$img_file_prod_cache='';

				// VAMOS PASSAR A USAR APENAS 3 TAMANHOS, PEQUENA, MEDIA, E FICHA DE PRODUTO
				//Se existir a original
				if(file_exists($GLOBALS['site_shell_url'].$img_file) && $ext!='')
				{
					if(!file_exists($GLOBALS['site_shell_url'].$img_file_small))geraimagens($img_file, $img_file_small, $i_small['w'], $i_small['h'], $i_small['crop'], $i_small['m_trans'], $i_small['c_white']);
					if(!file_exists($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file, $img_file_thum,  $i_thum['w'], $i_thum['h'], $i_thum['crop'], $i_thum['m_trans'], $i_thum['c_white']);
					if(!file_exists($GLOBALS['site_shell_url'].$img_file_prod))geraimagens($img_file, $img_file_prod,  $i_prod['w'], $i_prod['h'], $i_prod['crop'], $i_prod['m_trans'], $i_prod['c_white']);
				}
				if(file_exists($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.png?dp='.filemtime($GLOBALS['site_shell_url'].$img_file_small);
				if(file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.filemtime($GLOBALS['site_shell_url'].$img_file_thum);
				if(file_exists($GLOBALS['site_shell_url'].$img_file_prod ) && $ext!='') $img_file_prod_cache='files/products/'.$prod_id.'_'.$ordem.'_prod.png?dp='.filemtime($GLOBALS['site_shell_url'].$img_file_prod);
				/*if($webp==2)
				{
					$img_webp_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
					$img_webp_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
					$img_webp_prod='files/products/'.$prod_id.'_'.$ordem.'_prod.webp';

					if(file_exists($GLOBALS['site_shell_url'].$img_file_small))
					{
						if(!file_exists($GLOBALS['site_shell_url'].$img_webp_small))convert_webp($img_file_small);
						$img_file_small='files/products/'.$prod_id.'_'.$ordem.'_small.webp';
						$img_file_small_cache='files/products/'.$prod_id.'_'.$ordem.'_small.webp?dp='.filemtime($GLOBALS['site_shell_url'].$img_webp_small);
					}

					if(file_exists($GLOBALS['site_shell_url'].$img_file_thum))
					{
						if(!file_exists($GLOBALS['site_shell_url'].$img_webp_thum))convert_webp($img_file_thum);
						$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.webp';
						$img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.webp?dp='.filemtime($GLOBALS['site_shell_url'].$img_webp_thum);
					}
					if(file_exists($GLOBALS['site_shell_url'].$img_file_prod))
					{
						if(!file_exists($GLOBALS['site_shell_url'].$img_webp_prod))convert_webp($img_file_prod);
						$img_file_prod='files/products/'.$prod_id.'_'.$ordem.'_prod.webp';
						$img_file_prod_cache='files/products/'.$prod_id.'_'.$ordem.'_prod.webp?dp='.filemtime($GLOBALS['site_shell_url'].$img_webp_prod);
					}
				}*/

				if(file_exists($GLOBALS['site_shell_url'].$img_file_small) && $ext!='') $img_file_small=$img_file_small_cache; else $img_file_small=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
				if(file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache; else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				if(file_exists($GLOBALS['site_shell_url'].$img_file_prod) && $ext!='') $img_file_prod=$img_file_prod_cache; else $img_file_prod=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';

				if($ext)
				{
					$arr_img[]=$GLOBALS['site_http_url'].$img_file;
					$arr_img_cart[]=$img_file_small;
					$arr_img_med[]=$img_file_prod;
					$arr_img_new[]=$img_file_thum;
					$arr_img_news[]=$img_file_thum;
					$arr_img_fb[]  =$img_file_prod;
				}
				else
				{
					$arr_img[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
					$arr_img_cart[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
					$arr_img_med[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
					$arr_img_new[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
					$arr_img_news[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
					$arr_img_fb[]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				}
			}
			else
			{
				$arr_img[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				$arr_img_cart[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
				$arr_img_med[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo2.png';
				$arr_img_new[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
				$arr_img_news[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
				if($i_fb['ativo'] == 1) $arr_img_fb[0]=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
			}

			$prods['prd_imgm_new']=$arr_img_new[0];
			$prods['prd_imgm_news']=$arr_img_news[0];
			$prods['prd_imgm_cart']=$arr_img_cart[0];
			$prods['prd_imgm_med']=$arr_img_med[0];
			$prods['prd_imgm_fb']=$arr_img_fb[0];

			echo $arr_img_news[0];
		}
	}
?>