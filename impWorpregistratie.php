<?php /*7-2-2020 bestand gekopieerd van reader 
8-3-2020 Onderdeel gemaakt van impReaderAgrident.php 
21-3-2020  Bestand hernoemd naar impWorpregistratie.php 
30-5-2020 Uitval via Worpregistrtaie standaard als Volledig dood geboren opgeslagen 
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident 
23-1-2021 : Transponder en MoederTransponder toegevoegd 
03-03-2024 : Hele worp doodgeboren toegevoegd */

   
    //$input = $inhoud; //file_get_contents('php://input'); // php://input is de rauwe data. nl. het json bestand.
    //var_dump( $data ->glossary->GlossDiv->title) ;


$velden_worp = array('ActId', 'MoederTransponder', 'Moeder', 'Datum', 'RasId', 'HokId', 'Verloop', 'Geboren', 'Levend', 'Reden', 'Lammeren');
$velden_lam = array('Transponder', 'Levensnummer', 'Geslacht', 'Gewicht');

$worp_velden = count($velden_worp);
$last_element = $worp_velden-1;
$lam_velden = count($velden_lam);

       foreach($inhoud as $key => $waarde) {        
      
        /*if ($key == 0) {
          $insert_qry_mdr .= ','; // is komma tussen twee records die worden ingelezen. De komma bestaat pas van index 1 !!!
        }*/

$aantal_levend = $waarde -> {$velden_worp[8]};

// Inlezen record
for($g = 0; $g < $worp_velden; $g++) { // Er zijn maar $worp_velden elementen in de array want element $worp_velden is weer een array met meerdere elementen

  if($g == 0) { $insert_qry_mdr = " INSERT INTO impAgrident SET "; $select_qry = ""; }
  

        //$insert_qry_mdr .= '('; // begin elke in te lezen record met haakje openen. Tussen haakjes staan immers de waarde.

// Lege velden of 0 waarde omzetten in NULL
if($g < $last_element && $g != 8 /* 8 is aantal levend geboren en mag wel 0 zijn */ && ($waarde -> {$velden_worp[$g]} == "" || $waarde -> {$velden_worp[$g]} == "0"))
   {  $insert_qry_mdr .= "$velden_worp[$g] = NULL, "; 
      $select_qry .= "ISNULL($velden_worp[$g]) and "; }
else if($g < $last_element) {  $insert_qry_mdr .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]} ) . "', "; 
    $select_qry .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]} ) . "' and "; }
// Einde Lege velden of 0 waarde omzetten in NULL

if ($g == $last_element && !empty($aantal_levend)) { // element 8 is array met lammeren

$array = $waarde -> {$velden_worp[$g]}; //echo var_dump($waarde -> {$velden_worp[$g]} );

foreach($array as $key1 => $waarde1) {

$insert_qry_lam = "";

  for($gl = 0; $gl < $lam_velden; $gl++) {

  //$veld1 = $waarde1 -> {$velden_lam[$gl]};

     
     $insert_qry_lam .= "$velden_lam[$gl] = '" . mysqli_real_escape_string($db, $waarde1 -> {$velden_lam[$gl]}) . "', ";
} // for($gl = 0



$insert_qry = $insert_qry_mdr;
$insert_qry .= $insert_qry_lam;
$insert_qry .= ' lidId = '.mysqli_real_escape_string($db,$lidid).';';


echo $insert_qry; mysqli_query($db,$insert_qry) or die (mysqli_error($db));
unset($insert_qry_lam);
unset($insert_qry);

} // foreach($array

} // Einde element 8 is array met lammeren

else if ($g == $last_element && empty($aantal_levend)) { // element 8 is array zonder lammeren


$insert_qry = $insert_qry_mdr;
//$insert_qry .= $insert_qry_lam;
$insert_qry .= ' lidId = '.mysqli_real_escape_string($db,$lidid).';';


echo $insert_qry; mysqli_query($db,$insert_qry) or die (mysqli_error($db));
//unset($insert_qry_lam);
unset($insert_qry);




}

} // for($g = 0;

/****** Splits doden lammeren van levend per worp of bij meerdere doodgeboren zonder 1 levend lam ******/
$zoek_laatste_record =  mysqli_query ($db,"
SELECT max(Id) Id FROM impAgrident WHERE lidId = ".mysqli_real_escape_string($db,$lidid)." and actId = 1
") or die (mysqli_error($db));
  while ($mi = mysqli_fetch_assoc($zoek_laatste_record)) { $impId = $mi['Id']; }




if(isset($impId)) { // De allereerste keer per klant kan er nog geen record zijn !!
// Zoek worp aantallen uit het laatste record
$zoek_worp_aantallen = mysqli_query ($db,"
SELECT geboren, levend FROM impAgrident WHERE Id = ".mysqli_real_escape_string($db,$impId)."
") or die (mysqli_error($db));
  while ($wa = mysqli_fetch_assoc($zoek_worp_aantallen)) { $geboren = $wa['geboren']; $levend = $wa['levend']; }

$doden = $geboren - $levend; } // Einde if(isset($impId)) 


if(isset($doden) && $doden > 0){

/* Bij allemaal dode dieren moet er 1 record minder worden gedupliceerd omdat 1 dood lam reeds is ingelezen.
Daarnaast moet bij het reeds ingelezen dode lam het verblijf worden gewist en moment worden gevuld met 'dood geboren'*/
if($geboren == $doden) { $doden = $doden -1; 

$update_hokId = "UPDATE impAgrident set hokId = NULL, momId = 1 WHERE Id = ".mysqli_real_escape_string($db,$impId)." ";
  mysqli_query($db,$update_hokId) or die (mysqli_error($db));
}

for ($d = 1; $d <= $doden; $d++){

$insert_dood = $insert_qry_mdr.' lidId = '.mysqli_real_escape_string($db,$lidid).';';
echo $insert_dood; mysqli_query($db,$insert_dood) or die (mysqli_error($db));

$zoek_laatste_record =  mysqli_query ($db,"
SELECT max(Id) Id FROM impAgrident WHERE lidId = ".mysqli_real_escape_string($db,$lidid)."
") or die (mysqli_error($db));
  while ($lst = mysqli_fetch_assoc($zoek_laatste_record)) { $lstId = $lst['Id']; }

  $update_hokId = "UPDATE impAgrident set hokId = NULL, momId = 1 WHERE Id = ".mysqli_real_escape_string($db,$lstId)." ";
  mysqli_query($db,$update_hokId) or die (mysqli_error($db));

}
  $update_tabel = "UPDATE impAgrident set reden = NULL WHERE levensnummer is not null and lidId = ".mysqli_real_escape_string($db,$lidid)." ";

    mysqli_query($db,$update_tabel) or die (mysqli_error($db));
}
/****** Einde Splits doden lammeren van levend per worp of bij meerdere doodgeboren zonder 1 levend lam ******/

// Einde Inlezen record




       } // Einde foreach($inhoud .....

?>