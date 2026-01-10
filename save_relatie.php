<?php

/* 29-12-2023 : sql beveiligd met quotes en db_null_input() en veld actief bij Rendac niet wijigbaar gemaakt
toegepast in :
- Relatie.php */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
$adres_gateway = new AdresGateway();
$partij_gateway = new PartijGateway();
$relatie_gateway = new RelatieGateway();
foreach ($multip_array as $id) {
    // TODO: #0004225 verwijder dubbele lus
    foreach ($id as $key => $value) {
        if ($key == 'txtrId') {
            foreach ($id as $key => $value) {
                if ($key == 'txtrId' && !empty($value)) {
                    $updId = $value;
                }
                if ($key == 'txtStraat' && !empty($value)) {
                    $fldStraat = $value;
                } elseif ($key == 'txtStraat' && empty($value)) {
                    $fldStraat = '';
                }
                if ($key == 'txtNr' && !empty($value)) {
                    $fldNr = $value;
                } elseif ($key == 'txtNr' && empty($value)) {
                    $fldNr = '';
                }
                if ($key == 'txtPc' && !empty($value)) {
                    $fldPc = $value;
                } elseif ($key == 'txtPc' && empty($value)) {
                    $fldPc = '';
                }
                if ($key == 'txtPlaats' && !empty($value)) {
                    $fldPlaats = $value;
                } elseif ($key == 'txtPlaats' && empty($value)) {
                    $fldPlaats = '';
                }
                if ($key == 'chkActief' && !empty($value)) {
                    $fldActief = $value;
                } //else if ($key == 'chkActief' && empty($value)) { $fldActief = 0; }
            }
            if (isset($updId)) {
                $adrId = $adres_gateway->zoek_adres($updId);
                // Invoer adres als deze nog niet bestaat
                if (
                    !isset($adrId) && ( // als adres niet bestaat en plaats, nr, postcode of woonplaats is ingevuld
                        $fldStraat != '' || $fldNr != '' || $fldPc != '' || $fldPlaats != ''
                    )
                ) {
                    $invoeradres = $adres_gateway->invoeradres($updId);
                }
                $straat = $partij_gateway->zoek_straat($updId);
                if (isset($fldStraat) && $fldStraat <> $straat) {
                    $wijzigstraat = $adres_gateway->wijzigstraat($fldStraat, $updId);
                }
                unset($straat);
                $huisnr = $partij_gateway->zoek_nr($updId);
                if (isset($fldNr) && $fldNr <> $huisnr) {
                    $adres_gateway->wijzignummer($fldNr, $updId);
                }
                unset($huisnr);
                $postcode = $relatie_gateway->zoek_postcode($updId);
                if (isset($fldPc) && $fldPc <> $postcode) {
                    $wijzigpostcode = $adres_gateway->wijzigpostcode($fldPc, $updId);
                }
                unset($postcode);
                $plaats = $relatie_gateway->zoek_plaats($updId);
                if (isset($fldPlaats) && $fldPlaats <> $plaats) {
                    $wijzigplaats = $adres_gateway->wijzigplaats($fldPlaats, $updId);
                }
                unset($plaats);
                $rel_ren = $relatie_gateway->zoek_rendac($updId);
                $actief = $relatie_gateway->zoek_actief($updId);
                if (!isset($rel_ren)) {
                    $wijzigactief = $relatie_gateway->wijzigactief($fldActief, $updId);
                }
                unset($rel_ren);
                unset($fldActief);
            }
        }
    }
}
