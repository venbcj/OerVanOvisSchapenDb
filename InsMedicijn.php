<?php

require_once("autoload.php");

/* 
11-12-2014 gemaakt 
8-3-2015 : Login toegevoegd */
$versie = '24-2-2017'; /* Aangpast na.v. Release 2 of wel nieuwe databasestructuur */
$versie = '12-3-2017'; /* Verwijderen mogelijk gemaakt */
$versie = '29-7-2017'; /* toedienen bij afgevoerden mogelijk gemaakt */
$versie = '25-2-2018'; /* standaard hoeveelheid gebasserd op combireden */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '14-11-2020'; /* Onderschied gemaakt tussen reader Agrident en Biocontrol */
$versie = '15-01-2021'; /* Toedien aantal uit tabel impAgrident gehaald */
$versie = '07-09-2021'; /* In query's zoek_afvoerdatum en zoek_fase h.skip = 0 in where clause toegevoegd */
$versie = '22-09-2021'; /* func_artikelnuttigen.php toegevoegd */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-01-2025'; /*  and isnull(st.rel_best) toegevoegd aan opvragen van gegevens uit tabel impAgrident zodat stalId's van uitgeschaarden niet worden getoond */
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
$titel = 'Inlezen Medicatie';
$file = "InsMedicijn.php";
include "login.php"; ?>

            <TD valign = "top">
<?php 
if (Auth::is_logged_in()) {
    $impagrident_gateway = new ImpAgridentGateway();
    $schaap_gateway = new SchaapGateway();
    $stal_gateway = new StalGateway();
    $historie_gateway = new HistorieGateway();
    $inkoop_gateway = new InkoopGateway();
    $reden_gateway = new RedenGateway();

require_once "func_artikelnuttigen.php";

If (isset ($_POST['knpInsert_'])) {
    include "post_readerMed.php"; #Deze include moet voor de vervversing in de functie header()
    } 

$velden = "rd.Id readId, date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr, NULL scan, 
    s.schaapId, rd.artId, rd.toedat, i.artId artId_db, round(i.stdat) stdat, i.eenheid, rd.reden reduId, i.actief a_act, 
    ru.pil r_act, i.inkId, i.vrdat";

    $tabel = $impagrident_gateway->getInsMedicijnAgridentFrom();
    $WHERE = $impagrident_gateway->getInsMedicijnAgridentWhere($lidId);
    $order_by = "ORDER BY sort, rd.Id";

include "paginas.php";
$data = $paginator->fetch_data($velden, $order_by);

?>

<table border = 0>
<form action="InsMedicijn.php" method = "post">

<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Toedien<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Medicijn<hr></th>
 <th>Aantal<hr></th>
 <th>hoeveel<br> heid<hr></th>
 <th>Eenheid<hr></th>
 <th>Reden<hr></th>
 <th>Status<hr></th>
</tr>
<?php 

if(isset($data))  {    foreach($data as $key => $array)
    {
        $var = $array['datum'];
$dm = str_replace('/', '-', $var);
$dag = date('d-m-Y', strtotime($dm));
$date  = date('Y-m-d', strtotime($dm));
    
    $Id = $array['readId'];
    $levnr = $array['levnr'];
    $scan = $array['scan']; # het scannummer uit het veld reden_pil in tabel impReader
    $schaapId = $array['schaapId']; 
    //$inkId = $array['inkId']; #InkId uit vw_Voorraad indien voorradig anders uit tblInkoop
    $artId_rd = $array['artId']; #Artikel uit impAgrident of uit tblCombiReden
    $artId_db = $array['artId_db'];
    $aantal = $array['toedat']; #Toedien aantal uit impAgrident
    $stdat = $array['stdat']; #stdat aantal uit tblCombiReden
    $eenheid = $array['eenheid']; 
    $reduId = $array['reduId']; /*Reden uit tblCombiReden*/
    $p_act = $array['a_act'];
    $r_act = $array['r_act'];
    $vrrd = $array['vrdat'];

    $kzlArt = $artId_db;
    $kzlRedu = $reduId;
    
if(isset($schaapId)) {
    $fs = $schaap_gateway->zoek_fase($lidId, $schaapId);
        $gevonden = $fs['schaapId'];    if(isset($gevonden)) { $fase = 'lam'; }
        $sekse = $fs['geslacht'];
        $prnt = $fs['prnt'];     if(isset($prnt)) { if($sekse = 'ooi') { $fase = 'moederdier'; } else if($sekse = 'ram') { $fase = 'vaderdier'; } }
        $weg = $fs['s_af']; if(isset($weg)) { $fase = 'afgevoerd'; }

// Zoek op afvoerdatum ter controle op toedien datum
        $stalId = $stal_gateway->zoek_laatste_stal_medicijn($schaapId);
        [$dmafv, $afvdm] = $historie_gateway->zoek_afvoerdatum($stalId);
// Einde Zoek op afvoerdatum ter controle op toedien datum
}    

// De voorwaarden om in te kunnen lezen. 
if (isset($_POST['knpVervers_'])) {

    $dag = $_POST["txtDatum_$Id"];
        $makedate = date_create($dag);
        $date =  date_format($makedate, 'Y-m-d');
    $kzlArt = $_POST["kzlPil_$Id"];
    $aantal = $_POST["txtAantal_$Id"];
    $reduId = $_POST["kzlReden_$Id"];
    
    if(empty($kzlArt)) {
        $vrrd = '';
    } else {
        [$vrrd, $p_act] = $inkoop_gateway->zoek_voorraad($lidId, $kzlArt);
    }

    if(empty($reduId)) {
        $r_act = '';
    } else {
        $r_act = $reden_gateway->zoek_reden_actief($lidId, $reduId);
    }
    } 


// Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
if (!empty($kzlArt)) {
    [$stdat, $eenheid] = $inkoop_gateway->porties($lidId, $kzlArt);
}
// Einde Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
    
unset($color);
unset($onjuist);

If (!isset($fase)) { $color = 'red'; $onjuist = 'Levensnummer onbekend'; }
else if ((!isset($artId_db) && !isset($_POST['knpVervers_'])) || (isset($_POST['knpVervers_']) && empty($_POST["kzlPil_$Id"])) ) { $color = 'red'; $onjuist = 'Medicijn is onbekend'; }
elseif (empty($dag)) { $color = 'red'; $onjuist = 'Datum is onbekend'; }
elseif ($p_act <> 1) { $color = 'red'; $onjuist = 'Het medicijn is niet actief'; }
elseif (empty($vrrd) || $vrrd == 0) { $color = 'red'; $onjuist = 'Het medicijn is niet meer op voorraad'; }
elseif (empty($aantal))                 { $color = 'red'; $onjuist = 'Het aantal is onbekend'; }
elseif (empty($stdat))                  { $color = 'red'; $onjuist = 'De standaard hoeveelheid is onbekend'; }
elseif (isset($dmafv) && $dmafv <= $date)   { $color = 'red'; $onjuist = 'De afvoerdatum moet na de toedien datum liggen'; }
elseif ($r_act <> 1 && !empty($reduId)) { $color = 'red'; $onjuist = 'De reden is niet actie'; }

    if (isset($onjuist)) {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE De voorwaarden om in te kunnen lezen.  

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:13px;">
 <td align = "center"> 

    <input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
    <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$Id"; ?> value = <?php echo $dag; ?> >
 </td>

<?php if(!empty($fase)) { ?> <td> <?php echo $levnr;} else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>


<?php 
// Declaratie MEDICIJN (De medicijnen uit voorraad)
$zoek_artId_op_voorraad = mysqli_query($db," 
SELECT a.artId, a.naam, a.stdat, e.eenheid, sum(i.inkat-coalesce(n.vbrat,0)) vrdat
FROM tblEenheid e
 join tblEenheiduser eu on (e.eenhId = eu.eenhId)
 join tblInkoop i on (i.enhuId = eu.enhuId)
 join tblArtikel a on (i.artId = a.artId)
 left join (
    SELECT n.inkId, sum(n.nutat*n.stdat) vbrat
    FROM tblNuttig n
    GROUP BY n.inkId
 ) n on (i.inkId = n.inkId)
WHERE eu.lidId = '".mysqli_real_escape_string($db,$lidId)."' and i.inkat-coalesce(n.vbrat,0) > 0 and a.soort = 'pil'
GROUP BY a.artId, a.naam, a.stdat, e.eenheid
ORDER BY a.naam
") or die (mysqli_error($db));

$index = 0;
while ($pil = mysqli_fetch_array($zoek_artId_op_voorraad))
{
   $pilId[$index] = $pil['artId'];
   $pilln[$index] = $pil['naam'];
   $pilRaak[$index] = $pil['artId'];
   $index++;
}
unset($index);
// EINDE Declaratie MEDICIJN ?> 


 <td style = "font-size : 9px;" >
<!-- KZLMEDICIJN -->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlPil_$Id"; ?> >
  <option></option>
<?php   $count = count($pilln);
for ($i = 0; $i < $count; $i++){

    $opties = array($pilId[$i]=>$pilln[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $artId_rd == $pilRaak[$i]) || (isset($_POST["kzlPil_$Id"]) && $_POST["kzlPil_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }     
            }
}
?> </select>
 </td> <!-- EINDE KZLMEDICIJN -->

 <td>
    <input type = "text" style = "font-size : 10px; text-align : right;" size = 1 name = <?php echo "txtAantal_$Id"; ?>  value = <?php echo $aantal; ?> >
 </td>

 <td align="center" >
<?php 
if(!empty($kzlArt) || isset($_POST['knpVervers_'])) {echo $stdat;} ?>
 </td>
 <td>
<?php if(!empty($kzlArt) || isset($_POST['knpVervers_'])) { echo $eenheid; } ?>
 </td>
<?php 
if(empty($reduId)) { $kzlRedu = 'NULL'; } else { $kzlRedu = $reduId; } /*echo '$kzlRedu = '.$kzlRedu;*/
$zoek_aantal_reden = mysqli_query($db,"
SELECT count(reduId) aant 
FROM (
    SELECT ru.reduId 
    FROM tblRedenuser ru
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.pil = 1
   union
    SELECT ru.reduId
    FROM tblRedenuser ru
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.reduId = '".mysqli_real_escape_string($db,$kzlRedu)."'
 ) B 
") or die (mysqli_error($db)); 
    while ($red = mysqli_fetch_array($zoek_aantal_reden)) { $records_klzReden = $red['aant'];   }  
    
// Declaratie REDEN (De redenen waaruit kan worden gekozen)
$queryReden = ("
SELECT reduId, reden 
FROM (
    SELECT ru.reduId, r.reden  
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.pil = 1
   union
    SELECT ru.reduId, r.reden
    FROM tblReden r
     join tblRedenuser ru on (r.redId = ru.redId)
    WHERE ru.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ru.reduId = '".mysqli_real_escape_string($db,$kzlRedu)."'
 ) A
GROUP BY reduId, reden
ORDER BY reden
              "); 
$qryReden = mysqli_query($db,$queryReden) or die (mysqli_error($db)); 


$index = 0; 
while ($red = mysqli_fetch_array($qryReden)) 
{ 
   $rduId[$index] = $red['reduId'];
   $redn[$index] = $red['reden'];
   $index++; 
}
unset($index); 
//dan het volgende: 
// EINDE Declaratie REDEN
?>
 <td>
<!-- KZLREDEN -->
 <select style="width:145; font-size:12px;" name = <?php echo "kzlReden_$Id"; ?> >
  <option></option>
<?php   $count = $records_klzReden;
for ($i = 0; $i < $count; $i++){

    $opties = array($rduId[$i]=>$redn[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $reduId == $rduId[$i]) || (isset($_POST["kzlReden_$Id"]) && $_POST["kzlReden_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }     
            }
} 
?> </select>
 </td> <!-- EINDE KZLREDEN -->
    
    
    
    
 <td style = "color : <?php echo $color; ?> ; font-size:12px; " > <?php 

 if (isset($onjuist)) { echo '&nbsp&nbsp '.$onjuist; } ?>

 
 </td>

</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php unset($schaapId); unset($dmafv); }
} //einde if(isset($data))
 unset($fase); ?>
</table>
</form> 

    </TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>
<?php
include "select-all.js.php";
?>

</body>
</html>
