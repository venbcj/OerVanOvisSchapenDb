
<?php
/* 29-12-2023 sql beveiligd met quotes 
23-02-2025 : Lege checkboxen gedefinieerd
toegepast in :
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
foreach($multip_array as $recId => $id) {
//echo '<br>'.'$recId = '.$recId.'<br>';

unset($fldActief);

foreach($id as $key => $value) { 

if ($key == 'txtLetter' && !empty($value)) 	{ $fldLetter = $value; }
if ($key == 'txtRoep' && !empty($value)) 	{ $fldRoep = $value; }
if ($key == 'txtVgsl' && !empty($value)) 	{ $fldVgsl = $value; }
if ($key == 'txtNaam' && !empty($value)) 	{ $fldNaam = $value; }
if ($key == 'txtTel' && !empty($value))  	{ $fldTel = $value;  }
if ($key == 'txtGsm' && !empty($value))  	{ $fldGsm = $value;  }
if ($key == 'txtMail' && !empty($value)) 	{ $fldMail = $value; }
if ($key == 'txtFunct' && !empty($value))	{ $fldFunctie = $value; }
if ($key == 'chkActief' && !empty($value))	{ $fldActief = $value; }	

}

if(!isset($fldActief)) { $fldActief = 0; }

if($recId > 0) {

$zoek_persoon = mysqli_query($db,"
	SELECT letter, roep, voeg, naam, tel, gsm, mail, functie, actief
	FROM tblPersoon
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
") or die(mysqli_error($db));
	while( $zp = mysqli_fetch_assoc($zoek_persoon)) { 
		$letter_db = $zp['letter'];
		$roep_db = $zp['roep'];
		$voeg_db = $zp['voeg'];
		$naam_db = $zp['naam'];
		$tel_db = $zp['tel'];
		$gsm_db = $zp['gsm'];
		$mail_db = $zp['mail'];
		$functie_db = $zp['functie'];
		$actief_db = $zp['actief'];

	}

// Wijzigen voorletters
if($fldLetter <> $letter_db) {
$wijzigvoorletters = "
	UPDATE tblPersoon
	SET letter = ".db_null_input($fldLetter)." 
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo '$wijzigvoorletters = '. $wijzigvoorletters.'<br>';*/	mysqli_query($db,$wijzigvoorletters) or die (mysqli_error($db));


 }
//unset($letter); // Alleen nodig als een geheel record uit een tabel niet bestaat. Dat is bij contactpersonen niet het geval.
// Einde Wijzigen voorletters

// Wijzigen roepnaam
if($fldRoep <> $roep_db) {
$wijzigroepnaam = "
	UPDATE tblPersoon
	SET roep = ".db_null_input($fldRoep)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigroepnaam.'<br>';*/	mysqli_query($db,$wijzigroepnaam) or die (mysqli_error($db));
 }
//unset($roep);
// Einde Wijzigen roepnaam

// Wijzigen tussenvoegsel
if($fldVgsl <> $voeg_db) {

$wijzigtussenvoegsel = "
	UPDATE tblPersoon
	SET voeg = ".db_null_input($fldVgsl)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigtussenvoegsel.'<br>';*/	mysqli_query($db,$wijzigtussenvoegsel) or die (mysqli_error($db));

 }
//unset($voeg);
// Einde Wijzigen tussenvoegsel

// Wijzigen naam
if($fldNaam <> $naam_db) {

$wijzignaam = "
	UPDATE tblPersoon
	SET naam = ".db_null_input($fldNaam)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzignaam.'<br>';*/	mysqli_query($db,$wijzignaam) or die (mysqli_error($db));

 }
//unset($naam);
// Einde Wijzigen naam

// Wijzigen telefoon
if($fldTel <> $tel_db) {

$wijzigtelefoon = "
	UPDATE tblPersoon
	SET tel = ".db_null_input($fldTel)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigtelefoon.'<br>';*/	mysqli_query($db,$wijzigtelefoon) or die (mysqli_error($db));

 }
//unset($tel);
// Einde Wijzigen telefoon


// Wijzigen mobiel
if($fldGsm <> $gsm_db) {

$wijzigmobiel = "
	UPDATE tblPersoon
	SET gsm = ".db_null_input($fldGsm)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigmobiel.'<br>';*/	mysqli_query($db,$wijzigmobiel) or die (mysqli_error($db));

 }
//unset($gsm);
// Einde Wijzigen mobiel


// Wijzigen email
if($fldMail <> $mail_db) {

$wijzigemail = "
	UPDATE tblPersoon
	SET mail = ".db_null_input($fldMail)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigemail.'<br>';*/	mysqli_query($db,$wijzigemail) or die (mysqli_error($db));

 }
//unset($mail);
// Einde Wijzigen email


// Wijzigen functie
if($fldFunctie <> $functie_db) {

$wijzigfunctie = "
	UPDATE tblPersoon
	SET functie = ".db_null_input($fldFunctie)."
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo $wijzigfunctie.'<br>';*/	mysqli_query($db,$wijzigfunctie) or die (mysqli_error($db));

 }
//unset($func);
// Einde Wijzigen functie


// Wijzigen actief
  if($fldActief <> $actief_db) {
$wijzigactief = "
	UPDATE tblPersoon
	SET actief = '".$fldActief."'
	WHERE persId = '".mysqli_real_escape_string($db,$recId)."'
";
/*echo '$wijzigactief = '.$wijzigactief.'<br>';*/	mysqli_query($db,$wijzigactief) or die (mysqli_error($db));

 }
// Einde Wijzigen actief


}					



} ?>
					
	