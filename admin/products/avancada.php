<?php

	header ('Content-type:text/html; charset=UTF-8');
	include "settings.inc.php";
	require_once $path . '../admin/products/filtros/funcoes.inc.php';

	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(trim($total_prods=="")) $total_prods=10;
	if(trim($from=="")) $from=0;
	if(trim($porpagina=="")) $porpagina=$total_prods;

	$array_estado[] = 'Todos';
	$array_estado[] = 'Destaque';
	$array_estado[] = 'Promoção';
	$array_estado[] = 'Travel Size';
	$array_estado[] = 'Novidade';
	$array_estado[] = 'Carrinho';
	$array_estado[] = 'Visiveis';
	$array_estado[] = 'Invisiveis';
	$array_estado[] = 'Sync Auto off';
	// $array_estado[] = 'Outlet';

	$dados=new DataObj;
	$dados2=new DataObj;
	//ORDENAÇÃO
	if(!isset($_GET["tipoorder"]) || !isset($_GET["campo"]) || $_GET["tipoorder"]=="" || $_GET["campo"]=="") $order=" Ordem, nome_produto Asc";
	else $order=" " . base64_decode($campo) . " " . $tipoorder;
	if(isset($_GET["tipoorder"]) && $_GET["tipoorder"]=="ASC")$tipoorder="DESC";
	else $tipoorder="ASC";

	$flag=$_GET["flag"];
	// Reordenar
	if($flag==2)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"field_") > 0)
			{
				$sql="UPDATE products SET Ordem='$dv' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,1,'');
			}
		};
	}
	// apagar
	if($flag==1)
	{
		$from=0;
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				ApagarProduto($str_id);
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,0,'');
			}
		};
	}
	// mover
	if($flag==3)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$rs3 = new DataObj;
				$rs3->db_open($db_link,$DB_name,'Select ID_Marca from products WHERE ID="'.$str_id.'"');
				if($rs3->EOF())$marca=$rs3->db_data['ID_Marca'];
				$rs3->db_close();

				$sql="UPDATE products SET ID_ORG='$m_familia2' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				$sql="UPDATE products_families_obj SET ID_ORG='$m_familia2' WHERE ID_PROD=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);

				if (!$res)  echo db_error($db_link).$sql."<br>";
				products_families_save($str_id, $m_familia2);
		        //gera_produtos($ID_PROD,$m_familia,0,0,'');

				$fams = encontraFamiliasRecursiva_x($m_familia2);
				if(isset($fams[0])) $fam_id1 = $fams[0]; else $fam_id1 = $m_familia2;
				if(isset($fams[1])) $fam_id2 = $fams[1]; else $fam_id2 = 0;
				if(isset($fams[2])) $fam_id3 = $fams[2]; else $fam_id3 = 0;
				if(isset($fams[3])) $fam_id4 = $fams[3]; else $fam_id4 = 0;

				// echo '<br>'.$id_org;
				// echo '<br>'.$fam_id4.'|'.$fam_id3.'|'.$fam_id2.'|'.$fam_id1;

				$rs3->db_open($db_link,$DB_name,'Select prod_id from filters_products WHERE prod_id="'.$str_id.'"');
				if($rs3->EOF())
				{
					$sql = 'INSERT INTO filters_products(cat_id, valor_id, prod_id, fam_id1, fam_id2, fam_id3, fam_id4, marca_id, filtros_rel2, FLAG, filtro_active)
							VALUES("0", "0", "'.$str_id.'", "'.$fam_id1.'", "'.$fam_id2.'", "'.$fam_id3.'", "'.$fam_id4.'", "'.$marca.'", "", 1, 1)';
					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
				else
				{
					$sql ='UPDATE filters_products SET fam_id1="'.$fam_id1.'", fam_id2="'.$fam_id2.'", fam_id3="'.$fam_id3.'", fam_id4="'.$fam_id4.'", marca_id="'.$marca.'" where prod_id = "'.$str_id.'"';
					$res = Runquery($db_link, $GLOBALS['DB_name'], $sql);
				}
			}
		};
	}
	// activar
	if($flag==4)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Aproved=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,1,'');
			}
		};
	}

	// desactivar
	if($flag==5)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Aproved=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";

				//Atualiza a cache dos artigos
				del_produtc_info($str_id,'',1,0,$admin_lang,1);
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,1,'');
			}
		};
	}
	// mostrar web
	if($flag==6)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET mostrar_online=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// esconder web
	if($flag==7)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET mostrar_online=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// activar carrinho
	if($flag==10)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Carrinho=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,1,'');
			}
		};
	}
	// desactivar carrinho
	if($flag==11)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Carrinho=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,1,'');
			}
		};
	}


	// ativar destaque
	if($flag==20)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_destaque=1 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar destaque
	if($flag==21)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_destaque=0 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}

	// ativar outlet
	if($flag==22)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_outlet=1 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar outlet
	if($flag==23)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_outlet=0 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}

	// ativar promocao
	if($flag==24)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_promo_ativo=1 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar promocao
	if($flag==25)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_promo_ativo=0 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}

	// ativar novo
	if($flag==26)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_novo=1 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar novo
	if($flag==27)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_novo=0 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}


	// ativar mais vendido
	if($flag==28)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_dia=1 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar mais vendido
	if($flag==29)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products_objs SET obj_dia=0 WHERE obj_type_id=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}


	// ativar sync
	if($flag==30)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Sync=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar sync
	if($flag==31)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET Sync=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}



	// ativar encomenda sem stock
	if($flag==32)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET encomenda_sem_stock=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// desativar encomenda sem stock
	if($flag==33)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET encomenda_sem_stock=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	


	//----------------------------
	if($flag==14)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE products SET ID_Marca='$sel_marca2' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
				//function gera_produtos($prod_id=0, $fam=0, $marca=0, $auto=0, $lingua = '')
		        //gera_produtos($ID_PROD,0,0,0,'');
			}
		};
	}

	include '../layout/header.inc.php';
	include '../layout/body.inc.php';
	?>
	<style>
		.barra_operacoes{background-color:#999999}
		.preto{cursor:pointer}
		div.small{border:1px solid #ccd0d4;display:block;height:34px;line-height:34px}
		div.small > img{vertical-align:middle}
		input.small{border:1px solid #999;display:block;height:25px}
		input.small:focus{border:1px solid #5897fb;display:block;height:25px}
		input.search_bar{font-size:10px;margin:1px;border:1px solid #ccd0d4;box-shadow:none;line-height:1.42857143;padding:6px 12px;height:34px;background-color:#fff}
		td{font-size:10px}
		td a{font-size:10px}
		TD.preto {font-size:10px}
	</style>
	<link rel="stylesheet" type="text/css" href="avancada/chosen2.css" />
	<link rel="stylesheet" type="text/css" href="avancada/sa.css" />
	<script>
	function call_associated()
	{
		var a = document.getElementById("flag");
		if(a)
		{
			if(a.value==3)
			{
				document.getElementById("c_assoc_3").style.display="block";
				document.getElementById("c_assoc_14").style.display="none";
			}
			else if(a.value==14)
			{
				document.getElementById("c_assoc_3").style.display="none";
				document.getElementById("c_assoc_14").style.display="block";
			}
			else
			{
				document.getElementById("c_assoc_3").style.display="none";
				document.getElementById("c_assoc_14").style.display="none";
			}
		}
	}
	function checkFields()
	{
		var a = document.getElementById("flag");
		if(a)
		{
			if(a.value==1)
			{
				var r = confirm('Pretende remover os produtos seleccionados?');
				if(r == true) return true;
				return false;
			}
		}
		return true;
	}
	function abrir2(tipoorder, campo)
	{
		document.getElementById('tipoorder').value = tipoorder;
		document.getElementById('campo').value = campo;
		document.lista_produtos.submit();
	}
	function submit_fileds(pagina, porpagina, campo)
	{
		if (typeof campo == 'undefined') campo = '';
		if (typeof pagina == 'undefined') pagina = '';
		if (typeof porpagina == 'undefined') porpagina = '';

		var fields='';
		$('.formfields').each(function()
		{
			if($(this).find('input[type=text]').attr('name')!=undefined)
			{
				var name = $(this).find('input[type=text]').attr('name');
				var value = $(this).find('input[type=text]').val();

				fields += '&'+name+'='+escape(value);
			}
		});
		$('.formfields').each(function()
		{
			if($(this).find('select').attr('name')!=undefined)
			{
				var name = $(this).find('select').attr('name');
				var value = $(this).find('select').val();
				fields += '&'+name+'='+escape(value);
			}
		});
		$('.formfields').each(function()
		{
			if($(this).find('input[type=checkbox]').attr('name')!=undefined)
			{
				var name = $(this).find('input[type=checkbox]').attr('name');
				var isched = $(this).find('input[type=checkbox]:checked').val();
				if(isched) fields += '&'+name+'=1'
			}
		});


		if(pagina > 0 && porpagina > 0) window.open('avancada.php?campo='+campo+'&pagina='+pagina+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' :'ASC';?>'+fields+'&show_results=1','_self');
		else if(pagina > 0) window.open('avancada.php?campo='+campo+'&pagina='+pagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' :'ASC';?>'+fields+'&show_results=1','_self');
		else if(porpagina > 0) window.open('avancada.php?campo='+campo+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' :'ASC';?>'+fields+'&show_results=1','_self');
		else window.open('avancada.php?campo='+campo+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' :'ASC';?>'+fields+'&show_results=1','_self');

	}

	$(document).ready(function() {$('.fancybox_img').fancybox();});
	</script>

    <form name="lista_produtos" method="get" onSubmit="return checkFields()">
   	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
        <td valign="top" align="left"><?php	include '../layout/menus.inc.php'; ?>

		    <div class="panel panel-inverse overflow-hidden m-b-12">
		        <div class="panel-heading">
		            <h4 style="color:#fff"><input class="btn btn-primary" type="button" style="width:160px" value="Criar Novo Produto" onclick="javascript:window.open('products.php?ID=0&amp;ID_ORG=&amp;from_prods=&amp;to_prods=&amp;total_prods=','_self')"></h4>
		        </div>
		    </div>

			<table class="barra_operacoes" width="100%" border="0" cellspacing="0" cellpadding="2" style="background:#FFF">
			<tr>
				<td	class="preto" width="29" align="center">&nbsp;</td>
				<td	class="preto" width="29" align="center" title="Visivel" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("Aproved")?>')" ><img alt="produtos activos" src="ok.png" border="0" align="absmiddle" /></td>
				<td	class="preto" width="29" align="center" title="Listar produtos com foto" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("img_ext")?>')" >Foto</td>
				<td	class="preto" width="50" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("ID")?>')" >ID</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products_objs_lang.Cabecalho")?>')" >REF</td>
				<td	class="preto" width="100" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("ID_Marca")?>')" >MARCA</td>
				<td	class="preto" width="200px" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("ID_ORG")?>')" >FAMILIA</td>
				<td	class="preto" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("nome_produto")?>')" >DESIGNAÇÃO</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products_objs.obj_Supplier_ID")?>')" >SUPPLIER</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products_objs.obj_pvp")?>')" >PVP</td>
				<!--<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products_objs.obj_iva")?>')" >IVA</td>-->
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products.Ref_Ean")?>')" >EAN</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("products_objs.obj_stock")?>')" >STOCK</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?=$_GET['pagina']?>', '<?=$_GET['total_prods']?>','<?php echo base64_encode("Peso")?>')">PESO</td>
				<td	class="preto" width="100" align="center" style="cursor:default">Estado</td>
				<td	class="preto" width="29" align="center">&nbsp;</td>
			</tr>
			<tr>
				<td width="29" align="center" valign="top" class="formfields">
					<input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1" <?php if($filter) echo "checked"?>>
				</td>
				<td width="29" align="center" valign="top" class="formfields">
					<!--input type="checkbox" name="visivel" id="visivel" value="1" <?php if($visivel) echo "checked"?>-->
				</td>
				<td width="29" align="center" valign="top" class="formfields">
					<input type="checkbox" name="fotos" id="fotos" value="1" <?php if($fotos) echo "checked";?> >
				</td>
				<td width="50" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;padding-left:6px; padding-right:6px;" class="search_bar" type="text" name="pesq_id" id="pesq_id" value='<?php echo stripslashes($pesq_id)?>' class="small" />
				</td>
				<td width="80" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_ref" id="pesq_ref" value="<?=$pesq_ref?>" class="small">
				</td>
				<td width="100" align="center" valign="top" class="formfields">
					<?php echo marcas_select("sel_marca", $sel_marca,"150px","");?>
				</td>
				<td width="200" align="center" valign="top" class="formfields">
					<?php gera_lista_familiasv(0,'products_families','products_families_lang', '200px', $_GET['m_familia']);?>
				</td>
				<td valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_desig" id="pesq_desig" value="<?=$pesq_desig?>" class="small" />
				</td>
				<td width="80" align="center" valign="top" class="formfields">
					<?php echo supplier_select("pesq_suplier", $pesq_suplier,"150px","");?>
				</td>
				<td width="80" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_preco" id="pesq_preco" value="<?=$pesq_preco?>" class="small" />
				</td>
				<!--<td width="80" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_iva" id="pesq_iva" value="<?=$pesq_iva?>" class="small" />
				</td>-->
				<td width="80" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_ean" id="pesq_ean" value="<?=$pesq_ean?>" class="small" />
				</td>
				<td width="50" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;"class="search_bar" type="text" name="pesq_stock" id="pesq_stock" value="<?=$pesq_stock?>" class="small" />
				</td>
				<td width="50" align="center" valign="top" class="formfields">
					<input style="min-width:50px;width:90%;" class="search_bar" type="text" name="pesq_peso" id="pesq_peso" value="<?=$pesq_peso?>" class="small" />
				</td>
				<td width="100" align="center" valign="top" class="formfields"><?php estados_select('sel_estado', $_GET['sel_estado'], '90%', '');?></td>
				<td width="29" align="center" style="vertical-align:top" class="formfields">
					<div class="small" onclick="submit_fileds();" style="cursor:pointer">
						<img src="lupa2.png" border="0" title="Pesquisar" alt="Pesquisar"  />
					</div>
				</td>
			</tr>
            <?php

		if($_GET['show_results']==1)
        {
            //**************************OBTER OS FILTROS***********************************************
            if($visivel) $where.=" AND Aproved=1 ";

            $aux=getfiltros($pesq_id,"", "products.ID", "num");
            if($aux!="") $where.=" AND " . $aux;

			$aux=getfiltros($pesq_peso,"", "products.Peso", "num");
            if($aux!="") $where.=" AND " . $aux;

            //$aux=getfiltros($pesq_ref,"","products_objs_lang.Cabecalho", "s");
            $aux=getfiltros($pesq_ref,"","products.Ref", "s");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($sel_marca,"", "ID_Marca", "ex");
            if($aux!="") $where.=" AND " . $aux;

            // $aux=getfiltros($pesq_fam,"", "products_families_lang.Cabecalho", "s");
			// $aux=getfiltros($m_familia,"", "products_families_lang.ID_PAI", "num");
            if(isset($_GET['m_familia']) && $_GET['m_familia'] > 0) $where.=" AND (products_families_obj.ID_ORG='".$_GET['m_familia']."')";
            if(isset($_GET['m_familia']) && $_GET['m_familia'] == -1) $where.=" AND (products_families_obj.ID_ORG='0')";

            $aux=getfiltros($pesq_desig,"", "products_lang.Cabecalho", "s");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($sel_unvenda,"", "unidade_venda", "ex");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_preco,"", "products_objs.obj_pvp", "num");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_iva,"", "products_objs.obj_iva", "num");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_suplier,"", "products_objs.obj_Supplier_ID", "ex");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_ean,"", "products.Ref_Ean", "ex");
            if($aux!="") $where.=" AND " . $aux;

            // if($visivelweb!="") $where.=" AND Aproved=1";

            if($sel_estado==5) $where.=" AND Carrinho=1";
			elseif($sel_estado==1) $where.=" AND products_objs.obj_destaque=1";
			elseif($sel_estado==2) $where.=" AND (products_objs.obj_promo_ativo=1 and products_objs.obj_promo > 0 && products_objs.obj_promo<>products_objs.obj_pvp)";
			elseif($sel_estado==3) $where.=" AND (products_objs.obj_travelsize=1)";
			elseif($sel_estado==4) $where.=" AND (products_objs.obj_novo=1)";
			elseif($sel_estado==6) $where.=" AND (products.Aproved=1)";
			elseif($sel_estado==7) $where.=" AND (products.Aproved=0)";
			elseif($sel_estado==8) $where.=" AND (products.Sync=1)";

			if(isset($_GET['fotos']) && $_GET['fotos'] > 0)
			{
				$join = ' INNER JOIN products_images ON products_images.ID_ORG=products.ID';
				$where2 = ' AND products_images.img_ext<>"" ';
			}
			else
			{
				$join = '';
				$where2 = '';
			}
			    // echo 'where2:'.$where2;
            if($pesq_stock)
            {
                $aux=getfiltros($pesq_stock,"", "products_objs.obj_stock", "num");
                if($aux!="") $where.=" AND " . $aux;
            }
            /******************************************************************************************/


			$sql = '
					SELECT
						products.*, products_lang.Cabecalho as nome_produto,
						products.Carrinho as est_carrinho, products_objs.obj_destaque as est_dest,
						if(products_objs.obj_promo_ativo=1 and products_objs.obj_promo > 0 && products_objs.obj_promo<>products_objs.obj_pvp, 1, 0) as est_prom,
						products_objs.obj_travelsize as est_travel, products_objs.obj_novo as est_novo,
						products_families_lang.Cabecalho as nome_familia,
						products.Ref As Ref,products_objs.obj_pvp as PrecoCusto,products_objs.obj_iva as iva,products_objs.obj_Supplier_ID,
						products_objs.obj_stock as Stock,
						products_families_obj.ID_ORG as fam_id2
					FROM
						products
						LEFT OUTER JOIN products_lang ON products_lang.ID_PAI=products.ID AND products_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN products_families_obj ON products_families_obj.ID_PROD=products.ID
						LEFT OUTER JOIN products_families_lang ON products_families_lang.ID_PAI=products_families_obj.ID_ORG AND products_families_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN products_objs ON products_objs.obj_type_id=products.ID
					';

					if($join) $sql .= $join;
		            $sql .= ' WHERE 1=1' . $where.$where2;
		            if($order) $sql .= ' GROUP BY products.ID';
		            if($order) $sql .= ' ORDER BY '.$order;
					if(isset($_GET['log']) && $_GET['log'] > 0) echo '<span style="color:#FFF">'.$sql.'</span>';

			$dados=new DataObj;
			$dados->db_open($db_link,$DB_name,$sql);
			$total = $dados->db_rows;
			$dados->db_close();


			$n_pags = ceil($total / $total_prods);
			if(isset($_GET['pagina']) && $_GET['pagina'] > 0) $pagina = $_GET['pagina'];
			else $pagina = 1;

			$inicio = $pagina - 1;
			$inicio = $inicio * $total_prods;
			// echo $pagina;

			$sql2 = $sql.' LIMIT '.$inicio.', '.$total_prods;

			$dados = new DataObj;


			$dados->db_open($db_link,$DB_name,$sql2);

			$count=0;

            if(!$dados->EOF())
			{
				while(!$dados->EOF())
				{
					$prod_id = $dados->db_data["ID"];
					$ordem = $dados->db_data["Ordem"];
					$ref = $dados->db_data["Ref"];
					$ean = $dados->db_data["Ref_Ean"];
					$aproved = $dados->db_data["Aproved"];
					$Stock = (isset($dados->db_data["Stock"]) && $dados->db_data["Stock"] > 0) ? $dados->db_data["Stock"] :0;

					// IMAGENS DO PRODUTO
					$arr_imagens = get_product_images($prod_id); $arr_img = array();
					if(count($arr_imagens)>0)
					{
						for ($i=0; $i < count($arr_imagens); $i++)
						{
							$ext = $arr_imagens[$i]['img_ext'];
							$ordem2 = $arr_imagens[$i]['Ordem'];
							$img_file = 'files/products/'.$prod_id.'_'.$ordem2.'.'.$ext;
							$arr_img[] = $GLOBALS['site_http_url'].$img_file;
						}
					}

					// MARCA DO PRODUTO
					$marca = $dados->db_data["ID_Marca"];
					$sql = 'SELECT Nome FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI WHERE brands.ID="'.$marca.'"';
					$dados2->db_open($db_link,$DB_name,$sql);
					if(!$dados2->EOF()) $marca = $dados2->db_data["Nome"];	else $marca = '-';
					$dados2->db_close();

					// FAMILIAS DO PRODUTO
					$familia = $dados->db_data["fam_id2"];
					$familia = rtrim(show_breadcrums_recursivo_on($familia),'-');

					//nome_familia

					$Peso = $dados->db_data["Peso"];
					if(!$Peso)$Peso = $dados->db_data["peso_cliente"];

					$precoCusto=$dados->db_data["PrecoCusto"];
					$iva=$dados->db_data["iva"];
					$cabecalho=$dados->db_data["nome_produto"];

					$obj_Supplier_ID=$dados->db_data["obj_Supplier_ID"];

					$state = '';
					if($dados->db_data["est_carrinho"]==1) $state .= '<img src="avancada/carrinho.gif" width="16" title="Pode adicionar ao carrinho" border="0" align="absmiddle" />';
					if($dados->db_data["est_dest"]==1) $state .= '<img src="avancada/destaque.png" border="0" title="Em destaque na homepage" align="absmiddle" />';
					if($dados->db_data["est_prom"]==1) $state .= '<img src="avancada/promo.png" border="0" title="Em promoção" align="absmiddle" />';
					if($dados->db_data["est_travel"]==1) $state .= '<img src="avancada/travel.png" border="0" title="Pode ser expedido para fora de Portugal" align="absmiddle" />';
					if($dados->db_data["est_novo"]==1) $state .= '<img src="avancada/novidade.png" border="0" title="Novidade" align="absmiddle" />';
					if($dados->db_data["Sync"]==1) $state .= '<img src="avancada/sync_off.png" border="0" title="Sync OFF" align="absmiddle" />';

					if($count % 2 != 0)	{$cor = '#F6F6F6'; $n = '1';}
					else				{$cor = '#def2fa'; $n = '2';}
					?>
					<tr style="background:<?=$cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
						<td width="29" height="30" align="center"><input type="checkbox" name="chk_<?=$prod_id?>" id="chk_<?=$prod_id?>" value="<?=$prod_id?>" class="cb" /></td>
						<td width="29" align="center"><?php if($aproved) echo "<img src='ok.png' border='0' align='absmiddle' title='Produto Activo' />"; else echo "-";?></td>
						<td class="texto" width="29" align="center"><?php 

						if(isset($_GET['fotos']) && $_GET['fotos'] == 1)
						{
							if($arr_img[0]) echo '<img style="max-width:50px" src="'.$arr_img[0].'">'; else echo '&nbsp;'; 
						}
						else
						{
							if($arr_img[0]) echo '<a href="'.$arr_img[0].'" class="fancybox_img"><img src="photo.png" border="0" align="absmiddle" title="Produto com Foto" /></a>'; else echo '&nbsp;'; 
						}



						?></td>
						<td class="preto" width="50" align="center"><a href="products.php?ID=<?=$prod_id;?>&ID_ORG=<?=$ID;?>"><?=$prod_id?></a></td>

						<td class="preto" width="80" align="center" ><a href="products.php?ID=<?=$prod_id;?>&ID_ORG=<?=$ID;?>"><?=$ref?></a></td>
						<td class="preto" width="100" align="center" ><?=$marca?></td>
						<td class="preto" width="200" align="left" ><?=$familia?></td>
						<td class="preto"><a href="products.php?ID=<?=$prod_id;?>&ID_ORG=<?=$ID;?>"><?php if($cabecalho) echo ($cabecalho); else echo "[Sem nome]"?></a></td>

						<td class="preto" width="80" align="center" ><?=$GLOBALS['Suppliers_nomes'][$obj_Supplier_ID]?></td>
						<td class="preto" width="80" align="center" ><?=$precoCusto?></td>
						<!--<td class="preto" width="80" align="center" ><?=$iva?></td>-->
						<td class="preto" width="80" align="center" ><?=$ean?></td>
						<td class="preto" width="80" align="center" ><?=$Stock?></td>
						<td class="preto" width="80" align="center" ><?=$Peso?></td>
						<td class="preto" width="100" align="center"><?=$state;?></td>
						<td width="29" align="center" class="ordem<?=$n?>"><input class="ordem small" style="padding:0; width:30px" type="text" name="field_<?=$prod_id?>" value="<?=$ordem?>"></td>
					</tr>
					<?php

					$dados->db_next();
					$count++;
				}
				echo '<table>';
			}

			$dados->db_close();
		}
		?>
            <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td style="height: 30px;text-align: right;padding:10px;font-size: 16px;">TOTAL: <?=number_format($total,0,'.',',')?></td></tr></table>
            <table width="100%" style="background:#606060" border="0" cellspacing="0" cellpadding="0"><tr><td height="5"></td></tr><tr><td>

         	<table border="0" cellspacing="0" cellpadding="0">
            <tr>
			<td height="30" width="50" style="color:#FFF" align="center">Páginas:</td>
             <td>
						<?php if($n_pags)
						{
							// echo '<input type="hidden" name="from" value="'.$from.'">';
							echo '<input type="hidden" name="total_prods" value="'.$total_prods.'">';
							?>
						<select name="paginas" onChange='submit_fileds(this.value, "<?=$_GET['total_prods']?>");' class="form-control" style="width:80px">
						<?php
						for($pi = 1; $pi <= $n_pags; $pi++)
						{

							if($pi!=$pagina){?><option value="<?=$pi; ?>"><?=$pi?></option><?php }
							else {?><option selected="selected" value="<?=$pi?>"><?=$pi?></option><?php }
						}
						?>
						</select><?php
						}?>



						</td>
						<td width="2px" class="separador_operacoes"></td>
						<td width="50" style="color:#FFF" align="center"> Mostrar:</td>
						<td>
							<select name="total_prods" onChange='submit_fileds("<?=$_GET['pagina']?>", this.value);' class="form-control">
								<option value="10" <?php if($total_prods==10) echo "selected"?>>10</option>
								<option value="20" <?php if($total_prods==20) echo "selected"?>>20</option>
								<option value="50" <?php if($total_prods==50) echo "selected"?>>50</option>
								<option value="100" <?php if($total_prods==100) echo "selected"?>>100</option>
								<option value="500" <?php if($total_prods==500) echo "selected"?>>500</option>
								<option value="5000" <?php if($total_prods==5000) echo "selected"?>>5000</option>
							</select>
						</td>
						<td></td>
						<td width="100px"></td>
						<td width="2px"></td>
						<td valign="middle" nowrap="nowrap">
                          <table border="0" cellspacing="2" cellpadding="0">
                            <tr>
                           	  <td style="color:#FFF" width="150" class="preto">com os seleccionados:</td>
                                <td>
                                    <select name="flag" id="flag" style="width:125px" class="regular" onchange="call_associated()">
                                    <option value="" selected="selected"></option>
                                    <option value="1">apagar</option>
                                    <option value="2">reordenar</option>
                                    <option value="3">Mover para a Familia</option>
                                    <!--option value="14">Mover para a Marca</option><option value="" disabled="disabled"></option-->
                                    <option value="4">Ativar Produto</option>
                                    <option value="5">Desativar Produto</option>
                                    <option value="" disabled="disabled"></option>
                                    
                                    <option value="10">Ativar Carrinho</option>
                                    <option value="11">Desativar Carrinho</option>
                                    <option value="" disabled="disabled"></option>

                                    <option value="20">Ativar destaque</option>
                                    	<option value="21">Desativar destaque</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="22">Ativar outlet</option>
                                    	<option value="23">Desativar outlet</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="24">Ativar promoção</option>
                                    	<option value="25">Desativar promoção</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="26">Ativar novo</option>
                                    	<option value="27">Desativar novo</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="28">Ativar mais vendido</option>
                                    	<option value="29">Desativar mais vendido</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="30">Ativar sync automatica</option>
                                    	<option value="31">Desativar sync automatica</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="32">Ativar encomenda sem stock</option>
                                    	<option value="33">Desativar encomenda sem stock</option>
                                    	<option value="" disabled="disabled"></option>

                                    <option value="34">Substituir texto</option>
                                    	<option value="" disabled="disabled"></option>
                                    </select>
                                </td>
                                <td>
                                    <span id="c_assoc_3" style="display:none"><? show_familia_xx("","products_families","products_families_lang","","250px");?></span>
                                    <span id="c_assoc_14" style="display:none"><? produtos_select("sel_marca2", "marcas", "");?></span>
                                </td>
                                <td width="100"><input type="submit" name="APLICAR" value="APLICAR"  class="btn btn-primary" style="padding:5px"></td>
                                <!--td class="t1" align="right">
									<a class="preto_xs" href="#" onclick="javascript:window.open('<?=$site_path?>relatorios/produtos_export.php?tipo=1&where=<?php echo base64_encode(urlencode($where))?>&order=<?=$order?>')"><img title="Imprimir Produtos" src="button_print2.png" border="0" align="absmiddle" /></a>&nbsp;<a class="preto_xs" href="#" onclick="javascript:window.open('<?=$site_path?>relatorios/produtos_export.php?tipo=2&where=<?php echo base64_encode(urlencode($where))?>&order=<?=$order?>')"><img title="Exportar Produtos" src="button_export.png" border="0" align="absmiddle" /></a>
								</td-->
                            </tr>
                          </table>
						</td>

					</tr>
				</table>
				</td></tr><tr><td height="5"></td></tr></table>

			</div>

			</form>



            <table class="t3" align="center" border=0 cellpadding=5 cellspacing=0>
            <tr>
            	<td valign="top" align="left" bgcolor="#FFFFFF"><b>AJUDA:</b><br /><br />
            	  Em todos os campos numericos podem ser usadas as seguintes opções de pesquisa:<br />
            	  a) Pode ser inserida mais do que uma condição, por exemplo, preço entre 2 e 5 deve colcoar-se:&gt;2;&lt;5 ou seja, cada campo será separado por ;<br />
            	  b)
                As opções disponiveis são:<br />
                menor:&lt;<br />
                maior:&gt;<br />
                maior ou igual:&gt;=<br />
                menor ou igual:&lt;=<br />
                igual:=<br />
                diferente:!=</td></tr></table>


        </td>
        <!--td width="10px">&nbsp;</td>
        <td valign="top" width="200"><? show_fast_tools();?></td-->
    </tr>
    </table>

<? 	include "../layout/bottom.inc.php";

function produtos_select($nome_select, $tipo, $valor, $width="",$funcao="")
{
    global $DB_host,$DB_user,$DB_pass,$DB_name;
	$style="";
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	if ($width!="") $style=';width:' . $width;
    echo '<select name="'.$nome_select.'" id="'.$nome_select.'" style="font-size:10px;height:20px' . $style . '" ' . $funcao . '>';
	$rs=new Dataobj;
	$sql="Select outras_tabelas.Ordem, outras_tabelas_linguas.* from outras_tabelas INNER JOIN outras_tabelas_linguas ON outras_tabelas.ID=outras_tabelas_linguas.ID_PAI where Tipo='" . $tipo . "' $sql_cond ORDER BY Ordem, Descricao " .$limit;
	$hRes=$rs->db_open($db_link,$DB_name,$sql);

	echo "<option value=''>--</option>";
	while (!$rs->EOF())
	{
		$idd=$rs->db_data["ID_PAI"];
		echo '<option value="'. $idd .'"';
		if ($valor==$idd) echo ' selected ';
		echo '>';
		echo $rs->db_data["Descricao"] . '</option>';
		$rs->db_next();
	}
	echo '</select>';
	$rs->db_close();
}


function supplier_select($nome_select, $valor, $width="", $funcao="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$style = ''; if ($width != '') $style = ';width:' . $width;


	echo '<select name="'.$nome_select.'" id="'.$nome_select.'" style="font-size:10px;' . $style . '" ' . $funcao . ' class="small form-control" data-placeholder="Escolha uma Moeda" class="form-control">';
	echo '<option value=""'; if (!$valor){ echo ' selected ';} echo '>Todas</option>';

	$i=0;
	$t=count($GLOBALS['lista_importadores']);
	while($i<$t)
	{
		$idd=$GLOBALS['lista_importadores'][$i]['id'];
		echo '<option value="'. $idd .'"'; if ($valor==$idd){ echo ' selected ';} echo '>';
		echo $GLOBALS['lista_importadores'][$i]['name'];
		echo '</option>';
		$i++;
	}
	echo '</select>';


}

function marcas_select($nome_select, $valor, $width="", $funcao="")
{
	//var_dump($_SESSION);
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$style = ''; if ($width != '') $style = ';width:' . $width;
	$rs = new Dataobj;
	$sql = 'SELECT brands.ID, brands_lang.Nome FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI WHERE brands_lang.Lang=\''.$_SESSION['ss_admin_lang'].'\' ORDER BY brands.Ordem, brands_lang.Nome ASC';
	$rs->db_open($db_link,$DB_name,$sql);

	echo '<select name="'.$nome_select.'" id="'.$nome_select.'" style="font-size:10px;' . $style . '" ' . $funcao . ' class="small form-control" data-placeholder="Escolha uma Moeda" class="form-control">';
	echo '<option value=""'; if (!$valor){ echo ' selected ';} echo '>Todas</option>';
	while (!$rs->EOF())
	{
		$idd = $rs->db_data["ID"];
		echo '<option value="'. $idd .'"'; if ($valor==$idd){ echo ' selected ';} echo '>';
		echo $rs->db_data["Nome"] . '</option>';
		$rs->db_next();
	}
	echo '</select>';
	$rs->db_close();

}

function estados_select($nome_select, $valor, $width="", $funcao="")
{
	global $array_estado;
	$style = ''; if ($width != '') $style = ';width:' . $width;
	echo '<select name="'.$nome_select.'" id="sel_estado" style="font-size:10px'.$style.'" class="small form-control" '.$funcao.'>';
	for($a=0; $a<count($array_estado); $a++){ echo '<option'; if ($valor==$a){ echo ' selected ';} echo ' value="'.($a>0 ? $a :'').'">'.$array_estado[$a].'</option>'; }
	echo '</select>';
}


function getFiltros($campo1,$campo2,$campoBD,$tipo)
{
	$tags=array("<",">",">=","<=","=",'"',"'","!","!=");
	$campo1=stripslashes($campo1);
	$campo2=stripslashes($campo2);
	if($tipo=="ex" || $tipo=="lista"){if($campo1!="")return $campoBD." = '".$campo1."'";}
	elseif($tipo=="s")
	{
		$campo1=str_replace("'",'{p}',$campo1);
		if($campo1=="is null" || $campo1=="=")
		{
			return "LOWER(".$campoBD.") is null";
		}
		else
			if($campo1!="")return (substr_count($campo1,'"')>0 ? " LOWER(".$campoBD.")='".str_replace('"','',$campo)."'" :" LOWER(".$campoBD.") like '%".$campo1."%' ");
	}
	elseif($tipo=="num" || $tipo=="perc")
	{
		if($campo1!="")
		{
			if($campo1=="is null" || $campo1=="=") return $campoBD." is null ";
			$array=explode(";",$campo1);
			$i=0;
			$aux="";
			while($i<count($array))
			{
				if($aux)$aux.=" and ";
				$campo1=$array[$i];
				$campo1_aux=str_replace($tags,"",$campo1);
				$campo1_aux=str_replace(",",".",$campo1_aux);
				if(substr_count($campo1,'<>')>0 || substr_count($campo1,'!=')>0)$aux.=$campoBD." <> '".$campo1_aux."' ";
				elseif(substr_count($campo1,'>=')>0)$aux.=$campoBD." >='".$campo1_aux."' ";
				elseif(substr_count($campo1,'<=')>0)$aux.=$campoBD." <='".$campo1_aux."' ";
				elseif(substr_count($campo1,'=')>0)$aux.=$campoBD."='".$campo1_aux."' ";
				elseif(substr_count($campo1,'>')>0)$aux.=$campoBD." > '".$campo1_aux."' ";
				elseif(substr_count($campo1,'<')>0)$aux.=$campoBD." < '".$campo1_aux."' ";
				else $aux.=$campoBD." Like '%".$campo1_aux."%' ";
				$i++;
			}
			return $aux;
		}
	}
	elseif($tipo=="data")
	{
		if($campo1 && !$campo2){
			if(is_numeric($campo1))return "Year(".$campoBD.")='$campo1'";
			else return $campoBD." Like '$campo1%'";
		}
		elseif(!$campo1 && $campo2){
			if(is_numeric($campo2)) return "Year(".$campoBD.")='$campo2'";
			else return $campoBD." Like '$campo2%'";
		}
		elseif($campo1 && $campo2){
			if(is_numeric($campo1) && is_numeric($campo2))return "(Year(".$campoBD.")>='$campo1' and Year(".$campoBD.")<='$campo2')";
			return $campoBD." Between '$campo1' and '$campo2'";
		}
	}
	elseif($tipo=="ano"){if($campo1) return "YEAR(".$campoBD.")=".$campo1;}
	return "";
}

function show_familia_xx($id_org=0,$Nomeb,$Nomeb_linguas, $multi="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$txt_email14;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$obj2=new Dataobj;

	if($multi)
		echo '<select id="m_familia2" name="m_familia2" size="5" style="width:350px; height:100px" class="form-control4" onDblclick="move_to_left(document.getElementById(\'m_familias_seleccionadas\'),document.getElementById(\'m_familia2\'),\'m_familias_seleccionadas\');">';
	else{
    	echo '<select name="m_familia2" id="m_familia2" style="width:350px" class="form-control4">';
		if($id_org==0) echo '<option value="0" selected>- '.$txt_email14.' -</option>';
		else echo '<option value="0">- '.$txt_email14.' -</option>';
	}
	
	$sql="Select ID from $Nomeb where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    while(!$obj->EOF())
    {

		echo '<option value="'. $obj->db_data["ID"] .'"';

    	$sql="Select Cabecalho from $Nomeb_linguas where ID_PAI=".$obj->db_data["ID"]. " and Lang='$admin_lang'";
	    $hRes=$obj2->db_open($db_link,$DB_name,$sql);
		echo '>' . $obj2->db_data["Cabecalho"] . '</option>';
	    $obj2->db_close();

	    show_familiab_xx($obj->db_data["ID"],$id_org,$Nomeb,$Nomeb_linguas);

        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}

function show_familiab_xx($id_start=0,$id_org=0,$Nomeb,$Nomeb_linguas,$contador=5)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID,$ID_Fam;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new Dataobj;
	$sql="Select * from $Nomeb where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    while(!$obj->EOF())
    {


	    echo '<option value="'. $obj->db_data["ID"] .'"';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$obj->db_data["ID"]. " and Lang='$admin_lang'";
        $obj2=new Dataobj;
	    $hRes=$obj2->db_open($db_link,$DB_name,$sql);
		echo '>';
		//$i=0;while($i<$contador){echo "&nbsp;";$i++;}
		echo $obj2->db_data["Cabecalho"] . '</option>';

	    $obj2->db_close();

	    show_familiab_xx($obj->db_data["ID"],$id_org,$Nomeb,$Nomeb_linguas,$contador+5);

        $obj->db_next();
    }
    $obj->db_close();
}
function show_breadcrums_recursivo_on($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$sql = 'select pf.ID, pf.ID_ORG, pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID="'.$id_pai.'"';
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$str) $str = '-';	else $str='';
	if(!$rs->EOF())
	{
		if($rs->db_data['ID_ORG']==0)
		{
			$str=htmlentities_memo(stripslashes(($rs->db_data['Cabecalho']))).$str;
		}
		else
		{
			$str=htmlentities_memo(stripslashes($rs->db_data['Cabecalho'])).$str;
			$str=show_breadcrums_recursivo_on($rs->db_data['ID_ORG']).$str;
		}
	}
	$rs->db_close();

	return $str;
}

function gera_lista_familiasv($id_org=0,$tabela1,$tabela1_lang, $width='',$id_org2=0)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$style = ''; if ($width != '') $style = ';width:' . $width;

	$obj=new DataObj;
	$sql="Select * FROM $tabela1 WHERE ID_ORG='0' ORDER BY Ordem ASC";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" style="font-size:10px'.$style.'" class="form-control">';
	if($id_org==0) echo '<option value="0" selected>Todas</option>';
	else echo '<option value="0">Todas</option>';

	if($id_org==-1) echo '<option value="-1" selected>SEM FAMILIA</option>';
	else echo '<option value="-1">SEM FAMILIA</option>';

    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org2==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * FROM $tabela1_lang WHERE ID_PAI=".$obj->db_data["ID"]." AND Lang='$admin_lang' ORDER BY Cabecalho ASC";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);
		echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
		$obj2->db_close();
	    gera_lista_subfamiliasv($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang, 5, $id_org2);
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfamiliasv($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=5,$id_org2)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang,$ID;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql="Select * from $tabela1 where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    while(!$obj->EOF())
    {
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org2==$obj->db_data["ID"]) echo ' selected ';
		echo $sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang' ORDER BY Cabecalho ASC";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);
		echo '>';
		$i=0;while($i<$contador){echo "-";$i++;} echo " ";
		echo  htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';
	    $obj2->db_close();
	    gera_lista_subfamiliasv($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+5,$id_org2);
        $obj->db_next();
    }
    $obj->db_close();
}
?>
<script src="avancada/chosen.jquery.min2.js?dp=<?php echo dumpcache?>" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>

<script type="text/javascript">
$(".form-control4").chosen({disable_search_threshold: 10, no_results_text: "Não foram encontrados resultados!",width: "300px",enable_new_filter:false});
$(".form-control6").chosen({disable_search_threshold: 10, no_results_text: "Não foram encontrados resultados!",enable_new_filter:false});
</script>
