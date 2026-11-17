<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
global $admin_id;

if ($admin_id) {
    $currentFingerprint = hash('sha256', isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT'] : '');
    $fingerprintValid = isset($_SESSION['admin_fingerprint']) && hash_equals($_SESSION['admin_fingerprint'], $currentFingerprint);
    $activityValid = isset($_SESSION['admin_last_activity']) && (time() - (int) $_SESSION['admin_last_activity']) <= 1800;

    if (!$fingerprintValid || !$activityValid) {
        $_SESSION = array();
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
        }
        session_destroy();
        $admin_id = 0;
        header('Location: ' . $GLOBALS['site_http_url'] . 'admin/index.php?expired=1');
        exit;
    }

    $_SESSION['admin_last_activity'] = time();
}

if(!$admin_id && !isset($_POST['login']))
{
	global $site_http_url;
	echo '<meta http-equiv="refresh" content="0; URL='.$site_http_url.'admin/index.php">';exit();
}

function pesquisa_rapida()
{
	global $admin_lang,$site_http_url;
	$page_url=$_SERVER['REQUEST_URI'];
	if(!substr_count($page_url,'_trata.php'))
	{
		$curpage=curPageURL();
		$curpage=str_replace("?admin_lingua=".$admin_lang."&","?",$curpage);
		$curpage=str_replace("&admin_lingua=".$admin_lang."&","&",$curpage);
		?>
		<li class="navbar-form" style="margin:0px; list-style-type: none">
		<form action="<?=$site_http_url?>admin/pesquisa/pesquisa.php" method="POST" name="search">
		<div class="form-group">
		<input type="text" class="form-control" placeholder="Pesquisa" name="top_search">
		<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
		</div>
		</form>
		</li>

		<?php /*
		<div style="width: 100%"><form action="<?=$site_http_url?>admin/products/products_families.php" method="post">
		<div style="float: left;line-height: 35px; position: relative;">
			<input type="text" placeholder="Pesquisa" name="top_search" class="form-control" style="padding-right: 35px;"/>
			<button type="submit" class="btn btn-search"><i class="fa fa-search"></i></button>
		</div>
		</form>
		</div><?php */
	}
}
function pesquisa_rapida_bk()
{
	global $admin_lang,$site_http_url;
	$page_url=$_SERVER['REQUEST_URI'];
	if(!substr_count($page_url,'_trata.php'))
	{
		$curpage=curPageURL();
		$curpage=str_replace("?admin_lingua=".$admin_lang."&","?",$curpage);
		$curpage=str_replace("&admin_lingua=".$admin_lang."&","&",$curpage);
		?><div style="width: 100%"><form action="<?=$site_http_url?>admin/products/products_families.php" method="post">
		<div style="float: left;line-height: 35px">Pesquisa:&nbsp;</div>
		<div style="float: left;line-height: 35px"><input type="text" name="top_search" class="form-control" /></div>
		<div style="float: left;line-height: 35px"><select name="type_search" class="form-control">
			<option value="1">produtos</option>
			<option value="2">produtos nao importados</option>
		</select>
		</div>
		<div style="float: left;line-height: 35px"><input type="submit" value=" OK "  class="botao"/></div>
		</form>
		</div><?php
	}
}
function gera_estilos()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$users2=explode(';',$users);
	global $admin_id;
	$where='WHERE Tipo=1';
	$rs->db_open($db_link,$DB_name,'Select Nome from modulos_config '.$where);
	$i=0;
    while(!$rs->EOF())
    {
    	if($i>0)echo ',';
        echo '{name:\''.$rs->db_data['Nome'].'\',element:\'span\',attributes:{\'class\':\''.str_replace('.','',$rs->db_data['Nome']).'\'}}';
        $rs->db_next();
        $i++;
    }
    $rs->db_close(1);
}
function edit_chose_lang()
{
	global $admin_lang,$site_http_url;
	$page_url=$_SERVER['REQUEST_URI'];
	if(!substr_count($page_url,'_trata.php'))
	{
		$curpage=curPageURL();
		$curpage=str_replace("?admin_lingua=".$admin_lang."&","?",$curpage);
		$curpage=str_replace("&admin_lingua=".$admin_lang."&","&",$curpage);
		?>
		<div style="float: left;line-height: 35px"><a class="navlink" title="<?=write_lang('key_editar_linguas','Editar Linguas')?>" href="<?=$site_http_url?>admin/linguas/list.php">Língua:</a></div>
		<div style="float: left;line-height: 35px"><select class="form-control" style="width:150px" name="admin_lingua" onChange="reload_page(this.options[this.selectedIndex].value,'<?=$curpage?>')">
			<?php
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$rs->db_open($db_link,$DB_name,'Select * from linguas order by Ordem Asc');
		while(!$rs->EOF())
		{
			echo '<option value="'.$rs->db_data['Sigla'].'"';
			if($admin_lang==$rs->db_data['Sigla']) echo ' selected ';
			echo '>';
			echo $rs->db_data['Sigla'].'-'.$rs->db_data['Cabecalho'].'</option>';
			$rs->db_next();
		}
		$rs->db_close();
		?>
		<option value="-1">adicionar nova</option>
		</select></div><?php
	}
}
function curPageURL()
{
	$U='http';
	if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']=='on')$U.='s';
	$U.='://';
	if($_SERVER['SERVER_PORT'] != '80'){$U.=$_SERVER['SERVER_NAME'].':'.$_SERVER['SERVER_PORT'].$_SERVER['REQUEST_URI'];}else{$U.=$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];}
	if(substr_count($U,'?')) return $U.'&';else return $U.'?';
}
function select_lang_pro($admin_lang,$m_url)
{
	global $n_linguas,$linguas,$admin_lang,$task_form;
	if($n_linguas>1 && !$task_form)
	{?>
	<table class="l1" cellpadding=0 cellspacing=0><tr><td width="5"><img src=../images/l1.jpg></td><td class="tl">L&iacute;ngua activa: <img src=../img/flag_<?=$admin_lang?>.png height="17" border=0 align="absmiddle"></td>
	<td width=10><img src=../images/s1.jpg></td><?php
		if($m_url=='?') $m_url='?1=1';
		$m_str=$linguas;
		while($m_str <> '')
		{
			$act_lang=substr($m_str,0,strpos($m_str,','));
			if($act_lang==''){$act_lang=$m_str;$m_str='';}
			else $m_str=substr($m_str,strpos($m_str,',')+1,strlen($m_str));
			if($act_lang <> $admin_lang) echo '<td width=20><a title='.$act_lang.' href="'.$m_url.'&lingua='.$act_lang.'&d='.uniqid('').'"><img src=../img/flag_'.$act_lang.'.png height=17 border=0></a></td>';
		}?>
	<td width=5><img src=../images/r1.jpg></td>
	</tr></table><?php
	}
}
function act_sspasswd($p){return encripta($p);}
function comp_sspasswd($p){return base64_decode($p);}
function date2SQLstr($d){$s=$d['year'];$s.='-'.$d['mon'];$s.='-'.$d['mday'];$s='\''.$s.'\'';return($s);}
function str2date($s='')
{
	$D=getdate();
	if(!empty($s))
	{
		$T=mktime(0,0,0,(int)substr($s,5,2),(int)substr($s,8,2),(int)substr($s,0,4));
		$D=getdate($T);
	}
	return($D);
}
function removeChar2($s,$S1,$S2){return $s=substr($s,0,strpos($s,$S1)+1).substr($s,strrpos($s,$S2),strlen($s));}
function show_box($accao,$r,$d,$next_page,$voltar,$tipo_msg=0)
{
	global $path,$site_http_url,$site_http_url,$site_shell_url;
	$D=getdate();

	if (strpos($_SERVER['REQUEST_URI'], "rmas") !== false)
	{
		include '../layout/header.inc.php';
		include '../layout/body.inc.php';
		
		echo '<div class="center"><table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td valign="top">
						<table class="new_quadros" width="325px" border=0 cellspacing=0 cellpadding="4" align="center">
						<tr>
							<td valign="top">';

		echo '<table border=0 width="300" cellpadding=0 cellspacing=0 align=center><tr>';
		if($tipo_msg==2) echo '<td class="preto" align="center"><img width=32 height=32 src="'.$site_http_url.'admin/images/important.png"><br><br><b>'.$r.'</b>';
		elseif($tipo_msg==1) echo '<td class="preto" align="center"><img width=32 height=32 src="'.$site_http_url.'admin/images/error.png"><br><br><b>'.$r.'</b>';
		else echo '<td class="preto" align="center"><img width=37 height=40 src="'.$site_http_url.'admin/images/ok.png"><br><br><b>'.$r.'</b>';
		echo "<br><br>".$d."<br><br>";
		echo '<input class="botao" id="1" type="button" name="button" style="height:30px; width:50px;" value=" OK " onclick=window.open(\''.$next_page.'\',\'_self\')>';
		if($voltar)	echo '<input class="botao" type="button" name="Button" value="Voltar" OnClick="Javascript:history.go(-1);">';
		echo '<br><br></td></tr></table></td></tr></table></td></tr></table><script type="text/javascript">document.getElementById(\'1\').focus()</script></body></html>';
	}
	else
	{
		echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
		<html><head><title>ADMIN</title><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"></head><body>';
		include $site_shell_url.'admin/layout/header.inc.php';
		include $site_shell_url.'admin/layout/body.inc.php';

		echo '<div class="center"><table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
				<tr>
					<td valign="top">
						<table class="new_quadros" width="325px" border=0 cellspacing=0 cellpadding="4" align="center">
						<tr>
							<td valign="top">';

		echo '<table border=0 width="300" cellpadding=0 cellspacing=0 align=center><tr>';
		if($tipo_msg==2) echo '<td class="preto" align="center"><img width=32 height=32 src="'.$site_http_url.'admin/images/important.png"><br><br><b>'.$r.'</b>';
		elseif($tipo_msg==1) echo '<td class="preto" align="center"><img width=32 height=32 src="'.$site_http_url.'admin/images/error.png"><br><br><b>'.$r.'</b>';
		else echo '<td class="preto" align="center"><img width=37 height=40 src="'.$site_http_url.'admin/images/ok.png"><br><br><b>'.$r.'</b>';
		echo "<br><br>".$d."<br><br>";
		echo '<input class="botao" id="1" type="button" name="button" style="height:30px; width:50px;" value=" OK " onclick=window.open(\''.$next_page.'\',\'_self\')>';
		if($voltar)	echo '<input class="botao" type="button" name="Button" value="Voltar" OnClick="Javascript:history.go(-1);">';
		echo '<br><br></td></tr></table></td></tr></table></td></tr></table><script type="text/javascript">document.getElementById(\'1\').focus()</script></body></html>';
	}
}
function show_box_OK($t,$u='list.php')
{
	if($t==1) show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_registo_inserido_corretamente','REGISTO INSERIDO CORRETAMENTE'),write_lang('key_box_registo_foi_inserido','O seu registo foi inserido'),$u,0,0);
	if($t==2) show_box(write_lang('key_box_alterar','ALTERAR'),write_lang('key_box_registo_alterado_corretamente','REGISTO ALTERADO CORRETAMENTE'),write_lang('key_box_registo_foi_alterado','O seu registo foi alterado'),$u,0,0);
	if($t==3) show_box(write_lang('key_box_apagar','APAGAR'),write_lang('key_box_registo_apagado_corretamente','REGISTO APAGADO CORRETAMENTE'),write_lang('key_box_registo_foi_eliminado','O seu registo foi eliminado'),$u,0,0);
	if($t==5) show_box(write_lang('key_box_gerar','GERAR'),write_lang('key_box_gerar_ficheiro_traducoes','FICHEIRO TRADUÇÕES GERADO CORRETAMENTE'),write_lang('key_box_ficheiro_gerado','O ficheiro de traduções foi gerado'),$u,0,0);
}
function show_box_ERROR($t,$u='list.php')
{
	if($t==1) show_box(write_lang('key_box_inserir','INSERIR'),write_lang('key_box_erro_inserir','ERRO AO INSERIR'),write_lang('key_box_prob_removido','Devido a problemas técnicos, Este registo não foi inserido.'),$u,0,1);
	if($t==2) show_box(write_lang('key_box_alterar','ALTERAR'),write_lang('key_box_erro_alterar','ERRO AO ALTERAR'),write_lang('key_box_prob_alterado','Devido a problemas técnicos, Este registo não foi alterado.'),$u,0,1);
	if($t==3) show_box(write_lang('key_box_apagar','APAGAR'),write_lang('key_box_erro_eliminar','ERRO AO ELIMINAR'),write_lang('key_box_prob_removido','Devido a problemas técnicos, Este registo não foi removido.'),$u,0,1);
	if($t==4) show_box(write_lang('key_box_erro','ERRO'),write_lang('key_box_erro_eliminar','ERRO!!!'),write_lang('key_box_operacao','Operação Ilegal'),$u,0,1);
}
function show_familia($id_org=0,$T,$Tlang,$tipo='')
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	if(($tipo > 0 || $tipo==0) && $tipo!='') $tp=' and Tipo='.$tipo;
	else $tp='';
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$T.' where ID_ORG="0" '.$tp.' order by Ordem Asc');
	echo '<select class="form-control" name="m_familia" style="width:150px">';
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
		show_familiab($rs->db_data['ID'],$id_org,$T,$Tlang);
		$rs->db_next();
	}
	$rs->db_close(1);
	echo '</select>';
}
function show_familiab($id_start=0,$id_org=0,$T,$Tlang,$c=5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
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
		show_familiab($rs->db_data['ID'],$id_org,$T,$Tlang,$c+5);
		$rs->db_next();
	}
	$rs->db_close();
}
function show_lista($ID_Grupo,$tabela,$campo_name)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$tabela);
	echo '<select class="form-control" name="'.$campo_name.'">';
	if($ID_Grupo==0) echo '<option value="0" selected>- all -</option>';
	else echo '<option value="0">- all -</option>';
	while(!$rs->EOF())
	{
		echo '<option value="'.$rs->db_data['ID'].'"';
		if($ID_Grupo==$rs->db_data['ID']) echo ' selected ';
		echo '>';
		echo $rs->db_data['Descricao'].'</option>';
		$rs->db_next();
	}
	$rs->db_close(1);
	echo '</select>';
}
function show_editor($name,$textarea,$w,$h)
{
	global $caminho_bt_editor2,$usa_css,$site_http_url;?>
    <script>
	<?php if($usa_css){?>CKEDITOR.config.contentsCss=['<?php echo $site_http_url?>layout/<?php echo PROJETO?>/css/bootstrap.css','<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo dumpcache?>'];<?php }else{?>CKEDITOR.config.contentsCss=['<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo dumpcache?>'];<?php }?>


	//CKEDITOR.disableAutoInline = true;
	//var editor=CKEDITOR.inline( '<?php echo $textarea?>',{toolbar : 'Full', allowedContent: true} );

	var editor=CKEDITOR.replace( '<?php echo $textarea?>',{toolbar : 'Full', allowedContent: true});
	CKFinder.setupCKEditor( editor, '../editor/<?php echo EDITOR;?>/ckfinder/' ) ;


	</script><?php
}
function show_editor_small($name,$textarea,$w,$h)
{
	global $caminho_bt_editor2,$usa_css,$site_http_url;?>
    <script>
	<?php if($usa_css){?>CKEDITOR.config.contentsCss=['<?php echo $site_http_url?>layout/<?php echo PROJETO?>/css/bootstrap.css','<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo dumpcache?>'];<?php }else{?>CKEDITOR.config.contentsCss=['<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo dumpcache?>'];<?php }?>
	var editor=CKEDITOR.replace( '<?php echo $textarea?>',{toolbar : 'Small', allowedContent: true, height: <?php echo $h?>});
	CKFinder.setupCKEditor( editor, '../editor/<?php echo EDITOR;?>/ckfinder/' ) ;
	</script><?php
}
function show_page_title($str='')
{
	echo '<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
	<tr>
		<td width="10px" align="center">&nbsp;</td>
		<td align="left" style="font-size:18px;color:#322f31">'.$str.'</td>
	</tr>
	</table>';
}

function show_editor_antigo($name,$textarea,$w,$h)
{
	global $caminho_bt_editor2,$usa_css,$site_http_url;
	?>
	<script>
    var <?=$name?>=new InnovaEditor("<?=$name?>");
    <?=$name?>.width="<?=$w?>";
    <?=$name?>.height="<?=$h?>";
    <?=$name?>.arrCustomButtons=[["Banner","modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_media.php',365,270)","Insert Banner/multimedia","btnBanner.gif"],
    ["Menu","modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_menu.php',365,270)","Insert Menu","btnMenus.gif"],
    ["Form2","modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_form.php',365,270)","Insert Form","btnForm.gif"],
	["Page2","modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_content.php',365,270)","Insert Page","btnPage.gif"],
	["Component","modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_special.php',365,270)","Insert Component","btnCustom1.gif"],
	["FlickrButton","modelessDialogShow('<?=$caminho_bt_editor2?>common/flickr.htm',730,500)","Insert Flickr Photo","btnFlickr.gif"],
	["YoutubeVideo2","modelessDialogShow('<?=$caminho_bt_editor2?>common/youtube.htm',500,355)","Insert Youtube Video","btnYoutubeVideo.gif"],
	["Mp3","modelessDialogShow('<?=$caminho_bt_editor2?>common/mp3.htm',340,125)","Insert Music","btnMusic.gif"]]
    <?=$name?>.toolbarMode=2;
    <?=$name?>.groups=[
    ["grpEdit","",["FullScreen","Print","Search","Undo","Redo","Cut","Copy","Paste","PasteWord","PasteText","FontName","FontSize","Strikethrough","Superscript",
	"Bold","Italic","Underline","ForeColor","BackColor","RemoveFormat","Indent","Outdent","Styles","StyleAndFormatting",
	"BRK","JustifyLeft","JustifyCenter","JustifyRight","JustifyFull","Numbering","Bullets",
	"Table","Guidelines","AutoTable","Hyperlink","InternalLink","Bookmark","Page2","Image","Banner","Menu","Form2",
	"Media","Flash","YoutubeVideo2","Mp3","FlickrButton","Characters","Line","Component","HTMLSource"]]
    ];
    <?php if($usa_css){?><?=$name?>.css="<?=$site_http_url?>layout/template_admin.css";<?php }?>
    <?=$name?>.cmdAssetManager="modalDialogShow('<?=$caminho_bt_editor2?>assetmanager/assetmanager.php',640,465)";
    <?=$name?>.cmdInternalLink="modelessDialogShow('<?=$caminho_bt_editor2?>scripts/insert_hiperurl.php',365,270)";
    <?=$name?>.customColors=["#ff4500","#ffa500","#808000","#4682b4","#1e90ff","#9400d3","#ff1493","#a9a9a9"];
    <?=$name?>.REPLACE("<?=$textarea?>");
</script><?php
}
