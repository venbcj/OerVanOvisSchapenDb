<?php

/* toegepast in :
<!-- 15-11-2015 : gemaakt
30-5-2020 : hidden velden txtId, ctrScan en ctrActief verwijderd -->
- Ras.php */
foreach ($_POST as $fldname => $fldvalue) {  //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;  // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde.
}
foreach ($multip_array as $recId => $id) {
    unset($fldScan);
    unset($fldSort);
    if (!empty($recId)) {
        foreach ($id as $key => $value) {
            if ($key == 'txtScan' && !empty($value)) {
                $fldScan = $value;
            }
            if ($key == 'txtSort' && !empty($value)) {
                $fldSort = $value;
            }
            if ($key == 'chbActief') {
                $fldActief = $value;
            }
        }
        $ras_gateway = new RasGateway();
        $ras = $ras_gateway->zoek_ras_bij($recId, $lidId);
        if ($reader == 'Biocontrol' && $fldScan <> $ras['scan']) { //$fldScan bestaat niet bij Agrident reader
            // Zoeken naar dubbel scancode
            $aantsc = $ras_gateway->countScan($lidId, $fldScan);
            if ($aantsc) {
                $fout = " Het scannr bestaat al.";
            } else {
                $ras_gateway->updateScan($lidId, $fldScan, $recId);
            }
        }
        if ($reader == 'Agrident' && $fldSort <> $ras['sort']) {
            $ras_gateway->updateScan($lidId, $fldScan, $recId);
        }
        if ($fldActief <> $ras['actief']) {
            $ras_gateway->set_actief($recId, $fldActief);
            header("Location:" . Url::getWebroot() . "Ras.php");
        }
    }
}
