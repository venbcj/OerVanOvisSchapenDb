<!-- 14-11-2015 naamwijziging van Medicijnen naar Medicijnenbestand en Voersoorten naar Voerbestand
12-12-2015 :  $versie toegveoged -->
<html>

<body>
<?php include "msg.php";
include "url.php"; ?>

<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href='<?php echo $url;?>Home.php' style = 'color : blue'>
Home</a> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Medicijnen.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Medicijnen.php' style = 'color : grey'> <?php } ?>
Medicijnenbestand</a></hr>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Voer.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Voer.php' style = 'color : grey'> <?php } ?>
Voerbestand</a></hr>
<hr/style ='color : #E2E2E2	'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Inkopen.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Inkopen.php' style = 'color : grey'> <?php } ?>
Inkopen</a></hr>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Voorraad.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Voorraad.php' style = 'color : grey'> <?php } ?>
Voorraad</a></hr>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr><br/>
<hr/style ='color : #E2E2E2'> </hr>

<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?> </i> <br/> <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>



</body>
</html>