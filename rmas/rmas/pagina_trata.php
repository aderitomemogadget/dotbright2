<?php
	include "settings.inc.php";
	/* ini_set('display_errors',1);
	 ini_set('display_startup_errors',1);
	 error_reporting(-1);*/

	if(isset($_POST['slashes']))
	{		
		$result = submeteDados();
		$res = $result['resultado'];
		$id_acao = $result['id_acao'];
		if (!$res && !empty($_SESSION['rma_repair_error'])) {
			echo '<p role="alert" style="padding:16px;background:#fff0ed;color:#8c2516">'.dotbright_rma_html($_SESSION['rma_repair_error']).'</p>';
			unset($_SESSION['rma_repair_error']);
		}

		$acao = $result['acao']=='insere' ? 1 : 2;
		
		if(isset($_POST[TBID]) && $_POST[TBID] > 0 || $id_acao > 0) $retorno = 'pagina.php?'.TBID.'='.((isset($id_acao) && $id_acao > 0) ? $id_acao : $_POST[TBID]).'&tab='.preg_replace("/[^0-9]/","",$_POST['tab_a']);
		else $retorno = 'list.php';
	
		
		if($res)
		{ 
			if(rma_is_loan_block_result($res)){
				$rma_id = (isset($id_acao) && $id_acao > 0) ? (int)$id_acao : (isset($_POST[TBID]) ? (int)$_POST[TBID] : 0);
				?>
				<br>
				<table class="new_quadros" width="325px" cellspacing="0" cellpadding="4" border="0" align="center">
					<tr> 
						<td valign="top">
							<table width="300" cellspacing="0" cellpadding="0" border="0" align="center">
								<tr>
									<td class="preto" align="center">
										<br><br>
										<b>RMA não finalizada</b>
										<br><br>
										Não é possível aplicar um estado final enquanto o equipamento de empréstimo não estiver devolvido.
										<br><br>
										Esta é uma validação do empréstimo, não um erro técnico. Volte à RMA, abra o separador "Telefone Empréstimo", marque o equipamento como "Devolvido" e grave novamente.
										<?php if($rma_id > 0) { ?>
										<br><br>
										<input class="botao" type="button" value="VOLTAR À RMA" onclick="window.open('pagina.php?ID=<?php echo $rma_id; ?>','_self')" style="height:30px;width:120px" />
										<input class="botao" type="button" value="IMPRIMIR RECEPÇÃO" onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $rma_id; ?>&tipo=1&n_folhas=1&d=<?php echo date('YmdHis'); ?>','_blank')" style="height:30px;width:150px" />
										<input class="botao" type="button" value="IMPRIMIR INTERVENÇÃO" onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $rma_id; ?>&tipo=2&n_folhas=1&d=<?php echo date('YmdHis'); ?>','_blank')" style="height:30px;width:165px" />
										<?php } ?>
										<br><br>
									</td>
								</tr>
							</table>
						</td>
					</tr>
				</table>	
				<?php
			}
			else{				
				show_box_OK($acao, $retorno);
				
				?><br>
				<table class="new_quadros" width="325px" cellspacing="0" cellpadding="4" border="0" align="center">
				<tr> 
				<td valign="top">
				<table width="300" cellspacing="0" cellpadding="0" border="0" align="center">
				<tr>
				<td class="preto" align="center">
				<b>IMPRIMIR</b>			
				<br><br>
				
				<?php 
				// echo 'm_estado: '.$_POST['m_estado'];
				
				if($_POST['m_estado'] == 15 || $_POST['m_estado'] == 16 || $_POST['m_estado'] == 17 ||$_POST['m_estado'] == 18 || $_POST['m_estado'] == 10 || $_POST['m_estado'] == 11 || $_POST['m_estado'] == 12 ||$_POST['m_estado'] == 13 || $_POST['m_estado'] == 14){
					?>
					<input class="botao" name="button" style="height:30px; width:100px;" value=" IMPRIMIR 1 FOLHA " onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $id_acao;?>&tipo=2&d=<?php echo date('YmdHis')?>&n_folhas=1','_blank')" type="button">
					<input class="botao" name="button" style="height:30px; width:100px;" value=" IMPRIMIR 2 FOLHAS " onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $id_acao;?>&tipo=2&d=<?php echo date('YmdHis')?>&n_folhas=2','_blank')" type="button">
					<?php 
				}else{
					?>
					<input class="botao" name="button" style="height:30px; width:100px;" value=" IMPRIMIR 1 FOLHA " onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $id_acao;?>&tipo=1&d=<?php echo date('YmdHis')?>&n_folhas=1','_blank')" type="button">
					<input class="botao" name="button" style="height:30px; width:100px;" value=" IMPRIMIR 2 FOLHAS " onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $id_acao;?>&tipo=1&d=<?php echo date('YmdHis')?>&n_folhas=2','_blank')" type="button">
					<?php 
				}
				?>
				
				</td></tr></table>
				</td></tr></table>
				<?php
			}
		}
		else show_box_ERROR($acao, $retorno);
	}
	else show_box_ERROR(4,$retorno);
