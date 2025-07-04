<?php
// Always start the session to access it.
session_start();

// Unset all of the session variables.
$_SESSION = array();

// Destroy the session cookie.
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session.
session_destroy();

// Redirect to the login page with a confirmation message.
header("Location: login.php?status=loggedout");
exit();
?>