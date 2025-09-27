 <?php /* 8-4-2015 : sql beveiligd 
23-11-2015 : Berekening breddte kzlWerknr toegevoegd en query en berekening kzlHoknr toegevoegd	13-2-2017 : breedte kan niet kleiner zijn dan 60
3-12-2015 : $ubn aan sessie toegevoegd 
12-12-2015 : naast Id ook ubn rendac opgevragd 
19-12-2015 : $modfin toegevoegd 
16-09-2016 : modules gesplitst 
29-10-2016 : query modules bij inloggen toegevoegd zodat menu1.php goed wordt opgebouwd bij alleen melden 
27-07-2017 : $modbeheer toegevoegd 
18-03-2018 : $_SESSION["PA"]; en $_SESSION["RPP"]; toegevoegd.
13-05-2018 : $_SESSION["ID"]  $_SESSION["DT1"]  $_SESSION["BST"] toegevoegd
15-03-2020 : gebruik van welke reader toegevoegd 
16-01-2021 : function db_quote toegevoegd 
12-08-2023 : include "basisfuncties.php"; toegevoegd en alle functions daar naar verplaatst 
24-10-2023 : $zoek_laatste_versie toegevoegd 26-10-2023 $update_tblLeden toegevoegd
12-01-2024 : $_SESSION["KZ"]; toegevoegd. 14-01-2024 controle toegevoegd op juiste connectie met de database 
09-11-2024 : $w_hok = 12+(8*$lengte); gewijzigd naar $w_hok = 15+(9*$lengte); 
04-01-2025 : include header.php en include header_logout.php hier in geplaatst 
23-02-2025 : $_SESSION["Fase"] en $_SESSION["CNT"] toegevoegd
15-07-2015 : $ubn uit sessie gehaald omdat er per 10-7-2025 meerdere ubn's bij 1 gebruiker kunnen bestaan. */

include "url.php";
Include "connect_db.php";  //inclusief include "passw.php" => "url.php"
require_once("basisfuncties.php");

/*if($_SERVER["REQUEST_URI"] == '/Stallijst.php') { $Header = "header_search.php"; }
	else {*/ $Header = "header.php"; #}

  //$host = "localhost"; $user = "bvdvschaapovis"; $pw = "MSenWL44"; $dtb = $db_p;
if(($url == 'https://test.oervanovis.nl/' || $url == 'https://demo.oervanovis.nl/') && $dtb == 'k36098_bvdvSchapenDb') { ?> <h3 style="color : red ;"> PAS OP : Er is connectie met de productiedatabase ! </h3> <?php }

 // *** ALS NIET IS INGELOGD ***
 if (!isset($_SESSION["U1"]) || !isset($_SESSION["W1"]) || !isset($_SESSION["I1"]) ) {
  
 // destroy the session 
session_destroy();

 if (isset($_POST['knpLogin']) || isset($_POST['knpBasis']) ) {
$qrylidId = mysqli_query($db,"
SELECT lidId, alias, tech, fin, meld
FROM tblLeden 
WHERE login = '".mysqli_real_escape_string($db,$_POST['txtUser'])."' and passw = '".mysqli_real_escape_string($db,$passw)."' ;
") or die (mysqli_error($db));
	while($row = mysqli_fetch_assoc($qrylidId))
			{
				$lId = $row['lidId'];
				$ali = $row['alias'];
				//$UBN = $row['ubn']; // Per 10-7-2025 kunnen er meerdere ubn's aan 1 gebruiker zijn gekoppeld
				$modtech = $row['tech']; // Nodig bij demo_usercreate.php als wordt ingelogd. Dan bestaat $modtech hieronder nl. nog niet !!
				$modfin = $row['fin']; // Nodig bij menu1.php als wordt ingelogd. Dan bestaat $modfin hieronder nl. nog niet !!
				$modmeld = $row['meld']; // Nodig bij menu1.php als wordt ingelogd. Dan bestaat $modmeld hieronder nl. nog niet !!
			}	
			
if (mysqli_num_rows($qrylidId) == 0)
{ 
	Include "header_logout.php";
 echo '<br>';
 echo '<br>';
 echo '<br>';
 echo '<br>';
 echo '<br>'; ?>

<form method="POST" action=" <?php echo $file; ?> "> <!-- $file veranderen in $from_action -->
 <p><input type="text" name="txtUser" size="20"><br>
 <input type="password" name="txtPassw" size="20"><br>
 <input type="submit" value="Inloggen" name="knpLogin"></p>
 </form>
 <?php         echo "Gebruikersnaam of wachtwoord onjuist !"; 
          }
 else 
 { 

 session_start();
 
 $_SESSION["U1"] = "$_POST[txtUser]";
 $_SESSION["W1"] = "$_POST[txtPassw]";
 $_SESSION["I1"] = $lId;
 $_SESSION["A1"] = $ali;
 $_SESSION["PA"] = 1;
 $_SESSION["RPP"] = 30; // standaard aantal regels per pagina
 $_SESSION["ID"] = 0; // het Id waarmee de pagina is geopend. Bijv. hokId 1559 bij HokAfleveren.php
 $_SESSION["DT1"] = NULL; // Als (records per) pagina wordt ververst wordt datum onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw datum te kiezen. Zie HokAfleveren.php
 $_SESSION["BST"] = NULL; // Als (records per) pagina wordt ververst wordt bestemming onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokAfleveren.php
 $_SESSION["Fase"] = NULL; // Als (records per) pagina wordt ververst wordt fase onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokUitscharen.php (HokAfleveren.php)
 $_SESSION['KZ'] = NULL; // Als pagina wordt ververst wordt de keuze (filter) onthouden. Zie HokOverpl.php
 $_SESSION["CNT"] = NULL; // Gebruikt in Contact.php
//echo "Session variables are set.";
	$login = $_SESSION["U1"];
	$lidId = $_SESSION["I1"];
	$alias = $_SESSION["A1"];
	$pag = $_SESSION["PA"]; // paginanummer dat moet worden ontouden als de pagina wordt ververst
	$RPP = $_SESSION["RPP"]; // standaard aantal regels per pagina
	//$ID = $_SESSION["ID"]; // het Id waarmee de pagina is geopend. Bijv. hokId 1559 bij HokAfleveren.php

// In de demo omgeving worden de basis gegevens elke maand opnieuw vervangen.
if($dtb == "k36098_bvdvschapendbs" && $lidId > 1) {
 // Kijken of maand is verstreken o.b.v. createdatum in tabl tblSchapen
$maand_voorbij = mysqli_query($db,"SELECT date_format(min(st.dmcreatie),'%Y%m') maand FROM tblStal st WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

while ( $ym = mysqli_fetch_assoc($maand_voorbij)) {	$controle_maand = $ym['maand'];	}

$huidige_maand = date('Ym');

if($controle_maand < $huidige_maand && $lidId <> 1) 
	{
	include "demo_table_delete.php";
	include "demo_table_insert.php";
	}

}
// Einde In de demo omgeving worden de basis gegevens elke maand opnieuw vervangen.

if(isset($_POST['knpBasis'])) { include "demo_userdelete.php";  include "demo_table_insert.php"; }


// Bepalen modules ja of nee t.b.v. menu1.php bij inloggen
$module = mysqli_query($db,"SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'; ") or die (mysqli_error($db));
	while ($mod = mysqli_fetch_assoc($module)) { $modbeheer = $mod['beheer']; $modtech = $mod['tech']; $modfin = $mod['fin']; $modmeld = $mod['meld']; }
	
if (isset($menu)) { /*Include "connect_db.php";*/ include $menu; } // $menu is gedeclareerd in index.php. Als $menu bestaat en index.php dus actief moet het menu woden getoond na inloggen 
 

// Laatste inlog vastleggen
// $today is gedeclareerd in basisfuncties.php
$update_tblLeden = " UPDATE tblLeden set laatste_inlog = '".mysqli_real_escape_string($db,$nu)."' WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' ";

	mysqli_query($db,$update_tblLeden) or die(mysqli_error($db));


 Include "$Header";
 } 
}
else {
Include "header_logout.php";
 echo '<br>'; ?>
 <form method="POST" action=" <?php echo $file; ?> ">
<table border = 0 align = center>
<tr align = center>
 <td colspan = 3> Je bent niet ingelogd
 </td>
</tr>

<?php 
$pagina_naam = strtok($_SERVER["REQUEST_URI"],'?');
if($pagina_naam == '/index.php') { ?>

<tr align = center>
 <td colspan = 3>
	<input type="text" name="txtUser" size="20"><br>
 </td>
</tr>
<tr align = center>
 <td colspan = 3>
	<input type="password" name="txtPassw" size="20"><br>
 </td>
</tr>
<tr align = center>
<td width = 300></td>
 <td>
	<input type="submit" value="Inloggen" name="knpLogin">
 </td>
 <td width = 300>
<?php if($url == 'https://test.oervanovis.nl/' || $url == 'http://localhost:8080/Schapendb/') { ?>
	<input type="submit" value="Basisgegevens" name="knpBasis"> <?php } ?>
 </td>
</tr>
<?php } ?>

</table>
 </form>
 
<?php 


} 
 
 }  // *** EINDE ALS NIET IS INGELOGD ***
	// ***     ALS WEL IS INGELOGD    ***
 else if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {
 
 	$login = $_SESSION["U1"];
	$lidId = $_SESSION["I1"];
	//$alias = $_SESSION["A1"];
	$pag = $_SESSION["PA"];
	$RPP = $_SESSION["RPP"];
	
	date_default_timezone_set('Europe/Paris');

// Bepalen modules ja of nee
$module = mysqli_query($db,"SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'; ") or die (mysqli_error($db));
	while ($mod = mysqli_fetch_assoc($module)) { $modbeheer = $mod['beheer']; $modtech = $mod['tech']; $modfin = $mod['fin']; $modmeld = $mod['meld']; }

	Include "$Header";

// Bepalen aantal karakters werknr 
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
	while ($row = mysqli_fetch_assoc($result))
		{ $Karwerk = $row['kar_werknr']; } 
	$w_werknr = 25+(8*$Karwerk); 

// Bepalen aantal karakter verblijf
$max_lengte = mysqli_query($db,"SELECT max(length(hoknr)) lengte FROM`tblHok`WHERE lidId ='".mysqli_real_escape_string($db,$lidId)."' ") or die (mysqli_error($db));
	while( $max = mysqli_fetch_assoc($max_lengte)) { $lengte = $max['lengte']; }
	$w_hok = 15+(9*$lengte); if($w_hok < 60) { $w_hok = 60; }

// Bepalen Id van crediteur ophalen dode dieren (Rendac)
$qryRendac = mysqli_query ($db,"
	SELECT r.relId, p.ubn 
	FROM tblPartij p
	 join tblRelatie r on (p.partId = r.partId)
	WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.uitval = 1;") or die (mysqli_error($db));
	while ($ren = mysqli_fetch_assoc($qryRendac))
		{	$rendac_Id = $ren['relId'];	$rendac_ubn = $ren['ubn'];	}

// Bepalen welke reader wordt gebruikt 
$result = mysqli_query ($db,"SELECT reader FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' ;") or die (mysqli_error($db));
	while ($row = mysqli_fetch_assoc($result))
		{	$reader = $row['reader'];	} 

// Bepalen welke versie de laatste is. Wanneer deze nog niet is geinstalleerd wordt Beheer in menu1.php rood en ook Readerversies in menuBeheer.php

$dir = dirname(__FILE__); // Locatie bestanden op FTP server
$persoonlijke_map = $dir.'/user_'.$lidId;

/* Eerste query zoek alleen readerApp versies
Tweede query zoek naar readerApp versie i.c.m. taakversies 
Derde query zoek naar alleen taakversies */
$zoek_laatste_versie = mysqli_query($db,"
SELECT max(Id) lstId
FROM (
	SELECT a.Id
	FROM tblVersiebeheer a
	 left join tblVersiebeheer t on (a.Id = t.versieId)
	WHERE a.app = 'App' and isnull(t.Id)

	UNION
	SELECT a.Id
	FROM tblVersiebeheer a
	 join tblVersiebeheer t on (a.Id = t.versieId)
	WHERE a.app = 'App'

	UNION

	SELECT Id
	FROM tblVersiebeheer 
	WHERE app = 'Reader' and isnull(versieId)
 ) a
") or die (mysqli_error($db));

	while ( $zlv = mysqli_fetch_assoc($zoek_laatste_versie)) { $last_versieId = $zlv['lstId']; }

$zoek_readersetup_in_laatste_versie =  mysqli_query($db,"
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'App' and Id = '".mysqli_real_escape_string($db,$last_versieId)."'
") or die (mysqli_error($db));

	while ( $zrv = mysqli_fetch_assoc($zoek_readersetup_in_laatste_versie)) { 
		$Readersetup_bestand = $zrv['bestand']; 
	}

if(isset($Readersetup_bestand)) {
$appfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readersetup_bestand);
} else { $appfile_exists = 1; }

$zoek_readertaken_in_laatste_versie =  mysqli_query($db,"
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'Reader' and (Id = '".mysqli_real_escape_string($db,$last_versieId)."' or versieId = '".mysqli_real_escape_string($db,$last_versieId)."')
") or die (mysqli_error($db));

	while ( $zrv = mysqli_fetch_assoc($zoek_readertaken_in_laatste_versie)) { 
		$Readertaken_bestand = $zrv['bestand']; 
	}

if(isset($Readertaken_bestand)) {
$takenfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readertaken_bestand);
} else { $takenfile_exists = 1; }


if ($appfile_exists == 1 && $takenfile_exists == 1) { $actuele_versie = 'Ja'; }  
 
 #echo "gebruikersnaam " . $_SESSION["U1"] . " wachtwoord " . $_SESSION["W1"] . " lidId " . $_SESSION["I1"] . " alias " . $_SESSION["A1"] ."<br>";
 //echo phpversion();
 }
// ***     EINDE ALS WEL IS INGELOGD     ***
?>

