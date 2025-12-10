<?php

require_once("autoload.php");

/* 18-9-2016 aangemaakt t.b.v. afvoeren bij alleen module melden */
$versie = '22-11-2016'; /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '4-2-2017'; /* kalender toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Afvoerlijst';
$file = "Afvoerstal.php";
include "login.php"; ?>

                <TD valign = "top">
<?php
if (Auth::is_logged_in()) {

include "kalender.php";

if(isset($_POST['knpAfvoer_'])) { include "save_afvoerstal.php"; } 
$verder = 0;
// Declaratie RELATIE
$partij_gateway = new PartijGateway();
$qryRelatiekeuze = $partij_gateway->findKlant($lidId);

$index = 0; 
$relId = [];
$relnm = [];
while ($rel = $qryRelatiekeuze->fetch_array()) { 
   $relId[$index] = $rel['relId']; 
   $relnm[$index] = $rel['naam'];
   $index++; 
} 
unset($index);
// EINDE Declaratie RELATIE
$schaap_gateway = new SchaapGateway();
$schapen = $schaap_gateway->tel_niet_afgevoerd($lidId);
?>

<form action = "Afvoerstal.php" method = "post" >
<table Border = 0 align = "center">

<?php if(!isset($_POST['knpNext_']) && !isset($_POST['knpAfvoer_'])) { ?>
<!-- optionele velden om datum en bestemming te bepalen voor afvoerlijst -->
<tr>
 <td> Optioneel een datum voor alle <?php if($schapen > 10) { echo $schapen; } ?> schapen </td>
 <td>
 <input id  = "datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($dagkeuze)) { echo $dagkeuze; } ?> > 
 </td> 
 <td> <input type = submit name = "knpNext_" value = "Verder" >
</tr>
<tr><td> Optioneel een bestemming voor alle <?php if($schapen > 10) { echo $schapen; } ?> schapen </td>
 <td>
 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:150;" name= 'kzlBestall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

    $opties = array($relId[$i]=>$relnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((isset($_POST['kzlBestall_']) && $_POST['kzlBestall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->
</td><td></td></tr>
<!-- EINDE optionele velden om datum en bestemming te bepalen voor afvoerlijst -->
<?php }
if(isset($_POST['knpNext_']) || isset($_POST['knpAfvoer_'])) { 
 if(isset($_POST['knpNext_'])) { $txtDatum = $_POST['txtDatumall_']; $kzeBest = $_POST['kzlBestall_']; }?>
<!-- AFVOERLIJST -->
<tr>
 <td colspan = 9 ></td>
 <td>
 <input type = submit name = "knpAfvoer_" value = "Afvoeren" >
 </td>
</tr>

<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Afvoeren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Generatie<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Datum<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Bestemming<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Uitval<hr></th>
 <th width = 1></th>
 <th width=60></th>
</tr>

<?php
     $result = $schaap_gateway->afvoerlijst($lidId, $Karwerk);
        while ($row = $result->fetch_array()) {
        $Id = $row['stalId']; 
        $levnr = $row['levensnummer'];
        $sekse = $row['geslacht'];
        $aanw = $row['actId']; if(isset($aanw)) { if($sekse == 'ooi') { $fase = 'moederdier'; } else if($sekse == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
    if(isset($_POST['knpAfvoer_'])) { if(isset($_POST["chbKies_$Id"])) { $cbAfv = $_POST["chbKies_$Id"]; } $txtDatum = $_POST["txtDatum_$Id"]; $kzlBest = $_POST["kzlBest_$Id"]; if(isset($_POST["chbDood_$Id"])) { $cbDood = $_POST["chbDood_$Id"]; } } ?>
<tr align = center>    
 <td width = 0> </td>
 <td width = 100 > <input type = checkbox name = <?php echo "chbKies_$Id"; ?> value = 1 <?php if(isset($cbAfv)) { echo $cbAfv == 1 ? 'checked' : ''; }  ?> > </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr ?> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase ?> </td>
 <td width = 1> </td>
 <td> <input type = text name = <?php echo "txtDatum_$Id"; ?> size = 8 value = <?php if(isset($txtDatum)) { echo $txtDatum; } ?> > </td>
 <td width = 1> </td>
 <td> 
<!-- KZLRelatie -->
 <select style="width:150;" name= <?php echo "kzlBest_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($relnm);
for ($i = 0; $i < $count; $i++){

    $opties = array($relId[$i]=>$relnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((isset($_POST['knpNext_']) && $kzeBest == $relId[$i]) || (isset($_POST["kzlBest_$Id"]) && $_POST["kzlBest_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>

 <!-- EINDE KZLRelatie-->
 </td>       
 <td width = 1> </td>
 <td width = 100 > <input type = checkbox name = <?php echo "chbDood_$Id"; ?> value = 1 <?php if(isset($cbDood)) { echo $cbDood == 1 ? 'checked' : ''; }  ?> > </td>
 <td width = 1> </td>

 <td width = 300 style = 'color : red;' > <?php
 if(isset($kzlBest) && !empty($kzlBest) && isset($cbDood)) { echo "Bestemming en uitval kan niet beiden."; } 
 else if(isset($fldDag) && isset($dmmax) && $fldDag < $dmmax) { echo "De datum mag niet voor ".$maxdm." liggen."; }
    unset($cbAfv); unset($cbDood); unset($fldDag); unset($dmmax); unset($maxdm);
 ?>  </td>

</tr>                
        
    <?php    } ?>

<!-- EINDE AFVOERLIJST -->
<?php } ?>            
</table>
</form>
        </TD>
<?php
include "menu1.php"; } ?>

</body>
</html>
