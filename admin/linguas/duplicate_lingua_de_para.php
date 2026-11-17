<?php

die();

	include "settings.inc.php";

	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(-1);
	error_reporting(E_ERROR);


	// SEARCH FOR
	$from_lang  = 'pt';
	$to_lang  = 'es';


	global $DB_host,$DB_user,$DB_pass,$DB_name;

	$hostname = $DB_host;
	$database = $DB_name;
	$username = $DB_user;
	$password = $DB_pass;


	// Create connectio to DB
	$MJCONN = mysql_pconnect($hostname, $username, $password) or trigger_error(mysql_error(),E_USER_ERROR);
	mysql_select_db($database,$MJCONN);

	// Get list of tables
	$table_sql = 'SHOW TABLES';
	$table_q = mysql_query($table_sql,$MJCONN) or die("Cannot Query DB: ".mysql_error());
	$tables_r = mysql_fetch_assoc($table_q);
	$tables = array();


	do{
	    $tables[] = $tables_r['Tables_in_'.strtolower($database)];
	}while($tables_r = mysql_fetch_assoc($table_q));

	// LOOP THROUGH EACH TABLE
	foreach($tables as $table)
	{
		if(substr_count($table,'lang') > 0 || substr_count($table,'Lang') > 0)
		{
			//echo $table;
		    $sql = 'DROP TABLE IF EXISTS tmp';
			$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
			if(!$result) echo mysql_error().$sql."<br>";
			echo $sql."<br>";

			$sql = 'CREATE  TABLE tmp SELECT * FROM  '.$table.' WHERE bloco_lang ="'.$from_lang.'"';
			$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
			if(!$result)
			{
				$sql = 'CREATE  TABLE tmp SELECT * FROM  '.$table.' WHERE zone_lang ="'.$from_lang.'"';
				$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
				if(!$result)
				{
					$sql = 'CREATE  TABLE tmp SELECT * FROM  '.$table.' WHERE category_lang ="'.$from_lang.'"';
					$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
					if(!$result)
					{
						$sql = 'CREATE TABLE tmp SELECT * FROM  '.$table.' WHERE values_lang ="'.$from_lang.'"';
						$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
						if(!$result)
						{
							$sql = 'CREATE TABLE tmp SELECT * FROM  '.$table.' WHERE Lang ="'.$from_lang.'"';
							$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
							if(!$result)
							{
								$sql = 'CREATE TABLE tmp SELECT * FROM  '.$table.' WHERE lang ="'.$from_lang.'"';
								$result = mysql_query($sql,$MJCONN) or $error = mysql_error();
							}
						}
					}
				}
			}




			if($result)
			{

				$sql1 = 'DELETE FROM '.$table.' WHERE bloco_lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
				$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

				$sql2 = 'UPDATE tmp SET bloco_lang_id=NULL, bloco_lang ="'.$to_lang.'"';
				$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
				if(!$result)
				{
					$sql1 = 'DELETE FROM '.$table.' WHERE zone_lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
					$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

					$sql2 = 'UPDATE tmp SET zone_lang_id=NULL, zone_lang ="'.$to_lang.'"';
					$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
					if(!$result)
					{
						$sql1 = 'DELETE FROM '.$table.' WHERE category_lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
						$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

						$sql2 = 'UPDATE tmp SET category_lang_id=NULL, category_lang  ="'.$to_lang.'"';
						$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
						if(!$result)
						{
							$sql1 = 'DELETE FROM '.$table.' WHERE values_lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
							$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

							$sql2 = 'UPDATE tmp SET values_lang_id=NULL, values_lang   ="'.$to_lang.'"';
							$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
							if(!$result)
							{

								$sql1 = 'DELETE FROM '.$table.' WHERE lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
								$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

								$sql2 = 'UPDATE tmp SET bloco_img_lang_id=NULL, lang   ="'.$to_lang.'"';
								$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
								if(!$result)
								{
									$sql1 = 'DELETE FROM '.$table.' WHERE Lang ="'.$to_lang.'"';//Vamos remover a ligua que queremos substituir
									$result = mysql_query($sql1,$MJCONN) or $error = mysql_error();

									$sql2 = 'UPDATE tmp SET ID =NULL, Lang   ="'.$to_lang.'"';
									$result = mysql_query($sql2,$MJCONN) or $error = mysql_error();
									if(!$result)
									{
										echo "ERRO<br>";
									}
									else
									{
										$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
										$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
									}
								}
								else
								{
									$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
									$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
								}
							}
							else
							{
								$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
								$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
							}
						}
						else
						{
							$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
							$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
						}

					}
					else
					{
						$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
						$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
					}
				}
				else
				{
					$sql3 = 'INSERT INTO '.$table.' SELECT * FROM tmp';
					$result = mysql_query($sql3,$MJCONN) or $error = mysql_error();if(!$result) echo mysql_error().$sql3."<br>";
				}


			}
			echo $sql."<br>";
			echo $sql1."<br>";
			echo $sql2."<br>";
			echo $sql3."<br>";
			echo "<hr>";
		}
	}
	echo "fim";
?>