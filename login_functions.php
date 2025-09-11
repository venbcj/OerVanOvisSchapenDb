<?php

function login($row) {
    Auth::login($row);
}

function logout() {
    Auth::logout();
}
