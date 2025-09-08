<?php $versie = '28-12-2016'; /* Banknr gewijzigd naar IBAN, veld langer gemaakt en tonen van spaties mogelijk gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<?php
$titel = 'Relatie';
$file = "Relaties.php";
include "login.php"; ?>

				<TD valign = 'top'>
<?php
if (is_logged_in()) { 

if (empty($_GET['pstid']))
{	$pId = "$_POST[txtpId_]";	}
else
{	$pId = "$_GET[pstid]";	}

If (isset ($_POST['knpSave_']))
{	
		if (empty($_POST['txtUbn_']))	{	$updUbn = "NULL";	}  else		{	$updUbn = " '$_POST[txtUbn_]' ";	}
		if (empty($_POST['txtNaam_']))	{	$updNaam = "NULL";	}  else		{	$updNaam = " '$_POST[txtNaam_]' ";	}
		if (empty($_POST['txtBanknr_'])){	$updBank = "NULL";	}  else		{	$updBank = " '$_POST[txtBanknr_]' ";}
		
		if (empty($_POST['txtRelnr_']))	{	$updRelnr = "NULL";	}  else		{	$updRelnr = " '$_POST[txtRelnr_]' ";	}
		if (empty($_POST['txtWawo_']))	{	$updWawo = "NULL";	}  else		{	$updWawo = " '$_POST[txtWawo_]' ";	}		

  		/*if (empty($_POST['txtStraat']))	{	$updStraat = "NULL";}  else		{	$updStraat = " '$_POST[txtStraat]' ";	}
		if (empty($_POST['txtNr']))		{	$updNr = "nr = NULL";} else		{	$updNr = "nr = '$_POST[txtNr]' ";	}
		if (empty($_POST['txtPc']))		{	$updPc = "pc = NULL";} else		{	$updPc = "pc = '$_POST[txtPc]' ";	}
		if (empty($_POST['txtPlaats']))	{	$updPlaats = "NULL";}  else		{	$updPlaats = "'$_POST[txtPlaats]' ";	}*/

		if (empty($_POST['txtTel_']))	{	$updTel = "NULL";	}  else		{	$updTel = "'$_POST[txtTel_]' ";		}
		if (empty($_POST['txtFax_']))	{	$updFax = "NULL";	}  else		{	$updFax = "'$_POST[txtFax_]' ";		}
		if (empty($_POST['txtMail_']))	{	$updMail = "NULL";	}  else		{	$updMail = "'$_POST[txtMail_]' ";	}
		if (empty($_POST['txtSite_']))	{	$updSite = "NULL";	}  else		{	$updSite = "'$_POST[txtSite_]' ";	}
		/*if (empty($_POST['txtBank']))	{	$updBank = "NULL";	}  else		{	$updBank = "'$_POST[txtBank]' ";	}*/
		
		if (empty($_POST['chkActief']))	{	$updAct = "NULL";	}  else		{	$updAct = "'$_POST[chkActief]' ";	}

		if (empty($_POST['txtKent_']))	{	$updKent = "NULL";	}  else		{	$updKent = "'$_POST[txtKent_]' ";	}
		if (empty($_POST['txtHang_']))	{	$updHang = "NULL";	}  else		{	$updHang = "'$_POST[txtHang_]' ";	}

	
	$wijzigPartij = "
	update tblPartij p set ubn = ".$updUbn.", naam = ".$updNaam.", tel = ".$updTel.", fax = ".$updFax.", email = ".$updMail.", site = ".$updSite.", banknr = ".$updBank.", relnr = ".$updRelnr.", wachtw = ".$updWawo."
	where partId = $pId ";
		mysqli_query($db,$wijzigPartij) or die (mysqli_error($db));
//echo $wijzigPartij.'<br>';
	

// Wijzigen Vervoer
$zoek_vervoer = mysqli_query($db,"
	select v.vervId
	from tblVervoer v
	 join tblPartij p on (v.partId = p.partId)
	where p.partId = ".mysqli_real_escape_string($db,$pId)."
") or die(mysqli_error($db));
	while( $ve = mysqli_fetch_assoc($zoek_vervoer)) { $vervId = $ve['vervId']; }
// Invoer vervoer als deze nog niet bestaat
if(!isset($vervId) && ( !empty($_POST['txtKent_']) || !empty($_POST['txtHang_']) )) {
$insert_vervoer = "
	insert into tblVervoer
	set partId = ".mysqli_real_escape_string($db,$pId).", kenteken = ".$updKent.", aanhanger = ".$updHang."	
";
		mysqli_query($db,$insert_vervoer) or die (mysqli_error($db));
//echo $insert_vervoer.'<br>';
}
// Einde Invoer vervoer als deze nog niet bestaat
else if(isset($vervId)) {
	$wijzigVervoer = "
	update tblVervoer v
	set kenteken = ".$updKent.", aanhanger = ".$updHang."
	where partId = $pId
	";
		mysqli_query($db,$wijzigVervoer) or die (mysqli_error($db));
//echo $wijzigVervoer.'<br>';
	}
// Einde Wijzigen Vervoer

include "save_relatie.php";
}

$Partij = mysqli_query($db,"
select p.partId, r.relId, relatie, ubn, naam, tel, fax, email, site, banknr, p.relnr, p.wachtw, kenteken, aanhanger 
from tblPartij p
 join tblRelatie r on (p.partId = r.partId)
 left join tblVervoer v on (p.partId = v.partId) 
where p.partId = '$pId' ") or die (mysqli_error($db));
 while ($row = mysqli_fetch_assoc($Partij))
	{	$pId = $row['partId'];
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
 <td colspan = 4 ><input type= "hidden" name= "txtpId_" size = 1 value = <?php echo $pId; ?> > <!--hiddden-->
	  <input type= "text" name= "txtUbn_" size = 6 value = <?php echo $ubn; ?> ></td>
 <td></td>
</tr>

<tr>
 <td> Naam
 </td>
 <td colspan = 4 > <input type= "text" name= "txtNaam_" size = 60 value = <?php if(isset($naam)) { echo "'".$naam."'"; } ?> >
 </td>
</tr>
<tr>
 <td>IBAN</td>
 <td colspan = 4 > <input type= "text" name= "txtBanknr_" size = 30 value = <?php echo "'".$banknr."'"; ?> > </td>
</tr>
<tr height = 35 valign = bottom>
 <td>Relatienr</td>
 <td> <input type= "text" name= "txtRelnr_" size = 10 value = <?php echo $relnr; ?> > </td>
 <td width = 50></td>
 <td  align = right >Kenteken &nbsp </td>
 <td> <input type= "text" name= "txtKent_" size = 12 value = <?php echo $kent; ?> > </td>
</tr>
<tr>
 <td>Wachtwoord</td>
 <td> <input type= "text" name= "txtWawo_" size = 10 value = <?php echo $wawo; ?> > </td>
 <td></td>
 <td align = right >Aanhanger &nbsp </td>
 <td> <input type= "text" name= "txtHang_" size = 12 value = <?php echo $hang; ?> > </td>
</tr>
</table>

<table border = 0>
<tr>
 <td colspan = 2> <i>&nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Contactgegevens </i> </td>
</tr>
<tr>
 <td width = 100 align = right > telefoon &nbsp </td>
 <td><input type= "text" name= "txtTel_" size = 9 value = <?php if(isset($tel)) { echo $tel; } ?> > </td>
</tr>
<tr>
 <td align = right > fax &nbsp </td>
 <td> <input type= "text" name= "txtFax_" size = 9 value = <?php echo $fax; ?> > </td>
</tr>
<tr>
 <td align = right > email &nbsp </td>
 <td> <input type= "text" name= "txtMail_" size = 30 value = <?php echo $mail; ?> > </td>
</tr>
<tr>
 <td align = right > site &nbsp </td>
 <td> <input type= "text" name= "txtSite_" size = 30 value = <?php echo $site; ?> > </td>
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
$Relatie = mysqli_query($db,"
select r.relId, relatie, ubn, naam, straat, nr, pc, plaats, tel, fax, email, site, banknr, p.actief actief_p, r.actief 
from tblPartij p
 join tblRelatie r on (p.partId = r.partId)
 left join tblAdres a on (r.relId = a.relId) 
where p.partId = ".$pId." 
order by actief desc, relatie desc
") or die (mysqli_error($db));
 while ($row = mysqli_fetch_assoc($Relatie))
	{	$rId = $row['relId'];
		$rela = $row['relatie']; if($rela == 'deb') { $relatie = 'debiteur'; } else if($rela == 'cred') { $relatie = 'crediteur'; }
		$naam = $row['naam'];
		$straat = $row['straat']; $nr = $row['nr'];
		$pc = $row['pc'];
		$plaats = $row['plaats'];
		$r_actief = $row['actief'];
	 ?>

<tr>
 <td> <input type= "hidden" name= <?php echo "txtrId_$rId"; ?> size = 1 value = <?php echo $rId; ?> > <!--hiddden-->
	  <?php if(isset($relatie)) { echo $relatie; } ?> </td>
 <td> <input type= "text" name= <?php echo "txtStraat_$rId"; ?> size = 30 value = <?php if(isset($straat))  { echo "'".$straat."'"; } ?> >  </td>
 <td> <input type= "text" name= <?php echo "txtNr_$rId"; ?> 	size = 1  value = <?php if(isset($nr)) 		{ echo "'".$nr."'"; } ?> > 		</td>
 <td> <input type= "text" name= <?php echo "txtPc_$rId"; ?> style = "text-align : left;"	size = 6  value = <?php if(isset($pc)) 		{ echo "'".$pc."'"; } ?> > 		</td> 
 <td> <input type= "text" name= <?php echo "txtPlaats_$rId"; ?> size = 25 value = <?php if(isset($plaats))  { echo "'".$plaats."'"; } ?> >  </td>
 <td> <input type= "checkbox" name= <?php echo "chkActief_$rId"; ?> value = 1 <?php if($r_actief==1)  { ?> checked <?php } ?> >  </td>
 <td width = 200>  </td>
</tr>
<?php } ?>
<tr>
  <td colspan =11 align = right> <input type= "submit" name= "knpSave_" value = "Opslaan" > </td>
</tr>
</table>
</form>
<!-- Thomas bij de Belvederre station-->
</TD>
<?php
include "menuBeheer.php"; } ?>
</tr>
</table>

</body>
</html>
