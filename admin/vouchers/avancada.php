<?php	
	 header ('Content-type: text/html; charset=UTF-8');
	include "settings.inc.php";
	
	global $DB_host,$DB_user,$DB_pass,$DB_name,$dominio,$caminho,$userx,$passwordx,$site_shell_url,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

	if(trim($total_prods=="")) $total_prods=15;
	if(trim($from=="")) $from=0;
	if(trim($porpagina=="")) $porpagina=$total_prods;
	// $limit=" LIMIT ".$from.",".$porpagina;

	$array_estado[] = 'Todos';
	$array_estado[] = 'Destaque';
	$array_estado[] = 'Promoção';
	$array_estado[] = 'Travel Size';
	$array_estado[] = 'Novidade';
	$array_estado[] = 'Carrinho';
	
	// $array_estado[] = 'Outlet';


	$dados=new DataObj;
	$dados2=new DataObj;
	$obj3=new DataObj;
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
				$sql="UPDATE products SET ID_ORG='$m_familia' WHERE ID=$str_id";
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
				$sql="UPDATE products SET Aproved=1 WHERE ID=$str_id";
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
				$sql="UPDATE products SET Aproved=0 WHERE ID=$str_id";
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
				$sql="UPDATE products SET ID_Marca='$sel_marca2' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if (!$res)  echo db_error($db_link).$sql."<br>";
			}
		}; 
	}

	include 'avancada/header_print.inc.php';	
	include 'avancada/body_print.inc.php';
	?>
	<style>
		.barra_operacoes{background-color:#999999}
		.preto{cursor:pointer}
		.preto2X{ color: black;font-family: Arial;font-size: 11px;}
		div.small{border: 1px solid #999;  display: block;  height: 25px;}		
		
		div.smallbt{border: 1px solid #999; height: 25px; background-color:#FFF; background-image:url(avancada/save.png);background-repeat:no-repeat;color:#000;text-align:center;width:170px;cursor:pointer;padding-left:4px;padding-top:4px;padding-left:35px}
		div.smallbt > span{text-align:left;font-weight:bold;  display: table-cell; vertical-align: middle; height:25px;margin:0 auto;width:170px}
		div.smallbt:hover{background-color:#5897fb;color:#FFF;}	
		
		div.smallbtver{border: 1px solid #999; height: 25px; background-color:#FFF; background-image:url(avancada/lookup.png);background-repeat:no-repeat;color:#000;text-align:center;width:170px;cursor:pointer;padding-left:4px;padding-top:4px;padding-left:35px}
		div.smallbtver > span{text-align:left;font-weight:bold;  display: table-cell; vertical-align: middle; height:25px;margin:0 auto;width:170px}
		div.smallbtver:hover{background-color:#75ed91;color:#000;}	
		
		div.smallbtred{border: 1px solid #999; height: 25px; background-color:#FFF; background-image:url(avancada/save_red.png);background-repeat:no-repeat;color:#000;text-align:center;width:170px;cursor:pointer;padding-left:4px;padding-top:4px;padding-left:35px}
		div.smallbtred > span{text-align:left;font-weight:bold;  display: table-cell; vertical-align: middle; height:25px;margin:0 auto;width:170px}
		div.smallbtred:hover{background-color:#f07070;color:#FFF;}	
		
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
	
	function submit_fams_fileds(t)
	{
		
		if (typeof t == 'undefined') t = '';
		
		var marcaid = ''; var marcanome = ''; var famid = ''; var famnome = ''; var getstr = '';
		$('.formfields').each(function()
		{
			if($(this).find('select#sel_marca').attr('name')!=undefined)
			{
				var name = $(this).find('select#sel_marca').attr('name');
				var value = $(this).find('select#sel_marca').val();		
				var text = $(this).find('select#sel_marca').find(":selected").text();		
				
				if(value!=0 && value!='')
				{ 
					marcaid = value;
					marcanome = text;
					
					getstr += "&marcaid=" + escape(marcaid);
					// getstr += "&marcanome=" + escape(marcanome);
				}
			}
			if($(this).find('select#m_familia').attr('name')!=undefined)
			{
				var name = $(this).find('select#m_familia').attr('name');
				var value = $(this).find('select#m_familia').val();		
				var text = $(this).find('select#m_familia').find(":selected").text();		
				
				if(value!=0 && value!='')
				{ 
					famid = value;
					famnome = text;
					
					getstr += "&famid=" + escape(famid);
					// getstr += "&famnome=" + escape(famnome);
				}
			}
		});		
		// alert('ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>'+getstr+'&remove='+t);
		// return;
		// var fmm = executa_script(form);		
		if(getstr)
		{
			$.post('avancada_grava.php', 'ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>'+getstr+'&remove='+t, function(data)
			{
				if(t==1) alert('Produtos removidos com sucesso!');
				else alert('Produtos guardados com sucesso!');
				
				submit_fileds();

			}).error(function(request, status, error) 
			{
				alert('error');
			}); 
		}
		
	}
	
	function submit_prd_fileds(t)
	{
		
		if (typeof t == 'undefined') t = '';
		
		var marcaid = ''; var marcanome = ''; var famid = ''; var famnome = ''; var getstr = '';
		$('.formfields').each(function()
		{
			if($(this).find('select#sel_marca').attr('name')!=undefined)
			{
				var name = $(this).find('select#sel_marca').attr('name');
				var value = $(this).find('select#sel_marca').val();		
				var text = $(this).find('select#sel_marca').find(":selected").text();		
				
				if(value!=0 && value!='')
				{ 
					marcaid = value; marcanome = text;
					
					getstr += "&marcaid=" + escape(marcaid);
				}
			}
			if($(this).find('select#m_familia').attr('name')!=undefined)
			{
				var name = $(this).find('select#m_familia').attr('name');
				var value = $(this).find('select#m_familia').val();		
				var text = $(this).find('select#m_familia').find(":selected").text();		
				
				if(value!=0 && value!='')
				{ 
					famid = value;	famnome = text;
					
					getstr += "&famid=" + escape(famid);
				}
			}
			
			
		});	
		$('.cb').each(function()
		{
			
			if($(this).attr('name')!=undefined)
			{
				var name = $(this).attr('name');	
				var isched = $(this).is(':checked');
				var val = $(this).val();
				
				if(isched) getstr += "&prodon_"+val+"=" + escape(val);
				
			}			
		});
		$('.cboff').each(function()
		{
			
			if($(this).attr('name')!=undefined)
			{
				var name = $(this).attr('name');
				var isched = $(this).is(':checked');
				var val = $(this).val();
				
				if(isched) getstr += "&prodoff_"+val+"=" + escape(val);
				
			}			
		});	
		// alert('ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>'+getstr+'&remove='+t+'&prdi=1');
		// return;
		// var fmm = executa_script(form);		
		if(getstr)
		{
			$.post('avancada_grava.php', 'ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>'+getstr+'&remove='+t+'&prdi=1', function(data)
			{
				alert('Alterações feitas com sucesso!');
				submit_fileds();

			}).error(function(request, status, error) 
			{
				alert('error');
			}); 
		}		
	}
	function executa_script(frm)
	{
		frm = document.getElementById(frm);
		var getstr="";
		
		if(!frm || !frm.elements) {return;}
		var elms = frm.elements;

		for(var i = 0, maxI = elms.length; i < maxI; ++i) 
		{	
			var elm = elms[i];
			if (elm.type == "checkbox"){ if (elm.checked) getstr += elm.name + "=" + escape(elm.value) + "&";}		
			if (elm.type == "select-one")  getstr += elm.name + "=" + escape(elm.value) + "&";
			if (elm.type == "password") getstr += elm.name + "=" + escape(elm.value) + "&";
			if (elm.type == "number") getstr += elm.name + "=" + escape(elm.value) + "&";		
			if (elm.type == "tel") getstr += elm.name + "=" + escape(elm.value) + "&";
			if (elm.type == "text")  getstr += elm.name + "=" + escape(elm.value) + "&";
			if (elm.type == "hidden"){ if (elm.value) getstr += elm.name + "=" + escape(elm.value) + "&";}		
			if (elm.type == "radio"){ if (elm.checked) getstr += elm.name + "=" + escape(elm.value) + "&";}	
		}
		return getstr;
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

		if(pagina > 0 && porpagina > 0) window.open('avancada.php?ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>&campo='+campo+'&pagina='+pagina+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else if(pagina > 0) window.open('avancada.php?ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>&campo='+campo+'&pagina='+pagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else if(porpagina > 0) window.open('avancada.php?ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>&campo='+campo+'&total_prods='+porpagina+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
		else window.open('avancada.php?ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>&campo='+campo+'&tipoorder=<?php echo (isset($_GET['tipoorder']) && $_GET['tipoorder']=='ASC') ? 'DESC' : 'ASC';?>'+fields,'_self');
	}	
  
	function makeSelection(selObj,campo,t)
	{
		if(t)
		{
			var selObj2 = document.getElementById(selObj+'2');
			$('#'+selObj).removeAttr('checked');
			if(selObj2 && selObj2.checked==true)
			{
				$('.cboff').attr('checked', 'checked');
				$('.cb').removeAttr('checked');
			}
		
		}
		else
		{
			var selObj2 = document.getElementById(selObj);
			$('#'+selObj+'2').removeAttr('checked');
			if(selObj2 && selObj2.checked==true)
			{
				$('.cb').attr('checked', 'checked');
				$('.cboff').removeAttr('checked');
			}
		}
	}
	$(document).ready(function()
	{	
		$('input[type=checkbox]').click(function()
		{
			var ischecked = '';
			$('input[type=checkbox]').each(function () 
			{
				var sThisVal = (this.checked ? "1" : "0");
				if(sThisVal==1) ischecked = 1;
				
			});
			if(ischecked)
			{
				$('#pred_sel').show();
				$('#pred_sel2').hide();
			}
			else
			{
				$('#pred_sel').hide();
				$('#pred_sel2').show();
			}		
		});
	});
	</script>
    <form name="lista_produtos" method="get" >
    <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
        <td valign="top" align="left">
		<div style="position:relative;height:40px;z-index:999999">
            <div style="position:fixed; width:100%">
			<table height="40" width="100%" border="0" cellspacing="0" cellpadding="0" class="p1" style="background:#606060">
			<tr>
			<td align="left"><div class="smallbtver" style="margin-left:5px" onclick="window.open('avancada.php?ID_PAI=<?php echo $_GET['ID_PAI']?>&Desconto=<?php echo $_GET['Desconto']?>&prdcmp=1','_self')"><span>Ver Produtos associados a esta campanha</span></div></td>
			<td width="170" align="right"><div class="smallbt" id="pred_sel" style="float:right;<?php if(isset($_GET['pesq_id']) && $_GET['pesq_id']!='' || isset($_GET['pesq_ref']) && $_GET['pesq_ref']!='' || isset($_GET['pesq_desig']) && $_GET['pesq_desig']!=''){?>display:block<?php }else{?>display:none<?php }?>" onclick="submit_prd_fileds();"><span>Adicionar/Remover Produtos Selecionados</span></div></td>
			<td width="450" align="right" class="branco">
			<div id="pred_sel2" style="<?php if(isset($_GET['pesq_id']) && $_GET['pesq_id']!='' || isset($_GET['pesq_ref']) && $_GET['pesq_ref']!='' || isset($_GET['pesq_desig']) && $_GET['pesq_desig']!=''){?>display:none<?php }else{?>display:block<?php }?>">
			<?php 
				
				if(isset($_GET['m_familia']) && isset($_GET['sel_marca']) && $_GET['m_familia'] > 0 && $_GET['sel_marca'] > 0 ){?><div class="smallbt" style="float:left" onclick="submit_fams_fileds();"><span>Adicionar Todos Produtos da Marca e Família Selecionda</span></div><div class="smallbtred" style="float:left" onclick="submit_fams_fileds(1);"><span>Eliminar Todos Produtos da Marca e Família Selecionda</span></div><?php }
				elseif(isset($_GET['m_familia']) && $_GET['m_familia'] > 0){?><div class="smallbt" style="float:left" onclick="submit_fams_fileds();"><span>Adicionar Todos Produtos<br>da Família Selecionda</span></div><div class="smallbtred" style="float:left" onclick="submit_fams_fileds(1);"><span>Eliminar Todos Produtos<br>da Família Selecionda</span></div><?php }
				elseif(isset($_GET['sel_marca']) && $_GET['sel_marca'] > 0){?><div class="smallbt" style="float:left" onclick="submit_fams_fileds();"><span>Adicionar Todos Produtos da Marca Selecionda</span></div><div  style="float:left" class="smallbtred" onclick="submit_fams_fileds(1);"><span>Eliminar Todos Produtos da Marca Selecionda</span></div><?php }
			?>
			</div>	
			</td>
			<td width="5"></td>
			</tr>
			</table>
			</div>
		</div>
			<table class="barra_operacoes" width="100%" border="0" cellspacing="0" cellpadding="2" style="background:#FFF">
			<tr> 
				<td	class="preto" width="29" height="20" align="center" style="background:#69ffa9">ON</td>				
				<td	class="preto" width="29" height="20" align="center" style="background:#ff6969">OFF</td>				
				<td	class="preto" width="50" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID")?>')" >ID</td>
				<td	class="preto" width="80" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("products_objs_lang.Cabecalho")?>')" >REF</td>
				<td	class="preto" width="100" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID_Marca")?>')" >MARCA</td>
				<td	class="preto" width="200px" align="center" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("ID_ORG")?>')" >FAMILIA</td>
				<td	class="preto" onclick="submit_fileds('<?php echo $_GET['pagina']?>', '<?php echo $_GET['total_prods']?>','<?php echo base64_encode("nome_produto")?>')">DESIGNAÇÃO</td>		
				<td	class="preto" width="29" align="center">&nbsp;</td>
			</tr>
			<tr>
				<td width="29" align="center" valign="top" style="background:#69ffa9" class="formfields"><input onClick='makeSelection("filter","lista_produtos")' type="checkbox" name="filter" id="filter" value="1" /></td>	
				<td width="29" align="center" valign="top" style="background:#ff6969" class="formfields"><input onClick='makeSelection("filter","lista_produtos",1)' type="checkbox" name="filter2" id="filter2" value="1" /></td>	
				<td width="50" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_id" id="pesq_id" value='<?php echo stripslashes($pesq_id)?>' class="small" /></td>
				<td width="80" align="center" valign="top" class="formfields"><input style="width: 90%;" type="text" name="pesq_ref" id="pesq_ref" value="<?php echo $pesq_ref?>" class="small"></td>
				<td width="100" align="center" valign="top" class="formfields"><?php echo marcas_select("sel_marca", $sel_marca,"150px","onchange='submit_fileds()'");?></td>		
				<td width="200" align="center" valign="top" class="formfields"><?php gera_lista_familiasv(0,'products_families','products_families_lang', '200px', $_GET['m_familia']);?></td>
				<td valign="top" class="formfields"><input style="width: 98%;" type="text" name="pesq_desig" id="pesq_desig" value="<?php echo $pesq_desig?>" class="small" /></td>				
				<td width="29" align="center" style="vertical-align:top" class="formfields"><div class="small" onclick="submit_fileds();" style="cursor:pointer"><img src="avancada/lupa2.png" border="0" title="Pesquisar" alt="Pesquisar" /></div></td>
			</tr>           
            <?php
			
			$sql3 = '
					SELECT 
						PROD_ID
					FROM 
						vouchers_products 
					WHERE 
						promo_id="'.$_GET['ID_PAI'].'"';
				// echo $sql3;
			$obj3->db_open($db_link,$DB_name,$sql3);
			while(!$obj3->EOF())
			{
				$array_data[] = $obj3->db_data['PROD_ID'];
								
				$obj3->db_next();
			}
			$obj3->db_close();
                        
            //**************************OBTER OS FILTROS***********************************************
            if($visivel) $where.=" AND Aproved=1 ";
            
            $aux=getfiltros($pesq_id,"", "products.ID", "num");
            if($aux!="") $where.=" AND " . $aux; 
			
			$aux=getfiltros($pesq_peso,"", "products.Peso", "num");
            if($aux!="") $where.=" AND " . $aux;
                
            $aux=getfiltros($pesq_ref,"","products.Ref", "s");
            if($aux!="") $where.=" AND " . $aux;
        
            $aux=getfiltros($sel_marca,"", "ID_Marca", "ex");
            if($aux!="") $where.=" AND " . $aux;
        
            if(isset($_GET['m_familia']) && $_GET['m_familia'] > 0) $where.=" AND (filters_products.fam_id1='".$_GET['m_familia']."' OR filters_products.fam_id2='".$_GET['m_familia']."')";
            
            $aux=getfiltros($pesq_desig,"", "products_lang.Cabecalho", "s");
            if($aux!="") $where.=" AND " . $aux;
        
            $aux=getfiltros($sel_unvenda,"", "unidade_venda", "ex");
            if($aux!="") $where.=" AND " . $aux;
            
            $aux=getfiltros($pesq_preco,"", "products_objs.obj_pvp", "num");
            if($aux!="") $where.=" AND " . $aux;
            
            // if($visivelweb!="") $where.=" AND Aproved=1";
            
            if($sel_estado==5) $where.=" AND Carrinho=1";            
			elseif($sel_estado==1) $where.=" AND products_objs.obj_destaque=1";
			elseif($sel_estado==2) $where.=" AND (products_objs.obj_promo_ativo=1 and products_objs.obj_promo > 0 && products_objs.obj_promo<>products_objs.obj_pvp)";
			elseif($sel_estado==3) $where.=" AND (products_objs.obj_travelsize=1)";
			elseif($sel_estado==4) $where.=" AND (products_objs.obj_novo=1)";

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
			   
            if($pesq_stock)
            {
                $aux=getfiltros($pesq_stock,"", "products_objs.obj_stock", "num");
                if($aux!="") $where.=" AND " . $aux;
            }
			
			if(isset($_GET['campo']) && $_GET['campo']!='' || isset($_GET['pesq_id']) && $_GET['pesq_id']!='' || isset($_GET['pesq_ref']) && $_GET['pesq_ref']!='' || isset($_GET['pesq_desig']) && $_GET['pesq_desig']!='' || isset($_GET['sel_marca']) && $_GET['sel_marca']!='' || isset($_GET['m_familia']) && $_GET['m_familia']!='' && $_GET['m_familia']!='0')
			{
            /******************************************************************************************/
           /* $sql = '
					SELECT 
						products.*, products_lang.Cabecalho as nome_produto, 
						products.Carrinho as est_carrinho, products_objs.obj_destaque as est_dest,
						if(products_objs.obj_promo_ativo=1 and products_objs.obj_promo > 0 && products_objs.obj_promo<>products_objs.obj_pvp, 1, 0) as est_prom, 
						products_objs.obj_travelsize as est_travel, products_objs.obj_novo as est_novo,
						products_families_lang.Cabecalho as nome_familia,
						products_objs_lang.Cabecalho As Ref,products_objs.obj_pvp as PrecoCusto,
						products_objs.obj_stock as Stock,
						filters_products.fam_id2
					FROM
						products
						LEFT OUTER JOIN products_lang ON products_lang.ID_PAI=products.ID AND products_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN filters_products ON filters_products.prod_id=products.ID
						LEFT OUTER JOIN products_families_lang ON products_families_lang.ID_PAI=filters_products.fam_id1 AND products_families_lang.Lang="'.$admin_lang.'"
						LEFT OUTER JOIN products_objs ON products_objs.obj_type_id=products.ID
						LEFT OUTER JOIN products_objs_lang ON products_objs_lang.ID_PAI=products_objs.obj_id
					';
			
			if($join) $sql .= $join;
            $sql .= ' WHERE 1=1' . $where.$where2;
            if($order) $sql .= ' GROUP BY products.ID';
            if($order) $sql .= ' ORDER BY '.$order;
			if(isset($_GET['log']) && $_GET['log'] > 0) echo '<span style="color:#FFF">'.$sql.'</span>';*/
            

//Novo SQL Alterado por Aderito mais rapido
$sql = 'SELECT products.ID,products.Ref,products.ID_Marca,products.Aproved,products_families_lang.ID_PAI as fam_id2,products_families_lang.Cabecalho,products_lang.Cabecalho AS nome_produto
FROM products
INNER JOIN products_lang ON products_lang.ID_PAI = products.ID AND products_lang.Lang = "'.$admin_lang.'" 
INNER JOIN filters_products ON filters_products.prod_id = products.ID
INNER JOIN products_families_lang ON products_families_lang.ID_PAI = filters_products.fam_id1 AND products_families_lang.Lang = "'.$admin_lang.'"
';


$sql .= ' WHERE 1=1' . $where.$where2;
            if($order) $sql .= ' GROUP BY products.ID';
            if($order) $sql .= ' ORDER BY '.$order;



            
			$dados=new DataObj;
/*echo $sql;*/
			$dados->db_open($db_link,$DB_name,$sql);

			//echo '<span style="color:#000">'.$dados->db_rows.'</span>';
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
					$arr_imagens = get_product_images($prod_id); $arr_img = array();						
					if(count($arr_imagens)>0)
					{
						for ($i=0; $i < count($arr_imagens); $i++)
						{
							$ext = $arr_imagens[$i]['img_ext'];
							$ordem = $arr_imagens[$i]['Ordem'];
							$img_file = 'files/products/'.$prod_id.'_'.$ordem.'.'.$ext;
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
					$cabecalho = $dados->db_data["nome_produto"];
					
					$state = '';

					if($count % 2 != 0)	{$cor = '#F6F6F6'; $n = '1';}
					else				{$cor = '#def2fa'; $n = '2';}
					
					if($count % 2 != 0)	{$corx = '#a3fbc9';$corx2 = '#fd8e8e';}
					else				{$corx = '#69ffa9';$corx2 = '#ff6969';}
					
					$selected = '';
					$selected2 = '';
					if(in_array($prod_id, $array_data))
					{ 
						$selected = "checked='checked'";
						$selected2 = '';						
					}
					else
					{
						$selected2 = "checked='checked'";
						$selected = '';	
					}
					if($aproved){
					?>					
					
					<tr style="background:<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">					
					<td class="preto2X"style="background:<?php echo $corx;?>" width="29" height="30" align="center"><input type="checkbox" name="chk_<?php echo $prod_id?>" id="chk_<?php echo $prod_id?>" value="<?php echo $prod_id?>" class="cb" onclick="if(document.getElementById('echk_<?php echo $prod_id?>')){ if(document.getElementById('chk_<?php echo $prod_id?>').checked==false){document.getElementById('echk_<?php echo $prod_id?>').checked=true;}else{document.getElementById('echk_<?php echo $prod_id?>').checked=false;}}" <?php echo $selected;?> /></td>					
					<td class="preto2X" style="background:<?php echo $corx2;?>" width="29" height="30" align="center"><input type="checkbox" name="echk_<?php echo $prod_id?>" id="echk_<?php echo $prod_id?>" value="<?php echo $prod_id?>" class="cboff" onclick="if(document.getElementById('chk_<?php echo $prod_id?>')){ if(document.getElementById('echk_<?php echo $prod_id?>').checked==false){document.getElementById('chk_<?php echo $prod_id?>').checked=true;}else{document.getElementById('chk_<?php echo $prod_id?>').checked=false;}}"  <?php echo $selected2;?> /></td>					
					<td class="preto2X" width="50" align="center" ><?php echo $prod_id?></td>				
					<td class="preto2X" width="80" align="center" ><?php echo $ref?></td>			
					<td class="preto2X" width="100" align="center" ><?php echo $marca?></td>
					<td class="preto2X" width="200" align="left" ><?php echo $familia?></td>
					<td class="preto2X" ><?php if($cabecalho) echo ($cabecalho); else echo "[Sem nome]"?></td>
					<td class="preto2X"width="29" align="center" class="ordem<?php echo $n?>">&nbsp;</td>
					</tr>					
					
					<?php
					}
					$dados->db_next();	
					$count++;
				}
				
			}
			
			$dados->db_close();
			}
			echo '</table>';
			if(isset($_GET['prdcmp']) && $_GET['prdcmp']==1)
			{
				?>
				<br /><br />
				<table class="barra_operacoes" width="100%" border="0" cellspacing="0" cellpadding="2" style="background:#FFF">
				<tr> 
					<td	class="preto" width="29" height="20" align="center">&nbsp;</td>				
					<td	class="preto" height="40" align="center" style="font-size:14px;font-weight:bold;cursor:default">Produtos associados a esta campanha</td>		
					<td	class="preto" width="29" align="center">&nbsp;</td>
				</tr>
				<tr>
				<tr>
				<td	class="preto" width="29" height="20" align="center">&nbsp;</td>	
				<td colspan="2">
				<?php echo gera_lista_produtosx($_GET['ID_PAI'], 1, $_GET['Desconto']);?>
				</td>
				</tr>
				</table>
				<?php
			}
			?>
			
			
            <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
            <table width="100%" style="background:#606060" border="0" cellspacing="0" cellpadding="0"><tr><td height="5"></td></tr><tr><td>
			<?php 
			if($n_pags)
			{
			?>
			<table border="0" cellspacing="0" cellpadding="0">
			<tr>
			<td height="30" width="50" style="color:#FFF" align="center">Páginas:</td>
			<td>
			<?php 
				
				echo '<input type="hidden" name="total_prods" value="'.$total_prods.'" />';
				?>
				<select name="paginas" onChange='submit_fileds(this.value, "<?php echo $_GET['total_prods']?>");' class="chzn-select" style="width:80px">
				<?php
				for($pi = 1; $pi <= $n_pags; $pi++)
				{

				if($pi!=$pagina){?><option value="<?php echo $pi; ?>"><?php echo $pi?></option><?php }
				else {?><option selected="selected" value="<?php echo $pi?>"><?php echo $pi?></option><?php } 						
				}
				?>
				</select>
			</td>
			<td width="100px"></td>
			<td width="2px"></td>
			<td valign="middle" nowrap="nowrap">
			</td>

			</tr>
			</table>
			<?php }?>
			</td></tr><tr><td height="5"></td></tr></table>			
			</div>

			</form>
        </td>

    </tr>
    </table>
<? 	

function marcas_select($nome_select, $valor, $width="", $funcao="")
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$style = ''; if ($width != '') $style = ';width:' . $width; 
	$rs = new Dataobj;
	
	$sql = 'SELECT brands.ID, brands_lang.Nome FROM brands INNER JOIN brands_lang ON brands.ID=brands_lang.ID_PAI';
	$rs->db_open($db_link,$DB_name,$sql);
	
	echo '<select name="'.$nome_select.'" id="'.$nome_select.'" style="font-size:10px;' . $style . '" ' . $funcao . ' class="small chzn-select" data-placeholder="Escolha uma Moeda" class="chzn-select">';
	echo '<option value=""'; if (!$valor){ echo ' selected ';} echo '>Pesquise Marca</option>';
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
	echo '<select name="'.$nome_select.'" id="sel_estado" style="font-size:10px'.$style.'" class="small chzn-select" '.$funcao.'>';
	for($a=0; $a<count($array_estado); $a++){ echo '<option'; if ($valor==$a){ echo ' selected ';} echo ' value="'.($a>0 ? $a : '').'">'.$array_estado[$a].'</option>'; }
	echo '</select>';	
}


function getFiltros($campo1,$campo2,$campoBD,$tipo)
{
	$tags=array("<",">",">=","<=","=",'"',"'","!","!=");
	$campo1=stripslashes(trim($campo1));
	$campo2=stripslashes($campo2);	
	if($tipo=="ex" || $tipo=="lista"){if($campo1!="")return $campoBD." = '".$campo1."'";}
	elseif($tipo=="s")
	{
		// $campo1=mb_convert_case(str_replace("'",'{p}',$campo1),MB_CASE_LOWER,"ISO-8859-1");
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

function show_breadcrums_recursivo_on($id_pai)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new Dataobj;
	$sql = 'select pf.ID, pf.ID_ORG, pfl.Cabecalho from products_families pf inner join products_families_lang pfl on (pf.ID=pfl.ID_PAI) where pf.visivel=1 and pfl.Lang="'.$GLOBALS['site_lang'].'" and pf.ID="'.$id_pai.'"';
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
    echo '<select name="m_familia" id="m_familia" style="font-size:10px'.$style.'" onchange="submit_fileds()" class="small chzn-select">';
	if($id_org==0) echo '<option value="0" selected>Pesquisa Família</option>';
	else echo '<option value="0">Pesquisa Família</option>';
    while(!$obj->EOF())
	{
		echo '<option value="'.$obj->db_data["ID"].'"';
		if($id_org2==$obj->db_data["ID"]) echo ' selected ';
		$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
		$obj2=new DataObj;
		$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
		echo '>'. htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';			
		$obj2->db_close();
	    gera_lista_subfamiliasv($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang, 2, $id_org2,$obj2->db_data["Cabecalho"]);	    
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
function gera_lista_subfamiliasv($id_start=0,$id_org=0,$tabela1,$tabela1_lang,$contador=2,$id_org2,$cb='')
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
		$i=0;while($i<$contador){echo " ";$i++;} echo " ";
		echo  lower_strx(htmlentities_memo(stripslashes($cb)))." - ".htmlentities_memo(stripslashes($obj2->db_data["Cabecalho"])).'</option>';		
	    $obj2->db_close();
	    gera_lista_subfamiliasv($obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang,$contador+12,$id_org2,$obj2->db_data["Cabecalho"]);	    
        $obj->db_next();
    }
    $obj->db_close();
}
function lower_strx($str)
{
  $str = preg_replace("/Á/","/á/",$str);  $str = preg_replace("/À/","/à/",$str);  $str = preg_replace("/Â/","/â/",$str);  $str = preg_replace("/Ä/","/ä/",$str); $str = preg_replace("/Ã/","/ã/",$str);
  $str = preg_replace("/É/","/é/",$str);  $str = preg_replace("/È/","/è/",$str);  $str = preg_replace("/Ê/","/ê/",$str);  $str = preg_replace("/Ë/","/ë/",$str);
  $str = preg_replace("/Í/","/í/",$str);  $str = preg_replace("/Ì/","/ì/",$str);  $str = preg_replace("/Î/","/î/",$str);  $str = preg_replace("/Ï/","/ï/",$str);
  $str = preg_replace("/Ó/","/ó/",$str);  $str = preg_replace("/Ò/","/ò/",$str);  $str = preg_replace("/Ô/","/ô/",$str);  $str = preg_replace("/Ö/","/ö/",$str); $str = preg_replace("/Õ/","/õ/",$str);
  $str = preg_replace("/Ú/","/ú/",$str);  $str = preg_replace("/Ù/","/ù/",$str);  $str = preg_replace("/Û/","/û/",$str);  $str = preg_replace("/Ü/","/ü/",$str);
  $str = preg_replace("/Ç/","/ç/",$str);
  return strtolower(ltrim(rtrim(str_replace('/','',$str))));
}
?>
<script src="avancada/chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>