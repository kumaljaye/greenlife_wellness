<?php
    // Define a constant for the base URL
    define('BASE_URL', '/greenlife_wellness/');
    
    // Set the page title
    $page_title = 'Our Services | GreenLife Wellness Center';

    // Include the header
    include('includes/header.php');
?>

<header class="page-header">
    <div class="container">
        <h1>Our Wellness Services</h1>
        <p class="lead">A complete range of therapies designed to heal, restore, and elevate.</p>
    </div>
</header>

<main>
    <section id="ayurveda" class="service-detail-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://media.istockphoto.com/id/1162149826/photo/shirodhara-an-ayurvedic-healing-technique-oil-dripping-on-the-female-forehead-portrait-of-a.webp?a=1&b=1&s=612x612&w=0&k=20&c=Uk5CChV22akzk7Idx0SK39KWVVi1yj44e4voDPUuwck=" alt="Ayurvedic Therapy" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title">Ayurvedic Therapy</h2>
                    <h5 class="text-body-secondary mb-3">The Science of Life</h5>
                    <p>Our Ayurvedic treatments are deeply rooted in ancient wisdom. We offer personalized consultations to determine your unique dosha (body constitution) and create a tailored therapy plan to restore your natural balance, detoxify your body, and calm your mind.</p>
                    <ul class="service-features-list">
                        <li>Personalized Dosha Assessment</li>
                        <li>Abhyanga (Herbal Oil Massage)</li>
                        <li>Shirodhara (Forehead Oil Flow)</li>
                        <li>Panchakarma (Detoxification Programs)</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book This Service</a>
                </div>
            </div>
        </div>
    </section>

    <section id="yoga" class="service-detail-section services-bg">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1599901860904-17e6ed7083a0?q=80&w=2070&auto=format&fit=crop" alt="Yoga and Meditation" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2 class="section-title">Yoga & Meditation</h2>
                    <h5 class="text-body-secondary mb-3">Uniting Mind, Body, and Breath</h5>
                    <p>Whether you are a beginner or an experienced practitioner, our yoga and meditation classes provide a peaceful sanctuary to enhance your flexibility, build strength, and cultivate profound inner calm. Join our group classes or book a private session for focused guidance.</p>
                    <ul class="service-features-list">
                        <li>Hatha, Vinyasa, and Restorative Yoga</li>
                        <li>Guided Mindfulness Meditation</li>
                        <li>Pranayama (Breathing Techniques)</li>
                        <li>Private and Group Sessions Available</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book Session</a>
                </div>
            </div>
        </div>
    </section>

    <section id="nutrition" class="service-detail-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://images.unsplash.com/photo-1498837167922-ddd27525d352?q=80&w=2070&auto=format&fit=crop" alt="Nutrition and Diet Consultation" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title">Nutrition & Diet Consultation</h2>
                    <h5 class="text-body-secondary mb-3">Nourish From Within</h5>
                    <p>Food is medicine. Our expert nutritionists work with you to create sustainable, enjoyable, and effective dietary plans that address your specific health goals, from weight management to improving energy levels and managing chronic conditions.</p>
                    <ul class="service-features-list">
                        <li>Personalized Meal Planning</li>
                        <li>Weight Management Programs</li>
                        <li>Sports Nutrition Guidance</li>
                        <li>Dietary Support for Health Conditions</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book a Consultation</a>
                </div>
            </div>
        </div>
    </section>

    <section id="physiotherapy" class="service-detail-section services-bg">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <img src="https://media.istockphoto.com/id/2158934402/photo/therapist-treating-female-patient-in-clinic.webp?a=1&b=1&s=612x612&w=0&k=20&c=mHfgMELpGalQmE0Csbgr_az7opIRXhz-30XLnIVP-pk=" alt="Physiotherapy and Massage Therapy" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2 class="section-title">Physiotherapy</h2>
                    <h5 class="text-body-secondary mb-3">Restoring Movement and Releasing Tension</h5>
                    <p>Our certified physiotherapists help you recover from injury, manage pain, and improve mobility. Complement your recovery with our range of therapeutic massages, including Deep Tissue, Swedish, and Hot Stone, designed to alleviate stress and soothe sore muscles.</p>
                    <ul class="service-features-list">
                        <li>Post-Injury Rehabilitation</li>
                        <li>Chronic Pain Management</li>
                        <li>Deep Tissue & Sports Massage</li>
                        <li>Relaxation & Aromatherapy Massage</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book a Session</a>
                </div>
            </div>
        </div>
    </section>

    <section id="massage" class="service-detail-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 mb-4 mb-lg-0">
                    <img src="https://media.istockphoto.com/id/1480533125/photo/asian-woman-has-thai-massage-of-neck-stretching-for-treat-painful-from-office-syndrome.webp?a=1&b=1&s=612x612&w=0&k=20&c=viFUyurhWzHeH3MjkCb1Vh9eJ49fk4T23KvYRvbMnJU=" alt="Massage Therapy" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6">
                    <h2 class="section-title">Massage Therapy</h2>
                    <h5 class="text-body-secondary mb-3">Relax, Restore, and Rejuvenate</h5>
                    <p>Experience the healing power of touch. Our massage therapists offer a variety of techniques to relieve tension, reduce pain, and promote deep relaxation. Whether you seek stress relief, injury recovery, or simply a moment of peace, our tailored sessions are designed for your needs.</p>
                    <ul class="service-features-list">
                        <li>Swedish & Deep Tissue Massage</li>
                        <li>Hot Stone & Aromatherapy</li>
                        <li>Sports & Prenatal Massage</li>
                        <li>Stress & Pain Relief</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book a Massage</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Mindfulness & Meditation Workshop Section -->
    <section id="mindfulness" class="service-detail-section services-bg">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6 order-lg-2 mb-4 mb-lg-0">
                    <img src="https://media.istockphoto.com/id/1453736358/photo/yogi-with-headphones-listening-yoga-audio-guide.webp?a=1&b=1&s=612x612&w=0&k=20&c=Bt7uwvkUjObzVhbx5FpOoAX9jkMAwKNFju52AI94g9M=" alt="Mindfulness and Meditation Workshop" class="img-fluid rounded-3 shadow">
                </div>
                <div class="col-lg-6 order-lg-1">
                    <h2 class="section-title">Mindfulness & Meditation Workshop</h2>
                    <h5 class="text-body-secondary mb-3">Cultivate Calm, Focus, and Inner Peace</h5>
                    <p>Join our immersive workshops designed to help you develop mindfulness skills, reduce stress, and enhance emotional well-being. Guided by experienced instructors, you'll learn practical meditation techniques and mindful living strategies for everyday life.</p>
                    <ul class="service-features-list">
                        <li>Guided Meditation Sessions</li>
                        <li>Breathwork & Relaxation Techniques</li>
                        <li>Mindful Movement Practices</li>
                        <li>Stress Reduction Tools</li>
                    </ul>
                    <a href="<?php echo BASE_URL; ?>client/book_appointment.php" class="btn btn-accent mt-3">Book a Workshop</a>
                </div>
            </div>
        </div>
    </section>
</main>


<?php
    // Include the footer
    include('includes/footer.php');
?>