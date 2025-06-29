<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'admin';
include_once('../includes/session_check.php');
$page_title = 'Manage Users | Admin';
include_once('../includes/db_connect.php');

// --- HANDLE USER DELETION ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_user_id'])) {
    $user_to_delete = intval($_POST['delete_user_id']);
    // Security: Prevent admin from deleting their own account
    if ($user_to_delete == $_SESSION['user_id']) {
        header("Location: manage_users.php?error=selfdelete");
        exit();
    } else {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
        $stmt->bind_param("i", $user_to_delete);
        if ($stmt->execute()) {
            header("Location: manage_users.php?status=deleted");
            exit();
        }
        $stmt->close();
    }
}

// --- FETCH ALL USERS ---
$users_result = $conn->query("SELECT id, full_name, email, phone_number, role, created_at FROM users ORDER BY created_at DESC");

include_once('../includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>Manage Users</h1>
        <p class="lead">View, edit, and delete user accounts.</p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <!-- Navigation Box Include -->
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'deleted'): ?>
                    <div class="alert alert-success">User successfully deleted.</div>
                <?php endif; ?>
                <?php if (isset($_GET['error']) && $_GET['error'] == 'selfdelete'): ?>
                    <div class="alert alert-danger">You cannot delete your own account.</div>
                <?php endif; ?>

                <div class="card ">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">All System Users</h4>
                        <a href="add_user.php" class="btn btn-accent">Add New User</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Full Name</th>
                                        <th>Email</th>
                                        <th>Role</th>
                                        <th>Registered On</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($user = $users_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $user['id']; ?></td>
                                            <td><?php echo htmlspecialchars($user['full_name']); ?></td>
                                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    switch($user['role']) {
                                                        case 'admin': echo 'danger'; break;
                                                        case 'therapist': echo 'info'; break;
                                                        default: echo 'secondary';
                                                    }
                                                ?>"><?php echo ucfirst($user['role']); ?></span>
                                            </td>
                                            <td><?php echo date('M j, Y', strtotime($user['created_at'])); ?></td>
                                        
                                            
                                            <td>
                                                <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-outline-accent">Edit</a>
                                                
                                                <form method="POST" action="manage_users.php" class="d-inline" onsubmit="return confirm('Are you sure you want to permanently delete this user? This action cannot be undone.');">
                                                    <input type="hidden" name="delete_user_id" value="<?php echo $user['id']; ?>">
                                                    <button type="submit" class="btn btn-sm btn-outline-danger">Delete</button>
                                                </form>
                                            </td>

                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$conn->close();
include_once('../includes/footer.php');
?>