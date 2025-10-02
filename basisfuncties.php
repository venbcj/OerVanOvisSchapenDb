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
    if (mysqli_num_rows($view) > 0) {
        return mysqli_fetch_row($view)[0];
    }
    return null;
}

/*Toegepast in :
- Dekkingen.php
 */
// TODO: #0004160 alle query-functies op deze manier herschrijven --BCB
function startjaar_gebruiker($lidId) {
    global $db;
    $lidId = mysqli_real_escape_string($db, $lidId);
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

function last_day_of_month($d) {
    $day = new DateTime($d); 
    return $day->format( 'Y-m-t' );
}

function db_filter_afvoerdatum($keuze){
    global $db;
    //Evt kun je ook meteen is_boolean($var) omzetten naar 0/1, enz
    return $keuze == 1 ? "(isnull(afv.datum) or (afv.datum > date_add(curdate(), interval -666 month) )) and " : "isnull(afv.stalId) and ";
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

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
// De datum mag hier niet liggen voor de geboorte datum en speendatum verder zal er geen historie zijn.
function aantal_oke_aanv($datb, $fldReqId) {
    $juistaantal = mysqli_query($datb, "
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId) 
 left join (
    SELECT schaapId, max(datum) datum 
    FROM tblHistorie h 
     join tblStal st on (h.stalId = st.stalId)
    WHERE h.actId <= 4 and h.actId != 2 and h.skip = 0
    GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($datb, $fldReqId)."'
 and h.datum is not null
 and (h.datum >= mhd.datum or isnull(mhd.datum))
 and h.datum <= (curdate() + interval 3 day)
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12
 and m.skip <> 1
");
    /* Herkomst (ubn_herk) is niet verplicht te melden */
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

function zoek_oudste_request_niet_definitief_gemeld($db, $lidId) {
    $zoek_oudste_request_niet_definitief_gemeld = mysqli_query($db, "
    SELECT min(rq.reqId) reqId, l.relnr
    FROM tblRequest rq
     join tblMelding m on (rq.reqId = m.reqId)
     join tblHistorie h on (h.hisId = m.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblLeden l on (l.lidId = st.lidId)
    WHERE h.skip = 0 and l.lidId = '".mysqli_real_escape_string($db, $lidId)."' and isnull(rq.dmmeld) and rq.code = 'AAN' 
    GROUP BY l.relnr
    ") or die(mysqli_error($db));
    while ($req = mysqli_fetch_assoc($zoek_oudste_request_niet_definitief_gemeld)) {
        $reqId = $req['reqId'];
    }
    return $reqId ?? 0;
}

function alias_voor_lid($db, $lidId) {
    $qry_Leden = mysqli_query($db, "
    SELECT alias
    FROM tblLeden
    WHERE lidId = '".mysqli_real_escape_string($db, $lidId)."'
    ") or die(mysqli_error($db));
    while ($row = mysqli_fetch_assoc($qry_Leden)) {
        $alias = $row['alias'];
    }
    return $alias;
}

function aanvoer_request_rvo_query($db, $reqId) {
    return mysqli_query($db, "
    SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, u.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer, 3 soort, p.ubn ubn_herk, NULL ubn_best, NULL land_herk, date_format(hg.datum,'%d-%m-%Y'), NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
    FROM tblRequest rq
     join tblMelding m on (rq.reqId = m.reqId)
     join tblHistorie h on (m.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblLeden l on (st.lidId = l.lidId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     join tblStal st_all on (s.schaapId = st_all.schaapId)
     left join tblHistorie hg on (hg.stalId = st_all.stalId)
     left join tblRelatie rl on (rl.relId = st.rel_herk)
     left join tblPartij p on (p.partId = rl.partId)
    WHERE rq.reqId = '".mysqli_real_escape_string($db, $reqId)."'
        and (isnull(hg.actId) or hg.actId = 1)
        and h.datum is not null
        and (h.datum >= hg.datum or isnull(hg.datum))
        and h.datum <= (curdate() + interval 3 day)
        and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
        and m.skip <> 1
        and isnull(m.fout) 
        and h.skip = 0
        ");
}

function aanvoer_zoek_meldregels_query($db, $reqId, $lidId) {
    return mysqli_query($db, "
SELECT m.meldId, u.ubn ubn_gebruiker, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, s.levensnummer, s.geslacht,
  ouder.datum dmaanw, st.stalId, st.rel_herk, p.naam, p.ubn ubn_herk, m.skip, m.fout, rs.respId, rs.sucind, rs.foutmeld,
 lastdm.datum dmlst, date_format(lastdm.datum,'%d-%m-%Y') lstdm
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join (
     SELECT m.meldId, NULL BijDefinitiefMeldenVerwijderdenNietTonen
     FROM tblMelding m
     join tblRequest r on (r.reqId = m.reqId)
     WHERE m.reqId = '".mysqli_real_escape_string($db, $reqId)."' and m.skip = 1 and r.def = 'J' and dmmeld is not null
 ) hide on (hide.meldId = m.meldId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId)
 left join tblRelatie r on (r.relId = st.rel_herk)
 left join tblPartij p on (r.partId = p.partId)
 left join (
    SELECT max(respId) respId, levensnummer
    FROM impRespons
    WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."'
    GROUP BY levensnummer
 ) mresp on (mresp.levensnummer = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
 left join (
    SELECT st.schaapId, max(datum) datum 
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db, $lidId)."' and 
     not exists (SELECT max(stl.stalId) stalId FROM tblStal stl WHERE stl.lidId = '".mysqli_real_escape_string($db, $lidId)."' and stl.stalId = st.stalId)
    GROUP BY st.schaapId
 ) lastdm on (lastdm.schaapId = s.schaapId)
WHERE h.skip = 0 and m.reqId = '".mysqli_real_escape_string($db, $reqId)."' and isnull(hide.meldId)
ORDER BY u.ubn, m.skip, s.levensnummer 
");
}

function zoek_eerder_stalId($db, $levnr, $stalId) {
    $eerder_stalId = mysqli_query($db, "
SELECT max(stalId) stalId 
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE s.levensnummer = '".mysqli_real_escape_string($db, $levnr)."' and st.stalId != ".mysqli_real_escape_string($db, $stalId)."
") or die(mysqli_error($db));
    while ($vor = mysqli_fetch_assoc($eerder_stalId)) {
        $vorigStalId = $vor['stalId'];
    }
    return $vorigStalId;
}

function zoek_naam_partij($db, $rel_hrk) {
    $NaamPartij = mysqli_query($db, "
    SELECT naam
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE r.relId = ".db_null_input($rel_hrk));
    while ($p = mysqli_fetch_assoc($NaamPartij)) {
        $naam = $p['naam'];
    }
    return $naam ?? '';
}

function registreer_melddatum($db, $reqId) {
    // Melddatum registreren in tblRequest bij > 0 te melden en definitieve melding
    $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."' and def = 'J' ";
    mysqli_query($db, $upd_tblRequest) or die(mysqli_error($db));
}

function registreer_melddatum_definitief($db, $reqId) {
    // Melddatum registreren in tblRequest bij 0 te melden
    $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now(), def = 'J' WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."' ";
    mysqli_query($db, $upd_tblRequest) or die(mysqli_error($db));
}

function zoek_request_definitief($db, $reqId) {
    $definitief = mysqli_query($db, "
    SELECT r.def 
    FROM tblRequest r 
    WHERE r.reqId = '".mysqli_real_escape_string($db, $reqId)."' 
    ") or die(mysqli_error($db));
    $def = '';
    while ($defi = mysqli_fetch_assoc($definitief)) {
        $def = $defi['def'];
    }
    return $def;
}


function zoek_eerste_stalrecord($lidId) {
    global $db;
    $maand_voorbij = mysqli_query($db, "
        SELECT date_format(min(st.dmcreatie),'%Y%m') maand FROM tblStal st
         WHERE st.lidId = '".mysqli_real_escape_string($db, $lidId)."'
            ") or die(mysqli_error($db));
    while ($ym = mysqli_fetch_assoc($maand_voorbij)) {
        $controle_maand = $ym['maand'];
    }
    return $controle_maand;
}

// global seams

function setup_db() {
    global $db;
    include "database.php";
    if (!isset($db) || !$db) {
    $db = mysqli_connect($host, $user, $pw, $dtb);
    if ($db == false) {
        throw new Exception('Connectie database niet gelukt');
    }
    # TODO: (BCB) #0004161 dit is de plek om db centraal te registreren, in een object dat op Session lijkt (bv Current)
    # Nu is dat precies in het geval van db waarschijnlijk niet nodig, omdat:
    # - db is voor database-operaties
    # - alle database-operaties verhuizen eerst naar Gateway-objecten
    # - Gateway houdt de verantwoordelijkheid voor de query-formuleringen, maar
    # - Een nieuw object (DbView) gaat de communicatie met mysql verzorgen
    }
    return [
        'db' => $db,
        // dat deze twee nodig zijn in de applicatie snap ik nog niet --BCB
        'dtb' => $dtb,
        'db_p' => $db_p,
    ];
}

    // TODO: (BV) #0004162 gaat onderstaand commentaar over de drie delen van de union? of over de drie "methoden" laatste-versie, readersetup, readertaken?
    /* Eerste query zoek alleen readerApp versies
    Tweede query zoek naar readerApp versie i.c.m. taakversies
    Derde query zoek naar alleen taakversies */
function setup_versies($db, $persoonlijke_map) {
    $versie_gateway = new VersieGateway();
    $last_versieId = $versie_gateway->zoek_laatste_versie();
    $Readersetup_bestand = $versie_gateway->zoek_readersetup_in($last_versieId);
    $Readertaken_bestand = $versie_gateway->zoek_readertaken_in($last_versieId);

    // hee, dit fragment /staat/ al in Readerversies.php
    if (isset($Readersetup_bestand)) {
        $appfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readersetup_bestand);
    } else {
        $appfile_exists = 1;
    }

    // hee, dit fragment /staat/ al in Readerversies.php
    if (isset($Readertaken_bestand)) {
        $takenfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readertaken_bestand);
    } else {
        $takenfile_exists = 1;
    }

    if ($appfile_exists == 1 && $takenfile_exists == 1) {
        // deze variabele komt terug in menu1 en menubeheer, maar ook aangeroepen vanuit header.tpl. Daarom moet-ie er nu al zijn.
        $actuele_versie = 'Ja';
    }
    return compact(explode(' ', 'last_versieId Readersetup_bestand Readertaken_bestand appfile_exists takenfile_exists actuele_versie'));
}

