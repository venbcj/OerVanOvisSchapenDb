<?php

class Auth {

    public static function login($row) {
        global $db, $dtb;
        Session::start();

        Session::set("U1", "$_POST[txtUser]");
        Session::set("W1", "$_POST[txtPassw]");
        Session::set("I1", $row['lidId']);
        Session::set("A1", $row['alias']);
        Session::set("PA", 1);
        Session::set("RPP", 30); // standaard aantal regels per pagina
        Session::set("ID", 0); // het Id waarmee de pagina is geopend. Bijv. hokId 1559 bij HokAfleveren.php
        Session::set("DT1", null); // Als (records per) pagina wordt ververst wordt datum onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw datum te kiezen. Zie HokAfleveren.php
        Session::set("BST", null); // Als (records per) pagina wordt ververst wordt bestemming onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokAfleveren.php
        Session::set("Fase", null); // Als (records per) pagina wordt ververst wordt fase onthouden. Zo kan pagin worden doorlopen zonder steeds opnieuw bestemming te kiezen. Zie HokUitscharen.php (HokAfleveren.php)
        Session::set('KZ', null); // Als pagina wordt ververst wordt de keuze (filter) onthouden. Zie HokOverpl.php
        Session::set("CNT", null); // Gebruikt in Contact.php
        // TODO: (BCB) ik wil de global $lidId vervangen door getLidId(), als eerste stap
        $lidId = Session::get("I1");
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
        Session::destroy();
    }

    public static function is_logged_in() {
        return (Session::isset("U1")) && (Session::isset("W1")) && (Session::isset("I1"));
    }

}
