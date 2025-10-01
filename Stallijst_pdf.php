<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

include "just_connect_db.php";

//$ooi = $_GET['Id'];

$rapport = 'Stallijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

Session::start();

    $lidId = Session::get('I1');

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
        {        $stapel = $rij['aant'];        }

/* Totalen lammeren, ooien en rammen */

    $schaap_gateway = new SchaapGateway($db);
$lammer = $schaap_gateway->aantalLamOpStal($lidId);
$moeders = $schaap_gateway->aantalOoiOpStal($lidId);
$vaders = $schaap_gateway->aantalRamOpStal($lidId);

/* EInde Totalen lammeren, ooien en rammen */

$pdf = new StallijstPdf($Afdrukstand,'mm','A4'); //use new class

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
