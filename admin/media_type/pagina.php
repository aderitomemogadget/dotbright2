<?
	include "settings.inc.php";
	if($task=='apagar')	{$task_button=write_lang('key_apagar','Apagar');$task_form='apaga';$btn_class='btn-primary';}
	elseif($ID==0)		{$task_button=write_lang('key_inserir','Inserir');$task_form='insere';$btn_class='btn-primary';}
	else				{$task_button=write_lang('key_alterar','Alterar');$task_form='altera';$btn_class='btn-primary';}
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$fields=db_list_fields($DB_name,'media_type',$db_link);
	$columns=db_num_fields($fields);
	for ($i=0;$i < $columns;$i++){$field_array[$i]=db_field_name($fields,$i);}
	if(!in_array('Comportamento',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD Comportamento INT(2) NOT NULL');
	if(!in_array('altura',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD altura DOUBLE NOT NULL');
	if(!in_array('largura',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD largura DOUBLE NOT NULL');
	if(!in_array('select_image',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD select_image tinyint(1) NOT NULL');
	if(!in_array('tipo_fundo',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD tipo_fundo tinyint(10) NOT NULL');
	if(!in_array('legendas',$field_array)) $res=Runquery($db_link,$DB_name,'ALTER TABLE media_type ADD legendas tinyint(1) NOT NULL');
	if($ID!=0)
	{
		$rs=GetRSRecords2($ID);
		if($rs)
		{
			$nome=$rs->db_data["Nome"];
			$tipo=$rs->db_data["Tipo"];
			$help=$rs->db_data["Help"];
			$comportamento=$rs->db_data["Comportamento"];
			$altura=$rs->db_data["altura"];
			$largura=$rs->db_data["largura"];
			$select_image=$rs->db_data["select_image"];
			$tipo_fundo=$rs->db_data["tipo_fundo"];
			$bg_color=$rs->db_data["bg_color"];
			$aleatoria=$rs->db_data["aleatoria"];
			$legendas=$rs->db_data["legendas"];
			$bg_color=str_replace("#","",$bg_color);
			$Protegido=$rs->db_data["Protegido"];
		}
		$rs->db_close();
	}
	// apagar
	if($flag==1)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$res=Runquery($db_link,$DB_name,"delete from media WHERE ID=$str_id");
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// tornar visivel
	if($flag==4)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE media SET visible='1' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}
	// tornar invisivel
	if($flag==5)
	{
		foreach($_POST as $varName=> $value)
		{
			$dv=$value;
			$str_id=substr($varName,strpos($varName,"_")+1);
			if($str_id > 0 && substr_count($varName,"chk_") > 0)
			{
				$sql="UPDATE media SET visible='0' WHERE ID=$str_id";
				$res=Runquery($db_link,$DB_name,$sql);
				if(!$res)echo db_error($db_link).$sql."<br>";
			}
		};
	}

	include "../layout/header.inc.php";
	include "../layout/body.inc.php";
?>
<script>
function mostra_layer()
{
	tipo =document.getElementById("comportamento").value;
	if(tipo==3) document.getElementById("mostra").style.display="block";
	else if(tipo==4) document.getElementById("mostra").style.display="block";
	else if(tipo==5) document.getElementById("mostra").style.display="block";
	else if(tipo==6) document.getElementById("mostra").style.display="block";
	else document.getElementById("mostra").style.display="none";
}
</script>


    <div class="center">
<link rel="stylesheet" type="text/css" href="sa.css" />
<link rel="stylesheet" type="text/css" href="chosen.css?dp=<?php echo dumpacache?>" />
   		<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top" align="left"><? include "../layout/menus.inc.php"; ?>
                <div class="panel panel-inverse overflow-hidden m-b-12">
			        <div class="panel-heading">
			            <h4 style="color:#fff">MEDIA</h4>
			        </div>
			    </div>
            	<form name="form" method="post" action="pagina_trata.php?ID=<?php echo $ID?>&task_form=<?php echo $task_form?>&dump=<?php echo uniqID("")?>" enctype="multipart/form-data">
                <input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>">
                <input type="hidden" name="slashes" value="'">
                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
				<tr>
					<td valign="top">
					<table width="100%" border=0 cellspacing=0 cellpadding="5" class="subtitulo_janela">
					<tr>
					<td valign="middle"><a href="../media/list.php" class="brancoh"> &#60; Voltar para a lista de Media</a></td>
					</tr>
					</table>
					</td>
					</tr>
                    <tr>
                        <td valign="top">
                            <table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="F6F6F6" class="border1">
                            <tr>
                            	<td valign="top">
                                    <table width="100%" border=0 cellpadding="2" cellspacing=0>
                                    <tr>
                                        <td class="preto" align="left" valign="top" width="100"><? echo write_lang('key_nome','Nome');?>:</td>
                                        <td align="left" valign="top"><input name="nome"  maxlength="255" value="<?php echo $nome;?>" class="form-control" />    </td>
                                    </tr>
                                    <tr>
                                        <td class="preto" align="left" valign="top"><? echo write_lang('key_efeito','Efeito');?>:</td>
                                        <td align="left" valign="top">
                                        <select name="comportamento" id="comportamento" onChange="mostra_layer()" class="form-control" style="width:300px">
                                        <?
											for($a=0; $a<count($publicidade['codigo']);$a++)
											{
												echo '<option value="'.$publicidade['codigo'][$a].'"';
												if($comportamento==$publicidade['codigo'][$a]) echo 'selected="selected"';
												echo '>';
												echo $publicidade['nome'][$a];
												echo '</option>';
											}
										?>
                                        </select>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="preto" align="left" valign="top" width="100"><? echo write_lang('key_dimensoes','Dimensões');?>:<BR></td>
                                        <td align="left" valign="top">
                                            <table border=0 cellspacing=0 cellpadding=0>
                                            <tr>
                                                <td><input type="text" value="<?php echo $largura?>" name="largura" style="width:70px"  class="form-control" ></td>
                                                <td width="15" align="center" class="preto">x</td>
                                                <td align="center"><input type="text" value="<?php echo $altura?>" name="altura" style="width:70px"  class="form-control" /></td> <td width="15" class="preto" align="right">px</td>
                                                <td class="preto" align="right">&nbsp;&nbsp;(<? echo write_lang('key_largura','Largura');?> x <? echo write_lang('key_altura','Altura');?> px)&nbsp; <? echo write_lang('key_or','ou');?> (<? echo write_lang('key_em','em');?> %) <? echo write_lang('key_largura','exemplo');?>: (100% x 50)</td>
                                            </tr>
                                            </table>
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="preto" align="left" valign="top" width="100"><? echo write_lang('key_cor_de_fundo','Cor de fundo');?>:</td>
                                        <td class="preto"><input  class="form-control"  name="bg_color" type="text" id="bg_color" value="<?php echo $bg_color?>" style="width:100px">&nbsp;
                                        </td>
                                	</tr>
                                    </table>
                    			</td>
                            </tr>
                            </table>
                        </td>
                    </tr>
                    </table>
					<?php show_page_buttons($btn_class);?>
					</form><?
					if($ID)
					{?>
						<table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding="0" align="center">
                        <tr>
                            <td valign="top">
                               <?php
								$hRes=$rs->db_open($db_link,$DB_name,"SELECT * FROM media WHERE Tipo='".$ID."' AND Familia = 0 ORDER BY Peso ASC");
								//$hRes=$rs->db_open($db_link,$DB_name,"SELECT * FROM media WHERE Tipo='".$ID."' ORDER BY Peso ASC");
                                $t_to=$rs->db_rows;?>
                                <form method="post" name="lista_paginas" action="pagina.php?ID=<?php echo $ID?>&dump=<?php echo uniqID("")?>" onSubmit="return checkFields()">
                                <input type="hidden" name="m_lang" value="<?php echo $admin_lang;?>">
                                <input type="hidden" name="slashes" value="'">
                                <table class="new_quadros" width="100%" border=0 cellspacing=0 cellpadding=2>
                                <?php
                                    $count2=0;
                                    $t=0;
                                    if(!$rs->EOF())
                                    {?>
                                        <tr>
                                            <td height="30" width=29 align="center" bgcolor="#161716"><input onClick='makeSelection("filter","lista_paginas")' type="checkbox" name="filter" id="filter" value="1" class="cb"></td>
                                            <td colspan="7" bgcolor="#161716" class="branco">Selecionar todos</td>
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
                                            $LINGUA	= $rs->db_data["LINGUA"];

											if( $visible)
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
                                                <td width="40" bgcolor="<?php echo $cor2;?>" align="right"><div style="max-width:100px;max-height:100px;overflow:hidden">
												<?php if($I1 != '' && file_exists('../../files/media/'.$codigo.'_1.'.$I1)){?><a class="preto" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&Familia=<?php echo $familia?>"><img width="100" alt="" src="<?php echo '../../files/media/'.$codigo.'_1.'.$I1;?>"></a><?php }?></div></td>
                                                <td valign="top" bgcolor="<?php echo $cor2;?>" width="8"></td>
                                                <td valign="middle" bgcolor="<?php echo $cor2;?>"><a class="preto" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&Familia=<?php echo $familia?>"><?php echo ($name != '' ? $name : 'SEM NOME');?></a></td>
                                                <td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="180">LINGUA (<?php echo get_banner_lingua($LINGUA);?>)</td>
                                                <td valign="middle" bgcolor="<?php echo $cor2;?>" align="right" width="80"><a class="italico" href="../media/pagina.php?ID=<?php echo $rs->db_data["ID"]?>&task=apagar"><img alt="" src="del.png" border=0 align="absmiddle"></a></td>
                                                <td bgcolor="<?php echo $cor2;?>" width="10">&nbsp;</td>
                                            </tr>
                                            <?
                                            $rs->db_next();
                                            $count2++;
                                        }
                                        $rs->db_close(1);
                                    }
                                    else
                                    {

										if(in_array($comportamento,$comportamentos_arr))
										{
										?>
                                        <tr>
                                            <td bgcolor="<?php echo $cor2;?>" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_nova_galeria','Inserir Nova Galeria');?>" onclick="javascript:window.open('../media/pagina.php?ID=0&ID_ORG=<?php echo $ID?>&Dump=<?php echo uniqid("");?>','_self')"></td>
                                        </tr>
										<?
										}else{
										?>
										 <tr>
                                        <td bgcolor="<?php echo $cor2;?>" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<? echo write_lang('key_inserir_novo_elemento','Inserir Novo Elemento');?>" onclick="javascript:window.open('../media/pagina.php?ID=0&ID_ORG=<?php echo $ID?>&Dump=<?php echo uniqid("");?>','_self')"></td>
                                    </tr>
									<?
										}
                                    }
                            ?>
                            </table>
                            <table width="100%" class=p1 border=0 cellspacing=0 cellpadding=0>
                            <tr>
                                <td width="130px" class="t1">&nbsp;<?php echo write_lang('key_com_os_selecionados','Com os selecionados');?>:</td>
                                <td width="125px"><select name="flag" id="flag" class="select1">
                                        <option value="" selected="selected"></option>
                                        <option value="1"><?php echo write_lang('key_apagar','Apagar');?></option>
                                        <option value="4"><?php echo write_lang('key_visivel_on','Visivel - (ON)');?></option>
                                        <option value="5"><?php echo write_lang('key_visivel_off','Visivel - (OFF)');?></option>

                                    </select>
                                </td>
                                <td width="5px"></td>
                                <td>&nbsp;<input type="submit"  name="submeter" value="<?php echo write_lang('key_aplicar_accao','Aplicar Ação');?>" class="btn btn-primary" style="width:120px"></td>
                            </tr>
                     	</table>
                 	</form>
                </td>
            </tr>
        </table>

		<? }?>
    </td>
    <td width="10px">&nbsp;</td>
    <td valign="top" width="200"><? show_fast_tools();?><br /><? show_easy_create()?></td>
</tr>
</table></div>
<script src="chosen.jquery.min.js" type="text/javascript"></script>
<script type="text/javascript"> $(".chzn-select").chosen(); $(".chzn-select-deselect").chosen({allow_single_deselect:true}); </script>
<?php include "../layout/bottom.inc.php";?>