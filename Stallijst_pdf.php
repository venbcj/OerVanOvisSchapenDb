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
    

//$ooi = $_GET['Id'];

$rapport = 'Stallijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

session_start();
	$lidId = $_SESSION["I1"];

$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }

$qryStapel = mysqli_query($db,"
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best)
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_array($qryStapel))
		{ 	   $stapel = $rij['aant'];		}

/* Totalen lammeren, ooien en rammen */

function aantal_fase($datb,$lidid,$Sekse,$Ouder) {
$vw_aantalFase = mysqli_query($datb,"
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = ".mysqli_real_escape_string($datb,$lidid)." and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");

if($vw_aantalFase)
		{	$row = mysqli_fetch_assoc($vw_aantalFase);
				return $row['aant'];
		}
		return FALSE; // Foutafhandeling
}

$sekse = "(isnull(s.geslacht) or s.geslacht is not null)";;
$ouder = 'isnull(prnt.schaapId)';
$lammer = aantal_fase($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ooi'";
$ouder = 'prnt.schaapId is not null';
$moeders = aantal_fase($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ram'";
$ouder = 'prnt.schaapId is not null';
$vaders = aantal_fase($db,$lidId,$sekse,$ouder);

/* EInde Totalen lammeren, ooien en rammen */

class PDF extends FPDF {
	function header(){
		
global $rapport;
global $headerWidth;
global $imageWidth;
global $lidId;
global $stapel;
global $db;
global $lammer;
global $moeders;
global $vaders;
global $PageNo;

/****** Header *******/


		$this->SetFont('Times','',20);
		$this->SetFillColor(166,198,235); // Blauw
		$this->Cell($headerWidth,15,$rapport,0,1,'C',true);

		$this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

		$this->SetFillColor(158,179,104); // Groen
		$this->Cell($headerWidth,5,'',0,1,'',true);
$p = $this->PageNo();
if($p == 1) {
	$this->SetFont('Times','',12);
	$this->Cell(100,5,'Aantal schapen '. $stapel,'',0,'R',false);
	$this->SetFont('Times','',9);
	$this->Cell(10,5,'waarvan','',1,'',false);
	$this->Ln(2);

	$this->SetFont('Times','',10);
	$this->Cell(80,4,'',0,0,'',false);
	$this->Cell(15,4,'- '.$lammer.' lammeren',0,1,'',false);

	$this->Cell(80,4,'',0,0,'',false);
	$this->Cell(15,4,'- '.$moeders.' moeders',0,1,'',false);

	$this->Cell(80,4,'','',0,'',false);
	$this->Cell(15,4,'- '.$vaders.' vaders','',1,'',false);

	$this->Ln(5);
}
else { 	$this->Ln(5); }

	$this->SetFont('Times','',10);
	$this->Cell(45,5,'','',0,'C',false);
	$this->Cell(15,5,'Werknr',0,0,'C',false);
	$this->Cell(25,5,'Levensnummer',0,0,'C',false);
	$this->Cell(15,5,'Geboren',0,0,'C',false);
	$this->Cell(15,5,'Geslacht',0,0,'C',false);
	$this->Cell(15,5,'Generatie',0,1,'C',false);

	//$this->Ln(1);

/****** EINDE Header *******/
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


$pdf = new PDF($Afdrukstand,'mm','A4'); //use new class

$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

/****** BODY ******/


/* Gegevens stalijst */

$result = mysqli_query($db,"
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1) 
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best)
ORDER BY right(s.levensnummer, $Karwerk)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($result))
		{
		$werknr = $row['werknum'];
		$levnr = $row['levensnummer'];
		$datum = $row['gebdm'];
		$geslacht = $row['geslacht']; 
		$aanw = $row['aanw']; 
		if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } 

	   $pdf->SetFont('Times','',8);
	   $pdf->SetDrawColor(200,200,200); // Grijs
		$pdf->Cell(45,5,'','',0,'',false);
		$pdf->Cell(15,5,$werknr,'TB',0,'C',false);
		$pdf->Cell(25,5,$levnr,'TB',0,'',false);
		$pdf->Cell(15,5,$datum,'TB',0,'C',false);
		$pdf->Cell(15,5,$geslacht,'TB',0,'C',false);
		$pdf->Cell(15,5,$fase,'TB',1,'C',false);

}	
/* Einde Gegevens stallijst */


/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
?>
