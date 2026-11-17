<?
	include "settings.inc.php";
	global $DB_host,$DB_user,$DB_pass;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$result=new DataObj;
	if($f_data1=="" && $f_data2=="")
	{
		$f_data1=date ("Y")."-".date ("m")."-01";
		$f_data2=date ("Y")."-".date ("m")."-".date ("d");
	}


	if($grafico==1)
	{
		//dia
		$f_data1=$ano."-".$mes."-01";
		$f_data2=$ano."-".$mes."-31";
	}
	if($grafico==2)
	{
		//mes
		$f_data1=$ano."-01-01";
		$f_data2=$ano."-12-".date ("d");
	}

	if(isset($_POST['f_data1']) && isset($_POST['f_data2']))
	{
		$f_data1=$_POST['f_data1'];
		$f_data2=$_POST['f_data2'];
	}

	$campanha_id=0;
	if(isset($_GET['campanha']) && $_GET['campanha']>0)
	{
		$campanha_id=$_GET['campanha'];
	}

	$f_data1x=$f_data1." 00:00:01";
	$f_data2x=$f_data2." 23:59:59";
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	?>
	<div class="center">
        <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                <tr> <td valign="top"><? include("menu.inc.php"); ?><br><br>Total de encomendas por produto <br>
                        <table width="100%" cellspacing="0" cellpadding="0" border=1 bordercolor="000000">
                        <tr>
                          <td valign="top">
                            <iframe id="ifrm" name="ifrm" src="grafico3.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1?>&f_data2=<?=$f_data2?>&campanha=<?=$campanha_id?>" scrolling="no" width="850" height="250" frameborder="0">
                            [Content for browsers that don't support iframes goes here]
                            </iframe>
                            <iframe id="ifrm" name="ifrm" src="grafico3b.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1?>&f_data2=<?=$f_data2?>&campanha=<?=$campanha_id?>" scrolling="no" width="850" height="250" frameborder="0">
                            [Content for browsers that don't support iframes goes here]
                            </iframe>
                            <? /* <iframe id="ifrm" name="ifrm" src="grafico4.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1?>&f_data2=<?=$f_data2?>" scrolling="no" width="850" height="250" frameborder="0">
                            [Content for browsers that don't support iframes goes here]
                            </iframe>	*/ ?>
                            <table width="100%" cellpadding="5" cellspacing="2" border="0">
                              <tr align="center" bgcolor="#F5F5F5"> 
                                <td class="preto">Produto</td>
                                <td class="preto">Nº de encomendas</td>
                                <td class="preto">Valor</td>
                              </tr>
                                <?
								$result1=new DataObj;
								$result2=new DataObj;

								$and = '';
								if($campanha_id>0)
								{
									$and.= ' AND encomendas.Promos_ID  REGEXP "[[:<:]]'.$campanha_id.'[[:>:]]" '; 
								}
								$and.= ' AND (encomendas.Estado=3 or encomendas.Estado=6 or encomendas.Estado=7) '; 

								$sql="select encomendas_produtos.*,count(*) as Contador,SUM(encomendas_produtos.Valor) as TOTAL from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' ".$and." Group By ID_Produto Order by Contador Desc";
								$result1->db_open($db_link,$DB_name,$sql);
								while (!$result1->EOF())
								{
									$n_acessos=$result1->db_data["Contador"];
									$file=$result1->db_data["Nome"];
									$codigo=$result1->db_data["ID_Produto"];
									$ref=$result1->db_data["Ref"];
									$total=number_format($result1->db_data["TOTAL"],2,',','.');
									if($codigo==0)
									{?>
									  <tr>
										<td class="preto2"><a class="preto" href="detalhe_produto.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1;?>&f_data2=<?=$f_data2;?>&codigo=<?=$codigo;?>"><? echo "ofertas";?>  </a></td>
										<td class="pretob" align="center"><?=$n_acessos;?></td>
										<td class="pretob" align="center"><?=$total;?></td>
									  </tr><?
									}
									else
									{?>
									  <tr>
										<td class="preto2"><a class="preto" href="detalhe_produto.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1;?>&f_data2=<?=$f_data2;?>&codigo=<?=$codigo;?>"><?=$ref." :".$file;?>  </a></td>
										<td class="pretob" align="center"><?=$n_acessos;?></td>
										<td class="pretob" align="center"><?=$total;?></td>
									  </tr><?
									}
									$result1->db_next();
								}
								?>
							</table>
						</td>
						<td width="415" valign="top">
                        	<form name="formFiltro" method="post">
							<table width="415" border="0" align="center" cellpadding="10" cellspacing="2" class="bordo">
         					<tr>
                            	<td align="center" bgcolor="#F5F5F5" class=titulobordo><b>Mostrar todas as encomendas concluídas</b></td>
							</tr>
                            <tr bgcolor="#F1F1F1">
                            	<td align="center" bgcolor="#FFFFFF" class=preto><? include("pesquisa.inc.php");?><br><br>
                                Nº TOTAL DE ENCOMENDAS:<?
                                $and = '';
								if($campanha_id>0)
								{
									$and.= ' AND encomendas.Promos_ID  REGEXP "[[:<:]]'.$campanha_id.'[[:>:]]" '; 
								}
								$and.= ' AND (encomendas.Estado=3 or encomendas.Estado=6 or encomendas.Estado=7) '; 
                                    $result1=new DataObj;
                                    $sql="select count(*) as Contador from encomendas where Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x'". $and;
                                    $result1->db_open($db_link,$DB_name,$sql);
                                    if(!$result1->EOF()) echo "<b>".number_format($result1->db_data["Contador"],0,'','.')."</b>";
                                    $result1->db_close();
                                    ?><br><br>
                                VALOR ACUMULADO DOS PRODUTOS:<?
                                    $sql="select SUM(encomendas_produtos.Valor) as TOTAL from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where encomendas.Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x'". $and;
                                    $result1->db_open($db_link,$DB_name,$sql);
                                    if(!$result1->EOF()) echo "<b>".number_format($result1->db_data["TOTAL"],2,',','.')." &euro;</b>";
                                    $result1->db_close();
									/*?><p><p>TOP 100 ENCOMENDAS (<a href="grafico_cbs.php?grafico=<?=$grafico?>&ano=<?=$ano?>&mes=<?=$mes?>&d=<?=$d?>&f_data1=<?=$f_data1?>&f_data2=<?=$f_data2?>">ver gr&aacute;fico</a>)<p><? */
									 $sql="SELECT COUNT( * ) AS Counter,username,encomendas.ID_Cliente
												FROM encomendas
												INNER JOIN clientes ON clientes.ID=encomendas.ID_Cliente
												WHERE Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' $and
												GROUP BY ID_Cliente
												ORDER BY Counter DESC 
												LIMIT 0 ,100";
									$result1->db_open($db_link,$DB_name,$sql);
									if(!$result1->EOF())
									{?><br><br>
									<table class="bordo" border="0" cellspacing="10" cellpadding="5" width="200">
									<tr bgcolor="#F5F5F5" class=titulobordo>
									  <td align=center>Cliente</td><td align=center>Nº Enc.</td><td align=center>Valor</td></tr><?
										while(!$result1->EOF())
										{
						    			$sql="SELECT SUM( encomendas_produtos.Valor ) AS TOTAL FROM encomendas_produtos
						                  INNER JOIN encomendas ON encomendas.ID=encomendas_produtos.ID_PAI
						                  WHERE Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' $and AND encomendas.ID_Cliente=".$result1->db_data["ID_Cliente"];
											$result2->db_open($db_link,$DB_name,$sql);
											echo "<tr><td align=center><b>".$result1->db_data["username"]."</b></td><td align=right>".$result1->db_data["Counter"]."</td><td align=right>".number_format($result2->db_data["TOTAL"],2,',','.')."</td></tr>";
											$result2->db_close();
											$result1->db_next();
										}?>
									  </table>
										<?
									}
									$result1->db_close();
									?>

						            </td>
						          </tr>
							</table>
                            </form>
                            <table width="415" border="0" align="center" cellpadding="10" cellspacing="2" class="bordo">
         					<tr>
                            	<td align="center" bgcolor="#F5F5F5" class=titulobordo><b>Mostrar Nº de encomendas por campanha</b></td>
							</tr>
                            <tr bgcolor="#F1F1F1">
                            	<td align="center" bgcolor="#FFFFFF" class=preto>
                                Nº TOTAL DE ENCOMENDAS:<?
                                 $and = '';
								$and.= ' AND (encomendas.Estado=3 or encomendas.Estado=6 or encomendas.Estado=7) '; 
                                    $result1=new DataObj;
                                    $sql="select count(*) as Contador from encomendas where Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' AND Promos_ID!='' $and";
                                    $result1->db_open($db_link,$DB_name,$sql);
                                    if(!$result1->EOF()) echo "<b>".number_format($result1->db_data["Contador"],0,'','.')."</b>";
                                    $result1->db_close();
                                    ?><br><br>
                                VALOR:<?
                                    $sql="select SUM(encomendas_produtos.Valor) as TOTAL from encomendas_produtos inner join encomendas on encomendas.ID=encomendas_produtos.ID_PAI where encomendas.Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' AND encomendas.Promos_ID!='' $and";
                                    $result1->db_open($db_link,$DB_name,$sql);
                                    if(!$result1->EOF()) echo "<b>".number_format($result1->db_data["TOTAL"],2,',','.')." &euro;</b>";
                                    $result1->db_close();

                                    $array_promos=array();

                                    $sql="select Promos_ID from encomendas where Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' AND Promos_ID!='' $and";
                                    $result1->db_open($db_link,$DB_name,$sql);
                                    while(!$result1->EOF()) {
                                    	// echo "<b>".number_format($result1->db_data["TOTAL"],2,',','.')." &euro;</b>";
                                    	$vx = explode(';',$result1->db_data["Promos_ID"]);
                                    	for ($px=0; $px < count($vx); $px++) { 
                                    		if($vx[$px]!='') $array_promos[]=$vx[$px];
                                    	}
                                    	$result1->db_next();
                                    }
                                    $result1->db_close();
                                    $promos_id='';
                                    $array_promos= @array_unique($array_promos);
                                    for ($px=0; $px < count($array_promos); $px++) { 
                                    	if($px==0)  $promos_id.=$vx[$px];
                                    	else   $promos_id.=','.$vx[$px];
                                    }

									 $sql="SELECT *
												FROM sales_promotions
												WHERE promo_id in($promos_id)
												ORDER BY DESIGNACAO DESC 
												LIMIT 0 ,100";
									// echo $sql;
									$result1->db_open($db_link,$DB_name,$sql);
									if(!$result1->EOF())
									{?><br><br>
									<table class="bordo" border="0" cellspacing="10" cellpadding="5" width="300">
									<tr bgcolor="#F5F5F5" class=titulobordo>
									  <td align=center>Promoção</td><td align=center>Nº Enc.</td><td align=center>Valor</td></tr><?
										while(!$result1->EOF())
										{
											$sql="SELECT count(encomendas.ID) as Counter FROM encomendas
						                	WHERE Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' AND encomendas.Promos_ID  REGEXP '[[:<:]]".$result1->db_data["promo_id"]."[[:>:]]' $and";
											$result2->db_open($db_link,$DB_name,$sql);
											$NUM_ENCOMENDAS=$result2->db_data["Counter"];
											$result2->db_close();

						    				$sql="SELECT count(encomendas.ID) as Counter,SUM( encomendas_produtos.Valor ) AS TOTAL FROM encomendas_produtos
						                	INNER JOIN encomendas ON encomendas.ID=encomendas_produtos.ID_PAI
						                	WHERE Data_Criacao BETWEEN '$f_data1x' AND '$f_data2x' AND encomendas.Promos_ID  REGEXP '[[:<:]]".$result1->db_data["promo_id"]."[[:>:]]' $and";
											$result2->db_open($db_link,$DB_name,$sql);
											echo '<tr><td align=center><b><a href="produtos.php?grafico='.$grafico.'&amp;ano='.$ano.'&amp;mes='.$mes.'&amp;d='.$d.'&amp;f_data1='.$f_data1.'&amp;f_data2='.$f_data2.'&amp;campanha='.$result1->db_data["promo_id"].'">'.$result1->db_data["DESIGNACAO"].'</a></b></td><td align=right>'.$NUM_ENCOMENDAS.'</td><td align=right>'.number_format($result2->db_data["TOTAL"],2,',','.').'</td></tr>';
											$result2->db_close();
											$result1->db_next();
										}?>
									  </table>
										<?
									}
									$result1->db_close();
									?>

						            </td>
						          </tr>
							</table>
						</td>
                    
				</tr>
				</table>
            </td>
            <td width="10px">&nbsp;</td>
			<td valign="top" width=200px><br /></td>
		</tr>
		</table>
	</div><?
	include "../layout/bottom.inc.php";?>