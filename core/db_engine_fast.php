<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
class DataObj
{
	var $db_link,$db_res,$db_name,$db_data,$query,$db_pos,$db_rows;
	function getrows(){return $db_rows;}
	function db_open($db_link=0,$db_name='',$query='')
	{
		if(substr_count($query,'concat'))return 0;
		if($this && $this->db_res!=0)$this->db_close();
		if($db_link!=0)$this->db_link=$db_link;
		if($db_name!='')$this->db_name=$db_name;
		if($query!='')$this->sSQLquery=$query;
		if(mysql_select_db($this->db_name,$this->db_link)){
			mysql_set_charset('utf8');
			$this->db_res = mysql_query($this->sSQLquery, $this->db_link);
			if($this->db_res)
			{
				$this->db_data=mysql_fetch_array($this->db_res,MYSQL_ASSOC);
				$this->db_rows=mysql_num_rows($this->db_res);
			}
		}
		$this->db_pos=0;
		return $this->db_res;
	}
	function db_close($c=0)
	{
		if($this->db_res)mysql_free_result($this->db_res);
		$this->db_res=0;
		if($c)db_closedb_conection($this->db_link);
	}
	function db_next()
	{
		if(!$this->EOF())
		{
			$this->db_data=mysql_fetch_array($this->db_res,MYSQL_ASSOC);
			$this->db_pos++;
		}
	}
	function db_prev()
	{
		//switch is faster than if
		switch ($this->db_pos)
		{
			case 0:
        		$this->db_pos--;
        		break;
			default:
				if(mysql_data_seek($this->db_res,$this->db_pos-1))
				{
					$this->db_data=mysql_fetch_array($this->db_res,MYSQL_ASSOC);
					$this->db_pos--;
				}
		}
	}
	function EOF()
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
	function first()
	{
		mysql_data_seek($this->db_res,0);
		$this->db_data=mysql_fetch_array($this->db_res,MYSQL_ASSOC);
		$this->db_pos=0;
	}
	function last()
	{
		mysql_data_seek($this->db_res,$this->db_rows-1);
		$this->db_data=mysql_fetch_array($this->db_res,MYSQL_ASSOC);
		$this->db_pos=$this->db_rows-1;
	}
	function clear()
	{
		$this->db_data=array();
		$this->sSQLquery='';
		$this->db_rows='0';
	}
}
function Runquery($db_link=0,$db_name='',$sql)
{
	global $db_last_insert_id;
	global $db_affected_rows;
	$db_last_insert_id=0;
	$db_affected_rows=0;
	$db_res=0;
	if(mysql_select_db($db_name,$db_link))
	{
		mysql_set_charset('utf8');
		if($db_res=mysql_query($sql,$db_link))
		{
			$db_last_insert_id=mysql_insert_id($db_link);
			$db_affected_rows=mysql_affected_rows($db_link);
		}
	}
	return $db_res;
}
function db_opendb_conection($sServer,$sDB_user,$sPassword)
{
	$link=mysql_connect($sServer,$sDB_user,$sPassword);
	if(!$link)die('Could not connect: '.mysql_error());
	return $link;
}
function db_closedb_conection($l){mysql_close($l);}
function db_error($l=0){if($l!=0)return mysql_errno($l).': '.mysql_error($l);return mysql_errno().': '.mysql_error();}
function db_list_fields($db='',$T,$l=0){return mysql_list_fields($db,$T,$l);}
function db_field_name($f,$si){return mysql_field_name($f,$si);}
function Field_len($f,$si){return mysql_field_len($f,$si);}
function Field_type($f,$si){return mysql_field_type($f,$si);}
function db_num_fields($f){return mysql_num_fields($f);}
function db_get_last_insert_id($l=0){return mysql_insert_id($l);}
