<?
	$path="../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	include "funcoes.inc.php";
	if(!$ss_paginar)  $ss_paginar="20";
	if(trim($bgn)=="") $bgn=0;	
	if(!$num && $ss_paginar) $num=$ss_paginar;
	$limitar=" LIMIT ".$bgn.",".$ss_paginar;
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados=new DataObj;
	$dados2=new DataObj;
	$user_id=$admin_id;
	
	if($move){
		foreach($_POST as $key=> $valor){
			if((strcmp(substr($key,0,2),"m_")==0)){
				if($where) $where.=" OR id=$valor";
				else $where=" id=$valor ";
			}
		}
		if($where){
			if($move=="delete"){
				$dados2=new DataObj;
				$sql="SELECT * FROM mensagens WHERE $where";
				$hRes=$dados2->db_open($db_link,$DB_name,$sql);
				while(!$dados2->EOF()){
					$cod=$dados2->db_data["ID"];
					$filename=$dados2->db_data["filename"];
					$sql="DELETE FROM mensagens WHERE id=$cod";
					if(!Runquery($db_link,$DB_name,$sql)) echo db_error($db_link);
					$dados2->db_next();
				}
				$dados2->db_close();
			}
			else 
			{
				$sql="UPDATE mensagens SET folder='$move' WHERE $where";
				if(!Runquery($db_link,$DB_name,$sql)) echo db_error($db_link);
			}
		}
	}
	if($moveuniq){
		$sql="UPDATE mensagens SET folder='$moveuniq' WHERE id=$mail_id";
		if(!Runquery($db_link,$DB_name,$sql)) echo db_error($db_link);
	}
	if($flag){
		foreach($_POST as $key=> $valor){
			if((strcmp(substr($key,0,2),"m_")==0)){
				if($where) $where.=" OR id=$valor";
				else $where=" id=$valor ";
			}
		}
		if($where){
			if($flag==1) $sql="UPDATE mensagens SET was_read='1' WHERE $where";
			elseif($flag==2) $sql="UPDATE mensagens SET was_read='0' WHERE $where";
			if(!Runquery($db_link,$DB_name,$sql)) echo db_error($db_link);
		}
	}
	include "../layout/header.inc.php";	

	if(!$order) $order="Desc";
	elseif($order=="Desc") $order="Asc";
	else $order="Desc";
	if(!$sort) $sort="Data";
?>
<body bgcolor="#FFFFFF">
	<script language="JavaScript" type="text/javascript">
		<!--		
		function makeSelection(selObj){
			if(selObj.value==1)	for(i=0; i<document.mail.elements.length; i++)	document.mail.elements[i].checked=true;
			if(selObj.value==2)	for(i=0; i<document.mail.elements.length; i++)	document.mail.elements[i].checked=false;
		}
		//-->
		</script>
        <style>
        html,body{background-color:#FFF;background-image:none; border:none; padding:0; text-align:left}
		td{font-size:12px;font-family:verdana;color:#000;text-decoration:none}
		a{font-size:11px;font-family:verdana;color:#000;text-decoration:none}
		a:link{font-size:11px;font-family:verdana;color:#000;text-decoration:none}
		a:active{font-size:11px;font-family:verdana;color:#000;text-decoration:none}
		a:visited{font-size:11px;font-family:verdana;color:#000;text-decoration:none}
		a:hover{font-size:11px;font-family:verdana;color:#0295db;text-decoration:none}
		td.line{border-right:#FFF 1px solid}
		</style>
		<form class="mail" name="mail" id="mail" action="<?=$globalS[PHP_SELF]?>?folder=<?=$folder?>" method="post"><? if(!$emensagens){?>
		<table width=100% border=0 cellpadding="10" cellspacing=0 >
		<tr height=20>
			<td valign="top" class="branco">
                <select class="forms" name="filter" onChange="makeSelection(this);">
                    <option value="" selected="selected">Seleccionar:</option>
                    <option value="1">Tudo</option>
                    <option value="2">Nenhuma</option>
                </select>
                <select class="forms" name="flag">
                    <option value="" selected="selected">Marcar como</option>
                    <option value="1">Lida</option>
                    <option value="2">Não Lida</option>
                </select>
                <select class="forms" name="move">
                    <option value="" selected="selected">Mover para</option>
                    <option value="inbox">Recebidos</option>
                    <option value="bin">Lixo</option><?
                    show_msg_folders("msg_folders",$user_id);
                    if($folder=="bin") echo '<option value="delete">Apagar definitivamente</option>';
                    ?>
                </select>
                <input class="botao" type="submit" name="OK" value="OK" style="width:30px;">
        	</td>
		</tr>
		</table>
		<table width=100% border=0 cellspacing=4 cellpadding=4 bgcolor="#FFFFFF"><? if(!$emensagens){?>
		<tr height=25 style="text-align:center;" bgcolor="#115488">
			<td  style="text-align:center; width:15px;">&nbsp;</td>
			<td  style="text-align:left;" width="100" class="line"><a style="font-weight:bolder;color:#FFF" href="list.php?folder=<?=$folder?>&order=<?=$order?>&sort=ID_From" title="Enviado por:" target="_self">Enviado por:</a></td>
            <?
			if($folder=="sent") echo '<td  style="text-align:left; " width="150px" class="line"><a style="font-weight:bolder;color:#FFF" href="list.php?folder=<?=$folder?>&order=<?=$order?>&sort=ID_From" title="enviadas" target="_self">Para</a></td>';
			?>
			<td  style="text-align:center; " class="line"><a style="font-weight:bolder;color:#FFF" href="list.php?folder=<?=$folder?>&order=<?=$order?>&sort=Assunto" title="assunto" target="_self">Assunto</a></td>
			<td  style="text-align:left; " width="120px" class="line"><a style="font-weight:bolder;color:#FFF" href="list.php?folder=<?=$folder?>&order=<?=$order?>&sort=Data" title="data" target="_self">Data</a></td>
		</tr><? }
		
		if($folder=="sent") $sql2="SELECT * FROM mensagens WHERE ID_From Like '$user_id' and folder Like '$folder'";
		else $sql2="SELECT * FROM mensagens WHERE ID_Cliente Like '$user_id' and folder Like '$folder'";
		$sql=$sql2." order by $sort $order ".$limitar;
		$limit=0;
		$dados->db_open($db_link,$DB_name,$sql);
		
        while(!$dados->EOF())
		{
		    $style='';
			// onmouseover="this.style.backgroundColor=\'#def2fa\'"; 
			// onmouseout="this.style.backgroundColor=\'#f6f6f6\'"
			// ;
			if($color == "#d2ecff") $color = "#aedcff";
			else $color = "#d2ecff";
			
        	echo '<tr height=25 
			
			title="Clique para abrir a mensagem"
            style=\'cursor:pointer;this.style.background="'.$color.'"\' bgcolor="'.$color.'">';
            
			// If the mail was not read,display it bold
			switch($dados->db_data["was_read"])
			{
				case MAIL_UNREAD:
					$style='font-weight:bolder;';
					$img="unread.gif";
					break;
				case MAIL_READ:
					$img="read.gif";
					break;
				case MAIL_REPLIED:
					$img="replied_mail.gif";
					break;
				case MAIL_FORWARDED:
					$img="forwarded_mail.gif";
					break;
			}			
			if($dados->db_data["folder"]=="sent") $img="sent.gif";
			elseif(substr_count($dados->db_data["subject"],"Return Receipt")) $img="recibo_mail.gif";            		
			?>
			<? if(!$emensagens){?><td style="width:10px; text-align:center;<?=$style?>" title="Clique para (des)seleccionar"><input type="checkbox" name="m_<?=$dados->db_data["ID"]?>" value="<?=$dados->db_data["ID"]?>"></td><? }?>
			<? if($emensagens){?><td class="line" style="<?=$style?>"onClick="javascript:window.open('viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>','_self')"><? echo htmlspecialchars(($dados->db_data["ID_From"]))?></td><? }?>
			<td class="line" style="<?=$style?>" onClick="javascript:window.open('viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>','_self')"><img src="graphics/<?=$img?>" alt="" align="left">&nbsp;<a href="viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>" target="_self"><?
			$m_nome="";
			$sql="SELECT * FROM logins where ID=".$dados->db_data["ID_From"];
			$hRes=$dados2->db_open($db_link,$DB_name,$sql);
			if(!$dados2->EOF()) $m_nome=$dados2->db_data["Nome"];
			$dados2->db_close();
			// Cut the name if it's too long
			if(strlen($m_nome) > 100){
				$m_nome=substr($m_nome,0,100)."...";
			}
			echo $m_nome;
			
			?></a></td><?
			if($folder=="sent"){ ?>
			<td class="line" style="<?=$style?>" onClick="javascript:window.open('viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>','_self')"><a href="viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>" target="_self"><?

			$enviado=$dados->db_data["Enviado"];
			$ArrayPalavras=explode(";",$enviado);
			$n_palavras=count($ArrayPalavras);
			if($n_palavras>5) $n_palavras=5;
			for($j=0;$j<$n_palavras;$j++){
				if($j>0)  echo ";";
				$sql="SELECT * FROM clientes where ID=".$ArrayPalavras[$j];
				$hRes=$dados2->db_open($db_link,$DB_name,$sql);
				if(!$dados2->EOF()) $m_nome=$dados2->db_data["Nome"];
				$dados2->db_close();				
				
				echo $m_nome;
			}
			if(count($ArrayPalavras)>$n_palavras) echo "<br>... (mais ".(count($ArrayPalavras)-$n_palavras)." destinatários)";
			?></a></td><? }?>
			
			<td class="line" style="<?=$style?>" onClick="javascript:window.open('viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>','_self')"><?
				$subject=($dados->db_data["Assunto"]);
				if(strlen($subject) > 100){$subject=substr($subject,0,100)."...";}			
				echo htmlspecialchars($subject);
			?></td>
			<td class="line" style="<?=$style?>" onClick="javascript:window.open('viewmsg.php?emensagens=<?=$emensagens?>&mail_id=<?=$dados->db_data["ID"]?>&sort=<?=$sort?>&order=<?=$order?>&user_id=<?=$user_id?>&folder=<?=$folder?>&limit=<?=$limit?>','_self')"><?
				$date=mktime(substr($dados->db_data["Data"],8,2),substr($dados->db_data["Data"],10,2),substr($dados->db_data["Data"],12,2),substr($dados->db_data["Data"],4,2),substr($dados->db_data["Data"],6,2),substr($dados->db_data["Data"],0,4));
				$formatted_date=strftime("%x %X",$date);
				echo $formatted_date;?>
            </td>
			</tr>
			<tr><td  style="<?=$style?>height:1px;"></td><td  style="height:1px;"></td><td  style="height:1px;"></td><td  style="height:1px;"></td><? if($folder=="sent") echo '<td  style="height:1px;"></td>';?></tr>
            <?
			$dados->db_next();
			$limit++;
		}
		$dados->db_close();
		?>
</form>
		</table><? }
	?>
	
	<table width=100% border=0 cellspacing=0>
		<tr><td height="30"  align="center" valign="middle"><?
		//ss_paginar = total de emails por pagina
		//from = a partir de
		if($ss_paginar){
			$hRes=$dados->db_open($db_link,$DB_name,$sql2);
			if(!$dados->EOF()){
				if($order=="Desc") $order="Asc";
				else $order="Desc";

				$total_pag=ceil($dados->db_rows/$ss_paginar);				
				echo "&nbsp;Páginas:&nbsp;&nbsp;";
		
				$start=$bgn-($num*5);
				if($start<10) $start=0;
				
				$pagina=(($bgn/$num)-4);
				if($pagina<1) $pagina=1;
				
				if($total_pag>($pagina+10)) $total_pag_tmp=$pagina+10;
				else $total_pag_tmp=$total_pag;
				if($pagina>1) echo "<a  href=\"list.php?folder=".$folder."&order=".$order."&limitar=".$limitar."&num=".$num."&sort=".$sort."\">0</a>&nbsp;&nbsp;...";
				
				for($k=$pagina;$k<=$total_pag_tmp;$k++){
					if($bgn<>$start){
						echo "<a href='list.php?folder=".$folder."&order=".$order."&limitar=".$limitar."&num=".$num."&bgn=".$start."&sort=".$sort."' title='Página ".$k."'>".$k."</a>&nbsp;&nbsp;";
					} else {
						echo '<font class="red"><b>'.$k.'</b></font>&nbsp;&nbsp;';
					}
					$start+=$num;
				}
				if($total_pag>$total_pag_tmp) echo "<a href='list.php?folder=".$folder."&order=".$order."&limitar=".$limitar."&num=".$num."&bgn=".(($total_pag*10)-10)."&sort=$sort' title='Página ".$total_pag."'>".$total_pag."</a>&nbsp;&nbsp;...";			
			}
			$dados->db_close();
		}
		?></td></tr>
	</table>	
</body>
</html>