<?php

// Toegepast in :  Deklijst.php

$year = $year+0; // komt uit Deklijst.php en maakt $year numeriek 
$week1 = "01"; // Week 1 Dit moet 01 zijn tussen dubbele quotes !!
$week_eind = 52; // Week 52
/*
$maandag = date( "l, j M, Y", strtotime($year."W".$week1."1") ); // Eerste maandag van het jaar voluit
$maandag_end = date( "l, j M, Y", strtotime($year."W".$week_eind."1") ); // Laatste maandag van het jaar voluit
    echo $maandag . " - " . $maandag_end . '<br>';*/
    
$maandag1 = date( "Y-m-d", strtotime($year."W".$week1."1") ); // De maandag van week 1. Let op is soms nog december van het vorige jaar. Bijv. 29-12-2014
$maandag52 = date( "j", strtotime($year."W".$week_eind."1") ); // Laatste maandag van de maand december
    //echo $maandag1 . " - " . $maandag52;

if($maandag52 > 24) { $weken_jaar = 52; } else { $weken_jaar = 53; }
    //echo $weken_jaar.' weken in het jaar '.$year. '<br><br>';


    
// a.d.h.v. een for-loop de user_id invullen 
$day = strtotime($maandag1)-(86400*7); // De eerste maandag voorafgaand aan de loop moet 7 dagen voor de eerste maandag van het jaar liggen

for ($i = 1 ; $i <= $weken_jaar ; $i++){
    
        $datum = date('Y-m-d',$day+($i*86400*7)).'<br>';
        $juiste_jaar = date('Y',$day+($i*86400*7)).'<br>'; // Soms valt de eerste maandag in het vorige jaar bijv. 29-12-2014 is week 1 van 2015. 
        
        if($juiste_jaar == $year) { 
        
            //echo $datum;
            
        $Dek_jaar = "INSERT INTO tblDeklijst SET lidId = ".mysqli_real_escape_string($db,$lidId).", dmdek = '$datum' ";
            mysqli_query($db,$Dek_jaar) or die (mysqli_error($db));
        
        } 
}


// Kijken of het jaar ook binnen de liquiditeit moet worden aangemaakt.

$zoek_jaar = mysqli_query($db,"SELECT year(datum) jaar FROM tblLiquiditeit li join tblRubriekuser ru on (li.rubuId = ru.rubuId)WHERE ru.lidId  = ".mysqli_real_escape_string($db,$lidId)." and year(datum) = '$year' GROUP BY year(datum) ") or die (mysqli_error($db));
    while ( $zjr = mysqli_fetch_assoc($zoek_jaar)) { $liq_jaar = $zjr['jaar']; }
    
    if(!isset($liq_jaar)) { $new_jaar = $year; include "create_liquiditeit.php"; }

// EINDE Kijken of het jaar ook binnen de liquiditeit moet worden aangemaakt.

?>
