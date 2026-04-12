<?php 
$versie = '11-04-2026'; /* gemaakt */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<?php
$titel = 'Schapen per verblijf';
$file = "AlertHoknrSelectie.php";
Include "login.php"; ?>

		<TD valign = 'top' align = 'center'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) {

if(isset($_POST['knpStuur_']) && !empty($_POST['kzlHok'])) { 

$hokId = $_POST['kzlHok'];

$zoek_laatste_selectie = mysqli_query($db,"
SELECT max(volgnr) volgnr
FROM tblAlertselectie
WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
") or die (mysqli_error($db));

while($zls = mysqli_fetch_assoc($zoek_laatste_selectie))
			{
				$old_volgnr = $zls['volgnr']; }

if(empty($old_volgnr)) { $volgnr = 1; } else { $volgnr = $old_volgnr + 1; }

$zoek_nu_in_hok_met_transponder = "
SELECT s.levensnummer, s.transponder
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join 
 (
	SELECT h.hisId hisId_in, 
        LEAD(h.hisId) OVER (
            PARTITION BY h.stalId
            ORDER BY h.datum, h.hisId
        ) AS hisId_tot
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and (a.aan = 1 or a.uit = 1 or a.af = 1)
 ) uit on (uit.hisId_in = b.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.hisId_tot) and h.skip = 0 and s.transponder is not null
ORDER BY s.transponder
";

$zoek_transponder = mysqli_query($db,$zoek_nu_in_hok_met_transponder) or die (mysqli_error($db));	
	while($zt = mysqli_fetch_assoc($zoek_transponder))
			{
				$transponder = $zt['transponder'].$zt['levensnummer']; 

 $insert_tblAlertselectie  = "INSERT INTO tblAlertselectie set volgnr = '".mysqli_real_escape_string($db,$volgnr)."', lidId = '".mysqli_real_escape_string($db,$lidId)."', transponder = '".mysqli_real_escape_string($db,$transponder)."', alertId = 8 ";


/*echo $insert_tblAlertselectie.'<br>';*/ mysqli_query($db,$insert_tblAlertselectie) or die (mysqli_error($db));


			}

$goed = 'De levensnummers zijn verstuurd en staan klaar om naar de reader te worden vertuurd.';

} // Einde if(isset($_POST['knpStuur_']) && !empty($_POST['kzlHok'])) ?>

<form action= "AlertHoknrSelectie.php" method="post">
<table border = 0> 

<tr>
<td><i>Verblijf</i></td>
</tr>
<?php $verblijven_nu_bezet = mysqli_query($db,"
SELECT b.hokId, ho.hoknr
FROM tblBezet b
 join tblHok ho on (b.hokId = ho.hokId)
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT h.hisId hisId_in, 
        LEAD(h.hisId) OVER (
            PARTITION BY h.stalId
            ORDER BY h.datum, h.hisId
        ) AS hisId_tot
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and (a.aan = 1 or a.uit = 1 or a.af = 1)
 ) uit on (uit.hisId_in = b.hisId)
WHERE ho.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(uit.hisId_tot) and h.skip = 0
GROUP BY b.hokId, ho.hoknr
ORDER BY ho.hoknr
") or die(mysqli_error($db));
?>
<tr>
<td><select name="kzlHok" style="width:100;" >
	 <option></option>	
	<?PHP	while($row = mysqli_fetch_array($verblijven_nu_bezet))
			{
			
				$opties= array($row['hokId']=>$row['hoknr']);
				foreach ( $opties as $key => $waarde)
				{
							$keuze = '';
			
			if(isset($_POST['kzlHok']) && $_POST['kzlHok'] == $key)
			{
				$keuze = ' selected ';
			}
					
			echo '<option value="' . $key . '" ' . $keuze .'>' . $waarde . '</option>';
				}
			
			} ?>
	 </select>
 </td>

 <td>
  <input type="submit" name="knpToon_" value="Toon">
 </td>

</tr>
<tr><td colspan = 10 ><hr></td></tr>

<tr><td colspan = 50><table border = 0>
<?php if((isset($_POST['knpToon_']) || isset($_POST['knpStuur_'])) && !empty($_POST['kzlHok']) ) {

$hokId = $_POST['kzlHok'];

$aantal_nu_in_hok = "
SELECT count(h.hisId) aant
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
	SELECT h.hisId hisId_in, 
        LEAD(h.hisId) OVER (
            PARTITION BY h.stalId
            ORDER BY h.datum, h.hisId
        ) AS hisId_tot
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and (a.aan = 1 or a.uit = 1 or a.af = 1)
 ) uit on (uit.hisId_in = b.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.hisId_tot) and h.skip = 0
";

$zoek_nu_in_hok = "
SELECT s.levensnummer, s.transponder
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (st.schaapId = s.schaapId)
 left join 
 (
	SELECT h.hisId hisId_in, 
        LEAD(h.hisId) OVER (
            PARTITION BY h.stalId
            ORDER BY h.datum, h.hisId
        ) AS hisId_tot
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (st.ubnId = u.ubnId)
     join tblActie a on (h.actId = a.actId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and (a.aan = 1 or a.uit = 1 or a.af = 1)
 ) uit on (uit.hisId_in = b.hisId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and isnull(uit.hisId_tot) and h.skip = 0
ORDER BY s.transponder
";

$aantal_nu_in_hok = mysqli_query($db,$aantal_nu_in_hok) or die (mysqli_error($db));
	$anih = mysqli_fetch_assoc($aantal_nu_in_hok);
	$aantal = $anih['aant'];
				?>
<tr>
	<td colspan = 5>Dit zijn de levensnummers uit het gekozen verblijf. <br>Klik op de knop 'Verstuur' om deze levensnummers <br> klaar te zetten om naar de reader te sturen.<br> </td>
</tr>

<tr height = 75 align = "center" style = "font-size : 14px;" >
 <td></td>
 <td align="center"><br> <b> <?php echo $aantal.' Levensnummers'; ?>  </b><hr></td>

 <td valign="top"> <input type="submit" name="knpStuur_" value="Verstuur"> <br><br></td>
 <td></td>
</tr>


<?php
$toon_levensnummers = mysqli_query($db,$zoek_nu_in_hok) or die (mysqli_error($db));	
	while($tl = mysqli_fetch_assoc($toon_levensnummers))
			{
				$transp = $tl['transponder'];
				$levnr = $tl['levensnummer'];

?>
<tr align = "center" style = "font-size : 14px;"  >
 <td></td>
 <td> <?php echo $levnr; ?> </td>
 <td><?php if(empty($transp)) { echo 'Transponder is onbekend! '; } ?></td>
</tr>
<tr> <td colspan = 4 ><hr></td>
</tr>

<?php } // Einde while($mrl = mysqli_fetch_assoc($toon_levensnummers)) 
	} // Einde if(isset($_POST['knpToon_']) || isset($_POST['knpStuur_'])) ?>
</table>		


</td></tr></table>
</form>

</TD>
<?php } else { ?> <img src='ooikaart_php.jpg'  width='970' height='550'/> <?php }
Include "menuAlerts.php"; } ?>
</tr>
</table>

</body>
</html>
