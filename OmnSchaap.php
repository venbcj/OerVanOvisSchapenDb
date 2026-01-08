<?php

require_once("autoload.php");
$versie = '27-9-2020'; /* Gekopieerd van insOmnummeren.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>
<?php
$titel = 'Omnummeren';
$file = "OmnSchaap.php";
include "login.php";
?>
        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    include "kalender.php";
    include "validate-omnschaap.js.php";
    $pstnr = 0;
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $velden = (array_keys($_POST));
        $pstnr = Url::getIdFromKey($velden[0]);
    } else {
        if (!empty($_GET['pstschaap'])) {
            $pst = $_GET['pstschaap'];
            $schaap_gateway = new SchaapGateway();
            $pstnr = $schaap_gateway->zoek_oud_levensnummer_obv_schaapId($pst);
        }
    }
    if (isset($_POST["knpSave_$pstnr"])) {
        $levnr_old = $pstnr;
        $levnr_new = $_POST["txtLevnrNew"];
        $schaap_gateway = new SchaapGateway();
        $levnr_db = $schaap_gateway->zoek_op_bestaand_levensnummer($levnr_new);
        if (isset($levnr_db)) {
            $fout = "Dit levensnummer bestaat al.";
        } else {
            $datum = $_POST['txtDag'];
            $dag = date_create($datum);
            $fldDay =  date_format($dag, 'Y-m-d');
            $stal_gateway = new StalGateway();
            [$stalId, $schaapId] = $stal_gateway->zoek_stalId_omn($lidId, $levnr_old);
            $historie_gateway = new HistorieGateway();
            $insert_tblHistorie = $historie_gateway->omnummer($stalId, $fldDay, $levnr_old);
            $schaap_gateway = new SchaapGateway();
            $update_tblSchaap = $schaap_gateway->updateLevensnummer($schaapId, $levnr_new);
            if ($modmeld == 1) {
                $historie_gateway = new HistorieGateway();
                $hisId = $historie_gateway->zoek_omnummering($stalId);
                $Melding = 'VMD';
                include "maak_request.php";
            }
        } // Einde else
    } // Einde if (isset ($_POST["knpSave_$pstnr"]))
    unset($levnr_old);
    $schaap_gateway = new SchaapGateway();
    $levnr_old = $schaap_gateway->zoek_oud_levensnummer($pstnr);
?>
<table border = 0>
<tr> <form action="OmnSchaap.php" method = "post">
 <td width="450"></td>
 <td colspan = 12 align = 'right'><input type = "submit" onfocus = "verplicht()" name = <?php echo "knpSave_$pstnr"; ?> value = "Opslaan">&nbsp &nbsp </td>
 <td colspan = 2 > </td>
</tr>
<tr valign = bottom style = "font-size : 12px;">
 <th width="450"></th>
 <th>Omnummer<br>datum<hr></th>
 <th width="25"></th>
 <th>Oud<hr></th>
 <th width="25"></th>
 <th>nieuw<hr></th>
</tr>
<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->
<?php
    if (isset($levnr_old)) {
        if (isset($_POST["knpSave_$pstnr"])) {
            $dag = $_POST["txtDag"];
            $levnr_new = $_POST["txtLevnrNew"];
        } else {
            $dag = date('d-m-Y');
        }
?>
<tr style = "font-size:14px;">
 <td width="450"></td>
 <td>
    <input type = "text" size = 7 style = "font-size : 14px;" id="datepicker1" name = <?php echo "txtDag"; ?> value = <?php echo $dag; ?> >
 </td>
 <td></td>
 <td> <?php echo $levnr_old; ?>
 <td></td>
 </td>
 <td align="center">
 <input type = "text" size = 10 style = "font-size : 14px;" id="levnr" name = <?php echo "txtLevnrNew"; ?> value = <?php if (isset($levnr_new)) {
 echo $levnr_new;
 } ?> >
 </td>
</tr>
<?php
    }
?>
<!--**************************************
        **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->
</table>
</form>
</TD>
<?php
    include "menu1.php";
}
?>
</tr>
</table>
</body>
</html>
