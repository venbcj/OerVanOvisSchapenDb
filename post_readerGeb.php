<!-- 11-8-2014 : veld type gewijzigd in fase
 16-11-2014 include "maak_request.php"; toegevoegd 
 17-09-2016 : modules gesplitst 
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel  22-1-2017 : tblBezetting gewijzigd naar tblBezet 
 18-2-2017 : Controle op startdatum moeder toegevoegd 
 28-2-2017 : Ras en gewicht niet veplicht gemaakt
 28-4-2017 : $dmafv_mdr bij elke regel leeg gemaakt. Dit veroorzaakte het afbreken van 30 regels bij ± 7 regels. 
 28-2-2018 : Opslaan dood geboren toegevoegd 
 24-6-2018 : uitvaldatum verwijderd 
 16-3-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
  9-5-2020 : Worpverloop toegevoegd aan tblVolwas
 30-5-2020 : Veld moment opslaan ook bij reader Agrident t.b.v. taak Dood geboren
 13-7-2020 : impGeboortes vervangen door impAgrident 18-7 juiste uitvaldatum vastgelegd nl. txtUitvaldm anders txtDatum
 23-1-2021 : transponder toegevoegd. Sql beveiligd met quotes. Verschil tussen kiezen of verwijderen herschreven 30-1 $mdrId gewijzigd naar $moederId
 7-2-2021 : isset($verwerkt) toegevoegd om dubbele invoer te voorkomen
 10-1-2022: Code aangepast n.a.v. registratie dekkingen en dracht
-->
<?php
/* post_readerGeb.php toegepast in :
  - InsGeboortes.php */
  
function getNameFromKey($key) {
    $array = explode('_', $key);
    return $array[0];
}

function getIdFromKey($key) {
    $array = explode('_', $key);
    return $array[1];
}

$array = array();

foreach($_POST as $key => $value) {
    
    $array[getIdFromKey($key)][getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

// Id ophalen
//echo '<br>'.'$recId = '.$recId.'<br>'; #/#
// Einde Id ophalen
    
  unset($fldRas);
  unset($fldSekse);
  unset($fldKg);
  unset($fldMom);
  unset($fldUitvdag);
  unset($fldRed);
  
  foreach($id as $key => $value) { // Alle velden vullen in variabelen  

  if ($key == 'chbkies')   { $fldKies = $value; }
  if ($key == 'chbDel')   { $fldDel = $value; }

  if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $fldDag = date_format($dag, 'Y-m-d');  
                  /*echo $key.'='.$valuedag.' ';*/  }
  
  
  if ($key == 'kzlRas' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRas = $value; }
  // else if ($key == 'kzlRas' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRas = '' ; }

  
  if ($key == 'kzlSekse' && !empty($value)) { /*echo $key.'='.$value.'<br>';*/ $fldSekse = $value; }
  // else if ($key == 'kzlSekse' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldSekse = '' ; }


   if ($key == 'txtKg' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = str_replace(',', '.', $value); }
   //else if ($key == 'txtKg' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = ''; }
   

  if ($key == 'kzlOoi' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldMoeder = $value; 
    $query_datum_aanvoer_moeder = mysqli_query($db,"
    SELECT h.datum
    FROM (
      SELECT max(stalId) stalId
      FROM tblStal
      WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$fldMoeder)."'
     ) mst
     join tblHistorie h on (h.stalId = mst.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE a.op = 1 and h.skip = 0
    and not exists (
      SELECT datum 
      FROM tblHistorie ha 
       join tblStal st on (ha.stalId = st.stalId)
       join tblSchaap s on (st.schaapId = s.schaapId)
      WHERE actId = 2 and mst.stalId = st.stalId and h.actId = ha.actId-1 and s.schaapId = '" .mysqli_real_escape_string($db,$fldMoeder)/* bij aankoop incl. geboortedatum wordt geboortedatum niet getoond */. "')
    ") or die (mysqli_error($db)); 
    while($mdrdm1 = mysqli_fetch_array($query_datum_aanvoer_moeder))
    { $dmaanv_mdr = $mdrdm1['datum']; }

  unset($dmafv_mdr);

    $query_datum_afvoer_moeder = mysqli_query($db,"
    SELECT h.datum dmeind
    FROM (
      SELECT max(stalId) stalId
      FROM tblStal
      WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$fldMoeder)."'
    ) mst
     join tblStal st on (mst.stalId = st.stalId)
     join tblHistorie h on (h.stalId = st.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE a.af = 1 and h.skip = 0
    ") or die (mysqli_error($db)); 
    while($mdrdm = mysqli_fetch_array($query_datum_afvoer_moeder))
    { $dmafv_mdr = $mdrdm['dmeind']; }
  }

  if ($key == 'kzlHok' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldHok = $value; }

  
  if ($key == 'kzlMom' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldMom = $value; }
  else if ($key == 'kzlMom' && empty($value)) {  $fldMom = '' ; }

  if ($key == 'txtUitvaldm' && !empty($value)) { $uitvdag = date_create($value); $fldUitvdag = date_format($uitvdag, 'Y-m-d'); }

  if ($key == 'kzlRed' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRed = $value; }


                            } // Einde Alle velden vullen in variabelen


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
$zoek_levensnummer_transponder = mysqli_query($db, "
SELECT transponder tran, levensnummer lam, moeder, moedertransponder mdr_tran
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 
}
else {
$zoek_levensnummer_transponder = mysqli_query($db, "
SELECT NULL tran, levnr_geb lam, NULL moeder, NULL mdr_tran
FROM impReader
WHERE readId = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db));
}

    while( $lv = mysqli_fetch_assoc($zoek_levensnummer_transponder)) { 
      $tran     = $lv['tran']; 
      $fldLevnr = $lv['lam'];
      $moeder   = $lv['moeder']; 
      $mdrTran_rd = $lv['mdr_tran']; }

#echo '$fldKies = '.$fldKies. 'en $fldDel = '.$fldDel.'<br>';

// Transponder moeder inlezen als deze niet bestaat in tblSchaap
$zoek_transp_moeder = mysqli_query($db, "
SELECT schaapId, transponder
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$moeder)."'
") or die (mysqli_error($db));

    while( $ztm = mysqli_fetch_assoc($zoek_transp_moeder)) { $moederId = $ztm['schaapId']; $mdrTran_sch = $ztm['transponder']; }

if($mdrTran_rd <> $mdrTran_sch) {
  $update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$mdrTran_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$moederId)."' ";

  /*echo $update_tblSchaap.'<br>';*/  mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}
// Einde Transponder moeder inlezen als deze niet bestaat in tblSchaap


// ********************
//    BEPAAL VOLWID
// ********************
if(isset($fldMoeder)) {

unset($volwId);

// Zoek een huidige worp
 $zoek_huidige_worp = mysqli_query($db,"
   SELECT l.volwId
   FROM tblSchaap l
    join tblVolwas v on (l.volwId = v.volwId)
    join tblStal st on (l.schaapId = st.schaapId)
    join tblHistorie h on (h.stalId = st.stalId) 
   WHERE v.mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."' and h.actId = 1 and h.datum = '".mysqli_real_escape_string($db,$fldDag)."'
 ") or die (mysqli_error($db));
  while ( $zhw = mysqli_fetch_assoc($zoek_huidige_worp)) { $volwId = $zhw['volwId']; }

//if(isset($volwId)) { echo 'actuele worp = '.$volwId. '. Werpdatum = '.$fldDag.'<br>'; } #/#
if(!isset($volwId)) {
// Zoek vorige worp
unset($lst_volwId);

 $zoek_vorige_worp = mysqli_query($db,"
   SELECT max(l.volwId) volwId
   FROM tblSchaap l
    join tblVolwas v on (l.volwId = v.volwId)
    join tblStal st on (l.schaapId = st.schaapId)
    join tblHistorie h on (h.stalId = st.stalId)
    left join tblSchaap k on (k.volwId = v.volwId)
     left join (
        SELECT s.schaapId
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3
     ) ha on (k.schaapId = ha.schaapId) 
   WHERE v.mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."' and h.actId = 1 and h.datum < '".mysqli_real_escape_string($db,$fldDag)."' and isnull(ha.schaapId)
 ") or die (mysqli_error($db));
  while ( $zvw = mysqli_fetch_assoc($zoek_vorige_worp)) { $lst_volwId = $zvw['volwId']; }

// Zoek een bestaande dracht
 $zoek_actuele_dracht = mysqli_query($db,"
 SELECT v.volwId
 FROM tblVolwas v
  join tblDracht d on (v.volwId = d.volwId)
  join tblHistorie h on (d.hisId = h.hisId)
 WHERE h.skip = 0 and v.mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."' and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
 ") or die (mysqli_error($db));
  while ( $zadr = mysqli_fetch_assoc($zoek_actuele_dracht)) { $volwId = $zadr['volwId']; }

//if(isset($volwId)) { echo 'actuele dracht = '.$volwId.'<br>'; } #/#
}


if(!isset($volwId)) {
// Zoek een bestaande dekking
 $zoek_actuele_dekking = mysqli_query($db,"
 SELECT max(v.volwId) volwId
 FROM tblVolwas v
  join tblHistorie h on (v.hisId = h.hisId)
 WHERE h.skip = 0 and v.mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."' and v.volwId > '".mysqli_real_escape_string($db,$lst_volwId)."'
 ") or die (mysqli_error($db));
  while ( $zade = mysqli_fetch_assoc($zoek_actuele_dekking)) { $volwId = $zade['volwId']; }

//if(isset($volwId)) { echo 'actuele dekking = '.$volwId.'<br>'; } #/#
  }


if(!isset($volwId)) {

  // Koppel maken
 $insert_tblVolwas = "INSERT INTO tblVolwas set mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."', drachtig = 1 ";

/*echo $insert_tblVolwas.'<br>';  ##*/mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));
  // Einde Koppel maken

 $zoek_volwId = mysqli_query($db,"
 SELECT max(volwId) volwId
 FROM tblVolwas
 WHERE mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."'
 ") or die (mysqli_error($db));
  while ( $vw = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $vw['volwId']; }
} // Einde if(!isset($volwId))
// Einde Zoek een bestaande worp
else
{ // Drachtig Ja vastleggen

$zoek_drachtig = mysqli_query($db,"
 SELECT v.drachtig
 FROM tblVolwas v
 WHERE v.volwId = '".mysqli_real_escape_string($db,$volwId)."'
 ") or die (mysqli_error($db));
  while ( $zdr = mysqli_fetch_assoc($zoek_drachtig)) { $drachtig_db = $zdr['drachtig']; }

if(!isset($drachtig_db) || $drachtig_db == 0) {

$updateDracht = "UPDATE tblVolwas set drachtig = 1 WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' " ; 

/*echo "$updateDracht".'<br>'.'<br>';         ##*/mysqli_query($db,$updateDracht) or die (mysqli_error($db));
}


} // Einde Drachtig Ja vastleggen

// Worpverloop vastleggen
if($reader == 'Agrident') {

$zoek_worpverloop_reader = mysqli_query($db,"
 SELECT verloop
 FROM impAgrident
 WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
 ") or die (mysqli_error($db));
  while ( $zvr = mysqli_fetch_assoc($zoek_worpverloop_reader)) { $verloop_rd = $zvr['verloop']; }

$zoek_worpverloop_db = mysqli_query($db,"
 SELECT verloop
 FROM tblVolwas
 WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."'
 ") or die (mysqli_error($db));
  while ( $zvb = mysqli_fetch_assoc($zoek_worpverloop_db)) { $verloop_db = $zvb['verloop']; }

if(!isset($verloop_db) && isset($verloop_rd)) {

$updateDracht = "UPDATE tblVolwas set verloop = '".mysqli_real_escape_string($db,$verloop_rd)."' WHERE volwId = '".mysqli_real_escape_string($db,$volwId)."' " ; 

/*echo "$updateDracht".'<br>'.'<br>';      ##*/mysqli_query($db,$updateDracht) or die (mysqli_error($db));
}

}
// Einde Worpverloop vastleggen

} // Einde if($modtech == 1 && isset($fldMoeder))
// ********************
// EINDE BEPAAL VOLWID
// ********************


// ***************************
//    GEGEVENS INLEZEN
// ***************************

unset($rel_best);
//if(!isset($dmafv_mdr)) { $dmafv_mdr = $fldDag; }
if (
 (isset($fldDag) && isset($fldLevnr) && isset($fldMoeder) && $fldDag >= $dmaanv_mdr && (!isset($dmafv_mdr) || $fldDag <= $dmafv_mdr) && isset($fldHok)) // Veplichte velden bij module Technisch. $fldMoeder is verplicht bij module technisch
|| ($modtech == 0 && isset($fldDag) && isset($fldLevnr) ) // Veplichte velden zonder module Technisch
) { $scenario = 'Geboren_lam'; }

else if(!isset($fldLevnr) && isset($fldDag) && ((isset($fldMoeder) && $modtech == 1) || ($modtech == 0)) ) 
  { $scenario = 'Dood_geboren'; 
    $rel_best = $rendac_Id;
    $fldLevnr = $ubn;
  }

if(isset($scenario)) {
// SCHAAP invoeren

 $insert_tblSchaap = "
 INSERT INTO tblSchaap set levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."', rasId = " . db_null_input($fldRas) . ", geslacht = " . db_null_input($fldSekse) . ", volwId = " . db_null_input($volwId). ", momId = " . db_null_input($fldMom) . ", redId = " . db_null_input($fldRed) . ", transponder = " . db_null_input($tran); 

/*echo $insert_tblSchaap.'<br>';   ##*/mysqli_query($db,$insert_tblSchaap) or die (mysqli_error($db)); 
// Einde SCHAAP invoeren

$zoek_schaapId = mysqli_query($db,"
SELECT schaapId
FROM tblSchaap
WHERE levensnummer = '".mysqli_real_escape_string($db,$fldLevnr)."'
") or die (mysqli_error($db));
    while ( $zs = mysqli_fetch_assoc ($zoek_schaapId)) { $schaapId = $zs['schaapId']; }

if(isset($schaapId) && isset($rel_best)) {

// ubn uit veld levensnummer verwijderen
$update_tblSchaap = "UPDATE tblSchaap set levensnummer = NULL WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."' ";

/*echo $update_tblSchaap.'<br>';  ##*/mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
unset($levnr);
}

// Insert tblStal
$insert_tblStal = "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."', rel_best = " . db_null_input($rel_best) ;
/*echo "$insert_tblStal".'<br>';   ##*/mysqli_query($db,$insert_tblStal) or die (mysqli_error($db));
// Einde Insert tblStal

// Insert tblHistorie
$zoek_stalId = mysqli_query($db,"
SELECT stalId
FROM tblStal
WHERE schaapId = '".mysqli_real_escape_string($db,$schaapId)."'
") or die (mysqli_error($db));
    while ( $zst = mysqli_fetch_assoc ($zoek_stalId)) { $stalId = $zst['stalId']; }
      

  $insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$fldDag)."', kg = " . db_null_input($fldKg) . ", actId = 1 "; 

/*echo "$insert_tblHistorie".'<br>';   ##*/mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));

$zoek_hisId = mysqli_query($db,"
SELECT hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$stalId)."'
") or die (mysqli_error($db));
    while ( $zh = mysqli_fetch_assoc ($zoek_hisId)) { $hisId = $zh['hisId']; } // t.b.v. tblBezet en/of tblMelding

if(isset($rel_best)) { // Bij doodgeboren
  if(isset($fldUitvdag)) { $doodday = $fldUitvdag; } else { $doodday = $fldDag; }

$insert_tblHistorie_14 = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$stalId)."', datum = '".mysqli_real_escape_string($db,$doodday)."', actId = 14 ";

/*echo $insert_tblHistorie_14.'<br>';    ##*/mysqli_query($db,$insert_tblHistorie_14) or die (mysqli_error($db));
}
// Einde Insert tblHistorie

if($modtech == 1 && !isset($rel_best)) {
// Insert tblBezet 
  $insert_tblBezet = "INSERT INTO tblBezet set hisId = ".mysqli_real_escape_string($db,$hisId).", hokId = ".mysqli_real_escape_string($db,$fldHok)." " ;
/*echo "$insert_tblBezet".'<br>'; ##*/mysqli_query($db,$insert_tblBezet) or die (mysqli_error($db));  
// Einde Insert tblBezet  
}

if ($modmeld == 1 && !isset($rel_best)) { // Insert tblMeldingen 
$Melding = 'GER'; //geboren
##include "maak_request.php";
// Einde Insert tblMeldingen
}
/* Bijwerken tabel impReader*/    

if($reader == 'Agrident') { 
$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
}
else { 
$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
}

/*echo $updateReader.'<br>'.'<br>';  ##*/mysqli_query($db,$updateReader) or die (mysqli_error($db)); 

} // Einde if(isset($scenario))

// ***************************
//   EINDE GEGEVENS INLEZEN
// ***************************

} // EINDE if ($fldKies == 1 && $fldDel == 0)


  if($fldKies == 0 && $fldDel == 1) {
  if($reader == 'Agrident')  {
    $updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
  }
  else { 
    $updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ; 
  }
 /* echo $updateReader.'<br>';*/  mysqli_query($db,$updateReader) or die (mysqli_error($db));
 
}
                    


}


?>
          
  