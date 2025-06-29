<?php
// Therapist and admin can view all clients list
// Place this file in /client/view_clients.php

define('BASE_URL', '/greenlife_wellness/');
$allowed_roles = ['admin', 'therapist'];
include_once('../includes/session_check_multi_role.php');
$page_title = 'All Clients | GreenLife Wellness';
include_once('../includes/db_connect.php');

// Fetch all clients
$stmt = $conn->prepare("SELECT id, full_name, email, phone_number, created_at FROM users WHERE role = 'client' ORDER BY created_at DESC");
$stmt->execute();
$result = $stmt->get_result();

include_once('../includes/header.php');
?>
<header class="page-header">
    <div class="container">
        <h1>All Clients</h1>
        <p class="lead">Browse and view client profiles.</p>
    </div>
</header>
<main class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?php include('../includes/sidebar_nav.php'); ?>
            </div>
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header"><h4>Clients</h4></div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>Name</th>
                                        <th>Email</th>
                                        <th>Phone</th>
                                        <th>Registered</th>
                                        <th>Profile</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($client = $result->fetch_assoc()): ?>
                                        <tr>
                                            <td><?= htmlspecialchars($client['full_name']) ?></td>
                                            <td><?= htmlspecialchars($client['email']) ?></td>
                                            <td><?= htmlspecialchars($client['phone_number']) ?></td>
                                            <td><?= date('M j, Y', strtotime($client['created_at'])) ?></td>
                                            <td><a href="client_profile.php?id=<?= $client['id'] ?>" class="btn btn-sm btn-outline-accent">View Profile</a></td>
                                        </tr>
                                    <?php endwhile; ?>
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
