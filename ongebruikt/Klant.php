<?php 

exit("Dit kan niet werken. mvrgr, BCB");

require_once("autoload.php");

$versie = '14-8-2014'; /*Menu (rechts) veranderd van menuInkoop naar menuBeheer en html buiten php geprogrammeerd */
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
Session::start();
 ?>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<center>
<?php
$titel = 'Debiteur';
$subtitel = '';
include "header.tpl.php"; ?>
    <TD width = 960 height = 400 valign = "top">
<?php
$file = "Klanten.php";
include "login.php"; 
if (Auth::is_logged_in()) { 

if (empty($_GET['pstid']))
{    $klantid = "$_POST[txtpstid]";    }
else
{    $klantid = "$_GET[pstid]";    }

If (isset ($_POST['knpUpdate']))
{
    /*if (empty($_POST['txtubn']))
    {echo "De knop werkt";}
    else
    {*/
    
        if (empty($_POST['txtubn']))    {    $updubn = "NULL";    }
  else        {    $updubn = " '$_POST[txtubn]' ";    }
        if (empty($_POST['txtrelnr']))    {    $updrelnr = "NULL";    }
  else        {    $updrelnr = " '$_POST[txtrelnr]' ";    }
        if (empty($_POST['txtpassw']))    {    $updpassw = "NULL";    }
  else        {    $updpassw = " '$_POST[txtpassw]' ";    }
        if (empty($_POST['txtnaam']))    {    $updnaam = "NULL";    }
  else        {    $updnaam = " '$_POST[txtnaam]' ";    }
  
        if (empty($_POST['txtstraat']))    {    $updstraat = "NULL";    }
  else        {    $updstraat = " '$_POST[txtstraat]' ";    }
    
        if (empty($_POST['txtnr']))        {    $updnr = "nr = NULL";    }
  else        {    $updnr = "nr = '$_POST[txtnr]' ";    }

        if (empty($_POST['txtpc']))        {    $updpc = "pc = NULL";    }
  else        {    $updpc = "pc = '$_POST[txtpc]' ";    }

        if (empty($_POST['txtplaats']))    {    $updplaats = "plaats = NULL";    }
  else        {    $updplaats = "plaats = '$_POST[txtplaats]' ";    }

        if (empty($_POST['txtstraat1']))    {    $upd_straat = "NULL";    }
  else        {    $upd_straat = " '$_POST[txtstraat1]' ";    }
    
        if (empty($_POST['txtnr1']))        {    $upd_nr = "nr1 = NULL";    }
  else        {    $upd_nr = "nr1 = '$_POST[txtnr1]' ";    }

        if (empty($_POST['txtpc1']))        {    $upd_pc = "pc1 = NULL";    }
  else        {    $upd_pc = "pc1 = '$_POST[txtpc1]' ";    }

        if (empty($_POST['txtplaats1']))    {    $upd_plaats = "plaats1 = NULL";    }
  else        {    $upd_plaats = "plaats1 = '$_POST[txtplaats1]' ";    }

        if (empty($_POST['txttel']))    {    $updtel = "tel = NULL";    }
  else        {    $updtel = "tel = '$_POST[txttel]' ";        }

        if (empty($_POST['txtfax']))    {    $updfax = "fax = NULL";    }
  else        {    $updfax = "fax = '$_POST[txtfax]' ";        }
  
        if (empty($_POST['txtmail']))    {    $updmail = "email = NULL";    }
  else        {    $updmail = "email = '$_POST[txtmail]' ";    }

        if (empty($_POST['txtsite']))    {    $updsite = "site = NULL";    }
  else        {    $updsite = "site = '$_POST[txtsite]' ";        }

        if (empty($_POST['txtbank']))    {    $updbank = "banknr = NULL";    }
  else        {    $updbank = "banknr = '$_POST[txtbank]' ";    }

if (empty($_POST['chkActief']))    {    $updact = "actief = NULL";    }
  else        {    $updact = "actief = '$_POST[chkActief]' ";    }

        if (empty($_POST['txtkent']))    {    $updkent = "kenteken = NULL";    }
  else        {    $updkent = "kenteken = '$_POST[txtkent]' ";        }

        if (empty($_POST['txthang']))    {    $updhang = "aanhanger = NULL";    }
  else        {    $updhang = "aanhanger = '$_POST[txthang]' ";    }

        $relatie_gateway = new RelatieGateway();
        $relatie_gateway->update_klant();
        public function update_klant() {
            $this->run_query(
               <<<SQL
UPDATE tblRelaties SET ubn = ".$updubn.",
 relnr = ".$updrelnr.",
 wachtw = ".$updpassw.",
 relatie = ".$updnaam." ,
 adres = ".$updstraat." ,
 ".$updnr." ,
 ".$updpc." ,
 ".$updplaats." ,
 adres1 = ".$upd_straat." ,
 ".$upd_nr." ,
 ".$upd_pc." ,
 ".$upd_plaats." ,
 ".$updtel.",
 ".$updfax.",
 ".$updmail.",
 ".$updsite.",
 ".$updbank.",
 ".$updact.",
 ".$updkent.",
 ".$updhang."
WHERE lidId = ".mysqli_real_escape_string($db, $lidId)."
 and relatId = '$klantid'     
SQL
 ,
     [
     ]
            );
        }
        mysqli_query($db,$wijzigklant) or die (mysqli_error($db));

    //}
}

//echo "$id";

$klant = mysqli_query($db,"SELECT relatId, dc, ubn, relnr, wachtw, relatie, adres, nr, pc, plaats, adres1, nr1, pc1, plaats1, tel, fax, email, site, banknr, groep, actief, kenteken, aanhanger FROM tblRelaties WHERE relatid = '$klantid' ") or die (mysqli_error($db));
 while ($row = mysqli_fetch_assoc($klant))
    {    $ubn = "{$row['ubn']}";
        $relnr = "{$row['relnr']}";
        $passw = "{$row['wachtw']}";
        $relatie = "{$row['relatie']}";
        $straat = "{$row['adres']}"; $nr = "{$row['nr']}";
        $pc = "{$row['pc']}";
        $plaats = "{$row['plaats']}";
        $straat1 = "{$row['adres1']}"; $nr1 = "{$row['nr1']}";
        $pc1 = "{$row['pc1']}";
        $plaats1 = "{$row['plaats1']}";
        $tel = "{$row['tel']}";
        $fax = "{$row['fax']}";
        $email = "{$row['email']}";
        $site = "{$row['site']}";
        $bank = "{$row['banknr']}";
        $actif = "{$row['actief']}";
        $kent = "{$row['kenteken']}";
        $hang = "{$row['aanhanger']}";
    
    
?> <table border = 0 > <tr><td style='font-size : 25px' align = center colspan = 4 height = 25> <?php echo "$relatie"?></td></tr>
<tr><td width = 75></td><td>
<form action= "Klant.php" method = "post">
<table border = 0 >
<tr><td> <input type= "hidden" name= "txtpstid" value = <?php echo $klantid; ?> >
UBN </td>
<td>: ubn<input type= "text" name= "txtubn" value = <?php echo $ubn; ?> style = "width : 124px">
</td></tr><tr><td>
Relatienummer </td>
<td>: <input type= "text" name= "txtrelnr" value = <?php echo $relnr; ?> >
</td></tr><tr><td>
Wachtwoord </td>
<td>: <input type= "text" name= "txtpassw" value = <?php echo $passw; ?> >
</td></tr><tr><td>
Bedrijfsnaam </td>
<td>: <input type= "text" name= "txtnaam" value = <?php echo " \"$relatie\" "; ?> >
</td></tr><tr><td>
Adres </td>
<td>: <input type= "text" name= "txtstraat" value = <?php echo " \"$straat\" "; ?> >
</td></tr><tr><td>
Huisnummer </td>
<td>:  <input type= "text" name= "txtnr" value = <?php echo " \"$nr\" "; ?> >
</td></tr><tr><td>
Postcode </td>
<td>:  <input type= "text" name= "txtpc" value = <?php echo " \"$pc\" "; ?> > 
</td></tr><tr><td>
Woonplaats </td>
<td>:  <input type= "text" name= "txtplaats" value = <?php echo " \"$plaats\" "; ?> > 
</td></tr><tr><td>
Postadres </td>
<td>: <input type= "text" name= "txtstraat1" value = <?php echo " \"$straat1\" "; ?> >
</td></tr><tr><td>
Nummer </td>
<td>:  <input type= "text" name= "txtnr1" value = <?php echo " \"$nr1\" "; ?> > 
</td></tr><tr><td>
Postcode </td>
<td>:  <input type= "text" name= "txtpc1" value = <?php echo " \"$pc1\" "; ?> > 
</td></tr><tr><td>
Woonplaats </td>
<td>:  <input type= "text" name= "txtplaats1" value = <?php echo " \"$plaats1\" "; ?> > 
</td></tr>
</table>
 </td><td width = 100 ></td><td valign =top >
<table border = 0 >
<tr><td>
Telefoonnummer </td>
<td colspan = 2>:  <input type= "text" name= "txttel" value = <?php echo " \"$tel\" "; ?> >
</td></tr><tr><td>
Faxnummer </td>
<td colspan = 2>:  <input type= "text" name= "txtfax" value = <?php echo " \"$fax\" "; ?> >
</td></tr><tr><td>
email </td>
<td colspan = 2>:  <input type= "text" name= "txtmail" value = <?php echo " \"$email\" "; ?> >
</td></tr><tr><td>
Website </td>
<td colspan = 2>:  <input type= "text" name= "txtsite" value = <?php echo " \"$site\" "; ?> >
</td></tr><tr><td>
Banknummer </td>
<td colspan = 2>:  <input type= "text" name= "txtbank" value = <?php echo " \"$bank\" "; ?> >
</td></tr><tr>
 <td>Actief</td> <td>:  <input type = "checkbox" name = "chkActief" id= "c1" value= "1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?>         title = "Is debiteur te gebruiken ja/nee ?"> </td>
</td></tr>    
<?php    } ?>



    <tr><td>
Kenteken </td>
<td colspan = 2>:  <input type= "text" name= "txtkent" value = <?php echo " \"$kent\" "; ?> >
</td></tr><tr><td>
Aanhanger </td>
<td colspan = 2>:  <input type= "text" name= "txthang" value = <?php echo " \"$hang\" "; ?> >
</td></tr>
</table>
 </td></tr>
<tr><td colspan = 8 align = center>

<br><input type= "submit" name= "knpUpdate" value = "Opslaan" >

</form>
</td></tr></table >


</TD>
<?php
include "menuBeheer.php"; } ?>
</tr>
</table>
</center>

</body>
</html>
