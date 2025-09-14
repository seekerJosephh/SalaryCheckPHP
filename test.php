<?php
require_once '../vendor/autoload.php';
use Mpdf\Mpdf;
$mpdf = new Mpdf();
$mpdf->WriteHTML('<h1>សួស្តី</h1>');
$mpdf->Output('test.pdf', 'D');
exit;