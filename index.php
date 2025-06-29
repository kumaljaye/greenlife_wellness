<?php
    // Define a constant for the base URL used throughout the site for easy path management
    define('BASE_URL', '/greenlife_wellness/');
    
    
    $page_title = 'Welcome to GreenLife | Holistic Wellness Center';

    include('includes/header.php');
?>

<!-- Hero Section: visually striking intro with background image, overlay, and call-to-action -->
<header class="hero-section text-white text-center" style="background: url('assets/images/header-bg.jpg') center center/cover no-repeat; min-height: 420px; position: relative;">
    <!-- Main hero content box with dark overlay, rounded corners, and accent border -->
    <div class="hero-content" style="position: relative; z-index: 2; padding: 80px 32px 80px 32px; background: rgba(12,14,28,0.85); border-radius: 2rem; max-width: 900px; margin: 0 auto; box-shadow: 0 8px 40px rgba(24,28,47,0.18); border: 1.5px solid #7be495; backdrop-filter: blur(2px);">
        <h1 class="display-3 mb-3" style="font-weight: 700; letter-spacing: 1px;"><span class="text-brand">GreenLife Sanctuary for Your Soul</span></h1>
        <p class="lead mb-4" style="color: #ffe082; font-size: 1.35rem;">Experience profound tranquility and holistic healing at GreenLife, Colombo's premier wellness center.</p>
       
        <a href="<?php echo BASE_URL; ?>services.php" class="btn btn-outline-accent btn-lg mt-2 shadow" style="font-size: 1.15rem; padding: 0.75rem 2.5rem; border-radius: 2rem;">Explore Our Services</a>
    </div>
    <!-- Semi-transparent dark overlay for better text readability -->
    <div style="position: absolute; top:0; left:0; width:100%; height:100%; background: rgba(24,28,47,0.55); z-index:1;"></div>
</header>

<!-- Section: Holistic Living Philosophy -->
<section class="py-5">
    <div class="container text-center" style="max-width: 800px;">
        <h2 class="section-title">Embrace Holistic Living</h2>
        <p class="lead text-body-secondary mb-5">We believe true wellness is a harmonious balance of mind, body, and spirit. Our integrated approach blends ancient wisdom with modern therapies to guide you on your personal journey to optimal health.</p>
    </div>
</section>

<!-- Section: Signature Services (service cards with images and links) -->
<section class="py-5 services-bg">
    <div class="container">
        <div class="text-center mb-5">
            <h2 class="section-title">Our Signature Services</h2>
            <p class="text-body-secondary">Curated experiences designed to restore and rejuvenate.</p>
        </div>
        <div class="row">
            <!-- Service Card: Yoga & Meditation -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://images.unsplash.com/photo-1616376392785-8e7e283571e6?w=600&auto=format&fit=crop&q=60&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxzZWFyY2h8MTJ8fHlvZ2ElMjBtZWRpdGF0aW9ufGVufDB8fDB8fHww" class="card-img-top" alt="Yoga & Meditation">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Yoga & Meditation</h5>
                        <p class="card-text">Cultivate inner peace and enhance physical vitality through our guided classes.</p>
                        <!-- Link to Yoga section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#yoga" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <!-- Service Card: Ayurvedic Therapy -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://media.istockphoto.com/id/1162149826/photo/shirodhara-an-ayurvedic-healing-technique-oil-dripping-on-the-female-forehead-portrait-of-a.webp?a=1&b=1&s=612x612&w=0&k=20&c=Uk5CChV22akzk7Idx0SK39KWVVi1yj44e4voDPUuwck=" class="card-img-top" alt="Ayurvedic Therapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Ayurvedic Therapy</h5>
                        <p class="card-text">Detoxify and rebalance your body with timeless, personalized Ayurvedic treatments.</p>
                        <!-- Link to Ayurveda section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#ayurveda" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <!-- Service Card: Diet & Nutrition -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://media.istockphoto.com/id/1341976416/photo/healthy-eating-and-diet-concepts-top-view-of-spring-salad-shot-from-above-on-rustic-wood-table.webp?a=1&b=1&s=612x612&w=0&k=20&c=96_TRuKfajsZE30WYk1wQqaXLmIdcwX92tVhhLrbRvQ=" class="card-img-top" alt="Nutrition Consultation">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Diet & Nutrition</h5>
                        <p class="card-text">Nourish your body from within with bespoke diet plans from our expert consultants.</p>
                        <!-- Link to Nutrition section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#nutrition" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <!-- Service Card: Physiotherapy -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://media.istockphoto.com/id/2158934402/photo/therapist-treating-female-patient-in-clinic.webp?a=1&b=1&s=612x612&w=0&k=20&c=mHfgMELpGalQmE0Csbgr_az7opIRXhz-30XLnIVP-pk=" class="card-img-top" alt="Physiotherapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Physiotherapy</h5>
                        <p class="card-text">Restore movement, manage pain, and recover with expert physiotherapy and massage care.</p>
                        <!-- Link to Physiotherapy section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#physiotherapy" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <!-- Service Card: Massage Therapy -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://media.istockphoto.com/id/1480533125/photo/asian-woman-has-thai-massage-of-neck-stretching-for-treat-painful-from-office-syndrome.webp?a=1&b=1&s=612x612&w=0&k=20&c=viFUyurhWzHeH3MjkCb1Vh9eJ49fk4T23KvYRvbMnJU=" class="card-img-top" alt="Massage Therapy">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Massage Therapy</h5>
                        <p class="card-text">Relieve tension and rejuvenate your body with our range of therapeutic massages.</p>
                        <!-- Link to Massage section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#massage" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
            <!-- Service Card: Mindfulness & Meditation Workshop -->
            <div class="col-md-4 mb-4">
                <div class="card service-card h-100">
                    <img src="https://media.istockphoto.com/id/1453736358/photo/yogi-with-headphones-listening-yoga-audio-guide.webp?a=1&b=1&s=612x612&w=0&k=20&c=Bt7uwvkUjObzVhbx5FpOoAX9jkMAwKNFju52AI94g9M=" class="card-img-top" alt="Mindfulness and Meditation Workshop">
                    <div class="card-body d-flex flex-column">
                        <h5 class="card-title">Mindfulness & Meditation Workshop</h5>
                        <p class="card-text">Cultivate calm, focus, and inner peace with our immersive mindfulness workshops.</p>
                        <!-- Link to Mindfulness section on services page -->
                        <a href="<?php echo BASE_URL; ?>services.php#mindfulness" class="btn btn-outline-accent mt-auto">Discover More</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<?php
    // Include the site-wide footer (contact info, links, copyright)
    include('includes/footer.php');
?>