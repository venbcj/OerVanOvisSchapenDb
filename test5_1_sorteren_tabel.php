<!-- tabel sorteren BRON : https://www.youtube.com/watch?v=av5wFcAtuEI -->
<?php session_start(); ?>
<!DOCTYPE html>
<html>
  
<head>
<title> Sorteren tabel</title>
<style type="text/css">
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

$periId = 353; //$_GET['pstId'];

$periode = mysqli_query($db,"
SELECT ho.hoknr, d.doel, date_format(min(h.datum),'%d-%m-%Y') van, date_format(max(ht.datum),'%d-%m-%Y') tot, date_format(p.dmafsluit,'%d-%m-%Y') afsluitdm
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 join tblDoel d on (p.doelId = d.doelId)
 join tblBezet b on (b.hokId = ho.hokId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 left join 
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
WHERE h.skip = 0 and p.periId = '".mysqli_real_escape_string($db,$periId)."'
GROUP BY ho.hoknr, d.doel, dmafsluit, p.dmafsluit
") or die (mysqli_error($db));
    while($rij = mysqli_fetch_assoc($periode))
    {$dag = date_create($rij['van']);
        $van = date_format($dag, 'd-m-Y');
     $tot = $rij['tot'];
     $hok = $rij['hoknr'];
     $groep = $rij['doel'];
     $afsldm = $rij['afsluitdm'];
    } ?>

<table Border = 0>
<tr>
 <td colspan = 3 align = "right" style = "font-size:20px;"><b> <?php echo $hok; ?> </b></td> 
 <td colspan = 3 ><i style = "font-size:12px;"> &nbsp &nbsp Doelgroep : </i><b style = "font-size:13px;"> <?php echo $groep; ?> </b></td> 
 <td colspan = 7 ><i style = "font-size:12px"> &nbsp &nbsp Periode : </i><b style = "font-size:13px;"><?php Echo $van." - ".$afsldm;?></b></td>
</tr>
</table>

<?php
$result = mysqli_query($db,"
SELECT right(s.levensnummer,$Karwerk) werknr, r.ras, s.geslacht, date_format(h.datum,'%d-%m-%Y') indm, date_format(ht.datum,'%d-%m-%Y') uitdm, datediff(ht.datum, h.datum) schpdgn, h.kg kgin, ht.kg kguit, round((ht.kg-h.kg)/datediff(ht.datum, h.datum)*1000,2) gemgroei, date_format(hdo.datum,'%d-%m-%Y') uitvdm, a.actie status
FROM tblHok ho
 join tblPeriode p on (p.hokId = ho.hokId)
 join tblBezet b on (b.hokId = ho.hokId)
 join tblHistorie h on (h.hisId = b.hisId)
 join tblStal st on (st.stalId = h.stalId)
 join tblSchaap s on (s.schaapId = st.schaapId)
 join tblRas r on (r.rasId = s.rasId)
 left join 
 (
    SELECT b.bezId, st.schaapId, h1.hisId hisv, min(h2.hisId) hist
    FROM tblBezet b
     join tblHistorie h1 on (b.hisId = h1.hisId)
     join tblActie a1 on (a1.actId = h1.actId)
     join tblHistorie h2 on (h1.stalId = h2.stalId and ((h1.datum < h2.datum) or (h1.datum = h2.datum and h1.hisId < h2.hisId)) )
     join tblActie a2 on (a2.actId = h2.actId)
     join tblStal st on (h1.stalId = st.stalId)
    WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and a1.aan = 1 and a2.uit = 1 and h1.skip = 0 and h2.skip = 0
    GROUP BY b.bezId, st.schaapId, h1.hisId
 ) uit on (uit.bezId = b.bezId)
 left join tblHistorie ht on (ht.hisId = uit.hist)
 left join tblHistorie hdo on (hdo.hisId = uit.hist and hdo.actId = 14)
 left join tblActie a on (a.actId = ht.actId)
WHERE h.skip = 0 and p.periId = '".mysqli_real_escape_string($db,$periId)."'
ORDER BY right(s.levensnummer,$Karwerk), h.datum
") or die (mysqli_error($db));
?>

<table id="sortableTable">
  <thead>
    <tr>
 <th onclick="sortTable(0)" width= 80>Werknr <span id="arrow0" class="inactive"></span><hr></th>
 <th onclick="sortTable(1)" width= 50>Ras <span id="arrow1" class="inactive"></span><hr></th>
 <th onclick="sortTable(2)" width= 50>Geslacht <span id="arrow2" class="inactive"></span><hr></th>
 <th onclick="sortTable(3)" width= 200>Datum erin <span id="arrow3" class="inactive"></span><hr></th>
 <th onclick="sortTable(4)" width= 200>Datum eruit <span id="arrow4" class="inactive"></span><hr></th>
 <th onclick="sortTable(5)" width= 60>Schaap-dagen <span id="arrow5" class="inactive"></span><hr></th>
 <th onclick="sortTable(6)" width= 80>Begin gewicht <span id="arrow6" class="inactive"></span><hr></th>
 <th onclick="sortTable(7)" width= 80>Eind gewicht <span id="arrow7" class="inactive"></span><hr></th>
 <th onclick="sortTable(8)" width= 60>Gem groei <span id="arrow8" class="inactive"></span><hr></th>
 <th onclick="sortTable(9)" width = 60> <span id="arrow9" class="inactive"></span></th>
 <th onclick="sortTable(10)" width= 80>Reden uit verblijf <span id="arrow10" class="inactive"></span><hr></th>
    </tr>
    </thead>
    <tbody>
<?php
        while($row = mysqli_fetch_array($result))
        {   
            $werknr = $row['werknr'];
            $ras = $row['ras'];
            $geslacht = $row['geslacht'];
            $indm = $row['indm'];
            $uitdm = $row['uitdm'];
            $uitvdm = $row['uitvdm'];
            $schpdgn = $row['schpdgn'];
            $kgin = $row['kgin'];
            $kguit = $row['kguit'];
            $gemgroei = $row['gemgroei'];

            if($groep == 'Geboren' && $row['status'] == 'Eruit') { $status = 'Gespeend'; } 
           else if($groep == 'Gespeend' && $row['status'] == 'Eruit') { $status = 'Afgeleverd'; }
           else { $status = $row['status']; } ?>
        
<tr align = "center">
 <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $ras; ?> <br> </td>
 <td width = 100 style = "font-size:15px;"> <?php echo $geslacht; ?> <br> </td> 
 <td width = 200 style = "font-size:15px;"> <?php echo $indm ?> <br> </td>
 <?php     If (empty($uitdm))
{ ?>
 <td width = 200 style = "font-size:15px;"> <?php echo $uitvdm; ?> <br> </td>
<?php }
else    
{ ?>
 <td width = 200 style = "font-size:15px;"> <?php echo $uitdm; ?> <br> </td>
<?php } ?>
 <td width = 100 style = "font-size:15px;"> <?php echo $schpdgn; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $kgin; ?> <br> </td>
 <td width = 80 style = "font-size:15px;"> <?php echo $kguit; ?> <br> </td>
 <td width = 60 style = "font-size:15px;"> <?php echo $gemgroei; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php if(isset($status)) { echo $status; } else {echo "Onbekend"; } ?> <br> </td>

</tr>               
        
<?php       } ?>
                
    </tbody>
</table>

        </TD>
<?php
Include "menuRapport.php"; } ?>

<script type="text/javascript">
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
</script>
</body>

</html>