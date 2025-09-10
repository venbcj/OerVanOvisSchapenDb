<?php
require_once('validation_functions.php');
require_once('basisfuncties.php');
/*      23-11-2014 gemaakt
26-3-2015 login toegevoegd
mail 13-04-2015 Arjen Dijkstra : Andere meldingen dan een geboorte doet u binnen zeven kalenderdagen na de gebeurtenis
                      m.b.t. ‘datum in de toekomst’: dit is inderdaad 3 dagen en dat mag alleen bij Afvoermelding en Exportmelding */
$versie = '2-12-2016'; /* Index keuzelijst 'vastleggen' kzlDef_ gewijigd van 0 en 1 naar N en J    9-2-2017 : ook where clouse bij vastleggen melddatum */
$versie = '4-12-2016'; /* 'Controle of levensnummer wel wordt gevonden' verwijderd. Dit was noodzakelijk in de eerste release. Contorle op 12 cijfers en numeriek ook verwijderd */
$versie = '5-12-2016'; /* kzlPartijgewijzigd in kzlHerk */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '1-1-2020'; /* het pad ($file_r) naar FTP variabel gemaakt ipv uit tblLeden gehaald */
$versie = '30-1-2022'; /* Keuze controle en knop melden bij elkaar gezet. Sql beveiligd met quotes */
$versie = '1-4-2022'; /* $code binnen save_melding.php werd opgehaald uit responscheck.php */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '20-01-2024'; /* Controle melding verplicht gemaakt  */
$versie = '10-03-2024'; /* Als alle regels moeten worden verwijderd kan dit vanaf nu worden verwerkt zonder eerst 1 melding als controle melding te versturen. Verwijderde regels worden bij definitief melden meteen onzichtbaar. De url t.b.v. javascript geactualisserd van http://ajax.googleapis.com/ajax/libs/jquery/1.3.2/jquery.min.js naar https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '13-07-2025'; /* Ubn van gebruiker per regel getoond omdat een gebruiker per deze versie meerdere ubn's kan hebben */

session_start();
?>
<!DOCTYPE html>
<html>
<head>
    <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Melden Aanvoer';
$file = "Melden.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (is_logged_in()) {
    include "responscheck.php";

    if (isset($_POST['knpSave_'])) {
        /* $code bestaat ook in responscheck.php */
        $code = 'AAN';
        include "save_melding.php";
        header("Location: ".$curr_url);
    }

    $knptype = "submit";
    $today = date("Y-m-d");

    // De gegevens van het request
    $reqId = zoek_oudste_request_niet_definitief_gemeld($db, $lidId);
    // Einde De gegevens van het request

    // Aantal dieren te melden functie gedeclareerd in basisfuncties.php
    $aantMeld = aantal_melden($db, $reqId);
    // aantal_oke_aanv gedeclareerd in melden_functions
    $oke = aantal_oke_aanv($db, $reqId);
    // Einde Aantal dieren goed geregistreerd om automatisch te kunnen melden.
     
    // MELDEN
    if (isset($_POST['knpMeld_'])) {
        include "save_melding.php";
        $aantMeld = aantal_melden($db, $reqId);
        $oke = aantal_oke_aanv($db, $reqId);
        if ($aantMeld > 0 && $oke > 0) {
            // Bestand maken
            // TODO: (BV) soms voegen variabelen iets toe. Hier vind ik van niet. Het kan in 1 regel, zie hieronder:
            $alias = alias_voor_lid($db, $lidId);
            $file_r = dirname(__FILE__); // Het pad naar alle php bestanden
            $input_file = $alias."_".$reqId."_request.txt"; // Bestandsnaam
            $end_dir_reader = $file_r ."/". "BRIGHT/";
            $root = $end_dir_reader.$input_file;
            // dus
            // (invullen) $root = $file_r . '/BRIGHT/' . $input_file;
            // (invullen) $root = dirname(__FILE__) . '/BRIGHT/' . $alias . '_' . $reqId . '_request.txt';
            // Dit is in 1 regel:
            // $fh = fopen(dirname(__FILE__) . '/BRIGHT/' . alias_voor_lid($db, $lidId) . '_' . $reqId . '_request.txt', 'w');
            $fh = fopen($root, 'w');
            /* insert field values into data.txt */
            $qry_txtRequest_RVO = aanvoer_request_rvo_query($db, $reqId);
            /* Herkomst (ubn_herk) is niet verplicht te melden */
            while ($row = mysqli_fetch_array($qry_txtRequest_RVO)) {
                // TODO: (BV) volgens mij is dit hele stuk ...
                $num = mysqli_num_fields($qry_txtRequest_RVO) ;
                $last = $num - 1;
                for ($i = 0; $i < $num; $i++) {
                    fwrite($fh, $row[$i]);
                    if ($i != $last) {
                        fwrite($fh, ";");
                    }
                }
                fwrite($fh, PHP_EOL);
                // ... exact hetzelfde als
                // fwrite($fh, implode(';', $row);
            }
            fclose($fh);

            registreer_melddatum($db, $reqId);
            if ($_POST['kzlDef_'] == 'J') {
                $knptype = "hidden";
            }
            $goed = "De melding is verstuurd.";
        } elseif ($aantMeld == 0 || $oke == 0) {
            registreer_melddatum_definitief($db, $reqId);
             if ($_POST['kzlDef_'] == 'J' || $aantMeld == 0) {
                 $knptype = "hidden";
                 $goed = "De schapen kunnen handmatig worden gemeld.";
             } else {
                 $goed = "Er is niets te controleren.";
             }
        }
        $aantMeld = aantal_melden($db, $reqId);
    } // EINDE MELDEN

    // Ophalen 'vaststellen' cq 'controle'
    $def = zoek_request_definitief($db, $reqId);
?>
<form action="MeldAanvoer.php" method = "post">
<table border = 0>
<tr>
 <td align = "right">Meldingnr : </td>
 <td> <?php echo $reqId; ?> </td>
 <td width = 850 align = "right">Aantal dieren te melden : </td>
 <td><?php echo $aantMeld; ?></td>
</tr>

<tr>
 <td colspan="3" align = "right">
<?php
    $zoekControle = zoek_controle_melding($db, $reqId);
    if (isset($zoekControle) && $zoekControle > 0 && $aantMeld > 0) {
        /* Als er een controlemelding is gedaan en er zijn schapen te melden */
        // TODO: (BCB) dit uitvoeren met collection_select
?>
    <!-- KZLDefinitief --> 
    <select <?php echo "name=\"kzlDef_\" "; ?> style = "width:100; font-size:13px;">
<?php
        $opties = array('N'=>'Controle', 'J'=>'Vastleggen');
        foreach ($opties as $key => $waarde) {
            $selected = '';
            if ((!isset($_POST['knpSave_']) && $def == $key) || (isset($_POST["kzlDef_"]) && $_POST["kzlDef_"] == $key)) {
                $selected = ' selected="selected"';
            }
            echo '<option value="' . $key . '"' . $selected . '>' . $waarde . '</option>';
        }
?> 
    </select> <!-- EINDE KZLDefinitief -->
<?php
    } elseif ($aantMeld > 0) {
        echo 'Controle ';
    }
/* Als er geen controlemelding is gedaan en er zijn schapen te melden. Anders zijn er geen dieren te melden en alleen te verwijderen */
?>
&nbsp;&nbsp;
</td>
<td>
<?php
    if ($aantMeld == 0) {
        $meld_value = 'Verwijderen';
    } else {
        $meld_value = 'Melden';
    }
?>
    <input type = <?php echo $knptype; ?> name = "knpMeld_" value = "<?php echo $meld_value; ?>">
</td>
</tr>
<tr>
<td colspan = 10><hr></hr></td>
</tr>
</table>

<table border = 0 >
<tr> 
<td ><input type = <?php echo $knptype; ?> name = "knpSave_" value = "Opslaan"></td> 
<?php
    if ($knptype == 'submit') {
        if ($oke == 1) {
            $wwoord = 'wordt';
        } else {
            $wwoord = 'worden';
        }
    } else {
        if ($oke == 1) {
            $wwoord = 'is';
        } else {
            $wwoord = 'zijn';
        }
    }
?>
 <td colspan = 4 width = 500 align = "center" > <b style = "color : red;">
<?php
    if ($oke <> $aantMeld) {
        echo $oke . " van de " .$aantMeld. " dieren ".$wwoord." gemeld bij RVO.";
    }
?>
</b></td>
<td></td>
<td width = 50></td>
<td></td>
</tr>

<tr valign = bottom style = "font-size : 12px;">
 <td colspan = 20 height = 20></td>
</tr>

<tr valign = bottom style = "font-size : 12px;">
 <th>Ubn<hr></th>
 <th>Leverdatum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Generatie<hr></th>
 <th>Herkomst<hr></th>
 <th>Verwij- deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Bericht<hr></th>
 <th></th>
</tr>

<?php
    $zoek_meldregels = aanvoer_zoek_meldregels_query($db, $reqId, $lidId);

    while ($row = mysqli_fetch_assoc($zoek_meldregels)) {
        // TODO: (BCB) Inline Variable. Code hieronder mag direct uit $row[] putten.
        $Id = $row['meldId'];
        $ubn = $row['ubn_gebruiker'];
        $levnr = $row['levensnummer'];
        $geslacht = $row['geslacht'];
        $dmaanw = $row['dmaanw'];
        // TODO: (BCB) Extract Method.
        // $fase = getFase($dmaanw, geslacht), of zelfs getFase($row)
        if (isset($dmaanw)) {
            if ($geslacht == 'ooi') {
                $fase = 'moederdier';
                // TODO: (BV) geen elseif, maar else--of hebben schapen nog meer geslachten?
            } elseif ($geslacht == 'ram') {
                $fase = 'vaderdier';
            }
        } else {
            $fase = 'lam';
        }
        // TODO: (BV) de namen 'schaapdm' en 'dmschaap' maken niet duidelijk waarom het er twee zijn, en wat het verschil is. Vertel?
        $schaapdm = $row['schaapdm'];
        $dmschaap = $row['dmschaap'];
        $stalId = $row['stalId']; // Ter controle van eerdere stalId's
        $rel_hrk = $row['rel_herk'];
        $ubn_hrk = $row['ubn_herk'];
        $skip = $row['skip'];
        $fout_db = $row['fout'];
        $foutmeld = $row['foutmeld'];
        $respId = $row['respId'];
        $sucind = $row['sucind'];
        // TODO: (BV) ook hier: Vertel?
        $dmlst = $row['dmlst']; // Laatste datum van het vorige stalId van deze user
        $lstdm = $row['lstdm']; // t.b.v. commentaar

// Controleren of de te melden gegevens de juiste voorwaarde hebben .
        // TODO: (BCB) Extract Method. $waarschuwing = getSkippable($row)
        if (empty($schaapdm) || # datum is leeg
            empty($levnr) || # levensnummer is leeg
            $dmschaap > $today || # datum ligt na vandaag
            (isset($dmlst) && $dmschaap < $dmlst) || # datum ligt voor de laatste datum van het vorige stalId van deze user
            intval(str_replace('-', '', $schaapdm)) == 0 # Van datum naar nummer is 0 of te wel datum = 00-00-0000. Als $dmlst niet bestaat !
        ) {
            $check = 1;
            $waarschuwing = ' Dit dier wordt niet gemeld.';
        } else {
            $check = 0;
            unset($waarschuwing);
        }
// EINDE Controleren of de te melden gegevens de juiste voorwaarde hebben .

// Berichtgeving o.b.v. eigen foute registratie
        if (isset($fout_db)) {
            $foutieve_invoer = $fout_db.' '.$waarschuwing;
        }
// Einde Berichtgeving o.b.v. eigen foute registratie

// Berichtgeving o.b.v. terugkoppeling RVO
        if ($sucind == 'J' && !isset($foutmeld)) {
            $bericht = 'RVO meldt : Melding correct';
        } elseif (isset($foutmeld)) {
            $bericht = 'RVO meldt : '.$foutmeld;
            unset($foutmeld);
        } elseif (isset($respId)) {
            $bericht = 'Resultaat van melding is onbekend';
        }
        // Einde Berichtgeving o.b.v. terugkoppeling RVO ?>

<?php
        // Declaratie HERKOMST
        // TODO: (BCB) Extract Method. Eerst met BV duidelijk krijgen of relRaak nodig is
        // lower(if(isnull(ubn),'6karakters',ubn)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen tblPartij.
        $qryRelatie = ("
        SELECT relId, lower(coalesce(ubn,999999)) ubn, naam
        FROM tblRelatie r
         join tblPartij p on (r.partId = p.partId)
        WHERE p.lidId = '".mysqli_real_escape_string($db, $lidId)."'
         and r.relatie = 'cred' and ubn is not null
         and isnull(r.uitval) and r.actief = 1 and p.actief = 1
        ORDER BY relatie
        ");
        $relatienr = mysqli_query($db, $qryRelatie) or die(mysqli_error($db));
        $index = 0;
        while ($rnr = mysqli_fetch_array($relatienr)) {
            $relId[$index] = $rnr['relId'];
            $relnum[$index] = $rnr['naam'];
            // TODO: (BV) wat is het nut van relRaak? Je kunt toch relId gebruiken?
            $relRaak[$index] = $rnr['relId'];
            $index++;
        }
        unset($index);
        // EINDE Declaratie HERKOMST
?>

<!--
    **************************************
    **       OPMAAK  GEGEVENS           **
    **************************************
-->
<?php
        // display-logica: streepje als een nieuw ubn begint --BCB
if (isset($vorig_ubn) && $vorig_ubn != $ubn) { ?>
<tr><td colspan="15"><hr></td></tr>
<?php } ?>

<tr style = "font-size:15px;" >
<!-- Id -->
<?php
        // TODO: (BV) color heeft geen default-waarde. Dat kost je een notice. Moet-ie 'black' zijn?
        // Dit kan display-logica blijven. Misschien is een css-class beter dan een attribuut-waarde. --BCB
        if ($skip == 1) {
            $color = "#D8D8D8";
        }
?>
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<?php echo $ubn; ?>
 </td>
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<!-- DATUM -->
<?php
        echo $dmlst;
        if ($skip == 1) {
            echo $schaapdm;
        } else {
?>
    <input type = text size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo $schaapdm; ?> > 
<?php
        }
?>
 </td>
<!-- LEVENSNUMMER -->
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<?php
        //Als een eerder stalId bestaat mag het levensnummer niet worden gewijzigd.
        // TODO: (BCB) jammer van de n+1 queries. Kan dit in de rij-query bijgeschoven?
        $vorigStalId = zoek_eerder_stalId($db, $levnr, $stalId);
        if ($skip == 1 || isset($vorigStalId)) {
            echo $levnr;
        } else { ?> 
            <input type = text name = <?php echo " \"txtLevnr_$Id\" value = \"$levnr\" ;"?> size = 12 style = "font-size : 12px;"> 
        <?php
        }
?>
</td>

<td align = "center" style = "color : <?php echo $color; ?>;" >
<?php echo $fase; ?>
</td>

<!-- HERKOMST -->
 <td align = "center" style = "color : <?php echo $color; ?>;" >
<?php
        if ($skip == 1) {
            // Als Herkomst niet bestaat
            echo zoek_naam_partij($db, $rel_hrk);
        } else {
            // Herkomst moet wel bestaan
            # <!-- KZLHERKOMST    -->
            # BCB: het nu volgende blokje maakt een <select>
            # - name: "kzlHerk_$Id"
            # - style: "font-size: 12px"
            # - bevat lege optie
            # - opties value: relId, caption: relnum
            # - selected:
            #   - niet kpnSave? row[rel_hrk] == relRaak .. maar dat is hetzelfde als relId
            #   - wel knpSave? key == POST[<name>]
            #   (TODO: (BCB) knpSave is toch een submit? Die moet gevolgd worden door een redirect, niet direct het formulier renderen)
            #   TODO: (BCB) Extract Method collection_select($name, $collection, $selected, $include_blank, $attributes)
            #               of options_for_select($collection, $selected, $include_blank)
?>
 <select style="width:135;" name= <?php echo "kzlHerk_$Id"; ?> style ="font-size : 12px ;" >
  <option></option>
<?php
            $count = count($relnum);
            for ($i = 0; $i < $count; $i++) {
                $opties = array($relId[$i] => $relnum[$i]);
                // TODO: (BV) je hebt opties net zelf aangemaakt, daar hoef je toch niet doorheen te foreachen? schrijf ipv $key direct $relId[$i], en ipv $waarde $relnum[$i]
                foreach ($opties as $key => $waarde) {
                    // TODO: (BCB) $selected direct toewijzen, hoeft niet met if()
                    $selected = false;
                    if ((!isset($_POST['knpSave_']) && $rel_hrk == $relRaak[$i]) || (isset($_POST["kzlHerk_$Id"]) && $_POST["kzlHerk_$Id"] == $key)) {
                        $selected = true;
                    }
                    echo '<option value="' . $key . '" ' . ($selected ? 'selected' : '') . '>' . $waarde . '</option>';
                }
            }
?>
</select>
<?php
            // EINDE KZLHERKOMST
        } ?>
 </td>

 <td  width = 50 align = "center">
 <!--hidden checkbox, zelfde name: uitgevinkt wordt niet meege-POST, dus de POST ziet dan waarde 0 -->
    <input type = "hidden" size = 1 style = "font-size : 11px;" name = <?php echo " \"chbSkip_$Id\" "; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbSkip_$Id" ; ?> value = 1
<?php echo ($check == 1 || $skip == 1) ? 'checked' : '';
if ($check == 1) {
    echo 'disabled';
} ?>>
 </td>

 <td width = 400 style = "color : red; font-size : 12px;">        
<?php
# <!-- Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->
    # TODO: (BV) $wrong wordt niet gebruikt! Wat is de bedoeling?
        if (empty($schaapdm)) {
            $wrong = "Datum moet zijn gevuld.";
        } elseif (empty($levnr)) {
            $wrong = "Levensnummer moet zijn gevuld.";
        } elseif ($dmschaap > $today) {
            $wrong = "De datum mag niet in de toekomst liggen.";
        } elseif (isset($dmlst) && $dmschaap < $dmlst) {
            $wrong = "De datum mag niet voor ".$lstdm." liggen.";
        }
    # <!-- EINDE Meldingen bij foutieve waardes wanneer deze niet zijn onstaan bij het invoeren binnen MeldGeboortes -->

// TODO: (BV) color wordt hier hergebruikt. Net was het de tekstkleur van een geskipte regel. Hier een andere variabele maken?
        if ($skip == 1) {
            $boodschap = "Verwijderd";
            $color = "black";
        } elseif (isset($bericht)) {
            $boodschap = $bericht;
            $color = "#FF4000";
        } elseif (isset($foutieve_invoer)) {
            $boodschap = $foutieve_invoer;
            // $foutieve_invoer en $wrong kan gelijktijdig van toepassing zijn
            $color = "blue";
        } elseif (strlen($levnr) <> 12 || numeriek($levnr) == 1 || intval($levnr) == 0) {
            $color = 'red';
            $boodschap =  'Levensnummer is onjuist.'.$waarschuwing;
        } else {
            $color = 'red';
            $boodschap = $waarschuwing;
        }

        if ($sucind == 'J' && $skip == 0) {
            $color = "green";
        }
// $sucind van laatste response kan J zijn maar inmiddels ook verwijderd.
        if (isset($boodschap)) { ?>
            <div style = "color : <?php echo $color; ?>;" ><?php echo $boodschap; ?></div>
<?php
        }
        unset($color);
        unset($bericht);
        unset($foutieve_invoer);
        unset($boodschap);
?>
 </td> 
</tr>
<!--    **************************************
            **    EINDE OPMAAK GEGEVENS    **
        ************************************** -->
<?php
        $vorig_ubn = $ubn;
    }
?>    
</table>
</form> 

    </TD>
<?php
    include "menuMelden.php";
} ?>
</tr>

</table>

</body>
</html>
<SCRIPT language="javascript">
$(function(){

    // add multiple select / deselect functionality
    $("#selectall_del").click(function () {
          $('.delete').attr('checked', this.checked);
    });

    // if all checkbox are selected, check the selectall_del checkbox
    // and viceversa
    $(".delete").click(function(){

        if($(".delete").length == $(".delete:checked").length) {
            $("#selectall_del").attr("checked", "checked");
        } else {
            $("#selectall_del").removeAttr("checked");
        }

    });
});
</SCRIPT>
