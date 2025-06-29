<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'admin';
include_once('../includes/session_check.php');
$page_title = 'Add New User | Admin';
include_once('../includes/db_connect.php');

$error_message = '';
$success_message = '';

// --- HANDLE FORM SUBMISSION ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    $role = $_POST['role'];
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];

    $allowed_roles = ['client', 'therapist', 'admin'];
    if (empty($full_name) || empty($email) || empty($password) || empty($password_confirm) || empty($role) || empty($dob) || empty($gender)) {
        $error_message = "Please fill in all required fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "Invalid email format.";
    } elseif ($password !== $password_confirm) {
        $error_message = "Passwords do not match.";
    } elseif (strlen($password) < 8) {
        $error_message = "Password must be at least 8 characters long.";
    } elseif (!in_array($role, $allowed_roles)) {
        $error_message = "Invalid user role selected.";
    } else {
        // --- CHECK IF EMAIL ALREADY EXISTS ---
        $stmt_check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt_check->bind_param("s", $email);
        $stmt_check->execute();
        $stmt_check->store_result();

        if ($stmt_check->num_rows > 0) {
            $error_message = "An account with this email address already exists.";
        } else {
            // --- HASH THE PASSWORD ---
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            // --- INSERT THE NEW USER INTO THE DATABASE ---
            $stmt_insert = $conn->prepare("INSERT INTO users (full_name, email, phone_number, password, role, date_of_birth, gender) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt_insert->bind_param("sssssss", $full_name, $email, $phone, $hashed_password, $role, $dob, $gender);

            if ($stmt_insert->execute()) {
                header("Location: manage_users.php?status=added");
                exit();
            } else {
                $error_message = "Error: Could not create user. Please try again.";
            }
            $stmt_insert->close();
        }
        $stmt_check->close();
    }
}

include_once('../includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>Add a New User</h1>
        <p class="lead">Create a new account for a client, therapist, or administrator.</p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="form-container">
                    <?php if ($error_message): ?>
                        <div class="alert alert-danger"><?php echo $error_message; ?></div>
                    <?php endif; ?>
                    <form action="add_user.php" method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name <span style="color:red">*</span></label>
                            <input type="text" class="form-control" id="full_name" name="full_name" required>
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
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number">
                        </div>
                        <div class="mb-3">
                            <label for="role" class="form-label">Assign Role <span style="color:red">*</span></label>
                            <select class="form-select" id="role" name="role" required>
                                <option value="client" selected>Client</option>
                                <option value="therapist">Therapist</option>
                                <option value="admin">Admin</option>
                            </select>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="manage_users.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-accent">Create User Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$conn->close();
include_once('../includes/footer.php');
?>