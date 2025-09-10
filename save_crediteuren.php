<!-- 15-6-2016 : gemaakt
09-08-2020 : veld naamreader toegevoegd 
29-12-2023 : veld actief bij Rendac niet wijigbaar gemaakt 
27-02-2025 : In Relaties.php <input type= "hidden" name= echo "txtcreId_Id"; verwijderd en hier lege checkboxen gedefinieerd-->

<?php
/* toegepast in :
	- Relaties.php */
	
function getNaamFromKey($string) {
    $split_naam = explode('_', $string);
    return $split_naam[0];
}

function getIdFromKey($string) {
    $split_Id = explode('_', $string); 
    return $split_Id[1];
}

foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[getIdFromKey($fldname)][getNaamFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {  

unset($relId);

$zoek_relatie_crediteur = mysqli_query($db,"
	SELECT relId
	FROM tblRelatie
	WHERE relId = '".mysqli_real_escape_string($db,$recId)."' and relatie = 'cred'
") or die(mysqli_error($db));
	while( $zrc = mysqli_fetch_assoc($zoek_relatie_crediteur)) { 
		$relId = $zrc['relId'];

	}


unset($fldUbn);
unset($fldNaam);
unset($fldPres);
unset($fldStraat);
unset($fldNr);
unset($fldPc);
unset($fldPlaats);
unset($fldTel);
unset($fldActief);

foreach($id as $key => $value) {

    if ($key == 'txtcreUbn' && !empty($value)) 		{  $fldUbn = $value; }
    if ($key == 'txtcreNaam' && !empty($value))		{  $fldNaam = $value; }
    if ($key == 'txtcrePres' && !empty($value)) 	{  $fldPres = $value; }
    if ($key == 'txtcreStraat' && !empty($value)) {  $fldStraat = $value; }
    if ($key == 'txtcreNr' && !empty($value)) 		{  $fldNr = $value; }
    if ($key == 'txtcrePc' && !empty($value)) 		{  $fldPc = $value; }
    if ($key == 'txtcrePlaats' && !empty($value)) {  $fldPlaats = $value; }
    if ($key == 'txtcreTel' && !empty($value)) 		{  $fldTel = $value; }
    if ($key == 'chkcreActief' && !empty($value)) {  $fldActief = $value; }	
}

if (!isset($fldPres) && isset($fldNaam)) { $fldPres = $fldNaam; }
if (!isset($fldActief)) { $fldActief = 0; }

if(isset($relId) && $recId > 0) {

	/*echo $recId."<br/>";*/

$zoek_crediteur = mysqli_query($db,"
	SELECT p.ubn, p.naam, p.naamreader, a.adrId, tel, r.actief
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 left join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
") or die(mysqli_error($db));
	while( $zc = mysqli_fetch_assoc($zoek_crediteur)) { 
		$ubn_db = $zc['ubn'];
		$naam_db = $zc['naam'];
		$naamreader_db = $zc['naamreader'];
		$adrId_db = $zc['adrId'];
		$tel_db = $zc['tel'];
		$actief_db = $zc['actief'];

	}

unset($rel_ren);

$zoek_rendac = mysqli_query($db,"
	SELECT relId
	FROM tblRelatie r
	 join tblPartij p on (r.partId = p.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."' and p.naam = 'Rendac'
") or die(mysqli_error($db));
	while( $zr = mysqli_fetch_assoc($zoek_rendac)) { $rel_ren = $zr['relId']; }

// Wijzigen ubn
if($fldUbn <> $ubn_db && !isset($rel_ren)) {

if(isset($fldUbn)) {
$zoek_bestaand_ubn = mysqli_query($db,"
SELECT count(p.partId) aant
FROM tblPartij p
WHERE p.ubn = '".mysqli_real_escape_string($db,$fldUbn)."' and p.lidId = '" . mysqli_real_escape_string($db, $lidId) . "'
") or die(mysqli_error($db));
	while( $dub_ubn = mysqli_fetch_assoc($zoek_bestaand_ubn)) { $aant_ubn = $dub_ubn['aant']; }
}

if (isset($aant_ubn) && $aant_ubn > 0) { $fout = 'Dit ubn bestaat al'; }
else {

  //if($fldUbn == '') { $fldUbn = 'NULL'; }
$wijzigUbn = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET ubn = ". db_null_input($fldUbn) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigUbn.'<br>';*/ mysqli_query($db,$wijzigUbn) or die (mysqli_error($db));		
 }
}
// Einde Wijzigen ubn

// Wijzigen naam
if($fldNaam <> $naam_db && !isset($rel_ren)) {
 // if($fldNaam == '') { $fldNaam = "NULL"; } else { $fldNaam = "'".$fldNaam."'"; }
$wijzigNaam = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	set naam = ". db_null_input($fldNaam) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		mysqli_query($db,$wijzigNaam) or die (mysqli_error($db));
 }
// Einde Wijzigen naam

// Wijzigen naamreader
if($fldPres <> $naamreader_db) {
  //if($fldPres == '') { $fldPres = "NULL"; } else { $fldPres = "'".$fldPres."'"; }
$wijzigNaamreader = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	set naamreader = ". db_null_input($fldPres) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
	/*echo '$wijzigNaamreader = '.$wijzigNaamreader.'<br>';*/	mysqli_query($db,$wijzigNaamreader) or die (mysqli_error($db));
 }
// Einde Wijzigen naamreader

// geheel ADRES invoeren
if(!isset($adrId_db) && ( isset($fldStraat) || isset($fldNr) || isset($fldPc) || isset($fldPlaats)
  )) { // als adres niet bestaat en plaats, nr, postcode of woonplaats is ingevuld
$invoeradres = "
	INSERT INTO tblAdres
	SET relId = '".mysqli_real_escape_string($db,$recId)."', straat = ".db_null_input($fldStraat).", nr = ".db_null_input($fldNr).", pc = ".db_null_input($fldPc).", plaats = ".db_null_input($fldPlaats)."
";
		/*echo $invoeradres.'<br>';*/ mysqli_query($db,$invoeradres) or die (mysqli_error($db));
}
// Einde geheel ADRES invoeren

if(isset($adrId_db)) {

$zoek_adres_gegevens = mysqli_query($db,"
	SELECT a.straat, a.nr, a.pc, a.plaats
	FROM tblRelatie r
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
") or die(mysqli_error($db));
	while( $zag = mysqli_fetch_assoc($zoek_adres_gegevens)) { 
		$straat_db = $zag['straat'];
		$huisnr_db = $zag['nr'];
		$pc_db = $pc['pc'];
		$plaats_db = $st['plaats'];
	}

// Wijzigen straat
if($fldStraat <> $straat_db) {
  //if($fldStraat == '') { $fldStraat = 'NULL'; } else { $fldStraat = "'".$fldStraat."'"; }
$wijzigStraat = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET straat = ". db_null_input($fldStraat) ." 
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigStraat.'<br>';*/ mysqli_query($db,$wijzigStraat) or die (mysqli_error($db));
		
 }
// Einde Wijzigen straat

// Wijzigen huisnummer
if($fldNr <> $huisnr_db) {
  //if($fldNr == '') { $fldNr = 'NULL'; } else { $fldNr = "'".$fldNr."'"; }
$wijzigNummer = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET nr = ". db_null_input($fldNr) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigNummer.'<br>';*/ mysqli_query($db,$wijzigNummer) or die (mysqli_error($db));
		
 }
// Einde Wijzigen huisnummer

// Wijzigen postcode
if($fldPc <> $pc_db) {
 // if($fldPc == '') { $fldPc = 'NULL'; } else { $fldPc = "'".$fldPc."'"; }
$wijzigPostcode = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET pc = ". db_null_input($fldPc) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigPostcode.'<br>';*/ mysqli_query($db,$wijzigPostcode) or die (mysqli_error($db));
		
 }
// Einde Wijzigen postcode

// Wijzigen plaats
if($fldPlaats <> $plaats_db) {
 // if($fldPlaats == '') { $fldPlaats = 'NULL'; } else { $fldPlaats = "'".$fldPlaats."'"; }
$wijzigPlaats = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET plaats = ". db_null_input($fldPlaats) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigPlaats.'<br>';*/ mysqli_query($db,$wijzigPlaats) or die (mysqli_error($db));
		
 }
// Einde Wijzigen plaats
} // Einde if(isset($adrId_db)

// Wijzigen telefoon	
if($fldTel <> $tel_db) {
  //if($fldTel == '') { $fldTel = 'NULL'; } else { $fldTel = "'".$fldTel."'"; }
$wijzigTelefoon = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET tel = ". db_null_input($fldTel) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigTelefoon.'<br>';*/ mysqli_query($db,$wijzigTelefoon) or die (mysqli_error($db));
 }
// Einde Wijzigen telefoon

// Wijzigen actief excl. Rendac
if($fldActief <> $actief_db && !isset($rel_ren)) {
$wijzigActief = "
	UPDATE tblRelatie
	SET actief = '".mysqli_real_escape_string($db,$fldActief)."'
	WHERE relId = '".mysqli_real_escape_string($db,$recId)."'
";

		/*echo $wijzigActief.'<br>';*/ mysqli_query($db,$wijzigActief) or die (mysqli_error($db));

 }
// Einde Wijzigen actief


}	// Einde if($recId > 0)



						} // Einde foreach($multip_array as $recId => $id) ?>
					
	
