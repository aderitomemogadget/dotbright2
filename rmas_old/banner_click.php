<?
	$includes_path='core/';
	include $includes_path.'includes.inc.php';
	regista_clique($id);
	regista2($m_from);	
	if(!$url) $url = $site_http_url;
	echo '<script type="text/javascript">open("'.$url.'","_self");</script>';
?>