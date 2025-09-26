<?php /* 20-3-2014 Ovv Rina werknr toegevoegd en sortering op werknr van laag naar hoog.
	5-8-2014 karakters werknr variabel gemaakt
	11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '11-12-2016'; /* actId = 3 genest */
$versie = '27-03-2017'; /* geslacht niet verplicht gemaakt */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '14-02-2020'; /* geneste query uit query $stapel gehaald. Was left join en deed verder niks */
$versie = '27-02-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '19-08-2023'; /* Laatste scan- / controledatum toegevoegd */
$versie = '04-09-2023'; /* Export-xlsx toegevoegd */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'>  */

 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Stallijst';
$subtitel = '';
Include "header.php"; ?>
		<TD valign = 'top'>
<?php
$file = "Stallijst.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { 

function aantal_fase($datb,$lidid,$Sekse,$Ouder) {
$vw_aantalFase = mysqli_query($datb,"
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE st.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and isnull(st.rel_best) and ".$Sekse." and ".$Ouder." 
");

if($vw_aantalFase)
		{	$row = mysqli_fetch_assoc($vw_aantalFase);
				return $row['aant'];
		}
		return FALSE; // Foutafhandeling
} ?>

<table Border = 0 align = "center">

<!-- Aantal dieren -->

<tr>
 <td colspan = 7 align = 'right'>
<?php
$stapel = mysqli_query($db,"
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
") or die (mysqli_error($db));

	while($rij = mysqli_fetch_array($stapel))
		{ 	   echo "Aantal schapen ".$rij['aant'];		}
?>
 </td>
 <td colspan = 4 style = 'font-size:13px';>&nbsp waarvan</td>
 <td width ="150"><a href = '<?php echo $url;?>Stallijst_pdf.php' style = 'color : blue' > print pagina </a></td>
 <td colspan = 4 ><a href="exportStallijst.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a></td>
</tr>
<tr>
 <td colspan = 5></td>
 <td colspan = 6 style = 'font-size:13px';>
<?php
$sekse = "(isnull(s.geslacht) or s.geslacht is not null)";;
$ouder = 'isnull(prnt.schaapId)';
$lammer = aantal_fase($db,$lidId,$sekse,$ouder);
		   echo " - $lammer lammeren ";		
?>
</td>
</tr>
<tr>
 <td colspan = 5></td>
 <td colspan = 4 style = 'font-size:13px';>
<?php
unset($sekse);
unset($ouder);
$sekse = "s.geslacht = 'ooi'";
$ouder = 'prnt.schaapId is not null';
$moeders = aantal_fase($db,$lidId,$sekse,$ouder);

if($moeders == 1)
{ echo "- $moeders moeder";}
else if($moeders > 1)
{ echo "- $moeders moeders";}
?>
</td>
</tr>
<tr>
 <td colspan = 5></td>
 <td colspan = 4 style = 'font-size:13px';>
<?php
unset($sekse);
unset($ouder);
$sekse = "s.geslacht = 'ram'";
$ouder = 'prnt.schaapId is not null';
$vaders = aantal_fase($db,$lidId,$sekse,$ouder);

if($vaders == 1)
{ echo "- $vaders vader";}
else if($vaders > 1)
{ echo "- $vaders vaders";}
		
?>
</td>
</tr>

<!-- Einde Aantal dieren -->

<tr>
 <td> </td>

 <td></td>	<?php
$result = mysqli_query($db,"
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, scan.dag
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0) 
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join (
 	SELECT contr_scan.schaapId, date_format(datum,'%d-%m-%Y') dag
 	FROM tblHistorie h
 	 join (
	 	SELECT max(hisId) hismx, schaapId
	 	FROM tblHistorie h
	 	 join tblStal st on (h.stalId = st.stalId)
	 	WHERE actId = 22 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 	GROUP BY schaapId
	) contr_scan on (contr_scan.hismx = h.hisId)
 ) scan on (scan.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
ORDER BY right(s.levensnummer, $Karwerk)
") or die (mysqli_error($db));
 ?>


<tr style = "font-size:12px;">
 <th width = 0 height = 30></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Transponder<br> bekend<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Werknr<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 80>Levensnummer<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Geboren<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Geslacht<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Generatie<hr></th>
 <th width = 1></th>
 <th style = "text-align:center;"valign="bottom";width= 100>Laatste<br> controle<hr></th>
 <th width = 1></th>
 <th width=60></th>
</tr>

<?php
	while($row = mysqli_fetch_array($result))
	{
	$transponder = $row['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
	$werknr = $row['werknum'];
	$levnr = $row['levensnummer'];
	$gebdm = $row['gebdm'];
	$geslacht = $row['geslacht']; 
	$aanw = $row['aanw']; 
	$lstScan = $row['dag']; 
	if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } ?>
<tr align = center>	
 <td width = 0> </td>	   
 <td width = 100 style = "font-size:13px;"> <?php echo $transp; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 1> </td>	   
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm; ?> <br> </td>	   
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 1> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $lstScan; ?> <br> </td>
 <td width = 1> </td>
 <td width = 50> </td>
</tr>				
		
	<?php	} ?>
</tr>				
</table>
		</TD>
<?php
Include "menuRapport.php"; } ?>

</body>
</html>
