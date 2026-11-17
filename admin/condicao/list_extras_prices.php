<?
	include "settings.inc.php";
	// vamos verificar se os campos existem todos na base de dados
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	// Tratar dos dados submetidos na propria pagina
	// foreach($_POST as $varName=> $value) 
	// {
	// 	$str_id=substr($varName,strpos($varName,"_")+1);
	// 	if($str_id > 0 && substr_count($varName,"chk_") > 0)
	// 	{
	// 		$sql="DELETE from brands_offers";
	// 		$res=Runquery($db_link,$DB_name,$sql);
	// 		break;
	// 	}
	// }
	// foreach($_POST as $varName=> $value) 
	// {
	// 	$str_id=substr($varName,strpos($varName,"_")+1);
	// 	if($str_id > 0 && substr_count($varName,"chk_") > 0)
	// 	{
	// 		$var="field_".$str_id;
	// 		$dv=$$var;
	// 		$sql="INSERT INTO brands_offers(ID_Brand,Ordem) VALUES ('$str_id','$dv')";
	// 		$res=Runquery($db_link,$DB_name,$sql);
	// 		if(!$res)echo db_error($db_link).$sql."<br>";
	// 	}
	// }; 
	// print_r($_POST['price']);
	foreach ($_POST['price'] as $key => $value)
	{
		// if($value)
		$sql = "UPDATE products_extras_prices SET Price='".$value."' WHERE Tipo='".$key."' ";
		// echo '++++'.$sql;
		$res=Runquery($db_link,$DB_name,$sql);
		if(!$res)echo db_error($db_link).$sql."<br>";
		else $msg="Preços actualizados com sucesso!";
	}
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	?>
	<form action="list_extras_prices.php" method="post" name="lista_paginas" onSubmit="return checkFields()">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
		<td valign="top" align="left"><? 
			include "../layout/menus.inc.php";
			
			$rs=new DataObj;
			$sql = "SELECT * FROM products_extras_prices ORDER BY Tipo ASC";// GROUP BY bl.Nome
			$rs->db_open($db_link,$DB_name,$sql);
			$i=0;
			while(!$rs->EOF())
			{	
				$ID=$rs->db_data["ID"];
				$tipo=$rs->db_data["Tipo"];	
				$price=$rs->db_data["Price"];	

				if($i % 2 != 0)	{$cor="#def2fa";$class="r1";$n="1";}
				else				{$cor="#F6F6F6";$class="r2";$n="2";}
				$i++;
				?>
			    <table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
					<? if($msg){
						?><tr style="background: #0FD"><td></td><td></td><td></td><td></td><td height="30" style="text-align: center;"><strong><?=$msg?></strong></td></tr><?
						$msg='';
					}
					?>
					<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
				        <td width="29" align="center"><img src="../images/page.png"></td>
						<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /> </td>
					    <td width="10"></td>
					    <td width="100" valign="middle" align="left" class="preto"><?=$cfg_products_extras[$tipo]?></td>
					    <td valign="middle" align="left" class="preto">
					    	<!-- <input type="hidden" name="tipo_<?=$i?>" value="<?=$tipo?>"> -->
					    	<input type="text" name="price[<?=$tipo?>]" value="<?=$price?>" style="text-align:right; width:30px; padding:2px"> &euro;
					    </td>
					</tr>
				</table>
				<?
						
				$rs->db_next();
			}
			$rs->db_close();
			?>
			<table class="p1" align="center" border=0 cellpadding=0 cellspacing=0 width="100%">
			<tr>
	            <td width="65"></td>
				<td valign="middle" align="left"><input type="submit"  name="submeter" value="Atualizar preços" class="botao" style="width:120px;height:20px"></td>
			</tr>
			</table>
		</td>
		<td width="10px">&nbsp;</td>
	</tr>
	</table>
	</form>    
<? 	include "../layout/bottom.inc.php";?>