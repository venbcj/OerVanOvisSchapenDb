<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 www. weggehaald bij url
20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam */

include "just_connect_db.php";

$groep = $_GET['Id'];

$rapport = 'Hoklijst';
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

$pdf = new HoklijstPdf($Afdrukstand, 'mm', 'A4'); //use new class
$pdf->AliasNbPages('{pages}');
$pdf->AddPage();

/****** BODY ******/
$pdf->SetDrawColor(200, 200, 200); // Grijs

$bezet_gateway = new BezetGateway();
if ($groep == 1) {
    $zoek_hok_ingebruik = $bezet_gateway->zoek_hok_ingebruik_geb($lidId);
}
if ($groep == 2) {
    $zoek_hok_ingebruik = $bezet_gateway->zoek_hok_ingebruik_spn($lidId);
}
$i = 1;
while ($hk = mysqli_fetch_assoc($zoek_hok_ingebruik)) {
    $hokId = $hk['hokId'];
    $hok = $hk['hoknr'];
    if ($i > 1) {
        $pdf->AddPage();
    }
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(75, 3, '', '', 0, '', false);
    $pdf->Cell(30, 3, $hok, '', 1, '', false);
    $i++;
    $pdf->Ln(7);
    $pdf->SetFont('Times', 'B', 8);
    $pdf->SetFillColor(166, 198, 235); // blauwe opvulkleur
    $pdf->SetDrawColor(50, 50, 100);
    $pdf->Cell(50, 3, '', '', 0, '', false);
    $pdf->Cell(24, 3, 'Ras', '', 0, '', false);
    $pdf->Cell(14, 3, 'Geslacht', '', 0, '', false);
    $pdf->Cell(30, 3, 'Nu in verblijf', '', 1, '', false);
    if ($groep == 1) {
        $zoek_schapen_in_verblijf = $bezet_gateway->hoklijst_zoek_nu_in_verblijf_geb($hokId);
    }
    if ($groep == 2) {
        $zoek_schapen_in_verblijf = $bezet_gateway->hoklijst_zoek_nu_in_verblijf_spn($hokId);
    }
    while ($n = $zoek_schapen_in_verblijf->fetch_assoc()) {
        $ras = $n['ras'];
        $geslacht = $n['geslacht'];
        $nu = $n['nu'];
        $pdf->SetFont('Times', '', 8);
        $pdf->SetDrawColor(200, 200, 200); // Grijs
        $pdf->Cell(50, 5, '', '', 0, '', false);
        $pdf->Cell(23, 5, $ras, 'T', 0, '', false);
        $pdf->Cell(15, 5, $geslacht, 'T', 0, 'C', false);
        $pdf->Cell(15, 5, $nu, 'T', 1, 'C', false);
    } // Einde fetch_assoc($zoek_schapen_in_verblijf)
} // Einde fetch_assoc($zoek_hok_ingebruik)

/****** EINDE BODY ******/
$pdf->Output($rapport . ".pdf", "D");
