<?	
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$command = "mysqldump -h$DB_host -u$DB_user -p$DB_pass $DB_name pages pages_lang --add-drop-table | gzip > ../../files/bk/db.sql.gz";
	system($command);	
	$fx = "../../files/bk/db.sql.gz";
	$contentType='application/octet-stream';
	header("Content-Length: ".(string)(filesize($fx)));
	header("Content-Size: ".(string)(filesize($fx)));
	header("Pragma: ");
	header("Cache-Control: ");
	header("Content-type: $contentType");
	header("Content-Disposition: attachment; filename=\"bk_".date("YmdHmi").".sql.gz\"");
	$fd=fopen($fx,'r');
	fpassthru($fd);		
?>