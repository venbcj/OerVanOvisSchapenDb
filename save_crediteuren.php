<!-- 15-6-2016 : gemaakt
	 9-8-2020 : veld naamreader toegevoegd -->

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
foreach($multip_array as $id) {  


 foreach($id as $key => $value) { 


if($key == 'txtcreId') {
foreach($id as $key => $value) {

	if ($key == 'txtcreId' && !empty($value)) { $updId = $value; /*echo $key.'='.$value."<br/>";*/ }   

    if ($key == 'txtcreUbn' && !empty($value)) {  $fldUbn = $value; } else if ($key == 'txtcreUbn' && empty($value)) { $fldUbn = ''; }
    if ($key == 'txtcreNaam' && !empty($value)) {  $fldNaam = $value; } else if ($key == 'txtcreNaam' && empty($value)) { $fldNaam = ''; }
    
    if ($key == 'txtcrePres' && !empty($value)) {  $fldPres = $value; } else if ($key == 'txtcrePres' && empty($value)) { $fldPres = $fldNaam; }

    if ($key == 'txtcreStraat' && !empty($value)) {  $fldStraat = $value;} else if ($key == 'txtcreStraat' && empty($value)) { $fldStraat = ''; }
    if ($key == 'txtcreNr' && !empty($value)) {  $fldNr = $value;} else if ($key == 'txtcreNr' && empty($value)) { $fldNr = ''; }

    if ($key == 'txtcrePc' && !empty($value)) {  $fldPc = $value;} else if ($key == 'txtcrePc' && empty($value)) { $fldPc = ''; }
    if ($key == 'txtcrePlaats' && !empty($value)) {  $fldPlaats = $value;} else if ($key == 'txtcrePlaats' && empty($value)) { $fldPlaats = ''; }

    if ($key == 'txtcreTel' && !empty($value)) {  $fldTel = $value;} else if ($key == 'txtcreTel' && empty($value)) { $fldTel = ''; }
    if ($key == 'chkcreActief' && !empty($value)) {  $fldActief = $value;} //else if ($key == 'chkActief' && empty($value)) { $fldActief = 0; }	

	
}
/*
 echo $updId."<br/>";
					echo $fldUitv."<br/>";
					echo $fldPil."<br/>";*/
if(isset($updId)) {
// Wijzigen ubn
$zoek_ubn = mysqli_query($db,"
	SELECT ubn
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."' and (r.uitval != 1 or isnull(uitval))
") or die(mysqli_error($db));
	while( $ub = mysqli_fetch_assoc($zoek_ubn)) { $p_ubn = $ub['ubn']; }  if(!isset($p_ubn)) { $p_ubn = ''; }
	
if(isset($fldUbn) && $fldUbn <> $p_ubn) {

if($fldUbn != '') {
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
$wijzigrelatie = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET ubn = ". db_null_input($fldUbn) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo 'van : '.$p_ubn.' naar : '.$fldUbn.' bij : '.$updId.'<br>';*/ mysqli_query($db,$wijzigrelatie) or die (mysqli_error($db));
		//echo $wijzigrelatie;
 }

}
unset($fldUbn); unset($p_ubn);
// Einde Wijzigen ubn

// Wijzigen naam
$zoek_naam = mysqli_query($db,"
	SELECT naam
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $nm = mysqli_fetch_assoc($zoek_naam)) { $naam = $nm['naam']; }  if(!isset($naam)) { $naam = ''; }
	
if(isset($fldNaam) && $fldNaam <> $naam) {
 // if($fldNaam == '') { $fldNaam = "NULL"; } else { $fldNaam = "'".$fldNaam."'"; }
$wijzigrelatie = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	set naam = ". db_null_input($fldNaam) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzigrelatie) or die (mysqli_error($db));
 }
unset($fldNaam); unset($naam);
// Einde Wijzigen naam

// Wijzigen naamreader
$zoek_naam = mysqli_query($db,"
	SELECT naamreader
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $nm = mysqli_fetch_assoc($zoek_naam)) { $naamreader = $nm['naamreader']; }  if(!isset($naamreader)) { $naamreader = ''; }
	
if(isset($fldPres) && $fldPres <> $naamreader) {
  //if($fldPres == '') { $fldPres = "NULL"; } else { $fldPres = "'".$fldPres."'"; }
$wijzigrelatie = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	set naam = ". db_null_input($fldPres) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzigrelatie) or die (mysqli_error($db));
 }
unset($fldPres); unset($naamreader);
// Einde Wijzigen naamreader

// Wijzigen ADRES
$zoek_adres = mysqli_query($db,"
	SELECT a.adrId
	FROM tblAdres a
	WHERE a.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $ad = mysqli_fetch_assoc($zoek_adres)) { $adrId = $ad['adrId']; }
// Invoer adres als deze nog niet bestaat
if(!isset($adrId) && ( // als adres niet bestaat en plaats, nr, postcode of woonplaats is ingevuld
  $fldStraat != '' || $fldNr != '' || $fldPc != '' || $fldPlaats != ''
  )) {
$invoeradres = "
	INSERT INTO tblAdres
	SET relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $invoeradres.'<br>';*/ mysqli_query($db,$invoeradres) or die (mysqli_error($db));
}
unset($adrId);
// Einde Invoer adres als deze nog niet bestaat
// Wijzigen straat
$zoek_straat = mysqli_query($db,"
	SELECT a.straat
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_straat)) { $straat = $st['straat']; } if(!isset($straat)) { $straat = ''; }
if(isset($fldStraat) && $fldStraat <> $straat) {
  //if($fldStraat == '') { $fldStraat = 'NULL'; } else { $fldStraat = "'".$fldStraat."'"; }
$wijzigstraat = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET straat = ". db_null_input($fldStraat) ." 
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzigstraat.'<br>';*/ mysqli_query($db,$wijzigstraat) or die (mysqli_error($db));
		
 }
unset($straat); // Als een adres in een volgend record (partij) niet bestaat mag $straat ook niet meer bestaan. Variabele $fldStraat wordt altijd opnieuw gevuld. unset($fldStraat) is dus n.v.t.
// Einde Wijzigen straat

// Wijzigen huisnummer
$zoek_nr = mysqli_query($db,"
	SELECT a.nr
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $nr = mysqli_fetch_assoc($zoek_nr)) { $huisnr = $nr['nr']; } if(!isset($huisnr)) { $huisnr = ''; }
if(isset($fldNr) && $fldNr <> $huisnr) {
  //if($fldNr == '') { $fldNr = 'NULL'; } else { $fldNr = "'".$fldNr."'"; }
$wijzignummer = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET nr = ". db_null_input($fldNr) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzignummer.'<br>';*/ mysqli_query($db,$wijzignummer) or die (mysqli_error($db));
		
 }
unset($huisnr);
// Einde Wijzigen huisnummer

// Wijzigen postcode
$zoek_postcode = mysqli_query($db,"
	SELECT a.pc
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $pc = mysqli_fetch_assoc($zoek_postcode)) { $postcode = $pc['pc']; } if(!isset($postcode)) { $postcode = ''; }
if(isset($fldPc) && $fldPc <> $postcode) {
 // if($fldPc == '') { $fldPc = 'NULL'; } else { $fldPc = "'".$fldPc."'"; }
$wijzigpostcode = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	set pc = ". db_null_input($fldPc) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzigpostcode.'<br>';*/ mysqli_query($db,$wijzigpostcode) or die (mysqli_error($db));
		
 }
unset($postcode);
// Einde Wijzigen postcode

// Wijzigen plaats
$zoek_plaats = mysqli_query($db,"
	SELECT a.plaats
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_plaats)) { $plaats = $st['plaats']; } if(!isset($plaats)) { $plaats = ''; }
if(isset($fldPlaats) && $fldPlaats <> $plaats) {
 // if($fldPlaats == '') { $fldPlaats = 'NULL'; } else { $fldPlaats = "'".$fldPlaats."'"; }
$wijzigplaats = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	SET plaats = ". db_null_input($fldPlaats) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzigplaats.'<br>';*/ mysqli_query($db,$wijzigplaats) or die (mysqli_error($db));
		
 }
unset($plaats);
// Einde Wijzigen plaats
// Einde Wijzigen ADRES

// Wijzigen telefoon
$zoek_telefoon = mysqli_query($db,"
	SELECT tel
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $tl = mysqli_fetch_assoc($zoek_telefoon)) { $tel = $tl['tel']; } if(!isset($tel)) { $tel = ''; }
	
if(isset($fldTel) && $fldTel <> $tel) {
  //if($fldTel == '') { $fldTel = 'NULL'; } else { $fldTel = "'".$fldTel."'"; }
$wijzigtelefoon = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET tel = ". db_null_input($fldTel) ."
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzigtelefoon.'<br>';*/ mysqli_query($db,$wijzigtelefoon) or die (mysqli_error($db));
 }
unset($tel);
// Einde Wijzigen telefoon

// Wijzigen actief
$zoek_actief = mysqli_query($db,"
	SELECT r.actief
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $ac = mysqli_fetch_assoc($zoek_actief)) { $actief = $ac['actief']; } if(!isset($actief)) { $actief = ''; }
	
if(isset($fldActief) ) { $fldActief = 1; } else { $fldActief = 0; }
  if($fldActief <> $actief) {
$wijzigactief = "
	UPDATE tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	SET r.actief = '".mysqli_real_escape_string($db,$fldActief)."'
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
";
		/*echo $wijzigactief.'<br>';*/ mysqli_query($db,$wijzigactief) or die (mysqli_error($db));
 }
unset($fldActief); // Als een volgend record (relatie) niet actief is mag $fldActief niet meer bestaan.
// Einde Wijzigen actief


}					



/*
if($fldActief <> $ctrActief) {
	$update_ras = "Update tblRasuser SET actief = $fldActief WHERE rasId = '$updId' 	";
		mysqli_query($db,$update_ras) or die (mysqli_error($db)); header("Location:".$url."Ras.php"); 
		//echo 'wijzig Actief naar '.$fldActief.' bij '.$updId."<br/>";
 }  */

}




	
	
						}
}
?>
					
	