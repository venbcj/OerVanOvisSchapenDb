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
$versie = '18-6-2020';  /* Kopie gemaakt van insSpenen.php */
$versie = '4-7-2020'; /* 1 tabel impAgrident gemaakt */
$versie = '31-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
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
$titel = 'Inlezen Lambar';
$file = "InsLambar.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) {

If (isset($_POST['knpInsert_']))  {
    include "url.php";
    include "post_readerLbar.php"; #Deze include moet voor de vervversing in de functie header()
}

$velden = "str_to_date(rd.datum,'%Y-%m-%d') sort , rd.datum, rd.Id, rd.levensnummer levnr, rd.gewicht kg,
 kh.hoknr verblijf,
 dup.dubbelen,
 s.levensnummer, s.geslacht,
 lower(h.actie) actie, h.af,

 date_format(hs.datum,'%d-%m-%Y') speendm, hs.datum dmspeen, ouder.datum dmaanw,

 h.datum dmlst ";

$tabel = "
impAgrident rd
 left join (
     SELECT max(h.hisId) hisId, s.schaapId, s.levensnummer, s.geslacht
     FROM tblSchaap s
      join tblStal st on (st.schaapId = s.schaapId)
      join tblHistorie h on (st.stalId = h.stalId)
     WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0
     GROUP BY s.schaapId, s.levensnummer, s.geslacht
 ) s on (rd.levensnummer = s.levensnummer)
 left join (
    SELECT h.hisId, h.datum, a.actie, a.af
    FROM tblHistorie h
     join tblActie a on (h.actId = a.actId)
    WHERE h.skip = 0
 ) h on (h.hisId = s.hisId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 4 and h.skip = 0
 ) hs on (hs.schaapId = s.schaapId)
 left join (
    SELECT st.schaapId, h.datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE h.actId = 3 and h.skip = 0
 ) ouder on (ouder.schaapId = s.schaapId)
 left join tblHok kh on (rd.hokId = kh.hokId and kh.lidId = rd.lidId)
 left join (
     SELECT rd.Id, count(dup.Id) dubbelen
    FROM impAgrident rd
     join impAgrident dup on (rd.lidId = dup.lidId and rd.levensnummer = dup.levensnummer and rd.actId = dup.actId and rd.Id <> dup.Id)
    WHERE rd.actId = 16 and rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and ISNULL(rd.verwerkt) and ISNULL(dup.verwerkt)
    GROUP BY rd.Id
 ) dup on (rd.Id = dup.Id)
 left join (
    SELECT m.levensnummer, max(m.datum) datum
    FROM (
        SELECT s.levensnummer, h.datum
        FROM tblSchaap s 
         join tblStal st on (st.schaapId = s.schaapId)
         join tblHistorie h on (st.stalId = h.stalId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.levensnummer is not null and h.skip = 0
        
    ) m
    GROUP BY m.levensnummer 
 ) lstday on (lstday.levensnummer = rd.levensnummer )
 " ;

$WHERE = "WHERE rd.lidId = '".mysqli_real_escape_string($db,$lidId)."' and actId = 16 and isnull(rd.verwerkt)";

include "paginas.php";
$data = $page_nums->fetch_data($velden, "ORDER BY sort, rd.Id");

?>
<table border = 0>
<tr> <form action="InsLambar.php" method = "post">
 <td colspan = 3 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php echo $page_numbers; ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $kzlRpp; ?> </td>
 <td align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Speen<br>datum<hr></th>
 <th>Levensnummer<hr></th>
 <th>Gewicht<hr></th>
 <th width = 80 >naar verblijf<hr></th>
 <th width = 250 ><hr></th>
</tr>
<?php
if(isset($data))  { foreach($data as $key => $array)
    {
        $var = $array['datum'];
$date = str_replace('/', '-', $var);
$datum = date('d-m-Y', strtotime($date));
$dm       = date('Y-m-d', strtotime($date));
    
    $Id = $array['Id'];
    $levnr = $array['levnr']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levnr'];}
    $levnr_dupl = $array['dubbelen']; // twee keer in reader bestand
    $verblijf = $array['verblijf'];
    $kg = $array['kg'];
    $geslacht = $array['geslacht'];
    $dmaanw = $array['dmaanw']; if(isset($dmaanw)) { if($geslacht == 'ooi') {$fase = 'moederdier'; } else if($geslacht == 'ram') { $fase = 'vaderdier';} } 
                                else { $fase = 'lam';} 
    $af = $array['af']; if(isset($af) && $af == 1) { $status = $array['actie']; }
    $dmspeen = $array['dmspeen']; // Ter controle of speendatum reeds bestaat
    $speendm = $array['speendm']; // Ter controle of speendatum reeds bestaat
    $dmmax = $array['dmlst'];     $maxdm = date('d-m-Y', strtotime($dmmax)); // Laatste datum bepalen als spenen opnieuw kan worden ingelezen. De laatste datum kan dus geen speendatum zijn


// Controleren of ingelezen waardes correct zijn.
if (isset($_POST['knpVervers_'])) { $datum = $_POST["txtDag_$Id"]; $kg = $_POST["txtKg_$Id"];
    $makeday = date_create($_POST["txtDag_$Id"]); $dm =  date_format($makeday, 'Y-m-d');
}

     If     
     (    !isset($af) || $af == 1        || /*levensnummer moet bestaan en het dier moet aanweig zijn */
         isset($levnr_dupl)            || # of levensnummer bestaat al in reader bestand
        $fase == 'moederdier'        || $fase == 'vaderdier' || # dier moet een lam zijn
        empty($datum)                || # of datum is leeg
        $dm < $dmmax                || # of datum ligt voor de laatst geregistreerde datum van het schaap
        //(empty($kg) && $fase == 'lam')                    || # of gewicht is leeg        Per 20-1-2017 speengewicht niet verplicht gemaakt.
        !isset($verblijf)             || # of verblijf is onbekend
        isset($dmspeen)                   # het dier heeft al een speendatum
                                                 
     )
     {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes correct zijn.

     if (isset($_POST['knpVervers_']) && $_POST["laatsteOke_$Id"] == 0 && $oke == 1) /* Als onvolledig is gewijzigd naar volledig juist */ {$cbKies = 1; $cbDel = $_POST["chbDel_$Id"]; }
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
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDag_$Id"; ?> value = <?php echo $datum; ?> >
 </td>
 
<?php if(isset($af) && $af == 0) { ?> <td> <?php echo $levnr;} else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>
    
 <td style = "font-size : 9px;"> 
    <input type = "text" size = 3 style = "font-size : 11px;" name = <?php echo "txtKg_$Id" ?> value = <?php echo $kg; ?> > </td>

 <td align = "center">
<!-- VERBLIJF -->
 <?php if(isset($verblijf)) { echo $verblijf; } ?>
 <!-- EINDE VERBLIJF -->
  </td>
    

 
<!-- Foutmeldingen --> <?php 
     if (!isset($af))                 { $color = 'red';  $bericht =  "Levensnummer onbekend";}
else if (!isset($verblijf) )         { $color = 'blue'; $bericht =  "Lambar bestaat niet."; }
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
include "menu1.php"; }

include "select-all.js.php";
?>
</tr>

</table>

</body>
</html>
