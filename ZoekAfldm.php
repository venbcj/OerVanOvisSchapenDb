<?php 

require_once("autoload.php");

$versie = '20-2-2015'; /* login toegevoegd */ 
$versie = '19-12-2015'; /* Uitval toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '30-12-2023'; /* h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd  */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top"> gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 include login voor include header gezet */

 Session::start();
 ?>
<!DOCTYPE html>
<html>
<head>
<title>Zoekafleverdatum</title>
</head>
<body>

<?php 
$titel = 'Keuze afleverdatum t.b.v. VKI';
$file = "ZoekAfldm.php";
include "login.php"; ?>

        <TD align = "center" valign = "top">
<?php
if (Auth::is_logged_in()) { ?>

<table border = 0 width= 200 height = 200 align = "left" >
<tr> <td> </td> </tr> </table>

<br>

<form action="AfleverLijst.php" method="post"> 
<b> Kies een afleverdatum : </b><br/><br/>

<?php
    $schaap_gateway = new SchaapGateway($db);
$result = $schaap_gateway->afleverdatum($lidId);
?>
 <select style="width:200;" name="kzlPost" >";
 <option></option>
<?php        while($row = mysqli_fetch_array($result))
        {
                $dag = $row['datum'];
                $bedrijf = $row['relId'];
              $hisId = $row['hisId'];
              $ant = $row['aantal'];
              $bestm = $row['naam'];

            $opties= array($hisId=>$dag.'&nbsp &nbsp'.$bestm.'&nbsp &nbsp'.$ant);
            foreach ( $opties as $key => $waarde)
            {
                        $keuze = '';
        
        if(isset($_GET['kzlPost']) && $_GET['kzlPost'] == $key)
        {
            $keuze = ' selected ';
        }
                
        echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
            }
        
        }
?> </select>

&nbsp &nbsp &nbsp <input type = "submit" name="knpToon" value = "Toon" >
</form>

    </TD>
<?php
include "menuRapport.php"; }?>

    </body>
    </html>
