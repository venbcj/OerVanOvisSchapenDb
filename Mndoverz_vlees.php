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
if (Auth::is_logged_in()) { if ($modtech ==1) {
    $Periode_gateway = new PeriodeGateway();
    $Historie_gateway = new HistorieGateway();
    $Stal_gateway = new StalGateway();

    $kzlJaar = '';
    if (isset($_GET['jaar'])) { $kzlJaar = $_GET['jaar']; }    elseif (isset($_POST['kzlJaar'])) { $kzlJaar = $_POST['kzlJaar']; }
    if (isset($_GET['maand'])) { $keuze_mnd = $_GET['maand']; }
    $label = "Kies een jaartal &nbsp " ;
    If (isset($kzlJaar)) { unset($label); }
    ?>

<table Border = 0 align = "center">
<?php 
    $jaar1 = $Stal_gateway->zoek_startjaar_user($lidId);
    $jaarstart = date("Y")-3;
    if($jaar1 > $jaarstart && $dtb == "bvdvSchapenDb") { $jaarstart = $jaar1; }
    $kzl = $Historie_gateway->kzlJaar($lidId, $jaarstart);
    ?>
<form action = "Mndoverz_vlees.php" method = "post">
<tr>
 <td> </td>
 <td> <?php 
    if(isset($label)) { echo $label; }
    //Jaar selecteren
    $kzlId = $kzlJaar;
    $name = "kzlJaar";
    $width= 100 ;
    ?>
<select name=<?php 
    echo"$name";
    ?> style="width:<? echo "$width";?>;\" >
 <option></option>
<?php 
    while($row = mysqli_fetch_array($kzl))
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
        $result = $Historie_gateway->waardes_per_maand($lidId, $kzlJaar);
        ?>

<tr style = "font-size:18px;" align = "center">
 <td colspan = 1></td>
 <td><b>Jaar <?php 
        echo $kzlJaar;
        ?> </b></td>
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
        while($row = mysqli_fetch_array($result)) {
            /*    $row zorgt voor de waardes per maand     */
            $mndnr = $row['maand'];
            $mndkg = $Periode_gateway->kg_per_maand($lidId, $kzlJaar, $mndnr);
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
<?php        
        }
        // totalen 
        ?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> Totaal <?php 
        echo $kzlJaar;
        ?> </b><br> </td>       
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php 
        echo $totSpeen ?? 0;
        ?> </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php 
        echo $totDood ?? 0;
        ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php 
        echo $totAfv ?? 0;
        ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b>  </b><br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <hr /><b> <?php 
        echo $totKg ?? 0;
        ?>  </b><br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr> <?php 
        $mndat = $Historie_gateway->zoek_aantal_maanden($lidId, $kzlJaar);
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
    }
    //  Einde knop toon 
    /*****************************/
    // DETAILS UITVAL NA OPLEG
    /*****************************/
    if (isset($keuze_mnd)) {
        ?>

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
        $zoek_overleden_schapen = $Historie_gateway->zoek_overleden_schapen($Karwerk, $lidId, $kzlJaar, $keuze_mnd);
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
    }
        ?>
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
<?php 
} else { ?> <img src='mndoverz_vlees_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>
</body>
</html>
