<?php
require_once("autoload.php");
$versie = '30-08-2025'; /* Gekopieerd van InsAfvoer.php. ActId 12 (zijnde afgeleverd) uit tabel tblActie wordt vanaf nu ook gebruikt om ubn te wijzigen. Zie InsGrWijzigingUbn.php. Als het nieuwe veld ubnId in tabel impAgrident leeg is dan is het een reguliere afvoer van een lam. Is het veld ubnId gevuld dan betreft het een wijziging van ubn van de gebruiker. Dus afvoer oude ubn en aanvoer nieuwe ubn in 1 handeling via deze pagina */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>
<?php
$titel = 'Inlezen Ubn wijziging';
$subtitel = '';
$file = "InsGrWijzigingUbn.php";
include "login.php";
?>
            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $impagrident_gateway = new ImpAgridentGateway();
    $hok_gateway = new HokGateway();
    if (isset($_POST['knpInsert_'])) {
        include "post_readerUbn.php";
    }
    $velden = "rd.Id readId, rd.datum, right(rd.levensnummer,$Karwerk) werknr, rd.levensnummer levnr, rd.hokId hok_rd, u_best.ubn ubn_best, rel_best.naam bestemming, rel_best.relId rel_best, gewicht kg, s.schaapId, s.geslacht, u_herk.ubn ubn_herk, rel_herk.naam herkomst, rel_herk.relId rel_herk, ouder.datum dmaanw, lower(haf.actie) actie, haf.af, ho.hokId hok_db, date_format(max.datummax_afv,'%d-%m-%Y') maxdatum_afv, max.datummax_afv, date_format(max.datummax_kg,'%d-%m-%Y') maxdatum_kg, max.datummax_kg ";
    $tabel = $impagrident_gateway->getInsGrWijzigingUbnFrom();
    $WHERE = $impagrident_gateway->getInsGrWijzigingUbnWhere($lidId);
    include "paginas.php";
    $data = $paginator->fetch_data($velden, "ORDER BY right(rd.levensnummer,$Karwerk) ");
?>
<table border = 0>
<tr> <form action="InsGrWijzigingUbn.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = center style = "font-size : 14px;"><?php echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td align = 'right'> <input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Afvoeren<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Wijzigdatum<hr></th>
 <th>Werknr<hr></th>
 <th>Levensnummer<hr></th>
<?php
    if ($modtech == 1) {
        // Velden die worden getoond bij module technisch
?>
 <th>Gewicht<hr></th>
 <th>Verblijf<hr></th>
    <?php } ?>
 <th>Generatie<hr></th>
 <th>Bestemming<hr></th>
 <th></th>
 <th>Herkomst<hr></th>
 <th></th>
 <th colspan = 2 > <a href="exportInsGrWijzigingUbn.php?pst=<?php echo $lidId; ?> "> Export-xlsx </a> <br><br><hr></th>
 <th ></th>
</tr>
<?php
        if ($modtech == 1) {
            // Declaratie HOKNUMMER            // lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
            $qryHoknummer = $hok_gateway->hoknummer($lidId);
            $index = 0;
            while ($hknr = $qryHoknummer->fetch_assoc()) {
                $hoknId[$index] = $hknr['hokId'];
                $hoknum[$index] = $hknr['hoknr'];
                $index++;
            }
            unset($index);
            // EINDE Declaratie HOKNUMMER
        }
        if (isset($data)) {
            foreach ($data as $key => $array) {
                unset($status);
                $var = $array['datum'];
                $date = str_replace('/', '-', $var);
                $datum = date('d-m-Y', strtotime($date));
                $date       = date('Y-m-d', strtotime($date));
                $Id = $array['readId'];
                $werknr = $array['werknr'];
                $levnr = $array['levnr'];
                $ubn_best = $array['ubn_best'];
                $bestemming = $array['bestemming'];
                $rel_best = $array['rel_best'];
                $kg = $array['kg'];
                $schaapId = $array['schaapId'];
                $geslacht = $array['geslacht'];
                $ubn_herk = $array['ubn_herk'];
                $herkomst = $array['herkomst'];
                $rel_herk = $array['rel_herk'];
                $hok_rd = $array['hok_rd'];
                $hok_db = $array['hok_db'];
                $dmaanw = $array['dmaanw'];
                if (isset($dmaanw)) {
                    if ($geslacht == 'ooi') {
                        $fase = 'moederdier';
                    } elseif ($geslacht == 'ram') {
                        $fase = 'vaderdier';
                    }
                } else {
                    $fase = 'lam';
                }
                $status = $array['actie'];
                $af = $array['af'];
                if (isset($af) && $af == 1) {
                    $status = $status;
                } else {
                    $status = $fase;
                }
                $dmmax_bij_afvoer = $array['datummax_afv'];
                $dmmax_bij_wegen = $array['datummax_kg'];
                $maxdm_bij_afvoer = $array['maxdatum_afv'];
                $maxdm_bij_wegen = $array['maxdatum_kg'];
                // Controleren of ingelezen waardes worden gevonden.
                unset($onjuist);
                unset($color);
                if (isset($_POST['knpVervers_'])) {
                    $datum = $_POST["txtAfvoerdag_$Id"];
                    if (isset($_POST["txtKg_$Id"])) {
                        $kg = $_POST["txtKg_$Id"];
                    }
                    $makeday = date_create($_POST["txtAfvoerdag_$Id"]);
                    $date =  date_format($makeday, 'Y-m-d');
                }
                if (!isset($schaapId)) {
                    $color = 'red';
                    $onjuist = 'Levensnummer onbekend.';
                } elseif (empty($datum)) {
                    $color = 'red';
                    $onjuist = 'De datum onbekend.';
                } elseif ($status == 'afgeleverd') {
                    $color = 'red';
                    $onjuist = 'Dit schaap is reeds ' . $status . '.';
                } elseif ($status == 'overleden' || $status == 'uitgeschaard') {
                    $color = 'red';
                    $onjuist = 'Dit schaap is ' . $status . '.';
                } elseif (isset($fase) && $date < $dmmax_bij_afvoer) {
                    $color = 'red';
                    $onjuist = 'Datum ligt voor $maxdm_bij_afvoer.';
                } elseif ($ubn_best == $ubn_herk) {
                    $color = 'red';
                    $onjuist = 'Dit schaap staat al op ubn ' . $ubn_best . '.';
                } elseif (!isset($rel_best)) {
                    $color = 'red';
                    $onjuist = 'Bestemming wordt niet gevonden als debiteur.';
                } elseif (!isset($rel_herk)) {
                    $color = 'red';
                    $onjuist = 'Herkomst wordt niet gevonden als crediteur.';
                }
                if (isset($onjuist)) {
                    $oke_afv = 0;
                } else {
                    $oke_afv = 1;
                }  // $oke_afv kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
                // EINDE Controleren of ingelezen waardes worden gevonden .
                /* Als onvolledig is gewijzigd naar volledig juist wordt checkbox eenmalig automatisch aangevinkt */
                if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke_afv == 1) {
                    $cbKies = 1;
                    $cbDel = $_POST["chbDel_$Id"];
                } elseif (isset($_POST['knpVervers_'])) {
                    $cbKies = $_POST["chbkies_$Id"];
                    $cbDel = $_POST["chbDel_$Id"];
                } else {
                    $cbKies = $oke_afv;
                } // $cbKies is tbv het vasthouden van de keuze inlezen of niet
                //if(isset($_POST['knpVervers_'])) {}
?>
<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->
<tr style = "font-size:13px;">
 <td align = center>
    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1
<?php
                echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */
                if ($oke_afv == 0) {
                    /*Als voorwaarde niet klopt */
                  echo ' disabled';
                } else {
                    echo 'class="checkall"';
                } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/
?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke_afv; ?> > <!-- hiddden -->
 </td>
 <td align = center>
 <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if (isset($cbDel)) {
 echo $cbDel == 1 ? 'checked' : '';
                } ?> >
 </td>
 <td>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtAfvoerdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
<?php if (isset($schaapId)) {
echo "<td align = center >" . $werknr;
 } else {
     ?> <td align = center style = "color : red"> <?php echo $werknr;
 } ?>
 </td>
<?php if (isset($schaapId)) {
echo "<td>" . $levnr;
     } else {
         ?> <td align = center style = "color : red"> <?php echo $levnr;
     } ?>
 </td>
            <?php
         if ($modtech == 1) {
?>
 <td style = "font-size : 9px;">
    <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php echo $kg; ?> >
 </td>
 <td style = "font-size : 9px;">
<!-- KZLHOKNR -->
 <select style="width:65;" <?php echo " name=\"kzlHok_$Id\" "; ?> value = "" style = "font-size:12px;">
  <option></option>
<?php    $count = count($hoknum);
for ($i = 0; $i < $count; $i++) {
    $opties = array($hoknId[$i] => $hoknum[$i]);
    foreach ($opties as $key => $waarde) {
        if ((!isset($_POST['knpVervers_']) && $hok_rd == $hoknId[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)) {
            echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
        } else {
            echo '<option value="' . $key . '" >' . $waarde . '</option>';
        }
    }
}
?>    </select>
<?php
if (!empty($hok_rd) && empty($hok_db) && !isset($_POST['knpVervers_'])) {
    echo $hok_rd; ?> <b style = "color : red;"> ! </b>  <?php
} ?>
 </td> <!-- EINDE KZLHOKNR -->
            <?php 
         }
                //Einde if($modtech == 1)
?>
    <td width = 80 align = "center">
<?php
    if (isset($status)) {
        echo $fase ;
    }
?>
 </td>
 <td align="center"> <?php echo $ubn_best . ' - ' . $bestemming; ?> </td>
 <td width="10"></td>
 <td align="center"> <?php echo $ubn_herk . ' - ' . $herkomst; ?> </td>
 <td width="10"></td>
<!-- Foutmeldingen -->
<td colspan = 2 width = 300 style = "color : <?php echo $color; ?>"> <?php
if (isset($onjuist)) {
    echo $onjuist;
} ?>
 </td>
 <td>
 </td>
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->
<?php 
            }
        }
?>
</table>
</form>
    </TD>
<?php
        include "menu1.php";
}
?>
</tr>
</table>
<?php
    include "select-all.js.php";
?>
</body>
</html>
