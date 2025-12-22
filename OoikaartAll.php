<?php

require_once("autoload.php");

/* 6-5-2014 : Kolom 'aantal dagen moeder'moeder verwijderd
        query verwijderd omdat het gebruik ervan onbekend is en variabelen worden niet gebruikt :

4-8-2014 werknr variabel gemaakt
11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '26-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '8-12-2016';  /* actId = 1 uit on clause gehaald en als sub query genest */
$versie = '10-3-2017';  /* join tblRas gewijzigd naar left join tblRas */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" align = "center"> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Ooikaart';
$file = "OoikaartAll.php";
include "login.php"; ?>

        <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
?>
<form action= "OoikaartAll.php" method="post">
<table border = 0 id="myTable2">    
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th width = 1 height = 30></th>
<th onclick="sortTable(2)" style = "text-align:center;"valign= bottom width= 80><u>Levensnummer</u><hr></th>
<th width = 1></th>
<th onclick="sortTable(3)" style = "text-align:center;"valign= bottom width= 80><u>Werknr</u><hr></th>
<th width = 1></th>
<th onclick="sortTable(6)" style = "text-align:center;"valign= bottom width= 280>Ras<hr></th>
<th width = 1></th>
<th onclick="sortTable(7)" style = "text-align:center;"valign= bottom >Geboortedatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal lammeren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal levend geboren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>% levend geboren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal ooien<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal rammen<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Gem geboorte gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 50>Gespeend<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem speen gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem groei<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 50>Afgeleverd<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem aflever gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem groei<hr></th>
<th width = 60></th>
<th style = "text-align:center;"valign= bottom width= 80></th>
<th width = 600></th>
 </tr>
<?php
        $schaap_gateway = new SchaapGateway();
        $result = $schaap_gateway->ooikaart_all($lidId, $Karwerk);
        while ($row = mysqli_fetch_assoc($result)) {
            $schaapId = $row['schaapId'];
            $levnr = $row['levensnummer'];
            $werknr = $row['werknr'];
            $ras = $row['ras'];
            $dmgeb = $row['dmgebrn'];
            $gebdm = $row['geb_datum'];
            $lammeren = $row['lammeren'];
            $levend = $row['levend'];
            $percleven = $row['percleven'];
            $aantooi = $row['aantooi'];
            $aantram = $row['aantram'];
            $gemkg = $row['gemgewicht'];
            $aantspn = $row['aantspn'];
            $percspn = $row['percspn'];
            $gemspn = $row['gemspnkg'];
            $gemgr_spn = $row['gemgr_spn'];
            $aantafl = $row['aantafv'];
            $gemafl = $row['gemafvkg'];
            $gemgr_afv = $row['gemgr_afv'];
?>
<tr align = "center">    
 <td width = 0> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levnr; ?> <br> </td>
 <td width = 1 style = "font-size:0px;"> <?php echo $werknr; ?> </td>   
 <td width = 100 style = "font-size:14px;">
<?php echo View::link_to($werknr, 'Ooikaart.php?pstId='.$schaapId, ['style' => 'color: blue']); ?>
<br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $ras; ?> <br> </td>
 <td width = 1   style = "font-size:0px;"> <?php echo $dmgeb; ?> </td>              
 <td width = 100 style = "font-size:12px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 1> </td>         
 <td width = 100 style = "font-size:14px;"> <?php echo $lammeren; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levend; ?> <br> </td>
 <td width = 1> </td>    
 <td width = 100 style = "font-size:14px;"> <?php echo $percleven; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $aantooi; ?> <br> </td>
 <td width = 1> </td>    
 <td width = 100 style = "font-size:14px;"> <?php echo $aantram; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $gemkg; ?> <br> </td>    
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantspn; ?> <br> </td>
 <td width = 1> </td>    
 <td width = 100 style = "font-size:12px;"> <?php echo $gemspn; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $gemgr_spn; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantafl; ?> <br> </td>
 <td width = 1> </td>    
 <td width = 100 style = "font-size:12px;"> <?php echo $gemafl; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $gemgr_afv; ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:13px;" >
<?php
        }
?>            
 </td> 
</tr>
</table>
</form>
</TD>
<?php
    } else {
?>
        <img src='ooikaartAll_php.jpg'  width='950' height='500'/>
<?php
    }
    include "menuRapport1.php";
}
include "table-sort.js.php";
?>
</tr>
</table>
</body>
</html>
