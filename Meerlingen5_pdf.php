<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url 
11-11-2019 kolomkop worp gewijzigd in worpgrootte */

include "just_connect_db.php";

$stal = $_GET['Id'];
$van = $_GET['d1'];
$tot = $_GET['d2'];

$rapport = 'Meerling in periode';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$stal_gateway = new StalGateway();
$lidId = $stal_gateway->findLidByStal($stal);

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new MeerlingenPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$Karwerk = 5;
/*$van = '2013-12-01';
$tot = '2018-12-01';*/
$schaap_gateway = new SchaapGateway();
$zoek_meerling = $schaap_gateway->zoek_meerling($lidId, $Karwerk, $van, $tot);
while ($row = $zoek_meerling->fetch_assoc()) {
    $lam = $row['lam'];
    $sek = $row['geslacht'];
    $worp = $row['worp'];
    $datum = $row['datum'];
    $gemkg = $row['gemgroei'];
    $maxdm = $row['kgdag'];
    $ooi = $row['ooi'];

if(!isset($gemkg)) { $gemkg = 'Onbekend'; }
    else { $gemkg .= ' gr ( tot '. $maxdm .')'; }

$border = 'T'; 

    $pdf->Cell(30,5,'','',0);
     $pdf->Cell(20,5,$lam,$border,0,'C');
     $pdf->Cell(15,5,$sek,$border,0,'C'); 
     $pdf->Cell(15,5,$worp,$border,0,'C'); 
     $pdf->Cell(25,5,$datum,$border,0,'C'); 
     $pdf->Cell(38,5,$gemkg,$border,0,''); 
     $pdf->Cell(15,5,$ooi,$border,1,'C'); 
      

} // Einde while $zoek_schaap


$pdf->Output($rapport.".pdf","D");
