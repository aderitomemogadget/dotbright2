<?php

	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Memogadget server performance monitor</title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="memogadget.com">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="owner" content="memogadget.com 1 ilimited license to '.$site_title.'">
<link rel="stylesheet" type="text/css" href="../layout/bootstrap-3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="../admin_estilos.css" />
<link href="./pictures/flexigrid/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="./pictures/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px}
.subm{margin:0px;padding:0px; position:relative}
</style>



<link rel="stylesheet" type="text/css" href="../layout/reveal/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../layout/fontawesome-free-5.7.2-web/css/all.css" media="screen" />


<script defer src="../layout/fontawesome-free-5.7.2-web/js/all.js"></script>


<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->

<link rel="stylesheet" type="text/css" href="../layout/theme/plugins/bootstrap/bootstrap-4.1.1/css/bootstrap-grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../layout/theme/plugins/bootstrap-calendar/css/bootstrap_calendar.css" media="screen" />
<link rel="stylesheet" type="text/css" href="../layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.css" media="screen" />

<!-- ================== BEGIN BASE JS ================== -->

<script type="text/javascript" src="../layout/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="../layout/jquery.cookie.js"></script>

<script type="text/javascript" src="../layout/bootstrap-3.3.7/js/bootstrap.js"></script>

<link rel="stylesheet" href="../layout/jquery-ui-1.10.3.css" />
<link rel="stylesheet" type="text/css" href="../layout/datepicker/jquery-ui-timepicker-addon.css" media="screen" />
<script type="text/javascript" src="../layout/datepicker/jquery-1.8.2.js"></script>
<script type="text/javascript" src="../layout/jquery-ui-1.10.3.js"></script>
<script type="text/javascript" src="../layout/datepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="../layout/datepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="../layout/datepicker/localization/jquery-ui-timepicker-en.js"></script>
<script type="text/javascript" src="../layout/jquery.easing.1.3.js"></script>

<script language="JavaScript" src="../layout/theme/plugins/pace/pace.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/sparkline/jquery.sparkline.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.time.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.resize.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.pie.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.stack.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.crosshair.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/jquery.flot.categories.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/flot/CurvedLines/curvedLines.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-world-merc-en.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/chart-js/Chart.js"></script>
<script language="JavaScript" src="../layout/theme/plugins/gritter/js/jquery.gritter.js"></script>

<script language="JavaScript" src="layout/theme/js/widgets.js"></script>
<script language="JavaScript" src="layout/theme/js/apps.js"></script>

<link rel="stylesheet" href="../layout/theme/css/animate.min.css" />
<link rel="stylesheet" href="../layout/theme/css/style.min.css" />

<link rel="stylesheet" href="../layout/theme/css/style.css" />
<link rel="stylesheet" href="../layout/theme/css/style-responsive.min.css" />

<script>jQuery(function(){$('table').each(function() {
var cp = parseInt($( this ).attr('cellpadding'));
if(cp > 0){
$(this).children('tbody').children('tr').children('td').css('padding',cp+'px');
}
if($( this ).attr('bgcolor') !== ''){
$(this).css('background-color',$( this ).attr('bgcolor'));
}
});});

	$(document).ready(function()
	{
		App.init();
		AdminHome.init();
	});
</script>
<?php

//LINUX
//PROCESSADOR
$cpu_cores = shell_exec('cat /proc/cpuinfo | grep processor');
$cpu_ncores = explode("\n", $cpu_cores);
$cpu_ncores = sizeof($cpu_ncores) - 1;
$cpu_modelo = shell_exec('cat /proc/cpuinfo | grep "model name"');
$cpu_modelo = explode(':', $cpu_modelo);
$cpu_speed = shell_exec('cat /proc/cpuinfo | grep "cpu MHz"');
$cpu_speed = explode(':', $cpu_speed);
$cpu_speed = shell_exec('cat /proc/cpuinfo | grep "cpu MHz"');
$cpu_speed = explode(':', $cpu_speed);
$cpu_flags = shell_exec('cat /proc/cpuinfo | grep "flags"');
$cpu_flags = explode(':', $cpu_flags);
$cpu_x64 = strpos($cpu_flags['1'],'ln');
$cpu_hyper_thred = strpos($cpu_flags['1'],'ht');
$cpu_load_average = shell_exec('procinfo | grep "Load average"');
$cpu_load_average = explode('Load average:', $cpu_load_average);
$cpu_load_average = explode(' ', $cpu_load_average['1']);
//MEMÓRIA
$mem_mem = shell_exec('procinfo | grep RAM:');
while (strpos($mem_mem,'  ') !== false)
{
  $mem_mem = str_replace('  ',' ',$mem_mem);
}
$mem_detalhes = explode(" ", $mem_mem);
//DISCO
$disco_disco = shell_exec('df --total | grep total');
while (strpos($disco_disco,'  ') !== false)
{
  $disco_disco = str_replace('  ',' ',$disco_disco);
}
$disco_total = explode(" ", $disco_disco);
//SISTEMA
$sis_operativo = shell_exec('cat /etc/issue');
$sis_operativo = str_replace('\n','',$sis_operativo);
$sis_operativo = str_replace('\l','',$sis_operativo);
$sis_mem_virt = shell_exec('procinfo | grep Swap:');
while (strpos($sis_mem_virt,'  ') !== false)
{
  $sis_mem_virt = str_replace('  ',' ',$sis_mem_virt);
}
$sis_mem_virt = explode(" ", $sis_mem_virt);
$sis_processos = shell_exec('top -b -n1 | grep Task');
while (strpos($sis_processos,'  ') !== false)
{
  $sis_processos = str_replace('  ',' ',$sis_processos);
}
$sis_processos = explode(" ", $sis_processos);
$sis_last_boot = shell_exec('who -b');
preg_match('/[a-zA-Z]{3} [0-9]+ [0-9]+:[0-9]+/', $sis_last_boot,$sis_last_boot);
$sis_last_boot = explode(" ", $sis_last_boot['0']);

?>

<link type="text/css" href="dashboard.css" rel="stylesheet">
<link type="text/css" href="notify.css" rel="stylesheet">
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
	<div class="content">
		<!-- begin row -->
		<div class="row">
		<!-- begin col-6 -->
		<?
		$i=1;
		$total_cores=shapeSpace_system_cores();

		while($i<=$total_cores)
		{
		?>
	        <div class="col-lg-6">
	            <!-- begin widget -->
	            <div class="widget widget-chart">
	                <!-- begin widget-header -->
	                <div class="widget-header bg-inverse-dark">
						<h4 class="text-white"><a href="detail.php?n=<?=$i?>">Core <?=$i?> Load</a></h4>
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
						<div id="flot-server-chart<?=$i?>" class="m-b-15" data-height="230px"></div>
						<div class="row col-lg-with-border">
							<div class="col-lg-12 text-center">
								<div class="m-b-2">Server Usage</div>
								<h4 class="m-0" data-id="server-load-number<?=$i?>">0.00%</h4>
							</div>
						</div>
						<!-- end row -->
	                </div>
	                <!-- end widget-body -->
	            </div>
	            <!-- end widget -->
	        </div>
		<?
		$i++;
		}?>
        <!-- end col-6 -->

		<!-- begin col-6 -->
        <div class="col-lg-6">
			<!-- begin widget -->
	        <div class="widget">
	            <div class="widget-header bg-inverse">
	                <h4 class="text-white">Server Info</h4>
	                <?php

						function sys_getloadavg_hack()
						{
						    $str = substr(strrchr(shell_exec("uptime"),":"),1);
						    $avs = array_map("trim",explode(",",$str));
						    return $avs;
						}

						$free = ((disk_free_space('/')));
						$total = ((disk_total_space('/')));

						$disk_percent=$free*100/$total;
						$disk_percent=number_format($disk_percent,2,".","");

						function shapeSpace_system_cores()
						{
						    $cmd = "uname";
						    $OS = strtolower(trim(shell_exec($cmd)));

						    switch($OS) {
						       case('linux'):
						          $cmd = "cat /proc/cpuinfo | grep processor | wc -l";
						          break;
						       case('freebsd'):
						          $cmd = "sysctl -a | grep 'hw.ncpu' | cut -d ':' -f2";
						          break;
						       default:
						          unset($cmd);
						    }

						    if ($cmd != '') {
						       $cpuCoreNo = intval(trim(shell_exec($cmd)));
						    }

						    return empty($cpuCoreNo) ? 1 : $cpuCoreNo;

						}

	                    ?>
	            </div>
	            <ul class="widget-task-list">
	                <li>
	                    <h4>Free Disk Storage</h4>
	                    <?php
							$bytes = disk_free_space(".");
						    $si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
						    $base = 1024;
						    $class = min((int)log($bytes , $base) , count($si_prefix) - 1);
						    echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class];
						    ?>
	                </li>
	                <li>
	                    <h4>NUMBER OF RUNNING PROCESS</h4>
	                    <?=$sis_processos['1']; ?>
	                </li>
	                <li>
	                    <h4>DISK SPEED IO: 159 MB/s</h4>
	                    How to know how fast your VPS is?
						My general rule is: anything over 50 MB/s is considered acceptable. But if you bought a SSD-based VPS and it performs under 100MB/s, then that is unacceptable.

						<?
							//$cmd='dd if=/dev/zero of=test bs=64k count=16k conv=fdatasync';
							//no 202 =  102 MB/s
							//no yr =  159 MB/s

						?>
	                </li>
	            </ul>
	        </div>
	        <!-- end widget -->
		</div>
        <!-- end col-6 -->
		<!-- begin col-6 -->
        <div class="col-lg-12"><?

		$path = '../../core/';
		require_once $path . 'core_files.php';
		global $DB_host,$DB_user,$DB_pass,$DB_name,$site_http_url,$site_lang;

        $host=$DB_host;
        $user=$DB_user;
        $pass=$DB_pass;

		$link = mysql_connect($host,$user,$pass);
		if (!$link) {
		    die('Could not connect: ' . mysql_error());
		}
		$ver=mysql_get_server_info();
		$mysql_status = explode('  ', mysql_stat($link));
        ?>

        				<!-- begin widget -->
			            <div class="widget p-0">
			                <div class="row row-space-0">
			                    <div class="col-lg-12">
			                        <h5 class="p-l-20 p-t-3">Mysql</h5>
			                        <div class="table-responsive m-b-0 f-s-10">
                                        <table class="table table-bordered table-hover table-last-row-no-border-bottom m-b-0">
                                            <!-- <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Country</th>
                                                    <th class="text-nowrap">Total (millions)</th>
                                                </tr>
                                            </thead> -->
                                            <tbody>
                                                <tr>
                                                    <td>1</td>
                                                    <td>MySQL server version:</td>
                                                    <td><?=$ver?></td>
                                                </tr><?
                                                $i=0;
                                                while($i<count($mysql_status))
                                                {
                                                	$dados=explode(':',$mysql_status[$i]);
                                                	?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$dados[0]?>:</td>
	                                                    <td><?=$dados[1]?></td>
                                                	</tr><?
                                                	$i++;
                                                }?>
                                                <tr>
                                                    <td colspan="3"><b>RESUMO:</b></td>
                                            	</tr><?

												$result = mysql_query("show variables like '%open%'", $link);
												while ($row = mysql_fetch_assoc($result))
												{
													$var_name=$row['Variable_name'];

													?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$var_name?>:</td>
	                                                    <td><?=$row['Value']?></td>
                                                	</tr><?
												    $i++;
												}

                                                ?>
												<tr>
                                                    <td colspan="3">&nbsp;</td>
                                            	</tr><?


												$result = mysql_query("show global status like 'open_%'", $link);
												while ($row = mysql_fetch_assoc($result))
												{
													$var_name=$row['Variable_name'];

													?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$var_name?>:</td>
	                                                    <td><?=$row['Value']?></td>
                                                	</tr><?
												    $i++;
												}?>
												<tr>
                                                    <td colspan="3">&nbsp;</td>
                                            	</tr>
                                            	<tr>
                                                    <td colspan="3"><a href="https://dev.mysql.com/doc/refman/8.0/en/server-system-variables.html" target="_new"><b>LIMITES:</b><a></b></td>
                                            	</tr><?
												$result = mysql_query("show variables like '%limit%'", $link);
												while ($row = mysql_fetch_assoc($result))
												{
													$var_name=$row['Variable_name'];


													$var_name=str_replace('delayed_insert_limit','delayed_insert_limit <br>(number of seconds that the mysqld server waits for a connect packet before responding with Bad handshake. The default value is 10 seconds.)',$var_name);
													$var_name=str_replace('ft_query_expansion_limit','ft_query_expansion_limit <br>(The number of top matches to use for full-text searches performed using WITH QUERY EXPANSION)',$var_name);
													$var_name=str_replace('open_files_limit','open_files_limit <br>(You should try increasing the value of this variable if mysqld gives you the error Too many open files)',$var_name);





													?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$var_name?>:</td>
	                                                    <td><?=$row['Value']?></td>
                                                	</tr><?
												    $i++;
												}

												$result = mysql_query("show variables like '%timeout%'", $link);
												while ($row = mysql_fetch_assoc($result))
												{
													$var_name=$row['Variable_name'];

													?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$var_name?>:</td>
	                                                    <td><?=$row['Value']?></td>
                                                	</tr><?
												    $i++;
												}

                                                ?>
                                                <tr>
                                                    <td colspan="3">&nbsp;</td>
                                            	</tr>
                                            	<tr>
                                                    <td colspan="3"><b>OUTRAS VARIAVEIS:</b></td>
                                            	</tr><?


												$result = mysql_query('SHOW STATUS', $link);
												while ($row = mysql_fetch_assoc($result))
												{
													$var_name=$row['Variable_name'];
													$var_name=str_replace('Opened_files','Opened_files (how many times you have opened a table since the last time you restarted mysqld)',$var_name);
													$var_name=str_replace('Open_files','<b>Open_files</b>',$var_name);

													?>
                                                	<tr>
	                                                    <td><?=$i+1?></td>
	                                                    <td><?=$var_name?>:</td>
	                                                    <td><?=$row['Value']?></td>
                                                	</tr><?
												    $i++;
												}
												?>


                                            </tbody>
                                        </table>
			                        </div>
			                    </div>
			                </div>
			            </div>
			            <!-- end widget -->
		</div>

    </div>
    <!-- end row -->



    <?php



// $df contains the number of bytes available on "/"
/*$df = round(disk_free_space("/var/www") / 1024 / 1024 / 1024);
print("Free space: $df GB");
*/

/*$bytes = disk_free_space(".");
$si_prefix = array( 'B', 'KB', 'MB', 'GB', 'TB', 'EB', 'ZB', 'YB' );
$base = 1024;
$class = min((int)log($bytes , $base) , count($si_prefix) - 1);
echo $bytes . '<br />';
echo sprintf('%1.2f' , $bytes / pow($base,$class)) . ' ' . $si_prefix[$class] . '<br />';
*/



$MODULO['nome'] 			= 'Servidor';
$MODULO['descricao'] 	= 'Informações do servidor e estado do sistema.';

$MODULO['titulo'] 		= 'Servidor &amp; Sistema';
$MODULO['detalhes']		= 'Informações do servidor e estado do sistema.';




//$sis_last_boot['0'] = mesportugues($sis_last_boot['0']);


?>

<div class="painelscroll" style="height:386px;">
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td width="55" valign="top"><img src="./pictures/cpu.png" width="48" alt="CPU" /></td>
				<td>
					<b>Processadores:</b><br />
						<br />
						<?= trim($cpu_modelo['1']); ?> @ <?=$cpu_speed['1']; ?>Mhz (<?=$cpu_load_average['1']; ?>%)<br />
        <small>Endereçamento Actual</small> <? if($cpu_x64 = 1)  echo "64"; else echo "32"; ?>bit<br />
        <small>Nº de Núcleos</small> <?=$cpu_ncores; ?> | <small>Núcleos Lógicos</small> <? if($cpu_hyper_thred == "")  echo $cpu_ncores; else echo $cpu_ncores*2; ?><br />
        <div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" style="height:15px;">
							 <div style="width: <?=$cpu_load_average['1']; ?>%;" class="ui-progressbar-value ui-widget-header ui-corner-left"></div>
						</div>
				</td>
			</tr>
		</table>
	<!-- <hr />
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td width="55" valign="top"><img src="./pictures/memory.png?w=48&amp;h=48" alt="RAM" /></td>
				<td>
					<b>Memória:</b><br />
						<small>Disponível</small> <?= round($mem_detalhes['3'] / 1048576, 2); ?>GB | <small>Capacidade</small> <?= round($mem_detalhes['1'] / 1048576, 2); ?>GB<br />
						<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" style="height:15px;">
							 <div style="width: <?= round(($mem_detalhes['1'] - $mem_detalhes['3']) * 100 / $mem_detalhes['1'], 0); ?>%;" class="ui-progressbar-value ui-widget-header ui-corner-left"></div>
						</div>
				</td>
			</tr>
			</tr>
		</table> -->
<hr />
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td width="55" valign="top"><img src="./pictures/harddisk.png" width="48" alt="HDD" /></td>
				<td>
					<b>Discos Rígidos:</b><br />
						<br />
						<?= trim($obj->Name); ?> <small>Disponível</small> <?= round($disco_total['3'] / 1048576, 2); ?>GB | <small>Capacidade</small> <?= round(($disco_total['2'] + $disco_total['3']) / 1048576, 2); ?>GB<br />
						<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" style="height:15px;">
							 <div style="width: <?= round((($disco_total['2'] + $disco_total['3']) - $disco_total['3']) * 100 / ($disco_total['2'] + $disco_total['3']), 0); ?>%;" class="ui-progressbar-value ui-widget-header ui-corner-left"></div>
						</div>
				</td>
			</tr>
		</table>
<hr />
		<table width="100%" border="0" cellspacing="5" cellpadding="0">
			<tr>
				<td width="55" valign="top"><img src="./pictures/ubuntu.png" width="48" alt="HDD" /></td>
				<td>
					<b>Sistema:</b><br />
						<?=$sis_operativo; ?> <small><?=$obj->CSDVersion; ?></small><br /><br />
						<b>Memória Virtual:</b><br />
						<small>Disponível</small> <?= round($sis_mem_virt['3'] / 1048576, 2); ?>GB | <small>Capacidade</small> <?= round($sis_mem_virt['1'] / 1048576, 2); ?>GB<br />
						<div class="ui-progressbar ui-widget ui-widget-content ui-corner-all" style="height:15px;">
							 <div style="width: <?= round(($sis_mem_virt['1'] - $sis_mem_virt['3']) * 100 / $sis_mem_virt['1'], 0); ?>%;" class="ui-progressbar-value ui-widget-header ui-corner-left"></div>
						</div>
						<br />
						<b>Outras Informações:</b><br />
						<small>Processos</small> <?=$sis_processos['1']; ?> | <small>Último Boot</small> <?=$sis_last_boot['1'].' de '.$sis_last_boot['0'].' pelas '.$sis_last_boot['2'].' horas'; ?><br />
						<small>Arquitectura</small> <? if($cpu_x64 == "") echo "32-bit"; else echo "64-bit"; ?>
				</td>
			</tr>
		</table>
</div>

</div>
</html>