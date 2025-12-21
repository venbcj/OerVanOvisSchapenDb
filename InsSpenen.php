<?php

require_once("autoload.php");

/*10-4-2014 query op tabel impReader vervangen door query uit vw_Reader_sp.php 
Deze view bevat enkel te spenen schapen uit de tabel impReader aangevuld met de velden van te overplaatsen schapen. Is hok_sp leeg dan wordt hok_ovpl gebruikt.
Reden : na spenen van verschillende hokken worden de schapen herverdeeld o.b.v. gewicht.
23-11-2014 : functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server
5-3-2015 : Login toegevoegd 
6-3-2015 : sql beveiligd 
18-11-2015 : Hok gewijzigd naar verblijf */
$versie = '7-11-2016';  /* vw_Reader_sp aangepast tblSchaap is gerelateerd aan impReader via levensnummer. i.v.m. left join moet al zijn bepaald dat enkel schapen van lidId mogen worden getoond. Vandaar is tblSchaap eerst genest met tblStal */
$versie = '9-11-2016';  /* vw_StatusSchaap verwijderd en gebaseerd op laatste hisId */
$versie = '23-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '22-1-2017';  /* 20-1-2017 ; hok_uitgez = 'Geboren' gewijzigd in hok_uitgez = 1  21-1-2017 Overbodige hidden velden verwijderd (txtId, txtLevspn en txtOvplId)   22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '20-2-2017';  /* lidId aan tblHok alias kh toegevoegd binnen de query vw_Reader_sp */
$versie = '3-9-2017';  /* Nav inlezen tussenwegingen query's uitgebreid met levnr_sp is not null */
$versie = '19-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '7-3-2019'; /* gewicht gedeeld door 100 ipv 10 */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '8-6-2020'; /* Onderscheid gemaakt tussen reader Agrident en Biocontrol */
$versie = '8-6-2020'; /* Geslacht toegevoegd */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$versie = '10-03-2024'; /* Keuzelijst verblijf breder gemaakt van width:65 naar width:84 */
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
$titel = 'Inlezen Gespeenden';
$file = "InsSpenen.php";
include "login.php"; ?>

                <TD valign = "top">
<?php
if (Auth::is_logged_in()) {
    $impagrident_gateway = new ImpAgridentGateway();

If (isset($_POST['knpInsert_']))  {
    include "url.php";
    include "post_readerSpn.php"; #Deze include moet voor de vervversing in de functie header()
}

if($reader == 'Agrident') {
    $velden = "str_to_date(rd.datum,'%Y-%m-%d') sort , rd.datum, rd.Id readId, rd.levensnummer levnr, rd.gewicht kg,
        coalesce(rd.hokId,ro.hokId) rd_hok, kh.hokId db_scan,
        dup.dubbelen,
        s.levensnummer, s.geslacht,
        lower(h.actie) actie, h.af,
        date_format(hs.datum,'%d-%m-%Y') speendm, hs.datum dmspeen, ouder.datum dmaanw,
        lstday.datum dmlst ";

    $tabel = $impagrident_gateway->getInsSpenenAgridentFrom();
    $WHERE = $impagrident_gateway->getInsSpenenAgridentWhere($lidId);
    $order_by = "ORDER BY sort, rd.Id";
} else {
    $velden = "str_to_date(rd.datum,'%d/%m/%Y') sort , rd.datum, rd.readId, rd.levnr_sp levnr, round((rd.speenkg/100),2) kg,
        coalesce(rd.hok_sp,ro.hok_ovpl) rd_hok, kh.scan db_scan,
        dup.dubbelen,
        s.levensnummer, s.geslacht,
        lower(h.actie) actie, h.af,
        date_format(hs.datum,'%d-%m-%Y') speendm, hs.datum dmspeen, ouder.datum dmaanw,
        lstday.datum dmlst ";

    $tabel = $impagrident_gateway->getInsSpenenBiocontrolFrom();
    $WHERE = $impagrident_gateway->getInsSpenenBiocontrolWhere($lidId);
    $order_by = "ORDER BY sort, rd.readId";
}

include "paginas.php";
$data = $paginator->fetch_data($velden, $order_by); 

?>

<table border = 0>
<tr> <form action="InsSpenen.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); 
?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Speen<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Geslacht<hr></th>
 <th>Gewicht<hr></th>
 <th width = 80 >naar verblijf<hr></th>
 <th width = 250 ><hr></th>
</tr>
<?php


// Declaratie HOKNUMMER            // lower(if(isnull(scan),'6karakters',scan)) zorgt ervoor dat $raak nooit leeg is. Anders worden legen velden gevonden in legen velden binnen impReader.
$hok_gateway = new HokGateway();
$qryHoknummer = $hok_gateway->actieve_nummers_bij_lid($lidId);

$index = 0; 
while ($hnr = $qryHoknummer->fetch_array()) { 
   $hoknId[$index] = $hnr['hokId']; 
   $hoknum[$index] = $hnr['hoknr'];
   $index++; 
} 
unset($index);
// EINDE Declaratie HOKNUMMER

if(isset($data))  { foreach($data as $key => $array)
    {
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$dm       = date('Y-m-d', strtotime($date));
    
    $Id = $array['readId'];
    $levnr = $array['levnr']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levnr'];}
    $levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
    $rd_hok = $array['rd_hok'];
    $db_scan = $array['db_scan'];
    $kg = $array['kg'];
    $geslacht = $array['geslacht'];
    $dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else { $fase = 'lam';} 
    $af = $array['af']; if(isset($af) && $af == 1) { $status = $array['actie']; }
    $dmspeen = $array['dmspeen']; // Ter controle of speendatum reeds bestaat
    $speendm = $array['speendm']; // Ter controle of speendatum reeds bestaat
    $dmmax = $array['dmlst'];     $maxdm = date('d-m-Y', strtotime($dmmax)); // Laatste datum bepalen als spenen opnieuw kan worden ingelezen. De laatste datum kan dus geen speendatum zijn


// Controleren of ingelezen waardes correct zijn.
$kzlHok = $db_scan; 
if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtSpeendag_$Id"]; $kg = $_POST["txtKg_$Id"]; $kzlHok = $_POST["kzlHok_$Id"]; 
    $makeday = date_create($_POST["txtSpeendag_$Id"]); $dm =  date_format($makeday, 'Y-m-d');
}

     If     
     (    !isset($af) || $af == 1        || /*levensnummer moet bestaan en het dier moet aanweig zijn */
         isset($levnr_dupl)            || # of levensnummer bestaat al in reader bestand
        $fase == 'moederdier'        || $fase == 'vaderdier' || # dier moet een lam zijn
        empty($datum)                || # of datum is leeg
        $dm < $dmmax                || # of datum ligt voor de laatst geregistreerde datum van het schaap
        //(empty($kg) && $fase == 'lam')                    || # of gewicht is leeg        Per 20-1-2017 speengewicht niet verplicht gemaakt.
        empty($kzlHok)                 || # of verblijf is onbekend of leeg
        isset($dmspeen)                   # het dier heeft al een speendatum
                                                 
     )
     {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes correct zijn.

/* Als onvolledig is gewijzigd naar volledig juist */
     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1)  {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
else if (isset($_POST['knpVervers_'])) { $cbKies = $_POST["chbkies_$Id"];  $cbDel = $_POST["chbDel_$Id"]; } 
   else { $cbKies = $oke; } // $cbKies is tbv het vasthouden van de keuze inlezen of niet ?>

<!--    **************************************
        **            OPMAAK  GEGEVENS            **
        ************************************** -->

<tr style = "font-size:14px;">
 <td align = "center"> <?php //echo $Id; ?>
    
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
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtSpeendag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
 
<?php if(isset($af) && $af == 0) { ?> <td> <?php echo $levnr;} else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>
 <td align = "center">
     <?php echo $geslacht; ?>
 </td>
    
 <td style = "font-size : 9px;"> 
    <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id" ?> value = <?php echo $kg; ?> > </td>

 <td align = "center">
<!-- KZLVERBLIJF -->
 <select style="width:84; font-size:12px;" name = <?php echo "kzlHok_$Id"; ?> >
  <option></option>
<?php
$count = count($hoknum);
for ($i = 0; $i < $count; $i++){

    $opties = array($hoknId[$i]=>$hoknum[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $rd_hok == $hoknId[$i]) || (isset($_POST["kzlHok_$Id"]) && $_POST["kzlHok_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
}
?> </select>
<?php if( $rd_hok<> NULL && empty($db_scan) && empty($_POST["kzlHok_$Id"]) && $levnr > 0 ) {echo $rd_hok; ?> <b style = "color : red;"> ! </b>  <?php } ?>
 <!-- EINDE KZLVERBLIJF -->
    

 
<!-- Foutmeldingen --> <?php 
     if (!isset($af))                 { $color = 'red';  $bericht =  "Levensnummer onbekend";}
else if (isset($levnr_dupl) )         { $color = 'blue'; $bericht =  "Dubbel in de reader."; }
else if ($fase == 'moederdier' || $fase == 'vaderdier') 
                                    { $color = 'red';  $bericht =  "Dit schaap is een ".$fase."."; }
else if (isset($af) && $af == 1)     { $color = 'red';  $bericht =  "Dit schaap is ".strtolower($status)."."; }
else if($dm < $dmmax)                 { $color = 'red';  $bericht =  "Datum ligt voor $maxdm ."; } 
else if(isset($dmspeen))             { $color = 'red';  $bericht =  "Dit schaap is al gespeend op ".$speendm." ."; } ?>
 <td colspan = 2 style = "color : <?php echo $color; ?> ; font-size : 11px;" >
     <?php if ( isset($bericht) ) { echo $bericht; unset($bericht); unset($color); } ?>
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
include "menu1.php"; } ?>
</tr>

</table>

<?php
    include "select-all.js.php";
?>
</body>
</html>
