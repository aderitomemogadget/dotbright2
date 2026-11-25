<?
	include "settings.inc.php";
	if($task=='apagar'){$task_button="Apagar este Registo";$task_form='apaga';}
	elseif($task=='duplicar'){$task_button="Duplicar este Registo";$task_form='duplicar';}
	elseif($ID==0){$task_button="Gravar o Novo Registo";$task_form='insere';}
	else {$task_button="Gravar alterações";$task_form='altera';}
	$D=getdate();
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	
	if($ID!=0)
	{
		$sql="Select * from torneios WHERE ID=$ID order by Ordem Asc";
		$db_res=$rs->db_open($db_link,$DB_name,$sql);
		if($rs)
		{
			$datacriacao=$rs->db_data["Data"];
			$ordem=$rs->db_data["Ordem"];
			$name=$rs->db_data["Name"];
			$visivel=$rs->db_data["Visivel"];			
			$game=$rs->db_data["Game"];
			$type=$rs->db_data["Type"];
			$elimination_type=$rs->db_data["Elimination_type"];
			$max_players=$rs->db_data["Max_players"];
			$I1=$rs->db_data["img_ext"];
			$data1=str2date($rs->db_data["Date1"]);
			$data2=str2date($rs->db_data["Date2"]);			
			$data1["minutes"]=substr($rs->db_data["Date1"],14,2);
			$data1["hours"]=substr($rs->db_data["Date1"],11,2);			
			$data2["minutes"]=substr($rs->db_data["Date2"],14,2);
			$data2["hours"]=substr($rs->db_data["Date2"],11,2);
			
			$data1b=str2date($rs->db_data["Date1b"]);
			$data2b=str2date($rs->db_data["Date2b"]);			
			$data1b["minutes"]=substr($rs->db_data["Date1b"],14,2);
			$data1b["hours"]=substr($rs->db_data["Date1b"],11,2);			
			$data2b["minutes"]=substr($rs->db_data["Date2b"],14,2);
			$data2b["hours"]=substr($rs->db_data["Date2b"],11,2);
			
			
			$destaque=$rs->db_data["Destaque"];
			$sponsor=$rs->db_data["Sponsor"];
			
			$online=$rs->db_data["Online"];
			$estado=$rs->db_data["Estado"];
			$pago=$rs->db_data["Pago"];
			$aceita=$rs->db_data["Aceita"];
			
			$sql="Select * from torneios_lang where ID_PAI=$ID and lang='$admin_lang' order by ID Desc";
			$db_res=$rs2->db_open($db_link,$DB_name,$sql);	
			$T1=stripslashes($rs2->db_data["Awards"]);
			$T2=stripslashes($rs2->db_data["Rules"]);
			$rs2->db_close();
		}
		$rs->db_close();
	}
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";	
?>
<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<script type="text/javascript">
	if(document.getElementById){document.write('<style type="text/css">\n');document.write('.subm{display: none;}\n');document.write('</style>\n');}
	function SwitchMenu(obj){if(document.getElementById){var el=document.getElementById(obj);var ar=document.getElementById("md").getElementsByTagName("span");if(el.style.display!="block"){for(var i=0;i<ar.length;i++){if(ar[i].className=="subm")ar[i].style.display="none";}el.style.display="block";}else el.style.display="none";}}
	function select_page() {
	var editdoc=document.form.sponsor;
	var newurl=showModalDialog("listcontent.php", "", "resizable: no; help: no; status: no; scroll: yes;");
	if(newurl) editdoc.value=editdoc.value+newurl;
	}
</script>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
			<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
			<input type="hidden" name="slashes" value="'">
            <input type="hidden" name="tipo" value="<?=$tipo?>">
			<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
					<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
					<tr>
						<td align="left" valign="top" class=preto>
							
                            <table border=0 cellpadding="4" cellspacing=0 width="100%" style="border:#666666 1px solid;background-color:#bbcccc">
                            <tr style="background-color:#FFF">
                                <td class="preto" align="left" valign="top" style="font-size:16px"><b>Comentários:</b></td>
                            </tr>                                  
                           </table>
                           <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=0>
                           <?
						   global $IDs,$task;
						   if($IDs && $task=="desaprovar")
						   {
							   $sql="UPDATE comments Set Bloked=1 WHERE ID=$IDs";
							   $res=Runquery($db_link,$DB_name,$sql);
								if(!$res)echo db_error($db_link);
						   }
						   if($IDs && $task=="aprovar")
						   {
							   $sql="UPDATE comments Set Bloked=0 WHERE ID=$IDs";
							   $res=Runquery($db_link,$DB_name,$sql);
								if(!$res)echo db_error($db_link);
						   }
						   $sql="Select * from comments WHERE ID_Pag=$ID order by ID Asc";
						   $db_res=$rs->db_open($db_link,$DB_name,$sql);
						   if($rs)
						   {							   
							   $count2=0;?>
                               <?
                                while(!$rs->EOF())
                                {
									$IDs=$rs->db_data["ID"];
									$Date=$rs->db_data["Date"];
									$ID_User=$rs->db_data["ID_User"];
									$Bloked=$rs->db_data["Bloked"];
									$Comment=str_replace("\n","<br />",$rs->db_data["Comment"]);
                                    $t++;
									
									if($Bloked==0) $status="aproved";
									else  $status="Desaproved";
									
									$Name=stripslashes($rs->db_data["nome"]);
									
									
                                    if($count2 % 2 != 0)	$cor2="#E0E0E0";
                                    else					$cor2="#c0c0c0";?>
									<tr bgcolor="#FFFFFF"> 
									<td><div style="padding:5px"><b>Nome:</b> <?=$Name?></div>
									<div style="height:1px;border-bottom:#c0c0c0 1px solid"></div>
									<div style="padding:5px"><b>Data:</b> <?=$Date?></div>
									<div style="height:1px;border-bottom:#c0c0c0 1px solid"></div>
									<div style="padding:5px;line-height:14px"><b>Comentário:</b> <br /><?=$Comment?></div>
									<div style="height:1px;border-bottom:#c0c0c0 1px solid"></div>
									<div style="padding:5px">
									<? if($Bloked==0) { ?><a class="preto" href="comments.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&IDs=<?=$IDs?>&task=desaprovar"><div style="float:left"><img src="templates/delete_small.png" /></div><div style="float:left;padding-top:3px;margin-left:3px">Bloquear</div></a><? }
									else { ?><a class="preto" href="comments.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&IDs=<?=$IDs?>&task=aprovar"><div style="float:left"><img src="templates/ok.png" /></div><div style="float:left;padding-top:3px;margin-left:3px">Desbloquear</div></a><? }?>
									<div style="clear:both"></div>
									</div>
									</td>
									</tr>
									<tr> 
									<td height="1px">&nbsp;</td>
									</tr>
                                    <?
                                    $rs->db_next();	
                                    $count2++;
                                }
                                $rs->db_close();?>
							
                            <? }?>
                            </table>
						</td>
					</tr>
					
					</table>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top"><? show_fast_tools();?><? show_drafts()?><? show_drop_tools()?><? show_conversoes()?><? show_seo()?><? show_efeitos()?></td>
            </tr>
            </table>
		</form>
	</div>
<? 	include "../layout/bottom.inc.php";?>