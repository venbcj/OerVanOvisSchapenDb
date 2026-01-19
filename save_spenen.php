<?php

/* 22-11-2015 gemaakt
20-1-2017 : query aangepast n.a.v. nieuwe tblDoel. Speengewicht niet verplicht gemaakt    22-1-2017 tblBezetting gewijzigd naar tblBezet
13-2-2017 : tblPeriode verwijderd en verblijf opgeslagen in tblBezet
13-4-2019 : Volwassendieren kunnen ook uit verblijf worden gehaald door overplaasten of verlaten
26-4-2020 : $minDag als extra ontrole weggehaald. Controle zit ook al in HokSpenen.php
11-4-2021 : extra controle of speendatum al bestaat met isset($actId). Bijv. als pagina meerdere malen wordt verstuurd
29-12-2023 : and h.skip = 0 toegevoegd bij tblHistorie */
$array = array();
foreach ($_POST as $key => $value) {
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach ($array as $recId => $id) {
// recId ophalen
//echo '$recId = '.$recId.'<br>';
// Einde recId ophalen
    foreach ($id as $key => $value) {
        if ($key == 'chbkies' && $value == 1) {
            $box = $value ;
            foreach ($id as $key => $value) {
                if ($key == 'txtDatum') {
                    $dag = date_create($value);
                    $updDag =  date_format($dag, 'Y-m-d');
                }
                if ($key == 'txtKg' && !empty($value)) {
                    $updKg = $value;
                } elseif ($key == 'txtKg' && empty($value)) {
                    $updKg = '';
                }
                unset($kzlHok);
                if ($key == 'kzlHok' && !empty($value)) {
                    $kzlHok = $value;
                }
            }
            $zoek_generatie = mysqli_query($db, "
SELECT hisId
FROM tblStal st
 join tblHistorie h on (st.stalId = h.stalId)
WHERE st.schaapId = '" . mysqli_real_escape_string($db, $recId) . "' and h.actId = 3 and h.skip = 0
") or die(mysqli_error($db));
            while ($ge = mysqli_fetch_assoc($zoek_generatie)) {
                $aanw = $ge['hisId'];
            }
            if (isset($aanw)) {
                $gener = 'ouder';
            } else {
                $gener = 'lam';
            }
            $zoek_spenen = mysqli_query($db, "
SELECT hisId
FROM tblStal st
 join tblHistorie h on (st.stalId = h.stalId)
WHERE st.schaapId = '" . mysqli_real_escape_string($db, $recId) . "' and h.actId = 4 and h.skip = 0
") or die(mysqli_error($db));
            while ($sp = mysqli_fetch_assoc($zoek_spenen)) {
                $speen = $sp['hisId'];
            }
   // CONTROLE op alle verplichten velden bij spenen lam
            if (isset($recId) && $recId > 0 && !empty($updDag)) {
                  /*
                  echo "Datum = ".$updDag.'<br>' ;
                  echo "Kg = ".$updKg.'<br>' ;
                  echo "hokId = ".$newHok.'<br><br>' ; */
                      $stalId = 0;
                  $zoek_stalId = mysqli_query($db, "
SELECT stalId
FROM tblStal st
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE isnull(st.rel_best) and st.schaapId = '".mysqli_real_escape_string($db,$recId)."' and u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die(mysqli_error($db));
                while ($st = mysqli_fetch_assoc($zoek_stalId)) {
                    $stalId = $st['stalId'];
                }
                  //echo '$stalId = '.$stalId.'<br>';
                if (isset($kzlHok) && $gener == 'lam' && !isset($speen)) {
                    $actId = 4;
                } elseif (isset($kzlHok) && $gener == 'ouder') {
                    $actId = 5;
                } elseif (!isset($kzlHok)) {
                    $actId = 7;
                }
                if (isset($actId)) {
                //Bij meerder keren versturen van pagina bij spenen bestaat $actId niet meer!
                    $insert_tblHistorie = "
INSERT INTO tblHistorie
set stalId = '" . mysqli_real_escape_string($db, $stalId) . "', datum = '" . mysqli_real_escape_string($db, $updDag) . "', kg = " . db_null_input($updKg) . ", actId = '" . mysqli_real_escape_string($db, $actId) . "'
";
                    mysqli_query($db, $insert_tblHistorie) or die(mysqli_error($db));
                    if (isset($kzlHok)) {
           // Als moet worden overgplaatst en dus niet volwassen dieren die verblijf alleen verlaten
                        $zoek_hisId = mysqli_query($db, "
SELECT max(hisId) hisId
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = '".mysqli_real_escape_string($db,$actId)."'
") or die(mysqli_error($db));
                        while ($hi = mysqli_fetch_assoc($zoek_hisId)) {
                                               $hisId = $hi['hisId'];
                        }
                        if (!isset($newHok) || (isset($newHok) && $kzlHok <> $newHok)) {
                                        $newHok = $kzlHok;
                        }
                        if (isset($hisId)) {
                                         // $hisId bestaat niet bij verlaten volwassen dieren
                                        $insert_tblBezet = "INSERT INTO tblBezet set hisId = '" . mysqli_real_escape_string($db, $hisId) . "', hokId = '" . mysqli_real_escape_string($db, $newHok) . "' ";
                                mysqli_query($db, $insert_tblBezet) or die(mysqli_error($db));
                        }
                    }
                 // Einde if(isset($kzlHok))
                    unset($actId);
                }
 // Einde if(isset($actId))
            }
   // EINDE CONTROLE op alle verplichten velden bij spenen lam
        }
    // EINDE Alleen als checkbox chbkies de waarde 1 heeft
    }
}
