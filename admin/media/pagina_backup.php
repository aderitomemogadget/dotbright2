<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button = write_lang('key_apagar','Apagar');		$task_form = 'apaga';}
	elseif($ID==0)		{$task_button = write_lang('key_inserir','Inserir');	$task_form = 'insere';}
	else				{$task_button = write_lang('key_alterar','Alterar');	$task_form = 'altera';}

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$obj=new DataObj;

    $fields=db_list_fields($DB_name,'media',$db_link);
    $columns=db_num_fields($fields);
    for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
    if(!in_array('Permanente',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD Permanente tinyint(1) DEFAULT 0');  }
    if(!in_array('Descritivo',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD Descritivo BLOB NOT NULL'); }
    if(!in_array('TemaID',$field_array)){$res=Runquery($db_link,$DB_name,'ALTER TABLE media ADD TemaID int(11) DEFAULT 0'); }

	if($ID!=0)
	{
		$rs=new DataObj;
		//$hRes = $rs->db_open($db_link,$DB_name,"Select * from media where ID=$ID");
		$hRes = $rs->db_open($db_link,$DB_name,"SELECT media.*, media_type.Comportamento FROM media, media_type WHERE media.ID=$ID AND media.tipo = media_type.ID");
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

			$script			= stripslashes($rs->db_data["Script"]);
			$permanente		= $rs->db_data["Permanente"];
			$Descritivo		= $rs->db_data["Descritivo"];
			$tema			= $rs->db_data["TemaID"];
		}
		$rs->db_close(1);
	}
	else $tipo = $ID_ORG;

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";



















?>

<?

if(isset($Familia) && $Familia == 0 && $comportamento==23) // é família e é do tipo Multilevel Gallery
{
?>
<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
    <tr>
        <td valign="top">
           <?
            //$hRes=$rs->db_open($db_link,$DB_name,"Select * from media where Tipo='".$ID."' order by Peso ASC");
			$hRes=$rs->db_open($db_link,$DB_name,"Select * from media where Familia='".$ID."' order by Peso ASC");
            $t_to=$rs->db_rows;
            if($t_to>0)
            {?>
            <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                <tr>
                    <td valign="top"><?
                        if($comportamento==0) show_media0($ID,$largura,$altura,$select_image,$bg_color); // Não tem comportamento mostra só um com maior peso
                        if($comportamento==1) show_media1($ID,$largura,$altura,$select_image,$bg_color); // Aleatório ao abrir a página
                        if($comportamento==2) show_media2($ID,$largura,$altura,$select_image,$bg_color); // Automático Aleatorio
                        if($comportamento==3) show_media3($ID,$largura,$altura,$select_image,$bg_color); // Slide
                        if($comportamento==4) show_media4($ID,$largura,$altura,$select_image,$bg_color); // Fade In
                        if($comportamento==5) show_media5($ID,$largura,$altura,$select_image,$bg_color);	// Fade In com slide descritivo topo
                        if($comportamento==6) show_media6($ID,$largura,$altura,$select_image,$bg_color);	// Fade In com slide descritivo direita
                        if($comportamento==7) show_media7($ID,$largura,$altura,1,$bg_color);	// Fade In com fundo
                        if($comportamento==8) show_media8($ID,$largura,$altura,1,$bg_color);	// Fade In com seleccao por baixo
                        if($comportamento==9) show_media9($ID,$largura,$altura,1,$bg_color);	// Video
                        if($comportamento==10) show_media10($ID,$largura,$altura,1,$bg_color);	// Video
                        if($comportamento==11) show_media11($ID,$largura,$altura,1,$bg_color);	// 3d 1
                        if($comportamento==12) show_media12($ID,$largura,$altura,1,$bg_color);	// galeria
                        if($comportamento==13) show_media13($ID,$largura,$altura,1,$bg_color);	// slideshow pro
                        if($comportamento==14) show_media14($ID,$largura,$altura,1,$bg_color,1);	// portfolio
                        if($comportamento==15) show_media15($ID,$largura,$altura,1,$bg_color,1);	// portfolio
                        if($comportamento==16) show_media16($ID,$largura,$altura,1,$bg_color,1);	// Fade In com seleccao por cima
                        if($comportamento==22) show_media22($ID,$largura,$altura,1,$bg_color,1);	// HOMEPAGE SIDE BANNERS
                        if($comportamento==23) show_media23($ID,$largura,$altura,1,$bg_color,1);	// HOMEPAGE SIDE BANNERS
                        ?>
                    </td>
                </tr>
            </table>
            <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=0>
            <tr style="background:<?=$cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
                <td>&nbsp;</td>
            </tr>
            </table><?
            }?>
            <form method="post" name="lista_paginas" action="pagina.php?ID=<?=$ID?>&dump=<?=uniqID("")?>" onSubmit="return checkFields()">
            <input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
            <input type="hidden" name="slashes" value="'">
            <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=2>
            <?













				// mostrar elementos
				$count2=0;
                $t=0;
				if(!$rs->EOF()) // família tem elementos - lista de elementos da família
                {?>
                    <tr>
                        <td height="30" width=29 align="center" bgcolor="#161716"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
                        <td colspan="6" bgcolor="#161716" class="branco">Selecionar todos</td>
                    </tr>
                    <?
                    while(!$rs->EOF())
                    {
                        $t++;
                        $codigo		= $rs->db_data["ID"];
						$familia	= $rs->db_data["Familia"];
                        $name		= $rs->db_data["Name"];
                        $I1			= $rs->db_data["img_ext"];
                        $visible	= $rs->db_data["Visible"];

                        if($count2 % 2 != 0)	$cor2="#E0E0E0";
                        else					$cor2="#c0c0c0";?>

                        <tr>
                            <td width="29" align="center"><input type="checkbox" name="chk_<?=$codigo?>" id="chk_<?=$codigo?>" value="<?=$codigo?>" class="cb" /></td>
                            <td width="35" style="text-align:left"><img title="<? if($visible) echo write_lang('key_editar_pagina','Editar Página'); else echo write_lang('key_pagina_invisivel','Página Invisivel');?>" src="<? if($visible) echo "ok.png"; else echo "hidden.png";?>" border=0 align="absmiddle" /></td>
                            <td width="40" bgcolor="<?=$cor2;?>" align="right"><?
                                if($I1!="")
                                {?><? if(file_exists("../../files/media/$codigo"."_1.".$I1)){?><img width=100 alt="" src="<?="../../files/media/$codigo"."_1.".$I1;?>"><? }
                                }?></td>
                            <td valign="top" bgcolor="<?=$cor2;?>" width="8"></td>
                            <td valign="middle" bgcolor="<?=$cor2;?>"><a class="preto" href="../media/pagina.php?ID=<?=$rs->db_data["ID"]?>&Familia=<?=$familia?>"><? echo "$name";?></a></td>
                            <td valign="middle" bgcolor="<?=$cor2;?>" align="right" width="80"><a class="italico" href="../media/pagina.php?ID=<?=$rs->db_data["ID"]?>&task=apagar"><img alt="" src="del.png" border=0 align="absmiddle"></a></td>
                            <td bgcolor="<?=$cor2;?>" width="10">&nbsp;</td>
                        </tr>
                        <?
                        $rs->db_next();
                        $count2++;
                    }
                    $rs->db_close(1);



                }
                else // família não tem elementos - botão para inserção de novo elemento
                {


				   	?>
                    <tr>
                        <td bgcolor="<?=$cor2;?>" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_novo_elemento','Inserir Novo Elemento');?>" onclick="javascript:window.open('../media/pagina.php?Familia=<?=$ID?>&Dump=<?=uniqid("");?>','_self')"></td>
                    </tr><?
                }





















				?>
				</table>
			<table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
					<tr>
						<td width="130px" class="t1">&nbsp;<?=write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
						<td width="125px"><select name="flag" id="flag" class="select1">
								<option value="" selected="selected"></option>
								<option value="1"><?=write_lang('key_apagar','Apagar');?></option>
								<option value="4"><?=write_lang('key_visivel_on','Visivel - (ON)');?></option>
								<option value="5"><?=write_lang('key_visivel_off','Visivel - (OFF)');?></option>

							</select>
						</td>
						<td width="5px"></td>
						<td>&nbsp;<input type="submit"  name="submeter" value="<?=write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
					</tr>
				</table>
			</form>
		</td>

        <!--
        <td width="10px">&nbsp;</td>
        <td valign="top" width="200"><? show_fast_tools();?></td>-->

	</tr>
</table>






<?
}
else // não é família
{
?>
 	<div class="center">
        <form name="form" method="post" action="pagina_trata.php?ID=<?=$ID?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data">
            <input type="hidden" name="slashes" value="'">
            <input type="hidden" name="Familia" value="<?=$Familia?>">
            <table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                    <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                    <tr>
                        <td valign="top">
                            <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                            <tr>
                                <td valign="top">

                                    <table width="100%" border=0 cellpadding="2" cellspacing=0>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_grupo','Grupo');?>:</td>
                                        <td align="left"><? banner_tipos($tipo);?></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_nome','Nome');?>:</td>
                                        <td align="left"><input name="name" style="WIDTH:100%" maxlength="255" value="<?=$name?>" class="new_input"></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_url','Url');?>:</td>
                                        <td align="left"><input name="url" style="WIDTH:100%" maxlength="255" value="<?=$url?>" class="new_input"></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_imagem_swf','Imagem ou SWF');?>:</td>
                                        <td align="left" class="preto">
                                            <?
                                            if($I1!="")
                                            {?><div class="imagens_400"><? if(file_exists("../../files/media/$ID"."_1.".$I1)){?><img alt="" src="<?="../../files/media/$ID"."_1.".$I1;?>"><? }?></div><?
                                                if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar');?> <input type='checkbox' name='Apagar_img1' value='1'> <? echo write_lang('key_ou','ou');?> <? echo write_lang('key_alterar','alterar');?> <input type='file' name='img_peq1'> <input type='hidden' name='tem_foto1' value='1'><? }
                                            }
                                            else{?><? echo write_lang('key_nova','Nova');?>: <input type=file name='img_peq1'> <input type='hidden' name='tem_foto1' value='0'><? }?></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_ou','Ou');?> <? echo write_lang('key_script','Script');?>: </td>
                                        <td align="left"><textarea name="script" id="script" style="WIDTH: 100%; HEIGHT: 200px;"><? echo htmlentities_memo($script); ?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_descricao','Descrição');?>:</td>
                                        <td align="left" class="preto"><textarea name="Descritivo" style="WIDTH: 100%; HEIGHT: 50px;"><?=$Descritivo;?></textarea></td>
                                    </tr>
                                    <tr>
                                        <td width="80" class="preto"> <? echo write_lang('key_configuracoes','Configurações');?>:</td>
                                        <td align="left">
                                            <table border=0 cellpadding="4" cellspacing=0 width="100%" style="border:#666666 1px solid;background-color:#bbcccc">
                                            <tr style="background-color:#bbcccc">
                                                <td class="preto" width="110" align="left" valign="top"><? echo write_lang('key_valido','Válido');?> <? echo write_lang('key_de','de');?>:</td>
                                                <td class="preto" align="left" valign="top"><? option_data($D);?></td>
                                                <td rowspan=5 valign=top class="preto">
                                                    <label><input type="checkbox" name="visible" value="1" <? if($visible=='1') echo "checked";?>> <? echo write_lang('key_ativo','Ativo');?></label><br>
                                                    <label><input type="checkbox" name="permanente" value="1" <? if($permanente=='1') echo "checked";?>> <? echo write_lang('key_banner_permanente','Banner Permanente');?></label>
                                                </td>
                                            </tr>
                                            <tr>
                                                <td class="preto" align="left" valign="top"><? echo write_lang('key_ate','até');?>:</td>
                                                <td class="preto" align="left" valign="top"><? option_data($D2,2);?></td>
                                            </tr>
                                            <tr>
                                                <td class="preto" align="left" valign="top"><? echo write_lang('key_peso','Peso');?>:</td>
                                                <td class="preto" align="left" valign="top"><input name="peso" style="WIDTH:100px; HEIGHT:20px;text-align:center" value="<?=$peso;?>"></td>
                                            </tr>
                                            <tr>
                                                <td class="preto" align="left" valign="top"><? echo write_lang('key_n_impressoes','N. impressões');?>:</td>
                                                <td class="preto" align="left" valign="top"><input name="n_impressoes" style="WIDTH:100px; HEIGHT:20px;text-align:center" value="<?=$n_impressoes;?>"> <? echo write_lang('key_total_de_impressoes','Total de impressões');?>: <? if(!$t_impressoes) echo 0; else echo $t_impressoes;?></td>
                                            </tr>
                                            <tr>
                                                <td class="preto" align="left" valign="top"><? echo write_lang('key_n_cliques','N. cliques');?>:</td>
                                                <td class="preto" align="left" valign="top"><input name="n_cliques" style="WIDTH:100px; HEIGHT:20px;text-align:center" value="<?=$n_cliques;?>"> <? echo write_lang('key_total_de_cliques','Total de cliques');?>: <? if(!$t_cliques) echo 0; else echo $t_cliques;?></td>
                                           </tr>
                                           </table>
                                        </td>
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
        <td valign="top" width="200"><? show_fast_tools();?></td>
    </tr>
    </table>
    </form>

<?
	// include "../layout/bottom.inc.php";
}

?>
</div>
<?

include "../layout/bottom.inc.php";
?>