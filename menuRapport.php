<!-- 25-11-206 : versie weergave toegevoegd 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.php -->
<html>

<body>
<?php include "javascriptsAfhandeling.php";
include "url.php"; ?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href='<?php echo $url; ?>Home.php' style = 'color : blue'>
Home</a> <br/>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Stallijst.php' style = 'color : blue'>
Stallijst</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>ZoekAfldm.php' style = 'color : blue'>
Afleverlijst</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Mndoverz_fok.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Mndoverz_fok.php' style = 'color : grey'> <?php } ?>
Maandoverz. fokkerij</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Mndoverz_vlees.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Mndoverz_vlees.php' style = 'color : grey'> <?php } ?>
Maandoverz. vleeslam.</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Med_rapportage.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Med_rapportage.php' style = 'color : grey'> <?php } ?>
Medicijn rapportage</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Voer_rapportage.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Voer_rapportage.php' style = 'color : grey'> <?php } ?>
Voer rapportage</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Rapport1.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Rapport1.php' style = 'color : grey'> <?php } ?>
Ooi rapporten</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>MaandTotalen.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>MaandTotalen.php' style = 'color : grey'> <?php } ?>
Maandtotalen</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Groeiresultaat.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Groeiresultaat.php' style = 'color : grey'> <?php } ?>
Groei resultaten</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>ResultHok.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>ResultHok.php' style = 'color : grey'> <?php } ?>
Resultaten</a>
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