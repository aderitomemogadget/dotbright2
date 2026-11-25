<?
define("MAIL_UNREAD", 0);
define("MAIL_READ", 1);
define("MAIL_REPLIED", 2);
define("MAIL_FORWARDED", 3);
	
define("HIGHEST_PRIORITY", 1);
define("HIGH_PRIORITY", 2);
define("NORMAL_PRIORITY", 3);
define("LOW_PRIORITY", 4);
define("LOWEST_PRIORITY", 5);

define("FULL_VIEW", 0);
define("MID_VIEW", 1);
define("MIN_VIEW", 2);

define("POPUP_VIEW", 0);
define("IN_VIEW", 1);
define("SEP_VIEW", 2);	
define("FRAME_VIEW", 3);

function count_msgs($folder,$user_id){
	global $DB_host,$DB_user,$DB_pass,$DB_name,$ss_area;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);   
	$dados=new DataObj;
	/*if($folder=="sent")	$sql = "SELECT count(*) as total FROM mensagens WHERE ID_From = $user_id AND folder='$folder'";
	else 	$sql = "SELECT count(*) as total FROM mensagens WHERE ID_Cliente = $user_id AND folder='$folder'";

	$hRes= $dados->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	else $total=$dados->db_data["total"];
	$dados->db_close();
	if($total) echo " ($total)";*/
    $total=0;
	//$sql = "SELECT count(*) as total FROM mensagens WHERE ID_Cliente = $user_id AND folder='$folder' AND was_read=0 AND area='$ss_area'";
	$sql = "SELECT count(*) as total FROM mensagens WHERE ID_Cliente = $user_id AND folder='$folder' AND was_read=0 ";
	
	$hRes= $dados->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	else $total=$dados->db_data["total"];
	$dados->db_close();
	if($total) echo " <b>($total)</b>";
}

function show_msgs_folders($pai,$user_id){
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);   
	$dados=new DataObj;
	$sql = "SELECT * FROM msg_folders WHERE user_id = $user_id AND ID_ORG=0 order by Name";
	$hRes= $dados->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	$i=0;
    while(!$dados->EOF()){
    	$name=$dados->db_data["Name"];
		$ID_ORG=$dados->db_data["ID"];
    	$novo_top=$pai."s".$i;
		echo 'dtree_43de6364d8222.add("'.$novo_top.'","0","'.$name;
		count_msgs($name,$user_id);
		echo '","list.php?folder='.$name.'","","ifrm","");';
        msg_show_submenu($novo_top,$user_id,$ID_ORG);
		$pai++;
        $dados->db_next();	
        $i++;
	}
	$dados->db_close();
}
function msg_show_submenu($pai,$user_id,$ID_ORG){
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);   
	$dados=new DataObj;
    $sql="Select * from msg_folders where ID_ORG='".$ID_ORG."' AND user_id=$user_id order by Ordem, Name Asc";
	$hRes= $dados->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	$i=0;
    while(!$dados->EOF()){
	    $name=$dados->db_data["Name"];
		$ID_ORG=$dados->db_data["ID"];
    	$novo_top=$pai."s".$i;
		echo 'dtree_43de6364d8222.add("'.$novo_top.'","'.$pai.'","'.$name;
		count_msgs($name,$user_id);
		echo '","list.php?action=list&folder='.$name.'","","ifrm","");';
        msg_show_submenu($novo_top,$user_id,$ID_ORG);	
        $dados->db_next();	
        $i++;
	}
	$dados->db_close();
}

function show_msg($x_mail_id,$userid,$show_att_mail = false)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$mail_id;
	global $key_email27,$key_email29,$key_email28;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados= new DataObj;
	$dados2= new DataObj;
	$mail_id=$x_mail_id;
	$sql="SELECT * FROM mensagens WHERE ID='$x_mail_id'";
	$hRes= $dados->db_open($db_link,$DB_name,$sql);
	if(!$dados->EOF())
	{
		$m_nome="";
		if($dados->db_data["ID_From"])
		{
			$sql="SELECT * FROM logins where ID = ".$dados->db_data["ID_From"];
			$hRes= $dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $m_nome=$dados2->db_data["Nome"];
			$dados2->db_close();
		}
		//my_print_r($dados->db_data);
		echo "<tr><td valign=top>";
		echo "<table width=100% bgcolor=#f6f6f6 border=0 cellpadding=5 cellspacing=2  style=\"border-bottom: 1px solid black;\">";
		echo "<tr><td align=\"left\" width=\"15%\"><b>De:</b></td><td align=\"left\">".$m_nome."</td></tr>";
		echo "<tr><td align=\"left\" width=\"15%\"><b>Data:</b></td><td align=\"left\">".$dados->db_data["Data"]."</td></tr>";
		echo "<tr><td align=\"left\" width=\"15%\"><b>Para:</b></td><td align=\"left\">";
		    $enviado=$dados->db_data["Enviado"];
			$ArrayPalavras=explode(";",$enviado);
			$n_palavras=count($ArrayPalavras);
			if($n_palavras>5) $n_palavras=5;
			for($j=0;$j<$n_palavras;$j++)
			{
				if($j>0)  echo ";";
				if($ArrayPalavras[$j])
				{
					$sql="SELECT * FROM logins where ID=".$ArrayPalavras[$j];
					$hRes=$dados2->db_open($db_link,$DB_name,$sql);
					if(!$dados2->EOF()) $m_nome=$dados2->db_data["Nome"];
					$dados2->db_close();
					echo " ".$m_nome;
				}
				else echo " ".$m_nome;//e porque enviou para ele proprio
			}
			if(count($ArrayPalavras)>$n_palavras) echo "... (mais ".(count($ArrayPalavras)-$n_palavras)." destinatários)";
		echo "</td></tr>";
		echo "<tr><td align=\"left\" width=\"15%\"><b>Assunto:</b></td><td align=\"left\">".htmlspecialchars($dados->db_data["Assunto"])."</td></tr>";
		
		echo "</table>";
		echo "<table width=100% bgcolor=#ffffff border=0 cellpadding=10 cellspacing=0><tr><td align=\"left\">";
		
		echo $dados->db_data["Mensagem"];
		
		echo "</td></tr></table>";
		echo "</td></tr></table>";
		echo "</body></html>";

		$sql = "UPDATE mensagens SET was_read = ".MAIL_READ." WHERE ID='".$dados->db_data["ID"]."'";
		$res = Runquery($db_link,$DB_name,$sql);
		if(!$res){echo db_error($db_link);return;}
		$dados->db_close();	
	}
}
function show_mensagens2($x_mensagens_id,$userid,$show_att_mensagens = false){
	global $mensagens_id;
	$mensagens_id=$x_mensagens_id;
	$mensagenser = array();		
	unset($mensagenser);
	$mensagenser = new mensagenser($userid);
	$mensagenser->load($mensagens_id);
	echo("<tr bgcolor=ffffff><td>");
	echo("<div class=\"header\">");
	echo("<table bgcolor=E0E0E0 border=0 cellpadding=1 cellspacing=1>\n");
	echo("<tr><td width=\"15%\"><b>De:</b></td><td>".htmlspecialchars(stripslashes($mensagenser->headers->get("From")))."</td></tr>");
	echo("<tr><td><b>Para:</b></td><td>".htmlspecialchars(stripslashes($mensagenser->headers->get("To")))."</td></tr>");
	$cc = $mensagenser->cc();
	if(!empty($cc)) echo("<tr><td><b>Cc:</b></td><td>".htmlspecialchars($mensagenser->cc())."</td></tr>");
	echo("<tr><td><b>Data</b></td><td>".$mensagenser->headers->get("date")."</td></tr>");
	$subject = $mensagenser->charset_decode(stripslashes($mensagenser->subject()));
	echo("<tr><td><b>Assunto:</b></td><td>".htmlspecialchars($subject)."</td></tr>");
	$priority = $mensagenser->headers->get("X-Priority");
	$priority = $priority[0];		
	if(!empty($priority) && $priority != NORMAL_PRIORITY){
		echo("<tr><td><b>Prioridade:</b></td><td>");
		if($priority==HIGHEST_PRIORITY)		echo("<span style=\"background-color: #ff3333;\">HighestPriority</span>");
		if($priority==HIGH_PRIORITY) 		echo("<span style=\"background-color: #ff6666;\">HighPriority</span>");
		else if($priority==LOW_PRIORITY)		echo("<span style=\"background-color: #9999ff; color: white;\">LowPriority</span>");
		else if($priority==LOWEST_PRIORITY)	echo("<span style=\"background-color: #4444ff; color: white;\">LowestPriority</span>");
		echo("</td></tr>");
	}	
	// Otherwise we show the links to the attachment
	if($mensagenser->is_mime()){
		echo("<tr><td><b>Anexos: </b></td><td>");
		show_attachments($mensagenser);
	}
	echo("</td></tr>");
	show_notification_request();
	echo("</table>");	
	echo "<table bgcolor=ffffff border=0 cellpadding=10 cellspacing=0><tr><td>"; show_mensagens_content($mensagenser,$mensagens_id,$userid); echo "</td></tr></table>";
	echo("</tr></td></table>");
	echo("</div></body></html>");
}

function make_clickable(&$content){
	$content = preg_replace("|(\s+)(http://.*)(\s+)|U", "\\1<a href=\"\\2\" target=\"_blank\" style=\"width: auto; color: blue; text-decoration: underline;\">\\2</a>\\3",$content);
	$content = preg_replace("|(\s*)([a-zA-Z0-9.-_]*@[a-zA-Z0-9.-_]*)(\s+)|U", "\\1<a href=\"$globalS[PHP_SELF]?action=newmensagens&addr=\\2\" target=\"_parent\" style=\"width: auto; color: blue; text-decoration: underline;\">\\2</a>\\3",$content);
}

function set_mensagens_read($mensagens_id,$userid){
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);   
	$dados=new DataObj;
	$sql = "UPDATE mensagens SET was_read='".MAIL_READ."', Estado='3' WHERE id='$mensagens_id' AND user_id='$userid' AND was_read='".MAIL_UNREAD."'";
	$res = Runquery($db_link,$DB_name,$sql);
	if(!$res)	echo db_error($db_link);
	db_closedb_conection($db_link);
}

function show_msg_folders($Nomeb,$user_id,$id_org=0,$valor=""){
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql= "Select * from $Nomeb where ID_ORG='0' and user_id = $user_id order by Name Asc";
	$hRes= $obj->db_open($db_link,$DB_name,$sql);   
    while(!$obj->EOF()){
        echo '<option value="'.$obj->db_data["Name"].'"';
		if($valor==$obj->db_data["Name"]) echo ' selected ';
		echo '>'.$obj->db_data["Name"].'</option>';
	    show_msg_foldersb($Nomeb,$user_id,$id_org,$obj->db_data["ID"],5,$valor);
        $obj->db_next();
    }
    $obj->db_close();
}

function show_msg_foldersb($Nomeb,$user_id,$id_org=0,$id_start=0,$contador=5,$valor=""){
    global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql= "Select * from $Nomeb where ID_ORG='$id_start' and user_id = $user_id order by Ordem, Name Asc";
	$hRes= $obj->db_open($db_link,$DB_name,$sql);
    while(!$obj->EOF()){
        echo '<option value="'.$obj->db_data["Name"].'"';
		if($valor==$obj->db_data["Name"]) echo ' selected ';
		echo '>';
		$i=0;while($i<$contador){echo "&nbsp;";$i++;}
		echo $obj->db_data["Name"] . '</option>';
		show_msg_foldersb($Nomeb,$user_id,$id_org,$obj->db_data["ID"],$contador+5,$valor);
        $obj->db_next();
    }
    $obj->db_close();
}	
?>