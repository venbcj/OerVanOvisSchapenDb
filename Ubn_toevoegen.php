<?php 

$versie = '03-07-2025'; /* Bestand gemaakt als kopie van Hok.php */

session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Ubn';
$file = "Ubn_toevoegen.php";
include "login.php"; ?>

				<TD align = "center" valign = "top">
<?php 
if (is_logged_in()) {

if (isset($_POST['knpSave_'])) { include "save_ubn.php"; }

if (isset($_POST['knpInsert_']))
{
	$new_ubn = $_POST['insUbn_'];
	$new_adres = $_POST['insAdres_'];
	$new_plaats = $_POST['insPlaats_'];
// Zoek naar ubn op duplicaten
$controle = mysqli_query($db,"
SELECT count(ubn) aantal
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and ubn = '".mysqli_real_escape_string($db,$new_ubn)."'
") or die (mysqli_error($db));
				while ($row = mysqli_fetch_assoc($controle))
				{
					$dubbel = $row['aantal'];
				} // Einde Zoek naar ubn op duplicaten
	
	if (empty($_POST['insUbn_'])) 				{ $fout = "U heeft geen ubn ingevoerd."; }	
	else if (isset($dubbel) && $dubbel > 0)	 	{ $fout = "Dit ubn bestaat al.";	$new_ubn = '';	}	
	else 
	{
$ubn_toevoegen = "
  INSERT INTO tblUbn SET lidId = '".mysqli_real_escape_string($db,$lidId)."', ubn = '".mysqli_real_escape_string($db,$new_ubn)."', adres = ".db_null_input($new_adres).", plaats = ".db_null_input($new_plaats) ;
		
				/*echo $ubn_toevoegen; */ mysqli_query($db,$ubn_toevoegen) or die (mysqli_error($db));
	}
} ?>

<form action="Ubn_toevoegen.php" method="post">
<table border = 0>
<tr>
 <td width = 600 valign = 'top'>
<table border = 0>
<tr>
 <td> <b> Nieuw ubn : </b> </td>
 <td> <b> Adres : </b> </td>
 <td> <b> Woonplaats : </b> </td>
</tr>
<tr>
 <td> <input type= "text" name= "insUbn_" 	 size="8"	 value = <?php if(isset($new_ubn)) { echo $new_ubn; }; ?> > </td>
 <td> <input type= "text" name= "insAdres_"  					 value = <?php if(isset($new_adres)) { echo " \"$new_adres\" "; }; ?> > </td>
 <td> <input type= "text" name= "insPlaats_" size="12" value = <?php if(isset($new_plaats)) { echo " \"$new_plaats\" "; }; ?> > </td>
 <td> <input type = "submit" name= "knpInsert_" value = "Toevoegen" > </td>
</tr>
</table>

 </td>
 <td>		
<table border = 0 align = 'left' >
<tr>
 <td align="center" valign="bottom"> <b> Ubn</b> <hr></td>
 <td align="center"> in<br>gebruik <hr></td>
 <td align="center"> ver-<br>wijder <hr></td>
 <td align="center" valign="bottom"> Adres <hr></td>
 <td align="center" valign="bottom"> Woonplaats <hr></td>
 <td align="center" valign="bottom" width="100"> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> <hr></td>
 <td ></td>	
</tr>


<?php
// START LOOP	
$zoek_ubn = mysqli_query($db,"
SELECT ubnId, ubn, adres, plaats, actief
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY actief desc, ubn
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($zoek_ubn))
	{
	  $Id = $row['ubnId'];
	  $ubnnr = $row['ubn'];
	  $adres = $row['adres'];
	  $woonplaats = $row['plaats'];
	  $actief = $row['actief'];


$zoek_db_tabelrelaties =	mysqli_query($db,"
SELECT u.ubnId
FROM tblUbn u
 left join tblStal st on (st.ubnId = u.ubnId)
WHERE u.ubnId = '".mysqli_real_escape_string($db,$Id)."' and isnull(st.stalId)
") or die (mysqli_error($db));

	while($zdr = mysqli_fetch_assoc($zoek_db_tabelrelaties))
	{ $dbRelatie = $zdr['ubnId']; }  


If(!isset($_POST['txtAdres'])) { $txtAdres = $adres; } else { $txtAdres = $_POST['txtAdres']; } 
If(!isset($_POST['txtPlaats'])) { $txtPlaats = $woonplaats; } else { $txtPlaats = $_POST['txtPlaats']; } 

if($actief == 0) { $color = '#E2E2E2'; } else { $color = 'black'; }
?>


<tr>
 <td style = "color : <?php echo $color; ?> ;" > <?php echo $ubnnr; ?> </td>
 <td align="center"> 
	<input type = "checkbox" name = <?php echo "chbActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?> 		title = "Is ubn te gebruiken ja/nee ?">
 </td>
 <td align="center">
 	<?php if(isset($dbRelatie)) { ?>
 	<input type="checkbox" name= <?php echo "chbDel_$Id"; ?> >
 <?php } unset($dbRelatie); ?>
 </td>
 <td style = "color : <?php echo $color; ?> ;" >
<?php if($actief == 0) { echo $txtAdres; } else { ?> 
 	<input type="text" name= <?php echo "txtAdres_$Id"; ?> value = <?php echo " \"$txtAdres\" "; ?> >
<?php } ?>
 </td>
 <td style = "color : <?php echo $color; ?> ;" >
<?php if($actief == 0) { echo $txtPlaats; } else { ?> 
 	<input type="text" name= <?php echo "txtPlaats_$Id"; ?> size = 12 value = <?php echo " \"$txtPlaats\" "; ?> >
<?php } ?>
 </td>
</tr>
<?php	} ?>
 </td>
</tr>
</table>
</td></tr></table>

</form>



	</TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
