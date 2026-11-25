<?php

require_once '../../external_api/pdf_reader/PDFreader.class.php';

$PDF = new PDFreader();
try {
    $PDF->open($_GET['pdf']);
     $text = $PDF->readText();
}
catch(PDFexception $e) {
    echo '<p style="color: #FF0000; font-weight: bold; text-align: center;">';
    echo "$e</p>\n";
}

echo "<h2>Decoded text</h2>
<p>\n";
foreach ($text as $row) {
    echo "$row<br />\n";
}
echo "</p>\n";
?>