<?php
// Define constants and check session
define('BASE_URL', '/greenlife_wellness/');
$allowed_roles = ['admin', 'therapist'];
include_once('includes/session_check.php');
$page_title = 'Manage Appointments';
include_once('includes/db_connect.php');

// --- HANDLE APPOINTMENT ACTIONS (CANCEL/COMPLETE) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $appointment_id = intval($_POST['appointment_id'] ?? 0);
    if ($appointment_id > 0) {
        if (isset($_POST['cancel'])) {
            $stmt = $conn->prepare("UPDATE appointments SET status = 'cancelled' WHERE id = ?");
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $stmt->close();
        } elseif (isset($_POST['complete'])) {
            $stmt = $conn->prepare("UPDATE appointments SET status = 'completed' WHERE id = ?");
            $stmt->bind_param("i", $appointment_id);
            $stmt->execute();
            $stmt->close();
        }
    }
}

// --- FILTERING ---
$status_filter = $_GET['status'] ?? '';
$where_clauses = [];
$params = [];
$types = '';
if (in_array($status_filter, ['scheduled', 'completed', 'cancelled'])) {
    $where_clauses[] = "a.status = ?";
    $params[] = $status_filter;
    $types .= 's';
}
if ($_SESSION['user_role'] === 'therapist') {
    $where_clauses[] = "a.therapist_id = ?";
    $params[] = $_SESSION['user_id'];
    $types .= 'i';
}
$where_clause = '';
if (count($where_clauses) > 0) {
    $where_clause = 'WHERE ' . implode(' AND ', $where_clauses);
}

// --- FETCH APPOINTMENTS ---
// Scheduled appointments
$sql_scheduled = "SELECT
                        a.id,
                        a.appointment_datetime,
                        a.status,
                        a.notes,
                        c.full_name AS client_name,
                        t.full_name AS therapist_name,
                        s.name AS service_name
                    FROM
                        appointments a
                    JOIN users c ON a.client_id = c.id
                    JOIN users t ON a.therapist_id = t.id
                    JOIN services s ON a.service_id = s.id
                    WHERE a.status = 'scheduled'";
if ($_SESSION['user_role'] === 'therapist') {
    $sql_scheduled .= " AND a.therapist_id = ?";
    $params_scheduled = [$_SESSION['user_id']];
    $types_scheduled = 'i';
} else {
    $params_scheduled = [];
    $types_scheduled = '';
}
$sql_scheduled .= " ORDER BY a.appointment_datetime DESC";
$stmt_scheduled = $conn->prepare($sql_scheduled);
if (!empty($params_scheduled)) {
    $stmt_scheduled->bind_param($types_scheduled, ...$params_scheduled);
}
$stmt_scheduled->execute();
$scheduled_result = $stmt_scheduled->get_result();

// Completed & Cancelled appointments
$sql_other = "SELECT
                    a.id,
                    a.appointment_datetime,
                    a.status,
                    a.notes,
                    c.full_name AS client_name,
                    t.full_name AS therapist_name,
                    s.name AS service_name
                FROM
                    appointments a
                JOIN users c ON a.client_id = c.id
                JOIN users t ON a.therapist_id = t.id
                JOIN services s ON a.service_id = s.id
                WHERE (a.status = 'completed' OR a.status = 'cancelled')";
if ($_SESSION['user_role'] === 'therapist') {
    $sql_other .= " AND a.therapist_id = ?";
    $params_other = [$_SESSION['user_id']];
    $types_other = 'i';
} else {
    $params_other = [];
    $types_other = '';
}
$sql_other .= " ORDER BY a.appointment_datetime DESC";
$stmt_other = $conn->prepare($sql_other);
if (!empty($params_other)) {
    $stmt_other->bind_param($types_other, ...$params_other);
}
$stmt_other->execute();
$other_result = $stmt_other->get_result();

include_once('includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>Manage Appointments</h1>
        <p class="lead">View, filter, and manage all appointments.</p>
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
                <?php if (isset($_GET['status']) && $_GET['status'] == 'updated'): ?>
                    <div class="alert alert-success">Appointment updated successfully.</div>
                <?php endif; ?>
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Scheduled Appointments</h4>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Client</th>
                                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                        <th>Therapist</th>
                                        <?php endif; ?>
                                        <th>Service</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($scheduled_result->num_rows > 0): ?>
                                    <?php while ($appt = $scheduled_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('M j, Y g:i A', strtotime($appt['appointment_datetime'])) ?></td>
                                            <td><?= htmlspecialchars($appt['client_name']) ?></td>
                                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                            <td><?= htmlspecialchars($appt['therapist_name']) ?></td>
                                            <?php endif; ?>
                                            <td><?= htmlspecialchars($appt['service_name']) ?></td>
                                            <td><?= htmlspecialchars($appt['notes']) ?></td>
                                            <td>
                                                <span class="badge bg-primary">Scheduled</span>
                                            </td>
                                            <td style="min-width:180px;">
                                                <div class="row g-2 flex-nowrap align-items-stretch">
                                                    <div class="col h-100">
                                                        <form method="post" class="d-inline h-100">
                                                            <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                                            <button type="submit" name="complete" class="btn btn-sm btn-outline-success w-100 h-100">Mark Completed</button>
                                                        </form>
                                                    </div>
                                                    <div class="col h-100">
                                                        <form method="post" class="d-inline h-100">
                                                            <input type="hidden" name="appointment_id" value="<?= $appt['id'] ?>">
                                                            <button type="submit" name="cancel" class="btn btn-sm btn-outline-danger w-100 h-100">Cancel</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="<?= $_SESSION['user_role'] === 'admin' ? '7' : '6' ?>" class="text-center text-muted">No scheduled appointments found.</td></tr>
                                <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Completed and Cancelled Appointments</h4>
                        <form class="d-inline mb-0" method="get">
                            <div class="input-group input-group-sm">
                                <label for="status" class="input-group-text">Status</label>
                                <select name="status" id="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                    <option value="">All</option>
                                    <option value="completed" <?= $status_filter == 'completed' ? 'selected' : '' ?>>Completed</option>
                                    <option value="cancelled" <?= $status_filter == 'cancelled' ? 'selected' : '' ?>>Cancelled</option>
                                </select>
                            </div>
                        </form>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                    <tr>
                                        <th>Date & Time</th>
                                        <th>Client</th>
                                        <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                        <th>Therapist</th>
                                        <?php endif; ?>
                                        <th>Service</th>
                                        <th>Note</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php if ($other_result->num_rows > 0): ?>
                                    <?php while ($appt = $other_result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= date('M j, Y g:i A', strtotime($appt['appointment_datetime'])) ?></td>
                                            <td><?= htmlspecialchars($appt['client_name']) ?></td>
                                            <?php if ($_SESSION['user_role'] === 'admin'): ?>
                                            <td><?= htmlspecialchars($appt['therapist_name']) ?></td>
                                            <?php endif; ?>
                                            <td><?= htmlspecialchars($appt['service_name']) ?></td>
                                            <td><?= htmlspecialchars($appt['notes']) ?></td>
                                            <td>
                                                <span class="badge bg-<?php 
                                                    echo $appt['status'] == 'completed' ? 'success' : 'secondary'; ?>">
                                                    <?= ucfirst($appt['status']) ?>
                                                </span>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr><td colspan="<?= $_SESSION['user_role'] === 'admin' ? '6' : '5' ?>" class="text-center text-muted">No completed or cancelled appointments found.</td></tr>
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
$stmt_scheduled->close();
$stmt_other->close();
$conn->close();
include_once('includes/footer.php');
?>