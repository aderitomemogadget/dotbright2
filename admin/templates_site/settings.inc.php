<?
	$path="../../core/";
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	include "funcoes.inc.php";

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][15])) die("Sem Permiss&otilde;es");


	//Vamos verificar se já foi criada a tabela
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="Select ID FROM templates_site";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res)
	{
		// vamos verificar se os campos existem todos na base de dados
		$res=Runquery($db_link,$DB_name,'
			CREATE TABLE `templates_site` (
			  `ID` int(11) NOT NULL AUTO_INCREMENT,
			  `Ordem` int(5) NOT NULL DEFAULT \'0\',
			  `Data` date NOT NULL DEFAULT \'0000-00-00\',
			  `Data2` date DEFAULT \'0000-00-00\',
			  `img_ext` varchar(5) DEFAULT NULL,
			  `Visivel` tinyint(1) NOT NULL DEFAULT \'0\',
			  `Visivel2` tinyint(1) NOT NULL DEFAULT \'0\',
			  `Destaque` tinyint(1) NOT NULL DEFAULT \'0\',
			  `margem_top` int(2) NOT NULL DEFAULT \'0\',
			  `bg_color` varchar(7) NOT NULL DEFAULT \'\',
			  `efeito` tinyint(1) NOT NULL DEFAULT \'0\',
			  `URL` varchar(255) NOT NULL DEFAULT \'\',
			  `site_width` varchar(11) DEFAULT NULL,
			  `estilo` tinyint(1) NOT NULL DEFAULT \'0\',
			  `estilo2` tinyint(1) NOT NULL DEFAULT \'0\',
			  `Protected` tinyint(1) NOT NULL,
			  PRIMARY KEY (`ID`)
			)');
	}
	$sql="Select ID FROM templates_site_lang";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res)
	{
		// vamos verificar se os campos existem todos na base de dados
		$res=Runquery($db_link,$DB_name,'
			CREATE TABLE `templates_site_lang` (
		  `ID` int(11) NOT NULL AUTO_INCREMENT,
		  `ID_PAI` int(11) NOT NULL DEFAULT \'0\',
		  `Cabecalho` varchar(255) NOT NULL DEFAULT \'\',
		  `Texto1` blob,
		  `Texto2` blob,
		  `Texto3` blob,
		  `Texto4` blob,
		  `Lang` varchar(2) NOT NULL DEFAULT \'\',
		  PRIMARY KEY (`ID`),
		  KEY `ID_PAI` (`ID_PAI`),
		  KEY `Lang` (`Lang`)
		)');
	}



	// vamos verificar se os campos existem todos na base de dados
	$fields=db_list_fields($DB_name,'templates_site',$db_link);
	$columns=db_num_fields($fields);
	for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Tipo',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE templates_site ADD Tipo tinyint(1) ');


?>