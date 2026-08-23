<!-- 19-12-2021; Aangemaakt als kopie van post_readerDracht.php  -->
<?php

$ubn_gateway = new UbnGateway();
$stal_gateway = new StalGateway();
$historie_gateway = new HistorieGateway();

$array = array();
foreach ($_POST as $fldname => $fldvalue) {
    $array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach ($array as $recId => $id) {
    if (!$recId) {
        continue;
    }
// Id ophalen
#echo $recId.'<br>';
// Einde Id ophalen
$fldOoi = null;
$fldRam = null;
    foreach ($id as $key => $value) {
        if ($key == 'chbKies') {
            $fldKies = $value;
        }
        if ($key == 'chbDel') {
            $fldDel = $value;
        }
        if ($key == 'txtDatum' && !empty($value)) {
            $dag = date_create($value);
            $valuedag =  date_format($dag, 'Y-m-d');
                                  $fldDag = $valuedag;
        }
        if ($key == 'kzlOoi' && !empty($value)) {
       /*echo '$fldOoi = '.$value.'<br>';*/ $fldOoi = $value;
        }
    // betreft schaapId ooi
        if ($key == 'kzlRam' && !empty($value)) {
       /*echo '$fldRam = '.$value.'<br>';*/ $fldRam = $value;
        }
    // betreft schaapId ram
    }
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
    unset($verwerkt);
    $zoek_readerRegel_verwerkt = mysqli_query($db, "
SELECT verwerkt
FROM impAgrident
WHERE Id = '" . mysqli_real_escape_string($db, $recId) . "'
") or die(__FILE__ . ' (' . __LINE__ . ') ' . mysqli_error($db));
    while ($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt)) {
        $verwerkt = $verw['verwerkt'];
    }
// Einde (extra) controle of readerregel reeds is verwerkt.
    if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {
     // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// Controle op uitgeschaarde schapen (moederdieren)
unset($actId, $stalSchaarId, $stalId, $ubnId, $ubn);

if(isset($fldOoi)) {
    [$actId, $relId, $stalSchaarId] = $stal_gateway->zoek_uitgeschaarde_ooi($lidId, $fldOoi);
}

if(isset($stalSchaarId)) { $stalId = $stalSchaarId; }
if(isset($actId) && !isset($stalSchaarId)) { //schaap is uitgeschaard en heeft nog geen stalmoment van die lokatie
// Maak stalmoment van uitgeschaarde lokatie
[$ubn, $ubnId] = $stal_gateway->zoek_ubn_uitgeschaarde_lokatie($lidId,$relId);

If(!isset($ubnId)) { //Als de externe lokatie (ubn) nog niet voorkomt in tblUbn bij deze gebruiker

$ubnId = $ubn_gateway->insert($lidId, $ubn, 0);

$stalId = $stal_gateway->setAanvoer($ubnId, $fldOoi, 4); // 4 is een niet bestaand relId omdat de herkomst van de uitgeschaarde lokatie niet relevant is. Het vullen van het veld rel_herk is zo wel eenduidig in tblStal
} // Einde Maak stalmoment van uitgeschaarde lokatie
// Einde Controle op uitgeschaarde schapen (moederdieren)

    // CONTROLE op alle verplichten velden
        if (isset($fldDag) && isset($fldOoi)) {
        // De ooi mag binnen laatste 183 dagen geen worp hebben.
if (!isset($stalId)) { //Als deze variabele wel bestaat is dit het stalmoment van de uitgeschaarde lokatie
            $stalId = $stal_gateway->zoek_stalId_lokatie($fldOoi, $lidId, 1);
            }
            
$hisId = $historie_gateway->insert_tblHistorie_18($stalId, $fldDag);

$volwas_gateway->insert_uitgebreid($recId, $hisId, $fldOoi, $fldRam);

$impagrident_gateway->updateReaderAgrident($recId);
        // EINDE CONTROLE op alle verplichten velden
        } // Einde if (isset($fldDag) && isset($fldOoi))
      
    } // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
 // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
    if ($fldKies == 0 && $fldDel == 1) {
$impagrident_gateway->updateReaderAgrident($recId);
    }

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))
} // Einde foreach ($array as $recId => $id)
