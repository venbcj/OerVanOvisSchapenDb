<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

include "just_connect_db.php";

$rasnr = $_GET['Id'];

$rapport = 'Rassen';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$lid_gateway = new LidGateway();
$lidId = $lid_gateway->findByRas($rasnr);
$reader = $lid_gateway->findReader($lidId);

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new RasPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$ras_gateway = new RasGateway();
$zoek_ras = $ras_gateway->zoek_ras($lidId);
while ($row = $zoek_ras->fetch_assoc()) {
    $ras = $row['ras'];
    $scan = $row['scan'];
    $border = 'T'; 
    $pdf->Cell(75,5,'','',0);
    if ($reader == 'Biocontrol') {
        $pdf->Cell(15,5,$veld,$border,0,'C');
    }
    $pdf->Cell(18,5,$ras,$border,1,''); 
}

$pdf->Output($rapport.".pdf","D");
