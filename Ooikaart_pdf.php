<?php

require_once("autoload.php");

//https://www.youtube.com/watch?v=CamDi3Syjy4

include "just_connect_db.php";

$ooi = $_GET['Id'];

$rapport = 'Ooikaart';
$Afdrukstand = 'L';
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
$pdf = new OoikaartPdf($Afdrukstand, 'mm', 'A4'); //use new class

$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

/****** BODY ******/

/* Gegevens moederdier */

$pdf->SetFont('Times', 'B', 12);
$pdf->SetDrawColor(200, 200, 200); // Grijs
$pdf->Cell(80, 5, 'Moederdier', '', 1, 'C', false);
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(80, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aantal', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aantal', 0, 0, 'C', false);
$pdf->Cell(15, 3, '%', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gem.', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gem.', 0, 0, 'C', false);
$pdf->Cell(20, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gem.', 0, 1, 'C', false);

$pdf->Cell(60, 3, '', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'Geboorte', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'dagen', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aantal', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'levend', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'levend', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aantal', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aantal', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'geboorte', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'speen', 0, 0, 'C', false);
$pdf->Cell(20, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'aflever', 0, 1, 'C', false);

$pdf->Cell(10, 3, '', '', 0, 'C', false);
$pdf->Cell(25, 3, 'Levensnummer', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Werknr', 0, 0, 'C', false);
$pdf->Cell(10, 3, 'Ras', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'datum', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'moeder', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'lammeren', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'geboren', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'geboren', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'ooien', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'rammen', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'gewicht', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gespeend', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'gewicht', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'Afgeleverd', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'gewicht', '', 1, 'C', false);

$pdf->Ln(1);

$schaap_gateway = new SchaapGateway();
$zoek_moederdier = $schaap_gateway->zoek_moederdier($lidId, $Karwerk, $ooi);
while ($row = $zoek_moederdier->fetch_assoc()) {
    $levnr = $row['levensnummer'];
    $werknr = $row['werknr'];
    $ras = $row['ras'];
    $gebdm = $row['geb_datum'];
    $aanvdm = $row['aanvoerdm'];
    if (isset($gebdm)) {
        $opdm = $gebdm;
    } else {
        $opdm = $aanvdm;
    }
    $dagen = $row['dagen'];
    $lammeren = $row['lammeren'];
    $levend = $row['levend'];
    $percleven = $row['percleven'];
    $aantooi = $row['aantooi'];
    $aantram = $row['aantram'];
    $gemkg = $row['gemgewicht'];
    $aantspn = $row['aantspn'];
    $gemspn = $row['gemspnkg'];
    $aantafv = $row['aantafv'];
    $gemafv = $row['gemafvkg'];

    $pdf->SetFont('Times', '', 8);
    $pdf->Cell(10, 10, '', '', 0, '', false);
    $pdf->Cell(25, 10, $levnr, 'TB', 0, '', false);
    $pdf->Cell(15, 10, $werknr, 'TB', 0, 'C', false);
    $pdf->Cell(10, 10, $ras, 'TB', 0, 'C', false);
    $pdf->Cell(20, 10, $gebdm, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $dagen, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $lammeren, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $levend, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $percleven, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $aantooi, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $aantram, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $gemkg, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $aantspn, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $gemspn, 'TB', 0, 'C', false);
    $pdf->Cell(20, 10, $aantafv, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $gemafv, 'TB', 1, 'C', false);
}
/* Einde Gegevens moederdier */

$pdf->Ln(10);

/* Gegevens lammeren van moederdier */

$pdf->SetFont('Times', 'B', 12);
$pdf->Cell(80, 5, 'Lammeren van moederdier', '', 1, 'C', false);
$pdf->Ln(5);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(160, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gem.', 0, 0, 'C', false);
$pdf->Cell(45, 3, '', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'Gem.', 0, 1, 'C', false);

$pdf->Cell(130, 3, '', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Speen', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Speen', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'groei', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aflever', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Aflever', 0, 0, 'C', false);
$pdf->Cell(15, 3, '', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'groei', 0, 1, 'C', false);

$pdf->Cell(10, 3, '', '', 0, 'C', false);
$pdf->Cell(25, 3, 'Levensnummer', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Werknr', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Generatie', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Geslacht', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'Ras', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Geboren', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Gewicht', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'datum', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'gewicht', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'spenen', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'datum', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'gewicht', 0, 0, 'C', false);
$pdf->Cell(15, 3, 'Reden', 0, 0, 'C', false);
$pdf->Cell(20, 3, 'afleveren', 0, 1, 'C', false);

$pdf->Ln(1);

$zoek_lammeren = $schaap_gateway->zoek_lammeren($lidId, $ooi, $Karwerk);
while ($lam = $zoek_lammeren->fetch_assoc()) {
    if (empty($lam['levensnummer'])) {
        $Llevnr = 'Geen';
    } else {
        $Llevnr = $lam['levensnummer'];
    }
    $Lwerknr = $lam['werknr'];
    $Lsekse = $lam['geslacht'];
    $Ldmaanw = $lam['dmaanw'];
    if (isset($Ldmaanw)) {
        if ($Lsekse == 'ooi') {
            $Lfase = 'moeder';
        }
        if ($Lsekse == 'ram') {
            $Lfase = 'vader';
        }
    } else {
        $Lfase = 'lam';
    }
    $Lras = $lam['ras'];
    $Ldatum = $lam['gebrndm'];
    $Lkg = $lam['gebrnkg'];
    $Lspndm = $lam['speendm'];
    $Lspnkg = $lam['speenkg'];
    $gemgr_s = $lam['gemgr_s'];
    $Lafvdm = $lam['afvdm'];
    $Lafvkg = $lam['afvkg'];
    $Luitvdm = $lam['uitvaldm'];
    $Lreden = $lam['reden'];
    $gemgr_a = $lam['gemgr_a'];

    $pdf->SetFont('Times', '', 8);
    $pdf->Cell(10, 10, '', '', 0, '', false);
    $pdf->Cell(25, 10, $Llevnr, 'TB', 0, '', false);
    $pdf->Cell(15, 10, $Lwerknr, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lfase, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lsekse, 'TB', 0, 'C', false);
    $pdf->Cell(20, 10, $Lras, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Ldatum, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lkg, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lspndm, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lspnkg, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $gemgr_s, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lafvdm, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lafvkg, 'TB', 0, 'C', false);
    $pdf->Cell(15, 10, $Lreden, 'TB', 0, 'C', false);
    $pdf->Cell(20, 10, $gemgr_a, 'TB', 1, 'C', false);
    //        $pdf->Cell(15,10,$gemafv,'TB',1,'C',false);
}
/* Einde Gegevens lammeren van moederdier */

/****** EINDE BODY ******/

$pdf->Output($rapport . ".pdf", "D");
