
<?php
/* 29-12-2023 : sql beveiligd met quotes en db_null_input() en veld actief bij Rendac niet wijigbaar gemaakt 
 toegepast in :
	- Relatie.php */
	
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


if($key == 'txtrId') {
foreach($id as $key => $value) {

	if ($key == 'txtrId' && !empty($value)) { $updId = $value; /*echo $key.'='.$value."<br/>";*/ }   

    if ($key == 'txtStraat' && !empty($value)) {  $fldStraat = $value;} else if ($key == 'txtStraat' && empty($value)) { $fldStraat = ''; }
    if ($key == 'txtNr' && !empty($value)) {  $fldNr = $value;} else if ($key == 'txtNr' && empty($value)) { $fldNr = ''; }
    if ($key == 'txtPc' && !empty($value)) {  $fldPc = $value;} else if ($key == 'txtPc' && empty($value)) { $fldPc = ''; }
    if ($key == 'txtPlaats' && !empty($value)) {  $fldPlaats = $value;} else if ($key == 'txtPlaats' && empty($value)) { $fldPlaats = ''; }
    if ($key == 'chkActief' && !empty($value)) {  $fldActief = $value;} //else if ($key == 'chkActief' && empty($value)) { $fldActief = 0; }	

	
}

if(isset($updId)) {

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
		mysqli_query($db,$invoeradres) or die (mysqli_error($db));
//echo $invoeradres;
}
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
  
$wijzigstraat = "
	UPDATE tblAdres
	SET straat = ".db_null_input($fldStraat)." 
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzigstraat) or die (mysqli_error($db));
//echo $wijzigstraat;	
 }
unset($straat); // Als een adres in een volgend record (partij) niet bestaat mag $straat ook niet meer bestaan
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

$wijzignummer = "
	UPDATE tblAdres
	SET nr = ".db_null_input($fldNr)."
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzignummer) or die (mysqli_error($db));
//echo $wijzignummer;
 }
unset($huisnr);
// Einde Wijzigen huisnummer

// Wijzigen postcode
$zoek_postcode = mysqli_query($db,"
	SELECT a.pc
	FROM tblRelatie r
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $pc = mysqli_fetch_assoc($zoek_postcode)) { $postcode = $pc['pc']; } if(!isset($postcode)) { $postcode = ''; }
if(isset($fldPc) && $fldPc <> $postcode) {

$wijzigpostcode = "
	UPDATE tblAdres
	SET pc = ".db_null_input($fldPc)."
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzigpostcode) or die (mysqli_error($db));
//echo $wijzigpostcode;
 }
unset($postcode);
// Einde Wijzigen postcode

// Wijzigen plaats
$zoek_plaats = mysqli_query($db,"
	SELECT a.plaats
	FROM tblRelatie r
	 join tblAdres a on (a.relId = r.relId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_plaats)) { $plaats = $st['plaats']; } if(!isset($plaats)) { $plaats = ''; }
if(isset($fldPlaats) && $fldPlaats <> $plaats) {

$wijzigplaats = "
	UPDATE tblAdres
	SET plaats = ".db_null_input($fldPlaats)."
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
";
		mysqli_query($db,$wijzigplaats) or die (mysqli_error($db));
//echo $wijzigplaats;
 }
unset($plaats);
// Einde Wijzigen plaats
// Einde Wijzigen ADRES



// Wijzigen actief excl. Rendac
$zoek_rendac = mysqli_query($db,"
	SELECT relId
	FROM tblRelatie r
	 join tblPartij p on (r.partId = p.partId)
	WHERE r.relId = '".mysqli_real_escape_string($db,$updId)."' and p.naam = 'Rendac'
") or die(mysqli_error($db));
	while( $zr = mysqli_fetch_assoc($zoek_rendac)) { $rel_ren = $zr['relId']; }


$zoek_actief = mysqli_query($db,"
	SELECT actief
	FROM tblRelatie
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
") or die(mysqli_error($db));
	while( $ac = mysqli_fetch_assoc($zoek_actief)) { $actief = $ac['actief']; } if(!isset($actief)) { $actief = ''; }
	


$wijzigactief = "
	UPDATE tblRelatie
	SET actief = ".db_null_input($fldActief)."
	WHERE relId = '".mysqli_real_escape_string($db,$updId)."'
";

if(!isset($rel_ren)) {
		mysqli_query($db,$wijzigactief) or die (mysqli_error($db));
	}
	unset($rel_ren);
//echo $wijzigactief;

unset($fldActief); // Als een volgend record (relatie) niet actief is mag $fldActief niet meer bestaan.
// Einde Wijzigen actief


}					



}




	
	
						}
}
?>
					
	