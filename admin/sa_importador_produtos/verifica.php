<?php
require_once('settings.inc.php');
$path = dirname( __FILE__ ) . '/../../';
global $DB_host,$DB_user,$DB_pass,$DB_name;
$db_link = db_opendb_conection($DB_host, $DB_user, $DB_pass);

	$rs = new DataObj; $rs2=new DataObj;
	$linhas = '';
	$sql = "SELECT * FROM products_import ORDER BY import_id ASC LIMIT 10 ";
	$rs->db_open($db_link,$DB_name,$sql);
	if(!$rs->EOF())
	{
		$linhas .= '<table border="1" cellspacing="0" cellpadding="5" style="table-layout: fixed;background-color:#fff">';
		$linhas .= '<tr>';
		$linhas .= '<td colspan="20" style="font-size:16px;font-weight:bold">Preview: (primeiras 10 linhas)</td>';
		$linhas .= '</tr>';
		$linhas .= '<tr>';
		$linhas .= '<td colspan="20" style="font-size:14px;font-weight:bold">Verifique se a informação se encontra corretamente analisada</td>';
		$linhas .= '</tr>';
		$linhas .= '<tr>';

		$linhas .= '<td>Marca</td><td>Categoria</td><td style=" white-space: nowrap;">Sub Categoria</td><td style=" white-space: nowrap;">Sub Categoria 2</td><td>Tipo</td><td>Tipo_Caracteritica</td><td style=" white-space: nowrap;">Código do produto</td><td>Produto</td><td>Descrição</td><td>Medidas</td><td style=" white-space: nowrap;">Preço SITE com IVA</td><td>EAN</td><td>Stock</td><td style=" white-space: nowrap;">Imagem 1</td><td style=" white-space: nowrap;">Imagem 2</td><td style=" white-space: nowrap;">Imagem 3</td><td style=" white-space: nowrap;">Imagem 3</td>';
		$linhas .= '</tr>';
		while(!$rs->EOF())
		{
			
			$Tipo_Caracteritica_txt = '';
			if($rs->db_data['Tipo_Caracteritica'] != '')
			{
				
				$Tipo_Caracteritica = explode('[split2]', $rs->db_data['Tipo_Caracteritica']);
				for($a=0; $a<count($Tipo_Caracteritica);$a++)
				{
					$t = explode('[split]', $Tipo_Caracteritica[$a]);
					
					if($t[0] && $t[1]) $Tipo_Caracteritica_txt .= $t[0].': '.$t[1].'<br>';
				}
			}
			$linhas .= '<tr>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Marca'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Categoria'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;;">'.$rs->db_data['Sub_Categoria'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Sub_Categoria2'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Tipo'].'</td>';
			$linhas .= '<td style="white-space:nowrap;">'.$Tipo_Caracteritica_txt.'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Codigo_produto'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Produto'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Descricao'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Medidas'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Preco_IVA'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['EAN'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Stock'].'</td>';
			$linhas .= '<td style=" white-space: nowrap;">'.$rs->db_data['Imagem_1'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Imagem_2'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Imagem_3'].'</td>';
			$linhas .= '<td style="word-wrap: break-word;overflow-wrap: break-word;">'.$rs->db_data['Imagem_4'].'</td>';
			$linhas .= '</tr>';

			$tem_ficheiros = 1;

			$rs->db_next();
		}
		$linhas .= '</table>';
	}
	$rs->db_close();

	if($tem_ficheiros == 1 && !file_exists($path.'files/importar/atualizar.txt') && file_exists($path.'files/importar/atualizar_temp.txt'))
	{
		echo '0[split]';
		echo 'A analisar ficheiro...';
	}
	elseif($tem_ficheiros == 1 && !file_exists($path.'files/importar/atualizar.txt'))
	{
		echo '-1[split]';
		echo $linhas;
	}
	elseif(file_exists($path.'files/importar/atualizar.txt'))
	{ 
		echo '0[split]';
		echo '<center><b>a atualizar base de dados</b> <br><br> </center><div style="background:#FFF;font-size:11px;padding:10px">';
		
		$fh = fopen($path.'files/importar/atualizar.txt','r');
		while ($line = fgets($fh)) 
		{
			echo rtrim($line, ' | ');
		}
		fclose($fh);
		
		echo '...<img src="img/loading.gif" border="0" style="vertical-align:middle" /><br><br> <center><a href="#" onclick="remove2();return false;" style="color:#000">CANCELAR IMPORTAÇÃO</a></center></div>';
	}
	else
	{
		echo 'Nada a importar[split]';
	}




/*
$path = dirname( __FILE__ ) . '/../../';


if(file_exists($path.'files/importar/atualizar.txt'))
{
	$file_atualizar = dirname(__FILE__) . '/../../files/importar/atualizar.txt';
	if(file_exists($file_atualizar))
	{
		$line = fgets(fopen($file_atualizar, 'r'));
		$ext = pathinfo($line, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(SITE_SHELL_URL . 'files/importar/', '', $line));
		echo '<b>a importar ficheiro</b>: '.$file[0].'.'.$ext.' <img src="img/loading.gif" border="0" style="vertical-align:middle" />';
	}

	echo '[split]';
	$tem_ficheiros = 0;$num = 1;
	if(count(glob(dirname(__FILE__) . '/../../files/importar/*.xlsx')) > 0) echo '<span style="font-weight:bold;color:#000;font-size:12px">Ficheiros para importar na seguinte ordem:</span><br><br>';
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename));
		echo $num.' - '.$file[0].'.'.$ext.' - <a style="color:#000" href="#" onclick="remove(\''.str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename).'\');return false;">Remover</a><br>';
		$num++;
	}
}
else
{
	$tem_ficheiros = 0;
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$tem_ficheiros = 1;
	}

	if($tem_ficheiros == 1) echo '-1[split]';
	else echo '0[split]';

	$tem_ficheiros = 0; $num = 1;
	if(count(glob(dirname(__FILE__) . '/../../files/importar/*.xlsx')) > 0) echo '<span style="font-weight:bold;color:#000;font-size:12px">Ficheiros para importar na seguinte ordem:</span><br><br>';
	foreach (glob(dirname(__FILE__) . '/../../files/importar/*.xlsx') as $filename)
	{
		$ext = pathinfo($filename, PATHINFO_EXTENSION);
		$file = explode('_', str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename));
		echo $num.' - '.$file[0].'.'.$ext.' - <a style="color:#000" href="#" onclick="remove(\''.str_replace(dirname(__FILE__) . '/../../files/importar/', '', $filename).'\');return false;">Remover</a><br>';
		$num++;
	}
}*/