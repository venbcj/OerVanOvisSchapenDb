<!-- 3-9-2016 : sql beveiligd
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel en hidden velden in insOverplaats.php verwijderd en codering hier aangepast         22-1-2017 : tblBezetting gewijzigd naar tblBezet 
28-6-2017 : insert tblPeriode verwijderd Priode wordt sinds 12-2-2017 niet meer opgeslagen in tblBezet.
11-6-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
13-7-2020 : impVerplaatsing gewijzigd in impAgrident 
7-5-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes. -->

<?php
include "url.php";



$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

// Id ophalen
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen
 
  foreach($id as $key => $value) {

  if ($key == 'chbkies')   { $fldKies = $value; }
  if ($key == 'chbDel')    { $fldDel = $value; }

    if ($key == 'txtDag' ) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
                                     $fldDag = $valuedatum; }

    if ($key == 'txtKg' && !empty($value)) {  $fldKg = $value; }

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

// CONTROLE op alle verplichten velden bij overplaatsen lam
if ( !empty($fldDag))
{

$zoek_Lambar = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE hoknr = 'Lambar' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

    while ($lb = mysqli_fetch_assoc($zoek_Lambar)) { $hokId = $lb['hokId']; }

$zoek_levensnummer = mysqli_query($db,"
SELECT rd.levensnummer levnr
FROM impAgrident rd
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
    while ($ln = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $ln['levnr']; }

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
    while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';
    
    $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 16, kg = " . db_null_input($fldKg) . " ";
        mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h 
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 16
") or die (mysqli_error($db));
    while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }

        
    $insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId)."', hokId = '".mysqli_real_escape_string($db,$hokId)."' ";
        mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));


    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";

        mysqli_query($db,$updateReader) or die (mysqli_error($db));    
}
// EINDE CONTROLE op alle verplichten velden bij spenen lam

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))    


 if($fldKies == 0 && $fldDel == 1) {

       $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
    mysqli_query($db,$updateReader) or die (mysqli_error($db));
    }


    
    }
?>
