<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

// TODO: (BV) #0004141 dit samenvoegen met database.php?
if ($_SERVER['HTTP_HOST'] == 'localhost:8080') {
    $database = 'SchapenDb1';
    $username = 'root';
    $ww = 'usbw';
} else if($_SERVER['HTTP_HOST'] == 'test.oervanovis.nl') {
    $database = 'k36098_bvdvSchapenDbT';
    $username = 'bvdvschaapt';
    $ww = 'MSenWL44';
} else if($_SERVER['HTTP_HOST'] == 'ovis.oervanovis.nl') {
    $database = 'k36098_bvdvSchapenDb';
    $username = 'bvdvschaapovis';
    $ww = 'MSenWL44';
}
    $host = "localhost";
$user = $username;
$pw = $ww;
$dtb = $database;
 
    $db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }

$hok = $_GET['Id'];

$rapport = 'Verblijven';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$zoek_lid = mysqli_query($db,"
SELECT lidId
FROM tblHok
WHERE hokId = ".mysqli_real_escape_string($db,$hok)." 
") or die (mysqli_error($db));

while ($row = mysqli_fetch_assoc($zoek_lid)) { $lidId = $row['lidId']; }

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new HokPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs

$zoek_verblijf = mysqli_query($db,"
SELECT hoknr, scan
FROM tblHok
WHERE actief = 1 and lidId = ".mysqli_real_escape_string($db,$lidId)."
ORDER BY hoknr
") or die (mysqli_error($db));
while ($row = mysqli_fetch_assoc($zoek_verblijf)) {
    $hoknr = $row['hoknr'];
    $scan = $row['scan'];
$border = 'T'; 
    $pdf->Cell(75,5,'','',0);
     $pdf->Cell(15,5,$scan,$border,0,'C');
     $pdf->Cell(18,5,$hoknr,$border,1,'C'); 
} // Einde while $zoek_schaap

$pdf->Output($rapport.".pdf","D");
