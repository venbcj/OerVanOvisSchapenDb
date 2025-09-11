<?php

# include-path voorlopig maar even hier
set_include_path('.:classes');

spl_autoload_register(function ($class) {
    foreach (explode(':', get_include_path()) as $prefix) {
        foreach (explode(' ', ' .class .trait') as $type) {
            $file = "$class$type.php";
            if (file_exists("$prefix/$file")) {
                include_once $file;
                break;
            }
        }
    }
});
