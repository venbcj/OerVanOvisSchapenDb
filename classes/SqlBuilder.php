<?php

class SqlBuilder {

    // HERHAALD IN Gateway en SqlBuilder
    protected const TXT = 'txt';
    protected const INT = 'int';
    protected const FLOAT = 'float';
    protected const BOOL = 'bool';
    protected const DATE = 'date';

    private $db;

    public function __construct(Db $db) {
        $this->db = $db;
    }

    // Twee smaken:
    // - pdo-stijl, en alexander-stijl.
    // Dit is pdo-stijl:
    // in args zit een array van benoemde parameters:
    // parameter is een [naam, waarde, formaat]
    // naam is bijvoorbeeld :id
    // waarde is bijvoorbeeld 4
    // formaat kan zijn self::INT, self::FLOAT, self::TXT, self::BOOL, self::DATE
    // TODO meer formaten
    public function statement($SQL, $args = [], $type_list = []) {
        if (false !== strpos($SQL, ':%')) {
            return $this->interpret($SQL, $args, $type_list);
        }
        foreach ($args as $arg) {
            $arg = $this->validateArg($arg);
            [$name, $value, $format] = $arg;
            if (is_null($value)) {
                $value = 'NULL';
            } else {
                $value = $this->restrict($value, $format);
            }
            $SQL = preg_replace("#$name\b#", $value, $SQL);
        }
        return $SQL;
    }
    private $op;
    private $prefix = '';

    // Dit is alexander-stijl
    // WIP overgangsperiode: als de plaatshouders niet met : beginnen maar met :%,
    //   is $args een associatieve array met kolomnaam => waarde
    //   - kolomnaam BEGINT NIET MET :%
    //   - kolomnaam kan op een spatie en een operator (< > <> <= >= BETWEEN) eindigen
    //   - waarde kan een array zijn -> IN (waarde[,waarde]*) -- tenzij operator BETWEEN, dan -> BETWEEN \1 AND \2
    //   - waarde kan NULL zijn -> IS NULL
    //   NICETOHAVE - als kolomnaam een geregistreerde boolean is, -> truthy wordt VELD, falsy wordt NOT VELD
    private function interpret($SQL, $args, $type_list) {
        foreach ($args as $placeholder => $expr) {
            $key = $placeholder;
            $this->op = '=';
            // default formaat is TXT
            $type = self::TXT;
            if (array_key_exists($key, $type_list)) {
                $type = $type_list[$key];
            }
            if ($expr === null) {
                $value = 'NULL';
                $this->op = 'IS';
            } else {
                $value = $this->expression($key, $expr, $type);
            }
            $replacement = implode(' ', array_filter([$this->prefix, $key, $this->op, $value]));
            $SQL = preg_replace("#:%$placeholder\b#", $replacement, $SQL);
        }
        return $SQL;
    }

    private function restrict($value, $type) {
        switch ($type) {
        case self::TXT:
        case self::DATE:
            return $this->surround($this->db->real_escape_string($value), "''");
        case self::INT:
            return (int) $value;
        case self::FLOAT:
            return (float) $value;
        case self::BOOL:
            return $value ? 'true' : 'false';
        }
    }

    private function expression($key, $expr, $type) {
        if ($type == self::BOOL) {
            $this->op = '';
            $this->prefix = '';
            if (!$expr) {
                $this->prefix = 'NOT';
            }
            return '';
        }
        if (is_array($expr)) {
            $this->op = 'IN';
            return $this->surround(
                implode(
                    ',',
                    array_map(function ($val) use ($type) {
                        return $this->restrict($val, $type);
                    }, $expr)
                ),
                '()'
            );
        }
        return $this->restrict($expr, $type);
    }

    // brackets moet twee tekens zijn
    private function surround($target = '', $brackets = '') {
        // toch op 3 regels vanwege php-peculiariteiten.
        // - array_splice moet op een variabele werken, mag niet een expressie zijn
        // - array_splice geeft de geknipte elementen terug, niet het eindresultaat
        // (deze twee hangen samen)
        $bracks = str_split($brackets);
        $res = array_splice($bracks, 1, 0, $target);
        return implode('', $bracks);
    }

    private function validateArg($arg) {
        if (!is_array($arg)) {
            throw new Exception("Query-parameters: verwacht een array van arrays.");
        }
        // default formaat is TXT
        if (count($arg) == 2) {
            $arg[] = self::TXT;
        }
        if (count($arg) != 3) {
            throw new Exception("Query-parameters: een parameter moet twee of drie onderdelen bevatten.");
        }
        return $arg;
    }

}
