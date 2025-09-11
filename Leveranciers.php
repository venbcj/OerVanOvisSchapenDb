<?php

require_once("autoload.php");


 $versie = '14-8-2014'; /*Menu (rechts) veranderd van menuInkoop naar menuBeheer en html buiten php geprogrammeerd */
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is*/
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
if (isset ($_POST['knpUpdate'])) { include "url.php"; header("Location: ".$url."Leveranciers.php"); }
$titel = 'Crediteuren';
$subtitel = '';
include "header.tpl.php"; ?>

		<TD width = 960 height = 400 valign = "top">
<?php
$file = "Leveranciers.php";
include "login.php"; 
if (Auth::is_logged_in()) { 

if (isset ($_POST['knpInsert']))
{	

	$controle = mysqli_query($db,"
	select count(partId) aantal
	from tblPartij
	 join tblRelatie r on (p.partId = r.partId)
	where p.lidId = ".mysqli_real_escape_string($db,$lidId)." and p.naam = '$_POST[inslever])' and r.relatie = 'cred'
	group by naam
	") or die (mysqli_error($db));
				while ($rij = mysqli_fetch_assoc($controle))
				{
					$dubbel = ("{$rij['aantal']}");
				}

	if (empty($_POST['inslever']))
	{ ?>
		<center style = "color : red;">U heeft geen leverancier ingevoerd.
<?php }
	else if (!empty($dubbel) && $dubbel >= 1 )
	{ ?>
		Deze leverancier bestaat al.
<?php	}
	else 
	{
if (empty($_POST['insubn']))	{	$insubn = "NULL";	}
  else	{	$insubn = "'$_POST[insubn]'";	}

if (empty($_POST['inslever']))	{	$inslever = "NULL";	}
  else		{	$inslever = " '$_POST[inslever]' ";	}
  
if (empty($_POST['insadres']))	{	$insadres = "NULL";	}
  else		{	$insadres = " '$_POST[insadres]' ";	}

if (empty($_POST['insnr']))	{	$insnr = "NULL";	}
  else		{	$insnr = " '$_POST[insnr]' ";	}

if (empty($_POST['inspc']))	{	$inspc = "NULL";	}
  else	{	$inspc = "' $_POST[inspc]'";	}
  
if (empty($_POST['insplaats']))	{	$insplaats = "NULL";	}
  else	{	$insplaats = "'$_POST[insplaats]'";	}  

if (empty($_POST['instel']))	{	$instel = "NULL";	}
  else	{	$instel = "'$_POST[instel]'";	}

  
		$query_rel_toevoegen= "INSERT INTO tblRelatie SET lidId = ".mysqli_real_escape_string($db,$lidId).", relatie = ".$inslever.", adres = ".$insadres.", nr = ".$insnr.", pc = ".$inspc.", plaats = ".$insplaats.", tel=  ".$instel.", ubn = ".$insubn.", dc = 'crediteur', actief = 1 ";
		
				mysqli_query($db,$query_rel_toevoegen) or die (mysqli_error($db));
	}
}
?>

<table border= 0 align = "left" >
<tr>
<td colspan = 2>
<b>Crediteuren :</b>
</td></tr>


<tr style = "font-size:12px;" valign = "bottom">
 <th>Ubn</th>
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
where p.lidId = ".mysqli_real_escape_string($db,$lidId)." and r.relatie = 'cred'
order by p.naam
") or die (mysqli_error($db));

	while($record = mysqli_fetch_assoc($loop))
	{
            $id = ("{$record['relId']}");  



if (empty($_POST['txtId']))		{	$rowid = NULL;	}
  else		{	$rowid = $_POST['txtId'];	}

if (empty($_POST['txtubn']))	{	$updubn = "ubn = NULL";	}
  else		{	$updubn = "ubn = '$_POST[txtubn]' ";	}
  
if (empty($_POST['txtnaam']))	{	$updnaam = "NULL";	}
  else		{	$updnaam = " '$_POST[txtnaam]' ";	}
  
if (empty($_POST['txtadres']))	{	$updadres = "NULL";	}
  else		{	$updadres = " '$_POST[txtadres]' ";	}
	
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
select relId, naam, ubn, naam, adres, nr, pc, plaats, tel, fax, email, site, banknr, r.actief, kenteken, aanhanger
from tblPartij p
 join tblRelatie r on (r.partId = p.partId)
 left join tblAdres a on (p.partId = a.partId)
 left join tblVervoer v on (p.partId = v.partId)
where relId = '$id'
order by naam
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($query))
	{
		$ubn = "{$row['ubn']}";
		$naam = "{$row['naam']}";
		$straat = "{$row['adres']}"; $nr = "{$row['nr']}";
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
<form action= "Leveranciers.php" method= "post" > 
		<tr style = "font-size:12px;">
		<td><input type= "hidden" name= "txtId" value = <?php echo "$id"; ?> >
			<input type= "text" name= "txtubn" size = 5 value = <?php echo " \"$ubn\" "; ?> ></td>
		<td><input type= "text" name= "txtnaam" value = <?php echo " \"$naam\" "; ?> ></td>
		<td width = 1></td>
		<td><input type= "text" name= "txtadres" value = <?php echo " \"$straat\" "; ?> ></td>
		<td><input type= "text" name= "txtnr" value = <?php echo " \"$nr\" "; ?> style= "width: 30px; padding: 2px"></td>
		<td width = 1></td>
		<td><input type= "text" name= "txtpc" value = <?php echo " \"$pc\" "; ?> style= "width: 60px;"></td>
		<td width = 1></td>
		<td><input type= "text" name= "txtplaats" value = <?php echo " \"$plaats\" "; ?> ></td>
		<td><input type= "text" name= "txttel" value = <?php echo " \"$tel\" "; ?> style= "width: 80px;"></td>
<?php	if ($id == $rendac_Id) {
?><td><input type= "checkbox" name= "chkactief" id= "c1" <?php if(true){ echo "checked"; } ?>  disabled ></td><?php		
	}
	else
	{	
		?><td><input type = "checkbox" name = "chkActief" id= "c1" value= "1" <?php echo $row['actief'] == 1 ? 'checked' : ''; ?> 		title = "Is crediteur te gebruiken ja/nee ?"> </td><?php
	} ?>	
		<td ><input type = "submit" name= "knpUpdate" value = "Opslaan" ></td>
		<td>
					<a href='<?php echo $url; ?>Leverancier.php?pstid=<?php echo$id;?>' style = "color : blue">
			meer gegevens
			</a> <br> 
					<a href='<?php echo $url; ?>Contact.php?pstid=<?php echo$id;?>' style = "color : blue">
			contacten
			</a>  
		
		</td>
</form> 
	</td>
		
<?php	}
	if (isset ($_POST['knpUpdate']))
{
	$wijzigrelatie = "UPDATE tblRelaties SET relatie = ".$updnaam." , adres = ".$updadres." , ".$updnr." , ".$updpc." , ".$updplaats." , ".$updtel.", ".$updubn.", ".$updact." WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and relId = '$rowid' 	";
		mysqli_query($db,$wijzigrelatie) or die (mysqli_error($db));

}	?>

<td>
</td>

	</tr>
	

<?php    }   ?>     
</td>
</tr>
<tr><td colspan = 15><hr></td></tr>

<form action= "Leveranciers.php" method= "post" > 
<tr><td colspan = 2 style = "font-size:13px;"><i> Nieuwe crediteur : </i></td></tr>
<tr><td><input type= "text" name= "insubn" size = 5 value = ''></td>
<td><input type= "text" name= "inslever" value = ''></td>

<td></td>
<td><input type= "text" name= "insadres" value = ''></td>
<td><input type= "text" name= "insnr" value = '' style= "width: 30px; padding: 2px"></td>
<td width = 1></td>
<td><input type= "text" name= "inspc" value = '' style= "width: 60px;"></td>
<td width = 1></td>
<td><input type= "text" name= "insplaats" value = ''></td>
<td><input type= "text" name= "instel" value = '' style= "width: 80px;"></td>
<td><input type= "checkbox" name= "boxactief" id= "c2" <?php if(true){ echo "checked"; } ?>  disabled ></td>
<td colspan = 2><input type = "submit" name= "knpInsert" value = "Toevoegen" style = "font-size:10px;"></td></tr>
</table>
</form>
	
	

	


</TD>
<?php
include "menuBeheer.php"; } ?>
</body>
</html>
