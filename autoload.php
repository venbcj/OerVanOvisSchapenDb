<?php

# include-path voorlopig maar even hier
$app_folders = ['classes', 'templates'];
set_include_path(implode(':', array_merge(explode(':', get_include_path()), $app_folders)));

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
