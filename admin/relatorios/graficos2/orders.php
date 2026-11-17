<?php
$path='../../../core/';
include $path.'core_files.php';
include("pChart/pData.class");
include("pChart/pChart.class");

$anoactual=date("Y");
$anoanterior=$anoactual-1;

$facturadoanoanterior=total_facturado($anoanterior);
$facturadoanoactual=total_facturado($anoactual);

// Dataset definition
$DataSet = new pData;
//$DataSet->AddPoint(array(-10,-4,2,5,10,11,12,13,14,15,16,17,18,19,23,25,27,30,37),"Serie1");
//$DataSet->AddPoint(array(2,5,7,5,1,5,6,4,8,4,0,2,5,6,4,5,6,7,6),"Serie2");
$DataSet->AddPoint($facturadoanoanterior[1],"AnoAnterior");
$DataSet->AddPoint($facturadoanoactual[1],"AnoActual");
$DataSet->AddAllSeries();
$DataSet->SetAbsciseLabelSerie();
$DataSet->SetSerieName("Ano ".$anoanterior,"AnoAnterior");
$DataSet->SetSerieName("Ano ".$anoactual,"AnoActual");

// Initialise the graph
$Test = new pChart(700,230); // Largura x altura
$Test->setFontProperties("Fonts/tahoma.ttf",8); // fonte e tamanho
$Test->setGraphArea(60,30,680,200); // area util para desenho do grafico
$Test->drawFilledRoundedRectangle(7,7,693,223,5,240,240,240); // x,y,w,h, curva, R,G,B (interno)
$Test->drawRoundedRectangle(5,5,695,225,5,230,230,230); // x,y,w,h, curva, R,G,B (externo)
$Test->drawGraphArea(255,255,255,TRUE); // R,G,B cor do grafico
//dados,legendas,,R,G,B,,angulo,
$Test->drawScale($DataSet->GetData(),$DataSet->GetDataDescription(),SCALE_NORMAL,150,150,150,TRUE,0,2);
//,,R,G,B,
$Test->drawGrid(4,TRUE,230,230,230,50);

// Draw the 0 line
$Test->setFontProperties("Fonts/tahoma.ttf",6);
$Test->drawTreshold(0,143,55,72,TRUE,TRUE);

// Draw the filled line graph
$Test->drawFilledLineGraph($DataSet->GetData(),$DataSet->GetDataDescription(),50,TRUE);

// Finish the graph
$Test->setFontProperties("Fonts/tahoma.ttf",8);
$Test->drawLegend(65,35,$DataSet->GetDataDescription(),255,255,255);
$Test->setFontProperties("Fonts/tahoma.ttf",10);
$Test->drawTitle(60,22,"Evolução de Vendas",50,50,50,585);
$Test->Render(); //Gera a imagem*/

function total_facturado($ano)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $dados=new DataObj;

    $totalSemIVA_acumulado=0;
    $totalComIVA_acumulado=0;

    $totalSemIVA = array();
    $totalComIVA = array();	$tem_encomendas = 0;	$sql="SELECT ID from encomendas WHERE Estado > -1";    $dados->db_open($db_link,$DB_name,$sql);
	if(!$dados->EOF()) $tem_encomendas = 1;	$dados->db_close();
    $i=1;	if($tem_encomendas == 1)	{
		while($i<13)
		{
			if($i<10) $i='0'.$i;
			$mes=$i;

			$sql="SELECT SUM(TotalDocSemIva) as totalSemIVA, SUM(TotalDocComIva) as totalComIVA from encomendas WHERE Estado > -1 AND Data_Criacao like '%".$ano.'-'.$mes."%'";
			$hRes=$dados->db_open($db_link,$DB_name,$sql);

			if(!empty($dados->db_data["totalSemIVA"])) $totalSemIVA_acumulado+= (int) round($dados->db_data["totalSemIVA"], 0);
			if(!empty($dados->db_data["totalComIVA"])) $totalComIVA_acumulado+= (int) round($dados->db_data["totalComIVA"], 0);

			$totalSemIVA[]=$totalSemIVA_acumulado;
			$totalComIVA[]=$totalComIVA_acumulado;

			$dados->db_close();
			$i++;
		}	}
    return array($totalSemIVA,$totalComIVA);
}
?>