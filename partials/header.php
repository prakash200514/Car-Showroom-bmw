<?php
// partials/header.php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include_once __DIR__ . '/../includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BMW Showroom – Sheer Driving Pleasure</title>
    <meta name="description" content="Explore the latest BMW models in India. Book a test drive, configure your BMW, and experience sheer driving pleasure.">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- AOS -->
    <link href="https://unpkg.com/aos@2.3.1/dist/aos.css" rel="stylesheet">

    <!-- Theme CSS -->
    <link rel="stylesheet" href="/showroom/assets/css/theme.css?v=<?= time(); ?>">
</head>
<?php $pageClass = (basename($_SERVER['PHP_SELF']) === 'index.php') ? 'page-home' : ''; ?>
<body class="fade-in <?= $pageClass ?>"><?php // <-- page-home = transparent hero navbar (homepage only) ?>

<nav class="navbar" id="main-navbar">
    <div class="container d-flex justify-content-between align-items-center" style="width:100%;">

        <!-- Logo -->
        <a href="/showroom/index.php" class="logo">
            <img src="https://upload.wikimedia.org/wikipedia/commons/4/44/BMW.svg" alt="BMW" style="width:38px;height:38px;">
            <h2>BMW Showroom</h2>
        </a>

        <!-- Desktop Nav -->
        <div class="nav-links d-flex align-items-center" style="gap:24px;">
            <a href="/showroom/index.php"    class="nav-link <?= basename($_SERVER['PHP_SELF'])=='index.php'    ?'active':'' ?>">Home</a>
            <a href="/showroom/cars.php"     class="nav-link <?= basename($_SERVER['PHP_SELF'])=='cars.php'     ?'active':'' ?>">Cars</a>
            <a href="/showroom/new-launch.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='new-launch.php'?'active':'' ?>">New Launch</a>
            <a href="/showroom/about.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='about.php'?'active':'' ?>">About</a>
            <a href="/showroom/contact.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='contact.php'?'active':'' ?>">Contact</a>
            <a href="/showroom/services.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='services.php'?'active':'' ?>" style="color: var(--bmw-blue); font-weight: 700;">Services</a>
            <a href="/showroom/bmw-spares.php" class="nav-link <?= basename($_SERVER['PHP_SELF'])=='bmw-spares.php'?'active':'' ?>" style="font-weight:700; color:var(--bmw-blue);">BMW Car Spares</a>

            <a href="/showroom/cart.php" class="nav-link" style="position:relative; font-size:1.15rem; color:inherit;">
                <i class="fas fa-shopping-cart"></i>
                <?php 
                  $cartCount = isset($_SESSION['cart']) ? array_sum($_SESSION['cart']) : 0; 
                ?>
                <span class="badge nav-cart-badge" id="nav-cart-count" style="position:absolute; top:-8px; right:-12px; background:#e53935; color:white; font-size:0.65rem; width:18px; height:18px; border-radius:50%; display:<?= $cartCount > 0 ? 'flex' : 'none' ?>; justify-content:center; align-items:center; font-weight:bold; font-family:sans-serif;"><?= $cartCount ?></span>
            </a>

            <?php if(isLoggedIn()): ?>
                <div class="dropdown" style="position:relative;">
                    <button class="btn" onclick="toggleDropdown(event)"
                        style="border:1.5px solid rgba(255,255,255,0.6);color:#fff;padding:8px 18px;font-size:0.78rem;letter-spacing:0.1em;"
                        id="account-btn">
                        <i class="fas fa-user-circle"></i> Account
                    </button>
                    <div id="userDropdown" class="card" style="position:absolute;top:120%;right:0;width:200px;padding:10px;display:none;z-index:1001;box-shadow:var(--shadow);background:#fff;border-radius:0;">
                        <?php if(isAdmin()): ?>
                            <a href="/showroom/admin/dashboard.php" style="display:block;padding:10px 12px;font-size:0.85rem;color:var(--text-color);">Dashboard</a>
                        <?php else: ?>
                            <a href="/showroom/customer/dashboard.php" style="display:block;padding:10px 12px;font-size:0.85rem;color:var(--text-color);">My Profile</a>
                        <?php endif; ?>
                        <div style="border-top:1px solid var(--border-color);margin:5px 0;"></div>
                        <a href="/showroom/logout.php" style="display:block;padding:10px 12px;font-size:0.85rem;color:#c62828;">Logout</a>
                    </div>
                </div>
            <?php else: ?>
                <a href="/showroom/login.php" class="btn"
                   id="login-btn"
                   style="border:1.5px solid rgba(255,255,255,0.6);color:#fff;padding:8px 22px;font-size:0.78rem;letter-spacing:0.1em;">
                    Login
                </a>
            <?php endif; ?>
        </div>

        <!-- Mobile Toggle -->
        <div class="menu-toggle" id="mobile-menu-btn">
            <i class="fas fa-bars"></i>
        </div>
    </div>
</nav>

<!-- Mobile Nav Overlay -->
<div id="mobile-nav" style="position:fixed;top:0;left:0;width:100%;height:100vh;background:#fff;z-index:9999;display:none;flex-direction:column;align-items:center;justify-content:center;opacity:0;transition:opacity 0.3s;overflow-y:auto;padding:80px 20px;">
    <div style="position:absolute;top:20px;right:24px;font-size:2rem;cursor:pointer;color:#111;" id="close-mobile-menu">
        <i class="fas fa-times"></i>
    </div>
    <a href="/showroom/index.php"     class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em;">Home</a>
    <a href="/showroom/cars.php"      class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em;">Cars</a>
    <a href="/showroom/new-launch.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em;">New Launch</a>
    <a href="/showroom/about.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em;">About</a>
    <a href="/showroom/contact.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em;">Contact</a>
    <a href="/showroom/services.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:var(--bmw-blue);font-weight:800;letter-spacing:-0.01em;">Services</a>
    <a href="/showroom/bmw-spares.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:var(--bmw-blue);font-weight:800;letter-spacing:-0.01em;">BMW Car Spares</a>
    <a href="/showroom/cart.php" class="nav-link" style="font-size:1.6rem;margin:12px 0;color:#111;font-weight:700;letter-spacing:-0.01em; position:relative;">
        <i class="fas fa-shopping-cart"></i> My Cart
    </a>
    <?php if(isLoggedIn()): ?>
        <a href="/showroom/logout.php" class="btn btn-outline" style="margin-top:28px;border-color:#111;color:#111;">Logout</a>
    <?php else: ?>
        <a href="/showroom/login.php" class="btn btn-primary" style="margin-top:28px;">Login</a>
    <?php endif; ?>
</div>

<script>
(function() {
    const navbar  = document.getElementById('main-navbar');
    const loginBtn  = document.getElementById('login-btn');
    const accountBtn = document.getElementById('account-btn');

    function updateNavbar() {
        const scrolled = window.scrollY > 30;
        navbar.classList.toggle('scrolled', scrolled);

        // Swap login/account button border+text color when navbar becomes white
        const btn = loginBtn || accountBtn;
        if (btn) {
            if (scrolled) {
                btn.style.borderColor = 'rgba(0,0,0,0.4)';
                btn.style.color = '#111';
            } else {
                btn.style.borderColor = 'rgba(255,255,255,0.6)';
                btn.style.color = '#fff';
            }
        }
    }

    window.addEventListener('scroll', updateNavbar, { passive: true });
    updateNavbar();

    // Mobile menu
    const mobileBtn  = document.getElementById('mobile-menu-btn');
    const mobileNav  = document.getElementById('mobile-nav');
    const closeBtn   = document.getElementById('close-mobile-menu');

    if (mobileBtn) {
        mobileBtn.addEventListener('click', () => {
            mobileNav.style.display = 'flex';
            setTimeout(() => { mobileNav.style.opacity = '1'; }, 10);
            document.body.style.overflow = 'hidden';
        });
    }
    if (closeBtn) {
        closeBtn.addEventListener('click', () => {
            mobileNav.style.opacity = '0';
            setTimeout(() => { mobileNav.style.display = 'none'; }, 300);
            document.body.style.overflow = '';
        });
    }

    // Dropdown
    window.toggleDropdown = function(e) {
        e.preventDefault();
        const dd = document.getElementById('userDropdown');
        if (dd) dd.style.display = dd.style.display === 'block' ? 'none' : 'block';
    };
    document.addEventListener('click', function(e) {
        const dd = document.getElementById('userDropdown');
        if (dd && !e.target.closest('.dropdown') && dd.style.display === 'block') {
            dd.style.display = 'none';
        }
    });
})();
</script>
