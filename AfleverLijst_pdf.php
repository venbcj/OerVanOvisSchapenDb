<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
31-7-2020; wdgn gewijzigd in wdgn_v
30-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes
07-07-2024 : Werknr oplopend gesorteerd
19-03-2025 : Gewicht toegevoegd */

include "just_connect_db.php";

$his = $_GET['hisId'];
$rapport = 'Afleverlijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') {
    $headerWidth = 190;
    $imageWidth = 169;
}
if ($Afdrukstand == 'L') {
    $headerWidth = 277;
    $imageWidth = 256;
}
$historie_gateway = new HistorieGateway();
$zoek = $historie_gateway->zoek_afleverlijst($his);
while ($row = $zoek->fetch_assoc()) {
    $lidId = $row['lidId'];
    $afvDate = $row['date'];
    $afvDatum = $row['datum'];
    $relId = $row['rel_best'];
    $bestemming = $row['naam'];
}
$lid_gateway = new LidGateway();
$Karwerk = $lid_gateway->zoek_karwerk($lidId);
$schpn = $historie_gateway->count_afleverlijst($lidId, $afvDate, $relId, $Karwerk);
//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm
$pdf = new AfleverlijstPdf($Afdrukstand, 'mm', 'A4'); //use new class
//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');
$pdf->AddPage();
$pdf->SetFont('Times', '', 6);
$pdf->SetDrawColor(200, 200, 200); // Grijs
$zoek_schaap = $historie_gateway->zoek_schaap($lidId, $afvDate, $relId, $Karwerk);
while ($data = mysqli_fetch_array($zoek_schaap)) {
    $levnr_new = '';
    if (isset($levnr_new) && $levnr_new <> $data['levensnummer']) {
        $border = 'T';
        $pdf->Cell(23, 5, $data['levensnummer'], $border, 0);
        $pdf->Cell(15, 5, $data['werknr'], $border, 0, 'C');  // C = center
        $pdf->Cell(15, 5, $data['kg'], $border, 0, 'C');
    } else {
        $border = '';
        $pdf->Cell(23, 5, '', $border, 0);
        $pdf->Cell(15, 5, '', $border, 0);
        $pdf->Cell(15, 5, '', $border, 0);
    }
    
    $pdf->Cell(30, 5, $data['naam'], $border, 0);
    $pdf->Cell(18, 5, $data['datum'], $border, 0);
     $pdf->Cell(20, 5, $data['wdgn_v'], $border, 1, 'C');
    $levnr_new = $data['levensnummer'];
}
$pdf->Output($rapport . "_" . $afvDatum . ".pdf", "D");
