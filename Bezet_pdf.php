<?php

require_once("autoload.php");
//https://www.youtube.com/watch?v=CamDi3Syjy4
/* 20-12-2019 tabelnaam gewijzigd van UIT naar uit tabelnaam
09-05-2022 : Werknrs gesorteerd en sql beveiligd met quotes
30-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie
03-03-2024 : Laatst gewogen gewicht toegevoegd
11-03-2024 : Bij geneste query uit
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1
*/
include "just_connect_db.php";
if (isset($_GET['Id'])) {
    $hokId = $_GET['Id'];
}
 // via pagina Bezet.php bestaat $_GET['Id'] niet
$rapport = 'Verblijf';
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
$pdf = new BezetPdf($Afdrukstand, 'mm', 'A4'); //use new class
$pdf->AliasNbPages('
{
pages
}
');
$pdf->AddPage();
/****** BODY ******/
$pdf->SetDrawColor(200, 200, 200); // Grijs
if (!isset($hokId)) {
    $bezet_gateway = new BezetGateway();
    $zoek_verblijven_in_gebruik = $bezet_gateway->zoek_verblijven_in_gebruik_bezet($lidId);
    $doorloop_verblijf = $zoek_verblijven_in_gebruik;
} else {
    $hok_gateway = new HokGateway();
    $zoek_verblijf_gegevens = $hok_gateway->zoek_verblijf_gegevens($hokId);
    $doorloop_verblijf = $zoek_verblijf_gegevens;
}
$i = 1;
while ($row = $doorloop_verblijf->fetch_assoc()) {
    $hokId = $row['hokId'];
    $hok = $row['hoknr'];
    if ($i > 1) {
        $pdf->AddPage();
    }
 // hier wordt een nieuwe pagina gemaakt
    $i++;
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(75, 3, '', '', 0, '', false);
    $pdf->Cell(30, 3, $hok, '', 1, '', false);
    $bezet_gateway = new BezetGateway();
    $nu_geb = $bezet_gateway->zoek_nu_in_verblijf_geb($hokId);
    if ($nu_geb > 0) {
 // Als er lammeren voor spenen in het verblijf zitten
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(40, 3, 'Aantal lammeren voor spenen : ', '', 0, '', false);
        $pdf->Cell(10, 3, ' ' . $nu_geb, '', 1, '', false);
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'B', 8);
        $pdf->SetFillColor(166, 198, 235); // blauwe opvulkleur
        $pdf->SetDrawColor(50, 50, 100);
    // kopregel 1
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'Laatst', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 1, 'C', false);
    // kopregel 2
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewogen', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Fictieve', '', 1, 'C', false);
    // kopregel 3
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, 'Werknr', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewicht (kg)', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Ras', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geslacht', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geboortedatum', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Datum in verblijf', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'speendatum', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Moeder', '', 1, 'C', false);
        $bezet_gateway = new BezetGateway();
        $hok_inhoud_geb = $bezet_gateway->hok_inhoud_geb($Karwerk, $hokId);
        while ($row = $hok_inhoud_geb->fetch_array()) {
                 $werknr = $row['werknr'];
                 $lstkg = $row['lstkg'];
                 $ras = $row['ras'];
                 $geslacht = $row['geslacht'];
                 $vanaf = $row['van'];
                 $gebdm = $row['geb'];
                 $geslacht = $row['geslacht'];
                 $ficdm = $row['ficspn'];
                 $moeder = $row['mdr'];
               $pdf->SetFont('Times', '', 8);
               $pdf->SetDrawColor(200, 200, 200); // Grijs
                $pdf->Cell(5, 3, '', '', 0, '', false);
                $pdf->Cell(24, 3, $werknr, 'T', 0, 'C', false);
                $pdf->Cell(12, 3, $lstkg, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $ras, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $geslacht, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $gebdm, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $vanaf, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $ficdm, 'T', 0, 'C', false);
                $pdf->Cell(24, 3, $moeder, 'T', 1, 'C', false);
        }
    }
    $bezet_gateway = new BezetGateway();
    $nu_spn = $bezet_gateway->zoek_nu_in_verblijf_spn($hokId);
    if ($nu_spn > 0) {
     // Als er lammeren na spenen in het verblijf zitten
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(38, 3, 'Aantal lammeren na spenen : ', '', 0, '', false);
        $pdf->Cell(10, 3, ' ' . $nu_spn, '', 1, '', false);
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'B', 8);
        $pdf->SetFillColor(166, 198, 235); // blauwe opvulkleur
        $pdf->SetDrawColor(50, 50, 100);
        // kopregel 1
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'Laatst', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 1, 'C', false);
        // kopregel 2
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewogen', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Fictieve', '', 1, 'C', false);
        // kopregel 3
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, 'Werknr', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewicht (kg)', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Ras', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geslacht', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geboortedatum', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Datum in verblijf', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'afleverdatum', '', 1, 'C', false);
        $bezet_gateway = new BezetGateway();
        $hok_inhoud_spn = $bezet_gateway->hok_inhoud_spn($Karwerk, $hokId);
        while ($row = $hok_inhoud_spn->fetch_array()) {
             $werknr = $row['werknr'];
             $lstkg = $row['lstkg'];
             $ras = $row['ras'];
             $geslacht = $row['geslacht'];
             $gebdm = $row['geb'];
             $vanaf = $row['van'];
             $ficdm = $row['ficafv'];
               $pdf->SetFont('Times', '', 8);
               $pdf->SetDrawColor(200, 200, 200); // Grijs
            $pdf->Cell(5, 3, '', '', 0, '', false);
            $pdf->Cell(24, 3, $werknr, 'T', 0, 'C', false);
            $pdf->Cell(12, 3, $lstkg, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $ras, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $geslacht, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $gebdm, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $vanaf, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $ficdm, 'T', 1, 'C', false);
        }
    }
    $bezet_gateway = new BezetGateway();
    $nu_prnt = $bezet_gateway->zoek_nu_in_verblijf_prnt_pdf($hokId);
    if ($nu_prnt > 0) {
     // Als er volwassen schapenin het verblijf zitten
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'I', 9);
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(36, 3, 'Aantal volwassen schapen : ', '', 0, '', false);
        $pdf->Cell(10, 3, ' ' . $nu_prnt, '', 1, '', false);
        $pdf->Ln(7);
        $pdf->SetFont('Times', 'B', 8);
        $pdf->SetFillColor(166, 198, 235); // blauwe opvulkleur
        $pdf->SetDrawColor(50, 50, 100);
        // kopregel 1
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'Laatst', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 1, 'C', false);
        // kopregel 2
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewogen', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 0, 'C', false);
        $pdf->Cell(24, 3, '', '', 1, 'C', false);
        // kopregel 3
        $pdf->Cell(5, 3, '', '', 0, '', false);
        $pdf->Cell(24, 3, 'Werknr', '', 0, 'C', false);
        $pdf->Cell(12, 3, 'gewicht (kg)', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Ras', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geslacht', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Geboortedatum', '', 0, 'C', false);
        $pdf->Cell(24, 3, 'Datum in verblijf', '', 1, 'C', false);
        $bezet_gateway = new BezetGateway();
        $hok_inhoud_vanaf_aanwas = $bezet_gateway->hok_inhoud_vanaf_aanwas($Karwerk, $hokId);
        while ($row = $hok_inhoud_vanaf_aanwas->fetch_array()) {
             $werknr = $row['werknr'];
             $ras = $row['ras'];
             $geslacht = $row['geslacht'];
             $gebdm = $row['geb'];
             $vanaf = $row['van'];
             $lstkg = $row['lstkg'];
               $pdf->SetFont('Times', '', 8);
               $pdf->SetDrawColor(200, 200, 200); // Grijs
            $pdf->Cell(5, 3, '', '', 0, '', false);
            $pdf->Cell(24, 3, $werknr, 'T', 0, 'C', false);
            $pdf->Cell(12, 3, $lstkg, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $ras, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $geslacht, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $gebdm, 'T', 0, 'C', false);
            $pdf->Cell(24, 3, $vanaf, 'T', 1, 'C', false);
        }
    }
 // Einde if($nu_prnt > 0)
// EINDE VOLWASSEN DIEREN
}
 // Einde fetch_assoc($zoek_verblijven_in_gebruik)
/****** EINDE BODY ******/
$pdf->Output($rapport . ".pdf", "D");
