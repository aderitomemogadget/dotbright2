<?php

include 'login.inc.php';
require 'settings_modulos.inc.php';


global $admin_id,$admin_user,$site_http_url,$ss_id, $tem_area_reservada, $tem_loja,$tem_sms;
$mens = Bottom_count_msgs("",$ss_id);

if($GLOBALS['admin_lang'] == 'en') $txt_lingua_on = 'A trabalhar na língua Inglês';
elseif($GLOBALS['admin_lang'] == 'es') $txt_lingua_on = 'A trabalhar na língua Espanhol';
elseif($GLOBALS['admin_lang'] == 'fr') $txt_lingua_on = 'A trabalhar na língua Francês';
else $txt_lingua_on = 'A trabalhar na língua Português';

global $admin_id;
$dados = getDadosAdministradorAdmin($admin_id);

$dbAdminModule = basename(dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$dbAdminScreen = basename($_SERVER['SCRIPT_NAME'] ?? '');
$dbAdminClasses = ' db-admin';
if ($dbAdminModule === 'pages') $dbAdminClasses .= ' db-admin-pages';
if ($dbAdminModule === 'products') $dbAdminClasses .= ' db-admin-products';
if (in_array($dbAdminModule, array('pages','products'), true) && in_array($dbAdminScreen, array('list.php','products_families.php','avancada.php'), true)) $dbAdminClasses .= ' db-admin-browse';
if (($dbAdminModule === 'pages' && $dbAdminScreen === 'pagina.php') || ($dbAdminModule === 'products' && $dbAdminScreen === 'products.php')) $dbAdminClasses .= ' db-admin-content';
echo '<div id="page-container" class="fade page-sidebar-fixed page-header-fixed in'.$dbAdminClasses.'">';

echo comprime('<div class="topo">');
//echo comprime('<div class="admint1">');

echo comprime('<div id="header" class="header navbar navbar-default navbar-fixed-top">');
echo comprime('<div class="navbar-header">
					<a href="'.$site_http_url.'admin/" class="navbar-brand"><span class="navbar-logo"><img src="'.push_confs_admin('LOGOTIPO_PRINCIPAL').'"></span></a>
					<button type="button" class="navbar-toggle" data-click="sidebar-toggled">
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
						<span class="icon-bar"></span>
					</button>
				<div class="info info-responsive" style="display: inline-block; padding: 20px;">

					<!-- <small>Super Administrator</small> -->
					<div class="name dropdown">
		                <a href="javascript:;" data-toggle="dropdown" aria-expanded="false">'.$dados['Nome'].' <b class="caret"></b></a>
                        <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 20px, 0px);">
                            <li><a href="'.$site_http_url.'admin/users/pagina.php?ID='.$admin_id.'">Editar Perfil</a></li>
                            <li class="divider"></li>
                            <li><a href="'.$site_http_url.'admin/logout.php">Log Out</a></li>
                        </ul>
		            </div>
				</div>
				</div>');
//echo comprime('<div class="bemvindo"><br>&nbsp;&nbsp;'.write_lang('key_bem_vindo','Bem Vindo').' '.$admin_user.'&nbsp;</div>');
// echo comprime('<div class="bemvindo"></div>');
echo comprime('<div class="logout">');

if($GLOBALS['tem_admin_alertas'] == 1)echo comprime(show_alertas());



if(isset($dados['Permissoes'][8]) && $dados['Permissoes'][8]==1)
{
	echo comprime('<div class="pesquisa_rapida">');
	echo comprime(pesquisa_rapida());
	echo comprime('</div>');
}
if(isset($dados['Permissoes'][7]) && $dados['Permissoes'][7]==1){ echo comprime('<div class="linguas">'); echo comprime(edit_chose_lang()); echo comprime('</div>');}
echo comprime('<div class="info info-desktop" style="display: inline-block; padding: 20px;">

					<!-- <small>Super Administrator</small> -->
					<div class="name dropdown">
		                <a href="javascript:;" data-toggle="dropdown" aria-expanded="false">'.$dados['Nome'].' <b class="caret"></b></a>
                        <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 20px, 0px);">
                            <li><a href="'.$site_http_url.'admin/users/pagina.php?ID='.$admin_id.'">Editar Perfil</a></li>
                            <li class="divider"></li>
                            <li><a href="'.$site_http_url.'admin/logout.php">Log Out</a></li>
                        </ul>
		            </div>
				</div>');
/*if(isset($dados['Permissoes'][6]) && $dados['Permissoes'][6]==1) echo comprime('<a class="navlink" title="configurar moeda" href="'.$site_http_url.'admin/currency/list.php"><img src="'.$site_http_url.'admin/layout/currency.png" border=0></a>');
if(isset($dados['Permissoes'][5]) && $dados['Permissoes'][5]==1) echo comprime('<a class="navlink" title="'.write_lang('key_ver_estatiticas','Ver Estatísticas').'" href="'.$site_http_url.'admin/stats/produtos.php"><img src="'.$site_http_url.'admin/layout/stats.png" border=0 /></a>');
if(isset($dados['Permissoes'][3]) && $dados['Permissoes'][3]==1) echo comprime('<a class="navlink" title="'.write_lang('key_definicoes_plataforma','Definições da Plataforma').'" href="'.$site_http_url.'admin/modulos_config_site/pagina.php"><img src="'.$site_http_url.'admin/layout/settings1.png" border=0 /></a>');
if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) echo comprime('<a class="navlink" title="'.write_lang('key_mensagens_internas','Mensagens Internas').'" href="'.$site_http_url.'admin/messages/index.php"><img src="'.$site_http_url.'admin/layout/messages.png" border=0 /></a>');
if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<a class="navlink" title="'.write_lang('key_lista_de_utilizadores','Lista de Utilizadores').'" href="'.$site_http_url.'admin/users/list.php"><img src="'.$site_http_url.'admin/layout/perfil.png" border=0 /></a>');
*/
// echo comprime('<a class="navlink" title="'.write_lang('key_logout','Logout').'" href="'.$site_http_url.'admin/logout.php"><img src="'.$site_http_url.'admin/layout/logout.png" border=0 style="margin-top:8px" /></a>');




if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) if($mens) echo comprime('<div style="position:relative; text-align:left"><div style="position:absolute; top:-40px; left:420px; width:16px; height:14px; border:1px solid red; background-color:#333">'.$mens.'</div></div>');
echo comprime('</div></div></div>');




	$m=0;
	$tabs[$m][0]="ATUALIZAR CACHE";
	$tabs[$m][1]=$site_http_url."admin/cache/update.php";
	$tabs[$m][2]='fa fa-laptop';
	$tabs[$m][3]='';
	$m++;
	
	$tabs[$m][0]="HOME";
	$tabs[$m][1]=$site_http_url."admin/index.php";
	$tabs[$m][2]='fa fa-laptop';
	$tabs[$m][3]='';
	$m++;
	if(isset($dados['Permissoes'][12]) && $dados['Permissoes'][12]==1){
		$tabs[$m][0]="Menus";
		$tabs[$m][1]=$site_http_url."admin/menus/list.php";
		$tabs[$m][2]='fas fa-bars';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
	{
		$tabs[$m][0]="Páginas";
		$tabs[$m][1]=$site_http_url."admin/pages/list.php";
		$tabs[$m][2]='far fa-copy';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($GLOBALS['tem_timeline']) && $GLOBALS['tem_timeline']==1)
	{
		if(isset($dados['Permissoes'][32]) && $dados['Permissoes'][32]==1)
		{
			$tabs[$m][0]="TimeLine";
			$tabs[$m][1]=$site_http_url."admin/timeline/list.php";
			$tabs[$m][2]='far fa-copy';
			$tabs[$m][3]='';
			$m++;
		}
	}
	if(isset($GLOBALS['noticias_por_catalogo']) && $GLOBALS['noticias_por_catalogo']==1)
	{
		if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
		{
			$tabs[$m][0]="Notícias";
			$tabs[$m][1]=$site_http_url."admin/news1/list.php";
			$tabs[$m][2]='far fa-newspaper';
			$tabs[$m][3]='';
			$m++;
		}
	}
	if(isset($dados['Permissoes'][13]) && $dados['Permissoes'][13]==1)
	{
		$tabs[$m][0]="Formulários";
		$tabs[$m][1]=$site_http_url."admin/forms/list.php";
		$tabs[$m][2]='fab fa-wpforms';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($GLOBALS['imoveis_por_catalogo']) && $GLOBALS['imoveis_por_catalogo']==1)
	{
		if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
		{
			$tabs[$m][0]="Gerir Imóveis";
			$tabs[$m][1]=$site_http_url."admin/imoveis/list.php";
			$tabs[$m][2]='far fa-newspaper';
			$tabs[$m][3]='';
			$m2=0;
			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Listagem";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/imoveis/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/imoveis/list.php";
				$m2++;
			}
			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Tipologia";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/tipologia/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/tipologia/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Condição";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/condicao/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/condicao/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Orientação Solar";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/orientacao_solar/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/orientacao_solar/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Tipo de negócio";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/tipo_negocio/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/tipo_negocio/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Localização";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/localizacao/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/localizacao/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Distrito";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/distrito/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/distrito/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Concelho";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/concelho/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/concelho/list.php";
				$m2++;
			}

			if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
			{
				$tabs2[$m][$m2][0]="Freguesia";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/freguesia/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/freguesia/list.php";
				$m2++;
			}
			/*
			if($GLOBALS['tem_carrinho']==1 && isset($dados['Permissoes'][15]) && $dados['Permissoes'][15]==1){
				$tabs2[$m][$m2][0]="E-mail";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/templates_email/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/templates_email/list.php";
				$m2++;
			}*/

			$m++;
		}
	}
	if(isset($dados['Permissoes'][11]) && $dados['Permissoes'][11]==1)
	{
		$tabs[$m][0]="Media,Banners,Google";
		$tabs[$m][1]=$site_http_url."admin/media/list.php";
		$tabs[$m][2]='fas fa-video';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($GLOBALS['tem_publications']) && $GLOBALS['tem_publications']==1)
	{
		$tabs[$m][0]="Publicações";
		$tabs[$m][1]=$site_http_url."admin/publications/list.php";
		$tabs[$m][2]='fas fa-video';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($GLOBALS['tem_ficheiros']) && $GLOBALS['tem_ficheiros']==1)
	{
		$tabs[$m][0]="Gestão documental";
		$tabs[$m][1]=$site_http_url."admin/ficheiros/list.php";
		$tabs[$m][2]='fas fa-video';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($dados['Permissoes'][31]) && $dados['Permissoes'][31]==1)
	{
		$tabs[$m][0]="Componentes";
		$tabs[$m][1]=$site_http_url."admin/modulos/list.php";
		$tabs[$m][2]='fas fa-puzzle-piece';
		$tabs[$m][3]='';
		$m++;
	}
	if(isset($GLOBALS['tem_marketing']) && $GLOBALS['tem_marketing']==1)
	{
		if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
		{
			$tabs[$m][0]="Marketing e notificações";
			$tabs[$m][1]=$site_http_url."admin/marketing/pagina.php";
			$tabs[$m][2]='fas fa-bullhorn';
			$tabs[$m][3]='';
			$m++;
		}
	}
	/*if(isset($GLOBALS['tem_webmail']) && $GLOBALS['tem_webmail']==1)
	{
		if(isset($dados['Permissoes'][10]) && $dados['Permissoes'][10]==1)
		{
			$tabs[$m][0]="Webmail";
			$tabs[$m][1]=$site_http_url."admin/webmail/index.php";
			$tabs[$m][2]='fas fa-bullhorn';
			$tabs[$m][3]='';
			$m++;
		}
	}*/
	if( (isset($dados['Permissoes'][14]) && $dados['Permissoes'][14]==1) || (isset($dados['Permissoes'][15]) && $dados['Permissoes'][15]==1))
	{
		$tabs[$m][0]="Templates";
		$tabs[$m][1]=$site_http_url."admin/templates/list.php";
		$tabs[$m][2]='far fa-image';
		$tabs[$m][3]='';
		$m2=0;
			if(isset($dados['Permissoes'][14]) && $dados['Permissoes'][14]==1)
			{
				$tabs2[$m][$m2][0]="Website";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/templates/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/templates/list.php";
				$m2++;
			}
			if($GLOBALS['tem_carrinho']==1 && isset($dados['Permissoes'][15]) && $dados['Permissoes'][15]==1)
			{
				$tabs2[$m][$m2][0]="E-mail";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/templates_email/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/templates_email/list.php";
				$m2++;
			}
			/*if(isset($dados['Permissoes'][15]) && $dados['Permissoes'][15]==1){
				$tabs2[$m][$m2][0]="SITE TEMPLATES";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/templates_site/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/templates_site/list.php";
				$m2++;
			}*/
		$m++;
	}
	if(isset($dados['Permissoes'][16]) && $dados['Permissoes'][16]==1)
	{
		$tabs[$m][0]="Newsletter";
		$tabs[$m][1]=$site_http_url."admin/newsletter/list.php";
		$tabs[$m][2]='far fa-paper-plane';
		$tabs[$m][3]='';
		$m++;
	}
	/*
	if(isset($dados['Permissoes'][17]) && $dados['Permissoes'][17]==1){
		$tabs[$m][0]='CHAT';
		$tabs[$m][1]='javascript:;';
		$tabs[$m][2]='chat.png';
		$tabs[$m][3]='window.open(\''.$site_http_url.'chat/chat_admin.php\',\'chat_admin\',\'menubar=1,resizable=0,width=503,height=248\');';
		$m++;
	}
	*/
	if($tem_area_reservada && ((isset($dados['Permissoes'][18]) && $dados['Permissoes'][18]==1) || (isset($dados['Permissoes'][19]) && $dados['Permissoes'][19]==1) ))
	{
		$tabs[$m][0]="Clientes";
		$tabs[$m][1]="#";
		$tabs[$m][2]='fas fa-user-tie';
		$tabs[$m][3]='';

			$m2=0;
			if(isset($dados['Permissoes'][18]) && $dados['Permissoes'][18]==1)
			{
				$tabs2[$m][$m2][0]="Clientes";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/clientes/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/clientes/list.php";
				$m2++;
			}
			if(isset($dados['Permissoes'][19]) && $dados['Permissoes'][19]==1)
			{
				$tabs2[$m][$m2][0]="Grupos";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/grupos_clientes/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/grupos_clientes/list.php";
				$m2++;
			}
			if(isset($GLOBALS['tem_instituicoes']) && $GLOBALS['tem_instituicoes']==1)
			{
				$tabs2[$m][$m2][0]="Instituicoes";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/instituicoes/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/instituicoes/list.php";
				$m2++;
			}
			if(isset($GLOBALS['tem_departamentos']) && $GLOBALS['tem_departamentos']==1)
			{
				$tabs2[$m][$m2][0]="Departamentos";
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/departamentos/pagina.php?ID=0&ID_ORG=0&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/departamentos/list.php";
				$m2++;
			}			
			/*if(isset($dados['Permissoes'][18]) && $dados['Permissoes'][18]==1){
				$tabs2[$m][$m2][0]="Afiliados";
				$tabs2[$m][$m2]['novo'] = "";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/afiliados/list.php";
				$m2++;
			}*/
		$m++;
	}
	if( (isset($dados['Permissoes'][8]) && $dados['Permissoes'][8]==1)  && $tem_loja)
	{
		$tabs[$m][0]="Produtos";
		$tabs[$m][1]="#";
		$tabs[$m][2]='fas fa-dolly';
		$tabs[$m][3]='';
			$m2=0;

			$tabs2[$m][$m2][0]="Catálogo de Produtos";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = $site_http_url."admin/products/products.php?ID=0";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/products/list.php";
			$m2++;

            $tabs2[$m][$m2][0]='Pedidos de reparação';
            $tabs2[$m][$m2][1]='';
            $tabs2[$m][$m2]['list']=$site_http_url.'admin/products/repair_orders.php';
            $m2++;

			if(isset($GLOBALS['tem_importar_medida']) && $GLOBALS['tem_importar_medida']==1)
			{
				$tabs2[$m][$m2][0]="Importador de Produtos";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/importador_medida/import.php?ID=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/importador_medida/import.php";
				$m2++;
			}

			if(isset($GLOBALS['tem_importar_filtros']) && $GLOBALS['tem_importar_filtros']==1)
			{
				$tabs2[$m][$m2][0]="Importador de Filtros";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/importador_filtros/import.php?ID=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/importador_filtros/import.php";
				$m2++;

				$tabs2[$m][$m2][0]="Importador Produtos com Filtros";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/importadores/import_export/import.php?ID=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/importadores/import_export/import.php";
				$m2++;

				$tabs2[$m][$m2][0]="Exportador Produtos com Filtros";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/importadores/import_export/export.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/importadores/import_export/export.php";
				$m2++;
			}

			$tabs2[$m][$m2][0]="Marcas";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = $site_http_url."admin/brands/pagina.php?ID=0";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/brands/list.php";
			$m2++;

			$P_CONF=unserialize(PROD_CONF);
			if($P_CONF['tem_servicos_associados'] == 1)
			{
				$tabs2[$m][$m2][0]="Serviços complementares";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/servicos/products.php?garantia_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/servicos/list.php";
				$m2++;
			}

			if($P_CONF['tem_varios_tempos_entrega'] == 1)
			{
				$tabs2[$m][$m2][0]="Tempos de entrega";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/entregas/products.php?garantia_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/entregas/list.php";
				$m2++;
			}
			if(isset($GLOBALS['sincroniza_site']) && $GLOBALS['sincroniza_site']==1)
			{
				$tabs2[$m][$m2][0]="SYNCRONISAR COM ".$GLOBALS['sincroniza_site'];
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_sincroniza/pagina.php?ID=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_sincroniza/list.php";
				$m2++;
			}
			if(isset($GLOBALS['tem_exportador']) && $GLOBALS['tem_exportador']==1)
			{
				$tabs2[$m][$m2][0]="Categorização/exportar";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sales_margins/pagina.php?ID=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sales_margins/list.php";
				$m2++;
			}
			if($GLOBALS['carrinho_conf']['tem_extensao_garantia'] == 1)
			{
				$tabs2[$m][$m2][0]="Extensão Garantia";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_extensao_garantia/products.php?garantia_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_extensao_garantia/list.php";
				$m2++;
			}
			if($GLOBALS['carrinho_conf']['tem_servicos_extra'] == 1)
			{
				$tabs2[$m][$m2][0]="Serviços extra";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_servicos_extra/products.php?garantia_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_servicos_extra/list.php";
				$m2++;
			}
			if($GLOBALS['carrinho_conf']['tem_custos_logisticos'] == 1)
			{
				$tabs2[$m][$m2][0]="Custos logisticos";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_custos_logisticos/products.php?garantia_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_custos_logisticos/list.php";
				$m2++;
			}
			if(isset($GLOBALS['produtos_componentes']) && $GLOBALS['produtos_componentes']==1)
			{
				$tabs2[$m][$m2][0]="Componentes";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/products_components/pagina.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/products_components/list.php";
				$m2++;
			}
			if(isset($GLOBALS['produtos_extras']) && $GLOBALS['produtos_extras']==1)
			{
				$tabs2[$m][$m2][0]="Extras";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/products_extras/pagina.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/products_extras/list.php";
				$m2++;
			}
			if(isset($GLOBALS['produtos_medidas']) && $GLOBALS['produtos_medidas']==1)
			{
				$tabs2[$m][$m2][0]="Medidas";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/products_medidas/pagina.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/products_medidas/list.php";
				$m2++;
			}
			if(isset($GLOBALS['produtos_tipos']) && $GLOBALS['produtos_tipos']==1)
			{
				$tabs2[$m][$m2][0]="Tipos";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/products_tipos/pagina.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/products_tipos/list.php";
				$m2++;
			}
			if(isset($GLOBALS['tabela_medidas']) && $GLOBALS['tabela_medidas']==1)
			{
				$tabs2[$m][$m2][0]="Tabela de medidas";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/tabela_medidas/pagina.php";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/tabela_medidas/list.php";
				$m2++;
			}
			/*$tabs2[$m][$m2][0]="Logo Marcas Home";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/brands/list_destaques.php";
			$m2++;


			$tabs2[$m][$m2][0]="Amostras";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/brands/list_offers.php";
			$m2++;

			$tabs2[$m][$m2][0]="Prod. Destaques Home";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/products/list_destaques.php";
			$m2++;

			$tabs2[$m][$m2][0]="Prod. Promoções Home";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/products/list_promocoes.php";
			$m2++;

			$tabs2[$m][$m2][0]="Produtos Extra Loja";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/products/list_extra.php";
			$m2++;
			*/

			/*$tabs2[$m][$m2][0]="Testemunhos";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = "empty";
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/testemunhos/list.php";
			$m2++;
			*/

			// $tabs2[$m][$m2][0]="IMPORTADOR DE IMAGENS";
			// $tabs2[$m][$m2][1]='';
			// $tabs2[$m][$m2]['novo'] = "empty";
			// $tabs2[$m][$m2]['list'] = $site_http_url."admin/importadores/importacoes.php";
			// $m2++;

			// $tabs2[$m][$m2][0]="IMPORTADOR XLSX";
			// $tabs2[$m][$m2][1]='';
			// $tabs2[$m][$m2]['novo'] = "empty";
			// $tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_importador/import.php";
			// $m2++;
		$m++;
	}/**
	if(isset($dados['Permissoes'][11]) && $dados['Permissoes'][11]==1)
	{

		$tabs[$m][0]="Portfolio";
		$tabs[$m][1]=$site_http_url."admin/products/list.php";
		$tabs[$m][2]='fas fa-camera';
		$tabs[$m][3]='';
		$m++;

	}
	/**/

	if(isset($GLOBALS['portfolio3']) && $GLOBALS['portfolio3']==1)
	{
		$tabs[$m][0]="Portfolio";
		$tabs[$m][1]=$site_http_url."admin/portfolio3/list.php";
		$tabs[$m][2]='fas fa-camera';
		$tabs[$m][3]='';
		$m++;
	}

	if((isset($dados['Permissoes'][20]) && $dados['Permissoes'][20]==1) && $tem_loja)
	{
		$tabs[$m][0]="Filtros";
		$tabs[$m][1]=$site_http_url."admin/filters/list.php";
		$tabs[$m][2]='fas fa-filter';
		$tabs[$m][3]='';
		$m++;
	}
	if( ((isset($dados['Permissoes'][21]) && $dados['Permissoes'][21]==1) || (isset($dados['Permissoes'][22]) && $dados['Permissoes'][22]==1)|| (isset($dados['Permissoes'][23]) && $dados['Permissoes'][23]==1)|| (isset($dados['Permissoes'][24]) && $dados['Permissoes'][24]==1)|| (isset($dados['Permissoes'][25]) && $dados['Permissoes'][25]==1)) && $tem_loja)
	{
		$tabs[$m][0]="Loja";
		$tabs[$m][1]="#";
		$tabs[$m][2]='fas fa-shopping-cart';
		$tabs[$m][3]='';

			$m2=0;
			if(isset($dados['Permissoes'][21]) && $dados['Permissoes'][21]==1)
			{
				$tabs2[$m][$m2][0]="Países";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/country_zones/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/country_zones/list.php?dp=".uniqid('');
				$m2++;
			}
			if(isset($dados['Permissoes'][22]) && $dados['Permissoes'][22]==1)
			{
				$tabs2[$m][$m2][0]="Métodos de pagamento";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/metodos_pagamento/list.php?dp=".uniqid('');
				$m2++;
			}
			if(isset($dados['Permissoes'][23]) && $dados['Permissoes'][23]==1)
			{
				$tabs2[$m][$m2][0]="Métodos de envio";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/metodos_expedicao/list.php?dp=".uniqid('');
				$m2++;
			}
			if(isset($dados['Permissoes'][24]) && $dados['Permissoes'][24]==1)
			{
				$tabs2[$m][$m2][0]="Portes";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/".$GLOBALS['menu_portes']."/pagina.php?dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/".$GLOBALS['menu_portes']."/list.php?dp=".uniqid('');
				$m2++;
				$tabs2[$m][$m2][0]="Portes Gratuitos";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/portes_gratuitos/pagina.php?dp=".uniqid('');
				$m2++;
			}
			if(isset($GLOBALS['tem_entrega_prevista']) && $GLOBALS['tem_entrega_prevista']==1)
			{
				$tabs2[$m][$m2][0]="Prazo de Entrega Default";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/prazo_entrega_prevista/pagina.php?dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/prazo_entrega_prevista/pagina.php?dp=".uniqid('');
				$m2++;
			}
			/*if(isset($dados['Permissoes'][25]) && $dados['Permissoes'][25]==1){
				$tabs2[$m][$m2][0]="MARGENS";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sales_margins/pagina.php?margin_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sales_margins/list.php";
				$m2++;
			}*/
		$m++;
	}
	if((isset($dados['Permissoes'][9]) && $dados['Permissoes'][9]==1) && $tem_loja)
	{
		$tabs[$m][0]="Encomendas";
		$tabs[$m][1]=$site_http_url."admin/encomendas/list.php?dp=".uniqid('');
		$tabs[$m][2]='far fa-calendar-check';
		$tabs[$m][3]='';
		$m2=0;
            $tabs2[$m][$m2][0]='Livre resolução';
            $tabs2[$m][$m2][1]='';
            $tabs2[$m][$m2]['list']=$site_http_url.'admin/encomendas/withdrawals.php';
            $m2++;


			$tabs2[$m][$m2][0]="Encomendas de Clientes";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = $site_http_url."admin/encomendas/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/encomendas/list.php?dp=".uniqid('');
			$m2++;

			if(isset($GLOBALS['tem_encomenda_fornecedor']) && $GLOBALS['tem_encomenda_fornecedor']==1)
			{
				$tabs2[$m][$m2][0]="A Fornecedores";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/encomendas/products.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/encomendas/products.php?dp=".uniqid('');
				$m2++;
			}

			$tabs2[$m][$m2][0]="Estados das encomendas";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_estados_encomenda/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_estados_encomenda/list.php?dp=".uniqid('');
			$m2++;

			if(isset($GLOBALS['tem_msg_predefinidas']) && $GLOBALS['tem_msg_predefinidas']==1)
			{
				$tabs2[$m][$m2][0]="Mensagens pred.";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_msg_predefinidas/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_msg_predefinidas/list.php?dp=".uniqid('');
				$m2++;
			}

			$tabs2[$m][$m2][0]="Web Services";
			$tabs2[$m][$m2][1]='';
			$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sa_webservices_encomendas/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
			$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_webservices_encomendas/list.php?dp=".uniqid('');
			$m2++;

			if(isset($GLOBALS['tem_ctt_integracao']) && $GLOBALS['tem_ctt_integracao']==1)
			{
				$tabs2[$m][$m2][0]="CTT Export";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/ctt_expedicao/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/ctt_expedicao/list.php?dp=".uniqid('');
				$m2++;


				$tabs2[$m][$m2][0]="CTT Envios";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/ctt_logistica/import.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/ctt_logistica/import.php?dp=".uniqid('');
				$m2++;
			}
			if(isset($GLOBALS['tem_mrw_integracao']) && $GLOBALS['tem_mrw_integracao']==1)
			{
				$tabs2[$m][$m2][0]="MRW Config";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/mrw_config/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/mrw_config/list.php?dp=".uniqid('');
				$m2++;
				$tabs2[$m][$m2][0]="MRW Integração";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/mrw_expedicao/pagina.php?ID=0&ID_ORG=0&tipo=1&dp=".uniqid('');
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/mrw_expedicao/list.php?dp=".uniqid('');
				$m2++;
			}
		$m++;
	}
	if(isset($GLOBALS['tem_promocoes']) && $GLOBALS['tem_promocoes']==1)
	{
		if( ((isset($dados['Permissoes'][27]) && $dados['Permissoes'][27]==1) || (isset($dados['Permissoes'][28]) && $dados['Permissoes'][28]==1) || (isset($dados['Permissoes'][29]) && $dados['Permissoes'][29]==1)) && $tem_loja)
		{
			$tabs[$m][0]='Promoções';
			$tabs[$m][1]="#";
			$tabs[$m][2]='fas fa-percentage';
			$tabs[$m][3]='';
			$m2=0;
			if(isset($dados['Permissoes'][27]) && $dados['Permissoes'][27]==1)
			{
				$tabs2[$m][$m2][0]="Promoções";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = $site_http_url."admin/sales_promotions/pagina.php?promo_id=0";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sales_promotions/list.php";
				$m2++;
			}
			// if(isset($dados['Permissoes'][28]) && $dados['Permissoes'][28]==1)
			// {
			// 	$tabs2[$m][$m2][0]="VOUCHERS";
			// 	$tabs2[$m][$m2][1]='';
			// 	$tabs2[$m][$m2]['novo'] = $site_http_url."admin/vouchers/pagina.php?ID=0";
			// 	$tabs2[$m][$m2]['list'] = $site_http_url.'admin/vouchers/list.php';
			// 	$m2++;
			// }
			if(isset($dados['Permissoes'][29]) && $dados['Permissoes'][29]==1)
			{
				$tabs2[$m][$m2][0]="PONTOS";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = "empty";
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/pontos/pagina.php?dp=".uniqid('');
				$m2++;
			}
			$m++;
		}
	}
	if(isset($GLOBALS['tem_importadores']) && $GLOBALS['tem_importadores']==1)
	{
		if( ((isset($dados['Permissoes'][27]) && $dados['Permissoes'][27]==1) || (isset($dados['Permissoes'][28]) && $dados['Permissoes'][28]==1) || (isset($dados['Permissoes'][29]) && $dados['Permissoes'][29]==1)) && $tem_loja)
		{
			$tabs[$m][0]="Importadores";
			$tabs[$m][1]='';
			$tabs[$m][2]='fas fa-box-open';
			$tabs[$m][3]='';
			$m2=0;

			$i=0;
			$t=count($GLOBALS['lista_importadores']);
			while($i<$t)
			{
				$Supplier_ID_temp=$GLOBALS['lista_importadores'][$i]['id'];
				$myFile = dirname( __FILE__ )."/../../files/importar/analisar_csv".$Supplier_ID_temp.".txt";

				if(file_exists($myFile)) $tabs2[$m][$m2][0]=$GLOBALS['lista_importadores'][$i]['name'].' <i class="fas fa-cog fa-spin"></i>';
				else $tabs2[$m][$m2][0]=$GLOBALS['lista_importadores'][$i]['name'];


				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $GLOBALS['site_http_url'].'admin/'.$GLOBALS['lista_importadores'][$i]['url'];
				$m2++;

				$i++;
			}


			if(isset($GLOBALS['imp_xlsx']) && $GLOBALS['imp_xlsx']==1)
			{
				$tabs2[$m][$m2][0]="EXCEL - XLSX";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/sa_importador/import.php?dp=".uniqid('');
				$m2++;
			}

			//******************************/
			if(isset($GLOBALS['imp_CREDITO']) && $GLOBALS['imp_CREDITO']==1)
			{
				$tabs2[$m][$m2][0]="CREDITO";
				$tabs2[$m][$m2][1]='';
				$tabs2[$m][$m2]['novo'] = 'empty';
				$tabs2[$m][$m2]['list'] = $site_http_url."admin/importadores/credito/import.php?dp=".uniqid('');
				$m2++;
			}


			$m++;
		}
	}

	if((isset($dados['Permissoes'][35]) && $dados['Permissoes'][35]==1) && $tem_sms){
	    $tabs[$m][0]="SMS";
	    $tabs[$m][1]=$site_http_url."admin/sms_scheduler/list.php";
	    $tabs[$m][2]='fas fa-sms';
	    $tabs[$m][3]='';
	    $m++;
	}

	if((isset($dados['Permissoes'][35]) && (int)$dados['Permissoes'][35]===1) || (isset($dados['Permissoes'][1]) && (int)$dados['Permissoes'][1]===1)){
	    $tabs[$m][0]="RGPD";
	    $tabs[$m][1]=$site_http_url."admin/rgpd/pedidos.php";
	    $tabs[$m][2]='fas fa-cog';
	    $tabs[$m][3]='';
	    $m++;
	}


	if(isset($tem_menus_admin) && $tem_menus_admin>0)
	{
		$i=0;
		while($i<$tem_menus_admin)
		{
			$tabs[$m][0]=$menus_admin['nome'][$i];
		    $tabs[$m][1]=$menus_admin['url'][$i];
		    $tabs[$m][2]=$menus_admin['icon'][$i];
		    $tabs[$m][3]='';
		    $m++;
			$i++;
		}
	}


	if(isset($dados['Permissoes'][30]) && $dados['Permissoes'][30]==1){
		$tabs[$m][0]="EDITAR ONLINE";
		$tabs[$m][1]=$site_http_url."index.php?oe=1";
		$tabs[$m][2]='far fa-edit';
		$tabs[$m][3]='';
	}

function Bottom_count_msgs($folder,$user_id)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$admin_id;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
    $total=0;
	$sql="SELECT count(*) as total FROM mensagens WHERE ID_Cliente =$admin_id AND was_read=0 ";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	else $total=$rs->db_data["total"];
	$rs->db_close();
	return $total;
}

function show_menus($tabs,$tabs2='')
{
	global $site_http_url;
	$actual_link = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
	$actual_link=substr($actual_link,0,strrpos($actual_link,'/'));

	$i=0;
	$t = count($tabs);
	if($t>0)
	{
		while($i < $t)
		{
			$active='';
			$tem_submenus=0;
			if(isset($tabs2[$i]))
			{
				$tem_submenus = count($tabs2[$i]);
				if($tem_submenus>0)
				{
					$sub_menus='';
					$j=0;
					while($j < $tem_submenus)
					{
						$active2='';
						$link_url=$tabs2[$i][$j]['list'];
						$link_url=substr($link_url,0,strrpos($link_url,'/'));
						if($actual_link == $link_url){$active='active';$active2='active';}

						$sub_menus.='<li class=" '.$active2.'"><a href="'.$tabs2[$i][$j]['list'].'"><span>'.$tabs2[$i][$j][0].'</span></a></li>';
						$j++;
					}

					echo '<li class="has-sub '.$active.'"><a href="javascript:;"><b class="caret pull-right"></b><i class="'.$tabs[$i][2].'" aria-hidden="true"></i><span>'.$tabs[$i][0].'</span></a>';
					echo '<ul class="sub-menu">';
					echo $sub_menus;
					echo '</ul></li>';
				}
			}
			else
			{

				if($tabs[$i][1] != '')
				{
					$link_url=$tabs[$i][1];
					$link_url=substr($link_url,0,strrpos($link_url,'/'));
					if($actual_link == $link_url)$active='active';
				}
				echo '<li class=" '.$active.'"><a href="'.$tabs[$i][1].'"><i class="'.$tabs[$i][2].'"></i><span>'.$tabs[$i][0].'</span></a></li>';
			}
			$i++;
		}
	}
}



function show_alertas()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$admin_id,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$fields=db_list_fields($DB_name,'mensagens',$db_link);
	$columns=db_num_fields($fields);
	if($columns>0)
	{
		$field_array=array();
		for($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('ID_Admin',$field_array))$res=Runquery($db_link,$DB_name,'ALTER TABLE mensagens ADD ID_Admin Int(11) NOT NULL ');
	}



	$rs=new DataObj;
    $total=0;
	$sql="SELECT count(*) as total FROM mensagens WHERE ID_Admin = 0 AND was_read=0 ";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$hRes)	echo db_error($db_link);
	else $total=$rs->db_data["total"];
	$rs->db_close();

	if($total>0)
	{
		echo '<div class="mensagens">';
		echo '<div class="name dropdown">';
		echo '<a href="javascript:;" data-toggle="dropdown" class="dropdown-toggle f-s-14" aria-expanded="true"><i class="fa fa-bell"></i><span class="label">'.$total.'</span></a>';
		echo '<ul class="dropdown-menu media-list" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 0px; left: 0px; transform: translate3d(0px, 20px, 0px);">';

		echo '<div class="dropdown-header">NOTIFICATIONS ('.$total.')</div>';


		/*echo '<a href="javascript:;" class="dropdown-item media">
						<div class="media-left">
							<i class="fa fa-bug media-object bg-silver-darker"></i>
						</div>
						<div class="media-body">
							<h6 class="media-heading">Server Error Reports <i class="fa fa-exclamation-circle text-danger"></i></h6>
							<div class="text-muted f-s-10">3 minutes ago</div>
						</div>
					</a>';*/


		$sql="SELECT * FROM mensagens WHERE ID_Admin = 0 AND was_read=0 order by ID DESC";
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		while(!$rs->EOF())
		{
			echo '<a href="'.$site_http_url.'admin/encomendas/pagina.php?ID='.$rs->db_data["ID_Enc"].'" class="dropdown-item media">
						<div class="media-left">
						<img src="../assets/img/user/user-1.jpg" class="media-object" alt="">
						<i class="fab fa-facebook-messenger text-blue media-object-icon"></i>
						</div>
						<div class="media-body">
						<h6 class="media-heading">'.$rs->db_data["Assunto"].'</h6>
						<p>'.$rs->db_data["Mensagem"].'</p>
						<div class="text-muted f-s-10">'.$rs->db_data["idate"].'</div>
						</div>
						</a>';

			$rs->db_next();
		}
		$rs->db_close();

		/*echo '<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
					<img src="../assets/img/user/user-2.jpg" class="media-object" alt="">
					<i class="fab fa-facebook-messenger text-blue media-object-icon"></i>
					</div>
					<div class="media-body">
					<h6 class="media-heading">Olivia</h6>
					<p>Quisque pulvinar tellus sit amet sem scelerisque tincidunt.</p>
					<div class="text-muted f-s-10">35 minutes ago</div>
					</div>
					</a>';
		echo '<a href="javascript:;" class="dropdown-item media">
					<div class="media-left">
					<i class="fa fa-plus media-object bg-silver-darker"></i>
					</div>
					<div class="media-body">
					<h6 class="media-heading"> New User Registered</h6>
					<div class="text-muted f-s-10">1 hour ago</div>
					</div>
					</a>';
		echo '<a href="javascript:;" class="dropdown-item media">
						<div class="media-left">
							<i class="fa fa-envelope media-object bg-silver-darker"></i>
							<i class="fab fa-google text-warning media-object-icon f-s-14"></i>
						</div>
						<div class="media-body">
							<h6 class="media-heading"> New Email From John</h6>
							<div class="text-muted f-s-10">2 hour ago</div>
						</div>
					</a>';*/

		echo '<div class="dropdown-footer text-center"><a href="'.$site_http_url.'admin/messages/">Ver todas</a></div>';
		echo '</ul>';
		echo '</div>';
		echo '</div>';
	}


}
?>
<!-- begin #sidebar -->
<div id="sidebar" class="sidebar">
	<!-- begin sidebar scrollbar -->
	<div data-scrollbar="true" data-height="100%">
		<!-- begin sidebar user -->
		<ul class="nav">
			<li class="nav-profile" style="padding: 0;">

				<div class="info">


					<div class="name dropdown">
		                <a  style="padding: 20px; width: 100%; display: inline-block; color: #fff; text-decoration: none;" href="javascript:;" data-toggle="dropdown" aria-expanded="false"><?=$dados['Nome']?> <b class="caret"></b></a>
                        <ul class="dropdown-menu" x-placement="bottom-start" style="position: absolute; will-change: transform; top: 20px; left: 40px; transform: translate3d(0px, 20px, 0px);">
                            <li><a style="padding: 10px" href="<?= $site_http_url ?>admin/users/pagina.php?ID=<?= $admin_id ?>">Editar Perfil</a></li>
                            <li class="divider"></li>
                            <li><a style="padding: 10px" href="<?= $site_http_url ?>admin/logout.php">Log Out</a></li>
                        </ul>
		            </div>
				</div>
			</li>
		</ul>
		<!-- end sidebar user -->
		<!-- begin sidebar nav -->
		<ul class="nav">
			<?php show_menus($tabs,$tabs2); ?>

			<li class="has-sub ">
				<a href="javascript:;"><b class="caret pull-right"></b><i class="fa fa-cogs" aria-hidden="true"></i><span>Configurações</span></a>
				<ul class="sub-menu ">

					<?php
					if((isset($dados['Permissoes'][6]) && $dados['Permissoes'][6]==1) && $tem_loja) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/currency/list.php"><span>Moeda</span></a></li>');
					if(isset($dados['Permissoes'][5]) && $dados['Permissoes'][5]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/stats/produtos.php"><span>'.write_lang('key_ver_estatiticas','Ver Estatísticas').'</span></a></li>');
					if(isset($dados['Permissoes'][3]) && $dados['Permissoes'][3]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/modulos_config_site/pagina.php"><span>'.write_lang('key_definicoes_plataforma','Definições da Plataforma').'</span></a></li>');
					if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/messages/index.php"><span>'.write_lang('key_mensagens_internas','Mensagens Internas').'</span></a></li>');
					if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/users/list.php"><span>'.write_lang('key_lista_de_utilizadores','Lista de Utilizadores').'</span></a></li>');
					if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/sa_linguas/list.php"><span>Traduções</span></a></li>');
					if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<li class=" "><a href="'.$site_http_url.'admin/memomonitor/"><span>SERVER LOAD</span></a></li>');

					?>
				</ul>
			</li>
			<!-- begin sidebar minify button -->
			<li><a href="javascript:;" class="sidebar-minify-btn" data-click="sidebar-minify"><i class="fa fa-angle-double-left"></i></a></li>
	        <!-- end sidebar minify button -->
		</ul>
		<!-- end sidebar nav -->
	</div>
	<!-- end sidebar scrollbar -->
</div>
<div class="sidebar-bg"></div>
<!-- end #sidebar -->
<div id="content" class="content">
