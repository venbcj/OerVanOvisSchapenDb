<?php
$versie = "01-04-2026"; /* 01-04-2026 :gekopieerd van ResultHok.php */

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
$titel = 'Geadopteerde lammeren';
$file = "Adoptierapport.php";
Include "login.php"; ?>

				<TD valign = 'top'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { if($modtech ==1) { ?>

<script src="sorteren.js"></script>

	<?php
$result = mysqli_query($db,"
SELECT l.schaapId lamId, st.stalId stalId_lam, l.levensnummer, right(l.levensnummer,$Karwerk) werknr, 
b_in.hisId, ho.hoknr, ht.hisId hisId_tot, ht.datum datum_tot, lower(at.actie) actie_uit,
date_format(spn.datum,'%d-%m-%Y') speenDag,

date_format(lh.datum,'%Y%m%d') adopDay_sort, date_format(lh.datum,'%d-%m-%Y') adopDag, right(amdr.levensnummer,$Karwerk) adop_ooi_werknr, ast.rel_best best_amdr, aa.actie last_actie_adop, 
ho_adop.hoknr hoknr_adop, ht_adop.hisId hisId_tot_adop, ht_adop.datum datum_tot_adop, lower(at_adop.actie) actie_uit_adop, 

right(mdr.levensnummer,$Karwerk) bio_ooi_werknr

FROM tblSchaap l
 join (
    SELECT max(st.stalId) stalId, schaapId
    FROM tblStal st
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE isnull(rel_best) and u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY schaapId
 ) max_st_lam on (max_st_lam.schaapId = l.schaapId)
 join tblStal st on (st.stalId = max_st_lam.stalId)
 join tblUbn u on (u.ubnId = st.ubnId)
 join (
    SELECT max(h.hisId) hisId, h.stalId
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId) 
    WHERE actId = 15 and u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY h.stalId
 ) max_his_lam on (st.stalId = max_his_lam.stalId)
 join tblHistorie lh on (lh.hisId = max_his_lam.hisId)
 left join (
    SELECT schaapId, datum
    FROM tblStal st
     join tblHistorie h on (st.stalId = h.stalId)
    WHERE actId = 4
 ) spn on (spn.schaapId = l.schaapId)
 left join (
    SELECT st.stalId, max(datum) datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY st.stalId
 ) b_day_in on (b_day_in.stalId = st.stalId)
 left join (
    SELECT max(h.hisId) hisId, h.stalId, h.datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY h.stalId, h.datum
 ) b_in on (b_in.stalId = st.stalId and b_day_in.datum = b_in.datum)

 left join tblBezet b on (b_in.hisId = b.hisId)
 left join tblHok ho on (b.hokId = ho.hokId)
 left join (
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
 ) uit on (b_in.hisId = uit.hisId_in)
 left join tblHistorie ht on (uit.hisId_tot = ht.hisId)
 left join tblActie at on (at.actId = ht.actId)




 join (
    SELECT max(Id) Id, levensnummer
    FROM impAgrident
    WHERE actId = 15 and verwerkt = 1 and lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY levensnummer
 ) lst_adop on  (lst_adop.levensnummer = l.levensnummer)
 join impAgrident a on (lst_adop.Id = a.Id)
 left join tblSchaap amdr on (amdr.levensnummer = a.moeder)
 left join (
    SELECT max(st.stalId) stalId, schaapId
    FROM tblStal st
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY schaapId
 ) max_st_amdr on (max_st_amdr.schaapId = amdr.schaapId)
 left join tblStal ast on (max_st_amdr.stalId = ast.stalId)
 left join (
    SELECT max(h.hisId) hisId, h.stalId
    FROM tblHistorie h
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY h.stalId
 ) max_his_amdr on (max_his_amdr.stalId = ast.stalId)
 left join tblHistorie ah on (ah.hisId = max_his_amdr.hisId)
 left join tblActie aa on (aa.actId = ah.actId)
 
 left join (
    SELECT st.stalId, max(datum) datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY st.stalId
 ) b_day_in_adop on (b_day_in_adop.stalId = ast.stalId)
 left join (
    SELECT max(h.hisId) hisId, h.stalId, h.datum
    FROM tblHistorie h
     join tblBezet b on (h.hisId = b.hisId)
     join tblStal st on (st.stalId = h.stalId)
     join tblUbn u on (u.ubnId = st.ubnId)
    WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."'
    GROUP BY h.stalId, h.datum
 ) b_in_adop on (b_in_adop.stalId = ast.stalId and b_day_in_adop.datum = b_in_adop.datum)
 left join tblBezet b_adop on (b_in_adop.hisId = b_adop.hisId)
 left join tblHok ho_adop on (b_adop.hokId = ho_adop.hokId)
 left join (
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
 ) uit_adop on (b_in_adop.hisId = uit_adop.hisId_in)
 left join tblHistorie ht_adop on (uit_adop.hisId_tot = ht_adop.hisId)
 left join tblActie at_adop on (at_adop.actId = ht_adop.actId)


 left join tblVolwas v on (l.volwId = v.volwId)
 left join tblSchaap mdr on (mdr.schaapId = v.mdrId)

WHERE isnull(spn.schaapId)

") or die (mysqli_error($db));  ?>

<table Border = 0 id="sortableTable" align = "center">
  <thead>
     <tr class = "StickyHeader">
     <th onclick="sortTable(0)"> <br> Levensnummer <span id="arrow0" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren verblijf zonder link/url-->
     <th onclick="sortTable(1)"> <br> Werknr lam <span id="arrow1" class="inactive"></span> <hr></th>
     <th onclick="sortTable(2)"> <br> Verblijf lam <span id="arrow2" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(3)"> <span id="arrow4" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren adoptie datum o.b.v. jjjjmmdd -->  
     <th onclick="sortTable(3)"> <br> Datum adoptie <span id="arrow3" class="inactive"></span> <hr></th>
     <th onclick="sortTable(5)">  Werknr adoptiemoeder <span id="arrow5" class="inactive"></span> <hr></th> 
     <th onclick="sortTable(6)"> <br> Verblijf adoptiemoeder <span id="arrow6" class="inactive"></span> <hr></th>
     <th onclick="sortTable(6)"> <br> Status adoptiemoeder <span id="arrow6" class="inactive"></span> <hr></th>
     <th onclick="sortTable(7)"> Werknr bio- logische moeder <span id="arrow7" class="inactive"></span> <hr></th>

    </tr>
</thead>
<tbody>


<?php		while($row = mysqli_fetch_array($result))
		{ 
           $levnr = $row['levensnummer'];
           $werknr = $row['werknr']; 
           $actie_uit_lam = $row['actie_uit'];
           if(!isset($actie_uit_lam)) { $verblijf_lam = $row['hoknr']; }
           $adopDay_sort = $row['adopDay_sort'];
           $adopDag = $row['adopDag'];
           $adop_werknr = $row['adop_ooi_werknr'];
           $best_amdr = $row['best_amdr'];
           $last_actie_amdr = $row['last_actie_adop']; 
           $actie_uit_amdr = $row['actie_uit_adop']; 
           $verblijf_adop = $row['hoknr_adop']; if(!isset($actie_uit_amdr)) { $verblijf_amdr = $verblijf_adop; }

           if(isset($best_amdr)) { $status = $last_actie_amdr; }
           else if(isset($actie_uit_amdr)) { $status = 'Verblijf '.$actie_uit_amdr; }

           $bio_werknr = $row['bio_ooi_werknr']; ?>

    <tr align = "center">
     <td ><?php echo $levnr; ?></td>
     <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?></td>
     <td ><?php echo $verblijf_lam; ?></td>
     <td style="display:none;" ><?php echo $adopDay_sort; ?></td> <!-- Deze cel is t.b.v. sorteren adoptiedatum o.b.v. jjjjmmdd -->  
     <td width = 100 style = "font-size:15px;"><?php echo $adopDag; ?></td>
     <td width = 100 style = "font-size:15px;"><?php echo $adop_werknr; ?></td>
     <td style = "font-size:15px;"><?php echo $verblijf_amdr; ?></td>
     <td style = "font-size:15px;"><?php echo $status; ?></td>
     <td width = 100 style = "font-size:15px;"><?php echo $bio_werknr; ?></td>
    </tr>		
		
<?php	
unset($verblijf_amdr);
unset($status);
unset($actie_uit_lam);
unset($verblijf_lam);
	} ?>

	</tbody>	
</table>

		</TD>
<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
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