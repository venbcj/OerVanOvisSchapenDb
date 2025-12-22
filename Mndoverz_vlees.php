<?php

require_once("autoload.php");

/* 16-3-2014 Maandoverzicht wordt ovv Rina per jaar gekozen en getoond.
 11-10-2014 : Maanden gewijigd van cijfers naar omschrijving
11-3-2015 : Login toegevoegd */
$versie = "22-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe tblDoel        22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '25-2-2017'/* Maandoverzicht worden getoond vanaf begin van gebruik programma.     3-3-2017 : Geldt enkel voor productieomgeving !!! */;
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '16-11-2019'; /* Hoeveelheid voer per maand opnieuw gebouwd i.v.m. andere manier van kg voer vastleggen */
$versie = '27-03-2022'; /* Detail uitval voor spenen toegevoegd en sql beveiligd met quotes */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld bij tblHistorie en ook sub-queries gespeenden en afgeleverder herschreven */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Maandoverzicht vleeslammeren';
$file = "Mndoverz_vlees.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) { 

    $kzlJaar = '';
if (isset($_GET['jaar'])) { $kzlJaar = $_GET['jaar']; }    elseif (isset($_POST['kzlJaar'])) { $kzlJaar = $_POST['kzlJaar']; }
if (isset($_GET['maand'])) { $keuze_mnd = $_GET['maand']; } 

$label = "Kies een jaartal &nbsp " ;
If (isset($kzlJaar)) { unset($label); } ?>

<table Border = 0 align = "center">
<?php
$zoek_startjaar_user = mysqli_query($db,"
SELECT date_format(min(dmcreatie),'%Y') jaar 
FROM tblStal
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));
    while($jr1 = mysqli_fetch_array($zoek_startjaar_user)) { $jaar1 = $jr1['jaar']; }
    
$jaarstart = date("Y")-3; if($jaar1 > $jaarstart && $dtb == "bvdvSchapenDb") { $jaarstart = $jaar1; }// Alleen in productieomg rapport tonen vanaf startjaar user
$kzl = mysqli_query($db,"
SELECT date_format(h.datum,'%Y') jaar 
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(datum,'%Y') >= '$jaarstart' and h.actId = 4 and h.skip = 0
GROUP BY date_format(datum,'%Y')
ORDER BY date_format(datum,'%Y') desc 
") or die (mysqli_error($db));
?>
<form action = "Mndoverz_vlees.php" method = "post">
<tr>
 <td> </td>
 <td> <?php
if(isset($label)) { echo $label; }
//Jaar selecteren
$kzlId = $kzlJaar;
$name = "kzlJaar";
$width= 100 ; ?>
<select name=<?php echo"$name";?> style="width:<? echo "$width";?>;\" >
 <option></option>
<?php        while($row = mysqli_fetch_array($kzl))
        {
$kzlkey= $row['jaar'];
$kzlvalue= $row['jaar'];

include "kzl.php";
        }
// EINDE Jaar selecteren
?>
</select> 
 </td>
 <td> </td>
 
 <td> <input type = "submit" name ="knpToon" value = "Toon"> </td></tr>    
</form>
<tr>
 <td> </td>

<td>
<?php
if (isset($kzlJaar)) {    

    $mndnaam = array('','januari', 'februari', 'maart','april','mei','juni','juli','augustus','september','oktober','november','december'); 

$result = mysqli_query($db,"
SELECT jrmnd jaarmnd, jaar, maand, speenat, afvat, doodat, Perc_naopleg, round(daggroei,2) gemgroei, round(voer,2) voer
FROM (
    SELECT aant.jrmnd, aant.maand, aant.jaar, aant.speenat, aant.afvat, 
     naopleg.doodat, round((naopleg.doodat/aant.speenat*100),2) perc_naopleg, 
     groei.gemgroeidag daggroei,
     kgvoer.nutat_mnd voer
    FROM (
        SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, year(h.datum) jaar, count(h.hisId) speenat, count(haf.hisId) afvat
        FROM tblHistorie h
         join tblStal st on (st.stalId = h.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
         left join (
            SELECT h.stalId, h.hisId
            FROM tblHistorie h
            WHERE h.actId = 12 and h.skip = 0
         ) haf on (st.stalId = haf.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 4 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
        GROUP BY Month(h.datum), year(h.datum)
     ) aant
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, Month(h.datum) maand, Year(h.datum) jaar, count(distinct s.schaapId) doodat
        FROM tblSchaap s
         join tblStal st on (s.schaapId = st.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
         join tblHistorie ho on (st.stalId = ho.stalId and ho.actId = 14)
         join tblHistorie hs on (st.stalId = hs.stalId and hs.actId = 4)
         left join tblHistorie ha on (st.stalId = ha.stalId and ha.actId = 3)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 4 and h.skip = 0 and isnull(ha.actId) and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
        GROUP BY month(h.datum), Year(h.datum)    
     ) naopleg on (aant.jrmnd = naopleg.jrmnd)
    left join (
        SELECT date_format(h.datum,'%Y%m') jrmnd, sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) groeidag, count(distinct st.schaapId), 
        sum((haf.kg -  h.kg)*1000/ DATEDIFF(haf.datum, h.datum)) / count(st.schaapId) gemgroeidag
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (st.stalId = h.stalId and h.actId = 4)
         join (
            SELECT h.stalId, h.kg, h.datum
            FROM tblHistorie h
            WHERE h.actId = 12 and h.skip = 0
         ) haf on (st.stalId = haf.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
        GROUP BY Month(h.datum), Year(h.datum)
     ) groei on (aant.jrmnd = groei.jrmnd)
     left join (
        SELECT gesp_jrmnd, sum(nutat_peri_mnd) nutat_mnd
        FROM (
            SELECT date_format(spn.datum,'%Y%m') gesp_jrmnd, vantot.periId, dgperi.dgn_periId,
             sum(datediff(tot.datum,van.datum)) dgn,
             sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*100 perc_dgn,
             v.nutat,
             sum(datediff(tot.datum,van.datum))/dgperi.dgn_periId*v.nutat nutat_peri_mnd
            FROM (
                SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
                FROM tblBezet b
                 join tblHistorie h1 on (b.hisId = h1.hisId)
                 join tblActie a1 on (a1.actId = h1.actId)
                 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                 join tblActie a2 on (a2.actId = h2.actId)
                 join tblStal st on (h1.stalId = st.stalId)
                 join tblPeriode p on (b.periId = p.periId)
                 
                WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                 and p.doelId = 2 and year(h1.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
                GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
            ) vantot
             join (
                SELECT vantot.periId, sum(datediff(tot.datum,van.datum)) dgn_periId
                FROM (
                    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist, b.periId, h1.actId
                    FROM tblBezet b
                     join tblHistorie h1 on (b.hisId = h1.hisId)
                     join tblActie a1 on (a1.actId = h1.actId)
                     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
                     join tblActie a2 on (a2.actId = h2.actId)
                     join tblStal st on (h1.stalId = st.stalId)
                     join tblPeriode p on (b.periId = p.periId)
                     
                    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
                     and p.doelId = 2
                    GROUP BY b.bezId, st.schaapId, h1.hisId, h1.actId
                ) vantot
                 join tblHistorie van on (van.hisId = vantot.hisv)
                 join tblHistorie tot on (tot.hisId = vantot.hist)
                GROUP BY vantot.periId
             ) dgperi on (vantot.periId = dgperi.periId)
             join tblHistorie van on (van.hisId = vantot.hisv)
             join tblHistorie tot on (tot.hisId = vantot.hist)
             join tblVoeding v on (v.periId = vantot.periId)
             
             join tblStal st on (st.schaapId = vantot.schaapId)
             join (
                SELECT h.stalId, h.datum
                FROM tblHistorie h
                WHERE h.actId = 4 and h.skip = 0
             ) spn on (spn.stalId = st.stalId)
            GROUP BY date_format(spn.datum,'%Y%m'), vantot.periId, dgperi.dgn_periId, v.nutat
        ) vr_mnd
        GROUP BY gesp_jrmnd
     ) kgvoer on (aant.jrmnd = kgvoer.gesp_jrmnd)
) mv
ORDER BY jaarmnd desc
") or die (mysqli_error($db));
 ?>

<tr style = "font-size:18px;" align = "center">
 <td colspan = 1></td>
 <td><b>Jaar <?php echo $kzlJaar; ?> </b></td>
</tr>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <!--<th style = \"text-align:center;\"valign=\"bottom\";width= \"60\"></th>
 <th width = \"1\"></th>-->
 <th style = "text-align:center;"valign="bottom";width= 100>Speenmaand<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Aantal na opleg<hr></th>
 <th width = 1></th>

 <th style = "text-align:center;"valign="bottom";width= 80>uitval na opleg<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>% uitval na opleg<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Afgeleverd<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Gem Groei <hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Voer in kg <hr></th>
 <th width = 1></th>
 <th width=60></th>
</tr>

<?php
        while($row = mysqli_fetch_array($result))/*    $row zorgt voor de waardes per maand     */
        { $mndnr = $row['maand'];

// Kg voer per maand
$kg_per_maand = "
SELECT dagen_per_speenjaarmaand.jaarmaand, round(sum(dagen_per_speenjaarmaand.dgn*Kg_per_dag_per_periode.kgDag),2) kgMnd
FROM (

    SELECT p.periId, nutat/sum(dgn) kgDag
    FROM (

        SELECT p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
        FROM tblPeriode p
         join tblHok ho on (p.hokId = ho.hokId)
         join tblLeden l on (ho.lidId = l.lidId)
        WHERE doelId = 2 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01')
        union

        SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
        FROM tblPeriode p1
         join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
         join tblHok ho on (p1.hokId = ho.hokId)
        WHERE p1.doelId = 2 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY p2.periId, p2.hokId, p2.dmafsluit
     ) p
     left join (
         SELECT p.periId, sum(nutat) nutat
         FROM tblVoeding v
          join tblPeriode p on (v.periId = p.periId)
          join tblHok ho on (ho.hokId = p.hokId)
         WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
         GROUP BY p.periId
     ) v on (p.periId = v.periId)
     join (
        SELECT b.hokId, st.schaapId, hv.datum schpIn, ht.datum schpUit, datediff(coalesce(ht.datum,CURDATE()),hv.datum) dgn
        FROM tblBezet b
         join tblHistorie hv on (b.hisId = hv.hisId)
         join tblStal st on (hv.stalId = st.stalId)
         left join (
            SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
            WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
            GROUP BY b.bezId, st.schaapId, h1.hisId
         ) uit on (uit.hisv = b.hisId)
         left join tblHistorie ht on (uit.hist = ht.hisId)
        WHERE hv.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) s on (p.hokId = s.hokId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) spn on (spn.schaapId = s.schaapId)
      left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) prn on (prn.schaapId = s.schaapId)

    WHERE schpIn < pEind and schpUit > pStart and schpIn >= spn.datum and (schpIn < prn.datum or isnull(prn.schaapId))

    GROUP BY p.periId, v.nutat

 ) Kg_per_dag_per_periode

 join 

 (
    SELECT p.periId, date_format(spn.datum,'%Y%m') jaarmaand, sum(s.dgn) dgn
    FROM (

        SELECT p.periId, p.hokId, date_format(p.dmcreate,'%Y-%m-01') pStart, min(p.dmafsluit) pEind
        FROM tblPeriode p
         join tblHok ho on (p.hokId = ho.hokId)
         join tblLeden l on (ho.lidId = l.lidId)
        WHERE doelId = 2 and l.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY p.periId, p.hokId
        union

        SELECT p2.periId, p2.hokId, max(p1.dmafsluit) pStart, p2.dmafsluit pEind
        FROM tblPeriode p1
         join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
         join tblHok ho on (p1.hokId = ho.hokId)
        WHERE p1.doelId = 2 and ho.lidId = '".mysqli_real_escape_string($db,$lidId)."'
        GROUP BY p2.periId, p2.hokId, p2.dmafsluit
     ) p
     join (
        SELECT b.hokId, st.schaapId, hv.datum schpIn, ht.datum schpUit, datediff(coalesce(ht.datum,CURDATE()),hv.datum) dgn
        FROM tblBezet b
         join tblHistorie hv on (b.hisId = hv.hisId)
         join tblStal st on (hv.stalId = st.stalId)
         left join (
            SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
            FROM tblBezet b
             join tblHistorie h1 on (b.hisId = h1.hisId)
             join tblActie a1 on (a1.actId = h1.actId)
             join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
             join tblActie a2 on (a2.actId = h2.actId)
             join tblStal st on (h1.stalId = st.stalId)
            WHERE a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
            GROUP BY b.bezId, st.schaapId, h1.hisId
         ) uit on (uit.hisv = b.hisId)
         left join tblHistorie ht on (uit.hist = ht.hisId)
        WHERE hv.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) s on (p.hokId = s.hokId)
     join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 4 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and date_format(h.datum,'%Y') = '".mysqli_real_escape_string($db,$kzlJaar)."' and Month(h.datum) = '".mysqli_real_escape_string($db,$mndnr)."'
     ) spn on (spn.schaapId = s.schaapId)
     left join (
        SELECT st.schaapId, h.datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 3 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
     ) prn on (prn.schaapId = s.schaapId)

    WHERE schpIn < pEind and schpUit > pStart and schpIn >= spn.datum and (schpIn < prn.datum or isnull(prn.schaapId))

    GROUP BY p.periId, date_format(spn.datum,'%Y%m')

 ) dagen_per_speenjaarmaand on (Kg_per_dag_per_periode.periId = dagen_per_speenjaarmaand.periId)

 GROUP BY dagen_per_speenjaarmaand.jaarmaand
";

#echo $kg_per_maand.'<br><br><br>';

$kg_per_maand = mysqli_query($db,$kg_per_maand) or die (mysqli_error($db));

while($kgd = mysqli_fetch_array($kg_per_maand)) { $mndkg = $kgd['kgMnd']; }
// Einde Kg voer per Maand
?>        
<tr align = "center">
 <td width = 0> </td>       
 <td width = 100 style = "font-size:15px;" align = "right"> <?php echo $mndnaam[$mndnr]; ?> <br> </td>    
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['speenat']; ?> <br> </td>
<?php    if(isset($totSpeen)) {$totSpeen = $totSpeen+$row['speenat']; } else { $totSpeen = $row['speenat'] ?? 0; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;">

<?php echo View::link_to($row['doodat'], 'Mndoverz_vlees.php?jaar='.$kzlJaar.'&maand='.$mndnr, ['style' => 'color: blue']); ?>

 <br> </td>
<?php    if(isset($totDood)) {$totDood = $totDood+$row['doodat']; } else { $totDood = $row['doodat']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['Perc_naopleg']; ?> <br> </td>
<?php    if(isset($totOpleg)) {$totOpleg = $totOpleg+$row['Perc_naopleg']; } else { $totOpleg = $row['Perc_naopleg']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['afvat']; ?> <br> </td>
<?php    if(isset($totAfv)) {$totAfv = $totAfv+$row['afvat']; } else { $totAfv = $row['afvat']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['gemgroei']; ?> <br> </td>
<?php    if(isset($totGroei)) {$totGroei = $totGroei+$row['gemgroei']; } else { $totGroei = $row['gemgroei']; } ?>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $mndkg; ?> <br> </td>
<?php    if(isset($totKg)) {$totKg = $totKg+$mndkg; } else { $totKg = $mndkg; } unset($mndkg); ?>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>                
<?php        } 
        

// totalen ?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> Totaal <?php echo $kzlJaar; ?> </b><br> </td>       
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totSpeen ?? 0; ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totDood ?? 0; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totAfv ?? 0; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php echo $totKg ?? 0; ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> <?php
// EINDE totalen

// Gemiddelden 
$zoek_aantal_maanden = mysqli_query($db,"
SELECT count(distinct(month(h.datum))) mndat
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.actId = 4 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."'
") or die (mysqli_error($db));
    while($rij = mysqli_fetch_array($zoek_aantal_maanden)) { $mndat = $rij['mndat']; }

if($mndat > 0)    { ?>
<tr align = "center"  style = "font-size:13px;">
 <td width = 0> </td>
 <td width = 100>  Gem <?php echo $mndat; ?>Mnd </td>       
 <td width = 1> </td>
 <td width = 100> <?php $gemSpeen = round($totSpeen/$mndat,2); if($gemSpeen>0) { echo $gemSpeen; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemDood = round($totDood/$mndat,2); if($gemDood>0) { echo $gemDood; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemOpleg = round($totOpleg/$mndat,2); if($gemOpleg>0) { echo $gemOpleg; } ?> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemAfv = round($totAfv/$mndat,2); if($gemAfv>0) { echo $gemAfv; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemGroei = round($totGroei/$mndat,2); if($gemGroei>0) { echo $gemGroei; } ?> </td>
 <td width = 1> </td>
 <td width = 100> <?php $gemKg = round($totKg/$mndat,2); if($gemKg>0) { echo $gemKg; } ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> 

<?php }
// EINDE Gemiddelden 

} //  Einde knop toon 

/*****************************/
// DETAILS UITVAL NA OPLEG
/*****************************/

if(isset($keuze_mnd)) { ?>

<tr>
 <td colspan = 50 align="center">

<table>
<tr height = "50">
 <td></td>
</tr>
<tr style = "font-size:13px;" align="center">
 <td colspan="10"><h3>Detail uitval na opleg</h3></td>
</tr>

<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;" valign= "bottom" width= 1>Werknr <hr></th>
 <th style = "text-align:center;"valign= "bottom";width= 80> Gespeend <hr></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Uitvaldatum<hr></th>
 <th style = "text-align:center;"valign="bottom"width= 80>Reden<hr></th>
 <th style = "text-align:center;"valign= "bottom" width= 80>Meldnr RVO<hr></th>
</tr>

<?php 

$zoek_overleden_schapen = mysqli_query($db,"
SELECT right(s.levensnummer, $Karwerk) werknr, date_format(h.datum,'%d-%m-%Y') speendm, date_format(dood.datum,'%d-%m-%Y') uitvdm, r.reden, meld.meldnr

FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 left join tblReden r on (r.redId = s.redId)
 join(
     SELECT st.schaapId, datum
     FROM tblStal st
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE h.actId = 14 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) dood on (dood.schaapId = s.schaapId)
 left join(
     SELECT rs.levensnummer, rs.meldnr
     FROM impRespons rs
     WHERE rs.meldnr is not null and rs.melding = 'DOO'
 ) meld on (meld.levensnummer = s.levensnummer)
WHERE s.levensnummer is not null and h.actId = 4 and h.skip = 0 and year(h.datum) = '".mysqli_real_escape_string($db,$kzlJaar)."' and month(h.datum) = '".mysqli_real_escape_string($db,$keuze_mnd)."' and st.lidId = '".mysqli_real_escape_string($db,$lidId)."'
GROUP BY s.schaapId, st.stalId
") or die (mysqli_error($db));
    while($zos = mysqli_fetch_array($zoek_overleden_schapen)) {

    $werknr = $zos['werknr'];
    $speendm = $zos['speendm'];
    $uitvdm = $zos['uitvdm'];
    $reden = $zos['reden'];
    $meldnr = $zos['meldnr']; ?>


<tr style = "font-size:12px;" align="center">
 <td></td>
 <td><?php echo $werknr; ?></td>
 <td><?php echo $speendm; ?></td>
 <td><?php echo $uitvdm; ?></td>
 <td><?php echo $reden; ?></td>
 <td><?php echo $meldnr; ?></td>
 
</tr>


<?php
} ?>
</table>

 </td>
</tr>

<?php
}
/***********************************/
// Einde DETAILS UITVAL  NA OPLEG
/***********************************/
?>



</table>
        </TD>
<?php } else { ?> <img src='mndoverz_vlees_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>
</body>
</html>
