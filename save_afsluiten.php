<!-- 30-11-2014 : voerId gewijzigd naar InkId  In tblVoeding worden de eenhId, prijs en btw niet meer gebruikt 
27-11-2015 : hernoemd van insVoer.php naar save_voer.php en views variabel gemaakt.
29-11-2015 : Bij $svwHistorieHok UNION toegevoegd omdat gebleven schapen in $svwHistorieHok wordt gebaseerd op de afsluitdm. In dit script hebben gebleven schapen op enig moment nog afsluitdm NULL
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel	22-1-2017 : tblBezetting gewijzigd naar tblBezet
2-3-2017 : bestand save_voer.php gesplitst in save_afsluiten1.php en save_afsluiten2.php 1 staat voor geboren en 2 voor gespeend
18-8-2019 : Loop gebouwd om steeds nieuw inkoopid aan te spreken indien nodig en de splitsing van drie save_afluiten1 2 3.php samengevoegd tot 1 bestand save_afsluiten.php 
21-9-2021 : Functie func_artikelnuttigen toegevoegd.

save_afsluiten.php toegpast in : 
	- HokAfsluiten.php  -->

<?php
include "func_artikelnuttigen.php";

// Controle op volldig ingevulde vleden
if((isset($txtKg) && !isset($fldArt)) || !isset($txtKg) && isset($fldArt)) { $fout = "Het voer is onvolledig ingevuld."; }
		  
else { // als voer volledig is ingevuld of geen voer is ingevuld

// ASLUITPERIODE BEPALEN $dmsluit is verplicht. Deze controle zit reeds in HokAfsluiten.php
// Zoek naar eerdere bestaande afsluitperiode
$zoek_periode = mysqli_query($db,"
SELECT periId
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$Id)."' and doelId = '".mysqli_real_escape_string($db,$doelId)."' and dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."'
") or die (mysqli_error($db));

	while( $zp = mysqli_fetch_assoc($zoek_periode)) { $lst_periId = $zp['periId']; }

if(isset($lst_periId)) { $fout = "Deze afsluitdatum bestaat al."; }

else {

$insert_tblPeriode = "INSERT INTO tblPeriode set hokId = '".mysqli_real_escape_string($db,$Id)."', doelId= '".mysqli_real_escape_string($db,$doelId)."', dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."' ";
/*echo $insert_tblPeriode.'<br>';*/		mysqli_query($db,$insert_tblPeriode) or die (mysqli_error($db));

$zoek_periId = mysqli_query ($db,"
SELECT periId
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$Id)."' and doelId= '".mysqli_real_escape_string($db,$doelId)."' and dmafsluit = '".mysqli_real_escape_string($db,$dmsluit)."'
") or die (mysqli_error($db));
	while ($pi = mysqli_fetch_assoc($zoek_periId)) { $periId = $pi['periId']; }
}
// EINDE ASLUITPERIODE BEPALEN

if(isset($periId)) {

if(isset($txtKg) && isset($fldArt)) {

$zoek_voorraad_artikel = mysqli_query($db," 
SELECT sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblInkoop i
 left join (
    SELECT v.inkId, sum(v.nutat*v.stdat) vbrat
    FROM tblVoeding v
     join tblInkoop i on (v.inkId = i.inkId)
    WHERE i.artId = '".mysqli_real_escape_string($db,$kzlVoer)."'
    GROUP BY v.inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '".mysqli_real_escape_string($db,$kzlVoer)."' and i.inkat-coalesce(n.vbrat,0) > 0
") or die (mysqli_error($db));

while ($zv = mysqli_fetch_array($zoek_voorraad_artikel))
{
   $vrdat = $zv['vrdat'];
}

if (isset($vrdat) && $vrdat < $txtKg) { $fout = "Er is onvoldoende voer op voorraad."; }
else {

inlezen_voer($db, $fldArt, $txtKg, NULL, $periId, NULL);


}
} // Einde if(isset($txtKg) && isset($fldArt))



	
$zoek_hoknr = mysqli_query($db,"SELECT hoknr FROM tblHok WHERE hokId = '".mysqli_real_escape_string($db,$Id)."' ") or die (mysqli_error($db));
	while($hk = mysqli_fetch_assoc($zoek_hoknr))
		  {	$hoknr = $hk['hoknr'];	}

if(isset($hoknr)) {
 if(isset($txtKg) && isset($fldArt)) {
		$goed = "$hoknr is per $sluitdm afgesloten incl. voer."; }
 else { $goed = "$hoknr is per $sluitdm afgesloten excl. voer."; }

 //unset($sluitdm);
 unset($dmsluit);
 unset($periId); 
 unset($txtKg); }
	
	} // EINDE if(isset($periId)) 
} // Einde als voer volledig is ingevuld of geen voer is ingevuld ?>
