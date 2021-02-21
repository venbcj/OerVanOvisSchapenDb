
<?php
/* toegepast in :
	- Contact.php */
	
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


if($key == 'txtPersId') {
foreach($id as $key => $value) {

	if ($key == 'txtPersId' && !empty($value)) { $updId = $value; /*echo $key.'='.$value."<br/>";*/ }   

if ($key == 'txtLetter' && !empty($value)) {  $fldLetter = $value; $updLetter = "'".$value."'";} else if ($key == 'txtLetter' && empty($value)) { $fldLetter = ''; $updLetter = 'NULL'; }
if ($key == 'txtRoep' && !empty($value)) {  $fldRoep = $value; $updRoep = "'".$value."'";} else if ($key == 'txtRoep' && empty($value)) { $fldRoep = ''; $updRoep = 'NULL'; }
if ($key == 'txtVgsl' && !empty($value)) {  $fldVgsl = $value; $updVgsl = "'".$value."'";} else if ($key == 'txtVgsl' && empty($value)) { $fldVgsl = ''; $updVgsl = 'NULL'; }
if ($key == 'txtNaam' && !empty($value)) {  $fldNaam = $value; $updNaam = "'".$value."'";} else if ($key == 'txtNaam' && empty($value)) { $fldNaam = ''; $updNaam = 'NULL'; }
if ($key == 'txtTel' && !empty($value))  {  $fldTel = $value;  $updTel = "'".$value."'";}  else if ($key == 'txtTel' && empty($value))  { $fldTel = '';  $updTel = 'NULL';  }
if ($key == 'txtGsm' && !empty($value))  {  $fldGsm = $value;  $updGsm = "'".$value."'";}  else if ($key == 'txtGsm' && empty($value))  { $fldGsm = '';  $updGsm = 'NULL';  }
if ($key == 'txtMail' && !empty($value)) {  $fldMail = $value; $updMail = "'".$value."'";} else if ($key == 'txtMail' && empty($value)) { $fldMail = ''; $updMail = 'NULL'; }
if ($key == 'txtFunct' && !empty($value)){  $fldFunctie = $value; $updFunctie = "'".$value."'";} else if ($key == 'txtFunct' && empty($value)){ $fldFunctie = ''; $updFunctie = 'NULL'; }
if ($key == 'chkActief' && !empty($value)){ $fldActief = $value; }						   else { $fldActief = 0; }	

}

if(isset($updId)) {

// Wijzigen voorletters
$zoek_voorletters = mysqli_query($db,"
	select letter
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_voorletters)) { $letter = $st['letter']; } if(!isset($letter)) { $letter = ''; }
if($fldLetter <> $letter) {
$wijzigvoorletters = "
	update tblPersoon
	set letter = ".$updLetter." 
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
		//mysqli_query($db,$wijzigvoorletters) or die (mysqli_error($db));
echo $wijzigvoorletters;

 }
//unset($letter); // Alleen nodig als een geheel record uit een tabel niet bestaat. Dat is bij contactpersonen niet het geval.
// Einde Wijzigen voorletters

// Wijzigen roepnaam
$zoek_roep = mysqli_query($db,"
	select roep
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $rp = mysqli_fetch_assoc($zoek_roep)) { $roep = $rp['roep']; } if(!isset($roep)) { $roep = ''; }
if($fldRoep <> $roep) {
$wijzigroepnaam = "
	update tblPersoon
	set roep = ".$updRoep."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigroepnaam;
	mysqli_query($db,$wijzigroepnaam) or die (mysqli_error($db));
 }
//unset($roep);
// Einde Wijzigen roepnaam

// Wijzigen tussenvoegsel
$zoek_tussenvoegsel = mysqli_query($db,"
	select voeg
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $vg = mysqli_fetch_assoc($zoek_tussenvoegsel)) { $voeg = $vg['voeg']; } if(!isset($voeg)) { $voeg = ''; }
if($fldVgsl <> $voeg) {

$wijzigtussenvoegsel = "
	update tblPersoon
	set voeg = ".$updVgsl."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigtussenvoegsel;
	mysqli_query($db,$wijzigtussenvoegsel) or die (mysqli_error($db));

 }
//unset($voeg);
// Einde Wijzigen tussenvoegsel

// Wijzigen naam
$zoek_naam = mysqli_query($db,"
	select naam
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_naam)) { $naam = $st['naam']; } if(!isset($naam)) { $naam = ''; }
if($fldNaam <> $naam) {

$wijzignaam = "
	update tblPersoon
	set naam = ".$updNaam."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzignaam;
	mysqli_query($db,$wijzignaam) or die (mysqli_error($db));

 }
//unset($naam);
// Einde Wijzigen naam

// Wijzigen telefoon
$zoek_telefoon = mysqli_query($db,"
	select tel
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_telefoon)) { $tel = $st['tel']; } if(!isset($tel)) { $tel = ''; }
if($fldTel <> $tel) {

$wijzigtelefoon = "
	update tblPersoon
	set tel = ".$updTel."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigtelefoon;
	mysqli_query($db,$wijzigtelefoon) or die (mysqli_error($db));

 }
//unset($tel);
// Einde Wijzigen telefoon


// Wijzigen mobiel
$zoek_mobiel = mysqli_query($db,"
	select gsm
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_mobiel)) { $gsm = $st['gsm']; } if(!isset($gsm)) { $gsm = ''; }
if($fldGsm <> $gsm) {

$wijzigmobiel = "
	update tblPersoon
	set gsm = ".$updGsm."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigmobiel;
	mysqli_query($db,$wijzigmobiel) or die (mysqli_error($db));

 }
//unset($gsm);
// Einde Wijzigen mobiel


// Wijzigen email
$zoek_email = mysqli_query($db,"
	select mail
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_email)) { $mail = $st['mail']; } if(!isset($mail)) { $mail = ''; }
if($fldMail <> $mail) {

$wijzigemail = "
	update tblPersoon
	set mail = ".$updMail."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigemail;
	mysqli_query($db,$wijzigemail) or die (mysqli_error($db));

 }
//unset($mail);
// Einde Wijzigen email


// Wijzigen functie
$zoek_functie = mysqli_query($db,"
	select functie
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $st = mysqli_fetch_assoc($zoek_functie)) { $func = $st['functie']; } if(!isset($func)) { $func = ''; }
if($fldFunctie <> $func) {

$wijzigfunctie = "
	update tblPersoon
	set functie = ".$updFunctie."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigfunctie;
	mysqli_query($db,$wijzigfunctie) or die (mysqli_error($db));

 }
//unset($func);
// Einde Wijzigen functie


// Wijzigen actief
$zoek_actief = mysqli_query($db,"
	select actief
	from tblPersoon
	where persId = ".mysqli_real_escape_string($db,$updId)."
") or die(mysqli_error($db));
	while( $ac = mysqli_fetch_assoc($zoek_actief)) { $actief = $ac['actief']; }

  if($fldActief <> $actief) {
$wijzigactief = "
	update tblPersoon
	set actief = ".$fldActief."
	where persId = ".mysqli_real_escape_string($db,$updId)."
";
//echo $wijzigactief;
	mysqli_query($db,$wijzigactief) or die (mysqli_error($db));

 }
// Einde Wijzigen actief


}					



}




	
	
						}
}
?>
					
	