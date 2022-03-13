<?php 
$versie = '13-11-2016'; /* Aangemaakt als kopie van insAanvoer. 
schaap 100214520769 gewijzigd in */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset($data)) toegevoegd. Als alle records zijn verwerkt bestaat $data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '10-11-2018'; /* Inlezen darcht herzien. Rekening gehouden met worp laatste 183 en alleen ooien en rammen op stallijst !! */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '18-12-2021'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */

 session_start(); ?>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>

<style type="text/css">
        .selectt {
           /* color: #fff;
            padding: 30px;*/
            display: "inline-block";
            /*margin-top: 30px;
            width: 60%;
            background: grey;*/
            font-size: 12px;
        }
   </style>

</head>
<body>

<center>
<?php
$titel = 'Inlezen Dracht';
$subtitel = ''; 
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php 
$file = "InsDracht.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "vw_kzlOoien.php";

If (isset($_POST['knpInsert_']))  {
	//Include "url.php";
	Include "post_readerDracht.php"; #Deze include moet voor de vervversing in de functie header()
	//header("Location: ".$url."InsDracht.php"); 
	}

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

// Array tbv javascript om vader automatisch te tonen
	// Zoek de laatste dekkingen. Deze laatste dekking moet een vader hebben geregistreerd
	// Als er een dracht bestaat in tblDracht moet deze niet zijn verwijderd (zie hd.skip = 0)
$zoek_laatste_dekkingen_met_vader = mysqli_query($db,"
SELECT v.mdrId, v.vdrId, right(vdr.levensnummer,$Karwerk) lev
FROM tblVolwas v
 join (
 	SELECT v.mdrId, max(v.volwId) volwId
	FROM tblVolwas v
	 left join (
 		SELECT hisId
 		FROM tblHistorie h
 		 join tblStal st on (st.stalId = h.stalId)
 		WHERE skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) hv on (hv.hisId = v.hisId)
	 left join (
 	SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum
 	FROM tblDracht d 
	 join tblHistorie h on (h.hisId = d.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) d on (v.volwId = d.volwId)
	 left join tblSchaap k on (k.volwId = v.volwId)
	 left join (
	    SELECT s.schaapId
	    FROM tblSchaap s
	     join tblStal st on (s.schaapId = st.schaapId)
	     join tblHistorie h on (st.stalId = h.stalId)
	    WHERE h.actId = 3
	 ) ha on (k.schaapId = ha.schaapId)
	WHERE isnull(ha.schaapId)
	GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId)
 join tblSchaap vdr on (vdr.schaapId = v.vdrId)
") or die (mysqli_error($db));

while ( $zld = mysqli_fetch_assoc($zoek_laatste_dekkingen_met_vader)) { $array_vader_uit_koppel[$zld['mdrId']] = $zld['lev']; }

// Einde Array tbv javascript om vader automatisch te tonen
?>

<script>

function toon_dracht(id) {

var ooi = 'ooi_' + id;
var moeder = document.getElementById(ooi);		var mr = moeder.value;


// if(mr.length > 0) alert(jArray_vdr[mr]);
  if(mr.length > 0) toon_vader_uit_koppel(mr, id);

}

 var jArray_vdr = <?php echo json_encode($array_vader_uit_koppel); ?>;

function toon_vader_uit_koppel(m, i) {
	//document.getElementById('result_vader').innerHTML = jArray_vdr[m];

// alert('ooiId = ' + m + ' Id = ' + i); #/#

var ram = 'ram_' + i;
var resultRam = 'result_ram_' + i;
var dbRam = 'dbRam_' + i;

 	if(jArray_vdr[m] != null)
 	{
	document.getElementById(ram).style.display = "none";
  	document.getElementById(ram).value = null; // veld leegmaken indien gevuld
  	document.getElementById(resultRam).innerHTML = jArray_vdr[m];
	document.getElementById(dbRam).style.display = "none";
	}
  	else 
  	{
  	//document.getElementById(ram).style.display = "block";
	document.getElementById(ram).style.display = "inline-block";
	document.getElementById(resultRam).innerHTML = "";
	document.getElementById(dbRam).style.display = "none";
  	}

  	//alert('ram = ' + jArray_vdr[m]); #/#
}

</script>

<?php
//if($reader == 'Agrident') {
$velden = "rd.Id, rd.datum, rd.moeder, mdr.schaapId mdrId, rd.drachtig, rd.grootte";

$tabel = "
impAgrident rd 
 left join (
 	SELECT s.schaapId, s.levensnummer
 	FROM tblSchaap s
 	 join tblStal st on (s.schaapId = st.schaapId)
 	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
 	) mdr on (mdr.levensnummer = rd.moeder)
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 19 and isnull(verwerkt) ";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY str_to_date(rd.datum,'%d/%m/%Y'), rd.Id"); ?>

<table border = 0>
<tr> <form action="InsDracht.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 2 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen"> </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Dracht<br>datum<hr></th>
 <th>Moeder<hr></th>
 <th>Vader<hr></th>
 <th>Drachtig<hr></th>
 <th>Worpgrootte<hr></th>
 <th><hr></th>

</tr>

<?php
if($modtech == 1) {
// Declaratie MOEDERDIER alleen op stal en niet geworpen laatste 183 dagen
$zoek_moederdieren = mysqli_query($db,"
SELECT st.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
 	SELECT stalId, hisId
 	FROM tblHistorie h
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1
 ) haf on (haf.stalId = st.stalId)
 join (
 	SELECT schaapId
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.geslacht = 'ooi' and isnull(haf.hisId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));


$index = 0; 
while ($mdr = mysqli_fetch_assoc($zoek_moederdieren)) 
{ 
   $mdrkey[$index] = $mdr['schaapId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie VADERDIER  ALLEEN OP STAL tussen nu en de afgelopen 2 maanden
$zoek_vaderdieren = mysqli_query($db,"
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	SELECT stalId, hisId, datum
	FROM tblHistorie h
	 join tblActie a on (h.actId = a.actId)
	WHERE a.af = 1
 ) haf on (haf.stalId = st.stalId)
 join (
 	SELECT schaapId
 	FROM tblStal st
 	 join tblHistorie h on (st.stalId = h.stalId)
 	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.geslacht = 'ram' and ( isnull(haf.hisId) or date_add(haf.datum,interval 2 month) > CURRENT_DATE() )
ORDER BY right(levensnummer,$Karwerk)
") or die (mysqli_error($db)); 


$index = 0; 
while ($vdr = mysqli_fetch_assoc($zoek_vaderdieren)) 
{ 
   $vdrkey[$index] = $vdr['schaapId'];
   $wrknrRam[$index] = $vdr['werknr'];
   $vdrRaak[$index] = $vdr['schaapId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie VADERDIER
}

/**********************************
 **	 DUBBELE DRACHT ZOEKEN		**
 **********************************/ 
$array_dub = array();

	if (isset($_POST['knpVervers_']) ) {


function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

$array_rec = array();

foreach($_POST as $key => $value) {
    
    $array_rec[getIdFromKey($key)][getNameFromKey($key)] = $value;
}
foreach($array_rec as $recId => $id) {

// Id ophalen
#echo $recId.'<br>'; 
//var_dump($array_rec);
// Einde Id ophalen
   unset($kzlOoi);

 foreach($id as $key => $value) {
	
	if ($key == 'kzlOoi' && !empty($value)) { /*echo '$kzlOoi = '.$value.'<br>';*/ $kzlOoi = $value; } // betreft schaapId ooi

}

$array_dub[] = $kzlOoi;

}

}
else {
if(isset($data))  { foreach($data as $key => $array)
	{
	
	$array_dub[] = $array['mdrId']; // schaapId uit tblStal o.b.v. moeder uit reader

}
}

}


/*$array = array(12,43,66,21,56,43,43,78,78,100,43,43,43,21);*/
$vals = array_count_values($array_dub);
//echo 'No. of NON Duplicate Items: '.count($vals).'<br><br>';
/*print_r($vals);*/

/****************************************
 **	 EINDE DUBBELE DRACHT ZOEKEN		**
 ****************************************/ 

$array_readId = array();

if(isset($data))  {	foreach($data as $key => $array)
	{
		$var = $array['datum'];
	$date = str_replace('/', '-', $var);
	$datum = date('d-m-Y', strtotime($date));
	//$makeday = date_create($date); $day = date_format($makeday, 'Y-m-d');
	
	$Id = $array['Id'];
	$moeder_rd = $array['moeder']; // levensnummer moeder uit reader
	$mdrId_rd = $array['mdrId']; // schaapId uit tblStal

	$drachtig_rd = $array['drachtig'];
	$grootte_rd = $array['grootte'];

// Zoek vader uit laatste dekkingen o.b.v. ooi uit de reader
unset($vdrId_db);
unset($ram_db);


$zoek_vader_uit_laatste_dekkingen = mysqli_query($db,"
SELECT v.mdrId, v.vdrId, right(vdr.levensnummer,$Karwerk) werknr_ram
FROM tblVolwas v
 join tblSchaap vdr on (vdr.schaapId = v.vdrId)
 join (
 	SELECT v.mdrId, max(v.volwId) volwId
	FROM tblVolwas v
	 left join (
	 	SELECT hisId
 		FROM tblHistorie h
 		 join tblStal st on (st.stalId = h.stalId)
 		WHERE h.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$mdrId_rd)."'
	 ) hv on (hv.hisId = v.hisId)
	 left join (
	 	SELECT d.volwId
 		FROM tblDracht d 
		 join tblHistorie h on (h.hisId = d.hisId)
		 join tblStal st on (st.stalId = h.stalId)
		WHERE h.skip = 0 and st.schaapId = '".mysqli_real_escape_string($db,$mdrId_rd)."'
	 ) d on (v.volwId = d.volwId)
	 left join tblSchaap k on (k.volwId = v.volwId)
	 left join (
	    SELECT s.schaapId
	    FROM tblSchaap s
	     join tblStal st on (s.schaapId = st.schaapId)
	     join tblHistorie h on (st.stalId = h.stalId)
	    WHERE h.actId = 3
	 ) ha on (k.schaapId = ha.schaapId)
	WHERE isnull(ha.schaapId) and v.mdrId = '".mysqli_real_escape_string($db,$mdrId_rd)."'
	GROUP BY v.mdrId
 ) lv on (v.volwId = lv.volwId) 
") or die (mysqli_error($db));

while ( $zvud = mysqli_fetch_assoc($zoek_vader_uit_laatste_dekkingen)) { 

	$vdrId_db = $zvud['vdrId'];
	$ram_db = $zvud['werknr_ram'];

	
}

if(isset($vdrId_db)) { $array_readId[] = $Id; }

##var_dump($array_readId);



// Controleren of ingelezen waardes worden gevonden .
if (isset($_POST['knpVervers_']) ) {

	$txtDatum = $_POST["txtDatum_$Id"]; 
	//$makeday = strtotime($txtDatum); $day = date_format($makeday, 'Y-m-d');
	$kzlOoi = $_POST["kzlOoi_$Id"]; if(!empty($kzlOoi)) { unset($moeder_rd); }
	$kzlRam = $_POST["kzlRam_$Id"];
	$kzlDrachtig = $_POST["kzlDracht_$Id"];
	$txtGrootte = $_POST["txtGrootte_$Id"];
}
else { 

	$txtDatum = $datum;
	$kzlOoi = $mdrId_rd;
	$kzlDrachtig = $drachtig_rd;
	$txtGrootte = $grootte_rd;

}

$cnt_ooien = $vals[$kzlOoi];

if(!empty($kzlOoi)) {
$zoek_moeder = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap s
WHERE s.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
") or die (mysqli_error($db));

while ($moe = mysqli_fetch_assoc($zoek_moeder)) { $moeder_db = $moe['levensnummer']; }

$zoek_laatste_dekking_van_ooi = mysqli_query($db,"
SELECT v.mdrId, max(v.volwId) volwId
FROM tblVolwas v
 left join (
		SELECT hisId
		FROM tblHistorie h
		 join tblStal st on (st.stalId = h.stalId)
		WHERE skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) hv on (hv.hisId = v.hisId)
 left join (
		SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum
		FROM tblDracht d 
	 join tblHistorie h on (h.hisId = d.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) d on (v.volwId = d.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
    SELECT s.schaapId
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) ha on (k.schaapId = ha.schaapId)
WHERE (hv.hisId is not null or d.volwId is not null) and isnull(ha.schaapId) and v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."'
GROUP BY v.mdrId
") or die (mysqli_error($db));

while ( $zad = mysqli_fetch_assoc($zoek_laatste_dekking_van_ooi)) { $act_volwId = $zad['volwId']; }

unset($dmdracht);

$zoek_drachtdatum = mysqli_query($db,"
SELECT h.datum dmdracht, date_format(h.datum,'%d-%m-%Y') drachtdm
FROM tblVolwas v
 join tblDracht d on (v.volwId = d.volwId)
 join tblHistorie h on (d.hisId = h.hisId)
WHERE skip = 0 and v.volwId = '".mysqli_real_escape_string($db,$act_volwId)."'
") or die (mysqli_error($db));

while ($zddm = mysqli_fetch_assoc($zoek_drachtdatum)) { $dmdracht = $zddm['dmdracht']; $drachtdm = $zddm['drachtdm']; }


unset($lst_volwId);
unset($dmwerp);
unset($dagen_verschil_worp);

$zoek_laatste_worp = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
	SELECT s.schaapId
	FROM tblSchaap s
	 join tblStal st on (s.schaapId = st.schaapId)
    join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3
 ) ha on (k.schaapId = ha.schaapId)
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and isnull(ha.schaapId)
") or die (mysqli_error($db));

while ($zlw = mysqli_fetch_assoc($zoek_laatste_worp)) { $lst_volwId = $zlw['volwId']; }

if(isset($lst_volwId)) {
$zoek_werpdatum = mysqli_query($db,"
SELECT h.datum, date_format(h.datum,'%d-%m-%Y') werpdm
FROM tblVolwas v
 join tblSchaap l on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and v.volwId = '".mysqli_real_escape_string($db,$lst_volwId)."'
") or die (mysqli_error($db));

while ($zwd = mysqli_fetch_assoc($zoek_werpdatum)) { $dmwerp = $zwd['datum']; $werpdm = $zwd['werpdm']; }

$date_dracht = date_create($dmdracht);
$date_worp = date_create($dmwerp);

$verschil_drachtdm_worp = date_diff($date_dracht, $date_worp);
$dagen_verschil_worp 	= $verschil_drachtdm_worp->days;

}

/*echo '$lst_volwId = '.$lst_volwId.'<br>';#/#
echo '$dmwerp = '.$dmwerp.'<br>';#/#
echo '$dagen_verschil_worp = '.$dagen_verschil_worp.'<br>';#/#

echo '<br>';*/#/#

unset($afv_status_mdr);
$zoek_afvoerstatus_mdr = mysqli_query($db,"
SELECT a.actie
FROM tblStal st
 join (
 	SELECT max(stalId) stalId
 	FROM tblStal
 	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
 ) maxst on (maxst.stalId = st.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1
") or die (mysqli_error($db));

while ($sm = mysqli_fetch_assoc($zoek_afvoerstatus_mdr)) 
{ 
   $afv_status_mdr = $sm['actie'];
}

} // Einde if(isset($kzlOoi)) 



	if (
		(!isset($moeder_db) || empty($kzlOoi))	|| // moeder wordt niet gevonden
		$kzlDrachtig == 0 							|| // Drachting is nee
	 	(isset($dagen_verschil_worp) && $dagen_verschil_worp < 183)	|| // moederdier heeft laatste 183 al gelammerd
	 	$cnt_ooien > 1    	 					|| # moeder bestaat al in reader bestand
	 	isset($dmdracht)							|| // drachtdatum bestaat al
	 	empty($txtDatum)								   // drachtdatum is onbekend
	 												
	   )

	 {	$oke = 0;	} else {	$oke = 1;	} // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

unset($cbDel);
	 if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbKies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 

   if ($kzlDrachtig == 0) { $cbKies = 0; $cbDel = 1; } // Drachtig is Nee ?>

<!--	**************************************
		**	   	 OPMAAK  GEGEVENS			**
		************************************** -->

<tr style = "font-size:14px;">
 <td align = center>

	<!-- <input type = hidden size = 1 name = <?php echo "chbKies_$Id"; ?> value = 0 > --> <!-- hiddden -->
	<input type = checkbox 		  name = <?php echo "chbKies_$Id"; ?> value = 1 
	  <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

	  if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
	<input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
	<!-- <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 > -->
	<input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
 	<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $txtDatum; ?> >
 </td>

 <?php $width = 25+(8*$Karwerk) ; ?>

 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
 <select id= <?php echo "ooi_$Id"; ?> onchange = <?php echo "toon_dracht(".$Id.")"; ?> style= "width:<?php echo $width; ?>; font-size:12px;" name = <?php echo "kzlOoi_$Id"; ?> >
  <option></option>
<?php	$count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

	$opties = array($mdrkey[$i]=>$wnrOoi[$i]);
			foreach($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $mdrId_rd == $key) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }		
			}
}
?> </select> 
	<!-- EINDE KZLMOEDER --> 
 </td>
 <td width = <?php echo $width; ?> > 
 <div id= <?php echo "dbRam_$Id"; ?> align = "center" > <?php echo $ram_db; ?> </div> 
	<!-- KZLVADER -->
 <select style= "width:<?php echo $width; ?>; font-size:12px;" id= <?php echo "ram_$Id"; ?> class= "<?php echo $Id; ?> selectt" name = <?php echo "kzlRam_$Id"; ?> >
 <option></option>	
<?php	$count = count($wrknrRam);
for ($i = 0; $i < $count; $i++){

		
	$opties= array($vdrkey[$i]=>$wrknrRam[$i]);
			foreach ($opties as $key => $waarde)
			{
  if ((!isset($_POST['knpVervers_']) && $vdrId_db == $vdrRaak[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
		
} ?>
 </select><p id= <?php echo "result_ram_$Id"; ?> align = "center" ></p> 
	<!-- EINDE KZLVADER -->

 </td>
 <td>
	<!-- KZLDRACHTIG -->
	<select style="width:50; font-size:12px;" name= <?php echo "kzlDracht_$Id"; ?> >
<?php 
$opties = array('Nee', 'Ja');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if((!isset($_POST['knpVervers_']) && $drachtig_rd == $key) || (isset($_POST["kzlDracht_$Id"]) && $_POST["kzlDracht_$Id"] == $key))
   {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
} ?>
</select>
	<!-- EINDE KZLDRACHTIG -->
 </td>
 <td align="center">
	<input type = "text" size = 1 style = "font-size : 11px; text-align : right;" name = <?php echo "txtGrootte_$Id"; ?> value = <?php echo $txtGrootte; ?> >
 </td>
 
<?php if (!isset($moeder_db) || empty($kzlOoi))	{ $color = 'red'; $bericht = 'Ooi '.$moeder_rd.' onbekend'; }
 else if ($cnt_ooien > 1 ) 					{ $color = 'blue'; $bericht = "Dubbel in de reader."; }
 else if (isset($dmdracht)) { $color = 'red'; $bericht = 'Deze ooi is reeds drachtig per '.$drachtdm; }
 else if(isset($dagen_verschil_worp) && $dagen_verschil_worp < 183) { $color = 'red'; $bericht = 'Deze ooi heeft op '.$werpdm.' nog geworpen. Een ooi kan 1x per half jaar werpen.'; }
 else if (isset($afv_status_mdr) && !isset($kzlOoi)) 					 { $color = 'red'; $bericht = 'Ooi '.$moeder_db.' is '.$afv_status_mdr; }
?>
<td style = "color: <?php echo $color; ?> ; font-size:12px; " > <center> <?php 

 if (	isset($bericht) ) { echo $bericht; unset($bericht); unset($color); } ?>
</center>
 </td>
 <td></td>	
 <td></td> 
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->

<?php 
} 
} //einde if(isset($data)) ?>
</table>
</form> 



</TD>
<?php
Include "menu1.php"; } ?>
</tr>

</table>
</center>

</body>
</html>
<SCRIPT language="javascript">
$(function(){

	// add multiple select / deselect functionality
	$("#selectall").click(function () {
		  $('.checkall').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall checkbox
	// and viceversa
	$(".checkall").click(function(){

		if($(".checkall").length == $(".checkall:checked").length) {
			$("#selectall").attr("checked", "checked");
		} else {
			$("#selectall").removeAttr("checked");
		}

	});
});

$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});


var jArray_Id = <?php echo json_encode($array_readId); ?>;

for (let i = 0; i < jArray_Id.length; i++) {

var ram = 'ram_' + jArray_Id[i];

	document.getElementById(ram).value = null; // veld leegmaken indien gevuld
	$('.' + jArray_Id[i]).toggle();
}



  	


</SCRIPT>