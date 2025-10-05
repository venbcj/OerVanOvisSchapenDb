<?php

require_once("autoload.php");

$versie = '31-10-2016'; /* : kolom Liquiditeit toegevoegd */
$versie = '5-11-2016'; /* : Totalen toegevoegd */
$versie = '24-12-2016'; /* : Kolomkoppen aangepast */
$versie = '23-03-2017'; /* : Aantal dieren naar boven afgerond dmv ceil()    26-3 : aantal geboren lammeren toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '11-7-2020'; /* € gewijzigd in &euro; 12-7-2020 ë uit database gewijzigd in echo htmlentities(string, ENT_COMPAT,'ISO-8859-1', true); bron https://www.php.net/htmlspecialchars via https://www.phphulp.nl/php/forum/topic/speciale-tekens-in-code-omzetten/50786/ */
$versie = '17-01-2021'; /* Enkele quotes om variabele gezet*/
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 Session::start();
  ?>  
<!DOCTYPE html>
<html>
<head>
<title>Financieel</title>
</head>
<body>

<?php
$titel = 'Saldoberekening';
$file = "";
include "login.php"; ?>

            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { if($modfin == 1) {
    $salber_gateway = new SalberGateway();

require_once "func_euro.php";

$zoek_jaar = $salber_gateway->zoek_jaar($lidId);
while ($maxj = mysqli_fetch_assoc($zoek_jaar)) {
    $maxjaar = $maxj['jaar'];
} if(!isset($maxjaar)) {
$nextjaar = date('Y');
    } else {
        $nextjaar = $maxjaar+1;
    }

if(isset($_POST['kzlJaar_'])) { $kzlJaar = $_POST['kzlJaar_']; } else { $nu_jaar = date('Y'); if(!isset($nu_jaar) || $maxjaar > $nu_jaar) { $kzlJaar = date('Y'); } else { $kzlJaar = $maxjaar; } }
    $day1 = $kzlJaar.'-01-01'; $date1 = date_create($day1); $jan1 = date_format($date1,'Y-m-d');

if(isset($_POST['knpNext_'])) {
    $salber_gateway->insertJaar($lidId, $nextjaar);

    header("Location: ".$url."Saldoberekening.php"); 
    return;
}
if(isset($_POST['knpSave_'])) { include "save_saldoberekening.php"; }

/********    JAARTOTALEN T.B.V. PROGNOSE    ********/ 
$gebrn = $salber_gateway->countGeborenInJaar($lidId, $kzlJaar);
/********    einde     JAARTOTALEN T.B.V. PROGNOSE    einde    ********/ 
/********    JAARTOTALEN T.B.V. REALITEIT    ********/
$schaap_gateway = new SchaapGateway();
    $moe = $schaap_gateway->zoek_ooien_in_jaar($lidId, $kzlJaar);
# NOTE jan1 kun je ook ter plekke berekenen uit kzlJaar
# TODO geldt "op 1 januari" niet? Query doet > jan1
$lamrn = $schaap_gateway->zoek_lammeren_in_jaar($lidId, $kzlJaar, $jan1);

$lam_doo = $schaap_gateway->zoek_aantal_sterfte_lammeren_in_jaar($lidId, $kzlJaar);
    
$mdr_doo = $schaap_gateway->zoek_aantal_sterfte_moeder_in_jaar($lidId, $kzlJaar);
    
$worpn = $schaap_gateway->zoek_worpen_in_jaar($lidId, $kzlJaar);

/********    einde     JAARTOTALEN T.B.V. REALITEIT    einde    ********/ 

// Declaratie kzlJaar
$kzl_jaar = $salber_gateway->jaren($lidId);

$index = 0;
    while ( $kzljr = mysqli_fetch_assoc($kzl_jaar)) 
    {
       $jaarnr[$index] = $kzljr['jaar'];
       $index++; 
    }
// Einde Declaratie kzlJaar ?>
<form action = "Saldoberekening.php" method = 'post'>

<table border = 0 style= "font-size : 14px"> <!-- tabel 1 : Overall tabel -->
<tr>
 <td align =right style= "font-size : 18px"><b>Saldo berekening<b></td>
<?php if(isset($maxjaar)) { ?>
 <td colspan = 5 >
 <!-- KZLJAAR -->
 <select style="width:65;" name= "kzlJaar_" >
<?php    $count = count($jaarnr);    
for ($i = 0; $i < $count; $i++){

    $opties = array($jaarnr[$i]=>$jaarnr[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpToon_']) && $kzlJaar == $jaarnr[$i]) || (isset($_POST["kzlJaar_$Id"]) && $_POST["kzlJaar_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}

 ?> </select>
     <!-- EINDE KZLJAAR -->
    <input type = 'submit' name = 'knpToon_' value = 'Toon'> </td>
 <td align =right width = 82> <?php if($kzlJaar >= Date('Y')-8) { ?><input type = 'submit' name = 'knpSave_' value = 'Opslaan'> <?php } ?> </td>
<?php } ?>
 <td width = 250 align =right> <input type = 'submit' name = 'knpNext_' value = <?php echo $nextjaar."&nbsp&nbsp"."-aanmaken"; ?> > </td>
</tr>
<?php if(isset($maxjaar)) { ?>
<tr>
 <td style= "font-size : 18px"><i>Rekencomponenten</i></td>
 <td colspan = 4 align =right>SaldoBerekening</td>
 <td align =right></td>
 <td width = 75 align =right>Realiteit</td>
</tr>
<tr>
 <td colspan = 5><hr></td>
 <td><hr></td>
 <td><hr></td>
</tr>
<?php // worpaantal zoeken om mee te kunnen rekenen tijdens de prognose
// TODO dit is 1 record, dus kan ook naar de gateway
$zoek_rekencomponenten = $salber_gateway->zoek_rekencomponenten($lidId, $kzlJaar);
while ($el = mysqli_fetch_assoc($zoek_rekencomponenten)) {
    $p_ooital = $el['ooital'];
    $p_dooperc = $el['dooperc'];
    $p_worptal = $el['worptal'];
    $p_worpgr = $el['worpgr'];
}
?>
<tr>
    <td valign = top>
<table border = 0 style= "font-size : 14px">
<tr height = 23>
 <td width = 220>Lammeren productie geboren </td>
 <td align =right width = 70><?php $p_gebrn = $p_ooital*$p_worptal*$p_worpgr; echo $p_gebrn; ?> st.</td>
 <td width = 70></td>
</tr>
<tr height = 23>
 <td>Lammeren productie </td>
 <td align =right><?php $p_afv = $p_gebrn-($p_gebrn*$p_dooperc); echo round($p_afv); ?> st.</td>
 <td width = 70></td>
</tr>
<tr height = 23>
 <td>Grootgebracht per ooi </td>
 <td align =right><?php if($p_ooital == 0) {$p_worpgr_afv = 0;} else {$p_worpgr_afv = round($p_afv/$p_ooital,2); } echo $p_worpgr_afv; ?> st.</td>
 <td width = 70></td>
</tr>
<tr height = 23>
 <td>Percentage verkoop slachtlam</td>
 <td align =right>-100- %</td>
 <td width = 70></td>
</tr>

<tr height = 23>
 <td>Aantal nodig voor vervanging</td>
 <td align =right>-8- %</td>
 <td width = 70></td>
</tr>
<tr height = 23>
 <td>Percentage verkoop ooien</td>
 <td align =right>-3- %</td>
 <td width = 70></td>
</tr>
<tr height = 50> <td></td></tr>
<tr>
 <td colspan = 3>
 <table border = 0>
 <tr>
  <td align =right colspan = 2><b>Saldoberekening</b></td>
  <td align =right><b>Prognose</b></td>
  <td align =right><b>Realiteit</b></td>
 </tr>
<?php

$verv_ooi = $salber_gateway->zoek_element_vervanging_ooi($lidId, $kzlJaar);
if(isset($verv_ooi) && isset($p_ooital) && $p_ooital >0) {

$zoek_jaarbedragen_saldber_prognose_realiteit = $salber_gateway->jaarbasis($lidId, $kzlJaar, $p_ooital, $p_afv, $verv_ooi);

    while( $som = mysqli_fetch_assoc($zoek_jaarbedragen_saldber_prognose_realiteit)) {
        $som_sald = $som['bedrag_slb']; $som_prog = $som['bedrag_liq']; $som_real = $som['bedrag_real'];
        
if(isset($p_ooital) && $p_ooital > 0) { $slb_ooi = round($som_sald/$p_ooital,2); $prg_ooi = round($som_prog/$p_ooital,2); $eff_ooi = round($som_real/$p_ooital,2); }
if(isset($p_afv) && $p_afv > 0)       { $slb_lam = round($som_sald/$p_afv,2);      $prg_lam = round($som_prog/$p_afv,2);       $eff_lam = round($som_real/$p_afv,2); }
}
}
?>
 <tr height = 23 style = "color : blue";>
  <td width = 110><b>Saldo per ooi</b></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($slb_ooi)) { echo euro_format($slb_ooi); } ?></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($prg_ooi)) { echo euro_format($prg_ooi); } ?></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($eff_ooi)) { echo euro_format($eff_ooi); } ?></td>
 </tr>
 <tr height = 23 style = "color : blue";>
  <td width = 110><b>Saldo per lam</b></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($slb_lam)) { echo euro_format($slb_lam); } ?></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($prg_lam)) { echo euro_format($prg_lam); } ?></td>
  <td width = 60 align =right style = "font-size : 15 px";><?php if(isset($eff_lam)) { echo euro_format($eff_lam); } ?></td>
 </tr>
 </table>
 </td>
</tr>
</table> 
    </td>
    <td colspan = 6 align =right valign="top">
<table border = 0 style= "font-size : 14px">

<tr>
 <td> Aantal geboren lammeren</td>
 <td colspan=2></td>
 <td align="right"><?php if(isset($gebrn)) { echo $gebrn.' st'; } ?></td>
</tr>
<?php 
$zoek_element = $salber_gateway->zoek_element($lidId, $kzlJaar);
    while ($el = mysqli_fetch_assoc($zoek_element)) { // START LOOP COMPONENTEN
     $Id = $el['salbId']; $elemt = $el['element']; $waarde_eu = $el['waarde']; $elemId = $el['elemId']; $eenh = $el['eenheid'];
    
    $eenheid_voor = array(''=>' ','euro'=>'&euro;','getal'=>'&nbsp&nbsp','procent'=>'&nbsp&nbsp');
    $eenheid_achter = array(''=>'','euro'=>'','getal'=>' st','procent'=>'%');
    
// Realiteit en prognose ophalen per component 
    // TODO: constanten maken voor de waarden van $elemId, zodat het commentaar weg kan
if($elemId == 1 && $kzlJaar <= date('Y')) { /* 1 = Aantal ooien*/ $real = $mdrs; }
 
if($elemId == 12 && $kzlJaar <= date('Y')) { /* 12 = percentage uitval lammeren*/ if(isset($lamrn) && $lamrn > 0) { $real = round($lam_doo/$lamrn*100,2); } }
if($elemId == 12)                             { /* 12 = percentage uitval lammeren*/ $p_dooperc = $waarde_eu/100; } 

if($elemId == 13 && $kzlJaar <= date('Y')) { /* 13 = percentage uitval ooien*/ if(isset($mdrs) && $mdrs > 0) { $real = round($mdr_doo/$mdrs*100,2); }}
 
if($elemId == 16 && $kzlJaar <= date('Y')) { /* 16 = percentage vervanging ooien*/ if(isset($mdrs) && $mdrs > 0) { $real = round($mdr_doo/$mdrs*100,2); }}
if($elemId == 16)                             { /* 16 = percentage vervanging ooien*/ $p_dooperc_mdr = $waarde_eu/100; } 
 
if($elemId == 18 && $kzlJaar <= date('Y')) { /* 18 = Aantal worpen*/ $real = $worpn; }
 
if($elemId == 19 && $kzlJaar <= date('Y')) { /* 19 = Worpgrootte*/ if($worpn == 0) { $real = 0; } else { $real = round($gebrn/$worpn,2); } }
// Einde Realiteit en prognose ophalen per component 

if(isset($oud_eenh) && $oud_eenh <> $eenh) { ?> 
<tr>
 <td colspan = 2><hr> </td> 
</tr> <?php } ?>


<tr>
 
 <td width = 180><?php if(isset($elemt)) { echo $elemt; } ?> </td>
 <td width = 80 ><?php echo $eenheid_voor[$eenh].' '; ?><input type = text name = <?php echo "txtElem_$Id"; ?> size = 3 style= "font-size : 11px; text-align : right;" value = <?php echo $waarde_eu; ?> >
 <?php echo $eenheid_achter[$eenh]; ?> </td>
 <td width = 80 align =right></td>
 <td width = 80 align =right>
  <?php if(isset($real)) {
  echo $eenheid_voor[$eenh];
  echo $real; unset($real); 
  echo $eenheid_achter[$eenh];
  } ?> </td>
  
 
</tr>
<?php if(isset($eenh)) { $oud_eenh = $eenh; } } // EINDE LOOP COMPONENTEN ?>
</table>
 </td>
 </tr>
    <tr> <td colspan = 7><hr SIZE="5" NOSHADE> </td> </tr>
    <tr> <td>
<table border = 0>

</table>
    </td>
    <td colspan = 5 align =right>
<table border = 0 style= "font-size : 14px">

</table>
    </td> </tr>


<?php
  // TODO uit de loop tillen
  $rubriek_gateway = new RubriekGateway();
  $zoek_HfdRubriek = $rubriek_gateway->zoekHoofdrubriek($lidId);
    while ($rh = mysqli_fetch_assoc($zoek_HfdRubriek)) { $rubhId = $rh['rubhId']; $rubriek_h = $rh['rubriek']; ?>

<tr height = 50 > <td></td></tr>
<tr>
 <td style= "font-size : 18px"><b><?php if(isset($rubriek_h)) { echo htmlentities($rubriek_h, ENT_COMPAT,'ISO-8859-1', true); } ?><b></td>
 <td colspan = 4 align =right>Saldoberekening</td>
 <td align =right>Prognose</td>
 <td align =right>Realiteit</td>
</tr>
<tr>
 <td colspan = 5><hr></td>
 <td><hr></td>
 <td><hr></td>
</tr>
<tr>
 <td></td>
 <td> <?php if($rubhId == 6 /* voerkosten*/ ) { ?>kg/dier<?php } ?></td>
 <td align = "center"> <?php if($rubhId == 2 || $rubhId == 5 || $rubhId == 6 /* voerkosten*/ ) { ?>dieren<?php } ?> </td>
 <td align = "center"> <?php if($rubhId == 2 || $rubhId == 5 || $rubhId == 6 /* voerkosten*/ ) { ?>prijs/stuk<?php } else { ?>prijs<?php } ?> </td>
 <td align = "center"> Totaal </td>
</tr>
<?php // LOOP Rubrieken
$zoek_Rubriek = $rubriek_gateway->zoekRubriek($lidId, $rubhId, $kzlJaar);

while ($rub = mysqli_fetch_assoc($zoek_Rubriek)) {
    $Id = $rub['salbId']; $credeb = $rub['credeb']; $rubId = $rub['rubId']; $rubuId = $rub['rubuId']; $rubriek = $rub['rubriek']; $hoev_ru = $rub['hoev']; $waarde_ru = $rub['waarde']; $rubliq = $rub['bedrag_liq']; $rubreal = $rub['bedrag_real'];

if(!isset($waarde_ru)) { $waarde_ru = 0; }
$rubliq_eur = euro_format($rubliq); 
$rubreal_eur = euro_format($rubreal);
if(isset($subliq)) { $subliq = $subliq+$rubliq; } else { $subliq = $rubliq;} //echo $subliq.'<br>';
if(isset($subreal)) { $subreal = $subreal+$rubreal; } else { $subreal = $rubreal;}

// Toelichting 7 opties zie vw_Saldober_Jaarbasis.php 

//Benodigde variabelen voor Optie
if($rubId == 51 && !isset($hoev_ru)) { $hoev_ru = 0; }

//Benodigde variabelen voor Optie3
if($rubId == 10 || $rubId == 11 || $rubId == 18 || $rubId == 25 || $rubId == 32 || $rubId == 46 || $rubId == 49 || $rubId == 50) { $diern_o = $p_ooital; }

//Benodigde variabelen voor Optie4
if($rubId == 16 || $rubId == 19 || $rubId == 44)  { $diern_o = $p_ooital; if(!isset($hoev_ru)) { $hoev_ru = 0; } }

//Benodigde variabelen voor Optie5
if($rubId == 13 || $rubId == 36 || $rubId == 39)  { $diern_l = $p_afv; }

//Benodigde variabelen voor Optie6
if($rubId == 15 || $rubId == 17 || $rubId == 48)  { $diern_l = $p_afv; if(!isset($hoev_ru)) { $hoev_ru = 0; } }

//Benodigde variabelen voor Optie7
unset($diern_verv);
if($rubId == 1 || $rubId == 40) { $diern_verv = $p_ooital*$p_dooperc_mdr; } // Aantl t.b.v. Aankoop moederdier en Verkoop moederdier 


/*optie 1*/    
 if(!isset($diern_o) && !isset($diern_l) && !isset($diern_verv) && !isset($hoev_ru)) { $rubprog = $waarde_ru;                 $optie = 'optie1';}

/*optie 2*/ 
else if(!isset($diern_o) && !isset($diern_l) && !isset($diern_verv) && isset($hoev_ru)) { $rubprog = $hoev_ru*$waarde_ru;         $optie = 'optie2';}

/*optie 3*/
else if(isset($diern_o) && !isset($hoev_ru)) { $rubprog = $diern_o*$waarde_ru; $diern = $diern_o; unset($diern_o);                 $optie = 'optie3';}

/*optie 4*/
else if(isset($diern_o) && isset($hoev_ru)) { $rubprog = $diern_o*$hoev_ru*$waarde_ru; $diern = $diern_o; unset($diern_o);         $optie = 'optie4';}

/*optie 5*/
else if(isset($diern_l) && !isset($hoev_ru)) { $rubprog = $diern_l*$waarde_ru; $diern = $diern_l; unset($diern_l);                 $optie = 'optie5';}

/*optie 6*/
else if(isset($diern_l) && isset($hoev_ru)) { $rubprog = $diern_l*$hoev_ru*$waarde_ru; $diern = $diern_l; unset($diern_l);         $optie = 'optie6';}

/*optie 7*/
else if(isset($diern_verv) && !isset($hoev_ru)) { $rubprog = $diern_verv*$waarde_ru; $diern = $diern_verv; 
            $optie = 'optie7';}

/*optie 8*/
else { $rubprog = $waarde_ru+9999999; $optie = 'optie8'; }

 

    $rubprog_eur = euro_format($rubprog);
if(isset($subprog)) { $subprog = $subprog+$rubprog; } else { $subprog = $rubprog;}
     if (isset($subprog_dc) && $credeb == 'c') { $subprog_dc = $subprog_dc-$rubprog; } else if(!isset($subprog_dc) && $credeb == 'c') { $subprog_dc = -$rubprog;}
else if (isset($subprog_dc) && $credeb == 'd') { $subprog_dc = $subprog_dc+$rubprog; } else if(!isset($subprog_dc) && $credeb == 'd') { $subprog_dc = $rubprog;}
     if (isset($subliq_dc) && $credeb == 'c') { $subliq_dc = $subliq_dc-$rubliq; } else if(!isset($subliq_dc) && $credeb == 'c') { $subliq_dc = -$rubliq;}
else if (isset($subliq_dc) && $credeb == 'd') { $subliq_dc = $subliq_dc+$rubliq; } else if(!isset($subliq_dc) && $credeb == 'd') { $subliq_dc = $rubliq;}
     if (isset($subreal_dc) && $credeb == 'c') { $subreal_dc = $subreal_dc-$rubreal; } else if(!isset($subreal_dc) && $credeb == 'c') { $subreal_dc = -$rubreal;}
else if (isset($subreal_dc) && $credeb == 'd') { $subreal_dc = $subreal_dc+$rubreal; } else if(!isset($subreal_dc) && $credeb == 'd') { $subreal_dc = $rubreal;}
    /*echo $rubriek.' $rubprog = '.$rubprog.' $subprog_dc = '.$subprog_dc.'<br>'; */
    /*echo $rubriek.' $rubliq = '.$rubliq.' $rubliq_dc = '.$subliq_dc.'<br>'; */
    /*echo $rubriek.' $rubreal = '.$rubreal.' $rubreal_dc = '.$subreal_dc.'<br>';*/ 
?>
<tr>
 <td><?php echo /*$rubId.'-'.*/ $rubriek /*.' - '.$optie*/ ?></td>
 <td align ="center">
  <?php if($rubhId == 6) { ?>
 <input type = text name = <?php echo "txtRubat_$Id"; ?> size = 3 style= "font-size : 11px; text-align : right;" value = <?php if($hoev_ru > 0) { echo $hoev_ru; } ?> > <?php } ?>
 </td>
 <td align = "center">
  <?php if($rubId == 51) { // aantal aankoop vaderdieren handmatig in te vullen ?>
 <input type = text name = <?php echo "txtRubat_$Id"; ?> size = 3 style= "font-size : 11px; text-align : center;" value = <?php if($hoev_ru > 0) { echo str_replace('.00', '', $hoev_ru); } ?> > <?php }
   if(isset($diern) && $diern > 0 ) { echo ceil($diern); } unset($diern); ?>
 </td>
 <td>&euro; <input type = text name = <?php echo "txtRubriek_$Id"; ?> size = 3 style= "font-size : 11px; text-align : right;" value = <?php echo $waarde_ru; ?> ></td>
 <td align =right><?php if(isset($rubprog_eur)) { echo $rubprog_eur; } unset($rubprog_eur); ?></td>
 <td align =right><?php if($rubliq >0) { echo $rubliq_eur; } ?> </td>
 <td align =right><?php if($rubreal >0) { echo $rubreal_eur; } ?> </td>
</tr>
<?php unset($hoev_ru); } ?>
<tr>
 <td colspan = 4 align =right valign =bottom><b><?php echo "Totaal ".htmlentities($rubriek_h, ENT_COMPAT,'ISO-8859-1', true); ?></b></td>
 <!-- Subtotalen Rubrieken -->
 <td align =right><hr><b><?php if(isset($subprog)) { echo euro_format($subprog); $subprog = 0; } ?></b></td>
 <td align =right><hr><b><?php if(isset($subliq)) { echo euro_format($subliq); unset($subliq); } else { echo euro_format(0); unset($subliq); } ?></b></td>
 <td align =right><hr><b><?php if(isset($subreal)) { echo euro_format($subreal); unset($subreal); } ?></b></td>
</tr>
<?php } ?>


<tr>
 <td colspan = 4 align =right><b> TOTAAL opbrengsten -/- kosten &nbsp&nbsp&nbsp </b></td>
 <td align =right><hr><hr><b> <?php if(isset($subprog_dc)) { echo euro_format($subprog_dc); } ?> </b><hr></td>
 <td align =right><hr><hr><b> <?php if(isset($subliq_dc))  { echo euro_format($subliq_dc); } ?> </b><hr></td>
 <td align =right><hr><hr><b> <?php if(isset($subreal_dc)) { echo euro_format($subreal_dc); } ?> </b><hr></td>
</tr>
<tr height = 5 > <td></td></tr>
<tr style = "color : blue">
 <td colspan = 4 align =right valign =top><hr><b> Saldo per ooi &nbsp&nbsp&nbsp </b></td>
 <td align =right><hr><b> <?php if(isset($subprog_dc) && isset($p_ooital) && $p_ooital>0) { echo euro_format($subprog_dc/$p_ooital); } ?> </b><hr></td>
 <td align =right><hr><b> <?php if(isset($subliq_dc)  && isset($p_ooital) && $p_ooital>0) { echo euro_format($subliq_dc/$p_ooital); } ?> </b><hr></td>
 <td align =right><hr><b> <?php if(isset($subreal_dc) && isset($p_ooital) && $p_ooital>0) { echo euro_format($subreal_dc/$p_ooital); } ?> </b><hr></td>
</tr>
<tr style = "color : blue">
 <td colspan = 4 align =right valign =top><b> Saldo per lam &nbsp&nbsp&nbsp </b></td>
 <td align =right><b> <?php if(isset($subprog_dc) && isset($p_afv) && $p_afv>0) { echo euro_format($subprog_dc/$p_afv); } ?> </b><hr></td>
 <td align =right><b> <?php if(isset($subliq_dc)  && isset($p_afv) && $p_afv>0) { echo euro_format($subliq_dc/$p_afv); } ?> </b><hr></td>
 <td align =right><b> <?php if(isset($subreal_dc) && isset($p_afv) && $p_afv>0) { echo euro_format($subreal_dc/$p_afv); } ?> </b><hr></td>
</tr>
<tr height = 50 > <td></td></tr>


<?php } ?>

</table>





</form>
    </TD>
<?php } else { ?> <img src='saldoberekening_php.jpg'  width='970' height='2850'> <?php }
include "menuFinance.php"; } ?>
</tr>

</table>

</body>
</html>
