<?php /* 
<table width="100%" border="0" cellpadding="10" cellspacing="0" bgcolor="#F6F6F6" class="border1">
	<tr>
		<td valign="top" style="line-height:13px">
		<b>ESTADO:</b><br /><br />
			<span>(SWAP): O telefone está selado, não foi aberto.</span><br>
			<span>(A+) Como novo: O telefone encontra-se igual a novo, sem marcas ou riscos.</span><br>
			<span>(A) Muito bom: O telefone pode apresentar marcas ligeiras, pouco visíveis, tais como pequenos riscos, no entanto nunca marcas muito perceptíveis.</span><br>
			<span>(B) Bom: Apresenta marcas visíveis, tais como riscos, amolgadelas e outras marcas.</span><br>
			<span>(C) Satisfatório: O telefone apresenta marcas muito visíveis, tais como riscos muito acentuados e amolgadelas profundas.</span><br>
		<br><br>
		</td>
	</tr>
</table>
*/ ?>

<table width="100%" border=0 cellpadding="10" cellspacing=0 bgcolor="#F6F6F6" class="border1">
	<tr>
		<td valign="top">
			<table width="100%" border=0 cellpadding="2" cellspacing=0>
				<tr>
					<td width="150" class="preto">Estado</td>
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="SWAP") echo "checked";?> value="SWAP"> SWAP
					</td>
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="A+") echo "checked";?> value="A+"> A+
					</td>
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="A") echo "checked";?> value="A"> A
					</td>
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="B") echo "checked";?> value="B"> B  
					</td>
					<?php /*
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="C+") echo "checked";?> value="C+"> C+
					</td>
					*/?>
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="C") echo "checked";?> value="C"> C
					</td>
					<?php /*
					<td class="preto" width="150">
						<input type="radio" name="estado" <?php if (isset($estado) && $estado=="N") echo "checked";?> value="N"> N
					</td>
					*/?>
				</tr>
				<tr>
					<td width="150" class="preto">Bateria:</td>
					<td class="preto"><input type="text" name="bateria" id="bateria" style="WIDTH:150px;text-align:center;" value="<? echo stripslashes($bateria);?>"></td>
				</tr>
				<tr>
					<td width="150" class="preto">Localização Física:</td>
					<td class="preto"><input type="text" name="localizacao_fisica" id="localizacao_fisica" style="WIDTH:150px;text-align:center;" value="<? echo stripslashes($localizacao_fisica);?>"></td>
				</tr>
			</table>
		</td>
	</tr>
</table>