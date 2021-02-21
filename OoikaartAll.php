<?php /* 6-5-2014 : Kolom 'aantal dagen moeder'moeder verwijderd
		query verwijderd omdat het gebruik ervan onbekend is en variabelen worden niet gebruikt :
		$post = mysqli_query($db,"SELECT hoknr, doelgroep FROM vw_Hoklijsten Where levensnummer = '$levnr'  ") or die (mysqli_error($db));

	while ($rij=mysqli_fetch_assoc($post))
	{ $pstdoel = $rij['doelgroep'];
	$psthoknr = $rij['hoknr'];
	}
	
4-8-2014 werknr variabel gemaakt 
11-8-2014 : veld type gewijzigd in fase 
11-3-2015 : Login toegevoegd */
$versie = '26-11-2016';  /* actId = 3 uit on clause gehaald en als sub query genest */
$versie = '8-12-2016';  /* actId = 1 uit on clause gehaald en als sub query genest */
$versie = '10-3-2017';  /* join tblRas gewijzigd naar left join tblRas */
$versie = '5-8-2017';  /* Gem groei bij spenen toegevoegd */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
 session_start(); ?>
<html>
<head>
<title>Rapport</title>
</head>
<body>

<center>
<?php
$titel = 'Ooikaart';
$subtitel = '';
Include "header.php";?>
<TD width = 960 height = 400 valign = "top" align = "center">
<?php
$file = "OoikaartAll.php";
Include "login.php"; 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { ?>

<form action= "OoikaartAll.php" method="post">

<table border = 0 id="myTable2">	
			
<tr style = "font-size:12px;">
<th width = 0 height = 30></th>
<th width = 1 height = 30></th>
<th onclick="sortTable(2)" style = "text-align:center;"valign= bottom width= 80><u>Levensnummer</u><hr></th>
<th width = 1></th>
<th onclick="sortTable(3)" style = "text-align:center;"valign= bottom width= 80><u>Werknr</u><hr></th>
<th width = 1></th>
<th onclick="sortTable(6)" style = "text-align:center;"valign= bottom width= 280>Ras<hr></th>
<th width = 1></th>
<th onclick="sortTable(7)" style = "text-align:center;"valign= bottom >Geboortedatum<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal lammeren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal levend geboren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>% levend geboren<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal ooien<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Aantal rammen<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 60>Gem geboorte gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 50>Gespeend<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem speen gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem groei<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 50>Afgeleverd<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem aflever gewicht<hr></th>
<th width = 1></th>
<th style = "text-align:center;"valign= bottom width= 140>Gem groei<hr></th>

<th width = 60></th>

<th style = "text-align:center;"valign= bottom width= 80></th>
<th width = 600></th>

 </tr>

<?php


$result = mysqli_query($db,"
select mdr.schaapId, mdr.levensnummer, right(mdr.levensnummer,$Karwerk) werknr, r.ras, hg.datum dmgebrn, date_format(hg.datum,'%d-%m-%Y') geb_datum, date_format(haf.datum,'%d-%m-%Y') afleverdm, date_format(hdo.datum,'%d-%m-%Y') uitvaldm,  
 count(lam.schaapId) lammeren, count(lam.levensnummer) levend, round(((count(lam.levensnummer) / count(lam.schaapId)) * 100),2) percleven, count(ooi.schaapId) aantooi, count(ram.schaapId) aantram, round(avg(hg_lm.kg),2) gemgewicht, 
 count(hs_lm.datum) aantspn, ((count(hs_lm.datum)/count(lam.schaapId))*100) percspn, round(avg(hs_lm.kg),2) gemspnkg, round(avg(hs_lm.kg-hg_lm.kg),2) gemgr_spn,
 count(haf_lm.datum) aantafv, round(avg(haf_lm.kg),2) gemafvkg, round(avg(haf_lm.kg-hg_lm.kg),2) gemgr_afv 
from tblSchaap mdr 
 left join tblVolwas v on (mdr.schaapId = v.mdrId)
 left join tblSchaap lam on (v.volwId = lam.volwId)
 join tblStal st on (mdr.schaapId = st.schaapId)
 join (
	select st.schaapId
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 3 and h.skip = 0
 ) ouder on (mdr.schaapId = ouder.schaapId)
 left join (
	select st.schaapId, datum
	from tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	where h.actId = 1
 ) hg on (st.schaapId = hg.schaapId)
 left join tblHistorie haf on (st.stalId = haf.stalId and haf.actId = 13)
 left join tblHistorie hdo on (st.stalId = hdo.stalId and hdo.actId = 14)
 left join tblRas r on (r.rasId = mdr.rasId)
 left join tblSchaap ooi on (lam.schaapId = ooi.schaapId and ooi.geslacht = 'ooi')
 left join tblSchaap ram on (lam.schaapId = ram.schaapId and ram.geslacht = 'ram')
 left join tblStal st_lm on (lam.schaapId = st_lm.schaapId)
 left join tblHistorie hg_lm on (st_lm.stalId = hg_lm.stalId and hg_lm.actId = 1)
 left join tblHistorie hs_lm on (st_lm.stalId = hs_lm.stalId and hs_lm.actId = 4)
 left join tblHistorie haf_lm on (st_lm.stalId = haf_lm.stalId and haf_lm.actId = 12)
where st.lidId = ".mysqli_real_escape_string($db,$lidId)." and mdr.geslacht = 'ooi' and isnull(haf.datum) and isnull(hdo.datum)
group by mdr.levensnummer, r.ras, hg.datum, date_format(haf.datum,'%d-%m-%Y'), date_format(hdo.datum,'%d-%m-%Y')
") or die (mysqli_error($db));	

{	
while($row = mysqli_fetch_assoc($result))
			{
				$schaapId = $row['schaapId'];
				$levnr = $row['levensnummer'];
				$werknr = $row['werknr'];
				$ras = $row['ras'];
				$dmgeb = $row['dmgebrn'];
				$gebdm = $row['geb_datum'];
				$lammeren = $row['lammeren'];
				$levend = $row['levend'];
				$percleven = $row['percleven'];
				$aantooi = $row['aantooi'];
				$aantram = $row['aantram'];
				$gemkg = $row['gemgewicht'];
				$aantspn = $row['aantspn'];
				$percspn = $row['percspn'];
				$gemspn = $row['gemspnkg'];
				$gemgr_spn = $row['gemgr_spn'];
				$aantafl = $row['aantafv'];
				$gemafl = $row['gemafvkg'];
				$gemgr_afv = $row['gemgr_afv'];

?>

<tr align = center>	
 <td width = 0> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levnr; ?> <br> </td>
 <td width = 1 style = "font-size:0px;"> <?php echo $werknr; ?> </td>   
 <td width = 100 style = "font-size:14px;"> <a href=' <?php echo $url; ?>Ooikaart.php?pstId=<?php echo $schaapId; ?>' style = "color : blue">
<?php echo $werknr; ?></a> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $ras; ?> <br> </td>
 <td width = 1   style = "font-size:0px;"> <?php echo $dmgeb; ?> </td>	   	   
 <td width = 100 style = "font-size:12px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 1> </td>  	   
 <td width = 100 style = "font-size:14px;"> <?php echo $lammeren; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $levend; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:14px;"> <?php echo $percleven; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $aantooi; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:14px;"> <?php echo $aantram; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:14px;"> <?php echo $gemkg; ?> <br> </td>	
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantspn; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:12px;"> <?php echo $gemspn; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $gemgr_spn; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $aantafl; ?> <br> </td>
 <td width = 1> </td>	
 <td width = 100 style = "font-size:12px;"> <?php echo $gemafl; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:12px;"> <?php echo $gemgr_afv; ?> <br> </td>
 <td width = 1> </td>

 <td width = 80 style = "font-size:13px;" >

<?php	}  ?>			
 </td> 
<?php } ?>	   
</tr>
</table>

</form>


</TD>

<?php 
} else { ?> <img src='ooikaartAll_php.jpg'  width='950' height='500'/> <?php }
Include "menuRapport1.php"; } 
include "table_sort.php"; ?>
</tr>
</table>
</center>

</body>
</html>
