<footer class="pt-5 pb-4 footer-bg text-white">
    <div class="container text-center text-md-left">
        <div class="row">
            <!-- Brand and Description -->
            <div class="col-md-3 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-brand">GreenLife Wellness</h5>
                <p>Your sanctuary for holistic health and well-being in the heart of Colombo. Rejuvenate your mind, body, and soul.</p>
            </div>
            <!-- Quick Links -->
            <div class="col-md-3 col-lg-2 col-xl-2 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-brand">Quick Links</h5>
                <p><a href="<?php echo BASE_URL; ?>index.php" class="footer-link">Home</a></p>
                <p><a href="<?php echo BASE_URL; ?>services.php" class="footer-link">Services</a></p>
                <p><a href="<?php echo BASE_URL; ?>blog.php" class="footer-link">Blog</a></p>
                <p><a href="<?php echo BASE_URL; ?>about.php" class="footer-link">About Us</a></p>
                <p><a href="<?php echo BASE_URL; ?>contact.php" class="footer-link">Contact</a></p>
            </div>
            <!-- Contact Info -->
            <div class="col-md-4 col-lg-3 col-xl-3 mx-auto mt-3">
                <h5 class="text-uppercase mb-4 font-weight-bold text-brand">Contact</h5>
                <p><i class="bi bi-geo-alt-fill me-2"></i>Colombo, Sri Lanka</p>
                <p><i class="bi bi-envelope-fill me-2"></i>info@greenlifewellness.lk</p>
                <p><i class="bi bi-telephone-fill me-2"></i>+94 11 234 5678</p>
            </div>
        </div>
        <hr class="my-4" style="border-color: #7be495; opacity: 0.3;">
        <div class="row align-items-center">
            <div class="col-md-7 col-lg-8">
                <p class="mb-0"> © <?php echo date('Y'); ?> Copyright:
                    <a href="<?php echo BASE_URL; ?>index.php" style="text-decoration: none; color: #7be495;">
                        <strong class="text-brand">GreenLife Wellness Center</strong>
                    </a>
                </p>
            </div>
        </div>
    </div>
</footer>
<!-- Footer Custom Styles -->
<style>
.footer-bg {
    background: #0c0e1c; /* Solid black background */
    box-shadow: 0 -4px 32px rgba(24,28,47,0.12);
    border-top: 2px solid #222;
}
.text-brand {
    color: #fff !important;
    letter-spacing: 1px;
}
.footer-link {
    color: #ccc;
    text-decoration: none;
    transition: color 0.2s;
    font-weight: 500;
}
.footer-link:hover {
    color: #fff;
    text-decoration: underline;
}
.footer-bg h5 {
    font-size: 1.1rem;
    letter-spacing: 0.5px;
    color: #fff;
}
.footer-bg p, .footer-bg a {
    font-size: 1rem;
    color: #ccc;
}
.footer-bg p i {
    color: #888;
}
@media (max-width: 767px) {
    .footer-bg .row > div {
        margin-bottom: 2rem;
    }
}
</style>
<!-- Bootstrap Icons CDN for contact icons -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="<?php echo BASE_URL; ?>assets/js/script.js"></script>
</body>
</html>