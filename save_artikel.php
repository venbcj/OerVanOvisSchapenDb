<!-- 22-7-2017 gemaakt 
05-07-2020 : veld Perkg toegevoegd en wachtdagen gesplitst in vlees en melk
09-08-2020 : veld naamreader toegevoegd 
17-01-2022 : Sql beveiligd met quotes en Btw 0% toegevoegd 
21-02-2025 : Lege checkboxen gedefinieerd -->

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

foreach($_POST as $fldname => $fldvalue) {
    
    $multip_array[getIdFromKey($fldname)][getNameFromKey($fldname)] = $fldvalue;
}
foreach($multip_array as $recId => $id) {
//echo '<br>'.'$recId = '.$recId.'<br>';
	
unset($updNaam);
unset($updEenheid);
unset($updActief);

  foreach($id as $key => $value) {
	
	if ($key == 'txtNaam' && !empty($value)) {  $updNaam = $value; } 
	 /*else if ($key == 'txtNaam' && empty($value)){ $updNaam = ''; }*/

	if ($key == 'txtPres' && !empty($value)) {  $updPres = $value;} 
	 else if ($key == 'txtPres' && empty($value)) { $updPres = $updNaam; }

	if ($key == 'txtStdat' && !empty($value)) {  $updStdat = $value; } 
	 else if ($key == 'txtStdat' && empty($value)) { $updStdat = ''; } 

	if ($key == 'kzlNhd' && !empty($value)) {  $updEenheid = $value;} 
	 /*else if ($key == 'kzlNhd' && empty($value)){ $updEenheid = ''; } Eenheid bepaald user dus mag niet leeg zijn */

	if ($key == 'txtGewicht' && !empty($value)) { $updKg = $value; }
	 else if($key == 'txtGewicht') { $updKg = ''; }

	if ($key == 'kzlBtw' && !empty($value)) {  $updBtw = $value; } 
	 // else if ($key == 'kzlBtw' && empty($value)) { $updBtw = ''; } kzlBtw is nooit leeg

	if ($key == 'txtRegnr' && !empty($value)) {  $updRegnr = $value; } 
	 else if ($key == 'txtRegnr' && empty($value)) { $updRegnr = ''; }

	if ($key == 'kzlRelatie' && !empty($value)) {  $updRelatie = $value; } 
	 else if ($key == 'kzlRelatie' && empty($value)) { $updRelatie = ''; }

	if ($key == 'txtWdgnV' && !empty($value)) {  $updWdgn_v = $value; } 
	 else if ($key == 'txtWdgnV' && empty($value)) { $updWdgn_v = ''; }

	if ($key == 'txtWdgnM' && !empty($value)) {  $updWdgn_m = $value; } 
	 else if ($key == 'txtWdgnM' && empty($value)) { $updWdgn_m = ''; }

	if ($key == 'kzlRubriek' && !empty($value)) {  $updRubriek = $value; } 
	 else if ($key == 'kzlRubriek' && empty($value)) { $updRubriek = ''; }


	if ($key == 'chkActief') { $updActief = $value; }	
	
									}

If(!isset($updActief)) { $updActief = 0; }

if(isset($recId) and $recId > 0) {
$zoek_in_database = mysqli_query($db, "
SELECT naam, naamreader, stdat, enhuId, perkg, btw, regnr, relId, wdgn_v, wdgn_m, rubuId, actief
FROM tblArtikel
WHERE artId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while( $co = mysqli_fetch_assoc($zoek_in_database)) {
		$naam_db = $co['naam']; /*if(!isset($naam_db)) { $naam_db = ''; }*/
		$pres_db = $co['naamreader']; if(!isset($pres_db)) { $pres_db = ''; }
		$stdat_db = $co['stdat'];  	if(!isset($stdat_db)) { $stdat_db = ''; }
		$eenheid_db = $co['enhuId'];
		$kg_db = $co['perkg'];		if(!isset($kg_db)) { $kg_db = ''; }
		$btw_db = $co['btw'];		if(!isset($btw_db)) { $btw_db = ''; }
		$regnr_db = $co['regnr']; 	if(!isset($regnr_db)) { $regnr_db = ''; }
		$relId_db = $co['relId']; 	if(!isset($relId_db)) { $relId_db = ''; }
		$wdgn_v_db = $co['wdgn_v'];	if(!isset($wdgn_v_db)) { $wdgn_v_db = ''; }
		$wdgn_m_db = $co['wdgn_m'];	if(!isset($wdgn_m_db)) { $wdgn_m_db = ''; }
		$rubuId_db = $co['rubuId']; if(!isset($rubuId_db)) { $rubuId_db = ''; }
		$actief_db = $co['actief']; if(!isset($actief_db)) { $actief_db = 0; }

	}


/*Wijzig naam */
if(isset($updNaam) && $updNaam <> $naam_db && $actief_db == 1) {
	//echo '$updNaam = '.$updNaam.' en $naam_db = '.$naam_db.' dus'.'<br>';
$wijzig_naam = "UPDATE tblArtikel set naam = '".mysqli_real_escape_string($db,$updNaam)."' WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_naam.'<br>';*/		mysqli_query($db,$wijzig_naam) or die (mysqli_error($db));
}

/*Wijzig naamreader */
if(isset($updPres) && $updPres <> $pres_db && $actief_db == 1) {
	//echo '$updNaam = '.$updNaam.' en $naam_db = '.$naam_db.' dus'.'<br>';
$wijzig_naamreader = "UPDATE tblArtikel set naamreader = '".mysqli_real_escape_string($db,$updPres)."' WHERE artId = '".mysqli_real_escape_string($db,$recId)."' ";
/*echo $wijzig_naam.'<br>';*/		mysqli_query($db,$wijzig_naamreader) or die (mysqli_error($db));
}

/*Wijzig standaard aantal */
if($updStdat <> $stdat_db && $actief_db == 1) {
$wijzig_stdat = "UPDATE tblArtikel set stdat = " . db_null_input($updStdat) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_stdat.'<br>';*/	mysqli_query($db,$wijzig_stdat) or die (mysqli_error($db));
}

/*Wijzig eenheid */
if(isset($updEenheid) && $updEenheid <> $eenheid_db && $actief_db == 1) {
$wijzig_eenheid = "UPDATE tblArtikel set enhuId = '".mysqli_real_escape_string($db,$updEenheid)."' WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_eenheid.'<br>';*/	mysqli_query($db,$wijzig_eenheid) or die (mysqli_error($db));
}

/*Wijzig per gewicht */
if($updKg <> $kg_db && $actief_db == 1) {
$wijzig_perkg = "UPDATE tblArtikel set perkg = " . db_null_input($updKg) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_perkg.'<br>';*/	mysqli_query($db,$wijzig_perkg) or die (mysqli_error($db));
}

/*Wijzig btw */
if($updBtw <> $btw_db && $actief_db == 1) {
	/*echo '$updBtw = '.$updBtw.' en $btw_db = '.$btw_db.' dus'.'<br>';*/
$wijzig_btw = "UPDATE tblArtikel set btw = " . db_null_input($updBtw) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_btw.'<br>';*/		mysqli_query($db,$wijzig_btw) or die (mysqli_error($db));
}

/*Wijzig registratienummer */
if($updRegnr <> $regnr_db && $actief_db == 1) {
	//echo '$updRegnr = '.$updRegnr.' en $regnr_db = '.$regnr_db.' dus'.'<br>';
$wijzig_regnr = "UPDATE tblArtikel set regnr = " . db_null_input($updRegnr) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_regnr.'<br>';*/	mysqli_query($db,$wijzig_regnr) or die (mysqli_error($db));
}

/*Wijzig relatie */
if($updRelatie <> $relId_db && $actief_db == 1) {
$wijzig_relatie = "UPDATE tblArtikel set relId = " . db_null_input($updRelatie) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_relatie.'<br>';*/	mysqli_query($db,$wijzig_relatie) or die (mysqli_error($db));
}

/*Wijzig wachtdagen vlees */
if($updWdgn_v <> $wdgn_v_db && $actief_db == 1) {
$wijzig_wdgn_v = "UPDATE tblArtikel set wdgn_v = " . db_null_input($updWdgn_v) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_wdgn_v.'<br>';*/		mysqli_query($db,$wijzig_wdgn_v) or die (mysqli_error($db));
}

/*Wijzig wachtdagen melk */
if(isset($updWdgn_m) && $updWdgn_m <> $wdgn_m_db && $actief_db == 1) {
$wijzig_wdgn_m = "UPDATE tblArtikel set wdgn_m = " . db_null_input($updWdgn_m) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_wdgn_m.'<br>';*/		mysqli_query($db,$wijzig_wdgn_m) or die (mysqli_error($db));
}

/*Wijzig rubriek */
if($updRubriek <> $rubuId_db && $actief_db == 1) {
	//echo $updRubriek.' <> '.$rubuId_db.' dus ...'.'<br>';
$wijzig_rubriek = "UPDATE tblArtikel set rubuId = "	. db_null_input($updRubriek) . " WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_rubriek.'<br>';*/	mysqli_query($db,$wijzig_rubriek) or die (mysqli_error($db));
}

/*Wijzig actief */
if($updActief <> $actief_db) {
	//echo '$updActief = '.$updActief.' en $actief_db = '.$actief_db.' dus'.'<br>';
$wijzig_actief = "UPDATE tblArtikel set actief = '".mysqli_real_escape_string($db,$updActief)."' WHERE artId = '".mysqli_real_escape_string($db,$recId)."' 	";
/*echo $wijzig_actief.'<br>';*/	mysqli_query($db,$wijzig_actief) or die (mysqli_error($db));
}



	
} // Einde if(isset($recId) and $recId > 0)

	

	
	
	} // Einde foreach($array as $recId => $id)
				

?>