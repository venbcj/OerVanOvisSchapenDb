<?php
$versie = '7-11-2014'; /*gemaakt */
$versie = '24-3-2015'; /*login toegevoegd 
mail 13-04-2015 Arjen Dijkstra : U meldt een geboorte binnen zes maanden in I&R
					  m.b.t. ‘datum in de toekomst’: dit is inderdaad 3 dagen en dat mag alleen bij Afvoermelding en Exportmelding */
$versie = '4-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J  */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '3-1-2020'; /* het pad ($file_r) naar FTP variabel gemaakt ipv uit tblLeden gehaald */
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
$titel = 'Melden Geboortes';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "Melden.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

//Include "vw_Meldingen.php";
Include "responscheck.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}

if (isset($_POST['knpSave_'])) {	Include "save_melding.php";  header("Location: ".$curr_url); } 

$knptype = "submit";
$today = date("Y-m-d");

// De gegevens van het request
$zoek_oudste_request_niet_definitief_gemeld = mysqli_query($db,"
SELECT min(rq.reqId) reqId, l.relnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (l.lidId = st.lidId)
WHERE l.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(rq.dmmeld) and rq.code = 'GER' 
GROUP BY l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($zoek_oudste_request_niet_definitief_gemeld))
	{	$reqId = $req['reqId']; }
// Einde De gegevens van het request

// Aantal dieren te melden 
function aantal_melden($datb,$fldReqId) {

$aantalmelden = mysqli_query($datb,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."' and m.skip <> 1 and h.skip = 0
");//Foutafhandeling zit in return FALSE

	if($aantalmelden)
	{	$row = mysqli_fetch_assoc($aantalmelden);
            return $row['aant'];
	}
	return FALSE;
}

$aantMeld = aantal_melden($db,$reqId);
// Einde Aantal dieren te melden

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke($datb,$fldReqId) {

$juistaantal = mysqli_query ($datb,"
SELECT count(*) aant
FROM tblMelding m
 join tblHistorie h on (h.hisId = m. hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."' 
 and h.datum is not null
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12
 and LENGTH(RTRIM(CAST(h.datum AS UNSIGNED))) = 8
 and m.skip <> 1
");
	if($juistaantal)
	{	$row = mysqli_fetch_assoc($juistaantal);
			return $row['aant'];
	}
	return FALSE;
}
$oke = aantal_oke($db,$reqId);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden. 

// MELDEN
if (isset($_POST['knpMeld_'])) { 	Include "save_melding.php"; $aantMeld = aantal_melden($db,$reqId); $oke = aantal_oke($db,$reqId);
if( $aantMeld > 0 && $oke > 0) { 
// Bestand maken
$qry_Leden = mysqli_query($db,"
SELECT ubn, alias
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db)); 

	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$ubn = $row['ubn'];
			$alias = $row['alias']; }

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden
		  
$input_file = $ubn."_".$alias."_".$reqId."_request.txt"; // Bestandsnaam
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, l.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer, 3 soort, NULL ubn_herk, NULL ubn_best, NULL land_herk, NULL geboortedatum, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE rq.reqId = '".mysqli_real_escape_string($db,$reqId)."' 
 and h.datum is not null
 and h.datum <= curdate()
 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12
 and LENGTH(RTRIM(CAST(h.datum AS UNSIGNED))) = 8
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
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' and def = 'J' ";
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
$definitief = mysqli_query($db, "
SELECT r.def
FROM tblRequest r
WHERE r.reqId = '".mysqli_real_escape_string($db,$reqId)."'
") or die (mysqli_error($db));

	while($defi = mysqli_fetch_assoc($definitief))
	{	$def = $defi['def'];	}
?>
<form action="MeldGeboortes.php" method = "post">
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
<td colspan = 4 width = 500 align = center > <b style = "color : red;"><?php if($oke <> $aantMeld) {echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";} ?> </b></td>
<td></td>
<td width = 50></td>
<td></td></tr>
<tr valign = bottom style = "font-size : 12px;">
<td colspan = 20 height = 20></td>

</tr>
<tr valign = bottom style = "font-size : 12px;">
<th>Geboorte datum<hr></th>
<th>Levensnummer<hr></th>
<th>Werknr<hr></th>
<th>Geslacht<hr></th>
<th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
<th>Bericht<hr></th>
<th></th>

</tr>

<?php 
$zoek_meldregels = mysqli_query($db, "
SELECT m.meldId, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, s.levensnummer, right(s.levensnummer,".$Karwerk.") werknr, s.geslacht, m.skip, m.fout, rs.respId, rs.sucind, rs.foutmeld
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT max(respId) respId, levensnummer
	FROM impRespons
	WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."'
	GROUP BY levensnummer
 ) mresp on (mresp.levensnummer = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
WHERE m.reqId = '".mysqli_real_escape_string($db,$reqId)."'
ORDER BY m.skip, if(h.datum > curdate(),1,0 ) desc, right(s.levensnummer,".$Karwerk.")
" ) or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($zoek_meldregels))
	{
	$Id = $row['meldId'];
	$schaapdm = $row['schaapdm'];
	$dmschaap = $row['dmschaap'];
	$levnr = $row['levensnummer']; //if (strlen($levnr)== 11) {$levnr = '0'.$row['levensnummer'];}
	$werknr = $row['werknr'];
	$sekse = $row['geslacht'];
	$skip = $row['skip'];
	$fout_db = $row['fout'];
	$foutmeld = $row['foutmeld'];
	$respId = $row['respId'];		
	$sucind = $row['sucind'];

	 
if (empty($schaapdm) 		|| # datum is leeg
	empty($levnr)	 		|| # levensnummer is leeg
	empty($sekse)	 		|| # geslacht is leeg
	$dmschaap > $today 		|| # geboorte datum ligt in de toekomst 
	strlen($levnr)<> 12 	|| # of levensnummer is geen 12 karakters lang
	numeriek($levnr) == 1	|| # het levensnummer bevat een letter
	intval($levnr) == 0 	|| # het levensnummer is 000000000000 of te wel onjuist
	intval(str_replace('-','',$schaapdm)) == 0 # Van datum naar nummer is 0 of te wel datum = 00-00-0000
) 	 
 	{	$check = 1;	$waarschuwing = ' Dit dier wordt niet gemeld.'; } else { $check = 0; unset($waarschuwing); } 


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

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
 <td align = center style = "color : <?php echo $color; ?>;" >
<!-- DATUM -->
<?php //echo $Id;
if ($skip == 1) { echo $schaapdm; } 
else { ?>
	<input type = text size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> >
<?php } ?>
 </td>
<!-- LEVENSNUMMER -->
 <td align = center style = "color : <?php echo $color; ?>;" >
<?php 
if ($skip == 1) { echo $levnr; }
else { ?> 
 	<input type = text name = <?php echo " \"txtLevnr_$Id\" value = \"$levnr\" ;"?> size = 12 style = "font-size : 12px;"> <?php } ?>
 </td>
<!-- WERKNR -->
 <td align = center <?php if ($skip == 1) { ?> style = "color : <?php echo $color; ?>;" <?php } ?> >
   <?php echo $werknr;?>
 </td>
<!-- GESLACHT -->
 <td align = center style = "color : <?php echo $color; ?>;" >
<?php if ($skip == 1) { if($sekse == 'm') { $gesl = 'ram'; } else { $gesl = 'ooi'; } echo $gesl; ?>
	<input type = "hidden" size = 1 style = "font-size : 9px;" name = <?php echo " \"kzlSekse_$Id\" ;" ?> value = <?php echo $sekse; ?> > <!--hiddden--> <?php }  else { ?>

<!-- KZLgeslacht --> 
<select <?php echo "name=\"kzlSekse_$Id\" "; ?> style = "width:59; font-size:13px;">
<?php  
$opties = array('' => '', 'ooi' => 'ooi', 'ram' => 'ram');
foreach ( $opties as $key => $waarde)
{
   if((!isset($_POST['knpSave_']) && $sekse == $key) || (isset($_POST["kzlSekse_$Id"]) && $_POST["kzlSekse_$Id"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else {
	echo '<option value="' . $key . '">' . $waarde . '</option>';
  }
} ?> 
</select> <!-- EINDE KZLgeslacht -->
<?php } ?>
 </td>

 <td width = 50 align = "center">
	<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($skip == 1) ? 'checked' : ''; ?>  >
 </td>

 <td width = 400 style = "font-size : 14px;">

<?php 
	if($skip == 1) 					{ $boodschap = "Verwijderd"; 	 $color = "black"; }
elseif(isset($bericht)) 			{ $boodschap = $bericht; 		 $color = "#FF4000"; unset($bericht); }
elseif(isset($foutieve_invoer) )	{ $boodschap = $foutieve_invoer; $color = "blue"; unset($foutieve_invoer); /*unset($wrong);*/ } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn 
elseif(strlen($levnr) <> 12 || numeriek($levnr) == 1 || intval($levnr) == 0) { $color = 'red';  $boodschap =  'Levensnummer is onjuist.'.$waarschuwing; } 
//elseif (intval(str_replace('-','',$dmschaap)) == 0) { $color = 'red';  $boodschap =  "Datum is onjuist.".$waarschuwing; } 
else 								{ $color = 'red';  $boodschap = $waarschuwing; } 

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
} ?>
</table>
</form> 

	</TD>
<?php
Include "menuMelden.php"; } ?>
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