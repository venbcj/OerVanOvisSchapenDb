<?php

require_once("autoload.php");

/* 3-3-2015 : Login toegevoegd */
$versie = '12-12-2015'; /* : Ubn niet te wijzigen */
$versie = '29-10-2016'; /* : Optie Administrator toegevoegd */
$versie = '9-1-2017'; /* : Link naar teamviewer toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '1-6-2020'; /* Reader keuze toegevoegd */
$versie = '12-02-2021'; /* Systeemgegevens gewijzigd naar Instellingen */
$versie = '06-10-2024'; /* Standaard tonen van groei toegevoegd en sql beveiligd met enkele quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '10-04-2025'; /* Veld root_reader uit tblLeden verwijderd. Daarmee ook variabele updlokatie en html veld txtLokatie */

Session::start();

?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>
<?php
$titel = 'Instellingen';
$file = "Systeem.php";
include "login.php";
?>
            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    $lid_gateway = new LidGateway();
    if (isset($_POST['knpSave'])) {
        if ($_POST['user']['kar_werknr'] < 1 || $_POST['user']['kar_werknr'] > 8) {
            $fout = "Het aantal karakters van een werknr moet liggen tussen 1 en 8.";
        } else {
            $data = $_POST['user'];
            $data['lidId'] = $lidId;
            $lid_gateway->update_formdetails($data);
        }
    }
    [$relnr, $urvo, $prvo, $karwerknr, $histo, $groei] = $lid_gateway->get_form($lidId);
?>
<form action = "Systeem.php" method = "post" >
<table border = 0 width = 900>
    <tr><th colspan = 6 height="50"><hr></th></tr>
<tr>
 <td width = 150><u><i>Inloggegevens :</i></u></td>
 <td width = 150 align = 'right'>Gebruikersnaam :</td><td width = 100><?php echo Session::get("U1"); ?></td>
 <td width = 100 align = "right">Wachtwoord :</td><td> ************** </td>
 <td> 
<?php echo View::link_to('Inloggegevens wijzigen', 'Wachtwoord.php', ['style' => 'color: blue']); ?>
 </td>
</tr>
<tr><td height = 15></td></tr>
<tr><td colspan = 8><hr></hr></td></tr>
</table>
<table border = 0 width = 900>
<tr>
 <td> <b><u> Standaard instellingen </u></b> </td>
</tr>
<tr>
 <td style = "font-size : 14;"  >Aantal cijfers t.b.v. werknr (max 8)</td>
 <td width = 600 ><input type = text name = "user[kar_werknr]" size = 1 value = <?php echo $karwerknr; ?>></td><td ></td>
</tr>
<tr>
 <td style = "font-size : 14;"  >Historie schaap standaard tonen</td>
 <td width = 600 >
<!-- KZLja/nee -->
<select name="user[histo]" style = "width:60; font-size:13px;">
<?php
$opties = array(1 => 'Ja', 0 => 'Nee');
foreach ($opties as $key => $waarde) {
    if ((!isset($_POST['knpSave']) && $histo == $key) || (isset($_POST['user']['histo']) && $_POST['user']['histo'] == $key)) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
    } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
    }
}
?>
</select> <!-- EINDE KZLja/nee -->
 </td>
 <td></td>
</tr>
<tr>
 <td style = "font-size : 14;"  >Groei schaap standaard tonen als </td>
 <td width = 600 >
<!-- KZLGroei -->
<select name="user[groei]" style = "width:180; font-size:13px;">
<?php
$opties = array('Totale groei', 'Gemiddelde groei per dag');
foreach ($opties as $key => $waarde) {
    if ((!isset($_POST['knpSave']) && $groei == $key) || (isset($_POST['user']['groei']) && $_POST['user']['groei'] == $key)) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
    } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
    }
}
?>
</select> <!-- EINDE KZLGroei -->
 </td>
 <td></td>
</tr>
<tr><td height = 50></td></tr>
</table>
<table border = 0 width = 900>
<tr>
 <td style = "font-size : 14;"  >Reader</td>
 <td>
          <!-- kzlReader -->
          <select name="user[reader]" style = "width:80; font-size:13px;">
<option></option>
<?php
$opties = array('Agrident' => 'Agrident', 'Biocontrol' => 'Biocontrol');
foreach ($opties as $key => $waarde) {
    if ((!isset($_POST['knpSave']) && $reader == $key) || (isset($_POST['user']['reader']) && $_POST['user']['reader'] == $key)) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
    } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
    }
}
?>
</select> <!-- EINDE kzlReader -->
 </td>
</tr>
</table>
<?php
$array_ubn = array();
$zoek_ubn = $lid_gateway->findUbns($lidId);
while ($zu = $zoek_ubn->fetch_array()) {
    $array_ubn[] = $zu['ubn'];
}
?>
<table border = 0 width = 900>
<tr>
 <td></td>
</tr>
<tr>
 <td colspan = 8><hr></hr></td></tr>
<tr>
 <td colspan = 5><u><i>Bedrijfgegevens RVO :</i></u></td></tr>
<tr>
 <td width = 210 align = 'right'>Ubn :</td><td width = 100>
<?php
$count = count($array_ubn);
for ($i = 0; $i < $count; $i++) {
    echo $array_ubn[$i] . '<br>';
}
?>
 </td>
 <td width = 160 align = "right" >Gebruikersnaam RVO :</td><td><input type = "text" name = "user[urvo]" size = 15 value = <?php echo $urvo;
?> ></td>
</tr>
<tr>
<td width = 210 align = 'right'>Relatienummer RVO :</td><td width = 100><input type = text name = "user[relnr]" size = 15 value = <?php echo $relnr;
?>></td>
 <td width = 160 align = "right">Wachtwoord RVO :</td><td><input type = password name = "user[prvo]" size = 15 value = <?php echo $prvo; ?> ></td>
 <td>
<?php echo View::link_to('Ubn toevoegen', 'Ubn_toevoegen.php', ['style' => 'color: blue']); ?>
</td>
</tr>
<tr>
 <td height = 15></td>
</tr>
</table>
<table border = 0 width = 900>
<tr>
 <td colspan = 8><hr></hr></td></tr>
<tr height = 50 >
 <td>
<?php $host = $_SERVER['HTTP_HOST'];
if ($host == 'demo.oervanovis.nl' && $lidId == 1) {
    echo View::link_to('Database legen', 'demo_database_legen.php', ['style' => 'color: blue']);
}
?>
 </td>
</tr>
<tr>
 <td colspan = 4 align =left> Hulp op afstand ? Klik <a href='https://download.teamviewer.com/download/TeamViewerQS_nl.exe' target="_blank" style = 'color : blue' > hier </a>
 </td>
 <td colspan = 4 align =right><input type = "submit" name = "knpSave" value = "Opslaan"></td>
</tr>
</table>
</form>
</TD>
<?php
include "menuBeheer.php";
}
?>
</tr>
</table>
</body>
</html>
