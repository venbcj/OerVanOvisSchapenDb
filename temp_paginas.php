<?php
// Voorbeeldarray met dummygegevens
$data = [
    "Item 1", "Item 2", "Item 3", "Item 4", "Item 5",
    "Item 6", "Item 7", "Item 8", "Item 9", "Item 10",
    "Item 11", "Item 12", "Item 13", "Item 14", "Item 15"
];

$dir = dirname(__FILE__).'/user_1/';
$data = scandir($dir);
//$data = scandir($dir,1);

// Pagina instellingen
$itemsPerPage = 10;
$totalItems = count($data);
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
$itemsToShow = array_slice($data, $startIndex, $itemsPerPage);

// Toon de items
echo "<h2>Pagina $currentPage van $totalPages</h2>";

foreach ($itemsToShow as $filename) {
  $bestandsnaam = substr($filename,0,6).' '.substr($filename,15,2).'-'.substr($filename,12,2).'-'.substr($filename,7,4).' '.substr($filename,18,2).':'.substr($filename,21,2).':'.substr($filename,24,2).'u';


if(substr($filename,0,6) == 'reader') { ?>

<a href='<?php echo $url; ?>download.php?file=<?php echo $filename; ?>' > 

<?php
    echo "$bestandsnaam<br>";
}
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