<?php

class JsonAgridentParser {

    private $data;
    private $lidId;

    public function __construct($data, $lidId) {
        $this->data = $data;
        $this->lidId = $lidId;
    }

    public function execute() {
        ob_start();
        $this->perform();
        return ob_get_clean();
    }

    private function perform() {
        if (!$this->data) {
            return;
        }
        $db = Db::instance();
        $lidid = $this->lidId; // "interface" naar de includes
        $taken = array('Worpregistratie', 'Doodgeboren', 'Groepsgeboorte', 'Verplaatsing', 'Spenen', 'Afvoer', 'Aanvoer', 'Omnummeren', 'Medicaties', 'Halsnummers', 'Groepsafvoer', 'Voerregistratie', 'Dekken', 'Dracht');
        foreach ($this->data as $index => $item) {
            // Inlezen record
            for ($i = 0; $i < count($taken); $i++) { // Er zijn 7 elementen nl. zie array $velden
                if ($i == 0) {
                    $inhoud = $item -> {$taken[$i]} ;
                    include "impWorpregistratie.php";
                }
                if ($i == 1) {
                    $inhoud = $item -> {$taken[$i]} ;
                    include "impDoodgeboren.php";
                }
                if ($i == 2) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer');
                    include "impAgrident.php";
                }
                if ($i == 3) {
                    $inhoud = $item -> {$taken[$i]} ;
                    include "impVerplaatsing.php";
                }
                if ($i == 4) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'HokId', 'Levensnummer', 'Gewicht');
                    include "impAgrident.php";
                }
                if ($i == 5) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'Ubn', 'Reden', 'Transponder', 'Levensnummer', 'Gewicht');
                    include "impAgrident.php";
                }
                if ($i == 6) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('Datum', 'Ubn', 'RasId', 'HokId', 'Transponder', 'Levensnummer','Datumdier', 'Geslacht', 'ActId', 'Gewicht');
                    include "impAgrident.php";
                }
                if ($i == 7) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Nieuw_Transponder', 'Nieuw_Nummer');
                    include "impAgrident.php";
                }
                if ($i == 8) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'ArtId','Reden','Toedat','Transponder','Levensnummer');
                    include "impAgrident.php";
                }
                if ($i == 9) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'Transponder', 'Levensnummer', 'Kleur', 'Halsnr');
                    include "impAgrident.php";
                }
                if ($i == 10) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'Ubn', 'Transponder', 'Levensnummer');
                    include "impAgrident.php";
                }
                if ($i == 11) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'HokId', 'DoelId', 'ArtId', 'Toedat');
                    include "impAgrident.php";
                }
                if ($i == 12) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'VdrId', 'MoederTransponder', 'Moeder');
                    include "impAgrident.php";
                }
                if ($i == 13) {
                    $inhoud = $item -> {$taken[$i]} ;
                    $velden = array('ActId', 'Datum', 'MoederTransponder', 'Moeder', 'Drachtig', 'Grootte');
                    include "impAgrident.php";
                }
            }
        }
    }

}
