<?php 

require_once("autoload.php");

$versie = '03-09-2017';  /* aangemaakt */
$versie = '20-03-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-01-2019'; /* alles aan- en uitzetten met javascript */
$versie = '07-03-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '24-04-2020'; /* url Javascript libary aangepast */
$versie = '02-12-2023'; /* Toepassing bij reader Agrident mogelijk gemaakt */
$versie = '31-12-2023'; /* op 1 plek and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '24-11-2024'; /* subquery haf aangepast. Er werd gezocht naar max(hisId). Laatste hisId hoeft niet afvoer te zijn. sql beveiligd met quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */

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
$titel = 'Inlezen Wegingen';
$file = "InsWegen.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
$impagrident_gateway = new ImpAgridentGateway();

If (isset($_POST['knpInsert_']))  {
    include "url.php";
    include "post_readerWgn.php"; #Deze include moet voor de vervversing in de functie header()
}

// Aantal nog in te lezen WEGINGEN
$schaap_gateway = new SchaapGateway();
$aantwg = $schaap_gateway->zoek_wegingen($lidId);
// EINDE Aantal nog in te lezen WEGINGEN

$velden = "str_to_date(rd.datum,'%Y-%m-%d') sort , rd.datum, rd.Id readId, rd.levensnummer levnr, rd.gewicht kg,
 dup.dubbelen,
 s.schaapId, s.levensnummer, s.geslacht,
 lower(haf.actie) actie, haf.af, haf.datum dmafv,
 date_format(hlst.datum,'%d-%m-%Y') weegdm, hlst.datum dmweeg, ouder.datum dmaanw,
 lstday.datum dmlst ";
$tabel = $impagrident_gateway->getInsWegenFrom();
$WHERE = $impagrident_gateway->getInsWegenWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY sort, rd.Id");

?>

<table border = 0>
<tr> <form action="InsWegen.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Weeg<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Gewicht<hr></th>
 <th>Generatie<hr></th>
 <th colspan = 2 ><hr></th>
</tr>

<?php
if(isset($data))  {    foreach($data as $key => $array)
    {
$fase = '';
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$dm       = date('Y-m-d', strtotime($date));
    
    $Id = $array['readId'];
    $schaapId = $array['schaapId'];
    $levnr = $array['levnr'];
    $levnr_exist = $array['levensnummer'];
    $kg = $array['kg'];
    $geslacht = $array['geslacht'];
    $dmaanw = $array['dmaanw']; if(isset($dmaanw) && isset($schaapId)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else if(isset($schaapId)) { $fase = 'lam';} 
    $status = $array['actie'];
    $dmafv = $array['dmafv']; if(isset($dmafv))    { $afvdm = date('d-m-Y', strtotime($dmafv)); } // weeg datum mag niet na afvoerdatum liggen


// Controleren of ingelezen waardes correct zijn.
if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtWeegdag_$Id"]; $kg = $_POST["txtKg_$Id"];
    $makeday = date_create($_POST["txtWeegdag_$Id"]); $dm =  date_format($makeday, 'Y-m-d');
}

unset($foutbericht);

     if (!isset($schaapId))                         { $foutbericht = 'Levensnummer onbekend';}
else if(empty($datum))                                 { $foutbericht = 'De datum ontbreekt.'; } 
else if(empty($kg))                                     { $foutbericht = 'Gewicht is onbekend.'; } 
else if(isset($dmafv) && $dm > $dmafv)         { $foutbericht = 'Dit dier is ' . $status . '.'; }


if    (isset($foutbericht)) {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes corretc zijn.  

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = "center">

    <input type = hidden size = 1 name = <?php echo "chbkies_$Id"; ?> value = 0 > <!-- hiddden -->
    <input type = checkbox           name = <?php echo "chbkies_$Id"; ?> value = 1 
      <?php echo $cbKies == 1 ? 'checked' : ''; /* Als voorwaarde goed zijn of checkbox is aangevinkt */

      if ($oke == 0) /*Als voorwaarde niet klopt */ { ?> disabled <?php } else { ?> class="checkall" <?php } /* class="checkall" zorgt dat alles kan worden uit- of aangevinkt*/ ?> >
    <input type = hidden size = 1 name = <?php echo "laatsteOke_$Id"; ?> value = <?php echo $oke; ?> > <!-- hiddden -->
 </td>
 <td align = "center">
    <input type = hidden size = 1 name = <?php echo "chbDel_$Id"; ?> value = 0 >
    <input type = checkbox class="delete" name = <?php echo "chbDel_$Id"; ?> value = 1 <?php if(isset($cbDel)) { echo $cbDel == 1 ? 'checked' : ''; } ?> >
 </td>
 <td>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtWeegdag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
 
 <td> <?php echo $levnr; ?> 
 </td>
    
 <td style = "font-size : 9px;"> 
    <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id"; ?> value = <?php echo $kg; ?> > </td>

 <td align="center"> <?php echo $fase; ?> 
 </td>

    

 <td width = 200 style = "color : red">
<!-- Foutmeldingen --> <?php 
     if    (isset($foutbericht)) { echo $foutbericht; } ?>
 </td>    
</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php } 
} //einde if(isset($data)) ?>
</table>
</form> 



    </TD>
<?php
include "menu1.php"; }

include "select-all.js.php";
?>
</tr>

</table>

</body>
</html>
