<?php

function generatekey($length) {
    $options = 'abcdefghijklmnopqrstuvwxyz013456789';
    $code = '';
    for ($i = 0; $i < $length; $i++) {
        $key = rand(0, strlen($options) - 1);
        $code .= $options[$key];
    }
    return $code;
}

function getApiKey($datb) {
    $lid_gateway = new LidGateway();
    $apikey = generatekey(64);
    while ($lid_gateway->countWithReaderkey($apikey) > 0) {
        $apikey = generatekey(64);
    }
    return $apikey;
}

function getAlias($datb, $username, $vlgnr) {
    $lid_gateway = new LidGateway();
    $alias = $username;
    if ($vlgnr > 0) {
        $alias = $username . $vlgnr;
    }
    while ($lid_gateway->countWithAlias($alias) > 0) {
        $vlgnr++;
        $alias = $username . $vlgnr;
    }
    return $alias;
}
