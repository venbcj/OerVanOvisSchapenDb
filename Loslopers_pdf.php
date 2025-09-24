<?php

require_once("autoload.php");

//https://www.youtube.com/watch?v=CamDi3Syjy4
/* 30-12-2019 Gekopieerd van Bezet_pdf.php */
require('fpdf/fpdf.php');

include "database.php";

    $db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


if(isset($_GET['Id'])) { $hokId = $_GET['Id']; } // via pagina Bezet.php bestaat $_GET['Id'] niet 

$rapport = 'Loslopers';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

Session::start();

    $lidId = $_SESSION["I1"];

$zoek_karwerk = mysqli_query($db,"
SELECT kar_werknr 
FROM tblLeden
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." 
") or die (mysqli_error($db));
While ($krw = mysqli_fetch_assoc($zoek_karwerk)) { $Karwerk = $krw['kar_werknr']; }

$pdf = new LoslopersPdf($Afdrukstand,'mm','A4'); //use new class

$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

/****** BODY ******/


$pdf->SetDrawColor(200,200,200); // Grijs 


    $pdf->SetFont('Times','B',12);
        $pdf->Cell(75,3,'','',0,'',false);
        $pdf->Cell(30,3,'','',1,'',false);


// LAMMEREN VOOR SPENEN
$zoek_aantal_doelgroep1 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu1 = mysqli_fetch_assoc($zoek_aantal_doelgroep1))
        { $aanwezig1 = $nu1['aantin']; }

    if($aanwezig1 > 0) { // Als er lammeren voor spenen in het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(40,3,'Aantal lammeren voor spenen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$aanwezig1,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);
    // kopregel 1    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);
    // kopregel 2    
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',0,'C',false);
        $pdf->Cell(24,3,'','',1,'C',false);

$schapen_geb = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(spn.schaapId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($schapen_geb))
        {
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $vanaf = '';
         $gebdm = '';
         $ficdm = '';
         $moeder = '';



       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',0,'C',false);
        $pdf->Cell(24,3,'','T',1,'C',false);

        }

    } // Einde if($aanwezig1 > 0)
// EINDE LAMMEREN VOOR SPENEN
// LAMMEREN NA SPENEN
$zoek_aantal_doelgroep2 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu2 = mysqli_fetch_assoc($zoek_aantal_doelgroep2))
        { $aanwezig2 = $nu2['aantin']; }

if($aanwezig2 > 0) { // Als er lammeren na spenen in het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(38,3,'Aantal lammeren na spenen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$aanwezig2,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);

    // kopregel     
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',1,'C',false);

$schapen_spn = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = hin.schaapId)
 left join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($schapen_spn))
        {
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];

       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',1,'C',false);

        }
    } // if($aanwezig2 > 0)
// EINDE LAMMEREN NA SPENEN
// VOLWASSEN DIEREN
$zoek_aantal_doelgroep3 = mysqli_query($db,"
SELECT count(hin.schaapId) aantin
FROM (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
") or die (mysqli_error($db));
        
    while($nu3 = mysqli_fetch_assoc($zoek_aantal_doelgroep3))
        { $aanwezig3 = $nu3['aantin']; }

if($aanwezig3 > 0) { // Als er volwassen schapenin het verblijf zitten

        $pdf->Ln(7);
        $pdf->SetFont('Times','I',9);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(36,3,'Aantal volwassen schapen : ','',0,'',false);
        $pdf->Cell(10,3,' '.$aanwezig3,'',1,'',false);
        
        $pdf->Ln(7);

    $pdf->SetFont('Times','B',8);
        $pdf->SetFillColor(166,198,235); // blauwe opvulkleur 
        $pdf->SetDrawColor(50,50,100);

        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,'Werknr','',0,'C',false);
        $pdf->Cell(24,3,'Ras','',0,'C',false);
        $pdf->Cell(24,3,'Geslacht','',1,'C',false);

$schapen_vanaf_aanwas = mysqli_query ($db,"
SELECT s.schaapId, right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, r.ras, s.geslacht
FROM tblSchaap s
 left join tblRas r on (r.rasId = s.rasId)
 join (
    SELECT st.schaapId, max(hisId) hisId
    FROM tblStal st 
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (a.actId = h.actId) 
    WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(st.rel_best) and a.aan = 1
    GROUP BY st.schaapId
 ) hin on (hin.schaapId = s.schaapId)
 left join tblBezet b on (hin.hisId = b.hisId)
 left join (
    select b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    from tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId)
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    group by b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.hisv = hin.hisId)
 join (
    SELECT st.schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = hin.schaapId)
WHERE (isnull(b.hokId) or uit.hist is not null)
ORDER BY right(s.levensnummer,".mysqli_real_escape_string($db,$Karwerk).")
") or die (mysqli_error($db));

while($row = mysqli_fetch_array($schapen_vanaf_aanwas))
        {
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];


       $pdf->SetFont('Times','',8);
       $pdf->SetDrawColor(200,200,200); // Grijs
        $pdf->Cell(5,3,'','',0,'',false);
        $pdf->Cell(24,3,$werknr,'T',0,'C',false);
        $pdf->Cell(24,3,$ras,'T',0,'C',false);
        $pdf->Cell(24,3,$geslacht,'T',1,'C',false);
    }

} // Einde if($aanwezig3 > 0)
// EINDE VOLWASSEN DIEREN



/****** EINDE BODY ******/


$pdf->Output($rapport.".pdf","D");
