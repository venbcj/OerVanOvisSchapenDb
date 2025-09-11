<!-- 30-8-2020 gemaakt 
30-12-2023 sql beveiligd met quotes -->

<?php
/*Save_Artikel.php toegpast in :
    - Voorraadcorrectie.php    */




$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
//echo '<br>'.'$recId = '.$recId.'<br>';
    


  foreach($id as $key => $value) {
    
    if ($key == 'txtCorat' && !empty($value)){  $updAantal = str_replace(',', '.', $value);  } 

    if ($key == 'kzlCorr' && !empty($value)){  $updCorr = $value;  } 
     

                                    }


if(isset($recId) && $recId > 0 && isset($updAantal)) {

    if($updCorr == 'af')  { $updCorrat =  $updAantal; }
    if($updCorr == 'bij') { $updCorrat = -$updAantal; }

$zoek_soort_artikel = mysqli_query($db,"
SELECT a.soort
FROM tblInkoop i
 join tblArtikel a on (a.artId = i.artId)
WHERE i.inkId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
while ($srt = mysqli_fetch_assoc($zoek_soort_artikel))    { $soort = $srt['soort']; }

/*Wijzig voorraad medicatie */
if($soort == 'pil') { 

$zoek_voorraad_pil = mysqli_query($db,"
SELECT round(i.inkat - sum(coalesce(n.nutat*n.stdat,0)),0) voorraad, e.eenheid
FROM tblInkoop i
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblNuttig n on (n.inkId = i.inkId) 
WHERE i.inkId = '".mysqli_real_escape_string($db,$recId)."'
GROUP BY e.eenheid
") or die (mysqli_error($db));

while($vd = mysqli_fetch_assoc($zoek_voorraad_pil))
            { $voorraad = $vd['voorraad']; }

$zoek_afgeboekt_pil = mysqli_query($db,"
SELECT round(sum(n.nutat*n.stdat),0) af
FROM tblNuttig n 
WHERE n.inkId = '".mysqli_real_escape_string($db,$recId)."' and isnull(hisId)
") or die (mysqli_error($db));

while($afb = mysqli_fetch_assoc($zoek_afgeboekt_pil))
            { $afboek = $afb['af']; }

$tabel = 'tblNuttig';
}
/*Wijzig voorraad medicatie */

/*Wijzig voorraad voer */
if($soort == 'voer') {

$zoek_voorraad_voer = mysqli_query($db,"
SELECT round(i.inkat - sum(coalesce(v.nutat*v.stdat,0)),0) voorraad, e.eenheid
FROM tblInkoop i
 join tblEenheiduser eu on (eu.enhuId = i.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 left join tblVoeding v on (v.inkId = i.inkId) 
WHERE i.inkId = '".mysqli_real_escape_string($db,$recId)."'
GROUP BY e.eenheid
") or die (mysqli_error($db));

while($vd = mysqli_fetch_assoc($zoek_voorraad_voer))
            { $voorraad = $vd['voorraad']; 
              $eenh = $vd['eenheid']; }

$zoek_afgeboekt_voer = mysqli_query($db,"
SELECT round(sum(v.nutat*v.stdat),0) af
FROM tblVoeding v 
WHERE v.inkId = '".mysqli_real_escape_string($db,$recId)."' and isnull(periId)
") or die (mysqli_error($db));

while($afb = mysqli_fetch_assoc($zoek_afgeboekt_voer))
            { $afboek = $afb['af']; }

$tabel = 'tblVoeding';
}
/*Wijzig voorraad voer */

if($updCorr == 'af' && $voorraad == 0) { $fout = "De voorraad is reeds 0."; }
else if($updCorr == 'af' && $voorraad < $updAantal) { $fout = "De correctie kan niet meer zijn dan ".$voorraad." ".$eenh. "."; }
else if($updCorr == 'bij' && (!isset($afboek) || $afboek <= 0) ) { $fout = "Er is niets (meer) afgeboekt. Bijboeken is niet mogelijk."; }
else if($updCorr == 'bij' && $afboek < $updAantal) { $fout = "Er is maximaal ".$afboek." ".$eenh." bij te boeken."; }
else {
    

    $wijzig_voorraad = "INSERT INTO ".mysqli_real_escape_string($db,$tabel)." set inkId = '".mysqli_real_escape_string($db,$recId)."', nutat = '".mysqli_real_escape_string($db,$updCorrat)."', stdat = 1, correctie = 1 ";

    /*echo $wijzig_voorraad.'<br>';*/        mysqli_query($db,$wijzig_voorraad) or die (mysqli_error($db));

}

unset($updAantal);    
}

    }
                

?>