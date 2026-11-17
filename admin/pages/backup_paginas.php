<?php
	include 'settings.inc.php';
	if($task=='apagar_draft'){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}

	$retorno="list.php";
	

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(isset($_GET['ok']) && intval($_GET['ok']) == 1)
	{
		foreach ($pages as $ID => $value) 
		{
			$sql ="INSERT INTO `pages_backup`(`ID`, `ID_ORG`, `Data`, `Ordem`, `img_ext`, `img_ext2`, `Visible`, `Estado`, `Tipo`, `Protected`, `sim`, `Data_modify`, `mostra_img`, `m_editor`, `m_css`, `Template`, `DefaultHome`, `usa_css`, `PageEnter`, `PageExit`, `failed_attempts`, `ID_Grupo`, `Pesquisavel`, `Destaque`, `Data1`, `Data2`, `Destaque_topo`, `Destaque_text`, `Destaque_club`, `Permanente`, `Familia_noticias`, `Ticker`, `Hidden`, `IS_MENU`, `IS_MENU2`, `APP`, `Color`, `img_ext3`, `img_ext4`, `img_extdoc`, `DefaultHome2`, `bootstrap`, `script`, `Interna`)
		 	SELECT `ID`, `ID_ORG`, `Data`, `Ordem`, `img_ext`, `img_ext2`, `Visible`, `Estado`, `Tipo`, `Protected`, `sim`, `Data_modify`, `mostra_img`, `m_editor`, `m_css`, `Template`, `DefaultHome`, `usa_css`, `PageEnter`, `PageExit`, `failed_attempts`, `ID_Grupo`, `Pesquisavel`, `Destaque`, `Data1`, `Data2`, `Destaque_topo`, `Destaque_text`, `Destaque_club`, `Permanente`, `Familia_noticias`, `Ticker`, `Hidden`, `IS_MENU`, `IS_MENU2`, `APP`, `Color`, `img_ext3`, `img_ext4`, `img_extdoc`, `DefaultHome2`, `bootstrap`, `script`, `Interna` FROM `pages` WHERE pages.ID = ($ID);";
		 		$res=Runquery($db_link,$DB_name,$sql);
		 		if($res)
		 		{
		 			$sql ="DELETE FROM pages WHERE ID = $ID";
		 			$res=Runquery($db_link,$DB_name,$sql);
		 		}		
		}

		show_box_OK(1,$retorno);
	}	
	else show_box_ERROR(1,$retorno);