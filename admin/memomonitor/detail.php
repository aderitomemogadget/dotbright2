<?php

	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(0);

	$core=$_GET['n'];

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<title>Memogadget server performance monitor - CORE <?=$core?></title>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta name="copyright" content="memogadget.com">
<meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="owner" content="memogadget.com 1 ilimited license to '.$site_title.'">
<link rel="stylesheet" type="text/css" href="layout/bootstrap-3.3.7/css/bootstrap.min.css" />
<link rel="stylesheet" type="text/css" href="admin_estilos.css" />
<link href="./pictures/flexigrid/flexigrid.css" rel="stylesheet" type="text/css" />
<link href="./pictures/shadowbox/shadowbox.css" rel="stylesheet" type="text/css" />
<style type="text/css">
.menutitle{cursor:pointer;margin:0px;width:100%;padding:0px}
.subm{margin:0px;padding:0px; position:relative}
</style>
<link rel="stylesheet" type="text/css" href="layout/reveal/jquery.fancybox.css" media="screen" />
<link rel="stylesheet" type="text/css" href="layout/fontawesome-free-5.7.2-web/css/all.css" media="screen" />
<script defer src="layout/fontawesome-free-5.7.2-web/js/all.js"></script>
<!-- ================== BEGIN PAGE LEVEL CSS STYLE ================== -->
<link rel="stylesheet" type="text/css" href="layout/theme/plugins/bootstrap/bootstrap-4.1.1/css/bootstrap-grid.css" media="screen" />
<link rel="stylesheet" type="text/css" href="layout/theme/plugins/bootstrap-calendar/css/bootstrap_calendar.css" media="screen" />
<link rel="stylesheet" type="text/css" href="layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.css" media="screen" />
<!-- ================== BEGIN BASE JS ================== -->
<script type="text/javascript" src="layout/jquery-2.2.4.min.js"></script>
<script type="text/javascript" src="layout/jquery.cookie.js"></script>
<script type="text/javascript" src="layout/bootstrap-3.3.7/js/bootstrap.js"></script>
<link rel="stylesheet" href="layout/jquery-ui-1.10.3.css" />
<link rel="stylesheet" type="text/css" href="layout/datepicker/jquery-ui-timepicker-addon.css" media="screen" />
<script type="text/javascript" src="layout/datepicker/jquery-1.8.2.js"></script>
<script type="text/javascript" src="layout/jquery-ui-1.10.3.js"></script>
<script type="text/javascript" src="layout/datepicker/jquery-ui-timepicker-addon.js"></script>
<script type="text/javascript" src="layout/datepicker/jquery-ui-sliderAccess.js"></script>
<script type="text/javascript" src="layout/datepicker/localization/jquery-ui-timepicker-en.js"></script>
<script type="text/javascript" src="layout/jquery.easing.1.3.js"></script>

<script language="JavaScript" src="layout/theme/plugins/pace/pace.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/slimscroll/jquery.slimscroll.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/bootstrap-calendar/js/bootstrap_calendar.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/sparkline/jquery.sparkline.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.time.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.resize.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.pie.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.stack.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.crosshair.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/jquery.flot.categories.js"></script>
<script language="JavaScript" src="layout/theme/plugins/flot/CurvedLines/curvedLines.js"></script>
<script language="JavaScript" src="layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script language="JavaScript" src="layout/theme/plugins/jquery-jvectormap/jquery-jvectormap-world-merc-en.js"></script>
<script language="JavaScript" src="layout/theme/plugins/chart-js/Chart.js"></script>
<script language="JavaScript" src="layout/theme/plugins/gritter/js/jquery.gritter.js"></script>


<script language="JavaScript" src="layout/theme/js/widgets.js"></script>
<script language="JavaScript" src="layout/theme/js/apps.js"></script>

<link rel="stylesheet" href="layout/theme/css/animate.min.css" />
<link rel="stylesheet" href="layout/theme/css/style.min.css" />

<link rel="stylesheet" href="layout/theme/css/style.css" />
<link rel="stylesheet" href="layout/theme/css/style-responsive.min.css" />

<link type="text/css" href="dashboard.css" rel="stylesheet">

<div class="content">
	<div class="row">
		<?

	// Script for getting statistics information from server
	// To view the content in json _GET parameter "show" should be specified
	// To view the content in pretty format _GET parameter/value "show=pretty" should be specified
	// Gets individual core information
	function GetCoreInformation()
	{
	    $data = file('/proc/stat');
	    $cores = array();
	    foreach( $data as $line )
	    {
	        if( preg_match('/^cpu[0-9]/', $line) )
	        {
	            $info = explode(' ', $line );
	            $cores[] = array(
	                'user' => $info[1],
	                'nice' => $info[2],
	                'sys' => $info[3],
	                'idle' => $info[4]
	            );
	        }
	    }
    	return $cores;
	}
	// compares two information snapshots and returns the cpu percentage
	function GetCpuPercentages($stat1, $stat2)
	{
	    if( count($stat1) !== count($stat2) ) {
	        return;
	    }
    	$cpus = array();
    	for( $i = 0, $l = count($stat1); $i < $l; $i++) {
	        $dif = array();
	        $dif['user'] = $stat2[$i]['user'] - $stat1[$i]['user'];
	        $dif['nice'] = $stat2[$i]['nice'] - $stat1[$i]['nice'];
	        $dif['sys'] = $stat2[$i]['sys'] - $stat1[$i]['sys'];
	        $dif['idle'] = $stat2[$i]['idle'] - $stat1[$i]['idle'];
	        $total = array_sum($dif);
	        $cpu = array();
	        foreach($dif as $x=>$y) $cpu[$x] = round($y / $total * 100, 1);
	        $cpus['cpu' . $i] = $cpu;
	    }
	    return $cpus;
	}
	// get core information (snapshot)
	$stat1 = GetCoreInformation();
	// sleep on server for one second
	sleep(1);
	// take second snapshot
	$stat2 = GetCoreInformation();
	// get the cpu percentage based off two snapshots
	$data = GetCpuPercentages($stat1, $stat2);
	// makes a google image chart url
	function makeImageUrl($title, $data)
	{
	    $url = 'http://chart.apis.google.com/chart?chs=440x240&cht=pc&chco=0062FF|498049|F2CAEC|D7D784&chd=t:';
	    $url .= $data['user'] . ',';
	    $url .= $data['nice'] . ',';
	    $url .= $data['sys'] . ',';
	    $url .= $data['idle'];
	    $url .= '&chdl=User|Nice|Sys|Idle&chdlp=b&chl=';
	    $url .= $data['user'] . '%25|';
	    $url .= $data['nice'] . '%25|';
	    $url .= $data['sys'] . '%25|';
	    $url .= $data['idle'] . '%25';
	    $url .= '&chtt=Core+' . $title;
	    return $url;
	}
	//if ($_GET['show'] == 'pretty') {
    // ouput pretty images
    foreach( $data as $k => $v )
    {
        echo '<img src="' . makeImageUrl( $k, $v ) . '" />';
    }

	    echo '<BR><BR>';
	?>


</div>

</div>
</html>