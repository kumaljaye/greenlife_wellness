<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'therapist';
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');

$therapist_id = $_SESSION['user_id'];
$conversation_id = $_GET['id'] ?? 0;

if ($conversation_id == 0) { header("Location: messages.php"); exit(); }

// Security Check: Make sure the therapist is actually part of this conversation
$stmt_verify = $conn->prepare("SELECT subject, client_id FROM conversations WHERE id = ? AND therapist_id = ?");
$stmt_verify->bind_param("ii", $conversation_id, $therapist_id);
$stmt_verify->execute();
$verify_result = $stmt_verify->get_result();
if ($verify_result->num_rows === 0) {
    header("Location: messages.php"); // Not their conversation, redirect away
    exit();
}
$conversation = $verify_result->fetch_assoc();
$subject = $conversation['subject'];
$stmt_verify->close();


// --- HANDLE SENDING A NEW MESSAGE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['message_body']))) {
    $message_body = trim($_POST['message_body']);
    
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $conversation_id, $therapist_id, $message_body);
    $stmt->execute();
    $stmt->close();
    
    $stmt_update = $conn->prepare("UPDATE conversations SET last_reply_at = NOW() WHERE id = ?");
    $stmt_update->bind_param("i", $conversation_id);
    $stmt_update->execute();
    $stmt_update->close();

    header("Location: view_conversation.php?id=$conversation_id");
    exit();
}

// --- FETCH ALL MESSAGES FOR THIS CONVERSATION ---
$stmt_msgs = $conn->prepare("SELECT m.*, u.full_name as sender_name, u.role as sender_role FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? ORDER BY m.sent_at ASC");
$stmt_msgs->bind_param("i", $conversation_id);
$stmt_msgs->execute();
$messages_result = $stmt_msgs->get_result();

$page_title = 'View Conversation';
include_once('../includes/header.php');
?>
<style>
.therapist-messages-card {
    background: #23263a;
    border-radius: 0.5rem;
    box-shadow: 0 1px 6px rgba(30,60,114,0.08);
    border: 1px solid #23263a;
    color: #ffe082;
}
.therapist-messages-card .card-header {
    background: #181c2f !important;
    border-radius: 0.5rem 0.5rem 0 0;
    border: none;
}
.therapist-message-item {
    background: #23263a;
    color: #e0e0e0;
    border: none;
    border-bottom: 1px solid #181c2f;
    /* No hover effect */
}
.text-brand {
    color: #7be495 !important;
}
</style>
<header class="page-header">
    <div class="container">
        <h1>Conversation</h1>
        <p class="lead"><?php echo htmlspecialchars($subject); ?></p>
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
                
                <div class="row justify-content-center">
                    <div class="col-lg-8">
                        <div class="card service-card">
                            <div class="card-body" id="message-window" style="height: 50vh; overflow-y: auto;">
                                <?php while($message = $messages_result->fetch_assoc()): 
                                    $is_current_user = ($message['sender_id'] == $_SESSION['user_id']);
                                ?>
                                    <div class="message-bubble <?php echo $is_current_user ? 'staff' : 'client'; ?>">
                                        <p class="message-body p-3"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                        <small class="text-body-secondary">
                                            <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                                            - <?php echo date('M j, g:i A', strtotime($message['sent_at'])); ?>
                                        </small>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="card-footer">
                                <form action="view_conversation.php?id=<?php echo $conversation_id; ?>" method="POST">
                                    <div class="input-group">
                                        <textarea name="message_body" class="form-control" placeholder="Type your reply..." rows="2" required></textarea>
                                        <button class="btn btn-accent" type="submit">Send <i class="bi bi-send-fill"></i></button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    const messageWindow = document.getElementById('message-window');
    messageWindow.scrollTop = messageWindow.scrollHeight;
</script>
<?php
$stmt_msgs->close();
$conn->close();
include_once('../includes/footer.php');
?>