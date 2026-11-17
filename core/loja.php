<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

if(defined('CARRINHO_CONF')) $carrinho_conf=unserialize(CARRINHO_CONF); else $carrinho_conf=array();
global $n_enc;
if(!isset($_SESSION))@session_start();
if(!$n_enc) { $n_enc=0; $shop=array(); }
if(isset($_GET['prod_add'])) { $prod_id=intval($_GET['prod_add']);}
if(isset($_GET['prod_add_obj'])) {  $prod_obj_id=intval($_GET['prod_add_obj']); }
elseif(isset($_GET['prod_obj_id'])) { $prod_obj_id=intval($_GET['prod_obj_id']); } // compatibilidade com links antigos da area de cliente
$prod_obj_opces='';
if(isset($_GET['prod_add_opcoes'])) { $prod_obj_opces=clean_var($_GET['prod_add_opcoes']);}
if(isset($_GET['prod_garantia_add'])) $prod_garantia_id=clean_var($_GET['prod_garantia_add']);
if(isset($_GET['prod_servicos_extra_add'])) $prod_servicos_extra_id=clean_var($_GET['prod_servicos_extra_add']);
if(isset($_GET['prod_porte_add'])) $prod_porte_id=clean_var($_GET['prod_porte_add']);
if(isset($_GET['prod_combo_id'])) { $prod_combo_id=clean_var($_GET['prod_combo_id']);}
if(isset($_GET['prod_serv'])) { $prod_serv=clean_var($_GET['prod_serv']);}
if(isset($_GET['prod_quant_id'])) { $prod_quant_id=clean_var($_GET['prod_quant_id']);}
if(isset($_GET['tempo_escolhido'])) { $tempo_escolhido=clean_var($_GET['tempo_escolhido']);}
if(isset($_GET['largura'])) { $largura=clean_var($_GET['largura']);}
if(isset($_GET['altura'])) { $altura=clean_var($_GET['altura']);}
if(isset($_GET['tempo_realizacao'])) { $data_realizacao=$_GET['tempo_realizacao'];}
if(isset($_GET['fichei'])) { $nome_ficheiro=$_GET['fichei'];}
elseif(isset($_GET['pos']) && isset($shop)) {$prod_obj_opces=$shop[intval($_GET['pos'])][6];}
if(isset($_GET['prod_add_qtd'])) {$prod_add_qtd=intval($_GET['prod_add_qtd']);} else {$prod_add_qtd=1;}
save_endereco();
if(!isset($_SESSION['CLIENTE_ID_TEMP'])) { $_SESSION['CLIENTE_ID_TEMP']=date('YmdHis').uniqid();}
if($GLOBALS['module_voucher'] == 1)
{
	if(!isset($n_voucher) || !$n_voucher)
	{
		$n_voucher=0;
		$voucher=array();
	}
	if(isset($_GET['voucher_add']))
	{
		$voucher_code=htmlentities($_GET['voucher_add']);
	}
	if (isset($_POST['voucher_add']))
    {
        $voucher_code=htmlentities($_POST['voucher_add']);
    }
}
if(!isset($d) || !$d){$d=uniqid('');} // vou colocar esta info aqui porque existem situações que nao vem configurado,esta variavel representa a chave seguranca do artigo na encomenda


//VARIAVEIS PASSADAS POR POST
if(isset($_POST['m_metodoenvio'])) { $_SESSION['METODO_ENVIO']=clean_var($_POST['m_metodoenvio']);}
if(isset($_POST['m_metodopagamento'])) { $_SESSION['METODO_PAGAMENTO']=clean_var($_POST['m_metodopagamento']);}
if(isset($_GET['m_metodoenvio'])) { $_SESSION['METODO_ENVIO']=clean_var($_GET['m_metodoenvio']);}
if(isset($_GET['m_metodopagamento'])) { $_SESSION['METODO_PAGAMENTO']=clean_var($_GET['m_metodopagamento']);}


//*********************************************************
//Veio do carrinho e nao sabemos qual e o id do artigo,apenas a posicao no array enc
if(!isset($prod_id) && isset($_GET['num']) && intval($_GET['num']) > 0)
{
	$prod_id = $shop[intval($_GET['num'])][1];
	$found=intval($_GET['num']);
}
//*********************************************************
//Vamos adicionar o artigo a encomenda, devemos ordenar sempre os artigos de forma a que o de menor valor seja o primeiro
//Ou o que tiver menor custo logistico para ser mais facil
if(isset($_GET['prod_add']) && $prod_id > 0 && $prod_obj_id > 0)
{
	$verifica=verifica_stock($prod_id); // se for um produto,vamos ver se tem stock
	$qtd_enc_default=verifica_qtd($prod_id,$prod_add_qtd); // se for um produto,vamos ver se tem stock

	if($verifica == 1)//tem stock
	{
		//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)$custo_logistico=get_prod_custo_logistico($prod_id);

		$i=1;
		$found=0;
		while ($i <= $n_enc && $found == 0)
		{
			if($prod_obj_opces != '')
			{
				if($shop[$i][1] == $prod_id && $shop[$i][5] == $prod_obj_id && $shop[$i][6] == $prod_obj_opces)$found=$i;
			}
			else
			{
				if($shop[$i][1] == $prod_id && $shop[$i][5] == $prod_obj_id)$found=$i;
			}
			$i++;
		}

		$available_stock=get_stock($prod_id); // se for um produto,vamos ver se tem stock

		if(!$found)
		{
			if($prod_add_qtd <= 0) {   $prod_add_qtd=1; }
			$n_enc++; // mais uma linha de encomenda
			$shop[$n_enc][1]=$prod_id; //codigo do produto

			if((isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_enc_sem_stock'] == 1))
			{
				$shop[$n_enc][2] = $qtd_enc_default;
			}
			else
			{
				if($qtd_enc_default <= $available_stock) $shop[$n_enc][2] = $qtd_enc_default;
				else $shop[$n_enc][2] = $available_stock;
			}


			$shop[$n_enc][3]=1; // tipo de produto ( 1=produtos 2=Club)
			$shop[$n_enc][4]=$d; // chave seguranca
			$shop[$n_enc][5]=$prod_obj_id; // chave seguranca
			$shop[$n_enc][6]=$prod_obj_opces; // chave seguranca
			if($prod_combo_id > 0) $shop[$n_enc][7]=$prod_combo_id;
			if($prod_garantia_id > 0) $shop[$n_enc][8]=$prod_garantia_id;	else $shop[$n_enc][8]=0;
			if($prod_porte_id > 0) $shop[$n_enc][9]=$prod_porte_id;	else $shop[$n_enc][9]=0;
			if($servicos_extra_id > 0) $shop[$n_enc][10]=$servicos_extra_id; else $shop[$n_enc][10]=0;
			if($prod_serv!= '') $shop[$n_enc][12]=$prod_serv;
			if($prod_quant_id > 0) $shop[$n_enc][13]=$prod_quant_id;
			if($tempo_escolhido > 0) $shop[$n_enc][14]=$tempo_escolhido;
			if($altura > 0) $shop[$n_enc][15]=$altura;
			if($largura > 0) $shop[$n_enc][16]=$largura;
			if($data_realizacao!='') $shop[$n_enc][17]=$data_realizacao;
			if($nome_ficheiro!='') $shop[$n_enc][18]=$nome_ficheiro;
			$shop[$n_enc][19]=$custo_logistico;

			$found=$n_enc;
		}
		else
		{
			$shop[$found][1]=$prod_id; //codigo do produto
			if(($shop[$found][2] + $qtd_enc_default) > 0)
			{
				$shop[$found][1]=$prod_id; //codigo do produto

				if((isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_enc_sem_stock'] == 1))
				{
					$shop[$found][2]+= $qtd_enc_default;
				}
				else
				{
					if($shop[$found][2]+$qtd_enc_default <= $available_stock) $shop[$found][2]+= $qtd_enc_default;
					else $shop[$found][2] = $available_stock;
				}
			}
			if($shop[$found][2] <= 0)  $shop[$found][2]=1;

			$shop[$found][3]=1; // tipo de produto ( 1=produtos 2=Club)
			$shop[$found][4]=$d; // chave seguranca
			$shop[$found][5]=$prod_obj_id; // chave seguranca
			$shop[$found][6]=$prod_obj_opces; // chave seguranca
			if($prod_combo_id > 0)  $shop[$found][7]=$prod_combo_id;
			if($prod_garantia_id > 0) $shop[$found][8]=$prod_garantia_id;	else $shop[$found][8]=0;
			if($prod_porte_id > 0) $shop[$found][9]=$prod_porte_id; else $shop[$found][9]=0;
			if($servicos_extra_id > 0) $shop[$found][10]=$servicos_extra_id; else $shop[$found][10]=0;
			if($prod_serv!= '') $shop[$found][12]=$prod_serv;
			if($prod_quant_id > 0) $shop[$found][13]=$prod_quant_id;
			if($tempo_escolhido > 0) $shop[$found][14]=$tempo_escolhido;
			if($altura > 0) $shop[$found][15]=$altura;
			if($largura > 0) $shop[$found][16]=$largura;
			if($data_realizacao!='') $shop[$found][17]=$data_realizacao;
			if($nome_ficheiro!='') $shop[$found][18]=$nome_ficheiro;
			$shop[$found][19]=$custo_logistico;
		}




	}
	else
	{
		$erro=1;
	}

}


//Agora vou refazer o carrinho para limpar algum lixo e ao mesmo tempo reordenar pela ordem correta:
$shop_tmp=array();
$i=1;
$j=0;
$menor_valor=0;
while ($i <= $n_enc)
{
	if($shop[$i][1] > 0)
	{
		$j++;

		if($i>1)
		{
			if($shop[$i][19] > $menor_valor)
			{
				$menor_valor = $shop[$i][19];
				//Se este valor é o menor, vamos colocar em primeiro lugar
				$shop_tmp[$j][1]  = $shop_tmp[1][1];
				$shop_tmp[$j][2]  = $shop_tmp[1][2];
				$shop_tmp[$j][3]  = $shop_tmp[1][3];
				$shop_tmp[$j][4]  = $shop_tmp[1][4];
				$shop_tmp[$j][5]  = $shop_tmp[1][5];
				$shop_tmp[$j][6]  = $shop_tmp[1][6];
				$shop_tmp[$j][7]  = $shop_tmp[1][7];
				$shop_tmp[$j][8]  = $shop_tmp[1][8];
				$shop_tmp[$j][9]  = $shop_tmp[1][9];
				$shop_tmp[$j][10] = $shop_tmp[1][10];
				$shop_tmp[$j][12] = $shop_tmp[1][12];
				$shop_tmp[$j][13] = $shop_tmp[1][13];
				$shop_tmp[$j][14] = $shop_tmp[1][14];
				$shop_tmp[$j][15] = $shop_tmp[1][15];
				$shop_tmp[$j][16] = $shop_tmp[1][16];
				$shop_tmp[$j][17] = $shop_tmp[1][17];
				$shop_tmp[$j][18] = $shop_tmp[1][18];
				$shop_tmp[$j][19] = $shop_tmp[1][19];

				$shop_tmp[1][1]  = $shop[$i][1];
				$shop_tmp[1][2]  = $shop[$i][2];
				$shop_tmp[1][3]  = $shop[$i][3];
				$shop_tmp[1][4]  = $shop[$i][4];
				$shop_tmp[1][5]  = $shop[$i][5];
				$shop_tmp[1][6]  = $shop[$i][6];
				$shop_tmp[1][7]  = $shop[$i][7];
				$shop_tmp[1][8]  = $shop[$i][8];
				$shop_tmp[1][9]  = $shop[$i][9];
				$shop_tmp[1][10] = $shop[$i][10];
				$shop_tmp[1][12] = $shop[$i][12];
				$shop_tmp[1][13] = $shop[$i][13];
				$shop_tmp[1][14] = $shop[$i][14];
				$shop_tmp[1][15] = $shop[$i][15];
				$shop_tmp[1][16] = $shop[$i][16];
				$shop_tmp[1][17] = $shop[$i][17];
				$shop_tmp[1][18] = $shop[$i][18];
				$shop_tmp[1][19] = $shop[$i][19];//**
			}
			else
			{
				$shop_tmp[$j][1]  = $shop[$i][1];
				$shop_tmp[$j][2]  = $shop[$i][2];
				$shop_tmp[$j][3]  = $shop[$i][3];
				$shop_tmp[$j][4]  = $shop[$i][4];
				$shop_tmp[$j][5]  = $shop[$i][5];
				$shop_tmp[$j][6]  = $shop[$i][6];
				$shop_tmp[$j][7]  = $shop[$i][7];
				$shop_tmp[$j][8]  = $shop[$i][8];
				$shop_tmp[$j][9]  = $shop[$i][9];
				$shop_tmp[$j][10] = $shop[$i][10];
				$shop_tmp[$j][12] = $shop[$i][12];
				$shop_tmp[$j][13] = $shop[$i][13];
				$shop_tmp[$j][14] = $shop[$i][14];
				$shop_tmp[$j][15] = $shop[$i][15];
				$shop_tmp[$j][16] = $shop[$i][16];
				$shop_tmp[$j][17] = $shop[$i][17];
				$shop_tmp[$j][18] = $shop[$i][18];
				$shop_tmp[$j][19] = $shop[$i][19];//**
			}

		}
		else
		{
			$menor_valor = $shop[$i][19];
			$shop_tmp[1][1]  = $shop[$i][1];
			$shop_tmp[1][2]  = $shop[$i][2];
			$shop_tmp[1][3]  = $shop[$i][3];
			$shop_tmp[1][4]  = $shop[$i][4];
			$shop_tmp[1][5]  = $shop[$i][5];
			$shop_tmp[1][6]  = $shop[$i][6];
			$shop_tmp[1][7]  = $shop[$i][7];
			$shop_tmp[1][8]  = $shop[$i][8];
			$shop_tmp[1][9]  = $shop[$i][9];
			$shop_tmp[1][10] = $shop[$i][10];
			$shop_tmp[1][12] = $shop[$i][12];
			$shop_tmp[1][13] = $shop[$i][13];
			$shop_tmp[1][14] = $shop[$i][14];
			$shop_tmp[1][15] = $shop[$i][15];
			$shop_tmp[1][16] = $shop[$i][16];
			$shop_tmp[1][17] = $shop[$i][17];
			$shop_tmp[1][18] = $shop[$i][18];
			$shop_tmp[1][19] = $shop[$i][19];//**
		}


	}
	$i++;
}
$n_enc=$j;
$shop=$shop_tmp;
//***************************************************************




//Caso tenhamos mudado a extensão de garantia,vamos atualizar o produto na encomenda
//tem_extensao_garantia
if(isset($prod_id) && $prod_id > 0 && isset($prod_garantia_id) && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1))
{
	if($prod_garantia_id > 0)
	{
		$info=get_produtc_info($prod_id);
		if(isset($info['fam_id']))
		{
			$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
			if(!empty($garantia) && count($garantia) > 0)
			{
				for($a=0; $a<count($garantia); $a++)
				{
					if($prod_garantia_id == $garantia[$a]['id']) $shop[$found][8]=$garantia[$a]['id'];
				}
			}
		}
	}
	else
	{
		$shop[$found][8]='';
	}
}


//Caso tenhamos mudado os portes,vamos atualizar o produto na encomenda
//tem_multiportes
if(isset($prod_id) && $prod_id > 0 && isset($prod_porte_id) && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1))
{
	if($prod_porte_id > 0)
	{
		$info=get_produtc_info($prod_id);
		if(isset($info['fam_id']))
		{
		    $porte=multi_portes_prod($info['prod_id'],$info['prod_precos']['PVP'],$info['prod_peso']);
			if(!empty($porte) && count($porte) > 0)
			{
				for($a=0; $a<count($porte); $a++)
				{
					if($prod_porte_id == $porte[$a]['id']) $shop[$found][9]=$porte[$a]['id'];
				}
			}
		}
	}
	else
	{
		$shop[$found][9]='';
	}
}




//Caso tenhamos mudado os serviços extra,vamos atualizar o produto na encomenda
//tem_servicos_extra
if(isset($prod_id) && $prod_id > 0 && (isset($prod_servicos_extra_id)) && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1))
{
	if($prod_servicos_extra_id > 0)
	{
		$info=get_produtc_info($prod_id);
		if(isset($info['fam_id']))
		{
			$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
			if(!empty($servicos_extra) && count($servicos_extra) > 0)
			{
				for($a=0; $a<count($servicos_extra); $a++)
				{
					if($prod_servicos_extra_id == $servicos_extra[$a]['id']) $shop[$found][10]=$servicos_extra[$a]['id'];
				}
			}
		}
	}
	else
	{
		$shop[$found][10]='';
	}
}


//Caso tenhamos mudado as quantidades,vamos atualizar o produto na encomenda
if(isset($_GET['num']) && $_GET['num'] > 0 && $_GET['qtd'] > 0)
{
	/*$qtd_enc_default=verifica_qtd($shop[$_GET['num']][1],$_GET['qtd']); // se for um produto,vamos ver se tem stock
	$shop[$_GET['num']][2]=$qtd_enc_default;*/
	if($shop[$_GET['num']][2] <= 0)
	{
		RemLinhaEnc($_GET['num']);
	}
}


//************************************************************
if(isset($_GET['prod_add_wish']))
{
	$prods_id_wish=explode(',',$_GET['prod_add_wish']);
}
if(isset($_GET['prod_add_obj_wish']))
{
	$prods_obj_id_wish=explode(',',$_GET['prod_add_obj_wish']);
}
$prod_add_qtd_wish=1;

if(isset($_GET['prod_add_wish']) && is_array($prods_id_wish) && !empty($prods_id_wish) && is_array($prods_obj_id_wish) && !empty($prods_obj_id_wish))
{
	for ($key=0; $key < count($prods_id_wish); $key++)
	{
		$prod_id_wish=$prods_id_wish[$key];
		$prod_obj_id_wish=$prods_obj_id_wish[$key];
		$verifica=verifica_stock($prod_id_wish); // se for um produto,vamos ver se tem stock
		if($verifica == 1)
		{
			$i=1;
			$found=0;
			while ($i <= $n_enc && $found == 0)
			{
				if($shop[$i][1] == $prod_id_wish && $shop[$i][5] == $prod_obj_id_wish)
				{
					$found=$i;
				}
				$i++;
			}
			if(!$found)
			{
				$n_enc++; // mais uma linha de encomenda
				$shop[$n_enc][1]=$prod_id_wish; //codigo do produto
				$shop[$n_enc][2]=$prod_add_qtd_wish; //quantidade do produto por defeito
				$shop[$n_enc][3]=1; // tipo de produto ( 1=produtos 2=Club)
				$shop[$n_enc][4]=$d; // chave seguranca
				$shop[$n_enc][5]=$prod_obj_id_wish; // chave seguranca
			}
			else
			{
				$shop[$found][1]=$prod_id_wish; //codigo do produto
				$shop[$found][2] += $prod_add_qtd_wish; //quantidade do produto por defeito
				$shop[$found][3]=1; // tipo de produto ( 1=produtos 2=Club)
				$shop[$found][4]=$d; // chave seguranca
				$shop[$found][5]=$prod_obj_id_wish; // chave seguranca
			}
		}
	}
}
if(isset($_GET['prod_add_repetir']))
{
	$prods_id_repetir=explode(',',$_GET['prod_add_repetir']);
}
if(isset($_GET['prod_add_obj_repetir']))
{
	$prods_obj_id_repetir=explode(',',$_GET['prod_add_obj_repetir']);
}
if(isset($_GET['prod_add_qtd_repetir']))
{
	$prods_add_qtd_repetir=explode(',',$_GET['prod_add_qtd_repetir']);
}
else
{
	$prods_add_qtd_repetir=1;
}
if(isset($_GET['prod_add_repetir']) && is_array($prods_id_repetir) && !empty($prods_id_repetir) && is_array($prods_obj_id_repetir) && !empty($prods_obj_id_repetir))
{
	for ($key=0; $key < count($prods_id_repetir); $key++)
	{
		$prod_id_repetir=$prods_id_repetir[$key];
		$prod_obj_id_repetir=$prods_obj_id_repetir[$key];
		if(is_array($prods_add_qtd_repetir))
		{
			$prod_add_qtd_repetir=$prods_add_qtd_repetir[$key];
		}
		if($prod_add_qtd_repetir <= 0)
		{
			$prod_add_qtd_repetir=1;
		}
		$verifica=verifica_stock($prod_id_repetir); // se for um produto,vamos ver se tem stock
		if($verifica == 1)
		{
			$i=1;
			$found=0;
			while ($i <= $n_enc && $found == 0)
			{
				if($shop[$i][1] == $prod_id_repetir && $shop[$i][5] == $prod_obj_id_repetir)
				{
					$found=$i;
				}
				$i++;
			}
			if(!$found)
			{
				$n_enc++; // mais uma linha de encomenda
				$shop[$n_enc][1]=$prod_id_repetir; //codigo do produto
				$shop[$n_enc][2]=$prod_add_qtd_repetir; //quantidade do produto por defeito
				$shop[$n_enc][3]=1; // tipo de produto ( 1=produtos 2=Club)
				$shop[$n_enc][4]=$d; // chave seguranca
				$shop[$n_enc][5]=$prod_obj_id_repetir; // chave seguranca
			}
			else
			{
				$shop[$found][1]=$prod_id_repetir; //codigo do produto
				$shop[$found][2] += $prod_add_qtd_repetir; //quantidade do produto por defeito
				$shop[$found][3]=1; // tipo de produto ( 1=produtos 2=Club)
				$shop[$found][4]=$d; // chave seguranca
				$shop[$found][5]=$prod_obj_id_repetir; // chave seguranca
			}
			global $cli_id;
			if($cli_id > 0)
			{
				insertProdutosCarrinhoTemporario($cli_id,$prod_id_repetir,$prod_add_qtd_repetir);
			}
		}
	}
}




//Caso tenhamos inserido um voucher,vamos atualizar a encomenda

	function verifica_voucher($code,$vec_vouchers)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$erro='';
		$sql="SELECT * FROM vouchers WHERE voucher_code='".$code."' AND (curdate() BETWEEN voucher_dataini AND voucher_datafim)";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()) // Existe o codigo de desconto disponivel
		{
			$voucher_id=$rs->db_data["voucher_id"];
			$voucher_title=$rs->db_data["voucher_title"];
			$voucher_type=$rs->db_data["voucher_type"];
			$voucher_dataini=$rs->db_data["voucher_dataini"];
			$voucher_datafim=$rs->db_data["voucher_datafim"];
			$voucher_usage=$rs->db_data["voucher_usage"];
			$voucher_code=$rs->db_data["voucher_code"];
			$voucher_prod_id=$rs->db_data["voucher_prod_id"];
			$voucher_discount=$rs->db_data["voucher_discount"];
			$voucher_cliente_id=$rs->db_data["voucher_cliente_id"];
			$voucher_partner_profit=$rs->db_data["voucher_partner_profit"];
			$voucher_acumulavel=$rs->db_data["voucher_acumulavel"];

			if($voucher_type == 0) // Order Discount
			{
				if($voucher_usage == 0) // Pode ser usado sempre dentro de datas
				{
				}
				elseif($voucher_usage == 1) // Pode ser usado uma unica vez
				{
					$sql="SELECT * FROM encomendas_produtos WHERE voucher_id='".$voucher_id."'";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$erro=getLinguas('key_codigo_usado',$GLOBALS['site_lang']);
					}
					$rs2->db_close();
				}
			}
			elseif($voucher_type == 1) // Product Offer
			{
				if($voucher_usage == 0) // Pode ser usado sempre dentro de datas
				{
				}
				elseif($voucher_usage == 1) // Pode ser usado uma unica vez
				{
					$sql="SELECT * FROM encomendas_produtos WHERE voucher_id='".$voucher_id."'";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$erro=getLinguas('key_codigo_usado',$GLOBALS['site_lang']);
					}
					$rs2->db_close();
				}
			}
			elseif($voucher_type == 2) // Partner Offer
			{
				if($voucher_usage == 0) // Pode ser usado sempre dentro de datas e se o cliente não for o parceiro
				{
					if($voucher_cliente_id == $GLOBALS['cli_id']) {
						$erro=getLinguas('key_codigo_invalido_parceiro',$GLOBALS['site_lang']);
					}
				}
				elseif($voucher_usage == 1) // Pode ser usado uma unica vez
				{
					$sql="SELECT * FROM encomendas_produtos WHERE voucher_id='".$voucher_id."'";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$erro=getLinguas('key_codigo_usado',$GLOBALS['site_lang']);
					}
					else
					{
						if($voucher_cliente_id == $GLOBALS['cli_id'])
						{
							$erro=getLinguas('key_codigo_invalido_parceiro',$GLOBALS['site_lang']);
						}
					}
					$rs2->db_close();
				}
			}
			elseif($voucher_type == 3) // Cliente Offer
			{
				if($voucher_usage == 0) // Pode ser usado sempre dentro de datas e se o cliente não for o parceiro
				{
				}
				elseif($voucher_usage == 1) // Pode ser usado uma unica vez
				{
					$sql="SELECT * FROM encomendas_produtos WHERE voucher_id='".$voucher_id."'";
					$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF())
					{
						$erro=getLinguas('key_codigo_usado',$GLOBALS['site_lang']);
					}
					$rs2->db_close();
				}
			}
			//validacao acumulavel
			if($voucher_acumulavel == 0)
			{
				//verificar se já tem algum acumulavel
				for ($i=1; $i <= count($vec_vouchers); $i++)
				{
					$codigo=$vec_vouchers[$i][1];
					if($codigo != "")
					{
						$sql="SELECT * FROM vouchers WHERE voucher_code='".$codigo."'";
						$rs2->db_open($db_link,$DB_name,$sql);
						if(!$rs2->EOF())
						{
							$codx=$rs2->db_data['voucher_acumulavel'];
							if($codx == 0)
							{
								$erro="Não é possível adicionar vouchers não acumuláveis com outros não acumuláveis!";
								break;
							}
						}
						$rs2->db_close();
					}
				}
			}
			$rs->db_close();
			return array('tipo_voucher'=>$voucher_type,'discount_perc'=>$voucher_discount,'erro'=>$erro);
		}
		else
		{
			$rs->db_close();
			return array('erro'=>getLinguas('key_codigo_inexistente',$GLOBALS['site_lang']));
		}
	}
	function get_voucher_info($code,$tipo=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$log2;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$rs2=new Dataobj;
		$erro='';
		if($tipo == 1)$sql="SELECT * FROM vouchers WHERE voucher_id='".$code."'";
		else $sql="SELECT * FROM vouchers WHERE voucher_code='".$code."'";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()) // Existe o codigo de desconto disponivel
		{
			$voucher_id=$rs->db_data["voucher_id"];
			$voucher_title=$rs->db_data["voucher_title"];
			$voucher_type=$rs->db_data["voucher_type"];
			$voucher_dataini=$rs->db_data["voucher_dataini"];
			$voucher_datafim=$rs->db_data["voucher_datafim"];
			$voucher_usage=$rs->db_data["voucher_usage"];
			$voucher_code=$rs->db_data["voucher_code"];
			$voucher_prod_id=$rs->db_data["voucher_prod_id"];
			$voucher_discount=$rs->db_data["voucher_discount"];
			$voucher_cliente_id=$rs->db_data["voucher_cliente_id"];
			$voucher_partner_profit=$rs->db_data["voucher_partner_profit"];
			$voucher_type_discount=$rs->db_data["voucher_type_discount"];
			$rs->db_close();
			return array('voucher_id'=>$voucher_id,'voucher_title'=>$voucher_title,'voucher_type'=>$voucher_type,'voucher_type_discount'=>$voucher_type_discount,'voucher_dataini'=>$voucher_dataini,'voucher_datafim'=>$voucher_datafim,'voucher_usage'=>$voucher_usage,'voucher_code'=>$voucher_code,'voucher_prod_id'=>$voucher_prod_id,'voucher_discount'=>$voucher_discount,'voucher_cliente_id'=>$voucher_cliente_id,'voucher_partner_profit'=>$voucher_partner_profit,);
		}
		$rs->db_close();
		return;
	}
	if((isset($_GET['voucher_add'])||isset($_POST['voucher_add'])) && $voucher_code != '')
	{
		$verifica=verifica_voucher($voucher_code,$voucher);
		if($verifica['erro'] == '')
		{
			$i=1;
			$found=0;
			while ($i <= $n_voucher && $found == 0)
			{
				if($voucher[$i][1] == $voucher_code)
				{
					$found=$i;
				}
				$i++;
			}
			if(!$found)
			{
				$n_voucher++; // mais uma linha de encomenda
				$voucher[$n_voucher][1]=$voucher_code; //codigo do produto
				$voucher[$n_voucher][2]=1; //quantidade do voucher por defeito
				$voucher[$n_voucher][3]=$verifica['tipo_voucher']; // tipo de voucher
				$voucher[$n_voucher][4]=$d; // chave seguranca
			}
			else
			{
				$erro=getLinguas('key_carrinho_voucher',$GLOBALS['site_lang']);
			}
		}
		else
		{
			$erro=$verifica['erro']; //getLinguas('key_carrinho_voucher2',$GLOBALS['site_lang']);
		}

	}






//Caso tenhamos limpo o carrinho,vamos atualizar a encomenda
if(isset($_GET['reset']) && $_GET['reset'] == 1)
{
	unset($_SESSION['ss_shop']);
	unset($_SESSION['ss_n_enc']);
	$shop=array();
	$n_enc=0;
	if($GLOBALS['module_voucher'] == 1)
	{
		unset($_SESSION['ss_voucher']);
		unset($_SESSION['ss_n_voucher']);
		$voucher=array();
		$n_voucher=0;
	}
}


//Caso tenhamos removido um artigo do carrinho,vamos atualizar a encomenda
if(isset($_GET['del']) && $_GET['del'] > 0)
{
	$i=1;
	$found=0;
	while ($i <= $n_enc && $found == 0)
	{
		if($shop[$i][4] == $_GET['del'])
		{
			$found=$i;
		}
		$i++;
	}
	if($found)
	{
		RemLinhaEnc($found);
		global $cli_id;
		if($cli_id > 0)
		{
			deleteProdutosCarrinhoTemporario($cli_id,$prod_id);
		}
	}
}



//Caso tenhamos removido um voucher do carrinho,vamos atualizar a encomenda
if(isset($_GET['delvoucher']) && $_GET['delvoucher'] > 0)
{
	if($GLOBALS['module_voucher'] == 1)
	{
		$i=1;
		$found=0;
		while ($i <= $n_voucher && $found == 0)
		{
			if($voucher[$i][4] == $_GET['delvoucher'])
			{
				$found=$i;
			}
			$i++;
		}
		if($found)
		{
			RemLinhaVoucher($found);
		}
	}
}





//AGORA que ja temos o carrinho atualizado,vamos atualizar a nossa variavel de sessao com os dados atualizados
$_SESSION['ss_shop']=$shop;
$_SESSION['ss_n_enc']=$n_enc;
if($GLOBALS['module_voucher'] == 1)
{
	$_SESSION['ss_voucher']=$voucher;
	$_SESSION['ss_n_voucher']=$n_voucher;
}




//Os pedidos AJAX servem apenas para atualizar o carrinho. Alguns links antigos da
//area de cliente abrem diretamente a pagina de checkout com prod_add; nesses casos
//temos de continuar a renderizar a pagina depois de adicionar o artigo.
$is_cart_ajax_request = isset($_SERVER['HTTP_X_REQUESTED_WITH'])
	&& strtolower((string)$_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if(isset($_GET['prod_add']) && $is_cart_ajax_request){exit;}






function encomendaDominio($valor,$dominio){
	function crypto_rand_secure($min, $max)
	{
		$range = $max - $min;
			    if ($range < 1) return $min; // not so random...
			    $log = ceil(log($range, 2));
			    $bytes = (int) ($log / 8) + 1; // length in bytes
			    $bits = (int) $log + 1; // length in bits
			    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
			    do {
			    	$rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			        $rnd = $rnd & $filter; // discard irrelevant bits
			    } while ($rnd > $range);
			    return $min + $rnd;
	}

	function getToken($length)
	{
		$token = "";
		$codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
	    /*$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
	    $codeAlphabet.= "0123456789";*/
	    $max = strlen($codeAlphabet); // edited

	    for ($i=0; $i < $length; $i++) {
	    	$token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
	    }

	    return $token;
	}

	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
	$rs_cliente=new Dataobj;
	$dados_cliente=cliente_logado();

	if(is_array($dados_cliente))
	{
		$cli_id=$dados_cliente['cli_id'];
		$m_contribuinte='';
		$m_email='';
		$m_contribuinte=$dados_cliente['Nif'];
		$m_email=$dados_cliente['Email'];
	}


	$Referencia=getToken(9);
	$enc_token=md5($new_id.$dados_cliente['cli_id']);

	$sql="insert into encomendas (Referencia,Metodo_pagamento,ID_Cliente,TotalDocComIva,TotalDocSemIva,Data_Criacao,enc_token,Nota_privada) values ('$Referencia',17,'$cli_id','$valor','$valor','".date("Y-m-d H:i:s")."','$enc_token','URL: $dominio')";	
//echo $sql.'<hr>';
	$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
	
	if($res)
	{
		$order_id=db_get_last_insert_id($db_link);

		echo getMetodoPagamentoMBLusopayGateway($order_id);

		
	}
}




//a função que é invocada por defeito pela página publica é a carrinho_purchase() via pagina publica
/********************************************************************************/
/********************************************************************************/
/********************************************************************************/
/********************************************************************************/
function carrinho_purchase()
{
	date_default_timezone_set("Europe/Lisbon");
	setlocale(LC_TIME, "Portuguese_Standard");
	setlocale(LC_ALL, "en_US.UTF-8;LC_NUMERIC=C");
	setlocale(LC_NUMERIC, 'en_US');


	//Quando esta função é invocada é suposto o carrinho estar já com todas as variáveis definidas
	validaCarrinho();
	global $n_enc,$shop,$iva_default,$n_voucher,$voucher,$site_shell_url;
	$dados_cliente=cliente_logado();

	/********************************************************************************/
	//Só quando o cliente finaliza a encomenda é que entra nesta condicao e vamos gravar a encomenda em base de dados:
	/********************************************************************************/
	if(isset($_POST['finalizar']) && $_POST['finalizar'] == 1 && $n_enc > 0)  //&& is_array($dados_cliente)
	{
		$erros='';
		$valido=TRUE;
		$rs_cliente=new Dataobj;
		$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$m_criarconta=0;


		if($dados_cliente) // EXISTE LOGIN, AS INFORMAÇÕES DE ENTREGA E FATURAÇÃO VEM DE SELECTBOXS
		{
			// dados de faturacao
			$m_end_faturacao=intval($_POST['m_end_faturacao']);
			if($m_end_faturacao <= 0)
			{
				$valido=FALSE;
			}
			// dados de entrega
			$m_end_entrega=intval($_POST['m_end_entrega']);
			if($m_end_entrega <= 0)
			{
				$valido=FALSE;
			}



			$m_telefone='';
			$endFaturacao=getEnderecos($dados_cliente['cli_id'],1,$m_end_faturacao);
			if(!$endFaturacao->EOF())
			{
				$m_telefone=$endFaturacao->db_data['Telefone'];
			}
			$endFaturacao->db_close();

			//Obter info do endereço de entrega
			$m2_telefone='';
			$endEntrega=getEnderecos($dados_cliente['cli_id'],1,$m_end_entrega);
			if(!$endEntrega->EOF())
			{
				$m2_telefone=$endEntrega->db_data['Telefone'];
			}
			$endEntrega->db_close();

			if(!$m2_telefone){$valido=FALSE;$erros.='m2_telefone|';}





		}
		else // NÃO EXISTE LOGIN AS INFORMAÇÕES DE ENTREGA E FATURAÇÃO VEM DE CAMPOS DE TEXTO PORQUE PODE-SE FAZER ENCOMENDAS SEM REGISTO
		{
			$m_nome = db_real_escape_string($_POST['m_nome']);
			if(empty($m_nome)){ $valido=false; }
			$m_apelido  = db_real_escape_string($_POST['m_apelido']);
			$m_email            = db_real_escape_string($_POST['m_email']);
			if(empty($m_email) || !validEmail($m_email)){$valido=false; }
			$m_contribuinte     = db_real_escape_string($_POST['m_contribuinte']);
			$m_pais             = db_real_escape_string($_POST['m_pais']);
			if(empty($m_pais)){$valido=false; }
			$m_morada           = db_real_escape_string($_POST['m_morada']);
			if(empty($m_morada)){$valido=false; }
			$m_cidade           = db_real_escape_string($_POST['m_cidade']);
			if(empty($m_cidade)){$valido=false; }
			$m_codpos           = db_real_escape_string($_POST['m_codigo_postal']);
			if(empty($m_codpos)){$valido=false; }
			$m_telefone         = db_real_escape_string($_POST['m_telefone']);
			if(empty($m_telefone)){$valido=false; }

			// SE OS DADOS DE ENTREGA FOREM DIFERENTES
			if(isset($_POST['m_dadosdiferentes']))
			{
				$m2_nome            = db_real_escape_string($_POST['m2_nome']);
				if(empty($m2_nome)){$valido=false; }
				$m2_apelido         = db_real_escape_string($_POST['m2_apelido']);
				$m2_morada          = db_real_escape_string($_POST['m2_morada']);
				if(empty($m2_morada)){$valido=false; }
				$m2_pais            = db_real_escape_string($_POST['m2_pais']);
				if(empty($m2_pais)){$valido=false; }
				$m2_cidade          = db_real_escape_string($_POST['m2_cidade']);
				if(empty($m2_cidade)){$valido=false; }
				$m2_codpos          = db_real_escape_string($_POST['m2_codigo_postal']);
				if(empty($m2_codpos)){$valido=false; }
				$m2_telefone        = db_real_escape_string($_POST['m2_telefone']);
			}
			else
			{
				$m2_nome=$m_nome;
				$m2_apelido=$m_apelido;
				$m2_morada=$m_morada;
				$m2_pais=$m_pais;
				$m2_cidade=$m_cidade;
				$m2_codpos=$m_codpos;
				$m2_telefone=$m_telefone;
			}
			//CRIAR CONTA
			$m_criarconta       = db_real_escape_string($_POST['m_criarconta']);
		}
		//metodo de pagamento
		$m_metodopagamento=intval($_POST['m_metodopagamento']);
		if($m_metodopagamento <= 0)
		{
			$valido=false;
			$erros.='m_metodopagamento|';
		}
		//metodo de expedicao
		if(!isset($GLOBALS['carrinho_conf']) || isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 0 && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 0)
		{
			if($_GET['pp']==1)
			{
				echo "m_metodoenvio = ";
				var_dump($m_metodoenvio);
			}
			
			$m_metodoenvio=intval($_POST['m_metodoenvio']);
			
			if($_GET['pp']==1)
			{
				echo "m_metodoenvio = ";
				var_dump($m_metodoenvio);
			}

			if($m_metodoenvio <= 0)
			{
				$valido=false;
				$erros.='m_metodoenvio|';
			}
		}
		$m_observacoes=addslashes($_POST['m_observacoes']);
		if(!$valido)
		{
			$form='';
			$hidden='hidden';
			foreach($_POST as $key=>$value)
			{
				if($key == 'm_nome') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_apelido') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_morada') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_codigo_postal') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_cidade') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_pais') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_contribuinte') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_telefone') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_email') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_dadosdiferentes') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_criarconta') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_nome') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_apelido') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_morada') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_codigo_postal') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_cidade') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_pais') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm2_telefone') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_metodopagamento') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
				elseif($key == 'm_observacoes') $form .= '<input type="'.$hidden.'" name="'.$key.'" value="'.$value.'">';
			}
			if($form!='')
			{
				echo '<form action="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'?erro=1&erro_msg='.$erros.'" method="post" name="encomendas" id="encomendas" class="encomendas">';
				echo $form;
				echo '</form>';
				echo '<script>if(document.getElementById(\'encomendas\'))document.getElementById(\'encomendas\').submit(); </script>';
				exit;
			}
			else
			{
				echo '<script>window.location.href=\''.seo('pages.php?page_id='.$GLOBALS['page_id']).'?erro=1&erro_msg='.$erros.'\';</script>';
				exit;
			}
		}
		else
		{
			global $m_metodoenvio,$m_pais;

			if(isset($m_criarconta) && $m_criarconta == 1)
			{
				$sql='SELECT ID FROM clientes WHERE Email="'.$m_email.'"';
				$rs_cliente->db_open($db_link,$GLOBALS['DB_name'],$sql);
				if(!$rs_cliente->EOF())
				{
					$cli_id=$rs_cliente->db_data['ID'];
				}
				else
				{
					$m_password1=generateRandomString();
					$m_password=encripta($m_password1);
					$sql='INSERT INTO
						clientes
						(
							Username,
							Password,
							Nome,
							Apelido,
							Morada,
							Cidade,
							Codigo_Postal,
							Pais,
							Telefone,
							Email,
							Nome_ent,
							Apelido_ent,
							Morada_ent,
							Cidade_ent,
							Codigo_Postal_ent,
							Pais_ent,
							Telefone_ent,
							Email_ent,
							Nif,
							Data,
							Ativo,
							Sexo,
							DataNascimento,
							Estado
						)
						VALUES
						(
							"'.$m_email.'",
							"'.$m_password.'",
							"'.$m_nome.'",
							"'.$m_apelido.'",
							"'.$m_morada.'",
							"'.$m_cidade.'",
							"'.$m_codpos.'",
							"'.$m_pais.'",
							"'.$m_telefone.'",
							"'.$m_email.'",
							"'.$m2_nome.'",
							"'.$m2_apelido.'",
							"'.$m2_morada.'",
							"'.$m2_cidade.'",
							"'.$m2_codpos.'",
							"'.$m2_pais.'",
							"'.$m2_telefone.'",
							"'.$m2_email.'",
							"'.$m_contribuinte.'",
							"'.date("Y-m-d").'",
							"0",
							"'.$m_sexo.'",
							"'.$m_datanascimento.'",
							"0"
						)';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
					if($res)
					{
						$cli_id=db_get_last_insert_id($db_link);
						save_endereco($cli_id);
						global $site_http_url;
						$cli_token=uniqid('');
						$sql="UPDATE clientes SET failed_attempts=0,Token='".$cli_token."' WHERE ID=".$cli_id;
						$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						$body=array
								(
									'[code]'=>SITE_HTTP_URL.seo('pages.php?page_id='.$GLOBALS['verify_page_id'],'','',$GLOBALS['site_lang']).'?code='.$cli_token,
									'[name]'=>$m_nome,
									'[surname]'=>$m_apelido,
									'[city]'=>$m_cidade,
									'[birthdate]'=>$m_datanascimento,
									'[sex]'=>$sex,
									'[address]'=>$m_morada,
									'[zipcode]'=>$m_codpos,
									'[country]'=>country_name($m_pais),
									'[email]'   => $m_email.'<br>Password Temporária: '.$m_password1,
									'[username]'=>$m_email
								);
						$message=template_email($GLOBALS['template_clientes'],$body);
						send_email($m_email,$message['subject'],$message['body']);
						//if(!substr_count($_SERVER['HTTP_HOST'],'192.168.1.202')) send_email($GLOBALS['Default_notifications_email'],'Novo '.$message['subject'],$message['body']);
					}
				}
				$rs_cliente->db_close();
			}
			else
			{
				$cli_id=0;
			}
			$ID_Afiliado=0;
			$Afiliado_Perc=0;
			if(isset($_COOKIE['domesticosafiliado']) && intval($_COOKIE['domesticosafiliado']) > 0)
			{
				$ID_Afiliado=intval($_COOKIE['domesticosafiliado']);
				$sql="SELECT Afiliado_Perc FROM clientes WHERE ID='$ID_Afiliado' AND Afiliado=1";
				$rs_cliente->db_open($db_link,$GLOBALS['DB_name'],$sql);
				if(!$rs_cliente->EOF())
				{
					$Afiliado_Perc=$rs_cliente->db_data['Afiliado_Perc'];
				}
				else
				{
					$ID_Afiliado=0;
					$Afiliado_Perc=0;
				}
				$rs_cliente->db_close();
			}

			if(is_array($dados_cliente))
			{
				$cli_id=$dados_cliente['cli_id'];
				$m_contribuinte='';
				$m_email='';
				$m_contribuinte=$dados_cliente['Nif'];
				$m_email=$dados_cliente['Email'];
			}
			// OBTER INFO DO ENDEREÇO DE FATURACAO
			if($dados_cliente)
			{
				$m_designacao='';
				$m_morada='';
				$m_codpos='';
				$m_cidade='';
				$m_pais='';
				$m_telefone='';
				$endFaturacao=getEnderecos($dados_cliente['cli_id'],1,$m_end_faturacao);
				if(!$endFaturacao->EOF())
				{
					$m_nome=$endFaturacao->db_data['Nome'];
					$m_apelido=$endFaturacao->db_data['Apelido'];
					$m_morada=$endFaturacao->db_data['Morada'];
					$m_codpos=$endFaturacao->db_data['Codigo_Postal'];
					$m_cidade=$endFaturacao->db_data['Cidade'];
					$m_pais=$endFaturacao->db_data['Pais'];
					$m_telefone=$endFaturacao->db_data['Telefone'];
					$m_contribuinte=$endFaturacao->db_data['Nif'];
				}
				$endFaturacao->db_close();

				//Obter info do endereço de entrega
				$m2_designacao='';
				$m2_nome='';
				$m2_apelido='';
				$m2_morada='';
				$m2_codpos='';
				$m2_cidade='';
				$m2_pais='';
				$m2_telefone='';
				$endEntrega=getEnderecos($dados_cliente['cli_id'],1,$m_end_entrega);
				if(!$endEntrega->EOF())
				{
					$m2_nome=$endEntrega->db_data['Nome'];
					$m2_apelido=$endEntrega->db_data['Apelido'];
					$m2_morada=$endEntrega->db_data['Morada'];
					$m2_codpos=$endEntrega->db_data['Codigo_Postal'];
					$m2_cidade=$endEntrega->db_data['Cidade'];
					$m2_pais=$endEntrega->db_data['Pais'];
					$m2_telefone=$endEntrega->db_data['Telefone'];
				}
				$endEntrega->db_close();
			}
			// CALCULA ENCOMENDA COM PRODUTOS E COM VOUCHERS
			$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes

			if(!$m2_pais)$m2_pais=$m_pais;

			// VAI BUSCAR O VALOR DE PORTES POR ENCOMENDA
			$PortesCarrinho=0;
			if($totalEncomenda['TEM_PORTES']==1)
			{
				if(!isset($GLOBALS['carrinho_conf']) || isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 0)
				{
					if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m2_pais))
					{
						$PortesCarrinho=getPortesCarrinho($m_metodoenvio,$m2_pais);
						$valor_portes_gratuitos=getPortesGratuitos();
						if( ($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS']) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
						{
							$PortesCarrinho=0;
						}
					}
				}
			}
			//Se tem voucher oferta portes
			if($voucher_oferta_portes==1)$PortesCarrinho=0;

			// VAI BUSCAR TAXAS ASSOCIADAS AO METODO DE PAGAMENTO
			$taxas=0;
			if($metodoPagamentoObj=getEncomendaMetodoPagamento($m_metodopagamento))
			{
				if($metodoPagamentoObj['metodo_taxa_tipo'] == 1)
				{
					$metodo_taxa2=calcula_desconto($totalEncomenda['TOTALCIVA'],$metodoPagamentoObj['Taxa']);
					$taxas += $metodo_taxa2;
				}
				else
				{
					$taxas += $metodoPagamentoObj['Taxa'];
				}
			}

			if($GLOBALS['juntar_taxas_aos_portes']==1)
			{
				$PortesCarrinho+=$taxas;
				$taxas=0;
			}


			// VAI VER SE TEM OFERTAS
			$ofertas=array();
			foreach($_POST as $key=>$val)
			{
				if(substr_count($key,"mofertas"))
				{
					$ofertas[]=$val;
				}
			}
			$Data_Criacao=date("Y-m-d H:i:s");



			function crypto_rand_secure($min, $max)
			{
			    $range = $max - $min;
			    if ($range < 1) return $min; // not so random...
			    $log = ceil(log($range, 2));
			    $bytes = (int) ($log / 8) + 1; // length in bytes
			    $bits = (int) $log + 1; // length in bits
			    $filter = (int) (1 << $bits) - 1; // set all lower bits to 1
			    do {
			        $rnd = hexdec(bin2hex(openssl_random_pseudo_bytes($bytes)));
			        $rnd = $rnd & $filter; // discard irrelevant bits
			    } while ($rnd > $range);
			    return $min + $rnd;
			}

			function getToken($length)
			{
			    $token = "";
			    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
			    /*$codeAlphabet.= "abcdefghijklmnopqrstuvwxyz";
			    $codeAlphabet.= "0123456789";*/
			    $max = strlen($codeAlphabet); // edited

			    for ($i=0; $i < $length; $i++) {
			        $token .= $codeAlphabet[crypto_rand_secure(0, $max-1)];
			    }

			    return $token;
			}

			$Referencia=getToken(9);

			$sql='INSERT INTO
					encomendas
					(
						Data_Criacao,
						Metodo_pagamento,
						Metodo_envio,
						ID_Cliente,
						Contacto,
						enc_Nome,
						enc_Apelido,
						enc_Email,
						enc_Telefone,
						enc_Nif,
						enc_Pais,
						enc_Morada,
						enc_Cidade,
						enc_DataNascimento,
						enc_Sexo,
						enc_Codigo_Postal,
						enc_Nome_ent,
						enc_Apelido_ent,
						enc_Morada_ent,
						enc_Pais_ent,
						enc_Cidade_ent,
						enc_Codigo_Postal_ent,
						enc_Telefone_ent,
						Obs,
						ID_Afiliado,
						Afiliado_Perc,
						Lingua,
						Dispositivo,
						Referencia
					)
					VALUES
					(
						"'.date("Y-m-d H:i:s").'",
						"'.$m_metodopagamento.'",
						"'.$m_metodoenvio.'",
						"'.$cli_id.'",
						"'.$m_nome.' '.$m_apelido.'",
						"'.$m_nome.'",
						"'.$m_apelido.'",
						"'.$m_email.'",
						"'.$m_telefone.'",
						"'.$m_contribuinte.'",
						"'.$m_pais.'",
						"'.$m_morada.'",
						"'.$m_cidade.'",
						"'.$m_datanascimento.'",
						"'.$m_sexo.'",
						"'.$m_codpos.'",
						"'.$m2_nome.'",
						"'.$m2_apelido.'",
						"'.$m2_morada.'",
						"'.$m2_pais.'",
						"'.$m2_cidade.'",
						"'.$m2_codpos.'",
						"'.$m2_telefone.'",
						"'.$m_observacoes.'",
						"'.$ID_Afiliado.'",
						"'.$Afiliado_Perc.'",
						"'.$GLOBALS['site_lang'].'",
						"'.(defined('Dispositivo') ? Dispositivo : '').'",
						"'.$Referencia.'"
					)';
			$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
			if($res)
			{
				$DATAFIMCMP_arr=array();
				$ID_arr=array();
				$new_id=db_get_last_insert_id($db_link);

                UpdDataNascimento2($new_id);

				$enc_token=md5($new_id.$cli_id);
				$i=1;
				$totalciva=0;
				$totalsiva=0;
				$offer_arr=showSalesPromotions_offer($GLOBALS['proms']);
				if($offer_arr['p_html_offers'])
				{
					$DATAFIMCMP_arr[]=$offer_arr['p_data_limite'];
				}



				//AGORA VAMOS GRAVAR AS LINHAS SE ARTIGOS
				$total_custos_logisticos=0;
				$n_custos_logisticos=0;
				while ($i <= $n_enc)
				{
					$portes=0;

					$prod_id=$shop[$i][1];
					$qtd=$shop[$i][2];
					$tipo=$shop[$i][3];
					$chave=$shop[$i][4];
					$prod_obj=$shop[$i][5];
					$opcoes ='';$opcoes=$shop[$i][6];
					$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
					$garantia_id=0; $garantia_id=$shop[$i][8];
					$porte_id=0; $porte_id=$shop[$i][9];
					$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

					$prod_serv=0; $prod_serv	= $shop[$i][12];
					$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
					$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
					$altura=0; $altura	= $shop[$i][15];
					$largura=0; $largura	= $shop[$i][16];
					$data_realizacao=''; $data_realizacao	=$shop[$i][17];
					$nome_do_ficheiro=''; $nome_do_ficheiro	=$shop[$i][18];


					$info=get_produtc_info($prod_id);

					if($opcoes_id > 0)
					{
						$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
						if($tempo_escolhido>0)
						{
							$preco_final_siva=$info['prod_precos']['PVP'];

							$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
							$rs=new DataObj;

							$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
							$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
						    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
						    $opcoes.=' '.$rs->db_data['Nome'];
							$preco_final_siva=$preco_final_siva*$custo_entrega;
							$rs->db_close();

							$info['prod_precos']['PVP']=$preco_final_siva;
						}
					}

					//OUTROS SERVIÇOS ADICIONAIS, POR PRODUTO, EXEMPLO GRAFICA:
					if(isset($prod_serv) && $prod_serv!='' )
					{
						$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
						$rs2=new DataObj;
						$array_servicos=explode(";",$prod_serv);
						foreach($array_servicos as $servico_id)
						{
							if($servico_id>0)
							{
								$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
								$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
								if(!$rs2->EOF())
								{
									$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
									$custo=$rs2->db_data['Custos'];
									//$custo=$custo/1.23;
									$margem=$rs2->db_data['margem'];
									$opcoes.=', '.$rs2->db_data['Nome'];
									if($tipo==1)
									{
										//$preco_final_siva=$preco_final_siva+$custo;
										//$totalciva += $custo;
										$info['prod_precos']['PVP'] += $custo;

									}else{
										// vai ficar por fazer as do tipo margem
									}
								}
								$rs2->db_close();
							}
						}
					}
					$valorqtd=$info['prod_precos']['PVP'] * $qtd;







					$modelo=$info['prod_precos']['Modelo'];  // MODELO SECUNDARIO
					$arr_prom=showSalesPromotions($GLOBALS['proms'],$prod_id,$valorqtd,1);
					$DATAFIMCMP_arr[]=$arr_prom['DATAFIMCMP'];
					$ID_arr[]=$arr_prom['IDARR'];
					if($arr_prom['p_valor'] > 0)
					{
						$totalciva += $arr_prom['p_valor'];
					}
					else
					{
						$totalciva += $valorqtd;
					}
					$valorciva=$info['prod_precos']['PVP'];
					if($arr_prom['p_valor'] > 0)
					{
						$v_qtd=($valorqtd - $arr_prom['p_valor']);
					}
					else
					{
						$v_qtd=($valorqtd);
					}
					$iva=$info['prod_precos']['IVA'];
					if($iva <= 0)
					{
						$iva=$iva_default;
					}
					if($iva > 0)
					{
						$iva=1 + (floatval($iva) / 100);
						if($arr_prom['p_valor'] > 0)
						{
							$psiva=(($arr_prom['p_valor']) / $iva);
							$pciva=(($arr_prom['p_valor']));
						}
						else
						{
							$psiva=($info['prod_precos']['PVP'] / $iva);
							$pciva=($info['prod_precos']['PVP']);
						}
					}
					else
					{
						if($arr_prom['p_valor'] > 0)
						{
							$psiva=$arr_prom['p_valor'];
							$pciva=$arr_prom['p_valor'] * $iva;
						}
						else
						{
							$psiva=$info['prod_precos']['PVP'];
							$pciva=$info['prod_precos']['PVP'] * $iva;
						}
					}


					$totalsiva += $psiva;


					//MAS SE TIVERMOS SELECCIONADO UM METODO DE LEVANTAMENTO GRATUITO, NÂO HA CUSTOS DE LOGISTICA
					if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
					{
						$custo_logistico=0;
						if($metodoEnvioObj['Gratis']==1)
						{
							$custo_logistico=0;
							$portes=0;
						}
						else
						{
							//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
							if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
							{
								$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
								for($a=0; $a<count($custos_logisticos); $a++)
								{
									$n_custos_logisticos++;
									if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $n_custos_logisticos>1)
									{
										//Vamos cobrar apenas metade dos custos logisticos a partir do segundo artigo
										$totalciva += (($custos_logisticos[$a]['valor'] * $qtd)/2);
										$custo_logistico+=(($custos_logisticos[$a]['valor'] * $qtd)/2);
									}
									else
									{
										$totalciva += ($custos_logisticos[$a]['valor'] );
										$custo_logistico+=($custos_logisticos[$a]['valor'] );

										if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $qtd>1)
										{
											$totalciva += (($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
											$custo_logistico+=(($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
										}
										elseif($qtd>1)
										{
											$totalciva += ($custos_logisticos[$a]['valor'] * ($qtd-1));
											$custo_logistico+=($custos_logisticos[$a]['valor'] * ($qtd-1));
										}
									}
								}
							}
						}
					}


					//TEM SERVICOS EXTRA
					$produto_servicos_extra_valor=0;
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
					{
						if($servicos_extra_id > 0)
						{
							$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
							for($a=0; $a<count($servicos_extra); $a++)
							{
								if($servicos_extra_id == $servicos_extra[$a]['id'])
								{
									$produto_servicos_extra_valor=($servicos_extra[$a]['valor'] * $qtd);
									$titulo_servicos_extra=$servicos_extra[$a]['titulo'];
								}
							}
						}
					}

					//Se tiver esta flag ativa e se tiver serviços extra nao paga portes nem custos logisticos para este artigo
					if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium'] == 1)
					{
						if($produto_servicos_extra_valor>0)
						{
							$custo_logistico=0;
							$portes=0;
						}
					}

					//Se tem cuto logistico, troca o valor de portes por este
					if($custo_logistico>0)
					{
						$total_custos_logisticos+=$custo_logistico;
						$portes=$custo_logistico;
					}

					//agora vamos somar os portes deste artigo aos portes acumulados de todos os artigos
					if($portes > 0) $PortesCarrinho+=$portes;

					$taxa_iva=$info['prod_precos']['IVA'];
					if($taxa_iva <= 0)
					{
						$taxa_iva=$iva_default;
					}

					$Valor_custo_sem_iva=$info['prod_precos']['PC'];
					$Valor_sem_iva=$psiva;
					$IVA_valor=$pciva-$psiva;
					$IVA_taxa=$taxa_iva;
					$Peso=$info['prod_peso'];
					$EAN=$info['Ref_Ean'];
					$Ref=$info['prod_ref'];


					$txt_opcoes='';
					//TROT:
					if($GLOBALS['tabela_medidas']==1)
					{
						//echo $opcoes."<br>";
						if(isset($opcoes) && $opcoes != '' && $opcoes != '')
						{
							if(substr_count($opcoes,'TROT:')>0)
							{
								$info1=explode(';', $opcoes);
								$dados_peca = $info1[0];
								$dados_peca2 = explode('TROT:',$dados_peca);
								//echo $dados_peca2[1]."<br>";
								$txt_opcoes.="Tamanho ".$dados_peca2[1]."<br>";

								$users=$info1[1];
								$info2=explode(',', $users);

								$a=0;
								$b=count($info2);
								while($a<$b)
								{
									$info3=str_replace('_id_','',$info2[$a]);
									$info4=explode('_qtd_',$info3);

									$user_id=$info4[0];
									$user_qtd=$info4[1];

									if($user_id>0)
									{
										if($user_qtd>1)$txt_opcoes.="» ".show_user_name($user_id).", ".$user_qtd." unidades <br>";
										else $txt_opcoes.="» ".show_user_name($user_id).", ".$user_qtd." unidade <br>";
									}

									$a++;
								}
							}
							else $txt_opcoes=rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'].','.$opcoes : $info['prod_nome']),',');
						}
					}
					else $txt_opcoes=rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'].','.$opcoes : $info['prod_nome']),',');

					$sql='INSERT INTO
							encomendas_produtos
							(
								ID_PAI,
								ID_Cliente,
								ID_Produto,
								Ref,
								Nome,
								Qtd,
								Valor,
								Valor_Pagar,
								Tipo,
								Estado,
								Valor_Desconto,
								Perc_Desconto,
								Chave,
								Valor_custo_sem_iva,
								Valor_sem_iva,
								IVA_valor,
								IVA_taxa,
								Peso,
								EAN,
								Obs
							)
							VALUES
							(
								"'.$new_id.'",
								"'.$cli_id.'",
								"'.$prod_id.'",
								"'.$Ref.'",
								"'.$txt_opcoes.'",
								"'.$qtd.'",
								"'.$valorciva.'",
								"'.($Valor_sem_iva/$qtd).'",
								"'.$tipo.'",
								"0",
								"'.$v_qtd.'",
								"'.$arr_prom['p_desconto'].'",
								"'.$chave.'",
								"'.$Valor_custo_sem_iva.'",
								"'.$Valor_sem_iva.'",
								"'.$IVA_valor.'",
								"'.$IVA_taxa.'",
								"'.$Peso.'",
								"'.$EAN.'",
								"'.$obs.'"
							)';

					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

					if($res)
					{

						$ID_Produto_Pai=db_get_last_insert_id($db_link);

						if($info['prod_precos']['Sync']=="1")
						{
							 $sql='INSERT INTO
								reserva_stocks
								(
									ID_PAI,
									ID_Cliente,
									ID_Produto,
									Ref,
									Nome,
									Qtd,
									Valor,
									Valor_Pagar,
									Valor_Desconto,
									Perc_Desconto,
									Valor_custo_sem_iva,
									Valor_sem_iva,
									IVA_valor,
									IVA_taxa,
									Peso,
									EAN
								)
								VALUES
								(
									"'.$new_id.'",
									"'.$cli_id.'",
									"'.$prod_id.'",
									"'.$modelo.'",
									"'.$txt_opcoes.'",
									"'.$qtd.'",
									"'.$valorciva.'",
									"'.($Valor_sem_iva/$qtd).'",
									"'.$v_qtd.'",
									"'.$arr_prom['p_desconto'].'",
									"'.$Valor_custo_sem_iva.'",
									"'.$Valor_sem_iva.'",
									"'.$IVA_valor.'",
									"'.$IVA_taxa.'",
									"'.$Peso.'",
									"'.$EAN.'"
								)';
							$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						}


						// vamos verificar se existe uma imagem caso sim vamos guardar-la na pasta final com o nome certo
						//echo '../files/imagens_clientes/'.$nome_do_ficheiro.'<br>'.stream_resolve_include_path("'../files/imagens_clientes/'$nome_do_ficheiro");
						if($nome_do_ficheiro!='')
						{

							global $m_http_url_images, $site_shell_url;
							$pos=strrpos($nome_do_ficheiro,".");
							$file_ext=substr($nome_do_ficheiro,$pos+1,strlen($nome_do_ficheiro)+1-$pos);

							if(copia_segura($site_shell_url.'files/imagens_clientes/'.$nome_do_ficheiro, $site_shell_url.'/files/files_encomendas/'.$new_id.'_'.$ID_Produto_Pai.'.'.$file_ext))
							{
								// vamos fazer update a tabela de enconmenda para esta ter o novo nome do ficheiro da encomenda
								$sql='UPDATE encomendas_produtos SET file = "'.$new_id.'_'.$ID_Produto_Pai.'.'.$file_ext.'"WHERE ID='.$ID_Produto_Pai;
								$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							}
							else
							{
								echo 'Erro a salvar o ficheiro do produto';
							}
						}


						//Gravar como linha de artigo a extensão de garantia para este artigo
						if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)
						{
							$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
							if($garantia_id > 0)
							{
								for($a=0; $a<count($garantia); $a++)
								{
									if($garantia_id == $garantia[$a]['id'])
									{
										$totalciva += ($garantia[$a]['valor'] * $qtd);
										$sql='INSERT INTO
											encomendas_produtos
											(
												ID_PAI,
												ID_Cliente,
												ID_Produto,
												ID_Produto_Pai,
												Ref,
												Nome,
												Qtd,
												Valor,
												Valor_Pagar,
												Tipo,
												Estado,
												Valor_Desconto,
												Perc_Desconto,
												Chave,
												Obs
											)
											VALUES
											(
												"'.$new_id.'",
												"'.$cli_id.'",
												"'.$garantia_id.'",
												"'.$ID_Produto_Pai.'",
												"&nbsp;",
												"Garantia Extra '.$garantia[$a]['anos'].' '.($garantia[$a]['anos'] > 1 ? 'anos' : 'ano').'",
												"'.$qtd.'",
												"'.$garantia[$a]['valor'].'",
												"'.($garantia[$a]['valor']/1.23).'",
												"0",
												"0",
												"0",
												"0",
												"",
												""
											)';
										$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
									}
								}
							}
						}

						//Gravar como linha de artigo o serviço extra para este artigo
						if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
						{
							$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
							if($servicos_extra_id > 0)
							{
								for($a=0; $a<count($servicos_extra); $a++)
								{
									if($servicos_extra_id == $servicos_extra[$a]['id'])
									{
										$totalciva += ($servicos_extra[$a]['valor'] * $qtd);
										$sql='INSERT INTO
											encomendas_produtos
											(
												ID_PAI,
												ID_Cliente,
												ID_Produto,
												ID_Produto_Pai,
												Ref,
												Nome,
												Qtd,
												Valor,
												Valor_Pagar,
												Tipo,
												Estado,
												Valor_Desconto,
												Perc_Desconto,
												Chave,
												Obs
											)
											VALUES
											(
												"'.$new_id.'",
												"'.$cli_id.'",
												"'.$servicos_extra_id.'",
												"'.$ID_Produto_Pai.'",
												"&nbsp;",
												"'.$servicos_extra[$a]['titulo'].'",
												"'.$qtd.'",
												"'.$servicos_extra[$a]['valor'].'",
												"'.($servicos_extra[$a]['valor']/1.23).'",
												"0",
												"0",
												"0",
												"0",
												"",
												""
											)';
										$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
									}
								}
							}
						}

						//Gravar como linha de artigo os dados de uma promoção afeta a este produto
						// VAMOS INCLUIR A PROMOÇÃO COMO UMA LINHA
						if($arr_prom['p_valor'] > 0)
						{
							for($a=0; $a<count($arr_prom['p_desc_arr']); $a++)
							{
								$sql='INSERT INTO
									encomendas_produtos
									(
										ID_PAI,
										ID_Cliente,
										ID_Produto,
										ID_Produto_Pai,
										Ref,
										Nome,
										Qtd,
										Valor,
										Valor_Pagar,
										Tipo,
										Estado,
										Valor_Desconto,
										Perc_Desconto,
										Chave,
										Obs
									)
									VALUES
									(
										"'.$new_id.'",
										"'.$cli_id.'",
										"0",
										"'.$ID_Produto_Pai.'",
										"",
										"'.$arr_prom['p_desc_arr'][$a]['DESC'].'",
										"1",
										"'.$arr_prom['p_desc_arr'][$a]['VALOR_RETIRADO'].'",
										"'.($arr_prom['p_desc_arr'][$a]['VALOR_RETIRADO']/1.23).'",
										"2",
										"0",
										"0",
										"0",
										"",
										""
									)';
								$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
							}
						}



						//Gravar como linha de artigo os portes por produto,quando tem multiportes
						// PORTES POR PRODUTO
						if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
						{
							$totalporte=0;
							$titulo_porte='';
							$porte=multi_portes_prod($info['prod_id'],$info['prod_precos']['PVP'],$info['prod_peso']);
							if($porte_id > 0)
							{
								for($a=0; $a<count($porte); $a++)
								{
									if($porte_id == $porte[$a]['id'])
									{
										$portes += ($porte[$a]['valor'] * $qtd);
										$totalporte=($porte[$a]['valor'] * $qtd);
										$titulo_porte=$porte[$a]['titulo'];

											$sql='INSERT INTO
											encomendas_produtos
											(
												ID_PAI,
												ID_Cliente,
												ID_Produto,
												ID_Produto_Pai,
												Ref,
												Nome,
												Qtd,
												Valor,
												Valor_Pagar,
												Tipo,
												Estado,
												Valor_Desconto,
												Perc_Desconto,
												Chave,
												Obs
											)
											VALUES
											(
												"'.$new_id.'",
												"'.$cli_id.'",
												"'.$porte_id.'",
												"'.$ID_Produto_Pai.'",
												"",
												"'.$porte[$a]['titulo'].'",
												"'.$qtd.'",
												"'.$porte[$a]['valor'].'",
												"'.($porte[$a]['valor']/1.23).'",
												"0",
												"0",
												"0",
												"0",
												"",
												""
											)';
										$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
									}
								}
							}
							elseif($porte_id == 0)
							{
								for($a=0; $a<count($porte); $a++)
								{
									if($a == 0)
									{
										$portes += ($porte[$a]['valor'] * $qtd);
										$porte_id=$porte[$a]['id'];
										$totalporte=($porte[$a]['valor'] * $qtd);
										$titulo_porte=$porte[$a]['titulo'];


											$sql='INSERT INTO
											encomendas_produtos
											(
												ID_PAI,
												ID_Cliente,
												ID_Produto,
												ID_Produto_Pai,
												Ref,
												Nome,
												Qtd,
												Valor,
												Valor_Pagar,
												Tipo,
												Estado,
												Valor_Desconto,
												Perc_Desconto,
												Chave,
												Obs
											)
											VALUES
											(
												"'.$new_id.'",
												"'.$cli_id.'",
												"'.$porte_id.'",
												"'.$ID_Produto_Pai.'",
												"",
												"'.$porte[$a]['titulo'].'",
												"'.$qtd.'",
												"'.$porte[$a]['valor'].'",
												"'.($porte[$a]['valor']/1.23).'",
												"0",
												"0",
												"0",
												"0",
												"",
												""
											)';
										$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
										break;
									}
								}
							}
							$PortesCarrinho+=$totalporte;
						}
						else
						{
							//O valor portes total e o valor de portes por encomenda,
							// e for para ter portes por produto temos de ter uma nova flag:
							if($GLOBALS['portes_por_produto']==1)$portes=$portes_total;//este valor é passado para a funcao e ja foi previamente calculado
						}










					}



					//dar baixa de stock
					// $sql='UPDATE products_objs SET obj_stock=obj_stock-'.$qtd.' WHERE obj_type_id="'.$prod_id.'"';
					// Runquery($db_link,$GLOBALS['DB_name'],$sql);
					//remover produtos vistos pelo cliente
				   /*  if($dados_cliente['cli_id'] > 0)
					{
						actualizarProdutoNotificacao($dados_cliente['cli_id'],$prod_id);
					} */
					$i++;
				}

				//Gravar como linha de artigo os dados de uma promoção de carrinho
				if(count($offer_arr['p_html_offers_arr']) > 0)
				{
					for($a=0; $a<count($offer_arr['p_html_offers_arr']); $a++)
					{
						if((isset($_POST[$offer_arr['p_html_offers_arr'][$a]['campo_escolha']]) && $offer_arr['p_html_offers_arr'][$a]['escolha'] > 0
							&& $offer_arr['p_html_offers_arr'][$a]['escolha'] == $_POST[$offer_arr['p_html_offers_arr'][$a]['campo_escolha']]) // PRODUTO ESCOLHIDO PELO CLIENTE
							|| $offer_arr['p_html_offers_arr'][$a]['escolha'] == 0) // OFERTA DE 1 ou Vários
						{
							$sql='INSERT INTO
										encomendas_produtos
										(
											ID_PAI,
											ID_Cliente,
											ID_Produto,
											ID_Produto_Pai,
											Ref,
											Nome,
											Qtd,
											Valor,
											Tipo,
											Estado,
											Valor_Desconto,
											Perc_Desconto,
											Chave,
											Obs
										)
										VALUES
										(
											"'.$new_id.'",
											"'.$cli_id.'",
											"0",
											"0",
											"",
											"'.$offer_arr['p_html_offers_arr'][$a]['DESC'].'",
											"1",
											"'.$offer_arr['p_html_offers_arr'][$a]['VALOR_RETIRADO'].'",
											"3",
											"0",
											"0",
											"0",
											"",
											""
										)
								';
								$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
						}
					}
				}
				//VOUCHERS
				//Gravar como linha de artigo os dados de voucher
				if($GLOBALS['module_voucher'] == 1)
				{
					$i=1;
					while ($i <= $n_voucher)
					{
						$voucher_code=$voucher[$i][1];
						$chave=$voucher[$i][4];
						$prod_id=0;
						$info=get_voucher_info($voucher_code);
						$v_discount=$info['voucher_discount'];
						$nome_prod='';
						$valor_dif=0;
						$tipo=0;
						$ref_prod='';
						if($info['voucher_type'] == 1)
						{
							$prod_id=$info['voucher_prod_id'];
							$info_prod=get_produtc_info($prod_id);
							$nome_prod=getLinguas('key_voucher_oferta',$GLOBALS['site_lang']).': '.$info_prod['prod_nome'];
							$ref_prod=$info_prod['prod_ref'];
							$valor_dif=0;
							$v_discount=100;
						}
						else
						{
							if($info['voucher_type'] == 0 || $info['voucher_type'] == 3)
							{
								$nome_prod=getLinguas('key_voucher_desconto',$GLOBALS['site_lang']);
								$prod_id=$info['voucher_prod_id'];
								$info_prod=get_produtc_info($prod_id);
								$ref_prod=$info_prod['prod_ref'];
								if($info['voucher_type_discount'] == 0)
								{
									$valor_dif=calcula_desconto($totalEncomenda['TOTALCIVA'],$v_discount);
								}
								else
								{
									if($info['voucher_type_discount'] == 1)
									{
										if($v_discount > $totalEncomenda['TOTALCIVA'])
										{
											$valor_dif=$totalEncomenda['TOTALCIVA'];
											$v_discount=100;
										}
										else
										{
											$valor_dif=$v_discount;
											$v_discount=($v_discount * 100) / $totalEncomenda['TOTALCIVA'];
										}
									}
								}
							}

							if ($info['voucher_type'] == 4) // oferta de portes
			                {
			                    $nome_prod = ($info['voucher_title'] != '' ? $info['voucher_title'] : getLinguas('key_voucher_desconto'));
			                    $ref_prod = $info_prod['prod_ref'];
			                    $valor_dif = $PortesCarrinho;
			                    $v_discount = $PortesCarrinho;
			                    $desconto_valor = $PortesCarrinho;
			                    $info['voucher_type_discount']=1;

			                    //Se tem voucher oferta portes
								$voucher_oferta_portes=1;
								$PortesCarrinho=0;
			                }
						}

						$v_discount=0;//Ate descobrir o erro este valor fica a 0

						$sql="INSERT INTO encomendas_produtos(ID_PAI,ID_Cliente,ID_Produto,ID_Produto_Pai,Ref,Nome,Qtd,Valor,Valor_Desconto,Perc_Desconto,Tipo,Estado,Chave,voucher_id,download) VALUES ('$new_id','$cli_id','$prod_id','$ID_Produto_Pai','$ref_prod','$nome_prod',1,0,'$valor_dif','$v_discount','$tipo',0,'$chave','{$info['voucher_id']}','{$info['prod_download']}')";
						Runquery($db_link,$GLOBALS['DB_name'],$sql);
						$i++;
					}
				}

				$result_ids=@array_unique(@array_filter(array_flatten($ID_arr)));
				$Promos_ID='';
				foreach ($result_ids as $key=>$value)
				{
					$Promos_ID .= $value.';';
				}
				$data_limite='';
				$result_prom=@array_filter(array_flatten($DATAFIMCMP_arr));
				if(is_array($result_prom) && !empty($result_prom))
				{
					$min_key=array_search(min($result_prom),$result_prom);
					if($min_key !== false && !empty($result_prom[$min_key]))
					{
						$data_limite=$result_prom[$min_key];
					}
				}

				$tot=$totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] - $totalEncomenda['DESCONTOS'] + $PortesCarrinho + $taxas;

				if(!$GLOBALS['portes_tem_iva']==1)
				{
					$PortesSemIva=$PortesCarrinho/1.23;
				}
				else
				{
					$PortesSemIva=$PortesCarrinho;
				}



				foreach ($totalEncomenda as $key => $value)
				{
					$totalEncomenda[$key] = str_replace(',', '.', $value);
				}
				$tot = str_replace(',', '.', $tot);
				$PortesCarrinho = str_replace(',', '.', $PortesCarrinho);
				$PortesSemIva = str_replace(',', '.', $PortesSemIva);

				$ip=getenv("REMOTE_ADDR");
				$sql="UPDATE encomendas SET TotalDocIva='{$totalEncomenda['TOTALIVA']}',TotalIva='{$totalEncomenda['TOTALIVA']}',TotalDocComIva='$tot',TotalDocSemIva='{$totalEncomenda['TOTALSIVA']}',ValorDesconto='{$totalEncomenda['DESCONTOS']}',Taxas='$taxas',enc_token='$enc_token',Promos_ID='$Promos_ID',Portes='$PortesCarrinho',PortesSemIva='$PortesSemIva',IvaPortes='23',Custos_logisticos='$total_custos_logisticos',IP='$ip'";
				if($data_limite != '')
				{
					$sql .= ",Data_Limite='$data_limite' ";
				}
				$sql .= " WHERE ID='$new_id'";

				// exit;
				Runquery($db_link,$GLOBALS['DB_name'],$sql);
				if($cli_id)
				{
					$sql="UPDATE clientes SET NumEncomendas=NumEncomendas+1 WHERE ID='$cli_id'";
					Runquery($db_link,$GLOBALS['DB_name'],$sql);
				}



				/*echo showInfoEncomenda($new_id,'800',1,'');
				die("testes");*/

				//****************************************************************************
				// AGORA VAMOS APRESENTAR OS METODOS DE PAGAMENTO, ENVIAR EMAIL AO CLIENTE
				//****************************************************************************


				$message_mb='';
				$m_pagamento_tipo=getNomeMetodoPagamentoByTipo_ID($m_metodopagamento); 
				if($m_pagamento_tipo == MP_TP_TRANS_BANCARIA || $m_pagamento_tipo == MP_TP_MB_LUSOPAY || $m_pagamento_tipo == MP_TP_MB_IFTHEN || $m_pagamento_tipo == MP_TP_MB_EASYPAY || $m_pagamento_tipo == MP_TP_MB_SIBS || (defined('MP_TP_MB_EUPAGO') && $m_pagamento_tipo == constant('MP_TP_MB_EUPAGO')) || (defined('MP_TP_PAYSHOP_IFTHEN') && $m_pagamento_tipo == constant('MP_TP_PAYSHOP_IFTHEN')))
				{
					// METODOS DE PAGAMENTO
					tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
					enviarEmailCliente($new_id);
					echo show_alerta2('A sua encomenda foi registada com sucesso.<br>Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>'.$message_mb,'ok2');
					echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id,'800',1,'').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
				}
				elseif($m_pagamento_tipo == MP_TP_MBWAY_SIBS || (defined('MP_TP_MBWAY_EUPAGO') && $m_pagamento_tipo == constant('MP_TP_MBWAY_EUPAGO')) || $m_pagamento_tipo == MP_TP_MBWAY_UNICRE || $m_pagamento_tipo == MP_TP_MBWAY_EASYPAY)
				{
					$mbway_tel=$_POST['mbway_tel'];
					if(!$mbway_tel)
					{
						echo '<script>window.location.href=\''.seo('pages.php?page_id='.$GLOBALS['page_id']).'?erro=1\';</script>';
						return false;
					}
					else
					{
						// METODOS DE PAGAMENTO
						$msg=tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
						if($msg==false)
						{
							echo '<script>window.location.href=\''.seo('pages.php?page_id='.$GLOBALS['page_id']).'?erro=1\';</script>';
							return false;
						}
						else
						{
							echo $msg;
							enviarEmailCliente($new_id);
							echo show_alerta2('A sua encomenda foi registada com sucesso.<br>Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>'.$message_mb,'ok2');
							echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id,'800',1,'').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
						}
					}
				}
				elseif($m_pagamento_tipo == MP_TP_UNICRE)
				{
					$sql='INSERT INTO pagamentos (Nome,Codigo_do_cliente,Referente_factura,Quantia,data,Lingua)
							VALUES ("'.addslashes($m_nome.' '.$m_apelido).'","'.$cli_id.'","'.$new_id.'","'.$tot.'","'.date('Y-m-d H:i:s').'","'.$GLOBALS['site_lang'].'")';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
					echo tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
				}
				elseif($m_pagamento_tipo == MP_TP_MBWAY_LUSOPAY)
				{
					$mbway_tel=$_POST['mbway_tel'];
					$sql='INSERT INTO mbway_lusopay (ID_ENC,amount,currencyCode,operationTypeCode,merchantOprId,Final_status,telMBWay) values ("'.$new_id.'","'.$tot.'","EUR","","","0","'.$mbway_tel.'")';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

					if(!$mbway_tel)
					{
						echo '<script>window.location.href=\''.seo('pages.php?page_id='.$GLOBALS['page_id']).'?erro=1\';</script>';
						return false;
					}
					else
					{
						// METODOS DE PAGAMENTO
						$msg=tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
						if($msg==false)
						{
							echo '<script>window.location.href=\''.seo('pages.php?page_id='.$GLOBALS['page_id']).'?erro=1\';</script>';
							return false;
						}
						else
						{
							echo $msg;
							enviarEmailCliente($new_id);
							echo '<br>';
							echo show_alerta2('A sua encomenda foi registada com sucesso.<br>Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>'.$message_mb,'ok2');
							echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id,'800',1,'').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
						}
					}
				}
				elseif(defined('MP_TP_ONEY') && $m_pagamento_tipo == constant('MP_TP_ONEY'))
				{
					/*$sql='INSERT INTO mbway_lusopay (ID_ENC,amount,currencyCode,operationTypeCode,merchantOprId,Final_status,telMBWay) values ("'.$new_id.'","'.$tot.'","EUR","","","0","'.$dados_cliente['Telefone'].'")';
					$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);*/
					echo tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
				}
				elseif(defined('MP_TP_SEQURA') && $m_pagamento_tipo == MP_TP_SEQURA)
				{
					echo tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
				}
				else
				{
					echo tratarMetodosPagamento($new_id,$m_metodopagamento,number_format($tot,2,'.',''));
					enviarEmailCliente($new_id);
					echo show_alerta2('A sua encomenda foi registada com sucesso.<br>Se tiver alguma questão por favor entre em contacto connosco.<br><br>A confirmação da sua encomenda segue abaixo:<br/>'.$message_mb,'ok2');
					echo '<div class="cart-wrap text-center" id="print_order"><br>'.showInfoEncomenda($new_id,'800',1,'').'<br><center><button onclick="ImprimeConteudo(\'print_order\')" class="btn-form" style="max-width:200px">Imprimir</button></center><br></div>';
				}
			}
		}
	}
	elseif($n_enc > 0)
	{
		/********************************************************************************/
		// Se nao é para gravar a encomenda, vamos apresentar o layout de parameterizacao da encomenda
		/********************************************************************************/

		if(!isset($_POST['reload_cart'])) echo '<div id="shopping_cart_loading" style="position:relative">';

		/********************************************************************************/
		// Vamos gravar ou ler da cache as variaveis submetidas
		$enc_file=$site_shell_url."files/encomendas/".$_SESSION['CLIENTE_ID_TEMP'].'.enc';
		if(isset($_POST) && !empty($_POST['cart_quantity']))
		{
			if(stream_resolve_include_path($enc_file))
			{
				@unlink($enc_file);
				$fh=fopen($enc_file,'w');
				if( $fh == false )
				{echo "ERRO SAVING CACHE";} // HOUVE PROBLEMAS
				else
				{
					fwrite($fh,serialize($_POST));
					fclose($fh);
				}
			}
			else
			{
				$fh=fopen($enc_file,'w');
				if( $fh == false )
				{echo "ERRO SAVING CACHE";} // HOUVE PROBLEMAS
				else
				{
					fwrite($fh,serialize($_POST));
					fclose($fh);
				}
			}
		}
		else
		{
			if(stream_resolve_include_path($enc_file))
			{
				$_POST=unserialize(file_get_contents($enc_file));
			}
		}


		/********************************************************************************/
		// Vamos verificar se o cliente esta logado
		$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
		$rs=new Dataobj;
		if(is_array($dados_cliente))
		{
		   //cliente autenticado
			$m_pais=$dados_cliente['Pais'];
		}


		/********************************************************************************/
		// Vamos definir as variaveis default ou se foram passadas por parametro
		$m_end_faturacao=0;
		$m_end_entrega=0;
		$m_metodoenvio=0;
		$m_metodopagamento=0;
		if(isset($_POST['m_end_faturacao']))$m_end_faturacao=$_POST['m_end_faturacao'];
		if(isset($_POST['m_end_entrega']))$m_end_entrega=$_POST['m_end_entrega'];
		if(isset($_POST['m_metodoenvio']))$m_metodoenvio=$_POST['m_metodoenvio'];
		if(isset($_POST['m_metodopagamento']))$m_metodopagamento=$_POST['m_metodopagamento'];
		$m_pais             = db_real_escape_string($_POST['m_pais']);
		$m2_pais            = db_real_escape_string($_POST['m2_pais']);


		if(!$m_pais && defined('PAIS_ID_DEFAULT') && PAIS_ID_DEFAULT > 0 )$m_pais = PAIS_ID_DEFAULT;
		elseif(!$m_pais && defined('PAIS_DEFAULT') && PAIS_DEFAULT > 0 )$m_pais = PAIS_DEFAULT;

		if(!$m_pais) $m_pais = 165; //portugal é o default

		//*************************************************************************
		//Caso So temos 1 metodo de envio, entao este sera sempre o nosso default:
		if($m_metodoenvio>0)
		{
			$rs=getAllMetodosExpedicao('',1,$m_pais);
			if(!$rs->EOF())
			{
			    if($rs->db_rows==1)$m_metodoenvio=$rs->db_data['ID'];
			}
			$rs->db_close();
		}
		if(!$m_metodoenvio)
		{
			$rs=getAllMetodosExpedicao('',1,$m_pais);
			if(!$rs->EOF())
			{
			    $m_metodoenvio=$rs->db_data['ID'];
			}
			$rs->db_close();
		}
		//*************************************************************************

		//Caso o cliente escolha entrega em local diferente da morada de faturação,temos de ir buscar novos defaults
		if( isset($_POST['m_dadosdiferentes']) && $_POST['m_dadosdiferentes'] == 1)
		{
			if(!$m2_pais && defined('PAIS_ID_DEFAULT'))$m2_pais = PAIS_ID_DEFAULT;
			elseif(!$m2_pais && defined('PAIS_DEFAULT'))$m2_pais = PAIS_DEFAULT;
			elseif(!$m2_pais)$m2_pais = 165;
		}
		else $m2_pais=0;//Caso tenhamos recebido o pais 2 temos de o limpar
		//Se eu estiver logado tenho de obter estes dados de outra forma:
		if($dados_cliente)
		{
			$endEntrega=getEnderecos($dados_cliente['cli_id'],1,$m_end_entrega);
			if (!$endEntrega->EOF())
			{
				$m2_pais=$endEntrega->db_data['Pais'];
			}
			$endEntrega->db_close();
		}

		/********************************************************************************/
		// Vamos atualizar o valor de portes dos artigos, acho que esta parte é redundante, so faz sentido porque nao foram bem calculados antes
		$i=0;
		while($i <= $n_enc)
		{
			$prod_id=$shop[$i][1];
			if($m2_pais > 0)$shop[$i][9]=re_atualiza_portes($prod_id,$shop[$i][9],$m2_pais);
			else $shop[$i][9]=re_atualiza_portes($prod_id,$shop[$i][9],$m_pais);
			$i++;
		}
		/********************************************************************************/
		// Vamos atualizar a encomenda com os valores de portes
		$GLOBALS['shop']=$shop;//Vamos reatualizar as globais
		$_SESSION['ss_shop']=$shop;
		//*******************************************************************************






		?>
			<script type="text/javascript">
			function getCheckedValue(radioObj) {
				if(!radioObj) return "";
				var radioLength=radioObj.length;
				if(radioLength == undefined) {
					if(radioObj.checked) return radioObj.value;
					else return "";
				}
				for (var i=0; i < radioLength; i++) {
					if(radioObj[i].checked) return radioObj[i].value;
				}
				return "";
			}
			<?php if(is_array($dados_cliente)){?>
			function validar_encomenda()
			{
				var validado=true;
				var cor="#ffffe5";
				var cor2="#FFFFFF";
				var campos_obrigatorios=["m_end_faturacao","m_end_entrega"];
				var campos_preenchidos;
				var i=0;
				while (i < campos_obrigatorios.length)
				{
					if(document.forms['encomendas'].elements[campos_obrigatorios[i]]) document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor=cor2;
					i=i + 1;
				}
				i=0;
				while (i < campos_obrigatorios.length)
				{
					if(document.forms['encomendas'].elements[campos_obrigatorios[i]])
					{
						if(document.forms['encomendas'].elements[campos_obrigatorios[i]].value == "")
						{
							campos_preenchidos=1;
							document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor=cor;
						}
					}
					i=i + 1;
				}

				/* A validacao especifica dos meios de pagamento e feita no respetivo componente. */

				if(campos_preenchidos == 1)
				{
					new Apprise("<?php  echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
					validado=false;
					return false;
				}


				if(document.getElementById('metodos_envio'))
				{
					var metodo_envio=getCheckedValue(document.forms['encomendas'].elements['m_metodoenvio']);
					if(metodo_envio==""){ Apprise("<?=getLinguas('key_shop_msg2_envio',$GLOBALS['site_lang'])?>");validado=false;return false;}
				}
				/*
				if($("#m_embrulhar").is(":checked"))
				{
					 if($("#embrulho_msg").val()==""){Apprise("<?=getLinguas('key_shop_embulho',$GLOBALS['site_lang'])?>");validado=false;return false;}
				}
				if($("input[name='ofertas[]']:checked").length>3)
				{
					Apprise("<?=getLinguas('key_shop_ofertas',$GLOBALS['site_lang'])?>");validado=false;return false;
				}
				*/
				if(!$("#m_aceitarcompra").is(":checked"))
				{
					Apprise("<?=getLinguas('key_shop_termos',$GLOBALS['site_lang'])?>"); validado=false;return false;
				}
				if($(".escolhaprod").length > 0)
				{
					var tt=$(".escolhaprod").length;
					var ttx=0;
					$(".escolhaprod input:checked").each(function(){ ttx++;});
					if(tt!=ttx)
					{
						Apprise("Existem ofertas que ainda não escolheu!"); validado=false;return false;
					}
				}
				if(document.getElementById('mbway_tel'))
				{
					//Validar
					if(document.getElementById('mbway_tel').value=='')
					{
						Apprise("Para solicitar o pagamento por MBWay, indique o telemóvel associado à sua aplicação MBWay");

						document.getElementById('mbway_tel').style.backgroundColor=cor;

						validado=false;
						return false;
					}
				}
				if(validado)
				{
					document.getElementById('finalizar').value=1;
					document.encomendas.submit();
				}
			}
			function changeEndereco(tipo,el,val)
			{
				if(val == -1)$(el).show();
				else
				{
					$(el).hide();
					/*if(tipo == 2)reload_shopping_cart();*/
					reload_shopping_cart();
				}
			}
			function validarNovoEndereco(wrapper)
			{
				var cor="#ffffe5";
				var cor2="#FFFFFF";
				var campos_obrigatorios=["m_nome","m_morada","m_codigo_postal","m_cidade","m_pais","m_telefone"];
				var campos_preenchidos=0;
				var params='novo_endereco=1';
				$(wrapper + ' :input').each(function ()
				{
					if($.inArray($(this).attr('name'),campos_obrigatorios) > -1)
					{
						$(this).css('backgroundColor',cor2);
						if($(this).val() == "")
						{
							campos_preenchidos=1;
							$(this).css('backgroundColor',cor);
						}
						else
						{
							params += "&" + $(this).attr('name') + "=" + $(this).val();
						}
					}
					else
					{
						params += "&" + $(this).attr('name') + "=" + $(this).val();
					}
				});
				if(campos_preenchidos == 1)
				{
					new Apprise("<?php  echo getLinguas('key_camposobr',$GLOBALS['site_lang'])?>");
					return false;
				}
				else {
					$.post('<?php echo  $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']);?>',params,function () {
						window.location.href='<?php echo  $GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']);?>';
					});
				}
			}
			<?php }else{ ?>
			function validar_encomenda()
			{
				var validado=true;
				var cor="#ffffe5";
				var cor2="#FFFFFF";
				var campos_preenchidos=validarNovoEndereco();


				/* Obsolete VivaWallet client validation removed: provider is not configured. */

				if(campos_preenchidos == 1)
				{
					new Apprise("<?=getLinguas('key_camposobr'); ?> ");
					validado=false;
					$('html,body').animate({ scrollTop: $("#faturacao-novo-endereco").offset().top},'slow');
					return false;
				}

				if(document.getElementById('metodos_pagamento'))
				{
					var metodo_pagamento=getCheckedValue(document.forms['encomendas'].elements['pagamento']);
					if(metodo_pagamento==""){ Apprise("<?=getLinguas('key_shop_msg2',$GLOBALS['site_lang'])?>"); validado=false;return false;}
				}
				if(document.getElementById('metodos_envio'))
				{
					var metodo_envio=getCheckedValue(document.forms['encomendas'].elements['m_metodoenvio']);
					if(metodo_envio==""){ Apprise("<?=getLinguas('key_shop_msg2_envio',$GLOBALS['site_lang'])?>"); validado=false;return false;}
				}
				/* if($("#m_embrulhar").is(":checked")){
					 if($("#embrulho_msg").val()==""){Apprise("<?=getLinguas('key_shop_embulho',$GLOBALS['site_lang'])?>"); validado=false;return false;}
				 }
				 if($("input[name='ofertas[]']:checked").length>3){
				 Apprise("<?=getLinguas('key_shop_ofertas',$GLOBALS['site_lang'])?>"); validado=false;return false;
				 }
					*/


				if(!$("#m_aceitarcompra").is(":checked"))
				{
					Apprise("<?=getLinguas('key_shop_termos',$GLOBALS['site_lang'])?>"); validado=false;
					return false;
				}
				if($(".escolhaprod").length > 0)
				{
					var tt=$(".escolhaprod").length;
					var ttx=0;
					$(".escolhaprod input:checked").each(function(){ ttx++;});
					if(tt!=ttx)
					{
						Apprise("Existem ofertas que ainda não escolheu!"); validado=false;
						return false;
					}
				}

				if(document.getElementById('mbway_tel'))
				{
					//Validar
					if(document.getElementById('mbway_tel').value=='')
					{
						Apprise("Para solicitar o pagamento por MBWay, indique o telemóvel associado à sua aplicação MBWay");

						document.getElementById('mbway_tel').style.backgroundColor=cor;

						validado=false;
						return false;
					}
				}


				if(validado)
				{
					document.getElementById('finalizar').value=1;
					document.encomendas.submit();
				}
			}
			function validarNovoEndereco()
			{
				var cor="#ffffe5";
				var cor2="#FFFFFF";
				var campos_obrigatorios=["m_nome","m_morada","m_codigo_postal","m_cidade","m_pais","m_email","m2_nome","m2_morada","m2_codigo_postal","m2_cidade","m2_pais","m_telefone"];
				var campos_preenchidos=0;
				var i=0; while(i < campos_obrigatorios.length){ if(document.forms['encomendas'].elements[campos_obrigatorios[i]]) document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor=cor2; i=i+1;  }
				var i=0; while(i < campos_obrigatorios.length){ if(document.forms['encomendas'].elements[campos_obrigatorios[i]]) {if(document.forms['encomendas'].elements[campos_obrigatorios[i]].value==""){ campos_preenchidos=1; document.forms['encomendas'].elements[campos_obrigatorios[i]].style.backgroundColor=cor; }}       i=i+1;  }
				return campos_preenchidos;
			}
			<?php }?>
			</script>
			<?php if(isset($_GET['erro']) && intval($_GET['erro']) == 1){ echo show_alerta2('Por favor verifique os dados da sua encomenda,existem campos que não são válidos!','alert');}?>
			<form action="<?php echo seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']); ?>" method="post" name="encomendas" id="encomendas" class="encomendas">
				<input type="hidden" name="finalizar" id="finalizar" value="0"/>
				<input type="hidden" name="submitform" id="submitform" value="0"/>
				<div class="shop_left_top"> <!-- 2794 -->
					<h1 class="titulo_b"><?=getLinguas('key_shop_title',$GLOBALS['site_lang']); ?></h1>
					<?php if(!$dados_cliente)
					{
					?>
					<!-- DADOS DO CLIENTE 1 -->
					<div class="boxs">
					<div class="info">
						<h5 class="subtitulo_d_black"><?=getLinguas('key_ja_tem_conta',$GLOBALS['site_lang']); ?>? <a href="#" onclick="toggleLoginBox('<?php echo seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']);?>');return false;"><u><?=getLinguas('key_clique_entrar',$GLOBALS['site_lang']); ?></u></a></h5>
						<div class="clear"></div>
						<h5 class="subtitulo_d_black"><?=getLinguas('key_ou',$GLOBALS['site_lang']); ?></h5>
						<div class="clear"></div>
						<h5 class="subtitulo_d_black"><?=getLinguas('key_finalize_preenchendo',$GLOBALS['site_lang']); ?>:</h5>
					</div>
					</div>
					<?php }?>
					<!-- DADOS PARA FATURACAO OU ENTREGA -->
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="icon">
									<i class="fa fa-book"></i>
								</span>
								<span class="text"><?=getLinguas('key_shop_title_factura',$GLOBALS['site_lang']);  if(!isset($_POST['m_dadosdiferentes']) && !$dados_cliente){ echo '/'.getLinguas('key_shop_title_envio2',$GLOBALS['site_lang']); }?> </span>
							</h4>
						</div>
						<div class="panel-body">
							<?php if(is_array($dados_cliente))
							{
							?>
							<p style="font-size:11px"><?=getLinguas('key_shop_title_livro_enderecos',$GLOBALS['site_lang']);?></p>
							<div class="form-group">
								<?php
								$ef=0;
								$rs=getEnderecos($dados_cliente['cli_id'],0);
								?>
								<div style="width:100%;float:left">
									<div style="width:90%;float:left">
										<select title="Informações de Facturação"  class="chzn-select" data-live-search="true" onchange="changeEndereco(1,'#faturacao-novo-endereco',this.value)" name="m_end_faturacao" style="width:100%">
											<?php if($rs->EOF()) {?><option value=""><?=getLinguas('key_escolha_endereco_fact',$GLOBALS['site_lang']);?></option><?php }?>
											<?php
											while (!$rs->EOF())
											{
												$pais=country_name($rs->db_data['Pais']);
												if(!$m_end_faturacao && $ef == 0)
												{
													$m_tel = $rs->db_data['Telefone'];

													$m_end_faturacao = $rs->db_data['ID'];
													echo "<option value=\"{$rs->db_data['ID']}\"  selected='selected'>{$rs->db_data['Nome']} {$rs->db_data['Apelido']},{$rs->db_data['Morada']},{$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']},{$pais}</option>";
												}
												else
												{
													if($m_end_faturacao == $rs->db_data['ID'])$m_tel = $rs->db_data['Telefone'];

													echo "<option value=\"{$rs->db_data['ID']}\" ".(($m_end_faturacao == $rs->db_data['ID']) ? ' selected="selected" ' : '').">{$rs->db_data['Nome']} {$rs->db_data['Apelido']},{$rs->db_data['Morada']},{$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']},{$pais}</option>";
												}
												$ef++;
												$rs->db_next();
											}
											$rs->db_close();
											?>
											<option value="-1"><?=getLinguas('key_novo_endereco',$GLOBALS['site_lang']);?></option>
										</select>
									</div>
									<div style="width:10%;float:left;line-height:30px;padding-left:10px"><a href="p5-registo-utilizador-pt?show_carteira=1&amp;id_endereco=<?=$m_end_faturacao?>">editar</a></div>
								</div>
								<div class="clear"></div>
								<?php if(!$m_tel) echo '<div style="color:#ff0000">Necessita de indicar um numero de telefone, esse campo é obrigatório para as entregas</div>'; ?>
							</div>
							<div id="faturacao-novo-endereco" style="display:none">
								<?php shopShowFormNovoEndereco('#faturacao-novo-endereco'); ?>
							</div>
							<?php }else{?>
							<p style="font-size:11px"><?=getLinguas('key_nova_morada_fatura',$GLOBALS['site_lang']);?> </p>
							<div id="faturacao-novo-endereco" style="display:block">
								<?php shopShowFormNovoEndereco('#faturacao-novo-endereco',1); ?>
								<label for="m_dadosdiferentes" class="customCheckboxDadosDiferentes <?php if(isset($_POST['m_dadosdiferentes']) && !empty($_POST)) echo ' customCheckboxDadosDiferentesOn ';?>"><div class="left"><input type="checkbox" name="m_dadosdiferentes" id="m_dadosdiferentes" value="1" <?php if(isset($_POST['m_dadosdiferentes']) && !empty($_POST)) echo ' checked="checked" ';?>></div><div class="right"><?=getLinguas('key_shop_dadosdiferentes');?></div></label>
								<div class="clear"></div>
								<label for="m_criarconta" class="customCheckboxCriarConta <?php if(!isset($_POST['m_criarconta']) && empty($_POST) || isset($_POST['m_criarconta']) && !empty($_POST)) echo ' customCheckboxCriarContaOn ';?>"><div class="left"><input type="checkbox" name="m_criarconta" id="m_criarconta" value="1" <?php if(!isset($_POST['m_criarconta']) && empty($_POST) || isset($_POST['m_criarconta']) && !empty($_POST)) echo ' checked="checked" ';?>></div><div class="right"><?=getLinguas('key_shop_createaccount',$GLOBALS['site_lang'])?></div></label>
							</div>
							<?php }?>

					<!-- DADOS PARA ENTREGA  -->
					<?php if(is_array($dados_cliente))
					{
					?>
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="icon">
									<i class="fa fa-book"></i>
								</span>
								<span class="text"><?=getLinguas('key_info_envio',$GLOBALS['site_lang']);?></span>
							</h4>
						<!-- <h5 class="subtitulo_d_black"><?=getLinguas('key_info_envio',$GLOBALS['site_lang']);?></h5> -->
						</div>
						<p style="font-size:11px"><?=getLinguas('key_edereco_fatura_ou_novo',$GLOBALS['site_lang']);?></p>
						<div class="form-group">
							<?php
							$ef=0;
							$rs=getEnderecos($dados_cliente['cli_id'],0);
							// echo 'ddd '.$m_end_entrega;
							?>
							<div style="width:100%;float:left">
								<div style="width:90%;float:left">
								<select title="Informações de Envio"  class="chzn-select" data-live-search="true" onchange="changeEndereco(1,'#envio-novo-endereco',this.value)" name="m_end_entrega"  style="width:100%;height:30px">
								<?php if($rs->EOF()) {?><option value=""><?=getLinguas('key_escolha_endereco',$GLOBALS['site_lang']);?></option><?php }?>
									<?php

									while (!$rs->EOF())
									{
										$pais=country_name($rs->db_data['Pais']);
										if($m_end_entrega == 0) $m_pais=$rs->db_data['Pais'];
										elseif($m_end_entrega == $rs->db_data['ID'])
										{
											$m_pais=$rs->db_data['Pais'];
											$m_tel2 = $rs->db_data['Telefone'];
										}
										if(!$m_end_entrega)
										{
											$m_end_entrega = $rs->db_data['ID'];
											$m_tel2 = $rs->db_data['Telefone'];
											echo "<option value=\"{$rs->db_data['ID']}\"  selected='selected'>{$rs->db_data['Nome']} {$rs->db_data['Apelido']},{$rs->db_data['Morada']},{$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']},{$pais}</option>";
										}
										else echo "<option value=\"{$rs->db_data['ID']}\" ".(($m_end_entrega == $rs->db_data['ID']) ? ' selected="selected" ' : '').">{$rs->db_data['Nome']} {$rs->db_data['Apelido']},{$rs->db_data['Morada']},{$rs->db_data['Codigo_Postal']} {$rs->db_data['Cidade']},{$pais}</option>";
										$ef++;
										$rs->db_next();
									}
									$rs->db_close();

									?>
									<option value="-1">Novo endereço</option>
								</select>
							</div>
							<div style="width:10%;float:left;line-height:30px;padding-left:10px"><a href="p5-registo-utilizador-pt?show_carteira=1&amp;id_endereco=<?=$m_end_entrega?>">editar</a></div>
							<div class="clear"></div>
							<?php if(!$m_tel2) echo '<div style="color:#ff0000">Necessita de indicar um numero de telefone, esse campo é obrigatório para as entregas</div>'; ?>
						</div>
						<div class="clear"></div>
						<div id="envio-novo-endereco" style="display:none">
							<?php shopShowFormNovoEndereco('#envio-novo-endereco'); ?>
						</div>
						<?php }elseif(isset($_POST['m_dadosdiferentes'])){?>
						<div class="panel-heading" style="padding: 10px 0;margin-top: 20px">
							<h4 class="panel-title">
								<span class="icon">
									<i class="fa fa-book"></i>
								</span>
								<span class="text"><?=getLinguas('key_info_envio',$GLOBALS['site_lang']);?></span>
							</h4>
						<!-- <h5 class="subtitulo_d_black"><?=getLinguas('key_info_envio',$GLOBALS['site_lang']);?></h5> -->
						</div>
						<p style="font-size:11px"><?=getLinguas('key_nova_morada_entrega',$GLOBALS['site_lang']);?></p>
						<div id="faturacao-novo-endereco2" style="display:block">
							<?php shopShowFormNovoEndereco('#envio-novo-endereco2',1,'2'); ?>
						</div>

					<?php }?>
					</div>
					</div>
					</div>
					<?php




					//calcula encomenda com produtos e com vouchers e com serviços extra mas nao pode ter portes
					$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes


					//****************************************************************************
					//AGORA VAMOS APRESENTAR O LAYOUT QUE PERMITE ESCOLHER OS METODOS DE EXPEDIÇÃO
					//****************************************************************************
					if(!$m2_pais)$m2_pais=$m_pais;


					$portes_adicionais=0;
					if(!isset($GLOBALS['carrinho_conf']) || isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 0)
					{
						if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m2_pais))
						{
							$m_metodoenvio=$metodoEnvioObj['ID'];
							showLayoutMetodosExpedicao($m_metodoenvio,1,$m2_pais);
							// SE NÃO EXISTIREM CONFIGURAÇÕES DE MULTIPORTES ATIVAS VAMOS ATIVAR OS PORTES NORMAIS

							$portes_adicionais=getPortesCarrinho($m_metodoenvio,$m2_pais);

							//echo "portes_adicionais=$portes_adicionais($m_metodoenvio,$m_pais)";

							$valor_portes_gratuitos=getPortesGratuitos();
							if( ($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS']) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
							{
								$portes_adicionais=0;
							}
						}
					}

					/*echo "valor_portes_gratuitos=$valor_portes_gratuitos | ";
					echo "portes_adicionais=$portes_adicionais | ";

					var_dump($totalEncomenda);*/

					//****************************************************************************
					//AGORA VAMOS APRESENTAR O LAYOUT QUE PERMITE ESCOLHER OS METODOS DE PAGAMENTO
					//****************************************************************************
					$taxas_adicionais=0;
					if($metodoPagamentoObj=getEncomendaMetodoPagamento($m_metodopagamento))
					{
						$m_metodopagamento=$metodoPagamentoObj['ID'];
						if($metodoPagamentoObj['metodo_taxa_tipo'] == 1)
						{
							$metodo_taxa2=calcula_desconto($totalEncomenda['TOTALCIVA'],$metodoPagamentoObj['Taxa']);
							$taxas_adicionais += $metodo_taxa2;
						}
						else
						{
							$taxas_adicionais += $metodoPagamentoObj['Taxa'];
						}
						showLayoutMetodosPagamento($m_metodopagamento);
					}

					/*echo 'LOG:<br>';
					echo 'TOTALCIVA='.$totalEncomenda['TOTALCIVA'].'<br>';
					echo 'TOTAL_GARANTIA='.$totalEncomenda['TOTAL_GARANTIA'].'<br>';
					echo 'TOTAL_EXTRAS='.$totalEncomenda['TOTAL_EXTRAS'].'<br>';*/

					//****************************************************************************
					//AGORA VAMOS APRESENTAR O LAYOUT COM OS ARTIGOS E RESPETIVOS TOTAIS
					//****************************************************************************
					linhas_encomenda($totalEncomenda,$n_enc,$shop,$portes_adicionais,$taxas_adicionais);





					//****************************************************************************
					//AGORA VAMOS APRESENTAR O LAYOUT QUE PERMITE INSERIR OBSERVAÇÕES E FINALIZAR A ENCOMENDA
					//****************************************************************************
					if($GLOBALS['module_voucher']>0)
		            {
		                ?>
		                <div class="panel panel-default">
							<div class="panel-heading">
								<h4 class="panel-title">
									<span class="icon">
										<i class="fa fa-shopping-cart"></i>
									</span>
									<span class="text">Voucher </span>
								</h4>
							</div>
							<div class="panel-body">

								<div class="voucher-table">
									<div class="voucher-line">
					                    <div class="voucher-details">

					                            <div style="padding-top: 5px;display:inline-block;"><?php echo getLinguas('key_tem_voucher', $GLOBALS['site_lang']); ?></div>
					                            <input style="font-weight:normal" class="inputregisto vouchercls" name="voucher_add"
					                                   id="m_voucher" type="text" title="<?php echo getLinguas('key_insere_codigo', $GLOBALS['site_lang']); ?>"
					                                   onfocus="if(this.value=='<?php echo getLinguas('key_insere_codigo', $GLOBALS['site_lang']); ?>') this.value=''"
					                                   value="<?php echo getLinguas('key_insere_codigo', $GLOBALS['site_lang']); ?>"/>

					                    </div>
					                </div>
								</div>

							</div>
						</div>




					<?
		            }
		            ?>
					<div class="panel panel-default">
						<div class="panel-heading">
							<h4 class="panel-title">
								<span class="icon">
									<i class="fa fa-book"></i>
								</span>
								<span class="text"><?=getLinguas('key_prod_obs',$GLOBALS['site_lang']);?></span>
							</h4>
						</div>
						<div class="panel-body">
							<div class="form-group">
							<textarea title="Observações" id="m_observacoes" name="m_observacoes" class="form-control textarea-mobile" style="resize:none" onchange="reload_shopping_cart()"><?php if(isset($_POST['m_observacoes'])) {echo $_POST['m_observacoes'];} ?></textarea>
							</div>
							<div class="form-group">
								<div class="checkbox">
									<label for="m_aceitarcompra">
										<input type="checkbox" id="m_aceitarcompra" name="m_aceitarcompra" <?php if(isset($_POST['m_aceitarcompra'])) {
											echo ' checked="checked" ';
										} ?>> <?=getLinguas('txt_aceito_cond',$GLOBALS['site_lang']);?>
										<a class="fancybox2 pc" data-fancybox-type="iframe" href="ajax/condicoes_compra.php">(<?=getLinguas('txt_ver_cond',$GLOBALS['site_lang']);?>)</a>
										<a class="fancy_content mobile"  href="ajax/condicoes_compra.php">(<?=getLinguas('txt_ver_cond',$GLOBALS['site_lang']);?>)</a>
									</label>
								</div>
							</div>
							<div class="form-group">
								<div id="condicoes-compra-mobile" class="condicoes-compra-mobile"><?php show_content($GLOBALS['termos_page_id']); ?></div>
							</div>
						</div>
					</div>

					<div class="clear" style="clear:both"></div>
					<div class="row botoes-cart">
					<!-- <div class="col-md-3 text-center">&nbsp;</div> -->
					<!-- <div class="col-md-3 text-center"><button type="button" class="button3" onclick="ImprimeHtml('shopping-cart')">Imprimir Carrinho</button></div> -->
					<div class="col-md-12 text-center"><input type="button" class="button3 btn-primary" value="<?=getLinguas('key_send_encomenda',$GLOBALS['site_lang']);?>" onclick="validar_encomenda()"></div>
					<!-- <div class="col-md-3 text-center">&nbsp;</div> -->
					</div>
				</div>

			</form>
			<form id="codigo_pro"><input type="hidden" value="" name="voucher_add" id="promo_voucher2"></form>
			<?php

		if(!isset($_POST['reload_cart'])) echo '<div class="clear" style="clear:both"></div></div>';
	}
	else
	{
		echo show_alerta2('<img src="layout/'.PROJETO.'/images/icon-empty-cart.png" alt="" />');
	}
}


function estado_encomenda($estado_id=0,$lingua='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($estado_id == 0 || $estado_id=='') $estado_id=1;
	$return=array();
	$rs2=new DataObj;
	$sql='SELECT ee.Cor,ee.Ordem,ee.Email,ee.Pagamento_recebido,eel.Cabecalho,eel.Cabecalho2,eel.Texto FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI=ee.ID WHERE ee.ID='.$estado_id.' AND eel.Lang="'.$lingua.'"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$Cabecalho=$rs2->db_data['Cabecalho'];
			$Cabecalho2=$rs2->db_data['Cabecalho2'];
			$Ordem=$rs2->db_data['Ordem'];
			$Texto=$rs2->db_data['Texto'];
			$Email=$rs2->db_data['Email'];
			$Pagamento_recebido=$rs2->db_data['Pagamento_recebido'];
			$Cor=str_replace('#','',$rs2->db_data['Cor']);
			$return=array('nivel'=>$Ordem,'titulo'=>$Cabecalho,'titulo2'=>$Cabecalho2,'texto'=>$Texto,'cor'=>$Cor,'envia_mail'=>$Email,'Pagamento_recebido'=>$Pagamento_recebido);
			$rs2->db_next();
		}
	}
	$rs2->db_close();
	return $return;
}
function estados_encomenda($estado_ids=array(),$lingua='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$estado_id='';
	for($a=0; $a<count($estado_ids); $a++)
	{
		if($a==0) $estado_id .= 'ee.Ordem="'.$estado_ids[$a].'"';
		else $estado_id .= ' OR ee.Ordem="'.$estado_ids[$a].'"';
	}
	$rs2=new DataObj;
	$sql='SELECT ee.ID,ee.Cor,ee.Ordem,eel.Cabecalho,eel.Texto FROM encomendas_estados ee INNER JOIN encomendas_estados_lang eel ON eel.ID_PAI=ee.ID WHERE ('.$estado_id.') AND eel.Lang="'.$lingua.'" ORDER BY  ee.Ordem asc';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$ID=$rs2->db_data['ID'];
			$Cabecalho=$rs2->db_data['Cabecalho'];
			$Ordem=$rs2->db_data['Ordem'];
			$Texto=$rs2->db_data['Texto'];
			$Cor=str_replace('#','',$rs2->db_data['Cor']);
			$return[]=array('estado_id'=>$ID,'nivel'=>$Ordem,'titulo'=>$Cabecalho,'texto'=>$Texto,'cor'=>$Cor);
			$rs2->db_next();
		}
	}
	$rs2->db_close();
	return $return;
}
function getNomeMetodoPagamentoTipo($id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;
	$Tipo=0;
	$sql="SELECT Tipo FROM metodos_pagamento mp WHERE mp.ID=$id";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF()) $Tipo=$rs2->db_data['Tipo'];
	$rs2->db_close();
	return $Tipo;
}
function get_stock($prod_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

	$stock=0;
	$sql="SELECT obj_stock FROM products_objs WHERE obj_type_id=".$prod_id;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())$stock=$rs->db_data['obj_stock'];
	$rs->db_close();

	$sql="SELECT Sync FROM products WHERE ID=".$prod_id;
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())$Sync=$rs->db_data['Sync'];
	$rs->db_close();

	if($Sync==1)
	{
		//é um produto proprio entao temos de verificar a reserva de stock
		$sql='SELECT Qtd from reserva_stocks WHERE ID_Produto='.$prod_id.' Order By ID Desc';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())$stock=$stock-$rs->db_data['Qtd'];
		$rs->db_close();
	}
	else
	{
		//Vamos ver se pode encomendar sempre
		if($stock >0 && isset($GLOBALS['produtos_com_stock_nunca_limitar']) && $GLOBALS['produtos_com_stock_nunca_limitar']=1)return 9999;
	}

	//Caso contrario vamos ver quantas quantidades tem o artigo em stock





	return $stock;
}
function verifica_stock($prod_id=0)
{
	// PERMITE FAZER ENCOMENDA DE TODOS OS PRODUTO COM E SEM STOCK
	if((isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_enc_sem_stock'] == 1))
	{
		$deixa=1;
	}
	else
	{
		$deixa=0;
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$sql="SELECT po.obj_stock,p.encomenda_sem_stock
		FROM products_objs po INNER JOIN products p ON p.ID=po.obj_type_id
		WHERE po.obj_type_id=".$prod_id;
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$stock=$rs->db_data['obj_stock'];
			$encomenda_sem_stock=$rs->db_data['encomenda_sem_stock'];
		}
		else
		{
			$stock=0;
		}
		$rs->db_close();
		if($stock > 0 || $encomenda_sem_stock == 1)
		{
			$deixa=1;
		}
	}
	return $deixa;
}
function verifica_qtd($prod_id=0,$qtd_cli=0)
{
	$deixa=0;
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT obj_qtdmin FROM products_objs WHERE obj_type_id=".$prod_id;
	$rs->db_open($db_link,$DB_name,$sql);
	$qtd=0;
	if(!$rs->EOF())
	{
		if($qtd_cli<$rs->db_data['obj_qtdmin'])$qtd=$rs->db_data['obj_qtdmin'];
	}
	$rs->db_close();

	if ($qtd > 0 && $qtd_cli < 0) $deixa = -$qtd;
	elseif ($qtd > 0 && $qtd_cli < $qtd) $deixa = $qtd;
	else  $deixa = $qtd_cli;

	return number_format($deixa,0);
}
function carrinho_small()
{
	//Acho que nao esta em uso
	return;





	global $n_enc,$enc,$shop;
	if($n_enc > 0)
	{
		$i=1;
		$found=0;
		$portes_pagar=0;
		echo '<script>
			function alteraQuantidade(id){
				var valor=$("#quantidade"+id).val();
				$("#actualizar"+id).attr("href","'.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?num="+id+"&qtd="+valor)
			}
		</script>
		<h1>'.getLinguas('key_carrinho1',$GLOBALS['site_lang']).'</h1>
		<table border="0" width="100%" cellspacing="0" cellpadding="0" id="cartContentsDisplay">
		<tr class="tableHeading">
			<th scope="col" width="100" id="cartImageHeading">'.getLinguas('key_imagem1',$GLOBALS['site_lang']).'</th>
			<th scope="col" width="290" id="cartProductNameHeading">'.getLinguas('key_nome',$GLOBALS['site_lang']).'</th>
			<th scope="col" width="290" id="cartProductUpdateHeading">'.getLinguas('key_quantidade',$GLOBALS['site_lang']).'</th>
			<th scope="col" width="90" id="cartProductPriceHeading">'.getLinguas('key_preco',$GLOBALS['site_lang']).'</th>
		</tr>';
		$total=0;
		while ($i <= $n_enc && $found == 0)
		{
			$Preco=0;
			$Nome='';
			$total_desconto=0;
			$portes=0;
			$prod_id=$shop[$i][1];
			$qtd=$shop[$i][2];
			$tipo=$shop[$i][3];
			$chave=$shop[$i][4];
			$prod_obj=$shop[$i][5];
			$opcoes=$shop[$i][6];
			$opcoes_id=0;
			$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
			$garantia_id=0; $garantia_id=$shop[$i][8];
			$porte_id=0; $porte_id = $shop[$i][9];
			$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

			$prod_serv=0; $prod_serv	= $shop[$i][12];
			$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
			$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
			$altura=0; $altura	= $shop[$i][15];
			$largura=0; $largura	= $shop[$i][16];
			$data_realizacao=0; $data_realizacao	=$shop[$i][17];
			$nome_do_ficheiro=0; $nome_do_ficheiro	=$shop[$i][18];

			/*$info=get_produtc_info($prod_id,$prod_obj);
			if($opcoes_id > 0) $info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);*/
			$info=get_produtc_info($prod_id);
			if($opcoes_id > 0)
			{
				$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
				if($tempo_escolhido>0)
				{
					$preco_final_siva=$info['prod_precos']['PVP'];

					$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
					$rs=new DataObj;

					$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
					$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
				    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
				    $opcoes.=' '.$rs->db_data['Nome'];
					$preco_final_siva=$preco_final_siva*$custo_entrega;
					$rs->db_close();

					$info['prod_precos']['PVP']=$preco_final_siva;
				}
			}

			//OUTROS SERVIÇOS ADICIONAIS, POR PRODUTO, EXEMPLO GRAFICA:
			if(isset($prod_serv) && $prod_serv!='' )
			{
				$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
				$rs2=new DataObj;
				$array_servicos=explode(";",$prod_serv);
				foreach($array_servicos as $servico_id)
				{
					if($servico_id>0)
					{
						$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if(!$rs2->EOF())
						{
							$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
							$custo=$rs2->db_data['Custos'];
							$custo=$custo/1.23;
							$margem=$rs2->db_data['margem'];
							$opcoes.=', '.$rs2->db_data['Nome'];
							if($tipo==1)
							{
								$preco_final_siva=$preco_final_siva+$custo;

								$totalciva += $custo;
								//$produto_servicos_extra_valor=$custo;
								//$totalservicos_extra+=$custo;
								//$titulo_servicos_extra=$m_nome;
							}else{
								// vai ficar por fazer as do tipo margem
							}
						}
						$rs2->db_close();
					}
				}

			}


			$total += ($info['prod_precos']['PVP'] * $qtd);
			if($i % 2 == 0)
			{
				$class='even';
			}
			else
			{
				$class='odd';
			}
			echo '<tr class="'.$class.'">
				<td class="cartImage">
					<a href="'.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$prod_id,$prod_id,2).'">
						<img src="'.$info['prd_imgm'].'" alt="">
					</a>
				</td>
				<td class="cartProductName">
					<a href="'.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$prod_id,$prod_id,2).'">'.((isset($opcoes) && $opcoes != '') ? $info['prod_nome'].','.$opcoes : $info['prod_nome']).'</a>
				</td>
				<td class="cartProductUpdate">
					<div class="cartQty">
						<input style="float:left;margin-right:10px" type="text" name="cart_quantity[]" id="quantidade'.$i.'" value="'.$qtd.'" onkeyup="alteraQuantidade('.$i.')" size="2" >
						<input type="hidden" name="products_id[]" value="'.$prod_id.'">
					</div>
					<div class="cartBtn">
						<a style="float:left" id="actualizar'.$i.'" href="'.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?num='.$i.'&qtd='.($qtd - 1).'" class="ui-button">
							<span class="ui-icon ui-icon-actualizar"></span>
							<span style="position:relative;top: -1px;right:5px" class="ui-button-text">'.getLinguas('key_atualizar',$GLOBALS['site_lang']).'</span>
						</a>
						<span style="position:relative;top:5px" class="cartortext">or</span>
						<a style="position:relative;top:5px" href="'.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?del='.$chave.'">remover</a>
					</div>
				</td>
				<td class="cartProductPrice" align="center">'.numberformat($info['prod_precos']['PVP'] * $qtd).'</td>
			</tr>';
			$i++;
		}
		echo '</table>
		<div class="cartTotal">
			<div class="subTotalText">'.getLinguas('key_Subtotal',$GLOBALS['site_lang']).':</div>
			<div class="subTotalPrice">'.numberformat($total).'</div>
		</div>
		<div class="c"></div>
		<a style="float:right;margin-bottom:15px;width:230px" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'\',\'_self\')" class="ui-button">
			<span class="ui-icon ui-icon-triangle"></span>
			<span class="ui-button-text">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</span>
		</a>';
	}
	else
	{
		show_alerta('Carrinho vazio!','erro');
	}
}
function show_wishlist()
{
	echo '<div class="button"><a title="'.getLinguas('key_wishlist_hover',$GLOBALS['site_lang']).'" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_wishlist=1">'.getLinguas('key_wishlist',$GLOBALS['site_lang']).'</a></div>';
}
function show_wishlist_minhaconta()
{
	if($dados=cliente_logado())
	{
		echo '<div class="button2"><a title="'.getLinguas('key_logout',$GLOBALS['site_lang']).'" href="'.seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?logout=1">'.getLinguas('key_logout',$GLOBALS['site_lang']).'</a></div>';
		echo '<div class="button2"><span class="pipe">|</span></div>';
		echo '<div class="button2"><a title="'.getLinguas('key_shop_subtitle',$GLOBALS['site_lang']).'" href="'.seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?sumario=1">'.getLinguas('key_minha_conta',$GLOBALS['site_lang']).'</a></div>';
	}
	else
	{
		echo '<div class="button2"><span class="pipe">|</span></div>';
		echo '<div class="button2"><a title="'.getLinguas('key_shop_subtitle',$GLOBALS['site_lang']).'" href="'.seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?registo=1">'.getLinguas('key_minha_conta',$GLOBALS['site_lang']).'</a></div>';
	}
}
function carrinho_small2()
{
	echo '<div class="cart-bonus">';
	echo '<div class="cartitems" onclick="$(\'#listProdsCart\').toggle();$(\'#listProdsCart2\').hide();">';
	echo '<div class="title">'.getLinguas('key_carrinho_compras').'</div>';
	echo '<div class="floatl">';
	echo '<div class="itens">';
	echo carrinho_small3('qtd');
	echo ' item(s)</div>';
	echo '<div class="itensvalue">';
	echo carrinho_small3('valor');
	echo ' <i class="fa fa-angle-down" aria-hidden="true"></i></div>';
	echo '</div>';
	echo '<div class="clear"></div>';
	echo '</div>';
	carrinho_small_visualiza();
	echo '</div>';
	echo '<div class="cart-bonus2" id="cart-bonus2" style="'.($GLOBALS['n_enc_reparacoes'] > 0 ? 'display:block' : 'display:none').'">';
	echo '<div class="cartitems2" onclick="$(\'#listProdsCart2\').toggle();$(\'#listProdsCart\').hide();">';
	echo '<div class="floatl2">';
	echo '<div class="itens2">Itens (';
	echo carrinho_small3_reparacoes('qtd');
	echo ') <img border="0" alt="" src="layout/'.PROJETO.'/images/search_arrow_grey.png" /></div>';
	echo '<div class="itensvalue2">';
	echo carrinho_small3_reparacoes('valor');
	echo '</div>';
	echo '</div>';
	echo '</div>';
	carrinho_small_visualiza_reparacoes();
	echo '</div>';
}
//Função que mostra o carrinho no topo do site
function carrinho_small3($tipo='qtd')
{
	global $shop,$site_http_url,$registo_page_id,$cart_purchage_id,$site_lang,$key_minishop_products,$key_minishop_product,$key_minishop_hover,$key_wishlist_hover,$key_minishop_hover2,$m_pais,$m_metodoenvio;

	if(!$m_pais) $m_pais = 165; //portugal é o default
	if(!$m_metodoenvio)$m_metodoenvio=$_SESSION['METODO_ENVIO'];

	if($GLOBALS['n_enc'] > 0)
	{
		$total=0;
		if($tipo == 'qtd')
		{
			$qtd=0;
			for ($p=1; $p <= count($shop); $p++)
			{
				$qtd += $shop[$p][2];
			}
			echo '<span class="itensnum">'.$qtd.'</span>';
		}
		elseif($tipo == 'valor')
		{
			/*$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes
			$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $totalEncomenda['TOTAL_CUSTOSLOGISTICOS'];
			echo numberformat($total);*/
			$total=carrinho_small_calcula_total();
			echo numberformat($total, 1);
		}
		elseif($tipo == 'bonus')
		{
			$i=1;
			$found=0;
			$portes_pagar=0;
			while ($i <= $GLOBALS['n_enc'] && $found == 0)
			{
				$prod_id=$shop[$i][1];
				$info=show_prod_points($prod_id);
				$total += $info;
				$i++;
			}
			echo numberformat($total, 1);
		}
	}
	else
	{
		if($tipo == 'qtd')
		{
			echo '<span class="itensnum">0</span>';
		}
		elseif($tipo == 'valor')
		{
			echo numberformat(0, 1);
		}
		elseif($tipo == 'bonus')
		{
			echo numberformat(0, 1);
		}
	}
}
function carrinho_small4()
{
	if($_SESSION['ss_ismobile']==1)return;
	//-------------------------------------

    echo '<div class="search_cart" id="cart_block_top">';
    echo '<div class="cart-bonus">';
    echo '<div class="cartitems" onclick="$(\'#listProdsCart\').toggle();$(\'#listProdsCart2\').hide();">';
    echo '<div class="floatl">';
    echo '<div class="" style="color: #000"><img class="img-carrinho" src="layout/dotbright/images/shopping_cart.svg" width="38px"></div>';
    echo '<div class="itens">';
    echo carrinho_small3('qtd');
    echo '</div>';
    echo '</div>';
    echo '<div class="clear"></div>';
    echo '</div>';
    carrinho_small_visualiza();
    echo '</div>';
    echo '</div>';

	echo '<div class="search_cart" id="cart_block_top" style="'.($GLOBALS['n_enc_reparacoes'] > 0 ? 'display:block' : 'display:none').'">';
    echo '<div class="cart-bonus2" id="cart-bonus2">';
    echo '<div class="cartitems2" onclick="$(\'#listProdsCart2\').toggle();$(\'#listProdsCart\').hide();">';
    echo '<div class="floatl">';
    echo '<div class="" style="color: #000"><img class="img-carrinho" src="layout/dotbright/images/shopping_cart_repair.svg" width="38px"></div>';
    echo '<div class="itens">';
    echo carrinho_small3_reparacoes('qtd');
    echo '</div>';
    echo '</div>';
    echo '<div class="clear"></div>';
    echo '</div>';
    carrinho_small_visualiza_reparacoes();
    echo '</div>';
	echo '</div>';

}
function carrinho_small3_return($tipo='qtd')
{
	global $shop,$site_http_url,$registo_page_id,$cart_purchage_id,$site_lang,$key_minishop_products,$key_minishop_product,$key_minishop_hover,$key_wishlist_hover,$key_minishop_hover2,$m_pais,$m_metodoenvio;

	if(!$m_pais) $m_pais = 165; //portugal é o default
	if(!$m_metodoenvio)$m_metodoenvio=$_SESSION['METODO_ENVIO'];

	$return='';
	if($GLOBALS['n_enc'] > 0)
	{
		$total=0;
		if($tipo == 'qtd')
		{
			$qtd=0;
			for ($p=1; $p <= count($shop); $p++)
			{
				$qtd += $shop[$p][2];
			}
			$return .= '<span class="itensnum">'.$qtd.'</span>';
		}
		elseif($tipo == 'valor')
		{
			/*$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes
			$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $totalEncomenda['TOTAL_CUSTOSLOGISTICOS'];
			$return .=  numberformat($total);*/
			$total=carrinho_small_calcula_total();
			$return .=  numberformat($total);
		}
		elseif($tipo == 'bonus')
		{
			$i=1;
			$found=0;
			$portes_pagar=0;
			while ($i <= $GLOBALS['n_enc'] && $found == 0)
			{
				$prod_id=$shop[$i][1];
				$info=show_prod_points($prod_id);
				$total += $info;
				$i++;
			}
			$return .=  numberformat($total);
		}
	}
	else
	{
		if($tipo == 'qtd')
		{
			$return .=  '<span class="itensnum">0</span>';
		}
		elseif($tipo == 'valor')
		{
			$return .=  numberformat(0);
		}
		elseif($tipo == 'bonus')
		{
			$return .=  numberformat(0);
		}
	}
	return $return;
}
function carrinho_small_calcula_total($hide=0)
{
	global $n_enc,$shop,$site_http_url,$m_pais,$m_metodoenvio;
	if(!$m_pais) $m_pais = 165; //portugal é o default
	if(!$m_metodoenvio)$m_metodoenvio=$_SESSION['METODO_ENVIO'];
	if($n_enc > 0)
	{
		$PortesCarrinho=0;
		$i=1;
		$found=0;
		$portes_pagar=0;
		$n_custos_logisticos=0;
		//$total=0;
		while ($i <= $n_enc && $found == 0)
		{
			$portes=0;
			$Preco=0;
			$Nome='';
			$total_desconto=0;
			$prod_id=$shop[$i][1];
			$qtd=$shop[$i][2];
			$tipo=$shop[$i][3];
			$chave=$shop[$i][4];
			$prod_obj=$shop[$i][5];
			$opcoes=$shop[$i][6];
			$opcoes_id=0;
			$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
			$garantia_id=0; $garantia_id=$shop[$i][8];
			$porte_id=0; $porte_id = $shop[$i][9];
			$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

			$prod_serv=0; $prod_serv	= $shop[$i][12];
			$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
			$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
			$altura=0; $altura	= $shop[$i][15];
			$largura=0; $largura	= $shop[$i][16];
			$data_realizacao=0; $data_realizacao	=$shop[$i][17];
			$nome_do_ficheiro=0; $nome_do_ficheiro	=$shop[$i][18];

			$produto_servicos_extra_valor=0;

			$info=get_produtc_info($prod_id);


			if($opcoes_id > 0)
			{
				//Vamos trocar o preco pelo correto
				$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
				//Vamos aplicar uma margem de urgencia
				if($tempo_escolhido>0)
				{
					$preco_final_siva=$info['prod_precos']['PVP'];
					$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
					$rs=new DataObj;
					$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
					$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
				    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
				    $opcoes.=' '.$rs->db_data['Nome'];
					$preco_final_siva=$preco_final_siva*$custo_entrega;
					$rs->db_close();
					$info['prod_precos']['PVP']=$preco_final_siva;
				}
			}
			//Neste site vamos mostrar os preços sempre com IVA
			$pvp=$info['prod_precos']['PVP'];

			//tem_servicos_extra do tipo grafica
			if(isset($prod_serv) && $prod_serv!='' )
			{
				$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
				$rs2=new DataObj;
				$array_servicos=explode(";",$prod_serv);
				foreach($array_servicos as $servico_id)
				{
					if($servico_id>0)
					{
						$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if(!$rs2->EOF())
						{
							$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
							$custo=$rs2->db_data['Custos'];
							//$custo=$custo/1.23;
							$margem=$rs2->db_data['margem'];
							$titulo_servicos_extra=$rs2->db_data['Nome'];
							if($tipo==1)
							{
								echo '<div class="prod-price">'.$qtd.' x '.$titulo_servicos_extra.' '.numberformat($custo,2). '</div>';
							}else{
								// vai ficar por fazer as do tipo margem
							}
						}
						$rs2->db_close();
					}
				}
			}

			//TEM SERVICOS EXTRA
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
			{
				if($servicos_extra_id > 0)
				{
					$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
					for($a=0; $a<count($servicos_extra); $a++)
					{
						if($servicos_extra_id == $servicos_extra[$a]['id'])
						{
							$produto_servicos_extra_valor=($servicos_extra[$a]['valor'] * $qtd);
							$titulo_servicos_extra=$servicos_extra[$a]['titulo'];
						}
					}
				}
			}

			// TEM EXTENSAO DE GARANTIA
			if($garantia_id > 0 && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1))
			{
				$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
				for($a=0; $a<count($garantia); $a++)
				{
					if($garantia_id == $garantia[$a]['id'])
					{
						$produto_extensao_garantia_valor=($garantia[$a]['valor'] * $qtd);
						$titulo_garantia='Garantia Extra de '.$garantia[$a]['anos'].' '.($garantia[$a]['anos'] > 1 ? 'anos' : 'ano');
					}
				}
			}
			// PORTES POR PRODUTO
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
			{
				$totalporte=0;
				$titulo_porte='';
				$porte=multi_portes_prod($info['prod_id'],$info['prod_precos']['PVP'],$info['prod_peso']);
				if($porte_id > 0)
				{
					for($a=0; $a<count($porte); $a++)
					{
						if($porte_id == $porte[$a]['id'])
						{
							$portes += ($porte[$a]['valor'] * $qtd);
							$totalporte=($porte[$a]['valor'] * $qtd);
							$titulo_porte=$porte[$a]['titulo'];
						}
					}
				}
				elseif($porte_id == 0)
				{
					for($a=0; $a<count($porte); $a++)
					{
						if($a == 0)
						{
							$portes += ($porte[$a]['valor'] * $qtd);
							$porte_id=$porte[$a]['id'];
							$totalporte=($porte[$a]['valor'] * $qtd);
							$titulo_porte=$porte[$a]['titulo'];
							break;
						}
					}
				}
			}
			else
			{
				//O valor portes total e o valor de portes por encomenda,
				// e for para ter portes por produto temos de ter uma nova flag:
				if($GLOBALS['portes_por_produto']==1)$portes=$portes_total;//este valor é passado para a funcao e ja foi previamente calculado
			}


			//MAS SE TIVERMOS SELECCIONADO UM METODO DE LEVANTAMENTO GRATUITO, NÂO HA CUSTOS DE LOGISTICA
			if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
			{
				$custo_logistico=0;
				if($metodoEnvioObj['Gratis']==1)
				{
					$custo_logistico=0;
					$portes=0;
				}
				else
				{
					//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
					{
						$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
						for($a=0; $a<count($custos_logisticos); $a++)
						{
							$n_custos_logisticos++;
							if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $n_custos_logisticos>1)
							{
								//Vamos cobrar apenas metade dos custos logisticos a partir do segundo artigo
								$totalciva += (($custos_logisticos[$a]['valor'] * $qtd)/2);
								$custo_logistico+=(($custos_logisticos[$a]['valor'] * $qtd)/2);
							}
							else
							{
								$totalciva += ($custos_logisticos[$a]['valor'] );
								$custo_logistico+=($custos_logisticos[$a]['valor'] );

								if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $qtd>1)
								{
									$totalciva += (($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
									$custo_logistico+=(($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
								}
								elseif($qtd>1)
								{
									$totalciva += ($custos_logisticos[$a]['valor'] * ($qtd-1));
									$custo_logistico+=($custos_logisticos[$a]['valor'] * ($qtd-1));
								}
							}
						}
					}
				}
			}

			//Se tiver esta flag ativa e se tiver serviços extra nao paga portes nem custos logisticos
			if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium'] == 1)
			{
				if($produto_servicos_extra_valor>0)
				{
					$custo_logistico=0;
					$portes=0;
				}
			}

			//Se tem cuto logitico, troca o valor de portes por este
			if($custo_logistico>0)
			{
				$total_custos_logisticos+=$custo_logistico;
				$portes=$custo_logistico;
			}

			//agora vamos somar os portes deste artigo aos portes acumulados de todos os artigos
			if($portes > 0) $PortesCarrinho+=$portes;
			$i++;
		}

		$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes
		// VAI BUSCAR O VALOR DE PORTES DA ENCOMENDA
		if($totalEncomenda['TEM_PORTES']==1)
		{
			if(!isset($GLOBALS['carrinho_conf']) || isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 0)
			{
				if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
				{
					$PortesCarrinho=getPortesCarrinho($m_metodoenvio,$m_pais);
					$valor_portes_gratuitos=getPortesGratuitos();
					if( ($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] ) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
					{
						$PortesCarrinho=0;
					}
				}
			}
		}
		//Agora vamos verificar se os portes estao abrangidos pela oferta de portes gratis e mostrar o valor de portes no carrinho
		$valor_portes_gratuitos=getPortesGratuitos();
		if($valor_portes_gratuitos>0)
		{
			if(($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS']) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
			{
				$PortesCarrinho=$total_custos_logisticos;
			}
		}
		//Se tem voucher oferta portes
		if($voucher_oferta_portes==1)$PortesCarrinho=0;

		$Portes_total=$PortesCarrinho;

		//$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $totalEncomenda['TOTAL_CUSTOSLOGISTICOS']+$Portes_total;
		$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $Portes_total;
		return $total;
	}
	return 0;
}
function carrinho_small_visualiza($hide=0)
{
	//Funcao de detalhe de artigos quando se clica no carrinho em cima junto aos menus
	//
	if($hide == 0)   echo '<div id="listProdsCart" class="clear">';
	global $n_enc,$shop,$site_http_url,$m_pais,$m_metodoenvio;
	if(!$m_pais) $m_pais = 165; //portugal é o default
	if(!$m_metodoenvio)$m_metodoenvio=$_SESSION['METODO_ENVIO'];

	if($n_enc > 0)
	{


		$PortesCarrinho=0;
		$i=1;
		$found=0;
		$portes_pagar=0;
		$n_custos_logisticos=0;
		//$total=0;
		echo '<div class="prod-list">';
		while ($i <= $n_enc && $found == 0)
		{
			$portes=0;
			$Preco=0;
			$Nome='';
			$total_desconto=0;
			$prod_id=$shop[$i][1];
			$qtd=$shop[$i][2];
			$tipo=$shop[$i][3];
			$chave=$shop[$i][4];
			$prod_obj=$shop[$i][5];
			$opcoes=$shop[$i][6];
			$opcoes_id=0;
			$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
			$garantia_id=0; $garantia_id=$shop[$i][8];
			$porte_id=0; $porte_id = $shop[$i][9];
			$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

			$prod_serv=0; $prod_serv	= $shop[$i][12];
			$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
			$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
			$altura=0; $altura	= $shop[$i][15];
			$largura=0; $largura	= $shop[$i][16];
			$data_realizacao=0; $data_realizacao	=$shop[$i][17];
			$nome_do_ficheiro=0; $nome_do_ficheiro	=$shop[$i][18];

			$produto_servicos_extra_valor=0;

			$info=get_produtc_info($prod_id);


			if($opcoes_id > 0)
			{
				//Vamos trocar o preco pelo correto
				$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
				//Vamos aplicar uma margem de urgencia
				if($tempo_escolhido>0)
				{
					$preco_final_siva=$info['prod_precos']['PVP'];
					$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
					$rs=new DataObj;
					$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
					$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
				    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
				    $opcoes.=' '.$rs->db_data['Nome'];
					$preco_final_siva=$preco_final_siva*$custo_entrega;
					$rs->db_close();
					$info['prod_precos']['PVP']=$preco_final_siva;
				}
			}


			//Neste site vamos mostrar os preços sempre com IVA
			$pvp=$info['prod_precos']['PVP'];
			//Se Neste site vamos mostrar os preços sempre com IVA descomentar:
			//$prod_iva=$info['prod_precos']['IVA'];
			//if($info['prod_precos']['TEM_IVA'] == 1 && $pvp)$pvp=retiraiva($pvp,$prod_iva);
			//$total += ($pvp* $qtd);




			echo '<div class="prod-popup"><a href="'.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$info['prod_id'],$info['prod_id'],2).'">';
			echo '<div class="wp-side-image">
				<div class="image"><img src="'.$info['prd_imgm_cart'].'" alt="'.$info['prod_nome'].'" '.(substr_count($info['prd_imgm_cart'],'no_photo_cart.png') ? 'style="height:68px"' : '' ).'></div>
			</div>';
			echo '<div class="wp-side-text">
				<div class="prod-name">'.$info['prod_nome'].'</div>';
			echo '<div class="prod-price">'.$qtd.' x '.numberformat($pvp,1). '</div>';


			//tem_servicos_extra do tipo grafica
			if(isset($prod_serv) && $prod_serv!='' )
			{
				$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
				$rs2=new DataObj;
				$array_servicos=explode(";",$prod_serv);
				foreach($array_servicos as $servico_id)
				{
					if($servico_id>0)
					{
						$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if(!$rs2->EOF())
						{
							$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
							$custo=$rs2->db_data['Custos'];
							//$custo=$custo/1.23;
							$margem=$rs2->db_data['margem'];
							$titulo_servicos_extra=$rs2->db_data['Nome'];
							if($tipo==1)
							{
								echo '<div class="prod-price">'.$qtd.' x '.$titulo_servicos_extra.' '.numberformat($custo,2). '</div>';
							}else{
								// vai ficar por fazer as do tipo margem
							}
						}
						$rs2->db_close();
					}
				}
			}

			//TEM SERVICOS EXTRA
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
			{
				if($servicos_extra_id > 0)
				{
					$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
					for($a=0; $a<count($servicos_extra); $a++)
					{
						if($servicos_extra_id == $servicos_extra[$a]['id'])
						{
							$produto_servicos_extra_valor=($servicos_extra[$a]['valor'] * $qtd);
							$titulo_servicos_extra=$servicos_extra[$a]['titulo'];

							echo '<div class="prod-price">'.$titulo_servicos_extra.' '.numberformat($produto_servicos_extra_valor,1). '</div>';
						}
					}
				}
			}

			// TEM EXTENSAO DE GARANTIA
			if($garantia_id > 0 && (isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1))
			{
				$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
				for($a=0; $a<count($garantia); $a++)
				{
					if($garantia_id == $garantia[$a]['id'])
					{
						$produto_extensao_garantia_valor=($garantia[$a]['valor'] * $qtd);
						$titulo_garantia='Garantia Extra de '.$garantia[$a]['anos'].' '.($garantia[$a]['anos'] > 1 ? 'anos' : 'ano');
						echo '<div class="prod-price">'.$titulo_garantia.' '.numberformat($produto_extensao_garantia_valor,1). '</div>';
					}
				}
			}
			// PORTES POR PRODUTO
			if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
			{
				$totalporte=0;
				$titulo_porte='';
				$porte=multi_portes_prod($info['prod_id'],$info['prod_precos']['PVP'],$info['prod_peso']);
				if($porte_id > 0)
				{
					for($a=0; $a<count($porte); $a++)
					{
						if($porte_id == $porte[$a]['id'])
						{
							$portes += ($porte[$a]['valor'] * $qtd);
							$totalporte=($porte[$a]['valor'] * $qtd);
							$titulo_porte=$porte[$a]['titulo'];
						}
					}
				}
				elseif($porte_id == 0)
				{
					for($a=0; $a<count($porte); $a++)
					{
						if($a == 0)
						{
							$portes += ($porte[$a]['valor'] * $qtd);
							$porte_id=$porte[$a]['id'];
							$totalporte=($porte[$a]['valor'] * $qtd);
							$titulo_porte=$porte[$a]['titulo'];
							break;
						}
					}
				}
			}
			else
			{
				//O valor portes total e o valor de portes por encomenda,
				// e for para ter portes por produto temos de ter uma nova flag:
				if($GLOBALS['portes_por_produto']==1)$portes=$portes_total;//este valor é passado para a funcao e ja foi previamente calculado
			}


			//MAS SE TIVERMOS SELECCIONADO UM METODO DE LEVANTAMENTO GRATUITO, NÂO HA CUSTOS DE LOGISTICA
			if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
			{
				$custo_logistico=0;
				if($metodoEnvioObj['Gratis']==1)
				{
					$custo_logistico=0;
					$portes=0;
				}
				else
				{
					//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
					{
						$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
						for($a=0; $a<count($custos_logisticos); $a++)
						{
							$n_custos_logisticos++;
							if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $n_custos_logisticos>1)
							{
								//Vamos cobrar apenas metade dos custos logisticos a partir do segundo artigo
								$totalciva += (($custos_logisticos[$a]['valor'] * $qtd)/2);
								$custo_logistico+=(($custos_logisticos[$a]['valor'] * $qtd)/2);
							}
							else
							{
								$totalciva += ($custos_logisticos[$a]['valor'] );
								$custo_logistico+=($custos_logisticos[$a]['valor'] );

								if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $qtd>1)
								{
									$totalciva += (($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
									$custo_logistico+=(($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
								}
								elseif($qtd>1)
								{
									$totalciva += ($custos_logisticos[$a]['valor'] * ($qtd-1));
									$custo_logistico+=($custos_logisticos[$a]['valor'] * ($qtd-1));
								}
							}
						}
					}
				}
			}

			//Se tiver esta flag ativa e se tiver serviços extra nao paga portes nem custos logisticos
			if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium'] == 1)
			{
				if($produto_servicos_extra_valor>0)
				{
					$custo_logistico=0;
					$portes=0;
				}
			}

			//Se tem cuto logitico, troca o valor de portes por este
			if($custo_logistico>0)
			{
				$total_custos_logisticos+=$custo_logistico;
				$portes=$custo_logistico;
			}

			//agora vamos somar os portes deste artigo aos portes acumulados de todos os artigos
			if($portes > 0) $PortesCarrinho+=$portes;




			echo '<div class="c"></div>
			</div></a>
			<a style="position:absolute; top:10px;right:10px" href="'.seo('pages.php?page_id='.$GLOBALS['cart_purchage_id']).'?del='.$chave.'"><img src="layout/'.PROJETO.'/images/close.png" width="10"></a>
			<div class="c"></div></div>';
			$i++;
		}



		$totalEncomenda=calcularTotalEncomenda();//Valores sao devolvidos mas sem portes
		// VAI BUSCAR O VALOR DE PORTES DA ENCOMENDA
		if($totalEncomenda['TEM_PORTES']==1)
		{
			if(!isset($GLOBALS['carrinho_conf']) || isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 0)
			{
				if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
				{
					$PortesCarrinho=getPortesCarrinho($m_metodoenvio,$m_pais);
					$valor_portes_gratuitos=getPortesGratuitos();
					if( ($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] ) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
					{
						$PortesCarrinho=0;
					}
				}
			}
		}

		//$total+=$PortesCarrinho;
		//$Portes_total=$PortesCarrinho+$totalEncomenda['TOTAL_CUSTOSLOGISTICOS'];


		//Agora vamos verificar se os portes estao abrangidos pela oferta de portes gratis e mostrar o valor de portes no carrinho
		$valor_portes_gratuitos=getPortesGratuitos();
		if($valor_portes_gratuitos>0)
		{
			if(($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS']) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
			{
				$PortesCarrinho=$total_custos_logisticos;
			}
		}
		//Se tem voucher oferta portes
		if($voucher_oferta_portes==1)$PortesCarrinho=0;

		$Portes_total=$PortesCarrinho;


		//$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $totalEncomenda['TOTAL_CUSTOSLOGISTICOS']+$Portes_total;
		$total = $totalEncomenda['TOTALCIVA'] + $totalEncomenda['TOTAL_GARANTIA'] + $totalEncomenda['TOTAL_EXTRAS'] + $Portes_total;


		echo '</div>';
		if($Portes_total>0)echo '<div class="total"><div class="label text-left" style="font-size:12px">'.\getLinguas('key_portes',$GLOBALS['site_lang']).':</div><div class="valor text-right" style="font-size:12px">'.numberformat($Portes_total,1).'</div></div>';
		echo '<div class="total"><div class="label text-left">'.\getLinguas('key_total',$GLOBALS['site_lang']).':</div><div class="valor text-right">'.numberformat($total,1).'</div></div>';
		echo '<div class="wp-buttons">';
				echo '<div class="button3" onclick="javascript:window.open(\''.seo("pages.php?page_id=".$GLOBALS['cart_purchage_id']).'\',\'_top\')">'.\getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>
				<div class="c"></div>
			</div>';
	}
	else
	{
		echo '<div class="cart-visual" style="text-align:center">'.\getLinguas('key_carrinho_vazio',$GLOBALS['site_lang']).'</div>';
	}
	if($hide == 0)  echo '</div>';
}
function carrinho_small_visualiza_reparacoes($hide=0)
{
	if($hide == 0) echo '<div id="listProdsCart2" class="clear">';
	global $n_enc_reparacoes,$enc_reparacoes,$shop_reparacoes;
	if($n_enc_reparacoes > 0)
	{
		$i=1;
		$found=0;
		$portes_pagar=0;
		$total=0;
		echo '<div class="prod-list">';
		while ($i <= $n_enc_reparacoes && $found == 0)
		{
			$Preco=0;
			$Nome='';
			$total_desconto=0;
			$portes=0;
			$prod_id=$shop_reparacoes[$i][1];
			$qtd=$shop_reparacoes[$i][2];
			$tipo=$shop_reparacoes[$i][3];
			$chave=$shop_reparacoes[$i][4];
			$prod_obj=$shop_reparacoes[$i][5];
			$opcoes=$shop_reparacoes[$i][6];
			$opcoes_id=0;
			$opcoes_id=$shop_reparacoes[$i][7];
			$info=get_produtc_info($prod_id);
			if($opcoes_id > 0)
			{
				$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
				if($tempo_escolhido>0)
				{
					$preco_final_siva=$info['prod_precos']['PVP'];

					$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
					$rs=new DataObj;

					$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
					$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
				    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
				    $opcoes.=' '.$rs->db_data['Nome'];
					$preco_final_siva=$preco_final_siva*$custo_entrega;
					$rs->db_close();

					$info['prod_precos']['PVP']=$preco_final_siva;
				}
			}

			//OUTROS SERVIÇOS ADICIONAIS, POR PRODUTO, EXEMPLO GRAFICA:
			if(isset($prod_serv) && $prod_serv!='' )
			{
				$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
				$rs2=new DataObj;
				$array_servicos=explode(";",$prod_serv);
				foreach($array_servicos as $servico_id)
				{
					if($servico_id>0)
					{
						$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
						$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
						if(!$rs2->EOF())
						{
							$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
							$custo=$rs2->db_data['Custos'];
							$custo=$custo/1.23;
							$margem=$rs2->db_data['margem'];
							$opcoes.=', '.$rs2->db_data['Nome'];
							if($tipo==1)
							{
								$preco_final_siva=$preco_final_siva+$custo;
								$totalciva += $custo;
							}else{
								// vai ficar por fazer as do tipo margem
							}
						}
						$rs2->db_close();
					}
				}

			}




			$total += ($info['prod_precos']['PVP'] * $qtd);

			if($i > 2) echo '<div class="prod-popup prod-popup-hide" style="display:none">';
			else echo '<div class="prod-popup"><a href="'.seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$info['prod_id'],$info['prod_id'],2).'">';

			echo '<div class="wp-side-image"><div class="image"><img src="'.$info['prd_imgm_cart'].'" alt="'.$info['prod_nome'].'" '.(substr_count($info['prd_imgm_cart'],'no_photo_cart.png') ? 'style="height:68px"' : '' ).'></div></div>';

			echo '<div class="wp-side-text">
					<div class="prod-name">'.$info['prod_nome'].'</div>
					<div class="prod-price">'.$qtd.' x '.numberformat(($info['prod_precos']['PVP'] * $qtd), 1).'</div>
					<div class="c"></div>				
				  </div></a>
			<a style="position:absolute;top:5px;right:0" href="'.seo('pages.php?page_id='.$GLOBALS['cart_reparacoes_purchage_id']).'?del_reparacoes='.$chave.'"><img src="layout/'.PROJETO.'/images/icon-remover-popup-produtos.png" alt="remover" /></a>
			<div class="c"></div></div>';
			$i++;
		}
		echo '</div>';

		echo '<div class="total"><div class="label text-left">'.\getLinguas('key_total',$GLOBALS['site_lang']).':</div><div class="valor text-right">'.numberformat($total,1).'</div></div>';

		echo '<div class="wp-buttons">';
		// echo '<div class="button3" style="margin-right:5px" onclick="$(\'.prod-popup-hide\').toggle();">'.getLinguas('key_sumarioconta_vertudo',$GLOBALS['site_lang']).'</div>&nbsp;';
		echo '<div class="button3" onclick="javascript:window.open(\''.seo("pages.php?page_id=".$GLOBALS['cart_reparacoes_purchage_id']).'\',\'_top\')">'.getLinguas('key_finalizar_encomenda',$GLOBALS['site_lang']).'</div>
				<div class="c"></div>
			</div>';
	} else {
		echo '<div class="cart-visual" style="text-align:center">'.getLinguas('key_carrinho_vazio',$GLOBALS['site_lang']).'</div>';
	}
	if($hide == 0)echo '</div>';
}
function carrinho_small_modify()
{
	global $n_enc,$enc,$shop;
	if($n_enc > 0)
	{
		$i=1;
		$found=0;
		$portes_pagar=0;
		echo '<div class="cart-top2">';
		$total=0;
		echo '<div class="titles"><div class="col1">&nbsp;</div><div class="col2">'.getLinguas('key_titulo',$GLOBALS['site_lang']).'</div><div class="col3">'.getLinguas('key_qtd2',$GLOBALS['site_lang']).'</div><div class="col4">'.getLinguas('key_preco',$GLOBALS['site_lang']).'</div></div>';
		while ($i <= $n_enc && $found == 0)
		{
			$Preco=0;
			$Nome='';
			$total_desconto=0;
			$portes=0;
			$prod_id=$shop[$i][1];
			$qtd=$shop[$i][2];
			$tipo=$shop[$i][3];
			$chave=$shop[$i][4];
			$opcoes=$shop[$i][6];
			$opcoes_id=0;
			$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
			$garantia_id=0; $garantia_id=$shop[$i][8];
			$porte_id=0; $porte_id = $shop[$i][9];
			$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

			$prod_serv=0; $prod_serv	= $shop[$i][12];
			$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
			$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
			$altura=0; $altura	= $shop[$i][15];
			$largura=0; $largura	= $shop[$i][16];
			$data_realizacao=0; $data_realizacao	=$shop[$i][17];
			$nome_do_ficheiro=0; $nome_do_ficheiro	=$shop[$i][18];

			$info=get_produtc_info($prod_id);
			$preco=get_produtc_price($prod_id,$prod_obj);
			if($opcoes_id > 0)
			{
				$preco=get_produtc_price_combo($prod_id,$opcoes_id);
			}
			$modelo=products_objs_info($prod_obj);
			$fam_id=get_pai($info['fam_id']);
			$fam_info=get_family_info($fam_id);
			$total += ($preco['PVP'] * $qtd);
			echo '<div class="titles2"><div class="col1">';
			echo '<div class="image">';
			echo '<img src="'.$info['prd_imgm'].'" alt="" />';
			echo '</div>';
			echo '</div><div class="col2">';
			echo $fam_info['fam_nome'];
			echo '<br />';
			echo $info['prod_nome'].'-'.$modelo;
			echo '</div>
			<div class="col3">
			<div class="ctr" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?qtd='.($qtd - 1).'&num='.$i.'\',\'_self\')"><img src="'.$GLOBALS['site_http_url'].'layout/default/images/menos.png" /></div>
			<div class="qtd">'.$qtd.'</div>
			<div class="ctr" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?qtd='.($qtd + 1).'&num='.$i.'\',\'_self\')"><img src="'.$GLOBALS['site_http_url'].'layout/default/images/mais.png" /></div>
			</div>
			<div class="col4">';
			echo numberformat($preco['PVP'] * $qtd);
			echo '</div>
			<div class="col5" style="cursor:pointer" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['cart_page_id']).'?del='.$chave.'\',\'_self\')"><img src="'.$GLOBALS['site_http_url'].'layout/default/images/del2.png" alt="" /></div>
			</div>';
			$i++;
		}
		echo '<div class="cart-totalx">'.getLinguas('key_total',$GLOBALS['site_lang']).' '.numberformat($total).'</div>';
		if($n_enc > 0)
		{
			echo '<div class="cart-purchase"><div class="left" onclick="window.open(\''.seo('pages.php?page_id='.$GLOBALS['catalog_page_id'].'&cat='.$info['fam_id'],$info['fam_id'],3).'\',\'_self\')">&nbsp;&nbsp;&nbsp;&#60; '.getLinguas('key_backtoproducts',$GLOBALS['site_lang']).'</div><div class="right" onclick="$(\'#formx\').submit();"><img src="'.$GLOBALS['site_http_url'].'layout/default/images/bt_continuar_'.$GLOBALS['site_lang'].'.png" /></div></div>';
		}
		echo '</div>';
	}
}
function array_flatten($array)
{
	$return=array();
	foreach ($array as $key=>$value)
	{
		if(is_array($value))
		{
			$return=array_merge($return,array_flatten($value));
		}
		else
		{
			$return[$key]=$value;
		}
	}
	return $return;
}

function linhas_encomenda($totalEncomenda,$n_enc,$shop,$portes_total=0,$taxas=0)
{
	global $m_metodoenvio,$m_pais;
	global $n_voucher, $voucher,$n_saldo_cartao, $saldo_cartao;
	?>
	<script>
	function addProdutoExtra(prodid) {
		var qtd=1;
		$.ajax({
			type: "GET",
			url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: {prod_add: prodid,prod_add_obj: prodid,prod_add_qtd: qtd}
		}).done(function () {
			reload_shopping_cart();
		});
	}
	function removerVoucherCart(chave) {
		$.ajax({
			type: "GET",
			url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: {delvoucher: chave}
		}).done(function () {
			reload_shopping_cart();
		});
	}
	function removerProdutoCart(chave) {
		$.ajax({
			type: "GET",
			url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: {del: chave}
		}).done(function () {
			reload_shopping_cart();
		});
	}
	function spinnerUpShop(id,prodid,i,garantia,porte,servicos_extra)
	{
		 $.ajax({
		  type: "GET",
		  url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: { prod_add: prodid,prod_add_obj: prodid,prod_add_qtd:1,pos:i,'prod_garantia_add' : garantia ,'prod_porte_add' : porte,'prod_servicos_extra_add' : servicos_extra }
		  }).done(function(){
			reload_shopping_cart();
		  });
	}
	function spinnerDownShop(id,prodid,i,garantia,porte,servicos_extra)
	{
		if(parseInt($("#"+id).val())<=1){
		  $("#"+id).val(1);
		}else{
			$.ajax({
		  type: "GET",
		  url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: { prod_add: prodid,prod_add_obj: prodid,prod_add_qtd:-1,pos:i,'prod_garantia_add' : garantia,'prod_porte_add' : porte,'prod_servicos_extra_add' : servicos_extra}
		  }).done(function(){
			reload_shopping_cart();
		  });
		}
	}
	function ShopQtdUpdate(cur_id,id,prodid,i,garantia,porte,servicos_extra)
	{
		var dif = parseInt($("#"+id).val()) - parseInt($("#"+cur_id).val());
		
		$.ajax({type: "GET",url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
		data: { prod_add: prodid,prod_add_obj: prodid,prod_add_qtd:dif,pos:i,'prod_garantia_add' : garantia ,'prod_porte_add' : porte,'prod_servicos_extra_add' : servicos_extra }}).done(function(){reload_shopping_cart();});			
	}
	function garantia_shop_add(garantia_id,num,prod_id)
	{
		$.ajax({
		  type: "GET",
		  url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: { prod_garantia_add: (($('input[name=garantia_add_'+num+']').is(':checked')==true) ? garantia_id : 0),num : num}
		  }).done(function(){
			reload_shopping_cart();
		  });
	}
	function porte_shop_add(porte_id,num,prod_id)
	{
		$.ajax({
		  type: "GET",
		  url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: { prod_porte_add: (($('input[name=porte_add_'+num+']').is(':checked')==true) ? porte_id : 0),num : num}
		  }).done(function(){
			reload_shopping_cart();
		  });
	}
	function servicos_extra_shop_add(servicos_extra_id,num,prod_id)
	{
		$.ajax({
		  type: "GET",
		  url: "<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>",
			data: { prod_servicos_extra_add: (($('input[name=servicos_extra_add_'+num+']').is(':checked')==true) ? servicos_extra_id : 0),num : num}
		  }).done(function(){
			reload_shopping_cart();
		  });
	}
	
	function opc(id)
	{

		if($('#'+id).is(':visible')){$('#'+id).hide();}
		else{$('.tab-hidden').hide();$('#'+id).show();}
		return false;
	}
	<?php if(isset($_POST['submitform']) && $_POST['submitform'] > 0){?>$(document).ready(function () {  if($(".encomendas").length > 0) window.scrollTo(0,<?php echo $_POST['submitform'];?>);});<?php }?>
	</script>
	<div class="panel panel-default">
		<div class="panel-heading">
			<h4 class="panel-title">
				<span class="icon">
					<i class="fa fa-shopping-cart"></i>
				</span>
				<span class="text"><?=getLinguas('key_titulo_resumo_enc',$GLOBALS['site_lang'])?> </span>
			</h4>
		</div>
		<div class="panel-body">

			<div class="shopping-cart" id="shopping-cart">
				<div class="column-labels">
				<label class="product-image"><?=getLinguas('key_imagem',$GLOBALS['site_lang'])?></label>
				<label class="product-details"><?=getLinguas('key_prod_cart',$GLOBALS['site_lang'])?></label>
				<label class="product-price"><?=getLinguas('key_preco',$GLOBALS['site_lang'])?></label>
				<label class="product-quantity"><?=getLinguas('key_quantidade',$GLOBALS['site_lang'])?></label>
				<label class="product-removal"><?=getLinguas('key_remover',$GLOBALS['site_lang'])?></label>
				<label class="product-line-price"><?=getLinguas('key_total',$GLOBALS['site_lang'])?></label>
				</div>
				<?php

				$total_extensao_garantia_valor=0;
				$totalservicos_extra=0;
				$total_custos_logisticos=0;
				$n_custos_logisticos=0;
				$PortesCarrinho=0;

				$i=1;
				$found=0;
				while ($i <= $n_enc && $found == 0)
				{
					$portes=0;
					$custo_logistico=0;

					$prod_id=$shop[$i][1];
					$qtd=$shop[$i][2];
					$tipo=$shop[$i][3];
					$chave=$shop[$i][4];
					$prod_obj=$shop[$i][5];
					$opcoes=$shop[$i][6];
					$opcoes_id=0;
					$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
					$garantia_id=0; $garantia_id=$shop[$i][8];
					$porte_id=0; $porte_id=$shop[$i][9];
					$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];
					$prod_serv=0; $prod_serv	= $shop[$i][12];
					$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
					$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
					$altura=0; $altura	= $shop[$i][15];
					$largura=0; $largura	= $shop[$i][16];
					$data_realizacao=0; $data_realizacao	=$shop[$i][17];
					$nome_do_ficheiro=0; $nome_do_ficheiro	=$shop[$i][18];

					$info=get_produtc_info($prod_id);
					if($opcoes_id > 0)
					{
						$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
						if($tempo_escolhido>0)
						{
							$preco_final_siva=$info['prod_precos']['PVP'];
							$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
							$rs=new DataObj;
							$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
							$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
						    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
						    $opcoes.=' '.$rs->db_data['Nome'];
							$preco_final_siva=$preco_final_siva*$custo_entrega;
							$rs->db_close();
							$info['prod_precos']['PVP']=$preco_final_siva;
						}
					}

					//OUTROS SERVIÇOS ADICIONAIS, POR PRODUTO, EXEMPLO GRAFICA:
					if(isset($prod_serv) && $prod_serv!='' )
					{
						$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
						$rs2=new DataObj;
						$array_servicos=explode(";",$prod_serv);
						foreach($array_servicos as $servico_id)
						{
							if($servico_id>0)
							{
								$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
								$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
								if(!$rs2->EOF())
								{
									$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
									$custo=$rs2->db_data['Custos'];
									$margem=$rs2->db_data['margem'];
									$opcoes.=', '.$rs2->db_data['Nome'];
									if($tipo==1)
									{
										$info['prod_precos']['PVP'] += $custo;

									}else{
										// vai ficar por fazer as do tipo margem
									}
								}
								$rs2->db_close();
							}
						}
					}

					$valorqtd=$info['prod_precos']['PVP'] * $qtd;
					$oferta=0;
					// PROMOCOES QUE O PRODUTO TEM
					$arr_prom=showSalesPromotions($GLOBALS['proms'],$prod_id,$valorqtd);

					// EXTENSAO DE GARANTIA
					$produto_extensao_garantia_valor=0;
					$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
					$titulo_garantia='Sem garantia extra';
					if($garantia_id > 0)
					{
						for($a=0; $a<count($garantia); $a++)
						{
							if($garantia_id == $garantia[$a]['id'])
							{
								$totalciva += ($garantia[$a]['valor'] * $qtd);
								$produto_extensao_garantia_valor=($garantia[$a]['valor'] * $qtd);
								$total_extensao_garantia_valor+=$produto_extensao_garantia_valor;
								$titulo_garantia='Garantia Extra de '.$garantia[$a]['anos'].' '.($garantia[$a]['anos'] > 1 ? 'anos' : 'ano');
							}
						}
					}

					//SERVIÇOS EXTRA
					$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
					$produto_servicos_extra_valor=0;
					$titulo_servicos_extra='Sem serviços extra';
					if($servicos_extra_id > 0)
					{
						for($a=0; $a<count($servicos_extra); $a++)
						{
							if($servicos_extra_id == $servicos_extra[$a]['id'])
							{
								$totalciva += ($servicos_extra[$a]['valor'] * $qtd);
								$produto_servicos_extra_valor=($servicos_extra[$a]['valor'] * $qtd);
								$totalservicos_extra+=$produto_servicos_extra_valor;
								$titulo_servicos_extra=$servicos_extra[$a]['titulo'];
							}
						}
					}

					// PORTES POR PRODUTO
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
					{
						$totalporte=0;
						$titulo_porte='';
						$porte=multi_portes_prod($info['prod_id'],$info['prod_precos']['PVP'],$info['prod_peso']);
						if($porte_id > 0)
						{
							for($a=0; $a<count($porte); $a++)
							{
								if($porte_id == $porte[$a]['id'])
								{
									$portes += ($porte[$a]['valor'] * $qtd);
									$totalporte=($porte[$a]['valor'] * $qtd);
									$titulo_porte=$porte[$a]['titulo'];
								}
							}
						}
						elseif($porte_id == 0)
						{
							for($a=0; $a<count($porte); $a++)
							{
								if($a == 0)
								{
									$portes += ($porte[$a]['valor'] * $qtd);
									$porte_id=$porte[$a]['id'];
									$totalporte=($porte[$a]['valor'] * $qtd);
									$titulo_porte=$porte[$a]['titulo'];
									break;
								}
							}
						}
					}
					else
					{
						//O valor portes total e o valor de portes por encomenda,
						// e for para ter portes por produto temos de ter uma nova flag:
						if($GLOBALS['portes_por_produto']==1)$portes=$portes_total;//este valor é passado para a funcao e ja foi previamente calculado
					}

					//echo "portes_total = $portes_total";

					//MAS SE TIVERMOS SELECCIONADO UM METODO DE LEVANTAMENTO GRATUITO, NÂO HA CUSTOS DE LOGISTICA
					if($metodoEnvioObj=getEncomendaMetodoExpedicao($m_metodoenvio,1,$m_pais))
					{
						if($metodoEnvioObj['Gratis']==1)
						{
							$custo_logistico=0;
							$portes=0;
						}
						else
						{
							//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
							if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
							{
								$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
								for($a=0; $a<count($custos_logisticos); $a++)
								{
									$n_custos_logisticos++;
									if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $n_custos_logisticos>1)
									{
										//Vamos cobrar apenas metade dos custos logisticos a partir do segundo artigo
										$totalciva += (($custos_logisticos[$a]['valor'] * $qtd)/2);
										$custo_logistico+=(($custos_logisticos[$a]['valor'] * $qtd)/2);
									}
									else
									{
										$totalciva += ($custos_logisticos[$a]['valor'] );
										$custo_logistico+=($custos_logisticos[$a]['valor'] );

										if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $qtd>1)
										{
											$totalciva += (($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
											$custo_logistico+=(($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
										}
										elseif($qtd>1)
										{
											$totalciva += ($custos_logisticos[$a]['valor'] * ($qtd-1));
											$custo_logistico+=($custos_logisticos[$a]['valor'] * ($qtd-1));
										}
									}
								}
							}
						}
					}

					//Se tiver esta flag ativa e se tiver serviços extra nao paga portes nem custos logisticos
					if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium'] == 1)
					{
						//se tem entrega especial nao pode cobrar portes neste artigo
						if($produto_servicos_extra_valor>0)
						{
							$custo_logistico=0;
							$portes=0;
						}
					}

					//Se tem cuto logitico, troca o valor de portes por este
					if($custo_logistico>0)
					{
						$total_custos_logisticos+=$custo_logistico;
						$portes=$custo_logistico;
					}


					?>
					<div class="product" <?php  /* echo ($arr_prom['p_valor'] > 0 ? 'style="border-bottom:none"' : ''); */?>>
					<div class="product-image"><img src="<?php echo $info['prd_imgm_new']; ?>" <?php echo (substr_count($info['prd_imgm_new'],'no_photo_cart.png') ? 'style="height:100px"' : '' ); ?>></div>
					<div class="product-details">
						<div class="product-title"><?php echo $info['prod_ref']; ?></div>
						<p class="product-description"><?php

							//TROT:
							if($GLOBALS['tabela_medidas']==1)
							{
								//echo $opcoes."<br>";
								if(isset($opcoes) && $opcoes != '' && $opcoes != '')
								{
									if(substr_count($opcoes,'TROT:')>0)
									{
										$info1=explode(';', $opcoes);
										$dados_peca = $info1[0];
										$dados_peca2 = explode('TROT:',$dados_peca);
										//echo $dados_peca2[1]."<br>";

										echo "Tamanho ".$dados_peca2[1]."<br>";

										$users=$info1[1];
										$info2=explode(',', $users);



										$a=0;
										$b=count($info2);
										while($a<$b)
										{
											$info3=str_replace('_id_','',$info2[$a]);
											$info4=explode('_qtd_',$info3);

											$user_id=$info4[0];
											$user_qtd=$info4[1];

											if($user_id>0)
											{
												if($user_qtd>1)echo "» ".show_user_name($user_id).", ".$user_qtd." unidades <br>";
												else echo "» ".show_user_name($user_id).", ".$user_qtd." unidade <br>";
											}

											$a++;
										}
									}
									else echo rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'].','.$opcoes : $info['prod_nome']),',');
								}
							}
							else echo rtrim(((isset($opcoes) && $opcoes != '') ? $info['prod_nome'].','.$opcoes : $info['prod_nome']),',');

						?></p>
					</div>
					<div class="product-price"><?php

					echo numberformat($info['prod_precos']['PVP'],1); ?> <?php

					if($info['prod_precos']['TEM_IVA'] == 0)
					{
						echo '<br><span style="font-size:11px">'.$info['prod_precos']['IVA_TXT'].'</span>';
					} ?></div>
					<div class="product-quantity">
						<div class="produto_qtd">
							<input type="hidden" id="cur_prod_qtd<?php echo $i?>" value="<?php echo $qtd?>" />

							<input onchange="ShopQtdUpdate('cur_prod_qtd<?php echo $i?>','prod_qtd<?php echo $i?>','<?php echo $prod_id?>','<?php echo $i?>','<?php echo $garantia_id?>','<?php echo $porte_id?>','<?php echo $servicos_extra_id?>')" class="HvMd"  name="cart_quantity[]" id="prod_qtd<?php echo $i?>" value="<?php echo $qtd?>"  style="float:left;padding:0;text-indent:0;text-align:center" />
							<div class="spinnerwrapper">
								<div class="spinnerup" onclick="spinnerUpShop('prod_qtd<?php echo $i?>','<?php echo $prod_id?>','<?php echo $i?>','<?php echo $garantia_id?>','<?php echo $porte_id?>','<?php echo $servicos_extra_id?>');"></div>
								<div class="spinnerdown" onclick="spinnerDownShop('prod_qtd<?php echo $i?>','<?php echo $prod_id?>','<?php echo $i?>','<?php echo $garantia_id?>','<?php echo $porte_id?>','<?php echo $servicos_extra_id?>');"></div>
							</div>
							<input type="hidden" name="products_id[]" value="<?php echo $prod_id?>" />
						</div>
					</div>
					<div class="product-removal"><button class="remove-product" onclick="removerProdutoCart('<?php echo $chave?>');return false;">X</button></div>
					<div class="product-line-price"><?php echo ($arr_prom['p_valor'] != ($info['prod_precos']['PVP'] * $qtd) ? '<strike>'.numberformat(($info['prod_precos']['PVP'] * $qtd), 1).'</strike>' : numberformat(($info['prod_precos']['PVP'] * $qtd),1)); ?></div>
					<?php
					if(!empty($garantia) && count($garantia) > 0)
					{
						if($garantia_id == 0){ $sz='color:#378936;font-weight:bold;margin-bottom:0;padding:5px 10px;border:none;background:#EEE';}else{ $sz='margin-bottom:0;padding:5px 10px;border:none;margin-bottom:0';}
						//if($garantia_id == 0){ $sz2='color:#000;font-weight:bold'; $seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
						if($garantia_id == 0){ $sz2='color:#378936;font-weight:bold'; $seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
						?>
							<div class="clear"></div>
							<div class="product2" onclick="opc('garant<?php echo $prod_id?>');">
								<div class="product-image">&nbsp;</div>
								<div><div style="font-size: 14px;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?=getLinguas('key_ext_garantia',$GLOBALS['site_lang']);?>: <span style="font-size:12px; color:#000"><?php echo $titulo_garantia;?> (<?php echo numberformat($produto_extensao_garantia_valor);?>)</span> - <span class="alterar"><?=getLinguas('key_alterar',$GLOBALS['site_lang']);?></span></div></div>
							</div>
							<div class="tab-hidden" style="display:none" id="garant<?php echo $prod_id?>">
								<div class="product2" style="<?php echo $sz?>">
									<div class="product-image">&nbsp;</div>
									<div class="product-details">
										<div class="clear"></div>
										<div class="product-title" style="font-size: 12px;"><?=getLinguas('key_sem_ext_garantia',$GLOBALS['site_lang']);?></div>
									</div>
									<div class="product-price">&nbsp;</div>
									<div class="product-quantity">&nbsp;</div>
									<div class="product-removal"><label class="lbl_radio" for="garantia_<?php echo $i?>" style="width:auto;padding-right:20px;padding-bottom:0;background-position:right;font-size: 10px;margin-bottom:0;<?php echo $sz2?>"><input class="garantia_button" name="garantia_add_<?php echo $i?>" <?php if($garantia_id == 0){ echo 'checked="checked"';} ?> id="garantia_<?php echo $i?>" type="radio" value="0" onclick="garantia_shop_add('0','<?php echo $i;?>','<?php echo $prod_id?>')" /><?php echo $seleted;?></label></div>
									<div class="product-line-price">&nbsp;</div>
								</div>
								<?php
								for($a=0; $a<count($garantia); $a++)
								{
									if($garantia_id > 0 && $garantia_id == $garantia[$a]['id']){ $sz='color:#000;font-weight:bold;padding:5px 10px;margin-bottom:0;background:#c0c0c0';}else{ $sz='padding:5px 10px;margin-bottom:0;';}
									//if($garantia_id > 0 && $garantia_id == $garantia[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
									if($garantia_id > 0 && $garantia_id == $garantia[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
									?>
								<div class="clear"></div>
								<div class="product2" style="<?php echo $sz?>">
									<div class="product-image">&nbsp;</div>
										<div class="product-details">
											<div class="product-title">Garantia Extra de <?php echo $garantia[$a]['anos'].($garantia[$a]['anos'] > 1 ? ' anos' : ' ano');?> <?php /*<span class="termos">(Li e aceito os <a href="<?php echo $garantia[$a]['termos'];?>" target="_blank"><u>Termos e Condições</u></a>)</span>*/?></div>
										</div>
										<div class="product-price"><?php echo numberformat($garantia[$a]['valor'])?></div>
										<div class="product-quantity text-center">&nbsp;</div>
										<div class="product-removal"><label class="lbl_radio" for="garantia_<?php echo $garantia[$a]['id'];?>_<?php echo $i?>" style="width:auto;padding-right:20px;padding-bottom:0;background-position:right;font-size: 10px;margin-bottom:0;font-weight:normal;<?php echo $sz2?>"><input class="garantia_button" name="garantia_add_<?php echo $i?>" <?php if($garantia_id > 0 && $garantia_id == $garantia[$a]['id']){ echo 'checked="checked"';} ?> id="garantia_<?php echo $garantia[$a]['id'];?>_<?php echo $i?>" type="radio" value="<?php echo $garantia[$a]['id'];?>" onclick="garantia_shop_add('<?php echo $garantia[$a]['id'];?>','<?php echo $i;?>','<?php echo $prod_id?>')" /><?php echo $seleted;?></label></div>
										<div class="product-line-price"><?php echo numberformat($garantia[$a]['valor'] * $qtd)?>&nbsp;</div>
									</div><?php
								}
									?><div class="clear"></div>
								</div><?php
					}
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_multiportes'] == 1)
					{
						if(!empty($porte) && count($porte) > 0)
						{
							if($porte_id == 0){ $sz='color:#378936;font-weight:bold;margin-bottom:0;padding:5px 10px;border:none';}else{ $sz='margin-bottom:0;padding:5px 10px;border:none;margin-bottom:0';}
							if($porte_id == 0){ $sz2='color:#378936;font-weight:bold'; $seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
							//if($porte_id == 0){ $sz2='color:#000;font-weight:bold'; $seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
							?>
								<div class="clear"></div>
								<div class="product2" onclick="opc('port<?php echo $prod_id?>');">
									<div class="product-image">&nbsp;</div>
									<div><div style="font-size: 14px;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?=getLinguas('key_entrega_produto',$GLOBALS['site_lang']);?>: <span style="font-size:12px; color:#000"><?php echo $titulo_porte;?> (<?php echo numberformat($totalporte);?>)</span> - <span class="alterar"><?=getLinguas('key_alterar',$GLOBALS['site_lang']);?></span></div></div>
								</div>
								<div class="tab-hidden" style="display:none" id="port<?php echo $prod_id?>">
							<?php
							for($a=0; $a<count($porte); $a++)
							{
								if($porte_id > 0 && $porte_id == $porte[$a]['id']){ $sz='color:#000;font-weight:bold;padding:5px 10px;margin-bottom:0;background:#c0c0c0';}else{ $sz='padding:5px 10px;margin-bottom:0';}
								//if($porte_id > 0 && $porte_id == $porte[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
								if($porte_id > 0 && $porte_id == $porte[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
							?>
								<div class="clear"></div>
								<div class="product2" style="<?php echo $sz?>">
									<div class="product-image">&nbsp;</div>
									<div class="product-details">
										<div class="product-title"><?php echo $porte[$a]['titulo'];?><?/* <span class="termos">(<a href="<?php echo $porte[$a]['termos'];?>" target="_blank"><u><?=getLinguas('key_termos',$GLOBALS['site_lang']);?></u></a>)</span>*/?></div>
									</div>
									<div class="product-price"><?php echo numberformat($porte[$a]['valor'])?></div>
									<div class="product-quantity text-center">&nbsp;</div>
									<div class="product-removal"><label class="lbl_radio" for="porte_<?php echo $porte[$a]['id'];?>_<?php echo $i?>" style="width:auto;padding-right:20px;padding-bottom:0;background-position:right;font-size: 10px;margin-bottom:0;font-weight:normal;<?php echo $sz2?>"><input class="porte_button" name="porte_add_<?php echo $i?>" <?php if($porte_id > 0 && $porte_id == $porte[$a]['id']){ echo 'checked="checked"';} ?> id="porte_<?php echo $porte[$a]['id'];?>_<?php echo $i?>" type="radio" value="<?php echo $porte[$a]['id'];?>" onclick="porte_shop_add('<?php echo $porte[$a]['id'];?>','<?php echo $i;?>','<?php echo $prod_id?>')" /><?php echo $seleted;?></label></div>
									<div class="product-line-price"><?php echo numberformat($porte[$a]['valor'] * $qtd)?>&nbsp;</div>
								</div>

							<?php
							}
							?><div class="clear"></div></div><?php
						}
					}
					if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
					{
						if(!empty($servicos_extra) && count($servicos_extra) > 0)
						{
							if($servicos_extra_id == 0){ $sz='color:#378936;font-weight:bold;margin-bottom:0;padding:5px 10px;border:none';}else{ $sz='margin-bottom:0;padding:5px 10px;border:none;margin-bottom:0';}
							//if($servicos_extra_id == 0){ $sz2='color:#000;font-weight:bold'; $seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
							if($servicos_extra_id == 0){ $sz2='color:#378936;font-weight:bold'; $seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
							?>
								<div class="clear"></div>
								<div class="product2" onclick="opc('servic<?php echo $prod_id?>');">
									<div class="product-image">&nbsp;</div>
									<div><div style="font-size: 14px;"><i class="fa fa-caret-down" aria-hidden="true"></i> <?=getLinguas('key_servicos_extra',$GLOBALS['site_lang']);?>: <span style="font-size:12px; color:#000"><?php echo $titulo_servicos_extra;?> (<?php echo numberformat($produto_servicos_extra_valor);?>)</span> - <span class="alterar"><?=getLinguas('key_alterar',$GLOBALS['site_lang']);?></span></div></div>
								</div>
								<div class="tab-hidden" style="display:none" id="servic<?php echo $prod_id?>">
									<div class="product2" style="<?php echo $sz?>">
										<div class="product-image">&nbsp;</div>
										<div class="product-details">
											<div class="clear"></div>
											<div class="product-title" style="font-size: 12px;"><?=getLinguas('key_sem_servicos_extra',$GLOBALS['site_lang']);?></div>
										</div>
										<div class="product-price">&nbsp;</div>
										<div class="product-quantity">&nbsp;</div>
										<div class="product-removal"><label class="lbl_radio" for="servicos_extra_<?php echo $i?>" style="width:auto;padding-right:20px;padding-bottom:0;background-position:right;font-size: 10px;margin-bottom:0;<?php echo $sz2?>"><input class="servicos_extra_button" name="servicos_extra_add_<?php echo $i?>" <?php if($servicos_extra_id == 0){ echo 'checked="checked"';} ?> id="servicos_extra_<?php echo $i?>" type="radio" value="0" onclick="servicos_extra_shop_add('<?php echo $servicos_extra[$a]['id'];?>','<?php echo $i;?>','<?php echo $prod_id?>')" /><?php echo $seleted;?></label></div>
										<div class="product-line-price">&nbsp;</div>
									</div>
							<?php
							for($a=0; $a<count($servicos_extra); $a++)
							{
								if($servicos_extra_id > 0 && $servicos_extra_id == $servicos_extra[$a]['id']){ $sz='color:#000;font-weight:bold;padding:5px 10px;margin-bottom:0;background:#c0c0c0';}else{ $sz='padding:5px 10px;margin-bottom:0';}
								//if($servicos_extra_id > 0 && $servicos_extra_id == $servicos_extra[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SELECIONADO';}else{ $sz2='font-weight:normal';$seleted='SELECIONAR';}
								if($servicos_extra_id > 0 && $servicos_extra_id == $servicos_extra[$a]['id']){ $sz2='color:#000;font-weight:bold;';$seleted='SIM';}else{ $sz2='font-weight:normal';$seleted='NÃO';}
								// echo $servicos_extra_id;
								// echo '....';
								// var_dump($servicos_extra[$a]['id']);
							?>
								<div class="clear"></div>
								<div class="product2" style="<?php echo $sz?>">
									<div class="product-image">&nbsp;</div>
									<div class="product-details">
										<div class="product-title"><?php echo $servicos_extra[$a]['titulo'];?> <?/*<span class="termos">(<a href="<?php echo $servicos_extra[$a]['termos'];?>" target="_blank"><u><?=getLinguas('key_termos',$GLOBALS['site_lang']);?></u></a>)</span>*/?></div>
									</div>
									<div class="product-price"><?php echo numberformat($servicos_extra[$a]['valor'])?></div>
									<div class="product-quantity text-center">&nbsp;</div>
									<div class="product-removal"><label class="lbl_radio" for="servicos_extra_<?php echo $servicos_extra[$a]['id'];?>_<?php echo $i?>" style="width:auto;padding-right:20px;padding-bottom:0;background-position:right;font-size: 10px;margin-bottom:0;font-weight:normal;<?php echo $sz2?>"><input class="servicos_extra_button" name="servicos_extra_add_<?php echo $i?>" <?php if($servicos_extra_id > 0 && $servicos_extra_id == $servicos_extra[$a]['id']){ echo 'checked="checked"';} ?> id="servicos_extra_<?php echo $servicos_extra[$a]['id'];?>_<?php echo $i?>" type="radio" value="<?php echo $servicos_extra[$a]['id'];?>" onclick="servicos_extra_shop_add('<?php echo $servicos_extra[$a]['id'];?>','<?php echo $i;?>','<?php echo $prod_id?>')" /><?php echo $seleted;?></label></div>
									<div class="product-line-price"><?php echo numberformat($servicos_extra[$a]['valor'] * $qtd)?>&nbsp;</div>
								</div>

							<?php
							}
							?><div class="clear"></div></div><?php
						}
					}
					?></div><?php
					if($arr_prom['p_html'])
					{
						echo $arr_prom['p_html'];
					}
					if($oferta)
					{
						$infooferta=get_produtc_info($oferta);
						?>
						<div class="product">
							<div class="product-image">&nbsp;</div>
							<div class="product-details">
								<div class="product-title"><?php echo $infooferta['prod_ref']; ?></div>
								<p class="product-description"><?php echo $infooferta['prod_nome']; ?></p>
							</div>
							<div class="product-price"><?php echo numberformat(0,2) ?></div>
							<div class="product-quantity">&nbsp;</div>
							<div class="product-removal"><button class="remove-product">Remove</button></div>
							<div class="product-line-price">Oferta</div>
						</div>
						<?php
					}
					$i++;


					//agora vamos somar os portes deste artigo aos portes acumulados de todos os artigos
					if($portes > 0) $PortesCarrinho+=$portes;


				}


				$offer_arr=showSalesPromotions_offer($GLOBALS['proms']);
				if($offer_arr['p_html_offers'])
				{
					echo $offer_arr['p_html_offers'];
				}


				//Caso os porte sejam por encomenda, vamos adicionar este valor
				if(!$GLOBALS['portes_por_produto'] && $totalEncomenda['TEM_PORTES']==1)$PortesCarrinho+=$portes_total;

				//Agora vamos verificar se os portes estao abrangidos pela oferta de portes gratis e mostrar o valor de portes no carrinho
				$portes_msg_promo = '';
				$valor_portes_gratuitos=getPortesGratuitos();
				if($valor_portes_gratuitos>0)
				{

					//echo 'VALOR PARA PORTE GRATUITOS='.($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS']);

					if(($totalEncomenda['TOTALCIVA'] - $totalEncomenda['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS']) >= $valor_portes_gratuitos && $valor_portes_gratuitos > 0)
					{
						$PortesCarrinho=$total_custos_logisticos;
					}
					if($PortesCarrinho > 0 && $GLOBALS['mostrar_portes_gratuitos_falta_msg']==1 && $valor_portes_gratuitos>0)
					{
						$portes_msg_promo = '<span style="color:#137eb6">Faltam '.numberformat($valor_portes_gratuitos-$totalEncomenda['TOTALCIVA']).' para atingir portes gratuitos  (i)</span>';
					}
				}

				$portes_iva=0;
				if($PortesCarrinho>0)
				{
					if($GLOBALS['mostrar_portes_sem_iva']==1 && $GLOBALS['portes_tem_iva']==1)
					{
						$PortesCarrinho_sem_iva=$PortesCarrinho/1.23;
						$portes_iva=$PortesCarrinho-$PortesCarrinho_sem_iva;

						$PortesCarrinho=$PortesCarrinho_sem_iva;
					}
				}

				//*********** VOUCHERS***/
				$i=1;
    			$found=0;
				while ($i <= $n_voucher && $found == 0)
				{

	                $voucher_code = $voucher[$i][1];
	                $chave = $voucher[$i][4];
	                $valor_dif = 0;
	                $info = get_voucher_info($voucher_code);

	                $desconto_valor = 0;

	                if ($info['voucher_type'] == 1) // PRODUTO DE OFERTA
	                {
	                    $nome_prod = $GLOBALS['key_voucher_oferta'][$GLOBALS['site_lang']] . ': ' . $info_prod['prod_nome'];
	                    $ref_prod = $info_prod['prod_ref'];
	                    $valor_dif = 0;
	                    $v_discount = 100;

	                    $desconto_valor = $v_discount;
	                }
	                elseif ($info['voucher_type'] == 0 || $info['voucher_type'] == 3)
	                {
	                    $nome_prod = ($info['voucher_title'] != '' ? $info['voucher_title'] : getLinguas('key_voucher_desconto'));

	                    $v_discount = $info['voucher_discount'];
	                    $desconto_valor = $v_discount;

	                    if ($info['voucher_type_discount'] == 0) // PERCENTAGEM
	                    {
	                        if ($info['voucher_desconto_afeta'] == 1)
	                        {
	                            $prods = cart_info(0);
	                            $total_afetado = 0;
	                            foreach ($prods['cartprodsi'] as $key => $value)
	                            {
	                                if (in_array($key, $info['voucher_prods'])) $total_afetado += $value['precop'];
	                            }
	                            $valor_dif = calcula_desconto(($total_afetado), $v_discount);
	                        } else {

	                            $valor_dif = calcula_desconto(($totalEncomenda['TOTALCIVA'] ), $v_discount);
	                        }
	                    } else if ($info['voucher_type_discount'] == 1) // POR VALOR
	                    {
	                        if ($info['voucher_desconto_afeta'] == 1) {
	                            foreach ($prods['cartprodsi'] as $key => $value) {
	                                if (in_array($key, $info['voucher_prods'])) {
	                                    $valor_dif = $info['voucher_discount'];

	                                    if ($v_discount > ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS'])) {
	                                        $valor_dif = ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS']);
	                                        $v_discount = 100;
	                                    } else {
	                                        $valor_dif = $v_discount;
	                                        $v_discount = ($v_discount * 100) / ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS']);
	                                    }
	                                }
	                            }
	                        } else {
	                            if ($v_discount > ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS'])) {
	                                $valor_dif = ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS']);
	                                $v_discount = 100;
	                            } else {
	                                $valor_dif = $v_discount;
	                                $v_discount = ($v_discount * 100) / ($totalEncomenda['TOTALCIVA'] + $totalEncomenda['DESCONTOS']);
	                            }
	                        }
	                    }
	                }

	                if ($info['voucher_type'] == 4) // oferta de portes
	                {
	                    $nome_prod = ($info['voucher_title'] != '' ? $info['voucher_title'] : getLinguas('key_voucher_desconto'));
	                    $ref_prod = $info_prod['prod_ref'];
	                    $valor_dif = $PortesCarrinho;
	                    $v_discount = $PortesCarrinho;
	                    $desconto_valor = $PortesCarrinho;
	                    $info['voucher_type_discount']=1;

	                    //Se tem voucher oferta portes
						$voucher_oferta_portes=1;
						$PortesCarrinho=0;
	                }
	                ?>
	                <div class="product col-xs-12">
	                	<div>
							<label style="float:left;width:15%;text-align:center">voucher</label>
							<label style="float:left;width:42%"><?php echo $nome_prod; ?></label>
							<label style="float:left;width:12%"><?php echo numberformat($desconto_valor, 1, ($info['voucher_type_discount'] == 0 ? 1 : '')); ?></label>
							<label style="float:left;width:10%">&nbsp;</label>
							<label style="float:left;width:9%;text-align:center"><button class="remove-product" onclick="removerVoucherCart('<?php echo $chave ?>');return false;">X</button></label>
							<label style="float:left;width:12%;text-align:right"><?php echo '-' . numberformat($valor_dif, 1); ?></label>
						</div>
						<? /*
						<div class="product-details">
							<p class="product-title"><?php echo $nome_prod; ?></p>
							<div class="row">
								<div class="col-xs-5">
									<div class="product-price">
										<?php echo numberformat($desconto_valor, 1, ($info['voucher_type_discount'] == 0 ? 1 : '')); ?>
									</div>
								</div>
								<div class="col-xs-5">
									<div class="product-qtd"><?php echo '-' . numberformat($valor_dif, 1); ?></div>
								</div>
								<div class="col-xs-2">
									<div class="product-removal">
				                        <button class="remove-product"
				                                onclick="removerVoucherCart('<?php echo $chave ?>');return false;">X
				                        </button>
				                    </div>
			                	</div>
		                	</div>
						</div>*/?>
					</div>
	                <?php

	                $i++;
	            }
				//
				//**********************/




				//*******************************************************************************
				//*******************************************************************************
				//                     AGORA VAMOS MOSTRAR OS TOTAIS
				//*******************************************************************************
				//*******************************************************************************
				if($GLOBALS['juntar_taxas_aos_portes']==1)
				{
					$PortesCarrinho+=$taxas;
					$taxas=0;
				}

				?>
				<div class="totals form-horizontal qc-totals">
					<div class="totals-item">
						<label><?=getLinguas('key_Subtotal',$GLOBALS['site_lang'])?></label>
						<div class="totals-value" id="cart-subtotal"><?php echo numberformat($totalEncomenda['TOTALSIVA'],1); ?></div>
					</div><?php if($total_extensao_garantia_valor>0){?>
					<div class="totals-item">
						<label>Total das extensões de garantia</label>
						<div class="totals-value" id="cart-shipping"><?php echo numberformat($total_extensao_garantia_valor,1); ?></div>
					</div><?php }?>
					<div class="totals-item">
						<label><?php

						if($total_custos_logisticos>0) echo getLinguas('key_portes_logisticos',$GLOBALS['site_lang']); else echo getLinguas('key_portes',$GLOBALS['site_lang']);
						if($portes_msg_promo)echo '<br>'.$portes_msg_promo;


						?></label>
						<div class="totals-value" id="cart-shipping"><?php echo numberformat($PortesCarrinho,1); ?></div>
					</div><?php if($totalservicos_extra>0){?>
					<div class="totals-item">
						<label>Total dos serviços extra</label>
						<div class="totals-value" id="cart-shipping"><?php echo numberformat($totalservicos_extra,1); ?></div>
					</div><?php }





					if($taxas>0){?>
					<div class="totals-item">
						<label><?=getLinguas('key_prod_taxa',$GLOBALS['site_lang'])?></label>
						<div class="totals-value" id="cart-tax"><?php echo numberformat($taxas,1) ?></div>
					</div>
					<?php }?>
					<div class="totals-item">
						<label><?=getLinguas('key_prod_iva',$GLOBALS['site_lang'])?></label>
						<div class="totals-value" id="cart-tax"><?php echo numberformat($totalEncomenda['TOTALIVA']+$portes_iva,1) ?></div>
					</div>
					<div class="totals-item totals-item-total">
						<label><strong><?=getLinguas('key_total',$GLOBALS['site_lang'])?></strong></label>
						<div class="totals-value" id="cart-total"><strong><?php

//echo $totalEncomenda['TOTALCIVA']." - ".$totalEncomenda['DESCONTOS']." + $PortesCarrinho+$portes_iva + $taxas+$total_extensao_garantia_valor+$totalservicos_extra | ";

						echo numberformat(($totalEncomenda['TOTALCIVA'] - $totalEncomenda['DESCONTOS']) + $PortesCarrinho+$portes_iva + $taxas+$total_extensao_garantia_valor+$totalservicos_extra,1);

						?></strong></div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
function calcularTotalEncomenda()
{
	$n_enc=$GLOBALS['n_enc'];
	$shop=$GLOBALS['shop'];
	$n_voucher=$GLOBALS['n_voucher'];
	$voucher=$GLOBALS['voucher'];
	$descontos=0;
	$totalsiva=0;
	$totalciva=0;
	$totaliva=0;
	$t_garantia=0;
	$t_extras=0;
	$t_extras2=0;
	$t_extras3=0;
	$t_custoslogisticos=0;
	$n_custoslogisticos=0;
	$portes_especial = 0;
	$valor_total_de_artigos_com_custos_logisticos=0;
	$result=array('TOTALIVA'=>$totaliva,'TOTALCIVA'=>$totalciva,'TOTALSIVA'=>$totalsiva,'DESCONTOS'=>$descontos,'TOTAL_GARANTIA'=>$t_garantia,'TOTAL_EXTRAS'=>$t_extras,'TOTAL_CUSTOSLOGISTICOS'=>$t_custoslogisticos,'TOTAL_EXTRAS2'=>$t_extras2,'TOTAL_EXTRAS3'=>$t_extras3, 'PORTES_ESPECIAL' => $portes_especial);
	$i=1;
	$found=0;
	$tem_portes=0;
	while ($i <= $n_enc && $found == 0)
	{
		//echo "totalsiva = $totalsiva |";
		$prod_id=$shop[$i][1];
		$qtd=$shop[$i][2];
		$tipo=$shop[$i][3];
		$chave=$shop[$i][4];
		$prod_obj=$shop[$i][5];
		$opcoes=$shop[$i][6];
		$opcoes_id=0;
		$opcoes_id=(isset($shop[$i][7]) ? $shop[$i][7] : 0);
		$garantia_id=0; $garantia_id=(isset($shop[$i][9]) ? $shop[$i][8] : 0);
		$porte_id=0; $porte_id=$shop[$i][9];
		// $servicos_extra_id=0; $servicos_extra_id=(isset($shop[$i][10]) ? $shop[$i][9] : 0);
		$servicos_extra_id=0; $servicos_extra_id=$shop[$i][10];

		$prod_serv=0; $prod_serv	= $shop[$i][12];
		$prod_quant_id=0; $prod_quant_id	= $shop[$i][13];
		$tempo_escolhido=0; $tempo_escolhido	= $shop[$i][14];
		$data_realizacao=0; $data_realizacao	=$shop[$i][17];

		$info=get_produtc_info($prod_id);

		//Se o preço vier de uma combinação de opcoes em vez de vir do PVP do artigo
		if($opcoes_id > 0)
		{
			$info['prod_precos']=get_produtc_price_combo($prod_id,$opcoes_id);
			$iva=$info['prod_precos']['IVA'];
			$arr_prom['p_valor']=0;
			$valorqtd=$info['prod_precos']['PVP'] * $qtd;

			//Caso este produto seja composto e tenha um serviço associado que altere o seu valor, vamos fazer essa verificacao:
			if($tempo_escolhido>0)
			{
				$preco_final_siva=$info['prod_precos']['PVP'];

				if($iva > 0)
				{
					$preco_final_siva=$preco_final_siva/(1+(floatval($iva)/100));
				}


				$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
				$rs=new DataObj;

				$sql='SELECT entrega.ID, custo_entrega,Nome FROM entrega INNER JOIN entrega_lang ON entrega_lang.ID_PAI=entrega.ID WHERE entrega.ID='.$tempo_escolhido;
				$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
			    $custo_entrega=($rs->db_data['custo_entrega'])/100; // é um custo % em relação ao valor total do produto
			    $opcoes.=' '.$rs->db_data['Nome'];
				$preco_final_siva=$preco_final_siva*$custo_entrega;
				$rs->db_close();

				if($iva > 0)
				{
					$preco_final_siva=$preco_final_siva*(1+(floatval($iva)/100));
				}

				$info['prod_precos']['PVP']=$preco_final_siva;

				//Para ja vamos anular as promocoes, mas sera algo que tera de ser ainda programado
				$arr_prom['p_valor']=0;
				$valorqtd=$info['prod_precos']['PVP'] * $qtd;

			}
		}
		else
		{
			//Vou ler o preço do campo obj
			$valorqtd=$info['prod_precos']['PVP'] * $qtd;
			// PROMOCOES QUE O PRODUTO TEM
			$arr_prom=showSalesPromotions($GLOBALS['proms'],$prod_id,$valorqtd);

            $prod_ref =  strtoupper(substr($info['prod_ref'], -3));
            if($prod_ref == 'RFB'){
                $iva=0;

            }elseif($prod_ref == 'IVA'){
                $iva=0;
            }else{
                $iva= $info['prod_precos']['IVA'];

            }
			//$iva=0;//$info['prod_precos']['IVA'];
		}



		//Agora vamos aplicar ou retirar o iva e atualizar todas as variaveis
		if($GLOBALS['produtos_com_iva'] == 1)
		{
			//Os preços já vem com IVA
			if($arr_prom['p_valor'] > 0)$totalsiva += $arr_prom['p_valor'];
			else $totalsiva += $valorqtd;
			if($iva > 0)
			{
				if($arr_prom['p_valor'] > 0)$pciva=(($arr_prom['p_valor']) * (1+(floatval($iva)/100)));
				else $pciva=($valorqtd * (1+(floatval($iva)/100)));
				//else $pciva=($info['prod_precos']['PVP'] * (1+(floatval($iva)/100)));
			}
			else
			{
				if($arr_prom['p_valor'] > 0)$pciva=(($arr_prom['p_valor']) * (1+(floatval($iva)/100)));
				else $pciva=($valorqtd * (1+(floatval($iva)/100)));
				//else $pciva=($info['prod_precos']['PVP'] * (1+(floatval($iva)/100)));
			}
			$totalciva += $pciva;
		}
		else
		{

			//Os preços vem sem IVA
			if($arr_prom['p_valor'] > 0)$totalciva += ($arr_prom['p_valor']);
			else $totalciva += $valorqtd;

			if($iva > 0)
			{
				if($arr_prom['p_valor'] > 0)$psiva=(($arr_prom['p_valor']) / (1+(floatval($iva)/100)));
				else $psiva=($valorqtd / (1+(floatval($iva)/100)));
				//else $psiva=($info['prod_precos']['PVP'] / (1+(floatval($iva)/100)));
			}
			else
			{
				if($arr_prom['p_valor'] > 0)$psiva=(($arr_prom['p_valor']) / (1+(floatval($iva)/100)));
				else $psiva=($valorqtd / (1+(floatval($iva)/100)));
				//else $psiva=($info['prod_precos']['PVP'] / (1+(floatval($iva)/100)));
			}
			$totalsiva += $psiva;
		}

		// EXTENSAO DE GARANTIA
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)
		{
			$garantia=extensao_garantia_prod($info['prod_id'],$info['prod_precos']['PVP']);
			if($garantia_id > 0)
			{
				for($a=0; $a<count($garantia); $a++)
				{
					if($garantia_id == $garantia[$a]['id'])$t_garantia+=($garantia[$a]['valor'] * $qtd);
				}
			}
		}
		// SERVIÇOS EXTRA
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
		{
			$servicos_extra=servicos_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
			if($servicos_extra_id > 0)
			{
				for($a=0; $a<count($servicos_extra); $a++)
				{
					if($servicos_extra_id == $servicos_extra[$a]['id'])
					{
						$t_extras+= ($servicos_extra[$a]['valor'] * $qtd);
						$produto_servicos_extra_valor=$servicos_extra[$a]['valor'] * $qtd;
					}
				}
			}
		}
		//CUSTOS DE LOGISTICA POR ARTIGO E QUANTIDADE
		$custos_logisticos_tmp=0;
		if(isset($GLOBALS['carrinho_conf']) && $GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
		{
			$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
			for($a=0; $a<count($custos_logisticos); $a++)
			{
				$n_custoslogisticos++;
				if(isset($GLOBALS['portes_cobrar_metade']) && $GLOBALS['portes_cobrar_metade'] == 1 && $n_custoslogisticos>1)
				{
					//Vamos cobrar apenas metade dos custos logisticos a partir do segundo artigo
					$custos_logisticos_tmp+=(($custos_logisticos[$a]['valor'] * $qtd)/2);
				}
				else
				{
					$custos_logisticos_tmp+=$custos_logisticos[$a]['valor'];
					if($qtd>1)
					{
						$custos_logisticos_tmp+=(($custos_logisticos[$a]['valor'] * ($qtd-1))/2);
					}
				}
			}
		}
		//Se tiver esta flag ativa e se tiver serviços extra nao paga portes nem custos logisticos
		if(isset($GLOBALS['portes_nao_pagar_premium']) && $GLOBALS['portes_nao_pagar_premium'] == 1)
		{
			//se tem entrega especial nao pode cobrar portes neste artigo
			if($produto_servicos_extra_valor>0)
			{
				$custos_logisticos_tmp=0;
				$portes=0;
			}
		}
		$t_custoslogisticos+=$custos_logisticos_tmp;


		if($custos_logisticos_tmp==0)$tem_portes=1;
		else $valor_total_de_artigos_com_custos_logisticos+=$info['prod_precos']['PVP']*$qtd;

		//OUTROS SERVIÇOS ADICIONAIS EM QUE O PREÇO MUDA TIPO TAXA, EXEMPLO IMPRESSAO EM GRAFICA:
		if(isset($prod_serv) && $prod_serv!='' )
		{
			$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);
			$rs2=new DataObj;
			$array_servicos=explode(";",$prod_serv);
			foreach($array_servicos as $servico_id)
			{
				if($servico_id>0)
				{
					$sql='Select Tipo,Custos,margem,Nome from servicos inner join servicos_lang on servicos_lang.ID_PAI=servicos.ID where servicos.ID='.$servico_id.' and Visivel=1 and Lang="pt"';
					$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
					if(!$rs2->EOF())
					{
						$tipo=$rs2->db_data['Tipo'];  // tipo = 1 é valor fixo tipo = 2 percentual
						$custo=$rs2->db_data['Custos'];
						$custo_sem_IVA=$custo/1.23;
						//Aqui vou querer mostrar o valor com IVA
						if($tipo==1)
						{
							$t_extras2+=$custo;

							$totalciva+=$custo;
							$totalsiva+=$custo_sem_IVA;


						}else{
							$margem=$rs2->db_data['margem'];
							// vai ficar por fazer as do tipo margem
						}
					}
					$rs2->db_close();
				}
			}
		}


		$i++;
	}

	//echo "totalsiva final = $totalsiva |";

	// echo $totalciva;
	$totaliva=$totalciva - $totalsiva;
	$result['TOTALIVA']=$totaliva;
	$result['TOTALCIVA']=$totalciva;
	$result['TOTALSIVA']=$totalsiva;
	$result['TOTAL_GARANTIA']=$t_garantia;
	$result['TOTAL_EXTRAS']=$t_extras;
	$result['TOTAL_EXTRAS2']=$t_extras2;
	$result['TOTAL_CUSTOSLOGISTICOS']=$t_custoslogisticos;
	$result['VALOR_TOTAL_DE_ARTIGOS_COM_CUSTOS_LOGISTICOS']=$valor_total_de_artigos_com_custos_logisticos;
	$result['TEM_PORTES']=$tem_portes;//se esta flag estiver a 0 quer dizer que nenhum dos artigos esta sugeito a portes

	$i=1;
	$found=0;
	while ($i <= $n_voucher && $found == 0)
	{
		$voucher_code=$voucher[$i][1];
		$valor_dif=0;
		$info=get_voucher_info($voucher_code);
		/*if($info['voucher_type'] != 1)
		{
			$valor_dif=calcula_desconto($totalciva,$info['voucher_discount']);
		}*/
		if($info['voucher_type'] == 0 || $info['voucher_type'] == 2 || $info['voucher_type'] == 3)
		{
			if($info['voucher_type_discount'] == 0)
			{
				$valor_dif=calcula_desconto($totalciva,$info['voucher_discount']);
			}
			else
			{
				$v_discount=$info['voucher_discount'];
				if($info['voucher_type_discount'] == 1)
				{
					if($v_discount > $totalciva)
					{
						$valor_dif=$totalciva;
						$v_discount=100;
					}
					else
					{
						$valor_dif=$v_discount;
						$v_discount=($v_discount * 100) / $totalciva;
					}
				}
			}
		}
		if($info['voucher_type'] == 0 || $info['voucher_type'] == 2 || $info['voucher_type'] == 3)
		{
			$descontos += $valor_dif;
		}
		$i++;
	}

	// echo $descontos;
	//TODO ACRESCENTAR OS PONTOS
	$result['DESCONTOS']=$descontos;

	return $result;
}

function shopShowFormNovoEndereco($wrapperId,$naologado=0,$ext='')
{
	$rs=new Dataobj();
	$db_link=\db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);

	$m_nome 			= db_real_escape_string($_POST['m_nome']);
	$m_apelido 			= db_real_escape_string($_POST['m_apelido']);
	$m_email 			= db_real_escape_string($_POST['m_email']);
	$m_contribuinte 	= db_real_escape_string($_POST['m_contribuinte']);
	$m_pais 			= (isset($_POST['m_pais']) && ($_POST['m_pais']) > 0 ? db_real_escape_string($_POST['m_pais']) : 165);
	$m_morada 			= db_real_escape_string($_POST['m_morada']);
	$m_cidade 			= db_real_escape_string($_POST['m_cidade']);
	$m_codpos 			= db_real_escape_string($_POST['m_codigo_postal']);
	$m_telefone 		= db_real_escape_string($_POST['m_telefone']);
	$m2_nome 			= db_real_escape_string($_POST['m2_nome']);
	$m2_apelido 		= db_real_escape_string($_POST['m2_apelido']);
	$m2_morada 			= db_real_escape_string($_POST['m2_morada']);
	$m2_pais 			= (isset($_POST['m2_pais']) && ($_POST['m2_pais']) > 0 ? db_real_escape_string($_POST['m2_pais']) : 165);
	$m2_cidade 			= db_real_escape_string($_POST['m2_cidade']);
	$m2_codpos 			= db_real_escape_string($_POST['m2_codigo_postal']);
	$m2_telefone 		= db_real_escape_string($_POST['m2_telefone']);
	if($ext)
	{
		$m_nome=$m2_nome;
		$m_apelido=$m2_nome;
		$m_contribuinte=$m2_contribuinte;
		$m_pais=$m2_pais;
		$m_morada=$m2_morada;
		$m_cidade=$m2_cidade;
		$m_codpos=$m2_codpos;
		$m_telefone=$m2_telefone;
	}
	?>
	<div class="row">
		<div class="col-md-12 hidden">
			<div class="form-group">
				<input type="text" class="form-control" id="m_designacao" name="m_designacao" value="">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_morada"><?=getLinguas('key_nome',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_nome" name="m<?php echo $ext;?>_nome" value="<?php echo $m_nome;?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
			<label for="m<?php echo $ext;?>_morada"><?=getLinguas('key_apelido',$GLOBALS['site_lang']); ?></label>
				<input type="text"  class="form-control" id="m<?php echo $ext;?>_apelido" name="m<?php echo $ext;?>_apelido" value="<?php echo $m_apelido;?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_morada"><?=getLinguas('key_morada',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_morada" name="m<?php echo $ext;?>_morada" value="<?php echo $m_morada;?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_codigo_postal"><?=getLinguas('key_codpos',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_codigo_postal" name="m<?php echo $ext;?>_codigo_postal" value="<?php echo $m_codpos;?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_cidade"><?=getLinguas('key_cidade',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_cidade" name="m<?php echo $ext;?>_cidade" value="<?php echo $m_cidade;?>">
			</div>
		</div>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_pais">
					<?=getLinguas('key_pais',$GLOBALS['site_lang']); ?>
					<span class="mandatory">*</span>
				</label>
				<select onchange="reload_shopping_cart();" class="form-control" name="m<?php echo $ext;?>_pais" id="m<?php echo $ext;?>_pais" style="height:27px" >
					<?php
					// $sql="SELECT c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='{$GLOBALS['site_lang']}' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
					$sql="SELECT distinct c.zone_id,czl.zone_name FROM country_zones AS c INNER JOIN country_zones_lang AS czl ON c.zone_id=czl.zone_id WHERE czl.zone_lang='{$GLOBALS['site_lang']}' AND c.zone_visible=1 ORDER BY czl.zone_name ASC";
					$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
					while (!$rs->EOF())
					{
						$zone_name=$rs->db_data['zone_name'];
						$zone_id=$rs->db_data['zone_id'];
						?>
						<option value="<?php echo $zone_id; ?>" <?php if($m_pais==$zone_id){ echo 'selected="selected"';}?>><?php echo $zone_name; ?></option>
						<?php
						$rs->db_next();
					}
					$rs->db_close();
					?></select>
			</div>
		</div>
		<?php if($ext==''){?>
		<div class="col-md-6">
			<div class="form-group">
			<label for="m<?php echo $ext;?>_nif"><?=getLinguas('key_doc_identidade',$GLOBALS['site_lang']); ?></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_contribuinte" name="m<?php echo $ext;?>_contribuinte" value="<?php echo $m_contribuinte;?>">
			</div>
		</div>
		<?php }/* else{?>
		<div class="col-md-6">
			<div class="form-group">
			<label for="m<?php echo $ext;?>_email">Email <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m_email" name="m_email" value="<?php echo $m_email;?>">
			</div>
		</div>
		<?php } */?>
		<div class="col-md-6">
			<div class="form-group">
				<label for="m<?php echo $ext;?>_telefone"><?=getLinguas('key_tel',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m<?php echo $ext;?>_telefone" name="m<?php echo $ext;?>_telefone" value="<?php echo $m_telefone;?>">
			</div>
		</div>
		<?php if($ext==''){?>
		<div class="col-md-6">
			<div class="form-group">
			<label for="m<?php echo $ext;?>_email"><?=getLinguas('key_email',$GLOBALS['site_lang']); ?> <span class="mandatory">*</span></label>
				<input type="text" class="form-control" id="m_email" name="m_email" value="<?php echo $m_email;?>">
			</div>
		</div>
		<?php }?>
		<?php if($naologado == 0){?>
		<div class="col-md-12">
			<input style="margin-right:10px" type="button" value="&lt; VOLTAR" class="button3" onclick="$('<?php echo $wrapperId; ?>').hide();">
			<input type="button" value="CRIAR" class="button3" onclick="validarNovoEndereco('<?php echo $wrapperId; ?>');">
		</div>
		<?php }?>
	</div><br>
	<?php
}
function numberformat($value=0,$lado=0,$perc=0)
{
	date_default_timezone_set("Europe/Lisbon");
	setlocale(LC_TIME, "Portuguese_Standard");
	setlocale(LC_ALL, "en_US.UTF-8;LC_NUMERIC=C");
	setlocale(LC_NUMERIC, 'en_US');

	$value=(float)$value;
	//$value=str_replace(',', '.', $value); //sempre .

	$r=2;//valor default para arredondamento
	$n_casas=2;//valor default para arredondamento
	if($_SESSION['ss_currency_value'] != 0 && $_SESSION['ss_currency_value'] != 1)$value=$value * $_SESSION['ss_currency_value'];

	if(defined('_ROUND_DOUBLE_') )
	{
		$value=round($value,_ROUND_DOUBLE_);
		$n_casas=_ROUND_DOUBLE_;
	}

	if($perc == 1 && $lado == 0)
	{
		return '% '.numberFormatPrecision($value,$n_casas);
	}
	elseif($perc == 1 && $lado == 1)
	{
		return numberFormatPrecision($value,$n_casas).' %';
	}
	elseif($lado == 0)
	{
		return MOEDA.' '.numberFormatPrecision($value,$n_casas);
	}
	elseif($lado == 1)
	{
		return numberFormatPrecision($value,$n_casas).' '.MOEDA;
	}
	elseif($lado == 2)
	{
		return numberFormatPrecision($value,$n_casas).' &euro;';
	}
	elseif($lado == 3)
	{
		return '&euro; '.numberFormatPrecision($value,$n_casas);
	}
	elseif($lado == 4)
	{
		return numberFormatPrecision($value,$n_casas);
	}
}
function numberFormatPrecision($number, $precision = 2, $sep2=',', $separator = '.')
{
	date_default_timezone_set("Europe/Lisbon");
	setlocale(LC_TIME, "Portuguese_Standard");
	setlocale(LC_ALL, "en_US.UTF-8;LC_NUMERIC=C");
	setlocale(LC_NUMERIC, 'en_US');

	return number_format($number,$precision,',','.');


    /*$numberParts = explode($separator, $number);
    $response = $numberParts[0];
    if($response>1000)
    {
    	$th=substr($response, 0, (strlen($response)-3));
    	$rest=substr($response, (strlen($response)-3));
    	$response=$th.$sep2.$rest;
    }
    if (count($numberParts)>1 && $precision > 0) {
        $response .= $separator;
        $parte=substr($numberParts[1], 0, $precision);
        if(strlen($parte)==1)$parte.='0';
        $response .= $parte;
    }
    return $response;*/
}
function get_purchase_info($enc_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$log2;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;
	$erro='';
	$sql='
				SELECT
					E.Metodo_pagamento,
					E.Metodo_envio,
					E.Data_Criacao,
					E.enc_Nome,
					E.enc_Apelido,
					E.enc_Morada,
					E.enc_Localidade,
					E.enc_Cidade,
					E.enc_DataNascimento,
					E.enc_Sexo,
					E.enc_Codigo_Postal,
					E.enc_Email,
					E.enc_Telefone,
					E.enc_Nif,
					E.enc_Pais,
					E.enc_Nome_ent,
					E.enc_Apelido_ent,
					E.enc_Morada_ent,
					E.enc_Localidade_ent,
					E.enc_Cidade_ent,
					E.enc_Codigo_Postal_ent,
					E.enc_Email_ent,
					E.enc_Telefone_ent,
					E.enc_Nif_ent,
					E.enc_Pais_ent,
					E.Taxas,
					E.Portes,
					E.Custos_logisticos,
					E.TotalDocSemIva,
					E.TotalDocIva,
					E.TotalDocComIva,
					E.TotalDocSemIva,
					E.TotalDocOutrasTaxas,
					E.enc_Fatura
				FROM
					encomendas AS E
				WHERE
					E.ID="'.$enc_id.'"
			';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
		$Metodo_envio=$rs->db_data["Metodo_envio"];
		$enc_DataCriacao=$rs->db_data["Data_Criacao"];
		$enc_Nome=$rs->db_data["enc_Nome"].' '.$rs->db_data["enc_Apelido"];
		$enc_Morada=$rs->db_data["enc_Morada"];
		$enc_Localidade=$rs->db_data["enc_Localidade"];
		$enc_Cidade=$rs->db_data["enc_Cidade"];
		$enc_Sexo=$rs->db_data["enc_Sexo"];
		$enc_DataNascimento=$rs->db_data["enc_DataNascimento"];
		$enc_Codigo_Postal=$rs->db_data["enc_Codigo_Postal"];
		$enc_Email=$rs->db_data["enc_Email"];
		$enc_Telefone=$rs->db_data["enc_Telefone"];
		$enc_Nif=$rs->db_data["enc_Nif"];
		$enc_Pais=$rs->db_data["enc_Pais"];
		$enc_Nome_ent=$rs->db_data["enc_Nome_ent"].' '.$rs->db_data["enc_Apelido_ent"];
		$enc_Morada_ent=$rs->db_data["enc_Morada_ent"];
		$enc_Localidade_ent=$rs->db_data["enc_Localidade_ent"];
		$enc_Cidade_ent=$rs->db_data["enc_Cidade_ent"];
		$enc_Codigo_Postal_ent=$rs->db_data["enc_Codigo_Postal_ent"];
		$enc_Telefone_ent=$rs->db_data["enc_Telefone_ent"];
		$enc_Nif_ent=$rs->db_data["enc_Nif_ent"];
		$enc_Pais_ent=$rs->db_data["enc_Pais_ent"];
		$enc_Taxas=$rs->db_data["Taxas"];
		$enc_Portes=$rs->db_data["Portes"];
		$Custos_logisticos=$rs->db_data["Custos_logisticos"];
		$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
		$TotalDocIva=$rs->db_data["TotalDocIva"];
		$TotalDocComIva=$rs->db_data["TotalDocComIva"];
		$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
		$enc_Fatura=$rs->db_data["enc_Fatura"];
		$enc_cabecalho=array('enc_ID'=>$enc_id,'enc_Fatura'=>$enc_Fatura,'enc_MetodoPagamento'=>$Metodo_pagamento,'enc_MetodoExpedicao'=>$Metodo_envio,'enc_DataCriacao'=>$enc_DataCriacao,'enc_Nome'=>$enc_Nome,'enc_Morada'=>$enc_Morada,'enc_Localidade'=>$enc_Localidade,'enc_Cidade'=>$enc_Cidade,'enc_Sexo'=>$enc_Sexo,'enc_DataNascimento'=>$enc_DataNascimento,'enc_Codigo_Postal'=>$enc_Codigo_Postal,'enc_Email'=>$enc_Email,'enc_Telefone'=>$enc_Telefone,'enc_Nif'=>$enc_Nif,'enc_Pais'=>$enc_Pais,'enc_Nome_ent'=>$enc_Nome_ent,'enc_Morada_ent'=>$enc_Morada_ent,'enc_Localidade_ent'=>$enc_Localidade_ent,'enc_Cidade_ent'=>$enc_Cidade_ent,'enc_Codigo_Postal_ent'=>$enc_Codigo_Postal_ent,'enc_Telefone_ent'=>$enc_Telefone_ent,'enc_Nif_ent'=>$enc_Nif_ent,'enc_Pais_ent'=>$enc_Pais_ent,'enc_Taxas'=>$enc_Taxas,'enc_Portes'=>$enc_Portes,'enc_Total'=>$TotalDocComIva,'enc_TotalDocSemIva'=>$TotalDocSemIva,'enc_TotalDocIva'=>$TotalDocIva,'enc_TotalDocOutrasTaxas'=>$TotalDocOutrasTaxas);
		$sql='
				SELECT
					EP.ID_Produto,
					EP.Ref,
					EP.Nome,
					EP.Qtd,
					EP.Valor,
					EP.Valor_Desconto,
					EP.voucher_id,
					EP.Chave
				FROM
					encomendas_produtos AS EP
				WHERE
					EP.ID_PAI="'.$enc_id.'"
			';
		$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF())
		{
			while (!$rs2->EOF())
			{
				$ID_Produto=$rs2->db_data["ID_Produto"];
				$Ref=$rs2->db_data["Ref"];
				$Nome=$rs2->db_data["Nome"];
				$Qtd=$rs2->db_data["Qtd"];
				$Valor=$rs2->db_data["Valor"];
				$ValorDesconto=$rs2->db_data["Valor_Desconto"];
				$Chave=$rs2->db_data["Chave"];
				$voucher_id=$rs2->db_data["voucher_id"];
				$enc_produtos[]=array('prod_ID'=>$ID_Produto,'prod_Ref'=>$Ref,'prod_Voucher'=>$voucher_id,'prod_Nome'=>$Nome,'prod_Qtd'=>$Qtd,'prod_Valor'=>$Valor,'prod_Desconto'=>$ValorDesconto,'prod_Key'=>$Chave);
				$rs2->db_next();
			}
		}
		$rs2->db_close();
	}
	$rs->db_close();
	return array('enc_cabecalho'=>$enc_cabecalho,'enc_produtos'=>$enc_produtos);
}
function enviarEmailCliente($id_enc,$msg='',$estado=0)
{
	global $FromName,$usar_token_enc;
	$message=array();
	$enc_info=get_purchase_info($id_enc);
	$enc_state=estado_encomenda($estado,$GLOBALS['site_lang']);
	$message['body']=showInfoEncomenda($id_enc,'800',1,$enc_state['texto']);
	$body=array(
				'[n_enc]'=>$id_enc,
				'[area_cliente]'=>'<a href="'.$GLOBALS['site_http_url'].seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1" style="text-decoration:underline">Área Cliente</a>',
				'[portes]'=>numberformat($enc_info['enc_cabecalho']['Portes']),
				'[nome]'=>$enc_info['enc_cabecalho']['enc_Nome']
			);

	$message=template_email($GLOBALS['template_encomenda'],$body,800,0,$message['body'],0,getLinguas('key_encomenda').' '.$enc_state['titulo']);

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql ='UPDATE encomendas SET email_cliente="'.addslashes(htmlentities($message['body'])).'" WHERE ID="'.$id_enc.'"';
	$res=Runquery($db_link,$DB_name,$sql);


	if($usar_token_enc==1)
	{
		$Referencia='';
		if($GLOBALS['envia_emails_enc_admin']==1)send_email($GLOBALS['Default_notifications_email'],str_replace('[nr]',$Referencia,$message['subject']).' '.$enc_state['titulo'],$message['body']);
		send_email($enc_info['enc_cabecalho']['enc_Email'],str_replace('[nr]',$Referencia,$message['subject']).' '.$enc_state['titulo'],$message['body']);
	}
	else
	{
		if($GLOBALS['envia_emails_enc_admin']==1)send_email($GLOBALS['Default_notifications_email'],str_replace('[nr]',$id_enc,$message['subject']).' '.$enc_state['titulo'],$message['body']);
		send_email($enc_info['enc_cabecalho']['enc_Email'],str_replace('[nr]',$id_enc,$message['subject']).' '.$enc_state['titulo'],$message['body']);
	}

}
function country_name($country_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$zone_name='';
	$sql='SELECT
				cz.zone_name
			FROM
				country_zones_lang AS cz
			WHERE
				cz.zone_id="'.$country_id.'"
				AND
				cz.zone_lang="'.$site_lang.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$zone_name=$rs->db_data['zone_name'];
	}
	$rs->db_close();
	return $zone_name;
}
function country_id($country_name=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$zone_name='';
	$sql='SELECT
				cz.zone_id
			FROM
				country_zones_lang AS cz
			WHERE
				cz.zone_name LIKE "%'.$country_name.'%"
				AND
				cz.zone_lang="'.$site_lang.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$zone_id=$rs->db_data['zone_id'];
	}
	$rs->db_close();
	return $zone_id;
}
function calcula_desconto($valor,$desconto=0)
{
	$desconto=($desconto / 100);
	$valor_descontado=$valor * $desconto;
	return $valor_descontado;
}
function generateRandomString($length=10)
{
	$characters='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
	$randomString='';
	for ($i=0; $i < $length; $i++) {
		$randomString .= $characters[rand(0,strlen($characters) - 1)];
	}
	return $randomString;
}
function getPortesCarrinho($metodo_expedicao,$pais,$bd=0,$id_enc=0)
{
	if(isset($GLOBALS['portes_personalizados']) && $GLOBALS['portes_personalizados'] == 1)return getPortesCarrinhoPersonalizados($metodo_expedicao,$pais,$bd,$id_enc);


	if($_GET['pp']==1)
	{
		echo "$metodo_expedicao,$pais,$bd,$id_enc <br>";
		echo "<br>";
	}


	$peso_total=0;
	$portes=0;
	$enc_total=0;
	$tem_portes=0;//para ter portes será necessario pelo menos 1 artigo nao ter custos logisticos
	if($bd == 1)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2=new DataObj;
		$sql='SELECT ID_Produto,Qtd FROM encomendas_produtos WHERE ID_PAI='.$id_enc.'  AND voucher_id=0 AND Perc_Desconto!=100';
		$db_res=$rs2->db_open($db_link,$DB_name,$sql);
		while (!$rs2->EOF())
		{
			$info=get_produtc_info($rs2->db_data['ID_Produto']);
			$qtd=$rs2->db_data['Qtd'];
			$peso_total += floatval($info['prod_peso']);
			$enc_total += ($info['prod_precos']['PVP'] * $qtd);
			$rs2->db_next();
		}
		$rs2->db_close();
	}
	else
	{
		global $n_enc,$enc,$shop;
		$found=0;
		$i=1;
		while ($i <= $n_enc && $found == 0)
		{
			$prod_id=$shop[$i][1];
			$qtd=$shop[$i][2];
			$tipo=$shop[$i][3];
			$chave=$shop[$i][4];
			$prod_obj=$shop[$i][5];
			$info=get_produtc_info($prod_id,$prod_obj);

			//Temos de verificar se este artigo tem custos logisticos
			$t_custoslogisticos=0;
			$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
			for($a=0; $a<count($custos_logisticos); $a++)$t_custoslogisticos+=($custos_logisticos[$a]['valor'] * $qtd);
			if($t_custoslogisticos==0)$tem_portes=1;
			$peso_total += (floatval($info['prod_peso']) * $qtd);
			$enc_total += ($info['prod_precos']['PVP'] * $qtd);

			$i++;
		}
	}
	if($tem_portes==0)return 0;

	$linhas_peso=getLinhasPortes($metodo_expedicao,$pais);
	
	if($_GET['pp']==1)var_dump($linhas_peso);
	
	$tam=is_array($linhas_peso) ? count($linhas_peso) : 0;
	for ($i=0; $i < $tam; $i++)
	{
		$pesomin=$linhas_peso[$i]['PesoMin'];
		$pesomax=$linhas_peso[$i]['PesoMax'];
		$preco=$linhas_peso[$i]['Preco'];
		if($peso_total >= $pesomin && $peso_total <= $pesomax)
		{
			$portes=$preco;
			break;
		}
		else
		{
			if($i == ($tam - 1))
			{
				//É a ultima linha e não esta entre os valores
				$portes=$preco;
			}
		}
	}
	//echo $portes;
	// SE FOR LISBOA E A ENCOMENDA FOR SUPERIOR A 200
	// echo $pais.'==432 && '.$enc_total.' >= 200';
	// if($pais==432 && $enc_total > 200) $portes=0;

	return $portes;
}
function getPortesArtigo($metodo_expedicao,$pais,$prod_id)
{
	$peso_total=0;
	$portes=0;

	$info=get_produtc_info($prod_id);
	$peso_total=floatval($info['prod_peso']);
	$enc_total=$info['prod_precos']['PVP'];

	$linhas_peso=getLinhasPortes($metodo_expedicao,$pais);
	$tam=is_array($linhas_peso) ? count($linhas_peso) : 0;
	for ($i=0; $i < $tam; $i++) {
		$pesomin=$linhas_peso[$i]['PesoMin'];
		$pesomax=$linhas_peso[$i]['PesoMax'];
		$preco=$linhas_peso[$i]['Preco'];
		if($peso_total >= $pesomin && $peso_total <= $pesomax) {
			$portes=$preco;
			break;
		} else {
			if($i == ($tam - 1)) {
				//É a ultima linha e não esta entre os valores
				$portes=$preco;
			}
		}
	}
	// SE FOR LISBOA E A ENCOMENDA FOR SUPERIOE A 200
	// echo $pais.'==432 && '.$enc_total.' >= 200';
	// if($pais==432 && $enc_total > 200) $portes=0;
	return $portes;
}
function getLinhasPortes($metodo_expedicao='',$pais)
{
	if(!$metodo_expedicao)return 0;

	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$iva_default;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$result=array();
	$pos=0;
	$sql="SELECT zone_type FROM country_zones WHERE zone_id='{$pais}'";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$zone_type=$rs->db_data['zone_type'];
		$sql='SELECT * FROM portes WHERE ID_MetodoExpedicao='.$metodo_expedicao.' AND zone_type='.$zone_type.' ORDER BY PesoMin ASC,PesoMax ASC';
		$db_res=$rs2->db_open($db_link,$DB_name,$sql);
		while (!$rs2->EOF())
		{
			$result[$pos]['PesoMin']=$rs2->db_data['PesoMin'];
			$result[$pos]['PesoMax']=$rs2->db_data['PesoMax'];
			$usar_iva=$rs2->db_data['usar_iva'];
			if($usar_iva == 1 && $iva_default > 0)
			{
				$iva=1 + (floatval($iva_default) / 100);
				$preco=$rs2->db_data['Preco'] * $iva;
			}
			else
			{
				$preco=$rs2->db_data['Preco'];
			}
			$result[$pos]['Preco']=$preco;
			$pos++;
			$rs2->db_next();
		}
		$rs2->db_close();
		$rs->db_next();
	}
	$rs->db_close();

	return $result;
}
function getPortesGratuitos()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT * FROM portes_gratuitos';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()) {
		$valor_portes=$rs->db_data["valor_portes"];
	}
	$rs->db_close();
	return $valor_portes;
}
function cancelarEncomenda()
{
	if(isset($_POST['id_enc2']) && $_POST['id_enc2'] > 0) {
		$id_enc=$_POST['id_enc2'];
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$sql='UPDATE encomendas SET Estado="2" WHERE ID="'.$id_enc.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
}
function reEncomendarEncomenda()
{
	if(isset($_POST['id_enc2']) && $_POST['id_enc2'] > 0)
	{
		global $n_enc,$enc,$shop;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$id_enc=$_POST['id_enc2'];
		$listprods='';
		$listprodsqtds='';
		$sql='SELECT ID_Produto,Qtd FROM encomendas_produtos WHERE voucher_id=0 AND Perc_Desconto!=100 AND ID_PAI='.$id_enc;
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		while (!$rs->EOF())
		{
			$prod_obj_id=$rs->db_data['ID_Produto'];
			$prod_add_qtd=$rs->db_data['Qtd'];
			$listprods .= $prod_obj_id.',';
			$listprodsqtds .= $prod_add_qtd.',';
			$rs->db_next();
		}
		$rs->db_close();
		$listprods=substr($listprods,0,-1);
		echo '<script>';
		echo '$.ajax({
				type: "GET",
				url: SITE_HTTP_URL+"pages.php",
				  data: { page_id:6,prod_add_repetir: \''.$listprods.'\',prod_add_obj_repetir:\''.$listprods.'\',prod_add_qtd_repetir:\''.$listprodsqtds.'\' }
				}).done(function(resultado){
				  openPage(\''.seo("pages.php?page_id=".$GLOBALS['cart_purchage_id']).'\',"_top");
				});';
		echo '</script>';
	}
}
function limparEncomenda()
{
	unset($_SESSION['CLIENTE_ID_TEMP']);
	unset($_SESSION['ss_shop']);
	unset($_SESSION['ss_n_enc']);
	$shop=array();
	$n_enc=0;
	if($GLOBALS['module_voucher'] == 1) {
		unset($_SESSION['ss_voucher']);
		unset($_SESSION['ss_n_voucher']);
		$voucher=array();
		$n_voucher=0;
		$_SESSION['ss_voucher']=$voucher;
		$_SESSION['ss_n_voucher']=$n_voucher;
	}
	$_SESSION['ss_shop']=$shop;
	$_SESSION['ss_n_enc']=$n_enc;
}
function tratarMetodosPagamento($idEncomenda,$idMetodo,$totalEncomenda)
{ //die("Metodo".$idMetodo);
	$rs=getMetodoPagamentoById($idMetodo);
	if(!$rs->EOF())
	{
		$tipo=$rs->db_data['Tipo'];
		switch ($tipo)
		{
			case MP_TP_SEQURA:
				$return=getMetodoPagamentoSequraPayGateway($idMetodo,$totalEncomenda,$idEncomenda);
				break;
			case MP_TP_FLOAPAY:
				$return=getMetodoPagamentoFloaPayGateway($idMetodo,$totalEncomenda,$idEncomenda);
				break;
			case MP_TP_PAYPAL:
				$return=getMetodoPagamentoPaypalGateway($idMetodo,$totalEncomenda,$idEncomenda);
				break;
			case MP_TP_TRANS_BANCARIA:
			   limparEncomenda();
				$return=getMetodoPagamentoTransBancariaGateway($idMetodo,$totalEncomenda,$idEncomenda);
				break;
			case MP_TP_UNICRE:
				echo getMetodoPagamentoUnicreGateway($totalEncomenda,$idEncomenda);
				break;
			case MP_TP_MB_IFTHEN:
				limparEncomenda();
			   $return=getMetodoPagamentoMBIfthenGateway($idEncomenda);
				break;
			case MP_TP_MB_LUSOPAY:
				limparEncomenda();
			   $return=getMetodoPagamentoMBLusopayGateway($idEncomenda);
				break;
			case MP_TP_COBRANCA:
				limparEncomenda();
				$return=getMetodoPagamentoCobrancaGateway();
				break;
			case MP_TP_EM_LOJA:
				limparEncomenda();
				$return=getMetodoPagamentoEmLoja();
				break;
			case MP_TP_HIPAY:
				$return=getMetodoPagamentoHipayGateway($idEncomenda);
				break;
			case MP_TP_EASYPAY:
				$return=getMetodoPagamentoEasypayGateway($idEncomenda);
				break;
			case MP_TP_MB_EASYPAY:
				limparEncomenda();
				$return=getMetodoPagamentoMBEasypayGateway($idEncomenda);
				break;
			case MP_TP_MB_SIBS:
				limparEncomenda();
				$return=getMetodoPagamentoMBSibsGateway($idEncomenda);
				break;
			case MP_TP_PAYSHOP_IFTHEN:
				limparEncomenda();
				$return=getMetodoPagamentoPayShopIfthenGateway($idEncomenda);
				break;
			case MP_TP_VISA_IFTHEN:
				//limparEncomenda();
				$return=getMetodoPagamentoVisaIfthenGateway($idEncomenda);
				break;
			case MP_TP_CC_EASYPAY:
				//limparEncomenda();
				$return=getMetodoPagamentoVisaEasyPay($idEncomenda);
				break;
			case MP_TP_MBWAY_SIBS:
				$mbway_tel=$_POST['mbway_tel'];
				if(!$mbway_tel)
				{
					//dados incompletos
					return false;
				}
				else
				{
					$return=getMetodoPagamentoMBWaySibsGateway($idEncomenda);
					if($return!=false)limparEncomenda();
				}
				break;
			case MP_TP_MBWAY_UNICRE:
				$mbway_tel=$_POST['mbway_tel'];
				if(!$mbway_tel)
				{
					//dados incompletos
					return false;
				}
				else
				{
					$return=getMetodoPagamentoMBWayUnicreGateway($idEncomenda);
					if($return!=false)limparEncomenda();
				}
				break;
			case MP_TP_MBWAY_EASYPAY:
				$mbway_tel=$_POST['mbway_tel'];
				if(!$mbway_tel)
				{
					//dados incompletos
					return false;
				}
				else
				{
					$return=getMetodoPagamentoMBWAYeasypay($idEncomenda);
					if($return!=false)limparEncomenda();
				}
				break;
			case MP_TP_MBWAY_LUSOPAY:
				limparEncomenda();
				$return=getMetodoPagamentoMBWAYLusopayGateway($idEncomenda);
				break;
			case MP_TP_MBWAY_IFTHEN:
				limparEncomenda();
				$return=getMetodoPagamentoMBWAYifthenGateway($idEncomenda);
				break;
			case MP_TP_CC_VIVAWALLET:
				//limparEncomenda();
				$return=getMetodoPagamentoVivawalletGateway($idEncomenda);
			case MP_TP_ONEY:
				//limparEncomenda();
				$return=getMetodoPagamentoOneyGateway($idEncomenda);
				break;
			case MP_TP_ONEY4:
				//limparEncomenda();
				$return=getMetodoPagamentoOneyGateway_4x($idEncomenda);
				break;
			case MP_TP_MB_CGD:
				limparEncomenda();
				$return=getMetodoPagamentoMBCGDGateway($idEncomenda);
				break;
			case MP_TP_MB_EUPAGO:
				limparEncomenda();
				$return=getMetodoPagamentoMBeupagoGateway($idEncomenda);
				break;
			case MP_TP_PAYSHOP_EUPAGO:
				limparEncomenda();
				$return=getMetodoPagamentoPayShopeupagoGateway($idEncomenda);
				break;
			case MP_TP_MBWAY_EUPAGO:
				$mbway_tel=$_POST['mbway_tel'];
				if(!$mbway_tel)
				{
					//dados incompletos
					return false;
				}
				else
				{
					$return=getMetodoPagamentoMBWAYEupagoGateway($idEncomenda);
					if($return!=false)limparEncomenda();
				}
				break;
			case MP_TP_MBWAY_CGD:
				$mbway_tel=$_POST['mbway_tel'];
				if(!$mbway_tel)
				{
					//dados incompletos
					return false;
				}
				else
				{
					$return=getMetodoPagamentoMBWayCGDGateway($idEncomenda);
					if($return!=false)limparEncomenda();
				}
				break;
		}
	}
	$rs->db_close();
	return $return;
}
function verificarClientePontos($cli_id,$enc_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql='SELECT valor FROM pontos';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()) {
		$valor=$rs->db_data["valor"];
	} else {
		$valor=0;
	}
	$rs->db_close();
	if($valor > 0) {
		$sql='SELECT SUM(TotalDocComIva) AS TotalCompras FROM encomendas WHERE ID_Cliente="'.$cli_id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()) {
			if($rs->db_data["TotalCompras"] > 0) {
				$TotalCompras=$rs->db_data["TotalCompras"];
			} else {
				$TotalCompras=0;
			}
		} else {
			$TotalCompras=0;
		}
		$rs->db_close();
		$sql='SELECT SUM(Limite) AS TotalLimites FROM pontos_registos WHERE ID_Cliente="'.$cli_id.'"';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF()) {
			if($rs->db_data["TotalLimites"] > 0) {
				$TotalLimites=$rs->db_data["TotalLimites"];
			} else {
				$TotalLimites=0;
			}
		} else {
			$TotalLimites=0;
		}
		$rs->db_close();
		$valorComparacao=$TotalCompras - $TotalLimites;
		if($valorComparacao >= $valor) {
			$sql='INSERT INTO pontos_registos(ID_Cliente,ID_Encomenda,Data,Limite) VALUES ("'.$cli_id.'","'.$enc_id.'","'.date("Y-m-d H:i:s").'","'.$valor.'")';
			$res=Runquery($db_link,$DB_name,$sql);
			$dados=cliente_logado();
			$body=array('[Nome]'=>$dados['Nome'],'[limite]'=>numberformat($valor));
			$message=template_email($GLOBALS['template_notificacaopontos'],$body);
			send_email($GLOBALS['Default_notifications_email'],$message['subject'],$message['body']);
		}
	}
}
function lista_compras()
{
	$path="core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from,$cli_id,$page_id,$key_wishlist_notloggedin,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;$rs3=new Dataobj;
	$i=0;
	if(!$dados=cliente_logado())
	{
		echo show_alerta2($key_wishlist_notloggedin[$site_lang]);
	}
	else
	{
		$url=seo('pages.php?page_id='.intval($_GET['page_id'])).'?show_compras=1';

		if(!$from) $from=0;
		$qtd=10;
		if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0)
		{
			 $total=1;
		}
		else
		{
			$sql_all="Select * from encomendas where ID_Cliente=$cli_id AND Estado > -1";
			$rs->db_open($db_link,$DB_name,$sql_all);
			if(!$rs->EOF()) $total=$rs->db_rows;
			$rs->db_close();
		}
		if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0) $sql="SELECT * FROM encomendas where ID_Cliente=$cli_id AND  Estado > -1 AND ID='".$_GET['id_enc']."' Order by ID desc limit $from,$qtd";
		else $sql="Select * from encomendas where ID_Cliente=$cli_id AND  Estado > -1 Order by ID desc limit $from,$qtd";
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$i=$count=0;?>
			<script>
			function cancelarEncomenda(id_enc){
				if(confirm("Deseja Cancelar a Encomenda Nº: "+id_enc+"?"))
				{
					return true;
				}
				return false;
			}
			function submitopt(id_enc,enc_token)
			{
				<?php if($GLOBALS['n_enc'] > 0){?>
				Apprise("<?=getLinguas('key_prod_cart_on')?>");
				<?php }else{?>
				var empty='';	var escolha='';
				$('.opt_subs').each(function(i,obj){ if($(this).val()=='' || $(this).val()==0) empty=1;});
				if(empty == 1)
				{
					Apprise("<?=getLinguas('key_shop_msg_sub')?>");
				}
				else if(confirm("<?=getLinguas('key_shop_msg_alterar')?>"))
				{
					//window.open('<?php echo seo('pages.php?page_id='.$GLOBALS['cart_page_id'])?>?id_enc='+id_enc+'&enc_token='+enc_token);
					return true;
				}
				<?php }?>
				return false;
			}
			</script>
			<?php
			if(isset($_GET['id_enc']) && $_GET['id_enc'] > 0)
			{
			?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td align="left" valign="middle" class="title2" style="font-weight:bold;text-transform:uppercase"><a href="<?php echo $url?>" class="preto" style="text-decoration:none">« Voltar à listagem de encomendas</a></td>
			</tr>
			</table>
			<br>
			<?php }?>
			<table width="100%" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="2" align="center">&nbsp;</td>
				<td width="140" align="center" valign="middle" class="title2" style="font-weight:bold;text-transform:uppercase;font-size:12px"><b>Data Encomenda</b></td>
				<td width="40"  align="center" valign="middle" class="title2" style="font-weight:bold;text-transform:uppercase;font-size:12px"><b>ID</b></td>
				<td width="140" align="center" valign="middle" class="title2" style="font-weight:bold;text-transform:uppercase;font-size:12px"><b>Tipo Pagamento</b></td>
				<td width="120" valign="middle" align="right" class="title2" style="font-weight:bold;text-transform:uppercase;font-size:12px"><b>Total</b></td>
				<td width="160" align="center" class="title2" style="font-weight:bold;text-transform:uppercase;font-size:12px"><b>Estado</b></td>
				<td align="center" class="title2" style="font-weight:bold;">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="7">&nbsp;</td>
			</tr>
			<?php
			while(!$rs->EOF())
			{
				$count++;
				$IDb=$rs->db_data["ID"];
				$Referencia=$rs->db_data["Referencia"];
				$Cond_pag=$rs->db_data["Cond_pag"];
				$Local_Entrega=$rs->db_data["Local_Entrega"];
				$Tipo=$rs->db_data["Tipo"];
				$Anulada=$rs->db_data["Anulada"];
				$RazaoAnulacao=$rs->db_data["RazaoAnulacao"];
				$ID_Cliente=$rs->db_data["ID_Cliente"];
				$N_Contribuinte=$rs->db_data["enc_Nif"];
				$Des_Social=$rs->db_data["Des_Social"];
				$Contacto=$rs->db_data["enc_Nome"].' '.$rs->db_data["enc_Apelido"];
				$ContactoEntrega=$rs->db_data["enc_Nome_ent"].' '.$rs->db_data["enc_Apelido_ent"];
				$Morada=$rs->db_data["enc_Morada"];
				$Localidade=$rs->db_data["enc_Localidade"];
				$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
				$Cidade=$rs->db_data["enc_Cidade"];
				$CidadeEntrega=$rs->db_data["enc_Cidade_ent"];
				$Pais=country_name($rs->db_data["enc_Pais"]);
				$PaisEntrega=country_name($rs->db_data["enc_Pais_ent"]);
				$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
				$LocalidadeEntrega=$rs->db_data["LocalidadeEntrega"];
				$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
				$telefone=$rs->db_data["enc_Telefone"];
				$email=$rs->db_data["enc_Email"];
				$Obs=$rs->db_data["Obs"];
				$Portes=$rs->db_data["Portes"];
				$Custos_logisticos=$rs->db_data["Custos_logisticos"];
				$PortesSemIva=$rs->db_data["PortesSemIva"];
				$Taxas=$rs->db_data["Taxas"];
				$TaxasSemIva=$rs->db_data["TaxasSemIva"];
				$Cobrar=$rs->db_data["Cobrar"];
				$TaxasPaypal=$rs->db_data["TaxasPaypal"];
				$ValorDesconto=$rs->db_data["ValorDesconto"];
				$ID_Vendedor=$rs->db_data["ID_Vendedor"];
				$ID_Distribuidor=$rs->db_data["ID_Distribuidor"];
				$PropostaDe=$rs->db_data["PropostaDe"];
				$ID_Armazem=$rs->db_data["ID_Armazem"];
				$Local_carga=$rs->db_data["Local_carga"];
				$Local_descarga=$rs->db_data["Local_descarga"];
				$Expedicao=$rs->db_data["Expedicao"];
				$Viatura_expedicao=$rs->db_data["Viatura_expedicao"];
				$ID_Expedicao=$rs->db_data["ID_Expedicao"];
				$IDGuia=$rs->db_data["IDGuia"];
				$Operador=$rs->db_data["Operador"];
				$ValorDescontoExtra=$rs->db_data["ValorDescontoExtra"];
				$ValorJustificado=$rs->db_data["ValorJustificado"];
				$TotalDocComIva=$rs->db_data["TotalDocComIva"];
				$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
				$TotalDocDescontos=$rs->db_data["TotalDocDescontos"];
				$TotalDocOutrasTaxas=$rs->db_data["TotalDocOutrasTaxas"];
				$TotalDocIva=$rs->db_data["TotalDocIva"];
				$Data_Criacao=$rs->db_data["Data_Criacao"];
				$Data_Impressao=$rs->db_data["Data_Impressao"];
				$Data_Doc_Forn=$rs->db_data["Data_Doc_Forn"];
				$Data_expedicao=$rs->db_data["Data_expedicao"];
				$Data_entrega=$rs->db_data["Data_entrega"];
				$Estado=$rs->db_data["Estado"];
				$m_guia=$rs->db_data["IDGuia"];
				$MOEDA=$rs->db_data["MOEDA"];
				$entidadeatm=$rs->db_data["Ent_atm"];
				$referenciaatm=$rs->db_data["Ref_atm"];
				$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
				$Metodo_expedicao=$rs->db_data["Metodo_envio"];
				$Encomenda_Paga=$rs->db_data["Encomenda_Paga"];
				$enc_token=$rs->db_data["enc_token"];
				$TotalIva=$rs->db_data["TotalIva"];
				$enc_info='';
				$corTexto='#FFF';
				if($count % 2 != 0)	{$cor="#999";$n="1";}
				else				{$cor="#777";$n="2";}

				$enc_state= estado_encomenda($Estado,$GLOBALS['site_lang']);
				$enc_info=$enc_state['titulo'];
				$cor= '#'.$enc_state['cor'];
				$corTexto='#000';

				$extra='';
				$i =0;
				global $id_enc;
				if(isset($id_enc)) $id_enc=intval($id_enc);
				if(isset($from)) $fromurl='&from='.$from;
				$url=seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1&id_enc='.$IDb.$fromurl;
				?>
				<tr onMouseOver="this.style.backgroundColor='#AAA'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'" style="color:<?php echo $corTexto;?>;background-color:<?php echo $cor;?>;font-size:12px">
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:16px;padding-top:4px" width="50" height="35" class="branco" align="center"><?php if($id_enc == $IDb){ }else{?>[+]<?php }?></td>
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:12px" valign="middle" align="center" class="branco"><?php echo $Data_Criacao;?></td>
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:12px" valign="middle" align="center" class="branco"><?php echo $IDb;?></td>
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:12px" valign="middle" align="center" width="150" class="branco"><?php echo getNomeMetodoPagamentoByTipo($Metodo_pagamento) ?></td>
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:12px" valign="middle" align="right" class="branco" ><?php echo  numberformat($TotalDocComIva);?> </td>
				<td onclick="window.open('<?php echo $url?>','_self');" style="cursor:pointer;font-size:12px" align="center"  class="branco" valign="middle"><?php echo $enc_info; ?> </td>
				<td  align="right" class="branco" valign="middle"><?php echo $extra;?> </td>
				</tr>
				<tr>
				<td colspan="7" height="1">
				<?php
				if($id_enc == $IDb)
				{
						if(isset($_GET['altera']) && isset($_GET['id_enc']) && intval($_GET['id_enc']) == intval($_GET['altera']))
						{
							$prod=array();
							$c=count($_GET);
							$keys=array_keys($_GET);
							$i=0;	while($i < $c){	if(substr_count($keys[$i],'prod_') && $_GET[$keys[$i]]!='') $prod[]=array('prod_id'=>str_replace('prod_','',$keys[$i]),'estado'=>$_GET[$keys[$i]]); $i++; }
							$substituir=0;
							if(count($prod) > 0)
							{
								for($a=0; $a<count($prod); $a++)
								{
									if($prod[$a]['estado'] == 'sub')
									{
										$sql='UPDATE encomendas_produtos SET Substituir=1 WHERE ID_PAI='.intval($_GET['id_enc']).' AND ID_Produto='.$prod[$a]['prod_id'].' AND ID_Cliente='.$cli_id;
										$res=Runquery($db_link,$DB_name,$sql);
										$substituir=1;
									}
									elseif($prod[$a]['estado'] > 0)
									{
										$sql='UPDATE encomendas_produtos SET Estado="'.$prod[$a]['estado'].'" WHERE ID_PAI='.intval($_GET['id_enc']).' AND ID_Produto='.$prod[$a]['prod_id'].' AND ID_Cliente='.$cli_id;
										$res=Runquery($db_link,$DB_name,$sql);
									}
									// echo $sql.'<br>';
								}
								if($substituir == 1)
								{
									?>
									<form id="reenc" method="POST" action="<?=seo('pages.php?page_id='.$GLOBALS['areacliente_page_id']).'?show_compras=1'; ?>" >
									<input type="hidden" name="id_enc2" value="<?php echo $IDb?>">
									<input type="hidden" name="reencomendar" value="1">
									</form><?php
									echo '<script>$(document).ready(function(){$("#reenc").submit();});</script>';
								}
							}
							else
							{
								echo '<script>$(document).ready(function(){Apprise("'.getLinguas('key_shop_msg_sub').'");});</script>';
							}
						}
						?>
						<form name="substituir_enc" action="<?php echo seo('pages.php?page_id='.$GLOBALS['registo_page_id'])?>" method="GET" onsubmit="return submitopt('<?php echo $IDb?>','<?php echo $enc_token?>')">
						<input type="hidden" name="show_compras" value="1" />
						<input type="hidden" name="id_enc" value="<?php echo intval($id_enc)?>" />
						<input type="hidden" name="altera" value="<?php echo intval($id_enc)?>" />
						<div style="height:1px;width:1px" id="ancora"></div>
						<br>
						<table width="100%" border=0 cellpadding="2" cellspacing="0">
						<tr>
						<td width="10"></td>
						<td align="left" width="100" style="font-weight:bold;text-transform:uppercase;padding:3px;font-size:12px"><?=getLinguas('txt_header_a')?></td>
						<td align="left" style="font-weight:bold;text-transform:uppercase;padding:3px;font-size:12px"><?=getLinguas('txt_header_b')?></td>
						<td align="left" width="5" style="font-weight:bold;padding:3px"></td>
						<td align="left" width="80" style="font-weight:bold;text-transform:uppercase;padding:3px;font-size:12px"><?=getLinguas('key_preco')?></td>
						<td align="center" width="50" style="font-weight:bold;text-transform:uppercase;padding:3px;font-size:12px"><?=getLinguas('txt_header_c')?></td>
						<td align="center" width="100" style="font-weight:bold;text-transform:uppercase;padding:3px;font-size:12px">Total <?=getLinguas('key_preco')?></td>
						<td align="right" width="200" style="font-weight:bold;text-align:right;text-transform:uppercase;padding:3px;font-size:12px"><?=getLinguas('key_folha_reparacao_estado')?></td>
						<td align="left" width="180" style="font-weight:bold;text-align:center;text-transform:uppercase;padding:3px">&nbsp;</td>
						<td align="left" width="20" >&nbsp;</td>
						</tr><?php
						$altera=0;
						$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI=$IDb AND Apagar=0 AND Estado!=8";
						$rs2->db_open($db_link,$DB_name,$sql);
						while(!$rs2->EOF())
						{
							$IDb=$rs2->db_data["ID"];
							$ID_Cliente=$rs2->db_data["ID_Cliente"];
							$ID_Produto=$rs2->db_data["ID_Produto"];
							$Ref=rtrim($rs2->db_data["Ref"],'-');
							$Nome=$rs2->db_data["Nome"];
							$Qtd=$rs2->db_data["Qtd"];
							$Tamanho=$rs2->db_data["Tamanho"];
							$Cor=$rs2->db_data["Cor"];
							$Valor=$rs2->db_data["Valor"];
							$Valor_Desconto=$rs2->db_data["Valor_Desconto"];
							$Tipo=$rs2->db_data["Tipo"];
							$m_estado=$rs2->db_data["Estado"];
							$enc_state2= estado_encomenda($m_estado,$GLOBALS['site_lang']);
							$ID_Conf=$rs2->db_data["ID_Conf"];
							$login=$rs2->db_data["login"];
							$Chave=$rs2->db_data["Chave"];
							$Obs=$rs2->db_data["Obs"];
							$url=str_replace($site_http_url,'',seo('pages.php?page_id='.$GLOBALS['products_page_id'],$ID_Produto,2));

							$cor_texto=color_inverse($enc_state2['cor'])
							?>
							<tr style="background:#<?php echo $enc_state2['cor']?>">
							<td style="border-bottom:#000 1px solid" width="10"></td>
							<td height="35" align="left" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><a class="preto" target="_blank" href="<?php echo $url?>" style="color:#<?php echo $cor_texto?>"><?php echo $Ref?></a></td>
							<td align="left" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>" ><?php echo $Nome?></td>
							<td align="left" valign="middle" width="5px" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"></td>
							<td align="left" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>" ><?php echo numberformat($Valor)?></td>
							<td align="center" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>" ><?php echo $Qtd?></td>
							<td align="right" valign="middle" style="border-bottom:#000 1px solid" ><?php echo numberformat(($Valor * $Qtd)-$Valor_Desconto)?></td>
							<td align="right" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>"><?php echo $enc_state2['titulo']?></td>
							<td align="right" valign="middle" style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>">
							<?php if($enc_state2['nivel']==3.1)
							{
								$altera=1;
								$status=estados_encomenda(array('3.3','6'),$GLOBALS['site_lang']);
								if(is_array($status))
								{
								?>
								<select class="opt_subs" style="width:150px" name="prod_<?php echo $ID_Produto?>">
								<?php
								echo '<option value="">'.getLinguas('key_escolher').'</option>';
								echo '<option value="sub">'.getLinguas('key_substituir').'</option>';
								for($a=0; $a<count($status); $a++)
								{
									echo '<option value="'.$status[$a]['estado_id'].'">'.$status[$a]['titulo'].'</option>';
								}
								?>
								</select>
								<?php
								}
							}
							elseif($enc_state2['nivel']==3.2)
							{
								$altera=1;
								$status=estados_encomenda(array('6'),$GLOBALS['site_lang']);
								if(is_array($status))
								{
								?>
								<select class="opt_subs" style="width:150px" name="prod_<?php echo $ID_Produto?>">
								<?php
								echo '<option value="">'.getLinguas('key_escolher').'</option>';
								echo '<option value="sub">'.getLinguas('key_substituir').'</option>';
								for($a=0; $a<count($status); $a++)
								{
									echo '<option value="'.$status[$a]['estado_id'].'">'.$status[$a]['titulo'].'</option>';
								}
								?>
								</select>
								<?php
								}
							} ?></td>
							<td style="border-bottom:#000 1px solid;color:#<?php echo $cor_texto?>" width="10"></td>
							</tr>
							<?php
							$rs2->db_next();
						}
						$rs2->db_close();
						?>
						</table>
						<?php //echo showInfoEncomenda($id_enc,"98%");?>
						<br />

						<?php
						if($altera == 1)
						{
							echo '<center><input type="submit" class="button6" value="'.getLinguas('key_alterar').'"></center>';
						}
					?></form><?php
				}
				?></td></tr>
				<?php
				$rs->db_next();
				$i++;
			}
			?>
			</table>
			<?php
			$rs->db_close();
			echo show_paginacao_encomendas($total,$qtd);
		}else{
			echo show_alerta2('Não existem encomendas');
		}
		$rs->db_close();
	}
	return $i;
}
function getNomeMetodoExpedicaoByTipo($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;
	$sql="SELECT me.Tipo,mel.Cabecalho,mel.Cabecalho2 FROM metodos_expedicao me inner join metodos_expedicao_lang mel on mel.ID_PAI=me.ID WHERE me.ID=$tipo AND mel.Lang='".$site_lang."'";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodo_expedicao=($rs2->db_data['Tipo']==2 ? ($rs2->db_data['Cabecalho2'].' '.$rs2->db_data['Cabecalho']) : $rs2->db_data['Cabecalho']);
	}
	$rs2->db_close();
	return $metodo_expedicao;
}
function getNomeMetodoPagamentoByTipo($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;
	$sql="SELECT mpl.Cabecalho FROM metodos_pagamento mp inner join metodos_pagamento_lang mpl on mpl.ID_PAI=mp.ID WHERE (mp.ID=$tipo OR mp.Tipo=$tipo) AND mpl.Lang='".$site_lang."'";
	// echo $sql;
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$metodo_pagamento=$rs2->db_data['Cabecalho'];
	}
	$rs2->db_close();
	return $metodo_pagamento;
}
function show_paginacao_encomendas($total,$qtd)
{
	$str='';
	global $from;
	$from=intval($from) <=0 ? 1 : $from;
	if($total > $qtd) {
		$total=ceil($total / $qtd);
		$str .= '<div class="news_wrapper">';
		$str .= '<div class="paginacao">';
		if($from > 6) {
			$p_seo=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_compras=1';
			if(substr_count($p_seo,'?')) {
				$ash='&from=1';
			} else {
				$ash='?from=1';
			}
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$str .= '<button class="product_button" '.$pgn.' type="button">1</button>';
		}
		for ($pi=($from - 6); $pi < ($from + 7); $pi++) {
			$p_seo=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_compras=1';
			if(substr_count($p_seo,'?')) {
				$ash='&from='.$pi;
			} else {
				$ash='?from='.$pi;
			}
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$str .= ($pi >= 1 && $pi <= $total) ? $from == $pi ? '<button class="product_button_selected" '.$pgn.' type="button">'.sprintf(($pi)).'</button>' : '<button class="product_button" '.$pgn.' type="button">'.sprintf(($pi)).'</button>' : '';
		}
		if($total != $from && $from + 6 < $total) {
			$p_seo=seo('pages.php?page_id='.$GLOBALS['minhaconta_page_id']).'?show_compras=1';
			if(substr_count($p_seo,'?')) {
				$ash='&from='.$total;
			} else {
				$ash='?from='.$total;
			}
			$pgn='onclick=window.open("'.$p_seo.$ash.'","_self")';
			$str .= '<button title="Próximo" class="product_button" '.$pgn.' type="button"><img border="0" src="'.$GLOBALS['site_http_url'].'layout/default/filtros/foward.png"></button>';
		}
		$str .= '</div><div class="c"></div>';
	}
	return $str;
}
function showInfoEncomenda($enc_id,$width='100%',$external=0,$extramsg="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$usar_token_enc;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs3=new Dataobj;
	$messagebody='';
	$width='100%';
	$sql="SELECT * FROM encomendas WHERE ID='{$enc_id}'";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$IDb=$rs->db_data["ID"];
		$Referencia=$rs->db_data["Referencia"];
		$N_Contribuinte=$rs->db_data["enc_Nif"];
		$Contacto=$rs->db_data["enc_Nome"].' '.$rs->db_data["enc_Apelido"];
		$Contacto2=$rs->db_data["enc_Nome_ent"].' '.$rs->db_data["enc_Apelido_ent"];
		$Morada=$rs->db_data["enc_Morada"];
		$Localidade=$rs->db_data["enc_Cidade"];
		$Cod_Pos=$rs->db_data["enc_Codigo_Postal"];
		$MoradaEntrega=$rs->db_data["enc_Morada_ent"];
		$LocalidadeEntrega=$rs->db_data["enc_Cidade_ent"];
		$Cod_PosEntrega=$rs->db_data["enc_Codigo_Postal_ent"];
		$telefone=$rs->db_data["enc_Telefone"];
		$telefoneEntrega=($rs->db_data["enc_Telefone_ent"] == '' ? $rs->db_data["enc_Telefone"] : $rs->db_data["enc_Telefone_ent"]);
		$email=$rs->db_data["enc_Email"];
		$Portes=$rs->db_data["Portes"];
		$Custos_logisticos=$rs->db_data["Custos_logisticos"];
		$Taxas=$rs->db_data["Taxas"];
		$ValorDesconto=$rs->db_data["ValorDesconto"];
		$TotalDocComIva=$rs->db_data["TotalDocComIva"];
		$TotalDocSemIva=$rs->db_data["TotalDocSemIva"];
		$TotalIva=$rs->db_data["TotalIva"];
		$Data_Criacao=$rs->db_data["Data_Criacao"];
		$entidadeatm=$rs->db_data["Ent_atm"];
		$referenciaatm=$rs->db_data["Ref_atm"];
		$Metodo_pagamento=$rs->db_data["Metodo_pagamento"];
		$Metodo_expedicao=$rs->db_data["Metodo_envio"];
		$enc_pagamento_dados=$rs->db_data["enc_pagamento_dados"];
		$IDGuia=$rs->db_data["IDGuia"];
		$Obs=$rs->db_data["Obs"];
		$Pais=$rs->db_data["enc_Pais"];
		$PaisEntrega=$rs->db_data["enc_Pais_ent"];
		$Metodo_pagamentoNome=getNomeMetodoPagamentoByID($Metodo_pagamento);
		$Metodo_expedicaoNome=getNomeMetodoExpedicaoById($Metodo_expedicao);

		$messagebody .= '<div id="encomenda-bloco" style="max-width:900px;margin: 0 auto">';
		$messagebody .= '<div style="clear:both"></div>';
		$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0">';
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';

		if($usar_token_enc==1)$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;" colspan="2">Nº da Encomenda: '.$Referencia.'</td>';
		else $messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;" colspan="2">Nº da Encomenda: '.$IDb.'</td>';


		$messagebody .= '</tr>';
		if($IDGuia!='')
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
			$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;" colspan="2">TRAKING NUMBER: '.$IDGuia.'</td>';
			$messagebody .= '</tr>';
		}
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;" colspan="2">Encomenda efetuada: '.date('Y-m-d',strtotime($Data_Criacao)).'</td>';
		$messagebody .= '</tr>';
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td width="50%" style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;text-align:left">Dados de Facturação</td>';
		$messagebody .= '<td width="50%" style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7;text-align:left">Endereço de Envio</td>';
		$messagebody .= '</tr>';
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td style="font-size:12px;padding:10px;">';
		$messagebody .= '<span style="font-weight: bold;">'.$Contacto.'</span><br><br>';
		$messagebody .= 'Morada: '.$Morada.'<br>';
		$messagebody .= 'Código Postal: '.$Cod_Pos.'<br>';
		$messagebody .= 'Localidade: '.$Localidade.'<br>';
		$messagebody .= 'Pais: '.country_name($Pais).'<br><br>';
		$messagebody .= 'Email: '.$email.'<br>';
		$messagebody .= 'Nº Contribuinte: '.$N_Contribuinte.'<br>';
		$messagebody .= 'Telefone: '.$telefone.'<br>';
		$messagebody .= '</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;">';
		$messagebody .= '<span style="font-weight: bold;">'.$Contacto2.'</span><br><br>';
		$messagebody .= 'Morada: '.$MoradaEntrega.'<br>';
		$messagebody .= 'Código Postal: '.$Cod_PosEntrega.'<br>';
		$messagebody .= 'Localidade: '.$LocalidadeEntrega.'<br>';
		$messagebody .= 'Pais: '.country_name($PaisEntrega).'<br><br>';
		$messagebody .= 'Telefone: '.$telefoneEntrega.'<br><br><br>';
		$messagebody .= '</td>';
		$messagebody .= '</tr>';
		$messagebody .= '</table>';
		if($Obs !='')
		{
			$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0">';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7">Observações</td>';
			$messagebody .= '</tr>';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;padding:10px">'.$Obs.'</td>';
			$messagebody .= '</tr>';
			$messagebody .= '</table>';
		}
		if($Metodo_expedicaoNome != '')
		{
			$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0">';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7">Entregas</td>';
			$messagebody .= '</tr>';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;padding:10px">'.$Metodo_expedicaoNome.'</td>';
			$messagebody .= '</tr>';
			$messagebody .= '</table>';
		}
		$m_pagamento_tipo=getNomeMetodoPagamentoByTipo_ID($Metodo_pagamento);
		if($m_pagamento_tipo == MP_TP_TRANS_BANCARIA || $m_pagamento_tipo == MP_TP_MB_LUSOPAY || $m_pagamento_tipo == MP_TP_MB_IFTHEN || $m_pagamento_tipo == MP_TP_MB_EASYPAY || $m_pagamento_tipo == MP_TP_MB_SIBS || $m_pagamento_tipo == MP_TP_MB_EUPAGO || $m_pagamento_tipo == MP_TP_PAYSHOP_IFTHEN)
		{
			$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0">';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td width="50%" style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7">Pagamento Escolhido</td>';
			$messagebody .= '<td width="50%" style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7">Informações para pagamento</td>';
			$messagebody .= '</tr>';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;padding:10px">'.$Metodo_pagamentoNome.'</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px" align="left">'.stripslashes($enc_pagamento_dados).'</td>';
			$messagebody .= '</tr>';
			$messagebody .= '</table>';
		}
		else
		{
			$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0">';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7">'.getLinguas('key_pagamento').'</td>';
			$messagebody .= '</tr>';
			$messagebody .= '<tr style="border:1px solid #d6d5d5">';
			$messagebody .= '<td style="font-size:12px;padding:10px">'.$Metodo_pagamentoNome.'</td>';
			$messagebody .= '</tr>';
			$messagebody .= '</table>';
		}
		$messagebody .= '<table width="'.$width.'" cellspacing="0" cellpadding="0" border="0" style="margin-top:17px">';
		$messagebody .= '<tr style="border:1px solid #d6d5d5">';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7" width="10%" valign="middle">Referência</td>';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7" width="50%" valign="middle">Nome Produto</td>';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7" width="10%" valign="middle" align="right">Qtd</td>';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7" width="15%" valign="middle" align="right">Preço Unitário</td>';
		$messagebody .= '<td style="font-size:12px;font-weight:bold;padding:10px;background:#e7e7e7" width="15%" valign="middle" align="right">Subtotal</td>';
		$messagebody .= '</tr>';
		$messagebody .= get_produto_encomendado($enc_id);
		$ofertas=get_produto_encomendado_ofertas($enc_id);
		$messagebody .= $ofertas['messagebody'];

		$messagebody .= '<tr style="border:1px solid #d6d5d5;border-top:none !important;border-bottom:none !important">';
		$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" colspan="4" align="right"><span style="font-weight:bold">'.getLinguas('key_Subtotal',$GLOBALS['site_lang']).'</span></td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" align="right">'.numberformat($TotalDocSemIva, 1).'</td>';
		$messagebody .= '</tr>';
		if($Portes > 0)
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;border-top:none !important;border-bottom:none !important">';
			if($Custos_logisticos>0) $messagebody .= '<td style="font-size:12px;padding:10px" colspan="4" align="right"><span style="font-weight:bold;">Portes</span></td>';
			else $messagebody .= '<td style="font-size:12px;padding:10px" colspan="4" align="right"><span style="font-weight:bold;">Portes(IVA inc.)</span></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px" align="right">'.numberformat($Portes,1).'</td>';
			$messagebody .= '</tr>';
		}
		else
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;border-top:none !important;border-bottom:none !important">';
			$messagebody .= '<td style="font-size:12px;padding:10px" colspan="4" align="right"><span style="font-weight:bold;">Portes</span></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px" align="right">Gratuíto</td>';
			$messagebody .= '</tr>';
		}

		if($Taxas > 0)
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;border-top:none !important;border-bottom:none !important">';
			$messagebody .= '<td style="font-size:12px;padding:10px;" colspan="4" align="right"><span style="font-weight:bold">Taxas</span></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;" align="right">'.numberformat($Taxas, 1).'</td>';
			$messagebody .= '</tr>';
		}
		$messagebody .= '<tr style="border:1px solid #d6d5d5;border-top:none !important;border-bottom:none !important;">';
		$messagebody .= '<td style="font-size:12px;padding:10px" colspan="4" align="right"><span style="font-weight:bold">IVA</span></td>';
		$messagebody .= '<td style="font-size:12px;padding:10px" align="right">'.numberformat($TotalIva, 1).'</td>';
		$messagebody .= '</tr>';
		$messagebody .= '<tr style="border:1px solid #d6d5d5">';
		$messagebody .= '<td style="font-size:12px;padding:10px" colspan="4" align="right"><span style="font-weight:bold">TOTAL</span></td>';
		$messagebody .= '<td style="font-size:12px;padding:10px" align="right">'.numberformat($TotalDocComIva, 1).'</td>';
		$messagebody .= '</tr>';
		$messagebody .= '</table>';
		$messagebody .= '</div>';
	}
	$rs->db_close();
	return $messagebody;
}
function get_produto_encomendado($enc_id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs3=new DataObj;

	// Tipo=1 PRODUTOS
	// Tipo=0 Extensoes garantia,portes,etc
	// Tipo=2 PROMOCOES
	$messagebody='';
	$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI=".$enc_id.' AND ID_Produto_Pai=0 AND Tipo=1 ORDER BY ID ASC';
	// echo $sql.'<br>';
	$rs3->db_open($db_link,$DB_name,$sql);
	while (!$rs3->EOF())
	{
		$ID_linha_Produto=$rs3->db_data["ID"];
		$ID_Produto=$rs3->db_data["ID_Produto"];
		$Ref=$rs3->db_data["Ref"];
		$Nome=$rs3->db_data["Nome"];
		$Qtd=$rs3->db_data["Qtd"];
		$Valor=$rs3->db_data["Valor"];
		$Valor_Desconto=0;//$rs3->db_data["Valor_Desconto"];
		$Perc_Desconto=0;//$rs3->db_data["Perc_Desconto"];
		$voucher_id=$rs3->db_data["voucher_id"];

		$valorqtd=$Valor * $Qtd;
		$desconto=0;
		$oferta=0;
		if($Perc_Desconto == 100) {
			$oferta=1;
		} elseif($Perc_Desconto > 0) {
			$desconto=$Qtd * $Valor_Desconto;
			$descontoNome='DESCONTO';
			// $descontoNome .= ' ('.round($Perc_Desconto,2).'%)';
		}

		$urlprod='';
		if($voucher_id == 0)
		{
			$urlprod=seo('pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$ID_Produto,$ID_Produto,2);
			$info=get_produtc_info($ID_Produto);
		}
		if($voucher_id == 0)
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
			$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle"><b>'.$Ref.'</b></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle"><b>'.$Nome.'</b></td>';
			if($oferta)
			{
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right">Oferta</td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right">&nbsp;</td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right">&nbsp;</td>';
			}
			else
			{
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right"><b>'.$Qtd.'</b></td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right"><b>'.numberformat($Valor, 1).'</b></td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right"><b>'.numberformat($valorqtd, 1).'</b></td>';
			}
			$messagebody .= '</tr>';
		/*  if($desconto)
			{
				$messagebody .= '<tr><td style="font-size:12px;padding:10px;" colspan=3 align="right" valign="middle">'.$descontoNome.': </td><td style="font-size:12px;padding:10px;" align="right" valign="middle">-'.numberformat($desconto).'</td></tr>';
				$messagebody .= '<tr><td style="font-size:12px;padding:10px;" colspan=3 align="right" valign="middle">SUB-TOTAL: </td><td style="font-size:12px;padding:10px;" align="right" valign="middle">'.numberformat($valorqtd - $desconto).'</td></tr>';
			} */
		}
		else
		{
			if($Ref != "")
			{
				$info_prod=get_produtc_info($ID_Produto);
				$messagebody .= '<tr>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid"valign="middle" align="center"><img src="'.$GLOBALS['site_http_url'].$info_prod['prd_imgm_cart'].'" border="0"></td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="center">';
				$messagebody .= '<div class="titulo">'.getLinguas('key_voucher_oferta2',$GLOBALS['site_lang']).'</div>';
				$messagebody .= '<div class="subtitulo">'.$Nome.'</div>';
				$messagebody .= '</td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="center"></td>';
				$messagebody .= '<td style="font-size:12px;padding:10px;border-top:#d6d5d5 1px solid" valign="middle" align="right"></td>';
				$messagebody .= '</tr>';
			}
		}
		// DEVOLVE LINHAS DAS PROMOCOES
		$total_descontos=0; $tem_descontos=0;
		$messagebody_res=get_produto_encomendado_promos($enc_id,$ID_linha_Produto);
		if($messagebody_res['messagebody'])
		{
			$tem_descontos=1;
			$messagebody .= $messagebody_res['messagebody'];
			$total_descontos=$messagebody_res['valorqtd_total'];
		}
		// DEVOLVE LINHAS DAS PORTES/EXTENSAO GARANTIA ETC
		$total_outros=0; $tem_outros=0;
		$messagebody_res=get_produto_encomendado_outros($enc_id,$ID_linha_Produto);
		if($messagebody_res['messagebody'])
		{
			$tem_outros=1;
			$messagebody .= $messagebody_res['messagebody'];
			$total_outros=$messagebody_res['valorqtd_total'];
		}
		if($total_descontos > 0)
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right">&nbsp;</td>';
			//$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right"><b>'.getLinguas('key_Subtotal',$GLOBALS['site_lang']).':</b></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right"><b>'.numberformat((($valorqtd - $total_descontos) + $total_outros), 1).'</b></td>';
			$messagebody .= '</tr>';
			// $messagebody .= '<tr><td style="font-size:12px;padding:10px;" colspan=3 align="right" valign="middle">'.$descontoNome.': </td><td style="font-size:12px;padding:10px;" align="right" valign="middle">-'.numberformat($desconto).'</td></tr>';
			// $messagebody .= '<tr><td style="font-size:12px;padding:10px;" colspan=3 align="right" valign="middle">SUB-TOTAL: </td><td style="font-size:12px;padding:10px;" align="right" valign="middle">'.numberformat(($valorqtd - $total_descontos) + $total_outros).'</td></tr>';
		}
		elseif($tem_outros == 1)
		{
			$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right">&nbsp;</td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right">&nbsp;</td>';
			//	$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right"><b>'.getLinguas('key_Subtotal',$GLOBALS['site_lang']).':</b></td>';
			$messagebody .= '<td style="font-size:12px;padding:10px;background:#ececec" valign="middle" align="right"><b>'.numberformat(($valorqtd + $total_outros), 1).'</b></td>';
			$messagebody .= '</tr>';
		}
		$rs3->db_next();
	}
	$rs3->db_close();
	$rs3->db_close();
	return $messagebody;
}
function get_produto_encomendado_promos($enc_id=0,$produto_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs3=new DataObj;
	$messagebody=''; $valorqtd_total=0;
	$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI=".$enc_id.' AND ID_Produto_Pai='.$produto_pai.' AND Tipo=2 ORDER BY ID ASC';
	$rs3->db_open($db_link,$DB_name,$sql);
	while (!$rs3->EOF())
	{
		$ID_linha_Produto=$rs3->db_data["ID"];
		$ID_Produto=$rs3->db_data["ID_Produto"];
		$Ref=$rs3->db_data["Ref"];
		$Nome=$rs3->db_data["Nome"];
		$Qtd=$rs3->db_data["Qtd"];
		$Valor=$rs3->db_data["Valor"];
		$Valor_Desconto=0;//$rs3->db_data["Valor_Desconto"];
		$Perc_Desconto=0;//$rs3->db_data["Perc_Desconto"];
		$voucher_id=$rs3->db_data["voucher_id"];

		$valorqtd=$Valor;
		$valorqtd_total += $Valor;
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">'.$Ref.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">'.$Nome.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">&nbsp;</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">&nbsp;</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right"> - '.numberformat($valorqtd).'</td>';
		$messagebody .= '</tr>';
		$rs3->db_next();
	}
	$rs3->db_close();
	return array('messagebody'=>$messagebody,'valorqtd_total'=>$valorqtd_total);
}
function get_produto_encomendado_outros($enc_id=0,$produto_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs3=new DataObj;
	$messagebody=''; $valorqtd_total=0;
	$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI=".$enc_id.' AND ID_Produto_Pai='.$produto_pai.' AND Tipo=0 ORDER BY ID ASC';
	$rs3->db_open($db_link,$DB_name,$sql);
	while (!$rs3->EOF())
	{
		$ID_linha_Produto=$rs3->db_data["ID"];
		$ID_Produto=$rs3->db_data["ID_Produto"];
		$Ref=$rs3->db_data["Ref"];
		$Nome=$rs3->db_data["Nome"];
		$Qtd=$rs3->db_data["Qtd"];
		$Valor=$rs3->db_data["Valor"];
		$Valor_Desconto=0;//$rs3->db_data["Valor_Desconto"];
		$Perc_Desconto=0;//$rs3->db_data["Perc_Desconto"];
		$voucher_id=$rs3->db_data["voucher_id"];

		$valorqtd=$Valor * $Qtd;
		$valorqtd_total += ($Valor * $Qtd);
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">'.$Ref.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">'.$Nome.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">'.$Qtd.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">'.($Valor > 0 ? numberformat($Valor) : '').'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">'.numberformat($valorqtd).'</td>';
		$messagebody .= '</tr>';

		$rs3->db_next();
	}
	$rs3->db_close();
	return array('messagebody'=>$messagebody,'valorqtd_total'=>$valorqtd_total);
}
function get_produto_encomendado_ofertas($enc_id=0,$produto_pai=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs3=new DataObj;
	$messagebody=''; $valorqtd_total=0;
	$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI=".$enc_id.' AND ID_Produto_Pai='.$produto_pai.' AND Tipo=3 ORDER BY ID ASC';
	$rs3->db_open($db_link,$DB_name,$sql);
	while (!$rs3->EOF())
	{
		$ID_linha_Produto=$rs3->db_data["ID"];
		$ID_Produto=$rs3->db_data["ID_Produto"];
		$Ref=$rs3->db_data["Ref"];
		$Nome=$rs3->db_data["Nome"];
		$Qtd=$rs3->db_data["Qtd"];
		$Valor=$rs3->db_data["Valor"];
		$Valor_Desconto=0;//$rs3->db_data["Valor_Desconto"];
		$Perc_Desconto=0;//$rs3->db_data["Perc_Desconto"];
		$voucher_id=$rs3->db_data["voucher_id"];

		$valorqtd=$Valor * $Qtd;
		$valorqtd_total += ($Valor * $Qtd);
		$messagebody .= '<tr style="border:1px solid #d6d5d5;">';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">&nbsp;</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle">'.$Nome.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">'.$Qtd.'</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">&nbsp;</td>';
		$messagebody .= '<td style="font-size:12px;padding:10px;" valign="middle" align="right">Oferta</td>';
		$messagebody .= '</tr>';

		$rs3->db_next();
	}
	$rs3->db_close();
	return array('messagebody'=>$messagebody,'valorqtd_total'=>$valorqtd_total);
}
function showCurrency()
{
	$result='';
	$pageURL=get_uri();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT * FROM currency WHERE currency_estado=1";
	$rs->db_open($db_link,$DB_name,$sql);
	$t=0;
	$currency=array();
	while (!$rs->EOF())
	{
		$currency[$t]['ID']=$rs->db_data['currency_id'];
		$currency[$t]['country']=$rs->db_data['country'];
		$currency[$t]['symbol']=$rs->db_data['currency_symbol'];
		$currency[$t]['img_ext']=$rs->db_data['img_ext'];
		$rs->db_next();
		$t++;
	}
	$rs->db_close();
	$c=0;
	while ($c < $t) {
		$country=$currency[$c]['country'];
		$pageURL=str_replace("?site_country=".$country,"",$pageURL);
		$pageURL=str_replace("&site_country=".$country,"",$pageURL);
		$c++;
	}
	if(substr_count($pageURL,"?") > 0) {
		$op="&";
	} else {
		$op="?";
	}
	if($t > 1) {
		$result .= '<div class="CurrencyT helvRm">';
		$c=0;
		while ($c < $t) {
			$img_ext=$currency[$c]['img_ext'];
			$country=$currency[$c]['country'];
			$ID=$currency[$c]['ID'];
			$symbol=$currency[$c]['symbol'];
			$img_ext=$currency[$c]['img_ext'];
			if($img_ext) {
				$img='<img src="'.$site_http_url.'/files/currency/'.$ID.'_1.'.$img_ext.'">&nbsp;';
			} else {
				$img=$symbol;
			}
			$result .= '<button onclick="window.open(\''.$pageURL.$op.'site_country='.$country.'\',\'_self\')">'.$img.'</button>';
			$c++;
		}
		$result .= '</div>';
	}
	return $result;
}
function hasProdutosCarrinhoTemporario($cli_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$total=0;
	$sql='SELECT count(*) AS Num FROM encomendas_temp WHERE ID_Cliente="'.$cli_id.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$total=$rs->db_data['Num'];
	}
	$rs->db_close();
	return $total;
}
function getNomeMetodoPagamentoByTipo_ID($tipo)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang,$site_http_url;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2=new DataObj;
	$Tipo=0;
	$sql="SELECT Tipo FROM metodos_pagamento WHERE ID=$tipo";
	$db_res=$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$Tipo=$rs2->db_data['Tipo'];
	}
	$rs2->db_close();
	return $Tipo;
}
function getProdutosCarrinhoTemporario($cli_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$erro='';
	$sql='SELECT * FROM encomendas_temp WHERE ID_Cliente="'.$cli_id.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function insertProdutosCarrinhoTemporario($cli_id,$ID_Produto,$Qtd)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$sql='SELECT * FROM encomendas_temp WHERE ID_Cliente="'.$cli_id.'" AND ID_Produto="'.$ID_Produto.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if($rs->EOF())
	{
		$sql='INSERT INTO encomendas_temp(ID_Cliente,ID_Produto,Qtd) VALUES ("'.$cli_id.'","'.$ID_Produto.'","'.$Qtd.'")';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	else
	{
		$Qtd2=intval($rs->db_data['Qtd']) + $Qtd;
		$sql='UPDATE encomendas_temp SET Qtd="'.$Qtd2.'" WHERE ID_Cliente="'.$cli_id.'" AND ID_Produto="'.$ID_Produto.'"';
		$res=Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
}
function deleteProdutosCarrinhoTemporario($cli_id,$ID_Produto=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($ID_Produto > 0) {
		$and=' AND ID_Produto="'.$ID_Produto.'"';
	} else {
		$and='';
	}
	$sql='DELETE FROM encomendas_temp WHERE ID_Cliente="'.$cli_id.'"'.$and;
	$res=Runquery($db_link,$DB_name,$sql);
}
function showMensagemProdutosCarrinhoTemporario($cli_id)
{
	?>
	<form id="carrinho_temp" method="post" name="carrinho_temp" action="<?=seo('pages.php?page_id='.$GLOBALS['registo_page_id']).'?show_compras=1'; ?>">
		<input type="hidden" id="tipo_carrinho_temp" name="tipo_carrinho_temp" value="0"/>
		<div class="titulo_pagina">Carrinho guardado</div>
		<br> <br> <br>
		<table border="0" cellpadding="0" cellspacing="10" style="margin:0 auto">
			<tr>
				<td colspan="3">“Deseja voltar a carregar os produtos no seu carrinho? Se sim,todos os produtos já adicionados serão adicionados ao carrinho.
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td align="center">
					<div class="tip-left"></div>
					<div class="button_arrow HvMd" onclick="document.getElementById('tipo_carrinho_temp').value=0;document.carrinho_temp.submit();">
						NÃO
					</div>
				</td>
				<td align="center" width="10">&nbsp;</td>
				<td align="center">
					<div class="button_arrow_green HvMd" onclick="document.getElementById('tipo_carrinho_temp').value=1;document.carrinho_temp.submit();">
						SIM
					</div>
					<div class="tip-right_green"></div>
				</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
		</table>
	</form>
	<?php
}
function handleCarrinhoTemporario()
{
	global $cli_id,$site_http_url;
	$tipo_carrinho_temp=$_POST['tipo_carrinho_temp'];
	if($tipo_carrinho_temp == 0)
	{
		deleteProdutosCarrinhoTemporario($cli_id);
		echo '<script>window.open("'.$site_http_url.'","_self");</script>';
	}
	elseif($tipo_carrinho_temp == 1)
	{
		global $shop,$n_enc,$d;
		unset($_SESSION['ss_shop']);
		unset($_SESSION['ss_n_enc']);
		$shop=array();
		$n_enc=0;
		if($GLOBALS['module_voucher'] == 1)
		{
			global $voucher,$n_voucher;
			unset($_SESSION['ss_voucher']);
			unset($_SESSION['ss_n_voucher']);
			$voucher=array();
			$n_voucher=0;
		}
		$listprods='';
		$listprodsqtds='';
		$rs=getProdutosCarrinhoTemporario($cli_id);
		while (!$rs->EOF())
		{
			$prod_id=$rs->db_data['ID_Produto'];
			$prod_add_qtd=$rs->db_data['Qtd'];
			$verifica=verifica_stock($prod_id); // se for um produto,vamos ver se tem stock
			if($verifica == 1)
			{
				$i=1;
				$found=0;
				while ($i <= $n_enc && $found == 0)
				{
					if($shop[$i][1] == $prod_id && $shop[$i][5] == $prod_id)
					{
						$found=$i;
					}
					$i++;
				}

				$available_stock=get_stock($prod_id); // se for um produto,vamos ver se tem stock

				if(!$found)
				{
					$n_enc++; // mais uma linha de encomenda
					$shop[$n_enc][1]=$prod_id; //codigo do produto

					if($prod_add_qtd <= $available_stock)$shop[$n_enc][2] = $prod_add_qtd;
					else $shop[$n_enc][2] = $available_stock;

					$shop[$n_enc][3]=1; // tipo de produto ( 1=produtos 2=Club)
					$shop[$n_enc][4]=$d; // chave seguranca
					$shop[$n_enc][5]=$prod_id; // chave seguranca
				}
				else
				{
					$shop[$found][1]=$prod_id; //codigo do produto

					if($shop[$found][2]+$prod_add_qtd <= $available_stock)$shop[$found][2]+= $prod_add_qtd; //quantidade do produto por defeito
					else $shop[$found][2] = $available_stock;

					$shop[$found][3]=1; // tipo de produto ( 1=produtos 2=Club)
					$shop[$found][4]=$d; // chave seguranca
					$shop[$found][5]=$prod_id; // chave seguranca
				}
			}
			$rs->db_next();
		}
		$rs->db_close();

		$_SESSION['ss_shop']=$shop;
		$_SESSION['ss_n_enc']=$n_enc;
		echo '<script>window.open("'.seo("pages.php?page_id=".$GLOBALS['cart_purchage_id']).'","_self");</script>';
	}
	exit;
}
function validaCarrinho()
{
	global $n_enc,$shop;
	$i=1;
	$valid_prods=0;
	$emailmsg='';
	while ($i <= $n_enc)
	{
		$prod_id=$shop[$i][1];
		$qtd=$shop[$i][2];
		$tipo=$shop[$i][3];
		$chave=$shop[$i][4];
		$prod_obj=$shop[$i][5];

		$verifica=verifica_stock($prod_id);
		if($verifica == 1)
		{
			$qtd_enc_default=verifica_qtd($prod_id,$qtd);
			if($qtd <= 0)
			{
				$qtd=$qtd_enc_default;
			}
			$infoprod=get_produtc_info($prod_id);
			if($infoprod)
			{
				if($infoprod['prod_carrinho'] == 1 && $infoprod['prod_precos']['PVP'] > 0) {
					$valid_prods++;
				} else {
					unset($shop[$i]);
					$emailmsg='Tentativa de fraude por produto sem carrinho visivel e/ou sem PVP - Produto IDº '.$prod_id;
				}
			} else {
				unset($shop[$i]);
				$emailmsg='Tentativa de fraude por produto não aprovado/inexistente - Produto IDº '.$prod_id;
			}
		} else {
			unset($shop[$i]);
			$emailmsg='Tentativa de fraude por produto sem stock - Produto IDº '.$prod_id;
		}
		$i++;
	}
	/* if($emailmsg != '') {
		$message='';
		global $cli_id;
		if($cli_id > 0) {
			$message='O cliente Nº '.$cli_id.' realizou uma '.$emailmsg;
		} else {
			$message='Ocorreu uma '.$emailmsg;
		}
		send_message_admin('Tentativa de Fraude',$message,6);
	} */
	if($valid_prods > 0) {
		$shop=array_combine(range(1,count($shop)),array_values($shop));
		$_SESSION['ss_shop']=$shop;
		$_SESSION['ss_n_enc']=$valid_prods;
	} else {
		unset($_SESSION['ss_shop']);
		unset($_SESSION['ss_n_enc']);
		unset($_SESSION['CLIENTE_ID_TEMP']);
		$shop=array();
		$n_enc=0;
		$_SESSION['ss_shop']=$shop;
		$_SESSION['ss_n_enc']=$n_enc;
	}
}
function addKuantoKustaTracking($id_enc)
{
	global $iva_default;
	$enc_info=get_purchase_info($id_enc);
	$cabecalho=$enc_info['enc_cabecalho'];
	$total_enc=$cabecalho['enc_Total'];
	$total_portes=$cabecalho['enc_Portes'];
	$total_enc_sportes=$cabecalho['enc_TotalDocIva'];
	$tax=$total_enc_sportes / (1 + ($iva_default / 100));
	$tax=$total_enc_sportes - $tax;
	$str='<script type="text/javascript" charset="utf-8">
	$(document).ready(function(){
		__trackk(\'ecommerce:addTransaction\',
		{
			\'id\': \''.$id_enc.'\',				// ID da encomenda. Obrigatório.
			\'revenue\': \''.round($total_enc,2).'\',		// Total da factura,incluindo portes e IVA. Obrigatório.
			\'shipping\': \''.round($total_portes,2).'\',	// Custo de portes. Opcional.
			\'tax\': \''.round($tax,2).'\',				// IVA. Opcional.
			\'currency\': \''.MOEDA_ISO.'\'		// Moeda. Obrigatório.
		});
		__trackk(\'ecommerce:send\');
	});
	</script>';
	return $str;
}
function RemLinhaEnc($n_linha)
{
	global $n_enc,$shop;
	for ($i=$n_linha; $i <= $n_enc; $i++) //percorrer todas as linhas ate ao fim das encomendas
	{
		for ($col=1; $col <= 9; $col++) //percorrer as colunas da matriz encomendas
		{
			$shop[$i][$col]=$shop[$i + 1][$col]; //puxar os valores para tras uma posição
		}
	}
	$n_enc--;
}
/*if($GLOBALS['module_voucher'] == 1)
{*/
	function RemLinhaVoucher($n_linha)
	{
		global $n_voucher,$voucher;
		for ($i=$n_linha; $i <= $n_voucher; $i++) //percorrer todas as linhas ate ao fim das encomendas
		{
			for ($col=1; $col <= 9; $col++) //percorrer as colunas da matriz encomendas
			{
				$voucher[$i][$col]=$voucher[$i + 1][$col]; //puxar os valores para tras uma posição
			}
		}
		$n_voucher--;
	}
/*}*/
function get_prod_custo_logistico($prod_id=0)
{
	$v=0;
	$info=get_produtc_info($prod_id);
	$custos_logisticos=portes_extra_prod($info['prod_id'],$info['prod_precos']['PVP']);
	for($a=0; $a<count($custos_logisticos); $a++)
	{
		$v+=$custos_logisticos[$a]['valor'];
	}
	return $v;
}

function update_encomendas_paga($id_enc=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$log2,$site_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs2=new Dataobj;
	$rs3=new Dataobj;

	$sql="SELECT * FROM reserva_stocks WHERE ID_PAI=".$id_enc." AND Qtd > 0";
	$rs3->db_open($db_link,$DB_name,$sql);
	if(!$rs3->EOF())
	{

		// get individual products
		$sql="SELECT * FROM encomendas_produtos WHERE ID_PAI='".$id_enc."'";
		$rs->db_open($db_link,$DB_name,$sql);
		while(!$rs->EOF()) // Existe o codigo de desconto disponivel
		{
			$ID_Produto=$rs->db_data["ID_Produto"];
			$Qtd=$rs->db_data["Qtd"];

			// check if it is a MHR-stock product (sync=1)
			$sql="SELECT ID FROM products WHERE ID='$ID_Produto' AND Sync=1";
			$rs2->db_open($db_link,$DB_name,$sql);
			if(!$rs2->EOF())
			{
				// update stocks for each one
				$sql="UPDATE products_objs SET obj_stock=obj_stock-'$Qtd' WHERE obj_type='products' AND obj_type_id='$ID_Produto' AND obj_type_id > 0 LIMIT 1";
				// var_dump($sql); die();
				$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

			}
			$rs->db_next();
		}
		$rs->db_close();

		// reset stock reserve for the whole order
		// step 1
		$sql='UPDATE reserva_stocks set Qtd2 = Qtd WHERE ID_PAI='.$id_enc;//Guarda copia do antes
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);
		// step 2
		$sql='UPDATE reserva_stocks set Qtd = 0 WHERE ID_PAI='.$id_enc;
		$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

		//Atualiza a cache dos artigos
		get_produtc_info($ID_Produto,'',1,0,$site_lang,1);

	}

	// die('nova update_encomendas_paga: '.$id_enc);
}
function update_encomendas_nao_paga($id_enc=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$log2;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$sql='UPDATE reserva_stocks set Qtd2 = Qtd WHERE ID_PAI='.$id_enc;//Guarda copia do antes
	$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

	$sql='UPDATE reserva_stocks set Qtd = 0 WHERE ID_PAI='.$id_enc;//Guarda copia do antes
	$res=Runquery($db_link,$GLOBALS['DB_name'],$sql);

}

function UpdDataNascimento2($id_enc)
{

    if($_GET['floa_dtnasc']!= '' ){
        $data_nascimento = DateTime::createFromFormat('d/m/Y', $_GET['floa_dtnasc'])->format('Y-m-d');
    }

    if($_POST['floa_dtnasc']!= '' ){
        $data_nascimento = DateTime::createFromFormat('d/m/Y', $_POST['floa_dtnasc'])->format('Y-m-d');
    }

    if ($data_nascimento == '' || $data_nascimento == '0000-00-00') {
        return;
    }

    //$data_nascimento = DateTime::createFromFormat('d/m/Y', $data_nascimento)->format('Y-m-d');

    $db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
    $sql = "UPDATE encomendas SET enc_DataNascimento='$data_nascimento' WHERE ID='$id_enc'";
    $res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

}
