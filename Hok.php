<?php

require_once("autoload.php");


$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/Hok.php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd */
$versie = '18-11-2015'; /* hok verandert in verblijf*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer t.b.v. reader Agrident aangepast. Dubbele loop Hokken en  hidden velden scan en actief verwijderd */
$versie = '02-08-2020'; /* veld sort toegevoegd */
$versie = '20-04-2024'; /* Niet actieve verblijven weggefilterd met optie om te tonen. Verblijven die niet zijn gekoppeld aan andere tabellen kunnen worden verwijderd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '10-03-2025'; /* hidden veld chbActief_$Id verwijderd. <input type = hidden name = <?php echo "chbActief_$Id"; ?> value= 0 > <!-- hiddden --> */

Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Verblijven';
$file = "Hok.php";
include "login.php"; ?>

                <TD align = "center" valign = "top">
<?php 
if (Auth::is_logged_in()) { if($modtech ==1) {

include "vw_HoknBeschikbaar.php"; // toegepast in save_hok.php

if (isset ($_POST['knpSave_'])) { include "save_hok.php"; }

if (isset ($_POST['knpInsert_']))
{
    $hok = $_POST['insHok_'];
// Zoek naar hok op duplicaten
$zoek_hok = mysqli_query($db,"
SELECT hoknr
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and hoknr = '".mysqli_real_escape_string($db,$hok)."'
") or die (mysqli_error($db));
                while ($zh = mysqli_fetch_assoc($zoek_hok))
                {
                    $hok_aanwezig = $zh['hoknr'];
                } 
// Einde Zoek naar hok op duplicaten
                
    if (empty($_POST['insHok_']))                             { $fout = "U heeft geen verblijf ingevoerd."; }    
    else if(isset($hok_aanwezig))                                 { $fout = "Deze omschrijving bestaat al.";    $hok = '';    }    
    else if(!empty($hok) && strlen("$hok")> 10)    { $fout = "Het verblijf mag uit max. 10 karakters bestaan."; }    
    else 
    {

$query_hok_toevoegen= "
  INSERT INTO tblHok 
  SET lidId = '".mysqli_real_escape_string($db,$lidId)."', 
      hoknr = '".mysqli_real_escape_string($db,$hok)."',
      sort = ". db_null_input($_POST['insSort_']);

                 mysqli_query($db,$query_hok_toevoegen) or die (mysqli_error($db));
    }
}

$zoek_hok = mysqli_query($db,"
SELECT hokId
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY sort, hokId
") or die (mysqli_error($db));

    while($line = mysqli_fetch_assoc($zoek_hok))
    {
            $pdf = $line['hokId']; 
    } 

if(isset($_POST['radHide']) && $_POST['radHide'] == 0) { $Gebl = -1; } 
 else { $Gebl = 0; }

$zoek_db_relaties_alle_verblijven =    mysqli_query($db,"
SELECT count(h.hokId) aant
FROM tblHok h
 left join tblBezet b on (h.hokId = b.hokId)
 left join tblPeriode p on (h.hokId = p.hokId)
WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief > '".mysqli_real_escape_string($db,$Gebl)."' and isnull(b.hokId) and isnull(p.hokId)
") or die (mysqli_error($db));

    while($zdrav = mysqli_fetch_assoc($zoek_db_relaties_alle_verblijven))
    { $dbRelatie_allVerblijven = $zdrav['aant']; }    ?>

<form action="Hok.php" method="post">
<table border = 0>
<tr>
 <td width = 450 valign = 'top'>
<table border = 0>
<tr>
 <td>
<b> Nieuw verblijf </b> 
 </td>
 <td align = center width = 10 style ="font-size:12px;"> <b> sortering reader </b>
 </td>
</tr>
<tr>
 <td>
  <input type= "text" name= "insHok_" value = <?php if(isset($hok)) { echo $hok; }; ?> >
 </td>
 <td>
    <input type= "text" name= "insSort_" size = 1 title = "Leg hier het nummer vast om de volgorde in de reader te bepalen." > 
 </td>
 <td> <input type = "submit" name= "knpInsert_" value = "Toevoegen" > </td>
</tr>
</table>

 </td>
 <td>        
<table border = 0 align = 'left' >
<tr>
 <td> <b> Verblijven</b> </td>
 <td align = center style ="font-size:12px;"> sortering<br>reader </td>
 <td align = center style ="font-size:12px;"> in<br>gebruik </td>
<?php if($dbRelatie_allVerblijven > 0) { ?>
 <td align = center style ="font-size:12px;"> verwijder </td>
<?php } ?>
 <td align = center valign="bottom" width="100"> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> </td>
 <td ></td>

 <td style ="font-size:12px;"> 
         <input type = radio name = "radHide" value = 1 <?php if(!isset($_POST['knpToon']) || (isset($_POST['radHide']) && $_POST['radHide'] == 1 )) { echo "checked"; } ?> > Excl.

      <input type = radio name = "radHide" value = 0 <?php if(isset($_POST['radHide']) && $_POST['radHide'] == 0 ) { echo "checked"; } ?> > Incl. <br>&nbsp&nbsp&nbsp&nbsp geblokkeerden &nbsp&nbsp

    <input type = "submit" name ="knpToon" value = "Toon">
 </td>
 <td width= 200 align="right">
     <a href= '<?php echo $url;?>Hok_pdf.php?Id=<?php echo $pdf ?? ''; ?>' style = 'color : blue'>
    print pagina </a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
 </td>    
</tr>
<tr>
 <td colspan = 6><hr> </td>
</tr>


<?php
// START LOOP    
$query = mysqli_query($db,"
SELECT hokId, hoknr, scan, sort, actief
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief > '".mysqli_real_escape_string($db,$Gebl)."'
ORDER BY coalesce(sort, hoknr)
") or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($query))
    {
      $Id = $row['hokId'];
      $hoknr = $row['hoknr'];
      $scan = $row['scan']; 
      $sort = $row['sort']; 
      $actief = $row['actief'];



$zoek_db_relaties =    mysqli_query($db,"
SELECT h.hokId
FROM tblHok h
 left join tblBezet b on (h.hokId = b.hokId)
 left join tblPeriode p on (h.hokId = p.hokId)
WHERE h.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(b.hokId) and isnull(p.hokId)
") or die (mysqli_error($db));

    while($zdr = mysqli_fetch_assoc($zoek_db_relaties))
    { $dbRelatie = $zdr['hokId']; }  ?>



<tr>
 <td> <?php echo $hoknr; ?> </td>
 <td align = "center">
    <input type = text name = <?php echo "txtSort_$Id"; ?> size = 1 value = <?php echo $sort; ?>  >
 </td>
 <td align="center"> 
    <input type = "checkbox" name = <?php echo "chbActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?>         title = "Is verblijf te gebruiken ja/nee ?">
 </td>
 <td align="center">
     <?php if(isset($dbRelatie)) { ?>
     <input type="checkbox" name= <?php echo "chbDel_$Id"; ?> >
 <?php } unset($dbRelatie); ?>
 </td>
</tr>
<?php    } ?>
 </td>
</tr>
</table>
</td></tr></table>

</form>



    </TD>
<?php } else { ?> <img src='hok_php.jpg'  width='970' height='550'/> <?php }
include "menuBeheer.php"; } ?>
</body>
</html>
