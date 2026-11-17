<?php


error_reporting(E_ERROR);
ini_set('display_errors',-1);

$path = dirname( __FILE__ ) . '/../../core/';
require_once $path . 'core_files.php';

echo 'Inicio envio '.date('Y-m-d H:i:s').'<br>';

global $DB_host,$DB_user,$DB_pass,$DB_name,$lang;
$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);

error_reporting(E_ERROR);
ini_set('display_errors',1);


$rs = new DataObj;
$sql='ALTER TABLE clientes_notificacoes ADD ENVIADO TINYINT(1) AFTER ID_Prod';
$rs->db_open($db_link,$DB_name,$sql);

$rs = new DataObj;
$sql='SELECT * FROM clientes_notificacoes WHERE ENVIADO="0"';
$rs->db_open($db_link,$DB_name,$sql);
while(!$rs->EOF())
{
	$ID 		= $rs->db_data['ID'];
	$id_prod 	= $rs->db_data['ID_Prod'];
	$mailto 	= $rs->db_data["Email"];
	// $nome 		= $rs->db_data["NOME"];

	$prod = get_produtc_info($id_prod);
	// echo '<pre>';
	// print_r($prod);
	// echo '</pre>';
	$p_id = $prod['prod_id'];
	$p_nome = $prod['prod_nome'];
	$p_stock = $prod['prod_precos']['STOCK'];
	$p_preco = $prod['prod_precos']['PVP'];
	$p_url = seo('pages.php?page_id='.$GLOBALS['products_page_id'],$p_id,2);
	
	
	
	if($p_stock > 0 and $p_preco > 0)
	{

		echo ' &gt;&gt;&gt; '.$p_nome.' | '.$mailto.' - '.$nome.' - '.$p_preco.' &lt;&lt;&lt;';


		$mailsubject = SITE_TITLE." - Alerta de Stock";
		
		$body = "Caro Cliente,<br><br><br>";
		$body .= "A ".SITE_TITLE." alerta-o de que o produto: ".$p_nome." já se encontra disponível.<br>";
		$body .= "Pode aceder ao mesmo através do link: <a href=\"".$site_http_url.$p_url."\">".$site_http_url.$p_url."</a><br><br><br>";
		$body .= "Obrigado pela preferência.<br> ".SITE_TITLE."";

		// $body = array(
			// '[email]' => $mailto, 
			// '[code]' => $site_http_url.$p_url, 
			// '[image]' => '<img src="'.$site_http_url.$prod['prd_imgm_med'].'" alt=""/>', 
			// '[produto]' => $p_nome, 
			// '[dimensoes]' => $prod['prd_filtro'], 
			// '[preco]' => numberformat($prod['prod_precos']['PVP'])
		// );
        $message = template_email($GLOBALS['template_alerta_stock'], $body);
        // $message['body'] .= $body;
        // 
        // 
        // echo $prod['prd_imgm_med'].'<br>';
        echo $mailto.'<br>';
        echo $message['subject'].'<br>';
        echo $message['body'].'<br><br>';

		 if(send_email($mailto,$message['subject'],$message['body']))
		{
			echo " - sucesso!";
			$sql='UPDATE clientes_notificacoes SET ENVIADO="1" WHERE ID="'.$ID.'"';
			Runquery($db_link,$DB_name,$sql);
		} 
		 echo '<br>';
	}

	$rs->db_next();
}
$rs->db_close();
echo 'Finalizou envio '.date('Y-m-d H:i:s').'<br>';