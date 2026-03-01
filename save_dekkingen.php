<?php

/* 21-10-2018 gemaakt
17-02-2021 : SQL beveiligd met quotes
25-12-2021 : Bestand hernoemd van save_dracht.php naar save_dekkingen.php
28-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    #echo '<br>'.'$recId = '.$recId.'<br>';
    if (!empty($recId)) {
        unset($delete);
        unset($updRam);
        unset($fldDracht);
        unset($fldDrachtdm);
        unset($updGrootte);
        foreach ($id as $key => $value) {
            if ($key == 'chkDel') {
                $delete = 1;
            }
            if ($key == 'kzlRam' && !empty($value)) {
/*echo $key.'='.$value.' ';*/ $updRam = $value;
            }
            if ($key == 'kzlDrachtUpd') {
                $fldDracht = $value;
            }
            if ($key == 'txtDrachtdm' && !empty($value)) {
                $fldDrachtdm = $value;
                $makeday = date_create($value);
                $fldDmDracht = date_format($makeday, 'Y-m-d');
            }
            if ($key == 'txtGrootte' && !empty($value)) {
                $updGrootte = $value;
            }
        }
        unset($dmDek);
        unset($drachtdm_db);
        unset($schaapId);
        $volwas_gateway = new VolwasGateway();
        $dmDek = $volwas_gateway->zoek_dekdatum($recId);
        $volwas_gateway = new VolwasGateway();
        $ooiId = $volwas_gateway->zoek_ooi($recId);
        $schaap_gateway = new SchaapGateway();
        $dmWorp = $schaap_gateway->zoek_laatste_worpdatum($ooiId);
        $dracht_gateway = new DrachtGateway();
        [ $hisId_dr_db , $drachtdm_db ] = $dracht_gateway->zoek_drachtdatum($recId);
        $volwas_gateway = new VolwasGateway();
        $schaapId = $volwas_gateway->zoek_worp($recId);
        if (isset($drachtdm_db) || isset($schaapId)) {
            $drachtig = 'ja';
        } else {
            $drachtig = 'nee';
        }
        $volwas_gateway = new VolwasGateway();
        [$vdr_db, $grootte_db] = $volwas_gateway->zoek_worpgrootte_database($recId);
// Dekking verwijderen
        if (isset($delete)) {
            $volwas_gateway = new VolwasGateway();
            $hisId = $volwas_gateway->zoek_hisId($recId);
            $historie_gateway = new HistorieGateway();
            $historie_gateway->delete_dracht($hisId);
        }
// Ram wijzigen
        if (isset($updRam) && $vdr_db <> $updRam) {
            $volwas_gateway = new VolwasGateway();
            $volwas_gateway->updateRam($updRam, $recId);
        }
// Dracht wijzigen
        if ($drachtig == 'ja' && $fldDracht == 'ja') {
            // Drachtdatum wijzigen
            if ($drachtdm_db <> $fldDmDracht) {
                if (!isset($fldDrachtdm)) {
                    $fout = 'De drachtdatum is niet bekend.';
                } elseif ($dmDek > $fldDmDracht) {
                    $fout = 'De drachtdatum kan niet voor de dekdatum liggen.';
                } elseif ($dmWorp > $fldDmDracht) {
                    $fout = 'De drachtdatum kan niet voor de laatste werpdatum liggen.';
                } else {
                    $historie_gateway = new HistorieGateway();
                    $historie_gateway->updateDracht($fldDmDracht, $hisId_dr_db);
                }
            }
            // Worpgrootte wijzigen
            if ($grootte_db <> $updGrootte) {
                $volwas_gateway = new VolwasGateway();
                $volwas_gateway->updateDracht($updGrootte, $recId);
            }
        } elseif ($drachtig == 'nee' && $fldDracht == 'ja') {
            // dracht aanmaken
            if (!isset($fldDrachtdm)) {
                $fout = 'De drachtdatum is niet bekend.';
            } elseif ($dmDek > $fldDmDracht) {
                $fout = 'De drachtdatum kan niet voor de dekdatum liggen.';
            } elseif ($dmWorp > $fldDmDracht) {
                $fout = 'De drachtdatum kan niet voor de laatste worpdatum liggen.';
            } else {
                $volwas_gateway = new VolwasGateway();
                        $mdrId = $volwas_gateway->zoek_mdrId($recId);
                        $stal_gateway = new StalGateway();
                        $stalId = $stal_gateway->zoek_stalId($mdrId, $lidId);
                        $historie_gateway = new HistorieGateway();
                        $hisId = $historie_gateway->insert_tblHistorie_19($stalId, $fldDmDracht);
                    $dracht_gateway = new DrachtGateway();
                    $dracht_gateway->insert_tblDracht($recId, $hisId);
            }
        }
        if ($drachtig == 'ja' && $fldDracht == 'nee') {
            $dracht_gateway = new DrachtGateway();
            $hisId = $dracht_gateway->zoek_hisId($recId);
            $historie_gateway = new HistorieGateway();
            $update_tblHistorie = $historie_gateway->update_tblHistorie($hisId);
            $volwas_gateway = new VolwasGateway();
            $volwas_gateway->updateDracht2($recId);
        }
// Einde Dracht wijzigen
    } // Einde if(!empty($recId))
}
