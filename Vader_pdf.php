<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

include "just_connect_db.php";

$stal = $_GET['Id'];

$rapport = 'Dekrammen';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$stal_gateway = new StalGateway();
$lid_gateway = new LidGateway();
$schaap_gateway = new SchaapGateway();

$lidId = $stal_gateway->findLidByStal($stal);

$Karwerk = $lid_gateway->zoek_karwerk($lidId);

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new VaderPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$zoek_dekram = $schaap_gateway->zoek_staldetails($lidId, $Karwerk);
while ($row = mysqli_fetch_assoc($zoek_dekram)) {
    $stalId = $row['stalId'];
    $werknr = $row['werknr'];
    $halsnr = $row['halsnr'];
    $scan = $row['scan'];
    $levnr_new = '';
    //$vandaag = date('Y-m-d');
    $border = 'T'; 

    $pdf->Cell(68,5,'','',0);
    $pdf->Cell(15,5,$scan,$border,0,'C');
    $pdf->Cell(18,5,$werknr,$border,0,'C'); 
    $pdf->Cell(15,5,$halsnr,$border,1,'C');

} // Einde while $zoek_schaap
$pdf->Output($rapport.".pdf","D");
