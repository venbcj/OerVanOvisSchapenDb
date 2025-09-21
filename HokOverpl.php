<?php

require_once("autoload.php");

/* 21-11-2015 Individueel spenen gewijzigd naar heel hok spenen 
23-11-2015 breedte kzlHok flexibel gemaakt via login.php */
$versie = "20-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel en overbodige hidden velden verwijderd (txtLevnr en txtMindatum) */
$versie = "23-1-2017"; /* 22-1-2017 tblBezetting gewijzigd naar tblBezet 23-1-2017 kalender toegevoegd */
$versie = "6-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = "12-2-2017"; /* tblPeriode verwijderd en hok direct aan tblBezet gekoppeld */
$versie = '21-05-2018';  /* Meerdere pagina's gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd aan tblHistorie en sql beveiligd */
$versie = '05-01-2024'; /* Schapen die in het verblijf spenen de status aanwas kregen werden niet getoond. Dit is aangepast */
$versie = '07-01-2024'; /* Select_all toegevoegd en include kalender op een andere plek gezet omdat dit elkaar anders bijt. */
$versie = '12-01-2024'; /* Keuze alleen lammeren of alleen volwassenen mogelijk gemaakt incl sortering op fase en werknr */
$versie = '20-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 940 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
     <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Overplaatsen';
$file = "HokkenBezet.php";
include "login.php"; ?>

                <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {

if(isset($_GET['pstId']))    { $_SESSION["ID"] = $_GET['pstId']; } $ID = $_SESSION["ID"]; /* zorgt het Id wordt onthouden bij het opnieuw laden van de pagina */

switch ($_POST['radFase_'] ?? 'alles') {
case 'lam':
 $_SESSION["KZ"] = 1; 
break;
case 'volw':
 $_SESSION["KZ"] = 2; 
break;
default:
 $_SESSION["KZ"] = NULL; 
}
$KEUZE = $_SESSION["KZ"];


##echo '$KEUZE = '.$KEUZE.'<br>';
    if (!isset($_SESSION['BST'])) $_SESSION['BST'] = 1;
    if (!isset($_SESSION['DT1'])) $_SESSION['DT1'] = '1900-01-01';


if(isset($_POST['knpVerder_']) && isset($_POST['kzlHokall_']))    {
    $datum = $_POST['txtDatumall_']; $_SESSION["DT1"] = $datum;
    $hokkeuze = $_POST['kzlHokall_']; $_SESSION["BST"] = $hokkeuze; } 
 else { $hokkeuze = $_SESSION["BST"];  } $sess_dag = $_SESSION["DT1"]; $sess_bestm = $_SESSION["BST"];

$zoek_hok = mysqli_query ($db,"
SELECT hoknr
FROM tblHok
WHERE hokId = '".mysqli_real_escape_string($db,$ID)."'
") or die (mysqli_error($db));
    while ($h = mysqli_fetch_assoc($zoek_hok)) { $hoknr = $h['hoknr']; }

$zoek_nu_in_verblijf_geb_spn = mysqli_query($db,"
SELECT count(b.bezId) aantin
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)
") or die (mysqli_error($db));
        
    while($nu_l = mysqli_fetch_assoc($zoek_nu_in_verblijf_geb_spn))
        { $nu_lam = $nu_l['aantin']; }
        
$zoek_nu_in_verblijf_parent = mysqli_query($db,"
SELECT count(b.hisId) aantin
FROM (
    SELECT b.hisId, b.hokId
    FROM tblBezet b
     join tblHistorie h on (b.hisId = h.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join (
        SELECT st.schaapId, h.hisId, h.datum
        FROM tblStal st
        join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
    ) prnt on (prnt.schaapId = st.schaapId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and h.skip = 0 and h.datum >= prnt.datum
 ) b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(uit.bezId)
") or die (mysqli_error($db));
        
    while($nu_p = mysqli_fetch_assoc($zoek_nu_in_verblijf_parent))
        { $nu_prnt = $nu_p['aantin']; }        
        
    $nu = $nu_lam + $nu_prnt;

if(isset($_POST['knpSave_'])) { include "save_overpl.php"; } // staat hier omdat $doelId moet zijn gedeclareerd !
    
// Declaratie HOKNUMMER KEUZE

$qryHokkeuze = mysqli_query($db,"
SELECT hokId, hoknr
FROM tblHok h
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."' and hokId != '".mysqli_real_escape_string($db,$ID)."' and actief = 1
ORDER BY hoknr
") or die (mysqli_error($db));

$index = 0;
$hoknum = [];
while ($hnr = mysqli_fetch_array($qryHokkeuze)) 
{ 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $hokRaak[$index] = $hnr['hokId']; 
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER  KEUZE 

$filterResult = '';
         if($KEUZE == 1) { $filterResult = ' and isnull(prnt)'; }
else if($KEUZE == 2) { $filterResult = ' and prnt is not null'; }

// Opbouwen paginanummering 
$velden = " schaapId, levensnummer, geslacht, datum, dag, prnt ";

$tabel = " (

SELECT s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId, 'lam' sort
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
    SELECT max(hisId) hisId, stalId
    FROM tblHistorie
    WHERE skip = 0
    GROUP BY stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 
 join tblHistorie h on (st.stalId = h.stalId)
 join tblBezet b on (b.hisId = h.hisId)
 left join (
    SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
    WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and h.skip = 0 and isnull(uit.bezId) and isnull(prnt.schaapId)

UNION

SELECT s.schaapId, s.levensnummer, s.geslacht, hm.datum, date_format(hm.datum,'%d-%m-%Y') dag, prnt.schaapId prnt, b.hokId, uit.bezId, s.geslacht sort
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join (
        SELECT max(hisId) hisId, stalId
        FROM tblHistorie
        WHERE skip = 0
        GROUP BY stalId
 ) hmax on (hmax.stalId = st.stalId)
 join tblHistorie hm on (hm.hisId = hmax.hisId)
 
 join tblHistorie h on (st.stalId = h.stalId)
 join (
        SELECT b.hisId, b.hokId
        FROM tblBezet b
         join tblHistorie h on (b.hisId = h.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join (
                SELECT st.schaapId, h.hisId, h.datum
                FROM tblStal st
                join tblHistorie h on (st.stalId = h.stalId)
                WHERE h.actId = 3 and h.skip = 0
        ) prnt on (prnt.schaapId = st.schaapId)
        WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and h.skip = 0
 ) b on (b.hisId = h.hisId)
 left join (
        SELECT b.bezId, h1.hisId hisv, min(h2.hisId) hist
        FROM tblBezet b
         join tblHistorie h1 on (b.hisId = h1.hisId)
         join tblActie a1 on (a1.actId = h1.actId)
         join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
         join tblActie a2 on (a2.actId = h2.actId)
        WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h2.actId != 3
        GROUP BY b.bezId, h1.hisId
 ) uit on (uit.hisv = b.hisId)
 join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = st.schaapId) 
 WHERE b.hokId = '".mysqli_real_escape_string($db,$ID)."' and h.skip = 0 and isnull(uit.bezId)

) tbl ";
$WHERE = " WHERE hokId = '".mysqli_real_escape_string($db,$ID)."' and isnull(bezId) ". $filterResult;

include "paginas.php";

$data = $page_nums->fetch_data($velden, "ORDER BY sort, right(levensnummer,$Karwerk)"); 
// Einde Opbouwen paginanummering
if(!isset($sess_dag) && !isset($sess_bestm)) { $width = 100; } 
else { $width = 200; } ?>
<form action="HokOverpl.php" method = "post">
<table border = 0 > <!-- tabel1 --> <tr> <td>
<table border = 0 > <!-- tabel2 -->
<tr> 
<td width = <?php echo $width; ?> rowspan = 2 style = "font-size : 18px;">
  <b> <?php echo $hoknr; ?></b>
</td>

 <?php if(!isset($sess_dag) && !isset($sess_bestm)) {
  include "kalender.php"; ?>
     
     <td width = 750 style = "font-size : 14px;"> 
 &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp Optioneel een datum voor alle schapen
  <input id="datepicker1" type = text name = 'txtDatumall_' size = 8 value = <?php if(isset($sess_dag)) { echo $sess_dag; } ?> > &nbsp
 <?php } else { ?> <td style = "font-size : 14px;">  <?php } ?>
<!-- Opmaak paginanummering -->
 Regels Per Pagina: <?php echo $kzlRpp;
if(isset($sess_dag) || isset($sess_bestm)) { ?> </td> <td align = center > <?php echo $page_numbers.'<br>'; ?> </td> <td> <?php } 
// Einde Opmaak paginanummering ?>
 </td> 
 <td width = 150 align = center>
<?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
  &nbsp &nbsp &nbsp <input type = submit name = "knpVerder_" value = "Verder">
 </td>
 <td width = 200 align = 'right'></td>
   <?php }
else { ?>
  <input type = submit name = "knpVervers_" value = "Verversen"> 
 </td>
 <td width = 200 align = 'right'>
  <input type = submit name = "knpSave_" value = "Overplaatsen">&nbsp &nbsp
 </td> <?php } ?>
</tr>

<tr><td colspan = 7 align = left >
 <?php if(!isset($sess_dag) && !isset($sess_bestm)) { ?>
 Optioneel een verblijf voor alle schapen 
 <!-- KZLVERBLIJF KEUZE-->
 <select style="width:<?php echo $w_hok; ?>;" name= 'kzlHokall_' value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((isset($_POST['kzlHokall_']) && $_POST['kzlHokall_'] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }    
}
?> </select> &nbsp

 <!-- EINDE KZLVERBLIJF KEUZE -->
</td><td></td></tr>
<tr height = 50>
 <td></td>
 <td align="center">
     <input type="radio" name="radFase_" value="lam"> Enkel lammeren 
     <input type="radio" name="radFase_" value="volw"> Enkel volwassenen
     <input type="radio" name="radFase_" value="alles" <?php if(!isset($_POST['radFase_'])) { echo "checked"; } ?>> Beide
 </td>
</tr>
 <?php } ?> 

</table> <!-- einde tabel2 --> </td> </tr>
                                <tr> <td>
<table border = 0 align = left > <!-- tabel3 -->
<?php if(isset($sess_dag) || isset($sess_bestm)) { ?>
<tr valign = bottom style = "font-size : 12px;">
<th valign = bottom >Overplaatsen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
<th>Overplaatsdatum<hr></th>
<th>Levensnummer<hr></th>
<th>naar verblijf<hr></th>
<th>Generatie<hr></th>
<th colspan = 2 ><hr></th>
</tr>
<?php
if(isset($data)) {
    foreach($data as $key => $array)
    {
        $schaapId = $array['schaapId'];
        $levnr = $array['levensnummer'];
        $dmmax = $array['datum'];
        $maxdm = $array['dag'];
        $sekse = $array['geslacht'];
        $prnt = $array['prnt']; if(isset($prnt)) { if($sekse == 'ooi') { $fase = 'moeder'; } else if($sekse == 'ram') { $fase = 'vader'; } } else { $fase = 'lam'; }


        if( (isset($_POST['knpVervers_']) || isset($_POST['knpSave_']) ) && !isset($_POST['kzlHokall_']) ) {
            $cbKies = $_POST["chbkies_$schaapId"];
            $datum = $_POST["txtDatum_$schaapId"];
        }
        // Bij de eerste keer openen van deze pagina bestaat als enigste keer het veld kzlHokall_ .
        //  knpVervers_ bestaat als hidden veld. txtDatum_$schaapId en txtGewicht_$schaapId bestaan dan nog niet.
        //  Variabalen $datum en $kg kunnen enkel worden gevuld als wordt voldaan aan (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_']))  !!!
    if(!isset($datum) && isset($sess_dag)) { $datum = $sess_dag; }
            /*$datum kan al bestaan voor isset($_POST['knpVervers_']) */
        if(isset($datum)) {
            $makeday = date_create($datum);
            $day = date_format($makeday,'Y-m-d'); 
        }
    
// Controleren of ingelezen waardes correct zijn.
    if( empty($datum)                                                    || # Overplaatsdatum is leeg
        $day < $dmmax                                                    || # speendag is kleiner dan laatste registratiedatum
        empty($hokkeuze) # Hok is leeg
    )
    {$oke = 0; } else { $oke = 1; }
     
// EINDE Controleren of ingelezen waardes corretc zijn.  
if (isset($_POST['knpVervers_']) && !isset($_POST['kzlHokall_'])) { $cbKies = $_POST["chbkies_$schaapId"]; $txtOke = $_POST["txtOke_$schaapId"]; } else { $cbKies = $oke; $txtOke = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
    **       OPMAAK  GEGEVENS        **
    ************************************** -->

<tr style = "font-size:14px;">
 <td align = center> 
<!--    <input type = hidden size = 1 name = <?php #echo "txtOke_$schaapId"; ?>  value = <?php #echo $oke; ?> > --><!--hiddden Dit veld zorgt ervoor dat chbkies wordt aangevinkt als het ingebruk wordt gesteld -->
    <input type = hidden size = 1 name = <?php echo "chbkies_$schaapId"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbkies_$schaapId"; ?> value = 1 <?php echo $cbKies == 1 ? 'checked' : ''; if ($oke <> 1) { ?> disabled <?php }  else {    ?> class="checkall" <?php } /* else if ($txtOke == 0) wordt maar 1x gepasseerd nl. als onvolledige gegevens voor het eerst volledig zijn ingevuld. Anders is óf het eerst gedeeldte van het if-statement van toepassing of $txtOke == 1.  */ ?> >

 </td>

<!-- Overplaatsdatum -->
 <td align = center>
 <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDatum_$schaapId"; ?> value = <?php if(isset($datum)) { echo $datum; } ?> >
 </td>

 <td width = 110 align = center> <?php echo $levnr; ?>
 </td>

 <td width = 100 align = center>

<!-- KZLVERBLIJF -->
 <select style="width:<?php echo $w_hok; ?>;" name= <?php echo "kzlHok_$schaapId"; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if (($hokkeuze == $hokRaak[$i]) || (isset($_POST["kzlHok_$schaapId"]) && $_POST["kzlHok_$schaapId"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>

 <!-- EINDE KZLVERBLIJF -->
    
<td align = center> <?php if(isset($fase)) { echo $fase; } ?> </td>
<td colspan = 3 style = "color : red"> 
<?php if($day < $dmmax) { echo 'De datum '.$datum.' mag niet voor '.$maxdm.' liggen.';}
?>
</td>    
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php } 
        } // Einde if(isset($data))
      } ?>
</table> <!-- Einde tabel3 --> </td> </tr>
</table> <!-- Einde tabel1 -->
</form> 


</TD>
<?php    
          include "menu1.php"; }
include "select-all.js.php";
?>
</body>
</html>
