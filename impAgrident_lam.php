<?php /*15-11-2020 bestand gekopieerd van impAgrident.php en diverse imp... bestanden teruggebracht naar dit ene bestand 
 Het verschil met impAgrident is dat dit bestand een sub-array heeft me lammeren */


$cnt_taak = count($velden_taak);
$last_element = $cnt_taak-1; // het laatste element is een array met lammeren
$cnt_lam = count($velden_lam);

       foreach($inhoud as $key => $waarde) {        
      
        /*if ($key == 0) {
          $insert_qry_taak .= ','; // is komma tussen twee records die worden ingelezen. De komma bestaat pas van index 1 !!!
        }*/

// Inlezen record
for($g = 0; $g < $cnt_taak; $g++) { // Er zijn maar 8 elementen in de array want element 8 is weer een array met meerdere elementen

  if($g == 0) { $insert_qry_taak = " INSERT INTO impAgrident SET "; $select_qry = ""; }
  

        //$insert_qry_taak .= '('; // begin elke in te lezen record met haakje openen. Tussen haakjes staan immers de waarde.
if($g < $last_element && ($waarde -> {$velden_taak[$g]} == "" || $waarde -> {$velden_taak[$g]} == "0"))
   {  $insert_qry_taak .= "$velden_taak[$g] = NULL, "; 
      $select_qry .= "ISNULL($velden_taak[$g]) and "; }
else if($g < $last_element) {  $insert_qry_taak .= "$velden_taak[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_taak[$g]} ) . "', "; 
    $select_qry .= "$velden_taak[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_taak[$g]} ) . "' and "; }


if ($g == $last_element) { // element 3 is array met lammeren

$array = $waarde -> {$velden_taak[$g]}; //echo var_dump($waarde -> {$velden_taak[$g]} );

foreach($array as $key1 => $waarde1) {

$insert_qry_lam = "";

  for($gl = 0; $gl < $cnt_lam; $gl++) {

    if($waarde1 -> {$velden_lam[$gl]} == "")
      { $insert_qry_lam .= "$velden_lam[$gl] = NULL, "; }
    else {     
     $insert_qry_lam .= "$velden_lam[$gl] = '" . mysqli_real_escape_string($db, $waarde1 -> {$velden_lam[$gl]}) . "', ";
      }
} // for($gl = 0


$insert_qry = $insert_qry_taak;
$insert_qry .= $insert_qry_lam;
$insert_qry .= ' lidId = '.mysqli_real_escape_string($db,$lidid).';';


echo $insert_qry; mysqli_query($db,$insert_qry) or die (mysqli_error($db));
unset($insert_qry_lam);
unset($insert_qry);

} // foreach($array

} // Einde element 3 is array met lammeren

} // for($g = 0;


// Einde Inlezen record




       } // Einde foreach($inhoud .....
?>