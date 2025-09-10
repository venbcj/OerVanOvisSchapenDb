<?php /* 20-3-2014 Ovv Rina werknr toegevoegd en sortering op werknr van laag naar hoog.
	5-8-2014 karakters werknr variabel gemaakt
	11-8-2014 : veld type gewijzigd in fase
11-3-2015 : Login toegevoegd */
$versie = '11-12-2016'; /* actId = 3 genest */
$versie = '27-03-2017'; /* geslacht niet verplicht gemaakt */
$versie = '28-09-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '14-02-2020'; /* geneste query uit query zoek_stapel gehaald. Was left join en deed verder niks */
$versie = '27-02-2020'; /* SQL beveiligd met quotes en 'Transponder bekend' toegevoegd */
$versie = '19-08-2023'; /* Laatste scan- / controledatum toegevoegd */
$versie = '04-09-2023'; /* Export-xlsx toegevoegd */
$versie = '01-01-2024'; /* and h.skip = 0 aangevuld aan tblHistorie */
$versie = '30-11-2024'; /* Uitgeschaarde dieren toegevoegd */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include login voor include header gezet */
$versie = '19-01-2025'; /* Kolomkop vastgezet tegen de header */
$versie = '11-04-2025'; /* in query toon_aanwezigen en toon_aanwezigen aan subquery haf in where-clause and h.skip = 0 toegevoegd */


 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
<title>Rapport</title>
<style type="text/css">

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
#sortHeader th {
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
$titel = 'Stallijst';
$file = "Stallijst.php";
include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (is_logged_in()) {

function aantal_fase($datb,$lidid,$Sekse,$Ouder) {
$zoeken_aantalFase = mysqli_query($datb,"
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

if($zoeken_aantalFase)
		{	$row = mysqli_fetch_assoc($zoeken_aantalFase);
				return $row['aant'];
		}
		return FALSE; // Foutafhandeling
} 

function aantal_fase_uitgeschaard($datb,$lidid,$Sekse,$Ouder) {
$zoeken_aantalFase_uitgeschaard = mysqli_query($datb,"
SELECT count(distinct(s.schaapId)) aant 
FROM tblSchaap s
 join (
 	SELECT lidId, schaapId, max(stalId) stalId
 	FROM tblStal
 	WHERE lidId = '".mysqli_real_escape_string($datb,$lidid)."'
 	GROUP BY lidId, schaapId
  ) mst on (mst.schaapId = s.schaapId)
 join (
 	SELECT h.stalId, h.actId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	WHERE h.actId = 10
 ) haf on (haf.stalId = mst.stalId)
 left join (
	SELECT st.schaapId
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
WHERE mst.lidId = '".mysqli_real_escape_string($datb,$lidid)."' and ".$Sekse." and ".$Ouder." 
");

if($zoeken_aantalFase_uitgeschaard)
		{	$zau = mysqli_fetch_assoc($zoeken_aantalFase_uitgeschaard);
				return $zau['aant'];
		}
		return FALSE; // Foutafhandeling
} 

$zoek_stapel = mysqli_query($db,"
SELECT count(distinct(s.schaapId)) aant
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(st.rel_best)
") or die (mysqli_error($db));

	while($zs = mysqli_fetch_array($zoek_stapel))
		{ $stapel = $zs['aant']; }

$sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
$ouder = 'isnull(prnt.schaapId)';
$aantalLam_opStal = aantal_fase($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ooi'";
$ouder = 'prnt.schaapId is not null';
$aantalOoi_opStal = aantal_fase($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ram'";
$ouder = 'prnt.schaapId is not null';
$aantalRam_opStal = aantal_fase($db,$lidId,$sekse,$ouder);
?>

<table border = 0 align = "center">

<!-- Aantal dieren -->
<tr>
 <td colspan = 3 align = 'right'> <?php echo 'Aantal schapen '.$stapel; ?> </td>
 <td colspan = 2 style = 'font-size:13px';> &nbsp waarvan</td>
 <td width ="150"><a href = '<?php echo $url;?>Stallijst_pdf.php' style = 'color : blue' > print pagina </a></td>
 <td colspan = 2 ><a href="exportStallijst.php?pst=<?php echo $lidId; ?>'"> Export-xlsx </a></td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';> <?php  echo ' - '.$aantalLam_opStal. ' lammeren'; ?> </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';>
<?php
if($aantalOoi_opStal == 1) 		{ echo "- $aantalOoi_opStal moeder"; }
else if($aantalOoi_opStal > 1)  { echo "- $aantalOoi_opStal moeders"; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 2 style = 'font-size:13px';>
<?php
if($aantalRam_opStal == 1) 		{ echo '- ' .$aantalRam_opStal. ' vader'; }
else if($aantalRam_opStal > 1)  { echo '- ' .$aantalRam_opStal. ' vaders'; }
?>
 </td>
</tr>

<?php
$zoek_uitgeschaarden = mysqli_query($db,"
SELECT s.levensnummer, right(s.levensnummer, $Karwerk) werknum, s.transponder, date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, best.naam, haf.actId
FROM tblSchaap s
 join (
 	SELECT schaapId, max(stalId) stalId
 	FROM tblStal
 	WHERE lidId = '".mysqli_real_escape_string($db,$lidId)."'
 	GROUP BY schaapId
  ) mst on (mst.schaapId = s.schaapId)
 left join (
 	SELECT st.schaapId, h.datum
 	FROM tblHistorie h
 	 join tblStal st on (st.stalId = h.stalId)
 	WHERE h.actId = 1 and h.skip = 0
 ) hg on (s.schaapId = hg.schaapId) 
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId)
 join tblStal st on (st.stalId = mst.stalId)
 join (
 	SELECT relId, naam
 	FROM tblPartij p
 	 join tblRelatie r on (p.partId = r.partId)
 	WHERE p.lidId = '".mysqli_real_escape_string($db,$lidId)."'
 ) best on (best.relId = st.rel_best)
 join (
 	SELECT h.stalId, h.actId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and haf.actId = 10
") or die (mysqli_error($db));

$aantal_uitgeschaarden = mysqli_num_rows($zoek_uitgeschaarden);


 if($aantal_uitgeschaarden > 0) { ?>
<tr>
 <td colspan = 2></td>	
 <td colspan = 2 style = "font-size:12px";>
	<a href="#Uitgeschaarden" style = "color:blue";> Uitgeschaarde schapen </a>
 </td>
</tr>
<?php } ?>
</table>
<!-- Einde Aantal dieren -->

<br>

<?php
$toon_aanwezigen = mysqli_query($db,"
SELECT u.ubn, s.transponder, right(s.levensnummer, $Karwerk) werknum, s.levensnummer, date_format(hg.datum,'%Y%m%d') gebdm_sort, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, scan.dag_sort, scan.dag, haf.actId
FROM tblSchaap s
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (u.ubnId = st.ubnId)
 left join tblHistorie hg on (st.stalId = hg.stalId and hg.actId = 1 and hg.skip = 0) 
 left join (
	SELECT st.schaapId, datum
	FROM tblStal st
	 join tblHistorie h on (st.stalId = h.stalId)
	WHERE h.actId = 3 and h.skip = 0
 ) prnt on (prnt.schaapId = s.schaapId) 
 left join (
 	SELECT contr_scan.schaapId, date_format(datum,'%Y%m%d') dag_sort, date_format(datum,'%d-%m-%Y') dag
 	FROM tblHistorie h
 	 join (
	 	SELECT max(hisId) hismx, schaapId
	 	FROM tblHistorie h
	 	 join tblStal st on (h.stalId = st.stalId)
	 	WHERE actId = 22 and h.skip = 0 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
	 	GROUP BY schaapId
	) contr_scan on (contr_scan.hismx = h.hisId)
 ) scan on (scan.schaapId = s.schaapId)
 left join (
 	SELECT h.stalId, h.actId
 	FROM tblHistorie h
 	 join tblStal st on (h.stalId = st.stalId)
 	 join tblActie a on (h.actId = a.actId)
 	WHERE a.af = 1 and h.skip = 0
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(haf.actId)
ORDER BY u.ubn, right(s.levensnummer, $Karwerk)

") or die (mysqli_error($db)); ?>

<table border = 0 id="sortableTable" align = "center">

<?php
if(mysqli_num_rows($toon_aanwezigen) > 0) { ?>

<thead>
<tr id="sortHeader" class = "StickyHeader">
 <th onclick="sortTable(0)"> <br> Mijn ubn <span id="arrow0" class="inactive"></span> <hr></th>
 <th onclick="sortTable(1)"> Transponder<br> bekend <span id="arrow1" class="inactive"></span> <hr></th>
     <th onclick="sortTable(2)"> <br> Werknr <span id="arrow2" class="inactive"></span> <hr></th>
     <th onclick="sortTable(3)"> <br> Levensnummer <span id="arrow3" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(4)"> Geboren sortering <span id="arrow5" class="inactive"></span> <hr></th>
     <th onclick="sortTable(4)"> <br> Geboren <span id="arrow4" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 80 onclick="sortTable(6)"> Geslacht <span id="arrow6" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 80 onclick="sortTable(7)"> Generatie <span id="arrow7" class="inactive"></span> <hr></th>
     <th style="display:none;" valign="bottom";width= 50 onclick="sortTable(8)"> Laatstecontrole sortering <span id="arrow9" class="inactive"></span> <hr></th>
     <th valign="bottom";width= 50 onclick="sortTable(8)"> Laatste<br> controle <span id="arrow8" class="inactive"></span> <hr></th>
</tr>

</thead>


<?php } // Einde if(mysqli_num_rows($toon_aanwezigen) > 0) ?>

<tbody id="tbody_1">
<?php
while($ta = mysqli_fetch_array($toon_aanwezigen))
{
	$ubn = $ta['ubn'];
	$transponder = $ta['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
	$werknr = $ta['werknum'];
	$levnr = $ta['levensnummer'];
  $gebdm_sort = $ta['gebdm_sort'];
	$gebdm = $ta['gebdm'];
	$geslacht = $ta['geslacht']; 
	$aanw = $ta['aanw']; 
  $lstScan_sort = $ta['dag_sort'];
	$lstScan = $ta['dag']; 
	$actId_af = $ta['actId']; 
	if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; }

/*if(isset($vorig_ubn) && $vorig_ubn != $ubn) { ?> 
<tr><td colspan="15"><hr></td></tr>
<?php }*/ ?>

<tr align = "center">	   
 <td width = 100 style = "font-size:13px;"> <?php echo $ubn; ?> <br> </td>
 <td width = 100 style = "font-size:13px;"> <?php echo $transp; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td style="display:none;"> <?php echo $gebdm_sort; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td style="display:none;"> <?php echo $lstScan_sort; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $lstScan; ?> <br> </td>
</tr>				

	<?php /*$vorig_ubn = $ubn;*/ } // Einde while($ta = mysqli_fetch_array($result)) ?>

</tbody>
</table>

<?php
/* UITGESCHAARDE DIEREN */
if($aantal_uitgeschaarden > 0) { 

$sekse = "(isnull(s.geslacht) or s.geslacht is not null)";
$ouder = 'isnull(prnt.schaapId)';
$aantalLam_uitschaar = aantal_fase_uitgeschaard($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ooi'";
$ouder = 'prnt.schaapId is not null';
$aantalOoi_uitschaar = aantal_fase_uitgeschaard($db,$lidId,$sekse,$ouder);

$sekse = "s.geslacht = 'ram'";
$ouder = 'prnt.schaapId is not null';
$aantalRam_uitschaar = aantal_fase_uitgeschaard($db,$lidId,$sekse,$ouder);	?>

<table border = 0 align = "center">
<tr id="Uitgeschaarden" height = 150> <td></td></tr>
<tr> <td colspan="14"  align="center" valign="bottom"> UITGESCHAARDE SCHAPEN </td></tr>

<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalLam_uitschaar == 1) 	  { echo '- ' .$aantalLam_uitschaar. ' lam'; }
else if($aantalLam_uitschaar > 1) { echo '- ' .$aantalLam_uitschaar. ' lammeren'; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalOoi_uitschaar == 1) 	  { echo '- ' .$aantalOoi_uitschaar. ' moeder'; }
else if($aantalOoi_uitschaar > 1) { echo '- ' .$aantalOoi_uitschaar. ' moeders'; }
?>
 </td>
</tr>
<tr>
 <td colspan = 2></td>
 <td colspan = 3 align="center" style = 'font-size:13px';>
<?php
if($aantalRam_uitschaar == 1)	  { echo '- ' .$aantalRam_uitschaar. ' vader'; }
else if($aantalRam_uitschaar > 1) { echo '- ' .$aantalRam_uitschaar. ' vaders'; } ?>
 </td>
</tr>
</table>

<table border = 0 id="sortableTable_2" align = "center">
<thead>
<tr id="sortHeader" class = "StickyHeader" style = "font-size:12px;">
 <th onclick="sortTable_2(0)"> Transponder<br> bekend <span id="arrow2_0" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(1)"> <br> Werknr <span id="arrow2_1" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(2)"> <br> Levensnummer <span id="arrow2_2" class="inactive"></span> <hr></th>
 <th style="display:none;" onclick="sortTable_2(3)"> Geboren sortering <span id="arrow2_4" class="inactive"></span> <hr></th>
 <th onclick="sortTable_2(3)"> <br> Geboren <span id="arrow2_3" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 80 onclick="sortTable_2(5)"> Geslacht <span id="arrow2_5" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 80 onclick="sortTable_2(6)"> Generatie <span id="arrow2_6" class="inactive"></span> <hr></th>
 <th valign="bottom";width= 50 onclick="sortTable_2(7)"> Bestemming <span id="arrow2_7" class="inactive"></span> <hr></th>
</tr>

</thead>

<tbody id="tbody_2">
<?php

while($zu = mysqli_fetch_array($zoek_uitgeschaarden))
	{
	$transponder = $zu['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
	$werknr = $zu['werknum'];
	$levnr = $zu['levensnummer'];
	$gebdm_sort = $zu['gebdm_sort'];
	$gebdm = $zu['gebdm'];
	$geslacht = $zu['geslacht']; 
	$aanw = $zu['aanw']; 
	$bestemming = $zu['naam']; 
	$actId_af = $zu['actId']; 
if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; } ?>

<tr align = "center">
 <td width = 100 style = "font-size:13px;"> <?php echo $transp; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
 <td style="display:none;"> <?php echo $gebdm_sort; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $gebdm; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $fase; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $bestemming; ?> <br> </td>

 <td width = 50> </td>
</tr>				
		
	<?php
		} // Einde while($zu = mysqli_fetch_array($result))
		?> 
		</tbody>
		</table>
		<?php
} // Einde if(mysqli_num_rows($zoek_uitgeschaarden) > 0) ?>
<!-- EINDE UITGESCHAARDE DIEREN -->
		

		</TD>
<?php
include "menuRapport.php"; } ?>

		</TR>
	</tbody>
</table>

<script>
/* SORTEREN TABEL Bron : https://www.youtube.com/watch?v=av5wFcAtuEI */
    let sortOrder = []; // creëer een array met de naam sortOrder

    function sortTable(columnIndex) {
        const table = document.getElementById('sortableTable'),
              tbody = table.querySelector('tbody'),
              tbody_1 = document.getElementById('tbody_1'),
              rows = Array.from(tbody_1.querySelectorAll('tr'));

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

    function sortTable_2(columnIndex) {
        const table = document.getElementById('sortableTable_2'),
              tbody = table.querySelector('tbody'),
              tbody_2 = document.getElementById('tbody_2'),
              rows = Array.from(tbody_2.querySelectorAll('tr'));

        // TOGGLE BETWEEN ASCENDING AND DESCENDING ORDER
        sortOrder[columnIndex] = (sortOrder[columnIndex] === 'asc') ? 'desc' : 'asc';

        // UPDATE ARROW INDICATORS IN THE HEADER
        for (let i = 0; i < table.rows[0].cells.length; i++) {
            const arrow = document.getElementById('arrow2_' + i);
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
