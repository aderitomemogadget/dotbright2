<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function show_prod_points($prodid=0,$prods2=array())
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new Dataobj;
	$rs_org=new Dataobj;
	$sql='
			SELECT
				P.ID,P.points,PF.points as fpoints,BR.points AS bpoints
			FROM
				products AS P
				INNER JOIN products_lang AS PL
				INNER JOIN products_families AS PF
				INNER JOIN products_families_lang AS PFL
				INNER JOIN brands AS BR ON P.ID_Marca=BR.ID
				INNER JOIN products_objs AS PO ON P.ID=PO.obj_type_id
				AND P.ID_ORG=PF.ID
				AND PL.ID_PAI=P.ID
				AND PFL.ID_PAI=PF.ID
			AND
				P.Aproved=1
				AND P.ID=' . $prodid . '
				AND PL.Lang="' . $GLOBALS['site_lang'] . '"
			GROUP BY
				P.ID

		';
		// echo $sql;
	$rs->db_open($db_link,$GLOBALS['DB_name'],$sql);
	if (!$rs->EOF())
	{
		while (!$rs->EOF())
		{
			$prods['prod_id']=$rs->db_data['ID'];
			$prods['points']=rtrim(rtrim($rs->db_data['points'],'€'),'%');
			$prods['points_raw']=$rs->db_data['points'];
			$prods['fpoints']=rtrim(rtrim($rs->db_data['fpoints'],'€'),'%');
			$prods['fpoints_raw']=$rs->db_data['fpoints'];
			$prods['bpoints']=rtrim(rtrim($rs->db_data['bpoints'],'€'),'%');
			$prods['bpoints_raw']=$rs->db_data['bpoints'];

			$rs->db_next();
		}
	}
	$rs->db_close();

	$points=0;
	$points_raw=0;
	// echo $prods['points'];
	if ($prods['points'] > 0){ $points=$prods['points'];  $points_raw=$prods['points']; }
	elseif ($prods['bpoints'] > 0){ $points=$prods['bpoints'];  $points_raw=$prods['bpoints_raw'];  }
	else
	{
		// NEW QUERY
		$sql_org='SELECT PF.ID_ORG FROM products_families PF,products P WHERE P.ID=' . $prods['prod_id'] . ' and P.ID_ORG=PF.ID';
		$rs_org->db_open($db_link,$GLOBALS['DB_name'],$sql_org);
		if (!$rs_org->EOF())
		{
			$rs_org2=new DataObj;
			$sql_org2='SELECT points FROM products_families WHERE ID=' . $rs_org->db_data['ID_ORG'] . '';
			$rs_org2->db_open($db_link,$GLOBALS['DB_name'],$sql_org2);
			if (!$rs_org2->EOF()){ $points= rtrim(rtrim($rs_org2->db_data['points'],'€'),'%'); $points_raw= $rs_org2->db_data['points']; }
			$rs_org2->db_close();
		}
		$rs_org->db_close();
	}
	if ($points == 0) $points=$prods['fpoints'];

	$prods=$prods2;

	// POINT FIX
	if (strpos($points_raw,"%") != false)
	{
		$points=floatval($points);
		$points=floatval($prods2['PVP']) * ($points / 100);
	}
	else
	{
		if (strpos($points_raw,"€") != false)$points=floatval($points);
	}
	return $points;
}
function show_points($userid=0,$format='no')
{
	if(isset($GLOBALS['cli_id']) && ($userid==0 || empty($userid) || !$userid)) $userid=$GLOBALS['cli_id'];
	$pontos=0;
	if($userid > 0)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new Dataobj;
		$yesterdate=strtotime('-1 day',time()); //24 horas
		$yestermonth=strtotime('-3 month',time()); //90 dias
		$sql='
				SELECT
					cb.*,e.Estado
				FROM
					clientes_bonus as cb
					LEFT JOIN encomendas as e ON e.ID=cb.ID_Encomenda
				WHERE
					cb.ID_cliente='.$userid;
		$rs->db_open($db_link,$DB_name,$sql);
		$pontos=0; $usados_total=0;
		while(!$rs->EOF())
		{
			$tipo=$rs->db_data["Tipo"];
			$valor=$rs->db_data["Valor"];
			$estado=$rs->db_data["Estado"];
			$data=strtotime($rs->db_data["Data"]);

			switch ($tipo)
			{
				case 0:
					//Bonus adicionado por voucher
					$pontos += $valor;
					break;
				case 1:
					//Bonus adicionado por encomenda (produtos)
					if($data <= $yesterdate && $data > $yestermonth && $estado != 0) $pontos += $valor;
					break;
				case 2:
					//Bonus adicionado pelo admin
					if( $data>$yestermonth ) $pontos += $valor;
					break;
				case 3:
					//Bonus utilizado pelo cliente numa encomenda
					$usados_total += $valor;
					break;
				case 4:
					//bonus removido e atribuido ao cliente em dinheiro
					$usados_total += $valor;
					break;
			}
			$rs->db_next();
		}
		$rs->db_close();

		$pontos=$pontos - $usados_total;
		if ($pontos < 0) $pontos=0;
	}
	if($format == 'yes') return numberformat($pontos);
	else return $pontos;
}