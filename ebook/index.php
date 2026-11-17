<?php

$url='http://dev.memogadget.com/tecnometal/ebook/';
$url2='http://dev.memogadget.com/tecnometal/files/media/';
$url3=dirname( __FILE__ ) . '/../files/media/';

?><!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <title>TECNOMETAL</title>
  <meta name="viewport" content="width=device-width,initial-scale=1.0">
  <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:400,600,700" rel="stylesheet">
  <link href="<?=$url?>pflip/css/pdfflip.css" rel="stylesheet" type="text/css">
</head>
<body>
<?php
$fname=$_GET['fname'];
if(!substr_count($fname,'.pdf2'))$fname='';
else
{
	if(!file_exists($url3.$fname))
	{
		$fname2=str_replace('.pdf2','.pdf',$fname);
		copy($url3.$fname2,$url3.$fname);
	}
}

?>
<div class="PDFFlip" id="PDFF" source="<?=$url2?><?=$fname?>"></div>

<script src="<?=$url?>pflip/js/libs/jquery.min.js" type="text/javascript"></script>
<script src="<?=$url?>pflip/js/pdfflip.js" type="text/javascript"></script>
<script src="<?=$url?>settings.js" type="text/javascript"></script>
<script src="<?=$url?>toc.js" type="text/javascript"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js"></script>
<script>
	if( !window.jQuery ) document.write('<script src="<?=$url?>js/jquery-3.0.0.min.js"><\/script>');
</script>

</body>
</html>