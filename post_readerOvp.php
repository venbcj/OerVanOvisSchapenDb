<?php

/*
<!-- 3-9-2016 : sql beveiligd
20-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel en hidden velden in insOverplaats.php verwijderd en codering hier aangepast         22-1-2017 : tblBezetting gewijzigd naar tblBezet 
28-6-2017 : insert tblPeriode verwijderd Priode wordt sinds 12-2-2017 niet meer opgeslagen in tblBezet.
11-6-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol 
13-7-2020 : impVerplaatsing gewijzigd in impAgrident 
27-2-2021 : Opslaan transponder bij schaap als deze niet bestaat 
8-5-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen. Verschil tussen kiezen of verwijderen herschreven. SQL beveiligd met quotes 
11-03-2024 : Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 -->
 */

$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {
    if (!$recId) continue;

// Id ophalen
//echo '$recId = '.$recId.'<br>'; 
// Einde Id ophalen

  foreach($id as $key => $value) {

      if ($key == 'chbkies')   { $fldKies = $value; }
      if ($key == 'chbDel')   { $fldDel = $value; }

    if ($key == 'txtOvpldag' ) { $dag = date_create($value); $valuedatum =  date_format($dag, 'Y-m-d'); 
                                    /*echo $key.'='.$valuedatum.' ';*/ $fldDag = $valuedatum; }

    if ($key == 'kzlHok' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldHok = $value; }

                                    }


// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);
if($reader == 'Agrident') {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen


if($reader == 'Agrident') {
$zoek_transponder = mysqli_query($db, "
SELECT transponder tran, levensnummer levnr
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

    while( $zt = mysqli_fetch_assoc($zoek_transponder)) { 
      $tran_rd  = $zt['tran']; 
      $fldLevnr = $zt['levnr']; }


$zoek_transponder_schaap = mysqli_query($db, "
SELECT schaapId, transponder tran
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."'
") or die (mysqli_error($db)); 

    while( $zts = mysqli_fetch_assoc($zoek_transponder_schaap)) { 
        $schaapId = $zt['schaapId'];
        $tran_db = $zt['tran']; }

if(isset($tran_rd) && !isset($tran_db)) {
  $update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$tran_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";

  /*echo $update_tblSchaap.'<br>';*/  mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}

}




// CONTROLE op alle verplichten velden bij overplaatsen lam
if ( !empty($fldDag) && isset($fldHok))
{

if($reader == 'Agrident') {
$zoek_levensnummer_doelgroep = mysqli_query($db,"
SELECT rd.levensnummer levnr, p.doelId
FROM impAgrident rd
 left join (
    SELECT s.levensnummer, p.doelId
    FROM tblBezet b
     left join (
        SELECT h1.hisId hisv, min(h2.hisId) hist
        FROM tblHistorie h1
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
        WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY h1.hisId
     ) tot on (b.hisId = tot.hisv)
     join tblPeriode p on (p.periId = b.periId)
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(tot.hist) and h.skip = 0
 ) p on (rd.levensnummer = p.levensnummer)
WHERE rd.Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
else {
$zoek_levensnummer_doelgroep = mysqli_query($db,"
SELECT rd.levnr_ovpl levnr, p.doelId
FROM impReader rd
 left join (
    SELECT s.levensnummer, p.doelId
    FROM tblBezet b
     left join (
        SELECT h1.hisId hisv, min(h2.hisId) hist
        FROM tblHistorie h1
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
         join tblStal st on (h1.stalId = st.stalId)
         join tblUbn u on (u.ubnId = st.ubnId)
        WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
        GROUP BY h1.hisId
     ) tot on (b.hisId = tot.hisv)
     join tblPeriode p on (p.periId = b.periId)
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
     join tblSchaap s on (s.schaapId = st.schaapId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(tot.hist) and h.skip = 0
 ) p on (rd.levnr_ovpl = p.levensnummer)
WHERE rd.readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}
    while ($dl = mysqli_fetch_assoc($zoek_levensnummer_doelgroep)) { $levnr = $dl['levnr']; /*$doelId = $dl['doelId'];*/ }
//echo '$levnr = '.$levnr.'<br>';

$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal st
 join tblUbn u on (u.ubnId = st.ubnId)
 join tblSchaap s on (st.schaapId = s.schaapId)
WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer = '".mysqli_real_escape_string($db,$levnr)."' and isnull(st.rel_best)
") or die (mysqli_error($db));
    while ($st = mysqli_fetch_assoc($zoek_stalId)) { $stalId = $st['stalId']; }
//echo '$stalId = '.$stalId.'<br>';

/*$zoek_doelgr = /* Nodig als nieuw hok nog leeg is */ /*mysqli_query($db," 
SELECT p.doelId
FROM tblPeriode p 
 join tblBezet b on (b.periId = p.periId)
 join (
    SELECT max(bezId) bezId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
    WHERE st.stalId = '".mysqli_real_escape_string($db,$stalId)."'
 ) mb on (mb.bezId = b.bezId)
") or die (mysqli_error($db));
    while ($dl = mysqli_fetch_assoc($zoek_doelgr)) { $doelId = $dl['doelId']; }*/

    
    $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', actId = 5 ";
        mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie h 
 join tblStal st on (h.stalId = st.stalId)
WHERE st.stalId = '".mysqli_real_escape_string($db,$stalId)."' and actId = 5
") or die (mysqli_error($db));
    while ($hi = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $hi['hisId']; }


        
    $insert_tblBezet = "INSERT INTO tblBezet set hisId = '".mysqli_real_escape_string($db,$hisId)."', hokId = '".mysqli_real_escape_string($db,$fldHok)."' ";
/*echo $insert_tblBezet.'<br>';*/        mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));
unset($periId);

if($reader == 'Agrident') {        
    $updateReader = "UPDATE impAgrident SET verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' ";
}
else {    
    $updateReader = "UPDATE impReader SET verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' ";
}
        mysqli_query($db,$updateReader) or die (mysqli_error($db));    
}
// EINDE CONTROLE op alle verplichten velden bij spenen lam
        
} // Einde if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt))    

if ($fldKies == 0 && $fldDel == 1) {

if($reader == 'Agrident') {
       $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
}
else {
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
}
/*echo $updateReader.'<br>';*/    mysqli_query($db,$updateReader) or die (mysqli_error($db));

    }


    
    }
?>
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
                    
    
