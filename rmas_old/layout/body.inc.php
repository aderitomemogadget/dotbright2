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


echo comprime('<div class="topo rma-topbar">');
echo comprime('<div class="admint1 rma-topbar-inner">');
echo comprime('<div class="bemvindo rma-brand"><span class="rma-brand-mark">db</span><span class="rma-brand-copy"><strong>Gestão de RMAs</strong><small>'.write_lang('key_bem_vindo','Bem-vindo').', '.htmlspecialchars($dados['Nome'], ENT_QUOTES, 'UTF-8').'</small></span></div>');
echo comprime('<div class="logout rma-user-actions">');


function pesquisa_RMAS()
{
	global $admin_lang,$site_http_url;
	$page_url=$_SERVER['REQUEST_URI'];
	if(!substr_count($page_url,'_trata.php'))
	{
		$curpage = curPageURL(); 
		$curpage = str_replace("?admin_lingua=".$admin_lang."&","?",$curpage); 
		$curpage = str_replace("&admin_lingua=".$admin_lang."&","&",$curpage);
		?><form action="<?=$site_http_url?>rmas/rmas/list.php" method="get" class="rma-global-search">
		<label for="rma-top-search">Pesquisa</label>
		<input id="rma-top-search" type="text" name="top_search" value="<?php echo (isset($_GET['top_search']) && $_GET['top_search']!='' ? htmlspecialchars($_GET['top_search'], ENT_QUOTES, 'UTF-8') : '');?>" placeholder="Tracking, cliente ou equipamento" />
		<input type="submit" value="Pesquisar" class="botao" />
		</form><?
	}
}

if(isset($dados['Permissoes'][8]) && $dados['Permissoes'][8]==1){ echo comprime('<div class="pesquisa_rapida">'); echo comprime(pesquisa_RMAS()); echo comprime('</div>');}
if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4]==1) echo comprime('<a class="navlink" title="'.write_lang('key_mensagens_internas','Mensagens Internas').'" href="'.$site_http_url.'rmas/messages/index.php"><img src="'.$site_http_url.'rmas/layout/messages.png" border=0 /></a>');
if(isset($dados['Permissoes'][1]) && $dados['Permissoes'][1]==1) echo comprime('<a class="navlink" title="'.write_lang('key_lista_de_utilizadores','Lista de Utilizadores').'" href="'.$site_http_url.'rmas/users/list.php"><img src="'.$site_http_url.'rmas/layout/perfil.png" border=0 /></a>');
echo comprime('<a class="navlink rma-logout-link" title="'.write_lang('key_logout','Logout').'" aria-label="'.write_lang('key_logout','Logout').'" href="'.$site_http_url.'rmas/logout.php"><span class="rma-logout-icon" aria-hidden="true"><svg viewBox="0 0 24 24" focusable="false"><path d="M10 5H5.75A1.75 1.75 0 0 0 4 6.75v10.5C4 18.22 4.78 19 5.75 19H10"/><path d="M15 8l4 4-4 4M9 12h10"/></svg></span></a>');
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
