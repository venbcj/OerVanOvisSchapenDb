<?php

class VersieGateway extends Gateway {

    public function zoek_laatste_versie() {
    $vw = $this->db->query("
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
    while ($zlv = $vw->fetch_assoc()) {
         return $zlv['lstId'];
    }
    return null;
    }

    public function zoek_readersetup_in($last_versieId) {
        $vw =  $this->db->query("
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'App' and Id = '".$this->db->real_escape_string($last_versieId)."'
") or die(mysqli_error($this->db));
    while ($zrv = $vw->fetch_assoc()) {
         return $zrv['bestand'];
    }
    return null;
    }

    public function zoek_readertaken_in($last_versieId) {
        $vw =  $this->db->query("
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'Reader' and (Id = '".$this->db->real_escape_string($last_versieId)."' or versieId = '".$this->db->real_escape_string($last_versieId)."')
") or die(mysqli_error($this->db));
    while ($zrv = $vw->fetch_assoc()) {
         return $zrv['bestand'];
    }
    return null;
    }

}
