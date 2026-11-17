<?php
	include "settings.inc.php";

	if($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';$btn_class='btn-primary';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';$btn_class='btn-primary';}
	elseif($ID==0){$task_button=write_lang('key_gravar_novo_registo','Gravar Novo Registo');$task_form='insere';$btn_class='btn-primary';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alterações');$task_form='altera';$btn_class='btn-primary';}

	$D=getdate();
	$D2=getdate();
	$C="";$T="";$I1="";$I2="";$ordem="0";
	if($ID!=0)
	{
		if($ID!=0)
		{
			global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
			$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
			$rs=new DataObj;
			$rs2=new DataObj;
			$sql="Select * from templates WHERE ID=$ID";
			$db_res=$rs->db_open($db_link,$DB_name,$sql);
			if($rs)
			{
				$ID=$rs->db_data["ID"];
				$visivel= $rs->db_data["Visivel"];
				$destaque= $rs->db_data["Destaque"];
				$I1=$rs->db_data["img_ext"];
				$ordem=$rs->db_data["Ordem"];
				$D=str2date($rs->db_data["Data"]);
				$D2=str2date($rs->db_data["Data2"]);
				$margem_top=$rs->db_data["margem_top"];
				$url=$rs->db_data["URL"];
				$bg_color=$rs->db_data["bg_color"];
				$efeito=$rs->db_data["efeito"];
				$site_width=$rs->db_data["site_width"];
				$estilo=$rs->db_data["estilo"];
				$estilo2=$rs->db_data["estilo2"];
                $Video=$rs->db_data["Video"];
				$link_to=$rs->db_data["link_to"];

				$sql="Select * from templates_lang WHERE ID_PAI=$ID and Lang='$admin_lang'";
				$db_res=$rs2->db_open($db_link,$DB_name,$sql);
				if($rs2)
				{
					$C=$rs2->db_data["Cabecalho"];
					$texto1=$rs2->db_data["Texto1"];
					$texto2=$rs2->db_data["Texto2"];
					$texto3=$rs2->db_data["Texto3"];
					$texto4=$rs2->db_data["Texto4"];
				}
				$rs2->db_close();
			}
			$rs->db_close(1);
		}
	}
	if(!$caminho_bt_editor){$caminho_bt_editor1="../../../editor/";$caminho_bt_editor2="../editor/";}
	else{$caminho_bt_editor1=$caminho_bt_editor2=$caminho_bt_editor;}
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<link rel="stylesheet" href="../../external_api/codemirror/lib/codemirror.css" />
<link rel="stylesheet" href="../../external_api/codemirror/addon/dialog/dialog.css">
<link rel="stylesheet" href="../../external_api/codemirror/addon/search/matchesonscrollbar.css">
<script src="../../external_api/codemirror/lib/codemirror.js"></script>
<script src="../../external_api/codemirror/mode/markdown/markdown.js"></script>
<script src="../../external_api/codemirror/mode/xml/xml.js"></script>
<script src="../../external_api/codemirror/addon/selection/active-line.js"></script>
<script src="../../external_api/codemirror/addon/dialog/dialog.js"></script>
<script src="../../external_api/codemirror/addon/search/searchcursor.js"></script>
<script src="../../external_api/codemirror/addon/search/search.js"></script>
<script src="../../external_api/codemirror/addon/scroll/annotatescrollbar.js"></script>
<script src="../../external_api/codemirror/addon/search/matchesonscrollbar.js"></script>
<script type="text/javascript">
    $(document).ready(function()
    {
        $('.fancybox_img').fancybox();
        $('.fancybox2').on('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 400, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {} }); return false;});
    });
</script>
<script type="text/javascript">
    function uploadfile(op){
     $("#error-msgs").html('');
    var fd = new FormData();
    var filese = $('#filetouploade')[0].files[0];
    var filesd = $('#filetouploadd')[0].files[0];

    fd.append('filee',filese);
    fd.append('filed',filesd);
    //append another data
    fd.append('url_linke',$('#url_linke').val());
    fd.append('url_linkd',$('#url_linkd').val());

    fd.append('colorbge',$('#colorbge').val());
    fd.append('colorbgd',$('#colorbgd').val());


    if (typeof(filese) == 'undefined') {
        $("#error-msgs").html(emsg('Imagen direita é obrigatoria'));
        return true;
    }
    if (typeof(filesd) == 'undefined') {
        $("#error-msgs").html(emsg('Imagen esquerda é obrigatoria'));
        return true;
    }

    fd.append('ID_PAI',<?php echo $_GET['ID']?>);
   // console.log(fd);
    $.ajax({
    url: "<?php echo $GLOBALS['site_http_url']?>"+"ajax/uploadbgfile.php",
                    type: 'post',
                    data: fd,
                    contentType: false,
                    processData: false,
                    dataType: 'json',
                    success: function(json){

                        $("#files-links").html(json);

                    },
                });

         //// end of upload file
    }

    function emsg(msg){
        return '<div class="aerrormsg">'+msg+'</div>';
    }

    function apagaimgtemp(id){
        var str = '<span onclick="apagaitdef('+id+',1)">&#x2714;</span>';
         str += '<span onclick="apagaitdef('+id+',0)">&#x1F6AB;</span>';
        $("#apagarimgt"+id).html(str);
    }

    function apagaitdef(id,op){
          var str;
        if(op==0){
            str = '<div class="apagarimgt" id="apagarimgt'+id+'" onclick="apagaimgtemp('+id+')">&#x2716;</div>';
            $("#apagarimgt"+id).html(str);
        }
        if(op==1){
            var params = {
               "id" : id,
               "ID_PAI" : <?php echo $_GET['ID']?>
               }

         $.ajax({
                data:  params,
                url: "<?php echo $GLOBALS['site_http_url']?>"+"ajax/apagaitdef.php",
                dataType: 'json',
                success: function (json) {
                  $("#files-links").html(json);
                },
        type:  'post'

            });

        }
    }

    function savelinkandcor(id,s){
       // console.log
       var color = $("#color"+id+s).val();
       var link = $("#link"+id+s).val();

        var params = {
               "id" : id,
               "color" : color,
               "link" : link,
               "s" : s
               }

            $.ajax({
                data:  params,
                url: "<?php echo $GLOBALS['site_http_url']?>"+"ajax/savelinkcolor.php",
                dataType: 'json',
                success: function (json) {
                  //$("#files-links").html(json);
                  //smsg'.$rs->db_data['ID'].'e

                  $("#smsg"+id+s).html("Alterado com sucesso !");
                  setTimeout(function(){  $("#smsg"+id+s).html(""); }, 2000);
                },
        type:  'post'

            });
    }
</script>
<style type="text/css">
    .uploadfile{margin: 5px 5px 5px 20px; padding: 12px; background: #348fe2; color:#ffffff; cursor:pointer; transition:0.5s ease; border-radius: 3px; display:inline; }
    .uploadfile:hover{background: #1e77c5; }
    .aerrormsg{background: #ff4444; padding:10px 20px; color:#ffffff; margin: 10px 0px; font-size:18px; }
    .img-content{max-width: 250px; display:inline-block; padding: 5px; }
    .template-images-inner{border:1px solid #dddddd; margin-top:5px; margin-bottom:5px; position:relative; }
    .apagarimgt{position: absolute; right: 5px; top: 5px; font-size: 30px; color:#ff4444; cursor: pointer; }
    .savelinkandcor{font-size:20px; cursor:pointer; }
</style>
<div class="center">
 	<form name="form" method="post" action="pagina_trata.php?ID=<?=$ID;?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
    	<input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
		<input type="hidden" name="slashes" value="'">
        <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
        <tr>
            <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <div class="panel panel-inverse overflow-hidden m-b-12">
                    <div class="panel-heading">
                        <h4 style="color:#fff">TEMPLATES</h4>
                    </div>
                </div>
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                <tr>
                    <td valign="top">
                    	<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                        <tr>
                            <td valign="top">

                                <table width="100%" border=0 cellspacing="2" cellpadding=2>
                                <tr>
                                    <td class="preto"><? echo write_lang('key_activar_template','Activar template');?>:</td>
                                    <td class="preto" colspan="3"><? echo write_lang('key_sim','Sim');?> <input name="visivel" type="checkbox" id="visivel" value="1" <? if($visivel) echo "checked"; ?> /></td>
                                </tr>
                                <tr>
                                    <td width="150" class="preto"><? echo write_lang('key_ordem','Ordem');?>:</td>
                                    <td width="100"><input class="form-control" name="ordem" style="WIDTH:50px" maxlength="255" value="<?=$ordem;?>"></td>
                                    <td width="150" class="preto"><? echo write_lang('key_nome_da_template','Nome da template');?>:</td>
                                    <td align="left"><input class="form-control" name="cabecalho" style="WIDTH:99%" maxlength="255" value="<?=$C;?>"></td>
                                </tr>
                                <tr>
                                    <td class="preto"><? echo write_lang('key_margem_no_topo','Margem no Topo');?>:</td>
                                    <td class="preto"><input class="form-control" name="margem_top" type="text" id="margem_top" style="width:50px" value="<?=$margem_top;?>"> <? echo write_lang('key_em_pixels','em pixels');?></td>
                                    <td class="preto" width="150">Largura do site:</td>
                                    <td class="preto" valign="top">
                                        <input class="form-control" name="site_width" type="text" id="site_width" style="width:50px;float:left" value="<?=$site_width;?>"><div style="float:left;line-height: 30px">&nbsp;<? echo write_lang('key_em_pixels','em pixels');?>&nbsp;</div>
                                        <select class="form-control" name="estilo" style="width:180px;float: left;">
                                            <option value="" <? if(!$estilo) echo " selected";?>><? echo write_lang('key_alinhado_a_esquerda','Alinhado à Esquerda');?></option>
                                            <option value="1" <? if($estilo==1) echo " selected";?>><? echo write_lang('key_alinhado_ao_centro','Alinhado ao Centro');?></option>
                                        </select>
                                        <div style="float:left;line-height: 30px">&nbsp;<? echo write_lang('key_conteudos_dentro_do_site','Conteúdos Dentro do Site');?>:&nbsp;</div>
                                        <select name="estilo2" class="form-control" style="width:180px;float: left;">
                                            <option value="" <? if(!$estilo2) echo " selected";?>><? echo write_lang('key_alinhados_a_esquerda','Alinhados à Esquerda');?></option>
                                            <option value="1" <? if($estilo2==1) echo " selected";?>><? echo write_lang('key_alinhados_ao_centro','Alinhados ao Centro');?></option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preto" id="colorthis"><? echo write_lang('key_aplicar_uma_cor_de_fundo','Aplicar uma Cor de Fundo');?>:</td>
                                    <td class="preto"><? echo write_lang('key_sim','SIM');?> <input class="form-control" name="bg_color" id="bg_color" value="<?=$bg_color?>" style="width:90px"></td>
                                    <td class="preto"><? echo write_lang('key_efeito_pretendido','Efeito pretendido');?>:</td>
                                    <td class="preto">
                                        <select class="form-control" name="efeito">
                                            <option value="" <? if(!$efeito) echo " selected";?>><? echo write_lang('key_efeito_imagem','Repetir Imagem');?></option>
                                            <option value="1" <? if($efeito==1) echo " selected";?>><? echo write_lang('key_repetir_imagem_na_horizontal','Repetir Imagem na Horizontal');?></option>
                                            <option value="2" <? if($efeito==2) echo " selected";?>><? echo write_lang('key_repetir_imagem_na_vertical','Repetir Imagem na Vertical');?></option>
                                            <option value="3" <? if($efeito==3) echo " selected";?>><? echo write_lang('key_imagem_alinhada_ao_centro_topo','Imagem Alinhada ao Centro/Topo');?></option>
                                            <option value="4" <? if($efeito==4) echo " selected";?>>Imagem a Fullscreen</option>
                                        </select>
                                    </td>
                                </tr>
                                <tr>
                                    <td colspan="4">&nbsp;</td>
                                </tr>
                                <?php /* adicionar mas do que uma imagen de fundo */
                                  if($GLOBALS['conf_on_off']['lateralbackground']){
                                ?>
                                <tr>
                                    <td class="preto" align="left" width="150" colspan="4">
                                        Imagens Laterais de fundo
                                    </td>
                                </tr>
                                <tr>
                                    <td class="preto" align="left" width="150" colspan="4">
                                        <table style="margin:20px">
                                            <tr style="width:100%">
                                                <td >
                                                    <input type="file" id="filetouploade">
                                                  </td>

                                                  <td >
                                                    <input type="color" id="colorbge" value="#2a4e7b">
                                                  </td>
                                                   <td>
                                            &nbsp;link to:
                                            <input type="text" name="link_to" id="url_linke" value="<?=$link_to?>" style="width:300px;">
                                            <a href="listcontent.php?field=url_linke" class="preto fancybox2" style="font-size:16px;">&#x1F517;</a>
                                            </td>
                                            <td>


                                                </td>
                                            </tr>

                                            <tr style="width:100%">
                                                <td >
                                                    <input type="file" id="filetouploadd">
                                                  </td>

                                                  <td >
                                                     <input type="color" id="colorbgd" value="#2a4e7b">
                                                  </td>

                                                   <td>
                                            &nbsp;link to:
                                            <input type="text" name="link_to" id="url_linkd" value="<?=$link_to?>" style="width:300px;">
                                            <a href="listcontent.php?field=url_linkd" class="preto fancybox2" style="font-size:16px;">&#x1F517;</a>
                                            </td>
                                            <td>
                                                <div class="uploadfile" onclick="uploadfile()">Adicionar Imagens</div>


                                                </td>
                                            </tr>

                                            <tr>
                                                <td colspan="4">

                                                    <div id="error-msgs">

                                                    </div>
                                                </td>
                                            </tr>

                                                <tr>
                                                <td colspan="4">

                                                    <div id="files-links">
                                                        <?php
                                                        $str = templateimages($_GET['ID']);
                                                        echo $str;
                                                        ?>
                                                    </div>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                                <?php }
                                ?>
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150">
                                        Imagem de fundo:<br>
                                        <table width="100%" bgcolor="#cccccc" border=0 cellspacing=0 cellpadding=0>
                                        <tr><td><font style="font-family: verdana; font-size: 9px"><? echo write_lang('key_resolucao','Resolução');?> - <i>72 DPIs</i></font></td></tr>
                                        </table>
                                    </td>
                                    <td class="preto" align="left" valign="top" colspan="3"><?
                                    if($I1!="")
                                    {
                                        ?><a href="<?="../../files/templates/$ID"."_1.".$I1;?>" target="_blank"><? echo write_lang('key_ver_imagem','Ver Imagem');?></a><br>
                                        <a href="<?=$GLOBALS['site_http_url'].seo($link_to); ?>">
                                        <?php echo seo($link_to); ?></a>
                                        <br>
                                        <?
                                        if($task!="apagar"){ ?><? echo write_lang('key_apagar','Apagar');?>

                                        <input type="checkbox" name="Apagar_img1" value="1"><br><? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_alterar','Alterar');?>: <input type="file" name="img_peq1" /><input type="hidden" name="tem_foto1" value="1" />

                                    <? }
                                    }
                                    else
                                    {?><?
                                        echo '<div style="display:inline;float:left;">'.write_lang('key_nova','Nova').':&nbsp;</div> ';?>
                                        <input type="file" name="img_peq1" style="float:left;"><input type="hidden" name="tem_foto1" value="0">
                                        <div style="margin-left:10px">
                                            &nbsp;link to:
                                            <input type="text" name="link_to" id="url_link" value="<?=$link_to?>" style="width: calc(100% - 310px);">
                                            <a href="listcontent.php?field=url_link" class="preto fancybox2" style="font-size:16px;">&#x1F517;</a></div><? } ?>
                                    </td>
                                </tr>
                                <? /*
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150">Ou video de fundo:</td>
                                    <td class="preto" align="left" valign="top"><input name="Video" type="text" id="Video" style="width:300px" value="<?=$Video;?>"> (URL e Mpeg4)</td>
                                </tr>*/ ?>
                                <? /*
                                <tr>
                                    <td class="preto" align="left" valign="top" width="150">URL:</td>
                                    <td class="preto" align="left" valign="top"><input name="url" type="text" id="url" style="width=255" value="<?=$url;?>"></td>
                                </tr>*/?>


                                </table><br />
                                <? echo write_lang('key_topo_generico','Topo genérico');?>:
                            <table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                                <td align="left" valign="top"><div style="width:100%"><textarea id="texto1" name="texto1" style="width:200px;height:200px"><?=htmlentities_memo(stripslashes($texto1));?></textarea></div></td>
                            </tr>
                            </table>
                            <? echo write_lang('key_rodape_generico','Rodapé genérico');?>:
                            <table width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                                <td align="left" valign="top"><div style="width:100%"><textarea id="texto2" name="texto2" style="height:300px;font-size:11px"><?=htmlentities_memo(stripslashes($texto2));?></textarea></div></td>
                            </tr>
                            </table>
                			</td>
                        </tr>
                        </table>
                    </td>
                </tr>
                </table><? show_page_buttons();?>
            </td>
				<td width="10px">&nbsp;</td>
				<td valign="top" width="200"><? show_fast_tools();?><? show_other_templates($ID);?></td>
        </tr>
        </table>
		</form>
</div>
<script>var editor = CodeMirror.fromTextArea(document.getElementById("texto1"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
<script>var editor2 = CodeMirror.fromTextArea(document.getElementById("texto2"), { lineNumbers: true, styleActiveLine: true, lineNumbers: true, lineWrapping: true});</script>
<?php include "../layout/bottom.inc.php";?>