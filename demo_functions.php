<?php

function demo_table_delete($db, $dtb, $lidId) {
/* Aangemaakt : 14-5-2016  Onderstaande statements moeten worden uitgevoerd om maandelijks demo gegevens te verwijderen.
    Wordt ook gebruikt om handmatig de (basis)gegevens te verwijderen in de test omgevng
22-1-2017 : tblBezetting gewijzigd naar tblBezet
*/
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

function demo_table_insert($db, $lidId) {
/* Aangemaakt : 21-8-2016  Onderstaande statements moeten worden uitgevoerd om voor een bestaande klant maandelijks een demo omgeving te creeëren.
    Wordt ook gebruikt om handmatig de test omgevng te voorzien van nieuwe basisgegevens
18-1-2017 : in tblPeriode doel gewijzigd naar doelId
22-1-2017 : tblBezetting gewijzigd naar tblBezet
5-7-2020 : in tblArtikel en tblArtikel_basis prijs gewijzigd naar perkg, wdgn naar wdgn_v en wdgn_m toegevoegd
7-5-2023 : De tabellen tblHistorie, tblInkoop, tblPeriode, tblLiquiditeit en tblSalber worden ingelezen met actuele datums
11-6-2023 : mdrId en vdrId uit tblSchaap gehaald
 */
    $lidId = mysqli_real_escape_string($db, $lidId);

    // AANVULLEN TABELLEN
    $plus_levnr = ($lidId-1)*100000000;

    /*De datums worden geactualiseerd o.b.v. de datum van vandaag.
     * De maximale datum uit de tabellen tblHistorie_basis, tblInkoop_basis en tblPeriode_basis wordt opgezocht.
     * Het verschil in dagen met vandaag wordt bepaald met de variable $dagen.
     * Elke datum uit de tabellen tblHistorie_basis, tblInkoop_basis en tblPeriode_basis wordt opgehoogd
     *  met het getal in de variabele $dagen.
     */

    $view = mysqli_query($db, "
SELECT max(datum) maximaal
FROM (
    SELECT datum FROM `tblHistorie_basis` 
    UNION
    SELECT dmink FROM `tblInkoop_basis` 
    UNION
    SELECT dmafsluit FROM `tblPeriode_basis` 
) t
");

while ($mx = mysqli_fetch_assoc($view)) {
$dmmax = $mx['maximaal'];
}

$your_date = strtotime($dmmax);
$datediff = time() - $your_date;
$dagen = round($datediff / (60 * 60 * 24));

/* De datums worden geactualiseerd o.b.v. de datum van vandaag.
 * De minimale datum uit de tabellen tblLiquiditeit_basis en tblSalber_basis wordt opgezocht.
 * Het verschil in jaren met vandaag wordt bepaald met de variable $jaren.
 * Elke datum uit de tabellen tblLiquiditeit_basis en tblSalber_basis wordt opgehoogd
 * met het getal in de variabele $jaren.
 */

$view = mysqli_query($db, "
SELECT year(max(datum)) jaar
FROM (
    SELECT datum FROM `tblHistorie_basis` 
    UNION
    SELECT dmink FROM `tblInkoop_basis` 
    UNION
    SELECT dmafsluit FROM `tblPeriode_basis` 
) t
");

while ($mn = mysqli_fetch_assoc($view)) {
    $jaar = $mn['jaar'];
}

$now = DateTime::createFromFormat('U.u', microtime(true));
$ditjaar = $now->format("Y");
$jaren = $ditjaar - $jaar;

// Bepalen modules ja of nee
$view = mysqli_query($db, "SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '$lidId'; ");
while ($mod = mysqli_fetch_assoc($view)) {
    $modbeheer = $mod['beheer'];
    $modtech = $mod['tech'];
    $modfin = $mod['fin'];
    $modmeld = $mod['meld'];
}

/********************    Het schaap deel 1    *******************************************************************/
 //Aanvullen tblSchaap incl veld schaapId_basis als sleutelveld voor andere tabellen (foreign key)
if ($modtech == 1) {
    $SQL = "
INSERT INTO `tblVolwas` (readId, datum, mdrId, vdrId, volwId_basis, lidId_demo)
    SELECT readId, datum, mdrId, vdrId, volwId, '$lidId'
    FROM tblVolwas_basis
    ORDER BY volwId
";
    mysqli_query($db, $SQL);

    $SQL = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, volwId, indx, momId, redId, schaapId_basis, lidId_demo)
    SELECT s.levensnummer+$plus_levnr, s.rasId, s.geslacht, v.volwId, s.indx,    s.momId, s.redId, s.schaapId, '$lidId'
    FROM tblSchaap_basis s
     left join tblVolwas v on (s.volwId = v.volwId_basis and v.lidId_demo = '$lidId')
    ORDER BY s.schaapId
";
    mysqli_query($db, $SQL);

    //mdrId in tblVolwas aanpassen
    $SQL = "
UPDATE tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId_basis and mdr.lidId_demo = '$lidId')
set v.mdrId = mdr.schaapId
";
    mysqli_query($db, $SQL);

    //vdrId in tblVolwas aanpassen
    $SQL = "
UPDATE tblVolwas v
 join tblSchaap vdr on (v.vdrId = vdr.schaapId_basis and vdr.lidId_demo = '$lidId')
set v.vdrId = vdr.schaapId
";
    mysqli_query($db, $SQL);

    // volwId in tblSchaap aanpassen
    $SQL = "
UPDATE tblSchaap s 
 join tblVolwas v on (s.volwId = v.volwId_basis and s.lidId_demo = v.lidId_demo)
set s.volwId = v.volwId 
WHERE v.lidId_demo = '$lidId'
";
    mysqli_query($db, $SQL);
}

if ($modtech == 0) {
    // Alleen aanwezige schapen inlezen
    $SQL = "
INSERT INTO `tblSchaap` (levensnummer, rasId, geslacht, indx, momId, redId, schaapId_basis, lidId_demo)
    SELECT levensnummer+$plus_levnr, rasId, geslacht, indx,    momId, redId, s.schaapId, '$lidId'
    FROM tblSchaap_basis s
     join tblStal_basis st on (st.schaapId = s.schaapId)
     join tblHistorie_basis h on (st.stalId = h.stalId)
    WHERE isnull(st.rel_best)
    GROUP BY levensnummer+$plus_levnr, rasId, geslacht, indx, momId, redId, s.schaapId
    ORDER BY s.schaapId
";
    mysqli_query($db, $SQL);
}
/********************    Einde Het schaap deel 1    *******************************************************************/
/********************    Stamtabellen    *******************************************************************/

//Aanvullen tblRasuser incl veld rasuId_basis
$SQL = "
INSERT INTO tblRasuser (lidId, rasId, scan, actief, rasuId_basis)
    SELECT '$lidId', rasId, scan, actief, rasuId
    FROM tblRasuser_basis
    ORDER BY rasuId
";
mysqli_query($db, $SQL);

//Aanvullen tblMomentuser incl veld momuId_basis
$SQL = "
INSERT INTO tblMomentuser (lidId, momId, scan, actief, momuId_basis)
    SELECT '$lidId', momId, scan, actief, momuId
    FROM tblMomentuser_basis
    ORDER BY momuId
";
mysqli_query($db, $SQL);

//Aanvullen tblRedenuser incl veld reduId_basis
$SQL = "
INSERT INTO tblRedenuser (redId, lidId, uitval, pil, reduId_basis)
    SELECT redId, '$lidId', uitval, pil, reduId
    FROM tblRedenuser_basis
    ORDER BY reduId
";
mysqli_query($db, $SQL);

//Aanvullen tblRubriekuser incl veld rubuId_basis
$SQL = "
INSERT INTO tblRubriekuser (rubId, lidId, actief, rubuId_basis)
    SELECT rubId, '$lidId', actief, rubuId
    FROM tblRubriekuser_basis
    ORDER BY rubuId
";
mysqli_query($db, $SQL);

/********************    Einde Stamtabellen    *******************************************************************/
/********************    Relaties    *******************************************************************/

//Aanvullen tblPartij incl veld partId_basis als sleutelveld voor andere tabellen (foreign key)
$SQL = "
INSERT INTO tblPartij (lidId, ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId_basis)
    SELECT '$lidId', ubn, naam, tel, fax, email, site, banknr, relnr, wachtw, actief, partId
    FROM tblPartij_basis
    ORDER BY partId
";
mysqli_query($db, $SQL);

//Aanvullen tblRelatie incl veld relId_basis als sleutelveld voor andere tabellen (foreign key)
$SQL = "
INSERT INTO tblRelatie (partId, relatie, uitval, actief, relId_basis)
    SELECT p.partId, r.relatie, r.uitval, r.actief, r.relId
    FROM tblRelatie_basis r
     join tblPartij p on (r.partId = p.partId_basis)
    WHERE p.lidId = '$lidId'
    ORDER BY relId
";
mysqli_query($db, $SQL);

//Aanvullen tblAdres incl veld adrId_basis als sleutelveld voor andere tabellen (foreign key)
$SQL = "
INSERT INTO tblAdres (relId, straat, nr, pc, plaats, actief, adrId_basis)
    SELECT r.relId, a.straat, a.nr, a.pc, a.plaats, a.actief, a.adrId
    FROM tblAdres_basis a
     join tblRelatie r on (a.relId = r.relId_basis)
     join tblPartij p on (r.partId = p.partId)
    WHERE p.lidId = '$lidId'
    ORDER BY adrId
";
mysqli_query($db, $SQL);
/********************    Einde    Relaties    *******************************************************************/
/********************    Het schaap deel 2    *******************************************************************/
//Aanvullen tblStal incl veld stalId_basis als sleutelveld voor andere tabellen (foreign key)
$SQL = "
INSERT INTO tblStal (lidId, schaapId, kleur, halsnr, rel_herk, rel_best, stalId_basis)
    SELECT '$lidId', s.schaapId, kleur, halsnr, rh.relId, rb.relId, st.stalId
    FROM tblStal_basis st
     join tblSchaap s on (st.schaapId = s.schaapId_basis)
     left join tblRelatie rh on (st.rel_herk = rh.relId_basis)
     left join tblPartij ph on (ph.partId = rh.partId)
     left join tblRelatie rb on (st.rel_best = rb.relId_basis)
     left join tblPartij pb on (pb.partId = rb.partId)
    WHERE s.lidId_demo = '$lidId' and (isnull(ph.lidId) or ph.lidId = '$lidId') and (isnull(pb.lidId) or pb.lidId = '$lidId')
    ORDER BY st.stalId
";
mysqli_query($db, $SQL);

if ($modtech == 0) {
    //Aanvullen tblHistorie incl veld hisId_basis
    $SQL = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
    SELECT st.stalId, DATE_ADD(h.datum, INTERVAL $dagen DAY), NULL, h.actId, h.skip, h.hisId
    FROM tblHistorie_basis h
     join tblStal st on (h.stalId = st.stalId_basis)
    WHERE st.lidId = '$lidId' and (h.actId = 1 or h.actId = 2 or h.actId = 3 or h.actId = 10 or h.actId = 11 or h.actId = 12 or h.actId = 13 or h.actId = 14)
    ORDER BY h.hisId    
";
    mysqli_query($db, $SQL);
}

if ($modtech == 1) {
    //Aanvullen tblHistorie incl veld hisId_basis
    $SQL = "
INSERT INTO tblHistorie (stalId, datum, kg, actId, skip, hisId_basis)
    SELECT st.stalId, DATE_ADD(h.datum, INTERVAL $dagen DAY), h.kg, h.actId, h.skip, h.hisId
    FROM tblHistorie_basis h
     join tblStal st on (h.stalId = st.stalId_basis)
    WHERE st.lidId = '$lidId'
    ORDER BY h.hisId    
";
    mysqli_query($db, $SQL);
}
/********************    Einde Het schaap deel 2    *******************************************************************/
/********************    Voorraadbeheer        *******************************************************************/
//Aanvullen tblEenheiduser incl veld enhuId_basis
$SQL = "
INSERT INTO tblEenheiduser (lidId, eenhId, actief, enhuId_basis)
    SELECT '$lidId', eenhId, actief, enhuId
    FROM tblEenheiduser_basis
    ORDER BY enhuId
";
mysqli_query($db, $SQL);

if ($modtech == 1) {
    //Aanvullen tblArtikel incl veld artId_basis
    $SQL = "
INSERT INTO tblArtikel (soort, naam, stdat, enhuId, perkg, btw, regnr, relId, wdgn_v, wdgn_m, rubuId, actief, artId_basis)
    SELECT art.soort, art.naam, art.stdat, eu.enhuId, art.perkg, art.btw, art.regnr, r.relId, art.wdgn_v, art.wdgn_m, ru.rubuId, art.actief, art.artId
    FROM tblArtikel_basis art
     join tblEenheiduser eu on (art.enhuId = eu.enhuId_basis)
     join tblRelatie r on (art.relId = r.relId_basis)
     join tblPartij p on (p.partId = r.partId)
     left join tblRubriekuser ru on (art.rubuId = ru.rubuId_basis)
    WHERE eu.lidId = '$lidId'
     and p.lidId = '$lidId'
     and (isnull(ru.lidId) or ru.lidId = '$lidId')
    ORDER BY artId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblInkoop incl veld inkId_basis
    $SQL = " 
        INSERT INTO tblInkoop (dmink, artId, charge, dmvval, inkat, enhuId, prijs, btw, relId, inkId_basis)
        SELECT DATE_ADD(ink.dmink, INTERVAL $dagen DAY), art.artId, ink.charge, ink.dmvval, ink.inkat, eu.enhuId, ink.prijs, ink.btw, r.relId, ink.inkId
        FROM tblInkoop_basis ink
        join tblArtikel art on (ink.artId = art.artId_basis)
        join tblEenheiduser eu_art on (art.enhuId = eu_art.enhuId)
        join tblEenheiduser eu on (ink.enhuId = eu.enhuId_basis)
        join tblRelatie r on (ink.relId = r.relId_basis)
        join tblPartij p on (p.partId = r.partId)
        WHERE eu_art.lidId = '$lidId' and eu.lidId = '$lidId' and p.lidId = '$lidId'
        ORDER BY inkId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblNuttig veld nutId_basis n.v.t.
    $SQL = "
INSERT INTO tblNuttig (hisId, inkId, nutat, stdat, reduId)
    SELECT h.hisId, i.inkId, n.nutat, n.stdat, ru.reduId
    FROM tblNuttig_basis n
    join tblHistorie h on (n.hisId = h.hisId_basis)
     join tblStal st on (st.stalId = h.stalId)
     join tblInkoop i on (n.inkId = i.inkId_basis)
     join tblEenheiduser eu on (i.enhuId = eu.enhuId)
     join tblRedenuser ru on (n.reduId = ru.reduId_basis)
    WHERE st.lidId = '$lidId' and eu.lidId = '$lidId' and ru.lidId = '$lidId'
    ORDER BY nutId
";
    mysqli_query($db, $SQL);
}
/********************    Einde Voorraadbeheer    *******************************************************************/
/********************    Hokken    *******************************************************************/

if ($modtech == 1) {
    //Aanvullen tblHok incl veld hokId_basis
    $SQL = "
INSERT INTO tblHok (lidId, hoknr, scan, actief, hokId_basis)
    SELECT '$lidId', h.hoknr, h.scan, h.actief, h.hokId
    FROM tblHok_basis h
    ORDER BY h.hokId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblPeriode incl veld periId_basis
    $SQL = "
INSERT INTO tblPeriode (hokId, doelId, dmafsluit, periId_basis)
    SELECT h.hokId, p.doelId, DATE_ADD(p.dmafsluit, INTERVAL $dagen DAY), p.periId
    FROM tblPeriode_basis p
     join tblHok h on (h.hokId_basis = p.hokId)
    WHERE h.lidId = '$lidId'
    ORDER BY p.periId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblBezet  bezId_basis n.v.t. i.v.m. geen foreign key
    $SQL = "
INSERT INTO tblBezet (periId, hisId, hokId)
    SELECT p.periId, h.hisId, ho.hokId
    FROM tblBezet_basis b
     left join tblHok ho on (ho.hokId_basis = b.hokId)
     left join tblPeriode p on (b.periId = p.periId_basis and p.hokId = ho.hokId)
     join tblHistorie h on (b.hisId = h.hisId_basis)
     join tblStal st on (st.stalId = h.stalId)    
    WHERE st.lidId = '$lidId' and ho.lidId = '$lidId'
    ORDER BY b.bezId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblVoeding veld voedId_basis n.v.t.
    $SQL = "
INSERT INTO tblVoeding (periId, inkId, nutat, stdat)
    SELECT p.periId, i.inkId, nutat, stdat
    FROM tblVoeding_basis v
    join tblPeriode p on (v.periId = p.periId_basis)
     join tblHok ho on (ho.hokId = p.hokId)
     join tblInkoop i on (v.inkId = i.inkId_basis)
     join tblEenheiduser eu on (i.enhuId = eu.enhuId)
    WHERE ho.lidId = '$lidId' and eu.lidId = '$lidId'
    ORDER BY voedId
";
    mysqli_query($db, $SQL);
}
/********************    Einde Hokken    *******************************************************************/
/********************    Melden        *******************************************************************/

if ($modtech == 1) {
    //Aanvullen tblRequest incl veld reqId_basis
    $SQL = "
INSERT INTO tblRequest (code, def, dmcreate, dmmeld, reqId_basis, lidId_demo)
    SELECT code, def, dmcreate, dmmeld, reqId, '$lidId'
    FROM tblRequest_basis
    ORDER BY reqId;
";
    mysqli_query($db, $SQL);

    //Aanvullen tblMelding meldId_basis n.v.t. i.v.m. geen foreign key
    $SQL = "
INSERT INTO tblMelding (reqId, hisId, meldnr, skip, fout)
    SELECT r.reqId, h.hisId, m.meldnr, m.skip, m.fout
    FROM tblMelding_basis m
     join tblRequest r on (m.reqId = r.reqId_basis)
     join tblHistorie h on (m.hisId = h.hisId_basis)
     join tblStal st on (st.stalId = h.stalId)
    WHERE r.lidId_demo = '$lidId' and st.lidId = '$lidId'
    ORDER BY meldId
";
    mysqli_query($db, $SQL);
}
/********************    Einde Melden    *******************************************************************/
/********************    Reader        *******************************************************************/

//Aanvullen impReader readId_basis n.v.t. i.v.m. geen foreign key
$SQL = "
INSERT INTO impReader (datum, tijd, levnr_geb, teller, rascode, geslacht, moeder, hokcode, gewicht, col10, col11, moment1, col13, moment2, levnr_uitv, teller_uitv, reden_uitv, levnr_afv, teller_afv, ubn_afv, afvoerkg, levnr_aanv, teller_aanv, ubn_aanv, levnr_sp, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl, teller_ovpl, hok_ovpl, reden_pil, levnr_pil, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, readId, lidId, dmcreate)

    Select datum, tijd, levnr_geb+$plus_levnr, teller, rascode, geslacht, moeder+$plus_levnr, hokcode, gewicht, col10, col11, ru_vm.reduId, col13, ru_vm_t.reduId, levnr_uitv+$plus_levnr, teller_uitv, ru_ui.reduId, levnr_afv+$plus_levnr, teller_afv, ubn_afv, afvoerkg, levnr_aanv+$plus_levnr, teller_aanv, ubn_aanv, levnr_sp+$plus_levnr, teller_sp, hok_sp, speenkg, moeder_dr, col30, uitslag, vader_dr, levnr_ovpl+$plus_levnr, teller_ovpl, hok_ovpl, ru_pi.reduId, levnr_pil+$plus_levnr, teller_pil, col39, col40, col41, weegkg, levnr_weeg, col44, verwerkt, null, '$lidId', dmcreate
    FROM impReader_basis rd
     left join tblRedenuser ru_vm on (rd.moment1 = ru_vm.reduId_basis)
     left join tblRedenuser ru_vm_t on (rd.moment2 = ru_vm_t.reduId_basis)
     left join tblRedenuser ru_ui on (rd.reden_uitv = ru_ui.reduId_basis)
     left join tblRedenuser ru_pi on (rd.reden_pil = ru_pi.reduId_basis)
    WHERE (isnull(ru_vm.lidId) or ru_vm.lidId = '$lidId')
     and (isnull(ru_vm_t.lidId) or ru_vm_t.lidId = '$lidId')
     and (isnull(ru_ui.lidId) or ru_ui.lidId = '$lidId')
     and (isnull(ru_pi.lidId) or ru_pi.lidId = '$lidId')
";
mysqli_query($db, $SQL);

if ($modtech == 0) {
    // geboren lammeren zonder levensnummer mogen niet voorkomen als de module technisch niet wordt gebruikt
    $SQL = "UPDATE impReader SET verwerkt = 1 
        WHERE lidId = '$lidId' and isnull(levnr_geb) and teller is not null and isnull(verwerkt) ";
    mysqli_query($db, $SQL);
    // gespeende lammeren, overplaatsing en medicatie mogen niet voorkomen als de module technisch niet wordt gebruikt

    $SQL = "UPDATE impReader SET verwerkt = 1 
        WHERE lidId = '$lidId' and (teller_sp is not null or teller_ovpl is not null or teller_pil is not null) and isnull(verwerkt) ";
    mysqli_query($db, $SQL);
}
/********************    Einde Reader    *******************************************************************/
/********************    Financieel        *******************************************************************/

//Aanvullen tblElementuser incl. veld elemuId_basis
if ($modtech == 1) {
    $SQL = "
INSERT INTO tblElementuser (elemId, lidId, waarde, actief, elemuId_basis)
    SELECT elemId, '$lidId', waarde, actief, elemuId
    FROM tblElementuser_basis
    ORDER BY elemuId
";
    mysqli_query($db, $SQL);

    # $now = DateTime::createFromFormat('U.u', microtime(true));
    # deze toekenning staat eerder al --BCB
    $jaartal = $now->format("Y");
    $maandag52 = date("j", strtotime($jaartal."W"."52"."1"));
    $dag1 = date("d", strtotime("first monday of january $jaartal"));
    $monday1 = date("Y-m-d", strtotime($jaartal."W"."01"."1"));
    $day = strtotime($monday1)-(86400*7);

    if ($maandag52 > 24) {
        $weken_jaar = 52;
    } else {
        $weken_jaar = 53;
    }
    if ($dag1 < 05) {
        $startweek = 1;
    } else {
        $startweek = 2;
    }

    for ($i = $startweek; $i <= $weken_jaar; $i++) {
        if ($startweek == 2) {
            $dekId = $i-1;
        } else {
            $dekId = $i;
        }
        $datum = date('Y-m-d', $day+($i*86400*7));

        if ($dekId < 53) {
            $SQL = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
    SELECT '$lidId', dekat, '$datum'
    FROM tblDeklijst_basis
    WHERE dekId = '$dekId'
";
        } else {
            $SQL = "
INSERT INTO tblDeklijst (lidId, dmdek) VALUES ('$lidId', '$datum')
";
        }
        mysqli_query($db, $SQL);
    }

/*
$SQL = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
    SELECT '$lidId', dekat, dmdek
    FROM tblDeklijst_basis
    ORDER BY dekId
";
    mysqli_query($db, $SQL);
 */

    //Aanvullen tblLiquiditeit veld liqId_basis n.v.t.
    $SQL = "
INSERT INTO tblLiquiditeit (rubuId, datum, bedrag)
    SELECT ru.rubuId, DATE_ADD(l.datum, INTERVAL $jaren-1 YEAR), l.bedrag
    FROM tblLiquiditeit_basis l
     join tblRubriekuser ru on (l.rubuId = ru.rubuId_basis)
    WHERE ru.lidId = '$lidId'
    ORDER BY liqId
";
    mysqli_query($db, $SQL);

    //Aanvullen tblSalber veld salbId_basis n.v.t.
    $SQL = "
INSERT INTO tblSalber (datum, tbl, tblId, aantal, waarde)
    SELECT DATE_ADD(sb.datum, INTERVAL $jaren YEAR), 'ru' tbl, ru.rubuId tblId, sb.aantal, sb.waarde
    FROM tblSalber_basis sb
     join tblRubriekuser ru on (sb.tblId = ru.rubuId_basis)
    WHERE sb.tbl = 'ru' and ru.lidId = '$lidId'

    union

    SELECT DATE_ADD(sb.datum, INTERVAL $jaren YEAR), 'eu' tbl, eu.elemuId tblId, sb.aantal, sb.waarde
    FROM tblSalber_basis sb
     join tblElementuser eu on (sb.tblId = eu.elemuId_basis)
    WHERE sb.tbl = 'eu' and eu.lidId = '$lidId'
    ORDER BY tbl, tblId
";
    mysqli_query($db, $SQL);
}
/********************    Einde Financieel    *******************************************************************/
/************* verwijderd ****************************************************/
//Aanvullen impRespons respId_basis n.v.t. i.v.m. geen foreign key
/*
$SQL = "
INSERT INTO impRespons (reqId, prod, def, urvo, prvo, melding, relnr, ubn, schaapdm, land, levensnummer, soort, ubn_herk, ubn_best, land_herk, gebdm, sucind, foutind, foutcode, foutmeld, meldnr, respId, dmcreate)
    SELECT rq.reqId, rs.prod, rs.def, rs.urvo, rs.prvo, rs.melding, rs.relnr, rs.ubn, rs.schaapdm, rs.land, s.levensnummer, rs.soort, rs.ubn_herk, rs.ubn_best, rs.land_herk, rs.gebdm, rs.sucind, rs.foutind, rs.foutcode, rs.foutmeld, rs.meldnr, null, rs.dmcreate
    FROM imprespons_basis rs
     join tblRequest rq on (rs.reqId = rq.reqId_basis)
     join tblSchaap_basis sb on (sb.levensnummer = rs.levensnummer)
     join tblSchaap s on (sb.schaapId = s.schaapId_basis)
     join tblStal st on (st.schaapId = s.schaapId)
    WHERE rq.lidId_demo = '$lidId' and st.lidId = '$lidId'
ORDER BY rs.respId
";
    //mysqli_query($db, $SQL);
 */

//Aanvullen tblDeklijst veld dekId_basis n.v.t.
/*
$SQL = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
    SELECT '$lidId', dekat, dmdek
    FROM tblDeklijst_basis
    ORDER BY dekId
";
    //mysqli_query($db, $SQL);
 */

//Aanvullen tblOpgaaf veld dekId_basis n.v.t.
/*
$SQL = "
INSERT INTO tblOpgaaf (rubuId, datum, bedrag, toel, liq, his, dmcreate)
    SELECT ru.rubuId, datum, bedrag, toel, liq, his, dmcreate
    FROM tblOpgaaf_basis o
     join tblRubriekuser ru on (o.rubuId = ru.rubuId_basis)
     WHERE ru.lidId = '$lidId'
    ORDER BY opgId
";
    //mysqli_query($db, $SQL);
 */

/************* Einde verwijderd ****************************************************/
}
