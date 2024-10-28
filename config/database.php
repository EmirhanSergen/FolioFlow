<?php
// config/database.php

function connectDB() {
    $config = require __DIR__ . '/config.php';
    return new DataBase($config['database']);
}