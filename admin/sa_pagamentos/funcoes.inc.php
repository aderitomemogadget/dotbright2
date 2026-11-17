<?php
function submeteDados()
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$modulo_acao = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? 'update' : 'insere';
	$CMPS_MODULO_DADOS = $GLOBALS['CMPS_MODULO_DADOS'];

	if($modulo_acao=='insere')
	{
		$sql = 'INSERT INTO '.TBMODULO;
		$sql .= '(';
		$i = 0; foreach($CMPS_MODULO_DADOS as $campos){ if($campos != TBID){ if($i > 0){ $sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
		$sql .= ')VALUES(';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY PARA $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if($i>0){$sql .= ', ';}
				if(substr_count($campos,'_EXT'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					$sql .= '"'.$nome_ficheiro.'"';
				}
				elseif(substr_count($campos,'Password'))
				{

					$sql .= '"'.base64_encode($_POST[$campos]).'"';
				}
				else
				{
					$sql .= '"'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= '
				)
				';
	}
	elseif($modulo_acao=='update')
	{
		$sql = 'UPDATE '.TBMODULO.' SET ';
		$i = 0;
		foreach($CMPS_MODULO_DADOS as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
		{
			if($campos != TBID)
			{
				if(substr_count($campos,'_EXT'))
				{
					$nome_ficheiro = submete_ficheiro($campos, '');
					if($i > 0 && $nome_ficheiro){$sql .= ', ';}
					if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
				}
				elseif(substr_count($campos,'Password'))
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.base64_encode($_POST[$campos]).'"';
				}
				else
				{
					if($i > 0){$sql .= ', ';}
					$sql .= $campos.'="'.addslashes($_POST[$campos]).'"';
				}
				$i++;
			}
		}
		$sql .= 'WHERE '.TBID.'='.$_POST[TBID];
	}
	$res = Runquery($db_link, $DB_name, $sql);
	// echo '<pre style="color:#FFF">'.$sql.'</pre><br />';

	if($modulo_acao=='insere') $id_acao = db_get_last_insert_id($db_link);
	else $id_acao = $modulo_id;

	return array('resultado' => $res, 'acao' => $modulo_acao, 'id_acao' => $id_acao);
}
function submeteRenovacoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RENOCAOES'];

	// echo '<pre style="color:#FFF">';
	$query = '';

	foreach($_POST['RENOVACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];

		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRENOVACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRENOVACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRENOVACAOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRENOVACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRENOVACAOID)
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRENOVACAOID.'='.$field_id;
		}
		$res = Runquery($db_link, $DB_name, $sql);
	}
	// echo '</pre>';
}
function submeteAverbamentos($id_marca)
{
	// echo '<pre style="color:#FFF">';
	tabs_submete_av('ENDERECO_ID');
	tabs_submete_av('NOME_ID');
	tabs_submete_av('TRANSMISSAO_ID');
	tabs_submete_av('FUSAO_ID');
	tabs_submete_av('LICENSA_ID');
	// echo '</pre>';
}

function tabs_submete_av($tab)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_AVERBAMENTO'];
	// print_r($_POST[$tab]);
	foreach($_POST[$tab] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';
		// echo $modulo_acao.'<br />';
		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBAVERBAMENTOS;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBAVERBAMENTOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBAVERBAMENTOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBAVERBAMENTOS.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBAVERBAMENTOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBAVERBAMENTOID.'='.$field_id.' AND objeto_bloco="'.$field_bloco.'"';
		}
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteDeclaracoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_DECLARACAO'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['DECLARACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';
		// echo $modulo_acao.'<br />';
		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBDECLARACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBDECLARACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBDECLARACAOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBDECLARACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBDECLARACAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBDECLARACAOID.'='.$field_id;

		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteOposicoes($id_marca)
{
	echo '<pre style="color:#FFF">';
	tabs_submete_op('OPOSICAO_ID');
	tabs_submete_op('RESPOSTA_ID');
	echo '</pre>';
}

function tabs_submete_op($tab)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_OPOSICAO'];

	foreach($_POST[$tab] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBOPOSICOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBOPOSICAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID)
				{
					if($i>0){$sql .= ', ';}

					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBOPOSICOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBOPOSICAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBOPOSICAOID.'='.$field_id.' AND objeto_bloco="'.$field_bloco.'"';
		}
		// echo $sql.'<br />';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteReclamacoes($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECLAMACAO'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECLAMACAO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECLAMACOES;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECLAMACAOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECLAMACOES.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECLAMACAOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECLAMACAOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submeteRecursos($id_marca)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj;

	$modulo_id = (isset($_POST[TBID]) && $_POST[TBID] > 0) ? $_POST[TBID] : 0;
	$CMPS_MODULO = $GLOBALS['CMPS_RECURSOS'];
	// echo '<pre style="color:#FFF">';
	foreach($_POST['RECURSO_ID'] as $field_num2)
	{
		$codis = explode(';', $field_num2);
		$field_num = $codis[0];
		$field_id = $codis[1];
		$field_bloco = $codis[2];
		$modulo_acao = ($field_id > 0) ? 'update' : 'insere';

		if($modulo_acao=='insere')
		{
			$sql = 'INSERT INTO '.TBRECURSOS;
			$sql .= '(';
			$i=0; foreach($CMPS_MODULO as $campos){ if($campos != TBRECURSOID){ if($i>0){$sql .= ', ';} $sql .= $campos; $i++;} } // ARRAY DOS CAMPOS
			$sql .= ')VALUES(';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY PARA $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID)
				{
					if($i>0){$sql .= ', ';}
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						$sql .= '"'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						$sql .= '"'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						$sql .= '"'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						$sql .= '"'.$modulo_id.'"';
					}
					elseif(substr_count($campos,'objeto_bloco'))
					{
						$sql .= '"'.$field_bloco.'"';
					}
					else
					{
						$sql .= '"'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ')';
		}
		elseif($modulo_acao=='update')
		{
			$sql = 'UPDATE '.TBRECURSOS.' SET ';
			$i=0;
			foreach($CMPS_MODULO as $campos) // ARRAY DE CAMPOS E $_POST DOS CAMPOS
			{
				if($campos != TBRECURSOID && $campos != 'objeto_bloco')
				{
					if(substr_count($campos.$field_num,'_EXT'))
					{
						$nome_ficheiro = submete_ficheiro($field_bloco.'_'.$campos.$field_num, '');
						if($i>0 && $nome_ficheiro){$sql .= ', ';}
						if($nome_ficheiro) $sql .= $campos.'="'.$nome_ficheiro.'"';
					}
					elseif(substr_count($campos,'_REL'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$field_num.'"';
					}
					elseif(substr_count($campos,'objeto_nome'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.MODULO.'"';
					}
					elseif(substr_count($campos,'objeto_id'))
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.$modulo_id.'"';
					}
					else
					{
						if($i>0){$sql .= ', ';}
						$sql .= $campos.'="'.addslashes($_POST[$field_bloco.'_'.$campos.$field_num]).'"';
					}
					$i++;
				}
			}
			$sql .= ' WHERE '.TBRECURSOID.'='.$field_id;
		}
		// echo $sql.'<br />';
		// echo '</pre>';
		$res = Runquery($db_link, $DB_name, $sql);
	}
}

function submete_ficheiro($file_name, $nome_ficheiro_old)
{
	$img_path = '../../files/documentos/';
	$Img_peq["name"] = $_FILES[$file_name]['name'];
	$Img_peq["temp"] = $_FILES[$file_name]['tmp_name'];

	$nome_ficheiro = '';
	$nome_ficheiro = $nome_ficheiro_old;
	if($Img_peq["name"]!="")
	{
		$pos = strrpos($Img_peq['name'],".");
		$img_ext = substr($Img_peq['name'], $pos+1, strlen($Img_peq['name'])+1-$pos);
		if(file_exists($img_path.$nome_ficheiro_old) && !empty($nome_ficheiro_old))
		{
			unlink($img_path.$nome_ficheiro_old);
		}
		$token = date('YmdHis').bin2hex(openssl_random_pseudo_bytes(5));
		$nome_ficheiro = $token.".".$img_ext;

		copia_segura($Img_peq["temp"], $img_path.$nome_ficheiro);
	}

	return $nome_ficheiro;
}

function show_page_buttons()
{
	global $converter,$task_button,$ID,$ID_ORG,$Estado,$T;
	?>
	<div class="clearboth"></div>
	<div id="slidebottom">
    <table width="100%" border=0 cellspacing=0 cellpadding=0>
    <tr>
    	<td align="center" valign="middle" class="t1"><?php

				echo '<input class="btn btn-primary" type="submit" name="Submit" value="'.$task_button.'">';
				if($task_form=="insere" || $task_form=="apaga" || $task_form=="duplicar")
				{
					?><input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" /><?
				}
				if($task_form=="altera"){?><input class="btn btn-danger" type="button" name="Apagar" value="<?php echo write_lang('key_apagar','Apagar')?>" onClick="window.open('pagina_trata.php?ID=<?php echo $ID?>&task_form=apaga','_self')" /><? }
			?>
            <input class="btn btn-secondary" type="button" name="cancelar" value="<?php echo write_lang('key_cancelar','Cancelar')?>" onClick="window.open('list.php','_self')" />
		</td>
     </tr>
	</table>
	</div>
<?php
}
function show_fast_tools()
{
	global $ID, $admin_id, $BOTOES_L;
	?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">FERRAMENTAS</td>
	</tr>
	<?php
	if(isset($BOTOES_L) && is_array($BOTOES_L))
	{
		foreach($BOTOES_L as $key => $value)
		{
		?>
		<tr>
		<td class="preto" align="center"><input class="btn btn-primary" type="button" style="width:160px" value="<?php echo $key?>" onclick="window.open('<?php echo $value?>','_self')" /></td>
		</tr>
	<?php
		}
	}
	?>
	</table>
	<?php if(substr_count($_SERVER['SCRIPT_FILENAME'], 'pagina.php')){?>

	<table width="200px" border=0 cellspacing=0 cellpadding=0 class="p1">
    <tr>
	<td class="titulo2" align="center" height="20">DRAFT GERADO</td>
	</tr>
	<tr>
	<td class="branco" align="center" id="draftgen" height="25">Não foram gerados drafts</td>
	</tr>
	</table>
<?php }}
function show_page_tools($filter=0)
{
	global $tipos; ?>
	<script>
	function checkFields(){
		return true;
	}
	</script>
	<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr>
			<td width="5%" align="center">&nbsp;</td>
		    <td width="95%" valign="middle" style="font-weight:bold;color:#FFF"><?php echo MODULO?></td>
		</tr>
	</table>

<?php }
function show_page_bottom_tools()
{
?>
<table width="100%" height="5px" border=0 cellspacing=0 cellpadding=0><tr><td></td></tr></table>
<table width="100%" style="background:#FFF" border="0" cellspacing="0" cellpadding="0">
<tr>
<td width="150" height="35" align="right" style="font-weight:bold">Adicionar Pagamento</td>
<td height="35" width="135" align="center"><input type="text" style="width:100px;text-align:right;padding-right:5px" class="form-control" name="valor_conf" value="" /></td>
<td height="35" width="20" align="left" style="font-weight:bold"><img src="img/reload.png" onclick="$('#lista_paginas').submit();" style="cursor:pointer" /></td>
<td height="35" align="left" style="font-weight:bold">&nbsp;</td>
</tr>
</table>
<?php
}

function acoes_selecionados()
{
	if(isset($_POST['valor_conf']))
	{
		global $DB_host,$DB_user,$DB_pass,$DB_name;
		$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
		if($_POST['valor_conf'] > 0)
		{
			$VAL = $_POST['valor_conf'];
			$VAL = str_replace(',', '.', $VAL);

			$sql = 'INSERT INTO encomendas_produtos(Valor_Pagar, ID_Conf, Qtd) VALUES("'.$VAL.'", 1, 1)';
			$res = Runquery($db_link,$DB_name,$sql);
			if(!$res) echo db_error($db_link) . $sql . '<br>';
		}
	}
}
function show_lista_grupos($ID_Grupo,$tabela,$campo_name)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name,$admin_lang;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;
	$rs->db_open($db_link,$DB_name,'Select * from '.$tabela);
	echo '<select name="'.$campo_name.'" class="form-control">';
    while(!$rs->EOF())
    {
        echo '<option value="'.$rs->db_data['ID'].'"';
		if($ID_Grupo==$rs->db_data['ID']) echo ' selected ';
		echo '>';
		echo $rs->db_data['Descricao'].'</option>';
        $rs->db_next();
    }
    $rs->db_close(1);
	echo '</select>';
}
function show_content_list($pai=0,$contador=-5,$count=0,$md=0,$span=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link = db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs = new DataObj; $rs2 = new DataObj;
	// ?p=1&NUMERO=15154/2009&TIPO=figurativa&DESIGNACAO=IMOINVESTE&DATA_PEDIDO=2009-05-07

	$subsql = '(SELECT SUM(Valor_Pagar * Qtd) FROM '.TBMODULOLANG.' WHERE ISNULL(ID_Conf) AND Estado=7) AS total_pagar';
	$subsql2 = '(SELECT SUM(Valor_Pagar * Qtd) FROM '.TBMODULOLANG.' WHERE ID_Conf=1) AS total_retirar';


	$rs2->db_open($db_link, $DB_name, $sql);
	$total = $rs2->db_rows;
	$rs->db_close();

	$sql = '
			SELECT
				Ref, (Valor_Pagar * Qtd) as Valor_Pagar, Nome, Qtd, ID_Conf, Estado, '.$subsql.', '.$subsql2.'
			FROM
				 '.TBMODULOLANG.'
			WHERE
				(Estado=7 OR ID_Conf=1)
			ORDER BY
				'.TBMODULOLANG.'.ID DESC
			';
		/* $sql = '
			SELECT
				Ref, (Valor_Pagar * Qtd) as Valor_Pagar, Nome, Qtd, ID_Conf, Estado, '.$subsql.', '.$subsql2.'
			FROM
				 '.TBMODULOLANG.'
			ORDER BY
				'.TBMODULOLANG.'.ID DESC
			'; */

	$rs->db_open($db_link, $DB_name, $sql);
	$total = $rs->db_rows;
	$rs->db_close();

	$n_pags = ceil($total / N_PAGINAS);
	if(!isset($_GET['pagina'])) $pagina = 1;
	else $pagina = $_GET['pagina'];

	$inicio = $pagina - 1;
	$inicio = $inicio * N_PAGINAS;

	$sql2 = $sql.' Limit '.$inicio.', '.N_PAGINAS;
	$rs->db_open($db_link,$DB_name,$sql2);
	if(!$rs->EOF() || (isset($_GET['p']) && $_GET['p']==1))
	{
		?>

		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td valign="middle" align="left" height="50">&nbsp;</td>
		<td align="right" width="100" style="font-size:13px"><b>Valor Total:</b></td>
		<td valign="middle" align="left" style="font-size:13px">&nbsp;<?php echo numberformat($rs->db_data['total_pagar']);?></td>
		<td valign="middle"  width="100" align="right" style="font-size:13px"><b>Valor Pago:</b></td>
		<td valign="middle" align="left" style="font-size:13px">&nbsp;<?php echo numberformat($rs->db_data['total_retirar']);?></td>
		<td valign="middle"  width="100" align="right" style="font-size:13px"><b>Valor em Falta:</b></td>
		<td valign="middle" align="left" style="font-size:13px">&nbsp;<?php echo numberformat($rs->db_data['total_pagar'] - $rs->db_data['total_retirar']);?></td>
		<td valign="middle" align="left" style="font-size:13px"></td>
		</tr>
		</table>
		<table width="100%" height="50px" border=0 cellspacing=0 cellpadding=0 class="p1">
		<tr bgcolor="#FFFFFF">
		<td width="5%" height="25" align="center">&nbsp;</td>
		<td width="100" valign="middle"><b>Ref</b></td>
		<td valign="middle" align="left"><b>Produto</b></td>
		<td width="100" valign="middle" align="left"><b>Qtd</b></td>
		<td width="100" valign="middle" align="right"><b>Valor</b></td>
		<td width="100" valign="middle" align="right"><b>Estado</b></td>
		<td width="10" valign="middle">&nbsp;</td>
		<td width="10" valign="middle">&nbsp;</td></tr>

		<?php
		while(!$rs->EOF())
		{
			$count++;
			$id = $rs->db_data[TBID];
			$NUMERO       = $rs->db_data['Ref'];
			$nome         = $rs->db_data['Nome'];
			$Valor = $rs->db_data['Valor'];
			$Qtd = $rs->db_data['Qtd'];
			$m_estado = $rs->db_data['Estado'];
			$Valorp = $rs->db_data['Valor_Pagar'];
			$ID_Conf = $rs->db_data['ID_Conf'];

			if($ID_Conf<1)
			{
				if($cor=='#C8ECFA')	{$cor="#f0f3f5";$class="r1";$n="1";}
				else				{$cor="#C8ECFA";$class="r2";$n="2";}
			}
			else
			{
				if($cor=='#FFCC66')	{$cor="#FFFF99";$class="r1";$n="1";}
				else				{$cor="#FFCC66";$class="r2";$n="2";}
			}
			if(!$m_estado) $est = 'Pendente';
			if($m_estado==2) $est = 'Cancelada';
			if($m_estado==3) $est = 'Pagamento Aceite';
			if($m_estado==4) $est = 'Pagamento Cancelado';
			if($m_estado==5) $est = 'Pagamento Pendente';
			if($m_estado==6) $est = 'Em Expedição';
			if($m_estado==7) $est = 'Concluída';

			?>
			<tr bgcolor="<?php echo $cor;?>" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='<?php echo $cor;?>'">
			<td align="center">&nbsp;</td>
			<td valign="middle"><?php if($ID_Conf==1){ echo '-';}else{echo $NUMERO;}?></td>
			<td valign="middle" align="left"><? if($ID_Conf==1){ echo '<b>PAGAMENTO</b>';}else{if($nome){echo htmlentities_b(stripslashes($nome));} else echo "[Edit]"; }?></td>
			<td valign="middle" align="left"><?php echo $Qtd;?></td>
			<td valign="middle" align="right"><?php if($ID_Conf==1){ echo '<b>- ';} echo numberformat($Valorp); if($ID_Conf==1){ echo '</b>';}?></td>
			<td valign="middle" align="right"><?php if($ID_Conf==1){ echo '<b>PAGO</b>';}else{echo $est;}?></td>
			<td valign="middle" height="30">&nbsp;</td>
			<td valign="middle">&nbsp;</td>
			</tr>
			<?php
			$rs->db_next();
		}
		?>
		</table>
		<?php
		if($n_pags > 1)
		{
			$return = '<table width="100%" class="new_quadros" border=0 cellspacing=2 cellpadding=1><tr>';
			if($pagina > 6)
			{
				$pgn = 'onclick=window.open("list.php?ID='.$_GET['ID'].'&m_pesquisa='.urlencode($m_pesquisa).'&pagina=1","_self")';
				$return .= '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" >1... </td>';
			}
			for($pi = ($pagina - 6); $pi < ($pagina + 7); $pi++)
			{
				$pgn = 'onclick=window.open("list.php?ID='.$_GET['ID'].'&m_pesquisa='.urlencode($m_pesquisa).'&pagina='.$pi.'","_self")';
				$return .= ($pi >= 1 && $pi <= $n_pags) ? $pagina == $pi ? '<td width="2px">&nbsp;</td><td width="20" height="20" align="center" style="color:#FFF; font-weight:bold">'.sprintf(($pi)).'</td>' : '<td width="2px">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer">'.sprintf($pi).'</td>' :'';
			}
			if($n_pags!=$pagina)
			{
				$pgn='onclick=window.open("list.php?ID='.$_GET['ID'].'&&m_pesquisa='.urlencode($m_pesquisa).'&pagina='.$n_pags.'","_self")';
				$return .= '<td width="2px" style="">&nbsp;</td><td width="20" height="20" bgcolor="#edf4f9" align="center" '.$pgn.' style="cursor:pointer;padding-top:2px;border:#000 0px solid" > ...'.sprintf($n_pags).'</td>';
			}
			$return .= '<td>&nbsp;</td></tr></table>';
			echo $return;
		}
	}
	else
	{?>
		<table height="50px" border=0 cellspacing=0 cellpadding=0 class="r1">
		<tr bgcolor="#e8e7e7" onMouseOver="this.style.backgroundColor='#DBE7F1'" onMouseOut="this.style.backgroundColor='#e8e7e7'">
		<td colspan="8" align="center">Não Existem <?php echo MODULO?></td>
		</tr>
		</table>
	<?php
	}
	$rs->db_close();
}
?>