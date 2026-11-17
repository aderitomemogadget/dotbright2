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
	public function getrows(){return $db_rows;}
	public function db_open($db_link=0,$db_name='',$query='')
	{
		if(is_object($db_link)) $this->db_link=$db_link;
		$query=unti_sql_injection($query);
		if($this && $this->db_res!=0)$this->db_close();
		if($db_link!=0)$this->db_link=$db_link;
		if($db_name!='')$this->db_name=$db_name;
		if($query!='')$this->sSQLquery=$query;
		if(mysqli_select_db($this->db_link,$this->db_name))
		{
			mysqli_set_charset($this->db_link,'utf8');
			$this->db_res = mysqli_query($this->db_link,$this->sSQLquery);
			if($this->db_res)
			{
				//$this->db_data=mysqli_fetch_array($this->db_res,MYSQL_BOTH);
				$this->db_data=mysqli_fetch_array($this->db_res,3);//a constant MYSQL_BOTH deixou de existir
				$this->db_rows=mysqli_num_rows($this->db_res);
			}
		}
		$this->db_pos=0;
		return $this->db_res;
	}
	public function db_close($c=0)
	{
		if($this->db_res)mysqli_free_result($this->db_res);
		$this->db_res=0;
		if($c)db_closedb_conection($this->db_link);
	}
	public function db_next()
	{
		if(!$this->EOF())
		{
			//$this->db_data=mysqli_fetch_array($this->db_res,MYSQL_BOTH);
			$this->db_data=mysqli_fetch_array($this->db_res,3);
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
				if(mysqli_data_seek($this->db_res,$this->db_pos-1))
				{
					//$this->db_data=mysqli_fetch_array($this->db_res,MYSQL_BOTH);
					$this->db_data=mysqli_fetch_array($this->db_res,3);
					$this->db_pos--;
				}
		}
	}
	public function EOF()
	{
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
		mysqli_data_seek($this->db_res,0);
		//$this->db_data=mysqli_fetch_array($this->db_res,MYSQL_BOTH);
		$this->db_data=mysqli_fetch_array($this->db_res,3);
		$this->db_pos=0;
	}
	public function last()
	{
		mysqli_data_seek($this->db_res,$this->db_rows-1);
		//$this->db_data=mysqli_fetch_array($this->db_res,MYSQL_BOTH);
		$this->db_data=mysqli_fetch_array($this->db_res,3);
		$this->db_pos=$this->db_rows-1;
	}
	public function clear()
	{
		$this->db_data=array();
		$this->sSQLquery='';
		$this->db_rows='0';
	}
}
function unti_sql_injection($sql)
{
	$sql=str_replace('concat(','',$sql);
	$sql=str_replace('sleep(','',$sql);
	$sql=str_replace('SLEEP(','',$sql);
	$sql=str_replace('1=1--','',$sql);
	$sql=str_replace("'a'='a",'',$sql);
	$sql=str_replace('"a"="a','',$sql);
	return $sql;
}
function Runquery($db_link=0,$db_name='',$sql)
{
	global $db_last_insert_id;
	global $db_affected_rows,$admin_id;
	$db_last_insert_id=0;
	$db_affected_rows=0;
	$sql=unti_sql_injection($sql);
	$db_res=0;
	if(mysqli_select_db($db_link,$db_name))
	{
		mysqli_set_charset($db_link,'utf8');
		if($db_res=mysqli_query($db_link,$sql))
		{
			$db_last_insert_id=mysqli_insert_id($db_link);
			$db_affected_rows=mysqli_affected_rows($db_link);
		}
	}
	if($admin_id)save_log($sql,$admin_id);
	return $db_res;
}
function db_opendb_conection($sServer,$sDB_user,$sPassword)
{
	global $last_conection;
	if ( $last_conection !== null  )  return $last_conection;
	global $DB_name;
	if(!$DB_name2)$DB_name2=$DB_name;

	//$link=mysql_connect($sServer,$sDB_user,$sPassword);
	$link=mysqli_connect($sServer,$sDB_user,$sPassword);
	if(!$link)die('Could not connect: '.mysqli_error($link));
	return $link;
}
function db_closedb_conection($l){mysqli_close($l);}
function db_error($l=0){if($l!=0)return mysqli_error($l).': '.mysqli_error($l);return mysqli_error().': '.mysqli_error();}

function db_list_fields($db='',$T,$l=0)
{
	/*$colObj = mysqli_fetch_field_direct($f,$si);
	return $colObj;*/

	$sql = "SELECT * FROM $T";
	if ($result = mysqli_query($db , $sql)) {
		return $fieldinfo;
	}
	//return mysql_list_fields($db,$T,$l);
}
function db_field_name($f,$si){
	$colObj = mysqli_fetch_field_direct($f,$si);
	return $colObj->name;
	//return mysql_field_name($f,$si);
}
function Field_len($f,$si){
	$colObj = mysqli_fetch_field_direct($f,$si);
	return $colObj->length;
	//return mysql_field_len($f,$si);
}
function Field_type($f,$si){
	$colObj = mysqli_fetch_field_direct($f,$si);
	return $colObj->type;
	//return mysql_field_type($f,$si);
}
function db_num_fields($f){return mysqli_num_fields($f);}
function db_get_last_insert_id($l=0){return mysqli_insert_id($l);}
function db_real_escape_string($l){return unti_sql_injection($l);}
/*function db_real_escape_string($f,$l){return mysqli_real_escape_string($f,$l);}*/
function save_log($sql,$user)
{
	global $site_shell_url;
	file_put_contents($site_shell_url.'files/bk/log'.date("Ymd"), $user.";".$sql."\n\n", FILE_APPEND);
}


return unti_sql_injection($l);