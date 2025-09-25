<!-- tabel sorteren BRON : https://www.youtube.com/watch?v=av5wFcAtuEI -->

<!DOCTYPE html>
<html>
  
<head>
<title> Sorteren tabel</title>
<style type="text/css">

th {
    cursor: pointer;
    background-color: rgb(207, 207, 207);
}

.desc:after {
    content: ' ▼'; /*Alt 31*/
}

.asc:after {
    content: ' ▲'; /*Alt 30*/
}

.inactive:after {
    content: ' ▲';
    color: gray;
    opacity: 0.5;
}
h1 {
    text-align: center;
}
</style>
</head>

<body>
<?php include "connect_db.php";
//include "header.php"; 

$lidId = 22;

$toon_aanwezigen = mysqli_query($db,"
SELECT s.levensnummer, right(s.levensnummer, 5) werknum, s.transponder, date_format(hg.datum,'%d-%m-%Y') gebdm, s.geslacht, prnt.datum aanw, scan.dag, haf.actId
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
 left join (
    SELECT h.stalId, h.actId
    FROM tblHistorie h
     join tblStal st on (h.stalId = st.stalId)
     join tblActie a on (h.actId = a.actId)
    WHERE a.af = 1
 ) haf on (haf.stalId = st.stalId)
WHERE st.lidId = '".mysqli_real_escape_string($db,$lidId)."' and isnull(haf.actId)
ORDER BY right(s.levensnummer, 5)
") or die (mysqli_error($db));  ?>

   
<h1>Sortable HTML Table</h1>
<table id="sortableTable">
  <thead>
    <tr>
        <th onclick="sortTable(0)"> Name <span id="arrow0" class="inactive"></span> </th>
        <th onclick="sortTable(1)"> Age <span id="arrow1" class="inactive"></span> </th>
        <th onclick="sortTable(2)"> Country <span id="arrow2" class="inactive"></span> </th>
        <th onclick="sortTable(3)"> Datum <span id="arrow3" class="inactive"></span> </th>
    </tr>
    </thead>
    <tbody>
        <tr> <td>John Doe</td><td>25</td> <td>USA</td> <td>02-03-1998</td> </tr>
        <tr> <td>Jane Doe</td><td>30</td> <td>Canada</td> <td>15-07-2003</td> </tr>
        <tr> <td>Bob Smith</td><td>22</td> <td>UK</td> <td>24-08-2005</td> </tr>
        <tr> <td>Bas van de Ven</td><td>51</td> <td>Drunen</td> <td>03-03-2023</td> </tr>
        <tr> <td>Guus Meeuwis</td><td>54</td> <td>Tilburg</td> <td>05-11-2015</td> </tr>
        <tr> <td>Luuk de Jong</td><td>37</td> <td>Eindhoven</td> <td>11-09-2023</td> </tr>
        <tr> <td>Peter Bos</td><td>51</td> <td>Eindhoven</td> <td>08-05-2020</td> </tr>

                   
 <?php while($ta = mysqli_fetch_array($toon_aanwezigen))
    {
    $transponder = $ta['transponder']; if(isset($transponder)) {$transp = 'Ja'; } else {$transp = 'Nee'; }
    $werknr = $ta['werknum'];
    $levnr = $ta['levensnummer'];
    $gebdm = $ta['gebdm'];
    $geslacht = $ta['geslacht']; 
    $aanw = $ta['aanw']; 
    $lstScan = $ta['dag']; 
    $actId_af = $ta['actId']; 
    if(isset($aanw)) {if($geslacht == 'ooi') { $fase = 'moeder'; } else if($geslacht == 'ram') { $fase = 'vader'; } } else {$fase = 'lam'; }    ?>


<tr> <td><?php echo $werknr; ?></td><td><?php echo $levnr; ?></td> <td><?php echo $fase; ?></td> <td><?php echo $gebdm; ?> </td> </tr>

            
        
    <?php } ?>                  
    </tbody>
</table>
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