<?php
// Define constants and check session
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'client';
include_once('../includes/session_check.php');
$page_title = 'Book Appointment | GreenLife Wellness Center';

// Include DB connection
include_once('../includes/db_connect.php');

$error_message = '';
$success_message = '';

// --- PART 1: HANDLE THE FINAL BOOKING SUBMISSION (POST request) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $client_id = $_SESSION['user_id'];
    $service_id = $_POST['service_id'] ?? null;
    $therapist_id = $_POST['therapist_id'] ?? null;
    $appointment_date = $_POST['appointment_date'] ?? null;
    $appointment_time = $_POST['appointment_time'] ?? null;
    $note = $_POST['note'] ?? '';

    if (empty($service_id) || empty($therapist_id) || empty($appointment_date) || empty($appointment_time)) {
        $error_message = "There was an error. Please fill out all fields.";
    } else {
        // Combine date and time into a single format for the database
        $mysql_datetime = $appointment_date . ' ' . $appointment_time;

        // Insert the new appointment into the database
        $sql_insert = "INSERT INTO appointments (client_id, therapist_id, service_id, appointment_datetime, status, notes) VALUES (?, ?, ?, ?, 'scheduled', ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        $stmt_insert->bind_param("iiiss", $client_id, $therapist_id, $service_id, $mysql_datetime, $note);

        if ($stmt_insert->execute()) {
            $success_message = "Your appointment has been successfully booked!";
        } else {
            $error_message = "There was an error saving your appointment.";
        }
        $stmt_insert->close();
    }
}


// --- PART 2: HANDLE THE THERAPIST SEARCH (GET request) ---
$selected_service_id = $_GET['service_id'] ?? null;
$therapists = [];

if ($selected_service_id) {
    // If a service was selected, find the therapists for it
    $stmt_therapists = $conn->prepare("SELECT u.id, u.full_name FROM users u JOIN therapist_services ts ON u.id = ts.therapist_id WHERE ts.service_id = ?");
    $stmt_therapists->bind_param("i", $selected_service_id);
    $stmt_therapists->execute();
    $therapists_result = $stmt_therapists->get_result();
    while ($row = $therapists_result->fetch_assoc()) {
        $therapists[] = $row;
    }
    $stmt_therapists->close();
}

// Fetch service details
$service_details = null;
if ($selected_service_id) {
    $stmt_service = $conn->prepare("SELECT name, description, duration_minutes, price FROM services WHERE id = ?");
    $stmt_service->bind_param("i", $selected_service_id);
    $stmt_service->execute();
    $service_details = $stmt_service->get_result()->fetch_assoc();
    $stmt_service->close();
}

// Always get the list of all services for the first dropdown
$services_result = $conn->query("SELECT id, name FROM services ORDER BY name ASC");


// --- HTML DISPLAY ---
include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>Book an Appointment</h1>
        <p class="lead">Follow the steps below to schedule your session.</p>
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
                <div class="form-container">
                    <?php if ($success_message): ?><div class="alert alert-success"><?php echo $success_message; ?></div><?php endif; ?>
                    <?php if ($error_message): ?><div class="alert alert-danger"><?php echo $error_message; ?></div><?php endif; ?>

                    <form action="book_appointment.php" method="GET">
                        <div class="mb-3">
                            <label for="service" class="form-label fs-5"><span class="text-brand"></span> Choose a Service <span style="color:red">*</span></label>
                            <select class="form-select" id="service" name="service_id" required>
                                <option value="" disabled <?php if (!$selected_service_id) echo 'selected'; ?>>Select a service...</option>
                                <?php while ($service = $services_result->fetch_assoc()): ?>
                                    <option value="<?php echo $service['id']; ?>" <?php if ($service['id'] == $selected_service_id) echo 'selected'; ?>>
                                        <?php echo htmlspecialchars($service['name']); ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-outline-accent">Find Available Therapists</button>
                    </form>

                    <br><br>
                    <?php if ($selected_service_id && $service_details): ?>
                        <div class="card mb-4 shadow service-details-card" style="border-radius: 1rem; border: 2px; background: #181c2f; color: #fff;">
                            <div class="card-header" style="border-top-left-radius: 1rem; border-top-right-radius: 1rem;  color:rgb(252, 252, 255); font-weight: 600; letter-spacing: 0.5px;">
                                <i class="bi bi-info-circle me-2"></i>Service Details
                            </div>
                            <div class="card-body">
                                <h5 class="card-title mb-2" style=" font-weight: 600; letter-spacing: 0.5px;">
                                    <?php echo htmlspecialchars($service_details['name']); ?>
                                </h5>
                                <p class="mb-3" style="font-size: 1.08em; color: #e0e0e0;">
                                    <?php echo nl2br(htmlspecialchars($service_details['description'])); ?>
                                </p>
                                <ul class="list-unstyled mb-0">
                                    <li class="mb-2"><span class="badge bg-success bg-opacity-75 text-dark me-2" style="font-size:1em;"><i class="bi bi-clock me-1"></i>Duration</span> <span style="color:#7be495;"><strong><?php echo (int)$service_details['duration_minutes']; ?> minutes</strong></span></li>
                                    <li><span class="badge bg-accent bg-opacity-75 text-dark me-2" style="font-size:1em;"><i class="bi bi-cash-coin me-1"></i>Price</span> <span style="color:#ffe082;"><strong>Rs. <?php echo number_format($service_details['price'], 2); ?></strong></span></li>
                                </ul>
                            </div>
                        </div>
                    <?php endif; ?>
                    
                    <hr class="my-4">

                    <?php if ($selected_service_id): ?>
                        <form action="book_appointment.php" method="POST">
                            <input type="hidden" name="service_id" value="<?php echo $selected_service_id; ?>">
                            <div class="mb-3">
                                <label for="therapist" class="form-label fs-5"><span class="text-brand"></span> Select Your Therapist <span style="color:red">*</span></label>
                                <?php if (!empty($therapists)): ?>
                                    <select class="form-select" id="therapist" name="therapist_id" required>
                                        <option value="" disabled selected>Select a therapist...</option>
                                        <?php foreach ($therapists as $therapist): ?>
                                            <option value="<?php echo $therapist['id']; ?>"><?php echo htmlspecialchars($therapist['full_name']); ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <div class="alert alert-warning">No therapists are assigned to this service.</div>
                                <?php endif; ?>
                            </div>
                            <?php if (!empty($therapists)): ?>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="date" class="form-label fs-5"><span class="text-brand"></span> Select a Date <span style="color:red">*</span></label>
                                        <input type="date" class="form-control" id="date" name="appointment_date" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="time" class="form-label fs-5"><span class="text-brand"></span> Select a Time <span style="color:red">*</span></label>
                                        <input type="time" class="form-control" id="time" name="appointment_time" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label for="note" class="form-label">Note (optional)</label>
                                    <textarea class="form-control" id="note" name="note" rows="2" placeholder="Add any special requests or notes for your therapist..."></textarea>
                                </div>
                                <button type="submit" class="btn btn-accent btn-lg w-100">Book Appointment</button>
                            <?php endif; ?>
                        </form>
                    <?php endif; ?>

                </div>
            </div>
        </div>
    </div>
</main>
<?php
$conn->close();
include_once('../includes/footer.php');
?>