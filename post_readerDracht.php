<!-- 17-11-2016; Aangemaakt als kopie van post_readerAanv.php 
10-11-2018 invoer nieuwe schapen verwijderd (incl. ras dus) i.v.m. alleen ooien en rammen van stallijst 
7-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes. 
31-12-2023 : and skip = 0 toegevoegd aan $zoek_laatste_koppel_zonder_worp_obv_alleen_moederdier -->

<?php


$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
    if (!$recId) continue;

// Id ophalen
#echo $recId; 
// Einde Id ophalen

unset($fldRam);
unset($fldGrootte);
unset($fldKies);
unset($fldDel);
   
 foreach($id as $key => $value) {

  if ($key == 'chbKies')   { $fldKies = $value; }
  if ($key == 'chbDel')    { $fldDel = $value; }

    if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
                                    $fldDag = $valuedag; }
    
    if ($key == 'kzlOoi' && !empty($value)) {  $fldOoi = $value; } // betreft schaapId

    if ($key == 'kzlRam' && !empty($value)) {  $fldRam = $value; } // betreft levensnummer

    if ($key == 'txtGrootte' && !empty($value)) {  $fldGrootte = $value; }
     
                                    }

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.


if ($fldKies == 1 && !isset($fldDel) && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen


// CONTROLE op alle verplichten velden 
if(isset($fldOoi) && isset($fldDag)) {

// Dracht binnen laatste 183 dagen mag geen worp hebben. Dit is reeds uitgesloten in InsDracht.php Alleen gedekte moeders zoeken volstaat hier dus
// Scannen dracht wordt enkel moeder gevraagd in te geven. Vader wordt gebaseerd o.b.v. eventuele dekking.
$zoek_laatste_koppel_zonder_worp_obv_alleen_moederdier = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join tblSchaap s on (s.volwId = v.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and skip = 0
 ) ha on (k.schaapId = ha.schaapId)
WHERE isnull(s.volwId) and v.mdrId = '".mysqli_real_escape_string($db,$fldOoi)."' and isnull(ha.schaapId)
") or die (mysqli_error($db));
    while ( $lkzw = mysqli_fetch_assoc($zoek_laatste_koppel_zonder_worp_obv_alleen_moederdier)) { $volwId = $lkzw['volwId']; }


if (!isset($volwId)) { // Als er geen koppel en dus ook geen dekking is geregistreerd

    // koppel registreren zonder dekdatum
    $insertKoppel = "INSERT INTO tblVolwas SET mdrId = '".mysqli_real_escape_string($db,$fldOoi)."', vdrId = " . db_null_input($fldRam) . ", grootte = " . db_null_input($fldGrootte) ;    
mysqli_query($db,$insertKoppel) or die (mysqli_error($db));

$zoek_volwId = mysqli_query($db,"
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = '".mysqli_real_escape_string($db,$fldOoi)."' and " . db_null_filter(vdrId, $fldRam) . "
") or die (mysqli_error($db));
    while ( $zv = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $zv['volwId']; }

} // Einde Als er geen koppel en dus ook geen dekking is geregistreerd
else { // Bij bestaand volwId kunnen gegevens zijn gewijzigd

$zoek_grootte_db = mysqli_query($db,"
SELECT grootte
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."'
") or die (mysqli_error($db));
    while ( $zvd = mysqli_fetch_assoc($zoek_grootte_db)) { 
        $grootte_db = $zvd['grootte']; }

if(isset($fldRam)) {

$update_tblVolwas = "UPDATE tblVolwas set vdrId = '".mysqli_real_escape_string($db,$fldRam)."' WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' ";
mysqli_query($db,$update_tblVolwas) or die (mysqli_error($db));
}

if($fldGrootte <> $grootte_db) {

$update_tblVolwas = "UPDATE tblVolwas set grootte = " . db_null_input($fldGrootte) . " WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' ";
mysqli_query($db,$update_tblVolwas) or die (mysqli_error($db));
}


} // Einde Bij bestaand volwId kunnen gegevens zijn gewijzigd

// Registreren dracht
$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$fldOoi)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
    while ( $zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $zs['stalId']; }

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 19 ";    
mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 19 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
    while ( $zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$insert_tblDracht = "INSERT INTO tblDracht SET readId = '".mysqli_real_escape_string($db,$recId)."', volwId = '".mysqli_real_escape_string($db,$volwId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";    
mysqli_query($db,$insert_tblDracht) or die (mysqli_error($db));


// Einde Registreren dracht


        $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
mysqli_query($db,$updateReader) or die (mysqli_error($db));


unset($fldOoi); unset($fldRam); unset($volwId);
// EINDE CONTROLE op alle verplichten velden 

}  // Einde if(isset($fldOoi) && isset($fldRam) && isset($fldDag))

} // Einde if ($fldKies == 1 && !isset($fldDel) && !isset($verwerkt))

    
 if(!isset($fldKies) && $fldDel == 1) {

 if($reader == 'Agrident') {
       $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
}
mysqli_query($db,$updateReader) or die (mysqli_error($db));
    }





unset($fldlevnr);
    }
?>
                    
    
