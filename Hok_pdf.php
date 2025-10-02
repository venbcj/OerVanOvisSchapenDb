<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

require_once "just_connect_db.php";

$hok_gateway = new HokGateway();
$rapport = 'Verblijven';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$lidId = $hok_gateway->lidIdByHokId($_GET['Id']);

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new HokPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$zoek_verblijf = $hok_gateway->zoek_verblijf($lidId);
while ($row = mysqli_fetch_assoc($zoek_verblijf)) {
    $hoknr = $row['hoknr'];
    $scan = $row['scan'];
    $border = 'T'; 
    $pdf->Cell(75,5,'','',0);
     $pdf->Cell(15,5,$scan,$border,0,'C');
     $pdf->Cell(18,5,$hoknr,$border,1,'C'); 
} // Einde while $zoek_schaap

$pdf->Output($rapport.".pdf","D");
