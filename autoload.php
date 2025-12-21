<?php

# dit kan nog niet, zolang wij als allereerste geladen worden
# if (ENV != 'production') {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
# }

$base = str_replace('\\', '/', __DIR__);

$app_folders = [
    $base . '/classes',
    $base . '/templates',
    $base . '/fpdf_stub',
    $base . '/gateways'
];

set_include_path(implode(PATH_SEPARATOR, $app_folders));

spl_autoload_register(function ($class) {
    foreach (explode(PATH_SEPARATOR, get_include_path()) as $prefix) {
        foreach (explode(' ', ' .class .trait') as $type) {
            $file = "$class$type.php";
            if (file_exists("$prefix/$file")) {
                include_once "$prefix/$file";
                return;
            }
        }
    }
});

//trigger_error("Autoloader: Class '$class' not found in include path.", E_USER_WARNING);
