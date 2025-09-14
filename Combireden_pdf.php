<?php

require_once("autoload.php");

/* https://www.youtube.com/watch?v=CamDi3Syjy4
9-8-2019 : www. weggehaald bij url 
*/

require('fpdf/fpdf.php');

include "database.php";

    $db = mysqli_connect($host, $user, $pw, $dtb);

    if ($db == false )
    {
        echo 'Connectie database niet gelukt';
    }


$stal = $_GET['Id'];

$rapport = 'Combinaties met redenen';
$Afdrukstand = 'P';
if ($Afdrukstand == 'P') { $headerWidth = 190; $imageWidth = 169; }
if ($Afdrukstand == 'L') { $headerWidth = 277; $imageWidth = 256; }

$zoek_lid = mysqli_query($db,"
SELECT lidId
FROM tblStal
WHERE stalId = ".mysqli_real_escape_string($db,$stal)." 
") or die (mysqli_error($db));
While ($row = mysqli_fetch_assoc($zoek_lid)) {    $lidId = $row['lidId']; }


// *** Opbouwen van een array met die links en rechts op het rapport reps. reden uitval en reden medicijnen opbouwt ***
$zoek_reden_uitval = mysqli_query($db,"
SELECT cr.scan, r.reden
from tblCombireden cr
 join tblRedenuser ru on (cr.reduId = ru.reduId)
 join tblReden r on (r.redId = ru.redId)
where ru.lidId = ".mysqli_real_escape_string($db,$lidId)." and cr.tbl = 'd'
order by cr.scan
") or die (mysqli_error($db));
while ($row = mysqli_fetch_assoc($zoek_reden_uitval)) {
    $scan = $row['scan'];
    $reden = $row['reden'];

$array_d[] = array($scan,$reden);
}

$zoek_reden_medicijn = mysqli_query($db,"
SELECT cr.scan, a.naam, round(cr.stdat) stdat, r.reden
from tblCombireden cr
 join tblArtikel a on (cr.artId = a.artId)
 join tblEenheiduser eu on (eu.enhuId = a.enhuId)
 left join tblRedenuser ru on (cr.reduId = ru.reduId)
 left join tblReden r on (r.redId = ru.redId)
where eu.lidId = ".mysqli_real_escape_string($db,$lidId)." and cr.tbl = 'p'
order by cr.scan
") or die (mysqli_error($db));
while ($row = mysqli_fetch_assoc($zoek_reden_medicijn)) {
    $scan = $row['scan'];
    $artikel = $row['naam'];
    $stdat = $row['stdat'];
    $reden = $row['reden']; if(!isset($reden)) { $reden = ''; }

$array_p[] = array(4=>$scan,$artikel,$stdat,$reden);
}

if(isset($array_d)) { $count_ar1 = count($array_d); } else { $count_ar1 = 0; } //echo '$count_ar1 = '.$count_ar1.'<br>';
if(isset($array_p)) { $count_ar2 = count($array_p); } else { $count_ar2 = 0; } //echo '$count_ar2 = '.$count_ar2.'<br>';

$max = $count_ar1;
if($count_ar2 > $count_ar1) { $max = $count_ar2; } //echo '$max = '.$max.'<br>';

for($i=0; $i<$max; $i++) { // doorloop een loop een aantal keer dat gelijk is aan het maximum records van 1 van de twee query's
// $i is een index en begint bij 0. Als $i =3 dan is $max = 4 !!
// echo '$i = '.$i.'<br>';
    if($i < $count_ar1) {             // zolang $i kleiner is dan het aantal records uit query1 
        $ar_d = $array_d[$i];        // Vul de variable met het recordnr $i van query1
        foreach ($ar_d as $value) { // doorloop de velden uit recordnr $i van query1
            
            $temp_ar[] = $value;        // vul de array '$temp_ar' met de velden uit query1
        }
    }
    else {                             // Als $i niet kleiner is dan het aantal records uit query1

        for($l=0;$l<2;$l++){        // doorloop een loop 2x

            $temp_ar[] = '';            // vul de array '$temp_ar' met lege waarden.
         }
    }

    if($i < $count_ar2) {            // zolang $i kleiner is dan het aantal records uit query2
        $ar_p = $array_p[$i];        // Vul de variable met het recordnr $i van query2
        foreach ($ar_p as $value) {    // doorloop de velden uit recordnr $i van query2

            $temp_ar[] = $value;        // vul de array '$temp_ar' met de velden uit query2
        }
    }
    else {                            // Als $i niet kleiner is dan het aantal records uit query2

        for($l=0;$l<4;$l++){        // doorloop een loop 4x

            $temp_ar[] = '';            // vul de array '$temp_ar' met lege waarden.
        }
    }

    $new_ar[] = array($temp_ar[0],$temp_ar[1],$temp_ar[2],$temp_ar[3],$temp_ar[4],$temp_ar[5]);
    unset($temp_ar);
}
//echo var_dump($new_ar).'<br>';
// *** EINDE Opbouwen van een array met die links en rechts op het rapport reps. reden uitval en reden medicijnen opbouwt ***


//A4 width : 219
//default margin : 10mm each side
//writable horizontal : 219-(10*2)=189mm

$pdf = new CombiredenPdf($Afdrukstand,'mm','A4'); //use new class

//define new alias for total page numbers
$pdf->AliasNbPages('{pages}');

$pdf->AddPage();

$pdf->SetFont('Times','',9);
$pdf->SetDrawColor(200,200,200); // Grijs


$border_d = 'T'; 
$border_p = 'T'; 

foreach ($new_ar as $value) {
    
    $Scan_d = $value[0]; 
    $Reden_d = $value[1]; if($Reden_d == '') { $border_d = ''; } else { $border_d = 'T'; }
    $Scan_p = $value[2]; 
    $Artikel = $value[3]; if($Artikel == '') { $border_p = ''; } else { $border_p = 'T'; }
    $Stdat = $value[4];
    $Reden_p = $value[5];

    $pdf->Cell(10,5,'','',0,'C');
     $pdf->Cell(15,5,$Scan_d,$border_d,0,'C'); 
      $pdf->Cell(25,5,$Reden_d,$border_d,0,'');
    $pdf->Cell(35,5,'','',0,'C');             // Ruimte tussen twee blokken
     $pdf->Cell(15,5,$Scan_p,$border_p,0,'C'); 
      $pdf->Cell(35,5,$Artikel,$border_p,0,'');
      $pdf->Cell(15,5,$Stdat,$border_p,0,'C');
      $pdf->Cell(25,5,$Reden_p,$border_p,1,'');
}

$pdf->Output($rapport.".pdf","D");
