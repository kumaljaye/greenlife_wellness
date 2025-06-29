<?php
/**
 * Session & Authentication Check
 * This script is included on every secure page.
 * * It performs two main checks:
 * 1. Is the user logged in? (Is $_SESSION['user_id'] set?)
 * 2. Does the logged-in user have the required role to view the page?
 *
 * A variable `$required_role` must be set on the page before including this script.
 */

// Resume the session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check 1: Is the user logged in?
if (!isset($_SESSION['user_id'])) {
    // If not, redirect to the login page with a message.
    header("Location: " . BASE_URL . "login.php?error=accessdenied");
    exit();
}

// Check 2: Does the user have the required role?
if (isset($required_role) && $_SESSION['user_role'] !== $required_role) {
    // If their role doesn't match, send them away.
    // You could redirect to an "access denied" page or back to their own dashboard.
    // For now, we'll just send them to the homepage.
    header("Location: " . BASE_URL . "index.php?error=unauthorized");
    exit();
}

// If both checks pass, the script continues and the page content is displayed.
?>