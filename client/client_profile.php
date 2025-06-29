<?php
// Therapist and admin can view client profiles
// Place this file in /client/client_profile.php

define('BASE_URL', '/greenlife_wellness/');
$allowed_roles = ['admin', 'therapist'];
include_once('../includes/session_check_multi_role.php');
$page_title = 'Client Profile | GreenLife Wellness';
include_once('../includes/db_connect.php');

if (!isset($_GET['id'])) {
    header("Location: ../dashboard.php");
    exit();
}
$client_id = intval($_GET['id']);

$stmt_client = $conn->prepare("SELECT full_name, email, phone_number, created_at, dob, gender FROM users WHERE id = ? AND role = 'client'");
$stmt_client->bind_param("i", $client_id);
$stmt_client->execute();
$client_result = $stmt_client->get_result();
if ($client_result->num_rows === 0) {
    echo "Client not found.";
    exit();
}
$client = $client_result->fetch_assoc();

$stmt_appts = $conn->prepare("SELECT a.appointment_datetime, a.status, s.name AS service_name, u.full_name AS therapist_name FROM appointments a JOIN services s ON a.service_id = s.id JOIN users u ON a.therapist_id = u.id WHERE a.client_id = ? ORDER BY a.appointment_datetime DESC");
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
            <!-- Sidebar Navigation -->
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <!-- Main Content -->
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Client Profile</h4>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-4">
                                <div class="card service-card h-100">
                                    <div class="card-header"><h5>Contact Information</h5></div>
                                    <div class="card-body">
                                        <p><strong>Name:</strong> <?php echo htmlspecialchars($client['full_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($client['email']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($client['phone_number'] ?? 'Not Provided'); ?></p>
                                        <p><strong>Date of Birth:</strong> <?php echo !empty($client['date_of_birth']) ? htmlspecialchars($client['date_of_birth']) : 'Not Provided'; ?></p>
                                        <p><strong>Gender:</strong> <?php echo !empty($client['gender']) ? htmlspecialchars($client['gender']) : 'Not Provided'; ?></p>
                                        <p><strong>Member since:</strong> <?php echo date('F Y', strtotime($client['created_at'])); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Appointment History Table Below -->
                <div class="card mt-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Appointment History</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Service</th>
                                        <th>Therapist</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if($appointments_result->num_rows > 0): ?>
                                        <?php while($appt = $appointments_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?php echo date('M j, Y g:i A', strtotime($appt['appointment_datetime'])); ?></td>
                                                <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                                                <td><?php echo htmlspecialchars($appt['therapist_name']); ?></td>
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
                                        <tr><td colspan="4">This client has no appointment history.</td></tr>
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
