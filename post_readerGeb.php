<!-- 11-8-2014 : veld type gewijzigd in fase
 16-11-2014 include maak_request toegevoegd 
 17-09-2016 : modules gesplitst 
 18-1-2017 : Query's aangepast n.a.v. nieuwe tblDoel  22-1-2017 : tblBezetting gewijzigd naar tblBezet 
 18-2-2017 : Controle op startdatum moeder toegevoegd 
 28-2-2017 : Ras en gewicht niet veplicht gemaakt
 28-4-2017 : dmafv_mdr bij elke regel leeg gemaakt. Dit veroorzaakte het afbreken van 30 regels bij ± 7 regels. 
 28-2-2018 : Opslaan dood geboren toegevoegd 
 24-6-2018 : uitvaldatum verwijderd 
 16-3-2020 : onderscheid gemaakt tussen reader Agrident en Biocontrol
  9-5-2020 : Worpverloop toegevoegd aan tblVolwas
 30-5-2020 : Veld moment opslaan ook bij reader Agrident t.b.v. taak Dood geboren
 13-7-2020 : impGeboortes vervangen door impAgrident 18-7 juiste uitvaldatum vastgelegd nl. txtUitvaldm anders txtDatum
 23-1-2021 : transponder toegevoegd. Sql beveiligd met quotes. Verschil tussen kiezen of verwijderen herschreven 30-1 mdrId gewijzigd naar moederId
 07-02-2021 : isset(verwerkt) toegevoegd om dubbele invoer te voorkomen
 10-01-2022 : Code aangepast n.a.v. registratie dekkingen en dracht
 20-05-2023 : De datum van dekken (datum in tblVolwas) wordt vanaf nu vastgelegd in tblHistorie met actId 18
 11-07-2025 : Opslaan van ubn in tblStal toegevoegd. 29-8-2025 : Bij gebruikers zonder module technisch wordt ubn niet o.b.v. moederdier bepaald.
-->
<?php
/* post_readerGeb.php toegepast in :
  - InsGeboortes.php */
  


$array = array();

foreach($_POST as $key => $value) {
    
    $array[Url::getIdFromKey($key)][Url::getNameFromKey($key)] = $value;
}
foreach($array as $recId => $id) {

// Id ophalen
//echo '<br>'.'$recId = '.$recId.'<br>'; #/#
// Einde Id ophalen
    
  unset($fldRas);
  unset($fldSekse);
  unset($fldKg);
  unset($fldStalIdMdr);
  unset($fldMom);
  unset($fldUitvdag);
  unset($fldRed);
  
  foreach($id as $key => $value) { // Alle velden vullen in variabelen  

  if ($key == 'chbkies')   { $fldKies = $value; }
  if ($key == 'chbDel')   { $fldDel = $value; }

  if ($key == 'txtDatum' && !empty($value)) { $dag = date_create($value); $fldDag = date_format($dag, 'Y-m-d');  
                  /*echo $key.'='.$valuedag.' ';*/  
       $Dagberekening = strtotime($fldDag);
       $fldDrachtDay = date('Y-m-d', strtotime("-145 day", $Dagberekening));

                }
  
  
  if ($key == 'kzlRas' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRas = $value; }
  // else if ($key == 'kzlRas' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRas = '' ; }

  
  if ($key == 'kzlSekse' && !empty($value)) { /*echo $key.'='.$value.'<br>';*/ $fldSekse = $value; }
  // else if ($key == 'kzlSekse' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldSekse = '' ; }


   if ($key == 'txtKg' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = str_replace(',', '.', $value); }
   //else if ($key == 'txtKg' && empty($value)) { /*echo $key.'='.$value.' ';*/ $fldKg = ''; }
   

  if ($key == 'kzlOoi' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldStalIdMdr = $value; }

  if ($key == 'kzlHok' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldHok = $value; }

  
  if ($key == 'kzlMom' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldMom = $value; }
  else if ($key == 'kzlMom' && empty($value)) {  $fldMom = '' ; }

  if ($key == 'txtUitvaldm' && !empty($value)) { $uitvdag = date_create($value); $fldUitvdag = date_format($uitvdag, 'Y-m-d'); }

  if ($key == 'kzlRed' && !empty($value)) { /*echo $key.'='.$value.' ';*/ $fldRed = $value; }


                            } // Einde Alle velden vullen in variabelen

// (extra) controle of readerregel reeds is verwerkt. Voor als de pagina 2x wordt verstuurd bij fouten op de pagina
unset($verwerkt);

$zoek_readerRegel_verwerkt = mysqli_query($db,"
SELECT verwerkt
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

while($verw = mysqli_fetch_array($zoek_readerRegel_verwerkt))
{ $verwerkt = $verw['verwerkt']; }
// Einde (extra) controle of readerregel reeds is verwerkt.

if ($fldKies == 1 && $fldDel == 0 && !isset($verwerkt)) { // isset($verwerkt) is een extra controle om dubbele invoer te voorkomen


// Zoek gegevens behorende bij moederdier

if(isset($fldStalIdMdr)) {
// zoek ubn van moederdier bij gebruikers met module technisch
$zoek_schaapId_ubnId_moeder = mysqli_query($db,"
SELECT schaapId, ubnId
FROM tblStal
WHERE stalId = '".mysqli_real_escape_string($db,$fldStalIdMdr)."'
") or die (mysqli_error($db)); 
    
    while($zsum = mysqli_fetch_array($zoek_schaapId_ubnId_moeder))
    { $mdrId = $zsum['schaapId']; 
      $ubnId = $zsum['ubnId']; }
// Einde zoek ubn van moederdier bij gebruikers met module technisch

    $zoek_eerste_aanvoerdatum_moeder = mysqli_query($db,"
    SELECT h.datum
    FROM (
      SELECT min(stalId) stalId
      FROM tblStal
      WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$mdrId)."'
     ) st1
     join tblHistorie h on (h.stalId = st1.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE a.op = 1 and h.skip = 0
    and not exists (
      SELECT datum 
      FROM tblHistorie ha 
       join tblStal st on (ha.stalId = st.stalId)
       join tblSchaap s on (st.schaapId = s.schaapId)
      WHERE actId = 2 and st1.stalId = st.stalId and h.actId = ha.actId-1 and s.schaapId = '" .mysqli_real_escape_string($db,$mdrId)/* bij aankoop incl. geboortedatum wordt geboortedatum niet getoond */. "')
    ") or die (mysqli_error($db)); 
    while($zeam = mysqli_fetch_array($zoek_eerste_aanvoerdatum_moeder))
    { $dmaanv_1_mdr = $zeam['datum']; }

  unset($dmafv_mdr);

    $query_datum_afvoer_moeder = mysqli_query($db,"
    SELECT h.datum dmeind
    FROM (
      SELECT max(stalId) stalId
      FROM tblStal
      WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and schaapId = '".mysqli_real_escape_string($db,$mdrId)."'
    ) mst
     join tblStal st on (mst.stalId = st.stalId)
     join tblHistorie h on (h.stalId = st.stalId)
     join tblActie a on (a.actId = h.actId)
    WHERE a.af = 1 and h.skip = 0
    ") or die (mysqli_error($db)); 
    while($mdrdm = mysqli_fetch_array($query_datum_afvoer_moeder))
    { $dmafv_mdr = $mdrdm['dmeind']; }
}
// Einde if(isset($fldStalIdMdr)) Zoek gegevens behorende bij moederdier
else {
// zoek ubn van gebruiker bij gebruikers die module technisch niet hebben. Controle of gebruiker slechts 1 ubn heeft zit in InsGeboortes.php
$zoek_ubnId = mysqli_query($db,"
SELECT ubnId
FROM tblUbn
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db)); 
    
    while($zu = mysqli_fetch_array($zoek_ubnId))
    { $ubnId = $zu['ubnId']; }
// Eindezoek ubn van gebruiker bij gebruikers die module technisch niet hebben.
}



$zoek_levensnummer_transponder = mysqli_query($db, "
SELECT transponder tran, levensnummer lam, moeder, moedertransponder mdr_tran
FROM impAgrident
WHERE Id = '".mysqli_real_escape_string($db,$recId)."'
") or die (mysqli_error($db)); 

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

    while( $ztm = mysqli_fetch_assoc($zoek_transp_moeder)) { 
      $moederId = $ztm['schaapId'];
      $mdrTran_sch = $ztm['transponder']; 
    }

if($mdrTran_rd <> $mdrTran_sch) {
  $update_tblSchaap = "UPDATE tblSchaap set transponder = '".mysqli_real_escape_string($db,$mdrTran_rd)."' WHERE schaapId = '".mysqli_real_escape_string($db,$moederId)."' ";

  /*echo $update_tblSchaap.'<br>';*/  mysqli_query($db,$update_tblSchaap) or die (mysqli_error($db));
}
// Einde Transponder moeder inlezen als deze niet bestaat in tblSchaap


/* ********************
//    BEPAAL VOLWID
// ********************
Stap 1 : Eerst wordt o.b.v. moeder en geboortedatum van lam een actuele worp gezocht. Dus volwId in tblVolwas die al is gekoppeld aan een ander lam.
Stap 2 : Als deze niet bestaat wordt er gezocht naar een drachtige ooi zonder worp. Na de laatste worp van de ooi moet er nog een dracht zijn geregistreerd die niet ouder is dan 145 dagen.
Stap 3 : Als een drachtige ooi zonder worp niet bestaat wordt er gezocht op een actuele dekking die dus nog niet heeft geworpen. Na de laatste worp van de ooi moet er nog een dekking zijn geregistreerd die niet ouder is dan 145 dagen.
Stap 4 : Als deze dekking niet bestaat wordt een nieuw koppel (vader al dan niet bekend) aangemaakt in tblVolwas. Daarna wordt een fictieve dekdatum vastgelegd in tblHistorie met actId 18 (Dekken) zijnde geboortedatum - 145 dagen.
Een fictieve drachtdatum wordt niet vastgelegd. Deze moet reeds bestaan anders wordt deze niet met terugwerkende kracht aangemaakt.
*/

if(isset($fldStalIdMdr)) {

unset($volwId);

// Stap 1 : Zoek een huidige worp o.b.v. moeder en geboortedatum
 $zoek_huidige_worp = mysqli_query($db,"
   SELECT l.volwId
   FROM tblSchaap l
    join tblVolwas v on (l.volwId = v.volwId)
    join tblStal st on (l.schaapId = st.schaapId)
    join tblHistorie h on (h.stalId = st.stalId) 
   WHERE v.mdrId = '".mysqli_real_escape_string($db,$mdrId)."' and h.actId = 1 and h.datum = '".mysqli_real_escape_string($db,$fldDag)."'
 ") or die (mysqli_error($db));
  while ( $zhw = mysqli_fetch_assoc($zoek_huidige_worp)) { $volwId = $zhw['volwId']; }


// Stap 2 is overbodig. Als er een drachtige ooi bestaat moet deze hetzelfde volwId hebben als de volwId o.b.v. de laatste dekking zonder worp. Aan die volwId hangt immers de drachtregistratie.

/*if(!isset($volwId)) { // Stap 2 : Zoek drachtige ooi zonder worp binnen 145 dagen t.o.v. de geboortedatum $fldDag

$zoek_actuele_dracht = mysqli_query($db,"
 SELECT v.volwId
 FROM tblVolwas v
  join tblDracht d on (v.volwId = d.volwId)
  join tblHistorie h on (d.hisId = h.hisId)
  left join tblSchaap s on (s.volwId v.volwId)
 WHERE h.skip = 0 and v.mdrId = '".mysqli_real_escape_string($db,$fldMoeder)."' and isnull(s.volwId) and date_add(h.datum, interval 145 day) > '".mysqli_real_escape_string($db,$fldDag)."'
 ") or die (mysqli_error($db));
  while ( $zadr = mysqli_fetch_assoc($zoek_actuele_dracht)) { $volwId = $zadr['volwId']; }

}*/ // EInde Stap 2 : Zoek drachtige ooi zonder worp binnen 145 dagen t.o.v. de geboortedatum $fldDag


if(!isset($volwId)) { // Stap 3 : Zoek actuele dekking (al dan niet met drachtregistratie) zonder worp binnen 145 dagen

// Huidige worp bestaat niet want deze stap 3 wordt niet doorlopen als stap 1 van toepassing is. Binnen $zoek_actuele_dekking_binnen_145dagen hoeft dus geen rekening te worden gehouden met een actuele worp

$zoek_actuele_dekking_binnen_145dagen = mysqli_query($db,"
SELECT max(v.volwId) volwId
FROM tblVolwas v
 join tblHistorie h on (v.hisId = h.hisId)
 left join tblSchaap s on (s.volwId = v.volwId)
WHERE h.skip = 0 and v.mdrId = '".mysqli_real_escape_string($db,$mdrId)."' and isnull(s.volwId) and date_add(h.datum, interval 145 day) > '".mysqli_real_escape_string($db,$fldDag)."'
") or die (mysqli_error($db));
  while ( $zadr = mysqli_fetch_assoc($zoek_actuele_dekking_binnen_145dagen)) { $volwId = $zadr['volwId']; }

} // EInde Stap 3 : Zoek actuele dekking zonder worp en zonder registratie dracht

if(!isset($volwId)) { // Stap 4 nieuw koppel (vader al dan niet bekend) maken in tblVolwas

$insert_tblHistorie = "INSERT INTO tblHistorie set stalId = '".mysqli_real_escape_string($db,$fldStalIdMdr)."', datum = '".mysqli_real_escape_string($db,$fldDrachtDay)."', actId = 18 ";

/*echo $insert_tblHistorie.'<br>';  ##*/mysqli_query($db,$insert_tblHistorie) or die (mysqli_error($db));


$zoek_hisId = mysqli_query($db,"
SELECT max(hisId) hisId
FROM tblHistorie
WHERE stalId = '".mysqli_real_escape_string($db,$fldStalIdMdr)."' and actId = 18
") or die (mysqli_error($db));
  while ( $zh = mysqli_fetch_assoc($zoek_hisId)) { $hisId = $zh['hisId']; }

 $insert_tblVolwas = "INSERT INTO tblVolwas set hisId = '".mysqli_real_escape_string($db,$hisId)."', mdrId = '".mysqli_real_escape_string($db,$mdrId)."' ";

/*echo $insert_tblVolwas.'<br>';  ##*/mysqli_query($db,$insert_tblVolwas) or die (mysqli_error($db));
  // Einde Koppel maken

 $zoek_volwId = mysqli_query($db,"
 SELECT max(volwId) volwId
 FROM tblVolwas
 WHERE mdrId = '".mysqli_real_escape_string($db,$mdrId)."'
 ") or die (mysqli_error($db));
  while ( $vw = mysqli_fetch_assoc($zoek_volwId)) { $volwId = $vw['volwId']; }


} // Einde Stap 4 nieuw koppel maken




// Worpverloop vastleggen

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

// Einde Worpverloop vastleggen

} // Einde if(isset($fldStalIdMdr))
// ********************
// EINDE BEPAAL VOLWID
// ********************


// ***************************
//    GEGEVENS INLEZEN
// ***************************

unset($rel_best);

//if(!isset($dmafv_mdr)) { $dmafv_mdr = $fldDag; }
if (
 (isset($fldDag) && isset($fldLevnr) && isset($fldStalIdMdr) && $fldDag >= $dmaanv_1_mdr && (!isset($dmafv_mdr) || $fldDag <= $dmafv_mdr) && isset($fldHok)) // Veplichte velden bij module Technisch. Moeder is verplicht bij module technisch
|| ($modtech == 0 && isset($fldDag) && isset($fldLevnr) ) // Veplichte velden zonder module Technisch
) { $scenario = 'Geboren_lam'; }

else if(!isset($fldLevnr) && isset($fldDag) && ((isset($fldStalIdMdr) && $modtech == 1) || ($modtech == 0)) ) 
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
$insert_tblStal = "INSERT INTO tblStal set lidId = '".mysqli_real_escape_string($db,$lidId)."', ubnId = '".mysqli_real_escape_string($db,$ubnId)."', schaapId = '".mysqli_real_escape_string($db,$schaapId)."',  rel_best = " . db_null_input($rel_best) ;
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
include "maak_request.php";
// Einde Insert tblMeldingen
}
/* Bijwerken tabel impReader*/    

if($reader == 'Agrident') { 
$updateReader = "UPDATE impAgrident set verwerkt = 1 WHERE Id = '".mysqli_real_escape_string($db,$recId)."' " ;
}
else { 
$updateReader = "UPDATE impReader set verwerkt = 1 WHERE readId = '".mysqli_real_escape_string($db,$recId)."' " ;
}

if($recId == 59604) { echo $updateReader.'<br>'.'<br>'; } mysqli_query($db,$updateReader) or die (mysqli_error($db)); 

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
          
  
