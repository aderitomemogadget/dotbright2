<div id="info_renovacoes">
	<table width="100%" cellpadding="0" cellspacing="0" border="0" id="renovacoes" style="display:none">
	<tr><td colspan="2">&nbsp;<input type="hidden" value="" class="RENOVACAO_ID" /></td></tr>
	<tr>
		<td width="130" align="left">Imagem:</td>
		<td align="left">
			<table cellpadding="0" cellspacing="0" border="0">
			<tr>
				<td align="left"><input type="file" class="BOLETIM_EXT" value="" /></td>
				<td align="left">Zoom</td>
				<td align="left"><input type="checkbox" class="BOLETIM_EXT" value="1" /></td>
			</tr>
			</table>
		</td>
	</tr>
	<tr><td style="border-bottom:#c0c0c0 1px solid" colspan="2">&nbsp;</td></tr>
	<tr><td colspan="2">&nbsp;</td></tr>
	</table>

<table width="100%" cellpadding="10" cellspacing="10" border="0">
<tr>
	<td valign="top" bgcolor="#e7e7e7">
		<?
		$mostrar=0;
		if($categorias[0]==2)$mostrar=1;
		if($categorias[0]==5)$mostrar=1;
		if($categorias[0]==8)$mostrar=1;
		if($categorias[0]==11)$mostrar=1;
		if($categorias[0]==19)$mostrar=1;
		$mostrar2=0;
		if($categorias[0]==5)$mostrar2=1;
		if($categorias[0]==11)$mostrar2=1;
		if($mostrar==1 && $opcoes[$categorias[0]]['gal_qtd'])
		{
		?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td width="230" align="left">Número máximo de colunas por linha:</td>
			<td align="left">
				<select name="Qtd" id="Qtd" class="form-control" style="width:80px">
					<option value="1" <?php if($Qtd==1) echo 'selected'?>>1</option>
					<option value="2" <?php if($Qtd==2) echo 'selected'?>>2</option>
					<option value="3" <?php if(!$Qtd || $Qtd==3) echo 'selected'?>>3</option>
					<option value="4" <?php if($Qtd==4) echo 'selected'?>>4</option>
					<option value="5" <?php if($Qtd==5) echo 'selected'?>>5</option>
					<option value="6" <?php if($Qtd==6) echo 'selected'?>>6</option>
					<option value="7" <?php if($Qtd==7) echo 'selected'?>>7</option>
					<option value="8" <?php if($Qtd==8) echo 'selected'?>>8</option>
					<option value="9" <?php if($Qtd==9) echo 'selected'?>>9</option>
					<option value="10" <?php if($Qtd==10) echo 'selected'?>>10</option>
					<option value="11" <?php if($Qtd==11) echo 'selected'?>>11</option>
					<option value="12" <?php if($Qtd==12) echo 'selected'?>>12</option>
				</select>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td width="230" align="left">Mostrar setas de navegação:</td>
			<td align="left">
				<select name="Tem_setas" id="Tem_setas" class="form-control" style="width:100px">
					<option value="" <?php if(!$Tem_setas) echo 'selected'?>>NÃO</option>
					<option value="1" <?php if($Tem_setas) echo 'selected'?>>SIM</option>
				</select>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td width="230" align="left">Mostrar paginação:</td>
			<td align="left">
				<select name="Tem_paginacao" id="Tem_paginacao" class="form-control" style="width:100px">
					<option value="" <?php if(!$Tem_paginacao) echo 'selected'?>>NÃO</option>
					<option value="1" <?php if($Tem_paginacao) echo 'selected'?>>SIM</option>
				</select>
			</td>
		</tr>
		</table>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td width="230" align="left">Mostrar sombra:</td>
			<td align="left">
				<select name="Tem_sombra" id="Tem_sombra" class="form-control" style="width:150px">
					<option value="" <?php if(!$Tem_sombra) echo 'selected="selected"'?>>NÃO</option>
					<option value="1" <?php if($Tem_sombra==1) echo 'selected="selected"'?>>Sombra tipo 1</option>
					<option value="2" <?php if($Tem_sombra==2) echo 'selected="selected"'?>>Sombra tipo 2</option>
					<option value="3" <?php if($Tem_sombra==3) echo 'selected="selected"'?>>Sombra tipo 3</option>
				</select>
			</td>
		</tr>
		</table><?
		}
		if($mostrar2==1 && $opcoes[$categorias[0]]['legen_local'])
		{
		?>
		<table width="100%" cellpadding="1" cellspacing="0" border="0">
		<tr>
			<td width="230" align="left">Localização da legenda:</td>
			<td align="left">
				<select name="Posicao" class="form-control" style="width:250px">
					<option value="" <?php if(!$Posicao) echo 'selected="selected"'?>>Abaixo da imagem</option>
					<option value="1" <?php if($Posicao==1) echo 'selected="selected"'?>>Por cima da imagem</option>
					<option value="2" <?php if($Posicao==2) echo 'selected="selected"'?>>À esquerda da imagem</option>
					<option value="3" <?php if($Posicao==3) echo 'selected="selected"'?>>À Direita da imagem</option>
					<option value="4" <?php if($Posicao==4) echo 'selected="selected"'?>>Texto à Esquerda e Abaixo da Imagem</option>
					<option value="5" <?php if($Posicao==5) echo 'selected="selected"'?>>Texto à Direita e Abaixo da Imagem</option>
				</select>
			</td>
		</tr>
		</table>
		<?
		}
		$contador=0;
		global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
		$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
		$rs=new DataObj;

		$sql='SELECT * FROM '.TBIMAGENS.' WHERE bloco_img_ID_ORG="'.$ID.'" ORDER BY bloco_img_ordem ASC';
		$rs->db_open($db_link,$DB_name,$sql);
		if(!$rs->EOF() || $rs->EOF())
		{
			while(!$rs->EOF())
			{
				$bloco_img_id='';
				$bloco_img_ext='';
				$bloco_img2_ext='';
				$bloco_pdf_ext='';
				$bloco_img_id=$rs->db_data['bloco_img_id'];
				$bloco_img_ext=$rs->db_data['bloco_img_ext'];
				$bloco_img2_ext=$rs->db_data['bloco_img2_ext'];
				$bloco_pdf_ext=$rs->db_data['bloco_pdf_ext'];
				$bloco_img_ordem = $rs->db_data['bloco_img_ordem'];
				$bloco_img_video = $rs->db_data['bloco_img_video'];
				$bloco_img_url = $rs->db_data['bloco_img_url'];
				$bloco_img_animacao= $rs->db_data['bloco_img_animacao'];
				$bloco_img_limite= $rs->db_data['bloco_img_limite'];
				$bloco_img_titulo_visivel=$rs->db_data['bloco_img_titulo_visivel'];
				$bloco_img_url_default=$rs->db_data['bloco_img_url_default'];
				$bloco_img_titulo_cor=$rs->db_data['bloco_img_titulo_cor'];
				$bloco_img_titulo_tamanho=$rs->db_data['bloco_img_titulo_tamanho'];
				$bloco_img_titulo_font_weight=$rs->db_data['bloco_img_titulo_font_weight'];
				$bloco_img_txt_cor=$rs->db_data['bloco_img_txt_cor'];
				$bloco_img_txt_tamanho=$rs->db_data['bloco_img_txt_tamanho'];
				$bloco_img_txt_font_weight=$rs->db_data['bloco_img_txt_font_weight'];
				$bloco_img_txt_line_height=$rs->db_data['bloco_img_txt_line_height'];
 				$bloco_img_layout=$rs->db_data['bloco_img_layout'];
 				$bloco_img_lightbox=$rs->db_data['bloco_img_lightbox'];
 				$bloco_layout=$rs->db_data['bloco_layout'];
 				$bloco_img_data=$rs->db_data['bloco_img_data'];
 				$bloco_img_data_visivel=$rs->db_data['bloco_img_data_visivel'];

 				$a_data_x=$rs->db_data['a_data_x'];
				$a_data_y=$rs->db_data['a_data_y'];
				$a_data_voffset=$rs->db_data['a_data_voffset'];
				$a_data_hoffset=$rs->db_data['a_data_hoffset'];
				$a_data_speed=$rs->db_data['a_data_speed'];
				$a_data_start=$rs->db_data['a_data_start'];
				$a_data_easing=$rs->db_data['a_data_easing'];
				$a_data_endspeed=$rs->db_data['a_data_endspeed'];
				$a_data_text_align=$rs->db_data['a_data_text_align'];

				$b_data_x=$rs->db_data['b_data_x'];
				$b_data_y=$rs->db_data['b_data_y'];
				$b_data_voffset=$rs->db_data['b_data_voffset'];
				$b_data_hoffset=$rs->db_data['b_data_hoffset'];
				$b_data_speed=$rs->db_data['b_data_speed'];
				$b_data_start=$rs->db_data['b_data_start'];
				$b_data_easing=$rs->db_data['b_data_easing'];
				$b_data_endspeed=$rs->db_data['b_data_endspeed'];
				$b_data_text_align=$rs->db_data['b_data_text_align'];

				$c_data_x=$rs->db_data['c_data_x'];
				$c_data_y=$rs->db_data['c_data_y'];
				$c_data_voffset=$rs->db_data['c_data_voffset'];
				$c_data_hoffset=$rs->db_data['c_data_hoffset'];
				$c_data_speed=$rs->db_data['c_data_speed'];
				$c_data_start=$rs->db_data['c_data_start'];
				$c_data_easing=$rs->db_data['c_data_easing'];
				$c_data_endspeed=$rs->db_data['c_data_endspeed'];
				$c_data_text_align=$rs->db_data['c_data_text_align'];

				$d_data_x=$rs->db_data['d_data_x'];
				$d_data_y=$rs->db_data['d_data_y'];
				$d_data_voffset=$rs->db_data['d_data_voffset'];
				$d_data_hoffset=$rs->db_data['d_data_hoffset'];
				$d_data_speed=$rs->db_data['d_data_speed'];
				$d_data_start=$rs->db_data['d_data_start'];
				$d_data_easing=$rs->db_data['d_data_easing'];
				$d_data_endspeed=$rs->db_data['d_data_endspeed'];
				$d_data_text_align=$rs->db_data['d_data_text_align'];


				$bloco_img_titulo = '';
				$bloco_img_txt = '';
				$sql='SELECT bloco_img_titulo,bloco_img_txt,bloco_img2_txt,bloco_video_code,bloco_video_yt,bloco_video_vimeo FROM '.TBIMAGENS_LANG.' WHERE bloco_img_ID_PAI="'.$bloco_img_id.'" AND lang="'.$GLOBALS['admin_lang'].'"';

				$bloco_video_code = '';
				$bloco_video_yt = '';
				$bloco_video_vimeo = '';
				$rs2=new DataObj;
				$rs2->db_open($db_link,$DB_name,$sql);
				if(!$rs2->EOF())
				{
					$bloco_img_titulo = ($rs2->db_data['bloco_img_titulo']);
					$bloco_img_txt=limpa_editor_lixo((stripslashes($rs2->db_data['bloco_img_txt'])));
					$bloco_img2_txt=limpa_editor_lixo((stripslashes($rs2->db_data['bloco_img2_txt'])));
					$bloco_video_code = stripslashes($rs2->db_data['bloco_video_code']);
					$bloco_video_yt = stripslashes($rs2->db_data['bloco_video_yt']);
					$bloco_video_vimeo = stripslashes($rs2->db_data['bloco_video_vimeo']);
				}
				else
					echo "ERRO";
				$rs2->db_close();

				?><input type="hidden" value="<? echo $ID?>;<? echo $bloco_img_id?>" name="MODULO_IMG_ID[]" class="box RENOVACAO_ID" />
				<br><table width="100%" cellpadding="0" cellspacing="0" border="0" style="background:#FFF">
				<tr>
					<td align="left">
						<table width="100%" cellpadding="5" cellspacing="0" border="0" style="border: 1px solid #c0c0c0">
						<tr>
							<td colspan="2" align="right" style="border-bottom:#c0c0c0 1px solid">
								<?php echo 'Ordem: <input class="form-control" type="text" style="width:50px;display:inline-block" name="bloco_img_ordem'.$bloco_img_id.'" value="'.$bloco_img_ordem.'" />';?>
								<?php echo '<label style="display:inline-block" class="label_check2" for="Apagar_elemento'.$bloco_img_id.'">Apagar Elemento: <input type="checkbox" name="Apagar_elemento'.$bloco_img_id.'" id="Apagar_elemento'.$bloco_img_id.'" value="1" /></label>';?>
							</td>
						</tr>
						<tr>
							<td align="left" width="200" bgcolor="#c0c0c0" style="vertical-align:top"><?
							$img = 0;
							if($bloco_img_ext && file_exists('../../files/documentos/'.$bloco_img_ext))
							{
								$ext = pathinfo('../../files/documentos/'.$bloco_img_ext, PATHINFO_EXTENSION);
								if(strtolower($ext) == 'jpg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif' || strtolower($ext) == 'jpeg')
								{
									$ext_file = 0;
								}
								else
								{
									$ext_file = 1;
								}
								?>
								<table border="0">
								<tr>
									<td><?php  echo '<label style="display:inline-block" class="label_check" for="Apagar_img'.$bloco_img_id.'">'.($ext_file == 1 ? 'Apagar Ficheiro' : 'Apagar Imagem').' <input type="checkbox" name="Apagar_img'.$bloco_img_id.'" id="Apagar_img'.$bloco_img_id.'" value="1" /></label><br>';?></td>
								</tr>
								<tr>
									<td><?
								$img=1;
								echo '<a alt="Clique para ver imagem em grande" class=" fancybox_img" href="../../files/documentos/'.$bloco_img_ext.'" target="_blank" class="url_reno">';
								if($ext_file == 1) echo '<img width=72 src="../../layout/pdf.png">';
								else echo '<img width=200 src="../../files/documentos/'.$bloco_img_ext.'">';
								echo '</a><br />';?>
								</td>
								</tr>
								</table><?
							}?>
							<input type="file" name="bloco_img_ext<?php echo $bloco_img_id?>" class="BOLETIM_EXT" /> <br><?php
							if($categorias[0]==2) //Banner
							{?>
								Mobile:
								<?php

								//Imagem para mobile
								$img = 0;
								if($bloco_img2_ext && file_exists('../../files/documentos/'.$bloco_img2_ext))
								{
									$ext = pathinfo('../../files/documentos/'.$bloco_img2_ext, PATHINFO_EXTENSION);
									if(strtolower($ext) == 'jpg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif' || strtolower($ext) == 'jpeg')
									{
										$ext2_file = 0;
									}
									else
									{
										$ext2_file = 1;
									}
									?>
									<table border="0">
									<tr>
										<td><?php  echo '<label style="display:inline-block" class="label_check" for="Apagar2_img'.$bloco_img_id.'">'.($ext2_file == 1 ? 'Apagar Ficheiro' : 'Apagar Imagem').' <input type="checkbox" name="Apagar2_img'.$bloco_img_id.'" id="Apagar2_img'.$bloco_img_id.'" value="1" /></label><br>';?></td>
									</tr>
									<tr>
										<td><?
									$img=1;
									echo '<a alt="Clique para ver imagem em grande" class="fancybox_img" href="../../files/documentos/'.$bloco_img2_ext.'" target="_blank" class="url_reno">';
									echo '<img width=200 src="../../files/documentos/'.$bloco_img2_ext.'">';
									echo '</a><br />';?>
									</td>
									</tr>
									</table><?
								}?>
								<input type="file" name="bloco_img2_ext<?php echo $bloco_img_id?>" class="BOLETIM_EXT" />

								<br><br><span>Ebook (PDF)</span>
								<?php
								if($bloco_pdf_ext && file_exists('../../files/documentos/'.$bloco_pdf_ext))
								{
									$ext = pathinfo('../../files/documentos/'.$bloco_pdf_ext, PATHINFO_EXTENSION);
									if(strtolower($ext) == 'jpg' || strtolower($ext) == 'png' || strtolower($ext) == 'gif' || strtolower($ext) == 'jpeg')
									{

										$ext_file = 0;
									}
									else
									{
										$ext_file = 1;
									}
									?>
									<table border="0">
									<tr>
										<td><?php  echo '<label style="display:inline-block" class="label_check" for="Apagar_img_capa'.$bloco_img_id.'">'.($ext_file == 1 ? 'Apagar Imagem' : 'Apagar Imagem').' <input type="checkbox" name="Apagar_img_capa'.$bloco_img_id.'" id="Apagar_img_capa'.$bloco_img_id.'" value="1" /></label><br>';?></td>
									</tr>
									<tr>
										<td><?
									$img=1;
									echo '<a alt="Clique para ver imagem em grande" class=" fancybox_img" href="../../files/documentos/'.$bloco_pdf_ext.'" target="_blank" class="url_reno">';
									if($ext_file == 1) echo '<img width=72 src="../../files/pdf.png">';
									else echo '<img width=200 src="../../files/documentos/'.$bloco_pdf_ext.'">';	//echo '<a class="preto" href="../edit_image/edit_image.php?w=700&h=360&src=../../files/documentos/'.$bloco_img_ext.'" target="_blank">Editar imagem</a><br />';
									echo '</a><br />';?>
									</td>
									</tr>
									</table><?
								}
							}
							?>
							<input type="file" name="bloco_pdf_ext<?php echo $bloco_img_id?>" class="BOLETIM_EXT" /> <br>
							<br>  OU CÓDIGO NOME VIDEO<br> <i>(files/videos/)</i>
							<?php echo '<input class="form-control" type="text" style="width:98%;" name="bloco_video_code'.$bloco_img_id.'" id="bloco_video_code'.$bloco_img_id.'" value="'.$bloco_video_code.'" maxlength="255" />';?><br> OU CÓDIGO VIDEO YOUTUBE
							<?php echo '<input class="form-control" type="text" style="width:98%;" name="bloco_video_yt'.$bloco_img_id.'" id="bloco_video_yt'.$bloco_img_id.'" value="'.$bloco_video_yt.'" maxlength="255" />';?> <br> OU CÓDIGO VIDEO VIMEO
							<?php echo '<input class="form-control" type="text" style="width:98%;" name="bloco_video_vimeo'.$bloco_img_id.'" id="bloco_video_vimeo'.$bloco_img_id.'" value="'.$bloco_video_vimeo.'" maxlength="255" />';?>
							</td>
							<td align="left" valign="top" style="background:#FFF">
								<table border=0 width="100%" cellspacing="0" cellpadding="0">
								<?php
								if($opcoes[$categorias[0]]['gal_tipoanim'])
								{?>
								<tr>
									<td width="80">Animação imagem:</td>
									<td><?php
									if($categorias[0]==2) //Banner
									{?>

										<select name="bloco_img_animacao<?php echo $bloco_img_id?>" class="form-control" style="width:400px">
											<option value="" <?php if(!$bloco_img_animacao) echo 'selected'?>>---</option>
											<option value="slideup" <?php if($bloco_img_animacao=='slideup') echo 'selected';?>>slideup</option>
											<option value="slidedown" <?php if($bloco_img_animacao=='slidedown') echo 'selected';?>>slidedown</option>
											<option value="slideright" <?php if($bloco_img_animacao=='slideright') echo 'selected';?>>slideright</option>
											<option value="slideleft" <?php if($bloco_img_animacao=='slideleft') echo 'selected';?>>slideleft</option>
											<option value="slidehorizontal" <?php if($bloco_img_animacao=='slidehorizontal') echo 'selected';?>>slidehorizontal</option>
											<option value="slidevertical" <?php if($bloco_img_animacao=='slidevertical') echo 'selected';?>>slidevertical</option>
											<option value="boxslide" <?php if($bloco_img_animacao=='boxslide') echo 'selected';?>>boxslide</option>
											<option value="slotslide-horizontal" <?php if($bloco_img_animacao=='slotslide-horizontal') echo 'selected';?>>slotslide-horizontal</option>
											<option value="slotslide-vertical" <?php if($bloco_img_animacao=='slotslide-vertical') echo 'selected';?>>slotslide-vertical</option>
											<option value="boxfade" <?php if($bloco_img_animacao=='boxfade') echo 'selected';?>>boxfade</option>
											<option value="slotfade-horizontal" <?php if($bloco_img_animacao=='slotfade-horizontal') echo 'selected';?>>slotfade-horizontal</option>
											<option value="slotfade-vertical" <?php if($bloco_img_animacao=='slotfade-vertical') echo 'selected';?>>slotfade-vertical</option>
											<option value="fadefromright" <?php if($bloco_img_animacao=='fadefromright') echo 'selected';?>>fadefromright</option>
											<option value="fadefromleft" <?php if($bloco_img_animacao=='fadefromleft') echo 'selected';?>>fadefromleft</option>
											<option value="fadefromtop" <?php if($bloco_img_animacao=='fadefromtop') echo 'selected';?>>fadefromtop</option>
											<option value="fadefrombottom" <?php if($bloco_img_animacao=='fadefrombottom') echo 'selected';?>>fadefrombottom</option>
											<option value="fadetoleftfadefromright" <?php if($bloco_img_animacao=='fadetoleftfadefromright') echo 'selected';?>>fadetoleftfadefromright</option>
											<option value="fadetorightfadefromleft" <?php if($bloco_img_animacao=='fadetorightfadefromleft') echo 'selected';?>>fadetorightfadefromleft</option>
											<option value="fadetotopfadefrombottom" <?php if($bloco_img_animacao=='fadetotopfadefrombottom') echo 'selected';?>>fadetotopfadefrombottom</option>
											<option value="fadetobottomfadefromtop" <?php if($bloco_img_animacao=='fadetobottomfadefromtop') echo 'selected';?>>fadetobottomfadefromtop</option>
											<option value="parallaxtoright" <?php if($bloco_img_animacao=='parallaxtoright') echo 'selected';?>>parallaxtoright</option>
											<option value="parallaxtoleft" <?php if($bloco_img_animacao=='parallaxtoleft') echo 'selected';?>>parallaxtoleft</option>
											<option value="parallaxtotop" <?php if($bloco_img_animacao=='parallaxtotop') echo 'selected';?>>parallaxtotop</option>
											<option value="parallaxtobottom" <?php if($bloco_img_animacao=='parallaxtobottom') echo 'selected';?>>parallaxtobottom</option>
											<option value="scaledownfromright" <?php if($bloco_img_animacao=='scaledownfromright') echo 'selected';?>>scaledownfromright</option>
											<option value="scaledownfromleft" <?php if($bloco_img_animacao=='scaledownfromleft') echo 'selected';?>>scaledownfromleft</option>
											<option value="scaledownfromtop" <?php if($bloco_img_animacao=='scaledownfromtop') echo 'selected';?>>scaledownfromtop</option>
											<option value="scaledownfrombottom" <?php if($bloco_img_animacao=='scaledownfrombottom') echo 'selected';?>>scaledownfrombottom</option>
											<option value="zoomout" <?php if($bloco_img_animacao=='zoomout') echo 'selected';?>>zoomout</option>
											<option value="zoomin" <?php if($bloco_img_animacao=='zoomin') echo 'selected';?>>zoomin</option>
											<option value="slotzoom-horizontal" <?php if($bloco_img_animacao=='slotzoom-horizontal') echo 'selected';?>>slotzoom-horizontal</option>
											<option value="slotzoom-vertical" <?php if($bloco_img_animacao=='slotzoom-vertical') echo 'selected';?>>slotzoom-vertical</option>
											<option value="fade" <?php if($bloco_img_animacao=='fade') echo 'selected';?>>fade</option>
											<option value="random-static" <?php if($bloco_img_animacao=='random-static') echo 'selected';?>>random-static</option>
											<option value="random" <?php if($bloco_img_animacao=='random') echo 'selected';?>>random</option>


										</select><?
									}
									else
									{
									?>

										<select name="bloco_img_animacao<?php echo $bloco_img_id?>" class="form-control" style="width:400px">
											<option value="" <?php if(!$bloco_img_animacao) echo 'selected'?>>---</option>
											<option value="bounce" <?php if($bloco_img_animacao=='bounce') echo 'selected'?>>Bounce</option>
											<option value="flash" <?php if($bloco_img_animacao=='flash') echo 'selected'?>>Flash</option>
											<option value="pulse" <?php if($bloco_img_animacao=='pulse') echo 'selected'?>>pulse</option>
											<option value="rubberBand" <?php if($bloco_img_animacao=='rubberBand') echo 'selected'?>>rubberBand</option>
											<option value="shake" <?php if($bloco_img_animacao=='shake') echo 'selected'?>>shake</option>
											<option value="swing" <?php if($bloco_img_animacao=='swing') echo 'selected'?>>swing</option>
											<option value="tada" <?php if($bloco_img_animacao=='tada') echo 'selected'?>>tada</option>
											<option value="wobble" <?php if($bloco_img_animacao=='wobble') echo 'selected'?>>wobble</option>
											<option value="bounceIn" <?php if($bloco_img_animacao=='bounceIn') echo 'selected'?>>bounceIn</option>
											<option value="bounceInDown" <?php if($bloco_img_animacao=='bounceInDown') echo 'selected'?>>bounceInDown</option>
											<option value="bounceInLeft" <?php if($bloco_img_animacao=='bounceInLeft') echo 'selected'?>>bounceInLeft</option>
											<option value="bounceInRight" <?php if($bloco_img_animacao=='bounceInRight') echo 'selected'?>>bounceInRight</option>
											<option value="bounceInUp" <?php if($bloco_img_animacao=='bounceInUp') echo 'selected'?>>bounceInUp</option>
											<option value="fadeIn" <?php if($bloco_img_animacao=='fadeIn') echo 'selected'?>>fadeIn</option>
											<option value="fadeInDown" <?php if($bloco_img_animacao=='fadeInDown') echo 'selected'?>>fadeInDown</option>
											<option value="fadeInDownBig" <?php if($bloco_img_animacao=='fadeInDownBig') echo 'selected'?>>fadeInDownBig</option>
											<option value="fadeInLeft" <?php if($bloco_img_animacao=='fadeInLeft') echo 'selected'?>>fadeInLeft</option>
											<option value="fadeInLeftBig" <?php if($bloco_img_animacao=='fadeInLeftBig') echo 'selected'?>>fadeInLeftBig</option>
											<option value="fadeInRight" <?php if($bloco_img_animacao=='fadeInRight') echo 'selected'?>>fadeInRight</option>
											<option value="fadeInRightBig" <?php if($bloco_img_animacao=='fadeInRightBig') echo 'selected'?>>fadeInRightBig</option>
											<option value="fadeInUp" <?php if($bloco_img_animacao=='fadeInUp') echo 'selected'?>>fadeInUp</option>
											<option value="fadeInUpBig" <?php if($bloco_img_animacao=='fadeInUpBig') echo 'selected'?>>fadeInUpBig</option>
											<option value="flip" <?php if($bloco_img_animacao=='flip') echo 'selected'?>>flip</option>
											<option value="flipInX" <?php if($bloco_img_animacao=='flipInX') echo 'selected'?>>flipInX</option>
											<option value="flipInY" <?php if($bloco_img_animacao=='flipInY') echo 'selected'?>>flipInY</option>
											<option value="lightSpeedIn" <?php if($bloco_img_animacao=='lightSpeedIn') echo 'selected'?>>lightSpeedIn</option>
											<option value="rotateIn" <?php if($bloco_img_animacao=='rotateIn') echo 'selected'?>>rotateIn</option>
											<option value="rotateInDownLeft" <?php if($bloco_img_animacao=='rotateInDownLeft') echo 'selected'?>>rotateInDownLeft</option>
											<option value="rotateInDownRight" <?php if($bloco_img_animacao=='rotateInDownRight') echo 'selected'?>>rotateInDownRight</option>
											<option value="rotateInUpLeft" <?php if($bloco_img_animacao=='rotateInUpLeft') echo 'selected'?>>rotateInUpLeft</option>
											<option value="rotateInUpRight" <?php if($bloco_img_animacao=='rotateInUpRight') echo 'selected'?>>rotateInUpRight</option>
											<option value="slideInDown" <?php if($bloco_img_animacao=='slideInDown') echo 'selected'?>>slideInDown</option>
											<option value="slideInLeft" <?php if($bloco_img_animacao=='slideInLeft') echo 'selected'?>>slideInLeft</option>
											<option value="slideInRight" <?php if($bloco_img_animacao=='slideInRight') echo 'selected'?>>slideInRight</option>
											<option value="rollIn" <?php if($bloco_img_animacao=='rollIn') echo 'selected'?>>rollIn</option>
										</select><?php
										}?>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<?php }
								if($opcoes[$categorias[0]]['Layout']){?>
								<tr>
									<td width="80">Layout:</td>
									<td>
										<select name="bloco_img_layout<?php echo $bloco_img_id?>" class="form-control">
											<option value="" <?php if(!$bloco_img_layout) echo 'selected'?>>50% 50%</option>
											<option value="1" <?php if($bloco_img_layout=='1') echo 'selected'?>>25% 75%</option>
											<option value="2" <?php if($bloco_img_layout=='2') echo 'selected'?>>75% 25%</option>
											<option value="3" <?php if($bloco_img_layout=='3') echo 'selected'?>>100%</option>
										</select>
									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr><?php }?>
								<?php
									if($categorias[0]!=2) //diferente de Banner
									{?>
								<tr>
									<td width="80">Data</td>
									<td><?php echo '<input class="box dropdate" type="text" style="display:inline-block;width:100px" name="bloco_img_data'.$bloco_img_id.'" value="'.($bloco_img_data == '0000-00-00' ? date('Y-m-d') : $bloco_img_data).'" />';?>&nbsp;<label style="display:inline-block" class="label_check" for="bloco_img_data_visivel<?php echo $bloco_img_id?>"><input type="checkbox" name="bloco_img_data_visivel<?php echo $bloco_img_id?>" id="bloco_img_data_visivel<?php echo $bloco_img_id?>" value="1" <?php if($bloco_img_data_visivel==1)echo 'checked="checked"'; ?> /> Ocultar</label></td>
								</tr><tr><td colspan="2">&nbsp;</td></tr><?php
								}?>
								<tr>
									<td width="80"><?php if($categorias[0]==2) echo 'Texto 1:';else echo 'Designação:';?></td>
									<td><?php echo '<input class="form-control" type="text" style="display:inline-block;width:400px'.$style_bloco_img_titulo.'" name="bloco_img_titulo'.$bloco_img_id.'" value="'.($bloco_img_titulo).'" />';?>&nbsp;<label style="display:inline-block" class="label_check" for="bloco_img_titulo_visivel<?php echo $bloco_img_id?>"><input type="checkbox" name="bloco_img_titulo_visivel<?php echo $bloco_img_id?>" id="bloco_img_titulo_visivel<?php echo $bloco_img_id?>" value="1" <?php if($bloco_img_titulo_visivel==1)echo 'checked="checked"'; ?> /> Ocultar</label></td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>

								<tr>
									<td width="80">Estilo:</td>
									<td align="left">
										<table border=0>
										<tr>
										<td align="left">
										<select name="bloco_img_titulo_cor<?php echo $bloco_img_id?>" id="bloco_img_titulo_cor<?php echo $bloco_img_id?>" class="form-control" style="width:350px">
										<option value="" <?php if(!$bloco_img_titulo_cor) echo 'selected'?>>Sem Estilo</option>
										<?php mostra_estilos($bloco_img_titulo_cor);?>
										</select>
										</td>
										<td>
										<a href="#" class="preto" onclick="reload_select('bloco_img_titulo_cor<?php echo $bloco_img_id?>');return false;" style="text-decoration:underline" title="Atualizar Select Estilo">Atualizar</a> |
										</td>
										<td>
										<?php if($bloco_img_titulo_cor){?><a href="#" onclick="ver_estilo('#bloco_img_titulo_cor<?php echo $bloco_img_id?>');return false;" class="preto" target="_blank" style="text-decoration:underline" title="Ver Estilo Selecionado">Ver Estilo</a> | <?php }?>
										</td>
										<td>
										<a href="../modulos_config/pagina.php?ID=0&dp=<?php echo date('YmdHis');?>&Tipo=1" class="preto" target="_blank" style="text-decoration:underline">Criar Estilo</a>
										</td>
										</tr>
										</table>
								</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>

								<?php //if($img || $categorias[0]==8){?>
								<?php if($categorias[0] != 2 && $categorias[0] != 8){?>
								<tr>
									<td width="80">Video URL:</td>
									<td><?php echo '<input class="form-control" type="text" style="width:400px;" name="bloco_img_video'.$bloco_img_id.'" value="'.$bloco_img_video.'" />';?></td>
								</tr><tr><td colspan="2">&nbsp;</td></tr><?
								}?>
								<tr>
									<td width="80">URL:</td>
									<td>
									<?php echo '<input class="form-control" type="text" style="width:400px;display:inline-block" name="bloco_img_url'.$bloco_img_id.'" id="bloco_img_url'.$bloco_img_id.'" value="'.$bloco_img_url.'" />';?>
									<a href="../menus/listcontent.php?field=bloco_img_url<?php echo $bloco_img_id?>" class="preto fancybox2" style="text-decoration:underline"><?php echo write_lang('key_procurar','Procurar')?></a>
									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr><?php //}?>
								<?php /* <tr>
									<td> </td>
									<td><label style="display:inline-block" class="label_check" for="bloco_img_url_default<?php echo $bloco_img_id; ?>"><input type="checkbox" name="bloco_img_url_default<?php echo $bloco_img_id; ?>" id="bloco_img_url_default<?php echo $bloco_img_id; ?>" value="1" <?php if($bloco_img_url_default == 1) echo 'checked="checked"'; ?> /> ou usar URL: <?php echo $GLOBALS['site_http_url'].'pg'.$GLOBALS['modulos_page_id'].'-m'.$bloco_img_id.'-'.limpa_special($bloco_img_titulo).'-'.$GLOBALS['admin_lang'];?></label></td>
								</tr><tr><td colspan="2">&nbsp;</td></tr> */?>
								<?php if($categorias[0]==24){?>
								<tr>
									<td width="80">Layout Bloco:</td>
									<td align="left">
										<table>
										<tr>
											<td align="left">
												<select name="bloco_layout<?php echo $bloco_img_id?>" class="form-control">
													<option value="" <?php if(!$bloco_layout) echo 'selected'?>>---</option>
													<option value="1" <?php if($bloco_layout==1) echo 'selected'?>>Imagem à Esquerda, Texto à Direita</option>
													<option value="2" <?php if($bloco_layout==2) echo 'selected'?>>Texto à Esquerda, Imagem à Direita</option>

												</select>
											</td>
										</tr>
										</table>
									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>
								<? }?>
								<tr>
									<td width="80">Estilo <?php echo 'Texto:';?></td>
									<td align="left">
										<table border=0>
										<tr>
										<td align="left">
										<select name="bloco_img_txt_cor<?php echo $bloco_img_id?>" id="bloco_img_txt_cor<?php echo $bloco_img_id?>" class="form-control" style="width:350px">
										<option value="" <?php if(!$bloco_img_txt_cor) echo 'selected'?>>Sem Estilo</option>
										<?php mostra_estilos($bloco_img_txt_cor);?>
										</select>
										</td>
										<td>
										<a href="#" class="preto" onclick="reload_select('bloco_img_txt_cor<?php echo $bloco_img_id?>');return false;" style="text-decoration:underline" title="Atualizar Select Estilo">Atualizar</a> |
										</td>
										<td>
										<?php if($bloco_img_txt_cor){?><a href="#" onclick="ver_estilo('#bloco_img_txt_cor<?php echo $bloco_img_id?>');return false;" class="preto" target="_blank" style="text-decoration:underline" title="Ver Estilo Selecionado">Ver Estilo</a> | <?php }?>
										</td>
										<td>
										<a href="../modulos_config/pagina.php?ID=0&dp=<?php echo date('YmdHis');?>&Tipo=1" class="preto" target="_blank" style="text-decoration:underline">Criar Estilo</a>
										</td>
										</tr>
										</table>
									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td width="80">Limitar texto:</td>
									<td>
									<?php echo '<input class="form-control" type="text" style="width:100px;display:inline-block" name="bloco_img_limite'.$bloco_img_id.'" id="bloco_img_limite'.$bloco_img_id.'" value="'.$bloco_img_limite.'" /> caracteres';?>

									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>
								<tr>
									<td width="80" style="vertical-align:middle">Texto PC:</td>
									<td>
										<?php echo '<textarea style="width:400px;height:100px'.$style_bloco_img_txt.'" name="bloco_img_txt'.$bloco_img_id.'" id="bloco_img_txt'.$bloco_img_id.'">'.$bloco_img_txt.'</textarea>';
											global $caminho_bt_editor2,$usa_css,$site_http_url;?>
										    <script>
												CKEDITOR.config.contentsCss = ["<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo uniqid('')?>","<?php echo $site_http_url?>files/classes.css?d=<?php echo uniqid('')?>"];
												var editor = CKEDITOR.replace( '<?php echo 'bloco_img_txt'.$bloco_img_id;?>',{toolbar : 'Small'},{height : '600'});
												CKEDITOR.config.stylesSet =[	<?php gera_estilos();?> ];
												CKFinder.setupCKEditor( editor, '../editor/ckeditor_4_5_1/ckfinder/' ) ;
											</script>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>
								<?php if($categorias[0]==2){?>
								<tr>
									<td width="80" style="vertical-align:middle">Texto Mobile:</td>
									<td>
										<?php echo '<textarea style="width:400px;height:100px'.$style_bloco_img_txt.'" name="bloco_img2_txt'.$bloco_img_id.'" id="bloco_img2_txt'.$bloco_img_id.'">'.$bloco_img2_txt.'</textarea>';
											global $caminho_bt_editor2,$usa_css,$site_http_url;?>
										    <script>
												CKEDITOR.config.contentsCss = ["<?php echo $site_http_url?>layout/template_admin.css?d=<?php echo uniqid('')?>","<?php echo $site_http_url?>files/classes.css?d=<?php echo uniqid('')?>"];
												var editor = CKEDITOR.replace( '<?php echo 'bloco_img2_txt'.$bloco_img_id;?>',{toolbar : 'Small'},{height : '600'});
												CKEDITOR.config.stylesSet =[	<?php gera_estilos();?> ];
												CKFinder.setupCKEditor( editor, '../editor/ckeditor_4_5_1/ckfinder/' ) ;
											</script>
									</td>
								</tr>
								<tr><td colspan="2">&nbsp;</td></tr>

									<tr>
										<td width="80">PC</td>
										<td>

											<table cellpadding="4" cellspacing="4" border="1">
											<tr>
												<td width="80">&nbsp;</td>
												<td width="80">x</td>
												<td width="80">y</td>
												<td width="80">voffset</td>
												<td width="80">hoffset</td>
												<td width="80">speed</td>
												<td width="80">start</td>
												<td width="80">easing</td>
												<td width="80">endspeed</td>
												<td width="80">text align</td>
											</tr>
											<tr>
												<td width="80">TITULO:</td>
												<td width="80"><input type="text" style="width:80px" name="a_data_x<?=$bloco_img_id?>" value="<?=$a_data_x?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_y<?=$bloco_img_id?>" value="<?=$a_data_y?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_voffset<?=$bloco_img_id?>" value="<?=$a_data_voffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_hoffset<?=$bloco_img_id?>" value="<?=$a_data_hoffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_speed<?=$bloco_img_id?>" value="<?=$a_data_speed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_start<?=$bloco_img_id?>" value="<?=$a_data_start?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_easing<?=$bloco_img_id?>" value="<?=$a_data_easing?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_endspeed<?=$bloco_img_id?>" value="<?=$a_data_endspeed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="a_data_text_align<?=$bloco_img_id?>" value="<?=$a_data_text_align?>"></td>
											</tr>
											<tr>
												<td width="80">TEXTO:</td>
												<td width="80"><input type="text" style="width:80px" name="b_data_x<?=$bloco_img_id?>" value="<?=$b_data_x?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_y<?=$bloco_img_id?>" value="<?=$b_data_y?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_voffset<?=$bloco_img_id?>" value="<?=$b_data_voffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_hoffset<?=$bloco_img_id?>" value="<?=$b_data_hoffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_speed<?=$bloco_img_id?>" value="<?=$b_data_speed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_start<?=$bloco_img_id?>" value="<?=$b_data_start?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_easing<?=$bloco_img_id?>" value="<?=$b_data_easing?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_endspeed<?=$bloco_img_id?>" value="<?=$b_data_endspeed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="b_data_text_align<?=$bloco_img_id?>" value="<?=$b_data_text_align?>"></td>
											</tr>
											</table>
										</td>
									</tr>
									<tr><td colspan="2">&nbsp;</td></tr>

									<tr>
										<td width="80">Mobile</td>
										<td>

											<table cellpadding="4" cellspacing="4" border="1">
											<tr>
												<td width="80">&nbsp;</td>
												<td width="80">x</td>
												<td width="80">y</td>
												<td width="80">voffset</td>
												<td width="80">hoffset</td>
												<td width="80">speed</td>
												<td width="80">start</td>
												<td width="80">easing</td>
												<td width="80">endspeed</td>
												<td width="80">text align</td>
											</tr>
											<tr>
												<td width="80">TITULO:</td>
												<td width="80"><input type="text" style="width:80px" name="c_data_x<?=$bloco_img_id?>" value="<?=$c_data_x?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_y<?=$bloco_img_id?>" value="<?=$c_data_y?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_voffset<?=$bloco_img_id?>" value="<?=$c_data_voffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_hoffset<?=$bloco_img_id?>" value="<?=$c_data_hoffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_speed<?=$bloco_img_id?>" value="<?=$c_data_speed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_start<?=$bloco_img_id?>" value="<?=$c_data_start?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_easing<?=$bloco_img_id?>" value="<?=$c_data_easing?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_endspeed<?=$bloco_img_id?>" value="<?=$c_data_endspeed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="c_data_text_align<?=$bloco_img_id?>" value="<?=$c_data_text_align?>"></td>
											</tr>
											<tr>
												<td width="80">TEXTO:</td>
												<td width="80"><input type="text" style="width:80px" name="d_data_x<?=$bloco_img_id?>" value="<?=$d_data_x?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_y<?=$bloco_img_id?>" value="<?=$d_data_y?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_voffset<?=$bloco_img_id?>" value="<?=$d_data_voffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_hoffset<?=$bloco_img_id?>" value="<?=$d_data_hoffset?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_speed<?=$bloco_img_id?>" value="<?=$d_data_speed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_start<?=$bloco_img_id?>" value="<?=$d_data_start?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_easing<?=$bloco_img_id?>" value="<?=$d_data_easing?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_endspeed<?=$bloco_img_id?>" value="<?=$d_data_endspeed?>"></td>
												<td width="80"><input type="text" style="width:80px" name="d_data_text_align<?=$bloco_img_id?>" value="<?=$d_data_text_align?>"></td>
											</tr>
											</table>
										</td>
									</tr>

								</tr><tr><td colspan="2">&nbsp;</td></tr>
								<?php }?>
								<?php if($categorias[0]==22){?>
								<tr>
									<td width="80"></td>
									<td align="left">
										<table cellpadding="0" cellspacing="0" border="0">
										<tr>
											<td>
												<input type="checkbox" name="bloco_img_lightbox<?php echo $bloco_img_id?>" value="1" <?php if($bloco_img_lightbox==1) echo 'checked'; ?>> Mostrar numa janela<br>
											</td>
										</tr>
										</table>
									</td>
								</tr><tr><td colspan="2">&nbsp;</td></tr>
								<?php }?>

								</table>

							</td>
						</tr>
						</table>
					</td>
				</tr>
				<tr><td colspan="2">&nbsp;</td></tr>
				</table>
				<?
				$rs->db_next();
			}
		}
		?>
		<input type="hidden" name="total_linhas_renovacoes" id="total_linhas_renovacoes" value="<? echo ($contador+1)?>" />
	</td>
</tr>

</table>
</div>
	<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td bgcolor="#000" style="color:#fff" height="30" align="center">NOVO ELEMENTO</td>
	</tr>
	<tr>
		<td align="left">
			<table width="100%" cellpadding="5" cellspacing="0" border="0" style="border: 1px solid #c0c0c0">
			<tr>
				<td width="80">Designação:</td>
				<td><input class="form-control" type="text" style="width:380px;display:inline-block" name="bloco_img_titulo" />&nbsp;Ocultar: <input type="checkbox" name="bloco_img_titulo_visivel" id="bloco_img_titulo_visivel" value="1" /></td>
			</tr>
			<tr>
				<td width="80">Video URL:</td>
				<td><input class="form-control" type="text" style="width:380px" name="bloco_img_video" /></td>
			</tr>
			<tr>
				<td width="80">URL:</td>
				<td><input class="form-control" type="text" style="width:380px" name="bloco_img_url" /></td>
			</tr>
			<tr>
				<td width="80">Imagem:</td>
				<td><input type="file" name="bloco_img_ext" class="BOLETIM_EXT" /></td>
			</tr>
			<tr>
				<td width="80">Texto:</td>
				<td><textarea style="width:400px;height:100px" name="bloco_img_txt"></textarea></td>
			</tr>
			</table>

		</td>
	</tr>
	</table>
