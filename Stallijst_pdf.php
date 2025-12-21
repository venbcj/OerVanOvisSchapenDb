<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url */

include "just_connect_db.php";

//$ooi = $_GET['Id'];

$rapport = 'Stallijst';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') {
    $headerWidth = 190;
    $imageWidth = 169;
}
if ($Afdrukstand == 'L') {
    $headerWidth = 277;
    $imageWidth = 256;
}

Session::start();

$lidId = Session::get('I1');
$lid_gateway = new LidGateway();
$Karwerk = $lid_gateway->zoek_karwerk($lidId);
$schaap_gateway = new SchaapGateway();
// NOTE: query lijkt erg op countByStalFase, alleen is die nauwer: hier zit niets over sekse of ouder.
// Naam is voor verbetering vatbaar --BCB
$stapel = $schaap_gateway->countByStal($lidId);
$lammer = $schaap_gateway->aantalLamOpStal($lidId);
$moeders = $schaap_gateway->aantalOoiOpStal($lidId);
$vaders = $schaap_gateway->aantalRamOpStal($lidId);
// ... je doet niets met deze gegevens ? @TODO: #0004213
$pdf = new StallijstPdf($Afdrukstand, 'mm', 'A4'); //use new class
$pdf->AliasNbPages('{pages}');
$pdf->AddPage();

$result = $schaap_gateway->stallijstgegevens($lidId, $Karwerk);
while ($row = $result->fetch_array()) {
    $werknr = $row['werknum'];
    $levnr = $row['levensnummer'];
    $datum = $row['gebdm'];
    $geslacht = $row['geslacht'];
    $aanw = $row['aanw'];
    if (isset($aanw)) {
        if ($geslacht == 'ooi') {
            $fase = 'moeder';
        } elseif ($geslacht == 'ram') {
            $fase = 'vader';
        }
    } else {
        $fase = 'lam';
    }
    $pdf->SetFont('Times', '', 8);
    $pdf->SetDrawColor(200, 200, 200); // Grijs
    $pdf->Cell(45, 5, '', '', 0, '', false);
    $pdf->Cell(15, 5, $werknr, 'TB', 0, 'C', false);
    $pdf->Cell(25, 5, $levnr, 'TB', 0, '', false);
    $pdf->Cell(15, 5, $datum, 'TB', 0, 'C', false);
    $pdf->Cell(15, 5, $geslacht, 'TB', 0, 'C', false);
    $pdf->Cell(15, 5, $fase, 'TB', 1, 'C', false);
}
$pdf->Output($rapport . ".pdf", "D");
