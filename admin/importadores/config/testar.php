<?php
$Supplier_ID=1;
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////// ATENÇÃO, NOVO SCRIPT, FAZER BACKUP ANTES DE MODIFICAR ///////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////
///////////////////////////////////////////////////////////////////////////////////////////////////////////////

// $id_revendedor é configurado no conf.php


	$path = dirname(__FILE__).'/../../../core/';
	//Incluir apenas as librarias necessarias
	require_once $path.'../conf/conf.php';
	require_once $path.'seguranca.php';
	require_once $path.'db_engine.php';
	require_once $path.'sessions_engine.php';
	require_once $path.'global.php';
	require_once $path.'admin_engine.php';
	require_once $path.'seo.php';
	require_once $path.'gd.php';
	require_once $path.'email.php';
	require_once $path.'produtos.php';
	require_once $path.'filtros.php';
	require_once $path.'return_filtros.php';

	//incluir as funcoes dos importadores
	require_once('settings_crons.inc.php');
	require_once('funcoes.inc.php');

	$path2 = dirname( __FILE__ ).'/../../../';
	include $path2.'admin/products/filtros/funcoes.inc.php';




update_products_precos2(121432,43.47,0,3,'','',0,0,10,0,'23',0);

function Runquery2($a,$b,$sql)
{
	echo $sql."<br>";
}
function update_products_precos2($ID_PROD=0,$obj_pvp=0,$obj_qtdmin=0,$obj_stock=0,$obj_ref='',$obj_ean='',$tipo_pvp=0,$com_stock=0,$Supplier_ID=0,$price_conf=0,$obj_iva='23',$pc=0)
{
	if($ID_PROD<=0)return;


	$com_stock=1;//Nao sei porque tinha esta variavel

	$obj_stock=intval($obj_stock);
	$Supplier_ID=intval($Supplier_ID);

	global $prod_objs_history_dados,$stock_minimo_nulo;

	$stock_minimo=0;
	if(isset($stock_minimo_nulo))$stock_minimo=$stock_minimo_nulo;

	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$db_link=db_opendb_conection($GLOBALS['DB_host'],$GLOBALS['DB_user'],$GLOBALS['DB_pass']);


	//Vamos atualizar a data do artigo
	$udtime =  date("Y-m-d H:i:s");
	$sql = 'UPDATE products SET udtime="'.$udtime.'" WHERE ID="'.$ID_PROD.'"';
	$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);


	$sql='SELECT obj_id,obj_Supplier_ID,obj_pvp,obj_stock FROM products_objs WHERE obj_type_id="'.$ID_PROD.'" ORDER BY obj_id DESC';
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if(!$rs->EOF())
	{
		$obj_id=$rs->db_data['obj_id'];
		$obj_Supplier_ID=$rs->db_data['obj_Supplier_ID'];
		$obj_pvp_atual=$rs->db_data['obj_pvp'];
		$obj_stock_atual=$rs->db_data['obj_stock'];

		//ler a data mais recente em que o preco foi atualizado ou inserido
		$data_i = '';
		$data_u = '';

		$sql='SELECT ID,pvp,Stock,data_u FROM products_objs_history WHERE ID_ORG="'.$obj_id.'" ORDER BY data_u DESC,ID DESC limit 0,1';
		$rs3->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs3->EOF())
		{
			$data_i = $rs3->db_data['data_i'];
			$data_u = $rs3->db_data['data_u'];
		}
		$rs3->db_close();


		//*************************************************************
		$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,pvp,iva,Stock,Ref_loja,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_pvp.'","'.$obj_iva.'","'.$obj_stock.'","'.$obj_ref.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","2","'.$pc.'")';
		$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
		if(!$res)echo db_error($db_link);
		//1 - Vamos verificar se este artigo ja tem preços deste fornecedor
		/*$sql='SELECT ID,pvp,Stock,data_u FROM products_objs_history WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'" ORDER BY ID DESC limit 0,1';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			//Encontrou preços, vamos comparar os pvp e os stocks
			$IDb 	= $rs2->db_data['ID'];
			$pvp2 	= $rs2->db_data['pvp'];
			$Stock2 = $rs2->db_data['Stock'];


			if((round(floatval($pvp2),2) == round(floatval($obj_pvp),2)) && (intval($Stock2) == intval($obj_stock)))
			{
				//Os valores de preço e stock são iguais, então altera apenas a data do update
				echo "@";
				$sql='UPDATE products_objs_history set data_u = "'.date('Y-m-d H:i:s').'" WHERE ID = '.$IDb;
				$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
				if(!$res)echo db_error($db_link);
			}
			else
			{
				//Os valores ou o stock é diferente, por isso insere
				$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,pvp,iva,Stock,Ref_loja,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_pvp.'","'.$obj_iva.'","'.$obj_stock.'","'.$obj_ref.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","1","'.$pc.'")';
				$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
				if(!$res)echo db_error($db_link);
			}
		}
		else
		{
			$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,pvp,iva,Stock,Ref_loja,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_pvp.'","'.$obj_iva.'","'.$obj_stock.'","'.$obj_ref.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","2","'.$pc.'")';
			$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
			if(!$res)echo db_error($db_link);
		}
		$rs2->db_close();*/
		//*************************************************************

		//$Supplier_ID é o id do fornecedor que esta a ser importado
		//$obj_pvp é o preco que vem do fornecedor que esta a ser importado
		//$obj_stock é o stock que vem do fornecedor que esta a ser importado
		//
		//obj_Supplier_ID é o id do fornecedor que esta associado ao artigo neste momento
		//
		$fields='';
		if($com_stock == 1) $fields .= ', obj_stock="'.$obj_stock.'"';

		if($obj_pvp > 0 && $obj_stock > $stock_minimo)
		{
			if($Supplier_ID == $obj_Supplier_ID)
			{
				//é o mesmo fornecedor será para manter?
				$sql='1 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
				$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
				//$sql='UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang="pt"';
				//$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
			}
			else
			{
				$nao_atualizou_preco=1;
				//é outro fornecedor, será para trocar?
				if($price_conf==0)
				{
					//Se tiver um valor inferior
					if($obj_pvp < $obj_pvp_atual || ($obj_pvp == $obj_pvp_atual && ($obj_stock > $obj_stock_atual)) || ($obj_stock_atual<=$stock_minimo) )
					{
						$nao_atualizou_preco=0;
						//é para trocar
						$sql='2 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
						//$sql='UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang="pt"';
						//$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					}
				}
				else
				{
					//Se tiver um stock superior
					if($obj_stock > $obj_stock_atual)
					{
						$nao_atualizou_preco=0;
						//é para trocar
						$sql='3 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
						//$sql='UPDATE products_objs_lang SET Cabecalho="'.$obj_ref.'" WHERE ID_PAI="'.$obj_id.'" AND Lang="pt"';
						//$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					}
				}

				if($nao_atualizou_preco)
				{
					$sql='SELECT ID,ID_ORG,pvp,Stock FROM products_objs_history WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID <> "'.$Supplier_ID.'" AND (data_i Like "%'.date('Y-m-d').'%" OR data_u Like "%'.date('Y-m-d').'%")';
					$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
					if(!$rs2->EOF())
					{
						//Ja foi atualizado hoje entao nao faz nada
					}
					else
					{
						//é para colocar este novo supplier
						$sql='5 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					}

				}
			}
		}
		else
		{
			//veio sem preço ou sem stock
			if($Supplier_ID == $obj_Supplier_ID)
			{
				//Se era este fornecedor que estava ativo tenho de ir buscar outro
				if($price_conf==0)$object_preco=get_lowest_price($obj_id,$obj_Supplier_ID); //menor preco
				else $object_preco=get_higher_stock($obj_id,$obj_Supplier_ID); //mais stock

				//Se receber >0 é porque é para trocar de fornecedor
				if($object_preco['pvp']>0)
				{
					$obj_pvp 			= $object_preco['pvp'];	//novo pvp
					$obj_stock 			= $object_preco['Stock']; //novo stock
					$novo_obj_Supplier_ID 	= $object_preco['Supplier_ID']; //Novo fornecedor

					$fields='';
					if($com_stock == 1) $fields .= ', obj_stock="'.$obj_stock.'"';

					//Só se o melhor preço for o deste fornecedor entao atualiza os valores
					if($obj_pvp>0 && $obj_stock>$stock_minimo)
					{
						$sql='7 UPDATE products_objs SET obj_Supplier_ID="'.$novo_obj_Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					}
					else
					{
						//infelizmente o recomendado tb não tem preço ou stock, vamos atualizar este artigo e colocar sem stock
						$sql='8 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						if($obj_pvp>0)$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
						//se por algum motivo o preço vier a 0 nao posso colocar a 0
					}
				}
				else
				{
					//infelizmente o produto deixou de ter preço ou stock e não há mais vendedores com stock
					$sql='9 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
					if($obj_pvp>0)$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					//se por algum motivo o preço vier a 0 nao posso colocar a 0
				}
			}
			else
			{
				if($obj_Supplier_ID=='')
				{
					$sql='10 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
					$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
				}
				else
				{
					//Ainda nao foi atualizado hoje
					echo $data_i;
					echo " -- ";
					echo $data_u;
					if(substr_count($data_i,date("Y-m-d")) <= 0 && substr_count($data_u,date("Y-m-d")) <= 0 )
					{
						$sql='11 UPDATE products_objs SET obj_Supplier_ID="'.$Supplier_ID.'",obj_pvp="'.$obj_pvp.'",obj_stock="'.$obj_stock.'" WHERE obj_type_id="'.$ID_PROD.'"';
						$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
					}
				}
			}
		}
	}
	else
	{
		$fields_c='';
		$fields_v='';
		if($com_stock == 1)
		{
			$fields_c .= ', obj_stock';
			$fields_v .= ', "'.$obj_stock.'"';
		}
		$sql='INSERT INTO products_objs (obj_Supplier_ID,obj_type_id,obj_ativo,obj_pvp,obj_qtdmin,obj_type,obj_val2'.$fields_c.') VALUES ("'.$Supplier_ID.'","'.$ID_PROD.'",1,"'.$obj_pvp.'","'.$obj_qtdmin.'","products","'.$tipo_pvp.'"'.$fields_v.')';
		$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
		$obj_id=db_get_last_insert_id($db_link);
		$sql='INSERT INTO products_objs_lang(Cabecalho,ID_PAI,Lang) VALUES ("'.$obj_ref.'","'.$obj_id.'","pt")';
		$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
		//*************************************************************
		$sql='SELECT pvp FROM products_objs_history WHERE ID_ORG="'.$obj_id.'" AND Supplier_ID="'.$Supplier_ID.'" ORDER BY ID DESC';
		$rs2->db_open($db_link,$GLOBALS['DB_name'],$sql);
		if(!$rs2->EOF())
		{
			//Encontrou, vamos comparar os pvp
			$pvp2= $rs2->db_data['pvp'];
			if($pvp2 != $obj_pvp)
			{
				$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","3","'.$pc.'")';
				$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
			}
		}
		else
		{
			$sql='INSERT INTO products_objs_history(ID_ORG,Supplier_ID,iva,Stock,data_i,data_u,Flag,pc) values("'.$obj_id.'","'.$Supplier_ID.'","'.$obj_iva.'","'.$obj_stock.'","'.date('Y-m-d H:i:s').'","'.date('Y-m-d H:i:s').'","4","'.$pc.'")';
			$res=Runquery2($db_link,$GLOBALS['DB_name'],$sql);
		}
		$rs2->db_close();
		//*************************************************************
	}
	$rs->db_close();
}
?>