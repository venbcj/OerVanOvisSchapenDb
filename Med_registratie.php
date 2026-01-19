<?php
require_once("autoload.php");
/* 17-2-14 : schaapgegevens aangepast. vw_Schapen is aangevuld met max bezetId (laatste hoknr). Op deze manier wordt maar 1 regel uit vw_Bezetting gekoppeld met vw_Schapen. Het hok wordt alleen getoond als het dier een lam is.
        Bij keuze moederdier moet per schaap het selectieveld uit staan
  18-2-2014 : reslevnr aangepast. levnr vervangen door _POST['kzlLevnr']
  19-2-14 : Kolom 'ander medicijn' uitgezet omdat weergave niet juist is.
  19-2-14 : Uit kzl type = 'lam' verwijderd. Gevolg is uit reswerknr 'and isnull(tot) and isnull(afsluitdm)' verwijderd. Ook is kolom 'Generatie' toegevoegd
        Post levensnummer via link naar MedOverzSchaap.php
  8-8-2014 : Aantal karakters werknr variabel gemaakt, quotes bij variabelen weggehaald
  11-8-2014 : veld type gewijzigd in fase
  12-10-2014 : Ovv Rina 1e en 2e inenting eruit gehaald
  28-11-2014 Toediening aangepast op Chargenummer. Of te wel inkId
  20-2-2015 : login toegevoegd
  14-11-2015 : naamwijziging van Medicijn registratie naar Medicijn toediening en Keuze medicijn naar Keuze medicijnvoorraad
  8-12-2015 : laatste geboren lam bij moeders tonen hoeveelheid per schaap verplaatst en getotaliseerd
6-1-2016 : Hoknr gewijzigd aar Verblijf */
$versie = '25-11-2016'; /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '28-12-2016'; /* Bij keuze moederdieren wordt standaard chbKeuze aangevinkt */
$versie = "23-1-2017"; /* 18-1-2017 Query's aangepast n.a.v. nieuwe DoelId        22-1-2017 tblBezetting gewijzigd naar tblBezet    23-1-2017 kalender toegevoegd */
$versie = "7-2-2017"; /* de Extra opties bij hok leidde ook bij keuze schaap ook voor tonen van zowel lam als moederdier. Dit is aangepast.    9-2-2017 : foutmelding toegevoegd als schaap niet is geselecteerd.  */
$versie = "17-3-2017"; /* tblPeriode verwijderd     26-3-2017 : vanaf - t/m geboortedatum zoeken toegevoegd */
$versie = "25-3-2018"; /* Keuze moederdier van lammeren in een verblijf is gewijzigd naar keuze volwassen dieren die in het verblijf zitten  */
$versie = "17-6-2018"; /* Registreren van afgevoerde schapen mogelijk gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '6-1-2019'; /* javascript toegevoegd tbv stadat en eenheid wijzigen per medicijn */
$versie = '10-2-2019'; /* zoeken op Halsnr mogelijk gemaakt */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '23-09-2021'; /* func_artikelnuttigen.php toegevoegd. Sql beveiligd met quotes.*/
$versie = '06-11-2023'; /* Bij zoek_einddatum 'and h.skip = 0' toegevoegd */
$versie = '31-12-2023'; /* and h.skip = 0 in een enkele query aangevuld aan tblHistorie */
$versie = "11-03-2024"; /* Bij geneste query uit
    join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
    join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
    I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
    $versie = '30-11-2024'; /* In keuzelijst levensnummer en werknr uitgeschaarde dieren wel tonen. query's m.b.t. afvoer aangevuld met h.actId != 10 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = top > gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-01-2025'; /*h1.actId != 2 verwijderd in de geneste query 'uit' in de query kzl_verblijven */
Session::start();
?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>
<?php
$titel = 'Medicijn toediening';
$file = "Med_registratie.php";
include "login.php";
?>
        <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        $artikel_gateway = new ArtikelGateway();
        // poor man's Dependency Injection
        // aanroepende test kan een schaap_gateway klaarzetten in GLOBALS
        if (empty($schaap_gateway)) {
            $schaap_gateway = new SchaapGateway();
        }
        $stal_gateway = new StalGateway();
        $historie_gateway = new HistorieGateway();
        $reden_gateway = new RedenGateway();
        $bezet_gateway = new BezetGateway();
        include "kalender.php";
        require_once "func_artikelnuttigen.php";
        $vw = $artikel_gateway->zoek_pil_op_voorraad($lidId);
        while ($lin = $vw->fetch_array()) {
            $stdat = str_replace('.00', '', $lin['stdat']);
            $array_eenheid[$lin['artId']] = 'x ' . $stdat . $lin['eenheid'] . ' per schaap';
            // TODO deze include moet buiten de lus, denk ik --BCB
            include "med-registratie.js.php";
        }
        $hok_uitgez = "Alles";
        if (empty($_POST['txtAantal'])) {
            $toedat = 1;
        } else {
            $toedat = $_POST['txtAantal'];
        }
        if (empty($_POST['txtDatum'])) {
            $Datum = '';
        } else {
            $Datum = $_POST['txtDatum'];
        }
        if (empty($_POST['kzlArtikel'])) {
            $kzlArt = '';
        } else {
            $kzlArt = $_POST['kzlArtikel'];
        }
        if (isset($_POST['kzlReden'])) {
            $kzlReden = $_POST['kzlReden'];
        }
        if (isset($_POST['knpToon'])) {
            if (empty($_POST['kzlLevnr']) && empty($_POST['kzlWerknr']) && empty($_POST['kzlHalsnr']) && empty($_POST['chbOoi']) && empty($_POST['kzlHok']) && empty($_POST['txtGeb_van'])) {
                $fout = "Keuze uit schapen is niet gemaakt.";
            } elseif (empty($_POST['kzlArtikel'])) {
                $fout = "Medicijn is niet geselecteerd.";
                if (!empty($_POST['txtGeb_van'])) {
                    $Geb_van = $_POST['txtGeb_van'];
                }
                if (!empty($_POST['txtGeb_tot'])) {
                    $Geb_tot = $_POST['txtGeb_tot'];
                }
            } else {
                // TODO: rename. De bedoeling is een boolean: "moeten we de knop tonen?"
                $knpInsert = "toonknpInsert";
            }
        }
        if (isset($_POST['knpInsert'])) {
            if (empty($_POST['chbKeuze'])) {
                $fout = "Er is geen schaap geselecteerd.";
            } else {
                // Gegevens van artikel ophalen
                [$stdrd, $naam, $eenh, $stdat] = $artikel_gateway->zoek($kzlArt);
                // EINDE Gegevens van artikel ophalen
                // Berekening Totaal hoeveelheid toe te dienen medicijnen
                $rows_lev = $schaap_gateway->tel_bij_lid_en_levensnummer($lidId, implode(',', $_POST['chbKeuze']));
                $nut_totaal = $stdrd * $toedat * $rows_lev;
                // EINDE Berekening Totaal hoeveelheid toe te dienen medicijnen
                $stock = $artikel_gateway->voorraad($kzlArt);
                if (empty($_POST['txtDatum'])) {
                    $fout = "Datum is niet bekend.";
                    if (!empty($_POST['kzlArtikel'])) {
                        $knpInsert = "toonknpInsert";
                    }
                } elseif (empty($_POST['txtAantal'])) {
                    $fout = "Het aantal is niet bekend.";
                    if (!empty($_POST['kzlArtikel'])) {
                        $knpInsert = "toonknpInsert";
                    }
                } elseif (empty($_POST['kzlReden'])) {
                    $fout = "De reden is niet geselecteerd.";
                    if (!empty($_POST['kzlArtikel'])) {
                        $knpInsert = "toonknpInsert";
                    }
                } elseif (empty($_POST['chbKeuze'])) {
                  // TODO: (BV) #0004158 dit is dode code. Je komt alleen in deze tak als er wel een keuze is aangevinkt.
                    // @codeCoverageIgnoreStart
                    $fout = "Er is geen schaap geselecteerd.";
                    if (!empty($_POST['kzlArtikel'])) {
                        $knpInsert = "toonknpInsert";
                    }
                    // @codeCoverageIgnoreEnd
                } elseif ($nut_totaal > $stock) {
                  // Controle van het toedien aantal tov het voorraad aantal
                    $fout = "U kunt geen $nut_totaal $eenh toedienen er is nl. nog maar $stock $eenh beschikbaar.";
                } else {
                        // toevoegen medicijn
                        $kzlArt = "$_POST[kzlArtikel]";
                        $date = date_create($_POST['txtDatum']);
                        $fldDay = date_format($date, 'Y-m-d');
                        //Aantal daadwerkelijk ingelezen
                        $ingelezen = 0;
                        // Doorlopen van geselecteerde schapen
                        $vw = $schaap_gateway->zoek_per_levensnummers(implode(',', $_POST['chbKeuze']));
                    while ($s = $vw->fetch_assoc()) {
                        $schaapId = $s['schaapId'];
                        $levnsr = $s['levensnummer'];
                        $zoek_stalId = $stal_gateway->zoek_laatste_stal($lidId, $schaapId);
         // beetje raar, deze while. De query doet max en levert dus 1 record.
                        while ($s = $zoek_stalId->fetch_assoc()) {
                                    $stalId = $s['stalId'];
                                    // Zoek naar einddatum in geval schaap reeds is afgevoerd
                                    unset($dmafv);
                                    [$dmafv, $afvdm] = $historie_gateway->zoek_einddatum($stalId);
                                    // Einde Zoek naar einddatum in geval schaap reeds is afgevoerd
                            if (isset($dmafv) && $fldDay > $dmafv) {
                                $opm = $levnsr . ' de datum mag niet na de afvoerdatum ' . $afvdm . ' liggen.\n';
                                if (isset($melding)) {
                                    // TODO (BV) dit is dode code. $melding wordt alleen gezet in importRespons, en die wordt hier niet gebruikt.
                                            $melding .= $opm; // @codeCoverageIgnore
                                } else {
                                    $melding = $opm;
                                }
                            } else {
                 // Vervolgen toevoegen medicijn
                 // Aanvullen tblHistorie
                                $historie_gateway->medicijn_invoeren($stalId, $fldDay);
                 // zoeken laatste hisId van ingelezen historie t.b.v. tblNuttig ($insert_tblHistorie)
                                $hisId = $historie_gateway->zoek_laatste($stalId, $fldDay);
                 // hoeveelheid per dier
                                $toedtotal = $stdat * $toedat;
                                inlezen_pil($hisId, $kzlArt, $toedtotal, $fldDay, $kzlReden);
                                $ingelezen++;
                            }
                        }
                    }
                    $echtGenuttigd = $stdrd * $toedat * $ingelezen;
                    if ($ingelezen == 1) {
                        $meervoud = ' dier ';
                    } else {
                               $meervoud = ' dieren totaal ';
                    }
                    $goed = "Er is bij " . $ingelezen . $meervoud . $echtGenuttigd . $eenh . " " . $naam . " toegediend";
                }
                if (isset($melding)) {
                    $fout = 'De volgende dieren hebben geen medicatie gekregen !!\n' . $melding . '\n' . $goed;
                }
            }
        }
        ?>
<table border = 0>
<!--    **************************************
    **     GEGEVENS TBV MEDICIJN    **
    ************************************** -->
    <form action="Med_registratie.php" method="post">
<tr><td colspan = 5 style = "font-size : 18px;"><b> Keuze medicijnvoorraad </b></td></tr>
<tr>
<td><i><sub> Datum </sub></i></td>
<td><i><sub>medicijn &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</sub></i><i style = "font-size:12px;"><sub>(voorraad)</sub></i> </td>
<td><i><sub> Reden </sub></i></td>
<td colspan = 2 ><i><sub> Aantal</sub></i></td>
</tr>
<tr>
<td><input id = "datepicker1" type= text name = "txtDatum" size = "8" value = <?php echo "$Datum"; ?>
></td>
        <?php
        // Artikelgegevens ophalen van het gekozen artikel
        $queryEenheid = $artikel_gateway->zoek_eenheid($kzlArt);
        while ($qryeenh = $queryEenheid->fetch_assoc()) {
            $eheid = $qryeenh['eenheid'];
            $stadrd = str_replace('.00', '', $qryeenh['stdat']);
            $eenheid = $stadrd . $eheid;
        }
        ?>
<td>
        <?php
/* KZLMEDICIJN
Medicijnen met artId als key. deze inkId is de laagste inkId waarvan nog voorraad is */
        $zoek_artId_op_voorraad2 = $artikel_gateway->zoek_pil_op_voorraad($lidId);
        $name = "kzlArtikel";
        ?>
<select id = "artikel" name="kzlArtikel" width=250 onchange = "eenheid_artikel()" >";
    <option></option>
        <?php
        while ($row = $zoek_artId_op_voorraad2->fetch_array()) {
            $vrd = str_replace('.00', '', $row['vrdat']);
            $stdrd = str_replace('.00', '', $row['stdat']);
            $kzlkey = "$row[artId]";
            $kzlvalue = "$row[naam] &nbsp; per $stdrd $row[eenheid] &nbsp; ($vrd $row[eenheid])";
            include "kzl.php";
        }
        // EINDE KZLMEDICIJN
        ?>
</select></td>
<td>
        <?php
        // kzlReden
        $kzl_redenen = $reden_gateway->lijst_voor($lidId);
        $name = "kzlReden";
        $width = 200 ;
        ?>
<select name="kzlReden" style="width: <?php echo $width; ?> ;" >
 <option></option>
        <?php
        while ($row = $kzl_redenen->fetch_array()) {
            $kzlkey = "$row[reduId]";
            $kzlvalue = "$row[reden]";
            include "kzl.php";
        }
        // EINDE kzlReden
        ?>
</select></td>
<!-- Keuze 2e inenting -->
 <td>
    <input type = "text" name = "txtAantal" size = "1" value = <?php echo "$toedat";     ?>
>
 </td>
 <td style = "font-size:13px;" ><p  id="aantal" >
        <?php
        if (isset($eenheid)) {
            echo 'x ' . $eenheid . ' per schaap';
        } else {
            echo 'x';
        }
        ?>
 </p>
 </td>
        <?php
        if (!empty($knpInsert) || isset($fout)) {
            ?>
 <td></td>
 <td align = "center"><input type='submit' name='knpInsert' value='Toedienen'></td>
            <?php
        }
        ?>
</tr>
</table>
<hr>
        <?php

        /*******************************************
         **    EINDE GEGEVENS TBV MEDICIJN    **
         ********************************************
         *
         *******************************************
         **    GEGEVENS TBV SCHAAP ZOEKEN    **
         ********************************************/

        ?>
<table border = 0>
<tr>
 <td colspan = 4 style = "font-size : 18px;"><b> Keuze uit schapen </b></td>
  <td width = 35 ></td>
 <td colspan="2" align="center"><i><sub></sub></i></td>
 <td></td>
 <td width = 35 ></td>
 <td><i><sub>Opties bij keuze verblijf</sub></i></td>
 <td width = 35 ></td>
 <td><i><sub>Incl. afgevoerde dieren</sub></i></td>
</tr>
<tr>
 <td align = "center"><i><sub> alle moeders </sub></i> </td>
 <td><i><sub> Levensnummer </sub></i> </td>
 <td><i><sub> Werknr </sub></i> </td>
 <td><i><sub> Halsnr </sub></i> </td>
 <td width = 35 ></td>
 <td align="center"><i><sub>Geboren vanaf</sub></i></td>
 <td align="center"><i><sub>tot en met</sub></i></td>
 <td width = 35 ></td>
 <td><i><sub> Verblijf</sub></i><i style = "font-size:12px;"><sub> (aantal in verblijf) </sub></i> </td>
 <td><sub><input type = radio name = 'radHok' value = 1
        <?php
        if (!isset($_POST['knpToon']) || $_POST['radHok'] == 1) {
            echo "checked";
        }
        ?>
title = "Toont alleen lammeren uit gekozen verblijf"> Lammeren </sub></td>
 <td></td>
 <td><sub><input type = radio name = 'radAfv' value = 0
        <?php
        if (!isset($_POST['knpToon']) || $_POST['radAfv'] == 0) {
            echo "checked";
        }
        ?>
        title = "Alleen dieren van stallijst"> Nee </sub>
     <sub><input type = radio name = 'radAfv' value = 1
        <?php
        if ((isset($_POST['knpToon']) || isset($_POST['knpVervers'])) && $_POST['radAfv'] == 1) {
            echo "checked";
        }
        ?>
        title = "Alleen dieren van stallijst"> Ja </sub></td>
</tr>
<tr>
 <td align = "center" > <input type = checkbox name = "chbOoi" value = 1 > </td>
 <td>
        <?php
        $radAfv = 0;
        if (isset($_POST['radAfv'])) {
            $radAfv = $_POST['radAfv'];
        }
        $zoek_levensnummer = $schaap_gateway->zoek_medicatie_lijst($lidId, $_POST['radAfv'] ?? false);
        ?>
 <select name="kzlLevnr"  width=110 >
 <option></option>
        <?php
        while ($row = $zoek_levensnummer->fetch_array()) {
            $opties = array($row['schaapId'] => $row['levensnummer']);
            foreach ($opties as $key => $waarde) {
                $keuze = '';
                if (isset($_POST['kzlLevnr']) && $_POST['kzlLevnr'] == $key) {
                    $keuze = ' selected ';
                }
                echo '<option value="' . $key . '" ' . $keuze . '>' . $waarde . '</option>';
            }
        }
        ?>
</select>
 </td>
 <td>
        <?php
        // Einde kzlLevensnummer
        // kzlWerknr
        $zoek_werknummer = $schaap_gateway->zoek_medicatielijst_werknummer($lidId, $Karwerk, $radAfv);
        $name = "kzlWerknr";
        $width = 25 + (8 * $Karwerk) ;
        ?>
<select name="kzlWerknr" style="width: <?php echo $width; ?>">
 <option></option>
        <?php
        while ($row = $zoek_werknummer->fetch_array()) {
            $kzlkey = "$row[schaapId]";
            $kzlvalue = "$row[werknr]";
            include "kzl.php";
        }
        ?>
</select>
 </td>
 <td>
        <?php
        // Einde kzlWerknr
        // kzlHalsnr
        $zoek_halsnr = $stal_gateway->zoek_kleuren_halsnrs($lidId);
        $name = "kzlHalsnr";
        $width = 25 + (8 * $Karwerk) ;
        ?>
<select name="kzlHalsnr" style="width: <?php echo $width; ?>">
 <option></option>
        <?php
        while ($row = $zoek_halsnr->fetch_array()) {
            $kzlkey = "$row[schaapId]";
            $kzlvalue = "$row[halsnr]";
            include "kzl.php";
            // Einde kzlHalsnr
        }
        ?>
</select>
 </td>
 <td width = 35 ></td>
 <td><input id = "datepicker2" type= text name = "txtGeb_van" size = "8" value =
        <?php
        if (isset($Geb_van)) {
            echo "$Geb_van";
        }
        ?>
></td>
            <td><input id = "datepicker3" type= text name = "txtGeb_tot" size = "8" value =
        <?php
        if (isset($Geb_tot)) {
            echo "$Geb_tot";
        }
        ?>
></td>
 <td width = 35 ></td>
 <td>
        <?php
     //Verblijf zoeken
        $kzl_verblijven = $bezet_gateway->zoek_verblijven($lidId);
        $name = "kzlHok";
        $width = 100 ;
        ?>
<select name=<?php echo"$name"; ?>
style="width:<?php echo "$width"; ?>
;\" >";
 <option></option>
        <?php
        while ($row = $kzl_verblijven->fetch_array()) {
            $kzlkey = "$row[hokId]";
            $kzlvalue = "$row[hoknr] &nbsp; ($row[nu])";
            include "kzl.php";
        }
        // EINDE Verblijf zoeken
        ?>
</select>
 </td>
 <td><sub><input type = radio name = 'radHok' value = 2
        <?php
        if (isset($_POST['knpToon']) && $_POST['radHok'] == 2) {
            echo "checked";
        }
        ?>
title = "Toont alleen moederdieren van de lammeren uit gekozen verblijf"> Volwassen dieren </sub></td>
 <td></td>
 <td><input type = "submit" name="knpVervers" style = "font-size : 10px;" value = "Ververs"></td>
</tr>
<tr>
 <td colspan = 9 align = "center"><input type = "submit" name="knpToon" value = "toon"></td>
 <td><sub><input type = radio name = 'radHok' value = 3
        <?php
        if (isset($_POST['knpToon']) && $_POST['radHok'] == 3) {
            echo "checked";
        }
        ?>
title = "Toont zowel lammeren als hun moederdieren uit gekozen verblijf"> Beiden </sub></td>
</tr>
</table>
<!--
    **************************************************
    **    EINDE GEGEVENS TBV SCHAAP ZOEKEN    **
    **************************************************
    *
    ********************************************
    **    MEDICIJNREGISTRATIE TONEN    **
    ********************************************
-->
        <?php
    // Ophalen en tonen van dieren o.b.v. ingevulde keuzelijst(en)
        if (!empty($_POST['kzlArtikel']) && (!empty($_POST['kzlLevnr']) || !empty($_POST['kzlWerknr']) || !empty($_POST['kzlHalsnr']) || !empty($_POST['chbOoi']) || !empty($_POST['kzlHok']) || !empty($_POST['txtGeb_van']))) {
            [$filter, $filt_mdr] = $schaap_gateway->getMedicatieWhere($_POST);
            if (isset($filt_mdr)) {
                /*$where_mdr = $filt_mdr;*/
            }
            if (isset($where_mdr)) {
                // Geneste query t.b.v. het hok (aglias b) is nodig. Bij zoeken op hok moet $filter op betreffende lammeren filteren.
                // Zonder $reshok worden alle schapen getoond en is $levnr_mdr dus niet leeg !!
                $zoek_aanwezig_moeder = $schaap_gateway->zoek_aanwezig_moeder($lidId, $where_mdr);
                while ($kkop = $zoek_aanwezig_moeder->fetch_assoc()) {
                    $levnr_mdr = $kkop['levensnummer'];
                }
            }
            include "med-registratie-toggle.js.php";
            // TODO: #0004159 onclick aanhangen met event listener (jquery?), niet in html
            ?>
<table id="schapen" border="0">
<tr height = 30><td></td></tr>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;" width = 80 height = 30 ><input type="checkbox" onClick="toggle(this)" /> </th>
 <th style = "text-align:center;"valign= bottom width= 80>Levensnummer<hr></th>
 <th style = "text-align:center;"valign= bottom width= 80>Geboorte datum<hr></th>
 <th style = "text-align:center;"valign="bottom"; >Generatie<hr></th>
 <th width = 1></th>
            <?php
            if (!empty($_POST['chbOoi']) || isset($levnr_mdr)) {
                $veld = 'Laatst geboren lam';
            } else {
                  $veld = 'Verblijf';
            }
            ?>
 <th style = "text-align:center;"valign="bottom"; ><?php echo $veld;     ?>
<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom"; width= 140 > Historie <hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 200 ></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 60></th>
 <th width = 60></th>
 <th style = "text-align:center;"valign="bottom";width= 80></th>
 <th width = 600></th>
</tr>
            <?php
            $zoek_schaapgegevens = $schaap_gateway->zoek_schaapgegevens($lidId, $Karwerk, $radAfv, $filter);
            while ($row = $zoek_schaapgegevens->fetch_assoc()) {
                // nodig bij doorklikken naar historie medicatie
                $schaapId = $row['schaapId'];
                $levnr = $row['levensnummer'];
                $werknr = $row['werknr'];
                $gebdm = $row['gebdm'];
                $geslacht = $row['geslacht'];
                $aanw = $row['aanw'];
                if (isset($aanw)) {
                    if ($geslacht == 'ooi') {
                        $fase = 'moederdier';
                    } elseif ($geslacht == 'ram') {
                        $fase = 'vaderdier';
                    }
                } else {
                    $fase = 'lam';
                }
                $hoknr = $row['hoknr'];
                $lstdm = $row['lstgeblam'];
                $actId = $row['actId'];
                $afvoer = $row['af'];
                if ($actId == 10) {
                    $afvoer = 0;
                }
                if (!isset($schaapId)) {
                    $fout = "Er zijn geen resultaten gevonden";
                } else {
                    ?>
<tr align = "center">
 <td width = 0> </td>
 <td width = 90> <input type = checkbox name = "chbKeuze[]" value = <?php echo $levnr;     ?>
>
 </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr;     ?>
<br> </td>
 <td width = 100 style = "font-size:15px;">
                    <?php
                    if (isset($gebdm)) {
                        echo $gebdm;
                    }
                    ?>
<br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $fase;     ?>
<br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;">
                    <?php
                    if (isset($lstdm) && $afvoer == 0) {
                        echo $lstdm;
                    } else {
                        echo $hoknr;
                    }
                    ?>
<br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;">
                    <?php
                    // Zoeken naar historie medicijnen per schaap
                    $medic = $schaap_gateway->tel_medicijn_historie($lidId, $schaapId);
                    if (!empty($medic)) {
                        echo View::link_to('historie', 'MedOverzSchaap.php?pstId=' . $schaapId, ['style' => 'color: blue']);
                    }
                    ?>
<br> </td>
</tr>
                    <?php
                }
            }
            ?>
</table>
            <?php
        }
        ?>
<!--
    **************************************************
    **    EINDE MEDICIJNREGISTRATIE TONEN    **
    ***************************************************-->
</form>
    </TD>
        <?php
    } else {
        ?>
<img src="med_registratie_php.jpg"  width="970" height="550"/>
        <?php
    }
    include "menu1.php";
}
?>
</body>
</html>
