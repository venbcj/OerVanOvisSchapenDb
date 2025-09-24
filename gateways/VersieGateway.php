<?php

class VersieGateway extends Gateway {

    public function zoek_laatste_versie() {
    $zoek_laatste_versie = mysqli_query($this->db, "
SELECT max(Id) lstId
FROM (
    SELECT a.Id
    FROM tblVersiebeheer a
     left join tblVersiebeheer t on (a.Id = t.versieId)
    WHERE a.app = 'App' and isnull(t.Id)

    UNION
    SELECT a.Id
    FROM tblVersiebeheer a
     join tblVersiebeheer t on (a.Id = t.versieId)
    WHERE a.app = 'App'

    UNION

    SELECT Id
    FROM tblVersiebeheer 
    WHERE app = 'Reader' and isnull(versieId)
) a
    ") or Logger::error(mysqli_error($this->db));
    while ($zlv = mysqli_fetch_assoc($zoek_laatste_versie)) {
         return $zlv['lstId'];
    }
    return null;
    }

    public function zoek_readersetup_in($last_versieId) {
        $zoek_readersetup_in_laatste_versie =  mysqli_query($this->db, "
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'App' and Id = '".mysqli_real_escape_string($this->db, $last_versieId)."'
") or die(mysqli_error($this->db));
    while ($zrv = mysqli_fetch_assoc($zoek_readersetup_in_laatste_versie)) {
         return $zrv['bestand'];
    }
    return null;
    }

    public function zoek_readertaken_in($last_versieId) {
        $zoek_readertaken_in_laatste_versie =  mysqli_query($this->db, "
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'Reader' and (Id = '".mysqli_real_escape_string($this->db, $last_versieId)."' or versieId = '".mysqli_real_escape_string($this->db, $last_versieId)."')
") or die(mysqli_error($this->db));
    while ($zrv = mysqli_fetch_assoc($zoek_readertaken_in_laatste_versie)) {
         return $zrv['bestand'];
    }
    return null;
    }

}
