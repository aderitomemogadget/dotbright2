<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button = write_lang('key_apagar','Apagar');		$task_form = 'apaga'; $btn_class='btn-primary';}
	elseif($ID==0)		{$task_button = write_lang('key_inserir','Inserir');	$task_form = 'insere';$btn_class='btn-primary';}
	else				{$task_button = write_lang('key_alterar','Alterar');	$task_form = 'altera';$btn_class='btn-primary';}

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;

    $fields=db_list_fields($DB_name,'media',$db_link);
    $columns=db_num_fields($fields);
    for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('Permanente',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD Permanente tinyint(1) DEFAULT 0');  }
    if(!in_array('Descritivo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD Descritivo BLOB NOT NULL'); }
    if(!in_array('TemaID',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD TemaID int(11) DEFAULT 0'); }
    if(!in_array('Familia',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD Familia int(11) DEFAULT 0'); }
    if(!in_array('LINGUA',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD LINGUA varchar(6) DEFAULT ""'); }

	if($ID!=0)
	{
		$rs = new DataObj;

		$rs->db_open($db_link, $DB_name, "SELECT media.*, media_type.Comportamento FROM media, media_type WHERE media.ID=$ID AND media.tipo = media_type.ID");
		if($rs)
		{
			$name			= $rs->db_data["Name"];
			$ID_ORG			= $rs->db_data["Tipo"];
			$url			= $rs->db_data["URL"];
			$I1				= $rs->db_data["img_ext"];
			$largura		= $rs->db_data["Largura"];
			$altura			= $rs->db_data["Altura"];
			$tipo			= $rs->db_data["Tipo"];
			$pagina			= $rs->db_data["Pagina"];
			$anunciante		= $rs->db_data["ID_Anunciante"];
			$D				= str2date($rs->db_data["Data1"]);
			$D2				= str2date($rs->db_data["Data2"]);
			$n_impressoes	= $rs->db_data["N_impressoes"];
			$n_cliques		= $rs->db_data["N_cliques"];
			$t_impressoes	= $rs->db_data["T_impressoes"];
			$t_cliques		= $rs->db_data["T_cliques"];
			$peso			= $rs->db_data["Peso"];
			$visible		= $rs->db_data["Visible"];
			$comportamento	= $rs->db_data["Comportamento"];
			$LINGUA	= $rs->db_data["LINGUA"];

			$script			= stripslashes($rs->db_data["Script"]);
			$permanente		= $rs->db_data["Permanente"];
			$Descritivo		= $rs->db_data["Descritivo"];
			$tema			= $rs->db_data["TemaID"];
		}
		$rs->db_close();
	}
	else $tipo = $ID_ORG;


	if(isset($_GET['Familia']) && intval($_GET['Familia']) > 0)
	{
		$tipo = 0;
		$result = new DataObj;
		$result->db_open($db_link, $DB_name, "select Tipo from media WHERE ID='".intval($_GET['Familia'])."'");
		if(!$result->EOF()) $tipo = $result->db_data['Tipo'];
		$result->db_close();
	}

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
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
<script>$(document).ready(function()
{
	$('.fancybox_img').fancybox();
	$('.fancybox2').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', helpers : { overlay : { speedIn  : 0, speedOut : 300, opacity  : 0.8,css : { cursor : 'default' }, closeClick: false}},onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('.fancybox_pop').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', helpers : { overlay : { speedIn  : 0, speedOut : 300, opacity  : 0.8,css : { cursor : 'default' }, closeClick: false}},afterClose: function() {window.open('pagina.php?bloco_id=<?php echo $_GET['bloco_id'].'&d='.date('YmdHis')?>','_self'); },onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	$('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 754, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
	// $('.fancybox_url').live('click', function(){ var href = $(this).attr('href'); $.fancybox({ 'showCloseButton' : false, 'titlePosition' : 'inside', title : true, autoSize : false, modal : false, fitToView : false, topRatio : 0, content : '<iframe width="100%" height="100%" src="'+href+'"></iframe>', width : 1105, maxHeight : 600, scrolling : 'no', type : 'html', onComplete: function() {$("#fancybox-wrap").stop().animate({'top':'10px',}, 0); $("#fancybox-wrap").css({'z-index':'9999999999999999',}, 0); } }); return false;});
});
</script>
<div class="center">
<form name="form" method="post" action="pagina_trata.php?ID=<?php echo $ID?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
<input type="hidden" name="slashes" value="'">

<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
<td  align="left"><?php include "../layout/menus.inc.php"; ?>
	<div class="panel panel-inverse overflow-hidden m-b-12">
        <div class="panel-heading">
            <h4 style="color:#fff">MEDIA</h4>
        </div>
    </div>

		<table class="titulo_janela" width="100%" height="30px" cellspacing="0" cellpadding="5" border="0">
		<tr>
			<td valign="middle"><a href="../media_type/pagina.php?ID=<?php echo $tipo?>" class="brancoh"> &#60; Voltar para a lista de medias</a></td>
		</tr>
		</table>
		<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
		<tr>
			<td>
			<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
			<tr>
				<td>
				<table width="100%" border=0 cellpadding="2" cellspacing=0>

				<?php if(isset($_GET['Familia']) && intval($_GET['Familia']) > 0) {
					$tipo = 0;
					$result = new DataObj;
					$result->db_open($db_link, $DB_name, "select Tipo from media WHERE ID='".intval($_GET['Familia'])."'");
					if(!$result->EOF()) $tipo = $result->db_data['Tipo'];
					$result->db_close();
				?>
				<tr>
				<td width="80" class="preto"> <?php echo write_lang('key_grupo','Grupo');?>:</td>
				<td align="left"><?php banner_tipos($tipo);?></td>
				</tr>
				<tr>
				<td width="80" class="preto"> Media Pai:</td>
				<td align="left"><?php banner_media($tipo, intval($_GET['Familia']));?></td>
				</tr>
				<?php }else{?>
				<tr>
				<td width="80" class="preto"> <?php echo write_lang('key_grupo','Grupo');?>:</td>
				<td align="left"><?php banner_tipos($tipo);?></td>
				</tr>
				<?php }?>
				<tr>
				<td width="80" class="preto">Língua:</td>
				<td align="left">
				<table width="99%" cellpadding="0" cellspacing="0" border="0">
				<tr>
				<td width="150" class="preto"><?php banner_linguas($LINGUA);?></td>
				<td width="20" class="preto">&nbsp;</td>
				<td>(Este contéudo só aparecerá na lingua selecionada)</td>
				</tr>
				</table>

				</td>
				</tr>
				<tr>
				<td width="80" class="preto"> <?php echo write_lang('key_nome','Nome');?>:</td>
				<td align="left"><input name="name" style="WIDTH:100%" maxlength="255" value="<?php echo $name?>" class="form-control"></td>
				</tr>
				<tr>
				<td width="80" class="preto"> <?php echo write_lang('key_url','Url');?>:</td>
				<td align="left">
				<table width="99%" cellpadding="0" cellspacing="0" border="0">
				<tr>
				<td width="534" class="preto"><input name="url" id="url"  maxlength="255" value="<?php echo $url?>" class="form-control" style="width:534px"></td>
				<td width="20" class="preto">&nbsp;</td>
				<td><a href="../menus/listcontent.php?field=url" class="btn btn-primary fancybox2"><?php echo write_lang('key_procurar','Procurar')?></a></td>
				</tr>
				</table>



				</td>
				</tr>
				<tr>
				<td width="80" class="preto"> Imagem:</td>
				<td align="left" class="preto">
				<?php
				if($I1!="")
				{
					?><div class="imagens_200"><? if(file_exists("../../files/media/$ID"."_1.".$I1)){?><a href="<?php echo "../../files/media/$ID"."_1.".$I1;?>" class="fancybox_img"><img alt="" src="<?php echo "../../files/media/$ID"."_1.".$I1;?>"></a><? }?></div><?
					if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','ou');?> <? echo write_lang('key_alterar','alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
				}
				else
				{
					echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0' />
				<?php
				}?>
				</td>
				</tr>
				<tr>
				<td width="80" class="preto"> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_script','Script');?>: </td>
				<td align="left"><textarea name="script" id="script" style="WIDTH: 100%; HEIGHT: 150px;"><? echo htmlentities_memo($script); ?></textarea></td>
				</tr>
				<tr>
				<td width="80" class="preto"> <? echo write_lang('key_descricao','Descrição');?>:</td>
				<td align="left" class="preto"><textarea name="Descritivo" id="Descritivo" style="WIDTH: 100%; HEIGHT: 150px;"><?php echo $Descritivo;?></textarea></td>
				</tr>
				<tr><td colspan="10"><br></td></tr>
				<tr>
				<td width="80" class="preto"> <? echo write_lang('key_configuracoes','Configurações');?>:</td>
				<td align="left">
				<table border=0 cellpadding="4" cellspacing=0 width="100%" style="border:#666666 1px solid;background-color:#e5e5e5">
				<tr>
					<td class="preto" width="110" align="left"><? echo write_lang('key_valido','Válido');?> <? echo write_lang('key_de','de');?>:</td>
					<td class="preto" align="left"><? option_data($D);?></td>
					<td rowspan=5 valign=top class="preto">
						<label for="permanente"><input type="checkbox" name="permanente" id="permanente" value="1" <?php if($permanente=='1') echo "checked='checked'";?>> ou <? echo write_lang('key_banner_permanente','Banner Permanente');?> (ignora datas)</label>
					</td>
				</tr>
				<tr>
					<td class="preto" align="left"><? echo write_lang('key_ate','até');?>:</td>
					<td class="preto" align="left"><? option_data($D2,2);?></td>
				</tr>
				<tr>
					<td colspan="2"><hr></td>
				</tr>
				<tr>
					<td class="preto" align="left"><? echo write_lang('key_peso','Peso');?>:</td>
					<td class="preto" align="left"><input name="peso" style="width:100px; float:left" class="form-control" value="<?php echo $peso;?>"></td>
				</tr>
				<tr>
				<td class="preto" align="left"><? echo write_lang('key_n_impressoes','N. impressões');?>:</td>
				<td class="preto" align="left" style="line-height: 38px"><input name="n_impressoes" style="width:100px; float:left" class="form-control" value="<?php echo $n_impressoes;?>">&nbsp;<? echo write_lang('key_total_de_impressoes','Total de impressões');?>: <? if(!$t_impressoes) echo 0; else echo $t_impressoes;?></td>
				</tr>
				<tr>
				<td class="preto" align="left"><? echo write_lang('key_n_cliques','N. cliques');?>:</td>
				<td class="preto" align="left" style="line-height: 38px"><input name="n_cliques" style="width:100px; float:left" class="form-control" value="<?php echo $n_cliques;?>">&nbsp;<? echo write_lang('key_total_de_cliques','Total de cliques');?>: <? if(!$t_cliques) echo 0; else echo $t_cliques;?></td>
				</tr>
				</table>
				</td>
				</tr><tr>
				<td width="80" class="preto"> </td>
				<td align="left" class="preto"><label for="visible"><input type="checkbox" name="visible" id="visible" value="1" <?php if($visible == '1') echo "checked='checked'";?>> <? echo write_lang('key_ativo','Ativo');?></label><br></td>
				</tr>
				</table>
			</td>
			</tr>
			</table>
		</td>
		</tr>
		</table>
		<?php
		show_page_buttons();
		if(isset($_GET['Familia']) && $_GET['Familia'] == 0 && in_array($comportamento,$comportamentos_arr)) // é família e é do tipo Multilevel Gallery
{
?>
<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">

    <tr>
        <td >
           <?php

			$rs->db_open($db_link,$DB_name,"Select * from media where Familia='".$ID."' order by Peso ASC");

           ?>

            <form method="post" name="lista_paginas" action="pagina.php?ID=<?php echo $ID?>&dump=<?php echo uniqID("")?>" onSubmit="return checkFields()">
            <input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>" />
            <input type="hidden" name="slashes" value="'" />
            <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=2>
			<tr>
			<td bgcolor="<?php echo $cor2;?>" align="center" colspan="10"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_novo_elemento','Inserir Novo Elemento');?>" onclick="javascript:window.open('../media/pagina.php?Familia=<?php echo $ID?>&Dump=<?php echo uniqid("");?>','_self')"></td>
			</tr>
            <?php
             	// mostrar elementos
				$count2=0;
                $t=0;
				if(!$rs->EOF()) // família tem elementos - lista de elementos da família
                {
				?>
                    <tr>
                        <td height="30" width=29 align="center" bgcolor="#161716"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
                        <td colspan="6" bgcolor="#161716" class="branco">Selecionar todos</td>
                    </tr>
                    <?php
                    while(!$rs->EOF())
                    {
                        $t++;
                        $codigo		= $rs->db_data["ID"];
						$familia	= $rs->db_data["Familia"];
                        $name		= ($rs->db_data["Name"] ? $rs->db_data["Name"] : 'SEM TÍTULO');
                        $I1			= $rs->db_data["img_ext"];
                        $visible	= $rs->db_data["Visible"];

						if($visible)
						{
							if($count2 % 2 != 0)	$cor2 = "#87ceee";
							else					$cor2 = "#a9d8ed";
						}
						else
						{
							if($count2 % 2 != 0)	$cor2 = "#E0E0E0";
							else					$cor2 = "#c0c0c0";
						}
					?>

						<tr>
						<td width="29" bgcolor="<?php echo $cor2;?>" align="center"><input type="checkbox" name="chk_<?php echo $codigo?>" id="chk_<?php echo $codigo?>" value="<?php echo $codigo?>" class="cb" /></td>
						<td width="35" bgcolor="<?php echo $cor2;?>" style="text-align:left"><img title="<? if($visible) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($visible) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
						<td width="40" bgcolor="<?php echo $cor2;?>" align="right"><?
						if($I1!="")
						{
							if(file_exists("../../files/media/$codigo"."_1.".$I1)){?><a class="preto" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&Familia=<?php echo $familia?>"><img width=100 alt="" border="0" src="<?php echo "../../files/media/$codigo"."_1.".$I1;?>"></a><?php }
						}
						?></td>
						<td  bgcolor="<?php echo $cor2;?>" width="8"></td>
						<td bgcolor="<?php echo $cor2;?>" valign="middle"><a class="preto" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&Familia=<?php echo $familia?>"><? echo "$name";?></a></td>
						<td bgcolor="<?php echo $cor2;?>" valign="middle" align="right" width="80"><a class="italico" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&task=apagar"><img alt="" src="del2.png" border=0 align="absmiddle"></a></td>
						<td bgcolor="<?php echo $cor2;?>" width="10">&nbsp;</td>
						</tr>
                        <?php
                        $rs->db_next();
                        $count2++;
                    }
                    $rs->db_close(1);
                }

				?>
				</table>
				<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
				<tr>
					<td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
					<td width="125px">
						<select name="flag" id="flag" class="form-control">
							<option value="" selected="selected"></option>
							<option value="1"><?php echo write_lang('key_apagar','Apagar');?></option>
							<option value="4"><?php echo write_lang('key_visivel_on','Visivel - (ON)');?></option>
							<option value="5"><?php echo write_lang('key_visivel_off','Visivel - (OFF)');?></option>
						</select>
					</td>
					<td width="5px"></td>
					<td>&nbsp;<input type="submit" name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
				</tr>
				</table>
			</form>
		</td>
	</tr>
</table>
<?
}

		?>
        </td>
        <td width="10px">&nbsp;</td>
        <td  width="200" valign="top"><? show_fast_tools();?></td>
    </tr>
    </table>
    </form>
</div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>


<br /><br />
<?php
include "../layout/bottom.inc.php";
?>