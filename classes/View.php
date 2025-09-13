<?php

class View {

    # hiermee maak je een complete menu-link
    public static function link_to($caption, $path, $attributes = []) {
        $attribute_clause = self::attributes($attributes);
        return "<a href=\"".Url::getWebroot()."$path\"$attribute_clause>$caption</a>\n";
    }

    // public, want menu gebruikt dit. Slechte reden. Haal menu hier maar binnen TODO: (BCB) dit
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

}
