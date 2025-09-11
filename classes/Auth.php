<?php

class Auth {

    public static function login($row) {
        global $db, $dtb;
        session_start();
        $_SESSION["U1"] = "$_POST[txtUser]";
        $_SESSION["W1"] = "$_POST[txtPassw]";
        $_SESSION["I1"] = $row['lidId'];
        $_SESSION["A1"] = $row['alias'];
        $_SESSION["PA"] = 1;
        $_SESSION["RPP"] = 30; // standaard aantal regels per pagina
        $_SESSION["ID"] = 0; // het Id waarmee de pagina is geopend. Bijv. hokId 1559 bij HokAfleveren.php
        $_SESSION["DT1"] = null; // Als (records per) pagina wordt ververst wordt datum onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw datum te kiezen. Zie HokAfleveren.php
        $_SESSION["BST"] = null; // Als (records per) pagina wordt ververst wordt bestemming onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokAfleveren.php
        $_SESSION["Fase"] = null; // Als (records per) pagina wordt ververst wordt fase onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokUitscharen.php (HokAfleveren.php)
        $_SESSION['KZ'] = null; // Als pagina wordt ververst wordt de keuze (filter) onthouden. Zie HokOverpl.php
        $_SESSION["CNT"] = null; // Gebruikt in Contact.php
        // TODO: (BCB) ik wil de global $lidId vervangen door getLidId(), als eerste stap
        $lidId = $_SESSION["I1"];
        // In de demo omgeving worden de basis gegevens elke maand opnieuw vervangen.
        if ($dtb == "k36098_bvdvschapendbs" && $lidId > 1) {
            // Kijken of maand is verstreken o.b.v. createdatum in tabl tblSchapen
            $controle_maand = zoek_eerste_stalrecord($lidId);
            $huidige_maand = date('Ym');
            if ($controle_maand < $huidige_maand && $lidId <> 1) {
                // TODO: waarom worden de deletes in de ene database gedaan, en de inserts in de andere?
                demo_table_delete($db, $dtb, $lidId);
                demo_table_insert($db, $lidId);
            }
        }
        // Einde In de demo omgeving worden de basis gegevens elke maand opnieuw vervangen.
        if (isset($_POST['knpBasis'])) {
            demo_userdelete();
            demo_table_insert($db, $lidId);
        }
        // Laatste inlog vastleggen
        noteer_inlogtijd($lidId);
    }

    public static function logout() {
        if (session_status() != PHP_SESSION_ACTIVE) {
            session_start();
        }
        unset($_SESSION);
        session_destroy();
    }

    public static function is_logged_in() {
        return isset($_SESSION["U1"]) && isset($_SESSION["W1"]) && isset($_SESSION["I1"]);
    }

}
