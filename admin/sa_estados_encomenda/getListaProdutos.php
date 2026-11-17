<?php
  include "settings.inc.php";
  include "../layout/header.inc.php";

  $field = $_GET['field']; // id
  $field2 = $_GET['field2']; // desc
  $field3 = $_GET['field3']; // id cor
?>
<script language="javascript">
    function ReturnUrl(string)
    {
		string2 = string.split(';')

		var campo2 = parent.document.getElementById('<?php if(isset($_GET['field2'])){ echo $_GET['field2']; }?>');
		if(campo2) campo2.value = string2[1];

		str = string2[0].split('[spl]');
		var campo = parent.document.getElementById('<?php if(isset($_GET['field'])){ echo $_GET['field']; }?>');
		if(campo) campo.value = str[0];

		var campo3 = parent.document.getElementById('<?php if(isset($_GET['field3'])){ echo $_GET['field3']; }?>');
		if(campo3) campo3.value = str[1];


		parent.jQuery.fancybox.close();
    }
</script>
<style>
html, body{background:#FFF}
</style>
</head>
<body topmargin=0 leftmargin=0>
<form action="getListaProdutos.php" method="GET">
<input type="hidden" name="field" value="<?php echo $field?>" />
<input type="hidden" name="field2" value="<?php echo $field2?>" />
<input type="hidden" name="field3" value="<?php echo $field3?>" />
<table class="border1" width="100%" height="100%" border=0 cellspacing=0 cellpadding=0>
<tr bgcolor="#FFFFFF">
		<td height="35" align="center">&nbsp;</td>
		<td width="100" valign="middle" align="center"><b>Pesquisar</b></td>
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td>

		</tr>
		<tr bgcolor="#FFFFFF">
		<td width="50" valign="middle" align="center">&nbsp;</td>
		<td valign="middle" align="left"><input type="text" style="width:95%" name="pesquisa" id="pesquisa" class="form-control" value="<?php if(isset($_GET['pesquisa'])) echo $_GET['pesquisa'];?>" /></td>

		<td valign="middle" align="left" colspan="2"><input type="image" src="img/reload.png" title="Pesquisar" border="0" style="cursor:pointer;border:none" /></td>

		</tr>
  <tr><td colspan="4">
<?php

    show_links();
    echo '</td></tr></table></form><br><br>';
?>
<table width="100%" border=0><tr><td align="center"><input class="btn btn-primary" type="reset" value="<?php echo write_lang('key_cancelar','Cancelar');?>" name="btnCancel" onClick="parent.jQuery.fancybox.close();"></td></tr></table>
</td></tr></table>
</body></html><?php

function show_links($pai=0,$contador=-5)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
    $contador+=5;
	$rs=new DataObj;
	$pesq = '';
	if(isset($_GET['pesquisa']) && $_GET['pesquisa']!='')
	{
		$pesq = ' AND (products.Ref like "%'.$_GET['pesquisa'].'%" OR products_lang.Cabecalho like "%'.trim($_GET['pesquisa']).'%" OR products_tecidos.Name like "%'.trim($_GET['pesquisa']).'%")';
    }
	$sql = "
			SELECT
				products.ID, products_lang.Cabecalho, products_tecidos.ID AS ID_TECIDO, products_tecidos.Name
			FROM products
				INNER JOIN products_lang ON products_lang.ID_PAI = products.ID
				LEFT JOIN products_tecidos ON products_tecidos.ID_ORG = products.ID
			WHERE
				products.ID_ORG > 0
				".$pesq."
			GROUP BY
				products_tecidos.ID
			ORDER BY products.ID, products_tecidos.ID ASC
			";
	$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;

	$rs->db_open($db_link,$DB_name,$sql2);
	$i=0;    $c=0;
    while(!$rs->EOF())
	{
        if($c % 2 != 0) {$cor2="#def2fa";$class="r1";$n="1";}
        else            {$cor2="#F6F6F6";$class="r2";$n="2";}

	    $IDb = $rs->db_data["ID"];
	    $ID_TECIDO = $rs->db_data["ID_TECIDO"];
	    $Ref = $rs->db_data["Ref"];

		$nomeb = $rs->db_data["Cabecalho"];
		$cor = ''; $cor = $rs->db_data["Name"];
		$i=0;
		?>
			<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
			<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor2;?>'">
			<?php
				if($ID_TECIDO > 0) echo '<td align="left">'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'[spl]'.$ID_TECIDO.';'.htmlentities_b(addslashes($nomeb.' - '.$cor)).'")\'>'.$Ref.' - '.htmlentities_b(stripslashes($nomeb)).' '.($cor!='' ? ' - <u>'.$cor.'</u>' : '').'</a></td>';
				else echo '<td align="left">'.$sp.'&nbsp;<a class="preto" href="javascript:;" onclick=\'javascript:ReturnUrl("'.$rs->db_data["ID"].'|0;'.htmlentities_b(addslashes($nomeb)).'")\'>'.$Ref.' - '.htmlentities_b(stripslashes($nomeb)).' '.($cor!='' ? ' - '.$cor : '').'</a></td>';
			?>
			</tr>

			</table>
		 <?php

        $rs->db_next();
        $i++;
	}

	if($n_pags > 1)
		{

			$url_pages = 'getListaProdutos.php?pesquisa='.urlencode($_GET['pesquisa']).'&field='.urlencode($_GET['field']).'&field2='.urlencode($_GET['field2']).'&field3='.urlencode($_GET['field3']);

			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("'.$url_pages.'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("'.$url_pages.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}
	$rs->db_close();
}
?>