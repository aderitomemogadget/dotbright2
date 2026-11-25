<?php
global $site_shell_url;
include $site_shell_url."/rmas/layout/elements.inc.php";
	$m=0;
	$tabs[$m][0]="HOME";
	$tabs[$m][1]=$site_http_url."rmas/index.php";
	$tabs[$m][2]="home.png";
	$tabs[$m][3]="";
	$m++;

	$tabs[$m][0]="RMAS";
	$tabs[$m][1]=$site_http_url."rmas/rmas/list.php?ID=0&ID_ORG=0";
	$tabs[$m][2]="rmas.png";
	$tabs[$m][3]="";
	$m++;

	$tabs[$m][0]="CLIENTES";
	$tabs[$m][1]=$site_http_url."rmas/clientes/list.php";
	$tabs[$m][2]="clientes.png";
	$tabs[$m][3]="";
	$m++;

	$tabs[$m][0]="LOCALIZAÇÕES";
	$tabs[$m][1]=$site_http_url."rmas/localizacoes/list.php";
	$tabs[$m][2]="media.png";
	$tabs[$m][3]="";
	$m++;

	$tabs[$m][0]="LOJAS";
	$tabs[$m][1]=$site_http_url."rmas/rma_lojas/list.php";
	$tabs[$m][2]="store.png";
	$tabs[$m][3]="";
	$m++;
	
	$tabs[$m][0]="ESTADOS RMAS";
	$tabs[$m][1]=$site_http_url."rmas/sa_estados_rmas/list.php";
	$tabs[$m][2]="filters.png";
	$tabs[$m][3]="";
	$m++;
		
	show_menus($tabs,$tabs2);
?>