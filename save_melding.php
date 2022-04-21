<!-- 10-11-2014 gemaakt 
5-12-2016 : kzlPartij gesplitst in kzlHerk en kzlBest 	9-2-2017 ctr-velden verwijderd 
5-5-2017 : Controle bij wijzigen datum aangepast van $updDay < $last_day naar $updDay > $last_day
26-1-2018 : Bij verwijderen melding wordt kzlBest niet meer leeggemaakt 
19-2-2022 : SQL beveiligd met quotes 
4-4-2022 : Controle $zoek_laatste_datum_stalaf uitgezet want reden van deze controle onbekend -->

<?php
/*Save_Melding.php toegpast in :
	- MeldAanvoer.php
	- MeldAfvoer.php
	- MeldGeboortes.php
	- MeldUitval.php	*/

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
#echo $recId.'<br>';
	

unset($updSkip);

  foreach($id as $key => $value) {
	//if ($key == 'txtRequest' ) { foreach($id as $key => $value) { echo $key .' = '. $value. "<br><br>";  } }
  
 // echo $key .' = '. $value. "<br><br>";
  
	if ($key == 'kzlDef' ) { /*echo $key.'='.$value." &nbsp&nbsp  ";*/ $updDef = $value; }
	
	if ($key == 'txtSchaapdm' && !empty($value)) { /*echo $key.'='.$value.'<br>';*/ $txtDag = $value; $dag = date_create($value); $updDay =  date_format($dag,'Y-m-d');
																				 $updDag =  date_format($dag,'d-m-Y');	}

	if ($key == 'txtLevnr' && !empty($value)) { /*echo $key.'='.$value.'<br>';*/ $updLevnr = $value; } // in MeldGeboortes.php en mogelijk ook in MeldAanvoer.php
	
	if ($key == 'kzlSekse' && !empty($value)) { $updSekse = $value; }

	if ($key == 'kzlHerk' && !empty($value))  { $updHerk = $value; } else if ($key == 'kzlHerk' && empty($value))  { $updHerk = 'leegkeuzelijst'; }  // in MeldAanvoer.php
	if ($key == 'kzlBest' && !empty($value))  { /*echo $key.'='.$value.'<br>';*/ $updBest = $value; }  // in MeldAfvoer.php dus niet voor MeldUitval.php !!!
	//else if ($key == 'kzlBest' && empty($value))  { $kzlBest = 'leeg'; }  // in MeldAfvoer.php dus niet voor MeldUitval.php !!!
	
	if ($key == 'chbSkip' ) { $updSkip = $value;  /*echo $key.'='.$value; echo "<br/>";*/  }  
	
									}
if(!isset($updSkip)) { $updSkip = 0; }

if(isset($recId) and $recId > 0) {

//echo '$updSkip ='.$updSkip; echo "<br/>"; #/#

/****** CONTROLE DATUM *******/

$nummer_van_datum = intval(str_replace('-','',$txtDag));
//echo '$txtDag '.str_replace('-','',$txtDag).' = '.intval(str_replace('-','',$txtDag)).'<br>'; #/#


/* Laatste datum zoeken ter controle bij afvoer bedrijf */
/*if($code == 'AFV' || $code == 'DOO') {
$zoek_laatste_datum_stalaf = mysqli_query($db,"
SELECT max(datum) date, date_format(max(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join (
	SELECT st.stalId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	 join tblMelding m on (m.hisId = h.hisId)
	WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
") or die (mysqli_error($db));
	while( $mi = mysqli_fetch_assoc($zoek_laatste_datum_stalaf)) { $last_day = $mi['date']; $laatste_dag = $mi['datum']; }
}*/
/* Einde Laatste datum zoeken ter controle bij afvoer bedrijf */	

/* Eerste datum zoeken ter controle bij aanvoer bedrijf */
if($code == 'AAN' || $code == 'GER') {
$zoek_eerste_datum_stalop = mysqli_query($db,"
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join (
	SELECT st.stalId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	 join tblMelding m on (m.hisId = h.hisId)
	WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
") or die (mysqli_error($db));
	while( $mi = mysqli_fetch_assoc($zoek_eerste_datum_stalop)) { $first_day = $mi['date']; $eerste_dag = $mi['datum']; }
}
/* Einde Eerste datum zoeken ter controle bij aanvoer bedrijf */
	
/* Maximale datum RVO bepalen ter controle */	
if($code == 'AAN' || $code == 'GER' || $code == 'DOO') { $maxday_rvo = date("Y-m-d"); }
$overovermorgen = mktime(0, 0, 0, date("m")  , date("d")+3, date("Y"));
if($code == 'AFV') { $maxday_rvo = date('Y-m-d', $overovermorgen); }
/* Einde Maximale datum RVO bepalen ter controle */

if($nummer_van_datum == 0) 
{
	$wrong_dag = "De datum is onjuist";
}
elseif (isset($first_day) && $updDay < $first_day)
{
	$wrong_dag = "De datum (".$updDag.") kan niet voor ".$eerste_dag." liggen";
}
/*elseif (isset($last_day) && $updDay > $last_day)
{
	$wrong_dag = "De datum (".$updDag.") kan niet na ".$laatste_dag." liggen";
}*/
elseif (isset($maxday_rvo) && $updDay > $maxday_rvo)
{
	$wrong_dag = $txtDag." ligt voor RVO te ver in de toekomst";
}

//if(isset($wrong_dag)) { echo $wrong_dag.'<br>'; } #/#

/****** EINDE CONTROLE DATUM *******/

/****** CONTROLE LEVENSNUMMER *******/
if(isset($updLevnr)) { // Bestaat alleen bij Geboortes en Aanvoer
// Controle op duplicaten
$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db,$updLevnr)."' 
") or die (mysqli_error($db));
	$zs = mysqli_fetch_assoc($zoek_schaapId); $schaapId = $zs['schaapId'];

$count_levnr = mysqli_query($db,"
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db,$updLevnr)."' and schaapId <> '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
	$row = mysqli_fetch_assoc($count_levnr); $levnr_exist = $row['aant'];
// Einde Controle op duplicaten 
if(intval($updLevnr) == 0 ) // levensnummer is 000000000000
		{ if(isset($wrong_dag))		{ $wrong_levnr = $wrong_dag." en het levensnummer is onjuist";	}
		  else				{	$wrong_levnr = "Het levensnummer is onjuist";	}
		}
else if ($levnr_exist > 0) 
		{ if(isset($wrong_dag)) 	{ $wrong_levnr = $wrong_dag." en levensummer ".$updLevnr." bestaat al"; }
		  else 				{ $wrong_levnr = "Levensummer ".$updLevnr." bestaat al";	}
		}
else if (strlen($updLevnr) <> 12)
		{ if(isset($wrong_dag))  	{ $wrong_levnr = $wrong_dag." en ".$updLevnr." is geen 12 karakters lang";	} 
		  else				{	$wrong_levnr = $updLevnr." is geen 12 karakters lang";	}
		}
else if (numeriek($updLevnr) == 1) 
		{ if(isset($wrong_dag))		{ $wrong_levnr = $wrong_dag." en ".$updLevnr." bevat een letter";	}
		  else				{	$wrong_levnr = $updLevnr." bevat een letter";	}
		}
//if(isset($wrong_levnr)) { echo $wrong_levnr.'<br>'; } #/#
}
/****** EINDE CONTROLE LEVENSNUMMER *******/





$zoek_in_database = mysqli_query($db, "
SELECT r.reqId, r.code, r.def, m.skip, m.fout, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while( $co = mysqli_fetch_assoc($zoek_in_database)) { 
		$reqId = $co['reqId']; 
		$code = $co['code']; 
		$def_db = $co['def']; 
		$skip_db = $co['skip']; 
		$fout_db = $co['fout'];  //echo' $fout_db = '.$fout_db.'<br>';
		$datum_db = $co['datum']; 
		$Levnr_db = $co['levensnummer'];
		$sekse_db = $co['geslacht'];
		$herk_db = $co['rel_herk'];
		$best_db = $co['rel_best']; 
	}

// Als verwijderd wordt hersteld bestaat kzlBest niet maar de bestemming in de database mogelijk wel en dus $updBest dan ook !!
// Dit t.b.v. $wrong_partij
if($updSkip == 0 && $skip_db == 1) {
$zoek_bestemming_in_db = mysqli_query($db, "
SELECT st.rel_best
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while( $zbid = mysqli_fetch_assoc($zoek_bestemming_in_db)) { $updBest = $zbid['rel_best']; }
}


// Wijzigen keuze 'controle' versus 'vastleggen'		
	if(isset($updDef) && $updDef <> $def_db) { //echo $updDef." bij ".$reqId."<br>";
		$upd_tblRequest = "UPDATE tblRequest SET def = '".mysqli_real_escape_string($db,$updDef)."' WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' ";	
/*echo $upd_tblRequest.'<br>';*/			mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	}
	//unset ($reqId); //Hiermee wordt het requestId maar 1x doorlopen en is t.b.v. wijzigen tblRequest i.p.v. elke regel uit tblMeldingen


// CONTROLE op gewijzigde velden

// Wijzigen datum
//if(!isset($first_day)) {$first_day = date_format(date_create('11-09-1973'), 'Y-m-d');} // tbv MeldGeboortes.php daar bestaat geen minDag
	
if (!empty($updDay) && $updDay <> $datum_db && !isset($wrong_dag))
{

 $upd_tblHistorie = "
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".mysqli_real_escape_string($db,$updDay)."'
 WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."' 
 ";
		mysqli_query($db,$upd_tblHistorie) or die (mysqli_error($db));
}


// Wijzigen levensnummer
if (isset($updLevnr) && $updLevnr <> $Levnr_db && !isset($wrong_levnr))
{

	$upd_tblSchaap = "UPDATE tblSchaap SET levensnummer = '".mysqli_real_escape_string($db,$updLevnr)."' WHERE levensnummer = '".mysqli_real_escape_string($db,$Levnr_db)."' ";	
/*echo $upd_tblSchaap.'<br>';	*/	mysqli_query($db,$upd_tblSchaap) or die (mysqli_error($db));
		
}

unset($updLevnr); // Voor als kzlLevnr op de volgende regel (loop) leeg is en $updLevnr dus niet hoort te bestaan

// Wijzigen geslacht
 if (isset($updSekse) && ($updSekse <> $sekse_db || !isset($sekse_db))) {
	$upd_tblSchaap = "UPDATE tblSchaap SET geslacht = '".mysqli_real_escape_string($db,$updSekse)."' WHERE levensnummer = '".mysqli_real_escape_string($db,$Levnr_db)."' ";	
		mysqli_query($db,$upd_tblSchaap) or die (mysqli_error($db));		
}
unset($updSekse); // Voor als kzlSekse op de volgende regel (loop) leeg is en $updSekse dus niet hoort te bestaan

// Wijzigen herkomst
if ((isset($updHerk) && $updHerk <> 'leegkeuzelijst' && (!isset($herk_db) || $updHerk <> $herk_db) )) {
 		
$upd_tblStal = "
UPDATE tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblMelding m on (m.hisId = h.hisId)
set st.rel_herk = '".mysqli_real_escape_string($db,$updHerk)."' 
WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
";	
	mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));
}
else if (isset($updHerk) && $updHerk == 'leegkeuzelijst' && isset($herk_db)) {
	if(isset($wrong_levnr)) 							{ $wrong_partij = $wrong_levnr." en herkomst moet zijn gevuld."; }
	else if(!isset($wrong_levnr) && isset($wrong_dag))  { $wrong_partij = $wrong_dag." en herkomst moet zijn gevuld."; }
	else 												{ $wrong_partij = "Herkomst moet zijn gevuld."; }
	  }
unset($updHerk); // Voor als kzlHerk op de volgende regel (loop) leeg is en $updHerk dus niet hoort te bestaan

// Wijzigen bestemming
if ((isset($updBest) && (!isset($best_db) || $updBest <> $best_db) )) {
		
$upd_tblStal = "
UPDATE tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblMelding m on (m.hisId = h.hisId)
set st.rel_best = '".mysqli_real_escape_string($db,$updBest)."'
WHERE m.meldId = '".mysqli_real_escape_string($db,$recId)."'
";	
	mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));
}
else if (!isset($updBest) && $code == 'AFV')
{
  if(isset($wrong_levnr)) 		{ $wrong_partij = $wrong_levnr." en bestemming moet zijn gevuld."; }
  else if(isset($wrong_dag)) 	{ $wrong_partij = $wrong_dag." en bestemming moet zijn gevuld."; }
  else if(!isset($updBest) )	{ $wrong_partij = "Bestemming moet zijn gevuld."; }
}
unset($updBest); // Voor als kzlPartij op de volgende regel (loop) leeg is en $updBest dus niet hoort te bestaan
// EINDE CONTROLE op gewijzigde velden


// Veld skip vullen en veld fout ledigen. 
//Als skip wordt gewijzigd naar 0 dan wordt het veld fout eventueel ingevuld bij Foutmelding opslaan
if ($updSkip <> $skip_db) {
 $upd_tblMelding = "UPDATE tblMelding SET skip = '".mysqli_real_escape_string($db,$updSkip)."', fout = NULL WHERE meldId = '".mysqli_real_escape_string($db,$recId)."' ";	
/*echo $upd_tblMelding.'<br>';*/	mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));
		
}


// Foutmelding opslaan
	if(isset($wrong_partij)) { $wrong = $wrong_partij; } 
elseif(isset($wrong_levnr))  { $wrong = $wrong_levnr; } 
elseif(isset($wrong_dag))	 { $wrong = $wrong_dag; }

//echo '$wrong = '.$wrong.' en $fout_db = '.$fout_db.'<br>'; #/#

if((isset($wrong) && (!isset($fout_db) || ($wrong <> $fout_db) )) || (!isset($wrong) && isset($fout_db)) )	{


	$upd_tblMelding = "UPDATE tblMelding SET fout = " . db_null_input($wrong) . " WHERE meldId = '".mysqli_real_escape_string($db,$recId)."' and skip <> 1";

/*echo $upd_tblMelding.'<br>';*/	mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));
}
unset($wrong);		unset($wrong_dag);		unset($wrong_levnr);	unset($wrong_partij);	

// Einde Foutmelding opslaan

	
}

	
	
	
	
	

	
	
	}
    //echo '<br />';


					
	
	
						

?>