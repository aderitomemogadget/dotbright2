<?
	$path="../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	include "funcoes.inc.php";
	// vai verificar se já fez login...

	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$user_id=$admin_id;
	include "../layout/header.inc.php";
?>
<head>

<script>
function muda(obj){

  if(document.getElementById){
    var el=document.getElementById(obj);

    var obj2=obj + "b";
    var el2=document.getElementById(obj2);

    var obj3=obj + "a";
    var el3=document.getElementById(obj3);

    var ar=document.getElementById("pai").getElementsByTagName("span");
    if(el.style.display !="block"){
			el.style.display="block";
			el2.style.display="block";
			el3.style.display="none";
		}
		else
		{
			el.style.display="none";
			el2.style.display="none";
			el3.style.display="block";
		}
	}
}
</script>
<style>
	html body{ background-color:#FFF; background-image:none}
	td {font-size: 12px;font-family: verdana;color: #000;text-decoration: none;}
	a {font-size: 11px;font-family: verdana;color: #000;text-decoration: none;}
	a:link {font-size: 11px;  font-family: verdana;  color: #000;  text-decoration: none;}
	a:active {  font-size: 11px;  font-family: verdana;  color: #000;  text-decoration: none;}
	a:visited {  font-size: 11px;  font-family: verdana;  color: #000;  text-decoration: none;}
	a:hover {  font-size: 11px;  font-family: verdana;  color: #0295db;  text-decoration: none;}
</style>
</head>
<body bgcolor="#FFFFFF" style="overflow: auto"><form>
<table width="100%" height="100%" border=0 align="center" cellpadding=0 cellspacing=0 class="showmail">
<tr>
	<td bgcolor="#def2fa" class="toolbar" height="40" valign="middle">&nbsp;<?
		global $mail_id,$task_button;
		if(!$emails){
			if($folder !="sent") echo "<a href=\"send.php?tos=reply&mail_id=$mail_id\"><img src=\"graphics/reply.gif\" alt=\"reply\" title=\"reply\" border=\"0\"></a>&nbsp;";
			echo "<a href=\"list.php?action=list&folder=$folder&mail_id=$mail_id&moveuniq=bin&move=delete\"><img src=\"graphics/cancel.gif\" alt=\"apagar\" title=\"apagar\" border=\"0\"></a>&nbsp;";

			if(!$order){$order="Desc";$order2="Asc";}
			elseif($order=="Desc") $order2="Asc";
			else $order2="Desc";
			if(!$sort) $sort="idate";
			$limit2=$limit+1;
			if($limit>0){
				$sql="SELECT * FROM mensagens WHERE ID_Cliente Like '$user_id' and folder Like '$folder' order by $sort $order limit ".($limit-1).",1";
				$hRes=$dados->db_open($db_link,$DB_name,$sql);
				if(!$dados->EOF()) echo("<a href=\"viewmsg.php?mail_id=".$dados->db_data["ID"]."&sort=".$sort."&order=".$order."&user_id=".$user_id."&folder=".$folder."&limit=".($limit-1)."\"><img src=\"graphics/back.gif\" alt=\"previous\" title=\"previous\" border=\"0\"></a>&nbsp;");
				$dados->db_close();
				$limit++;
			}
			$sql="SELECT * FROM mensagens WHERE ID_Cliente Like '$user_id' and folder Like '$folder' order by $sort $order limit $limit2,1";
			$hRes=$dados->db_open($db_link,$DB_name,$sql);
			if(!$dados->EOF()) echo("<a href=\"viewmsg.php?mail_id=".$dados->db_data["ID"]."&sort=".$sort."&order=".$order."&user_id=".$user_id."&folder=".$folder."&limit=".$limit2."\"><img src=\"graphics/next.gif\" alt=\"next\" title=\"next\" border=\"0\"></a>&nbsp;");
			$dados->db_close();
		}?>
		</td>
	</tr>
	<?
		if(!$emails) show_msg($mail_id,$user_id);
		else show_msg2($mail_id,$user_id);
	?></form>
</body>
</html>