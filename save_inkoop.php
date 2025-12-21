<?php

/*Save_Artikel.php toegpast in :
- Inkopen.php
<!-- 16-6-2018 gemaakt
    28-11-2020 velde chkDel tegevoegd
    18-1-2022 SQL beveiligd met quotes
-->
 */
foreach ($_POST as $fldname => $fldvalue) {
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
}
$inkoop_gateway = new InkoopGateway();
foreach ($multip_array as $recId => $id) {
    unset($updPrijs);
    unset($delRec);
    foreach ($id as $key => $value) {
        if ($key == 'txtPrijs' && !empty($value)) {
            $updPrijs = str_replace(',', '.', $value);
        }
        if ($key == 'chkDel') {
            $delRec = $value;
        }
    }
    if ($recId > 0) {
        $inkoop_gateway->set_prijs($updPrijs, $recId);
        if (isset($delRec)) {
            $inkoop_gateway->remove($recId);
        }
    }
}
