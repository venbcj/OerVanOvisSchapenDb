<!-- 29-4-2015 : bestand gemaakt 
4-12-2016 : Response bestand in de map BRIGHT wordt gezocht o.b.v. requestId zonder melddatum (dmmeld) niet meer o.b.v. requestId zonder meldnummer 
12-2-2017 : Response bestand in de map BRIGHT wordt gezocht o.b.v. def = N uit de tabel impRespons niet meer o.b.v. requestId zonder melddatum (dmmeld). Als een melding wordt vastgelegd wordt het response-bestand anders niet ingelezen 
28-12-2018 : response bestand wordt ingelezen als requestbestand ook nog in de map BRIGHT staat. Als het response bestand (spontaan) nogmaals wordt aangeleverd wordt deze nu niet meer ingelezen 
20-2-2020 locatie van bestanden gebaseerd op een functie --> 
<?php
/* Toegepast in :
- Home.php
- MeldAanwas.php
- MeldAfleveren.php
- MedGeboortes.php
- Melden.php
- MeldUitval.php */

include "url.php";

/*** Script ter controle van het bestaan van Response.txt bestanden afkomstig van RVO ***/
// Lokatie en klant gegegevens Responsbestand ophalen
$result = mysqli_query($db,"SELECT alias FROM tblLeden WHERE lidId = ".mysqli_real_escape_string($db,$lidId)." ") or die (mysqli_error($db)); 
	while ($row = mysqli_fetch_assoc($result))
		{ $alias = $row['alias']; }

$dir = dirname(__FILE__); // Locatie bestanden op FTP server

// De gegevens van het request uit impResponse waarvan de laatste import een controle melding is
$zoek_laatste_response = mysqli_query ($db,"
SELECT r.reqId, r.code, l.ubn
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblLeden l on (st.lidId = l.lidId)
 left join(
	SELECT max(respId) respId, reqId
	FROM impRespons 
	GROUP BY reqId
	) lr on (r.reqId = lr.reqId)
 left join impRespons rp on (rp.respId = lr.respId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and (rp.def = 'N' or isnull(rp.def))
GROUP BY r.reqId, l.ubn
ORDER BY r.reqId
") or die (mysqli_error($db));
	While ($req = mysqli_fetch_assoc($zoek_laatste_response))
	{	$reqId = $req['reqId'];
		$code = $req['code'];	// t.b.v. importRespons.php
		$ubn = $req['ubn'];	
// Einde De gegevens van het request

$requestfile = $ubn."_".$alias."_".$reqId."_request.txt"; #echo $requestfile.' moet worden gezocht <br>'; // T.b.v. verplaatsen in importRespons.php
$responsfile = $ubn."_".$alias."_".$reqId."_response.txt"; #echo $responsfile.' moet worden gezocht <br>';

$request_aanwezig = file_exists($dir.'/BRIGHT/'.$requestfile);
$respons_aanwezig = file_exists($dir.'/BRIGHT/'.$responsfile);

if ($respons_aanwezig == 1 && $request_aanwezig == 1) {

	#echo '<br>'.$responsfile.'<br> zit WEL in map Bright';
include "importRespons.php";

} else {	/*echo '<br>'.$responsfile.'<br> zit NIET in map Bright'; */	}   
 
}

/*** EINDE  *** Script ter controle van het bestaan van Response.txt bestanden afkomstig van RVO *** EINDE ***/
?>


