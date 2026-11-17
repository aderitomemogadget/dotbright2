<?php
if(isset($_SERVER['SCRIPT_FILENAME']) && $_SERVER['SCRIPT_FILENAME'] == __FILE__ ) exit();
if(strpos($_SERVER['SCRIPT_NAME'], '/admin/') === false)
{
	global $site_http_url;
	function strip_utf($string)
	{
	   preg_match_all('/%u([[:alnum:]]{4})/', $string, $a);
	   foreach ($a[1] as $uniord){$string=str_replace('%u'.$uniord, '',  $string);}
	   return $string;
	}

	if(!empty($_REQUEST))
	{
		foreach($_REQUEST as $key=>$val)
		{
			if($val && is_string($val))
			{
				if(!preg_match('/^[-_a-zA-Z0-9]+$/', $key))
				{
					echo 'erro1';
					echo '<meta http-equiv="refresh" content="0;url='.$site_http_url.'">';
					die('');
				}
				$myval=preg_replace('#<script[^>]*>.*?</script>#is', '', $val);
				$myval=strip_tags($myval);
				$myval=preg_replace('#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+? \(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is', '<${1}>',$myval);
				$myval=str_replace("<", "&lt;", $myval);
				$myval=str_replace(">", "&gt;", $myval);
				$myval=strip_utf($myval);
				$myval=str_replace(array("003C","003c"), "&lt;", $myval);
				$myval=str_replace(array("003E","003e"), "&gt;", $myval);
				$_REQUEST[$key]=$myval;
			}
		}

	}

	if(!empty($_POST))
	{
		foreach($_POST as $key=>$val)
		{
			if($key!='m_password' && $key!='m_password_repete')
			{
				if($val && is_string($val))
				{
					$myval=preg_replace('#<script[^>]*>.*?</script>#is', '', $val);
					if($myval)
					{
						$myval=strip_tags($myval);
						$myval=preg_replace('#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+? \(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is', '<${1}>',$myval);
						$myval=str_replace("<", "&lt;", $myval);
						$myval=str_replace(">", "&gt;", $myval);
						$myval=strip_utf($myval);
						$myval=str_replace(array("003C","003c"), "&lt;", $myval);
						$myval=str_replace(array("003E","003e"), "&gt;", $myval);
					}
					$_POST[$key]=$myval;
				}
			}
		}
	}
	if(!empty($_GET))
	{
		foreach($_GET as $key=>$val)
		{
			if($val && is_string($val))
			{
				$myval=preg_replace('#<script[^>]*>.*?</script>#is', '', $val);
				$myval=strip_tags($myval,'');
				$myval=preg_replace('#\s*<(/?\w+)\s+(?:on\w+\s*=\s*(["\'\s])?.+? \(\1?.+?\1?\);?\1?|style=["\'].+?["\'])\s*>#is', '<${1}>',$myval);
				$myval=str_replace("<", "&lt;", $myval);
				$myval=str_replace(">", "&gt;", $myval);
				$myval=strip_utf($myval);
				$myval=str_replace(array("003C","003c"), "&lt;", $myval);
				$myval=str_replace(array("003E","003e"), "&gt;", $myval);
				$_GET[$key]=$myval;
			}
		}
	}
}




if(!empty($_FILES))
{
	$max_upload = min(ini_get('post_max_size'), ini_get('upload_max_filesize'));
	$max_upload = str_replace('M', '', $max_upload);
	$max_upload = $max_upload * 1024*1000;

	$allowed_extension=array('jpg','JPG','png','PNG','gif','GIF','pdf','PDF','doc','DOC','docx','DOCX','csv','CSV','txt','TXT','xml','XML','xls','xlsx','XLS','XLSX','svg','SVG');
    foreach ($_FILES as $input => $infoArr)
    {
    	$file_name = $infoArr['name'];
    	if($infoArr['tmp_name'] != '' && !array($infoArr['size']))
    	{
	        $file_size = $infoArr['size'];
	        $file_tmp = $infoArr['tmp_name'];
	        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
    		$file_extension = strtolower($file_extension);
    		// var_dump($file_size);
    		// var_dump($max_upload);
    		if($file_size > $max_upload)
	        {
	            die("maxsize of ".($max_upload/1024)."MB on $file_name has reached");
	        }
	        if(!in_array($file_extension, $allowed_extension))
	        {
	            die("format $file_extension in image $file_name is not accepted");
	        }
        	// -> string, normal file input
    	}
    	else
    	{
    		if(!empty($infoArr['size']))
    		{
	    		foreach ($infoArr['size'] as $key => $value)
	    		{
	    			$file_name = $infoArr['name'][$key];
	    			if($infoArr['tmp_name'][$key] != '')
	    			{
	    				$file_size = $infoArr['size'][$key];
				        $file_tmp = $infoArr['tmp_name'][$key];
				        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
			    		$file_extension = strtolower($file_extension);
			    		// var_dump($file_size);
			    		// var_dump($max_upload);
			    		if($file_size > $max_upload)
				        {
				            die("maxsize of ".($max_upload/1024)."MB on $file_name has reached");
				        }
				        if(!in_array($file_extension, $allowed_extension))
				        {
				            die("format $file_extension in image $file_name is not accepted");
				        }
			        	// -> string, normal file input
	    			}
	    		}
    		}
    	}
    }
}


function copia_segura($image, $dest)
{
	if(!$image)return false;
	if(!$dest)return false;
	
	//Vamos verificar qual o tipo de ficheiro:
	$original_extension=pathinfo($dest, PATHINFO_EXTENSION);

	if(!$original_extension)return false;

	$dest2=str_replace('.'.$original_extension, '', $dest);
	if($original_extension == 'jpg'){ return copy($image, $dest2.'.jpg'); }
	if($original_extension == 'JPG'){ return copy($image, $dest2.'.JPG'); }
	if($original_extension == 'jpeg'){ return copy($image, $dest2.'.jpeg'); }
	if($original_extension == 'JPEG'){ return copy($image, $dest2.'.JPEG'); }
	if($original_extension == 'png'){ return copy($image, $dest2.'.png'); }
	if($original_extension == 'PNG'){ return copy($image, $dest2.'.PNG'); }
	if($original_extension == 'gif'){ return copy($image, $dest2.'.gif'); }
	if($original_extension == 'GIF'){ return copy($image, $dest2.'.GIF'); }
	if($original_extension == 'pdf'){ return copy($image, $dest2.'.pdf'); }
	if($original_extension == 'PDF'){ return copy($image, $dest2.'.PDF'); }
	if($original_extension == 'doc'){ return copy($image, $dest2.'.doc'); }
	if($original_extension == 'DOC'){ return copy($image, $dest2.'.DOC'); }
	if($original_extension == 'docx'){ return copy($image, $dest2.'.docx'); }
	if($original_extension == 'DOCX'){ return copy($image, $dest2.'.DOCX'); }
	if($original_extension == 'csv'){ return copy($image, $dest2.'.csv'); }
	if($original_extension == 'CSV'){ return copy($image, $dest2.'.CSV'); }
	if($original_extension == 'txt'){ return copy($image, $dest2.'.txt'); }
	if($original_extension == 'TXT'){ return copy($image, $dest2.'.TXT'); }
	if($original_extension == 'xml'){ return copy($image, $dest2.'.xml'); }
	if($original_extension == 'XML'){ return copy($image, $dest2.'.XML'); }
	if($original_extension == 'xls'){ return copy($image, $dest2.'.xls'); }
	if($original_extension == 'xlsx'){ return copy($image, $dest2.'.xlsx'); }
	if($original_extension == 'XLS'){ return copy($image, $dest2.'.XLS'); }
	if($original_extension == 'XLSX'){ return copy($image, $dest2.'.XLSX'); }
	if($original_extension == 'svg'){ return copy($image, $dest2.'.svg'); }
	if($original_extension == 'SVG'){ return copy($image, $dest2.'.SVG'); }
	if($original_extension == 'webp'){ return copy($image, $dest2.'.webp'); }
	if($original_extension == 'HEIC'){ return copy($image, $dest2.'.HEIC'); }
	if($original_extension == 'heic'){ return copy($image, $dest2.'.heic'); }
	if($original_extension == 'heif'){ return copy($image, $dest2.'.heif'); }
	if($original_extension == 'HEIF'){ return copy($image, $dest2.'.HEIF'); }
	if($original_extension == 'PSD'){ return copy($image, $dest2.'.PSD'); }
	if($original_extension == 'psd'){ return copy($image, $dest2.'.psd'); }
	if($original_extension == 'cdr'){ return copy($image, $dest2.'.cdr'); }
	if($original_extension == 'CDR'){ return copy($image, $dest2.'.CDR'); }
	if($original_extension == 'ai'){ return copy($image, $dest2.'.ai'); }
	if($original_extension == 'AI'){ return copy($image, $dest2.'.AI'); }

	return false;
}
