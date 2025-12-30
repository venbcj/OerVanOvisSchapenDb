<?php

class Schema {

    protected const TXT = 'txt';
    protected const INT = 'int';
    protected const FLOAT = 'float';
    protected const BOOL = 'bool';
    protected const DATE = 'date';

    public static function dictionary() {
        return [
            'lidId' => 'int',
            'draId' => 'int',
            'volwId' => 'int',
        ];
    }

}
