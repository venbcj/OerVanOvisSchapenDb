<?php
$versie = "01-04-2026"; /* 01-04-2026 :gekopieerd van ResultHok.php */

require_once("autoload.php");

 Session::start();
  ?>
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
if (Auth::is_logged_in()) { if($modtech ==1) { ?>

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
     <th onclick="sortTable(7)"> <br> Status adoptiemoeder <span id="arrow7" class="inactive"></span> <hr></th>
     <th onclick="sortTable(8)"> Werknr bio- logische moeder <span id="arrow8" class="inactive"></span> <hr></th>

    </tr>
</thead>
<tbody>


<?php	$result = getAdoptieLammeren($lidId); 
    	while($row = mysqli_fetch_array($result))
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