<?php

class DoelGateway extends Gateway {

    public function zoek_doel($doelId) {
        return $this->first_field(
            <<<SQL
SELECT doel
FROM tblDoel
WHERE doelId = :doelId
SQL
        , [[':doelId', $doelId, Type::INT]]
            , 'fout'
        );
    }

}
