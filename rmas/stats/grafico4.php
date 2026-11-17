<?
  include "settings.inc.php";
  include ("jpgraph.php");
  include ("jpgraph_line.php");
  global $DB_host,$DB_user,$DB_pass;
  $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
  $result = new DataObj;
  $result2 = new DataObj;
  if($f_data1=="" && $f_data2=="")
  {
		$dias=31;
		$f_data1=date("Y-m-d", strtotime("-$dias day"));
		$f_data2=date("Y-m-d");		
  }
  $mes_nomes=array("","Jan","Fev","Mar","Abr","Mai","Jun","Jul","Ago","Set","Out","Nov","Dez");
  $f_data1x=$f_data1." 00:00:01";
  $f_data2x=$f_data2." 23:59:59";
 if($grafico==1) // diario
  {
	  $sql="select encomendas_produtos.*,sum(TotalIva) as Total,encomendas.Estado, DATE(Data_Criacao) AS xdata, EXTRACT(DAY FROM Data_Criacao) AS dia from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where Data_Criacao between '$f_data1x' and '$f_data2x' Group By xdata Order by Data_Criacao";
	  $i=0;
	  if($result->db_open($db_link, $DB_name, $sql))
	  while(!$result->EOF())	
	  {
		$dia = $result->db_data["dia"];
		$datay[$i] = $result->db_data["Total"];
		$datay2[$i] = $result->db_data["Total"];
		$datax[$i]=$dia;
		$i++;
		$result->db_next();
	  }
	  $result->db_close();
  }
  
  if($grafico==2) // mensal
  {
  	$sql="select encomendas_produtos.*,sum(TotalIva) as Total,encomendas.Estado, EXTRACT(YEAR_MONTH FROM Data_Criacao) AS xdata, EXTRACT(MONTH FROM Data_Criacao) AS mes from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where Data_Criacao between '$f_data1x' and '$f_data2x' Group By xdata Order by Data_Criacao";
	  $i=-1;
	  $mes=0;
	  if($result->db_open($db_link, $DB_name, $sql))
	  while(!$result->EOF())	
	  {
		if($result->db_data["mes"] != $mes)	
		{
			$i++;
			$mes = $result->db_data["mes"];
			$datay[$i] = $result->db_data["Total"];
			$datay2[$i] = $result->db_data["Total"];
			$datax[$i]=$mes_nomes[$mes];
		}
		else
		{
			$datay[$i]+= $result->db_data["Total"];
			$datay2[$i]+= $result->db_data["Total"];
		}		
		$result->db_next();
	  }
	  $result->db_close();
  }
  
  for($i=0;$i<count($datax);$i++){ 	 $datax[$i].=" = ".number_format($datay[$i],"0","","."); }

db_closedb_conection($db_link);

if(count($datax)>1)
{
	$graph = new Graph(800,200);
	$graph->img->SetMargin(50,10,30,87);
	$graph->img->SetAntiAliasing();
	$graph->SetFrame(1,"#F5F5F5");
	$graph->SetScale("textlin");
	$graph->title->SetFont(FF_FONT1,FS_BOLD);
	$graph->title->SetColor("#004286");
	$graph->SetMarginColor("#F5F5F5");
	$graph->SetColor("#F5F5F5");
	$graph->title->Set("VENDAS (Valor) ENTRE $f_data1 e $f_data2");
	$graph->xaxis->SetTickLabels($datax);
	$graph->xaxis->SetLabelAngle(90);
	$p1 = new LinePlot($datay);
	$p1->SetLegend("Vendas");
	$p1->SetWeight(1);
	$p1->SetLineWeight(2);
	$p1->SetColor("#004286");
	$p1->mark->SetType(MARK_FILLEDCIRCLE);
	$p1->mark->SetFillColor("#FF4900");
	$p1->mark->SetWidth(2);
	$p2 = new LinePlot($datay2);
	$p2->SetLegend("Rupturas");
	$p2->SetWeight(1);
	$p2->SetLineWeight(2);
	$p2->SetColor("#ff0000");
	$p2->mark->SetType(MARK_FILLEDCIRCLE);
	$p2->mark->SetFillColor("#FF4900");
	$p2->mark->SetWidth(2);
	$graph->Add($p1);
	$graph->Add($p2);
	$graph->Stroke();
}
?>