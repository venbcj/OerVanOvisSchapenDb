<?php

class GebruikerPageTest extends IntegrationCase {

    /* Deze tests falen nu als de Readerversies-link in het menu rood is in plaats van de verwachte zwart.
     * Maar dat heeft met de ingelogde gebruiker te maken, niet met de pstId
     * Kunnen we toch een approval test opstellen, alleen dan niet voor de HELE pagina?
     */

    public function testGebruikerPageForm() {
        $this->runfixture('user-kobus');
        $this->runfixture('hok');
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob']);
        $this->db->query("delete from tblRedenuser");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

    public function testGebruikerPageFormAgrident() {
        $this->runfixture('user-kobus-agrident');
        $this->runfixture('hok');
        // deze check kan uiteindelijk weg, even checken dat we echt tegen de goede data aankijken:
        $this->assertTableWithPK('tblLeden', 'lidId', 42, ['login' => 'kobus', 'alias' => 'koob', 'reader' => 'Agrident']);
        $this->db->query("delete from tblRedenuser");
        $this->get('/Gebruiker.php', ['ingelogd' => 1, 'pstId' => 42]);
        $this->approve();
    }

}
