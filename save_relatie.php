
<?php
/* toegepast in :
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
	select a.adrId
	from tblAdres a
	where a.relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $ad = mysqli_fetch_assoc($zoek_adres)) { $adrId = $ad['adrId']; }
// Invoer adres als deze nog niet bestaat
if(!isset($adrId) && ( // als adres niet bestaat en plaats, nr, postcode of woonplaats is ingevuld
  $fldStraat != '' || $fldNr != '' || $fldPc != '' || $fldPlaats != ''
  )) {
$invoeradres = "
	insert into tblAdres
	set relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$invoeradres) or die (mysqli_error($db));
//echo $invoeradres;
}
// Einde Invoer adres als deze nog niet bestaat
// Wijzigen straat
$zoek_straat = mysqli_query($db,"
	select a.straat
	from tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	where r.relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_straat)) { $straat = $st['straat']; } if(!isset($straat)) { $straat = ''; }
if(isset($fldStraat) && $fldStraat <> $straat) {
  if($fldStraat == '') { $fldStraat = 'NULL'; } else { $fldStraat = "'".$fldStraat."'"; }
$wijzigstraat = "
	update tblAdres
	set straat = ".$fldStraat." 
	where relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$wijzigstraat) or die (mysqli_error($db));
//echo $wijzigstraat;	
 }
unset($straat); // Als een adres in een volgend record (partij) niet bestaat mag $straat ook niet meer bestaan
// Einde Wijzigen straat

// Wijzigen huisnummer
$zoek_nr = mysqli_query($db,"
	select a.nr
	from tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	 join tblAdres a on (a.relId = r.relId)
	where r.relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $nr = mysqli_fetch_assoc($zoek_nr)) { $huisnr = $nr['nr']; } if(!isset($huisnr)) { $huisnr = ''; }
if(isset($fldNr) && $fldNr <> $huisnr) {
  if($fldNr == '') { $fldNr = 'NULL'; } else { $fldNr = "'".$fldNr."'"; }
$wijzignummer = "
	update tblAdres
	set nr = ".$fldNr."
	where relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$wijzignummer) or die (mysqli_error($db));
//echo $wijzignummer;
 }
unset($huisnr);
// Einde Wijzigen huisnummer

// Wijzigen postcode
$zoek_postcode = mysqli_query($db,"
	select a.pc
	from tblRelatie r
	 join tblAdres a on (a.relId = r.relId)
	where r.relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $pc = mysqli_fetch_assoc($zoek_postcode)) { $postcode = $pc['pc']; } if(!isset($postcode)) { $postcode = ''; }
if(isset($fldPc) && $fldPc <> $postcode) {
  if($fldPc == '') { $fldPc = 'NULL'; } else { $fldPc = "'".$fldPc."'"; }
$wijzigpostcode = "
	update tblAdres
	set pc = ".$fldPc."
	where relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$wijzigpostcode) or die (mysqli_error($db));
//echo $wijzigpostcode;
 }
unset($postcode);
// Einde Wijzigen postcode

// Wijzigen plaats
$zoek_plaats = mysqli_query($db,"
	select a.plaats
	from tblRelatie r
	 join tblAdres a on (a.relId = r.relId)
	where r.relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_plaats)) { $plaats = $st['plaats']; } if(!isset($plaats)) { $plaats = ''; }
if(isset($fldPlaats) && $fldPlaats <> $plaats) {
  if($fldPlaats == '') { $fldPlaats = 'NULL'; } else { $fldPlaats = "'".$fldPlaats."'"; }
$wijzigplaats = "
	update tblAdres
	set plaats = ".$fldPlaats."
	where relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$wijzigplaats) or die (mysqli_error($db));
//echo $wijzigplaats;
 }
unset($plaats);
// Einde Wijzigen plaats
// Einde Wijzigen ADRES



// Wijzigen actief
$zoek_actief = mysqli_query($db,"
	select actief
	from tblRelatie
	where relId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $ac = mysqli_fetch_assoc($zoek_actief)) { $actief = $ac['actief']; } if(!isset($actief)) { $actief = ''; }
	
if(isset($fldActief) ) { $fldActief = 1; } else { $fldActief = 0; }
  if($fldActief <> $actief) {
$wijzigactief = "
	update tblRelatie
	set actief = ".$fldActief."
	where relId = ".mysqli_real_escape_string($db,$updId)."
";
		mysqli_query($db,$wijzigactief) or die (mysqli_error($db));
//echo $wijzigactief;
 }
unset($fldActief); // Als een volgend record (relatie) niet actief is mag $fldActief niet meer bestaan.
// Einde Wijzigen actief


}					



}




	
	
						}
}
?>
					
	