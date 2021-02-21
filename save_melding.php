<!-- 10-11-2014 gemaakt 
5-12-2016 : kzlPartij gesplitst in kzlHerk en kzlBest 	9-2-2017 ctr-velden verwijderd 
5-5-2017 : Controle bij wijzigen datum aangepast van $updDay < $maxDay naar $updDay > $maxDay
26-1-2018 : Bij verwijderen melding wordt kzlBest niet meer leeggemaakt -->

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
	
  foreach($id as $key => $value) {
	//if ($key == 'txtRequest' ) { foreach($id as $key => $value) { echo $key .' = '. $value. "<br><br>";  } }
  
 // echo $key .' = '. $value. "<br><br>";
  
	if ($key == 'kzlDef' ) { /*echo $key.'='.$value." &nbsp&nbsp  ";*/ $updDef = $value; }
	
	if ($key == 'txtSchaapdm' && !empty($value)) {  $txtDag = $value; $dag = date_create($value); $updDay =  date_format($dag,'Y-m-d');
																				 $updDag =  date_format($dag,'d-m-Y');	}

	if ($key == 'txtLevnr' && !empty($value)) { $updLevnr = $value; } // in MeldGeboortes.php en mogelijk ook in MeldAanvoer.php
	
	if ($key == 'kzlSekse' && !empty($value)) { $updSekse = $value; }

	if ($key == 'kzlHerk' && !empty($value))  { $updHerk = $value; } else if ($key == 'kzlHerk' && empty($value))  { $updHerk = 'leegkeuzelijst'; }  // in MeldAanvoer.php
	if ($key == 'kzlBest' && !empty($value))  { $updBest = $value; }  // in MeldAfvoer.php dus niet voor MeldUitval.php !!!
	else if ($key == 'kzlBest' && empty($value))  { $kzlBest = 'leeg'; }  // in MeldAfvoer.php dus niet voor MeldUitval.php !!!
	
	if ($key == 'chbSkip' ) { $updSkip = $value; /*echo $key.'='.$value; echo "<br/>";*/  } 
	
									}
if(isset($recId) and $recId > 0) {
$zoek_in_database = mysqli_query($db, "
SELECT r.reqId, r.code, r.def, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while( $co = mysqli_fetch_assoc($zoek_in_database)) { 
		$reqId = $co['reqId']; 
		$db_Def = $co['def']; 
		$code = $co['code']; 
		$db_Datum = $co['datum']; 
		$db_Levnr = $co['levensnummer'];
		$db_Sekse = $co['geslacht'];
		$db_Herk = $co['rel_herk'];
		$db_Best = $co['rel_best']; }

/* Minimale datum zoeken ter controle bij afvoer bedrijf */
if($code == 'AFV' || $code == 'DOO') {
$zoek_max_datum_stalaf = mysqli_query($db,"
SELECT max(datum) date, date_format(max(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join (
	SELECT st.stalId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	 join tblMelding m on (m.hisId = h.hisId)
	WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)."
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
") or die (mysqli_error($db));
	while( $mi = mysqli_fetch_assoc($zoek_max_datum_stalaf)) { $minDay =  $mi['date']; $minDag =  $mi['datum']; }
}
/* Einde Minimale datum zoeken ter controle bij afvoer bedrijf */	

/* Maximale datum zoeken ter controle bij aanvoer bedrijf */
if($code == 'AAN' || $code == 'GER') {
$zoek_min_datum_stalop = mysqli_query($db,"
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join (
	SELECT st.stalId, h.hisId
	FROM tblStal st
	 join tblHistorie h on (h.stalId = st.stalId)
	 join tblMelding m on (m.hisId = h.hisId)
	WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)."
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
") or die (mysqli_error($db));
	while( $mi = mysqli_fetch_assoc($zoek_min_datum_stalop)) { $maxDay =  $mi['date']; $maxDag =  $mi['datum']; }
}
/* Einde Maximale datum zoeken ter controle bij aanvoer bedrijf */
	
/* Maximale datum RVO bepalen ter controle */	
if($code == 'AAN' || $code == 'GER' || $code == 'DOO') { $maxday_rvo = date("Y-m-d"); }
$overovermorgen = mktime(0, 0, 0, date("m")  , date("d")+3, date("Y"));
if($code == 'AFV') { $maxday_rvo = date('Y-m-d', $overovermorgen); }
/* Einde Maximale datum RVO bepalen ter controle */









// Wijzigen keuze 'controle' versus 'vastleggen'		
	if(isset($updDef) && $updDef <> $db_Def) { //echo $updDef." bij ".$reqId."<br>";
		$upd_tblRequest = "UPDATE tblRequest SET def = '".mysqli_real_escape_string($db,$updDef)."' WHERE reqId = ".mysqli_real_escape_string($db,$reqId)." ";	
/*echo $upd_tblRequest.'<br>';*/			mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	}
	//unset ($reqId); //Hiermee wordt het requestId maar 1x doorlopen en is t.b.v. wijzigen tblRequest i.p.v. elke regel uit tblMeldingen
	
// Veld skip vullen en veld fout ledigen
if (isset($updSkip)) {
 $upd_tblMelding = "UPDATE tblMelding SET skip = ".mysqli_real_escape_string($db,$updSkip).", fout = NULL WHERE meldId = ".mysqli_real_escape_string($db,$recId)." ";	
/*echo $upd_tblMelding.'<br>';*/		mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));
		
		//echo $recId;
}

// CONTROLE op gewijzigde velden

// Wijzigen datum
//if(!isset($minDay)) {$minDay = date_format(date_create('11-09-1973'), 'Y-m-d');} // tbv MeldGeboortes.php daar bestaat geen minDag
	
if (!empty($updDay) && $updDay <> $db_Datum)
{
 if (isset($minDay) && $updDay < $minDay)
		{ $wrong_dag = "De datum (".$updDag.") kan niet voor ".$minDag." liggen";		}
 else if (isset($maxDay) && $updDay > $maxDay)
		{ $wrong_dag = "De datum (".$updDag.") kan niet na ".$maxDag." liggen";		}
 else if ($updDay > $maxday_rvo)
		{ $wrong_dag = $txtDag." ligt voor RVO te ver in de toekomst";	}
 else { 
 $upd_tblHistorie = "
 UPDATE tblHistorie h
  join tblMelding m on (h.hisId = m.hisId)
 set   h.datum  = '".mysqli_real_escape_string($db,$updDay)."'
 WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)." 
 ";
		mysqli_query($db,$upd_tblHistorie) or die (mysqli_error($db));
	}
}


// Wijzigen levensnummer
if (isset($updLevnr) && $updLevnr <> $db_Levnr)
{
// Controle op duplicaten
$count_levnr = mysqli_query($db,"
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db,$updLevnr)."' 
") or die (mysqli_error($db));
	$row = mysqli_fetch_assoc($count_levnr); $levnr_exist = $row['aant'];
// Einde Controle op duplicaten 
if ($levnr_exist > 0) 
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
else
		{
	$upd_tblSchaap = "UPDATE tblSchaap SET levensnummer = '".mysqli_real_escape_string($db,$updLevnr)."' WHERE levensnummer = '".mysqli_real_escape_string($db,$db_Levnr)."' ";	
/*echo $upd_tblSchaap.'<br>';	*/	mysqli_query($db,$upd_tblSchaap) or die (mysqli_error($db));
		}
}
unset($updLevnr); // Voor als kzlLevnr op de volgende regel (loop) leeg is en $updLevnr dus niet hoort te bestaan

// Wijzigen geslacht
 if (isset($updSekse) && ($updSekse <> $db_Sekse || !isset($db_Sekse))) {
	$upd_tblSchaap = "UPDATE tblSchaap SET geslacht = '".mysqli_real_escape_string($db,$updSekse)."' WHERE levensnummer = '".mysqli_real_escape_string($db,$db_Levnr)."' ";	
		mysqli_query($db,$upd_tblSchaap) or die (mysqli_error($db));		
}
unset($updSekse); // Voor als kzlSekse op de volgende regel (loop) leeg is en $updSekse dus niet hoort te bestaan

// Wijzigen herkomst
if ((isset($updHerk) && $updHerk <> 'leegkeuzelijst' && (!isset($db_Herk) || $updHerk <> $db_Herk) )) {
 		
$upd_tblStal = "
UPDATE tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblMelding m on (m.hisId = h.hisId)
set st.rel_herk = ".mysqli_real_escape_string($db,$updHerk)." 
WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)."
";	
	mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));
}
else if (isset($updHerk) && $updHerk == 'leegkeuzelijst' && isset($db_Herk)) {
	if(isset($wrong_levnr)) 							{ $wrong_part = $wrong_levnr." en herkomst moet zijn gevuld."; }
	else if(!isset($wrong_levnr) && isset($wrong_dag))  { $wrong_part = $wrong_dag." en herkomst moet zijn gevuld."; }
	else 												{ $wrong_part = "Herkomst moet zijn gevuld."; }
	  }
unset($updHerk); // Voor als kzlHerk op de volgende regel (loop) leeg is en $updHerk dus niet hoort te bestaan

// Wijzigen bestemming
if ((isset($updBest) && (!isset($db_Best) || $updBest <> $db_Best) )) {
		
$upd_tblStal = "
UPDATE tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
 join tblMelding m on (m.hisId = h.hisId)
set st.rel_best = ".mysqli_real_escape_string($db,$updBest)." 
WHERE m.meldId = ".mysqli_real_escape_string($db,$recId)."
";	
	mysqli_query($db,$upd_tblStal) or die (mysqli_error($db));
}
else if (!isset($updBest) && $code == 'AFV') {
		  if(isset($wrong_levnr)) 							 { $wrong_part = $wrong_levnr." en bestemming moet zijn gevuld."; }
		  else if(!isset($wrong_levnr) && isset($wrong_dag)) { $wrong_part = $wrong_dag." en bestemming moet zijn gevuld."; }
		  else if(isset($kzlBest) )							 { $wrong_part = "Bestemming moet zijn gevuld."; }
		  }
unset($updBest); // Voor als kzlPartij op de volgende regel (loop) leeg is en $updBest dus niet hoort te bestaan
// EINDE CONTROLE op gewijzigde velden

// Foutmelding opslaan
if(isset($wrong_part)) { $wrong = $wrong_part; } else if(isset($wrong_levnr)) { $wrong = $wrong_levnr; } else if(isset($wrong_dag)) { $wrong = $wrong_dag; }
if(isset($wrong))	{
	$upd_tblMelding = "UPDATE tblMelding SET fout = '$wrong' WHERE meldId = ".mysqli_real_escape_string($db,$recId)." and skip <> 1";	
			mysqli_query($db,$upd_tblMelding) or die (mysqli_error($db));
unset($wrong);		unset($wrong_dag);		unset($wrong_levnr);	unset($wrong_part);	}

	
}	

	
	
	
	
	

	
	
	}
    //echo '<br />';


					
	
	
						

?>