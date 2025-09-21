<?php

// nu dit nog in een class verpakken, zodat de expliciete require in Melden kan vervallen

function melden_menu($db, $lidId) {
    $request_gateway = new RequestGateway($db);

    $rows_geb = $request_gateway->countPerCode($lidId, 'GER');
    $links['geboorte'] = [
        'href' => 'Melden.php',
        'caption' => 'melden geboortes',
        'remark' => '',
    ];
    if ($rows_geb) {
        $links['geboorte']['href'] = 'MeldGeboortes.php';
        $links['geboorte']['remark'] = "&nbsp $rows_geb geboorte(s) te melden.";
    }

    $rows_afl = $request_gateway->countPerCode($lidId, 'AFV');
    $links['afvoer'] = [
        'href' => 'Melden.php',
        'caption' => 'melden afvoer',
        'remark' => '',
    ];
    if ($rows_afl) {
        $links['afvoer']['href'] = 'MeldAfvoer.php';
        $links['afvoer']['remark'] = "&nbsp; $rows_afl afvoer te melden.";
        if ($rows_afl > 60) {
            $links['afvoer']['remark'] .= "&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. ";
        }
    }

    $rows_uitv = $request_gateway->countPerCode($lidId, 'DOO');
    $links['uitval'] = [
        'href' => 'Melden.php',
        'caption' => 'melden uitval',
        'remark' => '',
    ];
    if ($rows_uitv) {
        $links['uitval']['href'] = 'MeldUitval.php';
        $links['uitval']['remark'] = "&nbsp $rows_uitv uitval te melden.";
    }

    $rows_aanw = $request_gateway->countPerCode($lidId, 'AAN');
    $links['aanwas'] = [
        'href' => 'Melden.php',
        'caption' => 'melden aanvoer',
        'remark' => '',
    ];
    if ($rows_aanw) {
        $links['aanwas']['href'] = 'MeldAanvoer.php';
        $links['aanwas']['remark'] = "&nbsp $rows_aanw aanwas te melden.";
    }

    $rows_omn = $request_gateway->countPerCode($lidId, 'VMD');
    $links['nummer'] = [
        'href' => 'Melden.php',
        'caption' => 'melden omnummeren',
        'remark' => '',
    ];
    if ($rows_omn) {
        $links['nummer']['href'] = 'MeldOmnummer.php';
        $links['nummer']['remark'] = "&nbsp $rows_aanw omnummering te melden.";
    }
    return $links;
}
