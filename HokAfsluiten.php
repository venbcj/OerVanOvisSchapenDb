<?php

require_once("autoload.php");
/* 30-11-2014 : keuzelijst voer gewijigd zodat enkel voer in voorraad kan worden gekozen. Bovendie first in first out (via inkId )
28-2-2015 login toegevoegd
27-11-2015 : insVoer.php vervangen door save_voer.php */
$versie = "18-1-2017"; /* Query's aangepast n.a.v. nieuwe tblDoel */
$versie = "23-1-2017"; /* 22-1-2017 tblBezetting gewijzigd naar tblBezet 23-1-2017 kalender toegevoegd */
$versie = "5-2-2017"; /* Aanpassing n.a.v. verblijven met verschillende doelgroepen */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '29-2-2020'; /* Datum laatste schaap uit verblijf toegevoegd 7-3-2020 fouten uit code gehaald dmstop_geb moest dmstop_spn of dmstop_prnt zijn. Er waren onterecht volwassendieren waarvan de periode kon worden afgesloten */
$versie = '9-7-2021'; /* Schapen uit verblijf herzien. Join gewijzigd van h.hisId = uit.hisv naar b.bezId = uit.bezId */
$versie = '23-4-2023'; /* ht['laatste_uit']; gewijzigd naar hk['laatste_uit']; Resterende SQL beveiligd met quotes */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '20-01-2024'; /* in nestquery 'uit' is 'and a1.aan = 1' uit WHERE gehaald. De hisId die voorkomt in tblBezet volstaat. Bovendien is bij Pieter hisId met actId 3 gekoppeld aan tblBezet en heeft het veld 'aan' in tblActie de waarde 0. De WHERE incl. 'and a1.aan = 1' geeft dus een fout resultaat. */
$versie = "11-03-2024"; /* Bij geneste query uit
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 940 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>
<?php
$titel = 'Afsluiten periode';
$file = "Bezet.php";
include "login.php";
?>
            <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) {
    //include vw_Voorraad // incl. vw_Voorraden t.b.v. save_voer.php
    include "kalender.php";
    $eenheid_gateway = new EenheidGateway();
    $bezet_gateway = new BezetGateway();
    $qryKeuzelijstVoer = $eenheid_gateway->qryKeuzelijstVoer($lidId);
    if (empty($_GET['pstId'])) {
        $Id = $_POST['txtId'] ?? 0;
    } else {
        $Id = $_GET['pstId'];
    }
    if (isset($_POST['knpSave1'])) {
        if (!empty($_POST['txtKg1'])) {
            $txtKg = $_POST['txtKg1'];
        }
        if (!empty($_POST['kzlArtikel1'])) {
            $fldArt = $_POST['kzlArtikel1'];
        }
        $doelId = 1;
        if (!empty($_POST['txtSluitdm1'])) {
            $sluitdm1 = $_POST['txtSluitdm1'];
            $date = date_create("$sluitdm1");
            $dmsluit = date_format($date, 'Y-m-d');
            $sluitdm = $_POST['txtSluitdm1']; /*t.b.v. save_afsluiten.php*/
            include "save_afsluiten.php";
        } else {
            $fout = "Afsluitdatum is niet bekend";
        }
    }
    if (isset($_POST['knpSave2'])) {
        if (!empty($_POST['txtKg2'])) {
            $txtKg = $_POST['txtKg2'];
        }
        if (!empty($_POST['kzlArtikel2'])) {
            $fldArt = $_POST['kzlArtikel2'];
        }
        $doelId = 2;
        if (!empty($_POST['txtSluitdm2'])) {
            $sluitdm2 = $_POST['txtSluitdm2'];
            $date = date_create("$sluitdm2");
            $dmsluit = date_format($date, 'Y-m-d');
            $sluitdm = $_POST['txtSluitdm2']; /*t.b.v. save_afsluiten.php*/
            include "save_afsluiten.php";
        } else {
            $fout = "Afsluitdatum is niet bekend";
        }
    }
    if (isset($_POST['knpSave3'])) {
        if (!empty($_POST['txtKg3'])) {
            $txtKg = $_POST['txtKg3'];
        }
        if (!empty($_POST['kzlArtikel3'])) {
            $fldArt = $_POST['kzlArtikel3'];
        }
        $doelId = 3;
        if (!empty($_POST['txtSluitdm3'])) {
            $sluitdm3 = $_POST['txtSluitdm3'];
            $date = date_create("$sluitdm3");
            $dmsluit = date_format($date, 'Y-m-d');
            $sluitdm = $_POST['txtSluitdm3']; /*t.b.v. save_afsluiten.php*/
            include "save_afsluiten.php";
        } else {
            $fout = "Afsluitdatum is niet bekend";
        }
    }
?>
<form action=
<?php
    echo "HokAfsluiten.php";
?>
method="post">
    <table border = 0> <!-- table1 -->
    <tr> <!-- table1 rij1 -->
     <td> <input type ="hidden" name = "txtId"
<?php
    echo "value = \"$Id\" ";
?>
>
<?php
    $zoek_data_aantal_geb = $bezet_gateway->zoek_data_aantal_geb($Id);
    while ($hk = $zoek_data_aantal_geb->fetch_assoc()) {
        $hoknr_geb = $hk['hoknr'];
        $eerste_in_geb = $hk['eerste_in'];
        $laatste_uit_geb = $hk['laatste_uit'];
        $totat_geb = $hk['aant'];
        $dmstop_geb = $hk['dmstop'];
    }
    if (!isset($dmstop_geb)) {
        $dmstop_geb = '1988-06-25';
    }
    if (isset($eerste_in_geb)) {
        if ($eerste_in_geb < $dmstop_geb) {
            $dmstart_geb = $dmstop_geb;
        } else {
            $dmstart_geb = $eerste_in_geb;
        }
        $todate = date_create($dmstart_geb);
        $dag1_geb = date_format($todate, 'd-m-Y');
    }
    if (isset($laatste_uit_geb)) {
        $todate = date_create($laatste_uit_geb);
        $laatste_dag_geb = date_format($todate, 'd-m-Y');
    }
    // Als er schapen zonder speendatum in het verblijf zitten
    if (isset($eerste_in_geb)) {
        $nu_geb = $bezet_gateway->zoek_nu_in_hok3($Id);
?>
    <!--         HTML LINKER BOVEN GEDEELTE -->
    <table border = 0>
    <tr> <td colspan = 2 align = center> <h3> Afsluiten Foklammeren </h3></td> </tr>
    <tr> <td colspan = 2> <?php echo ucfirst($hoknr_geb);     ?>
heeft <?php echo $totat_geb; ?>
schapen voor het spenen geteld waarvan er nu nog <?php echo $nu_geb; ?>
in zitten.</td> </tr>
    <tr> <td> Startdatum </td><td><?php echo $dag1_geb;     ?>
</td> </tr>
    <tr> <td> Laatste uit verblijf </td><td>
<?php
        if (isset($laatste_dag_geb)) {
            echo $laatste_dag_geb;
        }
?>
</td> </tr>
    <tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker1" name = "txtSluitdm1" size = 6
<?php
        if (isset($sluitdm1)) {
            echo 'value = ' . $sluitdm1;
        }
?>
> </td> </tr>
    <tr>
     <td>
    Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg1" size = 6 value =
<?php
        if (isset($txtKg1)) {
            echo $txtKg1;
        }
?>
>
     </td>
    </tr>
    <tr>
     <td >Voer</td>
     <td>
<?php
        //kzl voer
        $qryKeuzelijstVoer->data_seek(0);
        $name = 'kzlArtikel1';
        $width = 250 ;
?>
    <select name=<?php echo"$name";     ?>
        style="width:<? echo "$width";
?>
;\" >";
     <option></option>
<?php
        while ($row = $qryKeuzelijstVoer->fetch_array()) {
            $vrd = str_replace('.00', '', $row[vrdat]);
            $stdrd = str_replace('.00', '', $row[stdat]);
            $kzlkey = "$row[artId]";
            $kzlvalue = "$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";
            include "kzl.php";
        }
        // EINDE kzl voer
?>
     </td>
    </tr>
    <tr>
     <td colspan =2 align = center> <input type =submit name = "knpSave1" value = "Opslaan" ></td> </tr>
    </table>
    <!--         EINDE    HTML LINKER BOVEN GEDEELTE     EINDE    -->
<?php
    }
    // Einde Als er schapen zonder speendatum in het verblijf zitten
?>
    </td>
<?php
    $zoek_data_aantal_spn = $bezet_gateway->zoek_data_aantal_spn($Id);
    while ($hk = $zoek_data_aantal_spn->fetch_assoc()) {
        $hoknr_spn = $hk['hoknr'];
        $eerste_in_spn = $hk['eerste_in'];
        $totat_spn = $hk['aant'];
        $laatste_uit_spn = $hk['laatste_uit'];
        $dmstop_spn = $hk['dmstop'];
    }
    if (!isset($dmstop_spn)) {
        $dmstop_spn = '1988-06-25';
    }
    if (isset($eerste_in_spn)) {
        if ($eerste_in_spn < $dmstop_spn) {
            $dmstart_spn = $dmstop_spn;
        } else {
            $dmstart_spn = $eerste_in_spn;
        }
        $todate = date_create($dmstart_spn);
        $dag1_spn = date_format($todate, 'd-m-Y');
    }
    if (isset($laatste_uit_spn)) {
        $todate = date_create($laatste_uit_spn);
        $laatste_dag_spn = date_format($todate, 'd-m-Y');
    }
    // Als er schapen met speendatum in het verblijf zitten
    if (isset($eerste_in_spn)) {
        $nu_spn = $bezet_gateway->zoek_nu_in_hok1($Id);
?>
         <td>
    <!--         HTML RECHTER BOVEN GEDEELTE -->
    <table border = 0>
    <tr> <td colspan = 2 align = center> <h3> Afsluiten Vleeslammeren </h3> </td> </tr>
    <tr> <td colspan = 2> <?php echo ucfirst($hoknr_spn);     ?>
heeft <?php echo $totat_spn; ?>
schapen na het spenen geteld waarvan er nu nog <?php echo $nu_spn; ?>
in zitten.</td> </tr>
    <tr> <td> Startdatum </td><td>
<?php
        if (isset($dag1_spn)) {
            echo $dag1_spn;
        }
?>
</td> </tr>
    <tr> <td> Laatste uit verblijf </td><td>
<?php
        if (isset($laatste_dag_spn)) {
            echo $laatste_dag_spn;
        }
?>
</td> </tr>
    <tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker2" name = "txtSluitdm2" size = 6 value =
<?php
        if (isset($sluitdm2)) {
            echo $sluitdm2;
        }
?>
> </td> </tr>
    <tr>
     <td>
    Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg2" size = 6 value =
<?php
        if (isset($txtKg2)) {
            echo $txtKg2;
        }
?>
>
     </td>
    </tr>
    <tr>
     <td>Voer</td>
     <td>
<?php
        //kzl voer
        $qryKeuzelijstVoer->data_seek(0);
        $name = 'kzlArtikel2';
        $width = 250 ;
?>
    <select name=<?php echo"$name";     ?>
        style="width:<? echo "$width";
?>
;\" >";
     <option></option>
<?php
        while ($row = $qryKeuzelijstVoer->fetch_array()) {
            $vrd = str_replace('.00', '', $row[vrdat]);
            $stdrd = str_replace('.00', '', $row[stdat]);
            $kzlkey = "$row[artId]";
            $kzlvalue = "$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";
            include "kzl.php";
        }
        // EINDE kzl voer
?>
     </td></tr>
    <tr> <td colspan =2 align = center> <input type =submit name = "knpSave2" value = "Opslaan" ></td> </tr>
    </table>
    <!--         EINDE    HTML RECHTER BOVEN GEDEELTE     EINDE    -->
<?php
    }
    // Einde Als er schapen met speendatum in het verblijf zitten
?>
    </td>
    </tr> <!-- table1 Einde rij1 -->
    <tr>  <!-- table1 rij2 -->
     <td height="25"></td>
    </tr>  <!-- table1 Einde rij2 -->
    <tr>  <!-- table1 rij3 -->
    <td>
<?php
    $zoek_data_aantal_prnt = $bezet_gateway->zoek_data_aantal_prnt($Id);
    while ($hk = $zoek_data_aantal_prnt->fetch_assoc()) {
        $hoknr_prnt = $hk['hoknr'];
        $eerste_in_prnt = $hk['eerste_in'];
        $totat_prnt = $hk['aant'];
        $laatste_uit_prnt = $hk['laatste_uit'];
        $dmstop_prnt = $hk['dmstop'];
    }
    if (!isset($dmstop_prnt)) {
        $dmstop_prnt = '1988-06-25';
    }
    if (isset($eerste_in_prnt)) {
        if ($eerste_in_prnt < $dmstop_prnt) {
            $dmstart_prnt = $dmstop_prnt;
        } else {
            $dmstart_prnt = $eerste_in_prnt;
        }
        $todate = date_create($dmstart_prnt);
        $dag1_prnt = date_format($todate, 'd-m-Y');
    }
    if (isset($laatste_uit_prnt)) {
        $todate = date_create($laatste_uit_prnt);
        $laatste_dag_prnt = date_format($todate, 'd-m-Y');
    }
    // Als er volwassen schapen in het verblijf zitten
    if (isset($eerste_in_prnt)) {
        $nu_prnt = $bezet_gateway->zoek_nu_in_hok2($Id);
?>
    <!--         HTML LINKER ONDER GEDEELTE -->
    <table border = 0>
    <tr> <td colspan = 2 align = center> <h3> Afsluiten Moeder- en vaderdieren </h3></td> </tr>
    <tr> <td colspan = 2> <?php echo ucfirst($hoknr_prnt);     ?>
heeft <?php echo $totat_prnt; ?>
moeder- en vaderdieren geteld waarvan er nu nog <?php echo $nu_prnt; ?>
in zitten.</td> </tr>
    <tr> <td> Startdatum </td><td><?php echo $dag1_prnt;     ?>
</td> </tr>
    <tr> <td> Laatste uit verblijf </td><td>
<?php
        if (isset($laatste_dag_prnt)) {
            echo $laatste_dag_prnt;
        }
?>
</td> </tr>
    <tr> <td> Afsluitdatum </td><td><input type =text id = "datepicker3" name = "txtSluitdm3" size = 6
<?php
        if (isset($sluitdm1)) {
            echo 'value = ' . $sluitdm1;
        }
?>
> </td> </tr>
    <tr>
     <td>
    Hoeveelheid voer </td><td valign = 'top'><input type ="text" name = "txtKg3" size = 6 value =
<?php
        if (isset($txtKg3)) {
            echo $txtKg3;
        }
?>
>
     </td>
    </tr>
    <tr>
     <td >Voer</td>
     <td>
<?php
        //kzl voer
        $qryKeuzelijstVoer->data_seek(0);
        $name = 'kzlArtikel3';
        $width = 250 ;
?>
    <select name=<?php echo"$name";     ?> style="width:<?php echo "$width"; ?>">
     <option></option>
<?php
        while ($row = $qryKeuzelijstVoer->fetch_array()) {
            $vrd = str_replace('.00', '', $row[vrdat]);
            $stdrd = str_replace('.00', '', $row[stdat]);
            $kzlkey = "$row[artId]";
            $kzlvalue = "$row[naam] &nbsp per $stdrd $row[eenheid] &nbsp ($vrd $row[eenheid])";
            include "kzl.php";
        }
        // EINDE kzl voer
?>
     </td>
    </tr>
    <tr>
     <td colspan =2 align = center> <input type =submit name = "knpSave3" value = "Opslaan" ></td> </tr>
    </table>
    <!--         EINDE    HTML LINKER ONDER GEDEELTE     EINDE    -->
<?php
    }
    // Einde Als er volwassen schapen in het verblijf zitten
?>
    </td>
    </tr> <!-- table1 Einde rij3 -->
    </table> <!-- Einde table1 -->
</TD>
<?php
    include "menu1.php";
}
?>
    </body>
    </html>
