<?php

$data = $_GET['data'];

$imagePath = 'C:/Users/jhoss/Pictures/qr/mi_receta_qr.png';
file_put_contents($imagePath, $img_str);

header('Content-Description: File Transfer');
header('Content-Type: application/octet-stream');
header('Content-Disposition: attachment; filename="mi_receta_qr.png"');
header('Expires: 0');
header('Cache-Control: must-revalidate');
header('Pragma: public');
header('Content-Length: ' . filesize($imagePath));
readfile($imagePath);
exit;
?>
