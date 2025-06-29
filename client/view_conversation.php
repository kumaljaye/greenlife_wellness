<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'client';
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');

// Get IDs from the URL link.
$therapist_id = $_GET['therapist_id'] ?? 0;
$service_id = $_GET['service_id'] ?? 0; // We use the service ID to generate a standard subject
$client_id = $_SESSION['user_id'];

// If the required IDs are not provided in the URL, redirect away.
if ($therapist_id == 0 || $service_id == 0) {
    header("Location: dashboard.php");
    exit();
}

// --- HANDLE WHEN THE CLIENT SENDS A NEW MESSAGE ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && !empty(trim($_POST['message_body']))) {
    $conversation_id = $_POST['conversation_id'];
    $message_body = trim($_POST['message_body']);
    $sender_id = $client_id;

    // Insert the new message into the database
    $stmt = $conn->prepare("INSERT INTO messages (conversation_id, sender_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $conversation_id, $sender_id, $message_body);
    $stmt->execute();
    $stmt->close();
    
    // Update the 'last_reply_at' timestamp in the conversations table
    $stmt_update = $conn->prepare("UPDATE conversations SET last_reply_at = NOW() WHERE id = ?");
    $stmt_update->bind_param("i", $conversation_id);
    $stmt_update->execute();
    $stmt_update->close();

    // Redirect to the same page to show the new message and prevent form resubmission
    header("Location: view_conversation.php?therapist_id=$therapist_id&service_id=$service_id");
    exit();
}

// --- FIND OR CREATE THE CONVERSATION THREAD ---
// First, get the names of the service and therapist for the conversation subject
$service_stmt = $conn->prepare("SELECT name FROM services WHERE id = ?");
$service_stmt->bind_param("i", $service_id);
$service_stmt->execute();
$service_name = $service_stmt->get_result()->fetch_assoc()['name'] ?? 'a service';
$service_stmt->close();

// Create a standard subject line
$subject = "Conversation regarding " . $service_name;

// Check if a conversation on this topic already exists between the client and therapist
$stmt = $conn->prepare("SELECT id FROM conversations WHERE client_id = ? AND therapist_id = ? AND subject = ?");
$stmt->bind_param("iis", $client_id, $therapist_id, $subject);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If it exists, get its ID
    $conversation = $result->fetch_assoc();
    $conversation_id = $conversation['id'];
} else {
    // If it does not exist, create a new conversation record
    $stmt_create = $conn->prepare("INSERT INTO conversations (client_id, therapist_id, subject) VALUES (?, ?, ?)");
    $stmt_create->bind_param("iis", $client_id, $therapist_id, $subject);
    $stmt_create->execute();
    $conversation_id = $conn->insert_id; // Get the ID of the new conversation
    $stmt_create->close();
}
$stmt->close();

// --- FETCH ALL MESSAGES FOR THIS CONVERSATION ---
$stmt_msgs = $conn->prepare("SELECT m.*, u.full_name as sender_name, u.role as sender_role FROM messages m JOIN users u ON m.sender_id = u.id WHERE m.conversation_id = ? ORDER BY m.sent_at ASC");
$stmt_msgs->bind_param("i", $conversation_id);
$stmt_msgs->execute();
$messages_result = $stmt_msgs->get_result();

$page_title = 'My Conversation';
include_once('../includes/header.php');
?>
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
                <div class="card service-card">
                    <div class="card-body" id="message-window" style="height: 50vh; overflow-y: auto;">
                        <?php if ($messages_result->num_rows > 0): ?>
                            <?php while($message = $messages_result->fetch_assoc()): 
                                $is_current_user = ($message['sender_id'] == $_SESSION['user_id']);
                            ?>
                                <div class="message-bubble <?php echo $is_current_user ? 'client' : 'staff'; ?>">
                                    <p class="message-body p-3"><?php echo nl2br(htmlspecialchars($message['message'])); ?></p>
                                    <small class="text-body-secondary">
                                        <strong><?php echo htmlspecialchars($message['sender_name']); ?></strong>
                                        - <?php echo date('M j, g:i A', strtotime($message['sent_at'])); ?>
                                    </small>
                                </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <p class="text-center text-body-secondary">No messages yet. Send the first message below.</p>
                        <?php endif; ?>
                    </div>
                    <div class="card-footer">
                        <form action="view_conversation.php?therapist_id=<?php echo $therapist_id; ?>&service_id=<?php echo $service_id; ?>" method="POST">
                            <input type="hidden" name="conversation_id" value="<?php echo $conversation_id; ?>">
                            <div class="input-group">
                                <textarea name="message_body" class="form-control" placeholder="Type your message..." rows="2" required></textarea>
                                <button class="btn btn-accent" type="submit">Send <i class="bi bi-send-fill"></i></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<script>
    // Simple script to scroll to the bottom of the message window
    const messageWindow = document.getElementById('message-window');
    messageWindow.scrollTop = messageWindow.scrollHeight;
</script>
<?php
$stmt_msgs->close();
$conn->close();
include_once('../includes/footer.php');
?>