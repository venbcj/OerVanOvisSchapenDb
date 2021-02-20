<?php 
$versie = '6-2-2019'; /* Vaderdier is tot een jaar terug te kiezen */
$versie = '11-7-2020'; /* Gegevens zijn ook langer dan het laatste half jaar zichtbaar. Als volwId is opgeslagen in tblSchaap kan het record niet meer worden verwijderd. */
session_start(); ?>  
<html>
<head>
<title>Registratie</title>
<?php include"kalender.php"; ?>
</head>
<body>

<center>
<?php
$titel = 'Dracht';
$subtitel = '';
Include "header.php";?>
<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Dracht.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech == 1) {

include "vw_kzlOoien.php";

// Declaratie vaderdier
$resultvader = mysqli_query($db,"
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblStal st 
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = ".mysqli_real_escape_string($db,$lidId)."
and not exists (
	SELECT stal.schaapId
	FROM tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	WHERE stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = ".mysqli_real_escape_string($db,$lidId).")
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); 

$index = 0; 
while ($va = mysqli_fetch_array($resultvader)) 
{ 
   //$vaId[$index] = $va['schaapId']; 
   $vawerknr[$index] = $va['werknr'];
   $vaRaak[$index] = $va['schaapId'];   
   $index++; 
} 
unset($index);
// EINDE Declaratie vaderdier

	if (isset ($_POST['knpSave_']))
{
	$dag = $_POST['txtDatum_']; 
		if(empty($dag)) { $dag = date('d-m-Y'); } #echo 'Datum :'.$dag.'<br>'; 
		$date = date_create($dag);
		$day =  date_format($date, 'Y-m-d');   #echo 'Datum database : '.$day.'<br>';
	$mdrId = $_POST['kzlOoi_']; if(empty($mdrId)) { $mdrId = 'NULL'; } #echo 'Moeder : '.$mdrId.'<br>';
	$vdrId = $_POST['kzlRam_']; if(empty($vdrId)) { $vdrId = 'NULL'; } #echo 'Vader : '.$vdrId.'<br>';
	if(isset($_POST['radDracht_'])) {
	$dracht = $_POST['radDracht_']; } else { $dracht = 'NULL'; } 	   #echo 'Dracht : '.$dracht.'<br><br>';



if ($mdrId <> 'NULL' && $vdrId <> 'NULL') {

$zoek_combi_moeder_met_vader = mysqli_query($db,"
SELECT date_add(max(datum),interval 183 day) datum
FROM tblVolwas
WHERE mdrId = ".mysqli_real_escape_string($db,$mdrId)." and vdrId = ".mysqli_real_escape_string($db,$vdrId)." 
") or die (mysqli_error($db));

	while ( $vw = mysqli_fetch_assoc ($zoek_combi_moeder_met_vader)) { $dmmax_mdr_met_vdr = $vw['datum']; } // Datum dat moeder weer drachtig kan zijn

$zoek_laatste_drachtdatum_van_moeder = mysqli_query($db,"
SELECT date_add(max(datum),interval 183 day) datum
FROM tblVolwas
WHERE mdrId = ".mysqli_real_escape_string($db,$mdrId)." 
") or die (mysqli_error($db));

	while ( $vw = mysqli_fetch_assoc ($zoek_laatste_drachtdatum_van_moeder)) { $dmmax = $vw['datum']; } // Datum dat moeder weer drachtig kan zijn

$vandaag = date('Y-m-d');
#echo $dmmax_mdr_met_vdr.'<br>';
#echo $vandaag.'<br>';

if(isset($dmmax_mdr_met_vdr) && $dmmax_mdr_met_vdr > $vandaag) { $fout = "Deze combinatie bestaat al binnen de afgelopen half jaar. "; }
else if(isset($dmmax) && $dmmax > $vandaag) { $fout = "Deze moeder is al drachtig geweest binnen de afgelopen half jaar . "; }
else {


$insert_tblVolwas = "INSERT INTO tblVolwas set datum = '".mysqli_real_escape_string($db,$day)."', mdrId = ".mysqli_real_escape_string($db,$mdrId).", vdrId = ".mysqli_real_escape_string($db,$vdrId).", drachtig = ".mysqli_real_escape_string($db,$dracht)." ";
/*echo $insert_tblVolwas;*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));
	  }

			} // Einde ($mdrId <> 'NULL' && $vdrId <> 'NULL')

			else { $fout = "Moederdier of vaderdier is onbekend."; }

} // Einde if (isset ($_POST['knpSave_']))

if(isset($_POST['knpUpdate_'])) { include"save_dracht.php"; }
?>	
<form action = "Dracht.php" method = "post" >
<table border = 0>

<tr>
 <th width="180">Invoer</th>
 <th width="100">Datum<hr></hr></th>
 <th width="100">Ooi<hr></hr></th>
 <th width="100">Ram<hr></hr></th>
 <th width="80">Drachtig<hr></hr></th>
 <th></th>
 <th></th>
</tr>

<tr> 
 <td colspan="2"> </td>	
 <td style = "font-size:10px;"><i> Werknr - lammeren - halsnr </i>
 </td> 
</tr>

<tr>
 <td></td>
 <td align="center">
 	<input type="text" id="datepicker2" name="txtDatum_" size = 8 value = <?php if(isset($dag)) { echo $dag; } else { echo date('d-m-Y'); } ?> >
 </td>
 <td align="center"> 
<?php
$result = mysqli_query($db,"(".$vw_kzlOoien.")  ") or die (mysqli_error($db)); ?>
	 <select name= "kzlOoi_" style= "width:65;" >
 <option></option>	
<?php	while($row = mysqli_fetch_array($result))
		{
			$opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['lamrn'].'&nbsp &nbsp '.$row['halsnr']);
			foreach ( $opties as $key => $waarde)
			{
						$keuze = '';
		
		if(isset($_POST['kzlOoi_']) && $_POST['kzlOoi_'] == $key)
		{
			$keuze = ' selected ';
		}
				
		echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
			}
		
		} ?>
 </select>
 </td>
 <td align="center"> 
 <select name= "kzlRam_" style= "width:65;" >
 <option></option>	
<?php	$count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
		
	$opties= array($vaRaak[$i]=>$vawerknr[$i]);
			foreach ( $opties as $key => $waarde)
			{
	if(($vdrId == $vaRaak[$i]) || (isset($_POST['kzlRam_']) && $_POST['kzlRam_'] == $key)) {
		echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
		else
		{
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select>
 </td>
 <td>
 	<input type = radio name = 'radDracht_' value = 1
		<?php if(isset($_POST['radDracht_']) && $_POST['radDracht_'] == 1 ) { echo "checked"; } ?> > Ja
	 <input type = radio name = "radDracht_" value = 0
		<?php if(!isset($_POST['radDracht_'])) { echo "checked"; } 
		 else if(isset($_POST['radDracht_']) && $_POST['radDracht_'] == 0 ) { echo "checked"; } ?> > Nee 
 </td>
</tr>
<tr height = 50>
 <td></td>
 <td colspan = 4 align="center">
	<input type="submit" name="knpSave_" value="Opslaan"> 	
 </td>
</tr>
<tr><td colspan = 10><hr></hr></td></tr>

<?php 
	 if(isset($_POST['knpShowHis_']) ) { $toonhis = 1; }
else if(isset($_POST['knpHideHis_']) ) { $toonhis = 0; } 
else if(isset($_POST['txtHis_'])) { $toonhis = $_POST['txtHis_']; } 
else { $toonhis = 0; } ?>
 	

<tr>
<?php if($toonhis == 0) { ?>
 <td colspan = 10 align="center"> <h3> Drachtperiode per ooi van het laatste half jaar </h3> </td>
<?php } else { ?>
 <td colspan = 10 align="center"> <h3> Alle drachtperiodes per ooi </h3> </td>
<?php } ?>
 <td> <?php if($toonhis == 1) { ?> 
 	<input type="submit" name= <?php echo "knpHideHis_"; ?> value = "Verberg" >
 	 <?php } else { ?>
 	<input type="submit" name= <?php echo "knpShowHis_"; ?> value = "Toon" > 
 	 <?php } ?> historie 
 	 <input type="hidden" name="txtHis_" value= <?php echo $toonhis; ?> >
 </td>
</tr>
<tr>
 <th width="180">Overzicht</th>
 <th width="100">Datum<hr></hr></th>
 <th width="100">Ooi<hr></hr></th>
 <th width="100">Ram<hr></hr></th>
 <th width="80">Drachtig<hr></hr></th>
 <th width="80">Verwijderen<hr></hr></th>
 <th width="180"> <input type="submit" name="knpUpdate_" value="Opslaan"></th>
</tr>
<?php
if($toonhis == 1) {
$where = " ";
} else {
$where = " and date_add(datum,interval 183 day) > CURRENT_DATE() ";
}

$zoek_drachtigen = mysqli_query($db,"
SELECT v.volwId, date_format(datum,'%d-%m-%Y') datum ,right(mdr.levensnummer,$Karwerk) mdr, v.vdrId vdrId , coalesce(drachtig,0) drachtig, lam.schaapId lamId
FROM tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join tblStal stv on (stv.schaapId = vdr.schaapId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
WHERE drachtig is not null and stm.lidId = ".mysqli_real_escape_string($db,$lidId)." and (stv.lidId = ".mysqli_real_escape_string($db,$lidId)." or isnull(stv.lidId)) ".$where. "
ORDER BY right(mdr.levensnummer,$Karwerk)
") or die (mysqli_error($db));
while ($dr = mysqli_fetch_assoc($zoek_drachtigen)) {
	$Id = $dr['volwId'];
	$datum = $dr['datum'];
	$moeder = $dr['mdr'];
	$vaderId = $dr['vdrId'];
	$drachtig = $dr['drachtig']; 
	$lam = $dr['lamId']; ?>

<tr>
 <td></td>
 <td align="center"><?php echo $datum; ?></td>
 <td align="center"><?php echo $moeder; ?></td>
 <td align="center">
 	<select name= <?php echo "kzlRamUpd_$Id"; ?> style= "width:65;" >
 <option></option>	
<?php	$count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
		
	$opties= array($vaRaak[$i]=>$vawerknr[$i]);
			foreach ( $opties as $key => $waarde)
			{
	if(($vaderId == $vaRaak[$i]) || (isset($_POST["kzlRamUpd_$Id"]) && $_POST["kzlRamUpd_$Id"] == $key)) {
		echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
		else
		{
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select></td>
 <td align="center"> <!-- Keuzelijst drachtig -->
 	<select name = <?php echo "kzlDrachtUpd_$Id"; ?> style = "width:60; font-size:13px;">
<?php  
$opties = array(1 => 'Ja', 0 => 'Nee');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpUpdate_']) && $drachtig == $key) || (isset($_POST["kzlDrachtUpd_$Id"]) && $_POST["kzlDrachtUpd_$Id"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select>
<!-- Einde Keuzelijst drachtig -->
 </td>
 <td width="80" align="center">
<?php if(!isset($lam)) { ?>
 	<input type="checkbox" name= <?php echo "chbDel_$Id"; ?> >
<?php } ?>
 </td>
 <td></td>
</tr>
<?php }
?>
</table>



</form>

</TD>
<?php
Include "menu1.php"; } 
} // Einde if($modtech == 1) ?>
</tr>

</table>
</center>



</body>
</html>
