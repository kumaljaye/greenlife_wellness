<?php
// --- SESSION MANAGEMENT ---
// session_start() must be the very first thing in your script.
// It creates a session or resumes the current one.
session_start();

// If the user is already logged in, redirect them away from the login page.
if (isset($_SESSION['user_id'])) {
    // Redirect to their respective dashboard based on role
    $role = $_SESSION['user_role'];
    header("Location: " . $role . "/dashboard.php");
    exit();
}

// Define constants and initialize message variables
define('BASE_URL', '/greenlife_wellness/');
$page_title = 'Login | GreenLife Wellness Center';
$error_message = '';

// --- BACKEND LOGIC: HANDLE LOGIN ATTEMPT ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    include_once('includes/db_connect.php');

    // --- 1. RETRIEVE FORM DATA ---
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // --- 2. VALIDATE INPUT ---
    if (empty($email) || empty($password)) {
        $error_message = "Both email and password are required.";
    } else {
        // --- 3. FETCH USER FROM DATABASE ---
        $sql = "SELECT id, full_name, email, password, role FROM users WHERE email = ?";
        
        // Use a PREPARED STATEMENT to prevent SQL Injection
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 1) {
            // User exists, now verify the password.
            $user = $result->fetch_assoc();

            // --- 4. VERIFY THE PASSWORD ---
            // password_verify() securely compares the submitted password with the hashed one from the database.
            if (password_verify($password, $user['password'])) {
                // Password is correct!

                // --- 5. STORE USER DATA IN THE SESSION ---
                session_regenerate_id(true); // Prevent session fixation attacks
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_name'] = $user['full_name'];
                $_SESSION['user_role'] = $user['role'];

                // --- 6. REDIRECT BASED ON ROLE ---
                header("Location: " . BASE_URL . "dashboard.php");
                exit(); // Crucial after a header redirect

            } else {
                // Password is not correct.
                $error_message = "Invalid email or password.";
            }
        } else {
            // No user found with that email address. Use a generic error for security.
            $error_message = "Invalid email or password.";
        }
        $stmt->close();
    }
    $conn->close();
}

// --- FRONTEND: DISPLAY THE PAGE ---
include_once('includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>Account Login</h1>
        <p class="lead">Welcome back! Please sign in to access your portal.</p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-5">
                <div class="form-container">
                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (isset($_GET['error']) && $_GET['error'] == 'accessdenied'): ?>
                         <div class="alert alert-warning" role="alert">
                            Please log in to access that page.
                        </div>
                    <?php endif; ?>
                    
                     <?php if (isset($_GET['status']) && $_GET['status'] == 'loggedout'): ?>
                         <div class="alert alert-success" role="alert">
                            You have been successfully logged out.
                        </div>
                    <?php endif; ?>

                    <form action="login.php" method="POST">
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg w-100">Log In</button>
                    </form>
                    <div class="text-center mt-3">
                        <p class="text-body-secondary">Don't have an account? <a href="register.php" class="footer-link">Sign Up Here</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
// Include the footer
include_once('includes/footer.php');
?>