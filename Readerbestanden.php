<?php 
$versie = '29-05-2025'; /* Gekopieerd van Readerversies.php */

 session_start(); ?>
<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
<title>Sjabloon</title>
</head>
<body>

<?php
$titel = 'Ingelezen readerbestanden';
$file = "Readerbestanden.php";
Include "login.php"; ?>

		<TD valign = 'top'>
<?php
if (isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"])) { ?>

<form action="Readerbestanden.php" method = "post">
<?php
$dir = dirname(__FILE__).'/user_'.$lidId.'/Readerbestanden/';
//echo $dir;
// Sort in ascending order - this is default
//$a = scandir($dir);

// Sort in descending order
$b = scandir($dir,1);

/*print_r($a);
print_r($b);*/
foreach ($b as $bestandsnaam) {

if (substr($bestandsnaam,7,4) >= $vorigjaar) { // $vorigjaar is gedeclareerd in basisfuncties.php
    $array[] = $bestandsnaam;
}

}

// Pagina instellingen
$itemsPerPage = 10;
$totalItems = count($array);
$totalPages = ceil($totalItems / $itemsPerPage);

// Bepaal de huidige pagina
$currentPage = isset($_GET['page']) ? (int)$_GET['page'] : 1;

// Corrigeer foutieve paginawaarden
if ($currentPage < 1) {
    $currentPage = 1;
} elseif ($currentPage > $totalPages) {
    $currentPage = $totalPages;
}

// Bepaal de offset
$startIndex = ($currentPage - 1) * $itemsPerPage;

// Haal de items voor de huidige pagina
$itemsToShow = array_slice($array, $startIndex, $itemsPerPage); ?>
<table border="0">
<tr>
 <td valign="top">

<?php
// Toon de items
echo "<h4>Pagina $currentPage van $totalPages</h4>";

foreach ($itemsToShow as $filename) {
  $bestandsnaam = substr($filename,0,6).' '.substr($filename,15,2).'-'.substr($filename,12,2).'-'.substr($filename,7,4).' '.substr($filename,18,2).':'.substr($filename,21,2).':'.substr($filename,24,2).'u';
?>

<a href='<?php echo $url; ?>download.php?file=<?php echo $filename; ?>&id=<?php echo $lidId; ?>' >

<?php
    echo "$bestandsnaam<br>";
}

// Toon navigatie
echo "<div style='margin-top: 20px;'>";
if ($currentPage > 1) {
    $prevPage = $currentPage - 1;
    echo "<a href='?page=$prevPage'>&laquo; Vorige</a> ";
}

for ($i = 1; $i <= $totalPages; $i++) {
    if ($i === $currentPage) {
        echo "<strong>$i</strong> ";
    } else {
        echo "<a href='?page=$i'>$i</a> ";
    }
}

if ($currentPage < $totalPages) {
    $nextPage = $currentPage + 1;
    echo "<a href='?page=$nextPage'>Volgende &raquo;</a>";
}
echo "</div>";

?>

 </td>
 <td width="150">
 </td>
 <td>
    <h4>Codering van acties</h4>
<?php

$zoek_acties = mysqli_query($db,"
SELECT actId, actie
FROM tblActie
") or die (mysqli_error($db));

while ($za = mysqli_fetch_array($zoek_acties)) {
    $actId = $za['actId'];
    $actie = $za['actie'];

echo $actId.' - '.$actie.'<br>';
}
?>
 </td>
</tr>
</table>
</form>

</TD>
<?php
Include "menuBeheer.php"; } ?>
</tr>

</table>

</body>
</html>
