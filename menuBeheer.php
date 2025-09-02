<!-- 18-11-2015 : hok verandert in verblijf 
12-12-2015 :  $versie toegveoged 
1-6-2020 : Uitval en redenen gewijzigd naar Redenen en momenten 
12-02-2021 : Systeemgegevens gewijzigd naar Instellingen 
29-8-2021 : msg.php gewijzigd naar javascriptsAfhandeling.php 
22-10-2023 : Readerversie toegevoegd -->
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
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Hok.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Hok.php' style = 'color : grey'> <?php } ?>
Verblijven</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Ras.php' style = 'color : blue'>
Rassen</a>
<hr/style ='color : #E2E2E2	'>
<a href='<?php echo $url; ?>Uitval.php' style = 'color : blue'>
Redenen en momenten</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Combireden.php' style = 'color : blue'>
Combi redenen</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Vader.php' style = 'color : blue'>
Dekrammen</a>
<hr/style ='color : #E2E2E2'> <br/>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Eenheden.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Eenheden.php' style = 'color : grey'> <?php } ?>
Eenheden</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Relaties.php' style = 'color : blue'>
Relaties</a>
<hr/style ='color : #E2E2E2'>
<?php if($reader != 'Agrident')	  { ?> <a href='<?php echo $url; ?>Readerversies.php' style = 'color : grey'> <?php }
 else if(isset($actuele_versie))  { ?> <a href='<?php echo $url; ?>Readerversies.php' style = 'color : blue'> <?php }
							else  { ?> <a href='<?php echo $url; ?>Readerversies.php' style = 'color : red'>  <?php } ?>
Readerversies</a>
<br/>
<?php if($modbeheer == 1 ) { ?>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Gebruikers.php' style = 'color : blue'>
Gebruikers</a> <?php } else { ?>
<hr/style ='color : #E2E2E2'> <br/>
<?php } ?>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url; ?>Systeem.php' style = 'color : blue'>
Instellingen</a>
<hr/style ='color : #E2E2E2'>

<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?> </i> <br/> <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>



</body>
</html>