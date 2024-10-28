<?php
// To access the existing session
session_start();

// Clear all session variables by setting it to empty array
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to home page
header('Location: /FolioFlow');
exit();