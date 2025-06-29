<?php
// Sidebar navigation box for role-based navigation
if (!isset($_SESSION)) session_start();
$user_role = $_SESSION['user_role'] ?? '';
?>
<div class="card h-100 mb-4 shadow sidebar-nav-card" style="border-radius: 1rem; background: #181c2f; color: #fff;">
    <div class="card-body p-4">
        <div class="mb-4 text-center">
            
           
        </div>
        <ul class="nav flex-column sidebar-nav-list text-center">
            <li class="nav-item mb-4">
                <a class="nav-link text-white fw-semibold d-flex align-items-center justify-content-center" style="border-radius: 0.5rem; background: rgba(255,255,255,0.08);" href="<?php echo BASE_URL; ?>dashboard.php">
                    <i class="bi bi-house-door me-2"></i> Home
                </a>
            </li>
            <?php if ($user_role === 'admin'): ?>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>manage_appointments.php"><i class="bi bi-calendar-check me-2"></i>Manage Appointments</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>admin/manage_users.php"><i class="bi bi-people me-2"></i>Manage Users</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>admin/manage_services.php"><i class="bi bi-heart-pulse me-2"></i>Manage Services</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>admin/add_blog.php"><i class="bi bi-pencil-square me-2"></i>Manage Blog Posts</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>manage_inquiries.php"><i class="bi bi-envelope-open me-2"></i>Manage Inquiries</a></li>
            <?php elseif ($user_role === 'client'): ?>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>client/my_appointments.php"><i class="bi bi-calendar-event me-2"></i>My Appointments</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>client/book_appointment.php"><i class="bi bi-calendar-plus me-2"></i>Book Appointment</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>client/my_messages.php"><i class="bi bi-chat-dots me-2"></i>My Inquiries</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
            <?php elseif ($user_role === 'therapist'): ?>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>manage_appointments.php"><i class="bi bi-calendar-check me-2"></i>Manage Appointments</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>therapist/my_messages.php"><i class="bi bi-chat-left-text me-2"></i>Client Messages</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>manage_inquiries.php"><i class="bi bi-envelope-open me-2"></i>Inquiries</a></li>
                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>client/view_clients.php"><i class="bi bi-people me-2"></i>View Clients</a></li>
                                <li class="nav-item mb-4"><a class="nav-link text-white d-flex align-items-center justify-content-center" href="<?php echo BASE_URL; ?>profile.php"><i class="bi bi-person me-2"></i>My Profile</a></li>
            <?php endif; ?>
        </ul>
    </div>
</div>
<style>
.sidebar-nav-card {
    box-shadow: 0 4px 24px rgba(30,60,114,0.15), 0 1.5px 6px rgba(46,139,87,0.10);
    background: #181c2f !important;
}
.sidebar-nav-list .nav-link:hover {
    background: rgba(255,255,255,0.18) !important;
    color: #ffe082 !important;
    text-decoration: none;
}
</style>
