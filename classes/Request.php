<?php

class Request {

    public static function maak_request($datb, $lidId, $fldCode) {
        $request_gateway = new RequestGateway();
        return $request_gateway->insert($lidId, $fldCode);
    }

}
