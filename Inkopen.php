<?php

require_once("autoload.php");

$versie = '17-2-14'; /*insInkat = ln['vrbat']*(_POST['txtBstat']); gewijzigd naar insInkat = _POST['txtBstat']; zodat de totale hoeveelheid kan worden ingevoerd bij inkoop ipv het totale aantal / verbruikeenheid in te voeren.*/
$versie = '27-11-2014'; /*chargenr toegevoegd.*/ 
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '20-12-2015'; /* Inkoop ook toegevoegd aan tblOpgaaf indien module financieel in gebruik */
$versie = '16-6-2018'; /* Bedrag bij ingekochte artikelen wijzigbaar. Bedrag bij inkoop niet verplicht. function verplicht() toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2018'; /* javascript toegevoegd tbv eenheid artikel wijzigen */
$versie = '7-4-2019'; /* Prijs in tblOpgaaf incl. btw gemaakt */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 1-8-2020 : kalender toegevoegd */
$versie = '28-11-2020'; /* 28-11-2020 velde chkDel toegevoegd */
$versie = '26-8-2021'; /* O.b.v. javascript inkopen per jaartal verborgen en zichtbaar gemaakt */
$versie = '17-1-2022'; /* Btw 0% en javascript verplicht() toegevoegd. SQL beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Inkoop</title>

    <style type="text/css">
        .selectt {
           /* color: #fff;
            padding: 30px;*/
            display: none;
            /*margin-top: 30px;
            width: 60%;
            background: grey;*/
            font-size: 12px;
        }
    </style>
</head>
<body>

<?php
$titel = 'Inkopen';
$file = "Inkopen.php";
include "login.php"; ?>

            <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {
include "kalender.php";

$newvoer = "
SELECT artId, stdat, naam, concat(' ', eenheid) heid, soort, eenheid
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.actief = 1
ORDER BY soort desc, naam
"; 

$q_newvoer2 = mysqli_query($db,$newvoer) or die (mysqli_error($db));
$array_eenheid = [];
    while($lin = mysqli_fetch_array($q_newvoer2))
        {

$array_eenheid[$lin['artId']] = $lin['eenheid'];

//echo $array_eenheid[$lin['artId']].'<br>';

    }

    // verwacht $array_eenheid
    include "validate-inkopen.js.php";

if (isset($_POST['knpSave_']) ) {     include "save_inkoop.php";     }

//*******************
// NIEUWE INVOER POSTEN
//*******************
$inkprijs = '';

if (isset ($_POST['knpAantal_'])) {    
    if(!empty($_POST['txtInkdm_'])) {$inkdatum = $_POST['txtInkdm_'];}
    if(!empty($_POST['txtCharge_'])) {$txtCharge = $_POST['txtCharge_'];}
    if(!empty($_POST['txtInkat_'])) {$inkwaarde = $_POST['txtInkat_'];}
    if(!empty($_POST['txtPrijs_'])) {$inkprijs = $_POST['txtPrijs_'];}    
                                }

If (isset ($_POST['knpInsert_'])) {
// Eenheid uit gekozen artikel ophalen om bij wijzigen artikel eenheid te controleren (die reeds is getoond)
$keuze_eenhd = mysqli_query($db,"
SELECT eenheid 
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a.artId = '$_POST[txtArtikel_]' 
") or die (mysqli_error($db));
while ($keusnhd = mysqli_fetch_assoc($keuze_eenhd))
{    $k_nhd = $keusnhd['eenheid'];    }
// EINDE Eenheid uit gekozen artikel ophalen

    
if (empty($_POST['txtPrijs_'])) { $insPrijs = 'NULL'; }
else { $txtPrijs = $_POST['txtPrijs_']; $insPrijs = str_replace(',', '.', $txtPrijs); }

if (empty($_POST['txtInkdm_']))    {    $insInkdm = "inkdm = NULL";    }
else        {    $dateink = date_create($_POST['txtInkdm_']);
            $insInkdm = date_format($dateink, 'Y-m-d');    }

if (!empty($_POST['txtCharge_']))    {    $insCharge = $_POST['txtCharge_'];    }

if (!empty($_POST['txtArtikel_']))    {    $insVoer = $_POST['txtArtikel_'];    }

$insInkat = $_POST['txtInkat_'];
    
    
$ophalen_waardes = mysqli_query($db,"
SELECT a.stdat, a.enhuId, a.btw, a.relId, a.rubuId, p.naam
FROM tblArtikel a
 left join tblRelatie r on (a.relId = r.relId)
 left join tblPartij p on (r.partId = p.partId)
WHERE a.artId = '".mysqli_real_escape_string($db,$insVoer)."'
") or die (mysqli_error($db));
    while ($ln= mysqli_fetch_assoc($ophalen_waardes)) {
        $enhuId = $ln['enhuId'];
        $insBtw = $ln['btw'];
        $rubuId = $ln['rubuId'];
        $relatie = $ln['naam'];
            if (empty($ln['relId']))    {    $insRc = "NULL";    }
            else        {    $insRc = "$ln[relId]";    }
            
            }

$insert_tblInkoop = "INSERT INTO tblInkoop SET dmink = '".mysqli_real_escape_string($db,$insInkdm)."', artId = '".mysqli_real_escape_string($db,$insVoer)."', charge = ". db_null_input($insCharge) .", inkat = '".mysqli_real_escape_string($db,$insInkat)."', enhuId = '".mysqli_real_escape_string($db,$enhuId)."', prijs = '".mysqli_real_escape_string($db,$insPrijs)."', btw = '".mysqli_real_escape_string($db,$insBtw)."', relId = " . db_null_input($insRc) ; 

/*echo "$insert_tblInkoop".'<br>'; ##*/mysqli_query($db,$insert_tblInkoop) or die (mysqli_error($db));

if($modfin == 1 && isset($rubuId)) {

if($insBtw > 1) { $btwBedrag = $insPrijs*$insBtw/100; }
else { $btwBedrag = 0; }

$PrijsInclBtw = $insPrijs + $btwBedrag;
        $insert_tblOpgaaf = "INSERT INTO tblOpgaaf SET rubuId = ". db_null_input($rubuId) .", datum = '".mysqli_real_escape_string($db,$insInkdm)."', bedrag = '".mysqli_real_escape_string($db,$PrijsInclBtw)."', toel = ". db_null_input($relatie) .", liq = 1 ";
        
            /*    ##*/mysqli_query($db,$insert_tblOpgaaf) or die (mysqli_error($db));
    }

} ?>
<table border= 0><tr><td>

<form action="Inkopen.php" method="post" >

<!--*********************************
         NIEUWE INVOER VELDEN
    ********************************* -->
<table border= 0>
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe inkoop : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom"> 
 <td>Inkoopdatum<hr></td>
 <td>Omschrijving<hr></td>
 <td>Chargenummer<hr></td>
 <td colspan = 2> Aantal <hr></td> 

 <td colspan = 2 width = 50 align = center>Totaalprijs excl. btw<hr></td> 
</tr>
<tr>
 <td><input id = "datepicker1" type="text" name = "txtInkdm_" size = 8 value = <?php if(isset($inkdatum)) { echo $inkdatum; } ?> ></td>
 <td>

<?php
// kzlvoer bij nieuwe invoer
$q_newvoer = mysqli_query($db,$newvoer) or die (mysqli_error($db));    ?>

 <select style= "width:280;" name = "txtArtikel_" id = "artikel" onchange = "eenheid_artikel()" >
 <option> </option>    
<?php        while($lijn = mysqli_fetch_array($q_newvoer))
        {

$name = $lijn['naam'];
if ($lijn['soort'] == 'pil') {$getal = "&nbsp per $lijn[stdat]"; $eenheid = $lijn['heid']; }
else {$getal = ''; $eenheid = '';}

$cijf = str_replace('.00', '', $getal); 
$wrde = "$name$cijf$eenheid";

        
            $opties= array($lijn['artId']=>$wrde);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['txtArtikel_']) && $_POST['txtArtikel_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>
</td>
<td><input type= "text" name = "txtCharge_" size = 14 value = <?php if(isset($txtcharge)) { echo $txtcharge; } ?> ></td>
<td><input type= "text" id="hoeveelheid" name = "txtInkat_" size = 3 value = <?php if(!isset($inkwaarde)) { $inkwaarde = 1; } echo $inkwaarde; ?> title = "Totale hoeveelheid ingekocht"> 
</td>
<td>
<p  id="aantal" > </p>
</td>
<td>
&euro;

</td>
<td><input type= "text" id="prijs" name = "txtPrijs_" size = 3  title = "Prijs totale hoeveelheid" <?php echo "value = $inkprijs "; ?> ></td> 

<td colspan = 2><input type = "submit" name = "knpInsert_" onfocus = "verplicht()" value = "Toevoegen" style = "font-size:10px;"></td></tr>

<tr><td colspan = 15><hr></td></tr>
</table>
<!--*********************************
        EINDE NIEUWE INVOER VELDEN
    ********************************* -->
</td></tr><tr><td>
<!--*****************************
             WIJZIGEN VOER
    ***************************** -->
 <table border= 0 align = "left" >
 <tr> 
  <td colspan =  16 > <b>Inkopen :</b> 
  </td>
  <td align="center" ><input type = "submit" name = "knpSave_" value = "Opslaan" style = "font-size:14px" >
 </td>
</tr>



<?php        
$current_year = date("Y");

// START LOOP
$group_jaar = mysqli_query($db,"
SELECT year(i.dmink) jaar
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.enhuId = eu.enhuId)
 join tblInkoop i on (a.artId = i.artId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY year(i.dmink)
ORDER BY year(i.dmink) desc
") or die (mysqli_error($db));

    while($lus = mysqli_fetch_assoc($group_jaar))
    {
            $jaar = ($lus['jaar']);   ?>
<tr>
 <td colspan="9">
     
 <input type="checkbox" name="jaartalCheckbox" value= <?php echo $jaar; if($jaar == $current_year) { ?> checked <?php } ?> > <?php echo $jaar; ?>
 </td>
 <td class= "<?php echo $jaar; ?> selectt" >
     Stuksprijs
 </td>
</tr>
 <tr style =  "font-size:12px;" valign =  "bottom" class= "<?php echo $jaar; ?> selectt" > 
         <th>Inkoopdatum<hr></th>
         <th></th> 
         <th>Omschrijving<hr></th>
         <th></th> 
         <th>Chargenummer<hr></th>
         <th></th> 
         <th colspan = 2>Aantal<hr></th> 
         <th></th> 
         <th width = 50>(excl.)<hr></th>
         <th></th> 
         <th>Prijs (excl.)<hr></th> 
         <th></th>
         <th>Btw<hr></th>
         <th></th>
         <th>Leverancier<hr></th> 
         <th>Verwijder<hr></th> 
          


 </tr> 

<?php
$array_btw = array(1 => '0%', 9 => '9%', 21 => '21%');

$query = mysqli_query($db,"
SELECT i.inkId, date_format(i.dmink,'%d-%m-%Y') inkdm, i.dmink, i.artId, a.naam, i.charge chargenr, inkat, i.enhuId, e.eenheid, round((i.prijs/inkat),2) stprijs, i.prijs, i.btw, p.naam crediteur, min(n.nutId) nutId, min(v.voedId) voedId
FROM tblInkoop i 
 join tblEenheiduser eu on (i.enhuId = eu.enhuId)
 join tblEenheid e on (e.eenhId = eu.eenhId)
 join tblArtikel a on (a.artId = i.artId)
 left join tblNuttig n on (n.inkId = i.inkId)
 left join tblVoeding v on (v.inkId = i.inkId)
 left join tblRelatie r on (i.relId = r.relId)
 join tblPartij p on (r.partId = p.partId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(i.dmink) = '" . mysqli_real_escape_string($db,$jaar). "'
GROUP BY i.inkId, i.dmink, i.dmink, i.artId, a.naam, i.charge, inkat, i.enhuId, e.eenheid, round((i.prijs/inkat),2), i.prijs, i.btw, p.naam
ORDER BY i.dmink desc, inkId desc
") or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($query))
    {
        $inkid = $row['inkId'];
        $inkdm = $row['inkdm'];
        $dmink = $row['dmink'];
        $naam = $row['naam'];
        $charge = $row['chargenr'];
        $bstat = $row['inkat']; 
        $eenhd = $row['eenheid'];
        $stprijs = $row['stprijs'];
        $prijs = $row['prijs'];
        $btw_db = $row['btw'];      $btw = $array_btw[$btw_db];
        $rc = $row['crediteur'];
        $nutId = $row['nutId'];
        $voedId = $row['voedId'];


/*if(isset($_POST['knpDelete_'.$inkid])) {
// crediteur toevoegen        
$delete_inkoop = "DELETE FROM tblInkoop WHERE inkId = ".mysqli_real_escape_string($db,$inkid) ;
    mysqli_query($db,$delete_inkoop) or die (mysqli_error($db));
}*/ ?>

<tr class= "<?php echo $jaar; ?> selectt" >
 <td align = center style = "font-size:12px;"><?php echo $inkdm; ?></td><td width = "1"></td>
 <td style = "font-size:16px;"><?php echo "$naam";?></td>
 <td width = "1"></td> 
 <td style = "font-size:16px;"><?php echo "$charge";?></td>
 <td width = "1"></td> 
 <td align = right style = "font-size:16px;"><?php echo $bstat;?></td>
 <td align = left style = "font-size:16px;"><?php echo "$eenhd";?></td>
 <td width = "1"></td>
 <td align = right > &euro;&nbsp <?php echo $stprijs;?> </td>
 <td width = "1"></td>
 <td align = right > &euro;&nbsp <input type = text name = <?php echo "txtPrijs_$inkid"; ?> size = 4 style = "font-size:11px; text-align:right;" value = <?php    echo $prijs;?> ></td>
 <td width = "1"></td>
 <td align = center style = "font-size:12px;"><?php if (!empty($btw)) {echo $btw;} ?></td>
 <td width = "1"></td>
 <td align = center style = "font-size:14px;"><?php echo "$rc";?></td>
 <td align = center style = "font-size:14px;"><?php if(!isset($nutId) && !isset($voedId)) { ?> 

<!--<button class=btn btn-sm btn-danger delete_class id= <?php echo $inkid; ?> >Verwijder inkoop</button> -->

      <input type = "checkbox" name= <?php echo "chkDel_$inkid"; ?> value = "Verwijder inkoop" style = "font-size:9px" >

      <?php } ?></td>
</tr>

<?php    } ?>
<tr class= "<?php echo $jaar; ?> selectt" ><td height="50"></td></tr>

<?php    } ?>

</td></tr>

</table>
<!--*****************************
         EINDE WIJZIGEN VOER
    ***************************** -->

</form>

<td><tr></table>

    </TD>
<?php } else { ?> <img src='Inkopen_php.jpg'  width='970' height='550'/> <?php }
include "menuInkoop.php"; } 

include "inkopen.js.php";
?>

</body>
</html>
