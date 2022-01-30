<?php /*	  7-11-2014 gemaakt 
24-3-2015 : login toegevoegd 
mail 13-04-2015 Arjen Dijkstra : Andere meldingen dan een geboorte doet u binnen zeven kalenderdagen na de gebeurtenis. De meldtermijn van 7 dagen betekent niet, dat je daarna niet meer kunt melden. Dit is in feite onbeperkt.
					  m.b.t. ‘datum in de toekomst’: dit is inderdaad 3 dagen en dat mag alleen bij Afvoermelding en Exportmelding */
$versie = '25-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '4-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J	9-2-2017 : ook where clouse bij vastleggen melddatum  */
$versie = '5-12-2016'; /* kzlPartijgewijzigd in kzlBest */
$versie = '26-1-2018'; /* Bij toch niet verwijderen wordt kzlBest weer gevuld */
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
$titel = 'Melden Afvoer';
$subtitel = '';
Include "header.php"; ?>
	<TD width = 960 height = 400 valign = "top">
<?php
$file = "Melden.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

include "vw_HistorieDm.php";
Include "responscheck.php";

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}
$knptype = "submit"; $vldtype = "text";
$overovermorgen = mktime(0, 0, 0, date("m")  , date("d")+3, date("Y"));
$maxdag = date('Y-m-d', $overovermorgen);

// De gegevens van het request 
$gegevensRequest = mysqli_query($db,"
SELECT r.reqId, l.relnr
FROM (
	SELECT min(rq.reqId) reqId 
	FROM tblRequest rq
	 join tblMelding m on (rq.reqId = m.reqId)
	 join tblHistorie h on (m.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and isnull(rq.dmmeld) and rq.code = 'AFV' 
 ) r
 join tblMelding m on (m.reqId = r.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (l.lidId = st.lidId)
WHERE h.skip = 0
GROUP BY r.reqId, l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($gegevensRequest))
	{	$reqId = $req['reqId']; }
// Einde De gegevens van het request
//if (isset($requestId)) {$reqId = $requestId; } else {$reqId = "$_POST[txtRequest_]";}
// Aantal dieren te melden
function aantal_melden($datb,$lidid,$fldReqId) {	
	$aantalmelden = mysqli_query($datb,"
	SELECT count(*) aant 
	FROM tblMelding m 
	 join tblHistorie h on (m.hisId = h.hisId)
	WHERE reqId = '".mysqli_real_escape_string($datb,$fldReqId)."' and m.skip <> 1 and h.skip = 0 ");//Foutafhandeling zit in return FALSE
		if($aantalmelden)
		{	$row = mysqli_fetch_assoc($aantalmelden);
	            return $row['aant'];
		}
		return FALSE;
}
// Maximum aantal meldingen binnen het request
$max_meldingen = mysqli_query($db,"
SELECT count(*) aant 
FROM tblMelding m 
 join tblHistorie h on (m.hisId = h.hisId)
WHERE reqId = '".mysqli_real_escape_string($db,$reqId)."' and h.skip = 0
");
		while ($maxmelding = mysqli_fetch_assoc($max_meldingen)) {	$maxmeld = $maxmelding['aant'];	}
// Einde Maximum aantal meldingen binnen het request

$aantMeld = aantal_melden($db,$lidId,$reqId);
// Einde Aantal dieren te melden

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke($datb,$lidid,$fldReqId,$nestHistorieDm) {
	$juistaantal = mysqli_query ($datb,"
	SELECT count(*) aant
	FROM tblMelding m
	 join tblHistorie h on (m.hisId = h.hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	 join ( 
		SELECT schaapId, max(datum) lastdatum 
		FROM (".$nestHistorieDm.") hd
		 left join tblActie a on (hd.actId = a.actId)
		WHERE hd.skip = 0 and (a.af = 0 or isnull(a.af)) and hd.actie != 'Gevoerd' and hd.actie not like '% gemeld'
		GROUP BY schaapId
	 ) mhd on (s.schaapId = mhd.schaapId)
	WHERE m.reqId = '".mysqli_real_escape_string($datb,$fldReqId)."' 
	 and h.datum is not null
	 and h.datum >= mhd.lastdatum
	 and h.datum <= (curdate() + interval 3 day)
	 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
	 and st.rel_best is not null
	 and m.skip <> 1
	 and h.skip = 0
	");
		if($juistaantal)
		{	$row = mysqli_fetch_assoc($juistaantal);
				return $row['aant'];
		}
		return FALSE;
}
$oke = aantal_oke($db,$lidId,$reqId,$vw_HistorieDm);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden.

// MELDEN
if (isset($_POST['knpMeld_'])) {	Include "save_melding.php"; $aantMeld = aantal_melden($db,$lidId,$reqId); $oke = aantal_oke($db,$lidId,$reqId,$vw_HistorieDm);
if( $aantMeld > 0 && $oke > 0) {
// Bestand maken
$qry_Leden = mysqli_query($db,"
SELECT ubn, alias
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db)); 
	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$ubn = $row['ubn'];
			$naam = $row['alias']; } // Het pad naar alle php bestanden

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden
		  
$input_file = $ubn."_".$naam."_".$reqId."_request.txt";
$end_dir_reader = $file_r ."/". "BRIGHT/";
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into het bestand */
$qry_txtRequest_RVO = mysqli_query ($db,"
SELECT rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, l.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer, 3 soort, NULL ubn_herk, p.ubn ubn_best, NULL land_herk, NULL geboortedm, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 join ( 
	SELECT schaapId, max(datum) datum 
	FROM (".$vw_HistorieDm.") hd
	 left join tblActie a on (hd.actId = a.actId)
	WHERE hd.skip = 0 and (a.af = 0 or isnull(a.af)) and hd.actie != 'Gevoerd' and hd.actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (s.schaapId = mhd.schaapId)
 left join tblRelatie rl on (rl.relId = st.rel_best)
 left join tblPartij p on (rl.partId = p.partId)
WHERE rq.reqId = '".mysqli_real_escape_string($db,$reqId)."' 
 and h.datum is not null
 and h.datum >= mhd.datum
 and h.datum <= (curdate() + interval 3 day) 
 and p.ubn is not null
 and m.skip <> 1 and h.skip = 0
 and isnull(fout)
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
	$goed = "De schapen kunnen handmatig worden gemeld."; }
		else {
	$goed = "Er is niets te controleren."; }
}
$aantMeld = aantal_melden($db,$lidId,$reqId);
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
<form name = "frmactive" action="MeldAfvoer.php" method = "post">
<table border = 0>
<tr>
 <td align = "right">Meldingnr : </td>
 <td><?php echo $reqId; ?> </td>
 <td width = 850 align = "right">Aantal dieren te melden : </td>
 <td><?php echo $aantMeld." van de ".$maxmeld; ?></td>
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
<th>Afvoerdatum<hr></th>
<th>Levensnummer<hr></th>
<th>Generatie<hr></th>
<th>Bestemming<hr></th>
<th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
<th>Bericht<hr></th>
<th></th>

</tr>

<?php
if (isset($_POST['knpSave_'])) {	Include "save_melding.php";	 /*header("Location: ".$curr_url);*/ } 
// Include kan niet binnen de loop van $qryMeldregels om dat de functies binnen 'save_melding' dan vaker wordt aangemaakt en dat kan niet. Via phphulp ben ik hier achter gekomen. bron : http://www.phphulp.nl/php/forum/topic/cannot-redeclare-makequote-previously/67477/  

$qryMeldregels = mysqli_query($db, "
SELECT m.meldId, date_format(h.datum,'%d-%m-%Y') datum, h.datum date, s.levensnummer, s.geslacht, ouder.datum dmaanw, st.rel_best, p.naam, p.ubn ubn_best, m.skip, rq.dmmeld, m.fout, rs.respId, rs.sucind, rs.foutmeld, date_format(mhd.datum,'%Y-%m-%d') datummin, date_format(mhd.datum,'%d-%m-%Y') mindatum

FROM tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId, h.datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) ouder on (s.schaapId = ouder.schaapId)
 join ( 
	SELECT schaapId, max(datum) datum 
	FROM (".$vw_HistorieDm.") hd
	 left join tblActie a on (hd.actId = a.actId)
	WHERE hd.skip = 0 and (a.af = 0 or isnull(a.af)) and hd.actie != 'Gevoerd' and hd.actie not like '% gemeld'
	GROUP BY schaapId
 ) mhd on (s.schaapId = mhd.schaapId)
 left join tblRelatie rl on (rl.relId = st.rel_best)
 left join tblPartij p on (rl.partId = p.partId)
 left join (
	SELECT max(respId) respId, rs.reqId, rs.levensnummer
	FROM impRespons rs
	GROUP BY rs.reqId, rs.levensnummer
 ) lrs on (lrs.levensnummer = s.levensnummer and lrs.reqId = rq.reqId)
 left join impRespons rs on (lrs.respId = rs.respId)
WHERE h.skip = 0 and m.reqId = '".mysqli_real_escape_string($db,$reqId)."'
ORDER BY m.skip, if (h.datum < mhd.datum, 1, if(h.datum > (curdate() + interval 3 day),1,0 )) desc, right(s.levensnummer,".$Karwerk.")
" ) or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryMeldregels))
	{
	$Id = $row['meldId'];
	$levnr = $row['levensnummer']; 
	$geslacht = $row['geslacht']; 
	$dmaanw = $row['dmaanw']; 	if(isset($dmaanw)) { if($geslacht == 'ooi') { $fase = 'moederdier';} else if($geslacht == 'ram') { $fase = 'vaderdier'; } } else { $fase = 'lam'; }
	$schaapdm = $row['datum'];
	$dmschaap = $row['date'];
	$rel_best = $row['rel_best'];
	$ubn_bst = $row['ubn_best'];
	$bestemming = $row['naam'];
	$skip = $row['skip'];
	$dmmeld = $row['dmmeld'];
	$fout = $row['fout'];  			if(isset($dmmeld) && isset($fout)) { $foutieve_invoer = 'Niet gemeld ivm '.strtolower($fout); } 
									else { $foutieve_invoer = $fout; }
	$foutmeld = $row['foutmeld'];
	$respId = $row['respId'];
	$sucind = $row['sucind'];		if(!isset($ubn_bst)) { $bericht = 'Ubn van bestemming is onbekend'; }
									else if($sucind == 'J' && !isset($foutmeld)) { $bericht = 'RVO meldt : Melding correct'; }
									else if(isset($foutmeld)) { $bericht = 'RVO meldt : '.$foutmeld; } 
									else if(isset($respId)) { $bericht = 'Resultaat van melding is onbekend'; }
	$dmmin = $row['datummin'];
	$mindm = $row['mindatum'];
								//if( m.fout is not null and r.dmmeld is not null,concat('Niet gemeld ivm ',lcase(m.fout)) ,m.fout)  fout
 	 
	if ($dmschaap < $dmmin || 
		$dmschaap > $maxdag ||
		!isset($ubn_bst) ) 	 {	$check = 1;	} else {	$check = 0;	} ?>
	 
<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
<td align = center style = "color : <?php echo $color; ?>;" >
<!-- DATUM -->
<?php if ($skip == 1) { echo $schaapdm; $vldtype = "hidden"; } ?>
<input type = <?php echo $vldtype; ?> size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> > 
</td>

<td align = center style = "color : <?php echo $color; ?>;" >	<?php echo $levnr; ?>

<td align = center style = "color : <?php echo $color; ?>;" >	<?php echo $fase; ?>  </td>

<?php
// Declaratie BESTEMMING			// lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen tblRelaties.
$qryRelatie = "
SELECT relId, lower(if(isnull(p.ubn),'6karakters',p.ubn)) ubn, p.naam
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId) 
WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.relatie = 'deb' and p.actief = 1 and r.actief = 1
ORDER BY naam
"; 
$relatienr = mysqli_query ($db,$qryRelatie) or die (mysqli_error($db)); 

$index = 0; 
while ($rnr = mysqli_fetch_array($relatienr)) 
{ 
   $relId[$index] = $rnr['relId']; 
   $relnum[$index] = $rnr['naam'];
   $relRaak[$index] = $rnr['relId'];   
   $index++; 
} 
unset($index);
// EINDE Declaratie BESTEMMING	?>
<td align = center style = "color : <?php echo $color; ?>;" > 
<?php if ($skip == 1) { echo $bestemming; }  else { ?>

<!-- KZLBESTEMMING	-->
<select <?php echo "name=\"kzlBest_$Id\" "; ?> style = "width:135; font-size:12px;" >
  <option></option>
<?php	$count = count($relnum);
for ($i = 0; $i < $count; $i++){

	$opties = array($relId[$i]=>$relnum[$i]);
			foreach ($opties as $key => $waarde)
			{
  if ((!isset($_POST["kzlBest_$Id"]) && $rel_best == $relRaak[$i]) || (isset($_POST["kzlBest_$Id"]) && $_POST["kzlBest_$Id"] == $key) ) {
	echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
	echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }	
			}
} ?>
</select> <!-- EINDE KZLBESTEMMING	-->
<?php } ?>
</td>

<td width = 50 align = "center">
<input type = "hidden" size = 1 style = "font-size : 12px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 > <!--hiddden-->

<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($skip == 1) ? 'checked' : ''; ?>  >
</td>

<td width = 600 style = "font-size : 14px;">		

<!-- Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
<?php 
		/*if (empty($schaapdm)  )			{ $wrong = "Datum moet zijn gevuld."; }
		else*/ if ($dmschaap < $dmmin) 	{ $color = "red"; /*$wrong = "De datum mag niet voor ".$mindm." liggen.";*/ } 
		else if ($dmschaap > $maxdag ) 	{ $color = "red"; /*$wrong = "De datum ligt verder dan 3 dagen in de toekomst.";*/ }  
		else { $color = "blue"; } ?>
<!-- EINDE Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes --> 
<?php if($skip == 1) 					{ $boodschap = "Verwijderd"; 	$color = "black"; }
	  else if(isset($bericht)) 			{ $boodschap = $bericht; 		$color = "#FF4000"; unset($bericht);}
	  else if(isset($foutieve_invoer) )	{ $boodschap = $foutieve_invoer; unset($foutieve_invoer); unset($wrong); } // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn
	  //else if(isset($wrong)) 			{ $boodschap = $wrong; unset($wrong); 				  $color = "blue"; } 

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