<?php

/*Save_Artikel.php toegpast in :
    - Medicijnen.php
    - Voer.php    */
$array = array();
foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
foreach ($multip_array as $recId => $id) {
    unset($updNaam);
    unset($updEenheid);
    unset($updActief);
    $artikel_gateway = new ArtikelGateway();
    foreach ($id as $key => $value) {
        if ($key == 'txtNaam' && !empty($value)) {
            $updNaam = $value;
        }
        if ($key == 'txtPres' && !empty($value)) {
            $updPres = $value;
        } elseif ($key == 'txtPres' && empty($value)) {
            $updPres = $updNaam;
        }
        if ($key == 'txtStdat' && !empty($value)) {
            $updStdat = $value;
        } elseif ($key == 'txtStdat' && empty($value)) {
            $updStdat = '';
        }
        if ($key == 'kzlNhd' && !empty($value)) {
            $updEenheid = $value;
        }
       // Eenheid bepaalt user dus mag niet leeg zijn
        if ($key == 'txtGewicht' && !empty($value)) {
            $updKg = $value;
        } elseif ($key == 'txtGewicht') {
            $updKg = '';
        }
        if ($key == 'kzlBtw' && !empty($value)) {
            $updBtw = $value;
        }
       // kzlBtw is nooit leeg
        if ($key == 'txtRegnr' && !empty($value)) {
            $updRegnr = $value;
        } elseif ($key == 'txtRegnr' && empty($value)) {
            $updRegnr = '';
        }
        if ($key == 'kzlRelatie' && !empty($value)) {
            $updRelatie = $value;
        } elseif ($key == 'kzlRelatie' && empty($value)) {
            $updRelatie = '';
        }
        if ($key == 'txtWdgnV' && !empty($value)) {
            $updWdgn_v = $value;
        } elseif ($key == 'txtWdgnV' && empty($value)) {
            $updWdgn_v = '';
        }
        if ($key == 'txtWdgnM' && !empty($value)) {
            $updWdgn_m = $value;
        } elseif ($key == 'txtWdgnM' && empty($value)) {
            $updWdgn_m = '';
        }
        if ($key == 'kzlRubriek' && !empty($value)) {
            $updRubriek = $value;
        } elseif ($key == 'kzlRubriek' && empty($value)) {
            $updRubriek = '';
        }
        if ($key == 'chkActief') {
            $updActief = $value;
        }
    }
    if (!isset($updActief)) {
        $updActief = 0;
    }
    if (isset($recId) and $recId > 0) {
        $zoek_in_database = $artikel_gateway->zoek_in_database($recId);
        while ($co = $zoek_in_database->fetch_assoc()) {
            $naam_db = $co['naam'];
            $pres_db = $co['naamreader'];
            if (!isset($pres_db)) {
                $pres_db = '';
            }
            $stdat_db = $co['stdat'];
            if (!isset($stdat_db)) {
                $stdat_db = '';
            }
            $eenheid_db = $co['enhuId'];
            $kg_db = $co['perkg'];
            if (!isset($kg_db)) {
                $kg_db = '';
            }
            $btw_db = $co['btw'];
            if (!isset($btw_db)) {
                $btw_db = '';
            }
            $regnr_db = $co['regnr'];
            if (!isset($regnr_db)) {
                $regnr_db = '';
            }
            $relId_db = $co['relId'];
            if (!isset($relId_db)) {
                $relId_db = '';
            }
            $wdgn_v_db = $co['wdgn_v'];
            if (!isset($wdgn_v_db)) {
                $wdgn_v_db = '';
            }
            $wdgn_m_db = $co['wdgn_m'];
            if (!isset($wdgn_m_db)) {
                $wdgn_m_db = '';
            }
            $rubuId_db = $co['rubuId'];
            if (!isset($rubuId_db)) {
                $rubuId_db = '';
            }
            $actief_db = $co['actief'];
            if (!isset($actief_db)) {
                $actief_db = 0;
            }
        }
        if (isset($updNaam) && $updNaam <> $naam_db && $actief_db == 1) {
            $artikel_gateway->wijzig_naam($updNaam, $recId);
        }
        if (isset($updPres) && $updPres <> $pres_db && $actief_db == 1) {
            $artikel_gateway->wijzig_naamreader($updPres, $recId);
        }
        if ($updStdat <> $stdat_db && $actief_db == 1) {
            $artikel_gateway->wijzig_stdat($updStdat, $recId);
        }
        if (isset($updEenheid) && $updEenheid <> $eenheid_db && $actief_db == 1) {
            $artikel_gateway->wijzig_eenheid($updEenheid, $recId);
        }
        if ($updKg <> $kg_db && $actief_db == 1) {
            $artikel_gateway->wijzig_perkg($updKg, $recId);
        }
        if ($updBtw <> $btw_db && $actief_db == 1) {
            $artikel_gateway->wijzig_btw($updBtw, $recId);
        }
        if ($updRegnr <> $regnr_db && $actief_db == 1) {
            $artikel_gateway->wijzig_regnr($updRegnr, $recId);
        }
        if ($updRelatie <> $relId_db && $actief_db == 1) {
            $artikel_gateway->wijzig_relatie($updRelatie, $recId);
        }
        if ($updWdgn_v <> $wdgn_v_db && $actief_db == 1) {
            $artikel_gateway->wijzig_wdgn_v($updWdgn_v, $recId);
        }
        if (isset($updWdgn_m) && $updWdgn_m <> $wdgn_m_db && $actief_db == 1) {
            $artikel_gateway->wijzig_wdgn_m($updWdgn_m, $recId);
        }
        if ($updRubriek <> $rubuId_db && $actief_db == 1) {
            $artikel_gateway->wijzig_rubriek($updRubriek, $recId);
        }
        if ($updActief <> $actief_db) {
            $artikel_gateway->wijzig_actief($updActief, $recId);
        }
    }
}
