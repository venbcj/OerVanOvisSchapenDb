<!--  25-6-2021 : Gekopieerd van post_readerMed.php 
5-9-2021 : Functie leesvoer_in toegevoegd -->

<?php



$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

    //echo '<br>'.'$recId = '.$recId;
    
  foreach($id as $key => $value) {

    if ($key == 'txtAantal' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldAantal = str_replace(',', '.', $value); }
     
                                    }

if(isset($fldAantal)) {
$zoek_aantal_uit_reader = mysqli_query($db,"
SELECT toedat, toedat_upd
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while ($za = mysqli_fetch_array($zoek_aantal_uit_reader))
{
   $toedat = $za['toedat']; 
   $toedat_upd = $za['toedat_upd'];
}

if($fldAantal != $toedat && (!isset($toedat_upd) || $fldAantal <> $toedat_upd) ) { /*echo 'Het veld toedat_upd van regel '.$recId.' wordt gevuld of gewijzigd. <br>'; */

    $updateReader = "UPDATE impAgrident set toedat_upd = '".mysqli_real_escape_string($db,$fldAantal)."' WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

        /*echo $updateReader.'<br>';*/        mysqli_query($db,$updateReader) or die (mysqli_error($db));

    }

if($fldAantal == $toedat && isset($toedat_upd)) { /*echo 'Het veld toedat_upd van regel '.$recId.' wordt leeggemaakt. <br>'; */

    $updateReader = "UPDATE impAgrident set toedat_upd = NULL WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;

        /*echo $updateReader.'<br>';*/        mysqli_query($db,$updateReader) or die (mysqli_error($db));

    }

}

//#echo '<br>'.'einde '.$recId.'<br>';
    }

?>
                    
    