<?php
    // Define a constant for the base URL
    define('BASE_URL', '/greenlife_wellness/');
    
    // Set the page title
    $page_title = 'Welcome to GreenLife | Holistic Wellness Center';

    // Include the header
    include('includes/header.php');
?>

<header class="hero-section text-white text-center">
    <div class="hero-content">
        <h1 class="display-3">A Sanctuary for Your Soul</h1>
        <p class="lead">Experience profound tranquility and holistic healing at GreenLife, Colombo's premier wellness center.</p>
        <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-accent btn-lg mt-3">Explore Our Therapies</a>
    </div>
</header>

<section class="py-5">
    <div class="container text-center" style="max-width: 800px;">
        <h2 class="section-title">Embrace Holistic Living</h2>
        <p class="lead text-body-secondary mb-5">We believe true wellness is a harmonious balance of mind, body, and spirit. Our integrated approach blends ancient wisdom with modern therapies to guide you on your personal journey to optimal health.</p>
    </div>
</section>

<section class="py-5 services-bg">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Signature Services</h2>
            <p class="text-body-secondary">Curated experiences designed to restore and rejuvenate.</p>
        </div>
        <div class="row">
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="images/yoga.jpg" class="card-img-top" alt="Yoga & Meditation">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Yoga & Meditation</h5>
                        <p class="card-text">Cultivate inner peace and enhance physical vitality through our guided classes.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#yoga" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="../images/ayurveda.jpg" class="card-img-top" alt="Ayurvedic Therapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Ayurvedic Therapy</h5>
                        <p class="card-text">Detoxify and rebalance your body with timeless, personalized Ayurvedic treatments.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#ayurveda" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                   <img src="images/nutrition.jpg" class="card-img-top" alt="Nutrition Consultation">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Diet & Nutrition</h5>
                        <p class="card-text">Nourish your body from within with bespoke diet plans from our expert consultants.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#nutrition" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://images.unsplash.com/photo-1519824145371-296894a0d72b?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Physiotherapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Physiotherapy</h5>
                        <p class="card-text">Restore movement, manage pain, and recover with expert physiotherapy and massage care.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#physiotherapy" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://images.unsplash.com/photo-1506744038136-46273834b3fb?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Massage Therapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Massage Therapy</h5>
                        <p class="card-text">Relieve tension and rejuvenate your body with our range of therapeutic massages.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#massage" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://images.unsplash.com/photo-1464983953574-0892a716854b?q=80&w=2070&auto=format&fit=crop" class="card-img-top" alt="Mindfulness and Meditation Workshop">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Mindfulness & Meditation Workshop</h5>
                        <p class="card-text">Cultivate calm, focus, and inner peace with our immersive mindfulness workshops.</p>
                        <a href="<?php echo BASE_URL; ?>services.php#mindfulness" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    // Include the footer
    include('includes/footer.php');
?>