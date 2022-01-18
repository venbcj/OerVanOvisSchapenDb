<!-- 20-12-2020 : Pagina gemaakt 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.php -->
<html>

<body>
<?php include "javascriptsAfhandeling.php"; ?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href='<?php echo $url; ?>Home.php' style = 'color : blue'>
Home</a> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>OoilamSelectie.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>OoilamSelectie.php' style = 'color : grey'> <?php } ?>
Ooitjes uit meerlingen</a>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'>

<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?> </i> <br/> <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>



</body>
</html>