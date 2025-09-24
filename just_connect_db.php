<?php

require_once "basisfuncties.php";
 
// dit is een verbouwsteiger: publiceer de relevante waarden nog even als globale variabelen
foreach(setup_db() as $name => $value) {
    $GLOBALS[$name] = $value;
    $$name = $value;
}
