<?php
/*
<!-- 28-4-2015 : Aangemaakt als kopie van importReader.php
28-12-2018 : bericht als definitieve melding terugkomt als contrle melding 29-12 : bericht bij juiste definitieve melding incl. response tijd opslaan in tblRequest
2-2-2020 : De root naar alle bestanden op de FTP server variabel gemaakt
21-2-2020 : LOAD DATA LOCAL INFILE vervangen door file()
28-8-2020 : Omnummering toegevoegd in array
26-9-2020 : in te lezen velden in impRespons aangepast ivm Omnummeren
4-11-2020 : een quute in een response melding veroorzaakt geen foute query stateent meer door str_replace("'", "''", $regel[$ii]);
12-12-2020 : Gearchiveerde bestanden RVO aangevuld met tijdstip van melden versus ontvangen response
01-04-2022 sql beveiligd met quotes
10-02-2025 : Bericht als definitieve melding terugkomt als controle melding aangepast
23-08-2025 : ubn uit bestandsnaam $responsfile_rename en $requestfile_rename gehaald -->
 */

include "url.php";

$dir = dirname(__FILE__); // Locatie bestanden op FTP server
$bright_map = $dir.'/BRIGHT/';
$persoonlijke_map = $dir.'/user_'.$lidId.'/';
copy($bright_map.$responsfile, $dir."/".$responsfile); // $responsfile is gedeclareerd in responscheck.php

if ($code == 'VMD') {
    // $code is gedeclareerd in responscheck.php
    $velden = array('reqId','prod','def','urvo','prvo','melding','relnr','ubn','schaapdm','land','levensnummer','soort','land_new','levensnummer_new','land_herk','gebdm','sucind','foutind','foutcode','foutmeld','meldnr','respId');
} else {
    $velden = array('reqId','prod','def','urvo','prvo','melding','relnr','ubn','schaapdm','land','levensnummer','soort','ubn_herk','ubn_best','land_herk','gebdm','sucind','foutind','foutcode','foutmeld','meldnr','respId');
}

if (!isset($dir) or empty($dir)) {
    $fout = "De oorspronkelijke locatie wordt niet gevonden.";
}
if (!isset($responsfile) or empty($responsfile)) {
    if (isset($fout)) {
        $fout .= "en het responsbestand wordt niet gevonden.";
    } else {
        $fout  =     "Het responsbestand wordt niet gevonden.";
    }
}
if (!isset($requestfile) or empty($requestfile)) {
    if (isset($fout)) {
        $fout .= " en het requestbestand wordt niet gevonden.";
    } else {
        $fout  =      "Het requestbestand wordt niet gevonden.";
    }
}
// $requestfile is gedeclareerd in responscheck.php

if (!isset($fout)) {
    // Als controles zijn doorstaan
    // Inlezen response bestand in tabel impRespons
    $lok = $dir.'/'. $responsfile;
    $inhoud = file($lok);
    $c = count($inhoud);
    for ($i=0; $i<$c; $i++) {
        $regel = explode(";", $inhoud[$i]);
        $cc = 21 /*count($regel)*/;
        $insert_qry = 'INSERT INTO impRespons SET ';
        for ($ii=0; $ii<$cc; $ii++) {
            $waarde = str_replace("'", "''", $regel[$ii]);
            if ($ii == 0) {
                $insert_qry = " INSERT INTO impRespons SET ".$velden[$ii]." = '".$waarde."'";
            } elseif ($regel[$ii] == "" || $regel[$ii] == "0") {
                $insert_qry .= ", ".$velden[$ii]." = NULL";
            } else {
                $insert_qry .= ", ".$velden[$ii]." = '".$waarde."'";
            }
        }
        $insert_qry .= ';';
        mysqli_query($db, $insert_qry) or die(mysqli_error($db));
    }
    //mysqli_set_local_infile_default($db);
    // Einde Inlezen response bestand in tabel impRespons

    // ZOEKEN NAAR RESPONSE- EN REQUESTBESTANDEN IN DIV MAPPEN bijv. 1507131_bas_4_respons.txt
    $zoek_response_phpBestanden = file_exists($dir.'/'.$responsfile);           // Response bestand tussen alle php bestanden
    $zoek_response_BrightMap    = file_exists($bright_map.$responsfile);        // Response bestand in Bright map
    $zoek_request_BrightMap     = file_exists($bright_map.$requestfile); // Request bestand in Bright map

    if ($zoek_response_phpBestanden == 1) { // Als response bestand bestaat tussen alle php-bestanden
        $DelFile = $dir."/".$responsfile;
        unlink($DelFile)or die("Kan bestand ".$responsfile." niet verwijderen. " . mysqli_error($db));// verwijdert respons bestand tussen alle php bestanden
    }

    // Verplaatsen van responsbestand van map BRIGHT naar persoonlijke map
    // Als response bestand bestaat in Bright map
    if ($zoek_response_BrightMap == 1) {
        // filedatum van response bestand ophalen
        $pad = $bright_map.$responsfile;

        $dag = date("d", filemtime($pad));
        $mnd = date("m", filemtime($pad)) * 1;
        $jaar = date("Y", filemtime($pad));
        $tijdstip = date("H:i:s", filemtime($pad));
        $tijd = date("H:i", filemtime($pad));

        $maand = array(1=>' jan ', ' feb ', ' mrt ', ' apr ', ' mei ', ' jun ', ' jul ', ' aug ', ' sep ', ' okt ', ' nov ', ' dec ');

        $datum = $dag.$maand[$mnd].$jaar.' '.$tijd.'u.';
        $date = $jaar.'-'.$mnd.'-'.$dag.' '.$tijdstip;
        $make_date = date_create($date);
        $response_filedate = date_format($make_date, 'Y-m-d H:i:s');
        // Einde filedatum van response bestand ophalen

        // Archiveren van response bestand bijv 1507131_bas_4_respons.txt
        copy($bright_map.$responsfile, $persoonlijke_map.$responsfile);

        $DelFile = $bright_map.$responsfile;
        // Verwijderen response bestand in Bright map
        unlink($DelFile)or die("Kan bestand ".$responsfile." in de map BRIGHT niet verwijderen. " . mysqli_error($db));// verwijdert respons bestand op locatie BRIGHT

        $responsfile_rename = $alias."_".$reqId."_response_".$response_filedate.".txt";
        // Respons bestand in persoonlijke map hernoemen
        rename($persoonlijke_map.$responsfile, $persoonlijke_map.$responsfile_rename); // toevoegen van tijdstip responsebestand
        // Einde Respomse bestand in persoonlijke map hernoemen
    }
    // EINDE Verplaatsen van responsbestand van map BRIGHT naar persoonlijke map

    // Als request bestand bestaat in Bright map
    if ($zoek_request_BrightMap == 1) {
        // filedatum van request bestand ophalen
        $pad = $bright_map.$requestfile;

        $dag = date("d", filemtime($pad));
        $mnd = date("m", filemtime($pad)) * 1;
        $jaar = date("Y", filemtime($pad));
        $tijdstip = date("H:i:s", filemtime($pad));
        $tijd = date("H:i", filemtime($pad));

        $maand = array(1=>' jan ', ' feb ', ' mrt ', ' apr ', ' mei ', ' jun ', ' jul ', ' aug ', ' sep ', ' okt ', ' nov ', ' dec ');

        $datum_requestfile = $dag.$maand[$mnd].$jaar.' '.$tijd.'u.';
        $date = $jaar.'-'.$mnd.'-'.$dag.' '.$tijdstip;
        $make_date = date_create($date);
        $request_filedate = date_format($make_date, 'Y-m-d H:i:s');
        // Einde filedatum van request bestand ophalen

        // Archiveren van request bestand bijv 1507131_bas_4_request.txt
        copy($bright_map.$requestfile, $persoonlijke_map.$requestfile);

        $DelFile = $bright_map.$requestfile;
        unlink($DelFile)or die("Kan bestand ".$requestfile." in de map BRIGHT niet verwijderen. " . mysqli_error($db));// verwijdert request bestand op locatie BRIGHT

        $requestfile_rename = $alias."_".$reqId."_request_".$request_filedate.".txt";
        // Request bestand in persoonlijke map hernoemen
        rename($persoonlijke_map.$requestfile, $persoonlijke_map.$requestfile_rename); // toevoegen van tijdstip responsebestand
        // Einde Respomse bestand in persoonlijke map hernoemen
    }
    // Einde Archiveren van request bestand bijv 1507131_bas_4_request.txt
    // EINDE ZOEKEN NAAR RESPONSE- EN REQUESTBESTANDEN IN DIV MAPPEN bijv. 1507131_bas_4_respons.txt

    $meldname = array('GER'=>'geboorte','AAN'=>'aanwas', 'AFV'=>'afvoer', 'DOO'=>'uitval', 'VMD'=>'omnummer');

    $zoek_status_request = mysqli_query($db, "
    SELECT r.def, r.code
    FROM tblRequest r
    WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."'
    ") or die(mysqli_error($db));

    while ($req = mysqli_fetch_assoc($zoek_status_request)) {
        $def_req = $req['def'];
        $code = $req['code'];
    }

    $melding = $meldname[$code];

    $zoek_status_response = mysqli_query($db, "
    SELECT r.def, r.meldnr
    FROM impRespons r
     join (
        SELECT max(respId) respId
        FROM impRespons
        WHERE reqId = '".mysqli_real_escape_string($db, $reqId)."'
     ) lr on (r.respId = lr.respId)
    ") or die(mysqli_error($db));

    while ($resp = mysqli_fetch_assoc($zoek_status_response)) {
        $def_resp = $resp['def'];
        $meldnr = $resp['meldnr'];
    }

    // bericht als definitieve melding terugkomt als controle melding
    if ($def_req == 'J' && $def_resp == 'N') {
        $fout = "Definitieve melding is teruggekomen als een controle melding !! Heb je geen meldnummer dan is je melding niet gedaan. Op de pagina Overzicht meldingen kun je de melding weer openzetten om de melding nogmaals te versturen.";
    }
    // Oude melding : Meld dit bij de beheerder. Heb je geen meldnummer dan is je melding niet gedaan.
    // Einde bericht als definitieve melding terugkomt als controle melding

    // bericht als controle melding terugkomt als definitieve melding
    if ($def_req == 'N' && $def_resp == 'J') {
        $fout = "Controle melding is teruggekomen als een definitieve melding !! Meld dit bij de beheerder. Heb je geen meldnummer dan is de definitieve melding niet goed gedaan.";
    }
    // Einde bericht als controle melding terugkomt als definitieve melding

    if ($def_req == 'J' && $def_resp == 'J') {
        $goed = 'De definitieve '.$melding.'melding is verwerkt op '.$datum.' Kijk onder meldingen naar het definitieve resultaat.';

        if (isset($response_filedate)) {
            $opslaan_response_datum = "UPDATE tblRequest SET dmresponse = '".mysqli_real_escape_string($db, $response_filedate)."' where reqId = '".mysqli_real_escape_string($db, $reqId)."' ";
            mysqli_query($db, $opslaan_response_datum) or die(mysqli_error($db));
        }
    }
} // Einde Als controles zijn doorstaan
