<?php

require_once("autoload.php");

/* 11-8-2014 : veld type gewijzigd in fase 
23-11-2014 : functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server 
8-3-2015 : Login toegevoegd 
$versie = '9-11-2016';  /* vw_StatusSchaap verwijderd */
$versie = '23-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = "22-1-2017"; /* tblBezetting gewijzigd naar tblBezet */
$versie = '28-2-2017'; /* Ras en gewicht niet veplicht gemaakt */
$versie = '5-5-2017'; /* Controle op aankoopdatum verwijderd */
$versie = '30-6-2017'; /* Wachtdagen toegevoegd      en unset(status) toegevoegd 27-7 : schaapId moet wel bestaan !! */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '15-6-2018';  /* dmmax_bij_afvoer houdt geen rekening meer met melddatums bij RVO */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2018'; /* Controle op speendatum gebeurd niet meer op dieren die in een verblijf hebben gezeten maar op aanwezigheid van aankoopdatum. Eenmaal aangekocht is speendatum nooit meer verplicht */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '15-2-2019'; /* In query data laatste historie aangepast. Dit was bedoel ter controle op afvoer. Bij nieuwe klant (Fokke) werd later speendatum aangevuld. Ipv afvoerdatum was speendatum het laatste historie-item. Nu wordt er specifiek alleen afvoer gezocht in historie. Het niet bestaan van status betekent nu niet meer dat levensnummer niet bestaat ! Bestaan van levensnummer wordt gecontroleerd met isset(schaapId). Hidden veld txtFase_Id verwijderd */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '9-11-2019'; /* Werknr toegevoegd en op gesorteerd */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '22-5-2020';  /*naam bestemming in keuzelijst uitgebreid met ubn. Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '27-6-2020';  /* Reden afvoer toegevoegd */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt 16-7 wdgn gewijzigd in wdgn_v */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$versie = '23-03-2024'; /* Alleen gewicht registreren (tussenweging) mogelijk gemaakt */
$versie = '27-10-2024'; /* Export-xlsx toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '24-08-2025'; /* Uitgeschaarde schapen konden worden afgevoerd. Nu niet meer */
$versie = '30-08-2025'; /* ActId 12 (zijnde afgeleverd) uit tabel tblActie wordt vanaf nu ook gebruikt om ubn te wijzigen. Zie InsGrWijzigingUbn.php. Als het nieuwe veld ubnId in tabel impAgrident leeg is dan is het een reguliere afvoer van een lam. Is het veld ubnId gevuld dan betreft het een wijziging van ubn van de gebruiker */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Inlezen Afvoer';$subtitel = '';
$file = "InsAfvoer.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) { 

include "vw_HistorieDm.php";


If (isset($_POST['knpInsert_'])) {

    include "post_readerAfv.php";#Deze include moet voor de verversing in de functie header()
    }
    
// Aantal nog in te lezen AFGELEVERDEN
/*if($reader == 'Biocontrol') {
$afgeleverden = mysqli_query($db,"SELECT count(*) aant
                                  FROM impReader
                                  WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and teller_afv is not NULL and isnull(verwerkt) ") or die (mysqli_error($db));
        $row = mysqli_fetch_assoc($afgeleverden);
            $aantafl = $row['aant'];
}

if($reader == 'Agrident') {
$afgeleverden = mysqli_query($db,"SELECT count(*) aant
                                  FROM impVerplaatsing
                                  WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and wat = 'Afvoer' and isnull(verwerkt) ") or die (mysqli_error($db));
        $row = mysqli_fetch_assoc($afgeleverden);
            $aantafl = $row['aant'];
}*/
// EINDE Aantal nog in te lezen AFGELEVERDEN


$velden = "rd.Id readId, rd.datum, right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") werknr, rd.levensnummer levnr, rd.ubn ubn_afv, r.ubn ctrubn, rd.reden redId_rd, red.reduId reduId_db, gewicht kg, s.schaapId, s.geslacht, ouder.datum dmaanw, lower(haf.actie) actie, haf.af, hs.datum dmspeen, ak.datum dmaankoop, date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, date_format(max.datummax_kg,'%d-%m-%Y') maxdatum_kg, max.datummax_kg, b.bezId ";

$tabel = "
impAgrident rd
 left join (
    SELECT s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (s.levensnummer = rd.levensnummer)
 left join (
 	SELECT st.schaapId, max(st.stalId) stalId
 	FROM tblStal st
 	 join tblUbn u on (st.ubnId = u.ubnId)
 	WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY st.schaapId
 ) mst on (mst.schaapId = s.schaapId)
 left join (
    SELECT st.stalId, h.hisId, a.actie, a.af
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1 and h.skip = 0
 ) haf on (mst.stalId = haf.stalId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 4 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT levensnummer, max(datum) datum 
    FROM tblSchaap s
     join tblStal st on (st.schaapId = s.schaapId)
     join tblHistorie h on (h.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 2 and h.skip = 0
    GROUP BY levensnummer
 ) ak on (ak.levensnummer = rd.levensnummer)
 left join (
    SELECT schaapId, max(datum) datummax_afv, max(datum_kg) datummax_kg
    FROM (
        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 1 and h.skip = 0 and s.levensnummer is not null

        Union

        SELECT s.schaapId, h.datum, h.datum datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE (a.actId = 5 or a.actId = 8 or a.actId = 9 or a.actId = 12 or a.actId = 13 or a.actId = 14) and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
         left join 
         (
            SELECT s.schaapId, h.actId, h.datum 
            FROM tblSchaap s
             join tblStal st on (st.schaapId = s.schaapId)
             join tblHistorie h on (h.stalId = st.stalId) 
            WHERE actId = 2 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
         ) koop on (s.schaapId = koop.schaapId and koop.datum <= h.datum)
        WHERE a.actId = 3 and h.skip = 0 and (isnull(koop.datum) or koop.datum < h.datum) and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'

        Union

        SELECT s.schaapId, h.datum, NULL datum_kg, a.actie, h.actId, h.skip
        FROM tblSchaap s
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (h.stalId = st.stalId)
         join tblActie a on (a.actId = h.actId)
        WHERE a.actId = 4 and h.skip = 0

        Union

        SELECT  mdr.schaapId, min(h.datum) datum, NULL datum_kg, 'Eerste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum, NULL datum_kg, 'Laatste worp' actie, NULL, 0 skip
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum, NULL datum_kg, 'Gevoerd' actie, NULL , h.skip
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' 
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY schaapId
 ) max on (s.schaapId = max.schaapId)
 left join (
    SELECT p.lidId, p.ubn
    FROM tblPartij p
     join tblRelatie r on (p.partId = r.partId)
    WHERE p.actief = 1 and r.relatie = 'deb' and r.actief = 1
 ) r on(r.ubn = rd.ubn and r.lidId = rd.lidId)
 left join (
    SELECT max(b.bezId) bezId, s.levensnummer
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (h.stalId = st.stalId)
     join tblSchaap s on (st.schaapId = s.schaapId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
    GROUP BY s.levensnummer
 ) b on (rd.levensnummer = b.levensnummer)
 left join tblRedenuser red on (rd.reden = red.redId and red.lidId = '".mysqli_real_escape_string($db,$lidId)."')
";

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 12 and isnull(rd.ubnId) and isnull(rd.verwerkt) ";

include "paginas.php";

$data = $page_nums-> fetch_data($velden, "ORDER BY right(rd.levensnummer,".mysqli_real_escape_string($db,$Karwerk).") "); 

?>

<table border = 0>
<tr> <form action="InsAfvoer.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php 
/*echo '<br>'; 
echo '$page_nums->total_pages : '.$page_nums->total_pages.'<br>'; 
echo '$page_nums->total_records : '.$page_nums->total_records.'<br>'; 
echo '$page_nums->rpp : '.$page_nums->rpp.'<br>'; */
echo /*'$page_numbers : '.*/$page_numbers/*.'<br> '.$record_numbers.'<br>'*/; 
/*echo '$page_nums->count_records() : '. $page_nums->count_records();*/ 
//echo '$page_nums->pagina_string : '. $page_nums->pagina_string; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Afvoeren<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Alleen<br>weging<br>registreren  <input type="checkbox" id="selectall_kg" /> <hr></th>
 <th>Afvoer<br>datum<hr></th>
 <th>Werknr<hr></th>
 <th>Levensnummer<hr></th>
<?php if($modtech == 1) { // Velden die worden getoond bij module technisch ?>
 <th>Gewicht<hr></th>
<?php } ?>
 <th>Bestemming<hr></th>
 <th>Reden<hr></th>
 <th>Generatie<hr></th>
 <th>Wachtdagen<br>resterend<hr></th>
 <th colspan = 2 > <a href="exportInsAfvoer.php?pst=<?php echo $lidId; ?> "> Export-xlsx </a> <br><br><hr></th>
 <th ></th>
</tr>
<?php

// Declaratie BESTEMMING            // lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$qryRelatie = ("SELECT r.relId, '6karakters' ubn, concat(p.ubn, ' - ', p.naam) naam
            FROM tblPartij p join tblRelatie r on (p.partId = r.partId)    
            WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and relatie = 'deb' and p.actief = 1 and r.actief = 1
                  and isnull(p.ubn)
            union
            
            SELECT r.relId, p.ubn, concat(p.ubn, ' - ', p.naam) naam
            FROM tblPartij p
             join tblRelatie r on (p.partId = r.partId)    
            WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."' and relatie = 'deb' and p.actief = 1 and r.actief = 1 
                  and ubn is not null
            ORDER BY naam"); 
$relatienr = mysqli_query($db,$qryRelatie) or die (mysqli_error($db)); 

$index = 0; 
$relnum = [];
while ($rnr = mysqli_fetch_array($relatienr)) 
{ 
   $relnId[$index] = $rnr['relId']; 
   $relnum[$index] = $rnr['naam'];
   $relUbn[$index] = $rnr['ubn'];   
   $index++; 
} 
unset($index);
// Einde Declaratie BESTEMMING

// Declaratie REDEN            
$qryReden = ("
SELECT ru.reduId, r.reden
FROM tblRedenuser ru
 join tblReden r on (r.redId = ru.redId)    
WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.afvoer = 1
ORDER BY reden
"); 
$reden = mysqli_query($db,$qryReden) or die (mysqli_error($db)); 

$index = 0; 
while ($rdn = mysqli_fetch_array($reden)) 
{ 
   $reduId[$index] = $rdn['reduId']; 
   $rednm[$index] = $rdn['reden']; 
   $index++; 
} 
unset($index);
// Einde Declaratie REDEN

if(isset($data))  {    foreach($data as $key => $array)
    {
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$date       = date('Y-m-d', strtotime($date));
    
    $Id = $array['readId'];
    $werknr = $array['werknr'];
    $levnr = $array['levnr'];
    $ubnbest = $array['ubn_afv'];
    $ubn_db = $array['ctrubn'];
    $redId_rd = $array['redId_rd'];
    $reduId_db = $array['reduId_db'];
    $kg = $array['kg'];
    $schaapId = $array['schaapId'];
    $geslacht = $array['geslacht'];
    $dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else { $fase = 'lam';}
    $status = $array['actie'];
    $af = $array['af']; if(isset($af) && $af == 1) { $status = $status; } else { $status = $fase; }
    $speen = $array['dmspeen'];
    $aank = $array['dmaankoop'];
    $bezet = $array['bezId'];
    $dmmax_bij_afvoer = $array['datummax_afv'];
    $dmmax_bij_wegen = $array['datummax_kg'];
    $maxdm_bij_afvoer = $array['maxdatum_afv'];
    $maxdm_bij_wegen = $array['maxdatum_kg'];

// Controleren of ingelezen waardes worden gevonden.
 $kzlRelatie = $ubn_db; 
if (isset($_POST['knpVervers_'])) { 
    $datum = $_POST["txtAfvoerdag_$Id"]; 
    if(isset($_POST["txtKg_$Id"])) { $kg = $_POST["txtKg_$Id"]; } 
    $kzlRelatie = $_POST["kzlBest_$Id"]; 
    $makeday = date_create($_POST["txtAfvoerdag_$Id"]); $date =  date_format($makeday, 'Y-m-d'); 
}

// t.b.v. checkbox Afvoeren
     If     
     ( !isset($schaapId) || $status == 'overleden' || $status == 'afgeleverd' || $status == 'uitgeschaard' || /*levensnummer moet aanwezig zijn */
         empty($datum)                            || # of datum is leeg
         $date < $dmmax_bij_afvoer                            || # of datum ligt voor de laatst geregistreerde datum van het schaap
         ($modtech == 1 && !isset($speen) && !isset($aank))        || # speendatum ontbreekt bij dieren die niet zijn aangekocht.
         //($modtech == 1 && !isset($aank) && !isset($bezet))        || # aankoopdatum ontbreekt van dieren die niet in een hok hebben gezeten.
         //($modtech == 1 && empty($kg) && $fase == 'lam')            || # of gewicht is leeg
         //$status == 'afgeleverd'    15-2-19 : dubbel benoemd            || # of is reeds afgeleverd
        // $status == 'overleden'    15-2-19 : dubbel benoemd            || # of is reeds overleden
        empty($kzlRelatie)                      # bestemming is onbekend                         
                                                 
     )
     {    $oke_afv = 0;    } else { $oke_afv = 1;    } // $oke_afv kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// Einde t.b.v. checkbox Afvoeren

// t.b.v. checkbox Alleen wegen
     If     
     ( !isset($schaapId) || $status == 'overleden' || $status == 'afgeleverd' || $status == 'uitgeschaard' || /*levensnummer moet aanwezig zijn */
         empty($datum)                                || # of datum is leeg
         $date < $dmmax_bij_wegen                    || # of datum ligt voor de laatst geregistreerde datum van het schaap
         ($modtech == 1 && !isset($speen) && !isset($aank))        || # speendatum ontbreekt bij dieren die niet zijn aangekocht.                     
         !isset($kg)                                 || # Bij aanroepen van pagina is Gewicht leeg
         (isset($kg) && empty($kg) )                       # Bij verversen van pagina is Gewicht leeg

     )
     {    $oke_kg = 0;    } else {    $oke_kg = 1;    } // $oke_kg kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// Einde t.b.v. checkbox Alleen wegen

// EINDE Controleren of ingelezen waardes worden gevonden . 

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke_afv == 1) /* Als onvolledig is gewijzigd naar volledig juist wordt checkbox eenmalig automatisch aangevinkt */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; $cbKg = $_POST["chbKg_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"];  $cbKg = $_POST["chbKg_$Id"]; } 
   else { $cbKies = $oke_afv; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet 


   //if(isset($_POST['knpVervers_'])) {} ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:13px;">
 <td align = center> 

    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke_afv == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke_afv; ?> > <!-- hiddden -->
 </td>
 <td align = center>
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
  <td align = center>
    <input type = checkbox class="weight" name = <?php echo "chbKg_$Id"; ?> value = 1 <?php if(isset($cbKg)) { echo $cbKg == 1 ? 'checked' : ''; } 

    if ($oke_kg == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
 </td>
 <td>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAfvoerdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>

<?php if(isset($schaapId)) { echo "<td align = center >".$werknr;} else { ?> <td align = center style = "color : red"> <?php echo $werknr;} ?>
 </td>

 <?php if(isset($schaapId)) { echo "<td>".$levnr;} else { ?> <td align = center style = "color : red"> <?php echo $levnr;} ?>
    <input type = "hidden" name = <?php echo "txtlevafl_$Id"; ?> value = <?php echo $levnr; ?> size = 9 style = "font-size : 9px;">
 </td>

<?php if($modtech == 1) { ?>    
 <td style = "font-size : 9px;"> 

    <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php echo $kg; ?> >

 </td>
<?php } ?>


 <td >

<!-- KZLBESTEMMING -->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlBest_$Id"; ?> >
  <option></option>
<?php    $count = count($relnum);
for ($i = 0; $i < $count; $i++){

    $opties = array($relnId[$i]=>$relnum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $ubnbest == $relUbn[$i]) || (isset($_POST["kzlBest_$Id"]) && $_POST["kzlBest_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>
<?php if( $ubnbest<> NULL && empty($ubn_db) && empty($_POST["kzlBest_$Id"]) ) {echo $ubnbest; ?> <b style = "color : red;"> ! </b>  <?php } ?>
    </td> <!-- EINDE KZLBESTEMMING -->

<td >

<!-- KZLREDEN-->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlReden_$Id"; ?> >
  <option></option>
<?php    $count = count($reduId);
for ($i = 0; $i < $count; $i++){

    $opties = array($reduId[$i]=>$rednm[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $reduId_db == $reduId[$i]) || (isset($_POST["kzlReden_$Id"]) && $_POST["kzlReden_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>
<?php 
// hier stond "$red_rd" maar die variabele bestaat niet. @TODO is dit juist gecorrigeerd?
if( $redId_rd <> NULL && empty($reduId_db) && empty($_POST["kzlReden_$Id"]) ) { ?> <b style = "color : red;"> ! </b>  <?php } ?>
    </td> <!-- EINDE KZLREDEN -->
    
 <td width = 80 align = "center"><?php 
if (isset($status)) { echo $fase ;} ?>
 </td> <?php
// Wachtdagen bepalen
if(isset($schaapId)) {
$zoek_pil = mysqli_query($db,"
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, DATEDIFF( (h.datum + interval art.wdgn_v day), '".mysqli_real_escape_string($db,$date)."') resterend
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 8 and h.skip = 0
 and '".mysqli_real_escape_string($db,$date)."' < (h.datum + interval art.wdgn_v day)
") or die (mysqli_error($db));    

$vandaag = date('Y-m-d');
        while($row = mysqli_fetch_array($zoek_pil))
        { $pildm = $row['datum']; 
          $pil = $row['naam']; 
          $wdgn_v = $row['resterend']; }
}
// Einde Wachtdagen bepalen
?>
 <td align = center ><?php if(isset($wdgn_v)) { echo $wdgn_v; } ?></td>
<!-- Foutmeldingen -->
 <td colspan = 2 width = 300 style = "color : red"> <?php
    if(!isset($schaapId))                       { echo 'Levensnummer onbekend.'; }
    else if($status == 'afgeleverd')   { echo "Dit schaap is reeds $status."; } 
    else if($status == 'overleden' || $status == 'uitgeschaard')   { echo "Dit schaap is $status."; } 
    else if(isset($fase) && $date < $dmmax_bij_afvoer)   { echo "Datum ligt voor $maxdm_bij_afvoer."; } 
    else if($date < $dmmax_bij_wegen)   { echo "Datum ligt voor $maxdm_bij_wegen."; } 
    else if($modtech == 1 && !isset($speen) && !isset($aank)) { echo "Dit schaap heeft nog geen speendatum."; }
    else if(isset($wdgn_v)) { echo $pildm.' - '.$pil; } unset($wdgn_v);
    //else if($modtech == 1 && !isset($aank) && !isset($bezet)) { echo "Dit schaap heeft nog geen aankoopdatum."; } 
    unset($status); ?>
 </td>    
 <td>    
 </td>    
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 




    </TD>
<?php
include "menu1.php"; }

include "select-all.js.php";
?>
</tr>
</table>

</body>
</html>
