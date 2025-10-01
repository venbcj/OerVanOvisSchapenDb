<?php
/* 27-3-2015 :  Code om connectie te maken met de database van de klant verplaatst van Login.php naar passw.php. Dit ivm post_readerGeb.php. Zie daar de reden achter Inckude "connect_db.php";
6-4-2015 : Toelichtin op het veld alias in tblLeden : alias wordt gebruikt om op plekken de sepcifieke klant te benoemen. Bijv. de mapnaam voor reader.txt
8-4-2015 : sql beveiligd
18-12-2015 : goed gewijzigd van goed = "De inloggegevens zijn gewijzigd naar ".wwnew ; naar goed = "De inloggegevens zijn gewijzigd." ;
22-2-2017 : wachtwoord voor versleuteling aangevuld met 50 karakters
 */
// Toegepast in connect_db.php

include "url.php";
global $passw, $veld;
if (isset($_POST['knpLogin']) || isset($_POST['knpBasis']) || isset($_POST['knpBasis1']) || isset($_POST['knpBasis2'])) {
    $passw = md5($_POST['txtPassw'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
} // wordt gebruikt bij login

$lid = null;
if (Auth::is_logged_in() && (Session::isset('A1'))) {
    $lid = Session::get('I1');
    # TODO: #0004123 in login.php wordt $login (note: wordt niet gebruikt) ook al gezet op de U1-sleutel uit sessie. Waarom nu weer? --BCB
    $login = Session::get('U1');
    $passw = md5(Session::get('W1').'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t'); // wordt gebruikt bij wachtwoordgegevens en is het wachtwoord van de klant
    $ww = $passw; // tbv value in veld txtOld binnen wachtwoord.php
}

