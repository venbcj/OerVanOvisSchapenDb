<?php

# dit kan nog niet, zolang wij als allereerste geladen worden
# if (ENV != 'production') {
    ini_set('display_errors', true);
    error_reporting(E_ALL);
# }

# include-path voorlopig maar even hier
$app_folders = ['.', 'classes', 'templates', 'fpdf', 'gateways'];
set_include_path(implode(':', $app_folders));

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
Session::set_instance(new Session());
Response::setProduction();
