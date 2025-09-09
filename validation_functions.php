<?php

# NOTE: preg_match geeft 1 terug bij een match, 0 bij geen match, en false als er iets misging.
# Je hoeft dus niet zelf een 1 terug te sturen.
function numeriek($subject) {
    if (preg_match('/([[a-zA-Z])/', $subject, $matches)) { return 1; }
}
