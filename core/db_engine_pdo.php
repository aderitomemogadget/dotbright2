<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
class DataObj
{
	public $db_link;
	public $db_res;
	public $db_name;
	public $db_data;
	public $query;
	public $db_pos;
	public $db_rows;
	public $db_params;
	public function getrows(){return $db_rows;}
	public function db_open($db_link=0,$db_name='',$query='')
	{
		/*$key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
		//$fp = fopen('/var/www/lojas/videoasis/files/mysql_log.txt', 'a');
		$fp = fopen('/home/www/dev.memogadget.com/public_html/mhr/files/mysql_log.txt', 'a');
		fwrite( $fp, str_replace("\n","",$query.' --> '.debug_backtrace()[$key]['file'])."\n\n");
		fclose( $fp );*/

		if(is_object($db_link)) $this->db_link=$db_link;
		if($db_name!='')$this->db_name=$db_name;
		if($query!='')$this->sSQLquery=$query;
		// if(count_params($this->sSQLquery))
		if(is_array($this->db_params) && count($this->db_params) > 0)
		{
			//prepared queries
			$this->db_res=$this->db_link->prepare($query);
			foreach ($this->db_params as $key => $value)
			{
				db_bindParam($this->db_res,$key,$value);
			}
			$this->db_res->execute();
			if($this->db_res)
			{
				$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC);
				$this->db_rows=$this->db_res->rowCount();
			}
		}
		else
		{
			//older queries
			$query=unti_sql_injection($query);
			$this->db_res=$this->db_link->query($query);
			if($this->db_res)
			{
				$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC);
				$this->db_rows=$this->db_res->rowCount();
			}
		}
		$this->db_pos=0;
		return $this->db_res;
	}

	public function db_close($c=0)
	{
		/*$key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
		//$fp = fopen('/var/www/lojas/videoasis/files/mysql_log.txt', 'a');
		$fp = fopen('/home/www/dev.memogadget.com/public_html/mhr/files/mysql_log.txt', 'a');

		fwrite( $fp, str_replace("\n","","close --> ".debug_backtrace()[$key]['file'])."\n\n");
		fclose( $fp );*/


		if($this->db_res) $this->db_res->closeCursor();
		$this->db_res=0;
		$this->db_rows=0;
		if($c)\db_closedb_conection($this->db_link);
	}
	public function db_next()
	{
		if(!$this->EOF())
		{
			$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC);
			$this->db_pos++;
		}
	}
	public function db_prev()
	{
		//switch is faster than if
		switch ($this->db_pos)
		{
			case 0:
				$this->db_pos--;
				break;
			default:
				$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_PRIOR);
				$this->db_pos--;
		}
	}
	public function EOF()
	{
		if($this->db_rows==0)return 1;
		switch ($this->db_pos)
		{
			case $this->db_rows:
				return 1;
				break;
			default:
				return 0;
		}
	}
	public function BOF()
	{
		if($this->db_pos<0)return 1;
		else return 0;
	}
	public function first()
	{
		$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_FIRST);
		$this->db_pos=0;
	}
	public function last()
	{
		$this->db_data=$this->db_res->fetch(PDO::FETCH_ASSOC,PDO::FETCH_ORI_LAST);
		$this->db_pos=$this->db_rows-1;
	}
	public function clear()
	{
		$this->db_data=array();
		$this->sSQLquery='';
		$this->db_rows='0';
	}
	public function bind_params($params)
	{
		$this->db_params=$params;
	}
}
function beginTransaction($db_link)
{
	return $db_link->beginTransaction();
}
function endTransaction($db_link)
{
	return $db_link->commit();
}
function cancelTransaction($db_link)
{
	return $db_link->rollBack();
}
function db_bindParam($db_res,$param, $value, $type=null)
{
	if (is_null($type))
	{
		switch (true)
		{
			case is_int($value):
				$type=PDO::PARAM_INT;
				break;
			case is_bool($value):
				$type=PDO::PARAM_BOOL;
				break;
			case is_null($value):
				$type=PDO::PARAM_NULL;
				break;
			default:
				$type=PDO::PARAM_STR;
		}
	}
	// echo 'xx'.$param.', '.$value.'<br>';
	$db_res->bindValue($param, $value, $type);
}
function count_params($query)
{
	preg_match_all('/\s*\w+\s*[=<>]\s*\:(\w+)/', $query, $matches);
	return count($matches[1]);
}
function unti_sql_injection($sql)
{
	// if(substr_count($sql,'concat'))die();
//	$sql=strip_tags($sql);
	$sql=str_replace('sleep(','',$sql);
	$sql=str_replace('concat(','',$sql);
	$sql=str_replace('1=1--','',$sql);
	$sql=str_replace("'a'='a",'',$sql);
	$sql=str_replace('"a"="a','',$sql);
	//$sql=str_replace('--','',$sql);
	return $sql;
}
function interpolateQuery($query, $params)
{
	$keys=array();
	$values=$params;

	# build a regular expression for each parameter
	foreach ($params as $key => $value) {
		if (is_string($key)) {
			$keys[]='/:'.$key.'/';
		} else {
			$keys[]='/[?]/';
		}

		if (is_array($value))
			$values[$key]=implode(',', $value);

		if (is_null($value))
			$values[$key]='NULL';
	}
	// Walk the array to see if we can add single-quotes to strings
	array_walk($values, function (&$v, $k) {
		if (!is_numeric($v) && $v != "NULL") {
			$v = "'".$v."'";
		}
	});

	$query=preg_replace($keys, $values, $query, 1, $count);

	return $query;
}
function sql($params=array(), $tabela='', $tipo='I', $params_where=array())
{
	$fields=''; $values=''; $query=''; $where='';
	if($tipo == 'I')
	{
		$a=0; foreach($params as $key => $value){ if($a > 0){ $fields .= ', '; $values .= ', ';} $fields .= ltrim($key, ':'); $values .= $key; $a++;}
		$query='INSERT INTO '.$tabela.'('.$fields.')VALUES('.$values.')';
	}
	elseif($tipo == 'U')
	{
		$a=0; foreach($params as $key => $value){ if($a > 0){ $fields .= ', ';} $fields .= ltrim($key, ':').'='.$key; $a++;}
		$a=0; foreach($params_where as $key => $value){ if($a > 0){ $where .= ' AND ';} $where .= ltrim($key, ':').'='.$key; $a++;}
		$query='UPDATE '.$tabela.' SET '.$fields.' WHERE '.$where.'';
	}
	return $query;
}
function flatten_sql($array=array())
{
	$return=array();
	array_walk_recursive($array, function($a) use (&$return) { $return[]=$a; });
	return $return;
}
function Runquery($db_link=0,$db_name='',$sql, $db_params=array())
{
	/*$fp = fopen('/var/www/lojas/videoasis/mysql_log.txt', 'a');
	fwrite( $fp, str_replace("\n","",$query)."\n\n");
	fclose( $fp );*/


	global $db_last_insert_id;
	global $db_affected_rows,$admin_id,$ss_token;
	$db_last_insert_id=0;
	$db_affected_rows=0;
	$db_res=0;
	if(!empty($db_params))
	{
		// prepared queries
		$db_prep=$db_link->prepare($sql);

		// print_r($db_prep);
		foreach ($db_params as $key => $value)
		{
			// echo $key.','.$value.'<br>';
			db_bindParam($db_prep,$key,$value);
		}
		$db_res=$db_prep->execute();


		if($db_res)
		{
			$db_last_insert_id=$db_link->lastInsertId();
			$db_affected_rows=$db_prep->rowCount();
		}

	}
	else
	{
		//older queries
		$query=\unti_sql_injection($sql);
		if($db_res=$db_link->query($query))
		{
			$db_last_insert_id=$db_link->lastInsertId();
			$db_affected_rows=$db_res->rowCount();
		}
	}
	if($admin_id)save_log($sql,$admin_id);
	return $db_res;
}
function db_opendb_conection($sServer, $sDB_user, $sPassword, $DB_name2='')
{
	/*$key = array_search(__FUNCTION__, array_column(debug_backtrace(), 'function'));
	$fp = fopen('/var/www/lojas/mhr/files/mysql_log.txt', 'a');
	fwrite( $fp, str_replace("\n","","dbopen --> ".debug_backtrace()[$key]['file'])."\n\n");
	fclose( $fp );*/

	global $last_conection;
	if ( $last_conection !== null  )  return $last_conection;

	global $DB_name;
	if(!$DB_name2)$DB_name2=$DB_name;
	$dsn='mysql:host='.$sServer.';dbname='.$DB_name2.';port=3306;charset=utf8';

	if(isset($GLOBALS['persist']) && $GLOBALS['persist']==1)
	{
			$options=array(
			PDO::ATTR_PERSISTENT    => true,
			PDO::ATTR_ERRMODE       => PDO::ERRMODE_WARNING,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
	}
	else
	{
		$options=array(
			PDO::ATTR_ERRMODE       => PDO::ERRMODE_WARNING,
			PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'utf8'");
	}
	try
	{
		$sageManaged = getenv('DOTBRIGHT_SAGE_MANAGED') === '1';
		if ($sageManaged) $options[PDO::ATTR_ERRMODE] = PDO::ERRMODE_EXCEPTION;
		$db_link=new PDO($dsn, $sDB_user, $sPassword, $options);
		if ($sageManaged) $last_conection = $db_link;
		global $conecoes;
		$conecoes=$db_link;
		return $db_link;
		//return new PDO($dsn, $sDB_user, $sPassword, $options);
	}
	catch(PDOException $e)
	{
		if (getenv('DOTBRIGHT_SAGE_MANAGED') === '1') throw $e;
		die('Could not connect:'.$e->getMessage());
	}
}
function db_closedb_conection($l){$l=null;}
function db_error($l=0){if($l!=0)return $l->errorCode().':'.print_r($l->errorInfo(), true);}
function db_list_fields($db='',$T,$l=0){
	global $rs_query;
	$rs_query=new Dataobj;
	$sql='select * from '.$T.' LIMIT 0';
	$rs_query->db_open($l, $db, $sql);
	return $rs_query->db_data;
}
function db_field_name($f,$si){
	global $rs_query;
	$meta=$rs_query->db_res->getColumnMeta($si);
	return $meta['name'];
}
function Field_len($f,$si){
	global $rs_query;
	$meta=$rs_query->db_res->getColumnMeta($si);
	return $meta['len'];
}
function Field_type($f,$si){
	global $rs_query;
	$meta=$rs_query->db_res->getColumnMeta($si);
	return $meta['native_type'];
}
function db_num_fields($f){
	global $rs_query;
	return $rs_query->db_res->columnCount();

}
function save_log($sql,$user)
{
	global $site_shell_url;
	file_put_contents($site_shell_url.'files/bk/log'.date("Ymd"), $user.";".$sql."\n\n", FILE_APPEND);
}
function db_get_last_insert_id($l=0){return $l->lastInsertId();}
function db_real_escape_string($l){return unti_sql_injection($l);}
