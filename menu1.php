<!-- 6-11-2014 Melden RVO toegevoegd 
26-2-2015 url aangepast 
14-11-2015 naamwijziging van Inkoop naar Voorraadbeheer en Medicijn registratie naar Medicijn toediening
18-11-2015 Hok gewijzigd naar verblijf 
6-12-2015 :  $versie toegveoged 
19-12-2015 : query $moduleFinancieel verplaatst naar login.php 
20-12-2020 : Alerts toegevoegd -->
<html>

<body>
<?php include "msg.php";
include "url.php"; 
?>
<td width = '150' height = '100' valign='top'>
Menu : </br>
<hr/style ='color : #A6C6EB'>
<a href= '<?php echo $url;?>Home.php' style = 'color : blue'>
Home</a> <br/>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>InvSchaap.php' style = 'color : blue'>
Invoer nieuwe schapen</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>InlezenReader.php' style = 'color : blue'>
Inlezen reader</a> <br/>
<hr/style ='color : #E2E2E2'>
<?php if($modmeld == 0) { ?> <a href='<?php echo $url;?>Melden.php' style = 'color : grey'> <?php }
else {
// Kijken of er nog meldingen openstaan
$req_open = mysqli_query($db,"
SELECT count(*) aant
FROM tblRequest r
 join tblMelding m on (r.reqId = m.reqId)
 join tblHistorie h on (h.hisId = m.hisId)
 join tblStal st on (st.stalId = h.stalId)
WHERE st.lidId = ".mysqli_real_escape_string($db,$lidId)." and h.skip = 0 and isnull(r.dmmeld) and m.skip <> 1 ") or die (mysqli_error($db));
		$row = mysqli_fetch_assoc($req_open);
			$num_rows = $row['aant'];
		if($num_rows == 0){  ?>
<a href='<?php echo $url;?>Meldpagina.php' style = 'color : blue'> <?php } else { ?>
<a href='<?php echo $url;?>Meldpagina.php' style = 'color : red'> <?php }  
} ?>
RVO</a> <br/>
<hr/style ='color : #E2E2E2'>

	<?php if($modtech == 0 && $modmeld == 1) { ?>
<a href='<?php echo $url;?>Afvoerstal.php' style = 'color : blue'>
Afvoerlijst</a>
	<?php } else { ?>
<a href='<?php echo $url;?>Bezet.php' style = 'color : blue'>
Verblijven in gebruik</a>
	<?php } ?>
	
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Zoeken.php' style = 'color : blue'>
Schaap opzoeken</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Med_registratie.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Med_registratie.php' style = 'color : grey'> <?php } ?>
Medicijn toediening</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Dracht.php' style = 'color : blue'>
Dracht</a>
<hr/style ='color : #E2E2E2'>

<?php if($modmeld == 0) { ?> <a href='<?php echo $url;?>Meldingen.php' style = 'color : grey'> <?php }
else { ?>
<a href='<?php echo $url; ?>Alerts.php' style = 'color : blue'> <?php } ?>
Raederalerts</a>
<hr/style ='color : #E2E2E2'>

<a href='<?php echo $url;?>Rapport.php' style = 'color : blue'>
Rapporten</a>
<hr/style ='color : #E2E2E2'>
<a href='<?php echo $url;?>Beheer.php' style = 'color : blue'>
Beheer</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Inkoop.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Inkoop.php' style = 'color : grey'> <?php } ?>
Voorraadbeheer</a>
<hr/style ='color : #E2E2E2'>
<?php  if($modtech == 1) { ?> <a href='<?php echo $url;?>Finance.php' style = 'color : blue'> <?php }
					else { ?> <a href='<?php echo $url;?>Finance.php' style = 'color : grey'> <?php } ?>
Financieel</a>
<hr/style ='color : #E2E2E2'>


<?php if(isset($versie)) { ?>
<i style = "color : #E2E2E2;"><?php echo "versie : ".$versie; ?> </i> <br/> <?php } ?>
<i style = "color : #E2E2E2;"><?php echo "ingelogd : ".$_SESSION["U1"]; ?></i>
</td>



</body>
</html>