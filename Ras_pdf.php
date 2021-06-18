<?php /* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */
require('fpdf/fpdf.php');

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

while ($row = mysqli_fetch_assoc($zoek_lid)) {	$lidId = $row['lidId']; }

$zoek_reader = mysqli_query($db,"
SELECT reader
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));

while ($re = mysqli_fetch_assoc($zoek_reader)) {	$reader = $re['reader']; }


class PDF extends FPDF {
	function header(){


global $rapport;
global $headerWidth;
global $imageWidth;

		$this->SetFont('Times','',20);
		$this->SetFillColor(166,198,235); // Blauw
		$this->Cell($headerWidth,15,$rapport,0,1,'C',true);

		$this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

		$this->SetFillColor(158,179,104); // Groen
		$this->Cell($headerWidth,5,'',0,1,'',true);
		$this->SetFont('Times','I',7);
		$this->Ln(5);


		$this->Ln(5);

		$this->SetFont('Times','B',9);

		$this->SetFillColor(166,198,235);
		$this->SetDrawColor(50,50,100);
		$this->Cell(75,3,'','',0,'',false);
if($reader == 'Biocontrol') {
		$this->Cell(15,3,'Code','',0,'C',false);
}
		$this->Cell(18,3,'','',1,'C',false);

		$this->Cell(75,5,'','',0,'',false);
if($reader == 'Biocontrol') {
		$this->Cell(15,5,'reader','',0,'C',false);
}
		$this->Cell(18,5,'Ras','',1,'C',false);
		
	}
	function Footer(){

		//Go to 1.5 cm from bottom
		$this->SetY(-15);

		$this->SetFont('Times','',8);

		//Cell(float w, float h, string txt, mixed border, int ln, string align, boolean fill, mixed link)
		//width = 0 means the cell is extended up to the right margin
		$this->Cell(0,10,'Pagina '.$this->PageNo()." / {pages}",0,0,'C');
	}
}

//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new PDF($Afdrukstand,'mm','A4'); //use new class

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
?>