<?php
// Check if the form was submitted using the POST method
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    
    // Include the database connection file
    include_once('db_connect.php');

    // --- RETRIEVE AND SANITIZE FORM DATA ---
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // --- SERVER-SIDE VALIDATION ---
    if (empty($fullName) || empty($email) || empty($subject) || empty($message)) {
        // Redirect back with an error message if fields are empty
        header("Location: ../contact.php?status=error&msg=empty");
        exit();
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        // Redirect back with an error if the email is invalid
        header("Location: ../contact.php?status=error&msg=email");
        exit();
    }

    // --- INSERT INTO DATABASE ---
    // All checks passed, proceed to insert the data into the inquiries table.
    $sql = "INSERT INTO inquiries (full_name, email, subject, message, status) VALUES (?, ?, ?, ?, 'new')";
    
    // Use a prepared statement to prevent SQL injection
    $stmt = $conn->prepare($sql);
    // "ssss" means we are binding four string parameters
    $stmt->bind_param("ssss", $fullName, $email, $subject, $message);

    // Execute the statement and check if it was successful
    if ($stmt->execute()) {
        // Success: Redirect back to the contact page with a success status
        header("Location: ../contact.php?status=success");
        exit();
    } else {
        // Database Error: Redirect back with a generic error
        header("Location: ../contact.php?status=error&msg=db");
        exit();
    }

    $stmt->close();
    $conn->close();

} else {
    // If the page is accessed directly without a POST request, redirect to the homepage.
    header("Location: ../index.php");
    exit();
}
?>