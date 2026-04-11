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
$titel = 'Schapen per verblijf';
$file = "AlertHoknrSelectie.php";
include "login.php";
?>
        <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        if (empty($schaap_gateway)) {
            $schaap_gateway = new SchaapGateway();
        }

            if (isset($_POST['knpStuur_']) && !empty($_POST['kzlHok'])) {

            $hokId = $_POST['kzlHok'];

            $alert_gateway = new AlertGateway();
			$old_volgnr = $alert_gateway->laatste_selectie($lidId);

				if (empty($old_volgnr)) {
               	 $volgnr = 1;
            	} else {
               	 $volgnr = $old_volgnr + 1;
            	}

            $bezet_gateway = new BezetGateway();
            $zoek_transponder = $bezet_gateway->zoek_nu_in_hok_met_transponder($lidId, $hokId);
            while ($zt = $zoek_transponder->fetch_assoc()) {
                $transponder = $zt['transponder'] . $zt['levensnummer'];
                $alert_gateway->insert($volgnr, $lidId, $transponder, 8);
            	}
        	}
        
?>
<form action= "AlertHoknrSelectie.php" method="post">
<table border = 0> 
<tr>
<td><i>Verblijf</i></td>
</tr>
<?php 
$bezet_gateway = new BezetGateway();

[$hoknId, $hoknum] = $bezet_gateway->hokken_nu_bezet($lidId); ?>
<tr>
 <td>
<!-- KZLVERBLIJF KEUZE-->
 <select style="width:100;" name= 'kzlHok' value = "">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((isset($_POST['kzlHok']) && $_POST['kzlHok'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
    echo '<option value="' . $key . '" >' . $waarde . '</option>';
  }
            }
}
?> </select>

 <!-- EINDE KZLVERBLIJF KEUZE -->
 </td>
 <td>
  <input type="submit" name="knpToon_" value="Toon">
 </td>
</tr>
<tr><td colspan = 10 ><hr></td></tr>

<tr><td colspan = 50><table border = 0>
<?php
        if ((isset($_POST['knpToon_']) || isset($_POST['knpStuur_'])) && !empty($_POST['kzlHok']) ) {

$hokId = $_POST['kzlHok'];

$aantal_nu_in_hok = $bezet_gateway->aantal_nu_in_hok($lidId, $hokId);
$zoek_nu_in_hok = $bezet_gateway->zoek_nu_in_hok($lidId, $hokId); 

$anih = $aantal_nu_in_hok->fetch_assoc();
	$aantal = $anih['aant'];
?>

<tr>
    <td colspan = 5>Dit zijn de levensnummers uit het gekozen verblijf. <br>Klik op de knop 'Verstuur' om deze levensnummers <br> klaar te zetten om naar de reader te sturen.<br> </td>
</tr>

<tr height = 75 align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td align="center"><br> <b> <?php echo $aantal.' Levensnummers'; ?> </b><hr></td>

<?php
        if (isset($_POST['knpToon_']) || isset($_POST['knpStuur_'])) {
?>
 <td valign="top"> <input type="submit" name="knpStuur_" value="Verstuur"> <br><br></td>
 <td></td>
</tr>

<?php
            while ($znih = $zoek_nu_in_hok->fetch_assoc()) {
                $transp = $znih['transponder'];
				$levnr = $znih['levensnummer'];
?>
<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td> <?php echo $levnr; ?> </td>
 <td> <?php if(empty($transp)) { echo 'Transponder is onbekend! '; } ?> </td>
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
