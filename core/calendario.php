<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

if($site_lang=='pt')      $monthName=explode(",",'Janeiro,Fevereiro,Mar&ccedil;o,Abril,Maio,Junho,Julho,Agosto,Setembro,Outubro,Novembro,Dezembro');
else if($site_lang=='es') $monthName=explode(",",'Enero,Febrero,Marzo,April,Mayo,Junio,Julio,Agosto,Septiembre,Octubre,Noviembre,Diciembre');
else if($site_lang=='en') $monthName=explode(",",'January,February,March,April,May,June,July,August,September,October,November,December');
else if($site_lang=='de') $monthName=explode(",",'Januar,Februar,März,April,Mai,Juni,Juli,August,September,Oktober,November,Dezember');
else if($site_lang=='fr') $monthName=explode(",",'Janvier,Février,Mars,Avril,Mai,Juin,Juillet,Août,Septembre,Octobre,Novembre,Décembre');
else if($site_lang=='it') $monthName=explode(",",'Gennaio,Febbraio,Marzo,Aprile,Maggio,Giugno,Luglio,Agosto,Settembre,Ottobre,Novembre,Dicembre');

if($site_lang=='pt')      $semana=explode(",",'Seg,Ter,Qua,Qui,Sex,Sab,Dom');
else if($site_lang=='es') $semana=explode(",",'Seg,Ter,Qua,Qui,Sex,Sab,Dom');
else if($site_lang=='en') $semana=explode(",",'Mon,Tue,Wed,Thu,Fri,Sat,Sun');
else if($site_lang=='de') $semana=explode(",",'Seg,Ter,Qua,Qui,Sex,Sab,Dom');
else if($site_lang=='fr') $semana=explode(",",'Seg,Ter,Qua,Qui,Sex,Sab,Dom');
else if($site_lang=='it') $semana=explode(",",'Seg,Ter,Qua,Qui,Sex,Sab,Dom');

if($site_lang=='pt')      $days=explode(",","S,T,Q,Q,S,S,D");
else if($site_lang=='es') $days=explode(",","M,T,Q,Q,S,S,D");
else if($site_lang=='en') $days=explode(",","M,T,W,T,F,S,S");
else if($site_lang=='de') $days=explode(",","S,T,Q,Q,S,S,D");
else if($site_lang=='fr') $days=explode(",","S,T,Q,Q,S,S,D");
else if($site_lang=='it') $days=explode(",","S,T,Q,Q,S,S,D");

function strtolower_cal($str)
{
	$str=ereg_replace("á","Á",$str); $str=ereg_replace("à","À",$str); $str=ereg_replace("â","Â",$str); $str=ereg_replace("ä","Ä",$str); $str=ereg_replace("ã","Ã",$str);
	$str=ereg_replace("é","É",$str); $str=ereg_replace("è","È",$str); $str=ereg_replace("ê","Ê",$str); $str=ereg_replace("ë","Ë",$str);
	$str=ereg_replace("í","Í",$str); $str=ereg_replace("ì","Ì",$str); $str=ereg_replace("î","Î",$str); $str=ereg_replace("ï","Ï",$str);
	$str=ereg_replace("ó","Ó",$str); $str=ereg_replace("ò","Ò",$str); $str=ereg_replace("ô","Ô",$str); $str=ereg_replace("ö","Ö",$str); $str=ereg_replace("õ","Õ",$str);
	$str=ereg_replace("ú","Ú",$str); $str=ereg_replace("ù","Ù",$str); $str=ereg_replace("û","Û",$str); $str=ereg_replace("ü","Ü",$str);
	$str=ereg_replace("ç","Ç",$str);
	return strtoupper(ltrim(rtrim($str)));
}

function calendar($year='',$month='',$day='',$tp='',$url='')
{
	if($tp=='agenda') $caminho=seo('pages.php?page_id='.$GLOBALS['agenda_page_id']);
	elseif($tp=='pics') $caminho=seo('pages.php?page_id='.$GLOBALS['pictures_page_id']);
	if($year) $year=mysql_escape_string(htmlentities_memo(strip_tags($year)));
	if($month) $month=mysql_escape_string(htmlentities_memo(strip_tags($month)));
	if($day) $day=mysql_escape_string(htmlentities_memo(strip_tags($day)));
	if($month=="") $month=date("n");
	if($year=="")  $year=date("Y");
	if(isset($_GET['m'])) $month=mysql_escape_string(htmlentities_memo(strip_tags($_GET['m'])));
	if(isset($_GET['y'])) $year=mysql_escape_string(htmlentities_memo(strip_tags($_GET['y'])));
	$last_year=$year;
	$last_month=$month;
	$last_month--;
	if ($last_month=="0")
	{
		$last_year--;
		$last_month="12";
	}
	$next_year=$year;
	$next_month=$month;
	$next_month++;
	if ($next_month=="13")
	{
		$next_year++;
		$next_month='1';
	}
	$timestamp=mktime (0, 0, 0, $month, 1, $year);
	$time=date("H:i:s");
	$monthname=date("F", $timestamp);
	$output='<table border="0" width="100%" cellpadding="0" cellspacing="0"><tr><td width="50" align="center">';
	$output.='<span style="padding-top:12px;padding-bottom:12px;display:block;text-align:center"><a href="index.php?y='.$last_year.'&m='.$last_month.'&url='.$url.'" class="calendar_navigation'.$small.'"><b><img src="esq.png" /></b></a></span>';
	$output.='</td><td align="center"><span style="padding-top:12px;padding-bottom:12px;display:block"><b>'.$monthname.' '.$year.'</span></td><td width="50" align="center">';
	$output.='<span style="padding-top:12px;padding-bottom:12px;display:block"><a href="index.php?y='.$next_year.'&m='.$next_month.'&url='.$url.'" class="calendar_navigation'.$small.'"><b><img src="dir.png" /></b></a></span>';
	$output.='</td></tr></table>';
	$output.='<table border="0"  width="98%" cellpadding="0" cellspacing="0" align="center" style="border:#000 0px solid;margin-left:3px">';
	$monthstart=date("w", $timestamp);
	$lastday=date("d", mktime (0, 0, 0, $month + 1, 0, $year));
	if(($month-1) == 0) $last_month=12;
	else $last_month=($month-1);
	$date=mktime(0,0,0,$month-1,days_in_month($last_month, $year),$year);
	$last_day_r=explode('-', date('Y-m-d', $date));
	$last_day2=$last_day_r[2];
	$firstday=date('w',mktime(0,0,0,$month,1,$year));
	for($a=($firstday-1);$a>=0;$a--){$arr_last[]=($last_day2-$a);}
	$startdate=-$monthstart;
	$numrows=6;
	$startdate3=0;
	for ($k=1; $k <= $numrows; $k++)
	{
		$output.="<tr>";
		$startdate2=0;
		for ($i=0; $i < 7; $i++)
		{
			$startdate++;
			if (($startdate <= 0) || ($startdate > $lastday))
			{
				if ($startdate <= 0)
				{
					$output.='<td align="center"  class="cinza">';
					$output.='<div class="margin">'.$arr_last[$startdate2].'</div>';
					$output.='</td>';
					$startdate2++;
				}
				elseif($startdate > $lastday)
				{
					$startdate3++;

					$output.='<td align="center"  class="cinza">';
					$output.='<div class="margin">'.$startdate3.'</div>';
					$output.='</td>';
				}
			}
			else
			{
				if (strlen($month) == "1") $fmonth="0".$month;
				else $fmonth=$month;
				if (strlen($startdate) == "1") $fstartdate="0".$startdate;
				else $fstartdate=$startdate;
				$lookup_date=$year."-".$fmonth."-".$fstartdate;
				if ($startdate == date("j") && $month == date("n") && $year == date("Y"))
				{
					$output.='<td width="34" height="34" width="'.$width.'" style="cursor:pointer" valign="top" align="center" onclick="window.open(\''.$caminho.'?dia='.$fstartdate.'&mes='.$fmonth.'&ano='.$year.'\',\'_top\')" class="dia_atual">';
					$output.='<table width="34" height="34" cellpadding="0" cellspacing="0" border="0"><tr><td align="center">';
					$output.='<div class="margin"><b>'.$startdate.'</b></div>';
					$output.='</td></tr></table></td>';
				}
				else
				{
					if($startdate == $day) $class='background:#f0eded';
					else $class='';
					$output.='<td width="34" height="34" width="'.$width.'" style="cursor:pointer;'.$class.'" valign="top" align="center" onclick="window.open(\''.$caminho.'?dia='.$fstartdate.'&mes='.$fmonth.'&ano='.$year.'\',\'_top\')">';
					$output.='<table width="34" height="34" cellpadding="0" cellspacing="0" border="0"><tr><td align="center">';
					$output.='<div class="margin"><b>'.$startdate.'</b></div>';
					$output.='</td></tr></table></td>';
				}
			}
		}
		$output.="</tr>";
	}
	$output.="</table>";
	return $output;
}
function days_in_month($month, $year){return $month == 2 ? ($year % 4 ? 28 : ($year % 100 ? 29 : ($year % 400 ? 28 : 29))) : (($month - 1) % 7 % 2 ? 30 : 31);}
function calendario2($selectedDay,$selectedMonth,$selectedYear,$t_acontecimentos,$Pos,$cal_pag, $class="branco")
{
	global $site_lang, $monthName, $days;
	if(!$selectedDay) $selectedDay=date('d');
	if(!$selectedMonth) $selectedMonth=date('m');
	if(!$selectedYear) $selectedYear=date('Y');
	$Day=date('d');
	$Month=date('m');
	$Year=date('Y');
	echo $selectedYear;
	$firstday=date('w',mktime(0,0,0,$selectedMonth,1,$selectedYear));
	$lastday=31;
	do
	{
		$monthOrig=date('m',mktime(0,0,0,$selectedMonth,1,$selectedYear));
		$monthTest=date('m',mktime(0,0,0,$selectedMonth,$lastday,$selectedYear));
		if ($monthTest != $monthOrig) { $lastday -= 1; }
	}
	while ($monthTest != $monthOrig);
	$dayRow=0;
	$selectedMonth2=intval($selectedMonth);
	if ($selectedMonth < 10) $selectedMonth= '0'.$selectedMonth;
	$caminho=$GLOBALS['site_http_url'].'pages.php?page_id=145';
	if(($selectedMonth-1) == 0) $last_month=12;
	else $last_month=($selectedMonth-1);
	if(!$cal_pag) $cal_pag=1;
	$cal_pag=$cal_pag < 10 ?  $cal_pag='0'.$cal_pag : $cal_pag;
	$date=mktime(0,0,0,$selectedMonth-1,days_in_month($last_month, $selectedYear),$selectedYear);
	$last_day_r=explode('-', date('Y-m-d', $date));
	$last_day=$last_day_r[2];
	echo '
	<table width="100%" height="266" border="0" cellspacing="0" cellpadding="0">
	<tr><td height="42" align="center" valign="middle" class="top">'.$monthName[$selectedMonth-1].' '.$selectedYear.'</td></tr>
	<tr>
		<td align="center">
			<table width="214px" height="214px" border="0" cellpadding="0" cellspacing="0" style="border:#D8D8BF 0px solid">
			<tr>';

	// Não apagar estas linhas podem ser necessárias para mostrar dias da semana
	// for($i=0; $i<=6; $i++) echo"<td align=\"center\" width=\"34\" height=\"34\" class=\"preto\"><B>$days[$i]</B></td>";
	// echo "</tr><tr>";

	// Os dias do ultimo mes
	for($a=($firstday-1);$a>=0;$a--)
	{
		$arr_last[]=($last_day-$a);
	}
	$t_l=0;
	while($dayRow < $firstday)
	{
		echo '<td align="center" width="34" bgcolor="#ffffff" class="cinza"><div class="margin">'.$arr_last[$t_l].'</div></td>';
		$dayRow++;
		$t_l++;
	}
	$day=0;
	if($frametarget) $targetString='target='.$frametarget;
	else $targetString='';
	if($dayRow==0) $row=-1;
	else $row=0;
	while($day < $lastday)
	{
		$dia=$day +1;
		if ($dia < 10) $dia= '0'.$dia;
		if(($dayRow % 7) == 0)
		{
			echo '</tr><tr>';
			$row++;
		}
		$adjusted_day=$day+1;
		$data_c=$Year .'-'. $selectedMonth .'-'. $dia; //Data Completa
		if($t_acontecimentos[$data_c][0]==$data_c)
		{
			//EVENTOS
			echo '<td align="center" width="34" height="34" bgcolor="#'.$t_acontecimentos[$data_c][1].'"><div class="margin">';
			if(!($selectedDay == $adjusted_day && $selectedMonth == $Month && $selectedYear == $Year)) echo '<a class=branco href="'.$caminho.'&dia='.$dia.'&mes='.$selectedMonth.'&ano='.$Year.'&cal_pag='.$selectedMonth.'" target="_top">'.$adjusted_day.'</a>';
			else echo '<a class=branco href="'.$caminho.'&dia='.$dia.'&mes='.$selectedMonth.'&ano='.$Year.'&cal_pag='.$selectedMonth.'" target="_top"><b>'.$adjusted_day.'<b></a>';
			echo '</div></td>';
		}
		elseif (date("Y-m-d") == $Year.'-'.$selectedMonth.'-'.$dia) //DIA ACTUAL
		{
			echo '<td align="center" width="34" height="34" class="dia_atual"><div class="margin"><a class="today" href="'.$caminho.'&dia='.$dia.'&mes='.$selectedMonth.'&ano='.$Year.'&cal_pag='.$selectedMonth.'" target="_top"><b>'.$adjusted_day.'<b></a></div></td>';
		}
		elseif ($selectedYear.'-'.$selectedMonth.'-'.$selectedDay == $selectedYear.'.'.$cal_pag.'.'.$selectedDay) //DIA selecionado
		{
			echo '<td align="center" width="34" height="34" class="dia_sel"><div class="margin"><b>'.$dia.'<b></div></td>';
		}
		elseif(((($dayRow +1) % 7) == 0) || (($dayRow % 7) == 0)) //FINS DE SEMANA
		{
			echo '<td align="center" width="34" height="34" class="preto"><div class="margin"><a class="preto" href="'.$caminho.'&dia='.$dia.'&mes='.$selectedMonth.'&ano='.$Year.'&cal_pag='.$selectedMonth.'" target="_top">'.$adjusted_day.'</a></div></td>';
		}
		else // OUTROS DIAS
		{
			echo '<td align="center" width="34" height="34" class="preto"><div class="margin"><a class="preto" href="'.$caminho.'&dia='.$dia.'&mes='.$selectedMonth.'&ano='.$Year.'&cal_pag='.$selectedMonth.'" target="_top">'.$adjusted_day.'</a></div></td>';
		}
		$day += 1;
		$dayRow += 1;
	}
	// os dias do proximo mes
	$dayRow=($dayRow-1) % 7;
	$t=1;
	while($dayRow < 6)
	{
		echo '<td align="center" width="34" height="34" bgcolor="#ffffff" class="cinza"><div class="margin">'.$t.'</div></td>';
		$dayRow++;
		$t++;
	}
	while($row<5)
	{
		$dayRow=0;
		echo '</tr><tr>';
		while($dayRow < 7)
		{
			echo '<td align="center" width="34" height="34" bgcolor="#ffffff" class="cinza"><div class="margin">'.$t.'</div></td>';
			$dayRow++;
			$t++;
		}
		$row++;
	}
	echo '</tr></table></td></tr></table>';
	return $Pos;
}

function show_calendar()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$site_lang;
	$dbh=\db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if($dbh)
	{
		$result=new DataObj;

		$sql ='SELECT P.ID, P.Data1, P.Data2, PL.Cabecalho, PL.Texto2, PL.Lang FROM pages AS P
			INNER JOIN pages_lang AS PL ON PL.ID_PAI=P.ID
			WHERE P.ID_ORG ="145"
			AND ("'.$_GET['ano'].'-'.$_GET['mes'].'-'.$_GET['dia'].'" BETWEEN P.Data1  AND P.Data2)
				AND
				P.Visible=1
				AND
				P.Tipo=3
				AND
				PL.Lang="'.$site_lang.'"
			ORDER BY
				P.Data1 DESC
		';
		$result->db_open($dbh,$DB_name,$sql);
		$All=0;
		echo '<div style="margin-left:12px;margin-right:12px;border:#000 0px solid"><div style="font-size:20px;padding-bottom:20px;text-transform:uppercase"><b>'.$GLOBALS['monthName'][$_GET['mes']-1].' '.$_GET['dia'].', '.$_GET['ano'].'</b></div>';
		while(!$result->EOF())
		{
			$page_id=$result->db_data["ID"];
			$data1=$result->db_data["data_ini"];
			$data2=$result->db_data["data_end"];
			$Cabecalho=$result->db_data["Cabecalho"];
			$Texto2=$result->db_data["Texto2"];
			echo '<div class="titulo_noticias4">'.$Cabecalho.'</div>';
			echo '<div>'.$Texto2.'</div>';
			echo '<div class="noticias_more3" onclick="window.open(\''.seo('pages.php?page_id='.$page_id).'\',\'_self\')">MORE...</div>';
			$result->db_next();
		}
		$result->db_close();
		echo '</div>';
	}
}