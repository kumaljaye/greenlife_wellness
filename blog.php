<?php
define('BASE_URL', '/greenlife_wellness/');
$page_title = 'Wellness Blog | GreenLife Wellness Center';
include_once('includes/header.php');
include_once('includes/db_connect.php');

// Fetch blog posts from the database
$posts = [];
$sql = "SELECT id, title, content, image_path, created_at FROM blog_posts ORDER BY created_at DESC";
if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = $row;
    }
    $result->free();
}
?>
<style>
/* Blog page: Remove green borders and make images full-width */
.card.service-card, .card.service-card img.card-img-top {
    border: none !important;
    box-shadow: none !important;
}
.card.service-card img.card-img-top {
    width: 100%;
    display: block;
    border-radius: 0;
    margin: 0;
    padding: 0;
}
</style>
<header class="page-header">
    <div class="container">
        <h1>GreenLife Wellness Blog</h1>
        <p class="lead">Insights, tips, and inspiration for your holistic health journey.</p>
    </div>
</header>
<main>
    <section class="service-detail-section services-bg py-5">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8">
                    <?php if (empty($posts)): ?>
                        <div class="card mb-4">
                            <div class="card-body">
                                <h3 class="card-title">Welcome to Our Blog!</h3>
                                <p class="card-text">Stay tuned for expert articles on Ayurveda, yoga, nutrition, physiotherapy, and more. Our team will share wellness tips, healthy recipes, and stories to inspire your path to well-being.</p>
                                <p class="text-muted">(No blog posts yet. Check back soon!)</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($posts as $post): ?>
                            <div class="card mb-5">
                                <?php if (!empty($post['image_path'])): ?>
                                    <img src="<?php echo htmlspecialchars($post['image_path']); ?>" class="card-img-top" alt="Blog Image" style="max-height:420px;width:100%;object-fit:cover;border-radius:0;">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h2 class="section-title mb-2"><?php echo htmlspecialchars($post['title']); ?></h2>
                                    <p class="card-text mb-3"><?php echo nl2br(htmlspecialchars($post['content'])); ?></p>
                                    <p class="text-end text-muted mb-0">Posted on <?php echo date('F j, Y', strtotime($post['created_at'])); ?></p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </section>
</main>
<?php
include_once('includes/footer.php');
?>
