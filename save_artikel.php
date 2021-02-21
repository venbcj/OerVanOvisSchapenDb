<!-- 22-7-2017 gemaakt 
 5-7-2020 : veld Perkg toegevoegd en wachtdagen gesplitst in vlees en melk
 9-8-2020 : veld naamreader toegevoegd -->

<?php
/*Save_Artikel.php toegpast in :
	- Medicijnen.php
	- Voer.php	*/

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
//echo '<br>'.'$recId = '.$recId.'<br>';
	
unset($updNaam);

  foreach($id as $key => $value) {
	
	if ($key == 'txtNaam' && !empty($value)){  $updNaam = "'".$value."'"; } 
	 /*else if ($key == 'txtNaam' && empty($value)){ $updNaam = 'NULL'; }*/

	if ($key == 'txtPres' && !empty($value)){  $updPres = "'".$value."'";} 
	 else if ($key == 'txtPres' && empty($value)){ $updPres = $updNaam; }

	if ($key == 'txtRegnr' && !empty($value)){  $updRegnr = "'".$value."'";} 
	 else if ($key == 'txtRegnr' && empty($value)){ $updRegnr = 'NULL'; }

	if ($key == 'txtStdat' && !empty($value)){  $updStdat = $value;} 
	 else if ($key == 'txtStdat' && empty($value)){ $updStdat = 'NULL'; } 

	if ($key == 'kzlNhd' && !empty($value)){  $updEenheid = $value;} 
	 /*else if ($key == 'kzlNhd' && empty($value)){ $updEenheid = 'NULL'; } Eenheid bepaald user dus mag niet leeg zijn */

	if ($key == 'txtGewicht' && !empty($value)) { $updKg = $value; }
	 else if($key == 'txtGewicht') { $updKg = 'NULL'; }

	if ($key == 'kzlBtw' && !empty($value)){  $updBtw = $value; } 
	 else if ($key == 'kzlBtw' && empty($value)){ $updBtw = 'NULL'; }

	if ($key == 'kzlRelatie' && !empty($value)){  $updRelatie = $value; } 
	 else if ($key == 'kzlRelatie' && empty($value)){ $updRelatie = 'NULL'; }

	if ($key == 'txtWdgnV' && !empty($value)){  $updWdgn_v = $value; } 
	 else if ($key == 'txtWdgnV' && empty($value)){ $updWdgn_v = 'NULL'; }

	if ($key == 'txtWdgnM' && !empty($value)){  $updWdgn_m = $value; } 
	 else if ($key == 'txtWdgnM' && empty($value)){ $updWdgn_m = 'NULL'; }

	if ($key == 'kzlRubriek' && !empty($value)){  $updRubriek = $value; } 
	 else if ($key == 'kzlRubriek' && empty($value)){ $updRubriek = 'NULL'; }


	if ($key == 'chkActief'){ $updActief = $value; }						   else { $updActief = 0; }	
	
									}

if(isset($recId) and $recId > 0) {
$zoek_in_database = mysqli_query($db, "
SELECT naam, naamreader, stdat, enhuId, perkg, btw, regnr, relId, wdgn_v, wdgn_m, rubuId, actief
FROM tblArtikel
WHERE artId = ".mysqli_real_escape_string($db,$recId)."
") or die (mysqli_error($db));
	while( $co = mysqli_fetch_assoc($zoek_in_database)) {
		$dbNaam = $co['naam']; /*if(empty($dbNaam)) { $dbNaam = 'NULL'; } else {*/ $dbNaam = "'".$dbNaam."'"; //}
		$dbPres = $co['naamreader']; if(empty($dbPres)) { $dbPres = 'NULL'; } else { $dbPres = "'".$dbPres."'"; }
		$dbStdat = $co['stdat'];  	if(empty($dbStdat)) { $dbStdat = 'NULL'; }
		$dbEenheid = $co['enhuId'];
		$dbKg = $co['perkg'];		if(empty($dbKg)) { $dbKg = 'NULL'; }
		$dbBtw = $co['btw'];		if(empty($dbBtw)) { $dbBtw = 'NULL'; }
		$dbRegnr = $co['regnr']; if(empty($dbRegnr)) { $dbRegnr = 'NULL'; } else { $dbRegnr = "'".$dbRegnr."'"; }
		$dbRelatie = $co['relId']; 	if(empty($dbRelatie)) { $dbRelatie = 'NULL'; }
		$dbWdgn_v = $co['wdgn_v'];
		$dbWdgn_m = $co['wdgn_m'];
		$dbRubriek = $co['rubuId']; if(empty($dbRubriek)) { $dbRubriek = 'NULL'; }
		$dbActief = $co['actief']; };

/*Wijzig naam */
if(isset($updNaam) && $updNaam <> $dbNaam && $dbActief == 1) {
	//echo '$updNaam = '.$updNaam.' en $dbNaam = '.$dbNaam.' dus'.'<br>';
$wijzig_naam = "UPDATE tblArtikel set naam = ".$updNaam." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_naam.'<br>';*/		mysqli_query($db,$wijzig_naam) or die (mysqli_error($db));
}

/*Wijzig naamreader */
if(isset($updPres) && $updPres <> $dbPres && $dbActief == 1) {
	//echo '$updNaam = '.$updNaam.' en $dbNaam = '.$dbNaam.' dus'.'<br>';
$wijzig_naamreader = "UPDATE tblArtikel set naamreader = ".$updPres." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_naam.'<br>';*/		mysqli_query($db,$wijzig_naamreader) or die (mysqli_error($db));
}

/*Wijzig standaard aantal */
if($updStdat <> $dbStdat && $dbActief == 1) {
$wijzig_stdat = "UPDATE tblArtikel set stdat = ".$updStdat." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_stdat.'<br>';*/	mysqli_query($db,$wijzig_stdat) or die (mysqli_error($db));
}

/*Wijzig eenheid */
if(isset($updEenheid) && $updEenheid <> $dbEenheid && $dbActief == 1) {
$wijzig_eenheid = "UPDATE tblArtikel set enhuId = ".$updEenheid." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_eenheid.'<br>';*/	mysqli_query($db,$wijzig_eenheid) or die (mysqli_error($db));
}

/*Wijzig per gewicht */
if($updKg <> $dbKg && $dbActief == 1) {
$wijzig_perkg = "UPDATE tblArtikel set perkg = ".$updKg." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_perkg.'<br>';*/	mysqli_query($db,$wijzig_perkg) or die (mysqli_error($db));
}

/*Wijzig btw */
if($updBtw <> $dbBtw && $dbActief == 1) {
	/*echo '$updBtw = '.$updBtw.' en $dbBtw = '.$dbBtw.' dus'.'<br>';*/
$wijzig_btw = "UPDATE tblArtikel set btw = ".$updBtw." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_btw.'<br>';*/		mysqli_query($db,$wijzig_btw) or die (mysqli_error($db));
}

/*Wijzig registratienummer */
if(isset($updRegnr) && $updRegnr <> $dbRegnr && $dbActief == 1) {
	//echo '$updRegnr = '.$updRegnr.' en $dbRegnr = '.$dbRegnr.' dus'.'<br>';
$wijzig_regnr = "UPDATE tblArtikel set regnr = ".$updRegnr." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_regnr.'<br>';*/	mysqli_query($db,$wijzig_regnr) or die (mysqli_error($db));
}

/*Wijzig relatie */
if($updRelatie <> $dbRelatie && $dbActief == 1) {
$wijzig_relatie = "UPDATE tblArtikel set relId = ".$updRelatie." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_relatie.'<br>';*/	mysqli_query($db,$wijzig_relatie) or die (mysqli_error($db));
}

/*Wijzig wachtdagen vlees */
if(isset($updWdgn_v) && $updWdgn_v <> $dbWdgn_v && $dbActief == 1) {
$wijzig_wdgn_v = "UPDATE tblArtikel set wdgn_v = ".$updWdgn_v." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_wdgn_v.'<br>';*/		mysqli_query($db,$wijzig_wdgn_v) or die (mysqli_error($db));
}

/*Wijzig wachtdagen melk */
if(isset($updWdgn_m) && $updWdgn_m <> $dbWdgn_m && $dbActief == 1) {
$wijzig_wdgn_m = "UPDATE tblArtikel set wdgn_m = ".$updWdgn_m." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_wdgn_m.'<br>';*/		mysqli_query($db,$wijzig_wdgn_m) or die (mysqli_error($db));
}

/*Wijzig rubriek */
if($updRubriek <> $dbRubriek && $dbActief == 1) {
	//echo $updRubriek.' <> '.$dbRubriek.' dus ...'.'<br>';
$wijzig_rubriek = "UPDATE tblArtikel set rubuId = ".$updRubriek." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_rubriek.'<br>';*/	mysqli_query($db,$wijzig_rubriek) or die (mysqli_error($db));
}

/*Wijzig actief */
if($updActief <> $dbActief) {
	//echo '$updActief = '.$updActief.' en $dbActief = '.$dbActief.' dus'.'<br>';
$wijzig_actief = "UPDATE tblArtikel set actief = ".$updActief." WHERE artId = ".mysqli_real_escape_string($db,$recId)." 	";
/*echo $wijzig_actief.'<br>';*/	mysqli_query($db,$wijzig_actief) or die (mysqli_error($db));
}



	
}

	

	
	
	}
				

?>