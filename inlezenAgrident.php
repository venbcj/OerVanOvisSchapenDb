<?php /* 15-3-2020 : Aantal nog in te lezen gesplitst i.v.m. 2 verschillende readers
2-6-2020 : ovleg gewijzigd in adop
4-7-2020 : 1 tabel impAgrident gemaakt 
14-11-2020 aantpil aangepast naar impAgrident 
20-06-2021 Voerregistratie toegevoegd 
$versie = '18-12-2021'; /* Dekken en Dracht toegevoegd 
05-08-2023 Stallijstscan toegevoegd 
03-11-2024 Uitscharen en terug van uitscharen toegevoegd
30-08-2025 wijzigen ubn toegevoegd. toelichting ActId = 12 en nieuw veld ubnId is not null. Als actId = 12 en ubnId is leeg dan is het een reguliere afvoer lam  */


// Aantal nog in te lezen STALLIJSTSCAN ter controle stallijst
$stallijstscan_new_lid = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 21 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_newlid = mysqli_fetch_assoc($stallijstscan_new_lid)) {  $aantNewLid = $rec_newlid['aant'];  }
// EINDE Aantal nog in te lezen STALLIJSTSCAN ter controle stallijst

// Aantal nog in te lezen DEKKEN
$zoek_dekken = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 18 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($dek = mysqli_fetch_assoc($zoek_dekken))  { $aantdek = $dek['aant'];    }
// EINDE Aantal nog in te lezen DEKKEN

// Aantal nog in te lezen DRACHT
$zoek_dracht = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 19 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($dra = mysqli_fetch_assoc($zoek_dracht))  { $aantdra = $dra['aant'];    }
// EINDE Aantal nog in te lezen DRACHT

// Aantal nog in te lezen GEBOORTES
$lammeren = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 1 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_g = mysqli_fetch_assoc($lammeren))  {	$aantgeb = $rec_g['aant'];	}
// EINDE Aantal nog in te lezen GEBOORTES

// Aantal nog in te lezen LAMBAR
$lambar = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 16 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_lb = mysqli_fetch_assoc($lambar))  {	$aantLbar = $rec_lb['aant'];	}
// EINDE Aantal nog in te lezen LAMBAR

// Aantal nog in te lezen GESPEENDEN
$gespeenden = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 4 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_spn = mysqli_fetch_assoc($gespeenden))  {	$aantspn = $rec_spn['aant'];	}
// EINDE Aantal nog in te lezen GESPEENDEN

// Aantal nog in te lezen AFGELEVERDEN
$afgeleverden = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 12 and isnull(ubnId) and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_afl = mysqli_fetch_assoc($afgeleverden)) {	$aantafl = $rec_afl['aant'];	}
// EINDE Aantal nog in te lezen AFGELEVERDEN

// Aantal nog in te lezen UITGESCHAARDEN
$uitgeschaarden = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 10 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_uitsch = mysqli_fetch_assoc($uitgeschaarden)) {	$aantUitsch = $rec_uitsch['aant'];	}
// EINDE Aantal nog in te lezen UITGESCHAARDEN

// Aantal nog in te lezen UITVAL
$uitgevallen = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 14 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_u = mysqli_fetch_assoc($uitgevallen))  {	$aantuitv = $rec_u['aant'];	}
// EINDE Aantal nog in te lezen UITVAL

// Aantal nog in te lezen AANVOER
$aanvoer = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and (actId = 2 or actId = 3) and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_aan = mysqli_fetch_assoc($aanvoer)) {	$aantaanw = $rec_aan['aant'];	}
// EINDE Aantal nog in te lezen AANVOER


// Aantal nog in te lezen TERUG VAN UITSCHAREN
$TvUitscharen = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 11 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_Tuitsch = mysqli_fetch_assoc($TvUitscharen)) {	$aantTvUitsch = $rec_Tuitsch['aant'];	}
// EINDE Aantal nog in te lezen TERUG VAN UITSCHAREN

// Aantal nog in te lezen OVERPLAATSING
$overplaatsen = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 5 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_ovpl = mysqli_fetch_assoc($overplaatsen)) {	$aantovpl = $rec_ovpl['aant'];	}
 
$SpenenEnOverpl = mysqli_query($db,"
SELECT count(rs.datum) aantsp
FROM impAgrident rs 
 join (
	SELECT lidId, levensnummer
	FROM impAgrident
	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 5 and isnull(verwerkt)
 ) ro ON (rs.lidId = ro.lidId and rs.levensnummer = ro.levensnummer)
WHERE rs.lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 4 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_sp = mysqli_fetch_assoc($SpenenEnOverpl)) { $speen_ovpl = $rec_sp['aantsp'];	}
// EINDE Aantal nog in te lezen OVERPLAATSING

// Aantal nog in te lezen OVERLEGGEN
 $adoptie = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 15 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_adop = mysqli_fetch_assoc($adoptie)) {	$aantadop = $rec_adop['aant'];	}
// EINDE Aantal nog in te lezen OVERLEGGEN

// Aantal nog in te lezen MEDICIJNEN
$medicijn = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 8 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_pil = mysqli_fetch_assoc($medicijn)) {	$aantpil = $rec_pil['aant'];	}
// EINDE Aantal nog in te lezen MEDICIJNEN

// Aantal nog in te lezen WEGINGEN
$wegingen = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 9 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_wg = mysqli_fetch_assoc($wegingen))  {  $aantwg = $rec_wg['aant'];    }
// EINDE Aantal nog in te lezen WEGINGEN

// Aantal nog in te lezen OMNUMMEREN
$omnummer = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 17 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_omn = mysqli_fetch_assoc($omnummer)) {	$aantomn = $rec_omn['aant'];	}
// EINDE Aantal nog in te lezen OMNUMMEREN

// Aantal nog in te lezen HALSNUMMERS
$halsnummer = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 1717 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_hals = mysqli_fetch_assoc($halsnummer)) {	$aanthals = $rec_hals['aant'];	}
// EINDE Aantal nog in te lezen HALSNUMMERS

// Aantal nog in te lezen VOERREGISTRATIE
$voerregistratie = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 8888 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_voer = mysqli_fetch_assoc($voerregistratie)) {  $aantvoer = $rec_voer['aant'];  }
// EINDE Aantal nog in te lezen VOERREGISTRATIE

 // Aantal nog in te lezen UBN WIJZIGINGEN
$wijzigingen_ubn = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 12 and ubnId is not null and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_voer = mysqli_fetch_assoc($wijzigingen_ubn)) {  $aantubn = $rec_voer['aant'];  }
// EINDE Aantal nog in te lezen UBN WIJZIGINGEN

// Aantal nog in te lezen STALLIJSTSCAN ter controle stallijst
$stallijstscan_controle = mysqli_query($db,"
SELECT count(Id) aant 
FROM impAgrident 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 22 and isnull(verwerkt)
") or die (mysqli_error($db));
 while ($rec_scan = mysqli_fetch_assoc($stallijstscan_controle)) {  $aantscan = $rec_scan['aant'];  }
// EINDE Aantal nog in te lezen STALLIJSTSCAN ter controle stallijst
?>
