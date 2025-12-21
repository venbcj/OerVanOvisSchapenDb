<?php

require_once("autoload.php");

$versie = "16-12-2017"; /* Rapport gemaakt */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
Session::start();
?>
<html>
<head>
<title>Groeiresultaat schapen</title>
</head>
<body>
<center>
<?php
$titel = 'Groei resultaten per schaap';
$subtitel = '';
$file = "Groeiresultaat.php";
include "login.php";
?>
        <TD width = 960 height = 400 valign = "top" >
<?php
if (Auth::is_logged_in()) {
    if ($modtech == 1) {
        $schaap_gateway = new SchaapGateway();
        $result = $schaap_gateway->groeiresultaat($lidId, $Karwerk);
?>
<table border = 0 >
<tr>
<td> </td>
<td>
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geslacht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 50>Geboorte kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Weging 1<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 200>Speen kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Weging 2<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Weging 3<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 80>Aflever kg<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign="bottom";width= 60>Gem groei per dag<hr></th>
<th style = "text-align:center;"valign="bottom";width= 80></th>
<th width = 600></th>
<th width= 60 ></th>
</tr>
<?php
        while ($row = mysqli_fetch_array($result)) {
?>
<tr align = "center">
       <td width = 0> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $row['werknr']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $row['geslacht']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $row['gebkg']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 200 style = "font-size:15px;"> <?php echo $row['wg1']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 200 style = "font-size:15px;"> <?php echo $row['spkg']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 100 style = "font-size:15px;"> <?php echo $row['wg2']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 80 style = "font-size:15px;"> <?php echo $row['wg3']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 80 style = "font-size:15px;"> <?php echo $row['afvkg']; ?> <br> </td>
       <td width = 1> </td>
       <td width = 60 style = "font-size:15px;"> <?php echo $row['gemgroei']; ?> <br> </td>
</tr>
<?php
        }
?>
</tr>
</table>
        </TD>
<?php
    } else {
?>
    <img src='resultHok_php.jpg'  width='970' height='550'/>
<?php
    }
    include "menuRapport.php";
}
?>
</body>
</html>
