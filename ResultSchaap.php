<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt en html buiten php geprogrammeerd 
13-3-2015 : Login toegevoegd */
$versie = "22-1-2017"; /* 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel        22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '27-01-2025'; /* Sortering toegepast en vastzetten kolomkop. De gegevens klopte niet. Queries daarom ook aangepast. */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Resultaat schapen</title>
<style type="text/css">

i {
    font-size:12px;
}

b {
    font-size:13px;
}

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
th {
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
$titel = 'Resultaten per schaap uit 1 periode';
$file = "ResultHok.php";
include "login.php"; ?>

        <TD valign = 'top' align="center">
<?php
if (Auth::is_logged_in()) {

$periId = $_GET['pstId'] ?? 0;

$periode_gateway = new PeriodeGateway();
$zd = $periode_gateway->zoek_doelid($periId);
     $hokId = $zd['hokId'];
     $hok = $zd['hoknr'];
     $doelId = $zd['doelId'];
     $groep = $zd['doel'];
     $dmafsl = $zd['dmafsluit'];
     $afsldm = $zd['afsluitdm'];
    if (empty($hokId)) {
        $hokId = 1;
    }
     [$dmStartPeriode, $StartPeriodedm] = $periode_gateway->zoek_start_periode($hokId, $doelId, $dmafsl);

    $fase_tijdens_betreden_verblijf = 'true';
if($doelId == 1) { $fase_tijdens_betreden_verblijf = '( (isnull(spn.datum) and isnull(prnt.datum)) or h.datum < spn.datum)'; }
if($doelId == 2) { $fase_tijdens_betreden_verblijf = '((h.datum >= spn.datum and (isnull(prnt.datum) or h.datum < prnt.datum)) or (isnull(spn.datum) and h.datum < prnt.datum))'; }
if($doelId == 3) { $fase_tijdens_betreden_verblijf = '(h.datum >= prnt.datum or ht.datum > prnt.datum)'; }

$bezet_gateway = new BezetGateway();
$zoek_periode_met_aantal_schapen = $bezet_gateway->zoek_periode_met_aantal_schapen($lidId, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf);
    while($zpmas = $zoek_periode_met_aantal_schapen->fetch_assoc()) { 
     $schapen = $zpmas['aant_schapen'];
     $bewegingen = $zpmas['aant_beweging'];
     $dmEerste_in = $zpmas['dmEerste_in'];
     $eerste_inDm = $zpmas['eerste_inDm'];
     $laatste_uit = $zpmas['laatste_uit'];
    } 

if($dmStartPeriode < $dmEerste_in || !isset($dmStartPeriode)) { $StartPeriodedm = $eerste_inDm; } ?>

<table Border = 0>
<tr>
 <td colspan = 3 align = "right"><b style = "font-size:20px;"> <?php echo $hok; ?> </b></td> 
 <td colspan = 3 ><i> &nbsp &nbsp Doelgroep : </i><b> <?php echo $groep; ?> </b></td> 
 <td colspan = 7 ><i> &nbsp &nbsp Periode : </i><b><?php echo $StartPeriodedm." - ".$afsldm; ?></b></td>
</tr>
<tr>
 <td colspan= 6 align="right"><i> Aantal schapen : </i><b> <?php echo $schapen; ?> </b></td>
<?php if($bewegingen > $schapen) { ?>
<tr>
 <td colspan= 6 align="right"><i> Aantal bewegingen : </i><b> <?php echo $bewegingen; ?> </b></td>
<?php } ?>
</tr>
</table>

<?php
    $zoek_inhoud_periode = $bezet_gateway->zoek_inhoud_periode($lidId, $hokId, $dmafsl, $dmStartPeriode, $fase_tijdens_betreden_verblijf, $Karwerk);
?>
 
<table Border = 0 id="sortableTable" align = "center">
  <thead> 
<tr class = "StickyHeader" style = "font-size:12px;">
 <th onclick="sortTable(0)" width= 80> <br> Werknr <span id="arrow0" class="inactive"></span><hr></th>
 <th onclick="sortTable(1)" width= 80> <br> Ras <span id="arrow1" class="inactive"></span><hr></th>
 <th onclick="sortTable(2)" width= 80> <br> Geslacht <span id="arrow2" class="inactive"></span><hr></th>
 <th style="display:none;" onclick="sortTable(3)" > <br> Datum erin sorteren <span id="arrow4" class="inactive"></span><hr></th>
 <th onclick="sortTable(3)" width= 80> <br> Datum erin <span id="arrow3" class="inactive"></span><hr></th>
 <th style="display:none;" onclick="sortTable(5)"> <br> Datum eruit sorteren <span id="arrow6" class="inactive"></span><hr></th>
 <th onclick="sortTable(5)" width= 80> <br> Datum eruit <span id="arrow5" class="inactive"></span><hr></th>
 <th onclick="sortTable(7)" width= 80> Schaap-<br>dagen <span id="arrow7" class="inactive"></span><hr></th>
 <th onclick="sortTable(8)" width= 80> Begin<br>gewicht <span id="arrow8" class="inactive"></span><hr></th>
 <th onclick="sortTable(9)" width= 80> Eind<br>gewicht <span id="arrow9" class="inactive"></span><hr></th>
 <th onclick="sortTable(10)" width= 80> <br> Gem groei <span id="arrow10" class="inactive"></span><hr></th>
 <th onclick="sortTable(11)" width= 80>Reden uit verblijf <span id="arrow11" class="inactive"></span><hr></th>
</tr>
 </thead>
<tbody>
<?php
        while($zip = $zoek_inhoud_periode->fetch_array()) {     
            $werknr = $zip['werknr'];
            $ras = $zip['ras'];
            $geslacht = $zip['geslacht'];
            $indm_sort = $zip['indm_sort'];
            $indm = $zip['indm'];
            $uitdm_sort = $zip['uitdm_sort'];
            $uitdm = $zip['uitdm'];
            $uitvdm_sort = $zip['uitvdm_sort'];
            $uitvdm = $zip['uitvdm'];
            $schpdgn = $zip['schpdgn'];
            $kgin = $zip['kgin'];
            $kguit = $zip['kguit'];
            $gemgroei = $zip['gemgroei'];

            if($groep == 'Geboren' && $zip['status'] == 'Eruit') { $status = 'Gespeend'; } 
           else if($groep == 'Gespeend' && $zip['status'] == 'Eruit') { $status = 'Afgeleverd'; }
           else { $status = $zip['status']; } ?>
        
<tr align = "center">
 <td style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>    
 <td style="display:none;" style = "font-size:15px;"> <?php echo $indm_sort ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $indm ?> <br> </td>
<?php       If (empty($uitdm))
{ ?>
 <td style="display:none;" style = "font-size:15px;"> <?php echo $uitvdm_sort; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $uitvdm; ?> <br> </td>
<?php }
else    
{ ?>
 <td style="display:none;" style = "font-size:15px;"> <?php echo $uitdm_sort; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $uitdm; ?> <br> </td>
<?php } ?>
 <td style = "font-size:15px;"> <?php echo $schpdgn; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $kgin; ?> <br> </td>
 <td wstyle = "font-size:15px;"> <?php echo $kguit; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $gemgroei; ?> <br> </td>
 <td style = "font-size:15px;"> <?php if(isset($status)) { echo $status; } else {echo "Onbekend"; } ?> <br> </td>
</tr>                
        
<?php        } ?>
    
</tbody>            
</table>


        </TD>
<?php
include "menuRapport.php"; }

include "sort-1-table.js.php";

?>


</body>
</html>
