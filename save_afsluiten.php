<?php

/*
<!-- 30-11-2014 : voerId gewijzigd naar InkId  In tblVoeding worden de eenhId, prijs en btw niet meer gebruikt
27-11-2015 : hernoemd van insVoer.php naar save_voer.php en views variabel gemaakt.
29-11-2015 : Bij $svwHistorieHok UNION toegevoegd omdat gebleven schapen in $svwHistorieHok wordt gebaseerd op de afsluitdm. In dit script hebben gebleven schapen op enig moment nog afsluitdm NULL
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel    22-1-2017 : tblBezetting gewijzigd naar tblBezet
2-3-2017 : bestand save_voer.php gesplitst in save_afsluiten1.php en save_afsluiten2.php 1 staat voor geboren en 2 voor gespeend
18-8-2019 : Loop gebouwd om steeds nieuw inkoopid aan te spreken indien nodig en de splitsing van drie save_afluiten1 2 3.php samengevoegd tot 1 bestand save_afsluiten.php
21-9-2021 : Functie func_artikelnuttigen toegevoegd.
save_afsluiten.php toegpast in :
    - HokAfsluiten.php  -->
*/
require_once "func_artikelnuttigen.php";
// Controle op volldig ingevulde vleden
if ((isset($txtKg) && !isset($fldArt)) || !isset($txtKg) && isset($fldArt)) {
    $fout = "Het voer is onvolledig ingevuld.";
} else { // als voer volledig is ingevuld of geen voer is ingevuld
// ASLUITPERIODE BEPALEN $dmsluit is verplicht. Deze controle zit reeds in HokAfsluiten.php
// Zoek naar eerdere bestaande afsluitperiode
    $periode_gateway = new PeriodeGateway();
    $lst_periId = $periode_gateway->findByHokAndDoel($Id, $doelId, $dmsluit);
    if (isset($lst_periId)) {
        $fout = "Deze afsluitdatum bestaat al.";
    } else {
        $periId = $periode_gateway->insert($Id, $doelId, $dmsluit);
    }
// EINDE ASLUITPERIODE BEPALEN
    if (isset($periId)) {
        if (isset($txtKg) && isset($fldArt)) {
            $inkoop_gateway = new InkoopGateway();
            $vrdat = $inkoop_gateway->zoek_voorraad_artikel($kzlVoer);
            if (isset($vrdat) && $vrdat < $txtKg) {
                $fout = "Er is onvoldoende voer op voorraad.";
            } else {
                inlezen_voer($db, $fldArt, $txtKg, null, $periId, null);
            }
        } // Einde if(isset($txtKg) && isset($fldArt))
        $hok_gateway = new HokGateway();
        $hoknr = $hok_gateway->findHoknrById($Id);
        if (isset($hoknr)) {
            if (isset($txtKg) && isset($fldArt)) {
                 $goed = "$hoknr is per $sluitdm afgesloten incl. voer.";
            } else {
                $goed = "$hoknr is per $sluitdm afgesloten excl. voer.";
            }
            unset($dmsluit);
            unset($periId);
            unset($txtKg);
        }
    } // EINDE if(isset($periId))
} // Einde als voer volledig is ingevuld of geen voer is ingevuld
