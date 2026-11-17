<?php
	ini_set('max_execution_time','0');
	ini_set('memory_limit','1024M');
    require_once('settings.inc.php');
    require_once('../layout/header.inc.php');
    require_once('../layout/body.inc.php');

	require_once dirname(__FILE__) . '/import_xls2/PHPExcel-1.8/Classes/PHPExcel/IOFactory.php';

	?>
	<style>
	.botao{ padding:0 5px}
	</style>
	<table class="t3" align="center" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td valign="top" align="left"><?php require_once('../layout/menus.inc.php'); ?>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center"><h3>Importa&ccedil;&atilde;o de produtos</h3></td></tr>
            </table>
            <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
                <td style="color:#000;padding:10px;font-family:monospace;font-size:15px;" align="left"  bgcolor="#FFF"><?
                $ficheirocsv['name'] = $_FILES['import_file']['name'];
                $ficheirocsv['temp'] = $_FILES['import_file']['tmp_name'];


				$ext = pathinfo($ficheirocsv['name'], PATHINFO_EXTENSION);
                if($ext!='xlsx' && $ext!='XLSX')
                {
                   echo '<div style="color:#F00;text-align:center">ERRO: tipo de ficheiro errado. Deve ser xlsx<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
                }
                else
                {
					$nome_ficheiro = limpa_special(str_replace('.'.$ext, '', $ficheirocsv['name']));
					// exit;
					$filename = $nome_ficheiro.'_'.date('YmdHis');
                    $result = copia_segura($ficheirocsv['temp'], FILE_PATH.$filename.'.'.$ext);
                    if(!$result) echo '<div style="color:#F00;text-align:center">ERRO: Não foi possível colocar o ficheiro no servidor na pasta '.FILE_PATH.$filename.'.'.$ext.'<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
                    else
                    {
						$passa = 0;
						$camposObrigatorios = '';
						$contaTotalFicheiro = 0;
						$totalObrigatorios = 0;
						$camposIgnorados = array();

						$objReader = PHPExcel_IOFactory::createReader('Excel2007');

						$inputFileType = 'Excel2007';
						$inputFileName = FILE_PATH.$filename.'.'.$ext;
						$sheetIndex = 0;
						$myRow = 1; // LINHA COM OS CABECALHOS

						$objReader = PHPExcel_IOFactory::createReader($inputFileType);
						$sheetnames = $objReader->listWorksheetNames($inputFileName);
						$objReader->setLoadSheetsOnly($sheetnames[$sheetIndex]);
						$objPHPExcel = $objReader->load($inputFileName);
						$sheetData = $objPHPExcel->getActiveSheet() ->rangeToArray('A' . $myRow . ':' .  $objPHPExcel->getActiveSheet()->getHighestColumn() . $myRow );

						// CABEÇALHOS NA PRIMEIRA LINHA
						$names = array();
						foreach($sheetData[0] as $field){ if(trim($field) != '')$names[] = trim($field); }

						$CAMPOS_PERMITIDOS = array_map("CAMPOS", $GLOBALS['arr_campos_importar']);

						foreach($GLOBALS['arr_campos_importar'] as $campos)
						{
							// TOTAL DE OBRIGATORIOS
							if($campos['OBRIGATORIO'] == 1){ $totalObrigatorios += 1;}

							// CAMPOS PERMITIDOS DE IMPORTAÇÃO
							$CAMPOS_PERMITIDOS[] = $campos['CAMPO'];
						}
						for($a=0; $a<count($names); $a++)
						{
							// CAMPOS QUE VÃO SER IGNORADOS
							if(substr_count($names[$a], 'Tipo ') || substr_count($names[$a], 'TIPO ') || substr_count($names[$a], 'tipo ')) {}
							elseif(!in_array($names[$a], $CAMPOS_PERMITIDOS) && $names[$a] != '') $camposIgnorados[] = $names[$a];
							
							// echo $names[$a].'|'.substr_count(str_replace('Tipo – ', 'Tipo - ',strtolower($names[$a])), 'Tipo - ').'<br>';
						}
						
						foreach($GLOBALS['arr_campos_importar'] as $campos)
						{
							// CAMPOS QUE TEM QUE EXISTIR NO FICHEIRO
							if($campos['OBRIGATORIO'] == 1 && $camposObrigatorios != '') $camposObrigatorios .= ', '.$campos['CAMPO']; elseif($campos['OBRIGATORIO'] == 1) $camposObrigatorios .= $campos['CAMPO'];

							// VAMOS CONTAR DOS OBRIGATORIOS QUANTOS ESTÃO NO FICHEIRO
							if($campos['OBRIGATORIO'] == 1 && in_array($campos['CAMPO'], $names))
							{
								$contaTotalFicheiro += 1;
							}
						}

						// SE O NUMERO DE OBRIGATIRIOS É IGUAL AO NUMERO NO FICHEIRO ENTÃO PERMITIMOS A IMPORTAÇÃO
						if($totalObrigatorios == $contaTotalFicheiro) $passa = 1;

						if($passa == 0) // ERRO
						{
						   echo '<div style="color:#000;text-align:center">Existem campos obrigatórios que não foram encontrados no ficheiro, por favor, verifique:<br><br> '.$camposObrigatorios.'<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
						   @unlink(FILE_PATH.$filename.'.'.$ext);
						}
						else
						{
							if(count($camposIgnorados) > 0) echo '<div style="color:#000;text-align:center">As seguintes colunas foram ignoradas:<br><br>'.implode(' | ', $camposIgnorados).'<br><br></div>';


							echo '<div style="color:#000;text-align:center">O seu ficheiro vai ser analisado...<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
							echo '
							<script>
							$( document).ready(function()
							{
								$.ajax({type: \'GET\', url: "import_xls2/run_webservice_analise.php", dataType: \'html\', cache: false, success: function(id){}});
							});
							</script>';

						}

                    }
                }
                ?>
                </td>
			</tr>
            </table>
            <table class="p1" width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr><td style="color:#FFF" align="center">&nbsp;</td></tr>
            </table>
        </td>
        <td width="10px">&nbsp;</td>
        <td valign="top"></td>
    </tr>
	</table>
	<? require_once('../layout/bottom.inc.php');?>