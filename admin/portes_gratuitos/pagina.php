<?php
	include "settings.inc.php";

	$D=getdate();


	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;


		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs = new DataObj;
		$sql = 'SELECT * FROM portes_gratuitos';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF())
		{
			$valor_portes = $rs->db_data["valor_portes"];
		}
		$rs->db_close();

	$task_button = "Gravar";

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
	<div class="center">
		<form name="form" method="post" action="pagina_trata.php" >
			<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; show_page_tools(filter);?>
					<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="4" align="center">
                    <tr>
						<td align="left" valign="top" class="preto">
							<table width="100%" cellpadding=0 cellspacing=0 border=0>
	                        <tr>
								<td width="5" height="26"></td>
								<td width="200" class="new_preto">&nbsp;&nbsp;Valor >=&nbsp;</td>
								<td class="new_preto"><input  type="text" name="valor_portes" id="valor_portes" style="WIDTH: 200px"  value="<?php echo $valor_portes;?>" class="form-control" /></td>
								<td width="5" height="26"></td>
							</tr>
							</table>

						</td>
					</tr>

					</table>
                    <?php show_page_buttons();?>
                </td>
				<td width="10px">&nbsp;</td>
				<td valign="top">&nbsp;</td>
            </tr>
            </table>
		</form>
	</div>
<?php 	include "../layout/bottom.inc.php";?>