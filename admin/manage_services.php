<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'admin';
include_once('../includes/session_check.php');
include_once('../includes/db_connect.php');

$service_id = null;
$service_name = '';
$service_description = '';
$service_duration = 60;
$service_price = '';
$assigned_therapists = []; // Array to hold IDs of therapists assigned to this service
$page_mode = 'Add New';

// --- FETCH ALL AVAILABLE THERAPISTS FOR THE DROPDOWN ---
$therapists_result = $conn->query("SELECT id, full_name FROM users WHERE role = 'therapist' ORDER BY full_name ASC");

// --- CHECK IF IN EDIT MODE ---
if (isset($_GET['id']) && !empty($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $page_mode = 'Edit';

    // Fetch the service details
    $stmt = $conn->prepare("SELECT * FROM services WHERE id = ?");
    $stmt->bind_param("i", $service_id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $service = $result->fetch_assoc();
        $service_name = $service['name'];
        $service_description = $service['description'];
        $service_duration = $service['duration_minutes'];
        $service_price = $service['price'];
    }
    $stmt->close();

    // Fetch the currently assigned therapists for this service to pre-select them
    $stmt_assigned = $conn->prepare("SELECT therapist_id FROM therapist_services WHERE service_id = ?");
    $stmt_assigned->bind_param("i", $service_id);
    $stmt_assigned->execute();
    $assigned_result = $stmt_assigned->get_result();
    while ($row = $assigned_result->fetch_assoc()) {
        $assigned_therapists[] = $row['therapist_id'];
    }
    $stmt_assigned->close();
}

// --- HANDLE FORM SUBMISSION (FOR BOTH ADD AND EDIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Get service details from form
    $service_name = trim($_POST['name']);
    $service_description = trim($_POST['description']);
    $service_duration = intval($_POST['duration_minutes']);
    $service_price = floatval($_POST['price']);
    $service_id_post = $_POST['id'] ?? null;
    $selected_therapists = $_POST['therapists'] ?? []; // This will be an array

    // Use a transaction to ensure data integrity
    $conn->begin_transaction();
    try {
        if (!empty($service_id_post)) {
            // --- UPDATE an existing service ---
            $service_id = $service_id_post;
            $sql = "UPDATE services SET name = ?, description = ?, duration_minutes = ?, price = ? WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssidi", $service_name, $service_description, $service_duration, $service_price, $service_id);
            $stmt->execute();
        } else {
            // --- ADD a new service ---
            $sql = "INSERT INTO services (name, description, duration_minutes, price) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssid", $service_name, $service_description, $service_duration, $service_price);
            $stmt->execute();
            $service_id = $conn->insert_id; // Get the ID of the new service
        }

        // --- UPDATE THERAPIST ASSIGNMENTS ---
        // 1. First, remove all existing assignments for this service
        $stmt_delete = $conn->prepare("DELETE FROM therapist_services WHERE service_id = ?");
        $stmt_delete->bind_param("i", $service_id);
        $stmt_delete->execute();

        // 2. Then, insert the new assignments
        if (!empty($selected_therapists)) {
            $stmt_insert = $conn->prepare("INSERT INTO therapist_services (service_id, therapist_id) VALUES (?, ?)");
            foreach ($selected_therapists as $therapist_id) {
                $stmt_insert->bind_param("ii", $service_id, $therapist_id);
                $stmt_insert->execute();
            }
        }
        
        // If everything was successful, commit the changes
        $conn->commit();
        header("Location: manage_services.php?status=" . (empty($service_id_post) ? 'added' : 'updated'));
        exit();

    } catch (Exception $e) {
        // If anything failed, roll back the changes
        $conn->rollback();
        $error_message = "An error occurred while saving. Please try again.";
        // For debugging: error_log($e->getMessage());
    }
}

$page_title = $page_mode . ' Service | Admin';
include_once('../includes/header.php');
?>

<header class="page-header">
    <div class="container"><h1><?php echo $page_mode; ?> Service</h1></div>
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
                <div class="card mb-4">
                    <?php if (isset($_GET['status']) && $_GET['status'] === 'added'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Service added successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php elseif (isset($_GET['status']) && $_GET['status'] === 'updated'): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            Service updated successfully!
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0"><?php echo $page_mode; ?> Service</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-container">
                            <form action="manage_services.php<?php if ($service_id) echo '?id=' . $service_id; ?>" method="POST">
                                <input type="hidden" name="id" value="<?php echo $service_id; ?>">
                                <div class="mb-3">
                                    <label for="name" class="form-label">Service Name</label>
                                    <input type="text" class="form-control" id="name" name="name" value="<?php echo htmlspecialchars($service_name); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label for="description" class="form-label">Description</label>
                                    <textarea class="form-control" id="description" name="description" rows="5" required><?php echo htmlspecialchars($service_description); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label for="duration_minutes" class="form-label">Duration (in minutes)</label>
                                        <input type="number" class="form-control" id="duration_minutes" name="duration_minutes" value="<?php echo $service_duration; ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label for="price" class="form-label">Price (LKR)</label>
                                        <input type="number" step="0.01" class="form-control" id="price" name="price" value="<?php echo $service_price; ?>" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Assign Therapists</label>
                                    <div class="">
                                        <?php if ($therapists_result->num_rows > 0): ?>
                                            <?php while ($therapist = $therapists_result->fetch_assoc()): ?>
                                                <div class="col-md-6 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" name="therapists[]" id="therapist_<?php echo $therapist['id']; ?>" value="<?php echo $therapist['id']; ?>" <?php if (in_array($therapist['id'], $assigned_therapists)) echo 'checked'; ?>>
                                                        <label class="form-check-label" for="therapist_<?php echo $therapist['id']; ?>">
                                                            <?php echo htmlspecialchars($therapist['full_name']); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <div class="col-12 text-body-secondary">No therapists found.</div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="form-text">Check all therapists who can provide this service.</div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="manage_services.php" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-accent"><?php echo $page_mode; ?> Service</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$conn->close();
include_once('../includes/footer.php');
?>