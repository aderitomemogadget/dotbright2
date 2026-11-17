<?php
function show_familiax($id_org=0,$T,$Tlang,$tipo='')
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$tp='';
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql = 'Select * from '.$T.' where ID_ORG="0" '.$tp.' order by Ordem Asc';
	$rs->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" class="form-control" style="width:150px">';
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
	$rs->db_open($db_link,$DB_name,'Select * from '.$T.' where ID_ORG="'.$id_start.'" order by Ordem Asc');
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
		<td class="titulo2" align="center"><?php echo write_lang('key_campos_extra','CAMPOS EXTRA');?></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_mostrar_esconder','Mostrar/Esconder');?>" onClick="javascript:ShowSEO()"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }
function show_efeitos()
{
	/* Esta funcao foi comentada porque existem conflitos com o jquery */
	/*
	global $browser,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center"><?php echo write_lang('key_mostrar_efeitos','MOSTRAR EFEITOS');?></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_mostrar_esconder_efeitos','Mostrar/Esconder Efeitos');?>" onClick="javascript:ShowEFECT()"></td>
	</tr>
</table>
	<br><br>
<?
*/
}
function show_page_buttons($protected=0)
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T,$draft_id,$task,$task_form;
	global $task_button;
	?>
	<table width="100%" height="5px" border="0" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
    <table width="100%" class="p1" border="0" cellspacing="0" cellpadding="0">
    <tr>
    	<td align="center" valign="middle" class="t1"><?
			if($converter>0)
			{
				echo '<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onClick="window.open(\'list.php\',\'_self\')">';
				echo '&nbsp;<input class="btn btn-primary" type="submit" name="Submit" value=" '.write_lang('key_converter','Converter').' ">';
			}
			else
			{
				echo '<input class="btn btn-primary" type="submit" name="Submit" value=" '.$task_button.' ">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?>&nbsp;<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancel " onClick="window.open('list.php','_self')" /><?
				}
				if(($Estado==1 || !$T) && $task_form=="altera")
				{
					?>&nbsp;<input class="btn btn-primary" type="button" name="recuperar dados" value=" Recover data " onClick="javascript:window.open('pagina.php?recuperar=1&ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&Dump=<?php echo uniqID("")?>','_self');" /><?
				}
				if($draft_id && $task!="apagar_draft"){?>&nbsp;<input class="btn btn-info" type="button" name="pagina" value=" <?php echo write_lang('key_pre_visualizar_draf','Pré-visualizar draft');?> " onClick="window.open('../../pages.php?page_id=<?php echo $ID?>&draft_id=<?php echo $draft_id?>&prev=1&site_lingua=<?php echo $GLOBALS['admin_lang']?>','_blank')">
				&nbsp;<input class="btn btn-primary" type="button" name="pagina" value=" <?php echo write_lang('key_sobrepor_conteudo_principal','Sobrepor conteúdo principal');?> " onClick="javascript:aprove_draft()">
				&nbsp;<input class="btn btn-secondary" type="button" name="cancelar" value=" <?php echo write_lang('key_voltar_a_pagina','Voltar a página');?> " onClick="window.open('pagina.php?ID=<?php echo $ID?>','_blank')"><? }
				elseif($ID && $task!="apagar_draft"){?>&nbsp;<input class="btn btn-info" type="button" name="pagina" value=" <?php echo write_lang('key_pre_visualizar_conteudo','Pré-visualizar conteúdo');?> " onClick="window.open('../../pages.php?page_id=<?php echo $ID?>&prev=1&site_lingua=<?php echo $GLOBALS['admin_lang']?>','_blank')"><? }
                if($task_form=="altera" && !$protected){?>&nbsp;<input class="btn btn-danger" type="button" name="Apagar" value=" <?php echo write_lang('key_apagar','Apagar');?> " onClick="window.open('pagina.php?ID=<?php echo $ID?>&task=apagar','_self')"><? }
			}?>
		</td>
     </tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<?
}
function show_conversoes()
{
	global $ID,$ID_ORG;
	global $browser,$ID,$draft_id;
	if($ID && !$draft_id)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center"><?php echo write_lang('key_conversoes','CONVERSÕES');?></td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_em_conteudo_publico','Em conteúdo público');?>" onClick="javascript:window.open('pagina.php?ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&converter=1&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<?php /*
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_converter_em_conteudo_privado','Converter em conteúdo privado');?>" onClick="javascript:window.open('pagina.php?ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&converter=2&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	*/?>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_em_noticia','Em Notícia');?>" onClick="javascript:window.open('pagina.php?ID=<?php echo $ID?>&ID_ORG=<?php echo $ID_ORG?>&converter=3&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }}
function show_fast_tools($tipo=0)
{
	global  $draft_id,$ID,$noticias_por_catalogo;
	$tp = isset($_GET['tipo']) ? $_GET['tipo'] : $tipo;
	if(!$ID)$ID=0;
	if(!$draft_id){?>
	<table width="200" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
		<td class="titulo2" align="center">FAST TOOLS</td>
	</tr>
	<tr>
		<td class=preto align="center">
			<table border=0 cellspacing=0 cellpadding=0 class="p1">
			<tr>
				<td width="29px"><a class="preto_xs" href="backup.php" target="_blank"><img title="<?php echo write_lang('key_backup_das_paginas','Backup das Páginas');?>" src="images/backup.jpg" border=0 align="absmiddle"></a></td>
				<?
				// Esta funcao foi desativada por ainda estar em fase beta
                /*
				<td width="29px"><a class="preto_xs" href="filemanager.php"><img title="<?php echo write_lang('key_ver_ficheiros','Ver ficheiros');?>" src="show.jpg" border=0 align="absmiddle"></a></td>
				*/
				?>
				<td width="29px"><a class="preto_xs" href="javascript:;" onClick="javascript:window.open('print.php')"><img title="<?php echo write_lang('key_imprimir_paginas','Imprimir Páginas');?>" src="images/print.jpg" border=0 align="absmiddle" /></a></td>
				<td width="29px"><a class="preto_xs" href="javascript:;" onClick="javascript:window.open('export.php')"><img title="<?php echo write_lang('key_exportar_paginas','Exportar Páginas');?>" src="images/save.jpg" border=0 align="absmiddle" /></a></td>
			</tr>
			</table>
		</td>
	</tr>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_listar_paginas_publicas','Listar Páginas Públicas');?>" onClick="javascript:window.open('<?php echo $GLOBALS['site_http_url']?>admin/pages/list.php?tipo=0&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
    <?php /*
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_paginas_privadas','Páginas Privadas');?>" onClick="javascript:window.open('<?php echo $GLOBALS['site_http_url']?>admin/pages/list.php?tipo=1&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	*/ ?>
	<?php if($noticias_por_catalogo) {?>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_listar_noticias','Listar Notícias');?>" onClick="javascript:window.open('<?php echo $GLOBALS['site_http_url']?>admin/news1/list.php?Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<?php } else { ?>
    <tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_listar_noticias','Listar Notícias');?>" onClick="javascript:window.open('<?php echo $GLOBALS['site_http_url']?>admin/pages/list.php?tipo=2&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<?php } ?>
    </table>
    <img src="../images/bsh.png" width="100%" height="30px">
    <br />
    <table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_nova_pagina','Criar Nova Página');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=0&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<?php /*
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_nova_pagina_privada','Nova Página Privada');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=1&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	*/?>
	<? if($noticias_por_catalogo){?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_nova_noticia','Criar Nova Notícia');?>" onClick="javascript:window.open('../news1/news1.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=2&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<? }elseif($GLOBALS['familia_noticias'] || $GLOBALS['tipo']!=2){?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_nova_noticia','Criar Nova Notícia');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=2&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<? }elseif($GLOBALS['tipo']==2){?>
	<tr>
		<td class=preto align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_criar_nova_noticia','Criar Nova Notícia');?>" onClick="javascript:window.open('pagina.php?ID=0&ID_ORG=<?php echo $ID?>&tipo=2&Dump=<?php echo uniqid("");?>','_self')"></td>
	</tr>
	<? } ?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }}
function show_drafts()
{
	global $ID,$draft_id;
	if($ID && !$draft_id){?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center" colspan="2">DRAFTS</td>
	</tr>
    <tr>
		<td class=preto align="center" colspan="2"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo write_lang('key_gravar_como_draft','Gravar como draft');?>" onClick="javascript:save_draft()"></td>
	</tr><?
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$sql="SELECT drafts.*,Cabecalho from drafts inner join drafts_lang on drafts_lang.ID_PAI=drafts.ID WHERE drafts.ID_ORG=$ID AND Lang='$admin_lang'";
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	while(!$rs->EOF())
	{?>
    	<tr>
		<td class=preto align="right"><input class="btn btn-primary" type="button" style="width:145px;height:20px" value="<?php echo $rs->db_data["Cabecalho"]?>" onClick="javascript:show_draft(<?php echo $rs->db_data["ID"]?>)"></td><td width="30" align="left"><a href="pagina.php?ID=<?php echo $ID?>&draft_id=<?php echo $rs->db_data["ID"]?>&task=apagar_draft"><img title="<?php echo write_lang('key_apagar_draft','Apagar draft');?>" src="images/del2.png" border=0></a></td>
		</tr><?
		$rs->db_next();
	}
	$rs->db_close();
	db_closedb_conection($db_link);
	?>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }}
function show_drop_tools()
{
	global $browser,$ID;
	if($browser=="Firefox" && $ID)
	{?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
		<td class="titulo2" align="center">EASY DROP</td>
	</tr>
	<tr>
		<td class=preto align="center">
			<table width="100%" height="100px" border=0 cellspacing=0 cellpadding=0 bgcolor="#FFFFFF">
			<tr>
				<td align="center"><? include "easydrop.php"; ?></td>
			</tr>
			</table>
		</td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
	<br><br>
<? }}
function show_page_tools($filter=0)
{
	global $tipos;?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">PÁGINAS</h4>
        </div>
    </div>
	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width=29 align="center"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
		<td width="10px" align="center"></td>
		<td width="91px" class="t1"><?php echo write_lang('key_pesquisar','Pesquisar');?>:</td>
		<td width="150px" class=pesq><input type="text" name="m_pesquisa" value="<?php echo $_POST["m_pesquisa"]?>" class="form-control"  ></td>
		<td width="28px"><input type="submit" value="<?php echo write_lang('key_submeter_pesquisa','Submeter Pesquisa');?>" class="btn btn-primary" ></td>
		<td width="90" align="right" style="color:#FFF"><?php echo write_lang('key_listar','Listar');?>:&nbsp;&nbsp;</td>
		<td>
 		<select class="form-control" onChange="window.open('<? echo $GLOBALS['site_http_url']?>admin/pages/list.php?tipo='+this.value,'_self');" name="tipos" style="width:160px">
		<option value="0" <? if(!$_GET['tipo'] || $_GET['tipo']=='') echo 'selected="selected"';?>>TODAS</option>
		<option value="1" <? if($_GET['tipo']==1) echo 'selected="selected"';?>><?php echo write_lang('key_paginas_publicas','Páginas Públicas');?></option>
		<option value="2" <? if($_GET['tipo']==2) echo 'selected="selected"';?>><?php echo write_lang('key_noticias','Notícias');?></option>
		<?php /* <option value="3" <? if($_GET['tipo']==3) echo 'selected="selected"';?>><?php echo write_lang('key_paginas_privadas','Páginas Privadas');?></option> */?>
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
		<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
		<td width="125px"><select name="flag" id="flag" class="select1">
				<option value="" selected="selected"></option>
				<option value="1"><?php echo write_lang('key_apagar','Apagar');?></option>
				<option value="2"><?php echo write_lang('key_reordenar','Reordenar');?></option>
				<option value="3"><?php echo write_lang('key_mover_para_a_familia','Mover para a familia');?></option>
				<option value="4"><?php echo write_lang('key_visivel_on','Visivel - (ON)');?></option>
				<option value="5"><?php echo write_lang('key_visivel_off','Visivel - (OFF)');?></option>
				<option value="6"><?php echo write_lang('key_protegido_on','Protegido - (ON)');?></option>
				<option value="7"><?php echo write_lang('key_protegido_off','Protegido - (OFF)');?></option>
                <option value="8"><?php echo write_lang('key_pesquisa_on','Pesquisa - (ON)');?></option>
				<option value="9"><?php echo write_lang('key_pesquisa_off','Pesquisa - (OFF)');?></option>
                <option value="10"><?php echo write_lang('key_destaque_on','Destaque - (ON)');?></option>
				<option value="11"><?php echo write_lang('key_destaque_off','Destaque - (OFF)');?></option>
			</select>
		</td>
        <td width="5px"></td>
		<td width="250px"><? show_familiax(0,"pages","pages_lang",$tipo);?><style>
			select{vertical-align:middle;height:20px; width:250px}
			</style>
		</td>
		<td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="100%" height="30px">
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
function InsertNewDraft($id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$script,$interna,$menus)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
	$data=date('Y-m-d');
	$DefaultHome2 = intval($_POST['DefaultHome2']);
	$sql="insert into drafts(ID_ORG,bootstrap,bootstrap_p,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,DefaultHome2,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker,Data1,Data2,script,Interna,menus) values ($id_org,'$bootstrap','$bootstrap_p','$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$DefaultHome2','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2','$script','$interna','$menus')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		InsertNewDraftLinguas($new_id,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$m_lang,$SEO_title,$SEO_description,$SEO_keywords);
	}
	else echo db_error($db_link).$sql;
	db_closedb_conection($db_link);
	return($res);
}
function InsertNewDraftLinguas($id_pai,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$m_lang,$SEO_title,$SEO_description,$SEO_keywords)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="insert into drafts_lang (ID_PAI,Cabecalho,Cabecalho2,Texto,Texto2,Texto3,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang,SEO_title,SEO_description,SEO_keywords) values ($id_pai,'$descricao','$descricao2','$texto','$texto2','$texto3','$texto10','$texto11','$texto12','$texto13','$texto14','$texto15','$texto16','$texto17','$texto18','$texto19','$texto20','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link).$sql;
    return($res);
}

function DuplicateRecord($id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$tem_foto2_ID,$tem_foto2_ext,$script,$interna,$menus)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
    $altera_img3=0;if($Img_peq3['name']){$altera_img3=1;$pos=strrpos($Img_peq3['name'],".");$img_ext3=substr($Img_peq3['name'],$pos+1,strlen($Img_peq3['name'])+1-$pos);}
    $altera_img4=0;if($Img_peq4['name']){$altera_img4=1;$pos=strrpos($Img_peq4['name'],".");$img_ext4=substr($Img_peq4['name'],$pos+1,strlen($Img_peq4['name'])+1-$pos);}
    $altera_imgdoc=0;if($Img_peqdoc['name']){$altera_imgdoc=1;$pos=strrpos($Img_peqdoc['name'],".");$img_extdoc=substr($Img_peqdoc['name'],$pos+1,strlen($Img_peqdoc['name'])+1-$pos);}
	$data=date('Y-m-d');
	$DefaultHome2 = intval($_POST['DefaultHome2']);
	$sql="insert into pages(ID_ORG,bootstrap,bootstrap_p,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,DefaultHome2,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,img_ext3,img_ext4,img_extdoc,Img_estilo,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker, Data1,Data2,IS_MENU,IS_MENU2,script,Interna,menus) values ('$id_org','$bootstrap','$bootstrap_p','$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$DefaultHome2','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$img_ext3','$img_ext4','$img_extdoc','$Img_estilo','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2','$is_menu','$is_menu2','$script','$interna','$menus')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);
		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
		if($altera_img2)copia_segura($Img_peq2["temp"],$img_path.$new_id."_2.".$img_ext2);
        if($altera_img3)copia_segura($Img_peq3["temp"],$img_path.$new_id."_3.".$img_ext3);
        if($altera_img4)copia_segura($Img_peq4["temp"],$img_path.$new_id."_4.".$img_ext4);
        if($altera_imgdoc)copia_segura($Img_peqdoc["temp"],$img_path.$new_id."_doc.".$img_extdoc);
		copia_segura($img_path.$tem_foto2_ID."_2.".$tem_foto2_ext,$img_path.$new_id."_2.".$tem_foto2_ext);
		if(InsertNewRecordLinguas($new_id,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id))
		{
			if($texto)
			{
				global $admin_lang;
				if(!$m_lang)$m_lang=$admin_lang;
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Cabecalho2,Texto,Texto2,Texto3,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang2,SEO_title,SEO_description,SEO_keywords) values (".$new_id.",'$descricao','$descricao2','$texto','$texto2','$texto3','$texto10','$texto11','$texto12','$texto13','$texto14','$texto15','$texto16','$texto17','$texto18','$texto19','$texto20','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link).$sql;
			}
		}
	}
	else echo db_error($db_link).$sql;
	if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');
	db_closedb_conection($db_link);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return array('res' => $res, 'ID' => $new_id);
}
function InsertNewRecord($id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$script,$interna,$menus)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
	$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
    $altera_img3=0;if($Img_peq3['name']){$altera_img3=1;$pos=strrpos($Img_peq3['name'],".");$img_ext3=substr($Img_peq3['name'],$pos+1,strlen($Img_peq3['name'])+1-$pos);}
    $altera_img4=0;if($Img_peq4['name']){$altera_img4=1;$pos=strrpos($Img_peq4['name'],".");$img_ext4=substr($Img_peq4['name'],$pos+1,strlen($Img_peq4['name'])+1-$pos);}
    $altera_imgdoc=0;if($Img_peqdoc['name']){$altera_imgdoc=1;$pos=strrpos($Img_peqdoc['name'],".");$img_extdoc=substr($Img_peqdoc['name'],$pos+1,strlen($Img_peqdoc['name'])+1-$pos);}
	$data=date('Y-m-d');
	$DefaultHome2 = intval($_POST['DefaultHome2']);
	$sql="insert into pages(ID_ORG,bootstrap,bootstrap_p,Data,Ordem,Visible,sim,usa_css,Tipo,Template,DefaultHome,DefaultHome2,PageEnter,PageExit,ID_Grupo,Pesquisavel,img_ext,img_ext2,img_ext3,img_ext4,img_extdoc,Img_estilo,Destaque,Destaque_topo,Destaque_text,Destaque_club,Permanente,Familia_noticias,Ticker, Data1,Data2,IS_MENU,IS_MENU2,script,Interna,menus) values ($id_org,'$bootstrap','$bootstrap_p','$data','$ordem','$visible','$sim','$usa_css','$tipo','$template','$defaulthome','$DefaultHome2','$PageEnter','$PageExit','$ID_Grupo','$pesquisavel','$img_ext','$img_ext2','$img_ext3','$img_ext4','$img_extdoc','$Img_estilo','$destaque','$destaque_topo','$destaque_text','$destaque_club','$permanente','$familia_noticias','$ticker','$data1','$data2','$is_menu','$is_menu2','$script','$interna','$menus')";
	$res=Runquery($db_link,$DB_name,$sql);
	if($res)
	{
		$new_id=db_get_last_insert_id($db_link);

		if($altera_img)	copia_segura($Img_peq1["temp"],$img_path.$new_id."_1.".$img_ext);
		if($altera_img2)copia_segura($Img_peq2["temp"],$img_path.$new_id."_2.".$img_ext2);
        if($altera_img3)copia_segura($Img_peq3["temp"],$img_path.$new_id."_3.".$img_ext3);
        if($altera_img4)copia_segura($Img_peq4["temp"],$img_path.$new_id."_4.".$img_ext4);
        if($altera_imgdoc)copia_segura($Img_peqdoc["temp"],$img_path.$new_id."_doc.".$img_extdoc);
		if(InsertNewRecordLinguas($new_id,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id))
		{
			if($texto)
			{
				global $admin_lang;
				if(!$m_lang)$m_lang=$admin_lang;
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Cabecalho2,Texto,Texto2,Texto3,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang2,SEO_title,SEO_description,SEO_keywords) values (".$new_id.",'$descricao','$descricao2','$texto','$texto2','$texto3','$texto10','$texto11','$texto12','$texto13','$texto14','$texto15','$texto16','$texto17','$texto18','$texto19','$texto20','$m_lang','$SEO_title','$SEO_description','$SEO_keywords')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res) echo db_error($db_link).$sql;
			}
		}
	}
	else echo db_error($db_link).$sql;
	if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');
	db_closedb_conection($db_link);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function InsertNewRecordLinguas($id_pai,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if(!$m_lang)$m_lang=$admin_lang;
	$sql="insert into pages_lang (ID_PAI,Cabecalho,Cabecalho2,Texto,Texto2,Texto3,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang,SEO_title,SEO_description,SEO_keywords,video_id)
				values ($id_pai,'$descricao','$descricao2','$texto','$texto2','$texto3','$texto10','$texto11','$texto12','$texto13','$texto14','$texto15','$texto16','$texto17','$texto18','$texto19','$texto20','$m_lang','$SEO_title','$SEO_description','$SEO_keywords','$video_id')";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link).$sql;
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
		else echo db_error($db_link).$sql;
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
			$sql2="delete from pages_lang where ID_PAI=$id";
			$res2=Runquery($db_link,$DB_name,$sql2);
		}
		else echo db_error($db_link).$sql;
	}
	$cache_filename = $site_shell_url.'files/tmp/page_'.$id.'_header_'.$admin_lang.'.txt';
	if(file_exists($cache_filename)) unlink($cache_filename);
	exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
	return($res);
}
function ChangeDraft($id,$id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$script,$interna,$menus)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	$DefaultHome2 = intval($_POST['DefaultHome2']);
	$sql="Update drafts Set ID_ORG='$id_org',bootstrap='$bootstrap',bootstrap_p='$bootstrap_p',Data_modify='$data2',Ordem='$ordem',Visible='$visible',sim='$sim',usa_css='$usa_css',Tipo='$tipo',Template='$template',DefaultHome='$defaulthome',DefaultHome2='$DefaultHome2',PageEnter='$PageEnter',PageExit='$PageExit',ID_Grupo='$ID_Grupo',Pesquisavel='$pesquisavel',Destaque='$destaque',Destaque_topo='$destaque_topo',Destaque_text='$destaque_text',Destaque_club='$destaque_club',Permanente='$permanente',Familia_noticias='$familia_noticias',Ticker='$ticker',Data1='$data1',Data2='$data2',script='$script',Interna='$interna',menus='$menus' where ID=$id";
	$res=Runquery($db_link,$DB_name,$sql);
	$sql="Update drafts_lang Set Cabecalho='$descricao',Cabecalho2='$descricao2',Texto='$texto',Texto2='$texto2',Texto3='$texto3',Texto10='$texto10',Texto11='$texto11',Texto12='$texto12',Texto13='$texto13',Texto14='$texto14',Texto15='$texto15',Texto16='$texto16',Texto17='$texto17',Texto18='$texto18',Texto19='$texto19',Texto20='$texto20',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords' where ID_PAI=$id and Lang='$m_lang'";
	$res=Runquery($db_link,$DB_name,$sql);
	if(!$res)echo db_error($db_link).$sql;
	db_closedb_conection($db_link);
	return($res);
}
function ChangeRecord($id,$id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome=0,$draft=0,$draft_id=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$Img_peqdoc,$Img_estilo,$Apagar_img1,$Apagar_img2,$Apagar_img3,$Apagar_img4,$Apagar_imgdoc,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$script,$interna,$menus)
{

	if($draft)return InsertNewDraft($id,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Img_peq3,$Img_peq4,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2);
	if($draft_id)return ChangeDraft($draft_id,$id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome=0,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$Apagar_img1,$Apagar_img2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2);
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
        $img_ext3=$rs->db_data["img_ext3"];
        $img_ext4=$rs->db_data["img_ext4"];
        $img_extdoc=$rs->db_data["img_extdoc"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecord($id_org,$ordem,$visible,$descricao,$descricao2,$bootstrap,$bootstrap_p,$sim,$usa_css,$tipo,$template,$defaulthome,$SEO_title,$SEO_description,$SEO_keywords,$PageEnter,$PageExit,$m_lang,$ID_Grupo,$pesquisavel,$img_path,$Img_peq1,$Img_peq2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$destaque,$destaque_topo,$destaque_text,$destaque_club,$permanente,$familia_noticias,$ticker,$data1,$data2,$video_id,$is_menu,$is_menu2,$script,$interna,$menus);
	else
	{
		$altera_img=0;if($Img_peq1['name']){$altera_img=1;$pos=strrpos($Img_peq1['name'],".");$img_ext=substr($Img_peq1['name'],$pos+1,strlen($Img_peq1['name'])+1-$pos);}
		$altera_img2=0;if($Img_peq2['name']){$altera_img2=1;$pos=strrpos($Img_peq2['name'],".");$img_ext2=substr($Img_peq2['name'],$pos+1,strlen($Img_peq2['name'])+1-$pos);}
        $altera_img3=0;if($Img_peq3['name']){$altera_img3=1;$pos=strrpos($Img_peq3['name'],".");$img_ext3=substr($Img_peq3['name'],$pos+1,strlen($Img_peq3['name'])+1-$pos);}
        $altera_img4=0;if($Img_peq4['name']){$altera_img4=1;$pos=strrpos($Img_peq4['name'],".");$img_ext4=substr($Img_peq4['name'],$pos+1,strlen($Img_peq4['name'])+1-$pos);}
        $altera_imgdoc=0;if($Img_peqdoc['name']){$altera_imgdoc=1;$pos=strrpos($Img_peqdoc['name'],".");$img_extdoc=substr($Img_peqdoc['name'],$pos+1,strlen($Img_peqdoc['name']) + 1 - $pos);}
		//IMAGEM NUMERO 1
		if($_POST['tem_foto1'] && $_POST['Apagar_img1']){$ficheiro1=$id."_1.".$img_ext;unlink($img_path.$ficheiro1);if(!$altera_img)$img_ext='';}
		if($altera_img)copia_segura($Img_peq1["temp"],$img_path.$id."_1.".$img_ext);
		//IMAGEM NUMERO 2
		if($_POST['tem_foto2'] && $_POST['Apagar_img2']){$ficheiro2=$id."_2.".$img_ext2;unlink($img_path.$ficheiro2);if(!$altera_img2)$img_ext2='';}
		if($altera_img2)copia_segura($Img_peq2["temp"],$img_path.$id."_2.".$img_ext2);
        //IMAGEM NUMERO 3
		if($_POST['tem_foto3'] && $_POST['Apagar_img3']){$ficheiro3=$id."_3.".$img_ext3;unlink($img_path.$ficheiro3);if(!$altera_img3)$img_ext3='';}
		if($altera_img3)copia_segura($Img_peq3["temp"],$img_path.$id."_3.".$img_ext3);
        //IMAGEM NUMERO 4
		if($_POST['tem_foto4'] && $_POST['Apagar_img4']){$ficheiro4=$id."_4.".$img_ext4;unlink($img_path.$ficheiro4);if(!$altera_img4)$img_ext4='';}
		if($altera_img4)copia_segura($Img_peq4["temp"],$img_path.$id."_4.".$img_ext4);
        //IMAGEM NUMERO DOC
		if($_POST['tem_fotodoc'] && $_POST['Apagar_imgdoc']){$ficheirodoc=$id."_doc.".$img_extdoc;unlink($img_path.$ficheirodoc);if(!$altera_imgdoc)$img_extdoc='';}
		if($altera_imgdoc)copia_segura($Img_peqdoc["temp"],$img_path.$id."_doc.".$img_extdoc);
		$datab=date('Y-m-d');
		if($defaulthome)
		{
			$sql="Update pages Set DefaultHome='0'";
			$res=Runquery($db_link,$DB_name,$sql);
		}
		$DefaultHome2 = intval($_POST['DefaultHome2']);
		$sql = "Update pages Set ID_ORG='$id_org',bootstrap='$bootstrap',bootstrap_p='$bootstrap_p',Data_modify='$datab',Ordem='$ordem',Visible='$visible',sim='$sim',usa_css='$usa_css',Tipo='$tipo',Template='$template',DefaultHome='$defaulthome',DefaultHome2='$DefaultHome2',PageEnter='$PageEnter',PageExit='$PageExit',ID_Grupo='$ID_Grupo',Pesquisavel='$pesquisavel',img_ext='$img_ext',img_ext2='$img_ext2', img_ext3='$img_ext3', img_ext4='$img_ext4', img_extdoc='$img_extdoc',Img_estilo='$Img_estilo',Destaque='$destaque',Destaque_topo='$destaque_topo',Destaque_text='$destaque_text',Destaque_club='$destaque_club',Permanente='$permanente',Familia_noticias='$familia_noticias',Ticker='$ticker',Data1='$data1',Data2='$data2',IS_MENU='$is_menu',IS_MENU2='$is_menu2',script='$script',Interna='$interna',menus='$menus' where ID=$id";

		$res = Runquery($db_link,$DB_name,$sql);
		if(ChangeRecordLinguas($id,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id))
		{
			if($texto)
			{
			  $sql="Select * from pages_lang where ID_PAI=$id and Lang2='$m_lang'";
			  $db_res=$rs->db_open($db_link,$DB_name,$sql);
			  if($rs->EOF())
			  {
				$sql="insert into pages_lang(ID_PAI,Cabecalho,Cabecalho2,Texto,Texto2,Texto3,Texto10,Texto11,Texto12,Texto13,Texto14,Texto15,Texto16,Texto17,Texto18,Texto19,Texto20,Lang2,SEO_title,SEO_description,SEO_keywords,video_id) values ($id,'$descricao','$descricao2','$texto','$texto2','$texto3','$texto10','$texto11','$texto12','$texto13','$texto14','$texto15','$texto16','$texto17','$texto18','$texto19','$texto20','$m_lang','$SEO_title','$SEO_description','$SEO_keywords','$video_id')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql;
			  }
			  else
			  {
				$sql="Update pages_lang Set Cabecalho='$descricao',Cabecalho2='$descricao2',Texto='$texto',Texto2='$texto2',Texto3='$texto3',Texto10='$texto10',Texto11='$texto11',Texto12='$texto12',Texto13='$texto13',Texto14='$texto14',Texto15='$texto15',Texto16='$texto16',Texto17='$texto17',Texto18='$texto18',Texto19='$texto19',Texto20='$texto20',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords',video_id='$video_id' where ID_PAI=$id and Lang2='$m_lang'";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql;
			  }
			  $rs->db_close();
			  $sql="Update pages Set Estado=0 where ID=$id";
			  $res=Runquery($db_link,$DB_name,$sql);
			  if(!$res)echo db_error($db_link).$sql;
			}
		}
		db_closedb_conection($db_link);
		$cache_filename = $site_shell_url.'files/tmp/page_'.$id.'_header_'.$admin_lang.'.txt';
		if(file_exists($cache_filename)) unlink($cache_filename);
		if($tipo==2 || $tipo==3)exec('php '.$GLOBALS['site_shell_url'].'crons/gera_rss.php > /dev/null &');
		exec('php '.$GLOBALS['site_shell_url'].'crons/gera_sitemap.php > /dev/null &');
		return($res);
	}
}
function ChangeRecordLinguas($id_pai,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	if(!$m_lang)$m_lang=$admin_lang;
	if($id_org==$id)$id_org=0;
	$altera=0;
	$rs->db_open($db_link,$DB_name,"Select * from pages_lang WHERE ID_PAI=$id_pai and Lang='$m_lang'");
	if(!$rs->EOF())
	{
		$altera=1;
		$img_ext=$rs->db_data["SEO_img"];
	}
	$rs->db_close();
	if(!$altera)return InsertNewRecordLinguas($id_pai,$descricao,$descricao2,$texto,$texto2,$texto3,$texto10,$texto11,$texto12,$texto13,$texto14,$texto15,$texto16,$texto17,$texto18,$texto19,$texto20,$SEO_title,$SEO_description,$SEO_keywords,$m_lang,$video_id);
	else
	{
		$SEO_img_file["name"] = $_FILES['SEO_img']['name'];
		$SEO_img_file["temp"] = $_FILES['SEO_img']['tmp_name'];
		$Apagar_SEO_img = $_POST['Apagar_SEO_img'];
		$tem_SEO_img = $_POST['tem_SEO_img'];
		$altera_img=0;if($SEO_img_file["name"]){$altera_img=1;$pos=strrpos($SEO_img_file["name"],".");$img_ext=substr($SEO_img_file["name"],$pos+1,strlen($SEO_img_file["name"])+1-$pos);}
		if($tem_SEO_img && $Apagar_SEO_img)
		{
			unlink('../../files/pages/'.$id_pai."_seo_".$m_lang.".".$img_ext);
			if(!$altera_img)$img_ext='';
		}
		if($altera_img)
		{
			copia_segura($SEO_img_file["temp"],'../../files/pages/'.$id_pai."_seo_".$m_lang.".".$img_ext);
		}


		$sql = "Update pages_lang Set Cabecalho='$descricao',Cabecalho2='$descricao2',Texto='$texto',Texto2='$texto2',Texto3='$texto3',Texto10='$texto10',Texto11='$texto11',Texto12='$texto12',Texto13='$texto13',Texto14='$texto14',Texto15='$texto15',Texto16='$texto16',Texto17='$texto17',Texto18='$texto18',Texto19='$texto19',Texto20='$texto20',SEO_title='$SEO_title',SEO_description='$SEO_description',SEO_keywords='$SEO_keywords',video_id='$video_id',SEO_img='$img_ext' where ID_PAI='$id_pai' and Lang='$m_lang'";
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
		$C2=stripslashes($rs1->db_data["Cabecalho2"]);
		$C1= $C!="" ? ($C2 != '' ? $C.': '.$C2: $C) : "[EDITAR]";
		$rs1->db_close();
	}
	if($codigo!=0)
	{
		$str.=show_menupath($id_org1,$table,$pagina,$tipo);
		$str.=" / <a href=\"$pagina"."?ID=$codigo&ID_ORG=$id_org1&tipo=$tipo&Dumpe=".uniqid('')."\" class=\"new_caminho\">$C1</a>";
	}
	else $str.=" <a href=\"list.php?tipo=$tipo&Dump=".uniqid('')."\" class=\"new_caminho\">HOME</a>";
	return $str;
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0,$tipo_on=0)
{
	$path="../../core/";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$key_apagar,$admin_lang,$m_resumo,$tipo,$tipos,$template,$defaulthomes,$tem_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$contador+=5;

	$sql="Select * from pages where ID_ORG='".$pai."' AND (interna='' ";

	if($tem_loja)$sql.=" OR interna='1' ";
	if($subscribe_newsletter)$sql.=" OR interna='2' ";
	if($tem_area_reservada)$sql.=" OR interna='3' ";

	$sql.=" OR interna='4' ";//outra


	$sql.=")";

	if($tipo_on < 1)	$sql.=" order by Ordem,ID";
	if($tipo_on == 1)	$sql.=" and Tipo='0' order by Ordem,ID";
	if($tipo_on == 2)	$sql.=" and Tipo='2' order by Ordem,ID";
	if($tipo_on == 3)	$sql.=" and Tipo='3' order by Ordem,ID";


	$rs=new DataObj;
	$rs2=new DataObj;
	$db_res=$rs->db_open($db_link,$DB_name,$sql);
	$i=0;
	while(!$rs->EOF())
	{
		$count++;
		$IDb=$rs->db_data["ID"];
		$Visivel=$rs->db_data["Visible"];
		$Protected=$rs->db_data["Protected"];
		$interna=$rs->db_data["Interna"];
		$ordem=$rs->db_data["Ordem"];
		$tipo=$rs->db_data["Tipo"];
		$pesquisavel=$rs->db_data["Pesquisavel"];
		$destaque=$rs->db_data["Destaque"];
		$ID_ORG=$rs->db_data["ID_ORG"];
		$Home=$rs->db_data["DefaultHome"];
		$sql="Select * from pages_lang where ID_PAI='".$IDb."' and lang='$admin_lang'";
		$db_res=$rs2->db_open($db_link,$DB_name,$sql);
		$descricao=$rs2->db_data["Cabecalho"];
		$descricao2=$rs2->db_data["Cabecalho2"];
		$T=$rs2->db_data["Texto"];
		$rs2->db_close();
		if(!$descricao) $descricao = "N/A";
		if($descricao != '' && $descricao2 != '') $descricao = $descricao.': '.$descricao2;
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#fff";$class="r2";$n="2";}
		if($Home) {$cor="#4bc8fa";$class="r4";$n="2";}
		$i=0;
		$mostra=1;
		if($tipos==1 && $tipo !=0)$mostra=0;
		elseif($tipos==2 && $tipo !=1)$mostra=0;
		elseif($tipos==3 && $tipo !=2)$mostra=0;
		if($mostra)
		{
			if($pai==0) echo '<div id="md"><div class="menutitle">';?>
			<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
				<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb" /></td>
				<td width="25" style="text-align:left">
				<?
					$rs2=new DataObj;
					$sql="Select ID from pages where ID_ORG=".$rs->db_data["ID"];
					$db_res=$rs2->db_open($db_link,$DB_name,$sql);
					if(!$rs2->EOF() && $pai==0){?><a href="javascript:SwitchMenu('m<?php echo $IDb?>')"><img alt="More pages" src="../images/<? if($pai==0) echo "more.gif"; else echo "bullet.gif";?>" border="0" /></a><? }
					else echo '<img width="22" height="8" src="../images/blank.gif" />';
					$rs2->db_close();
				?>
				</td>
				<td valign="middle" style="text-align:left"><? while($i<$contador){echo "&nbsp;";$i++;}?>
                <?
                if($interna>=1)echo '<img align="absmiddle" src="../images/page_conf.png" style="vertical-align: middle" />';
				else
				{
					if($Visivel && $tipo==0) echo '<img align="absmiddle" src="../images/page.png" style="vertical-align: middle" />';
					if(!$Visivel && $tipo==0) echo '<img align="absmiddle" src="../images/page_hidden.png" style="vertical-align: middle" />';
					if($tipo==1) echo '<img align="absmiddle" src="../images/page_priv.png" style="vertical-align: middle" />';
					if($tipo==2) echo '<img align="absmiddle" src="../images/page_news.png" style="vertical-align: middle" />';
				}
				?><a title="editar página" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>
				<?
				if($tipo==2) echo '<td width="30"><a title="'.write_lang('key_ver_comentarios','Ver Comentários').'" class="lista_links" href="comments.php?ID='.$IDb.'&ID_ORG='.$rs->db_data["ID_ORG"].'&Dump='.uniqid("").'"><img border=0 src="../images/comments.png" /></a></td>';
				if($pesquisavel) echo '<td width="30"><img src="../images/pesq.png" /></td>';
				if($destaque) echo '<td width="30"><img src="../images/destaq.png"></td>';
				?>
                <td width="30" style="text-align:left"><img title="<? if($Visivel) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="images/<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
              	<td width="30" align="right" class=preto style="text-align:left"><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<?php echo $ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_duplicar_pagina','Duplicar Página');?>" src="images/duplicate.png" border=0></a></td>
				<td width="30" align="right" class=preto style="text-align:left"><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="<?php echo write_lang('key_apagar_pagina','Apagar Página');?>" src="images/del2.png" border="0" /></a><? }else echo '<img title="" src="images/locked.png"  border=0 align="absmiddle" />'; ?></td>

				<td class="ordem<?php echo $n?>" style="text-align:center"><input class="ordem" type=text name="field_<?php echo $IDb?>" value="<?php echo $ordem?>"></td>
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
		$id_org=$rs_lang->db_data["ID_ORG"];
		$id_pai=$rs_lang->db_data["ID_PAI"];
		$Visivel=$rs_lang->db_data["Visible"];
		$ordem=$rs_lang->db_data["Ordem"];
		$tipo=$rs_lang->db_data["Tipo"];
		$pesquisavel=$rs->db_data["Pesquisavel"];
		$descricao=$rs_lang->db_data["Cabecalho"];
		$Protected=$rs_lang->db_data["Protected"];
		$T=$rs_lang->db_data["Texto"];
		$Home=$rs->db_data["DefaultHome"];
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		if($Home) {$cor="#4bc8fa";$class="r4";$n="2";}
	?><table border=0 cellspacing=0 cellpadding=0 class="<?php echo $class?>">
	<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
		<td width="29" align="center"><input type="checkbox" name="chk_<?php echo $IDb?>" id="chk_<?php echo $IDb?>" value="<?php echo $IDb?>" class="cb"></td>
		<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
            <td width="29" align="center"><?
			if($pesquisavel) echo '<img src="../images/pesq.png">';
			?></td>
        <td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
        <td width="29" align="center"><?
			if($tipo==0) echo '<img align="absmiddle" src="../images/page.png" style="vertical-align: middle" />';
			if($tipo==1) echo '<img align="absmiddle" src="../images/page_priv.png" style="vertical-align: middle" />';
			if($tipo==2) echo '<img align="absmiddle" src="../images/page_news.png" style="vertical-align: middle" />';
			if($tipo==3) echo '<img align="absmiddle" src="../images/page_event.png" style="vertical-align: middle" />';
			if($tipo==4) echo '<img align="absmiddle" src="../images/page_blog.png" style="vertical-align: middle" />';
			if($tipo==5) echo '<img align="absmiddle" src="../images/poll.png" style="vertical-align: middle" />';
			?></td>
		<td valign="middle" width="35"><? while($i<$contador){echo "&nbsp;";$i++;}?><img title="<? if($Visivel) echo "editar página "; else echo "página invisivel ";?>" src="images/<? if($Visivel) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle"></td>
            <td valign="middle"><a title="editar página" class="lista_links" href="pagina.php?ID=<?php echo $IDb;?>&ID_ORG=<?php echo $rs->db_data["ID_ORG"];?>&Dump=<? echo uniqid("");?>"><? if($descricao){echo htmlentities_b(stripslashes($descricao));} else echo "[Editar]";?></a></td>
			<td width="72" align="right" class=preto><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<?php echo $ID_ORG;?>&task=duplicar&Dump=<? echo uniqid("");?>"><img title="duplicar pagina" src="images/duplicate.png" border=0></a></td>
			<td width="100" align="right" class=preto><? if(!$Protected){?><a class="italico" href="pagina.php?ID=<?php echo $IDb?>&ID_ORG=<? echo "$ID_ORG";?>&task=apagar&Dump=<? echo uniqid("");?>"><img title="apagar pagina" src="images/del2.png" border=0></a><? }else echo '<img title="" src="images/locked.png"  border=0 align="absmiddle">'; ?></td>
			<td width="4" align="right"><img width="2" src="../images/s6_<?php echo $n?>.jpg"></td>
			<td class="ordem<?php echo $n?>"><input class="ordem" type=text name="field_<?php echo $IDb?>" value="<?php echo $ordem?>"></td>
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