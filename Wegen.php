<?php /*13-3-2015 : Login toegevoegd */
$versie = '3-4-2018'; /* : Tussenweging toegevoegd bij UpdatSchaap.php */
$versie = '16-6-2018'; /* : Kalender toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */

 session_start(); ?>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<center>
<?php
include"kalender.php";
$titel = 'Tussenwegingen';
$subtitel = '';
Include "header.php"; ?>

		<TD width = 960 height = 400 valign = "top" > <?php
If(empty($_GET['pstId']))  { $schaapId = $_POST['txtlevnr']; } else { $schaapId = "$_GET[pstId]"; }

 ?>
<table border = 0 valign = "top" ><tr><td valign = "top"> <!-- table 1 -->
<?php
$file = "Wegen.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 
if (isset($_POST['knpSave']))
{
// Controle 1 x per dag mag een schaap een tussenweging doen
$date = date_create($_POST['txtdatum']);
		$datum = date_format($date, 'Y-m-d');
		
$dagwegingen = mysqli_query($db,"
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." and datum = '".mysqli_real_escape_string($db,$datum)."' and h.actId = 9
") or die (mysqli_error($db));

	$row = mysqli_fetch_assoc($dagwegingen);  $rows_dag = $row['aant'];

$zoek_laatste_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." and schaapId = ".mysqli_real_escape_string($db,$schaapId)." 
") or die (mysqli_error($db));

while ($rij = mysqli_fetch_array($zoek_laatste_stalId))
		{ $stalId =  "$rij[stalId]"; }

$eerste_datum_schaap = /*Is maxdatum van laatste stalId !! bijv. als aangekocht én geboortedatum bestaat !!*/ mysqli_query($db," 
SELECT max(datum) datumfirst, date_format(max(datum),'%d-%m-%Y') datum1
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = ".mysqli_real_escape_string($db,$stalId)." and (h.actId = 1 or h.actId = 2 or h.actId = 11)
") or die (mysqli_error($db));

	$lijn = mysqli_fetch_assoc($eerste_datum_schaap);  $day1 = $lijn['datumfirst']; $dag1 = $lijn['datum1'];

$laatste_datum_schaap = mysqli_query($db,"
SELECT max(datum) datumend, date_format(max(datum),'%d-%m-%Y') enddatum
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = ".mysqli_real_escape_string($db,$stalId)." and (h.actId = 10 or h.actId = 12 or h.actId = 13 or h.actId = 14)
") or die (mysqli_error($db));

	$lst = mysqli_fetch_assoc($laatste_datum_schaap);  $endday = $lst['datumend']; $enddag = $lst['enddatum'];	


	 if(empty($_POST['txtdatum']) && !empty($_POST['txtgram']))	{ $fout = "De weegdatum is niet ingevuld"; 
	$kg = $_POST['txtgram']; }
else if(empty($_POST['txtgram']) && !empty($_POST['txtdatum'])) { $fout = "Het weeggewicht is niet ingevuld";
	$dag = $_POST['txtdatum']; }
else if ($rows_dag > 0) 		{ $fout = "Een schaap kan maar 1 keer per dag worden gewogen.";
	$kg = $_POST['txtgram']; }
else if (!empty($_POST['txtdatum']) && !empty($_POST['txtgram']) && $rows_dag == 0 )
		{
		
		$date = date_create($_POST['txtdatum']);
		$datum = date_format($date, 'Y-m-d');

	 if($datum < $day1)		{ $fout = "De datum mag niet voor ".$dag1." liggen.";
	$kg = $_POST['txtgram']; }
else if(isset($endday) && $datum > $endday)		{ $fout = "De datum mag niet na ".$enddag." liggen.";
	$kg = $_POST['txtgram']; }
else {
$newkg = $_POST["txtgram"];

$query_wegen_invoeren = "INSERT INTO tblHistorie SET stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$datum)."', kg = '".mysqli_real_escape_string($db,$newkg)."', actId = 9 ";
/*echo $query_wegen_invoeren.'<br>';*/		mysqli_query($db,$query_wegen_invoeren) or die (mysqli_error($db));
	}
		}
} ?>
<form action="Wegen.php" method = "post">
<table border = 0 valign = "top"> <?php // table 2
$weeg = mysqli_query($db,"
SELECT s.schaapId, s.levensnummer 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId) 
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and st.schaapId = $schaapId
") or die (mysqli_error($db));

		while ($row = mysqli_fetch_array($weeg))
		{
		$schaapId =  $row['schaapId'];
		$levnr =  $row['levensnummer']; ?>	

	
<tr>
 <td colspan = 4 align = "left"><i style = "font-size:14px;"> Levensnummer :</i> <b style = "font-size:15px;"> <?php echo $levnr; ?> </b></td>
 <td> <input type = "hidden" name ="txtlevnr" value= <?php echo $schaapId; ?> > </td> <!--hiddden-->
</tr>
<?php		} ?>

<tr><td height = 40></td></tr>
<tr>
 <td align = "right">Datum : </td>
 <td></td>
 <td><input type="text" id = "datepicker1" name = "txtdatum" size = 9 value= <?php if(isset($dag)) { echo $dag; } ?>></td>
 <td></td>
 <td>Gewicht : </td>
 <td></td>
 <td><input type="text" name = "txtgram" size = 9 value = <?php if(isset($kg)) { echo $kg; } ?> ></td>
</tr>
<tr><td colspan = 8 align = "center"><input type = "submit" name = "knpSave" value = "Opslaan"> </td>	   
</tr>
</table></td> <!-- EInde table 2 -->

<td valign = "top"> <table border = 0 > <!-- table 3 -->
<tr>
 <td></td>
 <td colspan = 4 align = "center"> Eerdere wegingen </td></tr>
<tr style = "font-size:12px;">
 <th width = 50 height = 30></th>
 <th width= 120 style = "text-align:center;"valign="bottom">weegnr<hr></th>
 <th width = 1></th>
 <th width= 120 style = "text-align:center;"valign="bottom">Datum weging<hr></th>
 <th width = 1></th>
 <th width= 120 style = "text-align:center;"valign="bottom">Gewicht<hr></th>
 <th width = 0></th>
</tr>
<?php
$weegaantal = mysqli_query($db,"
SELECT count(hisId) aant
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and st.schaapId = $schaapId and h.actId = 9
") or die (mysqli_error($db));

		while ($aa = mysqli_fetch_array($weegaantal))
		{ $aantal = "$aa[aant]"; }

$weeg = mysqli_query($db,"
SELECT datum, kg
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and st.schaapId = $schaapId and h.actId = 9
ORDER BY datum desc
") or die (mysqli_error($db));

		while ($row = mysqli_fetch_array($weeg))
		{
			$date = date_create($row['datum']);
		$weegdm = date_format($date, 'd-m-Y');
		$weegkg = "$row[kg]";
?>
		
				
<tr align = "center">	
 <td width = 0> </td>
 <td width = 120 style = "font-size:15px;" >  <?php echo $aantal; $aantal = $aantal-1; ?> <br> </td>
 <td width = 1> </td>
 <td width = 120 style = "font-size:15px;" >  <?php echo $weegdm; ?> <br> </td>
 <td width = 1> </td>
 <td width = 120 style = "font-size:15px;">  <?php echo $row['kg']; ?> <br> </td>
 <td width = 1> </td>
</tr>				
<?php		} ?>
</table></td></tr> <!-- Einde table 3-->
		
</tr>				
</table> <!-- Einde table 1 -->
</form>

		</TD>
<?php
Include "menu1.php"; } ?>

</body>
</html>
