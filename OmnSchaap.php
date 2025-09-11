<?php 

require_once("autoload.php");

$versie = '27-9-2020'; /* Gekopieerd van insOmnummeren.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>	
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Omnummeren';
$file = "OmnSchaap.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (is_logged_in()) { 

include "kalender.php";
if ($modmeld == 1 ) { include "maak_request_func.php"; }

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

?>

<script>
function verplicht() {
var levnr = document.getElementById("levnr"); 		var levnr_v = levnr.value;
var datum = document.getElementById("datepicker1");	var datum_v = datum.value;

	 if(levnr_v.length == 0) levnr.focus() 	+ alert("Het nieuwe levensnummer moet zijn ingevuld.");
else if(levnr_v.length > 0 && levnr_v.length != 12) levnr.focus() 	+ alert("Het levensnummer moet uit 12 cijfers bestaan.");
else if(isNaN(levnr_v)) levnr.focus() 	+ alert("Het levensnummer bevat een letter.");
else if(datum_v.length == 0) datum.focus() 	+ alert("De datum moet zijn ingevuld.");

}

</script>
<?php

if(!empty($_GET['pstschaap'])) 	{	$pst = $_GET['pstschaap']; 

$zoek_oud_levensnummer_obv_schaapId = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap 
WHERE schaapId = ".mysqli_real_escape_string($db,$pst)."
") or die (mysqli_error($db));

	while ( $zl = mysqli_fetch_assoc($zoek_oud_levensnummer_obv_schaapId)) { $pstnr = $zl['levensnummer']; }

//var_dump(array_keys($_GET)); 
//$velden = (array_keys($_GET));
//echo '<br>'.$velden[0];


}  else	{

//var_dump(array_keys($_POST));
$velden = (array_keys($_POST)); //echo '<br> velden na POST = '.$velden[0];

$pstnr = getIdFromKey($velden[0]); //echo '<br> $uitkomst = '.$pstnr;

}

if (isset ($_POST["knpSave_$pstnr"])) {

$levnr_old = $pstnr;

$levnr_new = $_POST["txtLevnrNew"];

$zoek_op_bestaand_levensnummer = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db,$levnr_new)."'
") or die (mysqli_error($db));

	while ( $bl = mysqli_fetch_assoc($zoek_op_bestaand_levensnummer)) { $levnr_db = $bl['schaapId']; }


if (isset($levnr_db)) 
	{
		$fout = "Dit levensnummer bestaat al.";
	}
else {

$datum = $_POST['txtDag'];
$dag = date_create($datum); $fldDay =  date_format($dag,'Y-m-d');

$zoek_stalId = mysqli_query($db,"
SELECT stalId, s.schaapId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and s.levensnummer = '".mysqli_real_escape_string($db,$levnr_old)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; $schaapId = $st['schaapId']; }

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = ".mysqli_real_escape_string($db,$stalId).", datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 17, oud_nummer = '".mysqli_real_escape_string($db,$levnr_old)."' ";

		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));


	$update_tblSchaap = "UPDATE tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$levnr_new)."' WHERE schaapId = ".mysqli_real_escape_string($db,$schaapId);

		mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));



if($modmeld == 1) {
	
$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = ".mysqli_real_escape_string($db,$stalId)." and actId = 17
") or die (mysqli_error($db));
	while ($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$Melding = 'VMD';
include "maak_request.php";
}

	} // Einde else

} // Einde if (isset ($_POST["knpSave_$pstnr"]))

unset($levnr_old);
$zoek_oud_levensnummer = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db,$pstnr)."'
") or die (mysqli_error($db));

	while ( $si = mysqli_fetch_assoc($zoek_oud_levensnummer)) { $levnr_old = $si['levensnummer']; 

//echo '<br> levensnummer uit database = '.$levnr_old;
}

 ?>
<table border = 0>
<tr> <form action="OmnSchaap.php" method = "post">
 <td width="450"></td>
 <td colspan = 12 align = 'right'><input type = "submit" onfocus = "verplicht()" name = <?php echo "knpSave_$pstnr"; ?> value = "Opslaan">&nbsp &nbsp </td>
 <td colspan = 2 > </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th width="450"></th>
 <th>Omnummer<br>datum<hr></th>
 <th width="25"></th>
 <th>Oud<hr></th>
 <th width="25"></th>
 <th>nieuw<hr></th>
</tr>


<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->
<?php if(isset($levnr_old)) {
if(isset($_POST["knpSave_$pstnr"])) { $dag = $_POST["txtDag"]; $levnr_new = $_POST["txtLevnrNew"]; } else { $dag = date('d-m-Y'); } ?>

<tr style = "font-size:14px;">
 <td width="450"></td>
 <td>
	<input type = "text" size = 7 style = "font-size : 14px;" id="datepicker1" name = <?php echo "txtDag"; ?> value = <?php echo $dag; ?> >
 </td>
 <td></td>
 <td> <?php echo $levnr_old; ?>
 <td></td>
 </td>
 <td align="center">
 	<input type = "text" size = 10 style = "font-size : 14px;" id="levnr" name = <?php echo "txtLevnrNew"; ?> value = <?php if(isset($levnr_new)) { echo $levnr_new; } ?> >
 </td>
</tr>
<?php } ?>
<!--**************************************
		**	EINDE OPMAAK GEGEVENS	**
	************************************** -->


</table>
</form> 




</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>

</body>
</html>
