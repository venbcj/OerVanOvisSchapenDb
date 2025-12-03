<?php /*22-6-2018 bestand gemaakt 
22-8-2020 : In bastandsnaam mili seconden toegevoegd.


ALTER TABLE `impreader`

CHANGE `uitvalcode` `col10` VARCHAR( 5 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'aantal dode dieren',
CHANGE `uit_vmdm` `col11` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'uitval voor merken lam',
CHANGE `reduId_vm` `moment1` VARCHAR( 3 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'reden uitval voor merken 1',
CHANGE `uit_vmdm_tmp` `col13` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'tijdelijk uitval voor merken zie Inlezenreader.php',
CHANGE `reduId_vm_tmp` `moment2` VARCHAR( 3 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL COMMENT 'tijdelijk reden uitval voor merken zie Inlezenreader.php',
 
CHANGE `reduId_uitv` `reden_uitv` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
CHANGE `levnr_afl` `levnr_afv` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `teller_afl` `teller_afv` INT(3) NULL DEFAULT NULL,
CHANGE `ubn_afl` `ubn_afv` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `afleverkg` `afvoerkg` DECIMAL( 5, 2 ) NULL DEFAULT NULL ,
CHANGE `levnr_aanw` `levnr_aanv` VARCHAR( 12 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL ,
CHANGE `teller_aanw` `teller_aanv` INT( 3 ) NULL DEFAULT NULL ,
CHANGE `ubn_aanw` `ubn_aanv` VARCHAR( 10 ) CHARACTER SET latin1 COLLATE latin1_swedish_ci NULL DEFAULT NULL,
CHANGE `reduId_pil` `reden_pil` INT( 3 ) NULL DEFAULT NULL ;


*/

include "connect_db.php";
$string = '';
if ($_SERVER['REQUEST_METHOD'] == 'GET') {

    $data = array(
        /* GEB */ array("datum" => "28-01-2018", "tijd" => "15:48:35",    "levnr_geb" => "123456789012", "teller" => "1", "rascode" => "CH", "geslacht" => "OOI", 
                  "moeder" => "100190403456", "hokcode" => "3", "gewicht" => "62", "col10" => "0", "col11" => "28-01-2018", 
                  "moment1" => "1", "col13" => "28-01-2018", "moment2" => "1", "levnr_uitv" => NULL, 
                  "teller_uitv" => NULL, "reden_uitv" => "0", "levnr_afv" => NULL, "teller_afv" => NULL, 
                  "ubn_afv" => NULL, "afvoerkg" => NULL, "levnr_aanv" => NULL, "teller_aanv" => NULL, 
                  "ubn_aanv" => NULL, "levnr_sp" => NULL, "teller_sp" => NULL, "hok_sp" => NULL, "speenkg" => NULL, 
                  "moeder_dr" => NULL, "col30" => NULL, "uitslag" => NULL, "vader_dr" => NULL, "levnr_ovpl" => NULL, 
                  "teller_ovpl" => NULL, "hok_ovpl" => NULL, "reden_pil" => "1", "levnr_pil" => NULL, 
                  "teller_pil" => NULL, "col39" => "0", "col40" => "0", "col41" => "0", "weegkg" => NULL, 
                  "levnr_weeg" => NULL, "col44" => NULL));
    header('Content-Type', 'application/json');
    echo strtolower(json_encode($data));
    exit();
}
else
{  // Begin van else

$headers = getallheaders(); // geef in een array ook headers terug die ik naar de server heb gestuurd in eerste instant
//var_dump($headers);

//echo is_array($string) ? 'dit is een array' : 'dit is geen array';
    

if (!isset($headers['Authorization'])) { // Als in de headers geen index 'Autorization voorkomt'
    http_response_code(401); // Unauthorized
    Echo 'authorization header bestaat niet.';
    exit;
} else 
{

    $authorization = explode ( " ", $headers['Authorization'] );
  /*  echo trim($authorization[0]).'<br>';
    echo strlen(trim($authorization[1])).'<br>';*/
    if (count($authorization) == 2 && trim($authorization[0]) == "Bearer" && strlen(trim($authorization[1])) == 64) {

        $statement = "SELECT lidId from tblLeden where readerkey = '".mysqli_real_escape_string($db,$authorization[1])."'" ;
        $zoek_lidId_result = mysqli_query($db, $statement) or die(mysqli_error($db));

        $row = mysqli_fetch_assoc($zoek_lidId_result);

        if($row){
           $lidid = $row['lidId'];
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

switch ($_SERVER['REQUEST_METHOD']) { // Switch
    case 'POST':      
        $input = file_get_contents('php://input'); // php://input is de rauwe data. nl. het json bestand.

        /*$input = '[
{"datum":"29-01-2018","tijd":"16:44:25","levnr_geb":"100190703471","teller":null,"rascode":null,"geslacht":"OOI","moeder":"100506251436","hokcode":"8","gewicht":30,"col10":null,"col11":"29-01-2018","moment1":"1","col13":"29-01-2018","moment2":"2","levnr_uitv":null,"teller_uitv":null,"reden_uitv":"0","levnr_afv":null,"teller_afv":null,"ubn_afv":null,"afvoerkg":null,"levnr_aanv":null,"teller_aanv":"1","ubn_aanv":"222222","levnr_sp":null,"teller_sp":null,"hok_sp":null,"speenkg":null,"moeder_dr":null,"col30":null,"uitslag":null,"vader_dr":null,"levnr_ovpl":null,"teller_ovpl":null,"hok_ovpl":null,"reden_pil":"1","levnr_pil":null,"teller_pil":null,"col39":"0","col40":"0","col41":"0","weegkg":null,"levnr_weeg":null,"col44":null},
{"datum":"29-01-2018","tijd":"18:21:46","levnr_geb":null,"teller":null,"rascode":null,"geslacht":null,"moeder":null,"hokcode":"0","gewicht":null,"col10":"0","col11":null,"moment1":"0","col13":null,"moment2":"0","levnr_uitv":null,"teller_uitv":null,"reden_uitv":"0","levnr_afv":null,"teller_afv":null,"ubn_afv":null,"afvoerkg":null,"levnr_aanv":null,"teller_aanv":null,"ubn_aanv":null,"levnr_sp":null,"teller_sp":null,"hok_sp":null,"speenkg":null,"moeder_dr":"100190703488","col30":null,"uitslag":null,"vader_dr":"100190703881","levnr_ovpl":null,"teller_ovpl":null,"hok_ovpl":null,"reden_pil":"1","levnr_pil":null,"teller_pil":null,"col39":"0","col40":"0","col41":"0","weegkg":null,"levnr_weeg":null,"col44":null}
]';*/
        $data = json_decode($input); 
        //var_dump($data) ;
        //var_dump( $data ->glossary->GlossDiv->title) ;
        
        //if(!empty($data)) { echo '$DATA = '; // als $data bestaat

$velden = array('datum', 'tijd', 'levnr_geb', 'teller', 'rascode', 'geslacht', 'moeder', 'hokcode', 'gewicht', 'col10', 'col11', 'moment1', 'col13', 'moment2', 'levnr_uitv', 'teller_uitv', 'reden_uitv', 'levnr_afv', 'teller_afv', 'ubn_afv', 'afvoerkg', 'levnr_aanv', 'teller_aanv', 'ubn_aanv', 'levnr_sp', 'teller_sp', 'hok_sp', 'speenkg', 'moeder_dr', 'col30', 'uitslag', 'vader_dr', 'levnr_ovpl', 'teller_ovpl', 'hok_ovpl', 'reden_pil', 'levnr_pil', 'teller_pil', 'col39', 'col40', 'col41', 'weegkg', 'levnr_weeg', 'col44', 'lidId');


             foreach($data as $index => $item) {                 
            
                 /*if ($index == 0) {
                     $insert_qry .= ','; // is komma tussen twee records die worden ingelezen. De komma bestaat pas van index 1 !!!
                 }*/

// Inlezen record
for($i = 0; $i<=43; $i++) {

    if($i == 0) { $insert_qry = " INSERT INTO impReader SET "; $select_qry = ""; }
    

                 //$insert_qry .= '('; // begin elke in te lezen record met haakje openen. Tussen haakjes staan immers de waarde.
if($item -> {$velden[$i]} == "" || $item -> {$velden[$i]} == "0")
     {  $insert_qry .= "$velden[$i] = " . mysqli_real_escape_string($db, "NULL") . ", "; 
        $select_qry .= "ISNULL($velden[$i]) and "; }
else {  $insert_qry .= "$velden[$i] = '" . mysqli_real_escape_string($db, $item -> {$velden[$i]}) . "', "; 
        $select_qry .= "$velden[$i] = '" . mysqli_real_escape_string($db, $item -> {$velden[$i]}) . "' and "; }
} 

    $insert_qry .= ' lidId = '.mysqli_real_escape_string($db,$lidid).';';

/*echo $insert_qry.'<br>'.'<br>';*/    mysqli_query($db,$insert_qry) or die (mysqli_error($db));
// Einde Inlezen record

// CONTROLE JUIST INGELEZEN
$zoek_max_readId = mysqli_query($db,"
SELECT max(readId) readId
FROM impReader
WHERE lidId = ".mysqli_real_escape_string($db,$lidid)."
") or die (mysqli_error($db));
while ($max = mysqli_fetch_assoc($zoek_max_readId))
    { $readId_max = $max['readId']; }

    $select_qry .= ' lidId = '.mysqli_real_escape_string($db,$lidid). " and readId = ".$readId_max;
//echo 'where = '.$select_qry.'<br>'.'<br>';

$zoek_record_obv_waarden = " SELECT count(readId) hits FROM impReader WHERE ".$select_qry ;

/*echo '$zoek_record_obv_waarden = '.$zoek_record_obv_waarden.'<br>'.'<br>';*/

$zoek_record_obv_waarden = mysqli_query($db,$zoek_record_obv_waarden) or die (mysqli_error($db));
while ($cnt = mysqli_fetch_assoc($zoek_record_obv_waarden))
    { $hits = $cnt['hits']; }

 



// Reactie bij geboren schaap 
if($item -> levnr_geb != "") {
    $levnr = mysqli_real_escape_string($db, $item -> levnr_geb); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (geboren)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (geboren)'; }
}
// Einde Reactie bij geboren schaap
// Reactie bij uitval schaap
if($item -> levnr_uitv != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_uitv);

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (uitval)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (uitval)'; }
}
// Einde Reactie bij uitval schaap
// Reactie bij afvoeren schaap
if($item -> levnr_afv != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_afv); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (afvoer)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (afvoer)'; }
}
// Einde Reactie bij afvoeren schaap
// Reactie bij aanvoer schaap
if($item -> levnr_aanv != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_aanv); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (aanvoer)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (aanvoer)'; }
}
// Einde Reactie bij aanvoer schaap
// Reactie bij spenen schaap 
if($item -> levnr_sp != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_sp); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (spenen)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (spenen)'; }
}
// Einde Reactie bij spenen schaap 
// Reactie bij dracht schaap 
if($item -> moeder_dr != "" || $item -> vader_dr != "") {

$levnr_m = mysqli_real_escape_string($db, $item -> moeder_dr); 
$levnr_v = mysqli_real_escape_string($db, $item -> vader_dr);

if(!empty($levnr_m) && !empty($levnr_v))     { $levnr = $levnr_m.' en '.$levnr_v; } 
else                                     { $levnr = $levnr_m.$levnr_v; } 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (dracht)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (dracht)'; }
}
// Einde Reactie bij dracht schaap 
// Reactie bij overplaatsen schaap      vader_dr   
if($item -> levnr_ovpl != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_ovpl); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (overplaatsen)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (overplaatsen)'; }
}
// Einde Reactie bij overplaatsen schaap 
// Reactie bij medicatie schaap 
if($item -> levnr_pil != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_pil); 

if(isset($hits) && $hits == 1)  {     $terug[] = $levnr. ' is correct ingelezen (medicatie)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (medicatie)'; }
}
// Einde Reactie bij medicatie schaap 
// Reactie bij wegen schaap 
if($item -> levnr_weeg != "") {
$levnr = mysqli_real_escape_string($db, $item -> levnr_weeg); 

if(isset($hits) && $hits == 1)     {     $terug[] = $levnr. ' is correct ingelezen (wegen)'; }
else                             {     $terug[] = $levnr. ' is niet correct ingelezen (wegen)'; }
}
// Einde Reactie bij wegen schaap 

unset($hits);
/*echo $terug[$index].'<br>'.'<br>'.'<br>';*/

// Einde CONTROLE JUIST INGELEZEN

// *** UITVAL TIJDENS GEBOORTE SCHEIDEN *** 
/* Bij een geboren schaap kan 1 of 2 x uitval voor merken op 1 regel in het txt bestand staan. Er kunnen dus max drie schapen van 1 moeder op een regel staan waarvan er dan twee zijn overleden voor merken.
Als twee lammeren van 1 moeder uitvallen voor merken worden beiden lammeren geregistreerd op 1 moeder en op 1 regel/ record.  
Om er meerder records (max 3) van te maken volgt hier een bewerking van tabel impReader. */

// BEWERKING 1 van 2: Eerst worden de 1 of 2 uitgevallen lammeren gescheiden van het geboren lam in 1 nieuw record
$zoek_naar_geborenENuitval = mysqli_query($db,"
select count(readId) aantid
from impReader
where readId = ".mysqli_real_escape_string($db,$readId_max)." and levnr_geb is not null and 
(moment1 is not null or moment2 is not null)
") or die (mysqli_error($db));
    while ($qrycntr = mysqli_fetch_assoc($zoek_naar_geborenENuitval))

if (!empty($qrycntr['aantid'])) {
$insertimpreader ="
 INSERT INTO impReader (datum, tijd, teller, moeder, moment1, moment2, lidId) 
     select datum, tijd, teller, moeder, moment1, moment2, ".mysqli_real_escape_string($db,$lidid)."
    from impReader 
    where readId = ".mysqli_real_escape_string($db,$readId_max)."
 ;
";
    mysqli_query($db,$insertimpreader) or die (mysqli_error($db));

$zoek_max_readId = mysqli_query($db,"
SELECT max(readId) readId
FROM impReader
WHERE lidId = ".mysqli_real_escape_string($db,$lidid)."
") or die (mysqli_error($db));
while ($max = mysqli_fetch_assoc($zoek_max_readId))
    { $new_max = $max['readId']; }

// Einde BEWERKING 1 van 2

// BEWERKING 2 van 2 : Twee uitgevallen lammeren (in het ene nieuwe record) gescheiden indien van toepassing.
if ($new_max > $readId_max)    {

$GeborenLamUniekMaken = mysqli_query($db,"
UPDATE impReader SET moment1 = NULL, moment2 = NULL 
WHERE readId = ".mysqli_real_escape_string($db,$readId_max)."
") or die (mysqli_error($db));

$zoek_eerste_uitval = mysqli_query($db," 
select count(readId) aant
from impReader
where readId = ".mysqli_real_escape_string($db,$new_max)." and moment1 is not null
") or die (mysqli_error($db));
    while ($do1 = mysqli_fetch_assoc($zoek_eerste_uitval))  { $uitv1 = $do1['aant']; }

$zoek_tweede_uitval = mysqli_query($db," 
select count(readId) aant
from impReader
where readId = ".mysqli_real_escape_string($db,$new_max)." and moment2 is not null
") or die (mysqli_error($db));
    while ($do2 = mysqli_fetch_assoc($zoek_tweede_uitval))  { $uitv2 = $do2['aant']; }

/* LET OP als uival 2 bestaat en 1 niet kan hier dit scenario worden uitgewerk. */

if ($uitv1 == 1 && $uitv2 == 1) // Als er geen 1 maar 2 uitvallen zijn
 { 

$insert_impreader = "
INSERT INTO impReader (datum, tijd, teller, moeder, moment1, lidId)
    select datum, tijd, teller, moeder, moment2, ".mysqli_real_escape_string($db,$lidid)."
    from impReader
    where readId = ".mysqli_real_escape_string($db,$new_max)." 
";
    mysqli_query($db,$insert_impreader) or die (mysqli_error($db));

} // Einde Als er geen 1 maar 2 uitvallen zijn

} // Einde BEWERKING 2 van 2
} // Einde if (!empty($qrycntr['aantid']))

// *** Einde UITVAL TIJDENS GEBOORTE SCHEIDEN ***

             } // Einde foreach($data .....

// Maak een backup in de persoonlijke map op de server
/*$zoek_locatie = mysqli_query($db,"SELECT root_files from tblLeden where lidId = ".mysqli_real_escape_string($db,$lidid)." ;") or die (mysqli_error($db)); 
    while ($row = mysqli_fetch_assoc($zoek_locatie))
        {    $file_r = $row['root_files'];    } */ // Het pad naar alle php bestanden

$dir = dirname(__FILE__);

$now = DateTime::createFromFormat('U.u', microtime(true));
$tijdstip = $now->format("Y-m-d_H:i:s.u");
$bestandsnaam = 'reader_'.$tijdstip.'.txt';
$locatie = $dir ."/". "user_".$lidid."/";
$root = $locatie.$bestandsnaam;

$fh = fopen($root, 'w'); //bron : https://www.phphulp.nl/php/tutorial/php-functies/fopen/78/de-functie-fopen/145/
fwrite($fh, $input);
fclose($fh);

// Einde Maak een backup in de persoonlijke map op de server


$output = json_encode($terug);
var_dump($output);

return $output;
                 //http_response_code(500)); // Internal Server Error


         break;
    default:
        http_response_code(405); // Methode niet toegestaan
        exit;
    
} // Einde Switch
//echo json_encode(array("Result" => "Tweede goede resultaat "));
http_response_code(200); // Ok alles is goed

 
} // Einde Begin van else

?>
