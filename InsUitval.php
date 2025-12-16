<?php

require_once("autoload.php");

/* 18-2-2014 : Keuzelijst uitval uitgebreid met uitvalId > 3 and uitvalId <= 6 ipv uitvalId > 3 en gesorteerd op uitvalId 
9-5-2014 : txtUitvdm gewijzigd in txtuitvdm
11-8-2014 : veld type gewijzigd in fase 
23-11-2014 : functie header() toegevoegd. In de header wordt het vervevrsen van de pagina verstuurd (request =. response) naar de server 
6-3-2015 : sql beveiligd 
8-3-2015 : Login toegevoegd 
$versie = '9-11-2016';  /* vw_StatusSchaap verwijderd en gebaseerd op laatste hisId */
$versie = '23-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '2-3-2017';  /* hidden veld txtId verwijderd     10-3-2017 : view vw_HistorieDm vervangen door script */
$versie = '20-3-2018';  /* Meerdere pagina's gemaakt 12-5-2018 : if(isset(data)) toegevoegd. Als alle records zijn verwerkt bestaat data nl. niet meer !! */
$versie = '22-6-2018';  /* Velden in impReader aangepast */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-1-2019'; /* alles aan- en uitzetten met javascript */
$versie = '24-4-2020'; /* url Javascript libary aangepast */
$versie = '13-6-2020'; /* Onderschied gemaakt tussen reader Agrident en Biocontrol */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '23-1-2021'; /* Alias readId bestond niet in query Agrident. Sql beveiligd met quotes */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie */
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
$titel = 'Inlezen Uitval';
$file = "InsUitval.php";
include "login.php"; ?>

                <TD valign = "top">
<?php
if (Auth::is_logged_in()) {

If (isset($_POST['knpInsert_'])) {
    include "post_readerUitv.php"; #Deze include moet voor de vervversing in de functie header()
    //header("Location: ".$url."insUitval.php"); 
    }
if($reader == 'Agrident') {
$velden = "rd.Id readId, date_format(rd.datum,'%Y-%m-%d') sort, rd.datum, rd.levensnummer levnr, rd.reden reden_uitv, ru.reduId dbreduId,
lower(h.actie) actie, h.af, s.geslacht, ouder.datum dmaanw, date_format(max.datummax,'%Y-%m-%d') datummax, date_format(max.datummax,'%d-%m-%Y') maxdatum"; 

$tabel = "
impAgrident rd
 left join (
    SELECT r.reduId, r.lidId 
    FROM tblRedenuser r
    WHERE r.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.uitval = 1
 ) ru on (ru.reduId = rd.reden and ru.lidId = rd.lidId)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT sd.schaapId, max(sd.datum) datummax 
    FROM (
        SELECT st.schaapId, max(h.datum) datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)/* max datum uit tblHistorie */."' and h.skip = 0
        GROUP BY st.schaapId         
        
        union
        
        SELECT  mdr.schaapId, min(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Eerste worp */."'
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Laatste worp */."'
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Gevoerd */."'
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY sd.schaapId
 ) max on (s.schaapId = max.schaapId)
"; 

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.actId = 14 and isnull(rd.verwerkt) ";
$order_by = "ORDER BY sort, rd.Id";

} else {
$velden = "rd.readId, str_to_date(rd.datum,'%d/%m/%Y') sort, rd.datum, rd.levnr_uitv levnr, rd.reden_uitv, ru.reduId dbreduId,
lower(h.actie) actie, h.af, s.geslacht, ouder.datum dmaanw, date_format(max.datummax,'%Y-%m-%d') datummax, date_format(max.datummax,'%d-%m-%Y') maxdatum"; 

$tabel = "
impReader rd
 left join (
    SELECT r.reduId, r.lidId 
    FROM tblRedenuser r
    WHERE r.lidId = '".mysqli_real_escape_string($db,$lidId)."' and r.uitval = 1
 ) ru on (ru.reduId = rd.reden_uitv and ru.lidId = rd.lidId)
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levnr_uitv = s.levensnummer)
 left join (
    SELECT h.hisId, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join (
    SELECT sd.schaapId, max(sd.datum) datummax 
    FROM (
        SELECT st.schaapId, max(h.datum) datum
        FROM tblStal st
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)/* max datum uit tblHistorie */."' and h.skip = 0
        GROUP BY st.schaapId         
        
        union
        
        SELECT  mdr.schaapId, min(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Eerste worp */."'
        GROUP BY mdr.schaapId

        Union

        SELECT mdr.schaapId, max(h.datum) datum
        FROM tblSchaap mdr
         join tblVolwas v on (mdr.schaapId = v.mdrId)
         join tblSchaap lam on (v.volwId = lam.volwId)
         join tblStal st on (st.schaapId = lam.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE h.actId = 1 and h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Laatste worp */."'
        GROUP BY mdr.schaapId, h.actId
        HAVING (max(h.datum) > min(h.datum))

        Union

        SELECT s.schaapId, p.dmafsluit datum
        FROM tblVoeding vd
         join tblPeriode p on (p.periId = vd.periId)
         join tblBezet b on (b.periId = p.periId)
         join tblHistorie h on (h.hisId = b.hisId)
         join tblStal st on (st.stalId = h.stalId)
         join tblSchaap s on (s.schaapId = st.schaapId)
        WHERE h.skip = 0 and st.lidId = '".mysqli_real_escape_string($db,$lidId)/* Gevoerd */."'
        GROUP BY s.schaapId, p.dmafsluit
    ) sd
    GROUP BY sd.schaapId
 ) max on (s.schaapId = max.schaapId)
"; 

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and rd.teller_uitv is not null and isnull(rd.verwerkt) ";
$order_by = "ORDER BY sort, rd.readId";

}

include "paginas.php";
$data = $page_nums->fetch_data($velden, $order_by); 

?>

<table border = 0>
<tr> <form action="InsUitval.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $page_numbers; ?></td>
 <td colspan = 2 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Uitval<br>datum<hr></th>
 <th width = 100>Levensnummer<hr></th>
<!--<th>Moment<hr></th>-->
 <th width = 170 >Reden<hr></th>
 <th width = 80 >Generatie<hr></th>
 <th colspan = 2><hr></th>
</tr>
<?php

// Declaratie REDEN
$reden_gateway = new RedenGateway();
$reden = $reden_gateway->alle_lijst_voor($lidId);

$index = 0; 
$redId = [];
while ($red = mysqli_fetch_array($reden)) { 
   $redId[$index] = $red['redId'];
   $redn[$index] = $red['reden'];
   $index++; 
} 
unset($index); 
//dan het volgende: 
// EINDE Declaratie REDEN

if(isset($data))  {    foreach($data as $key => $array)
    {
        $var = $array['datum'];
$day = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($day));
$date  = date('Y-m-d', strtotime($day));
    
    $Id = $array['readId'];
    $levnr = $array['levnr'];
    $redenId = $array['reden_uitv'];
    $reden_exist = $array['dbreduId'];
    $geslacht = $array['geslacht']; 
    $dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else { $fase = 'lam';}
    $af = $array['af']; if(isset($af) && $af == 1) { $status = $array['actie']; }
    $dmmax = $array['datummax'];
    $maxdm = $array['maxdatum'];
 
// Controleren of ingelezen waardes worden gevonden .
$kzlReden = $reden_exist;
if (isset($_POST['knpVervers_'])) { $makeday = date_create($_POST["txtuitvdm_$Id"]); $date =  date_format($makeday, 'Y-m-d'); 
    $kzlReden = $_POST["kzlreden_$Id"]; }
     If     
     (    !isset($af) || $af == 1            || /*levensnummer moet bestaan en het dier moet aanweig zijn */    
         empty($datum)                    || # of datum is leeg
         $date < $dmmax                    || # of datum ligt voor de laatst geregistreerde datum van het schaap
        empty($kzlReden)                   # reden uitval is onbekend
     )
     {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .

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
 <?php if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtuitvdm_$Id"]; } ?>
<input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtuitvdm_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
 <?php if(isset($af) && $af == 0) { ?> <td align = "center"> <?php } 
                                                          else { ?> <td align = "center" style = "color : red"> <?php } 
echo $levnr; ?>
<input type = "hidden" name = <?php echo  "txtlevuitv_$Id"; ?> value = <?php echo $levnr; ?> size = 8 style = "font-size : 9px;">
 </td>
 </td>
<input type = "hidden" name = <?php echo  "txtlevuitv_$Id"; ?> value = <?php echo $levnr; ?> size = 8 style = "font-size : 9px;">
 </td>
 <td align = "center">
<!-- KZLREDEN UITVAL -->
 <select style="width:150; font-size:12px;" name = <?php echo "kzlreden_$Id"; ?> >
  <option></option>
<?php    $count = count($redId);
for ($i = 0; $i < $count; $i++){

    $opties = array($redId[$i]=>$redn[$i]);
            foreach($opties as $key => $waarde)
            {
  if ((!isset($_POST['knpVervers_']) && $redenId == $redId[$i]) || (isset($_POST["kzlreden_$Id"]) && $_POST["kzlreden_$Id"] == $key)){
    echo '<option value="' . $key . '" selected>' . $waarde . '</option>';
  } else { 
    echo '<option value="' . $key . '" >' . $waarde . '</option>';  
  }        
            }
} 
?> </select><b style = "color : red;">
<?php if( $redenId <> NULL && empty($reden_exist) && empty($_POST["kzlreden_$Id"]) && empty($levnr)) {echo $redenId;?> <b style = "color : red;"> ! </b> <?php } ?></b>

 </td> <!-- EINDE KZLREDEN UITVAL -->
 <td align = "center">
 <?php if(isset($af) && $af == 0) { echo $fase; } ?>
 </td>
 <td colspan = 2 align = 'left' style = "color : red"><?php 
 if (!isset($af)) {echo "Levensnummer onbekend";} 
 else if(isset($af) && $af == 1){ echo "Dit schaap is reeds ".strtolower($status).".";} 
 else if($date < $dmmax) { echo "Datum ligt voor $maxdm."; } ?>
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
