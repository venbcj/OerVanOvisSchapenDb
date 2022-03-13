<?php 
$versie = '6-2-2019'; /* Vaderdier is tot een jaar terug te kiezen */
$versie = '11-7-2020'; /* Gegevens zijn ook langer dan het laatste half jaar zichtbaar. Als volwId is opgeslagen in tblSchaap kan het record niet meer worden verwijderd. */
$versie = '25-12-2021'; /* Pagina hernoemd van Dracht.php naar Dekkingen.php. */
session_start(); ?>  
<html>
<head>
<title>Registratie</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <style type="text/css">
        .selectt {
           /* color: #fff;
            padding: 30px;*/
            display: none;
            /*margin-top: 30px;
            width: 60%;
            background: grey;*/
            font-size: 12px;
        }
    </style>

<?php include"kalender.php"; ?>
</head>
<body>

<center>
<?php
$titel = 'Dekkingen / Dracht';
$subtitel = '';
Include "header.php";?>
<TD width = 960 height = 400 valign = "top" >
<?php
$file = "Dekkingen.php";
Include "login.php";
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech == 1) {

include "vw_kzlOoien.php"; ?>

<script type="text/javascript">

function toon_txtDatum(id, datum, aantal) {

var txtDrachtdm = 'drachtdatum_' + id;
var kzlDrachtig = 'drachtig_' + id;
var txtWorp = 'worp_' + id;

dracht = document.getElementById(kzlDrachtig);		var dr = dracht.value;

// if(mr.length > 0) alert(jArray_vdr[mr]);
  if(dr == 'ja') {

  	document.getElementById(txtDrachtdm).style.display = "inline-block";
  	document.getElementById(txtDrachtdm).value = datum;
  	document.getElementById(txtWorp).style.display = "inline-block";
  	if(aantal > 0) {
  	document.getElementById(txtWorp).value = aantal;
  	}

  }
  else
  {
  	document.getElementById(txtDrachtdm).style.display = "none";
  	document.getElementById(txtDrachtdm).value = null;
  	document.getElementById(txtWorp).style.display = "none";
  	document.getElementById(txtWorp).value = null;
  }

}



</script>


<?php
// Declaratie vaderdier
$resultvader = mysqli_query($db,"
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblStal st 
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
and not exists (
	SELECT stal.schaapId
	FROM tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie  a on (a.actId = h.actId)
	WHERE stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."')
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

	if (isset ($_POST['knpInsert_']))
{
	if(!empty($_POST['txtDatum_'])) {
		$dag = $_POST['txtDatum_']; 
		if(empty($dag)) { $dag = date('d-m-Y'); } #echo 'Datum :'.$dag.'<br>'; 
		$date = date_create($dag);
		$day =  date_format($date, 'Y-m-d');   #echo 'Datum database : '.$day.'<br>';
	}
	if(!empty($_POST['kzlWat_'])) { $registratie = $_POST['kzlWat_']; }
	if(!empty($_POST['kzlOoi_'])) { $mdrId = $_POST['kzlOoi_']; } #echo 'Moeder : '.$mdrId.'<br>';
	if(!empty($_POST['kzlRamNew_'])) { $vdrId = $_POST['kzlRamNew_']; } #echo 'Vader : '.$vdrId.'<br>';
	if(isset($_POST['txtWorp_'])) { $txtGrootte = $_POST['txtWorp_']; }  #echo 'Dracht : '.$dracht.'<br><br>';




if (isset($day) && isset($registratie) && isset($mdrId)) {

$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$mdrId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ( $zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $zs['stalId']; }

// Controle op dubbele invoer achter elkaar en dekking binnen 183 dagen

$zoek_183dagen_na_laatste_worp = mysqli_query($db,"
SELECT date_add(max(h.datum),interval 183 day) datum
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE mdrId = '".mysqli_real_escape_string($db,$mdrId)."' and h.actId = 1
") or die (mysqli_error($db));

	while ( $vw = mysqli_fetch_assoc ($zoek_183dagen_na_laatste_worp)) { $vroegst_volgende_dekdatum = $vw['datum']; } // Datum dat moeder weer drachtig kan zijn

$zoek_laatste_koppel_na_laatste_worp_obv_moeder = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join tblHistorie hv on (hv.hisId = v.hisId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
WHERE (isnull(hv.skip) or hv.skip = 0) and isnull(lam.volwId) and v.mdrId = '".mysqli_real_escape_string($db,$mdrId)."'
") or die (mysqli_error($db));

	while ( $lk = mysqli_fetch_assoc ($zoek_laatste_koppel_na_laatste_worp_obv_moeder)) { $koppel = $lk['volwId']; } //Laatste_koppel_zonder_worp

$zoek_moeder_vader_uit_laatste_koppel = mysqli_query($db,"
SELECT mdrId, vdrId, v.hisId his_dek, d.hisId his_dracht
FROM tblVolwas v
 left join tblDracht d on (d.volwId = v.volwId) 
 left join tblHistorie hd on (hd.hisId = d.hisId)
WHERE (isnull(hd.skip) or hd.skip = 0) and v.volwId = '".mysqli_real_escape_string($db,$koppel)."'
") or die (mysqli_error($db));

	while ( $v_m = mysqli_fetch_assoc ($zoek_moeder_vader_uit_laatste_koppel)) { 
		$lst_mdr = $v_m['mdrId']; 
		$lst_vdr = $v_m['vdrId']; 
		$dekMoment = $v_m['his_dek']; 
		$drachtMoment = $v_m['his_dracht']; }

/*echo 'laatste koppel = '.$koppel.'<br>';
echo '$lst_mdr = '.$lst_mdr.' keuze mdr = '.$mdrId.'<br>' ;
echo '$lst_vdr = '.$lst_vdr.' keuze vdr = '.$vdrId.'<br>' ;*/


if($lst_mdr == $mdrId) {
if(isset($dekMoment) && $lst_vdr == $vdrId && isset($vdrId)) {

$zoek_dekdatum = mysqli_query($db,"
SELECT date_format(datum,'%d-%m-%Y') datum, year(datum) jaar
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($db,$dekMoment)."' and skip = 0
") or die (mysqli_error($db));
	while ( $zd = mysqli_fetch_assoc($zoek_dekdatum)) { $dekdm = $zd['datum']; $dekjaar = $zd['jaar']; }

	$fout = "Deze ram heeft deze ooi reeds als laatste gedekt en wel op ".$dekdm.". ";

	if($registratie == 'dracht') { $fout .= " Wijzig de dekking uit ".$dekjaar."."; }
}
if(isset($drachtMoment)) {

$zoek_drachtdatum = mysqli_query($db,"
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($db,$drachtMoment)."' and skip = 0
") or die (mysqli_error($db));
	while ( $zd = mysqli_fetch_assoc($zoek_drachtdatum)) { $drachtdm = $zd['datum']; }

	$fout = "Deze ooi is reeds drachtig per ".$drachtdm.". ";
}


}

else if(isset($vroegst_volgende_dekdatum) && $vroegst_volgende_dekdatum > $day) { $fout = "Deze ooi is heeft binnen het laatste half jaar nog geworpen. "; }
// Einde Controle op dubbele invoer achter elkaar en dekking binnen 183 dagen

if(!isset($fout) && $registratie == 'dekking') {

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$day)."', actId = 18 ";	
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 18 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ( $zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$insert_tblVolwas = "INSERT INTO tblVolwas set hisId = '".mysqli_real_escape_string($db,$hisId)."', mdrId = '".mysqli_real_escape_string($db,$mdrId)."', vdrId = " . db_null_input($vdrId);
/*echo $insert_tblVolwas;*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));
	  }

else if(!isset($fout) && $registratie == 'dracht') {

$insert_tblVolwas = "INSERT INTO tblVolwas set mdrId = '".mysqli_real_escape_string($db,$mdrId)."', vdrId = " . db_null_input($vdrId) . ", grootte = " . db_null_input($txtGrootte) ;
/*echo $insert_tblVolwas;*/		mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));

$zoek_volwId = mysqli_query($db,"
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = '".mysqli_real_escape_string($db,$mdrId)."' and " . db_null_filter(vdrId, $vdrId) . "
") or die (mysqli_error($db));
	while ( $zv = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $zv['volwId']; }

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$day)."', actId = 19 ";	
/*echo $insert_tblHistorie.'<br>';*/		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 19 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
	while ( $zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$insert_tblDracht = "INSERT INTO tblDracht SET volwId = '".mysqli_real_escape_string($db,$volwId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";	
/*echo $insert_tblDracht.'<br>';*/		mysqli_query($db,$insert_tblDracht) or die (mysqli_error($db));

	  }

			} // Einde if (isset($day) && isset($registratie) && isset($mdrId))

			else if(!isset($day)) 			{ $fout = "De datum is onbekend."; }
			else if(!isset($registratie))	{ $fout = "Soort registratie is onbekend."; }
			else if(!isset($mdrId))			{ $fout = "Moederdier is onbekend."; }

} // Einde if (isset ($_POST['knpInsert_']))

if(isset($_POST['knpSave_'])) { include"save_dekkingen.php"; }
?>	
<form action = "Dekkingen.php" method = "post" >

<table border= 0><tr><td>
<!--*********************************
		 NIEUWE INVOER VELDEN
	********************************* -->
<table border= 0 >
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe dekking / dracht : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom"> 
 <td width="100">Datum<hr></hr></td>
 <td align="center" width="100">Registratie<hr></hr></td>
 <td align="center" width="100">Ooi<hr></hr></td> <!--<td style = "font-size:10px;"><i> Werknr - lammeren - halsnr </i>
 </td> -->
 <td align="center" width="100">Ram<hr></hr></td>
 <td align="center" width="100">Worpgrootte<hr></hr></td>
</tr>
<tr>
 <td align="center"><input type="text" id="datepicker1" name="txtDatum_" size = 8 value = <?php if(isset($dag)) { echo $dag; } else { echo date('d-m-Y'); } ?> >
 </td>
 <td align="center">
<select name= "kzlWat_" style= "width:80;" > 
<?php
$opties = array('' => '', 'dekking' => 'dekking', 'dracht' => 'dracht');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlWat_']) && $_POST['kzlWat_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
 </select>
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
 <select name= "kzlRamNew_" style= "width:65;" >
 <option></option>	
<?php	$count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
		
	$opties= array($vaRaak[$i]=>$vawerknr[$i]);
			foreach ( $opties as $key => $waarde)
			{
	if(($vdrId == $vaRaak[$i]) || (isset($_POST['kzlRamNew_']) && $_POST['kzlRamNew_'] == $key)) {
		echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
		else
		{
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select>
 </td>

 <td align="center"><input type = "text" size = 1 name = "txtWorp_" style = "font-size:10px;" value = <?php echo $txtGrootte; ?> >
 </td>
 <td colspan = 2><input type = "submit" name = "knpInsert_" value = "Toevoegen" style = "font-size:10px;">
 </td>
</tr>

<tr><td colspan = 15><hr></td></tr>
</table>
<!--*********************************
		EINDE NIEUWE INVOER VELDEN
	********************************* -->

</td></tr>

<?php if(isset($_POST['txtJaar_'])) { $hisJaar = $_POST['txtJaar_']; } 
else { $hisJaar = 2; } ?>

<tr><td align="right">
<!--*****************************
	 		WIJZIGEN DEKKINGEN
	***************************** -->
 <table border= 0>
 <tr height = 17 valign="bottom"> 
 </tr>
 <tr>
  <td colspan = 2 > <b>Dekkingen :</b> 
  <td colspan = 6 align="right"> Toon laatste
  	<input type="text" name="txtJaar_" size="1" style = "font-size:9px; text-align : center;" value = <?php echo $hisJaar; ?> >
   jaar
  </td>
  <td align="right" rowspan="2"> 
  	<input type="submit" name="knpVervers_" value="Ververs" style = "font-size:9px;">
  </td>
 </tr>
 <tr>
  <td colspan = 8 align="right" style="font-size: 13px;">
  	Eerdere dekkingen tonen 
  	<input type="radio" name="radAllDekkingen" value= 1 <?php if(isset($_POST['radAllDekkingen']) && $_POST['radAllDekkingen'] == 1) { echo "checked"; } ?> > Ja
  	<input type="radio" name="radAllDekkingen" value= 0 <?php if(!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == 0) { echo "checked"; } ?>  > Nee
  </td>
</tr>
<tr>
 <td colspan = 16 align="right" ><input type = "submit" name = "knpSave_" value = "Opslaan" style = "font-size:14px" >
 </td>
</tr>



<?php		
$current_year = date("Y");
$first_year = date("Y")-$hisJaar+1;

$array_drachtdatum = array();

// Historie jaren mogen niet verder in het verleden liggen dan het eerst dek- of drachtjaar. Het getoonde jaar moet dus altijd recenter of gelijk zijn aan het eerst dek- of drachtjaar
$zoek_jaartal_eerste_dekking_dracht = mysqli_query($db,"
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE (actId = 18 or actId = 19) and skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

	while($zj = mysqli_fetch_assoc($zoek_jaartal_eerste_dekking_dracht)) { $first_year_db = $zj['jaar']; }


if(!isset($first_year_db)) { $first_year = $current_year; }	// Als er geen dekking of dracht bestaat
else if($first_year < $first_year_db) { $first_year = $first_year_db; } 



for($jaar=$current_year; $jaar>=$first_year; $jaar--) { ?>

<tr>
 <td colspan="9">
 	
 <input type="checkbox" name="jaartalCheckbox" value= <?php echo $jaar; if($jaar == $current_year) { ?> checked <?php } ?> > <?php echo $jaar; ?>
 </td>
 <td class= "<?php echo $jaar; ?> selectt" >
 </td>
</tr>
 <tr style =  "font-size:12px;" valign =  "bottom" class= "<?php echo $jaar; ?> selectt" > 
	 <th></th> 
	 <th>Verwijder<hr></th> 
	 <th>Dekdatum<hr></th>
	 <th></th> 
	 <th>Ooi<hr></th>
	 <th></th> 
	 <th>Ram<hr></th>
	 <th></th> 
	 <th>Drachtig<hr></th>
	 <th>Drachtdatum<hr></th>
	 <th>Worpgrootte<hr></th>
	 <th>Werpdatum<hr></th>
 </tr> 

<?php
if(!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == '0') { $alle_dekkingen = 'Nee'; } else { $alle_dekkingen = 'Ja'; }

$zoek_dekkingen = mysqli_query($db,"
SELECT v.volwId, v.hisId, dekdatum, right(mdr.levensnummer,$Karwerk) mdr, v.vdrId vdrId, count(lam.schaapId) lamrn, drachtdatum, v.grootte, werpdatum,
lst_volwId
FROM tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 left join (
 	SELECT hisId, date_format(h.datum,'%d-%m-%Y') dekdatum, year(h.datum) dekjaar, skip
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) hv on (v.hisId = hv.hisId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
	SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum, year(h.datum) drachtjaar
 	FROM tblDracht d 
	 join tblHistorie h on (h.hisId = d.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) d on (d.volwId = v.volwId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join tblStal stl on (stl.schaapId = lam.schaapId)
 left join (
 	SELECT stalId, date_format(datum,'%d-%m-%Y') werpdatum, year(date_add(datum,interval -145 day)) dekjaar_obv_worp
 	FROM tblHistorie
 	WHERE actId = 1
 ) hl on (stl.stalId = hl.stalId)
 join (
	SELECT v.mdrId, max(v.volwId) lst_volwId
   FROM tblVolwas v
    left join (
   	SELECT hisId
      FROM tblHistorie
      WHERE actId = 18 and skip = 0
    ) hv on (v.hisId = hv.hisId)
    left join ( 
   	SELECT volwId
      FROM tblDracht d
       join tblHistorie hd on (hd.hisId = d.hisId)
      WHERE skip = 0
    ) d on (d.volwId = v.volwId)
    left join tblSchaap k on (k.volwId = v.volwId)
    left join (
   	SELECT s.schaapId
      FROM tblSchaap s
       join tblStal st on (s.schaapId = st.schaapId)
       join tblHistorie h on (st.stalId = h.stalId)
   	WHERE h.actId = 3
    ) ha on (k.schaapId = ha.schaapId)
    WHERE (hv.hisId is not null or d.volwId is not null) and isnull(ha.schaapId)
    GROUP BY mdrId
 ) lst_v on (lst_v.mdrId = v.mdrId)
WHERE stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (dekdatum is not null or drachtdatum is not null) and coalesce(dekjaar, dekjaar_obv_worp, drachtjaar) = '".mysqli_real_escape_string($db,$jaar)."'
GROUP BY v.volwId, v.hisId, dekdatum ,mdr.levensnummer, v.vdrId, drachtdatum, werpdatum, v.grootte
ORDER BY right(mdr.levensnummer,$Karwerk), dekdatum desc
") or die (mysqli_error($db));

	while($zd = mysqli_fetch_assoc($zoek_dekkingen))
	{
		$Id = $zd['volwId'];
		$hisId = $zd['hisId'];
		$dekdm = $zd['dekdatum'];
		$moeder = $zd['mdr'];
		$vaderId = $zd['vdrId'];
		$lamrn = $zd['lamrn']; if($lamrn == 0) { unset($lamrn); }
		$drachtdm = $zd['drachtdatum'];
		$werpdm = $zd['werpdatum'];
		$grootte = $zd['grootte'];
		$lst_volwId = $zd['lst_volwId'];

		if(isset($drachtdm) || isset($lamrn) || $_POST["kzlDrachtUpd_$Id"] == 'ja') { $drachtig = 'ja'; } else { $drachtig = 'nee'; }

if($drachtig == 'nee' || isset($lamrn)) {
	if($alle_dekkingen == 'Nee' && $Id == $lst_volwId) { $array_drachtdatum[] = $Id; }
	else if($alle_dekkingen == 'Ja') { $array_drachtdatum[] = $Id; }
}



		if($Id <> $lst_volwId && !isset($lamrn)) { $color = 'grey'; $fontsize = '14px'; } 
		else { $fontsize = '16px'; }

	$txtGrootte = $grootte;


if($Id <> $lst_volwId && !isset($lamrn) && (!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == '0') ) { $tonen = 'Nee'; } else { $tonen = 'Ja'; } 

if($tonen == 'Ja') { ?>

<tr class= "<?php echo $jaar; ?> selectt" > 
<td><?php echo $Id; ?> </td>
 <td align = center style = "font-size:14px;"><?php if(!isset($drachtig) || $drachtig =='nee' ) { ?> 

<!-- <button class=btn btn-sm btn-danger delete_class id= <?php echo $Id; ?> >Verwijder dekking</button> -->

<input type = "checkbox" name= <?php echo "chkDel_$Id"; ?> value = 1 style = "font-size:9px" >

 	 <?php } ?>
 </td>
 <td align = center style = "font-size: <?php echo $fontsize; ?> ; color : <?php echo $color; ?> ;"><?php echo $dekdm; ?></td><td width = "1">
 </td>
 <?php if(isset($lamrn) ) { unset($fontsize); } ?>
 <td align = center style = "font-size: <?php echo $fontsize; ?> ; color : <?php echo $color; ?> ;"><?php echo "$moeder";?>
 </td>
 <td width = "1">
 </td> 
 <td align="center">
 <!-- KZLVADER -->
 	<select name= <?php echo "kzlRam_$Id"; ?> style= "width:65;" >
 <option></option>	
<?php	$count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
		
	$opties= array($vaRaak[$i]=>$vawerknr[$i]);
			foreach ( $opties as $key => $waarde)
			{
	if(($vaderId == $vaRaak[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)) {
		echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
		else
		{
			echo '<option value="' . $key . '" >' . $waarde . '</option>';
		}
			}
		
		} ?>
 </select>
 <!-- Einde KZLVADER -->
 </td>
 <td width = "1">
 </td> 
 <td align="center"> 
<?php $opties = array('ja' => 'Ja', 'nee' => 'Nee');

$param = $Id . ", '" . $drachtdm . "', " . $txtGrootte;

if(isset($lamrn) ) { echo $opties[$drachtig]; } else { ?>
 	<!-- Keuzelijst drachtig -->
 	<select id= <?php echo "drachtig_$Id"; ?> name = <?php echo "kzlDrachtUpd_$Id"; ?> onchange= "toon_txtDatum( <?php echo $param; ?> )" style = "width:60; font-size:13px;">
<?php  

foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave_']) && $drachtig == $key) || (isset($_POST["kzlDrachtUpd_$Id"]) && $_POST["kzlDrachtUpd_$Id"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select>
<!-- Einde Keuzelijst drachtig -->
<?php } ?>
 </td>
 <td align="center">
 	<input type="text"  size = 8 id= <?php echo "drachtdatum_$Id"; ?> class= "<?php echo $Id; ?> " name= <?php echo "txtDrachtdm_$Id"; ?> value = <?php echo $drachtdm; ?> >
 	<?php if(isset($lamrn) ) { echo $drachtdm; } ?>
 </td>



 <td align="center">
 	<?php echo $lamrn ?>
	<input type = "text" id= <?php echo "worp_$Id"; ?> class= "<?php echo $Id; ?>" size = 1 style = "font-size : 11px; text-align : center;" name = <?php echo "txtGrootte_$Id"; ?> value = <?php echo $txtGrootte; ?> >
 </td>


 <td><?php echo $werpdm; ?></td>



</tr>

<?php } // Einde if($tonen == 'Ja') ?>


</tr>
<?php unset($color); 

}
  ?>
<tr class= "<?php echo $jaar; ?> selectt" ><td height="50"></td></tr>

<?php    } //var_dump($array_drachtdatum);
 ?>

</td></tr>

</table>
<!--*****************************
	 	EINDE WIJZIGEN DEKKINGEN
	***************************** -->
</form>
</td></tr></table>



</TD>

<?php
Include "menu1.php"; } 
} // Einde if($modtech == 1) ?>


<script type="text/javascript">
var cur_year = new Date().getFullYear();

//$('.' + cur_year + '.selectt').toggle();
$('.' + cur_year).toggle();



    $(document).ready(function() {
        $('input[type="checkbox"]').click(function() {
            var inputValue = $(this).attr("value");
            //alert(inputValue);
            $("." + inputValue).toggle();
        });
    });



var jArray_Id = <?php echo json_encode($array_drachtdatum); ?>;

for (let i = 0; i < jArray_Id.length; i++) {

	//alert(i);

var drachtdm = 'drachtdatum_' + jArray_Id[i];

	document.getElementById(drachtdm).value = null; // veld leegmaken indien gevuld
	$('.' + jArray_Id[i]).toggle();
}



</script>


</body>
</html>
