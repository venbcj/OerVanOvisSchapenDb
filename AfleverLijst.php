<?php

require_once("autoload.php");

/* 9-8-2014 : werknr variabel gemaakt zie $Karwerk en quotes bij "$datum" en "$aantal" weggehaald 
1-3-2015 : login toegevoegd 
19-12-2015 : Uitval toegevoegd */
$versie = '08-01-2017'; /* LidId = 1 variabel gemaankt naar lidId = ".mysqli_real_escape_string($db,$lidId)." */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '05-07-2020'; /* wdgn gewijzigd in wdgn_v */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$versie = '07-07-2024'; /* Werknr oplopend gesorteerd */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '19-03-2025'; /* Gewicht toegevoegd en exporteren naar excel mogelijk gemaakt */
 Session::start();
  ?>
<!DOCTYPE html>
<html>
<head>
<title>Afleverlijst</title>
</head>
<body>

<?php
$titel = 'Afleverlijst';
$file = "ZoekAfldm.php";
include "login.php";
?>
        <TD align = "center" valign = "top">
<?php 
if (Auth::is_logged_in()) {
    if (empty($schaap_gateway)) {
$schaap_gateway = new SchaapGateway();
    }
//include vw_Voeding

$hisId = $_POST['kzlPost'] ?? ''; // kzlPost bestaat in ZoekAfldm.php 

[$date, $bestm] = $schaap_gateway->zoek_datum_bestemming($hisId);

/*Telt aantal schapen per bestemming/afleverdatum*/
[$bestemming, $datum, $aantal] = $schaap_gateway->zoek_aflevergegevens($bestm, $date);
?>
<table id="schaapdetails" border="0">
<tr>
<td></td>
<td>    
<tr>
 <td></td> 
 <td colspan = 10 align =center>
     <a href= '<?php echo $url;?>AfleverLijst_pdf.php?hisId=<?php echo $hisId; ?>' style = 'color : blue'>
    print pagina </a>
 </td> 
 <td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Bestemming :</i></td> 
 <td colspan = 4><b style = \"font-size:15px;\"><?php echo $bestemming; ?> </b></td>
</tr>

<tr >
<td></td> 
<td colspan = 10></td> 
<td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Afleverdatum :</i></td> 
<td colspan = 2><b style = \"font-size:15px;\"><?php echo $datum; ?> </b></td>
</tr>

<tr >
<td></td> 
<td colspan = 10></td> 
<td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Aantal schapen :</i></td> 
<td colspan = 2><b style = \"font-size:15px;\"><?php echo $aantal; ?> </b></td>
</tr>

<tr style = \"font-size:12px;\">
<th width = 0 height = 30></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Levensnummer<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Werknummer<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Gewicht<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 90>Medicijn<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 120>Datum toepassing<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Wachtdagen<hr></th>
<th width = 80 ></th>
<td colspan = 2 ><a href="exportAfleverlijst.php?pst=<?php echo $lidId; ?>&best=<?php echo $bestm; ?>&date=<?php echo $date; ?>"> Export-xlsx </a></td>

<?php
$zoek_schaap = $schaap_gateway->zoek_schaap_aflever($bestm, $date, $Karwerk);
while ($zs = $zoek_schaap->fetch_assoc()) {
    $levnr = $zs['levensnummer'];
    if(!isset($levnr)) {
        $levnr = 'Geen'; 
    } 
            $werknr = $zs['werknr'];
            $schaapId = $zs['schaapId'];
            $kg = $zs['kg'];
?>
<tr align = center>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $kg; ?> <br> </td>
    <td colspan = 6><table border = 0>
<?php
            $zoek_pil = $schaap_gateway->zoek_pil_aflever($lidId, $schaapId);
$vandaag = date('Y-m-d');
        while($row = mysqli_fetch_array($zoek_pil)) {
            If (!empty($row['datum'])) {
?>
<tr align = center>
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;" align = "left"> <?php echo $row['naam']; ?> <br> </td>
 <td width = 1> </td>            
 <td width = 120 style = "font-size:15px;"> <?php echo $row['datum']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $row['wdgn_v']; ?> <br> </td> 
 <td width = 1> </td>       
 </tr>
<?php
            }
        }
?>
    </table></td>
<?php
}
?>
</tr>                
</table>
        </TD>
<?php
include "menuRapport.php";
}
?>
</body>
</html>
