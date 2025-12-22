<?php

/*
 * <!-- 4-7-2020 : Gekopieerd van post_readerAdop.php 
    21-9-2020 OMN moet VMD zijn 
1-02-2021 Transponder toegevoegd 
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven -->
*/

$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
    if (!$recId) continue;

// Id ophalen
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen

  foreach($id as $key => $value) {

if ($key == 'chbkies')   { $fldKies = $value; }
if ($key == 'chbDel')   { $fldDel = $value; }

    if ($key == 'txtDag' && !empty($value)) { $dag = date_create($value); $valuedate =  date_format($dag, 'Y-m-d'); 
                                    /*echo $key.'='.$valuedate.' ';*/ $fldDay = $valuedate; }

                                    }
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
if($reader == 'Agrident') {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

// CONTROLE op alle verplichten velden bij omnummeren lam
if ( isset($fldDay))
{
    
$zoek_old_levensnummer = mysqli_query($db,"
SELECT rd.levensnummer levnr
FROM impAgrident rd
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
    while ($dl = mysqli_fetch_assoc($zoek_old_levensnummer)) { $levnr_old = $dl['levnr']; }
//echo '$levnr = '.$levnr.'<br>';

$zoek_new_levensnummer = mysqli_query($db,"
SELECT rd.nieuw_nummer levnr, nieuw_transponder tran
FROM impAgrident rd
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
    while ($nl = mysqli_fetch_assoc($zoek_new_levensnummer)) { $levnr_new = $nl['levnr'];  $tran_new = $nl['tran']; }


$zoek_stalId = mysqli_query($db,"
SELECT stalId, s.schaapId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer = '".mysqli_real_escape_string($db,$levnr_old)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
    while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; $schaapId = $st['schaapId']; }
//echo '$stalId = '.$stalId.'<br>';

    
    $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 17, oud_nummer = '".mysqli_real_escape_string($db,$levnr_old)."' ";
        mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

    $uupdate_tblSchaap = "UPDATE tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$levnr_new)."', transponder = " . db_null_input($tran_new) . "  WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";
        mysqli_query($db,$uupdate_tblSchaap) or die (mysqli_error($db));

if($modmeld == 1) {
$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 17
") or die (mysqli_error($db));
    while ($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$Melding = 'VMD';
include "maak_request.php";

}

    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
        mysqli_query($db,$updateReader) or die (mysqli_error($db));    
}
// EINDE CONTROLE op alle verplichten velden bij omnummeren lam

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))    

if ($fldKies == 0 && $fldDel == 1) {    

    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
    mysqli_query($db,$updateReader) or die (mysqli_error($db));

    }


    
                        }
?>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
    
