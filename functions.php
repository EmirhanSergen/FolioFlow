<?php

function dd($value){
    echo "<pre>";
    var_dump($value);
    echo "</pre>";

    die();
}

// Are requested Url and value same ?
function urlIs($value){
    return $_SERVER['REQUEST_URI'] === $value;
}



