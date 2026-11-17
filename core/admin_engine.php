<?php
	if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
	function edit_content($page_id=0,$txt="",$page=0)
	{
		if($page)echo '<a class="edit" href="admin/pages/pagina.php?ID='.$page_id.'" target="_blank">EDIT PAGE</a>';
		put_content($txt,1);
	}
	include $path.'linguas.admin.php';
	function write_lang($str,$str2='')
	{
		global $site_lang,$key_linguas,$lingua_admin;
		$key_file=$key_linguas[$str][$lingua_admin];
		if($key_file=='')
		{
			$key_file=$str2;
		}
		return $key_file;
	}
	function search_put($searchfor='',$str2='')
	{
		$file=$GLOBALS['site_shell_url'].'files/linguas.admin.txt';
		$fh=fopen($file, 'a') or die("can't open file");
		header('Content-Type: text/plain');
		$contents=file_get_contents($file);
		$pattern=preg_quote($searchfor, '/');
		$pattern="/^.*$pattern.*\$/m";
		if(!preg_match_all($pattern, $contents, $matches))
		{
			$var="\t\t'".$searchfor."' => array\n\t\t\t\t\t\t\t(\n\t\t\t\t\t\t\t\t'pt' => '".$str2."',\n\t\t\t\t\t\t\t\t'en' => '".$str2."'\n\t\t\t\t\t\t\t),";
			fwrite($fh, $var."\n");
		}
		fclose($fh);
	}