<?php
 
require_once('library/fpdf/fpdf.php');

$pdf = new FPDF('P', 'cm', 'A4');

$pdf->AddPage();

$pdf->SetFont('Arial', 'B', 16);
$pdf->Text(5, 5, 'dsfdsfdsfsfs');

$pdf->Output('test.pdf', 'D');

?>
