<?php

function checkAuth() {
    // If no session exists, start one
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is not logged in, redirect to login
    if (!isset($_SESSION['user_id'])) {
        header('Location: /FolioFlow/login');
        exit();
    }
}

function checkGuest() {
    // If no session exists, start one
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    // If user is already logged in, redirect to dashboard
    if (isset($_SESSION['user_id'])) {
        header('Location: /FolioFlow/dashboard');
        exit();
    }
}