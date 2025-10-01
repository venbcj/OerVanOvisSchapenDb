<?php

# <!-- 30-08-2025 Kopie gemaakt van post_readerAfv.php  -->

$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}

foreach($array as $recId => $id) {

unset($fldKies);
unset($fldDel);
unset($fldDag);
unset($fldKg);

// Id ophalen
//echo $recId.'<br>'; 
// Einde Id ophalen

    
  foreach($id as $key => $value) {
    //if ($key == 'txtId' ) { /*echo $key.'='.$value.' ';*/ $fldId = $value; }    

  if ($key == 'chbkies' /*&& $value == 1*/ )     { /*$box = $value ; */ $fldKies = $value; }

  if ($key == 'chbDel' /*&& $value == 0*/ )     { $fldDel = $value; }


    if ($key == 'txtAfvoerdag' && !empty($value)) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
                                    /*echo $key.'='.$valuedatum.' ';*/ $fldDag = $valuedatum; }
    
    if ($key == 'txtKg' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = str_replace(',', '.', $value); }
     //else if ($key == 'txtKg' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = ''; } if(!isset($fldKg)) { $fldKg = ''; } /*Als module technisch nvt is */
     
                                    }
// Als checkboxen niet bestaan
if(!isset($fldKies)) { $fldKies = 0; }
if(!isset($fldDel)) { $fldDel = 0; }

//echo '<br>$fldKies = '.$fldKies.', $fldDel = '.$fldDel.'<br><br>';

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

unset($hisId_afv);
unset($hisId_aanv);

/**** UBN WIJZIGING REGISTREREN ****/

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) {

//if($recId > 0) {
// CONTROLE op alle verplichten velden bij afvoer
if ( isset($fldDag) )
{

$zoek_data_reader = "
SELECT a.levensnummer, u.ubnId, u.ubn, s.schaapId
FROM impAgrident a
 join tblUbn u on (a.ubnId = u.ubnId)
 join tblSchaap s on (a.levensnummer = s.levensnummer)
WHERE a.Id = '".mysqli_real_escape_string($db,$recId)."'
";

/* echo '$zoek_data_reader = '.$zoek_data_reader.'<br>';*/

$zoek_data_reader = mysqli_query($db,$zoek_data_reader) or die (mysqli_error($db));
    while ($zdr = mysqli_fetch_assoc($zoek_data_reader)) { 
        $levnr = $zdr['levensnummer'];
        $ubnId_best = $zdr['ubnId']; 
        $ubn_best = $zdr['ubn']; 
        $schaapId = $zdr['schaapId']; }

/* AFVOER registreren */
$zoek_relatie_afvoer = mysqli_query($db,"
SELECT r.relId
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.ubn = '".mysqli_real_escape_string($db,$ubn_best)."' and r.relatie = 'deb'
") or die (mysqli_error($db));

while ($zraf = mysqli_fetch_assoc($zoek_relatie_afvoer)) { 
        $rel_best = $zraf['relId']; }
        
$zoek_stalId_afvoer = "
SELECT stalId, u.ubn
FROM tblStal st
 join tblUbn u on (st.ubnId = u.ubnId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best)
";

/*echo '$zoek_stalId_afvoer = '.$zoek_stalId_afvoer.'<br>';*/
$zoek_stalId_afvoer = mysqli_query($db,$zoek_stalId_afvoer) or die (mysqli_error($db));
        while ($zsa = mysqli_fetch_assoc($zoek_stalId_afvoer)) { $stalId_afv = $zsa['stalId']; $ubn_herk = $zsa['ubn']; }

if(!isset($stalId_afv)) { echo $fldLevnr.' staat niet meer op de stallijst !'; }
else {
$zoek_aanwas = mysqli_query($db,"
SELECT hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and actId = 3 and skip = 0
") or die (mysqli_error($db));
    while ($za = mysqli_fetch_assoc($zoek_aanwas)) { $aanwas = $za['hisId']; }
    
if(isset($aanwas)) { $actId = 13; } else { $actId = 12; } unset($aanwas);
    

$insert_tblHistorie_afvoer = "
INSERT INTO tblHistorie 
set stalId = '".mysqli_real_escape_string($db,$stalId_afv)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = '".mysqli_real_escape_string($db,$actId)."', kg = " . db_null_input($fldKg) . " ";

    /*echo $insert_tblHistorie_afvoer.'<br>';*/    mysqli_query($db,$insert_tblHistorie_afvoer) or die (mysqli_error($db));
     
// Update tblStal
$update_tblStal_afvoer = "UPDATE tblStal
set rel_best = '".mysqli_real_escape_string($db,$rel_best)."'
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_afv)."' ";
/*echo $update_tblStal_afvoer.'<br>';*/    mysqli_query($db,$update_tblStal_afvoer) or die (mysqli_error($db));
// Einde Update tblStal

if ($modmeld == 1 ) {

$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_afv)."' and actId = '".mysqli_real_escape_string($db,$actId)."' and skip = 0
") or die (mysqli_error($db));
        while ( $hId = mysqli_fetch_assoc ($zoek_hisId)) { $hisId_afv = $hId['hisId']; }

$Melding = 'AFV';
$hisId = $hisId_afv;
include "maak_request.php";
}
/* Einde AFVOER registreren */        

/* AANVOER registreren */
$zoek_relatie_aanvoer = mysqli_query($db,"
SELECT r.relId
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE p.ubn = '".mysqli_real_escape_string($db,$ubn_herk)."' and r.relatie = 'cred'
") or die (mysqli_error($db));

while ($zraan = mysqli_fetch_assoc($zoek_relatie_aanvoer)) { 
        $rel_herk = $zraan['relId']; }

$insert_tblStal_aanvoer = "
INSERT INTO tblStal
set lidId = '".mysqli_real_escape_string($db,$lidId)."', ubnId = '".mysqli_real_escape_string($db,$ubnId_best)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', rel_herk = '".mysqli_real_escape_string($db,$rel_herk)."' ";

    /*echo ,$insert_tblStal_aanvoer.'<br>';*/    mysqli_query($db,$insert_tblStal_aanvoer) or die (mysqli_error($db));

$zoek_stalId_aanvoer = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and isnull(rel_best)
") or die (mysqli_error($db));
        while ($zsaan = mysqli_fetch_assoc($zoek_stalId_aanvoer)) { $stalId_aanv = $zsaan['stalId']; }

$insert_tblHistorie_aanvoer = "
INSERT INTO tblHistorie 
set stalId = '".mysqli_real_escape_string($db,$stalId_aanv)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = '2', kg = " . db_null_input($fldKg) . " ";

    /*echo $insert_tblHistorie_aanvoer.'<br>';*/    mysqli_query($db,$insert_tblHistorie_aanvoer) or die (mysqli_error($db));

if ($modmeld == 1 ) {

$zoek_hisId_aanv = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId_aanv)."' and actId = '2' and skip = 0
") or die (mysqli_error($db));
        while ( $zha = mysqli_fetch_assoc ($zoek_hisId_aanv)) { $hisId_aanv = $zha['hisId']; }

$Melding = 'AAN';
$hisId = $hisId_aanv;
include "maak_request.php";
}
/* Einde AANVOER registreren */        

$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

/*echo $updateReader.'<br>';*/    mysqli_query($db,$updateReader) or die (mysqli_error($db));    


} // Einde else isset($stalId_afv)
} // Einde if ( isset($fldDag) && isset($fldBest) )
// EINDE CONTROLE op alle verplichten velden bij afvoer
                          
} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))


/**** Einde UBN WIJZIGING REGISTREREN ****/


/**** VERWIJDEREN ****/

if ($fldKies == 0 && $fldDel == 1) {


    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

/*echo $updateReader.'<br>';*/  mysqli_query($db,$updateReader) or die (mysqli_error($db));
                                        
    }
                                        
/**** Einde VERWIJDEREN ****/  




    }

?>
                    
    
