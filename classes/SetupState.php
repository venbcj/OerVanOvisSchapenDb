<?php

class SetupState {

    // TODO: (BV) #0004162 gaat onderstaand commentaar over de drie delen van de union? of over de drie "methoden" laatste-versie, readersetup, readertaken?
    /* Eerste query zoek alleen readerApp versies
    Tweede query zoek naar readerApp versie i.c.m. taakversies
    Derde query zoek naar alleen taakversies */
    public function versies($persoonlijke_map) {
        $versie_gateway = new VersiebeheerGateway();
        $last_versieId = $versie_gateway->zoek_laatste_versie();
        $Readersetup_bestand = $versie_gateway->zoek_readersetup_in($last_versieId);
        $Readertaken_bestand = $versie_gateway->zoek_readertaken_in($last_versieId);
        // hee, dit fragment /staat/ al in Readerversies.php
        if (isset($Readersetup_bestand)) {
            $file = $persoonlijke_map.'/Readerversies/'.$Readersetup_bestand;
            $appfile_exists = file_exists($file);
        } else {
            $appfile_exists = true;
        }
        // hee, dit fragment /staat/ al in Readerversies.php
        if (isset($Readertaken_bestand)) {
            $takenfile_exists = file_exists($persoonlijke_map.'/Readerversies/'.$Readertaken_bestand);
        } else {
            $takenfile_exists = true;
        }
        $actuele_versie = null;
        if ($appfile_exists && $takenfile_exists) {
            // deze variabele komt terug in menu1 en menubeheer, maar ook aangeroepen vanuit header.tpl. Daarom moet-ie er nu al zijn.
            // "Nu"? Ja, in de veronderstelling dat deze code wordt aangeroepen voordat "het topmenu" wordt opgebouwd.
            $actuele_versie = 'Ja';
        }
        return compact(explode(' ', 'last_versieId Readersetup_bestand Readertaken_bestand appfile_exists takenfile_exists actuele_versie'));
    }

}
