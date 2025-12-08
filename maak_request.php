<?php

include "url.php";
unset($reqId); // Nodig als er diverse soorten meldingen tegelijk worden aangemaakt. Zie bijv. Afvoerstal.php (alleen module melden)
// *** HET REQUEST ***
//Zoeken naar een openstaand request
$zoek_req = mysqli_query($db, "
SELECT r.reqId
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h. hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '" . mysqli_real_escape_string($db, $lidId) . "'
 and isnull(r.dmmeld)
 and r.code = '" . mysqli_real_escape_string($db, $Melding) . "'
 and h.skip = 0
GROUP BY r.reqId
HAVING (count(r.reqId) < 60)
") or die(mysqli_error($db));
while ($req = mysqli_fetch_assoc($zoek_req)) {
    $reqId = $req['reqId'];
} // Einde Zoeken naar een openstaand request
        
if (!isset($reqId)) {
// Nieuw request aanmaken indien nodig
    $reqId = Request::maak_request($db, $lidId, $Melding);
    $newlidId = $lidId;
    // T.t.v. het aanmaken van het request zijn er nog geen meldingen gekoppeld
    // en is dus niet bekend voor welke gebruiker dit request is bestemd.
    //  Zolang er geen meldingen voorkomen in tblMelding
    //    bepaalt $newlidId voor welke gebruiker het request is bestemd.
}
// *** EINDE HET REQUEST ***

// *** DE MELDINGEN ***
$insert_tblMelding = "INSERT INTO tblMelding SET
    reqId = '" . mysqli_real_escape_string($db, $reqId) . "',
 hisId = '" . mysqli_real_escape_string($db, $hisId) . "' ";
        /*echo $insert_tblMelding.'<br>';*/    mysqli_query($db, $insert_tblMelding) or die(mysqli_error($db));
        
if (isset($newlidId)) {
    $update_tblRequest = "UPDATE tblRequest SET lidId_new = NULL where reqId = '" . mysqli_real_escape_string($db, $reqId) . "' ";
    mysqli_query($db, $update_tblRequest) or die(mysqli_error($db));
    unset($newlidId);
}
// *** EINDE DE MELDINGEN ***
