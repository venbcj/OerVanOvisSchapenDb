<?php

require_once("autoload.php");


$versie = '10-6-2017'; /* Gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '15-3-2020'; /* veld reader toegevoegd */
$versie = '20-6-2020'; /* knop bewerken toegevoegd als reader = Agrident en bepaalde redenen en Lambar bestaan niet of redenen niet actief */
$versie = '12-2-2021'; /* Redenen afvoer toegevoegd. Controle lambar verwijderd */
$versie = '11-8-2023'; /* Veld ingescand toegevoed. Dit is de laatste dag dat een stallijst kan worden ingelezen bij een nieuwe klant. functie db_null_input() gebruikt. Sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
 Session::start();
 
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Gebruiker';
$file = "Systeem.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $lid_gateway = new LidGateway();

    if(isset($_GET['pstId']))    {
        Session::set("ID", $_GET['pstId']); 
    }
    $ID = Session::get("ID");
    include "validate-gebruiker.js.php";

 // $ID is de gebruiker die op de pagina is opgeroepen
 // $lidId is de gebruiker die is ingelogd
if (isset($_POST['knpSave'])) {
    $data = $_POST;
    $data['lstScanDay'] =  date_format(date_create($_POST['txtIngescand']), 'Y-m-d');
    if (empty($_POST['txtIngescand'])) {
        $data['lstScanDay'] = $lid_gateway->zoek_ingescand($ID);
    }
    $lid_gateway->update_details($ID, $data);
}

if (isset ($_POST['knpUpdate'])) {
    $lidid = $ID;
    include "newreader_keuzelijsten.php";
}

$row = $lid_gateway->get_data($ID);
?>

<form action = "Gebruiker.php" method = "post" >

<table border = 0 width = 900>
<tr height = 20><td></td></tr>
<tr><th colspan = 3><hr>Gebruiker gegevens<hr></th></tr>
</table>

<table border = 0 width = 900>
<tr>
 <td colspan = 15><u><i>Gebruiker :</i></u></td>
</tr>
<tr>
  <td colspan = 15>
    Roepnaam : <input type="text" name="txtRoep" id="voornaam" size="10" value="<?php echo $row['roep']; ?>">
    Tussenvoegsel : <input type="text" name="txtVoeg" id="tussen" size="3" value="<?php echo $row['voegsel']; ?>">
    &nbsp&nbsp
    Achternaam : <input type="text" name="txtNaam" id="achternaam" size="27" value="<?php echo $row['naam']; ?>">
  </td>
</tr>
<tr>
  <td colspan = 15 >
    Telefoonnr : <input type="text" name="txtTel" id="telefoon" size="10" value="<?php echo $row['tel']; ?>">
    &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
    E-mail : <input type="text" name="txtMail" size=50 value="<?php echo $row['mail']; ?>">
  </td>
</tr>
<tr><td height = 15></td>
</table>
<table border = 0 width = 900>
<tr>
 <td colspan = 15><u><i>Bedrijfgegevens RVO :</i></u></td>
</tr>
<tr>
 <td width = 150 align = 'right'>Ubn :</td>
 <td width = 100> <?php echo $row['ubn']; ?> </td>
 <td width = 100 align = "right" >Gebruikersnaam RVO :</td>
 <td colspan = 2 ><input type = "text" name = "txtUrvo" size = 10 value = <?php echo $row['urvo']; ?> ></td>
</tr>
<tr>
 <td width = 150>Relatienummer RVO :</td>
 <td><input type = text name = "txtRelnr" id="relatienummer" size = 10 value = <?php echo $row['relnr']; ?>></td>
 <td width = 160 align = "right">Wachtwoord RVO :</td>
 <td colspan = 2 ><input type = password name = "txtPrvo" size = 10 value = <?php echo $row['prvo']; ?> ></td>

</tr>
<tr><td height = 20></td>
</tr>
<tr>
 <td colspan=>Reader :      

     <!-- kzlReader --> 
<select name="kzlReader" style = "width:80; font-size:13px;">
<option></option>
<?php
$opties = array('Agrident' => 'Agrident', 'Biocontrol' => 'Biocontrol');
foreach ( $opties as $key => $waarde)
{
    $selected = '';
    if((!isset($_POST['knpSave']) && $row['reader'] == $key) || (isset($_POST["kzlReader"]) && $_POST["kzlReader"] == $key) ) {
        $selected = ' selected';
    }
    echo '<option value="' . $key . '"'.$selected.'>' . $waarde . '</option>';
} ?> 
</select> <!-- EINDE kzlReader -->

 </td>
 <?php // knpUpdate hoeft alleen te worden getoond als er iets valt bij te werken bij reader Agrident 
if ($row['reader'] == 'Agrident') {
    $rd_db = $lid_gateway->zoek_redenen_uitval($ID);
    $rd_db += $lid_gateway->zoek_redenen_afvoer($ID);
?>
 <td>
     <?php if($rd_db < 14 ) { ?>
     <input type = "submit" name ="knpUpdate" value="Bijwerken">
 </td>
 <?php }
 } ?>
</tr>
<tr>
 <td colspan="4">Reader wachtwoord : <?php echo $row['readerkey']; ?> </td>
</tr>
</table>

<table border = 0 width = 900>
<tr height = 15><td></td></tr>
<tr><th><hr> Module<hr></th><th colspan = 3 align="left"><hr>&nbsp&nbsp<hr></th></tr>
<tr>
 <td width = 105 >Melden : </td>
 <td> <?php View::janee('radMeld', $_POST['radMeld'] ?? $row['meld']); ?> </td>
</tr>
<tr>
 <td width = 105 >Technisch : </td>
 <td> <?php View::janee('radTech', $_POST['radTech'] ?? $row['tech']); ?> </td>
</tr>
<tr>
 <td width = 105 >Financieel : </td>
 <td> <?php View::janee('radFin', $_POST['radFin'] ?? $row['fin']); ?> </td>
</tr>
<tr>
 <td height="15">
 </td>
</tr>
<tr>
 <td width = 105 >Administrator : </td>
 <td>
     <!-- kzlBeheer ja/nee --> 
<select name="kzlAdm" style="width:60; font-size:13px;">
<?php
$opties = array(1 => 'Ja', 0 => 'Nee');
foreach ( $opties as $key => $waarde)
{
    if((!isset($_POST['knpSave']) && $row['beheer'] == $key) || (isset($_POST["kzlAdm"]) && $_POST["kzlAdm"] == $key) ) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
    } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
    }
} ?> 
</select> <!-- EINDE kzlBeheer ja/nee -->
 </td>
</tr>
<tr> <td colspan="4"> <hr></td></tr>
</table>

<table border = 0 width = 900>
<tr>
 <td width = 105 >
    Laatste dag stallijst inlezen
 </td>
 <td >
    <input type="text" name="txtIngescand" size="8" value="<?php echo $row['ingescand']; ?>">
 </td>
 <td> t.b.v. nieuwe klanten
 </td>
 <td width = 500 ></td>
</tr>
<tr height = 50 ></tr>
<tr>
 <td colspan = 4 align =right><input type = "submit" name = "knpSave" onfocus = "verplicht()" value = "Opslaan"></td>
</tr>
</table>
</form>

</TD>
<?php
    include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
