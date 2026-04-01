<?php
include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Washly</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@400;700&family=Montserrat:wght@600;700&display=swap" rel="stylesheet">

    <link href="assets/css/main-page-style.css" rel="stylesheet">

</head>

<body>
    <main class="container hero-section">
        <div class="row align-items-center">
            <div class="col-md-6" data-aos="fade-right">
                <h1 class="text-center bold">Welcome to Washly</h1>
                <p>
                    Washly brings professional car cleaning to you anytime, anywhere, so your car always looks its best.
                </p>
                <div class="hero-buttons">
                    <a href="#" class="btn primary-btn">Get Started</a>
                    <a href="#" class="btn secondary-btn">Learn More</a>
                </div>
            </div>
            <div class="col-md-6 text-center" data-aos="fade-left">
                <div class="hero-logo">
                    <img src="assets/images/Washly_Logo.png" alt="Washly logo">
                </div>
            </div>
        </div>
    </main>
    <div class="content-image">
    </div>

    <section class="container perks-section" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Our Perks</h2>
            <p class="section-subtitle">Why settle for less when your ride can be the star of the show?</p>
        </div>

        <div class="card perk-card">
            <div class="row g-0">
                <div class="col-md-5 d-flex align-items-center">
                    <div class="perk-image-container">
                        <img src="assets/images/pic3.jpeg" class="img-fluid rounded-start" alt="Car cleaning inside">
                    </div>
                </div>
                <div class="col-md-7 d-flex align-items-center">
                    <div class="perk-content-container">
                        <h3 class="perk-title">Easy Booking</h3>
                        <p class="perk-description">No more phone calls or waiting. Book your car's pampering session in seconds, from your couch or the moon.</p>
                        <div class="perk-buttons">
                            <a href="#" class="btn perk-primary-btn">Learn More</a>
                            <a href="#" class="btn perk-secondary-btn">Simple</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="card perk-card mt-5">
            <div class="row g-0">
                <div class="col-md-5 d-flex align-items-center">
                    <div class="perk-image-container">
                        <img src="assets/images/pic4.avif" class="img-fluid rounded-start" alt="Eco-friendly cleaning products">
                    </div>
                </div>
                <div class="col-md-7 d-flex align-items-center">
                    <div class="perk-content-container">
                        <h3 class="perk-title">Eco-Friendly Service</h3>
                        <p class="perk-description">We use high-quality, non-toxic, and biodegradable cleaning products to give your car a brilliant shine while protecting the environment.</p>
                        <div class="perk-buttons">
                            <a href="#" class="btn perk-primary-btn">Our Mission</a>
                            <a href="#" class="btn perk-secondary-btn">Learn More</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Why Choose Us?</h2>
            <p class="section-subtitle">Our commitment to quality, convenience, and the environment sets us apart.</p>
        </div>
        <div class="row text-center mt-5">
            <div class="col-lg-4 mb-4" data-aos="fade-right" data-aos-delay="100">
                <div class="feature-card">
                    <div class="icon-circle"><i class="bi bi-clock-fill feature-icon"></i></div>
                    <h4 class="feature-title">On-Demand Service</h4>
                    <p class="text-muted">Book a wash anytime, anywhere. We come to you, so you can save time and effort.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4" data-aos="fade-up" data-aos-delay="300">
                <div class="feature-card">
                    <div class="icon-circle"><i class="bi bi-star-fill feature-icon"></i></div>
                    <h4 class="feature-title">High-Quality Results</h4>
                    <p class="text-muted">Our professional detailers use premium products and techniques for a perfect finish every time.</p>
                </div>
            </div>
            <div class="col-lg-4 mb-4" data-aos="fade-left" data-aos-delay="500">
                <div class="feature-card">
                    <div class="icon-circle"><i class="bi bi-hand-thumbs-up-fill feature-icon"></i></div>
                    <h4 class="feature-title">Eco-Friendly Products</h4>
                    <p class="text-muted">We use biodegradable, water-saving solutions to keep your car sparkling and the planet clean.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Our Simple Booking Process</h2>
            <p class="section-subtitle">A sparkling clean car is just four easy steps away.</p>
        </div>
        <div class="row g-4 process-container">
            <div class="col-md-3 text-center" data-aos="zoom-in" data-aos-delay="100">
                <div class="process-step">
                    <div class="step-icon-circle"><i class="bi bi-1-circle-fill"></i></div>
                    <h5 class="step-title">Choose a Service</h5>
                    <p class="text-muted">Select your preferred car wash or detailing package from our list of services.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-aos="zoom-in" data-aos-delay="300">
                <div class="process-step">
                    <div class="step-icon-circle"><i class="bi bi-2-circle-fill"></i></div>
                    <h5 class="step-title">Schedule a Time</h5>
                    <p class="text-muted">Pick a date and time that works best for you. We'll be there on time, guaranteed.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-aos="zoom-in" data-aos-delay="500">
                <div class="process-step">
                    <div class="step-icon-circle"><i class="bi bi-3-circle-fill"></i></div>
                    <h5 class="step-title">We Come to You</h5>
                    <p class="text-muted">Our mobile team arrives at your location, fully equipped to clean your car on-site.</p>
                </div>
            </div>
            <div class="col-md-3 text-center" data-aos="zoom-in" data-aos-delay="700">
                <div class="process-step">
                    <div class="step-icon-circle"><i class="bi bi-4-circle-fill"></i></div>
                    <h5 class="step-title">Enjoy Your Clean Car</h5>
                    <p class="text-muted">Relax and enjoy your perfectly clean, polished, and fresh-smelling vehicle.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5" id="services" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Our Services</h2>
            <p class="section-subtitle">Choose the perfect package for your vehicle's needs.</p>
        </div>
        <div class="row row-cols-1 row-cols-md-2 row-cols-lg-4 g-4 pricing-plans">

            <div class="col">
                <div class="pricing-card" data-aos="fade-up" data-aos-delay="100">
                    <div class="card-header">
                        <div class="service-icon">✨</div>
                        <h4 class="service-title">Basic Wash</h4>
                        <div class="service-price">8.00 JD</div>
                    </div>
                    <ul class="list-unstyled service-features">
                        <li><i class="bi bi-check-circle-fill"></i> Exterior wash, rinse, and dry</li>
                        <li><i class="bi bi-check-circle-fill"></i> Tire cleaning</li>
                    </ul>
                    <div class="service-meta">
                        <span class="meta-item"><i class="bi bi-clock"></i> 60 min</span>
                    </div>
                    <a href="#" class="btn btn-primary w-100 mt-4">Book Now</a>
                </div>
            </div>

            <div class="col">
                <div class="pricing-card highlighted-card" data-aos="fade-up" data-aos-delay="200">
                    <div class="card-header">
                        <div class="service-icon">🧽</div>
                        <h4 class="service-title">Premium Wash</h4>
                        <div class="service-price">15.00 JD</div>
                    </div>
                    <ul class="list-unstyled service-features">
                        <li><i class="bi bi-check-circle-fill"></i> Exterior wash</li>
                        <li><i class="bi bi-check-circle-fill"></i> Interior vacuum</li>
                        <li><i class="bi bi-check-circle-fill"></i> Dashboard cleaning</li>
                        <li><i class="bi bi-check-circle-fill"></i> Windows cleaning</li>
                    </ul>
                    <div class="service-meta">
                        <span class="meta-item"><i class="bi bi-clock"></i> 120 min</span>
                    </div>
                    <a href="#" class="btn btn-primary w-100 mt-4">Book Now</a>
                </div>
            </div>

            <div class="col">
                <div class="pricing-card" data-aos="fade-up" data-aos-delay="300">
                    <div class="card-header">
                        <div class="service-icon">🧹</div>
                        <h4 class="service-title">Full Detailing</h4>
                        <div class="service-price">35.00 JD</div>
                    </div>
                    <ul class="list-unstyled service-features">
                        <li><i class="bi bi-check-circle-fill"></i> Premium wash</li>
                        <li><i class="bi bi-check-circle-fill"></i> Interior detailing</li>
                        <li><i class="bi bi-check-circle-fill"></i> Wax application</li>
                        <li><i class="bi bi-check-circle-fill"></i> Leather conditioning</li>
                        <li><i class="bi bi-check-circle-fill"></i> Engine cleaning</li>
                    </ul>
                    <div class="service-meta">
                        <span class="meta-item"><i class="bi bi-clock"></i> 40 min</span>
                    </div>
                    <a href="#" class="btn btn-primary w-100 mt-4">Book Now</a>
                </div>
            </div>

            <div class="col">
                <div class="pricing-card" data-aos="fade-up" data-aos-delay="400">
                    <div class="card-header">
                        <div class="service-icon">🚗</div>
                        <h4 class="service-title">Interior Only</h4>
                        <div class="service-price">12.00 JD</div>
                    </div>
                    <ul class="list-unstyled service-features">
                        <li><i class="bi bi-check-circle-fill"></i> Deep interior cleaning and sanitization</li>
                        <li><i class="bi bi-check-circle-fill"></i> Vacuum cleaning</li>
                        <li><i class="bi bi-check-circle-fill"></i> Dashboard polish</li>
                        <li><i class="bi bi-check-circle-fill"></i> Seat cleaning</li>
                        <li><i class="bi bi-check-circle-fill"></i> Air freshener</li>
                    </ul>
                    <div class="service-meta">
                        <span class="meta-item"><i class="bi bi-clock"></i> N/A</span>
                    </div>
                    <a href="#" class="btn btn-primary w-100 mt-4">Book Now</a>
                </div>
            </div>

        </div>
    </section>

    <section class="container my-5" data-aos="fade-up">
        <h2 class="text-center section-title">What Our Customers Say</h2>
        <p class="text-center section-subtitle">See why our clients love Washly's convenience and quality.</p>
        <div id="testimonialsCarousel" class="carousel slide" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <div class="testimonial-card">
                        <p>"Absolutely amazing service! My car has never looked this good. The team was professional and fast. Highly recommended!"</p>
                        <div class="author">- Abood</div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <p>"Convenient, eco-friendly, and a perfect finish every time. Washly is my go-to for all my car washing needs."</p>
                        <div class="author">- Mazen</div>
                    </div>
                </div>
                <div class="carousel-item">
                    <div class="testimonial-card">
                        <p>"The best car wash experience I've ever had. They paid attention to every detail, and the results speak for themselves."</p>
                        <div class="author">- Saad</div>
                    </div>
                </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="prev">
                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#testimonialsCarousel" data-bs-slide="next">
                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                <span class="visually-hidden">Next</span>
            </button>
        </div>
    </section>

    <section class="container my-5" id="faqs" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Frequently Asked Questions</h2>
            <p class="section-subtitle">Got questions? We have the answers. If not, contact us!</p>
        </div>
        <div class="accordion" id="faqAccordion">
            <div class="accordion-item" data-aos="fade-up">
                <h2 class="accordion-header" id="headingOne">
                    <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                        How does the at-home car wash work?
                    </button>
                </h2>
                <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        You simply book an appointment through our website or app, and our professional team will come to your location—be it your home or office—to wash your car. We bring all the necessary equipment and supplies.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="100">
                <h2 class="accordion-header" id="headingTwo">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                        Are your cleaning products safe for the environment?
                    </button>
                </h2>
                <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        Yes, we are committed to being eco-friendly. We use high-quality, biodegradable, and non-toxic cleaning products that are safe for your car, your family, and the environment.
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="200">
                <h2 class="accordion-header" id="headingThree">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                        What areas do you serve?
                    </button>
                </h2>
                <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        We currently serve major cities and their surrounding areas. Please enter your location during the booking process to confirm if we cover your area. We are always expanding our service regions!
                    </div>
                </div>
            </div>
            <div class="accordion-item" data-aos="fade-up" data-aos-delay="300">
                <h2 class="accordion-header" id="headingFour">
                    <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                        How long does a typical wash take?
                    </button>
                </h2>
                <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour" data-bs-parent="#faqAccordion">
                    <div class="accordion-body">
                        The duration depends on the service package you choose. A basic exterior wash can take as little as 30-40 minutes, while a full detailing service may take up to 2-3 hours.
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container my-5" id="contact" data-aos="fade-up">
        <div class="text-center section-header">
            <h2 class="section-title">Contact Us</h2>
            <p class="section-subtitle">Have a question or a special request? We're here to help.</p>
        </div>
        <div class="row">
            <div class="col-md-6 mb-4" data-aos="fade-right">
                <div class="contact-form-container p-4">
                    <form>
                        <div class="mb-3">
                            <label for="name" class="form-label">Name</label>
                            <input type="text" class="form-control" id="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email address</label>
                            <input type="email" class="form-control" id="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Message</label>
                            <textarea class="form-control" id="message" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn primary-btn w-100">Send Message</button>
                    </form>
                </div>
            </div>
            <div class="col-md-6" data-aos="fade-left">
                <div class="map-container rounded-3 overflow-hidden">
                    <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3455.577232230353!2d31.25827011504938!3d29.98774208191953!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x1458380b2d6a5c13%3A0xc6c4f8d3d9d3d3a!2sCairo%2C%20Egypt!5e0!3m2!1sen!2sjo!4v1672345678901!5m2!1sen!2sjo" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
                </div>
            </div>
        </div>
    </section>

    <section class="container cta-section my-5 text-center p-5 rounded-3" data-aos="zoom-in">
        <h2 class="cta-title">Ready to Get Your Car Washed?</h2>
        <p class="cta-subtitle">Book your appointment today and experience the Washly difference.</p>
        <a href="#" class="btn btn-lg cta-btn mt-3">Book Now</a>
    </section>


    <script src="assets/js/scroll_animation.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/aos/2.3.4/aos.js"></script>
    <script src="assets/js/header_script.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>

<?php include 'includes/footer.php'; ?>