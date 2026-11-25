<?php
	header ('Content-type: text/html; charset=UTF-8');
    include 'settings.inc.php';
	include "../layout/header.inc.php";
	
	
	$field = explode(';', $_GET['field']);
	
	
    global $DB_host,$DB_user,$DB_pass,$DB_name;
    $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

    if(!$bgn) $bgn=0;
    if(!$num) $num=50;
    $limit=" LIMIT ".$bgn.",".$num;
    /******************************************************/
    // BEGIN ORDENA��O

    if(!isset($_POST["tipoorder"]) || !isset($_POST["campo"]) || $_POST["tipoorder"]=="" || $_POST["campo"]=="") $order=" ORDER by products_families_lang.Cabecalho,products_lang.Cabecalho,Ref ";
    else $order=" ORDER by " . base64_decode($campo) . " " . $tipoorder;

    if(isset($_POST["tipoorder"]) && $_POST["tipoorder"]=="ASC"){$tipoorder="DESC";$ordenar=$txt_ordenar_desc;}
    else{$tipoorder="ASC";$ordenar=$txt_ordenar_asc;}

    $dados3=new DataObj;
    if (isset($_POST["ref"]) && $_POST["ref"]!="")
    {
        if($_POST["campo"]=="") $campo="ref" . $i;
        ?>
        <script type="text/javascript">
            window.parent.opener.document.form.<?php echo $campo?>.value="<?php echo $ref?>";
            window.parent.opener.carrega_des(window.parent.opener.document.form.<?php echo $campo?>,<?php echo $i?>);

            if (window.parent.opener.document.getElementById("IDTabelaLinhas")) {
                    
                    if(!window.opener.document.getElementById("ref<?php echo $i+1?>"))
                        window.parent.opener.inserirLinhaDocumento('<?php echo $i?>','<?php echo $tabela?>','<?php echo $ID?>');
            }
            window.close();
        </script>
    <?php
        exit;
    }
    ?>
<script>
function reset_campos()
{
	document.formFiltro.bgn.value=0;
}
function abrir(ref)
{
        document.getElementById("ref").value=ref;	
	document.form.submit();
}
function ordenar_tabela(tipoorder, campo){
	document.getElementById("tipoorder").value=tipoorder;
	document.getElementById("campo").value=campo;	
	document.formFiltro.submit();	
}

</script>
<style>
html, body{background:#FFF}
form{
	margin:0px;
	padding:0px;
}
body{
	margin:0px;
	padding:0px;
}
</style>
<link rel="stylesheet" type="text/css" href="sa.css" />
</head>
<body>
<table>
<tr>
<td valign="top">

	<form action="popup_produtos.php" method="post" name="formFiltro">
		<input type="hidden" name="bgn" id="bgn" value="<?php echo $bgn?>" />
		<input type="hidden" name="num" id="num" value="<?php echo $num?>" />
		<input type="hidden" name="ID" id="ID" value="<?php echo $ID?>" />
		<input type="hidden" name="tabela" id="tabela" value="" />
		<input type="hidden" name="f_arm" id="f_arm" value="<?php echo $f_arm?>" />
		<input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo?>" />
		<input type="hidden" name="campo" id="campo" value="<?php echo $campo?>" />
		<input type="hidden" name="i" id="i" value="<?php echo $i?>" />
		<input type="hidden" name="tipoorder" id="tipoorder" value="<?php echo $tipoorder;?>" />		<script type="text/javascript">
                   
                  
			
		</script>
			<table width="100%" border=0 cellspacing=0>				
				<tr>
					<td height=5 colspan=2><img src="cm_fill.gif" width="5" height="5">
					</td>
				</tr>
		  </table>
		  <table width="100%" bgcolor="#fafafa" border=0 cellspacing=0 cellpadding=1>  			
			<tr> 
				<td height="30px" width="29px" class="titulos_campos">&nbsp;</td>
				<td width="90" class="titulos_campos" title="<?php echo $txt_ordenar." ".$ordenar." família";?>" style="cursor:pointer;"  onclick="ordenar_tabela('<?php echo $tipoorder?>', '<?php echo base64_encode('products_families_lang.Cabecalho')?>;')">Família</td>
				<td width="90" class="titulos_campos" title="<?php echo $txt_ordenar." ".$ordenar." Referência";?>" style="cursor:pointer;"  onclick="ordenar_tabela('<?php echo $tipoorder?>', '<?php echo base64_encode('Ref')?>;')">Produto</td>
				<td class="titulos_campos" title="<?php echo $txt_ordenar." ".$ordenar." Descrição";?>" style="cursor:pointer;"  onclick="ordenar_tabela('<?php echo $tipoorder?>', '<?php echo base64_encode('products_lang.Cabecalho')?>;')">Designação</td>
				<td class="titulos_campos" title="<?php echo $txt_ordenar." ".$ordenar." P.Custo";?>" style="cursor:pointer;"  onclick="ordenar_tabela('<?php echo $tipoorder?>', '<?php echo base64_encode('PrecoCusto')?>')" width="70px" align="center">P.Custo</td>
				<td class="titulos_campos" title="<?php echo $txt_ordenar." ".$ordenar." Stock";?>" style="cursor:pointer;"  onclick="ordenar_tabela('<?php echo $tipoorder?>', '<?php echo base64_encode('Disponibilidade')?>')" width="70px" align="center">Stock</td>
				<td class="titulos_campos">Preços</td>
				<td class="titulos_campos" width="20px">&nbsp;</td>
			</tr>
			<tr>  
				<td class="titulos_campos">&nbsp;</td>
				<td class="titulos_campos"><input type="text" onchange="javascript:reset_campos()" name="f_fam" value="<?php echo $f_fam;?>" style="WIDTH:100%; font-size:10px" class="small"></td>
				<td class="titulos_campos"><input type="text" onchange="javascript:reset_campos()" name="f_ref" value="<?php echo $f_ref;?>" style="WIDTH:100%; font-size:10px" class="small"></td>
				<td class="titulos_campos"><input type="text" onchange="javascript:reset_campos()" name="f_nome" value="<?php echo $f_nome;?>" style="WIDTH:100%; font-size:10px" class="small"></td>
				<td class="titulos_campos"><input type="text" onchange="javascript:reset_campos()" name="f_pcusto" value="<?php echo $f_pcusto;?>" style="WIDTH:100%; font-size:10px" class="small"></td>
				<td class="titulos_campos"><input type="text" onchange="javascript:reset_campos()" name="f_stock" value="<?php echo $f_stock;?>" style="WIDTH:100%; font-size:10px" class="small"></td>
				<td class="titulos_campos">&nbsp;</td>
				<td class="titulos_campos" width="20px"><input name="image" type="image" class="" src="img/findicon.png" alt="" align="absmiddle"></td>				
			</tr>	
		</form>
		<form action="popup_produtos.php" name="form" method="post">
			<input type="hidden" name="ID" id="ID" value="<?php echo $ID?>" />
			<input type="hidden" name="tabela" id="tabela" value="" />
			<input type="hidden" name="tipo" id="tipo" value="<?php echo $tipo?>" />
			<input type="hidden" name="campo" id="campo" value="<?php echo $campo?>" />
			<input type="hidden" name="i" id="i" value="<?php echo $i?>" />
			<input type="hidden" name="ref" id="ref" value="" />
			
			<?
			$sql="SELECT products.*, products_lang.Cabecalho, products_families_lang.Cabecalho as Familia FROM products inner join products_lang on products.ID=products_lang.ID_PAI and products_lang.Lang='pt' left outer join products_families_lang on products.ID_ORG=products_families_lang.ID_PAI and products_families_lang.Lang='pt' Where products.ID=products_lang.ID_PAI " . $sql_cond ." ".$order;
		
			$dados=new DataObj;
			$dados2=new DataObj;

			$dados->db_open($db_link,$DB_name,$sql);
			$total_pag=ceil($dados->getrows()/$num);

			$sql=$sql.$limit;
			
			$dados->db_open($db_link,$DB_name,$sql);
			$count=0;
			$old_familia="";
			while(!$dados->EOF())
			{
				$dados->db_data["Ref"];
				
				if($cor=="#F6F6F6")	$cor="#E0E0E0";
	         	else				$cor="#F6F6F6";		
				
				if(!$dados->db_data["Familia"])$dados->db_data["ID_ORG"]=-1;
				if($old_familia!=$dados->db_data["ID_ORG"]){
					
					$familia=$dados->db_data["Familia"];
					if(!$familia)$familia="SEM FAMILIA";
					?>
					<tr bgcolor="#ffffff">
						<td height="30" align="center" valign="top" style="border-top:solid 1px #000;">&nbsp;</td>
						<td valign="top" class="texto" colspan=7 style="vertical-align:middle;border-top:solid 1px #000;text-align:left; font-weight:bold;"><?php echo $familia;?></td>
					<?
				}
				if($familia)$old_familia=$dados->db_data["ID_ORG"];else $old_familia="0";
				?>
				<tr bgcolor="<?php echo $cor;?>" onmouseover="this.style.backgroundColor='#DBE7F1'; this.style.cursor='pointer';" onmouseout="this.style.backgroundColor='<?php echo $cor;?>'" onDblClick="javascript:abrir('<?php echo $dados->db_data["Ref"]?>')" title="Duplo click para seleccionar o produto">
					<td height="30" align="center" valign="middle"><a href="../produtos/products.php?ID=<?php echo $dados->db_data["ID"]?>&ID_ORG=<?php echo $dados->db_data["ID_ORG"]?>" title="Click para abrir a ficha do produto" target="_blank"><img title="" src="../templates/<?php echo $theme?>/folder.gif" border="0" align="absmiddle"></a></td>
					<td valign="top" class=texto style="vertical-align:middle;" colspan="2"><?php echo $dados->db_data["Ref"];?></td>
					<td valign="top" class=texto style="text-align:left; vertical-align:middle;"><?php echo $dados->db_data["Cabecalho"];?></td>
					<td valign="top" class="texto" style="vertical-align:middle"><?php echo $dados->db_data["PrecoCusto"];?></td>
					<td valign="top" class="texto" style="vertical-align:middle"><?php echo $dados->db_data["Disponibilidade"];?></td>
					<td valign="top" style="vertical-align:middle" class="texto"><?php echo round($precos,4);?></td>
					<td class="texto"><br /></td>
				</tr>		
				<?					
				$dados->db_next();						
				$count++;
			}
			$dados->db_close();
		?>
		</form>
		</table>
		<table width="100%" align="center" border=0 cellspacing=0 class="fundo_botoes" height="30">
			<tr>
				<td bgcolor=#B3B3B3 class="texto">
				  	<?
					if($total_pag) {
					  echo "&nbsp;P�ginas:&nbsp;&nbsp;";
				  
					  $start=$bgn-($num*5);
					  if($start<10) $start=0;
					  
					  $pagina=(($bgn/$num)-4);
					  if($pagina<1) $pagina=1;
					  
					  if($total_pag>($pagina+10)) $total_pag_tmp=$pagina+10;
					  else $total_pag_tmp=$total_pag;
					  echo '<script language="JavaScript" type="text/JavaScript">';
							echo 'function submitPag(num1, num2) {';
							echo 'document.formFiltro.bgn.value=num1;';
							echo 'document.formFiltro.num.value=num2;';
							echo 'document.formFiltro.submit();';
							echo '}';
							echo '</script>';
					
					if($pagina>1) echo '<a class="page_number" href="javascript:submitPag(0, '.$num.');" title="P�gina 1">1</a>&nbsp;&nbsp;...&nbsp;&nbsp;';
				  
					for($k=$pagina;$k<=$total_pag_tmp;$k++) {
						if($bgn<>$start) {
							echo '<a class="page_number" href="javascript:submitPag('.$start.', '.$num.');" title="P�gina ' . $k . '">'.$k.'</a>&nbsp;&nbsp;';
							} else {
							echo '<font class="red"><b>'.$k.'</b></font>&nbsp;&nbsp;';
						}
						
						$start+=$num;
					}
				  	if($total_pag>$total_pag_tmp) echo '&nbsp;&nbsp;...&nbsp;&nbsp;<a class="page_number" href="javascript:submitPag('.(($total_pag*10)-10).', '.$num.');" title="P�gina ' . $total_pag . '">'.$total_pag.'</a>';
				}
			  ?>
			  </td>
			</tr>
		</table>
	</td>
</tr>
</table>

</body>
</html>

