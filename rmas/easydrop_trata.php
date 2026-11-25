<?
	$path='../core/';
	include $path.'core_files.php';
	include $path.'admin_globals.php';
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;	
	$strURL="../files/cache";
	if(!is_dir($strURL)) mkdir($strURL, 0777);	
	$origem=$strURL;
	$n_images=0;
	if($n_files>0) $only_images=1;
	for($w=1;$w<=$n_files;$w++)
	{
		$select="select_".$w;
		$select=$$select;
		$file="file".$w;
		$file=$$file;
		$file_name="file_".$w;
		$file_name=$$file_name;
		$tipo="tipo".$w;
		$tipo=$$tipo;
		$ext_fich = "";
		$pos = strrpos($file, ".");
		$ext_fich = substr($file,$pos + 1, strlen($file) + 1 - $pos);
		if($select==1)
		{
			$n_images++;
			if($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG") $only_images=1;
			else $only_images=0;
		}	
	}
	if($only_images && !$tema && $n_images>1)
	{
		include "layout/header.inc.php";	
		include "layout/body.inc.php";
	?>
			<div class="center">
            <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                
                
                
                <table border=0 cellspacing=0 cellpadding=0 class="new_quadros"><tr><td>
			<font color="#FF0000"><?=$msg?></font>
			<form action="easydrop_trata.php" method="post" name="dropandgo"><input type="hidden" name="n_files" value="<?=$n_files?>">
			<?
			for($w=1;$w<=$n_files;$w++)
			{
				$select="select_".$w;
				$file="file".$w;
				$file_name="file_".$w;
				$tipo="tipo".$w;
				$ext_fich="";
				$pos=strrpos($$file,".");
				$ext_fich=substr($$file,$pos+1,strlen($$file)+1-$pos);

				if($$select==1)
				{
					if($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
					{
						echo '<input type="hidden" name="'.$file.'" value="'.$$file.'">';
						echo '<input type="hidden" name="'.$file_name.'" value="'.$$file_name.'">';
						echo '<input type="hidden" name="'.$select.'" value="'.$$select.'">';
						echo '<input type="hidden" name="'.$tipo.'" value="'.$$tipo.'">';						
					}
				}	
			}?>
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="t2">
				<tr> 
				<td width="10" height="10"></td>
				<td class="tema_ed">Easy drop process - choose a theme</td>
				</tr>
				</table>
				<table class="t3" border=0 cellpadding=0 cellspacing=0><tr><td valign="top">
				<tr>
				<td>
					<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
					<tr>
						<td width=29 align="center"><input type="checkbox" name="tema" id="tema" value="1" class="cb"></td>
						<td width="5px" align="center"><img src="images/s2.jpg"></td>
						<td width="10">&nbsp;</td>
						<td>Theme 1: one image per page</td>
					</tr><? if($n_images>1 && $n_images<3){?>
					<tr>
						<td width=29 align="center"><input type="checkbox" name="tema" id="tema" value="2" class="cb"></td>
						<td width="5px" align="center"><img src="images/s2.jpg"></td>
						<td width="10">&nbsp;</td>
						<td>Theme 2: two image on the same page side by side</td>
					</tr>
					<tr>
						<td width=29 align="center"><input type="checkbox" name="tema" id="tema" value="3" class="cb"></td>
						<td width="5px" align="center"><img src="images/s2.jpg"></td>
						<td width="10">&nbsp;</td>
						<td>Theme 3: two image on the same page one above and another below</td>
					</tr><? }?>
					<tr>
						<td height=25 colspan=4>&nbsp;<input type="submit" value="execute action"></td>
					</tr>
					</table>
				</td>
				</tr>
				</table>
			</form>
			</td></tr></table>
		<img src="images/bsh.png" width="100%">
        
        </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"></td>
            </tr>
            </table>
	</div>
    <? 	include "../layout/bottom.inc.php";?><?
	}
	elseif($tema && $n_images>1)
	{
		$i=0;
		for($w=1;$w<=$n_files;$w++)
		{
			$select="select_".$w;
			$file="file".$w;
			$file_name="file_".$w;
			$tipo="tipo".$w;
			$ext_fich="";
			$pos=strrpos($$file,".");
			$ext_fich=substr($$file,$pos+1,strlen($$file)+1-$pos);

			if($$select==1)
			{
				if($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
				{
					$array_images[$i][0]=$$file;
					$array_images[$i][1]=$$file_name;
					$array_images[$i][2]=$$select;
					$array_images[$i][3]=$$tipo;
					$i++;
				}
			}	
		}
		if($tema==1)
		{
			for($j=0;$j<$i;$j++)
			{
				$select=$array_images[$j][2];
				$file=$array_images[$j][0];
				$file_name=$array_images[$j][1];
				$tipo=$array_images[$j][3];
			
				$fx_s=$strURL."/".$file;
				$newURL=str_replace("/cache", "",$strURL);
				$fx_d=$newURL."/".$file;
				copy($fx_s,$fx_d);
				$fx_d=str_replace("../", "../../",$fx_d);
				$contents='<img src="'.$fx_d.'">';
				
				$data=date("Y-m-d");
				$sql="insert into pages(ID_ORG,Data) values (0,'$data')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo GetError($db_link).$sql;
				$id_pai=db_get_last_insert_id($db_link);
	
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Lang) values ('$id_pai','".$file_name."','$contents','pt')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo GetError($db_link).$sql;
				unlink($fx_s);
			}
		}
		if($tema==2)
		{
			$j=0;
			$select=$array_images[$j][2];
			$file=$array_images[$j][0];
			$file_name=$array_images[$j][1];
			$tipo=$array_images[$j][3];
			$fx_s=$strURL."/".$file;
			$newURL=str_replace("/cache", "",$strURL);
			$fx_d=$newURL."/".$file;
			copy($fx_s,$fx_d);
			unlink($fx_s);
			$fx_d=str_replace("../", "../../",$fx_d);
			$contents='<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td><img src="'.$fx_d.'">';
			$j++;
			$select=$array_images[$j][2];
			$file=$array_images[$j][0];
			$tipo=$array_images[$j][3];
			$fx_s=$strURL."/".$file;
			$newURL=str_replace("/cache", "",$strURL);
			$fx_d=$newURL."/".$file;
			copy($fx_s,$fx_d);
			unlink($fx_s);
			$fx_d=str_replace("../", "../../",$fx_d);
			$contents.='</td><td><img src="'.$fx_d.'"></td></tr></table>';
				
			$data=date("Y-m-d");
			$sql="insert into pages(ID_ORG,Data) values (0,'$data')";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res) echo GetError($db_link).$sql;
			$id_pai=db_get_last_insert_id($db_link);
	
			$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Lang) values ('$id_pai','".$file_name."','$contents','pt')";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res) echo GetError($db_link).$sql;
				
		}
		if($tema==3)
		{
			$j=0;
			$select=$array_images[$j][2];
			$file=$array_images[$j][0];
			$file_name=$array_images[$j][1];
			$tipo=$array_images[$j][3];
			$fx_s=$strURL."/".$file;
			$newURL=str_replace("/cache", "",$strURL);
			$fx_d=$newURL."/".$file;
			copy($fx_s,$fx_d);
			unlink($fx_s);
			$fx_d=str_replace("../", "../../",$fx_d);
			$contents='<table width=100% border=0 cellspacing=0 cellpadding=0><tr><td><img src="'.$fx_d.'">';
			$j++;
			$select=$array_images[$j][2];
			$file=$array_images[$j][0];
			$tipo=$array_images[$j][3];
			$fx_s=$strURL."/".$file;
			$newURL=str_replace("/cache", "",$strURL);
			$fx_d=$newURL."/".$file;
			copy($fx_s,$fx_d);
			unlink($fx_s);
			$fx_d=str_replace("../", "../../",$fx_d);
			$contents.='</td></tr><tr><td><img src="'.$fx_d.'"></td></tr></table>';
				
			$data=date("Y-m-d");
			$sql="insert into pages(ID_ORG,Data) values (0,'$data')";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res) echo GetError($db_link).$sql;
			$id_pai=db_get_last_insert_id($db_link);
	
			$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Lang) values ('$id_pai','".$file_name."','$contents','pt')";
			$res=Runquery($db_link,$DB_name,$sql);
			if(!$res) echo GetError($db_link).$sql;
				
		}
		include "layout/header.inc.php";	
		include "layout/body.inc.php";
		?>
			<div class="center">
            <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                
                
            <table border=0 cellspacing=0 cellpadding=0 class="new_quadros"><tr><td>
			<font color="#FF0000"><?=$msg?></font>
			<form action="easydrop_trata.php" method="post" name="dropandgo"><input type="hidden" name="n_files" value="<?=$n_files?>">
			<?
			for($w=1;$w<=$n_files;$w++)
			{
				$select="select_".$w;
				$file="file".$w;
				$file_name="file_".$w;
				$tipo="tipo".$w;
				$ext_fich="";
				$pos=strrpos($$file,".");
				$ext_fich=substr($$file,$pos+1,strlen($$file)+1-$pos);

				if($$select==1)
				{
					if($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
					{
						echo '<input type="hidden" name="'.$file.'" value="'.$$file.'">';
						echo '<input type="hidden" name="'.$file_name.'" value="'.$$file_name.'">';
						echo '<input type="hidden" name="'.$select.'" value="'.$$select.'">';
						echo '<input type="hidden" name="'.$tipo.'" value="'.$$tipo.'">';						
					}
				}	
			}?>
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="t2">
				<tr> 
				<td width="10" height="10"></td>
				<td class="tema_ed">Easy drop process - choose a theme</td>
				</tr>
				</table>
				<table class="t3" border=0 cellpadding=0 cellspacing=0>
				<tr>
				<td align=center height=50>
					Your pages were created
				</td>
				</tr>
				<tr>
						<td height=25>&nbsp;<input type="button" onClick="window.open('easydrop.php','_self')" value="Back to Easy drop">&nbsp;<input type="button" onClick="window.open('pages/list.php','_self')" value="Go to public pages"></td>
				</tr>
				</table>
			</form>
			</td></tr></table>
			<img src="images/bsh.png" width="100%">
            </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"></td>
            </tr>
            </table>
	</div>
    <? 	include "../layout/bottom.inc.php";?><?
	}
	else
	{
		for($w=1;$w<=$n_files;$w++)
		{
			$select="select_".$w;
			$select=$$select;
			$file="file".$w;
			$file=$$file;
			$file_name="file_".$w;
			$file_name=$$file_name;
			$tipo="tipo".$w;
			$tipo=$$tipo;
			$ext_fich = "";
			$pos = strrpos($file, ".");
			$ext_fich = substr($file,$pos + 1, strlen($file) + 1 - $pos);
			
			$seleccionado=0;
			if($tipos_fich==1 || $select==1)$seleccionado=1;
			elseif($tipos_fich==2 && ($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG"))$seleccionado=1;
			elseif($tipos_fich==3 && ($ext_fich=="html" || $ext_fich=="htm" || $ext_fich=="HTML" || $ext_fich=="HTM" || $ext_fich=="asp"))$seleccionado=1;
			elseif($tipos_fich==4 && ($ext_fich=="csv" || $ext_fich=="CSV"))$seleccionado=1;
	
			if($seleccionado==1)
			{
				//echo " processa-";
				if($accao==1)
				{
					//echo " --> MOVER P files";
					
					$fx_s=$strURL."/".$file;
					$newURL=str_replace("/cache", "",$strURL);
					$fx_d=$newURL."/".$file;
					if(copy($fx_s,$fx_d))
					{
						//echo " SUCESSO!";
						unlink($fx_s);
					}
					else
						echo " ERROR!";
				}
				elseif($accao==2)
				{
					//echo " --> APAGAR";
					$fx_s=$strURL."/".$file;
					unlink($fx_s);
				}
				elseif($accao==3)
				{
					//echo " --> CONVERTER EM PÁGINA PÚBLICA";
					$fx2=$strURL."/".$file;
	
					if($ext_fich=="html" || $ext_fich=="htm" || $ext_fich=="HTML" || $ext_fich=="HTM" || $ext_fich=="txt" || $ext_fich=="TXT")
					{
						$contents = file_get_contents($fx2);
						$contents=mb_convert_encoding($contents, 'ISO-8859-1');
						$data=date("Y-m-d");
						$sql="insert into pages(ID_ORG,Data) values (0,'$data')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						$id_pai=db_get_last_insert_id($db_link);
						$contents=strip_only($contents, array('script','noframes','frames','input','form','iframe'));						
						$contents=addslashes(stripslashes($contents));
						if($ext_fich=="txt" || $ext_fich=="TXT")$contents=str_replace("\n","\n<br>",$contents);
						$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Lang) values ('$id_pai','".$file_name."','$contents','pt')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						unlink($fx2);
					}
					elseif($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
					{
						$fx_s=$strURL."/".$file;
						$newURL=str_replace("/cache", "",$strURL);
						$fx_d=$newURL."/".$file;
						copy($fx_s,$fx_d);
						$fx_d=str_replace("../", "../../",$fx_d);
						$contents='<img src="'.$fx_d.'" />';
						
						$data=date("Y-m-d");
						$sql="insert into pages(ID_ORG,Data) values (0,'$data')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						$id_pai=db_get_last_insert_id($db_link);
	
						$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Lang) values ('$id_pai','".$file_name."','$contents','pt')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						unlink($fx_s);
						
					}
					else
						$msg.="<br />Convert to Public Page: Only available for HTML files or images";
				}
				elseif($accao==4)
				{
					//echo " --> CONVERTER EM BANNER";
					$fx2=$strURL."/".$file;
					if($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
					{
						$fx_s=$strURL."/".$file;
						// criar banner
						list($largura,$altura,$type,$attr)=getimagesize($fx_s);
						//      busca um tipo de banner
						$result=new DataObj;
						$result->db_open($db_link,$DB_name, "SELECT * FROM media_type ORDER BY ID ASC LIMIT 1");
						if(!$result->EOF())
						{
							$tipo=$result->db_data["ID"];
						}
						$data1=date("Y-m-d");
						$data2=strtotime('+1 month');
						$data2=date("Y-m-d",$data2);
						$sql="insert into media(Name,img_ext,Largura,Altura,Tipo,Data1,Data2,Visible) values ('$file_name','$ext_fich','$largura','$altura','$tipo','$data1','$data2','1')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						$id_banner=db_get_last_insert_id($db_link);
						
						// processar imagem
						$newURL=str_replace("/cache", "/media",$strURL);
						$fx_d=$newURL."/".$id_banner."_1.".$ext_fich;
						copy($fx_s,$fx_d);
						unlink($fx_s);
	
					}
					else
						$msg.="<br />Convert Banner: Only available for Images";
				}
				elseif($accao==5)
				{
					//echo " --> CONVERTER EM NOTICIA";
					$fx2=$strURL."/".$file;
					if($ext_fich=="html" || $ext_fich=="htm" || $ext_fich=="HTML" || $ext_fich=="HTM")
					{
						$contents = file_get_contents($fx2);
						$contents=mb_convert_encoding($contents, 'ISO-8859-1');
	
						//      busca uma familia
						$result=new DataObj;
						$result->db_open($db_link,$DB_name, "SELECT * FROM catalogo_noticias_familias ORDER BY ID ASC LIMIT 1");
						if(!$result->EOF())
						{
							$id_fam=$result->db_data["ID"];
						}
						$data1=date("Y-m-d");
						$data2=strtotime('+1 month');
						$data2=date("Y-m-d",$data2);
						$sql="INSERT INTO catalogo_noticias (ID_ORG,Visivel,Data1,Data2) VALUES ('$id_fam','0','$data1','$data2')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						$id_pai=db_get_last_insert_id($db_link);
	
						$contents=strip_only($contents, array('script','noframes','frames','input','form','iframe'));
						$contents=addslashes(stripslashes($contents));
	
						$sql="INSERT INTO catalogo_noticias_linguas (ID_PAI,Cabecalho,Texto,Lang) VALUES ('$id_pai','".$file_name."','$contents','pt')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						unlink($fx2);
					}
					elseif($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
					{
						$fx_s=$strURL."/".$file;
						$newURL=str_replace("/cache", "/",$strURL);
						$fx_d=$newURL."/".$file;
						copy($fx_s,$fx_d);
						$fx_d=str_replace("../", "../../",$fx_d);
						$contents='<img src="'.$fx_d.'">';
						
						//      busca uma familia
						$result=new DataObj;
						$result->db_open($db_link,$DB_name, "SELECT * FROM catalogo_noticias_familias ORDER BY ID ASC LIMIT 1");
						if(!$result->EOF())
						{
							$id_fam=$result->db_data["ID"];
						}
						$data1=date("Y-m-d");
						$data2=strtotime('+1 month');
						$data2=date("Y-m-d",$data2);
						$sql="INSERT INTO catalogo_noticias (ID_ORG,Visivel,img_ext,Data1,Data2) VALUES ('$id_fam','0','$ext_fich','$data1','$data2')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						$id_pai=db_get_last_insert_id($db_link);
						
						$newURL=str_replace("/cache", "/catalogo_noticias",$strURL);
						$fx_d=$newURL."/".$id_pai."_1.".$ext_fich;
						copy($fx_s,$fx_d);
	
						$sql="INSERT INTO catalogo_noticias_linguas (ID_PAI,Cabecalho,Texto,Lang) VALUES ('$id_pai','".$file_name."','$contents','pt')";
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						unlink($fx_s);
	
					}
					else
						$msg.="<br />Convert News: Only available for HTML files or images";
				}
				elseif($accao==6)
				{
					//echo " --> IMPORTAR EMAILS";
					$fx2=$strURL."/".$file;
					if($ext_fich=="csv" || $ext_fich=="CSV")
					{
						$fp=fopen($fx2,"r");
						while($data=fgetcsv($fp,1000,","))
						{
							$i=0;
							$j=count($data);
							while($i<$j){echo $data[$i];$i++;}
						}
						fclose($fx2);
						if(!$j)
						{
							$fp=fopen($fx2,"r");
							while($data=fgetcsv($fp,1000,";"))
							{
								$i=0;
								$j=count($data);
								while($i<$j){echo $data[$i];$i++;}
							}
						}					
						//unlink($fx2);
					}
					else
						$msg.="<br />Import Your Mail: Only available for CSV files";
				}
				elseif($accao==7)
				{
					//echo " --> ALTERAR PÁGINA PÚBLICA";
					$fx2=$strURL."/".$file;
	
					if($ext_fich=="html" || $ext_fich=="htm" || $ext_fich=="HTML" || $ext_fich=="HTM")
					{
						$contents=file_get_contents($fx2);
						$contents=mb_convert_encoding($contents, 'ISO-8859-1');
	
						$data=date("Y-m-d");
						$contents=strip_only($contents, array('script','noframes','frames','input','form','iframe'));
						$contents=addslashes(stripslashes($contents));
						$sql="UPDATE pages_lang set Texto='$contents' WHERE Cabecalho='".$file_name."'";
						
						$res=Runquery($db_link,$DB_name,$sql);
						if(!$res) echo GetError($db_link).$sql;
						unlink($fx2);
					}
					else
						$msg.="<br />Convert to Public Page: Only available for HTML files or images";
				}
			}
			//exit;
		}
		///////////////////////////////////////////////////////////////////////////////////////////////////////////
		
		if($dh=opendir($strURL)) 
		{
			include "layout/header.inc.php";	
			include "layout/body.inc.php";?>
			<script>function makeSelection(campo,obj)
			{
				//nenhum
				if(obj.value==0)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_") eval("document."+campo+".elements[i].checked=false");}
				//todos
				if(obj.value==1)for(i=0; i<eval("document."+campo+".elements.length");i++){if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_") eval("document."+campo+".elements[i].checked=true");}
				//imagens
				if(obj.value==2)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==2) eval("document."+campo+".elements[i].checked=true");
					}
				}
				//html
				if(obj.value==3)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==1) eval("document."+campo+".elements[i].checked=true");
					}
				}
				//csv
				if(obj.value==4)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==3) eval("document."+campo+".elements[i].checked=true");
					}
				}
				//pdf
				if(obj.value==5)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==4) eval("document."+campo+".elements[i].checked=true");
					}
				}
				//video
				if(obj.value==6)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==5) eval("document."+campo+".elements[i].checked=true");
					}
				}
				//outros fxs
				if(obj.value==7)for(i=0; i<eval("document."+campo+".elements.length");i++)
				{
					if(eval("document."+campo+".elements[i].name.charAt(0)")+eval("document."+campo+".elements[i].name.charAt(1)")+eval("document."+campo+".elements[i].name.charAt(2)")+eval("document."+campo+".elements[i].name.charAt(3)")+eval("document."+campo+".elements[i].name.charAt(4)")+eval("document."+campo+".elements[i].name.charAt(5)")+eval("document."+campo+".elements[i].name.charAt(6)")=="select_")
					{
						var x=7;
						var cod="";
						var str=eval("document."+campo+".elements[i].name");
						while(x<str.length)
						{
							cod=cod+eval("document."+campo+".elements[i].name.charAt(x)");						
							x++;
						}
						tipo=eval("document."+campo+".tipo"+cod+".value");
						if(tipo==6) eval("document."+campo+".elements[i].checked=true");
					}
				}
			}
			</script>
			<div class="center">
            <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                
            
			<table border=0 cellspacing=0 cellpadding=0 class="new_quadros"><tr><td>
			<font color="#FF0000"><?=$msg?></font>
			<form action="easydrop_trata.php" method="post" name="dropandgo">
			<table width="100%" border=0 cellspacing=0 cellpadding=0 class="t2">
			<tr> 
			<td width="10" height="10"></td>
			<td class="tema_ed">Easy drop - Process</td>
			</tr>
			</table>
			<table class="t3" border=0 cellpadding=0 cellspacing=0><tr><td valign="top">
			<tr>
			<td>
				<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
				<tr>
				<td width=29 align="center"><input onClick='makeSelection("dropandgo",this)' type="checkbox" name="filter" id="filter" value="1" <? if($filter) echo "checked"?> class="cb"></td>
				<td width="5px" align="center"><img src="images/s2.jpg"></td>
				<td width="10">&nbsp;</td>
				<td style="color:#FFF">
				
				Select files:
				<select name="tipos_fich" onChange="makeSelection('dropandgo',this)";>
					<option value="0">None</option>
					<option value="1">All Files</option>
					<option value="2">images</option>
					<option value="3">html</option>
					<option value="4">csv</option>
					<option value="5">PDF</option>
					<option value="6">Flv/mov/mpg/avi</option>			    
				</select>
				Action:
				<select name="accao">
					<option value="0">--</option>
					<option value="1">Move to files</option>
					<option value="2">Delete</option>
					<option value="3">Create Public Page</option>
					<option value="4">Create Banner</option>
					<option value="5">Create News</option>
					<option value="6">Import to emails</option>
					<option value="-1">--</option>
					<option value="7">Change Page Public</option>
				</select>
				<input type="submit" name="OK" value="EXECUTE" style="height:20px" />
				</td></tr></table>
			</td>
			<td></td>
		</tr>
		</table>
		<table width="100%" class="border1" border=0 align="center" cellpadding=0 cellspacing=0>
		<tr><td height=30px width=29></td><td></td><td></td><td>File</td><td>Name</td><td></td></tr><?
			$count=1;
			while(($file = readdir($dh)) !== false)
			{
				if($count % 2 != 0)	{$cor="#C6EBFA";$class="r1";$n="1";}
				else				{$cor="#F6F6F6";$class="r2";$n="2";}
				echo "<tr BGCOLOR=$cor>";
			switch (filetype("$origem/$file")) 
			{
			  case "file"://percorrer em busca de imagem do tipo jpg	
				$pos = strrpos($file, ".");
				$ext_fich="";
				$ext_fich = substr($file,$pos + 1, strlen($file) + 1 - $pos);
				if($ext_fich=="html" || $ext_fich=="htm")
				{
				?><input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td class=preto align=center>html<input type="hidden" name="tipo<?=$count;?>" value="1"></td>	        			
							<td><a class=preto href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><?					
						$count++;
				}	        
				elseif($ext_fich=='jpg' || $ext_fich=="jpeg" || $ext_fich=="png" || $ext_fich=='jpg' || $ext_fich=="JPEG")
				{
					$filename=$origem."/$file";?>
					<input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td align=center>photo<input type="hidden" name="tipo<?=$count;?>" value="2"></td>	        			
							<td><a class=preto href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><? 
							$count++;
				}
				elseif($ext_fich=="csv")
				{
					$filename=$origem."/$file";?>
					<tr><input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td class=preto align=center>csv<input type="hidden" name="tipo<?=$count;?>" value="3"></td>	        			
							<td><a class=preto href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><? 
							$count++;
				}
				elseif($ext_fich=="pdf")
				{
					$filename=$origem."/$file";?>
					<tr><input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td class=preto align=center>pdf<input type="hidden" name="tipo<?=$count;?>" value="4"></td>	        			
							<td><a class=preto href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><? 
							$count++;
				}
					elseif($ext_fich=="flv" || $ext_fich=="avi" || $ext_fich=="mpg" || $ext_fich=="mov")
				{
					$filename=$origem."/$file";?>
					<tr><input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td class=preto align=center>video<input type="hidden" name="tipo<?=$count;?>" value="5"></td>	        			
							<td><a class=preto href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><? 
							$count++;
				}
				else{
					$filename=$origem."/$file";?>
					<tr><input type="hidden" name="file<?=$count;?>" value="<?=$file;?>">
							<td align=center><input name="select_<?=$count;?>" type="checkbox" value="1" class="cb"></td>
							<td width="4" align="right"><img width="2" src="images/s6_<?=$n?>.jpg"></td>
							<td class=preto align=center>file<input type="hidden" name="tipo<?=$count;?>" value="6"></td>	        			
							<td><a href="<?="$origem/$file";?>" target="_blank"><?=$file;?></a></td>
							<td><input style="width:100%" type=text name=file_<?=$count;?> value="<?=$file?>"></td><?
							$count++;
				}
				echo "<td width=10></td></tr>";
			} 
	  }  
	}
	?>
	</table>
	<input type="hidden" name="n_files" value="<?=($count-1);?>">
	</form>
	</td></tr></table>	
		<img src="images/bsh.png" width="100%">
	
	
	</td>
				<td width="10px">&nbsp;</td>
				<td valign="top"></td>
            </tr>
            </table>
	</div>
    <? 	include "../layout/bottom.inc.php";
}
function strip_only($str,$tags,$stripContent = false)
{
	$content = '';
	if(!is_array($tags))
	{
		$tags = (strpos($str, '>') !== false ? explode('>', str_replace('<', '',$tags)) : array($tags));
		if(end($tags)=='') array_pop($tags);
	}
	foreach($tags as $tag)
	{
		if($stripContent)$content = '(.+</'.$tag.'[^>]*>|)';
		 $str = preg_replace('#</?'.$tag.'[^>]*>'.$content.'#is', '',$str);
	}
	return $str;
}?>