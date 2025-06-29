<?php
define('BASE_URL', '/greenlife_wellness/');
$required_role = 'admin';
include_once('../includes/session_check.php');
$page_title = 'Add Blog Post | Admin';
include_once('../includes/db_connect.php');

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $content = trim($_POST['content']);
    $image_path = '';

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'gif'];
        if (in_array($ext, $allowed)) {
            $target_dir = '../uploads/blog_images/';
            if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);
            $filename = uniqid('blog_', true) . '.' . $ext;
            $target_file = $target_dir . $filename;
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                $image_path = 'uploads/blog_images/' . $filename;
            } else {
                $message = '<div class="alert alert-danger">Image upload failed.</div>';
            }
        } else {
            $message = '<div class="alert alert-danger">Invalid image format. Allowed: jpg, jpeg, png, gif.</div>';
        }
    }

    if ($title && $content && !$message) {
        $stmt = $conn->prepare("INSERT INTO blog_posts (title, content, image_path, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $title, $content, $image_path);
        if ($stmt->execute()) {
            $message = '<div class="alert alert-success">Blog post added successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">Failed to add blog post.</div>';
        }
        $stmt->close();
    } elseif (!$message) {
        $message = '<div class="alert alert-danger">Please fill in all required fields.</div>';
    }
}

include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>Add Blog Post</h1>
        <p class="lead">Create a new blog post for the wellness blog.</p>
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
                <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="mb-0">Add Blog Post</h4>
                    </div>
                    <div class="card-body">
                        <div class="form-container">
                            <?php if ($message) echo $message; ?>
                            <form method="POST" enctype="multipart/form-data">
                                <div class="mb-3">
                                    <label for="title" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="title" name="title" required>
                                </div>
                                <div class="mb-3">
                                    <label for="content" class="form-label">Content</label>
                                    <textarea class="form-control" id="content" name="content" rows="7" required></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="image" class="form-label">Image (optional)</label>
                                    <input class="form-control" type="file" id="image" name="image" accept="image/*">
                                </div>
                                <div class="d-flex justify-content-end">
                                    <a href="../blog.php" class="btn btn-secondary me-2">Cancel</a>
                                    <button type="submit" class="btn btn-accent">Add Blog Post</button>
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
include_once('../includes/footer.php');
?>
