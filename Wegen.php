<?php

require_once("autoload.php");

/*13-3-2015 : Login toegevoegd */
$versie = '3-4-2018'; /* : Tussenweging toegevoegd bij UpdatSchaap.php */
$versie = '16-6-2018'; /* : Kalender toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '01-01-2024'; /* h.skip = 0 aangevuld bij tblHistorie en sql beveiligd met quotes */
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
$titel = 'Tussenwegingen';
$file = "Wegen.php";
include "login.php"; ?>

        <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) {
$historie_gateway = new HistorieGateway();
$stal_gateway = new StalGateway();
$schaap_gateway = new SchaapGateway();

If(empty($_GET['pstId']))  { $schaapId = $_POST['txtlevnr'] ?? ''; } else { $schaapId = "$_GET[pstId]"; }

include "kalender.php";

if (isset($_POST['knpSave']))
{
// Controle 1 x per dag mag een schaap een tussenweging doen
$date = date_create($_POST['txtdatum']);
        $datum = date_format($date, 'Y-m-d');
        
$rows_dag = $historie_gateway->dagwegingen($lidId, $schaapId, $datum);

$stalId = $stal_gateway->zoek_laatste_stalId($lidId, $schaapId);

    /*Is maxdatum van laatste stalId !! bijv. als aangekocht én geboortedatum bestaat !!*/
$day1 = $historie_gateway->eerste_datum_schaap($stalId);
$dag1 = mens_datum($day1);

$endday = $historie_gateway->laatste_datum_schaap($stalId);
$enddag = mens_datum($endday);

     if(empty($_POST['txtdatum']) && !empty($_POST['txtgram']))    { $fout = "De weegdatum is niet ingevuld"; 
    $kg = $_POST['txtgram']; }
else if(empty($_POST['txtgram']) && !empty($_POST['txtdatum'])) { $fout = "Het weeggewicht is niet ingevuld";
    $dag = $_POST['txtdatum']; }
else if ($rows_dag > 0)         { $fout = "Een schaap kan maar 1 keer per dag worden gewogen.";
    $kg = $_POST['txtgram']; }
else if (!empty($_POST['txtdatum']) && !empty($_POST['txtgram']) && $rows_dag == 0 )
        {
        
        $date = date_create($_POST['txtdatum']);
        $datum = date_format($date, 'Y-m-d');

     if($datum < $day1)        { $fout = "De datum mag niet voor ".$dag1." liggen.";
    $kg = $_POST['txtgram']; }
else if(isset($endday) && $datum > $endday)        { $fout = "De datum mag niet na ".$enddag." liggen.";
    $kg = $_POST['txtgram']; }
else {
$newkg = $_POST["txtgram"];

$historie_gateway->wegen_invoeren($stalId, $datum, $newkg);
    }
        }
} ?>
<form action="Wegen.php" method = "post">
<table border = 0 valign = "top" > <!-- table 1 -->
<tr>
 <td valign = "top">
    <table border = 0 valign = "top"> <?php // table 2
    $weeg = $schaap_gateway->weeg($lidId, $schaapId);

        while ($row = mysqli_fetch_array($weeg))
        {
        $schaapId =  $row['schaapId'];
        $levnr =  $row['levensnummer']; ?>    

    
    <tr>
     <td colspan = 4 align = "left"><i style = "font-size:14px;"> Levensnummer :</i> <b style = "font-size:15px;"> <?php echo $levnr; ?> </b></td>
     <td> <input type = "hidden" name ="txtlevnr" value= <?php echo $schaapId; ?> > </td> <!--hiddden-->
    </tr>
<?php        } ?>

    <tr><td height = 40></td></tr>
    <tr>
     <td align = "right">Datum : </td>
     <td></td>
     <td><input type="text" id = "datepicker1" name = "txtdatum" size = 9 value= <?php if(isset($dag)) { echo $dag; } ?>></td>
     <td></td>
     <td>Gewicht : </td>
     <td></td>
     <td><input type="text" name = "txtgram" size = 9 value = <?php if(isset($kg)) { echo $kg; } ?> ></td>
    </tr>
    <tr><td colspan = 8 align = "center"><input type = "submit" name = "knpSave" value = "Opslaan"> </td>       
    </tr>
    </table> <!-- EInde table 2 -->
</td>

<td valign = "top"> 
    <table border = 0 > <!-- table 3 -->
    <tr>
     <td></td>
     <td colspan = 4 align = "center"> Eerdere wegingen </td>
    </tr>
    <tr style = "font-size:12px;">
     <th width = 50 height = 30></th>
     <th width= 120 style = "text-align:center;"valign="bottom">weegnr<hr></th>
     <th width = 1></th>
     <th width= 120 style = "text-align:center;"valign="bottom">Datum weging<hr></th>
     <th width = 1></th>
     <th width= 120 style = "text-align:center;"valign="bottom">Gewicht<hr></th>
     <th width = 0></th>
    </tr>
<?php
        $aantal = $historie_gateway->weegaantal($lidId, $schaapId);

        $weeg = $historie_gateway->weeg($lidId, $schaapId);

        while ($row = mysqli_fetch_array($weeg))
        {
            $date = date_create($row['datum']);
        $weegdm = date_format($date, 'd-m-Y');
        $weegkg = "$row[kg]";
?>
        
                
    <tr align = "center">    
     <td width = 0> </td>
     <td width = 120 style = "font-size:15px;" >  <?php echo $aantal; $aantal = $aantal-1; ?> <br> </td>
     <td width = 1> </td>
     <td width = 120 style = "font-size:15px;" >  <?php echo $weegdm; ?> <br> </td>
     <td width = 1> </td>
     <td width = 120 style = "font-size:15px;">  <?php echo $row['kg']; ?> <br> </td>
     <td width = 1> </td>
    </tr>                
<?php        } ?>
    </table> <!-- Einde table 3-->

 </td>
</tr>                
</table> <!-- Einde table 1 -->
</form>

        </TD>
<?php
include "menu1.php"; } ?>

</body>
</html>
