<?php

class View {

    # hiermee maak je een complete menu-link
    public static function link_to($caption, $path, $attributes = []): string {
        $attribute_clause = self::attributes($attributes);
        return "<a href=\"".Url::getWebroot()."$path\"$attribute_clause>$caption</a>\n";
    }

    // public, want menu gebruikt dit. Slechte reden. Haal menu hier maar binnen TODO: (BCB) #0004129 dit
    public static function attributes($attributes = []) {
        return implode(
            ' ',
            array_map(
                function ($attr, $val) {
                    return " $attr=\"$val\"";
                },
                array_keys($attributes),
                array_values($attributes)
            )
        );
    }

    // $name is bestandsnaam zonder .tpl.php, uitgaande van /templates
    // In fase 1 dumpt dit de uitvoer nog naar het scherm.
    public static function render($name, $data = []) {
        // TODO: #0004130  hier moet waarschijnlijk nog een file-root voor, vergelijkbaar met Url::getWebroot
        $file = "templates/$name.tpl.php";
        if (!file_exists($file)) {
            throw new Exception("template file $file not found");
        }
        extract($data);
        include $file;
    }

    public static function janee($name, $selected) {
        self::radios($name, ['Ja' => 1, 'Nee' => 0], $selected);
    }

    public static function radios($name, $collection, $selected) {
        self::render('form/_radios', compact(array_keys(get_defined_vars())));
    }

    public static function nonzero($value) {
        // niet gezet, leeg, of 0? Dan zie je niks.
        if (!empty($value)) {
            echo $value;
        }
    }

}
