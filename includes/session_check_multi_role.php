<?php
/**
 * Multi-Role Session & Authentication Check
 * This script checks if a user is logged in AND if their role is in an allowed list.
 * A variable `$allowed_roles` (an array) must be set on the page before including this script.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check 1: Is the user logged in at all?
if (!isset($_SESSION['user_id'])) {
    header("Location: " . BASE_URL . "login.php?error=accessdenied");
    exit();
}

// Check 2: Is the user's role in the list of allowed roles?
if (!isset($allowed_roles) || !is_array($allowed_roles) || !in_array($_SESSION['user_role'], $allowed_roles)) {
    // If their role is not allowed, send them away.
    header("Location: " . BASE_URL . "index.php?error=unauthorized");
    exit();
}
?>