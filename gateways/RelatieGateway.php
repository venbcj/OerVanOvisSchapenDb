<?php

class RelatieGateway extends Gateway {

    public function zoek_bestemming($last_stalId) {
        return $this->first_field(
            <<<SQL
SELECT r.partId
FROM tblStal st
 join tblRelatie r on (st.rel_best = r.relId)
WHERE st.stalId = :stalId
SQL
        , [[':stalId', $last_stalId, self::INT]]
        );
    }

    public function zoek_crediteur($partId) {
        return $this->first_field(
            <<<SQL
SELECT relId
FROM tblRelatie
WHERE partId = :partId
 and relatie = 'cred'
SQL
        , [[':partId', $partId, self::INT]]
        );
    }

}
