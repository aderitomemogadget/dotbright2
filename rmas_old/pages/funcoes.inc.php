<?
function show_familiax($id_org=0,$T,$Tlang,$tipo='')
{		
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	if(($tipo > 0 || $tipo==0) && $tipo!='') $tp = ' and Tipo='.$tipo;
	else $tp='';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql = 'Select * from '.$T.' where ID_ORG="0" '.$tp.' order by Ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" style="width:150px">';
	if($id_org==0) echo '<option value="0" selected>- nenhuma -</option>';
	else echo '<option value="0">- nenhuma -</option>';
    while(!$rs->EOF())
    {
	    echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data["ID"]) echo ' selected ';
        $rs2=new DataObj;
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from '.$Tlang.' where ID_PAI='.$rs->db_data['ID'].' and Lang="'.$admin_lang.'"');   
		echo '>'.$rs2->db_data['Cabecalho'].'</option>';
	    $rs2->db_close();
	    show_familiaxb($rs->db_data['ID'],$id_org,$T,$Tlang);
        $rs->db_next();
    }
    $rs->db_close(1);
    echo '</select>';
}
function show_familiaxb($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$T.' where ID_ORG="'.$id_start.'" and Familia_noticias=1 order by Ordem Asc');
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if($id_org==$rs->db_data['ID']) echo ' selected ';
	    $hRes=$rs2->db_open($db_link,$DB_name,'Select * from '.$Tlang.' where ID_PAI="'.$rs->db_data['ID'].'" and Lang="'.$admin_lang.'"');
		echo '>';
		$i=0;while($i<$c){echo '&nbsp;';$i++;}
		echo $rs2->db_data['Cabecalho'].'</option>';
	    $rs2->db_close();
	    show_familiaxb($rs->db_data['ID'],$id_org,$T,$Tlang,$c+5);
        $rs->db_next();
    }
    $rs->db_close();	
}
function show_seo()
{
	global $browser,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center"><?=write_lang('key_seo_optimization','SEO OPTIMIZATION');?></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_mostrar_esconder_seo_campos','Mostrar/Esconder SEO Campos');?>" onClick="javascript:ShowSEO()"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }
function show_efeitos()
{
	/* Esta funcao foi comentada porque existem conflitos com o jquery */
	/*
	global $browser,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center"><?=write_lang('key_mostrar_efeitos','MOSTRAR EFEITOS');?></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_mostrar_esconder_efeitos','Mostrar/Esconder Efeitos');?>" onClick="javascript:ShowEFECT()"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? 
*/
}
function show_page_buttons($protected)
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$draft_id,$task,$task_form;
	?>
	<table width="100%" height="5px" border="0" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
    <table width="100%" class="p1" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td width="5px"></td>
    	<td align="left" valign="middle" class="t1"><?
			if($converter>0)
			{
				echo '<input class="botao" type="button" name="cancelar" value=" Cancelar " onMouseUp="window.open(\'list.php\',\'_self\')">';
				echo '&nbsp;<input class="botao" type="submit" name="Submit" value=" '.write_lang('key_converter','Converter').' ">';
			}
			else
			{
				echo '<input class="botao" type="submit" name="Submit" value=" '.$task_button.' ">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?>&nbsp;<input class="botao" type="button" name="cancelar" value=" Cancel " onMouseUp="window.open('list.php','_self')" /><? 
				}
				if(($Estado==1 || !$T) && $task_form=="altera")
				{
					?>&nbsp;<input class="botao" type="button" name="recuperar dados" value=" Recover data " onClick="javascript:window.open('pagina.php?recuperar=1&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&Dump=<?=uniqID("")?>','_self');" /><? 
				}
				if($draft_id && $task!="apagar_draft"){?>&nbsp;<input class="botao" type="button" name="pagina" value=" <?=write_lang('key_pre_visualizar_draf','Pré-visualizar draft');?> " onClick="window.open('../../pages.php?page_id=<?=$ID?>&draft_id=<?=$draft_id?>&prev=1','_blank')">
				&nbsp;<input class="botao" type="button" name="pagina" value=" <?=write_lang('key_sobrepor_conteudo_principal','Sobrepor conteúdo principal');?> " onClick="javascript:aprove_draft()">
				&nbsp;<input class="botao" type="button" name="cancelar" value=" <?=write_lang('key_voltar_a_pagina','Voltar a página');?> " onClick="window.open('pagina.php?ID=<?=$ID?>','_blank')"><? }
				elseif($ID && $task!="apagar_draft"){?>&nbsp;<input class="botao" type="button" name="pagina" value=" <?=write_lang('key_pre_visualizar_conteudo','Pré-visualizar conteúdo');?> " onClick="window.open('../../pages.php?page_id=<?=$ID?>&prev=1','_blank')"><? }
                if($task_form=="altera" && !$protected){?>&nbsp;<input class="botao" type="button" name="Apagar" value=" <?=write_lang('key_apagar','Apagar');?> " onMouseUp="window.open('pagina.php?ID=<?=$ID?>&task=apagar','_self')"><? }
			}?>
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="890px" height="20px">
<?
}
function show_conversoes()
{
	global $ID,$ID_ORG;
	global $browser,$ID,$draft_id;
	if($ID && !$draft_id)
	{
?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center"><?=write_lang('key_conversoes','CONVERSÕES');?></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_converter_em_conteudo_publico','Converter em conteúdo público');?>" onClick="javascript:window.open('pagina.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&converter=1&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_converter_em_conteudo_privado','Converter em conteúdo privado');?>" onClick="javascript:window.open('pagina.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&converter=2&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_converter_em_noticia','Converter em Notícia');?>" onClick="javascript:window.open('pagina.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&converter=3&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }}
function show_fast_tools($tipo=0)
{
	global  $draft_id,$ID;
	
	$tp = isset($_GET['tipo']) ? $_GET['tipo'] : $tipo;
	if(!$ID)$ID=0;
	if(!$draft_id){?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="35px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center">		
			<table border=0 cellspacing=0 cellpadding=0 class="p1">
			<tr>
				<td width="29px"><a class="preto_xs" href="backup.php" target="_blank"><img title="<?=write_lang('key_backup_das_paginas','Backup das Páginas');?>" src="backup.jpg" border=0 align="absmiddle"></a></td>
				<?
				// Esta funcao foi desativada por ainda estar em fase beta
                /*				
				<td width="29px"><a class="preto_xs" href="filemanager.php"><img title="<?=write_lang('key_ver_ficheiros','Ver ficheiros');?>" src="show.jpg" border=0 align="absmiddle"></a></td>
				*/
				?>
				<td width="29px"><a class="preto_xs" href="javascript:;" onClick="javascript:window.open('print.php')"><img title="<?=write_lang('key_imprimir_paginas','Imprimir Páginas');?>" src="print.jpg" border=0 align="absmiddle" /></a></td>
				<td width="29px"><a class="preto_xs" href="javascript:;" onClick="javascript:window.open('export.php')"><img title="<?=write_lang('key_exportar_paginas','Exportar Páginas');?>" src="save.jpg" border=0 align="absmiddle" /></a></td>				
			</tr>
			</table>			
		</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_paginas_publicas','Páginas Públicas');?>" onClick="javascript:window.open('<?=$GLOBALS['site_http_url']?>rmas/pages/list.php?tipo=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_paginas_privadas','Páginas Privadas');?>" onClick="javascript:window.open('<?=$GLOBALS['site_http_url']?>rmas/pages/list.php?tipo=1&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_noticias','Notícias');?>" onClick="javascript:window.open('<?=$GLOBALS['site_http_url']?>rmas/pages/list.php?tipo=2&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    </table>
    <br />
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_nova_pagina','Nova Página');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&tipo=0&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>    
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_nova_pagina_privada','Nova Página Privada');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&tipo=1&Dump=<?=uniqid("");?>','_self')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<? if($GLOBALS['familia_noticias'] || $GLOBALS['tipo']!=2){?>
	<tr> 
	<td width="5px" align="left"><img src="../images/s2.jpg"></td>
	<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_nova_noticia','Nova Notícia');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&tipo=2&Dump=<?=uniqid("");?>','_self')"></td>
	<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<? }elseif($GLOBALS['tipo']==2){?>
	<tr> 
	<td width="5px" align="left"><img src="../images/s2.jpg"></td>
	<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_nova_categoria','Nova Categoria');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?=$ID?>&tipo=2&Dump=<?=uniqid("");?>','_self')"></td>
	<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<? } ?>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }}
function show_drafts()
{
	global $ID,$draft_id;
	if($ID && !$draft_id){?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center" colspan="2">DRAFTS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center" colspan="2"><input class="botao" type="button" style="width:160px;height:20px" value="<?=write_lang('key_gravar_como_draft','Gravar como draft');?>" onClick="javascript:save_draft()"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr><?
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT drafts.*,Cabecalho from drafts inner join drafts_lang on drafts_lang.ID_PAI=drafts.ID WHERE drafts.ID_ORG=$ID AND Lang='$admin_lang'";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{?>
    	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="right"><input class="botao" type="button" style="width:145px;height:20px" value="<?=$rs->db_data["Cabecalho"]?>" onClick="javascript:show_draft(<?=$rs->db_data["ID"]?>)"></td><td width="30" align="left"><a href="pagina.php?ID=<?=$ID?>&draft_id=<?=$rs->db_data["ID"]?>&task=apagar_draft"><img title="<?=write_lang('key_apagar_draft','Apagar draft');?>" src="del2.png" border=0></a></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
		</tr><?
		$rs->db_next();
	}
	$rs->db_close();
	db_closedb_conection($db_link);	
	?>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px" />
<? }}
function show_drop_tools()
{
	global $browser,$ID;
	if($browser=="Firefox" && $ID)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">EASY DROP</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	<tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center">
		
			<table width="100%" height="100px" border=0 cellspacing=0 cellpadding=0 bgcolor="#FFFFFF">
			<tr>
				<td align="center"><? include "easydrop.php"; ?></td>				
			</tr>
			</table>
			
		</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? }}
function show_page_tools($filter=0)
{
	global $tipos;?>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr> 
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1"><?=write_lang('key_pesquisar','Pesquisar');?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?=$_POST["m_pesquisa"]?>" class="pesq" style="height:20px"></td>
		<td width="28px"><input type="submit" value="<?=write_lang('key_submeter_pesquisa','Submeter Pesquisa');?>" src="../images/btp.jpg" class="botao" style="width:120px;height:20px"></td>
		<td width="90" align="right" style="color:#FFF"><?=write_lang('key_listar','Listar');?>:&nbsp;&nbsp;</td>
		<td>
 		<select onChange="window.open('<? echo $GLOBALS['site_http_url']?>rmas/pages/list.php?tipo='+this.value,'_self');" name="tipos" style="width:160px">
		<option value="0" <? if($_GET['tipo']==0 || !$_GET['tipo']) echo 'selected="selected"';?>><?=write_lang('key_paginas_publicas','Páginas Públicas');?></option>
		<option value="1" <? if($_GET['tipo']==1) echo 'selected="selected"';?>><?=write_lang('key_paginas_privadas','Páginas Privadas');?></option>
		<option value="2" <? if($_GET['tipo']==2) echo 'selected="selected"';?>><?=write_lang('key_noticias','Notícias');?></option>
		</select>
		</td>
	</tr>
	</table>
<? }
function show_page_bottom_tools($tipo=0)
{?>
	<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
    <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
	<tr>
		<td width="130px" class="t1">&nbsp;<?=write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?=write_lang('key_apagar','Apagar');?></option>
				<option value="2"><?=write_lang('key_reordenar','Reordenar');?></option>
				<option value="3"><?=write_lang('key_mover_para_a_familia','Mover para a familia');?></option>
				<option value="4"><?=write_lang('key_visivel_on','Visivel - (ON)');?></option>
				<option value="5"><?=write_lang('key_visivel_off','Visivel - (OFF)');?></option>
				<option value="6"><?=write_lang('key_protegido_on','Protegido - (ON)');?></option>
				<option value="7"><?=write_lang('key_protegido_off','Protegido - (OFF)');?></option>
                <option value="8"><?=write_lang('key_pesquisa_on','Pesquisa - (ON)');?></option>
				<option value="9"><?=write_lang('key_pesquisa_off','Pesquisa - (OFF)');?></option>
                <option value="10"><?=write_lang('key_destaque_on','Destaque - (ON)');?></option>
				<option value="11"><?=write_lang('key_destaque_off','Destaque - (OFF)');?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familiax(0,"pages","pages_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?=write_lang('key_aplicar_accao','Aplicar Ação');?>" class="botao" style="width:120px;height:20px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="890px">
<?
}
function SearchRecords($d)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;	
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$d=strtolower(ltrim(rtrim($d)));
	$sql="SELECT distinct ID_PAI,
	lower(Texto) as Texto,length(lower(Texto)) AS len,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."'))/(length(lower(Texto))+1-length(replace(lower(Texto),' ','')) ) AS occwds,
	((length(Texto)-length(replace(lower(Texto),'".$d."','')))/length('".$d."')) AS occ
	FROM pages_lang
	WHERE (lower(Texto) LIKE '%".$d."%' OR Cabecalho like '%".$d."%') AND Lang='$admin_lang' ORDER BY occ DESC,occwds DESC";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	if(!$db_res) echo 'ERROR';
	return $rs;
}
function InsertNewDraft($id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
	$data=date('Y-m-d');
	$sql="insert into drafts(ID_ORG,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker,Data1,Data2) values ($id_org,'$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		InsertNewDraftLinguas($new_id,$descricao,$texto,$texto2,$texto3,$m_lang,$SEO_title,$SEO_description,$SEO_keywords);
	}
	else echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewDraftLinguas($id_pai,$descricao,$texto,$texto2,$texto3,$m_lang,$SEO_title,$SEO_description,$SEO_keywords)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="insert into drafts_lang (ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang,SEO_title,SEO_description,SEO_keywords) values ($id_pai,'$descricao','$texto','$texto2','$texto3','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
	$res=Runquery($db_link,$DB_name,$sql);	
	if(!$res)echo db_error($db_link);
    return($res);
}
function DuplicateRecord($id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$tem_foto2_ID,$tem_foto2_ext)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
	$data=date('Y-m-d');
	$sql="insert into pages(ID_ORG,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker, Data1,Data2,IS_MENU,IS_MENU2) values ($id_org,'$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2','$is_menu','$is_menu2')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS pages_m');//limpar a tabela de ram
		if($altera_img)	copy($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
		if($altera_img2)copy($Img_peq2["temp"],$img_path.$new_id."_2.".$img_ext2);
		copy($img_path.$tem_foto2_ID."_2.".$tem_foto2_ext,$img_path.$new_id."_2.".$tem_foto2_ext);
		if(InsertNewRecordLinguas($new_id,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang))
		{
			if($texto)
			{
				global $admin_lang;
				if(!$m_lang)$m_lang=$admin_lang;
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang2,SEO_title,SEO_description,SEO_keywords) values (".$new_id.",'$descricao','$texto','$texto2','$texto3','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link);
			}
		}
	}
	else echo db_error($db_link);
	if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');
	db_closedb_conection($db_link);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertNewRecord($id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
	$data=date('Y-m-d');
	$sql="insert into pages(ID_ORG,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker, Data1,Data2,IS_MENU,IS_MENU2) values ($id_org,'$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2','$is_menu','$is_menu2')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS pages_m');//limpar a tabela de ram
		if($altera_img)	copy($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
		if($altera_img2)copy($Img_peq2["temp"],$img_path.$new_id."_2.".$img_ext2);
		if(InsertNewRecordLinguas($new_id,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id))
		{
			if($texto)
			{
				global $admin_lang;
				if(!$m_lang)$m_lang=$admin_lang;
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang2,SEO_title,SEO_description,SEO_keywords) values (".$new_id.",'$descricao','$texto','$texto2','$texto3','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link);
			}
		}
	}
	else echo db_error($db_link);
	if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');
	db_closedb_conection($db_link);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertNewRecordLinguas($id_pai,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="insert into pages_lang (ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang,SEO_title,SEO_description,SEO_keywords,video_id) values ($id_pai,'$descricao','$texto','$texto2','$texto3','$m_lang','$SEO_title','$SEO_description','$SEO_keywords','$video_id')";
	$res=Runquery($db_link,$DB_name,$sql);	
	if(!$res)echo db_error($db_link);
    return($res);
}
function DeleteRecord($id,$draft_id=0,$img_path)
{	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($draft_id)
	{
		$sql="delete from drafts where ID=$draft_id";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$sql2="delete from drafts_lang where ID_PAI=$draft_id";
			$res2=Runquery($db_link,$DB_name,$sql2);
		}
		else echo db_error($db_link);
	}
	else
	{	
		$rs=GetRecords_Recursos(0,$id);
		while(!$rs->EOF())
		{
			$codigo=htmlentities_b($rs->db_data["ID"]);
			DeleteRecord($codigo);
			$rs->db_next();			
		}
		$rs->db_close();
		$sql="delete from pages where ID=$id";
		$res=Runquery($db_link,$DB_name,$sql);
		if($res)
		{
			$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS pages_m');//limpar a tabela de ram
			
			$sql2="delete from pages_lang where ID_PAI=$id";
			$res2=Runquery($db_link,$DB_name,$sql2);
		}
		else echo db_error($db_link);
	}
	$cache_filename = $site_shell_url.'files/cache/page_'.$id.'_header_'.$admin_lang.'.txt';
	if(file_exists($cache_filename)) unlink($cache_filename);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function ChangeDraft($id,$id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="Update drafts Set ID_ORG='$id_org',Data_modify='$data2',Ordem='$ordem',Visible='$visible',sim='$sim',usa_css='$usa_css',Tipo='$tipo',Template='$template',DefaultHome='$defaulthome',PageEnter='$PageEnter',PageExit='$PageExit',ID_Grupo='$ID_Grupo',Pesquisavel='$pesquisavel',Destaque='$destaque',Destaque_topo='$destaque_topo',Destaque_text='$destaque_text',Destaque_club='$destaque_club',Permanente='$permanente',Familia_noticias='$familia_noticias',Ticker='$ticker',Data1='$data1',Data2='$data2' where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="Update drafts_lang Set Cabecalho='$descricao',Texto='$texto',Texto2='$texto2',Texto3='$texto3',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords' where ID_PAI=$id and Lang='$m_lang'";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link);
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome=0,$draft=0,$draft_id=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2)
{
	if($draft)return InsertNewDraft($id,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2);
	if($draft_id)return ChangeDraft($draft_id,$id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2);
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from pages WHERE ID=$id");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["img_ext"];
		$img_ext2=$rs->db_data["img_ext2"];
	}
	$rs->db_close();	
	if(!$altera)return InsertNewRecord($id_org,$ordem,$visible,$descricao,$sim,$texto,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto2,$texto3,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2);
	else
	{
		$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
		$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
		//IMAGEM NUMERO 1
		if($tem_foto1 && $Apagar_img1){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copy($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
		//IMAGEM NUMERO 2
		if($tem_foto2 && $Apagar_img2){$ficheiro2=$id."_2.".$img_ext2;unlink($img_path.$ficheiro2);if(!$altera_img2)$img_ext2='';}
		if($altera_img2)copy($Img_peq2["temp"],$img_path.$id."_2.".$img_ext2);
		$datab=date('Y-m-d');
		if($defaulthome)
		{
			$sql="Update pages Set DefaultHome='0'";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$sql = "Update pages Set ID_ORG='$id_org',Data_modify='$datab',Ordem='$ordem',Visible='$visible',sim='$sim',usa_css='$usa_css',Tipo='$tipo',Template='$template',DefaultHome='$defaulthome',PageEnter='$PageEnter',PageExit='$PageExit',ID_Grupo='$ID_Grupo',Pesquisavel='$pesquisavel',img_ext='$img_ext',img_ext2='$img_ext2',Destaque='$destaque',Destaque_topo='$destaque_topo',Destaque_text='$destaque_text',Destaque_club='$destaque_club',Permanente='$permanente',Familia_noticias='$familia_noticias',Ticker='$ticker',Data1='$data1',Data2='$data2',IS_MENU='$is_menu',IS_MENU2='$is_menu2' where ID=$id";
		$res=Runquery($db_link,$DB_name,'DROP TABLE IF EXISTS pages_m');//limpar a tabela de ram
		$res = Runquery($db_link,$DB_name,$sql);
		if(ChangeRecordLinguas($id,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id))
		{
			if($texto)
			{
			  $sql="Select * from pages_lang where ID_PAI=$id and Lang2='$m_lang'";
			  $db_res=$rs->db_open($db_link,$DB_name,$sql);
			  if($rs->EOF())
			  {
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Texto,Texto2,Texto3,Lang2,SEO_title,SEO_description,SEO_keywords,video_id) values ($id,'$descricao','$texto','$texto2','$texto3','$m_lang','$SEO_title','$SEO_description','$SEO_keywords','$video_id')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link);
			  }
			  else
			  {
				$sql="Update pages_lang Set Cabecalho='$descricao',Texto='$texto',Texto2='$texto2',Texto3='$texto3',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords',video_id='$video_id' where ID_PAI=$id and Lang2='$m_lang'";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link);
			  }
			  $rs->db_close();
			  $sql="Update pages Set Estado=0 where ID=$id";
			  $res=Runquery($db_link,$DB_name,$sql);
			  if(!$res)echo db_error($db_link);
			}
		}
		db_closedb_conection($db_link);
		$cache_filename = $site_shell_url.'files/cache/page_'.$id.'_header_'.$admin_lang.'.txt';
		if(file_exists($cache_filename)) unlink($cache_filename);
		if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');		
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
	}
}
function ChangeRecordLinguas($id_pai,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;	
	$altera=0;$rs->db_open($db_link,$DB_name,"Select * from pages_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())$altera=1;$rs->db_close();	
	if(!$altera)return InsertNewRecordLinguas($id_pai,$descricao,$texto,$texto2,$texto3,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id);
	else
	{
		$sql = "Update pages_lang Set Cabecalho='$descricao',Texto='$texto',Texto2='$texto2',Texto3='$texto3',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords',video_id='$video_id' where ID_PAI='$id_pai' and Lang='$m_lang'";
		$res = Runquery($db_link,$DB_name,$sql);
		return($res);
	}
}
function GetRecords_Recursos($id=0,$id_org=0,$campos="*",$first=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($id!=0)
	{
		$cond="where ID=$id";
		if($id_org!=0 || $first!=0)	$cond="$cond and ID_ORG=$id_org";
	}
	else
	{
		$cond="where ID > 0";
		if($id_org!=0 || $first!=0)	$cond=$cond." and ID_ORG=$id_org";
	}
    $sql="Select $campos from pages $cond order by Ordem Asc";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	return $rs;
}
function CountRcords($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$cond = $cond . " where ID_ORG=$id and Lang='".$GLOBALS['admin_lang']."'";
    $sql = "Select count(*) as total from pages inner join pages_lang on pages_lang.ID_PAI = pages.ID $cond ";
	$rs->db_open($db_link,$DB_name,$sql);
	return $rs->db_data['total'];
}
function CountRcords_Comments($id=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$cond = $cond . " where ID_ORG=$id and Lang='".$GLOBALS['admin_lang']."'";
    $sql="Select count(*) as total from pages inner join pages_lang on pages_lang.ID_PAI = pages.ID $cond  group by pages.ID";
	$rs->db_open($db_link,$DB_name,$sql);
	return $rs->db_data['total'];
}
function noticia_on($id=0,$tp)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if($tp==1) $cond = " ID_ORG=$id";
	else  $cond = " ID=$id";
    $sql="Select * from pages where $cond and Familia_noticias=1";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()) return 1;
	else return 0;	
}
function pagina_nivel($id_org=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$rs = new DataObj;
	$pai = $id_org; 
	$nivel = 1; 
    while($pai > 0)
	{
		$sql = 'SELECT ID_ORG FROM pages WHERE ID="'.$pai.'"';
		$rs->db_open($db_link,$DB_name,$sql);	
		$pai=$rs->db_data["ID_ORG"];
		$nivel++;		
	}
	return $nivel;
}
function show_menupath($id_org,$table="pages",$pagina="pagina.php",$tipo=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;  $rs1=new DataObj;
	if($id_org!=0)
	{
		$sql="SELECT * FROM $table WHERE ID=$id_org";
		$rs->db_open($db_link,$DB_name,$sql); 
		$codigo=$rs->db_data["ID"];
		$id_org1=$rs->db_data["ID_ORG"];
		$rs->db_close();
		$sql="SELECT * FROM $table"."_lang WHERE ID_PAI=$codigo";
		$rs1->db_open($db_link,$DB_name,$sql); 
		$C=stripslashes($rs1->db_data["Cabecalho"]);
		$C1=$C!="" ? $C : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" - <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&tipo=$tipo&Dumpe=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" - <a href=\"list.php?tipo=$tipo&Dump=".uniqid('')."\" class=\"new_caminho\">Pages</a>";
	return $str;  
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0,$tipo_on=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipo,$tipos,$template,$defaulthomes;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;
	$rs=new DataObj;$rs2=new DataObj;
	$sql="Select * from pages where ID_ORG='".$pai."' and Tipo='".$tipo_on."' order by Ordem, ID";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Visivel=$rs->db_data["Visible"];
		$Protected=$rs->db_data["Protected"];
		$ordem=$rs->db_data["Ordem"];
		$tipo=$rs->db_data["Tipo"];
		$pesquisavel=$rs->db_data["Pesquisavel"];
		$destaque=$rs->db_data["Destaque"];
		$ID_ORG=$rs->db_data["ID_ORG"];
		$Home=$rs->db_data["DefaultHome"];
		$sql="Select * from pages_lang where ID_PAI='".$IDb."' and lang='$admin_lang'";
		$db_res=$rs2->db_open($db_link,$DB_name,$sql);
		$descricao=$rs2->db_data["Cabecalho"];
		$T=$rs2->db_data["Texto"];
		$rs2->db_close();
		
		if(!$descricao) $descricao = "N/A";
		
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		if($Home) {$cor="#4bc8fa";$class="r4";$n="2";}
		$i=0;
		$mostra=1;
		if($tipos==1 && $tipo !=0)$mostra=0;
		elseif($tipos==2 && $tipo !=1)$mostra=0;
		elseif($tipos==3 && $tipo !=2)$mostra=0;
		
		if($mostra)
		{
			if($pai==0) echo '<div id="md"><div class="menutitle">';?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
				<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb" /></td>
				<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
				<td width="25" style="text-align:left">
				<? 
					$rs2=new DataObj;
					$sql="Select * from pages where ID_ORG=".$rs->db_data["ID"];
					$db_res=$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF() && $pai==0){?><a href="javascript:SwitchMenu('m<?=$IDb?>')"><img alt="More pages" src="../images/<? if($pai==0) echo "more.gif"; else echo "bullet.gif";?>" border="0" /></a><? }
					else echo '<img width="22" height="8" src="../images/blank.gif" />';
					$rs2->db_close();
				?>
				</td>
				<td valign="middle" style="text-align:left"><? while($i<$contador){echo "&nbsp;";$i++;}?>
                <?
				if($tipo==0) echo '<img align="absmiddle" src="../images/page.png" style="vertical-align: middle" />';
				if($tipo==1) echo '<img align="absmiddle" src="../images/page_priv.png" style="vertical-align: middle" />';
				if($tipo==2) echo '<img align="absmiddle" src="../images/page_news.png" style="vertical-align: middle" />';
				
				?><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>
				<?
				if($tipo==2) echo '<td width="25"><a title="'.write_lang('key_ver_comentarios','Ver Comentários').'" class="lista_links" href="comments.php?ID='.$IDb.'&ID_ORG='.$rs->db_data["ID_ORG"].'&Dump='.uniqid("").'"><img border=0 src="../images/comments.png" /></a></td>';
				
				if($pesquisavel) echo '<td width="25"><img src="../images/pesq.png" /></td>';
				if($destaque) echo '<td width="25"><img src="../images/destaq.png"></td>';
				
				?>
                <td width="35" style="text-align:left"><img title="<? if($Visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
              	<td width="24" align="right" class=preto style="text-align:left"><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_Familia=<?=$ID_ORG;?>&ID_ORG=<?=$ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?=write_lang('key_duplicar_pagina','Duplicar Página');?>" src="duplicate.png" border=0></a></td>
				<td width="24" align="right" class=preto style="text-align:left"><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?=write_lang('key_apagar_pagina','Apagar Página');?>" src="del2.png" border="0" /></a><? }else echo '<img title="" src="locked.png"  border=0 align="absmiddle" />'; ?></td>
				<td width="4" align="right" style="text-align:left"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
				<td class="ordem<?=$n?>" style="text-align:left"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
			</tr>
			</table><?
			if($IDb==0) echo '</div></div>';
			if($pai==0 && $IDb>0) echo '</div><span class="subm" id="m'.$IDb.'">';
		}
		 show_content_list($IDb,$contador,$count,$md,$span,$tipo_on);  
		if($mostra && $pai==0 && $IDb>0) echo'</span></div>';
		$rs->db_next(); 
		$i++;
	}
	$rs->db_close();
}
function show_content_from_search($reg,$tipo_on=0)
{
	if(!$reg)return false;
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$key_apagar;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs_lang=new DataObj;
	$sql="Select distinct(pages.ID) as IDb,pages.*,pages_lang.Cabecalho,pages_lang.Texto from pages inner join pages_lang on pages.ID=pages_lang.ID_PAI where lang='$admin_lang' AND ID_PAI IN($reg) and Tipo='".$tipo_on."' order by Ordem,Cabecalho ASC";
	
	$rs_lang->db_open($db_link,$DB_name,$sql);
	$count=0;
	while(!$rs_lang->EOF())
	{	$count++;
		$IDb=$rs_lang->db_data["IDb"];
		$ID_Familia=$rs_lang->db_data["ID_ORG"];
		$id_pai=$rs_lang->db_data["ID_PAI"];
		$Visivel=$rs_lang->db_data["Visible"];
		$ordem=$rs_lang->db_data["Ordem"];
		$tipo=$rs_lang->db_data["Tipo"];
		$pesquisavel=$rs_lang->db_data["Pesquisavel"];
		$descricao=$rs_lang->db_data["Cabecalho"];
		$Protected=$rs_lang->db_data["Protected"];
		$T=$rs_lang->db_data["Texto"];
		$Home=$rs_lang->db_data["DefaultHome"];
		if($count % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}
		if($Home) {$cor="#4bc8fa";$class="r4";$n="2";}
	?><table border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?=$IDb?>" id="chk_<?=$IDb?>" value="<?=$IDb?>" class="cb"></td>
		<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
            <td width="29" align="center"><?
			if($pesquisavel) echo '<img src="../images/pesq.png">';

			?></td>
        <td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
        <td width="29" align="center"><?
			if($tipo==0) echo '<img align="absmiddle" src="../images/page.png" style="vertical-align: middle" />';
			if($tipo==1) echo '<img align="absmiddle" src="../images/page_priv.png" style="vertical-align: middle" />';
			if($tipo==2) echo '<img align="absmiddle" src="../images/page_news.png" style="vertical-align: middle" />';
			if($tipo==3) echo '<img align="absmiddle" src="../images/page_event.png" style="vertical-align: middle" />';
			if($tipo==4) echo '<img align="absmiddle" src="../images/page_blog.png" style="vertical-align: middle" />';
			if($tipo==5) echo '<img align="absmiddle" src="../images/poll.png" style="vertical-align: middle" />';
			
			?></td>
		<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? if($Visivel) echo "editar página "; else echo "página invisivel ";?>" src="<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?=$IDb;?>&ID_ORG=<?=$rs_lang->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>
			<td width="72" align="right" class=preto><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<?=$rs_lang->db_data["ID_ORG"];?>&ID_Familia=<?=$rs_lang->db_data["ID_ORG"];?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="duplicar pagina" src="duplicate.png" border=0></a></td>
			<td width="100" align="right" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?=$IDb?>&ID_ORG=<? echo "$rs_lang->db_data[\"ID_ORG\"]";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar pagina" src="del2.png" border=0></a><? }else echo '<img title="" src="locked.png"  border=0 align="absmiddle">'; ?></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
			<td class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$IDb?>" value="<?=$ordem?>"></td>
	  </tr>
	  </table><?
	  $rs_lang->db_next();
  }
  $rs_lang->db_close();     
}
function limpa_lixo($str)
{
	$valores=array("(",")","°","˛","+","˝","%","«","»");$str=str_replace($valores,'',$str);	
	$valores=array("'","/","&",":",";","`","´","~","^","¨","_",",",".","+","|","\\");$str=str_replace($valores,'',$str);
	$valores=array("á","ŕ","ă","â","ä"); 	$str=str_replace($valores,'a',$str);
	$valores=array("é","č","ę","ë"); 		$str=str_replace($valores,'e',$str);
	$valores=array("í","ě","î","ď"); 		$str=str_replace($valores,'i',$str);
	$valores=array("ó","ň","ő","ô","ö"); 	$str=str_replace($valores,'o',$str);
	$valores=array("ú","ů","ű","ü"); 		$str=str_replace($valores,'u',$str);
	$valores=array("ń"); 		$str=str_replace($valores,'n',$str);
	$valores=array("ç"); 		$str=str_replace($valores,'c',$str);
	$valores=array("Á","Ŕ","Ă","Â","Ä"); 	$str=str_replace($valores,'A',$str);
	$valores=array("É","Č","Ę","Ë"); 		$str=str_replace($valores,'E',$str);
	$valores=array("Í","Ě","Î","Ď"); 		$str=str_replace($valores,'I',$str);
	$valores=array("Ó","Ň","Ő","Ô","Ö"); 	$str=str_replace($valores,'O',$str);
	$valores=array("Ú","Ů","Ű","Ü"); 		$str=str_replace($valores,'U',$str);
	$valores=array("Ń"); 		$str=str_replace($valores,'N',$str);
	$valores=array("Ç"); 		$str=str_replace($valores,'C',$str);
	return strtolower($str);
}
function limpaHTML($text) 
{ 
	$text = strip_tags ($text);
    $text = stripslashes($text); 
    $text = ereg_replace("<([p|b|i|u])[^>]*>", "<\\1>",$text); 
    $text = ereg_replace("<([h1|h2|h3|h4|h5|h6][1-6])[^>]*>", "<\\1>",$text); 	
	return str_replace("&nbsp;", " ",$text);
}
?>