<?php

	$path='../../core/';
	require_once $path.'core_files.php';

class Lusopay{

	private $chave, $entidade, $nif;

	/**
	 * Contruct da classe
	 * @param string  $ent_chave Chave fornecida pela Memopay
	 * @param integer $ent_entidade   Entidade do cliente
	 * @param integer $ent_nif  NIF do cliente
	 */
	public function __construct($chave, $entidade, $ent_nif2){
		$this->chave = $chave;
		$this->entidade = $entidade;
		$this->nif = $nif;
	}

	/**
	 * Retorna o numero no formato pedido pela LusoPay
	 * @param  string $number Numero a ser transformado
	 * @return double         Numero formatado
	 */
	private function format_number($number)
	{
		$verifySepDecimal = number_format(99,2);
		$valorTmp = $number;
		$sepDecimal = substr($verifySepDecimal, 2, 1);
		$hasSepDecimal = True;
		$i=(strlen($valorTmp)-1);
		for($i;$i!=0;$i-=1)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==",")
			{
				$hasSepDecimal = True;
				$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
				break;
			}
		}
		if($hasSepDecimal!=True)
		{
			$valorTmp=number_format($valorTmp,2);
			$i=(strlen($valorTmp)-1);
			for($i;$i!=1;$i--)
			{
				if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)==",")
				{
					$hasSepDecimal = True;
					$valorTmp = trim(substr($valorTmp,0,$i))."@".trim(substr($valorTmp,1+$i));
					break;
				}
			}
		}

		for($i=1;$i!=(strlen($valorTmp)-1);$i++)
		{
			if(substr($valorTmp,$i,1)=="." || substr($valorTmp,$i,1)=="," || substr($valorTmp,$i,1)==" ")
			{
				$valorTmp = trim(substr($valorTmp,0,$i)).trim(substr($valorTmp,1+$i));
				break;
			}
		}

		if (strlen(strstr($valorTmp,'@'))>0)
		{
			$valorTmp = trim(substr($valorTmp,0,strpos($valorTmp,'@'))).trim($sepDecimal).trim(substr($valorTmp,strpos($valorTmp,'@')+1));
		}

		return $valorTmp;
	}

	/**
	 * Faz o pedido e retorna a referencia MB e Payshop
	 * @param string  $order_id    ID da encomenda que vai como ref à LusoPay
	 * @param double  $order_value Valor da encomenda
	 * @param integer $sanbox      0 - Ambiente Produção | 1 - Ambiente Testes
	 */
	public function GenerateRef($order_id, $order_value, $sanbox = 0)
	{
		$order_value= sprintf("%01.2f", $order_value);
		$order_value =  $this->format_number($order_value);

		//Apenas sao considerados os 4 caracteres mais a direita do order_id
		//$order_id = substr($order_id, (strlen($order_id) - 4), strlen($order_id));

		if ($order_value < 2) {
			echo "Lamentamos mas é impossível gerar uma referência MB para valores inferiores a 2 Euro";
			return;
		}


		$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";
		if($sanbox) $soapUrl = "https://services.lusopay.com/PaymentServices_test/PaymentServices.svc?wsdl";

		$xml_post_string='<?xml version="1.0" encoding="utf-8"?>
		<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
		   <soapenv:Body>
		      <tem:getNewDynamicReference>
		         <tem:clientGuid>'.$this->chave.'</tem:clientGuid>
		         <tem:vatNumber>'.$this->nif.'</tem:vatNumber>
		         <tem:valueList>
		            <!--Zero or more repetitions:-->
		            <pay:References>
		               <pay:amount>'.$order_value.'</pay:amount>
		               <pay:description>'.$order_id.'</pay:description>
		               <pay:serviceType>Both</pay:serviceType>
		            </pay:References>
		         </tem:valueList>
		         <tem:sendEmail>true</tem:sendEmail>
		      </tem:getNewDynamicReference>
		   </soapenv:Body>
		</soapenv:Envelope>';

		//https://services.lusopay.com/PaymentServices/PaymentServices.svc?xsd=xsd0  VER ESTE URL

		$headers = array(
            "Host: services.lusopay.com",
            "Content-type: text/xml;charset=\"utf-8\"",
            "Accept: text/xml",
            "Cache-Control: no-cache",
            "Pragma: no-cache",
            "SOAPAction: http://tempuri.org/IPaymentServices/getNewDynamicReference",
            "Content-length: ".strlen($xml_post_string),
        );

		$url = $soapUrl;
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		$response = curl_exec($ch);
		curl_close($ch);

		$referenceMB = "/<a:referenceMB>(.*?)<\/a:referenceMB>/s";
		$referencePS = "/<a:referencePS>(.*?)<\/a:referencePS>/s";

		if(preg_match($referencePS,$response,$referencePS_value) && preg_match($referenceMB, $response, $referenceMB_value)) {
			$refs[1] = $referencePS_value[1];
			$refs[2] = $referenceMB_value[1];

			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs = new DataObj;

			$rs->Open($db_link, $DB_name, 'SELECT id_order FROM magnimeiosreferences WHERE id_order = '.$order_id);
			if(!$rs->EOF()){
				$sql='UPDATE magnimeiosreferences SET refMB = "'.$refs[2].'", refPS = "'.$refs[1].'", value = "'.$order_value.'" WHERE id_order = "'.$order_id.'"';
			}else $sql='INSERT INTO magnimeiosreferences (id_order, refMB, refPS, value) values ('.$order_id.', "'.$refs[2].'", "'.$refs[1].'", "'.$order_value.'")';
			$rs->db_close();

			$res = Runquery($db_link,$DB_name,$sql);

			$sql = "UPDATE encomendas SET Ent_atm='".$this->entidade."',Ref_atm='".str_replace(' ', '', $refs[2])."' WHERE ID='" . $order_id . "'";
			$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);

		} else {
			$message = "/<a:message>(.*?)<\/a:message>/s";
			if(preg_match($message,$response,$message_value)) echo $message_value[1];
		}
		return true;
	}

	/**
	 * Vai à tabela verificar a magnimeiosreferences
	 * @param  string $order_id id da encomenda
	 * @return string           retorna tabela com as referencias MB e Payshop
	 */
	public function getRef($order_id)
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;

		$rs->db_open($db_link,$DB_name, 'SELECT refMB, refPS, value FROM magnimeiosreferences WHERE id_order = '.$order_id);
		while(!$rs->EOF())
		{
			$refs[2] = $rs->db_data["refMB"];
			$refs[1] = $rs->db_data["refPS"];
			$order_value = $rs->db_data["value"];


			// Referencia MB
			if($refs[2] != -1) {
				$refs[2] = substr($refs[2],0,3).' '.substr($refs[2],3,3).' '.substr($refs[2],6,3);
				$tabelaMulti = '
				<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">
					<tr>
						<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Multibanco (by LUSOPAY)</center></td>
					</tr>
					<tr>
						<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_MB125x80px.png" alt=""/></div></td>
						<td style="font-size: x-small; font-weight:bold; text-align:left">Entidade:</td>
						<td style="font-size: x-small; text-align:left">'.$this->entidade.'</td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">Referência:</td>
						<td style="font-size: x-small; text-align:left">'. $refs[2].'</td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>
						<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
						<td style="font-size: x-small; text-align:left">&nbsp;</td>
					</tr>
					<tr>
						<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O talão emitido pela caixa automática faz prova de pagamento. Conserve-o.</center></td>
					</tr>
				</table>';

			}

			//Referencia Payshop
			if($refs[1] != -1) {
				$refs[1] = substr($refs[1],0,3).' '.substr($refs[1],3,3).' '.substr($refs[1],6,3).' '.substr($refs[1],9,3).' '.substr($refs[1],12,1);

				$tabelaMulti .= '
				<table cellpadding="3" width="400px" cellspacing="0" style="margin-top: 10px;border: 1px solid #dcdcdc" align="center">
					<tr>
						<td style="font-size: x-small; border-top: 0px; border-left: 0px; border-right: 0px; border-bottom: 1px solid #dcdcdc; background-color: #dcdcdc; color: black" colspan="3"><center>Pagamento por Payshop (by LUSOPAY)</center></td>
					</tr>
					<tr>
						<td rowspan="4"><div align="center"><img src="https://www.lusopay.com/App_Files/cms/documents/images/Logo_Lusopay_Payshop125x80px.png" alt=""/></div></td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">Referência:</td>
						<td style="font-size: x-small; text-align:left">'. $refs[1].'</td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">Valor:</td>
						<td style="font-size: x-small; text-align:left">'.number_format($order_value, 2,',', ' ').'&nbsp;&euro;</td>
					</tr>
					<tr>
						<td style="font-size: x-small; font-weight:bold; text-align:left">&nbsp;</td>
						<td style="font-size: x-small; text-align:left">&nbsp;</td>
					</tr>
					<tr>
						<td style="font-size: xx-small;border-top: 1px solid #dcdcdc; border-left: 0px; border-right: 0px; border-bottom: 0px; background-color: #dcdcdc; color: black" colspan="3"><center>O talão emitido faz prova de pagamento. Conserve-o.</center></td>
					</tr>
				</table>';

			}
			$rs->db_next();
		}
		$rs->db_close();

		return $tabelaMulti;
	}


}
?>