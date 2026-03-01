<?php

/* post_readerGeb.php toegepast in :
- InsGeboortes.php
<!-- 11-8-2014 : veld type gewijzigd in fase
 16-11-2014 include maak_request toegevoegd
 17-09-2016 : modules gesplitst
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel  22-1-2017 : tblBezetting gewijzigd naar tblBezet
 18-2-2017 : Controle op startdatum moeder toegevoegd
 28-2-2017 : Ras en gewicht niet veplicht gemaakt
 28-4-2017 : dmafv_mdr bij elke regel leeg gemaakt. Dit veroorzaakte het afbreken van 30 regels bij ± 7 regels.
 28-2-2018 : Opslaan dood geboren toegevoegd
 24-6-2018 : uitvaldatum verwijderd
 16-3-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
  9-5-2020 : Worpverloop toegevoegd aan tblVolwas
 30-5-2020 : Veld moment opslaan ook bij reader Agrident t.b.v. taak Dood geboren
 13-7-2020 : impGeboortes vervangen door impAgrident 18-7 juiste uitvaldatum vastgelegd nl. txtUitvaldm anders txtDatum
 23-1-2021 : transponder toegevoegd. Sql beveiligd met quotes. Verschil tussen kiezen of verwijderen herschreven 30-1 mdrId gewijzigd naar moederId
 07-02-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen
 10-01-2022 : Code aangepast n.a.v. registratie dekkingen en dracht
 20-05-2023 : De datum van dekken (datum in tblVolwas) wordt vanaf nu vastgelegd in tblHistorie met actId 18
 11-07-2025 : Opslaan van ubn in tblStal toegevoegd. 29-8-2025 : Bij gebruikers zonder module technisch wordt ubn niet o.b.v. moederdier bepaald.
-->
 */

$ubn = 'FIXME'; // nodig in doodgeboren-scenario, zo te zien, maar niet gezet.
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}

$bezet_gateway = new BezetGateway();
$historie_gateway = new HistorieGateway();
$impagrident_gateway = new ImpAgridentGateway();
$schaap_gateway = new SchaapGateway();
$stal_gateway = new StalGateway();
$ubn_gateway = new UbnGateway();
$volwas_gateway = new VolwasGateway();
$hok_gateway = new HokGateway();

foreach ($array as $recId => $id) {
    if (!$recId) {
        continue;
    }
    unset($levnr_rd);
    unset($fldRas);
    unset($fldSekse);
    unset($fldKg);
    unset($fldStalIdMdr);
    unset($fldHokMdr);
    unset($fldMom);
    unset($fldUitvdag);
    unset($fldRed);

    foreach ($id as $key => $value) {
        if ($key == 'chbkies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtDatum' && !empty($value)) {
            $dag = date_create($value);
            $fldDag = date_format($dag, 'Y-m-d');
            $Dagberekening = strtotime($fldDag);
            $fldDrachtDay = date('Y-m-d', strtotime("-145 day", $Dagberekening));
        }
        if ($key == 'kzlRas' && !empty($value)) {
            $fldRas = $value;
        }
        if ($key == 'kzlSekse' && !empty($value)) {
            $fldSekse = $value;
        }
        if ($key == 'txtKg' && !empty($value)) {
            $fldKg = str_replace(',', '.', $value);
        }
        if ($key == 'kzlOoi' && !empty($value)) {
            $fldStalIdMdr = $value;
        }
        if ($key == 'kzlHokLam' && !empty($value)) {
            $fldHokLam = $value;
        }
        if ($key == 'kzlHokMdr' && !empty($value)) {
            $fldHokMdr = $value;
        }
        if ($key == 'kzlMom' && !empty($value)) {
            $fldMom = $value;
        } elseif ($key == 'kzlMom' && empty($value)) {
            $fldMom = '' ;
        }
        // dit wordt niet in deze code gebruikt. Wel in InsGeboortes, maar of het *deze* waarde is, vind ik zo moeilijk te zeggen
        if ($key == 'txtUitvaldm' && !empty($value)) {
            $uitvdag = date_create($value);
            $fldUitvdag = date_format($uitvdag, 'Y-m-d');
        }
        if ($key == 'kzlRed' && !empty($value)) {
            $fldRed = $value;
        }
    }
    // (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    unset($verwerkt);
    $verwerkt = $impagrident_gateway->zoek_readerRegel_verwerkt($recId);
    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {
        if (isset($fldStalIdMdr)) {
            [$mdrId, $ubnId] = $stal_gateway->zoek_schaapId_ubnId_moeder($fldStalIdMdr);
            $dmaanv_1_mdr = $stal_gateway->zoek_eerste_aanvoerdatum_moeder($lidId, $mdrId);
        // TODO: slaat dit commentaar nog ergens op?
          // bij aankoop incl. geboortedatum wordt geboortedatum niet getoond
            unset($dmafv_mdr);
            $dmafv_mdr = $stal_gateway->query_datum_afvoer_moeder($lidId, $mdrId);
        } else {
    // TODO is de ariteit juist? Een lid zou toch meerdere ubn-records kunnen hebben?
    // TODO hadden we hier ook $ubn zullen declareren?
            $ubnId = $ubn_gateway->zoek_ubnId($lidId);
        }
          [$tran, $levnr_rd, $moeder, $mdrTran_rd] = $impagrident_gateway->zoek_levensnummer_transponder($recId);
    // Transponder moeder inlezen als deze niet bestaat in tblSchaap
        $mdrTran_sch = $mdrTran_rd;
        [$moederId, $mdrTran_sch] = $schaap_gateway->zoek_transp_moeder($moeder);
        if ($mdrTran_rd <> $mdrTran_sch) {
            $schaap_gateway->update_tblSchaap($mdrTran_rd, $moederId);
        }
    // Einde Transponder moeder inlezen als deze niet bestaat in tblSchaap
    /* ********************
    //    BEPAAL VOLWID
    // ********************
    Stap 1 : Eerst wordt o.b.v. moeder en geboortedatum van lam een actuele worp gezocht. Dus volwId in tblVolwas die al is gekoppeld aan een ander lam.
    Stap 2 : Als deze niet bestaat wordt er gezocht naar een drachtige ooi zonder worp. Na de laatste worp van de ooi moet er nog een dracht zijn geregistreerd die niet ouder is dan 145 dagen.
    Stap 3 : Als een drachtige ooi zonder worp niet bestaat wordt er gezocht op een actuele dekking die dus nog niet heeft geworpen. Na de laatste worp van de ooi moet er nog een dekking zijn geregistreerd die niet ouder is dan 145 dagen.
    Stap 4 : Als deze dekking niet bestaat wordt een nieuw koppel (vader al dan niet bekend) aangemaakt in tblVolwas. Daarna wordt een fictieve dekdatum vastgelegd in tblHistorie met actId 18 (Dekken) zijnde geboortedatum - 145 dagen.
    Een fictieve drachtdatum wordt niet vastgelegd. Deze moet reeds bestaan anders wordt deze niet met terugwerkende kracht aangemaakt.
    */
        if (isset($fldStalIdMdr)) {
            unset($volwId);
            $volwId = $schaap_gateway->zoek_huidige_worp_geb($mdrId, $fldDag);
        // Stap 2 is overbodig. Als er een drachtige ooi bestaat moet deze hetzelfde volwId hebben als de volwId o.b.v. de laatste dekking zonder worp. Aan die volwId hangt immers de drachtregistratie.
            if (!isset($volwId)) {
                $volwId = $volwas_gateway->zoek_actuele_dekking_binnen_145dagen($mdrId, $fldDag);
            } // EInde Stap 3 : Zoek actuele dekking zonder worp en zonder registratie dracht
            if (!isset($volwId)) {
                $hisId = $historie_gateway->insert_tblHistorie_18($fldStalIdMdr, $fldDrachtDay);
                $volwId = $volwas_gateway->insert_tblVolwas($hisId, $mdrId);
            }
            $verloop_rd = $impagrident_gateway->zoek_worpverloop_reader($recId);
            $verloop_db = $volwas_gateway->zoek_worpverloop_db($volwId);
            if (!isset($verloop_db) && isset($verloop_rd)) {
                $volwas_gateway->updateVerloop($verloop_rd, $volwId);
            }
        // Einde Worpverloop vastleggen
        }
    // ********************
    // EINDE BEPAAL VOLWID
    // ********************
    // ***************************
    //    GEGEVENS INLEZEN
    // ***************************
        unset($rel_best);
        if (
            (
            isset($fldDag)
            && isset($levnr_rd)
            && isset($fldStalIdMdr)
            && $fldDag >= $dmaanv_1_mdr
            && (!isset($dmafv_mdr) || $fldDag <= $dmafv_mdr)
            && isset($fldHokLam)
            )
            // Veplichte velden bij module Technisch. Moeder is verplicht bij module technisch
            || (
            $modtech == 0
            && isset($fldDag)
            && isset($levnr_rd)
            )
            // Veplichte velden zonder module Technisch
        ) {
            $scenario = 'Geboren_lam';
        } elseif (
            !isset($levnr_rd)
            && isset($fldDag)
            && (
            (isset($fldStalIdMdr) && $modtech == 1)
            || ($modtech == 0)
            )
        ) {
            $scenario = 'Dood_geboren';
            $rel_best = $rendac_Id; // werd gezet in login_logic
            $levnr_rd = $ubn; // deze variabele bestaat niet :(
        }
    #  refactor-opzetje
    #  if (!function_exists('bepaal_scenario')) {
    #  function bepaal_scenario($input) {
    #      extract($input);
    #  if (
    #      (
    #          isset($fldDag)
    #          && isset($levnr_rd)
    #          && isset($fldStalIdMdr)
    #          && $fldDag >= $dmaanv_1_mdr
    #          && (!isset($dmafv_mdr) || $fldDag <= $dmafv_mdr)
    #          && isset($fldHok)
    #      )
    #      // Veplichte velden bij module Technisch. Moeder is verplicht bij module technisch
    #      || (
    #          $modtech == 0
    #          && isset($fldDag)
    #          && isset($levnr_rd)
    #      )
    #      // Veplichte velden zonder module Technisch
    #  ) {
    #      $scenario = 'Geboren_lam';
    #  } else if(
    #      !isset($levnr_rd)
    #      && isset($fldDag)
    #      && (
    #          (isset($fldStalIdMdr) && $modtech == 1)
    #          || ($modtech == 0)
    #      )
    #  ) {
    #      $scenario = 'Dood_geboren';
    #      $rel_best = $rendac_Id;
    #      $levnr_rd = $ubn;
    #    }
    #  return [
    #      'scenario' => $scenario ?? null,
    #      'rel_best' => $rel_best ?? null,
    #      'fldLevnr' => $levnr_rd ?? null,
    #  ];
    #  }
    #  }
    #  // jammer dus: doordat niet alle variabelen gezet hoeven zijn, werkt de compact-aanpak niet
    #  // $decision_inputs = compact(explode(' ', 'fldDag fldLevnr fldStalIdMdr dmaanv_1_mdr dmafv_mdr fldHok modtech ubn rendac_Id scenario'));
    #  $decision_inputs = [
    #      'fldDag'        => $fldDag        ?? null,
    #      'fldLevnr'      => $levnr_rd      ?? null,
    #      'fldStalIdMdr'  => $fldStalIdMdr  ?? null,
    #      'fldHok'        => $fldHok        ?? null,
    #      'modtech'      => $modtech        ?? null,
    #      'dmaanv_1_mdr'  => $dmaanv_1_mdr  ?? null,
    #      'dmafv_mdr'     => $dmafv_mdr     ?? null,
    #      'ubn'           => $ubn           ?? null,
    #      'rendac_Id'     => $rendac_Id     ?? null,
    #  ];
    #  $shadow = bepaal_scenario($decision_inputs);
    #  $scenario = $shadow['scenario'];
    #  $rel_best = $shadow['rel_best'];
    #  $levnr_rd = $shadow['fldLevnr'];
    // TODO: *welk* scenario het is, doet kennelijk niet terzake? Als je dan toch iets uitrekent, vertrouw er dan vervolgens op.
        if (isset($scenario)) {
            // NOTE dit is de enige plek voor fldMom
            $schaapId = $schaap_gateway->insert_tblSchaap($levnr_rd, $fldRas, $fldSekse, $volwId, $fldMom, $fldRed, $tran);
            if (isset($schaapId) && isset($rel_best)) {
                $schaap_gateway->wis_levensnummer_by_id($schaapId);
                unset($levnr);
            }
            $stalId = $stal_gateway->insert_tblStal($lidId, $ubnId, $schaapId, $rel_best ?? null);
            $hisId = $historie_gateway->insert_tblHistorie_geb($stalId, $fldDag, $fldKg);
        // t.b.v. tblBezet en/of tblMelding
            if (isset($rel_best)) {
                // Bij doodgeboren
                if (isset($fldUitvdag)) {
                    $doodday = $fldUitvdag;
                } else {
                    $doodday = $fldDag;
                }
                $historie_gateway->insert_tblHistorie_14($stalId, $doodday);
            }
        // Einde Insert tblHistorie
            if ($modtech == 1 && !isset($rel_best)) {
                $insert_tblBezet = $bezet_gateway->insert_tblBezet($hisId, $fldHokLam);
            




// Moeder volgt lam naar hetzelfde verblijf als het lam mits het lam niet naar Lambar gaat. Er wordt eerst gekeken in welk verblijf de moeder voor het laatst is geplaatst. Mogelijk zit de ooi al is het verblijf van het lam.
          unset($hokIdOoi_in);

$zoek_hokId_Lambar = $hok_gateway->zoek_lambar($lidId);

$zhl = mysqli_fetch_assoc($zoek_hokId_Lambar); $hokId_lambar = $zhl['hokId'];


if(isset($fldHokMdr)) { // kijk of moeder nu in een verblijf zit en zoek naar een terugwerkende kracht mutatie
$zoek_laatste_verblijf_moeder = $hok_gateway->zoek_laatste_verblijf_moeder($fldStalIdMdr);

$zlvm = mysqli_fetch_assoc($zoek_laatste_verblijf_moeder); 
  $hokIdOoi_in = $zlvm['hokId'];
  $day_in = $zlvm['day_in'];
  $actId_in = $zlvm['actId_in'];
  $actId_uit = $zlvm['actId_uit'];
  $day_uit = $zlvm['day_uit'];
  $ooi_af = $zlvm['af'];

if($hokIdOoi_in <> $fldHokMdr) { // Bij meerdere lammeren hoeft moeder maar 1x te worden overgplaatst. Na eerste lam is $hokIdOoi_in == $fldHokMdr

unset($actId);
if( (empty($hokIdOoi_in) /* Betreft scenario 7 uit incident 0004244 */) || ($ooi_af == 0 && !empty($day_uit) && $day_uit <= $fldDag) /*scenario 4 uit incident 0004244 */)     { $actId = 6; }         
else if($day_in > $fldDag)  { $actId = $actId_in; } /* Betreft terugwerkende kracht mutatie zie scenario 1 uit incident 0004244 */
else { $actId = 5; } // uit incident 0004244 scenario 3, 5 en 6

if(isset($actId)) { // Moeder (over)plaatsen in verblijf
  $hisIdOoi = $historie_gateway->insert_tblHistorie($fldStalIdMdr, $fldDag, $actId);

  $insert_tblBezet_ooi = $bezet_gateway->insert_tblBezet($hisIdOoi, $fldHokMdr); 

} // Einde Moeder (over)plaatsen in verblijf

} // Einde if($hokIdOoi_in <> $fldHokMdr)

} // Einde if(isset($fldHokMdr))
// Einde Moeder volgt lam naar hetzelfde verblijf als het lam.











                // @TODO: is de spelling echt "GER", of had dit "GEB" moeten zijn? Nu ja, het is nu al in de hele applicatie "GER". Maak hier constanten voor, of een enum, of een hele klasse --BCB
                $Melding = 'GER'; //geboren
                include "maak_request.php";
            }
            if ($reader == 'Agrident') {
                $impagrident_gateway->updateReaderAgrident($recId);
            } else {
                $impagrident_gateway->updateReaderBiocontrol($recId);
            }
        }
    // ***************************
    //   EINDE GEGEVENS INLEZEN
    // ***************************
    }
    if ($fldKies == 0 && $fldDel == 1) {
        if ($reader == 'Agrident') {
            $impagrident_gateway->updateReaderAgrident($recId);
        } else {
            $updateReader = $impagrident_gateway->updateReaderBiocontrol($recId);
        }
    }
}
