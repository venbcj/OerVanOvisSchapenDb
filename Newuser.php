<?php

require_once("autoload.php");

/* 3-3-2015 : Login toegevoegd */
$versie = '12-12-2015'; /* : Ubn niet te wijzigen */
$versie = '29-10-2016'; /* : Optie Administrator toegevoegd */
$versie = '09-01-2017'; /* : Link naar teamviewer toegevoegd */
$versie = '23-01-2019'; /* aanmaken persoonlijke map toegevoegd */
$versie = '12-08-2023'; /* veld ingescand toegevoegd en functie db_null_input() gebruikt. Sql beveiligd met quotes */
$versie = '16-10-2023'; /* Aanmaken map /Readerversies toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '29-08-2025'; /* Ubn wordt vanaf nu opgeslagen in tblUbn i.p.v. tblLeden. Tevens de keuze Biocontrol in het veld Reader verwijderd */

Session::start();

require_once("newuser_functions.php");

?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Nieuwe gebruiker';
$file = "Systeem.php";
include "login.php";
?>
            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    $lid_gateway = new LidGateway();
    $ubn_gateway = new UbnGateway();
    include "validate-newuser.js.php";
    $ingescand = date_add_months($today,1); //zie basisfuncties.php
    if (isset($_POST['knpSave'])) {
        $userForm = $_POST['user'];
        if ($ubn_gateway->exists($userForm['ubn'])) {
            $fout = "Dit ubn bestaat al.";
        } else {
            $userForm['login'] = $userForm['ubn'];
            $userForm['alias'] = getAlias($db, substr($userForm['naam'], 0, 4) . substr($userForm['roep'], 0, 1) ,0);
            $userForm['passw'] = md5($userForm['ubn'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
            $userForm['readerkey'] = getApiKey($db);
            $userForm['kar_werknr'] = 5;
            $userForm['actief'] = 1;
            $userForm['ingescand'] = $ingescand;
            $userForm['beheer'] = 0;
            $userForm['histo'] = 1;
            $lid_gateway->save_new($userForm);
            # alias is nu de primaire sleutel; uniek door de aanpak in "getAlias" (die we "createAlias" gaan noemen).
            # Rest van de db gebruikt lidId, dus die halen we even op.
            # Na verbouwing kan een insert-query direct het aangemaakte nummer teruggeven --BCB
            $newId = $lid_gateway->findIdByAlias($userForm['alias']);
            $ubn_gateway->insert($newId, $userForm['ubn']);
            # voormalige include "newuser_data":
/* 11-6-2020 Standaard Lambar toegevoegd bij nieuwe users
8-4-2023 naamreader Rendac standaard vullen. Relatie Vermist standaard toevoegen en SQL beveiligd met quotes
21-02-2025 Invoer Rendac in tblRelatie uitval = 1 gesplitst van Invoer Vermist in tblRelatie i.v.m. uitval = 0 */
            $lid_gateway->createLambar($newId);
            $lid_gateway->createMoments($newId);
            $lid_gateway->createEenheden($newId);
            $lid_gateway->createElementen($newId);
            $lid_gateway->createPartij($newId);
            $lid_gateway->createRelatie($newId);
            $lid_gateway->createRubriek($newId);
            $lidid = $newId;
            include "newreader_keuzelijsten.php";
            $map = 'user_'.$newId;
            if (file_exists($map)) {
                throw new Exception("Map voor lid id=$newId is al aanwezig");
            }
            mkdir("$map"); // Persoonlijk map voor user maken
            $map = 'user_'.$newId.'/Readerbestanden';
            mkdir("$map"); // Persoonlijk map voor user maken t.b.v. readerbestanden erroruit de reader komen als de reader wordt uitgelezen
            $map = 'user_'.$newId.'/Readerversies';
            mkdir("$map"); // Persoonlijk map voor user maken t.b.v. readerversies
            $persoonlijke_map = $dir.'/user_'.$lidId;
            $goed = "De gebruiker is ingevoerd.";
        } // Einde als $gevonden_ubn niet bestaat

    } // Einde If (isset ($_POST['knpSave']))
?>

<form action = "Newuser.php" method = "post" >

<table border = 0 width = 900>
<tr height = 20><td></td></tr>
<tr><th colspan = 3><hr>Gebruiker gegevens<hr></th></tr>
</table>


<table border = 0 width = 900>


<tr>
 <td colspan = 15><u><i>Gebruiker :</i></u></td>
</tr>
<tr>
 <td colspan=15>Roepnaam* : <input type="text" name="user[roep]" id="voornaam" size=10  >
     Tussenvoegsel : <input type="text" name="user[voegsel]" size=3 >
     &nbsp&nbsp Achternaam* : <input type="text" name="user[naam]" id="achternaam" size=27 ></td>
</tr>
<tr>
 <td colspan=15 >Telefoonnr &nbsp&nbsp: <input type="text" name="user[tel]" id="telefoon" size=10 >
      &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbspE-mail : <input type="text" name="user[mail]" size=50 ></td>
</tr>
<tr><td height=15></td>
</table>
<table border=0 width=900>
<tr>
 <td colspan=15><u><i>Bedrijfgegevens RVO :</i></u></td>
</tr>
<tr>
 <td width=150 align='right'>Ubn* :</td>
 <td width=100><input type="text" name="user[ubn]" id="ubn" size=10 > </td>
 <td width=100 align="right" >Gebruikersnaam RVO :</td>
 <td colspan=2 ><input type="text" name="user[urvo]" size=10 > </td>
</tr>
<tr>
 <td width=150>Relatienummer RVO :</td>
 <td><input type=text name="user[relnr]" id="relatienummer" size=10 > </td>
 <td width=160 align="right">Wachtwoord RVO :</td>
 <td colspan=2 ><input type=password name="user[prvo]" size=10 > </td>

</tr>
<tr><td height=25></td></tr>
</table>

<table border=0 width=900>
<tr>
 <td width=105> Reader
 </td>
 <td>
<!-- kzlReader -->
<select name="user[reader]" style = "width:80; font-size:13px;">
<option></option>
<?php
    $opties = array('Agrident' => 'Agrident');
    foreach ( $opties as $key => $waarde)
    {
        if((isset($_POST['user']["reader"]) && $_POST['user']["reader"] == $key) ) {
            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
        } else {
            echo '<option value="' . $key . '">' . $waarde . '</option>';
        }
    } ?>
</select>
<!-- EINDE kzlReader -->
 </td>
</tr>
</table>

<table border=0 width=900>
<tr height=20><td></td></tr>
<tr><th><hr>&nbspModule<hr></th><th colspan=3 align="left"><hr>&nbsp&nbsp<hr></th></tr>
<tr>
 <td width=105 >Melden : </td>
 <td><input type=radio name='user[meld]' value=1 <?php if(isset($_POST['user']['meld']) && $_POST['user']['meld'] == 1) { echo "checked"; } ?>
      > Ja
      <input type=radio name='user[meld]' value=0 <?php if(isset($_POST['user']['meld']) && $_POST['user']['meld'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
      > Nee
 </td>
</tr>
<tr>
 <td width=105 >Technisch : </td>
 <td><input type=radio name='user[tech]' value=1 <?php if(isset($_POST['user']['tech']) && $_POST['user']['tech'] == 1) { echo "checked"; } ?>
      > Ja
      <input type=radio name='user[tech]' value=0 <?php if(isset($_POST['user']['tech']) && $_POST['user']['tech'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
      > Nee
 </td>
</tr>
<tr>
 <td width=105 >Financieel : </td>
 <td><input type=radio name='user[fin]' value=1 <?php if(isset($_POST['user']['fin']) && $_POST['user']['fin'] == 1) { echo "checked"; } ?>
      > Ja
      <input type=radio name='user[fin]' value=0 <?php if(isset($_POST['user']['fin']) && $_POST['user']['fin'] == 0) { echo "checked"; } else if(!isset($_POST['knpSave']) ) { echo "checked"; } ?>
      > Nee
 </td>
</tr>
</table>

<table border=0 width=900>
<tr><td colspan=8><hr></hr></td></tr>
<tr height=50 ></tr>
<tr>
 <td colspan=4 align =right><input type="submit" name="knpSave" onfocus="verplicht()" value="Opslaan"></td>
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
