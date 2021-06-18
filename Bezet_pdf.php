<?php //https://www.youtube.com/watch?v=CamDi3Syjy4
/* 20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam */
require('fpdf/fpdf.php');

include "database.php";

	$db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


if(isset($_GET['Id'])) { $hokId = $_GET['Id']; } // via pagina Bezet.php bestaat $_GET['Id'] niet 

$rapport = 'Verblijf';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

session_start();
//Include "login.php";
	$lidId = $_SESSION["I1"];

$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }


class PDF extends FPDF {
	function header(){

global $rapport;
global $headerWidth;
global $imageWidth;
global $lidId;
global $Karwerk;
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

if(!isset($hokId)) {
$zoek_verblijven_in_gebruik = mysqli_query($db,"
select h.hokId, h.hoknr, count(distinct schaap_geb) maxgeb, count(distinct schaap_spn) maxspn, min(dmin) eerste_in, max(dmuit) laatste_uit
from (
	select b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, NULL dmuit
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		from tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		 left join (
			select st.schaapId, h.datum dmspn	from tblStal st join tblHistorie h on (st.stalId = h.stalId)	where h.actId = 4
		 ) spn on (spn.schaapId = st.schaapId)
		 left join (
			select st.schaapId, h.datum dmprnt	from tblStal st join tblHistorie h on (st.stalId = h.stalId)	where h.actId = 3
		 ) prnt on (prnt.schaapId = st.schaapId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		 and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
		group by b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId)
	and isnull(spn.schaapId)
	and isnull(prnt.schaapId)

	UNION

	select b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, NULL dmuit
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		from tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		group by b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId)
	and isnull(prnt.schaapId)

	UNION

	select b.hokId, st.schaapId schaap_geb, NULL schaap_spn, h.datum dmin, ht.datum dmuit
	from tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join 
	 (
		select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		from tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		 left join (
			select st.schaapId, h.datum dmspn	from tblStal st join tblHistorie h on (st.stalId = h.stalId)	where h.actId = 4
		 ) spn on (spn.schaapId = st.schaapId)
		 left join (
			select st.schaapId, h.datum dmprnt	from tblStal st join tblHistorie h on (st.stalId = h.stalId)	where h.actId = 3
		 ) prnt on (prnt.schaapId = st.schaapId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
		 and h1.datum <= coalesce(dmspn, coalesce(dmprnt,'2200-01-01'))
		group by b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	 join tblStal st on (st.stalId = h.stalId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
		select p.hokId, max(p.dmafsluit) dmstop
		from tblPeriode p
		 join tblHok h on (h.hokId = p.hokId)
		where h.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.doelId = 1 and dmafsluit is not null
		group by p.hokId
	 ) endgeb on (endgeb.hokId = b.hokId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and ht.datum > coalesce(dmstop,'1973-09-11') 
	 and (isnull(spn.schaapId)  or spn.datum  > coalesce(dmstop,'1973-09-11') and h.datum < spn.datum) 
	 and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11'))

	UNION

	select b.hokId, NULL schaap_geb, st.schaapId schaap_spn, h.datum dmin, ht.datum dmuit
	from tblBezet b
	 join tblHistorie h on (h.hisId = b.hisId)
	 join 
	 (
		select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
		from tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
		group by b.bezId, st.schaapId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 join tblHistorie ht on (ht.hisId = uit.hist)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 4
	 ) spn on (spn.schaapId = st.schaapId)
	 left join (
		select st.schaapId, h.datum
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	 left join (
		select p.hokId, max(p.dmafsluit) dmstop
		from tblPeriode p
		 join tblHok h on (h.hokId = p.hokId)
		where h.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.doelId = 2 and dmafsluit is not null
		group by p.hokId
	 ) endspn on (endspn.hokId = b.hokId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId /*9-1-2019 weggehaald and (isnull(prnt.schaapId) or prnt.datum > coalesce(dmstop,'1973-09-11')) */)." and ht.datum > coalesce(dmstop,'1973-09-11') 
	 and h.datum >= spn.datum and (h.datum < prnt.datum or isnull(prnt.schaapId))
	 

	UNION

	select b.hokId, NULL schaap_geb, NULL schaap_spn, NULL dmin, NULL dmuit
	from (
		select b.hisId, b.hokId
		from tblBezet b
		 join tblHistorie h on (b.hisId = h.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		 join (
			select st.schaapId, h.hisId, h.datum
			from tblStal st
			join tblHistorie h on (st.stalId = h.stalId)
			where h.actId = 3
		) prnt on (prnt.schaapId = st.schaapId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.datum >= prnt.datum
	 ) b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 left join 
	 (
		select b.bezId, h1.hisId hisv, min(h2.hisId) hist
		from tblBezet b
		 join tblHistorie h1 on (b.hisId = h1.hisId)
		 join tblActie a1 on (a1.actId = h1.actId)
		 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
		 join tblActie a2 on (a2.actId = h2.actId)
		 join tblStal st on (h1.stalId = st.stalId)
		where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
		group by b.bezId, h1.hisId
	 ) uit on (uit.hisv = b.hisId)
	 join (
		select st.schaapId
		from tblStal st
		 join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	 ) prnt on (prnt.schaapId = st.schaapId)
	where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(uit.bezId)
 ) ingebr
 join tblHok h on (ingebr.hokId = h.hokId)
group by h.hokId, h.hoknr
order by hoknr
") or die (mysqli_error($db));

	$doorloop_verblijf = $zoek_verblijven_in_gebruik;
}
else
{
$zoek_verblijf_gegevens = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok
WHERE hokId = ".mysqli_real_escape_string($db,$hokId)."
") or die (mysqli_error($db));

	$doorloop_verblijf = $zoek_verblijf_gegevens;
}
$i = 1;

		while($row = mysqli_fetch_assoc($doorloop_verblijf))
		{	$hokId = $row['hokId'];
			$hok = $row['hoknr'];
 

if($i >1) { $pdf->AddPage(); } // hier wordt een nieuwe pagina gemaakt
$i++;

	$pdf->SetFont('Times','B',12);
		$pdf->Cell(75,3,'','',0,'',false);
		$pdf->Cell(30,3,$hok,'',1,'',false);


// LAMMEREN VOOR SPENEN
$zoek_nu_in_verblijf_geb = mysqli_query($db,"
select count(b.bezId) aantin
from tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
") or die (mysqli_error($db));
		
	while($nu = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb))
		{ $nu_geb = $nu['aantin']; }

	if($nu_geb > 0) { // Als er lammeren voor spenen in het verblijf zitten

		$pdf->Ln(7);
		$pdf->SetFont('Times','I',9);

		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(40,3,'Aantal lammeren voor spenen : ','',0,'',false);
		$pdf->Cell(10,3,' '.$nu_geb,'',1,'',false);
		
		$pdf->Ln(7);

	$pdf->SetFont('Times','B',8);
		$pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
		$pdf->SetDrawColor(50,50,100);
	// kopregel 1	
		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'Fictieve','',1,'C',false);
	// kopregel 2	
		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,'Werknr','',0,'C',false);
		$pdf->Cell(24,3,'Ras','',0,'C',false);
		$pdf->Cell(24,3,'Geslacht','',0,'C',false);
		$pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
		$pdf->Cell(24,3,'Datum in verblijf','',0,'C',false);
		$pdf->Cell(24,3,'speendatum','',0,'C',false);
		$pdf->Cell(24,3,'Moeder','',1,'C',false);

$hok_inhoud_geb = mysqli_query ($db,"
select s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, right(mdr.levensnummer,$Karwerk) mdr
from tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
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
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 1
 ) hg on (hg.schaapId = st.schaapId)
  left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_geb))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
		 $vanaf = $row['van'];
		 $gebdm = $row['geb'];
		 $geslacht = $row['geslacht'];
		 $ficdm = $row['ficspn'];
		 $moeder = $row['mdr'];



	   $pdf->SetFont('Times','',8);
	   $pdf->SetDrawColor(200,200,200); // Grijs
	    $pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,$werknr,'T',0,'C',false);
		$pdf->Cell(24,3,$ras,'T',0,'C',false);
		$pdf->Cell(24,3,$geslacht,'T',0,'C',false);
		$pdf->Cell(24,3,$gebdm,'T',0,'C',false);
		$pdf->Cell(24,3,$vanaf,'T',0,'C',false);
		$pdf->Cell(24,3,$ficdm,'T',0,'C',false);
		$pdf->Cell(24,3,$moeder,'T',1,'C',false);

		}

	} // Einde if($nu_geb > 0)
// EINDE LAMMEREN VOOR SPENEN
// LAMMEREN NA SPENEN
$zoek_nu_in_verblijf_spn = mysqli_query($db,"
select count(b.bezId) aantin
from tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
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
") or die (mysqli_error($db));

	while($n = mysqli_fetch_assoc($zoek_nu_in_verblijf_spn))
		{ $nu_spn = $n['aantin']; }

if($nu_spn > 0) { // Als er lammeren na spenen in het verblijf zitten

		$pdf->Ln(7);
		$pdf->SetFont('Times','I',9);

		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(38,3,'Aantal lammeren na spenen : ','',0,'',false);
		$pdf->Cell(10,3,' '.$nu_spn,'',1,'',false);
		
		$pdf->Ln(7);

	$pdf->SetFont('Times','B',8);
		$pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
		$pdf->SetDrawColor(50,50,100);
	// kopregel 1	
		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'','',0,'C',false);
		$pdf->Cell(24,3,'Fictieve','',1,'C',false);
	// kopregel 2	
		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,'Werknr','',0,'C',false);
		$pdf->Cell(24,3,'Ras','',0,'C',false);
		$pdf->Cell(24,3,'Geslacht','',0,'C',false);
		$pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
		$pdf->Cell(24,3,'Datum in verblijf','',0,'C',false);
		$pdf->Cell(24,3,'afleverdatum','',1,'C',false);

$hok_inhoud_spn = mysqli_query ($db,"
select s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(spn.datum,'%d-%m-%Y') spn, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, date_format(hg.datum + interval 130 day,'%d-%m-%Y') ficafv, right(mdr.levensnummer,$Karwerk) mdr
from tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
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
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 1
 ) hg on (hg.schaapId = st.schaapId)
 join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_spn))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
		 $gebdm = $row['geb'];
		 $vanaf = $row['van'];
		 $ficdm = $row['ficafv'];

	   $pdf->SetFont('Times','',8);
	   $pdf->SetDrawColor(200,200,200); // Grijs
	    $pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,$werknr,'T',0,'C',false);
		$pdf->Cell(24,3,$ras,'T',0,'C',false);
		$pdf->Cell(24,3,$geslacht,'T',0,'C',false);
		$pdf->Cell(24,3,$gebdm,'T',0,'C',false);
		$pdf->Cell(24,3,$vanaf,'T',0,'C',false);
		$pdf->Cell(24,3,$ficdm,'T',1,'C',false);

		}
	} // if($nu_spn > 0)
// EINDE LAMMEREN NA SPENEN
// VOLWASSEN DIEREN
$zoek_nu_in_verblijf_prnt = mysqli_query($db,"
select count(b.hisId) aantin
from (
	select b.hisId, b.hokId
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		select st.schaapId, h.hisId, h.datum
		from tblStal st
		join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId)
") or die (mysqli_error($db));
		
	while($nu = mysqli_fetch_assoc($zoek_nu_in_verblijf_prnt))
		{ $nu_prnt = $nu['aantin']; }

if($nu_prnt > 0) { // Als er volwassen schapenin het verblijf zitten

		$pdf->Ln(7);
		$pdf->SetFont('Times','I',9);

		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(36,3,'Aantal volwassen schapen : ','',0,'',false);
		$pdf->Cell(10,3,' '.$nu_prnt,'',1,'',false);
		
		$pdf->Ln(7);

	$pdf->SetFont('Times','B',8);
		$pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
		$pdf->SetDrawColor(50,50,100);

		$pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,'Werknr','',0,'C',false);
		$pdf->Cell(24,3,'Ras','',0,'C',false);
		$pdf->Cell(24,3,'Geslacht','',0,'C',false);
		$pdf->Cell(24,3,'Geboortedatum','',0,'C',false);
		$pdf->Cell(24,3,'Datum in verblijf','',1,'C',false);

$hok_inhoud_vanaf_aanwas = mysqli_query ($db,"
select s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(prnt.datum,'%d-%m-%Y') aanw, date_format(h.datum,'%d-%m-%Y') van, b.hisId
from (
	select b.hisId, b.hokId
	from tblBezet b
	 join tblHistorie h on (b.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join (
		select st.schaapId, h.hisId, h.datum
		from tblStal st
		join tblHistorie h on (st.stalId = h.stalId)
		where h.actId = 3
	) prnt on (prnt.schaapId = st.schaapId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
	select b.bezId, h1.hisId hisv, min(h2.hisId) hist
	from tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
	group by b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 1
 ) hg on (hg.schaapId = st.schaapId)
 join (
	select st.schaapId, h.datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
where b.hokId = ".mysqli_real_escape_string($db,$hokId)." and isnull(uit.bezId)
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($hok_inhoud_vanaf_aanwas))
		{
		 $werknr = $row['werknr'];
		 $ras = $row['ras'];
		 $geslacht = $row['geslacht'];
		 $gebdm = $row['geb'];
		 $vanaf = $row['van'];


	   $pdf->SetFont('Times','',8);
	   $pdf->SetDrawColor(200,200,200); // Grijs
	    $pdf->Cell(5,3,'','',0,'',false);
		$pdf->Cell(24,3,$werknr,'T',0,'C',false);
		$pdf->Cell(24,3,$ras,'T',0,'C',false);
		$pdf->Cell(24,3,$geslacht,'T',0,'C',false);
		$pdf->Cell(24,3,$gebdm,'T',0,'C',false);
		$pdf->Cell(24,3,$vanaf,'T',1,'C',false);

	}

} // Einde if($nu_prnt > 0)
// EINDE VOLWASSEN DIEREN

		} // Einde fetch_assoc($zoek_verblijven_in_gebruik)

/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
?>