<?php

require_once("autoload.php");

/* 28-11-2014 Chargenummer toegevoegd
    11-3-2015 : Login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '5-7-2020'; /* wdgn gewijzigd naar wdgn_v en wdgn_m */
$versie = '29-4-2023'; /* sql beveiligd met quotes */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>
<?php
$titel = 'Rapportage per medicijn';
$file = "Med_rapportage.php";
include "login.php";
?>
                <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        $schaap_gateway = new SchaapGateway();
        $minjaar = date("Y") - 8;
        $maxjaar = date("Y");
        if (isset($_POST['knpToon'])) {
            $kzlpil = $_POST['kzlpil'];
        }
?>
<table Border = 0 align = "center">
<?php
        $artikel_gateway = new ArtikelGateway();
        $kzl = $artikel_gateway->pilForLid($lidId);
?>
<form action = "Med_rapportage.php" method = "post">
<tr> <td> </td>
<td colspan = 4 style = "font-size : 13px;">
<?php
/*$minjaar = date("Y")-2;
$maxjaar = date("Y");*/
        if (isset($_POST['knpToon']) && !empty($_POST['kzlpil'])) {
            $kzlpil = $_POST['kzlpil'];
            $aantperiodes = $artikel_gateway->aantal_periodes($lidId, $minjaar, $maxjaar, $_POST['kzlpil']);
            if ($aantperiodes >1) {
                echo "Mogelijkheid filter periode " ;
                //kzlJaarMaand
                // Verzameld alle jaarmaanden van een toegediend medicijn.
                $kzljrmnd = $artikel_gateway->periodes($lidId, $minjaar, $maxjaar, $_POST['kzlpil']);
                $name = "kzlmdjr";
                $width = 108;
?>
<select name=<?php echo"$name";?> style="font-size : 13px width:<?php echo "$width";?>;\" >
 <option></option>
<?php
                while($row = $kzljrmnd->fetch_array()) {
                    $maand = $row['mnd'];
                    $mndname = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
                    $jaar = $row['jaar'];
                    $kzlkey = "$row[jrmnd]";
                    $kzlvalue = "$mndname[$maand] $row[jaar]";
                    include "kzl.php";
                }
            }
        }
        // EINDE kzlJaarMaand
?>
</select>
 </td>
<td colspan = 3>
<?php
        $label = "Kies een medicijn &nbsp " ;
        if (isset($_POST['knpToon']) && !empty($_POST['kzlpil'])) {
            $label = "";
        }
        echo $label;
        //kzlMedicijn
        // TODO: (BCB) #0004170 $kzl verder extraheren, zit nu half in artikelgateway
        $name = "kzlpil";
        $width= 200 ;
?>
<select name=<?php echo"$name";?> style="width:<?php echo "$width";?>;\" >
 <option></option>
<?php        while($row = $kzl->fetch_array())
        {
            $kzlkey="$row[artId]";
            $kzlvalue="$row[naam]";
            include "kzl.php";
        }
        // EINDE kzlMedicijn
?>
</select>
 </td>
 <td colspan = 2> <input type = "submit" name ="knpToon" value = "Toon"> </td></tr>
</form>
<tr>
<td> </td>
<td>
<?php
        if (isset($_POST['knpToon']) && !empty($_POST['kzlpil'])) {
            if ($rows_per <= 1 || empty($_POST['kzlmdjr'])) {
                $resJrmnd = "( date_format(h.datum,'%Y%m') is not null )";
            } elseif ($rows_per > 1 && !empty($_POST['kzlmdjr'])) {
                $resJrmnd = "( date_format(h.datum,'%Y%m') = $_POST[kzlmdjr] )";
            }
            //$maandjaren verzameld alle maandjaren die worden gevonden
            $maandjaren = $artikel_gateway->maandjaren($lidId, $minjaar, $maxjaar, $artId, $redJrmnd);
            while ($rij = $maandjaren->fetch_assoc()) {
                $mndnr = $rij['mnd'];
                $jr = $rij['jaar'];
                $mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december');
                $tot = date("Ym");
                $maand = date("m");
                $jaarstart = date("Y")-8;
                //$vanaf = "$jaarstart$maand";
?>
<tr style = "font-size:18px;" ><td></td><td colspan = 3><b><?php echo "$mndnaam[$mndnr] &nbsp $rij[jaar]"; ?></b></td></tr>
<tr style = "font-size:12px;">
<tr><td colspan = 9><hr></td></tr>
<?php
                // TOTALEN
                $sekse = 's.geslacht is not null';
                $ouder = 'isnull(oudr.hisId)';
                $werknrs = $schaap_gateway->med_aantal_fase($lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
                if ($werknrs == 1) {
                    $fasen = 'lam';
                } elseif (isset($werknrs)) {
                    $fasen = 'lammeren';
                }
                $voer = $schaap_gateway->voer_fase($lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
                $eenheid = $schaap_gateway->eenheid_fase($lidId,$mndnr,$jr,$kzlpil,$sekse,$ouder);
?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"><b></b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"><b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"><b> <?php if (isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if (isset($voer)) { echo "$voer $eenheid"; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;" align = "right"><b>  </b><br> </td>
 </tr>
<?php
                unset($fasen);
                $sekse = 's.geslacht = \'ooi\'';
                $ouder = 'oudr.hisId is not null';
                $werknrs = $schaap_gateway->med_aantal_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
                if ($werknrs == 1) {
                    $fasen = 'moederdier';
                } elseif (isset($werknrs)) {
                    $fasen = 'moederdieren';
                }
                $voer = $schaap_gateway->voer_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
                $eenheid = $schaap_gateway->eenheid_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <b> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if (isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if (isset($voer)) { echo "$voer $eenheid"; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;" align = "right"> <b>  </b><br> </td>
 </tr>
<?php
                unset($fasen);
                $sekse = 's.geslacht = \'ram\'';
                $ouder = 'oudr.hisId is not null';
                $werknrs = $schaap_gateway->med_aantal_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
                if ($werknrs == 1) {
                    $fasen = 'vaderdier';
                } elseif (isset($werknrs)) {
                    $fasen = 'vaderdieren';
                }
                $voer = $schaap_gateway->voer_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
                $eenheid = $schaap_gateway->eenheid_fase($lidId, $mndnr, $jr, $kzlpil, $sekse, $ouder);
?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <b> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php echo $werknrs; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if (isset($fasen)) { echo $fasen; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <b> <?php if (isset($voer)) { echo "$voer $eenheid"; }; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;" align = "right"> <b> </b><br> </td>
 </tr> <?php
                // EINDE TOTALEN
?>
<tr><td colspan = 25><hr></td></tr>
<tr ><td colspan = 25></td></tr>
<?php
                $nuttig_gateway = new NuttigGateway();
                $result = $nuttig_gateway->periode_medicijnen($lidId, $mndnr, $rij['jaar'], $_POST['kzlpil'], $Karwerk);
?>
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 100>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Generatie <hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Datum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Hoeveelheid<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Eenheid<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Chargenr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Wachtdagen resterend <br> vlees &nbsp&nbsp&nbsp melk <hr></th>
<th width = 1></th>
<th width=60></th>
</tr>
<?php
                while ($row = $result->fetch_array()) {
                    $rest = $row['rest'];
                    $wdgn_v = $row['wdgn_v'];
                    if ($wdgn_v > $rest) {
                        $restdgn_v = $wdgn_v - $rest;
                    } else {
                        $restdgn_v = "geen";
                    }
                    $wdgn_m = $row['wdgn_m'];
                    if ($wdgn_m > $rest) {
                        $restdgn_m = $wdgn_m - $rest;
                    } else {
                        $restdgn_m = "geen";
                    }
                    $geslacht = $row['geslacht'];
                    if (!empty($row['ouder'])) {
                        if ($geslacht == 'ooi') {
                            $fase = 'moeder';
                        } elseif ($geslacht == 'ram') {
                            $fase = 'vader';
                        }
                    } else {
                        $fase = 'lam';
                    }
?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['werknr']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['toedm']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['totat']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['eenheid']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['charge']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $restdgn_v.' &nbsp&nbsp&nbsp&nbsp '.$restdgn_m; ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>
<?php
                }
?>
<tr style = "height : 100px;"><td colspan = 25></td></tr>
<?php
            }
        } //  Einde knop toon
?>
</table>
        </TD>
<?php
    } else {
?>
            <img src='med_rapportage_php.jpg'  width='970' height='550'/>
<?php
    }
    include "menuRapport.php";
}
?>
</body>
</html>
