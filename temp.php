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

	echo '<br>';
	echo '<br>';

$vandaag = $now->format("Y-m-d"); Echo '$vandaag = '.$vandaag.'<br>';
//$jaartal = $now->format("Y");  echo '$jaartal = '.$jaartal.'<br>';

$max = date('Y-m-d',strtotime('2018-05-02'));
Echo '$max = '.$max.'<br>'.'<br>';


$now = time(); // or your date as well
$your_date = strtotime("2018-7-5");
$datediff = $now - $your_date;
$dagen = round($datediff / (60 * 60 * 24));

echo $dagen.'<br>';

$ins_tblHistorie = "
SELECT min(dmdek) datum FROM `tblDeklijst_basis` 	
";
//	mysqli_query($db,$ins_tblHistorie) or die(mysqli_error($db));
//echo '$ins_tblHistorie = '.$ins_tblHistorie.'<br>'.'<br>'; 


/*
include "connect_db.php";
$zoek_min_jaar = mysqli_query($db,"
SELECT year(max(datum)) jaar
FROM (
	SELECT datum FROM `tblHistorie_basis` 
	UNION
	SELECT dmink FROM `tblInkoop_basis` 
	UNION
	SELECT dmafsluit FROM `tblPeriode_basis` 
) t
") or die (mysqli_error($db));

while ($mn = mysqli_fetch_assoc($zoek_min_jaar)) { $jaar = $mn['jaar']; }

$now = DateTime::createFromFormat('U.u', microtime(true));
$ditjaar = $now->format("Y");
$jaren = $jaar;  echo '$jaren = '.$jaren.'<br>';


$lidId = 8; echo '$lidId = '.$lidId.'<br>';
$now = DateTime::createFromFormat('U.u', microtime(true));
$jaartal = $now->format("Y");  echo '$jaartal = '.$jaartal.'<br>';
$maandag52 = date( "j", strtotime($jaartal."W"."52"."1") );  echo '$maandag52 = '.$maandag52.'<br>';
$dag1 = date("d", strtotime("first monday of january $jaartal"));  echo '$dag1 = '.$dag1.'<br>';
$monday1 = date( "Y-m-d", strtotime($jaartal."W"."01"."1") );  echo '$monday1 = '.$monday1.'<br>';
$day = strtotime($monday1)-(86400*7);  echo '$day = '.$day.'<br>';

if($maandag52 > 24) { $weken_jaar = 52; } else { $weken_jaar = 53; }
if($dag1 < 05) { $startweek = 1; } else { $startweek = 2; }



for ($i = $startweek ; $i <= $weken_jaar ; $i++){
    
    if($startweek == 2){ $dId = $i-1; } else { $dId = $i; }
        $datum = date('Y-m-d',$day+($i*86400*7)); /*echo '$datum week '.$i.' = '.$datum.'<br>';*/
/*
if($dId < 53) {        
$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dekat, dmdek)
	SELECT '".mysqli_real_escape_string($db,$lidId)."', dekat, '".mysqli_real_escape_string($db,$datum)."'
	FROM tblDeklijst_basis
	WHERE dekId = '".mysqli_real_escape_string($db,$dId)."'
";
} else {
	$ins_tblDeklijst = "
INSERT INTO tblDeklijst (lidId, dmdek) VALUES ('".mysqli_real_escape_string($db,$lidId)."', '".mysqli_real_escape_string($db,$datum)."')
";
}
      // mysqli_query($db,$ins_tblDeklijst) or die(mysqli_error($db));
      // echo 'query inlezen dekId '.$dId.' = '.$ins_tblDeklijst.'<br>';
       }*/



/*SELECT year(dmdek) jaar, min(dmdek) datum 
FROM tblDeklijst
GROUP BY year(dmdek); */


$dag = date_create('28-01-2023'); $fldDag = date_format($dag, 'Y-m-d');  
  echo '$fldDag = '.$fldDag.'<br>';  
  

 $fldDag = strtotime($fldDag);
      $fldDrachtDay = date('Y-m-d', strtotime("-145 day", $fldDag));
  //    $fldDekDay = date('Y-m-d', strtotime("-290 day", $fldDag));
  echo '$fldDrachtDay = '.$fldDrachtDay.'<br>';  
//  echo '$fldDekDay = '.$fldDekDay.'<br>';  

?>