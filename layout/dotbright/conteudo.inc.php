<?php
if(isset($page_id))
{
	if(!defined('is_home')) echo BREADCRUMBS();
	if($GLOBALS['site_lang'] == 'pt')setlocale(LC_ALL,  'pt_PT.UTF8');
    else setlocale(LC_ALL,  'en_GB.UTF8');
    $fdata = strftime('%d %B %Y', strtotime($data));

	if($tipo==2 && $page_id != $GLOBALS['videos_page_id'])
	{
		// echo '<div class="titulo_main_news">NOTÍCIAS</div>';
		echo '<div class="divider-50"></div>';
		echo '<div class="news-img-content" style="">';
		// if(!isset($GLOBALS['ocultar_titulo']) || !$GLOBALS['ocultar_titulo'])echo '<div class="titulo_d_news">'.$page_name.'</div>';
		if(!isset($GLOBALS['ocultar_titulo']) || !$GLOBALS['ocultar_titulo']) echo '<div class="titulo_d">'.$page_name.'</div>';
		if($sub_page_name != '')echo '<div class="sub_titulo_d">'.$sub_page_name.'</div>';
		// echo '<div class="data_d_news">'.$fdata.'</div>';
		if(!empty($imgs))echo '<div class="images_d">'.$imgs.'</div>';
		echo '</div>';

	}
	else
	{
		if(!empty($imgs))
		{
			if($Img_estilo==1)
			{
				echo '<div class="images_d" style="float:left;max-width:50%;height:100vh;padding-right:10px">'.$imgs.'</div>';
			}
			else echo '<div class="images_d">'.$imgs.'</div>';
		}
		if(!isset($GLOBALS['ocultar_titulo']) || !$GLOBALS['ocultar_titulo'])echo '<h1 class="titulo_d">'.$page_name.'</h1>';
		if($sub_page_name != '')echo '<div class="sub_titulo_d">'.$sub_page_name.'</div>';
	}


	if(!$bootstrap || $bootstrap==1)show_content($page_id);

	if($bootstrap_p != '')
	{
		$colunas=explode(';',$bootstrap_p);
		echo '<div class="row">';
		$i=0;
		while($i<count($colunas))
		{
			echo '<div class="col-md-'.$colunas[$i].' col-sm-'.$colunas[$i].' col-xs-12">';
			if($i==0)put_content($Texto);
			if($i==1)put_content($Texto10);
			if($i==2)put_content($Texto11);
			if($i==3)put_content($Texto12);
			if($i==4)put_content($Texto13);
			if($i==5)put_content($Texto14);
			if($i==6)put_content($Texto15);
			if($i==7)put_content($Texto16);
			if($i==8)put_content($Texto17);
			if($i==9)put_content($Texto18);
			if($i==10)put_content($Texto19);
			if($i==11)put_content($Texto20);
			echo '</div>';
			$i++;
		}
		echo '</div>';
	}
	else
	{
		if($bootstrap==2)
		{
			echo '<div class="row">';
			echo '<div class="col-md-3 col-sm-3 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-8 col-sm-8 col-xs-12">';
			put_content($Texto10);
			echo '</div></div>';
		}
		if($bootstrap==3)
		{
			echo '<div class="row">';
			echo '<div class="col-md-6 col-sm-6 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-6 col-sm-6 col-xs-12">';
			put_content($Texto10);
			echo '</div></div>';
		}
		if($bootstrap==4)
		{
			echo '<div class="row">';
			echo '<div class="col-md-4 col-sm-4 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-4 col-sm-4 col-xs-12">';
			put_content($Texto10);
			echo '</div><div class="col-md-4 col-sm-4 col-xs-12">';
			put_content($Texto11);
			echo '</div></div>';
		}
		if($bootstrap==5)
		{
			echo '<div class="row">';
			echo '<div class="col-md-3 col-sm-6 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
			put_content($Texto10);
			echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
			put_content($Texto11);
			echo '</div><div class="col-md-3 col-sm-6 col-xs-12">';
			put_content($Texto12);
			echo '</div></div>';
		}
		if($bootstrap==6)
		{
			echo '<div class="row">';
			echo '<div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto10);
			echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto11);
			echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto12);
			echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto13);
			echo '</div><div class="col-md-2 col-sm-2 col-xs-12">';
			put_content($Texto14);
			echo '</div></div>';
		}
		if($bootstrap==7)
		{
			echo '<div class="row">';
			echo '<div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto10);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto11);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto12);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto13);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto14);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto15);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto16);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto17);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto18);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto19);
			echo '</div><div class="col-md-1 col-sm-1 col-xs-12">';
			put_content($Texto20);
			echo '</div></div>';
		}
	}
}
?>