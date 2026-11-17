<?php
	error_reporting(E_ERROR);
	ini_set('display_errors', 0);
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);
	$loja = $dados['Loja'];
	$and_loja='';
	$where_loja='';
	if($loja != '') {$where_loja=' WHERE Loja = "'.$loja.'"';$and_loja=' AND Loja = "'.$loja.'"';}


	$tem_graficos = 0;
	if($dados['Grupo_ID'] == 1) $tem_graficos = 1;

	?><link type="text/css" href="notify.css" rel="stylesheet">

    <style type="text/css">
		.titulos{
			font-size: 32px;
			font-weight: 200;
			padding:20px;
		}
		.border3 {
			border-radius: 3px;
			background-color: white;
			color: black;
			border: 2px solid #f44336;
		}
		.btn2 {
			background-color: #4CAF50;
			border: none;
			color: #FFFFFF;
			padding: 15px 32px;
			text-align: center;
			-webkit-transition-duration: 0.4s;
			transition-duration: 0.4s;
			margin: 2px 0 !important;
			text-decoration: none;
			font-size: 16px;
			width: 250px
		}
		.inativo{
			background-color: #CCCCCC;
		}
		button, html input[type="button"], input[type="reset"], input[type="submit"] {
			-webkit-appearance: button;
			cursor: pointer;
		}
		button, select {
			text-transform: none;
		}
		button {
			overflow: visible;
		}
		button, input, select, textarea {
			font: inherit;
			margin: 0;
		}
		.border33:hover {
			background-color: #f44336;
			color: white;
		}
    </style>

	<script type="text/javascript" src="notify.js"></script>

	<script>
		function showStickySuccessToast(new_message, id)
		{
			$().toastmessage('showToast',{
				text     : new_message,
				sticky   : true,
				position : 'top-right',
				type     : 'success',
				closeText: '',
				close    : function(){}
			});
		}
	</script>
<?php

$TOTAL_RMAS = 0; $TOTAL_CLIENTES = 0; $TOTAL_MARCAS = 0; $TOTAL_MODELOS = 0;$TOTAL_ESTADOS = 0;$TOTAL_TEC = 0;$TOTAL_LC = 0;
$TOTAL_POR_INTERVENCIONAR = 0; $TOTAL_MENSAGENS_PENDENTES = 0; $TOTAL_REPARADOS_POR_ENTREGAR = 0;
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs2=new DataObj;
$sql = "SELECT count(ID) as TOTAL_RMAS FROM rmas $where_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_RMAS = $rs2->db_data["TOTAL_RMAS"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL_CLIENTES FROM clientes WHERE rma_associado = 1 $and_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_CLIENTES = $rs2->db_data["TOTAL_CLIENTES"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL_MARCAS FROM marcas_rmas";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_MARCAS = $rs2->db_data["TOTAL_MARCAS"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL_MODELOS FROM modelos_rmas";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_MODELOS = $rs2->db_data["TOTAL_MODELOS"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL_ESTADOS FROM rmas_estados";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_ESTADOS = $rs2->db_data["TOTAL_ESTADOS"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL_TEC FROM rmas_descricao_tecnico $where_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_TEC = $rs2->db_data["TOTAL_TEC"];
$rs2->db_close();

$sql = "SELECT count(zone_id) as TOTAL_LC FROM localizacoes_lang";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_LC = $rs2->db_data["TOTAL_LC"];
$rs2->db_close();

$sms_saldo = 'Indisponivel';

$sql = "SELECT count(id) as TOTAL_EMP FROM rmas_emp WHERE estado = 2";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $emprestimos = $rs2->db_data["TOTAL_EMP"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL FROM rmas WHERE m_estado = 3 $and_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_POR_INTERVENCIONAR = $rs2->db_data["TOTAL"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL FROM rmas WHERE COALESCE(notes, '') <> '' AND COALESCE(lida, 0) = 0 $and_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_MENSAGENS_PENDENTES = $rs2->db_data["TOTAL"];
$rs2->db_close();

$sql = "SELECT count(ID) as TOTAL FROM rmas WHERE m_estado = 12 $and_loja";
$rs2->db_open($db_link, $DB_name, $sql);
if(!$rs2->EOF()) $TOTAL_REPARADOS_POR_ENTREGAR = $rs2->db_data["TOTAL"];
$rs2->db_close();


?>
<div class="content-inner">

          <section class="rma-direct-search" aria-labelledby="rma-direct-search-title">
            <div class="rma-direct-search-heading">
              <div>
                <span class="rma-direct-search-kicker">Acesso direto</span>
                <h2 id="rma-direct-search-title">Pesquisa rápida de equipamentos</h2>
              </div>
              <p>Listagens operacionais sem selecionar datas.</p>
            </div>
            <div class="rma-direct-search-grid">
              <a class="rma-direct-search-card rma-direct-search-card--info" href="rmas/list.php?pesquisar=1&amp;quick_filter=por_intervencionar&amp;f_origem=" aria-label="Ver <?php echo (int)$TOTAL_POR_INTERVENCIONAR; ?> equipamentos por intervencionar">
                <span class="rma-direct-search-icon" aria-hidden="true"><i class="fa fa-wrench"></i></span>
                <span class="rma-direct-search-copy"><strong>Por intervencionar</strong><small>Equipamentos por reparar</small></span>
                <span class="rma-direct-search-count"><?php echo (int)$TOTAL_POR_INTERVENCIONAR; ?></span>
                <span class="rma-direct-search-arrow" aria-hidden="true">&#8594;</span>
              </a>
              <a class="rma-direct-search-card rma-direct-search-card--warning" href="rmas/list.php?pesquisar=1&amp;quick_filter=mensagens_por_responder&amp;f_origem=" aria-label="Ver <?php echo (int)$TOTAL_MENSAGENS_PENDENTES; ?> equipamentos com mensagens por responder">
                <span class="rma-direct-search-icon" aria-hidden="true"><i class="fa fa-commenting-o"></i></span>
                <span class="rma-direct-search-copy"><strong>Mensagens por responder</strong><small>Respostas novas de clientes</small></span>
                <span class="rma-direct-search-count"><?php echo (int)$TOTAL_MENSAGENS_PENDENTES; ?></span>
                <span class="rma-direct-search-arrow" aria-hidden="true">&#8594;</span>
              </a>
              <a class="rma-direct-search-card rma-direct-search-card--success" href="rmas/list.php?pesquisar=1&amp;quick_filter=reparados_por_entregar&amp;f_origem=" aria-label="Ver <?php echo (int)$TOTAL_REPARADOS_POR_ENTREGAR; ?> equipamentos reparados por entregar">
                <span class="rma-direct-search-icon" aria-hidden="true"><i class="fa fa-check-circle-o"></i></span>
                <span class="rma-direct-search-copy"><strong>Reparados, por entregar</strong><small>Reparação concluída</small></span>
                <span class="rma-direct-search-count"><?php echo (int)$TOTAL_REPARADOS_POR_ENTREGAR; ?></span>
                <span class="rma-direct-search-arrow" aria-hidden="true">&#8594;</span>
              </a>
            </div>
          </section>

          <!-- Dashboard Counts Section-->
          <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow">
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">

                  <div class="item d-flex align-items-center" onclick="window.open('rmas/list.php','_self');">
                    <div class="icon bg-blue"><i class="icon-bill"></i></div>
                    <div class="title"><span>RMAS<br>Assistências</span></div>
                    <div class="number"><strong><?php echo $TOTAL_RMAS;?></strong></div>
                  </div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                  <div class="item d-flex align-items-center" onclick="window.open('sa_clientes/list.php','_self');">
                    <div class="icon bg-red"><i class="icon-user"></i></div>
                    <div class="title"><span>CLIENTES<br>Assistências</span> </div>
                    <div class="number"><strong><?php echo $TOTAL_CLIENTES;?></strong></div>
                  </div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                  <div class="item d-flex align-items-center"  onclick="window.open('sa_marcas_modelos/list.php','_self');">
                    <div class="icon bg-green"><i class="icon-padnote"></i></div>
                    <div class="title"><span>MARCAS<br>Modelos</span></div>
                    <div class="number"><strong><?php echo $TOTAL_MARCAS;?>/<?php echo $TOTAL_MODELOS;?></strong></div>
                  </div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                  <div class="item d-flex align-items-center" onclick="window.open('localizacoes/list.php','_self');">
                    <div class="icon bg-orange"><i class="icon-check"></i></div>
                    <div class="title"><span>LOCAL<br>Equipamento</span></div>
                    <div class="number"><strong><?php echo $TOTAL_LC;?></strong></div>
                  </div>
                </div>
              </div>
            </div>
          </section>

		  <section class="dashboard-counts no-padding-bottom">
            <div class="container-fluid">
              <div class="row bg-white has-shadow">
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
					<div class="item d-flex align-items-center" onclick="window.open('sa_descricao_tecnico/list.php','_self');">
						<div class="icon bg-violet"><i class="icon-page"></i></div>
						<div class="title"><span>DESCRIÇÃO<br>Técnico</span></div>
						<div class="number"><strong><?php echo $TOTAL_TEC;?></strong></div>
					</div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
                  	<div class="item d-flex align-items-center" onclick="window.open('sa_estados_rmas/list.php','_self');">
                    	<div class="icon bg-black"><i class="icon-form"></i></div>
                    	<div class="title"><span>RMAS<br>Estados</span> </div>
                    	<div class="number"><strong><?php echo $TOTAL_ESTADOS;?></strong></div>
					</div>
                </div>
                <!-- Item -->
                <div class="col-xl-3 col-sm-6">
					<div class="item d-flex align-items-center" style="background:#FFF;cursor:default">
						<div class="icon bg-green"><i class="icon-flask"></i></div>
						<div class="title"><span>SALDO<br>SMS</span></div>
						<div class="number"><strong><?php echo $sms_saldo;?></strong></div>
					</div>
                </div>
				<!-- Item -->
				<div class="col-xl-3 col-sm-6">
					<div class="item d-flex align-items-center" onclick="window.open('rmas/list_emp.php','_self');">
						<div class="icon bg-violet"><i class="icon-page"></i></div>
						<div class="title"><span>Empréstimos<br></span></div>
						<div class="number"><strong><?php echo $emprestimos;?></strong></div>
					</div>
                </div>
              </div>
            </div>
          </section>

		   <?php if($tem_graficos == 1) {?>

		  <br>
			<div class="row bg-white has-shadow" style="margin:0">
			<!-- Item -->
			<div class="col-md-6">

			<div class="bar-chart-example">
			<div class="card-close">
			<div class="dropdown">
			<button type="button" id="closeCard" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
			<div aria-labelledby="closeCard" class="dropdown-menu has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
			</div>
			</div>
			<div class="card-header d-flex align-items-center">
			<h3 class="h4" style="font-size:14px">TOTAL REPARAÇÕES ENTREGUES ÚLTIMOS 7 DIAS (€)</h3>
			</div>
			<div class="card-body">
			<canvas id="barChartExample"></canvas>
			</div>
			</div>
			</div>
			<div class="col-md-6">

			<div class="bar-chart-example">
			<div class="card-close">
			<div class="dropdown">
			<button type="button" id="closeCard" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
			<div aria-labelledby="closeCard" class="dropdown-menu has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
			</div>
			</div>
			<div class="card-header d-flex align-items-center">
			<h3 class="h4" style="font-size:14px">TOTAL REPARAÇÕES REGISTADAS/CONCLUÍDAS ÚLTIMOS 7 DIAS</h3>
			</div>
			<div class="card-body">
			<canvas id="barChartExample2"></canvas>
			</div>
			</div>
			</div>
			</div>
               <br>
			<div class="row bg-white has-shadow" style="margin:0">
			<!-- Item -->
			<div class="col-md-12">

			<div class="bar-chart-example">
			<div class="card-close">
			<div class="dropdown">
			<button type="button" id="closeCard" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
			<div aria-labelledby="closeCard" class="dropdown-menu has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
			</div>
			</div>
			<div class="card-header d-flex align-items-center">
			<h3 class="h4">TOTAL REPARAÇÕES REGISTADAS/CONCLUÍDAS ÚLTIMOS 12 MESES</h3>
			</div>
			<div class="card-body">
			<canvas id="barChartExample3"></canvas>
			</div>
			</div>
			</div>

			</div>
		   <?php }?>

		   <br>
		    <div class="recent-updates card">
                    <div class="card-close">
                      <div class="dropdown">
                        <button type="button" id="closeCard" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false" class="dropdown-toggle"><i class="fa fa-ellipsis-v"></i></button>
                        <div aria-labelledby="closeCard" class="dropdown-menu has-shadow"><a href="#" class="dropdown-item remove"> <i class="fa fa-times"></i>Close</a><a href="#" class="dropdown-item edit"> <i class="fa fa-gear"></i>Edit</a></div>
                      </div>
                    </div>
                    <div class="card-header">
                      <h3 class="h4">10 Últimas Assistências</h3>
                    </div>
                    <div class="card-body no-padding">
                      <!-- Item-->
                      <?php
					  $rs=new DataObj;	$rs2=new DataObj;
						$sql = "SELECT
								ID, m_imei, m_novo_imei, m_tracking, m_referencia, ID_Cliente, m_marca, m_modelo,
								DataInsert, DataUpdate, DataFinalizado, m_nserie, DataUpdate, m_estado,
								cliente_nome, cliente_sobrenome, cliente_apelido, cliente_empresa, m_origem, m_pago,
								 MONTH(DataInsert) as MES, DAY(DataInsert) as DIA
							FROM
								rmas
								$where_loja
							ORDER BY ID DESC
							LIMIT 10";

						$rs->db_open($db_link,$DB_name,$sql);
						$cor="#def2fa";$class="r1";$n="1";
						$count=0;
						if(!$rs->EOF())
						{
							while(!$rs->EOF())
							{
								$IDb = $rs->db_data['ID'];
								// $Tracking = $rs->db_data["m_tracking"];
								$Tracking = "DB".str_pad((int) $rs->db_data['ID'], 6, "0", STR_PAD_LEFT);
								$Ref = $rs->db_data["m_referencia"];
								$ID_Cliente = $rs->db_data["ID_Cliente"];
								$Marca = $rs->db_data["m_marca"];
								$Modelo = $rs->db_data["m_modelo"];
								$cliente_nome = $rs->db_data["cliente_nome"];
								$cliente_sobrenome = $rs->db_data["cliente_sobrenome"];
								$cliente_apelido = $rs->db_data["cliente_apelido"];
								$cliente_empresa = $rs->db_data["cliente_empresa"];
								$DataFinalizado = $rs->db_data["DataFinalizado"];
								$NrSerie = $rs->db_data["m_nserie"];
								$imei = $rs->db_data["m_imei"];
								$novo_imei = $rs->db_data["m_novo_imei"];

								$DataInsert = $rs->db_data["DataInsert"];
								$Dataacao = $rs->db_data["DataUpdate"];
								$DIA = $rs->db_data["DIA"];
								$MES = $rs->db_data["MES"];
						?>
						  <div class="item d-flex justify-content-between" style="padding:10px">
							<div class="info d-flex">
							  <div class="icon"><i class="icon-rss-feed"></i></div>
							  <div class="title">
								<a href="rmas/pagina.php?ID=<?php echo $IDb;?>" style="text-decoration:none">
								<h5><?php echo '('.$Tracking.') - '.$Marca.' '.$Modelo;?></h5>
								<p><?php echo $cliente_nome.' '.$cliente_sobrenome.' '.$cliente_apelido;?></p>
								</a>
							  </div>
							</div>
							<div class="date text-right"><strong><?php echo $DIA?></strong><span><?php echo $monthName[($MES-1)]?></span></div>
						  </div>
						<?php
								$rs->db_next();
							}
						}
						$rs->db_close();?>
                    </div>
                  </div>
</div>		<br><br>

	<?php
	if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4] == 1)
	{
		$rs=new DataObj;	$rs2=new DataObj;
		$sql = "SELECT ID, ID_From, was_read, Assunto FROM mensagens WHERE (ID_Cliente = '$admin_id' || ID_Cliente=0) and folder = '' and was_read=0 order by ID limit 0,5";
		$rs->db_open($db_link,$DB_name,$sql);
		$cor="#def2fa";$class="r1";$n="1";
		$count=0;
		if(!$rs->EOF())
		{
			while(!$rs->EOF())
			{
				$count++;
				$m_nome = '';
				$sql = "SELECT Nome FROM logins where ID=".$rs->db_data["ID_From"];
				$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF()) $m_nome = $rs2->db_data["Nome"];
				else  $m_nome = 'website';
				$rs2->db_close();

				if($rs->db_data["was_read"] == 0){ echo '<script>showStickySuccessToast("<b>'.$m_nome.'</b> enviou-lhe uma nova mensagem:<br />'.htmlentities($rs->db_data["Assunto"]).'<br /><a class=branco2 href=\"messages/index.php?frame=viewmsg.php?mail_id='.$rs->db_data["ID"].'\">LER MENSAGEM</a>") </script>';}

				$rs->db_next();
			}

		}
		$rs->db_close(1);
	}
?>
<script src="//cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>


<?php
$monthName2 = explode(",",'Jan,Fev,Mar,Abr,Mai,Jun,Jul,Ago,Set,Out,Nov,Dez');

function estado_encomenda_rmas_grupo($grupo_id=0)
{

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$return = array();

	$rs2 = new DataObj;
	$sql = 'SELECT ee.ID FROM rmas_estados ee WHERE f_estado = '.$grupo_id;
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$ID = $rs2->db_data['ID'];
			$return[] = $ID;

			$rs2->db_next();
		}
	}
	$rs2->db_close();

	return $return;
}

function estado_rmas_nome($estado_id=0)
{

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$return = '';

	$rs2 = new DataObj;
	$sql = 'SELECT ee.Cor, ee.Ordem, ee.Email, ee.f_leitura, eel.Cabecalho, eel.Cabecalho2, eel.Texto FROM rmas_estados ee INNER JOIN rmas_estados_lang eel ON eel.ID_PAI = ee.ID WHERE ee.ID='.$estado_id.' AND eel.Lang="pt"';
	$rs2->db_open($db_link,$DB_name,$sql);
	if(!$rs2->EOF())
	{
		$Cabecalho = $rs2->db_data['Cabecalho'];
		$return = $Cabecalho;
	}
	$rs2->db_close();

	return $return;
}
if($tem_graficos == 1)
{
    $gradient1 = $gradient2 = $gradient1_1 = $gradient2_1 = array();
	$TOTAL_MESES = array();


	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs2 = new DataObj;

	// TOTAL REPARAÇÕES ENTREGUES ÚLTIMOS 7 DIAS (€)
	$entregues = estado_encomenda_rmas_grupo(5);
	$sql = "SELECT  SUM(total) AS TOTAL_EUROS, YEAR(DataUpdate) as ANO, MONTH(DataUpdate) as MES, DAY(DataUpdate) as DIA FROM rmas WHERE (DataUpdate  >= DATE(NOW()) - INTERVAL 7 DAY) AND (m_estado = '".implode("' OR m_estado = '", $entregues)."') $and_loja GROUP BY YEAR(DataUpdate), MONTH(DataUpdate), DAY(DataUpdate) ORDER BY YEAR(DataUpdate) DESC, MONTH(DataUpdate) DESC, DAY(DataUpdate) DESC";
	// echo $sql;



//SELECT  SUM(total) AS TOTAL_EUROS, YEAR(DataUpdate) as ANO, MONTH(DataUpdate) as MES, DAY(DataUpdate) as DIA FROM rmas WHERE (DataUpdate  >= DATE(NOW()) - INTERVAL 7 DAY) AND (m_estado = '15' OR m_estado = '16' OR m_estado = '17' OR m_estado = '18' OR m_estado = '23' OR m_estado = '25')  GROUP BY YEAR(DataUpdate), MONTH(DataUpdate), DAY(DataUpdate) ORDER BY YEAR(DataUpdate) DESC, MONTH(DataUpdate) DESC, DAY(DataUpdate) DESC

	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$TOTAL_MESES[] = array('mes_dia' => $monthName2[($rs2->db_data["MES"]-1)].'-'.$rs2->db_data["DIA"], 'total' => $rs2->db_data["TOTAL_EUROS"]);

			$rs2->db_next();
		}
	}
	$rs2->db_close();

	$meses = array();
	$meses_total_entregas = array();
	for($a=0; $a<count($TOTAL_MESES); $a++)
	{
		$meses[] = $TOTAL_MESES[$a]['mes_dia'];
		$meses_total_entregas[] = number_format($TOTAL_MESES[$a]['total'], 2,'.','');
	}

//	var_dump($meses);
	//var_dump($meses_total_entregas);

	$gradient3[] = "#bce02e";
	$gradient3[] = "#e0642e";
	$gradient3[] = "#e0d62e";
	$gradient3[] = "#2e97e0";
	$gradient3[] = "#b02ee0";
	$gradient3[] = "#e02e75";
	$gradient3[] = "#5ce02e";


	// TOTAL REPARAÇÕES REGISTADAS/CONCLUÍDAS ÚLTIMOS 7 DIAS
	$TOTAL_MESES_REGISTADAS = array();
	$entregues = estado_encomenda_rmas_grupo(5);
	//if($admin_id == 6) print_r($entregues);
	$sql = "SELECT  COUNT(ID) AS TOTAL, YEAR(DataInsert) as ANO, MONTH(DataInsert) as MES, DAY(DataInsert) as DIA FROM rmas WHERE (DataInsert  >= DATE(NOW()) - INTERVAL 7 DAY) $and_loja GROUP BY YEAR(DataInsert), MONTH(DataInsert), DAY(DataInsert) ORDER BY YEAR(DataInsert) DESC, MONTH(DataInsert) DESC, DAY(DataInsert) DESC";
	// if($admin_id == 6) echo $sql.'<br>';
	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$TOTAL_MESES_REGISTADAS[$rs2->db_data["MES"].'-'.$rs2->db_data["DIA"]] = array('mes_dia' => $monthName2[$rs2->db_data["MES"]-1].'-'.$rs2->db_data["DIA"], 'total' => $rs2->db_data["TOTAL"]);

			$rs2->db_next();
		}
	}
	$rs2->db_close();


	$concluidas = estado_encomenda_rmas_grupo(4);
	//if($admin_id == 6) print_r($concluidas);
	$TOTAL_ESTADOS_CONCLUIDAS = array();
	$sql = "SELECT  COUNT(ID) AS TOTAL, YEAR(DataUpdate) as ANO, MONTH(DataUpdate) as MES, DAY(DataUpdate) as DIA FROM rmas WHERE (DataUpdate  >= DATE(NOW()) - INTERVAL 7 DAY) AND (m_estado = '".implode("' OR m_estado = '", $concluidas)."') $and_loja GROUP BY YEAR(DataUpdate), MONTH(DataUpdate), DAY(DataUpdate) ORDER BY YEAR(DataUpdate) DESC, MONTH(DataUpdate) DESC, DAY(DataUpdate) DESC";
	// if($admin_id == 6) echo $sql.'<br>';
	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$TOTAL_ESTADOS_CONCLUIDAS[$rs2->db_data["MES"].'-'.$rs2->db_data["DIA"]] = array('mes_dia' => $monthName2[$rs2->db_data["MES"]-1].'-'.$rs2->db_data["DIA"], 'total' => $rs2->db_data["TOTAL"]);
			$rs2->db_next();
		}
	}
	$rs2->db_close();

	$meses_registadas = array();
	$meses_total_concluidas = array();
	$meses_total_registadas = array();
	foreach($TOTAL_MESES_REGISTADAS AS $MES_ANO => $VALORES)
	{
		$meses_registadas[] = $VALORES['mes_dia'];
		$meses_total_registadas[] = $VALORES['total'];
		$meses_total_concluidas[] = ($TOTAL_ESTADOS_CONCLUIDAS[$MES_ANO]['total'] ?? 0);
		$gradient1[] = '#bce02e';
		$gradient2[] = '#e0642e';
	}


	// TOTAL REPARAÇÕES REGISTADAS/CONCLUÍDAS ÚLTIMOS 12 MESES
	$entregues = estado_encomenda_rmas_grupo(5);
	$TOTAL_MESES_REGISTADAS2 = array();
	$sql = "SELECT  COUNT(ID) AS TOTAL, YEAR(DataInsert) as ANO, MONTH(DataInsert) as MES FROM rmas WHERE (DataInsert  >= DATE(NOW()) - INTERVAL 12 MONTH) GROUP BY YEAR(DataInsert), MONTH(DataInsert) $and_loja ORDER BY YEAR(DataInsert) DESC, MONTH(DataInsert) DESC";
	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$TOTAL_MESES_REGISTADAS2[$rs2->db_data["ANO"].'-'.$rs2->db_data["MES"]] = array('mes_dia' => $rs2->db_data["ANO"].'-'.$monthName2[$rs2->db_data["MES"]-1], 'total' => $rs2->db_data["TOTAL"]);
			$rs2->db_next();
		}
	}
	$rs2->db_close();

	// $concluidas = estado_encomenda_rmas_grupo(4);
	$TOTAL_ESTADOS_CONCLUIDAS2 = array();
	$sql = "SELECT  COUNT(ID) AS TOTAL, YEAR(DataUpdate) as ANO, MONTH(DataUpdate) as MES FROM rmas WHERE (DataUpdate  >= DATE(NOW()) - INTERVAL 12 MONTH) AND (m_estado = '".implode("' OR m_estado = '", $concluidas)."') $and_loja GROUP BY YEAR(DataUpdate), MONTH(DataUpdate) ORDER BY YEAR(DataUpdate), MONTH(DataUpdate) DESC";
	// echo $sql;
	$rs2->db_open($db_link, $DB_name, $sql);
	if(!$rs2->EOF())
	{
		while(!$rs2->EOF())
		{
			$TOTAL_ESTADOS_CONCLUIDAS2[$rs2->db_data["ANO"].'-'.$rs2->db_data["MES"]] = array('mes_dia' => $monthName2[$rs2->db_data["MES"]-1].'-'.$rs2->db_data["DIA"], 'total' => $rs2->db_data["TOTAL"]);
			$rs2->db_next();
		}
	}
	$rs2->db_close();

	$meses_registadas2 = array();
	$meses_total_concluidas2 = array();
	$meses_total_registadas2 = array();
	foreach($TOTAL_MESES_REGISTADAS2 AS $MES_ANO => $VALORES)
	{
		$meses_registadas2[] = $VALORES['mes_dia'];
		$meses_total_registadas2[] = $VALORES['total'];
		$meses_total_concluidas2[] = ($TOTAL_ESTADOS_CONCLUIDAS2[$MES_ANO]['total'] ?? 0);
		$gradient1_1[] = '#bce02e';
		$gradient2_1[] = '#e0642e';
	}
}
?>


<script>
$(document).ready(function ()
{

  /*'use strict';*/

  <?php if($tem_graficos == 1) {?>

  var BARCHARTEXMPLE    = $('#barChartExample');
    var barChartExample = new Chart(BARCHARTEXMPLE, {
        type: 'bar',
        options: {scales: {xAxes: [{ display: true, gridLines: {color: '#eee'}}],  yAxes: [{ display: true, gridLines: {color: '#eee'}}]}, legend: {
                display: false
            }},
        data: {
            labels: ["<?php echo implode('" ,"', $meses);?>"],
            datasets: [
                {
                    label: "", backgroundColor: ["<?php echo implode('","', $gradient3);?>"], data: [<?php echo implode(',', $meses_total_entregas);?>]
				}
            ]
        }
    });



	var BARCHARTEXMPLE    = $('#barChartExample2');
    var barChartExample = new Chart(BARCHARTEXMPLE, {
        type: 'bar',
        options: {scales: {xAxes: [{ display: true, gridLines: {color: '#eee'}}],  yAxes: [{ display: true, gridLines: {color: '#eee'}}]},},
        data: {
            labels: ["<?php echo implode('" ,"', $meses_registadas);?>"],
            datasets: [
                {
                    label: "REGISTADAS", backgroundColor: ["<?php echo implode('","', $gradient1);?>"], data: [<?php echo implode(',', $meses_total_registadas);?>]
				},
                {
                    label: "CONCLUÍDAS", backgroundColor: ["<?php echo implode('","', $gradient2);?>"], data: [<?php echo implode(',', $meses_total_concluidas);?>]
                }
            ]
        }
    });

	var BARCHARTEXMPLE    = $('#barChartExample3');
    var barChartExample = new Chart(BARCHARTEXMPLE, {
        type: 'bar',
        options: {scales: {xAxes: [{ display: true, gridLines: {color: '#eee'}}],  yAxes: [{ display: true, gridLines: {color: '#eee'}}]},},
        data: {
            labels: ["<?php echo implode('" ,"', $meses_registadas2);?>"],
            datasets: [
                {
                    label: "REGISTADAS", backgroundColor: ["<?php echo implode('","', $gradient1_1);?>"], data: [<?php echo implode(',', $meses_total_registadas2);?>]
				},
                {
                    label: "CONCLUÍDAS", backgroundColor: ["<?php echo implode('","', $gradient2_1);?>"], data: [<?php echo implode(',', $meses_total_concluidas2);?>]
                }
            ]
        }
    });
  <?php }?>
 });
</script>
