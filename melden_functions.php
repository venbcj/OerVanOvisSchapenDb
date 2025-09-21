<?php

function melden_menu($db, $lidId) {
    $rows_geb = aantal_te_melden($db, $lidId, 'GER');
    $target['geboorte'] = 'Melden.php';
    $caption['geboorte'] = 'melden geboortes';
    $remark['geboorte'] = '';
    if ($rows_geb) {
        $target['geboorte'] = 'MeldGeboortes.php';
        $remark['geboorte'] = "&nbsp $rows_geb geboorte(s) te melden.";
    }
    $rows_afl = aantal_te_melden($db, $lidId, 'AFV');
    $target['afvoer'] = 'Melden.php';
    $caption['afvoer'] = 'melden afvoer';
    $remark['afvoer'] = '';
    if ($rows_afl) {
        $target['afvoer'] = 'MeldAfvoer.php';
        $remark['afvoer'] = "&nbsp; $rows_afl afvoer te melden.";
        if ($rows_afl > 60) {
            $remark['afvoer'] .= "&nbsp&nbsp&nbsp U ziet per melding max. 60 schapen. ";
        }
    }
    $rows_uitv = aantal_te_melden($db, $lidId, 'DOO');
    $target['uitval'] = 'Melden.php';
    $caption['uitval'] = 'melden uitval';
    $remark['uitval'] = '';
    if ($rows_uitv) {
        $target['uitval'] = 'MeldUitval.php';
        $remark['uitval'] = "&nbsp $rows_uitv uitval te melden.";
    }
    $rows_aanw = aantal_te_melden($db, $lidId, 'AAN');
    $target['aanwas'] = 'Melden.php';
    $caption['aanwas'] = 'melden aanvoer';
    $remark['aanwas'] = '';
    if ($rows_aanw) {
        $target['aanwas'] = 'MeldAanvoer.php';
        $remark['aanwas'] = "&nbsp $rows_aanw aanwas te melden.";
    }
    $rows_omn = aantal_te_melden($db, $lidId, 'VMD');
    $target['nummer'] = 'Melden.php';
    $caption['nummer'] = 'melden omnummeren';
    $remark['nummer'] = '';
    if ($rows_omn) {
        $target['nummer'] = 'MeldOmnummer.php';
        $remark['nummer'] = "&nbsp $rows_aanw omnummering te melden.";
    }
    return [$target, $caption, $remark];
}
