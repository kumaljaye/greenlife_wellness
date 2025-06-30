<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'admin';
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');

$user_id = $_GET['id'] ?? null;
if (!$user_id) { header("Location: manage_users.php"); exit(); }
$error_message = '';

// --- HANDLE FORM SUBMISSION (UPDATE LOGIC) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $full_name = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone_number']);
    $dob = $_POST['dob'];
    $gender = $_POST['gender'];
    $new_password = $_POST['new_password'];

    // Check if a new password was provided
    if (!empty($new_password)) {
        // A new password was entered, so we hash it and include it in the update
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, dob = ?, gender = ?, password = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssssi", $full_name, $email, $phone, $dob, $gender, $hashed_password, $user_id);
    } else {
        // No new password, so we don't update the password field
        $sql = "UPDATE users SET full_name = ?, email = ?, phone_number = ?, dob = ?, gender = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $full_name, $email, $phone, $dob, $gender, $user_id);
    }

    if ($stmt->execute()) {
        header("Location: manage_users.php?status=updated");
        exit();
    } else {
        $error_message = "Failed to update user.";
    }
    $stmt->close();
}

// --- FETCH CURRENT USER DATA TO PRE-FILL THE FORM ---
// The query is updated to fetch the new fields
$stmt = $conn->prepare("SELECT full_name, email, phone_number, role, dob, gender FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) { header("Location: manage_users.php"); exit(); }
$user = $result->fetch_assoc();
$stmt->close();

$page_title = 'Edit User | Admin';
include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container"><h1>Edit User: <?php echo htmlspecialchars($user['full_name']); ?></h1></div>
</header>
<main class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <div class="col-md-9">
                <div class="form-container">
                     <?php if ($error_message): ?><div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>
                    <form action="edit_user.php?id=<?php echo $user_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="full_name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="full_name" name="full_name" value="<?php echo htmlspecialchars($user['full_name']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="phone_number" class="form-label">Phone Number</label>
                            <input type="text" class="form-control" id="phone_number" name="phone_number" value="<?php echo htmlspecialchars($user['phone_number'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="dob" class="form-label">Date of Birth</label>
                            <input type="date" class="form-control" id="dob" name="dob" value="<?php echo htmlspecialchars($user['date_of_birth'] ?? ''); ?>">
                        </div>
                        <div class="mb-3">
                            <label for="gender" class="form-label">Gender</label>
                            <select class="form-select" id="gender" name="gender">
                                <option value="Male" <?php if (($user['gender'] ?? '') == 'Male') echo 'selected'; ?>>Male</option>
                                <option value="Female" <?php if (($user['gender'] ?? '') == 'Female') echo 'selected'; ?>>Female</option>
                                <option value="Other" <?php if (($user['gender'] ?? '') == 'Other') echo 'selected'; ?>>Other</option>
                            </select>
                        </div>
                        <hr class="my-4">
                        <div class="mb-3">
                            <label for="new_password" class="form-label">New Password</label>
                            <input type="password" class="form-control" id="new_password" name="new_password">
                            <div class="form-text">Leave this field blank to keep the user's current password.</div>
                        </div>
                        <div class="d-flex justify-content-end">
                            <a href="manage_users.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-accent">Save Changes</button>
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