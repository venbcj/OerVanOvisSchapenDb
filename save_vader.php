<?php
/* 6-3-2015 : sql beveiligd */



foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  
#echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {


foreach($id as $key => $value) {

    if ($key == 'txtScan' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $updScan = "'".$value."'"; } else if ($key == 'txtScan' && empty($value)) { $updScan = 'NULL'; }
        
                                    }
$zoek_scan = mysqli_query($db,"
SELECT scan
FROM tblStal
WHERE stalId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));

while ($sc = mysqli_fetch_assoc($zoek_scan)) { $scan_dbb = $sc['scan']; 
    if(empty($scan_dbb)) { $scan_db = 'NULL'; } 
    else $scan_db = "'".$scan_dbb."'";
}



/*echo '$updScan = '.$updScan.'<br>';
echo '$scan_db = '.$scan_db.'<br>';    */
if($updScan <> $scan_db) {
// Zoeken naar dubbel scancode
$dublicate_scannr = mysqli_query($db,"
SELECT scan 
FROM tblStal
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and scan = ".$updScan." and scan is not NULL and isnull(rel_best)
") or die(mysqli_error($db));
    while ( $dub = mysqli_fetch_assoc($dublicate_scannr)) { $dubbel = $dub['scan']; }
// EINDE Zoeken naar dubbel scancode
if(isset($dubbel))    { $fout = " Het scannr bestaat al."; }
else {

$verwijder_scan_afgevoerden = "UPDATE tblStal SET scan = NULL WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and scan = ".$updScan." and rel_best is not null";
        mysqli_query($db,$verwijder_scan_afgevoerden) or die (mysqli_error($db));

$updateScan = "UPDATE tblStal SET scan = ".$updScan." WHERE stalId = ".mysqli_real_escape_string($db,$recId)." ";    
        mysqli_query($db,$updateScan) or die (mysqli_error($db));
    }
        }

    
    
    }
    }

?>
                    
    