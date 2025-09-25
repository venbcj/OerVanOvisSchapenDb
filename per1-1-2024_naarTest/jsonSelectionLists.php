<?php /* 9-8-2020 : gemaakt 
12-02-2021 : keuzelijsten SterfteOorzaak en Doodgeboorte toegevoegd 
19-06-2021 : Keuzelijst voer toegevoegd 
18-12-2021 : Keuzelijst vaderdier toegevoegd 
31-12-2023 : nog enkele sql beveiligd met quotes */

include "connect_db.php";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

	exit();
}
else
{  // Begin van else

$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instantie
//var_dump($headers);
//echo is_array($string) ? 'dit is een array' : 'dit is geen array';
	

if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    Echo 'authorization header bestaat niet.';
    exit;
} else 
{

    $authorization = explode ( " ", $headers['Authorization'] );
 
 	if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {

		$zoek_lidId = mysqli_query($db, "SELECT lidId FROM tblLeden WHERE readerkey = '".mysqli_real_escape_string($db,$authorization[1])."'" ) or die(mysqli_error($db));

		$result = mysqli_fetch_array($zoek_lidId);

		if($result){
           $lidid = $result['lidId'];
		} else {
			http_response_code(401); // Unauthorized
			echo 'via authorization header wordt de gebruiker niet gevonden.';
	    	exit;
		}

	} else {
    	http_response_code(401); // Unauthorized
    	echo 'authorization header heeft niet de juiste opmaak.';
    	exit;
	}
}
 
// $lidid = 3;

switch ($_SERVER['REQUEST_METHOD']) { // Switch
	case 'GET':      

// Bepalen aantal karakters werknr 
$zoek_karwerk = mysqli_query ($db,"
SELECT kar_werknr
FROM tblLeden
WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."'
") or die (mysqli_error($db));
	while ($row = mysqli_fetch_assoc($zoek_karwerk))
		{ $karwerk = $row['kar_werknr']; } 


//$listname = array('Relatie', 'Worpverloop', 'lokatie');
$listname = array('Relatie', 'Worpverloop', 'Afvoerreden', 'SterfteOorzaak', 'Doodgeboorte', 'Groepen', 'RedenMedicijn', 'Ziekten', 'Behandelingen', 'Medicijnlijst', 'Lokatie', 'SoortBloedonderzoek', 'Behandelplan', 'Dekinfo', 'Ramcode', 'Kleurblok', 'Status', 'Rassenlijst', 'Voer' );
$countLists = count($listname);

for($i = 0; $i < $countLists; $i++) {

unset($result);

// Relaties
if($i == 0) {
$result = mysqli_query($db,"
SELECT ubn Id, naamreader `name`
FROM tblPartij p
 join tblRelatie r on (p.partId = r.partId)
WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."' and p.actief = 1 and r.actief = 1 and isnull(r.uitval) and ubn is not null
GROUP BY ubn, naamreader
ORDER BY naam
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Reden afvoer
if($i == 2) {
$result = mysqli_query($db,"
SELECT ru.redId Id, r.reden `name`
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidid)."' and ru.afvoer = 1 and r.actief = 1
ORDER BY reden
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Sterfte oorzaak bij taak Verplaatsing
if($i == 3) {
$result = mysqli_query($db,"
SELECT ru.redId Id, r.reden `name`
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidid)."' and ru.sterfte = 1 and r.actief = 1
ORDER BY reden
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Sterfte oorzaak bij doodgeboren
if($i == 4) {
$result = mysqli_query($db,"
SELECT ru.redId Id, r.reden `name`
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidid)."' and ru.uitval = 1 and r.actief = 1
ORDER BY reden
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// RedenMedicijn
if($i == 6) {
$result = mysqli_query($db,"
SELECT ru.reduId Id, r.reden `name`
FROM tblReden r
 join tblRedenuser ru on (r.redId = ru.redId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidid)."' and ru.pil = 1 and r.actief = 1
ORDER BY reden
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Medicijnlijst
if($i == 9) { // Toont medicijnen die actief zijn of waar nog voorraad van is
$result = mysqli_query($db,"
SELECT a.artId Id, coalesce(a.naamreader,a.naam) `name`, sum(i.inkat-coalesce(n.vbrat,0)) vrdat, a.actief
FROM tblArtikel a
 join tblEenheiduser eu on (a.enhuId = eu.enhuId)
 LEFT join tblInkoop i on (a.artId = i.artId)
 left join (
	SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
	FROM tblEenheiduser eu
	 join tblArtikel a on (a.enhuId = eu.enhuId)
	 join tblInkoop i on (i.artId = a.artId)
	 join tblNuttig n on (i.inkId = n.inkId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidid)."' and a.soort = 'pil'
	GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE soort = 'pil' and eu.lidId = '".mysqli_real_escape_string($db,$lidid)."'
GROUP BY a.artId, a.naamreader, a.naam, a.actief
HAVING a.actief = 1 or sum(i.inkat-coalesce(n.vbrat,0)) > 0
ORDER BY naamreader
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Lokatie
if($i == 10) {
$result = mysqli_query($db,"
SELECT hokId Id, hoknr `name` 
FROM tblHok
WHERE lidId = '".mysqli_real_escape_string($db,$lidid)."' and actief = 1
ORDER BY coalesce(sort, hokId + 500), hoknr
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Ramcode
if($i == 14) { //ramcode of te wel vaderdieren op de stallijst
$result = mysqli_query($db,"
SELECT s.schaapId Id, right(s.levensnummer,$karwerk) `name`
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
WHERE s.geslacht = 'ram' and h.actId = 3 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidid)."'
and not exists (
	SELECT st.schaapId
	FROM tblStal stal 
	 join tblHistorie h on (h.stalId = stal.stalId)
	 join tblActie a on (a.actId = h.actId)
	WHERE stal.schaapId = s.schaapId and a.af = 1 and h.datum < DATE_ADD(CURDATE(), interval -1 year) and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidid)."')
ORDER BY right(s.levensnummer,$karwerk)
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Rassen
if($i == 17) {
$result = mysqli_query($db,"
SELECT r.rasId Id, r.ras name
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidid)."' and r.actief = 1 and ru.actief = 1
ORDER BY coalesce(ru.sort, r.rasId + 500), r.ras
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}

// Voer
if($i == 18) { // Toont voer dat actief is of waar nog voorraad van is
$result = mysqli_query($db,"
SELECT a.artId Id, coalesce(a.naamreader,a.naam) `name`, sum(i.inkat-coalesce(n.vbrat,0)) vrdat, a.actief
FROM tblArtikel a
 join tblEenheiduser eu on (a.enhuId = eu.enhuId)
 LEFT join tblInkoop i on (a.artId = i.artId)
 left join (
	SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
	FROM tblEenheiduser eu
	 join tblArtikel a on (a.enhuId = eu.enhuId)
	 join tblInkoop i on (i.artId = a.artId)
	 join tblNuttig n on (i.inkId = n.inkId)
	WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidid)."' and a.soort = 'voer'
	GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE soort = 'voer' and eu.lidId = '".mysqli_real_escape_string($db,$lidid)."'
GROUP BY a.artId, a.naamreader, a.naam, a.actief
HAVING a.actief = 1 or sum(i.inkat-coalesce(n.vbrat,0)) > 0
ORDER BY a.naamreader, a.naam
") or die (mysqli_error($db)); 

//$rows = mysqli_num_rows($result);

}


$n = 0;

unset($opties);
$rows = mysqli_num_rows($result);

if(isset($result) && $rows > 0) {
while($row = mysqli_fetch_array($result))
		{
			//echo $row['hokId'].' - '.$row['hoknr'].'<br>';
			
			//$opties[] = array('recordid' => $row['Id'], 'name' => $row['name'], 'rownum' => $n); //https://stackoverflow.com/questions/16168552/how-to-convert-mysqli-fetch-assoc-results-into-json-format
			
			$opties[] = array('recordid' => $row['Id'], 'name' => str_replace( "/’/", "_", $row['name']), 'rownum' => $n);

			$n++;


			/*$vb = json_encode($opties);
echo $vb;*/
			
//var_dump($vb);
			
			}

$listnaam[$listname[$i]] = $opties;
//$listnaam[] = array($listname[$i] => $opties);
//unset($opties); 
}
else {
	$listnaam[$listname[$i]] = array();
}

//$allOpties = $opties; 
$allOpties = $listnaam; 
//$allOpties = array('lijsten' => $listnaam);
 
} // EInde for($i; ...)

//$opties[$listname[$i]][] = array('recordid' => $row['Id'], 'name' => $row['name'], 'rownum' => $n);


$vb = json_encode($allOpties);
echo $vb;



		 break;
	default:
		http_response_code(405); // Methode niet toegestaan
		exit;
	
} // Einde Switch
//echo json_encode(array("Result" => "Tweede goede resultaat "));
http_response_code(200); // Ok alles is goed

 
} // Einde Begin van else
?>