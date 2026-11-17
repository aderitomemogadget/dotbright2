<?php
    require_once('settings.inc.php');
    require_once('../../layout/header.inc.php');
    require_once('../../layout/body.inc.php');

/**
	function limpa_special_import($s)
	{
		$s=strip_tags($s);
		$s=str_replace('<br>','',$s);
		$s=str_replace('<br/>','',$s);
		$s=str_replace('<br />','',$s);
		$s=trim($s,"\t\n\r\0\x0B");
		$v=array("(",")","³","°","²","½","%","«","»","?","ª","º","®",'"','”','*','’','‘',"!","#","=");
		$s=str_replace($v,'',$s);
		$v=array(" ","'","/","&",":",";","`","´","~","^","¨","_",",",".","|","\\","<",">","—","€","±","×","¿");
		$s=str_replace($v,'-',$s);
		$s=str_replace('-----','-',$s);
		$s=str_replace('----','-',$s);
		$s=str_replace('---','-',$s);
		$s=str_replace('--','-',$s);
		$v=array("á","à","ã","â","ä"); 	$s=str_replace($v,'a',$s);
		$v=array("é","è","ê","ë"); 		$s=str_replace($v,'e',$s);
		$v=array("í","ì","î","ï"); 		$s=str_replace($v,'i',$s);
		$v=array("ó","ò","õ","ô","ö"); 	$s=str_replace($v,'o',$s);
		$v=array("ú","ù","û","ü"); 		$s=str_replace($v,'u',$s);
		$v=array("ñ"); 					$s=str_replace($v,'n',$s);
		$v=array("ç"); 					$s=str_replace($v,'c',$s);
		$v=array("Á","À","Ã","Â","Ä"); 	$s=str_replace($v,'A',$s);
		$v=array("É","È","Ê","Ë"); 		$s=str_replace($v,'E',$s);
		$v=array("Í","Ì","Î","Ï"); 		$s=str_replace($v,'I',$s);
		$v=array("Ó","Ò","Õ","Ô","Ö"); 	$s=str_replace($v,'O',$s);
		$v=array("Ú","Ù","Û","Ü"); 		$s=str_replace($v,'U',$s);
		$v=array("Ñ"); 		$s=str_replace($v,'N',$s);
		$v=array("Ç"); 		$s=str_replace($v,'C',$s);
		return $s;
	}
/**/
	// VAMOS ATUALIZAR TODAS AS FAMILIAS COM UM CÓDIGO ESPECIAL
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);


?>



<script type="text/javascript" src="../../external_api/fancyBox/jquery.fancybox.pack.js"></script>
<style type="text/css">
	.error-msg{
		color: #ff0000;
		font-weight: bold;
		padding: 5px 5px 5px 20px;
		background: #ffffff;
		border:1px solid #ff0000;
		border-radius: 2px;
		margin:10px 0px;
		display: inline-block;
	}
	#close-msg{
		margin-left: 20px;
		font-size: 15px;
		cursor:pointer;
	}
</style>

<form enctype="multipart/form-data" method="POST">
	 <input type = "file" name = "excel" onchange="ValidateSingleInput(this)" style="display:inline-block;"/>
	 <span id="submit-btn"></span>

</form>
<div style="height: 20px;"></div>

	<input type="button" class="btn btn-primary" value=" Download Ficheiro Exemplo" onclick="window.open('download/f_exemplo.xlsx','_blank')" />

<div style="height: 20px;"></div>
<div id="error-msg"></div>

<div id="table-data"></div>

<script type="text/javascript">
var _validFileExtensions = [".xls", ".csv"];
function ValidateSingleInput(oInput) {
    if (oInput.type == "file") {
        var sFileName = oInput.value;
         if (sFileName.length > 0) {
            var blnValid = false;
            for (var j = 0; j < _validFileExtensions.length; j++) {
                var sCurExtension = _validFileExtensions[j];
                if (sFileName.substr(sFileName.length - sCurExtension.length, sCurExtension.length).toLowerCase() == sCurExtension.toLowerCase()) {
                    blnValid = true;
                    break;
                }
            }

            if (!blnValid) {
                $("#error-msg").html('<span class="error-msg">Erro, o ficheiro ' + sFileName + ' é invalido, os tipos validos são: ' + _validFileExtensions.join(', ')+'<span onclick="closemsg(\'error-msg\')" id="close-msg">X</span></span>');

                oInput.value = "";
                return false;
            }
            else{
            	$("#submit-btn").html('<input type="submit" class="btn btn-primary" name="btn" value="Upload" style="padding: 0px 10px;">');
            }
        }
    }
    return true;
}

function closemsg(bloco){
	$("#"+bloco).html("");
}

</script>

<?php
$addcols = array(
	array('name'=>'PRAZO','col'=>'A','type'=>'INT(6)'),
    array('name'=>'FP','col'=>'B','type'=>'VARCHAR(15) NOT NULL'),
    array('name'=>'MINIMO','col'=>'C','type'=>'VARCHAR(15) NOT NULL'),
    array('name'=>'DESPESAS_CONTRATUAIS','col'=>'D','type'=>'VARCHAR(6) NOT NULL'),
    array('name'=>'FACTOR','col'=>'E','type'=>'DECIMAL(15,15) NOT NULL')
			   );

$table = 'credito';


addtableorfields($table,$addcols);
$tv = sizeof($addcols);
$j = 0;
$insert = 'INSERT INTO '.$table.' (';
foreach ($addcols as $key) {
	$insert.= $key['name'];
	if($j < $tv-1){
		$insert.= ',';
	}
	$j++;
	}
$insert .= ') VALUES(';
$rs = new DataObj;
$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);

$prazos = array();

//echo $insert;
if(isset($_POST) && $_POST['btn']=='Upload' ){
	if(isset($_FILES['excel']) && $_FILES['excel']['error']==0) {
		require_once dirname(__FILE__) . '/import_xls2/PHPExcel-1.8/Classes/PHPExcel.php';
		$v = 1;
		$tmpfname = $_FILES['excel']['tmp_name'];
		/**/
		$ext = pathinfo($_FILES['excel']['name'], PATHINFO_EXTENSION);
		$nome_ficheiro = limpa_special(str_replace('.'.$ext, '', $_FILES['excel']['name']));
					// exit;
		$filename = $nome_ficheiro.'_'.date('YmdHis');
		$thefile = $GLOBALS['site_shell_url'] .'files/importar/'.$filename.'.'.$ext;
		/**/

		//Excel ou CSV
		$ficheiro['name'] = $_FILES['excel']['name'];
		$ficheiro['temp'] = $_FILES['excel']['tmp_name'];
		$valid = 0;
		$ext = pathinfo($ficheiro['name'], PATHINFO_EXTENSION);
		$valid  = $ext == 'csv'?1:0;
		$valid += $ext == 'CSV'?1:0;
		$valid += $ext == 'xls'?1:0;
		$valid += $ext == 'xlsx'?1:0;


		if($valid == 0)
		{
		   echo '<div style="color:#F00;text-align:center">ERRO: tipo de ficheiro errado. Deve ser CSV ou XLS<br><br><input class="botao" type="button" name="voltar" value="Voltar" onclick="window.open(\'import.php\',\'_self\');"></div>';
		   die();
		}
		$filename = 'importa_creditos';

		if($ext=='csv' || $ext=='CSV') {$result = copia_segura($ficheiro['temp'],  dirname(__FILE__) .'/../../../files/importar/'.$filename.'.csv'); $thefile = dirname(__FILE__) .'/../../../files/importar/'.$filename.'.csv';}
		if($ext=='xls' || $ext=='XLS') {$result = copia_segura($ficheiro['temp'],  dirname(__FILE__) .'/../../../files/importar/'.$filename.'.xls');$thefile = dirname(__FILE__) .'/../../../files/importar/'.$filename.'.xls';}




        /*$result = copia_segura($_FILES['excel']['temp'], $thefile);
        echo $thefile;*/
		/**/

		//$thefile = dirname(__FILE__) .'/../../../files/importar/'.$filename.'.csv';


		$excelReader = PHPExcel_IOFactory::createReaderForFile($thefile);
		$excelObj = $excelReader->load($thefile);
		$worksheet = $excelObj->getSheet(0);
		$lastRow = $worksheet->getHighestRow();
		$j = 0;

		$inseridos = 0;
		$atualizados = 0;
		for ($row = 1; $row <= $lastRow; $row++) {
			if($j==0){
				foreach ($addcols as $key) {
					if($worksheet->getCell($key['col'].$row)->getValue()!=$key['name']){
						$v = 0;
					}
				}
			}
			if($j > 0 && $v == 1){
				if($j==1){
					/***/
						$truncate = 'truncate '.$table;
						Runquery($db_link,$DB_name,$truncate);
					/***/
				}

				$prazo = $worksheet->getCell('A'.$row)->getValue();
				$fp = $worksheet->getCell('B'.$row)->getValue();
				$minimo = $worksheet->getCell('C'.$row)->getValue();
				$despeza = $worksheet->getCell('D'.$row)->getValue();
				$factor = $worksheet->getCell('E'.$row)->getValue();

				if($fp != ""){
					$prazos[$prazo]=array('fp' => $fp,
										  'minimo' => $minimo,
										 'despeza' => $despeza,
										 'factor'  => $factor
											 );
				}

				$sql = 'select PRAZO from '.$table.' where PRAZO='.$prazo;
				$rs->db_open($db_link, $DB_name, $sql);
				if ($rs->EOF()) // adiciona si no existe
				{
					$sqli = $insert.$prazo.','.$fp.','.$minimo.','.$despeza.','.$factor.')';
					Runquery($db_link,$DB_name,$sqli);
					$inseridos++;
				}else{ //atualiza
					$sqli = 'update '.table.' set PRAZO="'.$prazo.'",
											      FP="'.$fp.'",
											      MINIMO="'.$minimo.'",
											      DESPESAS_CONTRATUAIS="'.$despeza.'",
											      FACTOR="'.$factor.'"
					       					where PRAZO='.$prazo;
					Runquery($db_link,$DB_name,$sqli);
					$atualizados++;
				}
				$rs->db_close();

			}

			 $j++;
		}

}
 echo '<div id="a-msg"><span class="error-msg">';
	if($v == 0){
		echo 'Erro ao fazer upload. Cabeçalhos da tabela de excel não validos';
	}else{
		$fp = fopen(dirname(__FILE__) .'/../../../files/importar/credito.json', 'w');
		$credito = array('credito'=>$prazos);
		fwrite($fp, json_encode($credito));
		fclose($fp);
		echo 'A tabela de crédito foi atualizada';
	}
 echo '<span onclick="closemsg(\'a-msg\')" id="close-msg">X</span></span></div>';
}

$sql = 'select * from '.$table.' order by PRAZO';
$rs->db_open($db_link, $DB_name, $sql);
echo '<div style="height: 20px;"></div>';
echo 'A tabela de excel deve ter os seguintes Cabeçalhos: ';
echo '<table cellpadding="5" cellspacing="5" border="1" style="text-align:center">';
echo '<tr>';
	echo '<td>PRAZO</td>';
	echo '<td>FP</td>';
	echo '<td>MINIMO</td>';
	echo '<td>DESPESAS_CONTRATUAIS</td>';
	echo '<td>FACTOR</td>';
    echo '</tr>';
while (!$rs->EOF()) // adiciona si no existe
{
	echo '<tr>';
	echo '<td>'.$rs->db_data['PRAZO'].'</td>';
	echo '<td>'.$rs->db_data['FP'].'</td>';
	echo '<td>'.$rs->db_data['MINIMO'].'</td>';
	echo '<td>'.$rs->db_data['DESPESAS_CONTRATUAIS'].'</td>';
	echo '<td>'.$rs->db_data['FACTOR'].'</td>';
    echo '</tr>';
	$rs->db_next();
}
$rs->db_close();
echo '</table>';
//echo '<>';
?>
<div style="height: 20px;">
</div>
<img src="../../images/bsh.png" width="100%" height="30px">
<?php require_once('../../layout/bottom.inc.php');