https://sourceforge.net/projects/tcpdf/files/
Main source: https://github.com/tecnickcom/TCPDF

<?php

require_once ("tcpdf_barcodes_2d.php");

$code = "hello";
$type = "PDF417";

$barcodeobj = new TCPDF2DBarcode($code, $type);

$barcodeobj->getBarcodePNG();

?>