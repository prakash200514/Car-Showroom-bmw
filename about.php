<?php
session_start();
include 'partials/header.php';
?>

<div style="background: url('https://images.unsplash.com/photo-1556189250-72ba954cfc2b?auto=format&fit=crop&q=80&w=1920') center/cover no-repeat; padding: 120px 20px; text-align: center; color: white; position: relative;">
    <div style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.6);"></div>
    <div style="position: relative; z-index: 1; max-width: 800px; margin: 0 auto;">
        <h1 style="font-size: 3.5rem; font-weight: 900; margin-bottom: 20px; text-transform: uppercase; letter-spacing: 2px;">About BMW Showroom</h1>
        <p style="font-size: 1.2rem; line-height: 1.6; color: #f0f0f0;">Experience the epitome of automotive excellence. We are dedicated to delivering sheer driving pleasure through an unparalleled selection of luxury vehicles.</p>
    </div>
</div>

<div class="container" style="max-width: 1000px; margin: 60px auto; padding: 0 20px;">
    <!-- Mission Section -->
    <div class="grid grid-2" style="gap: 50px; align-items: center; margin-bottom: 80px;">
        <div>
            <h2 style="font-size: 2.2rem; color: #111; margin-bottom: 20px;">Our Mission</h2>
            <p style="color: #555; line-height: 1.8; font-size: 1.1rem; margin-bottom: 15px;">At BMW Showroom, our mission is to redefine the automotive retail experience by providing personalized, premium services tailored to each client's unique lifestyle.</p>
            <p style="color: #555; line-height: 1.8; font-size: 1.1rem;">We strive to be more than just a dealership; we aim to be your lifelong automotive partner, ensuring every journey is as exhilarating as the destination.</p>
        </div>
        <div>
            <img src="https://images.unsplash.com/photo-1617814076367-b759c7d7e738?auto=format&fit=crop&w=800&q=80" alt="BMW Heritage" style="width: 100%; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
        </div>
    </div>

    <!-- Stats Section -->
    <div style="background: #f8f9fa; border-radius: 12px; padding: 50px; margin-bottom: 80px; text-align: center;">
        <div class="grid grid-3" style="gap: 30px;">
            <div>
                <i class="fas fa-car" style="font-size: 2.5rem; color: var(--bmw-blue); margin-bottom: 15px;"></i>
                <h3 style="font-size: 2.5rem; font-weight: 900; color: #111; margin: 0;">500+</h3>
                <p style="color: #666; font-size: 1.1rem; margin-top: 5px;">Vehicles Delivered</p>
            </div>
            <div>
                <i class="fas fa-users" style="font-size: 2.5rem; color: var(--bmw-blue); margin-bottom: 15px;"></i>
                <h3 style="font-size: 2.5rem; font-weight: 900; color: #111; margin: 0;">10,000+</h3>
                <p style="color: #666; font-size: 1.1rem; margin-top: 5px;">Happy Customers</p>
            </div>
            <div>
                <i class="fas fa-trophy" style="font-size: 2.5rem; color: var(--bmw-blue); margin-bottom: 15px;"></i>
                <h3 style="font-size: 2.5rem; font-weight: 900; color: #111; margin: 0;">15</h3>
                <p style="color: #666; font-size: 1.1rem; margin-top: 5px;">Awards Won</p>
            </div>
        </div>
    </div>

    <!-- Legacy Section -->
    <div class="grid grid-2" style="gap: 50px; align-items: center; margin-bottom: 80px;">
        <div style="order: 2;">
            <h2 style="font-size: 2.2rem; color: #111; margin-bottom: 20px;">A Legacy of Excellence</h2>
            <p style="color: #555; line-height: 1.8; font-size: 1.1rem; margin-bottom: 15px;">From the iconic kidney grille to the unmistakable Hofmeister kink, every BMW represents decades of engineering perfection.</p>
            <p style="color: #555; line-height: 1.8; font-size: 1.1rem;">Our team of certified experts is passionate about preserving this legacy while embracing the electrified future of modern mobility.</p>
        </div>
        <div style="order: 1;">
            <img src="https://images.unsplash.com/photo-1549399542-7e3f8b79c341?auto=format&fit=crop&w=800&q=80" alt="BMW Engineering" style="width: 100%; border-radius: 12px; box-shadow: 0 20px 40px rgba(0,0,0,0.1);">
        </div>
    </div>
</div>

<?php include 'partials/footer.php'; ?>
