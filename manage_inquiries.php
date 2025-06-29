<?php
define('BASE_URL', '/greenlife_wellness/');
$allowed_roles = ['admin', 'therapist'];
include_once('includes/session_check_multi_role.php');
$page_title = 'Manage Inquiries ';
include_once('includes/db_connect.php');

// --- HANDLE REPLY SUBMISSION (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['reply_inquiry_id'])) {
    $inquiry_id = intval($_POST['reply_inquiry_id']);
    $reply_body = trim($_POST['reply_body']);
    $admin_id = $_SESSION['user_id'];

    if (!empty($reply_body)) {
        // Update the inquiry with the reply text and set status to 'replied'
        $stmt = $conn->prepare("UPDATE inquiries SET status = 'replied', reply_body = ?, replied_at = NOW(), replied_by_id = ? WHERE id = ?");
        $stmt->bind_param("sii", $reply_body, $admin_id, $inquiry_id);
        if ($stmt->execute()) {
            header("Location: manage_inquiries.php?status=replied");
            exit();
        }
        $stmt->close();
    }
}

// --- FETCH ALL INQUIRIES ---
$sql = "SELECT i.*, u.full_name AS replied_by_name FROM inquiries i LEFT JOIN users u ON i.replied_by_id = u.id ORDER BY i.submitted_at DESC";
$inquiries_result = $conn->query($sql);

include_once('includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>Manage Customer Inquiries</h1>
        <p class="lead">Read and reply to messages from the website contact form.</p>
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
                <?php if (isset($_GET['status']) && $_GET['status'] == 'replied'): ?>
                    <div class="alert alert-success">Your reply has been sent.</div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">All Inquiries</h4>
                    </div>
                    <div class="card-body">
                        <div class="accordion" id="inquiriesAccordion">
                            <?php if ($inquiries_result->num_rows > 0): ?>
                                <?php while ($inquiry = $inquiries_result->fetch_assoc()): ?>
                                    <div class="accordion-item inquiry-item-<?php echo $inquiry['status']; ?>">
                                        <h2 class="accordion-header">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $inquiry['id']; ?>">
                                                <div class="w-100 d-flex justify-content-between align-items-center pe-3">
                                                    <span>
                                                        <span class="badge bg-<?php echo ($inquiry['status']=='new'?'accent':'info'); ?> me-2"><?php echo ucfirst($inquiry['status']); ?></span>
                                                        <strong><?php echo htmlspecialchars($inquiry['subject']); ?></strong>
                                                    </span>
                                                    <span class="text-body-secondary">from <?php echo htmlspecialchars($inquiry['full_name']); ?></span>
                                                </div>
                                            </button>
                                        </h2>
                                        <div id="collapse-<?php echo $inquiry['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#inquiriesAccordion">
                                            <div class="accordion-body">
                                                <p><strong>From:</strong> <?php echo htmlspecialchars($inquiry['full_name']); ?> (<?php echo htmlspecialchars($inquiry['email']); ?>)</p>
                                                <p><strong>Received:</strong> <?php echo date('M j, Y, g:i A', strtotime($inquiry['submitted_at'])); ?></p>
                                                <hr>
                                                <p class="message-body"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                                <hr>
                                                <form method="POST" action="manage_inquiries.php">
                                                    <input type="hidden" name="reply_inquiry_id" value="<?php echo $inquiry['id']; ?>">
                                                    <div class="mb-3">
                                                        <label class="form-label"><strong>Your Reply:</strong></label>
                                                        <textarea name="reply_body" class="form-control" rows="4" <?php if($inquiry['status']=='replied') echo 'disabled'; ?>><?php echo htmlspecialchars($inquiry['reply_body'] ?? ''); ?></textarea>
                                                    </div>
                                                    <?php if($inquiry['status'] != 'replied'): ?>
                                                        <button type="submit" class="btn btn-accent">Submit Reply</button>
                                                    <?php else: ?>
                                                        <p class="text-success"><i class="bi bi-check-circle-fill"></i> You replied to this on <?php echo date('M j, Y', strtotime($inquiry['replied_at'])); ?></p>
                                                    <?php endif; ?>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-body-secondary">There are no inquiries in the system.</p>
                            <?php endif; ?>
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