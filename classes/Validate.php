<?php

class Validate {

    public static function numeriek($subject) {
        if (preg_match('/([[a-zA-Z])/', $subject, $matches)) { return 1; }
    }

}
