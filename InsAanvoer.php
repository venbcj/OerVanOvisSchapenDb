<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt en quotes bij "kg" weggehaald 
23-11-2014 : functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server
8-3-2015 : Login toegevoegd 
18-11-2015 Aanwas gewijzigd naar Aanvoer
 */
$versie = '9-11-2016'; /* Controle moederdier aangepast */
$versie = '11-11-2016'; /* Controle of dier elders nog op stal staat verwijderd. Dit werkt ave rechts op het programma. Alleen i.v.m. andere gebruikers heeft dit een blokkerende werking. */
$versie = '20-1-2017'; /* hok_uitgez = 'Gespeend' gewijzigd in hok_uitgez = 2. */
$versie = '1-2-2017'; /* Halsnummer toegevoegd  */
$versie = '28-2-2017'; /* Ras en gewicht niet veplicht gemaakt        4-4-2017 : kleuren halsnummer uitgebreid */
$versie = '17-2-2018'; /* keuzelijst moederdier verwijderd */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '24-6-2020'; /* onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '28-2-2020'; /* fase gebaseerd om omschrijving geslacht */
$versie = '26-11-2022'; /* geboortedatum toegevoegd en sql beveiligd met enkele quotes */
$versie = '10-11-2024'; /* ubn toegevoegd aan keuzelijst herkomst */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '17-04-2025'; /* Controle of levensnummer al bestaat in database vervangen door controle op aan- of afwezigheid op de stallijst. Zie (levnr_stal > 0 && !isset(afgevoerd)) */
$versie = '11-07-2025'; /* Veld Ubn toegevoegd. Betreft eigen ubn van gebruiker. Per deze versie kan een gebruiker meerdere ubn's hebben */

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
$titel = 'Inlezen Aanvoer';
$file = "InsAanvoer.php";
include "login.php"; ?>

            <TD valign = "top">
<?php 
if (Auth::is_logged_in()) {
 
If (isset($_POST['knpInsert_']))  {
    include "post_readerAanv.php"; #Deze include moet voor de vervversing in de functie header()
    //header("Location: ".$url."InsAanvoer.php"); 
    }

// Aantal nog in te lezen AANVOER
/*$aanvoer = mysqli_query($db,"SELECT count(*) aant 
                            FROM impReader 
                            WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and teller_aanv is not NULL and isnull(verwerkt) ") or die (mysqli_error($db));
    $row = mysqli_fetch_assoc($aanvoer);
        $aantaanw = $row['aant'];*/
// EINDE Aantal nog in te lezen AANVOER

$velden = "rd.actId, rd.Id readId, rd.datum, rd.ubnId ubnId_rd, rd.levensnummer levnr_rd, rd.ubn ubn_herk, rd.rasId rasId_rd, rd.geslacht, rd.hokId hok_rd, rd.gewicht, rd.datumdier geb_datum, 
s.schaapId schaapId_db, st.levensnummer levnr_stal, afv.actId afvoerId, p.ubn ubn_db, r.rasId rasId_db, ho.hokId hok_db, dup.dubbelen ";

$tabel = "
impAgrident rd
 left join tblSchaap s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
    GROUP BY s.schaapId, s.levensnummer
 ) st on (rd.levensnummer = st.levensnummer)
 left join (
    SELECT max(h.datum) datum, s.schaapId, s.levensnummer
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and h.skip = 0
    GROUP BY s.schaapId, s.levensnummer
 ) lstDate on (rd.levensnummer = lstDate.levensnummer)
 left join (
     SELECT h.actId, h.datum, st.schaapId
     FROM tblHistorie h
      join tblStal st on (h.stalId = st.stalId)
      join tblActie a on (a.actId = h.actId)
     WHERE st.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and a.af = 1 and h.skip = 0
 ) afv on (afv.datum = lstDate.datum and afv.schaapId = lstDate.schaapId)
 left join tblPartij p on (rd.ubn = p.ubn and p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "')
 left join (
    SELECT ru.lidId, r.rasId
    FROM tblRas r
     join tblRasuser ru on (r.rasId = ru.rasId)
    WHERE r.actief = 1 and ru.actief = 1
 ) r on (rd.rasId = r.rasId and r.lidId = rd.lidId)
 left join (
    SELECT ho.hokId
    FROM tblHok ho
    WHERE ho.lidId = '" . mysqli_real_escape_string($db,$lidId) . "'
 ) ho on (rd.hokId = ho.hokId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.Id <> dup.Id and rd.actId = dup.actId and isnull(dup.verwerkt))
    WHERE rd.actId = 2 or rd.actId = 3
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
";

$WHERE = "WHERE rd.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and (rd.actId = 2 or rd.actId = 3) and isnull(rd.verwerkt)";

include "paginas.php";
$data = $page_nums->fetch_data($velden, "ORDER BY rd.datum, rd.Id"); ?>

<table border = 0>
<tr> <form action="InsAanvoer.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td colspan = 3 align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 3 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet herkend. <br> 
<?php if($modtech == 1) { ?>* Alleen verplicht bij lammeren. <?php } ?> </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Aanvoer<br>datum<hr></th>
 <th>Ubn<hr></th>
 <th>Levensnummer<hr></th>
 <th colspan = 2>Halsnummer<hr></th>
 <th>Ras<hr></th>
 <th>Geslacht<hr></th>
 <th>Generatie<hr></th>
<?php if($modtech == 1) { ?>
 <th>Gewicht<hr></th>
 <th>Geboren<hr></th>
 <th>Verblijf*<hr></th>
<?php } ?>
 <th>Herkomst<hr></th>
 <th><hr></th>

</tr>

<?php
// Declaratie kzlUbn
$qryUbn = mysqli_query($db,"
SELECT ubnId, ubn
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and actief = 1
ORDER BY ubn
") or die (mysqli_error($db));

$index = 0; 
while ($qu = mysqli_fetch_assoc($qryUbn)) 
{ 
   $kzlUbnId[$index] = $qu['ubnId'];
   $ubnnm[$index] = $qu['ubn'];
   $index++;
} 
unset($index);

$count = count($kzlUbnId);
// Einde Declaratie kzlUbn

// Declaratie ras
$qryRassen = ("
SELECT r.rasId, r.ras, lower(coalesce(isnull(ru.scan),'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and r.actief = 1 and ru.actief = 1
ORDER BY ras
"); 
$RAS = mysqli_query($db,$qryRassen) or die (mysqli_error($db)); 

$index = 0; 
$rasId = [];
$rasnm = [];
while ($ras = mysqli_fetch_array($RAS)) 
{
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
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
if($modtech == 1) {
// Declaratie HOKNUMMER            // lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryHoknummer = mysqli_query($db,"
SELECT hokId, hoknr, lower(if(isnull(scan),'6karakters',scan)) scan
FROM tblHok
WHERE lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db));

$index = 0; 
while ($hknr = mysqli_fetch_assoc($qryHoknummer)) 
{ 
   $hoknId[$index] = $hknr['hokId']; 
   $hoknum[$index] = $hknr['hoknr'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER
}

// Declaratie HERKOMST            // lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryRelatie = ("SELECT r.relId, '6karakters' ubn, concat(p.ubn, ' - ', p.naam) naam
            FROM tblPartij p join tblRelatie r on (p.partId = r.partId)    
            WHERE p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and relatie = 'cred' and isnull(r.uitval) and p.actief = 1 and r.actief = 1
                  and isnull(p.ubn)
            union
            
            SELECT r.relId, p.ubn, concat(p.ubn, ' - ', p.naam) naam
            FROM tblPartij p join tblRelatie r on (p.partId = r.partId)    
            WHERE p.lidId = '" . mysqli_real_escape_string($db,$lidId) . "' and relatie = 'cred' and isnull(r.uitval) and p.actief = 1 and r.actief = 1 
                  and ubn is not null
            ORDER BY naam"); 
$relatienr = mysqli_query($db,$qryRelatie) or die (mysqli_error($db)); 

$index = 0; 
while ($rnr = mysqli_fetch_array($relatienr)) 
{ 
   $relnId[$index] = $rnr['relId'];
   $relnum[$index] = $rnr['naam'];
   $relUbn[$index] = $rnr['ubn'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HERKOMST

if(isset($data))  {    

//echo count($data);

    foreach($data as $key => $array)
    {
unset($fase_rd); // $fase_rd kan leeg zijn!

        $var = $array['datum'];
$date = str_replace('/', '-', $var);
//$gebdatum = date('d-m-Y', strtotime($date)-365*60*60*24);
$datum = date('d-m-Y', strtotime($date));
if (!empty($array['uit_vmdm'])) {
        $varuitv = $array['uit_vmdm'];
$date2 = str_replace('/', '-', $varuitv);
$uitvdm = date('d-m-Y', strtotime($date2));
        } else { $uitvdm = '' ; } 
    
    $Id = $array['readId'];
    $ubnId_rd = $array['ubnId_rd'];
    $levnr_rd = $array['levnr_rd']; //if (strlen($levnr_rd)== 11) {$levnr_rd = '0'.$array['levnr'];}
    $levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
    $schaapId_db = $array['schaapId_db'];
    $levnr_stal = $array['levnr_stal'];
    $afgevoerd = $array['afvoerId'];
    $rasId_rd = $array['rasId_rd'];
    $rasId_db = $array['rasId_db'];
    $sekse_rd = $array['geslacht'];
    $gewicht = $array['gewicht'];
    $geb_datum = $array['geb_datum']; if(isset($geb_datum)) { $gebdm = date('d-m-Y', strtotime($geb_datum)); }
    $hok_rd = $array['hok_rd'];
    $hok_db = $array['hok_db'];
    $actId = $array['actId'];
    if($actId == 2 && !isset($schaapId_db)) {
        $fase_rd = 'lam';
    }
    else if($actId == 3) { 
    if($sekse_rd == 'ram') { $fase_rd = 'vader'; }
    else { $fase_rd = 'moeder'; }
    }

    $ubn_herk_rd = $array['ubn_herk'];
    $ubn_herk_db = $array['ubn_db'];

// Zoek schaap gegevens als het levensnummer in de database al bestaat maar niet op de stallijst van de gebruiker voorkomt
unset($sekse_herk);
unset($fase_herk);

    if(!isset($levnr_stal) && isset($schaapId_db)) {
$zoek_schaapgegevens = mysqli_query($db,"
SELECT r.ras, s.geslacht, ouder.schaapId aanw, date_format(geb.datum, '%d-%m-%Y') gebdm
FROM tblSchaap s
 left join tblRas r on (s.rasId = r.rasId)
 left join (
     SELECT st.schaapId
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 3
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
     SELECT st.schaapId, h.datum
     FROM tblHistorie h
      join tblStal st on (st.stalId = h.stalId)
     WHERE h.actId = 1
 ) geb on (geb.schaapId = s.schaapId)
 WHERE s.schaapId = '" . mysqli_real_escape_string($db, $schaapId_db) . "'
") or die (mysqli_error($db));

while ($zs = mysqli_fetch_assoc($zoek_schaapgegevens)) 
{ 
    
    $ras_herk = $zs['ras']; // Ras van herkomst of te wel van een bestaand schaap dat niet op de stallijst van de gebruiker staat
    $sekse_herk = $zs['geslacht']; /* $sekse_herk moet het eventuele geslacht ($sekse_rd) uit de reader overschrijven */
    $volwassen_herk = $zs['aanw']; 
        if(isset($volwassen_herk)) { 
                  if($sekse_herk == 'ooi') { $fase_herk = 'moeder'; }
            else if($sekse_herk == 'ram') { $fase_herk = 'vader'; }
             unset($volwassen_herk); 
                                             }
        else { $fase_herk = 'lam'; }
    $gebdm_herk = $zs['gebdm'];
}
}
// Eind Zoek schaap gegevens als het levensnummer in de database al bestaat maar niet op de stallijst van de gebruiker voorkomt


// Controleren of ingelezen waardes worden gevonden .
$kzlUbn = $ubnId_rd;  
$kzlRas = $rasId_db;
$kzlHok = $hok_db; 
if(!empty($sekse_herk)) { $sekse = $sekse_herk; } else { $sekse = $sekse_rd; }
if(!empty($fase_herk)) { $fase = $fase_herk; } else { $fase = $fase_rd; }

if (isset($_POST['knpVervers_'])) {
    $datum = $_POST["txtaanwdm_$Id"]; 
    $kzlUbn = $_POST["kzlUbn_$Id"];
    $hnr = $_POST["txtHnr_$Id"];
    $kzlRas = $_POST["kzlras_$Id"];
    $kzlFase = $_POST["kzlFase_$Id"]; if(isset($kzlFase)) { $fase = $kzlFase; unset($kzlFase); }

if($modtech == 1) { $gewicht = $_POST["txtkg_$Id"]; $kzlHok = $_POST["kzlHok_$Id"]; }
     }
     If     
     ( /*Aanvoer volwassenen */   ( ($fase == 'moeder' || $fase == 'vader') && #generatie moet moeder of vader zijn
                        (    empty($datum)                || # of aanvoerdatum is leeg
                           empty($kzlUbn)             || # of ubn is onbekend of leeg
    ($levnr_stal > 0 && !isset($afgevoerd))    || # of levensnummer staat op de stallijst
                                isset($levnr_dupl)      || # of levensnummer bestaat al in reader bestand
                            strlen($levnr_rd)<> 12    || # of levensnummer is geen 12 karakters lang of dus leeg
                        Validate::numeriek($levnr_rd) == 1    || # of levensnummer bevat een letter 
        ($fase == 'moeder' && $sekse =='ram')     || #generatie en geslacht is tegenstrijdig
        ($fase == 'vader' && $sekse =='ooi')     ) #generatie en geslacht is tegenstrijdig
                            
                      ) 
    ||    
    /*Aanvoer lammeren*/  (  $fase == 'lam' && #generatie moet lam zijn
                        (    empty($datum)                || # of datum is leeg
                           empty($kzlUbn)             || # of ubn is onbekend of leeg
    ($levnr_stal > 0 && !isset($afgevoerd))    || # of levensnummer staat op de stallijst
                                isset($levnr_dupl)      || # of levensnummer bestaat al in reader bestand
                            strlen($levnr_rd)<> 12    || # of levensnummer is geen 12 karakters lang of dus leeg
                        Validate::numeriek($levnr_rd) == 1    || # of levensnummer bevat een letter
              ($modtech == 1 && empty($kzlHok))     ) # of hoknr is onbekend of leeg
                    )
    ||
    /*Aanvoer niet moeders of lammeren*/  (   empty($fase) #generatie kan nooit vader of leeg zijn
                    )
    ) {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **               OPMAAK  GEGEVENS                **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = center> 
     <?php //echo $Id; ?>

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
<?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtaanwdm_$Id"]; } ?>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtaanwdm_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
  <td>
<!-- KZLUBN -->
 <select style="width:65;" <?php echo " name=\"kzlUbn_$Id\" "; ?> value = "" style = "font-size:10px;">
  <option></option>
<?php    $count = count($kzlUbnId);    
for ($i = 0; $i < $count; $i++){

    $opties = array($kzlUbnId[$i]=>$ubnnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ubnId_rd == $kzlUbnId[$i]) || (isset($_POST["kzlUbn_$Id"]) && $_POST["kzlUbn_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select>
 <!-- Einde KZLUBN -->
 </td>
<?php if (($levnr_stal == 0 || ($levnr_stal > 0 && isset($afgevoerd)) ) && strlen($levnr_rd) == 12 && Validate::numeriek($levnr_rd) <> 1) { ?> 
 <td>
<?php echo $levnr_rd; } else { ?> <td style = "color : red;" > <?php echo $levnr_rd; } ?>
<!-- <input type = "hidden" name = <p??hp echo " \"txtlevgeb_$Id\" value = \"$levnr_rd\" ;"?> size = 9 style = "font-size : 9px;"> -->
 </td>
 <td>
<!-- HALSKLEUR -->
 <select name= <?php echo "kzlKleur_$Id"; ?> style= "width:63;" > 
<?php
$opties = array('' => '', 'blauw' => 'blauw', 'geel' => 'geel', 'groen' => 'groen', 'oranje' => 'oranje', 'paars' => 'paars', 'rood'=>'rood', 'wit' => 'wit', 'zwart' => 'zwart');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave']) && $kleur == $key) || (isset($_POST["kzlKleur_$Id"]) && $_POST["kzlKleur_$Id"] == $key) ) {
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
    echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?>
</select>  </td>
<!-- HALSNR -->
 <td>
    <input type = text name = <?php echo "txtHnr_$Id"; ?> style = "text-align : right" size = 1 value = <?php if(isset($hnr)) { echo $hnr; } ?> > </td>
 <td align="center">

<?php if(!isset($levnr_stal) && isset($schaapId_db)) { echo $ras_herk; /* Als het levensnummer al bestaat maar niet op de stallijst van de gebruiker */ } else { ?>

<!-- KZLRAS -->
 <select style="width:65;" <?php echo " name=\"kzlras_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($rasId);    
for ($i = 0; $i < $count; $i++){

    $opties = array($rasId[$i]=>$rasnm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $rasId_rd == $rasId[$i]) || (isset($_POST["kzlras_$Id"]) && $_POST["kzlras_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select>
<?php if( !empty($rasId_rd) && empty($rasId_db) && !isset($_POST['knpVervers_']) ) {echo $rasId_rd; ?> <b style = "color : red;"> ! </b> <?php } ?>
     <?php // EINDE KZLRAS
} // Einde if(!isset($levnr_stal) && isset($schaapId_db))  ?>
 </td>
 <td align="center">

<?php if(!isset($levnr_stal) && isset($schaapId_db)) { echo $sekse; /* Als het levensnummer al bestaat maar niet op de stallijst van de gebruiker */ } else { 

// KZLGESLACHT ?> 
<select <?php echo " name=\"kzlsekse_$Id\" "; ?> style="width:59; font-size:13px;">

<?php  echo "$row[geslacht]";
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $sekse == $key) || (isset($_POST["kzlsekse_$Id"]) && $_POST["kzlsekse_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

    ?> </select> <?php // EINDE KZLGESLACHT 
} // Einde if(!isset($levnr_stal) && isset($schaapId_db))  ?>
 </td>
 <td align="center">

<?php if(!isset($levnr_stal) && isset($schaapId_db)) { echo $fase; /* Als het levensnummer al bestaat maar niet op de stallijst van de gebruiker */ } else { 

// KZLGENERATIE ?>
<select <?php echo " name=\"kzlFase_$Id\" "; ?> >

<?php  
$opties = array('' => '', 'lam' => 'lam', 'moeder' => 'moeder', 'vader' => 'vader');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpVervers_']) && $fase_rd == $key) || (isset($_POST["kzlFase_$Id"]) && $_POST["kzlFase_$Id"] == $key) ) {
   echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
 } else {
   echo '<option value"' . $key . '">' . $waarde . '</option>';
   }
}

    ?> </select> <?php // EINDE KZLGENERATIE 
} // Einde if(!isset($levnr_stal) && isset($schaapId_db)) ?>
 </td>
<?php if($modtech == 1) { ?>
<!-- GEWICHT -->
<?php if(isset($_POST["knpVervers_"])) {    $gewicht = $_POST["txtkg_$Id"];    } ?>    
 <td align = center style = "font-size : 11px;"> <input type = "text" name = <?php echo "txtkg_$Id"; ?> size = 1 value = <?php echo $gewicht;?> >
 </td> <!-- EINDE GEWICHT -->

 <!-- GEBOORTE DATUM -->
<?php if(isset($_POST["knpVervers_"])) {    $gebdm = $_POST["txtGebdm_$Id"];    } ?>    
 <td align="center" style = "font-size : 11px;" >

<?php if(!isset($levnr_stal) && isset($schaapId_db)) { echo $gebdm_herk; /* Als het levensnummer al bestaat maar niet op de stallijst van de gebruiker */ } else { ?>

 <input type = "text" align = center size = 7 style = "font-size : 11px;" name = <?php echo "txtGebdm_$Id"; ?>  value = <?php echo $gebdm; unset($gebdm); ?> >

<?php } // Einde if(!isset($levnr_stal) && isset($schaapId_db)) ?>
 </td> <!-- EINDE GEBOORTE DATUM --> 

 <td style = "font-size : 9px;">
<!-- KZLHOKNR --> 
 <select style="width:65;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>

<?php    $count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hoknId[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?>    </select>
<?php 
if ( !empty($hok_rd) && empty($hok_db) && !isset($_POST['knpVervers_']) ) {echo $hok_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHOKNR -->
<?php } ?>

 <td style = "font-size : 11px;">
<!-- KZLHERKOMST -->
 <select style="width:145;" <?php echo " name=\"kzlHerk_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($relnum);
for ($i = 0; $i < $count; $i++){

    $opties = array($relnId[$i]=>$relnum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ubn_herk_rd == $relUbn[$i]) || (isset($_POST["kzlHerk_$Id"]) && $_POST["kzlHerk_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>
<?php if( isset($ubn_herk_rd) && empty($ubn_herk_db) && !isset($_POST['knpVervers_']) && !isset($bericht) ) {echo $ubn_herk_rd; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 </td> <!-- EINDE KZLHERKOMST -->    

<?php
if ( !empty($levnr_rd) && ($levnr_stal > 0 && !isset($afgevoerd)) )         { $color = 'red'; $bericht = "Staat al op stallijst."; }
else if (isset($levnr_dupl) )                     { $color = 'blue'; $bericht =  "Dubbel in de reader."; }
else if (isset($levnr_rd) && strlen($levnr_rd) <> 12) { $color = 'red'; $bericht = "Levensnummer geen 12 karakters."; }  
else if (Validate::numeriek($levnr_rd) == 1)                 { $color = 'red'; $bericht = "Levensnummer bevat een letter."; } ?>


 <td colspan = 3 style = "color : <?php echo $color; ?> ; font-size : 11px;"> <?php if(isset($bericht)) { echo $bericht; unset($bericht); unset($color); } ?>
<!-- EINDE Als levensnummer uniek is EN 12 karakters lang is EN geen letter bevat --> 
 </td> 
</tr>
<!--    **************************************
        **            EINDE OPMAAK GEGEVENS        **
        ************************************** -->

<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 



</TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>
<?php
    include "select-all.js.php";
?>

</body>
</html>
