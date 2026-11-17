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
	$rs=new DataObj;
	$rs2=new DataObj;
	$rs_copy=new DataObj;
	$rs3=new DataObj;


	if($GLOBALS['update_db']==1)
	{
		$field_array = array();
		$fields=db_list_fields($DB_name,'products',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('preco_revenoff', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD preco_revenoff tinyint(1) DEFAULT 0');  }
		if(!in_array('Ref_secundaria', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_secundaria VARCHAR(255) DEFAULT ""');  }
		if(!in_array('peso_cliente', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD peso_cliente decimal(10,4) DEFAULT 0');  }
		if(!in_array('descontopreco', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD descontopreco tinyint(1) DEFAULT 0');  }
		if(!in_array('points', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD points varchar(200) DEFAULT 0');  }
		if(!in_array('personalizacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD personalizacao text');  }
		if(!in_array('estados_produto', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD estados_produto tinyint(1)');  }
		if(!in_array('encomenda_sem_stock', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD encomenda_sem_stock tinyint(1)');  }
		if(!in_array('tem_prod_destaque_homepage', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD tem_prod_destaque_homepage tinyint(1)');  }
		if(!in_array('prod_estado', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD prod_estado varchar(50)');  }
		if(!in_array('ID_Integracao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD ID_Integracao int(11)');  }
		if(!in_array('ID_Admin', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD ID_Admin int(11)');  }
		if(!in_array('qnt_esp', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD qnt_esp tinyint(1)');  }
		if(!in_array('c_submissao_ficheiro', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD c_submissao_ficheiro tinyint(1)');  }
		if(!in_array('profundidade', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD profundidade decimal(10,1) DEFAULT 0');  }
		if(!in_array('altura', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD altura decimal(10,1) DEFAULT 0');  }
		if(!in_array('largura', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD largura decimal(10,1) DEFAULT 0');  }
		if(!in_array('produto_standard', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD produto_standard tinyint(1) DEFAULT 0');  }
		if(!in_array('servicos', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD servicos varchar(255) DEFAULT ""');  }
		if(!in_array('entregas', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD entregas varchar(255) DEFAULT ""');  }
		// novo campo Preco Custo criado a 2020.11.06
		if(!in_array('preco_custo_custom', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD preco_custo_custom DOUBLE NOT NULL');  }
		if(!in_array('EtiquetaEnergiaNova', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD EtiquetaEnergiaNova tinyint(1) DEFAULT "0"');  }
		if(!in_array('udtime', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD udtime datetime DEFAULT "0000-00-00 00:00:00"');  }
		if(!in_array('idtime', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD idtime datetime DEFAULT "0000-00-00 00:00:00"');  }
		if(!in_array('fornecedor_importacao', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD fornecedor_importacao varchar (255) DEFAULT ""');  }
		if(!in_array('Ref_Ean', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD Ref_Ean varchar (30) DEFAULT ""');  }
		if(!in_array('Video', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD Video varchar (255) DEFAULT ""');  }
		if(!in_array('corEE', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD corEE varchar (10) DEFAULT ""');  }
		if(!in_array('ee_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD ee_ext varchar (5) DEFAULT ""');  }
		if(!in_array('ue_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD ue_ext varchar (5) DEFAULT ""');  }
		if(!in_array('dt_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD dt_ext varchar (5) DEFAULT ""');  }
		if(!in_array('destaque_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD destaque_ext varchar (5) DEFAULT ""');  }
		if(!in_array('destaque2_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD destaque2_ext varchar (5) DEFAULT ""');  }
		if(!in_array('destaque3_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD destaque3_ext varchar (5) DEFAULT ""');  }
		if(!in_array('destaque4_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD destaque4_ext varchar (5) DEFAULT ""');  }

		


		if(!in_array('u_imagetiq', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_imagetiq varchar (255) DEFAULT ""');  }
		if(!in_array('u_catetiq', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_catetiq varchar (255) DEFAULT ""');  }
		if(!in_array('u_coretiq', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD u_coretiq varchar (255) DEFAULT ""');  }


		if(!in_array('margem_fam', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD margem_fam tinyint(1)');  }
		if(!in_array('store_icons', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD store_icons varchar(255) DEFAULT ""');  }
		if(!in_array('bateria', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD bateria int(11) DEFAULT 0');  }
		if(!in_array('estado', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD estado varchar(255) DEFAULT ""');  }
		if(!in_array('localizacao_fisica', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products ADD localizacao_fisica varchar(255) DEFAULT ""');  }

		$fields=db_list_fields($DB_name,'products_objs',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('obj_destaque_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_destaque_data date DEFAULT "0000-00-00"');  }
		if(!in_array('obj_novo_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_novo_data date DEFAULT "0000-00-00"');  }
		if(!in_array('obj_outlet_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_outlet_data date DEFAULT "0000-00-00"');  }
		if(!in_array('obj_promo_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_promo_data date DEFAULT "0000-00-00"');  }
		if(!in_array('obj_dia_data', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_dia_data date DEFAULT "0000-00-00"');  }
		if(!in_array('obj_dia', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_dia tinyint(1) DEFAULT "0"');  }
		if(!in_array('obj_Supplier_ID', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_Supplier_ID int(11) DEFAULT "0"');  }
		if(!in_array('obj_promo_id', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_promo_id tinyint(1) DEFAULT "0"');  }
		if(!in_array('obj_iva_txt', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_iva_txt text DEFAULT ""');  }
		if(!in_array('grau', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD grau varchar (255) DEFAULT ""');  }
		if(!in_array('obj_val2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_objs ADD obj_val2 int (11) DEFAULT "0"');  }

		$fields=db_list_fields($DB_name,'products_lang',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('tempo_entrega', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD tempo_entrega varchar (255)');  }
		if(!in_array('SEO_title', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_title varchar (255)');  }
		if(!in_array('SEO_description', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_description varchar (255)');  }
		if(!in_array('SEO_keywords', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_keywords varchar (255)');  }
		if(!in_array('SEO_alt', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_alt varchar (255)');  }
		if(!in_array('SEO_h1', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_h1 varchar (255)');  }
		if(!in_array('SEO_img', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD SEO_img varchar (5)');  }
		if(!in_array('ICON_img', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE products_lang ADD ICON_img varchar (5)');  }

		$fields=db_list_fields($DB_name,'filters_products',$db_link);
		$columns=db_num_fields($fields);
		for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
		if(!in_array('img_ext', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE filters_products ADD img_ext varchar (5)');  }
	}

	if(!isset($caminho_bt_editor)){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
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
			$encomenda_sem_stock=$rs->db_data["encomenda_sem_stock"];
			$produto_standard=$rs->db_data["produto_standard"];
			$personalizacao = explode(';', $rs->db_data["personalizacao"]);
			$Data_criacao = $rs->db_data["Data_criacao"];
			$Data_update = $rs->db_data["udtime"];
			$ID_Admin = $rs->db_data["ID_Admin"];

			$corEE = $rs->db_data["corEE"];
			$Video = $rs->db_data["Video"];
			$ee_ext = $rs->db_data["ee_ext"];
			$ue_ext = $rs->db_data["ue_ext"];
			$dt_ext = $rs->db_data["dt_ext"];
			$u_catetiq = $rs->db_data["u_catetiq"];
			$u_coretiq = $rs->db_data["u_coretiq"];
			$destaque_ext = $rs->db_data["destaque_ext"];
			$destaque2_ext = $rs->db_data["destaque2_ext"];
			$destaque3_ext = $rs->db_data["destaque3_ext"];
			$destaque4_ext = $rs->db_data["destaque4_ext"];

			$margem_fam = $rs->db_data["margem_fam"];

			$store_icons = $rs->db_data["store_icons"];
			$ar_store_icons = explode(';', $store_icons);
			

			//campos para sites com produtos do tipo gráfica
			$qnt_esp = $rs->db_data["qnt_esp"];
			$c_submissao_ficheiro = $rs->db_data["c_submissao_ficheiro"];
			$profundidade = $rs->db_data["profundidade"];
			$altura = $rs->db_data["altura"];
			$largura = $rs->db_data["largura"];
			$servicos=explode(';',$rs->db_data["servicos"]);
			$entregas=explode(';',$rs->db_data["entregas"]);

			// novo campo Preco Custo criado a 2020.11.06
			$preco_custo_custom = $rs->db_data["preco_custo_custom"];

			//campos novos
			$bateria = $rs->db_data["bateria"];
			$estado = $rs->db_data["estado"];
			$localizacao_fisica = $rs->db_data["localizacao_fisica"];

			if($qnt_esp==1)
			{
				$rs2=new DataObj;
				$sql="SELECT  L_minima, L_max, L_padrao, A_min, A_max, A_padrao from products_qnt_esp where id_prod=$ID";
				$rs2->db_open($db_link,$DB_name, $sql);
				if($rs2)
				{
					$L_minima=$rs2->db_data["L_minima"];
					$L_max=$rs2->db_data["L_max"];
					$L_padrao=$rs2->db_data["L_padrao"];
					$A_min=$rs2->db_data["A_min"];
					$A_max=$rs2->db_data["A_max"];
					$A_padrao=$rs2->db_data["A_padrao"];
				}
				$rs2->db_close();

			}
			/*if($c_submissao_ficheiro==1)
			{
				$rs2=new DataObj;
				$sql="SELECT  dpi, formatos from products_input_file where id_prod=$ID";
				$rs2->db_open($db_link,$DB_name, $sql);
				if($rs2)
				{
					$dpi_min=$rs2->db_data["dpi"];
					$formatos_premitidos=$rs2->db_data["formatos"];

				}
				$rs2->db_close();

			}*/


			if(isset($GLOBALS['CAMPOS_EXTRA']) && count($GLOBALS['CAMPOS_EXTRA']) > 0)
			{
				$i=0;
				while($i<count($GLOBALS['CAMPOS_EXTRA']))
				{
					$GLOBALS['CAMPOS_EXTRA'][$i]['valor']=$rs->db_data[$GLOBALS['CAMPOS_EXTRA'][$i]['campo']];
					$i++;
				}
			}



			$cod_ean    = $rs->db_data["Ref_Ean"];
			$fornecedor = $rs->db_data["fornecedor_importacao"];
			$tem_prod_destaque_homepage = $rs->db_data["tem_prod_destaque_homepage"];
		}
		//********* linguas ***//
		$rs3->db_open($db_link,$DB_name, "Select * from products_lang where ID_PAI=$ID and lang='$admin_lang'");
		$video_id=$rs3->db_data["ID"];
		$C = htmlentities_memo(stripslashes($rs3->db_data["Cabecalho"]));
		$ST = htmlentities_memo(stripslashes($rs3->db_data["Subtitulo"]));
		$T = limpa_editor_lixo(stripslashes($rs3->db_data["Texto1"]));
		$T2 = limpa_editor_lixo(stripslashes($rs3->db_data["Texto2"]));
		$T3 = limpa_editor_lixo(stripslashes($rs3->db_data["Texto3"]));
		$T4 = limpa_editor_lixo(stripslashes($rs3->db_data["Texto4"]));
		$T4 = limpa_editor_lixo(stripslashes($rs3->db_data["Texto4"]));
		$T5 = limpa_editor_lixo(stripslashes($rs3->db_data["Tamanho"]));
		$T6 = limpa_editor_lixo(stripslashes($rs3->db_data["Cor"]));
		$Cabecalho_Texto5 = htmlentities_memo(stripslashes($rs3->db_data["Cabecalho_Texto5"]));
		$Texto5 = limpa_editor_lixo(stripslashes($rs3->db_data["Texto5"]));
		$tempo_entrega = limpa_editor_lixo(stripslashes($rs3->db_data["tempo_entrega"]));
		//SEO
		$SEO_title = htmlentities_memo(stripslashes($rs3->db_data["SEO_title"]));
		$SEO_description = htmlentities_memo(stripslashes($rs3->db_data["SEO_description"]));
		$SEO_keywords = htmlentities_memo(stripslashes($rs3->db_data["SEO_keywords"]));
		$SEO_alt = htmlentities_memo(stripslashes($rs3->db_data["SEO_alt"]));
		$SEO_h1 = htmlentities_memo(stripslashes($rs3->db_data["SEO_h1"]));
		$SEO_img = $rs3->db_data["SEO_img"];

		$SEO_title_preview = $SEO_title != '' ? $SEO_title : $C;
		$SEO_description_preview = $SEO_description != '' ? $SEO_description : $C;
		$rs3->db_close();

		$rs3->db_open($db_link,$DB_name, "Select obj_stock from products_objs where obj_type_id=$ID");
		$stock = $rs3->db_data["obj_stock"];
		$rs3->db_close();

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
<script type="text/javascript" src="../editor/<?php echo EDITOR?>/ckeditor.js"></script>
<script type="text/javascript" src="../editor/<?php echo EDITOR?>/ckfinder/ckfinder.js"></script>
<link rel="stylesheet" type="text/css" href="avancada/chosen2.css?dp=111" />
<link rel="stylesheet" type="text/css" href="avancada/sa.css" />
<script type="text/javascript">
	function ShowSEO(){if(document.getElementById){var el=document.getElementById("SEO");if(el.style.display!="block")el.style.display="block";else el.style.display="none";}}
</script>
<script language="JavaScript">
if(document.getElementById)
{
	document.write('<style type="text/css">\n')
	document.write('.submenu{display: none;}\n')
	document.write('</style>\n')
}
function showmenu_prod(obj,tabId)
{
	document.form.tab_a.value=obj;
	document.form.tab_b.value=tabId;
	if(document.getElementById)
	{
		$("#masterdiv .submenu").hide();
		$("#"+obj).show();
		$("#masterdiv .tab").removeClass("active");
		$("#"+tabId).addClass("active");
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
	val7 = $('#obj_qtdmin0').val();
	$('#c_obj_iva'+a).val(val1);
	$('#c_obj_preco'+a).val(val2);
	$('#c_obj_precosiva'+a).val(val3);
	$('#c_obj_stock'+a).val(val4);
	$('#c_obj_promo'+a).val(val5);
	$('#c_obj_promosiva'+a).val(val6);
	$('#c_obj_qtdmin'+a).val(val7);
}
</script>
<style>
	.input_off{background-color: #ccc}
</style>
<script type="text/javascript" src="../../external_api/fancyBox/jquery.fancybox.pack.js"></script>
<form name="form" id="form" method="post" action="products_trata.php?ID=<? echo $ID?>&ID_ORG=<? echo $ID_Fam?>&task_form=<? echo $task_form?>&dump=<? echo uniqID("")?>"  enctype="multipart/form-data">

	<input type="hidden" name="m_lang" value="<? echo $admin_lang;?>" />
	<input type="hidden" name="slashes" value="'" />
	<input type="hidden" name="tab_a" value="0" />
	<input type="hidden" name="tab_b" value="0" />

	<div class="center">

	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
	<tr>
	<td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
		<div class="panel panel-inverse overflow-hidden m-b-12">
	        <div class="panel-heading">
	            <h4 style="color:#fff">PRODUTOS</h4>
	        </div>
	    </div>

		<div class="panel panel-inverse overflow-hidden m-b-12">
	        <div class="titulo_janela">
	            <h4 style="color:#fff"><a href="list.php"><?php echo write_lang('key_inicio','Início')?></a>
				<?php
				$arr_familias = getListaFamiliasProduto($ID);
				if(count($arr_familias)>0)
				{
					echo ' / ';
					show_products_menu_path($arr_familias[0]['ID'], "products_families.php", "products_families", $total_prods);
					echo ' / '.$Ref.': '.$C;
				}
				?></h4>
	        </div>
	    </div>

		<table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<tr>
		<td valign="top">

		<div id="masterdiv">

		<div class="tabs-bar" role="tablist" aria-label="Ficha de produto">
			<div class="branco tab" onClick="showmenu_prod('sub1','pinta1')" id="pinta1">GERAL</div>
			<div class="branco tab" onClick="showmenu_prod('sub2','pinta2')" id="pinta2"><? echo write_lang('key_imagens_ficheiros','IMAGENS/FICHEIROS')?></div>
			<?php if(isset($prod_conf) && $prod_conf['tem_outras_imagens'] == 1){?><div class="branco tab" onClick="showmenu_prod('sub10','pinta10')" id="pinta10">OUTRAS IMAGENS</div><?php }?>
			<div class="branco tab" onClick="showmenu_prod('sub3','pinta3')" id="pinta3">DESCRIÇÃO</div>
			<div class="branco tab" onClick="showmenu_prod('sub30','pinta30')" id="pinta30">SEO</div>
			<?php if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1){?><div class="branco tab" onClick="showmenu_prod('sub4','pinta4')" id="pinta4"><? echo write_lang('key_caracteristicas','CARACTERÍSTICAS')?></div><?php }?>
			<?php if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1){?><div class="branco tab" onClick="showmenu_prod('sub5','pinta5')" id="pinta5"><? echo write_lang('key_acabamentos', 'ACABAMENTOS')?></div><?php }?>
			<div class="branco tab" onClick="showmenu_prod('sub6','pinta6')" id="pinta6">ESPECIFICAÇÕES</div>
			<?php if(isset($prod_conf) && $prod_conf['tem_etiquetas_energeticas'] == 1){?><div class="branco tab" onClick="showmenu_prod('sub7','pinta7')" id="pinta7">ETIQUETAS ENERGÉTICAS</div><?php }?>
			<div class="branco tab" onClick="load_html('#sub8','tab_vendas.php?ID=<?=$ID?>');showmenu_prod('sub8','pinta8')" id="pinta8">VENDAS</div>
			<?php if(isset($GLOBALS['tem_importadores'] ) && $GLOBALS['tem_importadores'] == 1){?><div class="branco tab" onClick="load_html('#sub9','tab_historico.php?ID=<?=$ID?>');showmenu_prod('sub9','pinta9')" id="pinta9">HISTÓRICO PREÇOS</div><?php }?>
			<div class="branco tab" onClick="showmenu_prod('sub17','pinta17')" id="pinta17">ÍCONES</div>
			<div class="branco tab" onClick="showmenu_prod('sub20','pinta20')" id="pinta20">SEMINOVOS</div>
		</div>

		<div style="position:relative; width: 100%; display:block;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub1"><?php include 'tabs/tab_dados.php'; ?></div>

		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub2"><?php include 'tabs/tab_imagens.php'; ?></div>

		<?php if(isset($prod_conf) && $prod_conf['tem_outras_imagens'] == 1){?>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub10"><?php include 'tabs/tab_outras_imagens.php'; ?></div>
		<?php }?>

		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub3"><?php include 'tabs/tab_descricoes.php'; ?></div>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub30"></div>

		<?php if(isset($prod_conf) && $prod_conf['tem_filtros'] == 1){?>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub4"><?php include 'tabs/tab_filtros.php'; ?></div>
		<?php }else{?>
		<script>$(document).ready(function(){ $('.dropdate').datepicker({showOn: "button", buttonImage: "calendar.png", buttonImageOnly: true, dateFormat: 'yy-mm-dd', changeMonth: true,changeYear: true});});</script>
		<?php }?>

		<?php if(isset($prod_conf) && $prod_conf['tem_acabamentos'] == 1){?>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub5"><?php include 'tabs/tab_acabamentos.php'; ?></div>
		<?php }?>

		<?php //if(isset($prod_conf) && $prod_conf['tem_personalizacao'] == 1){?>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub6"><?php include 'tabs/tab_personalizacao.php'; ?></div>
		<?php //}?>

		<?php if(isset($prod_conf) && $prod_conf['tem_etiquetas_energeticas'] == 1){?>
		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub7"><?php include 'tabs/tab_etiquetas.php'; ?></div>
		<?php }?>

		<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub8"><?php include 'tabs/tab_vendas.php'; ?></div>

		<?php if(isset($GLOBALS['tem_importadores'] ) && $GLOBALS['tem_importadores'] == 1){?>
			<div style="position:relative; width: 100%; display:none;border-radius: 5px 5px 0px 0px;" class="submenu" id="sub9"><?php /*include 'tabs/tab_historico.php';*/ ?></div>
		<?php }?>

		<?php /*  if(isset($prod_conf) && $prod_conf['tem_personalizacao'] == 1){ */?>
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub17"><?php include 'tabs/tab_icones.php'; ?></div>
		<?php //}?>

		<?php /*  if(isset($prod_conf) && $prod_conf['tem_personalizacao'] == 1){ */?>
		<div style="position:relative; width: 100%; display:none;" class="submenu" id="sub20"><?php include 'tabs/tab_seminovos.php'; ?></div>
		<?php //}?>

		</div>
		</td>
		</tr>
		</table>
		<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
		<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
		<tr>
		<td align="center">
			<input class="btn btn-primary" type="button" name="Submit" onclick="verify_fields()" value=" <?php echo $task_button;?> " />
			<input class="btn btn-info" type="button" name="cancelar" value=" Pré-visualizar " onclick="javascript:window.open('<?php echo $GLOBALS['site_http_url'].'index.php?page_id='.$GLOBALS['products_page_id'].'&prod_id='.$ID.'&site_lingua='.$GLOBALS['admin_lang'].'&site_prev=1';?>','_blank');" />
			<input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:window.open('products_families.php?ID=<?php echo (isset($ID_Fam) && $ID_Fam > 0) ? $ID_Fam : ((isset($_GET['ID_ORG']) && $_GET['ID_ORG'] > 0) ? $_GET['ID_ORG'] : 0) ?>','_self')" />
		</td>
		</tr>
		</table>
		<img src="../images/bsh.png" width="100%" height="30px">

	</td>
	<td width="10px">&nbsp;</td>
	<td valign="top" width="200px"><?php show_fast_tools();?><?php show_product_tools();?><br /><?php show_drop_tools()?><br/><? show_seo()?></td>
	</tr>
	</table>
</div>
</form>
<script type="text/javascript">
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
function close_prod(e)
{
	$(e).css({"opacity":"0"});
	$(e).hide();
	$(e).html('');
	var e2 = e+'_x';
	$('html, body').animate({     scrollTop: ($(e2).offset().top-300) + 'px'   }, 1000, 'swing');
}
function close_inquire(e)
{
	$(e).css({"opacity":"0"});
	$(e).hide();
}
function load_prod(e,url)
{
	$(e).load(url,function() {
	$(e).css({"opacity":"0"});
	$(e).show();
	// smooth scroll to the anchor id
	$('html, body').animate({     scrollTop: ($(e).offset().top-150) + 'px'   }, 1000, 'swing');
	$(e).animate({"opacity":"1","transition":"opacity .5s ease-in-out"}, 'slow');
});
}
function load_html(e,url)
{
	$(e).load(url,function() {
		$(e).css({"opacity":"0"});
		$(e).show();
		// smooth scroll to the anchor id
		$('html, body').animate({     scrollTop: ($(e).offset().top-150) + 'px'   }, 1000, 'swing');
		$(e).animate({"opacity":"1","transition":"opacity .5s ease-in-out"}, 'slow');
		$(e).css({"display":"block"});
	});
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
