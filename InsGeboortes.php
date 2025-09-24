<?php

require_once("autoload.php");

$versie = '18-2-2014'; /*Keuzelijst uitval uitgebreid met uitvalId <= 3 en gesorteerd op uitvalId */
$versie = '15-4-2014'; /*vw_Reader_geb toegevoegd query verplaatst naar vw_Reader.php*/
$versie = '9-05-2014'; /*Toegevogd => Ras en geslacht mogen niet leeg zijn, dan is nl. het selectieveld niet aangevinkt*/
$versie = '8-8-2014'; /*Aantal karakters werknr variabel gemaakt en quotes bij "kg" en "ooi_rd" weggehaald*/
$versie = '11-8-2014'; /*veld type gewijzigd in fase */
$versie = '13-11-2014'; /*functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server*/
$versie = '21-2-2015'; /*login toegevoegd*/
$versie = '5-3-2015'; /*sql beveiligd*/
$versie = '3-12-2015'; /*Karwerki gewijzigd in Karwerk*/
$versie = '17-09-2016'; /* modules gesplitst */
$versie = '2-11-2016'; /* : controle of moment bij doelgroep geboren hoort verwijderd. Betreft voormalig veld 'geb' in tblMoment */
$versie = '20-1-2017'; /* : hok_uitgez = 'Gespeend' gewijzigd in hok_uitgez = 2 */
$versie = '15-2-2017'; /* Alle actieve hokken laten zien      18-2-2017 : Controle op startdatum moeder toegevoegd */
$versie = '28-2-2017'; /* Ras en gewicht niet veplicht gemaakt         16-3 geslacht niet verplicht gemaakt */ 
$versie = '28-4-2017'; /* Hidden velden txtId en txtLevnr verwijderd */ 
$versie = '1-7-2017'; /* Controle dat moederdier niet kan lammeren 4 maanden voor en na laatste lam */ 
$versie = '25-2-2018'; /* Bij controle verplichte velden 'dood zonder levensnummer' moet afvoerdatum wel bestaan. isset(dmafvmdr) toegevoegd */ 
$versie = '11-3-2018'; /* ooi_db uitgebreid zodat het dier wel een moeder is (actId = 3) en dat het dier op het bedrijf moet zijn. Anders wordt het dier onterecht gevonden in tblSchaap en 'bestaat' de moeder. */ 
$versie = '19-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast 6-7 query (velden en tabel) aangepast. dubbele 'inlees' als query genest */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '2-2-2020'; /* keuzelijst geslacht uitgebreid met kween */
$versie = '4-3-2020'; /* tabel tabel gewijzgd. Aanwasmoment en of moeder aanwezig is uit elkaar gehaald */
$versie = '16-3-2020'; /* scr (bibliotheek) van java script gewijzigd. Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '1-6-2020'; /* Taak Dood geboren toegevoegd */
$versie = '13-6-2020'; /* Einddatum moeder gebaseerd op juiste stalId */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '17-7-2020'; /* De query data aangepast met moeders die tot 2 maanden terug nog op de stallijst stonden */
$versie = '6-9-2020'; /* De query data toegevoegd (isnull(af.datum) or .... toegevoegd  */
$versie = '24-1-2021'; /* Sql beveiligd met quotes. Reden uitval alleen bij reader Agrodent en indien dood geboren en uitval voor merken */
$versie = '10-1-2022'; /* Code aangepast n.a.v. registratie dekkingen en dracht */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '10-03-2024'; /* Keuzelijst verblijf breder gemaakt van width:68 naar width:84 Veld datum smaller van size = 9 naar size = 7 */
$versie = '24-11-2024'; /* In keuzelijst moederdieren uitgeschaarde dieren wel tonen. zoek_einde_moeder aangevuld met h.actId = 10 */
$versie = '26-12-2024'; /* <TD width = 1010 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '09-04-2025'; /* De subquery mdr binnen query data aangepast. Uitgeschaarden worden niet getoond in sybquery mdr. Als ze inmiddels terug zijn van uitscharen wordem 2 records getoond. Het veld stalId is verwijderd en de velden s.schaapId, s.levensnummer, af.datum worden bij deze gegroepeerd */
$versie = '10-07-2025'; /* De index van kzlOoi gewijzigd van schaapId naar stalId zodat het ubnId makkelijker kan worden opgehaald */
$versie = '29-08-2025'; /* Controle of ubn kan worden gevonden bij gebruikers die geen module technisch hebben toegevoegd */

 Session::start();
 ?>   
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Geboortes';
$file = "InlezenReader.php";
include "login.php"; ?>

       <TD valign = "top">
<?php
if (Auth::is_logged_in()) {

include "vw_kzlOoien.php";

$today_number = strtotime("now"); 
$jaarlater = date('Y-m-d', strtotime('+ 1 year', $today_number));

if (isset($_POST['knpSave_']) ) { 
    include "save_reader.php"; 
    }
    
if (isset($_POST['knpInsert_']) ) { 
    include "post_readerGeb.php"; #Deze include moet voor de vervversing in de functie header()
    }
    

$velden = "rd.Id readId,date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr_rd, s.levensnummer levnr_db, rd.rasId ras_rd, r.rasId ras_scan, rd.geslacht, rd.moeder, mdr.schaapId mdrId_db, 
    date_format(mdr.datum,'%Y-%m-%d') eindmdr, rd.hokId hok_rd, hb.hokId hok_scan, rd.gewicht, rd.verloop, rd.leef_dgn, rd.momId mom_rd, DATE_ADD(rd.datum, interval rd.leef_dgn day) date_dood, date_format(DATE_ADD(rd.datum, interval rd.leef_dgn day),'%d-%m-%Y') datum_dood, rd.reden red_rd, red.redId red_db, dup.dubbelen";

$tabel = "
impAgrident rd
 left join (
 SELECT levensnummer 
 FROM tblSchaap s
  join tblStal st on (st.schaapId = s.schaapId)
 WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT s.schaapId, s.levensnummer, af.datum
    FROM tblSchaap s
     join tblStal st on (s.schaapId = st.schaapId)
     join (
        SELECT schaapId
        FROM tblStal st
         join tblHistorie h on (h.stalId = st.stalId)
        WHERE h.actId = 3 and h.skip = 0
     ) prnt on (prnt.schaapId = s.schaapId)
     left join (
       SELECT st.stalId, datum, hisId
       FROM tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        join tblActie a on (a.actId = h.actId)
       WHERE a.af = 1 and h.actId != 10 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) af on (af.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'  and (isnull(af.datum) or af.datum > date_add(curdate(), interval -2 month) )
    GROUP BY s.schaapId, s.levensnummer, af.datum
 ) mdr on (rd.moeder = mdr.levensnummer)
 left join (
    SELECT ru.rasId
    FROM tblRas r
     join tblRasuser ru on (r.rasId = ru.rasId)
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
 ) r on (rd.rasId = r.rasId)
 left join (
    SELECT hokId
    FROM tblHok
    WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
 ) hb on (rd.hokId = hb.hokId)
 left join (
    SELECT r.redId
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.uitval = 1
 ) red on (rd.reden = red.redId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = 1 and dup.actId = 1 and isnull(dup.verwerkt))
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 " ;

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 1 and isnull(rd.verwerkt)";

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id"); ?>



<table border = 0>
<tr> <form action="InsGeboortes.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"><!--<input type = "submit" name = "knpSave_" value = "Opslaan">--> </td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
/*echo '<br>'; 
echo '$page_nums->total_pages : '.$page_nums->total_pages.'<br>'; 
echo '$page_nums->total_records : '.$page_nums->total_records.'<br>'; 
echo '$page_nums->rpp : '.$page_nums->rpp.'<br>'; */
echo /*'$page_numbers : '.*/$page_numbers/*.'<br> '.$record_numbers.'<br>'*/; 
/*echo '$page_nums->count_records() : '. $page_nums->count_records();*/ 
//echo '$page_nums->pagina_string : '. $page_nums->pagina_string; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 1 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 3 style = "font-size : 12px;"><?php if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_'])) { ?><b style = "color : red;">!</b> = waarde uit reader niet gevonden. <br> <?php } ?> </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Geboorte<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Ras<hr></th>
 <th>Geslacht<hr></th>
<?php if($modtech == 1) { ?>
 <th>Gewicht<hr></th>
 <th>Moederdier<hr></th>
 <th>Verblijf<hr></th>
 <?php if($reader == 'Biocontrol') { ?> <th></th> <?php } 
  else if($reader == 'Agrident')   { ?> <th>Worpverloop<hr></th> <?php } ?> 
  <th>Uitval moment<hr></th>
  <th>Uitval datum<br>(voor merken) <hr></th>
 <?php if($reader == 'Biocontrol') { ?> <th></th> <?php } 
  else if($reader == 'Agrident')   { ?> <th>Reden uitval<hr></th> <?php } ?>
 <th><hr></th>
 
<?php } ?>
 </tr>

<?php
// Declaratie ubn
$qryUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY ubn
") or die (mysqli_error($db));

$index = 0; 
while ($qu = mysqli_fetch_assoc($qryUbn)) 
{ 
   $ubnId[$index] = $qu['ubnId']; 
   $ubnnm[$index] = $qu['ubn'];
   $index++; 
} 
unset($index);

$count = count($ubnId); 
// EINDE Declaratie ubn

// Declaratie ras
$qryRassen = mysqli_query($db,"
SELECT r.rasId, r.ras, lower(if(isnull(ru.scan),'6karakters',ru.scan)) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.actief = 1 and ru.actief = 1
ORDER BY r.ras
") or die (mysqli_error($db));

$index = 0; 
$rasId = [];
$rasnm = [];
$rasRaak = [];
while ($ras = mysqli_fetch_assoc($qryRassen)) 
{ 
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
   $rasRaak[$index] = $ras['scan'];   if($reader == 'Agrident') { $rasRaak[$index] = $ras['rasId']; }
   $index++; 
} 
unset($index);

//dan het volgende:
$count = count($rasId); 
/*
echo "<select name=\"kzlras_Id\">"; 
for ($i = 0; $i <= $count; $i++) 
{ 
    echo "<option value=\"$rasId[$i]\">$rasnm[$i]</option>"; 
} 
echo "</select>";*/
// EINDE Declaratie ras

// Declaratie MOEDERDIER
$qryMoeder = ("(".$vw_kzlOoien.") "); 
$moederdier = mysqli_query($db,$qryMoeder) or die (mysqli_error($db)); 

$index = 0; 
while ($mdr = mysqli_fetch_assoc($moederdier)) 
{ 
   $mdrStalId[$index] = $mdr['stalId']; // 10-07-2025 gewijzigd van $mdr['schaapId']; naar $mdr['stalId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $mdrRaak[$index] = $mdr['stalId']; // 10-07-2025 gewijzigd van $mdr['schaapId']; naar $mdr['stalId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie HOKNUMMER            // lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, scan, hoknr
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db)); 

$index = 0; 
while ($hnr = mysqli_fetch_assoc($qryHoknummer)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['scan'];   if($reader == 'Agrident') { $hokRaak[$index] = $hnr['hokId']; }
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

// Declaratie MOMENT
$qryMoment = "
SELECT m.momId, moment, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblMoment m
 join tblMomentuser mu on (m.momId = mu.momId)
WHERE mu.lidId = '".mysqli_real_escape_string($db,$lidId)."'

union

SELECT 3, 'uitval voor merken', 3 scan
FROM dual

ORDER BY momId"; 

$moment = mysqli_query($db,$qryMoment) or die (mysqli_error($db)); 

$index = 0; 
while ($mom = mysqli_fetch_assoc($moment)) 
{ 
   $momId[$index] = $mom['momId'];
   $momnt[$index] = $mom['moment'];
  if($reader == 'Biocontrol')  { $momRaak[$index] = $mom['scan']; }
  if($reader == 'Agrident')  { $momRaak[$index] = $mom['momId']; }
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOMENT

// Declaratie REDEN bij Agrident
// lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryReden = "
SELECT r.redId, r.reden
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId) 
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."'
ORDER BY r.reden"; 

$redenen = mysqli_query($db,$qryReden) or die (mysqli_error($db)); 

$index = 0; 
while ($red = mysqli_fetch_assoc($redenen)) 
{ 
   $redId[$index] = $red['redId'];
   $reden[$index] = $red['reden'];
   $redRaak[$index] = $red['redId'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie REDEN bij Agrident

// ARRAY Zoek naar meerdere werpdatums per moeder

if($modtech == 1 && isset($data))  {    
    foreach($data as $key => $array) {

    $Id = $array['readId'];
    $datu = $array['datum'];
    $moe = $array['moeder'];


if(isset($_POST['knpVervers_'])) { $datu = $_POST["txtDatum_$Id"]; $moeId = $_POST["kzlOoi_$Id"]; 

//echo $moeId.'<br>';

if(empty($moeId)) { $moeId = 1; }
$zoek_ooi = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
WHERE stalId = '". mysqli_real_escape_string($db,$moeId) ."'
") or die (mysqli_error($db));

while ($m = mysqli_fetch_assoc($zoek_ooi)) { $moe = $m['levensnummer']; //echo 'Levensnummer = '.$moe.'<br>'; 

                   $ar_DatumMoeder[] = array( 1 => $datu, 6 => $moe); }
                          }

else {    $ar_DatumMoeder[] = array( 1 => $datu, 6 => $moe); }
}
}

if($modtech == 1 && isset($ar_DatumMoeder)) { //Als alle records zijn verwerkt bestaat $ar_DatumMoeder niet meer !!
// output
$out = array();

// build data
foreach ($ar_DatumMoeder as $row) {
    //if(isset($moeId)) { echo $moeId.'<br>'; }
    //echo $row[6].'<br><br>';
    // if mother is not registered yet
    if (!isset($out[$row[6]])) {
        // if birthdate is not register yet
        $out[$row[6]] = array();
    } 
    if (!isset($out[$row[6]][$row[1]])) {
        $out[$row[6]][$row[1]] = 0;
    }
    // add one animal to this mother and date
    $out[$row[6]][$row[1]]++;
}

// check for duplicate dates per mother
foreach ($out as $mother => $dates) {
    if (count($dates) > 1) {

        $ar_mdr[$mother] = 'Werpdatum ooi verschilt';

/*echo '******<br>';
var_dump($ar_mdr);
echo '<br>';


        echo 'mother '.$mother.' has multiple birthdates:<br>';*/
        foreach ($dates as $date => $count) {
            //echo '- '.$date.' ('.$count.')<br>';
        }
    }
}
}

// Einde ARRAY Zoek naar meerdere werpdatums per moeder


if(isset($data))  {    foreach($data as $key => $array)
{
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
//$day = date('Y-m-d', strtotime($date));
$datum = date('d-m-Y', strtotime($date));
$makeday = date_create($datum); $day = date_format($makeday, 'Y-m-d');
    
    $Id = $array['readId'];
    $levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr_rd'];}
    $levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
    $levnr_db = $array['levnr_db'];
    $ras_rd = $array['ras_rd'];
    $ras_db = $array['ras_scan'];
    $sekse = $array['geslacht'];

    if($modtech == 1) {
        $ooi_rd = $array['moeder']; //echo $ar_mdr[$kzlMoeder].'<br>'; //if (strlen($ooi_rd)== 11) {$ooi_rd = '0'.$array['moeder'];}
        $dmafvmdr = $array['eindmdr'];  /*if(!isset($dmafvmdr)) { $dmafvmdr = $jaarlater; }*/ //$einddatum = strtotime ("+".$dagen."days", $begindatum);
        $ooi_db = $array['mdrId_db'];                      
        $hok_rd = $array['hok_rd'];
        $hok_db = $array['hok_scan'];
        $verloop = $array['verloop']; 
    $leef_dgn = $array['leef_dgn'];
    $mom_rd = $array['mom_rd']; if($leef_dgn > 0) { $mom_rd = 3; }

      $var1 = $array['date_dood']; // uitvaldatum voor merken
  $date1 = str_replace('/', '-', $var1);
  $uitvdag = date('d-m-Y', strtotime($date1));
  $makeday1 = date_create($uitvdag); $uitvday = date_format($makeday1, 'Y-m-d');

  //  $uitvday = $array['date_dood']; // uitvaldatum voor merken
    //    $uitvdag = $array['datum_dood']; // uitvaldatum voor merken
    $red_rd = $array['red_rd'];  
    $red_db = $array['red_db']; 
        $gewicht = $array['gewicht'];
    }

if(isset($_POST['knpVervers_'])) { $datum = $_POST["txtDatum_$Id"]; }



// Controleren of ingelezen waardes worden gevonden .
$kzlRas = $ras_db; 
$kzlSekse = $sekse; 

if($modtech == 1) {
$kzlOoi = $ooi_db; 
$kzlMoeder = $ooi_rd;
$kzlHok = $hok_db;
    if($reader == 'Biocontrol' && !empty($var1)) { $mom_rd = 3; } // Bij $mom_rd == 3 wordt keuzelijst moment gevuld met 'uitval voor merken' en bij $kzlMom == 3 wordt het veld uitvaldatum getoond
$kzlMom = $mom_rd; 
    } 
if (isset($_POST['knpVervers_'])) {

  $datum = $_POST["txtDatum_$Id"]; $makeday = strtotime($_POST["txtDatum_$Id"]);  $day = date('Y-m-d',$makeday);
$uitvdag = $_POST["txtUitvaldm_$Id"]; $makeday = strtotime($_POST["txtUitvaldm_$Id"]);  $uitvday = date('Y-m-d',$makeday);

    $kzlRas = $_POST["kzlRas_$Id"]; 
   $kzlSekse = $_POST["kzlSekse_$Id"];

if($modtech == 1) { 
  $kzlHok = $_POST["kzlHok_$Id"];
  $kzlMom = $_POST["kzlMom_$Id"];
  $gewicht = $_POST["txtKg_$Id"];
 if(!empty($_POST["kzlOoi_$Id"])) { $kzlOoi = $_POST["kzlOoi_$Id"]; 

$zoek_levensnummer_ooi = mysqli_query($db,"
SELECT levensnummer
FROM tblSchaap 
WHERE schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
") or die (mysqli_error($db)); 
      while($zlo = mysqli_fetch_array($zoek_levensnummer_ooi))
      { $kzlMoeder = $zlo['levensnummer']; }
 }

 } // Einde if($modtech == 1)
}  // Einde if (isset($_POST['knpVervers_']))

unset($werpdag);

$zoek_stalId_terug_uitscharen = mysqli_query($db,"
SELECT st.stalId
FROM tblStal st
 join tblHistorie h on (h.stalId = st.stalId)
WHERE h.actId = 11 and st.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
") or die (mysqli_error($db)); 
      while($zstu = mysqli_fetch_array($zoek_stalId_terug_uitscharen))
      { $terugstalId = $zstu['stalId']; }


unset($dmaanvmdr);


if(isset($kzlOoi)) {
$query_start_moeder = mysqli_query($db,"
SELECT s.levensnummer, h.datum
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE stalId != '".mysqli_real_escape_string($db,$terugstalId)."' and lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
    GROUP BY schaapId
 ) mst on (mst.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = mst.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.op = 1 and h.skip = 0
and not exists (
    SELECT datum 
    FROM tblHistorie ha 
     join tblStal st on (ha.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE actId = 2 and h.skip = 0 and mst.stalId = st.stalId and h.actId = ha.actId-1 and s.schaapId = '".mysqli_real_escape_string($db,$kzlOoi)/* bij aankoop incl. geboortedatum wordt geboortedatum niet getoond */."' )
") or die (mysqli_error($db)); 
        while($mdrdm1 = mysqli_fetch_array($query_start_moeder))
        { $dmaanvmdr = $mdrdm1['datum']; }
        
$zoek_einde_moeder = mysqli_query($db,"
SELECT s.levensnummer, h.datum
FROM tblSchaap s
 join (
    SELECT max(stalId) stalId, schaapId
    FROM tblStal
    WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$kzlOoi)."'
    GROUP BY schaapId
 ) st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
WHERE a.af = 1 and h.actId != 10 and h.skip = 0        
") or die (mysqli_error($db)); 
        while($mdrdm = mysqli_fetch_array($zoek_einde_moeder))
        { $dmafvmdr = $mdrdm['datum']; } /*if(!isset($dmafvmdr)) { $dmafvmdr = $jaarlater; }*/



//****************
//  WORPCONTROLE
//****************


// Zoek vorige worp
unset($lst_volwId);
// zoek de vorige worp waarbij de werpdatum minimaal 30 dagen voor de geboortedatum moet liggen. Dit voor het geval de huidige worp enkele dagen voor de geboortedatum ligt.
 $zoek_vorige_worp = mysqli_query($db,"
SELECT max(l.volwId) volwId
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 left join tblSchaap k on (k.volwId = v.volwId)
 left join (
   SELECT s.schaapId
   FROM tblSchaap s
    join tblStal st on (s.schaapId = st.schaapId)
    join tblHistorie h on (st.stalId = h.stalId)
   WHERE h.actId = 3 and h.skip = 0
 ) ha on (k.schaapId = ha.schaapId) 
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.skip = 0 and date_add(h.datum,interval 30 day) < '".mysqli_real_escape_string($db,$day)."' and isnull(ha.schaapId)
 ") or die (mysqli_error($db));
  while ( $zvw = mysqli_fetch_assoc($zoek_vorige_worp)) { $lst_volwId = $zvw['volwId']; }

unset($werpday);
// Zoek een huidige worp
$zoek_huidige_worp = mysqli_query($db,"
SELECT l.volwId, h.datum dmwerp, date_format(h.datum,'%d-%m-%Y') werpdm
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.skip = 0 and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
 ") or die (mysqli_error($db));
  while ( $zhw = mysqli_fetch_assoc($zoek_huidige_worp)) { $werpday = $zhw['dmwerp']; $werpdag = $zhw['werpdm']; }

$birthday = date_create($day);
$date_worp = date_create($werpday);

unset($dagen_verschil_worp);
if(isset($werpday)) {
$verschil_gebdm_worp = date_diff($birthday, $date_worp);
$dagen_verschil_worp = $verschil_gebdm_worp->days;
}

/*echo 'Geboortedatum = '.$day.'<br>'; #/#
echo '$lst_volwId bij '.$kzlOoi.' = '. $lst_volwId.'<br>'; #/#
echo 'Werpdatum = '.$werpdag.'<br>'; #/#
echo '$dagen_verschil_worp bij '.$kzlOoi.' = '. $dagen_verschil_worp.'<br>'; #/#
echo '<br>';*/ #/#


if($dagen_verschil_worp == 0 || $dagen_verschil_worp > 183) { unset($werpdag); }

//**********************
//  EINDE WORPCONTROLE
//**********************
} //Einde if(isset($kzlOoi))
else
{
// Als kzlOoi niet bestaat mag een gebruiker geen meerdere ubn's hebben want dan kan het ubn (van de gebruiker) o.b.v. het moederdier niet worden gevonden. In tblStal wordt nl. het ubn van de gebruiker opgeslagen.
$zoek_aantal_ubn = mysqli_query($db,"
SELECT count(ubnId) aant_ubn
FROM tblUbn 
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ") or die (mysqli_error($db));
  while ( $zau = mysqli_fetch_assoc($zoek_aantal_ubn)) { $aant_ubn = $zau['aant_ubn']; }

}
    
unset($dmaanvmdr);
unset($onjuist);
unset($color);

/* Controle bij zowel levend als dood geboren */
if (empty($datum))             { $color = 'red';  $onjuist =  'De datum ontbreekt.'; }
else if ($modtech == 1 && isset($_POST['knpVervers_']) && empty($kzlOoi)) { $color = 'red';  $onjuist = 'Moederdier ontbreekt'; }
else if ($modtech == 1 && isset($dmaanvmdr) && $day < $dmaanvmdr) { $color = 'red'; $onjuist = 'Geboortedatum ligt voor aanvoer moeder.'; }
else if ($modtech == 1 && isset($dmafvmdr) && $day > $dmafvmdr)  { $color = 'red'; $onjuist = 'Geboortedatum ligt na afvoer moeder.'; }
else if ($modtech == 1 && isset($ar_mdr[$kzlMoeder])) { $color = 'red'; $onjuist = $ar_mdr[$kzlMoeder]; }
else if($modtech == 1 && isset($werpdag))             { $color = 'red'; $onjuist = 'Werpdatum ooi is '.$werpdag; }
else if($modtech == 0 && $aant_ubn > 1)             { $color = 'red'; $onjuist = 'Ubn kan niet worden bepaald.'; } /*Gebruiker heeft meerdere ubn's en geen module Technisch. Alleen bij module technisch bestaat het moederdier en moederdier bepaald het eigen ubn (in tblStal) dat bij het lam hoort. Zonder moederdier en meerdere ubn's kan het juiste ubn dus niet worden bepaald */
/* Einde Controle bij zowel levend als dood geboren */

If (!empty($levnr_rd)) { /* Controle bij levend geborenen */
if($levnr_db > 0)                   { $color = 'red';  $onjuist = 'Dit levensnummer bestaat al.'; }
else if (isset($levnr_dupl) )       { $color = 'blue'; $onjuist = 'Dubbel in de reader.'; }
else if (strlen($levnr_rd) <> 12)   { $color = 'red';  $onjuist = 'Dit levensnummer is geen 12 karakters lang.'; }
else if (Validate::numeriek($levnr_rd) == 1)  { $color = 'red';  $onjuist =  "Levensnummer bevat een letter."; } 
else if (strlen($array['levnr_rd']) == 11 && strlen($levnr_rd) == 12) { $onjuist =  "Toevoeging voorloopnul levensnummer !"; }
else if($modtech == 1 && empty($kzlHok))              { $color = 'red'; $onjuist = 'Het verblijf is niet ingevuld'; }

}

else If (!isset($levnr_rd) || empty($levnr_rd) || $levnr_rd == '') { /* Controle bij dood geborenen */
if ($kzlMom == 3 && $uitvday <= $day) { $color = 'red'; $onjuist = 'Dit moment van uitval ligt voor de geboortedatum.'; }
}

 
     if    ( isset($onjuist)) { $oke = 0; } else { $oke = 1; }  // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden . 

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--  **************************************************************
      **        Test  GEGEVENS t.b.v. werp tijdens ontwikkelen      **
      ************************************************************** -->
<?php

$zoek_huidige_worp = mysqli_query($db,"
SELECT l.volwId, h.datum dmwerp
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.skip = 0 and h.datum = '".mysqli_real_escape_string($db,$day)."'
") or die (mysqli_error($db)); 
      while($zhw = mysqli_fetch_array($zoek_huidige_worp))
      { $worp_nu = $zhw['volwId']; 
        $werpdm_nu = $zhw['dmwerp']; }

/*echo '$kzlOoi = '.$kzlOoi.'<br>';
echo '$worp_nu = '.$worp_nu.' en werpdatum = '.$werpdm_nu.'<br>';*/

 $zoek_vorige_worp = mysqli_query($db,"
SELECT l.volwId, h.datum dmwerp
FROM tblSchaap l
 join tblVolwas v on (l.volwId = v.volwId)
 join tblStal st on (l.schaapId = st.schaapId)
 join tblHistorie h on (h.stalId = st.stalId) 
WHERE v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and h.actId = 1 and h.skip = 0 and h.datum < '".mysqli_real_escape_string($db,$day)."'
 ") or die (mysqli_error($db));
  while ( $zvw = mysqli_fetch_assoc($zoek_vorige_worp)) { $lst_volwId = $zvw['volwId']; $lst_werpdm = $zvw['dmwerp']; }

 // echo '$vorige_worp ($lst_volwId ! ) = '.$lst_volwId.' en vorige werpdatum = '.$lst_werpdm.'<br>';

$zoek_dracht = mysqli_query($db,"
 SELECT v.volwId, h.datum
 FROM tblVolwas v
  join tblDracht d on (v.volwId = d.volwId)
  join tblHistorie h on (h.hisId = d.hisId)
 WHERE h.skip = 0 and v.mdrId = '".mysqli_real_escape_string($db,$kzlOoi)."' and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
 ") or die (mysqli_error($db));
  while ( $zdr = mysqli_fetch_assoc($zoek_dracht)) { $dracht = $zdr['volwId']; $drachtdm = $zdr['datum']; } ?>
<!--  **************************************************************
      **       EINDE Test  GEGEVENS t.b.v. werp tijdens ontwikkelen      **
      ************************************************************** -->

<!--  **************************************
      **        OPMAAK  GEGEVENS       **
      ************************************** -->

<tr style = "font-size:14px;">
 <td align = center> <?php //echo $Id; ?>

    <input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */    

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = center>
    <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
<?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtDatum_$Id"]; } ?>
    <input type = "text" size = 7 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
<?php if ($levnr_db == 0 && strlen($levnr_rd) == 12 && Validate::numeriek($levnr_rd) == 0) { ?> 
 <td>
<?php echo $levnr_rd; } else { ?> <td style = "color : red;" > <?php echo $levnr_rd; } ?>
 </td>
 <td style = "font-size : 11px;">
<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlRas_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($rasId);    
for ($i = 0; $i < $count; $i++){

    $opties = array($rasId[$i]=>$rasnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ras_rd == $rasRaak[$i]) || (isset($_POST["kzlRas_$Id"]) && $_POST["kzlRas_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select>
<?php if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_']) && $ras_rd<> NULL && empty($ras_db) && empty($_POST["kzlRas_$Id"]) && $levnr_rd > 0 ) {
    
    if($reader == 'Agrident') { $ras_rd = ''; } echo "$ras_rd"; ?> <b style = "color : red;"> ! </b>  <?php } ?>
     <!-- EINDE KZLRAS -->
 </td>
 <td>
<!-- KZLGESLACHT --> 
<select <?php echo " name=\"kzlSekse_$Id\" "; ?> style="width:50; font-size:13px;">

<?php /* echo "$row[geslacht]";*/
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram', 'kween' => 'kween');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $sekse == $key) || (isset($_POST["kzlSekse_$Id"]) && $_POST["kzlSekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value="' . $key . '">' . $waarde . '</option>';
   }
}

    ?> </select> <!-- EINDE KZLGESLACHT -->
 </td>
<?php if($modtech == 1) { // Velden die worden getoond bij module technisch 
if(isset($_POST["knpVervers_"])) {    $gewicht = $_POST["txtKg_$Id"];    } ?>    
 <td align = center> <input type = "text" name = <?php echo "txtKg_$Id"; ?> style = "font-size : 11px;" size = 1 value = <?php echo $gewicht;?> ></td>
 <td style = "font-size : 11px;">
<!-- KZLMOEDER -->
<?php $width = 25+(8*$Karwerk); //echo $kzlOoi; #/# ?>
 <select style= "width:<?php echo $width; ?>;" <?php echo " name=\"kzlOoi_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($wnrOoi);
for ($i = 0; $i < $count; $i++){

    $opties = array($mdrStalId[$i]=>$wnrOoi[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ooi_db == $mdrRaak[$i]) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select> 
<?php 
if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_']) && $ooi_rd <> NULL && empty($ooi_db) && empty($_POST["kzlOoi_$Id"]) ) {echo $ooi_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
    <!-- EINDE KZLMOEDER --> </td>

 <td style = "font-size : 9px;">
 <!-- KZLHOKNR --> 
 <select style="width:84;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php    $count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hokRaak[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?>    </select>
<?php
if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_']) && $hok_rd <> NULL && empty($hok_db) && empty($_POST["kzlHok_$Id"]) && $levnr_rd > 0 ) {

if($reader == 'Agrident') { $hok_rd = ''; } echo "$hok_rd"; ?> <b style = "color : red;"> ! </b>  <?php } ?>

    </td> <!-- EINDE KZLHOKNR -->

 <td style = "font-size : 11px;"> <?php echo $verloop; ?>
 </td>

 <td style = "font-size : 11px;">

<!-- KZLMOMENT UITVAL -->
<select style="width:120;" <?php echo " name=\"kzlMom_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    
 $count = count($momId);
for ($i = 0; $i < $count; $i++){

  $opties = array($momId[$i]=>$momnt[$i]);

            foreach($opties as $key => $waarde)
            {

  if ((!isset($_POST['knpVervers_']) && $mom_rd == $momRaak[$i]) || (isset($_POST["kzlMom_$Id"]) && $_POST["kzlMom_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
 ?> </select> <!-- EINDE KZLMOMENT UITVAL -->    
 </td>
 <td> <?php if($kzlMom == 3) { ?>
  <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtUitvaldm_$Id"; ?> value = <?php echo $uitvdag; ?> >
 <?php } ?>
 </td>
 <td>
<?php 
if($reader == 'Agrident' && $mom_rd != 2) { /*Bij onvolledig dood geboren niet tonen */ ?>
<!-- KZLREDEN UITVAL bij Agrident-->
<select style="width:180;" <?php echo " name=\"kzlRed_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($redId);
for ($i = 0; $i < $count; $i++){

    $opties = array($redId[$i]=>$reden[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $red_rd == $redRaak[$i]) || (isset($_POST["kzlRed_$Id"]) && $_POST["kzlRed_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
 ?> </select> 
<?php 

if(!isset($_POST['knpVervers_']) && !isset($_POST['knpInsert_']) && $red_rd <> NULL && empty($red_db) && empty($_POST["kzlRed_$Id"]) && empty($levnr_rd) ) { ?> <b style = "color : red;"> ! </b>  <?php } ?>

<!-- EINDE KZLREDEN UITVAL bij Agrident -->
<?php } ?>
    </td> 


<?php } // Einde if($modtech == 1) Velden die worden getoond bij module technisch



         unset($dmafvmdr); ?>

<td style = "color : <?php echo $color; ?> ; font-size : 11px;" >  <?php 

 if (isset($onjuist)) { echo $onjuist; } ?>



    </td> 
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php } 
} //einde if(isset($data)) ?>

</table>
</form> 



    </TD>
<?php
$handl = "Hndl_InsGeboortes.html";
include "menu1.php"; } ?>
    </tr>

</table>
<?php
    include "select-all.js.php";
?>
</body>
</html>
