<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
function grava_visita($pagename)
{
	return;
  global $save_stats,$nwid;
  if($save_stats)
  {
	global $DB_host,$DB_user,$DB_pass,$DB_name,$HTTP_USER_AGENT;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($db_link)
	{
		$data=date ("Y")."-".date ("m")."-".date ("d");
		$time=date("H:i");
		$dia_semana=date("l");
		$browser=$HTTP_USER_AGENT;
		$ip=getenv("REMOTE_ADDR");
		$pagename=addslashes($pagename);
		$host='';
		if(!$nwid)
		{
			/*if (isset($id)<>"") $referer=$id."; ";
			else $referer='';
			$referer.=getenv("HTTP_REFERER");
			$referer=addslashes($referer);
			$params=array(
				':pagename'=>$pagename,
				':hora'=>$time,
				':dayweek'=>$dia_semana,
				':browser'=>$browser,
				':host'=>$host,
				':ip'=>$ip,
				':data'=>$data,
				':referer'=>$referer
			);
			$result=Runquery($db_link,$DB_name,'INSERT INTO stats (pagename,hora,dayweek,browser,host,ip,data,referer) VALUES (:pagename,:hora,:dayweek,:browser,:host,:ip,:data,:referer)',$params);*/
			//db_closedb_conection($db_link);

    		$data=date ("Y") . "-" . date ("m") . "-" . date ("d");
    		$time=date("H:i");
    		$dia_semana=date("l");
    		$browser=$HTTP_USER_AGENT;
			$referer=getenv("HTTP_REFERER");
    		$ip=getenv("REMOTE_ADDR");
    		$host=gethostbyaddr($ip);
    		$result=Runquery($db_link,$GLOBALS['DB_name'],"INSERT INTO stats(pagename,hora,dayweek,browser,host,ip,data,referer) VALUES ('$pagename','$time','$dia_semana','$browser','$host','$ip','$data','$referer')");

		}
		else
		{

    		$sql="SELECT * FROM emails_registados WHERE ID=".$nwid;
			$hres=$rs->db_open($db_link,$DB_name,$sql);
			$email=$rs->db_data["Email"];
			$rs->db_close();
			$data=date ("Y") . "-" . date ("m") . "-" . date ("d");
    		$time=date("H:i");
    		$dia_semana=date("l");
    		$browser=$HTTP_USER_AGENT;
			$referer=getenv("HTTP_REFERER");
    		$ip=getenv("REMOTE_ADDR");
    		$host=gethostbyaddr($ip);
    		$result=Runquery($db_link,$GLOBALS['DB_name'],"INSERT INTO stats2(pagename,hora,dayweek,browser,host,ip,data,Email) VALUES ('$pagename','$time','$dia_semana','$browser','$host','$ip','$data','$email')");
			if (!$result) echo db_error($db_link);

			/*
			$sql="SELECT * FROM emails_registados WHERE ID=".$nwid;
			$hres=$rs->db_open($db_link,$DB_name,$sql);
			$email=$rs->db_data["Email"];
			$rs->db_close();
			$params=array(
				':pagename'=>$pagename,
				':hora'=>$time,
				':dayweek'=>$dia_semana,
				':browser'=>$browser,
				':host'=>$host,
				':ip'=>$ip,
				':data'=>$data,
				':Email'=>$email
			);
			$result=Runquery($db_link,$DB_name,"INSERT INTO stats2 (pagename,hora,dayweek,browser,host,ip,data,Email) VALUES (':pagename',':time',':dia_semana',':browser',':host',':ip',':data',':email')");
			if (!$result) echo db_error($db_link);*/
			//db_closedb_conection($db_link);
		}
	}
  }
}