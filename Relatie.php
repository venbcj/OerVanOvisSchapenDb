<?php

require_once("autoload.php");
$versie = '28-12-2016'; /* Banknr gewijzigd naar IBAN, veld langer gemaakt en tonen van spaties mogelijk gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>
<?php
$titel = 'Relatie';
$file = "Relaties.php";
include "login.php";
?>
                <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    $partij_gateway = new PartijGateway();
    if (empty($_GET['pstid'])) {
        $pId = "$_POST[txtpId_]";
    } else {
        $pId = "$_GET[pstid]";
    }
    if (isset($_POST['knpSave_'])) {
        // TODO: FIXME: #0004139 ja grappenmaker, tblPartij.naam is verplicht
        $partij = [
            'ubn' => $_POST['txtUbn_'] ?? null,
            'naam' => $_POST['txtNaam_'] ?? null,
            'tel' => $_POST['txtTel_'] ?? null,
            'fax' => $_POST['txtFax_'] ?? null,
            'email' => $_POST['txtEmail_'] ?? null,
            'site' => $_POST['txtSite_'] ?? null,
            'banknr' => $_POST['txtBanknr_'] ?? null,
            'relnr' => $_POST['txtRelnr_'] ?? null,
            'wachtw' => $_POST['txtWawo_'] ?? null,
            'partId' => $pId,
        ];
        $partij_gateway->update($partij);
        $vervId = $partij_gateway->zoek_vervoer($pId);
        // Invoer vervoer als deze nog niet bestaat
        if (!isset($vervId) && ( !empty($_POST['txtKent_']) || !empty($_POST['txtHang_']) )) {
            $partij_gateway->insert_vervoer($pId, $_POST['txtKent_'], $_POST['txtHang_']);
        } elseif (isset($vervId)) {
            $partij_gateway->wijzig_vervoer($pId, $_POST['txtKent_'] ?? null, $_POST['txtHang_'] ?? null);
        }
        include "save_relatie.php";
    }
    $Partij = $partij_gateway->find($pId);
    while ($row = $Partij->fetch_assoc()) {
        $pId = $row['partId'];
        $ubn = $row['ubn'];
        $relnr = $row['relnr'];
        $wawo = $row['wachtw'];
        $naam = $row['naam'];
           /*$straat = $row['straat']; $nr = $row['nr'];
           $pc = $row['pc'];
           $plaats = $row['plaats'];*/
        $tel = $row['tel'];
        $fax = $row['fax'];
        $mail = $row['email'];
        $site = $row['site'];
        $banknr = $row['banknr'];
        $kent = $row['kenteken'];
        $hang = $row['aanhanger'];
    }
?>
<form action= "Relatie.php" method = "post">
<table border = 0  align = "left" >
<tr valign = "bottom">
 <td>ubn</td>
 <td colspan = 4 ><input type= "hidden" name= "txtpId_" size = 1 value = <?php
    echo $pId;
?> > <!--hiddden-->
      <input type= "text" name= "txtUbn_" size = 6 value = <?php echo $ubn; ?> ></td>
 <td></td>
</tr>
<tr>
 <td> Naam
 </td>
 <td colspan = 4 > <input type= "text" name= "txtNaam_" size = 60 value = <?php
    if (isset($naam)) {
        echo "'" . $naam . "'";
    }
?> >
 </td>
</tr>
<tr>
 <td>IBAN</td>
 <td colspan = 4 > <input type= "text" name= "txtBanknr_" size = 30 value = <?php
    echo "'" . $banknr . "'";
?> > </td>
</tr>
<tr height = 35 valign = bottom>
 <td>Relatienr</td>
 <td> <input type= "text" name= "txtRelnr_" size = 10 value = <?php
    echo $relnr;
?> > </td>
 <td width = 50></td>
 <td  align = right >Kenteken &nbsp </td>
 <td> <input type= "text" name= "txtKent_" size = 12 value = <?php
    echo $kent;
?> > </td>
</tr>
<tr>
 <td>Wachtwoord</td>
 <td> <input type= "text" name= "txtWawo_" size = 10 value = <?php
    echo $wawo;
?> > </td>
 <td></td>
 <td align = right >Aanhanger &nbsp </td>
 <td> <input type= "text" name= "txtHang_" size = 12 value = <?php
    echo $hang;
?> > </td>
</tr>
</table>
<table border = 0>
<tr>
 <td colspan = 2> <i>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Contactgegevens </i> </td>
</tr>
<tr>
 <td width = 100 align = right > telefoon &nbsp </td>
 <td><input type= "text" name= "txtTel_" size = 9 value = <?php
    if (isset($tel)) {
        echo $tel;
    }
?> > </td>
</tr>
<tr>
 <td align = right > fax &nbsp </td>
 <td> <input type= "text" name= "txtFax_" size = 9 value = <?php
    echo $fax;
?> > </td>
</tr>
<tr>
 <td align = right > email &nbsp </td>
 <td> <input type= "text" name= "txtEmail_" size = 30 value = <?php
    echo $mail;
?> > </td>
</tr>
<tr>
 <td align = right > site &nbsp </td>
 <td> <input type= "text" name= "txtSite_" size = 30 value = <?php
    echo $site;
?> > </td>
</tr>
</table>
<br>
<br>
<br>
<table border = 0 >
<tr>
 <td><i> relatie </i></td>
 <td><i> straat </i></td>
 <td><i> nr </i></td>
 <td><i> Postcode </i></td>
 <td><i> Plaats </i></td>
 <td><i> actief </i></td>
</tr>
<?php
    $Relatie = $partij_gateway->Relatie($pId);
    while ($row = $Relatie->fetch_assoc()) {
        $rId = $row['relId'];
        $rela = $row['relatie'];
        if ($rela == 'deb') {
            $relatie = 'debiteur';
        } elseif ($rela == 'cred') {
            $relatie = 'crediteur';
        }
        $naam = $row['naam'];
        $straat = $row['straat'];
        $nr = $row['nr'];
        $pc = $row['pc'];
        $plaats = $row['plaats'];
        $r_actief = $row['actief'];
?>
   <tr>
    <td> <input type= "hidden" name= <?php echo "txtrId_$rId"; ?> size = 1 value = <?php echo $rId; ?> > <!--hiddden-->
<?php if (isset($relatie)) {
echo $relatie;
        } ?> </td>
            <td> <input type= "text" name= <?php echo "txtStraat_$rId"; ?> size = 30 value = <?php if (isset($straat)) {
            echo "'" . $straat . "'";
} ?> >  </td>
    <td> <input type= "text" name= <?php echo "txtNr_$rId"; ?>     size = 1  value = <?php if (isset($nr)) {
    echo "'" . $nr . "'";
            } ?> >         </td>
                <td> <input type= "text" name= <?php echo "txtPc_$rId"; ?> style = "text-align : left;"    size = 6  value = <?php if (isset($pc)) {
                echo "'" . $pc . "'";
    } ?> >         </td>
        <td> <input type= "text" name= <?php echo "txtPlaats_$rId"; ?> size = 25 value = <?php if (isset($plaats)) {
        echo "'" . $plaats . "'";
                } ?> >  </td>
                    <td> <input type= "checkbox" name= <?php echo "chkActief_$rId"; ?> value = 1 <?php if ($r_actief == 1) {
                    ?> checked <?php
        } ?> >  </td>
    <td width = 200>  </td>
   </tr>
<?php }
?>
<tr>
  <td colspan =11 align = right> <input type= "submit" name= "knpSave_" value = "Opslaan" > </td>
</tr>
</table>
</form>
<!-- Thomas bij de Belvederre station-->
</TD>
<?php
include "menuBeheer.php";
}
?>
</tr>
</table>
</body>
</html>
