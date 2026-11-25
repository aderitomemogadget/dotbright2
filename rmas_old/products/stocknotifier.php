<?php
	require_once("settings.inc.php");

	if(isset($_POST['idprod']) && $_POST['idprod']>0){
		$id_prod=$_POST['idprod'];
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;$rs2=new DataObj;
		$sql="SELECT * FROM clientes_notificacoes WHERE ID_PROD='".$id_prod."'";
		$rs->db_open($db_link, $DB_name, $sql);
		if(!$rs->EOF()){
			$numemailsenviados=$numemailserro=0;
			$mailsubject='Alerta de Stock';
			$sql2='SELECT 
					PL.Cabecalho
				FROM 
					products AS P
					INNER JOIN products_lang AS PL
					ON PL.ID_PAI=P.ID
				WHERE 
					P.ID="'.$id_prod.'" AND
					PL.Lang="'.$GLOBALS['admin_lang'].'"';
			$rs2->db_open($db_link, $DB_name, $sql2);
			if(!$rs2->EOF()){
				$prod_nome=$rs2->db_data['Cabecalho'];
				$url=seo('pages.php?page_id='.$GLOBALS['products_page_id'], $id_prod, 2);

				while(!$rs->EOF()){
					$emailcli=$rs->db_data['Email'];

					$message='Estimado cliente, <br /><br /> Informamos que o produto '.$prod_nome.' já se encontra disponível para compra.<br /><br />Poderá consultá-lo através do seguinte link: <a href="'.$url.'" target="_blank">'.$url.'</a>.';
				
					if(send_email($emailcli,$mailsubject,$message)){
						$numemailsenviados++;
						$sql="DELETE FROM clientes_notificacoes WHERE ID_PROD='".$id_prod."' AND Email='".$emailcli."'";
						$res=Runquery($db_link,$DB_name,$sql);
					}else{
						$numemailserro++;
					}

					$rs->db_next();
				}
			}
			$rs2->db_close();
			
			echo "Email enviados: ".$numemailsenviados."\n\nErro a enviar email: ".$numemailserro;
		}else{
			echo 'Não existem notificações associadas a este produto';
		}
		$rs->db_close();
	}else{
		echo 'Erro a receber o ID do produto';
	}
/*
if(send_email($mailto,$mailsubject,$message)){


}*/

?>