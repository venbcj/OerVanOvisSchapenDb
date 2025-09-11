<?php

/*   7-11-2014 gemaakt 
24-3-2015 : login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '4-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J	9-2-2017 : ook where clouse bij vastleggen melddatum  */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '1-1-2020'; /* het pad (file_r) naar FTP variabel gemaakt ipv uit tblLeden gehaald */
$versie = '30-1-2022'; /* Keuze controle en knop melden bij elkaar gezet. Sql beveiligd met quotes */
$versie = '1-4-2022'; /* code binnen save_melding.php werd opgehaald uit responscheck.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '01-01-2024'; /* hd.skip = 0 verwijderd i.v.m. vw_HistorieDm.php aangepast */
$versie = '20-01-2024'; /* Controle melding verplicht gemaakt  */
$versie = '10-03-2024'; /* Als alle regels moeten worden verwijderd kan dit vanaf nu worden verwerkt zonder eerst 1 melding als controle melding te versturen. Verwijderde regels worden bij definitief melden meteen onzichtbaar. De url t.b.v. javascript geactualisserd van http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js naar https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '11-08-2025'; /* Ubn van gebruiker per regel getoond omdat een gebruiker per deze versie meerdere ubn's kan hebben */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Melden Uitval';
$file = "Melden.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (is_logged_in()) {

include "vw_HistorieDm.php";
include "responscheck.php";

if (isset($_POST['knpSave_'])) { /* $code bestaat ook in responscheck.php */ $code = 'DOO';	include "save_melding.php";  header("Location: ".$curr_url); } 

$knptype = "submit"; $vldtype = "text";
// $today = date("Y-m-d"); //$today gedeclareerd in basisfuncties.php  // tbv save_melding.php

// De gegevens van het request
$zoek_oudste_request_niet_definitief_gemeld = mysqli_query($db,"
SELECT min(rq.reqId) reqId, l.relnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (l.lidId = st.lidId)
WHERE h.skip = 0 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rq.dmmeld) and rq.code = 'DOO' 
GROUP BY l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($zoek_oudste_request_niet_definitief_gemeld))
	{	$reqId = $req['reqId']; }
// Einde De gegevens van het request


$aantMeld = aantal_melden($db,$reqId); // Aantal dieren te melden functie gedeclareerd in basisfuncties.php


// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke_uitv($datb,$lidid,$fldReqId,$nestHistorieDm) {

$juistaantal = mysqli_query ($datb,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId) 
 join (
	SELECT schaapId, max(datum) lastdatum 
	FROM (".$nestHistorieDm.") hd 
	WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."'
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and p.ubn is not null	
 and m.skip <> 1
 and h.skip = 0							
");
	if($juistaantal)
	{	$row = mysqli_fetch_assoc($juistaantal);
			return $row['aant'];
	}
	return FALSE;
}
$oke = aantal_oke_uitv($db,$lidId,$reqId,$vw_HistorieDm);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden

// MELDEN
if (isset($_POST['knpMeld_'])) {	include "save_melding.php"; $aantMeld = aantal_melden($db,$reqId); $oke = aantal_oke_uitv($db,$lidId,$reqId,$vw_HistorieDm);
if( $aantMeld > 0 && $oke > 0) {
// Bestand maken
$qry_Leden = mysqli_query($db,"
SELECT alias
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' 
") or die (mysqli_error($db));

	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$alias = $row['alias']; }

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden				
		  
$input_file = $alias."_".$reqId."_request.txt";
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, u.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer, 3 soort, NULL ubn_herk, NULL ubn_best, NULL land_herk, NULL geboortedm, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join ( 
	SELECT schaapId, max(datum) lastdatum
	FROM (".$vw_HistorieDm.") hd 
	WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (s.schaapId = mhd.schaapId)
WHERE rq.reqId = '".mysqli_real_escape_string($db,$reqId)."'
 and h.datum is not null
 and h.datum >= mhd.lastdatum
 and h.datum <= curdate()
 and st.rel_best is not null
 and m.skip <> 1 and h.skip = 0
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
	{	$def = $defi['def'];	}
?>
<form action="MeldUitval.php" method = "post">
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
						  else { if($oke == 1) {$wwoord = 'is';} 	else {$wwoord = 'zijn';} }?>
 <td colspan = 4 width = 500 align = "center" > <b style = "color : red;"><?php if($oke <> $aantMeld) {echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";} ?> </b></td>
 <td></td>
 <td width = 50></td>
 <td></td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <td colspan = 20 height = 20></td>

</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Ubn<hr></th>
 <th>Uitvaldatum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Generatie<hr></th>
 <th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Bericht<hr></th>
 <th></th>

</tr>

<?php
$zoek_meldregels = mysqli_query($db, "
SELECT m.meldId, u.ubn ubn_gebruiker, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, s.levensnummer, s.geslacht, ouder.datum dmaanw, p.ubn ubn_best, st.rel_best, m.skip, m.fout, rs.sucind, mhd.datum datummin, rs.foutmeld

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
 ) ouder on (s.schaapId = ouder.schaapId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
 join (
	SELECT schaapId, max(datum) datum 
	FROM (".$vw_HistorieDm.") hd 
	WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld' GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
 left join (
	SELECT max(respId) respId, levensnummer
	FROM impRespons
	WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."'
	GROUP BY levensnummer
 ) mresp on (mresp.levensnummer = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
WHERE h.skip = 0 and m.reqId = '".mysqli_real_escape_string($db,$reqId)."' and isnull(hide.meldId)
ORDER BY u.ubn, m.skip, if(h.datum < mhd.datum, 1, if(h.datum > curdate(),1,0 )) desc
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($zoek_meldregels))
	{
	$Id = $row['meldId']; 
	$ubn = $row['ubn_gebruiker'];
	$schaapdm = $row['schaapdm'];
	$dmschaap = $row['dmschaap'];
	$levnr = $row['levensnummer'];
	$geslacht = $row['geslacht']; 
	$dmaanw = $row['dmaanw']; 	if(isset($dmaanw)) { if($geslacht == 'ooi') { $fase = 'moederdier';} else if($geslacht == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
	//$ubn_bst = $row['ubn_best'];
	$rel_best = $row['rel_best'];
	$skip = $row['skip'];
	$fout_db = $row['fout'];
	$foutmeld = $row['foutmeld'];
	$sucind = $row['sucind'];
	$dmmin = $row['datummin']; // Laatste datum uit de historie.
		  
if (empty($schaapdm)	|| # datum is leeg
	$dmschaap > $today	||   # uitvaldatum ligt na vandaag
	intval(str_replace('-','',$schaapdm)) == 0 # Van datum naar nummer is 0 of te wel datum = 00-00-0000
) 	 {	$check = 1;	$waarschuwing = ' Dit dier wordt niet gemeld.'; } else { $check = 0; unset($waarschuwing); } 


// Berichtgeving o.b.v. eigen foute registratie
if (isset($fout_db)) { $foutieve_invoer = $fout_db.' '.$waarschuwing; }
// Einde Berichtgeving o.b.v. eigen foute registratie

// Berichtgeving o.b.v. terugkoppeling RVO
if($sucind == 'J' && !isset($foutmeld)) { $bericht = 'RVO meldt : Melding correct'; }
else if(isset($foutmeld)) 				{ $bericht = 'RVO meldt : '.$foutmeld; } 
else if(isset($respId)) 				{ $bericht = 'Resultaat van melding is onbekend'; }
// Einde Berichtgeving o.b.v. terugkoppeling RVO
?>

<!--	**************************************
			**	   OPMAAK  GEGEVENS		**
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
if ($skip == 1) { echo $schaapdm; $vldtype = "hidden"; } ?>
	<input type = <?php echo $vldtype; ?> size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> >
 </td>

 <td align = "center" style = "color : <?php echo $color; ?>;" >	<?php echo $levnr; ?> </td>

 <td align = "center" style = "color : <?php echo $color; ?>;" >	<?php echo $fase; ?>  </td>

 <td width = 50 align = "center">
	<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($skip == 1) ? 'checked' : ''; ?>  >
 </td>
	
 <td width = 600 style = "font-size : 14px;">

<?php 
	if($skip == 1) 				{ $boodschap = "Verwijderd"; 	 $color = "black"; }
elseif(isset($bericht)) 		{ $boodschap = $bericht; 		 $color = "#FF4000"; unset($bericht); }
elseif(isset($foutieve_invoer))	{ $boodschap = $foutieve_invoer; $color = "blue"; 	 unset($foutieve_invoer); /*unset($wrong);*/ } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn
else 							{ $color = 'red';  $boodschap = $waarschuwing; }

if($sucind == 'J' && $skip == 0) { $color = "green"; } // $sucind van laatste response kan J zijn maar inmiddels ook verwijderd.
if(isset($boodschap)) { ?>
	<div style = "color : <?php echo $color; ?>;" > <?php echo $boodschap; } unset($color); unset($boodschap); ?>
	</div>
 </td> 
</tr>
<!--	**************************************
			**	EINDE OPMAAK GEGEVENS	**
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

</body>
</html>
<SCRIPT language="javascript">
$(function(){

	// add multiple select / deselect functionality
	$("#selectall_del").click(function () {
		  $('.delete').attr('checked', this.checked);
	});

	// if all checkbox are selected, check the selectall_del checkbox
	// and viceversa
	$(".delete").click(function(){

		if($(".delete").length == $(".delete:checked").length) {
			$("#selectall_del").attr("checked", "checked");
		} else {
			$("#selectall_del").removeAttr("checked");
		}

	});
});
</SCRIPT>
