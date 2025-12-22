<?php

require_once("autoload.php");


$versie = '4-7-2020'; /* Gekopieerd van insAdoptie.php */
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
$titel = 'Inlezen Omnummeren';
$file = "InsOmnummeren.php";
include "login.php"; ?>

            <TD valign = "top">
<?php
if (Auth::is_logged_in()) { 
    $impagrident_gateway = new ImpAgridentGateway();

if (isset ($_POST['knpInsert_'])) {
    include "post_readerOmnum.php"; #Deze include moet voor de vervversing in de functie header()
    }

$velden = "rd.Id, date_format(rd.datum,'%d-%m-%Y') datum, rd.datum sort, rd.levensnummer, rd.nieuw_nummer,
s.schaapId oud_db, new.schaapId nieuw_db,
lower(h.actie) actie, h.af, date_format(h.datum,'%d-%m-%Y') maxdatum, h.datum datummax";

$tabel = $impagrident_gateway->getInsOmnummerenFrom();
$WHERE = $impagrident_gateway->getInsOmnummerenWhere($lidId);

include "paginas.php";
$data = $paginator->fetch_data($velden, "ORDER BY sort, rd.Id");

 ?>
<table border = 0>
<tr> <form action="InsOmnummeren.php" method = "post">
 <td colspan = 2 style = "font-size : 13px;">
  <input type = "submit" name = "knpVervers_" value = "Verversen"></td>
 <td colspan = 2 align = "center" style = "font-size : 14px;"><?php 
echo $paginator->show_page_numbers(); ?></td>
 <td colspan = 3 align = left style = "font-size : 13px;"> Regels Per Pagina: <?php echo $paginator->show_rpp(); ?> </td>
 <td colspan = 3 align = 'right'><input type = "submit" name = "knpInsert_" value = "Inlezen">&nbsp &nbsp </td>
 <td colspan = 2 style = "font-size : 12px;"><b style = "color : red;">!</b> = waarde uit reader niet gevonden. </td></tr>
<tr valign = bottom style = "font-size : 12px;">
 <th>Inlezen<br><b style = "font-size : 10px;">Ja/Nee</b><br> <input type="checkbox" id="selectall" checked /> <hr></th>
 <th>Verwij-<br>deren<br> <input type="checkbox" id="selectall_del" /> <hr></th>
 <th>Omnummer<br>datum<hr></th>
 <th>Oud<hr></th>
 <th>nieuw<hr></th>
</tr>
<?php

if(isset($data))  {    foreach($data as $key => $array)
    {
    $Id = $array['Id'];
    $datum = $array['datum'];
    $date = $array['sort'];
    $levnr = $array['levensnummer']; if (strlen($levnr)== 11) {$levnr = '0'.$array['levensnummer'];}
    $nieuw = $array['nieuw_nummer'];
    $nieuw_db = $array['nieuw_db'];
    $status = $array['actie']; 
    $af = $array['af'];     
    $maxdm = $array['maxdatum'];
    $dmmax = $array['datummax'];


// Controleren of ingelezen waardes worden gevonden .
$dag = $datum ; $dmdag = $date;
if (isset($_POST['knpVervers_'])) { $dag = $_POST["txtDag_$Id"]; 
    $makeday = date_create($_POST["txtDag_$Id"]); $dmdag =  date_format($makeday, 'Y-m-d');
}

     If     
     ( ((isset($af) && $af == 1) || !isset($status))    || /*levensnummer moet bestaan*/    
         empty($dag)                || # of datum is leeg
         isset($nieuw_db)            || # Het nieuwe nummer bestaat al
         $dmdag < $dmmax             # of datum ligt voor de laatst geregistreerde datum van het schaap
                                                 
     )
     {    $oke = 0;    } else {    $oke = 1;    } // $oke kijkt of alle velden juist zijn gevuld. Zowel voor als na wijzigen.
// EINDE Controleren of ingelezen waardes worden gevonden .  

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
 <td>
    <input type = "text" size = 9 style = "font-size : 11px;" name = <?php echo "txtDag_$Id"; ?> value = <?php echo $dag; ?> >
 </td>

<?php if(!empty($status)) { ?> <td> <?php echo $levnr; } else { ?> <td style = "color : red"> <?php echo $levnr;} ?>
 </td>

 <td align="center"><?php echo $nieuw; ?>
 </td>    

 <td style = "color : red" align="center"><?php 
          if (empty($status))         { echo "Oud levensnummer onbekend"; }
     else if (isset($nieuw_db))         { echo "Nieuw levensnummer bestaat al"; }
     else if(isset($af) && $af == 1) { echo 'Dit dier is '. $status; } 
 ?>
    <input type = "hidden" size = 8 style = "font-size : 9px;" name = <?php echo "txtStatus_$Id"; ?> value = <?php echo $status; ?> > <!--hiddden-->
 </td>
 <td style = "color : red"> <?php 
if($dmdag < $dmmax) { echo "Datum ligt voor $maxdm ."; } ?>
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
