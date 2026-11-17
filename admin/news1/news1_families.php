<?
	include "settings.inc.php";
	if($task=="apagar_draft"){$task_button=write_lang('key_apagar_este_draft','Apagar este Draft');$task_form='apaga';}
	elseif($task=='apagar'){$task_button=write_lang('key_apagar_este_registo','Apagar este Registo');$task_form='apaga';}
	elseif($task=='duplicar'){$task_button=write_lang('key_duplicar_este_registo','Duplicar este Registo');$task_form='duplicar';}
	elseif($ID==0){$task_button=write_lang('key_gravar_o_novo_registo','Gravar o Novo Registo');$task_form='insere';}
	elseif($draft_id) {$task_button=write_lang('key_gravar_draft','Gravar Draft');$task_form='altera';}
	else {$task_button=write_lang('key_gravar_alteracoes','Gravar alteraçőes');$task_form='altera';}

	if(trim($Nivel)=="") $Nivel=0;
	if(trim($total_prods=="")) $total_prods=200;
	if(trim($from_prods=="")) $from_prods=0;
	if(trim($to_prods=="")) $to_prods=$total_prods;
	$limite_from=$from_prods;
	$limite_to=$to_prods;
	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
	global $DB_host,$DB_user,$DB_pass,$DB_name,$n_imagens_familia;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;$rs2=new DataObj;



?>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckeditor.js"></script>
<script type="text/javascript" src="../editor/ckeditor_4_5_1/ckfinder/ckfinder.js"></script>
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
				//alert(v+" - "+obj);
				 if(ar[i].className=="submenu") ar[i].style.display="none";
			}

			for (var i=0; i<ar2.length; i++){
				v="sub"+(i+1);
				//alert(v+" - "+obj);
				 if(ar2[i].className=="branco") ar2[i].style.background="#5694B4";
			}
			el.style.display="block";
			el2.style.background="#000000";
		}else{
			//el.style.display="none";
		}
	}

}
window.onload =function(){<? if($t1 && $t2){?>showmenu_prod('<?=$t1?>','<?=$t2?>');<? }else{?>showmenu_prod('sub1','pinta1');<? }?>}
</script>
<div class="center">
	<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
    <tr>
    	<td align="left" valign="top"><? include "../layout/menus.inc.php"; ?>
            <div class="panel panel-inverse overflow-hidden m-b-12">
                <div class="panel-heading">
                    <h4 style="color:#fff">NOTÍCIAS</h4>
                </div>
            </div>

					<?
                    if(!$pesquisa && !$pesquisa2)
                    {
                        $C="";$T="";$I1="";$I2="";
                        $ordem=0;
                        if($ID!=0)
                        {
							$hRes=$rs->db_open($db_link,$DB_name, "Select * from news1_families where ID=$ID order by Ordem Desc");
                            if($rs)
                            {
                                $D=str2date($rs->db_data["Data"]);
                                $I1=$rs->db_data["img_ext"];
                                $I2=$rs->db_data["img2_ext"];
                                $I3=$rs->db_data["img3_ext"];
                                $ordem=$rs->db_data["Ordem"];
                                $id_org=$rs->db_data["ID_ORG"];
                                $cor=$rs->db_data["cor"];
                                $visivel=$rs->db_data["visivel"];
                                $destaque=$rs->db_data["Destaque"];
                                $margemcli=$rs->db_data["MargemCli"];
                                $margemrev=$rs->db_data["MargemRev"];
                                $cat1=$rs->db_data["cat1"];
                                $cat2=$rs->db_data["cat2"];
                                $cat3=$rs->db_data["cat3"];
                                $download=$rs->db_data["Download"];
								$tipo_noticia=$rs->db_data["Tipo"];
                            }
                            $rs->db_close();
							//********* linguas ***//
							$hRes=$rs->db_open($db_link,$DB_name, "Select * from news1_families_lang where ID_PAI=$ID and lang='$admin_lang'");
							$C = htmlentities_memo(stripslashes($rs->db_data["Cabecalho"]));
							$T = stripslashes($rs->db_data["Texto"]);
							$areas = stripslashes($rs->db_data["Areas"]);
							$URL1=$rs->db_data["URL"];
							$rs->db_close();
                        }
                        ?><form name="form" method="post" action="news1_families_trata.php?prev=0&ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&task_form=<?=$task_form?>&dump=<?=uniqID("")?>" enctype="multipart/form-data"  accept-charset="UTF-8">
                            <input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
                            <input type="hidden" name="slashes" value="'">
                            <input type="hidden" name="tab_a" value="0">
                            <input type="hidden" name="tab_b" value="0">
                            <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                                <td>

                                    <table width="100%" height="30px" border=0 cellspacing=0 cellpadding="5" class="titulo_janela">
                                    <tr>
                                        <td><a href="list.php" class="new_caminho"><? echo write_lang('key_inicio','Ínicio')?></a> - <? show_news1_menu_path($ID, "news1_families.php", "news1_families",$total_prods);?></td>
                                    </tr>
                                    </table>
                                    <div id="masterdiv"><br>
                                        <table height="20" border=0 cellspacing=0 cellpadding=0>
                                        <tr>
                                            <td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub1','pinta1')" style="cursor:pointer" id="pinta1"><? echo write_lang('key_categoria','CATEGORIA')?></td>
                                            <? /*<td><img src="cm_fill.gif" width="8" height="1"></td>
                                            <td><img src="cm_fill.gif" width="8" height="1"></td>
                                            <td width="220" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub2','pinta2')" style="cursor:pointer" id="pinta2"><? echo write_lang('key_imagens','IMAGENS')?></td>
                                            <td width="120" bgcolor="#6699CC" align="center" class="branco" onClick="showmenu_prod('sub3','pinta3')" style="cursor:pointer" id="pinta3"><? echo write_lang('key_caracteriticas','CARACTERÍSTICAS')?></td><td><img src="cm_fill.gif" width="8" height="1"></td>*/?>
                                        </tr>
                                        </table>
                                        <input name="Nivel" type="hidden" id="Nivel" value="<?=$Nivel;?>">
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub1">
                                        <table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="10">&nbsp;</td>
                                            <td class="preto" width="120"><? echo write_lang('key_ordem','Ordem')?>:</td>
                                            <td align="left" class="preto"><input class="form-control" name="ordem" style="WIDTH:100px" maxlength="5" value="<?=$ordem;?>"></td>
                                            <td width="4">&nbsp;</td>
                                        </tr>
                                       <tr>
                                            <td>&nbsp;</td>
                                            <td class="preto">Familia:</td>
                                            <td align="left"><? gera_lista_familias($ID_ORG,"news1_families","news1_families_lang");?></td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="preto"><? echo write_lang('key_nome','Nome')?>:</td>
                                            <td align="left"><input class="form-control" name="cabecalho" maxlength="255" value="<?=$C;?>"></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td class="preto">Tipo de Notícia:</td>
                                            <td align="left"><? gera_lista_tipo_noticia($tipo_noticia);?></td>
                                            <td>&nbsp;</td>
                                        </tr>

                                        <? /* <tr>
                                            <td>&nbsp;</td>
                                            <td class="preto">Áreas de Negócio:</td>
                                            <td align="left"><input name="areas" style="WIDTH:100%; HEIGHT:20px" maxlength="255" value="<?=$areas;?>">(inserir separadas por ;)</td>
                                            <td>&nbsp;</td>
                                        </tr>
                                       <tr>
                                            <td height=10></td>
                                            <td></td>
                                            <td class=preto><input type=checkbox name=destaque value=1 <? if($destaque) echo "checked";?>>&nbsp;<? echo write_lang('key_nome','Nome')?></td>
                                            <td></td>
                                        </tr>
										 <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td align="left" class=preto><input name="download" type="checkbox" value="1" <? if($download) echo "checked"; ?>> Put as menu</td>
                                            <td>&nbsp;</td>
                                        </tr>*/?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td align="left" class=preto><input name="visivel" type="checkbox" value="1" <? if($visivel) echo" checked"; ?>> <? echo write_lang('key_tornar_categoria_visivel','Tornar categoria visível')?></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        </table>
                                        </div>
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub2">
                                        <table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        <tr>
                                            <td width="10">&nbsp;</td>
                                            <td class="preto" align="left" width="163"><? echo write_lang('key_imagem','Imagem')?> 1:
                                                <table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td class="preto">(jpg | png | gif) 72 dpis</td></tr></table>
                                            </td>
                                            <td class="preto3" align="left"><?
                                            if($I1!="")
                                            {?>
                                              <div class="imagens_400"><img alt="" src="<? echo "../../files/news1_families/".$ID."_1.".$I1;?>"></div>
                                              <br><?
                                              if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_img1" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="img_peq1"><input type="hidden" name="tem_foto1" value="1"><? }
                                            }
                                            else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="img_peq1"><input type="hidden" name="tem_foto1" value="1"><? }?>
                                            </td>
                                            <td width="4">&nbsp;</td>
                                        </tr>
                                        <?/* if($n_imagens_familia>1){?> <tr>
                                            <td width="10">&nbsp;</td>
                                            <td class="preto" align="left" width="163"><? echo write_lang('key_imagem','Imagem')?> 2:
                                                <table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td class="preto">(jpg | png | gif) 72 dpis</td></tr></table>
                                            </td>
                                            <td class="preto3" align="left"><?
                                            if($I2!="")
                                            {?>
                                              <div class="imagens_400"><img alt="" src="<? echo "../../files/news1_families/".$ID."_2.".$I2;?>"></div>
                                              <br><?
                                              if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_img2" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="img_peq2"><input type="hidden" name="tem_foto2" value="1"><? }
                                            }
                                            else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="img_peq2"><input type="hidden" name="tem_foto2" value="0"><? }?>
                                            </td>
                                            <td width="4">&nbsp;</td>
                                        </tr><?
										}
										if($n_imagens_familia>2){?>
                                        <tr>
                                            <td width="10">&nbsp;</td>
                                            <td class="preto" align="left" width="163"><? echo write_lang('key_imagem','Imagem')?> 3:
                                                <table width="100%" border=0 cellspacing=0 cellpadding=0><tr><td class="preto">(jpg | png | gif) 72 dpis</td></tr></table>
                                            </td>
                                            <td class="preto3" align="left"><?
                                            if($I3!="")
                                            {?>
                                              <div class="imagens_400"><img alt="" src="<? echo "../../files/news1_families/".$ID."_3.".$I3;?>"></div>
                                              <br><?
                                              if($task!="apagar"){?><? echo write_lang('key_apagar','Apagar')?> <input type="checkbox" name="Apagar_img3" value="1"> <? echo write_lang('key_ou','ou')?> <? echo write_lang('key_alterar','Alterar')?> <input type="file" name="img_peq3"><input type="hidden" name="tem_foto3" value="1"><? }
                                            }
                                            else{?><? echo write_lang('key_nova','Nova')?>:<input type="file" name="img_peq3"><input type="hidden" name="tem_foto3" value="0"><? }?>
                                            </td>
                                            <td width="4">&nbsp;</td>
                                        </tr><?
										}*/?>
                                        <tr>
                                            <td>&nbsp;</td>
                                            <td></td>
                                            <td></td>
                                            <td>&nbsp;</td>
                                        </tr>
                                        </table>
                                        </div>
                                        <div style="position:relative; width: 100%; display:none;" class="submenu" id="sub3">
                                        <table width="100%" border=0 cellpadding="2" cellspacing=0 bgcolor="F6F6F6" class="border1">
                                        <tr>
                                            <td width="10">&nbsp;</td>
                                            <td>

                                                <table width="100%" border=0 cellpadding="2" cellspacing="2">
                                                <tr>
                                                    <td class="preto" width="200">Cor:</td>
                                                    <td align="left">
                                                        <table border=0 cellspacing=0 cellpadding=0>
                                                        <tr>
                                                            <td width="65%"><input type="text" disabled="disabled" style="height:17; width:15; background-color:<?=$cor?>">&nbsp;<input name="cor" type="text" style="height:17; width:80; font-family: Arial" value="<?=$cor?>">&nbsp;</td>
                                                            <td width="35%"><input id="pick1" onClick="cp2.select(document.forms[0].cor,'pick1');return false;" name="pick1" type="button" style="height:17; width:52; border-color:#000000; background-color:#FFFFFF; font-size:10px; cursor:hand" value="escolher"></td>
                                                        </tr>
                                                        </table>
                                                    </td>
                                                </tr>
                                                <!--tr>
                                                    <td class="preto" width="200">Margem Cliente(%):</td>
                                                    <td class="preto" align="left"><input name="margemcli" style="WIDTH:30; HEIGHT:20px" value="<?//=$margemcli;?>"></td>
                                                </tr>
                                                <tr>
                                                    <td class="preto" width="200">Margem Revendedor(%):</td>
                                                    <td class="preto" align="left"><input name="margemrev" style="WIDTH:30; HEIGHT:20px" value="<?//=$margemrev;?>"></td>
                                                </tr-->
                                                <tr>
                                                    <td class="preto" width="200">Texto:</td>
                                                    <td></td>
                                                </tr>
                                                </table>
                                                <textarea name="texto" style="WIDTH:100%; HEIGHT:50px"><?=$T;?></textarea><? show_editor("oEdit1","texto","99%",150)?>


                                        </td>
                                            <td width="4">&nbsp;</td>
                                        </tr>
                                        </table>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            </table>

                            <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
                            <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
                            <tr>
                                <td align="center"><input class="btn btn-primary" type="submit" name="Submit" value=" <?=$task_button;?> "> <input class="btn btn-secondary" type="button" name="cancelar" value="<? echo write_lang('key_cancelar','Cancelar')?>" onMouseUp="window.open('list.php','_self');"></td>
                            </tr>
                            </table>
                            <img src="../images/bsh.png" width="100%" height="30px">
                        </form>
                        <?
                        if($task_form=="altera")
                        {
							$rs->db_open($db_link,$DB_name, "Select * from news1_families where ID_ORG=$ID order by Ordem asc");
							if(!$rs->EOF())
							{?>
                            <form action="reordena2.php" method="post"><input type="hidden" name="url_return" value="news1_families.php?ID=<?=$ID?>&ID_ORG=<?=$ID_ORG?>&total_prods=<?=$total_prods?>&checkshowimages=<?=$checkshowimages?>">
                            <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                            <tr>
                            	<td>
                                    <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p1">
                                    <tr>
                                        <td width="5"></td>
                                        <td class="branco"> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?></td>
                                    </tr>
                                    </table>
                                    <?
									$count=0;
									while(!$rs->EOF())
									{
										$codigo=$rs->db_data["ID"];
										$ordem=$rs->db_data["Ordem"];
										$hRes=$rs2->db_open($db_link,$DB_name,"Select * from news1_families_lang where ID_PAI=$codigo and lang='$admin_lang'");
										$cabecalho = stripslashes($rs2->db_data["Cabecalho"]);
										$rs2->db_close();

										if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
										else				{$cor="#FFFFFF";$class="r2";$n="2";}?>
										<table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
										<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
										<tr>
											<td width="29" align="center"><img src="../images/page.png"></td>
											<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
											<td width="20" align="center" class="preto"><?=$codigo?></td>
											<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
											<td class="preto"><a class="preto" href="news1_families.php?ID=<?=$rs->db_data["ID"];?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&Nivel=<?=$Nivel+1;?>">&nbsp;<? if($cabecalho) echo htmlentities_b($cabecalho); else echo "[Editar]"?></a><?
											if($num_total) echo "&nbsp&nbsp<span style=\"{color:black;FONT-SIZE: 11px;}\">($num_total) TOTAL de notícias(s)</span>&nbsp&nbsp";
											if($num_prod_a) echo "&nbsp&nbsp<span style=\"{color:green;FONT-SIZE: 11px;}\">$num_prod_a Novo(s) ou Alterado(s)</span>&nbsp&nbsp";
											if($num_prod_p) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_d Em promoção</span>&nbsp&nbsp";
											if($num_prod_n) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_n Novidades</span>&nbsp&nbsp";
											if($num_prod_d) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_d Destaques</span>&nbsp&nbsp";
											if($num_prod_s) echo "&nbsp&nbsp<span style=\"{color:E65700;}\">$num_prod_s Saldos</span>&nbsp&nbsp";
											?></td>
											<td width="20" align="right" bgcolor="<?=$cor;?>"><a class="italico" href="news1_families.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=duplicar"><img title="<? echo write_lang('key_duplicar_pagina','Duplicar Página')?>" src="duplicate.png" border=0></a></td>
											<td width="20" align="right" bgcolor="<?=$cor;?>"><a class="italico" href="news1_families.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=apagar"><img title="<? echo write_lang('key_apagar_pagina','Apagar Página')?>" src="del2.png" border=0></a></td>
											<td class="ordem<?=$n?>"><input class="ordem" type=text name="chk_<?=$codigo?>" value="<?=$ordem?>"></td>
										</tr>
										</table><?
										$rs->db_next();
										$count++;
									}
									$rs->db_close();?>
                            	</td>
                            </tr>
                            </table>

                                <table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
                                <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
                                <tr>
                                    <td>&nbsp;<input class="btn btn-primary" type="submit" name="Reordenar" value="<? echo write_lang('key_reordenar','Reordenar')?> <? echo 'SUB-';?><? echo write_lang('key_categorias','Categories')?>"></td>
                                </tr>
                                </table>
                                <img src="../images/bsh.png" width="100%" height="30px">
                            </form><?
							}
                        }
                    }
                    if($task_form=="altera" || $pesquisa || $pesquisa2)
					// if($prod_act)
                    {
                        if($flag==1) // Apagar
                        {
                            foreach($_POST as $key => $valor)
                            {
                                if((strcmp(substr($key,0,4),"chk_")==0))
                                {
                                    $id=$valor;
                                    $img_path="../../files/news1/";
                                    DeleteProd($id,$img_path);
                                }
                            }
                        }
                        if($flag==2) // Reordenar
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key => $valor)
                            {
                                if((strcmp(substr($key,0,6),"field_")==0))
                                {
                                    $id=substr($key, strpos($key,"_")+1);
                                    $sql ="UPDATE news1 SET Ordem=$valor WHERE ID=$id";
                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($flag==3) // mudar de familia
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key => $valor)
                            {
                                if((strcmp(substr($key,0,4),"chk_")==0))
                                {
                                    $id=$valor;
                                    $sql ="UPDATE news1 SET ID_ORG=$m_familia WHERE ID=$id";
                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($flag==4) // actualiza preco
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key => $valor)
                            {
                                if((strcmp(substr($key,0,6),"preco_")==0))
                                {
                                    $prod_id=substr($key, strpos($key,"_")+1);
                                    $preco=$valor;
                                    $sql ="UPDATE news1 SET Preco1=$preco WHERE ID=$prod_id";

                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
                        if($flag==5) // actualiza preco
                        {
                            global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
                            $db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
                            foreach($_POST as $key => $valor)
                            {
                                if((strcmp(substr($key,0,13),"configurador_")==0))
                                {
                                    $prod_id=substr($key, strpos($key,"_")+1);
                                    $conf=$valor;
                                    $sql ="UPDATE news1 SET Configurador='$conf' WHERE ID=$prod_id";

                                    $res =Runquery($db_link,$DB_name,$sql);
                                    if(!$res)  echo db_error($db_link);
                                }
                            }
                            db_closedb_conection($db_link);
                        }
						if($m_familia) $ID=$m_familia;
						if($pesquisa)
						{
							$rs=Pesquisa_backoffice($pesquisa);
						}
						elseif($pesquisa2)
						{
							$rs=Pesquisa_backoffice($pesquisa2,2);
						}
						elseif($novos)
						{
							global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
							$db_link =db_opendb_conection($DB_host,$DB_user,$DB_pass);
							$where=" WHERE Aproved=0 ";
							$sql ="Select * from news1 $where order by Cabecalho Asc";
							$rs=new DataObj;
							$hRes=$rs->db_open($db_link,$DB_name,$sql);
						}
						else $rs=GetRecords_Recursos_1_1(0,$ID,$Nivel+1,"*",$limite_from,$limite_to,$total_prods);
						if($rs && !$rs->EOF())
                        {
					    ?><script language="JavaScript" type="text/javascript">
                            <!--
                            function makeSelection(selObj)
                            {
                                if(selObj.value==1)	for(i=0; i<document.form2.elements.length; i++)	document.form2.elements[i].checked=true;
                                if(selObj.value==2)	for(i=0; i<document.form2.elements.length; i++)	document.form2.elements[i].checked=false;
                            }
                            //-->
                            </script>
                            <form name="form2" method="post">
                                <input type="hidden" name="ID_Fam" value="<?=$ID?>">
                                <input type="hidden" name="ID" value="<?=$ID?>">
                                <input type="hidden" name="ID_ORG" value="<?=$ID_ORG?>">
                                <input type="hidden" name="total_prods" value="<?=$total_prods?>">
                                <input type="hidden" name="checkshowimages" value="<?=$checkshowimages?>">
                                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                                <tr>
                                    <td>

                                        <table width="100%" border=0 cellspacing=0 cellpadding=10 class="p2">
                                        <tr>
                                            <td width="5px">&nbsp;</td>
                                            <td class="branco"><? echo write_lang('key_noticias','Notícias')?></td>
                                            <td class="branco" align="right">
                                                    <? echo write_lang('key_pesquisar_por_nome','Pesquisar por nome')?>: <input style="width: 100px;" type="text" name="pesquisa" id="pesquisa" value="<?=$pesquisa?>">
                                                    <input class="btn btn-primary" type="submit" name="Pesquisar" value="Pesquisar">
                                             </td>
                                        </tr>
                                        </table>
                                        <?
                                        $count=0;
                                        while(!$rs->EOF())
                                        {
                                            $visivel=$rs->db_data["Aproved"];
                                            $codigo=$rs->db_data["ID"];
                                            $img_ext=$rs->db_data["img_ext"];
											$ordem=$rs->db_data["Ordem"];
                                            $ref=$rs->db_data["Ref"];
                                            $preco1=$rs->db_data["Preco1"];
                                            $preco2=$rs->db_data["Preco2"];
                                            $precoCusto=$rs->db_data["Preco_prec"];
                                            $ID=$rs->db_data["ID_ORG"];
                                            $importado=$rs->db_data["Importado"];
                                            $id_fornecedor=$rs->db_data["id_fornecedor"];
                                            $tipo=$rs->db_data["Tipo"];
                                            $configurador=$rs->db_data["Configurador"];
                                            //********* linguas ***//
                                            $hRes=$rs2->db_open($db_link,$DB_name, "Select * from news1_lang where ID_PAI=$codigo and lang='$admin_lang'");
                                            $C=stripslashes($rs2->db_data["Cabecalho"]);
                                            $T=$rs2->db_data["Texto1"];
                                            $rs2->db_close();

                                            if($count % 2 !=0)	{$cor="#f0f3f5";$class="r1";$n="1";}
                                            else				{$cor="#FFFFFF";$class="r2";$n="2";}
                                            ?><table height="50px" border=0 cellspacing=0 cellpadding=0 class="<?=$class?>">
											<tr onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?=$cor;?>'">
											<td width="29" align="center"><input type="checkbox" name="chk_<?=$codigo?>" id="chk_<?=$codigo?>" value="<?=$codigo?>" class="cb" /></td>
											<td width="4" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg" /></td>
											<td width="29" align="center"><img src="../images/page.png" /></td>
											<td><a class="preto" href="news1.php?ID=<?=$rs->db_data["ID"];?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&Nivel=<?=$Nivel+1;?>">[<?=$codigo?>]&nbsp;- <?=$ref?> - <? if($C) echo htmlentities_b($C); else echo "[Edit]"?> </a></td>
											<td width="20px" align="right" bgcolor="<?=$cor;?>"><a class="italico" href="news1.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=duplicar"><img title="Duplicar" src="duplicate.png" border="0" /></a></td>
											<td width="20px" align="right" bgcolor="<?=$cor;?>"><a class="italico" href="news1.php?ID=<?=$rs->db_data["ID"]?>&ID_ORG=<?=$ID;?>&ID_ORG_ANT=<?=$ID_ORG?>&task=apagar"><img title="<? echo write_lang('key_apagar','Apagar')?>" src="del2.png" border="0" /></a></td>
											<td width="4px" align="right"><img width="2" src="../images/s6_<?=$n?>.jpg"></td>
											<td width="30px" class="ordem<?=$n?>"><input class="ordem" type=text name="field_<?=$codigo?>" value="<?=$ordem?>"></td>
											<td width="1px"><? if($fotos && $img_ext && file_exists("../../files/news1/".$rs->db_data["ID"]."_1.".$rs->db_data["img_ext"])){ echo "<img width=100 height=100 src=\"".$site_http_url."files/news1/".$rs->db_data["ID"]."_1.".$rs->db_data["img_ext"]."\" border=0 align=absmiddle />";} ?>&nbsp;</td>
											</tr>
                                            </table><?
                                            $rs->db_next();
                                            $count++;
                                        }
                                        $rs->db_close();?>
                               			<table class=p1 width=100% border=0 cellspacing=0 cellpadding=2>
                                        <tr>
                                            <td class="branco" align="center">
                                        <?
                                        if(!$pesquisa && !$pesquisa2)
                                        {
                                            echo "($from_prods a $to_prods";
                                            $m_url="news1_families.php?ID=$ID&ID_ORG=$ID_ORG&Nivel=$Nivel&total_prods=$total_prods";
                                            show_num_pages2_backoffice($ID," news1.ID ","","news1",$m_url,$total_prods,$checkshowimages,$ordenacao);
                                        }
                                        ?>
                                        	</td>
                                        </tr>
                                        </table>
                                        <table class=p1 width=100% border=0 cellspacing=0 cellpadding=2>
                                        <tr>
                                            <td width=10 >&nbsp;</td>
                                            <td class="preto">
                                                <select name="filter" onChange="makeSelection(this);" style="font-size:9px">
                                                    <option value="" selected="selected">Seleccionar:</option>
                                                    <option value="1">Tudo</option>
                                                    <option value="2">Nenhuma</option>
                                                </select>
                                                <select name="flag" id="flag" style="font-size:9px">
                                                    <option value="" selected="selected">Escolher</option>
                                                    <option value="1">Apagar</option>
                                                    <option value="2">Reordenar</option>
                                                    <option value="3">Mover para a familia</option>
                                                    <option value="4">Actualizar preço</option>
                                                </select>
                                                <? gera_lista_familias($ID_ORG,"news1_families","news1_families_lang");?><input class="btn btn-primary" type="submit" name="OK" value="OK"><br />
                                            </td>
                                        </tr>
                                        </table>
                                    </td>
                                </tr>
                                </table>
                            </form><?
						}
                    }
                    ?>
         </td>
         <td width="10px">&nbsp;</td>
         <td width="200px" valign="top"><? show_fast_tools();?><br /><? //show_easy_create()?></td>
    </tr>
    </table>
</div>
<SCRIPT language="Javascript" src="ColorPicker2.js"></SCRIPT>
<script>
	var cp =new ColorPicker('window');
	var cp2 =new ColorPicker();
</script>
<SCRIPT language=JavaScript>cp.writeDiv()</SCRIPT>
<? 	include "../layout/bottom.inc.php";?>