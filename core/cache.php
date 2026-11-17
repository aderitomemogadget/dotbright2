<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();

function get_cache($f_original='')
{
	$return='';
	$time_expire=time() - 1*60*1; // Expire Time (1 minute)
	if(stream_resolve_include_path($f_original) && filectime($f_original) > $time_expire )
	{
		$myFile=$f_original;
		$data_change=filemtime(SITE_SHELL_URL.$f_original);
		$return='<script type="text/javascript" src="'.str_replace('.js', '.js?dp='.$data_change, $myFile).'"></script>';
	}
	return $return;
}
function gera_cache($f_original='', $f_novo='', $content='')
{
	$return='';
	if($content != '')
	{
		$time_expire=time() - 1*60*1; // Expire Time (1 minute)
		if(isset($GLOBALS['gerar']) && $GLOBALS['gerar']!=1 && stream_resolve_include_path($f_original) && filectime($f_original) > $time_expire && !isset($_GET['site_country'])) $myFile=$f_original;
		else
		{
			$content=str_replace(' : ',':',$content);
			$file_contents="\xEF\xBB\xBF".$content;
			$myFile=$f_original;
			$fh=fopen(SITE_SHELL_URL.$myFile, 'w');
			if ($fh===false) {
				// error reading or opening file
				return '<script>'.$content.'</script>';
			}
			else
			{
				if (fwrite($fh,$file_contents) === FALSE)
				{
                   //echo 'Cannot write to file ('.$myFile.')';
                }
				fclose($fh);
			}
		}
		$data_change=filemtime(SITE_SHELL_URL.$f_original);
		$return='<script type="text/javascript" src="'.str_replace('.js', '.js?dp='.$data_change, $myFile).'"></script>';
	}
	else
	{
		if(!$f_novo)return;
		if(stream_resolve_include_path($GLOBALS['site_shell_url'].$f_original)) $original=date('Y-m-d H:i:s',filemtime($GLOBALS['site_shell_url'].$f_original));
		if(stream_resolve_include_path($GLOBALS['site_shell_url'].$f_novo)) $nova=date('Y-m-d H:i:s',filemtime($GLOBALS['site_shell_url'].$f_novo));
		if((!stream_resolve_include_path($GLOBALS['site_shell_url'].$f_novo) || $nova < $original) && stream_resolve_include_path($GLOBALS['site_shell_url'].$f_original))
		{
			$data_change=filemtime(SITE_SHELL_URL.$f_original);
			$file_contents=minimize_code(file_get_contents($f_original));
			if($file_contents != '')
			{
				$myFile=$f_novo;
				$fh=fopen($myFile, 'w');
				if ($fh===false) {
					// error reading or opening file
					return '<link rel="stylesheet" href="layout/'.PROJETO.'/css/template.css?dp='.$data_change.'" type="text/css" media="all" />';
				}
				else
				{
					fwrite($fh, $file_contents);
					fclose($fh);
				}
			}
		}
	}
	return $return;
}
function minimize_code($input)
{
    // Remove comments
    $output=preg_replace('#/\*.*?\*/#s', '', $input);
    // Remove whitespace
    $output=preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
    // Remove trailing whitespace at the start
    $output=preg_replace('/\s\s+(.*)/', '$1', $output);
    // Remove unnecesairy ;'s
    $output=str_replace(';}', '}', $output);
    return $output;
}


function gera_cache_multiple($f_original=array(), $f_novo='', $type='all', $ext='')
{
	$file_contents=''; $force=0; $alterado=0;

	if(stream_resolve_include_path($GLOBALS['site_shell_url'].$f_novo)) $nova=date('Y-m-d H:i:s',filemtime($GLOBALS['site_shell_url'].$f_novo));

	foreach($f_original as $files)
	{
		if(stream_resolve_include_path($GLOBALS['site_shell_url'].$files)) $original=date('Y-m-d H:i:s',filemtime($GLOBALS['site_shell_url'].$files));

		// echo
		if((!stream_resolve_include_path($GLOBALS['site_shell_url'].$f_novo) || $nova < $original) && stream_resolve_include_path($GLOBALS['site_shell_url'].$files))
		{
			$alterado=1;
		}
	}

	// CASO TENHAM SIDO ALGUM DOS FICHEIROS ALTERADOS VAMOS GERAR OUTRA VEZ
	if($alterado == 1 || $force == 1)
	{
		foreach($f_original as $files)
		{
			if($ext == 'css') $file_contents .= minimize_code_css($GLOBALS['site_http_url'].$files);
			elseif($ext == 'js') $file_contents .= minimize_code_js(file_get_contents($GLOBALS['site_http_url'].$files));
			$file_contents .= "\n";
		}
		$myFile=$GLOBALS['site_shell_url'].$f_novo.'.'.$ext;
		$fh=fopen($myFile, 'w');
		fwrite($fh, $file_contents);
		fclose($fh);
	}

	// VAMOS VERIFICAR SE EXISTE O FICHEIRO CACHE NOS FILES
	$return='';
	if(stream_resolve_include_path($GLOBALS['site_shell_url'].$f_novo.'.'.$ext) && !substr_count($_SERVER['HTTP_HOST'], '192.168.1.202'))
	{
		if($ext == 'css') $return .= '<link rel="stylesheet" href="'.$f_novo.'_v_'.dumpcache.'.'.$ext.'" type="text/css" media="'.$type.'" /> ';
		elseif($ext == 'js') $return .= '<script type="text/javascript" src="'.$f_novo.'_v_'.dumpcache.'.'.$ext.'"></script>';
	}
	else
	{
		foreach($f_original as $files)
		{
			$files=str_replace('.'.$ext,'',$files);
			if($ext == 'css') $return .= '<link rel="stylesheet" href="'.$files.'_v_'.dumpcache.'.'.$ext.'" type="text/css" media="'.$type.'" />';
			elseif($ext == 'js') $return .= '<script type="text/javascript" src="'.$files.'_v_'.dumpcache.'.'.$ext.'"></script>';
		}
	}
	return $return;
}
function minimize_code_css($file_src, $pasta='layout')
{
	$input=file_get_contents($file_src);
	$file_path=str_replace(basename($file_src),'',$file_src);

	preg_match_all('~[(](.+?)[)]~', $input, $arrMatches);

	foreach($arrMatches[1] as $match)
	{
		if(substr_count(strtolower($match), '.jpg') || substr_count(strtolower($match), '.png') || substr_count(strtolower($match), '.gif'))
		{
			$match=str_replace('"', '', $match);
			$match=str_replace("'", '', $match);

			//die($match);
			$input=str_replace('('.$match.')', '('.$file_path.$match.')', $input);

			//http://192.168.1.202/lojas/hello.pt/layout/hello/css/
			//../images/fb_login.png
			/*
			if(stream_resolve_include_path($GLOBALS['site_shell_url'].'layout/'.PROJETO.'/images/'.$match))
			{
				$path=$GLOBALS['site_http_url'].'layout/'.PROJETO.'/images/'.$match;
				$type=pathinfo($path, PATHINFO_EXTENSION);
				$data=file_get_contents($path);
				$base64='data:image/' . $type . ';base64,' . base64_encode($data);
				$input=str_replace('('.$match.')', '('.$base64 .')', $input);
				$input=str_replace('("'.$match.'")', '('.$base64 .')', $input);
				$input=str_replace('(\''.$match.'\')', '('.$base64 .')', $input);
			}
			elseif(stream_resolve_include_path($GLOBALS['site_shell_url'].'external_api/reveal/'.$match))
			{
				$path=$GLOBALS['site_http_url'].'external_api/reveal/'.$match;
				$type=pathinfo($path, PATHINFO_EXTENSION);
				$data=file_get_contents($path);
				$base64='data:image/' . $type . ';base64,' . base64_encode($data);
				$input=str_replace('('.$match.')', '('.$base64 .')', $input);
				$input=str_replace('("'.$match.'")', '('.$base64 .')', $input);
				$input=str_replace('(\''.$match.'\')', '('.$base64 .')', $input);
			}
			else
			{
				$input=str_replace('('.$match.')', '(../layout/'.PROJETO.'/images/'.$match.')', $input);
				$input=str_replace('("'.$match.'")', '(../layout/'.PROJETO.'/images/'.$match.')', $input);
				$input=str_replace('(\''.$match.'\')', '(../layout/'.PROJETO.'/images/'.$match.')', $input);
			}*/
		}
	}

	// Remove comments
    $output=preg_replace('#/\*.*?\*/#s', '', $input);
    // Remove whitespace
    $output=preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
    // Remove trailing whitespace at the start
    $output=preg_replace('/\s\s+(.*)/', '$1', $output);
    // Remove unnecesairy ;'s
    $output=str_replace(';}', '}', $output);
    return $output;
}
function minimize_code_js($input)
{
	// Remove comments
	// $pattern='/((?:\/\*(?:[^*]|(?:\*+[^*\/]))*\*+\/)|(?:\/\/.*))/';
    // $output=preg_replace($pattern, '', $input);
	// $output=preg_replace("/\/\*[\s\S]*?\*\//", '', $input);
	// $output=preg_replace("/\/\/.*$/", '', $output);
    // Remove whitespace
    // $output=preg_replace('/\s*([{}|:;,])\s+/', '$1', $output);
    // Remove trailing whitespace at the start
    // $output=preg_replace('/\s\s+(.*)/', '$1', $output);
    // Remove unnecesairy ;'s
    // $output=str_replace(';}', '}', $output);
    return $input;
}