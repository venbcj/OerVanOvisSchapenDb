<?php
/* 6-3-2015 : sql beveiligd 
30-5-2020 : hidden velden ctrScan en ctrActief verwijderd en aangepast op Agrident reader
02-08-2020 : veld sort toegevoegd 
20-04-2024 : Verblijven kunnen worden verwijderd zolang er geen relatie ligt met andere tabellen 
10-03-2025 : Hidden veld chbActief_Id in Hok.php verwijderd en hier lege checkbox gedefinieerd */



foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {
unset($fldSort);
unset($fldActief);
unset($fldDelete);

#echo '<br>'.'$recId = '.$recId.'<br>';

foreach($id as $key => $value) {

    if ($key == 'txtSort' && !empty($value)) { $fldSort = $value; }
    
    if ($key == 'chbActief' ) { $fldActief = $value; /*echo $key.'='.$value."<br/>";*/ }

    if ($key == 'chbDel' ) { $fldDelete = $value; /*echo $key.'='.$value."<br/>";*/ }

                                }

if(!isset($fldActief)) { $fldActief = 0; }

if($recId > 0) {

$zoek_db_waardes = mysqli_query($db,"
SELECT sort, actief
FROM tblHok
WHERE hokId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while($row = mysqli_fetch_assoc($zoek_db_waardes))
    { $Sort_db = $row['sort'];
    $Actief_db = $row['actief']; }

/*echo '$fldSort = '.$fldSort.'<br>';                                    
echo '$Sort_db = '.$Sort_db.'<br>';    */

if($fldSort <> $Sort_db) {

    $updateSort = "UPDATE tblHok SET sort = ". db_null_input($fldSort) ." WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";
    /*echo $updateSort; */ mysqli_query($db, $updateSort) or die (mysqli_error($db));

    $goed = 'De wijziging is opgeslagen. Vergeet niet de reader bij te werken!';
}

if($fldActief <> $Actief_db) {    
// Zoeken naar hoeveelheid schapen per hok
$aanwezige_schapen = mysqli_query($db,"SELECT hoknr, nu aantal FROM (".$vw_HoknBeschikbaar.") hb WHERE hokId = ".mysqli_real_escape_string($db,$recId)." ") or die (mysqli_error($db));
    while ($rij = mysqli_fetch_assoc($aanwezige_schapen))
        {    
            $hoknr = "{$rij['hoknr']}";
            $inhok = "{$rij['aantal']}";
        }
// EINDE Zoeken naar hoeveelheid schapen per hok




    if (isset($inhok) && $inhok > 0 && $fldActief == 0 ) 
     {    if ($inhok == 1 ) {$fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog 1 schaap in zit.";}    
        else {$fout = "$hoknr kan niet buiten gebruik worden gesteld omdat er nog $inhok schapen in zitten.";}
     }

else {
    $updateHok = "UPDATE tblHok SET actief = '". mysqli_real_escape_string($db,$fldActief) ."' WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";
        mysqli_query($db,$updateHok) or die (mysqli_error($db));

        $goed = 'De wijziging is opgeslagen. Vergeet niet de reader bij te werken!';
     }

}

if(isset($fldDelete)) {

    $deleteHok = "DELETE FROM tblHok WHERE hokId = '".mysqli_real_escape_string($db,$recId)."' ";
        mysqli_query($db,$deleteHok) or die (mysqli_error($db));

}





    
    
    }

}

?>
                    
    
