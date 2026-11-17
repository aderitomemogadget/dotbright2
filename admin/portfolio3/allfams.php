<?
	 header('Content-Type: text/html; charset=iso-8859-1');
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$site_http_url;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;

?>

<script>

</script>
<div class="container">

	<div style="height:5px;"></div>
	<div style="clear:both"></div>
	<?
	$paging = 19*$page;
	// $limit = 'Limit '.$paging.', 19';
	$limit = '';

	if($search != '') $searching = "AND (pl.Cabecalho like '%$search%' OR p.ID like '%$search%')";

	$sql = "Select p.ID, pl.Cabecalho from portfolio3_families p inner join portfolio3_families_lang pl on (pl.ID_PAI=p.ID) Where pl.Lang='".$admin_lang."' $searching group by pl.Cabecalho order by ID $limit";
	// echo $sql;
	$rs->db_open($db_link,$DB_name,$sql);
	$cont = 1;
	while(!$rs->EOF())
	{
		if($cont % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
		else				{$cor="#F6F6F6";$class="r2";$n="2";}

		$rs->db_data['Cabecalho'] = htmlentities_memo(str_replace("'",'´',$rs->db_data['Cabecalho']));
		?>

		<div style="height:15px; padding-top:5px; background:<?=$cor?>; width:330px;" id="linha_<?=$cont?>">&nbsp;&nbsp;<?=$rs->db_data['ID']?> - <? echo $rs->db_data['Cabecalho'];?><a onclick="totext2(<?=$_GET['val']?>, <?=$rs->db_data['ID']?>,'<?=$rs->db_data['ID']?> - <?  echo addslashes(substr($rs->db_data['Cabecalho']))?>')" href="javascript:;" style="text-aling:right;float:right;" class="preto"><u>Adicionar</u>&nbsp;&nbsp;</a></div>
		<div style="clear:both"></div>
		<?
		$cont++;
		$rs->db_next();
	}
	$rs->db_close();

	/* // $sql = "Select p.ID, pl.Cabecalho from portfolio3_families p inner join portfolio3_families_lang pl on (pl.ID_PAI=p.ID) Where pl.Lang='".$admin_lang."' $searching group by pl.Cabecalho order by p.Data Desc, ID";
	$sql = "Select p.ID, pl.Cabecalho from portfolio3_families p inner join portfolio3_families_lang pl on (pl.ID_PAI=p.ID) Where pl.Lang='".$admin_lang."' $searching group by pl.Cabecalho order by ID";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF()){
		$total = $rs->db_rows;
	}
	$rs->db_close();
	$total_pages = ceil($total/19);

	$pages_li = '';
	$cont=0;
	for($i = $page; $i < $total_pages; $i++ ) {
		if( $cont != 10){
			if($i == $page) $pages_li .= '<li style="display:inline;">'.($i+1).'&nbsp;&nbsp;</li>';
			else $pages_li .= '<li style="display:inline;"><a href="javascript:;" onclick="allArticles('.$val.','.$i.')">'.($i+1).'</a>&nbsp;&nbsp;</li>';
			$cont++;
		}else break;
	}
	 */

	?>
<div style="clear:both"></div>

</div>