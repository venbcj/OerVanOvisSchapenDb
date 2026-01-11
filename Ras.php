<?php

require_once("autoload.php");
$versie = '11-11-2014'; /*header("Location: http://localhost:8080/schapendb/.....php");   toegevoegd. Dit ververst de pagina zodat een wijziging op het eerste record direct zichtbaar is */
$versie = '8-3-2015'; /*Login toegevoegd*/
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-5-2020'; /* Scannummer t.b.v. reader Agrident aangepast. Hidden velden scan en actief verwijderd */
$versie = '30-5-2020'; /* function db_null_input toegevoegd en pagina opgebouwd/ingedeeld als Hok.php */
$versie = '13-6-2020'; /* Mogelijkheid eigen rassen toevoegen */
$versie = '20-4-2024'; /* Sortering rassen in gebruik */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Beheer</title>
</head>
<body>
<?php
$titel = 'Invoer rassen';
$file = "Ras.php";
include "login.php";
?>
            <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    $ras_gateway = new RasGateway();
    include "validate-ras.js.php";
    if (isset($_POST['knpSave_'])) {
        include "save_ras.php";
    }
    if (isset($_POST['knpInsert2_'])) {
        if (empty($_POST['txtRas_'])) {
            $fout = "Er is geen ras ingevoerd.";
        } else {
            $txtRas = $_POST['txtRas_'];
            $eigenras_db = $ras_gateway->zoek_ras_name($txtRas, $lidId);
            if (isset($eigenras_db)) {
                $fout = "Dit ras bestaat al.";
            } else {
                $rasId = $ras_gateway->query_ras_toevoegen($txtRas, $lidId);
                $query_rasuser_toevoegen = $ras_gateway->query_rasuser_toevoegen($lidId, $rasId);
                $ras_gateway->update_tblRas($lidId);
            } // Einde else van if(isset($eigenras_db))
        } // Einde else van if (empty($_POST['txtRas_']))
    }
    // Einde if (isset ($_POST['knpInsert2_']))
    if (isset($_POST['knpInsert_'])) {
        if (empty($_POST['kzlRas_'])) {
            $fout = "U heeft geen ras geselecteerd.";
        } else {
            $query_ras_toevoegen = $ras_gateway->rasuser_toevoegen($lidId, $_POST['kzlRas_'], $_POST['insScan_'], $_POST['insSort_']);
        }
    }
    $pdf = $ras_gateway->zoek_rasuId($lidId);
?>
<form action="Ras.php" method="post">
<table border = 0>
<tr>
 <td valign = 'top'>
<table border = 0>
<tr>
 <td>
<?php 
    if ($reader == 'Agrident') {
        $kop = 'sortering reader';
    } else {
        $kop = 'code tbv reader';
    }
?>
    <b> Nieuw ras :</b> <td align = "center" width = 10 style ="font-size:12px;"> <b> <?php 
    echo $kop;
?>
 </b>
 </td>
 <td colspan = 2>
 </td>
  <td>
      <b> Eigen Ras</b>
 </td>
</tr>
<?php 
    $qryRas = $ras_gateway->qryRas($lidId);
    $index = 0;
    $rasId = [];
    while ($qr = $qryRas->fetch_assoc()) {
        $rasId[$index] = $qr['rasId'];
        $rasnm[$index] = $qr['ras'];
        $index++;
    }
    // EINDE DECLARATIE RAS
?>
<tr>
 <td>
<!-- KZLRAS -->
 <select style="width:180;" name="kzlRas_" value = "" style = "font-size:12px;">
  <option></option>
<?php 
    $count = count($rasId);
    for ($i = 0; $i < $count; $i++) {
        $opties = array($rasId[$i] => $rasnm[$i]);
        foreach ($opties as $key => $waarde) {
            $keuze = '';
            if ((isset($_POST['kzlRas_']) && $_POST['kzlRas_'] == $key)) {
                echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
            } else {
                echo '<option value="' . $key . '" >' . $waarde . '</option>';
            }
        }
    }
?>
</select>
<!-- EINDE KZLRAS -->
 </td>
  <td>
<?php 
    if ($reader == 'Agrident') {
?>
    <input type= "text" name= "insSort_" size = 1 title = "Leg hier het nummer vast om de volgorde in de reader te bepalen." >
<?php
    } else {
?>
    <input type= "text" name= "insScan_" size = 1 title = "Leg hier de code vast die u tijdens het scannen met de reader gaat gebruiken." value =
<?php
        if (isset($txtScan)) {
            echo $txtScan;
        }
?>
 >
<?php
    }
?>
 </td>
 <td align = "center"><input type = "submit" name="knpInsert_" value = "Toevoegen" > </td>
  <td width="125">
  </td>
  <td>
      <input type="text" name="txtRas_" id="txtRas">
 </td>
 <td> <input type = "submit" name= "knpInsert2_" onfocus = "verplicht()" value = "Toevoegen" style = "font-size:12px;"> </td>
 <td width="125">
  </td>
</tr>
</table>
 </td>
 <td>
<table border = 0 align = 'left' >
<tr>
 <td> <b> Rassen</b> </td>
 <td align = "center" style ="font-size:12px;"> <?php 
    echo $kop;
?>
 </td>
 <td align = "center" style ="font-size:12px;"> in gebruik </td>
 <td> <input type = "submit" name= "knpSave_" value = "Opslaan" style = "font-size:12px;"> </td>
 <td width= 100 align = "right">
<?php 
    echo View::link_to('print pagina', 'Ras_pdf.php?Id=' . $pdf, ['style' => 'color: blue']);
?>
 </td>
 <td width="50">
 </td>
</tr>
<tr>
 <td colspan = 5><hr> </td>
</tr>
<?php 
    $query = $ras_gateway->query($lidId);
    while ($rij = $query->fetch_assoc()) {
        $Id = $rij['rasId'];
        $ras = $rij['ras'];
        $scan = $rij['scan'];
        $sort = $rij['sort'];
        $actief = $rij['actief'];
?>
<tr>
 <td> <?php echo $ras; ?>
 </td>
 <td width = 100 align = "center">
<?php
        if ($reader == 'Agrident') {
?>
    <input type = text name = <?php echo "txtSort_$Id"; ?> size = 1 value = <?php echo $sort; ?>  >
<?php
        } else {
?>
    <input type = text name = <?php echo "txtScan_$Id"; ?>
 size = 1 title = "Wijzig hier de code die u tijdens het scannen met de reader gaat gebruiken." value = <?php echo $scan; ?>  >
<?php
        }
?>
 </td>
 <td>
    <input type = hidden name = <?php echo "chbActief_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = "checkbox" name = <?php echo "chbActief_$Id"; ?> id="c1" value= 1 <?php echo $actief == 1 ? 'checked' : ''; ?> title = "Is dit ras te gebruiken ja/nee ?"/>
 </td>
</tr>
<?php
    }
?>
 </td>
</tr>
</table>
</td>
</tr>
</table>
</form>
    </TD>
<?php 
    include "menuBeheer.php";
}
?>
</body>
</html>
