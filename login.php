 <!-- 8-4-2015 : sql beveiligd 
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
16-01-2021 : function db_quote toegevoegd -->
 <?php
//include "url.php"; Deze include zit ook al in include header.php
Include "connect_db.php";  //inclusief include "passw.php" => "url.php"

function db_null_input($var){
global $db;
//Evt kun je ook meteen is_boolean($var) omzetten naar 0/1, enz
return $var === null || empty($var) ? 'NULL' : "'" . mysqli_real_escape_string($db,$var) . "'";
}

function db_null_filter($field, $var){
global $db;
//Evt kun je ook meteen is_boolean($var) omzetten naar 0/1, enz
return $var === null || empty($var) ? "ISNULL(" . $field . ")" : $field . " = '" . mysqli_real_escape_string($db,$var) . "'";
}

 // *** ALS NIET IS INGELOGD ***
 if (!isset($_SESSION["U1"]) || !isset($_SESSION["W1"]) || !isset($_SESSION["I1"]) ) {	
  
 // destroy the session 
session_destroy();

 if (isset($_POST['knpLogin']) || isset($_POST['knpBasis']) ) {
$qrylidId = mysqli_query($db,"
SELECT lidId, alias, ubn, tech, fin, meld
FROM tblLeden 
WHERE login = '".mysqli_real_escape_string($db,$_POST['txtUser'])."' and passw = '".mysqli_real_escape_string($db,$passw)."' ;
") or die (mysqli_error($db));
	while($row = mysqli_fetch_assoc($qrylidId))
			{
				$lId = $row['lidId'];
				$ali = $row['alias'];
				$UBN = $row['ubn'];
				$modtech = $row['tech']; // Nodig bij demo_usercreate.php als wordt ingelogd. Dan bestaat $modtech hieronder nl. nog niet !!
				$modfin = $row['fin']; // Nodig bij menu1.php als wordt ingelogd. Dan bestaat $modfin hieronder nl. nog niet !!
				$modmeld = $row['meld']; // Nodig bij menu1.php als wordt ingelogd. Dan bestaat $modmeld hieronder nl. nog niet !!
			}	
			
if (mysqli_num_rows($qrylidId) == 0)
{ ?>

<form method="POST" action=" <?php echo $file; ?> "> <!-- $file veranderen in $from_action -->
 <p><input type="text" name="txtUser" size="20"></br>
 <input type="password" name="txtPassw" size="20"></br>
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
 $_SESSION["UB"] = $UBN;
 $_SESSION["PA"] = 1;
 $_SESSION["RPP"] = 30; // standaard aantal regels per pagina
 $_SESSION["ID"] = 0; // het Id waarmee de pagina is geopend. Bijv. hokId 1559 bij HokAfleveren.php
 $_SESSION["DT1"] = NULL; // Als (records per) pagina wordt ververst wordt datum onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw datum te kiezen. Zie HokAfleveren.php
 $_SESSION["BST"] = NULL; // Als (records per) pagina wordt ververst wordt bestemming onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokAfleveren.php

//echo "Session variables are set.";
	$login = $_SESSION["U1"];
	$lidId = $_SESSION["I1"];
	$alias = $_SESSION["A1"];
	$ubn = $_SESSION["UB"];
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
 } 
}
else
{ echo "<br/>"."U bent niet ingelogd "; ?>
 <form method="POST" action=" <?php echo $file; ?> ">
<table border = 0 align = center>
<tr align = center>
<td colspan = 3>
 <input type="text" name="txtUser" size="20"></br>
</td>
</tr>
<tr align = center>
<td colspan = 3>
 <input type="password" name="txtPassw" size="20"></br>
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
</table>
 </form>
 
<?php 


} 
 
 }  // *** EINDE ALS NIET IS INGELOGD ***
	// ***     ALS WEL IS INGELOGD     ***
 else if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {
 
 	$login = $_SESSION["U1"];
	$lidId = $_SESSION["I1"];
	//$alias = $_SESSION["A1"];
	$ubn = $_SESSION["UB"];
	$pag = $_SESSION["PA"];
	$RPP = $_SESSION["RPP"];
	
	date_default_timezone_set('Europe/Paris');

// Bepalen modules ja of nee
$module = mysqli_query($db,"SELECT beheer, tech, fin, meld FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'; ") or die (mysqli_error($db));
	while ($mod = mysqli_fetch_assoc($module)) { $modbeheer = $mod['beheer']; $modtech = $mod['tech']; $modfin = $mod['fin']; $modmeld = $mod['meld']; }

// Bepalen aantal karakters werknr 
$result = mysqli_query ($db,"SELECT kar_werknr FROM tblLeden WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."';") or die (mysqli_error($db));
	while ($row = mysqli_fetch_assoc($result))
		{ $Karwerk = $row['kar_werknr']; } 
	$w_werknr = 25+(8*$Karwerk); 

// Bepalen aantal karakter verblijf
$max_lengte = mysqli_query($db,"SELECT max(length(hoknr)) lengte FROM`tblHok`WHERE lidId ='".mysqli_real_escape_string($db,$lidId)."' ") or die (mysqli_error($db));
	while( $max = mysqli_fetch_assoc($max_lengte)) { $lengte = $max['lengte']; }
	$w_hok = 12+(8*$lengte); if($w_hok < 60) { $w_hok = 60; }

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
 
 #echo "gebruikersnaam " . $_SESSION["U1"] . " wachtwoord " . $_SESSION["W1"] . " lidId " . $_SESSION["I1"] . " alias " . $_SESSION["A1"] ."<br>";
 //echo phpversion();
 }
// ***     EINDE ALS WEL IS INGELOGD     ***
?>

