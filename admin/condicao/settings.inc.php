<?
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php'; 
	include "funcoes.inc.php";

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][8])) die("Sem Permiss&otilde;es");

	$fol = end(explode("/",dirname(__FILE__))); //folder 
	$gen_name = 'Condição';
	$gender = 'Nova';
	$table = $fol;

	$tablefields = array(
		      array('name'=>'ID','type'=>'INT(6)','form'=>'','list'=>0),
		      array('name'=>'visivel','type'=>'TINYINT NOT NULL','form'=>'checkbox','list'=>1),
		      array('name'=>'ordem','type'=>'TINYINT NOT NULL','form'=>'number','list'=>1)
		     );
	
	addtableorfields($table,$tablefields);


	$table_lang = $table.'_lang';

	$tablefields_lang = array(
		      array('name'=>'ID','type'=>'INT(6)','form'=>'','list'=>0),
		      array('name'=>'ID_PAI','type'=>'INT(6) NOT NULL','form'=>'checkbox','list'=>1),
		      array('name'=>'nome','type'=>'VARCHAR(255) NOT NULL','form'=>'text','list'=>1),
		      array('name'=>'Lang','type'=>'VARCHAR(2) NOT NULL','form'=>'text','list'=>1)
		     );
	
	addtableorfields($table_lang,$tablefields_lang);

?>