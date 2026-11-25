<?
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	if(!empty($_POST)){
		foreach($_POST as $varName=> $value) 
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="DELETE from products_destaques";
				$res=Runquery($db_link,$DB_name,$sql);
				break;
			}
		}
		foreach($_POST as $varName=> $value) 
		{
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$var="field_".$str_id;
				$dv=$$var;
				$sql="INSERT INTO products_destaques(ID_Prod,Ordem) VALUES ('$str_id','$dv')";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		}

		$exe_file = $GLOBALS['site_shell_url'] . "crons/gera_produtospromocao.php";	
		exec('php '.$exe_file.' > /dev/null &');

		$exe_file = $GLOBALS['site_shell_url'] . "crons/gera_produtosdestaque.php";	
		exec('php '.$exe_file.' > /dev/null &');
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	?>
	<form action="list_destaques.php" method="post" name="lista_paginas" >
	<input type="submit" style="display:none" />
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0 >
	<tr>
		<td valign="top" align="left"><? 
			include "../layout/menus.inc.php";
			show_page_tools_select(filter);
            show_content_list_destaques();	
            
			if(isset($_GET['added']) && $_GET['added']==1){
            ?>
                <script>document.getElementById(<?=$_GET['id_produto']?>).style.background="#0F0";</script>
            <?php } ?>
			<table class="p1" align="center" border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr>
	            <td width="65"></td>
				<td valign="middle" align="left"><input type="submit"  name="submeter" value="Atualizar" class="botao" style="width:120px;height:20px"></td>
			</tr>
			</table>
		</td>
	    <td width="10px">&nbsp;</td>
	    <td valign="top"><? show_fast_tools_destaques();?></td>
	</tr>
	</table>
	</form>
<? 	include "../layout/bottom.inc.php";?>