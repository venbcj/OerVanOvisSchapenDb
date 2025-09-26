<!-- tabel zoeken en filter BRON : https://www.youtube.com/watch?v=iwflcCfxeBc
     tabel sorteren BRON : https://www.youtube.com/watch?v=WbkPGesI-OY -->

<!DOCTYPE html>
<html>
  
<head>
<title>        
</title>

    <link rel="stylesheet"  href="test4_sorteren_zoeken.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css"> <!-- is t.b.v. icoon in zoekveld -->

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

    <main class="table">
        <section class="table_header">
            <h1>Customer's Orders</h1>
            <div class="inner-addon left-addon"> <!-- zorgt dat icoon in het zoekveld staat i.p.v. er naast -->
                <div class="input-group"><i class="glyphicon glyphicon-search"><!-- icoon bij zoekveld--></i>
                <input type="search" placeholder="Zoek hier .....">
             <!--   <img src="images/search.png" alt=""> -->
            </div>
            </div>
        </section>        
        <section class="table_body">
            <table>
                <thead>
                    <tr>
                        <th> Id <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Customer <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Location <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Order Date <span class="icon-arrow">&UpArrow;</span> </th>
                        <th> Amount <span class="icon-arrow">&UpArrow;</span> </th>
                    </tr>
                </thead>
                <tbody>
                    <tr> <td>1</td><td></td> <td></td> <td></td> </tr>
                    <tr> <td>1</td><td></td> <td></td> <td></td> </tr>
                    <tr> <td>1</td><td></td> <td></td> <td></td> </tr>
                    <tr> <td>1</td><td>Zinzu Chan Lee</td> <td>Seoul</td> <td>17 Dec, 2022</td> </tr>
                    <tr> <td>2</td><td>Jeet Saru</td> <td>Kathmandu</td> <td>27 Aug, 2023</td> </tr>
                    <tr> <td>3</td><td>Sonal Gharti</td> <td>Tokyo</td> <td>14 Mar, 2023</td> </tr>
                    <tr> <td>4</td><td>Alson GC</td> <td>New Delhi</td> <td>25 May, 2023</td> </tr>
                    <tr> <td>5</td><td>Sarita Limbu</td> <td>Paris</td> <td>23 Apr, 2023</td> </tr>
                    <tr> <td>6</td><td>Alex Gonley</td> <td>London</td> <td>23 Apr, 2023</td> </tr>

                   
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
        </section>
    </main>

    <script src="test4_sorteren_zoeken.js"></script>
</body>

</html>