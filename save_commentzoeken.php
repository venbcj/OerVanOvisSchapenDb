<?php
/* 29-3-2017 : gemaakt 
29-12-2023 sql voorzien van enkele quotes */



foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  


 foreach($id as $key => $value) { 


if(isset($recId) && $recId > 0) {
foreach($id as $key => $value) {

    if ($key == 'txtComm' && !empty($value)) {
        $updComm = "'".$value."'";
    } else if ($key == 'txtComm' && empty($value)) {
        $updComm = 'NULL';
    }
}




$zoek_commentaar = mysqli_query($db,"
SELECT comment
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($db,$recId)."' 
") or die(mysqli_error($db));
    while ( $co = mysqli_fetch_assoc($zoek_commentaar)) { $comm = $co['comment']; }

if(!isset($comm)) { $dbComm = 'NULL'; } else { $dbComm = "'".$comm."'"; }


if($updComm <> $dbComm && $updComm == 'NULL') {

$update_tblHistorie = "UPDATE tblHistorie SET comment = NULL WHERE hisId = '".mysqli_real_escape_string($db,$recId)."'  ";    
/*echo $update_tblHistorie.'<br>';*/        mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
    
        }

if($updComm <> $dbComm && $updComm <> 'NULL') {

$update_tblHistorie = "UPDATE tblHistorie SET comment = ".$updComm." WHERE hisId = '".mysqli_real_escape_string($db,$recId)."'  ";        
/*echo $update_tblHistorie.'<br>';*/        mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));
    
        }



    }



    
    
    }
    }

?>
                    
    
