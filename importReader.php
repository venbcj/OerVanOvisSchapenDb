<!-- // http://php.net/manual/en/mysqli.set-local-infile-handler.php 
4-11-2015 Bij de drie bewerkingen hieronder is de filter isnull(verwerkt) toegevoegd Online werd bij Rina per 16-8-2015 steeds opnieuw een regel ingelezen 
16-9-2016 : overschrijven van reader.txt gewijzigd in aanvullen 
13-11-2016 : scancode verwijderd. Dit had betrekking op moment 'uitval voor merken'. Dit moment wordt bepaald a.d.h.v. uivaldatum en geboortedatum. 'uitval voor merken' is in tblMoment daarom niet meer actief 
25-2-2018 : velden hernoemd en verplaatst zie C:\Users\Bas van de Ven\Documents\Development\SchapenM&W\NaarWebsite\Herinrichting2\New_tabellen\100_impReader.sql 
22-6-2018 : Velden in impReader aangepast 
7-3-2019 : uitvaldatum voor merken weer toegevoegd
2-2-2020 : De root naar alle bestanden op de FTP server variabel gemaakt 
26-2-2020 : LOAD DATA LOCAL INFILE vervangen door file() -->

<?php
include "url.php";

$velden = array('datum','tijd','levnr_geb','teller','rascode','geslacht','moeder','hokcode','gewicht','col10','col11','moment1','col13','moment2','levnr_uitv','teller_uitv','reden_uitv','levnr_afv','teller_afv','ubn_afv','afvoerkg','levnr_aanv','teller_aanv','ubn_aanv','levnr_sp','teller_sp','hok_sp','speenkg','moeder_dr','col30','uitslag','vader_dr','levnr_ovpl','teller_ovpl','hok_ovpl','reden_pil','levnr_pil','teller_pil','col39','col40','col41','weegkg','levnr_weeg','col44');

$stream = $end_file_reader; //Bestandsnaam bijv reader_1 . Gedeclareerd in InlezenReader.php
$lok = $Backupnaam; // Gedeclareerd in InlezenReader.php en uploadReader.php

$inhoud = file($lok); //Leest bestand uit //var_dump($inhoud);

$c = count($inhoud); /*Aantal regels uit bestand */ //echo '<br>'.'<br>'.$c.'<br>'.'<br>';

for($i=0;$i<$c;$i++){

$regel = explode(";", $inhoud[$i]); //var_dump($regel);

$cc = 44; //count($regel); // Aantal velden per regel zijn bij BurgM 46 elementen !! dan gaat het fout als $cc variabel is.

//echo'<br>';

for($ii=0; $ii<$cc; $ii++){

	if($ii == 0) { $insert_qry = " INSERT INTO impReader SET ".$velden[$ii]." = '".$regel[$ii]."'"; }
	else if($regel[$ii] == "" || $regel[$ii] == "0") {   $insert_qry .= ", ".$velden[$ii]." = NULL"; }
	else {   $insert_qry .= ", ".$velden[$ii]." = '".$regel[$ii]."'"; }
}

$insert_qry .= ', lidId = ' . $lidId . ';';

mysqli_query($db,$insert_qry) or die (mysqli_error($db)); /*echo $insert_qry; 
echo'<br><br><br><br>';*/

}
  
  

// VERWIJDEREN VAN HET BESTAND bijv. READER_1.TXT TUSSEN ALLE PHP BESTANDEN verplaatsen en hernoemen is gebeurd in uploadReader.php

$reader_aanwezig = file_exists($dir.'/'.$stream);

if ($reader_aanwezig == 1)
{ 
 $DelFile = $dir."/".$stream;
unlink($DelFile)or die ("Kan bestand ".$inputfile." niet verwijderen. " . mysqli_error($db));// verwijderd bestand
}
// EINDE VERWIJDEREN VAN HET BESTAND READER.TXT TUSSEN ALLE PHP BESTANDEN verplaatsen en hernoemen is gebeurd in uploadReader.php


	
/* Bij een geboren schaap kan 1 of 2 x uitval voor merken op 1 regel in het txt bestand staan. Er kunnen dus max drie schapen van 1 moeder op een regel staan waarvan er dan twee zijn overleden voor merken.
Als twee lammeren van 1 moeder uitvallen voor merken worden beiden lammeren geregistreerd op 1 moeder en op 1 regel/ record.  
Om er meerder records (max 3) van te maken volgt hier een bewerking van tabel impReader. */

// BEWERKING 1 : Eerst worden de 1 of 2 uitgevallen lammeren gescheiden van het geboren lam
$zoek_aantal_geborenENuitval = mysqli_query($db,"
select count(readId) aantid
from impReader
where lidId = ".mysqli_real_escape_string($db,$lidId)." and levnr_geb is not null and (moment1 is not null or moment2 is not null)
") or die (mysqli_error($db));
	while ($qrycntr = mysqli_fetch_assoc($zoek_aantal_geborenENuitval))

if (!empty($qrycntr['aantid']))	{
$ScheidUitvalVanGeboren = mysqli_query($db,"
select datum, tijd, teller, moeder, moment1, moment2 
from impReader 
where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(verwerkt) and levnr_geb is not null and (moment1 is not null or moment2 is not null)
order by teller
") or die (mysqli_error($db));
	while ($qryins = mysqli_fetch_assoc($ScheidUitvalVanGeboren)) {
$insertimpreader = mysqli_query($db,"
 INSERT INTO impReader SET datum = '$qryins[datum]', tijd = '$qryins[tijd]', teller = '$qryins[teller]', moeder = '$qryins[moeder]', 
	moment1 = '$qryins[moment1]', moment2 = '$qryins[moment2]',
	lidId = ".mysqli_real_escape_string($db,$lidId)." ;
") or die (mysqli_error($db));
			}
$GeborenLamUniekMaken = mysqli_query($db,"
UPDATE impReader SET moment1 = NULL, moment2 = NULL
where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(verwerkt) and levnr_geb is not null and (moment1 is not null or moment2 is not null)
") or die (mysqli_error($db));
			
		}
// EINDE BEWERKING 1 : Eerst worden de 1 of 2 uitgevallen lammeren gescheiden van het geboren lam
// BEWERKING 2 : Daarna worden twee uitgevallen lammeren gescheiden indien van toepassing.
$zoek_naar_2_uitval = mysqli_query($db,"
select count(readId) aantid
from impReader
where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(verwerkt) and isnull(levnr_geb) and moment2 is not null
") or die (mysqli_error($db));
	while ($dubl_do = mysqli_fetch_assoc($zoek_naar_2_uitval))
if (!empty($dubl_do['aantid']))	{ /* Als er waardes bestaan dan eerst nieuwe records invoegen daarna pas update query of te wel 
											lege velden moment1 vullen met moment2 !!  Dit wanneer wel moment2 is geregistreerd maar geen moment1 */
		
$zoek_2_uitval = mysqli_query($db,"
select readId, datum, tijd, teller, moeder, moment2
from impReader
where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(verwerkt) and isnull(levnr_geb) and moment2 is not null
order by teller
") or die (mysqli_error($db));
	while ($qry = mysqli_fetch_assoc($zoek_2_uitval))	
		{ $insert_impReader = mysqli_query($db,"INSERT INTO impReader SET datum = '$qry[datum]', tijd = '$qry[tijd]', teller = '$qry[teller]', moeder = '$qry[moeder]', moment1 = '$qry[moment2]', lidId = ".mysqli_real_escape_string($db,$lidId)."  ") or die (mysqli_error($db));
		$readId = $qry['readId'];
		  $update_impReader = mysqli_query($db,"UPDATE impReader SET moment2 = NULL WHERE readId = ".mysqli_real_escape_string($db,$readId)."  ") or die (mysqli_error($db));

		}

$bijwerkimpreader = mysqli_query($db,"
update impReader SET moment1 = moment2 where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(levnr_geb) and isnull(moment1)
") or die (mysqli_error($db));
// EINDE BEWERKING 2 : Daarna worden twee uitgevallen lammeren gescheiden indien van toepassing.										
}

if($modtech == 0) { // geboren lammeren zonder levensnummer mogen niet voorkomen als de module technisch niet wordt gebruikt
mysqli_query($db,"
update impReader SET verwerkt = 1 where lidId = ".mysqli_real_escape_string($db,$lidId)." and isnull(levnr_geb) and teller is not null and isnull(verwerkt)
") or die (mysqli_error($db));
}


// EINDE BEWERKING TABEL IMPREADER

?>
