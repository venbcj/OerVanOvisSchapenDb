<?php
/* Aangemaakt : 14-5-2016  Onderstaande statements moeten worden uitgevoerd om maandelijks demo gegevens te verwijderen.
    Wordt ook gebruikt om handmatig de (basis)gegevens te verwijderen in de test omgevng
22-1-2017 : tblBezetting gewijzigd naar tblBezet
*/

function demo_table_delete($db, $dtb, $lidId) {
    $lidId = mysqli_real_escape_string($db, $lidId);
// VERWIJDEREN RECORDS
/********************    Voorraadbeheer    *******************************************************************/

//tblNuttig
$view = mysqli_query($db, "
    SELECT n.nutId
    FROM $dtb.tblNuttig n
     join $dtb.tblHistorie h on (h.hisId = n.hisId)
     join $dtb.tblStal st on (st.stalId = h.stalId)
    WHERE st.lidId = $lidId
    ORDER BY n.nutId
    ");
$ids = array();
while ($nut = mysqli_fetch_assoc($view)) {
    $ids[] = $nut['nutId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblNuttig` WHERE nutId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
// Na de stap "noem hetzelfde wat hetzelfde doet" zie ik,
// dit kan ook in 1x:
// DELETE n FROM $dtb.tblNuttig n
// INNER JOIN $dtb.tblHistorie h USING(hisId)
// INNER JOIN $dtb.tblStal st USING(stalId)
// WHERE st.lidId = $lidId
//
//Einde tblNuttig

//tblInkoop
$view = mysqli_query($db, "
    SELECT i.inkId
    FROM $dtb.tblInkoop i
     join $dtb.tblArtikel a on (a.artId = i.artId)
     join $dtb.tblEenheiduser eu on (eu.enhuId = a.enhuId)
    WHERE eu.lidId = $lidId
    ORDER BY i.inkId
    ");
$ids = array();
while ($ink = mysqli_fetch_assoc($view)) {
    $ids[] = $ink['inkId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblInkoop` WHERE inkId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblInkoop

//tblArtikel
$view = mysqli_query($db, "
    SELECT a.artId
    FROM $dtb.tblArtikel a
     join $dtb.tblEenheiduser eu on (eu.enhuId = a.enhuId)
    WHERE eu.lidId = $lidId
    ORDER BY a.artId
    ");
$ids = array();
while ($art = mysqli_fetch_assoc($view)) {
    $ids[] = $art['artId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblArtikel` WHERE artId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblArtikel

//tblEenheiduser
$view = mysqli_query($db, "
    SELECT eu.enhuId
    FROM $dtb.tblEenheiduser eu
    WHERE eu.lidId = $lidId
    ");
$ids = array();
while ($enhu = mysqli_fetch_assoc($view)) {
    $ids[] = $enhu['enhuId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblEenheiduser` WHERE enhuId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblEenheiduser

/********************    Einde Voorraadbeheer    *******************************************************************/

/********************    Melden    *******************************************************************/

//tblRequest
$view = mysqli_query($db, "
    SELECT r.reqId
    FROM $dtb.tblRequest r
     join $dtb.tblMelding m on (r.reqId = m.reqId)
     join $dtb.tblHistorie h on (h.hisId = m.hisId)
     join $dtb.tblStal st on (st.stalId = h.stalId)
    WHERE st.lidId = $lidId
    GROUP BY r.reqId
    ORDER BY r.reqId
    ");
$ids = array();
while ($req = mysqli_fetch_assoc($view)) {
    $ids[] = $req['reqId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblRequest` WHERE reqId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblRequest

//tblMelding
$view = mysqli_query($db, "
    SELECT m.meldId
    FROM $dtb.tblMelding m
     join $dtb.tblHistorie h on (h.hisId = m.hisId)
     join $dtb.tblStal st on (st.stalId = h.stalId)
    WHERE st.lidId = $lidId
    ORDER BY m.meldId
    ");
$ids = array();
while ($meld = mysqli_fetch_assoc($view)) {
    $ids[] = $meld['meldId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblMelding` WHERE meldId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblMelding

/********************    Einde Melden    *******************************************************************/

/********************    Het schaap        *******************************************************************/

//tblVolwas
$view = mysqli_query($db, "
    SELECT v.volwId
    FROM $dtb.tblVolwas v
     join $dtb.tblSchaap s on (v.volwId = s.volwId)
     join $dtb.tblStal st on (s.schaapId = st.schaapId)
    WHERE st.lidId = $lidId
    ORDER BY v.volwId
    ");
$ids = array();
while ($volwas = mysqli_fetch_assoc($view)) {
    $ids[] = $volwas['volwId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblVolwas` WHERE volwId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblVolwas

//tblSchaap
$view = mysqli_query($db, "
    SELECT s.schaapId
    FROM $dtb.tblSchaap s
     join $dtb.tblStal st on (s.schaapId = st.schaapId)
    WHERE st.lidId = $lidId
    ORDER BY s.schaapId
    ");
$ids = array();
while ($schaap = mysqli_fetch_assoc($view)) {
    $ids[] = $schaap['schaapId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblSchaap` WHERE schaapId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblSchaap

//tblHistorie
$view = mysqli_query($db, "
    SELECT h.hisId
    FROM $dtb.tblHistorie h
     join $dtb.tblStal st on (st.stalId = h.stalId)
    WHERE st.lidId = $lidId
    ORDER BY h.hisId
    ");
$ids = array();
while ($his = mysqli_fetch_assoc($view)) {
    $ids[] = $his['hisId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblHistorie` WHERE hisId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblHistorie

//tblStal
$view = mysqli_query($db, "
    SELECT st.stalId
    FROM $dtb.tblStal st
    WHERE st.lidId = $lidId
    ORDER BY st.stalId
    ");
$ids = array();
while ($stal = mysqli_fetch_assoc($view)) {
    $ids[] = $stal['stalId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblStal` WHERE stalId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblStal

// Dieren die niet zijn gekoppeld aan een stalId verwijderen. Dit kan bij inlezen dracht vaderdieren zijn.
// Wordt niet verwijderd als dit dier bij anderen ook voorkomt in impReader => dracht. Zie not exists

//tblSchaap
$view = mysqli_query($db, "
    SELECT s.schaapId
    FROM $dtb.tblSchaap s
     join $dtb.impReader r on (r.levnr_ovpl = s.levensnummer)
     left join $dtb.tblStal st on (s.schaapId = st.schaapId)
    WHERE r.lidId = $lidId and isnull(st.stalId) and isnull(teller_ovpl)
     and not exists (
        SELECT rd.levnr_ovpl
        FROM $dtb.impReader rd
        WHERE s.levensnummer = rd.levnr_ovpl and isnull(rd.teller_ovpl) and rd.lidId <> $lidId
        )
    GROUP BY s.schaapId
    ORDER BY s.schaapId
    ");
$ids = array();
while ($sch = mysqli_fetch_assoc($view)) {
    $ids[] = $sch['schaapId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblSchaap` WHERE schaapId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblSchaap

/********************    Einde Het schaap    *******************************************************************/

/********************    Reader        *******************************************************************/

// Dracht uit tabel tblVolwas halen. Dit is het restant uit tabel tblVolwas nadat de schapen zijn verwijderd hierboven.

//tblVolwas
$view = mysqli_query($db, "
    SELECT v.volwId
    FROM $dtb.tblVolwas v
     join impReader r on (v.readId = r.readId)
    WHERE r.lidId = $lidId
    ORDER BY v.volwId
    ");
$volwId = array();
while ($volw = mysqli_fetch_assoc($view)) {
    $ids[] = $volw['volwId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblVolwas` WHERE volwId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblVolwas

//impReader
$del_impReader = "DELETE FROM ".$dtb.".`impReader` WHERE lidId = $lidId ; ";
mysqli_query($db, $del_impReader);

/********************    Einde Reader    *******************************************************************/

/********************    Relaties        *******************************************************************/

//tblPersoon
$view = mysqli_query($db, "
    SELECT ps.persId
    FROM $dtb.tblPersoon ps
     join $dtb.tblPartij p on (p.partId = ps.partId)
    WHERE p.lidId = $lidId
    ORDER BY ps.persId 
    ");
$ids = array();
while ($pers = mysqli_fetch_assoc($view)) {
    $ids[] = $pers['persId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblPersoon` WHERE persId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblPersoon

//tblVervoer
$view = mysqli_query($db, "
    SELECT v.vervId
    FROM $dtb.tblVervoer v
     join $dtb.tblPartij p on (p.partId = v.partId)
    WHERE p.lidId = $lidId
    ORDER BY v.vervId
    ");
$ids = array();
while ($verv = mysqli_fetch_assoc($view)) {
    $ids[] = $verv['vervId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblVervoer` WHERE vervId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblVervoer

//tblAdres
$view = mysqli_query($db, "
    SELECT a.adrId
    FROM $dtb.tblAdres a
     join $dtb.tblRelatie r on (a.relId = r.relId)
     join $dtb.tblPartij p on (p.partId = r.partId)
    WHERE p.lidId = $lidId
    ORDER BY a.adrId
    ");
$ids = array();
while ($adr = mysqli_fetch_assoc($view)) {
    $ids[] = $adr['adrId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblAdres` WHERE adrId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblAdres

//tblRelatie
$view = mysqli_query($db, "
    SELECT r.relId
    FROM $dtb.tblRelatie r
     join $dtb.tblPartij p on (p.partId = r.partId)
    WHERE p.lidId = $lidId
    ORDER BY r.relId
    ");
$ids = array();
while ($rel = mysqli_fetch_assoc($view)) {
    $ids[] = $rel['relId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblRelatie` WHERE relId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblRelatie

//tblPartij
$view = mysqli_query($db, "
    SELECT p.partId
    FROM $dtb.tblPartij p
    WHERE p.lidId = $lidId
    ORDER BY p.partId
    ");
$ids = array();
while ($part = mysqli_fetch_assoc($view)) {
    $ids[] = $part['partId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblPartij` WHERE partId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblPartij

/********************    Einde Relaties    *******************************************************************/

/********************    Hokken        *******************************************************************/

//tblBezet
$view = mysqli_query($db, "
    SELECT b.bezId
    FROM $dtb.tblBezet b
     join $dtb.tblPeriode p on (b.periId = p.periId)
     join $dtb.tblHok h on (p.hokId = h.hokId)
    WHERE h.lidId = $lidId
    ORDER BY b.bezId
    ");
$ids = array();
while ($bez = mysqli_fetch_assoc($view)) {
    $ids[] = $bez['bezId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblBezet` WHERE bezId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblBezet

//tblVoeding
$view = mysqli_query($db, "
    SELECT v.voedId
    FROM $dtb.tblVoeding v
     join $dtb.tblPeriode p on (v.periId = p.periId)
     join $dtb.tblHok h on (p.hokId = h.hokId)
    WHERE h.lidId = $lidId
    ORDER BY v.voedId
    ");
$ids = array();
while ($voed = mysqli_fetch_assoc($view)) {
    $ids[] = $voed['voedId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblVoeding` WHERE voedId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblVoeding

//tblPeriode
$view = mysqli_query($db, "
    SELECT p.periId
    FROM $dtb.tblPeriode p
     join $dtb.tblHok h on (p.hokId = h.hokId)
    WHERE h.lidId = $lidId
    ORDER BY p.periId
    ");
$ids = array();
while ($peri = mysqli_fetch_assoc($view)) {
    $ids[] = $peri['periId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblPeriode` WHERE periId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblPeriode

//tblHok
$view = mysqli_query($db, "
SELECT h.hokId
FROM $dtb.tblHok h
WHERE h.lidId = $lidId
ORDER BY h.hokId
");
$ids = array();
while ($hok = mysqli_fetch_assoc($view)) {
    $ids[] = $hok['hokId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblHok` WHERE hokId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblHok

/********************    Einde Hokken    *******************************************************************/

/********************    Financieel        *******************************************************************/

//tblLiquiditeit
$view = mysqli_query($db, "
    SELECT l.liqId
    FROM $dtb.tblLiquiditeit l
     join $dtb.tblRubriekuser ru on (ru.rubuId = l.rubuId)
    WHERE ru.lidId = $lidId
    ORDER BY l.liqId
    ");
$ids = array();
while ($liq = mysqli_fetch_assoc($view)) {
    $ids[] = $liq['liqId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblLiquiditeit` WHERE liqId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblLiquiditeit

//tblOpgaaf
$view = mysqli_query($db, "
    SELECT o.opgId
    FROM $dtb.tblOpgaaf o
     join $dtb.tblRubriekuser ru on (ru.rubuId = o.rubuId)
    WHERE ru.lidId = $lidId
    ORDER BY o.opgId
    ");
$ids = array();
while ($opg = mysqli_fetch_assoc($view)) {
    $ids[] = $opg['opgId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblOpgaaf` WHERE opgId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblOpgaaf

//tblSalber
$view = mysqli_query($db, "
    SELECT sb.salbId
    FROM $dtb.tblSalber sb
     join $dtb.tblRubriekuser ru on (ru.rubuId = sb.tblId)
    WHERE ru.lidId = $lidId and tbl = 'ru'
    UNION
    SELECT sb.salbId
    FROM $dtb.tblSalber sb
     join $dtb.tblElementuser eu on (eu.elemuId = sb.tblId)
    WHERE eu.lidId = $lidId and tbl = 'eu'
    ORDER BY salbId
    ");
$ids = array();
while ($opg = mysqli_fetch_assoc($view)) {
    $ids[] = $opg['salbId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblSalber` WHERE salbId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblSalber

//tblDeklijst
$view = mysqli_query($db, "
    SELECT d.dekId
    FROM $dtb.tblDeklijst d
    WHERE d.lidId = $lidId
    ORDER BY d.dekId
    ");
$ids = array();
while ($dek = mysqli_fetch_assoc($view)) {
    $ids[] = $dek['dekId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblDeklijst` WHERE dekId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblDeklijst

//tblRubriekuser
$view = mysqli_query($db, "
    SELECT ru.rubuId
    FROM $dtb.tblRubriekuser ru
    WHERE ru.lidId = $lidId
    ORDER BY ru.rubuId
    ");
$ids = array();
while ($rubu = mysqli_fetch_assoc($view)) {
    $ids[] = $rubu['rubuId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblRubriekuser` WHERE rubuId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblRubriekuser

//tblElementuser
$view = mysqli_query($db, "
    SELECT eu.elemuId
    FROM $dtb.tblElementuser eu
    WHERE eu.lidId = $lidId
    ORDER BY eu.elemuId
    ");
$ids = array();
while ($elemu = mysqli_fetch_assoc($view)) {
    $ids[] = $elemu['elemuId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblElementuser` WHERE elemuId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblElementuser

/********************    Einde Financieel    *******************************************************************/

/********************    Stamtabellen    *******************************************************************/

//tblMomentuser
$view = mysqli_query($db, "
    SELECT mu.momuId
    FROM $dtb.tblMomentuser mu
    WHERE mu.lidId = $lidId
    ORDER BY mu.momuId
    ");
$ids = array();
while ($momu = mysqli_fetch_assoc($view)) {
    $ids[] = $momu['momuId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblMomentuser` WHERE momuId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblMomentuser

//tblRasuser
$view = mysqli_query($db, "
    SELECT ru.rasuId
    FROM $dtb.tblRasuser ru
    WHERE ru.lidId = $lidId
    ORDER BY ru.rasuId
    ");
$ids = array();
while ($rasu = mysqli_fetch_assoc($view)) {
    $ids[] = $rasu['rasuId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblRasuser` WHERE rasuId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}
//Einde tblRasuser

//tblRedenuser
$view = mysqli_query($db, "
    SELECT ru.reduId
    FROM $dtb.tblRedenuser ru
    WHERE ru.lidId = $lidId
    ORDER BY ru.reduId
    ");
$ids = array();
while ($redu = mysqli_fetch_assoc($view)) {
    $ids[] = $redu['reduId'];
}
if (count($ids)) {
    $SQL = "DELETE FROM $dtb.`tblRedenuser` WHERE reduId IN (".implode(',', $ids).") ; ";
    mysqli_query($db, $SQL);
}

//Einde tblRedenuser

/********************    Einde Stamtabellen    *******************************************************************/
}
