<?php

class CombiredenGateway extends Gateway {

    public function zoek_reden_uitval($lidId) {
        return $this->run_query(<<<SQL
SELECT cr.scan, r.reden
from tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
 join tblReden r on (r.redId = ru.redId)
where ru.lidId = :lidId
 and cr.tbl = 'd'
order by cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        )->fetch_all();
    }

    public function zoek_reden_medicijn($lidId) {
        return $this->run_query(
            <<<SQL
SELECT cr.scan, a.naam, round(cr.stdat) stdat, r.reden
from tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 left join tblRedenuser ru on (cr.reduId = ru.reduId)
 left join tblReden r on (r.redId = ru.redId)
where eu.lidId = :lidId
 and cr.tbl = 'p'
order by cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function bestaat_reden($whereArtId, $whereStdat, $whereRed, $fldTbl): int {
        return $this->run_query(
            <<<SQL
SELECT cr.comrId
FROM tblCombireden cr
WHERE $whereArtId and $whereStdat and $whereRed and cr.tbl = '$fldTbl'
GROUP BY cr.artId, cr.reduId
SQL
        )->num_rows ?? 0;
    }

    public function bestaat_scannr($lidId, $whereScan, $fldTbl): int {
        return $this->run_query(
            <<<SQL
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and $whereScan and cr.tbl = '$fldTbl'
GROUP BY cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        )->num_rows ?? 0;
    }

    public function insert($fldTbl, $insArtId, $insStdat, $insRed, $insScan): void {
        $this->run_query(<<<SQL
INSERT INTO tblCombireden SET tbl = '$fldTbl', artId = '$insArtId', stdat = '$insStdat', reduId = '$insRed', scan = '$insScan'
SQL
        );
    }

    public function zoek_reden_uitval_combi($lidId) {
        return $this->run_query(<<<SQL
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and cr.tbl = 'd'
ORDER BY cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function find($comrId) {
        return $this->run_query(<<<SQL
SELECT scan, artId, reduId
FROM tblCombireden
WHERE comrId = :comrId
ORDER BY scan
SQL
        , [[':comrId', $comrId, Type::INT]]
        );
    }

    public function bestaat_combireden2($lidId, $whereRed, $rowid_d): int {
        return $this->run_query(<<<SQL
SELECT cr.comrId 
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and $whereRed and cr.comrId != $rowid_d and cr.tbl = 'd'
GROUP BY cr.artId, cr.reduId
SQL
        , [[':lidId', $lidId, Type::INT]]
        )->num_rows ?? 0;
    }

    public function bestaat_scannr2($lidId, $whereScan, $rowid_d): int {
        return $this->run_query(<<<SQL
SELECT cr.comrId
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and $whereScan and cr.comrId != $rowid_d and cr.tbl = 'd'
GROUP BY cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        )->num_rows ?? 0;
    }

    public function update($rowid_d, $fldScan, $fldReden): void {
        $this->run_query(<<<SQL
UPDATE tblCombireden set $fldScan, $fldReden WHERE comrId = $rowid_d
SQL
        );
    }

    public function update2($rowid_p, $fldScan, $fldArtId, $fldStdat, $fldReden): void {
        $this->run_query(<<<SQL
UPDATE tblCombireden set $fldScan, $fldArtId, $fldStdat, $fldReden WHERE comrId = $rowid_p
SQL
        );
    }

    public function delete($comrId): void {
        $this->run_query(<<<SQL
DELETE FROM tblCombireden WHERE comrId = :comrId
SQL
        , [[':comrId', $comrId, Type::INT]]
        );
    }

    public function p_list_for($lidId) {
        return $this->run_query(<<<SQL
SELECT cr.comrId
FROM tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = :lidId and cr.tbl = 'p'
ORDER BY cr.scan
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function c_list_for($lidId, $comrId) {
        return $this->run_query(<<<SQL
SELECT cr.scan, cr.artId, cr.stdat, cr.reduId
FROM tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
WHERE eu.lidId = :lidId and cr.comrId = :comrId
ORDER BY cr.scan
SQL
        , [[':lidId', $lidId, Type::INT], [':comrId', $comrId, Type::INT]]
        );
    }

    // TODO niet meer null teruggeven. Dwz die where-clauses binnen boord samenstellen
    public function bestaat_combireden3($lidId, $whereStdat, $whereRed, $rowid_p): ?int {
        return $this->first_field(<<<SQL
SELECT count(*)
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and $whereStdat and $whereRed and cr.comrId != :comrId and cr.tbl = 'p'
GROUP BY cr.artId, cr.reduId
SQL
        , [[':lidId', $lidId, Type::INT], [':comrId', $rowid_p, Type::INT]]
        );
    }

    public function bestaat_scan3($lidId, $whereScan, $comrId): ?int {
        return $this->first_field(<<<SQL
SELECT count(*)
FROM tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
WHERE ru.lidId = :lidId and $whereScan and cr.comrId != :comrId and cr.tbl = 'p'
GROUP BY cr.scan
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':comrId', $comrId, Type::INT],
        ]
        );
    }

}
