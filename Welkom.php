<?php 

require_once("autoload.php");

require_once('url_functions.php');
// # TODO: waar komen de modules vandaan? in header.tpl.php wordt op hun aanwezigheid gerekend. --BCB

$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '9-1-2020'; /* schapencentrum.. vervangen door Oer van Ovis */
$versie = '26-12-2024'; /* <TD width = 1160 height = 400 valign = "top"> gewijzigd naar <TD valign = 'top'>  */

 session_start(); ?>  
<!DOCTYPE html>
<html>
<head>
<title>Welkom</title>
</head>
<body>

<?php
$titel = 'Welkompagina';
$file = "";
include "header.tpl.php"; ?>

		<TD valign = 'top'>

<form action= "Welkom.php" method= "post" >
<table border = 0 align = "center">
<tr>
<td valign = 'top'>
	<table border = 0>
<tr style = "color : blue;"><td colspan = 7 align = "center">
<h3>Welkom in de demo-omgeving van het management programma<br/> OER van OVIS.<br/><br/>
</td></tr>
<tr>
<td colspan = 2>
Voordat u verder gaat even het volgende.<br/>
Nadat u zodadelijk uw eigen demo account hebt aangemaakt is het programma gevuld met een aantal basisgegevens.<br/>
Zonder eerst uw eigen administratie in te voeren beschikt u dus direct over een hoeveelheid fictieve gegevens.<br/><br/>
Naar wens kunt u zelf aanvullen of wijzigen zonder dat er iets fout of verloren kan gaan. Om u op weg te helpen vindt u na inloggen op de homepagina <i style = "color : blue;"> een instructieboekje</i>. U krijgt hiermee een indruk van wat het managementprogramma te bieden heeft ter ondersteuning en <i style = "color : blue;"> verbetering van uw bedrijfsproces</i>.<br/>
Het programma kent drie modules :<br/>
&nbsp&nbsp&nbsp- Module melden RVO<br/>
&nbsp&nbsp&nbsp- Technische module<br/>
&nbsp&nbsp&nbsp- Financiële module<br/>
Alle modules zijn in deze demo-omgeving beschikbaar.<br/><br/>

Let op : Elke maand worden alle gegevens teruggezet naar de basisgegevens.<br/> <br/>
</td></tr>



<tr>
<td colspan = 2>Wilt u een <i style = "color : blue;">geheel eigen omgeving</i> neem dan contact op met ons. 
U krijgt dan toegang tot de productie omgeving van OER van OVIS.<br/> Vanaf dat moment kunt u onbeperkt beschikken over uw eigen administratie. De eerste maand is kostenloos.<br/><br/>
Neem bij vragen gerust contact met ons op. Wij staan u graag te woord en denken eventueel mee uw administratie over te zetten naar het managementprogramma.<br/><br/>
Met vriendelijke groeten, </td></tr>
<tr><td>
Oer van Ovis 
</td>
<td><a href=' <?php echo $url; ?>index.php' style = "color : blue"> Terug </a>&nbsp&nbsp&nbsp <a href=' <?php echo $url; ?>Welkom2.php' style = "color : blue"> Verder </a></td></tr>
<tr><td>
06-48400813
</td></tr>

</tr>



<tr><td colspan = 7 align = "center" height = 20></td></tr>
</table>


<td valign = top>
	<table border = 0>
	<tr><td height = 80 align = "center" valign = 'top' ><img src='deklijst.jpg' width='285' height='220'>
	</td></tr>
	<tr><td height = 400 valign = "center"><img src='ooikaart.jpg' width='325' height='250'>
	</td></tr>
	<tr><td>
	</td></tr>
	</table>

</td></tr>
</table>




	</TD>

</tr>

</table>
</form>

</body>
</html>
