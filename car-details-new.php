<?php include 'partials/header.php'; ?>

<style>
/* ── Premium Model Page Styles ── */
:root {
    --bmw-black: #0a0a0a;
    --bmw-dark-grey: #1a1a1a;
    --bmw-accent: #1c69d4;
    --text-muted: rgba(255,255,255,0.6);
}

.model-hero {
    position: relative;
    height: 80vh;
    min-height: 500px;
    background: #000;
    overflow: hidden;
    display: flex;
    align-items: center;
    justify-content: center;
}

.model-hero img {
    position: absolute;
    inset: 0;
    width: 100%;
    height: 100%;
    object-fit: cover;
    opacity: 0.7;
    animation: zoomOut 20s infinite alternate linear;
}

@keyframes zoomOut {
    from { transform: scale(1.1); }
    to { transform: scale(1); }
}

.model-hero__content {
    position: relative;
    z-index: 2;
    text-align: center;
    padding: 0 20px;
}

.model-hero__eyebrow {
    font-size: 0.8rem;
    font-weight: 700;
    letter-spacing: 0.3em;
    text-transform: uppercase;
    color: var(--bmw-accent);
    margin-bottom: 10px;
    display: block;
}

.model-hero__title {
    font-size: clamp(3rem, 10vw, 6rem);
    font-weight: 900;
    letter-spacing: -0.04em;
    line-height: 1;
    margin: 0;
    color: #fff;
    text-transform: uppercase;
}

.specs-grid-section {
    background: var(--bmw-black);
    padding: 100px 0;
    position: relative;
}

.specs-container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 40px;
}

.specs-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
    gap: 40px;
}

.spec-card {
    background: var(--bmw-dark-grey);
    padding: 40px;
    border-radius: 4px; /* BMW aesthetic is sharp */
    border: 1px solid rgba(255,255,255,0.05);
    transition: all 0.3s ease;
}

.spec-card:hover {
    border-color: var(--bmw-accent);
    transform: translateY(-5px);
    background: #222;
}

.spec-card i {
    font-size: 2rem;
    color: var(--bmw-accent);
    margin-bottom: 20px;
    display: block;
}

.spec-card h3 {
    font-size: 1.2rem;
    font-weight: 700;
    margin-bottom: 10px;
    color: #fff;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.spec-card p {
    font-size: 0.95rem;
    color: var(--text-muted);
    line-height: 1.6;
    margin: 0;
}

.advantages-section {
    background: #fff;
    padding: 100px 0;
    color: #000;
}

.advantage-item {
    margin-bottom: 60px;
}

.advantage-item:last-child { margin-bottom: 0; }

.advantage-item h2 {
    font-size: 2.5rem;
    font-weight: 800;
    letter-spacing: -0.02em;
    margin-bottom: 20px;
}

.advantage-item p {
    font-size: 1.1rem;
    color: #444;
    max-width: 700px;
    line-height: 1.7;
}

.cta-strip {
    background: var(--bmw-accent);
    padding: 60px 0;
    text-align: center;
}

.cta-strip h2 {
    color: #fff;
    font-size: 2rem;
    margin-bottom: 30px;
}

.premium-btn {
    display: inline-block;
    padding: 18px 50px;
    background: #000;
    color: #fff;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    text-decoration: none;
    transition: background 0.3s;
}

.premium-btn:hover {
    background: #333;
}
</style>

<div class="model-hero">
    <img src="https://images.unsplash.com/photo-1555215695-3004980adade?ixlib=rb-4.0.3&auto=format&fit=crop&w=2000&q=90" alt="BMW 2 Series Interior">
    <div class="model-hero__content">
        <span class="model-hero__eyebrow">The All-New</span>
        <h1 class="model-hero__title">BMW 2 SERIES</h1>
    </div>
</div>

<section class="specs-grid-section">
    <div class="specs-container">
        <div style="margin-bottom: 60px; text-align: center;">
            <h2 style="font-size: 2.5rem; color: #fff; font-weight: 800;">Technical Masterpiece.</h2>
            <p style="color: var(--text-muted);">Explore the core components of the BMW 2 Series Gran Coupé.</p>
        </div>
        
        <div class="specs-grid">
            <!-- Engine -->
            <div class="spec-card">
                <i class="fas fa-microchip"></i>
                <h3>Engine</h3>
                <p>2.0L BMW TwinPower Turbo 4-Cylinder Petrol Engine. Perfectly balanced for performance and efficiency.</p>
            </div>
            
            <!-- Power -->
            <div class="spec-card">
                <i class="fas fa-bolt"></i>
                <h3>Power & Torque</h3>
                <p>Delivers 340 HP and 450 Nm of torque, propelling you from 0-100 km/h in just 4.8 seconds.</p>
            </div>
            
            <!-- Gearbox -->
            <div class="spec-card">
                <i class="fas fa-cog"></i>
                <h3>Gearbox</h3>
                <p>8-Speed Steptronic Sport Transmission with paddle shifters for lightning-fast gear changes.</p>
            </div>
            
            <!-- Tyres -->
            <div class="spec-card">
                <i class="fas fa-circle-notch"></i>
                <h3>Tyres & Wheels</h3>
                <p>19" M Light Alloy wheels with Run-flat safety tires for ultimate grip and security.</p>
            </div>
            
            <!-- Airbags -->
            <div class="spec-card">
                <i class="fas fa-shield-alt"></i>
                <h3>Airbags</h3>
                <p>Comprehensive 8-airbag system with front, side, and curtain protection for all occupants.</p>
            </div>
            
            <!-- Safety -->
            <div class="spec-card">
                <i class="fas fa-user-shield"></i>
                <h3>Safety Systems</h3>
                <p>Active Guard with Lane Departure Warning, Frontal Collision Warning, and Dynamic Stability Control.</p>
            </div>
            
            <!-- Seats -->
            <div class="spec-card">
                <i class="fas fa-chair"></i>
                <h3>Seating</h3>
                <p>Dakota Leather M-Sport Seats with 14-way power adjustment and memory function.</p>
            </div>
            
            <!-- Lights -->
            <div class="spec-card">
                <i class="fas fa-lightbulb"></i>
                <h3>Lighting</h3>
                <p>Adaptive LED Headlamps with high-beam assistant and iconic BMW hexagonal signature.</p>
            </div>
            
            <!-- Speakers -->
            <div class="spec-card">
                <i class="fas fa-volume-up"></i>
                <h3>Surround Sound</h3>
                <p>Harman Kardon 16-speaker Premium Surround Sound System for a concert-like audio experience.</p>
            </div>
        </div>
    </div>
</section>

<section class="advantages-section">
    <div class="specs-container">
        <div class="advantage-item">
            <h2 style="color: var(--bmw-accent);">The Advantages</h2>
            <p>The BMW 2 Series Gran Coupé blends aesthetic appeal with powerful driving dynamics. Its low, wide stance and distinctive coupe silhouette make every journey an event.</p>
        </div>
        <div class="advantage-item">
            <h2>iDrive 8.5 Connectivity</h2>
            <p>Experience the latest in cabin technology with the BMW Curved Display and the intuitive iDrive 8.5 operating system, keeping you connected effortlessly.</p>
        </div>
    </div>
</section>

<section class="cta-strip">
    <div class="specs-container">
        <h2>Ready to feel the power?</h2>
        <a href="test-drive.php" class="premium-btn">Book an Appointment</a>
    </div>
</section>

<?php include 'partials/footer.php'; ?>
