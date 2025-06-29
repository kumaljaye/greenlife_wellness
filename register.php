<?php
// We define the BASE_URL and page_title here, at the very top.
define('BASE_URL', '/greenlife_wellness/');
$page_title = 'Register | GreenLife Wellness Center';

// Initialize message variables to be empty
$error_message = '';
$success_message = '';

// --- BACKEND LOGIC: HANDLE FORM SUBMISSION ---
// Check if the form was submitted by checking the request method
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Include the database connection file.
    // We use include_once to prevent it from being included multiple times.
    include_once('includes/db_connect.php');

    // --- 1. RETRIEVE AND SANITIZE FORM DATA ---
    // We retrieve the data from the $_POST array.
    // It's good practice to trim whitespace from inputs.
    $fullName = trim($_POST['fullName']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password']; // Passwords should not be trimmed
    $password_confirm = $_POST['password_confirm'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    // --- 2. VALIDATE THE DATA ---
    if (empty($fullName) || empty($email) || empty($password) || empty($password_confirm) || empty($dob) || empty($gender)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } else {
        // --- 3. CHECK IF EMAIL ALREADY EXISTS ---
        // This is a critical step to ensure no duplicate users.
        $sql_check = "SELECT id FROM users WHERE email = ?";
        
        // Use a PREPARED STATEMENT to prevent SQL Injection
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $email); // "s" means the parameter is a string
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "An account with this email address already exists.";
        } else {
            // --- 4. HASH THE PASSWORD ---
            // NEVER store plain-text passwords. This is a major security risk.
            // password_hash() is the modern, secure way to do this in PHP.
            // It automatically handles salting.
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // --- 5. INSERT THE NEW USER INTO THE DATABASE ---
            $sql_insert = "INSERT INTO users (full_name, email, phone_number, password, role, date_of_birth, gender) VALUES (?, ?, ?, ?, 'client', ?, ?)";
            
            // Use another PREPARED STATEMENT for the INSERT query
            $stmt_insert = $conn->prepare($sql_insert);
            // "ssssss" indicates six string parameters
            $stmt_insert->bind_param("sssss", $fullName, $email, $phone, $hashed_password, $dob, $gender);

            if ($stmt_insert->execute()) {
                $success_message = "Registration successful! You can now log in.";
            } else {
                $error_message = "Error: Could not register. Please try again later.";
                // For debugging, you might log the actual error: error_log($stmt_insert->error);
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
    $conn->close();
}

// --- FRONTEND: DISPLAY THE PAGE ---
// The header must be included AFTER the backend logic.
include_once('includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>Create an Account</h1>
        <p class="lead">Join our community to start your wellness journey today.</p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="form-container">
                    <?php if (!empty($success_message)): ?>
                        <div class="alert alert-success" role="alert">
                            <?php echo $success_message; ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger" role="alert">
                            <?php echo $error_message; ?>
                        </div>
                    <?php endif; ?>

                    <form action="register.php" method="POST">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address <span style="color:red">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender <span style="color:red">*</span></label>
                            <select class="form-select" id="gender" name="gender" required>
                                <option value="">Select Gender</option>
                                <option value="Male">Male</option>
                                <option value="Female">Female</option>
                                <option value="Other">Other</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password (min. 8 characters) <span style="color:red">*</span></label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="password_confirm" class="form-label">Confirm Password <span style="color:red">*</span></label>
                            <input type="password" class="form-control" id="password_confirm" name="password_confirm" required>
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth <span style="color:red">*</span></label>
                            <input type="date" class="form-control" id="dob" name="dob" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg w-100">Create Account</button>
                    </form>
                    <div class="text-center mt-3">
                        <p class="text-body-secondary">Already have an account? <a href="login.php" class="footer-link">Log In</a></p>
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