<?php

require_once("autoload.php");
$versie = '22-12-2019'; /* Kopie van Hoklijsten.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd */
$versie = "11-03-2024"; /* Bij geneste query uit
    join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
    join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
    I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
    $versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Actueel</title>
</head>
<body>
<?php
$titel = 'Schapen zonder verblijf';
$file = "Loslopers.php";
include "login.php";
?>
        <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {
    $historie_gateway = new HistorieGateway();
    $aanwezig1 = $historie_gateway->zoek_aantal_doelgroep1($lidId);
    $aanwezig2 = $historie_gateway->zoek_aantal_doelgroep2($lidId);
    $aanwezig = $aanwezig1 + $aanwezig2;
    $aanwezig3 = $historie_gateway->zoek_aantal_doelgroep3($lidId);
    $aanwezig_incl = $aanwezig + $aanwezig3;
?>
<table border = 0>
<tr>
 <td colspan = 6 style = "font-size : 15px;"> </td>
 <td><?php echo View::link_to('print pagina', 'Loslopers_pdf.php', ['style' => 'color: blue']); ?></td>
 <td> </td>
 <td rowspan = 6 width = 100 align = "center">
     <hr>
<?php
    if ($aanwezig_incl > 0) {
        Session::set("DT1", null);
        Session::set("BST", null);
        echo View::link_to('In verblijf plaatsen', 'LoslopersPlaatsen.php', ['style' => 'color: blue']);
    }
?>
 <br>
 <br>
<?php
    if (isset($aanwezig3) && $aanwezig3 > 0) {
        Session::set("DT1", null);
        Session::set("BST", null);
        echo View::link_to('Verkopen', 'LoslopersVerkopen.php', ['style' => 'color: blue']);
    } else {
?>
 <u style = "color : grey"> Verkopen </u>
<?php
    }
?>
 <br>
 <br>
 </td>
</tr>
<?php
    if ($aanwezig1 > 0) {
?>
<tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren voor spenen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>
<?php
        $schaap_gateway = new SchaapGateway();
        $schapen_geb = $schaap_gateway->schapen_geboren($lidId, $Karwerk);
?>
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
</tr>
<?php
        while ($row = $schapen_geb->fetch_array()) {
            $werknr = $row['werknr'];
            $ras = $row['ras'];
            $geslacht = $row['geslacht'];
?>
<tr align = "center">
 <td width = 80 style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $ras;?> <br> </td>
 <td width = 50 style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80 style = "font-size:15px;"><br> </td>
 <td width = 120 style = "font-size:13px;" align = "left" >
            <?php echo View::link_to('Gegevens wijzigen', 'UpdSchaap.php?pstschaap=' . $row['schaapId'], ['style' => 'color: blue']); ?>
</td>
</tr>
<?php
        }
    }
    if ($aanwezig2 > 0) {
        if ($aanwezig1 > 0) {
            $height_spn = 50;
        } else {
            $height_spn = 35;
        } /* alleen eerste blok is 35 hoog anders 50*/
?>
<tr height = <?php echo $height_spn; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal lammeren na spenen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig2;?> </b></td>
</tr>
<?php
        $schapen_spn = $schaap_gateway->schapen_speen($lidId, $Karwerk);
?>
<tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 > <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 ></th>
 <th width=60></th>
</tr>
<?php
        while ($row = $schapen_spn->fetch_array()) {
            $werknr = $row['werknr'];
            $ras = $row['ras'];
            $geslacht = $row['geslacht'];
?>
<tr align = "center">
 <td width = 80  style = "font-size:15px;"> <?php echo $werknr;?>  <br> </td>
 <td width = 80  style = "font-size:15px;"> <?php echo $ras;?> <br> </td>
 <td width = 50  style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80  style = "font-size:15px;"> <br> </td>
       <td width = 180 style = "font-size:13px;" align = "left" >
            <?php echo View::link_to('Gegevens wijzigen', 'UpdSchaap.php?pstschaap=' . $row['schaapId'], ['style' => 'color: blue', 'valign' => 'top']); ?>
       </td>
</tr>
<?php
        }
    }
?>
</tr>
<!-- Einde gespeende lammeren -->
<?php
    if ($aanwezig3 > 0) {
        if ($aanwezig1 > 0 || $aanwezig2 > 0) {
            $height_prnt = 50;
        } else {
            $height_prnt = 35;
        } /* alleen eerste blok is 35 hoog anders 50*/
?>
<tr height = <?php echo $height_prnt; ?> valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal volwassen schapen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig3;?> </b></td>
</tr>
<?php
        $schapen_vanaf_aanwas = $schaap_gateway->schapen_vanaf_aanwas($lidId, $Karwerk);
?>
<tr style = "font-size:12px;" height = 48>
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100 ><hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> </th>
 <th style = "text-align:center;" valign=bottom width= 80> </th>
 <th width=120></th>
</tr>
<?php
        while ($row = $schapen_vanaf_aanwas->fetch_array()) {
            $werknr = $row['werknr'];
            $ras = $row['ras'];
            $geslacht = $row['geslacht'];
?>
<tr align = "center">
 <td width = 80 style = "font-size:15px;"> <?php echo $werknr; ?>  <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>
 <td width = 50 style = "font-size:15px;"> <?php echo $geslacht;?> <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 100 style = "font-size:15px;">  <br> </td>
 <td width = 80 style = "font-size:15px;"> <br> </td>
 <td width = 180 style = "font-size:13px;" align = "left" >
            <?php echo View::link_to('Gegevens wijzigen', 'UpdSchaap.php?pstschaap=' . $row['schaapId'], ['style' => 'color: blue', 'valign' => 'top']); ?>
       </td>
</tr>
<?php
        }
    }
    if ($aanwezig1 == 0 && $aanwezig2 == 0 && $aanwezig3 == 0) {
?>
 <tr height = 35 valign =bottom>
 <td colspan = 6><i style = "font-size : 15px;" >Aantal schapen :  &nbsp </i><b style = "font-size:15px;"><?php echo $aanwezig1;?> </b></td>
</tr>
    <tr style = "font-size:12px;">
 <th style = "text-align:center;" valign=bottom width= 80 > Werknr<hr></th>
 <th style = "text-align:center;" valign=bottom width= 80 > Ras<hr></th>
 <th style = "text-align:center;" valign=bottom width= 50 > Geslacht<hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 100> <hr></th>
 <th style = "text-align:center;" valign=bottom width= 120> <hr></th>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>
<tr> <td height = 25></td>
</tr>
<?php
    }
?>
</tr>
</table>
    </TD>
<?php
    include "menu1.php";
}
?>
</tr>
</body>
</html>
