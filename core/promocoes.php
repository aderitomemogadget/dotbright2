<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function getSalesPromotions()
{		
	// INFORMAÇÃO DO CARRINHO
	$cart=cart_info();
	$cartprods=isset($cart['cartprods']) && is_array($cart['cartprods']) ? $cart['cartprods'] : array();
	$cprds=implode(',',$cartprods);

	if($cprds != '')
	{
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new DataObj;
		$sql='SELECT 
					sp.promo_id, sp.DESIGNACAO, sp.TIPO, sp.MINIMO, sp.MAXIMO, sp.TIPOPROMOCAO, sp.TIPOCAMPANHA, sp.TEMPO,
					spp.DESCONTO, spp.PROD_ID,
					date_add(sp.DATA_FIM, interval 1 day) AS DTAFIM
				FROM 
					sales_promotions sp
					INNER JOIN sales_promotions_products spp ON spp.promo_id=sp.promo_id
				WHERE 
					(sp.DATA_INICIO <= NOW() AND sp.DATA_FIM >= NOW())
					AND sp.ATIVO=1
					AND sp.removido=0
					AND spp.PROD_ID > 0	
					AND spp.PROD_ID IN ('.$cprds.')
				ORDER BY sp.MINIMO';
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$PROMO_ID=$rs->db_data['promo_id']; // ID CAMPANHA	
				$DESIGNACAO=$rs->db_data['DESIGNACAO']; // DESIGNAÇÃO CAMPANHA
				$TIPO=$rs->db_data['TIPO']; // PERCENTAGEM=0 | VALOR=1
				$TIPOPROMOCAO=$rs->db_data['TIPOPROMOCAO']; // INTERVALO VALORES=1 | INTERVALO QUANTIDADES=2 | VALOR ACUMULADO=3
				$TIPOCAMPANHA=$rs->db_data['TIPOCAMPANHA']; // NORMAL=1 | DESCONTO DOS 3=2
				$MINIMO=$rs->db_data['MINIMO'];
				$MAXIMO=$rs->db_data['MAXIMO'];
				$DESCONTO=$rs->db_data['DESCONTO']; // PERCENTAGEM | VALOR
				$PROD_ID=$rs->db_data['PROD_ID'];			
				$TEMPO=$rs->db_data['TEMPO'];
				$DTAFIM=$rs->db_data['DTAFIM'];			
				$p_info=cart_info($PROD_ID);			
				$cmps[$PROMO_ID][$PROD_ID]['PROMO_ID']= $PROMO_ID;
				$cmps[$PROMO_ID][$PROD_ID]['DATAFIMCMP']= $DTAFIM;
				$cmps[$PROMO_ID][$PROD_ID]['DESIGNACAO']=$DESIGNACAO;
				$cmps[$PROMO_ID][$PROD_ID]['TIPO']=$TIPO;
				$cmps[$PROMO_ID][$PROD_ID]['TIPOPROMOCAO']=$TIPOPROMOCAO;
				$cmps[$PROMO_ID][$PROD_ID]['TIPOCAMPANHA']=$TIPOCAMPANHA;
				$cmps[$PROMO_ID][$PROD_ID]['MINIMO']=$MINIMO;
				$cmps[$PROMO_ID][$PROD_ID]['MAXIMO']=$MAXIMO;
				$cmps[$PROMO_ID][$PROD_ID]['TEMPO']=$TEMPO;
				$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$DESCONTO;				
				$cmps[$PROMO_ID][$PROD_ID]['PROD_ID']=$PROD_ID;				
				$cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']=$p_info['qtd'];
				$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']=$p_info['preco'];
				$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_ORDER']=($p_info['preco'] * $p_info['qtd']);
				$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$p_info['preco'];
				$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=0;
				$cmps[$PROMO_ID][$PROD_ID]['N_PROMOS']=floor(($p_info['qtd'] * 1) / $MINIMO);
				uasort($cmps[$PROMO_ID], 'cmp_ordena');
				// CASO SEJA A CAMPANHA DOS 3 VAMOS ACRESCENTAR O ARRAY DE TODOS
				if($TIPOCAMPANHA==2) $cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3'][]=$DESCONTO;
				// CASO SEJA A CAMPANHA DOS 2 VAMOS ACRESCENTAR O ARRAY DE TODOS
				if($TIPOCAMPANHA==7) $cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS2'][]=$DESCONTO;
				$rs->db_next();
			}	
		}
		$rs->db_close();
		// CALCULAR CAMPANHAS PARA DEPOIS ATRIBUIR-MOS A CADA PRODUTO NO CARRINHO
		$cmp_res=calcula_campanhas($cmps, $cart['totalcarrinho']);
	}
	
	return $cmp_res;
}
function cmp_ordena($a,$b){return strcmp($b['PROD_PRECO_ORDER'], $a['PROD_PRECO_ORDER']);}
function getSalesPromotionsOffers($PROMO_ID=0,$TIPOCAMPANHA=0,$desc='')
{		
	// INFORMAÇÃO DO CARRINHO
	$cart=cart_info();
	$cartprods=isset($cart['cartprods']) && is_array($cart['cartprods']) ? $cart['cartprods'] : array();
	$cprds=implode(',',$cartprods);
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;$rs2=new DataObj;
	$sql='SELECT PROD_ID, DESIGNACAO FROM sales_promotions_offers WHERE promo_id="'.$PROMO_ID.'" ORDER BY DESIGNACAO';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$sql='SELECT 
				date_add(sp.DATA_FIM, interval 1 day) AS DTAFIM
			FROM 
				 sales_promotions sp
			WHERE 
				promo_id="'.$PROMO_ID.'"';
		$rs2->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if(!$rs2->EOF())
		{
			$DTAFIM=$rs2->db_data['DTAFIM'];
		}		
		$rs2->db_close();
		while(!$rs->EOF())
		{			
			$PROD_ID=$rs->db_data['PROD_ID']; // ID CAMPANHA
			$DESIGNACAO=$rs->db_data['DESIGNACAO']; // DESIGNAÇÃO CAMPANHA
			$cmps['PROMO_ID']=$PROMO_ID;
			$cmps['DTAFIM']=$DTAFIM;
			$cmps['DESC']=$desc;
			$cmps[$TIPOCAMPANHA][$PROD_ID]=$DESIGNACAO;
			$rs->db_next();
		}	
	}
	$rs->db_close();
	return $cmps;
}
function getSalesPromotionsOffersAcm()
{
	// INFORMAÇÃO DO CARRINHO
	$cart=cart_info();
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$sql='SELECT sp.promo_id, sp.TIPOCAMPANHA, sp.MINIMO, sp.DESIGNACAO as CMP_DESC, date_add(sp.DATA_FIM, interval 1 day) AS DTAFIM, spf.PROD_ID, spf.DESIGNACAO
	FROM sales_promotions sp INNER JOIN sales_promotions_offers spf ON spf.promo_id=sp.promo_id
	WHERE sp.ATIVO=1 AND ((sp.TIPOCAMPANHA=4 AND sp.TIPOPROMOCAO=3) OR (sp.TIPOCAMPANHA=3 AND sp.TIPOPROMOCAO=3)) AND sp.removido=0
	ORDER BY spf.DESIGNACAO';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	if(!$rs->EOF())
	{
		while(!$rs->EOF())
		{			
			$PROMO_ID=$rs->db_data['promo_id']; // ID CAMPANHA
			$PROD_ID=$rs->db_data['PROD_ID']; // ID CAMPANHA
			$DESIGNACAO=$rs->db_data['DESIGNACAO']; // DESIGNAÇÃO CAMPANHA
			$CMP_DESC=$rs->db_data['CMP_DESC']; // DESIGNAÇÃO CAMPANHA
			$TIPOCAMPANHA=$rs->db_data['TIPOCAMPANHA']; // DESIGNAÇÃO CAMPANHA
			$MINIMO=$rs->db_data['MINIMO']; // DESIGNAÇÃO CAMPANHA
			$DTAFIM=$rs->db_data['DTAFIM']; // DESIGNAÇÃO CAMPANHA
			if($cart['totalcarrinho'] >= $MINIMO) // ESTA NO INTERVALO DEFINIDO && $valor_acumulado <= $info['MAXIMO']
			{
				$cmps[$PROMO_ID]['PROMO_ID']=$PROMO_ID;
				$cmps[$PROMO_ID]['DTAFIM']=$DTAFIM;
				$cmps[$PROMO_ID]['DESC']=$CMP_DESC;
				$cmps[$PROMO_ID][$TIPOCAMPANHA][$PROD_ID]=$DESIGNACAO;	 
			}			
			$rs->db_next();
		}	
	}
	$rs->db_close();
	return $cmps;
}
function includedValues($array,$min,$max,$tp=0)
{
	sort($array, SORT_NUMERIC);
    // REMOVE VALORES INFERIORES AO MINIMO
    while($array[0]<$min){array_shift($array);}
    // REMOVE VALORES MAIORES QUE O MAXIMO
    while($array[count($array)-1]>$max){array_pop($array);}
	if($tp==2) // RETORNO DE ARRAY COM TODOS OS VALORES SEM REPETIÇOES E ORDENADOS
	{	
		$array=array_values(array_unique($array));
		return $array;
	}
	else // RETORNO DO VALOR MEDIO SEMPRE O MAIS ALTO
	{
		if(count($array)>2) $valor_med=max(array_values(array_diff($array, array($min, $max))));
		else $valor_med=$min;
		if($valor_med <= 0) $valor_med=$min;
		return $valor_med;
	}
}
function includedDesconto($array,$min,$max,$tp=0)
{
    sort($array, SORT_NUMERIC);
    // REMOVE VALORES INFERIORES AO MINIMO
    while($array[0]<$min){ array_shift($array);}
    // REMOVE VALORES MAIORES QUE O MAXIMO
    while($array[count($array)-1]>$max){array_pop($array);}
	$array=array_unique($array);
	$array=array_values($array);
	return $array[1];	
}
function sales_promotions_reserves($promo_id=0,$MAXIMO=0,$finalizada=0,$enc_id=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$id_client_temp=$_SESSION['CLIENTE_ID_TEMP'];
	$sql='SELECT * FROM sales_promotions_reserves spr WHERE spr.promo_id="'.$promo_id.'" AND spr.ID_CLIENTE="'.$id_client_temp.'"';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if($rs->EOF())
	{
		$sql='INSERT INTO sales_promotions_reserves(
					promo_id,
					N_CMPS,
					DATA_RESERVA,
					ID_CLIENTE,
					FINALIZADA
				)
				VALUES
				(					
					"'.$promo_id.'",
					"1",
					"'.date('Y-m-d H:i:s').'",
					"'.$id_client_temp.'",
					"'.$finalizada.'"
				)';
		$res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
	}
	else
	{	
		$sql='UPDATE sales_promotions_reserves 
		SET FINALIZADA="'.$finalizada.'",ENC_ID="'.$enc_id.'"
		WHERE promo_id="'.$promo_id.'" AND ID_CLIENTE="'.$id_client_temp.'"';
		$res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
	}
	$rs->db_close();
}
function sales_promotions_reserves_verify($promo_id=0,$MAXIMO=0,$TEMPO=0,$OFERTAS=0)
{
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs=new DataObj;
	$id_client_temp=$_SESSION['CLIENTE_ID_TEMP'];
	if($OFERTAS==1)
	{
		$sql='SELECT MAXIMO,TEMPO FROM sales_promotions sp WHERE sp.promo_id="'.$promo_id.'"';
		$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
		if(!$rs->EOF())
		{
			$MAXIMO=$rs->db_data['MAXIMO'];
			$TEMPO=$rs->db_data['TEMPO'];		
		}
		$rs->db_close();	
	}	
	$passa=0;
	$n_campanhas_reservadas_off=$MAXIMO;
	// CAMPANHAS QUE SE ENCONTRA RESERVADAS
	$n_campanhas_reservadas=0;
	// VAMOS VERIFICAR QUANTAS JÁ ESTÃO RESERVADAS
	$sql='SELECT count(N_CMPS) as N_CMPS_T FROM sales_promotions_reserves spr WHERE spr.promo_id="'.$promo_id.'"';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	if(!$rs->EOF()) // JÁ EXISTEM REGISTOS, VAMOS VERIFICAR SE AINDA DÁ PARA PARA FAZER PROMOS
	{
		$n_campanhas_reservadas=$rs->db_data['N_CMPS_T'];
	}
	$rs->db_close();
	// VAMOS VERIFICAR DE TODAS AS CAMPANHAS RESERVADAS SE EXISTE ALGUMA QUE NÃO TENHA NÃO TENHA SIDO FINALIZADA E SE ESTÁ LIVRE
	$sql='SELECT 
				spr.reserve_id,spr.FINALIZADA, spr.ID_CLIENTE, TIMESTAMPDIFF(MINUTE, DATA_RESERVA, now()) as minutes_passed
			FROM 
				sales_promotions_reserves spr
			WHERE 
				spr.promo_id="'.$promo_id.'"';
	$rs->db_open($db_link,$GLOBALS['DB_name'], $sql);
	if(!$rs->EOF()) // já existem registos, vamos verificar se ainda dá para para fazer promos
	{
		while(!$rs->EOF())
		{
			$minutes_passed=$rs->db_data['minutes_passed'];
			$FINALIZADA=$rs->db_data['FINALIZADA'];
			$reserve_id=$rs->db_data['reserve_id'];
			$n_campanhas_reservadas_off--;
			if($minutes_passed >= $TEMPO) 
			{			
				if($FINALIZADA==0 && $reserve_id > 0)
				{
					$sql='DELETE FROM sales_promotions_reserves WHERE reserve_id='.$reserve_id.' AND FINALIZADA=0';
					$res=Runquery($db_link,$GLOBALS['DB_name'], $sql);
				}
			}			
			if($FINALIZADA==0) $arr_cmp_user[]=$rs->db_data['ID_CLIENTE'];			
			$rs->db_next();
		}
	}
	$rs->db_close();	
	$n_cmp=0;
	$n_cmp=($n_campanhas_reservadas - $n_campanhas_reservadas_off);	
	if($n_cmp < $MAXIMO || in_array($id_client_temp,$arr_cmp_user))$passa=1;	
	return $passa;
}
function strInArray($haystack,$needle)
{
    $i=0;
    foreach($haystack as $value)
    {
        $result=stripos($value,$needle);
        if($result !== FALSE) return TRUE;
        $i++;
    }
    return FALSE;
}
function calcula_campanhas($cmps,$total_carrinho)
{
	$pdr=array();
	foreach($cmps as $PROMO_ID=>$protutos)
	{
		// PARA QUANTIDADES E PARA VALORES
		$qtd_total=0;	$qtd_totald2=0; $qtd_totald3=0;	$valor_acumulado=0; $arr_prod_d3=array(); $cmps_restante=0;
		foreach($protutos as $PROD_ID=>$info)
		{
			if($info['TIPOCAMPANHA']==6)
			{
				if(!in_array($PROD_ID, $prd[$PROMO_ID]))
				{
					$prd[$PROMO_ID][]=$PROD_ID;
					$qtd_total += $info['PROD_QTD']; 
					$valor_acumulado += ($info['PROD_PRECO'] * $info['PROD_QTD']); 
				}
			}
			elseif($info['TIPOCAMPANHA']!=6)
			{
				$qtd_total += $info['PROD_QTD']; 
				$valor_acumulado += ($info['PROD_PRECO'] * $info['PROD_QTD']); 
			}			
			if($info['TIPOCAMPANHA']==2) // PROMO DOS 3
			{
				if(!in_array($PROD_ID, $prd[$PROMO_ID])) $qtd_totald3 += 1;
				$arr_prod_d3[]=$PROD_ID;$arr_prodp_d3[$PROMO_ID][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
			}
			if($info['TIPOCAMPANHA']==7) // PROMO DOS 2
			{
				
				if(!in_array($PROD_ID, $prd[$PROMO_ID])) $qtd_totald2 += 1;
				$arr_prod_d2[]=$PROD_ID;$arr_prodp_d2[]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
			}
			if($info['TIPOCAMPANHA']==6)
			{
				$arr_prod_d4[$PROMO_ID][]=$PROD_ID; $arr_prodp_d4[$PROMO_ID][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
			}
		}
		$arrprom=array();		
		foreach($protutos as $PROD_ID=>$info)
		{
			$PROMO_ID=$info['PROMO_ID']; // QTD | VALOR | ACUMULADO
			$MAXIMO=$info['MAXIMO']; // QTD | VALOR | ACUMULADO
			$TEMPO=$info['TEMPO']; // QTD | VALOR | ACUMULADO
			$prom_tipo=$info['TIPOPROMOCAO']; // QTD =1 para todos | QTD=4 limitado | VALOR | ACUMULADO
			$cmp_tipo=$info['TIPOCAMPANHA']; // NORMAL | DOS 3			
			$arrpromd3[$PROMO_ID]=array();
			$passa=sales_promotions_reserves_verify($PROMO_ID, $MAXIMO, $TEMPO);
			if(($prom_tipo==1 || $prom_tipo==4) && $passa==1) // INT. QUANTIDADES
			{
				if($qtd_total >= $info['MINIMO']) // ESTA NO INTERVALO DEFINIDO // && $qtd_total <= $info['MAXIMO'] || $qtd_total >= $info['MAXIMO'] 
				{
					if($info['TIPO'] == 0) // PERCENTAGEM
					{
						if($cmp_tipo==2) // PROMOCAO DOS 3 DESCONTOS
						{
							// REGRAS							
							// O MENOR VALOR - 25 %
							// O MÉDIO VALOR - 50 %
							// O MAIOR VALOR - 75 %
							$preco_max=max($arr_prodp_d3[$PROMO_ID]);
							$preco_min=min($arr_prodp_d3[$PROMO_ID]);
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d3[$PROMO_ID], $preco_min, $preco_max);
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d3[$PROMO_ID], $preco_min, $preco_max,2);
							$desc_max=max($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							$desc_min=min($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=includedDesconto($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3'], $des_min, $desc_max, 1);							
							if($PROMO_ID == 60) echo $desc_min.' | '.$desc_med.' | '.$desc_max.'<br />';
							$i=0;
							// QUANTOS PRODUTOS EXISTEM QUE PERTENCEM A CAMPANHA
							$keyCount=0;
							foreach($cmps[$PROMO_ID] as $key=>$val){$keyCount +=  $val['PROD_QTD'];}
							$cmps[$PROMO_ID]['available']=floor(($keyCount * 1) / $info['MINIMO']); // NUMERO DE PROMOS DISPONIVEIS QUE O UTILIZADOR TEM DIREITO A GANHAR
							while($i<$qtd_total)
							{
								$countValues=array_count_values($arrpromd3[$PROMO_ID]);
								$countValues_min=($countValues[$desc_min]=='' ? 0 : $countValues[$desc_min]);
								$countValues_med=($countValues[$desc_med]=='' ? 0 : $countValues[$desc_med]);
								$countValues_max=($countValues[$desc_max]=='' ? 0 : $countValues[$desc_max]);
								
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && $countValues_min < $cmps[$PROMO_ID]['available'])
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;									
									
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
									$arrpromd3[$PROMO_ID][]=$desc_min;
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && $countValues_max < $cmps[$PROMO_ID]['available'])
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
									$arrpromd3[$PROMO_ID][]=$desc_max;
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']  && $countValues_med < $cmps[$PROMO_ID]['available'])
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;
									
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
									$arrpromd3[$PROMO_ID][]=$desc_med;
								}
								$i++;
							}
						}
						elseif($cmp_tipo==7) // PROMOCAO DOS 2 DESCONTOS
						{
							// REGRAS
							// O MAIOR VALOR - 25 %
							// O MENOR VALOR - 15 %
							$preco_max=max($arr_prodp_d2);
							$preco_min=min($arr_prodp_d2);						
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d2, $preco_min, $preco_max, 2);
							$desc_max=max($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS2']);
							$desc_min=min($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS2']);
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							if($qtd_totald2 == 1)
							{
								$i=0;
								while($i<$qtd_total)
								{
									if($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_max, $arrpromd2[$PROMO_ID]))
									{
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
										$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
										$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
										$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
										$arrpromd2[$PROMO_ID][]=$desc_max;											
									}
									elseif($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_min, $arrpromd2[$PROMO_ID]))
									{
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
										$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
										$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
										$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
										$arrpromd2[$PROMO_ID][]=$desc_min;
									}
									$i++;
								}
							}
							else
							{
								$i=0;
								if($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_max, $arrpromd2[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
									$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
									$arrpromd2[$PROMO_ID][]=$desc_max;
								}
								elseif($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_min, $arrpromd2[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
									$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
									$arrpromd2[$PROMO_ID][]=$desc_min;
								}
							}
						}
						elseif($cmp_tipo==3 || $cmp_tipo==4 || $cmp_tipo==5)
						{
							 $cmps_offer=getSalesPromotionsOffers($cmps[$PROMO_ID][$PROD_ID]['PROMO_ID'], $cmp_tipo, $cmps[$PROMO_ID][$PROD_ID]['DESIGNACAO']);
							 $cmps['OFERTAS'][$PROMO_ID]=$cmps_offer;
						}
						elseif($cmp_tipo==6) // PROMOCAO 2=3
						{
							// REGRAS
							// O MAIOR VALOR - 0 %
							// O MENOR VALOR - 100 %
							// O MÉDIO VALOR - 0 %
							$cmps_ds=array();
							$preco_max=max($arr_prodp_d4[$PROMO_ID]);
							$preco_min=min($arr_prodp_d4[$PROMO_ID]);						
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d4[$PROMO_ID], $preco_min, $preco_max);
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d4[$PROMO_ID], $preco_min, $preco_max, 2);
							$desc_max=100;
							$desc_min=0;
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=0;
							$i=0;
							while($i<$qtd_total)
							{								
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_med, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_med, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;									
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;
									$arrprom[$PROMO_ID][]=$desc_med;
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_max, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;	
									$arrprom[$PROMO_ID][]=$desc_max;
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_min, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;	
									$arrprom[$PROMO_ID][]=$desc_min;
								}
								$i++;
							}
						}
						else
						{
							$qtd_prd_limite[$PROMO_ID][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'];
							if(!isset($qtd_prd_rest[$PROMO_ID])) $qtd_prd_rest[$PROMO_ID]=$info['MINIMO'];
							if($prom_tipo==4 && array_sum($qtd_prd_limite[$PROMO_ID]) > $info['MINIMO'])
							{
								if($qtd_prd_rest[$PROMO_ID] > 0)
								{
									while($qtd_prd_rest[$PROMO_ID] > 0)
									{
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $qtd_prd_rest[$PROMO_ID]) - (($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $qtd_prd_rest[$PROMO_ID])); 
										$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $qtd_prd_rest[$PROMO_ID]));
										$qtd_prd_rest[$PROMO_ID]-=$qtd_prd_rest[$PROMO_ID];
									}
								}
							}
							else
							{
								$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']) - (($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'])); 
								$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']));
								$qtd_prd_rest[$PROMO_ID] -= $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']; // QUANTAS CAMPANHAS AINDA FALTAM
							}
						}
					}
				}		
			}
			elseif($prom_tipo==2 && $passa==1) // INT. VALORES
			{
				if($valor_acumulado >= $info['MINIMO'] ) // ESTA NO INTERVALO DEFINIDO && $valor_acumulado <= $info['MAXIMO']
				{
					if($info['TIPO'] == 0) // PERCENTAGEM
					{
						if($cmp_tipo==2) // PROMOCAO DOS 3 DESCONTOS
						{
							// REGRAS							
							// O MENOR VALOR - 25 %
							// O MÉDIO VALOR - 50 %
							// O MAIOR VALOR - 75 %
							$preco_max=max($arr_prodp_d3);
							$preco_min=min($arr_prodp_d3);
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d3, $preco_min, $preco_max);
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d3, $preco_min, $preco_max, 2);
							$desc_max=max($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							$desc_min=min($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=includedDesconto($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3'], $des_min, $desc_max, 1);
							$i=0;
							while($i<$qtd_total)
							{							
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];									
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_med, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;									
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								$i++;
							}
						}
						elseif($cmp_tipo==7) // PROMOCAO DOS 2 DESCONTOS
						{
							// REGRAS
							// O MAIOR VALOR - 25 %
							// O MENOR VALOR - 15 %							
							$preco_max=max($arr_prodp_d2);
							$preco_min=min($arr_prodp_d2);
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d2, $preco_min, $preco_max, 2);
							$desc_max=max($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS2']);
							$desc_min=min($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS2']);
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$i=0;
							while($i<$qtd_total)
							{							
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOSd2']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOSd2']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOSd2'][]=$desc_min;									
									$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOSd2']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOSd2']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;									
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOSd2'][]=$desc_max;
									$cmps[$PROMO_ID]['PRECOSd2'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];									
								}
								$i++;
							}
						}
						elseif($cmp_tipo==3 || $cmp_tipo==4 || $cmp_tipo==5)
						{
							 $cmps_offer=getSalesPromotionsOffers($cmps[$PROMO_ID][$PROD_ID]['PROMO_ID'],$cmp_tipo,$cmps[$PROMO_ID][$PROD_ID]['DESIGNACAO']);
							 $cmps['OFERTAS'][$PROMO_ID]=$cmps_offer;
						}
						elseif($cmp_tipo==6) // PROMOCAO 2=3
						{
							// REGRAS
							// O MAIOR VALOR - 0 %
							// O MENOR VALOR - 100 %
							// O MÉDIO VALOR - 0 %
							$preco_max=max($arr_prodp_d4[$PROMO_ID]);
							$preco_min=min($arr_prodp_d4[$PROMO_ID]);
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d4[$PROMO_ID],$preco_min,$preco_max);
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d4[$PROMO_ID],$preco_min,$preco_max,2);
							$desc_max=100;
							$desc_min=0;
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=0;
							$i=0;
							while($i<$qtd_total)
							{							
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_med, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
								}
								$i++;
							}
						}
						else
						{							
							$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']) - (($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'])); 
							$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']));
						}
					}
				}								
			}
			elseif($prom_tipo==3 && $passa==1) // VALOR ACUMULADO NO CARRINHO LIQUIDO
			{			
				if($total_carrinho >= $info['MINIMO']) // ESTA NO INTERVALO DEFINIDO // && $total_carrinho <= $info['MAXIMO']
				{
					if($info['TIPO'] == 0) // PERCENTAGEM
					{
						if($cmp_tipo==2) // PROMOCAO DOS 3 DESCONTOS
						{
							// REGRAS							
							// O MENOR VALOR - 25 %
							// O MÉDIO VALOR - 50 %
							// O MAIOR VALOR - 75 %							
							$preco_max=max($arr_prodp_d3);
							$preco_min=min($arr_prodp_d3);							
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d3,$preco_min,$preco_max);							
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d3,$preco_min,$preco_max,2);
							$desc_max=max($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							$desc_min=min($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3']);
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=includedDesconto($cmps[$PROMO_ID][$PROD_ID]['DESCONTODOS3'],$des_min,$desc_max,1);
							$i=0;
							while($i<$qtd_total)
							{							
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] && !in_array($desc_med, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'],$cmps[$PROMO_ID]['PRECOS']))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;
									$cmps[$PROMO_ID]['PRECOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'];
								}
								$i++;
							}
						}
						elseif($cmp_tipo==3 || $cmp_tipo==4 || $cmp_tipo==5)
						{
							 $cmps_offer=getSalesPromotionsOffers($cmps[$PROMO_ID][$PROD_ID]['PROMO_ID'], $cmp_tipo);
							 $cmps['OFERTAS'][$PROMO_ID]=$cmps_offer;
						}
						elseif($cmp_tipo==6) // PROMOCAO 2=3
						{
							// REGRAS
							// O MAIOR VALOR - 0 %
							// O MENOR VALOR - 100 %
							// O MÉDIO VALOR - 0 %							
							$preco_max=max($arr_prodp_d4[$PROMO_ID]);
							$preco_min=min($arr_prodp_d4[$PROMO_ID]);							
							// DEVOLVE O VALOR ENTRE O MINIMO E MAXIMO
							$preco_med=includedValues($arr_prodp_d4[$PROMO_ID],$preco_min,$preco_max);
							// DEVOLVE TODOS OS VALORES SEM ESTAREM REPETIDOS
							$valores_arr=includedValues($arr_prodp_d4[$PROMO_ID],$preco_min,$preco_max,2);							
							$desc_max=100;
							$desc_min=0;							
							// DEVOLVE O DESCONTO ENTRE O MINIMO E MAXIMO
							$desc_med=0;								
							$i=0;
							while($i<$qtd_total)
							{							
								if($preco_max==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] && !in_array($desc_med, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_med, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_med;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_med / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_med;
									$arrprom[$PROMO_ID][]=$desc_med;
								}
								elseif($preco_min==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] && !in_array($desc_max, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_max, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);	
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_max;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_max / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_max;
									$arrprom[$PROMO_ID][]=$desc_max;
								}
								elseif($preco_med==$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] && !in_array($desc_min, $cmps[$PROMO_ID][$PROD_ID]['DESCONTOS']) && !in_array($desc_min, $arrprom[$PROMO_ID]))
								{
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTO']=$desc_min;
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVOS'][]=$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'] - (($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']); 
									$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADOS'][]=(($desc_min / 100) * $cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']);
									$cmps[$PROMO_ID][$PROD_ID]['DESCONTOS'][]=$desc_min;
									$arrprom[$PROMO_ID][]=$desc_min;
								}
								$i++;
							}
						}
						else
						{						
							$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_NOVO']=($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']) - (($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD'])); 
							$cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO_RETIRADO']=(($cmps[$PROMO_ID][$PROD_ID]['DESCONTO'] / 100) * ($cmps[$PROMO_ID][$PROD_ID]['PROD_PRECO'] * $cmps[$PROMO_ID][$PROD_ID]['PROD_QTD']));
						}
					}
				}
			}
		}
	}	
	if(is_array($cmps['OFERTAS'][$PROMO_ID])) return array_merge($cmps, $cmps['OFERTAS']);
	else return $cmps;
}
function showSalesPromotions($cmps,$PROD_ID,$valor_antigo=0,$finaliza=0)
{					
	$res=getArrays($PROD_ID, $cmps);
	$i=0;
	$res_pos='';
	$res_pos_arr=array();
	$valor_total_desconto=0; 
	$p_ret_tem=0; 
	while($i<count($res))
	{
		$p=$res[$i]['PROD_PRECO']; // PRECO PRODUTO
		$p_new=$res[$i]['PROD_PRECO_NOVO']; // PRECO DO PRODUTO COM DESCONTO
		$p_ret=$res[$i]['PROD_PRECO_RETIRADO']; // PRECO DO PRODUTO COM DESCONTO
		$p_ret_tem=(isset($res[($i+1)]['PROD_PRECO_RETIRADO']) && $res[($i+1)]['PROD_PRECO_RETIRADO'] > 0 ? 1 : 0); // PRECO DO PRODUTO COM DESCONTO
		$tipo=$res[$i]['TIPO']; // TIPO - PERCENTAGEM | VALOR
		$desconto=$res[$i]['DESCONTO'];
		$descontos=$res[$i]['DESCONTOS'];
		$descontoNome=$res[$i]['DESIGNACAO'];
		$promo_id=$res[$i]['PROMO_ID'];
		$MAXIMO=$res[$i]['MAXIMO'];
		$DATAFIMCMP=$res[$i]['DATAFIMCMP'];
		$N_PROMS=$res[$i]['N_PROMOS'];
		if(is_array($descontos))
		{
			$a=0; $valor_ret=0;
			while($a<count($descontos))
			{
				$p_new=$res[$i]['PROD_PRECO_NOVOS'][$a]; // PRECO DO PRODUTO COM DESCONTO
				$p_ret=$res[$i]['PROD_PRECO_RETIRADOS'][$a]; // PRECO DO PRODUTO COM DESCONTO
				if($p!=$p_new) // OS VALORES SÃO DIFERENTES, GANHOU DESCONTO
				{					
					if($tipo == 0)
					{ 
						$desc_v_f=numberformat($descontos[$a], 1, 1);
						$p_desconto=($p - $p_new);
					}
					else
					{ 
						$desc_v_f=numberformat($descontos[$a], 1, 0);
						$p_desconto=($p - $p_new);
					}
					$valor_total_desconto += $descontos[$a];	
					$valor_antigo -= $p_ret;					
					
				}
				else  // AINDA NÃO GANHOU DESCONTO
				{	
					$p_desconto='';
				}
				if($p_desconto)
				{				
					$res_pos .= '<div class="product2" style="cursor:default;border-bottom: 2px solid #c0c0c0;'.($p_ret_tem == 0 ? 'margin-bottom:20px' : 'margin-bottom:10px').'">
					<div class="product-image">&nbsp;</div>
					<div class="product-details">
					<div class="product-title">';
					$res_pos .= $descontoNome.'-'.$desc_v_f.'('.numberformat($p).')';
					$res_pos .= '</div>
					</div>
					<div class="product-price">-'.numberformat($p_ret).'</div>
					<div class="product-quantity">&nbsp;</div>
					<div class="product-removal">&nbsp;</div>
					<div class="product-line-price">'.($p_ret_tem == 1 ? '<strike>'.numberformat($valor_antigo).'</strike>' : numberformat($valor_antigo)).'</div>
					</div>';
					$res_pos_arr[]=array('DESC'=>$descontoNome.'-'.$desc_v_f.'('.numberformat($p).')', 'VALOR_RETIRADO'=>$p_ret);
					if(!in_array($DATAFIMCMP, $DATAFIMCMP_arr)) $DATAFIMCMP_arr[]=$DATAFIMCMP;
					if(!in_array($promo_id, $ID_arr)) $ID_arr[]=$promo_id;
				}
				$a++;
			}
			sales_promotions_reserves($promo_id,$MAXIMO,$finaliza);
		}
		else
		{
			if($p!=$p_new) // OS VALORES SÃO DIFERENTES, GANHOU DESCONTO
			{
				if($tipo == 0)
				{ 
					$desc_v_f=numberformat($desconto, 1, 1);
					$p_desconto=($p - $p_new);
				}
				else
				{ 
					$desc_v_f=numberformat($desconto, 1, 0);
					$p_desconto=($p - $p_new);
				}
				$valor_total_desconto += $desconto;	
				$valor_antigo -= $p_ret;

			}
			else  // AINDA NÃO GANHOU DESCONTO
			{	
				$p_desconto='';
			}
			if($p_desconto)
			{
				$res_pos .= '<div class="product2" style="cursor:default;border-bottom: 2px solid #c0c0c0;'.($p_ret_tem == 0 ? 'margin-bottom:20px' : 'margin-bottom:10px').'">
				<div class="product-image">&nbsp;</div>
				<div class="product-details">
				<div class="product-title">';
				$res_pos .= $descontoNome.'-'.$desc_v_f;
				$res_pos .= '</div>
				</div>
				<div class="product-price">-'.numberformat($p_ret).'</div>
				<div class="product-quantity">&nbsp;</div>
				<div class="product-removal">&nbsp;</div>
				<div class="product-line-price">'.($p_ret_tem == 1 ? '<strike>'.numberformat($valor_antigo).'</strike>' : numberformat($valor_antigo)).'</div>
				</div>';
				$res_pos_arr[]=array('DESC'=>$descontoNome.'-'.$desc_v_f, 'VALOR_RETIRADO'=>$p_ret);
				if(!in_array($DATAFIMCMP, $DATAFIMCMP_arr)) $DATAFIMCMP_arr[]=$DATAFIMCMP;
				if(!in_array($promo_id, $ID_arr)) $ID_arr[]=$promo_id;
				sales_promotions_reserves($promo_id, $MAXIMO, $finaliza);
			}
		}
		$i++;
	}
	return array('p_html'=>$res_pos, 'p_desc_arr'=>$res_pos_arr, 'p_valor'=>$valor_antigo, 'p_desconto'=>$valor_total_desconto, 'DATAFIMCMP'=>$DATAFIMCMP_arr, 'IDARR'=>$ID_arr);
}
function showSalesPromotions_offer($cmps,$finaliza=0)
{
	$offersa=$cmps['OFERTAS'];	
	$offersb=getSalesPromotionsOffersAcm();
	if(is_array($offersb) && is_array($offersa)) $offers=array_merge($offersa, $offersb);
	elseif(is_array($offersb)) $offers=$offersb;
	elseif(is_array($offersa))  $offers=$offersa;
	$res_pos_arr=array();
	$i=0; $res_pos=''; $valor_total_desconto=0; 
	if(isset($offers) && count($offers))
	{
		foreach($offers as $PROMO_ID=>$PROMO_ARR)
		{			
			$passa=sales_promotions_reserves_verify($PROMO_ARR['PROMO_ID'], 0, 0, 1);
			if($passa == 1)
			{				
				foreach($PROMO_ARR as $TIPOCAMPANHA=>$PRODS_OFERTA)
				{					
					$total=count($PRODS_OFERTA); $height='';
					$height='max-height:200px;';
					if($TIPOCAMPANHA==3)
					{
						$res_pos .= '<div class="product2" style="cursor:default;border-bottom: none;">						
						<div class="product-details">
						<div class="product-title" style="text-transform:uppercase">';
						$res_pos .= '<b>'.$PROMO_ARR['DESC'].'</b>';
						$res_pos .= '</div>						
						</div>
						</div>';
					}
					elseif($TIPOCAMPANHA==4)
					{
						$res_pos .= '<div class="product2" style="cursor:default;border-bottom: none;">						
						<div class="product-details">
						<div class="product-title"  style="text-transform:uppercase">';
						$res_pos .= '<b>'.$PROMO_ARR['DESC'].' </b>';
						$res_pos .= '</div>						
						</div>
						</div>';
					}

					foreach($PRODS_OFERTA as $PRODUTO_ID=>$PRODUTO)
					{				
						$key=$PRODUTO_ID;
						if($TIPOCAMPANHA == 3)
						{
							$res_pos .= '<div class="product2" style="cursor:default;border-bottom: 2px solid #c0c0c0;">
							<div class="product-image"><label class="lbl_check2" for="oferta'.$PROMO_ID.'_'.$key.'" style="width:20px !important;margin:0 auto !important"><input id="oferta'.$PROMO_ID.'_'.$key.'" type="checkbox" checked="checked" value="'.$key.'" name="ofertas'.$PROMO_ID.'_'.$key.'" /></label></div>
							<div class="product-details">
							<div class="product-title">';
							$res_pos .= $PRODUTO;
							$res_pos .= '</div>
							</div>
							<div class="product-price">&nbsp;</div>
							<div class="product-quantity">1</div>
							<div class="product-removal">&nbsp;</div>
							<div class="product-line-price">OFERTA</div>
							</div>';
							$res_pos_arr[]=array('DESC'=>$PROMO_ARR['DESC'].' - '.$PRODUTO, 'VALOR_RETIRADO'=>0, 'campo_escolha'=>'oferta'.$PROMO_ID.'_'.$key, 'escolha'=>0);
						}
						elseif($TIPOCAMPANHA == 4)
						{
							
							$res_pos .= '<div class="product2" style="cursor:default;border-bottom: 2px solid #c0c0c0;'.($p_ret_tem == 0 ? 'margin-bottom:20px' : 'margin-bottom:10px').'">
							<div class="product-image"><label class="lbl_radio" for="mofertas'.$PROMO_ID.'_'.$key.'" style="width:20px !important;height:20px !important;margin:0 auto"><input id="mofertas'.$PROMO_ID.'_'.$key.'" type="radio" value="'.$key.'" class="cmp_sel" name="mofertas'.$PROMO_ID.'" /></label></div>
							<div class="product-details">
							<div class="product-title">';
							$res_pos .= $PRODUTO;
							$res_pos .= '</div>
							</div>
							<div class="product-price">&nbsp;</div>
							<div class="product-quantity">1</div>
							<div class="product-removal">&nbsp;</div>
							<div class="product-line-price">OFERTA</div>
							</div>';
							$res_pos_arr[]=array('DESC'=>$PROMO_ARR['DESC'].' - '.$PRODUTO, 'VALOR_RETIRADO'=>0, 'campo_escolha'=>'mofertas'.$PROMO_ID, 'escolha'=>$key);
						}
					}
				}
				if(!in_array($PROMO_ARR['DATAFIMCMP'], $DATAFIMCMP_arr)) $DATAFIMCMP_arr[]=$PROMO_ARR['DTAFIM'];
				if(!in_array($PROMO_ARR['PROMO_ID'], $ID_arr)) $ID_arr[]=$PROMO_ARR['PROMO_ID'];
				sales_promotions_reserves($PROMO_ID, 0, $finaliza);
				$res_pos .= '<div class="product2" style="cursor:default;border-bottom:none;margin-bottom:10px">
				<div class="product-image"></div>
				<div class="product-details">
				<div class="product-title"></div></div></div>';
			}
		}
	}
	return array('p_html_offers'=>$res_pos,'p_html_offers_arr'=>$res_pos_arr,'p_data_limite'=>$DATAFIMCMP_arr,'IDARR'=>$ID_arr);
}
function getArrays($needle,$haystack) 
{
	$in_multi_array=array();
	foreach ($haystack as $key=>$val) 
	{
		if($key==$needle) $in_multi_array[]=$val;
		foreach ($val as $key2=>$val2) 
		{
			if($key2==$needle) $in_multi_array[]=$val2;
		}
	}
	return $in_multi_array;
} 
function reIndexArray($arr,$startAt=0)
{
    return ( 0 == $startAt ) ? array_values( $arr ) : array_combine( range( $startAt, count( $arr ) + ( $startAt - 1 ) ), array_values( $arr ) );
}
function cart_info($id_prod=0)
{
	global $n_enc, $enc, $shop;	
	$total=0;
	if($n_enc > 0 && $id_prod==0)
	{
		$i=1;	
		while($i<=$n_enc)
		{
			$prod_id=$shop[$i][1];
			$prod_qtd=$shop[$i][2];
			$prod_obj=$shop[$i][5];
			$info=get_produtc_info($prod_id,$prod_obj);
			$valorqtd=$info['prod_precos']['PVP'];
			$total += ($valorqtd * $prod_qtd);
			$prods[]=$prod_id;			
			$prodsi[$prod_id]=array('prod'=>$prod_id, 'qtd'=>$prod_qtd, 'precop'=>($valorqtd * $prod_qtd), 'precoprod'=>$total);
			$i++;
		}
		return array('cartprods'=>$prods, 'cartprodsi'=>$prodsi, 'totalcarrinho'=>$total);		
	}
	else
	{
		while($i<=$n_enc)
		{
			$prod_id=$shop[$i][1];
			$prod_qtd=$shop[$i][2];
			$prod_obj=$shop[$i][5];
			$info=get_produtc_info($prod_id,$prod_obj);
			$valorqtd=$info['prod_precos']['PVP'];
			if($id_prod==$prod_id) return array('preco'=>$valorqtd,'qtd'=>$prod_qtd);
			$i++;
		}
	}
}
if(substr_count($_SERVER['REQUEST_URI'], 'carrinho') || substr_count($_SERVER['REQUEST_URI'], 'do_shopping_cart'))
{ 
	$proms=getSalesPromotions();
}
?>