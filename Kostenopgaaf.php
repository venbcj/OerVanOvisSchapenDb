<?php

require_once("autoload.php");

$versie = '19-10-2016';
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; */
$versie = '17-1-2022'; /* Btw 0% toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = center> gewijzigd naar <TD valign = "top" align = center> 31-12-24 include login voor include header gezet */

 session_start(); ?>  
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<?php
$titel = 'Inboeken kosten/opbrengsten';
$file = "Kostenopgaaf.php";
include "login.php"; ?>

		<TD valign = "top" align = "center">
<?php
if (is_logged_in()) { if($modfin ==1) {

if(isset($_POST['knpSave_'])) {
 include "save_opgaaf.php"; }
 
 
if(isset($_POST['knpInsert_'])) {
	if(isset($_POST['insLiq_'])) { $insLiq = 1; } else { $insLiq = 0; }
		$rubr =	$_POST['insRubr_'];
		$date = date_create($_POST['insDatum_']);
		 $dag =  date_format($date, 'd-m-Y');
		 $day =  date_format($date, 'Y-m-d');
		$bedrag = $_POST['insBedrag_'];
		$toel = $_POST['insToel_'];
		
	if(empty($rubr)) { $fout = "Rubriek is onbekend"; 
	 if(!empty($_POST['insDatum_'])) { $dag = $_POST['insDatum_']; }
	 if(!empty($_POST['insBedrag_'])) { $bedrag = $_POST['insBedrag_']; }
	 if(!empty($_POST['insToel_'])) { $toel = $_POST['insToel_']; }
	}
	else if(empty($date) ) { $fout = "Datum is onbekend";
	 if(!empty($_POST['insBedrag_'])) { $bedrag = $_POST['insBedrag_']; }
	 if(!empty($_POST['insToel_'])) { $toel = $_POST['insToel_']; }
	}
	else if(empty($bedrag)) { $fout = "Bedrag is onbekend"; 
	 if(!empty($_POST['insDatum_'])) { $dag = $_POST['insDatum_']; }
	 if(!empty($_POST['insToel_'])) { $toel = $_POST['insToel_']; }
	}
	else {
		
		
	$insert_Opgaaf = "INSERT INTO tblOpgaaf SET rubuId = '".mysqli_real_escape_string($db,$rubr)."', datum = '".mysqli_real_escape_string($db,$day)."', bedrag = '".mysqli_real_escape_string($db,$bedrag)."', toel = '".mysqli_real_escape_string($db,$toel)."', liq = '".mysqli_real_escape_string($db,$insLiq)."' ";
		
		/*echo '$insert_Opgaaf = '.$insert_Opgaaf.'<br>';*/ mysqli_query($db,$insert_Opgaaf) or die (mysqli_error($db));
	  }
}
else { 
	$jaar = date('Y');
	$mnd = date('m'); 
	$dag = '01-'.$mnd.'-'.$jaar;}
?>
<form action="Kostenopgaaf.php" method = "post">
<table border = 0>
<tr><th align = "center" valign = 'bottom' style = "font-size : 13px">t.b.v. liquiditeit<hr></th>
<th valign = 'bottom' >Rubriek<hr></th>
<th valign = 'bottom' align = "center" >Datum<hr></th>
<th valign = 'bottom' >Bedrag<hr></th>
<th valign = 'bottom' align = left>&nbsp&nbsp&nbsp Toelichting<hr></th></tr>
<!--*************************
	INVOERVELDEN
     ************************* --->
<tr><td width = 50 align = "center"><input type = checkbox name = 'insLiq_' value = 1 checked = 'checked' ></td>
<td>
<?php
// KzlSubrubriek nieuwe invoer
$qrySubRubriek = mysqli_query($db,"
SELECT ru.rubuId, r.rubriek
FROM tblRubriekuser ru
 join tblRubriek r on (ru.rubId = r.rubId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
ORDER BY r.rubriek
") or die (mysqli_error($db)); ?>
 <select name= "insRubr_" style= "width:200;" >
 <option></option>
<?php	while ( $sub = mysqli_fetch_array($qrySubRubriek)) 
		{ 		
			$opties = array($sub['rubuId'] => $sub['rubriek']);
			foreach ($opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['insRubr_']) && $_POST['insRubr_'] == $key)
		{
			$keuze = ' selected ';
		}
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		}
	

// Einde KzlSubrubriek nieuwe invoer
?>
 </select>
 
</td>
<td>
<input type = 'text' name = 'insDatum_' size = 8 value = <?php echo $dag; ?> ></td>
<td>&euro;<input type = 'text' name = 'insBedrag_' size = 5 value = <?php if(isset($bedrag)) { echo $bedrag; } ?> > </td>
<td><input type = 'text' name = 'insToel_' size = 45 value = <?php if(isset($toel)) { echo "'".$toel."'"; } ?> > </td>
<td colspan = 2><input type = 'submit' name = 'knpInsert_' value = "Toevoegen" > </td>
<tr>
<tr><td colspan = 25><hr></td><tr>
<!--******************************
	EINDE INVOERVELDEN
     ****************************** --->

<!--*************************
	OPMAAK VELDEN
     ************************* --->
<tr><td align = "center"></td>
<td colspan = 4 ></td>
<td width = 40 align = "center" style = "font-size : 13px">Betaald</td>
<td width = 40 align = "center" style = "font-size : 13px">Verwij-<br>deren</td>
<td> <input type = 'submit' name = 'knpSave_' value = "Opslaan" > </td><td></td></tr>	 
<?php
$zoek_inboekingen = mysqli_query($db,"
SELECT op.opgId, op.rubuId, date_format(op.datum,'%d-%m-%Y') datum, op.bedrag, op.toel, op.liq, op.his
FROM tblOpgaaf op
 join tblRubriekuser ru on (op.rubuId = ru.rubuId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (isnull(op.his) or op.his =0)
") or die (mysqli_error($db));
	while ( $zi = mysqli_fetch_assoc($zoek_inboekingen)) {
		$Id = $zi['opgId'];
		$rubuId = $zi['rubuId'];
		$datum = $zi['datum'];
		$bedrag = $zi['bedrag'];
		$toel = $zi['toel']; 
		$liq = $zi['liq']; 
		$his = $zi['his']; 
		
		if(isset($POST_["chbLiq_$Id"])) { $liq = $POST_["chbLiq_$Id"]; } ?>


<tr>
 <td width = 50 align = "center">
<?php /*echo $Id;*/ ?>
	<input type = checkbox name = <?php echo "chbLiq_$Id"; ?> value = 1 <?php echo $liq == 1 ? 'checked' : '';  ?> >
 <td>
<?php
// KzlSubrubriek
$qrySubRubriek = mysqli_query($db,"SELECT ru.rubuId, r.rubriek FROM tblRubriekuser ru join tblRubriek r on (ru.rubId = r.rubId) WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1 ORDER BY r.rubriek ") or die (mysqli_error($db)); 

$index = 0;
	while ( $sub = mysqli_fetch_array($qrySubRubriek)) 
	{
	   $rub_Id[$index] = $sub['rubuId'];
	   $rubri[$index] = $sub['rubriek'];
	   $rubRaak[$index] = $rubuId;
	   $index++; 
    }

?>
 <select name= <?php echo "kzlRubr_$Id"; ?> style= "width:200;" >
 <option></option>
<?php	$count = count($rub_Id);
for ($i = 0; $i < $count; $i++){ 
		
		
			$opties = array($rub_Id[$i] => $rubri[$i]);
			foreach ($opties as $key => $waarde)
			{
						$keuze = '';
		
		if( (!isset($_POST['knpSave_']) && $rubRaak[$i] == $key) || ( isset($_POST["kzlRubr_$Id"]) && $_POST["kzlRubr_$Id"] == $key) )
		{
			echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
		}
		else
		{		
		echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
}
	

// Einde KzlSubrubriek
?>
 </td>
 <td>
 	<input type = text name = <?php echo "txtDatum_$Id"; ?> size = 8 value = <?php echo $datum; ?> >
 </td>
 <td><?php echo "&euro;"; ?>
	<input type = text name = <?php echo "txtBedrag_$Id"; ?> size = 5 style="text-align : right"; value = <?php echo $bedrag; ?> >
 </td>
 <td>
 	<input type = text name = <?php echo "txtToel_$Id"; ?> size = 45 value = <?php echo "'"."$toel"."'"; ?> >
 </td>
 <td width = 40 align = "center">
	<input type = checkbox name = <?php echo "chbArch_$Id"; ?> value = 1 <?php if(isset($his)) { echo $his == 1 ? 'checked' : ''; } ?> ></td>
<td width = 40 align = "center">
 <input type = checkbox name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> ></td>
<td width = 10></td></tr>
<!--******************************
	EINDE OPMAAK VELDEN
     ****************************** --->

<?php	}
?>


</table>
</form>
	</TD>
<?php } else { ?> <img src='kostenopgaaf_php.jpg'  width='970' height='550'/> <?php }
include "menuFinance.php"; } ?>
</tr>

</table>

</body>
</html>
