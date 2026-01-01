<?php

class SqlBuilderTest extends UnitCase {

    public function setup(): void {
        $this->sut = new SqlBuilder(Db::instance());
    }

    public function test_plain_statement() {
        $SQL = 'TRUNCATE tabel';
        $args = [];
        $expected = 'TRUNCATE tabel';
        $this->assertEquals($expected, $this->sut->statement($SQL, $args));
    }

    public function test_refuses_valueless_arguments() {
        $this->expectException(Exception::class);
        $this->sut->statement('', [['aap']]);
    }

    public function test_refuses_noisy_arguments() {
        $this->expectException(Exception::class);
        $this->sut->statement('', [['aap', 'noot', 'mies', 'wim']]);
    }

    public function test_refuses_nonarray_arguments() {
        $this->expectException(Exception::class);
        $this->sut->statement('', ['aap']);
    }

    // nog een testcase maken die laat zien dat rommel wordt weggefilterd?
    public function test_pdo_types() {
        $SQL = 'SELECT * FROM tabel WHERE getal = :getal AND bedrag = :bedrag AND tekst = :tekst AND datum = :datum AND boolean = :boolean';
        $args = [
            [':getal', 1, 'int'], // getver... moet ik tegen een constante-implementatie testen. Of die in SqlBuilder publiek maken.. is dat de oplossing?
            [':bedrag', 3.2, 'float'],
            [':tekst', 'tekst', 'txt'],
            [':datum', '2010-01-01', 'date'],
            [':boolean', true, 'bool'],
        ];
        $expected = "SELECT * FROM tabel WHERE getal = 1 AND bedrag = 3.2 AND tekst = 'tekst' AND datum = '2010-01-01' AND boolean = true";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args));
    }

    public function test_pdo_null() {
        $SQL = "SELECT * FROM tabel WHERE param = :param";
        $args = [[':param', null, 'int']];
        $expected = "SELECT * FROM tabel WHERE param = NULL"; // en dat is fout...
        $this->assertEquals($expected, $this->sut->statement($SQL, $args));
    }

    public function test_pdo_defaults_to_string() {
        $SQL = "SELECT * FROM tabel WHERE param = :param";
        $args = [[':param', 'dinges']];
        $expected = "SELECT * FROM tabel WHERE param = 'dinges'";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args));
    }

    public function test_alexander_int_is_cast_and_unquoted() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'int',
        ];
        $args = [
            'param' => 1,
        ];
        $expected = "SELECT * FROM tabel WHERE param = 1";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_float_is_cast_and_unquoted() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'float',
        ];
        $args = [
            'param' => '3.2a',
        ];
        $expected = "SELECT * FROM tabel WHERE param = 3.2";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_string_is_escaped_and_quoted() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'txt',
        ];
        $args = [
            'param' => "test'; drop table users",
        ];
        $expected = "SELECT * FROM tabel WHERE param = 'test\'; drop table users'";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    // is dit de plek om ongeldige params te weigeren? Ik betwijfel het.
    public function test_alexander_date_is_quoted() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'date',
        ];
        $args = [
            'param' => "2010-01-01",
        ];
        $expected = "SELECT * FROM tabel WHERE param = '2010-01-01'";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_bool_is_cast() {
        $SQL = "SELECT * FROM tabel WHERE :%param AND :%untrue";
        $type_list = [
            'param' => 'bool',
            'untrue' => 'bool',
        ];
        $args = [
            'param' => "2010-01-01",
            'untrue' => false,
        ];
        $expected = "SELECT * FROM tabel WHERE param AND NOT untrue";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_value_is_null() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'txt',
        ];
        $args = [
            'param' => null,
        ];
        $expected = "SELECT * FROM tabel WHERE param IS NULL";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_array_ints_becomes_in() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'int',
        ];
        $args = [
            'param' => [1,2,3],
        ];
        $expected = "SELECT * FROM tabel WHERE param IN (1,2,3)";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

    public function test_alexander_array_strings_becomes_in() {
        $SQL = "SELECT * FROM tabel WHERE :%param";
        $type_list = [
            'param' => 'txt',
        ];
        $args = [
            'param' => [1,2,3],
        ];
        $expected = "SELECT * FROM tabel WHERE param IN ('1','2','3')";
        $this->assertEquals($expected, $this->sut->statement($SQL, $args, $type_list));
    }

}
