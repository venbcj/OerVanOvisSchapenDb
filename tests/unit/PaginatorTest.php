<?php

class PaginatorTest extends UnitCase {

    // dependencies. Er zijn (nog) tests die records verwijderen uit tblLeden, en niet opruimen.
    // Daardoor faalt deze test af en toe met een constante voor het ledental.
    public function test_constructor() {
        $this->uses_db();
        $aantal_leden = $this->db->query("SELECT COUNT(*) FROM tblLeden")->fetch_row()[0];
        $sut = new Paginator('tblLeden', '', null);
        $this->assertEquals($aantal_leden, $sut->total_records);
    }

    public function test_condition_limits_results() {
        $this->uses_db();
        $sut = new Paginator('tblLeden', 'where lidId=1', null);
        $this->assertEquals(1, $sut->total_records);
    }

    public function test_fetch_data_apparently_needs_positive_page() {
        $this->uses_db();
        $sut = new Paginator('tblActie', 'where actId=1', null);
        $this->expectException(Exception::class);
        $actual = $sut->fetch_data();
    }

    public function test_fetch_data() {
        $this->uses_db();
        // ik kijk in de acties-tabel omdat die minder velden heeft dan de leden-tabel.
        $sut = new Paginator('tblActie', 'where actId=1', null, 1);
        $expected = [[
            'actId' => 1,
            'actie' => 'Geboren',
            'op' => 1,
            'af' => 0,
            'aan' => 1,
            'uit' => 0,
        ]];
        $actual = $sut->fetch_data();
        $this->assertEquals($expected, $actual);
    }

    public function test_confirm_show_rpp() {
        $this->uses_db();
        $sut = new Paginator('tblActie', 'where actId=1', null, 1);
        $expected = '<script>
            function openUrl() {
                var control = document.getElementById(\'rpp\');
                window.location = "?page=1&rpp="+control.options[control.selectedIndex].value;
    }
        </script>
        <select id="rpp" onchange="openUrl();"><option value="10">10</option>
<option value="20">20</option>
<option value="30">30</option>
<option value="40">40</option>
<option value="50">50</option>
<option value="60" selected="selected">60</option>
<option value="70">70</option>
<option value="80">80</option>
<option value="90">90</option>
<option value="100">100</option>
</select>
';
        $actual = $sut->show_rpp();
        $expected_nowhitespace = preg_replace('/\s/', '', $expected);
        $actual_nowhitespace = preg_replace('/\s/', '', $actual);
        if ($expected_nowhitespace != $actual_nowhitespace) {
            $diff = Stringdiff::create(5, $expected_nowhitespace, $actual_nowhitespace);
            $this->fail($diff->diff());
        }
        $this->assertTrue(true);
    }

    public function test_show_rpp_has_selected_option() {
        $this->uses_db();
        $sut = new Paginator('tblActie', 'where actId=1', null, 1);
        $actual = $sut->show_rpp();
        $this->assertStringContainsString('selected="selected"', $actual);
    }

    public function test_no_pagenumbers_when_one_page() {
        $this->uses_db();
        $sut = new Paginator('tblActie', '', null, 1);
        $actual = $sut->show_page_numbers();
        $this->assertSame(null, $actual);
    }

    public function test_pagenumbers_when_more_pages() {
        $this->uses_db();
        $sut = new Paginator('tblActie', '', null, 1, 10);
        $actual = $sut->show_page_numbers();
        // met 22 records in de fixture en 10 per pagina krijg je 3 pagina's
        $this->assertStringContainsString(' van 3', $actual);
    }

    public function test_more_pages_than_links() {
        $this->uses_db();
        $sut = new Paginator('tblActie', '', null, 1, 5);
        $actual = $sut->show_page_numbers(2);
        // als je 2 page_numbers vraagt, krijg je ook nog een Last Page-link. Vandaar 3.
        $this->assertEquals(3, substr_count($actual, '<a href'));
    }

    public function test_show_first_page_link_when_window_doesnt_contain_it() {
        $this->uses_db();
        $sut = new Paginator('tblActie', '', null, 3, 5);
        $actual = $sut->show_page_numbers(2);
        $this->assertStringContainsString('First Page (1)', $actual);
    }

    // en wat betekent dit precies?
    // Test bedoelt de end > total_pages - controle te raken.
    // De assert is niet kenmerkend. Hoe verschijnt het effect van dat terugschuiven van het venster in de uitvoer?
    public function test_adjust_window_back_on_last_page() {
        $this->uses_db();
        $sut = new Paginator('tblActie', '', null, 9, 5);
        $actual = $sut->show_page_numbers(2);
        $this->assertStringContainsString('First Page (1)', $actual);
    }

}
