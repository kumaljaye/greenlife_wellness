<?php
define('BASE_URL', '/greenlife_wellness/');
// --- ADVANCED ACCESS CONTROL ---
// Define the roles that are allowed to access this page.
$allowed_roles = ['admin', 'therapist'];
include_once('../includes/session_check_multi_role.php'); // We will create this new session check file

$page_title = 'Client Profile | GreenLife Wellness';

// --- DATA FETCHING ---
include_once('../includes/db_connect.php');

// Get the client ID from the URL.
if (!isset($_GET['id'])) {
    // Redirect if no ID is provided
    header("Location: dashboard.php");
    exit();
}
$client_id = intval($_GET['id']);

// Fetch client's personal details
$stmt_client = $conn->prepare("SELECT full_name, email, phone_number, created_at FROM users WHERE id = ? AND role = 'client'");
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$client_result = $stmt_client->get_result();

if ($client_result->num_rows === 0) {
    // No client found with this ID, handle error
    echo "Client not found."; // Or redirect
    exit();
}
$client = $client_result->fetch_assoc();

// Fetch client's appointment history
$stmt_appts = $conn->prepare("SELECT a.appointment_datetime, a.status, s.name AS service_name, u.full_name AS therapist_name
                             FROM appointments a
                             JOIN services s ON a.service_id = s.id
                             JOIN users u ON a.therapist_id = u.id
                             WHERE a.client_id = ? ORDER BY a.appointment_datetime DESC");
$stmt_appts->bind_param("i", $client_id);
$stmt_appts->execute();
$appointments_result = $stmt_appts->get_result();

include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>Client Profile: <?php echo htmlspecialchars($client['full_name']); ?></h1>
        <p class="lead">Member since <?php echo date('F Y', strtotime($client['created_at'])); ?></p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-4 mb-4">
                <div class="card service-card h-100">
                    <div class="card-header"><h4>Contact Information</h4></div>
                    <div class="card-body">
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['phone_number'] ?? 'Not Provided'); ?></p>
                    </div>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="card service-card">
                    <div class="card-header"><h4>Appointment History</h4></div>
                    <div class="card-body">
                        <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                            <table class="table">
                                <tbody>
                                    <?php if($appointments_result->num_rows > 0): ?>
                                        <?php while($appt = $appointments_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('M j, Y', strtotime($appt['appointment_datetime'])); ?></td>
                                                <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                                                <td><small class="text-body-secondary">with <?php echo htmlspecialchars($appt['therapist_name']); ?></small></td>
                                                <td>
                                                    <span class="badge <?php 
                                                        switch($appt['status']) {
                                                            case 'completed': echo 'bg-success'; break;
                                                            case 'cancelled': echo 'bg-danger'; break;
                                                            case 'scheduled': echo 'bg-info'; break;
                                                        }
                                                    ?>"><?php echo ucfirst($appt['status']); ?></span>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr><td>This client has no appointment history.</td></tr>
                                    <?php endif; ?>
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
$stmt_client->close();
$stmt_appts->close();
$conn->close();
include_once('../includes/footer.php');
?>