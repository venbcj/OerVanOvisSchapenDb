<!-- 23-5-2020 : Gekopieerd van post_readerOvp.php
13-7-2020 : impVerplaatsing gewijzigd in impAgrident 
11-4-2021 : Adoptie losgekoppeld van verblijf 
5-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven 
31-12-2023 :  and h.skip = 0 toegevoegd aan zoek_laatste_hisId -->

<?php
include "url.php";

function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

$array = array();

foreach($_POST as $key => $value) {
    
    $array[getIdFromKey($key)][getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

// Id ophalen
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen

  foreach($id as $key => $value) {

  if ($key == 'chbkies')   { $fldKies = $value; }
  if ($key == 'chbDel')   { $fldDel = $value; }

	if ($key == 'txtDag' && !empty($value)) { $dag = date_create($value); $valuedate =  date_format($dag, 'Y-m-d'); 
									/*echo $key.'='.$valuedate.' ';*/ $fldDay = $valuedate; }
	if ($key == 'kzlHok' && !empty($value)) { $hokId = $value; /*echo $key.'='.$valuedate.' ';*/ }

									}
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
if($reader == 'Agrident') {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen


// CONTROLE op alle verplichten velden bij adoptie lam
if (isset($fldDay) && isset($hokId))
{
	
$zoek_levensnummer = mysqli_query($db,"
SELECT rd.levensnummer levnr
FROM impAgrident rd
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ($dl = mysqli_fetch_assoc($zoek_levensnummer)) { $levnr = $dl['levnr']; }
//echo '$levnr = '.$levnr.'<br>';

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
	while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';

// zoek verblijf om te kijken of het dier moet worden (over)geplaatst
$zoek_laatste_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and (a.aan = 1 or a.uit = 1) and h.skip = 0
") or die (mysqli_error($db));
	while ($zlh = mysqli_fetch_assoc($zoek_laatste_hisId)) { $last_his = $zlh['hisId']; }

$zoek_verblijf = mysqli_query($db,"
SELECT hokId
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$last_his)."'
") or die (mysqli_error($db));
	while ($zv = mysqli_fetch_assoc($zoek_verblijf)) { $last_hokId = $zv['hokId']; }

if(!isset($last_hokId)) { $actId = 6; }
else if($last_hokId != $hokId) { $actId = 5; }
// Einde zoek verblijf om te kijken of het dier moet worden (over)geplaatst
	
	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = 15 ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

// Optie om dier in verblijf (over) te plaatsen
if(isset($actId)) {

	$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDay)."', actId = '".mysqli_real_escape_string($db,$actId)."' ";
		mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));


$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = '".mysqli_real_escape_string($db,$actId)."'
") or die (mysqli_error($db));
	while ($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

	$insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId)."', hokId = '".mysqli_real_escape_string($db,$hokId). "'";
		mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
}
// EInde Optie om dier in verblijf (over) te plaatsen
		
	$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
		mysqli_query($db,$updateReader) or die (mysqli_error($db));	
}
// EINDE CONTROLE op alle verplichten velden bij spenen lam
		

}  // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))

  if($fldKies == 0 && $fldDel == 1) {	

    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
	mysqli_query($db,$updateReader) or die (mysqli_error($db));

    }


	
						} // Einde foreach($array as $recId => $id)
?>
