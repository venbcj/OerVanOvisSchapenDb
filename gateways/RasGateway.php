<?php

class RasGateway extends Gateway {

    public function zoek_ras($lidId) {
        return $this->run_query(
            <<<SQL
SELECT ras, scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.actief = 1 and lidId = :lidId
ORDER BY sort, ras
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    // had ook "lijst()" kunnen heten
    public function rassen($lidId) {
        return $this->run_query(
            <<<SQL
SELECT r.rasId, r.ras, lower(coalesce(ru.scan,'6karakters')) scan
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE ru.lidId = :lidId and r.actief = 1 and ru.actief = 1
ORDER BY ras
SQL
        , [[':lidId', $lidId, Type::INT]]
        );
    }

    public function rassenKV($lidId) {
        return $this->KV($this->rassen($lidId));
    }

    public function zoek_ras_bij($rasId, $lidId) {
        return $this->first_record(<<<SQL
SELECT ru.scan, ru.sort, ru.actief
FROM tblRas r
 join tblRasuser ru on (r.rasId = ru.rasId)
WHERE r.rasId = :rasId and ru.lidId = :lidId
SQL
        , [[':lidId', $lidId, Type::INT], [':rasId', $rasId, Type::INT]]
            , ['scan' => null, 'sort' => null, 'actief' => null]
        );
    }

    public function countScan($lidId, $scan) {
        return $this->first_field(<<<SQL
SELECT count(scan)
FROM tblRasuser
WHERE lidId = :lidId and scan = :scan and scan is not NULL
SQL
        , [[':lidId', $lidId, Type::INT], [':scan', $scan]]
        );
    }

    public function updateScan($lidId, $scan, $rasId) {
        $this->run_query(<<<SQL
UPDATE tblRasuser SET scan = :scan WHERE lidId = :lidId and rasId = :rasId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':rasId', $rasId, Type::INT],
            [':scan', $scan],
        ]
        );
    }

    public function updateSort($lidId, $sort, $rasId) {
        $this->run_query(<<<SQL
UPDATE tblRasuser SET sort = :sort WHERE lidId = :lidId and rasId = :rasId
SQL
        , [
            [':lidId', $lidId, Type::INT],
            [':rasId', $rasId, Type::INT],
            [':sort', $sort, Type::INT],
        ]
        );
    }

    public function set_actief($rasId, $actief) {
        $this->run_query(<<<SQL
UPDATE tblRasuser SET actief = :actief WHERE rasId = :rasId
SQL
        , [[':rasId', $rasId, Type::INT], [':actief', $actief]]
        );
    }

    public function delete_user($lidId) {
        $this->run_query(<<<SQL
            DELETE FROM tblRasuser WHERE lidId = :lidId
SQL
        , [
            [':lidId', $lidId, Type::INT],
        ]
        );
    }

    public function zoek_ras_name($txtRas, $lidId) {
        $sql = <<<SQL
                SELECT ras
                FROM tblRas r
                 join tblRasuser ru
                WHERE r.ras = :txtRas and ru.lidId = :lidId
SQL;
        $args = [[':txtRas', $txtRas], [':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function query_ras_toevoegen($txtRas, $lidId) {
        $sql = <<<SQL
 INSERT INTO tblRas
   SET ras = :txtRas,
       eigen = :lidId
SQL;
        $args = [[':txtRas', $txtRas], [':lidId', $lidId, Type::INT]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function query_rasuser_toevoegen($lidId, $rasId) {
        $sql = <<<SQL
INSERT INTO tblRasuser
  SET lidId = :lidId,
      rasId = :rasId
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':rasId', $rasId, Type::INT]];
        $this->run_query($sql, $args);
        return $this->db->insert_id;
    }

    public function update_tblRas($lidId) {
        $sql = <<<SQL
UPDATE tblRas set eigen = 1 WHERE eigen = :lidId
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function rasuser_toevoegen($lidId, $ras, $scan, $sort) {
        $sql = <<<SQL
                     INSERT INTO tblRasuser
                       SET lidId = :lidId,
                           rasId = :rasId,
                           scan  = :scan,
                           sort  = :sort
SQL;
        $args = [[':lidId', $lidId, Type::INT], [':rasId', $ras], [':scan', $scan], [':sort', $sort]];
        return $this->run_query($sql, $args);
    }

    public function zoek_rasuId($lidId) {
        $sql = <<<SQL
        SELECT rasuId
        FROM tblRasuser
        WHERE lidId = :lidId
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->first_field($sql, $args);
    }

    public function qryRas($lidId) {
        $sql = <<<SQL
        SELECT r.rasId, r.ras
        FROM tblRas r
         left join tblRasuser ru on (ru.rasId = r.rasId and ru.lidId = :lidId)
        WHERE isnull(ru.rasId) and r.actief = 1 and eigen = 0
        ORDER BY r.ras
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

    public function query($lidId) {
        $sql = <<<SQL
        SELECT r.rasId, r.ras, ru.scan, ru.sort, ru.actief
        FROM tblRas r
         join tblRasuser ru on (r.rasId = ru.rasId)
        WHERE ru.lidId = :lidId and r.actief = 1
        ORDER BY actief desc, coalesce(sort,ras) asc
SQL;
        $args = [[':lidId', $lidId, Type::INT]];
        return $this->run_query($sql, $args);
    }

}
