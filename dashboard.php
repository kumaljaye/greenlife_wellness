<?php
// Unified Dashboard for All Roles - GreenLife Wellness
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
define('BASE_URL', '/greenlife_wellness/');
include_once('includes/db_connect.php');

// Check if user is logged in and get role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['user_role'])) {
    header("Location: " . BASE_URL . "login.php?error=accessdenied");
    exit();
}

$user_id = $_SESSION['user_id'];
$user_role = $_SESSION['user_role'];
$user_name = isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : '';
$page_title = 'Dashboard | GreenLife Wellness Center';

// --- DATA FETCHING BASED ON ROLE ---
$stats = [];
if ($user_role === 'admin') {
    // Admin: fetch stats
    $stats['total_appointments'] = $conn->query("SELECT COUNT(*) AS count FROM appointments")->fetch_assoc()['count'];
    $stats['new_inquiries'] = $conn->query("SELECT COUNT(*) AS count FROM inquiries WHERE status = 'new'")->fetch_assoc()['count'];
    $today = date('Y-m-d');
    $stats['todays_appointments'] = $conn->query("SELECT COUNT(*) AS count FROM appointments WHERE DATE(appointment_datetime) = '$today' AND status = 'scheduled'")->fetch_assoc()['count'];
}
if ($user_role === 'client') {
    // Client: fetch upcoming appointments and recent inquiries
    $stmt_appt = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE client_id = ? AND status = 'scheduled' AND appointment_datetime >= NOW()");
    $stmt_appt->bind_param("i", $user_id);
    $stmt_appt->execute();
    $stats['upcoming_appts'] = $stmt_appt->get_result()->fetch_assoc()['count'];
    $stmt_appt->close();
    $stmt_user = $conn->prepare("SELECT email FROM users WHERE id = ?");
    $stmt_user->bind_param("i", $user_id);
    $stmt_user->execute();
    $client_email = $stmt_user->get_result()->fetch_assoc()['email'];
    $stmt_user->close();
    $stmt_inquiries = $conn->prepare("SELECT id, subject, submitted_at, status, reply_body FROM inquiries WHERE email = ? ORDER BY submitted_at DESC LIMIT 3");
    $stmt_inquiries->bind_param("s", $client_email);
    $stmt_inquiries->execute();
    $recent_inquiries = $stmt_inquiries->get_result();
}
if ($user_role === 'therapist') {
    // Therapist: fetch today's appointments
    $today = date('Y-m-d');
    $stmt = $conn->prepare("SELECT a.id, a.appointment_datetime, s.name AS service_name, c.full_name AS client_name FROM appointments a JOIN services s ON a.service_id = s.id JOIN users c ON a.client_id = c.id WHERE a.therapist_id = ? AND DATE(a.appointment_datetime) = ? AND a.status = 'scheduled' ORDER BY a.appointment_datetime ASC");
    $stmt->bind_param("is", $user_id, $today);
    $stmt->execute();
    $todays_appointments = $stmt->get_result();
    // All appointments count
    $stmt_all = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE therapist_id = ?");
    $stmt_all->bind_param("i", $user_id);
    $stmt_all->execute();
    $stats['all_appointments'] = $stmt_all->get_result()->fetch_assoc()['count'];
    $stmt_all->close();
    // Upcoming appointments count
    $stmt_upcoming = $conn->prepare("SELECT COUNT(*) AS count FROM appointments WHERE therapist_id = ? AND status = 'scheduled' AND appointment_datetime >= NOW()");
    $stmt_upcoming->bind_param("i", $user_id);
    $stmt_upcoming->execute();
    $stats['upcoming_appointments'] = $stmt_upcoming->get_result()->fetch_assoc()['count'];
    $stmt_upcoming->close();
}

include_once('includes/header.php');
?>
<style>
    .stat-card {
        background: #23263a;
        border-radius: 0.75rem;
        box-shadow: 0 2px 12px rgba(30,60,114,0.10);
        border: none;
        color: #ffe082;
        transition: box-shadow 0.2s;
    }
    .stat-card .card-title {
        color: #7be495;
        font-size: 1.1rem;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }
    .stat-card .display-5 {
        color: #ffe082;
        font-size: 2.2rem;
        font-weight: 700;
    }
    .list-group-item {
        background: #23263a;
        color: #e0e0e0;
        border: none;
    }
    .list-group-item .badge {
        background: #7be495;
        color: #23263a;
        font-weight: 600;
    }
    .section-title, .page-header h1 {
        color: #ffe082;
        font-weight: 700;
        letter-spacing: 1px;
    }
    .text-brand {
        color: #7be495 !important;
    }
    .text-muted, .text-body-secondary {
        color: #b0b0b0 !important;
    }
</style>
<header class="page-header">
    <div class="container">
        <h1 class="mb-2">Welcome, <?php if ($user_name) echo $user_name; ?></h1>
        <p class="lead mb-0">Here is your dashboard for <?php echo date('l, F j, Y'); ?>.</p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <!-- Navigation Box Include -->
            <div class="col-md-3">
                <?php include('includes/sidebar_nav.php'); ?>
            </div>
            <!-- Dashboard Content -->
            <div class="col-md-9">
                <div class="row g-4">
                    <!-- Admin Section -->
                    <?php if ($user_role === 'admin'): ?>
                        <h2 class="section-title mb-4">Admin Overview</h2>
                        <div class="col-lg-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Total Appointments</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['total_appointments']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">New Inquiries</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['new_inquiries']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Today's Appointments</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['todays_appointments']; ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Client Section -->
                    <?php if ($user_role === 'client'): ?>
                        <h2 class="section-title mb-4">Your Activity</h2>
                        <div class="col-lg-6">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Upcoming Appointments</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['upcoming_appts']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-6">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Recent Inquiries</h5>
                                    <?php if (isset($recent_inquiries) && $recent_inquiries->num_rows > 0): ?>
                                        <ul class="list-group">
                                            <?php while ($inq = $recent_inquiries->fetch_assoc()): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?php echo htmlspecialchars($inq['subject']); ?>
                                                    <span class="badge bg-secondary"><?php echo date('M j', strtotime($inq['submitted_at'])); ?></span>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No recent inquiries.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Therapist Section -->
                    <?php if ($user_role === 'therapist'): ?>
                        <h2 class="section-title mb-4">Today's Schedule</h2>
                        <div class="col-lg-4 mb-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">All Appointments</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['all_appointments']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-4 mb-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Upcoming Appointments</h5>
                                    <p class="display-5 fw-bold mb-0"><?php echo $stats['upcoming_appointments']; ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-lg-12 mb-4">
                            <div class="card stat-card h-100">
                                <div class="card-body">
                                    <h5 class="card-title">Today's Appointments</h5>
                                    <?php if (isset($todays_appointments) && $todays_appointments->num_rows > 0): ?>
                                        <ul class="list-group">
                                            <?php while ($appt = $todays_appointments->fetch_assoc()): ?>
                                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <?php echo htmlspecialchars($appt['service_name']) . ' with ' . htmlspecialchars($appt['client_name']); ?>
                                                    <span class="badge bg-secondary"><?php echo date('H:i', strtotime($appt['appointment_datetime'])); ?></span>
                                                </li>
                                            <?php endwhile; ?>
                                        </ul>
                                    <?php else: ?>
                                        <p class="text-muted mb-0">No appointments scheduled for today.</p>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$conn->close();
include_once('includes/footer.php');
?>
