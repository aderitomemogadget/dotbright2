<?
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	include "settings.inc.php";
	

	/*$path="../../core/";
	require_once $path."sessions_engine.php";
	require_once $path."settings.php";
	require_once $path."db_engine.php";
	require_once $path."global.php";
	include "funcoes.inc.php";*/

	if(isset($_GET['m_pesquisa']) && $_GET['m_pesquisa']!='') $and = " WHERE ( c.Username like '%".$_GET['m_pesquisa']."%' OR  c.Email like '%".$_GET['m_pesquisa']."%' OR c.Nome like '%".$_GET['m_pesquisa']."%' OR c.Apelido like '%".$_GET['m_pesquisa']."%')";
	else $and = " WHERE 1=1";

	$filt1 = $_GET['filt1'];
	$filt2 = $_GET['filt2'];

	// ESTADO/TIPO
	if(isset($_GET['filt1']) && $_GET['filt1']==1) $and .= ' and (c.Ativo=1 || c.Estado=1)';
	if(isset($_GET['filt1']) && $_GET['filt1']==2) $and .= ' and (c.Ativo=0 and c.Estado=0)';
	if(isset($_GET['filt1']) && $_GET['filt1']==5) $and .= ' and c.Newsletter=1';
	if(isset($_GET['filt1']) && $_GET['filt1']==6) $and .= ' and c.Afiliado=1';

	// OUTROS PARAMETROS
	$filt_p = '';
	if(isset($_GET['op1_data']) && isset($_GET['m_data1_ini']) && isset($_GET['m_data1_fim']))
	{
		$and .= ' AND (DATE(c.Data) >= "'.$_GET['m_data1_ini'].'" AND DATE(c.Data) <= "'.$_GET['m_data1_fim'].'")';
		$filt_p .= '&m_data1_ini='.$_GET['m_data1_ini'];
		$filt_p .= '&m_data1_fim='.$_GET['m_data1_fim'];
	}
	if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))
	{
		$and2 .= ' AND (DATE(e.Data_Criacao) >= "'.$_GET['m_data2_ini'].'" AND DATE(e.Data_Criacao) <= "'.$_GET['m_data2_fim'].'")';
		$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
		$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
	}
	if(isset($_GET['op3_data']) && isset($_GET['m_data3_ini']) && isset($_GET['m_data3_fim']))
	{
		$and3 .= ' AND (DATE(nl.data_hora) >= "'.$_GET['m_data3_ini'].'" AND DATE(nl.data_hora) <= "'.$_GET['m_data3_fim'].'")';
		$filt_p .= '&m_data3_ini='.$_GET['m_data3_ini'];
		$filt_p .= '&m_data3_fim='.$_GET['m_data3_fim'];
	}
	if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))
	{
		$and .= ' AND (DATE(c.DataNascimento) >= "'.$_GET['m_data4_ini'].'" AND DATE(c.DataNascimento) <= "'.$_GET['m_data4_fim'].'")';
		$filt_p .= '&op4_data='.$_GET['op4_data'];
		$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
		$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
	}
	if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
	{
		$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
		$and2 .= ' AND (e.Estado=7)';
	}
	if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
	{
		$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
		$and2 .= ' AND (e.Estado!=7)';
	}
	if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
	{
		$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
		$and2 .= ' AND (el.voucher_id > 0)';
	}

	// ORDERNACAO
	if(!$_GET['filt2'] || $_GET['filt2']==0) $order = 'ORDER BY c.Nome ASC, c.Apelido ASC';
	elseif($_GET['filt2']==1) $order = 'ORDER BY c.Nome DESC, c.Apelido DESC';
	elseif($_GET['filt2']==2) $order = 'ORDER BY c.Data ASC';
	elseif($_GET['filt2']==3) $order = 'ORDER BY c.Data DESC';
	elseif($_GET['filt2']==4) $order = 'ORDER BY c.DataNascimento ASC';
	elseif($_GET['filt2']==5) $order = 'ORDER BY c.DataNascimento DESC';
	$groupby = ' GROUP BY c.ID';


	$contador+=5;
	if(!$from)$from=0;
	$qtd=30;
	/*if($and2 != '') $sql = 'SELECT c.*,e.enc_Email FROM clientes c ';
	else $sql = 'SELECT c.* FROM clientes c ';*/
    $sql = 'SELECT c.* FROM clientes c ';

    /*if($and2 != '') $sql .= ' INNER JOIN encomendas e ON e.ID_Cliente=c.ID INNER JOIN encomendas_produtos el ON el.ID_PAI=e.ID';
	if($and3 != '') $sql .= ' INNER JOIN nl_emails_registados nl ON c.Username=nl.Email';*/

	$sql .= $and.' '.$and2.' '.$and3.' '.$groupby.' '.$order;
	//echo $sql."<br>";

	//Nao fizeram registo
	if(isset($_GET['filt1']) && $_GET['filt1']==7)
	{

		// OUTROS PARAMETROS
		$filt_p='';

		if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))
		{
			$and2 .= ' AND (DATE(e.Data_Criacao) >= "'.$_GET['m_data2_ini'].' 00:00:00" AND DATE(e.Data_Criacao) <= "'.$_GET['m_data2_fim'].' 23:59:59")';
			$filt_p .= '&m_data2_ini='.$_GET['m_data2_ini'];
			$filt_p .= '&m_data2_fim='.$_GET['m_data2_fim'];
		}
		if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))
		{
			$and2 .= ' AND (DATE(e.enc_DataNascimento) >= "'.$_GET['m_data4_ini'].'" AND DATE(e.enc_DataNascimento) <= "'.$_GET['m_data4_fim'].'")';
			$filt_p .= '&op4_data='.$_GET['op4_data'];
			$filt_p .= '&m_data4_ini='.$_GET['m_data4_ini'];
			$filt_p .= '&m_data4_fim='.$_GET['m_data4_fim'];
		}
		if(isset($_GET['filt3_compra']) && $_GET['filt3_compra']==1)
		{
			$filt_p .= '&filt3_compra='.$_GET['filt3_compra'];
			$and2 .= ' AND (e.Estado=7)';
		}
		if(isset($_GET['filt3_n_conc']) && $_GET['filt3_n_conc']==1)
		{
			$filt_p .= '&filt3_n_conc='.$_GET['filt3_n_conc'];
			$and2 .= ' AND (e.Estado!=7)';
		}
		if(isset($_GET['filt3_voucher']) && $_GET['filt3_voucher']==1)
		{
			$filt_p .= '&filt3_voucher='.$_GET['filt3_voucher'];
			$and2 .= ' AND (el.voucher_id > 0)';
		}


		// ORDERNACAO
		if(!$_GET['filt2'] || $_GET['filt2']==0) $order='ORDER BY e.enc_Nome ASC, e.enc_Apelido ASC';
		elseif($_GET['filt2']==1) $order='ORDER BY e.enc_Nome DESC, e.enc_Apelido DESC';
		elseif($_GET['filt2']==2) $order='ORDER BY e.Data_Criacao ASC';
		elseif($_GET['filt2']==3) $order='ORDER BY e.Data_Criacao DESC';
		elseif($_GET['filt2']==4) $order='ORDER BY e.enc_DataNascimento ASC';
		elseif($_GET['filt2']==5) $order='ORDER BY e.enc_DataNascimento DESC';

		$groupby=' GROUP BY e.enc_Email';

		$sql='SELECT e.enc_Nome as Nome, e.enc_Apelido as Apelido,e.Data_Criacao as Data,e.enc_Email as enc_Email,e.enc_DataNascimento as DataNascimento,
		e.enc_Sexo as Sexo,

		e.enc_Morada as Morada,
		e.enc_Codigo_Postal as Codigo_Postal,
		e.enc_Telefone as Telefone,
		e.enc_Nif as Nif


		FROM encomendas e WHERE Username <> ""  AND Username <> "0"  AND ID_Cliente=0 '.$and2.' '.$groupby.' '.$order;
		//echo $sql."<br>";
	}


	$dados = new DataObj;
	$dados2 = new DataObj;
    $dados3 = new DataObj;

	global $DB_host,$DB_user,$DB_pass,$DB_name,$m_http_url_images,$site_http_url,$site_lang;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$dados->db_open($db_link,$DB_name,$sql);

	/*$filename = "listagem.csv";*/

	if(isset($_GET['op1_data']) && isset($_GET['m_data1_ini']) && isset($_GET['m_data1_fim']))$line .= 'DATA DE REGISTO ENTRE '.$_GET['m_data1_ini'].' e '.$_GET['m_data1_fim'].";\n";
	if(isset($_GET['op2_data']) && isset($_GET['m_data2_ini']) && isset($_GET['m_data2_fim']))$line .= 'FEZ ENCOMENDAS ENTRE '.$_GET['m_data2_ini'].' e '.$_GET['m_data2_fim'].";\n";
	if(isset($_GET['op3_data']) && isset($_GET['m_data3_ini']) && isset($_GET['m_data3_fim']))$line .= 'REGISTOU-SE NA NEWSLETTER ENTRE '.$_GET['m_data3_ini'].' e '.$_GET['m_data3_fim'].";\n";
	if(isset($_GET['op4_data']) && isset($_GET['m_data4_ini']) && isset($_GET['m_data4_fim']))$line .= 'NASCEU ENTRE '.$_GET['m_data4_ini'].' e '.$_GET['m_data4_fim'].";\n";

	//$line .= "NOME;APELIDO;MORADA;CODIGO POSTAL;TELEFONE;EMAIL;EMAIL ENC;NIF;SEXO;DATA NASCIMENTO;ATIVO;NEWSLETTER;AFILIADO;FEZ ENCOMENDAS;CONCLUIU COMPRAS\n";
    $line .= "DATA REGISTO;NOME;APELIDO;MORADA;CODIGO POSTAL;CIDADE;TELEFONE;EMAIL;NIF;SEXO;DATA NASCIMENTO;ATIVO;NEWSLETTER;AFILIADO;FEZ ENCOMENDAS;CONCLUIU COMPRAS;\n";
	while(!$dados->EOF())
	{
		$id_cliente = $dados->db_data['ID'];
		$m_nome = $dados->db_data['Nome'];
		$Data = $dados->db_data['Data'];
		$m_apelido = $dados->db_data['Apelido'];
		$m_morada = $dados->db_data['Morada'];
		$m_codpos = $dados->db_data['Codigo_Postal'];
        $m_cidade = $dados->db_data['Cidade'];
        $m_telefone=$dados->db_data['Telefone'];
		$m_email = $dados->db_data['Email'];
		$enc_Email = $dados->db_data['enc_Email'];
		$m_contribuinte = $dados->db_data['Nif'];
		$m_datanascimento = $dados->db_data['DataNascimento'];


        $sql3 = 'SELECT m_familia, bloco_titulo FROM frames f INNER JOIN frames_lang fl ON fl.bloco_id = f.bloco_id AND f.m_familia = '.$nfederacao;
        $dados3->db_open($db_link,$DB_name,$sql3);
        if(!$dados3->EOF())
        {
            $federacao = $dados3->db_data['bloco_titulo'];
        }
        $dados3->db_close();

        $fisio_mes = date('m', strtotime($dados->db_data['Data']));
        $fisio_ano = date('y', strtotime($dados->db_data['Data']));
        $IDU = 'FP'.$fisio_ano.$fisio_mes.str_pad($id_cliente, 6, '0', STR_PAD_LEFT);

        $Newsletter = ($dados->db_data['Newsletter']==1) ? 'SIM' : 'NÃO';
		$m_ativo = ($dados->db_data['Ativo']==1 || $dados->db_data['Estado']==1) ? 'SIM' : 'NÃO';

		$m_sexo='---';
		if($dados->db_data['Sexo']==1)$m_sexo='MASCULINO';
		if($dados->db_data['Sexo']==2)$m_sexo='FEMININO';

		//$m_datanascimento = $dados->db_data['DataNascimento'];
		$Afiliado = $dados->db_data['Afiliado']==1 ? 'SIM' : 'NÃO';


		if(isset($_GET['filt1']) && $_GET['filt1']==7)
		{
			$sql="Select count(*) as total from encomendas WHERE enc_Email='$enc_Email'";
			$dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $FEZ_COMPRA = $dados2->db_data['total']; else $FEZ_COMPRA = 0;
			$dados2->db_close();
			$sql="Select count(*) as total from encomendas WHERE enc_Email='$enc_Email' AND Estado=7";
			$dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $CONCLUIU_COMPRAS = $dados2->db_data['total']; else $CONCLUIU_COMPRAS = 0;
			$dados2->db_close();
		}
		else
		{
			$sql="Select count(*) as total from encomendas WHERE ID_Cliente=$id_cliente";
			$dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $FEZ_COMPRA = $dados2->db_data['total']; else $FEZ_COMPRA = 0;
			$dados2->db_close();
			$sql="Select count(*) as total from encomendas WHERE ID_Cliente=$id_cliente AND Estado=7";
			$dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $CONCLUIU_COMPRAS = $dados2->db_data['total']; else $CONCLUIU_COMPRAS = 0;
			$dados2->db_close();
		}

		$line .= $Data.";".$m_nome.";".$m_apelido.";".$m_morada.";".$m_codpos.";".$m_cidade.";".$m_telefone.";".$m_email.";".$m_contribuinte.";".$m_sexo.";".$m_datanascimento.";".$m_ativo.";".$Newsletter.";".$Afiliado.";".$FEZ_COMPRA.";".$CONCLUIU_COMPRAS." \n";
        //$line .="<br>";

		$dados->db_next();
	}
	$dados->db_close();

	// echo str_replace("\n", '<br>', $line);
	// exit;


	if($line)
	{
		$filename='clientes_'.date('YmdHmi').'.csv';
		header('Content-Encoding: UTF-8');
                header('Content-type: text/csv; charset=UTF-8');
                header("Content-Disposition: attachment;filename=\"".$filename."\"");
                header("Cache-Control: max-age=0");
                echo "\xEF\xBB\xBF";
                /*
	  header("Content-type: application/vnd.ms-excel");
	  header("Content-Disposition: attachment; filename=".$filename);
	  header("Content-type: text/html; charset=utf-8");
	  header("Pragma: no-cache");
	  header("Expires: 0");*/

	  echo $line;
	}

?>