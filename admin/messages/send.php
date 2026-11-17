<?
	$path="../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	include "funcoes.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome,$user_nome,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$color_top="#f1f4f6";
	include "../layout/header.inc.php";
?>
<head>
<style>
html body{ background-color:#FFF; background-image:none}
</style>
<script type="text/javascript" charset="utf-8">
		function selectAll(chkObj,tp)
		{
			var multi=document.getElementById(chkObj);
			if(tp==1)
			{
				for(i=1;i<multi.options.length;i++)
				{
					multi.options[i].selected = true;
				}
			}
			else
			{
				for(i=1;i<multi.options.length;i++)
				{
					multi.options[i].selected = false;
				}
			}
		}
	</script>
</head>
<body bgcolor="#ffffff">
<td valign=top>
 	<table width="100%" height=31 bgcolor="#3399FF" border=0 cellspacing=0 cellpadding=0>
		<tr height=31>
			<td width=20 style="text-align:right; vertical-align:middle; padding-top:4px">&nbsp;</td>
			<td class="branco" valign="center">&nbsp;<b>Nova mensagem</b></td>
		</tr>
	</table>
<?php

$id_from=0;
if($tos && $mail_id)
{
	$sql="SELECT * FROM mensagens WHERE ID=$mail_id"; echo "1-".$sql."<br>";
	$dados1=new DataObj;
	$hRes=$dados1->db_open($db_link,$DB_name,$sql);
	if(!$dados1->EOF())
	{
		$msg=$dados1->db_data["Mensagem"];
		$id_from=$dados1->db_data["ID_From"];
		$assunto="Re: ".$dados1->db_data["Assunto"];
	}
	$dados1->db_close();
}
if(isset($_POST['nova']) && $_POST['nova']==1)
{
	$user_id=$admin_id;
	$data=date("Y-m-d H:i:s");
	$mensagem=nl2br($_POST['mensagem']);
	$assunto=$_POST['assunto'];
	$dados1=new DataObj;
	$rs=new DataObj;
	$sql="SELECT * FROM logins ORDER BY username"; //echo "2-".$sql."<br>";
	$hRes=$dados1->db_open($db_link,$DB_name,$sql);
	$enviado="";

	$interno = $_POST["id_interno"];
	$logins = $_POST["id_login"];
	$grupos = $_POST["id_grupo"];
	$teams = $_POST["id_teams"];

	$datax=substr($data,0,10);
	if($mensagem!="" && $assunto!="")
		{
		// Interno
		$enviado = "";
		if(isset($interno))
		{
			for($a=0;$a<count($interno);$a++)
			{
				$id_cliente = $interno[$a];
				if($id_cliente!='')
				{
					$sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1)"; //echo "3-".$sql."<br>";
					// echo $sql."<br>";
					$result=Runquery($db_link,$DB_name,$sql);

					if($enviado) $enviado .= ";" . $id_cliente;
					else $enviado = $id_cliente;
				}
			}

			if($enviado)
			{
				$sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova,folder,Enviado) Values ($user_id,0,'$data','$assunto','$mensagem',0,1,'sent','$enviado')"; //echo "4-".$sql."<br>";
				// echo $sql."<br>";
				$result=Runquery($db_link,$DB_name,$sql);
				// if(!$result)	echo db_error($hConn);
			}
		}
		// Logins
		$enviado = "";
		if(isset($logins))
		{
			for($a=0;$a<count($logins);$a++)
			{
				$id_cliente = $logins[$a];
				if($id_cliente!='')
				{
					// $sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1)"; //echo "3-".$sql."<br>";
					// echo $sql."<br>";
					// $result=Runquery($db_link,$DB_name,$sql);

					// $sql = "INSERT INTO players_msg (ID_From,ID_To,Data,Assunto,Mensagem,Estado,Nova,Folder) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1,'inbox')"; //echo "3-".$sql."<br>";
					// $result = Runquery($db_link,$DB_name,$sql);

					if($enviado) $enviado .= ";" . $id_cliente;
					else $enviado = $id_cliente;

					$sql="SELECT * from clientes where ID='$id_cliente'";
					$rs->db_open($db_link,$DB_name,$sql);
					if(!$rs->EOF())$Email=$rs->db_data["Email"];
					$rs->db_close();



					if($mensagem)
					{
						$message=$mensagem;
						//echo "A enviar mensagem para:".$Email;
						send_email($Email,$assunto,$message,"",1);
					}
				}
			}

			if($enviado)
			{
				$sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova,folder,Enviado) Values ($user_id,0,'$data','$assunto','$mensagem',0,1,'sent','$enviado')"; //echo "4-".$sql."<br>";
				// echo $sql."<br>";
				$result=Runquery($db_link,$DB_name,$sql);
				// if(!$result)	echo db_error($hConn);
			}
		}
		// Grupos
		$enviado = "";
		if(isset($grupos))
		{
			for($a=0;$a<count($grupos);$a++)
			{
				$dados1=new DataObj;
				$sql="SELECT ID,Email FROM clientes WHERE Grupo_ID=".$grupos[$a]." Group by ID";
				// echo $sql."<br>";
				$dados1->db_open($db_link,$DB_name,$sql);
				while(!$dados1->EOF())
				{
					$id_cliente = $dados1->db_data["ID"];
					$Email=$rs->db_data["Email"];
					if($id_cliente!='')
					{
						// $sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1)"; //echo "3-".$sql."<br>";
						// $result = Runquery($db_link,$DB_name,$sql);

						// $sql = "INSERT INTO players_msg (ID_From,ID_To,Data,Assunto,Mensagem,Estado,Nova,Folder) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1,'inbox')"; //echo "3-".$sql."<br>";
						// $result = Runquery($db_link,$DB_name,$sql);

						if($mensagem)
						{
							$message=$mensagem;
							//echo "A enviar mensagem para:".$Email;
							send_email($Email,$assunto,$message,"",1);
						}

						if($enviado) $enviado .= ";" . $id_cliente;
						else $enviado = $id_cliente;
					}
					$dados1->db_next();
				}
				$dados1->db_close();
			}

			if($enviado)
			{
				$sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova,folder,Enviado) Values ($user_id,0,'$data','$assunto','$mensagem',0,1,'sent','$enviado')"; //echo "4-".$sql."<br>";
				// echo $sql."<br>";
				$result=Runquery($db_link,$DB_name,$sql);
				if(!$result)	echo db_error($hConn);
			}
		}
		// Teams
		// $enviado = "";
		// if(isset($teams))
		// {
		// 	for($a=0;$a<count($teams);$a++)
		// 	{
		// 		$dados1=new DataObj;
		// 		$sql="SELECT ID_Player as ID FROM teams_players WHERE ID_Team=".$teams[$a]." Group by ID_Player";
		// 		// echo $sql."<br>";
		// 		$dados1->db_open($db_link,$DB_name,$sql);
		// 		while(!$dados1->EOF())
		// 		{
		// 			$id_cliente = $dados1->db_data["ID"];
		// 			if($id_cliente!='')
		// 			{
		// 				// $sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1)"; //echo "3-".$sql."<br>";
		// 				// $result = Runquery($db_link,$DB_name,$sql);

		// 				$sql = "INSERT INTO players_msg (ID_From,ID_To,Data,Assunto,Mensagem,Estado,Nova,Folder) Values ($user_id,".$id_cliente.",'$data','$assunto','$mensagem',0,1,'inbox')"; //echo "3-".$sql."<br>";
		// 				$result = Runquery($db_link,$DB_name,$sql);

		// 				if($enviado) $enviado .= ";" . $id_cliente;
		// 				else $enviado = $id_cliente;
		// 			}
		// 			$dados1->db_next();
		// 		}
		// 		$dados1->db_close();
		// 	}

		// 	if($enviado)
		// 	{
		// 		$sql = "INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova,folder,Enviado) Values ($user_id,0,'$data','$assunto','$mensagem',0,1,'sent','$enviado')"; //echo "4-".$sql."<br>";
		// 		// echo $sql."<br>";
		// 		$result=Runquery($db_link,$DB_name,$sql);
		// 		if(!$result)	echo db_error($hConn);
		// 	}
		// }
		// if()
		/*
	  	while(!$dados1->EOF())
		{
	    	$campo = "id_cliente".$dados1->db_data["ID"];
		    if($$campo==1)
			{
				$datax=substr($data,0,10);
				//post_message($dados1->db_data["ID"],$datax." 00:00:00","O utilizador ".$user_nome." enviou-lhe a seguinte mensagem:<br>".$assunto,$mensagem,"msn");

				$sql="INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova) Values ($user_id,".$dados1->db_data["ID"].",'$data','$assunto','$mensagem',0,1)"; //echo "3-".$sql."<br>";
				// $result=Runquery($db_link,$DB_name,$sql);
	    		// if(!$result) echo db_error($hConn);

				if($enviado) $enviado.=";".$dados1->db_data["ID"];
				else $enviado=$dados1->db_data["ID"];

		    }
		    $dados1->db_next();
	  	}
	  	$dados1->db_close();
		if($enviado)
		{
			$sql="INSERT INTO mensagens (ID_From,ID_Cliente,Data,Assunto,Mensagem,Estado,Nova,folder,Enviado) Values ($user_id,0,'$data','$assunto','$mensagem',0,1,'sent','$enviado')"; //echo "4-".$sql."<br>";
	    	// $result=Runquery($db_link,$DB_name,$sql);
	    	// if(!$result)	echo db_error($hConn);
		}*/
		echo '<table width="100%" align="center" bgcolor="#f1f4f6" border=0 cellpadding=0 cellspacing=0>
	          <tr><td>&nbsp;</td></tr>
	          <tr>
	            <td class="preto">&nbsp;&nbsp;Mensagem enviada com sucesso.</td>
	          </tr>
	          <tr><td>&nbsp;</td></tr>
	        </table>';
	 }else{
	 	echo '<table width="100%" align="center" bgcolor="#f1f4f6" border=0 cellpadding=0 cellspacing=0>
	          <tr><td>&nbsp;</td></tr>
	          <tr>
	            <td class="preto">&nbsp;&nbsp;Mensagem não foi enviada por falta de informação.</td>
	          </tr>
	          <tr><td>&nbsp;</td></tr>
	        </table>';
	 }
}
else
{
?>
  <form action="send.php" method="post" name="sugira" onSubmit="return teste()">
  <input name="nova" type="hidden" id="nova" value="1">
  <table width="100%" align="center" bgcolor="#f1f4f6" border=0 cellpadding=1 cellspacing=0>
    <tr><td colspan="4" class=preto>&nbsp;</td></tr>
    <tr>
      <td>&nbsp;</td>
      <td width="100" align="right" valign="top" class="preto"> Destinatários : </td>
      <td valign="top" class="preto">

        <table border=0 cellspacing=0 cellpadding="0" width="100%">
          <tr>
		  <td height="20" style="font-size:13px;color:#000"><b>Interno:<a href="javascript:;" onClick="selectAll('id_interno',1)" class="preto" style="text-decoration:underline">Todos</a> / <a href="javascript:;" onClick="selectAll('id_interno',0)" class="preto" style="text-decoration:underline">Nenhum</a></b></td><td style="font-size:13px;color:#000"><b>Utilizadores:<a href="javascript:;" onClick="selectAll('id_login',1)" class="preto" style="text-decoration:underline">Todos</a> / <a href="javascript:;" onClick="selectAll('id_login',0)" class="preto" style="text-decoration:underline">Nenhum</a></b></td><td style="font-size:13px;color:#000"><b>Grupos:<a href="javascript:;" onClick="selectAll('id_grupo',1)" class="preto" style="text-decoration:underline">Todos</a> / <a href="javascript:;" onClick="selectAll('id_grupo',0)" class="preto" style="text-decoration:underline">Nenhum</a></b></td><!--td style="font-size:13px;color:#000"><b>Teams:<a href="javascript:;" onClick="selectAll('id_teams',1)" class="preto" style="text-decoration:underline">Todos</a> / <a href="javascript:;" onClick="selectAll('id_teams',0)" class="preto" style="text-decoration:underline">Nenhum</a></b></td-->
		  </tr>
		  <tr>
		  <td valign="top" class="preto">
			<select name="id_interno[]" id="id_interno" size="10" multiple="multiple"  style="width:200px;height:120px;">
			<option value="" selected="true">Nenhum</option>
		<?
		$dados1=new DataObj;
		$sql="SELECT * FROM logins WHERE Estado=1 ORDER BY username";
		$dados1->db_open($db_link,$DB_name,$sql);
		$n=1;
		while(!$dados1->EOF())
		{
			$ID=$dados1->db_data["ID"];
			$Nome=$dados1->db_data["Nome"];
			$Acesso=$dados1->db_data["Acesso"]; // 2009-12-07 20:25:46

			// if($timeInMinutes2<=$timeInMinutes)	echo "<b>".$Nome."</b>";
			// else echo $Nome;

			echo '<option value="'.$ID.'">'.$Nome.'</option>';

			$dados1->db_next();
			$n++;
		}
		$dados1->db_close();
		?>
		</select>
            </td>
            <td valign="top" class="preto">
			<select name="id_login[]" id="id_login" size="10" multiple="multiple" style="width:200px;height:120px;">
			<option value="" selected="true">Nenhum</option>
		<?
		$dados1=new DataObj;
		$sql="SELECT * FROM clientes WHERE Estado=1 ORDER BY username";
		$dados1->db_open($db_link,$DB_name,$sql);
		$n=1;
		while(!$dados1->EOF())
		{
			$ID=$dados1->db_data["ID"];
			$Nome=$dados1->db_data["Nome"];
			$Apelido=$dados1->db_data["Apelido"];
			$Acesso=$dados1->db_data["Acesso"]; // 2009-12-07 20:25:46

			// if($timeInMinutes2<=$timeInMinutes)	echo "<b>".$Nome."</b>";
			// else echo $Nome;

			echo '<option value="'.$ID.'">'.$Nome.' '.$Apelido.'</option>';

			$dados1->db_next();
			$n++;
		}
		$dados1->db_close();
		?>
		</select>
            </td>

            <td valign="top" class="preto">
		<select name="id_grupo[]" id="id_grupo" size="10" multiple="multiple" style="width:200px;height:120px;">
		<option value="" selected="true">Nenhum</option>
		<?
		$dados1=new DataObj;
		$sql="SELECT * FROM grupos_clientes";
		$dados1->db_open($db_link,$DB_name,$sql);
		$n=1;
		while(!$dados1->EOF())
		{
			$ID = $dados1->db_data["ID"];
			$Nome = $dados1->db_data["Descricao"];

			echo '<option value="'.$ID.'">'.$Nome.'</option>';

			$dados1->db_next();
			$n++;
		}
		$dados1->db_close();
		?>
		</select>
            </td>

            <!--td valign="top" class="preto">
			<select name="id_teams[]" id="id_teams" size="10" multiple="multiple" style="height:120px;">
			<option value="" selected="true">Nenhum</option>
			<?/*
    		$dados1=new DataObj;
    		$sql="SELECT * FROM teams where Visivel=1";
        	$dados1->db_open($db_link,$DB_name,$sql);
	        $n=1;
    	    while(!$dados1->EOF())
			{
	        	$ID = $dados1->db_data["ID"];
				$Nome=$dados1->db_data["Team"];

				echo '<option value="'.$ID.'">'.$Nome.'</option>';

				$dados1->db_next();
				$n++;
			}
			$dados1->db_close();*/
			?>
			</select>
            </td-->
          </tr>
        </table>
      </td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
      <td width="100" align="right" valign="top" class="preto"> Assunto : </td>
      <td class="preto" align="left"><input name="assunto" id="assunto" style="WIDTH:100%; height:20px" maxlength="254" value="<?=$assunto;?>"></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td>&nbsp;</td>
        <td width="100" align="right" valign="top" class="preto">Mensagem : </td>
        <td class="preto" align="left"><textarea name="mensagem" id="mensagem" style="WIDTH: 100%; HEIGHT: 150px" class="mceEditor"><? if($tos && $mail_id) echo $msg;?></textarea></td>
      <td>&nbsp;</td>
    </tr>
    <tr>
      <td height="10" width="10"></td>
      <td width="100"></td>
      <td></td>
      <td width="4"></td>
    </tr>
    <tr>
      <td width="10"></td>
      <td width="100"></td>
      <td valign="top" align="center"><input style="width:200px" type="submit" name="Submit" class="btn btn-primary" value=" enviar "></td>
      <td width="4"></td>
    </tr>
    <tr>
      <td height="10" width="10"></td>
      <td width="100"></td>
      <td></td>
      <td width="4"></td>
    </tr>
  </table>
</form>
<?
  db_closedb_conection($hConn);
}
?>
</td>
</body>
</html>