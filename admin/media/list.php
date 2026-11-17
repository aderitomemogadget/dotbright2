<?
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Reordenar
	if($flag==2)
	{
		foreach($_POST as $varName=> $value)
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$var="field_".$str_id;
				$dv=$$var;
				$sql="UPDATE media SET Ordem='$dv' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// apagar
	if($flag==1)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"delete from media WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// tornar visivel
	if($flag==4)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE media SET Visivel='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// tornar invisivel
	if($flag==5)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE media SET Visivel='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	if($_POST && $_POST["m_pesquisa"])
	{
		$registos=SearchRecords($_POST["m_pesquisa"]);
		$reg="";
		while(!$registos->EOF()){if($reg)$reg.=",";$reg.=$registos->db_data["ID"];$registos->db_next();}
		$registos->db_close();
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	?><script>
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.submenu{display:none;}\n');document.write('</style>\n')}
	function SwitchMenu(obj)
	{
		if(document.getElementById)
		{
			var el=document.getElementById(obj);
			var el2=document.getElementById("a"+obj);
			var ar=document.getElementById("masterdiv").getElementsByTagName("span");
			var ar2=document.getElementById("masterdiv").getElementsByTagName("img");
			if(el.style.display != "block")
			{
				for (var i=0; i<ar.length; i++){ if(ar[i].className=="submenu"){	ar[i].style.display="none";}}
				for (var i=0; i<ar2.length; i++){ if(ar2[i].className=="image"){	ar2[i].src="templates/more.gif";}}
				el.style.display="block";
				el2.src="templates/less.gif";
			}
			else
			{
				el.style.display="none";
				el2.src="templates/more.gif";
			}
		}
	}
	</script>
    <div id="masterdiv">
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
		<tr>
		    <td valign="top" align="left"><?
				include "../layout/menus.inc.php";

				show_page_tools();
				global $DB_host,$DB_user,$DB_pass,$DB_name;
				$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
				$rs=new DataObj;
				$hRes=$rs->db_open($db_link,$DB_name,"Select * from media_type");

				$count=0;
				$num=0;
				while(!$rs->EOF())
				{
					$num++;

					$codigo		= $rs->db_data["ID"];
					$tipo		= $rs->db_data["Comportamento"];
					$largura	= $rs->db_data["largura"];
					$altura		= $rs->db_data["altura"];
					$Protegido	= $rs->db_data["Protegido"];

					for($a=0; $a < count($GLOBALS['publicidade']['codigo']); $a++)
					{
						if($tipo == $GLOBALS['publicidade']['codigo'][$a] || ($tipo == '0' && !$GLOBALS['publicidade']['codigo'][$a])) $efeito = $GLOBALS['publicidade']['nome'][$a];// . ' - ' . $largura . 'x' . $altura;
					}

					$name = ($rs->db_data["Nome"]);

					if($count % 2 != 0)	$cor = "#C6EBFA";
					else				$cor = "#F6F6F6";

					$t_media = conta_banners($codigo);
					?>
					<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=0>
                        <tr style="background:<?=$cor;?>" <? if($t_media>0){?>onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'"<? }?>>
                            <td width="35" align="center"><? if($t_media>0){?><a href="javascript:;" onClick="SwitchMenu('sub<?=$num?>')" class="preto"><img id="asub<?=$num?>" class="image" src="../images/more.gif" border=0 align="absmiddle"></a><? }?></td>
                            <td valign="top"></td>
                            <td valign="middle" class="preto"><? if(!$Protegido){?><a href="../media_type/pagina.php?ID=<?=$rs->db_data["ID"]?>" class="preto"><strong><? echo "$name"; ?></strong> - [<?=$t_media?> <? echo write_lang('key_anuncios_inseridos','anúncios inseridos');?>]</a><? }else {?><strong><?=$name; ?></strong> <? }?></td>
                            <td width="200" class="preto" align="right"><? if($t_media>0){?><a href="javascript:;" onClick="SwitchMenu('sub<?=$num?>')" class="preto"><? }?><?=$efeito?><? if($t_media>0){?></a><? }?>&nbsp;&nbsp;</td>
                            <? if($Protegido){?><td class="preto" align="right" width="80"><img alt="" src="locked.png" border=0 align="absmiddle" /></td><? }else{?><td class="preto" align="center" width="35"><a class="italico" href="../media_type/pagina.php?ID=<?=$rs->db_data["ID"]?>&task=apagar"><img alt="" src="del2.png" border=0 align="absmiddle"></a></td><? }?>
                        </tr>
					</table>
					<span id="sub<?=$num?>" class="submenu">
                  		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=0>
						<?

						global $DB_host,$DB_user,$DB_pass,$DB_name;
						$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
						$rs2=new DataObj;
						// echo "Select * from media where Tipo='".$codigo."'";

						$rs2->db_open($db_link,$DB_name,"SELECT * FROM media WHERE Tipo='".$codigo."' AND Familia = 0 ORDER BY Peso ASC");
						$count2=0;
						$t=0;
						$t_to=$rs2->db_rows;
						while(!$rs2->EOF())
						{
							$t++;

							$codigo		= $rs2->db_data["ID"];
							$familia	= $rs2->db_data["Familia"];
							$name		= $rs2->db_data["Name"];
							$Protegido	= $rs2->db_data["Protegido"];

							if($count2 % 2 != 0) 	$cor2 = "#E0E0E0";
							else					$cor2 = "#c0c0c0";

							?>
                            <tr>
								<td width="40" bgcolor="<?=$cor2;?>" align="right"><img src="templates/joinbottom<? if($t==$t_to){?>2<? }?>.gif" border=0 align="absmiddle"></td>



                                <td width="10" valign="top" bgcolor="<?=$cor2;?>"><a class="preto" href="pagina.php?ID=<?=$rs2->db_data["ID"]?>&ID_ORG=<?=$rs2->db_data["ID_ORG"]?>"><img src="ok.png" border=0 align="absmiddle"></a></td>



								<td valign="top" bgcolor="<?=$cor2;?>" width="8"></td>










                                <? /*
                                <td valign="middle" bgcolor="<?=$cor2;?>"><a class="preto" href="pagina.php?ID=<?=$rs2->db_data["ID"]?>&ID_ORG=<?=$rs2->db_data["ID_ORG"]?>"><? echo "$name";?></a></td>
								*/ ?>
                                <td valign="middle" bgcolor="<?=$cor2;?>"><a class="preto" href="pagina.php?ID=<?=$rs2->db_data["ID"]?>&Familia=<?=$rs2->db_data["Familia"]?>"><? echo "$name";?></a></td>














								<? if($Protegido){?><td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="80"><img alt="" src="locked.png" border=0 align="absmiddle" /></td><? }else{?><td valign="middle" bgcolor="<?=$cor2;?>" align="center" width="35"><a class="italico" href="pagina.php?ID=<?=$rs2->db_data["ID"]?>&task=apagar"><img alt="" src="del2.png" border=0 align="absmiddle" /></a></td><? }?>
							</tr>
							<?
							$rs2->db_next();
							$count2++;
						}
						$rs2->db_close();
					?>
					</table>
					</span>
					<?
					$rs->db_next();
					$count++;
				}
				$rs->db_close();
				?>
			</td>
			<td width="10px">&nbsp;</td>
			<td valign="top" width="200"><? show_fast_tools();?></td>
		</tr>
		</table>
</div>
<? 	include "../layout/bottom.inc.php";?>