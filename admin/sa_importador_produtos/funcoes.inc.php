<?php

function getImportadorConfiguracoes()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);	
	$obj = new DataObj;
	$sql2 = '
			SELECT
				*
			FROM
				a_importador_configuracoes
			';
			// echo $sql2;
	$obj->db_open($db_link,$DB_name,$sql2);
	
	return $obj;
}
	/* if(!$obj->EOF())
	{
		while(!$obj->EOF())
		{
			$Marca = stripslashes($obj->db_data['Marca']);
			$MarcaDesc = stripslashes($obj->db_data['MarcaDesc']);
			$Categoria = stripslashes($obj->db_data['Categoria']);
			$CategoriaDesc = stripslashes($obj->db_data['CategoriaDesc']);
			$Sub_Categoria = stripslashes($obj->db_data['Sub_Categoria']);
			$Sub_CategoriaDesc = stripslashes($obj->db_data['Sub_CategoriaDesc']);
			$Sub_Categoria2 = stripslashes($obj->db_data['Sub_Categoria2']);
			$Sub_Categoria2Desc = stripslashes($obj->db_data['Sub_Categoria2Desc']);
			$Codigo_produto = stripslashes($obj->db_data['Codigo_produto']);
			$Produto = stripslashes($obj->db_data['Produto']);
			$Descricao = stripslashes($obj->db_data['Descricao']);
			$Preco_IVA = stripslashes($obj->db_data['Preco_IVA']);
			$EAN = stripslashes($obj->db_data['EAN']);
			$Stock = stripslashes($obj->db_data['Stock']);
			$Imagem1 = stripslashes($obj->db_data['Imagem1']);
			$Imagem2 = stripslashes($obj->db_data['Imagem2']);
			$Imagem3 = stripslashes($obj->db_data['Imagem3']);
			$Imagem4 = stripslashes($obj->db_data['Imagem4']);
			$Separador = stripslashes($obj->db_data['Separador']);
			$Separador2 = htmlentities(stripslashes($obj->db_data['Separador2']));
			$conf1 = $obj->db_data['conf1'];
			$conf2 = $obj->db_data['conf2'];
			$conf3 = $obj->db_data['conf3'];
			
			$Preco_conf = $obj->db_data['Preco_conf'];
			$Preco_iva_valor = $obj->db_data['Preco_iva_valor'];
			
			$Imagem_conf = $obj->db_data['Imagem_conf'];
			$Imagem_pasta = $obj->db_data['Imagem_pasta'];
			
			$Peso = $obj->db_data['Peso'];

			$obj->db_next();
		}
	}
	$obj->db_close();
	
	return $obj;
}
/* 

function cleanField($field){
	return rtrim(ltrim($field));
}
function checkEncode($field)
{
	return mb_convert_encoding($field, 'UTF-8');
}
function isnumeric($v)
{
	return is_numeric(str_replace(',','.',str_replace('.','',$v)));  
}
function tonumeric($v)
{
	return str_replace(',','.',str_replace('.','',$v));  
}
function showMessage($msg,$tipo=0)
{
	if($tipo==1) return '<span style="color:#F00">'.$msg.'</span>';
	if($tipo==2) return '<span style="color:#339933">'.$msg.'</span>';
	else return '<span>'.$msg.'</span>';
}
function insereProdutos($prods)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs3=new DataObj;
	$rs4=new DataObj;
	for ($i=0; $i < count($prods); $i++)
	{
		$rs->db_open($db_link,$DB_name,'SELECT ID from products WHERE Ref="'.$prods[$i][IMPORT_POS_REF].'"');
		if($rs->EOF())
		{
			echo '<br><br>PRODUTO NÃO EXISTE --> "'.$prods[$i][IMPORT_POS_REF].'"<br>';
			$Peso=0;
			$Categoria=0;
			$Familia=0;
			
			echo '|- Peso --> "'.$prods[$i][IMPORT_POS_PESO].'"<br>';
			$Peso=$prods[$i][IMPORT_POS_PESO];
			$sql='INSERT INTO products(Ref,Aproved,Peso) VALUES ("'.$prods[$i][IMPORT_POS_REF].'",0,"'.$Peso.'")';
			$res=Runquery($db_link,$DB_name,$sql);
			if($res)
			{
				$prod_id=db_get_last_insert_id($db_link);
				echo '|- NOME --> "'.$prods[$i][IMPORT_POS_NOME].'"<br>';
				$sql='INSERT INTO products_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$prod_id.'","'.$prods[$i][IMPORT_POS_NOME].'","pt")';
				$res=Runquery($db_link,$DB_name,$sql);
				if($res)echo showMessage('|- INSERIDO o Nome<br>',2);
				else echo showMessage('Erro a inserir o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);

				echo '|- OBTER FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>';
//				$rs3->db_open($db_link,$DB_name,'SELECT ID_PAI from products_families_lang WHERE Cabecalho="'.$prods[$i][IMPORT_POS_FAMILIA].'" and Lang="pt"');
				$rs3->db_open($db_link,$DB_name,'SELECT ID as ID_PAI from products_families WHERE ID="'.$prods[$i][IMPORT_POS_FAMILIA].'"');
				if(!$rs3->EOF())
				{
					$Familia=$rs3->db_data['ID_PAI'];
					echo showMessage('|- FAMILIA IDº "'.$Familia.'"<br>',2);
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_families_obj(ID_PROD,ID_ORG) VALUES ("'.$prod_id.'","'.$Familia.'")');
					if($res) echo showMessage('|- INSERIDA A FAMILIA DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				else echo showMessage('|- FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'" Não encontrada<br>',1);
				$rs3->db_close();

				$res=Runquery($db_link,$DB_name,'INSERT INTO products_objs(obj_type,obj_type_id,obj_ativo) VALUES ("products","'.$prod_id.'",1)');
				if($res)
				{
					$obj_id=db_get_last_insert_id($db_link);
					echo showMessage('|- INSERIDA A LINHA BASE OBJ DO PRODUTO <br>',2);
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_objs_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$obj_id.'","'.$prods[$i][IMPORT_POS_REF].'","pt")');
					if($res) echo showMessage('|- INSERIDA A LINHA BASE OBJ LANG DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a linha base obj lang do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
					

				}
				else echo showMessage('Erro a inserir a linha base obj do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			else
			{
				echo showMessage('Erro a inserir o produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				continue;
			}
		}
		else
		{
			$prod_id=$rs->db_data['ID'];
			echo '<br><br>PRODUTO JÁ EXISTE --> "'.$prods[$i][IMPORT_POS_REF].'" -- ID: "'.$prod_id.'"<br>';
		}
		//Agora que ja temos o produto inserido vamos fazer algumas atualizacoes aos seus dados
		$rs2->db_open($db_link,$DB_name,'SELECT obj_id,obj_type_id from products_objs WHERE obj_type_id="'.$rs->db_data['ID'].'"');
		$obj_id=$rs2->db_data['obj_id'];
		$rs2->db_close();
		
		$ID_Marca=0;
		$Peso=0;
		$Categoria=0;
		$Familia=0;
		

		if(isset($_POST['imp_nome']))
		{
			echo '|- ATUALIZAR NOME PRODUTO PARA "'.$prods[$i][IMPORT_POS_NOME].'"<br>';
			$rs2->db_open($db_link,$DB_name,'SELECT * from products_lang WHERE ID_PAI="'.$prod_id.'" and Lang="pt"');
			if(!$rs2->EOF())
			{
				$res=Runquery($db_link,$DB_name,'UPDATE products_lang SET Cabecalho="'.$prods[$i][IMPORT_POS_NOME].'" WHERE ID_PAI="'.$prod_id.'" AND Lang="pt"');
				if($res)echo showMessage('|- ATUALIZADO o Nome do produto para "'.$prods[$i][IMPORT_POS_NOME].'"<br>',2);
				else echo showMessage('Erro a ATUALIZAR o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			else
			{
				$res=Runquery($db_link,$DB_name,'INSERT INTO products_lang(ID_PAI,Cabecalho,Lang) VALUES ("'.$prod_id.'","'.$prods[$i][IMPORT_POS_NOME].'","pt")');
				if($res)echo showMessage('|- INSERIDO o Nome do produto <br>',2);
				else echo showMessage('Erro a inserir o Nome do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
			}
			$rs2->db_close();
		}
		if(isset($_POST['imp_peso']))
		{
			echo '|- ATUALIZAR PESO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PESO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Peso="'.$prods[$i][IMPORT_POS_PESO].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o peso do produto para "'.$prods[$i][IMPORT_POS_PESO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o peso do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
		}
		if(isset($_POST['imp_carrinho']))
		{
			echo '|- ATUALIZAR FLAG CARRINHO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET destaque="'.$prods[$i][IMPORT_POS_CARRINHO].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADA a flag carrinho do produto para "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a flag carrinho do produto --> "'.$prods[$i][IMPORT_POS_CARRINHO].'"<br>',1);
		}
		if(isset($_POST['imp_ordem']))
		{
			echo '|- ATUALIZAR ORDEM DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Ordem="'.$prods[$i][IMPORT_POS_ORDEM].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO a ordem do produto para "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a ordem do produto --> "'.$prods[$i][IMPORT_POS_ORDEM].'"<br>',1);
		}
		if(isset($_POST['imp_sinonimos']))
		{
			echo '|- ATUALIZAR SINONIMOS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Hottags="'.$prods[$i][IMPORT_POS_SINONIMOS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS os sinonimos do produto para "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR os sinonimos do produto --> "'.$prods[$i][IMPORT_POS_SINONIMOS].'"<br>',1);
		}
		if(isset($_POST['imp_medidas']))
		{
			echo '|- ATUALIZAR AS MEDIDAS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Medidas="'.$prods[$i][IMPORT_POS_MEDIDAS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS as medidas do produto para "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR as medidas do produto --> "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',1);
		}
		if(isset($_POST['imp_medidas']))
		{
			echo '|- ATUALIZAR AS MEDIDAS DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET destaque="'.$prods[$i][IMPORT_POS_MEDIDAS].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADOS as medidas do produto para "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR as medidas do produto --> "'.$prods[$i][IMPORT_POS_MEDIDAS].'"<br>',1);
		}
		if(isset($_POST['imp_destaque']))
		{
			echo '|- ATUALIZAR A OPCAO DESTAQUE DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Home="'.$prods[$i][IMPORT_POS_DESTAQUE].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o destaque do produto para "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o destaque do produto --> "'.$prods[$i][IMPORT_POS_DESTAQUE].'"<br>',1);
		}
		
		if(isset($_POST['imp_modelo']))
		{
			echo '|- ATUALIZAR O MODELO DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_MODELO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs_lang SET Cabecalho="'.$prods[$i][IMPORT_POS_MODELO].'" WHERE ID_PAI="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o modelo do produto para "'.$prods[$i][IMPORT_POS_MODELO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o modelo do produto --> "'.$prods[$i][IMPORT_POS_MODELO].'"<br>',1);
		}
		if(isset($_POST['imp_pvp']))
		{
			echo '|- ATUALIZAR O PVP DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PVP].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_pvp="'.$prods[$i][IMPORT_POS_PVP].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o pvp do produto para "'.$prods[$i][IMPORT_POS_PVP].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o pvp do produto --> "'.$prods[$i][IMPORT_POS_PVP].'"<br>',1);
		}
		if(isset($_POST['imp_pvp_promo']))
		{
			echo '|- ATUALIZAR O pvp promo DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_promo="'.$prods[$i][IMPORT_POS_PVP_PROMO].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o pvp promo do produto para "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o pvp promo do produto --> "'.$prods[$i][IMPORT_POS_PVP_PROMO].'"<br>',1);
		}
		if(isset($_POST['imp_promo']))
		{
			echo '|- ATUALIZAR O campo promo DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_PROMO].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_promo_ativo="'.$prods[$i][IMPORT_POS_PROMO].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o campo promo do produto para "'.$prods[$i][IMPORT_POS_PROMO].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o campo promo do produto --> "'.$prods[$i][IMPORT_POS_PROMO].'"<br>',1);
		}
		if(isset($_POST['imp_stock']))
		{
			echo '|- ATUALIZAR O STOCK DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_STOCK].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products_objs SET obj_stock="'.$prods[$i][IMPORT_POS_STOCK].'" WHERE obj_id="'.$obj_id.'"');
			if($res)echo showMessage('|- ATUALIZADO o stock do produto para "'.$prods[$i][IMPORT_POS_STOCK].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR o stock do produto --> "'.$prods[$i][IMPORT_POS_STOCK].'"<br>',1);
		}
		
		if(isset($_POST['imp_visivel']))
		{
			echo '|- ATUALIZAR A OPCAO VISIVEL DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>';
			$res=Runquery($db_link,$DB_name,'UPDATE products SET Aproved="'.$prods[$i][IMPORT_POS_VISIVEL].'" WHERE ID="'.$prod_id.'"');
			if($res)echo showMessage('|- ATUALIZADO a opcao visivel do produto para "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>',2);
			else echo showMessage('Erro a ATUALIZAR a opcao visivel  do produto --> "'.$prods[$i][IMPORT_POS_VISIVEL].'"<br>',1);
		}
		if(isset($_POST['imp_familia']))
		{
			echo '|- ATUALIZAR FAMILIA DO PRODUTO PARA "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>';
			$rs2->db_open($db_link,$DB_name,'SELECT ID as ID_PAI from products_families WHERE ID="'.$prods[$i][IMPORT_POS_FAMILIA].'"');
			//$rs2->db_open($db_link,$DB_name,'SELECT ID_PAI from products_families_lang WHERE Cabecalho="'.$prods[$i][IMPORT_POS_FAMILIA].'" and Lang="pt"');
			if(!$rs2->EOF())
			{
				$Familia=$rs2->db_data['ID_PAI'];
				echo showMessage('|- FAMILIA IDº "'.$Familia.'"<br>',2);
				$rs4->db_open($db_link,$DB_name,'SELECT ID_ORG from products_families_obj where ID_PROD="'.$prod_id.'"');
				if(!$rs4->EOF())
				{
					$res=Runquery($db_link,$DB_name,'UPDATE products_families_obj SET ID_ORG="'.$Familia.'" WHERE ID_PROD="'.$prod_id.'"');
					if($res) echo showMessage('|- ATUALIZADA A FAMILIA DO PRODUTO  para "'.$prods[$i][IMPORT_POS_FAMILIA].'"<br>',2);
					else echo showMessage('Erro a atualizar a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				else
				{
					$res=Runquery($db_link,$DB_name,'INSERT INTO products_families_obj(ID_PROD,ID_ORG) VALUES ("'.$prod_id.'","'.$Familia.'")');
					if($res)echo showMessage('|- INSERIDA A FAMILIA DO PRODUTO <br>',2);
					else echo showMessage('Erro a inserir a familia do produto --> "'.$prods[$i][IMPORT_POS_REF].'"<br>',1);
				}
				$rs4->db_close();
			}
			else echo showMessage('|- FAMILIA --> "'.$prods[$i][IMPORT_POS_FAMILIA].'" Não encontrada<br>',1);
			$rs3->db_close();
		}
		$rs->db_close();
	}
}
function show_fast_tools()
{
	global $ID_ORG,$ID;?>
	<table width="200px" border=0 cellspacing=0 cellpadding=0>
	<tr> 
		<td height="27px">&nbsp;</td>
	</tr>
    </table>
	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class="titulo2" align="center">FAST TOOLS</td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
    <tr> 
		<td width="5px" align="left"><img src="../images/s2.jpg"></td>
		<td class=preto align="center"><input class="botao" type="button" style="width:160px;height:20px" value="Ficheiro Exemplo" onclick="javascript:window.open('exemplo.csv','_new')"></td>
		<td width="5px" align="right"><img src="../images/s2.jpg"></td>
	</tr>
	</table>
	<img src="../images/bsh.png" width="200px" height="30px">
<? } */
?>