<!--  25-6-2021 : Gekopieerd van post_readerMed.php 
5-9-2021 : Functie inlezen_voer toegevoegd 
24-6-2023 : N.a.v. samenvoegen registraties hier weer uitgeplitst per registratie -->

<?php




foreach($_POST as $fldname => $fldvalue) {
    
    $array[Url::getIdFromKey($fldname)][Url::getNameFromKey($fldname)] = $fldvalue; // Opbouwen van een Multidimensional array met 2 indexen. [Id] [naamveld] en een waarde nl. de veldwaarde. 
}


foreach($array as $recId => $id) {
    if (!$recId) continue;

    #echo '<br>'.'$recId = '.$recId.'<br>';

  foreach($id as $key => $value) {

      if ($key == 'chbkies')     { $fldKies = $value; }
      if ($key == 'chbDel')     { $fldDel = $value; }

    if ($key == 'kzlHok' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldHok = $value; }

    if ($key == 'kzlVoer' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldArtId = $value; }

    if ($key == 'txtAfslDatum' && !empty($value)) { $dag = date_create($value); $fldDmAfsluit =  date_format($dag, 'Y-m-d'); 
                                    /*echo $key.'='.$fldDmAfsluit.' ';*/  }

      
                                    }

/*echo '<br>';
echo 'Het gekozen verblijf = ' . $fldHok . '<br>';
echo 'Het gekozen artikel = ' . $fldArtId . '<br>';
echo 'Het gekozen afsluitdatum = ' . $fldDmAfsluit . '<br>';*/

/******* SPLITSEN VOERREGISTRATIE *******  
1 regel kan een samenvoeging zijn van meerdere voerregistraties. Elke voerregistratie wordt afzonderlijk verwerkt in tblVoeding. Daar wordt verwezen naar bijbehorende readerId */

$zoek_hok_voer_doelId = mysqli_query($db,"
SELECT hokId, artId, doelId
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

while($zhvd = mysqli_fetch_array($zoek_hok_voer_doelId))
{ $hokId_rd = $zhvd['hokId'];
  $artId_rd = $zhvd['artId'];
  $doelId_rd = $zhvd['doelId'];
} 


$zoek_gegevens_uit_reader = mysqli_query($db,"
SELECT Id, coalesce(toedat_upd,toedat) toedat, datum
FROM impAgrident
WHERE actId = 8888 and isnull(verwerkt) and lidId = '".mysqli_real_escape_string($db,$lidId)."' and hokId = '".mysqli_real_escape_string($db,$hokId_rd)."' and artId = '".mysqli_real_escape_string($db,$artId_rd)."' and doelId = '".mysqli_real_escape_string($db,$doelId_rd)."'
") or die (mysqli_error($db)); 

while($zgr = mysqli_fetch_array($zoek_gegevens_uit_reader))
{ $readId = $zgr['Id'];
  $datum_rd = $zgr['datum'];
  $toedat_rd = $zgr['toedat'];


// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);

$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$readId)."'
") or die (mysqli_error($db)); 

while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen

/*$zoek_gegevens_uit_reader = mysqli_query($db,"
SELECT hokId, artId, doelId, coalesce(toedat_upd,toedat) toedat, datum
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));

    while( $zd = mysqli_fetch_assoc($zoek_gegevens_uit_reader)) {
        $doelId_rd = $zd['doelId'];
        $datum_rd = $zd['datum']; 
        $toedat_rd = $zd['toedat']; 
    }*/

$zoek_gegevens_gekozen_artikel = mysqli_query($db,"
SELECT a.artId, a.naam, a.stdat
FROM tblArtikel a
WHERE a.artId = '".mysqli_real_escape_string($db,$fldArtId)."'
") or die (mysqli_error($db));
    while( $zga = mysqli_fetch_assoc($zoek_gegevens_gekozen_artikel)) { $naam = $zga['naam']; $stdat = $zga['stdat']; }

// Controle voldoende voervoorraad per verblijf, artikel en doelgroep
$zoek_totaalaantal_kg_van_oorspronkelijk_verblijf_artikel_en_doelgroep = mysqli_query($db,"
SELECT sum(coalesce(toedat_upd, toedat)) toedtot
FROM impAgrident
WHERE hokId = '".mysqli_real_escape_string($db,$hokId_rd)."' and artId = '".mysqli_real_escape_string($db,$artId_rd)."' and doelId = '".mysqli_real_escape_string($db,$doelId_rd)."' and actId = 8888 and isnull(verwerkt)
") or die (mysqli_error($db));
    while( $ztkg = mysqli_fetch_assoc($zoek_totaalaantal_kg_van_oorspronkelijk_verblijf_artikel_en_doelgroep)) 
        { $toedtot = $ztkg['toedtot']; } // Uitgangspunt : $toedtot is reeds vermenigvuldigd met standaard aantal !!

$zoek_voorraad_voer = mysqli_query ($db,"
SELECT sum(i.inkat) - sum(coalesce(n.nutat,0)) vrdat, a.stdat
FROM tblArtikel a
 join tblInkoop i on (a.artId = i.artId)
 left join (
    SELECT inkId, sum(nutat*stdat) nutat
    FROM tblVoeding 
    GROUP BY inkId
 ) n on (i.inkId = n.inkId)
WHERE i.artId = '".mysqli_real_escape_string($db,$fldArtId)."'
GROUP BY a.stdat
") or die (mysqli_error($db));
  while ($zv = mysqli_fetch_assoc($zoek_voorraad_voer)) 

      { $voer_vrd = $zv['vrdat']; }

/*echo 'Datum_gevoerd = '.$datum_rd.'<br>';
echo 'Kilogram_voer = '.$toedat_rd.'<br>';
echo 'Voer_voorraad = '.$voer_vrd.'<br>';*/

// Einde Controle voldoende voervoorraad per verblijf, artikel en doelgroep

// CONTROLE op alle verplichten velden bij voerregistratie
if (isset($fldDmAfsluit) && isset($fldHok) && isset($fldArtId) && $toedtot <= $voer_vrd)
{
if(!isset($periId) || ($hok_loop <> $fldHok || $art_loop <> $fldArtId || $doel_loop <> $doelId_rd) ) {
$hok_loop = $fldHok;
$art_loop = $fldArtId;
$doel_loop = $doelId_rd;

unset($periId);
// ASLUITPERIODE BEPALEN
// Zoek naar eerdere bestaande afsluitperiode
$zoek_periode = mysqli_query($db,"
SELECT periId
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$fldHok)."' and doelId = '".mysqli_real_escape_string($db,$doelId_rd)."' and dmafsluit = '".mysqli_real_escape_string($db,$fldDmAfsluit)."'
") or die (mysqli_error($db));

    while( $zp = mysqli_fetch_assoc($zoek_periode)) { $periId = $zp['periId']; }

if(isset($periId)) { $fout = "Deze afsluitdatum bestaat al."; }

else if(!isset($periId)) {

$insert_tblPeriode = "INSERT INTO tblPeriode set hokId = '".mysqli_real_escape_string($db,$fldHok)."', doelId= '".mysqli_real_escape_string($db,$doelId_rd)."', dmafsluit = '".mysqli_real_escape_string($db,$fldDmAfsluit)."' ";
/*echo $insert_tblPeriode.'<br>';*/        mysqli_query($db,$insert_tblPeriode) or die (mysqli_error($db));

$zoek_periId = mysqli_query ($db,"
SELECT periId
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$fldHok)."' and doelId= '".mysqli_real_escape_string($db,$doelId_rd)."' and dmafsluit = '".mysqli_real_escape_string($db,$fldDmAfsluit)."'
") or die (mysqli_error($db));
    while ($pi = mysqli_fetch_assoc($zoek_periId)) { $periId = $pi['periId']; }
}
// EINDE ASLUITPERIODE BEPALEN

}

//echo 'De periode = ' . $periId . '<br>';

/* INVOEREN */ 

$inleeshoeveelheid = $toedat_rd/$stdat; 

/*echo 'noodzakelijke gegevens : artikel ' . $fldArtId . ', $inleeshoeveelheid = ' .$inleeshoeveelheid. ', $datum_rd = '. $datum_rd.', $periId = '. $periId .', $recId = '. $recId.'<br>';*/

if($datum_rd <= $fldDmAfsluit) {

inlezen_voer($db, $fldArtId, $inleeshoeveelheid, $datum_rd, $periId, $readId); // Zit in func_artikelnuttigen.php zie insVoerregistratie.php

 /* EINDE INVOEREN */

    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$readId)."' " ;

    /*echo $updateReader.'<br>';*/    mysqli_query($db,$updateReader) or die (mysqli_error($db));

} // Einde if($datum_rd <= $fldDmAfsluit)

 } // Einde if (isset($fldDmAfsluit) && isset($fldHok) && isset($fldArtId) && $toedtot <= $voer_vrd)


} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))



if ($fldKies == 0 && $fldDel == 1) {

    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$readId)."' " ;

        /*echo $updateReader.'<br>';*/        mysqli_query($db,$updateReader) or die (mysqli_error($db));
}


} //Einde while($zri = mysqli_fetch_array($zoek_gegevens_uit_reader))
// ******* Einde SPLITSEN VOERREGISTRATIE ******* 

//#echo '<br>'.'einde '.$recId.'<br>';

    } // Einde foreach($array as $recId => $id)

?>
                    
    
