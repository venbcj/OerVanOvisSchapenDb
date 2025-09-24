<?php

require_once("autoload.php");


$versie = '14-8-2014'; /*Menu (rechts) veranderd van menuInkoop naar menuBeheer en html buiten php geprogrammeerd */
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '23-02-2025'; /* <input type= "hidden" name = <?php echo "txtPersId_Id"; ?> verwijderd en <input  type= "hidden" name= "txtId_" vervangen door Session::get("CNT") en alleen aanhef verplicht gemaakt */

Session::start();
 ?>
<html>
<head>
<title>Inkoop</title>
</head>
<body>

<?php
$titel = 'Contactpersonen';
$file = "Beheer.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) { 

If (empty($_GET['pstid']))
{    $partId = Session::get("CNT") ?? 0;    }
else { Session::set("CNT", $_GET['pstid']); $partId = $_GET['pstid'];    }

if (isset ($_POST['knpInsert_']))
{
    if (empty($_POST['kzlSeskse_']) /*|| empty($_POST['insRoep_']) || empty($_POST['insLetter_']) || empty($_POST['insNaam_'])*/     )
    {
         
         if(!empty($_POST['insRoep_'])) { $txtRoep = $_POST['insRoep_']; }
         if(!empty($_POST['insLetter_'])) { $txtLetter = $_POST['insLetter_']; }
         if(!empty($_POST['insVgsl_'])) { $txtVgsl = $_POST['insVgsl_']; }
         if(!empty($_POST['insNaam_'])) { $txtNaam = $_POST['insNaam_']; }
         if(!empty($_POST['insTel_'])) { $txtTel = $_POST['insTel_']; }
         if(!empty($_POST['insGsm_'])) { $txtGsm = $_POST['insGsm_']; }
         if(!empty($_POST['insMail_'])) { $txtMail = $_POST['insMail_']; }
         if(!empty($_POST['insFunct_'])) { $txtFunct = $_POST['insFunct_']; }

         /*if ( empty($_POST['insRoep_']) || empty($_POST['insLetter_']) || empty($_POST['insNaam_']) ) { $fout = 'De naam is onvolledig.'; }
         else if (empty($_POST['kzlSeskse_'])) { */
        $fout = 'De aanhef is verplicht.'; //}
    }
    else
    {
  
$insVoor = $_POST['insRoep_'];
$insLetr = $_POST['insLetter_'];
$insVgsl = $_POST['insVgsl_'];
$insNaam = $_POST['insNaam_'];
$insTel = $_POST['insTel_'];
$insGsm = $_POST['insGsm_'];
$insMail = $_POST['insMail_'];
$insFunct = $_POST['insFunct_'];
    

        $pers_invoegen = "INSERT INTO tblPersoon SET partId = '".mysqli_real_escape_string($db,$partId)."', roep = ".db_null_input($insVoor).", letter = ".db_null_input($insLetr).", voeg = ".db_null_input($insVgsl).", naam = ".db_null_input($insNaam).", geslacht = '".$_POST['kzlSeskse_']."', tel = ".db_null_input($insTel).", gsm = ".db_null_input($insGsm).", mail = ".db_null_input($insMail).", functie = ".db_null_input($insFunct)." ";
//echo $pers_invoegen;        
                mysqli_query($db,$pers_invoegen) or die (mysqli_error($db));
    }
}

if (isset ($_POST['knpSave_']))
{ include "save_contact.php";    }

$querybedrijf = mysqli_query($db,"SELECT naam FROM tblPartij WHERE partId = ".mysqli_real_escape_string($db,$partId)." ") or die (mysqli_error($db));
$bedrijf = '';
        While($rij= mysqli_fetch_assoc($querybedrijf))
        {    $bedrijf = ("{$rij['naam']}");    } ?>

<form action= "Contact.php" method= "post" > 
<table border= 0 width= 100 align = "left" >
<tr>
 <td colspan = 6 valign = "top" height = 45px>
    Contactpersonen van : <b><?php echo "$bedrijf"; ?> </b>
 </td>
 <td colspan = 15 align = right ><input type = "submit" name= "knpSave_" value = "Opslaan" ></td>
</tr>

<tr style = "font-size:12px;" valign = "bottom">
        <th>Aanhef*</th>
        <th>Voornaam*</th>
        <th>Voorletter*</th>
        <th></th>
        <th>Tus.voegsel</th>
        <th>Achternaam*</th>
        <th></th>
        <th>Telefoon</th>
        <th>gsm</th>
        <th>E-mail</th>
        <th>Functie</th>
        <th>Actief</th>
</tr>
<?php        
// START LOOP
$loop = mysqli_query($db,"
SELECT persId
FROM tblPersoon
WHERE partId = ".mysqli_real_escape_string($db,$partId)."
ORDER BY actief desc
") or die (mysqli_error($db));

    while($record = mysqli_fetch_assoc($loop))
    {
            $Id = ("{$record['persId']}");  

$query = mysqli_query($db,"
SELECT persId, partId, letter, roep, voeg, naam, geslacht, tel, gsm, mail, functie, actief
FROM tblPersoon
WHERE persId = ".mysqli_real_escape_string($db,$Id)."
ORDER BY naam
") or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($query))
    {
            if($row['geslacht'] == 'm')
        {$aanhef = "Dhr.";    }
            else if($row['geslacht'] == 'v')
        {$aanhef = "Mevr.";    }
        $roep = "{$row['roep']}";
        $letter = "{$row['letter']}"; 
        $voeg = "{$row['voeg']}";
        $naam = "{$row['naam']}";
        //$ = "{$row['']}";
        $tel = "{$row['tel']}";
        $gsm = "{$row['gsm']}";
        $email = "{$row['mail']}";
        $functie = "{$row['functie']}"; ?>

<tr style = "font-size:12px;">
 <td align = center>
   <?php echo "$aanhef"; ?> </td>
 <td><input type= "text" name = <?php echo "txtRoep_$Id"; ?> value = <?php echo " \"$roep\" "; ?> style= "width: 100px;"></td>
 <td><input type= "text" name = <?php echo "txtLetter_$Id"; ?> value = <?php echo " \"$letter\" "; ?> style= "width: 50px; padding: 2px"></td>
 <td width = 1></td>
 <td><input type= "text" name = <?php echo "txtVgsl_$Id"; ?> value = <?php echo " \"$voeg\" "; ?> style= "width: 60px;"></td>
 <td><input type= "text" name = <?php echo "txtNaam_$Id"; ?> value = <?php echo " \"$naam\" "; ?>></td>
 <td width = 1></td>
 <td><input type= "text" name = <?php echo "txtTel_$Id"; ?> value = <?php echo " \"$tel\" "; ?> style= "width: 90px;"></td>
 <td><input type= "text" name = <?php echo "txtGsm_$Id"; ?> value = <?php echo " \"$gsm\" "; ?> style= "width: 90px;"></td>
 <td><input type= "text" name = <?php echo "txtMail_$Id"; ?> value = <?php echo " \"$email\" "; ?>></td>
 <td><input type= "text" name = <?php echo "txtFunct_$Id"; ?> value = <?php echo " \"$functie\" "; ?>></td>
 <td><input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id= "c1" value= "1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> title = "Is contactpersoon te gebruiken ja/nee ?"> </td>
 <td> </td>
</tr>
 <?php

    } ?>
</tr>
<?php }  ?>
</td>
</tr>
<tr><td colspan = 12 align = 'right'> *</td><td> verplicht</td></tr>
<tr><td colspan = 15 ><hr></td></tr> 
<tr><td colspan = 2 style = "font-size:13px;"><i> Nieuwe contactpersoon : </i></td></tr>
<td>
<?php    $opties = array('' => '','m' => 'Dhr.', 'v' => 'Mevr.'); ?>

<select name= "kzlSeskse_" style= "width:50; ">
<?php
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlSeskse_']) && $_POST['kzlSeskse_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
</select> </td>
 <td><input type="text" name= "insRoep_" style="width: 100px;"    value = <?php if(isset($txtRoep)) { echo $txtRoep;} ?> ></td>
 <td><input type="text" name= "insLetter_" style="width: 50px; padding: 2px"     value = <?php if(isset($txtLetter)) { echo $txtLetter;} ?> ></td>
 <td width = 1 >
     <input  type= "hidden" name= "insId_"  value = <?php echo $partId; ?> > </td> <!-- hiddden -->
 <td><input type="text" name= "insVgsl_" style="width: 60px;"    value = <?php if(isset($txtVgsl)) { echo $txtVgsl;} ?> ></td>
 <td><input type="text" name= "insNaam_"     value = <?php if(isset($txtNaam)) { echo $txtNaam;} ?>></td>
 <td width = 1></td>
 <td><input type="text" name= "insTel_" style="width: 80px;"        value = <?php if(isset($txtTel)) { echo $txtTel;} ?> ></td>
 <td><input type="text" name= "insGsm_" style="width: 80px;"        value = <?php if(isset($txtGsm)) { echo $txtGsm;} ?> ></td>
 <td><input type="text" name= "insMail_"     value = <?php if(isset($txtMail)) { echo $txtMail;} ?>></td>
 <td><input type="text" name= "insFunct_"     value = <?php if(isset($txtFunct)) { echo $txtFunct;} ?>></td>
 <td><input type= "checkbox" name= "boxactief_" id= "c2" <?php if(true){ echo "checked"; } ?>  disabled ></td>
 <td colspan = 2 ><input type = "submit" style = "font-size:10px;" name="knpInsert_" value = "Toevoegen" ></td></tr>
</table>
</form>
    


        
        
</TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
