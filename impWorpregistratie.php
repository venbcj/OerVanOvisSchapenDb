<?php /*7-2-2020 bestand gekopieerd van reader 
8-3-2020 Onderdeel gemaakt van impReaderAgrident.php 
21-3-2020  Bestand hernoemd naar impWorpregistratie.php 
30-5-2020 Uitval via Worpregistrtaie standaard als Volledig dood geboren opgeslagen 
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident 
23-1-2021 : Transponder en MoederTransponder toegevoegd */

   
    //$input = $inhoud; //file_get_contents('php://input'); // php://input is de rauwe data. nl. het json bestand.
    //var_dump( $data ->glossary->GlossDiv->title) ;


$velden_worp = array('ActId', 'MoederTransponder', 'Moeder', 'Datum', 'RasId', 'HokId', 'Verloop', 'Geboren', 'Levend', 'Reden', 'Lammeren');
$velden_lam = array('Transponder', 'Levensnummer', 'Geslacht', 'Gewicht');

$cnt_worp = count($velden_worp);
$last_element = $cnt_worp-1;
$cnt_lam = count($velden_lam);

       foreach($inhoud as $key => $waarde) {        
      
        /*if ($key == 0) {
          $insert_qry_mdr .= ','; // is komma tussen twee records die worden ingelezen. De komma bestaat pas van index 1 !!!
        }*/

// Inlezen record
for($g = 0; $g < $cnt_worp; $g++) { // Er zijn maar $cnt_worp elementen in de array want element $cnt_worp is weer een array met meerdere elementen

  if($g == 0) { $insert_qry_mdr = " INSERT INTO impAgrident SET "; $select_qry = ""; }
  

        //$insert_qry_mdr .= '('; // begin elke in te lezen record met haakje openen. Tussen haakjes staan immers de waarde.
if($g < $last_element && ($waarde -> {$velden_worp[$g]} == "" || $waarde -> {$velden_worp[$g]} == "0"))
   {  $insert_qry_mdr .= "$velden_worp[$g] = NULL, "; 
      $select_qry .= "ISNULL($velden_worp[$g]) and "; }
else if($g < $last_element) {  $insert_qry_mdr .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]} ) . "', "; 
    $select_qry .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]} ) . "' and "; }


if ($g == $last_element) { // element 8 is array met lammeren

$array = $waarde -> {$velden_worp[$g]}; //echo var_dump($waarde -> {$velden_worp[$g]} );

foreach($array as $key1 => $waarde1) {

$insert_qry_lam = "";

  for($gl = 0; $gl < $cnt_lam; $gl++) {

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

} // for($g = 0;

/*Splits doden lammeren van levend per worp*/
$zoek_laatste_record =  mysqli_query ($db,"
SELECT max(Id) Id FROM impAgrident WHERE lidId = ".mysqli_real_escape_string($db,$lidid)." and actId = 1
") or die (mysqli_error($db));
  while ($mi = mysqli_fetch_assoc($zoek_laatste_record)) { $impId = $mi['Id']; }

if(isset($impId)) { // De allereerste keer per klant kan er nog geen record zijn !!
// De laatste record moet er wel een zijn van een levend lam
$zoek_worp_aantallen = mysqli_query ($db,"
SELECT geboren, levend FROM impAgrident WHERE levensnummer is not null and Id = ".mysqli_real_escape_string($db,$impId)."
") or die (mysqli_error($db));
  while ($wa = mysqli_fetch_assoc($zoek_worp_aantallen)) { $geboren = $wa['geboren']; $levend = $wa['levend']; }

$doden = $geboren - $levend; }
if(isset($doden) && $doden > 0){
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
/*Einde Splits doden lammeren van levend per worp*/

// Einde Inlezen record




       } // Einde foreach($inhoud .....

?>