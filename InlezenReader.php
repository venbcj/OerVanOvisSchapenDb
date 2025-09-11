<?php 

require_once("autoload.php");

$versie = '10-4-2014'; /*vw_Reader_sp wordt gebruikt in InsSpenen*/
$versie = '13-4-2014'; /*vw_Reader_ovpl wordt gebruikt in InsOverplaatsen */
$versie = '20-2-2015'; /*login toegevoegd*/ 
$versie = '18-11-2015'; /*gewijzigd inlezen aanwas naar inlezen aanvoer en inlezen locatie naar inlezen verblijf*/
$versie = '16-9-2016'; /*overschrijven van reader.txt gewijzigd in aanvullen*/
$versie = '22-6-2018'; /*Velden in impReader aangepast*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '2-2-2020'; /* De root naar alle bestanden op de FTP server variabel gemaakt */
$versie = '15-3-2020'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-6-2020'; /* Overleggen gewijzigd in adoptie */
$versie = '30-9-2020'; /* Halsnummers toegevoegd */
$versie = '14-11-2020'; /* Medicatie aangepast i.v.m. mogelijk vanuit reader Agrident */
$versie = '20-06-2021'; /* Voerregistratie toegevoegd */
$versie = '18-12-2021'; /* Dekken en Dracht toegevoegd */
$versie = '05-08-2023'; /* Stallijstscan toegevoegd */
$versie = '02-12-2023'; /* Tussenweging toegevoegd */
$versie = '03-11-2024'; /* Uitscharen en terug van uitscharen toegevoegd */
$versie = '21-12-2024'; /* Bestanden uploaden van raeder Biocontrol verwijderd */
$versie = '31-12-2024'; /* include login voor include header gezet */
$versie = '28-02-2025'; /* Als de stallijst leeg is wordt de link inlezen stallijst nieuwe klant ook getoond */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php 
$titel = 'Inlezen reader';
$file = "InlezenReader.php";
include "login.php"; ?>

         <TD>
<?php
if (Auth::is_logged_in()) {

$_SESSION["RPP"] = 30; $RPP = $_SESSION["RPP"];
$_SESSION["PA"] = 1; $pag = $_SESSION["PA"];

include "responscheck.php"; ?>

 <form action="#" method="post" enctype="multipart/form-data">

<?php include "inlezenAgrident.php"; 
$zoek_lege_stallijst = mysqli_query($db,"
SELECT count(stalId) aant
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' 
") or die (mysqli_error($db));

   while ( $zls = mysqli_fetch_assoc ($zoek_lege_stallijst)) { $stallijstaantal = $zls['aant']; } ?>

<table border = 0 align="center" style = "font-size: 17px"; >
   <h2 align="center" style="color:blue";>Hier kun je de gegevens uit de reader verwerken<br> in het managementprogramma.</h2>
<tr height = 50 ><td></td> </tr>

<?php $leeg = "<a href=' ". $url . "InlezenReader.php' style = 'color : blue'>"; 

if($aantNewLid > 0 || $stallijstaantal == 0) { ?>
<tr height = 50 valign="top">
 <td> <?php if (!empty($aantNewLid)){ ?> <a href='<?php echo $url;?>InsStallijstscan_nieuwe_klant.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen stallijst nieuwe klant </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantNewLid)){ echo "&nbsp $aantNewLid dieren in te lezen."; } ?>
 </td>
</tr>

<?php } ?>


<tr>
 <td>
    <?php if (!empty($aantdek)){ ?> <a href='<?php echo $url;?>InsDekken.php' style = 'color : blue'> <?php }
          else { echo "$leeg"; } ?>
inlezen dekken </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantdek)){ echo "&nbsp $aantdek dekkingen in te lezen.";   } ?>
 </td>
</tr>

<tr>
 <td>
    <?php if (!empty($aantdra)){ ?> <a href='<?php echo $url;?>InsDracht.php' style = 'color : blue'> <?php }
          else { echo "$leeg"; } ?>
inlezen dracht </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantdra)){ echo "&nbsp $aantdra dracht in te lezen.";   } ?>
 </td>
</tr>

<tr>
 <td>
  <?php if (!empty($aantgeb)){ ?> <a href='<?php echo $url;?>InsGeboortes.php' style = 'color : blue'> <?php }
        else { echo "$leeg"; } ?>
inlezen geboortes </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantgeb)){ echo "&nbsp $aantgeb geboorte(s) in te lezen."; } ?>
 </td>
</tr>

<?php if($reader == 'Agrident') { ?> 
<tr>
 <td>
  <?php if (!empty($aantLbar)){ ?> <a href='<?php echo $url;?>InsLambar.php' style = 'color : blue'> <?php }
        else { echo "$leeg"; } ?>
inlezen lambar </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantLbar)){ echo "&nbsp $aantLbar lambar in te lezen."; } ?>
 </td>
</tr>
<?php } ?>

<tr>
 <td>
  <?php if (!empty($aantspn)){ ?> <a href='<?php echo $url;?>InsSpenen.php' style = 'color : blue'> <?php }
        else { echo "$leeg"; } ?>
inlezen gespeenden </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantspn)){ echo "&nbsp $aantspn gespeenden in te lezen.";    } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantwg)){ ?> <a href='<?php echo $url;?>InsWegen.php' style = 'color : blue' > <?php }
            else { echo "$leeg"; } ?>
inlezen wegingen </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantwg)){ echo "&nbsp $aantwg wegingen in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td>
  <?php if (!empty($aantafl)){ ?><a href='<?php echo $url;?>InsAfvoer.php' style = 'color : blue'><?php }
        else { echo "$leeg"; } ?>
inlezen afvoer </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantafl)){ echo "&nbsp $aantafl afgeleverden in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td>
  <?php if (!empty($aantUitsch)){ ?><a href='<?php echo $url;?>InsUitscharen.php' style = 'color : blue'><?php }
        else { echo "$leeg"; } ?>
inlezen uitscharen </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantUitsch)){ echo "&nbsp $aantUitsch afgeleverden in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td>
  <?php if (!empty($aantuitv)){ ?><a href='<?php echo $url;?>InsUitval.php' style = 'color : blue'> <?php }
        else { echo "$leeg"; } ?>
inlezen uitval </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantuitv)){ echo "&nbsp $aantuitv uitval in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantaanw)){ ?> <a href='<?php echo $url;?>InsAanvoer.php' style = 'color : blue'>  <?php }
            else {echo "$leeg"; } ?>
inlezen aanvoer </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantaanw)){ echo "&nbsp $aantaanw aanwas in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantTvUitsch)){ ?> <a href='<?php echo $url;?>InsTvUitscharen.php' style = 'color : blue'>  <?php }
            else {echo "$leeg"; } ?>
inlezen terug van uitscharen </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantTvUitsch)){ echo "&nbsp $aantTvUitsch terug van uitscharen in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantovpl)){ ?> <a href='<?php echo $url;?>InsOverplaats.php' style = 'color : blue'>  <?php }
            else { echo "$leeg"; } ?>
inlezen overplaatsen </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantovpl) && empty($speen_ovpl)){    echo "&nbsp $aantovpl overplaatsingen in te lezen.";    }
    else if (!empty($aantovpl) && $speen_ovpl == 1)    {    echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moet worden gespeend. *";    }
    else if (!empty($aantovpl) && $speen_ovpl > 1)    {    echo "&nbsp $aantovpl overplaatsingen in te lezen waarvan er $speen_ovpl eerst moeten worden gespeend. *";    } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantadop)){ ?> <a href='<?php echo $url;?>InsAdoptie.php' style = 'color : blue'>  <?php }
            else { echo "$leeg"; } ?>
inlezen adoptie </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantadop)){ echo "&nbsp $aantadop adoptie in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantpil)){ ?> <a href='<?php echo $url;?>InsMedicijn.php' style = 'color : blue' > <?php }
            else { echo "$leeg"; } ?>
inlezen medicatie </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantpil)){ echo "&nbsp $aantpil medicatie in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantomn)){ ?> <a href='<?php echo $url;?>InsOmnummeren.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen omnummeren </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantomn)){ echo "&nbsp $aantomn omnummeren in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aanthals)){ ?> <a href='<?php echo $url;?>InsHalsnummers.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen halsnummers </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aanthals)){ echo "&nbsp $aanthals halsnummers in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantvoer)){ ?> <a href='<?php echo $url;?>InsVoerregistratie.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen voerregistratie </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantvoer)){ echo "&nbsp $aantvoer voerregistraties in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantubn)){ ?> <a href='<?php echo $url;?>InsGrWijzigingUbn.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen ubn wijziging </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantubn)){ echo "&nbsp $aantubn ubn wijzigingen in te lezen."; } ?>
 </td>
</tr>

<tr>
 <td> <?php if (!empty($aantscan)){ ?> <a href='<?php echo $url;?>InsStallijstscan_controle.php' style = 'color : blue'><?php }
            else { echo "$leeg"; } ?>
inlezen stallijstscan </a>
 </td>
 <td style = "font-size : 14px;">
    <?php if (!empty($aantscan)){ echo "&nbsp $aantscan stallijstscans in te lezen."; } ?>
 </td>
</tr>

</table>
<br><br><br>
<table>
<tr><td style = "font-size : 13px ;"> <?php if (!empty($aantovpl) && $speen_ovpl > 0) { ?> * Mogelijk moeten schapen worden herverdeeld na het spenen.<br> Deze herverdeling (= functie locatie in reader) gebeurt gelijktijdig met het inlezen van gespeende lammeren.<?php } ?>    </td>
</tr>
</table>

</form>

    </TD>
<?php
include "menu1.php"; } ?>
</tr>
</table>

</body>
</html>
<!-- Aantal in te lezen medicatie niet zichtbaar maken. -->
