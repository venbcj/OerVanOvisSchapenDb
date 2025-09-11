<?php

class View {

    # hiermee maak je een complete menu-link
    public static function link_to($caption, $path, $attributes = []) {
        $attribute_clause = implode(
            ' ',
            array_map(
                function ($attr, $val) {
                    return " $attr=\"$val\"";
                },
                array_keys($attributes),
                array_values($attributes)
            )
        );
        return "<a href=\"".Url::getWebroot()."$path\"$attribute_clause>$caption</a>";
    }

}
