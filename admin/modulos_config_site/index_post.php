<?
$path='../../core/';
include $path.'core_files.php';
include $path.'admin_globals.php';
global $user_cliente;

$x_login = trim($_POST['login']);
$password = trim($_POST['password']);

if(trim($x_login)=='') echo '<meta http-equiv="refresh" content="0; URL="pagina.php?show_erro=1" />';

global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$logins_rs=new DataObj;
$logins_rs->db_open($db_link,$DB_name,"SELECT logins.* FROM logins, grupos_logins WHERE MD5(username)='".md5($x_login)."' AND logins.Grupo_ID = grupos_logins.ID AND grupos_logins.ID=1");

if(!$logins_rs->EOF())
{
	if($logins_rs->db_data['estado']==0)echo '<script>window.open("pagina.php?show_erro=3","_self")</script>';
	else
	{ 
		$res=Runquery($db_link,$DB_name,"UPDATE logins SET failed_attempts=failed_attempts+1 WHERE ID=".$logins_rs->db_data['ID']);
		if($password)
		{
			if(encripta($password)==$logins_rs->db_data['password'])
			{					
				$res=Runquery($db_link,$DB_name,"UPDATE logins SET failed_attempts=0 WHERE ID=".$logins_rs->db_data['ID']);
				echo '<script>window.open("pagina.php","_self")</script>';			
			}
			else
			{
				$admin_passwd="";
				$logins_rs->db_close();
				echo '<script>window.open("pagina.php?show_erro=3","_self")</script>';				
			}
			$logins_rs->db_close();
		}
		else
		{
			$logins_rs->db_close();	
			echo '<script>window.open("pagina.php?show_erro=2","_self")</script>';
		}
	}
}
else 
{
	$logins_rs->db_close();
	echo '<script>window.open("pagina.php?show_erro=1","_self")</script>';
}
?>