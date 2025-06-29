<?php
// Define constants and check session for the 'therapist' role
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'therapist';
include_once('../includes/session_check.php');
$page_title = 'My Messages | Therapist Portal';
include_once('../includes/db_connect.php');

$therapist_id = $_SESSION['user_id'];

// --- FETCH ALL CONVERSATIONS FOR THIS THERAPIST ---
// We join with the users table to get the client's name for each conversation
$sql = "SELECT c.id, c.subject, c.last_reply_at, c.created_at, u.full_name AS client_name
        FROM conversations c
        JOIN users u ON c.client_id = u.id
        WHERE c.therapist_id = ?
        ORDER BY c.last_reply_at DESC, c.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $therapist_id);
$stmt->execute();
$conversations_result = $stmt->get_result();

include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>My Messages</h1>
        <p class="lead">Conversations with your clients.</p>
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
                <div class="card service-card therapist-messages-card">
                    <div class="card-header bg-dark text-light"><h4 class="mb-0">Conversation Threads</h4></div>
                    <div class="card-body">
                        <div class="list-group">
                            <?php if ($conversations_result->num_rows > 0): ?>
                                <?php while ($convo = $conversations_result->fetch_assoc()): ?>
                                    <a href="view_conversation.php?id=<?php echo $convo['id']; ?>" class="list-group-item list-group-item-action therapist-message-item">
                                        <div class="d-flex w-100 justify-content-between">
                                            <h6 class="mb-1 text-brand"><?php echo htmlspecialchars($convo['subject']); ?></h6>
                                            <small class="text-body-secondary">
                                                Last Activity: <?php echo date('M j, Y', strtotime($convo['last_reply_at'] ?? $convo['created_at'])); ?>
                                            </small>
                                        </div>
                                        <p class="mb-1">Conversation with <?php echo htmlspecialchars($convo['client_name']); ?></p>
                                    </a>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p class="text-body-secondary">You have no messages from clients.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$stmt->close();
$conn->close();
include_once('../includes/footer.php');
?>