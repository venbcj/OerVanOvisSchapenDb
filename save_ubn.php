<?php

/* 03-07-2025 : Bestand gemaakt als kopie van save_hok.php  */

// SPIKE-plan #0004181
// In plaats van te posten met veldnamen zoals chbActief_4,
// kun je de elementen ook ubn[4][actief] noemen
//
foreach($_POST as $fldname => $fldvalue) {
    //  Voor elke post die wordt doorlopen wordt de veldnaam en de waarde teruggeven als een array
    $multip_array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue;
    // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}
foreach($multip_array as $ubnId => $id) {
    unset($fldActief);
    unset($fldDelete);
    unset($fldAdres);
    unset($fldPlaats);
    if($ubnId > 0) {
        foreach($id as $key => $value) {
            if ($key == 'chbActief') {
                $fldActief = $value;  
            }
            if ($key == 'chbDel') {
                $fldDelete = $value;  
            }
            if ($key == 'txtAdres') {
                $fldAdres = $value;  
            }
            if ($key == 'txtPlaats') {
                $fldPlaats = $value;  
            }
        }
        if(!isset($fldActief)) {
            $fldActief = 0; 
        }
        # NOTE: wij krijgen ubn_gateway van onze includer
        $ubn_row = $ubn_gateway->zoek_op_id_met_plaats($ubnId);
        # deze drie updates zouden in 1 statement kunnen
        if(isset($fldAdres) && $fldAdres <> $ubn_row['adres']) {
            // isset($fldAdres) is nodig als een inactief ubn weer actief wordt gemaakt en een adres heeft. 
            $ubn_gateway->update_adres($ubnId, $fldAdres);
        }
        if(isset($fldPlaats) && $fldPlaats <> $ubn_row['plaats']) {
            $ubn_gateway->update_plaats($ubnId, $fldPlaats);
        }
        if($fldActief <> $ubn_row['actief']) {
            $ubn_gateway->update_actief($ubnId, $fldActief);
        }
        if(isset($fldDelete)) {
            $ubn_gateway->delete_by_id($ubnId);
        }
    } // Einde if($ubnId > 0)
} // Einde foreach($multip_array as $ubnId => $id)
