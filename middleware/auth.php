<?php
/**
 * Start session if not already active.
 */
function startSession() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
}

/**
 * Ensure the user is authenticated.
 * If not, redirect to the login page.
 */
function checkAuth() {
    startSession();

    if (!isset($_SESSION['user_id'])) {
        header('Location: ' . getenv('LOGIN_URL')); // Redirect to login
        exit();
    }
}

/**
 * Ensure the user is a guest (not logged in).
 * If already logged in, redirect to dashboard.
 */
function checkGuest() {
    startSession();

    if (isset($_SESSION['user_id'])) {
        header('Location: ' . getenv('DASHBOARD_URL')); // Redirect to dashboard
        exit();
    }
}
