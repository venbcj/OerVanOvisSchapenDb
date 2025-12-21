<?php

require_once('demo_functions.php');

include "connect_db.php";

if (empty($ubn)) {
    $fout = "Gebruikersnaam (ubn) is niet ingevuld.";
} elseif (empty($pword)) {
    $fout = "Wachtwoord is niet ingevuld.";
} elseif (empty($ctr_p)) {
    $fout = "Bevestig het wachtwoord.";
} elseif (empty($tel) && empty($mail)) {
    $fout = "1 van de 2 contactgevens moet zijn ingevuld.";
} elseif (!empty($mail)) {
    $isValid = true;
    $atIndex = strrpos($mail, "@");
    if (is_bool($atIndex) && !$atIndex) {
        $isValid = false;
    } else {
        $domain = substr($mail, $atIndex+1);
        $local = substr($mail, 0, $atIndex);
        // ... work with domain and local parts
    }
    if (!isset($domain)) {
        $isValid = false;
    } else {
        // controle top level domain mail
        $domIndex = strrpos($domain, ".");
        if (is_bool($domIndex) && !$domIndex) {
            $isValid = false;
        } else {
            $top_level_domain = substr($domain, $domIndex+1);
            $domain_name = substr($domain, 0, $domIndex);
            // ... work with domain and local parts
        }
        if (!isset($top_level_domain)) {
            $isValid = false;
        } else {
            $localLen = strlen($local);
            $domainLen = strlen($domain);
            $top_l_domainLen = strlen($top_level_domain);
            if ($localLen < 1 || $localLen > 64) {
                // local part length exceeded
                $isValid = false;
            } elseif ($domainLen < 1 || $domainLen > 255) {
                // domain part length exceeded
                $isValid = false;
            } elseif ($top_l_domainLen < 2 || $top_l_domainLen > 3) {
                // top level domain part length exceeded
                $isValid = false;
            }
        }
    }
    if ($isValid == false) {
        $fout = "mailadres is foutief.";
    }
}
if (!empty($ubn) && !empty($pword) && !empty($ctr_p) && $pword == $ctr_p && (!empty($tel) || (!empty($mail) && $isValid == true) )) {
    $lid_gateway = new LidGateway();
    if ($lid_gateway->ubn_exists($ubn)) {
        $fout = "Dit ubn bestaat al." ;
    } elseif (!empty($ubn) && Validate::numeriek($ubn) == 1) {
        $fout = "Dit ubn wordt niet herkend.";
    } elseif ($ubn == 1234567 || $ubn == 2345678 || $ubn == 3456789 || $ubn == 4567890 || $ubn == 0123456) {
        echo "Nee, Dit is geen ubn";
    } else {
        // Nu kan worden ingelezen
        $lid_gateway->store($ubn, $passw, $tel, $mail);
        // Sessie gegevens ophalen
        $lidId = $lid_gateway->findLididByUbn($ubn);
        Session::set("U1", $ubn);
        Session::set("W1", $passw);
        Session::set("I1", $lidId);
        Session::set("UB", $ubn);
        demo_table_insert($db, $lidId);
        header("location: ".$url."Home.php");
        exit();
    }
}
