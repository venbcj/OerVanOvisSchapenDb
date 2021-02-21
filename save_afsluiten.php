<!-- 30-11-2014 : voerId gewijzigd naar InkId  In tblVoeding worden de eenhId, prijs en btw niet meer gebruikt 
27-11-2015 : hernoemd van insVoer.php naar save_voer.php en views variabel gemaakt.
29-11-2015 : Bij $svwHistorieHok UNION toegevoegd omdat gebleven schapen in $svwHistorieHok wordt gebaseerd op de afsluitdm. In dit script hebben gebleven schapen op enig moment nog afsluitdm NULL
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel	22-1-2017 : tblBezetting gewijzigd naar tblBezet
2-3-2017 : bestand save_voer.php gesplitst in save_afsluiten1.php en save_afsluiten2.php 1 staat voor geboren en 2 voor gespeend
18-8-2019 : Loop gebouwd om steeds nieuw inkoopid aan te spreken indien nodig en de splitsing van drie save_afluiten1 2 3.php samengevoegd tot 1 bestand save_afsluiten.php 

save_voer.php toegpast in : 
	- HokAfsluiten.php  -->

<?php

// Controle op fouten
if(isset($dmsluit)) {

$zoek_afgesloten_periode = mysqli_query($db,"SELECT periId FROM tblPeriode WHERE hokId = ".mysqli_real_escape_string($db,$Id)." and doelId = ".mysqli_real_escape_string($db,$doelId)." and dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."' ") or die (mysqli_error($db));
	while($per = mysqli_fetch_assoc($zoek_afgesloten_periode))
		  {	$periId_slt = $per['periId'];	}
}
if(isset($periId_slt)) { $fout = "De afsluitdatum bestaat al."; }

else if((isset($txtKg) && !isset($fldInk)) || !isset($txtKg) && isset($fldInk)) { $fout = "Het voer is onvolledig."; }
		  
else if(isset($txtKg) && isset($fldInk)) {

$zoek_voerId = mysqli_query($db,"
SELECT i.artId
FROM tblInkoop i
WHERE i.inkId = ".mysqli_real_escape_string($db,$fldInk)."
GROUP BY i.artId
") or die(mysqli_error($db));
	while( $ar = mysqli_fetch_assoc($zoek_voerId)) { $voerId = $ar['artId']; }

// Totale hoeveelheid voer op voorraad bepalen bij het oudste inkId dat nog voorraad heeft.
$queryStock = mysqli_query($db,"
SELECT sum(i.inkat-coalesce(v.vbrat,0)) vrdat
FROM tblInkoop i
 left join (
	SELECT i.inkId, sum(v.nutat*v.stdat) vbrat
	FROM tblVoeding v
	 join tblInkoop i on (v.inkId = i.inkId)
	WHERE i.artId = ".mysqli_real_escape_string($db,$voerId)."
	GROUP BY i.inkId
 ) v on (i.inkId = v.inkId)
WHERE i.artId = ".mysqli_real_escape_string($db,$voerId)."
") or die(mysqli_error($db));

while ($hoev = mysqli_fetch_assoc($queryStock)) { $instock = $hoev['vrdat']; }
// EINDE Totale hoeveelheid voer op voorraad bepalen bij het oudste inkId dat nog voorraad heeft.  

// Controle of voorraad toereikend is
if (isset($instock) && $instock < $txtKg && isset($fldInk) ) { $fout = "Er is onvoldoende voer op voorraad."; }

}
// Einde Controle of voorraad toereikend is

// Einde Controle op fouten

if (!isset($fout) && isset($dmsluit)) { // Voer hoeft niet te zijn opgegeven bij afsluiten periode !!

			
// STAP 1) tblPeriode afsluiten
$insert_tblPeriode = "
INSERT INTO tblPeriode SET hokId = ".mysqli_real_escape_string($db,$Id).", doelId = ".mysqli_real_escape_string($db,$doelId).", dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."'
"; 
	mysqli_query($db,$insert_tblPeriode) or die(mysqli_error($db));

if (isset($fldInk) && isset($txtKg)) { // Als voer is opgegeven

//********** functies maken *******************
function oudste_inkoop($datb, $VoerId) {
$zoek_actueel_inkId = mysqli_query($datb,"
	SELECT min(i.inkId) inkId
	FROM tblInkoop i
	 join tblArtikel a on (i.artId = a.artId)
	 left join (
		SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
		FROM tblVoeding v
		 join tblInkoop i on (i.inkId = v.inkId)
		WHERE i.artId = ".mysqli_real_escape_string($datb,$VoerId)."
		GROUP BY v.inkId
	 ) v on (i.inkId = v.inkId)
	WHERE i.artId = ".mysqli_real_escape_string($datb,$VoerId)." and i.inkat-coalesce(v.vbrat,0) > 0
") or die (mysqli_error($datb));
		while($ink = mysqli_fetch_assoc($zoek_actueel_inkId))  { $inkId_new = $ink['inkId'];	}

	return $inkId_new;
}

function actuele_voorraad($datb,$inknr){
$qryStock = mysqli_query($datb,"
	SELECT i.inkat - sum(coalesce(v.nutat,0)) stock
	FROM tblInkoop i
	 left join tblVoeding v on (i.inkId = v.inkId)
	WHERE i.inkId = ".mysqli_real_escape_string($datb,$inknr)."
	GROUP BY i.inkat
") or die(mysqli_error($datb));
		while($istk = mysqli_fetch_assoc($qryStock))  {	$inkvrd = $istk['stock']; }

	return $inkvrd;
}


function inlezen_voer($datb, $PeriId, $VoerId, $Stdat, $TxtKg){
		$inkId = oudste_inkoop($datb,$VoerId);

		$act_vrd = actuele_voorraad($datb, $inkId);

		if($act_vrd < $TxtKg ) { 

			$insert_tblVoeding_rest = "INSERT INTO tblVoeding set periId = ".mysqli_real_escape_string($datb,$PeriId).", inkId = ".mysqli_real_escape_string($datb,$inkId).", nutat = ".mysqli_real_escape_string($datb,$act_vrd).", stdat = ".mysqli_real_escape_string($datb,$Stdat)."
";
		mysqli_query($datb,$insert_tblVoeding_rest) or die(mysqli_error($datb));

		$TxtKg = $TxtKg-$act_vrd;

			unset($inkId);

			$inlezen = inlezen_voer($datb, $PeriId, $VoerId, $Stdat, $TxtKg);

			return $inlezen;
		}

		else if($act_vrd >= $TxtKg && $TxtKg > 0 ) { 

			$insert_tblVoeding = "INSERT INTO tblVoeding set periId = ".mysqli_real_escape_string($datb,$PeriId).", inkId = ".mysqli_real_escape_string($datb,$inkId).", nutat = ".mysqli_real_escape_string($datb,$TxtKg).", stdat = ".mysqli_real_escape_string($datb,$Stdat)."
";
		mysqli_query($datb,$insert_tblVoeding) or die(mysqli_error($datb));

		}

	

}

//********** Einde functies maken *******************

// STAP 2) Zoek periode
$zoek_periode = mysqli_query($db,"SELECT periId FROM tblPeriode WHERE hokId = ".mysqli_real_escape_string($db,$Id)." and doelId = ".mysqli_real_escape_string($db,$doelId)." and dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."' ") or die (mysqli_error($db));
	while($per = mysqli_fetch_assoc($zoek_periode))
		  {	$periId = $per['periId'];	}

// STAP 3) Standaard hoeveelheid voer ophalen
$zoek_stdat = mysqli_query($db,"
SELECT a.stdat
FROM tblArtikel a
WHERE artId = ".mysqli_real_escape_string($db,$voerId)." 
") or die(mysqli_error($db));
	while($lijn = mysqli_fetch_assoc($zoek_stdat)) { $stdat = $lijn['stdat'];	}


// STAP 4) Voer inlezen

echo inlezen_voer($db, $periId, $voerId, $stdat, $txtKg);

		} // EINDE Als voer is opgegeven
		
	
$zoek_hoknr = mysqli_query($db,"SELECT hoknr FROM tblHok WHERE hokId = ".mysqli_real_escape_string($db,$Id)." ") or die (mysqli_error($db));
	while($hk = mysqli_fetch_assoc($zoek_hoknr))
		  {	$hoknr = $hk['hoknr'];	}

if(isset($hoknr)) {
 if(isset($txtKg) && isset($fldInk)) {
		$goed = "$hoknr is per $sluitdm afgesloten incl. voer."; }
 else { $goed = "$hoknr is per $sluitdm afgesloten excl. voer."; }

 //unset($sluitdm);
 unset($dmsluit);
 unset($txtKg); }
	
	} // EINDE if (!isset($fout) && isset($sluitdm)) ?>