<!DOCTYPE html>
<html>
<head>
<title>
	
</title>
<link rel="stylesheet" type="text/css" href="test1_style_header.css">
</head>
<body id='inhoud' >
<center>

<?php $titel = "Header";
	  //$subtitel = "Optimalisering En Rendementverbetering van het Schaap"; ?>

<table>
<thead>	
<tr>		
 <td colspan = 2>

	<table class = 'vast_header_blauw'>
		
	<tr>
	 <td>
	 	<table border = 0>
		<tr align = center style = "font-size : 30px ";>
			<td id="tekst_header_blauw" ><?php echo $titel; ?></td>
		</tr>
		<tr align = center>
			<td><sup style = "font-size : 18px "; ><?php echo $subtitel; ?></sup></td>
		</tr>
		</table>

	 </td>
	 <td >
		<img src='OER_van_OVIS.jpg' class = 'header_afbeelding'> <!-- height bepaald hoogte van blauwe balk -->
	 </td>

	</tr>
	
	</table>
 </td>
</tr>

<?php $host = $_SERVER['HTTP_HOST']; 
if($host == 'localhost:8080' )  	{ $tagid = 'balkOntw'; } 
if($host == 'test.oervanovis.nl') 	{ $tagid = 'balkTest'; }
if($host == 'demo.oervanovis.nl')  	{ $tagid = 'balkDemo'; }
if($host == 'ovis.oervanovis.nl') 	{ $tagid = 'balkProd'; } 

?>

<tr>

<td id = 'vast_header_groen' >
	<?php if($host == 'demo.oervanovis.nl' ) { ?> 
	DEMO &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp DEMO &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp DEMO
<?php } ?></td>
<th colspan='1' height = '20' align = 'right'> <i style = "font-size:12px;" >
<?php if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]) && $curr_url != $url.'index.php') { ?>
	<a href='<?php echo $url; ?>index.php' style = 'color : blue'>uitloggen</a></i>
<?php } ?>

</tr>
</thead>

<TR  >

<!-- Dit mag weg na testen opmaak header -->

</TR>

</table>


<table>

	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
	<tr><td> rij </td>	</tr>
</table>

</center>

<!-- Einde Dit mag weg na testen opmaak header -->

</body>

</html>