<?php

require_once("autoload.php");


$versie = '4-7-2020'; /* gekopieerd van MeldAanvoer.php */
$versie = '26-9-2020'; /* Aangepast op 14-8 na.v. contact met Bright */
$versie = '30-1-2022'; /* Keuze controle en knop melden bij elkaar gezet. Sql beveiligd met quotes */
$versie = '1-4-2022'; /* code binnen save_melding.php werd opgehaald uit responscheck.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '20-01-2024'; /* Controle melding verplicht gemaakt  */
$versie = '10-03-2024'; /* Als alle regels moeten worden verwijderd kan dit vanaf nu worden verwerkt zonder eerst 1 melding als controle melding te versturen. Verwijderde regels worden bij definitief melden meteen onzichtbaar. De url t.b.v. javascript geactualisserd van http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js naar https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '11-08-2025'; /* Ubn van gebruiker toegevoegd omdat een gebruiker per deze versie meerdere ubn's kan hebben */

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
$titel = 'Melden Omnummeren';
$file = "Melden.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {

include "responscheck.php";

if (isset($_POST['knpSave_'])) {
    $code = 'VMD';
    include "save_melding.php";
    Response::redirect($curr_url);
    return;
}

$knptype = "submit";
$today = date("Y-m-d");

// De gegevens van het request
$reqId = 0;
$zoek_oudste_request_niet_definitief_gemeld = mysqli_query($db,"
SELECT min(rq.reqId) reqId, l.relnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (l.lidId = st.lidId)
WHERE h.skip = 0 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rq.dmmeld) and rq.code = 'VMD' 
GROUP BY l.relnr
") or die (mysqli_error($db));
    While ($req = mysqli_fetch_assoc($zoek_oudste_request_niet_definitief_gemeld))
    {    $reqId = $req['reqId'];    }
// Einde De gegevens van het request

$aantMeld = aantal_melden($db,$reqId); // Aantal dieren te melden functie gedeclareerd in basisfuncties.php
// Einde Aantal dieren te melden

$melding_gateway = new MeldingGateway($db);

$oke = $melding_gateway->aantal_oke_Omnum($reqId);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden.
 
// MELDEN
if (isset($_POST['knpMeld_'])) {    include "save_melding.php"; $aantMeld = aantal_melden($db,$reqId); $oke = $melding_gateway->aantal_oke_Omnum($reqId);
if( $aantMeld > 0 && $oke > 0) {
// Bestand maken
$qry_Leden = mysqli_query($db,"
SELECT alias
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

    while ($row = mysqli_fetch_assoc($qry_Leden))
        {    $alias = $row['alias'];    } 

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden
          
$input_file = $alias."_".$reqId."_request.txt"; // Bestandsnaam
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, u.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, h.oud_nummer, 3 soort,
 'NL' land_new, s.levensnummer, NULL land_herk, NULL gebDatum, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join tblRelatie rl on (rl.relId = st.rel_herk)
 
WHERE rq.reqId = '".mysqli_real_escape_string($db,$reqId)."'
 and h.skip = 0
 and h.datum is not null
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and m.skip <> 1
 and isnull(m.fout) 
") or die (mysqli_error($db));

    while ($row = mysqli_fetch_array($qry_txtRequest_RVO)) {          
        $num = mysqli_num_fields($qry_txtRequest_RVO) ;    
        $last = $num - 1;
        for($i = 0; $i < $num; $i++) {            
            fwrite($fh, $row[$i]);                       
            if ($i != $last) {
                fwrite($fh, ";");
            }
        }                                                                 
        fwrite($fh, PHP_EOL);
    }
    fclose($fh);
    
// Melddatum registreren in tblRequest bij > 0 te melden en definitieve melding
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' and def = 'J' ";
    mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
    
        if($_POST['kzlDef_'] == 'J'){
    $knptype = "hidden"; }
    $goed = "De melding is verstuurd.";
}

else if ( $aantMeld == 0 || $oke == 0) {
// Melddatum registreren in tblRequest bij 0 te melden
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now(), def = 'J' WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' ";
    mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
    
        if($_POST['kzlDef_'] == 'J' || $aantMeld == 0){
    $knptype = "hidden";
    $goed = "De schapen kunnen handmatig worden gemeld."; }
        else {
    $goed = "Er is niets te controleren."; }
}
$aantMeld = aantal_melden($db,$reqId);
} // EINDE MELDEN

// Ophalen 'vaststellen' cq 'controle'
$definitief = mysqli_query($db, "
SELECT r.def 
FROM tblRequest r 
WHERE r.reqId = '".mysqli_real_escape_string($db,$reqId)."' 
") or die (mysqli_error($db));

    while($defi = mysqli_fetch_assoc($definitief))
    {    $def = $defi['def'];    }
?>
<form action="MeldOmnummer.php" method = "post">
<table border = 0>
<tr>
 <td align = "right">Meldingnr : </td>
 <td>
     <?php echo $reqId; ?>
 </td>
 <td width = 850 align = "right">Aantal dieren te melden : </td>
 <td><?php echo $aantMeld; ?></td>
</tr>

<tr>
 <td colspan="3" align = 'right'>

<?php $zoekControle = zoek_controle_melding($db,$reqId); 
if(isset($zoekControle) && $zoekControle > 0 && $aantMeld > 0) { /* Als er een controlemelding is gedaan en er zijn schapen te melden */ ?>

    <!-- KZLDefinitief --> 
    <select <?php echo "name=\"kzlDef_\" "; ?> style = "width:100; font-size:13px;">
    <?php
    $opties = array('N'=>'Controle', 'J'=>'Vastleggen');
    foreach ( $opties as $key => $waarde)
    {
       if((!isset($_POST['knpSave_']) && $def == $key) || (isset($_POST["kzlDef_"]) && $_POST["kzlDef_"] == $key) ) {
        echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
      } else {
        echo '<option value="' . $key . '">' . $waarde . '</option>';
      }
    } ?> 
    </select> <!-- EINDE KZLDefinitief -->

<?php } else if ($aantMeld > 0) { echo 'Controle '; } /* Als er geen controlemelding is gedaan en er zijn schapen te melden. Anders zijn er geen dieren te melden en alleen te verwijderen */ ?> &nbsp &nbsp
 </td>
 <td>
<?php if($aantMeld == 0) { ?>
     <input type = <?php echo $knptype; ?> name = "knpMeld_" value = "Verwijderen">
<?php } else { ?>
     <input type = <?php echo $knptype; ?> name = "knpMeld_" value = "Melden">
<?php } ?>
 </td>
</tr>
<tr>
 <td colspan = 10><hr></hr></td>
</tr>
</table>

<table border = 0 >
<tr> 
 <td colspan = 2><input type = <?php echo $knptype; ?> name = "knpSave_" value = "Opslaan"></td> 
<?php if($knptype == 'submit') { if($oke == 1) {$wwoord = 'wordt';} else {$wwoord = 'worden';} } 
                          else { if($oke == 1) {$wwoord = 'is';}     else {$wwoord = 'zijn';} }?>
 <td colspan = 4 width = 500 align = "center" > <b style = "color : blue;"><?php if($oke <> $aantMeld) {echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";} ?> </b></td>
 <td></td>
 <td width = 50></td>
 <td></td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <td colspan = 20 height = 20></td>

</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Ubn<hr></th>
 <th>Datum<hr></th>
 <th>Levensnummer oud<hr></th>
 <th>Levensnummer nieuw<hr></th>
 <th>Generatie<hr></th>
 <th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Bericht<hr></th>
 <th></th>

</tr>

<?php
$zoek_meldregels = mysqli_query($db, "
SELECT m.meldId, u.ubn ubn_gebruiker, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, h.oud_nummer, s.levensnummer, s.geslacht, ouder.datum dmaanw, st.stalId, m.skip, m.fout, rs.respId, rs.sucind, rs.foutmeld, lastdm.datum dmlst, date_format(lastdm.datum,'%d-%m-%Y') lstdm
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join (
     SELECT m.meldId, NULL BijDefinitiefMeldenVerwijderdenNietTonen
     FROM tblMelding m
     join tblRequest r on (r.reqId = m.reqId)
     WHERE m.reqId = '".mysqli_real_escape_string($db,$reqId)."' and m.skip = 1 and r.def = 'J' and dmmeld is not null
 ) hide on (hide.meldId = m.meldId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId )
 left join (
    SELECT max(respId) respId, levensnummer_new
    FROM impRespons
    WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."'
    GROUP BY levensnummer_new
 ) mresp on (mresp.levensnummer_new = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
 left join (
    SELECT st.schaapId, max(datum) datum 
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
    WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and 
     not exists (SELECT max(stl.stalId) stalId FROM tblStal stl WHERE stl.lidId = '".mysqli_real_escape_string($db,$lidId)."' and stl.stalId = st.stalId)
    GROUP BY st.schaapId
 ) lastdm on (lastdm.schaapId = s.schaapId)
WHERE h.skip = 0 and m.reqId = '".mysqli_real_escape_string($db,$reqId)."' and isnull(hide.meldId)
ORDER BY u.ubn, m.skip 
") or die (mysqli_error($db));

    while($row = mysqli_fetch_assoc($zoek_meldregels))
    {
    $Id = $row['meldId'];
    $ubn = $row['ubn_gebruiker'];
    $schaapdm = $row['schaapdm'];
    $dmschaap = $row['dmschaap'];
    $levnr_old = $row['oud_nummer'];
    $levnr = $row['levensnummer'];
    $geslacht = $row['geslacht']; 
    $dmaanw = $row['dmaanw'];     if(isset($dmaanw)) { if($geslacht == 'ooi') { $fase = 'moederdier';} else if($geslacht == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
    $stalId = $row['stalId']; // Ter controle van eerdere stalId's
    $skip = $row['skip'];
    $dmmeld = $row['dmmeld'];
    $fout_db = $row['fout'];
    $foutmeld = $row['foutmeld'];
    $respId = $row['respId'];
    $sucind = $row['sucind'];        
    $dmlst = $row['dmlst']; // Laatste datum van het vorige stalId van deze user
    $lstdm = $row['lstdm']; // t.b.v. commentaar
    

// Controleren of de te melden gegevens de juiste voorwaarde hebben .
if    ( 
    empty($schaapdm)                        || # datum is leeg
    empty($levnr)                             || # levensnummer is leeg
    $dmschaap > $today                         || # datum ligt na vandaag
    (isset($dmlst) && $dmschaap < $dmlst)    || # datum ligt voor de laatste datum van het vorige stalId van deze user 
    intval(str_replace('-','',$schaapdm)) == 0 # Van datum naar nummer is 0 of te wel datum = 00-00-0000. Als $dmlst niet bestaat !
)
     {    $check = 1;    $waarschuwing = ' Dit dier wordt niet gemeld.'; } else { $check = 0; unset($waarschuwing); } 
// EINDE Controleren of de te melden gegevens de juiste voorwaarde hebben . 

// Berichtgeving o.b.v. eigen foute registratie
if (isset($fout_db)) { $foutieve_invoer = $fout_db.' '.$waarschuwing; }
// Einde Berichtgeving o.b.v. eigen foute registratie

// Berichtgeving o.b.v. terugkoppeling RVO
if($sucind == 'J' && !isset($foutmeld)) { $bericht = 'RVO meldt : Melding correct'; }
elseif(isset($foutmeld))                 { $bericht = 'RVO meldt : '.$foutmeld; }
elseif(isset($respId))                     { $bericht = 'Resultaat van melding is onbekend'; }
// Einde Berichtgeving o.b.v. terugkoppeling RVO
?>

<!--    **************************************
            **       OPMAAK  GEGEVENS        **
        ************************************** -->
<?php
if(isset($vorig_ubn) && $vorig_ubn != $ubn) { ?>
<tr><td colspan="15"><hr></td></tr>
<?php
    } ?>

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<?php echo $ubn; ?>
 </td>
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<!-- DATUM -->
<?php //echo $Id;
if ($skip == 1) { echo $schaapdm; }
else { ?>
    <input type = text size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> >
<?php } ?>
 </td>

 <td align="center" style = "color : <?php echo $color; ?>;" >
<?php echo $levnr_old;  ?>
 </td>

 <td align="center" style = "color : <?php echo $color; ?>;" >
<?php
if ($skip == 1) { echo $levnr; } 
else { ?> 
    <input type = text name = <?php echo " \"txtLevnr_$Id\"; " ?> value = <?php echo $levnr; ?> size = 15 style = "font-size : 12px;"> 
<?php } ?>
 </td>

 <td align = "center" style = "color : <?php echo $color; ?>;" >
<?php echo $fase; ?>
 </td>

 <td  width = 50 align = "center">
    <input type = "hidden" size = 1 style = "font-size : 11px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 > <!--hiddden-->

    <input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($check == 1 || $skip == 1) ? 'checked' : ''; if ($check == 1) { ?> disabled <?php } ?>  >
 </td>

 <td width = 400 style = "color : red; font-size : 12px;">        

<?php
    if($skip == 1)                     { $boodschap = "Verwijderd";      $color = "black"; }
elseif(isset($bericht))             { $boodschap = $bericht;          $color = "#FF4000"; unset($bericht); }
elseif(isset($foutieve_invoer) )    { $boodschap = $foutieve_invoer; $color = "blue"; unset($foutieve_invoer); /*unset($wrong);*/ } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn 
else                                 { $color = 'red';  $boodschap = $waarschuwing; } 

if($sucind == 'J' && $skip == 0) { $color = "green"; } // $sucind van laatste response kan J zijn maar inmiddels ook verwijderd.
if(isset($boodschap)) { ?>
    <div style = "color : <?php echo $color; ?>;" > <?php echo $boodschap; } unset($color); unset($boodschap); ?>
    </div>
 </td> 
</tr>
<!--    **************************************
            **    EINDE OPMAAK GEGEVENS    **
        ************************************** -->
<?php 
$vorig_ubn = $ubn;
} ?>    
</table>
</form> 

    </TD>
<?php
include "menuMelden.php"; } ?>
</tr>

</table>
<?php
include "select-all.js.php";
?>

</body>
</html>
