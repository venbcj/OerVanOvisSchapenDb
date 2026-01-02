<?php 

require_once("autoload.php");

$versie = '21-10-2023'; /* Gekopieerd van Melden.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

$info_beheer = "In de readerApp zit een knop \'Schaapinfo inlezen\'. Met deze knop wordt een databse ingelezen in de reader met schaap gegevens. Bij het scannen van een schaap worden de volgende gegevens getoond op de reader: \\n Geslacht : Dit is het geslacht van het schaap \\n Ras : Dit is het ras van het schaap \\n Laatst gedekt : Als het een ooi betreft wordt hier de laatste dekdatum getoond mits deze bestaat \\n Laatste dekram : Dit is het werknr van de ram bij de laatste dekking van de gescande ooi \\n";
 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>

<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<style type="text/css">
.selectt {
   /* color: #fff;
    padding: 30px;*/
    display: none;
    /*margin-top: 30px;
    width: 60%;
    background: grey;
    font-size: 12px;*/
}
</style>

</head>
<body>

<?php
$titel = 'Beschikbare readerversies';
$file = "Readerversies.php";
include "login.php"; ?>

                <TD valign = 'top'>    
<?php
if (Auth::is_logged_in()) {
    if ($reader == 'Agrident') {
        $versiebeheer_gateway = new VersiebeheerGateway();
        $lid_gateway = new LidGateway();
        include "kalender.php";
?>

<script type="text/javascript">

function toon_velden(id) {

var chbVersie = 'versieChb_' + id;
var lblReaderApp = 'readerApp_' + id;

versiekeuze = document.getElementById(chbVersie);        var vk = versiekeuze.value;

// if(mr.length > 0) alert(jArray_vdr[mr]);
  if(vk == id) {

      document.getElementById(lblReaderApp).style.display = "inline-block";
      //document.getElementById(txtDrachtdm).value = datum;
      //document.getElementById(txtWorp).style.display = "inline-block";

  }
  else
  {
      document.getElementById(lblReaderApp).style.display = "none";
      //document.getElementById(txtDrachtdm).value = null;
      //document.getElementById(txtWorp).style.display = "none";
      //document.getElementById(txtWorp).value = null;
  }

}

</script>


<?php 
    if(isset($_POST['knpInsert'])){
    $insDatum = $_POST['insDatum']; $insDate = date_format(date_create($insDatum),'Y-m-d');
    $insVersie = $_POST['insVersie'];
    $insNaamApp = $_POST['insNaamApp'];
    $insNaamTaak = $_POST['insNaamTaak'];
    $insToel = $_POST['insToel'];

    if(!empty($insNaamApp)) {
        $insert_tblVersiebeheer = $versiebeheer_gateway->insert_app($insDate, $insVersie, $insNaamApp, $insToel);
    }
    if(!empty($insNaamTaak)) {
        if(!empty($insNaamApp)) {
            $versieId = $versiebeheer_gateway->zoek_versieId($insNaamApp);
            $versiebeheer_gateway->insert_taak_versie($versieId, $insDate, $insVersie, $insNaamTaak, $insToel);
        }
        else {
            $versiebeheer_gateway->insert_taak($insDate, $insVersie, $insNaamTaak, $insToel);
        }
    }
    }
    // Einde if(isset($_POST['knpInsert']))
    if(isset($_POST['txtVersies_'])) { $hisVersies = $_POST['txtVersies_']; } 
    else { $hisVersies = 2; }
?>


<form action="Readerversies.php" method = "post">

<br> <h4 style = 'color : grey'>
 
     Download hier de nieuwste versie van de readerApp en de readertaken (indien van toepassing) door op de link te klikken.<br> Na het downloaden klik je op de knop Downloaden afronden. <br></h4>
     <h4 style = 'color : red'>
     Denk er aan dat voor het downloaden de reader is uitgelezen! <br><br>
 
</h4>
<table border = 0>
<tr height = 50 valign="top">
 <td colspan = 4 align="center"> Toon laatste
    <input type="text" name="txtVersies_" size="1" style = "font-size:9px; text-align : center;" value = <?php 
    echo $hisVersies;
    ?> >
versies
 </td>
 <td  align="left" > 
    <input type="submit" name="knpVervers_" value="Ververs" style = "font-size:9px;">
 </td>
</tr>

<tr>
 <th width="75"> Versie <hr></th>
 <th width="5"> </th>
 <th width="100"> Releasedatum <hr></th>
 <th width="15"> </th>
 <th valign="bottom"> ReaderApp <hr></th> 
 <th width="15"> </th>
 <th valign="bottom"> Readertaken <hr></th>
 <th width="25"> </th>
 <th width="300"> Toelichting <hr></th>
 <th width="25"> </th>
 <th> </th>
</tr>
<?php 
    $dmStart = $lid_gateway->zoek_startdatum_klant($lidId);
    $zoek_versies = $versiebeheer_gateway->zoek_versies($dmStart, $last_versieId, $hisVersies);
    while ( $zvs = $zoek_versies->fetch_assoc()) {
        $Id = $zvs['Id']; 
        $datum = $zvs['datum']; 
        $jaar = $zvs['jaar']; 
        $versienr = $zvs['versie']; 
        $setup_bestand = $zvs['bestandApp']; 
        $taken_bestand = $zvs['bestandTaak']; 
        $Toelichting = $zvs['comment']; 


if(isset($_POST['knpAfronden_'.$Id])) {

// $dir en $persoonlijke_map in login.php gedeclareerd
copy($dir.'/Readerversies/'.$setup_bestand , $persoonlijke_map.'/Readerversies/'.$setup_bestand);
copy($dir.'/Readerversies/'.$taken_bestand , $persoonlijke_map.'/Readerversies/'.$taken_bestand);

// Naast login.php ook hier opnieuw controleren of bestand in persoonlijk map Readerversie staat zodat de rode tekst in het menu meteen blauw wordt
// BCB: dat hoeft niet. Het hoeft maar op 1 plek. TODO: #0004119 goede plek vinden.
if (isset($Readersetup_bestand)) {
    $appfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readersetup_bestand);
} else {
    $appfile_exists = 1;
}

if (isset($Readertaken_bestand)) {
    $takenfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readertaken_bestand);
} else {
    $takenfile_exists = 1;
}

if ($appfile_exists == 1 && $takenfile_exists == 1) {
    $actuele_versie = 'Ja';
}

 }


if(isset($_POST['knpNogmaals_'.$Id]) && isset($setup_bestand)) {

 $DelFileApp = $persoonlijke_map.'/Readerversies/'.$setup_bestand;

unlink($DelFileApp);// verwijdert $setup_bestand
}

if(isset($_POST['knpNogmaals_'.$Id]) && isset($taken_bestand)) {

 $DelFileTaak = $persoonlijke_map.'/Readerversies/'.$taken_bestand;

unlink($DelFileTaak);// verwijdert taken bestand

}

$appfile_aanwezig = file_exists($persoonlijke_map.'/Readerversies/'.$setup_bestand);
$takenfile_aanwezig = file_exists($persoonlijke_map.'/Readerversies/'.$taken_bestand);

if ($appfile_aanwezig == 1 && $takenfile_aanwezig == 1) { $afgerond = 'Ja'; }
$current_versie = $versiebeheer_gateway->zoek_huidige_versie($last_versieId);
// $last_versieId gedeclareerd in login.php
 ?>

<tr height = 30>
  <td>
     
 <input onchange = "toon_velden(<?php echo $Id; ?>)" id= <?php echo "versieChb_$Id"; ?> type="checkbox" name= <?php echo "chbVersienr_$Id"; ?> value= <?php echo $Id; if($versienr == $current_versie || isset($_POST['chbVersienr_'.$Id]) ) { ?> checked <?php } ?> > <?php echo $versienr; ?>
 </td>
 <td> </td>
 <td align="center" > <?php echo $datum; ?> </td>

 <td> </td>
 <td align="center" style="color : grey" > <p id= <?php echo "readerApp_$Id"; ?> class= "<?php echo $Id; ?> selectt" > 
     <?php 
if (!isset($setup_bestand)) {
    echo 'n.v.t.'; 
} elseif ($afgerond == 'Ja') {
    echo 'ReaderApp'; 
} else {
    echo View::link_to('ReaderApp', '/Readerversies/'.$setup_bestand, ['style' => 'color: blue']);
}
?> </p>
 </td>

 <td> </td>
 <td align="center" style="color : grey" > <p class= "<?php echo $Id; ?> selectt" >
     <?php 
if (!isset($taken_bestand)) {
    echo 'n.v.t.'; 
} elseif ($afgerond == 'Ja') {
    echo 'Readertaken'; 
} else { 
    echo View::link_to('ReaderTaken', '/Readerversies/'.$taken_bestand, ['style' => 'color: blue']);
}
?> </p>
 </td>

 <td> </td>
 <td> <?php echo $Toelichting; ?>
 </td>
 <td> </td>
 <td class= "<?php echo $Id; ?> selectt"  > 
<?php 
if ($afgerond == 'Ja') { ?> 
    <input type="submit" name= <?php echo "knpNogmaals_$Id"; ?> value="Nogmaals downloaden" style = "font-size:12px;"> <?php }
else { ?> 
    <input type="submit" name= <?php echo "knpAfronden_$Id"; ?> value="Downloaden afronden" style = "font-size:12px;"> <?php } ?>
 </td>
</tr>

<?php unset($afgerond); }
    // Einde while ( $zvs = mysqli_fetch_assoc($zoek_versies)) 
    ?>
</table>


<?php 
    if($modbeheer == 1) { ?>
    <!-- Toevoegen van nieuwe versie door de beheerder -->
    <table>
    <tr>
     <td colspan="15" height="150" valign="bottom"><hr></td>
    </tr>
    
    <tr>
     <th width="100"> Releasedatum <hr></th>
     <th width="50"> Versie <hr></th>
     <th width="180"> Bestandsnaam App<hr></th> 
     <th width="180"> Bestandsnaam Taken<hr></th>
     <th width="250"> Toelichting <hr></th>
     <th width="35"> </th>
    </tr>
    <tr>
     <td>
     </td>
    </tr>
    
    <tr>
     <td> <input type="text" name="insDatum" size="8" id = "datepicker1" > </td>
     <td> <input type="text" name="insVersie" size="5" > </td>
     <td> <input type="text" name="insNaamApp" > </td>
     <td> <input type="text" name="insNaamTaak"  > </td>
     <td> <input type="text" name="insToel" size="50" > </td>
     <td> <input type="submit" name="knpInsert" value="Opslaan"> </td>
    </tr>
    
    
    </table>
    <!-- Einde Toevoegen van nieuwe versie door de beheerder -->
    <?php }
    // Einde if($modbeheer == 1) 
    ?>

    </TD>
<?php 
} else { ?> <img src='Readerversies.jpg'  width='970' height='550'/> <?php }
include "menuBeheer.php"; 
 } // Einde if ((Session::isset("U1")) && isset($_SESSI....... ?>
</tr>
</table>
</form>

<?php
include "readerversies.js.php";
?>
</body>
</html>
