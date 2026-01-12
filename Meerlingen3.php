<?php 

require_once("autoload.php");

$versie = '1-2-2019'; /* gemaakt */
$versie = '28-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie sql voorzien van enkele quotes */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = 'top' align = center > gewijzigd naar <TD valign = 'top' align = 'center'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Meerling oplopend';
$file = "Meerlingen3.php";
include "login.php"; ?>

                <TD valign = 'top' align = 'center'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {
    $schaap_gateway = new SchaapGateway();
?>

<form action= "Meerlingen3.php" method="post">
<table border = 0> 
<tr align = "center" valign = 'top' ><td colspan = 10>    

<table border = 0>
<tr>
 
<?php if (isset($raak)) { ?>
 <td>
<?php echo View::link_to('print pagina', 'Meerlingen3_pdf.php?Id='.$raak, ['style' => 'color: blue']); ?>
</td>
<?php } elseif (isset($gekozen_ooi)) { ?>
 <td> 
<?php echo View::link_to('print pagina', 'Meerlingen3_pdf.php?Id='.$gekozen_ooi, ['style' => 'color: blue']); ?>
</td>
<?php } ?>
</tr>

</table>        </td></tr>    

<tr><td colspan = 10 align = "center"><h3>lammeren per moederdier </td></tr>
<tr><td colspan = 10 ><hr></td></tr>
<tr><td></td></tr>
<!--    Einde Gegevens tbv MOEDERDIER        -->
<tr><td colspan = 50><table border = 0>

<?php

if(isset($_POST['ascTotat'])) {    $order = "sum(worp)"; }
elseif(isset($_POST['descTotat'])) { $order = "sum(worp) desc"; }
else { $order = "ooi"; }

$ooien_met_meerlingworpen = $schaap_gateway->ooien_met_meerlingworpen1($lidId, $Karwerk, $order);

while ($jm = $ooien_met_meerlingworpen->fetch_assoc()) { 
    // TODO: [overal] niet dit datapompen, maar gewoon de waardes gebruiken waar je ze nodig hebt.
    // Optioneel: fetch_object(), en dan $jm->schaapId.
    // "$jm" vervangen door "$rec", of $record als je wil. "rec" is zo'n standaardafkorting die ik toelaatbaar vind. --BCB
    $ooiId = $jm['schaapId']; 
    $ooi = $jm['ooi'];
    $totat = $jm['totat'];
?>

<tr height = 30 valign = 'bottom'>
 <td style = "font-size : 18px;"> <b><?php echo $ooi; ?></b></td>
 <td style = "font-size : 12px;" ><?php echo 'Totaal : '.$totat.'&nbsp'; ?>
     <input type = "submit" name="ascTotat"  value = "A" style= "font-size:7px";>
    <input type = "submit" name="descTotat" value = "Z" style= "font-size:7px";></td>
</tr>

<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td><b> 2-ling </b><hr></td>
 <td><b> 3-ling </b><hr></td>
 <td><b> 4-ling </b><hr></td> 
 <td><b> 5-ling </b><hr></td>
 <td><b> > 5-ling </b><hr></td>

</tr>

<?php $maand = array(1 => 'Jan','Feb','Mrt','Apr','Mei','Jun','Jul','Aug','Sep','Okt','Nov','Dec'); ?>    
<tr>
 <td>  </td>


 <td>
<!-- Cel waar tweelingen worden getoond -->

<table border = 0>

<?php
$mling2 = $schaap_gateway->aantal_meerlingen_perOoi($lidId,$ooiId,2);
    while($mrl = $mling2->fetch_assoc())
            {
                $vw = $mrl['volwId']; 
            
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl2 = $schaap_gateway->periode($vw);

echo $maand[$p_mrl2[1]].' '.$p_mrl2[2];

 ?>
 </td>
 <td width = 60 align="right" style="font-size: 11px"; >
 <?php
 
$lam_mrl2 = $schaap_gateway->de_lammeren($vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
     echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar tweelingen worden getoond -->
 </td>


 <td>
<!-- Cel waar drielingen worden getoond -->

<table border = 0>

<?php
$mling3 = $schaap_gateway->aantal_meerlingen_perOoi($lidId,$ooiId,3);
    while($mrl = $mling3->fetch_assoc())
            {
                $vw = $mrl['volwId']; 
            
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl3 = $schaap_gateway->periode($vw);

echo $maand[$p_mrl3[1]].' '.$p_mrl3[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = $schaap_gateway->de_lammeren($vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
     echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar drielingen worden getoond -->
 </td>
 
 <td>
<!-- Cel waar vierlingen worden getoond -->

<table border = 0>

<?php
$mling4 = $schaap_gateway->aantal_meerlingen_perOoi($lidId,$ooiId,4);
    while($mrl = $mling4->fetch_assoc())
            {
                $vw = $mrl['volwId']; 
            
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl4 = $schaap_gateway->periode($vw);

echo $maand[$p_mrl4[1]].' '.$p_mrl4[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = $schaap_gateway->de_lammeren($vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
     echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar vierlingen worden getoond -->
 </td>

  <td>
<!-- Cel waar vijflingen worden getoond -->

<table border = 0>

<?php
$mling5 = $schaap_gateway->ooien_met_vijfling($lidId,$ooiId,5);
    while($mrl = $mling5->fetch_assoc())
            {
                $vw = $mrl['volwId']; 
            
 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl5 = $schaap_gateway->periode($vw);

echo $maand[$p_mrl5[1]].' '.$p_mrl5[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = $schaap_gateway->de_lammeren($vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
     echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar vijflingen worden getoond -->
 </td>
   <td>
<!-- Cel waar meer dan vijflingen worden getoond -->

<table border = 0>

<?php
// TODO variabele is misgenaamd. Voorstel: mlingmeer. Beter voorstel: gewoon $res gebruiken overal --BCB
$mling5 = $schaap_gateway->aantal_meerlingen_perOoi($lidId,$ooiId,'6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21,22,23,24,25,26,27,28,29,30');
    while($mrl = $mling5->fetch_assoc()) {
                $vw = $mrl['volwId']; 
?>
<tr>
 <td width = 60 align="left" style = "font-size : 13px";> <?php
 
$p_mrl5 = $schaap_gateway->periode($vw);

echo $maand[$p_mrl5[1]].' '.$p_mrl5[2];

 ?>
 </td>
 <td width = 60 align="right" style = "font-size : 11px";>
 <?php
 
$lam_mrl2 = $schaap_gateway->de_lammeren($vw,$Karwerk);
 
 foreach ($lam_mrl2 as $key => $value) {
     echo $value[0].' '.$value[1].'<br>';
 }
//echo $ooi_st.' '.$geslacht.'<br>';

?>

</td>
</tr>
<?php } ?>
</table>

<!-- Einde Cel waar meer dan vijflingen worden getoond -->
 </td>

 <td style = "font-size : 11px"; >
 </td>
 
 <td align="left" style = "font-size : 11px";>  </td>
 
 <td align = 'right'>  </td>

</tr> <tr>
<tr><td>

</td>
</tr>
<?php 

} // Einde $zoek_ooien_uit_periode
?>
</table>        

<!--    Einde Gegevens tbv LAM    -->    

</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport1.php"; } ?>
</tr>
</table>

</body>
</html>
