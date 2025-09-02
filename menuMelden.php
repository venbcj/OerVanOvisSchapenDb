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
<a href='<?php echo $url;?>Melden.php' style = 'color : blue'> <?php } else { ?>
<a href='<?php echo $url;?>Melden.php' style = 'color : red'> <?php }  
} ?>
Melden RVO</a> <br/>
<hr/style ='color : #E2E2E2'>

<?php if($modmeld == 0) { ?> <a href='<?php echo $url;?>Meldingen.php' style = 'color : grey'> <?php }
else { ?>
<a href='<?php echo $url; ?>Meldingen.php' style = 'color : blue'> <?php } ?>
Meldingen</a>
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