<!-- 6-12-2015 :  $versie toegveoged 
28-12-2016 : linken grijs bij module niet in gebruik 
29-12-2016 : Archief gewijzigd in Betaalde 
29-08-2021: msg.php gewijzigd naar javascriptsAfhandeling.php 
07-01-2025: De omschrijving Invulformulier gewijzigd naar Inboeken -->

<html>

<body>
<?php include "javascriptsAfhandeling.php"; ?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href='<?php echo $url;?>Home.php' style = 'color : blue'>
Home</a> <br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Kostenopgaaf.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Kostenopgaaf.php' style = 'color : grey'> <?php } ?>
Inboeken</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Deklijst.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Deklijst.php' style = 'color : grey'> <?php } ?>
Deklijst</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Liquiditeit.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Liquiditeit.php' style = 'color : grey'> <?php } ?>
Liquiditeit</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Saldoberekening.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Saldoberekening.php' style = 'color : grey'> <?php } ?>

Saldoberekening</a>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Rubrieken.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Rubrieken.php' style = 'color : grey'> <?php } ?>
Rubrieken</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Componenten.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Componenten.php' style = 'color : grey'> <?php } ?>
Componenten</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modfin == 1) { ?> <a href='<?php echo $url;?>Kostenoverzicht.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Kostenoverzicht.php' style = 'color : grey'> <?php } ?>
Betaalde posten</a>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'>

<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?></i>  <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>



</body>
</html>