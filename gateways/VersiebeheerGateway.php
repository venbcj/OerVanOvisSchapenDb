<?php

class VersiebeheerGateway extends Gateway {

    public function insert_app($insDate, $insVersie, $insNaamApp, $insToel) {
        $sql = <<<SQL
            INSERT INTO tblVersiebeheer set datum = :insDate, versie = :insVersie, bestand = :insNaamApp, app = 'App', comment = :insToel
SQL;
        $args = [[':insDate', $insDate], [':insVersie', $insVersie], [':insNaamApp', $insNaamApp], [':insToel', $insToel]];
        return $this->run_query($sql, $args);
    }

    public function zoek_versieId($insNaamApp) {
        $sql = <<<SQL
            SELECT Id
            FROM tblVersiebeheer
            WHERE bestand = :insNaamApp
SQL;
        $args = [[':insNaamApp', $insNaamApp]];
        return $this->first_field($sql, $args);
    }

    public function insert_taak_versie($versieId, $insDate, $insVersie, $insNaamTaak, $insToel) {
        $sql = <<<SQL
            INSERT INTO tblVersiebeheer set versieId = :versieId, datum = :insDate, versie = :insVersie, bestand = :insNaamTaak, app = 'Reader', comment = :insToel
SQL;
        $args = [[':versieId', $versieId], [':insDate', $insDate], [':insVersie', $insVersie], [':insNaamTaak', $insNaamTaak], [':insToel', $insToel]];
        return $this->run_query($sql, $args);
    }

    public function insert_taak($insDate, $insVersie, $insNaamTaak, $insToel) {
        $sql = <<<SQL
            INSERT INTO tblVersiebeheer set datum = :insDate, versie = :insVersie, bestand = :insNaamTaak, app = 'Reader', comment = :insToel
SQL;
        $args = [[':insDate', $insDate], [':insVersie', $insVersie], [':insNaamTaak', $insNaamTaak], [':insToel', $insToel]];
        return $this->run_query($sql, $args);
    }

    public function zoek_versies($dmStart, $last_versieId, $hisVersies) {
        $sql = <<<SQL
        SELECT a.Id, date_format(a.datum, '%d-%m-%Y') datum, year(a.datum) jaar, a.versie, a.bestand bestandApp, NULL bestandTaak, a.comment
        FROM tblVersiebeheer a
         left join tblVersiebeheer t on (a.Id = t.versieId)
        WHERE a.app = 'App' and isnull(t.Id) and (a.datum > :dmStart or a.Id = :last_versieId)
        UNION
        SELECT a.Id, date_format(a.datum, '%d-%m-%Y') datum, year(a.datum) jaae, a.versie, a.bestand bestandApp, t.bestand bestandTaak, a.comment
        FROM tblVersiebeheer a
         join tblVersiebeheer t on (a.Id = t.versieId)
        WHERE a.app = 'App' and (a.datum > :dmStart or a.Id = :last_versieId)
        UNION
        SELECT Id, date_format(datum, '%d-%m-%Y') datum, year(datum) jaar, versie, NULL bestandApp, bestand bestandTaak, comment
        FROM tblVersiebeheer 
        WHERE app = 'Reader' and isnull(versieId) and (datum > :dmStart or Id = :last_versieId)
        ORDER BY Id desc
        LIMIT :hisVersies
SQL;
        $args = [[':dmStart', $dmStart], [':last_versieId', $last_versieId], [':hisVersies', $hisVersies, self::INT]];
        return $this->run_query($sql, $args);
    }

    public function zoek_huidige_versie($last_versieId) {
        $sql = <<<SQL
    SELECT versie
    FROM tblVersiebeheer 
    WHERE Id = :last_versieId
SQL;
        $args = [[':last_versieId', $last_versieId]];
        return $this->first_field($sql, $args);
    }

}
