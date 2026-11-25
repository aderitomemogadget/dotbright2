<?php
include "settings.inc.php";
$software_arr = array();
$hardware_arr = array();
set_time_limit(0);
ini_set('max_execution_time','9999999999');
ini_set('memory_limit','2048M');

if ($task == 'apagar') {$task_button = 'APAGAR REGISTO';$task_form='apaga';}
elseif ($task == 'duplicar') {$task_button = 'DUPLICAR REGISTO';$task_form='duplicar';}
elseif ($ID == 0){$task_button = 'GRAVAR NOVO REGISTO'; $task_form='insere';}
else {$task_button = 'GRAVAR ALTERAÇŐES';$task_form='altera';}

if (trim($Nivel) == "") $Nivel=0;
$rma_full_sheet_mobile = true;
include '../layout/header.inc.php';
include '../layout/body.inc.php';
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
$rs=new DataObj;
$rs2=new DataObj;
$rs3=new DataObj;



$field_array = array();
$fields = db_list_fields($DB_name,'clientes',$db_link);
$columns = db_num_fields($fields);
for ($i=0; $i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
if(!in_array('Sobrenome', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Sobrenome varchar(255) DEFAULT ""');  }
if(!in_array('Telefone2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD Telefone2 varchar(255) DEFAULT ""');  }
if(!in_array('rma_associado', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE clientes ADD rma_associado tinyint(1) DEFAULT 0');  }

$field_array = array();
$fields = db_list_fields($DB_name,'rmas_logs',$db_link);
$columns = db_num_fields($fields);
for ($i=0; $i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
if(!in_array('POST', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas_logs ADD POST LONGTEXT NOT NULL;');  }
if(!in_array('DADOS_ENVIO', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas_logs ADD DADOS_ENVIO LONGTEXT NOT NULL;');  }


$field_array = array();
$fields = db_list_fields($DB_name,'rmas',$db_link);
$columns = db_num_fields($fields);
for ($i=0; $i < $columns;$i++){$field_array[]=db_field_name($fields,$i);}
if(!in_array('preorcamento', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD preorcamento decimal(4,2) DEFAULT 0;');  }
if(!in_array('hardware_sp', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD hardware_sp tinyint(1) DEFAULT 0;');  }
if(!in_array('ID_Cliente2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD ID_Cliente2 INT(11) DEFAULT 0;');  }
if(!in_array('ID_Cliente2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD ID_Cliente2 INT(11) DEFAULT 0;');  }
if(!in_array('signed', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD signed blob');  }
if(!in_array('signed2', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD signed2 blob');  }

if(!in_array('signed_date', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD signed_date date');  }
if(!in_array('signed2_date', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD signed2_date date');  }
if(!in_array('lida', $field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE rmas ADD lida tinyint(1) DEFAULT 0');  }

$grupo = get_user_grupo($_SESSION['ss_admin_id']);

// echo 'grupo: '.$grupo.'<br>';


if($grupo == 5 || $grupo == 6) $m_origem = 3;
elseif($grupo == 7 || $grupo == 8) $m_origem = 2;
else $m_origem = 3;


if (!$caminho_bt_editor) {$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
else {$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}

$C = ''; $T = ''; $ordem = 0; $readonly = 0;
$rma_repair_order_id = 0;

if ($ID != 0)
{
    $rs->db_open($db_link,$DB_name,'SELECT * FROM rmas WHERE ID="'.$ID.'"');
    if ($rs)
    {
        $IDb = $rs->db_data['ID'];
        $rma_repair_order_id = (int)($rs->db_data['repair_order_id'] ?? 0);
        $ID_Cliente = $rs->db_data['ID_Cliente'];
		$ID_Cliente2 = $rs->db_data['ID_Cliente2'];

        $m_origem = $rs->db_data['m_origem'];
        $m_estado = $rs->db_data['m_estado'];

		$estados_rmas = estado_encomenda_rmas($m_estado, 'pt');

		$readonly = $estados_rmas['readonly'];
		if(isset($_GET['reabrir']) && intval($_GET['reabrir']) == 1) $readonly = 0;

        $ndocumento = $rs->db_data['ndocumento'];

        $Grupo_ID = $rs->db_data['Grupo_ID'];
        $cliente_telefone1 = $rs->db_data['cliente_telefone1'];
        $cliente_telefone2 = $rs->db_data['cliente_telefone2'];
        $cliente_nome = $rs->db_data['cliente_nome'];
        $cliente_sobrenome = $rs->db_data['cliente_sobrenome'];
        $cliente_apelido = $rs->db_data['cliente_apelido'];
        $cliente_morada = $rs->db_data['cliente_morada'];
        $cliente_cp = $rs->db_data['cliente_cp'];
        $cliente_username = $rs->db_data['cliente_username'];
        $fake = $rs->db_data['fake'];
        $cliente_empresa = $rs->db_data['cliente_empresa'];
        $cliente_nif = $rs->db_data['cliente_nif'];
        $codigo_desbloqueio = $rs->db_data['codigo_desbloqueio'];
        $m_referencia = $rs->db_data['m_referencia'];
        $m_marca = $rs->db_data['m_marca'];
        $m_modelo = $rs->db_data['m_modelo'];
        $m_imei = $rs->db_data['m_imei'];
        $m_nserie = $rs->db_data['m_nserie'];
        $m_avaria = $rs->db_data['m_avaria'];
        $m_estado_equipamento = $rs->db_data['m_estado_equipamento'];
        $m_estado_equipamento_text = $rs->db_data['m_estado_equipamento_text'];
        $m_acessorios = $rs->db_data['m_acessorios'];
        $m_observacoes = $rs->db_data['m_observacoes'];
        $notes= $rs->db_data['notes'];
        $lida= $rs->db_data['lida'];
        $m_observacoestec = $rs->db_data['m_observacoestec'];
        $m_valor = $rs->db_data['m_valor'];
        $m_tecnico = $rs->db_data['m_tecnico'];
        $m_novo_imei = $rs->db_data['m_novo_imei'];
        $m_observacoestec_relatorio = $rs->db_data['m_observacoestec_relatorio'];
        $m_obsint = $rs->db_data['m_obsint'];
        $zone = $rs->db_data['sel_local'];

        $tipo_reparacao_po = $rs->db_data['tipo_reparacao_po'];
        $tipo_reparacao_gl = $rs->db_data['tipo_reparacao_gl'];
        $tipo_reparacao_gf = $rs->db_data['tipo_reparacao_gf'];
        $tipo_reparacao_gr = $rs->db_data['tipo_reparacao_gr'];

        $tipo_reparacao = $rs->db_data['tipo_reparacao'];

        $software_dr=$rs->db_data['software_dr'];
        $software_rc=$rs->db_data['software_rc'];
        $software_na=$rs->db_data['software_na'];
        $software_cg=$rs->db_data['software_cg'];
        $software_ci=$rs->db_data['software_ci'];
        $software_bl=$rs->db_data['software_bl'];
        $hardware_tp=$rs->db_data['hardware_tp'];
        $hardware_tnf=$rs->db_data['hardware_tnf'];
        $hardware_lp=$rs->db_data['hardware_lp'];
        $hardware_lpb=$rs->db_data['hardware_lpb'];
        $hardware_nc=$rs->db_data['hardware_nc'];
        $hardware_ssa=$rs->db_data['hardware_ssa'];
        $hardware_sa=$rs->db_data['hardware_sa'];
        $hardware_sam=$rs->db_data['hardware_sam'];
        $hardware_sal=$rs->db_data['hardware_sal'];
        $hardware_saa=$rs->db_data['hardware_saa'];
        $hardware_nl=$rs->db_data['hardware_nl'];
        $hardware_ncrg=$rs->db_data['hardware_ncrg'];
        $hardware_ncrw=$rs->db_data['hardware_ncrw'];
        $hardware_svl=$rs->db_data['hardware_svl'];
        $hardware_cfnf=$rs->db_data['hardware_cfnf'];
        $hardware_cpnf=$rs->db_data['hardware_cpnf'];
        $hardware_bhn=$rs->db_data['hardware_bhn'];
        $hardware_bvn=$rs->db_data['hardware_bvn'];
        $hardware_bsn=$rs->db_data['hardware_bsn'];
        $hardware_bof=$rs->db_data['hardware_bof'];
        $hardware_caj=$rs->db_data['hardware_caj'];
        $hardware_lsim=$rs->db_data['hardware_lsim'];
        $hardware_lmsd=$rs->db_data['hardware_lmsd'];
        $hardware_lc=$rs->db_data['hardware_lc'];
        $hardware_sp=$rs->db_data['hardware_sp'];

        $m_padrao=$rs->db_data['m_padrao'];

        $m_pago=$rs->db_data['m_pago'];
        $m_aceite=$rs->db_data['m_aceite'];
        $total=$rs->db_data['total'];
        $preorcamento=$rs->db_data['preorcamento'];


		if($software_dr) $software_arr[] = 'Desbloqueio à REDE';
		if($software_rc) $software_arr[] = 'Remover Código de Segurança';
		if($software_na) $software_arr[] = 'Não arranca';
		if($software_cg) $software_arr[] = 'Remover Conta Google';
		if($software_ci) $software_arr[] = 'Remover Conta iCloud';
		if($software_bl) $software_arr[] = 'Bloqueia / está lento';


		if($hardware_tp) $hardware_arr[] = 'Touch partido';
		if($hardware_tnf) $hardware_arr[] = 'Touch não funciona';
		if($hardware_lp) $hardware_arr[] = 'LCD partido';
		if($hardware_lpb) $hardware_arr[] = 'LCD em branco/preto';
		if($hardware_nc) $hardware_arr[] = 'Não carrega';
		if($hardware_ssa) $hardware_arr[] = 'Sem sinal áudio no auscultador';
		if($hardware_sa) $hardware_arr[] = 'Sinal áudio no auscultador baixo ou com Ruído';
		if($hardware_sam) $hardware_arr[] = 'Sem sinal áudio no microfone';
		if($hardware_sal) $hardware_arr[] = 'Sem sinal áudio no altifalante';
		if($hardware_saa) $hardware_arr[] = 'Sinal áudio no altifalante baixo ou com Ruído';
		if($hardware_nl) $hardware_arr[] = 'Não Liga';
		if($hardware_ncrg) $hardware_arr[] = 'Não capta REDE GSM';
		if($hardware_ncrw) $hardware_arr[] = 'Não capta REDE WIFI/bluetooth';
		if($hardware_svl) $hardware_arr[] = 'Lente LCD partido(LCD OK) 7 dias';
		if($hardware_cfnf) $hardware_arr[] = 'Camera frontal não funciona';
		if($hardware_cpnf) $hardware_arr[] = 'Camera principal não funciona';
		if($hardware_bhn) $hardware_arr[] = 'Botão Home não funciona';
		if($hardware_bvn) $hardware_arr[] = 'Botão de Volume não funciona';
		if($hardware_bsn) $hardware_arr[] = 'Botão de Silêncio não funciona';
		if($hardware_bof) $hardware_arr[] = 'Botão ON/OFF não funciona';
		if($hardware_caj) $hardware_arr[] = 'Conector audioJack não funciona';
		if($hardware_lsim) $hardware_arr[] = 'Leitor SIM';
		if($hardware_lmsd) $hardware_arr[] = 'Leitor MicroSD';
		if($hardware_lc) $hardware_arr[] = 'Lente da camera partida';
		if($hardware_sp) $hardware_arr[] = 'Sensor de proximidade';


        
    }
    $rs->db_close();
} else $ID_Fam=$ID_ORG;

if($ID){
    $rs_emp=new DataObj;
    $rs_emp->db_open($db_link,$DB_name,'SELECT * FROM rmas_emp WHERE id_rma="'.$ID.'"');
    
    if(!$rs_emp->EOF()){
        $m_marca_emp=$rs_emp->db_data['marca'];
        $m_modelo_emp=$rs_emp->db_data['modelo'];
        $m_imei_emp=$rs_emp->db_data['imei'];
        $m_estado_emp=$rs_emp->db_data['estado'];
    }
    $rs_emp->db_close();
}

?>
<script src='../editor/scripts/innovaeditor.js'></script>

<script>

    function enterAsTab(e) {
        event=e || window.event; // compliant with ie6

        var keyPressed=event.keyCode; // get the Key that is pressed

        if (keyPressed == 13)
		{
            var $focused = $(':focus').attr('type') ||  $(':focus').prop('tagName') ||  $(':focus').attr('type');

			if($focused != 'TEXTAREA')
			{
				//case the KeyPressed is the [Enter]
				var inputs=$('input'); // storage a array of Inputs
				var a=inputs.index(document.activeElement);
				//get the Index of Active Element Input inside the Inputs(array)

				if (inputs[a + 1] !== null) {
					// case the next index of array is not null
					var nextBox=inputs[a + 1];
					nextBox.focus(); // Focus the next input element. Make him an Active Element
					event.preventDefault();
				}

				return false;
			}
        }

        else {
            return keyPressed;
        }
    }


    if (document.getElementById) {
        document.write('<style type="text/css">\n')
        document.write('.submenu{display:none}\n')
        document.write('</style>\n')
    }
    function showmenu_prod(obj,obj2)
	{
        document.form.tab_a.value=obj;
        document.form.tab_b.value=obj2;
        if (document.getElementById) {
            var el=document.getElementById(obj);
            var el2=document.getElementById(obj2);
            var ar=document.getElementById("masterdiv").getElementsByTagName("div");
            var ar2=document.getElementById("masterdiv").getElementsByTagName("td");
            if (el.style.display != "block") {
                for (var i=0; i < ar.length; i++) {
                    v="sub" + (i + 1);
                    if (ar[i].className == "submenu") ar[i].style.display="none";
                }
                for (var i=0; i < ar2.length; i++) {
                    v="sub" + (i + 1);
                    if (ar2[i].className == "branco") ar2[i].style.background="#5694B4";
                }
                el.style.display="block";
                el2.style.background="#000000";


            }
        }
    }

	function pesqCliente(texto, tipo)
	{

		if(texto != '')
		{
			$.ajax({
			type: "GET",
			url: "getCliente4.php",
			data:'keyword='+texto,
			beforeSend: function(){
				$("#cliente_pesquisa").css("background","#FFF url(LoaderIcon.gif) no-repeat 270px");
			},
			success: function(data){
				$("#cliente_pesquisa").val(texto);
				$("#cliente_pesquisa").css("background","#FFF");
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data)
				$('html, body').animate({scrollTop: ($("#cliente_pesquisa").offset().top-20) }, 500);
				showmenu_prod('sub1','pinta1');
				}
			});
		}
		else
		{
			if(tipo == 'nif')alert('Digite Nif para pesquisar!');
			else if(tipo == 'tel1') alert('Digite Telefone 1 para pesquisar!');
			else if(tipo == 'tel2') alert('Digite Telefone 2 para pesquisar!');
			else if(tipo == 'email') alert('Digite E-mail para pesquisar!');
			else alert('Digite E-mail ou Telefone ou Nif para obter informações');
			showmenu_prod('sub1','pinta1');
		}

	}


    window.onload=function () {
        <?php if(isset($_GET['tab']) && $_GET['tab']!=''){?>showmenu_prod('sub<?php echo $_GET['tab']?>','pinta<?php echo $_GET['tab']?>');
        <? }else{?>showmenu_prod('sub1','pinta1');<? }?>}

	$(function ()
	{
        $('.result').hide();

		$("#cli_click").click(function()
		{
			if($("#cliente_pesquisa").val() != '')
			{
				$.ajax({
				type: "GET",
				url: "getCliente4.php",
				data:'keyword='+$("#cliente_pesquisa").val(),
				beforeSend: function(){
					$("#cliente_pesquisa").css("background","#FFF url(LoaderIcon.gif) no-repeat 270px");
				},
				success: function(data){

					$("#cliente_pesquisa").css("background","#FFF");
					$("#suggesstion-box").show();
					$("#suggesstion-box").html(data);

				}
				});
			}
			else
			{
				alert('Digite email ou telefone ou nif para obter informações');
			}
		});

		$("#cliente_pesquisa").keyup(function()
		{
			$.ajax({
			type: "GET",
			url: "getCliente4.php",
			data:'keyword='+$(this).val(),
			beforeSend: function(){
				$("#cliente_pesquisa").css("background","#FFF url(LoaderIcon.gif) no-repeat 180px");
			},
			success: function(data){

				$("#cliente_pesquisa").css("background","#FFF");
				$("#suggesstion-box").show();
				$("#suggesstion-box").html(data);

			}
			});
		});
    });
	function selectClient(val, tipo)
	{
		var array_cli = val.split('[split]');
		if (array_cli.length > 0)
		{
			$('#ID_Cliente').val(array_cli[0]);
			if(array_cli[0] > 0)
			{
				$('#cliente_associado').html('<img src="ok.png" border="0" align="absmiddle" style="vertical-align:middle"> <span style="color:#0078d7;font-weight:bold">Assistência Associada a Cliente Website</span>');
				$('#novo_cliente3').attr('checked','checked');
				setupLabel();
			}

			// if($('#cliente_nome').val() == '' || tipo==1) $('#cliente_nome').val(array_cli[1]);
			// if($('#cliente_apelido').val() == '' || tipo==1) $('#cliente_apelido').val(array_cli[2]);
			// if($('#cliente_username').val() == '' || tipo==1) $('#cliente_username').val(array_cli[3]);
			// if($('#cliente_morada').val() == '' || tipo==1) $('#cliente_morada').val(array_cli[7]);
			// if($('#cliente_cp').val() == '' || tipo==1) $('#cliente_cp').val(array_cli[8]);
			// if($('#cliente_telefone').val() == '' || tipo==1) $('#cliente_telefone').val(array_cli[4]);
			// if($('#cliente_empresa').val() == '' || tipo==1) $('#cliente_empresa').val(array_cli[5]);
			// if($('#cliente_nif').val() == '' || tipo==1) $('#cliente_nif').val(array_cli[6]);
			// if($('#cliente_telefone1').val() == '' || tipo==1) $('#cliente_telefone1').val(array_cli[9]);
			// if($('#cliente_telefone2').val() == '' || tipo==1) $('#cliente_telefone2').val(array_cli[10]);
			// if($('#cliente_sobrenome').val() == '' || tipo==1) $('#cliente_sobrenome').val(array_cli[11]);
			$('#cliente_nome').val(array_cli[1]);
			$('#cliente_apelido').val(array_cli[2]);
			$('#cliente_username').val(array_cli[3]);
			$('#cliente_morada').val(array_cli[7]);
			$('#cliente_cp').val(array_cli[8]);
			$('#cliente_telefone').val(array_cli[4]);
			$('#cliente_empresa').val(array_cli[5]);
			$('#cliente_nif').val(array_cli[6]);
			$('#cliente_telefone1').val(array_cli[9]);
			$('#cliente_telefone2').val(array_cli[10]);
			$('#cliente_sobrenome').val(array_cli[11]);
			$('#Grupo_ID option[value="'+array_cli[12]+'"]').attr("selected", "selected");
			$("select").trigger("chosen:updated");
			$('select').trigger("liszt:updated");

			$("#suggesstion-box").hide();
		}

	}
	function selectClient2(val, tipo)
	{
		var array_cli = val.split('[split]');
		if (array_cli.length > 0)
		{
			$('#ID_Cliente2').val(array_cli[0]);
			if(array_cli[0] > 0)
			{
				$('#cliente_associado').html('<img src="ok.png" border="0" align="absmiddle" style="vertical-align:middle"> <span style="color:#0078d7;font-weight:bold">Assistência Associada a Cliente RMA<br>(sem conta website)</span>');
				$('#novo_cliente3').attr('checked','checked');
				setupLabel();
			}

			if($('#cliente_nome').val() == '' || tipo==1) $('#cliente_nome').val(array_cli[1]);
			if($('#cliente_apelido').val() == '' || tipo==1) $('#cliente_apelido').val(array_cli[2]);
			if($('#cliente_username').val() == '' || tipo==1) $('#cliente_username').val(array_cli[3]);
			if($('#cliente_morada').val() == '' || tipo==1) $('#cliente_morada').val(array_cli[7]);
			if($('#cliente_cp').val() == '' || tipo==1) $('#cliente_cp').val(array_cli[8]);
			if($('#cliente_telefone').val() == '' || tipo==1) $('#cliente_telefone').val(array_cli[4]);
			if($('#cliente_empresa').val() == '' || tipo==1) $('#cliente_empresa').val(array_cli[5]);
			if($('#cliente_nif').val() == '' || tipo==1) $('#cliente_nif').val(array_cli[6]);
			if($('#cliente_telefone1').val() == '' || tipo==1) $('#cliente_telefone1').val(array_cli[9]);
			if($('#cliente_telefone2').val() == '' || tipo==1) $('#cliente_telefone2').val(array_cli[10]);
			if($('#cliente_sobrenome').val() == '' || tipo==1) $('#cliente_sobrenome').val(array_cli[11]);
			$('#Grupo_ID option[value="'+array_cli[12]+'"]').attr("selected", "selected");
			$("select").trigger("chosen:updated");
			$('select').trigger("liszt:updated");
			$("#suggesstion-box").hide();
		}

	}
    function get_cs_user() {
        var t=$('.result').is(':visible');
        if (t == false) {
            $('#choose').html('<img src="del2.png" border="0" title="close box" />');
            $.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>',function (data) {
                $('.result').show("slow");
                $('.result').html(data);
            });
        }
        else {
            $('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
            $('.result').hide("slow");
        }
    }
    function totext(div) {
        $('#m_cliente_url').val($('#' + div).val());
        $('#cor').val($('#_' + div).val());
        $('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
        $('.result').hide("slow");
    }
    function search() {
        $.get('../pages/get_youtube.php?dp=<? echo date("YmdHis")?>&pesquisa=' + $('#search_by').val(),function (data) {
            $('.result').html(data);
        });
    }
    function empty() {
        $('#m_cliente_url').val("");
        $('#cor').val(0);
        $('#choose').html('<img src="config.png" border="0"  title="choose a video" />');
        $('.result').hide("slow");
    }
     function getCliente(tipo,valor)
	{
		// console.log($('#ID_Cliente').val()+' '+);
		if($('#ID_Cliente').val() > 0 || ($('#cliente_username').val() !='' && $('#cliente_nome').val() !='' && verifyEmail('cliente_username')))
		{

		}
		else
		{
			$.get('getCliente.php?' + tipo + '=' + valor,function (data)
			{
				if (data.trim() != '')
				{
					var array_cli = data.split('&;');
					if (array_cli.length > 0)
					{
						$('#ID_Cliente').val(array_cli[0]).attr('title',array_cli[0]);
						$('#cliente_nome').val(array_cli[1]).attr('title',array_cli[1]);
						$('#cliente_apelido').val(array_cli[2]).attr('title',array_cli[2]);
						$('#cliente_username').val(array_cli[3]).attr('title',array_cli[3]);
						$('#cliente_morada').val(array_cli[7]).attr('title',array_cli[7]);
						$('#cliente_cp').val(array_cli[8]).attr('title',array_cli[8]);
						$('#cliente_telefone').val(array_cli[4]).attr('title',array_cli[4]);
						$('#cliente_empresa').val(array_cli[5]).attr('title',array_cli[5]);
						$('#cliente_nif').val(array_cli[6]).attr('title',array_cli[6]);
						$('#cliente_telefone1').val(array_cli[9]).attr('title',array_cli[9]);
						$('#cliente_telefone2').val(array_cli[10]).attr('title',array_cli[10]);
						$('#cliente_sobrenome').val(array_cli[11]).attr('title',array_cli[11]);
						// $('#Grupo_ID').val(array_cli[12]);
						alert(array_cli[12]);
						// $('#element option[value="no"]').attr("selected", "selected");
						// $(".chzn-select").chosen();
					}
					$('#dados_cliente').show();
				}
			});
		}
    }

    function getProduto(valor,linha) {
        var PVP=$('#pvp_reven').is(':checked');
        if (PVP)
            PVP=1;
        else PVP=0;
        $.get('getProduto.php?ref=' + valor + '&PVP=' + PVP,function (data) {
            if (data != '') {
                var array_cli=data.split('&;');
                if (array_cli.length > 0) {
                    $('input[name="desc[]"]').eq(linha).val(array_cli[1]);
                    $('input[name="prec[]"]').eq(linha).val(array_cli[2]);
                }
            }
        });
    }
    function validaCampos()
	{
        var fake = document.getElementById('fake').checked;
        if(!fake)
        {
        	var campos=['cliente_nome','cliente_sobrenome','cliente_apelido','cliente_morada','cliente_cp','cliente_username','cliente_telefone1','m_imei'];
        	var alertas=[' - Nome \n',' - Sobrenome \n',' - Apelido \n',' - Morada \n',' - Código Postal \n',' - Email \n',' - Telefone 1 \n',' - Imei \n'];
        }
        else
        {
        	var campos=['cliente_nome','cliente_sobrenome','cliente_apelido','cliente_morada','cliente_cp','cliente_telefone1','m_imei'];
        	var alertas=[' - Nome \n',' - Sobrenome \n',' - Apelido \n',' - Morada \n',' - Código Postal \n',' - Telefone 1 \n',' - Imei \n'];
        	$('#cliente_username').removeClass('box_err');
        }
        
        var alerta='';
        for (var i=0; i < campos.length; i++)
		{
            if (document.getElementById(campos[i]).value == '')
			{
				alerta += alertas[i];
				$('#'+campos[i]).addClass('box_err');
			}
			else if(campos[i]=='cliente_username' && !verifyEmail('cliente_username'))
			{
				alerta += ' - Email não é válido \n';
				$('#'+campos[i]).addClass('box_err');
			}
			else
			{
				$('#'+campos[i]).removeClass('box_err');
			}
        }

		if(document.getElementById('parent') && (document.getElementById('parent').value == 19 || document.getElementById('parent').value == 20))
		{
			if(document.getElementById('sel_local') && (document.getElementById('sel_local').value == 0 || document.getElementById('sel_local').value == ''))
			{
				alerta += ' - "Localização do equipamento" é de escolha obrigatória (Tab: "RELATORIO REPARAÇÃO") \n';
			}
		}

        if (alerta != '')
		{
			alert('Faltam os seguintes campos: \n' + alerta);
			return false;
		}
        else
		{
			// SE NÃO EXISTIR ID DE CLIENTE VAMOS VERIFICAR SE A INFORMAÇÃO
			if(document.getElementById('ID_Cliente').value == 0 || document.getElementById('ID_Cliente').value == '')
			{
				var data = testClient();
				if (data.trim() != '')
				{
					// alert(2);
					var array_cli = data.split('&;');
					if (array_cli.length > 0)
					{
						if($.trim(document.getElementById('cliente_username').value) == $.trim(array_cli[3]))
						{
							alert('O campo E-mail que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
							pesqCliente(array_cli[3]);
							return false;
						}
						else if($.trim(document.getElementById('cliente_telefone1').value) == $.trim(array_cli[4]) )
						{
							alert('O campo Telefone1 que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
							pesqCliente(array_cli[4]);
							return false;
						}
						else if($.trim(document.getElementById('cliente_telefone2').value) == $.trim(array_cli[4]))
						{
							pesqCliente(array_cli[4]);
							alert('O campo Telefone2 que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
							return false;
						}
						else if($.trim(document.getElementById('cliente_nif').value) == $.trim(array_cli[6]))
						{
							pesqCliente(array_cli[6]);
							alert('O campo Nif que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
							return false;
						}
						else
						{
							// alert(5);
							return true;
						}
						return false;
					}
					else
					{
					// alert(6);
						return true;
					}
					return false;
				}
				else
				{
					// alert(3);
					return true;
				}

				/* $.ajax({
					url: 'getCliente3.php',
					global: false,
					type: 'GET',
					data: {email: document.getElementById('cliente_username').value, tel:document.getElementById('cliente_telefone1').value, tel2: document.getElementById('cliente_telefone2').value, nif:document.getElementById('cliente_nif').value},
					async: false, //blocks window close
					success: function(data)
					{
						alert(1);

						return false;
					}
				}); */


				/* $.get('getCliente3.php?email=' + document.getElementById('cliente_username').value+'&tel=' + document.getElementById('cliente_telefone1').value+'&tel2=' + document.getElementById('cliente_telefone2').value+'&nif=' + document.getElementById('cliente_nif').value,function (data)
				{
					alert(1);
					if (data.trim() != '')
					{
						alert(2);
						var array_cli = data.split('&;');
						if (array_cli.length > 0)
						{
							if($.trim(document.getElementById('cliente_username').value) == $.trim(array_cli[3]))
							{
								alert('O campo E-mail que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
								pesqCliente(array_cli[3]);
								return false;
							}
							else if($.trim(document.getElementById('cliente_telefone1').value) == $.trim(array_cli[4]) )
							{
								alert('O campo Telefone1 que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
								pesqCliente(array_cli[4]);
								return false;
							}
							else if($.trim(document.getElementById('cliente_telefone2').value) == $.trim(array_cli[4]))
							{
								pesqCliente(array_cli[4]);
								alert('O campo Telefone2 que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
								return false;
							}
							else if($.trim(document.getElementById('cliente_nif').value) == $.trim(array_cli[6]))
							{
								pesqCliente(array_cli[6]);
								alert('O campo Nif que introduziu já existe na base dados, por favor, use a caixa de pesquisa para associar o cliente!');
								return false;
							}
							else
							{
								alert(5);
								return true;
							}
							return false;
						}
						else
						{	alert(6);
							return true;
						}
						return false;
					}
					else
					{
						alert(3);
						return true;
					}
					return false;
				});	 */
				return false;
			}
			else
			{
				// alert(4);
				return true;
			}
		}
    }
	function verifyEmail(campo)
	{
		var status = false;

		var emailRegEx = /^[A-Z0-9._%+-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i;
		if (document.getElementById(campo).value.search(emailRegEx) == -1)
		{
			return false;
		}

		return true;

	}

	function testClient() {
		var jqXHR = $.ajax({
			url: 'getCliente3.php',
			global: false,
			type: 'GET',
			data: {email: document.getElementById('cliente_username').value, tel:document.getElementById('cliente_telefone1').value, tel2: document.getElementById('cliente_telefone2').value, nif:document.getElementById('cliente_nif').value},
			async: false //blocks window close
		});
		return jqXHR.responseText;
	}


    function pesqCampo(tipo) {

        var valor='';
        var id=''
        if (tipo == 'TipoEquip')
            id='m_tipoequip';
        else if (tipo == 'Marca')
            id='m_marca';
        else if (tipo == 'Modelo')
            id='m_modelo';

        valor=$('#' + id).val();

        if (valor != '') {
            $.get('getArrays.php',{campo: tipo,valor: valor}).done(function (data) {
                if (data != '') {
                    var array=data.split(';');
                    $("#" + id).autocomplete({source: array});
                }
            });
        }

    }
</script>

<style type="text/css">
    .patt-holder{background:#3382c0;-ms-touch-action:none}.patt-wrap{position:relative;cursor:pointer}.patt-wrap li,.patt-wrap ul{list-style:none;margin:0;padding:0}.patt-circ{position:relative;float:left;box-sizing:border-box;-moz-box-sizing:border-box}.patt-circ.hovered{border:3px solid #090}.patt-error .patt-circ.hovered{border:3px solid #BA1B26}.patt-hidden .patt-circ.hovered{border:0}.patt-dots,.patt-lines{border-radius:5px;height:10px;position:absolute}.patt-dots{background:#FFF;width:10px;top:50%;left:50%;margin-top:-5px;margin-left:-5px}.patt-lines{background:rgba(255,255,255,.7);transform-origin:5px 5px;-ms-transform-origin:5px 5px;-webkit-transform-origin:5px 5px}.patt-hidden .patt-lines{display:none}

    .mhn-ui-date-time,
    .text-center {
        text-align: center
    }
    *,
    :after,
    :before {
        box-sizing: border-box
    }
    .pull-left {
        float: left
    }
    .pull-right {
        float: right
    }
    .clearfix:after,
    .clearfix:before {
        content: '';
        display: table
    }
    .clearfix:after {
        clear: both;
        display: block
    }
    body {
        margin: 0;
        color: #fff;
    }
    a {
        color: inherit;
        text-decoration: none
    }
    a:hover {
        text-decoration: underline
    }
    .mhn-ui-wrap {
        width: 300px;
        height: 475px;
        overflow: hidden;
        position: relative;
        margin: 30px auto 0;
        background: url(http://raw.githubusercontent.com/khadkamhn/lock-screen/master/img/mhn-ui-bg.jpg) center no-repeat #2c3e50;
        box-shadow: 0 17px 50px 0 rgba(0,0,0,.19),0 12px 15px 0 rgba(0,0,0,.24)
    }
    .mhn-ui-wrap:before {
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        content: '';
        position: absolute;
        background: rgba(0,0,0,.4)
    }
    .mhn-ui-date-time {
        color: #eee;
        z-index: 100;
        position: relative
    }
    .mhn-ui-date-time .mhn-ui-time {
        font-size: 28px;
        font-weight: 400;
        margin-bottom: 15px
    }
    .mhn-ui-date-time .mhn-ui-day {
        font-size: 24px;
        margin-bottom: 10px
    }
    .mhn-ui-date-time .mhn-ui-date {
        font-size: 18px;
        font-weight: 400
    }
    .mhn-ui-app-time {
        padding: 0 5px;
        font-size: 12px;
        text-align: right;
        margin: -15px -15px auto;
        background: rgba(0,0,0,.6)
    }
    .mhn-lock-wrap {
        left: 0;
        right: 0;
        bottom: 0;
        z-index: 100;
        position: absolute
    }
    .mhn-lock-wrap .mhn-lock-title {
        text-align: center;
        text-shadow: 0 1px 1px rgba(0,0,0,.5)
    }
    .mhn-lock-wrap .mhn-lock-success {
        color: transparent;
        text-shadow: none
    }
    .mhn-lock-wrap .mhn-lock-failure {
        color: #f34235
    }
    .mhn-lock {
        margin: auto;
        background: 0 0
    }
    .patt-wrap {
        margin: auto;
        overflow: hidden
    }
    .patt-wrap li {
        transition: all .4s ease-in-out 0s
    }
    .patt-dots,
    .patt-lines {
        transition: background .1s ease-in-out 0s
    }
    .patt-circ {
        border: 3px solid transparent
    }
    .patt-dots {
        background: rgba(255,255,255,.8)
    }
    .patt-lines {
        background: rgba(255,255,255,.4)
    }
    .patt-circ.hovered {
        border-color: #ddd;
        background: rgba(255,255,255,.2)
    }
    .patt-error .patt-circ.hovered {
        background: rgba(255,255,255,.2);
        border-color: #ddd;
    }
    .patt-error .patt-lines {
        background: rgba(255,255,255,.4);
    }
    .patt-success .patt-circ.hovered {
        background: rgba(75,174,79,.4);
        border-color: rgba(75,174,79,.8)
    }
    .patt-success .patt-lines {
        background: rgba(75,174,79,.5)
    }
    .mhn-ui-page {
        height: 100%;
        z-index: 200;
        display: none;
        padding: 15px;
        position: relative
    }
    .mhn-ui-page.page-lock {
        position: initial
    }
    .mhn-ui-page .mhn-ui-app-title-head {
        padding: 15px;
        font-size: 16px;
        margin: 0 -15px;
        background: rgba(0,0,0,.4)
    }
    .mhn-ui-page .mhn-ui-filter {
        float: right;
        position: relative
    }
    .mhn-ui-page .mhn-ui-filter .mhn-ui-btn {
        right: 0;
        top: -5px;
        padding: 5px;
        cursor: pointer;
        position: absolute;
        display: inline-block
    }
    .mhn-ui-page .mhn-ui-filter .mhn-ui-btn.active {
        background: teal
    }
    .mhn-ui-page .mhn-ui-filter-list {
        right: 0;
        top: 20px;
        padding: 5px;
        width: 180px;
        display: none;
        position: absolute;
        background: rgba(0,0,0,.8)
    }
    .mhn-ui-page .mhn-ui-filter-list>div {
        display: block;
        font-size: 14px;
        cursor: pointer;
        padding: 2px 4px
    }
    .mhn-ui-page .mhn-ui-filter-list>div.active {
        color: teal
    }
    .mhn-ui-page .mhn-ui-filter-list>div:hover {
        background: teal
    }
    .mhn-ui-page .mhn-ui-filter-list>div.active:hover {
        background: 0 0
    }
    .mhn-ui-page .mhn-ui-row {
        margin-top: 15px
    }
    .mhn-ui-page .mhn-ui-row:after,
    .mhn-ui-page .mhn-ui-row:before {
        content: '';
        display: table
    }
    .mhn-ui-page .mhn-ui-row:after {
        clear: both;
        display: block
    }
    .mhn-ui-page .mhn-ui-col {
        width: 25%;
        float: left;
        margin-bottom: 15px
    }
    .mhn-ui-bottom-link-bar {
        left: 0;
        right: 0;
        bottom: 0;
        padding: 15px;
        position: absolute;
        text-align: center
    }
    .mhn-ui-bottom-link-bar .mhn-ui-bottom-btn {
        width: 40px;
        height: 40px;
        cursor: pointer;
        font-size: 28px;
        line-height: 40px;
        text-align: center;
        border-radius: 50%;
        display: inline-block
    }
    .mhn-ui-bottom-link-bar .mhn-ui-bottom-btn:nth-child(1) {
        margin-right: 15px
    }
    .mhn-ui-bottom-link-bar .mhn-ui-bottom-btn:nth-child(2) {
        margin-left: 15px
    }
    .mhn-ui-bottom-link-bar .mhn-ui-bottom-btn:hover {
        color: #ccc;
        background: rgba(0,0,0,.8)
    }
    .mhn-ui-icon {
        text-align: center
    }
    .mhn-ui-icon span {
        width: 55px;
        height: 55px;
        margin: auto;
        display: block;
        font-size: 28px;
        cursor: pointer;
        line-height: 55px;
        text-align: center;
        border-radius: 15px;
        background: rgba(0,0,0,.3);
        transition: background .4s ease-in-out 0s;
        box-shadow: 0 -1px 0 rgba(255,255,255,.5) inset
    }
    .mhn-ui-icon .mhn-ui-icon-title {
        margin-top: 5px;
        cursor: default;
        overflow: hidden;
        font-size: 13px;
        text-overflow: ellipsis;
        text-shadow: 0 1px 1px rgba(0,0,0,.5)
    }
    .mhn-ui-page.page-author img {
        padding: 8px;
        margin-top: 15px;
        border-radius: 50%;
        background: rgba(255,255,255,.7)
    }
    .mhn-ui-credit {
        padding: 5px;
        font-size: 13px;
        margin-top: 15px;
        background: rgba(0,0,0,.2);
        border: 1px solid rgba(0,0,0,.2)
    }
    .mhn-ui-credit p {
        margin: 0;
        color: #aaa
    }
    .mhn-ui-credit a {
        font-weight: 500
    }
    .mhn-ui-dialog-wrap {
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        display: none;
        z-index: 1000;
        position: absolute;
        background: rgba(0,0,0,.7)
    }
    .mhn-ui-dialog {
        padding: 15px;
        background: #000;
        margin: 50% 0 auto
    }
    .mhn-ui-dialog .mhn-ui-dialog-title {
        font-size: 18px;
        font-weight: 500
    }
    .mhn-ui-dialog .mhn-ui-dialog-btn {
        padding: 5px;
        min-width: 65px;
        cursor: pointer;
        margin-right: 10px;
        text-align: center;
        display: inline-block;
        border: 2px solid rgba(255,255,255,.8)
    }
    .mhn-ui-dialog .mhn-ui-dialog-btn:hover {
        background: #009688;
        text-decoration: none
    }
    .mhn-ui-info {
        margin: 30px 0;
        font-size: 16px;
        text-align: center
    }
    .mhn-ui-date,
    .mhn-ui-time {
        animation: zoomIn 1s
    }
    .mhn-ui-day {
        animation: rubberBand 1s
    }
    .mhn-lock-failure {
        animation: zoomIn .4s
    }
    .patt-circ:nth-child(1),
    .patt-circ:nth-child(2),
    .patt-circ:nth-child(3) {
        animation: fadeInUp .4s
    }
    .patt-circ:nth-child(4),
    .patt-circ:nth-child(5),
    .patt-circ:nth-child(6) {
        animation: fadeInUp .6s
    }
    .patt-circ:nth-child(7),
    .patt-circ:nth-child(8),
    .patt-circ:nth-child(9) {
        animation: fadeInUp .8s
    }
    .mhn-ui-icon span {
        animation: zoomIn .6s
    }
    .mhn-ui-bottom-btn {
        animation: bounceInUp .8s
    }
    .mhn-ui-credit-list .mhn-ui-credit:nth-child(1) {
        animation: fadeInUp .4s
    }
    .mhn-ui-credit-list .mhn-ui-credit:nth-child(2) {
        animation: fadeInUp .5s
    }
    .mhn-ui-credit-list .mhn-ui-credit:nth-child(3) {
        animation: fadeInUp .6s
    }
    .mhn-ui-credit-list .mhn-ui-credit:nth-child(4) {
        animation: fadeInUp .7s
    }
    .mhn-ui-credit-list .mhn-ui-credit:nth-child(5) {
        animation: fadeInUp .8s
    }
    .frmSearch {border: 1px solid #a8d4b1;background-color: #c6f7d0;margin: 2px 0px;padding:10px;border-radius:4px;}
    #country-list{float:left;list-style:none;margin-top:0;padding:0;width:652px;position: absolute;z-index:999999;border-radius:4px; background: #c6f7d0;}
    #country-list li{padding: 10px; background: #c6f7d0; border-bottom: #bbb9b9 1px solid;}
    #country-list li:hover{background:#ece3d2;cursor: pointer;}
    /* #cliente_pesquisa{padding: 10px;border: #a8d4b1 1px solid;border-radius:4px;} */
</style>

<body class="rma-form-page rma-operational-form" onKeyDown="enterAsTab(event)">

    <?php if ($rma_repair_order_id > 0) { ?>
    <p><a href="../../admin/reparacoes/pagina.php?id=<?=$rma_repair_order_id?>">Pedido de origem REP-<?=$rma_repair_order_id?></a> — consulte as moradas RRE, os valores estimados e a opção de acesso no pedido de origem.</p>
    <?php } ?>

    <form class="rma-modern-form" name="form" id="rma_form" method="post" action="pagina_trata.php?ID=<?php echo $ID ?>&task_form=<?php echo $task_form ?>&d=<?php echo uniqID('') ?>" enctype="multipart/form-data" onsubmit="return validaCampos()">
        <input type="hidden" name="m_lang" value="<?php echo $GLOBALS['admin_lang']; ?>">
        <input type="hidden" name="slashes" value="'">
        <input type="hidden" name="tab_a" value="0">
        <input type="hidden" name="tab_b" value="0">
        <input type="hidden" name="ID" value="<?php echo $ID?>">
        <input name="ID_Cliente" id="ID_Cliente" type="hidden" value="<?php echo $ID_Cliente; ?>">
        <input name="ID_Cliente2" id="ID_Cliente2" type="hidden" value="<?php echo $ID_Cliente2; ?>">

        <div class="center">
            <table class="t3 rma-form-shell" align="center" border=0 cellpadding=0 cellspacing=0>
                <tr>
                    <td class="rma-form-main" valign="top" align="left">
                        <? include "../layout/menus.inc.php"; ?>
                        <br>
                        <table class="new_quadros rma-form-card rma-record-card" width="880px" border=0 cellspacing=0 cellpadding="4" align="center">
                            <tr>
                                <td valign="top">
                                    <table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
                                        <tr>
                                            <td valign="middle"><a href="list.php" class="brancoh"> &#60; Voltar para a lista de <?php echo MODULO?></a> </td>
                                        </tr>
                                    </table>
                                </td>
                            </tr>
							<?php if($readonly == 0) { ?>
							<tr>
								<td valign="top">
									<table width="100%" border="0" cellspacing="0" cellpadding="6" style="background:#333039;border:1px solid #000">
										<tr>
											<td align="left">
												<input class="botao" type="submit" name="SubmitTop" value="<?php echo $task_button; ?>" style="width:190px;height:30px" />
												<input class="botao" type="button" name="cancelar_top" value=" CANCELAR " onMouseUp="window.open('list.php','_self')" style="width:120px;height:30px" />
												<?php if($ID > 0) { ?>
												<input class="botao" type="button" value="IMPRIMIR RECEPÇÃO" onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $ID; ?>&tipo=1&n_folhas=1&d=<?php echo uniqid(''); ?>','_blank')" style="width:160px;height:30px" />
												<input class="botao" type="button" value="IMPRIMIR INTERVENÇÃO" onclick="window.open('../gera_pdf5.php?idtracking=<?php echo $ID; ?>&tipo=2&n_folhas=1&d=<?php echo uniqid(''); ?>','_blank')" style="width:170px;height:30px" />
												<?php } ?>
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<?php } ?>
                            <tr>
                                <td valign="top">
                                    <div id="masterdiv">
                                        <table class="rma-record-tabs" height="20" border="0" cellspacing="0" cellpadding="0" width="100%">
                                            <tr>
                                                <td width="120" height="30" bgcolor="#000000" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1');<?php if($readonly == 1){?>disable_all();$('.chzn-select').prop('disabled', true).trigger('chosen:updated');<?php }?>" style="cursor:pointer;background:#000" id="pinta1">RESUMO</td>
                                                <td><img src="cm_fill.gif" width="8" height="1"></td>
                                                <td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3');<?php if($readonly == 1){?>disable_all();$('.chzn-select').prop('disabled', true).trigger('chosen:updated');<?php }?>" style="cursor:pointer" id="pinta3">RELATORIO REPARAÇÃO</td>
                                                <td><img src="cm_fill.gif" width="8" height="1"></td>
                                                <td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub4','pinta4');<?php if($readonly == 1){?>disable_all();$('.chzn-select').prop('disabled', true).trigger('chosen:updated');<?php }?>" style="cursor:pointer" id="pinta4">HISTÓRICO </td>
                                                <td><img src="cm_fill.gif" width="8" height="1"></td>
                                                <td width="220" bgcolor="#5694B4" align="center" class="branco" onClick="showmenu_prod('sub5','pinta5');<?php if($readonly == 1){?>disable_all();$('.chzn-select').prop('disabled', true).trigger('chosen:updated');<?php }?>" style="cursor:pointer" id="pinta5">Telefone Empréstimo</td>
                                                <td align="right" class="preto">Utilizador: <?php echo $admin_user?></td>
                                            </tr>
                                        </table>

                                        <table width="100%" border=0 cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1 rma-record-status">
                                        <tr>
                                            <td class="preto">
                                                <table border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="120">Estado do RMA:</td>
                                                        <td>
                                                            <select id="parent" name="m_estado" class="form-control chzn-select">
                                                            <?php
                                                            // ARRAY NO SETTINGS
                                                            if(isset($GLOBALS['estados_rmas_cab']))
                                                            {

                                                                if($_GET['reabrir']==1)$m_estado=1;
                                                                for($a=0; $a<count($GLOBALS['estados_rmas_cab']); $a++)
                                                                {
                                                                    echo '<optgroup label="'.$GLOBALS['estados_rmas_cab'][$a].'">';
                                                                    show_rmas_estados($a, $m_estado);
                                                                    echo '</optgroup>';
                                                                }
                                                            }
                                                            ?>
                                                            </select>
                                                        </td>
                                                        <td><img src="cm_fill.gif" width="8" height="1"></td>
                                                        <td>
                                                            <label class="label_check" for="nao_envio_email"><input type="checkbox" id="nao_envio_email" name="nao_envio_email" value="1" <?php if(isset($_GET['reabrir']) && intval($_GET['reabrir']) == 1){ echo 'checked="checked"';}?>>Não enviar e-mail ao Cliente</a>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                            <td align="right" class="preto">
                                                <table border="0" cellspacing="0" cellpadding="0">
                                                    <tr>
                                                        <td width="100" align="center">Proveniência: </td>
                                                        <td>
                                                            <select id="m_origem" name="m_origem" class="form-control chzn-select" style="width:150px">
                                                                <?php
                                                                // ARRAY EM SETTINGS
                                                                for($a=1; $a<=count($GLOBALS['lojas_rmas']); $a++)
                                                                {
                                                                    if($a == 1) echo '<option value="'.$a.'" '.((!$m_origem || $m_origem == $a) ? ' selected="selected"' : '').' >'.$GLOBALS['lojas_rmas'][$a].'</option>';
                                                                    else echo '<option value="'.$a.'" '.(($m_origem == $a) ? ' selected="selected"' : '').' >'.$GLOBALS['lojas_rmas'][$a].'</option>';
                                                                }
                                                                ?>
                                                            </select>
                                                        </td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                        </table>
                                        <div style="position:relative; width: 100%; display:block;" class="submenu" id="sub1"><?php include 'tabs/resumo.php';?></div>
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3"><?php include 'tabs/relatorio_reparacao.php';?></div>
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub4"><?php include 'tabs/historico.php';?></div>
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub5"><?php include 'tabs/tel_emp.php';?></div>
                                    </div>
                                </td>
                            </tr>
                        </table>
                        <?php 
                            if($readonly == 0) {
                                ?>
                                <table width="100%" height="5px" border="0" cellspacing="0" cellpadding="0"><tr><td></td></tr></table>
                                <table width="100%" border="0" cellspacing="0" cellpadding="0" style="border:#000 1px solid;background:#333039;height:40px">
                                    <tr>
                                        <td align="center">
                                        <input class="botao" type="submit" name="Submit" value="<?php echo $task_button; ?>" style="width:190px;height:30px" />
                                        <input class="botao" type="button" name="cancelar" value=" CANCELAR " onMouseUp="window.open('list.php','_self')"  style="width:190px;height:30px" />
                                    </tr>
                                </table>
                                <?php 
                            }
                        ?>
                        <br>
                    </td>
                    <td width="10px">&nbsp;</td>
                    <td valign="top" width="200px"><br/><br/><br/><br/><?php show_fast_tools2(); ?><br/></td>
                </tr>
            </table>
        </div>
    </form>

    <script src="<?php echo $GLOBALS['site_http_url']?>rmas/layout/choosen/chosen.jquery.min.js" type="text/javascript"></script>

    <script type="text/javascript">
        <?php if($readonly == 1){?><?php }else{?>$(".chzn-select").chosen();<?php }?>
    </script>

</body>
</html>

<script>
    function showpattern(){
        var pattern = document.getElementById("mypattern");
        pattern.style.display = "block";
        var bpattern = document.getElementById("buttonpattern");
        bpattern.style.display = "block";
    }
    function hidepattern(){
        var pattern = document.getElementById("mypattern");
        pattern.style.display= "none";
        var bpattern = document.getElementById("buttonpattern");
        bpattern.style.display = "none";
    }
	function disable_all()
	{
		// VAMOS REMOVER TODAS AS CAIXAS DE TEXTO DENTRO DA FORM
		$('#rma_form').find('input[type=text]:visible').each(function()
		{
			if(parseFloat($(this).css('font-size')) > 11)$(this).replaceWith("<div style='padding-top:4px;font-weight:bold;width:"+$(this).width()+"px;height:"+$(this).height()+"px;font-size:"+$(this).css('font-size')+"'>" + (this.value!='' ? this.value : 'N/A') + "</div>");
			else $(this).replaceWith("<div style='padding-top:"+($(this).height()/4)+"px;font-weight:bold;width:"+$(this).width()+"px;height:"+$(this).height()+"px;font-size:"+$(this).css('font-size')+"'>" + (this.value!='' ? this.value : 'N/A') + "</div>");
		});

		$('#rma_form').find('textarea:visible').each(function()
		{
			$(this).replaceWith("<div style='padding-top:5px;font-weight:bold;width:"+$(this).width()+"px;min-height:20px'>" + (this.value!='' ? this.value.replace(/\n/g, "<br />") : 'N/A') + "</div>");
		});

		$('#rma_form input[type=checkbox]:not(:checked)').parent('label').remove();
		$('#rma_form input[type=checkbox]:checked').attr('disabled', 'disabled');

		$('#rma_form input[type=radio]:not(:checked)').parent('label').remove();
		$('#rma_form input[type=radio]:checked').attr('disabled', 'disabled');

		$('#rma_form option:selected').each(function()
		{
			$(this).closest('select').replaceWith("<div style='padding-top:2px;font-weight:bold'>" + ($(this).text()!='' ? $(this).text() : 'N/A') + "</div>");
		});


	}

	<?php if($readonly == 1){?>disable_all();<?php }?>
</script>

<script>
    $(function(){
        mhnUI.setup();
        // mhnUI.setPattern('12587');
    });
    mhnUI={
        pattern: "",
        setup: function() {
            this.lock(),this.colors()

        },
        lock: function()
        {
            mhnUI.page.hide(),pattern=new PatternLock(".mhn-lock",{

                onDraw:function(pattern){

                    alert(pattern);
                    var x_padrao = document.getElementById("m_padrao");
                    x_padrao.value=pattern;

                },

                margin: 15
            }),$(".mhn-lock-title").html($(".mhn-lock-title").data("title")),pattern.checkForPattern("123",function() {
                $(".patt-holder").addClass("")
        }<?php if(isset($m_padrao) && $m_padrao!=''){?>,pattern.setPattern("<?php echo $m_padrao;?>")<?php }?>);


        },
        page: {
            show: function(t) {
                t=t ? t : "page-home",$(".mhn-ui-page").hide(),$(".mhn-ui-page." + t).show()
            },
            hide: function(t) {
                t=t ? t : "page-lock",$(".mhn-ui-page").hide(),$(".mhn-ui-page." + t).show()
            }
        },
        colors: function() {
            $(".mhn-ui-icon span").on("mouseover",function() {
                $(this).css("background",$(this).data("color"))
            }).on("mouseout",function() {
                $(this).removeAttr("style")
            })
        }
    };
!function(t,e,n,a){"use strict";function r(t){for(var e=t.holder,n=t.option,a=n.matrix,r=n.margin,i=n.radius,o=['<ul class="patt-wrap" style="padding:'+r+'px">'],s=0,l=a[0]*a[1];l>s;s++)o.push('<li class="patt-circ" style="margin:'+r+"px; width : "+2*i+"px; height : "+2*i+"px; -webkit-border-radius: "+i+"px; -moz-border-radius: "+i+"px; border-radius: "+i+'px; "><div class="patt-dots"></div></li>');o.push("</ul>"),e.html(o.join("")).css({width:a[1]*(2*i+2*r)+2*r+"px",height:a[0]*(2*i+2*r)+2*r+"px"}),t.pattCircle=t.holder.find(".patt-circ")}function i(t,e,n,a){var r=e-t,i=a-n;return{length:Math.ceil(Math.sqrt(r*r+i*i)),angle:Math.round(180*Math.atan2(i,r)/Math.PI)}}function o(){}function s(e,n){var a=this,i=a.token=Math.random(),h=p[i]=new o,u=h.holder=t(e);if(0!=u.length){h.object=a,n=h.option=t.extend({},s.defaults,n),r(h),u.addClass("patt-holder"),"static"==u.css("position")&&u.css("position","relative"),u.on("touchstart mousedown",function(t){d.call(this,t,a)}),h.option.onDraw=n.onDraw||l;var c=n.mapper;h.mapperFunc="object"==typeof c?function(t){return c[t]}:"function"==typeof c?c:l,h.option.mapper=null}}var l=function(){},p={},d=function(e,a){e.preventDefault();var r=p[a.token];if(!r.disabled){r.option.patternVisible||r.holder.addClass("patt-hidden");var i="touchstart"==e.type?"touchmove":"mousemove",o="touchstart"==e.type?"touchend":"mouseup";t(this).on(i+".pattern-move",function(t){h.call(this,t,a)}),t(n).one(o,function(){u.call(this,e,a)});var s=r.holder.find(".patt-wrap"),l=s.offset();r.wrapTop=l.top,r.wrapLeft=l.left,a.reset()}},h=function(e,n){e.preventDefault();var a=e.pageX||e.originalEvent.touches[0].pageX,r=e.pageY||e.originalEvent.touches[0].pageY,o=p[n.token],s=o.pattCircle,l=o.patternAry,d=o.option.lineOnMove,h=o.getIdxFromPoint(a,r),u=h.idx,c=o.mapperFunc(u)||u;if(l.length>0){var f=i(o.lineX1,h.x,o.lineY1,h.y);o.line.css({width:f.length+10+"px",transform:"rotate("+f.angle+"deg)"})}if(u){if(-1==l.indexOf(c)){var v,m=t(s[u-1]);if(o.lastPosObj){for(var g=o.lastPosObj,x=g.i,w=g.j,b=Math.abs(h.i-x),j=Math.abs(h.j-w);(0==b&&j>1||0==j&&b>1||j==b&&j>1)&&(w!=h.j||x!=h.i);){x=b?Math.min(h.i,x)+1:x,w=j?Math.min(h.j,w)+1:w,b=Math.abs(h.i-x),j=Math.abs(h.j-w);var M=(w-1)*o.option.matrix[1]+x,y=o.mapperFunc(M)||M;-1==l.indexOf(y)&&(t(s[M-1]).addClass("hovered"),l.push(y))}v=[],h.j-g.j>0?v.push("s"):h.j-g.j<0?v.push("n"):0,h.i-g.i>0?v.push("e"):h.i-g.i<0?v.push("w"):0,v=v.join("-")}m.addClass("hovered"),l.push(c);var P=o.option.margin,k=o.option.radius,C=(h.i-1)*(2*P+2*k)+2*P+k,O=(h.j-1)*(2*P+2*k)+2*P+k;if(1!=l.length){var D=i(o.lineX1,C,o.lineY1,O);o.line.css({width:D.length+10+"px",transform:"rotate("+D.angle+"deg)"}),d||o.line.show()}v&&(o.lastElm.addClass(v+" dir"),o.line.addClass(v+" dir"));var E=t('<div class="patt-lines" style="top:'+(O-5)+"px; left:"+(C-5)+'px"></div>');o.line=E,o.lineX1=C,o.lineY1=O,o.holder.append(E),d||o.line.hide(),o.lastElm=m}o.lastPosObj=h}},u=function(t,e){t.preventDefault();var n=p[e.token],a=n.patternAry.join("");n.holder.off(".pattern-move").removeClass("patt-hidden"),a&&(n.option.onDraw(a),n.line.remove(),n.rightPattern&&(a==n.rightPattern?n.onSuccess():(n.onError(),e.error())))};o.prototype={constructor:o,getIdxFromPoint:function(t,e){var n=this.option,a=n.matrix,r=t-this.wrapLeft,i=e-this.wrapTop,o=null,s=n.margin,l=2*n.radius+2*s,p=Math.ceil(r/l),d=Math.ceil(i/l),h=r%l,u=i%l;return p<=a[1]&&d<=a[0]&&h>2*s&&u>2*s&&(o=(d-1)*a[1]+p),{idx:o,i:p,j:d,x:r,y:i}}},s.prototype={constructor:s,option:function(t,e){var n=p[this.token],i=n.option;return e===a?i[t]:(i[t]=e,void(("margin"==t||"matrix"==t||"radius"==t)&&r(n)))},getPattern:function(){return p[this.token].patternAry.join("")},setPattern:function(t){var e=p[this.token],n=e.option,a=n.matrix,r=n.margin,i=n.radius;if(n.enableSetPattern){this.reset(),e.wrapLeft=0,e.wrapTop=0;for(var o=0;o<t.length;o++){var s=t[o]-1,d=s%a[1],u=Math.floor(s/a[1]),c=d*(2*r+2*i)+2*r+i,f=u*(2*r+2*i)+2*r+i;h.call(null,{pageX:c,pageY:f,preventDefault:l,originalEvent:{touches:[{pageX:c,pageY:f}]}},this)}}},enable:function(){var t=p[this.token];t.disabled=!1},disable:function(){var t=p[this.token];t.disabled=!0},reset:function(){var t=p[this.token];t.pattCircle.removeClass("hovered dir s n w e s-w s-e n-w n-e"),t.holder.find(".patt-lines").remove(),t.patternAry=[],t.lastPosObj=null,t.holder.removeClass("patt-error patt-success")},error:function(){p[this.token].holder.addClass("patt-error")},checkForPattern:function(t,e,n){var a=p[this.token];a.rightPattern=t,a.onSuccess=e||l,a.onError=n||l}},s.defaults={matrix:[3,3],margin:20,radius:25,patternVisible:!0,lineOnMove:!0,enableSetPattern:1},e.PatternLock=s}(jQuery,window,document);


</script>
