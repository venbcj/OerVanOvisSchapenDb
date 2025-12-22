<?php

/*
<!-- 29-4-2015 : bestand gemaakt 
4-12-2016 : Response bestand in de map BRIGHT wordt gezocht o.b.v. requestId zonder melddatum (dmmeld) niet meer o.b.v. requestId zonder meldnummer 
12-2-2017 : Response bestand in de map BRIGHT wordt gezocht o.b.v. def = N uit de tabel impRespons niet meer o.b.v. requestId zonder melddatum (dmmeld). Als een melding wordt vastgelegd wordt het response-bestand anders niet ingelezen 
28-12-2018 : response bestand wordt ingelezen als requestbestand ook nog in de map BRIGHT staat. Als het response bestand (spontaan) nogmaals wordt aangeleverd wordt deze nu niet meer ingelezen 
20-2-2020 locatie van bestanden gebaseerd op een functie 
1-4-2022 sql beveiligd met quotes 
10-05-2023 : Als er definitieve melding om redenen opnieuw moet worden aangeboden aan RVO mag het veld impRespons.def niet de waarde J hebben bij het betreffende reqId. Anders wordt het response bestand van Bright niet meer verwerkt. De waarde J heb ik gewijzigd naar Y en de query $zoek_laatste_response hierop aangepast. rp.def = 'N' gewijzigd naar rp.def != 'J'  
28-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie 
15-07-2025 : ubn uit bestandsnaam $requestfile en $responsfile (richting RVO) gehaald --> 
 */

# TODO: #0004106 ik denk dat dit bestand, en importRespons, functies kunnen worden
# - veroorzaakt geen uitvoer
# - heeft neveneffecten (bestanden hernoemd/verplaatst, database gewijzigd)
# - zijn er nog andere global-effecten dan $fout en $goed?

/*** Script ter controle van het bestaan van Response.txt bestanden afkomstig van RVO ***/
// Lokatie en klant gegegevens Responsbestand ophalen
$lid_gateway = new LidGateway();
$alias = $lid_gateway->findAlias($lidId);
$dir = dirname(__FILE__); // Locatie bestanden op FTP server

// De gegevens van het request uit impResponse waarvan de laatste import een controle melding is
$request_gateway = new RequestGateway();
$zoek_laatste_response = $request_gateway->zoekLaatsteResponse($lidId);
// is dit terecht een while(), of is er hoogstens 1 regel?
while ($req = mysqli_fetch_assoc($zoek_laatste_response)) {
    $reqId = $req['reqId'];
    $code = $req['code'];   // t.b.v. importRespons.php
    // Einde De gegevens van het request
    // requestfile, responsefile: parameters voor importRespons
    $requestfile = $alias."_".$reqId."_request.txt";  // T.b.v. verplaatsen in importRespons.php
    $responsfile = $alias."_".$reqId."_response.txt";
    # error_log("requestfile=$requestfile\n", 3, "log/development.log");
    $request_aanwezig = file_exists($dir.'/BRIGHT/'.$requestfile);
    $respons_aanwezig = file_exists($dir.'/BRIGHT/'.$responsfile);
    if ($respons_aanwezig == 1 && $request_aanwezig == 1) {
        include "importRespons.php";
    }
}
