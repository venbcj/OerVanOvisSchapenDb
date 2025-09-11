<?php 

require_once("autoload.php");

require_once('url_functions.php');

$versie = '14-8-2014'; /*Menu (rechts) veranderd van menuInkoop naar menuBeheer en html buiten php geprogrammeerd */
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
if (isset ($_POST['knpSave_'])) { /*header("Location: ".$url."Klanten.php");*/ }
$titel = 'Debiteuren';
$subtitel = ''; 
include "header.tpl.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Klanten.php";
include "login.php"; 
if (is_logged_in()) {

if (isset($_POST['knpSave_'])) { include "save_klanten.php"; }

if (isset($_POST['knpInsert']))
{

	$controle = mysqli_query($db,"
	select count(partId) aantal
	from tblPartij
	 join tblRelatie r on (p.partId = r.partId)
	where p.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.naam = '$_POST[insklant])' and r.relatie = 'deb'
	group by naam
	") or die (mysqli_error($db));
				while ($rij = mysqli_fetch_assoc($controle))
				{
					$dubbel = ("{$rij['aantal']}");
				}

	if (empty($_POST['insklant']))
	{ ?>
		<center style = "color : red;">U heeft geen klant ingevoerd.
<?php	}
	else if (!empty($dubbel) && $dubbel >= 1 )
	{ ?>
		Deze klant bestaat al.
<?php	}
	else 
	{
if (empty($_POST['insubn']))	{	$insubn = "NULL";	}
  else	{	$insubn = "'$_POST[insubn]'";	}

if (empty($_POST['insklant']))	{	$insklant = "NULL";	}
  else		{	$insklant = " '$_POST[insklant]' ";	}
  
if (empty($_POST['insstraat']))	{	$insstraat = "NULL";	}
  else		{	$insstraat = " '$_POST[insstraat]' ";	}

if (empty($_POST['insnr']))	{	$insnr = "NULL";	}
  else		{	$insnr = " '$_POST[insnr]' ";	}

if (empty($_POST['inspc']))	{	$inspc = "NULL";	}
  else	{	$inspc = "' $_POST[inspc]'";	}
  
if (empty($_POST['insplaats']))	{	$insplaats = "NULL";	}
  else	{	$insplaats = "'$_POST[insplaats]'";	}  

if (empty($_POST['instel']))	{	$instel = "NULL";	}
  else	{	$instel = "'$_POST[instel]'";	}

  
		$query_rel_toevoegen= "INSERT INTO tblRelatie SET lidId = ".mysqli_real_escape_string($db,$lidId).", relatie = ".$insklant.", straat = ".$insstraat.", nr = ".$insnr.", pc = ".$inspc.", plaats = ".$insplaats.", tel=  ".$instel.", ubn = ".$insubn.", dc = 'debiteur', actief = 1 ";
		
				mysqli_query($db,$query_rel_toevoegen) or die (mysqli_error($db));
	}
} ?>

<form action= "Klanten.php" method= "post" >
<table border= 0  align = "left" >
<tr>
 <td colspan = 12> <b>Debiteuren :</b> </td>
 <td ><input type = "submit" name= <?php echo "knpSave_"; ?> value = "Opslaan" ></td> 
</tr>


<tr style = "font-size:12px;" valign = "bottom">
 <th>ubn</th>
 <th>Bedrijfsnaam</th>
 <th></th>
 <th>Vestigingsadres</th>
 <th>Huisnr</th>
 <th></th>
 <th>Postcode</th>
 <th></th>
 <th>Woonplaats</th>
 <th>Telefoon</th>
 <th>Actief</th>
</tr>
<?php		
// START LOOP
$loop = mysqli_query($db,"
select relId, naam
from tblPartij p
 join tblRelatie r on (r.partId = p.partId)
where p.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.relatie = 'deb'
order by p.naam
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($loop))
	{
            $Id = ("{$record['relId']}");  



if (empty($_POST['txtId']))		{	$rowid = NULL;	}
  else		{	$rowid = $_POST['txtId'];	}

if (empty($_POST['txtubn']))	{	$updubn = "ubn = NULL";	}
  else		{	$updubn = "ubn = '$_POST[txtubn]' ";	}
    
if (empty($_POST['txtnaam']))	{	$updnaam = "NULL";	}
  else		{	$updnaam = " '$_POST[txtnaam]' ";	}
  
if (empty($_POST['txtStraat']))	{	$updStraat = "NULL";	}
  else		{	$updStraat = " '$_POST[txtStraat]' ";	}
	
if (empty($_POST['txtnr']))		{	$updnr = "nr = NULL";	}
  else		{	$updnr = "nr = '$_POST[txtnr]' ";	}

if (empty($_POST['txtpc']))		{	$updpc = "pc = NULL";	}
  else		{	$updpc = "pc = '$_POST[txtpc]' ";	}

if (empty($_POST['txtplaats']))	{	$updplaats = "plaats = NULL";	}
  else		{	$updplaats = "plaats = '$_POST[txtplaats]' ";	}

if (empty($_POST['txttel']))	{	$updtel = "tel = NULL";	}
  else		{	$updtel = "tel = '$_POST[txttel]' ";		}
  
if (empty($_POST['chkActief']))	{	$updact = "actief = NULL";	}
  else		{	$updact = "actief = '$_POST[chkActief]' ";	}



$query = mysqli_query($db,"
select relId, relatie, ubn, naam, straat, nr, pc, plaats, tel, fax, email, site, banknr, r.actief, kenteken, aanhanger
from tblPartij p
 join tblRelatie r on (r.partId = p.partId)
 left join tblAdres a on (p.partId = a.partId)
 left join tblVervoer v on (p.partId = v.partId)
where relId = '$Id'
order by naam
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($query))
	{
		$ubn = "{$row['ubn']}";
		$naam = "{$row['naam']}";
		$straat = "{$row['straat']}"; $nr = "{$row['nr']}";
		$pc = "{$row['pc']}";
		$plaats = "{$row['plaats']}";
		$tel = "{$row['tel']}";
		$fax = "{$row['fax']}";
		$email = "{$row['email']}";
		$site = "{$row['site']}";
		$bank = "{$row['banknr']}";
		$kent = "{$row['kenteken']}";
		$hang = "{$row['aanhanger']}";
?>		 
<tr style = "font-size:12px;">
 <td><!--Id --><input type= "text" name= <?php echo "txtId_$Id"; ?> size = 1 value = <?php echo $Id; ?> > <!-- hiddden -->
 <input type= "text" name= <?php echo "txtUbn_$Id"; ?> size = 5 value = <?php echo $ubn; ?> ></td>
 <td><input type= "text" name= <?php echo "txtNaam_$Id"; ?> value = <?php echo " \"$naam\" "; ?> ></td>
 <td width = 1></td>
 <td><input type= "text" name= <?php echo "txtStraat_$Id"; ?> value = <?php echo " \"$straat\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtNr_$Id"; ?> value = <?php echo " \"$nr\" "; ?> style= "width: 30px; padding: 2px"></td>
 <td width = 1></td>
 <td><input type= "text" name= <?php echo "txtPc_$Id"; ?> value = <?php echo " \"$pc\" "; ?> style= "width: 60px;"></td>
 <td width = 1></td>
 <td><input type= "text" name= <?php echo "txtPlaats_$Id"; ?> value = <?php echo " \"$plaats\" "; ?> ></td>
 <td><input type= "text" name= <?php echo "txtTel_$Id"; ?> value = <?php echo " \"$tel\" "; ?> style= "width: 80px;"></td>
 <td><input type = "checkbox" name = <?php echo "chkActief_$Id"; ?> id= "c1" value= "1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> 		title = "Is debiteur te gebruiken ja/nee ?"> </td>
		
 <td width = 80> <a href='<?php echo $url; ?>Klant.php?pstid=<?php echo $Id; ?>' style = "color : blue"> meer gegevens </a> </td>
 <td> 			 <a href='<?php echo $url; ?>Contact.php?pstid=<?php echo $Id; ?>' style = "color : blue"> contacten </a> </td>

	</td>
		
<?php	}	?>

 <td> </td>
</tr>
	

<?php    } ?>
</td>
</tr>
<tr><td colspan = 15><hr></td></tr>
<tr><td colspan = 2 style = "font-size:13px;"><i> Nieuwe debiteur : </i></td></tr>
<tr><td><input type= "text" name= "insubn_" size = 5 value = ''></td>
<td><input type= "text" name= "insklant_" value = ''></td>
<td></td>
<td><input type= "text" name= "insstraat_" value = ''></td>
<td><input type= "text" name= "insnr_" value = '' style= "width: 30px; padding: 2px"></td>
<td width = 1></td>
<td><input type= "text" name= "inspc_" value = '' style= "width: 60px;"></td>
<td width = 1></td>
<td><input type= "text" name= "insplaats_" value = ''></td>
<td><input type= "text" name= "instel_" value = '' style= "width: 80px;"></td>
<td><input type= "checkbox" name= "boxactief_" id= "c2" <?php if(true){ echo "checked"; } ?>  disabled ></td>
<td colspan = 2><input type = "submit" name= "knpInsert_" value = "Toevoegen" style = "font-size:10px;"></td></tr>
</table>
</form>
	

</TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
