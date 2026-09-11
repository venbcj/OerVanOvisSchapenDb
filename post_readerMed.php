<!--  18-12-2014 : Gemaakt 
 12-03-2017 : Aangpast na.v. Release 2 of wel nieuwe databasestructuur, verwijderen mogelijk gemaakt en mysql verandert in mysqli 
Toegepast in : InsMedicijn.php 
15-11-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol 
24-1-2021 : Sql beveiligd met quotes. Transponder nummer opslaan in tblSchaap als deze nog niet bestaat Verschil tussen kiezen of verwijderen herschreven 31-1 schaapId gewijzigd naar schaapId_db 
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen 
25-6-20221 Bij zoek_totale_voorraad GROUP BY inkId toegevoegd 
22-9-2021 : Functie inlezen_pil toegevoegd -->
<?php

// include url Zit al in InsMedicijn.php
$array = array();

$impagrident_gateway = new ImpAgridentGateway();
$stal_gateway = new StalGateway();
$ubn_gateway = new UbnGateway();
$historie_gateway = new HistorieGateway();
$schaap_gateway = new SchaapGateway();
$artikel_gateway = new ArtikelGateway();

foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach ($array as $recId => $id) {
    if (!$recId) {
        continue;
    }
    //echo '<br>'.'$recId = '.$recId;
    foreach ($id as $key => $value) {
        if ($key == 'chbkies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtDatum' && !empty($value)) {
            $dag = date_create($value);
            $valuedatum =  date_format($dag, 'Y-m-d');
                                    /*echo $key.'='.$valuedatum.' ';*/ $fldDay = $valuedatum;
        }
        if ($key == 'kzlPil' && !empty($value)) {
     /*echo $key.'='.$value.' ';*/ $fldArtId = $value;
        }
        if ($key == 'txtAantal' && !empty($value)) {
     /*echo $key.'='.$value.' ';*/ $fldToedat = $value;
        }
        if ($key == 'kzlReden' && !empty($value)) {
     /*echo $key.'='.$value.' ';*/ $fldReden = $value;
        }
    }
// Transponder nummer inlezen als deze nog niet bestaat in tblSchaap
[$tran_rd, $levnr_rd] = $schaap_gateway->zoek_transponder_reader($recId);

[$schaapId_db, $tran_db] = $schaap_gateway->zoek_transponder($levnr_rd);

        if (isset($schaapId_db) && $tran_rd <> $tran_db) {

            $schaap_gateway->update_transponder_tblSchaap($tran_rd, $schaapId_db);
        }
// Einde Transponder nummer inlezen als deze nog niet bestaat in tblSchaap

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    unset($verwerkt);
$verwerkt = $impagrident_gateway->zoek_readerRegel_verwerkt($recId);
// Einde (extra) controle of readerregel reeds is verwerkt.

    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {
     // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// Controle op uitgeschaarde schapen (moederdieren)
unset($actId, $stalSchaarId, $stalId, $ubnId, $ubn);

if(isset($schaapId_db)) {
    [$actId, $relId, $stalSchaarId] = $stal_gateway->zoek_uitgeschaarde_schaap($lidId, $schaapId_db);
}

if(isset($stalSchaarId)) { $stalId = $stalSchaarId; }
if(isset($actId) && !isset($stalSchaarId)) { //schaap is uitgeschaard en heeft nog geen stalmoment van die lokatie
// Maak stalmoment van uitgeschaarde lokatie
[$ubn, $ubnId] = $stal_gateway->zoek_ubn_uitgeschaarde_lokatie($lidId, $relId);

If(!isset($ubnId)) { //Als de externe lokatie (ubn) nog niet voorkomt in tblUbn bij deze gebruiker

$ubnId = $ubn_gateway->insert($lidId, $ubn, 0);
}

$stalId = $stal_gateway->setAanvoer($ubnId, $schaapId_db, 4); // 4 is een niet bestaand relId omdat de herkomst van de uitgeschaarde lokatie niet relevant is. Het vullen van het veld rel_herk is zo wel eenduidig in tblStal

} // Einde Maak stalmoment van uitgeschaarde lokatie
// Einde Controle op uitgeschaarde schapen (moederdieren)

// CONTROLE op alle verplichten velden bij medicatie
if (isset($fldDay) && isset($fldToedat) && isset($fldArtId)) 
{

$stalId = $stal_gateway->zoek_stalId($schaapId_db, $lidId);
        /* CONTROLE */
        // Controle op afvoerdatum

unset($dmafv);
[$dmafv, $advdm]  = $historie_gateway->zoek_afvoerdatum($stalId);

        // Einde Controle op afvoerdatum
            if (isset($dmafv) && $dmafv <= $fldDay) {
                $levnr = $schaap_gateway->zoek_levensnummer($stalId);
                    $fout = 'De datum bij ' . $levnr . ' moet voor ' . $afvdm . ' liggen.';
            }
         /* EINDE CONTROLE */
            else {
             /* INVOEREN */
     [$naam, $stdat] = $artikel_gateway->zoek_artikel($fldArtId);

        $toedtotal = $fldToedat * $stdat;

        $tot_vrd = $artikel_gateway->zoek_totale_voorraad($fldArtId);
                
                if ($tot_vrd < $toedtotal) {
                    $fout = "De voorraad van " . $naam . " is niet toereikend";
                } else {
                    
                    $hisId = $historie_gateway->insert_tblHistorie($stalId, $fldDay, 8);

                    inlezen_pil($hisId, $fldArtId, $fldToedat, $fldDay, $fldReden);

                    $impagrident_gateway->set_verwerkt($recId);

                }
            } /* EINDE INVOEREN  EINDE */
         
        } // CONTROLE op alle verplichten velden bij medicatie
     
    } // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    
if ($fldKies == 0 && $fldDel == 1) {  $impagrident_gateway->set_verwerkt($recId);  }

//echo '<br>'.'einde '.$recId.'<br>';
}
