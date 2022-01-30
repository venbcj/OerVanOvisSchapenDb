<?php 
$versie = '4-7-2020'; /* gekopieerd van MeldAanvoer.php */
$versie = '26-9-2020'; /* Aangepast op 14-8 na.v. contact met Bright */
$versie = '30-1-2022'; /* Keuze controle en knop melden bij elkaar gezet. Sql beveiligd met quotes */

 session_start(); ?>

<html>
<head>
	<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<center>
<?php
$titel = 'Melden Omnummeren';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "Melden.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

Include "responscheck.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
} // toegepast in save_melding.php

if (isset($_POST['knpSave_'])) {	Include "save_melding.php";  header("Location: ".$curr_url); }

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
WHERE l.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rq.dmmeld) and rq.code = 'VMD' 
GROUP BY rq.reqId, l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($gegevensRequest))
	{	$reqId = $req['reqId'];	}
// Einde De gegevens van het request

// Aantal dieren te melden 
function aantal_melden($datb,$fldReqId) {

$aantalmelden = mysqli_query($datb,"
SELECT count(*) aant 
FROM tblMelding m
WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."' and m.skip <> 1
"); // Foutafhandeling zit in return FALSE
	if($aantalmelden)
	{	$row = mysqli_fetch_assoc($aantalmelden);
			return $row['aant'];
	}
	return FALSE;
}
$aantMeld = aantal_melden($db,$reqId);
// Einde Aantal dieren te melden

// Aantal dieren goed geregistreerd om automatisch te kunnen melden. De datum mag hier niet liggen na de afvoerdatum.
function aantal_oke($datb,$fldReqId,$fldFout) {

$juistaantal = mysqli_query ($datb,"
SELECT count(*) aant 
FROM tblMelding m
 join tblHistorie h on (h.hisId = m. hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT schaapId, max(datum) datum 
	FROM tblHistorie h 
	 join tblStal st on (h.stalId = st.stalId)
	 join tblActie a on (h.actId = a.actId)
	WHERE a.af = 1
	GROUP BY schaapId
 ) afv on (st.schaapId = afv.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."'
 and h.datum is not null
 and (h.datum <= afv.datum or isnull(afv.datum))
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
 and m.skip <> 1
 and ".$fldFout." 
");

	if($juistaantal)
	{	$row = mysqli_fetch_assoc($juistaantal);
			return $row['aant'];
	}
	return FALSE;
}
$vldFout = '(isnull(fout) or fout is not null)';
$oke = aantal_oke($db,$reqId,$vldFout);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden.
 
// MELDEN
if (isset($_POST['knpMeld_'])) {	Include "save_melding.php"; $vldFout = 'isnull(fout)'; $oke = aantal_oke($db,$reqId,$vldFout);
if(aantal_melden($db,$reqId) > 0 && $oke > 0) {
// Bestand maken
$qry_Leden = mysqli_query($db,"
SELECT ubn, alias
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$ubn = $row['ubn'];
			$alias = $row['alias'];	} 

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden
		  
$input_file = $ubn."_".$alias."_".$reqId."_request.txt"; // Bestandsnaam
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, l.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, h.oud_nummer, 3 soort,
 'NL' land_new, s.levensnummer, NULL land_herk, NULL gebDatum, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join tblStal st_all on (s.schaapId = st_all.schaapId)
 left join tblRelatie rl on (rl.relId = st.rel_herk)
 
WHERE rq.reqId = '".mysqli_real_escape_string($db,$reqId)."'
	and h.datum is not null
	and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
	and m.skip <> 1
	and isnull(m.fout) 
") or die (mysqli_error($db));   /* Herkomst (ubn_herk) is niet verplicht te melden */
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
	
// Melddatum registreren in tblRequest bij > 0 te melden
 $upd_tblRequest = "UPDATE tblRequest set dmmeld = now() WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	
		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden"; }
	$goed = "De melding is verstuurd.";
}

else if (aantal_melden($db,$reqId) == 0 || $oke == 0) {
// Melddatum registreren in tblRequest bij 0 te melden
 $upd_tblRequest = "UPDATE tblRequest set dmmeld = now() WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	
		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden"; }
	$goed = "De schapen kunnen handmatig worden gemeld.";
}
$Melddm = 'dmmeld is not null'; $ReqId = "reqId = $reqId";
$aantMeld = aantal_melden($db,$reqId);
} // EINDE MELDEN

// Ophalen vaststellen cq controle
$definitief = mysqli_query($db, "
SELECT r.def 
FROM tblRequest r 
WHERE r.reqId = '".mysqli_real_escape_string($db,$reqId)."' 
") or die (mysqli_error($db));

	while($defi = mysqli_fetch_assoc($definitief))
	{	$def = $defi['def'];	}
?>
<form action="MeldOmnummer.php" method = "post">
<table border = 0>
<tr>
 <td align = "right">Meldingnr : </td>
 <td><?php echo $reqId; ?> </td>
 <td width = 850 align = "right">Aantal dieren te melden : </td>
 <td><?php echo $aantMeld; ?></td>
</tr>

<tr>
 <td align = "right">Ubn &nbsp &nbsp &nbsp &nbsp &nbsp: </td>
 <td><?php echo $ubn; ?></td>
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
 </td>
 <td><input type = <?php echo $knptype; ?> name = "knpMeld_" value = "Melden"></td>
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
<td colspan = 4 width = 500 align = center > <b style = "color : blue;"><?php if($oke <> $aantMeld) {echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";} ?> </b></td>
<td></td>
<td width = 50></td>
<td></td></tr>
<tr valign = bottom style = "font-size : 12px;">
<td colspan = 20 height = 20></td>

</tr>
<tr valign = bottom style = "font-size : 12px;">
<th>Datum<hr></th>
<th>Levensnummer oud<hr></th>
<th>Levensnummer nieuw<hr></th>
<th>Generatie<hr></th>
<th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
<th>Bericht<hr></th>
<th></th>

</tr>

<?php

$qryMeldregels = mysqli_query($db, "
SELECT m.meldId, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, h.oud_nummer, s.levensnummer, s.geslacht, ouder.datum dmaanw, st.stalId, m.skip, rq.dmmeld, m.fout, rs.respId, rs.sucind, rs.foutmeld, lastdm.datum dmlst, date_format(lastdm.datum,'%d-%m-%Y') lstdm

FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId and h.actId = 3 and h.skip = 0)
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
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and 
	 not exists (SELECT max(stl.stalId) stalId FROM tblStal stl WHERE stl.lidId = '".mysqli_real_escape_string($db,$lidId)."' and stl.stalId = st.stalId)
	GROUP BY st.schaapId
 ) lastdm on (lastdm.schaapId = s.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($db,$reqId)."' 
ORDER BY m.skip 
") or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryMeldregels))
	{
	$Id = $row['meldId'];
	$levnr_old = $row['oud_nummer'];
	$levnr = $row['levensnummer'];
	$geslacht = $row['geslacht']; 
	$dmaanw = $row['dmaanw']; 	if(isset($dmaanw)) { if($geslacht == 'ooi') { $fase = 'moederdier';} else if($geslacht == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
	$schaapdm = $row['schaapdm'];
	$dmschaap = $row['dmschaap'];
	$stalId = $row['stalId']; // Ter controle van eerdere stalId's
	$skip = $row['skip'];
	$dmmeld = $row['dmmeld'];
	$fout = $row['fout'];  			if(isset($dmmeld) && isset($fout)) { $foutieve_invoer = 'Niet gemeld ivm '.strtolower($fout); } 
									else { $foutieve_invoer = $fout; }
	$foutmeld = $row['foutmeld'];
	$respId = $row['respId'];
	$sucind = $row['sucind'];		if($sucind == 'J' && !isset($foutmeld)) { $bericht = 'RVO meldt : Melding correct'; } 
									else if($sucind == 'N' && isset($foutmeld)) { $bericht = 'RVO meldt : '.$foutmeld; } 
									else if(isset($respId)) { $bericht = 'Resultaat van melding is onbekend'; }
	$dmlst = $row['dmlst']; // Laatste datum van het vorige stalId van deze user
	$lstdm = $row['lstdm']; // t.b.v. commentaar
	

// Controleren of de te melden gegevens de juiste voorwaarde hebben .
	 If	( 
		empty($schaapdm)						||
		empty($levnr)	 						||
		$dmschaap > $maxdag 					|| # datum ligt na vandaag
		(isset($dmlst) && $dmschaap < $dmlst)	   # datum ligt voor de laatste datum van het vorige stalId van deze user 
		)
		 {	$check = 1;	} else {	$check = 0;	} 
// EINDE Controleren of de te melden gegevens de juiste voorwaarde hebben .  ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
<td align = center style = "color : <?php echo $color; ?>;" >
<!-- DATUM -->
<?php if ($skip == 1) { echo $schaapdm; } else { ?>
<input type = text size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> > <?php } ?>
</td>

<td style = "color : <?php echo $color; ?>;" >
<?php echo $levnr_old;  ?>
</td>

<td style = "color : <?php echo $color; ?>;" >
<?php
if ($skip == 1) { echo $levnr; } 
else { ?> 
	<input type = text name = <?php echo " \"txtLevnr_$Id\"; " ?> value = <?php echo $levnr; ?> size = 15 style = "font-size : 12px;"> 
<?php } ?>
</td>

<td align = center style = "color : <?php echo $color; ?>;" >
<?php echo $fase; ?>
</td>

<td  width = 50 align = center>
<input type = "hidden" size = 1 style = "font-size : 11px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 > <!--hiddden-->

<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($check == 1 || $skip == 1) ? 'checked' : ''; if ($check == 1) { ?> disabled <?php } ?>  >
</td>

<td width = 400 style = "color : red; font-size : 12px;">		

<!-- Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
<?php 
		if (empty($schaapdm)  )			{ $wrong = "Datum moet zijn gevuld."; }  
		else if (empty($levnr)  )  		{ $wrong = "Levensnummer moet zijn gevuld."; }
		else if ($dmschaap > $maxdag ) 	{ $wrong = "De datum mag niet in de toekomst liggen."; }
		else if (isset($dmlst) && $dmschaap < $dmlst) 	{ $wrong = "De datum mag niet voor ".$lstdm." liggen."; } ?>
<!-- EINDE Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
<?php

	if($skip == 1) 						{ $boodschap = "Verwijderd"; 	$color = "black"; }
	else if(isset($bericht)) 			{ $boodschap = $bericht; 		$color = "#FF4000"; unset($bericht); }
	else if(isset($foutieve_invoer) )	{ $boodschap = $foutieve_invoer; unset($foutieve_invoer); /*unset($wrong);*/ } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn 
	else if(isset($wrong) )				{ $boodschap = $wrong; unset($wrong); }

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