<?php
	include 'settings.inc.php';

	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'pages',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('Data_modify',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Data_modify Date NOT NULL ');
		if(!in_array('mostra_img',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD mostra_img tinyint(1) NOT NULL ');
		if(!in_array('usa_css',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD usa_css tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Template',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Template tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('DefaultHome',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD DefaultHome tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('DefaultHome2',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD DefaultHome2 tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('PageEnter',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD PageEnter tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('PageExit',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD PageExit tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_topo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Destaque_topo tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_text',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Destaque_text tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_club',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Destaque_club tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Permanente',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Permanente tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Familia_noticias',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Familia_noticias tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Ticker',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Ticker tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('img_ext2',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD img_ext2 varchar(5) NOT NULL ');
		if(!in_array('img_ext3',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD img_ext3 varchar(5) NOT NULL ');
		if(!in_array('img_ext4',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD img_ext4 varchar(5) NOT NULL ');
		if(!in_array('img_extdoc',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD img_extdoc varchar(5) NOT NULL ');
		if(!in_array('bootstrap',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD bootstrap tinyint(1) NOT NULL ');
		if(!in_array('bootstrap_p',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD bootstrap_p varchar(50) NOT NULL ');
		if(!in_array('script',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD script tinyint(1) NOT NULL ');
		if(!in_array('Interna',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Interna tinyint(1) NOT NULL ');
		if(!in_array('Img_estilo',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD Img_estilo int(2) NOT NULL ');
		if(!in_array('menus',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD menus tinyint(1) NOT NULL ');
	}

	$fields2=db_list_fields($DB_name,'pages_lang',$db_link);
	$columns2=db_num_fields($fields2);
	if($columns2>0)
	{
		$field_array2=array();
		for($i=0;$i < $columns2;$i++){$field_array2[]=db_field_name($fields2,$i);}
		if(!in_array('ID_PAI2',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD ID_PAI2 int(11) NOT NULL ');
		if(!in_array('Lang3',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Lang3 char(3) NOT NULL ');
		if(!in_array('SEO_title',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD SEO_title varchar(255) NOT NULL ');
		if(!in_array('SEO_description',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD SEO_description varchar(255) NOT NULL ');
		if(!in_array('SEO_keywords',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD SEO_keywords varchar(255) NOT NULL ');
		if(!in_array('Cabecalho2',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Cabecalho2 varchar(255) NOT NULL ');

		if(!in_array('Texto10',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto10 longtext NULL ');
		if(!in_array('Texto11',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto11 longtext NULL ');
		if(!in_array('Texto12',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto12 longtext NULL ');
		if(!in_array('Texto13',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto13 longtext NULL ');
		if(!in_array('Texto14',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto14 longtext NULL ');
		if(!in_array('Texto15',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto15 longtext NULL ');
		if(!in_array('Texto16',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto16 longtext NULL ');
		if(!in_array('Texto17',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto17 longtext NULL ');
		if(!in_array('Texto18',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto18 longtext NULL ');
		if(!in_array('Texto19',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto19 longtext NULL ');
		if(!in_array('Texto20',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD Texto20 longtext NULL ');
		if(!in_array('SEO_img', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE pages_lang ADD SEO_img varchar (5)');  }
	}

	$fields=db_list_fields($DB_name,'drafts',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('Data_modify',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Data_modify Date NOT NULL ');
		if(!in_array('mostra_img',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD mostra_img tinyint(1) NOT NULL ');
		if(!in_array('usa_css',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD usa_css tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Template',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Template tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('DefaultHome',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD DefaultHome tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('DefaultHome2',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD DefaultHome2 tinyint(1) NOT NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('PageEnter',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD PageEnter tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('PageExit',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD PageExit tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_topo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Destaque_topo tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_text',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Destaque_text tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Destaque_club',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Destaque_club tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Permanente',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Permanente tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Familia_noticias',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Familia_noticias tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('Ticker',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Ticker tinyint(1) NULL '); if(!$res)echo db_error($db_link).$sql.'<br>';}
		if(!in_array('bootstrap',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD bootstrap tinyint(1) NOT NULL ');
		if(!in_array('bootstrap_p',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE pages ADD bootstrap_p varchar(50) NOT NULL ');
		if(!in_array('script',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD script tinyint(1) NOT NULL ');
		if(!in_array('Interna',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts ADD Interna tinyint(1) NOT NULL ');
	}
	$fields2=db_list_fields($DB_name,'drafts_lang',$db_link);
	$columns2=db_num_fields($fields2);
	if($columns2>0)
	{
		$field_array2=array();
		for($i=0;$i < $columns2;$i++){$field_array2[$i]=db_field_name($fields2,$i);}
		if(!in_array('ID_PAI2',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD ID_PAI2 int(11) NOT NULL ');
		if(!in_array('Lang3',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Lang3 char(3) NOT NULL ');
		if(!in_array('SEO_title',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD SEO_title varchar(255) NOT NULL ');
		if(!in_array('SEO_description',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD SEO_description varchar(255) NOT NULL ');
		if(!in_array('SEO_keywords',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD SEO_keywords varchar(255) NOT NULL ');
		if(!in_array('Cabecalho2',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Cabecalho2 varchar(255) NOT NULL ');

		if(!in_array('Texto10',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto10 longtext NULL ');
		if(!in_array('Texto11',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto11 longtext NULL ');
		if(!in_array('Texto12',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto12 longtext NULL ');
		if(!in_array('Texto13',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto13 longtext NULL ');
		if(!in_array('Texto14',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto14 longtext NULL ');
		if(!in_array('Texto15',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto15 longtext NULL ');
		if(!in_array('Texto16',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto16 longtext NULL ');
		if(!in_array('Texto17',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto17 longtext NULL ');
		if(!in_array('Texto18',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto18 longtext NULL ');
		if(!in_array('Texto19',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto19 longtext NULL ');
		if(!in_array('Texto20',$field_array2))$res=Runquery($db_link,$DB_name,'ALTER TABLE drafts_lang ADD Texto20 longtext NULL ');
	}

	if(!is_dir('../../files/bk')) mkdir('../../files/bk',0777);
	// Reordenar
	if($flag==2)
	{
		foreach($_POST as $varName=> $value)
		{
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$var='field_'.$str_id;
				$dv=$$var;
				$sql="UPDATE pages SET ordem='$dv' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// apagar
	if($flag==1)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$res=Runquery($db_link,$DB_name,"delete from pages WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql.'<br>';
				$res=Runquery($db_link,$DB_name,"delete from pages_lang WHERE ID_PAI=$str_id");
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// mover
	if($flag==3)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET ID_ORG='$m_familia' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// tornar visivel
	if($flag==4)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Visible='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// tornar invisivel
	if($flag==5)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Visible='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// proteger
	if($flag==6)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Protected='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// desproteger
	if($flag==7)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Protected='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// pesquisavel
	if($flag==8)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Pesquisavel='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// nao pesquisavel
	if($flag==9)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Pesquisavel='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// em destaque
	if($flag==10)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Destaque='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		};
	}
	// destaque off
	if($flag==11)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,'_')+1);
			if($str_id > 0 && substr_count($varName,'chk_') > 0)
			{
				$sql="UPDATE pages SET Destaque='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql.'<br>';
			}
		}
	}
	if($_POST && $_POST['m_pesquisa'])
	{
		$registos=SearchRecords($_POST['m_pesquisa']);
		$reg='';
		while(!$registos->EOF())
		{
			if($reg)$reg.=',';
			$reg.=$registos->db_data['ID_PAI'];
			$registos->db_next();
		}
		$registos->db_close();
	}
	include '../layout/header.inc.php';
	include '../layout/body.inc.php';

	?>
	<form action="list.php?tipo=<? echo $tipo?>" method="post" name="lista_paginas" onSubmit="return checkFields()">
	<input type="hidden" value="<? echo $tipo?>" name="tipo" />
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><?
			include '../layout/menus.inc.php';
			show_page_tools(filter);
			if($reg || $_POST['m_pesquisa']) show_content_from_search($reg,$tipo); else show_content_list('0','-5','0','0','0',$tipo);
			show_page_bottom_tools($tipo);?>
		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>
	</form>
<? 	include '../layout/bottom.inc.php';?>