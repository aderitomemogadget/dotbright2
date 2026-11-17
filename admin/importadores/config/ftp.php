<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','-1');
	header('Content-Type: text/html; charset=utf-8');

	if(isset($argv) && $argv[1] > 0)$Supplier_ID=intval($argv[1]);
	if(isset($_GET['Supplier_ID']))$Supplier_ID=intval($_GET['Supplier_ID']);

	/*$path = dirname(__FILE__) . '/../../../core/';
	include $path . 'core_files.php';
	include dirname(__FILE__) .'/funcoes.inc.php';*/

	require_once('settings.inc.php');

	global $Supplier_ID;
	 // as funcoes tem de estar todas dentro desta pasta para poder copiar o admin sem ter de copiar fxs do core para outros sites
	$rs = getImportadorConfiguracoes_2($Supplier_ID);

	$ftp_servidor = $rs->db_data['ftp_servidor'];
	$ftp_porta = $rs->db_data['ftp_porta'];
	$ftp_user = $rs->db_data['ftp_user'];
	$ftp_password = $rs->db_data['ftp_password'];
	$ftp_passivo = $rs->db_data['ftp_passivo'];
	$ftp_caminho = $rs->db_data['ftp_caminho'];

	$rs->db_close();

	define('SERVER', $ftp_servidor); //IP o Nome do Servidor
	define('PORT', ($ftp_porta == 0 || $ftp_porta == '' ? 21 : $ftp_porta)); //Porto
	define('USER', $ftp_user); //Nome de Usuário
	define('PASSWORD', $ftp_password); //Senha de acesso
	define('PASV', ($ftp_passivo == 1 ? true : false)); //Ativa modo passivo

	function ConectarFTP2()
	{
		// PERMITE SE CONECTAR AO SERVIDOR FTP
		$id_ftp = ftp_connect(SERVER, PORT) or die("Não consegui ligação com ".SERVER);

		// TRY TO LOGIN
		if (@ftp_login($id_ftp,USER, PASSWORD))
		{
			echo SERVER." » Ligação OK";
		}
		else
		{
			echo "Dados ftp incorretos";
		}

		if(PASV == true) ftp_pasv($id_ftp, MODO); //Estabelece o modo de conexão
		return $id_ftp; //Devolve o manejador à função
	}

	function ObterRoot()
	{
		// Obtem rota do diretório do Servidor FTP (Comando PWD)
		$id_ftp = ConectarFTP2(); //Obtém um manejador e se conecta ao Servidor FTP
		ftp_pasv($id_ftp, true);
		ftp_chdir($id_ftp, "/exports/");
		ftp_quit($id_ftp); //Fecha a conexão FTP
		return $Diretorio; //Devolve a rota à função
	}
?>