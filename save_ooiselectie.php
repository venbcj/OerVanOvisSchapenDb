<!-- 19-12-2020 : gekopieerd van save_ras.php 
    31-1-2021 : Transponder uit database gehaald
    29-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie
 -->

<?php
/* toegepast in :
    - Ras.php */

$zoek_laatste_selectie = mysqli_query($db,"
SELECT max(volgnr) volgnr
FROM tblAlertselectie
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

while($zs = mysqli_fetch_assoc($zoek_laatste_selectie))
            {
                $old_volgnr = $zs['volgnr']; }




foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  

    //echo '<br>'.'$worpId = '.$recId.'<br>';
    //var_dump($id); echo '<br>'; 

    foreach($id as $key => $value) {

    if ($key == 'check' && !empty($value)) { $uitvoeren = $value; } // Einde if ($key == 'check' && !empty($value))

        if ($key == 'txtWorpVan' && !empty($value)) { $dag = date_create($value); $flddagvan = date_format($dag, 'Y-m-d');  
                  /*echo $key.'='.$valuedag.' ';*/  }

        if ($key == 'txtWorpTot' && !empty($value)) { $dag = date_create($value); $flddagtot = date_format($dag, 'Y-m-d');  
                  /*echo $key.'='.$valuedag.' ';*/  }

if(!empty($recId)) {

        /*echo '<br> $worpId = '.$recId.' <br>';
        echo 'Vanaf = '.$flddagvan.' <br>';
        echo 'Vanaf = '.$flddagtot.' <br>';*/



if(!isset($old_volgnr)) { $volgnr = 1; } else { $volgnr = $old_volgnr + 1; }

$zoek_dieren = mysqli_query($db,"
SELECT levensnummer, transponder
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join (
    SELECT s.volwId, count(s.schaapId) aant
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
    WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY s.volwId
 ) w on (s.volwId = w.volwId)
WHERE s.geslacht = 'ooi' and isnull(st.rel_best) and h.actId = 1 and h.skip = 0 and h.datum >= '".mysqli_real_escape_string($db,$flddagvan)."' and h.datum <= '".mysqli_real_escape_string($db,$flddagtot)."' and w.aant = '".mysqli_real_escape_string($db,$recId)."'
ORDER BY levensnummer

") or die (mysqli_error($db));

while($zd = mysqli_fetch_assoc($zoek_dieren))
            {
                $transponder = $zd['transponder'].$zd['levensnummer'];

 $insert_tblAlertselectie  = "INSERT INTO tblAlertselectie set volgnr = '".mysqli_real_escape_string($db,$volgnr)."', lidId = '".mysqli_real_escape_string($db,$lidId)."', transponder = '".mysqli_real_escape_string($db,$transponder)."', alertId = '".mysqli_real_escape_string($db,$recId)."' ";


/*echo $insert_tblAlertselectie.'<br>';*/ mysqli_query($db,$insert_tblAlertselectie) or die (mysqli_error($db));

            }





}
if(isset($volgnr)) {
$zoek_aantal_selectie = mysqli_query($db,"
SELECT count(Id) aant
FROM tblAlertselectie
WHERE volgnr = '".mysqli_real_escape_string($db,$volgnr)."'
") or die (mysqli_error($db));

while($as = mysqli_fetch_assoc($zoek_aantal_selectie))
            {
                $aantal = $as['aant']; }

    $goed = 'Er staan '.$aantal.' schapen klaar om naar de reader te sturen.';
}

}

        }
?>
                    
    