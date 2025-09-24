<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

include "database.php";

    $db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


$rasnr = $_GET['Id'];

$rapport = 'Rassen';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$zoek_lid = mysqli_query($db,"
SELECT lidId
FROM tblRasuser
WHERE rasuId = ".mysqli_real_escape_string($db,$rasnr)." 
") or die (mysqli_error($db));

while ($row = mysqli_fetch_assoc($zoek_lid)) {    $lidId = $row['lidId']; }

$zoek_reader = mysqli_query($db,"
SELECT reader
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));

while ($re = mysqli_fetch_assoc($zoek_reader)) {    $reader = $re['reader']; }



//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new RasPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$zoek_ras = mysqli_query($db,"
SELECT ras, scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.actief = 1 and lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY sort, ras
") or die (mysqli_error($db));
while ($row = mysqli_fetch_assoc($zoek_ras)) {
    $ras = $row['ras'];
    $scan = $row['scan'];


$border = 'T'; 

    $pdf->Cell(75,5,'','',0);
if($reader == 'Biocontrol') {
     $pdf->Cell(15,5,$veld,$border,0,'C');
}
     $pdf->Cell(18,5,$ras,$border,1,''); 
      


} // Einde while $zoek_schaap


$pdf->Output($rapport.".pdf","D");
