<?php
/* 21-10-2018 gemaakt 
17-02-2021 : SQL beveiligd met quotes 
25-12-2021 : Bestand hernoemd van save_dracht.php naar save_dekkingen.php
28-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie */

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
#echo '<br>'.'$recId = '.$recId.'<br>';

if(!empty($recId)) {

unset($delete);
unset($updRam);
unset($fldDracht);
unset($fldDrachtdm);
unset($updGrootte);

foreach($id as $key => $value) {

	if ($key == 'chkDel') { $delete = 1; }
	if ($key == 'kzlRam' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $updRam = $value; }
	if ($key == 'kzlDrachtUpd')  {  $fldDracht = $value; }
	if ($key == 'txtDrachtdm' && !empty($value))  {  $fldDrachtdm = $value;
													 $makeday = date_create($value); $fldDmDracht = date_format($makeday,'Y-m-d');
													}
	if ($key == 'txtGrootte' && !empty($value)) { $updGrootte = $value; }
	
		
									}


unset($dmDek);
unset($drachtdm_db);
unset($schaapId);

$zoek_dekdatum = mysqli_query($db,"
SELECT h.datum
FROM tblVolwas v
 join tblHistorie h on (v.hisId = h.hisId)
WHERE h.skip = 0 and volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ( $zdm = mysqli_fetch_assoc($zoek_dekdatum)) { $dmDek = $zdm['datum']; }

// Als dekdatum niet bestaat laatste worpdatum van de ooi
$zoek_ooi = mysqli_query($db,"
SELECT mdrId
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ( $zo = mysqli_fetch_assoc($zoek_ooi)) { $ooiId = $zo['mdrId']; }

$zoek_laatste_worpdatum = mysqli_query($db,"
SELECT max(h.datum) dmworp
FROM tblSchaap s
 join tblVolwas v on (s.volwId = v.volwId)
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
WHERE h.actId = 1 and v.mdrId = '".mysqli_real_escape_string($db,$ooiId)."' and h.skip = 0
") or die (mysqli_error($db));
	while ( $zdm = mysqli_fetch_assoc($zoek_laatste_worpdatum)) { $dmWorp = $zdm['dmworp']; }

// Bepaald drachtig ja of nee o.b.v. drachtdatum of worp
$zoek_drachtdatum = mysqli_query($db,"
SELECT h.hisId, h.datum
FROM tblDracht d
 join tblHistorie h on (d.hisId = h.hisId)
WHERE h.skip = 0 and d.volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while ($zdra = mysqli_fetch_assoc($zoek_drachtdatum)) {
	$hisId_dr_db = $zdra['hisId'];
	$drachtdm_db = $zdra['datum'];
}

$zoek_worp = mysqli_query($db,"
SELECT s.schaapId
FROM tblVolwas v
 join tblSchaap s on (s.volwId = v.volwId)
WHERE v.volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while ($zw = mysqli_fetch_assoc($zoek_worp)) {
	$schaapId = $zw['schaapId'];
}
if(isset($drachtdm_db) || isset($schaapId)) { $drachtig = 'ja'; } else { $drachtig = 'nee'; }
// Einde Bepaald drachtig ja of nee o.b.v. drachtdatum of worp


/*echo '$fldDracht = '. $fldDracht.'<br>';
echo '$updGrootte = '. $updGrootte.'<br>';*/

$zoek_worpgrootte_database = mysqli_query($db,"
SELECT vdrId, grootte
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while ($dra = mysqli_fetch_assoc($zoek_worpgrootte_database)) {
	$vdr_db = $dra['vdrId'];
	$grootte_db = $dra['grootte'];
}


// Dekking verwijderen
if(isset($delete)) {

$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$delete_dracht = "UPDATE tblHistorie SET skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId). "' " ;
/*echo $delete_dracht.'<br>';	##*/mysqli_query($db,$delete_dracht) or die (mysqli_error($db));

}


// Ram wijzigen
if(isset($updRam) && $vdr_db <> $updRam) {

$updateRam = "UPDATE tblVolwas SET vdrId = ".db_null_input($updRam)." WHERE volwId = '".mysqli_real_escape_string($db,$recId)."' ";	
/*echo $updateRam.'<br>';	##*/mysqli_query($db,$updateRam) or die (mysqli_error($db));
	
		}


// Dracht wijzigen
if($drachtig == 'ja' && $fldDracht == 'ja') { // bestaande dracht wijzigen

// Drachtdatum wijzigen
if($drachtdm_db <> $fldDmDracht) {

if(!isset($fldDrachtdm)) { $fout = 'De drachtdatum is niet bekend.'; }
else if($dmDek > $fldDmDracht) { $fout = 'De drachtdatum kan niet voor de dekdatum liggen.'; }
else if($dmWorp > $fldDmDracht) { $fout = 'De drachtdatum kan niet voor de laatste werpdatum liggen.'; }
else {

$updateDracht = "UPDATE tblHistorie SET datum = '".mysqli_real_escape_string($db,$fldDmDracht)."' WHERE hisId = '".mysqli_real_escape_string($db,$hisId_dr_db)."' ";	

/*echo $updateDracht.'<br>';	##*/mysqli_query($db,$updateDracht) or die (mysqli_error($db));
	}
}

// Worpgrootte wijzigen
if($grootte_db <> $updGrootte) {

$updateDracht = "UPDATE tblVolwas SET grootte = ".db_null_input($updGrootte)." WHERE volwId = '".mysqli_real_escape_string($db,$recId)."' ";	

/*echo $updateDracht.'<br>';	##*/mysqli_query($db,$updateDracht) or die (mysqli_error($db));

}

}
else if($drachtig == 'nee' && $fldDracht == 'ja') { // dracht aanmaken

if(!isset($fldDrachtdm)) { $fout = 'De drachtdatum is niet bekend.'; }
else if($dmDek > $fldDmDracht) { $fout = 'De drachtdatum kan niet voor de dekdatum liggen.'; }
else if($dmWorp > $fldDmDracht) { $fout = 'De drachtdatum kan niet voor de laatste worpdatum liggen.'; }
else {

$zoek_mdrId = mysqli_query($db,"
SELECT mdrId
FROM tblVolwas
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
	while ( $zm = mysqli_fetch_assoc($zoek_mdrId)) { $mdrId = $zm['mdrId']; }

$zoek_stalId = mysqli_query($db,"
SELECT max(stalId) stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$mdrId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
	while ( $zs = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $zs['stalId']; }

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDmDracht)."', actId = 19 ";	
/*echo $insert_tblHistorie.'<br>';	##*/mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE actId = 19 and stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));

while($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$insert_tblDracht = "INSERT INTO tblDracht SET volwId = '".mysqli_real_escape_string($db,$recId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";	
/*echo $insert_tblDracht.'<br>';	##*/mysqli_query($db,$insert_tblDracht) or die (mysqli_error($db));
	
}
}

if($drachtig == 'ja' && $fldDracht == 'nee') { // dracht verwijderen

$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblDracht
WHERE volwId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while($zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

$update_tblHistorie = "UPDATE tblHistorie SET skip = 1 WHERE hisId = '".mysqli_real_escape_string($db,$hisId)."' ";	
/*echo $update_tblHistorie.'<br>';	##*/mysqli_query($db,$update_tblHistorie) or die (mysqli_error($db));

$updateDracht = "UPDATE tblVolwas SET grootte = NULL WHERE volwId = '".mysqli_real_escape_string($db,$recId)."' ";	

/*echo $updateDracht.'<br>';	##*/mysqli_query($db,$updateDracht) or die (mysqli_error($db));

		
}
// Einde Dracht wijzigen



	




	} // Einde if(!empty($recId))

	}

?>
					
	