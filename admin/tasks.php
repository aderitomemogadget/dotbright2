<?php
	global $admin_id;
	$dados = getDadosAdministrador($admin_id);

	?><link type="text/css" href="dashboard.css" rel="stylesheet"><link type="text/css" href="notify.css" rel="stylesheet"><script type="text/javascript" src="notify.js"></script>
	<script language="JavaScript" src="<?=$GLOBALS['site_http_url']?>admin/layout/theme/js/widgets.js"></script>
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
	$(document).ready(function()
	{
		AdminHome.init();
	});
	</script>
	<?php

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id,$tem_loja;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs->db_open($db_link,$DB_name,'SELECT max(Data) as Data FROM sync_history');
	$count=0;
	if(!$rs->EOF())$Data=$rs->db_data['Data'];
	$rs->db_close();

	if((isset($dados['Permissoes'][9]) && $dados['Permissoes'][9] == 1) && $tem_loja)
	{		
		?>
		<div id="content" class=""><?php 
			if(isset($GLOBALS['tem_loja']) && $GLOBALS['tem_loja']==1)
			{?>
				<div class="row">
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-green">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
							<div class="stats-title">CLIENTES ACTIVOS</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = "select ID from clientes where (Ativo=1 OR Estado=1)";
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_rows,2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 100%;"></div>
							</div>
							<div class="stats-desc">&nbsp;</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-purple">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-shopping-cart fa-fw"></i></div>
							<div class="stats-title">ENCOMENDAS HOJE</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = 'select ID from encomendas where Data_Criacao like "'.date('Y-m-d').'%"';
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_rows,2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Crescimento em comparação com a semana passada (0%)</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-blue">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-tags fa-fw"></i></div>
							<div class="stats-title">ENCOMENDAS VALOR HOJE</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = 'select sum(TotalDocComIva) as Total from encomendas where Data_Criacao like "'.date('Y-m-d').'%"';
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_data['Total'],2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Crescimento em comparação com a semana passada (0%)</div>
						</div>
					</div><?php
					if( isset($GOBALS['tem_log_kk']) && $GOBALS['tem_log_kk'] == 1)
					{?>
						<div class="col-lg-4 col-sm-6 col-xs-12">
							<div class="widget widget-stats bg-orange">
								<div class="stats-icon stats-icon-lg"><i class="fa fa-chart-line fa-fw"></i></div>
								<div class="stats-title">NÚMERO PRODUTOS FICHEIRO KK</div>
								<div class="stats-number" style="color:#fff"><?php

								$dt=date('Y-m-d');
								$myFile=$site_shell_url."files/kk".$dt.".txt"; 
								$no_of_lines = is_file($myFile) && is_readable($myFile) ? count(file($myFile)) : 0;
								echo number_format($no_of_lines,2);
								
								?></div>
								<div class="stats-progress progress">
									<div class="progress-bar" style="width: 0%;"></div>
								</div>
								<div class="stats-desc">Clique <a style="color:white" href="https://www.kuantokusta.pt/lojas/on4tech" target="_blank">aqui</a> para ver os seus produtos no KK</div>
							</div>
						</div><?php
					}?>
					<!--div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-red">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-chart-line fa-fw"></i></div>
							<div class="stats-title">NÚMERO PRODUTOS DESATIVADOS HOJE</div>
							<div class="stats-number" style="color:#fff"><?php

							$dt=date('Y-m-d');
							$myFile=$site_shell_url."files/artigos_desativados".$dt.".txt"; 
							$no_of_lines = is_file($myFile) && is_readable($myFile) ? count(file($myFile)) : 0;
							echo number_format($no_of_lines,2);
							
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Clique <a style="color:white" href="<?php echo 'https://www.mhr.pt/files/artigos_desativados'.$dt.'.txt'; ?>" download>aqui</a> para descarregar o ficheiro</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-black">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-chart-line fa-fw"></i></div>
							<div class="stats-title">NÚMERO PRODUTOS PRÓPRIOS SEM STOCK VISÍVEIS NO SITE</div>
							<div class="stats-number" style="color:#fff"><?php

							$dt=date('Y-m-d');
							$myFile=$site_shell_url."files/artigos_sem_stock".$dt.".txt"; 
							$no_of_lines = is_file($myFile) && is_readable($myFile) ? count(file($myFile)) : 0;
							echo number_format($no_of_lines,2);
							
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Clique <a style="color:white" href="https://www.mhr.pt/admin/products/avancada.php?campo=&total_prods=100&tipoorder=ASC&pesq_id=&pesq_ref=&pesq_desig=&pesq_preco=&pesq_ean=&pesq_stock=%3D0&pesq_peso=&sel_marca=&m_familia=0&pesq_suplier=13&sel_estado=&show_results=1" target="_blank">aqui</a> para ver todos os artigos com stock 0 próprios</div>
						</div>
					</div-->
				</div><?php
			}
			else {?>
				<div class="row">
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-green">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-globe fa-fw"></i></div>
							<div class="stats-title">CLIENTES ACTIVOS</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = "select ID from clientes where (Ativo=1 OR Estado=1)";
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_rows,2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 100%;"></div>
							</div>
							<div class="stats-desc">&nbsp;</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-purple">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-shopping-cart fa-fw"></i></div>
							<div class="stats-title">ENCOMENDAS HOJE</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = 'select ID from encomendas where Data_Criacao like "'.date('Y-m-d').'%"';
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_rows,2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Crescimento em comparação com a semana passada (0%)</div>
						</div>
					</div>
					<div class="col-lg-4 col-sm-6 col-xs-12">
						<div class="widget widget-stats bg-blue">
							<div class="stats-icon stats-icon-lg"><i class="fa fa-tags fa-fw"></i></div>
							<div class="stats-title">ENCOMENDAS VALOR HOJE</div>
							<div class="stats-number" style="color:#fff"><?php

								$sql = 'select sum(TotalDocComIva) as Total from encomendas where Data_Criacao like "'.date('Y-m-d').'%"';
								$rs->db_open($db_link,$DB_name,$sql);
								if(!$rs->EOF())
								{
									echo number_format($rs->db_data['Total'],2);
								}
								else echo '0';
								$rs->db_close();
							?></div>
							<div class="stats-progress progress">
								<div class="progress-bar" style="width: 0%;"></div>
							</div>
							<div class="stats-desc">Crescimento em comparação com a semana passada (0%)</div>
						</div>
					</div>					
				</div><?php
			}
			/*

			?>
			<!-- begin row -->
            <div class="row"><?php
            	if($GLOBALS['tem_log_macorlux']==1)
            	{?>
	                <div class="col-lg-3">
	                	<!-- begin widget -->
			            <div class="widget">
			                <div class="widget-header">

			                    <h4 class="m-b-5 m-t-5">Última importação de produtos da Macorlux <a href="importadores/macorlux/import.php?op=1" data-click="widget-reload"><i class="fa fa-redo" title="re-importar"></i></a></h4>
			                    <p class="f-s-11 m-b-10">
	                                <?=date ("Y-m-d H:i:s",filemtime('../files/log_produtos.txt'))?><a href="marketing/pagina.php" class="m-l-5"><i class="fa fa-cog" title="settings"></i></a>
	                            </p>
			                </div>
			                <div id="flot-browser-chart" data-height="270px"></div>
			            </div>
			            <!-- end widget -->
	                </div><?
            	}
            	if($GLOBALS['tem_log_macorlux_stocks']==1)
            	{
            	?>
                <div class="col-lg-3">
                	<!-- begin widget -->
			            <div class="widget">
			                <div class="widget-header">
			                    <h4 class="m-b-5 m-t-5">Última sincronização de stocks da Macorlux <a href="importadores/macorlux/import.php?op=2" data-click="widget-reload"><i class="fa fa-redo" title="re-importar"></i></a></h4>
			                    <p class="f-s-11 m-b-10">
                                    <?=date ("Y-m-d H:i:s",filemtime('../files/log_stocks.txt'))?> <a href="marketing/pagina.php" class="m-l-5"><i class="fa fa-cog" title="settings"></i></a>
			                    </p>
			                </div>
			                <div id="flot-browser-chart" data-height="270px"></div>
			            </div>
			            <!-- end widget -->
                </div><?
            	}
            	if($GLOBALS['tem_log_kk']==1)
            	{
            	?>


                <!-- begin col-3 -->
                <div class="col-lg-3">
                	<!-- begin widget -->
			            <div class="widget">
			                <div class="widget-header">
			                    <h4 class="m-b-5 m-t-5">Última geração de ficheiro kuantokusta <a href="importadores/macorlux/import.php?op=3" data-click="widget-reload"><i class="fa fa-redo" title="re-importar"></i></a></h4>
			                    <p class="f-s-11 m-b-10">
                                    <?=date ("Y-m-d H:i:s",filemtime('../files/log_kk.txt'))?>
			                    </p>
			                </div>
			                <div id="flot-browser-chart" data-height="270px"></div>
			            </div>
			            <!-- end widget -->
                </div><?
            	}
            	if($GLOBALS['tem_log_google']==1)
            	{
            	?>

				<!-- begin col-3 -->
                <div class="col-lg-3">
                	<!-- begin widget -->
			            <div class="widget">
			                <div class="widget-header">
			                    <h4 class="m-b-5 m-t-5">Última geração de ficheiro sitemap para google <a href="importadores/macorlux/import.php?op=4" data-click="widget-reload"><i class="fa fa-redo" title="re-importar"></i></a></h4>
			                    <p class="f-s-11 m-b-10">
                                    <?=date ("Y-m-d H:i:s",filemtime('../files/sitemap.xml'))?>
			                    </p>
			                </div>
			                <div id="flot-browser-chart" data-height="270px"></div>
			            </div>
			            <!-- end widget -->
                </div>
                <?php }

                */?>
            </div>
            
			<script>
			$( document).ready(function()
			{
				$('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
			});
			function loadsexport(tipo)
			{			
			}
			function callfile()
			{
				$.ajax({type: 'GET',url: "importa/corre.php",dataType: 'html',cache: false, success: function(id){}});
			}
			function remove2()
			{
				if(confirm('Tem a certeza que quer cancelar a importação, os produtos que já foram atualizados não serão repostos?'))
				{
					$.ajax({type: 'GET',url: "importa/remove.php",dataType: 'html',cache: false, success: function(id){}});
				}
			}
			</script>			
			</div>

		<div class="row">
		<div class="col-md-12">
		<div class="" >
		<div class="p2">
		<?php
		require_once 'encomendas/funcoes.inc.php';
		?>
		<div class="panel PC">
			<div class="panel-heading">
		        <div class="panel-heading-btn">
		            <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-grey" data-click="panel-expand" data-original-title="" title=""><i class="fa fa-expand"></i></a>		            
		        </div>
		        <h4 class="panel-title">ÚLTIMAS ENCOMENDAS</h4>
		    </div>
			<div class="table-responsive">
				<table width="100%" border="0" cellspacing=0 cellpadding=2 class="table table-bordered table-hover table-striped table-td-valign-middle">
				<thead>
					<tr class="inverse">
						<th width="70" height="30" class="text-center" style="background:#FFFFFF">Nº Enc</th>
						<? if($GLOBALS['usar_token_enc']==1){?><th width="70" height="30" class="text-center" style="background:#FFFFFF">Ref</th><? }?>
						<th width="150" class="text-center" style="background:#FFFFFF">Data</th>
						<th width="60" class="text-center" style="background:#FFFFFF">ID Cliente</th>
						<th class="text-center" style="background:#FFFFFF">Nome</th>
						<th width="150" class="text-center" style="background:#FFFFFF">Tipo Pagamento</th>
						<th width="100" class="text-center" style="background:#FFFFFF">Total</th>
						<th width="250" class="text-center" style="background:#FFFFFF">Estado</th>
						<th width="100"  class="text-center" style="background:#FFFFFF">Expedição</th>
						<th width="90" class="text-center" style="background:#FFFFFF">Email Enviado</th>
						<th width="50" class="text-center" style="background:#FFFFFF">PDF</th>
					</tr>
				</thead>
				<tbody>
				<?php
					show_content_list_tasks();
				?>
				</tbody>
				</table>
			</div>
		</div>

		</div>
		</div>
		</div>
		</div>
		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td align="center" valign="middle" class="t1"><input onClick="window.open('encomendas/list.php','_self')" type=button class="btn btn-primary" value=" VER TODAS AS ENCOMENDAS "></td>
		</tr>
		</table>
        <br><br>
	<?php
	}
	else
	{?>
		<div id="content" class="" >

			<!-- begin row -->
            <div class="row">
                <!-- begin col-6 -->
                <div class="col-lg-6">
                    <!-- begin widget -->
                    <div class="widget widget-chart">
                        <!-- begin widget-header -->
                        <div class="widget-header bg-inverse-dark">
                        	<!-- <ul class="widget-header-btn">
								<li><a href="#" class="btn btn-white">1 Nov 2017 - 31 Nov 2017 <i class="fa fa-cog"></i></a></li>
							</ul> -->
							<h4 class="text-white">Visitor Analytics</h4>
                        </div>
                        <!-- end widget-header -->
                        <!-- begin widget-body -->
                        <div class="widget-body">
							<!-- begin chart-placeholder -->
							<ul class="widget-chart-placeholder text-center inline">
								<li><span class="legend-circle bg-primary"></span> Total de Visitas</li>
								<li><span class="legend-circle bg-info"></span> Novas Visitas</li>
								<li><span class="legend-circle bg-inverse"></span> Visitants Retornados</li>
							</ul>
							<!-- end chart-placeholder -->
							<!-- begin visitor-chart -->
							<div id="flot-visitor-chart" class="m-b-15" data-height="230px"></div>
							<!-- end visitor-chart -->
							<!-- begin row -->
                        	<div class="row col-lg-with-border">
                        		<div class="col-lg-12 text-center">
									<div class="m-b-2">Total de Visitas</div>
									<h4 class="m-0"><?
										if($f_data1=="" && $f_data2=="")
										{
											$dias=31;
											$f_data1=date("Y-m-d", strtotime("-$dias day"));
											$f_data2=date("Y-m-d");
										}
										if(!$mes) $mes=date("m");
										if(!$ano) $ano=date("Y");

										$result1=new DataObj;
										$sql="select count(*) as Contador from stats where Data BETWEEN '$f_data1' AND '$f_data2' Group By ip ";
										$result1->db_open($db_link,$DB_name,$sql);
										if(!$result1->EOF())
										{
											$n_acessos=$result1->db_data["Contador"];
										}
										$result1->db_close();
										echo number_format($n_acessos,0,'','.');

										?></h4>
                        		</div>
                        	</div>
                        	<!-- end row -->
                        </div>
                        <!-- end widget-body -->
                    </div>
                    <!-- end widget -->
                </div>
                <!-- end col-6 -->
                <!-- begin col-6 -->
                <div class="col-lg-6">
                    <!-- begin widget -->
                    <div class="widget widget-chart">
                        <!-- begin widget-header -->
                        <div class="widget-header bg-inverse-dark">
							<h4 class="text-white">Server Load <a href="memomonitor/">[mais info]</a></h4>
                        </div>
                        <!-- end widget-header -->
                        <!-- begin widget-body -->
                        <div class="widget-body">
							<!-- begin chart-placeholder -->
							<ul class="widget-chart-placeholder text-center inline">
								<li><span class="legend-circle bg-danger"></span> Overload > 75%</li>
								<li><span class="legend-circle bg-warning"></span> Heavy Load > 50%</li>
								<li><span class="legend-circle bg-lime"></span> Normal < 50%</li>
							</ul>
							<!-- end chart-placeholder -->
							<!-- begin server-chart -->
							<div id="flot-server-chart" class="m-b-15" data-height="230px"></div>
							<!-- end server-chart -->
							<!-- begin row -->
							<div class="row col-lg-with-border">
								<div class="col-lg-6 text-center">
									<div class="m-b-2">Server Usage</div>
									<h4 class="m-0" data-id="server-load-number">0.00%</h4>
								</div>
								<div class="col-lg-6 text-center">
									<div class="m-b-2">Disk Storage</div>
									<h4 class="m-0"><?php
									$bytes = disk_free_space(".");
								    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
								    $base = 1024;
								    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);

								    echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class];


								     ?></h4>
								</div>
							</div>
							<!-- end row -->
                        </div>
                        <!-- end widget-body -->
                    </div>
                    <!-- end widget -->
                </div>
                <!-- end col-6 -->
            </div>
            <!-- end row -->




				<!-- end scroll to top btn -->
			</div><?

	}
	if(isset($dados['Permissoes'][4]) && $dados['Permissoes'][4] == 1)
	{
	?>
		<br>
		<table width=100% border=0 cellspacing=0 class=".table">
		<tr>
		<td id="dashboard_ajax">
			<div class="panel panel-warning overflow-hidden m-b-10">
                <div class="panel-heading">
                    <h4 class="panel-title">MENSAGENS</h4>
                </div>
            </div>
		<table width=100% border=0 cellspacing=0 cellpadding=2>
		<tr>
		<td valign="top" colspan=2 class="agenda2">
		<?
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_id;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;
		$rs2=new DataObj;
		$sql="SELECT * FROM mensagens WHERE (ID_Cliente = '$admin_id' || ID_Admin=0) and folder = '' and was_read=0 order by ID limit 0,5";
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		$cor="#f0f3f5";$class="r1";$n="1";
		$count=0;
		if(!$rs->EOF())
		{
		while(!$rs->EOF())
		{
		$count++;
		if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
		else				{$cor="#FFFFFF";$class="r2";$n="2";}
		$m_nome="";
		$sql="SELECT * FROM logins where ID=".$rs->db_data["ID_From"];
		$hRes=$rs2->db_open($db_link,$DB_name,$sql);
		if(!$rs2->EOF()) $m_nome=$rs2->db_data["Nome"];
		else  $m_nome='website';
		$rs2->db_close();
		if(strlen($m_nome) > 100)$m_nome=substr($m_nome,0,100)."...";
		echo '<table class="'.$class.'" width=100%><tr><td width=29 class="agenda2">&nbsp;</td><td height=20 class="agenda2"><a class=preto href="messages/index.php?frame=viewmsg.php?mail_id='.$rs->db_data["ID"].'">O utilizador <b>'.$m_nome.'</b> enviou-lhe a seguinte mensagem: <b>'.$rs->db_data["Assunto"].'</b></a></td></tr></table>';

		if($rs->db_data["was_read"]==0){ echo '<script>showStickySuccessToast("<b>'.$m_nome.'</b> enviou-lhe uma nova mensagem:<br />'.htmlentities($rs->db_data["Assunto"]).'<br /><a class=branco2 href=\"messages/index.php?frame=viewmsg.php?mail_id='.$rs->db_data["ID"].'\">LER MENSAGEM</a>") </script>';}

		$rs->db_next();
		}
		$rs->db_close();
		}
		else echo '<table  class="'.$class.'" width=100%><tr><td height=100 class="agenda2" align=center>Sem mensagens!</td></tr></table>';?>
		</table>
		</td>
		</tr></table>

		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td align="center" valign="middle" class="t1"><input onClick="window.open('messages/index.php','_self')" type=button class="btn btn-primary" value=" VER TODAS AS MENSAGENS "></td>
		</tr>
		</table>

		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
			<td align="center" valign="middle" class="t1"><input onClick="window.open('../index.php','_self')" type=button class="btn btn-primary" value=" ABRIR SITE "></td>
		</tr>
		</table>
	<br><br><?php
	}
