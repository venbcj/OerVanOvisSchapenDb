<?php /* 9-8-2014 : werknr variabel gemaakt zie $Karwerk en quotes bij "$datum" en "$aantal" weggehaald 
1-3-2015 : login toegevoegd 
19-12-2015 : Uitval toegevoegd */
$versie = '08-01-2017'; /* LidId = 1 variabel gemaankt naar lidId = ".mysqli_real_escape_string($db,$lidId)." */
$versie = '28-9-2018'; /* titel.php verwijderd. Zit in header.php samen met Style.css */
$versie = '05-07-2020'; /* wdgn gewijzigd in wdgn_v */
$versie = '30-12-2023'; /* and h.skip = 0 toegevoegd bij tblHistorie en sql beveiligd met quotes */
$versie = '07-07-2024'; /* Werknr oplopend gesorteerd */
$versie = '31-12-2024'; /* <TD width = 960 height = 400 valign = "top" > gewijzigd naar <TD align = "center" valign = "top"> 31-12-24 Include "login.php"; voor Include "header.php" gezet */
$versie = '19-03-2025'; /* Gewicht toegevoegd en exporteren naar excel mogelijk gemaakt */
 session_start();  ?>
<!DOCTYPE html>
<html>
<head>
<title>Afleverlijst</title>
</head>
<body>

<?php
$titel = 'Afleverlijst';
$file = "ZoekAfldm.php";
Include "login.php";
?>
        <TD align = "center" valign = "top">
<?php 
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) {

//Include "vw_Voeding.php";

$hisId = $_POST['kzlPost']; // kzlPost bestaat in ZoekAfldm.php 

$zoek_datum_bestemmming = mysqli_query($db,"
SELECT datum, rel_best
FROM tblHistorie h
 join tblStal st on (st.stalId = h.stalId)
WHERE h.hisId = '".mysqli_real_escape_string($db,$hisId)."'
") or die (mysqli_error($db));
    while( $da_be = mysqli_fetch_assoc($zoek_datum_bestemmming)) { $date = $da_be['datum']; $bestm = $da_be['rel_best']; }



/*Telt aantal schapen per bestemming/afleverdatum*/
$zoek_aflevergegevens = mysqli_query($db,"
SELECT p.naam, date_format(h.datum,'%d-%m-%Y') datum, count(st.schaapId) tal
FROM tblStal st
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
 join tblRelatie r on (st.rel_best = r.relId)
 join tblPartij p on (p.partId = r.partId)
WHERE a.af = 1 and st.rel_best = '".mysqli_real_escape_string($db,$bestm)."' and h.datum = '".mysqli_real_escape_string($db,$date)."' and h.skip = 0
GROUP BY p.naam, date_format(h.datum,'%d-%m-%Y')
") or die (mysqli_error($db));
    while ($za = mysqli_fetch_array($zoek_aflevergegevens))
    {
$bestemming = $za['naam'];
$datum =  $za['datum'];
$aantal = $za['tal'];
    } ?>
<table border = 0 >

    
<tr >
<td > </td>

        <td  >  
<tr >
 <td></td> 
 <td colspan = 10 align =center>
     <a href= '<?php echo $url;?>AfleverLijst_pdf.php?hisId=<?php echo $hisId; ?>' style = 'color : blue'>
    print pagina </a>
 </td> 
 <td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Bestemming :</i></td> 
 <td colspan = 4><b style = \"font-size:15px;\"><?php echo $bestemming; ?> </b></td>
</tr>

<tr >
<td></td> 
<td colspan = 10></td> 
<td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Afleverdatum :</i></td> 
<td colspan = 2><b style = \"font-size:15px;\"><?php echo $datum; ?> </b></td>
</tr>

<tr >
<td></td> 
<td colspan = 10></td> 
<td colspan = 2 align = \"left\"><i style = \"font-size:14px;\"> Aantal schapen :</i></td> 
<td colspan = 2><b style = \"font-size:15px;\"><?php echo $aantal; ?> </b></td>
</tr>
<?php
?>
<tr style = \"font-size:12px;\">
<th width = 0 height = 30></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Levensnummer<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Werknummer<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Gewicht<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 90>Medicijn<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 120>Datum toepassing<hr></th>
<th width = 1></th>
<th style = \"text-align:center;\" valign = bottom width= 100>Wachtdagen<hr></th>
<th width = 80 ></th>
<td colspan = 2 ><a href="exportAfleverlijst.php?pst=<?php echo $lidId; ?>&best=<?php echo $bestm; ?>&date=<?php echo $date; ?>"> Export-xlsx </a></td>

<?php
$zoek_schaap = mysqli_query($db,"
SELECT s.schaapId, s.levensnummer, right(s.levensnummer,$Karwerk) werknr, h.kg 
FROM tblSchaap s
 join tblStal st on (s.schaapId = st.schaapId)
 join tblHistorie h on (st.stalId = h.stalId)
 join tblActie a on (h.actId = a.actId)
WHERE a.af = 1 and st.rel_best = '".mysqli_real_escape_string($db,$bestm)."' and h.datum = '".mysqli_real_escape_string($db,$date)."' and h.skip = 0
ORDER BY right(s.levensnummer,$Karwerk)
") or die (mysqli_error($db));
        while($zs = mysqli_fetch_assoc($zoek_schaap))
        {   $levnr = $zs['levensnummer'];   if(!isset($levnr)) { $levnr = 'Geen'; } 
            $werknr = $zs['werknr'];
            $schaapId = $zs['schaapId'];
            $kg = $zs['kg']; ?>
<tr align = center>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $levnr; ?> <br> </td>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $werknr; ?> <br> </td>
    <td width = 0 > </td>
    <td width = 100 style = "font-size:15px;"> <?php echo $kg; ?> <br> </td>

        <td colspan = 6><table border = 0>

<?php
$zoek_pil = mysqli_query($db,"
SELECT date_format(h.datum,'%d-%m-%Y') datum, art.naam, art.wdgn_v, (h.datum + interval art.wdgn_v day) toon
FROM tblSchaap s 
 join tblStal st on (st.schaapId = s.schaapId)
 join tblUbn u on (st.ubnId = u.ubnId)
 join tblHistorie h on (h.stalId = st.stalId)
 join tblActie a on (a.actId = h.actId)
 left join tblNuttig n on (h.hisId = n.hisId)
 left join tblInkoop i on (i.inkId = n.inkId)
 left join tblArtikel art on (i.artId = art.artId) 
WHERE u.lidId = '".mysqli_real_escape_string($db,$lidId)."' and s.schaapId = '".mysqli_real_escape_string($db,$schaapId)."' and h.actId = 8 and h.skip = 0 and (h.datum + interval art.wdgn_v day) >= sysdate()
") or die (mysqli_error($db));  

$vandaag = date('Y-m-d');
        while($row = mysqli_fetch_array($zoek_pil))
        {
If (!empty($row['datum']))      { ?>
<tr align = center>
 <td width = 0> </td>
 <td width = 100 style = "font-size:15px;" align = "left"> <?php echo $row['naam']; ?> <br> </td>
 <td width = 1> </td>           
 <td width = 120 style = "font-size:15px;"> <?php echo $row['datum']; ?> <br> </td>
 <td width = 1> </td>
 <td width = 100 style = "font-size:15px;"> <?php   echo $row['wdgn_v']; ?> <br> </td> 
 <td width = 1> </td>      
</tr>           <?php   }

        } ?>
    </table></td> <?php } ?>
</tr>               
</table>


        </TD>
<?php
Include "menuRapport.php"; } ?>
</body>
</html>
