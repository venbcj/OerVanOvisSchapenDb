<!-- 19-12-2021; Aangemaakt als kopie van post_readerDracht.php  -->

<?php
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
#echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen
if($recId > 0) {

 foreach($id as $key => $value) {

  if ($key == 'chbKies')   { $fldKies = $value; }
  if ($key == 'chbDel')    { $fldDel = $value; }

	if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $valuedag =  date_format($dag, 'Y-m-d'); 
									$fldDag = $valuedag; }
	
	if ($key == 'kzlOoi' && !empty($value)) { /*echo '$fldOoi = '.$value.'<br>';*/ $fldOoi = $value; } // betreft schaapId ooi

	if ($key == 'kzlRam' && !empty($value)) { /*echo '$fldRam = '.$value.'<br>';*/ $fldRam = $value; } // betreft schaapId ram
	 
									}
// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

unset($actId, $stalSchaarId, $stalId, $ubnId, $ubn);

if(isset($fldOoi)) {
$zoek_uitgeschaard = mysqli_query($db,"
SELECT h.actId, st.rel_best, stSchaar.stalId stSchaar
FROM tblStal st
 join (
 	SELECT max(st.stalId) stalId, st.schaapId
 	FROM tblStal st
 	 join tblUbn u on (st.ubnId = u.ubnId)
 	WHERE st.schaapId = '".mysqli_real_escape_string($db,$fldOoi)."' and u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and u.lidubn = 1
 	GROUP BY st.schaapId
 ) stm on (stm.stalId = st.stalId)
 join tblHistorie h on (h.stalId = st.stalId)
 left join tblStal stSchaar on (stSchaar.stalId > stm.stalId and stSchaar.schaapId = stm.schaapId)
 left join tblUbn uSchaar on (stSchaar.ubnId = uSchaar.ubnId)
WHERE h.actId = 10 and (uSchaar.lidId = '".mysqli_real_escape_string($db,$lidId)."' or isnull(uSchaar.lidId))
") or die(mysqli_error($db));

 	 $zu = mysqli_fetch_assoc($zoek_uitgeschaard);
 	 if($zu) {
 	   $actId = $zu['actId']; 
 	   $relId = $zu['rel_best']; 
 	   $stalSchaarId = $zu['stSchaar']; }
}

if(isset($stalSchaarId)) { $stalId = $stalSchaarId; }
if(isset($actId) && !isset($stalSchaarId)) { //schaap is uitgeschaard en heeft nog geen stalmoment van die lokatie
// Maak stalmoment van uitgeschaarde lokatie
$zoek_ubn_uitgeschaarde_lokatie = mysqli_query($db,"
SELECT p.ubn, u.ubnId
FROM tblRelatie r
 join tblPartij p on (r.partId = p.partId)
 left join tblUbn u on (u.ubn = p.ubn)
WHERE r.relId = '".mysqli_real_escape_string($db,$relId)."' and ((u.lidubn = 0 and u.lidId = '".mysqli_real_escape_string($db,$lidId)."') or u.ubnId IS NULL)
") or die (mysqli_error($db));

$zuul = mysqli_fetch_assoc($zoek_ubn_uitgeschaarde_lokatie); {
	$ubn = $zuul['ubn'];
	$ubnId = $zuul['ubnId'];
}

If(!isset($ubnId)) { //Als de externe lokatie (ubn) nog niet voorkomt in tblUbn bij deze gebruiker
$ubn_toevoegen = "
  INSERT INTO tblUbn SET lidId = '".mysqli_real_escape_string($db,$lidId)."', ubn = '".mysqli_real_escape_string($db,$ubn)."', lidubn = 0";
		
/*echo "<pre>";
echo $ubn_toevoegen;
echo "</pre>";*/
						  mysqli_query($db,$ubn_toevoegen) or die (mysqli_error($db));

$zoek_ubnId = mysqli_query($db,"
SELECT u.ubnId
FROM tblUbn u
WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ubn = '".mysqli_real_escape_string($db,$ubn)."'
") or die (mysqli_error($db));

$zu = mysqli_fetch_assoc($zoek_ubnId);
	$ubnId = $zu['ubnId'];
}

$stalmoment_aanmaken = "
  INSERT INTO tblStal SET ubnId = '".mysqli_real_escape_string($db,$ubnId)."', schaapId = '".mysqli_real_escape_string($db,$fldOoi)."', rel_herk = 4 "; // 4 bestaat niet als relId in tblRelatie en staat voor herkomst van deze gebruiker
		
/*echo "<pre>";
echo $stalmoment_aanmaken;
echo "</pre>";*/
					 mysqli_query($db,$stalmoment_aanmaken) or die (mysqli_error($db));

$zoek_stalmoment = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE ubnId = '".mysqli_real_escape_string($db,$ubnId)."' and schaapId = '".mysqli_real_escape_string($db,$fldOoi)."' and rel_best IS NULL
") or die (mysqli_error($db));

$zs = mysqli_fetch_assoc($zoek_stalmoment);
	$stalId = $zs['stalId'];
} // Einde Maak stalmoment van uitgeschaarde lokatie

// CONTROLE op alle verplichten velden 
if(isset($fldDag) && isset($fldOoi)) {

// De ooi mag binnen laatste 183 dagen geen worp hebben.
if (!isset($stalId)) { //Als deze variabele wel bestaat is dit het stalmoment van de uitgeschaarde lokatie
$zoek_stalId = mysqli_query($db,"
SELECT st.stalId
FROM tblStal st
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE st.schaapId = '".mysqli_real_escape_string($db,$fldOoi)."' and u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and u.lidubn = 1 and isnull(st.rel_best)
") or die (mysqli_error($db));

$zs = mysqli_fetch_assoc($zoek_stalId); $stalId = $zs['stalId'];
}
	$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 18 ";	
/*echo "<pre>";
echo $insert_tblHistorie.'<br>';
echo "</pre>";*/
						mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."' and datum = '".mysqli_real_escape_string($db,$fldDag)."' and actId = 18
") or die (mysqli_error($db));

while($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

	$insert_tblVolwas = "INSERT INTO tblVolwas SET readId = '".mysqli_real_escape_string($db,$recId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."', mdrId = '".mysqli_real_escape_string($db,$fldOoi)."', vdrId = ". db_null_input($fldRam);	
/*echo "<pre>";
echo $insert_tblVolwas.'<br>';
echo "</pre>";*/
						mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));	

		$updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo "<pre>";
echo $updateReader.'<br>';
echo "</pre>";*/

					mysqli_query($db,$updateReader) or die (mysqli_error($db));


unset($fldOoi); unset($fldRam);
// EINDE CONTROLE op alle verplichten velden 

}  // Einde if(isset($fldOoi) && isset($fldRam) && isset($fldDag))

} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))

	
 if($fldKies == 0 && $fldDel == 1) {	
	
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
/*echo $updateReader.'<br>';*/		mysqli_query($db,$updateReader) or die (mysqli_error($db));
	}





unset($fldlevnr);

} // Einde if(isset($recId))
	} // Einde foreach($array as $recId => $id)
?>
					
	