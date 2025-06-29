<?php
define('BASE_URL', '/greenlife_wellness/');
$page_title = 'About Us | GreenLife Wellness Center';
include_once('includes/db_connect.php'); // Include DB connection



include_once('includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>About GreenLife Wellness</h1>
        <p class="lead">Discover our story, our philosophy, and the team dedicated to your well-being.</p>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6 mb-4 mb-lg-0 d-flex align-items-center justify-content-center" style="min-height: 350px; max-height: 800px; overflow: hidden;">
                <img src="https://plus.unsplash.com/premium_photo-1674841252391-efd5fe8266d5?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MXx8QSUyMGRvY3RvciUyMGNvbnN1bHRpbmclMjB3aXRoJTIwYSUyMHBhdGllbnR8ZW58MHwxfDB8fHww" alt="A doctor consulting with a patient" class="img-fluid rounded-3 shadow" style="height: 100%; object-fit: cover; min-height: 250px; max-height: 700px;">
            </div>
            <div class="col-lg-6">
                <p class="mb-4">GreenLife Wellness was founded with a simple mission: to create a sanctuary where individuals can find holistic healing, balance, and rejuvenation. Our journey began with a passion for blending ancient wisdom with modern science, offering a unique approach to wellness that nurtures the mind, body, and spirit.</p>
                <h2 class="section-title">Our Philosophy</h2>
                <p class="mb-4">We believe that true wellness is more than the absence of illness—it's a vibrant state of being. At GreenLife, we focus on preventive care, personalized therapies, and empowering our clients to take charge of their health. Our integrated services include Ayurveda, yoga, nutrition, physiotherapy, and more, all delivered by a compassionate team of experts.</p>
                <h2 class="section-title">Meet Our Team</h2>
                <p class="mb-4">Our team is made up of experienced therapists, certified nutritionists, yoga instructors, and medical professionals who are dedicated to your well-being. We work together to create customized wellness plans and provide ongoing support, ensuring every client feels heard, valued, and cared for.</p>
                <h2 class="section-title">Why Choose Us?</h2>
                <ul>
                    <li>Comprehensive, integrated wellness services</li>
                    <li>Personalized care and attention</li>
                    <li>Evidence-based therapies and traditional wisdom</li>
                    <li>Warm, welcoming environment</li>
                    <li>Commitment to your long-term health and happiness</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<?php

include_once('includes/footer.php');
?>