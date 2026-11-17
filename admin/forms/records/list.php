<?
	include "settings.inc.php";
	include "../../layout/header.inc.php";
	include "../../layout/body.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;

	$sql="SELECT * FROM forms WHERE ID=$ID_ORG";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$nome=$rs->db_data["Name"];
		$campos_listagem=$rs->db_data["Campos_listagem"];
		$titulos_listagem=$rs->db_data["Titulos_listagem"];
	}
	$rs->db_close();
	$tem_data_auto=0;
	$sql="SELECT * FROM form_fields WHERE Tipo=10 AND ID_ORG=$ID_ORG";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()) $tem_data_auto=1;
	$rs->db_close();
	$tem_prod=0;
	$sql="SELECT * FROM form_fields WHERE Tipo=32 AND ID_ORG=$ID_ORG";
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()) $tem_prod=1;
	$rs->db_close();

	if(!$bgn) $bgn=0;
	if(!$num) $num=20;
	$limit=" LIMIT ".$bgn.",".$num;
	$sql="SELECT COUNT(ID) FROM form_records WHERE ID_ORG=".$ID_ORG;
	$hRes=$rs->db_open($db_link,$DB_name,$sql);
	if(!$hRes) echo db_error($db_link);
	$total_pag=ceil($rs->db_data[0]/$num);
	$rs->db_close();
	$a_campos=explode(";",$campos_listagem);
	$n_campos=count($a_campos);
?>
<table width="100%" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top"><? include "../../layout/menus.inc.php";?>

<script>
function ficheiro()
{
	open("exportar_excel.php?dump=<?=uniqid("");?>",target="_self");
}
</script>
<table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
	<tr>
	<td width="16" height=20 valign="middle">&nbsp;</td>
  	<form name="formFiltro" method="post"><?
	if($tem_data_auto)
	{
	?>
 		<td width="200" align="left" class="preto">&nbsp;&nbsp;De <input style="height:15; WIDTH:80; FONT-FAMILY: 'Arial Unicode MS'; FONT-SIZE: 9px;" name="f_data1" value="<?=$f_data1;?>"> a <input style="height:15; WIDTH:80; FONT-FAMILY: 'Arial Unicode MS'; FONT-SIZE: 9px;" name="f_data2" value="<?=$f_data2;?>"></td><?
	}
	if($tem_prod)
	{
		?>
        <td width="190" class="text-center" class="preto">
            <select style="height=15; font-family:'Arial Unicode MS'; font-size: 9px;" name="f_nome">
            	<option value="">
                	Todos
                </option>
				<?
					for ($i=0;$i < $counter;$i ++)
					{
						echo "<option value=" . $id[$i] . ">" . $header[$i] . "</option>" ;
					}
				?>
            </select>
        </td><?
	}
	if($tem_data_auto || $tem_prod){?>
        <td width="16" class="text-center" valign="middle">
        	<input type="image" src="../../images/open.png" align="bottom" class="normal" style="height:16;"></td><?
	}?>
		<td valign=top bgcolor="<?=$cor;?>" class="preto">&nbsp;</td>
    </form>
    <?
		global $DB_host,$DB_user,$DB_pass,$DB_name,$Nome;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$count=0;
		$rs=new DataObj;
		$sql="SELECT * FROM form_records WHERE ID_ORG=".$ID_ORG." ORDER BY form_records.ID ASC ".$limit;
		$hRes=$rs->db_open($db_link,$DB_name,$sql);
		if(!$hRes) echo db_error($db_link);

		$data=new DataObj;
		$sql="SELECT ID FROM form_fields WHERE ID_ORG=". $ID_ORG ."  AND Tipo=32";
		$hRes=$data->db_open($db_link,$DB_name,$sql);
		$product=$data->db_data["ID"];
		$data->db_close();

		$data=new DataObj;
		$sql="SELECT ID FROM form_fields WHERE ID_ORG=" . $ID_ORG . " AND Tipo=10";
		$hRes=$data->db_open($db_link,$DB_name,$sql);
		$main_date=$data->db_data["ID"];
		$data->db_close();

		$obj=new DataObj;
		$sql="SELECT * FROM forms WHERE ID=".$ID_ORG;
		$hRes=$obj->db_open($db_link,$DB_name,$sql);
		$campos_listagem=$obj->db_data["Campos_listagem"];

		if($campos_listagem)
		{
			$a_campos=explode(";",$campos_listagem);
			$n_campos=count($a_campos);
			$i=0;
			if($f_nome)
			{
				$ID= GetId($ID_ORG,$f_nome,$f_data1,$f_data2,$main_date,$product);
			}
			elseif(!$f_data1=="" && !$f_data2=="")
			{
				$ID=GetId($ID_ORG,$f_nome,$f_data1,$f_data2,$main_date,$product);
			}
			else
			{
				$list=GetId($ID_ORG,$f_nome,$f_data1,$f_data2,$main_date,$product);
				$clean_list=array();
				$clean_list=array_unique($list);
				$i=0;
				$ID=array();
				foreach ($clean_list as $key => $value)
				{
				  if(!is_null($value))
				  {
					$ID[$i]=$value;
					$i++;
				  }
				}
			}
			$ID_List=$ID;
			if(count($ID)>0)
			{?>
            <td align="right" valign="top" bgcolor="<?=$cor;?>" class="preto">
            <form name="export_all" method="post" action="form_records_exporta.php" target="_blank"/>
                <input type="hidden" name="ID_ORG" value="<?=$ID_ORG?>">
                <input type="submit" name="export" value="Exportar Listagem" class="btn btn-primary">
               <?
                if($ID_List)
				{
					foreach($ID_List as $value)
                	{
               		?>
                    	<input type="hidden" name="id_list[]" value="<?=$value;?>">
                	<?
                	}
				}
               	?>
                <input type="hidden" name="ID_ORG" value="<?=$ID_ORG;?>">
            </form>
        </td><?
			}?>
    </tr>
</table>
			<? if(count($ID)==0)
			{?>
	            <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                <tr>
                	<td valign="top">
                    	<table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
                        <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
                        	<td width=25 height=20 valign="middle">Ainda não existem registos</td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table>
				<?
			}
			for ($count=0;$count < count($ID);$count++ )
			{
				if($count % 2 != 0)	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#FFFFFF";$class="r2";$n="2";}

			?>
            <table width="100%" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
            <tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
            	<td width=25 height=20 valign="middle"><a class="preto" href="form_records.php?ID=<? echo $ID[$count]?>&ID_ORG=<?=$ID_ORG?>&bgn=<?=$bgn;?>&num=<?=$num;?>"><img border=0 src="../../images/page.png"></a></td>
                <?
                    $j=0;
				    while($j < $n_campos - 1)
                    {
                       	$obj=new DataObj;
                        $sql="SELECT * FROM form_records_fields WHERE Name=". $a_campos[$j]." AND ID_ORG=". $ID[$count];
						$hRes=$obj->db_open($db_link,$DB_name,$sql);
                        if($count % 2 != 0)	$cor="#fdfdfd";
                        else $cor="#f1f4f1";
						$str=$obj->db_data["Valor"];
						if($a_campos[$j]==$product)
						{
							$data=new DataObj;
							$sql="SELECT Cabecalho FROM products_lang WHERE ID=" . $str;
							$data->db_open($db_link,$DB_name,$sql);
							$str=$data->db_data["Cabecalho"];
						}
                        $str=strlen($str) > 59 ? substr($str,0,57) . "..." : $str;
					    $str=htmlentities_b($str);
					    ?>
                        <td width="20%" height="20" align="left" valign="middle" class="preto"><a class="preto" href="form_records.php?ID=<? echo $ID[$count]?>&ID_ORG=<?=$ID_ORG?>&bgn=<?=$bgn;?>&num=<?=$num;?>">&nbsp;<?=$str;?></a></td>
                        <?
                        $j++;
                    }
                    ?>
                    <td width="400px" align=right><table width="400px"><tr><td align=right>
                        <form name="single_export<?=$ID[$count];?>" method="post" action="form_records_exporta.php" target="_blank">
                            <a href="javascript:;" onclick="javascript:document.single_export<?=$ID[$count];?>.submit();" target=_self><img alt="" src="exportar.png" border=0 align="absmiddle" border=0></a>
                          <? if($imprimir){?><a href="print.php?ID=<?=$ID[$count]?>&ID_ORG=<?="$ID_ORG";?>&task=imprimir" target=blank><img alt="" src="imprimir.png" border=0 align="absmiddle" border=0></a><? }?>
				            <a href="form_records.php?ID=<?=$ID[$count]?>&ID_ORG=<?="$ID_ORG";?>&task=apagar"><img alt="" src="del.png" border=0 align="absmiddle"></a>
                           <input type="hidden" name="ID" value="<?=$ID[$count];?>">
                           <input type="hidden" name="ID_ORG" value="<?=$ID_ORG;?>">
                        </form></td></tr></table>
                    </td>
				</tr>
                </table>
				<?
			 }
		 }
		 else
		 {
			$obj=new DataObj;
			$str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$codigo and form_fields.Tipo=12";
			$hRes=$obj->db_open($db_link,$DB_name,$str);
			$Loja_ID=$obj->db_data["Valor"];
			$str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$codigo and form_fields.Tipo=11";
			$hRes=$obj->db_open($db_link,$DB_name,$str);
			$Activo=$obj->db_data["Valor"];
			$str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$codigo and form_fields.Tipo=10";
			$hRes=$obj->db_open($db_link,$DB_name,$str);
			$Data="";
			$Data=$obj->db_data["Valor"];
			if(!$Loja_ID){
			  $str="Select * from form_records_fields inner join form_fields on form_records_fields.Name=form_fields.ID where form_records_fields.ID_ORG=$codigo and form_fields.Tipo=1";
			  $hRes=$obj->db_open($db_link,$DB_name,$str);
			  $Loja_ID=$obj->db_data["Valor"];
			  if(!$Loja_ID) $Loja_ID="Outros";
			}
			$name=$Loja_ID;
			if($Activo){
				if($count % 2 != 0) $cor="#CCFF00";
				else $cor="#EBFF98";
			} else {
				if($count % 2 != 0)	$cor="#fdfdfd";
				else $cor="#f1f4f1";
			}
		}
		if(count($ID)>0)
		{
?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
<tr>
  <td height=20 class="t1" class="text-center" valign="middle">
  <?
  if(!$f_nome && !$f_data1 && !$f_data2)
  {
        if($total_pag)
        {
            echo "&nbsp;Páginas:&nbsp;&nbsp;";
            $start=$bgn-125;
            if($start<10) $start=0;
            $pagina=(($bgn/25)-4);
            if($pagina<1) $pagina=1;
            if($total_pag>($pagina+10)) $total_pag_tmp=$pagina+10;
            else $total_pag_tmp=$total_pag;
            if($pagina>1) echo '<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&num='.$num.'">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;';
            for($k=$pagina;$k<=$total_pag_tmp;$k++)
            {
                if($bgn<>$start)echo '<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&bgn='.$start.'&num='.$num.'">'.$k.'</a>&nbsp;&nbsp;';
                else echo '<font class="red"><b>'.$k.'</b></font>&nbsp;&nbsp;';
                $start+=$num;
            }
            if($total_pag>$total_pag_tmp) echo '&nbsp;&nbsp;...&nbsp;&nbsp;<a class="preto" href="list.php?ID_ORG='.$ID_ORG.'&bgn='.(($total_pag*25)-25).'&num='.$num.'">'.$total_pag.'</a>';
        }
    }?>
	</td>
</tr>
</table><? }?>
<!-- <img src="../../images/bsh.png" width="890px"> -->


		</td>
		<td width="10px">&nbsp;</td>
		<td valign="top" width="200"><? show_fast_tools();?></td>
	</tr>
	</table>
<? 	include "../layout/bottom.inc.php";?>