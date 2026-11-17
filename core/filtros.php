<?php
	//if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME']== __FILE__ ) exit();
	function filtros_left($tipo=0)
	{
		set_time_limit(0);
		if(isset($_GET['pesquisa']) && $_GET['pesquisa'] != '') 
		{
			$cache = '<div class="filtros-titulo">'.getLinguas('key_filtros', $GLOBALS['site_lang']).'</div>';
			$cache.='<div class="pesq_txt"><div class="pesq_txt_padding"><input placeholder="'.getLinguas('key_pesquisa_rapida', $GLOBALS['site_lang']).'" type="text" id="search_box" name="pesquisa" class="pesq_input" value="" /></div></div>';			
			global $conf_on_off;

			$cache .= '<div class="filtro-preco">';
	            $cache .= '<div class="filtro_titulo filtro_titulo_preco">';
	            	$cache .='<span>'.getLinguas('key_preco', $GLOBALS['site_lang']).'</span>';
	            $cache .='</div>';
	            $cache .= '<div class="intervalos-text">';
	            	$cache .='<div class="intervalo_titulo">'.getLinguas('key_intervalo', $GLOBALS['site_lang']).'</div>';
		            $cache .= '<div id="slider-snap-value-lower-text">0</div>';
		            $cache .= '<div>-</div>';
		            $cache .= '<div id="slider-snap-value-upper-text">1</div>';
	            $cache .= '</div>';
	            $cache .= '<div class="clear"></div>';
            	$cache .= '<div id="range" min="0" max="1"></div>';
	            $cache .= '<div class="intervalos">';
		            $cache .= '<div id="slider-snap-value-lower"></div>';
		            $cache .= '<div id="slider-snap-value-upper"></div>';
	            $cache .= '</div>';
            	$cache .= '<div class="clear"></div>';
        	$cache .= '</div>';

        	global $REPARACOES;
        	if($REPARACOES==1)
        	{
        		$cache .= '<div class="filtro-preco " id="filtro-stock"></div>';
				$cache .= '<div class="filtro-marcas drop " id="filtro-marcas"></div>';        		
        	}
        	else
        	{
				$cache .= '<div class="filtro-preco " id="filtro-stock"></div>';
				$cache .= '<div class="filtro-stock drop " id="filtro-stock"></div>';
				$cache .= '<div class="filtro-marcas drop " id="filtro-marcas"></div>';
        	}
			$cache .= '<div class="filtro-caracteristicas drop2"><h3></h3><ul id="c_l">';

			global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$obj=new DataObj;
			$sql="Select * from filters_categories inner join filters_categories_lang on filters_categories_lang.category_id=filters_categories.category_id where category_active=1";
			$obj->db_open($db_link, $DB_name, $sql);
			while(!$obj->EOF())
			{
				$cache .= '<li class="f_ativos3" id="f'.$obj->db_data['category_id'].'" style="display:none">';
				$cache .= '<h3 style="margin:0">';
				$cache .= '		<a href="#" class="parent" onclick="return false">';
				$cache .= '			<span id="f_ativos3">'.$obj->db_data['category_name'].'</span>';
				$cache .= '		</a>';
				$cache .= '	</h3>';
				$cache .= '<ul style="" id="v'.$obj->db_data['category_id'].'">';
				$cache .= '</ul>';
				$cache .= '</li>';
				$obj->db_next();
			}
			$obj->db_close();
			$cache .= '<div class="c"></div></ul></div>';
			if($tipo == 0) echo $cache;
			else return $cache;
		}
		elseif(isset($_GET['cat']) && intval($_GET['cat']) > 0)
		{
			$id_cat=intval($_GET['cat']);
			$f_original=$GLOBALS['site_shell_url'].'files/cache/filtro'.$_GET['cat'];
			$time_expire=time() - 60*60; // Expire Time (60 minuto)

			if((file_exists($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
			{
				$fh=fopen($f_original, 'r');
				$cache=(fread($fh, filesize($f_original)));
				fclose($fh);
			}
			else
			{
				$cache = '<div class="filtros-titulo">'.getLinguas('key_filtros', $GLOBALS['site_lang']).'</div>';
				$cache.='<div class="pesq_txt"><div class="pesq_txt_padding"><input placeholder="'.getLinguas('key_pesquisa_rapida', $GLOBALS['site_lang']).'" type="text" id="search_box" name="pesquisa" class="pesq_input" value="" /></div></div>';
				//Se estivermos no ultimo nivel de familias, vamos mostrar filtros
				global $conf_on_off;

				//$cache .= FILTRO_PRECOS_NEW(getPrecosblocos($id_cat));
				$cache .= FILTRO_PRECOS_NEW(array(0,1));
				
				//if($conf_on_off['brand_filter']==1) $cache .= FILTRO_MARCAS_NEW($id_cat,0); //Tem filtro por marca nos filtros
				//else $cache .= FILTRO_MARCAS_STANDARD($id_cat,0); //Tem filtro por marca nos filtros

				$cache .= '<div class="filtro-stock drop " id="filtro-stock"></div>';
				$cache .= '<div class="filtro-marcas drop " id="filtro-marcas"></div>'; 

				//a proxima função foi alterada para ser por JS
				//$cache .= FILTRO_CARACTERISTICAS_NEW($id_cat,0,0,2,$GLOBALS['site_lang']);
				$cache .= '<div class="filtro-caracteristicas drop2"><h3></h3><ul id="c_l">';

				global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$obj=new DataObj;
				$sql="Select category_id,category_name from filters_categories inner join filters_categories_lang on filters_categories_lang.category_id=filters_categories.category_id where category_active=1";
				$obj->db_open($db_link, $DB_name, $sql);
				while(!$obj->EOF())
				{
					$cache .= '<li class="f_ativos3" id="f'.$obj->db_data['category_id'].'" style="display:none">';
					$cache .= '<h3 style="margin:0">';
					$cache .= '		<a href="#" class="parent" onclick="return false">';
					$cache .= '			<span id="f_ativos3">'.$obj->db_data['category_name'].'</span>';
					$cache .= '		</a>';
					$cache .= '	</h3>';

					$cache .= '<ul style="" id="v'.$obj->db_data['category_id'].'">';

					$cache .= '</ul>';
					$cache .= '</li>';

					$obj->db_next();
				}
				$obj->db_close();

				$cache .= '<div class="c"></div></ul></div>';

			

				$fh=fopen($f_original, 'w');
				fwrite($fh, $cache);
				fclose($fh);
			}

			if($tipo == 0) echo $cache;
			else return $cache;
		}
	}
	function filtros_servicos($prod_id)
	{
		global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql="Select servicos from products where ID=".$prod_id;
		$return='';
		$value_id='';
		$obj->db_open($db_link, $DB_name, $sql);
		if(!$obj->EOF())
		{
		$return.='<div class="servicos"><div class="servicos_produto"><div><div class="nome_tab">Escolhe caso pretendas serviços adicionais</div></div></div><div class="row"><div class="col-md-12">';
		$filtros=explode(";",$obj->db_data['servicos']);
		foreach($filtros as $key => $id)
		{
			$obj2=new DataObj;
			$sql="Select s.ID, s.Tipo, s.Custos, s.margem, sl.Nome  from servicos as s INNER JOIN servicos_lang as sl on sl.ID_PAI=s.ID where s.ID=".$id;
			$obj2->db_open($db_link,$DB_name,$sql);
			if(!$obj2->EOF())
			{
				$Custo=$obj2->db_data['Custos'];
				$id=$obj2->db_data['ID'];
				$tipo=$obj2->db_data['Tipo'];
				$margem=$obj2->db_data['margem'];
				$Nome=$obj2->db_data['Nome'];
				if($tipo==1)
				{
					// tipo preço fixo
					$return.='<div class="servico"><input type="checkbox" onchange="verifica_servicos('.$prod_id.');" class="servicos_prod" value="'.$id.'" id="servico'.$key.'"><label for="servico'.$key.'" class="label_servicos">  '.$Nome.': <span class="custo_valor">'.sprintf("%.2f", $Custo).' &euro; <span style="font-size:10px;color:#005d93">inclui IVA à taxa legal em vigor</span></span> </label></div>';
					$value_id.=";".$id;
				}else if($tipo==2){
					// tipo percentual (ainda não se encontra desenvolvido)
					$return .='<div class="servico"><input type="checkbox" onchange="verifica_servicos('.$prod_id.');" value="'.$id.'" id="servico'.$key.'"><label for="servico'.$key.'" class="label_servicos">  '.$Nome.': <span class="custo_valor">'.$margem.'%</span></label></div>';
					$value_id.=";".$id;
				}
			}
			$obj2->db_close();
		}
		}
		$obj->db_close();
		$return.='</div></div></div>';
		$return.='<input type="hidden" id="serv_code" value="">';
		$return.='<script>
		function verifica_servicos(prod_id){
			var array_serv="";
			$(".servicos_prod").each(function( index ){
				if($(this).is(":checked"))
				{
					array_serv=array_serv+$(this).attr("value")+";";
				}
			});
			$("#serv_code").attr("value", array_serv);
			var codes="";
			total_selected=0;
			$("select.opcoesprod").find("option:selected").each(function()
				{
					cat_id=$(this).attr("data-catid");
					val_id=$(this).attr("data-valueid");
					if(cat_id > 0 && val_id > 0)
					{
						codes += cat_id+"-"+val_id+";";
						total_selected++;
					}
				});
			var tempo= $("#tempo_entrega").val();
			var mult=$("#quantidade_mult").attr("value");
			var qnt_esp=$("#qnt_esp").attr("value");
			var quanti=0;
			var largura=0;
			var altura=0;
			if(qnt_esp==1)
			{
				largura=$("#largura").attr("value");
				altura=$("#altura").attr("value");
			}
			else
			{
				quanti=$(".choose_quant_active").attr("value");
			}
			$.ajax({
				type: "POST",
				url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
				data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:quanti, lar:largura, alt:altura}
				})
				.done(function(res)
				{
					var result=res.split(",");
					$("#peco_fin").empty();
					$("#peco_fin").append(result[0]+"&euro;");
					$("#peco_liq").empty();
					$("#peco_liq").append(result[1]+"&euro;");
				});
		}
		</script>';
		return $return;
	}
	function filtros_quantidade($prod_id,$prod_combo,$prod_serv=0,$quantidade=1,$quant=0,$tempo=0,$altura=0,$largura=0)
	{
		global $DB_host,$DB_user,$DB_pass, $DB_name;
	    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		// vamos verificar se o produto não tem a flag de quantidade especial activada
		$qnt_esp=get_qnt_esp($prod_id);
		$return.='<input type="hidden" id="qnt_esp" value="'.$qnt_esp['flag_esp'].'">';
		if($qnt_esp['flag_esp']==1)
		{
			$return.='<div class="quantidade_esp col-md-12 row">';
			if($largura >0 && $largura < $qnt_esp['L_max'] && $largura > $qnt_esp['L_min'])
			{
				$valor_largura= $largura;
			}
			else
			{
				$valor_largura = $qnt_esp['L_padrao'] ;
			}
			if($altura >0 && $altura < $qnt_esp['A_max'] && $altura > $qnt_esp['A_min'])
			{
				$valor_altura=$altura;
			}
			else
			{
				$valor_altura=$qnt_esp['A_padrao'] ;
			}
			$return.='<div class="largura col-md-6 row"><div class="col-xs-12">Largura [cm]</div><div class="col-xs-6"><input type="number" id="largura" min="'.$qnt_esp['L_min'].'" max="'.$qnt_esp['L_max'].'" value="'.$valor_largura.'" onchange="verifica_valores(1,'.$qnt_esp['L_min'].','.$qnt_esp['L_max'].')" ></div></div>';
			$return.='<div class="altura col-md-6 row"><div class="col-xs-12">Altura [cm]</div><div class="col-xs-6"><input type="number" id="altura" min="'.$qnt_esp['A_min'].'" max="'.$qnt_esp['A_max'].'" value="'.$valor_altura.'" onchange="verifica_valores(2,'.$qnt_esp['A_min'].','.$qnt_esp['A_max'].')" ></div></div></div>';
			$return.='<script>
					function verifica_valores(campo,min,max){
						if(campo==1)
						{
							var nome_campo="#largura";
							var campo2="#unidades_largura";
						}else {
							var nome_campo="#altura";
							var campo2="#unidades_altura";
						}
						var valor=$(nome_campo).val();
						if(valor < min )
						{
							$(nome_campo).attr("value",min);
							$(campo2).empty();
							$(campo2).append(min);
						}else if(valor > max)
						{
							$(nome_campo).attr("value",max);
							$(campo2).empty();
							$(campo2).append(max);
						}else{
							$(campo2).empty();
							$(campo2).append(valor);
						}
						var prod_id='.$prod_id.';
						var codes="";
						total_select=$("select.opcoesprod").length;
						total_selected=0;
						$("select.opcoesprod").find("option:selected").each(function()
						{
						cat_id=$(this).attr("data-catid");
						val_id=$(this).attr("data-valueid");
						if(cat_id > 0 && val_id > 0)
						{
							codes += cat_id+"-"+val_id+";";
							total_selected++;
						}
						});

						var quanti=0;
						var largura=0;
						var altura=0;
						var qnt_esp=$("#qnt_esp").attr("value");
						if(qnt_esp==1)
						{
							largura=$("#largura").attr("value");
							altura=$("#altura").attr("value");
						}else{
						quanti=$(".choose_quant_active").attr("value");
						}
						var array_serv=$("#serv_code").attr("value");
						var tempo= $("#tempo_entrega").val();
						var mult=$("#quantidade_mult").attr("value");
						$.ajax(
						{
						type: "POST",
						url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
						data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:quanti, lar:largura, alt:altura }
						})
						.done(function(res)
						{
							var result=res.split(",");
							$("#peco_fin").empty();
							$("#peco_fin").append(result[0]+"&euro;");
							$("#peco_liq").empty();
							$("#peco_liq").append(result[1]+"&euro;");
						});


					}</script>';
		}else{

			$custo_entrega=1;
			if($tempo!=0 && $tempo!='' )
			{
				// vamos buscar o valor da variavel de tempo
				$rs5=new DataObj;
				$sql="Select custo_entrega from entrega where ID=".$tempo." and visivel=1";
				$rs5->db_open($db_link, $GLOBALS['DB_name'], $sql);
				if(!$rs5->EOF())
				{
					$custo_entrega=$rs5->db_data['custo_entrega']/100;
				}
				$rs5->db_close();
			}
			$rs=new DataObj;
			$sql='Select p_combo.obj_id , p_combo.obj_pvp, p_combo.obj_iva, p_combo.obj_qtdmin, p_combo.obj_promo, p_combo.obj_promo_ativo from products_objs_combo as p_combo INNER JOIN products_objs as p_o on p_o.obj_type_id=p_combo.obj_type_id where p_combo.obj_type_id='.$prod_id.' and p_combo.obj_combo2="'.$prod_combo.'" order by p_combo.obj_qtdmin ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$return.='<p>Preços / Qtd</p><div class="row">';
			if($quant==0){$i=0;$quantidade=1;}else{$i=1;}
			//$return.=$quant;
			while (!$rs->EOF())
			{
				$obj_id=$rs->db_data['obj_id'];
				$obj_pvp=$rs->db_data['obj_pvp']*$quantidade*$custo_entrega;
				$obj_iva=(intval($rs->db_data['obj_iva']))/100;
				$obj_qtdmin=intval($rs->db_data['obj_qtdmin']);
				$obj_promo=$rs->db_data['obj_promo']*$quantidade*$custo_entrega;
				$obj_promo_ativo=$rs->db_data['obj_promo_ativo'];
				if($obj_promo_ativo=1 && $obj_promo > 0 && $obj_promo < $obj_pvp)
				{
					$obj_pvp=$obj_promo;
				}
				/*$preco_Siva=numberformat($obj_pvp/(1+$obj_iva));*/
				$preco_Siva=$obj_pvp/(1+$obj_iva);

				if($i==0 || $obj_qtdmin==$quant ){$active='choose_quant_active';$quant_act=$obj_qtdmin;}else{$active='';};
				$return.='<div class="col-md-3 quantidades"><div onclick="change_color('.$obj_qtdmin.')" class="choose_quant '.$active.'" id="qnt'.$obj_qtdmin.'" value="'.$obj_qtdmin.'" preco_iva="'.$obj_pvp.'"  preco_siva="'.$preco_Siva.'"><i>'.$obj_qtdmin.' Qtd.</i> '.$preco_Siva.'<span>('.$obj_pvp.' com IVA)</span></div></div>';
				$rs->db_next();
				$i++;
			}
			$return.='</div>';
			$return.='
			<script>
				function change_color(id){
					console.log("BUG");

					$(".choose_quant_active").removeClass("choose_quant_active");
					$("#qnt"+id).addClass("choose_quant_active");
					var quantidade=$("#quantidade_mult").attr("value");
					quantidade= quantidade*id;
					$("#qnt_prod_span").empty();
					$("#qnt_prod_span").append(quantidade);
					$("#unidades_resumo").empty();
					$("#unidades_resumo").append(quantidade);
					var codes="";
					total_selected=0;
					$("select.opcoesprod").find("option:selected").each(function()
					{
						cat_id=$(this).attr("data-catid");
						val_id=$(this).attr("data-valueid");
						if(cat_id > 0 && val_id > 0)
						{
							codes += cat_id+"-"+val_id+";";
							total_selected++;
						}
					});
					var prod_id='.$prod_id.';
					var array_serv=$("#serv_code").attr("value");
					var tempo= $("#tempo_entrega").val();
					var mult=$("#quantidade_mult").attr("value");
					var qnt=$(".choose_quant_active").attr("value");

					$.ajax(
						{
						type: "POST",
						url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
						data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:qnt}
						})
						.done(function(res)
						{
							var result=res.split(",");
							$("#peco_fin").empty();
							$("#peco_fin").append(result[0]+"&euro;");
							$("#peco_liq").empty();
							$("#peco_liq").append(result[1]+"&euro;");
						});
				}
			</script>';
			$rs->db_close();
		}
		return $return;

	}
	//Esta funcao nao faz sentido
	function get_qnt_esp($id_prod)
	{
		return array('flag_esp'=>0);
	}

	function filtros_ficha_produto_grafica($prod_id=0, $fam_id=0, $desejado, $ids_limitados)
	{
	    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj2=new DataObj; $filtros=array();$return='';
		$sql2='
				SELECT
					fp.cat_id, fp.valor_id, fp.filtro_area
				FROM
					filters_products fp
				WHERE
					(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.')
					AND prod_id='.$prod_id.'
					AND fp.filtro_active=1
					AND (fp.filtro_area=1 OR fp.filtro_area=2)
				GROUP BY
					fp.valor_id, fp.cat_id
				Order By
					fp.filtro_id asc
				';
		$obj2->db_open($db_link,$DB_name,$sql2);
		$a=0;
		while(!$obj2->EOF())
		{
			$filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];
			$obj2->db_next();
		}
		$obj2->db_close();
		$i=1;
		$filtros_update=0;
		$return .= '<div class="row">';
		$combo_final=';';
		foreach($filtros as $key => $value)
		{
			$cat=buscacategoriaTipoeNomex($key);
			$sufix=' ';
			if($cat['category_type'] == 2)
			{
				$return .= '<div class="col-md-12">';
				$return .= '<p style="margin-bottom:0;display:block"><label>'.$cat['category_name'] . '</label>';
				$return .= '<div id="wrap_cores_'.$key.'"">';
				for($a=0;$a<count($value); $a++)
				{
					$nm_value=buscavalorfiltrosx_arr($value[$a]);
					$hex=$nm_value['values_colorhex'];
					$value_id=$nm_value['value_id'];
					$value_ext=$nm_value['value_ext'];
					$values_name=$nm_value['values_name'];
					$elementos=' id="opt_'.$key.'_'.$value[$a].'"  name="opt'.$key.'"';

					if($value_ext!='' && is_file($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext)) $return .= '<div class="col-xs-2 col-sm-2 col-md-2" style="padding:2px"><label  title="'.$values_name.'" class="label_check_ps" for="opt_'.$key.'_'.$value[$a].'" id="lbl'.$key.'_'.$value[$a].'"  data-catid="'.$key.'"><div class="f_color" style="background-image:url('.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.');border:#c0c0c0 1px solid"></div><div id="f_right"><input '.$elementos.' type="radio" class="colors" /></div><div class="c"></div></label></div>';
					else $return .= '<div class="col-xs-2 col-sm-2 col-md-2" style="padding:2px"><label title="'.$values_name.'" class="label_check_ps" for="opt_'.$key.'_'.$value[$a].'" id="lbl'.$key.'_'.$value[$a].'"  data-catid="'.$key.'"><div class="f_color" style="background-color:#'.ltrim($hex, '#').';border:#c0c0c0 1px solid"></div><div id="f_right"><input '.$elementos.' type="radio" class="colors" /></div><div class="c"></div></label></div>';
				}
				$return .= '</div><div class="clear"></div></p>';
				$return .= '<p id="wrap_'.$key.'" style="display:none">';
				$return .= '<select name="opcoes[]" class="opcoesprod" id="sel'.$key.'" datacatid="'.$key.'" style="">';
				for($a=0;$a<count($value); $a++)
				{
					$nm_value=buscavalorfiltrosx($value[$a]);
					$return .= '<option '.$estado_option.' value="'.$cat['category_name'].': '.$nm_value.'" data-catid="'.$key.'" data-valueid="'.$value[$a].'" data-prodid="'.$prod_id.'" id="sel_lbl'.$key.'_'.$value[$a].'"';
					if(isset($_GET['op' . $i]) && $_GET['op'.$i]==$value[$a]){$return .= ' selected';$filtros_update=1;}
					$return .= '>'.$nm_value.$sufix.'</option>';
				}
				$return .= '</select>';
				$return .= '</p>';
				$return .= '</div>';
			}
			else
			{
				$combo_atual=$combo_final;
				//Vai buscar as categorias de cada filtro
				$return .= '<div class="col-md-12">';
				$return .= '<p class="filter" id="wrap_'.$key.'"><label>'.$cat['category_name'].'</label><br>';
				$return .= '<select name="opcoes[]" class="opcoesprod" datacatid="'.$key.'" >';

				$esta_seleccionado=0;
				$ultimo_com_preco=0;
				for($a=0;$a<count($value); $a++)
				{
				//echo "<br><br>".$cat['category_name'].":".$combo_atual.$key."-".$value[$a].";"."<br>";
					//Vamos mostrar apenas as opções que tem preço inserido
					if(filtro_tem_preco($combo_atual.$key."-".$value[$a].";"))
					{
						$ultimo_com_preco=$a;

						//Agora vai buscar todas as opções de filtros para cada categoria
						$nm_value=buscavalorfiltrosx($value[$a]);
						// vamos verificar se o utilizador já escolheu um dos valores de um dos filtros , caso sim temos que por essa solução como selected,
						// caso não temos que por as opçoes pelas quais não existe combo com os filtros escolhidos como disable
						$estado_option='';
						if( $ids_limitados!='' && isset($ids_limitados) && $desejado!='' && isset($desejado) )
						{
							// 1º verificar se esta foi uma categoria em que o utilizador já escolheu um valor
							$opcoes_escolhidas=explode(';',$desejado);
							foreach ($opcoes_escolhidas as $opt)
							{
								$segmentos=explode('-',$opt);
								if($segmentos[0]==$key && $segmentos[1]==$value[$a])
								{
									$estado_option='selected';
								}
							}
							// 2º temos que verificar quais são as opçoes que irão estar disable, por não existir uma combinação com os filtros selecionados
							$flag_antidisabel=0;
						}
						else
						{
							if($a==0)$estado_option='selected'; // apenas utilizado na ª1 vez que se faz o load da página do produto
						}
						$return .= '<option '.$estado_option.' value="'.$cat['category_name'].': '.$nm_value.'" data-catid="'.$key.'" data-valueid="'.$value[$a].'" data-prodid="'.$prod_id.'" ';

						if(isset($_GET['op' . $i]) && $_GET['op'.$i]==$value[$a]){$return .= ' selected';$filtros_update=1;}
						$return .= '>'.$nm_value.$sufix.$disable_filtro.'</option>';
						if($estado_option=="selected")
						{
							$combo_final.= $key."-".$value[$a].";";
							$esta_seleccionado=1;
						}
					}
				}
				//Se nao encontrou nenhum
				if($esta_seleccionado==0)
				{
					//Vai carregar o ultimo
					$combo_final.= $key."-".$value[$ultimo_com_preco].";";
				}
				$return .= '</select>';
				$return .= '</p>';
				$return .= '</div>';
			}
			$i++;
		}
		if($filtros_update) $return .= '<script>filtros_update();</script>';
		// vamos criar o filtro do tempo de entrega desejado
		$obj3=new DataObj;
		$sql='Select entregas from products where ID='.$prod_id;
		$obj3->db_open($db_link,$DB_name,$sql);
		if(!$obj3->EOF())
		{
			$ids_entregas=explode(';',$obj3->db_data['entregas']);
			if($ids_entregas!='')
			{
				$return.='<div id="entregas_select" class="col-md-12"><p><lable id="entrega_lbl">Tempo de Realização</label></p>';
				$return.='<select id="tempo_entrega" onchange="muda_tempo_select('.$prod_id.');" >';
				$i=0;
				foreach($ids_entregas as $key => $id)
				{
					$obj4=new DataObj;
					$sql="Select e.dias, e.ID, e.custo_entrega, el.Nome from entrega as e INNER JOIN entrega_lang as el on el.ID_PAI=e.ID where e.ID=".$id;
					$obj4->db_open($db_link,$DB_name,$sql);
					$nome=$obj4->db_data['Nome'];
					$custo=$obj4->db_data['custo_entrega'];
					$dias=$obj4->db_data['dias'];
					$ID_select=$obj4->db_data['ID'];
					$obj4->db_close();
					$selected='';
					if($i==0)
					{
						$selected='selected';
						$quanti_id=$id;
					}
					$return.='<option '.$selected.' value='.$ID_select.'>'.$nome.'</option>';
					$i++;
				}
				$return.='</select></div>';
				$return.='<script>
				function muda_tempo_select(prod_id){
				var tempo= $("#tempo_entrega").val();
				$.ajax(
					{
					type: "POST",
					url: SITE_HTTP_AJAX_URL + "ajaxShop_muda_data_grafica.php",
					data: { tempo:tempo}
					})
					.done(function(res)
					{
						$("#t_realizacao").empty();
						$("#t_realizacao").append(res);
				});
				var codes="";
				total_selected=0;
				$("select.opcoesprod").find("option:selected").each(function()
				{
					cat_id=$(this).attr("data-catid");
					val_id=$(this).attr("data-valueid");
					if(cat_id > 0 && val_id > 0)
					{
						codes += cat_id+"-"+val_id+";";
						total_selected++;
					}
				});
				var prod_id='.$prod_id.';
				var array_serv=$("#serv_code").attr("value");
				var mult=$("#quantidade_mult").attr("value");
				var qnt=0;
				var qnt_esp = $("#qnt_esp").attr("value");
				var altura=0;
				var largura=0;
				if(qnt_esp==1)
				{
					largura=$("#largura").attr("value");
					altura=$("#altura").attr("value");
				}else{
				 qnt=$(".choose_quant_active").attr("value");
				}

				$.ajax(
				{
				type: "POST",
				url: SITE_HTTP_AJAX_URL + "ajaxShop_quantidade_grafica.php",
				data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:qnt, alt:altura, lar:largura}
				})
				.done(function(res)
				{
					$(".container_info_quantidade").empty();
					$(".container_info_quantidade").append(res);
				});

				$.ajax(
					{
					type: "POST",
					url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
					data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:qnt, alt:altura, lar:largura}
					})
					.done(function(res)
					{
						var result=res.split(",");
						$("#peco_fin").empty();
						$("#peco_fin").append(result[0]+"&euro;");
						$("#peco_liq").empty();
						$("#peco_liq").append(result[1]+"&euro;");
				});

				}
			</script>';
			}
			//$return.=$ids_entregas[1];
		}
		$obj3->db_close();
		if( $ids_limitados!='' && isset($ids_limitados) && $desejado!='' && isset($desejado) )
		{
			return $return;
		}
		else
		{
			return array('html' => $return, 'combo' => $combo_final, 'quantidade' => $quanti_id);
		}
	}

	function filtro_tem_preco($combo='')
	{
		global $site_lang;
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$sql='SELECT obj_id FROM products_objs_combo WHERE obj_combo2 like "%'.$combo.'%" AND obj_pvp > 0';
		$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
		$tem=0;
		if(!$rs->EOF())$tem=1;
		$rs->db_close();
		return $tem;
	}

	function filtros_ficha_produto($prod_id=0, $fam_id=0, $desejado='', $ids_limitados='')
	{
	    global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj2=new DataObj; $filtros=array();$return='';
		$sql2='
				SELECT
					fp.cat_id, fp.valor_id, fp.filtro_area
				FROM
					filters_products fp
				WHERE
					(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.')
					AND prod_id='.$prod_id.'
					AND fp.filtro_active=1
					AND (fp.filtro_area=1 OR fp.filtro_area=2)
				GROUP BY
					fp.valor_id, fp.cat_id
				Order By
					fp.filtro_id asc
				';
		$obj2->db_open($db_link,$DB_name,$sql2);
		$a=0;
		while(!$obj2->EOF())
		{
			$filtros[$obj2->db_data['cat_id']][]=$obj2->db_data['valor_id'];
			$obj2->db_next();
		}
		$obj2->db_close();
		$i=1;
		$filtros_update=0;
		$return .= '<div class="row">';
		$combo_final=';';
		foreach($filtros as $key => $value)
		{
			$cat=buscacategoriaTipoeNomex($key);
			$sufix=' ';
			if($cat['category_type'] == 2)
			{
				$return .= '<div class="col-md-12">';
				$return .= '<p style="margin-bottom:0;display:block"><label>'.$cat['category_name'] . '</label>';
				$return .= '<div id="wrap_cores_'.$key.'"">';
				for($a=0;$a<count($value); $a++)
				{
					$nm_value=buscavalorfiltrosx_arr($value[$a]);
					$hex=$nm_value['values_colorhex'];
					$value_id=$nm_value['value_id'];
					$value_ext=$nm_value['value_ext'];
					$values_name=$nm_value['values_name'];
					$elementos=' id="opt_'.$key.'_'.$value[$a].'"  name="opt'.$key.'"';

					if($value_ext!='' && is_file($GLOBALS['site_shell_url'].'files/filters/'.$value_id.'_1.'.$value_ext)) $return .= '<div class="col-xs-2 col-sm-2 col-md-2" style="padding:2px"><label  title="'.$values_name.'" class="label_check_ps" for="opt_'.$key.'_'.$value[$a].'" id="lbl'.$key.'_'.$value[$a].'"  data-catid="'.$key.'"><div class="f_color" style="background-image:url('.$GLOBALS['site_http_url'].'files/filters/'.$value_id.'_1.'.$value_ext.');border:#c0c0c0 1px solid"></div><div id="f_right"><input '.$elementos.' type="radio" class="colors" /></div><div class="c"></div></label></div>';
					else $return .= '<div class="col-xs-2 col-sm-2 col-md-2" style="padding:2px"><label title="'.$values_name.'" class="label_check_ps" for="opt_'.$key.'_'.$value[$a].'" id="lbl'.$key.'_'.$value[$a].'"  data-catid="'.$key.'"><div class="f_color" style="background-color:#'.ltrim($hex, '#').';border:#c0c0c0 1px solid"></div><div id="f_right"><input '.$elementos.' type="radio" class="colors" /></div><div class="c"></div></label></div>';
				}
				$return .= '</div><div class="clear"></div></p>';
				$return .= '<p id="wrap_'.$key.'" style="display:none">';
				$return .= '<select name="opcoes[]" class="opcoesprod" id="sel'.$key.'" datacatid="'.$key.'" style="">';
				for($a=0;$a<count($value); $a++)
				{
					$nm_value=buscavalorfiltrosx($value[$a]);
					$return .= '<option '.$estado_option.' value="'.$cat['category_name'].': '.$nm_value.'" data-catid="'.$key.'" data-valueid="'.$value[$a].'" data-prodid="'.$prod_id.'" id="sel_lbl'.$key.'_'.$value[$a].'"';
					if(isset($_GET['op' . $i]) && $_GET['op'.$i]==$value[$a]){$return .= ' selected';$filtros_update=1;}
					$return .= '>'.$nm_value.$sufix.'</option>';
				}
				$return .= '</select>';
				$return .= '</p>';
				$return .= '</div>';
			}
			else
			{
				$combo_atual=$combo_final;
				//Vai buscar as categorias de cada filtro
				$return .= '<div class="col-md-12">';
				$return .= '<p class="filter" id="wrap_'.$key.'"><label>'.$cat['category_name'].'</label><br>';
				$return .= '<select name="opcoes[]" class="opcoesprod" datacatid="'.$key.'" >';

				$esta_seleccionado=0;
				$ultimo_com_preco=0;
				for($a=0;$a<count($value); $a++)
				{
				//echo "<br><br>".$cat['category_name'].":".$combo_atual.$key."-".$value[$a].";"."<br>";
					//Vamos mostrar apenas as opções que tem preço inserido
					if(filtro_tem_preco($combo_atual.$key."-".$value[$a].";"))
					{
						$ultimo_com_preco=$a;

						//Agora vai buscar todas as opções de filtros para cada categoria
						$nm_value=buscavalorfiltrosx($value[$a]);
						// vamos verificar se o utilizador já escolheu um dos valores de um dos filtros , caso sim temos que por essa solução como selected,
						// caso não temos que por as opçoes pelas quais não existe combo com os filtros escolhidos como disable
						$estado_option='';
						if( $ids_limitados!='' && isset($ids_limitados) && $desejado!='' && isset($desejado) )
						{
							// 1º verificar se esta foi uma categoria em que o utilizador já escolheu um valor
							$opcoes_escolhidas=explode(';',$desejado);
							foreach ($opcoes_escolhidas as $opt)
							{
								$segmentos=explode('-',$opt);
								if($segmentos[0]==$key && $segmentos[1]==$value[$a])
								{
									$estado_option='selected';
								}
							}
							// 2º temos que verificar quais são as opçoes que irão estar disable, por não existir uma combinação com os filtros selecionados
							$flag_antidisabel=0;
						}
						else
						{
							if($a==0)$estado_option='selected'; // apenas utilizado na ª1 vez que se faz o load da página do produto
						}
						$return .= '<option '.$estado_option.' value="'.$cat['category_name'].': '.$nm_value.'" data-catid="'.$key.'" data-valueid="'.$value[$a].'" data-prodid="'.$prod_id.'" ';

						if(isset($_GET['op' . $i]) && $_GET['op'.$i]==$value[$a]){$return .= ' selected';$filtros_update=1;}
						$return .= '>'.$nm_value.$sufix.$disable_filtro.'</option>';
						if($estado_option=="selected")
						{
							$combo_final.= $key."-".$value[$a].";";
							$esta_seleccionado=1;
						}
					}
				}
				//Se nao encontrou nenhum
				if($esta_seleccionado==0)
				{
					//Vai carregar o ultimo
					$combo_final.= $key."-".$value[$ultimo_com_preco].";";
				}
				$return .= '</select>';
				$return .= '</p>';
				$return .= '</div>';
			}
			$i++;
		}
		if($filtros_update) $return .= '<script>filtros_update();</script>';
		// vamos criar o filtro do tempo de entrega desejado
		
				$return.='<script>
				
				var codes="";
				total_selected=0;
				$("select.opcoesprod").find("option:selected").each(function()
				{
					cat_id=$(this).attr("data-catid");
					val_id=$(this).attr("data-valueid");
					if(cat_id > 0 && val_id > 0)
					{
						codes += cat_id+"-"+val_id+";";
						total_selected++;
					}
				});
				var prod_id='.$prod_id.';
				var array_serv=$("#serv_code").attr("value");
				var mult=$("#quantidade_mult").attr("value");
				var qnt=0;
				var qnt_esp = $("#qnt_esp").attr("value");
				var altura=0;
				var largura=0;
				var tempo="";
				if(qnt_esp==1)
				{
					largura=$("#largura").attr("value");
					altura=$("#altura").attr("value");
				}else{
				 qnt=$(".choose_quant_active").attr("value");
				}

				$.ajax(
				{
					type: "POST",
					url: SITE_HTTP_AJAX_URL + "ajaxShop_quantidade_grafica.php",
					data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:qnt, alt:altura, lar:largura}
				})
				.done(function(res)
				{
					$(".container_info_quantidade").empty();
					$(".container_info_quantidade").append(res);
				});

				/*$.ajax(
					{
						type: "POST",
						url: SITE_HTTP_AJAX_URL + "ajaxShop_mudanca_preco_grafica.php",
						data: { codes:codes, prodid:prod_id, tempo:tempo, servicos:array_serv, multiple:mult, qnt:qnt, alt:altura, lar:largura}
					})
					.done(function(res)
					{
						var result=res.split(",");
						$("#peco_fin").empty();
						$("#peco_fin").append(result[0]+"&euro;");
						$("#peco_liq").empty();
						$("#peco_liq").append(result[1]+"&euro;");
				});*/

				
			</script>';
			

		return $return;
		
	}

	function filtros_ficha_produto_combo($prod_id=0,$codes='')
	{
		global $site_lang;
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$sql='UPDATE products_objs_combo SET obj_combo2=concat(";", obj_combo, ";")';
		Runquery($db_link,$GLOBALS['DB_name'],$sql);
		$code=explode('|',$codes);
		$and='';
		for($a=0; $a<count($code); $a++)
		{
			$and .= ' AND fp.obj_combo2 LIKE "%;'.$code[$a].';%"';
		}
		$obj2=new DataObj; $filtros=array();$return='';
		$sql2='SELECT fp.obj_id,fp.obj_pvp,fp.obj_combo FROM products_objs_combo fp WHERE fp.obj_type_id='.$prod_id.''.$and.'AND fp.obj_pvp > 0';
		
		$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
		$a=0;
		$id_final=0;
		while(!$obj2->EOF())
		{
			$dt=explode(';',$obj2->db_data['obj_combo']);
			for($x=0; $x<count($dt); $x++)
			{
				$sl=explode('-',$dt[$x]);
				$key=$sl[0];
				$val=$sl[1];
				if($cat_id!=$key) $filtros[$key][]=$val;
			}
			$id_final=$obj2->db_data['obj_id'];
			$obj2->db_next();
		}
		$obj2->db_close();
		foreach($filtros as $key => $value)
		{
			$cat=buscacategoriaTipoeNomex($key);
			$sufix=' ';
			$sufix='';
			$return .= $key.'[spl2]';
			if($cat['category_type']!=2) $return .= '<p id="wrap_'.$key.'"><label>'.$cat['category_name'].'</label><br>';
			$return .= '<select name="opcoes[]" class="opcoesprod" id="sel'.$key.'" datacatid="'.$key.'">';
			$return .= '<option value="">'.getLinguas('key_escolha').'</option>';
			$array_val=array();
			for($a=0;$a<count($value); $a++)
			{
				$nm_value=buscavalorfiltrosx($value[$a]);
				if(!in_array($value[$a],$array_val))
				{
					$return .= '<option value="'.$cat['category_name'].': '.$nm_value.'" data-catid="'.$key.'" data-valueid="'.$value[$a].'" data-prodid="'.$prod_id.'" id="sel_lbl'.$key.'_'.$value[$a].'">'.$nm_value.$sufix.'</option>';
					$array_val[]=$value[$a];
				}
			}
			$return .= '</select>';
			if($cat['category_type']!=2)$return .= '</p>';
			$return .= '<div class="c"></div>[spl]';
		}
		return array('return' => $return, 'combo_id' => $id_final);
	}
	function filtros_ficha_produto_combo_opts($prod_id=0,$familia='',$marcax='',$name='',$linkz='',$foto='')
	{
		global $site_lang;
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$and='';
		$obj2=new DataObj; $filtros=array();$return='';
		$sql2='SELECT
					fp.obj_id, fp.obj_pvp, fp.obj_combo, fp.obj_ref, fp.obj_stock
				FROM
					products_objs_combo fp
				WHERE
					fp.obj_type_id='.$prod_id.'
					'.$and.'
					AND fp.obj_pvp > 0
					AND fp.obj_ref <> ""';
		$obj2->db_open($db_link,$GLOBALS['DB_name'],$sql2);
		$a=0;
		$id_final=0;
		$prods_tz='';
		while(!$obj2->EOF())
		{
			$dt=explode(';',$obj2->db_data['obj_combo']);
			$filtros='';
			$filtros_txt='';
			$b=0;
			for($x=0; $x<count($dt); $x++)
			{
				$sl=explode('-',$dt[$x]);
				$key=$sl[0];
				$val=$sl[1];
				if($cat_id!=$key)
				{
					if($b == 0) $filtros .= '?op'.($x+1).'='.$val;
					else $filtros .= '&op'.($x+1).'='.$val;
					$filtros_txt.=' '.buscavalorfiltrosx($val);
					$b++;
				}
			}
			$ref=$obj2->db_data['obj_ref'];
			$v_portes=0; $Ref_icecat='';
			$t_stock=$obj2->db_data['obj_stock'];
			if($t_stock <= 0) $disponib='Esgotado';
			elseif($t_stock > 0)
			{
				if($t_stock <= 5) $disponib='Stock Limitado';
				else $disponib='Em Stock';
			}
			$precos=array(); $precos=get_produtc_price_combo($prod_id,$obj2->db_data['obj_id']);
			$linkz2='';
			$linkz2=$linkz.$filtros;
			$prods_tz .= $familia.";".$marcax.";".$ref.";".$Ref_icecat.";".$name.$filtros_txt.";".$precos['PVP'].";".$linkz2.";".$foto.";".$disponib.";".$v_portes."\n";
			$obj2->db_next();
		}
		$obj2->db_close();
		return $prods_tz;
	}


	function FILTRO_FAMILIAS_NEW($fam_id=0, $tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		$return='';
		if((isset($_GET['filtro_marcas']) && $_GET['filtro_marcas'] > 0) || ($fam_id > 0 && $tipo==1) || ($fam_id == 0 && $tipo=='novidade') || ($fam_id == 0 && $tipo==4))
		{
			if($tipo==4)
			{
				$sql='SELECT fp.fam_id1
				FROM filters_products fp
				INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
				INNER JOIN brands b ON b.ID=fp.marca_id
				WHERE fp.fam_id1 <> 0 AND fp.filtro_active=1 AND (po.obj_promo_ativo=1 AND po.obj_promo_data >= CURDATE()) AND b.Visivel=1
				GROUP BY fp.fam_id1';
			}
			elseif($tipo=='novidade' && !is_int($tipo))
			{
				$sql='SELECT fp.fam_id1
				FROM filters_products fp
				INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
				INNER JOIN brands b ON b.ID=fp.marca_id
				WHERE fp.fam_id1 <> 0 AND fp.filtro_active=1 AND (po.obj_novo=1 AND po.obj_novo_data >= CURDATE()) AND b.Visivel=1
				GROUP BY fp.fam_id1';
			}
			elseif($fam_id > 0 && $tipo==1) $sql='SELECT fam_id1 FROM filters_products WHERE marca_id='.$fam_id.' AND filtro_active=1 GROUP BY fam_id1';
			else $sql='SELECT fam_id1 FROM filters_products WHERE marca_id='.$_GET['filtro_marcas'].' AND filtro_active=1 AND filtros_rel2 <> "" GROUP BY fam_id1';

			$familia->db_open($db_link, $DB_name, $sql);
			if(!$familia->EOF())
			{
				if($tipo==4 || ($tipo=='novidade' && !is_int($tipo))) $return .= '<ul id="f_l" style="margin-right:10px">';
				else $return .= '<ul id="f_l" style="padding:0;margin-right:3px;float:right">';
				$return .= '<select id="fam_level1" name="fam_level1" class="fam_level1 chzn-select2">';

				$return.='<option value="">Categoria</option>';
				while(!$familia->EOF())
				{
					$ID_FAMILIA=$familia->db_data['fam_id1'];
					$t=buscacabecalho_cf($ID_FAMILIA);
					$return.='<option value="'.$ID_FAMILIA.'">'.$t.'</option>';
					$familia->db_next();
				}
				$return.='</select>';
				$return.='</ul><div class="c"></div>';
			}
			$familia->db_close();
		}

		return $return;
	}
	function FILTRO_MARCAS_NEW($fam_id=0, $tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$conf_on_off;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$marcas=new DataObj;
		$return='';
		$and='';
		$nivel=0;

		if($tipo==5)
		{
			$sql='SELECT fp.marca_id
				FROM filters_products fp
				INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
				INNER JOIN brands b ON b.ID=fp.marca_id
				WHERE fp.marca_id <> 0 AND fp.filtro_active=1 AND po.obj_destaque=1 AND b.Visivel=1
				GROUP BY fp.marca_id';
		}
		else if($tipo=='outlet' && !is_int($tipo))
		{
			$sql='SELECT fp.marca_id
			FROM filters_products fp
			INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
			INNER JOIN brands b ON b.ID=fp.marca_id
			WHERE fp.marca_id <> 0 AND fp.filtro_active=1 AND po.obj_outlet=1 AND b.Visivel=1
			GROUP BY fp.marca_id';
		}
		else if($tipo=='novidade' && !is_int($tipo))
		{
			$sql='
					SELECT
						fp.marca_id
					FROM
						filters_products fp
						INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
						INNER JOIN brands b ON b.ID=fp.marca_id
					WHERE
						fp.marca_id <> 0
						AND fp.filtro_active=1
						AND (po.obj_novo=1 AND po.obj_novo_data >= CURDATE())
						AND b.Visivel=1
					GROUP BY
						fp.marca_id
					';
		}
		else if($tipo==4)
		{
			$sql='
					SELECT
						fp.marca_id
					FROM
						filters_products fp
						INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
						INNER JOIN brands b ON b.ID=fp.marca_id
						WHERE
							fp.marca_id <> 0
							AND fp.filtro_active=1
							AND (po.obj_promo_ativo=1 AND po.obj_promo_data >= CURDATE())
							AND b.Visivel=1
						GROUP BY
							fp.marca_id
					';
		}
		else if($tipo==3)
		{
			//pesquisa
			global $Str_pesquisar,$site_lang;
			$Str_pesquisar=strtolower($Str_pesquisar);
			$and.=' lower(pol.Cabecalho) like "%'.$Str_pesquisar.'%" ';
			$and.=' OR lower(pl.Cabecalho) like "%'.$Str_pesquisar.'%" ';
			$sql='SELECT pf.marca_id
			FROM products p
			INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
			INNER JOIN filters_products pf ON pf.prod_id=p.ID
			INNER JOIN products_objs po ON po.obj_type_id=p.ID
			INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE ( '.$and.' ) AND pf.marca_id <> 0 AND pf.filtro_active=1 AND p.Aproved=1 AND pl.Lang="'.$site_lang.'" AND pol.Lang="'.$site_lang.'" AND b.Visivel=1 group by pf.marca_id';
		}
		else
		{
			$sql='SELECT b.ID AS marca_id, bl.Nome
			FROM products p
			INNER JOIN products_families_obj as PFO ON p.ID=PFO.ID_PROD
			INNER JOIN brands b ON b.ID = p.ID_Marca
			INNER JOIN brands_lang bl ON bl.ID_PAI = b.ID
			AND bl.Lang = "'.$GLOBALS['site_lang'].'"
			WHERE (PFO.ID_ORG ='.$fam_id.')
			AND p.ID_Marca <>0
			AND b.Visivel =1
			GROUP BY p.ID_Marca';


			/*$sql='SELECT b.ID as marca_id, bl.Nome
			FROM filters_products fp
			INNER JOIN brands b ON b.ID=fp.marca_id
			INNER JOIN brands_lang bl ON bl.ID_PAI = b.ID AND bl.Lang = "'.$GLOBALS['site_lang'].'"
			WHERE (fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.') AND fp.marca_id <> 0 AND fp.filtro_active=1 AND b.Visivel=1
			GROUP BY fp.marca_id';*/
		}

		$marcas->db_open($db_link,$DB_name,$sql);
		if(!$marcas->EOF())
		{
			while(!$marcas->EOF())
			{
				$id_m=$marcas->db_data["marca_id"];
				$nome_m=$marcas->db_data["Nome"];
				$arr[]=array($id_m, $nome_m, $nivel);
				$marcas->db_next();
			}
		}
		$marcas->db_close();
		$arr=sort_arr_of_obj($arr,"1","asc");
		$return='';
		$total=count($arr);

		//---------------------------------------------------
		global $REPARACOES;
    	if($REPARACOES==1)
    	{
    		
    	}
    	else
    	{
			$return .= '<div class="filtro-stock drop">';
			$return .= '<h3>Stock</h3>';
			$return .= '<ul id="s_l">';
			$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="com_stock"><div id="f_left"><span>Com Stock</span></div><div id="f_right"><input id="com_stock" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
			//$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="por_encomenda"><div id="f_left"><span>Por encomenda</span></div><div id="f_right"><input id="por_encomenda" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
			$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="sem_stock"><div id="f_left"><span>Sem Stock</span></div><div id="f_right"><input id="sem_stock" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
			$return.='</ul><div class="c"></div></div>';
    	}

		//---------------------------------------------------

		if($total > 0)
		{
			$f=$total;
			$return .= '<div class="filtro-marcas drop">';
			$return .= '<h3>'.getLinguas('key_fabricante', $GLOBALS['site_lang']).'</h3>';
			$return .= '<ul id="m_l">';
			$f2=$f;
			for($a=0; $a<$f2; $a++)
			{
				$id_m=$arr[$a][0];
				$titulo=$arr[$a][1];
				$nivel=$arr[$a][2];

				 $class='class="active"';
				if(isset($_GET['cat']) && $_GET['cat'] == $catalogo_id) $class='class="active2"';

				$return.='<li '.$class.'><h3 style="margin:0"><label class="label_check_p" for="marca_'.$id_m.'"><div id="f_left"><span>'.$titulo.'</span></div> <div id="f_right"><input id="marca_'.$id_m.'" value="'.$id_m.'" type="checkbox"></div><div class="c"></div></label></h3></li>';
			}
			$return.='</ul><div class="c"></div></div>';
		}
		return $return;
	}
	function FILTRO_MARCAS_STANDARD($fam_id=0, $tipo=0)
	{return;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$conf_on_off;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$marcas=new DataObj;
		$return='';
		$and='';
		$nivel=0;

		if($tipo==5)
		{
			$sql='SELECT p.ID_Marca
				FROM products p
				INNER JOIN products_objs po on po.obj_type_id=p.ID
				INNER JOIN brands b ON b.ID=p.ID_Marca
				WHERE po.obj_destaque=1 AND b.Visivel=1
				GROUP BY p.ID_Marca';
		}
		else if($tipo=='outlet' && !is_int($tipo))
		{
			$sql='SELECT p.ID_Marca
				FROM products p
				INNER JOIN products_objs po on po.obj_type_id=p.ID
				INNER JOIN brands b ON b.ID=p.ID_Marca
				WHERE po.obj_outlet=1 AND b.Visivel=1
				GROUP BY p.ID_Marca';
		}
		else if($tipo=='novidade' && !is_int($tipo))
		{
			$sql='SELECT p.ID_Marca
				FROM products p
				INNER JOIN products_objs po on po.obj_type_id=p.ID
				INNER JOIN brands b ON b.ID=p.ID_Marca
				WHERE (po.obj_novo=1 AND po.obj_novo_data >= CURDATE()) AND b.Visivel=1
				GROUP BY p.ID_Marca';
		}
		else if($tipo==4)
		{
			$sql='SELECT p.ID_Marca
				FROM products p
				INNER JOIN products_objs po on po.obj_type_id=p.ID
				INNER JOIN brands b ON b.ID=p.ID_Marca
				WHERE (po.obj_promo_ativo=1 AND po.obj_promo_data >= CURDATE()) AND b.Visivel=1
				GROUP BY p.ID_Marca';
		}
		else if($tipo==3)
		{
			//pesquisa
			global $Str_pesquisar,$site_lang;
			$Str_pesquisar=strtolower($Str_pesquisar);
			$and.=' lower(pol.Cabecalho) like "%'.$Str_pesquisar.'%" ';
			$and.=' OR lower(pl.Cabecalho) like "%'.$Str_pesquisar.'%" ';
			$sql='SELECT pf.marca_id
			FROM products p
			INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
			INNER JOIN filters_products pf ON pf.prod_id=p.ID
			INNER JOIN products_objs po ON po.obj_type_id=p.ID
			INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE ( '.$and.' ) AND pf.marca_id <> 0 AND pf.filtro_active=1 AND p.Aproved=1 AND pl.Lang="'.$site_lang.'" AND pol.Lang="'.$site_lang.'" AND b.Visivel=1 group by pf.marca_id';
		}
		else
		{
			$fams = BuscarArvoreFams_F_FrontEnd($fam_id);

			$sql='SELECT p.ID_Marca, bl.Nome
				FROM products p
				INNER JOIN products_families_obj pfo on pfo.ID_PROD=p.ID
				INNER JOIN brands b ON b.ID=p.ID_Marca
				INNER JOIN brands_lang bl ON bl.ID_PAI = b.ID AND bl.Lang = "'.$GLOBALS['site_lang'].'"
				WHERE (pfo.ID_ORG in ('.implode(",",$fams).')) AND b.Visivel=1
				GROUP BY p.ID_Marca';
		}
		$marcas->db_open($db_link,$DB_name,$sql);
		if(!$marcas->EOF())
		{
			while(!$marcas->EOF())
			{
				$id_m=$marcas->db_data["ID_Marca"];
				$nome_m=$marcas->db_data["Nome"];
				$arr[]=array($id_m, $nome_m, $nivel);
				$marcas->db_next();
			}
		}
		$marcas->db_close();
		$arr=sort_arr_of_obj($arr,"1","asc");
		$return='';
		$total=count($arr);

		//---------------------------------------------------
		$return .= '<div class="filtro-stock drop">';
		$return .= '<h3>Stock</h3>';
		$return .= '<ul id="s_l">';
		$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="com_stock"><div id="f_left"><span>Com Stock</span></div><div id="f_right"><input id="com_stock" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
		//$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="por_encomenda"><div id="f_left"><span>Por encomenda</span></div><div id="f_right"><input id="por_encomenda" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
		$return .= '<li><h3 style="margin:0"><label class="label_check_p" for="sem_stock"><div id="f_left"><span>Sem Stock</span></div><div id="f_right"><input id="sem_stock" value="1" type="checkbox"></div><div class="c"></div></label></h3></li>';
		$return.='</ul><div class="c"></div></div>';
		//---------------------------------------------------

		if($total > 0 && $conf_on_off['brand_filter'])
		{
			$f=$total;
			$return .= '<div class="filtro-marcas drop">';
			$return .= '<h3>'.getLinguas('key_fabricante', $GLOBALS['site_lang']).'</h3>';
			$return .= '<ul id="m_l">';
			$f2=$f;
			for($a=0; $a<$f2; $a++)
			{
				$id_m=$arr[$a][0];
				$titulo=$arr[$a][1];
				$nivel=$arr[$a][2];

				 $class='class="active"';
				if(isset($_GET['cat']) && $_GET['cat'] == $catalogo_id) $class='class="active2"';

				$return.='<li '.$class.'><h3 style="margin:0"><label class="label_check_p" for="marca_'.$id_m.'"><div id="f_left"><span>'.$titulo.'</span></div> <div id="f_right"><input id="marca_'.$id_m.'" value="'.$id_m.'" type="checkbox"></div><div class="c"></div></label></h3></li>';
			}
			$return.='</ul><div class="c"></div></div>';
		}
		return $return;
	}
	function roundTo($n,$t)
	{
		return round($n/$t,0)*$t;
	}
	function FILTRO_PRECOS_NEW($precos = array(), $prod_id = 0, $lingua = '', $site_country= '')
	{
		if (isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/'))
		{
			if ($lingua == '')$lingua = $GLOBALS['admin_lang'];
			if ($site_country == '') $lingua = $GLOBALS['pais_site'];
		}
		else
		{
			if ($lingua == '')$lingua = $GLOBALS['site_lang'];
			if ($site_country == '') $lingua = $GLOBALS['ss_site_country'];
		}
	    $precos = array_unique($precos);

	    global $txt_preco, $site_lang;
	    $return = '';
	    $titulo = '';

	    if (!empty($precos))
	    {
	        sort($precos);
	        $min = 0;
	        $max = $precos[0];

	        if (count($precos) > 1)
	        {
	            $min = $precos[0];
	            $max = $precos[count($precos) - 1];
	        }


	        if(trim($min)<=0)$min='0';
	        


	        if ($max > 0 && $min != $max)
	        {

	            $return .= '<div class="filtro-preco">';
		            $return .= '<div class="filtro_titulo filtro_titulo_preco">';
		            	$return .='<span>'.getLinguas('key_preco', $GLOBALS['site_lang']).'</span>';
		            $return .='</div>';
		            $return .= '<div class="intervalos-text">';
		            	$return .='<div class="intervalo_titulo">'.getLinguas('key_intervalo', $GLOBALS['site_lang']).'</div>';
			            $return .= '<div id="slider-snap-value-lower-text">'.str_replace(' ','',numberformat($min, 2)).'</div>';
			            $return .= '<div>-</div>';
			            $return .= '<div id="slider-snap-value-upper-text">'.str_replace(' ','',numberformat($max, 2)).'</div>';
		            $return .= '</div>';
		            $return .= '<div class="clear"></div>';
	            	$return .= '<div id="range" min="'.$min.'" max="'.$max.'"></div>';
	            	//$return .= '<div id="range" min="'.number_format($min, 2, '.', '').'" max="'.number_format($max, 2,'.', '').'"></div>';
		            $return .= '<div class="intervalos">';
			            $return .= '<div id="slider-snap-value-lower"></div>';
			            $return .= '<div id="slider-snap-value-upper"></div>';
		            $return .= '</div>';

	            	$return .= '<div class="clear"></div>';
            	$return .= '</div>';

	        }
	    }
	    return $return;
	}
	function getniv($fam_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$obj->db_open($db_link,$DB_name,'SELECT ID_ORG FROM products_families WHERE ID="'.$fam_id.'"');
		if(!$obj->EOF())
		{
			$id_org=$obj->db_data['ID_ORG'];
			$obj->db_close();
			if($id_org > 0) return '2';
			else  return '1';
		}
		$obj->db_close();
	}
	function get_filtros_rep2($cat_id,$valor_id,$fam_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		$familia->db_open($db_link,$DB_name,'SELECT filtros_rel2 FROM filters_products WHERE (fam_id1='.$fam_id.' OR fam_id2='.$fam_id.' OR fam_id3='.$fam_id.' OR fam_id4='.$fam_id.') AND cat_id='.$cat_id.' AND valor_id='.$valor_id);
		$flt='';
		while(!$familia->EOF())
		{
			$flt.=$familia->db_data['filtros_rel2'];
			$familia->db_next();
		}
		$familia->db_close();
		return $flt;
	}
	function aasort(&$array,$key)
	{
		$sorter=array();
		$ret=array();
		if(is_array($array))
		{
			reset($array);
			foreach ($array as $i=> $va)$sorter[$i]=$va[$key];
		}
		asort($sorter);
		foreach ($sorter as $i=> $va)$ret[$i]=$array[$i];
		$array=$ret;
	}
	function FILTRO_CARACTERISTICAS_NEW_PESQUISA($fam_id=0,$tipo=0,$fam_id2=0,$nivelforced=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;

		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		if($fam_id)$nivel=getniv($fam_id);
		elseif($_GET['cat']) $nivel=getniv($fam_id);
		elseif($_GET['tipo_catalogo']) $nivel=2;
		elseif($_GET['filtro_marcas']) $nivel=2;
		elseif($_GET['filtro_pesquisa']) $nivel=2;
		elseif($nivelforced>0) $nivel=$nivelforced;

		global $Str_pesquisar,$site_lang;
		$and.=' pol.Cabecalho like "%'.$Str_pesquisar.'%" ';
		$and.=' OR pl.Cabecalho like "%'.$Str_pesquisar.'%" ';
		$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order
		FROM products p
		INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
		INNER JOIN filters_products fp ON fp.prod_id=p.ID
		INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
		INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
		INNER JOIN products_objs po ON po.obj_type_id=p.ID
		INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id
		INNER JOIN brands b ON b.ID=p.ID_Marca
		WHERE ( '.$and.' ) AND fp.filtro_active=1 AND p.Aproved=1 AND pl.Lang="'.$site_lang.'" AND pol.Lang="'.$site_lang.'"  AND b.Visivel=1
		GROUP BY fp.fam_id';
		$filtros='';
		$familia->db_open($db_link,$DB_name,$sql);
		if(!$familia->EOF())
		{
			$return='<div class="filtro" style="height:20px;padding:0"><div class="filtro_titulo " ><span>'.$GLOBALS['txt_f_filterby'][$GLOBALS['gera_lingua']].' '.$GLOBALS['txt_caracteristicas'][$GLOBALS['gera_lingua']].'</span></div><div class="c"></div></div>';
			while(!$familia->EOF())
			{
				$filtros.= FILTRO_CARACTERISTICAS_NEW($familia->db_data['fam_id'],3);

				$familia->db_next();
			}
		}
		$familia->db_close();

		return $return.$filtros;
	}
	function FILTRO_CARACTERISTICAS_NEW($fam_id=0,$tipo=0,$NOT_ANYMORE=0,$nivelforced=0,$lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$filtro_marcas_id;
		$and='';
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		$rs=new DataObj;
		$rs2=new DataObj;

		/*if($fam_id)$nivel=getniv($fam_id);
		elseif(isset($_GET['cat'])) $nivel=getniv($fam_id);
		elseif(isset($_GET['tipo_catalogo'])) $nivel=2;
		elseif(isset($_GET['filtro_marcas'])) $nivel=2;
		elseif(isset($_GET['filtro_pesquisa'])) $nivel=2;
		elseif(isset($_GET['ALLPRODUCTS'])) $nivel=2;
		elseif($nivelforced > 0) $nivel=$nivelforced;*/

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		//------------------------------------
		$fam_ids=$fam_id;
		if($fam_id>0)
		{
			$sql="SELECT ID FROM products_families WHERE ID_ORG='".$fam_id."'";
			$rs->db_open($db_link,$DB_name,$sql);
			if(!$rs->EOF())
			{
				while(!$rs->EOF())
				{
					$fam_ids.=",".$rs->db_data['ID'];
					$sql="SELECT ID FROM products_families WHERE ID_ORG='".$rs->db_data['ID']."'";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						while(!$rs2->EOF())
						{
							$fam_ids.=",".$rs2->db_data['ID'];
							$rs2->db_next();
						}
					}
					$rs2->db_close();
					$rs->db_next();
				}
			}
			$rs->db_close();
		}



		if($fam_id > 0)
		{
			//$sql='SELECT * FROM acelerador_pesquisa WHERE fam_id1 IN ('.$fam_ids.') OR fam_id2 IN ('.$fam_ids.') OR fam_id3 IN ('.$fam_ids.') OR fam_id4 IN ('.$fam_ids.')';

			$sql='SELECT  DISTINCT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext,fcl.category_name,fcl.category_name2,fvl.values_name2,fvl.values_name,fc.category_type
			FROM filters_products fp
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_categories_lang fcl ON fcl.category_id = fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN filters_values_lang fvl on fvl.values_id = fp.valor_id
			WHERE (fp.fam_id1 IN ('.$fam_ids.') OR fp.fam_id2 IN ('.$fam_ids.') OR fp.fam_id3 IN ('.$fam_ids.') OR fp.fam_id4 IN ('.$fam_ids.'))
				AND fp.filtros_rel2<>""
				AND fp.filtro_active=1
				AND fc.category_active=1
				AND fcl.category_lang = "'.$lingua.'"
				AND fvl.values_lang="'.$lingua.'"
				AND fp.filtro_area<>1 AND fp.filtro_area<>3';
		}
		elseif($tipo == 5)
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
			FROM filters_products fp
			INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
			INNER JOIN products p ON p.ID=fp.prod_id
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE po.obj_destaque=1 AND fp.filtro_active=1 AND b.Visivel=1 AND fv.value_active=1 AND fc.category_active=1';
			$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
			$sql .= ' GROUP BY fp.valor_id';
		}
		elseif($tipo == 'outlet' && !is_int($tipo))
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
			FROM filters_products fp
			INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
			INNER JOIN products p ON p.ID=fp.prod_id
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE po.obj_outlet=1 AND fp.filtro_active=1 AND b.Visivel=1 AND fv.value_active=1 AND fc.category_active=1';
			$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
			$sql .= ' GROUP BY fp.valor_id';
		}
		elseif($tipo == 4)
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
			FROM filters_products fp
			INNER JOIN products_objs po on po.obj_type_id=fp.prod_id
			INNER JOIN products p ON p.ID=fp.prod_id
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE po.obj_promo_ativo=1 AND fp.filtro_active=1 AND b.Visivel=1 AND fv.value_active=1 AND fc.category_active=1';
			$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
			$sql .= ' GROUP BY fp.valor_id';
		}
		elseif($tipo == 3)
		{
			global $Str_pesquisar,$site_lang;
			$and.=' pol.Cabecalho like "%'.$Str_pesquisar.'%" ';
			$and.=' OR pl.Cabecalho like "%'.$Str_pesquisar.'%" ';
			$sql='
					SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
					FROM products p
					INNER JOIN products_lang pl ON pl.ID_PAI=p.ID
					INNER JOIN filters_products fp ON fp.prod_id=p.ID
					INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
					INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
					INNER JOIN products_objs po ON po.obj_type_id=p.ID
					INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id
					INNER JOIN brands b ON b.ID=p.ID_Marca
					WHERE ( '.$and.' ) AND (fp.fam_id IN ('.$fam_ids.')) AND fp.filtro_active=1 AND p.Aproved=1';

					$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
					$sql .= ' AND pl.Lang="'.$lingua.'" AND pol.Lang="'.$lingua.'"';

			$sql .= 'AND b.Visivel=1 AND fv.value_active=1 AND fc.category_active=1
			GROUP BY fp.valor_id';
		}
		elseif($tipo == 1)
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
			FROM filters_products fp
			INNER JOIN products p ON p.ID=fp.prod_id
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE marca_id='.$fam_id.' AND fp.filtro_active=1 AND b.Visivel=1 AND fv.value_active=1 AND fc.category_active=1';
			$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
			$sql .= ' GROUP BY fp.valor_id';
		}
		elseif($tipo == 'all' && !is_int($tipo))
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext
			FROM filters_products fp
			INNER JOIN products p ON p.ID=fp.prod_id
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN brands b ON b.ID=p.ID_Marca
			WHERE (fp.fam_id >0) AND fp.filtros_rel2<>"" AND fp.filtro_active=1 AND b.Visivel=1';
			$sql .= ' AND fp.filtro_area<>1 AND fp.filtro_area<>3';
		}
		else
		{
			$sql='SELECT fp.cat_id,fp.valor_id,fp.filtros_rel2,fp.marca_id,fp.fam_id1,fc.category_order,fv.value_order,fv.value_ext,fcl.category_name,fcl.category_name2,fvl.values_name2,fvl.values_name,fc.category_type
			FROM filters_products fp
			INNER JOIN filters_categories fc ON fc.category_id=fp.cat_id
			INNER JOIN filters_categories_lang fcl ON fcl.category_id = fp.cat_id
			INNER JOIN filters_values fv ON fv.value_id=fp.valor_id
			INNER JOIN filters_values_lang fvl on fvl.values_id = fp.valor_id
			WHERE (fp.fam_id1 IN ('.$fam_ids.') OR fp.fam_id2 IN ('.$fam_ids.') OR fp.fam_id3 IN ('.$fam_ids.') OR fp.fam_id4 IN ('.$fam_ids.'))
				AND fp.filtros_rel2<>""
				AND fp.filtro_active=1
				AND fc.category_active=1
				AND fcl.category_lang = "'.$lingua.'"
				AND fvl.values_lang="'.$lingua.'"
				AND fp.filtro_area<>1 AND fp.filtro_area<>3';

		}

		if($_GET['log']==5)echo $sql;

		$familia->db_open($db_link,$DB_name,$sql);
		$contador=0;
		if(!$familia->EOF())
		{
			$old_cat_id=array('');
			$valor_id_old=array('');
			$valor_id_old2=array('');
			$c=0;
			while(!$familia->EOF())
			{
				$ordem=$familia->db_data['category_order'];
				$ordem_val=$familia->db_data['value_order'];
				$value_ext=''; $value_ext=$familia->db_data['value_ext'];
				$cat_id=$familia->db_data['cat_id'];
				$cat_nome=($familia->db_data["category_name2"] != '' ? $familia->db_data["category_name2"]:$familia->db_data["category_name"]);
				$category_type=$familia->db_data["category_type"];
				if(!in_array($cat_id, $old_cat_id))
				{
					if($cat_nome) $arr_categorias_x[]=array($cat_id, $cat_nome, $ordem, $category_type);
					$old_cat_id[]=$cat_id;
				}
				$filtros_rel2='';
				$valor_id=$familia->db_data['valor_id'];
				$valor_nome=($familia->db_data["values_name2"] != '' ? $familia->db_data["values_name2"]:$familia->db_data["values_name"]);
				$filtros_rel=$familia->db_data['filtros_rel2'];
				if($valor_nome && $cat_nome)
				{
					if($fam_id)
					{
						if( !in_array($valor_id,$valor_id_old[$cat_id]))
						{
							$fl=explode(";",get_filtros_rep2($cat_id,$valor_id,$fam_id));
							$fl=array_unique($fl);
							$fl=implode(";",$fl);

//							"53_1102;121_1100;10_216;"
//var_dump($fl);							
							$arr_valores_x[$cat_id][]=array($valor_id, $valor_nome, $fl, $ordem_val, $value_ext);
						}
						$valor_id_old[$cat_id][]=$valor_id;
					}
					else
					{
						if( !in_array($valor_id,$valor_id_old[$cat_id]))
						{
							$fl=explode(";",get_filtros_rep2($cat_id,$valor_id,$fam_id));
							$fl=array_unique($fl);
							$fl=implode(";",$fl);

//var_dump($fl);
							$arr_valores_x[$cat_id][]=array($valor_id, $valor_nome, $fl, $ordem_val, $value_ext);
						}
						$valor_id_old[$cat_id][]=$valor_id;
					}
				}
				$familia->db_next();
			}
		}
		$familia->db_close();

		/*$familia->db_open($db_link,$DB_name,$sql);
		$i=0;
		while(!$familia->EOF())
		{
			$ordem=0;
			$category_type=0;
			$ordem_val=0;
			$value_ext='';//caso tenha um ficheiro num filtro
			$fl='';

			$fl=$filtros_rel=$combo=$familia->db_data["Filtros"];
			$combos_separados=explode(";",$combo);


			foreach($combos_separados as $values)
			{
				$componentes=explode("_",$values);
				$cat_id=$componentes[0];
				$valor_id=$componentes[1];

				$cat_nome   =buscacategoriaTipoeNome($cat_id);
				$valor_nome =buscavalorfiltros($valor_id);
			
				$arr_categorias_x[]=array($cat_id, $cat_nome, $ordem, $category_type);
				$arr_valores_x[$cat_id][]=array($valor_id, $valor_nome, $fl, $ordem_val, $value_ext);
			}

			$familia->db_next();
		}
		$familia->db_close();*/






		//----------------------------------------
		if($_GET['log']==5)
		{
			var_dump($arr_categorias_x);
		}



		aasort($arr_categorias_x, 1);
		foreach($arr_categorias_x as $key=> $new_array){$arr_categorias[]=array($new_array[0],$new_array[1],$new_array[3]);}
		$return='';	$total=0;
		if(isset($arr_categorias)) $total=count($arr_categorias);
		if($total > 0)
		{

			$return .= '<div class="c"></div>';
			$return .= '<div id="muda_c" class="filtro"><div class="c"></div>';
			$return .= '<div class="filtro-caracteristicas drop2">';
			$return .= '<h3>'.getLinguas('key_caracteristicas', $GLOBALS['site_lang']).'</h3>';
			$return .= '<ul id="c_l">';

			for($a=0; $a < $total; $a++)
			{
				$cat_id=$arr_categorias[$a][0];
				$cat_nome=$arr_categorias[$a][1];
				$cat_type=$arr_categorias[$a][2];

				if($cat_type == 0)
				{
					$return .= '<li class="f_ativos3" id="f'.$cat_id.'" style="display:none"><h3 style="margin:0"><a href="#" class="parent" onclick="return false">';

					if(count($arr_valores_x[$cat_id]) > 0) $return .= '<span id="f_ativos3">'.$cat_nome.'</span>';
					$return .= '</a></h3><ul>';
					aasort($arr_valores_x[$cat_id],1);
					$tipo_input=0; $range_min=0; $range_max=0;
					$arr_val=array();

					$style="none";

					foreach($arr_valores_x[$cat_id] as $key=> $new_array)
					{
						$arr_valores[$cat_id][]=array($new_array[0], $new_array[1], $new_array[2], $new_array[4]);
					}
					$f=count($arr_valores[$cat_id]);
					$f2=$f;
					for($b=0; $b < $f2; $b++)
					{
						$valor_id=$arr_valores[$cat_id][$b][0];
						$valor_nome=$arr_valores[$cat_id][$b][1];
						$arr_val[]=$valor_nome;
						$filtros_rel=$arr_valores[$cat_id][$b][2];
						$img_ext=''; $img_ext=$arr_valores[$cat_id][$b][3];

						if($img_ext && is_file($GLOBALS['site_shell_url'].'files/filters/'.$valor_id.'_1.'.$img_ext)) $return .= '<li class="filtros-sel" style="display:'.$style.'"><div id="rel_category_'.$cat_id.'_'.$valor_id.'" class="t" title="'.$valor_nome.'"><label class="label_check_p" for="category_'.$cat_id.'_'.$valor_id.'"><div id="f_left"><span><img src="'.$GLOBALS['site_http_url'].'files/filters/'.$valor_id.'_1.'.$img_ext.'" alt="" width="30" border="0" /></span></div> <div id="f_right"><input valor="'.$valor_nome.'" id="category_'.$cat_id.'_'.$valor_id.'" value="'.$cat_id.'_'.$valor_id.'" name="'.$cat_id.'" data="'.$filtros_rel.'" type="checkbox"></div><div class="c"></div></label></div></li>';
						else $return .= '<li class="filtros-sel" style="display:'.$style.'"><div id="rel_category_'.$cat_id.'_'.$valor_id.'" class="t" title="'.$valor_nome.'"><label class="label_check_p" for="category_'.$cat_id.'_'.$valor_id.'"><div id="f_left"><span>'.$valor_nome.'</span></div> <div id="f_right"><input valor="'.$valor_nome.'" id="category_'.$cat_id.'_'.$valor_id.'" value="'.$cat_id.'_'.$valor_id.'" name="'.$cat_id.'"  data="'.$filtros_rel.'" type="checkbox"></div><div class="c"></div></label></div></li>';
					}
					sort($arr_val);
					$return.='<div class="c"></div></ul></li>';

				}
				else if($cat_type==2)
				{
					$return .= '<li class="f_ativos3"><h3><a href="#" class="parent" onclick="return false;">';
					if(count($arr_valores_x[$cat_id]) > 0)$return .= '<span id="f_ativos3">'.$cat_nome.'</span>';
					$return .= '</a></h3><ul>';
					aasort($arr_valores_x[$cat_id],3);
					$range_min=0;
					$range_max=0;
					$arr_val=array();
					$style="none";
					foreach($arr_valores_x[$cat_id] as $key=> $new_array)
					{
						$arr_valores[$cat_id][]=array($new_array[0],$new_array[1],$new_array[2], $new_array[4]);
					}
					$f=count($arr_valores[$cat_id]);
					$cores=new DataObj;



					for($b=0; $b < $f; $b++)
					{
						$valor_id=$arr_valores[$cat_id][$b][0];
						$valor_nome=$arr_valores[$cat_id][$b][1];
						$arr_val[]=$valor_nome;
						$filtros_rel=$arr_valores[$cat_id][$b][2];
						$img_ext = $arr_valores[$cat_id][$b][3];
						$sql6='SELECT fv.values_colorhex FROM filters_values_lang fv WHERE fv.values_id='.$valor_id;
						$cores->db_open($db_link,$DB_name,$sql6);
						$hex='';
						if(!$cores->EOF()) $hex=$cores->db_data['values_colorhex'];
						$cores->db_close();
						if($hex != '')
						{
							$return .= '<li class="filtros-sel"><div id="rel_category_'.$cat_id.'_'.$valor_id.'" style="padding:0px;" title="'.$valor_nome.'"><LABEL class="color_check round" style="background-color:#'.ltrim($hex, '#').'"  for="category_'.$cat_id.'_'.$valor_id.'" ><div style="width:23px;height:23px"></div><input valor="'.$valor_nome.'" id="category_'.$cat_id.'_'.$valor_id.'" value="'.$cat_id.'_'.$valor_id.'" data="'.$filtros_rel.'" type="checkbox"></LABEL></div></li>';
						}
					}
					$return.='<div class="c"></div></ul></li>';
				}
			}
			$return .= '<div class="c"></div></div></div>';
		}

		return $return;
	}
	function getPrecosblocos($fam_id=0, $tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$rs2=new DataObj;


		$and=$fam_id;


		$sql="SELECT ID FROM products_families WHERE ID_ORG='".$fam_id."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$and.=",".$rs->db_data['ID'];
				// LEVEL 2
				$sql="SELECT ID FROM products_families WHERE ID_ORG='".$rs->db_data['ID']."'";
				$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					while(!$rs2->EOF())
					{
						$and.=",".$rs2->db_data['ID'];
						$rs2->db_next();
					}
				}
				$rs2->db_close();
				// end of LEVEL 2
				$rs->db_next();
			}
		}
		$rs->db_close();



		$sql='SELECT ID_PROD FROM products_families_obj WHERE ID_ORG in ('.$and.')';
		$rs->db_open($db_link,$DB_name,$sql);

		$return='';
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$ID=$rs->db_data['ID_PROD'];

				if($ID)
				{
					$ar_precos=get_produtc_price($ID);
					$precopromo=$ar_precos['PVP'];
					$precos_bloco[]=$precopromo;
				}
				$rs->db_next();
			}
			return $precos_bloco;
		}
		$rs->db_close();
	}
	/*function getPrecosblocos($fam_id=0, $tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql='SELECT fp.prod_id FROM filters_products fp INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE (fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.') AND p.Aproved=1 AND fp.filtro_active=1 AND b.Visivel=1 GROUP BY fp.prod_id';
		$rs->db_open($db_link,$DB_name,$sql);

		$return='';
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$ID=$rs->db_data['prod_id'];

				if($ID)
				{
					$ar_precos=get_produtc_price($ID);
					$precopromo=$ar_precos['PVP'];
					$precos_bloco[]=$precopromo;
				}
				$rs->db_next();
			}
			return $precos_bloco;
		}
		$rs->db_close();
	}*/
	function BuscarProdutosFiltros_NOVO($fam_id=0,$tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;

		if(!$tipo)
		{
			$sql='SELECT fp.* FROM filters_products fp INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE(fp.fam_id1='.$fam_id.' OR fp.fam_id2='.$fam_id.' OR fp.fam_id3='.$fam_id.' OR fp.fam_id4='.$fam_id.') AND fp.filtro_active=1 AND b.Visivel=1 GROUP BY fp.prod_id';
		}
		else
		{
			if($tipo==2)	$sql='SELECT fp.* FROM filters_products fp INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE fp.filtro_active=1 AND b.Visivel=1 Group by fp.prod_id';
			elseif($tipo==1)$sql='SELECT fp.* FROM filters_products fp INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE fp.marca_id='.$fam_id.' AND fp.filtro_active=1 AND b.Visivel=1 GROUP BY fp.prod_id';
			elseif($tipo==3)//pesquisa
			{
				global $Str_pesquisar,$site_lang;
				$sql='SELECT pf.* FROM products p INNER JOIN products_lang pl ON pl.ID_PAI=p.ID INNER JOIN filters_products pf ON pf.prod_id=p.ID INNER JOIN products_objs po ON po.obj_type_id=p.ID INNER JOIN products_objs_lang pol ON pol.ID_PAI=po.obj_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE ( pol.Cabecalho like "%'.$Str_pesquisar.'%" OR pl.Cabecalho like "%'.$Str_pesquisar.'%" ) AND pf.filtro_active=1 AND p.Aproved=1 AND pl.Lang="'.$site_lang.'" AND pol.Lang="'.$site_lang.'" AND b.Visivel=1 GROUP BY pf.prod_id';
			}
			elseif($tipo==4)$sql='SELECT fp.* FROM filters_products fp INNER JOIN products_objs po on po.obj_type_id=fp.prod_id INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE fp.filtro_active=1 AND po.obj_promo_ativo=1 AND b.Visivel=1 GROUP BY  fp.prod_id';
			elseif($tipo=='outlet' && !is_int($tipo))$sql='SELECT fp.* FROM filters_products fp INNER JOIN products_objs po on po.obj_type_id=fp.prod_id INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE fp.filtro_active=1 AND po.obj_outlet=1 AND b.Visivel=1 GROUP BY  fp.prod_id';
			elseif($tipo==5)$sql='SELECT fp.* FROM filters_products fp INNER JOIN products_objs po on po.obj_type_id=fp.prod_id INNER JOIN products p ON p.ID=fp.prod_id INNER JOIN brands b ON b.ID=p.ID_Marca WHERE fp.filtro_active=1 AND po.obj_destaque=1 AND b.Visivel=1 GROUP BY fp.prod_id';
		}
		if(isset($sql)) $obj->db_open($db_link,$DB_name,$sql);
		return $obj;
	}
	function buscavalorfiltros($ID, $lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		$sql="SELECT values_name, values_name2 from filters_values_lang where values_id='$ID' and values_lang='".$lingua."'";
		$dados->db_open($db_link,$DB_name,$sql);
		if(!$dados->EOF())
		{
			if($dados->db_data["values_name2"] != '')$res=$dados->db_data["values_name2"];
			else $res=$dados->db_data["values_name"];
		}
		$dados->db_close();
		return $res;
	}
	function buscacategoriafiltros($ID, $lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}
		$sql="SELECT category_name, category_name2 from filters_categories_lang where category_id='$ID' and category_lang='".$lingua."'";
		$dados->db_open($db_link,$DB_name,$sql);
		if(!$dados->EOF())
		{
			if($dados->db_data["category_name2"] != '')$res=$dados->db_data["category_name2"];
			else $res=$dados->db_data["category_name"];
		}
		$dados->db_close();
		return $res;
	}
	function buscacategoriaTipofiltros($ID)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;
		$sql="SELECT category_type from filters_categories where category_id='$ID'";
		$res='';
		$dados->db_open($db_link,$DB_name,$sql);
		if(!$dados->EOF())
		{
			$res=$dados->db_data["category_type"];
		}
		$dados->db_close();
		return $res;
	}
	function buscacategoriaTipoeNome($ID, $lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		$sql="
				SELECT
					filters_categories.category_type, filters_categories_lang.category_name, filters_categories_lang.category_name2
				FROM
					filters_categories INNER JOIN filters_categories_lang ON filters_categories_lang.category_id=filters_categories.category_id
				WHERE
					filters_categories.category_id='$ID'
					AND filters_categories_lang.category_lang='".$lingua."'
					";

		$dados->db_open($db_link,$DB_name,$sql);
		if(!$dados->EOF())
		{
			$category_type = $dados->db_data["category_type"];
			if($dados->db_data["category_name2"] != '')$category_name = $dados->db_data["category_name2"];
			else $category_name = $dados->db_data["category_name"];
		}
		$dados->db_close();
		return array('category_type' => $category_type, 'category_name' => $category_name);
	}
	function buscacabecalho_marca($ID=0, $lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$dados=new DataObj;
		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}
		$Nome='';
		$sql="SELECT Nome from brands_lang where ID_PAI='$ID' and Lang='".$lingua."'";
		$dados->db_open($db_link,$DB_name,$sql);
		if(!$dados->EOF())
		{
			$Nome = $dados->db_data["Nome"];
		}
		$dados->db_close();
		return $Nome;
	}
	function sort_arr_of_obj($array, $sortby, $direction='asc')
	{
		$sortedArr=array();
		$tmp_Array=array();
		foreach($array as $k=> $v) { $tmp_Array[]=strtolower($v[$sortby]);}
		if($direction=='asc') asort($tmp_Array);
		else arsort($tmp_Array);
		foreach($tmp_Array as $k=>$tmp){$sortedArr[]=$array[$k];}
		return $sortedArr;
	}
	function getNivelFiltros($fam_id=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$obj=new DataObj;
		$sql='SELECT
					fam_id
				FROM
					filters_products
				WHERE
					(
						fam_id='.$fam_id.'
					)

					AND filtro_active=1
					AND filtros_rel2 <> ""
				LIMIT 1';
		$res='';
		$obj->db_open($db_link,$DB_name,$sql);
		if(!$obj->EOF())
		{
			if($obj->db_data['fam_id']==$fam_id) $res='1';
		}
		$obj->db_close();
		return $res;
	}
	function buscacabecalho_cf($ID, $lingua='')
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		$rs->db_open($db_link,$DB_name,'SELECT Cabecalho FROM products_families_lang WHERE ID_PAI="'.$ID.'" AND Lang="'.$lingua.'"');
		if(!$rs->EOF())$str=$rs->db_data["Cabecalho"];
		$rs->db_close();
		return $str;
	}
	function FamilyLevel($field='',$fam_id=0,$nivel=0)
	{
		global $ss_revend;
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;

		$sql='
				SELECT
					fam_id1
				FROM
					filters_products
				WHERE
					filtro_active=1
					AND filtros_rel2 <> ""
				GROUP BY
					fam_id1
				';

		$value=nivel_anterior2($_GET['cat']);
		if($value=='') $value=$_GET['cat'];

		$familia->db_open($db_link,$DB_name,$sql);
		if(!$familia->EOF())
		{
			$return.='<div class="filtro">

						<div class="filtro_titulo "><span>'.$GLOBALS['txt_rp_familias'][$GLOBALS['site_lang']].'</span></div>
						<ul id="f_l">
						';

			$return.='<select name="fam_id1" class="styled2" onchange="if(this.value!=\'\') window.open(this.value,\'_self\')">';
			$return.='<option value="">Escolha uma Familia</option>';
			while(!$familia->EOF())
			{
				$ID_FAMILIA=$familia->db_data['fam_id1'];
				$t=buscacabecalho_cf($ID_FAMILIA);
				$way=seo2(1,$GLOBALS['catalog_page_id'],'','',$ID_FAMILIA,$t);
				$return.='<option value="'.$way.'"';
				if($value==$ID_FAMILIA) $return.=' selected="selected" ';
				$return.='>'.$t.'</option>';
				$familia->db_next();
			}
			$return.='</select>';
			$return.='</ul></div>';
		}
		$familia->db_close();
		return $return;
	}
	function FamilyLevel2($nivel,$field='',$fam_id=0,$s=0)
	{
		global $ss_revend;
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		$idd=nivel_anterior2($_GET['cat']);
		if($idd=='') $idd=$_GET['cat'];
		$sql='SELECT '.$field.' FROM filters_products WHERE fam_id'.$nivel.'= "'.$idd.'" AND '.$field.' > 0 GROUP BY '.$field;
		$familia->db_open($db_link,$DB_name,$sql);
		if(!$familia->EOF())
		{
			$return.='<div class="filtro"><div class="filtro_titulo "><span>SUB-'.$GLOBALS['txt_rp_familias'][$GLOBALS['site_lang']].'</span></div>
			<ul id="f_l">
			';
			$value=$_GET['cat'];
			$pp=buscacabecalho_cf($idd);
			$return.='<select name="'.$field.'" class="styled2" onchange="if(this.value!=\'\') window.open(this.value,\'_self\')">';
			$way=seo2(1,$GLOBALS['catalog_page_id'],'','',$idd,$t);
			if($value!=$idd) $return.='<option value="'.$way.'">Voltar a '.$pp.'</option>';
			else $return.='<option value="'.$way.'">Escolha uma Sub-Familia</option>';
			while(!$familia->EOF())
			{
				$ID_FAMILIA=$familia->db_data[$field];
				$t=buscacabecalho_cf($ID_FAMILIA);
				$way=seo2(1,$GLOBALS['catalog_page_id'],'','',$ID_FAMILIA,$t);
				$return.='<option value="'.$way.'"';
				if($value==$ID_FAMILIA) $return.=' selected="selected" ';
				$return.='>'.$t.'</option>';
				$familia->db_next();
			}
			$return.='</select>';
			$return.='</ul></div>';
		}
		$familia->db_close();
		return $return;
	}
	function nivel_anterior2($nivel)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$familia=new DataObj;
		$sql='SELECT ID_ORG FROM products_families WHERE ID='.$_GET['cat'];
		$res='';
		$familia->db_open($db_link,$DB_name,$sql);
		if($familia->db_data['ID_ORG']) $res=$familia->db_data['ID_ORG'];
		$familia->db_close();

		return $res;
	}
	function tem_sub_familias($ID_ORG=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql='SELECT ID FROM products_families WHERE ID_ORG='.$ID_ORG;
		$rs->db_open($db_link,$DB_name,$sql);
		$r=0;
		if(!$rs->EOF()) $r=1;
		$rs->db_close();
		return $r;
	}
	// FUNÇOES DO IMPORTADOR ------------------------------------------------------------------------
	function filtros_atualiza($prod_id=0, $IDCategoria=0, $Categoria='', $IDValor=0, $Valor='', $brand=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql_ref='SELECT category_id FROM filters_categories WHERE category_id_import="'.$IDCategoria.'" AND brand="'.$brand.'"';

		$rs->db_open($db_link,$DB_name,$sql_ref);
		if(!$rs->EOF())
		{
			$category_id=$rs->db_data['category_id'];

			$sql='UPDATE filters_categories
			SET category_type=0,category_active=1,category_flag=1
			WHERE category_id_import="'.$IDCategoria.'" AND category_id="'.$category_id.'" AND brand="'.$brand.'"';
			$res=Runquery($db_link,$DB_name,$sql);

			$sql='UPDATE filters_categories_lang
			SET category_name="'.$Categoria.'",category_lang="pt"
			WHERE category_id_import="'.$IDCategoria.'" AND category_id="'.$category_id.'"';
			$res2=Runquery($db_link,$DB_name,$sql);

		}
		else
		{

			$sql='INSERT INTO filters_categories(category_type,category_active,category_flag,category_id_import,brand)
			VALUES ("0","1","1","'.$IDCategoria.'","'.$brand.'")';
			$res=Runquery($db_link,$DB_name,$sql);
			$category_id=db_get_last_insert_id($db_link);

			if($category_id > 0)
			{
				$sql='INSERT INTO filters_categories_lang(category_id,category_name,category_lang,category_id_import)
				VALUES ("'.$category_id.'","'.$Categoria.'","pt","'.$IDCategoria.'")';
				$res2=Runquery($db_link,$DB_name,$sql);

			}
		}
		if($res && $res2 && $category_id > 0)
		{
			$value_id=filtros_valores_atualiza($prod_id, $IDCategoria, $Categoria, $IDValor, $Valor, $category_id);
		}
		$rs->db_close();

		return array('category_id'=> $category_id, 'value_id'=> $value_id);
	}
	function filtros_valores_atualiza($prod_id=0, $IDCategoria=0, $Categoria='', $IDValor=0, $Valor='', $category_id=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql_ref='SELECT value_id,category_id FROM filters_values WHERE category_id_import="'.$IDCategoria.'" AND value_id_import="'.$IDValor.'"';

		$rs->db_open($db_link,$DB_name,$sql_ref);
		if(!$rs->EOF())
		{
			$value_id=$rs->db_data['value_id'];
			$category_id=$rs->db_data['category_id'];

			$sql='UPDATE filters_values
			SET value_order=0,value_active=1,value_flag=1
			WHERE category_id_import="'.$IDCategoria.'" AND value_id_import="'.$IDValor.'" AND category_id="'.$category_id .'"';
			$res=Runquery($db_link,$DB_name,$sql);

			$sql='UPDATE filters_values_lang
			SET values_name="'.$Valor.'", values_lang="pt"
			WHERE category_id_import="'.$IDCategoria.'" AND value_id_import="'.$IDValor.'"';
			$res=Runquery($db_link,$DB_name,$sql);
		}
		else
		{
			$sql='INSERT INTO filters_values(value_order,value_active,value_flag,category_id_import,value_id_import,category_id)
			VALUES("0","1","1","'.$IDCategoria.'","'.$IDValor.'","'.$category_id .'")';
			$res=Runquery($db_link,$DB_name,$sql);
			$value_id=db_get_last_insert_id($db_link);
			if($value_id > 0)
			{
				$sql='INSERT INTO filters_values_lang(values_id,values_name,values_lang,category_id_import,value_id_import)
				VALUES("'.$value_id.'","'.$Valor.'","pt","'.$IDCategoria.'","'.$IDValor.'")';
				$res=Runquery($db_link,$DB_name,$sql);
			}
		}
		$rs->db_close();
		return $value_id;
	}
	function getFiltroID($m_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql="SELECT value_id FROM filters_values WHERE value_id_import='$m_id'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())$filtro_id = $rs->db_data['value_id'];
		else $filtro_id = 0;
		$rs->db_close();
		return $filtro_id;
	}
	function getProdutosRelacionados_filtros($prod_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$prods=get_produtc_info($prod_id);
		$idfam=$prods['fam_id'];

		$sql='SELECT pfo.ID_Prod2 as idprod FROM products p INNER JOIN produtos_opcoes pfo ON p.ID=pfo.ID_Prod WHERE pfo.ID_Prod='.$prod_id.' AND p.Aproved=1 GROUP BY pfo.ID_Prod2';
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if($rs->EOF())
		{
			$sql='SELECT pfo.ID_Prod as idprod FROM products p INNER JOIN products_families_obj pfo ON p.ID=pfo.ID_Prod WHERE pfo.ID_ORG='.$idfam.' AND pfo.ID_Prod!='.$prod_id.' AND p.Aproved=1 GROUP BY pfo.ID_Prod ORDER BY RAND() LIMIT 10 ';
			$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		}
		if(!$rs->EOF())
		{
			$i=0;
			while(!$rs->EOF())
			{
				$prod_id=$rs->db_data['idprod'];
				$result[$i]=$prod_id;
				$rs->db_next();
				$i++;
			}
		}
		$rs->db_close();
		return $result;
	}

	function get_stock_legenda($prods = '')
	{
		if(!isset($_SESSION))session_start();
		if(isset($_SESSION['configs']))
		{
			$configs = $_SESSION['configs'];
		}
		else
		{
			if (function_exists('stock_plataforma'))
			{
				$_SESSION['configs']=$configs = stock_plataforma();
			}
			else
			{
				global $DB_host,$DB_user,$DB_pass, $DB_name, $site_lang;
			    $db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			    $rs=new DataObj;
			    $rs2='';
			    $sql ="SELECT  * FROM modulos_config_site";
			    $rs->db_open($db_link, $DB_name, $sql);
			    if(!$rs->EOF()) $rs2 = $rs->db_data;
			    $rs->db_close();
			    return $rs2;
			}
		}

		$stock_txt='';

		$precos = $prods['prod_precos'];

		if($configs['stock_verde_ativo'] == 1 && $precos['STOCK'] >= $configs['stock_verde_qtd']) //3
		{
			// var_dump('ola');
			$stock_txt='<span class="disponivel" title="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_verde'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
		}
		elseif($configs['stock_amarelo_ativo'] == 1 && $precos['STOCK'] == $configs['stock_amarelo_qtd']) //2
		{
			$stock_txt='<span class="limitado" title="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_amarelo'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
		}
		elseif($configs['stock_azul_ativo'] == 1 && $precos['STOCK'] == $configs['stock_azul_qtd']) //1
		{
			$stock_txt='<span class="porencomenda" title="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_azul'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
		}
		elseif($configs['stock_vermelho_ativo'] == 1 && $precos['STOCK'] == $configs['stock_vermelho_qtd']) //1
		{
			$stock_txt='<span class="esgotado" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
		}
		else
		{
			$stock_txt='<span class="esgotado" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'">'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'<i class="fa fa-circle" alt="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" title="'.getLinguas($configs['stock_vermelho'],$GLOBALS['site_lang']).'" aria-hidden="true"></i></span>';
		}

		return $stock_txt;
	}
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	/**************************** FILTROS *****************************/
	function gera_json_categorias($gera=0,$cat=0,$lingua='')
	{
		if($cat!=0)$id_org_gerador=$cat;
		else $id_org_gerador=isset($_GET['cat']) ? $_GET['cat']:(isset($_GET['c']) && $_GET['c'] > 0 ? $_GET['c']:$fam_id_cf); // FAMILIAS

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		$and=$id_org_gerador;
		// get all the child families
		$id_org_to_process=$id_org_gerador;
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/json_r_'.$GLOBALS['page_id'].'_'.$id_org_gerador.'_'.$lingua.'.js';

		$time_expire=time() - (60*15); // Expire Time (15 minutos)
		if( (is_file($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			//Definir o formato dos preços
			date_default_timezone_set("Europe/Lisbon");
			setlocale(LC_TIME,"Portuguese_Standard");
			setlocale(LC_ALL,"en_US.UTF-8;LC_NUMERIC=C");
			setlocale(LC_NUMERIC,'en_US');

			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$rs2=new DataObj;

			if($GLOBALS['page_id']==148)
			{
				$f_json='var products=[';

				//Vamos construir a lista de icons de familias
				$sql="SELECT ID,img_ext,Ordem FROM products_families WHERE ID_ORG='".$id_org_to_process."' AND visivel=1";
				$rs->db_open($db_link,DB_NAME,$sql);
				while(!$rs->EOF())
				{
					$titulo='';

					$fam_id = $rs->db_data['ID'];
					$img_ext = $rs->db_data['img_ext'];
					$ordem = $rs->db_data['Ordem'];

					$sql="SELECT Cabecalho FROM products_families_lang WHERE ID_PAI='".$fam_id."' AND Lang = '".$GLOBALS['site_lang']."'";
					$rs2->db_open($db_link,DB_NAME,$sql);
					if(!$rs2->EOF())$titulo=$rs2->db_data['Cabecalho'];
					$rs2->db_close();

					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);

					$img_file='';

					if($img_ext && stream_resolve_include_path($GLOBALS['site_shell_url'].'files/products_families/'.$fam_id.'_1.'.$img_ext))$img_file='files/products_families/'.$fam_id.'_1.'.$img_ext;
					else $img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';

					$link=seo('pages.php?page_id=148&cat='.$fam_id,$fam_id,1);
					//$link=seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$fam_id,$fam_id,1);

					if($j>0)$f_json.=',';

					$f_json.='{
					"a0":"1",
					"a1":"'.$fam_id.'",
					"a2":"'.$ordem.'",
					"a3":"",
					"a4":"",
					"a5":"",
					"a6":"",
					"a7":"",
					"a8":"",
					"a9":"'.$img_file.'",
					"a10":"'.$titulo.'",
					"a11":"",
					"a12":"'.$link.'",
					"a13":"",
					"a14":"",
					"a15":"",
					"a16":"",
					"a17":"",
					"a18":"",
					"a19":"",
					"a20":"",
					"a21":"",
					"a22":"",
					"a23":"",
					"a24":"",
					"a25":"",
					"a26":"",
					"a27":"",
					"a28":"",
					"a29":"",
					"a30":"0",
					"a31":"1",
					"a32":"",
					"a33":"",
					"a34":"1"
					}';

					$j++;


					$rs->db_next();
				}
				$rs->db_close();

				//**************** AGORA Vamos carregar os produtos desta familia:
				$sql='SELECT products_objs.*,Sync,Cabecalho,Ref,u_catetiq,u_coretiq,u_imagetiq,P.Variante,P.estado,P.bateria,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
				FROM products_objs
				INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
				INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
				INNER JOIN products P on P.ID=products_objs.obj_type_id
				WHERE FPO.ID_ORG = "'.$id_org_to_process.'" AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 group by products_objs.obj_type_id order by products_objs.obj_pvp DESC';

				//echo $sql;

				$rs->db_open($db_link,DB_NAME,$sql);
				while(!$rs->EOF())
				{
					$prod_id=$rs->db_data['obj_type_id'];
					$obj_pvp=$price2=$rs->db_data['obj_pvp'];
					$obj_stock=$price2=$rs->db_data['obj_stock'];
					$prod_ordem=$rs->db_data['Ordem2'];
					$marca=$rs->db_data['ID_Marca'];
					$Carrinho=$rs->db_data['Carrinho'];
					$obj_promo_ativo = $rs->db_data['obj_promo_ativo'];
					$precopromo=$obj_promo = $rs->db_data['obj_promo'];
					$obj_destaque = $rs->db_data['obj_destaque'];
					$obj_novo = $rs->db_data['obj_novo'];
					$obj_outlet = $rs->db_data['obj_outlet'];
					$prods['prod_id']=$rs->db_data['obj_type_id'];

					$Variante = $rs->db_data["Variante"];
					$estado = $rs->db_data["estado"];
					$bateria = $rs->db_data["bateria"];
					if($estado != '')$rs->db_data['Cabecalho'].=' GRAU: '.$estado;
					if($bateria != '' && $bateria != '0')$rs->db_data['Cabecalho'].=' BAT: '.$bateria.'%';

					$prods['prod_nome']=$rs->db_data['Cabecalho'];


					$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
					$ID_Sub_Familia=$rs->db_data['ID_Fam'];

					$aux_icons=$rs->db_data['store_icons'];
					$aux_icons = substr($aux_icons, 0, strlen($aux_icons)-1);
					$aux_icons = str_replace(';', ',', $aux_icons);


					//Agora vamos ler a margem do produto:
					if($GLOBALS['ignorar_margens']==1 && $Sync==1)
					{
						//Vamos ignorar as margens
					}
					else
					{
						if(!$descontos[$ID_Sub_Familia])
						{
							// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
							$margem_formula=get_product_families_Margem_formula($ID_Sub_Familia);
							$margem_formula_value=get_product_margin_formula_value($margem_formula,$obj_pvp);
							if($margem_formula_value!==null) $margem=$margem_formula_value;
							else $margem=get_product_families($ID_Sub_Familia);
							
							$descontos[$ID_Sub_Familia] = $margem;

						}
						else $margem = $descontos[$ID_Sub_Familia];

						// VAMOS COLOCAR MARGEM
						$pc=$obj_pvp;
						if(isset($GLOBALS['Margem_personalizada']) && $GLOBALS['Margem_personalizada']==1)
						{
							if($precopromo>0)$precopromo=get_margem_personalizada($precopromo,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
							if($obj_pvp>0)$obj_pvp=get_margem_personalizada($obj_pvp,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
						}
						if(!is_array($margem))
						{
							if($margem != 0 && $obj_pvp > 0) $obj_pvp=$obj_pvp + ($obj_pvp * ($margem / 100));
							if($margem != 0 && $precopromo > 0) $precopromo=$precopromo + ($precopromo * ($margem / 100));
						}

					}




					if($j>0)$f_json.=',';

					$prod_ref = addslashes($rs->db_data['Ref']);
					$Codigo_Desconto = $rs->db_data['Codigo_Desconto'];
					$titulo= $rs->db_data['Cabecalho'];					

					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);

					$REPARACOES=1;
					if($REPARACOES==1)
					{
						$img=get_reparacoes_img($prod_ref);
						if(!is_file($GLOBALS['site_shell_url'].'layout/imgs/'.$img) )$img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						else $img_file=$GLOBALS['site_http_url'].'layout/imgs/'.$img;
					}
					else
					{
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
					}



					$prods['u_catetiq']=$rs->db_data['u_catetiq'];
					$prods['u_coretiq']=$rs->db_data['u_coretiq'];
					if($rs->db_data['u_imagetiq'] != '' && is_file($GLOBALS['site_shell_url'].'files/products/EE/'.$rs->db_data['u_imagetiq'])) $prods['u_imagetiq']=SITE_HTTP_URL.'files/products/EE/'.$rs->db_data['u_imagetiq'];
					else $prods['u_imagetiq']='';


					$global_promos='[{}]';
					if($price2 != $obj_pvp && $obj_promo > 0 && $obj_promo_ativo == 1)
					{
						$global_promos='['.\show_global_promos($prod_id).']';
					}
					else $price2=0;

					$categories_prod='{}';

					$categories_prod = \getCategoriesProds($prod_id);
					$families_prod = \getFamiliesProds($families);

					$prods['prod_id_marca']=$marca;
					$prods['prod_marca']=get_produtc_brand($marca);


					$f_json.='{
						"a0":"1",
						"a1":"'.$prod_id.'",
						"a2":'.$prod_ordem.',
						"a3":"'.$prod_ref.'",
						"a4":"'.$obj_pvp.'",
						"a5":"'.$price2.'",
						"a6":"'.$obj_pvp.'",
						"a7":"'.$obj_stock.'",
						"a8":"",
						"a9":"'.$img_file.'",
						"a10":"'.$titulo.'",
						"a11":"",
						"a12":"'.$link.'",
						"a13":"'.$obj_promo_ativo.'",
						"a14":"",
						"a15":'.$global_promos.',
						"a16":"'.$obj_destaque.'",
						"a17":"'.$prods['prod_id_marca'].'",
						"a18":['.$categories_prod.'],
						"a19":['.$families_prod.'],
						"a20":"'.$prods["u_imagetiq"].'",
						"a21":"'.$prods["u_catetiq"].'",
						"a22":"'.$prods["u_coretiq"].'",
						"a23":"'.$Carrinho.'",
						"a24":"'.$obj_novo.'",
						"a25":"",
						"a26":"'.$obj_outlet.'",
						"a27":"",
						"a28":"",
						"a29":"",
						"a30":"0",
						"a31":"1",
						"a32":"'.$prods['prod_marca']['marca_nome'].'",
						"a33":"'.$aux_icons.'",
						"a34":""
						}';

						$j++;
					

					$rs->db_next();
				}
				$rs->db_close();

				//---------------------------------------------
				//---------------------------------------------
				//---------------------------------------------
				//---------------------------------------------




				$f_json.='];';

				// GRAVAR EM FICHEIRO, nao necessita porque no header ja esta a gravar
				$myFile=$f_original;
				$fh=fopen($myFile, 'w');
				fwrite($fh, $f_json);
				fclose($fh);
		
				return str_replace("\n","",$f_json);
			}


			$SEMINOVOS=0;
			$REPARACOES=0;
			$array_f=encontraFamiliasRecursiva_x($id_org_to_process);
			if(in_array(7,$array_f))$SEMINOVOS=1;
			if(in_array(85,$array_f))$REPARACOES=1;

			//Vai carregar todas as familias
			if($id_org_to_process>0)
			{
				$sql="SELECT ID FROM products_families WHERE ID_ORG='".$id_org_to_process."' order by Ordem ASC";
				$rs->db_open($db_link,DB_NAME,$sql);
				if(!$rs->EOF())
				{
					while(!$rs->EOF())
					{
						$and.=",".$rs->db_data['ID'];
						// LEVEL 2
						$sql="SELECT ID FROM products_families WHERE ID_ORG='".$rs->db_data['ID']."' order by Ordem ASC";
						$rs2->db_open($db_link,DB_NAME,$sql);
						if(!$rs2->EOF())
						{
							while(!$rs2->EOF())
							{
								$and.=",".$rs2->db_data['ID'];
								$rs2->db_next();
							}
						}
						$rs2->db_close();
						// end of LEVEL 2
						$rs->db_next();
					}
				}
				$rs->db_close();
			}


			if($SEMINOVOS==1)
			{
				/*$sql='SELECT products_objs.obj_type_id as obj_type_id,products.encomenda_sem_stock,products.margem_fam,products.Sync,obj_iva,obj_iva_txt,obj_pvp,obj_promo,obj_promo_ativo,obj_promo_data,obj_Supplier_ID,
					obj_stock,products.Ref as Cabecalho,products.udtime,obj_destaque,obj_val2,obj_destaque_data,obj_travelsize,obj_novo,obj_novo_data,obj_outlet,
					obj_outlet_data,obj_dia,obj_dia_data,estados_produto,grau,fornecedor_importacao,Promo_descricao,products_objs.Promo_data_fim as Promo_data_fim,products_objs.Promo_data_ini as Promo_data_ini
				FROM products_objs INNER JOIN products on products.ID=products_objs.obj_type_id
				WHERE obj_type="products" AND obj_type_id="'.$prod_id.'"';
				$sql .= ' AND (obj_val2=0 OR obj_val2 IS NULL)';*/

				//mostrar apenas os que tem stock:
				$sql='SELECT products_objs.*,
				UPPER(TRIM(SUBSTRING_INDEX(SUBSTRING_INDEX(P.Ref, "_", 1), ",", -1))) AS uniq_ref,
				IF(products_objs.obj_promo_ativo=1 AND products_objs.obj_promo>0, products_objs.obj_promo, products_objs.obj_pvp) AS uniq_price,
				UPPER(TRIM(COALESCE(P.estado, ""))) AS uniq_grade,
				COALESCE(NULLIF(P.bateria, ""), 0) AS uniq_battery,
				Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
				FROM products_objs
				INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
				INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
				INNER JOIN products P on P.ID=products_objs.obj_type_id
				WHERE FPO.ID_ORG IN ('.$and.') AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 AND products_objs.obj_stock>0  AND (obj_val2=0 OR obj_val2 IS NULL)
				GROUP BY uniq_ref, uniq_price, uniq_grade, uniq_battery
				ORDER BY P.bateria DESC,products_objs.obj_pvp DESC';


			}
			else
			{
				if($REPARACOES==1)
				{
					$sql='SELECT products_objs.*,Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
					FROM products_objs
					INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
					INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
					INNER JOIN products P on P.ID=products_objs.obj_type_id
					WHERE FPO.ID_ORG IN ('.$and.') AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 GROUP BY products_objs.obj_type_id';
				}
				else
				{
					//Para os outros artigos vamos mostrar apenas os que tiveram movimentacao de stock a menos de 6 meses
					/*$data_ant=date('Y-m-d H:i:s',strtotime('-6 months'));
					$sql='SELECT products_objs.*,Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
					FROM products_objs
					INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
					INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
					INNER JOIN products P on P.ID=products_objs.obj_type_id
					WHERE FPO.ID_ORG IN ('.$and.') AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 AND ( products_objs.obj_stock > 0 OR (products_objs.obj_stock = 0 AND products_objs.obj_stockdate > "'.$data_ant.'")) group by products_objs.obj_type_id';*/

					//Bruno pediu em 16/06/2024 para mostrar apenas os que tem stock
					//$data_ant=date('Y-m-d H:i:s',strtotime('-6 months'));
					$sql='SELECT products_objs.*,Sync,Cabecalho,Variante,estado,bateria,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam,obj_Supplier_ID,fornecedor_importacao
					FROM products_objs
					INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
					INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
					INNER JOIN products P on P.ID=products_objs.obj_type_id
					WHERE FPO.ID_ORG IN ('.$and.') AND PL.Lang="pt" AND P.Aproved=1 AND products_objs.obj_val2=0 AND ( products_objs.obj_stock > 0 ) group by products_objs.obj_type_id';


				}
			}
			$rs->db_open($db_link,DB_NAME,$sql);


			//if($gera==1)echo $sql;

			$descontos=array();

			if(!$rs->EOF())
			{
				$prods=array();
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$prod_id=$rs->db_data['obj_type_id'];
					$obj_pvp=$price2=$rs->db_data['obj_pvp'];
					$obj_stock=$price2=$rs->db_data['obj_stock'];
					$prod_ordem=$rs->db_data['Ordem2'];
					$marca=$rs->db_data['ID_Marca'];
					$Carrinho=$rs->db_data['Carrinho'];
					$obj_promo_ativo = $rs->db_data['obj_promo_ativo'];
					$precopromo=$obj_promo = $rs->db_data['obj_promo'];
					$obj_destaque = $rs->db_data['obj_destaque'];
					$obj_novo = $rs->db_data['obj_novo'];
					$obj_outlet = $rs->db_data['obj_outlet'];
					$Variante = $rs->db_data["Variante"];
					$estado = $rs->db_data["estado"];
					$bateria = $rs->db_data["bateria"];
					if($estado != '')$rs->db_data['Cabecalho'].=' GRAU: '.$estado;
					if($bateria != '' && $bateria != '0')$rs->db_data['Cabecalho'].=' BAT: '.$bateria.'%';
					$prods['prod_nome']=$rs->db_data['Cabecalho'];
					$prods['prod_id']=$rs->db_data['obj_type_id'];
					$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
					$ID_Sub_Familia=$rs->db_data['ID_Fam'];
					$aux_icons=$rs->db_data['store_icons'];
					$aux_icons = substr($aux_icons, 0, strlen($aux_icons)-1);
					$aux_icons = str_replace(';', ',', $aux_icons);
					//Agora vamos ler a margem do produto:

					if($GLOBALS['ignorar_margens']==1 && $Sync==1)
					{
						//Vamos ignorar as margens
					}
					else
					{
						if(!$descontos[$ID_Sub_Familia])
						{
							// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
							$margem_formula=get_product_families_Margem_formula($ID_Sub_Familia);
							$margem_formula_value=get_product_margin_formula_value($margem_formula,$obj_pvp);
							if($margem_formula_value!==null) $margem=$margem_formula_value;
							else $margem=get_product_families($ID_Sub_Familia);
							
							$descontos[$ID_Sub_Familia] = $margem;

						}
						else $margem = $descontos[$ID_Sub_Familia];

						// VAMOS COLOCAR MARGEM
						$pc=$obj_pvp;
						if(isset($GLOBALS['Margem_personalizada']) && $GLOBALS['Margem_personalizada']==1)
						{
							if($precopromo>0)$precopromo=get_margem_personalizada($precopromo,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
							if($obj_pvp>0)$obj_pvp=get_margem_personalizada($obj_pvp,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
						}
						if(!is_array($margem))
						{
							if($margem != 0 && $obj_pvp > 0) $obj_pvp=$obj_pvp + ($obj_pvp * ($margem / 100));
							if($margem != 0 && $precopromo > 0) $precopromo=$precopromo + ($precopromo * ($margem / 100));
						}

					}

					if($j>0)$f_json.=',';

					$prod_ref = addslashes($rs->db_data['Ref']);
					$Codigo_Desconto = $rs->db_data['Codigo_Desconto'];
					$titulo= $rs->db_data['Cabecalho'];					

					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);

					$titulo = str_replace('(SEMINOVO) ','',$titulo);
					$titulo = str_replace('GRAU: ','<br>GRAU: ',$titulo);
					$titulo = str_replace('BAT: ','<br>BAT: ',$titulo);

					if($REPARACOES==1)
					{
						$img=get_reparacoes_img($prod_ref);
						if(!is_file($GLOBALS['site_shell_url'].'layout/imgs/'.$img) )$img_file=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/no_photo.png';
						else $img_file=$GLOBALS['site_http_url'].'layout/imgs/'.$img;
					}
					else
					{
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
					}


					$prods['u_catetiq']=$rs->db_data['u_catetiq'];
					$prods['u_coretiq']=$rs->db_data['u_coretiq'];
					if($rs->db_data['u_imagetiq'] != '' && is_file($GLOBALS['site_shell_url'].'files/products/EE/'.$rs->db_data['u_imagetiq'])) $prods['u_imagetiq']=SITE_HTTP_URL.'files/products/EE/'.$rs->db_data['u_imagetiq'];
					else $prods['u_imagetiq']='';


					$global_promos='[{}]';
					if($price2 != $obj_pvp && $obj_promo > 0 && $obj_promo_ativo == 1)
					{
						$global_promos='['.\show_global_promos($prod_id).']';
					}
					else $price2=0;

					$categories_prod='{}';

					$categories_prod = \getCategoriesProds($prod_id);
					$families_prod = \getFamiliesProds($families);

					$prods['prod_id_marca']=$marca;
					$prods['prod_marca']=get_produtc_brand($marca);


					$f_json.='{
						"a0":"1",
						"a1":"'.$prod_id.'",
						"a2":'.$prod_ordem.',
						"a3":"'.$prod_ref.'",
						"a4":"'.$obj_pvp.'",
						"a5":"'.$price2.'",
						"a6":"'.$obj_pvp.'",
						"a7":"'.$obj_stock.'",
						"a8":"",
						"a9":"'.$img_file.'",
						"a10":"'.$titulo.'",
						"a11":"",
						"a12":"'.$link.'",
						"a13":"'.$obj_promo_ativo.'",
						"a14":"",
						"a15":'.$global_promos.',
						"a16":"'.$obj_destaque.'",
						"a17":"'.$prods['prod_id_marca'].'",
						"a18":['.$categories_prod.'],
						"a19":['.$families_prod.'],
						"a20":"'.$prods["u_imagetiq"].'",
						"a21":"'.$prods["u_catetiq"].'",
						"a22":"'.$prods["u_coretiq"].'",
						"a23":"'.$Carrinho.'",
						"a24":"'.$obj_novo.'",
						"a25":"",
						"a26":"'.$obj_outlet.'",
						"a27":"",
						"a28":"",
						"a29":"",
						"a30":"0",
						"a31":"1",
						"a32":"'.$prods['prod_marca']['marca_nome'].'",
						"a33":"'.$aux_icons.'",
						"a34":""
						}';

						$j++;
					

					$rs->db_next();
				}
				$f_json.='];';
			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();



			// GRAVAR EM FICHEIRO, nao necessita porque no header ja esta a gravar
			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);
		}
		return str_replace("\n","",$f_json);
	}
	function gera_json_destaques($gera=0)
	{
		//$gera=1;
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_destaque'.$GLOBALS['cli_id'].'.txt';

		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']==1)$gera=1;
		if(!isset($GLOBALS['tem_cron_filtros']))
		{
			$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
			if(is_file($f_original) && filectime($f_original) < $time_expire)$gera=1;
		}
		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json_destaque=(fread($fh, filesize($myFile)));
			fclose($fh);
			$f_json_destaque = mb_convert_encoding($f_json_destaque,'HTML-ENTITIES','UTF-8');			
			$f_json_destaque=json_decode( $f_json_destaque, true );

			if(!$f_json_destaque)$f_json_destaque=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_destaque), true );
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			$data_ant = date('Y-m-d 00:00:00');
			$data_ant = date('Y-m-d 00:00:00', strtotime($data_ant. ' -5 days'));
			
			$rs->db_open($db_link,DB_NAME,'SELECT obj_type_id
			FROM products_objs
			INNER JOIN products on products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products.Aproved=1 AND products.udtime > "'.$data_ant.'" AND products_families.visivel=1 and obj_stock > 0 AND products.img_ext !="" group by obj_type_id order by obj_destaque Desc, RAND() limit 0,15');

			if(!$rs->EOF())
			{
				$f_json_destaque='{"Produtos":[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$price2=0;
					$global_promos='[{}]';

					$prods=\get_produtc_info($m_id);

					 

					$new_prods = '';
					//verifica se o produto está fora das datas de promoção
					$new_prods = verifica_promo($m_id, $prods['prod_precos']);
					
					
					if(!empty($new_prods))
					{
						$prods = $new_prods;
					}
					// echo '<pre>'; echo $prods['store_icons'] ; echo '</pre>';

					if($prods['prod_precos']['PVP'] > 0)
					{
						$pvp=$prods['prod_precos']['PVP'];
						if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
						{
							$price2=$prods['prod_precos']['PRECO_ANTIGO'];
							$global_promos='['.\show_global_promos($m_id).']';
						}
					}
					
					
					
					$prod_id=$m_id;
					$families_prod='{}';
					$categories_prod='{}';

					if(!$pvp)$pvp=0;
	 				$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);

	 				$titulo= $prods['prod_nome'];
					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);


					$subtitulo= $prods['prod_descricao'];
					$subtitulo = mb_convert_encoding($subtitulo,'HTML-ENTITIES','UTF-8');
					$subtitulo = strip_tags($subtitulo,'<br>');
					$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
					$subtitulo = str_replace('"','\"',$subtitulo);
					
					
					
					// echo '<pre>store_icons: '.$prods['store_icons'].'</pre>';

					if($j>0)$f_json_destaque.=',';
					$f_json_destaque.='{
						"id":"'.$m_id.'",
						"ord":'.$prods['prod_ordem'].',
						"ref":"'.$prods['prod_ref'].'",
						"price":'.$pvp.',
						"price_riscado":"'.$price2.'",
						"pvp":"&euro; '.$pvp.'",
						"stock": "'.$prods['prod_precos']['STOCK'].'",
						"image":"'.$prods['prd_imgm_new'].'",
						"title":"'.$titulo.'",
						"subtitle":"'.$subtitulo.'",
						"caminho":"'.$link.'",
						"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
						"promo_data":"0000-00-00",
						"global_promos":'.$global_promos.',
						"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
						"novidade":"'.$prods['prod_precos']['NOVO'].'",
						"m_id":"'.$prods['prod_id_marca'].'",
						"c":['.$categories_prod.'],
						"f":['.$families_prod.'],
						"u_i":"'.$prods["u_imagetiq"].'",
						"u_c":"'.$prods["u_catetiq"].'",
						"u_co":"'.$prods["u_coretiq"].'",
						"carrinho":"'.$prods['prod_carrinho'].'",
						"fl":"0",
						"produto_n_standard":"'.$prods['produto_standard'].'",
						"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'",
						"store_icons":"'.$prods['store_icons'].'"
						}';


					$rs->db_next();
					$j++;
				}
				$f_json_destaque.=']}';

				// echo '<pre>'; print_r($f_json_destaque); echo '</pre>';

				$f_json_destaque = mb_convert_encoding($f_json_destaque,'HTML-ENTITIES','UTF-8');

				$myFile=$f_original;
				$fh=fopen($myFile, 'w');
				fwrite($fh, $f_json_destaque);
				fclose($fh);

				//$f_json_destaque = mb_convert_encoding($f_json_destaque,'HTML-ENTITIES','UTF-8');
				//$f_json_destaque=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_destaque), true );
				$f_json_destaque=json_decode( $f_json_destaque, true );
				if(!$f_json_destaque)$f_json_destaque=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_destaque), true );

			}
			$rs->db_close();

		}

		return $f_json_destaque;
	}
	function gera_json_home_promocoes($gera=0)
	{
		//$gera=1;
		$f_json_promocao='';
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_promocao'.$GLOBALS['cli_id'].'.txt';

		//Como as promocoes podem terminar ou começar de minuto a minuto temos de gravar cache apenas de 1minuto
		$time_expire=time() - 60; // Expire Time (1 minuto)
		if((is_file($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json_promocao=(fread($fh, filesize($myFile)));
			fclose($fh);
			$f_json_promocao = mb_convert_encoding($f_json_promocao,'HTML-ENTITIES','UTF-8');
			$f_json_promocao=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_promocao), true );
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$sql = 'SELECT obj_type_id
			FROM products_objs
			INNER JOIN products on products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products_objs.obj_promo_ativo=1 AND products_families.visivel=1 AND products.Aproved=1 AND products_objs.Promo_data_ini <= CURDATE() AND products_objs.Promo_data_fim >= CURDATE() group by products_objs.obj_type_id limit 0,15';
			$rs->db_open($db_link,DB_NAME,$sql);
			// $rs->db_open($db_link,DB_NAME,'SELECT obj_type_id FROM products_objs WHERE Visivel=1 AND Promo_data_ini <= CURDATE() AND Promo_data_fim >= CURDATE() order by obj_destaque Desc,RAND() limit 0,15');

			if(!$rs->EOF())
			{
				$f_json_promocao='{"Produtos":[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$price2=0;
					$global_promos='[{}]';

					$prods=\get_produtc_info($m_id);
					$new_prods = '';
					//verifica se o produto está fora das datas de promoção
					$new_prods = verifica_promo($m_id, $prods['prod_precos']);
					if(!empty($new_prods))
					{
						$prods = $new_prods;
					}

					if($prods['prod_precos']['PVP'] > 0)
					{
						$pvp=$prods['prod_precos']['PVP'];

						if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
						{
							$price2=$prods['prod_precos']['PRECO_ANTIGO'];
							$global_promos='['.\show_global_promos($m_id).']';
						}
					}

					$prod_id=$m_id;
	 				$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
					if($j>0)$f_json_promocao.=',';

					$families_prod='{}';
					$categories_prod='{}';

					$titulo= $prods['prod_nome'];
					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);

					$subtitulo= $prods['prod_descricao'];
					$subtitulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$subtitulo = strip_tags($subtitulo,'<br>');
					$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
					$subtitulo = str_replace('"','\"',$subtitulo);

					$f_json_promocao.='{
						"id":"'.$m_id.'",
						"ord":'.$prods['prod_ordem'].',
						"ref":"'.$prods['prod_ref'].'",
						"price":'.$pvp.',
						"price_riscado":"'.$price2.'",
						"pvp":"&euro; '.$pvp.'",
						"stock": "'.$prods['prod_precos']['STOCK'].'",
						"image":"'.$prods['prd_imgm_new'].'",
						"title":"'.$titulo.'",
						"subtitle":"'.$subtitulo.'",
						"caminho":"'.$link.'",
						"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
						"promo_data":"0000-00-00",
						"global_promos":'.$global_promos.',
						"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
						"novidade":"'.$prods['prod_precos']['NOVO'].'",
						"m_id":"'.$prods['prod_id_marca'].'",
						"c":['.$categories_prod.'],
						"f":['.$families_prod.'],
						"u_i":"'.$prods["u_imagetiq"].'",
						"u_c":"'.$prods["u_catetiq"].'",
						"u_co":"'.$prods["u_coretiq"].'",
						"carrinho":"'.$prods['prod_carrinho'].'",
						"fl":"0",
						"produto_n_standard":"'.$prods['produto_standard'].'",
						"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'"
						}';



					$rs->db_next();
					$j++;
				}
				$f_json_promocao.=']}';

				$myFile=$f_original;
				$fh=fopen($myFile, 'w');
				fwrite($fh, $f_json_promocao);
				fclose($fh);

				$f_json_promocao = mb_convert_encoding($f_json_promocao,'HTML-ENTITIES','UTF-8');
				$f_json_promocao=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_promocao), true );
			}
			$rs->db_close();
		}
		return $f_json_promocao;
	}
	function gera_json_maisvendidos($gera=0)
	{
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_maisvendido'.$GLOBALS['cli_id'].'.txt';

		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']==1)$gera=1;
		if(!isset($GLOBALS['tem_cron_filtros']))
		{
			$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
			if(is_file($f_original) && filectime($f_original) < $time_expire)$gera=1;
		}
		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json_maisvendido=(fread($fh, filesize($myFile)));
			fclose($fh);
			$f_json_maisvendido = mb_convert_encoding($f_json_maisvendido,'HTML-ENTITIES','UTF-8');
			$f_json_maisvendido=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_maisvendido), true );
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			/*$sql='SELECT obj_type_id as ID_Produto, COUNT(encomendas_produtos.ID) AS N_Compras
			FROM products_objs
			INNER JOIN encomendas_produtos on encomendas_produtos.ID_Produto=products_objs.obj_type_id
			LEFT JOIN products On products.ID=products_objs.obj_type_id
			WHERE products.Aproved=1
			group by products_objs.obj_type_id
			UNION
			SELECT obj_type_id as ID_Produto, NULL
			FROM products_objs
			LEFT JOIN products On products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products.Aproved=1 AND products_families.visivel=1
			group by products_objs.obj_type_id
			ORDER BY `N_Compras` DESC
			limit 0,15';*/

			$sql='SELECT obj_type_id as ID_Produto, COUNT(encomendas_produtos.ID) AS N_Compras
			FROM products_objs
			INNER JOIN encomendas_produtos on encomendas_produtos.ID_Produto=products_objs.obj_type_id
			INNER JOIN products on products_objs.obj_type_id=products.id
			
			WHERE products_objs.Visivel=1
			group by products_objs.obj_type_id
			UNION
			SELECT obj_type_id as ID_Produto, NULL
			FROM products_objs
			
			WHERE products_objs.Visivel=1 AND products.Aproved=1
			group by products_objs.obj_type_id
			ORDER BY `N_Compras` DESC
			limit 0,15';


			$rs->db_open($db_link,DB_NAME,$sql);

			if(!$rs->EOF())
			{
				$f_json_maisvendido='{"Produtos":[';
				$j=0;
				while(!$rs->EOF())
				{
					//$num_mais_vendidos++;
					$m_id=$rs->db_data['ID_Produto'];
					$price2=0;
					$prods=\get_produtc_info($m_id);

					if(is_array($prods))
					{
						$new_prods = '';
						//verifica se o produto está fora das datas de promoção
						$new_prods = verifica_promo($m_id, $prods['prod_precos']);
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}

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

						$prod_id=$m_id;
		 				$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json_maisvendido.=',';

						$families_prod='{}';
						$categories_prod='{}';

						$titulo= $prods['prod_nome'];
						$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$titulo = strip_tags($titulo,'<br>');
						$titulo = preg_replace( "/\r|\n/", " ", $titulo);
						$titulo = str_replace('"','',$titulo);
						$titulo = str_replace('\\','',$titulo);

						$subtitulo= $prods['prod_descricao'];
						$subtitulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$subtitulo = strip_tags($subtitulo,'<br>');
						$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
						$subtitulo = str_replace('"','\"',$subtitulo);

						$f_json_maisvendido.='{
							"id":"'.$m_id.'",
							"ord":'.$prods['prod_ordem'].',
							"ref":"'.$prods['prod_ref'].'",
							"price":'.$pvp.',
							"price_riscado":'.$price2.',
							"pvp":"&euro; '.$pvp.'",
							"stock": "'.$prods['prod_precos']['STOCK'].'",
							"image":"'.$prods['prd_imgm_new'].'",
							"title":"'.$titulo.'",
							"subtitle":"'.$subtitulo.'",
							"caminho":"'.$link.'",
							"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
							"promo_data":"0000-00-00",
							"global_promos":'.$global_promos.',
							"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"m_id":"'.$prods['prod_id_marca'].'",
							"c":['.$categories_prod.'],
							"f":['.$families_prod.'],
							"u_i":"'.$prods["u_imagetiq"].'",
							"u_c":"'.$prods["u_catetiq"].'",
							"u_co":"'.$prods["u_coretiq"].'",
							"carrinho":"'.$prods['prod_carrinho'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"novidade_data":"0000-00-00",
							"outlet":"'.$prods['prod_precos']['OUTLET'].'",
							"outlet_data":"0000-00-00",
							"destaque_data":"0000-00-00",
							"dodia_data":"0000-00-00",
							"fl":"0",
							"produto_n_standard":"'.$prods['produto_standard'].'",
							"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'"
							}';
						$j++;
					}

					$rs->db_next();
				}
				$f_json_maisvendido.=']}';
			}
			else
			{
				$f_json_maisvendido='{"Produtos":[]}';
			}
			$rs->db_close();

			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json_maisvendido);
			fclose($fh);

			$f_json_maisvendido = mb_convert_encoding($f_json_maisvendido,'HTML-ENTITIES','UTF-8');
			$f_json_maisvendido=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_maisvendido), true );
		}
		return $f_json_maisvendido;
	}
	function gera_json_home_novidades($gera=0)
	{
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_novidades'.$GLOBALS['cli_id'].'.txt';

		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']==1)$gera=1;
		if(!isset($GLOBALS['tem_cron_filtros']))
		{
			$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
			if(is_file($f_original) && filectime($f_original) < $time_expire)$gera=1;
		}
		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json_novidades=(fread($fh, filesize($myFile)));
			fclose($fh);
			$f_json_novidades = mb_convert_encoding($f_json_novidades,'HTML-ENTITIES','UTF-8');
			$f_json_novidades=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_novidades), true );
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			/*$sql = 'SELECT obj_type_id
			FROM products_objs
			INNER JOIN products on products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products.Aproved=1 AND products_families.visivel=1 AND obj_novo=1 group by obj_type_id order by RAND() limit 0,15';
			$rs->db_open($db_link,DB_NAME,$sql);*/
			// $rs->db_open($db_link,DB_NAME,'SELECT obj_type_id FROM products_objs where Visivel=1 and obj_novo=1 order by RAND() limit 0,15');

			$sql = 'SELECT obj_type_id FROM products_objs INNER JOIN products ON products.ID=products_objs.obj_type_id WHERE products_objs.Visivel=1 AND products_objs.obj_novo=1 AND products.Aproved=1 ORDER BY RAND() LIMIT 0,15';

			$sql = 'SELECT obj_type_id
			FROM products_objs
			INNER JOIN products on products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products.Aproved=1 AND products_families.visivel=1 and obj_stock > 0  group by obj_type_id order by obj_novo DESC, RAND() limit 0,15';


			$rs->db_open($db_link,DB_NAME,$sql);

			if(!$rs->EOF())
			{
				$f_json_novidades='{"Produtos":[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$price2=0;
					$prods=\get_produtc_info($m_id);

					if(is_array($prods))
					{
						$new_prods = '';
						//verifica se o produto está fora das datas de promoção
						$new_prods = verifica_promo($m_id, $prods['prod_precos']);
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}

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

						$prod_id=$m_id;
		 				$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json_novidades.=',';

						$families_prod='{}';
						$categories_prod='{}';

						$titulo= $prods['prod_nome'];
						$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$titulo = strip_tags($titulo,'<br>');
						$titulo = preg_replace( "/\r|\n/", " ", $titulo);
						$titulo = str_replace('"','',$titulo);
						$titulo = str_replace('\\','',$titulo);

						$subtitulo= $prods['prod_descricao'];
						$subtitulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$subtitulo = strip_tags($subtitulo,'<br>');
						$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
						$subtitulo = str_replace('"','\"',$subtitulo);

						

						$f_json_novidades.='{
							"id":"'.$m_id.'",
							"ord":'.$prods['prod_ordem'].',
							"ref":"'.$prods['prod_ref'].'",
							"price":'.$pvp.',
							"price_riscado":'.$price2.',
							"pvp":"&euro; '.$pvp.'",
							"stock": "'.$prods['prod_precos']['STOCK'].'",
							"image":"'.$prods['prd_imgm_new'].'",
							"title":"'.$titulo.'",
							"subtitle":"'.$subtitulo.'",
							"caminho":"'.$link.'",
							"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
							"promo_data":"0000-00-00",
							"global_promos":'.$global_promos.',
							"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"m_id":"'.$prods['prod_id_marca'].'",
							"c":['.$categories_prod.'],
							"f":['.$families_prod.'],
							"u_i":"'.$prods["u_imagetiq"].'",
							"u_c":"'.$prods["u_catetiq"].'",
							"u_co":"'.$prods["u_coretiq"].'",
							"carrinho":"'.$prods['prod_carrinho'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"novidade_data":"0000-00-00",
							"outlet":"'.$prods['prod_precos']['OUTLET'].'",
							"outlet_data":"0000-00-00",
							"destaque_data":"0000-00-00",
							"dodia_data":"0000-00-00",
							"fl":"0",
							"produto_n_standard":"'.$prods['produto_standard'].'",
							"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'"
							}';

						$j++;
					}

					$rs->db_next();
				}
				$f_json_novidades.=']}';
			}
			else
			{
				$f_json_novidades='{"Produtos":[]}';
			}
			$rs->db_close();

			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json_novidades);
			fclose($fh);

			$f_json_novidades = mb_convert_encoding($f_json_novidades,'HTML-ENTITIES','UTF-8');
			$f_json_novidades=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_novidades), true );
		}

		return $f_json_novidades;
	}
	function gera_json_home_random($gera=0)
	{
		//$gera=1;
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_random'.$GLOBALS['cli_id'].'.txt';

		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']==1)$gera=1;
		if(!isset($GLOBALS['tem_cron_filtros']))
		{
			$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
			if(is_file($f_original) && filectime($f_original) < $time_expire)$gera=1;
		}
		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json_random=(fread($fh, filesize($myFile)));
			fclose($fh);
			$f_json_random = mb_convert_encoding($f_json_random,'HTML-ENTITIES','UTF-8');			
			$f_json_random=json_decode( $f_json_novidades, true );

			if(!$f_json_random)$f_json_random=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_random), true );
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			
			// $sql='SELECT obj_type_id
			// 	FROM products_objs
			// 	INNER JOIN products on products.ID=products_objs.obj_type_id
			// 	INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			// 	INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			// 	WHERE products.Aproved=1 AND products_families.visivel=1 and obj_stock > 0 group by obj_type_id order by obj_destaque Desc, RAND() limit 0,15';

			$sql='SELECT obj_type_id
				FROM products_objs
				INNER JOIN products on products.ID=products_objs.obj_type_id
				INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
				INNER JOIN products_families on products_families.ID=FPO.ID_ORG
				WHERE products.Aproved=1 AND products_families.visivel=1 and obj_stock > 0 order by RAND() limit 0,15';

			//vou trocar por querys a tabela onde estão os preços, existe um ganho muito significativo porque deixo de ter inner joins:
			//$sql = 'SELECT obj_type_id FROM products_objs where Visivel=1 order by obj_destaque Desc, RAND() limit 0,15';
			$rs->db_open($db_link,DB_NAME,$sql);

			if(!$rs->EOF())
			{
				$f_json_random='{"Produtos":[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$price2=0;
					$global_promos='[{}]';

					$prods=\get_produtc_info($m_id);

					 

					$new_prods = '';
					//verifica se o produto está fora das datas de promoção
					$new_prods = verifica_promo($m_id, $prods['prod_precos']);
					
					
					if(!empty($new_prods))
					{
						$prods = $new_prods;
					}
					// echo '<pre>'; echo $prods['store_icons'] ; echo '</pre>';

					if($prods['prod_precos']['PVP'] > 0)
					{
						$pvp=$prods['prod_precos']['PVP'];
						if($prods['prod_precos']['PRECO_ANTIGO'] > 0)
						{
							$price2=$prods['prod_precos']['PRECO_ANTIGO'];
							$global_promos='['.\show_global_promos($m_id).']';
						}
					}
					
					
					
					$prod_id=$m_id;
					$families_prod='{}';
					$categories_prod='{}';

					if(!$pvp)$pvp=0;
	 				$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);

	 				$titulo= $prods['prod_nome'];
					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);


					$subtitulo= $prods['prod_descricao'];
					$subtitulo = mb_convert_encoding($subtitulo,'HTML-ENTITIES','UTF-8');
					$subtitulo = strip_tags($subtitulo,'<br>');
					$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
					$subtitulo = str_replace('"','\"',$subtitulo);
					
					
					
					// echo '<pre>store_icons: '.$prods['store_icons'].'</pre>';

					if($j>0)$f_json_random.=',';
					$f_json_random.='{
						"id":"'.$m_id.'",
						"ord":'.$prods['prod_ordem'].',
						"ref":"'.$prods['prod_ref'].'",
						"price":'.$pvp.',
						"price_riscado":"'.$price2.'",
						"pvp":"&euro; '.$pvp.'",
						"stock": "'.$prods['prod_precos']['STOCK'].'",
						"image":"'.$prods['prd_imgm_new'].'",
						"title":"'.$titulo.'",
						"subtitle":"'.$subtitulo.'",
						"caminho":"'.$link.'",
						"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
						"promo_data":"0000-00-00",
						"global_promos":'.$global_promos.',
						"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
						"novidade":"'.$prods['prod_precos']['NOVO'].'",
						"m_id":"'.$prods['prod_id_marca'].'",
						"c":['.$categories_prod.'],
						"f":['.$families_prod.'],
						"u_i":"'.$prods["u_imagetiq"].'",
						"u_c":"'.$prods["u_catetiq"].'",
						"u_co":"'.$prods["u_coretiq"].'",
						"carrinho":"'.$prods['prod_carrinho'].'",
						"fl":"0",
						"produto_n_standard":"'.$prods['produto_standard'].'",
						"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'",
						"store_icons":"'.$prods['store_icons'].'"
						}';


					$rs->db_next();
					$j++;
				}
				$f_json_novidades.=']}';

				// echo '<pre>'; print_r($f_json_random); echo '</pre>';

				$f_json_novidades = mb_convert_encoding($f_json_novidades,'HTML-ENTITIES','UTF-8');

				$myFile=$f_original;
				$fh=fopen($myFile, 'w');
				fwrite($fh, $f_json_random);
				fclose($fh);

				//$f_json_random = mb_convert_encoding($f_json_random,'HTML-ENTITIES','UTF-8');
				//$f_json_random=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_random), true );
				$f_json_random=json_decode( $f_json_random, true );
				if(!$f_json_random)$f_json_random=json_decode( preg_replace('/[\x00-\x1F\x80-\xFF]/', '', $f_json_random), true );
			}
			$rs->db_close();
		}

		return $f_json_random;
	}
	function gera_json_relacionados($prod_id=0,$gera=0)
	{
		//$gera=1;
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/f_json_relacionados_'.$prod_id.'.txt';

		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			$f_json='var products=[';
			$j=0;

			$pvp=$price2=0;

			//o proximo codigo serve para ler os produtos relacionados
			$prods2 = getProdutosRelacionados_filtros($prod_id);
			for ($b = 0;$b < count($prods2);$b++)
			{
				$m_id=$prods2[$b];
				$prods=\get_produtc_info($m_id);
				if(is_array($prods))
				{
					$new_prods = '';
					//verifica se o produto está fora das datas de promoção
					$new_prods = verifica_promo($m_id, $prods['prod_precos']);
					if(!empty($new_prods))
					{
						$prods = $new_prods;
					}

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

					$prod_id=$m_id;
	 				$stock_txt = get_stock_legenda($prods);

	 				$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);

	 				
					if($j>0)$f_json.=',';

					$families_prod='{}';
					$categories_prod='{}';

					$titulo= $prods['prod_nome'];
					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);
					


					$f_json.='{
						"a0":"1",
						"a1":"'.$m_id.'",
						"a2":'.$prods['prod_ordem'].',
						"a3":"'.$prods['prod_ref'].'",
						"a4":"'.$pvp.'",
						"a5":"'.$price2.'",
						"a6":"'.$pvp.'",
						"a7":"'.$prods['prod_precos']['STOCK'].'",
						"a8":"'.addslashes($stock_txt).'",
						"a9":"",
						"a10":"'.$titulo.'",
						"a11":"",
						"a12":"'.$link.'",
						"a13":"'.$prods['prod_precos']['PROMOCAO'].'",
						"a14":"",
						"a15":'.$global_promos.',
						"a16":"'.$prods['prod_precos']['DESTAQUE'].'",
						"a17":"'.$prods['prod_id_marca'].'",
						"a18":['.$categories_prod.'],
						"a19":['.$families_prod.'],
						"a20":"'.$prods["u_imagetiq"].'",
						"a21":"'.$prods["u_catetiq"].'",
						"a22":"'.$prods["u_coretiq"].'",
						"a23":"'.$prods['prod_carrinho'].'",
						"a24":"'.$prods['prod_precos']['NOVO'].'",
						"a25":"",
						"a26":"'.$prods['prod_precos']['OUTLET'].'",
						"a27":"",
						"a28":"",
						"a29":"",
						"a30":"0",
						"a31":"'.$prods['produto_standard'].'",
						"a32":"'.$prods['c_submissao_ficheiro'].'"
						}';

						$j++;
				}
			}

			$f_json.='];';
			$f_json=str_replace(array('title=\"\"', 'alt=\"\"'), '', $f_json);
			$f_json=str_replace('  ',' ',$f_json);
			$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);

			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);
		}
		return $f_json;
	}
	function gera_json_outlet($gera=0)
	{
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/json_outlet_'.$GLOBALS['site_lang'].'.js';
		
		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']==1)$gera=1;
		if(!isset($GLOBALS['tem_cron_filtros']))
		{
			$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
			if(is_file($f_original) && filectime($f_original) < $time_expire)$gera=1;
		}
		if(is_file($f_original) && !$gera)
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			/*$sql='SELECT obj_type_id
					FROM products_objs
					INNER JOIN products on products.ID=products_objs.obj_type_id
					INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
					INNER JOIN products_families on products_families.ID=FPO.ID_ORG
					WHERE products.Aproved=1 AND products_families.visivel=1 AND products_objs.obj_outlet=1 group by products_objs.obj_type_id limit 0,2000';
			$rs->db_open($db_link,DB_NAME,$sql);*/

			// $rs->db_open($db_link,DB_NAME,'SELECT obj_type_id FROM products_objs where Visivel=1 and obj_outlet=1 order by RAND() limit 0,15');

			$sql = 'SELECT obj_type_id FROM products_objs INNER JOIN products ON products.ID=products_objs.obj_type_id WHERE products_objs.Visivel=1 AND products_objs.obj_outlet=1 AND products.Aproved=1 ORDER BY RAND() limit 0,15';
			$rs->db_open($db_link,DB_NAME,$sql);

			if(!$rs->EOF())
			{
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$pvp=$price2=0;
					$prods=\get_produtc_info($m_id);
					if(is_array($prods))
					{
						$new_prods = '';
						$new_prods = verifica_promo($m_id, $prods['prod_precos']);
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}
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
						// ARVORE DE FAMILIAS, PARA VÁRIOS NIVEIS
						$fam_id = $prods['fam_id'];
						$fams = BuscarArvoreFams_F_FrontEnd($fam_id);
						$fm = '';
						$families = array();
						for($a=0;$a<count($fams);$a++)
						{
							if($fams[$a] > 0) $families[] = array('', $fams[$a]);
							if($a > 0){ $fm .= '_f'.$fams[$a];}else{$fm .= 'f'.$fams[$a];}
						}

						$prod_id=$m_id;
						$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json.=',';

						$categories_prod = \getCategoriesProds($m_id);
						$families_prod = \getFamiliesProds($families);

						if(!$families_prod)$families_prod='{}';
						if(!$categories_prod)$categories_prod='{}';

						$titulo= $prods['prod_nome'];
						$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$titulo = strip_tags($titulo,'<br>');
						$titulo = preg_replace( "/\r|\n/", " ", $titulo);
						$titulo = str_replace('"','',$titulo);
						$titulo = str_replace('\\','',$titulo);

						$subtitulo= $prods['prod_descricao'];
						$subtitulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$subtitulo = strip_tags($subtitulo,'<br>');
						$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
						$subtitulo = str_replace('"','\"',$subtitulo);

						$f_json.='{
							"id":"'.$m_id.'",
							"ord":'.$prods['prod_ordem'].',
							"ref":"'.$prods['prod_ref'].'",
							"price":'.$pvp.',
							"price_riscado":'.$price2.',
							"pvp":"&euro; '.$pvp.'",
							"stock": "'.$prods['prod_precos']['STOCK'].'",
							"image":"'.$prods['prd_imgm_new'].'",
							"title":"'.$titulo.'",
							"subtitle":"'.$subtitulo.'",
							"caminho":"'.$link.'",
							"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
							"promo_data":"0000-00-00",
							"global_promos":'.$global_promos.',
							"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"m_id":"'.$prods['prod_id_marca'].'",
							"c":['.$categories_prod.'],
							"f":['.$families_prod.'],
							"u_i":"'.$prods["u_imagetiq"].'",
							"u_c":"'.$prods["u_catetiq"].'",
							"u_co":"'.$prods["u_coretiq"].'",
							"carrinho":"'.$prods['prod_carrinho'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"novidade_data":"0000-00-00",
							"outlet":"'.$prods['prod_precos']['OUTLET'].'",
							"outlet_data":"0000-00-00",
							"destaque_data":"0000-00-00",
							"dodia_data":"0000-00-00",
							"fl":"0",
							"produto_n_standard":"'.$prods['produto_standard'].'",
							"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'"
							}';
							$j++;
					}

					$rs->db_next();
				}
				$f_json.='];';
				$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);

			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();


			// GRAVAR EM FICHEIRO
			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);
		}
		return $f_json;
	}
	function gera_json_cat_promocoes($gera=0)
	{
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/json_promocoes_'.$GLOBALS['site_lang'].'.js';
		$time_expire=time() - (60); // Expire Time (30 minutos)

		//$gerar=1;
		if((is_file($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			/*$sql = 'SELECT obj_type_id
			FROM products_objs
			INNER JOIN products on products.ID=products_objs.obj_type_id
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_families on products_families.ID=FPO.ID_ORG
			WHERE products_objs.obj_promo_ativo=1 AND products_families.visivel=1 AND products.Aproved=1 AND products_objs.Promo_data_ini <= CURDATE() AND products_objs.Promo_data_fim >= CURDATE() group by products_objs.obj_type_id limit 0,2000';
			$rs->db_open($db_link,DB_NAME,$sql);*/
			
			// $rs->db_open($db_link,DB_NAME,'SELECT obj_type_id FROM products_objs WHERE Visivel=1 AND Promo_data_ini <= CURDATE() AND Promo_data_fim >= CURDATE() order by obj_destaque Desc, limit 0,2000');
			
			$sql = 'SELECT obj_type_id FROM products_objs INNER JOIN products ON products.ID=products_objs.obj_id WHERE products_objs.Visivel=1 AND products_objs.Promo_data_ini <= CURDATE() AND products_objs.Promo_data_fim >= CURDATE() AND products.Aproved=1 ORDER BY obj_destaque DESC, LIMIT 0,2000';
			$rs->db_open($db_link,DB_NAME,$sql);
			
			if(!$rs->EOF())
			{
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$pvp=$price2=0;
					$prods=\get_produtc_info($m_id);
					if(is_array($prods))
					{
						$new_prods = '';
						$new_prods = verifica_promo($m_id, $prods['prod_precos']);
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}
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
						// ARVORE DE FAMILIAS, PARA VÁRIOS NIVEIS
						$fam_id = $prods['fam_id'];
						$fams = BuscarArvoreFams_F_FrontEnd($fam_id);
						$fm = '';
						$families = array();
						for($a=0;$a<count($fams);$a++)
						{
							if($fams[$a] > 0) $families[] = array('', $fams[$a]);
							if($a > 0){ $fm .= '_f'.$fams[$a];}else{$fm .= 'f'.$fams[$a];}
						}

						$prod_id=$m_id;
						$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json.=',';

						$categories_prod = \getCategoriesProds($m_id);
						$families_prod = \getFamiliesProds($families);

						if(!$families_prod)$families_prod='{}';
						if(!$categories_prod)$categories_prod='{}';

						$titulo= $prods['prod_nome'];
						$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$titulo = strip_tags($titulo,'<br>');
						$titulo = preg_replace( "/\r|\n/", " ", $titulo);
						$titulo = str_replace('"','',$titulo);
						$titulo = str_replace('\\','',$titulo);

						$subtitulo= $prods['prod_descricao'];
						$subtitulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$subtitulo = strip_tags($subtitulo,'<br>');
						$subtitulo = preg_replace( "/\r|\n/", " ", $subtitulo);
						$subtitulo = str_replace('"','\"',$subtitulo);

						$f_json.='{
							"id":"'.$m_id.'",
							"ord":'.$prods['prod_ordem'].',
							"ref":"'.$prods['prod_ref'].'",
							"price":'.$pvp.',
							"price_riscado":'.$price2.',
							"pvp":"&euro; '.$pvp.'",
							"stock": "'.$prods['prod_precos']['STOCK'].'",
							"image":"'.$prods['prd_imgm_new'].'",
							"title":"'.$titulo.'",
							"subtitle":"'.$subtitulo.'",
							"caminho":"'.$link.'",
							"promo":"'.$prods['prod_precos']['PROMOCAO'].'",
							"promo_data":"0000-00-00",
							"global_promos":'.$global_promos.',
							"destaque":"'.$prods['prod_precos']['DESTAQUE'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"m_id":"'.$prods['prod_id_marca'].'",
							"c":['.$categories_prod.'],
							"f":['.$families_prod.'],
							"u_i":"'.$prods["u_imagetiq"].'",
							"u_c":"'.$prods["u_catetiq"].'",
							"u_co":"'.$prods["u_coretiq"].'",
							"carrinho":"'.$prods['prod_carrinho'].'",
							"novidade":"'.$prods['prod_precos']['NOVO'].'",
							"novidade_data":"0000-00-00",
							"outlet":"'.$prods['prod_precos']['OUTLET'].'",
							"outlet_data":"0000-00-00",
							"destaque_data":"0000-00-00",
							"dodia_data":"0000-00-00",
							"fl":"0",
							"produto_n_standard":"'.$prods['produto_standard'].'",
							"c_submissao_ficheiro":"'.$prods['c_submissao_ficheiro'].'"
							}';
							$j++;
					}

					$rs->db_next();
				}
				$f_json.='];';
				$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);

			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();


			// GRAVAR EM FICHEIRO
			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);
		}
		return $f_json;
	}
	function gera_json_cat_destaques($gera=0)
	{
		$tipo_catalogo='destaque';
		//$gera=1;
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/json_destaque_'.$GLOBALS['cli_id'].$GLOBALS['site_lang'].'.js';

		$time_expire=time() - (60*60*2); // Expire Time (2 horas minutos)
		if((is_file($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;

			/*$sql='SELECT obj_type_id
			FROM products_objs
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products P on P.ID=products_objs.obj_type_id
			WHERE P.Aproved=1 and obj_destaque = 1 group by products_objs.obj_type_id';
			$rs->db_open($db_link,DB_NAME,$sql);*/
			$sql = 'SELECT obj_type_id FROM products_objs INNER JOIN products ON products.ID=products_objs.obj_type_id WHERE products_objs.Visivel=1 AND products_objs.obj_destaque = 1 AND products.Aproved=1 GROUP BY products_objs.obj_type_id';


			$rs->db_open($db_link,DB_NAME,$sql);

			if(!$rs->EOF())
			{
				$prods=array();
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$m_id=$rs->db_data['obj_type_id'];
					$pvp=$price2=0;
					$prods=\get_produtc_info($m_id,1);//Obter dados ignorando as imagens
					if(is_array($prods))
					{
						$new_prods = '';
						if(!empty($new_prods))
						{
							$prods = $new_prods;
						}
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
						$prod_id=$m_id;

						$stock_txt = get_stock_legenda($prods);
						$link=$GLOBALS['site_http_url'].\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
						if($j>0)$f_json.=',';

						/*$families_prod='{}';
						$categories_prod='{}';*/

						$categories_prod='{}';
						$categories_prod = \getCategoriesProds($m_id);
						$families_prod = \getFamiliesProds($families);

						$titulo= $prods['prod_nome'];

						$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
						$titulo = strip_tags($titulo,'<br>');
						$titulo = preg_replace( "/\r|\n/", " ", $titulo);
						$titulo = str_replace('"','',$titulo);
						$titulo = str_replace('\\','',$titulo);


						$f_json.='{
							"a0":"1",
							"a1":"'.$m_id.'",
							"a2":'.$prods['prod_ordem'].',
							"a3":"'.addslashes($prods['prod_ref']).'",
							"a4":"'.$pvp.'",
							"a5":"'.$price2.'",
							"a6":"'.$pvp.'",
							"a7":"'.$prods['prod_precos']['STOCK'].'",
							"a8":"'.addslashes($stock_txt).'",
							"a9":"",
							"a10":"'.$titulo.'",
							"a11":"",
							"a12":"'.$link.'",
							"a13":"'.$prods['prod_precos']['PROMOCAO'].'",
							"a14":"",
							"a15":'.$global_promos.',
							"a16":"'.$prods['prod_precos']['DESTAQUE'].'",
							"a17":"'.$prods['prod_id_marca'].'",
							"a18":['.$categories_prod.'],
							"a19":['.$families_prod.'],
							"a20":"'.$prods["u_imagetiq"].'",
							"a21":"'.$prods["u_catetiq"].'",
							"a22":"'.$prods["u_coretiq"].'",
							"a23":"'.$prods['prod_carrinho'].'",
							"a24":"'.$prods['prod_precos']['NOVO'].'",
							"a25":"",
							"a26":"'.$prods['prod_precos']['OUTLET'].'",
							"a27":"",
							"a28":"",
							"a29":"",
							"a30":"0",
							"a31":"'.$prods['produto_standard'].'",
							"a32":"'.$prods['c_submissao_ficheiro'].'"
							}';

							$j++;
					}

					$rs->db_next();
				}
				$f_json.='];';
				$f_json=str_replace(array('title=\"\"', 'alt=\"\"'), '', $f_json);
				$f_json=str_replace('  ',' ',$f_json);
				$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);
			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();

			// GRAVAR EM FICHEIRO, nao necessita porque no header ja esta a gravar
			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);

		}

		return $f_json;
	}
	function gera_json_marcas($gera=0,$marca=0,$lingua='')
	{
		if($marca!=0)$id_org_gerador=$marca;
		else $id_org_gerador=(isset($_GET['s_marca']) && $_GET['s_marca'] > 0 ? $_GET['s_marca'] : 0);		

		if($lingua == '')
		{
			if(isset($_SERVER['REQUEST_URI']) && substr_count($_SERVER['REQUEST_URI'], '/admin/')) $lingua=$GLOBALS['admin_lang'];
			else $lingua=$GLOBALS['site_lang'];
		}

		$and=$id_org_gerador;
		// get all the child families
		$f_original=$GLOBALS['site_shell_url'].'files/filtros/json_m_'.$id_org_gerador.'_'.$lingua.'.js';

		$gera=1;
		$time_expire=time() - (60*5); // Expire Time (5 minutos)
		if( (is_file($f_original) && !isset($GLOBALS['gerar']) && !$gera && filectime($f_original) > $time_expire) || (isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1) )
		{
			$myFile=$f_original;
			$fh=fopen($myFile, 'r');
			$f_json=(fread($fh, filesize($myFile)));
			fclose($fh);
		}
		else
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$rs2=new DataObj;
			
			// $sql='SELECT products_objs.*,Cabecalho,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam
			// FROM products_objs
			// INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			// INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
			// INNER JOIN products P on P.ID=products_objs.obj_type_id
			// WHERE P.ID_Marca = "'.$id_org_gerador.'" AND PL.Lang="pt" AND products_objs.Visivel=1 group by products_objs.obj_type_id';

			$sql='SELECT products_objs.*,Cabecalho,Ref,u_catetiq,u_coretiq,u_imagetiq,P.ID_Marca,P.Ordem as Ordem2,FPO.ID_ORG as ID_Fam
			FROM products_objs
			INNER JOIN products_families_obj FPO on FPO.ID_PROD=products_objs.obj_type_id
			INNER JOIN products_lang PL on PL.ID_PAI=products_objs.obj_type_id
			INNER JOIN products P on P.ID=products_objs.obj_type_id
			WHERE P.ID_Marca = "'.$id_org_gerador.'" AND PL.Lang="pt" AND products_objs.Visivel=1 AND P.Aproved=1 group by products_objs.obj_type_id';

			$rs->db_open($db_link,DB_NAME,$sql);

			//Definir o formato dos preços
			date_default_timezone_set("Europe/Lisbon");
			setlocale(LC_TIME,"Portuguese_Standard");
			setlocale(LC_ALL,"en_US.UTF-8;LC_NUMERIC=C");
			setlocale(LC_NUMERIC,'en_US');

			$descontos=array();

			if(!$rs->EOF())
			{
				$prods=array();
				$f_json='var products=[';
				$j=0;
				while(!$rs->EOF())
				{
					$prod_id=$rs->db_data['obj_type_id'];
					$obj_pvp=$price2=$rs->db_data['obj_pvp'];
					$obj_stock=$price2=$rs->db_data['obj_stock'];
					$prod_ordem=$rs->db_data['Ordem2'];
					$ID_Marca=$rs->db_data['ID_Marca'];
					$Carrinho=$rs->db_data['Carrinho'];
					$obj_promo_ativo = $rs->db_data['obj_promo_ativo'];
					$precopromo=$obj_promo = $rs->db_data['obj_promo'];
					$obj_destaque = $rs->db_data['obj_destaque'];
					$obj_novo = $rs->db_data['obj_novo'];
					$obj_outlet = $rs->db_data['obj_outlet'];
					$prods['prod_nome']=$rs->db_data['Cabecalho'];
					$prods['prod_id']=$rs->db_data['obj_type_id'];
					$link=$site_http_url.\seo2(2,$GLOBALS['products_page_id'],$prods,$GLOBALS['site_lang']);
					$ID_Sub_Familia=$rs->db_data['ID_Fam'];


					//Agora vamos ler a margem do produto:
					if($GLOBALS['ignorar_margens']==1 && $Sync==1)
					{
						//Vamos ignorar as margens
					}
					else
					{
						if(!$descontos[$ID_Sub_Familia])
						{
							// VAMOS VERIFICAR SE AS FAMILIAS DO PRODUTO TEM ALGUMA MARGEM
							$margem_formula=get_product_families_Margem_formula($ID_Sub_Familia);
							$margem_formula_value=get_product_margin_formula_value($margem_formula,$obj_pvp);
							if($margem_formula_value!==null) $margem=$margem_formula_value;
							else $margem=get_product_families($ID_Sub_Familia);
							
							$descontos[$ID_Sub_Familia] = $margem;

						}
						else $margem = $descontos[$ID_Sub_Familia];

						// VAMOS COLOCAR MARGEM
						$pc=$obj_pvp;
						if(isset($GLOBALS['Margem_personalizada']) && $GLOBALS['Margem_personalizada']==1)
						{
							if($precopromo>0)$precopromo=get_margem_personalizada($precopromo,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
							if($obj_pvp>0)$obj_pvp=get_margem_personalizada($obj_pvp,$obj_Supplier_ID,$fornecedor_importacao,$ID_Sub_Familia);
						}
						if(!is_array($margem))
						{
							if($margem != 0 && $obj_pvp > 0) $obj_pvp=$obj_pvp + ($obj_pvp * ($margem / 100));
							if($margem != 0 && $precopromo > 0) $precopromo=$precopromo + ($precopromo * ($margem / 100));
						}

					}




					if($j>0)$f_json.=',';

					$prod_ref = $rs->db_data['Ref'];
					$Codigo_Desconto = $rs->db_data['Codigo_Desconto'];
					$titulo= $rs->db_data['Cabecalho'];					

					$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
					$titulo = strip_tags($titulo,'<br>');
					$titulo = preg_replace( "/\r|\n/", " ", $titulo);
					$titulo = str_replace('"','',$titulo);
					$titulo = str_replace('\\','',$titulo);




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


						$prods['u_catetiq']=$rs->db_data['u_catetiq'];
						$prods['u_coretiq']=$rs->db_data['u_coretiq'];
						if($rs->db_data['u_imagetiq'] != '' && is_file($GLOBALS['site_shell_url'].'files/products/EE/'.$rs->db_data['u_imagetiq'])) $prods['u_imagetiq']=SITE_HTTP_URL.'files/products/EE/'.$rs->db_data['u_imagetiq'];
						else $prods['u_imagetiq']='';


						$global_promos='[{}]';
						if($price2 != $obj_pvp && $obj_promo > 0 && $obj_promo_ativo == 1)
						{
							$global_promos='['.\show_global_promos($prod_id).']';
						}
						else $price2=0;

						$categories_prod='{}';
						$categories_prod = \getCategoriesProds($prod_id);
						$families_prod = \getFamiliesProds($families);


						
						$f_json.='{
							"a0":"1",
							"a1":"'.$prod_id.'",
							"a2":'.$prod_ordem.',
							"a3":"'.$prod_ref.'",
							"a4":"'.$obj_pvp.'",
							"a5":"'.$price2.'",
							"a6":"'.$obj_pvp.'",
							"a7":"'.$obj_stock.'",
							"a8":"",
							"a9":"'.$img_file.'",
							"a10":"'.$titulo.'",
							"a11":"",
							"a12":"'.$link.'",
							"a13":"'.$obj_promo_ativo.'",
							"a14":"",
							"a15":'.$global_promos.',
							"a16":"'.$obj_destaque.'",
							"a17":"'.$ID_Marca.'",
							"a18":['.$categories_prod.'],
							"a19":['.$families_prod.'],
							"a20":"'.$prods["u_imagetiq"].'",
							"a21":"'.$prods["u_catetiq"].'",
							"a22":"'.$prods["u_coretiq"].'",
							"a23":"'.$Carrinho.'",
							"a24":"'.$obj_novo.'",
							"a25":"",
							"a26":"'.$obj_outlet.'",
							"a27":"",
							"a28":"",
							"a29":"",
							"a30":"0",
							"a31":"1",
							"a32":""
							}';

							$j++;
					

					$rs->db_next();
				}
				$f_json.='];';
			}
			else
			{
				$f_json='var products=[];';
			}
			$rs->db_close();



			// GRAVAR EM FICHEIRO, nao necessita porque no header ja esta a gravar
			$myFile=$f_original;
			$fh=fopen($myFile, 'w');
			fwrite($fh, $f_json);
			fclose($fh);
		}
		return str_replace("\n","",$f_json);
	}
	function gera_json_pesquisa($searchstr='')
	{
		$prods2=searchProductsMemoryTable($searchstr);
	    if(!empty($prods2))
	    {
	    	$prods=array();
			$f_json='var products=[';
			$j=0;

			foreach($prods2 as $prodId => $prodNome)
			{
				$prod_id=$m_id=$prodId;
				$titulo= $prodNome;
				$titulo = mb_convert_encoding($titulo,'HTML-ENTITIES','UTF-8');
				$titulo = strip_tags($titulo,'<br>');
				$titulo = preg_replace( "/\r|\n/", " ", $titulo);
				$titulo = str_replace('"','',$titulo);
				$titulo = str_replace('\\','',$titulo);

				//-------------------------------
				$pvp=$price2=0;
				$prods['prod_precos']=get_produtc_price($prod_id);
				if($prods['prod_precos']['PVP'] > 0)
				{
					$pvp=$prods['prod_precos']['PVP'];
				}
				//-------------------------------------
				$families_prod='{}';					
				$categories_prod = \getCategoriesProds($m_id);
				if(!$families_prod)$families_prod='{}';
				if(!$categories_prod)$categories_prod='{}';

				//if($prods['prod_precos']['STOCK'] > 0 || $prods['fam_id']==85)
				//{
					if($j>0)$f_json.=',';
					
					$f_json.='{
						"a0":"1",
						"a1":"'.$m_id.'",
						"a2":"0",
						"a3":"",
						"a4":"'.$pvp.'",
						"a5":"'.$price2.'",
						"a6":"'.$pvp.'",
						"a7":"'.$prods['prod_precos']['STOCK'].'",
						"a8":"",
						"a9":"'.$img_file.'",
						"a10":"'.$titulo.'",
						"a11":"",
						"a12":"",
						"a13":"'.$prods['prod_precos']['PROMOCAO'].'",
						"a14":"",
						"a15":"",
						"a16":"",
						"a17":"",
						"a32":"",
						"a18":['.$categories_prod.'],
						"a19":['.$families_prod.'],
						"a20":"",
						"a21":"",
						"a22":"",
						"a23":"",
						"a24":"'.$prods['prod_precos']['NOVO'].'",
						"a25":"",
						"a26":"'.$prods['prod_precos']['OUTLET'].'",
						"a27":"",
						"a28":"",
						"a29":"",
						"a30":"0",
						"a31":""
						}';
					
					$j++;
				//}
			}
			$f_json.='];';
			$f_json=str_replace(array('title=\"\"', 'alt=\"\"'), '', $f_json);
			$f_json=str_replace('  ',' ',$f_json);
			$f_json=preg_replace('/[\x00-\x1F\x7F]/u', '', $f_json);
	    }
	    else
		{
			$f_json='var products=[];';
		}
		return $f_json;
	}

