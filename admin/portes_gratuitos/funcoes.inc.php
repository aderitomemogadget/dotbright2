<?
function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$site_http_url;
	?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1">
    		<input class="btn btn-primary" type="submit" name="Submit" value="<?=$task_button?>">
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('<?=$site_http_url?>admin/index.php','_self')" />
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
<?
}

function show_page_tools($filter=0)
{
	global $tipos; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PORTES GRATUITOS</h4>
        </div>
    </div>
<? }
function ChangeRecord()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$valor_portes = $_POST['valor_portes'];

	$sql = 'SELECT * FROM portes_gratuitos';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$sql="UPDATE portes_gratuitos SET valor_portes='".$valor_portes."'";
		$res = Runquery($db_link,$DB_name,$sql);
	}else{
		$sql="INSERT INTO portes_gratuitos(valor_portes) VALUES ('".$valor_portes."')";
		$res = Runquery($db_link,$DB_name,$sql);
	}
	$rs->db_close();
	return $res;
}
?>