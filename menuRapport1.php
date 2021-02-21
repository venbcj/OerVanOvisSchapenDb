<!-- 25-11-2006 : versie weergave toegevoegd -->
<html>

<body>
<?php include "msg.php";
include "url.php"; ?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href='<?php echo $url; ?>Home.php' style = 'color : blue'>
Home</a> <br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Ooikaart.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Ooikaart.php' style = 'color : grey'> <?php } ?>
Ooikaart detail</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>OoikaartAll.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>OoikaartAll.php' style = 'color : grey'> <?php } ?>
Ooikaart moeders</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Meerlingen5.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Meerlingen5.php' style = 'color : grey'> <?php } ?>
Meerling in periode</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Meerlingen.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Meerlingen.php' style = 'color : grey'> <?php } ?>
Meerling per geslacht</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Meerlingen2.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Meerlingen2.php' style = 'color : grey'> <?php } ?>
Meerlingen per jaar</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Meerlingen3.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Meerlingen3.php' style = 'color : grey'> <?php } ?>
Meerling oplopend</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Meerlingen4.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Meerlingen4.php' style = 'color : grey'> <?php } ?>
Meerlingen aanwezig</a>

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