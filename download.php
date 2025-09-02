<?php
/*29-05-2025 gemaakt om readerbestanden te downloaden. Zie Readerbestanden.php */

$pstFile = $_GET['file'];
$lidId = $_GET['id'];

//if(isset($pstFile)) { 
//echo '$pstFile = '.$pstFile.'<br>';
// Bestandsnaam (relatief pad of absoluut pad)
$dir = dirname(__FILE__).'/user_'.$lidId.'/Readerbestanden/';
$bestand = $dir.$pstFile;

// Check of het bestand bestaat
if (file_exists($bestand)) {

//echo $bestand.'<br>'.'<br>';

    // Stuur headers om de download te forceren
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Transfer-Encoding: Binary');
    header('Content-Disposition: attachment; filename="' . basename($bestand) . '"');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($bestand));

    // Stuur het bestand naar de browser
    readfile($bestand);
    exit;
} else {
    echo $bestand. "Het bestand bestaat niet.";
}

//}
?>