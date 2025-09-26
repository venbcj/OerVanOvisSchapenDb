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
    Logger::debug('*** passw uit post');
    $passw = md5($_POST['txtPassw'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
} // wordt gebruikt bij login

if (Auth::is_logged_in() && (Session::isset("A1"))) {
    $lid = Session::get("I1");
    # TODO: #0004123 in login.php wordt $login (note: wordt niet gebruikt) ook al gezet op de U1-sleutel uit sessie. Waarom nu weer? --BCB
    $login = Session::get("U1");
    Logger::debug('*** passw uit sessie');
    $passw = md5(Session::get("W1").'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t'); // wordt gebruikt bij wachtwoordgegevens en is het wachtwoord van de klant
    $ww = $passw; // tbv value in veld txtOld binnen wachtwoord.php
}

// Code tbv login.php EN post_readerGeb.pgp
// Ophalen gegevens om connectie te kunnen maken met de database van een klant
/*
if (isset($_POST['knpLogin'])) {
    $username = $_POST['txtUser'];
} else if ((Session::isset("U1"))) {
    $username = Session::get("U1");
}
if (isset($_POST['knpLogin']) || Auth::is_logged_in()) ) {
    if ($db == false ) {
        echo 'Connectie database mislukt';
    }
    $result = mysqli_query($db_clients,"SELECT db, user_db, pw_db FROM tblKlanten WHERE login = '$username' and passw = '$passw' ") or die (mysqli_error($db_clients));
    while($row = mysqli_fetch_assoc($result)) {
        $database = $row['db'];
        $user_db = $row['user_db'];
        $pw_db = $row['pw_db'];
    }
    $host = "localhost";
}
 */
 // Einde Ophalen gegevens om connectie te kunnen maken met de database van een klant LET OP Deze code zit ook in post_readerGeb.php Zie daar voor de reden !!
// Einde Code tbv login.php EN post_readerGeb.pgp
// CODE T.B.V. WIJZIGEN WACHTWOORD
# TODO: (BV) #0004124 als dit toch alleen mag in Wachtwoord... waarom dan niet opnemen in Wachtwoord? --BCB
if ($curr_url == $url."Wachtwoord.php") { // $curr_url gedeclareerd is url.php
    $veld = "submit";
    if (isset($_POST['knpChange'])) {
        $zoek_login = mysqli_query($db, "
SELECT login, passw
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db, $lid)."'
") or die(mysqli_error($db));
        while ($li = mysqli_fetch_assoc($zoek_login)) {
            $user_db = $li['login'];
            $passw_db = $li['passw'];
        }
        $txtuser = "$_POST[txtUser]";
        $txtuserold = "$_POST[txtUserOld]";
        $wwold = "$_POST[txtOld]";
        $ww = md5($_POST['txtOld'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
        $txtpassw = "$_POST[txtNew]";
        $wwnew = md5($txtpassw.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
        if (empty($txtuser) || empty($_POST['txtOld'])) {
            $fout = "Gebruikersnaam of wachtwoord is onbekend.";
            unset($ww);
        //} elseif (empty($txtpassw)) {
        // $fout = "Nieuw wachtwoord is leeg";
        } elseif ($txtpassw <> $_POST['txtBevest']) {
            $fout = "Het nieuwe wachtwoord komt niet overeen met de bevestiging.";
            unset($ww);
        } elseif ($ww <> $passw && $_POST['txtOld'] <> $passw) {
            $fout = "Het oude wachtwoord is onjuist.";
            unset($ww);
        } elseif (!empty($txtpassw) && strlen($txtpassw)< 6) {
            $fout = "Het wachtwoord moet uit minstens 6 karakters bestaan.";
            unset($ww);
        //} elseif ($txtuser == $txtuserold && $wwold == $passw) {
        //  unset($ww);
        } else {
            // controle of combinatie tussen user en passw al bestaat
            if (empty($txtpassw)) {
                $wwnew = $ww;
            }
            echo "user $txtuser password $wwnew";
            $count = mysqli_query($db, "SELECT login, passw FROM tblLeden 
                WHERE login = '".mysqli_real_escape_string($db, $txtuser)."' 
                and passw = '".mysqli_real_escape_string($db, $wwnew)."' ") or die(mysqli_error($db));
            $num_rows = mysqli_num_rows($count);
            if ($num_rows > 0) {
                $fout = "Deze combinatie tussen gebruikersnaam en wachtwoord bestaat al. Kies een andere combinatie.";
            } else {
            // EINDE controle of combinatie tussen user en passw al bestaat
                // username en wachtwoord wijzigen
                if ($txtuser <> $user_db) {
                // username wijzigen
                    $updateUS = "UPDATE tblLeden SET login = '".mysqli_real_escape_string($db, $txtuser)."' 
                        WHERE lidId = '".mysqli_real_escape_string($db, $lid)."' ";
                    mysqli_query($db, $updateUS) or die(mysqli_error($db));
                    Session::set("U1", $txtuser); /* tbv de query $result in login.php*/
                    $goed = "De inloggegevens zijn gewijzigd";
                    $veld = "hidden";
                } elseif (isset($wwnew) && $passw_db <> $wwnew) {
                    // wachtwoord wijzigen
                    $updateWW = "UPDATE tblLeden SET passw = '".mysqli_real_escape_string($db, $wwnew)."' 
                        WHERE lidId = '".mysqli_real_escape_string($db, $lid)."' ";
                    /*echo $updateWW.'<br>';*/ mysqli_query($db, $updateWW) or die(mysqli_error($db));
                    Logger::debug('*** passw uit wijziging');
                    $passw = $wwnew; /*tbv de query $result in login.php */
                    Session::set("W1", $txtpassw); /* tbv (nieuwe) sessie gegevens */
                    $goed = "De inloggegevens zijn gewijzigd." ;
                    $veld = "hidden";
                }
            }
        }
        if (isset($fout)) {
            unset($ww);
        }
    }
}
// EINDE CODE T.B.V. WIJZIGEN WACHTWOORD
