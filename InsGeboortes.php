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
    $impagrident_gateway = new ImpAgridentGateway();

include "vw_kzlOoien.php";

$today_number = strtotime("now"); 
$jaarlater = date('Y-m-d', strtotime('+ 1 year', $today_number));

if (isset($_POST['knpSave_']) ) { 
    include "save_reader.php"; 
    }
    
if (isset($_POST['knpInsert_']) ) { 
    include "post_readerGeb.php";
    }
    

$velden = "rd.Id readId,date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr_rd, s.levensnummer levnr_db, rd.rasId ras_rd, r.rasId ras_scan, rd.geslacht, rd.moeder, mdr.stalId mdrStalId_db, 
    date_format(mdr.datum,'%Y-%m-%d') eindmdr, rd.hokId hok_rd, hb.hokId hok_scan, rd.gewicht, rd.verloop, rd.leef_dgn, rd.momId mom_rd, DATE_ADD(rd.datum, interval rd.leef_dgn day) date_dood, date_format(DATE_ADD(rd.datum, interval rd.leef_dgn day),'%d-%m-%Y') datum_dood, rd.reden red_rd, red.redId red_db, dup.dubbelen";

$tabel = $impagrident_gateway->getInsGeboortesFrom();
    $WHERE = $impagrident_gateway->getInsGeboortesWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY sort, rd.Id");

?>
<table border = 0>
<tr> <form action="InsGeboortes.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;"> 
  <input type = "submit" name = "knpVervers_" value = "Verversen"><!--<input type = "submit" name = "knpSave_" value = "Opslaan">--> </td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
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
      $ubn_gateway = new UbnGateway();
$qryUbn = $ubn_gateway->lijst($lidId);

$index = 0; 
$ubnId = [];
while ($qu = $qryUbn->fetch_assoc()) { 
   $ubnId[$index] = $qu['ubnId']; 
   $ubnnm[$index] = $qu['ubn'];
   $index++; 
} 
unset($index);

$count = count($ubnId); 
// EINDE Declaratie ubn

// Declaratie ras
$ras_gateway = new RasGateway();
$qryRassen = $ras_gateway->rassen($lidId);

$index = 0; 
$rasId = [];
$rasnm = [];
while ($ras = $qryRassen->fetch_assoc()) { 
   $rasId[$index] = $ras['rasId']; 
   $rasnm[$index] = $ras['ras'];
   $index++; 
} 
unset($index);

//dan het volgende:
$count = count($rasId); 
// EINDE Declaratie ras

// Declaratie MOEDERDIER
$stal_gateway = new StalGateway();
$moederdier = $stal_gateway->kzlOoien($lidId, $Karwerk);

$index = 0; 
$wnrOoi = [];
$mdrStalId = [];
while ($mdr = $moederdier->fetch_assoc()) { 
   $mdrStalId[$index] = $mdr['stalId']; // 10-07-2025 gewijzigd van $mdr['schaapId']; naar $mdr['stalId'];
   $wnrOoi[$index] = $mdr['werknr'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOEDERDIER

// Declaratie HOKNUMMER
$hok_gateway = new HokGateway();
$qryHoknummer = $hok_gateway->kzlHok($lidId);
$index = 0; 
while ($hnr = $qryHoknummer->fetch_assoc()) { 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

// Declaratie MOMENT
$moment_gateway = new MomentGateway();
$moment = $moment_gateway->kzlMoment($lidId);
$index = 0; 
while ($mom = $moment->fetch_assoc()) { 
   $momId[$index] = $mom['momId'];
   $momnt[$index] = $mom['moment'];
   $index++; 
} 
unset($index); 
// EINDE Declaratie MOMENT

// Declaratie REDEN bij Agrident
$reden_gateway = new RedenGateway();
$redenen = $reden_gateway->kzlReden($lidId);
$index = 0; 
while ($red = $redenen->fetch_assoc()) { 
   $redId[$index] = $red['redId'];
   $reden[$index] = $red['reden'];
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
        if(isset($_POST['knpVervers_'])) {
            $datu = $_POST["txtDatum_$Id"]; $moeId = $_POST["kzlOoi_$Id"]; 
            if(empty($moeId)) { $moeId = 1; }
            $moe = $schaap_gateway->zoek_bestaand_levensnummer($moeId);
            $ar_DatumMoeder[] = array( 1 => $datu, 6 => $moe); 
        } else {
            $ar_DatumMoeder[] = array( 1 => $datu, 6 => $moe);
        }
    }
}

if($modtech == 1 && isset($ar_DatumMoeder)) {
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
        $ooi_db = $array['mdrStalId_db'];                      
        $hok_rd = $array['hok_rd'];
        $hok_db = $array['hok_scan'];
        $verloop = $array['verloop']; 
    $leef_dgn = $array['leef_dgn'];
    $mom_rd = $array['mom_rd']; if($leef_dgn > 0) { $mom_rd = 3; }

      $var1 = $array['date_dood']; // uitvaldatum voor merken
  $date1 = str_replace('/', '-', $var1 . '');
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
$kzlOoi = $ooi_db . ''; // TODO #0004197 noodfix om nulls in str_replace ed te voorkomen; vervangen door echte invoer-aanpak
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

  $kzlMoeder = $schaap_gateway->zoek_bestaand_levensnummer($kzlOoi);
 }

 } // Einde if($modtech == 1)
}  // Einde if (isset($_POST['knpVervers_']))

unset($werpdag);

$terugstalId = $stal_gateway->zoek_terug_uitscharen($kzlOoi);

if(isset($kzlOoi)) {
$dmaanvmdr = $schaap_gateway->start_moeder($lidId, $kzlOoi, $terugstalId);
$dmafvmdr = $schaap_gateway->einde_moeder($lidId, $kzlOoi);

//****************
//  WORPCONTROLE
//****************

// Zoek vorige worp
$lst_volwId = $schaap_gateway->zoek_vorige_worp($kzlOoi, $day);

// Zoek een huidige worp
[$werpday, $werpdag] = $schaap_gateway->zoek_huidige_worp($kzlOoi, $lst_volwId);
$birthday = date_create($day);
$date_worp = date_create($werpday);
unset($dagen_verschil_worp);
if(isset($werpday)) {
$verschil_gebdm_worp = date_diff($birthday, $date_worp);
$dagen_verschil_worp = $verschil_gebdm_worp->days;
}

if($dagen_verschil_worp == 0 || $dagen_verschil_worp > 183) { unset($werpdag); }

//**********************
//  EINDE WORPCONTROLE
//**********************
//Einde if(isset($kzlOoi))
} else {
// Als kzlOoi niet bestaat mag een gebruiker geen meerdere ubn's hebben want dan kan het ubn (van de gebruiker) o.b.v. het moederdier niet worden gevonden. In tblStal wordt nl. het ubn van de gebruiker opgeslagen.
    $aant_ubn = $ubn_gateway->countPerLid($lidId);
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
  if ((!isset($_POST['knpVervers_']) && $ras_rd == $rasId[$i]) || (isset($_POST["kzlRas_$Id"]) && $_POST["kzlRas_$Id"] == $key)){
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
  if ((!isset($_POST['knpVervers_']) && $ooi_db == $mdrStalId[$i]) || (isset($_POST["kzlOoi_$Id"]) && $_POST["kzlOoi_$Id"] == $key)){
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
  if ((!isset($_POST['knpVervers_']) && $hok_rd == $hoknId[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
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

  if ((!isset($_POST['knpVervers_']) && $mom_rd == $momId[$i]) || (isset($_POST["kzlMom_$Id"]) && $_POST["kzlMom_$Id"] == $key)){
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
  if ((!isset($_POST['knpVervers_']) && $red_rd == $redId[$i]) || (isset($_POST["kzlRed_$Id"]) && $_POST["kzlRed_$Id"] == $key)){
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
