<?php 

require_once("autoload.php");

$versie = '6-2-2019'; /* Vaderdier is tot een jaar terug te kiezen */
$versie = '11-7-2020'; /* Gegevens zijn ook langer dan het laatste half jaar zichtbaar. Als volwId is opgeslagen in tblSchaap kan het record niet meer worden verwijderd. */
$versie = '25-12-2021'; /* Pagina hernoemd van Dracht.php naar Dekkingen.php. */
$versie = '18-11-2023'; /* Dekken per verblijf toegevoegd. querys vervangen door functies uit basisfuncties.php*/
$versie = '30-12-2023'; /* and h.ship = 0 toegevoegd aan tblHistorie en sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '07-09-2024'; /* Periode tussen werpen en dekken teruggebracht naar 60 i.p.v. 183 dagen */
$versie = '16-12-2024'; /* Wijzigen van drachtdaatum met dekdatum ouder dan 1 jaar niet meer mogelijk gemaakt. Voorgaande jaren kan niet ouder zijn dan het jaar dat de gebruiker is gestart met het managementprogramma */
$versie = '18-12-2024'; /* query Declaratie vaderdier aangepast. Aanwas hoeft niet meer zijn aangemaakt bij de ingelogde gebruiker. */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '21-01-2025'; /* In subquery vmax_mdr_met_vdr h.skip = 0 toegevoegd */
$versie = '12-03-2025'; /* In query zoek_dekkingen tabel 'join tblHistorie h on (stm.stalId = h.stalId and v.hisId = h.hisId)' toegevoegd om de relaties met andere stalId's uit tblStal (bijv. uitgeschaarden) uit te sluiten. Dit veroorzaakte nl. dubbel aantal worpen */
$versie = '26-03-2025'; /* Verblijf tijdens dekking toegevoegd aan historie */

session_start(); ?>  
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
    <style type="text/css">
        .selectt {
           /* color: #fff;
            padding: 30px;*/
            display: none;
            /*margin-top: 30px;
            width: 60%;
            background: grey;*/
            font-size: 12px;
        }
    </style>

<?php include "kalender.php"; ?>
</head>
<body>

<?php
$titel = 'Dekkingen / Dracht';
$file = "Dekkingen.php";
include "login.php"; ?>

        <TD valign = "top">
<?php
// wordt in javascript gebruikt, dat buiten de if() wordt ingeclude.
$array_drachtdatum = array();
if (Auth::is_logged_in()) { if($modtech == 1) {

    include "vw_kzlOoien.php";

    include "dekkingen.js.php";

// Declaratie vaderdier
$resultvader = mysqli_query($db,"
SELECT st.schaapId, right(s.levensnummer,$Karwerk) werknr
FROM tblStal st
 join tblSchaap s on (st.schaapId = s.schaapId)
 join (
     SELECT schaapId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) prnt on (s.schaapId = prnt.schaapId)
 join (
     SELECT schaapId, max(stalId) stalId
     FROM tblStal st
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     GROUP BY schaapId
 ) mst on (s.schaapId = mst.schaapId)
 left join (
     SELECT st.stalId
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
      join tblActie a on (h.actId = a.actId)
     WHERE a.af = 1 and h.skip = 0
 ) afv on (afv.stalId = mst.stalId)
WHERE s.geslacht = 'ram' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(afv.stalId)
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db)); 

$index = 0; 
$vawerknr = [];
$vaRaak = [];
while ($va = mysqli_fetch_array($resultvader)) 
{ 
   //$vaId[$index] = $va['schaapId']; 
   $vawerknr[$index] = $va['werknr'];
   $vaRaak[$index] = $va['schaapId'];   
   $index++; 
} 
unset($index);
// EINDE Declaratie vaderdier


    /****************************************
            NIEUWE INVOER PER DIER
    *****************************************/
    if (isset($_POST['knpInsert1_']))
{
    if(!empty($_POST['txtDatum1_'])) {
        $dag1 = $_POST['txtDatum1_']; 
        //if(empty($dag1)) { $dag1 = date('d-m-Y'); } #echo 'Datum :'.$dag1.'<br>'; 
        $date = date_create($dag1);
        $txtDay =  date_format($date, 'Y-m-d');   #echo 'Datum database : '.$txtDay.'<br>';
    }
    if(!empty($_POST['kzlWat_'])) { $registratie = $_POST['kzlWat_']; }
    if(!empty($_POST['kzlOoi_'])) { $kzlMdr = $_POST['kzlOoi_']; } #echo 'Moeder : '.$kzlMdr.'<br>';
    if(!empty($_POST['kzlRamNew1_'])) { $kzlVdr = $_POST['kzlRamNew1_']; } #echo 'Vader : '.$kzlVdr.'<br>';
    if(isset($_POST['txtWorp_'])) { $txtGrootte = $_POST['txtWorp_']; }  #echo 'Dracht : '.$dracht.'<br><br>';




if (isset($txtDay) && isset($registratie) && isset($kzlMdr)) {

$stalId = zoek_max_stalId($lidId,$kzlMdr);

// Controle op dubbele invoer achter elkaar en dekking binnen 60 dagen

$zoek_60dagen_na_laatste_worp = mysqli_query($db,"
SELECT date_add(max(h.datum),interval 60 day) datum
FROM tblVolwas v
 join tblSchaap lam on (lam.volwId = v.volwId)
 join tblStal st on (st.schaapId = lam.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE mdrId = '".mysqli_real_escape_string($db,$kzlMdr)."' and h.actId = 1 and h.skip = 0
") or die (mysqli_error($db));

    while ( $vw = mysqli_fetch_assoc ($zoek_60dagen_na_laatste_worp)) { $vroegst_volgende_dekdatum = $vw['datum']; } // Datum dat moeder weer drachtig kan zijn

$zoek_laatste_koppel_na_laatste_worp_obv_moeder = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 left join tblHistorie dek on (dek.hisId = v.hisId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
WHERE (isnull(dek.skip) or dek.skip = 0) and isnull(lam.volwId) and v.mdrId = '".mysqli_real_escape_string($db,$kzlMdr)."'
") or die (mysqli_error($db));

    while ( $lk = mysqli_fetch_assoc ($zoek_laatste_koppel_na_laatste_worp_obv_moeder)) { $koppel = $lk['volwId']; } //Laatste_koppel_zonder_worp

$zoek_moeder_vader_uit_laatste_koppel = mysqli_query($db,"
SELECT mdrId, vdrId, v.hisId his_dek, d.hisId his_dracht
FROM tblVolwas v
 left join tblDracht d on (d.volwId = v.volwId) 
 left join tblHistorie hd on (hd.hisId = d.hisId)
WHERE (isnull(hd.skip) or hd.skip = 0) and v.volwId = '".mysqli_real_escape_string($db,$koppel)."'
") or die (mysqli_error($db));

    while ( $v_m = mysqli_fetch_assoc ($zoek_moeder_vader_uit_laatste_koppel)) { 
        $lst_mdr = $v_m['mdrId']; 
        $lst_vdr = $v_m['vdrId']; 
        $dekMoment = $v_m['his_dek']; 
        $drachtMoment = $v_m['his_dracht']; }

/*echo 'laatste koppel = '.$koppel.'<br>';
echo '$lst_mdr = '.$lst_mdr.' keuze mdr = '.$kzlMdr.'<br>' ;
echo '$lst_vdr = '.$lst_vdr.' keuze vdr = '.$kzlVdr.'<br>' ;*/


if($lst_mdr == $kzlMdr) {
if(isset($dekMoment) && $lst_vdr == $kzlVdr && isset($kzlVdr)) {

$zoek_dekdatum = mysqli_query($db,"
SELECT date_format(datum,'%d-%m-%Y') datum, year(datum) jaar
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($db,$dekMoment)."' and skip = 0
") or die (mysqli_error($db));
    while ( $zd = mysqli_fetch_assoc($zoek_dekdatum)) { $dekdm = $zd['datum']; $dekjaar = $zd['jaar']; }

    $fout = "Deze ram heeft deze ooi reeds als laatste gedekt en wel op ".$dekdm.". ";

    if($registratie == 'dracht') { $fout .= " Wijzig de dekking uit ".$dekjaar."."; }
}
if(isset($drachtMoment)) {

$zoek_drachtdatum = mysqli_query($db,"
SELECT date_format(datum,'%d-%m-%Y') datum
FROM tblHistorie
WHERE hisId = '".mysqli_real_escape_string($db,$drachtMoment)."' and skip = 0
") or die (mysqli_error($db));
    while ( $zd = mysqli_fetch_assoc($zoek_drachtdatum)) { $drachtdm = $zd['datum']; }

    $fout = "Deze ooi is reeds drachtig per ".$drachtdm.". ";
}


}

else if(isset($vroegst_volgende_dekdatum) && $vroegst_volgende_dekdatum > $txtDay) { $fout = "Deze ooi is heeft binnen de 2 maanden nog geworpen. "; }
// Einde Controle op dubbele invoer achter elkaar en dekking binnen 60 dagen

if(!isset($fout) && $registratie == 'dekking') {

insert_tblHistorie($stalId,$txtDay,18);

$hisId = zoek_max_hisId_stal($stalId,18);

insert_dekking_mdr($hisId,$kzlMdr,$kzlVdr);

      }

else if(!isset($fout) && $registratie == 'dracht') {

insert_dracht_mdr($kzlMdr,$kzlVdr,$txtGrootte);

$volwId = zoek_max_volwId_mdr($kzlMdr,$kzlVdr);

insert_tblHistorie($stalId,$txtDay,19);

$hisId = zoek_max_hisId_stal($stalId,19);

$insert_tblDracht = "INSERT INTO tblDracht SET volwId = '".mysqli_real_escape_string($db,$volwId)."', hisId = '".mysqli_real_escape_string($db,$hisId)."' ";    
/*echo $insert_tblDracht.'<br>';*/        mysqli_query($db,$insert_tblDracht) or die (mysqli_error($db));

      }

            } // Einde if (isset($txtDay) && isset($registratie) && isset($kzlMdr))

            else if(!isset($txtDay))         { $fout = "De datum is onbekend."; }
            else if(!isset($registratie))    { $fout = "Soort registratie is onbekend."; }
            else if(!isset($kzlMdr))            { $fout = "Moederdier is onbekend."; }

} // Einde if (isset($_POST['knpInsert1_']))
    /****************************************
        EINDE NIEUWE INVOER PER DIER
    *****************************************/


    /****************************************
            NIEUWE INVOER O.B.V. VERBLIJF
    *****************************************/
    if (isset($_POST['knpInsert2_']))
{
    if(!empty($_POST['txtDatum2_'])) {
        $dag2 = $_POST['txtDatum2_']; 
        //if(empty($dag2)) { $dag2 = date('d-m-Y'); } #echo 'Datum :'.$dag2.'<br>'; 
        $date = date_create($dag2);
        $txtDay =  date_format($date, 'Y-m-d');   #echo 'Datum database : '.$txtDay.'<br>';
    }
    $registratie = 'dekking';
    if(!empty($_POST['kzlHok_'])) { $kzlHok = $_POST['kzlHok_']; } #echo '$kzlHok : '.$kzlHok.'<br>';
    if(!empty($_POST['kzlRamNew2_'])) { $kzlVdr = $_POST['kzlRamNew2_']; } #echo 'Vader : '.$kzlVdr.'<br>';


if (isset($txtDay) && isset($registratie) && isset($kzlHok) && isset($kzlVdr)) {

//Controle of vaderdier dit verblijf als laatste reeds heeft gedekt. Zie toelichting onder deze eerste query

$aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader = mysqli_query($db,"
SELECT count(mdrs.mdrId) aant, datediff('".mysqli_real_escape_string($db,$txtDay)."', h.datum) verschil
FROM (
    SELECT st.schaapId mdrId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi' and b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and isnull(uit.bezId) and h.skip = 0
 ) mdrs
 join (
    SELECT v.mdrId, max(v.volwId) mxvolwId
    FROM tblVolwas v
     join tblHistorie h on (v.hisId = h.hisId)
    WHERE v.vdrId = '". mysqli_real_escape_string($db,$kzlVdr) ."' and h.skip = 0
    GROUP BY v.mdrId 
 ) vmax_mdr_met_vdr on (mdrs.mdrId = vmax_mdr_met_vdr.mdrId)
 join (
    SELECT mdrId, max(volwId) mxvolwId
    FROM tblVolwas
    GROUP BY mdrId 
 ) vmax_mdr on (vmax_mdr_met_vdr.mxvolwId = vmax_mdr.mxvolwId)
 join tblVolwas v on (vmax_mdr.mxvolwId = v.volwId)
 join tblHistorie h on (h.hisId = v.hisId)
 GROUP BY h.datum

") or die (mysqli_error($db));

    while ( $ald = mysqli_fetch_assoc($aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader)) { $aant_mdrs = $ald['aant'];  // aantal moeders uit verblijf waarvan laatste dekking van gekozen vaderdier is
        $dgn_verschil = $ald['verschil']; } // aantal dagen tussen laatste dekking met dit vaderdier en de huidig gekozen datum

/*
echo '$aant_mdrs = '. $aant_mdrs.'<br>';
echo '$dgn_verschil = '. $dgn_verschil.'<br>';*/
        
/* getest met Freija verblijf 1.2 :
Dekdatum        Ooi            Ram
12-11-2023        10643        38944
11-11-2023        10643        43524
10-11-2023        10643        38944
10-11-2023        11702        38944
10-11-2023        11716        38944
10-11-2023        11718        38944
10-11-2023        11720        38944
10-11-2023        11722        38944
10-11-2023        11741        38944
12-11-2023        45241        43524

Laatste dekdatum van ooi 10643 en ram 38944 is hier 12-11-2023 terwijl het meer en deel van de ooien op 10-11-2023 is gedekt met ram 38944 binnen verblijf 1.2

De gekozen dekdatum mag niet voor de laatste dekdatum liggen. Bij hoerveel dieren dit het geval is in het verblijf doet er niet toe. Vandaar geen HAVING (count(mdrs.mdrId)) >= 5 in de query hierboven. 

Zijn de ooien binnen het verblijf als laatst gedekt door het gekozen vaderdier dan moeten er minimaal 5 moeders een koppels zijn met het gekozen vaderdier om te beoordelen dat dit verblijf de laatste keer reeds is gedekt door het gekozen vaderdier. Vandaar HAVING (count(mdrs.mdrId)) >= 5 in de query hieronder.
De dekdatum van de eerste registratie mag niet binnen de 21 dagen liggen met de gekozen dekdatum van de tweede registratie
*/
if($dgn_verschil < 0) { $fout = 'De dekdatum mag niet voor de laatste dekking met dit vaderdier liggen. Dit geldt voor tenminste 1 moederdier uit dit verblijf.'; }
else {

$aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader = mysqli_query($db,"
SELECT count(mdrs.mdrId) aant, datediff('".mysqli_real_escape_string($db,$txtDay)."', h.datum) verschil
FROM (
    SELECT st.schaapId mdrId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi' and b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and isnull(uit.bezId) and h.skip = 0
 ) mdrs
 join (
    SELECT v.mdrId, max(v.volwId) mxvolwId
    FROM tblVolwas v
     join tblHistorie h on (v.hisId = h.hisId)
    WHERE v.vdrId = '". mysqli_real_escape_string($db,$kzlVdr) ."' and h.skip = 0
    GROUP BY v.mdrId
 ) vmax_mdr_met_vdr on (mdrs.mdrId = vmax_mdr_met_vdr.mdrId)
 join (
    SELECT mdrId, max(volwId) mxvolwId
    FROM tblVolwas
    GROUP BY mdrId 
 ) vmax_mdr on (vmax_mdr_met_vdr.mxvolwId = vmax_mdr.mxvolwId)
 join tblVolwas v on (vmax_mdr.mxvolwId = v.volwId)
 join tblHistorie h on (h.hisId = v.hisId)
 GROUP BY h.datum
 HAVING (count(mdrs.mdrId)) >= 5

") or die (mysqli_error($db));

    while ( $ald = mysqli_fetch_assoc($aantal_laatste_dekkingen_van_moeders_uit_gekozen_verblijf_met_laatste_dekkingen_met_gekozen_vader)) { $aant_mdrs = $ald['aant'];  // aantal moeders uit verblijf waarvan laatste dekking van gekozen vaderdier is
        $dgn_verschil = $ald['verschil']; } // aantal dagen tussen laatste dekking met dit vaderdier en de huidig gekozen datum


if($aant_mdrs >= 5 && $dgn_verschil <= 21) { $fout = 'Controle dubbele registratie: \nMinimaal 5 moederdieren uit dit verblijf zijn al als laatst gedekt door dit vaderdier binnen 21 dagen. \nEr wordt daarom niets ingelezen. '; }
}
// Einde Controle of vaderdier dit verblijf als laatste reeds heeft gedekt. 

if(!isset($fout)) {
$foutaantal = 0;

$zoek_moeders_in_verblijf = mysqli_query($db,"
SELECT st.schaapId mdrId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
     left join 
     (
        SELECT b.bezId, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
        WHERE b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY b.bezId
     ) uit on (uit.bezId = b.bezId)
     join (
        SELECT st.schaapId
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = st.schaapId)
    WHERE s.geslacht = 'ooi' and b.hokId = '".mysqli_real_escape_string($db,$kzlHok)."' and isnull(uit.bezId) and h.skip = 0
") or die (mysqli_error($db));

$num_rows = mysqli_num_rows($zoek_moeders_in_verblijf);

if($num_rows == 0) { $fout = 'Dit verblijf heeft geen moederdieren.'; }
else {

    while ( $zmv = mysqli_fetch_assoc($zoek_moeders_in_verblijf)) { $mdrId = $zmv['mdrId']; 



unset($max_worp);
unset($dmwerp_plus_60dgn);
// Controle dekking binnen 60 dagen na laatste worp per moeder
$zoek_laatste_worp_moeder = mysqli_query($db,"
SELECT max(v.volwId) max_worp
FROM tblVolwas v
 join tblSchaap s on (v.volwId = s.volwId)
WHERE v.mdrId = '" . mysqli_real_escape_string($db,$mdrId) . "'
") or die (mysqli_error($db));
    while ($zlw = mysqli_fetch_assoc($zoek_laatste_worp_moeder)) { $max_worp = $zlw['max_worp']; }


/*
1323 1 stuks
1326 5 stuks
1333 9 stuks
*/

if(isset($max_worp)) {
$zoek_laatste_werpdatum = mysqli_query($db,"
SELECT date_add(max(h.datum),interval 60 day) werpdate
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 1 and h.skip = 0 and s.volwId = '" . mysqli_real_escape_string($db,$max_worp) . "'
") or die (mysqli_error($db));
    while ($zlw = mysqli_fetch_assoc($zoek_laatste_werpdatum)) { $dmwerp_plus_60dgn = $zlw['werpdate']; }

if($dmwerp_plus_60dgn > $txtDay) { 

$zoek_werknr_ooi = mysqli_query($db,"
SELECT right(levensnummer, $Karwerk) werknr
FROM tblSchaap
WHERE schaapId = '" . mysqli_real_escape_string($db,$mdrId) . "'
") or die (mysqli_error($db));

    while ($zw = mysqli_fetch_assoc($zoek_werknr_ooi)) { $werknr_ooi = $zw['werknr']; }

    $foutaantal ++;

    if($foutaantal > 5) { 
    $fout = $foutaantal." ooien hebben binnen de 2 maanden nog geworpen. ".'\n'."Deze ".$foutaantal." dieren zijn daarom niet gedekt.";
    }
    else {
    $fout .= 'De ooi ' .$werknr_ooi. ' heeft binnen de 2 maanden nog geworpen en is daarom niet gedekt. \n'; 
    }

}


} // Einde if(isset($max_worp))
// Einde Controle dekking binnen 60 dagen na laatste worp per moeder

if(!isset($max_worp) || (isset($dmwerp_plus_60dgn) && $dmwerp_plus_60dgn <= $txtDay)) {
// Inlezen dekking door kzlVdr bij ooien in verblijf kzlHok


$stalId = zoek_stalId_in_stallijst($lidId,$mdrId);

insert_tblHistorie($stalId,$txtDay,18);

$hisId = zoek_max_hisId_stal($stalId,18);

insert_dekking_mdr($hisId,$mdrId,$kzlVdr);

// Einde Inlezen dekking door kzlVdr bij ooien in verblijf kzlHok
}

} // Einde while ( $zmv = mysqli_fetch_assoc($zoek_moeders_in_verblijf))

} // Else van if($num_rows == 0)

} // Einde if(!isset($fout))

            } // Einde if (isset($txtDay) && isset($registratie) && isset($kzlHok) && isset($kzlVdr))

            else if(!isset($txtDay))             { $fout = "De datum is onbekend."; }
            else if(!isset($kzlHok))            { $fout = "Verblijf is onbekend."; }
            else if(!isset($kzlVdr))            { $fout = "Ram is onbekend."; }


} // Einde if (isset($_POST['knpInsert2_']))

    /****************************************
        EINDE NIEUWE INVOER O.B.V. VERBLIJF
    *****************************************/

if(isset($_POST['knpSave_'])) { include "save_dekkingen.php"; }
?>    
<form action = "Dekkingen.php" method = "post" >

<table border= 0>
<!--*********************************
         NIEUWE INVOER VELDEN
    ********************************* -->
<tr>
 <td align="center" valign="bottom"><b> Invoer per schaap </b> </td>
 <td width="20"></td>
 <td align="center" valign="bottom"><b> Invoer per verblijf </b> </td>
</tr>

<tr>
    <td> <!-- INVOER PER DIER -->
<table border= 0 >
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe dekking / dracht : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom"> 
 <td width="100">Datum<hr></hr></td>
 <td align="center" width="100">Registratie<hr></hr></td>
 <td align="center" width="100">Ooi<hr></hr></td> <!--<td style = "font-size:10px;"><i> Werknr - lammeren - halsnr </i>
 </td> -->
 <td align="center" width="100">Ram<hr></hr></td>
 <td align="center" width="100">Worpgrootte<hr></hr></td>
</tr>
<tr>
 <td align="center"><input type="text" id="datepicker1" name="txtDatum1_" size = 8 value = <?php if(isset($dag1)) { echo $dag1; } else { echo date('d-m-Y'); } ?> >
 </td>
 <td align="center">
<select name= "kzlWat_" style= "width:80;" > 
<?php
$opties = array('' => '', 'dekking' => 'Dekking', 'dracht' => 'Dracht');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST['kzlWat_']) && $_POST['kzlWat_'] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
 </select>
 </td>
 <td align="center"> 
<?php
$result = mysqli_query($db,"(".$vw_kzlOoien.")  ") or die (mysqli_error($db)); ?>
     <select name= "kzlOoi_" style= "width:65;" >
 <option></option>    
<?php    while($row = mysqli_fetch_array($result))
        {
            $opties= array($row['schaapId']=>$row['werknr'].'&nbsp &nbsp '.$row['lamrn'].'&nbsp &nbsp '.$row['halsnr']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlOoi_']) && $_POST['kzlOoi_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>
 </td>
<td align="center"> 
 <select name= "kzlRamNew1_" style= "width:65;" >
 <option></option>    
<?php    $count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
        
    $opties= array($vaRaak[$i]=>$vawerknr[$i]);
            foreach ( $opties as $key => $waarde)
            {
    if((isset($_POST['kzlRamNew1_']) && $_POST['kzlRamNew1_'] == $key)) {
        echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
        else
        {
            echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            }
        
        } ?>
 </select>
 </td>

 <td align="center"><input type = "text" size = 1 name = "txtWorp_" style = "font-size:10px;" value = <?php echo $txtGrootte; ?> >
 </td>
 <td colspan = 2><input type = "submit" name = "knpInsert1_" value = "Toevoegen" style = "font-size:10px;">
 </td>
</tr>

<tr><td colspan = 15><hr>

 </td>

</tr>
</table>

    </td>  <!-- Einde INVOER PER DIER -->
<td width="20"></td>
    <td> <!-- INVOER PER VERBLIJF -->
<table border= 0 >
<tr><td colspan = 3 style = "font-size:13px;"><i> Nieuwe dekking : </i></td></tr>
<tr style =  "font-size:12px;" valign =  "bottom"> 
 <td width="100">Datum<hr></hr></td>
 <td align="center" width="70">Registratie<hr></hr></td>
 <td align="center" width="100">Verblijf<hr></hr></td> <!--<td style = "font-size:10px;"><i> Werknr - lammeren - halsnr </i>
 </td> -->
 <td align="center" width="100">Ram<hr></hr></td>

</tr>
<tr>
 <td align="center"><input type="text" id="datepicker2" name="txtDatum2_" size = 8 value = <?php if(isset($dag2)) { echo $dag2; } else { echo date('d-m-Y'); } ?> >
 </td>
 <td align="center"> Dekking
 </td>
 <td align="center"> 
<?php
$result = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok
WHERE actief = 1 and lidId = '" . mysqli_real_escape_string($db,$lidId) . "' 
ORDER BY hoknr 
") or die (mysqli_error($db)); ?>
     <select name= "kzlHok_" style= "width:65;" >
 <option></option>    
<?php    while($row = mysqli_fetch_array($result))
        {
            $opties= array($row['hokId']=>$row['hoknr']);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_POST['kzlHok_']) && $_POST['kzlHok_'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        } ?>
 </select>
 </td>
<td align="center"> 
 <select name= "kzlRamNew2_" style= "width:65;" >
 <option></option>    
<?php    $count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
        
    $opties= array($vaRaak[$i]=>$vawerknr[$i]);
            foreach ( $opties as $key => $waarde)
            {
    if((isset($_POST['kzlRamNew2_']) && $_POST['kzlRamNew2_'] == $key)) {
        echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
        else
        {
            echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            }
        
        } ?>
 </select>
 </td>
 <td colspan = 2><input type = "submit" name = "knpInsert2_" value = "Toevoegen" style = "font-size:10px;">
 </td>
</tr>

<tr><td colspan = 15><hr>

 </td>

</tr>
</table>

    </td>  <!-- Einde INVOER PER VERBLIJF -->
</tr>
<!--*********************************
        EINDE NIEUWE INVOER VELDEN
    ********************************* -->

<?php 
if(isset($_POST['txtJaar_'])) { $hisJaar = $_POST['txtJaar_']; } 
else { $hisJaar = 2; } ?>

<tr><td align="right">
<!--*****************************
             WIJZIGEN DEKKINGEN
    ***************************** -->
 <table border= 0>
 <tr height = 17 valign="bottom"> 
 </tr>
 <tr>
  <td colspan = 2 > <b>Dekkingen :</b> 
  <td colspan = 6 align="right"> Toon laatste
      <input type="text" name="txtJaar_" size="1" style = "font-size:9px; text-align : center;" value = <?php echo $hisJaar; ?> >
   jaar
  </td>
  <td align="right" rowspan="2"> 
      <input type="submit" name="knpVervers_" value="Ververs" style = "font-size:9px;">
  </td>
 </tr>
 <tr>
  <td colspan = 8 align="right" style="font-size: 13px;">
      Eerdere dekkingen zonder worp tonen 
      <input type="radio" name="radAllDekkingen" value= 1 <?php if(isset($_POST['radAllDekkingen']) && $_POST['radAllDekkingen'] == 1) { echo "checked"; } ?> > Ja
      <input type="radio" name="radAllDekkingen" value= 0 <?php if(!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == 0) { echo "checked"; } ?>  > Nee
  </td>
</tr>
<tr>
 <td colspan = 16 align="right" ><input type = "submit" name = "knpSave_" value = "Opslaan" style = "font-size:14px" >
 </td>
</tr>



<?php        
$current_year = date("Y");
$first_year = date("Y")-$hisJaar+1;

$startjaar_gebruiker = startjaar_gebruiker($lidId); //Het jaartal dat de gebruiker is gestart
$een_startjaar_eerder_gebruiker = startjaar_gebruiker($lidId)-1; // 1 jaar eerder t.o.v. het jaartal dat de gebruiker is gestart
$start_datum_historie = date_add_months($today,-12); /* Oude dekkingen zijn tot een jaar terug te wijzigen */

$array_drachtdatum = array();

// Historie jaren mogen niet verder in het verleden liggen dan het eerst dek- of drachtjaar. Het getoonde jaar moet dus altijd recenter of gelijk zijn aan het eerst dek- of drachtjaar
$zoek_jaartal_eerste_dekking_dracht = mysqli_query($db,"
SELECT year(min(h.datum)) jaar
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE (actId = 18 or actId = 19) and skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(h.datum) >= '".mysqli_real_escape_string($db,$een_startjaar_eerder_gebruiker)."'
") or die (mysqli_error($db));

    while($zj = mysqli_fetch_assoc($zoek_jaartal_eerste_dekking_dracht)) { $first_year_db = $zj['jaar']; }


if(!isset($first_year_db)) { $first_year = $current_year; }    // Als er geen dekking of dracht bestaat
else if($first_year < $first_year_db) { $first_year = $first_year_db; } 



for($jaar = $current_year; $jaar >= $first_year; $jaar--) { ?>

<tr>
 <td colspan="9">

 <input type="checkbox" name="jaartalCheckbox" value= <?php echo $jaar; if($jaar == $current_year) { ?> checked <?php } ?> > <?php echo $jaar; ?>
 </td>
 <td class= "<?php echo $jaar; ?> selectt" >
 </td>
</tr>
 <tr style =  "font-size:12px;" valign =  "bottom" class= "<?php echo $jaar; ?> selectt" > 
     <th></th>
     <th>Verwijder<hr></th>
     <th>Dekdatum<hr></th>
     <th></th>
     <th>Ooi<hr></th>
     <th></th>
     <th>Ram<hr></th>
     <th></th>
     <th>Drachtig<hr></th>
     <th>Drachtdatum<hr></th>
     <th>Worpgrootte<hr></th>
     <th>Werpdatum<hr></th>
     <th>Verblijf<hr></th>
 </tr> 

<?php
if(!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == '0') { $alle_dekkingen = 'Nee'; } else { $alle_dekkingen = 'Ja'; }

$zoek_dekkingen = "
SELECT v.volwId, v.hisId, dekdate, dekdatum, v.mdrId, right(mdr.levensnummer,$Karwerk) mdr, v.vdrId, count(lam.schaapId) lamrn, drachtdatum, v.grootte, werpdatum,
lst_volwId
FROM tblVolwas v
 join tblSchaap mdr on (v.mdrId = mdr.schaapId)
 join tblStal stm on (stm.schaapId = mdr.schaapId)
 join tblHistorie h on (stm.stalId = h.stalId and v.hisId = h.hisId)
 left join (
     SELECT hisId, h.datum dekdate, date_format(h.datum,'%d-%m-%Y') dekdatum, year(h.datum) dekjaar, skip
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE actId = 18 and skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) dek on (v.hisId = dek.hisId)
 left join tblSchaap vdr on (v.vdrId = vdr.schaapId)
 left join (
    SELECT d.volwId, date_format(h.datum,'%d-%m-%Y') drachtdatum, year(h.datum) drachtjaar
     FROM tblDracht d 
     join tblHistorie h on (h.hisId = d.hisId)
     join tblStal st on (st.stalId = h.stalId)
    WHERE actId = 19 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) dra on (dra.volwId = v.volwId)
 left join tblSchaap lam on (lam.volwId = v.volwId)
 left join tblStal stl on (stl.schaapId = lam.schaapId)
 left join (
     SELECT stalId, date_format(datum,'%d-%m-%Y') werpdatum, year(date_add(datum,interval -145 day)) dekjaar_obv_worp
     FROM tblHistorie
     WHERE actId = 1 and skip = 0
 ) hl on (stl.stalId = hl.stalId)
 join (
    SELECT v.mdrId, max(v.volwId) lst_volwId
   FROM tblVolwas v
    left join (
       SELECT hisId
      FROM tblHistorie
      WHERE actId = 18 and skip = 0
    ) dek on (v.hisId = dek.hisId)
    left join ( 
       SELECT volwId
      FROM tblDracht d
       join tblHistorie hd on (hd.hisId = d.hisId)
      WHERE skip = 0
    ) dra on (dra.volwId = v.volwId)
    left join tblSchaap k on (k.volwId = v.volwId)
    left join (
       SELECT s.schaapId
      FROM tblSchaap s
       join tblStal st on (s.schaapId = st.schaapId)
       join tblHistorie h on (st.stalId = h.stalId)
       WHERE h.actId = 3 and h.skip = 0
    ) ha on (k.schaapId = ha.schaapId)
    WHERE (dek.hisId is not null or dra.volwId is not null) and isnull(ha.schaapId)
    GROUP BY mdrId
 ) lst_v on (lst_v.mdrId = v.mdrId)
WHERE stm.lidId = '".mysqli_real_escape_string($db,$lidId)."' and (isnull(stl.lidId) or stl.lidId = '".mysqli_real_escape_string($db,$lidId)."') and (dekdatum is not null or drachtdatum is not null) and coalesce(dekjaar, dekjaar_obv_worp, drachtjaar) = '".mysqli_real_escape_string($db,$jaar)."' and isnull(stm.rel_best)
GROUP BY v.volwId, v.hisId, dekdatum, v.mdrId, mdr.levensnummer, v.vdrId, drachtdatum, werpdatum, v.grootte
ORDER BY right(mdr.levensnummer,$Karwerk), dekdate desc
";

$zoek_dekkingen1 = mysqli_query($db,$zoek_dekkingen) or die (mysqli_error($db));
$zoek_dekkingen2 = mysqli_query($db,$zoek_dekkingen) or die (mysqli_error($db));

/**********************************
 **     DUBBELE DEKKING ZOEKEN        **
 **********************************/ 
$array_dub = array();


if(isset($zoek_dekkingen1))  { foreach($zoek_dekkingen1 as $key => $array)
    {
    
    $worpgrootte = $array['lamrn'];

    if($worpgrootte > 0) { // Vul de array alleen als er een worp bestaat
    $array_dub[] = $array['mdrId']; // schaapId van moeder
    }
}
}



/*$array = array(12,43,66,21,56,43,43,78,78,100,43,43,43,21);*/
$vals = array_count_values($array_dub);
//echo 'No. of NON Duplicate Items: '.count($vals).'<br><br>';
/*print_r($vals);*/

/****************************************
 **     EINDE DUBBELE DEKKING ZOEKEN        **
 ****************************************/ 

    while($zd = mysqli_fetch_assoc($zoek_dekkingen2))
    {
        $Id = $zd['volwId'];
        $hisId = $zd['hisId'];
        $dmdek = $zd['dekdate']; if($dmdek < $start_datum_historie) { $oude_registratie ='Ja'; } else { $oude_registratie ='Nee'; } /* Oude dekkingen zijn tot een jaar terug te wijzigen */
        $dekdm = $zd['dekdatum'];
        $mdrId = $zd['mdrId'];
        $moeder = $zd['mdr'];
        $vaderId = $zd['vdrId'];
        $lamrn = $zd['lamrn']; if($lamrn == 0) { unset($lamrn); }
        $drachtdm = $zd['drachtdatum'];
        $werpdm = $zd['werpdatum'];
        $grootte = $zd['grootte'];
        $lst_volwId = $zd['lst_volwId'];

        if(isset($drachtdm) || isset($lamrn) || $_POST["kzlDrachtUpd_$Id"] == 'ja') { $drachtig = 'ja'; } else { $drachtig = 'nee'; }

        if($Id <> $lst_volwId && !isset($lamrn)) { $color = 'grey'; $fontsize = '12px'; } 
        else if($Id <> $lst_volwId ) { $fontsize = '12px'; } 
        else { $fontsize = '14px'; }

$cnt_ooien = $vals[$mdrId];
        if($cnt_ooien > 1) { $color = 'blue'; }

    $txtGrootte = $grootte;

/*$cnt_ooien = $vals[$mdrId];
echo $cnt_ooien.'<br>';*/

/* Zoek het verblijf tijdens het dekken */
unset($verblijf);

$zoek_datum_verblijf_tijdens_dekking = mysqli_query($db,"
SELECT max(h.datum) datum
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$mdrId)."' and h.datum <= '".mysqli_real_escape_string($db,$dmdek)."'
") or die (mysqli_error($db)); 

while ($zdvtd = mysqli_fetch_array($zoek_datum_verblijf_tijdens_dekking)) 
{ $date_verblijf = $zdvtd['datum']; }


$zoek_hisId_verblijf_tijdens_dekking = mysqli_query($db,"
SELECT max(h.hisId) hisId
FROM tblHistorie h
 join tblBezet b on (h.hisId = b.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and st.schaapId = '".mysqli_real_escape_string($db,$mdrId)."' and h.datum = '".mysqli_real_escape_string($db,$date_verblijf)."'
") or die (mysqli_error($db)); 

while ($zhvtd = mysqli_fetch_array($zoek_hisId_verblijf_tijdens_dekking)) 
{ $hisId_verblijf = $zhvtd['hisId']; }


$zoek_verblijf_tijdens_dekking = mysqli_query($db,"
SELECT ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 left join
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (b.hisId = uit.hisv)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE b.hisId = '".mysqli_real_escape_string($db,$hisId_verblijf)."' and (isnull(uit.bezId) or ht.datum > '".mysqli_real_escape_string($db,$dmdek)."')
") or die (mysqli_error($db)); 

while ($zvtd = mysqli_fetch_array($zoek_verblijf_tijdens_dekking)) 
{ $verblijf = $zvtd['hoknr']; }

/* Einde Zoek het verblijf tijdens het dekken */


if($Id <> $lst_volwId && !isset($lamrn) && (!isset($_POST['radAllDekkingen']) || $_POST['radAllDekkingen'] == '0') )
/* Eerdere dekkingen en dekkingen zonder worp en keuze Eerdere dekkingen niet tonen */
 { $regels_tonen = 'Nee'; } else { $regels_tonen = 'Ja'; } 

if($regels_tonen == 'Ja') { 

$array_drachtdatum[] = $Id; ?>

<tr class= "<?php echo $jaar; ?> selectt" > 
<td><?php /*echo $Id;*/ ?> </td>
 <td align = center style = "font-size:14px;"><?php if(!isset($drachtig) || $drachtig =='nee' ) { ?> 

<!-- <button class=btn btn-sm btn-danger delete_class id= <?php echo $Id; ?> >Verwijder dekking</button> -->

<input type = "checkbox" name= <?php echo "chkDel_$Id"; ?> value = 1 style = "font-size:9px" >

      <?php } ?>
 </td>
 <td align = center style = "font-size: <?php echo $fontsize; ?> ; color : <?php echo $color; ?> ;"><?php echo $dekdm; ?></td><td width = "1">
 </td>
 <?php if(isset($lamrn) ) { /*unset($fontsize);*/ } ?>
 <td align = center style = "font-size: <?php echo $fontsize; ?> ; color : <?php echo $color; ?> ;"><?php echo "$moeder";?>
 </td>
 <td width = "1">
 </td> 
 <td align="center">
 <!-- KZLVADER -->
     <select name= <?php echo "kzlRam_$Id"; ?> style= "width:65;" >
 <option></option>    
<?php    $count = count($vawerknr);
for ($i = 0; $i < $count; $i++){
        
    $opties= array($vaRaak[$i]=>$vawerknr[$i]);
            foreach ( $opties as $key => $waarde)
            {
    if(($vaderId == $vaRaak[$i]) || (isset($_POST["kzlRam_$Id"]) && $_POST["kzlRam_$Id"] == $key)) {
        echo '<option value="'. $key .'" selected>' . $waarde . '</option>'; }
        else
        {
            echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
            }
        
        } ?>
 </select>
 <!-- Einde KZLVADER -->
 </td>
 <td width = "1">
 </td> 
 <td align="center"> 
<?php $opties = array('ja' => 'Ja', 'nee' => 'Nee');

$param = $Id . ", '" . $drachtdm . "', " . $txtGrootte;

/* Oude dekkingen zijn tot een jaar terug te wijzigen. Zie $oude_registratie */
if(isset($lamrn) || $oude_registratie == 'Ja') { echo $opties[$drachtig]; } else { ?>
     <!-- Keuzelijst drachtig -->
     <select id= <?php echo "drachtig_$Id"; ?> name = <?php echo "kzlDrachtUpd_$Id"; ?> onchange= "toon_txtDatum( <?php echo $param; ?> )" style = "width:60; font-size:13px;">
<?php  

foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave_']) && $drachtig == $key) || (isset($_POST["kzlDrachtUpd_$Id"]) && $_POST["kzlDrachtUpd_$Id"] == $key) ) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
    echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select>
<!-- Einde Keuzelijst drachtig -->
<?php } ?>
 </td>
 <td align="center">
     <input type="text"  size = 8 id= <?php echo "drachtdatum_$Id"; ?> class= "<?php echo $Id; ?> " name= <?php echo "txtDrachtdm_$Id"; ?> value = <?php echo $drachtdm; ?> >
     <?php if(isset($lamrn) ) { echo $drachtdm; } ?>
 </td>



 <td align="center">
     <?php if(isset($lamrn)) { echo $lamrn; } else { ?>
    <input type = "text" id= <?php echo "worp_$Id"; ?> class= "<?php echo $Id; ?>" size = 1 style = "font-size : 11px; text-align : center;" name = <?php echo "txtGrootte_$Id"; ?> value = <?php echo $txtGrootte; ?> >
     <?php } ?>
 </td>


 <td><?php echo $werpdm; ?></td>


 <td><?php echo $verblijf; ?></td>

</tr>

<?php } // Einde if($regels_tonen == 'Ja') ?>


</tr>
<?php unset($color); 

}
  ?>
<tr class= "<?php echo $jaar; ?> selectt" ><td height="50"></td></tr>

<?php    }
 ?>

</td></tr>

</table>
<!--*****************************
         EINDE WIJZIGEN DEKKINGEN
    ***************************** -->
</form>
</td></tr></table>



</TD>

<?php
include "menu1.php"; } 
} // Einde if($modtech == 1)
include "dekkingen-2.js.php";
?>
</body>
</html>
