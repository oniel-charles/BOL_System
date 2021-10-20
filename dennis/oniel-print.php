<?php
require('fpdf.php');
$request = explode('/', trim($_SERVER['PATH_INFO'],'/'));
$pdf = new FPDF();
$pdf = new FPDF('L','mm',array(100,300));
$pdf->AddPage();
$pdf->SetFont('Arial','B',16);
$pdf->Cell(40,10,'Hello World!'.$request[0]);

$pdf->Output();
?>
