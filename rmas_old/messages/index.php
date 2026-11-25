<?
	$path="../../core/";
	include $path."core_files.php";
	include $path."admin_globals.php";
	include "funcoes.inc.php";

	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	if(!isset($dados['Permissoes'][4])) die("Sem Permiss&otilde;es");
	
	$nome_pagina="Mensagens";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_url;
	global $admin_user;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$dados3=new DataObj;
	
	$user_id=$admin_id;
	if(!$frame) $frame="list.php?folder=";

	include "../layout/header.inc.php";	
	include "../layout/body.inc.php"
?>
<div class="center">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
        <td valign="top"><? include "../layout/menus.inc.php"; ?>
	
	<table width="100%" height="500px" border=0 cellspacing=0 cellpadding=0 style="border-top:solid 1px #000;border-left:solid 1px #000;border-right:solid 1px #000;border-bottom:solid 1px #000;">
		<tr>
			<td valign="top">
            	<table width="100%" height="36px" border=0 cellpadding=0 cellspacing=0 background="img/bg1.gif">
                <tr>
                    <td valign="top">
                        <table border=0 cellspacing=0 cellpadding=0 height="36px">
                        <tr>
                        	<td>&nbsp;</td>
                        	<td><input type="button" value="escrever nova mensagem" class="botao" onclick="window.open('send.php','ifrm')" style="height:25px; width:155px" /></td>
                        </tr>
                        </table>
                    </td>
                </tr>
	            </table>
				<table width="100%" height="100%" border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td height=100% valign="top">
						<table width="100%" height="100%" border=0 align="center" cellpadding=0 cellspacing=0 style="border-top:solid 1px #000; background-color:#FFF"> 
							<tr> 
								<td valign="top" width="200" background="img/bg2.jpg" style="background-repeat:repeat-x">
									<table border=0 cellpadding=0 cellspacing=0 height="100%" width="100%">
										<tr>
											<td valign="top">
												<table border=0 cellpadding=10 cellspacing=10 width="100%">
													<tr>
														<td valign="top" align="left">
															<style>
																a {font-size: 11px;font-family: verdana;color: #fff;text-decoration: none;}
																a:link {font-size: 11px;  font-family: verdana;  color: #fff;  text-decoration: none;}
																a:active {  font-size: 11px;  font-family: verdana;  color: #fff;  text-decoration: none;}
																a:visited {  font-size: 11px;  font-family: verdana;  color: #fff;  text-decoration: none;}
																a:hover {  font-size: 11px;  font-family: verdana;  color: #0295db;  text-decoration: none;}
																img{ border:0}
															</style>
															<script type="text/javascript" src="dtree/dtree.js"></script>
															<script type="text/javascript">
																/*function(id, pid, name, url, title, target, icon, iconOpen, open)*/
																dtree_43de6364d8222=new dTree('dtree_43de6364d8222',"<?=$site_url?>mensagens");
																dtree_43de6364d8222.config.useSelection=true;
																dtree_43de6364d8222.config.useLines=true;
																dtree_43de6364d8222.config.useIcons=true;
																dtree_43de6364d8222.config.useCookies=false;
																dtree_43de6364d8222.config.useStatusText=false;
																dtree_43de6364d8222.config.closeSameLevel=false;
																dtree_43de6364d8222.add("0","-1","&nbsp;<font style='font-size:11px; font-family:verdana; color:#ffffff;'>Mensagens:<br></font>","","ifrm","");																																
																dtree_43de6364d8222.add("10","0"," recebidas <?=count_msgs("",$user_id);?>","list.php?action=list&folder=","","ifrm","img/b_receber.gif");
																dtree_43de6364d8222.add("20","0"," enviadas <?=count_msgs("sent",$user_id);?>","list.php?action=list&folder=sent","","ifrm","img/b_enviado.gif");
																dtree_43de6364d8222.add("30","0"," apagadas <?=count_msgs("bin",$user_id);?>","list.php?action=list&folder=bin","","ifrm","img/b_apagado.gif");
																document.write(dtree_43de6364d8222);
																dtree_43de6364d8222.closeAll();
																dtree_43de6364d8222.openTo("0","true");
															</script>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
								<td width="10" background="img/bg.jpg"></td>
								<td valign="top">
									<script type="text/javascript">
										var iframeids=["ifrm"]
										var iframehide="yes"
										var getFFVersion=navigator.userAgent.substring(navigator.userAgent.indexOf("Firefox")).split("/")[1]
										var FFextraHeight=parseFloat(getFFVersion)>=0.1? 16 : 0 //extra height in px to add to iframe in FireFox 1.0+ browsers
										function resizeCaller(){
											var dyniframe=new Array()
											for (i=0; i<iframeids.length; i++){
												if(document.getElementById) resizeIframe(iframeids[i])
												if((document.all || document.getElementById) && iframehide=="no"){
													var tempobj=document.all? document.all[iframeids[i]] : document.getElementById(iframeids[i])
													tempobj.style.display="block"
												}
											}
										}
										function resizeIframe(frameid){
											var currentfr=document.getElementById(frameid)
											if(currentfr && !window.opera){
												currentfr.style.display="block"
												if(currentfr.contentDocument && currentfr.contentDocument.body.offsetHeight) //ns6 syntax
													currentfr.height=currentfr.contentDocument.body.offsetHeight+FFextraHeight; 
												else if(currentfr.Document && currentfr.Document.body.scrollHeight) //ie5+ syntax
													currentfr.height=currentfr.Document.body.scrollHeight;
												if(currentfr.addEventListener)
													currentfr.addEventListener("load", readjustIframe, false)
												else if(currentfr.attachEvent){
													currentfr.detachEvent("onload", readjustIframe) // Bug fix line
													currentfr.attachEvent("onload", readjustIframe)
												}
											}
										}
										function readjustIframe(loadevt){
											var crossevt=(window.event)? event : loadevt
											var iframeroot=(crossevt.currentTarget)? crossevt.currentTarget : crossevt.srcElement
											if(iframeroot)	resizeIframe(iframeroot.id);
										}
										function loadintoIframe(iframeid, url){
											if(document.getElementById) document.getElementById(iframeid).src=url
										}
										if(window.addEventListener)	window.addEventListener("load", resizeCaller, false)
										else if(window.attachEvent)	window.attachEvent("onload", resizeCaller)
										else	window.onload=resizeCaller
									</script>
									<iframe id="ifrm" name="ifrm" src="<?=$frame;?>" scrolling="auto" marginwidth="0" marginheight="0" frameborder=0 vspace="0" hspace="0" style="overflow:visible; width:100%; height:500px; display:none">
										[Content for browsers that don't support iframes goes here]
									</iframe>&nbsp;
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

		</td>
	</tr>
</table>
	</div>
<? 	include "../layout/bottom.inc.php";?>