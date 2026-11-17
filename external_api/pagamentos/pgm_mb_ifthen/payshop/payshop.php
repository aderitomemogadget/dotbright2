<?php
	$path =  dirname( __FILE__ ) . '/../../../../core/';
	include $path.'core_files.php';
	$id_enc=intval($_GET['id_enc']);
	$GLOBALS['PAYSHOP_KEY'] = 'XSE-077653';

	function sendPayshopRequest($payshopkey='',$id_enc='',$valor='')
	{
		$rs = new DataObj;
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$sql="INSERT INTO ifthen_payshop_logs (ID_Encomenda,Data,Estado,Erros) VALUES('".$id_enc."','".date("Y-m-d H:m:i")."','0','')";
		$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
		$ID_Log=db_get_last_insert_id($db_link);

		if (!is_null($payshopkey) && !is_null($id_enc) && !is_null($valor) )
		{
			$url='https://ifthenpay.com/api/payshop/get?payshopkey='.$payshopkey.'&id='.$id_enc.'&valor='.$valor.'&validade=';

			$response=file_get_contents($url);
			$sql="UPDATE ifthen_payshop_logs set Erros='".addslashes($response)."' WHERE ID = '$ID_Log' ";
			$rs=Runquery($db_link, $GLOBALS['DB_name'], $sql);

		    return $response;

        }
        else return 'Informação incompleta para poder gerar a referencia payshop!';

	}

	if ($id_enc > 0)
	{
		$db_link = db_opendb_conection($GLOBALS['DB_host'], $GLOBALS['DB_user'], $GLOBALS['DB_pass']);
		$rs = new DataObj;

		$sql_all = "SELECT TotalDocComIva,enc_Email FROM encomendas WHERE encomendas.ID='" . $id_enc . "'";
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql_all);
		if (!$rs->EOF())
		{
			$valor=$rs->db_data['TotalDocComIva'];
		}
		$rs->db_close();
		$valor=number_format($valor,2,'.','');

		$refgen = sendPayshopRequest($GLOBALS['PAYSHOP_KEY'],$id_enc,$valor);
		$refgen2=json_decode($refgen,true);


		if($refgen!='')
		{
			$msg_tmp.= '<table width="350" border=0 cellspacing=0 cellpadding=0 style="border:1px solid grey;margin:0 auto;border-collapse:inherit;border-spacing:10px;">';
			$msg_tmp.= '<tr><td align="center"><img style="width:150px" src="' .SITE_HTTP_URL . $GLOBALS['pgm_metodos'][MP_TP_PAYSHOP_IFTHEN]['url']. 'payshop.png" border="0" /></td></tr>';
			$msg_tmp.= "<tr><td align='center' style='font-weight:bold;'>&euro;&nbsp;" . number_format($valor , 2, ',', ' ').'</td></tr>';
			$msg_tmp.= "<tr><td align='center' style='font-weight:bold'>".$refgen2['Reference'].'</td></tr>';
			$msg_tmp.= '<tr><td align="center" style="font-size:13px">Para pagamento em agentes Payshop.</td></tr>';
			$msg_tmp.= '</table>';

			$sql = 'UPDATE encomendas SET enc_pagamento_dados="'.addslashes($msg_tmp).'" WHERE ID = '.$id_enc;
			Runquery($db_link, $GLOBALS['DB_name'], $sql);

		}else{
			$msg_tmp.='Não foi possivel gerar referência.';
		}
		echo $msg_tmp;

	}
?>