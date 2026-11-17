<?php
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////// CRON MASTER, ESTE CRON CONTROLA TODOS ///////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////////////////////////////////////////////

	$path = dirname( __FILE__ ) . '/../../core/';

	$path . 'core_files.php';
	require_once $path . 'core_files.php';

	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$db_link=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$rs=new DataObj;


	header('Content-Type: text/html; charset=utf-8');
	ini_set('max_execution_time','60000');
	ini_set('max_input_time','60000');
	ini_set('memory_limit','-1');

	//Vamos evitar lancar novos processos se o servidor estiver a morrer ....
	if(DB_host_loads()<5)
	{
		$sql="select * FROM cron_job WHERE Estado=1";
		$res=$rs->db_open($db_link,$DB_name,$sql);
		if(!$res)echo db_error($db_link).$sql;
		if(!$rs->EOF())
		{
			$tmp2="";

			$comando = $rs->db_data["Comando"];
			$Pid = $rs->db_data["Pid"];
			$Hora = $rs->db_data["Hora"];
			$Minuto = $rs->db_data["Minuto"];
			$Dia = $rs->db_data["Dia"];
			$Mes = $rs->db_data["Mes"];
			$DiaSemana = $rs->db_data["DiaSemana"];

			if($Pid=='')
			{
				$Pid=new Process($comando);
				update_queue($rs->db_data["ID"],$Pid);
			}
			else
			{
				echo get_status($Pid);
			}
		}
		$rs->db_close();
		sleep(3);
	}


//    /usr/bin/php5.6



	function DB_host_loads()
	{
		$uptime=(exec('uptime'));
		if($uptime){$cpu=substr($uptime,strpos($uptime,"average:")+strlen("average:"));$cpu=(float)substr($cpu,0,strpos($cpu,","));}
		return $cpu;
	}





class Process
{
    private $pid;
    private $command;
    public function __construct($cl=false)
	{
        if ($cl !=false)
		{
            $this->command=$cl;
            $this->runCom();
        }
    }
    private function runCom(){
        $command='nohup '.$this->command.' > /dev/null 2>&1 & echo $!';
        exec($command ,$op);
        $this->pid=(int)$op[0];
    }
    public function setPid($pid){$this->pid=$pid;}
    public function getPid(){return $this->pid;}
    public function status(){
        $command='ps -p '.$this->pid;
        exec($command,$op);
        if (!isset($op[1]))return false;
        else return true;
    }
    public function start(){
        if ($this->command !='')$this->runCom();
        else return true;
    }
    public function stop(){
        $command='kill '.$this->pid;
        exec($command);
        if ($this->status()==false)return true;
        else return false;
    }
}


function update_queue($sID="",$Pid=0)
{
	global $DB_host,$DB_user,$DB_pass,$DB_name;
	$dbh=db_opendb_conection($DB_host,$DB_user,$DB_pass);
	$res=Runquery($dbh,$DB_name,"UPDATE cron_job SET Pid=$Pid where ID='$sID'");
}

function get_status($Pid=0)
{
	$command='ps -p '.$Pid;
    exec($command,$op);
    if (!isset($op[1]))return false;
    else return true;
}
?>