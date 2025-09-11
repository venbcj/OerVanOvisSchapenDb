<!-- 15-6-2016 : gemaakt 
22-01-2021 : Dubbele invoer ubn niet mogelijk gemaakt. sql met quotes beveiligd 
23-04-2023 key == 'txtdebPres' bestond niet en in query wijzigNaamreader werd het veld naam gewijzigd i.p.v. naamreader
07-03-2025 : In Relaties.php <input type= "hidden" name= echo "txtId_Id"; verwijderd en hier lege checkboxen gedefinieerd en wijzigingen vergeleken met data uit database -->

<?php
/* toegepast in :
	- Relaties.php */
	


foreach($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $recId => $id) {

unset($relId);

$zoek_relatie_debiteur = mysqli_query($db,"
	SELECT relId
	FROM tblRelatie
	WHERE relId = '".mysqli_real_escape_string($db,$recId)."' and relatie = 'deb'
") or die(mysqli_error($db));
	while( $zrc = mysqli_fetch_assoc($zoek_relatie_debiteur)) { 
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

    if ($key == 'txtdebUbn' && !empty($value)) 		{  $fldUbn = $value; }
    if ($key == 'txtdebNaam' && !empty($value)) 	{  $fldNaam = $value; }
    if ($key == 'txtdebPres' && !empty($value)) 	{  $fldPres = $value; }
    if ($key == 'txtdebStraat' && !empty($value)) {  $fldStraat = $value; }
    if ($key == 'txtdebNr' && !empty($value)) 		{  $fldNr = $value; }
    if ($key == 'txtdebPc' && !empty($value)) 		{  $fldPc = $value; }
    if ($key == 'txtdebPlaats' && !empty($value)) {  $fldPlaats = $value; }
    if ($key == 'txtdebTel' && !empty($value)) 		{  $fldTel = $value; }
    if ($key == 'chkdebActief' && !empty($value)) {  $fldActief = $value; }
}

if (!isset($fldPres) && isset($fldNaam)) { $fldPres = $fldNaam; }
if (!isset($fldActief)) { $fldActief = 0; }

if(isset($relId) && $recId > 0) {

/* echo $recId."<br/>";*/

$zoek_debiteur = mysqli_query($db,"
	SELECT p.ubn, p.naam, p.naamreader, a.adrId, tel, r.actief
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 left join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$recId)."'
") or die(mysqli_error($db));
	while( $zd = mysqli_fetch_assoc($zoek_debiteur)) { 
		$ubn_db = $zd['ubn'];
		$naam_db = $zd['naam'];
		$naamreader_db = $zd['naamreader'];
		$adrId_db = $zd['adrId'];
		$tel_db = $zd['tel'];
		$actief_db = $zd['actief'];

}

// Wijzigen ubn
if($fldUbn <> $ubn_db) {

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

 // if($fldUbn == '') { $fldUbn = 'NULL'; }
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
if($fldNaam <> $naam_db) {
  //if($fldNaam == '') { $fldNaam = "NULL"; } else { $fldNaam = "'".$fldNaam."'"; }
$wijzigNaam = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET naam = ". db_null_input($fldNaam) ."
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
 // if($fldNr == '') { $fldNr = 'NULL'; } else { $fldNr = "'".$fldNr."'"; }
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
  //if($fldPc == '') { $fldPc = 'NULL'; } else { $fldPc = "'".$fldPc."'"; }
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

// Wijzigen actief
  if($fldActief <> $actief_db) {
$wijzigActief = "
	UPDATE tblRelatie
	SET actief = '".mysqli_real_escape_string($db,$fldActief)."' 
	WHERE relId = '".mysqli_real_escape_string($db,$recId)."'
";
		/*echo $wijzigActief.'<br>';*/ mysqli_query($db,$wijzigActief) or die (mysqli_error($db));
 }
// Einde Wijzigen actief


}					

} ?>
					
	
