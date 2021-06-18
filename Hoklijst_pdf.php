<?php /* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url 
20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam */
require('fpdf/fpdf.php');

include "database.php";

	$db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }

$groep = $_GET['Id'];

$rapport = 'Hoklijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

session_start();
//Include "login.php";
	$lidId = $_SESSION["I1"];

$zoek_doel = mysqli_query($db,"select doel from tblDoel where doelId = ".mysqli_real_escape_string($db,$groep)." ") or die (mysqli_error($db));
while($dl = mysqli_fetch_array($zoek_doel)){ $dgroep = $dl['doel']; }

class PDF extends FPDF {
	function header(){

global $rapport;
global $headerWidth;
global $imageWidth;
global $lidId;
global $dgroep;
global $zoek_hok_ingebruik;
/****** Header *******/

		$this->SetFont('Times','',20);
		$this->SetFillColor(166,198,235); // Blauw
		$this->Cell($headerWidth,15,$rapport,0,1,'C',true);

		$this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

		$this->SetFillColor(158,179,104); // Groen
		$this->Cell(190,5,'',0,1,'',true);

/****** EINDE Header *******/

		$this->SetFont('Times','I',7);
		$this->Ln(5);

		$this->Cell(145,4,'',0,0,'',false); 		$this->Cell(40,4,'Doelgroep : '.$dgroep,0,1,'L',false);
		

		$this->Ln(5);

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


$pdf->SetDrawColor(200,200,200); // Grijs

if($groep == 1) {
$zoek_hok_ingebruik_geb = mysqli_query($db,"
select ho.hokId, ho.hoknr
from tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
group by ho.hokId, ho.hoknr
") or die (mysqli_error($db)); $zoek_hok_ingebruik = $zoek_hok_ingebruik_geb; }

if($groep == 2) {
$zoek_hok_ingebruik_spn = mysqli_query($db,"
select ho.hokId, ho.hoknr
from tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId) and isnull(prnt.schaapId)
group by ho.hokId, ho.hoknr
") or die (mysqli_error($db)); $zoek_hok_ingebruik = $zoek_hok_ingebruik_spn; }

$i = 1;

while($hk = mysqli_fetch_assoc($zoek_hok_ingebruik))
		{ $hokId = $hk['hokId']; $hok = $hk['hoknr'];  

$at = mysqli_num_rows($zoek_hok_ingebruik);


if($i >1) { $pdf->AddPage(); }

	$pdf->SetFont('Times','B',12);
		$pdf->Cell(75,3,'','',0,'',false);
		$pdf->Cell(30,3,$hok,'',1,'',false);
$i++;
		$pdf->Ln(7);

	$pdf->SetFont('Times','B',8);
		$pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
		$pdf->SetDrawColor(50,50,100);
		$pdf->Cell(50,3,'','',0,'',false);
		$pdf->Cell(24,3,'Ras','',0,'',false);
		$pdf->Cell(14,3,'Geslacht','',0,'',false);
		$pdf->Cell(30,3,'Nu in verblijf','',1,'',false);

if($groep == 1) {
$zoek_schapen_in_verblijf_geb = mysqli_query($db,"
select ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
from tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
group by ho.hoknr, r.ras, s.geslacht
") or die (mysqli_error($db));  $zoek_schapen_in_verblijf = $zoek_schapen_in_verblijf_geb; }

if($groep == 2) {
$zoek_schapen_in_verblijf_spn = mysqli_query($db,"
select ho.hoknr, count(b.bezId) nu, r.ras, s.geslacht
from tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (s.rasId = r.rasId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId) and isnull(prnt.schaapId)
group by ho.hoknr, r.ras, s.geslacht
") or die (mysqli_error($db));  $zoek_schapen_in_verblijf =$zoek_schapen_in_verblijf_spn; }

	while($n = mysqli_fetch_assoc($zoek_schapen_in_verblijf))
		{ $ras = $n['ras'];
		  $geslacht = $n['geslacht'];
		  $nu = $n['nu']; 

	   $pdf->SetFont('Times','',8);
	   $pdf->SetDrawColor(200,200,200); // Grijs
		$pdf->Cell(50,5,'','',0,'',false);
		$pdf->Cell(23,5,$ras,'T',0,'',false);
		$pdf->Cell(15,5,$geslacht,'T',0,'C',false);
		$pdf->Cell(15,5,$nu,'T',1,'C',false);


		} // Einde fetch_assoc($zoek_schapen_in_verblijf)
		} // Einde fetch_assoc($zoek_hok_ingebruik)

/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
?>