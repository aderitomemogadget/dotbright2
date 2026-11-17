<?
	include "settings.inc.php";
	include "../../layout/header.inc.php";
	include "../../layout/body.inc.php";

	if(isset($_GET['ID'])) $ID=intval($_GET['ID']);

	if($task=="apagar"){$task_button = "Apagar este Registo"; $task_form = "apaga"; $btn_class='btn-danger';}
	elseif($ID==0){$task_button = "Gravar o Novo Registo"; $task_form = "insere"; $btn_class='btn-primary';}
	else {$task_button = "Gravar alterações"; $task_form = "altera"; $btn_class='btn-primary';}

	$Name="";	$Email="";
	$D=getdate();
	$D2=getdate();
	if($ID!=0)
	{
		$data = BuscarRegistos2($ID);
		if($data)
		{
			$visible=$data->db_data["Visivel"];
			$data2 =BuscarRegistosLinguas($ID);
			$descricao=$data2->db_data["Descricao"];
		}
		$data->db_close(1);
	}

?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
<tr>
	<td valign="top"><? include "../../layout/menus.inc.php"; ?>

    <!--form name="form" method="post" action="grupos_trata.php?ID=<? //=$ID?>&task_form=<? //=$task_form?>&dump=<? //=uniqID("")?>" enctype="multipart/form-data"-->
    <form name="form" method="post" action="grupos_trata.php?dump=<?=uniqID("")?>" enctype="multipart/form-data">
    <input type="hidden" name="m_lang" value="<?=$admin_lang;?>">
    <input type="hidden" name="slashes" value="'">
    <input type="hidden" name="ID" value="<?=$ID?>">
    <input type="hidden" name="task_form" value="<?=$task_form?>">
    <table class="new_quadros" width="100%" border="0" cellspacing="0" cellpadding="5">
    <tr>
        <td class="preto" align="left" width="25">Descrição:</td>
        <td align="left" valign="top"><input type="text" name="descricao" style="WIDTH: 100%; HEIGHT: 20px" maxlength="254" value="<?=$descricao;?>"> </td>
    </tr>
    <tr>
        <td class="preto" align="left" width="25">Activo</td>
        <td align="left" class="preto"><br/><input type="checkbox" name="visible" value="1" <? if($visible) echo "checked" ?>>
        (<strong>ATEN&Ccedil;&Atilde;O</strong>!!! S&Oacute; DEVE ACTIVAR ESTA FUN&Ccedil;&Atilde;O SE PRETENDER QUE OS UTILIZADORES
          POSSAM ATRAV&Eacute;S DO SITE REGISTAR-SE AUTOMATICAMENTE NESTE GRUPO) </td>
    </tr>
    <tr>
        <td width="73"></td>
        <td align="left" > <input type="submit" name="Submit" class="btn btn-primary" value=" <?=$task_button;?> "> <input class="btn btn-secondary" type="button" name="cancelar" value=" Cancelar " onMouseUp="javascript:history.go(-1);"> </td>
    </tr>
    </table>
	</form>

	</td>
    <td width="20">&nbsp;</td>
	<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>

<? 	include "../../layout/bottom.inc.php";?>