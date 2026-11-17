<?php
//die();

	date_default_timezone_set('Greenwich');
	echo getcwd();
	echo "<br>\n";
	echo 'display_errors = '.ini_get('display_errors');echo "<br>\n";
	echo 'register_globals = '.ini_get('register_globals');echo "<br>\n";
	echo 'post_max_size = '.ini_get('post_max_size');echo "<br>\n";
	echo 'safe_mode = '.ini_get('safe_mode');echo "<br>\n";
	echo 'register_argc_argv = '.ini_get('register_argc_argv');echo "<br>\n";
	echo 'disable_functions = '.ini_get('disable_functions');echo "<br>\n";
	echo 'track_vars = '.ini_get('track_vars');echo "<br>\n";
	echo 'file_uploads = '.ini_get('file_uploads');echo "<br>\n";
	echo 'upload_max_filesize = '.ini_get('upload_max_filesize');echo "<br>\n";
	echo "date = ".date("Y-m-d H:i:s");
	echo phpinfo();
?>