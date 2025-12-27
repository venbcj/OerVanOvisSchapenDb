<?php

/*
<!-- 11-8-2014 : veld type gewijzigd in fase 
 23-11-2014 include Maak_Request toegevoegd 
 9-11-2016 controle op bestaand levensnummer toegevoegd ( isset(schaapId) ) 
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel    22-1-2017 : tblBezetting gewijzigd naar tblBezet 
 1-2-2017 : Halsnummer toegevoegd
11-2-2017 : Mogelijkheid moeders en vaders aan hokken toevoegen 
28-2-2017 :  Ras en gewicht niet veplicht gemaakt 
9-7-2020 : Onderscheid gemaakt tussen reader Agrident en Biocontrol 
5-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. SQL beveiligd met quotes. Verschil tussen kiezen of verwijderen herschreven 
26-11-2022 Invoer geboortedatum toegevoegd 
06-08-2023 : Fout gevonden in update_tblSchaap . WHERE schaapId = schaapId)."' toegevoegd
25-09-2023 : Fout hersteld in  zoek_stalId Van 
    query(db,"SELECT stalId FROM tblStal WHERE lidId = lidId)."' , schaapId = schaapId)."' and isnull(rel_best) ") 
    naar
     (db,"SELECT stalId FROM tblStal WHERE lidId = '".lidId)."' and schaapId = '".schaapId)."' and isnull(rel_best) ")
31-12-2023 : and h.skip = 0 bij een enkele query toegevoegd 
17-04-2025 : conedring impReader (Biocontrol) verwijderd 
13-07-2025 : Opslaan ubn in tblStal toegevoegd 
-->
*/

$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
    if (!$recId) continue;
 unset($fldRas);
 unset($fldSekse);
 unset($fldFase);
 unset($fldGebdag);
 unset($fldHok);
 unset($fldHerk);

  foreach($id as $key => $value) {

if ($key == 'chbkies')   { $fldKies = $value; }
if ($key == 'chbDel')   { $fldDel = $value; }

if ($key == 'txtaanwdm' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
                                $flddag = $valuedag; }
if ($key == 'kzlUbn' && !empty($value)) {  $fldUbn = $value; }

if ($key == 'kzlKleur' && !empty($value)) {  $fldKleur = $value; }
 else if ($key == 'txtKleur' && empty($value)) {  $fldKleur = '' ; }

if ($key == 'txtHnr' && !empty($value)) {  $fldHnr = $value; }
 else if ($key == 'txtHnr' && empty($value)) {  $fldHnr = '' ; }
 
if ($key == 'kzlras' && !empty($value)) {  $fldRas = $value; }
 else if ($key == 'kzlras' && empty($value)) {  $fldRas = '' ; }

if ($key == 'kzlsekse' && !empty($value)) {  $fldSekse = $value; }

if ($key == 'kzlFase' && !empty($value)) {  $fldFase = $value; }

         if($fldFase == 'moeder' && !isset($fldSekse) ) { $fldSekse = 'ooi'; }
     else if($fldFase == 'vader' && !isset($fldSekse) ) { $fldSekse = 'ram'; }
 
 if ($key == 'txtkg' && !empty($value)) {  $fldKg = str_replace(',', '.', $value); }
 else if ($key == 'txtkg' && empty($value)) {  $fldKg = ''; }    

 if ($key == 'txtGebdm' && !empty($value)) { $gebDag = date_create($value); $valueGebdag =  date_format($gebDag, 'Y-m-d'); 
                                $fldGebdag = $valueGebdag; }

if ($key == 'kzlHok' && !empty($value)) {  $fldHok = $value; }

if ($key == 'kzlHerk' && !empty($value)) {  $fldHerk = $value; }
     
                                    }

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
 $impagrident_gateway = new ImpAgridentGateway();
 $schaap_gateway = new SchaapGateway();
 $stal_gateway = new StalGateway();
 $historie_gateway = new HistorieGateway();
 $bezet_gateway = new BezetGateway();
 $volwas_gateway = new VolwasGateway();
 $verwerkt = $impagrident_gateway->zoek_readerregel_verwerkt($recId);

if ($fldKies == 1 && $fldDel == 0 && !$verwerkt) { // $verwerkt is een extra controle om dubbele invoer te voorkomen

// Levensnummer ophalen
    [$levnr_rd, $transp_rd] = $impagrident_gateway->zoek_levnr_reader($recId);
$schaapId = $schaap_gateway->zoek_schaapid($levnr_rd);
// TODO: ... en hier doe je niets mee.

// CONTROLE op alle verplichten velden bij AANVOER MOEDER- EN VADERDIEREN
if (isset($flddag) && isset($fldUbn) && isset($levnr_rd) && (
     (($fldFase == 'moeder' && $fldSekse == 'ooi') || ($fldFase == 'vader' && $fldSekse == 'ram') ) 
    ||
    (isset($levnr_db))
    ) )
{
    [$schaapId, $transp_db] = $schaap_gateway->zoek_schaapid_transponder($levnr_rd);
if(!isset($schaapId)) {
    $schaapId = $schaap_gateway->maak_minimaal_schaap($levnr_rd, $fldRas, $fldSekse);
}
if(!isset($transp_db) && isset($transp_rd)) {
    $schaap_gateway->updateTransponder($schaapId, $transp_rd);
}

$stalId = $stal_gateway->insert($lidId, $schaapId, $fldHerk, $fldUbn, $fldKleur, $fldHnr, null);

if(isset($fldGebdag)) {
$historie_gateway->insert_geboorte($stalId, $fldGebdag);
}
  // Insert aanvoer    
$hisId_aanv = $historie_gateway->herstel_invoeren($stalId, $flddag, $fldKg, 2);
if(isset($fldHok)) { 
$bezet_gateway->insert($hisId_aanv, $fldHok);
    }

  // Insert aanwas
$aanwId = $historie_gateway->zoek_aanwasdatum($schaapId)[0];
if(!isset($aanwId)) {    
    $historie_gateway->insert_afvoer_act($stalId, $flddag, 3);
}
  // Einde Insert aanwas
// Einde Insert tblHistorie

$impagrident_gateway->set_verwerkt($recId);

if ($modmeld == 1 ) {
    // Insert tblMeldingen
    $hisId = $historie_gateway->zoek_actId($stalId, 2);
$Melding = 'AAN';
include "maak_request.php";
    // Einde Insert tblMeldingen    
}        
unset($schaapId);
}
// EINDE CONTROLE op alle verplichten velden bij AANVOER MOEDER- EN VADERDIEREN

// CONTROLE op alle verplichten velden bij AANVOER LAMMEREN
if (
 isset($flddag) && isset($fldUbn) && isset($levnr_rd) && $fldFase == 'lam' && 
 ( ($modtech == 1 && isset($fldHok)) || ($modtech == 0) )
)
{
    $schaapId = $schaap_gateway->zoek_schaapid($levnr_rd);
    
if(!isset($schaapId)) { // Als lam nog niet bestaat in tblSchaap
    if($modtech == 1) {
        $volwId = $volwas_gateway->insert($recId, $fldMoeder);
    }
        $schaapId = $schaap_gateway->maak_schaap($levnr_rd, $fldRas, $fldSekse, $volwId, null, null);
    }
    $stalId = $stal_gateway->insert($lidId, $fldUbn, $schaapId, $fldHerk);
// $zoek_hisId is voor tblBezet én tblMelding
$hisId = $historie_gateway->herstel_invoeren($stalId, $flddag, $fldKg, 2);    
if($modtech == 1) {
$bezet_gateway->insert($hisId, $fldHok);    
    }

$impagrident_gateway->set_verwerkt($recId);

if ($modmeld == 1 ) {        // Insert tblMeldingen
$Melding = 'AAN';
include "maak_request.php";
    // Einde Insert tblMeldingen
}    
unset($schaapId);    
unset($periId);    
}
// EINDE CONTROLE op alle verplichten velden bij AANVOER LAMMEREN
    } // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))                    
  if($fldKies == 0 && $fldDel == 1) {    
$impagrident_gateway->set_verwerkt($recId);
    }
unset($levnr_rd);
    } // Einde foreach($array as $recId => $id)
