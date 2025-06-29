<?php
// Define constants and check session for the 'therapist' role
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'therapist';
include_once('../includes/session_check.php');
$page_title = 'My Full Schedule | GreenLife Wellness Center';

// Include DB connection
include_once('../includes/db_connect.php');

$therapist_id = $_SESSION['user_id'];

// --- HANDLE APPOINTMENT ACTIONS (CANCEL/COMPLETE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    if ($appointment_id > 0) {
        if (isset($_POST['cancel'])) {
            $stmt_action = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ? AND therapist_id = ?");
            $stmt_action->bind_param("ii", $appointment_id, $therapist_id);
            $stmt_action->execute();
            $stmt_action->close();
        } elseif (isset($_POST['complete'])) {
            $stmt_action = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ? AND therapist_id = ?");
            $stmt_action->bind_param("ii", $appointment_id, $therapist_id);
            $stmt_action->execute();
            $stmt_action->close();
        }
        // Refresh to prevent resubmission
        header("Location: schedule.php?status=updated");
        exit();
    }
}

// --- FETCH ALL UPCOMING APPOINTMENTS FOR THIS THERAPIST ---
$sql = "SELECT 
            a.id, 
            a.appointment_datetime, 
            a.notes, 
            a.service_id, 
            s.name AS service_name, 
            s.duration_minutes,
            c.full_name AS client_name, 
            c.phone_number AS client_phone
        FROM 
            appointments a
        JOIN 
            services s ON a.service_id = s.id
        JOIN 
            users c ON a.client_id = c.id
        WHERE 
            a.therapist_id = ? 
            AND a.appointment_datetime >= NOW() 
            AND a.status = 'scheduled'
        ORDER BY 
            a.appointment_datetime ASC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $therapist_id);
$stmt->execute();
$schedule_result = $stmt->get_result();

// --- FRONTEND DISPLAY ---
include_once('../includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>My Full Schedule</h1>
        <p class="lead">A complete list of your upcoming client appointments.</p>
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

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">All Upcoming Appointments</h4>
                        <!-- (Optional) Add filter dropdown here if you want to filter by status -->
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Client</th>
                                        <th>Phone</th>
                                        <th>Service</th>
                                        <th>Notes</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if ($schedule_result->num_rows > 0): ?>
                                        <?php while ($appt = $schedule_result->fetch_assoc()): ?>
                                            <tr>
                                                <td><?= date('M j, Y g:i A', strtotime($appt['appointment_datetime'])) ?></td>
                                                <td><?= htmlspecialchars($appt['client_name']) ?></td>
                                                <td><?= htmlspecialchars($appt['client_phone']) ?></td>
                                                <td><?= htmlspecialchars($appt['service_name']) ?></td>
                                                <td><?= htmlspecialchars($appt['notes']) ?></td>
                                                <td>
                                                    <form method="POST" action="schedule.php" class="d-inline">
                                                        <input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
                                                        <button type="submit" name="complete" class="btn btn-sm btn-outline-success mb-1">Complete</button>
                                                    </form>
                                                    <form method="POST" action="schedule.php" class="d-inline">
                                                        <input type="hidden" name="appointment_id" value="<?= $appt['id']; ?>">
                                                        <button type="submit" name="cancel" class="btn btn-sm btn-outline-danger mb-1">Cancel</button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endwhile; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="6" class="text-center text-muted">You have no upcoming appointments in your schedule.</td>
                                        </tr>
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
$stmt->close();
$conn->close();
include_once('../includes/footer.php');
?>