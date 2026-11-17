<?php
	$path='../core/';
	include $path.'core_files.php';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$lingua = $GLOBALS['admin_lang'];

	$filterdProducts = $_GET['filterdProducts'];
	$array_produtos=explode(';',$filterdProducts);

	$f_json='[';

	$j=0;
	$t=count($array_produtos);
	while($j<$t)
	{
		$img='';
		$prod_id=$array_produtos[$j];
		/*$arr_imagens=get_product_images_first($prod_id);//na versao rapida trazemos apenas 1 imagem
		if(count($arr_imagens) > 0)
		{
			$i_thum=unserialize(IMG_THUM);
			$i=0;
			$ext=$arr_imagens[$i]['img_ext'];
			$ordem=$arr_imagens[$i]['Ordem'];
			$img_file='files/products/'.$prod_id.'_'.$ordem.'.'.$ext;
			$img_file_thum='files/products/'.$prod_id.'_'.$ordem.'_thum.png';
			

			// VAMOS PRIMEIRO GERAR
			if($i_thum['ativo'] == 1) 	geraimagens($img_file, $img_file_thum,  $i_thum['w'], $i_thum['h'], $i_thum['crop'], $i_thum['m_trans'], $i_thum['c_white']);

			$img_file_thum_cache='';

			// VAMOS PASSAR A USAR APENAS 3 TAMANHOS, PEQUENA, MEDIA, E FICHA DE PRODUTO
			//Se existir a original
			if(file_exists($GLOBALS['site_shell_url'].$img_file) && $ext!=''){if(!file_exists($GLOBALS['site_shell_url'].$img_file_thum))geraimagens($img_file, $img_file_thum,  $i_thum['w'], $i_thum['h'], $i_thum['crop'], $i_thum['m_trans'], $i_thum['c_white']);}
			if(file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum_cache='files/products/'.$prod_id.'_'.$ordem.'_thum.png?dp='.filemtime($GLOBALS['site_shell_url'].$img_file_thum);
			if(file_exists($GLOBALS['site_shell_url'].$img_file_thum) && $ext!='') $img_file_thum=$img_file_thum_cache; else $img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';

			if(!$ext)
			{
				$img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
			}
			
		}
		else
		{
			$img_file_thum=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo_cart.png';
		}*/
		
		$ordem=1;
		$ext='png';
		$img_file='files/products/'.$prod_id.'_'.$ordem.'_thum.'.$ext;
		if(!is_file($GLOBALS['site_shell_url'].$img_file) )
		{
			$img_file_original='files/products/'.$prod_id.'_'.$ordem.'.jpg';
			if(is_file($GLOBALS['site_shell_url'].$img_file_original))
			{
				$i_thum=unserialize(IMG_THUM);
				geraimagens($img_file_original,$img_file, $i_thum['w'],$i_thum['h'],$i_thum['crop'],$i_thum['m_trans'],$i_thum['c_white']);
			}
			if(!is_file($GLOBALS['site_shell_url'].$img_file) )$img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
		}
		else $GLOBALS['site_http_url'].$img_file;
		//-------------------


		
		//Agora a descricao
		$rs->db_open($db_link,$GLOBALS['DB_name'],'SELECT Cabecalho,Texto1 from products_lang WHERE ID_PAI='.$prod_id.' AND Lang="'.$lingua.'"');
		$designacao= converterPontoParaTraco(limpa_editor_lixo(stripslashes(strlen($rs->db_data['Texto1']) > 150 ? html_cut($rs->db_data['Texto1'],147)."... " : $rs->db_data['Texto1'])));
		$designacao=str_replace('"','',$designacao);
		$rs->db_close();

		//Agora os precos
		$return_pvp='';
		$prods=get_produtc_info($prod_id);
		$precos=$prods['prod_precos'];
		//$precos = get_produtc_price($prod_id);
		$PODE_ENCOMENDAR=$precos['PODE_ENCOMENDAR'];
		$return_pvp='<div class="price"';
		if( ($precos['STOCK'] <= 0 || $prods['prod_carrinho'] <= 0)  && $GLOBALS['produtos_sem_stock_ocultar_preco']==1)
		{
			$return_pvp.=' >&nbsp;</div><div class="clear"></div><span class="price" style="height:27px;display:block;text-align:center;cursor:pointer;line-height:30px">Ver Info</span>';
		}
		else
		{
			if($prods['prod_precos']['PROMOCAO'] == 1 && $prods['prod_precos']['PRECO_ANTIGO'] > 0 && $prods['prod_precos']['PRECO_ANTIGO'] > $prods['prod_precos']['PVP']){$price_riscado=numberformat($prods['prod_precos']['PRECO_ANTIGO']);}
			if($prods['prod_precos']['PVP'] > 0)
			{
				$pvp=$prods['prod_precos']['PVP'];
				$iva=intval($prods['prod_precos']['IVA']);
			}
			if($pvp <= 0 )$return_pvp.=' style="visibility:hidden" ';
			$return_pvp.=' >';

			if($price_riscado!='')$return_pvp.='<span>'.$price_riscado.'&nbsp;</span>';
			if($pvp>0)$return_pvp.=numberformat($pvp);


			$not_produto_standard=$prods['produto_standard'];
			$c_submissao_ficheiro=$prods['c_submissao_ficheiro'];

			if($not_produto_standard || $c_submissao_ficheiro)
			{
				$caminho=seo('pages.php?page_id='.$GLOBALS['products_page_id'], $id, 2, $GLOBALS['site_lang']);

				$botao= '<div class="fp_comprar_esgotado2 fancy_stock" href="'.$caminho.'">Ver mais detalhes</div>';
			}
			else
			{
				if($precos['STOCK'] > 0 && $prods['prod_precos']['PVP'] > 0 && $prods['prod_carrinho'] == 1) $botao = '<div class="prodby">Comprar<input type="hidden" value="'.$prods['prod_id'].'" name="prodid"><input type="hidden" value="" name="prodfiltros"></div>';
				else $botao= '<div class="fp_comprar_esgotado2 fancy_stock" href="'.$GLOBALS['site_http_ajax_url'].'setNotificacaoProduto.php?idprod='.$prods['prod_id'].'">Alerta de Stock</div>';
			}

			$return_pvp.='</div>';
			$return_pvp.='<div class="clear"></div>';
			if($pvp<=0)$return_pvp.='<span class="price" style="height:27px;display:block;text-align:center;cursor:pointer">Ver Info</span>';
			else $return_pvp.=$stock_txt.$wish.$botao;
		}

		

		$return_pvp.='<div class="clear"></div>';
		$return_pvp=str_replace('"',"'",$return_pvp);
		$return_pvp=str_replace('&euro; ',"€ ",$return_pvp);

		//agora wishlist:
		$wishlist='';
		if($dados=cliente_logado())
		{
			$cli_id=$dados['cli_id'];
			if(isProductInWishList($prod_id, $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $wishlist = getLinguas('key_wishlist_rem');
			else $wishlist = getLinguas('key_wishlist_add');
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInWishList($prod_id, 0, $_SESSION['CLIENTE_ID_TEMP'])) $wishlist = getLinguas('key_wishlist_rem');
			else $wishlist = getLinguas('key_wishlist_add');
		}
		else $wishlist = "-1";



		/*
		//agora wishlist:
		$comparador='';
		if($dados=cliente_logado())
		{
			$cli_id=$dados['cli_id'];
			if(isProductInComparador($prod_id, $cli_id, $_SESSION['CLIENTE_ID_TEMP'])) $comparador = getLinguas('key_comparador_rem');
			else $comparador = getLinguas('key_comparador_add');
		}
		elseif($_SESSION['CLIENTE_ID_TEMP'] != '')
		{
			if(isProductInComparador($prod_id, 0, $_SESSION['CLIENTE_ID_TEMP'])) $comparador = getLinguas('key_comparador_rem');
			else $comparador = getLinguas('key_comparador_add');
		}
		else $comparador = "-1";
		*/
		$comparador = "-1";



		//$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prod_id,$GLOBALS['site_lang']);
		//$prod_id=$rs->db_data['ID'];
		//$prods=\get_produtc_info($prod_id);
		//$url=str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['products_page_id'],$prods['prod_id'],2));

		//---------------------------------------------------------
		$link=$GLOBALS['site_http_url'].\seo_prod($prods);
		//---------------------------------------------------------
		$titulo= $prods['prod_nome'];



		//$titulo = str_replace('(SEMINOVO) ','',$titulo);
		$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
		$titulo = strip_tags($titulo,'<br>');
		
		$estado = $prods['estado'];
		$bateria = $prods['bateria'];
		if($estado != '')$titulo.=' GRAU: <b>'.$estado.'</b>';
		if($bateria != '' && $bateria != '0')$titulo.=' BAT: <b>'.$bateria.'%</b>';

		$titulo = str_replace('GRAU: ','<br>GRAU: ',$titulo);
		$titulo = str_replace('BAT: ','<br>BAT: ',$titulo);


		$titulo = preg_replace( "/\r|\n/", " ", $titulo);
		$titulo = str_replace('"','',$titulo);
		$titulo = str_replace('\\','',$titulo);
		//---------------------------------------------------------
		$global_promos='[{}]';
		if($prods['prod_precos']['PVP'] > 0)
		{
			$pvp=$prods['prod_precos']['PVP'];
		}
		if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
		{
			$price2=$prods['prod_precos']['PRECO_ANTIGO'];
			$global_promos='['.\show_global_promos($m_id).']';
		}


		$ref=$prods['prod_ref'];
		/*
		if(substr($ref, 0,3)=='REP')
		{
			//if(substr_count($ref, 'DIAGNOSTICO')) $img='diagno.jpg';
			if(substr_count($ref, 'FRP')) $img='GOOGLE.jpg';
			if(substr_count($ref, 'SIM')) $img='GAVETASIM.jpg';
			if(substr_count($ref, 'TOUCH')) $img='TOUCH.jpg';
			if(substr_count($ref, 'LCD')) $img='LCD.jpg';
			if(substr_count($ref, 'WIFI')) $img='WIFI.jpg';
			if(substr_count($ref, 'REDE')) $img='REDE.jpg';
			if(substr_count($ref, 'TDN')) $img='TDN.jpg';
			if(substr_count($ref, 'TDDI')) $img='TDDI.jpg';
			if(substr_count($ref, 'CAMARA')) $img='CAMARA.jpg';
			if(substr_count($ref, 'UNLOCK')) $img='UNLOCK.jpg';
			if(substr_count($ref, 'CODIGO')) $img='CODIGO.jpg';
			if(substr_count($ref, 'SWAP')) $img='SWAP.jpg';
			if(substr_count($ref, 'HOME')) $img='HOME.jpg';
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
			if(substr_count($ref, 'ONOFF')) $img='ONOFF.jpg';
			if(substr_count($ref, 'SILENCIO')) $img='SILENCIO.jpg';
			if(substr_count($ref, 'VOLUME')) $img='VOLUME.jpg';
			if(substr_count($ref, 'VIBRADOR')) $img='VIBRADOR.jpg';
			if(substr_count($ref, 'SENSOR')) $img='SENSOR.jpg';
			if(substr_count($ref, 'ICCARGA')) $img='IC_BOARD.jpg';
			if(substr_count($ref, 'AUDIOJACK')) $img='AUDIOJACK.jpg';
			if(substr_count($ref, 'VIDROTRAS')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'DILCD')) $img='DILCD.jpg';
			if(substr_count($ref, 'NLCD')) $img='NLCD.jpg';
			if(substr_count($ref, 'CAM3X')) $img='CAM3X.jpg';
			if(substr_count($ref, 'CAM2V')) $img='CAM2V.jpg';
			if(substr_count($ref, 'CAM2D')) $img='CAM2D.jpg';
			if(substr_count($ref, 'VTR3X')) $img='VTR3X.jpg';
			if(substr_count($ref, 'VTR2V')) $img='VTR2V.jpg';
			if(substr_count($ref, 'VTR2D')) $img='VTR2D.jpg';
			if(substr_count($ref, 'LCAM3X')) $img='LCAM3X.jpg';
			if(substr_count($ref, 'LCAM2V')) $img='LCAM2V.jpg';
			if(substr_count($ref, 'LCAM2D')) $img='LCAM2D.jpg';
			if(substr_count($ref, 'BMF')) $img='BMF.jpg';
			if(substr_count($ref, 'CFN')) $img='CFN.jpg';
			if(substr_count($ref, 'CFDI')) $img='CFDI.jpg';
			if(substr_count($ref, 'CARC2V')) $img='CARC2V.jpg';
			if(substr_count($ref, 'CARC2D')) $img='CARC2D.jpg';
			if(substr_count($ref, 'CARC3X')) $img='CARC3X.jpg';
			if(substr_count($ref, 'OLEDDILCD')) $img='DILCD.jpg';
			if(substr_count($ref, 'OLEDNLCD')) $img='NLCD.jpg';
			if(substr_count($ref, 'MBLCD')) $img='LCD_MAC.jpg';
			if(substr_count($ref, 'IMLCD')) $img='LCD_IMAC.jpg';
			if(substr_count($ref, 'MBBATERIA')) $img='BATERIA_MAC.jpg';
			if(substr_count($ref, 'IMBATERIA')) $img='BATERIA_IMAC.jpg';
			
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
			
			if(substr_count($ref, 'LENTECAMARA')) $img='LENTE.jpg';
			if(substr_count($ref, 'TAMPATRASEIRA')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'CAMARAFRONTAL')) $img='CAMARAFRONTAL.jpg';
			if(substr_count($ref, 'VIDROTRASEIRO')) $img='TRASEIRA.jpg';
			if(substr_count($ref, 'MODULOORIG')) $img='MODULO.jpg';
			if(substr_count($ref, 'MODULOCOMP')) $img='OLED.jpg';
			
			if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';
			if(substr_count($ref, 'TDNOFI')) $img='TDNOFI.jpg';
			if(substr_count($ref, 'DILCDOFI')) $img='DILCDOFI.jpg';
			if(substr_count($ref, 'NLCDOFI')) $img='NLCDOFI.jpg';
			if(substr_count($ref, 'BATOFI')) $img='BATOFI.jpg';
			if(substr_count($ref, 'CAM3XOFI')) $img='CAM3XOFI.jpg';
			if(substr_count($ref, 'CAM2VOFI')) $img='CAM2VOFI.jpg';
			if(substr_count($ref, 'CAM2DOFI')) $img='CAM2DOFI.jpg';
			if(substr_count($ref, 'TDDIOFI')) $img='TDDIOFI.jpg';

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

			$img_file=$GLOBALS['site_http_url'].'layout/imgs/'.$img;

			if(!is_file($GLOBALS['site_shell_url'].'layout/imgs/'.$img) )$img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';

		}
		*/

		$imgs_prod_total = get_imagem_produto_total($ref,$prod_id);
		$img_file = $imgs_prod_total['main'];



		if(substr_count($ref, 'DIAGNOSTICO')) $pvp=0;


		if($j>0)$f_json.=',';


		$f_json.='{
		"a1":"'.$prod_id.'",
		"a2":"'.$prods['prod_ordem'].'",
		"a3":"'.$prods['prod_ref'].'",
		"a4":"'.$pvp.'",
		"a5":"'.$price2.'",
		"a6":"'.$pvp.'",
		"a7":"'.$prods['prod_precos']['STOCK'].'",
		"a8":"'.addslashes($stock_txt).'",
		"a9":"'.$img_file.'",
		"a10":"'.$titulo.'",
		"a11":"",
		"a12":"'.$link.'",
		"a13":"'.$prods['prod_precos']['PROMOCAO'].'",
		"a14":"",
		"a15":'.$global_promos.',
		"a16":"'.$prods['prod_precos']['DESTAQUE'].'",
		"a17":"'.$prods['prod_id_marca'].'",
		"a32":"'.$prods['prod_marca']['marca_nome'].'",
		"a20":"'.$prods["u_imagetiq"].'",
		"a21":"'.$prods["u_catetiq"].'",
		"a22":"'.$prods["u_coretiq"].'",
		"a23":"'.$prods['prod_carrinho'].'",
		"a24":"'.$prods['prod_precos']['NOVO'].'",
		"a25":"",
		"a26":"'.$prods['prod_precos']['OUTLET'].'",
		"a33":"'.$prods['prod_precos']['OUTLETBOX'].'",
		"a27":"",
		"a28":"",
		"a29":"",
		"a30":"0",
		"a31":"'.$prods['produto_standard'].'"
		}';

		$j++;
	}

	$f_json.=']';
	$f_json=str_replace(array('title=\"\"', 'alt=\"\"'), '', $f_json);
	$f_json=str_replace('  ',' ',$f_json);
	$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);


	header('Content-Type: application/json; charset=utf-8');
	echo ($f_json);
?>