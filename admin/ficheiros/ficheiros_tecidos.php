<?php
include "settings.inc.php";
?>
	<html>
	<head>
		<title>ADMIN</title>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
		<link rel="stylesheet" type="text/css" href="<?= $site_http_url ?>admin/admin_estilos.css"/>
		<style>HTML, body {
				background: #ffffff
			}</style>
	</head>
<body>
<?php
insert_api_multiple_files("ficheiros_tecidos", $ID);

function insert_api_multiple_files($table_name, $ID_ORG)
{
	global $DB_host, $DB_user, $DB_pass, $DB_name, $site_shell_url, $site_http_url;
	$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);
	$rs = new DataObj;
	?>
	<script type="text/javascript">
		function uniqid() {
			var newDate = new Date;
			return newDate.getTime();
		}
		function upload_new_file(inputId2, tablename, id_org, id_file) {
			inputId = document.getElementById(inputId2).value;
			var allowedExt = new Array(".gif", ".jpg", ".png", ".GIF", ".JPG", ".PNG");
			var ext = getfileextension_v(inputId);
			var returns = "";
			for (var a = 0; a < allowedExt.length; a++) {
				if (ext == allowedExt[a]) returns = 1;
			}
			if (returns == "") {
				alert(EXT_INV);
				resetFileInput_v(inputId2);
			}
			else {
				startUpload(inputId2, tablename, id_org, id_file);
			}
		}
		function getfileextension_v(inputId) {
			var filename = inputId;
			if (filename.length == 0) return "";
			var dot = filename.lastIndexOf(".");
			if (dot == -1) return "";
			var extension = filename.substr(dot, filename.length);
			return extension;
		}

		function resetFileInput_v(inputID) {
			var originalInput = document.getElementById(inputID);
			var clearedInput = originalInput.cloneNode(false);
			originalInput.parentNode.insertBefore(clearedInput, originalInput);
			originalInput.parentNode.removeChild(originalInput);
		}

		function startUpload(id, tablename, id_org, id_file) {
			ficheiro = document.getElementById(id).value;
			if (ficheiro != "") {
				document.getElementById(id + '_loading').style.display = 'block';
				document.configurar.action = "upload_tecidos.php?tablename=" + tablename + "&id_org=" + id_org + "&id_file=" + id_file + "&dmp=" + uniqid();
				document.configurar.target = id + "_frame";
				document.configurar.submit();
				return true;
			}
			else alert("Please, select an image to upload!");
			return false;
		}

		function delFile(id, tablename, id_org, id_file, file1, file2) {
			var r = confirm("Tem a certeza que pretende remover este ficheiro?")
			if (r == true) {
				document.getElementById(id + '_loading').style.display = 'block';
				document.configurar.action = "upload_tecidos.php?del=1&tablename=" + tablename + "&id_org=" + id_org + "&id_file=" + id_file + "&file1=" + file1 + "&file2=" + file2 + "&dmp=" + uniqid();
				document.configurar.target = id + "_frame";
				document.configurar.submit();
			}
		}

		function updateTXT(id, tablename, id_org, id_file) {
			document.getElementById(id + '_loading').style.display = 'block';
			document.configurar.action = "upload_tecidos.php?update=1&tablename=" + tablename + "&id_org=" + id_org + "&id_file=" + id_file + "&dmp=" + uniqid();
			document.configurar.target = id + "_frame";
			document.configurar.submit();
		}

	</script>
	<form name="configurar" id="configurar" enctype="multipart/form-data" method="post">
		<table border=0 cellspacing="0" cellpadding="0">
			<tr>
				<td valign="top">
					<iframe id="ad_img_peq_frame" name="ad_img_peq_frame" src="" frameborder=0 width="0" height="0"
					        style="width:0px;height:0px;border:0px solid #000;"></iframe>
					<table width="129" border=0 cellspacing="0" cellpadding="0">
						<tr>
							<td>
								<table width="129" border=0 cellspacing="0" cellpadding="0">
									<tr>
										<td style="background:url('<?= $site_http_url ?>admin/layout/img_default.jpg'); background-repeat:no-repeat"
										    height="100" align="center" valign="top" class="preto">
											<div style="overflow:hidden; width:129px; height:130px;">
												<div
													style="overflow:hidden; width:129px; height:130px; opacity:0;filter:alpha(opacity=0);cursor:pointer;">
													<input type="file" id="ad_img_peq" name="ad_img_peq"
													       title="Adicionar novo ficheiro"
													       onchange="upload_new_file('ad_img_peq','<?= $table_name ?>','<?= $ID_ORG ?>','');"
													       size="1" style="width:129px; height:130px; cursor:pointer"/>
												</div>
											</div>
											<div id="ad_img_peq_loading" style="display:none"><img
													src="<?= $site_http_url ?>admin/layout/1.gif" border=0/></div>
											<div style="float:left; width:25%;">
												Ordem:<br/>
												<input type="text" id="img_ordem" name="img_ordem"
												       style="width:100%; height:30px"/>
											</div>
											<div style="float:left; width:75%;">
												Nome:<br/><input type="text" id="img_name" name="img_name"
												                 style="width:100%; height:30px"/>
											</div>
											<div id="ad_img_peq_loading" style="display:none"><img
													src="<?= $site_http_url ?>admin/layout/1.gif" border=0/></div>
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</td><?
				$sql = "Select * from $table_name where ID_ORG=$ID_ORG ORDER BY Ordem, Name ASC";
				$db_res = $rs->db_open($db_link, $DB_name, $sql);
				$x = 0;
				while (!$rs->EOF())
				{
					$x++;
					$id_file = $rs->db_data['ID'];
					$I1 = $rs->db_data['img_ext'];
					$img_name = stripslashes($rs->db_data['Name']);
					$img_name = str_replace('.jpg', '', $img_name);
					$img_name = str_replace('.JPG', '', $img_name);
					$img_name = str_replace('.png', '', $img_name);
					$img_name = str_replace('.PNG', '', $img_name);
					$img_ordem = $rs->db_data['Ordem'];

					$img_file = "files/" . $table_name . "/" . $ID_ORG . "_" . $id_file . "_1." . $I1;
					$img_file_med = "files/tmp/" . $table_name . "_" . $ID_ORG . "_" . $id_file . "_1." . $I1;

					if (file_exists($site_shell_url . $img_file)) $original = date("Y-m-d H:i:s", filemtime($site_shell_url . $img_file)); // data/hora em que foi modificada/criada
					if (file_exists($site_shell_url . $img_file_med)) $peq = date("Y-m-d H:i:s", filemtime($site_shell_url . $img_file_med)); //data/hora em que foi modificada/criada
					if ((!file_exists($site_shell_url . $img_file_med) || $peq < $original) && file_exists($site_shell_url . $img_file))
					{
						smart_resize_image($site_shell_url . $img_file, 130, 130, true, $site_shell_url . $img_file_med, false, false);
					}
					?>
					<td valign="top">
						<iframe id="ad_img_peq<?= $id_file ?>_frame" name="ad_img_peq<?= $id_file ?>_frame" src=""
						        frameborder=0 width="0" height="0"
						        style="width:0px;height:0px;border:0px solid #000;"></iframe>
						<table width="129" border=0 cellspacing="0" cellpadding="0">
							<tr>
								<td>
									<table width="129" border=0 cellspacing="0" cellpadding="5">
										<tr>
											<td style="border:#999999 0px solid;" height="130" align="center"
											    valign="top" class="preto">
												<? if (file_exists($site_shell_url . $img_file_med))
												{ ?><a href="<?= $site_http_url . $img_file ?>?dmp=<?= uniqid(''); ?>"
												       target="_blank" style="text-decoration:underline"><img
														src="<?= $site_http_url . $img_file_med ?>" width="129"
														height="130" border=0></a><? }
												else
												{ ?><img src="<?= $site_http_url ?>admin/layout/img_default.jpg"
												         width="129" border=0><? } ?>

												<table width="129px" border=0 cellspacing="0" cellpadding="0">
													<tr>
														<td class="preto" align="center" colspan="4">
															<div style="float:left; width:25%;">
																Ordem:<br/>
																<input type="text" id="img_ordem<?= $id_file ?>"
																       name="img_ordem<?= $id_file ?>"
																       style="width:100%; height:30px"
																       value="<?= $img_ordem ?>"/>
															</div>
															<div style="float:left; width:75%;">
																Nome:<br><input type="text" id="img_name<?= $id_file ?>"
																                name="img_name<?= $id_file ?>"
																                style="width:100%; height:30px"
																                value="<?= $img_name ?>"/>
															</div>
														</td>
													</tr>
													<tr>
														<td width="18px"
														    style="background:url('<?= $site_http_url ?>admin/images/change2.png'); background-repeat:no-repeat; cursor:pointer"
														    height="18" align="center" valign="top" class="preto">
															<div style="overflow:hidden; width:18px; height:18px">
									<span id="f<?= $id_file ?>"
									      style="overflow:hidden; width:18px; height:18px; opacity:0;filter:alpha(opacity=0);cursor:pointer;">
										<input type="file" id="ad_img_peq<?= $id_file ?>"
										       name="ad_img_peq<?= $id_file ?>" title="<?= $txt_car_load2[$lang] ?>"
										       onchange="upload_new_file('ad_img_peq<?= $id_file ?>','<?= $table_name ?>','<?= $ID_ORG ?>','<?= $id_file ?>');"
										       size="1" style="width:18px; height:18px; cursor:pointer"/>
									</span>
															</div>

														</td>
														<td width="18px"><a
																href="javascript:updateTXT('ad_img_peq<?= $id_file ?>','<?= $table_name ?>','<?= $ID_ORG ?>','<?= $id_file ?>')"><img
																	title="gravar foto"
																	src="<?= $site_http_url ?>admin/images/save.png"
																	border="0" style="width:18px; height:18px"/></a>
														</td>
														<td width="10px">
															<div id="ad_img_peq<?= $id_file ?>_loading"
															     style="display:none"><img
																	src="<?= $site_http_url ?>admin/layout/1.gif"
																	border=0/></div>
														</td>

														<td align="right"><a
																href="javascript:delFile('ad_img_peq<?= $id_file ?>','<?= $table_name ?>','<?= $ID_ORG ?>','<?= $id_file ?>','<?= $site_http_url . $img_file ?>','<?= $site_http_url . $img_file_med ?>')"><img
																	title="apagar foto"
																	src="<?= $site_http_url ?>admin/images/del.png"
																	border="0"/></a></td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
					<?
					if ($x > 4)
					{
						$x = 0;
						echo "</tr><tr>";
					}
					$rs->db_next();
				}
				$rs->db_close();
				?></tr>
		</table>
	</form><?
}