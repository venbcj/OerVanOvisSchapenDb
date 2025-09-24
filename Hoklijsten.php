<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt en quotes bij "hok", "groep", "aantl", "aant", "ent1", "ent2", "startdm" en "vanaf" verwijderd 
28-2-2015 : login toegevoegd 
14-11-2015 : 1e en 2e inenting verwijderd
22-11-2015 Link rsp. Spenen en Afleveren verwijderd en kolom geboortedatum toegevoegd bij doelgroep Gespeend o.v.v. Rina */
$versie = '12-11-2016'; /* query hok_inhoud aangepast. left join relatie met geboortedatum i.p.v. join. Bij aankoop hoeft geboortedatum nl. niet bekend te zijn. */
$versie = '15-1-2017'; /* Veld generatie toegevoegd. */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = "6-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen        13-2-2017 : tekst hok gewijzigd naar verblijf */
$versie = "1-3-2017"; /* Ras niet verplicht gemaakt door left join te maken */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '18-05-2019'; /* Afleveren, spenen Overplaatsen en Aanwas hier mogelijk gemaakt */
$versie = '25-07-2019'; /* Gesorteerd op werknr */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '22-12-2019'; /* Dubbele querys zoek_nu_in_verblijf_geb, zoek_nu_in_verblijf_spn en zoek_nu_in_verblijf_prnt verwijderd */
$versie = '8-2-2021'; /* zoek_nu_in_verblijf_prnt herschreven i.v.m. dubbele records. Sql beveiligd met quotes 
h2.actId != 3 uit query hok_inhoud_vanaf_aanwas gehaald zodat aanwas ook uit verblijf wordt gehaald. Dit leverde nl. een dubbele record op i.c.m. een overplaatsing bij schaapId 5856 */
$versie = '11-7-2021'; /* Schapen uit verblijf herzien. Join gewijzigd van h.hisId = uit.hisv naar b.bezId = uit.bezId */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie 14-01-2024 Gemiddelde groei en kg voer weggehaald. Dit is niet te berekenen */
$versie = '19-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = '03-03-2024'; /*Laatst gewogen gewicht toegevoegd */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = "10-11-2024"; /* Uitscharen toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '20-01-2025'; /* In subquery hg where clause gewijzigd van h.actId = 1 naar h.actId = 1 and h.skip = 0 */
$versie = '23-02-2025'; /* $_SESSION["Fase"] = NULL toegevoegd */
$versie = '13-07-2025'; /* veld Ubn toegevoegd */
 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>

<?php
$titel = 'Verblijflijst';
$file = "HokkenBezet.php";
include "login.php"; ?>

        <TD valign = "top">
<?php
if (Auth::is_logged_in()) { 

$Id = $_GET['pst'] ?? 0;

$zoek_hok = mysqli_query ($db,"
SELECT hoknr FROM tblHok WHERE hokId = '".mysqli_real_escape_string($db,$Id)."'
") or die (mysqli_error($db));
$hoknr = 0;
    while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; } 

// ***** BEZETTING VANAF LAATSTE PERIODE ***** 
$zoek_startdatum_programma = mysqli_query($db,"
SELECT date_format(dmcreate,'%d-%m-%Y') startdm
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
    while ($sp = mysqli_fetch_assoc($zoek_startdatum_programma)) { 
        $startdm = $sp['startdm']; }

// Doelgroep geboren
$zoek_data_aantal_geb = mysqli_query($db,"
SELECT count(st.schaapId) aant, date_format(endgeb.dmstop,'%d-%m-%Y') stopdm
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
   FROM tblBezet b
    join tblHistorie h1 on (b.hisId = h1.hisId)
    join tblActie a1 on (a1.actId = h1.actId)
    join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
    join tblActie a2 on (a2.actId = h2.actId)
    join tblStal st on (h1.stalId = st.stalId)
   WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
   GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
    SELECT st.schaapId, h.datum
   FROM tblStal st
    join tblHistorie h on (st.stalId = h.stalId)
   WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT p.hokId, max(p.dmafsluit) dmstop
    FROM tblPeriode p
    WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 1 and dmafsluit is not null
    GROUP BY p.hokId
 ) endgeb on (endgeb.hokId = b.hokId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."'
 and (isnull(ht.hisId) or ht.datum > coalesce(dmstop,'1973-09-11'))
 and (isnull(spn.schaapId) or h.datum < spn.datum)
 and (isnull(prnt.schaapId) or h.datum < prnt.datum)
GROUP BY endgeb.dmstop
") or die (mysqli_error($db));
    while ($zda = mysqli_fetch_assoc($zoek_data_aantal_geb)) { 
        $totat_geb = $zda['aant'];
        $stopdm_geb = $zda['stopdm']; } 

if(!isset($totat_geb)) {
$totat_geb = 0;

$zoek_laatste_afsluitdm_geb = mysqli_query($db,"
SELECT date_format(max(p.dmafsluit),'%d-%m-%Y') stopdm
FROM tblPeriode p
WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 1 and dmafsluit is not null
") or die (mysqli_error($db));
    while ($lag = mysqli_fetch_assoc($zoek_laatste_afsluitdm_geb)) { 
        $stopdm_geb = $lag['stopdm']; } 
}
        if(!isset($stopdm_geb)) { $stopdm_geb = $startdm; }
// Einde Doelgroep geboren
// Doelgroep gespeend
$zoek_data_aantal_spn = mysqli_query($db,"
SELECT count(st.schaapId) aant, date_format(endspn.dmstop,'%d-%m-%Y') stopdm
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId) 
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
   FROM tblBezet b
    join tblHistorie h1 on (b.hisId = h1.hisId)
    join tblActie a1 on (a1.actId = h1.actId)
    join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
    join tblActie a2 on (a2.actId = h2.actId)
    join tblStal st on (h1.stalId = st.stalId)
   WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
   GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT p.hokId, max(p.dmafsluit) dmstop
    FROM tblPeriode p
    WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 2 and dmafsluit is not null
    GROUP BY p.hokId
 ) endspn on (endspn.hokId = b.hokId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."'
 and (isnull(ht.hisId) or ht.datum > coalesce(dmstop,'1973-09-11'))
 and h.datum >= spn.datum
 and (isnull(prnt.schaapId) or h.datum < prnt.datum) and h.skip = 0
GROUP BY endspn.dmstop
") or die (mysqli_error($db));
    while ($zda = mysqli_fetch_assoc($zoek_data_aantal_spn)) { 
        $totat_spn = $zda['aant'];
        $stopdm_spn = $zda['stopdm']; } 

if(!isset($totat_spn)) {
$totat_spn = 0;

$zoek_laatste_afsluitdm_spn = mysqli_query($db,"
SELECT date_format(max(p.dmafsluit),'%d-%m-%Y') stopdm
FROM tblPeriode p
WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 2 and dmafsluit is not null
") or die (mysqli_error($db));
    while ($las = mysqli_fetch_assoc($zoek_laatste_afsluitdm_spn)) { 
        $stopdm_spn = $las['stopdm']; } 
}
        if(!isset($stopdm_spn)) { $stopdm_spn = $startdm; }
// EindeDoelgroep gespeend
// Doelgroep volwassen
$zoek_data_aantal_prnt = mysqli_query($db,"
SELECT count(distinct(st.schaapId)) aant, date_format(endprnt.dmstop,'%d-%m-%Y') stopdm
FROM tblBezet b
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
   FROM tblBezet b
    join tblHistorie h1 on (b.hisId = h1.hisId)
    join tblActie a1 on (a1.actId = h1.actId)
    join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
    join tblActie a2 on (a2.actId = h2.actId)
    join tblStal st on (h1.stalId = st.stalId)
   WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
   GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT p.hokId, max(p.dmafsluit) dmstop
    FROM tblPeriode p
    WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 3 and dmafsluit is not null
    GROUP BY p.hokId
 ) endprnt on (endprnt.hokId = b.hokId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."'
 and (isnull(ht.hisId) or ht.datum > coalesce(dmstop,'1973-09-11'))
 and h.datum >= prnt.datum and h.skip = 0
GROUP BY endprnt.dmstop
") or die (mysqli_error($db));
    while ($zda = mysqli_fetch_assoc($zoek_data_aantal_prnt)) { 
        $totat_prnt = $zda['aant'];
        $stopdm_prnt = $zda['stopdm']; } 

if(!isset($totat_prnt)) {
$totat_prnt = 0;

$zoek_laatste_afsluitdm_prnt = mysqli_query($db,"
SELECT date_format(max(p.dmafsluit),'%d-%m-%Y') stopdm
FROM tblPeriode p
WHERE p.hokId = '".mysqli_real_escape_string($db,$Id)."' and p.doelId = 3 and dmafsluit is not null
") or die (mysqli_error($db));
    while ($las = mysqli_fetch_assoc($zoek_laatste_afsluitdm_prnt)) { 
        $stopdm_prnt = $las['stopdm']; } 
}
        if(!isset($stopdm_prnt)) { $stopdm_prnt = $startdm; }
// Einde Doelgroep volwassen

// ***** EINDE BEZETTING VANAF LAATSTE PERIODE ***** 

// ***** ACTUELE BEZETTING ***** 
$zoek_nu_in_verblijf_geb = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId)
and isnull(spn.schaapId)
and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu1 = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb))
        { $aanwezig1 = $nu1['aantin']; }

$zoek_nu_in_verblijf_spn = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId)
and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu2 = mysqli_fetch_assoc($zoek_nu_in_verblijf_spn))
        { $aanwezig2 = $nu2['aantin']; }

    $aanwezig_geb_spn = $aanwezig1 + $aanwezig2;

$zoek_nu_in_verblijf_prnt = mysqli_query($db,"
SELECT count(s.schaapId) aantin
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT max(h.hisId) hisId, h.stalId
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblBezet b on (b.hisId = h.hisId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."'
    GROUP BY h.stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie h on (h.hisId = hmax.hisId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join (
    SELECT schaapId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)

WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId) and h.skip = 0
") or die (mysqli_error($db));
        
    while($nu3 = mysqli_fetch_assoc($zoek_nu_in_verblijf_prnt))
        { $aanwezig3 = $nu3['aantin']; }

        $aanwezig_geb_spn_aanw = $aanwezig_geb_spn + $aanwezig3; ?>
<table border = 0> <tr valign="top"><td>
<table border = 0>
<tr>
 <td colspan = 6 style = "font-size : 15px;"> <b style = "font-size : 19px;"><?php echo $hoknr;?> </b> </td>
 <td><a href= '<?php echo $url;?>Bezet_pdf.php?Id=<?php echo $Id; ?>' style = 'color : blue'>print pagina </a></td>
 <td> </td>
</tr>

<?php        
if($aanwezig1 > 0) { ?>

<tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren voor spenen aanwezig :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>
<?php
$hok_inhoud_geb = mysqli_query ($db,"
SELECT u.ubn, s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, right(mdr.levensnummer,$Karwerk) mdr, lastkg.kg lstkg
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId) and isnull(spn.schaapId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Ubn<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Laatst gewogen gewicht (kg)<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Geboortedatum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Datum in verblijf<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Fictieve speendatum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Moeder<hr></th>
</tr>
<?php
        while($row = mysqli_fetch_array($hok_inhoud_geb))
        {
         $ubn = $row['ubn'];
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $vanaf = $row['van'];
         $gebdm = $row['geb'];
         $geslacht = $row['geslacht'];
         $ficdm = $row['ficspn'];
         $lstkg = $row['lstkg'];
?>        
<tr align = center>    
 <td width = 80  style = "font-size:15px;"> <?php echo $ubn;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $lstkg;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $ras;?> <br> </td>       
 <td width = 50  style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>           
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm;?> <br> </td> 
 <td width = 100 style = "font-size:15px;"> <?php echo $vanaf;?> <br> </td>       
 <td width = 100 style = "font-size:15px;"> <?php echo $ficdm;?> <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo"{$row['mdr']}"; ?> <br> </td>
 <td width = 120 style = "font-size:13px;" align = "left" >
    <a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top"> Gegevens wijzigen </a> </td>
</tr>                

        
<?php    }    

 }

if($aanwezig2 > 0) {

    if($aanwezig1 >0) { $height_spn = 50; } else { $height_spn = 35; } /* alleen eerste blok is 35 hoog anders 50*/ ?>

<tr height = <?php echo $height_spn; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren na spenen aanwezig :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig2;?> </b></td>
</tr>
<?php
$hok_inhoud_spn = mysqli_query ($db,"
SELECT u.ubn, s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(spn.datum,'%d-%m-%Y') spn, date_format(h.datum,'%d-%m-%Y') van, date_format(hg.datum + interval 7 week,'%d-%m-%Y') ficspn, date_format(hg.datum + interval 130 day,'%d-%m-%Y') ficafv, right(mdr.levensnummer,$Karwerk) mdr,
    lastkg.kg lstkg
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join tblVolwas v on (v.volwId = s.volwId)
 left join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 left join 
 (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4
 ) spn on (spn.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId) and isnull(prnt.schaapId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Ubn<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Laatst gewogen gewicht (kg)<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > Geboortedatum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > Datum in verblijf<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > Fictieve afleverdatum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 ></th>
 <th width=60></th>
</tr>
<?php
        while($row = mysqli_fetch_array($hok_inhoud_spn))
        {
         $ubn = $row['ubn'];
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $gebdm = $row['geb'];
         $vanaf = $row['van'];
         $ficdm = $row['ficafv'];
         $lstkg = $row['lstkg'];
?>        
<tr align = center>
 <td width = 80  style = "font-size:15px;"> <?php echo $ubn;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $lstkg;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $ras;?> <br> </td>       
 <td width = 50  style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>       
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm;?> <br> </td>              
 <td width = 100 style = "font-size:15px;"> <?php echo $vanaf;?> <br> </td>       
 <td width = 100 style = "font-size:15px;"> <?php echo $ficdm;?> <br> </td>
 <td width = 80  style = "font-size:15px;"> <br> </td>    

       <td width = 180 style = "font-size:13px;" align = "left" >

               <a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top">
            Gegevens wijzigen
            </a>

       </td>
</tr>                

        
<?php    }    } ?>
</tr>                
<!-- Einde gespeende lammeren -->

<?php

if($aanwezig3 > 0) { 

    if($aanwezig1 >0 || $aanwezig2 >0 ) { $height_prnt = 50; } else { $height_prnt = 35; } /* alleen eerste blok is 35 hoog anders 50*/ ?>
<tr height = <?php echo $height_prnt; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal volwassen schapen aanwezig :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig3;?> </b></td>
</tr>
<?php
$hok_inhoud_vanaf_aanwas = mysqli_query ($db,"
SELECT u.ubn, s.schaapId, right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(hg.datum,'%d-%m-%Y') geb, date_format(prnt.datum,'%d-%m-%Y') aanw, date_format(h.datum,'%d-%m-%Y') van, b.hisId,
    lastkg.kg lstkg
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join (
    SELECT max(h.hisId) hisId, h.stalId
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblBezet b on (b.hisId = h.hisId)     
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."'
    GROUP BY h.stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie h on (h.hisId = hmax.hisId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
    GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 join (
    SELECT schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3
 ) prnt on (prnt.schaapId = st.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 1 and h.skip = 0
 ) hg on (hg.schaapId = st.schaapId)
 left join tblRas r on (r.rasId = s.rasId)
 left join (
    SELECT st.schaapId, max(h.hisId) hisId
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.kg is not null
    GROUP BY st.schaapId
 ) hkg on (hkg.schaapId = st.schaapId)
 left join tblHistorie lastkg on (lastkg.hisId = hkg.hisId)

WHERE b.hokId = '".mysqli_real_escape_string($db,$Id)."' and isnull(uit.bezId) and h.skip = 0
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));

?> 
<tr style = "font-size:12px;" height = 48>
 <th style = "text-align:center;" valign=bottom width= 80 > Ubn<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Laatst gewogen gewicht (kg)<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Geboortedatum<hr></th>  
 <th style = "text-align:center;" valign=bottom width= 100 > Datum in verblijf<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> </th>
 <th style = "text-align:center;" valign=bottom width= 80> </th>
 <th width=120></th>
</tr>
<?php
        while($row = mysqli_fetch_array($hok_inhoud_vanaf_aanwas))
        {
         $ubn = $row['ubn'];
         $werknr = $row['werknr'];
         $ras = $row['ras'];
         $geslacht = $row['geslacht'];
         $gebdm = $row['geb'];
         $vanaf = $row['van'];
         $lstkg = $row['lstkg'];
         /*$hisId = $row['hisId'];*/
?>        
<tr align = center>  
 <td width = 80  style = "font-size:15px;"> <?php echo $ubn;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $werknr; ?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $lstkg; ?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>       
 <td width = 50  style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>           
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm;?> <br> </td>          
 <td width = 100 style = "font-size:15px;"> <?php echo $vanaf;?> <br> </td>       
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80  style = "font-size:15px;"> <br> </td>
 <td width = 180 style = "font-size:13px;" align = "left" >

               <a href='<?php echo $url; ?>UpdSchaap.php?pstschaap=<?php echo $row['schaapId']; ?>' style = "color : blue;" valign= "top">
            Gegevens wijzigen
            </a>

       </td>
</tr>                

        
<?php    }    }

if($aanwezig1 == 0 && $aanwezig2 == 0 && $aanwezig3 == 0) { ?>

 <tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal schapen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>

    <tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Geboortedatum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Datum in verblijf<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> Datum<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 120> <hr></th>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>

<?php } // ***** EINDE ACTUELE BEZETTING ***** ?>
</tr>                
</table> </td><td>
<table border = 0>
<tr>
 <td rowspan = 9 width = 100 align = center>
     <?php echo $hoknr; ?><hr>
 <?php if(isset($aanwezig1) && $aanwezig1 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
     <a href='<?php echo $url; ?>HokSpenen.php?pstId=<?php echo $Id; ?>' style = "color : blue">   
    Spenen      
 </a> <?php } else { ?> <u style = "color : grey"> Spenen </u> <?php } ?>
 <br>
 <br>
 <?php if(isset($aanwezig2) && $aanwezig2 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>HokAfleveren.php?pstId=<?php echo $Id; ?>' style = "color : blue">   
    Afleveren     
 </a>  
 <br>
 <br>
 <a href='<?php echo $url; ?>HokAanwas.php?pstId=<?php echo $Id; ?>' style = "color : blue">   
    Aanwas    
 </a> 
 <?php } else { ?> 
     <u style = "color : grey"> Afleveren </u> 
 <br>
 <br>
     <u style = "color : grey"> Aanwas </u> <?php } ?>
 <br>
 <br>
 <?php 
if ($aanwezig_geb_spn_aanw > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>HokOverpl.php?pstId=<?php echo $Id; ?>' style = "color : blue">    
    Overplaatsen
 </a> <?php } else { ?> <u style = "color : grey"> Overplaatsen </u> <?php } ?>
 <br>
 <br>
  <?php 
if ($aanwezig3 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>HokVerkopen.php?pstId=<?php echo $Id; ?>' style = "color : blue">    
    Verkopen
 </a> <?php } else { ?> <u style = "color : grey"> Verkopen </u> <?php } ?>
 <br>
 <br>
  <?php 
if ($aanwezig3 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; ?>
 <a href='<?php echo $url; ?>HokVerlaten.php?pstId=<?php echo $Id; ?>' style = "color : blue">    
    Uit verblijf halen
 </a> <?php } else { ?> <u style = "color : grey"> Uit verblijf halen </u> <?php } ?>
 <br>
 <br>
  <?php 
if ($aanwezig3 > 0) { $_SESSION["DT1"] = NULL; $_SESSION["BST"] = NULL; $_SESSION["Fase"] = NULL ?>
 <a href='<?php echo $url; ?>HokUitscharen.php?pstId=<?php echo $Id; ?>' style = "color : blue">    
    Uitscharen
 </a> <?php } else { ?> <u style = "color : grey"> Uitscharen </u> <?php } ?>
 <br>
 <br>



 </td>
</tr>
</table>
</td>
<td width="25"></td>
<td>
<table border="0">
<tr>
 <td style = "font-size : 12px;">
<?php echo 'Bezetting van '.$hoknr.' in huidige periode <br> incl. verlaten<br><br>';
?> <u><b> <?php echo 'Schapen voor spenen <br>'; ?> </b></u> <?php
echo 'Totaal : '.$totat_geb.' in gezeten sinds start periode : '.$stopdm_geb.'<br><br>';

?> <u><b> <?php echo 'Schapen na spenen <br>'; ?> </b></u> <?php
echo 'Totaal : '.$totat_spn.' in gezeten sinds start periode : '.$stopdm_spn.'<br><br>';

?> <u><b> <?php echo 'Volwassen schapen <br>'; ?> </b></u> <?php
echo 'Totaal : '.$totat_prnt.' in gezeten sinds start periode : '.$stopdm_prnt.'<br><br>';
 ?>
 </td>
</tr>
</table>

</td></tr></table>
    </TD>
<?php include "menu1.php"; } ?>
</tr>

</body>
</html>
