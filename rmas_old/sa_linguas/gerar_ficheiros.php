<?php
	
	$path = dirname( __FILE__ ) . '/../../core/';
	require_once $path . 'core_files.php';
	
	define('TBMODULO', 'blocos_linguas');	
	define('TBID', 'bloco_id');
	define('TBMODULO_LANG', 'blocos_linguas_lang');
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
  
	$rs2 = new Dataobj;

	$str  = '<?php '."\n";
	$str .= 'if(isset($_SERVER["SCRIPT_FILENAME"]) && $_SERVER["SCRIPT_FILENAME"] == __FILE__ ) exit();'."\n";
		
	$sql = 'SELECT bloco_titulo, bloco_texto, bloco_lang FROM '.TBMODULO.' b INNER JOIN '.TBMODULO_LANG.' bl ON bl.bloco_id = b.bloco_id ORDER BY bloco_titulo ASC';
	$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{			
			$key = $rs2->db_data['bloco_titulo'];
			$key_txt = $rs2->db_data['bloco_texto'];
			$key_txt = str_replace(array('\\\\','\\\\\\'), '', $rs2->db_data['bloco_texto']);
			$lingua = $rs2->db_data['bloco_lang'];
			
			$str .= '$'.$key.'[\''.$lingua.'\'] = \''.addslashes($key_txt)."';\n";		
			
			
			$rs2->db_next();
		}
		$rs2->db_close();
		
	}
	$rs2->db_close();
	
	
	$str .= "?>";
	
	unlink(dirname( __FILE__ ).'/../../files/linguas/linguas.php');
	file_put_contents(dirname( __FILE__ ).'/../../files/linguas/linguas.php', $str);
	
?>
