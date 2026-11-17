<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button="Apagar";$task_form='apaga';}
	elseif($ID==0){$task_button="Inserir";$task_form='insere';}
	else {$task_button="Alterar";$task_form='altera';}
	$D=getdate();
	$D2=getdate();
	$v_permissoes = array();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	if($ID!=0)
	{
		if($ID!=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;$rs2=new DataObj;
			$sql="Select * from grupos_logins WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$IDb=$rs->db_data["ID"];
				$Descricao=$rs->db_data["Descricao"];
				$Permissoes=$rs->db_data["Permissoes"];
                $Loja=$rs->db_data["Loja"];
				$v_permissoes = decodePermissoes($Permissoes);

			}
			$rs->db_close(1);
		}
	}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<div class="center">
	<form name="form" method="post" action="pagina_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>">
		<input type="hidden" name="slashes" value="'">
    	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Nome:</td>
                                    <td align="left" valign="top"><input name="Descricao" style="WIDTH:99%; HEIGHT:20px" maxlength="255" value="<?=$Descricao;?>"></td>
                                </tr>
                                </table>
                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Loja:</td>
                                    <td align="left" valign="top"><input name="Loja" style="WIDTH:200px; HEIGHT:20px" maxlength="100" value="<?=$Loja;?>"></td>
                                </tr>
                                </table>

                                <table width="100%" border=0 cellspacing=0 cellpadding="2">
                                <tr>
                                    <td class="preto" align="left" valign="top" width="120">Módulos:</td>
                                    <td align="left" valign="top">
                                    	<?php
                                    		echo '<table>';
                                    		foreach ($modulos as $id_modulo => $NOME)
                                    		{
                                    			echo '<tr>';
                                    			echo '<td><input type="checkbox" name="modulos_'.$id_modulo.'" value="1"';
                                    			if(isset($v_permissoes[$id_modulo]) && $v_permissoes[$id_modulo]==1) echo ' checked="checked" ';
												echo '></td>';
                                    			echo '<td>&nbsp;</td>';
                                    			echo '<td>'.$NOME.'</td>';
                                    			echo '</tr>';
                                    		}
                                    		echo '</table>';
                                    	?>
                                    </td>
                                </tr>
                                </table>
                           </td>
                       </tr>
                       </table>
                	</td>
				</tr>
                </table><? show_page_buttons();?>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top"><? show_fast_tools();?><br /></td>
		</tr>
		</table>
    </form>
</div>
<? 	include "../layout/bottom.inc.php";?>