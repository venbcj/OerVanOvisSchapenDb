<?php

require_once("autoload.php");

/* 
11-12-2014 gemaakt 
8-3-2015 : Login toegevoegd */
$versie = '24-2-2017'; /* Aangpast na.v. Release 2 of wel nieuwe databasestructuur */
$versie = '12-3-2017'; /* Verwijderen mogelijk gemaakt */
$versie = '29-7-2017'; /* toedienen bij afgevoerden mogelijk gemaakt */
$versie = '25-2-2018'; /* standaard hoeveelheid gebasserd op combireden */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '14-11-2020'; /* Onderschied gemaakt tussen reader Agrident en Biocontrol */
$versie = '15-01-2021'; /* Toedien aantal uit tabel impAgrident gehaald */
$versie = '07-09-2021'; /* In query's zoek_afvoerdatum en zoek_fase h.skip = 0 in where clause toegevoegd */
$versie = '22-09-2021'; /* func_artikelnuttigen.php toegevoegd */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = "top"> 31-12-24 include login voor include header gezet */
$versie = '15-01-2025'; /*  and isnull(st.rel_best) toegevoegd aan opvragen van gegevens uit tabel impAgrident zodat stalId's van uitgeschaarden niet worden getoond */
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
$titel = 'Inlezen Medicatie';
$file = "InsMedicijn.php";
include "login.php"; ?>

            <TD valign = "top">
<?php 
if (Auth::is_logged_in()) {
    $impagrident_gateway = new ImpAgridentGateway();
    $schaap_gateway = new SchaapGateway();
    $stal_gateway = new StalGateway();
    $historie_gateway = new HistorieGateway();
    $inkoop_gateway = new InkoopGateway();
    $reden_gateway = new RedenGateway();

require_once "func_artikelnuttigen.php";

If (isset ($_POST['knpInsert_'])) {
    include "post_readerMed.php"; #Deze include moet voor de vervversing in de functie header()
    } 

if($reader == 'Agrident') {
$velden = "rd.Id readId, date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr, NULL scan, 
    s.schaapId, rd.artId, rd.toedat, round(i.stdat) stdat, i.eenheid, rd.reden reduId, i.actief a_act, 
    ru.pil r_act, i.inkId, i.vrdat";

    $tabel = $impagrident_gateway->getInsMedicijnAgridentFrom();
    $WHERE = $impagrident_gateway->getInsMedicijnAgridentWhere($lidId);
    $order_by = "ORDER BY sort, rd.Id";
} else {
$velden = "rd.readId, str_to_date(rd.datum,'%Y/%m/%d') sort, rd.datum, rd.levnr_pil levnr, rd.reden_pil scan, 
    s.schaapId, cr.artId, 1 toedat, round(cr.stdat) stdat, i.eenheid, cr.reduId, cr.actief a_act, 
    cr.pil r_act, i.inkId, i.vrdat";

    $tabel = $impagrident_gateway->getInsMedicijnBiocontrolFrom();
    $WHERE = $impagrident_gateway->getInsMedicijnBiocontrolWhere($lidId);
    $order_by = "ORDER BY sort, rd.readId";
}

include "paginas.php";
$data = $paginator->fetch_data($velden, $order_by);

?>

<table border = 0>
<form action="InsMedicijn.php" method = "post">

<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Toedien<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Medicijn<hr></th>
 <th>Aantal<hr></th>
 <th>hoeveel<br> heid<hr></th>
 <th>Eenheid<hr></th>
 <th>Reden<hr></th>
 <th>Status<hr></th>
</tr>
<?php 

if(isset($data))  {    foreach($data as $key => $array)
    {
        $var = $array['datum'];
$dm = str_replace('/', '-', $var);
$dag = date('d-m-Y', strtotime($dm));
$date  = date('Y-m-d', strtotime($dm));
    
    $Id = $array['readId'];
    $levnr = $array['levnr'];
    $scan = $array['scan']; # het scannummer uit het veld reden_pil in tabel impReader
    $schaapId = $array['schaapId']; 
    //$inkId = $array['inkId']; #InkId uit vw_Voorraad indien voorradig anders uit tblInkoop
    $artId_rd = $array['artId']; #Artikel uit impAgrident of uit tblCombiReden
    $aantal = $array['toedat']; #Toedien aantal uit impAgrident
    $stdat = $array['stdat']; #stdat aantal uit tblCombiReden
    $eenheid = $array['eenheid']; 
    $reduId = $array['reduId']; /*Reden uit tblCombiReden*/
    $p_act = $array['a_act'];
    $r_act = $array['r_act'];
    $vrrd = $array['vrdat'];

    $kzlArt = $artId_rd;
    $kzlRedu = $reduId;
    
if(isset($schaapId)) {
    $fs = $schaap_gateway->zoek_fase($lidId, $schaapId);
        $gevonden = $fs['schaapId'];    if(isset($gevonden)) { $fase = 'lam'; }
        $sekse = $fs['geslacht'];
        $prnt = $fs['prnt'];     if(isset($prnt)) { if($sekse = 'ooi') { $fase = 'moederdier'; } else if($sekse = 'ram') { $fase = 'vaderdier'; } }
        $weg = $fs['s_af']; if(isset($weg)) { $fase = 'afgevoerd'; }

// Zoek op afvoerdatum ter controle op toedien datum
        $stalId = $stal_gateway->zoek_laatste_stal_medicijn($schaapId);
        [$dmafv, $afvdm] = $historie_gateway->zoek_afvoerdatum($stalId);
// Einde Zoek op afvoerdatum ter controle op toedien datum
}    

// De voorwaarden om in te kunnen lezen. 
if (isset($_POST['knpVervers_'])) {

    $dag = $_POST["txtDatum_$Id"];
        $makedate = date_create($dag);
        $date =  date_format($makedate, 'Y-m-d');
    $kzlArt = $_POST["kzlPil_$Id"];
    $aantal = $_POST["txtAantal_$Id"];
    $reduId = $_POST["kzlReden_$Id"];
    
    if(empty($kzlArt)) {
        $vrrd = '';
    } else {
        [$vrrd, $p_act] = $inkoop_gateway->zoek_voorraad($lidId, $kzlArt);
    }

    if(empty($reduId)) {
        $r_act = '';
    } else {
        $r_act = $reden_gateway->zoek_reden_actief($lidId, $reduId);
    }
    } 


// Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
if (!empty($kzlArt)) {
    [$stdat, $eenheid] = $inkoop_gateway->porties($lidId, $kzlArt);
}
// Einde Als medicijn uit Reader niet wordt gevonden of medicijn wordt aangepast moet $stdat en $eenheid opnieuw gezocht worden.
    
If     ( empty($fase)                       || /*levensnummer moet bestaan */    
        empty($dag)                        || # of datum is leeg
        empty($kzlArt) || $p_act <> 1    || # medicijn bestaat niet in kezeuelijst of is niet actief
        empty($vrrd) || $vrrd == 0        || # medcijn niet meer op voorraad
        empty($aantal)                    || # aantal is leeg
        empty($stdat)                    || # Standaard hoeveelheid is leeg
        (isset($dmafv) && $dmafv <= $date)    || #Afvoerdatum is gelijk aan of ligt voor toedien datum
        ($r_act <> 1 && !empty($reduId))     # reden t.b.v. medicijn niet actief
     )
     {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE De voorwaarden om in te kunnen lezen.  

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:13px;">
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
 

</tr>
<!--    **************************************
    **    EINDE OPMAAK GEGEVENS    **
    ************************************** -->

<?php unset($schaapId); unset($dmafv); }
} //einde if(isset($data))
 unset($fase); ?>
</table>
</form> 

    </TD>
<?php
include "menu1.php"; } ?>
</tr>

</table>
<?php
include "select-all.js.php";
?>

</body>
</html>
