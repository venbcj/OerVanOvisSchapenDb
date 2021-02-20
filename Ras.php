<?php
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is */
$versie = '8-3-2015'; /*Login toegevoegd*/ 
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer t.b.v. reader Agrident aangepast. Hidden velden scan en actief verwijderd */
$versie = '30-5-2020'; /* function db_null_input toegevoegd en pagina opgebouwd/ingedeeld als Hok.php */
session_start(); ?>
<html>
<head>
<title>Beheer</title>
</head>
<body>

<center>
<?php
$titel = 'Invoer rassen';
$subtitel = '';
Include "header.php"; ?>

			<TD width = 960 height = 400 valign = "top">
<?php
$file = "Ras.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

if (isset ($_POST['knpSave_'])) { include "save_ras.php"; }

if (isset ($_POST['knpInsert_']))
{

/*if($reader == 'Agrident') { // Agrident scannr n.v.t.
// Zoek naar sortnr op duplicaten
$zoek_sort = mysqli_query($db,"
SELECT count(sort) sort
FROM tblRasuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and " . db_null_filter('sort', $_POST['insSort_']) . " and sort is not null
");

	while( $zs = mysqli_fetch_assoc($zoek_sort)) { $aantso = $zs['sort']; }
}
else*/ if($reader == 'Biocontrol') { // Biocontrol sortering n.v.t.
// Zoek naar scannr op duplicaten	
$zoek_scan = mysqli_query($db,"
SELECT count(scan) scan
FROM tblRasuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and ". db_null_filter('scan', $_POST['insScan_']) ." and scan is not null
") or die (mysqli_error($db));
	while( $zs = mysqli_fetch_assoc($zoek_scan)) { $aantsc = $zs['scan']; }
} // Einde Biocontrol


	if (empty($_POST['kzlRas_']))			{ $fout = "U heeft geen ras geselecteerd."; }	
	else if( isset($aantsc) && $aantsc > 0)	{ $fout = "De scancode bestaat al."; }
	//else if( isset($aantso) && $aantso > 0)	{ $fout = "Dit sorteringsnummer bestaat al."; }
	else 
	{
		
$query_ras_toevoegen = "
  INSERT INTO tblRasuser
  SET lidId = '".mysqli_real_escape_string($db,$lidId)."',
      rasId = '".mysqli_real_escape_string($db,$_POST['kzlRas_'])."',
      scan  = ".db_null_input($_POST['insScan_']).",
      sort  = ".db_null_input($_POST['insSort_']);

				/*echo $query_ras_toevoegen;*/ mysqli_query($db,$query_ras_toevoegen) or die (mysqli_error($db));
	}
}

$zoek_rasuId = mysqli_query($db,"
SELECT rasuId
FROM tblRasuser
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

	while($line = mysqli_fetch_assoc($zoek_rasuId))
	{
            $pdf = $line['rasuId']; 
    } ?>

<form action="Ras.php" method="post">
<table border = 0>
<tr>
 <td width = 450 valign = 'top'>
<table border = 0>
<tr>
 <td>
 	<?php if($reader == 'Agrident') { $kop = 'sortering reader'; } else { $kop = 'code tbv reader'; }  ?>
 	<b> Nieuw ras :</b> <td align = center width = 10 style ="font-size:12px;"> <b> <?php echo $kop; ?> </b>
 </td>
 </td>
</tr>
<tr>
  <td>
<!-- KZLRAS -->
<?php

$qryRas = mysqli_query($db,"
SELECT r.rasId, r.ras
FROM tblRas r 
 left join tblRasuser ru on (ru.rasId = r.rasId and ru.lidId = '".mysqli_real_escape_string($db,$lidId)."')
WHERE isnull(ru.rasId) and r.actief = 1
ORDER BY r.ras
 ") or die (mysqli_error($db));?>
 <select style="width:180;" name="kzlRas_" value = "" style = "font-size:12px;">
  <option></option>
<?php		while($rs = mysqli_fetch_array($qryRas))
		{
			$raak = $rs['rasId'];

			$opties= array($rs['rasId']=>$rs['ras']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if( (!isset($_POST['knpInsert_']) && $rasId == $raak) || (isset($_POST['kzlRas_']) && $_POST['kzlRas_'] == $key) )
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		}
		else
		{		
		echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			
		}
}
?>
</select>
<!-- EINDE KZLRAS -->
 </td>
  <td> <?php if($reader == 'Agrident') { ?>
	<input type= "text" name= "insSort_" size = 1 title = "Leg hier het nummer vast om de volgorde in de reader te bepalen." > 
<?php } else { ?> 
	<input type= "text" name= "insScan_" size = 1 title = "Leg hier de code vast die u tijdens het scannen met de reader gaat gebruiken." value = <?php if(isset($txtScan)) { echo $txtScan; } ?> >
<?php } ?>
 </td>
 <td colspan = 3 align = center><input type = "submit" name="knpInsert_" value = "Toevoegen" > </td>
</tr>
</table>

 </td>
 <td>
<table border = 0 align = 'left' >
<tr>
 <td> <b> Rassen</b> </td>
 <td align = center style ="font-size:12px;"> <?php echo $kop; ?> </td>
 <td align = center style ="font-size:12px;"> in gebruik </td>
 <td> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> </td>
 <td width= 200 align = "right">
 	<a href= '<?php echo $url;?>Ras_pdf.php?Id=<?php echo $pdf; ?>' style = 'color : blue'>
	print pagina </a> &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
 </td>
</tr>
<tr>
 <td colspan = 5><hr> </td>
</tr>


<?php
// START LOOP
$query = mysqli_query($db,"
SELECT r.rasId, r.ras, ru.scan, ru.sort, ru.actief 
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1
ORDER BY coalesce(sort,r.rasId + 500), ras ") or die (mysqli_error($db));
	while($rij = mysqli_fetch_assoc($query))
	{ 
		$Id = $rij['rasId'];
		$ras = $rij['ras'];
		$scan = $rij['scan'];
		$sort = $rij['sort'];
		$actief = $rij['actief'];		
		?>

<tr>
 <td> <?php echo $ras; ?> </td>
 <td width = 100 align = center>
<?php if ($reader == 'Agrident') { ?>
	<input type = text name = <?php echo "txtSort_$Id"; ?> size = 1 value = <?php echo $sort; ?>  >
<?php } else { ?>
	<input type = text name = <?php echo "txtScan_$Id"; ?> size = 1 title = "Wijzig hier de code die u tijdens het scannen met de reader gaat gebruiken." value = <?php echo $scan; ?>  > <?php } ?>
 </td>
 <td>
	<input type = hidden name = <?php echo "chbActief_$Id"; ?> value = 0 > <!-- hiddden -->
	<input type = "checkbox" name = <?php echo "chbActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?> 		title = "Is dit ras te gebruiken ja/nee ?"/>
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
Include "menuBeheer.php"; } ?>
</body>
</html>
