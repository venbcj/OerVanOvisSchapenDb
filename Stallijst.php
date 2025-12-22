<?php

require_once("autoload.php");

/* 20-3-2014 Ovv Rina werknr toegevoegd en sortering op werknr van laag naar hoog.
    5-8-2014 karakters werknr variabel gemaakt
    11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '11-12-2016'; /* actId = 3 genest */
$versie = '27-03-2017'; /* geslacht niet verplicht gemaakt */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '14-02-2020'; /* geneste query uit query zoek_stapel gehaald. Was left join en deed verder niks */
$versie = '27-02-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '19-08-2023'; /* Laatste scan- / controledatum toegevoegd */
$versie = '04-09-2023'; /* Export-xlsx toegevoegd */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '30-11-2024'; /* Uitgeschaarde dieren toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '19-01-2025'; /* Kolomkop vastgezet tegen de header */
$versie = '11-04-2025'; /* in query toon_aanwezigen en toon_aanwezigen aan subquery haf in where-clause and h.skip = 0 toegevoegd */


 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
<style type="text/css">

/* VASTZETTEN KOLOMKOP */
table {
  border-collapse: collapse; /* Dit zorgt ervoor dat de cellen tegen elkaar aan staan */
}

tr.StickyHeader th { /* Binnen de table row met class StickyHeader wordt deze opmaak toegepast op alle th velden */
  background: white;
  position: sticky;
  top: 86px; /* Don't forget this, required for the stickiness */
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}
/* Einde VASTZETTEN KOLOMKOP */

/* SORTEREN TABEL Bron : https://www.youtube.com/watch?v=av5wFcAtuEI */
#sortHeader th {
    cursor: pointer;
    font-size: 12px;
    /*text-align: center; dit doet niets */
    /*vertical-align: text-bottom; dit doet niets */
    height: 30px;
    border: 0px solid blue;
    /*background-color: rgb(207, 207, 207);*/
}

.desc:after {
    content: ' ▼'; /*Alt 31*/
}

.asc:after {
    content: ' ▲'; /*Alt 30*/
}

.inactive:after {
    content: ' ▲';
    color: grey;
    opacity: 0.5;
}
/* Einde SORTEREN TABEL */
</style>
</head>
<body>

<?php 
$titel = 'Stallijst';
$file = "Stallijst.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {

    $schaap_gateway = new SchaapGateway();
    $stapel = $schaap_gateway->zoekStapel($lidId);

    $aantalLam_opStal = $schaap_gateway->aantalLamOpStal($lidId);
    $aantalOoi_opStal = $schaap_gateway->aantalOoiOpStal($lidId);
    $aantalRam_opStal = $schaap_gateway->aantalRamOpStal($lidId);
?>

<table border = 0 align = "center">

<!-- Aantal dieren -->
<tr>
 <td colspan = 3 align = 'right'> <?php echo 'Aantal schapen '.$stapel; ?> </td>
 <td colspan = 2 style = 'font-size:13px';> &nbsp waarvan</td>
 <td width ="150">
<?php echo View::link_to('print pagina', 'Stallijst_pdf.php', ['style' => 'color: blue']); ?>
</td>
 <td colspan = 2 ><a href="exportStallijst.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a></td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';> <?php  echo ' - '.$aantalLam_opStal. ' lammeren'; ?> </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';>
<?php
if($aantalOoi_opStal == 1)         { echo "- $aantalOoi_opStal moeder"; }
else if($aantalOoi_opStal > 1)  { echo "- $aantalOoi_opStal moeders"; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';>
<?php
if($aantalRam_opStal == 1)         { echo '- ' .$aantalRam_opStal. ' vader'; }
else if($aantalRam_opStal > 1)  { echo '- ' .$aantalRam_opStal. ' vaders'; }
?>
 </td>
</tr>

<?php

$aantal_uitgeschaarden = $schaap_gateway->countUitgeschaarden($lidId);

 if($aantal_uitgeschaarden > 0) { ?>
<tr>
 <td colspan = 2></td>    
 <td colspan = 2 style = "font-size:12px";>
    <a href="#Uitgeschaarden" style = "color:blue";> Uitgeschaarde schapen </a>
 </td>
</tr>
<?php } ?>
</table>
<!-- Einde Aantal dieren -->

<br>

<?php
     $toon_aanwezigen = $schaap_gateway->aanwezigen($lidId, $Karwerk);
?>
<table border = 0 id="sortableTable" align = "center">

<?php
if(mysqli_num_rows($toon_aanwezigen) > 0) { ?>

<thead>
<tr id="sortHeader" class = "StickyHeader">
 <th onclick="sortTable(0)"> <br> Mijn ubn <span id="arrow0" class="inactive"></span> <hr></th>
 <th onclick="sortTable(1)"> Transponder<br> bekend <span id="arrow1" class="inactive"></span> <hr></th>
     <th onclick="sortTable(2)"> <br> Werknr <span id="arrow2" class="inactive"></span> <hr></th>
     <th onclick="sortTable(3)"> <br> Levensnummer <span id="arrow3" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(4)"> Geboren sortering <span id="arrow5" class="inactive"></span> <hr></th>
     <th onclick="sortTable(4)"> <br> Geboren <span id="arrow4" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 80 onclick="sortTable(6)"> Geslacht <span id="arrow6" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 80 onclick="sortTable(7)"> Generatie <span id="arrow7" class="inactive"></span> <hr></th>
     <th style="display:none;" valign="bottom";width= 50 onclick="sortTable(8)"> Laatstecontrole sortering <span id="arrow9" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 50 onclick="sortTable(8)"> Laatste<br> controle <span id="arrow8" class="inactive"></span> <hr></th>
</tr>

</thead>


<?php } // Einde if(mysqli_num_rows($toon_aanwezigen) > 0) ?>

<tbody id="tbody_1">
<?php
    // TODO: overstappen
while($ta = mysqli_fetch_array($toon_aanwezigen))
{
    $ubn = $ta['ubn'];
    $transponder = $ta['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
    $werknr = $ta['werknum'];
    $levnr = $ta['levensnummer'];
  $gebdm_sort = $ta['gebdm_sort'];
    $gebdm = $ta['gebdm'];
    $geslacht = $ta['geslacht']; 
    $aanw = $ta['aanw']; 
  $lstScan_sort = $ta['dag_sort'];
    $lstScan = $ta['dag']; 
    $actId_af = $ta['actId']; 
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; }

/*if(isset($vorig_ubn) && $vorig_ubn != $ubn) { ?> 
<tr><td colspan="15"><hr></td></tr>
<?php }*/ ?>

<tr align = "center">       
 <td width = 100 style = "font-size:13px;"> <?php echo $ubn; ?> <br> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo $transp; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td style="display:none;"> <?php echo $gebdm_sort; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td style="display:none;"> <?php echo $lstScan_sort; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $lstScan; ?> <br> </td>
</tr>                

    <?php /*$vorig_ubn = $ubn;*/ } // Einde while($ta = mysqli_fetch_array($result)) ?>

</tbody>
</table>

<?php
/* UITGESCHAARDE DIEREN */
if($aantal_uitgeschaarden > 0) { 

$aantalLam_uitschaar = $schaap_gateway->aantalLamUitschaar($lidId);
$aantalOoi_uitschaar = $schaap_gateway->aantalOoiUitschaar($lidId);
$aantalRam_uitschaar = $schaap_gateway->aantalRamUitschaar($lidId);
?>
<table border = 0 align = "center">
<tr id="Uitgeschaarden" height = 150> <td></td></tr>
<tr> <td colspan="14"  align="center" valign="bottom"> UITGESCHAARDE SCHAPEN </td></tr>

<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalLam_uitschaar == 1)       { echo '- ' .$aantalLam_uitschaar. ' lam'; }
else if($aantalLam_uitschaar > 1) { echo '- ' .$aantalLam_uitschaar. ' lammeren'; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalOoi_uitschaar == 1)       { echo '- ' .$aantalOoi_uitschaar. ' moeder'; }
else if($aantalOoi_uitschaar > 1) { echo '- ' .$aantalOoi_uitschaar. ' moeders'; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalRam_uitschaar == 1)      { echo '- ' .$aantalRam_uitschaar. ' vader'; }
else if($aantalRam_uitschaar > 1) { echo '- ' .$aantalRam_uitschaar. ' vaders'; } ?>
 </td>
</tr>
</table>

<table border = 0 id="sortableTable_2" align = "center">
<thead>
<tr id="sortHeader" class = "StickyHeader" style = "font-size:12px;">
 <th onclick="sortTable_2(0)"> Transponder<br> bekend <span id="arrow2_0" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(1)"> <br> Werknr <span id="arrow2_1" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(2)"> <br> Levensnummer <span id="arrow2_2" class="inactive"></span> <hr></th>
 <th style="display:none;" onclick="sortTable_2(3)"> Geboren sortering <span id="arrow2_4" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(3)"> <br> Geboren <span id="arrow2_3" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 80 onclick="sortTable_2(5)"> Geslacht <span id="arrow2_5" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 80 onclick="sortTable_2(6)"> Generatie <span id="arrow2_6" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 50 onclick="sortTable_2(7)"> Bestemming <span id="arrow2_7" class="inactive"></span> <hr></th>
</tr>

</thead>

<tbody id="tbody_2">
<?php

    $zoek_uitgeschaarden = $schaap_gateway->zoekUitgeschaarden($lidId, $Karwerk);
// TODO: #0004163 overstappen op een object dat ->each_record() ondersteunt (BCB)
while($zu = mysqli_fetch_array($zoek_uitgeschaarden))
    {
    $transponder = $zu['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
    $werknr = $zu['werknum'];
    $levnr = $zu['levensnummer'];
    $gebdm_sort = $zu['gebdm_sort'];
    $gebdm = $zu['gebdm'];
    $geslacht = $zu['geslacht']; 
    $aanw = $zu['aanw']; 
    $bestemming = $zu['naam']; 
    $actId_af = $zu['actId']; 
if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } ?>

<tr align = "center">
 <td width = 100 style = "font-size:13px;"> <?php echo $transp; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td style="display:none;"> <?php echo $gebdm_sort; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $bestemming; ?> <br> </td>

 <td width = 50> </td>
</tr>                
        
    <?php
        } // Einde while($zu = mysqli_fetch_array($result))
        ?> 
        </tbody>
        </table>
        <?php
} // Einde if(mysqli_num_rows($zoek_uitgeschaarden) > 0) ?>
<!-- EINDE UITGESCHAARDE DIEREN -->
        

        </TD>
<?php
include "menuRapport.php"; }


?>

        </TR>
    </tbody>
</table>
<?php

include "sort-2-table.js.php";

?>
</body>
</html>
