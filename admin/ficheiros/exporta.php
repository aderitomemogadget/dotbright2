<?php 
	include "settings.inc.php";
	header ('Content-type: text/html; charset=UTF-8'); 
	header('Content-type: application/csv');
	header("Content-Disposition: attachment; filename=produtos".date('YmdHis').".csv");
	header('Expires: 0');
	header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
	header('Pragma: public');
		
	function cat_niveis($id_org=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);	
		$rs=new DataObj;
		$pai=$id_org;
		while($pai > 0)
		{
			$sql = 'select pf.ID, pf.ID_ORG, pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and Download=1 and pfl.Lang="'.$GLOBALS['admin_lang'].'" and pf.ID='.$pai;
			$rs->db_open($db_link,$DB_name,$sql);
			$pai=$rs->db_data["ID_ORG"];
			$C[] = $rs->db_data["Cabecalho"]=='' ? 'SEM FAMILIA' : $rs->db_data["Cabecalho"];
		}		
		return array_reverse($C);
	}
	
	function products_objs_ex($obj_id=0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$m_resumo,$from;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs2 = new DataObj;
		$sql = 'SELECT Linha_preco, Revendedor FROM grupos_clientes';
		$rs2->db_open($db_link,$DB_name,$sql);
		while(!$rs2->EOF())
		{
			$gps_linha[] = $rs2->db_data['Linha_preco'];
			$gps_tipo[$rs2->db_data['Linha_preco']] = $rs2->db_data['Revendedor'];
			
			$rs2->db_next();
		}
		$rs2->db_close();
		
		$rs=new DataObj;
		$sql = "SELECT * FROM products_objs inner join products_objs_lang on products_objs_lang.ID_PAI = products_objs.obj_id WHERE obj_type='products' AND obj_type_id='".$obj_id."' and Lang='".$GLOBALS['admin_lang']."' ORDER BY products_objs_lang.Cabecalho ASC";
		$rs->db_open($db_link,$DB_name,$sql);
		$Preco = 0;	$Preco_Promo = 0;$P_Revenda = 0;$P_Revenda_Promo = 0;
		while(!$rs->EOF())
		{		
			
			if(in_array($rs->db_data['Cabecalho'], $gps_linha) && $gps_tipo[$rs->db_data['Cabecalho']]==0) // NORMAL
			{ 
				// echo 'enctrou<br>';
				$Preco = str_replace('.',',',$rs->db_data['obj_pvp']);
				if($rs->db_data['obj_pvp']!=$rs->db_data['obj_promo'] && $rs->db_data['obj_promo'] > 0 && $rs->db_data['obj_promo_ativo']==1) $Preco_Promo = str_replace('.',',',$rs->db_data['obj_promo']);
				else $Preco_Promo = 0;
			}
			elseif(in_array($rs->db_data['Cabecalho'], $gps_linha) && $gps_tipo[$rs->db_data['Cabecalho']]==1) // REVENDA
			{
				// echo 'enctrou2<br>';
				$P_Revenda = str_replace('.',',',$rs->db_data['obj_pvp']);
				if($rs->db_data['obj_pvp']!=$rs->db_data['obj_promo'] && $rs->db_data['obj_promo'] > 0 && $rs->db_data['obj_promo_ativo']==1) $P_Revenda_Promo = str_replace('.',',',$rs->db_data['obj_promo']);
				else $P_Revenda_Promo = 0;
			}
			$rs->db_next();
		}
		$rs->db_close();
		
		return array('Preco' => $Preco,'Preco_Promo' => $Preco_Promo, 'P_Revenda' => $P_Revenda, 'P_Revenda_Promo' => $P_Revenda_Promo);
	}
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;

	echo '"Familia";';
	echo '"Marca";';
	echo '"Referência";';
	echo '"Nome do produto";';
	echo '"PVP";';
	echo '"PVP PROMO";';
	echo '"PVP REVENDA";';
	echo '"PVP REVENDA PROMO";';
	echo '"QTD";';
	echo '"ATIVO";';
	echo "\n";

	$count =0;
	$sql1 = '
			SELECT 
				P.ID,P.Qtd,P.ID_Marca, P.ID_ORG, P.Ref, P.Aproved, PL.Cabecalho
			FROM products P
			INNER JOIN products_lang PL ON PL.ID_PAI = P.ID 
			GROUP BY 
				P.ID
			ORDER BY
				P.Ref
			';
	$rs->db_open($db_link,$DB_name,$sql1);
	while(!$rs->EOF())
	{
		$ID = $rs->db_data["ID"];
		$C = str_replace('"','“',stripslashes($rs->db_data["Cabecalho"]));		
		$Ref = $rs->db_data["Ref"];
		$ID_Marca = $rs->db_data["ID_Marca"];
		$ID_ORG = $rs->db_data["ID_ORG"];
		$aproved = $rs->db_data["Aproved"];
		$qtd = $rs->db_data["Qtd"];
		$fams_n = cat_niveis($ID_ORG);
		$fms_nome = '';	for($a=0;$a<count($fams_n);$a++){if($a>0 && $fams_n[$a]){$fms_nome.=',';}$fms_nome.=str_replace('"','“',stripslashes($fams_n[$a]));}
		
		$linhas_precos = products_objs_ex($ID);		
		$marca='';

		$rs3->db_open($db_link,$DB_name,'SELECT * FROM brands_lang where ID_PAI='.$ID_Marca);
		$marca = $rs3->db_data["Nome"];
		$rs3->db_close();

		echo '"'.$fms_nome.'";';
		echo '"'.$marca.'";';
		echo '"'.$Ref.'";';
		echo '"'.$C.'";';	
		echo '"'.$linhas_precos['Preco'].'";';
		echo '"'.$linhas_precos['Preco_Promo'].'";';
		echo '"'.$linhas_precos['P_Revenda'].'";';
		echo '"'.$linhas_precos['P_Revenda_Promo'].'";';		
		echo '"'.$qtd.'";';

		if($aproved) echo '"SIM";'; else echo '"NÃO";';
		
		echo "\n";
		
		$rs->db_next();
		$count++;
	}
	$rs->db_close();
?>