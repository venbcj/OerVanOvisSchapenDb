<?php

require_once("autoload.php");

$versie = '29-8-2020'; /* kopie gemaakt van MedOverzSchaap.php */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Registratie</title>
</head>
<body>

<?php
$titel = 'Voorraadcorrectie';
$file = "Voorraad.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
# 4176 is Auth::is_logged_in() ook goed?
if ((Session::isset("U1")) && (Session::isset("W1"))) {

    $artId = 0;
if (!empty($_GET['pst']))
    {    $artId = $_GET['pst'];    }
      else
    {     /*$artId = $_POST['txtArtId_'];*/ 

        $ink_id = 0;
    foreach ($_POST as $name => $value) {
   //echo $name.'<br>'; 
   //echo $value;

   $split = explode('_', $name) ;
    $ink_id = $split[1]; // Laatste veldnaam moet wel een recordId hebben in de naam
}
$inkoop_gateway = new InkoopGateway();
$artikel_gateway = new ArtikelGateway();
$artId = $inkoop_gateway->findArtikel($ink_id);

    }

    $soort = $artikel_gateway->zoek_soort($artId);

if(isset($_POST['knpSave_'])) { include "save_voorraadcorrectie.php";  }
                
?>
<form action="Voorraadcorrectie.php" method="post">
<table border = 0>
<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;"valign= bottom ;>Artikel<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Inkoop moment<hr></th>
<?php if($soort == 'pil') { ?> 
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>chargenummer<hr></th>
<?php } ?>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Voorraad<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Gecorrigeerd<hr></th>
 <th width = 60></th>
 <th style = "text-align:center;"valign= bottom ;width= 50>Correctie aantal<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign= bottom ;width= 80> Actie <hr></th>
 <th width = 300></th>
 <th style = "text-align:right;"valign= bottom ;width= 80> <input type = "submit" name = "knpSave_" value = "Opslaan" > </th>

</tr>
<?php
if($soort == 'pil') {
    $result = $artikel_gateway->pilregels($artId);
}
if($soort == 'voer') {
    $result = $artikel_gateway->voerregels($artId);
}

while($row = mysqli_fetch_assoc($result)) {
                $Id = $row['inkId'];
                $naam = $row['naam'];
                $toedm = $row['toedm'];
                $naam = $row['naam'];
                $charge = $row['charge'];
                $totat = $row['totat'];
                $eenh = $row['eenheid'];

$afboek = $inkoop_gateway->zoek_afgeboekt($Id);
?>

<tr>
 <td width = 0> </td>
 <td width = 300 align = "center" style = "font-size:15px;"> <?php echo $naam; ?> <br> 
    <input type="hidden" name="txtArtId_" value= <?php echo $artId; ?> >
 </td>

 <td width = 1> </td>              
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $toedm; ?> <br> </td>
<?php if($soort == 'pil') { ?> 
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $charge; ?> <br> </td>
<?php } ?>
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <?php echo $totat.' '.$eenh; ?> </td>
  <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:13px;"> <?php if(isset($afboek)) { echo $afboek.' '.$eenh; } ?> </td>
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> <input type="text" size = 1 name="<?php echo "txtCorat_$Id"; ?>" >
     <?php echo ' '.$eenh; ?> </td>
 <td width = 1> </td>
 <td width = 100 align = "center" style = "font-size:15px;"> 
<select name= "<?php echo "kzlCorr_$Id"; ?>" style= "width:90;" > 
<?php
$opties = array('af' => 'Afboeken', 'bij' => 'Bijboeken');
foreach ( $opties as $key => $waarde)
{
   $keuze = '';
   if(isset($_POST["kzlCorr_$Id"]) && $_POST["kzlCorr_$Id"] == $key)
   {
        $keuze = ' selected ';
   }
   echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
} ?>
 </select>
 </td>
       
<?php       }

?>

</form>
</table>


        </TD>
<?php
include "menuInkoop.php"; } ?>
    </tr>

</table>

</body>
</html>
