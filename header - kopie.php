<!-- 12-12-2015 : Link uitloggen alleen tonen als is ingelogd 
	 28-9-2018 titel.php verwijderd. Zit in header.php samen met Style.css -->
<html>
<head>
	<link rel="stylesheet" type="text/css" href="Style.css">
</head>
<body>
<?php include "url.php"; ?>

<table border = 0 >
<tr>
 <td colspan = 2>	

	<table id = 'titel' width = '1390' border = 0 > <!-- Width bepaald totale breedte -->
	<tr>
	 <td width = '1390' align = 'center' style = 'font-size:30px;'> <!-- Width zorgt dat afbeelding rechts staat -->
		<table border = 0>
		<tr align = center style = "font-size : 30px ";>
			<td><?php echo $titel; ?></td>
		</tr>
		<tr align = center>
			<td><sup style = "font-size : 18px "; ><?php echo $subtitel; ?></sup></td>
		</tr>
		</table>
	 </td>
	 <td>
		<img src='OER_van_OVIS.jpg' width='175' height='57'> <!-- height bepaald hoogte van blauwe balk -->
	 </td>
	</tr>
	</table>


<?php $host = $_SERVER['HTTP_HOST']; 
if($host == 'localhost:8080' )  	{ $tagid = 'balkOntw'; } 
if($host == 'test.oervanovis.nl') 	{ $tagid = 'balkTest'; }
if($host == 'demo.oervanovis.nl')  	{ $tagid = 'balkDemo'; }
if($host == 'ovis.oervanovis.nl') 	{ $tagid = 'balkProd'; } 

?>
 </td>
</tr>
<tr>
<td id = <?php echo $tagid; ?> colspan='2' height = '20' align= center valign='top'>
	<?php if($host == 'demo.oervanovis.nl' ) { ?> 
	DEMO &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp DEMO &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp &nbsp DEMO
<?php } ?></td>
<th colspan='1' height = '20' align = 'right'> <i style = "font-size:12px;" >
<?php if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]) && $curr_url != $url.'index.php') { ?>
	<a href='<?php echo $url; ?>index.php' style = 'color : blue'>uitloggen</a></i>
<?php } ?>
</th>
</tr>

<TR>




</body>
</html>