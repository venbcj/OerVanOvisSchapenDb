<?php

require_once("autoload.php");

$versie = '28-03-2026'; /* gemaakt */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>
<?php
$titel = 'Controle levensnummers uit reader';
$file = "AlertLevnrSelectie.php";
include "login.php";
?>
        <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        if (empty($schaap_gateway)) {
            $schaap_gateway = new SchaapGateway();
        }
        include "kalender.php";

        if (isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) {
            $datumvan = $_POST['txtDatumVan_'];
            $van = date_format(date_create($datumvan), 'Y-m-d');
            $datumtot = $_POST['txtDatumTot_'];
            $tot = date_format(date_create($datumtot), 'Y-m-d');
            $toon_spooknummers = $agrident_gateway->getSpookLevensnummer($lidId, $van, $tot);


            if (isset($_POST['knpStuur_'])) {
            $alert_gateway = new AlertGateway();
			$old_volgnr = $alert_gateway->laatste_selectie($lidId);

				if (!isset($old_volgnr)) {
               	 $volgnr = 1;
            	} else {
               	 $volgnr = $old_volgnr + 1;
            	}

            $agrident_gateway = new ImpAgridentGateway();
            $zoek_transponder = $agrident_gateway->getSpookTransponder($lidId, $flddagvan, $flddagtot);
            while ($zt = $zoek_transponder->fetch_assoc()) {
                $transponder = $zt['transponder'] . $zt['levensnummer'];
                $alert_gateway->insert($volgnr, $lidId, $transponder, 7);
            	}
        	}
        }
?>
<form action= "AlertLevnrSelectie.php" method="post">
<table border = 0> 
<tr>
<td align="right"><i>Reader geleegd vanaf &nbsp</i></td>
 <td align="left"><i>&nbsp&nbsp&nbsp tot en met</i></td>
</tr>
<tr>
<td align="right"><input id = "datepicker1" type= text name = "txtDatumVan_" size = "8" value = <?php if (isset($datumvan)) {
echo "$datumvan";
        }
?> ></td>
    <td align="left"><input id = "datepicker2" type= text name = "txtDatumTot_" size = "8" value = <?php if (isset($datumtot)) {
    echo "$datumtot";
        }
?> >
  <input type="submit" name="knpZoek_" value="Zoek">
 </td>
</tr>
<tr><td colspan = 10 ><hr></td></tr>

<tr><td colspan = 50><table border = 0>
<?php
        if (isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) {
?>
<tr>
    <td colspan = 5>Dit zijn de levensnummers binnen de gekozen<br> periode uit de reader die niet voorkomen<br> in het management programma. <br>Klik op de knop 'Verstuur' om deze <br>levensnummers klaar te zetten om <br>naar de reader te sturen ter controle.<br> </td>
</tr>
<?php
        }
?>
<tr height = 75 align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td width = 80 align="center"><b> Levensnummer </b><hr></td>
 <td width = 80 align="center"><b> Datum van <br> legen reader </b><hr></td>
 <td ><b> Taak </b><hr></td>
<?php
        if (isset($_POST['knpZoek_']) || isset($_POST['knpStuur_'])) {
?>
 <td valign="top"> <input type="submit" name="knpStuur_" value="Verstuur"> <br><br><hr></td>
 <td></td>
</tr>
<?php
            while ($ts = $toon_spooknummers->fetch_assoc()) {
                $transp = $ts['transponder'];
				$levnr = $ts['levensnummer'];
				$datum = $ts['datum'];
				$taak = $ts['taak'];
?>
<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td> <?php echo $levnr; ?> </td>
 <td> <?php echo $datum; ?> </td>
 <td> <?php echo $taak; ?> </td>
 <td> <?php if(!isset($transp)) { echo 'Transponder is onbekend! '; } ?> </td>
</tr>
<tr> <td colspan = 4 ><hr></td>
</tr>
<?php
            }
        }
?>
</table>        
  
</td></tr></table>
</form>
</TD>
<?php
    } else {
?>
        <img src="ooikaart_php.jpg"  width='970' height='550'/>
<?php
    }
    include "menuAlerts.php";
}
?>
</tr>
</table>
</body>
</html>
