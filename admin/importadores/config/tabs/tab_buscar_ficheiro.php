<div style="display:inline-block;padding:1px;vertical-align:top;background:#FFF;min-height:118px">
<div style="font-size:15px;font-weight:bold">
<label for="tipo_importacao4"><input type="radio" value="4" name="tipo_importacao" id="tipo_importacao4" <?php echo ($tipo_importacao == 4 ? 'checked="checked"' : '');?>> UPLOAD manual de ficheiro (CSV ou EXCEL)</label>
</div><br>

<div style="font-size:15px;font-weight:bold">
<label for="tipo_importacao0"><input type="radio" value="0" name="tipo_importacao" id="tipo_importacao0" <?php echo ($tipo_importacao == 0 ? 'checked="checked"' : '');?>> FTP - Download através de conta FTP</label>
</div><br>

<h2>Configure os Dados</h2><br>

<div style="width:145px;display:inline-block;padding:5px 0">Servidor</div><div style="width:150px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ftp_servidor;?>" name="ftp_servidor" id="ftp_servidor" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Porta</div><div style="width:150px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ftp_porta;?>" name="ftp_porta" id="ftp_porta" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Utilizador</div><div style="width:150px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ftp_user;?>" name="ftp_user" id="ftp_user" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Password</div><div style="width:150px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ftp_password;?>" name="ftp_password" id="ftp_password" style="width:140px"> </div><br>

<input class="btn btn-danger" value="Abrir e ver ficheiros" type="button" style="width:150px" onclick="window.open('phftp/ftp.php');">

<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Modo Passivo</div><div style="width:150px;display:inline-block;padding:5px 0">
<label for="ftp_passivo"><input type="checkbox" value="1" name="ftp_passivo" id="ftp_passivo" <?php echo ($ftp_passivo == 1 ? 'checked="checked"' : '');?>> SIM</label>
</div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Pasta online</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ftp_caminho;?>" name="ftp_caminho" id="ftp_caminho" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Fx. Completo para Download</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ficheiro_ftp;?>" name="ficheiro_ftp" id="ficheiro_ftp" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">OU</div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Fx. Caracteristicas para Download</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ficheiro_ftp1;?>" name="ficheiro_ftp1" id="ficheiro_ftp1" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Fx. Stock para Download</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ficheiro_ftp2;?>" name="ficheiro_ftp2" id="ficheiro_ftp2" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Fx. Preços para Download</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="form-control" type="text" value="<?php echo $ficheiro_ftp3;?>" name="ficheiro_ftp3" id="ficheiro_ftp3" style="width:240px"> </div><br>
<div style="clear:both"></div>
</div>

<div style="display:inline-block;padding:10px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:500px">
	<div style="font-size:15px;font-weight:bold">
		<label for="tipo_importacao1"><input type="radio" value="1" name="tipo_importacao" id="tipo_importacao1" <?php echo ($tipo_importacao == 1 ? 'checked="checked"' : '');?>> URL - Download através de url externo(ficheiro .csv)</label>
	</div>
	<br>
	<h2>Insira a URL completa com https:// ou http://</h2><br>
	<div style="width:40px;display:inline-block;padding:5px 0">URL</div>
	<div style="width:250px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?php echo $ficheiro_caminho;?>" name="ficheiro_caminho" id="ficheiro_caminho" style="width:240px">
	</div>

	<div style="clear:both;margin-top:25px"></div>
	<div style="font-size:15px;font-weight:bold">
		<label for="tipo_importacao2"><input type="radio" value="2" name="tipo_importacao" id="tipo_importacao2" <?php echo ($tipo_importacao == 2 ? 'checked="checked"' : '');?>> URL - Download através de url externo(ficheiro .json)</label>
	</div>
	<br>
	<h2>Insira a URL completa com https:// ou http://</h2><br>
	<div style="width:40px;display:inline-block;padding:5px 0">URL</div>
	<div style="width:250px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?php echo $ficheiro_caminho_json;?>" name="ficheiro_caminho_json" id="ficheiro_caminho_json" style="width:240px">
	</div>

	<div style="clear:both;margin-top:25px"></div>
	<div style="font-size:15px;font-weight:bold">
		<label for="tipo_importacao3"><input type="radio" value="3" name="tipo_importacao" id="tipo_importacao3" <?php echo ($tipo_importacao == 3 ? 'checked="checked"' : '');?>> URL - WEB API</label>
	</div>
	<br>
	<h2>Insira a URL completa com https:// ou http://</h2><br>
	<div style="width:80px;display:inline-block;padding:5px 0">URL</div>
	<div style="width:250px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?php echo $ficheiro_caminho_api;?>" name="ficheiro_caminho_api" id="ficheiro_caminho_api" style="width:240px">
	</div><br>
	<div style="width:80px;display:inline-block;padding:5px 0">Login</div>
	<div style="width:250px;display:inline-block;padding:5px 0">
		<input class="form-control" type="text" value="<?php echo $ficheiro_caminho_api_login;?>" name="ficheiro_caminho_api_login" id="ficheiro_caminho_api_login" style="width:240px">
	</div><br>
	<div style="width:80px;display:inline-block;padding:5px 0">Password</div>
	<div style="width:250px;display:inline-block;padding:5px 0">
		<input class="form-control" type="password" value="<?php echo $ficheiro_caminho_api_pass;?>" name="ficheiro_caminho_api_pass" id="ficheiro_caminho_api_pass" style="width:240px">
	</div>
</div>


<?php
if($tipo_importacao != 4)
{?>
	<div style="clear:both"></div>
	<div style="border-top:solid 1px #000;width:100%">&nbsp;</div>
	<div style="width:160px;display:inline-block;padding:5px 0;float:left"><input class="btn btn-info" value="Testar Ligação" type="button" style="width:150px" onclick="test_conection();"></div>
	<div style="width:160px;display:inline-block;padding:5px 0"><input class="btn btn-primary" value="Testar Importação" type="button" style="width:150px" onclick="prerun_conection();"></div>
	<div style="width:160px;display:inline-block;padding:5px 0"><input class="btn btn-danger" value="Iniciar Importação" type="button" style="width:150px" onclick="run_conection();"></div><br>
<?}?>