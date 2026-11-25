<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>ADMIN</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="memogadget.com">
<meta name="owner" content="memogadget.com 1 ilimited license to dotbright.pt">

<?php
//if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
header('Content-type: text/html; charset: UTF-8');

require_once "../core/settings.php";
require_once "../core/db_engine_pdo.php";
require_once "../core/global.php";
require_once "../core/seo.php";
require_once "rmas/funcoes.inc.php";
//require_once('mpdf/mpdf.php');


	// error_reporting(E_ALL);
	// ini_set('display_errors', 1); 


$m_origem[0] = 'Online';
$m_origem[1] = 'Online';
$m_origem[2] = 'Matosinhos';
$m_origem[3] = 'Maia';



echo '<style>td{font-size:11px}</style>';

if(isset($_GET['f_relatorio']) && ($_GET['f_relatorio'] == 1 || $_GET['f_relatorio'] == 2 || $_GET['f_relatorio'] == 3 || $_GET['f_relatorio'] == 4 || $_GET['f_relatorio'] == 5)) relatorio1();
if(isset($_GET['f_relatorio2']) && ($_GET['f_relatorio2'] == 10 || $_GET['f_relatorio2'] == 20 || $_GET['f_relatorio2'] == 30 || $_GET['f_relatorio2'] == 40 || $_GET['f_relatorio2'] == 50)) relatorio2();


if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 101) relatorio_A();
if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 102) relatorio_B();
if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 103) relatorio_C();
if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 104) relatorio_D();
if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 105) relatorio_E();
if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 106) relatorio_F();



function relatorio_A($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$q = '';
	$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
	if(count($estados_fechados) > 0)
	{
		$q .= ' AND (';
		for($a=0; $a<count($estados_fechados); $a++)
		{
			if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
			else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

		}
		$q .= ')';
	}

	if($_GET['f_loja'])
	{
		$m_origem = intval($_GET['f_loja']);

		if($m_origem==1){$q .= ' and (m_origem = "1" OR m_origem = "3") ';}
		else $q .= ' and m_origem = "'.$m_origem.'"';
	}

	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';

	$sql = 'SELECT
				rmas.ID, rmas.DataUpdate,clientes.Nome, clientes.Apelido, rmas.total AS TOTAL
			FROM
				rmas
			INNER JOIN
				clientes ON clientes.ID = rmas.ID_Cliente
			WHERE
				1=1
				'.$q.'
			ORDER BY
				clientes.Nome, rmas.DataUpdate ASC
			';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Tracking/Número da reparação</b></td>
				<td width="5cm" height="1cm"><b>Nome do Cliente</b></td>
				<td width="5cm" height="1cm"><b>Data entrega</b></td>
				<td width="3cm" height="1cm"><b>Valor Pago</b></td>
			</tr>';
			$COUNT=0;
			$TOTAL2=0;
			while(!$rs->EOF())
			{
				$COUNT++;
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);
				$Nome = $rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
				$DataUpdate=$rs->db_data['DataUpdate'];
				$TOTAL2+=$TOTAL = $rs->db_data['TOTAL'];
				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td height="1cm">'.$DataUpdate.'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
			$linhas .= '<tr>
				<td width="4cm" height="1cm">qtd: '.$COUNT.'</td>
				<td width="5cm" height="1cm"></td>
				<td width="5cm" height="1cm"></td>
				<td align="right">'.number_format($TOTAL2,2).' €</td>
			</tr>';
		}


		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Relatório Diário de Reparações Entregues<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';



		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}

function relatorio_B($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$q = '';
	$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
	if(count($estados_fechados) > 0)
	{
		$q .= ' AND (';
		for($a=0; $a<count($estados_fechados); $a++)
		{
			if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
			else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

		}
		$q .= ')';
	}

	if($_GET['f_loja'])
	{
		$m_origem = intval($_GET['f_loja']);

		if($m_origem==1){$q .= ' and (m_origem = "1" OR m_origem = "3") ';}
		else $q .= ' and m_origem = "'.$m_origem.'"';
	}

	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';

	 $sql = 'SELECT
				rmas.ID, rmas.m_origem, rmas.m_estado
			FROM
				rmas
			WHERE
				1=1
				'.$q.'
			ORDER BY
				rmas.DataUpdate ASC
			';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Tracking/Número da reparação</b></td>
				<td width="5cm" height="1cm"><b>Estado</b></td>
				<td width="3cm" height="1cm"><b>Loja</b></td>
			</tr>';
			$TOTAL_h=0;
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);
				$m_estado = $rs->db_data['m_estado'];
				$m_origem = $rs->db_data['m_origem'];

				$loja = ($m_origem==1 ? 'Loja da Maia' : ($m_origem==2 ? 'Loja de Matosinhos' : ($m_origem==3 ? 'Loja da Maia' : '')));



				$sql = 'SELECT * FROM rmas_estados_lang WHERE ID_PAI='.$m_estado;
				$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
				$estados_nome = $rs2->db_data['Cabecalho'];
				$rs2->db_close();


				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$estados_nome.'</td>
					<td align="right">'.$loja.'</td>
				</tr>';
				$TOTAL_h++;

				$rs->db_next();
			}
			$rs->db_close();
		}


		$linhas .= '<tr>
			<td height="1cm"></td>
			<td height="1cm"></td>
			<td align="right">'.$TOTAL_h.'</td>
		</tr>';


		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Relatório Diário RMAS<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';



		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}


function relatorio_C($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$q = '';
	$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
	if(count($estados_fechados) > 0)
	{
		$q .= ' AND (';
		for($a=0; $a<count($estados_fechados); $a++)
		{
			if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
			else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

		}
		$q .= ')';
	}
	if($_GET['cliente_x'])
	{
		$sanitized_email = filter_var($_GET['cliente_x'], FILTER_SANITIZE_EMAIL);
		$q .= ' and clientes.Email = "'.$sanitized_email.'"';
	}
	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';

	$sql = 'SELECT
				rmas.ID, rmas.m_origem, rmas.m_estado,rmas.DataUpdate, rmas.total,clientes.Email AS TOTAL,clientes.Nome,clientes.Apelido
			FROM
				rmas
			INNER JOIN
				clientes ON clientes.ID = rmas.ID_Cliente
			WHERE
				1=1
				'.$q.'
			ORDER BY
				rmas.DataUpdate ASC
			';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Tracking/Número da reparação</b></td>
				<td width="5cm" height="1cm"><b>Nome do Cliente</b></td>
				<td width="5cm" height="1cm"><b>Data</b></td>
				<td width="3cm" height="1cm"><b>Valor</b></td>
				<td width="3cm" height="1cm"><b>Estado</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);
				$Nome = $rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
				$m_estado = $rs->db_data['m_estado'];
				$m_origem = $rs->db_data['m_origem'];
				$DataUpdate = $rs->db_data['DataUpdate'];
				$TOTAL = $rs->db_data['TOTAL'];

				$loja = ($m_origem==1 ? 'Loja da Maia' : ($m_origem==2 ? 'Loja de Matosinhos' : ($m_origem==3 ? 'Loja da Maia' : '')));



				$sql = 'SELECT * FROM rmas_estados_lang WHERE ID_PAI='.$m_estado;
				$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
				$estados_nome = $rs2->db_data['Cabecalho'];

				$rs2->db_close();


				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td height="1cm">'.$DataUpdate.'</td>
					<td align="right">'.$TOTAL.'</td>
					<td height="1cm">'.$estados_nome.'</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}


		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Relatório Extrato Cliente<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>

		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';


		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}

function relatorio_D($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs0 = new DataObj;
	$rs2 = new DataObj;
echo '<script src="'.$GLOBALS['site_http_url'].'external_api/jquery-1.7.1.min.js"></script>';
	//Data, técnico, valor, total do valor, número de rma, total rmas, marca modelo
	//
	//Vamos seleccionar todos os tecnicos
	//
	$q = '';
	if($_GET['f_tecnico'])
	{
		$sanitized_ID = filter_var($_GET['f_tecnico'], FILTER_SANITIZE_EMAIL);
		$q .= ' and ID = "'.$sanitized_ID.'"';
	}

	//$sql = 'SELECT * FROM logins where Grupo_ID<>6 and Grupo_ID<>8 and estado = 1 '.$q;
	$sql = 'SELECT * FROM logins where tecnico=1 and estado = 1 '.$q.' order by Nome';
	printdiv('line 432: '.$sql);
	$rs0->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs0->EOF())
	{

		while(!$rs0->EOF())
		{
			$ID_tecnico = $rs0->db_data['ID'];
			$Nome_tecnico = $rs0->db_data['Nome'];
			$TOTAL=0;

			$num_reparacoes_entregues=0;
			$num_reparacoes=0;

			$q = '';
			if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '')
			{
				$q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
			}
			$q1=$q2=$q;



			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-50);
			//print_r($estados_fechados);
			if(count($estados_fechados) > 0)
			{
				$q2 .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($estados_fechados[$a]!=16 && $estados_fechados[$a]!=17){
						if($a==0) $q2 .= 'm_estado = "'.$estados_fechados[$a].'"';
						else $q2 .= ' or m_estado = "'.$estados_fechados[$a].'"';
					}

				}
				$q2 .= ' ) '; 
			}


			//Software:
			$q3=' AND (software_dr=1 OR software_rc=1 OR software_na=1 OR software_cg=1 OR software_ci=1 OR software_bl=1)';
			$sql = 'SELECT distinct(rmas.ID) FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q1.' ORDER BY rmas.ID, DataUpdate ASC';

			printdiv('line 474: '.$sql,1);
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes=0;
			while(!$rs->EOF()){$num_reparacoes++;$rs->db_next();}
			$rs->db_close();

			$sql = 'SELECT distinct(rmas.ID), total FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q2.$q3.' ORDER BY rmas.ID, DataUpdate ASC';

			//printdiv('line 482: '.$sql);
			$tent = totalentregues($ID_tecnico,$q2);
			//printdiv('line 484: '.$tent['total'].' - '.$tent['valor']);


			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes_entregues=0;
			$TOTAL=0;
			while(!$rs->EOF()){$num_reparacoes_entregues++;$TOTAL+=$rs->db_data['total'];$rs->db_next();}
			$rs->db_close();


			//Hardware:
			$q3=' AND (hardware_tp=1 OR hardware_tnf=1 OR hardware_lp=1 OR hardware_lpb=1 OR hardware_nc=1 OR hardware_ssa=1 OR hardware_sa=1 OR hardware_sam=1 OR hardware_sal=1 OR hardware_saa=1 OR hardware_nl=1 OR hardware_ncrg=1 OR hardware_ncrw=1 OR hardware_svl=1 OR hardware_cfnf=1 OR hardware_cpnf=1 OR hardware_bhn=1 OR hardware_bvn=1 OR hardware_bsn=1 OR hardware_bof=1 OR hardware_caj=1 OR hardware_lsim=1 OR hardware_lmsd=1 OR hardware_lc=1)';

			$sql = 'SELECT distinct(rmas.ID), total FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q2.$q3.' ORDER BY rmas.ID, DataUpdate ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes_entregues_h=0;
			$TOTAL_h=0;
			while(!$rs->EOF()){$num_reparacoes_entregues_h++;$TOTAL_h+=$rs->db_data['total'];$rs->db_next();}
			$rs->db_close();



			if($num_reparacoes+$num_reparacoes_entregues+$num_reparacoes_h+$num_reparacoes_entregues_h > 0)
			{
				/**
				$linhas .= '<tr>
						<td colspan=3><b><a href="gera_relatorios.php?tab='.$_GET['tab'].'&f_relatorio='.$_GET['f_relatorio'].'&f_loja='.$_GET['f_loja'].'&f_tecnico='.$ID_tecnico.'&f_data1='.$_GET['f_data1'].'&f_data2='.$_GET['f_data2'].'&cliente_x='.$_GET['cliente_x'].'">'.$Nome_tecnico.'</a></b></td>
					</tr>';
					/**/
				$linhas_aux = '<tr>
						<td colspan=3>
						<div id="info-tecnico'.$ID_tecnico.'"></div>
						<script>
						$("#info-tecnico'.$ID_tecnico.'").html("A carregar data");						
    					$("#info-tecnico'.$ID_tecnico.'").load("gera_relatorios.php?tab='.$_GET['tab'].'&f_relatorio='.$_GET['f_relatorio'].'&f_loja='.$_GET['f_loja'].'&f_tecnico='.$ID_tecnico.'&f_data1='.$_GET['f_data1'].'&f_data2='.$_GET['f_data2'].'&cliente_x='.$_GET['cliente_x'].' #tableidt'.$ID_tecnico.'");
						</script>
						</td>
					</tr>';

					if(!$_GET['f_tecnico']) $linhas .= $linhas_aux;


				if($_GET['f_tecnico'])
				{
					//mostrar tudo
					//Software:
					//$q3=' AND (software_dr=1 OR software_rc=1 OR software_na=1 OR software_cg=1 OR software_ci=1 OR software_bl=1)';

					$sql = 'SELECT distinct(rmas.ID),cliente_nome,tipo_reparacao,total,DataUpdate,m_estado,m_pago,m_estado FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q1.$q2.' ORDER BY rmas.ID, DataUpdate ASC';
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
					/**
					$linhas .= '<tr><td colspan=3 ><table width=100% id="tableidt'.$ID_tecnico.'"><tr><td><b>RMA</b></td><td><b>Cliente</b></td><td><b>Tipo de reparação</b></td><td><b>Valor</b></td><td><b>Data</b></td><td><b>Estado</b></td><td><b>Pago</b></td></tr>';
					/**/
					$linhas .= '<tr><td colspan=3 ><table width=100% id="tableidt'.$ID_tecnico.'"><tr><td style="width:150px;"><b>Técnico</b></td><td><b>RMA</b></td><td></td><td></td><td><b>Data</b></td><td style="text-align:right;"><b>Valor</b></td></tr>';
					while(!$rs->EOF())
					{

						$sql = 'SELECT rel.Cabecalho, re.f_estado from rmas_estados re inner join rmas_estados_lang rel on re.ID=rel.ID_PAI where rel.ID_PAI = '.$rs->db_data['m_estado'];
						//$sql = 'SELECT * from rmas_estados_lang where ID_PAI = '.$rs->db_data['m_estado'];
						
						$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
						$estado=$rs2->db_data['Cabecalho'];
						$f_estado=$rs2->db_data['f_estado'];
						$rs2->db_close();

						$pago='---';if($rs->db_data['m_pago']==1)$pago='SIM';
						/**
						$linhas .= '<tr><td><a href="rmas/pagina.php?ID='.$rs->db_data['ID'].'">'.$rs->db_data['ID'].'</a></td><td>'.$rs->db_data['cliente_nome'].'</td><td>'.$rs->db_data['tipo_reparacao'].'</td><td>'.$rs->db_data['total'].'</td><td>'.$rs->db_data['DataUpdate'].' '.$rs->db_data['m_estado'].'</td><td>'.$estado.'</td><td>'.$pago.'</td></tr>';
						/**/
						$linhas .= '<tr><td>'.$Nome_tecnico.'</td><td><a href="rmas/pagina.php?ID='.$rs->db_data['ID'].'">'.$rs->db_data['ID'].'</a> '.$rs->db_data['m_estado'].'</td><td></td><td></td><td>'.$rs->db_data['DataUpdate'].' - '.$estado.' '.$f_estado.'</td><td style="text-align:right;">'.$rs->db_data['total'].'</td></tr>';
						$rs->db_next();
					}
					$rs->db_close();




					//Hardware:
					/*$q3=' AND (hardware_tp=1 OR hardware_tnf=1 OR hardware_lp=1 OR hardware_lpb=1 OR hardware_nc=1 OR hardware_ssa=1 OR hardware_sa=1 OR hardware_sam=1 OR hardware_sal=1 OR hardware_saa=1 OR hardware_nl=1 OR hardware_ncrg=1 OR hardware_ncrw=1 OR hardware_svl=1 OR hardware_cfnf=1 OR hardware_cpnf=1 OR hardware_bhn=1 OR hardware_bvn=1 OR hardware_bsn=1 OR hardware_bof=1 OR hardware_caj=1 OR hardware_lsim=1 OR hardware_lmsd=1 OR hardware_lc=1)';

					$sql = 'SELECT distinct(rmas.ID),cliente_nome,tipo_reparacao,total,DataUpdate,m_estado,m_pago FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q2.$q3.' ORDER BY DataUpdate ASC';
					$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
					while(!$rs->EOF())
					{
						$sql = 'SELECT * from rmas_estados_lang where ID_PAI = '.$rs->db_data['m_estado'];
						$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
						$estado=$rs2->db_data['Cabecalho'];
						$rs2->db_close();

						$pago='---';if($rs->db_data['m_pago']==1)$pago='SIM';

						$linhas .= '<tr><td><a href="rmas/pagina.php?ID='.$rs->db_data['ID'].'">'.$rs->db_data['ID'].'</a></td><td>hardware</td><td>'.$rs->db_data['cliente_nome'].'</td><td>'.$rs->db_data['tipo_reparacao'].'</td><td>'.$rs->db_data['total'].'</td><td>'.$rs->db_data['DataUpdate'].'</td><td>'.$estado.'</td><td>'.$pago.'</td></tr>';
						$rs->db_next();
					}
					$rs->db_close();*/


					$linhas .= '</table></td></tr>';
					$rs->db_close();
				}

				if($num_reparacoes+$num_reparacoes_entregues > 0)
				{
//$tent['total']
					$linhas .= '<tr>';
					//$linhas .= '<td height="1cm">'.number_format($num_reparacoes,'0',',','.').'</td>';
					$linhas .= '<td height="1cm" style="width:250px;"></td>';
						/**
					$linhas .= '<td height="1cm">'.number_format($num_reparacoes_entregues+$num_reparacoes_entregues_h,'0',',','.').'</td>';
					$linhas .= '<td align="right">'.number_format($TOTAL+$TOTAL_h,'2',',','.').' &euro;</td>';
						/**/
					/**/
					$linhas .= '<td height="1cm" ><b>Total de reparações:</b> '.number_format($tent['total'],'0',',','.').'</td>';
					$linhas .= '<td align="right"><b>Valor total das reparações:</b> '.number_format($tent['valor'],'2',',','.').' &euro;</td>';
					/**/
					$linhas .= '</tr>';
				}
			}
			$rs0->db_next();
		}
      
		//Data, técnico, valor, total do valor, número de rma, total rmas, marca modelo
        $cabecalho = '<tr>
			<td width="4cm" height="1cm"><b>Total de Reparações</b></td>
			<td width="5cm" height="1cm"><b>Total Entregues ao cliente</b></td>
			<td width="3cm" height="1cm"><b>Valor total das reparações</b></td>
		</tr>';
		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Número de reparações concluídas por técnico<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>

		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">';
		
		//$html .= $cabecalho;
		$html .= $linhas;
		$html .= '</table>';
	}
	$rs0->db_close();



		$prev=1;

		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}
//echo $DB_name; //dotbright_online_2018_bk.pt
}


function relatorio_E($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs0 = new DataObj;
	$rs2 = new DataObj;

	//Número rma, modelo/marca, imei, local, totais
	//Pesquisa sem datas, apenas por localidade
	//Estado enviado para garantia
	//
	//Vamos seleccionar todos os tecnicos
	//
	$q = '';

	$sql = 'SELECT
					ID, m_imei, m_novo_imei, m_tracking, m_referencia, ID_Cliente, ID_Cliente2, m_marca, m_modelo, sel_local,
					DataInsert, DataUpdate, DataFinalizado, m_nserie, DataUpdate, m_estado,
					cliente_nome, cliente_sobrenome, cliente_apelido, cliente_empresa, m_origem, m_pago
				FROM
					rmas
				WHERE
					(m_estado=19 || m_estado=20)
				ORDER BY
					ID DESC';


	$rs0->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs0->EOF())
	{
		while(!$rs0->EOF())
		{
			$ID_tecnico = $rs0->db_data['ID'];
			$Nome_tecnico = $rs0->db_data['Nome'];
			$zone = $rs0->db_data['sel_local'];
			$TOTAL=0;

			$num_reparacoes_entregues=0;
			$num_reparacoes=0;

			$IDb=$rs0->db_data['ID'];

			$num = "DB".str_pad((int)$IDb, 6, "0", STR_PAD_LEFT);

			$linhas .= '<tr>
				<td height="1cm">'.$num.'</td>
				<td height="1cm">'.$rs0->db_data['m_modelo'].' / '.$rs0->db_data['m_marca'].'</td>
				<td height="1cm">'.$rs0->db_data['m_imei'].'</td>
				<td align="right">'.$zone.'</td>
			</tr>';

			$rs0->db_next();
		}

		//Data, técnico, valor, total do valor, número de rma, total rmas, marca modelo

		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm;border-top:1px solid #000;border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Relatório de localização<br></b>
				</td>
			</tr>
		</table>

		<table style="width:19cm; height:5cm" border="1" cellpadding="4" cellspacing="0">
		<tr>
			<td width="4cm" height="1cm"><b>Número rma</b></td>
			<td width="5cm" height="1cm"><b>Modelo/marca</b></td>
			<td width="5cm" height="1cm"><b>Imei</b></td>
			<td width="3cm" height="1cm"><b>Local</b></td>
		</tr>
		'.$linhas.'
		</table>';
	}
	$rs0->db_close();



		$prev=1;

		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}
function relatorio_D_($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs0 = new DataObj;
	$rs2 = new DataObj;

	//Data, técnico, valor, total do valor, número de rma, total rmas, marca modelo
	//
	//Vamos seleccionar todos os tecnicos
	//
	$q = '';
	if($_GET['f_tecnico'])
	{
		$sanitized_ID = filter_var($_GET['f_tecnico'], FILTER_SANITIZE_EMAIL);
		$q .= ' and ID = "'.$sanitized_ID.'"';
	}

	$sql = 'SELECT * FROM logins where estado = 1 '.$q;
	$rs0->db_open($db_link, $GLOBALS['DB_name'], $sql);
	if(!$rs0->EOF())
	{
		while(!$rs0->EOF())
		{
			$ID_tecnico = $rs0->db_data['ID'];
			$Nome_tecnico = $rs0->db_data['Nome'];
			$TOTAL=0;

			$num_reparacoes_entregues=0;
			$num_reparacoes=0;

			$q = '';
			if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '')
			{
				$q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
			}
			$q1=$q2=$q;



			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
			if(count($estados_fechados) > 0)
			{
				$q2 .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($a==0) $q2 .= 'm_estado = "'.$estados_fechados[$a].'"';
					else $q2 .= ' or m_estado = "'.$estados_fechados[$a].'"';

				}
				$q2 .= ')';
			}



			//Software:
			$q3=' AND (software_dr=1 OR software_rc=1 OR software_na=1 OR software_cg=1 OR software_ci=1 OR software_bl=1)';
			$sql = 'SELECT distinct(rmas.ID) FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q1.' ORDER BY DataUpdate ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes=0;
			while(!$rs->EOF()){$num_reparacoes++;$rs->db_next();}
			$rs->db_close();

			$sql = 'SELECT distinct(rmas.ID), total FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q2.$q3.' ORDER BY DataUpdate ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes_entregues=0;
			$TOTAL=0;
			while(!$rs->EOF()){$num_reparacoes_entregues++;$TOTAL+=$rs->db_data['total'];$rs->db_next();}
			$rs->db_close();


			//Hardware:
			$q3=' AND (hardware_tp=1 OR hardware_tnf=1 OR hardware_lp=1 OR hardware_lpb=1 OR hardware_nc=1 OR hardware_ssa=1 OR hardware_sa=1 OR hardware_sam=1 OR hardware_sal=1 OR hardware_saa=1 OR hardware_nl=1 OR hardware_ncrg=1 OR hardware_ncrw=1 OR hardware_svl=1 OR hardware_cfnf=1 OR hardware_cpnf=1 OR hardware_bhn=1 OR hardware_bvn=1 OR hardware_bsn=1 OR hardware_bof=1 OR hardware_caj=1 OR hardware_lsim=1 OR hardware_lmsd=1 OR hardware_lc=1)';
			/*$sql = 'SELECT distinct(rmas.ID) FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q1.' ORDER BY DataUpdate ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes_h=0;
			while(!$rs->EOF()){$num_reparacoes_h++;$rs->db_next();}
			$rs->db_close();*/

			$sql = 'SELECT distinct(rmas.ID), total FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$ID_tecnico.' '.$q2.$q3.' ORDER BY DataUpdate ASC';
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			$num_reparacoes_entregues_h=0;
			$TOTAL_h=0;
			while(!$rs->EOF()){$num_reparacoes_entregues_h++;$TOTAL_h+=$rs->db_data['total'];$rs->db_next();}
			$rs->db_close();



			if($num_reparacoes+$num_reparacoes_entregues+$num_reparacoes_h+$num_reparacoes_entregues_h > 0)
			{
				$linhas .= '<tr>
						<td colspan=4><b>'.$Nome_tecnico.'</b></td>
					</tr>';

				/*if($num_reparacoes+$num_reparacoes_entregues > 0)
				{

					$linhas .= '<tr>
						<td height="1cm">'.$num_reparacoes.'</td>
						<td height="1cm">'.$num_reparacoes_entregues.'</td>
						<td height="1cm">SOFTWARE</td>
						<td align="right">'.$TOTAL.'</td>
					</tr>';
				}
				if($num_reparacoes_h+$num_reparacoes_entregues_h > 0)
				{
					$linhas .= '<tr>
						<td height="1cm">'.$num_reparacoes.'</td>
						<td height="1cm">'.$num_reparacoes_entregues_h.'</td>
						<td height="1cm">HARDWARE</td>
						<td align="right">'.$TOTAL_h.'</td>
					</tr>';
				}*/
				if(($num_reparacoes+$num_reparacoes_entregues > 0) || ($num_reparacoes_h+$num_reparacoes_entregues_h > 0))
				{

					$linhas .= '<tr>
						<td height="1cm">'.$num_reparacoes.'</td>
						<td height="1cm">'.($num_reparacoes_entregues+$num_reparacoes_entregues_h).'</td>
						<td align="right">'.($TOTAL+$TOTAL_h).'</td>
					</tr>';
				}
			}


			$rs0->db_next();

	}

		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Número de reparações efetuada por técnico<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
		<tr>
			<td width="4cm" height="1cm"><b>Total de Reparações</b></td>
			<td width="5cm" height="1cm"><b>Total Entregues ao cliente</b></td>
			<td width="3cm" height="1cm"><b>Valor total das reparações</b></td>
		</tr>
		'.$linhas.'
		</table>';
	}
	$rs0->db_close();








		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

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

/* Relatórios Pré Configurados */
function relatorio1($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$f_tracking = ''; $f_nrserie = ''; $f_tipoequip = ''; $f_estado = ''; $f_origem = ''; $f_emp = ''; $f_cli = ''; $f_tlm = ''; $f_data1 = ''; $f_data2 = ''; $f_pago = '';

	$q = '';
	if(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 1)//Relatório Valor de Reparações
	{
		$q = '';
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';

		$sql = '
				SELECT
					DataInsert, sum(total) AS TOTAL
				FROM
					rmas
				WHERE
					1=1
					'.$q.'
				GROUP BY
					DataInsert ASC
				ORDER BY
					DataInsert ASC
				';

		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

		if(!$rs->EOF())
		{

			$linhas = '<tr>
				<td width="9cm" height="1cm"><b>Data</b></td>
				<td width="10cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$ID = $rs->db_data['ID'];
				$DataInsert = $rs->db_data['DataInsert'];
				$TOTAL = $rs->db_data['TOTAL'];
				$linhas .= '<tr>

					<td width="9cm" height="1cm">'.$DataInsert.'</td>
					<td width="10cm" align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}
		$titulo = 'Relatório Valor Reparações Por Dia';

	}
	elseif(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 2)//Relatório Número de Reparações
	{
		$q = '';
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, count(rmas.ID) AS TOTAL
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';

		/*$sql = '
				SELECT
					distinct rmas_logs.login_id,logins.Nome, date(rmas_logs.criado) as criado, (rmas.ID) AS TOTAL
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';*/





		$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, login_id AS TOTAL
				FROM
					rmas
				INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
				INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					rmas_logs.rma_id
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';


/*$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, count(rmas.ID) AS TOTAL
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';*/
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

		if(!$rs->EOF())
		{

			$linhas = '<tr>
				<td width="5cm" height="1cm"><b>Técnico</b></td>
				<td width="5cm" height="1cm"><b>Data</b></td>
				<td width="9cm" align="right"><b>TOTAL</b></td>
			</tr>';

			$lista_ids[]='';
			$dados[]='';
			$dados2[]='';

			while(!$rs->EOF())
			{
				$Nome = $rs->db_data['Nome'];
				$DataUpdate = $rs->db_data['criado'];
				$login_id = $rs->db_data['TOTAL'];
				if($dados2[$login_id]==1)
				{
					$dados[$login_id][2]++;
				}
				else
				{
					$dados2[$login_id]=1;

					$lista_ids[]=$login_id;

					$dados[$login_id][0]=$Nome;
					$dados[$login_id][1]=$DataUpdate;
					$dados[$login_id][2]=1;
				}

				$rs->db_next();
			}

			$i=0;
			$t=count($lista_ids);
			while($i<$t)
			{
				$login_id=$lista_ids[$i];

				$linhas .= '<tr>
					<td width="5cm" height="1cm">'.$dados[$login_id][0].'</td>
					<td width="5cm" height="1cm">'.$dados[$login_id][1].'</td>
					<td width="9cm" align="right">'.$dados[$login_id][2].'</td>
				</tr>';
				$i++;
			}

			$rs->db_close();
		}

		$titulo = 'Relatório Número de Reparações';
	}
	elseif(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 3)//Relatório de Reparações
	{
		$q = '';
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		/*$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, SUM(rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';*/

		$sql = '
				SELECT
					distinct rmas_logs.rma_id,logins.Nome, date(rmas_logs.criado) as criado, (rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id, m_estado
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'

				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{


			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Reparação</b></td>
				<td width="5cm" height="1cm"><b>Marca/Modelo</b></td>
				<td width="3cm" height="1cm"><b>Técnico</b></td>
				<td width="3cm" height="1cm"><b>Data</b></td>
				<td width="3cm" height="1cm"><b>Estado</b></td>
				<td width="7cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['rma_id'], 6, "0", STR_PAD_LEFT);

				$m_marca = $rs->db_data['m_marca'];
				$m_modelo = $rs->db_data['m_modelo'];
				$Nome = $rs->db_data['Nome'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['TOTAL'];
				$Data = $rs->db_data['criado'];
				$m_estado = $rs->db_data['m_estado'];



				$sql = 'SELECT * FROM rmas_estados_lang WHERE ID_PAI='.$m_estado;
				$rs2->db_open($db_link, $GLOBALS['DB_name'], $sql);
				$estados_nome = $rs2->db_data['Cabecalho'];
				$rs2->db_close();







				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$m_marca.' '.$m_modelo.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td height="1cm">'.$Data.'</td>
					<td height="1cm">'.$estados_nome.'</td>
					<td align="right">'.$TOTAL.' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Relatório de Reparações';
	}
	elseif(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 4)//Relatório de Reparações Entregues
	{		$q = '';
			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
			if(count($estados_fechados) > 0)
			{
				$q .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
					else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

				}
				$q .= ')';
			}
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		/*$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, SUM(rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';*/


//,


$sql = '
				SELECT
					distinct rmas_logs.rma_id, logins.Nome, rmas.DataUpdate as DataUpdate,clientes.Nome as nomecliente, clientes.Apelido as apelidocliente, date(rmas_logs.criado) as criado, (rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id
				FROM
					rmas
					inner JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					inner JOIN logins ON logins.ID = rmas_logs.login_id
					INNER JOIN clientes ON clientes.ID = rmas.ID_Cliente
				WHERE
					1=1
					'.$q.'
				Group BY rmas.ID
				ORDER BY
					rmas.ID, logins.Nome, rmas_logs.criado ASC
				';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Reparação</b></td>
				<td width="5cm" height="1cm"><b>Marca/Modelo</b></td>
				<td width="5cm" height="1cm"><b>Nome do Cliente</b></td>
				<td width="3cm" height="1cm"><b>Técnico</b></td>
				<td width="5cm" height="1cm"><b>Data entrega</b></td>
				<td width="7cm" align="right"><b>Valor Pago</b></td>
			</tr>';
			$sumtotal = 0;
			$j = 0;
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['rma_id'], 6, "0", STR_PAD_LEFT);
				$pd = looklastperson($rs->db_data['rma_id']);
				$m_marca = $rs->db_data['m_marca'];
				$m_modelo = $rs->db_data['m_modelo'];
				$Nome = $rs->db_data['Nome'];
				$Nomecliente = $rs->db_data['nomecliente'].' '.$rs->db_data['apelidocliente'];
				$DataUpdate = $rs->db_data['criado'];
				$DataUpdate2=$rs->db_data['DataUpdate'];
				$TOTAL = $rs->db_data['TOTAL'];
				$sumtotal += $TOTAL;
				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$m_marca.' '.$m_modelo.'</td>
					<td height="1cm">'.$Nomecliente.'</td>
					<td height="1cm">'.$pd['nome'].'</td>
					<td height="1cm">'.$DataUpdate2.'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';
				$j++;
				$rs->db_next();
			}
			$rs->db_close();

			$linhas .= '<tr>
					<td height="1cm">qtd: '.$j.'</td>
					<td height="1cm"></td>
					<td height="1cm"></td>
					<td height="1cm"></td>
					<td height="1cm"></td>
					<td align="right">'.number_format($sumtotal,2).' €</td>
				</tr>';
		}

		$titulo = 'Relatório de Reparações Entregues';
	}
	elseif(isset($_GET['f_relatorio']) && $_GET['f_relatorio'] == 5)//Extrato de Conta Por Cliente - Reparação
	{
		$q = '';
			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
			if(count($estados_fechados) > 0)
			{
				$q .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
					else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

				}
				$q .= ')';
			}
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					ID, m_imei, m_novo_imei, m_tracking, m_referencia, ID_Cliente, m_marca, m_modelo,
					DataUpdate, DataUpdate, DataFinalizado, m_nserie, DataUpdate, m_estado,
					cliente_nome, cliente_sobrenome, cliente_apelido, cliente_empresa, m_origem, m_pago , total
				FROM
					rmas
				WHERE
					1=1
					'.$q.'
				ORDER BY
					ID ASC
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="3cm" height="1cm"><b>Reparação</b></td>
				<td width="4cm" height="1cm"><b>Data</b></td>
				<td width="4cm" height="1cm"><b>Imei</b></td>
				<td width="4cm" height="1cm"><b>Pago</b></td>
				<td width="4cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);

				$DataInsert = $rs->db_data['DataInsert'];
				$m_imei = $rs->db_data['m_imei'];
				$Nome = $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['total'];
					$linhas .= '<tr>
					<td height="1cm" colspan="5">'.$Nome.'</td>
				</tr>';
				$linhas .= '<tr bgcolor="#c0c0c0">
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$DataInsert.'</td>
					<td height="1cm">'.$m_imei.'</td>
					<td height="1cm">'.($m_pago == 1 ? 'Não' : 'Sim').'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Extrato de Conta de Clientes Entregues';
	}


		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>

		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
				</td>
				<td align="right">
					<b>'.$titulo.'<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';
		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}


function looklastperson($id = ''){
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$output = array();

	$sql = 'SELECT max(rmas_logs.criado) as criado
			FROM rmas_logs
			INNER JOIN logins
            ON logins.id = rmas_logs.login_id
			WHERE rmas_logs.rma_id ='.$id;
$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
			if(!$rs->EOF())
			{
				 $data = $rs->db_data['criado'];


			}
			$rs->db_close();
			$output['data'] = $data;
$rs = new DataObj;

	$sql = 'SELECT logins.Nome as nome, max(rmas_logs.criado) as criado
			FROM rmas_logs
			INNER JOIN logins
            ON logins.id = rmas_logs.login_id
			WHERE rmas_logs.criado ="'.$data.'" and rmas_logs.rma_id ='.$id;
			$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
$output['sql'] = $sql;
			if(!$rs->EOF())
			{
				$output['nome'] = $rs->db_data['nome'];
				$output['criado'] = $rs->db_data['criado'];
			}
			$rs->db_close();
	return $output;
}

/*Relatórios P/ Pesquisa*/
function relatorio2($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$f_tracking = ''; $f_nrserie = ''; $f_tipoequip = ''; $f_estado = ''; $f_origem = ''; $f_emp = ''; $f_cli = ''; $f_tlm = ''; $f_data1 = ''; $f_data2 = ''; $f_pago = '';

	$q = '';


	$estados_fechados_entregue = estado_encomenda_rmas_fechados();
	// ESTADO DO RMA
	if((isset($_GET["f_estado"]) && $_GET["f_estado"] == '-1') || !isset($_GET["f_estado"]))
	{
		$estados_fechados = estado_encomenda_rmas_fechados();
		for($a=0; $a<count($estados_fechados); $a++)
		{
			$q .= ' AND m_estado <> '.$estados_fechados[$a];

		}
	}
	elseif(isset($_GET['f_estado']) && (trim($_GET['f_estado']) == '-10' || trim($_GET['f_estado']) == '-20' || trim($_GET['f_estado']) == '-30' || trim($_GET['f_estado']) == '-40' || trim($_GET['f_estado']) == '-50' || trim($_GET['f_estado']) == '-60'))
	{

		$estados_fechados = estado_encomenda_rmas_fechados_tipo(trim($_GET['f_estado']));
		// print_r($estados_fechados);
		if(count($estados_fechados) > 0)
		{
			$q .= ' AND (';
			for($a=0; $a<count($estados_fechados); $a++)
			{
				if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
				else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

			}
			$q .= ')';
		}
	}
	elseif(isset($_GET['f_estado']) && trim($_GET['f_estado']) != '') $q .= ' AND m_estado="'.trim($_GET['f_estado']).'"';
	elseif(isset($_GET['f_estado']) && trim($_GET['f_estado']) == '') $q .= ''; // TODOS OS ESTADO

	// ORIGEM - LOJAS OU ONLINE
	if(isset($_GET['f_origem']) && trim($_GET['f_origem']) != '') $q .= ' AND m_origem="'.trim($_GET['f_origem']).'"';
	elseif(isset($_GET['f_origem']) && trim($_GET['f_origem']) == '') $q .= '';
	else $q .= ' AND m_origem=1 ';

	// ENTRE DATAS
	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUp BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
	// elseif(isset($_GET['f_data1']) && $_GET['f_data1'] != '') $q .= ' AND ((DataFinalizado = \''.$_GET['f_data1'].'\') OR (DataInsert = \''.$_GET['f_data1'].'\')) ';

	// PAGAMENTO
	if(isset($_GET['f_pago']) && $_GET['f_pago'] != '') $q .= ' AND m_pago="'.$_GET['f_pago'].'" ';

	if(isset($_GET['f_relatorio2']) && $_GET['f_relatorio2'] == 10)
	{
		// $q = '';
		// if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		/*$sql = '
				SELECT
					DataInsert, sum(total) AS TOTAL, m_origem, m_pago, rmas_estados_lang.Cabecalho
				FROM
					rmas
					INNER JOIN rmas_estados_lang ON rmas_estados_lang.ID_PAI = rmas.m_estado
				WHERE
					1=1
					'.$q.'
				GROUP BY
					DataInsert ASC,rmas.m_origem,rmas.m_pago,rmas.m_estado
				ORDER BY
					DataInsert ASC,rmas.m_origem DESC,rmas.m_pago DESC,rmas.m_estado DESC
				';*/

		$sql = '
				SELECT
					distinct rmas_logs.rma_id,DataInsert, (total) AS TOTAL, m_origem, m_pago, rmas_estados_lang.Cabecalho
				FROM
					rmas
					INNER JOIN rmas_estados_lang ON rmas_estados_lang.ID_PAI = rmas.m_estado
				WHERE
					1=1
					'.$q.'
				ORDER BY
					DataInsert ASC,rmas.m_origem DESC,rmas.m_pago DESC,rmas.m_estado DESC
				';

		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		echo $sql;
		if(!$rs->EOF())
		{

			$linhas = '<tr>
				<td width="3cm" height="1cm"><b>Data</b></td>
				<td width="3cm" align="right"><b>Origem</b></td>
				<td width="4cm" align="right"><b>Pago</b></td>
				<td width="4cm" align="right"><b>Estado</b></td>
				<td width="4cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$DataInsert = $rs->db_data['DataInsert'];
				$TOTAL = $rs->db_data['TOTAL'];
				$estado = $rs->db_data['Cabecalho'];
				$m_pago = ($rs->db_data['m_pago'] == 1 ? 'Não' : 'Sim');
				$m_origem = $GLOBALS['m_origem'][$rs->db_data['m_origem']];

				$linhas .= '<tr>
					<td height="1cm">'.$DataInsert.'</td>
					<td align="right">'.$m_origem.'</td>
					<td align="right">'.$m_pago.'</td>
					<td align="right">'.$estado.'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}
		$titulo = 'Relatório Valor Reparações Por Dia';

	}
	elseif(isset($_GET['f_relatorio2']) && $_GET['f_relatorio2'] == 20)
	{
		// $q = '';
		// if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, count(rmas.ID) AS TOTAL, m_origem, m_pago, rmas_estados_lang.Cabecalho
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
					INNER JOIN rmas_estados_lang ON rmas_estados_lang.ID_PAI = rmas.m_estado
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC,rmas.m_origem,rmas.m_pago,rmas.m_estado
				ORDER BY
					logins.Nome, rmas_logs.criado ASC,rmas.m_origem DESC,rmas.m_pago DESC,rmas.m_estado DESC
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);

		if(!$rs->EOF())
		{

			$linhas = '<tr>
				<td width="3cm" height="1cm"><b>Técnico</b></td>
				<td width="3cm" height="1cm"><b>Data</b></td>
				<td width="3cm" align="right"><b>Origem</b></td>
				<td width="3cm" align="right"><b>Pago</b></td>
				<td width="4cm" align="right"><b>Estado</b></td>
				<td width="3cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$Nome = $rs->db_data['Nome'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['TOTAL'];

				$estado = $rs->db_data['Cabecalho'];
				$m_pago = ($rs->db_data['m_pago'] == 1 ? 'Não' : 'Sim');
				$m_origem = $GLOBALS['m_origem'][$rs->db_data['m_origem']];

				$linhas .= '<tr>
					<td height="1cm">'.$Nome.'</td>
					<td height="1cm">'.$DataUpdate.'</td>
					<td align="right">'.$m_origem.'</td>
					<td align="right">'.$m_pago.'</td>
					<td align="right">'.$estado.'</td>
					<td align="right">'.$TOTAL.'</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Relatório Número de Reparações';
	}
	elseif(isset($_GET['f_relatorio2']) && $_GET['f_relatorio2'] == 30)
	{
		// $q = '';
		// if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, SUM(rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id, m_origem, m_pago, rmas_estados_lang.Cabecalho
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
					INNER JOIN rmas_estados_lang ON rmas_estados_lang.ID_PAI = rmas.m_estado
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{


			$linhas = '<tr>
				<td height="1cm"><b>Reparação</b></td>
				<td height="1cm"><b>Marca/Modelo</b></td>
				<td height="1cm"><b>Técnico</b></td>
				<td align="right"><b>Origem</b></td>
				<td align="right"><b>Pago</b></td>
				<td align="right"><b>Estado</b></td>
				<td align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['rma_id'], 6, "0", STR_PAD_LEFT);

				$m_marca = $rs->db_data['m_marca'];
				$m_modelo = $rs->db_data['m_modelo'];
				$Nome = $rs->db_data['Nome'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['TOTAL'];
								$estado = $rs->db_data['Cabecalho'];
				$m_pago = ($rs->db_data['m_pago'] == 1 ? 'Não' : 'Sim');
				$m_origem = $GLOBALS['m_origem'][$rs->db_data['m_origem']];
				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$m_marca.' '.$m_modelo.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td align="right">'.$m_origem.'</td>
					<td align="right">'.$m_pago.'</td>
					<td align="right">'.$estado.'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Relatório de Reparações';
	}
	elseif(isset($_GET['f_relatorio2']) && $_GET['f_relatorio2'] == 4)
	{		$q = '';
			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
			if(count($estados_fechados) > 0)
			{
				$q .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
					else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

				}
				$q .= ')';
			}
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					logins.Nome, date(rmas_logs.criado) as criado, SUM(rmas.total) AS TOTAL, rmas.m_marca, rmas.m_modelo, rmas_logs.rma_id
				FROM
					rmas
					INNER JOIN rmas_logs ON rmas_logs.rma_id = rmas.ID
					INNER JOIN logins ON logins.ID = rmas_logs.login_id
				WHERE
					1=1
					'.$q.'
				GROUP BY
					 rmas_logs.login_id, date(rmas_logs.criado) ASC
				ORDER BY
					logins.Nome, rmas_logs.criado ASC
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Reparação</b></td>
				<td width="5cm" height="1cm"><b>Marca/Modelo</b></td>
				<td width="3cm" height="1cm"><b>Técnico</b></td>
				<td width="7cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['rma_id'], 6, "0", STR_PAD_LEFT);

				$m_marca = $rs->db_data['m_marca'];
				$m_modelo = $rs->db_data['m_modelo'];
				$Nome = $rs->db_data['Nome'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['TOTAL'];
				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$m_marca.' '.$m_modelo.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td align="right">'.$TOTAL.' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Relatório de Reparações Entregues';
	}
	elseif(isset($_GET['f_relatorio2']) && $_GET['f_relatorio2'] == 5)
	{
		$q = '';
			$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
			if(count($estados_fechados) > 0)
			{
				$q .= ' AND (';
				for($a=0; $a<count($estados_fechados); $a++)
				{
					if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
					else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

				}
				$q .= ')';
			}
		if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataUpdate BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';
		$sql = '
				SELECT
					ID, m_imei, m_novo_imei, m_tracking, m_referencia, ID_Cliente, m_marca, m_modelo,
					DataUpdate, DataUpdate, DataFinalizado, m_nserie, DataUpdate, m_estado,
					cliente_nome, cliente_sobrenome, cliente_apelido, cliente_empresa, m_origem, m_pago , total
				FROM
					rmas
				WHERE
					1=1
					'.$q.'
				ORDER BY
					ID ASC
				';
		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="3cm" height="1cm"><b>Reparação</b></td>
				<td width="4cm" height="1cm"><b>Data</b></td>
				<td width="4cm" height="1cm"><b>Imei</b></td>
				<td width="4cm" height="1cm"><b>Pago</b></td>
				<td width="4cm" align="right"><b>Valor</b></td>
			</tr>';
			while(!$rs->EOF())
			{
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);

				$DataInsert = $rs->db_data['DataInsert'];
				$m_imei = $rs->db_data['m_imei'];
				$Nome = $rs->db_data['cliente_nome'].' '.$rs->db_data['cliente_sobrenome'].' '.$rs->db_data['cliente_apelido'];
				$DataUpdate = $rs->db_data['criado'];
				$TOTAL = $rs->db_data['total'];
					$linhas .= '<tr>
					<td height="1cm" colspan="5">'.$Nome.'</td>
				</tr>';
				$linhas .= '<tr bgcolor="#c0c0c0">
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$DataInsert.'</td>
					<td height="1cm">'.$m_imei.'</td>
					<td height="1cm">'.($m_pago == 1 ? 'Não' : 'Sim').'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
		}

		$titulo = 'Extrato de Conta de Clientes Entregues';
	}


		$html .= '
		<style>body{font-family: Verdana, Geneva, sans-serif; }</style>

		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000;">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm;" ><br>
				</td>
				<td align="right">
					<b>'.$titulo.'<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';

		$prev=1;

		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}


function relatorio_F($prev = 0)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	$rs2 = new DataObj;

	$q = '';
	$estados_fechados = estado_encomenda_rmas_fechados_tipo(-60);
	if(count($estados_fechados) > 0)
	{
		$q .= ' AND (';
		for($a=0; $a<count($estados_fechados); $a++)
		{
			if($a==0) $q .= 'm_estado = "'.$estados_fechados[$a].'"';
			else $q .= ' or m_estado = "'.$estados_fechados[$a].'"';

		}
		$q .= ')';
	}

	if($_GET['f_loja'])
	{
		$m_origem = intval($_GET['f_loja']);

		if($m_origem==1){$q .= ' and (m_origem = "1" OR m_origem = "3") ';}
		else $q .= ' and m_origem = "'.$m_origem.'"';
	}

	if(isset($_GET['f_data1']) && isset($_GET['f_data2']) && $_GET['f_data1'] != '' && $_GET['f_data2'] != '') $q .= ' AND ((DataInsert BETWEEN \''.$_GET['f_data1'].'\' AND \''.$_GET['f_data2'].'\')) ';

	$sql = 'SELECT
				rmas.ID,rmas.DataInsert, clientes.Nome, clientes.Apelido, rmas.total AS TOTAL
			FROM
				rmas
			INNER JOIN
				clientes ON clientes.ID = rmas.ID_Cliente
			WHERE
				1=1
				'.$q.'
			ORDER BY
				clientes.Nome, rmas.DataUpdate ASC
			';


		$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
		// echo $sql;
		if(!$rs->EOF())
		{
			$linhas = '<tr>
				<td width="4cm" height="1cm"><b>Tracking/Número da reparação</b></td>
				<td width="4cm" height="1cm"><b>Data entrada</b></td>
				<td width="5cm" height="1cm"><b>Nome do Cliente</b></td>
				<td width="3cm" height="1cm"><b>Valor Pago</b></td>
			</tr>';
			$COUNT=0;
			$TOTAL2=0;
			while(!$rs->EOF())
			{
				$COUNT++;
				$rep = "DB".str_pad((int)$rs->db_data['ID'], 6, "0", STR_PAD_LEFT);
				$Nome = $rs->db_data['Nome'].' '.$rs->db_data['Apelido'];
				$DataInsert = $rs->db_data['DataInsert'];
				$TOTAL2+=$TOTAL = $rs->db_data['TOTAL'];
				$linhas .= '<tr>
					<td height="1cm">'.$rep.'</td>
					<td height="1cm">'.$DataInsert.'</td>
					<td height="1cm">'.$Nome.'</td>
					<td align="right">'.number_format($TOTAL,2).' €</td>
				</tr>';

				$rs->db_next();
			}
			$rs->db_close();
			$linhas .= '<tr>
				<td width="4cm" height="1cm">qtd: '.$COUNT.'</td>
				<td width="5cm" height="1cm"></td>
				<td width="5cm" height="1cm"></td>
				<td align="right">'.number_format($TOTAL2,2).' €</td>
			</tr>';
		}


		$html .= '
		<style>body{font-family:Verdana}</style>
		<table style="width:19cm; border-top:1px solid #000; border-bottom:1px solid #000">
			<tr>
				<td align="left">
					<img src="'.$GLOBALS['site_http_url'].'layout/'.PROJETO.'/logos/logo_top.png" style="width:6.5cm;margin-top:2mm; margin-bottom:2mm" ><br>
				</td>
				<td align="right">
					<b>Relatório Diário de Reparações Entregues<br></b>
					Período de '.$_GET['f_data1'].' até '.$_GET['f_data2'].'
				</td>
			</tr>
		</table>


		<table style="width:19cm; height:5cm;" border="1" cellpadding="4" cellspacing="0">
			'.$linhas.'
		</table>';



		$prev=1;
		if($prev == 1) 	echo $html;
		else
		{
			// ESCREVE E IMPRIME
			$mpdf = new mPDF('c', 'A4');
			$html = iconv("UTF-8", "UTF-8//IGNORE", $html);

			$mpdf->WriteHTML($html);

			$mpdf->Output('DB-'.limpa_special($titulo).'-'.date('YmdHis').'.pdf', 'D');
		}

}


function totalentregues($login_id = '',$q=''){
	global $DB_host, $DB_user, $DB_pass, $DB_name, $key_apagar;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;/**/
	$sql = 'SELECT distinct(rmas.ID), total FROM rmas inner join rmas_logs on rmas_logs.rma_id=rmas.ID WHERE login_id='.$login_id.$q;
	//printdiv($sql);

	$rs->db_open($db_link, $GLOBALS['DB_name'], $sql);
	$total = 0;
	$j=0;
	while(!$rs->EOF())
	{
		$total += $rs->db_data['total'];
		$j++;
		$rs->db_next();
	}
	$rs->db_close();
	return array('valor'=>$total,'total'=>($j));/**/
}


function printdiv($str = '',$display=1,$y = 1){
	$return.= '';
	$return.= '<div style="border:1px solid #dddddd;padding:10px;margin:30px;'.($display?'display:none':'').'">';
	$return.= $str;
	$return.= '</div>';
	if($y) echo $return;
}