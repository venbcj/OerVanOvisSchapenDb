<?php /*   7-11-2014 gemaakt 
24-3-2015 : login toegevoegd */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '4-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J	9-2-2017 : ook where clouse bij vastleggen melddatum  */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '1-1-2020'; /* het pad ($file_r) naar FTP variabel gemaakt ipv uit tblLeden gehaald */

 session_start(); ?>

<html>
<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Melden Uitval';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "Melden.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "vw_HistorieDm.php";
Include "responscheck.php";

if (isset($_POST['knpSave_'])) {	Include "save_melding.php";  header("Location: ".$curr_url); } 

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}
$knptype = "submit"; $vldtype = "text";
$maxdag = date("Y-m-d"); // tbv save_melding.php

// De gegevens van het request
$gegevensRequest = mysqli_query($db,"
SELECT rq.reqId, l.relnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (l.lidId = st.lidId)
WHERE l.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(rq.dmmeld) and rq.code = 'DOO' 
GROUP BY rq.reqId, l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($gegevensRequest))
	{	$reqId = $req['reqId'];
		$relnr = $req['relnr'];	}
// Einde De gegevens van het request

// Aantal dieren te melden 
function aantal_melden($datb,$fldReqId) {
	$aantalmelden = mysqli_query($datb,"SELECT count(*) aant 
										FROM tblMelding m
										 join tblHistorie h on (h.hisId = m. hisId)
										WHERE m.reqId = ".mysqli_real_escape_string($datb,$fldReqId)." and m.skip <> 1 and h.skip = 0 ");//Foutafhandeling zit in return FALSE
		if($aantalmelden)
		{	$row = mysqli_fetch_assoc($aantalmelden);
	            return $row['aant'];
		}
		return FALSE;
}
// Maximum aantal meldingen binnen het request
$max_meldingen = mysqli_query($db,"SELECT count(*) aant 
								FROM tblMelding m
								 join tblHistorie h on (m.hisId = h.hisId)
								WHERE m.reqId = ".$reqId." and m.skip = 0 and h.skip = 0 ");
		while ($maxmelding = mysqli_fetch_assoc($max_meldingen)) {	$maxmeld = $maxmelding['aant'];	}
// Einde Maximum aantal meldingen binnen het request

$aantMeld = aantal_melden($db,$reqId);
// Einde Aantal dieren te melden

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke($datb,$fldReqId,$nestHistorieDm) {
	$juistaantal = mysqli_query ($datb,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m. hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId) 
 join (
	SELECT schaapId, max(datum) datum 
	FROM (".$nestHistorieDm.") hd 
	WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (st.schaapId = mhd.schaapId)
 join tblRelatie r on (r.relId = st.rel_best)
 join tblPartij p on (r.partId = p.partId)
WHERE m.reqId = ".mysqli_real_escape_string($datb,$fldReqId)."
 and h.datum is not null
 and h.datum >= mhd.datum
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
$oke = aantal_oke($db,$reqId,$vw_HistorieDm);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden

// MELDEN
if (isset($_POST['knpMeld_'])) {	Include "save_melding.php"; $aantMeld = aantal_melden($db,$reqId); $oke = aantal_oke($db,$reqId,$vw_HistorieDm);
if( $aantMeld > 0 && $oke > 0) {
// Bestand maken
$qry_Leden = mysqli_query($db,"SELECT ubn, alias FROM tblLeden WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ;") or die (mysqli_error($db)); 
	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$ubn = $row['ubn'];
			$alias = $row['alias']; }

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden				
		  
$input_file = $ubn."_".$alias."_".$reqId."_request.txt";
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, l.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer,
 3 soort, NULL ubn_herk, NULL ubn_best, NULL land_herk, NULL geboortedm, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join ( 
	SELECT schaapId, max(datum) datum
	FROM (".$vw_HistorieDm.") hd 
	WHERE hd.actId != 14 and actie != 'Gevoerd' and actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (s.schaapId = mhd.schaapId)
WHERE rq.reqId = ".mysqli_real_escape_string($db,$reqId)."
 and h.datum is not null
 and h.datum >= mhd.datum
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
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() WHERE reqId = ".mysqli_real_escape_string($db,$reqId)." and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	
		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden"; }
	$goed = "De melding is verstuurd.";
}
	
else if ( $aantMeld == 0 || $oke == 0) { 
// Melddatum registreren in tblRequest bij 0 te melden
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() WHERE reqId = ".mysqli_real_escape_string($db,$reqId)." and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	
		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden";
	$goed = "De schapen zijn verwijderd."; }
		else {
	$goed = "Er is niets te controleren."; }
}
$aantMeld = aantal_melden($db,$reqId);
} // EINDE MELDEN

// Ophalen 'vaststellen' cq 'controle'
$definitief = mysqli_query($db, "SELECT r.def FROM tblRequest r WHERE r.reqId = ".$reqId." " ) or die (mysqli_error($db));

	while($defi = mysqli_fetch_assoc($definitief))
	{	$def = $defi['def'];	}
?>
<form action="MeldUitval.php" method = "post">
<table border = 0>
<tr><td align = "right">Meldingnr : </td><td><?php echo $reqId; ?>

<input type = "hidden" size = 1 name = "txtRequest_" value = <?php echo $reqId ; ?>></td> <!--hiddden-->

<td align = 'right'>
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
	<input type = 'hidden' name = "cntrDef_" size = 1 value = <?php echo $def; ?> > <!-- hiddden -->
</td>
<td>
</td>
<td width = 450 align = "right">Aantal dieren dat u wilt melden : </td><td><?php echo $aantMeld." van de ".$maxmeld; ?></td>
<td></td>
<td><input type = <?php echo $knptype; ?> name = "knpMeld_" value = "Melden"></td> </tr>

<tr><td align = "right">Relatienr&nbsp &nbsp: </td><td><?php echo $relnr; ?></td>
<td width = 180 > </td> </tr>
<tr><td colspan = 10><hr></hr></td></tr>
</table>

<table border = 0 >
<tr> 
<td colspan = 2><input type = <?php echo $knptype; ?> name = "knpSave_" value = "Opslaan"></td>
<?php if($knptype == 'submit') { if($oke == 1) {$wwoord = 'wordt';} else {$wwoord = 'worden';} } 
						  else { if($oke == 1) {$wwoord = 'is';} 	else {$wwoord = 'zijn';} }?>
<td colspan = 4 width = 500 align = center > <b style = "color : red;"><?php if($oke <> $aantMeld) {echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";} ?> </b></td>
<td></td>
<td width = 50></td>
<td></td></tr>
<tr valign = bottom style = "font-size : 12px;">
<td colspan = 20 height = 20></td>

</tr>
<tr valign = bottom style = "font-size : 12px;">
<th>Uitvaldatum<hr></th>
<th>Levensnummer<hr></th>
<th>Generatie<hr></th>
<th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
<th>Bericht<hr></th>
<th></th>

</tr>

<?php
// Include kan niet binnen de loop van $qryMeldregels om dat de functies binnen 'save_melding' dan vaker wordt aangemaakt en dat kan niet. Via phphulp ben ik hier achter gekomen. bron : http://www.phphulp.nl/php/forum/topic/cannot-redeclare-makequote-previously/67477/

$qryMeldregels = mysqli_query($db, "
SELECT m.meldId, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, s.levensnummer, s.geslacht, ouder.datum dmaanw, p.ubn ubn_best, st.rel_best, m.skip, m.fout, rs.sucind, mhd.datum datummin,
case when rs.sucind = 'J' and isnull(rs.foutmeld) then 'RVO meldt : Melding correct'
 when rs.sucind = 'N' and rs. foutmeld is not null then concat('RVO meldt : ' ,rs. foutmeld)
 when rs.respId is not null then 'Resultaat van melding is onbekend'
end bericht

FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
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
	WHERE reqId = ".$reqId."
	GROUP BY levensnummer
 ) mresp on (mresp.levensnummer = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
WHERE m.reqId = ".$reqId." and h.skip = 0
ORDER BY m.skip, if(h.datum < mhd.datum, 1, if(h.datum > curdate(),1,0 )) desc " ) or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryMeldregels))
	{
	$Id = $row['meldId']; 
	$schaapdm = $row['schaapdm'];
	$dmschaap = $row['dmschaap'];
	$levnr = $row['levensnummer'];
	$geslacht = $row['geslacht']; 
	$dmaanw = $row['dmaanw']; 	if(isset($dmaanw)) { if($geslacht == 'ooi') { $fase = 'moederdier';} else if($geslacht == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
	$ubn_bst = $row['ubn_best'];
	$rel_best = $row['rel_best'];
	$skip = $row['skip'];
	$foutieve_invoer = $row['fout'];
	$bericht = $row['bericht'];
	$sucind = $row['sucind'];
	$dmmin = $row['datummin']; // Laatste datum uit de historie.
		  
	 if ( /*(isset($dmpost) && $dmpost < $dmmin) || Deze voorwaarde is hier nvt omdat dmpost niet is opgeslagen in de databas. oorspronkelijke datum wordt weer getoond */
		empty($schaapdm) ||
		empty($levnr)	 ||
		$dmschaap > $maxdag	||
		empty($ubn_best)  ) 	 {	$check = 1;	} else {	$check = 0;	} ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
<td align = center width = 80 style = "color : <?php echo $color; ?>;" > <!--meldId:--> <input type= "hidden" size = 1 <?php echo "name=\"txtId_$Id\" value = $Id"; ?> > <!--hiddden-->
<!-- DATUM -->
<?php if ($skip == 1) { echo $schaapdm; $vldtype = "hidden"; } ?>
<input type = <?php echo $vldtype; ?> size = 9 style = "font-size : 11px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> >
<input type = "hidden" size = 9 style = "font-size : 11px;" name = <?php echo " \"cntrSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> > <!--hiddden-->
<input type = "hidden" size = 9 style = "font-size : 12px;" name = <?php echo " \"minSchaapdm_$Id\" ;"?> value = <?php echo $dmmin; ?> > <!--hiddden-->
</td>

<td align = center style = "color : <?php echo $color; ?>;" >	<?php echo $levnr; ?> 
   <input type = "hidden" name = <?php echo " \"cntrLevnr_$Id\" value = \"$levnr\" ;"?> size = 12 style = "font-size : 9px;"> </td>
<!-- veld cntrLevnr_$Id is noodzakelijk voor variabele $cntrLevnr in Save_Melding.php-->

<td align = center style = "color : <?php echo $color; ?>;" >	<?php echo $fase; ?> 
<input type = "hidden" size = 9 style = "font-size : 12px;" name = <?php echo " \"cntrfase_$Id\" ;"?> value = <?php echo $fase; ?> > </td> <!--hiddden-->

<td align = "center">
<input type = "hidden" size = 1 style = "font-size : 11px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 > <!--hiddden-->

<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($skip == 1) ? 'checked' : ''; ?>  >
</td>
	
<td width = 600 style = "color : red; font-size : 14px;">	

<!-- Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
<?php 
		/*if (empty($schaapdm)  )  { $wrong = " Datum moet zijn gevuld."; } 
		else*/ if ($dmschaap < $dmmin) 	{ $color = "red"; /*$wrong = "De datum mag niet voor ".$mindm." liggen.";*/ } 
		else if ($dmschaap > $maxdag )  { $color = "red"; /*$wrong = "De datum mag niet in de toekomst liggen.";*/ } 
		else { $color = "blue"; } ?>
<!-- EINDE Meldingen bij foutieve waardes --> 
<?php if($skip == 1) 					{ $boodschap = "Verwijderd"; 	$color = "black"; }
	  else if(isset($bericht)) 			{ $boodschap = $bericht; 		$color = "#FF4000"; }
	  else if(isset($foutieve_invoer) )	{ $boodschap = $foutieve_invoer; unset($foutieve_invoer); unset($wrong); } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn

if($sucind == 'J' && $skip == 0) { $color = "green"; } // $sucind van laatste response kan J zijn maar inmiddels ook verwijderd.
if(isset($boodschap)) { ?> <div style = "color : <?php echo $color; ?>;" > <?php echo $boodschap; } unset($color); unset($boodschap); ?></div>
</td> 
</tr>
<!--	**************************************
	**	EINDE OPMAAK GEGEVENS	**
	************************************** -->
<?php 
} ?>
</table>
</form> 

	</TD>
<?php
Include "menu1.php"; } ?>
</tr>

</table>
</center>

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