<?php

class ImpReaderGateway extends Gateway {

    public function zoek_readerregel_verwerkt($readId) {
        return $this->first_field(<<<SQL
SELECT verwerkt
FROM impReader
WHERE readId = :readId
SQL
        , [[':readId', $readId, Type::INT]]
        );
    }

}
