<?php
	include "settings.inc.php";
	if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}
	
	
	// CONFIGURACAO EM SETTINGS
	if (defined('PROD_CONF')) $prod_conf = unserialize(PROD_CONF); else $prod_conf = array();
	
	if(trim($Nivel)=='') $Nivel=0;
	include "../layout/header.inc.php";	
	include "../layout/body.inc.php";
	
	$tem_caracteristicas = 1;
	
	// REMOTO ---------------------------------
	
	// global $DB_host_remote,$DB_user_remote,$DB_pass_remote,$DB_name_remote;
	// $db_link_remote = db_opendb_conection($DB_host_remote, $DB_user_remote, $DB_pass_remote, $DB_name_remote);
	// $rs_remote = new DataObj;
	// $fields_remote = db_list_fields($DB_name_remote, 'products', $db_link_remote);
	
	// $field_array_remote = array();
	// $columns_remote = db_num_fields($fields_remote);
	// for ($i=0;$i < $columns_remote; $i++){$field_array_remote[] = db_field_name($fields_remote,$i);}
	// if(!in_array('ID_REMOTE', $field_array_remote)){ $res = Runquery($db_link_remote,$DB_pass_remote,'ALTER TABLE products ADD ID_REMOTE INT(11) DEFAULT 0');}

	//-----------------------------------------
	
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;$rs_copy=new DataObj;
	$field_array = array();
	$fields=db_list_fields($DB_name,'products',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('preco_revenoff', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD preco_revenoff tinyint(1) DEFAULT 0');  }
	// if(!in_array('filtro_area', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_products ADD filtro_area tinyint(1) DEFAULT 0');  }
	if(!in_array('peso_cliente', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD peso_cliente decimal(10,4) DEFAULT 0');  }	
	if(!in_array('descontopreco', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD descontopreco tinyint(1) DEFAULT 0');  }	
	if(!in_array('points', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD points varchar(200) DEFAULT 0');  }	
	if(!in_array('personalizacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD personalizacao text');  }
	if(!in_array('estados_produto', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD estados_produto tinyint(1)');  }
	
	$fields=db_list_fields($DB_name,'products_objs',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('obj_destaque_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_destaque_data date DEFAULT "0000-00-00"');  }
	if(!in_array('obj_novo_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_novo_data date DEFAULT "0000-00-00"');  }
	if(!in_array('obj_outlet_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_outlet_data date DEFAULT "0000-00-00"');  }
	if(!in_array('obj_promo_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_promo_data date DEFAULT "0000-00-00"');  }
	if(!in_array('obj_dia_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_dia_data date DEFAULT "0000-00-00"');  }
	if(!in_array('obj_dia', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_dia tinyint(1)');  }
	
	$fields=db_list_fields($DB_name,'products_lang',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
	if(!in_array('tempo_entrega', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD tempo_entrega varchar (255)');  }
	
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

	
	$ID_Fam_remote = 0;	$m_marca_remote = 0;$on = 0; $personalizacao = array();	
	
	$C=''; $T=''; $ID_REMOTE = 0;$ID_REMOTE_INS=0;
	$ordem=0;
	if($ID!=0)
	{
					
		$rs->db_open($db_link,$DB_name, "Select *,products_families_obj.ID_ORG as familia from products left join products_families_obj on products.ID=products_families_obj.ID_PROD where products.ID=$ID order by Ordem Desc");
		if($rs)
		{		
			$ID_Fam=$rs->db_data["familia"];
			$D=str2date($rs->db_data["Data"]);
			$Ref=ltrim(rtrim($rs->db_data["Ref"]));
			$ordem=$rs->db_data["Ordem"];			
			$preco1=$rs->db_data["Preco1"];
			$PrecoCusto=$rs->db_data["PrecoCusto"];
			$preco2=$rs->db_data["Preco2"];
			$preco_prec=$rs->db_data["Preco_prec"];
			$precopromo1=$rs->db_data["PrecoPromo1"];
			$margem=$rs->db_data["Margem"];
			$margem_activa=$rs->db_data["MargemActiva"];
			$destaque=$rs->db_data["Destaque"];
			$decumentario=$rs->db_data["decumentario"];
			$novidade=$rs->db_data["Novidade"];
			$promocao=$rs->db_data["Promo"];
			$home=$rs->db_data["Home"];
			$m_marca=$rs->db_data["ID_Marca"];
			$saldo=$rs->db_data["Saldo"];
			$aproved=$rs->db_data["Aproved"];	
			$carrinho=$rs->db_data["Carrinho"];	
			$tipo=$rs->db_data["Tipo"];
			$strHottags=$rs->db_data["Hottags"];			
			$peso=$rs->db_data["Peso"];	
			$peso_cliente=$rs->db_data["peso_cliente"];	
			$medidas=$rs->db_data["Medidas"];	
			$stock_txt=$rs->db_data["StockTxt"];
			$stock=$rs->db_data["Qtd"];
			$precopromo2=$rs->db_data["PrecoPromo2"];			
			$usado=$rs->db_data["usado"];
			$configurado=$rs->db_data["configurado"];
			$configurador=$rs->db_data["Configurador"];
			$download=$rs->db_data["Download"];
			$sync=$rs->db_data["Sync"];
			$preco_revenoff=$rs->db_data["preco_revenoff"];
			$descontopreco=$rs->db_data["descontopreco"];
			$pontos=$rs->db_data["points"];
			$estados_produto=$rs->db_data["estados_produto"];
			$personalizacao = explode(';', $rs->db_data["personalizacao"]);
		}
		//********* linguas ***//
		$rs->db_open($db_link,$DB_name, "Select * from products_lang where ID_PAI=$ID and lang='$admin_lang'");
		$video_id=$rs->db_data["ID"];
		$C = htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
		$ST = htmlentities_memo(stripslashes($rs->db_data["Subtitulo"]));
		$T = limpa_editor_lixo(stripslashes($rs->db_data["Texto1"]));
		$T2 = limpa_editor_lixo(stripslashes($rs->db_data["Texto2"]));
		$T3 = limpa_editor_lixo(stripslashes($rs->db_data["Texto3"]));
		$T4 = limpa_editor_lixo(stripslashes($rs->db_data["Texto4"]));		
		$T4 = limpa_editor_lixo(stripslashes($rs->db_data["Texto4"]));			
		$T5 = limpa_editor_lixo(stripslashes($rs->db_data["Tamanho"]));
		$T6 = limpa_editor_lixo(stripslashes($rs->db_data["Cor"]));
		$Cabecalho_Texto5 = htmlentities_memo(stripslashes($rs->db_data["Cabecalho_Texto5"]));	
		$Texto5 = limpa_editor_lixo(stripslashes($rs->db_data["Texto5"]));
		$tempo_entrega = limpa_editor_lixo(stripslashes($rs->db_data["tempo_entrega"]));
			
		$rs->db_close();
	}
	else $ID_Fam=$ID_ORG;
	
	
	if(isset($prod_conf) && $prod_conf['tem_bonus'] == 1)
	{
		// BONUS
		if(strpos($pontos, "%") != false)
		{
			$select1 = 'selected="selected"';
			$select2 = '';
			$pontos = str_replace("%", "", $pontos);
		}
		else
		{
			$select2 = 'selected="selected"';
			$select1 = '';
			$pontos = str_replace("€", "", $pontos);
		}
	}
?>
<script type="text/javascript" src="../editor/ckeditor/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor/ckfinder/ckfinder.js"></script>
<link rel="stylesheet" type="text/css" href="avancada/chosen2.css?dp=111" />
<link rel="stylesheet" type="text/css" href="avancada/sa.css" />
<script language="JavaScript">
if(document.getElementById)
{ 
	document.write('<style type="text/css">\n')
	document.write('.submenu{display: none;}\n')
	document.write('</style>\n')
}
function showmenu_prod(obj,obj2){
document.form.tab_a.value=obj;
document.form.tab_b.value=obj2;
if(document.getElementById){

	var el=document.getElementById(obj);
	var el2=document.getElementById(obj2);
	
	var ar=document.getElementById("masterdiv").getElementsByTagName("div"); 
	var ar2=document.getElementById("masterdiv").getElementsByTagName("td"); 
	
		if(el.style.display !="block"){
			for (var i=0; i<ar.length; i++){
				v="sub"+(i+1);
				 if(ar[i].className=="submenu") ar[i].style.display="none";
			}
			
			for (var i=0; i<ar2.length; i++){
				v="sub"+(i+1);
				 if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
			}
			el.style.display="block";
			el2.style.background="#000000";
		}
	}

}
window.onload=function(){<?php if($_GET['t1'] && $_GET['t2']){?>showmenu_prod('<?php echo $t1?>','<?php echo $t2?>');<?php }else{?>showmenu_prod('sub1','pinta1');<?php }?>}

$(function(){ $('.result').hide();});

function get_cs_user()
{
	var t=$('.result').is(':visible');

	if(t==false)
	{
		$('#choose').html('<img src="del2.png" border="0" title="close box" />');
		$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>', function(data) {  $('.result').show("slow"); $('.result').html(data);});
	}
	else
	{
		$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
		$('.result').hide("slow");
	}
}
function totext(div)
{
	$('#m_cliente_url').val($('#'+div).val());
	$('#cor').val($('#_'+div).val());
	$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
	$('.result').hide("slow");
}
function search()
{
	$.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>&pesquisa='+$('#search_by').val(), function(data) { $('.result').html(data);});
}
function empty()
{
	$('#m_cliente_url').val("");
	$('#cor').val(0);
	$('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
	$('.result').hide("slow");
}


function mudaStock(valor)
{
	$("input[name='obj_stock[]']").val(valor);
}
function addToProduct()
{
	$("#s_listafamiliasprod").append($("#s_listafamilias option:selected"));
}

function removeFromProduct()
{
	$("#s_listafamilias").append($("#s_listafamiliasprod option:selected"));
}
function number_format_p(number, decimals, dec_point, thousands_sep)
{
    
    var n = number, prec = decimals;
    var toFixedFix = function (n,prec) 
	{
        var k = Math.pow(10,prec);
        return (Math.round(n*k)/k).toString();
    };

    n = !isFinite(+n) ? 0 : +n;
    prec = !isFinite(+prec) ? 0 : Math.abs(prec);
    var sep = (typeof thousands_sep === 'undefined') ? ',' : thousands_sep;
    var dec = (typeof dec_point === 'undefined') ? '.' : dec_point;

    var s = (prec > 0) ? toFixedFix(n, prec) : toFixedFix(Math.round(n), prec); //fix for IE parseFloat(0.55).toFixed(0) = 0;

    var abs = toFixedFix(Math.abs(n), prec);
    var _, i;

    if (abs >= 1000) 
	{
        _ = abs.split(/\D/);
        i = _[0].length % 3 || 3;

        _[0] = s.slice(0,i + (n < 0)) +
              _[0].slice(i).replace(/(\d{3})/g, sep+'$1');
        s = _.join(dec);
    } else {
        s = s.replace('.', dec);
    }

    var decPos = s.indexOf(dec);
    if (prec >= 1 && decPos !== -1 && (s.length-decPos-1) < prec) {
        s += new Array(prec-(s.length-decPos-1)).join(0)+'0';
    }
    else if (prec >= 1 && decPos === -1) {
        s += dec+new Array(prec).join(0)+'0';
    }
    return s;
}
function handleKeyPress(id, tipo, iva_default)
{
	iva = document.getElementById('obj_iva'+id);
	
	pvpciva = document.getElementById('obj_preco'+id);
	pvpsiva = document.getElementById('obj_precosiva'+id);
	
	pvppromociva = document.getElementById('obj_promo'+id);
	pvppromosiva = document.getElementById('obj_promosiva'+id);
	
	if(iva && iva.value==0) iva.value = number_format_p(iva_default,4,'.','');
	
	if(iva && pvpciva && pvpsiva)
	{	
		if(tipo=='0')
		{
			pvpsiva.value = number_format_p((pvpciva.value / (1 + (iva.value / 100))),4,'.','');
			pvppromosiva.value = number_format_p((pvppromociva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='1') // ATUALIZA PVP SEM IVA
		{
			pvpsiva.value = number_format_p((pvpciva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='2') // ATUALIZA PVP COM IVA
		{
			pvpciva.value = number_format_p((pvpsiva.value * (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='3') // ATUALIZA PVP PROMO SEM IVA
		{
			pvppromosiva.value = number_format_p((pvppromociva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='4') // ATUALIZA PVP PROMO COM IVA
		{
			pvppromociva.value = number_format_p((pvppromosiva.value * (1 + (iva.value / 100))),4,'.','');
		}
	}	
}
function handleKeyPress_combo(id, tipo, iva_default)
{
	iva = document.getElementById('c_obj_iva'+id);
	
	pvpciva = document.getElementById('c_obj_preco'+id);
	pvpsiva = document.getElementById('c_obj_precosiva'+id);
	
	pvppromociva = document.getElementById('c_obj_promo'+id);
	pvppromosiva = document.getElementById('c_obj_promosiva'+id);
	
	if(iva && iva.value==0) iva.value = number_format_p(iva_default,4,'.','');
	
	if(iva && pvpciva && pvpsiva)
	{	
		if(tipo=='0')
		{
			pvpsiva.value = number_format_p((pvpciva.value / (1 + (iva.value / 100))),4,'.','');
			pvppromosiva.value = number_format_p((pvppromociva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='1') // ATUALIZA PVP SEM IVA
		{
			pvpsiva.value = number_format_p((pvpciva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='2') // ATUALIZA PVP COM IVA
		{
			pvpciva.value = number_format_p((pvpsiva.value * (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='3') // ATUALIZA PVP PROMO SEM IVA
		{
			pvppromosiva.value = number_format_p((pvppromociva.value / (1 + (iva.value / 100))),4,'.','');
		}
		else if(tipo=='4') // ATUALIZA PVP PROMO COM IVA
		{
			pvppromociva.value = number_format_p((pvppromosiva.value * (1 + (iva.value / 100))),4,'.','');
		}
	}	
}

 function get_precos(a)
{							
	val1 = $('#obj_iva0').val();		
	val2 = $('#obj_preco0').val();		
	val3 = $('#obj_precosiva0').val();		
	val4 = $('#obj_stock0').val();		
	val5 = $('#obj_promo0').val();		
	val6 = $('#obj_promosiva0').val();	
	
	$('#c_obj_iva'+a).val(val1);
	$('#c_obj_preco'+a).val(val2);
	$('#c_obj_precosiva'+a).val(val3);
	$('#c_obj_stock'+a).val(val4);
	$('#c_obj_promo'+a).val(val5);
	$('#c_obj_promosiva'+a).val(val6);
}
</script>
<form name="form" id="form" method="post" action="products_trata.php?ID=<? echo $ID?>&ID_ORG=<? echo $ID_Fam?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>"  enctype="multipart/form-data">
	
	<input type="hidden" name="m_lang" value="<? echo $admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<input type="hidden" name="tab_a" value="0" />
	<input type="hidden" name="tab_b" value="0" />

	<div class="center">

	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
	<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
		
		<table class="new_quadros" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
		<tr> 
		<td valign="top">
					
		<table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
		<tr>
		<td>
		<a href="list.php" class="new_caminho"><?php echo write_lang('key_inicio','Início')?></a> 
		<?php 
		$arr_familias = getListaFamiliasProduto($ID);
		if(count($arr_familias)>0)
		{
			echo ' - ';
			show_products_menu_path($arr_familias[0]['ID'], "products_families.php", "products_families", $total_prods);
		}
		?>
		</td>
		</tr>
		</table>
						
		<div id="masterdiv"><br />			
			
		<table height="20" border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td width="120" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer;background:#000" id="pinta1">GERAL</td>
		<td><img src="cm_fill.gif" width="8" height="1"></td>						
		<td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2"><? echo write_lang('key_imagens_ficheiros','IMAGENS/FICHEIROS')?></td>
		<td><img src="cm_fill.gif" width="8" height="1"></td>
		<td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="cursor:pointer" id="pinta3">DESCRIÇÃO</td>
		
		<?php if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1){?>
		<td><img src="cm_fill.gif" width="8" height="1"></td>
		<td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub4','pinta4')" style="cursor:pointer" id="pinta4"><? echo write_lang('key_caracteristicas','CARACTERÍSTICAS')?></td>
		<?php }?>
		
		<?php if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1){?>
		<td><img src="cm_fill.gif" width="8" height="1"></td>
		<td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub5','pinta5')" style="cursor:pointer" id="pinta5"><? echo write_lang('key_acabamentos', 'ACABAMENTOS')?></td>
		<?php }?>
		
		<?php if(isset($prod_conf) && $prod_conf['tem_personalizacao'] == 1){?>
		<td><img src="cm_fill.gif" width="8" height="1"></td>
		<td width="120" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub6','pinta6')" style="cursor:pointer" id="pinta6">PERSONALIZAÇÃO</td>
		<?php }?>
		
		</tr>
		</table> 
						
		<div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1"><?php include 'tabs/tab_dados.php'; ?></div>
		
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2"><?php include 'tabs/tab_imagens.php'; ?></div>  
		
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3"><?php include 'tabs/tab_descricoes.php'; ?></div>
		
		<?php if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1){?>
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub4"><?php include 'tabs/tab_filtros.php'; ?></div>
		<?php }else{?>
		<script>$(document).ready(function(){ $('.dropdate').datepicker({showOn: "button", buttonImage: "calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});});</script>
		<?php }?>                    
		
		<?php if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1){?>
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub5"><?php include 'tabs/tab_acabamentos.php'; ?></div>
		<?php }?>
		
		<?php if(isset($prod_conf) && $prod_conf['tem_personalizacao'] == 1){?>
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub6"><?php include 'tabs/tab_personalizacao.php'; ?></div>
		<?php }?>
		
		</div>
		</td>
		</tr>
		</table>
					
					
		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>

		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td>&nbsp;
		<input class="botao" type="button" name="Submit" onclick="verify_fields()" value=" <?php echo $task_button;?> " /> 
		<input class="botao" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:window.open('products_families.php?ID=<?php echo (isset($ID_Fam) && $ID_Fam > 0) ? $ID_Fam : ((isset($_GET['ID_ORG']) && $_GET['ID_ORG'] > 0) ? $_GET['ID_ORG'] : 0) ?>','_self')" /> <input class="botao" type="button" name="cancelar" value=" Pré-visualizar " onclick="javascript:window.open('<?php echo $GLOBALS['site_http_url'].'pages.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$ID.'&site_prev=1';?>','_blank');" /></td>
		</tr>
		</table>
		
	</td>   
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200px"><?php show_fast_tools();?><?php show_product_tools();?><br /><?php show_drop_tools()?></td>
	</tr>
	</table>
	
</div>
</form>
<script src="avancada/chosen.jquery.min2.js?dp=<?php echo dumpcache?>" type="text/javascript"></script>
<script type="text/javascript"> 
$(".chzn-select").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
$(".chzn-select2").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
$(".chzn-select3").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
$(".chzn-select4").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "300px", enable_new_filter:false});
$(".chzn-select5").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", width: "180px", enable_new_filter:false});
$(".chzn-select6").chosen({disable_search_threshold: 10,  no_results_text: "Não foram encontrados resultados!", enable_new_filter:false});
 
function verify_fields()
{
	if(document.getElementById('form'))
	{ 
		if(document.getElementById('marca').value=='' || document.getElementById('marca').value==0 || document.getElementById('m_familia').value=='' || document.getElementById('m_familia').value==0)
		{
			alert('Campos: Marca e Familias são obrigatórios!');
		}
		else
		{
			document.getElementById('form').submit();			
		}
	}	
}
</script>
</body>
</html>
<?php
function gera_lista_familias_cat($ID,$id_org=0,$tabela1,$tabela1_lang)
{
    global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;
	$sql='SELECT PF.ID FROM products_families PF  WHERE  PF.ID_ORG="0" and PF.ID NOT IN (SELECT ID_ORG from products_families_obj where ID_PROD='.$ID.') order by PF.Ordem Asc';
	$hRes=$obj->db_open($db_link,$DB_name,$sql);
    echo '<select id="s_listafamilias" name="s_listafamilias" multiple="multiple" style="width:100%;height:65px;font-size:9px;">';
    while(!$obj->EOF())
	{        

			echo '<option value="'.$obj->db_data["ID"].'"';
			if($id_org==$obj->db_data["ID"] || $ID_Fam==$obj->db_data["ID"]) echo ' selected ';
			$sql="Select * from $tabela1_lang where ID_PAI=".$obj->db_data["ID"]." and Lang='$admin_lang'";
			$obj2=new DataObj;
			$hRes=$obj2->db_open($db_link,$DB_name,$sql);   
			echo '>'.$obj2->db_data["Cabecalho"].'</option>';			
			$obj2->db_close();

	    gera_lista_subfamilias_produto($ID,$obj->db_data["ID"],$id_org,$tabela1,$tabela1_lang);	    
        $obj->db_next();
    }
    $obj->db_close();
    echo '</select>';
}
?>
