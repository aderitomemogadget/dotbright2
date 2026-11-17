<?php
function compress_html($compress){$compress = str_replace(array("\n", "\r", "\t", "\o", "\xOB"), '', $compress);return $compress;}

function gera_promos()
{
	$familia_produtos = 'files/promos/promos.js';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$listagem2 = new DataObj;

	$sql = '
			SELECT
				sp.promo_id, sp.DESIGNACAO, spp.PROD_ID, sp.DATA_INICIO,sp.DATA_FIM AS DTAFIM
			FROM
				sales_promotions sp
				INNER JOIN sales_promotions_products spp ON spp.promo_id = sp.promo_id
			WHERE
				sp.ATIVO = 1
				AND sp.removido = 0
				AND spp.PROD_ID > 0
			ORDER BY
				spp.PROD_ID
			';

	$listagem2->db_open($db_link,$DB_name,$sql);

	$return = '';
	if(!$listagem2->EOF())
	{
		$i=0;
		while(!$listagem2->EOF())
		{
			$prod_id = $listagem2->db_data['PROD_ID'];
			$designacao = $listagem2->db_data['DESIGNACAO'];
			$data_inicio = strtotime($listagem2->db_data['DATA_INICIO']);
			$data_fim = strtotime($listagem2->db_data['DTAFIM']);

			if($i > 0) $return .= ',{"id" : "'.$prod_id.'", des: "'.$designacao.'", DATA_INICIO: "'.$data_inicio.'", DATA_FIM: "'.$data_fim.'"}';
			else $return .= '{"id" : "'.$prod_id.'", des: "'.$designacao.'", DATA_INICIO: "'.$data_inicio.'", DATA_FIM: "'.$data_fim.'"}';

			$listagem2->db_next();
			$i++;
		}
	}
	$listagem2->db_close();

	if($return)
	{
		$script = 'var products2 = ['.compress_html($return).'];';
		$file_name = $GLOBALS['site_shell_url'] . $familia_produtos;
		$fh = fopen($file_name, 'w') or die("can't open file");
		fwrite($fh, $script);
		fclose($fh);
	}
	else
	{
		$script = 'var products2 = [];';
		$file_name = $GLOBALS['site_shell_url'] . $familia_produtos;
		$fh = fopen($file_name, 'w') or die("can't open file");
		fwrite($fh, $script);
		fclose($fh);
	}
}
?>