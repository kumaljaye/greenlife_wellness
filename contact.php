<?php
    // Define a constant for the base URL
    define('BASE_URL', '/greenlife_wellness/');
    
    // Set the page title
    $page_title = 'Contact Us | GreenLife Wellness Center';

    // Include the header - using include_once is a best practice
    include_once('includes/header.php');
?>

<?php
// Check if user is logged in
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
$logged_in = isset($_SESSION['user_id']);
?>

<header class="page-header">
    <div class="container">
        <h1>Get In Touch</h1>
        <p class="lead">We're here to answer your questions and help you start your wellness journey.</p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-5 mb-5 mb-lg-0">
                <div class="contact-info-box">
                    <h3 class="mb-4">Contact Information</h3>
                    <div class="contact-info-item">
                        <i class="bi bi-geo-alt-fill icon"></i>
                        <p>GreenLife Wellness Center<br>123 Galle Road, Colombo 03<br>Sri Lanka</p>
                    </div>
                    <div class="contact-info-item">
                        <i class="bi bi-telephone-fill icon"></i>
                        <p>+94 11 234 5678</p>
                    </div>
                    <div class="contact-info-item">
                        <i class="bi bi-envelope-fill icon"></i>
                        <p>info@greenlifewellness.lk</p>
                    </div>
                    <hr class="my-4">
                    <h5 class="mb-3">Opening Hours</h5>
                    <div class="contact-info-item">
                        <i class="bi bi-clock-fill icon"></i>
                        <p>Monday - Friday: 9:00 AM - 7:00 PM<br>Saturday: 10:00 AM - 5:00 PM<br>Sunday: Closed</p>
                    </div>
                </div>

            </div>

            <div class="col-lg-7">
                <div class="contact-form-box">
                    <h3>Send Us a Message</h3>
                    <p class="text-body-secondary">Please fill out the form below, and our team will get back to you shortly.</p>

                    <?php if (isset($_GET['status'])): ?>
                        <?php if ($_GET['status'] == 'success'): ?>
                            <div class="alert alert-success mt-3">
                                Thank you! Your message has been sent successfully.
                            </div>
                        <?php else: ?>
                            <div class="alert alert-danger mt-3">
                                Sorry, there was an error sending your message. Please try again.
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                    
                    <form action="includes/form_handler.php" method="POST" class="mt-4" id="contactForm">
                        <div class="mb-3">
                            <label for="fullName" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="fullName" name="fullName" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email Address</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subject</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Your Message</label>
                            <textarea class="form-control" id="message" name="message" rows="6" required></textarea>
                        </div>
                        <button type="submit" class="btn btn-accent btn-lg w-100">Send Message</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var contactForm = document.getElementById('contactForm');
        if (contactForm) {
            contactForm.addEventListener('submit', function(e) {
                <?php if (!isset($_SESSION['user_id'])): ?>
                    e.preventDefault();
                    // Show notification only, do not redirect
                    let notif = document.createElement('div');
                    notif.className = 'alert alert-warning mt-3';
                    notif.innerHTML = 'You must be logged in to send a message.';
                    // Remove any previous notification
                    let prev = document.querySelector('.alert.alert-warning');
                    if (prev) prev.remove();
                    contactForm.parentNode.insertBefore(notif, contactForm);
                <?php endif; ?>
            });
        }
    });
</script>

<?php
    // Include the footer - using include_once is a best practice
    include_once('includes/footer.php');
?>