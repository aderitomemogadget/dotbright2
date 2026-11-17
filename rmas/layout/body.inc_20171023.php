<?php
global $admin_id,$admin_user,$site_http_url,$ss_id;
$mens = Bottom_count_msgs("",$ss_id);

if($GLOBALS['admin_lang'] == 'en') $txt_lingua_on = 'A trabalhar na língua Inglês';
elseif($GLOBALS['admin_lang'] == 'es') $txt_lingua_on = 'A trabalhar na língua Espanhol';
elseif($GLOBALS['admin_lang'] == 'fr') $txt_lingua_on = 'A trabalhar na língua Francês';
else $txt_lingua_on = 'A trabalhar na língua Português';

global $admin_id;
$dados = getDadosAdministrador($admin_id);
if($dados['Nome'] != '') $admin_user = $dados['Nome'];

echo comprime('<div class="topo">');
echo comprime('<div class="admint1">');
echo comprime('<div class="bemvindo" style="width:250px"><br>&nbsp;&nbsp;'.write_lang('key_bem_vindo','Bem Vindo').' '. $dados['Nome'].'&nbsp;</div>');
echo comprime('<div class="logout">');


function pesquisa_RMAS()
{
	global $admin_lang,$site_http_url;
	$page_url=$_SERVER['REQUEST_URI'];
	if(!substr_count($page_url,'_trata.php'))
	{
		$curpage = curPageURL(); 
		$curpage = str_replace("?admin_lingua=".$admin_lang."&","?",$curpage); 
		$curpage = str_replace("&admin_lingua=".$admin_lang."&","&",$curpage);
		?><form action="<?=$site_http_url?>rmas/rmas/list.php" method="get">
		Pesquisa:&nbsp;
		<input style="height: 22px !important;" type="text" name="top_search" />
		<input type="submit" value=" OK "  class="botao"/>
		</form><?
	}
}

if(isset($dados['Permissoes'][8]) && $dados['Permissoes'][8]==1){ echo comprime('<div class="pesquisa_rapida">'); echo comprime(pesquisa_RMAS()); echo comprime('</div>');}
if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) echo comprime('<a class="navlink" title="'.write_lang('key_mensagens_internas','Mensagens Internas').'" href="'.$site_http_url.'rmas/messages/index.php"><img src="'.$site_http_url.'rmas/layout/messages.png" border=0 /></a>');
if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<a class="navlink" title="'.write_lang('key_lista_de_utilizadores','Lista de Utilizadores').'" href="'.$site_http_url.'rmas/users/list.php"><img src="'.$site_http_url.'rmas/layout/perfil.png" border=0 /></a>');
echo comprime('<a class="navlink" title="'.write_lang('key_logout','Logout').'" href="'.$site_http_url.'rmas/logout.php"><img src="'.$site_http_url.'rmas/layout/logout.png" border=0 /></a>');		
if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) if($mens) echo comprime('<div style="position:relative; text-align:left"><div style="position:absolute; top:-40px; left:420px; width:16px; height:14px; border:1px solid red; background-color:#333">'.$mens.'</div></div>');
echo comprime('</div></div></div>');



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
}?>