<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'client';
include_once('../includes/session_check.php');
$page_title = 'My Messages | GreenLife Wellness Center';
include_once('../includes/db_connect.php');

// We fetch inquiries submitted by the logged-in user's email address
// Remove direct use of $_SESSION['user_email'] and always fetch from DB
$stmt_user = $conn->prepare("SELECT email FROM users WHERE id = ?");
$stmt_user->bind_param("i", $_SESSION['user_id']);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user_data = $result_user->fetch_assoc();
$client_email = $user_data['email'];
$stmt_user->close();


$stmt_inquiries = $conn->prepare("SELECT * FROM inquiries WHERE email = ? ORDER BY submitted_at DESC");
$stmt_inquiries->bind_param("s", $client_email);
$stmt_inquiries->execute();
$inquiries_result = $stmt_inquiries->get_result();

include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>My Inquiries</h1>
        <p class="lead">A history of your communication with our team.</p>
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
                <div class="d-flex justify-content-end mb-3">
                    <a href="../contact.php" class="btn btn-accent">
                        <i class="bi bi-plus-circle me-1"></i> New Inquiry
                    </a>
                </div>
                <div class="accordion" id="inquiriesAccordion">
                    <?php if ($inquiries_result->num_rows > 0): ?>
                        <?php while ($inquiry = $inquiries_result->fetch_assoc()): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header">
                                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapse-<?php echo $inquiry['id']; ?>">
                                        <strong><?php echo htmlspecialchars($inquiry['subject']); ?></strong>&nbsp;- Submitted on <?php echo date('M j, Y', strtotime($inquiry['submitted_at'])); ?>
                                    </button>
                                </h2>
                                <div id="collapse-<?php echo $inquiry['id']; ?>" class="accordion-collapse collapse" data-bs-parent="#inquiriesAccordion">
                                    <div class="accordion-body">
                                        <h5>Your Message:</h5>
                                        <p class="message-body"><?php echo nl2br(htmlspecialchars($inquiry['message'])); ?></p>
                                        <hr>
                                        <h5>Reply from GreenLife Wellness:</h5>
                                        <?php if (!empty($inquiry['reply_body'])): ?>
                                            <p class="message-body bg-success bg-opacity-10"><?php echo nl2br(htmlspecialchars($inquiry['reply_body'])); ?></p>
                                            <small class="text-body-secondary">Replied on: <?php echo date('M j, Y, g:i A', strtotime($inquiry['replied_at'])); ?></small>
                                        <?php else: ?>
                                            <p class="text-body-secondary">A member of our team will reply to you shortly.</p>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <p class="text-body-secondary">You have not sent any messages. <a href="../contact.php" class="footer-link">Contact us</a> with any questions!</p>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$stmt_inquiries->close();
$conn->close();
include_once('../includes/footer.php');
?>