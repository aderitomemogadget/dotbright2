<div style="display:inline-block;padding:5px;vertical-align:top;background:#FFF;min-height:118px">
<div style="font-size:15px;font-weight:bold">
<label for="tipo_importacao0"><input type="radio" value="0" name="tipo_importacao" id="tipo_importacao0" <?php echo ($tipo_importacao == 0 ? 'checked="checked"' : '');?>> FTP - Download atravês de conta FTP</label>
</div><br>

<h2>Configure os Dados</h2><br>

<div style="width:145px;display:inline-block;padding:5px 0">Servidor</div><div style="width:150px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ftp_servidor;?>" name="ftp_servidor" id="ftp_servidor" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Porta</div><div style="width:150px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ftp_porta;?>" name="ftp_porta" id="ftp_porta" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">User</div><div style="width:150px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ftp_user;?>" name="ftp_user" id="ftp_user" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Password</div><div style="width:150px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ftp_password;?>" name="ftp_password" id="ftp_password" style="width:140px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Passivo</div><div style="width:150px;display:inline-block;padding:5px 0">
<label for="ftp_passivo"><input type="checkbox" value="1" name="ftp_passivo" id="ftp_passivo" <?php echo ($ftp_passivo == 1 ? 'checked="checked"' : '');?>> SIM</label>
</div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Caminho</div><div style="width:250px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ftp_caminho;?>" name="ftp_caminho" id="ftp_caminho" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Ficheiro para Download</div><div style="width:250px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ficheiro_ftp;?>" name="ficheiro_ftp" id="ficheiro_ftp" style="width:240px"> </div><br>
<div style="clear:both"></div>
<div style="width:145px;display:inline-block;padding:5px 0">Testar Ligação</div><div style="width:250px;display:inline-block;padding:5px 0"><input class="botao" value="Testar" type="button" style="width:50px" onclick="test_conection();"></div><br>
<div style="clear:both"></div>
</div>

<div style="display:inline-block;padding:5px;vertical-align:top;border-left:#c0c0c0 1px solid;background:#FFF;min-height:118px">


<div style="font-size:15px;font-weight:bold">
<label for="tipo_importacao1"><input type="radio" value="1" name="tipo_importacao" id="tipo_importacao1" <?php echo ($tipo_importacao == 1 ? 'checked="checked"' : '');?>> URL - Download atravês de url externo(ficheiro .csv)</label>
</div><br>

<h2>Url completa com https ou http</h2><br>
<div style="width:145px;display:inline-block;padding:5px 0">URL</div><div style="width:250px;display:inline-block;padding:5px 0"><input type="text" value="<?php echo $ficheiro_caminho;?>" name="ficheiro_caminho" id="ficheiro_caminho" style="width:240px"> </div><br>
<div style="clear:both"></div>
</div>