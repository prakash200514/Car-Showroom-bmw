<?php
session_start();
include 'config/database.php';
include 'includes/functions.php';
include 'partials/header.php';

// Fetch services from DB
$services = $pdo->query("SELECT * FROM services ORDER BY base_price ASC")->fetchAll();
?>

<style>
.services-hero {
    position: relative;
    background: url('https://images.unsplash.com/photo-1625047509248-ec889cbff17f?auto=format&fit=crop&w=1920&q=80') center/cover no-repeat;
    padding: 120px 20px 80px;
    text-align: center;
    color: white;
    overflow: hidden;
}
.services-hero::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, rgba(0,0,0,0.6), rgba(0,0,0,0.8));
}
.services-hero-content { position: relative; z-index: 1; max-width: 800px; margin: 0 auto; }
.services-hero-content h1 { font-size: clamp(2.5rem, 5vw, 4rem); font-weight: 900; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 15px; }
.services-hero-content p { font-size: 1.2rem; color: #ddd; line-height: 1.6; }

.services-section { max-width: 1200px; margin: 0 auto; padding: 60px 20px; }

/* BMW Service Categories */
.service-categories { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 30px; margin-bottom: 80px; }
.service-cat-card {
    background: #fff;
    border-radius: 12px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0,0,0,0.06);
    border: 1px solid #eee;
    transition: transform 0.3s, box-shadow 0.3s;
    cursor: pointer;
}
.service-cat-card:hover { transform: translateY(-8px); box-shadow: 0 15px 40px rgba(0,0,0,0.12); }
.service-cat-img { width: 100%; height: 200px; object-fit: cover; display: block; }
.service-cat-body { padding: 25px; }
.service-cat-icon { font-size: 2rem; color: #1c6bba; margin-bottom: 12px; }
.service-cat-title { font-size: 1.3rem; font-weight: 800; color: #111; margin-bottom: 8px; }
.service-cat-desc { font-size: 0.9rem; color: #666; line-height: 1.6; margin-bottom: 15px; }
.service-cat-price { font-size: 1.1rem; font-weight: 700; color: #1c6bba; }

/* How it works */
.how-it-works { background: #f8f9fa; padding: 70px 20px; text-align: center; }
.steps-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; max-width: 900px; margin: 40px auto 0; }
.step-item { position: relative; }
.step-number {
    width: 60px; height: 60px;
    background: #1c6bba;
    color: white;
    font-size: 1.5rem;
    font-weight: 900;
    border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    margin: 0 auto 15px;
}
.step-title { font-size: 1.1rem; font-weight: 700; color: #111; margin-bottom: 8px; }
.step-desc { font-size: 0.9rem; color: #666; line-height: 1.5; }

/* Booking Form */
.booking-section { max-width: 800px; margin: 0 auto; padding: 60px 20px; }
.booking-card { background: #fff; border-radius: 12px; padding: 40px; box-shadow: 0 10px 30px rgba(0,0,0,0.07); border: 1px solid #eee; }
.form-group { margin-bottom: 22px; }
.form-group label { display: block; font-weight: 600; color: #333; margin-bottom: 8px; }
.form-group input, .form-group select, .form-group textarea {
    width: 100%;
    padding: 13px 16px;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    font-size: 1rem;
    outline: none;
    transition: border-color 0.3s, box-shadow 0.3s;
    background: #fdfdfd;
    box-sizing: border-box;
}
.form-group input:focus, .form-group select:focus, .form-group textarea:focus {
    border-color: #1c6bba;
    box-shadow: 0 0 0 3px rgba(28, 107, 186, 0.1);
}
.form-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 20px; }
.submit-btn {
    width: 100%; padding: 15px; background: #1c6bba; color: #fff;
    border: none; border-radius: 8px; font-size: 1.1rem; font-weight: 700;
    cursor: pointer; transition: background 0.3s, transform 0.1s;
}
.submit-btn:hover { background: #155598; transform: translateY(-2px); }

/* Why BMW Service */
.why-section { background: #111; color: white; padding: 70px 20px; text-align: center; }
.why-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 40px; max-width: 1000px; margin: 40px auto 0; }
.why-item i { font-size: 2.5rem; color: #1c6bba; margin-bottom: 15px; }
.why-item h4 { font-size: 1.1rem; font-weight: 700; margin-bottom: 8px; }
.why-item p { font-size: 0.9rem; color: #aaa; line-height: 1.5; }

.section-heading { font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 900; text-align: center; color: #111; margin-bottom: 10px; }
.section-sub { text-align: center; color: #666; font-size: 1rem; margin-bottom: 40px; }

@media (max-width: 600px) { .form-grid-2 { grid-template-columns: 1fr; } }
</style>

<!-- HERO -->
<div class="services-hero">
    <div class="services-hero-content">
        <p style="color: #1c6bba; font-weight: 700; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 10px;">BMW Certified Service</p>
        <h1>Your BMW Deserves the Best</h1>
        <p>Expert care from certified BMW technicians using genuine OEM parts. Schedule your service today.</p>
        <a href="#book-service" style="display: inline-block; margin-top: 25px; background: #1c6bba; color: white; padding: 14px 35px; border-radius: 6px; font-weight: 700; font-size: 1rem; text-decoration: none; transition: background 0.3s;">Book a Service <i class="fas fa-arrow-right" style="margin-left: 8px;"></i></a>
    </div>
</div>

<!-- SERVICES GRID -->
<div class="services-section">
    <h2 class="section-heading">Our Services</h2>
    <p class="section-sub">Comprehensive BMW care — from routine maintenance to complex repairs.</p>
    
    <?php if (count($services) > 0): ?>
    <div class="service-categories">
        <?php foreach ($services as $service): ?>
        <div class="service-cat-card">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-wrench"></i></div>
                <div class="service-cat-title"><?= htmlspecialchars($service['service_name']) ?></div>
                <div class="service-cat-desc"><?= htmlspecialchars($service['description'] ?? 'Professional BMW certified service.') ?></div>
                <div class="service-cat-price">Starting from ₹<?= number_format($service['base_price'], 2) ?></div>
                <?php if ($service['duration_hours']): ?>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> <?= $service['duration_hours'] ?> hr<?= $service['duration_hours'] > 1 ? 's' : '' ?></div>
                <?php endif; ?>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
    <?php else: ?>
    <!-- Default service cards when DB is empty -->
    <div class="service-categories">

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1591293835940-934a7c4f2d9b?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="Oil Change">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-oil-can"></i></div>
                <div class="service-cat-title">Oil & Filter Change</div>
                <div class="service-cat-desc">Genuine BMW engine oil and OEM filter replacement for peak engine performance and longevity.</div>
                <div class="service-cat-price">Starting from ₹3,500</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 1–2 hrs</div>
            </div>
        </div>

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1620288627223-53302f4e8c74?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="Brake Service">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-brake-warning"></i></div>
                <div class="service-cat-title">Brake Inspection & Service</div>
                <div class="service-cat-desc">Full brake pad, rotor, and caliper inspection and replacement with genuine BMW components.</div>
                <div class="service-cat-price">Starting from ₹8,000</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 2–4 hrs</div>
            </div>
        </div>

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1580983218765-f663bec07b37?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="Tyre Service">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-circle-notch"></i></div>
                <div class="service-cat-title">Tyre Rotation & Alignment</div>
                <div class="service-cat-desc">Precision wheel balancing, tyre rotation and 4-wheel alignment for a smooth, safe ride.</div>
                <div class="service-cat-price">Starting from ₹2,500</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 1 hr</div>
            </div>
        </div>

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="Annual Service">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-clipboard-check"></i></div>
                <div class="service-cat-title">Annual Inspection (CBS)</div>
                <div class="service-cat-desc">Comprehensive BMW Condition-Based Service covering all critical vehicle systems and safety checks.</div>
                <div class="service-cat-price">Starting from ₹12,000</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 4–6 hrs</div>
            </div>
        </div>

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1541899481282-d53bffe3c35d?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="AC Service">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-snowflake"></i></div>
                <div class="service-cat-title">AC & Climate Service</div>
                <div class="service-cat-desc">Air conditioning regas, filter replacement, and full HVAC system inspection and cleaning.</div>
                <div class="service-cat-price">Starting from ₹4,500</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 2 hrs</div>
            </div>
        </div>

        <div class="service-cat-card">
            <img src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&w=600&q=80" class="service-cat-img" alt="Diagnostics">
            <div class="service-cat-body">
                <div class="service-cat-icon"><i class="fas fa-laptop-code"></i></div>
                <div class="service-cat-title">BMW Diagnostics & Software Update</div>
                <div class="service-cat-desc">Factory-level ISTA diagnostics, ECU fault clearing, and the latest BMW software upgrades.</div>
                <div class="service-cat-price">Starting from ₹1,500</div>
                <div style="font-size: 0.85rem; color: #999; margin-top: 5px;"><i class="fas fa-clock"></i> 1–2 hrs</div>
            </div>
        </div>

    </div>
    <?php endif; ?>
</div>

<!-- HOW IT WORKS -->
<div class="how-it-works">
    <h2 class="section-heading">How It Works</h2>
    <p class="section-sub">Simple steps to get your BMW serviced.</p>
    <div class="steps-grid">
        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-title">Book Online</div>
            <div class="step-desc">Fill out the booking form with your car details and preferred date.</div>
        </div>
        <div class="step-item">
            <div class="step-number">2</div>
            <div class="step-title">We Confirm</div>
            <div class="step-desc">Our service advisor will call you within 2 hours to confirm your slot.</div>
        </div>
        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-title">Drop Your BMW</div>
            <div class="step-desc">Bring your car to our service centre at the scheduled time.</div>
        </div>
        <div class="step-item">
            <div class="step-number">4</div>
            <div class="step-title">Pick Up & Go</div>
            <div class="step-desc">Collect your fully serviced BMW along with a detailed service report.</div>
        </div>
    </div>
</div>

<!-- BOOKING FORM -->
<div class="booking-section" id="book-service">
    <h2 class="section-heading">Book a Service</h2>
    <p class="section-sub">Fill in the details below and we'll get back to you shortly.</p>

    <?php if (isset($_GET['success'])): ?>
    <div style="background: #e8f5e9; border-left: 4px solid #28a745; padding: 16px; border-radius: 8px; margin-bottom: 25px; color: #2e7d32; font-weight: 600;">
        <i class="fas fa-check-circle"></i> Your service booking request has been received! We'll contact you within 2 hours.
    </div>
    <?php endif; ?>

    <div class="booking-card">
        <form method="POST" action="service-booking.php">
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Full Name *</label>
                    <input type="text" name="name" placeholder="Your full name" required value="<?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : '' ?>">
                </div>
                <div class="form-group">
                    <label>Phone Number *</label>
                    <input type="tel" name="phone" placeholder="+91 XXXXX XXXXX" required>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>Email Address *</label>
                    <input type="email" name="email" placeholder="your@email.com" required>
                </div>
                <div class="form-group">
                    <label>Service Type *</label>
                    <select name="service_type" required>
                        <option value="">-- Select a Service --</option>
                        <?php if (count($services) > 0): ?>
                            <?php foreach ($services as $s): ?>
                            <option value="<?= htmlspecialchars($s['service_name']) ?>"><?= htmlspecialchars($s['service_name']) ?></option>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <option value="Oil & Filter Change">Oil & Filter Change</option>
                        <option value="Brake Inspection & Service">Brake Inspection & Service</option>
                        <option value="Tyre Rotation & Alignment">Tyre Rotation & Alignment</option>
                        <option value="Annual Inspection (CBS)">Annual Inspection (CBS)</option>
                        <option value="AC & Climate Service">AC & Climate Service</option>
                        <option value="BMW Diagnostics">BMW Diagnostics & Software Update</option>
                        <option value="Other">Other</option>
                        <?php endif; ?>
                    </select>
                </div>
            </div>
            <div class="form-grid-2">
                <div class="form-group">
                    <label>BMW Model *</label>
                    <input type="text" name="car_model" placeholder="e.g. BMW X5, 3 Series, i4" required>
                </div>
                <div class="form-group">
                    <label>Preferred Date *</label>
                    <input type="date" name="preferred_date" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                </div>
            </div>
            <div class="form-group">
                <label>Additional Notes</label>
                <textarea name="notes" rows="4" placeholder="Describe the issue or any special requests..."></textarea>
            </div>
            <button type="submit" class="submit-btn">
                <i class="fas fa-calendar-check" style="margin-right: 8px;"></i> Book My Service Appointment
            </button>
        </form>
    </div>
</div>

<!-- WHY BMW SERVICE -->
<div class="why-section">
    <h2 style="font-size: clamp(1.8rem, 3vw, 2.5rem); font-weight: 900; margin-bottom: 10px;">Why Choose BMW Certified Service?</h2>
    <p style="color: #aaa; font-size: 1rem;">Expert care you can trust, backed by the BMW standard.</p>
    <div class="why-grid">
        <div class="why-item">
            <i class="fas fa-certificate"></i>
            <h4>Certified Technicians</h4>
            <p>Our team is factory-trained and BMW certified with years of experience.</p>
        </div>
        <div class="why-item">
            <i class="fas fa-cog"></i>
            <h4>Genuine OEM Parts</h4>
            <p>We only use original BMW parts to ensure perfect fit and reliability.</p>
        </div>
        <div class="why-item">
            <i class="fas fa-shield-alt"></i>
            <h4>Service Warranty</h4>
            <p>All services are backed by our BMW workmanship guarantee.</p>
        </div>
        <div class="why-item">
            <i class="fas fa-laptop-code"></i>
            <h4>Latest Diagnostics</h4>
            <p>We use BMW ISTA factory diagnostic tools for accurate fault detection.</p>
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
