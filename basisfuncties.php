<?php
/* 12-08-2023 : bestand gemaakt
30-12-2023 and skip = 0 toegevoegd aan tblHistorie
 */

global $vandaag, $today, $nu, $ditjaar, $vorigjaar;

$vandaag = date('d-m-Y');
$today = date('Y-m-d');
$nu = date('Y-m-d H:i:s'); // Gebruikt in login.php
$ditjaar = date('Y');
$vorigjaar = date('Y')-1;

function first_field_from_result($SQL) {
    global $db;
    $view = mysqli_query($db, $SQL);
    if (mysqli_num_rows($db) > 0) {
        return mysqli_fetch_row($view)[0];
    }
    return null;
}

/*Toegepast in :
- Dekkingen.php
 */
// TODO: alle query-functies op deze manier herschrijven --BCB
function startjaar_gebruiker($LIDID) {
    $lidId = mysqli_real_escape_string($db, $LIDID);
    $SQL = <<<SQL
SELECT year(dmcreate) jaar
FROM tblLeden
WHERE lidId = '$lidId'
SQL;
    return first_field_from_result($SQL);
}

/*Toegepast in :
- Dekkingen.php
- Newuser.php
*/
function date_add_months($day, $var) {
     return date('Y-m-d', strtotime($day . $var .' months'));
}

function db_null_input($var){
    global $db;
    //Evt kun je ook meteen is_boolean($var) omzetten naar 0/1, enz
    return $var === null || empty($var) ? 'NULL' : "'" . mysqli_real_escape_string($db, $var) . "'";
}

function db_null_filter($field, $var){
    global $db;
    //Evt kun je ook meteen is_boolean($var) omzetten naar 0/1, enz
    return $var === null || empty($var) ? "ISNULL(" . $field . ")" : $field . " = '" . mysqli_real_escape_string($db, $var) . "'";
}

/*****************************************************************************************************
Toegepast in :
- InsStallijstscan_nieuwe_klant.php
- post_readerStalscan.php
- post_readerWgn.php */

function zoek_schaapId_in_database($LEVNR) {
 // zie post_readerStalscan.php
global $db;

$zoek_schaap_database = mysqli_query($db, "
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db, $LEVNR)."'
") or die(mysqli_error($db));

    while ($zsd = mysqli_fetch_assoc($zoek_schaap_database)) {
return $zsd['schaapId'];
    }
}

/*****************************************************************************************************
Toegepast in :
- post_readerStalscan.php */

function zoek_transponder_in_database($LEVNR) {
 // zie post_readerStalscan.php
global $db;

$zoek_schaap_database = mysqli_query($db, "
SELECT transponder
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db, $LEVNR)."'
") or die(mysqli_error($db));

    while ($zsd = mysqli_fetch_assoc($zoek_schaap_database)) {
return $zsd['transponder'];
    }
}

/*****************************************************************************************************
Toegepast in :
- post_readerStalscan.php
- InsStallijstscan_nieuwe_klant.php*/

function zoek_schaapId_in_stallijst($LIDID, $LEVNR) {
global $db;

$zoek_schaap_stallijst = mysqli_query($db, "
SELECT s.schaapId
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
WHERE levensnummer = '".mysqli_real_escape_string($db, $LEVNR)."' and st.lidId = '" . mysqli_real_escape_string($db, $LIDID) . "' and isnull(rel_best)
") or die(mysqli_error($db));

    while ($zss = mysqli_fetch_assoc($zoek_schaap_stallijst)) {
return $zss['schaapId'];
    }
}

/*****************************************************************************************************
Toegepast in :
- Dekkingen.php
- InsTvUitscharen.php
- post_readerStalscan.php
- post_readerWgn.php
- UpdSchaap.php */

function zoek_stalId_in_stallijst($LIDID, $Schaapid) {
global $db;

$zoek_stalId = mysqli_query($db, "
SELECT st.stalId
FROM tblStal st
WHERE st.lidId = '".mysqli_real_escape_string($db, $LIDID)."' and st.schaapId = '".mysqli_real_escape_string($db, $Schaapid)."' and isnull(rel_best)
") or die(mysqli_error($db));

while ($zst = mysqli_fetch_assoc($zoek_stalId)) {
        $stalId = $zst['stalId'];
}

return $stalId;
}

/*****************************************************************************************************
Toegepast in :
- Dekkingen.php
- post_readerTvUitsch.php */

function zoek_max_stalId($LIDID, $Schaapid) {
global $db;

$zoek_max_stalId = mysqli_query($db, "
SELECT max(st.stalId) stalId
FROM tblStal st
WHERE st.lidId = '".mysqli_real_escape_string($db, $LIDID)."' and st.schaapId = '".mysqli_real_escape_string($db, $Schaapid)."'
") or die(mysqli_error($db));

while ($zmst = mysqli_fetch_assoc($zoek_max_stalId)) {
        return $zmst['stalId'];
}
}

/*****************************************************************************************************
Toegepast in :
- post_readerStalscan.php
- UpdSchaap.php
- post_readerTvUitsch.php */

function zoek_hisId_stal($STALID, $ACTID){
global $db;

$zoek_hisId_stal = mysqli_query($db, "
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db, $STALID)."' and actId = '" . mysqli_real_escape_string($db, $ACTID) . "' and skip = 0
") or die(mysqli_error($db));

while ($zhst = mysqli_fetch_assoc($zoek_hisId_stal)) {
        return $zhst['hisId'];
}
}

/*****************************************************************************************************
Toegepast in :
- Dekkingen.php
- UpdSchaap.php */

function zoek_max_hisId_stal($STALID, $ACTID){
global $db;

$zoek_max_hisId_stal = mysqli_query($db, "
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db, $STALID)."' and actId = '" . mysqli_real_escape_string($db, $ACTID) . "' and skip = 0
") or die(mysqli_error($db));

while ($zmhst = mysqli_fetch_assoc($zoek_max_hisId_stal)) {
        return $zmhst['hisId'];
}
}

/*****************************************************************************************************
Toegepast in :
- UpdSchaap.php */

function zoek_hisId_stal_af($STALID){
global $db;

$zoek_hisId_stal_af = mysqli_query($db, "
SELECT hisId
FROM tblActie a
 join tblHistorie h on (a.actId = h.actId)
 join tblStal st on (st.stalId = h.stalId)
WHERE a.af = 1 and st.stalId = '".mysqli_real_escape_string($db, $STALID)."' and h.skip = 0
") or die(mysqli_error($db));

while ($zhsa = mysqli_fetch_assoc($zoek_hisId_stal_af)) {
        return $zhsa['hisId'];
}
}

/*****************************************************************************************************
Toegepast in :
- post_readerStalscan.php */

function zoek_hisId_schaap($SCHAAPID, $ACTID){
global $db;

$zoek_hisId_schaap = mysqli_query($db, "
SELECT hisId
FROM tblHistorie h
 join tblStal st on (h.stalId = st.stalId)
WHERE schaapId = '".mysqli_real_escape_string($db, $SCHAAPID)."' and actId = '" . mysqli_real_escape_string($db, $ACTID) . "' and skip = 0
") or die(mysqli_error($db));

while ($zhs = mysqli_fetch_assoc($zoek_hisId_schaap)) {
        return $zhs['hisId'];
}
}

/**********************************************************************
Toegepast in :
- Dekkingen.php */

function insert_tblHistorie($STALID, $DATUM, $ACTID){
global $db;

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db, $STALID)."', datum = '".mysqli_real_escape_string($db, $DATUM)."', actId = '".mysqli_real_escape_string($db, $ACTID)."' ";
/*echo $insert_tblHistorie.'<br>';*/        return mysqli_query($db, $insert_tblHistorie);
}

/*
Toegepast in :
- post_readerWgn.php */

function insert_tblHistorie_kg($STALID, $DATUM, $ACTID, $KG){
global $db;

$insert_tblHistorie = "INSERT INTO tblHistorie SET stalId = '".mysqli_real_escape_string($db, $STALID)."', datum = '".mysqli_real_escape_string($db, $DATUM)."', actId = '".mysqli_real_escape_string($db, $ACTID)."', kg = '".mysqli_real_escape_string($db, $KG)."' ";
/*echo $insert_tblHistorie.'<br>';*/        return mysqli_query($db, $insert_tblHistorie);
}

/**********************************************************************
Gebruikt in :
- InsStallijstscan_controle.php */

function eerste_datum_na_geboortedatum($SCHAAPID){
global $db;

$zoek_datum = mysqli_query($db, "
SELECT min(datum) datum1
FROM tblStal st 
join tblHistorie h on (h.stalId = st.stalId)
WHERE actId != 1 and schaapId = '".mysqli_real_escape_string($db, $SCHAAPID)."' and h.skip = 0
") or die(mysqli_error($db));

while ($zd = mysqli_fetch_assoc($zoek_datum)) {
        return $zd['datum1'];
}
}



/**********************************************************************
Toegepast in :
- Dekkingen.php */

function insert_dekking_mdr($HISID, $MDRID, $VDRID){
global $db;

$insert_tblVolwas = "INSERT INTO tblVolwas set hisId = '".mysqli_real_escape_string($db, $HISID)."', mdrId = '".mysqli_real_escape_string($db, $MDRID)."', vdrId = " . db_null_input($VDRID);
/*echo $insert_tblVolwas;*/     mysqli_query($db, $insert_tblVolwas) or die(mysqli_error($db));
}

/**********************************************************************
Toegepast in :
- Dekkingen.php */

function insert_dracht_mdr($MDRID, $VDRID, $WORPGR){
global $db;

$insert_tblVolwas = "INSERT INTO tblVolwas set mdrId = '".mysqli_real_escape_string($db, $MDRID)."', vdrId = " . db_null_input($VDRID) . ", grootte = " . db_null_input($WORPGR) ;
/*echo $insert_tblVolwas;*/     mysqli_query($db, $insert_tblVolwas) or die(mysqli_error($db));
}

/**********************************************************************
Toegepast in :
- Dekkingen.php */

function zoek_max_volwId_mdr($MDRID, $VDRID){
global $db;


$zoek_volwId = mysqli_query($db, "
SELECT max(volwId) volwId
FROM tblVolwas
WHERE mdrId = '".mysqli_real_escape_string($db, $MDRID)."' and " . db_null_filter(vdrId, $VDRID) . "
") or die(mysqli_error($db));
    while ($zv = mysqli_fetch_assoc($zoek_volwId)) {
return $zv['volwId'];
    }
}

/**********************************************************************
Toegepast in :
- Melden.php */

// Functie aantal nog te melden
function aantal_te_melden($datb, $lidid, $fldCode) {
$aantalmelden = mysqli_query($datb, "
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($datb, $lidid)."'
 and h.skip = 0
 and isnull(r.dmmeld)
 and code = '".mysqli_real_escape_string($datb, $fldCode)."'
"); // Foutafhandeling zit in return FALSE
    if ($aantalmelden) {
$row = mysqli_fetch_assoc($aantalmelden);
            return $row['aant'];
    }
    return false; // Foutafhandeling
}
// Einde Functie aantal nog te melden

/*
Toegepast in :
- MeldGeboortes.php
- MeldAfvoer.php
- MeldUitval.php
- MeldAanvoer.php
- MeldOmnummer.php */

// Aantal dieren te melden
function aantal_melden($datb, $fldReqId) {

$aantalmelden = mysqli_query($datb, "
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
WHERE m.reqId = '".mysqli_real_escape_string($datb, $fldReqId)."' and m.skip <> 1 and h.skip = 0
");//Foutafhandeling zit in return FALSE

    if ($aantalmelden) {
$row = mysqli_fetch_assoc($aantalmelden);
            return $row['aant'];
    }
    return false;
}


/*
Toegepast in :
- MeldGeboortes.php */

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke($datb, $fldReqId) {

$juistaantal = mysqli_query($datb, "
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m. hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($datb, $fldReqId)."' 
 and h.skip = 0
 and h.datum is not null
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12
 and LENGTH(RTRIM(CAST(h.datum AS UNSIGNED))) = 8
 and m.skip <> 1
");
    if ($juistaantal) {
$row = mysqli_fetch_assoc($juistaantal);
            return $row['aant'];
    }
    return false;
}


/*
Toegepast in :
- MeldGeboortes.php
- MeldAfvoer.php
- MeldUitval.php
- MeldAanvoer.php
- MeldOmnummer.php */

// Zoek controle melding
function zoek_controle_melding($datb, $fldReqId) {
    $aantalcontrole = mysqli_query($datb, "
SELECT count(*) aant
FROM impRespons
WHERE def = 'N' and reqId = '".mysqli_real_escape_string($datb, $fldReqId)."'
");//Foutafhandeling zit in return FALSE

    if ($aantalcontrole) {
        $row = mysqli_fetch_assoc($aantalcontrole);
        return $row['aant'];
    }
    return false;
}
