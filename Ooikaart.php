<?php

require_once("autoload.php");

/* 8-8-2014 Aantal karakters werknr variabel gemaakt
11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '30-11-2016';  /* actId = 3 aan schaapId gekoppeld i.p.v. een stalId */
$versie = '2-4-2017';  /* ras niet verplicht gemaakt => left join tblRas */
$versie = '5-5-2017';  /* Aantal lammeren gebasseerd op eigen lidId */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '12-12-2018'; /* Van het aantal lammeren worden alleen die met geboortedatum geteld. Aanvoer dieren van moeder dus niet */
$versie = '4-4-2020'; /* halsnrs in keuzelijst alleen van dieren op stallijst */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = 'top' align = 'center'> gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>
<?php
$titel = 'Ooikaart per moederdier';
$file = "Ooikaart.php";
include "login.php";
?>
        <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        if (isset($_GET['pstId'])) {
            $raak = $_GET['pstId'];
        }
?>
    <table border = 0>
<?php
        if (empty($_POST['kzllevnr'])) {
            $kzlLevnr = '';
        } else {
            $kzlLevnr = $_POST['kzllevnr'];
        }
        if (empty($_POST['kzlwerknr'])) {
            if (isset($raak)) {
                $kzlWerknr = $raak;
            } else {
                $kzlWerknr = '';
            }
        } else {
            $kzlWerknr = $_POST['kzlwerknr'];
        }
        if (empty($_POST['kzlHalsnr'])) {
            $kzlHalsnr = '';
        } else {
            $kzlHalsnr = $_POST['kzlHalsnr'];
        }
        /* Keuze ooi kan op basis van levensnummer, werknr en/of halsnr
        Onderstaande werkt het volgende uit:
        Alle keuzes worden bij elkaar opgeteld en gedeeld door het aantal ingevulde keuze velden
        Elk van de gevulde keuze velden moet gelijk zijn aan het resultaat van de deling.
        Dat is   $gekozen_ooi  !!! */
        if (!empty($kzlLevnr)) {
            $schaap_gateway = new SchaapGateway();
            $mdrId_obv_levnr = $schaap_gateway->zoek_moeder_ooikaart($kzlLevnr);
            $deel = 1;
        } else {
            $mdrId_obv_levnr = 0;
            $deel = 0;
        }
        if (!empty($kzlWerknr)) {
            $schaap_gateway = new SchaapGateway();
            $mdrId_obv_werknr = $schaap_gateway->zoek_moeder_werknr($kzlWerknr);
            $deel++ ;
        } else {
            $mdrId_obv_werknr = 0;
        }
        if (!empty($kzlHalsnr)) {
            $schaap_gateway = new SchaapGateway();
            $mdrId_obv_halsnr = $schaap_gateway->zoek_moeder_halsnr($kzlHalsnr);
            $deel++ ;
        } else {
            $mdrId_obv_halsnr = 0;
        }
        if ($deel > 0) {
            $mdrId_obv_keuze = ($mdrId_obv_levnr + $mdrId_obv_werknr + $mdrId_obv_halsnr) / $deel;
        }
        if (isset($mdrId_obv_keuze) && ($mdrId_obv_keuze == $mdrId_obv_levnr || $mdrId_obv_keuze == $mdrId_obv_werknr || $mdrId_obv_keuze == $mdrId_obv_halsnr)) {
            $gekozen_ooi = $mdrId_obv_keuze;
        }
        /* Einde  Keuze ooi kan op basis van levensnummer, werknr en/of halsnr */
?>
<tr align = "center" valign = 'top' ><td colspan = 35>    <table border = 0 id="schapen">
<tr>
<td width="150"> </td>    
<td colspan = 3><i><sub> Levensnummer </sub></i> </td>
<td> </td>    
<td colspan = 3><i><sub> Werknr </sub></i> </td>
<td> </td>
<td colspan = 3><i><sub> Halsnr </sub></i> </td>
<td width="150"> </td>
<td>
<?php
        echo View::link_to('print pagina', 'Ooikaart_pdf.php?Id=' . ($raak ?? $gekozen_ooi ?? ''), ['style' => 'color: blue']);
?>
</td>
</tr>
<tr>
<td> </td>
<form action= "Ooikaart.php" method= "post"> 
<td colspan = 3>
<?php
        $schaap_gateway = new SchaapGateway();
        $kzl = $schaap_gateway->kzl_ooikaart($lidId);
?>
 <select name= "kzllevnr" style= "width:120;" >
 <option> </option>     
<?php
        while ($row = $kzl->fetch_array()) {
            $opties = array($row['schaapId'] => $row['levensnummer']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if (isset($_POST['kzllevnr']) && $_POST['kzllevnr'] == $key) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
?>
    </select>
    </td>
    <td> </td>
<td colspan = 3>
<?php
        //Keuzelijst werknr
        $width = 25 + (8 * $Karwerk) ;
        $schaap_gateway = new SchaapGateway();
        $kzl = $schaap_gateway->kzl_werknr($Karwerk, $lidId);
?>
    <select name= "kzlwerknr" style= "width:<?php
        echo $width;
?>;" >
<option> </option>
<?php
        while ($row = $kzl->fetch_array()) {
            $opties = array($row['schaapId'] => $row['werknr']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if ((isset($_POST['kzlwerknr']) && $_POST['kzlwerknr'] == $key) || (isset($raak) && $raak == $key)) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
?>
    </select>
    </td>
<td> </td>
<!-- kzlHalsnr -->
<td>
<?php
        $schaap_gateway = new SchaapGateway();
        $zoek_halsnr = $schaap_gateway->kzl_halsnr($lidId);
?>
 <select name="kzlHalsnr" style= "width: 80;" >
 <option></option>
<?php
        while ($row = $zoek_halsnr->fetch_array()) {
            $opties = array($row['schaapId'] => $row['halsnr']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if (isset($_POST['kzlHalsnr']) && $_POST['kzlHalsnr'] == $key) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
?>
 </select>
</td>
<!-- Einde kzlHalsnr -->
</tr>
<tr>
<td colspan = 14 align = "center">
<input type = "submit" name="knpToon" value = "toon">
</td>
</tr>
</form>    
</table>        </td></tr>
<?php
        if (isset($gekozen_ooi)) {
            $schaap_gateway = new SchaapGateway();
            $result_mdr = $schaap_gateway->result_mdr($Karwerk, $lidId, $gekozen_ooi);
            while ($row = $result_mdr->fetch_assoc()) {
                $levnr = $row['levensnummer'];
                $werknr = $row['werknr'];
                $ras = $row['ras'];
                $gebdm = $row['geb_datum'];
                $aanvdm = $row['aanvoerdm'];
                if (isset($gebdm)) {
                    $opdm = $gebdm;
                } else {
                    $opdm = $aanvdm;
                }
                $dagen = $row['dagen'];
                $lammeren = $row['lammeren'];
                $levend = $row['levend'];
                $percleven = $row['percleven'];
                $aantooi = $row['aantooi'];
                $aantram = $row['aantram'];
                $gemkg = $row['gemgewicht'];
                $aantspn = $row['aantspn'];
                $gemspn = $row['gemspnkg'];
                $aantafl = $row['aantafv'];
                $gemafl = $row['gemafvkg'];
                /*    Gegevens tbv MOEDERDIER        */


?>
            <tr><td colspan = 6 align = "center"><h3>moederdier</td></tr>

            <tr style = "font-size:12px;">
             <th width = 0 height = 30></th>
             <th width = 1 height = 30></th>
             <th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 50>Ras<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 50><?php if (isset($gebdm)) {
             echo 'Geboortedatum';
                } else {
                    echo 'Aanvoerdatum';
                } ?><hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 200>Aantal dagen moeder<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>Aantal lammeren<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>Aantal levend geboren<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>% levend geboren<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>Aantal ooien<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>Aantal rammen<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 60>Gem geboorte gewicht<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 50>Gespeend<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 140>Gem speen gewicht<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 50>Afgeleverd<hr></th>
             <th width = 1></th>
             <th style = "text-align:center;"valign="bottom";width= 140>Gem aflever gewicht<hr></th>
             <th width = 60></th>
             <th style = "text-align:center;"valign="bottom";width= 80></th>
             <th width = 600></th>
            </tr>

            <tr align = "center">    
             <td width = 0> </td>
             <td width = 1> </td>
             <td width = 100 style = "font-size:14px;"> <?php echo $levnr; ?> <br> </td>
             <td width = 1> </td>   
             <td width = 100 style = "font-size:14px;"> <?php echo $werknr; ?> <br> </td>
             <td width = 1> </td>
             <td width = 100 style = "font-size:14px;"> <?php echo $ras; ?> <br> </td>
             <td width = 1> </td>              
             <td width = 100 style = "font-size:12px;"> <?php echo $opdm; ?> <br> </td>
             <td width = 1> </td>
             <td width = 100 style = "font-size:14px;"> <?php echo $dagen; ?> <br> </td>
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
             <td width = 100 style = "font-size:12px;"> <?php echo $aantafl; ?> <br> </td>
             <td width = 1> </td>    
             <td width = 100 style = "font-size:12px;"> <?php echo $gemafl; ?> <br> </td>
             <td width = 1> </td>
             <td width = 80 style = "font-size:13px;" >

<?php
            }
?>
       </td>
</tr> 
<tr><td colspan = 35 ><hr></td></tr>
<tr><td height = 25 ></td></tr>
<tr><td colspan = 10 align = "center"><h3>lammeren van moederdier </td></tr>
<tr><td></td></tr>
<!--    Einde Gegevens tbv MOEDERDIER        -->
<tr><td colspan = 50>
    <table border = 0>
    <tr align = "center" style = "font-size : 12px;" height = 30 valign = 'bottom' >
     <td> <b>Levensnummer</b><hr></td>
     <td></td> <td><b> werknr </b><hr></td>
     <td></td> <td><b> Generatie </b><hr></td>
     <td></td> <td><b> Geslacht </b><hr></td>
     <td></td> <td><b> Ras </b><hr></td> 
     <td></td> <td><b> Geboren </b><hr></td>
     <td></td> <td><b> Gewicht </b><hr></td> 
     <td></td> <td><b> Speendatum </b><hr></td>
     <td></td> <td><b> Speen gewicht </b><hr></td>
     <td></td> <td><b> Gem<br>groei<br>spenen </b><hr></td>
     <td></td> <td><b> Afvoerdatum </b><hr></td>
     <td></td> <td><b> Aflever gewicht </b><hr></td>
     <td></td> <td><b> Reden </b><hr></td>
     <td></td> <td><b> Gem<br>groei<br>afleveren </b><hr></td>
     </tr>
<?php
            $schaap_gateway = new SchaapGateway();
            $lammeren = $schaap_gateway->lammeren($Karwerk, $lidId, $gekozen_ooi);
            while ($lam = $lammeren->fetch_assoc()) {
                if (empty($lam['levensnummer'])) {
                    $Llevnr = 'Geen';
                } else {
                    $Llevnr = $lam['levensnummer'];
                }
                $Lwerknr = $lam['werknr'];
                $Lsekse = $lam['geslacht'];
                $Ldmaanw = $lam['dmaanw'];
                if (isset($Ldmaanw)) {
                    if ($Lsekse == 'ooi') {
                        $Lfase = 'moeder';
                    } if ($Lsekse == 'ram') {
                    $Lfase = 'vader';
                        }
                } else {
                    $Lfase = 'lam';
                }
                $Lras = $lam['ras'];
                $Ldatum = $lam['gebrndm'];
                $Lkg = $lam['gebrnkg'];
                $Lspndm = $lam['speendm'];
                $Lspnkg = $lam['speenkg'];
                $gemgr_s = $lam['gemgr_s'];
                $Lafldm = $lam['afvdm'];
                $Laflkg = $lam['afvkg'];
                $Luitvdm = $lam['uitvaldm'];
                $Lreden = $lam['reden'];
                $gemgr_a = $lam['gemgr_a'];

?>    
            <tr align = "center" style = "font-size : 14px";>
             <td align = "center" > <?php echo $Llevnr; ?>  </td>
             <td></td> <td> <?php echo $Lwerknr; ?> </td>
             <td></td> <td> <?php echo $Lfase; ?> </td>
             <td></td> <td> <?php echo $Lsekse; ?> </td>
             <td></td> <td> <?php echo $Lras; ?> </td>
             <td></td> <td width = 70> <?php echo $Ldatum; ?> </td>
             <td></td> <td> <?php echo $Lkg; ?> </td>
             <td></td> <td> <?php echo $Lspndm; ?> </td>
             <td></td> <td> <?php echo $Lspnkg; ?> </td>
             <td></td> <td width = 50 align = 'right'> <?php echo $gemgr_s . "&nbsp&nbsp"; ?> </td>
             <td></td> <td> <?php echo $Lafldm . $Luitvdm; ?> </td>
             <td></td> <td> <?php echo $Laflkg; ?> </td>
             <td></td> <td> <?php if (isset($Luitvdm) && !isset($Lreden)) {
             echo 'Overleden';
                } else {
                    echo $Lreden;
                } ?> </td>
             <td></td> <td align = 'right'> <?php echo $gemgr_a . "&nbsp"; ?> </td>
            </tr>
            <tr>
             <td></td>
            </tr>
<?php
            }
?>
</table>        
<!--    Einde Gegevens tbv LAM    -->    
<?php
        } elseif (isset($_POST['knpToon']) && $deel == 0) {
            $fout = "Er is geen keuze gemaakt.";
        } elseif (isset($_POST['knpToon'])) {
            $fout = "Het zoek criterium heeft geen resultaten opgeleverd. Pas het criterum eventueel aan. ";
        }
?>
 </td>
</tr>
</table>
</TD>
<?php
    } else {
?> 
            <img src='ooikaart_php.jpg'  width='970' height='550'/>
<?php
    }
    include "menuRapport1.php";
}
?>
</tr>
</table>
</body>
</html>
