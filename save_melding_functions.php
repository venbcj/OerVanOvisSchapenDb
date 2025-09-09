<?php

function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

# subquery geeft stalid 49 hisId 6
function zoek_eerste_datum_stalop($db, $recId) {
    return mysqli_query($db, "
SELECT min(datum) date, date_format(min(datum),'%d-%m-%Y') datum
FROM tblHistorie h
 join tblActie a on (a.actId = h.actId)
 join (
    SELECT st.stalId, h.hisId
    FROM tblStal st
     join tblHistorie h on (h.stalId = st.stalId)
     join tblMelding m on (m.hisId = h.hisId)
    WHERE m.meldId = '$recId'
 ) st on (st.stalId = h.stalId and st.hisId <> h.hisId)
 WHERE a.op = 1
");
}

function zoek_schaapid($db, $fldLevnr) {
    return mysqli_query($db, "
SELECT schaapId
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db, $fldLevnr)."'");
}

function count_levnr($db, $fldLevnr, $schaapId) {
    return mysqli_query($db, "
SELECT count(*) aant
FROM tblSchaap 
WHERE levensnummer = '".mysqli_real_escape_string($db, $fldLevnr)."' and schaapId <> '".mysqli_real_escape_string($db, $schaapId)."'");
}

function zoek_in_database($db, $recId) {
    return mysqli_query($db, "
SELECT r.reqId, r.code, r.def, m.skip, m.fout, h.datum, s.levensnummer, s.geslacht, st.rel_herk, st.rel_best
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE m.meldId = '$recId'
");
}

function zoek_bestemming_in_db($db, $recId) {
    return mysqli_query($db, "
SELECT st.rel_best
FROM tblMelding m
 join tblHistorie h on (m.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
WHERE m.meldId = '$recId'
");
}
