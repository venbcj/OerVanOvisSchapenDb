<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */
require('fpdf/fpdf.php');

include "database.php";

    $db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


$stal = $_GET['Id'];

$rapport = 'Dekrammen';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$zoek_lid = mysqli_query($db,"
SELECT lidId
FROM tblStal
WHERE stalId = ".mysqli_real_escape_string($db,$stal)." 
") or die (mysqli_error($db));
While ($row = mysqli_fetch_assoc($zoek_lid)) {    $lidId = $row['lidId']; }


$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));

while ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }



class PDF extends FPDF {
    function header(){


global $rapport;
global $headerWidth;
global $imageWidth;


        //dummy cel to put logo
        //$this->cell(12,0,'',0,0);
        //is equivalent to:
        //$this->Cell(18);

        $this->SetFont('Times','',20);
        $this->SetFillColor(166,198,235); // Blauw
        $this->Cell($headerWidth,15,$rapport,0,1,'C',true);


        $this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

        $this->SetFillColor(158,179,104); // Groen
        $this->Cell($headerWidth,5,'',0,1,'',true);
        $this->SetFont('Times','I',7);
        $this->Ln(5);

//dummy cell to give line spacing
        //$this->Cell(0,5,'',0,1);
        //is equivalent to:
        $this->Ln(5);

        $this->SetFont('Times','B',9);

        $this->SetFillColor(166,198,235);
        $this->SetDrawColor(50,50,100);
        $this->Cell(68,3,'','',0,'',false);
        $this->Cell(15,3,'Code','',0,'C',false);
        $this->Cell(18,3,'','',0,'C',false);
        $this->Cell(15,3,'','',1,'C',false);

        $this->Cell(68,5,'','',0,'',false);
        $this->Cell(15,5,'reader','',0,'C',false);
        $this->Cell(18,5,'Dekram','',0,'C',false);
        $this->Cell(15,5,'Halsnr','',1,'C',false);
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

$zoek_dekram = mysqli_query($db,"
SELECT st.stalId, right(levensnummer, $Karwerk) werknr, concat(kleur,' ',halsnr) halsnr, scan 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
     SELECT stalId
     FROM tblHistorie
     WHERE actId = 3
 ) ouder on (ouder.stalId = st.stalId)
WHERE s.geslacht = 'ram' and isnull(st.rel_best) and lidId = ".mysqli_real_escape_string($db,$lidId)." 
GROUP BY st.stalId, levensnummer, scan 
ORDER BY right(levensnummer, $Karwerk)
") or die (mysqli_error($db));
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
?>
