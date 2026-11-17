<?php
	 header ('Content-type: text/html; charset=UTF-8');
	include "settings.inc.php";

	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(trim($total_prods=="")) $total_prods=10;
	if(trim($from=="")) $from=0;
	if(trim($porpagina=="")) $porpagina=$total_prods;
	// $limit=" LIMIT ".$from.",".$porpagina;

	$array_estado[] = 'Todos';
	// $array_estado[] = 'Destaque';
	$array_estado[] = 'Promoção';
	// $array_estado[] = 'Travel Size';
	// $array_estado[] = 'Novidade';
	$array_estado[] = 'Carrinho';

	$array_estado[] = 'Visiveis';
	$array_estado[] = 'Invisiveis';

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
				$sql="UPDATE news1 SET Ordem='$dv' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
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
				$sql="UPDATE news1 SET ID_ORG='$m_familia' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
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
				$sql="UPDATE news1 SET Aproved=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
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
				$sql="UPDATE news1 SET Aproved=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
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
				$sql="UPDATE news1 SET mostrar_online=1 WHERE ID=$str_id";
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
				$sql="UPDATE news1 SET mostrar_online=0 WHERE ID=$str_id";
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
				$sql="UPDATE news1 SET Carrinho=1 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
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
				$sql="UPDATE news1 SET Carrinho=0 WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}
	if($flag==14)
	{
		foreach($_GET as $varName => $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE news1 SET ID_Marca='$sel_marca2' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		};
	}

	include '../layout/header.inc.php';
	include '../layout/body.inc.php';
	?>
	<style>
		.barra_operacoes{background-color:#999999}
		.preto{cursor:pointer}
		div.small{border: 1px solid #999;  display: block;  height: 25px;}
		div.small > img{margin-top:2px}
		input.small{border: 1px solid #999;  display: block;  height: 25px;}
		input.small:focus{border: 1px solid #5897fb;  display: block;  height: 25px;}
	</style>
	 <link rel="stylesheet" type="text/css" href="avancada/chosen.css" />
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


		if(pagina > 0 && porpagina > 0) window.open('avancada.php?campo='+campo+'&pagina='+pagina+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else if(pagina > 0) window.open('avancada.php?campo='+campo+'&pagina='+pagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else if(porpagina > 0) window.open('avancada.php?campo='+campo+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else window.open('avancada.php?campo='+campo+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
	}

	$(document).ready(function() {$('.fancybox_img').fancybox();});
	</script>
    <form name="lista_produtos" method="get" onSubmit="return checkFields()">
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
        <td valign="top" align="left"><?php	include '../layout/menus.inc.php'; ?>

			<table width="100%" border="0" cellspacing="0" cellpadding="0" class="p1">
			<tr>
			<td width="29" align="center"></td>
			<td width="10px" align="center"></td>
			<td align="right" class="branco"></td>
			<td width="5"></td>
			</tr>
			</table>

			<table class="barra_operacoes" width="100%" border="0" cellspacing="0" cellpadding="2" style="background:#FFF">
			<tr>
				<td	class="preto" width="29" align="center">&nbsp;</td>
				<td	class="preto" width="29" align="center" title="Visivel" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("Aproved")?>')" ><img alt="produtos activos" src="ok.png" border="0" align="absmiddle" /></td>
				<td	class="preto" width="29" align="center" title="Listar produtos com foto" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("img_ext")?>')" >Foto</td>
				<td	class="preto" width="50" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID")?>')" >ID</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("news1_objs_lang.Cabecalho")?>')" >REF</td>
				<td	class="preto" width="100" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID_Marca")?>')" >MARCA</td>
				<td	class="preto" width="200px" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID_ORG")?>')" >FAMILIA</td>
				<td	class="preto" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("nome_produto")?>')" >DESIGNAÇÃO</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("news1_objs.obj_pvp")?>')" >PVP</td>
				<td	class="preto" width="50" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("news1_objs.obj_stock")?>')" >STOCK</td>
				<td	class="preto" width="50" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("Peso")?>')">PESO</td>
				<td	class="preto" width="100" align="center" style="cursor:default">Estado</td>
				<td	class="preto" width="29" align="center">&nbsp;</td>
			</tr>
			<tr>
				<td width="29" align="center" valign="top" class="formfields"><input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1" <?php if($filter) echo "checked"?>></td>
				<td width="29" align="center" valign="top" class="formfields"><!--input type="checkbox" name="visivel" id="visivel" value="1" <?php if($visivel) echo "checked"?>--></td>
				<td width="29" align="center" valign="top" class="formfields"><!--input type="checkbox" name="fotos" id="fotos" value="1" <?php if($fotos) echo "checked"?>--></td>
				<td width="50" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_id" id="pesq_id" value='<?php echo stripslashes($pesq_id)?>' class="small" /></td>
				<td width="80" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_ref" id="pesq_ref" value="<?php echo $pesq_ref?>" class="small"></td>
				<td width="100" align="center" valign="top" class="formfields"><?php echo marcas_select("sel_marca", $sel_marca,"150px","");?></td>
				<td width="200" align="center" valign="top" class="formfields"><?php gera_lista_familiasv(0,'news1_families','news1_families_lang', '200px', $_GET['m_familia']);?></td>
				<td valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_desig" id="pesq_desig" value="<?php echo $pesq_desig?>" class="small" /></td>
				<td width="80" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_preco" id="pesq_preco" value="<?php echo $pesq_preco?>" class="small" /></td>
				<td width="50" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_stock" id="pesq_stock" value="<?php echo $pesq_stock?>" class="small" /></td>
				<td width="50" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_peso" id="pesq_peso" value="<?php echo $pesq_peso?>" class="small" /></td>
				<td width="100" align="center" valign="top" class="formfields"><?php estados_select('sel_estado', $_GET['sel_estado'], '90%', '');?></td>
				<td width="29" align="center" style="vertical-align:top" class="formfields"><div class="small" onclick="submit_fileds();" style="cursor:pointer"><img src="lupa2.png" border="0" title="Pesquisar" alt="Pesquisar"  /></div></td>
			</tr>
            <?php

            //**************************OBTER OS FILTROS***********************************************
            if($visivel) $where.=" AND Aproved=1 ";

            $aux=getfiltros($pesq_id,"", "news1.ID", "num");
            if($aux!="") $where.=" AND " . $aux;

			$aux=getfiltros($pesq_peso,"", "news1.Peso", "num");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_ref,"","news1_objs_lang.Cabecalho", "s");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($sel_marca,"", "ID_Marca", "ex");
            if($aux!="") $where.=" AND " . $aux;

            // $aux=getfiltros($pesq_fam,"", "news1_families_lang.Cabecalho", "s");
			// $aux=getfiltros($m_familia,"", "news1_families_lang.ID_PAI", "num");
            if(isset($_GET['m_familia']) && $_GET['m_familia'] > 0) $where.=" AND (news1.ID_ORG='".$_GET['m_familia']."')";

            $aux=getfiltros($pesq_desig,"", "news1_lang.Cabecalho", "s");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($sel_unvenda,"", "unidade_venda", "ex");
            if($aux!="") $where.=" AND " . $aux;

            $aux=getfiltros($pesq_preco,"", "news1_objs.obj_pvp", "num");
            if($aux!="") $where.=" AND " . $aux;

            // if($visivelweb!="") $where.=" AND Aproved=1";

            if($sel_estado==5) $where.=" AND home=1";
			elseif($sel_estado==1) $where.=" AND news1_objs.obj_destaque=1";
			elseif($sel_estado==2) $where.=" AND (news1_objs.obj_promo_ativo=1 and news1_objs.obj_promo > 0 && news1_objs.obj_promo<>news1_objs.obj_pvp)";
			elseif($sel_estado==3) $where.=" AND (news1_objs.obj_travelsize=1)";
			elseif($sel_estado==4) $where.=" AND (news1_objs.obj_novo=1)";
			elseif($sel_estado==6) $where.=" AND (news1.Aproved=1)";
			elseif($sel_estado==7) $where.=" AND (news1.Aproved=0)";

			if(isset($_GET['fotos']) && $_GET['fotos'] > 0)
			{
				$join = ' INNER JOIN news1_images ON news1_images.ID_ORG=news1.ID';
				$where2 = ' AND news1_images.img_ext<>"" ';
			}
			else
			{
				$join = '';
				$where2 = '';
			}
			    // echo 'where2:'.$where2;
            if($pesq_stock)
            {
                $aux=getfiltros($pesq_stock,"", "news1_objs.obj_stock", "num");
                if($aux!="") $where.=" AND " . $aux;
            }
            /******************************************************************************************/
/*             $sql = '
					SELECT
						news1.*, news1_lang.Cabecalho as nome_produto,
						news1.Carrinho as est_carrinho, news1_objs.obj_destaque as est_dest,
						if(news1_objs.obj_promo_ativo=1 and news1_objs.obj_promo > 0 && news1_objs.obj_promo<>news1_objs.obj_pvp, 1, 0) as est_prom,
						news1_objs.obj_travelsize as est_travel, news1_objs.obj_novo as est_novo,
						news1_families_lang.Cabecalho as nome_familia,
						news1_objs_lang.Cabecalho As Ref,news1_objs.obj_pvp as PrecoCusto,
						news1_objs.obj_stock as Stock,
						filters_news1.fam_id2
					FROM
						news1
						LEFT OUTER JOIN news1_lang ON news1_lang.ID_PAI=news1.ID AND news1_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN filters_news1  ON filters_news1.prod_id=news1.ID
						LEFT OUTER JOIN news1_families_lang ON news1_families_lang.ID_PAI=filters_news1.fam_id1 AND news1_families_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN news1_objs ON news1_objs.obj_type_id=news1.ID
						LEFT OUTER JOIN news1_objs_lang ON news1_objs_lang.ID_PAI=news1_objs.obj_id
					'; */
			$sql = '
					SELECT
						news1.*, news1_lang.Cabecalho as nome_produto,
						news1.home as est_carrinho, news1.Destaque as est_dest,
						if(news1_objs.obj_promo_ativo=1 and news1_objs.obj_promo > 0 && news1_objs.obj_promo<>news1_objs.obj_pvp, 1, 0) as est_prom,
						news1_families_lang.Cabecalho as nome_familia,
						news1_objs_lang.Cabecalho As Ref,news1_objs.obj_pvp as PrecoCusto,
						news1_objs.obj_stock as Stock
					FROM
						news1
						LEFT OUTER JOIN news1_lang ON news1_lang.ID_PAI=news1.ID AND news1_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN news1_objs ON news1_objs.obj_type_id=news1.ID
						LEFT OUTER JOIN news1_objs_lang ON news1_objs_lang.ID_PAI=news1_objs.obj_id
						LEFT OUTER JOIN news1_families  ON news1_families.ID=news1.ID_ORG
						LEFT OUTER JOIN news1_families_lang ON news1_families_lang.ID_PAI=news1_families.ID AND news1_families_lang.Lang="'.$admin_lang.'"
					';
			if($join) $sql .= $join;
            $sql .= ' WHERE 1=1' . $where.$where2;
            if($order) $sql .= ' GROUP BY news1.ID';
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
					$aproved = $dados->db_data["Aproved"];
					$Stock = (isset($dados->db_data["Stock"]) && $dados->db_data["Stock"] > 0) ? $dados->db_data["Stock"] : 0;

					// IMAGENS DO PRODUTO
				/* 	$arr_imagens = get_product_images($prod_id); $arr_img = array();
					if(count($arr_imagens)>0)
					{
						for ($i=0; $i < count($arr_imagens); $i++)
						{
							$ext = $arr_imagens[$i]['img_ext'];
							$ordem = $arr_imagens[$i]['Ordem'];
							$img_file = 'files/news1/'.$prod_id.'_'.$ordem.'.'.$ext;
							$arr_img[] = $GLOBALS['site_http_url'].$img_file;
						}
					} */

					// MARCA DO PRODUTO
					$marca = $dados->db_data["ID_Marca"];
					$sql = 'SELECT Nome FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI WHERE brands.ID="'.$marca.'"';
					$dados2->db_open($db_link,$DB_name,$sql);
					if(!$dados2->EOF()) $marca = $dados2->db_data["Nome"];	else $marca = '-';
					$dados2->db_close();

					// FAMILIAS DO PRODUTO
					$familia = $dados->db_data["ID_ORG"];
					$familia = rtrim(show_breadcrums_recursivo_on($familia),'-');

					$Peso = $dados->db_data["Peso"];

					$precoCusto=$dados->db_data["PrecoCusto"];
					$cabecalho=$dados->db_data["nome_produto"];

					$state = '';
					if($dados->db_data["est_carrinho"]==1) $state .= '<img src="avancada/carrinho.gif" width="16" title="Pode adicionar ao carrinho" border="0" align="absmiddle" />';
					if($dados->db_data["est_dest"]==1) $state .= '<img src="avancada/destaque.png" border="0" title="Em destaque na homepage" align="absmiddle" />';
					if($dados->db_data["est_prom"]==1) $state .= '<img src="avancada/promo.png" border="0" title="Em promoção" align="absmiddle" />';
					if($dados->db_data["est_travel"]==1) $state .= '<img src="avancada/travel.png" border="0" title="Pode ser expedido para fora de Portugal" align="absmiddle" />';
					if($dados->db_data["est_novo"]==1) $state .= '<img src="avancada/novidade.png" border="0" title="Novidade" align="absmiddle" />';

					if($count % 2 != 0)	{$cor = '#F6F6F6'; $n = '1';}
					else				{$cor = '#def2fa'; $n = '2';}

					?>


					<tr style="background:<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">

					<td width="29" height="30" align="center"><input type="checkbox" name="chk_<?php echo $prod_id?>" id="chk_<?php echo $prod_id?>" value="<?php echo $prod_id?>" class="cb" /></td>
					<td width="29" align="center"><?php if($aproved) echo "<img src='ok.png' border='0' align='absmiddle' title='Produto Activo' />"; else echo "-";?></td>
					<td class="texto" width="29" align="center"><?php if($arr_img[0]) echo '<a href="'.$arr_img[0].'" class="fancybox_img"><img src="photo.png" border="0" align="absmiddle" title="Produto com Foto" /></a>'; else echo '&nbsp;'; ?></td>
					<td class="preto" width="50" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $prod_id?></td>
					<td class="preto" width="80" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $ref?></td>
					<td class="preto" width="100" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $marca?></td>
					<td class="preto" width="200" align="left" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $familia?></td>
					<td class="preto" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php if($cabecalho) echo ($cabecalho); else echo "[Sem nome]"?></td>
					<td class="preto" width="80" align="center" onclick="window.open('news1>.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $precoCusto?></td>
					<td class="preto" width="50" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $Stock?></td>
					<td class="preto" width="50" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');" ><?php echo $Peso?></td>
					<td class="preto" width="100" align="center" onclick="window.open('news1.php?ID=<?php echo $prod_id;?>&ID_ORG=<?php echo $ID;?>','_self');"><?php echo $state;?></td>
					<td width="29" align="center" class="ordem<?php echo $n?>"><input class="ordem small" style="padding:0; width:30px" type="text" name="field_<?php echo $prod_id?>" value="<?php echo $ordem?>"></td>
					</tr>


					<?php
					$dados->db_next();
					$count++;
				}
				echo '<table>';
			}

			$dados->db_close();?>

            <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
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
						<select name="paginas" onChange='submit_fileds(this.value, "<?php echo $_GET['total_prods']?>");' class="form-control" style="width:80px">
						<?php
						for($pi = 1; $pi <= $n_pags; $pi++)
						{

							if($pi!=$pagina){?><option value="<?php echo $pi; ?>"><?php echo $pi?></option><?php }
							else {?><option selected="selected" value="<?php echo $pi?>"><?php echo $pi?></option><?php }
						}
						?>
						</select><?php
						}?>



						</td>
						<td width="2px" class="separador_operacoes"></td>
						<td width="50" style="color:#FFF" align="center"> Mostrar:</td>
						<td>
							<select name="total_prods" onChange='submit_fileds("<?php echo $_GET['pagina']?>", this.value);' class="form-control">
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
						<td valign="middle" nowrap="nowrap"><? /*
                          <table border="0" cellspacing="2" cellpadding="0">
                            <tr>
                           	  <td style="color:#FFF" width="150" class="preto">com os seleccionados:</td>
                                <td>
                                    <select name="flag" id="flag" style="width:125px" class="regular" onchange="call_associated()">
                                    <option value="" selected="selected"></option>
                                    <option value="1">apagar</option>
                                    <option value="2">reordenar</option>
                                    <option value="3">Mover para a Familia</option>
                                    <option value="14">Mover para a Marca</option><option value="" disabled="disabled"></option>
                                    <option value="4">Ativar Produto</option><option value="5">Desativar Produto</option><option value="" disabled="disabled"></option>
                                    <option value="6">Mostrar na WEB</option><option value="7">Esconder na WEB</option><option value="" disabled="disabled"></option>
                                    <option value="8">Ativar Outlet</option><option value="9">Desativar outlet</option><option value="" disabled="disabled"></option>
                                    <option value="10">Ativar Carrinho</option><option value="11">Desativar Carrinho</option><option value="" disabled="disabled"></option>
                                    </select>
                                </td>
                                <td>
                                    <span id="c_assoc_3" style="display:none"><? show_familia_xx("","news1_families","news1_families_lang","","250px");?></span>
                                    <span id="c_assoc_14" style="display:none"><? produtos_select("sel_marca2", "marcas", "");?></span>
                                </td>
                                <td><input type="image" src="bt_ok.png" name="OK" value="OK" hspace="5"></td>
                                <td class="t1" align="right">
									<a class="preto_xs" href="#" onclick="javascript:window.open('<?php echo $site_path?>relatorios/produtos_export.php?tipo=1&where=<?php echo base64_encode(urlencode($where))?>&order=<?php echo $order?>')"><img title="Imprimir Produtos" src="button_print2.png" border="0" align="absmiddle" /></a>&nbsp;<a class="preto_xs" href="#" onclick="javascript:window.open('<?php echo $site_path?>relatorios/produtos_export.php?tipo=2&where=<?php echo base64_encode(urlencode($where))?>&order=<?php echo $order?>')"><img title="Exportar Produtos" src="button_export.png" border="0" align="absmiddle" /></a>
								</td>
                            </tr>
                          </table>			*/ ?>
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
            	  a) Pode ser inserida mais do que uma condição, por exemplo, preço entre 2 e 5 deve colcoar-se: &gt;2;&lt;5 ou seja, cada campo será separado por ;<br />
            	  b)
                As opções disponiveis são:<br />
                menor: &lt;<br />
                maior: &gt;<br />
                maior ou igual: &gt;=<br />
                menor ou igual: &lt;=<br />
                igual: =<br />
                diferente: !=</td></tr></table>


        </td>
        <? /* <td width="10px">&nbsp;</td>
        <td valign="top"><? show_fast_tools();?></td> */ ?>
    </tr>
    </table>

<? 	include "../layout/bottom.inc.php";



function marcas_select($nome_select, $valor, $width="", $funcao="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$style = ''; if ($width != '') $style = ';width:' . $width;
	$rs = new Dataobj;

	$sql = 'SELECT brands.ID, brands_lang.Nome FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI';
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
	for($a=0; $a<count($array_estado); $a++){ echo '<option'; if ($valor==$a){ echo ' selected ';} echo ' value="'.($a>0 ? $a : '').'">'.$array_estado[$a].'</option>'; }
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
			if($campo1!="")return (substr_count($campo1,'"')>0 ? " LOWER(".$campoBD.")='".str_replace('"','',$campo)."'" : " LOWER(".$campoBD.") like '%".$campo1."%' ");
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

  /* global $Server,$UserName,$db_password,$DBName,$lang,$ID,$ID_Fam, $txt_email14;
	$dbh=OpenConnection($Server,$UserName,$db_password);
	$obj=new clsRecordset;
	$sql="Select * from $Nomeb where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->Open($dbh,$DBName,$sql);

	if($multi)
		echo '<select id="m_familia" name="m_familia" size="5" style="width:350px; height:100px" class="regular" onDblclick="move_to_left(document.getElementById(\'m_familias_seleccionadas\'),document.getElementById(\'m_familia\'),\'m_familias_seleccionadas\');">';
	else{
    	echo '<select name="m_familia" id="m_familia" style="width:350px" class="regular">';
		if($id_org==0) echo '<option value="0" selected>- '.$txt_email14.' -</option>';
		else echo '<option value="0">- '.$txt_email14.' -</option>';
	}
    while(!$obj->EOF())
    {

		echo '<option value="'. $obj->db_data["ID"] .'"';
		//if ($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$obj->db_data["ID"]. " and Lang='$lang'";
        $obj2=new clsRecordset;
	    $hRes=$obj2->Open($dbh,$DBName,$sql);
		echo '>' . $obj2->db_data["Cabecalho"] . '</option>';

	    $obj2->close();

	    show_familiab_xx($obj->db_data["ID"],$id_org,$Nomeb,$Nomeb_linguas);

        $obj->next();
    }
    $obj->close();
    echo '</select>';
    CloseConnection($dbh);*/
}

function show_familiab_xx($id_start=0,$id_org=0,$Nomeb,$Nomeb_linguas,$contador=5)
{
   /* global $Server,$UserName,$db_password,$DBName,$lang,$ID,$ID_Fam;
	$dbh=OpenConnection($Server,$UserName,$db_password);
	$obj=new clsRecordset;
	$sql="Select * from $Nomeb where ID_ORG='$id_start' order by Ordem Asc";
	$hRes=$obj->Open($dbh,$DBName,$sql);
    while(!$obj->EOF())
    {


	    echo '<option value="'. $obj->db_data["ID"] .'"';
		//if ($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
        $sql="Select * from $Nomeb_linguas where ID_PAI=".$obj->db_data["ID"]. " and Lang='$lang'";
        $obj2=new clsRecordset;
	    $hRes=$obj2->Open($dbh,$DBName,$sql);
		echo '>';
		$i=0;while($i<$contador){echo "&nbsp;";$i++;}
		echo $obj2->db_data["Cabecalho"] . '</option>';

	    $obj2->close();

	    show_familiab_xx($obj->db_data["ID"],$id_org,$Nomeb,$Nomeb_linguas,$contador+5);

        $obj->next();
    }
    $obj->close();*/
}
function show_breadcrums_recursivo_on($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$sql = 'select pf.ID, pf.ID_ORG, pfl.Cabecalho from news1_families pf inner join news1_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID="'.$id_pai.'"';
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
	$sql="Select * from $tabela1 where ID_ORG='0' order by Ordem Asc";
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select name="m_familia" style="font-size:10px'.$style.'" class="small form-control">';
	if($id_org==0) echo '<option value="0" selected>Todas</option>';
	else echo '<option value="0">Todas</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org2==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
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
<script src="avancada/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>