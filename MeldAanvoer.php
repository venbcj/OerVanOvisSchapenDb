<?php

require_once("autoload.php");

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
$titel = 'Melden Aanvoer';
$file = "Melden.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
    include "responscheck.php";

    if (isset($_POST['knpSave_'])) {
        /* $code bestaat ook in responscheck.php */
        $code = 'AAN';
        include "save_melding.php";
        Response::redirect(Url::getCurrentUrl());
        return;
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
            $fh = fopen(dirname(__FILE__) . '/BRIGHT/' . alias_voor_lid($db, $lidId) . '_' . $reqId . '_request.txt', 'w');
            /* insert field values into data.txt */
            $qry_txtRequest_RVO = aanvoer_request_rvo_query($db, $reqId);
            /* Herkomst (ubn_herk) is niet verplicht te melden */
            while ($row = $qry_txtRequest_RVO->fetch_row()) {

                 fwrite($fh, implode(';', $row) . PHP_EOL);
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
        $name = 'kzlDef_';
        $collection = array('N'=>'Controle', 'J'=>'Vastleggen');
        $selected = (isset($_POST['kzlDef_'])) ? $_POST['kzlDef'] : $def;
        // selected is afgeleid van:
        // if ((!isset($_POST['knpSave_']) && $def == $key) || (isset($_POST["kzlDef_"]) && $_POST["kzlDef_"] == $key)) {
        View::select($name, $collection, false, $selected, ['style' => 'width: 100; font-size: 13px']);
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
    while ($row = $zoek_meldregels->fetch_assoc()) {
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
        // TODO: (BV) #0004149 de namen 'schaapdm' en 'dmschaap' maken niet duidelijk waarom het er twee zijn, en wat het verschil is. Vertel?
        // BV:
        // ......dm is bij mij de leesbare datumnotatie dd-mm-jjjj.
        // dm...... is bij mij de database notatie jjjj-mm-dd
        //
        // BCB: Juist. Ik zie dat als volgt.
        //
        // Er is 1 bron voor het gegeven: de datum uit tblHistorie. Als je die ophaalt, kun je 'm vergelijken
        //   met een andere datum (na vandaag, voor de "laatste datum van het vorige stalId").
        //   Ik zou het nog schoner vinden om er een timestamp, of een Date-object van te maken, maar dat is iets voor later.
        //
        // schaapdm is een presentatie-gegeven, afgeleid van dmschaap. In plaats van daar een variabele voor te maken,
        //   kun je de dmschaap ook opnieuw formatteren bij het afdrukken.
        //   echo mensdatum($dmschaap);
        //
        //   function leesdatum($dmschaap) {
        //     return date('d-m-Y', strtotime($dmschaap)); BV Deze functie heb ik in basisfunctie gemaakt
        //   }
        //
        // De code doet ook *beslissingen* met schaapdm, maar die kunnen allemaal net zo goed met dmschaap gedaan worden.
        // Als de een leeg is, is de ander ook leeg, bijvoorbeeld.
        $dmschaap = $row['dmschaap'];
        $stalId = $row['stalId']; // Ter controle van eerdere stalId's
        $rel_hrk = $row['rel_herk'];
        $ubn_hrk = $row['ubn_herk'];
        $skip = $row['skip'];
        $fout_db = $row['fout'];
        $foutmeld = $row['foutmeld'];
        $respId = $row['respId'];
        $sucind = $row['sucind'];
        $dmlst = $row['dmlst']; // Laatste datum van het vorige stalId van deze user

// Controleren of de te melden gegevens de juiste voorwaarde hebben .
        // TODO: (BCB) Extract Method. $waarschuwing = getSkippable($row)
        if (empty($dmschaap) || # datum is leeg  
            empty($levnr) || # levensnummer is leeg
            $dmschaap > $today || # datum ligt na vandaag
            (isset($dmlst) && $dmschaap < $dmlst) || # datum ligt voor de laatste datum van het vorige stalId van deze user
            intval(str_replace('-', '', $dmschaap)) == 0 # Van datum naar nummer is 0 of te wel datum = 00-00-0000. Als $dmschaap niet bestaat !
            //                          ^^^BCB wat heeft $dmlst hier nu mee te maken? 
            // BV $dmlst moest $dmschaap volgens mij
            // als de datum in de tabel niet is ingevuld, is dmschaap null. Dat vind ik eenvoudiger te lezen dan intval(str_replace).
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
            $partij_gateway = new PartijGateway();
        $relatienr = $partij_gateway->relatienummers($lidId);
        $index = 0;
        while ($rnr = $relatienr->fetch_array()) {
            $relId[$index] = $rnr['relId'];
            $relnum[$index] = $rnr['naam'];
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
            echo leesdatum($dmschaap);
        } else {
?>
    <input type = text size = 9 style = "font-size : 12px;" name = <?php echo " \"txtSchaapdm_$Id\" ;"?> value = <?php echo leesdatum($dmschaap); ?> > 
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
                    if ((!isset($_POST['knpSave_']) && $rel_hrk == $relId[$i]) || (isset($_POST["kzlHerk_$Id"]) && $_POST["kzlHerk_$Id"] == $key)) {
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
        if (empty($dmschaap)) {
            $wrong = "Datum moet zijn gevuld.";
        } elseif (empty($levnr)) {
            $wrong = "Levensnummer moet zijn gevuld.";
        } elseif ($dmschaap > $today) {
            $wrong = "De datum mag niet in de toekomst liggen.";
        } elseif (isset($dmlst) && $dmschaap < $dmlst) {
            $wrong = "De datum mag niet voor ".leesdatum($dmlst)." liggen.";
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
        } elseif (strlen($levnr) <> 12 || Validate::numeriek($levnr) == 1 || intval($levnr) == 0) {
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

<?php
    include "select-all.js.php";
?>
</body>
</html>
