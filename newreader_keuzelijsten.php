<?php

/*1-6-2020 gemaakt 
wordt gebruikt in
 - Newuser.php
 - Gebruiker.php

Bij gebruik van reader Agrident moeten er bepaalde redenen t.b.v. uitval en afvoer in gebruik zijn bij een gebruiker 
20-6-2020 : controle op bestaan Lambar toegevoegd 
 31-1-2021 : Sql beveiligd met quotes. 
 12-02-2021 : Controle Lambar hier weggehaald en in impVerplaatsingen.php toegevoegd. */

$array_uitval = array( 8, 13, 22, 42, 43, 44 ); /*8 Klem gezeten 13 Onbekend 22 Zwak 42 In het vlies 43 Misvormd 44 Verkeerde ligging */
$array_afvoer = array( 15, 45, 46, 47, 48, 49, 50, 51); /*15 Prolaps 45 Slecht uier 46 Slacht ooi 47 Weinig melk 48 Verwerper 49 Gust 51 Weide lam */

// Aanvullen of bijwerken redenen uitval
for ($i = 0; $i< count($array_uitval); $i++) {
    unset($rd_db);
    $zoek_reden = mysqli_query($db,"
SELECT redId
FROM tblRedenuser
WHERE redId = '".mysqli_real_escape_string($db,$array_uitval[$i])."' and lidId = '".mysqli_real_escape_string($db,$lidid)."'
") or die (mysqli_error($db));
while ( $zr = mysqli_fetch_assoc($zoek_reden)) {
    $rd_db = $zr['redId']; 
}
if(isset($rd_db)) {
    $SQL = "UPDATE tblRedenuser set uitval = 1 WHERE redId = '".mysqli_real_escape_string($db,$array_uitval[$i])."' and lidId = '".mysqli_real_escape_string($db,$lidid)."' ";
} else {
    $SQL = "INSERT INTO tblRedenuser set uitval=1, redId = '".mysqli_real_escape_string($db,$array_uitval[$i])."', lidId = '".mysqli_real_escape_string($db,$lidid)."' ";
}
    mysqli_query($db,$SQL) or die (mysqli_error($db));
}
// Einde Controle redenen uitval

// Aanvullen of bijwerken redenen afvoer
for($j = 0; $j< count($array_afvoer); $j++) {
    unset($rd_db);
    $zoek_reden = mysqli_query($db,"
SELECT redId
FROM tblRedenuser
WHERE redId = '".mysqli_real_escape_string($db,$array_afvoer[$j])."' and lidId = '".mysqli_real_escape_string($db,$lidid)."' 
") or die (mysqli_error($db));
    while ( $zr = mysqli_fetch_assoc($zoek_reden)) { $rd_db = $zr['redId']; }
if(isset($rd_db)) {
    $SQL = "UPDATE tblRedenuser set afvoer = 1 WHERE redId = '".mysqli_real_escape_string($db,$array_afvoer[$j])."' and lidId = '".mysqli_real_escape_string($db,$lidid)."'";
} else {
    $SQL = "INSERT INTO tblRedenuser set afvoer = 1, redId = '".mysqli_real_escape_string($db,$array_afvoer[$j])."', lidId = '".mysqli_real_escape_string($db,$lidid)."'";
}
        mysqli_query($db,$SQL) or die (mysqli_error($db));
}
// Einde Controle redenen afvoer
