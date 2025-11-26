<?php
/* 17-05-2019 gemaakt 
01-01-2024 : sql beveiligd 
07-01-2024 : insert_tblBezet uitgezet omdat Aanwas niet aan een verblijf wordt toegekend. Zie het veld aan in tblActie bij actId 3. Dit staat op 0 
20-02-2025 Hidden velden in HokAanwas.php verwijderd en hier lege checkboxen gedefinieerd ondanks dat het niet nodig is! */

include "url.php";



$array = array();

foreach($_POST as $fldname => $fldvalue) {
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach($multip_array as $recId => $id) {
    if (!$recId) continue;

unset($fldKies);
unset($updDag);
unset($updKg);

foreach($id as $key => $value) {
     if ($key == 'chbkies' && $value == 1)     { /*echo $key.'='.$value.' ';*/  $fldKies = $value; } 

    if ($key == 'txtDatum' ) { $dag = date_create($value); $updDag =  date_format($dag, 'Y-m-d');  }
    
    if ($key == 'txtKg' && !empty($value)) { $updKg = str_replace(',', '.', $value); } /*else if ($key == 'txtKg' && empty($value)) { $updKg = 'NULL'; }*/
        
                                    }

//if(!isset($fldKies)) { $fldKies = 0; }

// CONTROLE op alle verplichten velden bij aanwas lam
if ($fldKies == 1 && !empty($updDag))
{
/*
echo "Datum = ".$updDag.'<br>' ; 
echo "Kg = ".$updKg.'<br>' ; */

    $stalId = 0;
$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
WHERE isnull(st.rel_best) and st.schaapId = '".mysqli_real_escape_string($db,$recId)."' and st.lidId = ".mysqli_real_escape_string($db,$lidId)."
") or die(mysqli_error($db));

while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }

    $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$updDag)."', kg = ".db_null_input($updKg).", actId = 3 ";
/*echo $insert_tblHistorie.'<br>';*/    mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

}
// EINDE CONTROLE op alle verplichten velden bij aanwas lam

            
    } ?>
                    
    
