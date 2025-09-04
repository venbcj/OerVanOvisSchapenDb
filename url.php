
<?php
// 20-12-2019 : http gewijzigd naar https
/* Naast verderop genoemde include bestanden toegepast in :
	- Eenheden.php
	- InsAanwas.php
	- InsAfleveren.php
	- InsGeboortes.php Ivm noodzakelijk voor include "header.php";
	- InsMedicijn.php
	- InsOverplaats.php
	- InsSpenen.php
	- InsUitval.php
	- Leveranciers.php
	- Uitval.php
	- Voer.php
	
*/

#$url = "http://localhost:8080/Schapendb/";
if ($_SERVER['HTTP_HOST'] == 'localhost:8080') {	$url = 'http://'.$_SERVER['HTTP_HOST'].'/Schapendb/'; } else {	$url = 'https://'.$_SERVER['HTTP_HOST'].'/';	}
#$url = "http://testapp.masterwebsite.nl/";
if (php_uname('n') == 'basq') {
    $url = 'http://oer-dev/';
}

// Include bestanden
 $curr_url = 'https://'.$_SERVER['HTTP_HOST'].strtok($_SERVER["REQUEST_URI"],'?'); // strtok zorgt ervoor dat alles na de paginanaam wordt verwijderd. bron : http://stackoverflow.com/questions/6969645/how-to-remove-the-querystring-and-get-only-the-url
 if ($curr_url == $url."connect_db.php"
 || $curr_url == $url."header.php" // Dit bestand komt in de meeste scripts voor en zorgt ervoor dat variabele $url is gedeclareerd. Mn. in hyperlinks
 || $curr_url == $url."importReader.php"
 || $curr_url == $url."importRespons.php"
 || $curr_url == $url."kzl.php"
 || $curr_url == $url."login.php"
 || $curr_url == $url."maak_Request.php"
 || $curr_url == $url."menu1.php"
 || $curr_url == $url."menuBeheer.php"
 || $curr_url == $url."menuFinance.php"
 || $curr_url == $url."menuInkoop.php"
 || $curr_url == $url."menuRapport.php"
 || $curr_url == $url."msg.php"
 || $curr_url == $url."passw.php"
 || $curr_url == $url."post_readerAanw.php"
 || $curr_url == $url."post_readerAflev.php"
 || $curr_url == $url."post_readerGeb.php"
 || $curr_url == $url."post_readerMed.php"
 || $curr_url == $url."post_readerOvp.php"
 || $curr_url == $url."post_readerSpn.php"
 || $curr_url == $url."post_readerUitv.php"
 || $curr_url == $url."responscheck.php"
 || $curr_url == $url."titel.php"
 || $curr_url == $url."uploadReader.php"
 || $curr_url == $url."url.php"
 || $curr_url == $url."vw_Reader.php"
 ) 
 { header("Location: ".$url."index.php"); }
?>
