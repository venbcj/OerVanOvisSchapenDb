<?php
/* 3-9-2017 aangemaakt 
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes */

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

      if ($key == 'chbkies') { $fldKies = $value; }
      if ($key == 'chbDel') { $fldDel = $value; }

    if ($key == 'txtWeegdag' && !empty($value)) { $dag = date_create($value); $fldday =  date_format($dag, 'Y-m-d');  }
    
    if ($key == 'txtKg' && !empty($value)) { $fldkg = str_replace(',', '.', $value); }
        
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

// CONTROLE op alle verplichten velden
if ( isset($fldday) && isset($fldkg) )
{
$zoek_levensnummer = mysqli_query($db,"
SELECT levensnummer
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."' 
") or die (mysqli_error($db));
    while ($lvn = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $lvn['levensnummer']; }
    
$schaapId = zoek_schaapId_in_database($levnr);
    
$stalId = zoek_stalId_in_stallijst($lidId, $schaapId);

insert_tblHistorie_kg($stalId,$fldday,9,$fldkg);

unset ($fldkg);

    
    $updateReader    =    "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo '$updateReader = '.$updateReader.'<br>';*/        mysqli_query($db,$updateReader) or die (mysqli_error($db));    

}
// EINDE CONTROLE op alle verplichten velden

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))


    
if ($fldKies == 0 && $fldDel == 1) {    
    
    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
    mysqli_query($db,$updateReader) or die (mysqli_error($db));
    }
    
    
    
    }

?>
                    
    
