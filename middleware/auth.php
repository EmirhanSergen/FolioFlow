<?php

function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

function checkAuth() {
    startSession();

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . getenv('LOGIN_URL')); // Redirect to login
        exit();
    }
}

function checkGuest() {
    startSession();

    if (isset($_SESSION['user_id'])) {
        header('Location: ' . getenv('DASHBOARD_URL')); // Redirect to dashboard
        exit();
    }
}
