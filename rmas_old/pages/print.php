<?
    include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	
	header('Expires: 0');
	header('Cache-Control: must-revalidate,post-check=0,pre-check=0');
	header('Pragma: public');
	$sql="SELECT * from pages_lang WHERE Lang='$admin_lang'";	
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
    while(!$rs->EOF())
	{
		$IDb=$rs->db_data["IDb"];
		$Visivel=$rs->db_data["Visible"];
		$Protected=$rs->db_data["Protected"];
		$ordem=$rs->db_data["Ordem"];
		$cabecalho=stripslashes($rs->db_data["Cabecalho"]);
		$T=stripslashes($rs->db_data["Texto"]);
		
		$T=clean_code($T);

        echo $cabecalho."<br>".$T."<hr>";

		$rs->db_next();	
	}
	$rs->db_close();
?><script language="javascript">
window.print();
</script>