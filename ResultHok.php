<?php

require_once("autoload.php");

/* 2-3-2015 : Login toegevoegd 
6-1-2016 : Hoknr gewijzigd aar Verblijf */
$versie = "22-1-2017"; /* 19-1-2017 Query's aangepast n.a.v. nieuwe tblDoel        22-1-2017 tblBezetting gewijzigd naar tblBezet*/
/*Wat als voer wordt ingekocht zonder rubriek aan het voer !!?? */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '20-12-2019'; /* tabelnaam gewijzigd van UIT naar uit tabelnaam */
$versie = '31-12-2023'; /* and h.skip = 0 aangevuld aan tblHistorie en sql beveiligd met quotes */
$versie = "11-03-2024"; /* Bij geneste query uit 
join tblHistorie h2 on (h1.stalId = h2.stalId and h1.hisId < h2.hisId) gewijzgd naar
join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
I.v.m. historie van stalId 22623. Dit dier is eerst verkocht en met terugwerkende kracht geplaatst in verblijf Afmest 1 */
$versie = '26-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD valign = 'top'> 31-12-24 include "login.php"; voor include "header.tpl.php" gezet */
$versie = '27-01-2025'; /* Sortering toegepast en vastzetten kolomkop. De gegevens klopte niet. Queries daarom aangepast. */

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
$titel = 'Periode resultaten';
$file = "ResultHok.php";
include "login.php"; ?>

                <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) { ?>

<script src="sorteren.js"></script>

    <?php
/* Binnen subquery hokIn zit een union t.b.v. doelId 3. In die Where cluse is h.datum >= prnt.datum toegepast i.p.v. (h.datum >= prnt.datum or ht.datum > prnt.datum) Schapen die in een verblijf een aanwasdatum krijgen worden niet meegeteld als doelgroep 3 zijnde 'Stallijst'. Deze vallen dus enkel in de doelgroep gespeend */

$result = mysqli_query($db,"
SELECT result.periId, h.hokId, h.hoknr, result.doelId, d.doel,
    min(result.dm_in) dmeerste_in, date_format(min(result.dm_in),'%Y%m%d') eertse_in_sort, date_format(min(result.dm_in),'%d-%m-%Y') eertse_in, 
        count(distinct result.schaapId) aant, 
        result.van dm_start_periode, date_format(result.van,'%Y%m%d') start_periode_sort, date_format(result.van,'%d-%m-%Y') start_periode,
        date_format(result.tot,'%Y%m%d') eind_periode_sort, date_format(result.tot,'%d-%m-%Y') eind_periode
FROM tblHok h
 join (
    SELECT hokIn.schaapId, hokIn.hokId, hokIn.doelId, hokIn.dm_in, periodes.periId, periodes.van, periodes.tot
    FROM (
        SELECT st.schaapId, b.hokId, 1 doelId, h.datum dm_in, ht.datum dm_uit
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
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and ( (isnull(spn.datum) and isnull(prnt.datum)) or h.datum < spn.datum)

        UNION

        SELECT st.schaapId, b.hokId, 2 doelId, h.datum dm_in, ht.datum dm_uit
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
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and ((h.datum >= spn.datum and (isnull(prnt.datum) or h.datum < prnt.datum)) or (isnull(spn.datum) and h.datum < prnt.datum))

        UNION

        SELECT st.schaapId, b.hokId, 3 doelId, h.datum dm_in, ht.datum dm_uit
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
         join (
            SELECT schaapId, datum
            FROM tblHistorie h
             join tblStal st on (st.stalId = h.stalId)
            WHERE actId = 3 and skip = 0
         ) prnt on (st.schaapId = prnt.schaapId)
        WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and h.skip = 0 and h.datum >= prnt.datum
    ) hokIn
     join (
        SELECT p2.hokId, p2.doelId, p.periId, '2000-01-01' van, p2.dmafsluit tot
        FROM  tblPeriode p
         join (
            SELECT p.hokId, p.doelId, min(p.dmafsluit) dmafsluit
            FROM tblPeriode p
             join tblHok h on (h.hokId = p.hokId)
            WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."'
            GROUP BY p.hokId, p.doelId
         ) p2 on (p.hokId = p2.hokId and p.doelId = p2.doelId and p.dmafsluit = p2.dmafsluit)

        UNION

        SELECT p1.hokId, p1.doelId, p2.periId, p1.dmafsluit dmafsluit1, p2.dmafsluit dmafsluit2
        FROM (
            SELECT p1.periId periId1, min(p2.periId) periId2
            FROM tblPeriode p1
             join tblHok h on (h.hokId = p1.hokId)
             join tblPeriode p2 on (p1.hokId = p2.hokId and p1.doelId = p2.doelId and p1.dmafsluit < p2.dmafsluit)
            WHERE h.lidId = '".mysqli_real_escape_string($db,$lidId)."'
            GROUP BY p1.periId
         ) a
         join tblPeriode p1 on (a.periId1 = p1.periId)
         join tblPeriode p2 on (a.periId2 = p2.periId)
     ) periodes on (hokIn.hokId = periodes.hokId and hokIn.doelId = periodes.doelId)
    WHERE (hokIn.dm_in  < periodes.tot and (isnull(hokIn.dm_uit) or hokIn.dm_uit > periodes.van))
) result on (result.hokId = h.hokId)
 join tblDoel d on (d.doelId = result.doelId)
GROUP BY result.periId, result.hokId, h.hoknr, result.doelId, d.doel, result.van, result.tot
ORDER BY result.hokId, result.doelId, result.van
") or die (mysqli_error($db));  ?>

<table Border = 0 id="sortableTable" align = "center">
  <thead>
    <tr class = "StickyHeader">
     <th style="display:none;" onclick="sortTable(0)"> <span id="arrow1" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren verblijf zonder link/url-->
     <th onclick="sortTable(0)"> <br> Verblijf <span id="arrow0" class="inactive"></span> <hr></th>
     <th onclick="sortTable(2)"> <br> Doelgroep <span id="arrow2" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(3)"> <span id="arrow4" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren afsluitdatum o.b.v. jjjjmmdd -->  
     <th onclick="sortTable(3)"> <br> Start periode <span id="arrow3" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(5)"> <span id="arrow6" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren afsluitdatum o.b.v. jjjjmmdd -->  
     <th onclick="sortTable(5)"> <br> Afsluitdatum <span id="arrow5" class="inactive"></span> <hr></th>
     <th style="display:none;" onclick="sortTable(7)"> <span id="arrow8" class="inactive"></span> <hr></th> <!-- Deze cel is t.b.v. sorteren $maxBezet -->
     <th onclick="sortTable(7)"> <br> Max. Bezetting <span id="arrow7" class="inactive"></span> <hr></th>
    </tr>
</thead>
<tbody>


<?php        while($row = mysqli_fetch_array($result))
        { 
           $hoknr = $row['hoknr'];
           $doelgr = $row['doel']; 
           $dm1_in = $row['dmeerste_in'];
           $indm1_sort = $row['eertse_in_sort'];
           $indm1 = $row['eertse_in'];
           $periId = $row['periId'];
           $dm_start_periode = $row['dm_start_periode']; if($dm1_in < $dm_start_periode) { 
                $indm1_sort = $row['start_periode_sort'];
                $indm1 = $row['start_periode'];
                }
           $dmSort = $row['eind_periode_sort'];
           $afsldm = $row['eind_periode'];
           $maxBezet = $row['aant']; $n = 10-strlen($maxBezet);

      $sort_maxBezet = '';
      for ($i = 0; $i<$n; $i++) { $sort_maxBezet .= '0'; }
           $sort_maxBezet .= $maxBezet; // echo '$sort_maxBezet = '.$sort_maxBezet.'<br>';

            ?>

    <tr align = "center">
     <td style="display:none;" ><?php echo $hoknr; ?></td> <!-- Deze cel is t.b.v. sorteren verblijf zonder link/url-->
     <td width = 100 style = "font-size:15px;"><a href='<?php echo $url; ?>ResultSchaap.php?pstId=<?php echo $periId; ?>' style = "color : blue"> <?php echo $hoknr; ?> </a></td>
     <td width = 80 style = "font-size:15px;"><?php echo $doelgr; ?></td>
     <td style="display:none;" ><?php echo $indm1_sort; ?></td> <!-- Deze cel is t.b.v. sorteren afsluitdatum o.b.v. jjjjmmdd -->  
     <td width = 100 style = "font-size:15px;"><?php echo $indm1; ?></td>
     <td style="display:none;" ><?php echo $dmSort; ?></td> <!-- Deze cel is t.b.v. sorteren afsluitdatum o.b.v. jjjjmmdd -->  
     <td width = 100 style = "font-size:15px;"><?php echo $afsldm; ?></td>
     <td style="display:none;"><?php echo $sort_maxBezet; ?></td> <!-- Deze cel is t.b.v. sorteren $maxBezet -->
     <td width = 100 style = "font-size:15px;"><?php echo $maxBezet; ?></td>
     <td style="display:none;"></td> <!-- Deze cel is t.b.v. sorteren $Schaapdgn -->
    </tr>        
        
<?php        } ?>

    </tbody>    
</table>

        </TD>
<?php } else { ?> <img src='resultHok_php.jpg'  width='970' height='550'/> <?php }
include "menuRapport.php"; } ?>

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
