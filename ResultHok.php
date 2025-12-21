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
$titel = 'Periode resultaten';
$file = "ResultHok.php";
include "login.php"; ?>

                <TD valign = 'top'>
<?php
if (Auth::is_logged_in()) { if($modtech ==1) {
// TODO: #0004104 (BV) sorteren.js zit niet in de repo!
?>
<script src="sorteren.js"></script>
<?php

    $result = $hok_gateway->resultaten($lidId);
?>

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
<?php
while ($row = $result->fetch_array())
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
include "menuRapport.php"; }

include "sort-1-table.js.php";

?>


</body>
</html>
