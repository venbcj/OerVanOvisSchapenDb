<?php /* 23-10-2015 : gemaakt 
07-01-2025 : Hidden velden in Kostenopgaaf.php verwijderd en hier lege checkboxen gedefinieerd */

include "url.php";



$array = array();

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {
    if (!$recId) continue;

unset($fldLiq);
unset($fldArch);
unset($fldDel);
   
 foreach($id as $key => $value) {

     //$fldLiq = 0;
    if ($key == 'chbLiq') {  $fldLiq = $value; /*echo $key.'='.$value."<br/>";*/ } 
    
    if ($key == 'kzlRubr') {  $fldRubr = $value; /*echo $key.'='.$value."<br/>";*/  }    
        if ($key == 'txtDatum') {
            $fldDatum = $value;
            $date = date_create($value);
            $fldDate = date_format($date, 'Y-m-d');
        }
    if ($key == 'txtBedrag') {  $fldBedrag = $value; /*echo $key.'='.$value."<br/>"; */ }      
    if ($key == 'txtToel') {  $fldToel = $value; /*echo $key.'='.$value."<br/>"; */ }       

    if ($key == 'chbArch') {  $fldArch = $value; /*echo $key.'='.$value."<br/>"; */ }      
    if ($key == 'chbDel') {  $fldDel = $value; /*echo $key.'='.$value."<br/>"; */ }       

}

if(!isset($fldLiq)) { $fldLiq = 0; }
if(!isset($fldArch)) { $fldArch = 0; }
if(!isset($fldDel)) { $fldDel = 0; }

$zoek_waarde_liquiditeit = mysqli_query($db,"
SELECT o.rubuId, o.datum, o.bedrag, o.toel, o.liq
FROM tblOpgaaf o
WHERE o.opgId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
    while ( $zwl = mysqli_fetch_assoc($zoek_waarde_liquiditeit)) { 
        $rubuId_db = $zwl['rubuId'];
        $date_db = $zwl['datum'];
        $bedrag_db = $zwl['bedrag'];
        $toel_db = $zwl['toel']; 
        $liq_db = $zwl['liq']; 
    }

/*if($recId == 1474) {
echo "<br/>";
echo '$fldLiq = '.$fldLiq."<br/>";
echo '$liq_db = '.$liq_db."<br/>";
echo '$fldRubr = '.$fldRubr."<br/>";
echo '$fldDate = '.$fldDate."<br/>";
echo '$fldBedrag = '.$fldBedrag."<br/>";
echo '$fldToel = '.$fldToel."<br/>";
echo '$fldArch = '.$fldArch."<br/>";
echo '$fldDel = '.$fldDel."<br/>";
echo "<br/>";
}*/

if($fldLiq <> $liq_db) {
    $update_tblOpgaaf_liq = "UPDATE tblOpgaaf SET liq = '".mysqli_real_escape_string($db,$fldLiq)."' WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' "; 
    
        /*echo '$update_tblOpgaaf_liq = '.$update_tblOpgaaf_liq.'<br>';*/ 
        mysqli_query($db,$update_tblOpgaaf_liq) or die (mysqli_error($db)); 
}
 
if($fldRubr <> $rubuId_db) {
    $update_tblOpgaaf_rubr = "UPDATE tblOpgaaf SET rubuId = '".mysqli_real_escape_string($db,$fldRubr)."' WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' "; 
        
        /*echo '$update_tblOpgaaf_rubr = '.$update_tblOpgaaf_rubr.'<br>';*/ 
        mysqli_query($db,$update_tblOpgaaf_rubr) or die (mysqli_error($db));   
}

if($fldDate <> $date_db) {
    $update_tblOpgaaf_date = "UPDATE tblOpgaaf SET datum = '".mysqli_real_escape_string($db,$fldDate)."' WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' ";

        /*echo '$update_tblOpgaaf_date = '.$update_tblOpgaaf_date.'<br>';*/ 
        mysqli_query($db,$update_tblOpgaaf_date) or die (mysqli_error($db));   
}

if($fldBedrag <> $bedrag_db) {
    $update_tblOpgaaf_bedrag = "UPDATE tblOpgaaf SET bedrag = '".mysqli_real_escape_string($db,$fldBedrag)."' WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' "; 

        /*echo '$update_tblOpgaaf_bedrag = '.$update_tblOpgaaf_bedrag.'<br>';*/ 
        mysqli_query($db,$update_tblOpgaaf_bedrag) or die (mysqli_error($db));
}

if($fldToel <> $toel_db) {
    $update_tblOpgaaf_toel = "UPDATE tblOpgaaf SET toel = '".mysqli_real_escape_string($db,$fldToel)."' WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' "; 

        /*echo '$update_tblOpgaaf_toel = '.$update_tblOpgaaf_toel.'<br>';*/ 
        mysqli_query($db,$update_tblOpgaaf_toel) or die (mysqli_error($db)); 
    }


if($fldArch == 1) {

$update_record = "UPDATE tblOpgaaf SET his = 1 WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' ";
        /*echo '$update_record = '.$update_record.'<br>';*/ 
        mysqli_query($db,$update_record) or die (mysqli_error($db));    
        }


if($fldDel == 1) {
$Delete_record = "DELETE FROM tblOpgaaf WHERE opgId = '".mysqli_real_escape_string($db,$recId)."' ";
        /*echo '$Delete_record = '.$Delete_record.'<br>';*/ 
        mysqli_query($db,$Delete_record) or die (mysqli_error($db));    
}

    
    
                        
}
?>
                    
    
