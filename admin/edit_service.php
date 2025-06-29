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
$page_mode = 'Add New';

// --- CHECK IF IN EDIT MODE ---
if (isset($_GET['id'])) {
    $service_id = intval($_GET['id']);
    $page_mode = 'Edit';
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
}

// --- HANDLE FORM SUBMISSION (FOR BOTH ADD AND EDIT) ---
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $service_name = trim($_POST['name']);
    $service_description = trim($_POST['description']);
    $service_duration = intval($_POST['duration_minutes']);
    $service_price = floatval($_POST['price']);
    $service_id_post = $_POST['id'] ?? null;

    if (!empty($service_id_post)) {
        // --- UPDATE an existing service ---
        $sql = "UPDATE services SET name = ?, description = ?, duration_minutes = ?, price = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssidi", $service_name, $service_description, $service_duration, $service_price, $service_id_post);
        if ($stmt->execute()) {
            header("Location: manage_services.php?status=updated");
            exit();
        }
    } else {
        // --- ADD a new service ---
        $sql = "INSERT INTO services (name, description, duration_minutes, price) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssid", $service_name, $service_description, $service_duration, $service_price);
        if ($stmt->execute()) {
            header("Location: manage_services.php?status=added");
            exit();
        }
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
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <form action="edit_service.php<?php if ($service_id) echo '?id=' . $service_id; ?>" method="POST">
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
                        <div class="d-flex justify-content-end">
                            <a href="manage_services.php" class="btn btn-secondary me-2">Cancel</a>
                            <button type="submit" class="btn btn-accent"><?php echo $page_mode; ?> Service</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</main>
<?php
$conn->close();
include_once('../includes/footer.php');
?>