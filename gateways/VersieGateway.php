<?php

class VersieGateway extends Gateway {

    public function zoek_laatste_versie() {
        return $this->first_field(<<<SQL
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
SQL
        );
    }

    public function zoek_readersetup_in($last_versieId) {
        return $this->first_field(<<<SQL
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'App' and Id = :id
SQL
        , [
            [':id', $last_versieId, self::INT],
        ]);
    }

    public function zoek_readertaken_in($last_versieId) {
        return $this->first_field(<<<SQL
SELECT bestand
FROM tblVersiebeheer 
WHERE app = 'Reader' and (Id = :id or versieId = :id)
SQL
        , [
            [':id', $last_versieId, self::INT],
        ]);
    }

}
