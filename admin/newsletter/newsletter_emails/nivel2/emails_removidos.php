<?
	include "settings.inc.php";
	include "../../../layout/header.inc.php";
	include "../../../layout/body.inc.php";

	if($task=="apagar"){$task_button = "Apagar este Registo"; $task_form = "apaga"; $btn_class='btn-danger';}
	elseif($ID==0){$task_button = "Gravar o Novo Registo"; $task_form = "insere"; $btn_class='btn-primary';}
	else {$task_button = "Gravar alterações"; $task_form = "altera"; $btn_class='btn-primary';}

	if($ID!=0)
	{
		$data = BuscarRegistos2($ID);
		if($data)
		{
            $nome=$data->db_data["Nome"];
			$apelido=$data->db_data["Apelido"];
			$empresa=$data->db_data["Empresa"];
			$email=$data->db_data["Email"];
			$contacto=$data->db_data["Empresa"];
            $morada=$data->db_data["Morada"];
            $codpos=$data->db_data["CodPos"];
			$localidade=$data->db_data["Localidade"];
            $tel=$data->db_data["Telefone"];
            $fax=$data->db_data["Fax"];
			$D=str2date($data->db_data["DataNasc"]);
            $obs=$data->db_data["Obs"];
		}
		$data->db_close(1);
	}
?>
<div class="center">
<table class="t3" align="center" border=0 cellpadding=0 cellspacing=0>
            <tr>
                <td valign="top"><? include "../../../layout/menus.inc.php"; ?>
<?
	echo "<form name=\"form\" method=\"post\" action=\"emails_removidos_trata.php?ID=$ID&ID_ORG=$ID_ORG&task_form=".$task_form."&dump=".uniqID("")."\" enctype=\"multipart/form-data\">\n";
?>
<table class="new_quadros" width="880px" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="10" height="10"></td>
    <td width="110" align="left" valign="top"></td>
    <td align="left" valign="top"></td>
    <td width="4"></td>
  </tr>

  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Nome:</td>
    <td align="left" valign="top" class="preto">
      <input name="nome" style="WIDTH: 100%; HEIGHT: 20px" maxlength="50" value="<?=$nome;?>"></td></td>
    <td width="4"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Apelido:</td>
    <td align="left" valign="top" class="preto">
      <input name="apelido" style="WIDTH: 100%; HEIGHT: 20px" maxlength="50" value="<?=$apelido;?>"></td></td>
    <td width="4"></td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Empresa:</td>
    <td class="preto" align="left" valign="top"> <input name="empresa" style="WIDTH: 100%; HEIGHT: 20px" value="<?=$empresa;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Email:</td>
    <td class="preto" align="left" valign="top"> <input name="email" style="WIDTH: 100%; HEIGHT: 20px" value="<?=$email;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Contacto:</td>
    <td class="preto" align="left" valign="top"> <input name="contacto" style="WIDTH: 100%; HEIGHT: 20px" value="<?=$contacto;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Morada:</td>
    <td class="preto" align="left" valign="top"> <input name="morada" style="WIDTH: 100%; HEIGHT: 20px" value="<?=$morada;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Código Postal:</td>
    <td class="preto" align="left" valign="top"><input name="codpos" style="WIDTH: 50px; HEIGHT: 20px" value="<?=$codpos;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Localidade:</td>
    <td class="preto" align="left" valign="top"> <input name="localidade" style="WIDTH: 100%; HEIGHT: 20px" value="<?=$localidade;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Tel:</td>
    <td class="preto" align="left" valign="top"> <input name="tel" style="WIDTH: 310px; HEIGHT: 20px" value="<?=$tel;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td>&nbsp;</td>
    <td class="preto" align="left" valign="top">Fax:</td>
    <td class="preto" align="left" valign="top"> <input name="fax" style="WIDTH: 310px; HEIGHT: 20px" value="<?=$fax;?>">
    </td>
    <td>&nbsp;</td>
  </tr>
  <tr>
    <td width="10">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="120">Data de Nascimento:</td>
    <td class="preto" align="left" valign="top">
    <?
	echo "<select name=\"dia\">";
	for($i=1;$i<32;$i++)
	{
		echo "                      <option value=\"";
		if ($i<10) echo "0";
		echo "$i\"";
		if ($D["mday"]==$i)	echo " selected";
		echo">";
		if ($i<10) echo "0";echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo "                    . \n";
	echo "                    <select name=\"mes\">\n";
	for($i=1;$i<13;$i++)
	{
		echo "                      <option value=\"";
		if ($i<10) echo "0";
		echo "$i\"";
		if ($D["mon"]==$i)
			echo " selected";
		echo">";
		if ($i<10) echo "0";
		echo "$i</option>\n";
	}
	echo "                    </select>\n";
	echo "                    . \n";
	echo "                    <select name=\"ano\">";
	for($i=1800;$i<=date('Y');$i++)
	{
		echo "                      <option value=\"";
		if ($i<10) echo "0";
		echo "$i\"";
		if ($D["year"]==$i)
			echo " selected";
		echo ">$i</option>\n";
	}
    echo "</select>\n";
?>
    </td>
    <td width="4">&nbsp;</td>
  </tr>
  <tr>
    <td width="10">&nbsp;</td>
    <td class="preto" align="left" valign="top" width="73">Observações:</td>
    <td class="preto" align="left" valign="top" width="1165"> <textarea name="obs" rows="5" id="obs" style="WIDTH: 100%; HEIGHT: 100px;"><?=$obs;?></textarea></td>
    <td width="4">&nbsp;</td>
  </tr>
  <tr>
    <td height="10" width="10"></td>
    <td width="73"></td>
    <td></td>
    <td width="4"></td>
  </tr>
  <tr>
    <td width="10"></td>
    <td width="73"></td>
    <td align="left" valign="top" >
      <input class="btn <?=$btn_class?>" type="submit" name="Submit" value="<?=$task_button;?>">
      <input class="btn btn-secondary" type="button" name="cancelar" value="Cancelar" onMouseUp="javascript:history.go(-1);">
    </td>
    <td width="4"></td>
  </tr>
  <tr>
    <td height="10" width="10"></td>
    <td width="73"></td>
    <td></td>
    <td width="4"></td>
  </tr>
</table>
</form>

</td>
<td valign="top" width="200"><? show_fast_tools();?></td>
</tr>
</table>
</div>

<? 	include "../../../layout/bottom.inc.php";?>
