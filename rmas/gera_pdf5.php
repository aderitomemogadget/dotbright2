<?php
//if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
require_once "../core/settings.php";
require_once "../core/db_engine_pdo.php";
require_once "../core/global.php";
//require_once('mpdf/mpdf.php');

require_once("vendor/autoload.php");

$contactos_lojas_maia = '
<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;color:#000000" href="'.$GLOBALS['site_http_url'].'">DotBright · telecomunicações</a><br>
Avenida Visconde Barreiros 167<br>
4470-151 Maia<br>
Telefone: 229 429 211 <span style="font-size:12px">(Chamada para a rede fixa nacional)</span><br>
E-mail: <a style="text-decoration:none;color:#000000" href="mailto:lojaonline@dotbright.pt">lojaonline@dotbright.pt</a><br>';

$contactos_lojas_matosinhos = '
<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;color:#000000" href="'.$GLOBALS['site_http_url'].'">DotBright · telecomunicações</a><br>
Rua Roberto Ivens, 879<br>
4450-256 Matosinhos<br>
Telefone: 220 142 941 (Chamada para a rede fixa nacional)<br>
E-mail: <a style="text-decoration:none;color:#000000" href="mailto:lojaonline@dotbright.pt">lojaonline@dotbright.pt</a><br>';

if($_GET['tipo'] == 1 && $_GET['idtracking'] > 0)
{
	pdfRecepcao($_GET['idtracking'], $_GET['n_folhas']);
}
else if($_GET['tipo'] == 2 && $_GET['idtracking'] > 0)
{
	pdfOrcamento($_GET['idtracking'], $_GET['n_folhas']);
}
else if($_GET['tipo'] == 3 && $_GET['idtracking'] > 0)
{
	pdfReparacao($_GET['idtracking'], $_GET['n_folhas']);
}
else if($_GET['tipo'] == 4 && $_GET['idtracking'] > 0)
{
	pdfRMACliente($_GET['idtracking'], $_GET['n_folhas']);
}
else if($_GET['enc'])
{
	pdfPedidoRecolha($_GET['enc'], $_GET['n_folhas']);
}

function get_rmas_descricao_tecnico($estado_sel=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs2 = new DataObj; $return = '';
	$sql="SELECT title FROM rmas_descricao_tecnico WHERE ID='".$estado_sel."'";
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$return = $rs2->db_data['title'];					
	}
	$rs2->db_close();
	
	return $return;
}

function pdfRecepcao($id, $_folhas=1)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$software_arr = array();
	$hardware_arr = array();
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas WHERE ID="'.$id.'"');
	if($rs)
	{
		$IDb = $rs->db_data['ID'];
		$ID_Cliente = $rs->db_data['ID_Cliente'];
		$Cliente_Nome = $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'];
		$Cliente_Morada = $rs->db_data['cliente_morada'];
		$Cliente_CP = $rs->db_data['cliente_cp'];
		$Cliente_Email = $rs->db_data['cliente_username'];

		/*$Cliente_Telefone = $rs->db_data['cliente_telefone1'];
		if(substr($rs->db_data['cliente_telefone1'],0,1)=='9')$Cliente_Telefone.='(Chamada para a rede móvel taxada à tarifa rede móvel Vodafone)';
		if(substr($rs->db_data['cliente_telefone1'],0,1)=='2')$Cliente_Telefone.='(Chamada para a rede fixa nacional)';

		if($rs->db_data['cliente_telefone2'] != '')
		{
			$Cliente_Telefone .= ' / '.$rs->db_data['cliente_telefone2'];
			if(substr($rs->db_data['cliente_telefone2'],0,1)=='9')$Cliente_Telefone.='(Chamada para a rede móvel taxada à tarifa rede móvel Vodafone)';
			if(substr($rs->db_data['cliente_telefone2'],0,1)=='2')$Cliente_Telefone.='(Chamada para a rede fixa nacional)';
		}*/

		$Cliente_Telefone = $rs->db_data['cliente_telefone1'].($rs->db_data['cliente_telefone2'] != '' ? ' / '.$rs->db_data['cliente_telefone2'] : '');

		$Cliente_Empresa = $rs->db_data['cliente_empresa'];
		$m_origem = $rs->db_data['m_origem'];

		$dataURI = $rs->db_data['signed2'];
		$signed_date = $rs->db_data['signed2_date'];

		/*
		###INSERÇÃO DE NOVO CAMPO M_ACEITE PARA VERIFICAR SE O ORÇAMENTO FOI ACEITE
		 */
		$m_aceite = $rs->db_data['m_aceite'];
		
		$loja = '';
		// ONLINE || MAIA
		if($rs->db_data['m_origem'] == 1 || $rs->db_data['m_origem'] == 3)
		{
			$loja = $GLOBALS['contactos_lojas_maia'];
		}
		elseif($rs->db_data['m_origem'] == 2)
		{
			$loja = $GLOBALS['contactos_lojas_matosinhos'];
		}
		
		// $ID_Tecnico = $rs->db_data['ID_Tecnico'];
		$m_imei = $rs->db_data['m_imei'];
		$marca = $rs->db_data['m_marca'];
		$modelo = $rs->db_data['m_modelo'];
		$tipoequip = $rs->db_data['TipoEquip'];
		$nserie = $rs->db_data['m_nserie'];
		$avaria = stripslashes($rs->db_data['m_avaria']);
		$m_estado_equipamento = $rs->db_data['m_estado_equipamento'];
		$m_estado_equipamento_text = $rs->db_data['m_estado_equipamento_text'];
		$estadoequip = '';
		if(!$m_estado_equipamento || $m_estado_equipamento==0) $estadoequip = 'Riscos ligeiros'; 
		if($m_estado_equipamento == 1) $estadoequip = 'Alguns riscos e mossas'; 
		if($m_estado_equipamento == 2) $estadoequip = 'Muitos riscos e mossas'; 
		if($m_estado_equipamento == 3) $estadoequip = 'Muito danificado'; 
		$estadoequip .= '<br>'.$m_estado_equipamento_text;
		
		
		$resumo = array();
		$tipo_reparacao_po = ($rs->db_data['tipo_reparacao_po']==1 ? 'Pedido de orçamento' : '');
		if($tipo_reparacao_po) $resumo[] = $tipo_reparacao_po;
		$tipo_reparacao_gl = ($rs->db_data['tipo_reparacao_gl']==1 ? 'Garantia de loja' : '');
		if($tipo_reparacao_gl) $resumo[] = $tipo_reparacao_gl;
		$tipo_reparacao_gf = ($rs->db_data['tipo_reparacao_gf']==1 ? 'Garantia do fabricante' : '');
		if($tipo_reparacao_gf) $resumo[] = $tipo_reparacao_gf;
		$tipo_reparacao_gr = ($rs->db_data['tipo_reparacao_gr']==1 ? 'Garantia de reparação' : '');
		if($tipo_reparacao_gr) $resumo[] = $tipo_reparacao_gr;
		$ndocumento = ($rs->db_data['ndocumento'] != 'insira o numero de documento' ? $rs->db_data['ndocumento'] : '');
		if($ndocumento) $resumo[] = $ndocumento;
		
		
		
		$software_dr=$rs->db_data['software_dr'];
        $software_rc=$rs->db_data['software_rc'];
        $software_na=$rs->db_data['software_na'];
        $software_cg=$rs->db_data['software_cg'];
        $software_ci=$rs->db_data['software_ci'];
        $software_bl=$rs->db_data['software_bl'];
        $hardware_tp=$rs->db_data['hardware_tp'];
        $hardware_tnf=$rs->db_data['hardware_tnf'];
        $hardware_lp=$rs->db_data['hardware_lp'];
        $hardware_lpb=$rs->db_data['hardware_lpb'];
        $hardware_nc=$rs->db_data['hardware_nc'];
        $hardware_ssa=$rs->db_data['hardware_ssa'];
        $hardware_sa=$rs->db_data['hardware_sa'];
        $hardware_sam=$rs->db_data['hardware_sam'];
        $hardware_sal=$rs->db_data['hardware_sal'];
        $hardware_saa=$rs->db_data['hardware_saa'];
        $hardware_nl=$rs->db_data['hardware_nl'];
        $hardware_ncrg=$rs->db_data['hardware_ncrg'];
        $hardware_ncrw=$rs->db_data['hardware_ncrw'];
        $hardware_svl=$rs->db_data['hardware_svl'];
        $hardware_cfnf=$rs->db_data['hardware_cfnf'];
        $hardware_cpnf=$rs->db_data['hardware_cpnf'];
        $hardware_bhn=$rs->db_data['hardware_bhn'];
        $hardware_bvn=$rs->db_data['hardware_bvn'];
        $hardware_bsn=$rs->db_data['hardware_bsn'];
        $hardware_bof=$rs->db_data['hardware_bof'];
        $hardware_caj=$rs->db_data['hardware_caj'];
        $hardware_lsim=$rs->db_data['hardware_lsim'];
        $hardware_lmsd=$rs->db_data['hardware_lmsd'];
        $hardware_lc=$rs->db_data['hardware_lc'];
        $hardware_sp=$rs->db_data['hardware_sp'];
        $preorcamento = ($rs->db_data['preorcamento'] > 0  ? number_format($rs->db_data['preorcamento'],2).' €' : '');

		
		$acessorios = $rs->db_data['m_acessorios'];
		$observacoestec = $rs->db_data['ObservacoesTecnico'];
		$relintervencao = $rs->db_data['RelatorioIntervencao'];
		$DataInsert = $rs->db_data['DataInsert'];
		$Estado = $rs->db_data['m_estado'];
		
		
		if($software_dr) $software_arr[] = 'Desbloqueio à REDE';
		if($software_rc) $software_arr[] = 'Remover Código de Segurança';
		if($software_na) $software_arr[] = 'Não arranca';
		if($software_cg) $software_arr[] = 'Remover Conta Google';
		if($software_ci) $software_arr[] = 'Remover Conta iCloud';
		if($software_bl) $software_arr[] = 'Bloqueia / está lento';
		
		
		if($hardware_tp) $hardware_arr[] = 'Touch partido';
		if($hardware_tnf) $hardware_arr[] = 'Touch não funciona';
		if($hardware_lp) $hardware_arr[] = 'LCD partido';
		if($hardware_lpb) $hardware_arr[] = 'LCD em branco/preto';
		if($hardware_nc) $hardware_arr[] = 'Não carrega';
		if($hardware_ssa) $hardware_arr[] = 'Sem sinal áudio no auscultador';
		if($hardware_sa) $hardware_arr[] = 'Sinal áudio no auscultador baixo ou com Ruído';
		if($hardware_sam) $hardware_arr[] = 'Sem sinal áudio no microfone';
		if($hardware_sal) $hardware_arr[] = 'Sem sinal áudio no altifalante';
		if($hardware_saa) $hardware_arr[] = 'Sinal áudio no altifalante baixo ou com Ruído';
		if($hardware_nl) $hardware_arr[] = 'Não Liga';
		if($hardware_ncrg) $hardware_arr[] = 'Não capta REDE GSM';
		if($hardware_ncrw) $hardware_arr[] = 'Não capta REDE WIFI/bluetooth';
		if($hardware_svl) $hardware_arr[] = 'Lente LCD partido(LCD OK) 7 dias';
		if($hardware_cfnf) $hardware_arr[] = 'Camera frontal não funciona';
		if($hardware_cpnf) $hardware_arr[] = 'Camera principal não funciona';
		if($hardware_bhn) $hardware_arr[] = 'Botão Home não funciona';
		if($hardware_bvn) $hardware_arr[] = 'Botão de Volume não funciona';
		if($hardware_bsn) $hardware_arr[] = 'Botão de Silêncio não funciona';
		if($hardware_bof) $hardware_arr[] = 'Botão ON/OFF não funciona';
		if($hardware_caj) $hardware_arr[] = 'Conector audioJack não funciona';
		if($hardware_lsim) $hardware_arr[] = 'Leitor SIM';
		if($hardware_lmsd) $hardware_arr[] = 'Leitor MicroSD';
		if($hardware_lc) $hardware_arr[] = 'Lente da camera partida';
		if($hardware_sp) $hardware_arr[] = 'Sensor de proximidade';
	
	
		$codigo_desbloqueio = ($rs->db_data['codigo_desbloqueio']!=0 && $rs->db_data['codigo_desbloqueio']!='' ? $rs->db_data['codigo_desbloqueio'] : $rs->db_data['m_padrao']);


		
		$rs->db_close();
		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>
		<p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Folha de Recepção</p>
		<table style="width:19cm; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>		
				</td>
				
				<td align="right">
					'.$loja.'
				</td>
			</tr>
		</table>
		<table style="width:19cm; height:5cm; border-bottom:1px solid #000;">
			<tr >
				<td height="0.5cm"><b>Data Recebido:</b></td>
				<td>'.$DataInsert.'</td>
				<td align="right" width="10cm"><b style="font-size:18pt">Tracking: #'."DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</b></td>
			</tr>
			<tr>
				<td height="0.5cm"><b>Cliente:</b></td>
				<td>'.$Cliente_Nome.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td height="0.5cm"><b>Contacto:</b></td>
				<td>'.$Cliente_Telefone.'</p></td>
				<td>&nbsp;</td>
			</tr>
			'.(count($resumo) > 0 ? '
			<tr >
				<td height="0.5cm"><b>Resumo:</b></td>
				<td colspan="2">'.implode(', ', $resumo).'</td>
				
			</tr>' : '').'
		</table>
		
		
		
		<table style="width:19cm; border-bottom:1px solid #000;">
			<tr>
				<td height="1cm" valing="middle" colspan="4"><b style="font-size:12pt;">Dados do Equipamento:</b></td>'.($rs->db_data['m_padrao']!='' &&  $rs->db_data['m_padrao']!=0 ? '
				<td rowspan="4" width="110">
				<img src="'.$GLOBALS['site_http_url'].'rmas/rmas/pattern.php?t='.$rs->db_data['m_padrao'].'" border="0" width="110">
				</td>' : '').'
			</tr>		
			<tr>
				<td width="1cm" height="0.5cm"><b>Imei:</b></td>
				<td width="2cm">'.$m_imei.'</td>
				<td width="1cm"><b>Nr. Série:</b></td>
				<td width="2cm">'.$nserie.'</td>
			</tr>
			<tr>
				<td width="1cm" height="0.5cm"><b>Marca:</b></td>
				<td width="2cm">'.$marca.'</td>
				<td width="1cm"><b>Modelo:</b></td>
				<td width="2cm">'.$modelo.'</td>

			</tr>
			<tr>
				<td width="1cm" height="0.5cm"><b>Código desbloqueio:</b></td>
				<td width="2cm">'.($codigo_desbloqueio == 0 || $codigo_desbloqueio == '' ? '' : $codigo_desbloqueio).'</td>
				<td width="1cm"><b>Pre Orçamento:</b></td>
				<td width="2cm">'.$preorcamento.'</td>

			</tr>
		</table>
		'.(count($software_arr) > 0  ? ' 
		<table style="width:19cm;">
			<tr>
				<td height="0.5cm" valing="middle"><b style="font-size:12pt;">Software</b></td>
			</tr>
		</table>
		<table  style="width:19cm; border-bottom:1px solid #000;">
		<tr>
		<td style="font-size:10pt">												
			'.implode(', ', $software_arr).'		
		</td>		
		</tr>
		</table>' : '').'
		
		'.(count($hardware_arr) > 0 ? ' 
		<table style="width:19cm;">
			<tr>
				<td height="0.5cm" valing="middle"><b style="font-size:12pt;">Hardware</b></td>
			</tr>
		</table>
		<table  style="width:19cm; border-bottom:1px solid #000;">
		<tr>
		<td style="font-size:10pt">												
			'.implode(', ', $hardware_arr).'
		
		</td>		
		</tr>
		</table>' : '').'
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Avaria Reportada:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($avaria).'</td></tr>
		</table>
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Estado do Equipamento:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($estadoequip).'</td></tr>
		</table>
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Acessórios</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;font-size:8pt">'.nl2br($acessorios).'</td></tr>
		</table>
		<table style="width:19cm; height:5cm;">
			<tr><td style="border: 0.1px solid #000;padding:2mm; font-size:7pt;">
			É da responsabilidade do cliente efetuar cópias de segurança do sistema, aplicações e restantes dados, bem como desativar qualquer proteção por palavra-passe de forma a possibilitar a reparação, tanto ao nível de software ou hardware . A DotBright, Lda. não se responsabiliza pela perda de quaisquer dados, independentemente do tipo de anomalia do equipamento.<br>
			A garantia da DotBright, Lda. cobre as reparações efetuadas por um período de 1 ano (12 meses), exceto reparações ao nível da Logic Board, cuja garantia é de 6 meses (180 dias).<br>
			A garantia aplica-se apenas aos serviços realizados e não cobre danos causados por má utilização, quedas, líquidos, oxidação, impactos ou outros fatores externos.<br>
			Salvo exceções devidamente acordadas, a DotBright, Lda. fica autorizada a abrir os selos de garantia e proceder à desmontagem dos equipamentos, sempre que necessário para a reparação. Este procedimento pode implicar perda de originalidade, funcionalidades e cobertura de garantia externa do fabricante.<br>
			Os equipamentos reparados devem ser levantados num prazo máximo de 120 dias (seguidos) após conclusão da intervenção. Após este período, os equipamentos poderão ser considerados abandonados, não havendo lugar a reembolso ou compensação.<br>
			Todos os equipamentos são automaticamente reparados caso o custo da reparação não ultrapasse 35 €.<br>
			A DotBright, Lda. informa ainda que, devido a condições adversas que possam ocorrer durante a intervenção técnica, não é possível garantir o sucesso dos procedimentos diagnósticos. A empresa também não se responsabiliza por quaisquer perdas de dados, modificações de informação contida no equipamento ou danos em acessórios, películas, capas, cartões SIM, cartões de memória ou outros componentes deixados com o equipamento.
			</td></tr>
		</table>
		<table style="width:19cm; height:5cm;">
			<tr><td align="center" height="1cm"><b style="font-size:10pt">Declaro que aceito as condições propostas para a reparação do meu equipamento.<br>Autorizo, igualmente, a receção de informações sobre o estado da(s) minha(s) encomenda(s) e/ou reparação(ões), bem como comunicações relacionadas com produtos, serviços, newsletters, SMS e outras informações de caráter informativo e comercial.</b></td></tr>';

			if($dataURI) $html .='<tr><td align=right><img src="'.$dataURI.'"></td></tr>';
			$html .='<tr><td height="1cm" align="center"><b>Data:</b> '.$signed_date.'&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>
			<tr><td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</td></tr>';

			
			$html .= '</table>';
		
		// echo $html;
		
			// ESCREVE E IMPRIME
			/*
		$mpdf = new mPDF('c', 'A4');
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);
		if($_folhas == 2)
		{
			$mpdf->WriteHTML($html);
			$mpdf->WriteHTML('<pagebreak>'.$html);
		}
		else
		{
			$mpdf->WriteHTML($html);
		}
		$mpdf->Output("DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).' - Recepcao.pdf', 'D');
		*/
		$mpdf = new \Mpdf\Mpdf(['orientation' => 'P']);
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

		//$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		$mpdf->AddPageByArray([
		    'margin-left' 	=> '9mm',
		    'margin-right'	=> '9mm',
		    'margin-top' 	=> '9mm',
		    'margin-bottom' => '9mm',
		]);
		if($_folhas == 2)
		{
			$mpdf->WriteHTML($html);
			$mpdf->WriteHTML('<pagebreak>'.$html);
		}
		else
		{
			$mpdf->WriteHTML($html);
		}
		$mpdf->Output("DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).' - Recepcao.pdf', 'D');
	}
}

function pdfOrcamento($id, $_folhas=1)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$software_arr = array();
	$hardware_arr = array();
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas WHERE ID="'.$id.'"');
	if($rs)
	{
		$IDb = $rs->db_data['ID'];
		$ID_Cliente = $rs->db_data['ID_Cliente'];
		$ID_Cliente = $rs->db_data['ID_Cliente'];
		$Cliente_Nome = $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'];
		$Cliente_Morada = $rs->db_data['cliente_morada'];
		$Cliente_CP = $rs->db_data['cliente_cp'];
		$Cliente_Email = $rs->db_data['cliente_username'];
		$Cliente_Telefone = $rs->db_data['cliente_telefone1'].($rs->db_data['cliente_telefone2'] != '' ? ' / '.$rs->db_data['cliente_telefone2'] : '');
		$Cliente_Empresa = $rs->db_data['cliente_empresa'];
		/*
		###INSERÇÃO DE NOVO CAMPO M_ACEITE PARA VERIFICAR SE O ORÇAMENTO FOI ACEITE
		 */
		$m_aceite = $rs->db_data['m_aceite'];

		$dataURI = $rs->db_data['signed'];
		$signed_date = $rs->db_data['signed_date'];
		
		$ID_Tecnico = $rs->db_data['ID_Tecnico'];
		$sql_cliente = 'SELECT * FROM logins WHERE ID='.$ID_Tecnico;
		$rs2->db_open($db_link, $DB_name, $sql_cliente);
		if(!$rs2->EOF())
		{
			$Tecnico_Nome = $rs2->db_data['Nome'];
		}
		$rs2->db_close();
		$m_imei = $rs->db_data['m_imei'];
		$marca = $rs->db_data['m_marca'];
		$modelo = $rs->db_data['m_modelo'];
		$tipoequip = $rs->db_data['TipoEquip'];
		$nserie = $rs->db_data['m_nserie'];
		$avaria = stripslashes($rs->db_data['m_avaria']);
		$m_estado_equipamento = $rs->db_data['m_estado_equipamento'];
		$m_estado_equipamento_text = $rs->db_data['m_estado_equipamento_text'];
		$estadoequip = '';
		if(!$m_estado_equipamento || $m_estado_equipamento==0) $estadoequip = 'Riscos ligeiros'; 
		if($m_estado_equipamento == 1) $estadoequip = 'Alguns riscos e mossas'; 
		if($m_estado_equipamento == 2) $estadoequip = 'Muitos riscos e mossas'; 
		if($m_estado_equipamento == 3) $estadoequip = 'Muito danificado'; 
		if($m_estado_equipamento == 4) $estadoequip = 'Sem marca visíveis'; 
		$estadoequip .= '<br>'.$m_estado_equipamento_text;

		$m_tecnico_arr[0] = 'Actualização de software';
		$m_tecnico_arr[1] = 'Desbloqueio';
		$m_tecnico_arr[2] = 'Remoção de conta iCloud';
		$m_tecnico_arr[3] = 'Remoção de conta Google';
		$m_tecnico_arr[4] = 'Reparação de circuito de memória';
		$m_tecnico_arr[5] = 'Substituição do módulo frontal (Touch+LCD)';
		$m_tecnico_arr[6] = 'Substituição do Touch';
		$m_tecnico_arr[7] = 'Substituição do LCD';
		$m_tecnico_arr[8] = 'Reparação do circuito de áudio';
		$m_tecnico_arr[9] = 'Reparação do circuito de alimentação';
		$m_tecnico_arr[10] = 'Reparação do circuito de rádio frequência';
		$m_tecnico_arr[11] = 'Reparação do circuito de WIFI/bluetooth';
		$m_tecnico_arr[12] = 'Substituição de bateria';
		$m_tecnico_arr[13] = 'Substituição da Board';
		$m_tecnico_arr[14] = 'Substituição do equipamento';
		 
		$m_tecnico = $rs->db_data['m_tecnico'];
		$acessorios = $rs->db_data['m_acessorios'];
		$observacoestec = $rs->db_data['ObservacoesTecnico'];
		// $relintervencao = $m_tecnico_arr[$m_tecnico]."".($rs->db_data['m_observacoestec_relatorio'] !='' ? ', '.$rs->db_data['m_observacoestec_relatorio'] : '');
		$relintervencao = $rs->db_data['m_observacoestec_relatorio'];
		$total = ($rs->db_data['total'] > 0 ? $rs->db_data['total'] : $rs->db_data['m_valor']);
		
		$software_dr=$rs->db_data['software_dr'];
        $software_rc=$rs->db_data['software_rc'];
        $software_na=$rs->db_data['software_na'];
        $software_cg=$rs->db_data['software_cg'];
        $software_ci=$rs->db_data['software_ci'];
        $software_bl=$rs->db_data['software_bl'];
        $hardware_tp=$rs->db_data['hardware_tp'];
        $hardware_tnf=$rs->db_data['hardware_tnf'];
        $hardware_lp=$rs->db_data['hardware_lp'];
        $hardware_lpb=$rs->db_data['hardware_lpb'];
        $hardware_nc=$rs->db_data['hardware_nc'];
        $hardware_ssa=$rs->db_data['hardware_ssa'];
        $hardware_sa=$rs->db_data['hardware_sa'];
        $hardware_sam=$rs->db_data['hardware_sam'];
        $hardware_sal=$rs->db_data['hardware_sal'];
        $hardware_saa=$rs->db_data['hardware_saa'];
        $hardware_nl=$rs->db_data['hardware_nl'];
        $hardware_ncrg=$rs->db_data['hardware_ncrg'];
        $hardware_ncrw=$rs->db_data['hardware_ncrw'];
        $hardware_svl=$rs->db_data['hardware_svl'];
        $hardware_cfnf=$rs->db_data['hardware_cfnf'];
        $hardware_cpnf=$rs->db_data['hardware_cpnf'];
        $hardware_bhn=$rs->db_data['hardware_bhn'];
        $hardware_bvn=$rs->db_data['hardware_bvn'];
        $hardware_bsn=$rs->db_data['hardware_bsn'];
        $hardware_bof=$rs->db_data['hardware_bof'];
        $hardware_caj=$rs->db_data['hardware_caj'];
        $hardware_lsim=$rs->db_data['hardware_lsim'];
        $hardware_lmsd=$rs->db_data['hardware_lmsd'];
        $hardware_lc=$rs->db_data['hardware_lc'];
        $hardware_sp=$rs->db_data['hardware_sp'];

		if($software_dr) $software_arr[] = 'Desbloqueio à REDE';
		if($software_rc) $software_arr[] = 'Remover Código de Segurança';
		if($software_na) $software_arr[] = 'Não arranca';
		if($software_cg) $software_arr[] = 'Remover Conta Google';
		if($software_ci) $software_arr[] = 'Remover Conta iCloud';
		if($software_bl) $software_arr[] = 'Bloqueia / está lento';
		
		
		if($hardware_tp) $hardware_arr[] = 'Touch partido';
		if($hardware_tnf) $hardware_arr[] = 'Touch não funciona';
		if($hardware_lp) $hardware_arr[] = 'LCD partido';
		if($hardware_lpb) $hardware_arr[] = 'LCD em branco/preto';
		if($hardware_nc) $hardware_arr[] = 'Não carrega';
		if($hardware_ssa) $hardware_arr[] = 'Sem sinal áudio no auscultador';
		if($hardware_sa) $hardware_arr[] = 'Sinal áudio no auscultador baixo ou com Ruído';
		if($hardware_sam) $hardware_arr[] = 'Sem sinal áudio no microfone';
		if($hardware_sal) $hardware_arr[] = 'Sem sinal áudio no altifalante';
		if($hardware_saa) $hardware_arr[] = 'Sinal áudio no altifalante baixo ou com Ruído';
		if($hardware_nl) $hardware_arr[] = 'Não Liga';
		if($hardware_ncrg) $hardware_arr[] = 'Não capta REDE GSM';
		if($hardware_ncrw) $hardware_arr[] = 'Não capta REDE WIFI/bluetooth';
		if($hardware_svl) $hardware_arr[] = 'Lente LCD partido(LCD OK) 7 dias';
		if($hardware_cfnf) $hardware_arr[] = 'Camera frontal não funciona';
		if($hardware_cpnf) $hardware_arr[] = 'Camera principal não funciona';
		if($hardware_bhn) $hardware_arr[] = 'Botão Home não funciona';
		if($hardware_bvn) $hardware_arr[] = 'Botão de Volume não funciona';
		if($hardware_bsn) $hardware_arr[] = 'Botão de Silêncio não funciona';
		if($hardware_bof) $hardware_arr[] = 'Botão ON/OFF não funciona';
		if($hardware_caj) $hardware_arr[] = 'Conector audioJack não funciona';
		if($hardware_lsim) $hardware_arr[] = 'Leitor SIM';
		if($hardware_lmsd) $hardware_arr[] = 'Leitor MicroSD';
		if($hardware_lc) $hardware_arr[] = 'Lente da camera partida';
		if($hardware_sp) $hardware_arr[] = 'Sensor de proximidade';
		
		
		
		
		$observacoes = '';
		if($observacoestec != '')
		{
			$observacoes = '
			<table style="width:19cm; height:5cm;">
				<tr><td height="0.5cm"><b>Observações do Técnico</b></td></tr>
				<tr><td style="border: 0.1px solid #000;padding:2mm;">'.$observacoestec.'</td></tr>
			</table>';
		}
		
		$DataInsert = $rs->db_data['DataInsert'];
		$Estado = $rs->db_data['m_estado'];
		$EstadoTexto = '';
		/* if(isset($GLOBALS['estados_rmas']))
		{							
			foreach($GLOBALS['estados_rmas'] as $estado_rma => $rma)
			{
				foreach($GLOBALS['estados_rmas'][$estado_rma] as $key => $val)
				{
					if($Estado == $val['id']) $EstadoTexto = $val['estado'];
				}
			}					
		}
		 */
		$rs->db_close();
		$rs2->db_open($db_link, $DB_name, 'SELECT * FROM rmas_linhas WHERE ID_ORG="'.$IDb.'" ORDER BY ID ASC');
		$lin = '';
		// $total = 0;
		while(!$rs2->EOF())
		{
			$lin .= ' 
			<tr style="font-size:9pt;">
				<td height="7mm" style="border-bottom: 0.1px solid #000;" align="center">'.$rs2->db_data['Quantidade'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center">'.$rs2->db_data['Ref'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;padding-left:3mm;">'.$rs2->db_data['Cabecalho'].'</td>
			</tr>';
			// $total += ($rs2->db_data['Quantidade'] * $rs2->db_data['Preco']);
			$rs2->db_next();
		}
		$rs2->db_close();
		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>
		<p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Relatório Intervenção</p>
		<table style="width:19cm; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
				</td>
				<td align="right">
					'.$GLOBALS['contactos_lojas'].'
				</td>
			</tr>
		</table>
		<table style="width:19cm; height:5cm; border-bottom:1px solid #000;">
			<tr >
				<td height="1cm"><b>Data Recebido:</b></td>
				<td>'.$DataInsert.'</td>
				<td align="right" width="10cm"><b style="font-size:18pt">Tracking: #'."DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</b></td>
			</tr>
			<tr>
				<td height="1cm"><b>Cliente:</b></td>
				<td>'.$Cliente_Nome.'</td>
				<td align="center">&nbsp;</td>
			</tr>
			<tr>
				<td height="1cm"><b>Contacto:</b></td>
				<td>'.$Cliente_Telefone.'</p></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<table style="width:19cm;">
			<tr>
				<td height="1cm" valing="middle"><b style="font-size:15pt;">Dados do Equipamento:</b></td>
			</tr>
		</table>
		<table style="width:19cm; height:5cm; border-bottom:1px solid #000;">
			<tr>
				<td width="1cm" height="1cm"><b>Imei:</b></td>
				<td width="2cm">'.$m_imei.'</td>
				<td width="1cm">&nbsp;</td>
				<td width="2cm">&nbsp;</td>
			</tr>
			<tr>
				<td width="1cm" height="1cm"><b>Marca:</b></td>
				<td width="2cm">'.$marca.'</td>
				<td width="1cm"><b>Modelo:</b></td>
				<td width="2cm">'.$modelo.'</td>
				<td width="2cm" align="right"><b>Nr. Série:</b></td>
				<td width="4cm">'.$nserie.'</td>
			</tr>
		</table>
			'.(count($software_arr) > 0  ? ' 
		<table style="width:19cm;">
			<tr>
				<td height="0.5cm" valing="middle"><b style="font-size:12pt;">Software</b></td>
			</tr>
		</table>
		<table  style="width:19cm; border-bottom:1px solid #000;">
		<tr>
		<td style="font-size:10pt">												
			'.implode(', ', $software_arr).'		
		</td>		
		</tr>
		</table>' : '').'
		
		'.(count($hardware_arr) > 0 ? ' 
		<table style="width:19cm;">
			<tr>
				<td height="0.5cm" valing="middle"><b style="font-size:12pt;">Hardware</b></td>
			</tr>
		</table>
		<table  style="width:19cm; border-bottom:1px solid #000;">
		<tr>
		<td style="font-size:10pt">												
			'.implode(', ', $hardware_arr).'
		
		</td>		
		</tr>
		</table>' : '').'
		<table style="width:19cm; height:5cm;">
			<tr><td height="1cm"><b>Avaria Reportada:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($avaria).'</td></tr>
		</table>
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Estado do Equipamento:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($estadoequip).'</td></tr>
		</table>
		<!--table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Acessórios</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($acessorios).'</td></tr>
		</table-->
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.5cm"><b>Relatório de Intervenção</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($relintervencao).'</td></tr>
		</table>
		'.nl2br($observacoes).'
		<table style="width:19cm; height:5cm;">
			<tr><td height="0.2cm">&nbsp;</td></tr>
			<tr><td height="0.5cm"><b>Orçamento:</b></td></tr>
		</table>
		'.($lin!='' ? '
		<table style="width:19cm; height:5cm;" >
			<tr>
				<td style="border-bottom: 0.1px solid #000;" align="center" width="1.5cm" height="0.5cm"><b>Qtd.</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center" width="3.5cm"><b>Referência</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center" width="18cm"><b>Descrição</b></td>				
				
			</tr>
			'.$lin.'
		</table>' : '').'
		<table style="width:19cm; height:5cm;" >
			<tr>
				<td align="right" width="5cm" height="0.2cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
				<td align="right" width="5cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
			</tr>
			<tr>
				<td align="right" width="5cm" height="0.5cm"><b>&nbsp;</b></td>
				<td align="left" width="7cm">&nbsp;</td>
				<td align="right" width="5cm" style="font-size:18px"><b>Total:</b></td>';

		/*
		###VALIDAÇÃO DE NOVO CAMPO M_ACEITE PARA VERIFICAR SE O ORÇAMENTO FOI ACEITE
		 */
				if($m_aceite){
					$html.='<td align="left" width="7cm" style="font-size:18px">ORÇAMENTO NÃO ACEITE</td>';
				}
				else $html.='<td align="left" width="7cm" style="font-size:18px">€'.number_format($total, 2).'</td>';
			
			$html.='</tr>
			<tr>
				<td align="right" width="5cm" height="0.2cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
				<td align="right" width="5cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
			</tr>
		</table>

		<table style="width:19cm; height:5cm;">
			<tr><td align="center" height="1cm"><b style="font-size:10pt">Aceito as condições propostas para a reparação do meu equipamento neste establecimento.<br>
Autorizo receber informação sobre o estado da(s) sua(s) encomenda(s) /reparação(ões), dos nossos produtos e serviços, bem como newsletters, SMS e outras informações de carácter informativo e comercial.</b></td></tr>';


			if($dataURI) $html .='<tr><td align=right><img src="'.$dataURI.'"></td></tr>';
			$html .= '<tr><td height="1cm" align="center"><b>Data:</b> '.$signed_date.'&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>
			<tr><td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</td></tr>';


			$html .= '</table><break>';



		// ESCREVE E IMPRIME
		//$mpdf = new mPDF('c', 'A4');
		$mpdf = new \Mpdf\Mpdf(['orientation' => 'P']);
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

		//$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		$mpdf->AddPageByArray([
		    'margin-left' 	=> '9mm',
		    'margin-right'	=> '9mm',
		    'margin-top' 	=> '9mm',
		    'margin-bottom' => '9mm',
		]);
		if($_folhas == 2)
		{
			$mpdf->WriteHTML($html);
			$mpdf->WriteHTML('<pagebreak>'.$html);
		}
		else
		{
			$mpdf->WriteHTML($html);
		}

		$mpdf->Output("DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).' - Relatorio.pdf', 'D');
	}
}

function pdfReparacao($id)
{
	 pdfOrcamento($id);
	/*
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas WHERE ID="'.$id.'"');
	if($rs)
	{
		$IDb = $rs->db_data['ID'];
		$ID_Cliente = $rs->db_data['ID_Cliente'];
		$sql_cliente = 'SELECT * FROM clientes WHERE ID='.$ID_Cliente;
		$rs2->db_open($db_link, $DB_name, $sql_cliente);
		if(!$rs2->EOF())
		{
			$Cliente_Nome = $rs2->db_data['Nome'].' '.$rs2->db_data['Apelido'];
			if($rs2->db_data['Empresa'] != '')
			{
				$Cliente_Nome = $rs2->db_data['Empresa'];
			}
			$Cliente_Morada = $rs2->db_data['Morada'];
			$Cliente_CP = $rs2->db_data['Codigo_Postal'];
			$Cliente_Email = $rs2->db_data['Username'];
			$Cliente_Telefone = $rs2->db_data['Telefone'];
			$Cliente_Empresa = $rs2->db_data['Empresa'];
			$Cliente_Contribuinte = $rs2->db_data['Nif'];
		}
		$rs2->db_close();
		$ID_Tecnico = $rs->db_data['ID_Tecnico'];
		$sql_cliente = 'SELECT * FROM logins WHERE ID='.$ID_Tecnico;
		$rs2->db_open($db_link, $DB_name, $sql_cliente);
		if(!$rs2->EOF())
		{
			$Tecnico_Nome = $rs2->db_data['Nome'];
		}
		$rs2->db_close();
		$referencia = $rs->db_data['Referencia'];
		$marca = $rs->db_data['Marca'];
		$modelo = $rs->db_data['Modelo'];
		$tipoequip = $rs->db_data['TipoEquip'];
		$nserie = $rs->db_data['NSerie'];
		$avaria = $rs->db_data['Avaria'];
		$estadoequip = $rs->db_data['EstadoEquip'];
		$acessorios = $rs->db_data['Acessorios'];
		$observacoestec = $rs->db_data['ObservacoesTecnico'];
		$observacoes = '';
		if($observacoestec != '')
		{
			$observacoes = '<table style="width:100%; height:5cm;"><tr><td height="0.5cm"><b>Observações do Tecnico</b></td></tr><tr><td style="border: 0.1px solid #000;padding:2mm;">'.$observacoestec.'</td></tr></table>';
		}
		$relintervencao = $rs->db_data['RelatorioIntervencao'];
		$DataInsert = $rs->db_data['DataInsert'];
		$Estado = $rs->db_data['Estado'];
		switch($Estado)
		{
			case 0:
				$EstadoTexto = $GLOBALS['key_rmas_estado0'][$GLOBALS['site_lang']];
				break;
			case 1:
				$EstadoTexto = $GLOBALS['key_rmas_estado1'][$GLOBALS['site_lang']];
				break;
			case 2:
				$EstadoTexto = $GLOBALS['key_rmas_estado2'][$GLOBALS['site_lang']];
				break;
			case 3:
				$EstadoTexto = $GLOBALS['key_rmas_estado3'][$GLOBALS['site_lang']];
				break;
			case 4:
				$EstadoTexto = $GLOBALS['key_rmas_estado7'][$GLOBALS['site_lang']];
				break;
			case 5:
				$EstadoTexto = $GLOBALS['key_rmas_estado4'][$GLOBALS['site_lang']];
				break;
			case 6:
				$EstadoTexto = $GLOBALS['key_rmas_estado5'][$GLOBALS['site_lang']];
				break;
			case 7:
				$EstadoTexto = $GLOBALS['key_rmas_estado6'][$GLOBALS['site_lang']];
				break;
		}
		$rs->db_close();
		$rs2->db_open($db_link, $DB_name, 'SELECT * FROM rmas_linhas WHERE ID_ORG="'.$IDb.'" ORDER BY ID ASC');
		$lin = '';
		$total = 0;
		while(!$rs2->EOF())
		{
			$lin .= ' 
			<tr style="font-size:9pt;">
				<td height="7mm" style="border-bottom: 0.1px solid #000;" align="center">'.$rs2->db_data['Quantidade'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;padding-left:3mm;">'.$rs2->db_data['Cabecalho'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center">€'.number_format($rs2->db_data['Preco'], 2).'</td>
			</tr>';
			$total += ($rs2->db_data['Quantidade'] * $rs2->db_data['Preco']);
			$rs2->db_next();
		}
		$rs2->db_close();
		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>
		<p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Relatório Final</p>
		<table style="width:100%; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/logopdf.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
				</td>
				<td align="right">
					'.$GLOBALS['contactos_lojas'].'
				</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr >
				<td height="1cm"><b>Data Recebido:</b></td>
				<td>'.$DataInsert.'</td>
				<td align="center" width="10cm"><b style="font-size:15pt">Tracking: #'."DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</b></td>
			</tr>
			<tr>
				<td height="1cm"><b>Cliente:</b></td>
				<td>'.$Cliente_Nome.'</td>
				<td align="center"><b>Técnico:</b>&nbsp;&nbsp;'.$Tecnico_Nome.'</td>
			</tr>
			<tr>
				<td height="1cm"><b>Contacto:</b></td>
				<td>'.$Cliente_Telefone.'</p></td>
				<td>&nbsp;</td>
			</tr>
		</table>
		<table style="width:100%;">
			<tr>
				<td height="1cm" valing="middle"><b style="font-size:15pt;">Dados do Equipamento:</b></td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr>
				<td width="1cm" height="1cm"><b>Referência:</b></td>
				<td width="2cm">'.$referencia.'</td>
				<td width="1cm">&nbsp;</td>
				<td width="2cm">&nbsp;</td>
			</tr>
			<tr>
				<td width="1cm" height="1cm"><b>Marca:</b></td>
				<td width="2cm">'.$marca.'</td>
				<td width="1cm"><b>Modelo:</b></td>
				<td width="2cm">'.$modelo.'</td>
				<td width="2cm" align="right"><b>Nr. Série:</b></td>
				<td width="4cm">'.$nserie.'</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td height="1cm"><b>Avaria Reportada:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($avaria).'</td></tr>
		</table>
		<!--table style="width:100%; height:5cm;">
			<tr><td height="0.5cm"><b>Estado do Equipamento:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($estadoequip).'</td></tr>
		</table-->
		<!--table style="width:100%; height:5cm;">
			<tr><td height="0.5cm"><b>Acessórios</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($acessorios).'</td></tr>
		</table-->
		<table style="width:100%; height:5cm;">
			<tr><td height="0.5cm"><b>Relatório de Intervenção</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($relintervencao).'</td></tr>
		</table>
		'.nl2br($observacoes).'
		<table style="width:100%; height:5cm;">
			<tr><td height="0.2cm">&nbsp;</td></tr>
			<tr><td height="0.5cm"><b>Descritivo:</b></td></tr>
		</table>
		<table style="width:100%; height:5cm;" >
			<tr>
				<td style="border-bottom: 0.1px solid #000;" align="center" width="1.5cm" height="0.5cm"><b>Qtd.</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center" width="15cm"><b>Descrição</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center" width="3cm"><b>Valor Ilíquido</b></td>
			</tr>
			'.$lin.'
		</table>
		<table style="width:100%; height:5cm;" >
			<tr>
				<td align="right" width="5cm" height="0.2cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
				<td align="right" width="5cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
			</tr>
			<tr>
				<td align="right" width="5cm" height="0.5cm"><b>Valor Iliquido</b></td>
				<td align="left" width="7cm">€'.number_format($total, 2).'</td>
				<td align="right" width="5cm"><b>Total:</b></td>
				<td align="left" width="7cm">€'.number_format(($total * 1.23), 2).'</td>
			</tr>
			<tr>
				<td align="right" width="5cm" height="0.2cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
				<td align="right" width="5cm">&nbsp;</td>
				<td align="left" width="7cm">&nbsp;</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td align="center" height="1cm"><!--b style="font-size:10pt">Aceito as condições propostas para a reparação do meu equipamento neste establecimento.</b--></td></tr>
			<tr><td height="1cm" align="center"><b>Data:</b> ______/______/__________&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>
			<tr><td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</tr>
		</table>';
		// ESCREVE E IMPRIME
		$mpdf = new mPDF();
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);
		$mpdf->WriteHTML($html);
		$mpdf->Output("DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).' - Reparacao.pdf', 'D');
	}*/
}

function pdfPedidoRecolha($enc)
{
	$enc = base64_decode($enc);
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;
	$rs->db_open($db_link, $DB_name, 'SELECT * FROM pedido_recolha WHERE ID="'.$enc.'"');
	if(!$rs->EOF())
	{
		$Cliente_Nome = $rs->db_data['Nome'];
		$Cliente_Telefone = $rs->db_data['Telefone'];
		$Cliente_Email = $rs->db_data['Email'];
		$Cliente_Morada = $rs->db_data['Morada'];
		$Cliente_CP = $rs->db_data['CodigoPostal'];
		$marca = $rs->db_data['Marca'];
		$modelo = $rs->db_data['Modelo'];
		$nserie = $rs->db_data['NrSerie'];
		$avaria = $rs->db_data['Avaria'];
		$acessorios = $rs->db_data['Acessorios'];
		$estadoequip = $rs->db_data['Estado'];
		$garantia = ($rs->db_data['Garantia'] == '0') ? 'Sim' : 'Não';
		$ultireparacao = $rs->db_data['UltimaReparacao'];
		$datarecolha = $rs->db_data['DataRecolha'];
		$DataInsert = $rs->db_data['DataCriacao'];
		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>
		<p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Pedido de Recolha</p>
		<table style="width:100%; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/logopdf.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>		
				</td>
				<td align="right">
					<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;" href="'.$GLOBALS['site_http_url'].'">www.dotbright.pt</a><br>
					Rua Conselheiro<br>Luís de Magalhães. Nº6<br>
					4470-616 Moreira – Maia<br>
					<b>Telefone:</b> 22 941 90 67<br>
					<b>E-mail:</b> <a style="text-decoration:none;" href="mailto:geral@dotbright.pt">geral@dotbright.pt</a>
				</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr >
				<td height="1cm"><b>Data Criado:</b></td>
				<td>'.$DataInsert.'</td>
				<td align="center" width="10cm"></td>
			</tr>
			<tr>
				<td height="1cm"><b>Cliente:</b></td>
				<td>'.$Cliente_Nome.'</td>
				<td>&nbsp;</td>
			</tr>
			<tr>
				<td height="1cm"><b>Contacto:</b></td>
				<td>'.$Cliente_Telefone.'</p></td>
				<td><b>E-Mail:</b>&nbsp;&nbsp;&nbsp;'.$Cliente_Email.'</td>
			</tr>
			<tr>
				<td height="1cm"><b>Morada:</b></td>
				<td>'.$Cliente_Morada.'</p></td>
				<td><b>Código Postal:</b>&nbsp;&nbsp;&nbsp;'.$Cliente_CP.'</td>
			</tr>
		</table>
		<table style="width:100%;">
			<tr>
				<td height="1cm" valing="middle"><b style="font-size:15pt;">Dados do Equipamento:</b></td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr>
				<td width="1cm" height="1cm"><b>Marca:</b></td>
				<td width="2cm">'.$marca.'</td>
				<td width="1cm"><b>Modelo:</b></td>
				<td width="2cm">'.$modelo.'</td>
				<td width="2cm" align="right"><b>Nr. Série:</b></td>
				<td width="4cm">'.$nserie.'</td>
			</tr>
			<tr>
				<td width="2cm" height="1cm"><b>Garantia:</b></td>
				<td width="2cm" align="left">'.$garantia.'</td>
				<td width="2cm"><b>Nr. Ultima Reparação:</b></td>
				<td width="2cm" align="left">'.$ultireparacao.'</td>
				<td width="2cm" align="right"><b>Data Recolha:</b></td>
				<td width="4cm">'.$datarecolha.'</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td height="1cm"><b>Avaria Reportada:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($avaria).'</td></tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td height="0.5cm"><b>Estado do Equipamento:</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($estadoequip).'</td></tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td height="0.5cm"><b>Acessórios</b></td></tr>
			<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($acessorios).'</td></tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td style="border: 0.1px solid #000;padding:2mm; font-size:8pt;">
			<p style="font-weight:bold">Condições gerais:</p>
			<p> - Após atribuído o nº da recolha, o cliente está automaticamente a aceitar as condições abaixo descritas.</p>
			<p> - O cliente deverá anexar o formulário impresso junto do equipamento avariado.</p>
			<p> - A recolha e envio é efetuada através da empresa Nacex, tendo este, um custo total para o cliente 12,5€ (IVA incluído)</p>
			<p> - É da responsabilidade do cliente o acondicionamento adequado para o envio.</p>
			<p> - Garantia de 18 meses para todas as reparações no nosso laboratório técnico.</p>
			<p> - Caso verificamos selos de garantia violados, automaticamente perde a garantia.</p>
			</td></tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td align="center" height="1cm"><b style="font-size:10pt">Aceito as condições propostas para a reparação do meu equipamento neste establecimento.</b></td></tr>
			<tr><td height="1cm" align="center"><b>Data:</b> ______/______/__________&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>
			<tr><td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</tr>
		</table>';
		// ESCREVE E IMPRIME
		//$mpdf = new mPDF();
		$mpdf = new \Mpdf\Mpdf(['orientation' => 'P']);
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);
		//$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		$mpdf->AddPageByArray([
		    'margin-left' 	=> '9mm',
		    'margin-right'	=> '9mm',
		    'margin-top' 	=> '9mm',
		    'margin-bottom' => '9mm',
		]);
		$mpdf->WriteHTML($html);
		$mpdf->Output("Pedido de Recolha - ".$Cliente_Nome.".pdf", 'D');
	}
	$rs->db_close();
}

function pdfRMACliente($id)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$rs->db_open($db_link, $DB_name, 'SELECT * FROM rmas_cliente WHERE id="'.$id.'"');
	if($rs)
	{
		$IDb = $rs->db_data['id'];

		$nome = $rs->db_data['nome'].' '.$rs->db_data['apelido'];
		$morada = $rs->db_data['morada'];
		$codp = $rs->db_data['codp'];
		$email = $rs->db_data['email'];
		$telefone = $rs->db_data['telefone'];
		$empresa = $rs->db_data['empresa'];
		$nif = $rs->db_data['nif'];

		$nfat = $rs->db_data['nfat'];
		$nenc = $rs->db_data['nenc'];
		$obs = $rs->db_data['obs'];
		$obscli = $rs->db_data['obscli'];
		$estado = $rs->db_data['estado'];
		$DataInsert = $rs->db_data['criado'];
		$observacoes = '';
		if($obs)
			$observacoes = '
			<table style="width:100%; height:5cm;">
				<tr><td height="1cm"><b>Avaria:</b></td></tr>
				<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($obs).'</td></tr>
			</table>';
		if($obscli)
			$observacoes .= '
			<table style="width:100%; height:5cm;">
				<tr><td height="0.5cm"><b>Observações do Tecnico</b></td></tr>
				<tr><td style="border: 0.1px solid #000;padding:2mm;">'.nl2br($obscli).'</td></tr>
			</table>';

		switch($estado)
		{
			case 0:
				$EstadoTexto = 'Pendente';
				break;
			case 1:
				$EstadoTexto = 'Aceite';
				break;
			case 2:
				$EstadoTexto = 'Rececionado';
				break;
			case 3:
				$EstadoTexto = 'Rejeitado';
				break;
			case 4:
				$EstadoTexto = 'Substituído';
				break;
			case 5:
				$EstadoTexto = 'Creditado';
				break;
			case 6:
				$EstadoTexto = 'Reparado';
				break;
		}
		$rs->db_close();
		$rs2->db_open($db_link, $DB_name, 'SELECT * FROM rmas_cliente_linha WHERE rmas_cliente_id="'.$IDb.'" ORDER BY Ordem ASC');
		$lin = '';
		$total = 0;
		while(!$rs2->EOF())
		{
			$lin .= ' 
			<tr style="font-size:9pt;">
				<td height="7mm" style="border-bottom: 0.1px solid #000;" align="center">'.$rs2->db_data['qtd'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;padding-left:3mm;">'.$rs2->db_data['ref'].'</td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;">'.$rs2->db_data['descricao'].'</td>
			</tr>';
			$rs2->db_next();
		}
		$rs2->db_close();
		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>
		<p style="text-align:center; font-size:14pt; font-weight:bold;margin: 0;">Pedido de RMA</p>
		<table style="width:100%; height:5cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/logopdf.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
				</td>
				<td align="right">
					<a style="text-decoration:none; font-weight:bold; font-size:15pt;margin-bottom:5mm;" href="'.$GLOBALS['site_http_url'].'">www.dotbright.pt</a><br>
					Rua Conselheiro<br>Luís de Magalhães. Nº6<br>
					4470-616 Moreira – Maia<br>
					<b>Telefone:</b> 22 941 90 67<br>
					<b>E-mail:</b> <a style="text-decoration:none;" href="mailto:geral@dotbright.pt">geral@dotbright.pt</a>
				</td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr >
				<td height="1cm"><b>Data Criacao:</b></td>
				<td>'.$DataInsert.'</td>
				<td align="right" width="10cm"><b style="font-size:18pt">Tracking: #'."RMA".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'</b></td>
			</tr>
			<tr>
				<td height="1cm"><b>Nome:</b></td>
				<td>'.$nome.'</td>
				<td><b>Empresa:</b>&nbsp;&nbsp;'.$empresa.'</td>
			</tr>
			<tr>
				<td height="1cm"><b>Morada:</b></td>
				<td>'.$morada.'</p></td>
				<td><b>Cód. Postal:</b>&nbsp;&nbsp;'.$codp.'</td>
			</tr>
			<tr>
				<td height="1cm"><b>Telefone:</b></td>
				<td>'.$telefone.'</p></td>
				<td><b>Email:</b>&nbsp;&nbsp;'.$email.'</td>
			</tr>
		</table>
		<table style="width:100%;">
			<tr>
				<td height="1cm" valing="middle"><b style="font-size:15pt;">Dados da Encomenda:</b></td>
			</tr>
		</table>
		<table style="width:100%; height:5cm; border-bottom:1px solid #000;">
			<tr>
				<td width="1cm" height="1cm"><b>Nº Fatura:</b></td>
				<td width="2cm">'.$nfat.'</td>
				<td width="1cm"><b>Nº Encomenda:</b></td>
				<td width="2cm">'.$nenc.'</td>
			</tr>
		</table>
		'.$observacoes.'
		<table style="width:100%; height:5cm;">
			<tr><td height="0.2cm">&nbsp;</td></tr>
			<tr><td height="0.5cm"><b>Descritivo:</b></td></tr>
		</table>
		<table style="width:100%; height:5cm;" >
			<tr>
				<td style="border-bottom: 0.1px solid #000;" align="center" width="1.5cm" height="0.5cm"><b>Qtd.</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center"  width="3cm"><b>Referência</b></td>
				<td style="border-left: 0.1px solid #000;border-bottom: 0.1px solid #000;" align="center"  width="15cm"><b>Descrição</b></td>
			</tr>
			'.$lin.'
		</table>
		<br>
		<table style="width:100%; height:5cm;">
			<tr><td style="border: 0.1px solid #000;padding:2mm; font-size:8pt;">
				<p><b>ATENÇÃO: QUALQUER ALTERAÇÃO AO PRODUTO DE ORIGEM INVALIDA A GARANTIA DO FABRICANTE E SERÁ PRONTAMENTE DEVOLVIDO AO CLIENTE COM PORTES A CARGO DO MESMO COMO O SELO DE VIOLADO DE UM ARTIGO PARA DEVOLUÇÃO!</b></p>
				<p>1 - Deverá enviar o(s) artigo(s) para as nossas instalações até um prazo máximo de 7 dias após o pedido de RMA.</p>
				<p>2 - No caso de o RMA ser uma devolução comercial, deverá proceder ao envio para as nossas instalações no prazo de 14 dias após tomar posse física dos bens.</p>
				<p>3 - As despesas de envio para as nossas instalações ficam a cargo do cliente, ficando as despesas de entrega ao cliente a cargo da Stagêxito</p>
				<p>4 - A cópia da fatura ou venda a dinheiro, e não o original remetido a quando da venda, deverá ser enviada em conjunto com os produtos dentro da embalagem. Este ponto é fundamental. O não cumprimento desta norma levará á recusa imediata do RMA.</p>
				<p>5 - Os produtos devem ser devolvidos na caixa original do produto com cabos, manuais e CD´s de origem.</p>
				<p>6 - Após a receção, os produtos serão testados e verificados segundo a descrição da avaria.</p>
				<p>7 - A Stagêxito não se responsabiliza pelos danos no material aquando do transporte.</p>
				<p>8 - O cliente só deverá enviar o artigo após efetuar todos os testes e certificar-se que este não está em condições.</p>
				<p>9 - Se o produto nos for entregue em perfeitas condições de funcionamento, ou seja, não apresentar qualquer anomalia após ser testado, o mesmo será devolvido ao cliente, sendo este responsável pelo pagamento dos portes.</p>
				<p>10 - O não cumprimento destas normas leva à recusa do RMA e consequente devolução dos produtos, com despesas de envio a cargo do cliente.</p>
				<p>11 – Caso o artigo seja uma peça/componente e verificarmos o selo violado da embalagem original, não será aceite a devolução do mesmo.</p>
				<p>12 - O departamento técnico recebe os pedidos de RMA, de segunda a sexta-feira.</p>
				<p>13 - Independentemente do artigo estar ou não garantia, sempre que este seja rececionado para reparação e não se detetar nenhuma avaria, ou que esta tenha sido causada por utilização incorreta, poderá ser cobrado uma taxa para cobrir custos logísticos/técnicos.</p>
				<p>14 - Em nenhum caso nos responsabilizamos pela eventual perda parcial ou total de dados, programas ou software, instalados nos produtos entregues para reparação.</p>
				<p>15 - Não nos responsabilizamos por equipamentos/artigos não levantados 6 meses após a sua reparação. Depois deste período, o equipamento será considerado abandonado e entregue a um centro de reciclagem.</p>
			</td></tr>
		</table>
		<table style="width:100%; height:5cm;">
			<tr><td align="center" height="1cm"><!--b style="font-size:10pt">Aceito as condições propostas para a reparação do meu equipamento neste establecimento.</b--></td></tr>
			<tr><td height="1cm" align="center"><b>Data:</b> ______/______/__________&nbsp;&nbsp;&nbsp;_______________________________________</td></tr>
			<tr><td>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;(Assinatura do cliente)</tr>
		</table>';
		// ESCREVE E IMPRIME
		//$mpdf = new mPDF('c', 'A3');
		$mpdf = new \Mpdf\Mpdf(['orientation' => 'P']);
		$mpdf->allow_charset_conversion=true;
		$mpdf->charset_in='UTF-8';
		//$html = mb_convert_encoding($html, 'UTF-8', 'UTF-8');
		$mpdf->AddPageByArray([
		    'margin-left' 	=> '9mm',
		    'margin-right'	=> '9mm',
		    'margin-top' 	=> '9mm',
		    'margin-bottom' => '9mm',
		]);
		$html = iconv("UTF-8", "UTF-8//IGNORE", $html);
		$mpdf->WriteHTML($html);
		$mpdf->Output("RMA".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT).'.pdf', 'D');
	}
}
