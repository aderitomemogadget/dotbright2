<?
	include "settings.inc.php";
	include "../../layout/header.inc.php";
	include "../../layout/body.inc.php";
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
    <td valign="top" align="left"><? include "../../layout/menus.inc.php"; ?>
        <div class="panel panel-inverse overflow-hidden m-b-12">
            <div class="panel-heading">
                <h4 style="color:#fff">NEWSLETTER - EMAILS</h4>
            </div>
        </div>

        <table width="100%" border=0 cellspacing=0 cellpadding=0 class="p2">
        <tr>
        	<td width="10"></td>
            <td class="text-left"><a class="branco" href="grupos.php?ID=0">Criar Novo Grupo</a></td>
            <td class="text-right branco">


                <form action="nivel1/list.php">
                	Procurar email: <input type="text" name="email">&nbsp;<input type="submit"  name="ok" value=" ok ">
                </form>

            </td>
            <td width="10"></td>
        </tr>
        </table>


        <table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="5">
        <tr>
            <td bgcolor="#E0E0E0">


            <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
                <td width="10" height="30" bgcolor="#E0E0E0"></td>
                <td width="1" bgcolor="#E0E0E0"></td>
                <td width="1" bgcolor="#E0E0E0"></td>
                <td bgcolor="#E0E0E0" class="preto">Grupos</td>
                <td width="100" align="right" bgcolor="#E0E0E0" class="preto">Emails registados</td>
                <td width="100" align="right" bgcolor="#E0E0E0" class="preto">Emails removidos</td>
                <td width="250" bgcolor="#E0E0E0"></td>
            </tr>
            <?
            $data=BuscarRegistosNew(0,"nl_grupos","nl_grupos_linguas","nl_grupos.ID=nl_grupos_linguas.ID_PAI","nl_grupos_linguas.Lang='$site_lang'","Descricao","nl_grupos.ID,nl_grupos.Visivel,nl_grupos_linguas.Descricao");
            $count=0;
            $total1=0;
            $total2=0;

            global $DB_host,$DB_user,$DB_pass,$DB_name;
            while(!$data->EOF())
            {
                $codigo=$data->db_data["ID"];
                $Visivel=$data->db_data["Visivel"];
                $descricao=$data->db_data["Descricao"];

                $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
                $obj=new DataObj;
                $sql="select count(*) as Num from nl_emails_registados where ID_ORG=$codigo";
                $hRes=$obj->db_open($db_link,$DB_name,$sql);
                $numero=$obj->db_data["Num"];
                $total1=$total1+$numero;
                $obj->db_close();

                $sql="select count(*) as Num from nl_emails_removidos where ID_ORG=$codigo";
                $hRes=$obj->db_open($db_link,$DB_name,$sql);
                $numero2=$obj->db_data["Num"];
                $obj->db_close();
                $total2=$total2+$numero2;

                if($Visivel)
                {
                    if($count % 2 !=0)	$cor="#CCFF00";
                    else				$cor="#EBFF98";
                }
                else
                {
                    if($count % 2 !=0)	$cor="#E0E0E0";
                    else				$cor="#F6F6F6";
                }?>
                <tr>
                    <td bgcolor="<?=$cor;?>">&nbsp;</td>
                    <td bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="grupos.php?ID=<?=$data->db_data["ID"]?>&task=alterar"><img title="Editar grupo" src="../../images/edit.png" width="16" height="16" border="0" align="absmiddle"></a></td>
                    <td bgcolor="<?=$cor;?>">&nbsp;</td>
                    <td bgcolor="<?=$cor;?>" class="preto">&nbsp;<a class="italico" href="grupos.php?ID=<?=$data->db_data["ID"]?>&task=alterar"><? if($descricao) echo htmlentities($descricao); else echo "[Editar]"?></a></td>
                    <td align="right" bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="nivel1/list.php?ID_ORG=<?=$codigo?>&num=50"><?=number_format($numero,0,"",".")?> emails <img title="Ver emails" src="../../images/user.png" width="16" height="14" border="0" align="absmiddle"></a></td>
                    <td align="right" bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="nivel2/list.php?ID_ORG=<?=$codigo?>"><?=number_format($numero2,0,"",".")?> emails <img title="Ver emails" src="../../images/user.png" width="16" height="14" border="0" align="absmiddle"></a></td>
                    <td align="right" bgcolor="<?=$cor;?>" class="preto">
                        <a class="italico" href="mover.php?ID=<?=$data->db_data["ID"]?>">&nbsp;&nbsp;&nbsp;Mover emails</a>
                        <a class="italico" href="grupos.php?ID=<?=$data->db_data["ID"]?>&task=alterar">&nbsp;&nbsp;&nbsp;Editar Grupo</a>
                        <a class="italico" href="grupos.php?ID=<?=$data->db_data["ID"]?>&task=apagar">&nbsp;&nbsp;&nbsp;Apagar Grupo</a>
                    </td>
                </tr><?
                $data->db_next();
                $count++;
            }
            $data->db_close(1);
            ?>
            <!-- <tr>
                <td height="10"></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="preto" align="right"><?=number_format($total1,0,"",".")?> emails </td>
                <td width="100" class="preto" align="right"><?=number_format($total2,0,"",".")?> emails</td>
                <td></td>
            </tr>
            <tr>
                <td height="10"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td width="100" align="right" class="preto"></td>
                <td></td>
            </tr> -->
            </table>

                   <table width="100%" border="0" cellspacing="0" cellpadding="2">
            <tr>
                <td width="10" height="30" bgcolor="#E0E0E0"></td>
                <td width="1" bgcolor="#E0E0E0"></td>
                <td width="1" bgcolor="#E0E0E0"></td>
                <!-- <td bgcolor="#E0E0E0" class="preto">Sem Grupo</td> -->
                <td bgcolor="#E0E0E0" class="preto">&nbsp;</td>
                <!-- <td width="100" align="right" bgcolor="#E0E0E0" class="preto">Emails registados</td> -->
                <!-- <td width="100" align="right" bgcolor="#E0E0E0" class="preto">Emails removidos</td> -->
                <td width="100" align="right" bgcolor="#E0E0E0" class="preto">&nbsp;</td>
                <td width="100" align="right" bgcolor="#E0E0E0" class="preto">&nbsp;</td>
                <td width="250" bgcolor="#E0E0E0"></td>
            </tr>
            <?

                $db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
                $obj=new DataObj;
                $sql="select count(*) as Num from nl_emails_registados where ID_ORG=0";
                $hRes=$obj->db_open($db_link,$DB_name,$sql);
                $numero=$obj->db_data["Num"];
                $total3=$total3+$numero;
                $obj->db_close();

                $sql="select count(*) as Num from nl_emails_removidos where ID_ORG=0";
                $hRes=$obj->db_open($db_link,$DB_name,$sql);
                $numero2=$obj->db_data["Num"];
                $obj->db_close();
                $total4=$total4+$numero2;

                if($Visivel)
                {
                    if($count % 2 !=0)  $cor="#CCFF00";
                    else                $cor="#EBFF98";
                }
                else
                {
                    if($count % 2 !=0)  $cor="#E0E0E0";
                    else                $cor="#F6F6F6";
                }?>
                <tr>
                    <td bgcolor="<?=$cor;?>">&nbsp;</td>
                    <!-- <td bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="grupos.php?ID=0&task=alterar"><img title="Editar grupo" src="../../images/edit.png" width="16" height="16" border="0" align="absmiddle"></a></td> -->
                    <td bgcolor="<?=$cor;?>" class="preto">&nbsp;</td>
                    <td bgcolor="<?=$cor;?>">&nbsp;</td>
                    <td bgcolor="<?=$cor;?>" class="preto">&nbsp;<a class="italico">Sem Grupo</a></td>
                    <!-- <td bgcolor="<?=$cor;?>" class="preto">&nbsp;<a class="italico" href="grupos.php?ID=0&task=alterar">Sem Grupo</a></td> -->
                    <td align="right" bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="nivel1/list.php?ID_ORG=0&num=50"><?=number_format($numero,0,"",".")?> emails <img title="Ver emails" src="../../images/user.png" width="16" height="14" border="0" align="absmiddle"></a></td>
                    <td align="right" bgcolor="<?=$cor;?>" class="preto"><a class="italico" href="nivel2/list.php?ID_ORG=0"><?=number_format($numero2,0,"",".")?> emails <img title="Ver emails" src="../../images/user.png" width="16" height="14" border="0" align="absmiddle"></a></td>
                    <td align="left" bgcolor="<?=$cor;?>" class="preto" >
                        <a class="italico" style="margin-left: 20px" href="mover.php?ID=0">&nbsp;&nbsp;&nbsp;Mover emails</a>
                                <!--<a class="italico" href="grupos.php?ID=0&task=alterar">&nbsp;&nbsp;&nbsp;Editar Grupo</a>-->
                                <!--<a class="italico" href="grupos.php?ID=0&task=apagar">&nbsp;&nbsp;&nbsp;Apagar Grupo</a>-->
                    </td>
                </tr><?
            ?>
            <!-- <tr>
                <td height="10"></td>
                <td></td>
                <td></td>
                <td></td>
                <td class="preto" align="right"><?=number_format($total3,0,"",".")?> emails </td>
                <td width="100" class="preto" align="right"><?=number_format($total4,0,"",".")?> emails</td>
                <td></td>
            </tr>
            <tr>
                <td height="10"></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td width="100" align="right" class="preto"></td>
                <td></td>
            </tr> -->
            </table>

            <table class="new_quadros" width="100%" border="0" cellspacing="1" cellpadding="2">
            <tr>
                <td>
                    <table width="100%" cellspacing="2" cellpadding="0">
                    <tr>
                        <td width="30">
                            <table width="20" height="20" border=1 cellspacing="0" cellpadding="0" bgcolor="#CCFF00" bordercolor="#000000"><tr><td>&nbsp;</td></tr></table>
                        </td>
                        <td width="30">
                            <table width="20" height="20" border=1 cellspacing="0" cellpadding="0" bgcolor="#EBFF98" bordercolor="#000000"><tr><td>&nbsp;</td></tr></table>
                        </td>
                        <td class=preto>&nbsp;Grupos visíveis no site na &aacute;rea de registo de utilizador</td>
                    </tr>
                    </table>
                </td>
            </tr>
            <tr>
                <td>
                    <table width="100%" cellspacing="2" cellpadding="0">
                    <tr>
                        <td width="30">
                            <table width="20" height="20" border=1 cellspacing="0" cellpadding="0" bgcolor="#E0E0E0" bordercolor="#000000"><tr><td>&nbsp;</td></tr></table>
                        </td>
                        <td width="30">
                            <table width="20" height="20" border=1 cellspacing="0" cellpadding="0" bgcolor="#F6F6F6" bordercolor="#000000"><tr><td>&nbsp;</td></tr></table>
                        </td>
                        <td class=preto>&nbsp;Grupos apenas para uso interno na area reservada</td>
                    </tr>
                    </table>
                </td>
            </tr>
            </table>
			</td>
        </tr>
        </table>

    </td>
    <td width="10px">&nbsp;</td>
	<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>
<? 	include "../../layout/bottom.inc.php";?>