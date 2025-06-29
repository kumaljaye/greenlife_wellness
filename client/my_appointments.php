<?php
// Define constants and check session
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'client';
include_once('../includes/session_check.php');
$page_title = 'My Appointments | GreenLife Wellness Center';

// Include DB connection
include_once('../includes/db_connect.php');

$client_id = $_SESSION['user_id'];

// --- BACKEND LOGIC: HANDLE CANCELLATION (POST REQUEST) ---
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cancel_appointment_id'])) {
    $appointment_to_cancel = intval($_POST['cancel_appointment_id']);

    // Security Check: Verify the appointment belongs to the logged-in user before cancelling
    $stmt_verify = $conn->prepare("SELECT id FROM appointments WHERE id = ? AND client_id = ?");
    $stmt_verify->bind_param("ii", $appointment_to_cancel, $client_id);
    $stmt_verify->execute();
    $result_verify = $stmt_verify->get_result();

    if ($result_verify->num_rows === 1) {
        // Verification successful, proceed with update
        $stmt_cancel = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
        $stmt_cancel->bind_param("i", $appointment_to_cancel);
        if ($stmt_cancel->execute()) {
            // Redirect to prevent form resubmission on refresh
            header("Location: my_appointments.php?status=cancelled");
            exit();
        }
        $stmt_cancel->close();
    }
    $stmt_verify->close();
}

// --- FRONTEND DATA: FETCH APPOINTMENTS (GET REQUEST) ---
$sql_upcoming = "SELECT a.id, a.appointment_datetime, a.status, a.service_id, s.name AS service_name, u.full_name AS therapist_name, u.id AS therapist_id
                 FROM appointments a
                 JOIN services s ON a.service_id = s.id
                 JOIN users u ON a.therapist_id = u.id
                 WHERE a.client_id = ? AND a.appointment_datetime >= NOW() AND a.status = 'scheduled'
                 ORDER BY a.appointment_datetime ASC";

$stmt_upcoming = $conn->prepare($sql_upcoming);
$stmt_upcoming->bind_param("i", $client_id);
$stmt_upcoming->execute();
$upcoming_appointments_result = $stmt_upcoming->get_result();

$sql_past = "SELECT a.id, a.appointment_datetime, a.status, a.service_id, s.name AS service_name, u.full_name AS therapist_name, u.id AS therapist_id
             FROM appointments a
             JOIN services s ON a.service_id = s.id
             JOIN users u ON a.therapist_id = u.id
             WHERE a.client_id = ? AND (a.appointment_datetime < NOW() OR a.status != 'scheduled')
             ORDER BY a.appointment_datetime DESC";

$stmt_past = $conn->prepare($sql_past);
$stmt_past->bind_param("i", $client_id);
$stmt_past->execute();
$past_appointments_result = $stmt_past->get_result();


// --- FRONTEND DISPLAY ---
include_once('../includes/header.php');
?>
<style>
    .appointment-card {
        
        border-radius: 0.5rem;
        box-shadow: 0 1px 6px rgba(30,60,114,0.08);
        padding: 1.25rem 1rem;
        margin-bottom: 1rem;
        border: 1px solid #181c2f;
        text-align: left;
    }
    .appointment-card .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: #ffe082;
    }
    .appointment-card .card-subtitle {
        font-size: 1rem;
        color: #7be495;
    }
    .appointment-card .card-text {
        font-size: 0.98rem;
        color: #e0e0e0;
    }
    .appointment-card .btn {
        min-width: 100px;
    }
    .section-title {
        color: #ffe082;
        font-weight: 600;
        letter-spacing: 0.5px;
    }
    .upcoming-appointments-row {
        justify-content: flex-start;
    }
</style>

<header class="page-header">
    <div class="container">
        <h1>My Appointments</h1>
        <p class="lead">View your appointment history and manage upcoming sessions.</p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <div class="col-md-9">
                <?php if (isset($_GET['status']) && $_GET['status'] == 'cancelled'): ?>
                    <div class="alert alert-success" role="alert">
                        Your appointment has been successfully cancelled.
                    </div>
                <?php endif; ?>

                <h2 class="section-title mb-4">Upcoming Appointments</h2>
                <div class="row upcoming-appointments-row">
                    <?php if ($upcoming_appointments_result->num_rows > 0): ?>
                        <?php while ($appt = $upcoming_appointments_result->fetch_assoc()): ?>
                            <div class="col-lg-6 mb-4">
                                <div class="appointment-card upcoming">
                                    <div class="card-body">
                                        <h5 class="card-title"><?php echo htmlspecialchars($appt['service_name']); ?></h5>
                                        <p class="card-subtitle mb-2">with <?php echo htmlspecialchars($appt['therapist_name']); ?></p>
                                        <hr>
                                        <p class="card-text"><i class="bi bi-calendar-check me-2"></i><strong>Date:</strong> <?php echo date('l, F j, Y', strtotime($appt['appointment_datetime'])); ?></p>
                                        <p class="card-text"><i class="bi bi-clock-fill me-2"></i><strong>Time:</strong> <?php echo date('g:i A', strtotime($appt['appointment_datetime'])); ?></p>
                                        <div class="d-flex mt-3">
                                            <form method="POST" action="my_appointments.php" onsubmit="return confirm('Are you sure you want to cancel this appointment?');" class="me-2">
                                                <input type="hidden" name="cancel_appointment_id" value="<?php echo $appt['id']; ?>">
                                                <button type="submit" class="btn btn-sm btn-outline-danger">Cancel</button>
                                            </form>
                                            <a href="view_conversation.php?therapist_id=<?php echo $appt['therapist_id']; ?>&service_id=<?php echo $appt['service_id']; ?>" class="btn btn-sm btn-outline-info">Message Therapist</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <div class="col">
                            <p class="text-body-secondary">You have no upcoming appointments. <a href="book_appointment.php" class="footer-link">Book one now!</a></p>
                        </div>
                    <?php endif; ?>
                </div>

                <hr class="my-5">

                <h2 class="section-title mb-4">Past & Cancelled Appointments</h2>
                <div class="table-responsive">
                    <table class="table table-dark table-hover align-middle rounded">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Service</th>
                                <th>Therapist</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                        <?php if ($past_appointments_result->num_rows > 0): ?>
                            <?php while ($appt = $past_appointments_result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo date('l, F j, Y', strtotime($appt['appointment_datetime'])); ?></td>
                                    <td><?php echo htmlspecialchars($appt['service_name']); ?></td>
                                    <td><?php echo htmlspecialchars($appt['therapist_name']); ?></td>
                                    <td>
                                        <span class="badge <?php 
                                            switch($appt['status']) {
                                                case 'completed': echo 'bg-success'; break;
                                                case 'cancelled': echo 'bg-danger'; break;
                                                default: echo 'bg-secondary';
                                            }
                                        ?>">
                                            <?php echo ucfirst($appt['status']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <a href="view_conversation.php?therapist_id=<?php echo $appt['therapist_id']; ?>&service_id=<?php echo $appt['service_id']; ?>" class="btn btn-sm btn-outline-info">Message Therapist</a>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <tr><td colspan="5" class="text-center text-muted">You have no past appointment history.</td></tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</main>

<?php
$stmt_upcoming->close();
$stmt_past->close();
$conn->close();
include_once('../includes/footer.php');
?>