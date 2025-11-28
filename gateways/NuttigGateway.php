<?php

class NuttigGateway extends Gateway {

    public function nuttig_pil($hisId, $inkId, $stdat, $reduId, $aantal) {
        $this->run_query(<<<SQL
INSERT INTO tblNuttig SET hisId = :hisId, inkId = :inkId, nutat = :aantal, stdat = :stdat, reduId = :reduId
SQL
        , [
            [':hisId', $hisId, self::INT],
            [':inkId', $inkId, self::INT],
            [':reduId', $reduId, self::INT],
            [':aantal', $aantal], // TODO: decimal
            [':stdat', $stdat],
        ]
        );
    }

}
