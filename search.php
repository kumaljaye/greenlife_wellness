<?php
define('BASE_URL', '/greenlife_wellness/');
$page_title = 'Search Results'; // Will be updated below
include_once('includes/db_connect.php');

$search_query = '';
$search_results = [];
$results_count = 0;

// Check if a query was submitted
if (isset($_GET['query']) && !empty(trim($_GET['query']))) {
    $search_query = trim($_GET['query']);
    $page_title = 'Search Results for "' . htmlspecialchars($search_query) . '"';

    // Prepare the search term for a LIKE query by adding wildcards
    $search_term = "%" . $search_query . "%";

    // --- SEARCH QUERY ---
    // We search the 'name' AND 'description' columns for the term.
    $sql = "SELECT * FROM services WHERE name LIKE ? OR description LIKE ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ss", $search_term, $search_term);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $results_count = $result->num_rows;
    while ($row = $result->fetch_assoc()) {
        $search_results[] = $row;
    }
    $stmt->close();
} else {
    // Redirect to homepage if no query is entered
    header("Location: " . BASE_URL . "index.php");
    exit();
}

include_once('includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1><?php echo $page_title; ?></h1>
        <p class="lead">Found <?php echo $results_count; ?> matching services.</p>
    </div>
</header>

<main class="py-5">
    <div class="container">
        <div class="row">
            <?php if ($results_count > 0): ?>
                <?php foreach ($search_results as $service): ?>
                    <div class="col-lg-4 col-md-6 mb-4">
                        <div class="card service-card h-100">
                            <div class="card-body d-flex flex-column">
                                <h5 class="card-title"><?php echo htmlspecialchars($service['name']); ?></h5>
                                <p class="card-text text-body-secondary">
                                    <?php 
                                        // Display a snippet of the description
                                        echo htmlspecialchars(substr($service['description'], 0, 100)) . '...'; 
                                    ?>
                                </p>
                                <a href="services.php#service-<?php echo $service['id']; ?>" class="btn btn-outline-accent mt-auto">View Details</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col text-center">
                    <p class="text-body-secondary fs-4">Sorry, no services were found matching your search.</p>
                    <p>Try searching for a different term or <a href="services.php" class="footer-link">view all our services</a>.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php
$conn->close();
include_once('includes/footer.php');
?>