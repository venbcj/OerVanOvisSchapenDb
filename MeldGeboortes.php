<?php
$versie = '7-11-2014'; /*gemaakt */
$versie = '24-3-2015'; /*login toegevoegd 
mail 13-04-2015 Arjen Dijkstra : U meldt een geboorte binnen zes maanden in I&R
					  m.b.t. ‘datum in de toekomst’: dit is inderdaad 3 dagen en dat mag alleen bij Afvoermelding en Exportmelding */
$versie = '4-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J  */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '3-1-2020'; /* het pad ($file_r) naar FTP variabel gemaakt ipv uit tblLeden gehaald */

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

if (isset($_POST['knpSave_'])) {	Include "save_melding.php";  header("Location: ".$curr_url); } 
// Include kan niet binnen de loop van $qryMeldregels om dat de functies binnen 'save_melding' dan vaker wordt aangemaakt en dat kan niet. Via phphulp ben ik hier achter gekomen. bron : http://www.phphulp.nl/php/forum/topic/cannot-redeclare-makequote-previously/67477/

function numeriek($subject) {
	if (preg_match('/([[a-zA-Z])/', $subject, $matches)) {  /*var_dump($matches[1]); */ return 1; }
}
$knptype = "submit"; $vldtype = "text";
$maxdag = date("Y-m-d");

// De gegevens van het request
$gegevensRequest = mysqli_query($db,"
select min(rq.reqId) reqId, l.relnr
from tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (l.lidId = st.lidId)
where l.lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(rq.dmmeld) and rq.code = 'GER' 
group by l.relnr
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($gegevensRequest))
	{	$reqId = $req['reqId'];
		$relnr = $req['relnr'];	}
// Einde De gegevens van het request
//if (isset($requestId)) {$reqId = $requestId; } else {$reqId = "$_POST[txtRequest_]";}
// Aantal dieren te melden 
function aantal_melden($datb,$lidid,$fldReqId) {
	$aantalmelden = mysqli_query($datb,"select count(*) aant from tblMelding m where m.reqId = ".$fldReqId." and m.skip <> 1 ");//Foutafhandeling zit in return FALSE
		if($aantalmelden)
		{	$row = mysqli_fetch_assoc($aantalmelden);
	            return $row['aant'];
		}
		return FALSE;
}
// Maximum aantal meldingen binnen het request
$max_meldingen = mysqli_query($db,"select count(*) aant from tblMelding m where m.reqId = ".$reqId." and m.skip = 0 ");
		while ($maxmelding = mysqli_fetch_assoc($max_meldingen)) {	$maxmeld = $maxmelding['aant'];	}
// Einde Maximum aantal meldingen binnen het request

$aantMeld = aantal_melden($db,$lidId,$reqId);
// Einde Aantal dieren te melden

// Aantal dieren goed geregistreerd om automatisch te kunnen melden.
function aantal_oke($datb,$lidid,$fldReqId) {
	$juistaantal = mysqli_query ($datb,"
	select count(*) aant
	from tblMelding m
	 join tblHistorie h on (h.hisId = m. hisId)
	 join tblStal st on (st.stalId = h.stalId)
	 join tblSchaap s on (st.schaapId = s.schaapId)
	where m.reqId = ".$fldReqId." 
	 and h.datum is not null
	 and h.datum <= curdate()
	 and LENGTH(RTRIM(CAST(s.levensnummer AS UNSIGNED))) = 12 
	 and m.skip <> 1
	");
		if($juistaantal)
		{	$row = mysqli_fetch_assoc($juistaantal);
				return $row['aant'];
		}
		return FALSE;
}
$oke = aantal_oke($db,$lidId,$reqId);
// Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden. 

// MELDEN
if (isset($_POST['knpMeld_'])) { 	Include "save_melding.php"; $aantMeld = aantal_melden($db,$lidId,$reqId); $oke = aantal_oke($db,$lidId,$reqId);
if( $aantMeld > 0 && $oke > 0) { 
// Bestand maken
$qry_Leden = mysqli_query($db,"select ubn, alias, root_files from tblLeden where lidId = ".mysqli_real_escape_string($db,$lidId)." ;") or die (mysqli_error($db)); 
	while ($row = mysqli_fetch_assoc($qry_Leden))
		{	$ubn = $row['ubn'];
			$naam = $row['alias']; } // Het pad naar alle php bestanden

$file_r = dirname(__FILE__); // Het pad naar alle php bestanden
		  
$input_file = $ubn."_".$naam."_".$reqId."_request.txt";
$end_dir_reader = $file_r ."/". "BRIGHT/"; 
$root = $end_dir_reader.$input_file;

    $fh = fopen($root, 'w');
   
/* insert field values into data.txt */
    $qry_txtRequest_RVO = mysqli_query ($db,"
select rq.reqId, l.prod, rq.def, l.urvo, l.prvo, rq.code melding, l.relnr, l.ubn, date_format(h.datum,'%d-%m-%Y'), 'NL' land, s.levensnummer, 3 soort,
 NULL ubn_herk, NULL ubn_best, NULL land_herk, NULL geboortedatum, NULL sucind, NULL foutind, NULL foutcode, NULL bericht, NULL meldnr
from tblRequest rq
 join tblMelding m on (rq.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 join tblSchaap s on (st.schaapId = s.schaapId)
where rq.reqId = ".mysqli_real_escape_string($db,$reqId)." 
 and h.datum is not null
 and h.datum <= curdate()
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
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() where reqId = ".mysqli_real_escape_string($db,$reqId)." and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));

		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden"; }
	$goed = "De melding is verstuurd.";
}	

else if ( $aantMeld == 0 || $oke == 0) {
// Melddatum registreren in tblRequest bij 0 te melden
 $upd_tblRequest = "UPDATE tblRequest SET dmmeld = now() where reqId = ".mysqli_real_escape_string($db,$reqId)." and def = 'J' ";
	mysqli_query($db,$upd_tblRequest) or die (mysqli_error($db));
	
		if($_POST['kzlDef_'] == 'J'){
	$knptype = "hidden";
	$goed = "De schapen zijn verwijderd."; }
		else {
	$goed = "Er is niets te controleren."; }
}
$aantMeld = aantal_melden($db,$lidId,$reqId);
} // EINDE MELDEN

// Ophalen 'vaststellen' cq 'controle'
$definitief = mysqli_query($db, "select r.def from tblRequest r where r.reqId = $reqId " ) or die (mysqli_error($db));

	while($defi = mysqli_fetch_assoc($definitief))
	{	$def = $defi['def'];	}
?>
<form action="MeldGeboortes.php" method = "post">
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
<th>Geboorte datum<hr></th>
<th>Levensnummer<hr></th>
<th>Werknr<hr></th>
<th>Geslacht<hr></th>
<th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
<th>Bericht<hr></th>
<th></th>

</tr>

<?php 
$qryMeldregels = mysqli_query($db, "
SELECT m.meldId, date_format(h.datum,'%d-%m-%Y') schaapdm, h.datum dmschaap, s.levensnummer, right(s.levensnummer,".$Karwerk.") werknr, s.geslacht, m.skip, m.fout, rs.sucind, rs.foutmeld
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join (
	SELECT max(respId) respId, levensnummer
	FROM impRespons
	WHERE reqId = ".$reqId."
	GROUP BY levensnummer
 ) mresp on (mresp.levensnummer = s.levensnummer)
 left join impRespons rs on (rs.respId = mresp.respId)
WHERE m.reqId = ".mysqli_real_escape_string($db,$reqId)."
ORDER BY m.skip, if(h.datum > curdate(),1,0 ) desc, right(s.levensnummer,".$Karwerk.")
" ) or die (mysqli_error($db));

	while($row = mysqli_fetch_assoc($qryMeldregels))
	{
	$Id = $row['meldId'];
	$schaapdm = $row['schaapdm'];
	$dmschaap = $row['dmschaap'];
	$levnr = $row['levensnummer']; //if (strlen($levnr)== 11) {$levnr = '0'.$row['levensnummer'];}
	$werknr = $row['werknr'];
	$sekse = $row['geslacht'];
	$skip = $row['skip'];
	$fout = $row['fout'];  			if(isset($dmmeld) && isset($fout)) { $foutieve_invoer = 'Niet gemeld ivm '.strtolower($fout); } 
									else { $foutieve_invoer = $fout; }
	$sucind = $row['sucind'];
	$foutmeld = $row['foutmeld'];

if($sucind == 'J' && !isset($foutmeld)) { $bericht = 'RVO meldt : Melding correct'; }
else if ($sucind == 'N' && isset($foutmeld)) { $bericht = 'RVO meldt : '.$foutmeld; }
else if (isset($respId)) { $bericht = 'Resultaat van melding is onbekend'; }
	 
	 if ( /*(isset($dmpost) && $dmpost < $dmmin) || Deze voorwaarde is hier nvt omdat dmpost niet is opgeslagen in de database. oorspronkelijke datum wordt weer getoond */
		empty($schaapdm) ||
		empty($levnr)	 ||
		empty($sekse)	 ||
		$dmschaap > $maxdag ||
		strlen($levnr)<> 12	|| # of levensnummer is geen 12 karakters lang
		numeriek($levnr) == 1 ) 	 {	$check = 1;	} else {	$check = 0;	} ?>

<!--	**************************************
	**	   OPMAAK  GEGEVENS		**
	************************************** -->

<tr style = "font-size:15px;" >
<!-- Id -->
<?php if ($skip == 1) { $color = "#D8D8D8"; } ?>
<td align = center style = "color : <?php echo $color; ?>;" > <!--meldId:--> <input type= "hidden" size = 1 <?php echo "name=\"txtId_$Id\" value = $Id"; ?> > <!--hiddden-->
<!-- DATUM -->
<?php if ($skip == 1) { echo $schaapdm; $vldtype = "hidden"; } ?>
<input type = <?php echo $vldtype; ?> size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> >
</td>
<!-- LEVENSNUMMER -->
<td align = center style = "color : <?php echo $color; ?>;" >
<?php 
if ($skip == 1) { echo $levnr; $vldtype = "hidden"; }
if (strlen($levnr) == 12 && numeriek($levnr) <> 1) { ?> 
	<input type = <?php echo $vldtype; ?> name = <?php echo " \"txtLevnr_$Id\" value = \"$levnr\" ;"?> size = 15 style = "font-size : 12px;"> <?php } else { ?> 
 	<input type = <?php echo $vldtype; ?> name = <?php echo " \"txtLevnr_$Id\" value = \"$levnr\" ;"?> size = 12 style = "font-size : 12px; color : red;"> <?php } ?>
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
<input type = "hidden" size = 1 style = "font-size : 12px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 > <!--hiddden-->

<input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1 <?php echo ($skip == 1) ? 'checked' : ''; ?>  >
</td>

<td width = 400 style = "font-size : 14px;">

<!-- Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
<?php 
		/*if (empty($schaapdm) || empty($levnr) || $sekse == '' )  { $wrong = " Alle velden moeten zijn gevuld."; }
		else*/ if ($dmschaap > $maxdag ) 	{ $color = "red"; /*$wrong = "De datum mag niet in de toekomst liggen.";*/ }
		else if (strlen($levnr) <> 12) 	{ $color = "red"; /*$wrong = "Levensnummer geen 12 karakters.";*/ }  
		else if (numeriek($levnr) == 1) { $color = "red"; /*$wrong = "Levensnummer bevat een letter.";*/ } 
		else { $color = "blue"; } ?>
<!-- EINDE Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes --> 
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
Include "menu1.php";  } ?>
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