<?php

require_once("autoload.php");

/*
<!-- 8-3-2015 : Login toegevoegd
14-11-2015 naamwijziging van Voer naar Voerbestand -->
 */
$versie = '19-12-2015'; /* : Rubriek toegevoegd */
$versie = '1-8-2017'; /* save_artikel.php toegevoegd */ 
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '7-4-2019'; /* Btw gewijzigd van 6 naar 9% */
$versie = '17-1-2022'; /* Btw 0% en javascript verplicht() toegevoegd. SQL beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<?php
$titel = 'Voer';
$file = "Voer.php";
include "login.php"; ?>

            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {
    $artikel_gateway = new ArtikelGateway();
    $eenheid_gateway = new EenheidGateway();
    $inkoop_gateway = new InkoopGateway();
    $partij_gateway = new PartijGateway();
    $rubriek_gateway = new RubriekGateway();
    include "validate-voer.js.php";

if (isset($_POST['knpSave_'])) { include "save_artikel.php"; }

//*******************
// NIEUWE INVOER POSTEN
//*******************
if (isset ($_POST['knpInsert_'])) {    

    $dubbel = $artikel_gateway->countVoerByName($lidId, $_POST['insNaam_']);

    if (!empty($dubbel) && $dubbel >= 1 )
    {
        echo "Dit voer bestaat al.";
    }
    else 
    {
if (!empty($_POST["insNaam_"]))    {    $insNaam = $_POST['insNaam_'];    } // Verplicht veld

if (!empty($_POST['insStdat_'])) {    $insStdat = $_POST['insStdat_'];    } // Verplicht veld

if (!empty($_POST['insNhd_']))    {    $insNhd = $_POST['insNhd_'];    } // Verplicht veld

if (!empty($_POST['insBtw_']))    {    $insBtw = $_POST['insBtw_'];    } // Verplicht veld
  
if (!empty($_POST['insRelatie_']))    {    $insRelatie = $_POST['insRelatie_'];    }

if ($modfin == 1  && !empty($_POST['insRubriek_']))    {    $insRubriek = $_POST['insRubriek_'];    }

$artikel_gateway->store($insNaam, $insStdat, $indNhd, $indBtw, $insRelatie, $insRubriek);
    }
}

//*****************************
//** ARTIKELEN IN GEBRUIK
//*****************************
?>
<form action="Voer.php" method="post" >
<table border= 0 align =  "left" > 
<tr> 
 <td colspan = 10 > <b>Voer in gebruik :</b> </td>
 <td colspan = 2 align=right><input type = "submit" name="knpSave_" value = "Opslaan"  style = "font-size:12px;"></td>
</tr> 


 <tr style =  "font-size:12px;" align = "center" valign =  "bottom"> 
         <th width = 200 >Omschrijving *</th>
         <th></th> 
         <th>stand.<br>aantal</th> 
         <th>Eenheid&nbsp*</th>
         <th></th> 
         <th>Btw</th> 
         <th>Leverancier</th> 
<?php if($modfin == 1 ) { ?>
         <th>Rubriek **</th>  <?php } ?>

         <th>Actief</th> 
 </tr> 
<?php        
// START LOOP
$loop = $artikel_gateway->findVoerByUser($lidId);

// TODO kan dit in 1 query?
    while($lus = mysqli_fetch_assoc($loop)) {
            $Id = $lus['artId'];  
            $qryArtikel = $artikel_gateway->details($Id);

// TODO: Inline Temp
    while($row = mysqli_fetch_assoc($qryArtikel)) {
        $soort = "{$row['soort']}";
        $voer = "{$row['naam']}";
        $stdat = "{$row['stdat']}";
        $enhuId = "{$row['enhuId']}";
        $eenhd = "{$row['eenheid']}";
        $btw = "{$row['btw']}";
        $rubuId = "{$row['rubuId']}";
        $relId = "{$row['relId']}";
        $actief = "{$row['actief']}";

// Bepalen of artikel al is ingekocht
        $rows_inkoop = $inkoop_gateway->countArtikel($Id);
// EINDE Bepalen of artikel al is ingekocht

?>
<tr style = "font-size:12px;">
 <td style = "font-size : 14px;">
<?php
// Veld Omschrijving (al dan niet te wijzigen) 
If ($rows_inkoop > 0) { echo $voer; }
else     { ?>

    <input type= "text" name= <?php echo "txtNaam_$Id"; ?> size = 30 value = <?php echo "'".$voer."'"; ?> style = "font-size:13px"; > 
<?php    } 
// EINDE  Veld Omschrijving (al dan niet te wijzigen) 
?></td>
<td width = 1></td>
<td> 

<input type= "text" name= <?php echo "txtStdat_$Id"; ?> size = 4 style = "font-size:12px; text-align : right;" title = "Standaard verbruikshoeveelheid" value = <?php echo $stdat; ?> >


?></select>

<?php }    ?>
</td>
<?php

// EINDE kzlVerbruikseenheid (al dan niet te wijzigen)

?>        <td width = 1 ></td><td>
<?php
// kzlBtw bij wijzigen        
$opties = array('1' => '0%', '9' => '9%', '21' => '21%'); ?>

 <select name= <?php echo "kzlBtw_$Id"; ?> style= "width:50;" style = "font-size:12px;">
<?php
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave_']) && $btw == $key) || (isset($_POST["kzlBtw_$Id"]) && $_POST["kzlBtw_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }

} ?>
    </select> </td>

 <select style= "width:110;" name= <?php echo "kzlRelatie_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php $count = count($relnm);
for ($i = 0; $i < $count; $i++){

    $opties = array($rel_Id[$i]=>$relnm[$i]);
            foreach($opties as $key => $waarde)
            {

   if ((!isset($_POST['knpSave_']) && $relId == $rel_Id[$i]) || (isset($_POST["kzlRelatie_$Id"]) && $_POST["kzlRelatie_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';
  }               
            }
} ?>
</select>
 </td>
<!-- EINDE kzlLeverancier bij wijzigen -->

<?php      
if($modfin == 1 ) {



<!-- KZLRUBRIEK bij wijzigen-->
<td>
 <select style="width:140;" name= <?php echo "kzlRubriek_$Id"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php 
$count = count($rubnm);
for ($i = 0; $i < $count; $i++){

            $opties = array($rubId[$i]=>$rubnm[$i]);
            foreach ($opties as $key => $waarde)
            {
                        
        if( (!isset($_POST['knpSave_']) && $rubuId == $rubId[$i]) || (isset($_POST["kzlRubriek_$Id"]) && $_POST["kzlRubriek_$Id"] == $key) )
        {
            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
        }
        else
        {        
        echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            
        }
}
?>
</select>
<!-- EINDE KZLRUBRIEK bij wijzigen -->
 </td> <?php } ?>
 <td> <input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id="c1" value="1" <?php echo $actief == 1 ? 'checked' : ''; ?> title = "Is voer te gebruiken ja/nee ?"> </td>
    
 <td></td>
 <td></td> 

<?php } ?>

<td></td></tr>

<?php } ?>
</td></tr>
<tr><td colspan = 6 style= "font-size : 13px";><sub>* Eenmaal ingekocht niet meer wijzigbaar !
 <?php if($modfin == 1 ) { ?> <br>** t.b.v. automatisch aanmaken factuur na inkopen ! <?php } ?>
 </sub></td></tr>

<tr><td colspan = 15><hr></td></tr>

<!--
*************************************
** EINDE ARTIKELEN IN GEBRUIK
*************************************
    
*********************************
 VELDEN TBV NIEUWE INVOER
********************************* -->


<tr><td style = "font-size:13px;"><i> Nieuw voer : </i></td></tr>
<tr><td><input type="text" id="artikel" name= "insNaam_" size = 30 value = '' maxlength = 50></td>
<td></td>
<td><input type= "text" id="standaard" name= "insStdat_" value = 1 size = 1 style = "text-align : right; font-size:13px;" title = "Standaard verbruikshoeveelheid"></td>
<td>
<?php
// kzlverbruikseenheid bij nieuwe invoer
$newvrb = $eenheid_gateway->findByLid($lidId);
?>
 <select style= "width:50;" id ="eenheid" name= "insNhd_" >
 <option></option> <?php
         while($lijn = mysqli_fetch_array($newvrb))
        {
        
            $opties= array($lijn['enhuId']=>$lijn['eenheid']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['insNhd_']) && $_POST['insNhd_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>

</td>
<td width = 1 ></td>
<td>

<?php
// kzl btw bij nieuwe invoer
$opties = array('' => '', '1' => '0%', '9' => '9%', '21' => '21%'); ?>

 <select name= "insBtw_" id="btw" style= "width:50;">
<?php
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['insBtw_']) && $_POST['insBtw_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select>

</td>
<td>
<?php
// kzlLeverancier bij nieuwe invoer
    $newcrediteur = $partij_gateway->findLeverancier($lidId);
?>
 <select name= "insRelatie_" style= "width:110;" >
 <option> </option>    
<?php        while($regel = mysqli_fetch_array($newcrediteur))
        {
        
            $opties= array($regel['relId']=>$regel['naam']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['insRelatie_']) && $_POST['insRelatie_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>

</td>
<?php if($modfin == 1 ) { ?>
<td>
<!-- KZLRUBRIEK bij nieuwe invoer -->
<?php
        $newRubriek = $rubriek_gateway->zoek_hoofdrubriek_6($lidId);
?>
 <select style="width:180;" name= "insRubriek_" value = "" style = "font-size:12px;">
  <option></option>
<?php        while($nwrub = mysqli_fetch_array($newRubriek))
        {

            $opties = array($nwrub['rubuId'] => $nwrub['rubriek']);
            foreach ($opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['insRubriek_']) && $_POST['insRubriek_'] == $key)
        {
            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
        }
        else
        {        
        echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            }
            
        } ?>
</select>
<!-- EINDE KZLRUBRIEK bij nieuwe invoer -->
</td>
<?php } ?>
<td><input type="checkbox" name="boxActief_" id="c2" <?php if(true){ echo "checked"; } ?>  disabled ></td><?php
?>
<td colspan = 2><input type = "submit" name="knpInsert_" onfocus="verplicht()" value = "Toevoegen" style = "font-size:10px;"></td></tr>
<!--
*********************************
 EINDE  VELDEN TBV NIEUWE INVOER
*********************************     -->

<tr><td colspan = 15><hr></td></tr>


<?php

//*****************************
//** ARTIKELEN NIET IN GEBRUIK
//***************************** 
// Aantal artikelen niet in gebruik 
    $niet_actief = $artikel_gateway->tel_niet_in_gebruik($lidId);
if ($niet_actief > 0) {
?>
 <tr> 
 <td colspan =  4 height = 80 valign = "bottom"> 
 <b>Voer niet in gebruik:</b> 
 </td></tr> 


 <tr style =  "font-size:12px;" valign =  "bottom"> 
         <th align = "left" >Omschrijving </th>
         <th></th> 
         <th>stand. aantal</th> 
         <th>Verbruiks eenheid</th>
         <th></th> 
         <th>Btw</th> 
         <th>Leverancier</th> 
<?php if($modfin == 1 ) { ?>
         <th>Rubriek</th>  <?php } ?>
         <th>Actief</th> 
 </tr> 
<?php        
// START LOOP
    $loop = $artikel_gateway->zoek_niet_in_gebruik($lidId);

    while($lus = mysqli_fetch_assoc($loop)) {
            $Id = $lus['artId'];  

            $qryArtikel = $artikel_gateway->details_met_partij($Id);
    while($row = mysqli_fetch_assoc($qryArtikel)) {
        $soort = "{$row['soort']}";
        $voer = "{$row['naam']}";
        $stdat = "{$row['stdat']}";
        $enhuId = "{$row['enhuId']}";
        $eenhd = "{$row['eenheid']}";
        $btw = "{$row['btw']}";
        $relatie = $row['relatie'];
        $rubriek = "{$row['rubriek']}";
        $actief = "{$row['actief']}";
?>
        <tr style = "font-size:12px;">
        <td style = "font-size : 14px;">
<?php
// Veld Medicijnnaam
echo $voer; 
// EINDE  Veld Medicijnnaam
?></td>
<td width = 1></td>

<td align = "center" >
<!-- Standaard verbruiksaantal -->
<?php echo $stdat; ?>        

</td><td align = "center" >
<?php // Verbruikseenheid
echo $eenhd; ?>
</td>    
<?php
// EINDE Verbruikseenheid

?>        <td width = 1 ></td>
<td align = "center" >
<?php
// Btw
echo $btw;  ?>
 </td>
<!-- EINDE Btw
 Leverancier -->
 <td> 
     <?php if(isset($relatie)) { echo $relatie; } ?>
 </td>
<!-- EINDE Leverancier -->

<!-- Rubriek -->
<td align = "center">
    <?php echo $rubriek; ?>
</td>
<!--EINDE Rubriek -->    
 <td align = "center">Nee</td>
 <td>
  <input type = "submit" name="knpActive_" value = "Activeer"  style = "font-size:12px;">
 </td>
<?php if (isset ($_POST['knpActive_'])) {

echo $activeer.'<br>';
$artikel_gateway->activeer($Id);
        //header("Location:  " .  echo $url;  . "Medicijnen.php");
    } ?>
<td></td></tr>

<?php    } ?> 
</td></tr>


<!--
*************************************
** EINDE ARTIKELEN NIET IN GEBRUIK
************************************* -->

<?php } ?>

<tr><td colspan = 15><hr></td></tr>

<?php } // EINDE Aantal artikelen niet in gebruik  ?>
</table>
</form>


    </TD>
<?php } else { ?> <img src='voer_php.jpg'  width='970' height='550'/> <?php }
include "menuInkoop.php"; } ?>

</body>
</html>
