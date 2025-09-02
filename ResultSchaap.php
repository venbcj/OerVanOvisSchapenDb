<?php /* 8-8-2014 Aantal karakters werknr variabel gemaakt en html buiten php geprogrammeerd 
13-3-2015 : Login toegevoegd */
$versie = "22-1-2017"; /* 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel		22-1-2017 tblBezetting gewijzigd naar tblBezet */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '27-01-2025'; /* Sortering toegepast en vastzetten kolomkop. De gegevens klopte niet. Queries daarom ook aangepast. */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Resultaat schapen</title>
<style type="text/css">

i {
	font-size:12px;
}

b {
	font-size:13px;
}

	/* VASTZETTEN KOLOMKOP */
table {
  border-collapse: collapse; /* Dit zorgt ervoor dat de cellen tegen elkaar aan staan */
}

tr.StickyHeader th { /* Binnen de table row met class StickyHeader wordt deze opmaak toegepast op alle th velden */
  background: white;
  position: sticky;
  top: 86px; /* Don't forget this, required for the stickiness */
  box-shadow: 0 2px 2px -1px rgba(0, 0, 0, 0.4);
}
/* Einde VASTZETTEN KOLOMKOP */

/* SORTEREN TABEL Bron : https://www.youtube.com/watch?v=av5wFcAtuEI */
th {
    cursor: pointer;
    font-size: 12px;
    /*text-align: center; dit doet niets */
    /*vertical-align: text-bottom; dit doet niets */
    height: 30px;
    border: 0px solid blue;
    /*background-color: rgb(207, 207, 207);*/
}

.desc:after {
    content: ' ▼'; /*Alt 31*/
}

.asc:after {
    content: ' ▲'; /*Alt 30*/
}

.inactive:after {
    content: ' ▲';
    color: grey;
    opacity: 0.5;
}
/* Einde SORTEREN TABEL */
</style>
</head>
<body>

<?php
$titel = 'Resultaten per schaap uit 1 periode';
$file = "ResultHok.php";
Include "login.php"; ?>

		<TD valign = 'top' align="center">
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

$periId = $_GET['pstId'];

$zoek_doelId = mysqli_query($db,"
SELECT p.hokId, ho.hoknr, p.doelId, d.doel, p.dmafsluit, date_format(p.dmafsluit,'%d-%m-%Y') afsluitdm
FROM tblPeriode p
 join tblHok ho on (p.hokId = ho.hokId)
 join tblDoel d on (p.doelId = d.doelId)
WHERE periId = ".mysqli_real_escape_string($db,$periId)."
") or die (mysqli_error($db));
	while($zd = mysqli_fetch_assoc($zoek_doelId))
	{
	 $hokId = $zd['hokId'];
	 $hok = $zd['hoknr'];
	 $doelId = $zd['doelId'];
	 $groep = $zd['doel'];
	 $dmafsl = $zd['dmafsluit'];
	 $afsldm = $zd['afsluitdm'];
	}

$zoek_start_periode = mysqli_query($db,"
SELECT max(dmafsluit) dmStart, date_format(max(dmafsluit),'%d-%m-%Y') Startdm
FROM tblPeriode
WHERE hokId = '".mysqli_real_escape_string($db,$hokId)."' and doelId = '".mysqli_real_escape_string($db, $doelId)."' and dmafsluit < '".mysqli_real_escape_string($db,$dmafsl)."'
") or die (mysqli_error($db));
	while($zsp = mysqli_fetch_assoc($zoek_start_periode))
	{ 
	 $dmStartPeriode = $zsp['dmStart'];
	 $StartPeriodedm = $zsp['Startdm'];
	}  

if($doelId == 1) { $fase_tijdens_betreden_verblijf = '( (isnull(spn.datum) and isnull(prnt.datum)) or h.datum < spn.datum)'; }
if($doelId == 2) { $fase_tijdens_betreden_verblijf = '((h.datum >= spn.datum and (isnull(prnt.datum) or h.datum < prnt.datum)) or (isnull(spn.datum) and h.datum < prnt.datum))'; }
if($doelId == 3) { $fase_tijdens_betreden_verblijf = '(h.datum >= prnt.datum or ht.datum > prnt.datum)'; }


$zoek_periode_met_aantal_schapen = mysqli_query($db,"
SELECT min(h.datum) dmEerste_in, date_format(min(h.datum),'%d-%m-%Y') eerste_inDm, date_format(max(ht.datum),'%d-%m-%Y') laatste_uit, count(distinct(st.schaapId)) aant_schapen, count(b.bezId) aant_beweging
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join (
 	SELECT schaapId, datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 4 and skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
 	SELECT schaapId, datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 3 and skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0
 and ".$fase_tijdens_betreden_verblijf."
 and (h.datum < '".mysqli_real_escape_string($db,$dmafsl)."' and (isnull(ht.datum) or ht.datum > '".mysqli_real_escape_string($db,$dmStartPeriode)."'))
") or die (mysqli_error($db));

	while($zpmas = mysqli_fetch_assoc($zoek_periode_met_aantal_schapen))
	{ 
	 $schapen = $zpmas['aant_schapen'];
	 $bewegingen = $zpmas['aant_beweging'];
	 $dmEerste_in = $zpmas['dmEerste_in'];
	 $eerste_inDm = $zpmas['eerste_inDm'];
	 $laatste_uit = $zpmas['laatste_uit'];
	} 

if($dmStartPeriode < $dmEerste_in || !isset($dmStartPeriode)) { $StartPeriodedm = $eerste_inDm; } ?>

<table Border = 0>
<tr>
 <td colspan = 3 align = "right"><b style = "font-size:20px;"> <?php echo $hok; ?> </b></td> 
 <td colspan = 3 ><i> &nbsp &nbsp Doelgroep : </i><b> <?php echo $groep; ?> </b></td> 
 <td colspan = 7 ><i> &nbsp &nbsp Periode : </i><b><?php echo $StartPeriodedm." - ".$afsldm; ?></b></td>
</tr>
<tr>
 <td colspan= 6 align="right"><i> Aantal schapen : </i><b> <?php echo $schapen; ?> </b></td>
<?php if($bewegingen > $schapen) { ?>
<tr>
 <td colspan= 6 align="right"><i> Aantal bewegingen : </i><b> <?php echo $bewegingen; ?> </b></td>
<?php } ?>
</tr>
</table>

<?php
	/*
	SELECT right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(h.datum,'%Y%m%d') indm_sort, date_format(h.datum,'%d-%m-%Y') indm, date_format(ht.datum,'%Y%m%d') uitdm_sort, date_format(ht.datum,'%d-%m-%Y') uitdm, datediff(ht.datum, h.datum) schpdgn, h.kg kgin, ht.kg kguit, round((ht.kg-h.kg)/datediff(ht.datum, h.datum)*1000,2) gemgroei, date_format(hdo.datum,'%Y%m%d') uitvdm_sort, date_format(hdo.datum,'%d-%m-%Y') uitvdm, a.actie status
	*/
$zoek_inhoud_periode = mysqli_query($db,"
SELECT right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(h.datum,'%Y%m%d') indm_sort, date_format(h.datum,'%d-%m-%Y') indm, date_format(ht.datum,'%Y%m%d') uitdm_sort, date_format(ht.datum,'%d-%m-%Y') uitdm, datediff(ht.datum, h.datum) schpdgn, h.kg kgin, ht.kg kguit, round((ht.kg-h.kg)/datediff(ht.datum, h.datum)*1000,2) gemgroei, date_format(hdo.datum,'%Y%m%d') uitvdm_sort, date_format(hdo.datum,'%d-%m-%Y') uitvdm, a.actie status
FROM tblBezet b
 join tblHistorie h on (b.hisId = h.hisId)
 join tblStal st on (h.stalId = st.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblRas r on (r.rasId = s.rasId)
 left join 
 (
	SELECT b.bezId, min(h2.hisId) hist
	FROM tblBezet b
	 join tblHistorie h1 on (b.hisId = h1.hisId)
	 join tblActie a1 on (a1.actId = h1.actId)
	 join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
	 join tblActie a2 on (a2.actId = h2.actId)
	 join tblStal st on (h1.stalId = st.stalId)
	WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a2.uit = 1 and h1.skip = 0 and h2.skip = 0 and h1.actId != 2
	GROUP BY b.bezId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join tblHistorie hdo on (hdo.hisId = uit.hist and hdo.actId = 14)
 left join tblActie a on (a.actId = ht.actId)
 left join (
 	SELECT schaapId, datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 4 and skip = 0
 ) spn on (st.schaapId = spn.schaapId)
 left join (
 	SELECT schaapId, datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE actId = 3 and skip = 0
 ) prnt on (st.schaapId = prnt.schaapId)
WHERE b.hokId = '".mysqli_real_escape_string($db,$hokId)."' and h.skip = 0
 and ".$fase_tijdens_betreden_verblijf."
 and (h.datum < '".mysqli_real_escape_string($db,$dmafsl)."' and (isnull(ht.datum) or ht.datum > '".mysqli_real_escape_string($db,$dmStartPeriode)."'))
ORDER BY st.schaapId, b.hisId
") or die (mysqli_error($db)); ?>
 
<table Border = 0 id="sortableTable" align = "center">
  <thead> 
<tr class = "StickyHeader" style = "font-size:12px;">
 <th onclick="sortTable(0)" width= 80> <br> Werknr <span id="arrow0" class="inactive"></span><hr></th>
 <th onclick="sortTable(1)" width= 80> <br> Ras <span id="arrow1" class="inactive"></span><hr></th>
 <th onclick="sortTable(2)" width= 80> <br> Geslacht <span id="arrow2" class="inactive"></span><hr></th>
 <th style="display:none;" onclick="sortTable(3)" > <br> Datum erin sorteren <span id="arrow4" class="inactive"></span><hr></th>
 <th onclick="sortTable(3)" width= 80> <br> Datum erin <span id="arrow3" class="inactive"></span><hr></th>
 <th style="display:none;" onclick="sortTable(5)"> <br> Datum eruit sorteren <span id="arrow6" class="inactive"></span><hr></th>
 <th onclick="sortTable(5)" width= 80> <br> Datum eruit <span id="arrow5" class="inactive"></span><hr></th>
 <th onclick="sortTable(7)" width= 80> Schaap-<br>dagen <span id="arrow7" class="inactive"></span><hr></th>
 <th onclick="sortTable(8)" width= 80> Begin<br>gewicht <span id="arrow8" class="inactive"></span><hr></th>
 <th onclick="sortTable(9)" width= 80> Eind<br>gewicht <span id="arrow9" class="inactive"></span><hr></th>
 <th onclick="sortTable(10)" width= 80> <br> Gem groei <span id="arrow10" class="inactive"></span><hr></th>
 <th onclick="sortTable(11)" width= 80>Reden uit verblijf <span id="arrow11" class="inactive"></span><hr></th>
</tr>
 </thead>
<tbody>
<?php
		while($zip = mysqli_fetch_array($zoek_inhoud_periode))
		{ 	
			$werknr = $zip['werknr'];
			$ras = $zip['ras'];
			$geslacht = $zip['geslacht'];
			$indm_sort = $zip['indm_sort'];
			$indm = $zip['indm'];
			$uitdm_sort = $zip['uitdm_sort'];
			$uitdm = $zip['uitdm'];
			$uitvdm_sort = $zip['uitvdm_sort'];
			$uitvdm = $zip['uitvdm'];
			$schpdgn = $zip['schpdgn'];
			$kgin = $zip['kgin'];
			$kguit = $zip['kguit'];
			$gemgroei = $zip['gemgroei'];

			if($groep == 'Geboren' && $zip['status'] == 'Eruit') { $status = 'Gespeend'; } 
		   else if($groep == 'Gespeend' && $zip['status'] == 'Eruit') { $status = 'Afgeleverd'; }
		   else { $status = $zip['status']; } ?>
		
<tr align = "center">
 <td style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>	
 <td style="display:none;" style = "font-size:15px;"> <?php echo $indm_sort ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $indm ?> <br> </td>
<?php	   If (empty($uitdm))
{ ?>
 <td style="display:none;" style = "font-size:15px;"> <?php echo $uitvdm_sort; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $uitvdm; ?> <br> </td>
<?php }
else	
{ ?>
 <td style="display:none;" style = "font-size:15px;"> <?php echo $uitdm_sort; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $uitdm; ?> <br> </td>
<?php } ?>
 <td style = "font-size:15px;"> <?php echo $schpdgn; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $kgin; ?> <br> </td>
 <td wstyle = "font-size:15px;"> <?php echo $kguit; ?> <br> </td>
 <td style = "font-size:15px;"> <?php echo $gemgroei; ?> <br> </td>
 <td style = "font-size:15px;"> <?php if(isset($status)) { echo $status; } else {echo "Onbekend"; } ?> <br> </td>
</tr>				
		
<?php		} ?>
	
</tbody>			
</table>


		</TD>
<?php
Include "menuRapport.php"; } ?>

<script type="text/javascript">
/* SORTEREN TABEL Bron : https://www.youtube.com/watch?v=av5wFcAtuEI */
    let sortOrder = []; // creëer een array met de naam sortOrder

    function sortTable(columnIndex) {
        const table = document.getElementById('sortableTable'),
              tbody = table.querySelector('tbody'),
              rows = Array.from(tbody.querySelectorAll('tr'));

        // TOGGLE BETWEEN ASCENDING AND DESCENDING ORDER
        sortOrder[columnIndex] = (sortOrder[columnIndex] === 'asc') ? 'desc' : 'asc';

        // UPDATE ARROW INDICATORS IN THE HEADER
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            const arrow = document.getElementById('arrow' + i);
            arrow.className = (i === columnIndex) ? sortOrder[columnIndex] : 'inactive';
        }

        // SORT THE ROWS BASED ON THE CONTENT OF THE SELECTED COLUMN
        rows.sort((a,b) => {
            const aValue = a.children[columnIndex].textContent.trim(),
                  bValue = b.children[columnIndex].textContent.trim();
            return sortOrder[columnIndex] === 'asc'
            ? aValue.localeCompare(bValue, undefined, {numeric: true, sensitivity: 'base'})
            : bValue.localeCompare(aValue, undefined, {numeric: true, sensitivity: 'base'});
        });

        // CLEAR THE EXISTING TABLE BODY
        tbody.innerHTML = '';

        // APPEND THE SORTED ROWS TO THE TABLE BODY
        rows.forEach(row => tbody.appendChild(row));
    }
/* Einde SORTEREN TABEL */
</script>
</body>
</html>
