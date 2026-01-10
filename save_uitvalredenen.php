<?php

/* 15-11-2015 : gemaakt
28-12-2016 : txtId verwijderd en recId toegevoegd. In update_reden veld `redId` gewijzigd in `reduId` binnen where clause
11-3-2017 :  Naast Id ook it (item) toegevoegd aan naam van de velden om opslaan reden en moment te kunnen splitsen. Hidden velden verwijderd in Uitval.php.
3-5-2020 : Aangepast voor Agrident reader
1-6-2020 : veld afvoer toegevoegd
12-02-2021 : veld sterfte toegevoegd. SQL beveiligd met quotes */
/* toegepast in :
- Uitval.php */
foreach ($_POST as $fldname => $fldvalue) {
    //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIndexFromKey($fldname)][Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
    // Opbouwen van een Multidimensional array met3 indexen.  [Id] [item] [naamveld] en een waarde nl. de veldwaarde.
}
$reden_gateway = new RedenGateway();
$moment_gateway = new MomentGateway();
foreach ($multip_array as $recId => $id) {
    foreach ($id as $item => $id) {
        // Item (reden of moment)  ophalen
        foreach ($id as $key => $value) {
            if ($key == 'chbUitval') {
                $fldUitv = $value;
            }
            if ($key == 'chbPil') {
                $fldPil = $value;
            }
            if ($key == 'chbAfvoer') {
                $fldAfoer = $value;
            }
            if ($key == 'chbSterfte') {
                $fldSterfte = $value;
            }
            if ($key == 'txtScan') {
                $fldScan = $value ?? null;
            }
            if ($key == 'chbActief') {
                $fldActief = $value;
            }
        }
        /*** CODE M.B.T. REDEN ***/
        if (isset($recId) && $recId > 0 && $item == 'reden') {
            $zoek_actief = $reden_gateway->zoek_in_db($recId);
            while ($act = $zoek_actief->fetch_assoc()) {
                $dbUitv = $act['uitval'];
                $dbPil = $act['pil'];
                $dbAfv = $act['afvoer'];
                $dbSterf = $act['sterfte'];
            }
            if ($fldUitv <> $dbUitv) {
                $update_reden = $reden_gateway->update_uitv($fldUitv, $recId);
            }
            if ($fldPil <> $dbPil) {
                $update_reden = $reden_gateway->update_pil($fldPil, $recId);
            }
            if ($fldAfoer <> $dbAfv) {
                $update_reden = $reden_gateway->update_afvoer($fldAfoer, $recId);
            }
            if ($fldSterfte <> $dbSterf) {
                $update_reden = $reden_gateway->update_sterfte($fldSterfte, $recId);
            }
        }
        /*** EINDE   CODE M.B.T. REDEN   EINDE ***/
        /*** CODE M.B.T. UITVALMOMENT ***/
        if (isset($recId) && $recId > 0 && $item == 'moment') {
            $zoek_scan = $moment_gateway->zoek_scan($recId);
            while ($m = $zoek_scan->fetch_assoc()) {
                $dbScan = $m['scan'];
            }
            unset($scan_aant);
            if (isset($fldScan)) {
                $zoek_dubbele_scan = $moment_gateway->zoek_dubbele_scan($lidId, $fldScan);
                while ($sc = $zoek_dubbele_scan->fetch_assoc()) {
                    $scan_aant = $sc['aant'];
                }
            }
            if (!isset($dbScan)) {
                $dbScan = null;
            }
            if (isset($fldScan) && $fldScan <> $dbScan && isset($scan_aant) && $scan_aant > 0) {
                $fout = "Deze scancode bestaat al.";
            } elseif (isset($fldScan) && $fldScan <> $dbScan) {
                $update_scan = $moment_gateway->update_scan($fldScan, $recId);
            }
            $zoek_actief = $moment_gateway->zoek_actief($recId);
            while ($ac = $zoek_actief->fetch_assoc()) {
                $dbActief = $ac['actief'];
            }
            if ($fldActief <> $dbActief) {
                $update_actief = $moment_gateway->update_actief($fldActief, $recId);
            }
        }
        /***  EINDE   CODE M.B.T. UITVALMOMENT   EINDE ***/
    }
}
