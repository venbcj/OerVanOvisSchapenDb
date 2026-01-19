<?php

$allowed = array('txt', 'TXT');
$maxsize = 51000; // bites !!
$now = DateTime::createFromFormat('U.u', microtime(true));
$Backup = $now->format("Y-m-d_H:i:s.u");
$Backupnaam = $end_dir_reader . 'reader_' . $Backup . '.txt';
//echo $end_dir_reader ;
$end_dir_exists = file_exists("$end_dir_reader");
    //echo $aanwezig ;
if (isset($_POST['knpUpload']) && $reader == 'Biocontrol') {
     // De systeemlokatie is de lokatie waar alle php-bestanden staan opgeslagen. Deze moet in tabel tblSysteem goed zijn ingevuld.
    if ($end_dir_exists == 0) {
        $fout = "De bestemmingslocatie, " . $end_dir_reader . ", wordt niet gevonden. Raadpleeg de beheerder. ";
    } else {
        if (is_uploaded_file($_FILES['bestand']['tmp_name'])) {
            $pathinfo = pathinfo($_FILES['bestand']['name']);
            if (in_array($pathinfo['extension'], $allowed)) {
                // De bestandsnaam van het uiteindelijke bestand
                // Natuurlijk naar eigen wens aan te passen.
                $file = $_FILES['bestand']['name'];
                if ($file == $input_file) {
                    if ($_FILES['bestand']['size'] < $maxsize) {
                        if (move_uploaded_file($_FILES['bestand']['tmp_name'], $end_dir_reader . $file)) { // Verplaatst reader.txt naar bijv. root ..../reader_1
                            $goed = 'Het bestand : ' . $file . ' is succesvol geüpload.';
                            //$content[] = '<p>De locatie van het bestand is: '.$dir.$file;
                            rename($end_dir_reader . $file, $end_dir_reader . $end_file_reader); // hernoemen van reader.txt naar bijv. user_1.txt
                            copy($end_dir_reader . $end_file_reader, $file_r . "/" . $end_file_reader);
                            // $DelFile = $end_dir_reader.$end_file_reader;
                             rename($end_dir_reader . $end_file_reader, $Backupnaam); // hernoemen van reader_1.txt naar bijv. reader_22-6-2015 20:42:44.txt
                            include "importReader.php";
                        } else {
                            $fout = 'Er is iets fout gegaan tijdens het uploaden.';
                        }
                    } else {
                        if ($maxsize == 0) {
                            $errors[] = 'Het uploaden van bestanden is uitgeschakeld.';
                        } else {
                            $fout = 'Het bestand is te groot.';
                        }
                    }
                } else {
                    $fout = 'De bestandsnaam is onjuist. Hernoem het eerst naar  : ' . $input_file;
                }
            } else {
                $fout = 'Deze extensie is niet toegestaan!';
            }
        } else {
            $fout = 'Er is geen bestand opgegeven.';
        }
    }
} elseif (isset($_POST['knpUpload'])) {
    $fout = 'Handmatig inlezen is met deze reader niet mogelijk.';
}
