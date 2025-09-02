<?php /* 27-3-2015 :  Code om connectie te maken met de database van de klant verplaatst van Login.php naar passw.php. Dit ivm post_readerGeb.php. Zie daar de reden achter Inckude "connect_db.php";
6-4-2015 : Toelichtin op het veld alias in tblLeden : alias wordt gebruikt om op plekken de sepcifieke klant te benoemen. Bijv. de mapnaam voor reader.txt 
8-4-2015 : sql beveiligd 
18-12-2015 : $goed gewijzigd van $goed = "De inloggegevens zijn gewijzigd naar ".$wwnew ; naar $goed = "De inloggegevens zijn gewijzigd." ;
22-2-2017 : wachtwoord voor versleuteling aangevuld met 50 karakters */

 
// Toegepast in connect_db.php

include "url.php";


if (isset($_POST['knpLogin']) || isset($_POST['knpBasis']) || isset($_POST['knpBasis1']) || isset($_POST['knpBasis2'])) {
$passw = md5($_POST['txtPassw'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t'); } // wordt gebruikt bij login

if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]) && isset($_SESSION["A1"])) {

	$lid = $_SESSION["I1"];
	$login = $_SESSION["U1"];
	$passw = md5($_SESSION["W1"].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t'); // wordt gebruikt bij wachtwoordgegevens en is het wachtwoord van de klant
	$ww = $passw; // tbv value in veld txtOld binnen wachtwoord.php
}

// Code tbv login.php EN post_readerGeb.pgp
// Ophalen gegevens om connectie te kunnen maken met de database van een klant
/*
if (isset($_POST['knpLogin'])) { $username = $_POST['txtUser']; } else if (isset($_SESSION["U1"])) { $username = $_SESSION["U1"]; }
if (isset($_POST['knpLogin'])  || (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]))	) {
    if ($db == false )    {   echo 'Connectie database mislukt';    }
	
	
 $result = mysqli_query($db_clients,"SELECT db, user_db, pw_db FROM tblKlanten WHERE login = '$username' and passw = '$passw' ") or die (mysqli_error($db_clients));
	while($row = mysqli_fetch_assoc($result))
			{
				$database = $row['db'];
				$user_db = $row['user_db'];
				$pw_db = $row['pw_db'];
			}
				$host = "localhost";
	}*/
 // Einde Ophalen gegevens om connectie te kunnen maken met de database van een klant LET OP Deze code zit ook in post_readerGeb.php Zie daar voor de reden !!
// Einde Code tbv login.php EN post_readerGeb.pgp






// CODE T.B.V. WIJZIGEN WACHTWOORD
if ($curr_url == $url."Wachtwoord.php") // $curr_url gedeclareerd is url.php
{
$veld = "submit"; 
if (isset($_POST['knpChange'])) {

$zoek_login = mysqli_query ($db,"
SELECT login, passw
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lid)."'
") or die (mysqli_error($db));
	while( $li = mysqli_fetch_assoc($zoek_login)) { $user_db = $li['login']; $passw_db = $li['passw']; }

$txtuser = "$_POST[txtUser]";
$txtuserold = "$_POST[txtUserOld]";
$wwold = "$_POST[txtOld]";
$ww = md5($_POST['txtOld'].'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');
$txtpassw = "$_POST[txtNew]";
$wwnew = md5($txtpassw.'zfO3puW?Wod/UT<-|=)1VT]+{hgABEK(Yh^!Wv;5{ja{P~wX4t');


	
	if( empty($txtuser) || empty($_POST['txtOld']) ) { $fout = "Gebruikersnaam of wachtwoord is onbekend."; unset($ww);}
	//else if(empty($txtpassw)) { $fout = "Nieuw wachtwoord is leeg"; }
	else if( $txtpassw <> $_POST['txtBevest']) {	$fout = "Het nieuwe wachtwoord komt niet overeen met de bevestiging.";	unset($ww); }
	else if( $ww <> $passw && $_POST['txtOld'] <> $passw) {	$fout = "Het oude wachtwoord is onjuist.";	unset($ww); }
	else if ( !empty($txtpassw) && strlen($txtpassw)< 6) {	$fout = "Het wachtwoord moet uit minstens 6 karakters bestaan.";	unset($ww); }

	//else if( $txtuser == $txtuserold && $wwold == $passw ) {	unset($ww);	}
	else {
	// controle of combinatie tussen user en passw al bestaat
	if(empty($txtpassw)) { $wwnew = $ww; }
	 $count = mysqli_query($db,"SELECT login, passw FROM tblLeden 
								WHERE login = '".mysqli_real_escape_string($db,$txtuser)."' 
								  and passw = '".mysqli_real_escape_string($db,$wwnew)."' ") or die (mysqli_error($db));
	$num_rows = mysqli_num_rows($count);
		if ($num_rows > 0) {	$fout = "Deze combinatie tussen gebruikersnaam en wachtwoord bestaat al. Kies een andere combinatie.";	}
	// EINDE controle of combinatie tussen user en passw al bestaat
		else {
	// username en wachtwoord wijzigen
		/*if ($txtuser <> $_POST['txtUserOld'] && 
			!empty($_POST['txtOld']) && !empty($txtpassw) && !empty($_POST['txtBevest']))  {
		
		$updateUW = "UPDATE tblLeden SET login = '".mysqli_real_escape_string($db,$txtuser)."', 
										 passw = '".mysqli_real_escape_string($db,$wwnew)."' 
					WHERE login = '".mysqli_real_escape_string($db,$login)."' 
					  and passw = '".mysqli_real_escape_string($db,$passw)."' ";
			mysqli_query($db, $updateUW) or die (mysqli_error($db));	
		$_SESSION["U1"] = $txtuser; /* tbv de query $result in login.php*/
		//$passw = $wwnew; /*tbv de query $result in login.php */ 
		//$_SESSION["W1"] = $txtpassw; /* tbv (nieuwe) sessie gegevens */  
		
		/*$goed = "De inloggegevens zijn gewijzigd";
		 $veld = "hidden";  }*/
	// username wijzigen
		/*else*/ if ($txtuser <> $user_db)  {
		$updateUS = "UPDATE tblLeden SET login = '".mysqli_real_escape_string($db,$txtuser)."' 
					 WHERE lidId = '".mysqli_real_escape_string($db,$lid)."' ";
			mysqli_query($db, $updateUS) or die (mysqli_error($db));
		$_SESSION["U1"] = $txtuser; /* tbv de query $result in login.php*/	
		
		$goed = "De inloggegevens zijn gewijzigd";
		 $veld = "hidden"; }
	// wachtwoord wijzigen
		else if (isset($wwnew) && $passw_db <> $wwnew)  {

			$updateWW = "UPDATE tblLeden SET passw = '".mysqli_real_escape_string($db,$wwnew)."' 
						 WHERE lidId = '".mysqli_real_escape_string($db,$lid)."' ";
			/*echo $updateWW.'<br>';*/ mysqli_query($db, $updateWW) or die (mysqli_error($db));	
		$passw = $wwnew; /*tbv de query $result in login.php */ 
		$_SESSION["W1"] = $txtpassw; /* tbv (nieuwe) sessie gegevens */  
		
		$goed = "De inloggegevens zijn gewijzigd." ;
		 $veld = "hidden"; }
		
			}
		 }
	
	if (isset($fout)) { unset($ww); } 	}
}
// EINDE CODE T.B.V. WIJZIGEN WACHTWOORD
 ?>
