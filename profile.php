<?php

define('BASE_URL', '/greenlife_wellness/');
$allowed_roles = ['client', 'therapist'];
include_once('includes/session_check_multi_role.php');
$page_title = 'My Profile | GreenLife Wellness Center';
include_once('includes/db_connect.php');

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$profile_message = '';
$password_message = '';

// --- HANDLE FORM SUBMISSIONS (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // --- Part 1: Handle Profile Details Update ---
    if (isset($_POST['update_profile'])) {
        $full_name = trim($_POST['full_name']);
        $email = trim($_POST['email']);
        $phone_number = trim($_POST['phone_number']);
        $dob = $_POST['dob'];
        $gender = $_POST['gender'];
        if (empty($full_name) || empty($email) || empty($dob) || empty($gender)) {
            $profile_message = '<div class="alert alert-danger">Full name, email, date of birth, and gender are required.</div>';
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $profile_message = '<div class="alert alert-danger">Invalid email format.</div>';
        } else {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $email, $user_id);
            $stmt->execute();
            if ($stmt->get_result()->num_rows > 0) {
                $profile_message = '<div class="alert alert-danger">This email address is already in use by another account.</div>';
            } else {
                $stmt = $conn->prepare("UPDATE users SET full_name = ?, email = ?, phone_number = ?, dob = ?, gender = ? WHERE id = ?");
                $stmt->bind_param("sssssi", $full_name, $email, $phone_number, $dob, $gender, $user_id);
                if ($stmt->execute()) {
                    $_SESSION['user_name'] = $full_name;
                    $profile_message = '<div class="alert alert-success">Profile updated successfully!</div>';
                }
            }
            $stmt->close();
        }
    }
    // --- Part 2: Handle Password Change ---
    if (isset($_POST['change_password'])) {
        $current_password = $_POST['current_password'];
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
            $password_message = '<div class="alert alert-danger">Please fill in all password fields.</div>';
        } elseif ($new_password !== $confirm_password) {
            $password_message = '<div class="alert alert-danger">New passwords do not match.</div>';
        } elseif (strlen($new_password) < 8) {
            $password_message = '<div class="alert alert-danger">New password must be at least 8 characters long.</div>';
        } else {
            $stmt = $conn->prepare("SELECT password FROM users WHERE id = ?");
            $stmt->bind_param("i", $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            $user = $result->fetch_assoc();
            if (password_verify($current_password, $user['password'])) {
                $hashed_new_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt_update = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
                $stmt_update->bind_param("si", $hashed_new_password, $user_id);
                if ($stmt_update->execute()) {
                    $password_message = '<div class="alert alert-success">Password changed successfully!</div>';
                }
                $stmt_update->close();
            } else {
                $password_message = '<div class="alert alert-danger">Incorrect current password.</div>';
            }
            $stmt->close();
        }
    }
}
// --- FETCH CURRENT USER DATA to pre-fill the form ---
$stmt = $conn->prepare("SELECT full_name, email, phone_number, dob, gender FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

include_once('includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>My Profile</h1>
        <p class="lead">Manage your personal information and account settings.</p>
    </div>
</header>
<main class="py-5">
    <div class="container">
        <div class="row">
            <!-- Navigation Box Include -->
            <div class="col-md-3">
                <?php include('includes/sidebar_nav.php'); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="row">
                    <div class="col-lg-6 mb-4">
                        <div class="form-container h-100">
                            <h4>Edit Profile Details</h4>
                            <hr>
                            <?php if ($profile_message) echo $profile_message; ?>
                            <form action="profile.php" method="POST">
                                <div class="mb-3">
                                    <label for="full_name" class="form-label">Full Name <span style="color:red">*</span></label>
                                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="email" class="form-label">Email Address <span style="color:red">*</span></label>
                                    <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="dob" class="form-label">Date of Birth <span style="color:red">*</span></label>
                                    <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['dob'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="gender" class="form-label">Gender <span style="color:red">*</span></label>
                                    <select class="form-select" id="gender" name="gender" required>
                                        <option value="">Select Gender</option>
                                        <option value="Male" <?php if (($user['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                                        <option value="Female" <?php if (($user['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                                        <option value="Other" <?php if (($user['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="phone_number" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                                </div>
                                <button type="submit" name="update_profile" class="btn btn-accent">Update Profile</button>
                            </form>
                        </div>
                    </div>
                    <div class="col-lg-6 mb-4">
                        <div class="form-container h-100">
                            <h4>Change Password</h4>
                            <hr>
                            <?php if ($password_message) echo $password_message; ?>
                            <form action="profile.php" method="POST">
                                <div class="mb-3">
                                    <label for="current_password" class="form-label">Current Password</label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="new_password" class="form-label">New Password</label>
                                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                                </div>
                                <div class="mb-3">
                                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                                </div>
                                <button type="submit" name="change_password" class="btn btn-accent">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$conn->close();
include_once('includes/footer.php');
?>
