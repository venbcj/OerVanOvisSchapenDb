<?php /* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url 
11-11-2019 kolomkop worp gewijzigd in worpgrootte */
require('fpdf/fpdf.php');

include "database.php";

	$db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }

$stal = $_GET['Id'];
$van = $_GET['d1'];
$tot = $_GET['d2'];

$rapport = 'Meerling in periode';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 183; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 270; }

$zoek_lid = mysqli_query($db,"
SELECT lidId
FROM tblStal
WHERE stalId = ".mysqli_real_escape_string($db,$stal)." 
") or die (mysqli_error($db));
While ($row = mysqli_fetch_assoc($zoek_lid)) {	$lidId = $row['lidId']; }



class PDF extends FPDF {
	function header(){


global $rapport;
global $headerWidth;
global $imageWidth;

		$this->SetFont('Times','',20);
		$this->SetFillColor(166,198,235); // Blauw
		$this->Cell($headerWidth,15,$rapport,0,1,'C',true);

		$this->Image('schaap.jpg',$imageWidth,11,16);

		$this->SetFillColor(158,179,104); // Groen
		$this->Cell($headerWidth,5,'',0,1,'',true);
		$this->SetFont('Times','I',7);
		$this->Ln(5);


		$this->Ln(5);

		$this->SetFont('Times','B',9);

		$this->SetFillColor(166,198,235);
		$this->SetDrawColor(50,50,100);

		$this->Cell(30,5,'','',0,'',false);
		$this->Cell(20,5,'Lammeren','',0,'C',false);
		$this->Cell(15,5,'Geslacht','',0,'C',false);
		$this->Cell(16,5,'Worpgrootte','',0,'C',false);
		$this->Cell(21,5,'Werpdatum','',0,'C',false);
		$this->Cell(40,5,'Gem. groei per dag','',0,'',false);
		$this->Cell(15,5,'Ooi','',1,'C',false);
		
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

$Karwerk = 5;
/*$van = '2013-12-01';
$tot = '2018-12-01';*/

$zoek_meerling = mysqli_query($db,"
SELECT right(lam.levensnummer,$Karwerk) lam, lam.geslacht, count(wrp.volwId) worp, h.datum date, date_format(h.datum,'%d-%m-%Y') datum, right(mdr.levensnummer,$Karwerk) ooi, round(((lstkg.kg - h.kg)*1000)/datediff(mx.mdm,h.datum),2) gemgroei, date_format(mx.mdm,'%d-%m-%Y') kgdag, st.stalId
FROM tblschaap lam
 join tblVolwas v on (lam.volwId = v.volwId)
 join tblSchaap mdr on (mdr.schaapId = v.mdrId)
 join tblSchaap wrp on (lam.volwId = wrp.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join (
 	SELECT stalId, max(datum) mdm
 	FROM tblHistorie
	WHERE kg is not null and actId > 1
	GROUP BY stalId
 ) mx on (mx.stalId = st.stalId)
 left join (
 	SELECT stalId, datum, max(kg) kg
 	FROM tblHistorie
	WHERE kg is not null and actId > 1
	GROUP BY stalId, datum
 ) lstkg on (lstkg.stalId = st.stalId and lstkg.datum = mx.mdm)
WHERE lam.levensnummer is not null and isnull(st.rel_best) and h.actId = 1 and st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.datum >= '".mysqli_real_escape_string($db,$van)."' and h.datum <= '".mysqli_real_escape_string($db,$tot)."'
GROUP BY lam.levensnummer, lam.geslacht, h.datum, mdr.levensnummer, mx.mdm, st.stalId
ORDER BY right(lam.levensnummer,$Karwerk)
") or die (mysqli_error($db));
while ($row = mysqli_fetch_assoc($zoek_meerling)) {
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
?>