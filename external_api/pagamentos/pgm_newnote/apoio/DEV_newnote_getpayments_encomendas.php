<?php

	$path = dirname( __FILE__ ).'/../../../core/';
	include $path.'core_files.php';
	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	
	$datas = array();
	
	$rs_metodo = new DataObj;	
	$sql = "SELECT ID, chavebo, entidade, subentidade FROM metodos_pagamento WHERE Tipo=9";	
	$rs_metodo->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$Sandbox = 0;
	$conta_ifthen = '';
	if (!$rs_metodo->EOF())
	{
		$ID = $rs_metodo->db_data["ID"];
		$chave = $rs_metodo->db_data["chavebo"];
		$subentidade = $rs_metodo->db_data["subentidade"];
	}
	$rs_metodo->db_close();
	
	if(!$chave && !$subentidade) exit;
		
	$rs = new DataObj;	
	$sql = 'SELECT Data_Criacao FROM encomendas WHERE Metodo_pagamento='.$ID.' AND Encomenda_Paga=0';
	$rs->db_open($db_link, $DB_name, $sql);
	while(!$rs->EOF())
	{
		$datas[] = strtotime($rs->db_data['Data_Criacao']);			
		$rs->db_next();
	}
	$rs->db_close();
	
	if(count($datas) > 0)
	{		
		$min_date = str_replace(' ', 'T', date('Y-m-d H:i:s', min($datas)));
		// $max_date = str_replace(' ', 'T', date('Y-m-d H:i:s', max($datas)));
		$max_date = str_replace(' ', 'T', date('Y-m-d H:i:s'));
		
		GETMOVIMENTSBYDATES($chave, $subentidade, $min_date, $max_date);
	}
	
	function get_string_between($string, $start, $end='')
	{
		if($end=='') $end = str_replace('<', '</', $start);
		
		$string = " ".$string;
		$ini = strpos($string,$start);
		if ($ini == 0) return "";
		$ini += strlen($start);
		$len = strpos($string,$end,$ini) - $ini;
		return substr($string,$ini,$len);
	}
	function GETMOVIMENTSBYDATES($ent_chave, $ent_nif2, $data_ini, $data_fim, $sanbox = 0)
	{
		$soapUrl = "https://services.lusopay.com/PaymentServices/PaymentServices.svc?wsdl";
		if($sanbox) $soapUrl = "https://services.lusopay.com/PaymentServices_test/PaymentServices.svc?wsdl";

		$xml_post_string='<?xml version="1.0" encoding="utf-8"?>
		<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:tem="http://tempuri.org/" xmlns:pay="http://schemas.datacontract.org/2004/07/PaymentServices">
		   <soapenv:Body>
		      <tem:getNewDynamicMovementsByDates>
		         <!--Optional:-->
		         <tem:clientGuid>'.$ent_chave.'</tem:clientGuid>
		         <!--Optional:-->
		         <tem:vatNumber>'.$ent_nif2.' </tem:vatNumber>
		         <!--Optional:-->
		         <tem:startDate>'.$data_ini.'</tem:startDate>
				  <!--Optional:-->
		         <tem:endDate>'.$data_fim.'</tem:endDate>
		      </tem:getNewDynamicMovementsByDates>
		   </soapenv:Body>
		</soapenv:Envelope>';

		$headers = array(
		            "Host: services.lusopay.com",
		            "Content-type: text/xml;charset=\"utf-8\"",
		            "Accept: text/xml",
		            "Cache-Control: no-cache",
		            "Pragma: no-cache",
		            "SOAPAction: http://tempuri.org/IPaymentServices/getNewDynamicMovementsByDates",
		            "Content-length: ".strlen($xml_post_string),
		        );
		//SOAPAction: your op URL
		$url = $soapUrl;
		// PHP cURL for http connection with auth
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
		
		// PARA TESTES
		//$response = '<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/"><s:Body><getNewDynamicMovementsByDatesResponse xmlns="http://tempuri.org/"><getNewDynamicMovementsByDatesResult><xs:schema id="NewDataSet" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns="" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata"><xs:element name="NewDataSet" msdata:IsDataSet="true" msdata:UseCurrentLocale="true"><xs:complexType><xs:choice minOccurs="0" maxOccurs="unbounded"><xs:element name="Table"><xs:complexType><xs:sequence><xs:element name="id" type="xs:int" minOccurs="0"/><xs:element name="transactionDateTime" type="xs:dateTime" minOccurs="0"/><xs:element name="clientPaymentDateTime" type="xs:dateTime" minOccurs="0"/><xs:element name="transactionCost" type="xs:decimal" minOccurs="0"/><xs:element name="reference" type="xs:string" minOccurs="0"/><xs:element name="amount" type="xs:decimal" minOccurs="0"/><xs:element name="serviceId" type="xs:int" minOccurs="0"/><xs:element name="description" type="xs:string" minOccurs="0"/></xs:sequence></xs:complexType></xs:element></xs:choice></xs:complexType></xs:element></xs:schema><diffgr:diffgram xmlns:diffgr="urn:schemas-microsoft-com:xml-diffgram-v1" xmlns:msdata="urn:schemas-microsoft-com:xml-msdata"><NewDataSet xmlns=""><Table diffgr:id="Table1" msdata:rowOrder="0"><id>255028</id><transactionDateTime>2015-08-11T17:53:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-13T09:08:09+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626564029</reference><amount>85.98</amount><serviceId>1</serviceId><description>76</description></Table><Table diffgr:id="Table2" msdata:rowOrder="1"><id>253647</id><transactionDateTime>2015-08-11T20:09:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-14T09:17:53+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626483364</reference><amount>22.38</amount><serviceId>1</serviceId><description>74</description></Table><Table diffgr:id="Table3" msdata:rowOrder="2"><id>255653</id><transactionDateTime>2015-08-12T21:36:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-17T09:11:16+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626599807</reference><amount>499.00</amount><serviceId>1</serviceId><description>77</description></Table><Table diffgr:id="Table4" msdata:rowOrder="3"><id>256297</id><transactionDateTime>2015-08-13T17:19:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-17T09:11:16+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626636827</reference><amount>503.49</amount><serviceId>1</serviceId><description>80</description></Table><Table diffgr:id="Table5" msdata:rowOrder="4"><id>257664</id><transactionDateTime>2015-08-17T12:45:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-19T09:05:00+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626716953</reference><amount>31.78</amount><serviceId>1</serviceId><description>82</description></Table><Table diffgr:id="Table6" msdata:rowOrder="5"><id>258707</id><transactionDateTime>2015-08-18T17:52:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-20T09:00:32+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626777779</reference><amount>20.98</amount><serviceId>1</serviceId><description>84</description></Table><Table diffgr:id="Table7" msdata:rowOrder="6"><id>259278</id><transactionDateTime>2015-08-19T16:14:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-21T09:05:59+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626812573</reference><amount>147.49</amount><serviceId>1</serviceId><description>86</description></Table><Table diffgr:id="Table8" msdata:rowOrder="7"><id>261016</id><transactionDateTime>2015-08-22T11:49:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-25T09:17:10+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626916198</reference><amount>19.58</amount><serviceId>1</serviceId><description>88</description></Table><Table diffgr:id="Table9" msdata:rowOrder="8"><id>261278</id><transactionDateTime>2015-08-24T14:36:00+01:00</transactionDateTime><clientPaymentDateTime>2015-08-26T09:15:14+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>626932022</reference><amount>8.09</amount><serviceId>1</serviceId><description>89</description></Table><Table diffgr:id="Table10" msdata:rowOrder="9"><id>269143</id><transactionDateTime>2015-09-02T10:38:00+01:00</transactionDateTime><clientPaymentDateTime>2015-09-04T09:46:56+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>627389258</reference><amount>108.99</amount><serviceId>1</serviceId><description>90</description></Table><Table diffgr:id="Table11" msdata:rowOrder="10"><id>269776</id><transactionDateTime>2015-09-02T18:33:00+01:00</transactionDateTime><clientPaymentDateTime>2015-09-04T09:46:56+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>627428991</reference><amount>586.59</amount><serviceId>1</serviceId><description>91</description></Table><Table diffgr:id="Table12" msdata:rowOrder="11"><id>271152</id><transactionDateTime>2015-09-03T22:36:00+01:00</transactionDateTime><clientPaymentDateTime>2015-09-08T08:48:42+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>627512448</reference><amount>25.49</amount><serviceId>1</serviceId><description>94</description></Table><Table diffgr:id="Table13" msdata:rowOrder="12"><id>277587</id><transactionDateTime>2015-09-14T09:18:00+01:00</transactionDateTime><clientPaymentDateTime>2015-09-16T09:12:16+01:00</clientPaymentDateTime><transactionCost>0.70</transactionCost><reference>627849210</reference><amount>90.49</amount><serviceId>1</serviceId><description>96</description></Table></NewDataSet></diffgr:diffgram></getNewDynamicMovementsByDatesResult></getNewDynamicMovementsByDatesResponse></s:Body></s:Envelope>';
		
		preg_match_all('/<table.*?>(.*?)<\/table>/si', $response, $matches); 
		
		if(isset($matches[1]))
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name;
			$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
			
			for($a=0; $a<count($matches[1]); $a++)
			{
				// $id = get_string_between($matches[1][$a], '<id>');
				// $transactionDateTime = get_string_between($matches[1][$a], '<transactionDateTime>');
				// $clientPaymentDateTime = get_string_between($matches[1][$a], '<clientPaymentDateTime>');
				// $transactionCost = get_string_between($matches[1][$a], '<transactionCost>');
				// $reference = get_string_between($matches[1][$a], '<reference>');
				// $amount = get_string_between($matches[1][$a], '<amount>');
				// $serviceId = get_string_between($matches[1][$a], '<serviceId>');
				$id_encomenda = get_string_between($matches[1][$a], '<description>');
								
							
				$sql = "UPDATE encomendas SET Encomenda_Paga=1, Estado='13' WHERE ID='" . $id_encomenda . "'";
				$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
			}		
		}
		return true;
	}

	
?>