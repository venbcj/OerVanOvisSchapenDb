<?php

/*28-5-2020 gekopieerd van impWorpregistratie.php
31-5-2020 veld leef_dgn toegevoegd
3-7-2020 : Gegevens reader opgeslagen in 1 tabel impAgrident
24-1-2021 : MoederTransponder toegevoegd */
   
$velden_worp = array('ActId', 'MoederTransponder', 'Moeder', 'Datum', 'Verloop', 'RasId', 'Geboren', 'Lammeren');
$velden_lam = array('Geslacht', 'Leef_dgn', 'MomId', 'Reden');

$cnt_worp = count($velden_worp);
$last_element = $cnt_worp - 1;
$cnt_lam = count($velden_lam);
foreach ($inhoud as $key => $waarde) {
    for ($g = 0; $g < $cnt_worp; $g++) {
        // Er zijn maar $cnt_worp elementen in de array want element $cnt_worp is weer een array met meerdere elementen
        if ($g == 0) {
            $insert_qry_mdr = " INSERT INTO impAgrident SET ";
            $select_qry = "";
        }
        if ($g < $last_element && ($waarde -> {$velden_worp[$g]} == "" || $waarde -> {$velden_worp[$g]} == "0")) {
            $insert_qry_mdr .= "$velden_worp[$g] = NULL, ";
              $select_qry .= "ISNULL($velden_worp[$g]) and ";
        } elseif ($g < $last_element) {
            $insert_qry_mdr .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]}) . "', ";
            $select_qry .= "$velden_worp[$g] = '" . mysqli_real_escape_string($db, $waarde -> {$velden_worp[$g]}) . "' and ";
        }
        if ($g == $last_element) { // laatste element is array met lammeren
            $array = $waarde -> {$velden_worp[$g]};
            foreach ($array as $key1 => $waarde1) {
                $insert_qry_lam = "";
                for ($gl = 0; $gl < $cnt_lam; $gl++) {
                    if ($velden_lam[$gl] == "Reden" && ($waarde1 -> {$velden_lam[$gl]} == "" || $waarde -> {$velden_lam[$gl]} == "0" )) {
                        $insert_qry_lam .= "$velden_lam[$gl] = NULL, "; // Bij onvolledig doodgeboren wordt geen reden opgegeven. Bij andere velden moet de 0 wel blijven bestaan.
                    } else {
                        $insert_qry_lam .= "$velden_lam[$gl] = '" . mysqli_real_escape_string($db, $waarde1 -> {$velden_lam[$gl]}) . "', ";
                    }
                }
                $insert_qry = $insert_qry_mdr;
                $insert_qry .= $insert_qry_lam;
                $insert_qry .= ' lidId = ' . mysqli_real_escape_string($db, $lidid) . ';';
                echo $insert_qry;
                mysqli_query($db, $insert_qry) or die(mysqli_error($db));
                unset($insert_qry_lam);
                unset($insert_qry);
            }
        }
    }
}
