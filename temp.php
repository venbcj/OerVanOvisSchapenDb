<?php 

$now = DateTime::createFromFormat('U.u', microtime(true));

$Backup = $now->format("Y-m-d_H:i:s.u");
$Backupnaam = 'reader_'.$Backup.'.txt';

echo $Backupnaam.'<br><br><br>';


$now = DateTime::createFromFormat('U.u', microtime(true));
echo $now->format("Y-m-d_H:i:s.u").'<br>';


$velden_worp = array('actId', 'moeder', 'datum', 'rasId', 'hokId', 'verloop', 'geboren', 'levend', 'reden', 'lammeren');


$cnt_worp = count($velden_worp);
$vldn_worp = $cnt_worp-1;

echo '<br> Aantal elementen ='.$cnt_worp.'<br>';

for($g = 0; $g < $cnt_worp; $g++) {

    echo $velden_worp[$g].'<br>';

}

?>