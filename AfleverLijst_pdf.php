<?php /* https://www.youtube.com/watch?v=CamDi3Syjy4
31-7-2020; wdgn gewijzigd in wdgn_v 
30-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes 
07-07-2024 : Werknr oplopend gesorteerd 
19-03-2025 : Gewicht toegevoegd */

require('fpdf/fpdf.php');

include "database.php";

	$db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


$his = $_GET['hisId'];

$rapport = 'Afleverlijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$zoek = mysqli_query($db,"
SELECT st.lidId, date_format(datum,'%d-%m-%Y') datum, datum date, rel_best, p.naam
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (p.partId = r.partId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$his)."' ") or die (mysqli_error($db));
While ($row = mysqli_fetch_assoc($zoek)) {
	$lidId = $row['lidId'];
	$afvDate = $row['date'];
	$afvDatum = $row['datum'];
	$relId = $row['rel_best'];
	$bestemming = $row['naam'];
}


$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }

$aantal = mysqli_query($db,"
SELECT count(distinct st.stalId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.datum = '".mysqli_real_escape_string($db,$afvDate)."' and st.rel_best = '".mysqli_real_escape_string($db,$relId)."' and a.af = 1 and h.skip = 0
");
while($rij = mysqli_fetch_array($aantal)){ $schpn = $rij['aant']; }

class PDF extends FPDF {
	function header(){


global $rapport;
global $headerWidth;
global $imageWidth;
global $bestemming;
global $afvDatum;
global $schpn;

		//dummy cel to put logo
		//$this->cell(12,0,'',0,0);
		//is equivalent to:
		//$this->Cell(18);

		$this->SetFont('Times','',20);
		$this->SetFillColor(166,198,235); // Blauw
		$this->Cell($headerWidth,15,$rapport,0,1,'C',true);


		//dummy cel to put logo
		//$this->cell(12,0,'',0,0);
		//is equivalent to:
		//$this->Cell(12);

		//put logo
		//$this->Image('schaap.jpg',5,7,18);
		$this->Image('OER_van_OVIS.jpg',$imageWidth,11,30,14);

		$this->SetFillColor(158,179,104); // Groen
		$this->Cell($headerWidth,5,'',0,1,'',true);
		$this->SetFont('Times','I',7);
		$this->Ln(5);

		$this->Cell(145,4,'',0,0,'',false); 		$this->Cell(40,4,'Bestemming : '.$bestemming,0,1,'L',false);
		$this->Cell(145,4,'',0,0,'',false);			$this->Cell(40,4,'Afleverdatum : '.$afvDatum,0,1,'L',false);
		$this->Cell(145,4,'',0,0,'',false);			$this->Cell(40,4,'Aantal schapen : '.$schpn,0,1,'L',false);

//dummy cell to give line spacing
		//$this->Cell(0,5,'',0,1);
		//is equivalent to:
		$this->Ln(5);

		$this->SetFont('Times','B',8);

		$this->SetFillColor(166,198,235);
		$this->SetDrawColor(50,50,100);
		$this->Cell(23,3,'','',0,'',false);
		$this->Cell(15,3,'','',0,'',false);
		$this->Cell(15,3,'','',0,'',false);
		$this->Cell(30,3,'','',0,'',false);
		$this->Cell(18,3,'Datum','',0,'',false);
		 $this->Cell(20,3,'','',1,'',false);
		$this->Cell(23,3,'Levensnummer','',0,'',false);
		$this->Cell(15,3,'Werknr','',0,'C',false);
		$this->Cell(15,3,'Gewicht','',0,'C',false);
		$this->Cell(30,3,'Medicijn','',0,'',false);
		$this->Cell(18,3,'toepassing','',0,'',false);
		 $this->Cell(20,3,'Wachtdagen','',1,'',false);
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

$pdf->SetFont('Times','',6);
$pdf->SetDrawColor(200,200,200); // Grijs

$zoek_schaap = mysqli_query($db,"
SELECT st.lidId, s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, h.kg, pil.datum, pil.naam, pil.wdgn_v
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblActie a on (h.actId = a.actId)
 left join (
	SELECT s.schaapId, date_format(h.datum,'%d-%m-%Y') datum, art.naam, art.wdgn_v
	FROM tblSchaap s 
	 join tblStal st on (st.schaapId = s.schaapId)
	 join tblHistorie h on (h.stalId = st.stalId)
	 join tblNuttig n on (h.hisId = n.hisId)
	 join tblInkoop i on (i.inkId = n.inkId)
	 join tblArtikel art on (i.artId = art.artId) 
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 8 and h.skip = 0 and (h.datum + interval art.wdgn_v day) >= sysdate()
) pil on (st.schaapId = pil.schaapId)
WHERE h.datum = '".mysqli_real_escape_string($db,$afvDate)."' and st.rel_best = '".mysqli_real_escape_string($db,$relId)."' and a.af = 1 and h.skip = 0
ORDER BY right(s.levensnummer,$Karwerk)
");
while($data=mysqli_fetch_array($zoek_schaap)){
	

$levnr_new = '';
//$vandaag = date('Y-m-d');

  if(isset($levnr_new) && $levnr_new <> $data['levensnummer']) { $border = 'T'; 

  	$pdf->Cell(23,5,$data['levensnummer'],$border,0);
  	$pdf->Cell(15,5,$data['werknr'],$border,0,'C');  // C = center
  	$pdf->Cell(15,5,$data['kg'],$border,0,'C');  }
  else { $border = ''; 
  	$pdf->Cell(23,5,'',$border,0); 
  	$pdf->Cell(15,5,'',$border,0);
  	$pdf->Cell(15,5,'',$border,0); }
	
	$pdf->Cell(30,5,$data['naam'],$border,0);
	$pdf->Cell(18,5,$data['datum'],$border,0);
 	$pdf->Cell(20,5,$data['wdgn_v'],$border,1,'C'); 

$levnr_new = $data['levensnummer']; 
} // Einde while $zoek_schaap


$pdf->Output($rapport."_".$afvDatum.".pdf","D");
?>